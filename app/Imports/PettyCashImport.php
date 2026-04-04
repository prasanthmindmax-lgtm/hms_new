<?php

namespace App\Imports;

use App\Models\Tblvendor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class PettyCashImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $rows = $rows->filter(function ($row) {
            return collect($row)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
        })->values();

        if ($rows->isEmpty()) {
            return;
        }

        $grouped = $rows->groupBy(function ($row, $index) {
            $g = trim((string) ($row['report_group'] ?? $row['report_id'] ?? ''));
            return $g !== '' ? $g : '__single_' . $index;
        });

        $userId = auth()->id();
        $now = now();

        foreach ($grouped as $groupKey => $lines) {
            $first = $lines->first();
            $reportName = trim((string) ($first['report_name'] ?? ''));
            if ($reportName === '') {
                Log::warning('PettyCashImport: skipped group — missing report_name', ['group' => $groupKey]);
                continue;
            }

            $vendorIdStr = trim((string) ($first['vendor_id'] ?? ''));
            $vendor = null;
            if ($vendorIdStr !== '') {
                $vendor = Tblvendor::where('vendor_id', $vendorIdStr)->first();
                if (!$vendor) {
                    Log::error('PettyCashImport: vendor not found', ['vendor_id' => $vendorIdStr, 'group' => $groupKey]);
                    continue;
                }
            }

            $zoneId = null;
            if (!empty($first['zone'] ?? '')) {
                $z = DB::table('tblzones')->where('name', trim($first['zone']))->first();
                $zoneId = $z ? (int) $z->id : null;
            }

            $branchId = null;
            if (!empty($first['branch'] ?? '')) {
                $b = DB::table('tbl_locations')->where('name', trim($first['branch']))->first();
                $branchId = $b ? (int) $b->id : null;
            }

            $companyId = null;
            if (!empty($first['company'] ?? '')) {
                $c = DB::table('company_tbl')->where('company_name', trim($first['company']))->first();
                $companyId = $c ? (int) $c->id : null;
            }

            $headerCategoryId = null;
            if (!empty($first['header_expense_category'] ?? '')) {
                $hc = DB::table('expense_categories')
                    ->where('name', trim($first['header_expense_category']))
                    ->where('is_active', 1)
                    ->first();
                $headerCategoryId = $hc ? (int) $hc->id : null;
            }

            $expenseDate = $this->formatExpenseDate($first['expense_date'] ?? null);
            $currency = trim((string) ($first['currency'] ?? 'INR')) ?: 'INR';
            $referenceNo = trim((string) ($first['reference_no'] ?? '')) ?: null;
            $notes = trim((string) ($first['notes'] ?? '')) ?: null;
            $status = strtolower(trim((string) ($first['status'] ?? 'draft')));
            if (!in_array($status, ['pending', 'approved', 'rejected', 'draft'], true)) {
                $status = 'draft';
            }

            $claim = strtolower(trim((string) ($first['claim_reimbursement'] ?? '0')));
            $claimReimbursement = in_array($claim, ['1', 'yes', 'true', 'y'], true) ? 1 : 0;

            $lineRows = [];
            $totalAmount = 0.0;

            foreach ($lines as $row) {
                $amount = (float) ($row['line_amount'] ?? $row['amount'] ?? 0);
                if ($amount <= 0) {
                    continue;
                }
                $catName = trim((string) ($row['expense_category'] ?? ''));
                if ($catName === '') {
                    Log::warning('PettyCashImport: skipped line — missing expense_category', ['group' => $groupKey]);
                    continue;
                }
                $cat = DB::table('expense_categories')
                    ->where('name', $catName)
                    ->where('is_active', 1)
                    ->first();
                if (!$cat) {
                    Log::error('PettyCashImport: expense category not found', ['name' => $catName, 'group' => $groupKey]);
                    continue;
                }
                $lineRows[] = [
                    'expense_category_id' => (int) $cat->id,
                    'description' => trim((string) ($row['item_description'] ?? '')) ?: null,
                    'amount' => $amount,
                ];
                $totalAmount += $amount;
            }

            if (empty($lineRows)) {
                Log::warning('PettyCashImport: skipped group — no valid line items', ['group' => $groupKey]);
                continue;
            }

            if ($headerCategoryId === null) {
                $headerCategoryId = $lineRows[0]['expense_category_id'];
            }

            $reportId = trim((string) ($first['report_id'] ?? ''));
            if ($reportId === '' || DB::table('petty_cash')->where('report_id', $reportId)->exists()) {
                $reportId = $this->nextUniqueReportId();
            }

            $report = DB::table('expense_reports')
    ->where('report_name', $reportName)
    ->first();

if (!$report) {
    $reportId = DB::table('expense_reports')->insertGetId([
        'report_name' => $reportName,
        'created_by' => $userId,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
} else {
    $reportId = $report->id;
}

            $pettyCashId = DB::table('petty_cash')->insertGetId([
                'report_id' => $reportId,
                'expense_date' => $expenseDate,
                'vendor_id' => $vendor ? $vendor->id : null,
                'expense_category_id' => $headerCategoryId,
                'company_id' => $companyId,
                'zone_id' => $zoneId,
                'branch_id' => $branchId,
                'currency' => $currency,
                'total_amount' => round($totalAmount, 2),
                'claim_reimbursement' => $claimReimbursement,
                'notes' => $notes,
                'reference_no' => $referenceNo,
                'status' => $status,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($lineRows as $line) {
                DB::table('petty_cash_items')->insert([
                    'petty_cash_id' => $pettyCashId,
                    'expense_category_id' => $line['expense_category_id'],
                    'description' => $line['description'],
                    'amount' => round($line['amount'], 2),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function nextUniqueReportId(): string
    {
        $maxAttempts = 5000;
        $n = DB::table('petty_cash')->count() + 1;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $candidate = 'RID-' . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
            if (!DB::table('petty_cash')->where('report_id', $candidate)->exists()) {
                return $candidate;
            }
            $n++;
        }

        return 'RID-' . uniqid();
    }

    private function formatExpenseDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        $str = trim((string) $value);

        try {
            return Carbon::createFromFormat('d/m/Y', $str)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($str)->format('Y-m-d');
            } catch (\Exception $e2) {
                Log::error('PettyCashImport: invalid expense_date', ['value' => $str]);
                return null;
            }
        }
    }
}
