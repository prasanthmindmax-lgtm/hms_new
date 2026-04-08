<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

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

        return view('bank-reconciliation.index', compact('admin', 'bankAccountsEnabled'));
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
        $request->validate($rules);

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
        // If requesting stats only
        if ($request->has('stats_only')) {
            return $this->getStatistics();
        }
        
        $select = [
            'bs.*',
            'matched_user.user_fullname as matched_by_name',
            'matched_user.username as matched_by_username',
            'income_user.user_fullname as income_matched_by_name',
            'income_user.username as income_matched_by_username',
            'bill.bill_number',
            'bill.vendor_name',
            'bill.grand_total_amount as bill_amount',
        ];
        if (Schema::hasColumn('bank_statements', 'radiant_matched_by')) {
            $select[] = 'radiant_user.user_fullname as radiant_matched_by_name';
            $select[] = 'radiant_user.username as radiant_matched_by_username';
        }
        if (Schema::hasColumn('bank_statements', 'bank_account_id') && Schema::hasTable('bank_reconciliation_accounts')) {
            $select[] = 'bra.account_number as bank_account_number';
            $select[] = 'bra.bank_name as bank_account_bank_name';
        }

        $query = DB::table('bank_statements as bs')
            ->leftJoin('users as matched_user', 'bs.matched_by', '=', 'matched_user.id')
            ->leftJoin('users as income_user', 'bs.income_matched_by', '=', 'income_user.id');
        if (Schema::hasColumn('bank_statements', 'radiant_matched_by')) {
            $query->leftJoin('users as radiant_user', 'bs.radiant_matched_by', '=', 'radiant_user.id');
        }
        if (Schema::hasColumn('bank_statements', 'bank_account_id') && Schema::hasTable('bank_reconciliation_accounts')) {
            $query->leftJoin('bank_reconciliation_accounts as bra', 'bs.bank_account_id', '=', 'bra.id');
        }
        $query->leftJoin('bill_tbl as bill', 'bs.matched_bill_id', '=', 'bill.id')
            ->select($select)
            ->orderBy('bs.transaction_date', 'desc')
            ->orderBy('bs.id', 'desc');

        if ($request->filled('bank_account_id') && Schema::hasColumn('bank_statements', 'bank_account_id')) {
            $query->where('bs.bank_account_id', (int) $request->bank_account_id);
        }

        // Apply filters - Date Range
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
        // // Amount Range
        // if ($request->filled('amount_min')) {
        //     $amountMin = (float) str_replace(',', '', $request->amount_min);
        //     $query->where(function($q) use ($amountMin) {
        //         $q->where('bs.withdrawal', '>=', $amountMin)
        //         ->orWhere('bs.deposit', '>=', $amountMin);
        //     });
        // }
        
        // if ($request->filled('amount_max')) {
        //     $amountMax = (float) str_replace(',', '', $request->amount_max);
        //     $query->where(function($q) use ($amountMax) {
        //         $q->where('bs.withdrawal', '<=', $amountMax)
        //           ->orWhere('bs.deposit', '<=', $amountMax);
        //     });
        // }
        // Amount Range
        if ($request->filled('amount_min') || $request->filled('amount_max')) {

            $amountMin = $request->filled('amount_min') 
                ? (float) str_replace(',', '', $request->amount_min) 
                : null;

            $amountMax = $request->filled('amount_max') 
                ? (float) str_replace(',', '', $request->amount_max) 
                : null;
            // dd($amountMin, $amountMax);
            $query->where(function ($q) use ($amountMin, $amountMax) {

                if ($amountMin !== null) {
                    $q->whereRaw(
                        "COALESCE(bs.withdrawal, bs.deposit) >= ?",
                        [$amountMin]
                    );
                }

                if ($amountMax !== null) {
                    $q->whereRaw(
                        "COALESCE(bs.withdrawal, bs.deposit) <= ?",
                        [$amountMax]
                    );
                }

            });
        }

        // Match Status
        if ($request->filled('match_status')) {
            $query->where('bs.match_status', $request->match_status);
        }

        // Income Match Status
        if ($request->filled('income_match')) {
            $val = $request->income_match;
            if ($val === 'income_matched') {
                $query->where('bs.income_match_status', 'income_matched');
            } elseif ($val === 'income_unmatched') {
                $query->where(function ($q) {
                    $q->where('bs.income_match_status', 'income_unmatched')
                      ->orWhereNull('bs.income_match_status');
                });
            }
        }

        // Radiant match filter (pickup linked / keyword-only / unmatched)
        if ($request->filled('radiant_match') && Schema::hasColumn('bank_statements', 'radiant_match_status')) {
            $val = $request->radiant_match;
            if ($val === 'radiant_matched') {
                $query->where('bs.radiant_match_status', 'radiant_matched');
            } elseif ($val === 'radiant_unmatched') {
                $query->where(function ($q) {
                    $q->where('bs.radiant_match_status', 'radiant_unmatched')
                        ->orWhereNull('bs.radiant_match_status');
                });
            } elseif ($val === 'radiant_keyword_only' && Schema::hasColumn('bank_statements', 'radiant_match_against')) {
                $query->whereNotNull('bs.radiant_match_against')
                    ->where('bs.radiant_match_against', '!=', '')
                    ->where(function ($q) {
                        $q->whereNull('bs.radiant_match_status')
                            ->orWhere('bs.radiant_match_status', '!=', 'radiant_matched');
                    });
            }
        }
        
        // Reference Number
        if ($request->filled('reference_number')) {
            $ref = $request->reference_number;
            $query->where(function ($q) use ($ref) {
                $q->where('bs.reference_number', 'LIKE', '%' . $ref . '%')
                    ->orWhere('bs.transaction_id', 'LIKE', '%' . $ref . '%');
            });
        }
        // General Search (Description, Reference, Cheque)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bs.description', 'LIKE', "%{$search}%")
                  ->orWhere('bs.reference_number', 'LIKE', "%{$search}%")
                  ->orWhere('bs.cheque_number', 'LIKE', "%{$search}%");
            });
        }
        
        $perPage = $request->get('per_page', 50);
        $statements = $query->paginate($perPage);
        
        return response()->json($statements);
    }
    
    /**
     * Get statistics
     */
    private function getStatistics()
    {
        $total    = DB::table('bank_statements')->count();
        $matched  = DB::table('bank_statements')->where('match_status', 'matched')->count();
        $unmatched = DB::table('bank_statements')->where('match_status', 'unmatched')->count();

        $totalWithdrawal = DB::table('bank_statements')->sum('withdrawal');
        $totalDeposit    = DB::table('bank_statements')->sum('deposit');

        // Income reconciliation match stats (only if the column exists — safe for older DBs)
        $incomeMatched   = 0;
        $incomeUnmatched = 0;
        if (Schema::hasColumn('bank_statements', 'income_match_status')) {
            $incomeMatched   = DB::table('bank_statements')->where('income_match_status', 'income_matched')->count();
            $incomeUnmatched = DB::table('bank_statements')->where(function ($q) {
                $q->where('income_match_status', 'income_unmatched')
                  ->orWhereNull('income_match_status');
            })->count();
        }

        $radiantMatched   = 0;
        $radiantUnmatched = 0;
        $radiantKeywordOnly = 0;
        if (Schema::hasColumn('bank_statements', 'radiant_match_status')) {
            $radiantMatched = DB::table('bank_statements')->where('radiant_match_status', 'radiant_matched')->count();
            $radiantUnmatched = DB::table('bank_statements')->where(function ($q) {
                $q->where('radiant_match_status', 'radiant_unmatched')
                    ->orWhereNull('radiant_match_status');
            })->count();
            if (Schema::hasColumn('bank_statements', 'radiant_match_against')) {
                $radiantKeywordOnly = DB::table('bank_statements')
                    ->whereNotNull('radiant_match_against')
                    ->where('radiant_match_against', '!=', '')
                    ->where(function ($q) {
                        $q->whereNull('radiant_match_status')
                            ->orWhere('radiant_match_status', '!=', 'radiant_matched');
                    })
                    ->count();
            }
        }

        return response()->json([
            'total'              => $total,
            'matched'            => $matched,
            'unmatched'          => $unmatched,
            'partially_matched'  => $total - $matched - $unmatched,
            'total_amount'       => $totalWithdrawal + $totalDeposit,
            'income_matched'     => $incomeMatched,
            'income_unmatched'   => $incomeUnmatched,
            'radiant_matched'    => $radiantMatched,
            'radiant_unmatched'  => $radiantUnmatched,
            'radiant_keyword_only' => $radiantKeywordOnly,
        ]);
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
            'match_type' => 'required|in:full,partial'
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
            DB::table('bank_statements')
                ->where('id', $statementId)
                ->update([
                    'match_status' => $matchType === 'full' ? 'matched' : 'partially_matched',
                    'matched_bill_id' => $billId,
                    'matched_amount' => $matchedAmount,
                    'matched_date' => now(),
                    'matched_by' => $userId,
                    'notes' => $request->notes,
                    'updated_at' => now()
                ]);

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
                
                // Cancel match record
                DB::table('bank_bill_matches')
                    ->where('id', $match->id)
                    ->update(['status' => 'cancelled']);
            }
            
            DB::table('bank_statements')
                ->where('id', $id)
                ->update([
                    'match_status' => 'unmatched',
                    'matched_bill_id' => null,
                    'matched_amount' => 0,
                    'matched_date' => null,
                    'matched_by' => null,
                    'updated_at' => now()
                ]);
            
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
        $stmt = DB::table('bank_statements as bs')
            ->select(
                'bs.*',
                'matched_user.user_fullname as matched_by_name',
                'matched_user.username as matched_by_username',
                'income_user.user_fullname as income_matched_by_name',
                'income_user.username as income_matched_by_username',
                'bill.bill_number',
                'bill.vendor_name',
                'bill.grand_total_amount as bill_amount'
            )
            ->leftJoin('users as matched_user', 'bs.matched_by', '=', 'matched_user.id')
            ->leftJoin('users as income_user', 'bs.income_matched_by', '=', 'income_user.id')
            ->leftJoin('bill_tbl as bill', 'bs.matched_bill_id', '=', 'bill.id')
            ->where('bs.id', $id)
            ->first();

        if (!$stmt) {
            return response()->json(['success' => false, 'message' => 'Statement not found'], 404);
        }

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
            return response()->json(['data' => []]);
        }
        $rows = DB::table('bank_reconciliation_accounts')
            ->orderBy('bank_name')
            ->orderBy('account_number')
            ->get([
                'id',
                'account_number',
                'bank_name',
                'branch_name',
                'ifsc_code',
                'account_holder_name',
                'notes',
                'updated_at',
            ]);

        return response()->json(['data' => $rows]);
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

        $request->validate([
            'account_number'        => 'required|string|max:64|unique:bank_reconciliation_accounts,account_number',
            'bank_name'             => 'nullable|string|max:191',
            'branch_name'           => 'nullable|string|max:191',
            'ifsc_code'             => 'nullable|string|max:32',
            'account_holder_name'   => 'nullable|string|max:191',
            'notes'                 => 'nullable|string|max:2000',
        ]);

        $id = DB::table('bank_reconciliation_accounts')->insertGetId([
            'account_number'      => trim($request->account_number),
            'bank_name'           => $request->bank_name ? trim($request->bank_name) : null,
            'branch_name'         => $request->branch_name ? trim($request->branch_name) : null,
            'ifsc_code'           => $request->ifsc_code ? trim($request->ifsc_code) : null,
            'account_holder_name' => $request->account_holder_name ? trim($request->account_holder_name) : null,
            'notes'               => $request->notes ? trim($request->notes) : null,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

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

        $request->validate([
            'account_number'        => 'required|string|max:64|unique:bank_reconciliation_accounts,account_number,' . $id,
            'bank_name'             => 'nullable|string|max:191',
            'branch_name'           => 'nullable|string|max:191',
            'ifsc_code'             => 'nullable|string|max:32',
            'account_holder_name'   => 'nullable|string|max:191',
            'notes'                 => 'nullable|string|max:2000',
        ]);

        DB::table('bank_reconciliation_accounts')->where('id', $id)->update([
            'account_number'      => trim($request->account_number),
            'bank_name'           => $request->bank_name ? trim($request->bank_name) : null,
            'branch_name'         => $request->branch_name ? trim($request->branch_name) : null,
            'ifsc_code'           => $request->ifsc_code ? trim($request->ifsc_code) : null,
            'account_holder_name' => $request->account_holder_name ? trim($request->account_holder_name) : null,
            'notes'               => $request->notes ? trim($request->notes) : null,
            'updated_at'          => now(),
        ]);

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
     * Apply income tag: link this bank statement to the income_reconciliation_table row
     * for the chosen branch + date + mode. If no row exists, create one with MOC DOC data.
     *
     * Field mapping (mirrors storeRadiant in IncomeController):
     *   Cash  → collection_amount (MOC cash), deposite_amount (bank deposit), cash_diff
     *   UPI/Card → mespos_upi (MOC upi), mespos_card (MOC card), bank_upi_card (bank), card_upi_diff
     *   NEFT  → bank_neft (bank), neft_others_diff
     *   Other → bank_others (bank), neft_others_diff
     *   radiant_diff = overall moc_total vs bank_total
     */
    public function applyIncomeTag(Request $request)
    {
        $request->validate([
            'bank_statement_id' => 'required|integer',
            'zone'              => 'required|string',
            'branch'            => 'required|string',
            'date'              => 'required|string',
            'mode'              => 'required|in:cash,card,upi,neft,other',
        ]);

        // --- 1. Load the bank statement ---
        $stmt = DB::table('bank_statements')->find($request->bank_statement_id);
        if (!$stmt) {
            return response()->json(['success' => false, 'message' => 'Bank statement not found'], 404);
        }

        // --- 2. Parse date (income_reconciliation_table stores d/m/Y) ---
        try {
            $dateObj = Carbon::createFromFormat('Y-m-d', $request->date);
        } catch (\Exception $e) {
            $dateObj = Carbon::parse($request->date);
        }
        $dateFormatted = $dateObj->format('d/m/Y');

        // --- 3. Mode → bank-ref column names ---
        $mode = $request->mode;
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

        $amount = $stmt->withdrawal > 0 ? (float)$stmt->withdrawal : (float)$stmt->deposit;

        // Format transaction date as d/m/Y (bank statements may store d/M/Y like "25/Mar/2026")
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

        // --- 4. Find existing income_reconciliation_table row for this branch + date ---
        $existing = DB::table('income_reconciliation_table')
            ->where('location_name', $request->branch)
            ->whereRaw("STR_TO_DATE(date_range, '%d/%m/%Y') = STR_TO_DATE(?, '%d/%m/%Y')", [$dateFormatted])
            ->first();

        $incomeReconId = null;

        if ($existing) {
            // ===== UPDATE path =====
            // Read current values so we can recalculate diffs correctly
            $curMocCash    = (float)($existing->moc_cash_amt    ?? 0);
            $curMocCard    = (float)($existing->moc_card_amt    ?? 0);
            $curMocUpi     = (float)($existing->moc_upi_amt     ?? 0);
            $curMocNeft    = (float)($existing->moc_neft_amt    ?? 0);
            $curMocOther   = (float)($existing->moc_other_amt   ?? 0);
            $curMocOverall = (float)($existing->moc_overall_total ?? 0);

            $curCollect  = (float)($existing->collection_amount ?? 0);
            $curDeposite = (float)($existing->deposite_amount   ?? 0);
            $curMesCard  = (float)($existing->mespos_card       ?? 0);
            $curMesUpi   = (float)($existing->mespos_upi        ?? 0);
            $curBankUpi  = (float)($existing->bank_upi_card     ?? 0);
            $curBankNeft = (float)($existing->bank_neft         ?? 0);
            $curBankOth  = (float)($existing->bank_others       ?? 0);

            $updateData = [
                $bankIdCol   => $stmt->id,
                $bankRefCol  => $refNo,
                'updated_at' => now(),
            ];

            if ($mode === 'cash') {
                // Cash bank deposit fills deposite_amount; MOC cash fills collection_amount
                $updateData['date_collection']   = $txnDate;
                $updateData['collection_amount'] = $curMocCash ?: $amount; // prefer MOC cash; fallback to bank amount
                $updateData['date_deposited']    = $txnDate;
                $updateData['deposite_amount']   = $amount;
                $updateData['cash_utr_number']   = $refNo;
                $curDeposite = $amount;
                $curCollect  = $updateData['collection_amount'];

            } elseif ($mode === 'card' || $mode === 'upi') {
                // MOC card+upi → mespos_card / mespos_upi; bank combined → bank_upi_card
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

            // Recalculate all difference fields after updating the relevant values
            $diffs = $this->calcIncomeDiffs(
                $curMocCash, $curMocCard, $curMocUpi, $curMocNeft, $curMocOther, $curMocOverall,
                $curCollect, $curDeposite,
                $curMesCard, $curMesUpi,
                $curBankUpi, $curBankNeft, $curBankOth
            );
            $updateData = array_merge($updateData, $diffs);
            DB::table('income_reconciliation_table')
                ->where('id', $existing->id)
                ->update($updateData);

            $incomeReconId = $existing->id;
            $action        = 'updated';
            $message       = 'Income record updated with bank reference and differences recalculated';

        } else {
            // ===== INSERT path — fetch MOC DOC data first =====
            $mocData = $this->fetchMocDocForBranch($request->branch, $dateObj->format('Ymd'));
            // dd($mocData);
            $mocCash    = (float)($mocData['cash']  ?? 0);
            $mocCard    = (float)($mocData['card']  ?? 0);
            $mocUpi     = (float)($mocData['upi']   ?? 0);
            $mocNeft    = (float)($mocData['neft']  ?? 0);
            $mocOther   = (float)($mocData['other'] ?? 0);
            $mocOverall = array_sum($mocData);

            // Bank amount fields — only the current mode is populated; others are 0
            $collectAmt = 0;
            $depositeAmt = 0;
            $mesposCard = $mocCard; // MOC card always goes into mespos_card
            $mesposUpi  = $mocUpi;  // MOC upi  always goes into mespos_upi
            $bankUpiCard = 0;
            $bankNeft    = 0;
            $bankOthers  = 0;

            $insertData = [
                'zone_name'          => $request->zone,
                'location_name'      => $request->branch,
                'date_range'         => $dateFormatted,

                // MOC DOC totals
                'moc_cash_amt'       => $mocCash,
                'moc_card_amt'       => $mocCard,
                'moc_upi_amt'        => $mocUpi,
                'moc_total_upi_card' => $mocCard + $mocUpi,
                'moc_neft_amt'       => $mocNeft,
                'moc_other_amt'      => $mocOther,
                'moc_overall_total'  => $mocOverall,

                // MESPOS machine amounts = MOC card/upi (same source in bank-recon context)
                'mespos_card'        => $mocCard,
                'mespos_upi'         => $mocUpi,

                // Bank reference
                $bankIdCol  => $stmt->id,
                $bankRefCol => $refNo,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Fill bank amount + date fields per mode
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
            // dd($insertData);
            // Calculate differences
            $diffs = $this->calcIncomeDiffs(
                $mocCash, $mocCard, $mocUpi, $mocNeft, $mocOther, $mocOverall,
                $collectAmt, $depositeAmt,
                $mesposCard, $mesposUpi,
                $bankUpiCard, $bankNeft, $bankOthers
            );
            $insertData = array_merge($insertData, $diffs);
            // dd($insertData);
            $incomeReconId = DB::table('income_reconciliation_table')->insertGetId($insertData);
            $action        = 'created';
            $message       = 'New income record created with MOC DOC data and differences';
        }

        // --- 5. Mark bank_statements row as income-matched ---
        $userId = Auth::id();
        $incomeUpdate = [
            'income_match_status'      => 'income_matched',
            'income_reconciliation_id' => $incomeReconId,
            'income_matched_branch'    => $request->branch,
            'income_matched_date'      => $dateFormatted,
            'income_matched_by'        => $userId,
            'income_matched_at'        => now(),
            'updated_at'               => now(),
        ];
        $safeUpdate = [];
        foreach ($incomeUpdate as $col => $val) {
            if (Schema::hasColumn('bank_statements', $col)) {
                $safeUpdate[$col] = $val;
            }
        }
        if (!empty($safeUpdate)) {
            DB::table('bank_statements')->where('id', $stmt->id)->update($safeUpdate);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'action'  => $action,
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

            // --- Find the linked income reconciliation row ---
            $reconId = $stmt->income_reconciliation_id ?? null;
            $recon   = $reconId ? DB::table('income_reconciliation_table')->find($reconId) : null;

            if ($recon) {
                // Determine which bank-ref columns this statement owns and clear them
                $clearData = ['updated_at' => now()];

                if ((int)($recon->cash_bank_id ?? 0) === (int)$id) {
                    $clearData['cash_bank_id']        = null;
                    $clearData['cash_bank_ref_no']    = null;
                    $clearData['collection_amount']   = 0;
                    $clearData['deposite_amount']     = 0;
                    $clearData['date_collection']     = null;
                    $clearData['date_deposited']      = null;
                }
                if ((int)($recon->card_upi_bank_id ?? 0) === (int)$id) {
                    $clearData['card_upi_bank_id']    = null;
                    $clearData['card_upi_bank_ref_no']= null;
                    $clearData['bank_upi_card']       = 0;
                    $clearData['mespos_card']         = 0;
                    $clearData['mespos_upi']          = 0;
                    $clearData['date_settlement']     = null;
                }
                if ((int)($recon->neft_bank_id ?? 0) === (int)$id) {
                    $clearData['neft_bank_id']        = null;
                    $clearData['neft_bank_ref_no']    = null;
                    $clearData['bank_neft']           = 0;
                }
                if ((int)($recon->other_bank_id ?? 0) === (int)$id) {
                    $clearData['other_bank_id']       = null;
                    $clearData['other_bank_ref_no']   = null;
                    $clearData['bank_others']         = 0;
                }

                // Merge cleared values with existing to recalculate diffs
                $merged = array_merge((array)$recon, $clearData);

                $diffs = $this->calcIncomeDiffs(
                    (float)($merged['moc_cash_amt']    ?? 0),
                    (float)($merged['moc_card_amt']    ?? 0),
                    (float)($merged['moc_upi_amt']     ?? 0),
                    (float)($merged['moc_neft_amt']    ?? 0),
                    (float)($merged['moc_other_amt']   ?? 0),
                    (float)($merged['moc_overall_total']?? 0),
                    (float)($merged['collection_amount']?? 0),
                    (float)($merged['deposite_amount'] ?? 0),
                    (float)($merged['mespos_card']     ?? 0),
                    (float)($merged['mespos_upi']      ?? 0),
                    (float)($merged['bank_upi_card']   ?? 0),
                    (float)($merged['bank_neft']       ?? 0),
                    (float)($merged['bank_others']     ?? 0)
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
            $safeReset = [];
            foreach ($resetUpdate as $col => $val) {
                if (Schema::hasColumn('bank_statements', $col)) {
                    $safeReset[$col] = $val;
                }
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
     */
    private function fetchMocDocForBranch(string $branchName, string $dateYmd): array
    {
        $totals = ['cash' => 0.0, 'card' => 0.0, 'upi' => 0.0, 'neft' => 0.0, 'other' => 0.0];

        $cityArray   = $this->incomeCityArray();
        $locationKey = array_search($branchName, $cityArray);

        if ($locationKey === false) {
            \Log::warning("fetchMocDocForBranch: branch '{$branchName}' not found in cityArray");
            return $totals;
        }

        // Match exact format used by SuperAdminController::postCurlApi for the billing list API
        $url         = 'https://mocdoc.in/api/get/billlist/draravinds-ivf';
        $postFields  = "date={$dateYmd}&entitylocation={$locationKey}";
        $headFields  = [
            'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
            'Date: Fri, 07 Mar 2025 10:07:52 GMT',
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: SRV=s1',
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postFields,
            CURLOPT_HTTPHEADER     => $headFields,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $response = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response || $httpCode !== 200) {
            \Log::warning("fetchMocDocForBranch: API returned HTTP {$httpCode} for branch '{$branchName}' date {$dateYmd}");
            return $totals;
        }

        $data = json_decode($response, true);

        // API returns individual billing records (same as incomereportAPI / saveCurlData)
        // Each record has 'paymenttype' (Cash/Card/UPI/Neft/...) and 'amt'
        if (!empty($data['billinglist']) && is_array($data['billinglist'])) {
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
        } else {
            \Log::info("fetchMocDocForBranch: no billinglist for branch '{$branchName}' date {$dateYmd}", [
                'locationKey' => $locationKey,
                'response'    => $data,
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
}