<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Tblaccount;

class BankStatementController extends Controller
{
    /**
     * Display bank statements page
     */
    public function index()
    {
        $admin = Auth::user();
        $bankAccountsEnabled = Schema::hasTable('bank_reconciliation_accounts')
            && Schema::hasColumn('bank_statements', 'bank_account_id');

        $chartAccountsForSelect = [];
        if (Schema::hasTable('account_tbl')) {
            $orderBy = Schema::hasColumn('account_tbl', 'name') ? 'name' : 'id';
            $chartAccountsForSelect = Tblaccount::query()
                ->orderBy($orderBy)
                ->orderBy('id')
                ->get()
                ->map(function ($a) {
                    $code = (string) ($a->code ?? '');
                    $name = (string) ($a->name ?? '');
                    $text = trim(($code !== '' ? $code.' — ' : '').$name);

                    return [
                        'id' => (int) $a->id,
                        'text' => $text !== '' ? $text : ('Account #'.$a->id),
                    ];
                })
                ->values()
                ->all();
        }

        return view('bank-reconciliation.index', compact('admin', 'bankAccountsEnabled', 'chartAccountsForSelect'));
    }

    /**
     * Batch upload history page (AJAX table, no full reload).
     */
    public function batchUploadPage()
    {
        $admin = Auth::user();
        $bankAccountsEnabled = Schema::hasTable('bank_reconciliation_accounts')
            && Schema::hasColumn('bank_statements', 'bank_account_id');

        return view('bank-reconciliation.batch_upload', compact('admin', 'bankAccountsEnabled'));
    }
    
    /**
     * Upload and process Excel file
     */
    // public function upload(Request $request)
    // {
    //     $request->validate([
    //         'excel_file' => 'required|mimes:xlsx,xls|max:10240'
    //     ]);
        
    //     try {
    //         $file = $request->file('excel_file');
    //         $fileName = time() . '_' . $file->getClientOriginalName();
    //         $filePath = $file->move(public_path('bank_statements'), $fileName);
            
    //         // Load Excel file
    //         $spreadsheet = IOFactory::load($filePath);
    //         $worksheet = $spreadsheet->getActiveSheet();
    //         $rows = $worksheet->toArray();
            
    //         // Generate batch ID
    //         $batchId = uniqid('BATCH_');
    //         $userId = Auth::id();
    //         $insertedCount = 0;
            
    //         // Skip header row and process data
    //         foreach ($rows as $index => $row) {
    //             if ($index === 0) continue; // Skip header
    //             // Skip empty rows
    //             if (empty($row[0]) && empty($row[2])) continue;
    //             // dd($row);
    //             // Map Excel columns (adjust based on your Excel structure)
    //             $transactionDate = $row[3] ?? '';
    //             $transactionId = $row[1] ?? '';
    //             $valueDate = $row[2] ?? '';
    //             $transaction_posted_date = $row[4] ?? '';
    //             $description = $row[6] ?? '';
    //             $chequeNumber = $row[5] ?? '';
    //             $withdrawal = $this->parseAmount($row[7] ?? 0);
    //             $deposit = $this->parseAmount($row[8] ?? 0);
    //             $balance = $this->parseAmount($row[9] ?? 0);
    //             // Generate reference number from description or cheque
    //             $referenceNumber = $this->extractReference($description, $chequeNumber);
                
    //             // Insert into database
    //             DB::table('bank_statements')->insert([
    //                 'user_id' => $userId,
    //                 'upload_batch_id' => $batchId,
    //                 'file_name' => $fileName,
    //                 'transaction_date' => $transactionDate,
    //                 'transaction_id' => $transactionId,
    //                 'transaction_posted_date' => $transaction_posted_date,
    //                 'value_date' => $valueDate,
    //                 'description' => $description,
    //                 'reference_number' => $referenceNumber,
    //                 'cheque_number' => $chequeNumber,
    //                 'withdrawal' => $withdrawal,
    //                 'deposit' => $deposit,
    //                 'balance' => $balance,
    //                 'category' => 'uncategory',
    //                 'match_status' => 'unmatched',
    //                 'created_at' => now(),
    //                 'updated_at' => now()
    //             ]);
                
    //             $insertedCount++;
    //         }
            
    //         return response()->json([
    //             'success' => true,
    //             'message' => "Successfully imported {$insertedCount} transactions",
    //             'batch_id' => $batchId,
    //             'count' => $insertedCount
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error processing file: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function upload(Request $request)
    {
        $rules = [
            'excel_file' => 'required|mimes:xlsx,xls|max:10240',
        ];
        if (Schema::hasTable('bank_reconciliation_accounts') && Schema::hasColumn('bank_statements', 'bank_account_id')) {
            $rules['bank_account_id'] = 'required|exists:bank_reconciliation_accounts,id';
        }
        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id') && Schema::hasTable('company_tbl')) {
            $rules['company_id'] = 'required|integer|exists:company_tbl,id';
        }
        $request->validate($rules);

        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id') && Schema::hasTable('company_tbl')
            && $request->filled('bank_account_id') && $request->filled('company_id')) {
            $belongs = DB::table('bank_reconciliation_accounts')
                ->where('id', (int) $request->bank_account_id)
                ->where('company_id', (int) $request->company_id)
                ->exists();
            if (! $belongs) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected bank account does not belong to the selected company.',
                ], 422);
            }
        }

        try {
            $file     = $request->file('excel_file');
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . $originalName;
            $filePath = $file->move(public_path('bank_statements'), $fileName);

            // Load the spreadsheet
            $spreadsheet = IOFactory::load($filePath);
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray();

            // Generate a unique batch ID for this upload
            $batchId  = uniqid('BATCH_');
            $userId   = Auth::id();
            $bankAccountId = null;
            if (Schema::hasColumn('bank_statements', 'bank_account_id') && $request->filled('bank_account_id')) {
                $bankAccountId = (int) $request->bank_account_id;
            }

            $insertedCount  = 0;
            $duplicateCount = 0;
            $skippedCount   = 0; // rows that are empty / non-data

            // DATA_START_ROW: rows 0-15 = metadata, row 16 = column headers.
            // Transaction data begins at index 17.
            $dataStartIndex = 17;

            foreach ($rows as $index => $row) {
                // Skip all metadata rows and the header row
                if ($index < $dataStartIndex) {
                    continue;
                }

                // Skip completely empty rows (S.N. column and Tran.Id both empty)
                if (empty($row[0]) && empty($row[1])) {
                    $skippedCount++;
                    continue;
                }

                // ── Map columns ──────────────────────────────────────────
                // Store the date exactly as it appears in Excel (e.g. "01/Jan/2026").
                // Carbon::createFromFormat('d/M/Y', $value) in matchBill() handles this.
                $transactionDate        = trim($row[3] ?? '');
                $transactionId          = trim($row[1] ?? '');
                $valueDate              = trim($row[2] ?? '');
                $transactionPostedDate  = trim($row[4] ?? '');
                $chequeNumber           = trim($row[5] ?? '');
                $description            = trim($row[6] ?? '');
                $withdrawal             = $this->parseAmount($row[7] ?? 0);
                $deposit                = $this->parseAmount($row[8] ?? 0);
                $balance                = $this->parseAmount($row[9] ?? 0);

                // ── Duplicate check ───────────────────────────────────────
                // A row is considered a duplicate when BOTH the transaction_date
                // AND description already exist in the bank_statements table.
                $isDuplicate = DB::table('bank_statements')
                    ->where('transaction_date', $transactionDate)
                    ->where('description', $description)
                    ->exists();

                if ($isDuplicate) {
                    $duplicateCount++;
                    continue; // Skip without inserting
                }

                // ── Extract reference number ──────────────────────────────
                $referenceNumber = $this->extractReference($description, $chequeNumber);

                // ── Insert into database ──────────────────────────────────
                $insertRow = [
                    'user_id'                  => $userId,
                    'upload_batch_id'          => $batchId,
                    'file_name'                => $fileName,
                    'transaction_date'         => $transactionDate,       // "01/Jan/2026"
                    'transaction_id'           => $transactionId,
                    'transaction_posted_date'  => $transactionPostedDate,
                    'value_date'               => $valueDate,
                    'description'              => $description,
                    'reference_number'         => $referenceNumber,
                    'cheque_number'            => $chequeNumber,
                    'withdrawal'               => $withdrawal,
                    'deposit'                  => $deposit,
                    'balance'                  => $balance,
                    'category'                 => 'uncategory',
                    'match_status'             => 'unmatched',
                    'created_at'               => now(),
                    'updated_at'               => now(),
                ];
                if ($bankAccountId !== null) {
                    $insertRow['bank_account_id'] = $bankAccountId;
                }
                DB::table('bank_statements')->insert($insertRow);

                $insertedCount++;
            }

            if (Schema::hasTable('bank_statement_upload_batches') && $bankAccountId !== null) {
                DB::table('bank_statement_upload_batches')->insert([
                    'bank_account_id'    => $bankAccountId,
                    'upload_batch_id'    => $batchId,
                    'original_file_name' => $originalName,
                    'stored_file_name'   => $fileName,
                    'rows_imported'      => $insertedCount,
                    'duplicates'         => $duplicateCount,
                    'skipped'            => $skippedCount,
                    'user_id'            => $userId,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }

            // Build a human-readable summary message
            $message = "Successfully imported {$insertedCount} transaction(s).";
            if ($duplicateCount > 0) {
                $message .= " {$duplicateCount} duplicate(s) were skipped (same date & description already exist).";
            }
            return response()->json([
                'success'         => true,
                'message'         => $message,
                'batch_id'        => $batchId,
                'count'           => $insertedCount,
                'duplicates'      => $duplicateCount,
                'empty_rows'      => $skippedCount,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get bank statements data with filters
     */
    public function getStatements(Request $request)
    {
        if ($request->has('stats_only')) {
            return response()->json($this->buildDashboardStatistics($request));
        }

        $perPage = $request->get('per_page', 50);

        $paginator = $this->bankStatementsFilteredQuery($request)->paginate($perPage);
        $paginator->getCollection()->transform(function ($row) {
            $this->hydrateStatementBillDisplayFields($row);

            return $row;
        });

        $payload = $paginator->toArray();
        $payload['dashboard'] = $this->buildDashboardStatistics($request);

        return response()->json($payload);
    }

    /**
     * SQL expression for merged chart-account ids (statement vs bill), for FIND_IN_SET filters.
     */
    private function bankReconMergedNatureAccountIdsExpr(): ?string
    {
        $hasBs   = Schema::hasTable('bank_statements') && Schema::hasColumn('bank_statements', 'br_nature_account_ids');
        $hasBill = Schema::hasTable('bill_tbl') && Schema::hasColumn('bill_tbl', 'br_nature_account_ids');
        if ($hasBs && $hasBill) {
            return 'TRIM(COALESCE(NULLIF(TRIM(IFNULL(bs.br_nature_account_ids, \'\')), \'\'), bill.br_nature_account_ids))';
        }
        if ($hasBs) {
            return 'TRIM(IFNULL(bs.br_nature_account_ids, \'\'))';
        }
        if ($hasBill) {
            return 'TRIM(IFNULL(bill.br_nature_account_ids, \'\'))';
        }

        return null;
    }

    /**
     * Same filters as the main statement list, but without match_status / match_statuses so drill-down can force "matched" only.
     */
    private function bankStatementsFilteredQueryForDrilldown(Request $request): Builder
    {
        $params = $request->all();
        unset($params['match_status'], $params['match_statuses']);

        return $this->bankStatementsFilteredQuery(Request::create('/', 'GET', $params));
    }

    /**
     * Paginated matched statements sharing the same nature of payment (chart account ids), AJAX for drill-down panel.
     */
    public function drilldownStatementsByNature(Request $request)
    {
        $ids = $this->requestIntIdArray($request, 'nature_account_ids');
        if (count($ids) === 0 && $request->filled('nature_account_ids')) {
            $raw = $request->input('nature_account_ids');
            if (is_string($raw) && $raw !== '') {
                $ids = array_values(array_unique(array_filter(
                    array_map(static fn ($x) => (int) $x, preg_split('/\s*,\s*/', $raw)),
                    static fn (int $x): bool => $x > 0
                )));
            }
        }
        if (count($ids) === 0) {
            return response()->json(['success' => false, 'message' => 'nature_account_ids is required.'], 422);
        }

        $expr = $this->bankReconMergedNatureAccountIdsExpr();
        if ($expr === null) {
            return response()->json([
                'success'    => true,
                'data'       => [],
                'total'      => 0,
                'message'    => 'Nature columns are not available.',
                'pagination' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 25, 'total' => 0],
            ]);
        }

        $perPage = (int) $request->get('per_page', 25);
        $perPage = max(5, min(100, $perPage));

        $query = $this->bankStatementsFilteredQueryForDrilldown($request);
        $query->where('bs.match_status', '=', 'matched');
        $query->where(function ($outer) use ($expr, $ids) {
            foreach ($ids as $id) {
                $outer->orWhereRaw(
                    'FIND_IN_SET(?, REPLACE('.$expr.", ' ', ''))",
                    [(int) $id]
                );
            }
        });

        $paginator = $query->paginate($perPage);
        $paginator->getCollection()->transform(function ($row) {
            $this->hydrateStatementBillDisplayFields($row);

            return $row;
        });

        return response()->json($paginator->toArray());
    }

    /**
     * Paginated matched statements for a bill zone/branch (bill_tbl), AJAX for drill-down panel.
     */
    public function drilldownStatementsByZone(Request $request)
    {
        $zoneId   = (int) $request->input('zone_id', 0);
        $branchId = (int) $request->input('branch_id', 0);
        if ($zoneId <= 0 && $branchId <= 0) {
            return response()->json(['success' => false, 'message' => 'zone_id or branch_id is required.'], 422);
        }

        $perPage = (int) $request->get('per_page', 25);
        $perPage = max(5, min(100, $perPage));

        $query = $this->bankStatementsFilteredQueryForDrilldown($request);
        $query->where('bs.match_status', '=', 'matched');

        if ($zoneId > 0 && Schema::hasTable('bill_tbl') && Schema::hasColumn('bill_tbl', 'zone_id')) {
            $query->where('bill.zone_id', '=', $zoneId);
        }
        if ($branchId > 0 && Schema::hasTable('bill_tbl') && Schema::hasColumn('bill_tbl', 'branch_id')) {
            $query->where('bill.branch_id', '=', $branchId);
        }

        $paginator = $query->paginate($perPage);
        $paginator->getCollection()->transform(function ($row) {
            $this->hydrateStatementBillDisplayFields($row);

            return $row;
        });

        return response()->json($paginator->toArray());
    }

    /**
     * Export filtered statements as CSV or XLSX (same filters as list; max 25k rows).
     */
    public function exportStatements(Request $request)
    {
        $format = strtolower((string) $request->get('format', 'csv'));
        if (! in_array($format, ['csv', 'xlsx'], true)) {
            $format = 'csv';
        }

        $rows = $this->bankStatementsFilteredQuery($request)->limit(25000)->get();
        // dd($rows);
        $headers = [
            'ID',
            'Transaction date',
            'Value date',
            'Account number',
            'Bank',
            'Description',
            'Reference',
            'Transaction ID',
            'Cheque',
            'Withdrawal',
            'Deposit',
            'Balance',
            'Category',
            'Match status',
            'Matched date',
            'Bill number',
            'Vendor',
            'Matched by',
        ];

        $baseName = 'bank_statements_' . date('Y-m-d_His');

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($rows, $headers) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($out, $headers);
                foreach ($rows as $r) {
                    fputcsv($out, $this->bankStatementExportRow($r));
                }
                fclose($out);
            }, $baseName . '.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');
        $rowNum = 2;
        foreach ($rows as $r) {
            $sheet->fromArray($this->bankStatementExportRow($r), null, 'A' . $rowNum);
            $rowNum++;
        }
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $baseName . '.xlsx', [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * @param  \stdClass  $r
     */
    private function bankStatementExportRow($r): array
    {
        // Bill comes from bill_tbl joined via bank_bill_matches.bill_id or bs.matched_bill_id (see applyBankStatementBillJoins).
        $billNumber = trim((string) ($r->resolved_bill_gen_number ?? ''));
        if ($billNumber === '' && ! empty($r->resolved_bill_gen_number ?? null)) {
            $billNumber = trim((string) $r->resolved_bill_gen_number);
        }
        $vendorName = trim((string) ($r->resolved_vendor_name ?? ''));
        $matchedByDisplay = trim((string) ($r->bbm_matched_by_name ?? ''));
        if ($matchedByDisplay === '') {
            $matchedByDisplay = trim((string) ($r->matched_by_name ?? ''));
        }
        if ($matchedByDisplay === '') {
            $matchedByDisplay = (string) ($r->bbm_matched_by_username ?? $r->matched_by_username ?? '');
        }

        $matchedWhen = '';
        if (! empty($r->matched_date)) {
            $matchedWhen = (string) $r->matched_date;
        } elseif (! empty($r->bank_match_matched_at)) {
            $matchedWhen = (string) $r->bank_match_matched_at;
        }

        return [
            $r->id ?? '',
            $r->transaction_date ?? '',
            $r->value_date ?? '',
            $r->bank_account_number ?? '',
            $r->bank_account_bank_name ?? '',
            $r->description ?? '',
            $r->reference_number ?? '',
            $r->transaction_id ?? '',
            $r->cheque_number ?? '',
            $r->withdrawal ?? '',
            $r->deposit ?? '',
            $r->balance ?? '',
            $r->category ?? '',
            $r->match_status ?? '',
            $matchedWhen,
            $billNumber,
            $vendorName,
            $matchedByDisplay,
        ];
    }

    /**
     * Join bank_bill_matches (when present) and bill_tbl so bill id resolves as
     * COALESCE(active_match.bill_id, bs.matched_bill_id), then bill rows supply number/vendor.
     */
    /**
     * Fill bill_number / vendor_name from joined bill_tbl (resolved_* aliases) for API consumers.
     */
    private function hydrateStatementBillDisplayFields(object $row): void
    {
        $bn = trim((string) ($row->resolved_bill_number ?? ''));
        if ($bn === '' && ! empty($row->resolved_bill_gen_number ?? null)) {
            $bn = trim((string) $row->resolved_bill_gen_number);
        }
        $row->bill_number = $bn !== '' ? $bn : null;
        $row->vendor_name = isset($row->resolved_vendor_name) && $row->resolved_vendor_name !== ''
            ? $row->resolved_vendor_name
            : null;
    }

    private function applyBankStatementBillJoins(Builder $query): void
    {
        if (Schema::hasTable('bank_bill_matches')) {
            $query->leftJoin('bank_bill_matches as bbm', function ($join) {
                $join->on('bbm.bank_statement_id', '=', 'bs.id');
                if (Schema::hasColumn('bank_bill_matches', 'status')) {
                    $join->where('bbm.status', '=', 'active');
                }
            })
                ->leftJoin('users as bbm_matcher', 'bbm_matcher.id', '=', 'bbm.matched_by')
                ->leftJoin('bill_tbl as bill', function ($join) {
                    $join->whereRaw('bill.id = COALESCE(bbm.bill_id, bs.matched_bill_id)');
                });
        } else {
            $query->leftJoin('bill_tbl as bill', 'bs.matched_bill_id', '=', 'bill.id');
        }
    }

    /**
     * @return list<int>
     */
    private function requestIntIdArray(Request $request, string $key): array
    {
        $v = $request->input($key);
        if (! is_array($v)) {
            if ($v === null || $v === '' || $v === false) {
                return [];
            }
            $v = [$v];
        }

        return array_values(array_unique(array_filter(array_map('intval', $v), static fn (int $x): bool => $x > 0)));
    }

    /**
     * @return list<string>
     */
    private function requestStringList(Request $request, string $key): array
    {
        $v = $request->input($key);
        if (is_string($v) && str_contains($v, ',')) {
            $v = array_map('trim', explode(',', $v));
        }
        if (! is_array($v)) {
            return ($v === null || $v === '' || $v === false) ? [] : [trim((string) $v)];
        }

        return array_values(array_filter(array_map(static fn ($x) => trim((string) $x), $v), static fn (string $s): bool => $s !== ''));
    }

    /**
     * Financial year tokens from the UI: "Y-m-d|Y-m-d" per selected FY.
     *
     * @return list<array{0: string, 1: string}>
     */
    private function requestFinancialYearRanges(Request $request): array
    {
        $raw = $request->input('financial_year_ranges', []);
        if (! is_array($raw)) {
            $raw = $raw ? [(string) $raw] : [];
        }
        $out = [];
        foreach ($raw as $item) {
            $parts = explode('|', (string) $item, 2);
            if (count($parts) !== 2) {
                continue;
            }
            $df = trim($parts[0]);
            $dt = trim($parts[1]);
            if ($df === '' || $dt === '') {
                continue;
            }
            $out[] = [$df, $dt];
        }

        return $out;
    }

    private function applyIncomeMatchValueToQuery(Builder $query, string $val): void
    {
        if ($val !== 'income_matched' && $val !== 'income_unmatched') {
            return;
        }
        if ($val === 'income_matched') {
            $query->where('bs.income_match_status', 'income_matched');
        } elseif ($val === 'income_unmatched') {
            $query->where(function ($q) {
                $q->where('bs.income_match_status', 'income_unmatched')
                    ->orWhereNull('bs.income_match_status');
            });
        }
    }

    private function applyRadiantMatchValueToQuery(Builder $query, string $val): void
    {
        if (! in_array($val, ['radiant_matched', 'radiant_unmatched', 'radiant_keyword_only'], true)) {
            return;
        }
        if ($val === 'radiant_matched') {
            $query->where('bs.radiant_match_status', 'radiant_matched');
        } elseif ($val === 'radiant_unmatched') {
            $query->where(function ($q) {
                $q->where('bs.radiant_match_status', 'radiant_unmatched')
                    ->orWhereNull('bs.radiant_match_status');
            });
        } elseif ($val === 'radiant_keyword_only') {
            $query->whereNotNull('bs.radiant_match_against')
                ->where('bs.radiant_match_against', '!=', '')
                ->where(function ($q) {
                    $q->whereNull('bs.radiant_match_status')
                        ->orWhere('bs.radiant_match_status', '!=', 'radiant_matched');
                });
        }
    }

    /**
     * Bank statements list query with joins and all UI filters applied.
     */
    // private function bankStatementsFilteredQuery(Request $request): Builder
    // {
    //     $select = [
    //         'bs.*',
    //         'matched_user.user_fullname as matched_by_name',
    //         'matched_user.username as matched_by_username',
    //         'income_user.user_fullname as income_matched_by_name',
    //         'income_user.username as income_matched_by_username',
    //         'bill.bill_number as resolved_bill_number',
    //         'bill.vendor_name as resolved_vendor_name',
    //         'bill.grand_total_amount as bill_amount',
    //     ];
    //     if (Schema::hasColumn('bill_tbl', 'bill_gen_number')) {
    //         $select[] = 'bill.bill_gen_number as resolved_bill_gen_number';
    //     }
    //     if (Schema::hasColumn('bank_statements', 'radiant_matched_by')) {
    //         $select[] = 'radiant_user.user_fullname as radiant_matched_by_name';
    //         $select[] = 'radiant_user.username as radiant_matched_by_username';
    //     }
    //     if (Schema::hasColumn('bank_statements', 'bank_account_id') && Schema::hasTable('bank_reconciliation_accounts')) {
    //         $select[] = 'bra.account_number as bank_account_number';
    //         $select[] = 'bra.bank_name as bank_account_bank_name';
    //     }
    //     if (Schema::hasTable('bank_bill_matches')) {
    //         $select[] = 'bbm_matcher.user_fullname as bbm_matched_by_name';
    //         $select[] = 'bbm_matcher.username as bbm_matched_by_username';
    //         $select[] = 'bbm.matched_at as bank_match_matched_at';
    //     }

    //     $query = DB::table('bank_statements as bs')
    //         ->leftJoin('users as matched_user', 'bs.matched_by', '=', 'matched_user.id')
    //         ->leftJoin('users as income_user', 'bs.income_matched_by', '=', 'income_user.id');
    //     if (Schema::hasColumn('bank_statements', 'radiant_matched_by')) {
    //         $query->leftJoin('users as radiant_user', 'bs.radiant_matched_by', '=', 'radiant_user.id');
    //     }
    //     if (Schema::hasColumn('bank_statements', 'bank_account_id') && Schema::hasTable('bank_reconciliation_accounts')) {
    //         $query->leftJoin('bank_reconciliation_accounts as bra', 'bs.bank_account_id', '=', 'bra.id');
    //     }

    //     $this->applyBankStatementBillJoins($query);

    //     $query->select($select);

    //     if ($request->filled('bank_account_id') && Schema::hasColumn('bank_statements', 'bank_account_id')) {
    //         $query->where('bs.bank_account_id', (int) $request->bank_account_id);
    //     }

    //     if ($request->filled('date_from')) {
    //         $query->whereRaw(
    //             "STR_TO_DATE(bs.transaction_date, '%d/%b/%Y') >= ?",
    //             [$request->date_from]
    //         );
    //     }

    //     if ($request->filled('date_to')) {
    //         $query->whereRaw(
    //             "STR_TO_DATE(bs.transaction_date, '%d/%b/%Y') <= ?",
    //             [$request->date_to]
    //         );
    //     }

    //     if ($request->filled('amount_min') || $request->filled('amount_max')) {
    //         $amountMin = $request->filled('amount_min')
    //             ? (float) str_replace(',', '', $request->amount_min)
    //             : null;
    //         $amountMax = $request->filled('amount_max')
    //             ? (float) str_replace(',', '', $request->amount_max)
    //             : null;
    //         $query->where(function ($q) use ($amountMin, $amountMax) {
    //             if ($amountMin !== null) {
    //                 $q->whereRaw('COALESCE(bs.withdrawal, bs.deposit) >= ?', [$amountMin]);
    //             }
    //             if ($amountMax !== null) {
    //                 $q->whereRaw('COALESCE(bs.withdrawal, bs.deposit) <= ?', [$amountMax]);
    //             }
    //         });
    //     }

    //     if ($request->filled('match_status')) {
    //         $query->where('bs.match_status', $request->match_status);
    //     }

    //     if ($request->filled('income_match')) {
    //         $val = $request->income_match;
    //         if ($val === 'income_matched') {
    //             $query->where('bs.income_match_status', 'income_matched');
    //         } elseif ($val === 'income_unmatched') {
    //             $query->where(function ($q) {
    //                 $q->where('bs.income_match_status', 'income_unmatched')
    //                     ->orWhereNull('bs.income_match_status');
    //             });
    //         }
    //     }

    //     if ($request->filled('radiant_match') && Schema::hasColumn('bank_statements', 'radiant_match_status')) {
    //         $val = $request->radiant_match;
    //         if ($val === 'radiant_matched') {
    //             $query->where('bs.radiant_match_status', 'radiant_matched');
    //         } elseif ($val === 'radiant_unmatched') {
    //             $query->where(function ($q) {
    //                 $q->where('bs.radiant_match_status', 'radiant_unmatched')
    //                     ->orWhereNull('bs.radiant_match_status');
    //             });
    //         } elseif ($val === 'radiant_keyword_only' && Schema::hasColumn('bank_statements', 'radiant_match_against')) {
    //             $query->whereNotNull('bs.radiant_match_against')
    //                 ->where('bs.radiant_match_against', '!=', '')
    //                 ->where(function ($q) {
    //                     $q->whereNull('bs.radiant_match_status')
    //                         ->orWhere('bs.radiant_match_status', '!=', 'radiant_matched');
    //                 });
    //         }
    //     }

    //     if ($request->filled('reference_number')) {
    //         $ref = $request->reference_number;
    //         $query->where(function ($q) use ($ref) {
    //             $q->where('bs.reference_number', 'LIKE', '%' . $ref . '%')
    //                 ->orWhere('bs.transaction_id', 'LIKE', '%' . $ref . '%');
    //         });
    //     }

    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('bs.description', 'LIKE', "%{$search}%")
    //                 ->orWhere('bs.reference_number', 'LIKE', "%{$search}%")
    //                 ->orWhere('bs.cheque_number', 'LIKE', "%{$search}%");
    //         });
    //     }

    //     if (Schema::hasColumn('bank_statements', 'matched_date')) {
    //         if ($request->filled('matched_date_from')) {
    //             $query->whereDate('bs.matched_date', '>=', $request->matched_date_from);
    //         }
    //         if ($request->filled('matched_date_to')) {
    //             $query->whereDate('bs.matched_date', '<=', $request->matched_date_to);
    //         }
    //     }

    //     return $query->orderBy('bs.transaction_date', 'desc')->orderBy('bs.id', 'desc');
    // }
    private function bankStatementsFilteredQuery(Request $request): Builder
    {
        $select = [
            'bs.*',
            'matched_user.user_fullname as matched_by_name',
            'matched_user.username as matched_by_username',
            'income_user.user_fullname as income_matched_by_name',
            'income_user.username as income_matched_by_username',
            'bill.bill_number as resolved_bill_number',
            'bill.bill_gen_number as resolved_bill_gen_number',
            'bill.vendor_name as resolved_vendor_name',
            'bill.grand_total_amount as bill_amount',
            'bill.zone_name as resolved_bill_zone_name',
            'bill.branch_name as resolved_bill_branch_name',
            'radiant_user.user_fullname as radiant_matched_by_name',
            'radiant_user.username as radiant_matched_by_username',
            'bra.account_number as bank_account_number',
            'bra.bank_name as bank_account_bank_name',
            'bbm_matcher.user_fullname as bbm_matched_by_name',
            'bbm_matcher.username as bbm_matched_by_username',
            'bbm.matched_at as bank_match_matched_at',
            'bill.id as resolved_bill_id',
            'bill.vendor_id as resolved_vendor_id',
            'bill.zone_id as resolved_bill_zone_id',
            'bill.branch_id as resolved_bill_branch_id',
        ];

        if (Schema::hasTable('bank_statements') && Schema::hasColumn('bank_statements', 'br_nature_account_names')
            && Schema::hasTable('bill_tbl') && Schema::hasColumn('bill_tbl', 'br_nature_account_names')) {
            $select[] = DB::raw(
                "COALESCE(NULLIF(TRIM(bs.br_nature_account_names), ''), bill.br_nature_account_names) as resolved_br_nature_account_names"
            );
        } elseif (Schema::hasTable('bill_tbl') && Schema::hasColumn('bill_tbl', 'br_nature_account_names')) {
            $select[] = 'bill.br_nature_account_names as resolved_br_nature_account_names';
        } elseif (Schema::hasTable('bank_statements') && Schema::hasColumn('bank_statements', 'br_nature_account_names')) {
            $select[] = 'bs.br_nature_account_names as resolved_br_nature_account_names';
        }

        if (Schema::hasTable('bank_statements') && Schema::hasColumn('bank_statements', 'br_nature_account_ids')
            && Schema::hasTable('bill_tbl') && Schema::hasColumn('bill_tbl', 'br_nature_account_ids')) {
            $select[] = DB::raw(
                "NULLIF(TRIM(COALESCE(NULLIF(TRIM(bs.br_nature_account_ids), ''), bill.br_nature_account_ids)), '') as resolved_br_nature_account_ids"
            );
        } elseif (Schema::hasTable('bill_tbl') && Schema::hasColumn('bill_tbl', 'br_nature_account_ids')) {
            $select[] = 'bill.br_nature_account_ids as resolved_br_nature_account_ids';
        } elseif (Schema::hasTable('bank_statements') && Schema::hasColumn('bank_statements', 'br_nature_account_ids')) {
            $select[] = 'bs.br_nature_account_ids as resolved_br_nature_account_ids';
        }

        if (Schema::hasTable('bill_lines_tbl')) {
            $select[] = DB::raw("(SELECT GROUP_CONCAT(DISTINCT TRIM(bl.account) SEPARATOR ', ')
                FROM bill_lines_tbl AS bl
                WHERE bl.bill_id = bill.id
                  AND TRIM(IFNULL(bl.account, '')) <> '') AS bill_line_account_names");
        }

        $query = DB::table('bank_statements as bs')
            ->leftJoin('users as matched_user', 'bs.matched_by', '=', 'matched_user.id')
            ->leftJoin('users as income_user', 'bs.income_matched_by', '=', 'income_user.id')
            ->leftJoin('users as radiant_user', 'bs.radiant_matched_by', '=', 'radiant_user.id')
            ->leftJoin('bank_reconciliation_accounts as bra', 'bs.bank_account_id', '=', 'bra.id');

        $this->applyBankStatementBillJoins($query);

        $query->select($select);

        $bankAccountIds = $this->requestIntIdArray($request, 'bank_account_ids');
        if (count($bankAccountIds) > 0 && Schema::hasColumn('bank_statements', 'bank_account_id')) {
            $query->whereIn('bs.bank_account_id', $bankAccountIds);
        } elseif ($request->filled('bank_account_id') && Schema::hasColumn('bank_statements', 'bank_account_id')) {
            $query->where('bs.bank_account_id', (int) $request->bank_account_id);
        } elseif ($request->filled('company_id') && Schema::hasColumn('bank_reconciliation_accounts', 'company_id')) {
            $query->where('bra.company_id', (int) $request->company_id);
        }

        $fyRanges = $this->requestFinancialYearRanges($request);
        if (count($fyRanges) > 0) {
            $query->where(function ($outer) use ($fyRanges) {
                foreach ($fyRanges as $pair) {
                    [$df, $dt] = $pair;
                    $outer->orWhere(function ($q) use ($df, $dt) {
                        $q->whereRaw(
                            "STR_TO_DATE(bs.transaction_date, '%d/%b/%Y') >= ?",
                            [$df]
                        )->whereRaw(
                            "STR_TO_DATE(bs.transaction_date, '%d/%b/%Y') <= ?",
                            [$dt]
                        );
                    });
                }
            });
        } else {
            if ($request->filled('date_from')) {
                $query->whereRaw(
                    "STR_TO_DATE(bs.transaction_date, '%d/%b/%Y') >= ?",
                    [$request->date_from]
                );
            }

            if ($request->filled('date_to')) {
                $query->whereRaw(
                    "STR_TO_DATE(bs.transaction_date, '%d/%b/%Y') <= ?",
                    [$request->date_to]
                );
            }
        }

        if ($request->filled('amount_min') || $request->filled('amount_max')) {
            $amountMin = $request->filled('amount_min')
                ? (float) str_replace(',', '', $request->amount_min)
                : null;
            $amountMax = $request->filled('amount_max')
                ? (float) str_replace(',', '', $request->amount_max)
                : null;
            $query->where(function ($q) use ($amountMin, $amountMax) {
                if ($amountMin !== null) {
                    $q->whereRaw('COALESCE(bs.withdrawal, bs.deposit) >= ?', [$amountMin]);
                }
                if ($amountMax !== null) {
                    $q->whereRaw('COALESCE(bs.withdrawal, bs.deposit) <= ?', [$amountMax]);
                }
            });
        }

        $allowedMatchStatuses = ['matched', 'unmatched', 'partially_matched'];
        $matchStatuses = array_values(array_intersect(
            $allowedMatchStatuses,
            $this->requestStringList($request, 'match_statuses')
        ));
        if (count($matchStatuses) > 0) {
            $query->whereIn('bs.match_status', $matchStatuses);
        } elseif ($request->filled('match_status')) {
            $query->where('bs.match_status', $request->match_status);
        }

        $allowedIncome = ['income_matched', 'income_unmatched'];
        $incomeMatches = array_values(array_intersect(
            $allowedIncome,
            $this->requestStringList($request, 'income_matches')
        ));
        if (count($incomeMatches) > 0) {
            $query->where(function ($outer) use ($incomeMatches) {
                foreach ($incomeMatches as $val) {
                    $outer->orWhere(function ($q) use ($val) {
                        $this->applyIncomeMatchValueToQuery($q, $val);
                    });
                }
            });
        } elseif ($request->filled('income_match')) {
            $this->applyIncomeMatchValueToQuery($query, (string) $request->income_match);
        }

        $allowedRadiant = ['radiant_matched', 'radiant_unmatched', 'radiant_keyword_only'];
        $radiantMatches = array_values(array_intersect(
            $allowedRadiant,
            $this->requestStringList($request, 'radiant_matches')
        ));
        if (count($radiantMatches) > 0) {
            $query->where(function ($outer) use ($radiantMatches) {
                foreach ($radiantMatches as $val) {
                    $outer->orWhere(function ($q) use ($val) {
                        $this->applyRadiantMatchValueToQuery($q, $val);
                    });
                }
            });
        } elseif ($request->filled('radiant_match')) {
            $this->applyRadiantMatchValueToQuery($query, (string) $request->radiant_match);
        }

        $allowedTxn = ['deposit', 'withdrawal', 'income', 'expense'];
        $txnTypes = array_values(array_intersect(
            $allowedTxn,
            array_map('strtolower', $this->requestStringList($request, 'txn_types'))
        ));
        if (count($txnTypes) > 0) {
            $query->where(function ($q) use ($txnTypes) {
                foreach ($txnTypes as $t) {
                    if ($t === 'deposit' || $t === 'income') {
                        $q->orWhereRaw('COALESCE(bs.deposit, 0) > 0');
                    } elseif ($t === 'withdrawal' || $t === 'expense') {
                        $q->orWhereRaw('COALESCE(bs.withdrawal, 0) > 0');
                    }
                }
            });
        }

        $zoneIds = $this->requestIntIdArray($request, 'zone_ids');
        if (count($zoneIds) > 0 && Schema::hasTable('bill_tbl') && Schema::hasColumn('bill_tbl', 'zone_id')) {
            $query->whereIn('bill.zone_id', $zoneIds);
        }

        $branchIds = $this->requestIntIdArray($request, 'branch_ids');
        if (count($branchIds) > 0 && Schema::hasTable('bill_tbl') && Schema::hasColumn('bill_tbl', 'branch_id')) {
            $query->whereIn('bill.branch_id', $branchIds);
        }

        $categories = $this->requestStringList($request, 'categories');
        if (count($categories) > 0 && Schema::hasColumn('bank_statements', 'category')) {
            $special = array_values(array_intersect(['__categorized', '__uncategorized'], $categories));
            $rest = array_values(array_diff($categories, ['__categorized', '__uncategorized']));
            // Legacy data often uses literal "Uncategorized" (or variants) instead of NULL/empty.
            $uncatLiterals = ['uncategorized', 'un-categorized', 'un categorised', 'uncategorised'];
            if (count($special) > 0 && count($rest) === 0) {
                $query->where(function ($q) use ($special, $uncatLiterals) {
                    foreach ($special as $s) {
                        if ($s === '__uncategorized') {
                            $q->orWhere(function ($qq) use ($uncatLiterals) {
                                $qq->whereNull('bs.category')
                                    ->orWhereRaw("TRIM(IFNULL(bs.category, '')) = ''")
                                    ->orWhereIn(DB::raw('LOWER(TRIM(bs.category))'), $uncatLiterals);
                            });
                        } elseif ($s === '__categorized') {
                            $q->orWhere(function ($qq) use ($uncatLiterals) {
                                $qq->whereNotNull('bs.category')
                                    ->whereRaw("TRIM(bs.category) <> ''")
                                    ->whereNotIn(DB::raw('LOWER(TRIM(bs.category))'), $uncatLiterals);
                            });
                        }
                    }
                });
            } elseif (count($rest) > 0 && count($special) === 0) {
                $query->whereIn('bs.category', $rest);
            } elseif (count($special) > 0 && count($rest) > 0) {
                $query->where(function ($outer) use ($special, $rest, $uncatLiterals) {
                    $outer->where(function ($q) use ($special, $uncatLiterals) {
                        foreach ($special as $s) {
                            if ($s === '__uncategorized') {
                                $q->orWhere(function ($qq) use ($uncatLiterals) {
                                    $qq->whereNull('bs.category')
                                        ->orWhereRaw("TRIM(IFNULL(bs.category, '')) = ''")
                                        ->orWhereIn(DB::raw('LOWER(TRIM(bs.category))'), $uncatLiterals);
                                });
                            } elseif ($s === '__categorized') {
                                $q->orWhere(function ($qq) use ($uncatLiterals) {
                                    $qq->whereNotNull('bs.category')
                                        ->whereRaw("TRIM(bs.category) <> ''")
                                        ->whereNotIn(DB::raw('LOWER(TRIM(bs.category))'), $uncatLiterals);
                                });
                            }
                        }
                    })->orWhereIn('bs.category', $rest);
                });
            }
        }

        $vendorNames = $this->requestStringList($request, 'vendor_names');
        if (count($vendorNames) > 0 && Schema::hasTable('bill_tbl')) {
            $query->whereIn('bill.vendor_name', $vendorNames);
        }

        if ($request->filled('reference_number')) {
            $ref = $request->reference_number;
            $query->where(function ($q) use ($ref) {
                $q->where('bs.reference_number', 'LIKE', '%' . $ref . '%')
                    ->orWhere('bs.transaction_id', 'LIKE', '%' . $ref . '%');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bs.description', 'LIKE', "%{$search}%")
                    ->orWhere('bs.reference_number', 'LIKE', "%{$search}%")
                    ->orWhere('bs.cheque_number', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('matched_date_from')) {
            $query->whereDate('bs.matched_date', '>=', $request->matched_date_from);
        }
        if ($request->filled('matched_date_to')) {
            $query->whereDate('bs.matched_date', '<=', $request->matched_date_to);
        }

        $matchedByIds = $this->requestIntIdArray($request, 'matched_by_user_ids');
        if (count($matchedByIds) > 0) {
            $query->where(function ($q) use ($matchedByIds) {
                $q->whereIn('bs.matched_by', $matchedByIds);
                if (Schema::hasColumn('bank_statements', 'income_matched_by')) {
                    $q->orWhereIn('bs.income_matched_by', $matchedByIds);
                }
                if (Schema::hasTable('bank_bill_matches')) {
                    $q->orWhereIn('bbm.matched_by', $matchedByIds);
                }
            });
        } elseif ($request->filled('matched_by_user_id')) {
            $uid = (int) $request->matched_by_user_id;
            if ($uid > 0) {
                $query->where(function ($q) use ($uid) {
                    if (Schema::hasTable('bank_bill_matches')) {
                        $q->where('bbm.matched_by', $uid)
                            ->orWhere('bs.matched_by', $uid);
                    } else {
                        $q->where('bs.matched_by', $uid);
                    }
                    if (Schema::hasColumn('bank_statements', 'income_matched_by')) {
                        $q->orWhere('bs.income_matched_by', $uid);
                    }
                });
            }
        }

        $sortBy = (string) $request->get('sort_by', 'transaction_date');
        $sortDir = strtolower((string) $request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'transaction_date') {
            $query->orderByRaw("STR_TO_DATE(bs.transaction_date, '%d/%b/%Y') {$sortDir}")
                ->orderBy('bs.id', $sortDir);
        } else {
            $query->orderBy('bs.id', 'desc');
        }

        return $query;
    }
    
    /**
     * Dashboard stats for the same filter set as the statement list (date range, account, search, etc.).
     *
     * @return array<string, int|float>
     */
    private function buildDashboardStatistics(Request $request): array
    {
        $q = clone $this->bankStatementsFilteredQuery($request);
        // bankStatementsFilteredQuery returns DB Query\Builder (not Eloquent) — no getQuery().
        $q->reorder();
        $ids = $q->select('bs.id')->distinct()->pluck('id')->filter()->values();

        if ($ids->isEmpty()) {
            return [
                'total'                => 0,
                'matched'              => 0,
                'unmatched'            => 0,
                'partially_matched'    => 0,
                'total_amount'         => 0,
                'income_matched'       => 0,
                'income_unmatched'     => 0,
                'radiant_matched'      => 0,
                'radiant_unmatched'    => 0,
                'radiant_keyword_only' => 0,
            ];
        }

        $idList = $ids->all();

        $total = $ids->count();
        $matched = DB::table('bank_statements')->whereIn('id', $idList)->where('match_status', 'matched')->count();
        $unmatched = DB::table('bank_statements')->whereIn('id', $idList)->where('match_status', 'unmatched')->count();

        $totalWithdrawal = (float) DB::table('bank_statements')->whereIn('id', $idList)->sum('withdrawal');
        $totalDeposit = (float) DB::table('bank_statements')->whereIn('id', $idList)->sum('deposit');

        $incomeMatched = 0;
        $incomeUnmatched = 0;
        if (Schema::hasColumn('bank_statements', 'income_match_status')) {
            $incomeMatched = DB::table('bank_statements')
                ->whereIn('id', $idList)
                ->where('income_match_status', 'income_matched')
                ->count();
            $incomeUnmatched = DB::table('bank_statements')
                ->whereIn('id', $idList)
                ->where(function ($q) {
                    $q->where('income_match_status', 'income_unmatched')
                        ->orWhereNull('income_match_status');
                })
                ->count();
        }

        $radiantMatched = 0;
        $radiantUnmatched = 0;
        $radiantKeywordOnly = 0;
        if (Schema::hasColumn('bank_statements', 'radiant_match_status')) {
            $radiantMatched = DB::table('bank_statements')
                ->whereIn('id', $idList)
                ->where('radiant_match_status', 'radiant_matched')
                ->count();
            $radiantUnmatched = DB::table('bank_statements')
                ->whereIn('id', $idList)
                ->where(function ($q) {
                    $q->where('radiant_match_status', 'radiant_unmatched')
                        ->orWhereNull('radiant_match_status');
                })
                ->count();
            if (Schema::hasColumn('bank_statements', 'radiant_match_against')) {
                $radiantKeywordOnly = DB::table('bank_statements')
                    ->whereIn('id', $idList)
                    ->whereNotNull('radiant_match_against')
                    ->where('radiant_match_against', '!=', '')
                    ->where(function ($q) {
                        $q->whereNull('radiant_match_status')
                            ->orWhere('radiant_match_status', '!=', 'radiant_matched');
                    })
                    ->count();
            }
        }

        return [
            'total'                => $total,
            'matched'              => $matched,
            'unmatched'            => $unmatched,
            'partially_matched'    => $total - $matched - $unmatched,
            'total_amount'         => $totalWithdrawal + $totalDeposit,
            'income_matched'       => $incomeMatched,
            'income_unmatched'     => $incomeUnmatched,
            'radiant_matched'      => $radiantMatched,
            'radiant_unmatched'    => $radiantUnmatched,
            'radiant_keyword_only' => $radiantKeywordOnly,
        ];
    }
    
    /**
     * Search bills based on amount range
     */
    // public function searchBills(Request $request)
    // {
    //     $amount = $request->amount;
    //     $tolerance = $request->tolerance ?? 100; // Default tolerance of 100
        
    //     $minAmount = $amount - $tolerance;
    //     $maxAmount = $amount + $tolerance;
        
    //     $query = DB::table('bill_tbl')
    //         ->select(
    //             'id',
    //             'bill_number',
    //             'bill_gen_number',
    //             'vendor_name',
    //             'bill_date',
    //             'due_date',
    //             'grand_total_amount',
    //             'balance_amount',
    //             'bill_status',
    //             'zone_name',
    //             'branch_name',
    //             'company_name'
    //         )
    //         ->where('delete_status', 0)
    //         ->where('balance_amount', '>', 0);
        
    //     // Apply additional filters if provided
    //     if ($request->filled('vendor_name')) {
    //         $query->where('vendor_name', 'LIKE', '%' . $request->vendor_name . '%');
    //     }
        
    //     if ($request->filled('date_from')) {
    //         $query->where('bill_date', '>=', $request->date_from);
    //     }
        
    //     if ($request->filled('date_to')) {
    //         $query->where('bill_date', '<=', $request->date_to);
    //     }
        
    //     // Best matches (exact or very close)
    //     $bestMatches = (clone $query)
    //         ->whereBetween('balance_amount', [$minAmount, $maxAmount])
    //         ->orderByRaw('ABS(balance_amount - ?) ASC', [$amount])
    //         ->limit(10)
    //         ->get();
        
    //     // Possible matches (wider range)
    //     $widerMin = $amount - ($tolerance * 3);
    //     $widerMax = $amount + ($tolerance * 3);
        
    //     $possibleMatches = (clone $query)
    //         ->whereBetween('balance_amount', [$widerMin, $widerMax])
    //         ->whereNotIn('id', $bestMatches->pluck('id'))
    //         ->orderByRaw('ABS(balance_amount - ?) ASC', [$amount])
    //         ->limit(20)
    //         ->get();
        
    //     return response()->json([
    //         'success' => true,
    //         'best_matches' => $bestMatches,
    //         'possible_matches' => $possibleMatches,
    //         'search_amount' => $amount,
    //         'min_amount' => $minAmount,
    //         'max_amount' => $maxAmount
    //     ]);
    // }
    public function searchBills(Request $request)
    {
        $amount = $request->amount;
        $tolerance = $request->tolerance ?? 100; // Default tolerance of 100
        
        $query = DB::table('bill_tbl')
            ->select(
                'id',
                'bill_number',
                'bill_gen_number',
                'vendor_name',
                'bill_date',
                'due_date',
                'grand_total_amount',
                'balance_amount',
                'bill_status',
                'zone_name',
                'branch_name',
                'company_name'
            )
            ->where('delete_status', 0)
            ->where('balance_amount', '>', 0);
        // Apply vendor name filter
        if ($request->filled('vendor_name')) {
            $query->where('vendor_name', 'LIKE', '%' . $request->vendor_name . '%');
        }

        if ($request->filled('billno')) {
            $billno = trim($request->billno);
            $query->where('bill_gen_number', 'LIKE', "%{$billno}%");
        }
        // Apply bill status filter
        if ($request->filled('bill_status')) {
            $query->where('bill_status', $request->bill_status);
        }
        
        // Apply date range filters
        if ($request->filled('date_from')) {
            $query->whereRaw(
                "STR_TO_DATE(bill_date, '%d/%m/%Y') >= ?",
                [$request->date_from]
            );
        }
        
        if ($request->filled('date_to')) {
            $query->whereRaw(
                "STR_TO_DATE(bill_date, '%d/%m/%Y') <= ?",
                [$request->date_to]
            );
        }
        
        
        // Check if amount_min and amount_max are provided
        $hasAmountRange = $request->filled('amount_min') && $request->filled('amount_max');
        
        // Initialize min/max values for best matches
        $bestMinAmount = null;
        $bestMaxAmount = null;
        $widerMinAmount = null;
        $widerMaxAmount = null;
        
        if ($hasAmountRange) {
            // If amount range is provided, use it directly without tolerance
            $bestMinAmount = $request->amount_min;
            $bestMaxAmount = $request->amount_max;
            
            // For possible matches, use a slightly wider range (10% wider)
            $rangeWidth = $request->amount_max - $request->amount_min;
            $widerRange = $rangeWidth * 0.1; // 10% wider on each side
            
            $widerMinAmount = $request->amount_min - $widerRange;
            $widerMaxAmount = $request->amount_max + $widerRange;
            
        } else {
            // If no amount range, use tolerance-based calculation
            $bestMinAmount = $amount - $tolerance;
            $bestMaxAmount = $amount + $tolerance;
            
            // Wider range for possible matches
            $widerMinAmount = $amount - ($tolerance * 3);
            $widerMaxAmount = $amount + ($tolerance * 3);
        }
        
        // Clone the query for best matches
        $bestQuery = clone $query;
        // dd($bestMinAmount, $bestMaxAmount);
        // Best matches
        $bestMatches = $bestQuery
            ->whereBetween('balance_amount', [$bestMinAmount, $bestMaxAmount])
            ->orderByRaw('ABS(balance_amount - ?) ASC', [$amount])
            ->limit(10)
            ->get();
        
        // Clone the query for possible matches (excluding best matches)
        $possibleQuery = clone $query;
        
        // Possible matches (wider range)
        $possibleMatches = $possibleQuery
            ->whereBetween('balance_amount', [$widerMinAmount, $widerMaxAmount])
            ->whereNotIn('id', $bestMatches->pluck('id')->toArray())
            ->orderByRaw('ABS(balance_amount - ?) ASC', [$amount])
            ->limit(20)
            ->get();
        
        // Also apply amount range filter to the main query for counting
        if ($hasAmountRange) {
            $query->whereBetween('balance_amount', [$request->amount_min, $request->amount_max]);
        }
        
        $totalBills = DB::table('bill_tbl')
            ->where('delete_status', 0)
            ->where('balance_amount', '>', 0)
            ->count();
        
        $filteredCount = $query->count();
        
        return response()->json([
            'success' => true,
            'best_matches' => $bestMatches,
            'possible_matches' => $possibleMatches,
            'search_amount' => $amount,
            'tolerance_used' => $hasAmountRange ? null : $tolerance,
            'best_min_amount' => $bestMinAmount,
            'best_max_amount' => $bestMaxAmount,
            'wider_min_amount' => $widerMinAmount,
            'wider_max_amount' => $widerMaxAmount,
            'filter_type' => $hasAmountRange ? 'amount_range' : 'tolerance',
            'applied_filters' => [
                'amount_min' => $request->amount_min,
                'amount_max' => $request->amount_max,
                'vendor_name' => $request->vendor_name,
                'bill_status' => $request->bill_status,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to
            ],
            'total_bills' => $totalBills,
            'filtered_bills_count' => $filteredCount
        ]);
    }
    /**
     * Filter bills with advanced criteria
     */
    public function filterBills(Request $request)
    {
        $query = DB::table('bill_tbl')
            ->select(
                'id',
                'bill_number',
                'vendor_name',
                'bill_date',
                'due_date',
                'grand_total_amount',
                'balance_amount',
                'bill_status',
                'zone_name',
                'branch_name',
                'company_name',
                'payment_terms'
            )
            ->where('delete_status', 0)
            ->where('balance_amount', '>', 0);
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('bill_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('bill_date', '<=', $request->date_to);
        }
        
        if ($request->filled('amount_min')) {
            $query->where('balance_amount', '>=', $request->amount_min);
        }
        
        if ($request->filled('amount_max')) {
            $query->where('balance_amount', '<=', $request->amount_max);
        }
        
        if ($request->filled('vendor_name')) {
            $query->where('vendor_name', 'LIKE', '%' . $request->vendor_name . '%');
        }
        
        if ($request->filled('bill_number')) {
            $query->where('bill_number', 'LIKE', '%' . $request->bill_number . '%');
        }
        
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        if ($request->filled('bill_status')) {
            $query->where('bill_status', $request->bill_status);
        }
        
        // Reference number search
        if ($request->filled('reference')) {
            $query->where(function($q) use ($request) {
                $q->where('bill_number', 'LIKE', '%' . $request->reference . '%')
                  ->orWhere('vendor_name', 'LIKE', '%' . $request->reference . '%');
            });
        }
        
        $perPage = $request->get('per_page', 20);
        $bills = $query->orderBy('bill_date', 'desc')->paginate($perPage);
        
        return response()->json($bills);
    }
    
    /**
     * Match bank statement with bill.
     * Creates a bill_pay (bill made) record like VendorController::savebillmade, so the payment appears in Bill Made.
     */
    public function matchBill(Request $request)
    {
        $request->validate([
            'bank_statement_id' => 'required|integer',
            'bill_id' => 'required|integer',
            'matched_amount' => 'required|numeric',
            'match_type' => 'required|in:full,partial',
            'nature_account_ids' => 'required|array|min:1',
            'nature_account_ids.*' => 'integer',
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'file|max:15360',
            'attachment_tags' => 'nullable|array',
            'attachment_tags.*' => 'nullable|string|max:255',
            'attachment_type_ids' => 'nullable|array',
            'attachment_type_ids.*' => 'nullable|integer',
        ], [
            'nature_account_ids.required' => 'Nature of payment is required — select at least one chart account.',
            'nature_account_ids.min' => 'Nature of payment is required — select at least one chart account.',
            'attachments.required' => 'At least one attachment is required — upload a file on the Attachments tab (e.g. PO, bill copy).',
            'attachments.min' => 'At least one attachment is required — upload a file on the Attachments tab (e.g. PO, bill copy).',
        ]);
        DB::beginTransaction();
        try {
            $statementId = $request->bank_statement_id;
            $billId = $request->bill_id;
            $matchedAmount = (float) $request->matched_amount;
            $matchType = $request->match_type;
            $userId = Auth::id();
            
            $statement = DB::table('bank_statements')->where('id', $statementId)->first();
            $bill = DB::table('bill_tbl')->where('id', $billId)->first();
            
            if (!$statement || !$bill) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Statement or bill not found'
                ], 404);
            }
            
            $newBalance = $bill->balance_amount - $matchedAmount;
            $newBalance = max(0, $newBalance);
            $paymentDate = $statement->transaction_date
                            ? Carbon::createFromFormat('d/M/Y', $statement->transaction_date)->format('Y-m-d')
                            : now()->toDateString();
        

            // bank_statement_status: paid (full), partially (partial), pending (other)
            $bankStatementStatus = $matchType === 'full' ? 'Paid' : ($matchType === 'partial' ? 'Partially' : 'Pending');

            // 1) Update bank statement
            $stmtUp = [
                'match_status' => $matchType === 'full' ? 'matched' : 'partially_matched',
                'matched_bill_id' => $billId,
                'matched_amount' => $matchedAmount,
                'matched_date' => now(),
                'matched_by' => $userId,
                'notes' => $request->notes,
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('bank_statements', 'category')) {
                $stmtUp['category'] = 'category';
            }
            DB::table('bank_statements')
                ->where('id', $statementId)
                ->update($stmtUp);

            // Check if this bill_id already has an active (delete_status=0) bill made (bill_pay) record
            // from Bank Reconciliation – update it instead of creating a duplicate
            $existingLine = DB::table('bill_pay_lines_tbl as l')
                ->join('bill_pay_tbl as p', 'p.id', '=', 'l.bill_pay_id')
                ->where('l.bill_id', $billId)
                ->where('p.paid_through', 'Bank Reconciliation')
                ->where('p.delete_status', 0)           // skip soft-deleted bill_pay records
                ->select('l.bill_pay_id', 'l.id as line_id', 'l.amount as old_amount')
                ->first();

            $existingBillPay = $existingLine
                ? DB::table('bill_pay_tbl')->where('id', $existingLine->bill_pay_id)->first()
                : null;

            if ($existingBillPay) {
                // UPDATE existing bill_pay (bill made) for this bill_id – same bill, just update amounts and link to this statement
                $billPayId = $existingBillPay->id;
                $oldMatchedAmount = (float) ($existingLine->old_amount ?? 0);

                // Bill balance: was reduced by oldMatchedAmount; now we apply new matchedAmount
                $newBalanceFromUpdate = $bill->balance_amount + $oldMatchedAmount - $matchedAmount;
                $newBalanceFromUpdate = max(0, $newBalanceFromUpdate);

                $billPayUpdate = [
                    'user_id' => $userId,
                    'vendor_id' => $bill->vendor_id,
                    'vendor_name' => $bill->vendor_name ?? '',
                    'zone_id' => $bill->zone_id ?? null,
                    'zone_name' => $bill->zone_name ?? '',
                    'branch_id' => $bill->branch_id ?? null,
                    'branch_name' => $bill->branch_name ?? '',
                    'company_name' => $bill->company_name ?? '',
                    'company_id' => $bill->company_id ?? null,
                    'payment_date' => $paymentDate,
                    'payment_mode' => 'Bank Transfer',
                    'paid_through' => 'Bank Reconciliation',
                    'payment' => 'NEFT',
                    'reference' => $statement->reference_number ?? ('Bank Stmt #' . $statementId),
                    'remark' => 'Bank Reconciliation',
                    'save_status' => 'Bank Paid',
                    'amount_paid' => $matchedAmount,
                    'amount_used' => $matchedAmount,
                    'amount_refunded' => 0,
                    'amount_excess' => 0,
                    'note' => 'Matched from Bank Statement #'.$statement->reference_number . ' - ' . $statement->description,
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('bill_pay_tbl', 'bank_statement_id')) {
                    $billPayUpdate['bank_statement_id'] = $statementId;
                }
                if (Schema::hasColumn('bill_pay_tbl', 'bank_statement_status')) {
                    $billPayUpdate['bank_statement_status'] = $bankStatementStatus;
                }
                DB::table('bill_pay_tbl')->where('id', $billPayId)->update($billPayUpdate);

                DB::table('bill_pay_lines_tbl')
                    ->where('bill_pay_id', $billPayId)
                    ->where('bill_id', $billId)
                    ->update([
                        'bill_date' => $bill->bill_date ?? null,
                        'due_date' => $bill->due_date ?? null,
                        'bill_number' => $bill->bill_number ?? '',
                        'grand_total_amount' => $bill->grand_total_amount ?? 0,
                        'balance_amount' => $newBalanceFromUpdate,
                        'payment_date' => $paymentDate,
                        'amount' => $matchedAmount,
                    ]);

                // Create or update NEFT record for this bill_pay (Bank Reconciliation)
                $existingLineForNeft = DB::table('bill_pay_lines_tbl')
                    ->where('bill_pay_id', $billPayId)
                    ->where('bill_id', $billId)
                    ->first();
                $this->createOrUpdateNeftForBillPay(
                    $billPayId,
                    $existingLineForNeft->id ?? null,
                    $bill,
                    $statement,
                    $matchedAmount,
                    $userId,
                    $paymentDate,
                    true
                );

                $existingMatch = DB::table('bank_bill_matches')
                    ->where('bank_statement_id', $statementId)
                    ->where('status', 'active')
                    ->first();
                if ($existingMatch) {
                    DB::table('bank_bill_matches')->where('id', $existingMatch->id)->update([
                        'bill_id' => $billId,
                        'matched_amount' => $matchedAmount,
                        'match_type' => $matchType,
                        'matched_by' => $userId,
                        'matched_at' => now(),
                        'notes' => $request->notes,
                    ]);
                } else {
                    $matchInsert = [
                        'bank_statement_id' => $statementId,
                        'bill_id' => $billId,
                        'matched_amount' => $matchedAmount,
                        'match_type' => $matchType,
                        'matched_by' => $userId,
                        'matched_at' => now(),
                        'notes' => $request->notes,
                        'status' => 'active',
                        'created_at' => now(),
                    ];
                    if (Schema::hasColumn('bank_bill_matches', 'bill_pay_id')) {
                        $matchInsert['bill_pay_id'] = $billPayId;
                    }
                    DB::table('bank_bill_matches')->insert($matchInsert);
                }

                $billUpdate = [
                    'balance_amount' => $newBalanceFromUpdate,
                    'bill_status' => $newBalanceFromUpdate <= 0 ? 'paid' : 'partially_paid',
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('bill_tbl', 'bill_made_status') && $newBalanceFromUpdate <= 0) {
                    $billUpdate['bill_made_status'] = 1;
                } elseif (Schema::hasColumn('bill_tbl', 'bill_made_status')) {
                    $billUpdate['bill_made_status'] = 0;
                }
                if (Schema::hasColumn('bill_tbl', 'partially_payment')) {
                    $billUpdate['partially_payment'] = ($bill->partially_payment ?? 0) - $oldMatchedAmount + $matchedAmount;
                }
                DB::table('bill_tbl')->where('id', $billId)->update($billUpdate);
            } else {
                // INSERT new bill_pay (bill made) record
                $lastBillPay = DB::table('bill_pay_tbl')->orderBy('id', 'desc')->first();
                $nextNumber = 1;
                if ($lastBillPay && !empty($lastBillPay->payment_gen_order)) {
                    $lastNumber = (int) preg_replace('/^PAYMENT\-/i', '', $lastBillPay->payment_gen_order);
                    if ($lastNumber > 0) {
                        $nextNumber = $lastNumber + 1;
                    }
                }
                $paymentGenOrder = 'PAYMENT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

                $billPayData = [
                    'user_id' => $userId,
                    'vendor_id' => $bill->vendor_id,
                    'vendor_name' => $bill->vendor_name ?? '',
                    'zone_id' => $bill->zone_id ?? null,
                    'zone_name' => $bill->zone_name ?? '',
                    'branch_id' => $bill->branch_id ?? null,
                    'branch_name' => $bill->branch_name ?? '',
                    'company_name' => $bill->company_name ?? '',
                    'company_id' => $bill->company_id ?? null,
                    'payment_gen_order' => $paymentGenOrder,
                    'payment_made' => $paymentGenOrder,
                    'payment_date' => $paymentDate,
                    'payment_mode' => 'Bank Transfer',
                    'paid_through' => 'Bank Reconciliation',
                    'payment' => 'NEFT',
                    'reference' => $statement->reference_number ?? ('Bank Stmt #' . $statementId),
                    'remark' => 'Bank Reconciliation',
                    'save_status' => 'Bank Reconciliation Paid',
                    'amount_paid' => $matchedAmount,
                    'amount_used' => $matchedAmount,
                    'amount_refunded' => 0,
                    'amount_excess' => 0,
                    'note' => 'Matched from Bank Statement #'.$statement->reference_number . ' - ' . $statement->description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('bill_pay_tbl', 'bank_statement_id')) {
                    $billPayData['bank_statement_id'] = $statementId;
                }
                if (Schema::hasColumn('bill_pay_tbl', 'bank_statement_status')) {
                    $billPayData['bank_statement_status'] = $bankStatementStatus;
                }
                $billPayId = DB::table('bill_pay_tbl')->insertGetId($billPayData);

                $lineData = [
                    'bill_pay_id' => $billPayId,
                    'bill_id' => $billId,
                    'bill_date' => $bill->bill_date ?? null,
                    'due_date' => $bill->due_date ?? null,
                    'bill_number' => $bill->bill_number ?? '',
                    'grand_total_amount' => $bill->grand_total_amount ?? 0,
                    'balance_amount' => $newBalance,
                    'payment_date' => $paymentDate,
                    'amount' => $matchedAmount,
                    'created_at' => now(),
                ];
                $billPayLineId = DB::table('bill_pay_lines_tbl')->insertGetId($lineData);

                // Insert NEFT record (like VendorController saveneft) for Bank Reconciliation
                $this->createOrUpdateNeftForBillPay(
                    $billPayId,
                    $billPayLineId,
                    $bill,
                    $statement,
                    $matchedAmount,
                    $userId,
                    $paymentDate,
                    false
                );

                $matchInsert = [
                    'bank_statement_id' => $statementId,
                    'bill_id' => $billId,
                    'matched_amount' => $matchedAmount,
                    'match_type' => $matchType,
                    'matched_by' => $userId,
                    'matched_at' => now(),
                    'notes' => $request->notes,
                    'status' => 'active',
                    'created_at' => now(),
                ];
                if (Schema::hasColumn('bank_bill_matches', 'bill_pay_id')) {
                    $matchInsert['bill_pay_id'] = $billPayId;
                }
                DB::table('bank_bill_matches')->insert($matchInsert);

                // Update current bill balance and status (insert path only)
                $billUpdate = [
                    'balance_amount' => $newBalance,
                    'bill_status' => $newBalance <= 0 ? 'paid' : 'partially_paid',
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('bill_tbl', 'bill_made_status') && $newBalance <= 0) {
                    $billUpdate['bill_made_status'] = 1;
                }
                if (Schema::hasColumn('bill_tbl', 'partially_payment')) {
                    $billUpdate['partially_payment'] = ($bill->partially_payment ?? 0) + $matchedAmount;
                }
                DB::table('bill_tbl')->where('id', $billId)->update($billUpdate);
            }

            $finalMatch = DB::table('bank_bill_matches')
                ->where('bank_statement_id', $statementId)
                ->where('status', 'active')
                ->orderByDesc('id')
                ->first();
            if ($finalMatch) {
                $this->applyBankReconMatchNatureAndAttachments($request, $billId, (int) $statementId);
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Bill matched successfully and payment recorded in Bill Made'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error matching bill: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save nature-of-payment (chart account) ids/names on bill_tbl + bank_statements (attachments JSON on bank_statements).
     */
    private function applyBankReconMatchNatureAndAttachments(Request $request, int $billId, int $statementId): void
    {
        $ids = $request->input('nature_account_ids', []);
        if (! is_array($ids)) {
            $ids = array_filter(array_map('intval', explode(',', (string) $ids)));
        } else {
            $ids = array_values(array_unique(array_map('intval', array_filter($ids))));
        }

        $nameParts = [];
        if (! empty($ids) && Schema::hasTable('account_tbl')) {
            $accounts = DB::table('account_tbl')->whereIn('id', $ids)->get();
            foreach ($ids as $nid) {
                $a = $accounts->firstWhere('id', $nid);
                if ($a) {
                    $code = isset($a->code) ? (string) $a->code : '';
                    $name = isset($a->name) ? (string) $a->name : '';
                    $label = trim(($code !== '' ? $code.' — ' : '').$name);
                    $nameParts[] = $label !== '' ? $label : ('#'.$nid);
                }
            }
        }

        if (Schema::hasTable('bill_tbl')) {
            $billUp = ['updated_at' => now()];
            if (Schema::hasColumn('bill_tbl', 'br_nature_account_ids')) {
                $billUp['br_nature_account_ids'] = implode(',', $ids);
            }
            if (Schema::hasColumn('bill_tbl', 'br_nature_account_names')) {
                $billUp['br_nature_account_names'] = implode(', ', $nameParts);
            }
            if (count($billUp) > 1) {
                DB::table('bill_tbl')->where('id', $billId)->update($billUp);
            }
        }

        $attachmentRows = [];
        $tagInputs = $request->input('attachment_tags', []);
        if (! is_array($tagInputs)) {
            $tagInputs = [];
        }
        $typeIdInputs = $request->input('attachment_type_ids', []);
        if (! is_array($typeIdInputs)) {
            $typeIdInputs = [];
        }
        if ($request->hasFile('attachments')) {
            $destDir = public_path('bank_recon_match_files/'.$statementId);
            if (! File::isDirectory($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }
            $idx = 0;
            foreach ($request->file('attachments') as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }
                $origName = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension() ?: 'bin';
                $base = Str::slug(pathinfo($origName, PATHINFO_FILENAME)) ?: 'file';
                $safe = $base.'-'.Str::random(6).'.'.$ext;
                $file->move($destDir, $safe);
                $relative = 'bank_recon_match_files/'.$statementId.'/'.$safe;
                $tagLabel = isset($tagInputs[$idx]) ? trim((string) $tagInputs[$idx]) : '';
                if ($tagLabel === '') {
                    $tagLabel = 'Unspecified';
                }
                $tid = isset($typeIdInputs[$idx]) ? (int) $typeIdInputs[$idx] : 0;
                $row = [
                    'name' => $origName,
                    'url' => $this->bankReconMatchAttachmentPublicUrl($relative),
                    'path' => $relative,
                    'tag'  => $tagLabel,
                ];
                if ($tid > 0) {
                    $row['tag_type_id'] = $tid;
                }
                $attachmentRows[] = $row;
                $idx++;
            }
        }

        if (Schema::hasTable('bank_statements')) {
            $hasStmtReconCols = Schema::hasColumn('bank_statements', 'br_nature_account_ids')
                || Schema::hasColumn('bank_statements', 'br_nature_account_names')
                || Schema::hasColumn('bank_statements', 'attachments_json');
            if ($hasStmtReconCols) {
                $stmtUp = [];
                if (Schema::hasColumn('bank_statements', 'br_nature_account_ids')) {
                    $stmtUp['br_nature_account_ids'] = ! empty($ids) ? implode(',', $ids) : null;
                }
                if (Schema::hasColumn('bank_statements', 'br_nature_account_names')) {
                    $stmtUp['br_nature_account_names'] = ! empty($nameParts) ? implode(', ', $nameParts) : null;
                }
                if (Schema::hasColumn('bank_statements', 'attachments_json')) {
                    $stmtUp['attachments_json'] = ! empty($attachmentRows) ? json_encode($attachmentRows) : null;
                }
                if (! empty($stmtUp) && Schema::hasColumn('bank_statements', 'updated_at')) {
                    $stmtUp['updated_at'] = now();
                }
                if (! empty($stmtUp)) {
                    DB::table('bank_statements')->where('id', $statementId)->update($stmtUp);
                }
            }
        }
    }

    /**
     * Absolute URL for files under public/bank_recon_match_files/….
     * When APP_URL points at the project folder (e.g. https://…/hms) instead of …/hms/public, {@see asset()}
     * omits the /public/ segment and links 404; insert /public/ before bank_recon_match_files for that layout.
     */
    private function bankReconMatchAttachmentPublicUrl(string $relative): string
    {
        $relative = ltrim(str_replace('\\', '/', $relative), '/');
        $url = asset($relative);
        $parts = parse_url($url);
        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host']) || empty($parts['path'])) {
            return $url;
        }
        $path = $parts['path'];
        if (str_contains($path, '/public/bank_recon_match_files')) {
            return $url;
        }
        $fixedPath = preg_replace('#^/([^/]+)/(bank_recon_match_files.*)$#', '/$1/public/$2', $path, 1);
        if ($fixedPath === null || $fixedPath === $path) {
            return $url;
        }
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return $parts['scheme'].'://'.$parts['host'].$port.$fixedPath
            .(isset($parts['query']) ? '?'.$parts['query'] : '')
            .(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
    }

    /**
     * Delete a single bank-reconciliation match attachment from disk (public/ or legacy storage/app/public).
     *
     * @param  array<string, mixed>  $item
     */
    private function deleteBankReconStoredAttachment(array $item): void
    {
        $path = isset($item['path']) ? (string) $item['path'] : '';
        if ($path === '') {
            return;
        }
        if (Str::startsWith($path, 'bank_recon_match_files/')) {
            $full = public_path($path);
            if (is_file($full)) {
                @unlink($full);
            }

            return;
        }
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Chart accounts for bank reconciliation match modal (Select2), same source as vendor bill lines.
     */
    public function listChartAccounts(Request $request)
    {
        if (! Schema::hasTable('account_tbl')) {
            return response()->json(['results' => []]);
        }

        $q = trim((string) $request->get('q', ''));
        $query = DB::table('account_tbl');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $hasName = Schema::hasColumn('account_tbl', 'name');
                $hasCode = Schema::hasColumn('account_tbl', 'code');
                if ($hasName) {
                    $w->where('name', 'like', '%'.$q.'%');
                }
                if ($hasCode) {
                    if ($hasName) {
                        $w->orWhere('code', 'like', '%'.$q.'%');
                    } else {
                        $w->where('code', 'like', '%'.$q.'%');
                    }
                }
            });
        }

        $orderCol = Schema::hasColumn('account_tbl', 'name') ? 'name' : 'id';
        $rows = $query->orderBy($orderCol)->orderBy('id')->limit(5000)->get();

        $results = $rows->map(function ($r) {
            $code = isset($r->code) ? (string) $r->code : '';
            $name = isset($r->name) ? (string) $r->name : '';
            $text = trim(($code !== '' ? $code.' — ' : '').$name);

            return [
                'id' => (int) $r->id,
                'text' => $text !== '' ? $text : ('Account #'.$r->id),
            ];
        });

        return response()->json(['results' => $results->values()->all()]);
    }
    
    /**
     * Unmatch bank statement.
     * Restores bill balance and soft-deletes the bill_pay (bill made) and NEFT records (delete_status = 1).
     */
    public function unmatch($id)
    {
        DB::beginTransaction();
        
        try {
            $statement = DB::table('bank_statements')->where('id', $id)->first();
            
            if (!$statement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Statement not found'
                ], 404);
            }
            
            $match = DB::table('bank_bill_matches')
                ->where('bank_statement_id', $id)
                ->where('status', 'active')
                ->first();
            if ($match) {
                $attachmentsJson = null;
                if (Schema::hasColumn('bank_statements', 'attachments_json') && $statement && ! empty($statement->attachments_json)) {
                    $attachmentsJson = $statement->attachments_json;
                }
                if ($attachmentsJson) {
                    $decoded = json_decode($attachmentsJson, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $item) {
                            $this->deleteBankReconStoredAttachment($item);
                        }
                    }
                }

                // Restore bill balance
                $bill = DB::table('bill_tbl')->where('id', $match->bill_id)->first();
                if ($bill) {
                    $newBalance = $bill->balance_amount + $match->matched_amount;
                    $billUpdate = [
                        'balance_amount' => $newBalance,
                        'bill_status' => $newBalance >= ($bill->grand_total_amount ?? 0) ? 'unpaid' : 'partially_paid',
                        'updated_at' => now()
                    ];
                    if (Schema::hasColumn('bill_tbl', 'bill_made_status')) {
                        $billUpdate['bill_made_status'] = 0;
                    }
                    if (Schema::hasColumn('bill_tbl', 'partially_payment')) {
                        $billUpdate['partially_payment'] = max(0, ($bill->partially_payment ?? 0) - $match->matched_amount);
                    }
                    if (Schema::hasColumn('bill_tbl', 'br_nature_account_ids')) {
                        $billUpdate['br_nature_account_ids'] = null;
                    }
                    if (Schema::hasColumn('bill_tbl', 'br_nature_account_names')) {
                        $billUpdate['br_nature_account_names'] = null;
                    }
                    DB::table('bill_tbl')->where('id', $match->bill_id)->update($billUpdate);
                }
                
                // Soft delete: set delete_status = 1 for NEFT and bill_pay (do not permanently delete)
                $billPayId = isset($match->bill_pay_id) ? (int) $match->bill_pay_id : null;
                if ($billPayId) {
                    $now = now();
                    $neftIds = DB::table('tbl_neft_payment')->where('bill_pay_id', $billPayId)->pluck('id');
                    if ($neftIds->isNotEmpty()) {
                        if (Schema::hasColumn('tbl_neft_lines', 'delete_status')) {
                            $lineUpdate = ['delete_status' => 1];
                            if (Schema::hasColumn('tbl_neft_lines', 'updated_at')) {
                                $lineUpdate['updated_at'] = $now;
                            }
                            DB::table('tbl_neft_lines')->whereIn('neft_id', $neftIds)->update($lineUpdate);
                        } else {
                            DB::table('tbl_neft_lines')->whereIn('neft_id', $neftIds)->delete();
                        }
                        if (Schema::hasColumn('tbl_neft_payment', 'delete_status')) {
                            $neftUpdate = ['delete_status' => 1];
                            if (Schema::hasColumn('tbl_neft_payment', 'updated_at')) {
                                $neftUpdate['updated_at'] = $now;
                            }
                            DB::table('tbl_neft_payment')->where('bill_pay_id', $billPayId)->update($neftUpdate);
                        } else {
                            DB::table('tbl_neft_payment')->where('bill_pay_id', $billPayId)->delete();
                        }
                    }
                    if (Schema::hasColumn('bill_pay_lines_tbl', 'delete_status')) {
                        $payLineUpdate = ['delete_status' => 1];
                        if (Schema::hasColumn('bill_pay_lines_tbl', 'updated_at')) {
                            $payLineUpdate['updated_at'] = $now;
                        }
                        DB::table('bill_pay_lines_tbl')->where('bill_pay_id', $billPayId)->update($payLineUpdate);
                    } else {
                        DB::table('bill_pay_lines_tbl')->where('bill_pay_id', $billPayId)->delete();
                    }
                    if (Schema::hasColumn('bill_pay_tbl', 'delete_status')) {
                        $payUpdate = ['delete_status' => 1];
                        if (Schema::hasColumn('bill_pay_tbl', 'updated_at')) {
                            $payUpdate['updated_at'] = $now;
                        }
                        DB::table('bill_pay_tbl')->where('id', $billPayId)->update($payUpdate);
                    } else {
                        DB::table('bill_pay_tbl')->where('id', $billPayId)->delete();
                    }
                }
                
                DB::table('bank_bill_matches')
                    ->where('id', $match->id)
                    ->update(['status' => 'cancelled']);
            }

            $stmtReset = [
                'match_status' => 'unmatched',
                'matched_bill_id' => null,
                'matched_amount' => 0,
                'matched_date' => null,
                'matched_by' => null,
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('bank_statements', 'br_nature_account_ids')) {
                $stmtReset['br_nature_account_ids'] = null;
            }
            if (Schema::hasColumn('bank_statements', 'br_nature_account_names')) {
                $stmtReset['br_nature_account_names'] = null;
            }
            if (Schema::hasColumn('bank_statements', 'attachments_json')) {
                $stmtReset['attachments_json'] = null;
            }
            if (Schema::hasColumn('bank_statements', 'category') && (($statement->income_match_status ?? '') !== 'income_matched')) {
                $stmtReset['category'] = 'uncategory';
            }
            DB::table('bank_statements')
                ->where('id', $id)
                ->update($stmtReset);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Bill unmatched successfully; Bill Made record removed'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error unmatching: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete bank statement
     */
    /**
     * Return a single bank statement row with matched user info,
     * used by the income reconciliation page when a bank ref number is clicked.
     */
    public function getBankStatementById($id)
    {
        $select = [
            'bs.*',
            'matched_user.user_fullname as matched_by_name',
            'matched_user.username as matched_by_username',
            'income_user.user_fullname as income_matched_by_name',
            'income_user.username as income_matched_by_username',
            'bill.bill_number as resolved_bill_number',
            'bill.vendor_name as resolved_vendor_name',
            'bill.grand_total_amount as bill_amount',
        ];
        if (Schema::hasColumn('bill_tbl', 'bill_gen_number')) {
            $select[] = 'bill.bill_gen_number as resolved_bill_gen_number';
        }
        if (Schema::hasTable('bank_bill_matches')) {
            $select[] = 'bbm_matcher.user_fullname as bbm_matched_by_name';
            $select[] = 'bbm_matcher.username as bbm_matched_by_username';
            $select[] = 'bbm.matched_at as bank_match_matched_at';
        }

        $query = DB::table('bank_statements as bs')
            ->leftJoin('users as matched_user', 'bs.matched_by', '=', 'matched_user.id')
            ->leftJoin('users as income_user', 'bs.income_matched_by', '=', 'income_user.id');
        $this->applyBankStatementBillJoins($query);
        $stmt = $query->select($select)->where('bs.id', $id)->first();

        if (!$stmt) {
            return response()->json(['success' => false, 'message' => 'Statement not found'], 404);
        }

        $this->hydrateStatementBillDisplayFields($stmt);

        return response()->json(['success' => true, 'data' => $stmt]);
    }

    public function destroy($id)
    {
        try {
            // Check if matched
            $statement = DB::table('bank_statements')->where('id', $id)->first();
            
            if (!$statement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Statement not found'
                ], 404);
            }
            
            if ($statement->match_status !== 'unmatched') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete matched statement. Please unmatch first.'
                ], 400);
            }
            
            DB::table('bank_statements')->where('id', $id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Statement deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete batch of statements
     */
    public function deleteBatch(Request $request)
    {
        $batchId = $request->batch_id;
        
        try {
            // Check if any statements in batch are matched
            $matchedCount = DB::table('bank_statements')
                ->where('upload_batch_id', $batchId)
                ->where('match_status', '!=', 'unmatched')
                ->count();
            
            if ($matchedCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete batch. {$matchedCount} statements are matched. Please unmatch them first."
                ], 400);
            }
            
            $deletedCount = DB::table('bank_statements')
                ->where('upload_batch_id', $batchId)
                ->delete();

            if (Schema::hasTable('bank_statement_upload_batches')) {
                DB::table('bank_statement_upload_batches')->where('upload_batch_id', $batchId)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "Deleted {$deletedCount} statements successfully"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting batch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * JSON list of bank accounts (for dropdowns).
     */
    public function listBankAccounts()
    {
        if (! Schema::hasTable('bank_reconciliation_accounts')) {
            return response()->json(['data' => [], 'companies' => []]);
        }

        $companies = [];
        if (Schema::hasTable('company_tbl')) {
            $companies = DB::table('company_tbl')
                ->orderBy('company_name')
                ->get(['id', 'company_name'])
                ->map(static function ($r) {
                    return [
                        'id' => (int) $r->id,
                        'company_name' => (string) ($r->company_name ?? ''),
                    ];
                })
                ->values()
                ->all();
        }

        $query = DB::table('bank_reconciliation_accounts as bra');
        $select = [
            'bra.id',
            'bra.account_number',
            'bra.bank_name',
            'bra.branch_name',
            'bra.ifsc_code',
            'bra.account_holder_name',
            'bra.notes',
            'bra.updated_at',
        ];
        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id')) {
            $select[] = 'bra.company_id';
            if (Schema::hasTable('company_tbl')) {
                $query->leftJoin('company_tbl as c', 'bra.company_id', '=', 'c.id');
                $select[] = 'c.company_name';
            }
        }

        $rows = $query
            ->select($select)
            ->orderBy('bra.account_number')
            ->orderBy('bra.bank_name')
            ->get();

        return response()->json(['data' => $rows, 'companies' => $companies]);
    }

    /**
     * Distinct users who appear as bill/statement matchers (for filter dropdown).
     */
    public function listMatchedByUsersForFilter()
    {
        if (! Schema::hasTable('bank_statements')) {
            return response()->json(['data' => []]);
        }

        $ids = DB::table('bank_statements')
            ->whereNotNull('matched_by')
            ->distinct()
            ->pluck('matched_by');

        if (Schema::hasTable('bank_bill_matches')) {
            $bbmIds = DB::table('bank_bill_matches')
                ->whereNotNull('matched_by')
                ->distinct()
                ->pluck('matched_by');
            $ids = $ids->merge($bbmIds);
        }

        if (Schema::hasColumn('bank_statements', 'income_matched_by')) {
            $incIds = DB::table('bank_statements')
                ->whereNotNull('income_matched_by')
                ->distinct()
                ->pluck('income_matched_by');
            $ids = $ids->merge($incIds);
        }

        $uniqueIds = $ids->unique()->filter()->values();
        if ($uniqueIds->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $users = DB::table('users')
            ->whereIn('id', $uniqueIds)
            ->orderBy('user_fullname')
            ->orderBy('username')
            ->get(['id', 'user_fullname', 'username']);

        $data = $users->map(function ($u) {
            $name = trim((string) ($u->user_fullname ?? ''));
            if ($name === '') {
                $name = (string) (($u->username ?? '') !== '' ? $u->username : 'User #' . $u->id);
            }

            return [
                'id'         => (int) $u->id,
                'name'       => $name,
                'username'   => $u->username,
            ];
        })->values();

        return response()->json(['data' => $data]);
    }

    /**
     * Dropdown data for the bank reconciliation quick filter (zones, branches, categories, vendors).
     */
    public function statementQuickFilterOptions(Request $request)
    {
        // Category options are fixed in the UI (Categorized / Uncategorized); keep empty here for compatibility.
        $categories = [];

        $vendorNames = [];
        if (Schema::hasTable('bill_tbl')) {
            $vendorNames = DB::table('bill_tbl');
            if (Schema::hasColumn('bill_tbl', 'delete_status')) {
                $vendorNames->where('delete_status', 0);
            }
            $vendorNames = $vendorNames
                ->whereNotNull('vendor_name')
                ->where('vendor_name', '!=', '')
                ->distinct()
                ->orderBy('vendor_name')
                ->limit(2500)
                ->pluck('vendor_name')
                ->values()
                ->all();
        }

        $zones = [];
        if (Schema::hasTable('tblzones')) {
            $zones = DB::table('tblzones')
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(static function ($z) {
                    return [
                        'id'   => (int) $z->id,
                        'name' => (string) $z->name,
                    ];
                })
                ->values()
                ->all();
        }

        $branches = [];
        $zoneIdsForBranches = $this->requestIntIdArray($request, 'zone_ids');
        if (Schema::hasTable('tbl_locations')) {
            $bq = DB::table('tbl_locations')
                ->select('id', 'name', 'zone_id')
                ->orderBy('name')
                ->limit(5000);
            if (count($zoneIdsForBranches) > 0) {
                $bq->whereIn('zone_id', $zoneIdsForBranches);
            }
            $branches = $bq->get()
                ->map(static function ($b) {
                    return [
                        'id'      => (int) $b->id,
                        'name'    => (string) $b->name,
                        'zone_id' => (int) $b->zone_id,
                    ];
                })
                ->values()
                ->all();
        }

        return response()->json([
            'categories'   => $categories,
            'vendor_names' => $vendorNames,
            'zones'        => $zones,
            'branches'     => $branches,
        ]);
    }

    /**
     * Store a bank account master row.
     */
    public function storeBankAccount(Request $request)
    {
        if (! Schema::hasTable('bank_reconciliation_accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'Run database migrations to enable bank accounts.',
            ], 503);
        }

        $accountNumberRules = ['required', 'string', 'max:64'];
        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id')) {
            $accountNumberRules[] = Rule::unique('bank_reconciliation_accounts')->where(function ($q) use ($request) {
                return $q->where('company_id', (int) $request->company_id);
            });
        } else {
            $accountNumberRules[] = Rule::unique('bank_reconciliation_accounts', 'account_number');
        }

        $rules = [
            'account_number'      => $accountNumberRules,
            'bank_name'           => 'nullable|string|max:191',
            'branch_name'         => 'nullable|string|max:191',
            'ifsc_code'           => 'nullable|string|max:32',
            'account_holder_name' => 'nullable|string|max:191',
            'notes'               => 'nullable|string|max:2000',
        ];
        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id') && Schema::hasTable('company_tbl')) {
            $rules['company_id'] = 'required|integer|exists:company_tbl,id';
        }
        $request->validate($rules);

        $insert = [
            'account_number'      => trim($request->account_number),
            'bank_name'           => $request->bank_name ? trim($request->bank_name) : null,
            'branch_name'         => $request->branch_name ? trim($request->branch_name) : null,
            'ifsc_code'           => $request->ifsc_code ? trim($request->ifsc_code) : null,
            'account_holder_name' => $request->account_holder_name ? trim($request->account_holder_name) : null,
            'notes'               => $request->notes ? trim($request->notes) : null,
            'created_at'          => now(),
            'updated_at'          => now(),
        ];
        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id')) {
            $insert['company_id'] = (int) $request->company_id;
        }

        $id = DB::table('bank_reconciliation_accounts')->insertGetId($insert);

        return response()->json([
            'success' => true,
            'message' => 'Account saved.',
            'account' => DB::table('bank_reconciliation_accounts')->where('id', $id)->first(),
        ]);
    }

    /**
     * Update an existing bank account master row.
     */
    public function updateBankAccount(Request $request, $id)
    {
        if (! Schema::hasTable('bank_reconciliation_accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'Run database migrations to enable bank accounts.',
            ], 503);
        }

        $id = (int) $id;
        $exists = DB::table('bank_reconciliation_accounts')->where('id', $id)->exists();
        if (! $exists) {
            return response()->json(['success' => false, 'message' => 'Account not found.'], 404);
        }

        $accountNumberRules = ['required', 'string', 'max:64'];
        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id')) {
            $accountNumberRules[] = Rule::unique('bank_reconciliation_accounts')
                ->ignore($id)
                ->where(function ($q) use ($request) {
                    return $q->where('company_id', (int) $request->company_id);
                });
        } else {
            $accountNumberRules[] = Rule::unique('bank_reconciliation_accounts', 'account_number')->ignore($id);
        }

        $rules = [
            'account_number'      => $accountNumberRules,
            'bank_name'           => 'nullable|string|max:191',
            'branch_name'         => 'nullable|string|max:191',
            'ifsc_code'           => 'nullable|string|max:32',
            'account_holder_name' => 'nullable|string|max:191',
            'notes'               => 'nullable|string|max:2000',
        ];
        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id') && Schema::hasTable('company_tbl')) {
            $rules['company_id'] = 'required|integer|exists:company_tbl,id';
        }
        $request->validate($rules);

        $update = [
            'account_number'      => trim($request->account_number),
            'bank_name'           => $request->bank_name ? trim($request->bank_name) : null,
            'branch_name'         => $request->branch_name ? trim($request->branch_name) : null,
            'ifsc_code'           => $request->ifsc_code ? trim($request->ifsc_code) : null,
            'account_holder_name' => $request->account_holder_name ? trim($request->account_holder_name) : null,
            'notes'               => $request->notes ? trim($request->notes) : null,
            'updated_at'          => now(),
        ];
        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id')) {
            $update['company_id'] = (int) $request->company_id;
        }

        DB::table('bank_reconciliation_accounts')->where('id', $id)->update($update);

        return response()->json([
            'success' => true,
            'message' => 'Account updated.',
            'account' => DB::table('bank_reconciliation_accounts')->where('id', $id)->first(),
        ]);
    }

    /**
     * Paginated upload batches (master) with filters.
     */
    public function listUploadBatches(Request $request)
    {
        if (! Schema::hasTable('bank_statement_upload_batches')) {
            return response()->json([
                'data'         => [],
                'current_page' => 1,
                'last_page'    => 1,
                'per_page'     => (int) $request->get('per_page', 25),
                'total'        => 0,
                'from'         => null,
                'to'           => null,
            ]);
        }

        $q = DB::table('bank_statement_upload_batches as b')
            ->join('bank_reconciliation_accounts as a', 'b.bank_account_id', '=', 'a.id')
            ->leftJoin('users as u', 'b.user_id', '=', 'u.id')
            ->select([
                'b.id',
                'b.upload_batch_id',
                'b.original_file_name',
                'b.stored_file_name',
                'b.rows_imported',
                'b.duplicates',
                'b.skipped',
                'b.created_at',
                'a.account_number',
                'a.bank_name',
                'u.user_fullname as uploaded_by_name',
                'u.username as uploaded_by_username',
            ])
            ->orderBy('b.id', 'desc');

        if ($request->filled('account_number')) {
            $term = '%' . $request->account_number . '%';
            $q->where('a.account_number', 'LIKE', $term);
        }

        if ($request->filled('file_name')) {
            $q->where('b.original_file_name', 'LIKE', '%' . $request->file_name . '%');
        }

        if ($request->filled('uploaded_by')) {
            $search = '%' . $request->uploaded_by . '%';
            $q->where(function ($w) use ($search) {
                $w->where('u.user_fullname', 'LIKE', $search)
                    ->orWhere('u.username', 'LIKE', $search);
            });
        }

        if ($request->filled('date_from')) {
            $q->whereDate('b.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $q->whereDate('b.created_at', '<=', $request->date_to);
        }

        $perPage = max(5, min(100, (int) $request->get('per_page', 25)));

        return response()->json($q->paginate($perPage));
    }

    /**
     * Download the original uploaded file for a batch (by upload_batch_id).
     */
    public function downloadBatchFile(string $uploadBatchId)
    {
        if (! Schema::hasTable('bank_statement_upload_batches')) {
            abort(404);
        }
        $batch = DB::table('bank_statement_upload_batches')->where('upload_batch_id', $uploadBatchId)->first();
        if (! $batch) {
            abort(404);
        }
        $path = public_path('bank_statements/' . $batch->stored_file_name);
        if (! is_file($path)) {
            abort(404, 'File no longer on disk.');
        }

        return response()->download($path, $batch->original_file_name);
    }

    /**
     * Paginated rows from bank_statements for batch preview (full upload, AJAX pages).
     */
    public function previewBatch(Request $request, string $uploadBatchId)
    {
        if (! Schema::hasTable('bank_statement_upload_batches')) {
            return response()->json([
                'batch'      => null,
                'rows'       => [],
                'total'      => 0,
                'per_page'   => 25,
                'current_page' => 1,
                'last_page'  => 1,
                'from'       => null,
                'to'         => null,
            ]);
        }
        $batch = DB::table('bank_statement_upload_batches as b')
            ->join('bank_reconciliation_accounts as a', 'b.bank_account_id', '=', 'a.id')
            ->leftJoin('users as u', 'b.user_id', '=', 'u.id')
            ->where('b.upload_batch_id', $uploadBatchId)
            ->select([
                'b.id',
                'b.upload_batch_id',
                'b.original_file_name',
                'b.rows_imported',
                'b.duplicates',
                'b.skipped',
                'b.created_at',
                'a.account_number',
                'a.bank_name',
                'u.user_fullname as uploaded_by_name',
                'u.username as uploaded_by_username',
            ])
            ->first();

        if (! $batch) {
            return response()->json(['batch' => null, 'rows' => []], 404);
        }

        $perPage = max(5, min(100, (int) $request->get('per_page', 25)));
        $page    = max(1, (int) $request->get('page', 1));

        $columns = [
            'id',
            'transaction_date',
            'value_date',
            'transaction_id',
            'transaction_posted_date',
            'reference_number',
            'cheque_number',
            'description',
            'withdrawal',
            'deposit',
            'balance',
            'category',
            'match_status',
        ];

        $paginator = DB::table('bank_statements')
            ->where('upload_batch_id', $uploadBatchId)
            ->orderBy('id')
            ->paginate($perPage, $columns, 'page', $page);

        return response()->json([
            'batch'         => $batch,
            'rows'          => $paginator->items(),
            'total'         => $paginator->total(),
            'per_page'      => $paginator->perPage(),
            'current_page'  => $paginator->currentPage(),
            'last_page'     => $paginator->lastPage(),
            'from'          => $paginator->firstItem(),
            'to'            => $paginator->lastItem(),
        ]);
    }

    /**
     * Helper: Parse date from Excel
     */
    private function parseDate($value)
    {
        if (empty($value)) return null;
        
        try {
            // If it's a number (Excel date serial)
            if (is_numeric($value)) {
                $unixDate = ($value - 25569) * 86400;
                return Carbon::createFromTimestamp($unixDate)->format('Y-m-d');
            }
            
            // Try to parse as date string
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Helper: Parse amount
     */
    private function parseAmount($value)
    {
        if (empty($value)) return 0;
        
        // Remove currency symbols and commas
        $cleaned = preg_replace('/[^0-9.-]/', '', $value);
        return (float) $cleaned;
    }
    
    /**
     * Helper: Extract reference number
     */
    private function extractReference($description, $chequeNumber)
    {
        if (!empty($chequeNumber)) {
            return $chequeNumber;
        }
        
        // Try to extract from description (look for patterns like REF:123456)
        if (preg_match('/REF[:\s]*([0-9A-Z]+)/i', $description, $matches)) {
            return $matches[1];
        }
        
        // Extract any number sequence
        if (preg_match('/([0-9]{6,})/', $description, $matches)) {
            return $matches[1];
        }
        
        return substr(md5($description . time()), 0, 10);
    }

    /**
     * Create or update NEFT record for a Bill Made (Bank Reconciliation), like VendorController::saveneft.
     * Inserts into tbl_neft_payment and tbl_neft_lines.
     *
     * @param int $billPayId
     * @param int|null $billPayLineId id from bill_pay_lines_tbl
     * @param object $bill row from bill_tbl
     * @param object $statement row from bank_statements
     * @param float $matchedAmount
     * @param int $userId
     * @param string $paymentDate Y-m-d
     * @param bool $isUpdate true = update existing NEFT for this bill_pay_id if found
     */
    private function createOrUpdateNeftForBillPay(
        $billPayId,
        $billPayLineId,
        $bill,
        $statement,
        $matchedAmount,
        $userId,
        $paymentDate,
        $isUpdate
    ) {
        $admin = Auth::user();
        $now = now();
        $neftTable = 'tbl_neft_payment';
        $neftLinesTable = 'tbl_neft_lines';

        if (!Schema::hasTable($neftTable) || !Schema::hasTable($neftLinesTable)) {
            return;
        }

        // Fetch vendor PAN and bank details (account number, IFSC) for NEFT
        $panNumber = null;
        $accountNumber = null;
        $ifscCode = null;
        $vendorId = $bill->vendor_id ?? null;
        if ($vendorId) {
            if (Schema::hasTable('vendor_tbl')) {
                $vendor = DB::table('vendor_tbl')->where('id', $vendorId)->first();
                if ($vendor && Schema::hasColumn('vendor_tbl', 'pan_number')) {
                    $panNumber = $vendor->pan_number ?? null;
                }
            }
            if (Schema::hasTable('bank_details_tbl')) {
                $bankDetail = DB::table('bank_details_tbl')->where('vendor_id', $vendorId)->first();
                if ($bankDetail) {
                    $accountNumber = $bankDetail->accont_number ?? $bankDetail->account_number ?? null;
                    $ifscCode = $bankDetail->ifsc_code ?? null;
                }
            }
        }
        $created_by=$admin->user_fullname.'-'.$admin->username;
        // Only match an active (not soft-deleted) NEFT record
        $existingNeft = DB::table($neftTable)
            ->where('bill_pay_id', $billPayId)
            ->where('delete_status', 0)
            ->first();

        $serialNumber = null;
        if (!$isUpdate || !$existingNeft) {
            // Generate serial number only for new NEFT (like VendorController getbillmade)
            $lastRecord = DB::table($neftTable)->orderBy('id', 'desc')->first();
            $nextNumber = 1;
            if ($lastRecord && !empty($lastRecord->serial_number)) {
                $lastNumber = (int) preg_replace('/NEFT\-/i', '', $lastRecord->serial_number);
                if ($lastNumber > 0) {
                    $nextNumber = $lastNumber + 1;
                }
            }
            $serialNumber = 'NEFT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $serialNumber = $existingNeft->serial_number;
        }

        $neftData = [
            'serial_number' => $serialNumber,
            'user_id' => $userId,
            'bill_pay_id' => $billPayId,
            'created_by' => $created_by,
            'vendor_id' => $bill->vendor_id ?? null,
            'vendor' => $bill->vendor_name ?? '',
            'branch_id' => $bill->branch_id ?? null,
            'branch_name' => $bill->branch_name ?? '',
            'zone_id' => $bill->zone_id ?? null,
            'zone_name' => $bill->zone_name ?? '',
            'company_id' => $bill->company_id ?? null,
            'company_name' => $bill->company_name ?? '',
            'nature_payment' => 'Bank Transfer',
            'payment_status' => 'Success',
            'payment_method' => 'NEFT',
            'utr_number' => $statement->description ?? ('BankRecon-' . $statement->reference_number),
            'pan_number' => $panNumber,
            'account_number' => $accountNumber,
            'ifsc_code' => $ifscCode,
            'checker_status' => 0,
            'approval_status' => 0,
            'created_at' => $now,
        ];
        if (Schema::hasColumn($neftTable, 'updated_at')) {
            $neftData['updated_at'] = $now;
        }

        if ($isUpdate && $existingNeft) {
            DB::table($neftTable)->where('id', $existingNeft->id)->update($neftData);
            $neftId = $existingNeft->id;

            $lineData = [
                'neft_id' => $neftId,
                'bill_id' => $bill->id,
                'bill_pay_id' => $billPayId,
                'bill_pay_lines_id' => $billPayLineId,
                'invoice_amount' => $bill->grand_total_amount ?? 0,
                'already_paid' => $matchedAmount,
                'only_payable' => $matchedAmount,
            ];
            if (Schema::hasColumn($neftLinesTable, 'updated_at')) {
                $lineData['updated_at'] = $now;
            }
            $existingLine = DB::table($neftLinesTable)
                ->where('neft_id', $neftId)
                ->where('bill_id', $bill->id)
                ->first();
            if ($existingLine) {
                DB::table($neftLinesTable)->where('id', $existingLine->id)->update($lineData);
            } else {
                $lineData['created_at'] = $now;
                DB::table($neftLinesTable)->insert($lineData);
            }
        } else {
            $neftId = DB::table($neftTable)->insertGetId($neftData);

            $lineData = [
                'neft_id' => $neftId,
                'bill_id' => $bill->id,
                'bill_pay_id' => $billPayId,
                'bill_pay_lines_id' => $billPayLineId,
                'invoice_amount' => $bill->grand_total_amount ?? 0,
                'already_paid' => $matchedAmount,
                'only_payable' => $matchedAmount,
                'created_at' => $now,
            ];
            if (Schema::hasColumn($neftLinesTable, 'updated_at')) {
                $lineData['updated_at'] = $now;
            }
            DB::table($neftLinesTable)->insert($lineData);
        }
    }

    // ============================================================
    // INCOME TAG - link a bank statement to income_reconciliation_table
    // ============================================================

    /**
     * Fetch zones for Income Tag dropdown
     */
    public function incomeTagZones()
    {
        $zones = DB::table('tblzones')->select('id', 'name')->orderBy('name')->get();
        return response()->json($zones);
    }

    /**
     * Fetch branches for a given zone_id (mirrors VendorController::getbranchfetch)
     */
    public function incomeTagBranches(Request $request)
    {
        $zoneId = $request->input('zone_id');
        $branches = DB::table('tbl_locations')
            ->where('zone_id', $zoneId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        return response()->json($branches);
    }

    /**
     * Parse a bank statement description and resolve to branch / zone / mode.
     *
     * Patterns handled:
     *   CASH  → "BY CASH -DHARMAPURI 6057 ..."   → mode=cash,  branch resolved by name
     *   MESPOS→ "FT-MESPOS SET 10XX123558 310126" → mode=card,  branch resolved by MOC code suffix
     *   NEFT  → "NEFT-..."                        → mode=neft
     *   UPI   → "UPI-..." / "UPI/"                → mode=upi
     */
    public function incomeTagResolveDescription(Request $request)
    {
        $description = strtoupper(trim($request->input('description', '')));
        $txnDate     = $request->input('txn_date', ''); // Y-m-d format

        $mode        = null;
        $branchId    = null;
        $branchName  = null;
        $zoneId      = null;
        $zoneName    = null;
        $date        = null; // collection date = txn date - 1 day

        // ---- Derive collection date (one day before transaction date) ----
        if ($txnDate) {
            try {
                $date = \Carbon\Carbon::parse($txnDate)->subDay()->format('d/m/Y');
            } catch (\Exception $e) {
                $date = null;
            }
        }

        // ---- MOC DOC branch code map: last-6-digits-of-code => location_id in tbl_locations ----
        // Codes from image (100000000XXXXXX → location id)
        $mocCodeMap = [
            '118961' => 23,  // ELECTRONIC CITY (Bengaluru)
            '118991' => 37,  // HEBBAL (Bengaluru)
            '118992' => 43,  // DASARAHALLI (Bengaluru)
            '118988' => 27,  // KONANAKUNTE (Bengaluru)
            '118958' => 30,  // TIRUPATHI
            '119008' => 10,   // CALICUT (Kerala - Kozhikode)
            '119001' => 11,   // PALAKADU (Kerala - Palakkad)
            '119109' => 8,   // ERODE
            '110737' => 7,  // TIRUPPUR
            '119064' => 18,  // GANAPATHY (Coimbatore)
            '119101' => 33,  // THUDIYALUR (Coimbatore)
            '119067' => 19,  // SUNDARAPURAM (Coimbatore)
            '119088' => 22,  // POLLACHI
            '118824' => 35,  // KALLAKURICHI
            '118815' => 9,  // SALEM
            '118825' => 5,  // HOSUR
            '118829' => 28,  // HARUR
            '118828' => 50,  // ATTUR
            '118843' => 51,  // NAMAKKAL
            '177476' => 46,  // TIRUPATTUR
            '119040' => 20,  // TRICHY
            '119051' => 21,  // THANJAVUR
            '119033' => 25,  // MADURAI
            '277463' => 48,  // SIVAKASI
            '288923' => 45,  // NAGAPATINAM
            '118921' => 17,  // THIRUVALLUR
            '118938' => 4,  // KANCHIPURAM
            '118877' => 1,  // SHOLINGANALLUR & KARAPAKKAM
            '118888' => 3,  // URAPAKKAM
            '118909' => 2,  // MADIPAKKAM
            '118918' => 24,  // TAMBARAM
            '123561' => 47,  // CHENGALPET
            '123558' => 41,  // VADAPALANI
            '118955' => 39,  // VELLORE
            '358644' => 58,  // KRISHNAKIRI
            '358635' => 29,  // KARUR
        ];

        // =========================================================
        // 1. CASH — "BY CASH -BRANCHNAME ..."
        // =========================================================
        if (preg_match('/BY\s+CASH\s*[-–]\s*([A-Z]+)/i', $description, $m)) {
            $mode       = 'cash';
            $branchKeyword = $m[1];

            $loc = DB::table('tbl_locations')
                ->whereRaw('UPPER(name) LIKE ?', ['%' . $branchKeyword . '%'])
                ->select('id', 'name', 'zone_id')
                ->first();

            if ($loc) {
                $branchId   = $loc->id;
                $branchName = $loc->name;
                $zoneId     = $loc->zone_id;
                $zone       = DB::table('tblzones')->where('id', $zoneId)->first();
                $zoneName   = $zone ? $zone->name : null;
            }
        }

        // =========================================================
        // 2. MESPOS — "FT-MESPOS SET 10XXNNNNNN" / "MESPOS 10XXNNNNNN"
        //    10XX<6+digits> → full code = 100000000<suffix>
        //    MESPOS collects both Card and UPI — select both modes
        // =========================================================
        elseif (preg_match('/MESPOS/i', $description)) {
            $mode = ['card', 'upi']; // MESPOS handles both card & UPI

            // Extract the 10XX code pattern: "10" + at least 4 digits (last 6 are the suffix)
            if (preg_match('/10[X0]+([0-9]{6,})/i', $description, $m)) {
                $suffix = substr($m[1], -6); // take last 6 digits
                // dd($suffix);
                if (isset($mocCodeMap[$suffix])) {
                    $locId = $mocCodeMap[$suffix];
                    // dd($locId);
                    $loc   = DB::table('tbl_locations')
                        ->where('id', $locId)
                        ->select('id', 'name', 'zone_id')
                        ->first();
                    if ($loc) {
                        $branchId   = $loc->id;
                        $branchName = $loc->name;
                        $zoneId     = $loc->zone_id;
                        $zone       = DB::table('tblzones')->where('id', $zoneId)->first();
                        $zoneName   = $zone ? $zone->name : null;
                    }
                }
            }
        }

        // =========================================================
        // 3. NEFT
        // =========================================================
        elseif (preg_match('/\bNEFT\b/i', $description)) {
            $mode = 'neft';
        }

        // =========================================================
        // 4. UPI
        // =========================================================
        elseif (preg_match('/\bUPI\b/i', $description)) {
            $mode = 'upi';
        }

        return response()->json([
            'resolved'    => !empty($mode),
            'mode'        => $mode,
            'zone_id'     => $zoneId,
            'zone_name'   => $zoneName,
            'branch_id'   => $branchId,
            'branch_name' => $branchName,
            'date'        => $date,
        ]);
    }

    /**
     * Apply income tag for one branch + collection date + mode with an explicit bank-side amount.
     * Updates or inserts income_reconciliation_table only (no bank_statements row).
     *
     * @return array{id:int, action:string, date_formatted:string}
     */
    private function applyIncomeTagCore(
        object $stmt,
        string $zone,
        string $branch,
        Carbon $dateObj,
        string $mode,
        float $bankAmount,
        ?string $incomeTagMismatchRemark = null
    ): array {
        $dateFormatted = $dateObj->format('d/m/Y');

        if ($mode === 'card' || $mode === 'upi') {
            $bankIdCol  = 'card_upi_bank_id';
            $bankRefCol = 'card_upi_bank_ref_no';
        } elseif ($mode === 'neft') {
            $bankIdCol  = 'neft_bank_id';
            $bankRefCol = 'neft_bank_ref_no';
        } elseif ($mode === 'other') {
            $bankIdCol  = 'other_bank_id';
            $bankRefCol = 'other_bank_ref_no';
        } else {
            $bankIdCol  = 'cash_bank_id';
            $bankRefCol = 'cash_bank_ref_no';
        }

        $amount = $bankAmount;

        try {
            $txnDate = Carbon::createFromFormat('d/M/Y', $stmt->transaction_date)->format('d/m/Y');
        } catch (\Exception $e) {
            try {
                $txnDate = Carbon::parse($stmt->transaction_date)->format('d/m/Y');
            } catch (\Exception $e2) {
                $txnDate = $stmt->transaction_date;
            }
        }

        $refNo = $stmt->reference_number ?: $stmt->transaction_id;

        $existing = DB::table('income_reconciliation_table')
            ->where('location_name', $branch)
            ->whereRaw("STR_TO_DATE(date_range, '%d/%m/%Y') = STR_TO_DATE(?, '%d/%m/%Y')", [$dateFormatted])
            ->first();
            // dd($existing);
        if ($existing) {
            // Always refresh MOC DOC for this branch + collection date (same as insert path).
            // Otherwise an existing row with zero/stale MOC never gets API data when income-tagging.
            $mocData = $this->fetchMocDocForBranch($branch, $dateObj->format('Ymd'));
            $curMocCash    = (float)($mocData['cash']  ?? 0);
            $curMocCard    = (float)($mocData['card']  ?? 0);
            $curMocUpi     = (float)($mocData['upi']   ?? 0);
            $curMocNeft    = (float)($mocData['neft']  ?? 0);
            $curMocOther   = (float)($mocData['other'] ?? 0);
            $curMocOverall = array_sum($mocData);

            $curCollect  = (float)($existing->collection_amount ?? 0);
            $curDeposite = (float)($existing->deposite_amount   ?? 0);
            $curMesCard  = $curMocCard;
            $curMesUpi   = $curMocUpi;
            $curBankUpi  = (float)($existing->bank_upi_card     ?? 0);
            $curBankNeft = (float)($existing->bank_neft         ?? 0);
            $curBankOth  = (float)($existing->bank_others       ?? 0);

            $updateData = [
                $bankIdCol   => $stmt->id,
                $bankRefCol  => $refNo,
                'zone_name'  => $zone,
                'moc_cash_amt'       => $curMocCash,
                'moc_card_amt'       => $curMocCard,
                'moc_upi_amt'        => $curMocUpi,
                'moc_total_upi_card' => $curMocCard + $curMocUpi,
                'moc_neft_amt'       => $curMocNeft,
                'moc_other_amt'      => $curMocOther,
                'moc_overall_total'  => $curMocOverall,
                'mespos_card'        => $curMocCard,
                'mespos_upi'         => $curMocUpi,
                'updated_at'         => now(),
            ];

            if ($mode === 'cash') {
                $updateData['date_collection']   = $txnDate;
                $updateData['collection_amount'] = $curMocCash ?: $amount;
                $updateData['date_deposited']    = $txnDate;
                $updateData['deposite_amount']   = $amount;
                $updateData['cash_utr_number']   = $refNo;
                $curDeposite = $amount;
                $curCollect  = $updateData['collection_amount'];
            } elseif ($mode === 'card' || $mode === 'upi') {
                $updateData['date_settlement'] = $txnDate;
                $updateData['mespos_card']     = $curMocCard;
                $updateData['mespos_upi']      = $curMocUpi;
                $updateData['bank_upi_card']   = $amount;
                $updateData['bank_upi_card_utr']   = $refNo;
                $updateData['moc_total_upi_card'] = $curMocCard + $curMocUpi;
                $curMesCard = $curMocCard;
                $curMesUpi  = $curMocUpi;
                $curBankUpi = $amount;
            } elseif ($mode === 'neft') {
                $updateData['date_settlement'] = $txnDate;
                $updateData['bank_neft']       = $amount;
                $updateData['bank_neft_utr']   = $refNo;
                $curBankNeft = $amount;
            } elseif ($mode === 'other') {
                $updateData['date_settlement'] = $txnDate;
                $updateData['bank_others']     = $amount;
                $updateData['bank_other_utr']   = $refNo;
                $curBankOth = $amount;
            }

            $diffs = $this->calcIncomeDiffs(
                $curMocCash, $curMocCard, $curMocUpi, $curMocNeft, $curMocOther, $curMocOverall,
                $curCollect, $curDeposite,
                $curMesCard, $curMesUpi,
                $curBankUpi, $curBankNeft, $curBankOth
            );
            $updateData = array_merge($updateData, $diffs);
            if ($incomeTagMismatchRemark !== null && $incomeTagMismatchRemark !== ''
                && Schema::hasColumn('income_reconciliation_table', 'income_tag_mismatch_remark')) {
                $updateData['income_tag_mismatch_remark'] = $incomeTagMismatchRemark;
            }
            DB::table('income_reconciliation_table')
                ->where('id', $existing->id)
                ->update($updateData);

            return ['id' => (int) $existing->id, 'action' => 'updated', 'date_formatted' => $dateFormatted];
        }
        $mocData = $this->fetchMocDocForBranch($branch, $dateObj->format('Ymd'));
       
        $mocCash    = (float)($mocData['cash']  ?? 0);
        $mocCard    = (float)($mocData['card']  ?? 0);
        $mocUpi     = (float)($mocData['upi']   ?? 0);
        $mocNeft    = (float)($mocData['neft']  ?? 0);
        $mocOther   = (float)($mocData['other'] ?? 0);
        $mocOverall = array_sum($mocData);

        $collectAmt = 0;
        $depositeAmt = 0;
        $mesposCard = $mocCard;
        $mesposUpi  = $mocUpi;
        $bankUpiCard = 0;
        $bankNeft    = 0;
        $bankOthers  = 0;

        $insertData = [
            'zone_name'          => $zone,
            'location_name'      => $branch,
            'date_range'         => $dateFormatted,
            'moc_cash_amt'       => $mocCash,
            'moc_card_amt'       => $mocCard,
            'moc_upi_amt'        => $mocUpi,
            'moc_total_upi_card' => $mocCard + $mocUpi,
            'moc_neft_amt'       => $mocNeft,
            'moc_other_amt'      => $mocOther,
            'moc_overall_total'  => $mocOverall,
            'mespos_card'        => $mocCard,
            'mespos_upi'         => $mocUpi,
            $bankIdCol  => $stmt->id,
            $bankRefCol => $refNo,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if ($mode === 'cash') {
            $collectAmt  = $mocCash ?: $amount;
            $depositeAmt = $amount;
            $insertData['date_collection']   = $txnDate;
            $insertData['collection_amount'] = $collectAmt;
            $insertData['date_deposited']    = $txnDate;
            $insertData['deposite_amount']   = $depositeAmt;
            $insertData['cash_utr_number']   = $refNo;
        } elseif ($mode === 'card' || $mode === 'upi') {
            $bankUpiCard = $amount;
            $insertData['date_settlement'] = $txnDate;
            $insertData['bank_upi_card']   = $bankUpiCard;
            $insertData['bank_upi_card_utr']   = $refNo;
        } elseif ($mode === 'neft') {
            $bankNeft = $amount;
            $insertData['date_settlement'] = $txnDate;
            $insertData['bank_neft']       = $bankNeft;
            $insertData['bank_neft_utr']   = $refNo;
        } elseif ($mode === 'other') {
            $bankOthers = $amount;
            $insertData['date_settlement'] = $txnDate;
            $insertData['bank_others']     = $bankOthers;
            $insertData['bank_other_utr']   = $refNo;
        } else {
            $insertData['date_settlement'] = $txnDate;
        }

        $diffs = $this->calcIncomeDiffs(
            $mocCash, $mocCard, $mocUpi, $mocNeft, $mocOther, $mocOverall,
            $collectAmt, $depositeAmt,
            $mesposCard, $mesposUpi,
            $bankUpiCard, $bankNeft, $bankOthers
        );
        $insertData = array_merge($insertData, $diffs);
        if ($incomeTagMismatchRemark !== null && $incomeTagMismatchRemark !== ''
            && Schema::hasColumn('income_reconciliation_table', 'income_tag_mismatch_remark')) {
            $insertData['income_tag_mismatch_remark'] = $incomeTagMismatchRemark;
        }
        $incomeReconId = DB::table('income_reconciliation_table')->insertGetId($insertData);

        return ['id' => (int) $incomeReconId, 'action' => 'created', 'date_formatted' => $dateFormatted];
    }

    /** Rupee tolerance when comparing MOC DOC bucket total vs bank tag amount */
    private const INCOME_TAG_MOC_AMOUNT_EPS = 1.0;

    /**
     * MOC DOC total for the income-tag mode (same buckets as fetchMocDocForBranch).
     */
    private function incomeTagMocAmountForMode(array $mocData, string $mode): float
    {
        return match ($mode) {
            'cash'  => (float) ($mocData['cash'] ?? 0),
            'card'  => (float) ($mocData['card'] ?? 0),
            'upi'   => (float) ($mocData['upi'] ?? 0),
            'neft'  => (float) ($mocData['neft'] ?? 0),
            'other' => (float) ($mocData['other'] ?? 0),
            default => 0.0,
        };
    }

    /**
     * Compare bank tag amount (per collection date) to MOC DOC for each selected mode.
     * Uses the same API spacing as applyIncomeTag to reduce HTTP 429 on multi-date checks.
     *
     * @param  array<int, string>  $sortedYmd
     * @param  array<int, string>  $modes
     * @param  array<string, float>  $amountByYmd
     * @return array<int, array{date_ymd:string, date_display:string, mode:string, moc_amount:float, tag_amount:float, diff:float}>
     */
    private function collectIncomeTagMocVsBankMismatches(string $branch, array $sortedYmd, array $modes, array $amountByYmd): array
    {
        $out = [];
        $eps = self::INCOME_TAG_MOC_AMOUNT_EPS;
        foreach ($sortedYmd as $idx => $ymd) {
            if ($idx > 0) {
                usleep(800000);
            }
            try {
                $dateObj = Carbon::createFromFormat('Y-m-d', $ymd);
            } catch (\Exception $e) {
                continue;
            }
            $mocData = $this->fetchMocDocForBranch($branch, $dateObj->format('Ymd'));
            $portion = (float) ($amountByYmd[$ymd] ?? 0);
            foreach ($modes as $mode) {
                $mocAmt = $this->incomeTagMocAmountForMode($mocData, $mode);
                if (abs($mocAmt - $portion) > $eps) {
                    $out[] = [
                        'date_ymd'      => $ymd,
                        'date_display'  => $dateObj->format('d/m/Y'),
                        'mode'          => $mode,
                        'moc_amount'    => round($mocAmt, 2),
                        'tag_amount'    => round($portion, 2),
                        'diff'          => round($portion - $mocAmt, 2),
                    ];
                }
            }
        }

        return $out;
    }

    /**
     * Apply income tag: link this bank statement to income_reconciliation_table row(s)
     * for the chosen branch + collection date(s) + mode(s). Each date fetches MOC DOC on insert and refresh on update.
     *
     * Supports:
     *   - Legacy: single `date` + `mode` (same as before).
     *   - Batch: `dates` (array of Y-m-d) + `modes` (array); optional `date_amounts` map Y-m-d => amount
     *     for splitting the bank line across collection dates (defaults to equal split).
     */
    public function applyIncomeTag(Request $request)
    {
        $request->validate([
            'bank_statement_id' => 'required|integer',
            'zone'              => 'required|string',
            'branch'            => 'required|string',
            'date'              => 'nullable|string',
            'dates'             => 'nullable|array',
            'dates.*'           => 'nullable|string',
            'mode'              => 'nullable|in:cash,card,upi,neft,other',
            'modes'             => 'nullable|array',
            'modes.*'           => 'nullable|in:cash,card,upi,neft,other',
            'date_amounts'      => 'nullable|array',
            'acknowledge_income_amount_mismatch' => 'nullable|boolean',
            'income_amount_mismatch_remark'      => 'nullable|string|max:2000',
        ]);

        $modes = [];
        if ($request->filled('modes') && is_array($request->modes)) {
            $modes = array_values(array_unique(array_filter($request->modes)));
        } elseif ($request->filled('mode')) {
            $modes = [$request->mode];
        }
        if ($modes === []) {
            return response()->json(['success' => false, 'message' => 'Select at least one mode of collection'], 422);
        }

        $dateStrings = [];
        if ($request->filled('dates') && is_array($request->dates)) {
            $dateStrings = array_values(array_filter($request->dates));
        } elseif ($request->filled('date')) {
            $dateStrings = [$request->date];
        }
        if ($dateStrings === []) {
            return response()->json(['success' => false, 'message' => 'Select at least one collection date'], 422);
        }

        $stmt = DB::table('bank_statements')->find($request->bank_statement_id);
        if (!$stmt) {
            return response()->json(['success' => false, 'message' => 'Bank statement not found'], 404);
        }

        $lineTotal = $stmt->withdrawal > 0 ? (float) $stmt->withdrawal : (float) $stmt->deposit;

        $parsedDates = [];
        foreach ($dateStrings as $ds) {
            try {
                $parsedDates[] = Carbon::createFromFormat('Y-m-d', $ds)->startOfDay();
            } catch (\Exception $e) {
                try {
                    $parsedDates[] = Carbon::parse($ds)->startOfDay();
                } catch (\Exception $e2) {
                    return response()->json(['success' => false, 'message' => 'Invalid collection date: ' . $ds], 422);
                }
            }
        }
        usort($parsedDates, function (Carbon $a, Carbon $b) {
            return $a->timestamp <=> $b->timestamp;
        });
        $sortedYmd = array_map(fn (Carbon $c) => $c->format('Y-m-d'), $parsedDates);
        // dd($sortedYmd);
        $n = count($sortedYmd);
        $amountByYmd = [];
        if ($n === 1) {
            $amountByYmd[$sortedYmd[0]] = $lineTotal;
        } else {
            $custom = $request->input('date_amounts', []);
            // dd($custom);
            if (is_array($custom) && count(array_filter($custom, fn ($v) => $v !== null && $v !== '')) > 0) {
                foreach ($sortedYmd as $ymd) {
                    $raw = $custom[$ymd] ?? null;
                    if ($raw === null || $raw === '') {
                        return response()->json(['success' => false, 'message' => 'Enter a bank amount for each selected collection date'], 422);
                    }
                    $amountByYmd[$ymd] = (float) $raw;
                }
            } else {
                $each = round($lineTotal / $n, 2);
                foreach ($sortedYmd as $i => $ymd) {
                    $amountByYmd[$ymd] = ($i === $n - 1)
                        ? round($lineTotal - $each * ($n - 1), 2)
                        : $each;
                }
            }
            $sumPortions = array_sum($amountByYmd);
            if (abs($sumPortions - $lineTotal) > 0.05) {
                return response()->json([
                    'success' => false,
                    'message' => 'Per-date amounts must sum to the bank line total (' . number_format($lineTotal, 2) . '). Current sum: ' . number_format($sumPortions, 2),
                ], 422);
            }
        }
        $zone   = $request->zone;
        $branch = $request->branch;

        $acknowledgeMismatch = $request->boolean('acknowledge_income_amount_mismatch');
        if ($acknowledgeMismatch) {
            $request->validate([
                'income_amount_mismatch_remark' => 'required|string|min:1|max:2000',
            ]);
        }
        $mismatchRemark = $acknowledgeMismatch
            ? trim((string) $request->input('income_amount_mismatch_remark', ''))
            : null;

        if (! $acknowledgeMismatch) {
            $mocMismatches = $this->collectIncomeTagMocVsBankMismatches($branch, $sortedYmd, $modes, $amountByYmd);
            if ($mocMismatches !== []) {
                return response()->json([
                    'success'    => false,
                    'code'       => 'income_tag_moc_amount_mismatch',
                    'message'    => 'MOC DOC amount does not match the bank amount you are tagging for one or more collection date / mode combinations. Review the differences below. If you still want to apply the tag, confirm below.',
                    'mismatches' => $mocMismatches,
                ], 409);
            }
        }

        $created = 0;
        $updated = 0;
        $firstDateFormatted = null;
        $primaryReconId = null;
        $reconIdsByYmd = [];

        DB::beginTransaction();
        try {
            foreach ($sortedYmd as $idx => $ymd) {
                // Second+ date: small pause so back-to-back MOC DOC calls are less likely to hit HTTP 429.
                if ($idx > 0) {
                    \Log::info('applyIncomeTag: delay before MOC DOC fetch for next collection date', [
                        'next_date_ymd' => $ymd,
                        'wait_ms'       => 800,
                    ]);
                    usleep(800000);
                }

                $dateObj = Carbon::createFromFormat('Y-m-d', $ymd);
                $portion = (float) ($amountByYmd[$ymd] ?? $lineTotal);
                $lastIdForDate = null;
                foreach ($modes as $mode) {
                    $res = $this->applyIncomeTagCore(
                        $stmt,
                        $zone,
                        $branch,
                        $dateObj,
                        $mode,
                        $portion,
                        $acknowledgeMismatch ? $mismatchRemark : null
                    );
                    
                    if ($res['action'] === 'created') {
                        $created++;
                    } else {
                        $updated++;
                    }
                    $lastIdForDate = $res['id'];
                }
                $reconIdsByYmd[$ymd] = $lastIdForDate;
            
                if ($idx === 0) {
                    $primaryReconId = $lastIdForDate;
                    $firstDateFormatted = $res['date_formatted'];
                }
            }

            $userId = Auth::id();
            $incomeUpdate = [
                'income_match_status'      => 'income_matched',
                'income_reconciliation_id' => $primaryReconId,
                'income_matched_branch'    => $branch,
                'income_matched_date'      => $firstDateFormatted,
                'income_matched_by'        => $userId,
                'income_matched_at'        => now(),
                'updated_at'               => now(),
            ];
            if ($n > 1 && Schema::hasColumn('bank_statements', 'income_match_split_json')) {
                $incomeUpdate['income_match_split_json'] = json_encode([
                    'dates_ymd'    => $sortedYmd,
                    'recon_ids'    => $reconIdsByYmd,
                    'amounts_ymd'  => $amountByYmd,
                    'modes'        => $modes,
                ]);
            } elseif (Schema::hasColumn('bank_statements', 'income_match_split_json')) {
                $incomeUpdate['income_match_split_json'] = null;
            }

            if (Schema::hasColumn('bank_statements', 'income_tag_mismatch_remark')) {
                $incomeUpdate['income_tag_mismatch_remark'] = ($acknowledgeMismatch && $mismatchRemark !== null && $mismatchRemark !== '')
                    ? $mismatchRemark
                    : null;
            }

            $safeUpdate = [];
            foreach ($incomeUpdate as $col => $val) {
                if (Schema::hasColumn('bank_statements', $col)) {
                    $safeUpdate[$col] = $val;
                }
            }
            if (!empty($safeUpdate)) {
                DB::table('bank_statements')->where('id', $stmt->id)->update($safeUpdate);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error applying income tag: ' . $e->getMessage(),
            ], 500);
        }

        $parts = [];
        if ($created) {
            $parts[] = $created . ' record(s) created';
        }
        if ($updated) {
            $parts[] = $updated . ' record(s) updated';
        }
        $message = ($parts ? implode('; ', $parts) : 'Income tag applied')
            . ' for ' . $n . ' collection date(s), modes: ' . implode(', ', $modes);

        return response()->json([
            'success'                    => true,
            'message'                    => $message,
            'created'                    => $created,
            'updated'                    => $updated,
            'action'                     => $created > 0 ? 'created' : 'updated',
            'primary_income_reconciliation_id' => $primaryReconId,
        ]);
    }

    /**
     * Remove income tag from a bank statement.
     * Clears the bank-ref and bank-amount columns that this statement filled in
     * on the linked income_reconciliation_table row, recalculates diffs,
     * and resets the bank_statements income match tracking columns.
     */
    public function unmatchIncome($id)
    {
        DB::beginTransaction();
        try {
            $stmt = DB::table('bank_statements')->find($id);
            if (!$stmt) {
                return response()->json(['success' => false, 'message' => 'Bank statement not found'], 404);
            }

            if (($stmt->income_match_status ?? '') !== 'income_matched') {
                return response()->json(['success' => false, 'message' => 'This statement has no income tag to remove'], 400);
            }

            $stmtId = (int) $id;
            $linkedRecons = DB::table('income_reconciliation_table')
                ->where(function ($q) use ($stmtId) {
                    $q->where('cash_bank_id', $stmtId)
                        ->orWhere('card_upi_bank_id', $stmtId)
                        ->orWhere('neft_bank_id', $stmtId)
                        ->orWhere('other_bank_id', $stmtId);
                })
                ->get();

            foreach ($linkedRecons as $recon) {
                $clearData = ['updated_at' => now()];

                if ((int) ($recon->cash_bank_id ?? 0) === $stmtId) {
                    $clearData['cash_bank_id'] = null;
                    $clearData['cash_bank_ref_no'] = null;
                    $clearData['collection_amount'] = 0;
                    $clearData['deposite_amount'] = 0;
                    $clearData['date_collection'] = null;
                    $clearData['date_deposited'] = null;
                }
                if ((int) ($recon->card_upi_bank_id ?? 0) === $stmtId) {
                    $clearData['card_upi_bank_id'] = null;
                    $clearData['card_upi_bank_ref_no'] = null;
                    $clearData['bank_upi_card'] = 0;
                    $clearData['mespos_card'] = 0;
                    $clearData['mespos_upi'] = 0;
                    $clearData['date_settlement'] = null;
                }
                if ((int) ($recon->neft_bank_id ?? 0) === $stmtId) {
                    $clearData['neft_bank_id'] = null;
                    $clearData['neft_bank_ref_no'] = null;
                    $clearData['bank_neft'] = 0;
                }
                if ((int) ($recon->other_bank_id ?? 0) === $stmtId) {
                    $clearData['other_bank_id'] = null;
                    $clearData['other_bank_ref_no'] = null;
                    $clearData['bank_others'] = 0;
                }

                if (Schema::hasColumn('income_reconciliation_table', 'income_tag_mismatch_remark')) {
                    $clearData['income_tag_mismatch_remark'] = null;
                }

                if (count($clearData) <= 1) {
                    continue;
                }

                $merged = array_merge((array) $recon, $clearData);

                $diffs = $this->calcIncomeDiffs(
                    (float) ($merged['moc_cash_amt'] ?? 0),
                    (float) ($merged['moc_card_amt'] ?? 0),
                    (float) ($merged['moc_upi_amt'] ?? 0),
                    (float) ($merged['moc_neft_amt'] ?? 0),
                    (float) ($merged['moc_other_amt'] ?? 0),
                    (float) ($merged['moc_overall_total'] ?? 0),
                    (float) ($merged['collection_amount'] ?? 0),
                    (float) ($merged['deposite_amount'] ?? 0),
                    (float) ($merged['mespos_card'] ?? 0),
                    (float) ($merged['mespos_upi'] ?? 0),
                    (float) ($merged['bank_upi_card'] ?? 0),
                    (float) ($merged['bank_neft'] ?? 0),
                    (float) ($merged['bank_others'] ?? 0)
                );

                DB::table('income_reconciliation_table')
                    ->where('id', $recon->id)
                    ->update(array_merge($clearData, $diffs));
            }

            // --- Reset bank_statements income tracking columns ---
            $resetUpdate = [
                'income_match_status'      => 'income_unmatched',
                'income_reconciliation_id' => null,
                'income_matched_branch'    => null,
                'income_matched_date'      => null,
                'income_matched_by'        => null,
                'income_matched_at'        => null,
                'updated_at'               => now(),
            ];
            if (Schema::hasColumn('bank_statements', 'income_match_split_json')) {
                $resetUpdate['income_match_split_json'] = null;
            }
            if (Schema::hasColumn('bank_statements', 'income_tag_mismatch_remark')) {
                $resetUpdate['income_tag_mismatch_remark'] = null;
            }
            $safeReset = [];
            foreach ($resetUpdate as $col => $val) {
                if (Schema::hasColumn('bank_statements', $col)) {
                    $safeReset[$col] = $val;
                }
            }
            if (Schema::hasColumn('bank_statements', 'category') && (($stmt->match_status ?? '') === 'unmatched')) {
                $safeReset['category'] = 'uncategory';
            }
            DB::table('bank_statements')->where('id', $id)->update($safeReset);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Income tag removed and reconciliation record cleared',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error removing income tag: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save Radiant keyword and/or link to radiant_cash_pickups (marks radiant_matched like income tag).
     */
    public function saveRadiantMatchAgainst(Request $request)
    {
        if (! Schema::hasColumn('bank_statements', 'radiant_match_against')) {
            return response()->json([
                'success' => false,
                'message' => 'Column radiant_match_against is missing. Run: php artisan migrate',
            ], 503);
        }

        $validated = $request->validate([
            'bank_statement_id'      => 'required|integer',
            'radiant_match_against'  => 'nullable|string|max:255',
            'radiant_cash_pickup_id' => 'nullable',
        ]);

        $stmt = DB::table('bank_statements')->where('id', $validated['bank_statement_id'])->first();
        if (! $stmt) {
            return response()->json([
                'success' => false,
                'message' => 'Bank statement not found',
            ], 404);
        }

        $raw = isset($validated['radiant_match_against']) ? trim($validated['radiant_match_against']) : '';
        $keyword = $raw === '' ? null : $raw;

        $pickupRaw = $request->input('radiant_cash_pickup_id');
        $pickupId = ($pickupRaw === '' || $pickupRaw === null) ? null : (int) $pickupRaw;

        $hadPickupLink = false;
        if (Schema::hasColumn('bank_statements', 'radiant_match_status')
            && (($stmt->radiant_match_status ?? '') === 'radiant_matched')) {
            $hadPickupLink = true;
        }
        if (Schema::hasColumn('bank_statements', 'radiant_cash_pickup_id')
            && ! empty($stmt->radiant_cash_pickup_id ?? null)) {
            $hadPickupLink = true;
        }

        $update = [
            'radiant_match_against' => $keyword,
            'updated_at'            => now(),
        ];

        $hasTracking = Schema::hasColumn('bank_statements', 'radiant_match_status');

        if ($hasTracking) {
            if ($pickupId) {
                $pickup = DB::table('radiant_cash_pickups')->where('id', $pickupId)->first();
                if (! $pickup) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Radiant cash pickup id '.$pickupId.' was not found.',
                    ], 422);
                }

                $pickupDateStr = $pickup->pickup_date;
                if (! $pickupDateStr && ! empty($pickup->pickup_date_parsed)) {
                    try {
                        $pickupDateStr = Carbon::parse($pickup->pickup_date_parsed)->format('d/m/Y');
                    } catch (\Exception $e) {
                        $pickupDateStr = (string) $pickup->pickup_date_parsed;
                    }
                }

                $user = Auth::user();
                $update['radiant_match_status'] = 'radiant_matched';
                $update['radiant_cash_pickup_id'] = $pickupId;
                $update['radiant_matched_location'] = $pickup->location;
                $update['radiant_matched_pickup_date'] = $pickupDateStr;
                $update['radiant_matched_by'] = $user ? $user->id : null;
                $update['radiant_matched_at'] = now();
            } else {
                $update['radiant_match_status'] = 'radiant_unmatched';
                $update['radiant_cash_pickup_id'] = null;
                $update['radiant_matched_location'] = null;
                $update['radiant_matched_pickup_date'] = null;
                $update['radiant_matched_by'] = null;
                $update['radiant_matched_at'] = null;
            }
        }

        $safeUpdate = [];
        foreach ($update as $col => $val) {
            if (Schema::hasColumn('bank_statements', $col)) {
                $safeUpdate[$col] = $val;
            }
        }

        DB::table('bank_statements')->where('id', $stmt->id)->update($safeUpdate);

        $fresh = DB::table('bank_statements')->where('id', $stmt->id)->first();
        $byName = null;
        $byUsername = null;
        if ($fresh && ! empty($fresh->radiant_matched_by) && Schema::hasTable('users')) {
            $u = DB::table('users')->where('id', $fresh->radiant_matched_by)->first();
            if ($u) {
                $byName = $u->user_fullname ?? null;
                $byUsername = $u->username ?? null;
            }
        }

        $msgParts = [];
        if ($keyword !== null) {
            $msgParts[] = 'Keyword saved';
        } else {
            $msgParts[] = 'Keyword cleared';
        }
        if ($pickupId && $hasTracking) {
            $msgParts[] = 'linked to Radiant pickup #'.$pickupId;
        } elseif ($hasTracking && $hadPickupLink && ! $pickupId) {
            $msgParts[] = 'pickup link cleared';
        }

        return response()->json([
            'success'                      => true,
            'message'                      => implode(' — ', $msgParts).'.',
            'radiant_match_against'        => $fresh->radiant_match_against ?? null,
            'radiant_match_status'         => $fresh->radiant_match_status ?? null,
            'radiant_cash_pickup_id'       => $fresh->radiant_cash_pickup_id ?? null,
            'radiant_matched_location'     => $fresh->radiant_matched_location ?? null,
            'radiant_matched_pickup_date'  => $fresh->radiant_matched_pickup_date ?? null,
            'radiant_matched_at'           => $fresh->radiant_matched_at ?? null,
            'radiant_matched_by_name'      => $byName,
            'radiant_matched_by_username'  => $byUsername,
        ]);
    }

    /**
     * Remove Radiant pickup link from a bank statement (keeps keyword if any).
     */
    public function unmatchRadiant($id)
    {
        if (! Schema::hasColumn('bank_statements', 'radiant_match_status')) {
            return response()->json([
                'success' => false,
                'message' => 'Radiant columns missing. Run migrations.',
            ], 503);
        }

        $stmt = DB::table('bank_statements')->where('id', $id)->first();
        if (! $stmt) {
            return response()->json(['success' => false, 'message' => 'Bank statement not found'], 404);
        }

        if (($stmt->radiant_match_status ?? '') !== 'radiant_matched') {
            return response()->json([
                'success' => false,
                'message' => 'This statement has no Radiant pickup link to remove',
            ], 400);
        }

        $reset = [
            'radiant_match_status'         => 'radiant_unmatched',
            'radiant_cash_pickup_id'       => null,
            'radiant_matched_location'     => null,
            'radiant_matched_pickup_date'  => null,
            'radiant_matched_by'          => null,
            'radiant_matched_at'           => null,
            'updated_at'                   => now(),
        ];
        $safe = [];
        foreach ($reset as $col => $val) {
            if (Schema::hasColumn('bank_statements', $col)) {
                $safe[$col] = $val;
            }
        }
        DB::table('bank_statements')->where('id', $id)->update($safe);

        return response()->json([
            'success' => true,
            'message' => 'Radiant pickup link removed (keyword kept if set)',
        ]);
    }

    /**
     * Calculate all income reconciliation difference fields.
     *
     *   cash_diff        = collection_amount (MOC cash) - deposite_amount (bank deposit)
     *   card_upi_diff    = (mespos_card + mespos_upi) - bank_upi_card
     *   neft_others_diff = (moc_neft + moc_other) - (bank_neft + bank_others)
     *   radiant_diff     = moc_overall_total - (deposite_amount + bank_upi_card + bank_neft + bank_others)
     */
    private function calcIncomeDiffs(
        float $mocCash, float $mocCard, float $mocUpi,
        float $mocNeft, float $mocOther, float $mocOverall,
        float $collectAmt, float $depositeAmt,
        float $mesposCard, float $mesposUpi,
        float $bankUpiCard, float $bankNeft, float $bankOthers
    ): array {
        $mocTotalUpiCard = $mocCard + $mocUpi;
        $cashDiff        = $collectAmt - $depositeAmt;
        $cardUpiDiff     = ($mesposCard + $mesposUpi) - $bankUpiCard;
        $neftOthersDiff  = ($mocNeft + $mocOther) - ($bankNeft + $bankOthers);
        $bankTotal       = $depositeAmt + $bankUpiCard + $bankNeft + $bankOthers;
        $radiantDiff     = $mocOverall - $bankTotal;

        return [
            'moc_total_upi_card' => $mocTotalUpiCard,
            'cash_diff'          => $cashDiff,
            'card_upi_diff'      => $cardUpiDiff,
            'neft_others_diff'   => $neftOthersDiff,
            'radiant_diff'       => $radiantDiff,
        ];
    }

    /**
     * Fetch MOC DOC totals (cash, card, upi, neft, other) for a given branch and date.
     * Uses the same MOC DOC API as IncomeReconciliationController.
     *
     * Retries with exponential backoff on HTTP 429/502/503/504 and logs each attempt so
     * laravel.log shows why a second date might return zeros (rate limit vs empty list).
     */
    private function fetchMocDocForBranch(string $branchName, string $dateYmd): array
    {
        $totals = ['cash' => 0.0, 'card' => 0.0, 'upi' => 0.0, 'neft' => 0.0, 'other' => 0.0];

        $cityArray   = $this->incomeCityArray();
        $locationKey = array_search($branchName, $cityArray);

        if ($locationKey === false) {
            \Log::warning('fetchMocDocForBranch: branch not in incomeCityArray — API not called', [
                'branch'  => $branchName,
                'dateYmd' => $dateYmd,
            ]);
            return $totals;
        }

        $url        = 'https://mocdoc.in/api/get/billlist/draravinds-ivf';
        $postFields = "date={$dateYmd}&entitylocation={$locationKey}";
        $headFields = [
            'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
            'Date: Fri, 07 Mar 2025 10:07:52 GMT',
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: SRV=s1',
        ];

        $maxAttempts  = 6;
        $successBody  = null;
        $lastHttp     = 0;
        $lastCurlErr  = '';
        $lastSnippet  = '';

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            if ($attempt > 1) {
                $waitMs = (int) min(12000, 1000 * (2 ** ($attempt - 2)));
                \Log::info('fetchMocDocForBranch: backoff before retry', [
                    'branch'     => $branchName,
                    'dateYmd'    => $dateYmd,
                    'locationKey'=> $locationKey,
                    'attempt'    => $attempt,
                    'wait_ms'    => $waitMs,
                    'last_http'  => $lastHttp,
                ]);
                usleep($waitMs * 1000);
            }

            \Log::info('fetchMocDocForBranch: HTTP request', [
                'branch'       => $branchName,
                'dateYmd'      => $dateYmd,
                'locationKey'  => $locationKey,
                'attempt'      => $attempt,
                'max_attempts' => $maxAttempts,
                'url'          => $url,
                'post_fields'  => $postFields,
            ]);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $postFields,
                CURLOPT_HTTPHEADER     => $headFields,
                CURLOPT_TIMEOUT        => 25,
            ]);
            $response    = curl_exec($ch);
            $lastCurlErr = curl_error($ch);
            $lastHttp    = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $flat = is_string($response) ? preg_replace('/\s+/u', ' ', $response) : '';
            $lastSnippet = Str::limit($flat, 500, '…');

            if ($response !== false && $lastHttp === 200) {
                $successBody = $response;
                break;
            }

            $retryable = in_array($lastHttp, [429, 502, 503, 504], true) || $response === false;
            if ($lastHttp === 429) {
                \Log::warning('fetchMocDocForBranch: HTTP 429 Too Many Requests (MOC DOC rate limit)', [
                    'branch'            => $branchName,
                    'dateYmd'           => $dateYmd,
                    'locationKey'       => $locationKey,
                    'attempt'           => $attempt,
                    'curl_error'        => $lastCurlErr,
                    'response_snippet'  => $lastSnippet,
                    'will_retry'        => $retryable && $attempt < $maxAttempts,
                ]);
            } else {
                \Log::warning('fetchMocDocForBranch: non-200 response', [
                    'branch'            => $branchName,
                    'dateYmd'           => $dateYmd,
                    'locationKey'       => $locationKey,
                    'attempt'           => $attempt,
                    'http_code'         => $lastHttp,
                    'curl_error'        => $lastCurlErr,
                    'response_snippet'  => $lastSnippet,
                    'will_retry'        => $retryable && $attempt < $maxAttempts,
                ]);
            }

            if (!$retryable || $attempt >= $maxAttempts) {
                break;
            }
        }

        if ($successBody === null) {
            \Log::error('fetchMocDocForBranch: giving up — returning zero MOC totals', [
                'branch'             => $branchName,
                'dateYmd'            => $dateYmd,
                'locationKey'        => $locationKey,
                'final_http_code'    => $lastHttp,
                'curl_error'         => $lastCurlErr,
                'response_snippet'   => $lastSnippet,
                'rate_limit_note'    => $lastHttp === 429
                    ? '429 means the API rejected the call due to too many requests in a short window; retries and spacing between dates reduce this.'
                    : null,
            ]);
            return $totals;
        }

        $data = json_decode($successBody, true);

        if (!empty($data['billinglist']) && is_array($data['billinglist'])) {
            $n = count($data['billinglist']);
            foreach ($data['billinglist'] as $bill) {
                $payType = strtolower(trim($bill['paymenttype'] ?? ''));
                $amt     = floatval($bill['amt'] ?? 0);

                switch ($payType) {
                    case 'cash': $totals['cash']  += $amt; break;
                    case 'card': $totals['card']  += $amt; break;
                    case 'upi':  $totals['upi']   += $amt; break;
                    case 'neft': $totals['neft']  += $amt; break;
                    default:     $totals['other'] += $amt; break;
                }
            }
            \Log::info('fetchMocDocForBranch: parsed billinglist OK', [
                'branch'       => $branchName,
                'dateYmd'      => $dateYmd,
                'locationKey'  => $locationKey,
                'billing_rows' => $n,
                'totals'       => $totals,
            ]);
        } else {
            \Log::info('fetchMocDocForBranch: HTTP 200 but no billinglist (empty day or unexpected JSON)', [
                'branch'       => $branchName,
                'dateYmd'      => $dateYmd,
                'locationKey'  => $locationKey,
                'decoded_keys' => is_array($data) ? array_keys($data) : null,
            ]);
        }

        return $totals;
    }

    /**
     * MOC DOC location key → branch name mapping (mirrors IncomeReconciliationController)
     */
    private function incomeCityArray(): array
    {
        // Keep in sync with SuperAdminController::cityArray()
        return [
            "location1"  => "Kerala - Palakkad",
            "location7"  => "Erode",
            "location14" => "Tiruppur",
            "location6"  => "Kerala - Kozhikode",
            "location20" => "Coimbatore - Ganapathy",
            "location21" => "Hosur",
            "location22" => "Chennai - Sholinganallur",
            "location23" => "Chennai - Urapakkam",
            "location24" => "Chennai - Madipakkam",
            "location26" => "Kanchipuram",
            "location27" => "Coimbatore - Sundarapuram",
            "location28" => "Trichy",
            "location29" => "Thiruvallur",
            "location30" => "Pollachi",
            "location31" => "Bengaluru - Electronic City",
            "location32" => "Bengaluru - Konanakunte",
            "location33" => "Chennai - Tambaram",
            "location34" => "Tanjore",
            "location36" => "Harur",
            "location39" => "Coimbatore - Thudiyalur",
            "location40" => "Madurai",
            "location41" => "Bengaluru - Hebbal",
            "location42" => "Kallakurichi",
            "location43" => "Vellore",
            "location44" => "Tirupati",
            "location45" => "Aathur",
            "location46" => "Namakal",
            "location47" => "Bengaluru - Dasarahalli",
            "location48" => "Chengalpattu",
            "location49" => "Chennai - Vadapalani",
            "location50" => "Pennagaram",
            "location51" => "Thirupathur",
            "location52" => "Sivakasi",
            "location13" => "Salem",
            "location54" => "Nagapattinam",
            "location56" => "Krishnagiri",
            "location57" => "Karur",
        ];
    }

    /**
     * Document types for bank–bill match attachments (master). Dropdown uses non-admin list (active only).
     */
    public function listMatchAttachmentTypes(Request $request)
    {
        if (! Schema::hasTable('bank_recon_match_attachment_types')) {
            return response()->json([]);
        }
        $q = DB::table('bank_recon_match_attachment_types')->orderBy('sort_order')->orderBy('id');
        if (! $request->boolean('admin')) {
            $q->where('is_active', true);
        }

        $rows = $q->get();

        return response()->json($rows->map(function ($r) {
            $path = isset($r->sample_file_path) ? (string) $r->sample_file_path : '';

            return [
                'id'               => (int) $r->id,
                'name'             => (string) ($r->name ?? ''),
                'sort_order'       => (int) ($r->sort_order ?? 0),
                'is_active'        => (bool) ($r->is_active ?? true),
                'sample_file_path' => $path !== '' ? $path : null,
                'sample_url'       => ($path !== '' && is_file(public_path($path))) ? asset($path) : null,
            ];
        })->values()->all());
    }

    public function storeMatchAttachmentType(Request $request)
    {
        if (! Schema::hasTable('bank_recon_match_attachment_types')) {
            return response()->json(['success' => false, 'message' => 'Run migrations to enable attachment types.'], 503);
        }
        $request->validate([
            'name'       => 'required|string|max:191',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'sample_file'=> 'nullable|file|max:5120',
        ]);

        $sort = (int) $request->input('sort_order', 0);
        $path = null;
        if ($request->hasFile('sample_file') && $request->file('sample_file')->isValid()) {
            $file = $request->file('sample_file');
            $destDir = public_path('bank_recon_attachment_type_samples');
            if (! File::isDirectory($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }
            $orig = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension() ?: 'bin';
            $base = Str::slug(pathinfo($orig, PATHINFO_FILENAME)) ?: 'sample';
            $safe = $base.'-'.Str::random(6).'.'.$ext;
            $file->move($destDir, $safe);
            $path = 'bank_recon_attachment_type_samples/'.$safe;
        }

        $id = DB::table('bank_recon_match_attachment_types')->insertGetId([
            'name'             => trim((string) $request->name),
            'sort_order'       => $sort,
            'is_active'        => true,
            'sample_file_path' => $path,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $row = DB::table('bank_recon_match_attachment_types')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Attachment type saved',
            'type'    => [
                'id'               => (int) $row->id,
                'name'             => (string) $row->name,
                'sort_order'       => (int) $row->sort_order,
                'sample_file_path' => $row->sample_file_path,
                'sample_url'       => $row->sample_file_path ? asset($row->sample_file_path) : null,
            ],
        ]);
    }

    public function updateMatchAttachmentType(Request $request, int $id)
    {
        if (! Schema::hasTable('bank_recon_match_attachment_types')) {
            return response()->json(['success' => false, 'message' => 'Table missing'], 503);
        }
        $existing = DB::table('bank_recon_match_attachment_types')->where('id', $id)->first();
        if (! $existing) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        $request->validate([
            'name'       => 'sometimes|required|string|max:191',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'is_active'  => 'sometimes|boolean',
            'sample_file'=> 'nullable|file|max:5120',
        ]);

        $update = ['updated_at' => now()];
        if ($request->has('name')) {
            $update['name'] = trim((string) $request->name);
        }
        if ($request->has('sort_order')) {
            $update['sort_order'] = (int) $request->sort_order;
        }
        if ($request->has('is_active')) {
            $update['is_active'] = $request->boolean('is_active');
        }
        if ($request->hasFile('sample_file') && $request->file('sample_file')->isValid()) {
            $oldPath = isset($existing->sample_file_path) ? (string) $existing->sample_file_path : '';
            if ($oldPath !== '' && Str::startsWith($oldPath, 'bank_recon_attachment_type_samples/')) {
                $full = public_path($oldPath);
                if (is_file($full)) {
                    @unlink($full);
                }
            }
            $file = $request->file('sample_file');
            $destDir = public_path('bank_recon_attachment_type_samples');
            if (! File::isDirectory($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }
            $orig = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension() ?: 'bin';
            $base = Str::slug(pathinfo($orig, PATHINFO_FILENAME)) ?: 'sample';
            $safe = $base.'-'.Str::random(6).'.'.$ext;
            $file->move($destDir, $safe);
            $update['sample_file_path'] = 'bank_recon_attachment_type_samples/'.$safe;
        }

        DB::table('bank_recon_match_attachment_types')->where('id', $id)->update($update);

        $row = DB::table('bank_recon_match_attachment_types')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'type'    => [
                'id'               => (int) $row->id,
                'name'             => (string) $row->name,
                'sort_order'       => (int) $row->sort_order,
                'is_active'        => (bool) $row->is_active,
                'sample_file_path' => $row->sample_file_path,
                'sample_url'       => $row->sample_file_path ? asset($row->sample_file_path) : null,
            ],
        ]);
    }

    public function destroyMatchAttachmentType(int $id)
    {
        if (! Schema::hasTable('bank_recon_match_attachment_types')) {
            return response()->json(['success' => false, 'message' => 'Table missing'], 503);
        }
        $row = DB::table('bank_recon_match_attachment_types')->where('id', $id)->first();
        if (! $row) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        $p = isset($row->sample_file_path) ? (string) $row->sample_file_path : '';
        if ($p !== '' && Str::startsWith($p, 'bank_recon_attachment_type_samples/')) {
            $full = public_path($p);
            if (is_file($full)) {
                @unlink($full);
            }
        }
        DB::table('bank_recon_match_attachment_types')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}