<?php

namespace App\Http\Controllers;

use App\Models\RadiantCashPickup;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $totalRecords = (clone $baseQuery)->count();
        $totalBatches = (clone $baseQuery)->whereNotNull('upload_batch_id')
            ->pluck('upload_batch_id')->unique()->filter()->count();
        $locationsCount = (clone $baseQuery)->whereNotNull('location')
            ->where('location', '!=', '')
            ->pluck('location')->unique()->filter()->count();

        return [
            'total_amount' => (float) $totalAmount,
            'total_records' => (int) $totalRecords,
            'total_batches' => (int) $totalBatches,
            'locations_count' => (int) $locationsCount,
        ];
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
        $zones = TblZonesModel::orderBy('name')->get();
        $branchesForFilter = collect();
        if ($request->filled('zone_id')) {
            $zid = (int) $request->zone_id;
            if ($zid > 0) {
                $branchesForFilter = TblLocationModel::where('zone_id', $zid)->orderBy('name')->get();
            }
        }

        return view('Radiant.radiant_cash_pickup', [
            'admin' => $admin,
            'records' => $records,
            'totalAmount' => $stats['total_amount'],
            'totalRecords' => $stats['total_records'],
            'totalBatches' => $stats['total_batches'],
            'locationsCount' => $stats['locations_count'],
            'states' => $states,
            'zones' => $zones,
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

        $message = "✓ Upload complete! {$inserted} rows inserted from \"{$fileName}\" (Batch: {$batchId}). {$skipped} rows skipped.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function deleteBatch(Request $request)
    {
        $batch = $request->input('batch_id');
        if (! $batch) {
            return back()->with('error', 'No batch specified.');
        }
        $count = RadiantCashPickup::where('upload_batch_id', $batch)->count();
        RadiantCashPickup::where('upload_batch_id', $batch)->delete();

        return back()->with('success', "Batch deleted: {$count} records removed.");
    }

    public function stats(Request $request)
    {
        $base = RadiantCashPickup::query();
        $this->applyRequestFilters($base, $request);

        return response()->json([
            'total_amount' => (clone $base)->sum('pickup_amount'),
            'total_records' => (clone $base)->count(),
            'by_state' => (clone $base)->selectRaw('state_name, SUM(pickup_amount) as total, COUNT(*) as cnt')->groupBy('state_name')->orderByDesc('total')->get(),
            'by_region' => (clone $base)->selectRaw('region, SUM(pickup_amount) as total, COUNT(*) as cnt')->groupBy('region')->orderByDesc('total')->get(),
        ]);
    }
}
