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
 * Export bills that have NEFT payment(s). Same filters as BillsExport.
 * Adds NEFT columns: UTR Number, Payment Method, Payment Status, Invoice Amount (NEFT), etc.
 */
class BillsExportNeft implements FromCollection, WithHeadings, WithEvents
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
        $query = Tblbill::with([
            'BillLines',
            'Tblvendor',
            'TblBilling',
            'Tblbankdetails',
            'Purchase',
            'Purchase.quotation',
            'billPayments',
            'billPayments.Neftget',
            'TblTDSsection',
            'TblTDSsection.section',
        ])
            ->where('delete_status', 0)
            ->whereHas('billPayments', function ($q) {
                $q->whereHas('Neftget');
            })
            ->orderBy('id', 'desc');

        $r = $this->request;

        if ($r->filled('date_from') && $r->filled('date_to')) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', trim($r->date_from))->startOfDay();
                $to   = Carbon::createFromFormat('d/m/Y', trim($r->date_to))->endOfDay();
                $query->whereRaw("STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ?", [$from, $to]);
            } catch (\Exception $e) { }
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
            $query->whereIn('zone_id', array_filter(explode(',', $r->zone_id)));
        }
        if ($r->filled('branch_id')) {
            $query->whereIn('branch_id', array_filter(explode(',', $r->branch_id)));
        }
        if ($r->filled('company_id')) {
            $query->whereIn('company_id', array_filter(explode(',', $r->company_id)));
        }
        if ($r->filled('vendor_id')) {
            $query->whereIn('vendor_id', array_filter(explode(',', $r->vendor_id)));
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

        $bills = $query->get();

        return $bills->flatMap(function ($bill) {
            $nefts = $bill->billPayments->pluck('Neftget')->flatten();
            $firstNeft = $nefts->first();

            $utrNumber     = $firstNeft ? ($firstNeft->utr_number ?? '') : '';
            $paymentMethod = $firstNeft ? ($firstNeft->payment_method ?? '') : '';
            $paymentStatus = $firstNeft ? ($firstNeft->payment_status ?? '') : '';
            $invoiceAmount = $firstNeft ? ($firstNeft->invoice_amount ?? '') : '';
            $accountNumber = $firstNeft ? ($firstNeft->account_number ?? '') : '';
            $ifscCode      = $firstNeft ? ($firstNeft->ifsc_code ?? '') : '';

            $lines = $bill->BillLines;
            if ($lines->isEmpty()) {
                return collect([[
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
                    ucfirst($bill->bill_status ?? ''),
                    $utrNumber,
                    $paymentMethod,
                    $paymentStatus,
                    $invoiceAmount,
                    $accountNumber,
                    $ifscCode,
                    '', '', '', '', '',
                ]]);
            }
            return $lines->map(function ($line) use ($bill, $utrNumber, $paymentMethod, $paymentStatus, $invoiceAmount, $accountNumber, $ifscCode) {
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
                    ucfirst($bill->bill_status ?? ''),
                    $utrNumber,
                    $paymentMethod,
                    $paymentStatus,
                    $invoiceAmount,
                    $accountNumber,
                    $ifscCode,
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
            'Status',
            'NEFT UTR Number', 'NEFT Payment Method', 'NEFT Payment Status', 'NEFT Invoice Amount', 'Account Number', 'IFSC Code',
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
                $lastCol = 'Y'; // 25 columns
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
}
