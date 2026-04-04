<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PettyCashExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;
    protected $format; // 'csv' | 'xlsx'

    public function __construct($request, $format = 'xlsx')
    {
        $this->request = $request;
        $this->format  = $format;
    }

    /* =======================
     * COLLECTION
     * ======================= */
    public function collection()
    {
        $r = $this->request;

        $query = DB::table('petty_cash')
            ->leftJoin('vendor_tbl',        'vendor_tbl.id',        '=', 'petty_cash.vendor_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'petty_cash.expense_category_id')
            ->leftJoin('tblzones',           'tblzones.id',           '=', 'petty_cash.zone_id')
            ->leftJoin('company_tbl',        'company_tbl.id',        '=', 'petty_cash.company_id')
            ->leftJoin('tbl_locations',      'tbl_locations.id',      '=', 'petty_cash.branch_id')
            ->leftJoin('expense_reports',    'expense_reports.id',    '=', 'petty_cash.report_id')
            ->select(
                'petty_cash.id',
                'petty_cash.expense_date',
                'petty_cash.reference_no',
                'petty_cash.total_amount',

                'petty_cash.status',
                'petty_cash.notes',
                'petty_cash.claim_reimbursement',
                'vendor_tbl.display_name    as merchant_name',
                'expense_categories.name    as category_name',
                'tblzones.name              as zone_name',
                'company_tbl.company_name   as company_name',
                'tbl_locations.name         as branch_name',
                'expense_reports.report_id  as expense_report_code',
                'expense_reports.report_name as expense_report_name'
            )
            ->orderBy('petty_cash.id', 'desc');

        // ── Filters (same logic as getPettyCashAjax) ──────────────────────

        if ($r->filled('date_from') && $r->filled('date_to')) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', trim($r->date_from))->startOfDay();
                $to   = Carbon::createFromFormat('d/m/Y', trim($r->date_to))->endOfDay();
                $query->whereBetween('petty_cash.expense_date', [$from, $to]);
            } catch (\Exception $e) {
                // invalid date – skip
            }
        }

        if ($r->filled('zone_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $r->zone_id))));
            if ($ids) {
                $query->whereIn('petty_cash.zone_id', $ids);
            }
        }

        if ($r->filled('branch_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $r->branch_id))));
            if ($ids) {
                $query->whereIn('petty_cash.branch_id', $ids);
            }
        }

        if ($r->filled('company_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $r->company_id))));
            if ($ids) {
                $query->whereIn('petty_cash.company_id', $ids);
            }
        }

        if ($r->filled('vendor_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $r->vendor_id))));
            if ($ids) {
                $query->whereIn('petty_cash.vendor_id', $ids);
            }
        }

        if ($r->filled('status_name')) {
            $statuses = [];
            foreach (explode(',', $r->status_name) as $s) {
                $s = strtolower(trim($s));
                if ($s === '') {
                    continue;
                }
                if (in_array($s, ['approve', 'approved'], true)) {
                    $statuses[] = 'approved';
                } elseif (in_array($s, ['reject', 'rejected'], true)) {
                    $statuses[] = 'rejected';
                } else {
                    $statuses[] = $s;
                }
            }
            if ($statuses) {
                $query->whereIn('petty_cash.status', array_unique($statuses));
            }
        }

        if ($r->filled('expense_report_id')) {
            $query->where('petty_cash.report_id', (int) $r->expense_report_id);
        }

        if ($r->filled('universal_search')) {
            $search = $r->universal_search;
            $query->where(function ($q) use ($search) {
                $q->where('expense_reports.report_id',   'LIKE', "%{$search}%")
                  ->orWhere('expense_reports.report_name', 'LIKE', "%{$search}%")
                  ->orWhere('vendor_tbl.display_name',    'LIKE', "%{$search}%")
                  ->orWhere('tblzones.name',              'LIKE', "%{$search}%")
                  ->orWhere('company_tbl.company_name',   'LIKE', "%{$search}%")
                  ->orWhere('tbl_locations.name',         'LIKE', "%{$search}%");
            });
        }

        $rows = $query->get();

        // ── Flatten: for itemized expenses, expand to one row per item ───
        return $rows->flatMap(function ($pc) {
            $items = DB::table('petty_cash_items')
                ->leftJoin('expense_categories', 'expense_categories.id', '=', 'petty_cash_items.expense_category_id')
                ->where('petty_cash_items.petty_cash_id', $pc->id)
                ->select(
                    'petty_cash_items.description',
                    'petty_cash_items.amount',
                    'expense_categories.name as item_category'
                )
                ->get();

            $baseRow = [
                $pc->expense_date   ? Carbon::parse($pc->expense_date)->format('d/m/Y') : '',
                $pc->merchant_name  ?? '',
                $pc->zone_name      ?? '',
                $pc->branch_name    ?? '',
                $pc->company_name   ?? '',
                $pc->category_name  ?? '',
                $pc->reference_no   ?? '',
                $pc->notes          ?? '',
                number_format((float) $pc->total_amount, 2, '.', ''),
                ucfirst($pc->expense_type  ?? 'single'),
                ucfirst($pc->status        ?? ''),
                ($pc->claim_reimbursement  ? 'Yes' : 'No'),
                $pc->expense_report_code   ?? '',
                $pc->expense_report_name   ?? '',
            ];

            if ($items->isEmpty()) {
                // Single expense or itemized with no sub-items stored
                return collect([array_merge($baseRow, ['', '', ''])]);
            }

            // One output row per item line
            return $items->map(function ($item) use ($baseRow) {
                return array_merge($baseRow, [
                    $item->item_category ?? '',
                    $item->description   ?? '',
                    number_format((float) $item->amount, 2, '.', ''),
                ]);
            });
        });
    }

    /* =======================
     * HEADINGS
     * ======================= */
    public function headings(): array
    {
        return [
            'Expense Date', 'Merchant', 'Zone', 'Branch', 'Company',
            'Category', 'Reference #', 'Notes', 'Amount',
            'Status', 'Reimbursable', 'Report ID', 'Report Name',
            // Item-level columns (only populated for itemized expenses)
            'Item Category', 'Item Description', 'Item Amount',
        ];
    }

    /* =======================
     * EVENTS (XLSX styling only)
     * ======================= */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // Skip all styling for CSV
                if ($this->format === 'csv') {
                    return;
                }

                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'Q'; // 17 columns

                // Header row: bold + fill
                $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color'    => ['rgb' => 'D6E4F0'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // All data rows: thin border
                $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Auto-fit column widths
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
