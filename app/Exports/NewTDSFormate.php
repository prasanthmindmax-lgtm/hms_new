<?php

namespace App\Exports;

use App\Models\Tblbill;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * TDS Export — matches TDS_Format.xlsx template exactly.
 *
 * Columns (A–O):
 *   A  Bill Date
 *   B  Bill Number
 *   C  VENDOR
 *   D  PAN
 *   E  TYPE OF DEDUCTEE   ← PAN 4th char: 'C' = Company, else Non Company
 *   F  NATURE              ← TDS tax_name / nature of payment
 *   G  SECTION             ← TDS section name
 *   H  TAXABLE             ← sub_total_amount (taxable value)
 *   I  CGST                ← sum of bill_lines_tbl.cgst_amount for this bill
 *   J  SGST                ← sum of bill_lines_tbl.sgst_amount for this bill
 *   K  IGST                ← sum of bill_lines_tbl.gst_amount for this bill
 *   L  TDS AMOUNT
 *   M  TDS %               ← tax_rate
 *   N  PAYMENT DATE        ← bank_statements.transaction_date via bank_bill_matches
 *   O  PAYMENT REF NO      ← bank_statements.reference_number via bank_bill_matches
 */
class NewTDSFormate implements FromArray, WithEvents
{
    protected $request;
    protected $format;

    public function __construct($request, string $format = 'xlsx')
    {
        $this->request = $request;
        $this->format  = $format;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // FromArray: return all rows including header
    // ──────────────────────────────────────────────────────────────────────────
    public function array(): array
    {
        $bills = $this->buildQuery()->get();
        $billIds = $bills->pluck('id')->toArray();

        // ── Pre-load GST sums from bill_lines_tbl per bill ────────────────────────
        // bill_lines_tbl columns: cgst_amount, sgst_amount, gst_amount (= IGST)
        // One bill has many lines; we sum all three per bill.
        $gstTotals = [];
        if (!empty($billIds)) {
            $gstRows = DB::table('bill_lines_tbl')
                ->whereIn('bill_id', $billIds)
                ->select(
                    'bill_id',
                    DB::raw('COALESCE(SUM(cgst_amount), 0) as total_cgst'),
                    DB::raw('COALESCE(SUM(sgst_amount), 0) as total_sgst'),
                    DB::raw('COALESCE(SUM(gst_amount),  0) as total_igst')
                )
                ->groupBy('bill_id')
                ->get();

            foreach ($gstRows as $g) {
                $gstTotals[$g->bill_id] = [
                    'cgst' => round((float) $g->total_cgst, 2),
                    'sgst' => round((float) $g->total_sgst, 2),
                    'igst' => round((float) $g->total_igst, 2),
                ];
            }
        }

        // ── Pre-load payment data from bank_bill_matches + bank_statements ────
        // Take the latest non-cancelled match per bill.
        $payments = [];
        if (!empty($billIds)) {
            $payRows = DB::table('bank_bill_matches as m')
                ->join('bank_statements as s', 's.id', '=', 'm.bank_statement_id')
                ->whereIn('m.bill_id', $billIds)
                ->where('m.status', '!=', 'cancelled')
                ->select(
                    'm.bill_id',
                    's.transaction_date',
                    's.reference_number',
                    'm.matched_at'
                )
                ->orderBy('m.matched_at', 'desc')
                ->get();

            foreach ($payRows as $row) {
                if (!isset($payments[$row->bill_id])) {
                    $payments[$row->bill_id] = [
                        'payment_date' => $row->transaction_date ?? '',
                        'payment_ref'  => $row->reference_number ?? '',
                    ];
                }
            }
        }

        // ── Build rows ────────────────────────────────────────────────────────
        $data = [];

        // Header — 15 columns A–O
        // Note: "TDS AMOPUNT" typo is kept to match the uploaded template exactly.
        $data[] = [
            'Bill Date',        // A
            'Bill Number',      // B
            'VENDOR',           // C
            'PAN',              // D
            'TYPE OF DEDUCTEE', // E
            'NATURE',           // F
            'SECTION',          // G
            'TAXABLE',          // H
            'CGST',             // I
            'SGST',             // J
            'IGST',             // K
            'TDS AMOUNT',      // L
            'TDS %',            // M
            'PAYMENT DATE',     // N
            'PAYMENT REF NO',   // O
        ];

        foreach ($bills as $bill) {
            // TDS section
            $tdsSection  = $bill->TblTDSsection;
            $sectionName = optional(optional($tdsSection)->section)->name ?? '';
            $taxName     = optional($tdsSection)->tax_name ?? '';
            $taxRate     = optional($tdsSection)->tax_rate ?? '';

            // PAN + deductee type
            $pan          = trim((string) (optional($bill->Tblvendor)->pan_number ?? ''));
            $deducteeType = $this->getDeducteeType($pan);

            // Taxable = sub_total_amount
            $subTotal = (float) ($bill->sub_total_amount ?? 0);

            // GST breakdown from bill_lines_tbl
            $gst  = $gstTotals[$bill->id] ?? ['cgst' => 0, 'sgst' => 0, 'igst' => 0];
            $cgst = $gst['cgst'] > 0 ? $gst['cgst'] : '';
            $sgst = $gst['sgst'] > 0 ? $gst['sgst'] : '';
            $igst = $gst['igst'] > 0 ? $gst['igst'] : '';

            // Payment
            $pay     = $payments[$bill->id] ?? ['payment_date' => '', 'payment_ref' => ''];
            $payDate = $this->formatPaymentDate($pay['payment_date']);
            $payRef  = $pay['payment_ref'];

            $data[] = [
                $bill->bill_date       ?? '',                           // A  Bill Date
                $bill->bill_gen_number ?? $bill->bill_number ?? '',     // B  Bill Number
                $bill->vendor_name     ?? '',                           // C  VENDOR
                $pan,                                                   // D  PAN
                $deducteeType,                                          // E  TYPE OF DEDUCTEE
                $taxName,                                               // F  NATURE
                $sectionName,                                           // G  SECTION
                $subTotal > 0 ? $subTotal : '',                        // H  TAXABLE
                $cgst,                                                  // I  CGST
                $sgst,                                                  // J  SGST
                $igst,                                                  // K  IGST
                $bill->tax_amount      ?? '',                           // L  TDS AMOUNT
                $taxRate !== '' ? $taxRate : '',                        // M  TDS %
                $payDate,                                               // N  PAYMENT DATE
                $payRef,                                                // O  PAYMENT REF NO
            ];
        }

        return $data;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // AfterSheet: apply header styling matching TDS_Format.xlsx template
    // Header: bold, green fill #B6D7A8, thin borders on all cells
    // ──────────────────────────────────────────────────────────────────────────
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                if ($this->format === 'csv') {
                    return;
                }

                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'O'; // 15 columns A–O

                // ── Header row styling ──────────────────────────────────────
                $sheet->getStyle('A1:O1')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'color' => ['rgb' => '000000'],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'B6D7A8'], // exact colour from template
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // ── Data rows — thin borders ────────────────────────────────
                if ($lastRow > 1) {
                    $sheet->getStyle('A2:O' . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => 'CCCCCC'],
                            ],
                        ],
                    ]);
                }

                // ── Column widths ───────────────────────────────────────────
                $colWidths = [
                    'A' => 14,  // Bill Date
                    'B' => 20,  // Bill Number
                    'C' => 28,  // VENDOR
                    'D' => 14,  // PAN
                    'E' => 16,  // TYPE OF DEDUCTEE
                    'F' => 22,  // NATURE
                    'G' => 16,  // SECTION
                    'H' => 14,  // TAXABLE
                    'I' => 12,  // CGST
                    'J' => 12,  // SGST
                    'K' => 12,  // IGST
                    'L' => 14,  // TDS AMOUNT
                    'M' => 8,   // TDS %
                    'N' => 16,  // PAYMENT DATE
                    'O' => 22,  // PAYMENT REF NO
                ];
                foreach ($colWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // ── Freeze header row ───────────────────────────────────────
                $sheet->freezePane('A2');

                // ── Auto-filter on header ───────────────────────────────────
                $sheet->setAutoFilter('A1:O1');
            },
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // TYPE OF DEDUCTEE logic:
    //   PAN format: AAAAA9999A  (5 letters, 4 digits, 1 letter)
    //   4th character (index 3) is the entity type:
    //     P = Individual (Non Company)
    //     C = Company
    //     H = HUF       (Non Company)
    //     F = Firm      (Non Company)
    //     A = AOP/BOI   (Non Company)
    //     B = Body of individuals (Non Company)
    //     G = Government (Non Company)
    //     J = Artificial juridical person (Non Company)
    //     L = Local authority (Non Company)
    //     T = Trust      (Non Company)
    // ──────────────────────────────────────────────────────────────────────────
    private function getDeducteeType(string $pan): string
    {
        $pan = strtoupper(trim($pan));
        if (strlen($pan) < 4) {
            return '';
        }
        $fourth = $pan[3]; // 4th character (0-based index 3)
        return $fourth === 'C' ? 'Company' : 'Non Company';
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Format payment date — bank_statements stores dates in various formats.
    // Normalise to dd/mm/yyyy for consistency with bill dates in the system.
    // ──────────────────────────────────────────────────────────────────────────
    private function formatPaymentDate($date): string
    {
        if (empty($date)) {
            return '';
        }
        try {
            // Handle formats: "01/Jan/2026", "2026-01-01", "01/01/2026", etc.
            $d = Carbon::parse($date);
            return $d->format('d/m/Y');
        } catch (\Exception $e) {
            return (string) $date;
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Query — only bills with TDS (tax_amount > 0), same filters as original
    // ──────────────────────────────────────────────────────────────────────────
    protected function buildQuery()
    {
        $query = Tblbill::with([
            'Tblvendor',
            'TblTDSsection',
            'TblTDSsection.section',
            'BillLines',   // needed for CGST / SGST / IGST sums
        ])
            ->where('delete_status', 0)
            ->whereRaw('CAST(COALESCE(tax_amount, 0) AS DECIMAL(10,2)) > 0')
            ->orderBy('id', 'desc');

        $r = $this->request;

        if ($r->filled('date_from') && $r->filled('date_to')) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', trim($r->date_from))->startOfDay();
                $to   = Carbon::createFromFormat('d/m/Y', trim($r->date_to))->endOfDay();
                $query->whereRaw("STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ?", [$from, $to]);
            } catch (\Exception $e) {}
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
                    $q->orWhere('status',      'LIKE', '%' . $status . '%')
                      ->orWhere('bill_status', 'LIKE', '%' . $status . '%');
                }
            });
        }
        if ($r->filled('universal_search')) {
            $search = $r->universal_search;
            $query->where(function ($q) use ($search) {
                $q->where('vendor_name',        'like', "%{$search}%")
                  ->orWhere('zone_name',         'like', "%{$search}%")
                  ->orWhere('branch_name',       'like', "%{$search}%")
                  ->orWhere('company_name',      'like', "%{$search}%")
                  ->orWhere('bill_gen_number',   'like', "%{$search}%")
                  ->orWhere('bill_number',       'like', "%{$search}%")
                  ->orWhere('order_number',      'like', "%{$search}%")
                  ->orWhere('bill_date',         'like', "%{$search}%")
                  ->orWhere('sub_total_amount',  'like', "%{$search}%")
                  ->orWhere('grand_total_amount','like', "%{$search}%")
                  ->orWhere('due_date',          'like', "%{$search}%");
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