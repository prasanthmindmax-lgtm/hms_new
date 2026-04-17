<?php

namespace App\Http\Controllers;

use App\Models\BankStatement;
use App\Models\BranchFinancialReport;
use App\Models\RadiantCashPickup;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use App\Services\RadiantMismatchService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RadiantCashPickupController extends Controller
{
    protected function applyRequestFilters($query, Request $request): void
    {
        if ($request->filled('date_from')) {
            try {
                $query->whereDate('pickup_date_parsed', '>=', Carbon::parse($request->date_from));
            } catch (\Exception $e) {
            }
        }
        if ($request->filled('date_to')) {
            try {
                $query->whereDate('pickup_date_parsed', '<=', Carbon::parse($request->date_to));
            } catch (\Exception $e) {
            }
        }
        if ($request->filled('state')) {
            $query->where('state_name', $request->state);
        }
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        // Zone / Branch filters (from Location Master)
        if ($request->filled('branch_id')) {
            $branch = TblLocationModel::find((int) $request->branch_id);
            if ($branch) {
                $branchName = trim($branch->name);
                $query->where(function ($q) use ($branchName) {
                    $q->whereRaw('LOWER(TRIM(location)) = LOWER(?)', [$branchName])
                      ->orWhere('location', 'like', '%' . $branchName . '%');
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($request->filled('zone_id')) {
            $zid = (int) $request->zone_id;
            if ($zid > 0) {
                $branches = TblLocationModel::where('zone_id', $zid)->get();
                if ($branches->count()) {
                    $query->where(function ($q) use ($branches) {
                        foreach ($branches as $b) {
                            $bn = trim($b->name);
                            $q->orWhereRaw('LOWER(TRIM(location)) = LOWER(?)', [$bn])
                              ->orWhere('location', 'like', '%' . $bn . '%');
                        }
                    });
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('state_name', 'like', "%{$s}%")
                    ->orWhere('region', 'like', "%{$s}%")
                    ->orWhere('location', 'like', "%{$s}%")
                    ->orWhere('hci_slip_no', 'like', "%{$s}%")
                    ->orWhere('point_id', 'like', "%{$s}%")
                    ->orWhere('pickup_date', 'like', "%{$s}%")
                    ->orWhere('deposit_mode', 'like', "%{$s}%")
                    ->orWhere('remarks', 'like', "%{$s}%")
                    ->orWhere('pickup_amount', 'like', "%{$s}%");
            });
        }
    }

    protected function computeFilteredStats($baseQuery): array
    {
        $totalAmount = (clone $baseQuery)->sum('pickup_amount');
        $totalBatches = (clone $baseQuery)->whereNotNull('upload_batch_id')
            ->pluck('upload_batch_id')->unique()->filter()->count();

        return [
            'total_amount' => (float) $totalAmount,
            'total_batches' => (int) $totalBatches,
        ];
    }

    /**
     * Return zones (and optionally branches) that have at least one matching
     * record in radiant_cash_pickups, respecting optional state / zone_id context.
     * Used both by index() and by the AJAX filter-options endpoint.
     */
    protected function zonesWithData(string $state = ''): \Illuminate\Support\Collection
    {
        $sql = "
            SELECT DISTINCT z.id, z.name
            FROM tblzones z
            INNER JOIN tbl_locations l ON l.zone_id = z.id
            INNER JOIN radiant_cash_pickups r ON (
                LOWER(TRIM(r.location)) = LOWER(TRIM(l.name))
                OR r.location LIKE CONCAT('%', l.name, '%')
            )
            " . ($state !== '' ? "WHERE r.state_name = ?" : "") . "
            ORDER BY z.name
        ";
        $rows = $state !== ''
            ? DB::select($sql, [$state])
            : DB::select($sql);

        return collect($rows);
    }

    protected function branchesWithData(int $zoneId, string $state = ''): \Illuminate\Support\Collection
    {
        $params = [$zoneId];
        $stateClause = '';
        if ($state !== '') {
            $stateClause = 'AND r.state_name = ?';
            $params[] = $state;
        }
        $sql = "
            SELECT DISTINCT l.id, l.name
            FROM tbl_locations l
            INNER JOIN radiant_cash_pickups r ON (
                LOWER(TRIM(r.location)) = LOWER(TRIM(l.name))
                OR r.location LIKE CONCAT('%', l.name, '%')
            )
            WHERE l.zone_id = ? {$stateClause}
            ORDER BY l.name
        ";
        return collect(DB::select($sql, $params));
    }

    /**
     * AJAX: return filter option lists (zones / branches) based on actual table data.
     * GET /radiant-cash-pickup/filter-options?state=X&zone_id=Y
     */
    public function getFilterOptions(Request $request)
    {
        $state  = trim($request->get('state', ''));
        $zoneId = (int) $request->get('zone_id', 0);

        $zones    = $this->zonesWithData($state);
        $branches = $zoneId > 0 ? $this->branchesWithData($zoneId, $state) : collect();

        return response()->json([
            'success'  => true,
            'zones'    => $zones->map(fn ($z) => ['id' => $z->id, 'name' => $z->name])->values(),
            'branches' => $branches->map(fn ($b) => ['id' => $b->id, 'name' => $b->name])->values(),
        ]);
    }

    public function index(Request $request)
    {
        $admin = Auth::user();
        $base = RadiantCashPickup::query();
        $this->applyRequestFilters($base, $request);

        $perPage = (int) $request->get('per_page', 25);
        $records = (clone $base)
            ->orderByDesc('pickup_date_parsed')
            ->orderBy('sno')
            ->paginate($perPage)
            ->withQueryString();

        $stats = $this->computeFilteredStats(clone $base);

        $states = RadiantCashPickup::distinct()->orderBy('state_name')->pluck('state_name')->filter()->values();

        // Only show zones that have actual matching records in the pickup table
        $selectedState = trim($request->get('state', ''));
        $zones = $this->zonesWithData($selectedState);

        // Only show branches for the selected zone that have actual data
        $branchesForFilter = collect();
        if ($request->filled('zone_id')) {
            $zid = (int) $request->zone_id;
            if ($zid > 0) {
                $branchesForFilter = $this->branchesWithData($zid, $selectedState);
            }
        }

        return view('Radiant.radiant_cash_pickup', [
            'admin'             => $admin,
            'records'           => $records,
            'totalAmount'       => $stats['total_amount'],
            'totalBatches'      => $stats['total_batches'],
            'states'            => $states,
            'zones'             => $zones,
            'branchesForFilter' => $branchesForFilter,
        ]);
    }

    /**
     * AJAX: table body + pagination + stats (bank-reconciliation style JSON payload).
     */
    public function data(Request $request)
    {
        $base = RadiantCashPickup::query();
        $this->applyRequestFilters($base, $request);

        $perPage = (int) $request->get('per_page', 25);
        $records = (clone $base)
            ->orderByDesc('pickup_date_parsed')
            ->orderBy('sno')
            ->paginate($perPage)
            ->withQueryString();

        $stats = $this->computeFilteredStats(clone $base);

        return response()->json([
            'success' => true,
            'table_html' => view('Radiant.partials.radiant_cash_rows', compact('records'))->render(),
            'pagination_html' => view('Radiant.partials.radiant_cash_pagination', compact('records'))->render(),
            'stats' => $stats,
            'result' => [
                'from' => $records->firstItem(),
                'to' => $records->lastItem(),
                'total' => $records->total(),
            ],
            'pagination_meta' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
            ],
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file = $request->file('excel_file');
        $batchId = 'RADIANT_' . strtoupper(Str::random(10)) . '_' . now()->format('Ymd_His');
        $fileName = $file->getClientOriginalName();

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
        } catch (\Exception $e) {
            $msg = 'Failed to read Excel file: ' . $e->getMessage();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        $dataRows = [];
        $totalRows = count($rows);
        $inserted = 0;
        $skipped = 0;

        for ($i = 4; $i < $totalRows; $i++) {
            $row = $rows[$i];

            $nonEmpty = array_filter($row, fn ($v) => $v !== null && $v !== '');
            if (empty($nonEmpty)) {
                $skipped++;
                continue;
            }

            $cell0 = strtolower(trim((string) ($row[0] ?? '')));
            $cell3 = strtolower(trim((string) ($row[3] ?? '')));
            if (($cell0 === '' || $cell0 === 'nan') && str_contains($cell3, 'grand total')) {
                $skipped++;
                continue;
            }

            $sno = $row[0] ?? null;
            if (! is_numeric($sno)) {
                $skipped++;
                continue;
            }

            $rawDate = trim((string) ($row[2] ?? ''));
            $parsedDate = null;
            if ($rawDate) {
                foreach (['d-m-Y', 'd/m/Y', 'Y-m-d', 'm/d/Y'] as $fmt) {
                    try {
                        $parsedDate = Carbon::createFromFormat($fmt, $rawDate)->toDateString();
                        break;
                    } catch (\Exception $e) {
                    }
                }
                if (! $parsedDate) {
                    try {
                        $parsedDate = Carbon::parse($rawDate)->toDateString();
                    } catch (\Exception $e) {
                    }
                }
            }

            $dataRows[] = [
                'sno' => (int) $sno,
                'state_name' => trim((string) ($row[1] ?? '')),
                'pickup_date' => $rawDate,
                'pickup_date_parsed' => $parsedDate,
                'region' => trim((string) ($row[3] ?? '')),
                'location' => trim((string) ($row[4] ?? '')),
                'customer_name' => trim((string) ($row[5] ?? '')),
                'pickup_address' => trim((string) ($row[6] ?? '')),
                'pickup_point_code' => trim((string) ($row[7] ?? '')),
                'client_code' => trim((string) ($row[8] ?? '')),
                'deposit_mode' => trim((string) ($row[9] ?? '')),
                'frequency' => trim((string) ($row[10] ?? '')),
                'cash_limit' => is_numeric($row[11]) ? (float) $row[11] : null,
                'hci_slip_no' => trim((string) ($row[12] ?? '')),
                'pickup_amount' => is_numeric($row[13]) ? (float) $row[13] : null,
                'deposit_slip_no' => trim((string) ($row[14] ?? '')),
                'seal_tag_no' => trim((string) ($row[15] ?? '')),
                'denom_2000' => is_numeric($row[16] ?? 0) ? (float) $row[16] : 0,
                'denom_1000' => is_numeric($row[17] ?? 0) ? (float) $row[17] : 0,
                'denom_500' => is_numeric($row[18] ?? 0) ? (float) $row[18] : 0,
                'denom_200' => is_numeric($row[19] ?? 0) ? (float) $row[19] : 0,
                'denom_100' => is_numeric($row[20] ?? 0) ? (float) $row[20] : 0,
                'denom_50' => is_numeric($row[21] ?? 0) ? (float) $row[21] : 0,
                'denom_20' => is_numeric($row[22] ?? 0) ? (float) $row[22] : 0,
                'denom_10' => is_numeric($row[23] ?? 0) ? (float) $row[23] : 0,
                'denom_5' => is_numeric($row[24] ?? 0) ? (float) $row[24] : 0,
                'coins' => is_numeric($row[25] ?? 0) ? (float) $row[25] : 0,
                'total' => is_numeric($row[26] ?? null) ? (float) $row[26] : null,
                'difference' => is_numeric($row[27] ?? null) ? (float) $row[27] : null,
                'remarks' => trim((string) ($row[28] ?? '')),
                'ccv' => trim((string) ($row[29] ?? '')),
                'point_id' => trim((string) ($row[30] ?? '')),
                'upload_batch_id' => $batchId,
                'uploaded_file_name' => $fileName,
                'uploaded_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $inserted++;
        }

        foreach (array_chunk($dataRows, 100) as $chunk) {
            DB::table('radiant_cash_pickups')->insert($chunk);
        }

        $uploadMsg = "✓ Upload complete! {$inserted} rows inserted from \"{$fileName}\" (Batch: {$batchId}). {$skipped} rows skipped.";

        /* ── Auto mismatch check for every unique date in this upload ── */
        $alertSummaries = [];
        if ($inserted > 0) {
            // Collect distinct dates from the uploaded data (ignore nulls)
            $uploadedDates = array_values(array_unique(array_filter(
                array_column($dataRows, 'pickup_date_parsed')
            )));

            $triggeredBy = auth()->user()->user_fullname
                        ?? auth()->user()->name
                        ?? 'Upload';

            $service = app(RadiantMismatchService::class);

            foreach ($uploadedDates as $date) {
                $alertSummaries[] = $service->checkAndAlert($date, $triggeredBy);
            }
        }

        /* Build a combined message */
        $alertMsg = '';
        if (!empty($alertSummaries)) {
            $totalMismatch = array_sum(array_column($alertSummaries, 'mismatch'));
            $datesChecked  = count($alertSummaries);

            if ($totalMismatch === 0) {
                $alertMsg = " | ✓ Mismatch check: all records matched across {$datesChecked} date(s) — no alert sent.";
            } else {
                $emailsSent = count(array_filter($alertSummaries, fn ($r) => $r['email_sent']));
                $alertMsg   = " | ⚠ {$totalMismatch} mismatch(es) found across {$datesChecked} date(s) — alert email sent ({$emailsSent} email(s) dispatched).";
            }
        }

        $message = $uploadMsg . $alertMsg;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'         => true,
                'message'         => $message,
                'alert_summaries' => $alertSummaries,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * AJAX: Compare one pickup row against Branch Financial Report and Bank Statement.
     */
    public function compare(Request $request, int $id)
    {
        $pickup = RadiantCashPickup::find($id);
        if (! $pickup) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        $locationName = trim($pickup->location ?? '');
        $pickupDate   = $pickup->pickup_date_parsed; // Carbon date or null
        // dd($locationName,$pickupDate);
        // ── Find matching TblLocation using multiple strategies ──────────────
        $tblLocation = null;
        if ($locationName) {
            // 1) Exact case-insensitive + trim
            $tblLocation = TblLocationModel::whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$locationName])->first();
            // dd($tblLocation);
            // 2) Pickup location LIKE '%branch_name%'
            if (! $tblLocation) {
                $tblLocation = TblLocationModel::whereRaw('LOWER(TRIM(?)) LIKE CONCAT(\'%\', LOWER(TRIM(name)), \'%\')', [$locationName])->first();
            }

            // 3) Branch name contains pickup location word
            if (! $tblLocation) {
                $tblLocation = TblLocationModel::where('name', 'like', '%' . $locationName . '%')->first();
            }

            // 4) Fuzzy: any branch whose name shares the first word with pickup location
            if (! $tblLocation) {
                $firstWord = explode(' ', $locationName)[0];
                if (strlen($firstWord) > 3) {
                    $tblLocation = TblLocationModel::where('name', 'like', '%' . $firstWord . '%')->first();
                }
            }
        }
        // dd($tblLocation,$pickupDate);
        // ── Branch Financial Reports ─────────────────────────────────────────
        // If pickup date falls on Monday, also pull Friday/Sat/Sun reports
        // (weekend cash is handed to Radiant on Monday)
        $branchReports = [];
        $bfrDateFrom   = null;
        $bfrDateTo     = null;
        if ($tblLocation && $pickupDate) {
            $pd          = Carbon::parse($pickupDate);
            $bfrDateFrom = $pd->toDateString();
            $bfrDateTo   = $pd->toDateString();

            $branchReports = BranchFinancialReport::where('branch_id', $tblLocation->id)
                ->whereBetween('report_date', [$bfrDateFrom, $bfrDateTo])
                ->with(['branch', 'zone'])
                ->orderBy('radiant_collected_date')
                ->get()
                ->map(function ($r) {
                    return [
                        'id'                            => $r->id,
                        'report_date'                   => optional($r->report_date)->format('d-m-Y'),
                        'branch_name'                   => optional($r->branch)->name,
                        'zone_name'                     => optional($r->zone)->name,
                        'radiant_collection_amount'     => (float) ($r->radiant_collection_amount ?? 0),
                        'radiant_collected_date'        => optional($r->radiant_collected_date)->format('d-m-Y'),
                        'radiant_not_collected'         => (bool) $r->radiant_not_collected,
                        'radiant_not_collected_remarks' => $r->radiant_not_collected_remarks,
                        'overall_approval_status'       => $r->overall_approval_status,
                        'overall_approval_label'        => $r->overall_approval_status_label,
                    ];
                })
                ->toArray();
        }

        // ── Bank Statement: description LIKE '%BY CASH%{location}%' ─────────
        // transaction_date is stored as "04/Feb/2026" string — use STR_TO_DATE in raw SQL
        // to avoid Carbon cast failures. We also skip Eloquent's date cast by using DB::table.
        $bankEntries = [];
        if ($locationName && $pickupDate) {
            $pd      = Carbon::parse($pickupDate);
            $bkFrom  = $pd->copy()->subDays(1)->toDateString();
            $bkTo    = $pd->copy()->addDays(1)->toDateString();

            $bankQuery = DB::table('bank_statements')
                ->whereRaw("STR_TO_DATE(transaction_date, '%d/%b/%Y') BETWEEN ? AND ?", [$bkFrom, $bkTo]);
            RadiantMismatchService::applyRadiantBankLocationMatch($bankQuery, $locationName);
            $rows = $bankQuery->orderBy('transaction_date')->get();

            $bankEntries = $rows->map(function ($b) {
                return [
                    'id'               => $b->id,
                    'transaction_date' => $b->transaction_date, // raw string e.g. "04/Feb/2026"
                    'description'      => $b->description,
                    'deposit'          => (float) ($b->deposit ?? 0),
                    'withdrawal'       => (float) ($b->withdrawal ?? 0),
                    'reference_number' => $b->reference_number ?? '',
                    'match_status'     => $b->match_status ?? '',
                ];
            })->toArray();
        }

        $bfrTotalAmt = array_sum(array_column($branchReports, 'radiant_collection_amount'));

        return response()->json([
            'success'        => true,
            'pickup'         => [
                'id'             => $pickup->id,
                'pickup_date'    => $pickup->pickup_date,
                'location'       => $pickup->location,
                'region'         => $pickup->region,
                'state_name'     => $pickup->state_name,
                'pickup_amount'  => (float) ($pickup->pickup_amount ?? 0),
                'hci_slip_no'    => $pickup->hci_slip_no,
                'deposit_mode'   => $pickup->deposit_mode,
                'point_id'       => $pickup->point_id,
                'deposit_slip_no'=> $pickup->deposit_slip_no,
                'difference'     => (float) ($pickup->difference ?? 0),
                'remarks'        => $pickup->remarks,
            ],
            'matched_branch' => $tblLocation ? [
                'id'      => $tblLocation->id,
                'name'    => $tblLocation->name,
                'zone_id' => $tblLocation->zone_id,
                'zone'    => optional($tblLocation->zone)->name,
                'status'  => $tblLocation->status,
            ] : null,
            'bfr_date_from'    => $bfrDateFrom,
            'bfr_date_to'      => $bfrDateTo,
            'bfr_total_amount' => (float) $bfrTotalAmt,
            'branch_reports' => $branchReports,
            'bank_entries'   => $bankEntries,
        ]);
    }

    public function deleteBatch(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|string|max:191',
        ]);

        $batch = $request->input('batch_id');
        $ids = RadiantCashPickup::where('upload_batch_id', $batch)->pluck('id');
        if ($ids->isEmpty()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No records found for this upload.'], 404);
            }

            return back()->with('error', 'No records found for this upload.');
        }

        if (Schema::hasColumn('bank_statements', 'radiant_cash_pickup_id')) {
            $upd = ['radiant_cash_pickup_id' => null];
            if (Schema::hasColumn('bank_statements', 'radiant_match_status')) {
                $upd['radiant_match_status'] = 'radiant_unmatched';
            }
            foreach ([
                'radiant_matched_location' => null,
                'radiant_matched_pickup_date' => null,
                'radiant_matched_by' => null,
                'radiant_matched_at' => null,
            ] as $col => $val) {
                if (Schema::hasColumn('bank_statements', $col)) {
                    $upd[$col] = $val;
                }
            }
            DB::table('bank_statements')->whereIn('radiant_cash_pickup_id', $ids)->update($upd);
        }

        $count = RadiantCashPickup::where('upload_batch_id', $batch)->delete();
        $msg = "Upload removed: {$count} pickup row(s) deleted for this file.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'deleted' => $count]);
        }

        return back()->with('success', $msg);
    }

    /**
     * AJAX: match / mismatch counts for current filters (heavy — run after table load).
     */
    public function reconcileCounts(Request $request)
    {
        $base = RadiantCashPickup::query();
        $this->applyRequestFilters($base, $request);
        $state = app(RadiantMismatchService::class)->reconcileDashboardState(clone $base, false);

        return response()->json([
            'success' => true,
            'match_count' => $state['match_count'],
            'mismatch_count' => $state['mismatch_count'],
        ]);
    }

    /**
     * AJAX: sample rows for matched / mismatched modals (capped).
     */
    public function reconcileLists(Request $request)
    {
        $cap = min(500, max(50, (int) $request->get('cap', 500)));
        $base = RadiantCashPickup::query();
        $this->applyRequestFilters($base, $request);
        $state = app(RadiantMismatchService::class)->reconcileDashboardState(clone $base, true, $cap);

        return response()->json([
            'success' => true,
            'match_count' => $state['match_count'],
            'mismatch_count' => $state['mismatch_count'],
            'matched' => $state['matched'],
            'mismatched' => $state['mismatched'],
            'list_cap' => $cap,
        ]);
    }

    /**
     * AJAX: upload batches visible under current filters (for batch management table).
     */
    public function batches(Request $request)
    {
        $base = RadiantCashPickup::query();
        $this->applyRequestFilters($base, $request);

        $rows = (clone $base)
            ->whereNotNull('upload_batch_id')
            ->where('upload_batch_id', '!=', '')
            ->selectRaw('upload_batch_id as batch_id, MAX(uploaded_file_name) as file_name, MIN(created_at) as uploaded_at, COUNT(*) as row_count')
            ->groupBy('upload_batch_id')
            ->orderByDesc(DB::raw('MIN(created_at)'))
            ->get();

        return response()->json([
            'success' => true,
            'batches' => $rows,
        ]);
    }

    public function stats(Request $request)
    {
        $base = RadiantCashPickup::query();
        $this->applyRequestFilters($base, $request);
        $reconcile = app(RadiantMismatchService::class)->reconcileDashboardState(clone $base, false);

        return response()->json([
            'total_amount' => (clone $base)->sum('pickup_amount'),
            'total_records' => (clone $base)->count(),
            'match_count' => $reconcile['match_count'],
            'mismatch_count' => $reconcile['mismatch_count'],
            'by_state' => (clone $base)->selectRaw('state_name, SUM(pickup_amount) as total, COUNT(*) as cnt')->groupBy('state_name')->orderByDesc('total')->get(),
            'by_region' => (clone $base)->selectRaw('region, SUM(pickup_amount) as total, COUNT(*) as cnt')->groupBy('region')->orderByDesc('total')->get(),
        ]);
    }
}
