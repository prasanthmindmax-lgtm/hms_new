<?php

namespace App\Exports;

use App\Models\Tblbill;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Export all bill data + GST line data (gst_type, gst_name, gst_rate %, cgst_amount, sgst_amount, gst_amount).
 * Same filters as BillsExport / getbill. Supports xlsx and csv.
 */
class BillsExportGst implements FromCollection, WithHeadings, WithEvents
{
    protected $request;
    protected $format;

    public function __construct($request, $format = 'xlsx')
    {
        $this->request = $request;
        $this->format  = $format;
    }

    public function collection()
    {
        $query = $this->buildQuery();
        $bills = $query->get();

        return $bills->flatMap(function ($bill) {
            return $bill->BillLines
                ->filter(function ($line) {
                    $gst = (float) ($line->gst_amount ?? 0);
                    $cgst = (float) ($line->cgst_amount ?? 0);
                    $sgst = (float) ($line->sgst_amount ?? 0);
                    return $gst > 0 || $cgst > 0 || $sgst > 0;
                })
                ->map(function ($line) use ($bill) {
                return [
                    $bill->bill_date,
                    $bill->due_date,
                    $bill->zone_name,
                    $bill->branch_name,
                    $bill->bill_gen_number,
                    $bill->bill_number,
                    $bill->order_number,
                    optional($bill->Tblvendor)->vendor_id,
                    $bill->vendor_name,
                    $bill->sub_total_amount,
                    $bill->grand_total_amount,
                    $bill->balance_amount,
                    $bill->adjustment_amount,
                    $bill->adjustment_reason ?? '',
                    ucfirst($bill->bill_status ?? ''),
                    // GST line columns
                    $line->gst_type ?? '',
                    $line->gst_name ?? '',
                    $line->gst_rate ?? '',
                    $line->cgst_amount ?? '',
                    $line->sgst_amount ?? '',
                    $line->gst_amount ?? '',
                    // Line
                    $line->item_details,
                    $line->account,
                    $line->quantity,
                    $line->rate,
                    $line->amount,
                ];
            });
        });
    }

    public function headings(): array
    {
        return [
            'Bill Date', 'Due Date', 'Zone', 'Branch', 'Bill Gen No', 'Bill No',
            'Order No', 'Vendor ID', 'Vendor Name', 'Sub Total', 'Total', 'Balance',
            'Adjustment', 'Adjustment Description', 'Status',
            'GST Type', 'GST Name', 'GST Rate %', 'CGST Amount', 'SGST Amount', 'GST Amount',
            'Item Name', 'Account Name', 'Quantity', 'Rate', 'Item Total',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                if ($this->format === 'csv') {
                    return;
                }
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'Y'; // 25 cols
                $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F8CBAD'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                if ($lastRow > 1) {
                    $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                        ],
                    ]);
                }
            },
        ];
    }

    protected function buildQuery()
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
            'TblTDSsection.section',
        ])
            ->where('delete_status', 0)
            ->whereHas('BillLines', function ($q) {
                $q->whereRaw('CAST(COALESCE(gst_amount, 0) AS DECIMAL(10,2)) > 0');
            })
            ->orderBy('id', 'desc');

        $r = $this->request;

        if ($r->filled('date_from') && $r->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $r->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $r->date_to)->endOfDay();
            $query->whereRaw("STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ?", [$from, $to]);
        }
        if ($r->filled('state_name')) {
            $state_name = $r->state_name;
            if ($state_name === 'Tamil Nadu') {
                $query->whereIn('zone_id', ['2','4','6','7','8','9']);
            } elseif ($state_name === 'Karnataka') {
                $query->whereIn('zone_id', ['3']);
            } elseif ($state_name === 'Kerala') {
                $query->whereIn('zone_id', ['5']);
            } elseif ($state_name === 'International') {
                $query->whereIn('zone_id', ['10']);
            } elseif ($state_name === 'Andra Pradesh') {
                $query->whereIn('branch_id', ['30']);
            }
        }
        if ($r->filled('zone_id')) {
            $query->whereIn('zone_id', explode(',', $r->zone_id));
        }
        if ($r->filled('branch_id')) {
            $query->whereIn('branch_id', explode(',', $r->branch_id));
        }
        if ($r->filled('company_id')) {
            $query->whereIn('company_id', explode(',', $r->company_id));
        }
        if ($r->filled('vendor_id')) {
            $query->whereIn('vendor_id', explode(',', $r->vendor_id));
        }
        if ($r->filled('status_name')) {
            $statuses = array_map('trim', explode(',', $r->status_name));
            $query->where(function ($q) use ($statuses) {
                foreach ($statuses as $status) {
                    $q->orWhere('status', 'LIKE', '%' . $status . '%')
                      ->orWhere('bill_status', 'LIKE', '%' . $status . '%');
                }
            });
        }
        if ($r->filled('universal_search')) {
            $search = $r->universal_search;
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
        if ($r->filled('bill_ids') && is_string($r->bill_ids)) {
            $ids = array_filter(explode(',', $r->bill_ids));
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        return $query;
    }
}
