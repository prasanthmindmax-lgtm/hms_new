<?php

namespace App\Exports;

use App\Models\Tblbill;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BillsExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Tblbill::with([
            'BillLines',
            'Tblvendor',
            'TblBilling',
            'Tblbankdetails',
            'Purchase',
            'Purchase.quotation',
            'billPayments',
            'TblTDSsection',
            'TblTDSsection.section'
        ])->where('delete_status',0)->orderBy('id', 'desc');

        $request = $this->request;
        // ---- SAME FILTERS ----
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->filled('vendor_id')) {
            $query->whereIn('vendor_id', explode(',', $request->vendor_id));
        }

        if ($request->filled('company_id')) {
            $query->whereIn('company_id', explode(',', $request->company_id));
        }

        if ($request->filled('zone_id')) {
            $query->whereIn('zone_id', explode(',', $request->zone_id));
        }

        if ($request->filled('branch_id')) {
            $query->whereIn('branch_id', explode(',', $request->branch_id));
        }

        if ($request->filled('nature_id')) {
            $natureIds = explode(',', $request->nature_id);
            $query->whereHas('BillLines', function ($q) use ($natureIds) {
                $q->whereIn('account_id', $natureIds);
            });
        }

        if ($request->filled('ids')) {
            $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        $bills = $query->get();

        // IMPORTANT FIX: EXPAND BILL LINES INTO MULTIPLE ROWS
        return $bills->flatMap(function ($bill) {
            return $bill->BillLines->map(function ($line) use ($bill) {

                // GST Logic
                $gstType = $line->gst_type; // cgst_sgst OR igst
                $gstRate = (float) ($line->gst_rate ?? 0);

                if ($gstType === 'GST') {
                    $cgstRate = $gstRate / 2;
                    $sgstRate = $gstRate / 2;
                    $igstRate = 0;

                    $cgstAmount = $line->cgst_amount;
                    $sgstAmount = $line->sgst_amount;
                    $igstAmount = 0;

                } else { // IGST
                    $cgstRate = 0;
                    $sgstRate = 0;
                    $igstRate = $gstRate;

                    $cgstAmount = 0;
                    $sgstAmount = 0;
                    $igstAmount = $line->gst_amount;
                }

                return [
                    'Bill Date'              => $bill->bill_date,
                    'Due Date'               => $bill->due_date,
                    'Zone'                   => $bill->zone_name,
                    'Branch'                 => $bill->branch_name,
                    'Bill Gen No'            => $bill->bill_gen_number,
                    'Bill No'                => $bill->bill_number,
                    'Order No'               => $bill->order_number,
                    'Vendor ID'              => optional($bill->Tblvendor)->vendor_id,
                    'Vendor Name'            => $bill->vendor_name,
                    'Sub Total'              => $bill->sub_total_amount,
                    'Total'                  => $bill->grand_total_amount,
                    'Balance'                => $bill->balance_amount,
                    'Adjustment'             => $bill->adjustment_amount,
                    'Adjustment Description' => $bill->adjustment_reason,
                    'Status'                 => ucfirst($bill->bill_status),

                    // Line Item
                    'Item Name'              => $line->item_details,
                    'Account Name'           => $line->account,
                    'Quantity'               => $line->quantity,
                    'Rate'                   => $line->rate,
                    'Item Total'             => $line->amount,

                    // TDS
                    'TDS Name'               => $bill->tax_name,
                    'TDS Percentage'         => $bill->tax_rate,
                    'TDS Section'            => optional($bill->TblTDSsection)->section_name,
                    'TDS Amount'             => $bill->tax_amount,

                    // Discount
                    'Discount Percentage'    => $bill->discount_percent,
                    'Discount Amount'        => $bill->discount_amount,

                    // GST
                    'CGST rate %'            => $cgstRate,
                    'SGST rate %'            => $sgstRate,
                    'IGST rate %'            => $igstRate,

                    'CGST'                   => $cgstAmount,
                    'SGST'                   => $sgstAmount,
                    'IGST'                   => $igstAmount,
                ];
            });
        });

    }

    public function headings(): array
    {
        return [
            'Bill Date',
            'Due Date',
            'Zone',
            'Branch',
            'Bill Gen No',
            'Bill No',
            'Order No',
            'Vendor ID',
            'Vendor Name',
            'Sub Total',
            'Total',
            'Balance',
            'Adjustment',
            'Adjustment Description',
            'Status',
            'Item Name',
            'Account Name',
            'Quantity',
            'Rate',
            'Item Total',
            'TDS Name',
            'TDS Percentage',
            'TDS Section',
            'TDS Amount',
            'Discount Percentage',
            'Discount Amount',
            'CGST rate %',
            'SGST rate %',
            'IGST rate %',
            'CGST',
            'SGST',
            'IGST',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                /** Header style - same color as TdsDetailedExport */
                $sheet->getStyle('A1:AF1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center'
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'F8CBAD'] // Light orange color
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin'
                        ]
                    ]
                ]);

                /** Apply borders to all data cells */
                $highestRow = $sheet->getHighestRow();
                if ($highestRow > 1) {
                    $sheet->getStyle("A1:AF{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'thin'
                            ]
                        ]
                    ]);
                }

                /** Auto size columns */
                foreach (range('A', 'AF') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}