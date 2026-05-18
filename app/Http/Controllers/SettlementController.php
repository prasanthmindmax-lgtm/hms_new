<?php

namespace App\Http\Controllers;

use App\Models\SettlementUpload;
use App\Models\SettlementAccount;
use App\Models\SettlementTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
class SettlementController extends Controller
{
    // ─── Views ─────────────────────────────────────────────────────────

    public function index(): \Illuminate\View\View
    {
        $admin = auth()->user();
        $zones = $this->listMasterZones();
        $bankPosLinking = $this->settlementBankPosLinkingAvailable();

        return view('settlement.index', compact('admin', 'zones', 'bankPosLinking'));
    }

    public function uploads(): \Illuminate\View\View
    {
        $admin=auth()->user();
        return view('settlement.uploads', compact('admin'));
    }

    // ─── Upload & Process ──────────────────────────────────────────────

    public function upload(Request $request): JsonResponse
    {
        // Ensure validation errors return JSON (not redirect) for AJAX calls
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['xlsx', 'xls'])) {
                        $fail('Only .xlsx and .xls files are allowed.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $file = $request->file('file');
        $this->ensureSettlementStorageDirectory();

        $stored = Str::uuid().'.'.$file->getClientOriginalExtension();
        $path   = $file->storeAs('settlement_reports', $stored, 'local');
        if ($path === false) {
            return response()->json([
                'success' => false,
                'message' => 'Could not save file. Check storage/app/settlement_reports permissions.',
            ], 500);
        }

        $user = Auth::user();

        $upload = SettlementUpload::create([
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename'   => $stored,
            'file_path'         => $path,
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
            'status'            => 'processing',
            'uploaded_by'       => $user?->id,
            'uploaded_by_name'  => $user?->user_fullname,
            'uploaded_by_email' => $user?->email,
            'uploaded_by_username' => $user?->username,
            'uploaded_ip'       => $request->ip(),
            'upload_user_agent' => Str::limit((string) $request->userAgent(), 512, ''),
        ]);

        try {
            $this->processExcel($upload, storage_path('app/' . $path));
        } catch (\Throwable $e) {
            $upload->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Processing failed: ' . $e->getMessage()], 422);
        }

        $fresh = $upload->fresh();
        $skipped = (int) ($fresh->duplicate_accounts_skipped ?? 0);
        $message = 'File uploaded and processed successfully.';
        if ($skipped > 0) {
            $message .= " {$skipped} duplicate account(s) (same MID + date) were skipped.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'upload'  => $this->formatUpload($fresh),
        ]);
    }

    private function ensureSettlementStorageDirectory(): void
    {
        Storage::disk('local')->makeDirectory('settlement_reports');
    }

    /**
     * Same MID + transaction date (+ settlement date when present) already exists → duplicate.
     */
    private function settlementAccountAlreadyExists(string $mid, ?string $transactionDate, ?string $settlementDate): bool
    {
        $q = SettlementAccount::query()->where('mid', $mid);

        if ($transactionDate) {
            $q->whereDate('transaction_date', $transactionDate);
        } else {
            $q->whereNull('transaction_date');
        }

        if ($settlementDate) {
            $q->whereDate('settlement_date', $settlementDate);
        } else {
            $q->whereNull('settlement_date');
        }

        return $q->exists();
    }

    private function processExcel(SettlementUpload $upload, string $filePath): void
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true);

        // Map header row
        $header = array_shift($rows);
        $map    = $this->buildColumnMap($header);

        $grouped = [];
        foreach ($rows as $row) {
            if (empty(array_filter($row))) continue;

            $mid = trim((string) ($row[$map['MID']] ?? ''));
            if (!$mid) continue;

            if (!isset($grouped[$mid])) {
                $grouped[$mid] = [
                    'rows'         => [],
                    'mid'          => $mid,
                    'tid'          => trim((string) ($row[$map['TID']] ?? '')),
                    'merchant'     => trim((string) ($row[$map['Merchant Name']] ?? '')),
                    'trading'      => trim((string) ($row[$map['Trading Name / DBA']] ?? '')),
                    'tx_date'      => $this->parseDate($row[$map['Transaction Date']] ?? ''),
                    'settle_date'  => $this->parseDate($row[$map['Settlement Date']] ?? ''),
                    'currency'     => trim((string) ($row[$map['Currency']] ?? 'INR')),
                ];
            }
            $grouped[$mid]['rows'][] = $row;
        }

        $totalTx       = 0;
        $totalNet      = 0;
        $skippedDupes  = 0;
        $insertedAccts = 0;

        DB::transaction(function () use ($upload, $grouped, $map, &$totalTx, &$totalNet, &$skippedDupes, &$insertedAccts) {
            foreach ($grouped as $mid => $group) {
                if ($this->settlementAccountAlreadyExists($mid, $group['tx_date'], $group['settle_date'])) {
                    $skippedDupes++;

                    continue;
                }

                $sumTx  = 0;
                $sumChg = 0;
                $sumTax = 0;
                $sumNet = 0;

                foreach ($group['rows'] as $row) {
                    $sumTx  += (float) ($row[$map['Transaction Amount']] ?? 0);
                    $sumChg += (float) ($row[$map['Charges']] ?? 0);
                    $sumTax += (float) ($row[$map['Taxes']] ?? 0);
                    $sumNet += (float) ($row[$map['Net Settlement Amount']] ?? 0);
                }

                $account = SettlementAccount::create([
                    'upload_id'                    => $upload->id,
                    'mid'                          => $mid,
                    'tid'                          => $group['tid'],
                    'merchant_name'                => $group['merchant'],
                    'trading_name'                 => $group['trading'],
                    'branch'                       => $group['merchant'],
                    'transaction_count'            => count($group['rows']),
                    'total_transaction_amount'     => $sumTx,
                    'total_charges'                => $sumChg,
                    'total_taxes'                  => $sumTax,
                    'total_net_settlement_amount'  => $sumNet,
                    'transaction_date'             => $group['tx_date'],
                    'settlement_date'              => $group['settle_date'],
                    'currency'                     => $group['currency'],
                ]);

                $txInsert = [];
                foreach ($group['rows'] as $row) {
                    $txInsert[] = [
                        'upload_id'            => $upload->id,
                        'account_id'           => $account->id,
                        'mid'                  => $mid,
                        'tid'                  => trim((string)($row[$map['TID']] ?? '')),
                        'merchant_name'        => trim((string)($row[$map['Merchant Name']] ?? '')),
                        'trading_name'         => trim((string)($row[$map['Trading Name / DBA']] ?? '')),
                        'transaction_date'     => $this->parseDate($row[$map['Transaction Date']] ?? ''),
                        'currency'             => trim((string)($row[$map['Currency']] ?? 'INR')),
                        'mode_of_payment'      => trim((string)($row[$map['Mode Of Payment']] ?? '')),
                        'card_number'          => trim((string)($row[$map['Card Number']] ?? '')),
                        'card_scheme'          => trim((string)($row[$map['Card Scheme']] ?? '')),
                        'card_program'         => trim((string)($row[$map['Card Program']] ?? '')),
                        'card_type'            => trim((string)($row[$map['Card Type']] ?? '')),
                        'card_category'        => trim((string)($row[$map['Card Category']] ?? '')),
                        'transaction_id'       => trim((string)($row[$map['Transaction ID']] ?? '')),
                        'invoice_number'       => trim((string)($row[$map['Invoice Number']] ?? '')),
                        'batch_number'         => trim((string)($row[$map['Batch Number']] ?? '')),
                        'rrn'                  => trim((string)($row[$map['RRN']] ?? '')),
                        'arn'                  => trim((string)($row[$map['ARN']] ?? '')),
                        'transaction_type'     => trim((string)($row[$map['Transaction Type']] ?? '')),
                        'transaction_amount'   => (float)($row[$map['Transaction Amount']] ?? 0),
                        'charges'              => (float)($row[$map['Charges']] ?? 0),
                        'taxes'                => (float)($row[$map['Taxes']] ?? 0),
                        'net_settlement_amount'=> (float)($row[$map['Net Settlement Amount']] ?? 0),
                        'auth_code'            => trim((string)($row[$map['Auth Code']] ?? '')),
                        'utr_reference'        => trim((string)($row[$map['UTR/ Transaction Reference No.'] ?? ''] ?? '')),
                        'transaction_datetime' => trim((string)($row[$map['Transaction Date and Time']] ?? '')),
                        'settlement_date'      => $this->parseDate($row[$map['Settlement Date']] ?? ''),
                        'status'               => trim((string)($row[$map['Status']] ?? 'SETTLED')),
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ];
                }
                SettlementTransaction::insert($txInsert);

                $totalTx  += $sumTx;
                $totalNet += $sumNet;
                $insertedAccts++;
            }
        });

        $totalRows = 0;
        foreach ($grouped as $g) {
            $totalRows += count($g['rows']);
        }

        $completed = [
            'status'                      => 'completed',
            'total_rows'                  => $totalRows,
            'total_accounts'              => $insertedAccts,
            'total_transaction_amount'    => $totalTx,
            'total_net_settlement_amount' => $totalNet,
        ];
        if (Schema::hasColumn('settlement_uploads', 'duplicate_accounts_skipped')) {
            $completed['duplicate_accounts_skipped'] = $skippedDupes;
        }
        $upload->update($completed);
    }

    private function buildColumnMap(array $header): array
    {
        $map = [];
        foreach ($header as $col => $name) {
            if ($name !== null) $map[trim((string)$name)] = $col;
        }
        return $map;
    }

    private function parseDate(mixed $value): ?string
    {
        if (!$value) return null;
        $str = trim((string)$value);
        if (!$str) return null;

        // Try various formats
        $formats = ['d-m-Y', 'd-M-y', 'd-M-Y', 'Y-m-d', 'd/m/Y'];
        foreach ($formats as $fmt) {
            $d = \DateTime::createFromFormat($fmt, $str);
            if ($d) return $d->format('Y-m-d');
        }
        try {
            return (new \DateTime($str))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    // ─── Data APIs (AJAX) ─────────────────────────────────────────────

    /**
     * Bank reconciliation POS link is available when bank_statements has the POS columns.
     */
    private function settlementBankPosLinkingAvailable(): bool
    {
        return Schema::hasTable('bank_statements')
            && Schema::hasColumn('bank_statements', 'pos_settlement_account_id')
            && Schema::hasColumn('bank_statements', 'pos_match_status');
    }

    /**
     * Base query for settlement_accounts list (filters only; no selectRaw / order / paginate).
     *
     * @param  ''|'tagged'|'untagged'  $bankPosTag
     */
    private function settlementAccountsFilteredQuery(Request $request, string $bankPosTag): \Illuminate\Database\Eloquent\Builder
    {
        $uploadIds = array_filter((array) $request->input('upload_ids', []));
        $mids = array_filter((array) $request->input('mids', []));
        $branches = array_filter((array) $request->input('branches', []));
        $zoneId = (int) $request->input('zone_id', 0);
        $branchId = (int) $request->input('branch_id', 0);
        $posTaggedBy = array_values(array_filter(array_map('intval', (array) $request->input('pos_tagged_by', []))));
        $hasPos = $this->settlementBankPosLinkingAvailable();
        if (! in_array($bankPosTag, ['tagged', 'untagged'], true)) {
            $bankPosTag = '';
        }

        $q = SettlementAccount::query()
            ->with('upload')
            ->when(count($uploadIds), fn ($qq) => $qq->whereIn('upload_id', $uploadIds))
            ->when(count($mids), fn ($qq) => $qq->whereIn('mid', $mids))
            ->when(count($branches), fn ($qq) => $qq->whereIn('merchant_name', $branches))
            ->when($request->filled('date_from'), fn ($qq) => $qq->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($qq) => $qq->whereDate('transaction_date', '<=', $request->date_to));

        if ($zoneId > 0 || $branchId > 0) {
            $this->applySettlementLocationFilter($q, $zoneId > 0 ? $zoneId : null, $branchId > 0 ? $branchId : null);
        }

        if ($hasPos && count($posTaggedBy) > 0 && Schema::hasColumn('bank_statements', 'pos_matched_by')) {
            $q->whereExists(function ($sub) use ($posTaggedBy) {
                $sub->select(DB::raw('1'))
                    ->from('bank_statements')
                    ->whereColumn('bank_statements.pos_settlement_account_id', 'settlement_accounts.id')
                    ->where('bank_statements.pos_match_status', 'pos_matched')
                    ->whereIn('bank_statements.pos_matched_by', $posTaggedBy);
            });
        }

        if ($hasPos && $bankPosTag === 'tagged') {
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw('1'))
                    ->from('bank_statements')
                    ->whereColumn('bank_statements.pos_settlement_account_id', 'settlement_accounts.id')
                    ->where('bank_statements.pos_match_status', 'pos_matched');
            });
        } elseif ($hasPos && $bankPosTag === 'untagged') {
            $q->whereNotExists(function ($sub) {
                $sub->select(DB::raw('1'))
                    ->from('bank_statements')
                    ->whereColumn('bank_statements.pos_settlement_account_id', 'settlement_accounts.id')
                    ->where('bank_statements.pos_match_status', 'pos_matched');
            });
        }

        return $q;
    }

    public function getAccounts(Request $request): JsonResponse
    {
        $bankPosTag = strtolower((string) $request->input('bank_pos_tag', ''));
        if (! in_array($bankPosTag, ['tagged', 'untagged'], true)) {
            $bankPosTag = '';
        }

        $hasPos = $this->settlementBankPosLinkingAvailable();
        $q = $this->settlementAccountsFilteredQuery($request, $bankPosTag);

        if ($hasPos) {
            $userJoin = Schema::hasTable('users') && Schema::hasColumn('bank_statements', 'pos_matched_by');
            $hasAt = Schema::hasColumn('bank_statements', 'pos_matched_at');
            $matchedSub = '(select bs.id from bank_statements bs where bs.pos_settlement_account_id = settlement_accounts.id '
                .'and bs.pos_match_status = ? order by '.($hasAt ? 'bs.pos_matched_at desc, ' : '').'bs.id desc limit 1)';
            $byNameSub = $userJoin
                ? '(select u.user_fullname from bank_statements bs left join users u on u.id = bs.pos_matched_by '
                .'where bs.pos_settlement_account_id = settlement_accounts.id and bs.pos_match_status = ? '
                .'order by '.($hasAt ? 'bs.pos_matched_at desc, ' : '').'bs.id desc limit 1)'
                : 'null';
            $byUserSub = $userJoin
                ? '(select u.username from bank_statements bs left join users u on u.id = bs.pos_matched_by '
                .'where bs.pos_settlement_account_id = settlement_accounts.id and bs.pos_match_status = ? '
                .'order by '.($hasAt ? 'bs.pos_matched_at desc, ' : '').'bs.id desc limit 1)'
                : 'null';
            $atSub = $hasAt
                ? '(select bs.pos_matched_at from bank_statements bs where bs.pos_settlement_account_id = settlement_accounts.id '
                .'and bs.pos_match_status = ? order by bs.pos_matched_at desc, bs.id desc limit 1)'
                : 'null';
            $statusSub = '(select bs.pos_match_status from bank_statements bs where bs.pos_settlement_account_id = settlement_accounts.id '
                .'and bs.pos_match_status = ? order by '.($hasAt ? 'bs.pos_matched_at desc, ' : '').'bs.id desc limit 1)';

            $bindings = ['pos_matched', 'pos_matched'];
            $extra = ', '.$matchedSub.' as bank_pos_first_statement_id';
            if ($userJoin) {
                $extra .= ', '.$byNameSub.' as bank_pos_matched_by_name';
                $extra .= ', '.$byUserSub.' as bank_pos_matched_by_username';
                $bindings[] = 'pos_matched';
                $bindings[] = 'pos_matched';
            }
            if ($hasAt) {
                $extra .= ', '.$atSub.' as bank_pos_matched_at_raw';
                $bindings[] = 'pos_matched';
            }
            $extra .= ', '.$statusSub.' as bank_pos_match_status_raw';
            $bindings[] = 'pos_matched';

            $q->selectRaw(
                'settlement_accounts.*, '
                .'(select count(*) from bank_statements where bank_statements.pos_settlement_account_id = settlement_accounts.id '
                .'and bank_statements.pos_match_status = ?) as bank_pos_link_count '
                .$extra,
                $bindings
            );
        }

        $perPage = max(1, min(100, (int) ($request->per_page ?? 15)));

        $summaryBase = $this->settlementAccountsFilteredQuery($request, '');
        $totalAccounts = (clone $summaryBase)->count();
        $summary = [
            'total_transaction_amount' => (float) (clone $summaryBase)->sum('total_transaction_amount'),
            'total_net_settlement_amount' => (float) (clone $summaryBase)->sum('total_net_settlement_amount'),
            'total_accounts' => $totalAccounts,
        ];
        if ($hasPos) {
            $tagged = (clone $summaryBase)->whereExists(function ($sub) {
                $sub->select(DB::raw('1'))
                    ->from('bank_statements')
                    ->whereColumn('bank_statements.pos_settlement_account_id', 'settlement_accounts.id')
                    ->where('bank_statements.pos_match_status', 'pos_matched');
            })->count();
            $summary['bank_pos_tagged'] = $tagged;
            $summary['bank_pos_untagged'] = max(0, $totalAccounts - $tagged);
        } else {
            $summary['bank_pos_tagged'] = null;
            $summary['bank_pos_untagged'] = null;
        }

        $data = (clone $q)->orderByDesc('settlement_accounts.created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $data->map(fn ($a) => $this->formatAccount($a, $hasPos)),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
            'summary' => $summary,
            'bank_pos_linking' => $hasPos,
        ]);
    }

    public function getUploads(Request $request): JsonResponse
    {
        $perPage = (int) ($request->per_page ?? 10);
        $data    = SettlementUpload::orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $data->map(fn($u) => $this->formatUpload($u)),
            'meta'    => [
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
                'from'         => $data->firstItem(),
                'to'           => $data->lastItem(),
            ],
        ]);
    }

    public function getFilterOptions(Request $request): JsonResponse
    {
        $mids = SettlementAccount::select('mid', 'merchant_name')
            ->distinct()
            ->orderBy('mid')
            ->get()
            ->map(fn ($a) => [
                'mid' => $a->mid,
                'merchant_name' => $a->merchant_name,
                'label' => trim($a->mid.' — '.($a->merchant_name ?? '')),
            ]);

        $branches = SettlementAccount::select('merchant_name')
            ->distinct()
            ->orderBy('merchant_name')
            ->pluck('merchant_name')
            ->filter()
            ->values();

        $uploads = SettlementUpload::select('id', 'original_filename', 'created_at')
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($u) => ['id' => $u->id, 'label' => $u->original_filename . ' (' . $u->created_at->format('d M Y') . ')']);

        $dates = SettlementAccount::select('transaction_date')
            ->distinct()
            ->orderBy('transaction_date')
            ->pluck('transaction_date')
            ->filter()
            ->map(fn($d) => $d instanceof \Carbon\Carbon ? $d->format('Y-m-d') : (string)$d)
            ->values();

        $zoneId = (int) $request->input('zone_id', 0);

        return response()->json([
            'success' => true,
            'mids' => $mids,
            'branches' => $branches,
            'uploads' => $uploads,
            'dates' => $dates,
            'zones' => $this->listMasterZones(),
            'location_branches' => $this->listMasterBranches($zoneId > 0 ? $zoneId : null),
            'pos_taggers' => $this->listBankReconTaggers(),
            'bank_pos_linking' => $this->settlementBankPosLinkingAvailable(),
        ]);
    }

    /**
     * @return list<array{id: int, name: string}>
     */
    private function listMasterZones(): array
    {
        if (! Schema::hasTable('tblzones')) {
            return [];
        }

        return DB::table('tblzones')
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(static fn ($z) => ['id' => (int) $z->id, 'name' => (string) $z->name])
            ->values()
            ->all();
    }

    /**
     * @return list<array{id: int, name: string, zone_id: int}>
     */
    private function listMasterBranches(?int $zoneId = null): array
    {
        if (! Schema::hasTable('tbl_locations')) {
            return [];
        }

        $q = DB::table('tbl_locations')->select('id', 'name', 'zone_id')->orderBy('name')->limit(5000);
        if ($zoneId !== null && $zoneId > 0) {
            $q->where('zone_id', $zoneId);
        }

        return $q->get()
            ->map(static fn ($b) => [
                'id' => (int) $b->id,
                'name' => (string) $b->name,
                'zone_id' => (int) $b->zone_id,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{id: int, name: string, username: string|null}>
     */
    private function listBankReconTaggers(): array
    {
        if (! Schema::hasTable('bank_statements') || ! Schema::hasTable('users')) {
            return [];
        }

        $ids = collect();
        foreach (['pos_matched_by', 'radiant_matched_by', 'income_matched_by', 'matched_by'] as $col) {
            if (Schema::hasColumn('bank_statements', $col)) {
                $ids = $ids->merge(
                    DB::table('bank_statements')->whereNotNull($col)->distinct()->pluck($col)
                );
            }
        }

        $uniqueIds = $ids->unique()->filter()->values();
        if ($uniqueIds->isEmpty()) {
            return [];
        }

        return DB::table('users')
            ->whereIn('id', $uniqueIds)
            ->orderBy('user_fullname')
            ->orderBy('username')
            ->get(['id', 'user_fullname', 'username'])
            ->map(function ($u) {
                $name = trim((string) ($u->user_fullname ?? ''));
                if ($name === '') {
                    $name = (string) (($u->username ?? '') !== '' ? $u->username : 'User #'.$u->id);
                }

                return [
                    'id' => (int) $u->id,
                    'name' => $name,
                    'username' => $u->username,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Zone / branch filter: POS MID/TID embed branch code (last 5–6 digits), same as
     * BankStatementController::incomeTagResolveDescription MESPOS / settlementAccountsForBankPos.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\SettlementAccount>  $query
     */
    private function applySettlementLocationFilter($query, ?int $zoneId, ?int $branchId): void
    {
        $locationIds = $this->resolveTargetLocationIds($zoneId, $branchId);
        if ($locationIds === []) {
            if ((int) ($zoneId ?? 0) > 0 || (int) ($branchId ?? 0) > 0) {
                $query->whereRaw('1 = 0');
            }

            return;
        }

        $locs = DB::table('tbl_locations')
            ->whereIn('id', $locationIds)
            ->select('id', 'name', 'zone_id')
            ->orderBy('name')
            ->get();

        $midKeys = $this->posMidMatchKeysForLocationIds($locationIds);
        $bankSuffixes = $this->bankAccountSuffixesForLocations($locs);
        $midKeys = array_values(array_unique(array_merge($midKeys, $bankSuffixes)));

        $query->where(function ($outer) use ($locs, $midKeys) {
            $matched = false;

            if (count($midKeys) > 0) {
                $matched = true;
                $outer->orWhere(function ($w) use ($midKeys) {
                    foreach ($midKeys as $key) {
                        $key = (string) $key;
                        if (strlen($key) < 4) {
                            continue;
                        }
                        $w->orWhere('mid', 'like', '%'.$key.'%')
                            ->orWhere('tid', 'like', '%'.$key.'%');
                    }
                });
            }

            foreach ($locs as $loc) {
                $name = trim((string) ($loc->name ?? ''));
                if ($name === '') {
                    continue;
                }
                $tokens = $this->branchMatchTokens($name);
                $matched = true;
                $outer->orWhere(function ($w) use ($name, $tokens) {
                    foreach (['merchant_name', 'trading_name', 'branch'] as $col) {
                        $w->orWhere(function ($qq) use ($col, $name, $tokens) {
                            $qq->whereRaw('LOWER(TRIM(IFNULL('.$col.', ""))) = LOWER(?)', [$name]);
                            foreach ($tokens as $tok) {
                                if ($tok === '') {
                                    continue;
                                }
                                $pat = '%'.addcslashes($tok, '%_\\').'%';
                                $qq->orWhereRaw('LOWER('.$col.') LIKE LOWER(?)', [$pat]);
                            }
                        });
                    }
                });
            }

            if (! $matched) {
                $outer->whereRaw('1 = 0');
            }
        });
    }

    /**
     * MOC / MESPOS branch suffix (last 6 digits of MID) → tbl_locations.id.
     * Keep in sync with BankStatementController::incomeTagResolveDescription $mocCodeMap.
     *
     * @return array<string, int>
     */
    private function posMocSuffixToLocationMap(): array
    {
        return [
            '118961' => 23,
            '118991' => 37,
            '118992' => 43,
            '118988' => 27,
            '118958' => 30,
            '119008' => 10,
            '119001' => 11,
            '119109' => 8,
            '110737' => 7,
            '119064' => 18,
            '119101' => 33,
            '119067' => 19,
            '119088' => 22,
            '118824' => 35,
            '118815' => 9,
            '118825' => 5,
            '118829' => 28,
            '118828' => 50,
            '118843' => 51,
            '177476' => 46,
            '119040' => 20,
            '119051' => 21,
            '119033' => 25,
            '277463' => 48,
            '288923' => 45,
            '118921' => 17,
            '118938' => 4,
            '118877' => 1,
            '118888' => 3,
            '118909' => 2,
            '118918' => 24,
            '123561' => 47,
            '123558' => 41,
            '118955' => 39,
            '358644' => 58,
            '358635' => 29,
        ];
    }

    /**
     * Short branch codes (often 5 digits) from corporate card / CC master — matched to tbl_locations by name.
     *
     * @return array<string, string> branch label => digit code
     */
    private function posBranchShortCodeMap(): array
    {
        return [
            'Aathur' => '11571',
            'Bengaluru - Dasarahalli' => '10592',
            'Bengaluru - Electronic City' => '10592',
            'Bengaluru - Hebbal' => '12246',
            'Bengaluru - Konanakunte' => '10592',
            'Chengalpattu' => '11102',
            'Chennai - Madipakkam' => '12093',
            'Chennai - Urapakkam' => '11972',
            'Chennai - Sholinganallur' => '11727',
            'Chennai - Tambaram' => '30006',
            'Chennai - Vadapalani' => '12093',
            'Coimbatore - Ganapathy' => '11605',
            'Coimbatore - Sundarapuram' => '11308',
            'Coimbatore - Thudiyalur' => '10055',
            'Erode' => '10969',
            'Harur' => '10965',
            'Hosur' => '11965',
            'Kallakurichi' => '11159',
            'Kanchipuram' => '10395',
            'Madurai' => '11710',
            'Namakal' => '11891',
            'Pennagaram' => '11882',
            'Pollachi' => '11310',
            'Salem' => '10737',
            'Thirupathur' => '10154',
            'Thiruvallur' => '12082',
            'Tirupati' => '12215',
            'Tiruppur' => '10302',
            'Trichy' => '10101',
            'Vellore' => '12215',
        ];
    }

    /**
     * Resolve tbl_locations.id from a POS MID/TID (last 5–6 numeric suffix), like income tagging.
     */
    private function resolveLocationIdFromPosMid(string $midOrTid): ?int
    {
        $digits = preg_replace('/\D/', '', $midOrTid);
        if ($digits === '' || strlen($digits) < 5) {
            return null;
        }

        $map6 = $this->posMocSuffixToLocationMap();
        $s6 = substr($digits, -6);
        if (isset($map6[$s6])) {
            return $map6[$s6];
        }

        $s5 = substr($digits, -5);
        foreach ($this->posFiveDigitCodeToLocationMap() as $code => $locId) {
            if ($code === $s5 || str_ends_with($s6, $code)) {
                return $locId;
            }
        }

        return null;
    }

    /**
     * @return array<string, int> 5-digit (or 6-digit padded) code => location id
     */
    private function posFiveDigitCodeToLocationMap(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $cache = [];
        if (! Schema::hasTable('tbl_locations')) {
            return $cache;
        }

        foreach ($this->posBranchShortCodeMap() as $label => $code) {
            $code = preg_replace('/\D/', '', (string) $code);
            if ($code === '') {
                continue;
            }

            $loc = DB::table('tbl_locations')
                ->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [trim($label)])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower(trim($label)).'%'])
                ->orderBy('id')
                ->first();

            if ($loc) {
                $cache[$code] = (int) $loc->id;
                if (strlen($code) === 5) {
                    $cache['1'.$code] = (int) $loc->id;
                    $cache[str_pad($code, 6, '0', STR_PAD_LEFT)] = (int) $loc->id;
                }
            }
        }

        return $cache;
    }

    /**
     * @return list<int>
     */
    private function resolveTargetLocationIds(?int $zoneId, ?int $branchId): array
    {
        if (! Schema::hasTable('tbl_locations')) {
            return [];
        }

        $zoneId = (int) ($zoneId ?? 0);
        $branchId = (int) ($branchId ?? 0);

        if ($branchId > 0) {
            $q = DB::table('tbl_locations')->where('id', $branchId);
            if ($zoneId > 0) {
                $q->where('zone_id', $zoneId);
            }
            $row = $q->first();

            return $row ? [(int) $row->id] : [];
        }

        if ($zoneId <= 0) {
            return [];
        }

        $ids = DB::table('tbl_locations')
            ->where('zone_id', $zoneId)
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->all();

        if (Schema::hasTable('tblzones')) {
            $zone = DB::table('tblzones')->where('id', $zoneId)->first();
            if ($zone && ! empty($zone->locations)) {
                foreach (explode(',', (string) $zone->locations) as $part) {
                    $part = (int) trim($part);
                    if ($part > 0) {
                        $ids[] = $part;
                    }
                }
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * MID/TID match keys (5- and 6-digit suffixes) for the given master branch ids.
     *
     * @param  list<int>  $locationIds
     * @return list<string>
     */
    private function posMidMatchKeysForLocationIds(array $locationIds): array
    {
        $locationIds = array_values(array_unique(array_filter(array_map('intval', $locationIds))));
        if ($locationIds === []) {
            return [];
        }

        $keys = [];
        $allowed = array_flip($locationIds);

        foreach ($this->posMocSuffixToLocationMap() as $suffix => $locId) {
            if (isset($allowed[$locId])) {
                $keys[] = $suffix;
                if (strlen($suffix) === 6) {
                    $keys[] = substr($suffix, -5);
                }
            }
        }

        foreach ($this->posFiveDigitCodeToLocationMap() as $code => $locId) {
            if (isset($allowed[$locId])) {
                $keys[] = $code;
            }
        }

        foreach (DB::table('tbl_locations')->whereIn('id', $locationIds)->pluck('name') as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            foreach ($this->posBranchShortCodeMap() as $label => $code) {
                if (strcasecmp($name, $label) === 0
                    || stripos($name, $label) !== false
                    || stripos($label, $name) !== false) {
                    $code = preg_replace('/\D/', '', (string) $code);
                    if ($code !== '') {
                        $keys[] = $code;
                        if (strlen($code) === 5) {
                            $keys[] = '1'.$code;
                            $keys[] = str_pad($code, 6, '0', STR_PAD_LEFT);
                        }
                    }
                }
            }
        }

        return array_values(array_unique(array_filter($keys, static fn ($k) => strlen((string) $k) >= 4)));
    }

    private function resolveMasterLocations(?int $zoneId, ?int $branchId): \Illuminate\Support\Collection
    {
        $ids = $this->resolveTargetLocationIds($zoneId, $branchId);
        if ($ids === [] || ! Schema::hasTable('tbl_locations')) {
            return collect();
        }

        return DB::table('tbl_locations')
            ->whereIn('id', $ids)
            ->select('id', 'name', 'zone_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return list<string>
     */
    private function branchMatchTokens(string $branchName): array
    {
        $name = trim($branchName);
        if ($name === '') {
            return [];
        }
        $tokens = [];
        $push = static function (string $t) use (&$tokens): void {
            $t = trim($t);
            if ($t !== '' && ! in_array($t, $tokens, true)) {
                $tokens[] = $t;
            }
        };
        $push($name);

        foreach (preg_split('/\s*[-–—,\/|]\s*/u', $name) as $part) {
            $part = trim((string) $part);
            if (mb_strlen($part) >= 2) {
                $push($part);
            }
        }

        return $tokens;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $locations
     * @return list<string>
     */
    private function bankAccountSuffixesForLocations(\Illuminate\Support\Collection $locations): array
    {
        if ($locations->isEmpty() || ! Schema::hasTable('bank_reconciliation_accounts')) {
            return [];
        }

        $suffixes = [];
        foreach ($locations as $loc) {
            $name = trim((string) ($loc->name ?? ''));
            if ($name === '') {
                continue;
            }

            $accounts = DB::table('bank_reconciliation_accounts')
                ->where(function ($w) use ($name) {
                    $w->whereRaw('LOWER(TRIM(IFNULL(branch_name, ""))) = LOWER(?)', [$name]);
                    if (Schema::hasColumn('bank_reconciliation_accounts', 'account_holder_name')) {
                        $w->orWhereRaw('LOWER(TRIM(IFNULL(account_holder_name, ""))) = LOWER(?)', [$name]);
                    }
                })
                ->pluck('account_number');

            foreach ($accounts as $acct) {
                $digits = preg_replace('/\D/', '', (string) $acct);
                if (strlen($digits) < 4) {
                    continue;
                }
                $suffixes[] = substr($digits, -min(6, strlen($digits)));
            }
        }

        return array_values(array_unique(array_filter($suffixes)));
    }

    // ─── Download ─────────────────────────────────────────────────────

    public function downloadUpload(Request $request, SettlementUpload $upload, string $format): StreamedResponse|\Illuminate\Http\Response
    {
        $accounts = SettlementAccount::where('upload_id', $upload->id)->get();

        if ($format === 'csv') {
            return $this->downloadCsv($upload, $accounts);
        }
        return $this->downloadXlsx($upload, $accounts);
    }

    private function downloadCsv(SettlementUpload $upload, $accounts): StreamedResponse
    {
        $filename = pathinfo($upload->original_filename, PATHINFO_FILENAME) . '_summary.csv';

        return response()->streamDownload(function () use ($accounts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['MID', 'TID', 'Merchant Name', 'Branch', 'Txn Count', 'Transaction Date',
                'Settlement Date', 'Total Transaction Amount', 'Total Charges', 'Total Taxes', 'Total Net Settlement Amount', 'Currency']);

            foreach ($accounts as $a) {
                fputcsv($handle, [
                    $a->mid, $a->tid, $a->merchant_name, $a->branch, $a->transaction_count,
                    $a->transaction_date?->format('d-m-Y'),
                    $a->settlement_date?->format('d-m-Y'),
                    $a->total_transaction_amount, $a->total_charges, $a->total_taxes,
                    $a->total_net_settlement_amount, $a->currency,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function downloadXlsx(SettlementUpload $upload, $accounts): \Illuminate\Http\Response
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Settlement Summary');

        $headers = ['MID', 'TID', 'Merchant Name', 'Branch', 'Txn Count', 'Transaction Date',
            'Settlement Date', 'Total Transaction Amount', 'Total Charges', 'Total Taxes', 'Total Net Settlement Amount', 'Currency'];

        $sheet->fromArray($headers, null, 'A1');

        // Style header
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B4F72']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($accounts as $a) {
            $sheet->fromArray([
                $a->mid, $a->tid, $a->merchant_name, $a->branch, $a->transaction_count,
                $a->transaction_date?->format('d-m-Y'),
                $a->settlement_date?->format('d-m-Y'),
                (float) $a->total_transaction_amount, (float) $a->total_charges, (float) $a->total_taxes,
                (float) $a->total_net_settlement_amount, $a->currency,
            ], null, 'A' . $row);
            $row++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = pathinfo($upload->original_filename, PATHINFO_FILENAME) . '_summary.xlsx';
        $tmp      = tempnam(sys_get_temp_dir(), 'settlement_') . '.xlsx';
        $writer->save($tmp);

        $content = file_get_contents($tmp);
        unlink($tmp);

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function deleteUpload(SettlementUpload $upload): JsonResponse
    {
        try {
            DB::transaction(function () use ($upload) {
                $this->clearBankPosLinksForSettlementUpload($upload);
                SettlementTransaction::where('upload_id', $upload->id)->delete();
                SettlementAccount::where('upload_id', $upload->id)->delete();
                $upload->delete();
            });
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: '.$e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Upload data removed from the dashboard. The original Excel file is kept in storage.',
        ]);
    }

    /**
     * Unlink bank recon POS tags before removing settlement rows (no FK on bank_statements).
     */
    private function clearBankPosLinksForSettlementUpload(SettlementUpload $upload): void
    {
        if (! Schema::hasTable('bank_statements')
            || ! Schema::hasColumn('bank_statements', 'pos_settlement_account_id')) {
            return;
        }

        $accountIds = SettlementAccount::where('upload_id', $upload->id)->pluck('id');
        if ($accountIds->isEmpty()) {
            return;
        }

        $reset = [
            'pos_match_status'            => 'pos_unmatched',
            'pos_settlement_account_id'   => null,
            'pos_matched_mid'             => null,
            'pos_matched_merchant'        => null,
            'pos_matched_settlement_date' => null,
            'pos_matched_by'              => null,
            'pos_matched_at'              => null,
            'updated_at'                  => now(),
        ];
        $safe = [];
        foreach ($reset as $col => $val) {
            if (Schema::hasColumn('bank_statements', $col)) {
                $safe[$col] = $val;
            }
        }

        if ($safe === []) {
            return;
        }

        DB::table('bank_statements')
            ->whereIn('pos_settlement_account_id', $accountIds)
            ->update($safe);
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    private function formatUpload(SettlementUpload $u): array
    {
        return [
            'id'                          => $u->id,
            'original_filename'           => $u->original_filename,
            'file_size'                   => $u->file_size_formatted,
            'total_rows'                  => $u->total_rows,
            'total_accounts'              => $u->total_accounts,
            'duplicate_accounts_skipped'  => (int) ($u->duplicate_accounts_skipped ?? 0),
            'total_transaction_amount'    => number_format((float)$u->total_transaction_amount, 2),
            'total_net_settlement_amount' => number_format((float)$u->total_net_settlement_amount, 2),
            'status'                      => $u->status,
            'status_badge'                => $u->status_badge,
            'uploaded_at'                 => $u->created_at->format('d M Y, h:i A'),
            'uploaded_by_id'              => $u->uploaded_by,
            'uploaded_by_name'            => $u->uploaded_by_name,
            'uploaded_by_email'           => $u->uploaded_by_email,
            'uploaded_by_username'        => $u->uploaded_by_username,
            'uploaded_ip'                 => $u->uploaded_ip,
            'upload_user_agent'           => $u->upload_user_agent,
            'uploaded_by_display'         => $this->formatUploaderLabel($u),
        ];
    }

    private function formatUploaderLabel(SettlementUpload $u): string
    {
        if ($u->uploaded_by_name) {
            $line = $u->uploaded_by_name;
            if ($u->uploaded_by_email) {
                $line .= ' · ' . $u->uploaded_by_email;
            }

            return $line;
        }
        if ($u->uploaded_by_username) {
            return $u->uploaded_by_username;
        }
        if ($u->uploaded_by) {
            return 'User #' . $u->uploaded_by;
        }

        return '—';
    }

    private function formatAccount(SettlementAccount $a, bool $bankPosLinking): array
    {
        $linkCount = isset($a->bank_pos_link_count) ? (int) $a->bank_pos_link_count : 0;
        $stmtId = isset($a->bank_pos_first_statement_id) ? (int) $a->bank_pos_first_statement_id : null;
        $tagged = $bankPosLinking && $linkCount > 0;

        $matchedBy = null;
        if ($bankPosLinking && $tagged) {
            $name = trim((string) ($a->bank_pos_matched_by_name ?? ''));
            if ($name === '') {
                $name = trim((string) ($a->bank_pos_matched_by_username ?? ''));
            }
            $matchedBy = $name !== '' ? $name : null;
        }

        $matchedAt = null;
        if ($bankPosLinking && $tagged && ! empty($a->bank_pos_matched_at_raw)) {
            try {
                $matchedAt = \Carbon\Carbon::parse($a->bank_pos_matched_at_raw)->format('d M Y, h:i A');
            } catch (\Throwable $e) {
                $matchedAt = (string) $a->bank_pos_matched_at_raw;
            }
        }

        $matchStatus = null;
        if ($bankPosLinking && $tagged) {
            $raw = trim((string) ($a->bank_pos_match_status_raw ?? 'pos_matched'));
            $matchStatus = str_replace('_', ' ', $raw);
        }

        return [
            'id' => $a->id,
            'upload_id' => $a->upload_id,
            'upload_filename' => $a->upload?->original_filename,
            'uploaded_by_display' => $a->upload ? $this->formatUploaderLabel($a->upload) : null,
            'mid' => $a->mid,
            'tid' => $a->tid,
            'merchant_name' => $a->merchant_name,
            'trading_name' => $a->trading_name,
            'branch' => $a->branch,
            'transaction_count' => $a->transaction_count,
            'total_transaction_amount' => number_format((float) $a->total_transaction_amount, 2),
            'total_charges' => number_format((float) $a->total_charges, 2),
            'total_taxes' => number_format((float) $a->total_taxes, 2),
            'total_net_settlement_amount' => number_format((float) $a->total_net_settlement_amount, 2),
            'transaction_date' => $a->transaction_date?->format('d M Y'),
            'settlement_date' => $a->settlement_date?->format('d M Y'),
            'currency' => $a->currency,
            'bank_pos_linking' => $bankPosLinking,
            'bank_pos_tag_status' => $bankPosLinking ? ($tagged ? 'tagged' : 'untagged') : 'na',
            'bank_pos_tag_label' => $bankPosLinking ? ($tagged ? 'Tagged (bank recon)' : 'Not tagged') : '—',
            'bank_pos_link_count' => $bankPosLinking ? $linkCount : 0,
            'bank_pos_statement_id' => $bankPosLinking && $stmtId > 0 ? $stmtId : null,
            'bank_pos_matched_by' => $matchedBy,
            'bank_pos_matched_at' => $matchedAt,
            'bank_pos_match_status' => $matchStatus,
        ];
    }
}