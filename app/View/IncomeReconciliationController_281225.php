<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomeReconciliation;
use App\Models\TblZonesModel;
use App\Models\TblLocationModel;
use Carbon\Carbon;

class IncomeReconciliationController extends Controller
{
    // Show view
    public function index()
    {
        $zones = TblZonesModel::select('id','name')->get();
        $admin = auth()->user();
        return view('superadmin.Income_reconciliation', compact('zones','admin'));
    }

    /**
     * Fetch MOC-DOC totals from API and return reconciliation row
     */
    public function fetch(Request $request)
    {
        set_time_limit(0);

        $dateRange = $request->input('date') ?? now()->format('d/m/Y');
        if (strpos($dateRange, ' - ') !== false) {
            [$start, $end] = explode(' - ', $dateRange);
            $date = Carbon::createFromFormat('d/m/Y', $start)->format('Y-m-d');
            $dates = $this->buildApiDatesArray($start, $end);
        } else {
            $dates = [Carbon::createFromFormat('d/m/Y', $dateRange)->format('Ymd')];
            $date = Carbon::createFromFormat('d/m/Y', $dateRange)->format('Y-m-d');
        }

        $zone = $request->input('zone') ?? null;
        $branch = $request->input('branch') ?? null;

        $totals = [
            'cash' => 0.00,
            'card' => 0.00,
            'upi'  => 0.00,
            'neft' => 0.00,
        ];

        $zlocations = $this->determineZLocations($zone, $branch);
        if (empty($zlocations)) {
            $zlocations = array_keys($this->cityArray());
        }

        $url = 'https://mocdoc.in/api/get/billlist/draravinds-ivf';

        foreach ($dates as $dte) {
            foreach ($zlocations as $zloc) {
                $apiResp = $this->postCurlApi($url, $dte, $zloc);

                if (!empty($apiResp['billinglist']) && is_array($apiResp['billinglist'])) {
                    foreach ($apiResp['billinglist'] as $bill) {
                        $totals['cash'] += floatval($bill['Cash'] ?? 0);
                        $totals['card'] += floatval($bill['Card'] ?? 0);
                        $totals['upi']  += floatval($bill['UPI'] ?? 0);
                        $totals['neft'] += floatval($bill['Neft'] ?? 0);
                    }
                }
            }
        }

        $row = IncomeReconciliation::firstOrCreate(
            [
                'date' => $date,
                'zone' => $zone ?? '',
                'branch' => $branch ?? '',
            ],
            [
                'cash_mocdoc' => $totals['cash'],
                'card_mocdoc' => $totals['card'],
                'upi_mocdoc'  => $totals['upi'],
                'neft_mocdoc' => $totals['neft'],
            ]
        );

        $row->update([
            'cash_mocdoc' => number_format($totals['cash'], 2, '.', ''),
            'card_mocdoc' => number_format($totals['card'], 2, '.', ''),
            'upi_mocdoc'  => number_format($totals['upi'], 2, '.', ''),
            'neft_mocdoc' => number_format($totals['neft'], 2, '.', ''),
        ]);

        return response()->json($row);
    }

    /**
     * Save manual edited radiant/bank values for a reconciliation row
     */
    public function insert(Request $r)
{
    IncomeReconciliation::create([
        'date'           => $r->date,
        'zone'           => $r->zone,
        'branch'         => $r->branch,

        'cash_mocdoc'    => $r->cash_mocdoc,
        'cash_radiant'   => $r->cash_radiant,
        'cash_bank'      => $r->cash_bank,

        'card_mocdoc'    => $r->card_mocdoc,
        'card_radiant'   => $r->card_radiant,
        'card_bank'      => $r->card_bank,

        'upi_mocdoc'     => $r->upi_mocdoc,
        'upi_radiant'    => $r->upi_radiant,
        'upi_bank'       => $r->upi_bank,

        'neft_mocdoc'    => $r->neft_mocdoc,
        'neft_bank'      => $r->neft_bank
    ]);

    return response()->json(["status" => "ok"]);
}

    public function save(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:income_reconciliations,id',
            'cash_radiant' => 'nullable|numeric',
            'cash_bank' => 'nullable|numeric',
            'card_radiant' => 'nullable|numeric',
            'card_bank' => 'nullable|numeric',
            'upi_radiant' => 'nullable|numeric',
            'upi_bank' => 'nullable|numeric',
            'neft_radiant' => 'nullable|numeric',
            'neft_bank' => 'nullable|numeric',
        ]);

        $row = IncomeReconciliation::find($validated['id']);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Row not found'], 404);
        }

        $row->update([
            'cash_radiant' => $request->input('cash_radiant', $row->cash_radiant),
            'cash_bank'    => $request->input('cash_bank', $row->cash_bank),
            'card_radiant' => $request->input('card_radiant', $row->card_radiant),
            'card_bank'    => $request->input('card_bank', $row->card_bank),
            'upi_radiant'  => $request->input('upi_radiant', $row->upi_radiant),
            'upi_bank'     => $request->input('upi_bank', $row->upi_bank),
            'neft_radiant' => $request->input('neft_radiant', $row->neft_radiant),
            'neft_bank'    => $request->input('neft_bank', $row->neft_bank),
        ]);

        return response()->json(['status' => 'success', 'data' => $row]);
    }

    // ---------------- Helpers ----------------

    private function buildApiDatesArray($startD, $endD)
    {
        $start = Carbon::createFromFormat('d/m/Y', $startD);
        $end = Carbon::createFromFormat('d/m/Y', $endD);
        $dates = [];
        while ($start <= $end) {
            $dates[] = $start->format('Ymd');
            $start->addDay();
        }
        return $dates;
    }

    private function determineZLocations($zone, $branch)
    {
        $zlocations = [];
        if (empty($zone) && empty($branch)) return $zlocations;

        $cityArr = $this->cityArray();
        if (!empty($branch)) {
            $br = trim(explode(',', $branch)[0]);
            $zloc = array_search($br, $cityArr);
            if ($zloc !== false) $zlocations[] = $zloc;
        } elseif (!empty($zone)) {
            $locations = TblBranchesModel::where('zone_name', $zone)->pluck('name')->toArray();
            foreach ($locations as $loc) {
                $zloc = array_search($loc, $cityArr);
                if ($zloc !== false) $zlocations[] = $zloc;
            }
        }

        return array_unique($zlocations);
    }

    private function postCurlApi($url, $dte, $zlocation)
    {
        $payload = [
            'date' => $dte,
            'location' => $zlocation
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return is_array($data) ? $data : ['billinglist' => []];
    }

    private function cityArray()
    {
        return [
            "location1" => "Kerala - Palakkad",
            "location7" => "Erode",
            "location14" => "Tiruppur",
            "location6" => "Kerala - Kozhikode",
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
        ];
    }

    // For AJAX branch dropdown
  // Route: Route::get('/superadmin/get-locations', [IncomeReconciliationController::class, 'getLocationsByZone'])->name('superadmin.get_locations_by_zone');
public function getLocationsByZone(Request $request)
{
    $zone = $request->zone;
    $locations = TblLocationModel::where('zone_name', $zone)  // <-- use actual column
                                 ->select('name', 'id')       // select id too if needed
                                 ->get();
    return response()->json($locations);
}


}
