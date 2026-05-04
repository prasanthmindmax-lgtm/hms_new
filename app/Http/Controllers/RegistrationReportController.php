<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use App\Models\MocdocRegistrationReport;

class RegistrationReportController extends Controller
{
    private function cityCodeMap(): array
    {
        return [
            'AFHARU'  => 'Harur',
            'AFMDU'   => 'Madurai',
            'AFVPAL'  => 'Vepanapalli',
            'AFCHENG' => 'Chengalpattu',
            'AFURP'   => 'Chennai - Urapakkam',
            'AFERD'   => 'Erode',
            'AFKAL'   => 'Kallakurichi',
            'AFNKL'   => 'Nagapattinam',
            'AFSLM'   => 'Salem',
            'AFHZR'   => 'Hosur',
            'AFTRY'   => 'Trichy',
            'AFTHR'   => 'Thiruporur',
            'AFSPM'   => 'Sivagangai',
            'AFVEL'   => 'Vellore',
            'AFOMR'   => 'Old Mahabalipuram Road',
            'AFCPK'   => 'Coimbatore - Ganapathy',
            'AFKONA'  => 'Bengaluru - Konanakunte',
            'AFCBR'   => 'Chidambaram',
            'AFTPR'   => 'Tiruppur',
            'AFTPT'   => 'Thirupathur',
            'AFTAM'   => 'Thiruvannamalai',
            'AFSTY'   => 'Sathyamangalam',
            'AFDAS'   => 'Dindigul',
            'AFHBL'   => 'Bengaluru - Hebbal',
            'AFTAN'   => 'Tirunelveli',
            'AFTHI'   => 'Tiruvannamalai',
            'AFATR'   => 'Aathur',
            'AFPOL'   => 'Pollachi',
            'AFMDP'   => 'Mettupalayam',
            'AFKAN'   => 'Bangalore',
            'AFECT'   => 'Echanari',
            'Coimbatore - Sundarapuram' => 'Coimbatore - Sundarapuram',
            'Coimbatore - Thudiyalur'   => 'Coimbatore - Thudiyalur',
            'Kerala - Kozhikode'        => 'Kerala - Kozhikode',
            'Karur'                     => 'Karur',
            'Tiruppur'                  => 'Tiruppur',
            'Kerala - Palakkad'         => 'Kerala - Palakkad',
            'Pennagaram'                => 'Pennagaram',
            'Tanjore'                   => 'Tanjore',
            'Kanchipuram'               => 'Kanchipuram',
            'Villupuram'                => 'Villupuram',
            'Thiruvallur'               => 'Thiruvallur',
            'Corporate Office - Guindy' => 'Corporate Office - Guindy',
            'Chennai - Madipakkam'      => 'Chennai - Madipakkam',
            'Chennai - Sholinganallur'  => 'Chennai - Sholinganallur',
            'Chennai - Tambaram'        => 'Chennai - Tambaram',
            'Chennai - Vadapalani'      => 'Chennai - Vadapalani',
        ];
    }

    public function index()
    {
        $admin    = auth()->user();
        $zones    = TblZonesModel::orderBy('name', 'asc')->get(['id', 'name']);
        $lastSync = MocdocRegistrationReport::max('synced_at');
        return view('modules.registration.index', compact('admin', 'zones', 'lastSync'));
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
            $start = Carbon::createFromFormat('d/m/Y', $startRaw)->startOfDay();
            $end   = Carbon::createFromFormat('d/m/Y', $endRaw)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format.']);
        }

        $branchNames = $this->resolveBranchNames($request);

        if ($source === 'local') {
            return $this->fetchLocal($request, $start, $end, $branchNames, $page, $perPage);
        }

        return $this->fetchLive($request, $start, $end, $branchNames, $page, $perPage);
    }

    // ── LOCAL ────────────────────────────────────────────────────────────────

    private function fetchLocal(Request $request, Carbon $start, Carbon $end,
        array $branchNames, int $page, int $perPage)
    {
        $query = MocdocRegistrationReport::whereBetween('reg_date', [
            $start->toDateString(), $end->toDateString()
        ]);

        // Zone / Branch filter — match against stored area name
        if (!empty($branchNames)) {
            $query->whereIn('area', $branchNames);
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name',   'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('phid',   'like', "%{$s}%");
            });
        }

        $total    = $query->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $rows     = $query->orderByDesc('reg_date')->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $data = $rows->map(fn($r) => [
            'phid'       => $r->phid,
            'name'       => $r->name,
            'mobile'     => $r->mobile,
            'gender'     => $r->gender,
            'age'        => $r->age,
            'area'       => $r->area,
            'prefix'     => $r->prefix,
            'reg_date'   => $r->reg_date?->format('Ymd'),
            'created_at' => $r->reg_date?->toDateString(),
        ])->values()->all();

        // Area summary for sidebar
        $summaryQuery = MocdocRegistrationReport::whereBetween('reg_date', [$start->toDateString(), $end->toDateString()]);
        if (!empty($branchNames)) $summaryQuery->whereIn('area', $branchNames);
        $areaSummary = $summaryQuery->selectRaw('area, count(*) as cnt')
            ->whereNotNull('area')->groupBy('area')->orderByDesc('cnt')
            ->pluck('cnt', 'area')->all();

        return response()->json([
            'success'      => true,
            'source'       => 'local',
            'data'         => $data,
            'total'        => $total,
            'page'         => $page,
            'per_page'     => $perPage,
            'last_page'    => $lastPage,
            'area_summary' => $areaSummary,
        ]);
    }

    // ── LIVE ─────────────────────────────────────────────────────────────────

    private function fetchLive(Request $request, Carbon $start, Carbon $end,
        array $branchNames, int $page, int $perPage)
    {
        $areaMap  = $this->cityCodeMap();
        $dateList = [];
        $curr     = $start->copy();
        while ($curr <= $end) { $dateList[] = $curr->format('Ymd'); $curr->addDay(); }

        $allRecords = [];
        foreach ($dateList as $dateKey) {
            $data = $this->callRegistrationApi($dateKey);
            if (empty($data['ptlist'])) continue;
            foreach ($data['ptlist'] as $item) {
                if (empty($item['phid'])) continue;
                $prefix = explode('-', $item['phid'])[0];
                $area   = $areaMap[$prefix] ?? ($areaMap[$item['phid']] ?? 'Unknown');
                $allRecords[] = [
                    'phid'     => $item['phid'],
                    'name'     => $item['name'] ?? '',
                    'mobile'   => $item['mobile'] ?? '',
                    'gender'   => $item['gender'] ?? '',
                    'age'      => $item['age'] ?? '',
                    'area'     => $area,
                    'prefix'   => $prefix,
                    'reg_date' => $dateKey,
                ];
            }
        }

        // Deduplicate
        $seen = []; $unique = [];
        foreach ($allRecords as $rec) {
            if (!isset($seen[$rec['phid']])) { $seen[$rec['phid']] = true; $unique[] = $rec; }
        }

        // Zone / Branch filter — match against resolved area/branch names
        if (!empty($branchNames)) {
            $unique = array_values(array_filter($unique, fn($r) => in_array($r['area'], $branchNames)));
        }

        usort($unique, fn($a, $b) => strcmp($b['reg_date'], $a['reg_date']));

        $total     = count($unique);
        $lastPage  = max(1, (int) ceil($total / $perPage));
        $paginated = array_slice($unique, ($page - 1) * $perPage, $perPage);

        $areaSummary = [];
        foreach ($unique as $rec) {
            $areaSummary[$rec['area']] = ($areaSummary[$rec['area']] ?? 0) + 1;
        }
        arsort($areaSummary);

        return response()->json([
            'success'      => true,
            'source'       => 'live',
            'data'         => $paginated,
            'total'        => $total,
            'page'         => $page,
            'per_page'     => $perPage,
            'last_page'    => $lastPage,
            'area_summary' => $areaSummary,
        ]);
    }

    public function getAreas()
    {
        return response()->json($this->sortedAreas());
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
        return response()->json(['last_sync' => MocdocRegistrationReport::max('synced_at')]);
    }

    private function sortedAreas(): array
    {
        $areas = array_values(array_unique($this->cityCodeMap()));
        sort($areas);
        return $areas;
    }

    private function callRegistrationApi(string $dateKey): array
    {
        return $this->curlPost(
            'https://mocdoc.com/api/get/ptlist/draravinds-ivf',
            'registrationdate=' . substr($dateKey, 0, 8),
            [
                'md-authorization: MD 7b40af0edaf0ad75:zzJIrJPzgSOMhucj/1bXawbz+GI=',
                'Date: Fri, 11 Apr 2025 06:18:59 GMT',
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: SRV=s1; vid3=CvAABmf4wWdOP+VJBV+AAg==',
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
