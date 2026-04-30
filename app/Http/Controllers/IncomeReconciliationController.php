<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomeReconciliation;
use App\Models\TblZonesModel;
use App\Models\TblLocationModel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncomeReconciliationExport;
use App\Exports\IncomeTemplateExport;
use App\Imports\IncomeImport;
use App\Imports\RadiantIncomeImport;
use App\Exports\IncomeTemplateExportnew;
use App\Exports\IncomeReconciliationExportnew;
use App\Exports\IncomeReconciliationMonthlyExport;

class IncomeReconciliationController extends Controller
{
    // Show view
    public function index()
    {
         $admin = auth()->user();

        // Get zones based on access limits
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            // Access limit 1 → All zones
            $zones = TblZonesModel::select('id', 'name')->get();
        } elseif ($admin->access_limits == 2) {
            // Access limit 2 → User branch zone only
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        } elseif ($admin->access_limits == 3 || $admin->access_limits == 5) {
            // Access limit 3 → User specific branch only (usually no zones needed or just their zone)
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        }

        return view('superadmin.Income_reconciliation', compact('zones', 'admin'));
    }
    public function indexNew()
    {
        $admin = auth()->user();

        // Get zones based on access limits
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            // Access limit 1 → All zones
            $zones = TblZonesModel::select('id', 'name')->get();
        } elseif ($admin->access_limits == 2) {
            // Access limit 2 → User branch zone only
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        } elseif ($admin->access_limits == 3 || $admin->access_limits == 5) {
            // Access limit 3 → User specific branch only (usually no zones needed or just their zone)
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        }

        return view('income.Income_reconciliation', compact('zones', 'admin'));
    }
    public function indexBranch()
    {
        $admin = auth()->user();

        // Get zones based on access limits
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            // Access limit 1 → All zones
            $zones = TblZonesModel::select('id', 'name')->get();
        } elseif ($admin->access_limits == 2) {
            // Access limit 2 → User branch zone only
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        } elseif ($admin->access_limits == 3 || $admin->access_limits == 5) {
            // Access limit 3 → User specific branch only (usually no zones needed or just their zone)
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        }

        return view('income.Income_reconciliation_branch', compact('zones', 'admin'));
    }
    public function overviewindex()
    {
        $admin = auth()->user();

        // Get zones based on access limits
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            // Access limit 1 → All zones
            $zones = TblZonesModel::select('id', 'name')->get();
        } elseif ($admin->access_limits == 2) {
            // Access limit 2 → User branch zone only
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        } elseif ($admin->access_limits == 3 || $admin->access_limits == 5) {
            // Access limit 3 → User specific branch only (usually no zones needed or just their zone)
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        }
        // dd($zones);
        return view('superadmin.Income_reconciliation_overview', compact('zones','admin'));
    }

    public function overviewindexnew()
    {
        $admin = auth()->user();

        // Get zones based on access limits
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            // Access limit 1 → All zones
            $zones = TblZonesModel::select('id', 'name')->get();
        } elseif ($admin->access_limits == 2) {
            // Access limit 2 → User branch zone only
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        } elseif ($admin->access_limits == 3 || $admin->access_limits == 5) {
            // Access limit 3 → User specific branch only (usually no zones needed or just their zone)
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        }
        // dd($zones);
        return view('income.Income_reconciliation_overview', compact('zones','admin'));
    }

    public function IncomeMontlyReport()
    {
        $admin = auth()->user();

        // Get zones based on access limits
        if ($admin->access_limits == 1) {
            // Access limit 1 → All zones
            $zones = TblZonesModel::select('id', 'name')->get();
        } elseif ($admin->access_limits == 2) {
            // Access limit 2 → User branch zone only
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        } else {
            // Access limit 3 → User specific branch only (usually no zones needed or just their zone)
            $zones = TblZonesModel::select('id', 'name')
                ->where('id', $admin->zone_id ?? null)
                ->get();
        }
        // dd($zones);
        return view('income.Income_monthly_report', compact('zones','admin'));
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
// public function checkDate(Request $request)
// {
//     dd($request);
//     $branch = $request->branch_name;
//      // Function to parse date with multiple formats
//     function parseDate($dateString)
//     {
//         // Try d/m/Y format first
//         try {
//             return Carbon::createFromFormat('d/m/Y', $dateString);
//         } catch (\Exception $e) {
//             // Try Y-m-d format if first fails
//             try {
//                 return Carbon::createFromFormat('Y-m-d', $dateString);
//             } catch (\Exception $e) {
//                 // Try any other format Carbon can automatically detect
//                 return Carbon::parse($dateString);
//             }
//         }
//     }

//     $from = parseDate($request->from_date);
//     $to   = parseDate($request->to_date);

//     // Rest of your code remains the same...
//     $coming_dates = $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y');


//     $period = CarbonPeriod::create($from, $to);

//     $dates = [];
//     foreach ($period as $date) {
//         $dates[] = $date->format('d/m/Y');
//     }

//     $existingDates = DB::table('income_reconciliation_table')
//         ->where('location_name', $branch)
//         ->whereIn('date_range', $dates)
//         ->pluck('date_range')
//         ->toArray();

//     $missingDates = array_values(array_diff($dates, $existingDates));

//     $totallist = [];   // 👈 prevent undefined var
//     $totalAmount = 0;

//     if (!empty($existingDates)) {

//         $totallist = DB::table('income_reconciliation_table')
//             ->where('location_name', $branch)
//             ->whereIn('date_range', $existingDates)
//             ->select('date_range', $request->column.' as amount')
//             ->orderBy('date_range','asc')
//             ->get();

//         $totalAmount = DB::table('income_reconciliation_table')
//             ->where('location_name', $branch)
//             ->whereIn('date_range', $existingDates)
//             ->sum($request->column);
//     }
//     // dd($totallist);
//     return response()->json([
//         'branch'          => $branch,
//         'coming_dates'    => $coming_dates,
//         'requested_dates' => $dates,
//         'existing_dates'  => $existingDates,
//         'missing_dates'   => $missingDates,
//         'all_exist'       => count($missingDates) === 0,
//         'total_amount'    => $totalAmount,
//         'totallist'       => $totallist,
//     ]);
// }
public function checkDate(Request $request)
{
    $branch = $request->branch_name;
    $column = $request->column; // e.g., 'cash_moc_amt', 'card_moc_amt', etc.

    // Extract the prefix from column name
    $columnPrefix = $this->extractColumnPrefix($column);

    // Function to parse date with multiple formats
    function parseDate($dateString)
    {
        // Try d/m/Y format first
        try {
            return Carbon::createFromFormat('d/m/Y', $dateString);
        } catch (\Exception $e) {
            // Try Y-m-d format if first fails
            try {
                return Carbon::createFromFormat('Y-m-d', $dateString);
            } catch (\Exception $e) {
                // Try any other format Carbon can automatically detect
                return Carbon::parse($dateString);
            }
        }
    }

    $from = parseDate($request->from_date);
    $to   = parseDate($request->to_date);

    // Format dates for display
    $coming_dates = $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y');

    // Generate date range
    $period = CarbonPeriod::create($from, $to);
    $dates = [];
    foreach ($period as $date) {
        $dates[] = $date->format('d/m/Y');
    }
    $dateFilterColumn = $columnPrefix . '_date_filter'; // e.g., 'cash_date_filter'
    if($request->tyle == 1){

        // First, check if ANY date within the range already exists in the date_filter column

        // Get all existing date ranges from the date_filter column for this branch
        $existingDateFilters = DB::table('income_reconciliation_table')
            ->where('location_name', $branch)
            ->whereNotNull($dateFilterColumn)
            ->where($dateFilterColumn, '!=', '')
            ->pluck($dateFilterColumn)
            ->toArray();

        // Parse existing date ranges to extract individual dates
        $existingIndividualDates = [];
        foreach ($existingDateFilters as $dateRange) {
            // Split date range like "22/12/2025 - 27/12/2025"
            $rangeParts = explode(' - ', $dateRange);
            if (count($rangeParts) === 2) {
                try {
                    $rangeFrom = Carbon::createFromFormat('d/m/Y', trim($rangeParts[0]));
                    $rangeTo = Carbon::createFromFormat('d/m/Y', trim($rangeParts[1]));

                    // Create period for this existing range
                    $existingPeriod = CarbonPeriod::create($rangeFrom, $rangeTo);
                    foreach ($existingPeriod as $date) {
                        $existingIndividualDates[] = $date->format('d/m/Y');
                    }
                } catch (\Exception $e) {
                    // Skip invalid date ranges
                    continue;
                }
            }
        }

        // Remove duplicates
        $existingIndividualDates = array_unique($existingIndividualDates);

        // Check if any of our requested dates already exist in date_filter ranges
        $overlappingDates = array_intersect($dates, $existingIndividualDates);

        // If ANY date already exists in date_filter column, return early
        if (!empty($overlappingDates)) {
            return response()->json([
                'status'              => 'exists_in_filter',
                'message'             => 'Some dates already exist in ' . $dateFilterColumn,
                'branch'              => $branch,
                'original_column'     => $column,
                'column_prefix'       => $columnPrefix,
                'coming_dates'        => $coming_dates,
                'date_filter_col'     => $dateFilterColumn,
                'requested_dates'     => $dates,
                'existing_date_ranges' => $existingDateFilters,
                'existing_individual_dates' => $existingIndividualDates,
                'overlapping_dates'   => array_values($overlappingDates),
                'all_exist'           => false,
                'proceed'             => false  // Do not proceed
            ]);
        }
    }

    // Only proceed with original logic if NO dates exist in date_filter column

    // Check for existing dates in date_range column (original logic)
    $existingDates = DB::table('income_reconciliation_table')
        ->where('location_name', $branch)
        ->whereIn('date_range', $dates)
        ->pluck('date_range')
        ->toArray();

    $missingDates = array_values(array_diff($dates, $existingDates));

    $totallist = [];   // 👈 prevent undefined var
    $totalAmount = 0;

    if (!empty($existingDates)) {

        $totallist = DB::table('income_reconciliation_table')
            ->where('location_name', $branch)
            ->whereIn('date_range', $existingDates)
            ->select('date_range', DB::raw($column . ' as amount'))
            ->orderBy('date_range','asc')
            ->get();

        $totalAmount = DB::table('income_reconciliation_table')
            ->where('location_name', $branch)
            ->whereIn('date_range', $existingDates)
            ->sum($column);
    }

    return response()->json([
        'status'          => 'proceed',
        'branch'          => $branch,
        'original_column' => $column,
        'column_prefix'   => $columnPrefix,
        'coming_dates'    => $coming_dates,
        'date_filter_col' => $dateFilterColumn,
        'requested_dates' => $dates,
        'existing_dates'  => $existingDates,
        'missing_dates'   => $missingDates,
        'all_exist'       => count($missingDates) === 0,
        'total_amount'    => $totalAmount,
        'totallist'       => $totallist,
        'proceed'         => true  // Indicates to proceed with remaining steps
    ]);
}

// Helper function to extract column prefix
private function extractColumnPrefix($columnName)
{
    $suffixes = [
        '_moc_amt',
        '_radiant',
        '_bank',
        '_date_filter',
        '_date_amt_filter'
    ];

    foreach ($suffixes as $suffix) {
        if (str_ends_with($columnName, $suffix)) {
            return str_replace($suffix, '', $columnName);
        }
    }

    // If no suffix found, try to extract first part before underscore
    $parts = explode('_', $columnName);
    return $parts[0] ?? $columnName;
}
public function incomeOverviewDateFilter(Request $request)
{
    set_time_limit(0);
    $filterRemovedDataAll = $request->input('morefilltersall');

    if (empty($filterRemovedDataAll)) {
        $filterRemovedDataAll = "tblzones.name='CHENNAI'";
    }
    $dateRange = $request->input('datefiltervalue');
    return $this->overviewdataprocess(
        $filterRemovedDataAll,
        $dateRange
    );
}
public function overviewdata(Request $request)
{
    set_time_limit(0);
    $filterRemovedDataAll = $request->input('morefilltersall');

    if (empty($filterRemovedDataAll)) {
        $filterRemovedDataAll = "tblzones.name='CHENNAI'";
    }
    $dateRange = $request->input('moredatefittervale');
    return $this->overviewdataprocess(
        $filterRemovedDataAll,
        $dateRange
    );
}

// public function overviewdataprocess($filter, $dateRange)
// {
//     set_time_limit(0);

//     // ====== READ ZONE LIST ======
//     preg_match("/tblzones\.name='([^']+)'/", $filter, $zoneMatch);
//     $zones = !empty($zoneMatch[1]) ? explode(',', $zoneMatch[1]) : [];

//     // ====== READ BRANCH LIST ======
//     preg_match("/tbl_locations\.name='([^']+)'/", $filter, $locMatch);
//     $branches = !empty($locMatch[1]) ? explode(',', $locMatch[1]) : [];

//     $query = DB::table('income_reconciliation_table');

//     // ====== DATE FILTER (ONLY IF SENT) ======
//     if (!empty($dateRange)) {

//         [$from, $to] = explode(' - ', $dateRange);

//         $start = Carbon::createFromFormat('d/m/Y', trim($from))->startOfDay();
//         $end   = Carbon::createFromFormat('d/m/Y', trim($to))->endOfDay();

//         $query->whereBetween(
//             DB::raw("STR_TO_DATE(date_range, '%d/%m/%Y')"),
//             [$start, $end]
//         );
//     }

//     if (!empty($zones)) {
//         $query->whereIn('zone_name', array_map('trim', $zones));
//     }

//     if (!empty($branches)) {
//         $query->whereIn('location_name', array_map('trim', $branches));
//     }

//     $data = $query->orderByRaw("STR_TO_DATE(date_range, '%d/%m/%Y') ASC")->get();
//     preg_match("/='(.*?)'/", $filter, $matches);

//     $ilocation = $matches[1] ?? null;   // selected name (zone or location)
//     if (!$ilocation) {
//         // fallback (no match found)
//         $zone_location = TblLocationModel::orderBy('name', 'asc')->get();
//     }
//     else if (strpos($filter, "tblzones.name=") !== false) {
//         $location = TblZonesModel::select('id')
//             ->where('name', $ilocation)
//             ->first();
//         if ($location) {
//             $zone_location = TblLocationModel::where('zone_id', $location->id)
//                 ->orderBy('name', 'asc')
//                 ->get();
//         } else {
//             $zone_location = collect(); // empty result safely
//         }
//     } else {
//         $zone_location = TblLocationModel::orderBy('name', 'asc')->get();
//     }
//     // ====== TOTALS ======
//     $totals = [
//         "type" => "Consolidated",
//         'cash'  => $data->sum('cash_moc_amt'),
//         'upi'   => $data->sum('upi_moc_amt'),
//         'card'  => $data->sum('card_moc_amt'),
//         'neft'  => $data->sum('neft_moc_amt'),
//         'total' => $data->sum('total_moc_amt'),
//     ];
//     $data->push($totals);
//     // dd($data);
//     return response()->json([
//         'data'   => $data,
//         'totals' => $totals,
//         'dropdown' => $zone_location,
//     ]);
// }

/**
 * Concatenate mismatch remarks from income_reconciliation_table and linked bank_statements (income tag).
 *
 * @param  object  $row  income_reconciliation_table row
 */
public function appendIncomeReconciliationBankTagRemarksForOverview(object $row): void
{
    $row->display_bank_income_remarks = '';

    /** @var array<string, string> Ordered unique remark texts (bank lines first, then row-level). */
    $ordered = [];

    $reconId = (int) ($row->id ?? 0);

    if (Schema::hasTable('bank_statements') && Schema::hasColumn('bank_statements', 'income_tag_mismatch_remark') && $reconId > 0) {
        $q = DB::table('bank_statements')
            ->where('income_match_status', 'income_matched')
            ->whereNotNull('income_tag_mismatch_remark')
            ->where('income_tag_mismatch_remark', '!=', '')
            ->where(function ($w) use ($reconId) {
                $w->where('income_reconciliation_id', $reconId)
                    ->orWhere('income_match_split_json', 'like', '%"'.$reconId.'"%')
                    ->orWhere('income_match_split_json', 'like', '%['.$reconId.',%')
                    ->orWhere('income_match_split_json', 'like', '%,'.$reconId.',%')
                    ->orWhere('income_match_split_json', 'like', '%,'.$reconId.']%')
                    ->orWhere('income_match_split_json', 'like', '%['.$reconId.']%')
                    ->orWhere('income_match_split_json', 'like', '%:'.$reconId.',%')
                    ->orWhere('income_match_split_json', 'like', '%:'.$reconId.'}%');
            });

        foreach ($q->orderBy('id')->pluck('income_tag_mismatch_remark') as $r) {
            $t = trim((string) $r);
            if ($t !== '' && ! isset($ordered[$t])) {
                $ordered[$t] = $t;
            }
        }

        if ($ordered === [] && $reconId > 0) {
            $q2 = DB::table('bank_statements')
                ->whereNotNull('income_tag_mismatch_remark')
                ->where('income_tag_mismatch_remark', '!=', '')
                ->where(function ($w) use ($reconId) {
                    $w->where('income_reconciliation_id', $reconId)
                        ->orWhere('income_match_split_json', 'like', '%"'.$reconId.'"%')
                        ->orWhere('income_match_split_json', 'like', '%['.$reconId.',%')
                        ->orWhere('income_match_split_json', 'like', '%,'.$reconId.',%')
                        ->orWhere('income_match_split_json', 'like', '%,'.$reconId.']%')
                        ->orWhere('income_match_split_json', 'like', '%['.$reconId.']%');
                });
            foreach ($q2->orderBy('id')->pluck('income_tag_mismatch_remark') as $r) {
                $t = trim((string) $r);
                if ($t !== '' && ! isset($ordered[$t])) {
                    $ordered[$t] = $t;
                }
            }
        }
    }

    if (Schema::hasTable('bank_statements') && Schema::hasColumn('bank_statements', 'income_tag_mismatch_remark')) {
        $bankIds = [];
        foreach (['cash_bank_id', 'card_upi_bank_id', 'neft_bank_id', 'other_bank_id'] as $col) {
            if (isset($row->$col) && (int) $row->$col > 0) {
                $bankIds[] = (int) $row->$col;
            }
        }
        $bankIds = array_values(array_unique($bankIds));

        if ($bankIds !== []) {
            $rows = DB::table('bank_statements')
                ->whereIn('id', $bankIds)
                ->whereNotNull('income_tag_mismatch_remark')
                ->orderBy('id')
                ->pluck('income_tag_mismatch_remark');

            foreach ($rows as $r) {
                $t = trim((string) $r);
                if ($t !== '' && ! isset($ordered[$t])) {
                    $ordered[$t] = $t;
                }
            }
        }
    }

    if (Schema::hasColumn('income_reconciliation_table', 'income_tag_mismatch_remark')) {
        $t = trim((string) ($row->income_tag_mismatch_remark ?? ''));
        if ($t !== '' && ! isset($ordered[$t])) {
            $ordered[$t] = $t;
        }
    }

    $row->display_bank_income_remarks = implode("\n\n", array_values($ordered));
}

public function overviewdataprocess($filter, $dateRange)
{
    set_time_limit(0);

    preg_match("/tblzones\.name='([^']+)'/", $filter, $zoneMatch);
    $zones = !empty($zoneMatch[1]) ? explode(',', $zoneMatch[1]) : [];

    preg_match("/tbl_locations\.name='([^']+)'/", $filter, $locMatch);
    $branches = !empty($locMatch[1]) ? explode(',', $locMatch[1]) : [];

    $query = DB::table('income_reconciliation_table');

    if (!empty($dateRange)) {

        [$from, $to] = explode(' - ', $dateRange);

        $start = Carbon::createFromFormat('d/m/Y', trim($from))->startOfDay();
        $end   = Carbon::createFromFormat('d/m/Y', trim($to))->endOfDay();

        $query->whereBetween(
            DB::raw("STR_TO_DATE(date_range, '%d/%m/%Y')"),
            [$start, $end]
        );
    }

    if (!empty($zones)) {
        $query->whereIn('zone_name', array_map('trim', $zones));
    }

    if (!empty($branches)) {
        $query->whereIn('location_name', array_map('trim', $branches));
    }

    $data = $query
        ->orderByRaw("STR_TO_DATE(date_range, '%d/%m/%Y') ASC")
        ->get();

    foreach ($data as $rec) {
        $this->appendIncomeReconciliationBankTagRemarksForOverview($rec);
    }

    // -------- MOC DOC TOTAL --------
    $mocTotals = [
        "type"  => "Consolidated",
        'cash'  => $data->sum('moc_cash_amt'),
        'card'  => $data->sum('moc_card_amt'),
        'upi'   => $data->sum('moc_upi_amt'),
        'neft'  => $data->sum('moc_neft_amt'),
        'others'  => $data->sum('moc_other_amt'),
        'total' => $data->sum('moc_overall_total'),
    ];

    // -------- ACTUAL TOTAL --------
    $actualTotals = [
        "type"  => "Actual",
        'cash'  => $data->sum('deposite_amount'),
        'card'  => $data->sum('mespos_card'),
        'upi'   => $data->sum('mespos_upi'),
        'neft'  => $data->sum('bank_neft'),
        'others'  => $data->sum('bank_others'),
        'bank_chargers'  => $data->sum('bank_chargers'),
        'bank_upi_card'  => $data->sum('bank_upi_card'),
        'total' =>
            $data->sum('deposite_amount') +
            $data->sum('mespos_card') +
            $data->sum('mespos_upi') +
            $data->sum('bank_neft') + $data->sum('bank_others'),
    ];

    // // -------- DIFFERENCE TOTAL --------
    // $differenceTotals = [
    //     "type"  => "Difference",
    //     'cash'  => $actualTotals['cash'] - $mocTotals['cash'],
    //     'card'  => $actualTotals['card'] - $mocTotals['card'],
    //     'upi'   => $actualTotals['upi']  - $mocTotals['upi'],
    //     'neft'  => $actualTotals['neft'] - $mocTotals['neft'],
    //     'total' => $actualTotals['total'] - $mocTotals['total'],
    // ];
    $data->push($mocTotals);
    $data->push($actualTotals);
    // $data->push($differenceTotals);

    preg_match("/='(.*?)'/", $filter, $matches);

    $ilocation = $matches[1] ?? null;   // selected name (zone or location)
    if (!$ilocation) {
        // fallback (no match found)
        $zone_location = TblLocationModel::orderBy('name', 'asc')->get();
    }
    else if (strpos($filter, "tblzones.name=") !== false) {
        $location = TblZonesModel::select('id')
            ->where('name', $ilocation)
            ->first();
        if ($location) {
            $zone_location = TblLocationModel::where('zone_id', $location->id)
                ->orderBy('name', 'asc')
                ->get();
        } else {
            $zone_location = collect(); // empty result safely
        }
    } else {
        $zone_location = TblLocationModel::orderBy('name', 'asc')->get();
    }

    // dd($data);
    return response()->json([
        'data'     => $data,
        'totals'   => [
            $mocTotals,
            $actualTotals,
        ],
        'dropdown' => $zone_location,
    ]);
}
public function incomeMonthlyDateFilter(Request $request)
{
    set_time_limit(0);

    $filter = $request->input('morefilltersall');
    if (empty($filter)) {
        $filter = "tblzones.name='CHENNAI'";
    }

    $dateRange = $request->input('datefiltervalue'); // DD/MM/YYYY - DD/MM/YYYY

    return $this->overviewdatamonthly($filter, $dateRange);
}

public function IncomeMontlyReportData(Request $request)
{
    set_time_limit(0);

    $filter = $request->input('morefilltersall');
    if (empty($filter)) {
        $filter = "tblzones.name='CHENNAI'";
    }

    $dateRange = $request->input('moredatefittervale'); // DD/MM/YYYY - DD/MM/YYYY

    return $this->overviewdatamonthly($filter, $dateRange);
}
public function overviewdatamonthly($filter = null, $dateRange = null)
{
    set_time_limit(0);

    // ------------------------------------------------
    // 1. DATE RANGE HANDLING
    // ------------------------------------------------
    if (!empty($dateRange)) {
        [$from, $to] = explode(' - ', $dateRange);

        $start = Carbon::createFromFormat('d/m/Y', trim($from))->startOfDay();
        $end   = Carbon::createFromFormat('d/m/Y', trim($to))->endOfDay();
    } else {
        // fallback → current month
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();
    }

    // ------------------------------------------------
    // 2. ZONE & BRANCH FILTER PARSE
    // ------------------------------------------------
    preg_match("/tblzones\.name='([^']+)'/", $filter, $zoneMatch);
    $zones = !empty($zoneMatch[1])
        ? array_map('trim', explode(',', $zoneMatch[1]))
        : [];

    preg_match("/tbl_locations\.name='([^']+)'/", $filter, $locMatch);
    $branches = !empty($locMatch[1])
        ? array_map('trim', explode(',', $locMatch[1]))
        : [];

    // ------------------------------------------------
    // 3. MAIN QUERY (BRANCH-WISE MONTHLY) - SAME AS BEFORE
    // ------------------------------------------------
    $query = DB::table('income_reconciliation_table')
            ->select(
                'zone_name',
                'location_name',

                // -------- MOC --------
                DB::raw('SUM(moc_cash_amt) as moc_cash'),
                DB::raw('SUM(moc_card_amt) as moc_card'),
                DB::raw('SUM(moc_upi_amt) as moc_upi'),
                DB::raw('SUM(moc_total_upi_card) as moc_total_upi_card'),
                DB::raw('SUM(moc_neft_amt) as moc_neft'),
                DB::raw('SUM(moc_other_amt) as moc_others'),
                DB::raw('SUM(moc_overall_total) as moc_total'),
                // -------- ACTUAL --------
                DB::raw('SUM(deposite_amount) as actual_cash'),
                DB::raw('SUM(bank_chargers) as bank_chargers'),
                DB::raw('SUM(bank_upi_card) as bank_upi_card'),
                DB::raw('SUM(mespos_card) as mespos_card'),
                DB::raw('SUM(mespos_upi) as mespos_upi'),
                DB::raw('SUM(bank_neft) as actual_neft'),
                DB::raw('SUM(bank_others) as actual_others'),

                // -------- DIFFERENCE --------
                DB::raw('SUM(radiant_diff) as cash_diff'),

                DB::raw('
                    SUM(moc_total_upi_card)
                    -
                    (SUM(bank_chargers) + SUM(bank_upi_card))
                    as upi_card_diff
                '),

                DB::raw('
                    SUM(moc_neft_amt)
                    -
                    SUM(bank_neft)
                    as neft_diff
                '),

                DB::raw('
                    SUM(moc_other_amt)
                    -
                    SUM(bank_others)
                    as others_diff
                ')
            )
            ->whereBetween(
                DB::raw("STR_TO_DATE(date_range, '%d/%m/%Y')"),
                [$start, $end]
            );


    // ------------------------------------------------
    // 4. APPLY FILTERS
    // ------------------------------------------------
    if (!empty($zones)) {
        $query->whereIn('zone_name', $zones);
    }

    if (!empty($branches)) {
        $query->whereIn('location_name', $branches);
    }

    // ------------------------------------------------
    // 5. GROUP BY BRANCH
    // ------------------------------------------------
    $data = $query
        ->groupBy('location_name')
        ->orderBy('location_name', 'asc')
        ->get();

    // ------------------------------------------------
    // 6. GET ALL DATES AND REMARKS FOR EACH BRANCH
    // ------------------------------------------------
    $dateRemarksQuery = DB::table('income_reconciliation_table')
        ->select(
            'location_name',
            'date_range',
            'remark'
        )
        ->whereBetween(
            DB::raw("STR_TO_DATE(date_range, '%d/%m/%Y')"),
            [$start, $end]
        );

    // Apply same filters
    if (!empty($zones)) {
        $dateRemarksQuery->whereIn('zone_name', $zones);
    }

    if (!empty($branches)) {
        $dateRemarksQuery->whereIn('location_name', $branches);
    }

    $dateRemarksQuery->orderBy('location_name', 'asc')
        ->orderBy(DB::raw("STR_TO_DATE(date_range, '%d/%m/%Y')"), 'asc');

    $allRecords = $dateRemarksQuery->get();

    // Organize date remarks by branch
    $dateRemarksByBranch = [];

    foreach ($allRecords as $record) {
        $branchName = $record->location_name;

        if (!isset($dateRemarksByBranch[$branchName])) {
            $dateRemarksByBranch[$branchName] = [];
        }

        // Store date and remark
        $dateRemarksByBranch[$branchName][] = [
            'date' => $record->date_range,
            'remark' => $record->remark
        ];
    }

    // ------------------------------------------------
    // 7. FORMAT DATA (EXCEL STYLE) - SAME AS BEFORE
    // ------------------------------------------------
    $rows = collect();

    foreach ($data as $row) {
        $actualTotal =
            $row->actual_cash +
            $row->mespos_card +
            $row->mespos_upi +
            $row->actual_neft +
            $row->actual_others;

        $branchName = $row->location_name;

        $rows->push([
            'Branch' => $branchName,
            'Zone' => $row->zone_name,

            // MOC
            'moc_cash'   => $row->moc_cash,
            'moc_card'   => $row->moc_card,
            'moc_upi'    => $row->moc_upi,
            'moc_total_upi_card'  => $row->moc_total_upi_card,
            'moc_neft'   => $row->moc_neft,
            'moc_others' => $row->moc_others,
            'moc_total'  => $row->moc_total,

            // ACTUAL
            'actual_cash'   => $row->actual_cash,
            'actual_card'   => $row->mespos_card,
            'actual_upi'    => $row->mespos_upi,
            'actual_neft'   => $row->actual_neft,
            'actual_others' => $row->actual_others,
            'bank_chargers' => $row->bank_chargers,
            'bank_upi_card' => $row->bank_upi_card,
            'cash_diff' => $row->cash_diff,
            'upi_card_diff' => $row->upi_card_diff,
            'neft_diff' => $row->neft_diff,
            'others_diff' => $row->others_diff,
            'actual_total'  => $actualTotal,

            // DIFFERENCE
            'diff_total' => $actualTotal - $row->moc_total,

            // ADD DATE REMARKS ARRAY
            'date_remarks' => $dateRemarksByBranch[$branchName] ?? []
        ]);
    }

    // ------------------------------------------------
    // 8. CONSOLIDATED TOTAL ROW
    // ------------------------------------------------
    $rows->push([
        'type' => 'CONSOLIDATED',

        'moc_cash'   => $rows->sum('moc_cash'),
        'moc_card'   => $rows->sum('moc_card'),
        'moc_upi'    => $rows->sum('moc_upi'),
        'moc_neft'   => $rows->sum('moc_neft'),
        'moc_total_upi_card'   => $rows->sum('moc_total_upi_card'),
        'moc_others' => $rows->sum('moc_others'),
        'moc_total'  => $rows->sum('moc_total'),

        'actual_cash'   => $rows->sum('actual_cash'),
        'actual_card'   => $rows->sum('actual_card'),
        'actual_upi'    => $rows->sum('actual_upi'),
        'actual_neft'   => $rows->sum('actual_neft'),
        'actual_others' => $rows->sum('actual_others'),
        'actual_total'  => $rows->sum('actual_total'),

        'bank_chargers'  => $rows->sum('bank_chargers'),
        'bank_upi_card'  => $rows->sum('bank_upi_card'),

        'diff_total' => $rows->sum('actual_total') - $rows->sum('moc_total'),
    ]);

    // ------------------------------------------------
    // 8b. DAILY DATA (for split-up view & date-wise 1-31 report)
    // ------------------------------------------------
    $dailyQuery = DB::table('income_reconciliation_table')
        ->select(
            'date_range',
            'zone_name',
            'location_name',
            'moc_cash_amt',
            'moc_card_amt',
            'moc_upi_amt',
            'moc_total_upi_card',
            'moc_neft_amt',
            'moc_other_amt',
            'moc_overall_total',
            'deposite_amount',
            'bank_chargers',
            'bank_upi_card',
            'mespos_card',
            'mespos_upi',
            'bank_neft',
            'bank_others',
            'cash_utr_number',
            'bank_upi_card_utr',
            'bank_neft_utr',
            'bank_other_utr',
            'deposit_amount_files',
            'mespos_card_files',
            'mespos_upi_files',
            'bank_chargers_files',
            'bank_upi_card_files',
            'bank_neft_files',
            'bank_others_files',
            'cash_utr_files',
            'card_upi_utr_files',
            'neft_utr_files',
            'other_utr_files'
        )
        ->whereBetween(
            DB::raw("STR_TO_DATE(date_range, '%d/%m/%Y')"),
            [$start, $end]
        );

    if (!empty($zones)) {
        $dailyQuery->whereIn('zone_name', $zones);
    }
    if (!empty($branches)) {
        $dailyQuery->whereIn('location_name', $branches);
    }

    $dailyRows = $dailyQuery
        ->orderBy('location_name', 'asc')
        ->orderBy(DB::raw("STR_TO_DATE(date_range, '%d/%m/%Y')"), 'asc')
        ->get();

    $daily_data = $dailyRows->map(function ($r) {
        $actualTotal = ($r->deposite_amount ?? 0) + ($r->mespos_card ?? 0) + ($r->mespos_upi ?? 0)
            + ($r->bank_neft ?? 0) + ($r->bank_others ?? 0);
        return [
            'date_range'       => $r->date_range,
            'zone_name'        => $r->zone_name,
            'location_name'    => $r->location_name,
            'Zone'             => $r->zone_name,
            'Branch'           => $r->location_name,
            'moc_cash_amt'     => (float) ($r->moc_cash_amt ?? 0),
            'moc_card_amt'     => (float) ($r->moc_card_amt ?? 0),
            'moc_upi_amt'      => (float) ($r->moc_upi_amt ?? 0),
            'moc_total_upi_card' => (float) ($r->moc_total_upi_card ?? 0),
            'moc_neft_amt'     => (float) ($r->moc_neft_amt ?? 0),
            'moc_other_amt'    => (float) ($r->moc_other_amt ?? 0),
            'moc_overall_total'=> (float) ($r->moc_overall_total ?? 0),
            'deposite_amount'  => (float) ($r->deposite_amount ?? 0),
            'bank_chargers'    => (float) ($r->bank_chargers ?? 0),
            'bank_upi_card'    => (float) ($r->bank_upi_card ?? 0),
            'mespos_card'      => (float) ($r->mespos_card ?? 0),
            'mespos_upi'       => (float) ($r->mespos_upi ?? 0),
            'bank_neft'        => (float) ($r->bank_neft ?? 0),
            'bank_others'      => (float) ($r->bank_others ?? 0),
            'actual_total'     => $actualTotal,
            'cash_utr_number'  => $r->cash_utr_number ?? null,
            'bank_upi_card_utr'=> $r->bank_upi_card_utr ?? null,
            'bank_neft_utr'    => $r->bank_neft_utr ?? null,
            'bank_other_utr'   => $r->bank_other_utr ?? null,
            'deposit_amount_files'   => $r->deposit_amount_files ?? null,
            'mespos_card_files'      => $r->mespos_card_files ?? null,
            'mespos_upi_files'       => $r->mespos_upi_files ?? null,
            'bank_chargers_files'    => $r->bank_chargers_files ?? null,
            'bank_upi_card_files'   => $r->bank_upi_card_files ?? null,
            'bank_neft_files'        => $r->bank_neft_files ?? null,
            'bank_others_files'      => $r->bank_others_files ?? null,
            'cash_utr_files'         => $r->cash_utr_files ?? null,
            'card_upi_utr_files'     => $r->card_upi_utr_files ?? null,
            'neft_utr_files'         => $r->neft_utr_files ?? null,
            'other_utr_files'        => $r->other_utr_files ?? null,
        ];
    })->values()->all();

    // ------------------------------------------------
    // 9. DROPDOWN DATA
    // ------------------------------------------------
    preg_match("/='(.*?)'/", $filter, $matches);
    $ilocation = $matches[1] ?? null;

    if (!$ilocation) {
        $zone_location = TblLocationModel::orderBy('name')->get();
    }
    elseif (strpos($filter, "tblzones.name=") !== false) {
        $zone = TblZonesModel::where('name', $ilocation)->first();
        $zone_location = $zone
            ? TblLocationModel::where('zone_id', $zone->id)->orderBy('name')->get()
            : collect();
    }
    else {
        $zone_location = TblLocationModel::orderBy('name')->get();
    }

    // ------------------------------------------------
    // 10. FINAL RESPONSE
    // ------------------------------------------------
    return response()->json([
        'from_date'  => $start->format('d/m/Y'),
        'to_date'    => $end->format('d/m/Y'),
        'data'       => $rows,
        'daily_data' => $daily_data,
        'dropdown'   => $zone_location
    ]);
}

public function downloadIncomeRconciliation(Request $request)
{
    // $format = $request->get('format', 'xlsx'); // default to xlsx
    $format = 'csv'; // default to xlsx

    if ($format === 'csv') {
        $fileName = 'Income_Reconciliation_'.now()->format('Y_m_d_His').'.csv';
        $exportType = \Maatwebsite\Excel\Excel::CSV;
    } else {
        $fileName = 'Income_Reconciliation_'.now()->format('Y_m_d_His').'.xlsx';
        $exportType = \Maatwebsite\Excel\Excel::XLSX;
    }

    return Excel::download(
        new IncomeReconciliationExport(
            $request->datefiltervalue,
            $request->filterRemoveData
        ),
        $fileName,
        $exportType
    );
}
public function downloadIncomeRconciliationNew(Request $request)
{
    $format  = $request->get('format', 'xlsx');
    $zones   = array_filter($request->input('zones', []));
    $branches = array_filter($request->input('branches', []));

    if ($format === 'csv') {
        $fileName   = 'Income_Reconciliation_' . now()->format('Y_m_d_His') . '.csv';
        $exportType = \Maatwebsite\Excel\Excel::CSV;
    } else {
        $fileName   = 'Income_Reconciliation_' . now()->format('Y_m_d_His') . '.xlsx';
        $exportType = \Maatwebsite\Excel\Excel::XLSX;
    }

    return Excel::download(
        new IncomeReconciliationExportnew(
            $request->datefiltervalue,
            array_values($zones),
            array_values($branches)
        ),
        $fileName,
        $exportType
    );
}

public function downloadincome_montly_report(Request $request)
{
    $format   = $request->get('format', 'xlsx');
    $zones    = array_filter($request->input('zones', []));
    $branches = array_filter($request->input('branches', []));

    if ($format === 'csv') {
        $fileName   = 'Income_Monthly_Report_' . now()->format('Y_m_d_His') . '.csv';
        $exportType = \Maatwebsite\Excel\Excel::CSV;
    } else {
        $fileName   = 'Income_Monthly_Report_' . now()->format('Y_m_d_His') . '.xlsx';
        $exportType = \Maatwebsite\Excel\Excel::XLSX;
    }

    return Excel::download(
        new IncomeReconciliationMonthlyExport(
            $request->datefiltervalue,
            array_values($zones),
            array_values($branches)
        ),
        $fileName,
        $exportType
    );
}
public function Incometemplate()
    {
        return Excel::download(new IncomeTemplateExportnew, 'Income_template.xlsx');
    }
public function importIncomeExcel(Request $request)
{
     $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    Excel::import(new RadiantIncomeImport, $request->file('file'));

   return response()->json([
        'status' => 'success',
        'message' => 'Income data imported successfully!'
    ]);
}
}
