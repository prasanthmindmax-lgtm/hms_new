<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use App\Models\MocdocCheckinReport;

class CheckinReportController extends Controller
{
    private function mocdocLocations(): array
    {
        return [
            'location1'  => 'Kerala - Palakkad',
            'location6'  => 'Kerala - Kozhikode',
            'location7'  => 'Erode',
            'location13' => 'Salem (Agraharam)',
            'location14' => 'Tiruppur',
            'location20' => 'Coimbatore - Ganapathy',
            'location21' => 'Hosur',
            'location22' => 'Chennai - Sholinganallur',
            'location23' => 'Chennai - Urapakkam',
            'location24' => 'Chennai - Madipakkam',
            'location25' => 'Salem',
            'location26' => 'Kanchipuram',
            'location27' => 'Coimbatore - Sundarapuram',
            'location28' => 'Trichy',
            'location29' => 'Thiruvallur',
            'location30' => 'Pollachi',
            'location31' => 'Electronic City',
            'location33' => 'Chennai - Tambaram',
            'location34' => 'Tanjore',
            'location35' => 'Konanakunte',
            'location36' => 'Harur',
            'location38' => 'Varadhambalayam',
            'location39' => 'Coimbatore - Thudiyalur',
            'location40' => 'Madurai',
            'location41' => 'Hebbal',
            'location42' => 'Kallakurichi',
            'location43' => 'Vellore',
            'location45' => 'Aathur',
            'location46' => 'Namakal',
            'location47' => 'Dasarahalli',
            'location48' => 'Chengalpattu',
            'location49' => 'Chennai - Vadapalani',
            'location50' => 'Pennagaram',
            'location51' => 'Thirupathur',
            'location53' => 'Dharmapuri',
        ];
    }

    public function index()
    {
        $admin    = auth()->user();
        $zones    = TblZonesModel::orderBy('name', 'asc')->get(['id', 'name']);
        $lastSync = MocdocCheckinReport::max('synced_at');

        return view('modules.checkin.index', compact('admin', 'zones', 'lastSync'));
    }

    /**
     * Resolve branch names from zone_ids / branch_ids request params.
     * Returns empty array when neither is provided (= no filter = all records).
     */
    private function resolveBranchNames(Request $request): array
    {
        $branchIds = array_filter((array) $request->input('branch_ids', []));
        $zoneIds   = array_filter((array) $request->input('zone_ids',   []));

        if (!empty($branchIds)) {
            return TblLocationModel::whereIn('id', $branchIds)
                ->where('status', 1)->pluck('name')->toArray();
        }

        if (!empty($zoneIds)) {
            return TblLocationModel::whereIn('zone_id', $zoneIds)
                ->where('status', 1)->pluck('name')->toArray();
        }

        return [];
    }

    /**
     * Unified fetch — source=live|local
     */
    public function fetch(Request $request)
    {
        set_time_limit(0);

        $source    = $request->input('source', 'live');
        $dateRange = trim($request->input('date_range', ''));
        $page      = max(1, (int) $request->input('page', 1));
        $perPage   = min(100, max(10, (int) $request->input('per_page', 25)));

        if (empty($dateRange)) {
            return response()->json(['success' => false, 'message' => 'Date range is required.']);
        }

        $parts    = explode(' - ', $dateRange);
        $startRaw = trim($parts[0] ?? '');
        $endRaw   = trim($parts[1] ?? $startRaw);

        try {
            $startDate = Carbon::createFromFormat('d/m/Y', $startRaw)->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', $endRaw)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format.']);
        }

        $branchNames = $this->resolveBranchNames($request);
        // dd($branchNames);
        if ($source === 'local') {
            return $this->fetchLocal($request, $startDate, $endDate, $branchNames, $page, $perPage);
        }

        return $this->fetchLive($startDate, $endDate, $branchNames, $page, $perPage);
    }

    // ── LOCAL (from DB) ──────────────────────────────────────────────────────

    private function fetchLocal(Request $request, Carbon $start, Carbon $end,
        array $branchNames, int $page, int $perPage)
    {
        $query = MocdocCheckinReport::whereBetween('checkin_date', [
            $start->toDateString(), $end->toDateString()
        ]);

        // Zone / Branch filter — match against stored location name
        if (!empty($branchNames)) {
            $query->whereIn('mocdoc_location_name', $branchNames);
        }

        // Purpose filter
        if ($request->filled('purpose')) {
            $query->where('purpose', 'like', '%' . $request->purpose . '%');
        }

        // City filter
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('patient_name', 'like', "%{$s}%")
                  ->orWhere('mobile',       'like', "%{$s}%");
            });
        }

        $total    = $query->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $rows     = $query->orderBy('checkin_date')->orderBy('checkin_time')
                          ->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $data = $rows->map(fn($r) => [
            'checkinkey'  => $r->checkinkey,
            'phid'        => $r->phid,
            'datetime'    => $r->checkin_date->format('d-m-Y') . ' ' . ($r->checkin_time ?? ''),
            'date_only'   => $r->checkin_date->format('d-m-Y'),
            'time'        => $r->checkin_time,
            'name'        => $r->patient_name,
            'mobile'      => $r->mobile,
            'dob'         => $r->dob?->format('d-m-Y'),
            'age'         => $r->age,
            'gender'      => $r->gender,
            'purpose'     => $r->purpose,
            'ptsource'    => $r->ptsource,
            'city'        => $r->city,
            'state'       => $r->state,
            'bookeddr'    => $r->bookeddr_name,
            'visittype'   => $r->visittype,
            'opno'        => $r->opno,
            'location'    => $r->mocdoc_location_name,
        ])->values()->all();

        // Stats for local
        $statsQuery = MocdocCheckinReport::whereBetween('checkin_date', [
            $start->toDateString(), $end->toDateString()
        ]);
        if (!empty($branchNames)) $statsQuery->whereIn('mocdoc_location_name', $branchNames);
        $purposes = $statsQuery->clone()->selectRaw('purpose, count(*) as cnt')->whereNotNull('purpose')->groupBy('purpose')->orderByDesc('cnt')->pluck('cnt', 'purpose')->all();
        $sources  = $statsQuery->clone()->selectRaw('ptsource, count(*) as cnt')->whereNotNull('ptsource')->groupBy('ptsource')->orderByDesc('cnt')->pluck('cnt', 'ptsource')->all();
        $cities   = $statsQuery->clone()->selectRaw('city, count(*) as cnt')->whereNotNull('city')->groupBy('city')->orderByDesc('cnt')->limit(10)->pluck('cnt', 'city')->all();

        $locationName = empty($branchNames) ? 'All Locations' : implode(', ', $branchNames);

        return response()->json([
            'success'       => true,
            'source'        => 'local',
            'data'          => $data,
            'total'         => $total,
            'page'          => $page,
            'per_page'      => $perPage,
            'last_page'     => $lastPage,
            'location_name' => $locationName,
            'stats'         => [
                'total'    => $total,
                'purposes' => $purposes,
                'sources'  => $sources,
                'cities'   => $cities,
            ],
        ]);
    }

    // ── LIVE (from API) ──────────────────────────────────────────────────────

    private function fetchLive(Carbon $startDate, Carbon $endDate, array $branchNames, int $page, int $perPage)
    {
        $allLocs  = $this->mocdocLocations();
        $startFmt = $startDate->format('Ymd') . '00:00:00';
        $endFmt   = $endDate->format('Ymd')   . '23:59:59';

        // Only call the API for the selected locations; fall back to all when no filter is active
        if (!empty($branchNames)) {
            $keysToQuery = array_keys(array_filter($allLocs, fn($name) => in_array($name, $branchNames)));
        } else {
            $keysToQuery = array_keys($allLocs);
        }

        $results = [];

        foreach ($keysToQuery as $locationKey) {
            $apiData = $this->callCheckinApi($startFmt, $endFmt, $locationKey);
            if (empty($apiData['checkinlist'])) continue;

            foreach ($apiData['checkinlist'] as $item) {
                $cdate = null;
                if (!empty($item['date'])) {
                    try { $cdate = Carbon::createFromFormat('Ymd', $item['date'])->format('d-m-Y'); } catch (\Exception $e) {}
                }
                $dob = null;
                if (!empty($item['patient']['dob'])) {
                    try { $dob = Carbon::createFromFormat('Ymd', $item['patient']['dob'])->format('d-m-Y'); } catch (\Exception $e) {}
                }
                $results[] = [
                    'checkinkey'  => $item['checkinkey'] ?? null,
                    'phid'        => $item['patient']['phid'] ?? '',
                    'datetime'    => ($cdate ?? '') . ' ' . ($item['start'] ?? ''),
                    'date_only'   => $cdate,
                    'time'        => $item['start'] ?? '',
                    'name'        => trim(($item['patient']['title'] ?? '') . ' ' . ($item['patient']['name'] ?? '') . ($item['patient']['lname'] ? ' ' . $item['patient']['lname'] : '')),
                    'mobile'      => $item['patient']['mobile'] ?? '',
                    'dob'         => $dob,
                    'age'         => $item['patient']['age'] ?? '',
                    'gender'      => $item['patient']['gender'] ?? '',
                    'purpose'     => $item['purpose'] ?? '',
                    'ptsource'    => $item['patient']['ptsource'] ?? '',
                    'city'        => $item['patient']['address']['city'] ?? '',
                    'state'       => $item['patient']['address']['state'] ?? '',
                    'bookeddr'    => $item['bookeddr_name'] ?? '',
                    'visittype'   => $item['visittype'] ?? '',
                    'opno'        => $item['opno'] ?? '',
                    'location'    => $allLocs[$locationKey] ?? $locationKey,
                    'location_key' => $locationKey,
                ];
            }
        }

        usort($results, fn($a, $b) => strcmp($a['datetime'], $b['datetime']));

        $total     = count($results);
        $lastPage  = max(1, (int) ceil($total / $perPage));
        $paginated = array_slice($results, ($page - 1) * $perPage, $perPage);

        $locationLabel = !empty($branchNames) ? implode(', ', $branchNames) : 'All Locations';

        return response()->json([
            'success'       => true,
            'source'        => 'live',
            'data'          => $paginated,
            'total'         => $total,
            'page'          => $page,
            'per_page'      => $perPage,
            'last_page'     => $lastPage,
            'location_name' => $locationLabel,
            'stats'         => $this->buildStats($results),
        ]);
    }

    private function buildStats(array $results): array
    {
        $purposes = $sources = $cities = [];
        foreach ($results as $r) {
            $p = $r['purpose'] ?: 'Not specified';
            $s = $r['ptsource'] ?: 'Unknown';
            $c = $r['city'] ?: 'Unknown';
            $purposes[$p] = ($purposes[$p] ?? 0) + 1;
            $sources[$s]  = ($sources[$s]  ?? 0) + 1;
            $cities[$c]   = ($cities[$c]   ?? 0) + 1;
        }
        arsort($purposes); arsort($sources); arsort($cities);
        return ['total' => count($results), 'purposes' => $purposes, 'sources' => $sources, 'cities' => array_slice($cities, 0, 10, true)];
    }

    public function getLocations()
    {
        $locs = $this->mocdocLocations();
        asort($locs);
        return response()->json(array_map(fn($k, $n) => ['key' => $k, 'name' => $n], array_keys($locs), $locs));
    }

    public function getZones()
    {
        return response()->json(TblZonesModel::orderBy('name', 'asc')->get(['id', 'name']));
    }

    public function getBranches(Request $request)
    {
        $query = TblLocationModel::where('status', 1)->orderBy('name', 'asc');
        if ($request->filled('zone_id')) $query->where('zone_id', $request->zone_id);
        if ($request->filled('zone_ids')) {
            $ids = is_array($request->zone_ids) ? $request->zone_ids : explode(',', $request->zone_ids);
            $query->whereIn('zone_id', $ids);
        }
        return response()->json($query->get(['id', 'name', 'zone_id']));
    }

    public function getLastSync()
    {
        $checkin = MocdocCheckinReport::max('synced_at');
        return response()->json(['last_sync' => $checkin]);
    }

    private function callCheckinApi(string $startDate, string $endDate, string $locationKey): array
    {
        return $this->curlPost(
            'https://mocdoc.in/api/checkedin/draravinds-ivf',
            "startdate={$startDate}&enddate={$endDate}&entitylocation={$locationKey}",
            [
                'md-authorization: MD 7b40af0edaf0ad75:jR1+YyQZVWCIIaXlgxt1z8uixQ4=',
                'Date: Mon, 31 Mar 2025 08:05:38 GMT',
                'Content-Type: application/x-www-form-urlencoded',
            ]
        );
    }

    private function curlPost(string $url, string $postFields, array $headers): array
    {
        for ($retry = 0, $delay = 2; $retry < 5; $retry++) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 60,
                CURLOPT_FOLLOWLOCATION => true, CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postFields, CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($curl);
            $status   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($status === 200) return json_decode($response, true) ?? [];
            if ($status === 429) { sleep($delay); $delay *= 2; } else break;
        }
        return [];
    }
}
