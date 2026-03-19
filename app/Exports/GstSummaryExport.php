<?php

namespace App\Exports;

use App\Models\Tblbill;
use App\Models\Tbltdstax;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class GstSummaryExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])
                ->whereHas('BillLines', function ($q) {
                    $q->whereRaw('CAST(gst_amount AS DECIMAL(10,2)) > 0');
                })->where('delete_status',0)
                ->orderBy('id', 'desc');

        $request = $this->request;

        // --- Filters same as your getgstsummary() ---
        if ($request->filled('section_id')) {
            $tdsids = Tbltdstax::where('section_id', $request->section_id)->pluck('id')->toArray();
            if (!empty($tdsids)) {
                $query->whereIn('tds_tax_id', $tdsids);
            }
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->filled('zone_id')) {
            $ids = explode(',', $request->zone_id);
            $query->whereIn('zone_id', $ids);
        }

        if ($request->filled('branch_id')) {
            $ids = explode(',', $request->branch_id);
            $query->whereIn('branch_id', $ids);
        }

        if ($request->filled('company_id')) {
            $ids = explode(',', $request->company_id);
            $query->whereIn('company_id', $ids);
        }

        if ($request->filled('vendor_id')) {
            $ids = explode(',', $request->vendor_id);
            $query->whereIn('vendor_id', $ids);
        }

        if ($request->filled('bill_ids')) {
            if(!empty($request->bill_ids)){
                $query->whereIn('id', explode(',', $request->bill_ids));
            }
        }

        if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function ($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                    ->orWhere('zone_name', 'like', "%{$search}%")
                    ->orWhere('branch_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('bill_gen_number', 'like', "%{$search}%")
                    ->orWhere('bill_number', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('bill_date', 'like', "%{$search}%")
                    ->orWhere('sub_total_amount', 'like', "%{$search}%")
                    ->orWhere('tax_type', 'like', "%{$search}%")
                    ->orWhere('grand_total_amount', 'like', "%{$search}%")
                    ->orWhere('due_date', 'like', "%{$search}%");
            });
        }

        // --- Prepare Export Data ---
        $bills = $query->get();

        return $bills->map(function ($bill) {
            $gstAmount = $bill->BillLines->sum('gst_amount');
            return [
                'Date' => $bill->bill_date,
                'Bill Generate No' => $bill->bill_gen_number,
                'Bill #' => $bill->bill_number,
                'Vendor Name' => $bill->vendor_name,
                'Due Date' => $bill->due_date,
                'Invoice Amount' => $bill->grand_total_amount,
                'GST Amount' => $gstAmount > 0 ? $gstAmount : 0,
                'Status' => ucfirst($bill->bill_status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Bill Generate No',
            'Bill #',
            'Vendor Name',
            'Due Date',
            'Invoice Amount',
            'GST Amount',
            'Status',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                /** Header style - consistent with other exports */
                $sheet->getStyle('A1:H1')->applyFromArray([
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
                if ($highestRow > 1) {
                    $sheet->getStyle("A1:H{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'thin'
                            ]
                        ]
                    ]);
                }

                /** Auto size columns */
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                /** Optional: Format numeric columns */
                if ($highestRow > 1) {
                    // Columns F and G (Invoice Amount and GST Amount)
                    $sheet->getStyle("F2:G{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                    /** Optional: Apply background colors to important columns */
                    $sheet->getStyle("F2:F{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'FFF58A'] // Yellow for Invoice Amount
                        ]
                    ]);

                    $sheet->getStyle("G2:G{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'D9EAD3'] // Light green for GST Amount (different shade)
                        ]
                    ]);

                    /** Optional: Highlight the Status column */
                    $sheet->getStyle("H2:H{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'A3CCDA'] // Blue for Status
                        ]
                    ]);
                }

                /** Optional: Text alignment */
                $sheet->getStyle("A2:H{$highestRow}")->getAlignment()->setHorizontal('left');
                $sheet->getStyle("F2:G{$highestRow}")->getAlignment()->setHorizontal('right');
                $sheet->getStyle("H2:H{$highestRow}")->getAlignment()->setHorizontal('center');

                /** Optional: Add total row */
                if ($highestRow > 1) {
                    $totalRow = $highestRow + 1;
                    $sheet->setCellValue("E{$totalRow}", "Total:");
                    $sheet->setCellValue("F{$totalRow}", "=SUM(F2:F{$highestRow})");
                    $sheet->setCellValue("G{$totalRow}", "=SUM(G2:G{$highestRow})");

                    // Style the total row
                    $sheet->getStyle("E{$totalRow}:H{$totalRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'E6E6E6'] // Light gray for total row
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'thin'
                            ]
                        ]
                    ]);

                    $sheet->getStyle("F{$totalRow}:G{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("F{$totalRow}:G{$totalRow}")->getAlignment()->setHorizontal('right');
                }

                /** Optional: Freeze header row */
                $sheet->freezePane('A2');
            }
        ];
    }
}