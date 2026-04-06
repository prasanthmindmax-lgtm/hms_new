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
        
        return view('bank-reconciliation.index', compact('admin'));
    }
    
    /**
     * Upload and process Excel file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240'
        ]);
        
        try {
            $file = $request->file('excel_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->move(public_path('bank_statements'), $fileName);
            
            // Load Excel file
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Generate batch ID
            $batchId = uniqid('BATCH_');
            $userId = Auth::id();
            $insertedCount = 0;
            
            // Skip header row and process data
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header
                // Skip empty rows
                if (empty($row[0]) && empty($row[2])) continue;
                // dd($row);
                // Map Excel columns (adjust based on your Excel structure)
                $transactionDate = $row[3] ?? '';
                $transactionId = $row[1] ?? '';
                $valueDate = $row[2] ?? '';
                $transaction_posted_date = $row[4] ?? '';
                $description = $row[6] ?? '';
                $chequeNumber = $row[5] ?? '';
                $withdrawal = $this->parseAmount($row[7] ?? 0);
                $deposit = $this->parseAmount($row[8] ?? 0);
                $balance = $this->parseAmount($row[9] ?? 0);
                // Generate reference number from description or cheque
                $referenceNumber = $this->extractReference($description, $chequeNumber);
                
                // Insert into database
                DB::table('bank_statements')->insert([
                    'user_id' => $userId,
                    'upload_batch_id' => $batchId,
                    'file_name' => $fileName,
                    'transaction_date' => $transactionDate,
                    'transaction_id' => $transactionId,
                    'transaction_posted_date' => $transaction_posted_date,
                    'value_date' => $valueDate,
                    'description' => $description,
                    'reference_number' => $referenceNumber,
                    'cheque_number' => $chequeNumber,
                    'withdrawal' => $withdrawal,
                    'deposit' => $deposit,
                    'balance' => $balance,
                    'category' => 'uncategory',
                    'match_status' => 'unmatched',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $insertedCount++;
            }
            
            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$insertedCount} transactions",
                'batch_id' => $batchId,
                'count' => $insertedCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
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
        
        $query = DB::table('bank_statements as bs')
            ->select(
                'bs.*',
                'matched_user.user_fullname as matched_by_name',
                'matched_user.username as matched_by_username',
                'bill.bill_number',
                'bill.vendor_name',
                'bill.grand_total_amount as bill_amount'
            )
            ->leftJoin('users as matched_user', 'bs.matched_by', '=', 'matched_user.id')
            ->leftJoin('bill_tbl as bill', 'bs.matched_bill_id', '=', 'bill.id')
            ->orderBy('bs.transaction_date', 'desc')
            ->orderBy('bs.id', 'desc');
        
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
        
        // Reference Number
        if ($request->filled('reference_number')) {
            $query->where('bs.reference_number', 'LIKE', '%' . $request->reference_number . '%');
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
        $total = DB::table('bank_statements')->count();
        $matched = DB::table('bank_statements')->where('match_status', 'matched')->count();
        $unmatched = DB::table('bank_statements')->where('match_status', 'unmatched')->count();
        
        $totalWithdrawal = DB::table('bank_statements')->sum('withdrawal');
        $totalDeposit = DB::table('bank_statements')->sum('deposit');
        
        return response()->json([
            'total' => $total,
            'matched' => $matched,
            'unmatched' => $unmatched,
            'partially_matched' => $total - $matched - $unmatched,
            'total_amount' => $totalWithdrawal + $totalDeposit
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

            // Check if this bill_id already has a bill made (bill_pay) record from Bank Reconciliation – then update it
            $existingLine = DB::table('bill_pay_lines_tbl as l')
                ->join('bill_pay_tbl as p', 'p.id', '=', 'l.bill_pay_id')
                ->where('l.bill_id', $billId)
                ->where('p.paid_through', 'Bank Reconciliation')
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

        $existingNeft = DB::table($neftTable)->where('bill_pay_id', $billPayId)->first();

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
            'created_by' => $userId,
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
}