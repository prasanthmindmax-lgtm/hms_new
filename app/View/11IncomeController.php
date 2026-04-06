<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;

class IncomeController extends Controller
{
 
public function storeRadiant(Request $request)
{

    DB::table('income_reconciliation_table')->insert([
        'zone_name'      => $request->zone_name,
        'location_name'  => $request->location_name,
        'date_range'     => $request->date_range,

        'cash_radiant'   => $request->cash_radiant ?? 0,
          'cash_bank'   => $request->cash_bank ?? 0,
        'card_radiant'   => $request->card_radiant ?? 0,   // FIX
         'card_bank'   => $request->card_bank ?? 0,   // FIX
        'upi_radiant'    => $request->upi_radiant ?? 0,    // FIX
         'upi_bank'    => $request->upi_bank ?? 0,    // FIX
        'neft_bank'      => $request->neft_bank ?? 0,      // FIX

        'created_at'     => now(),
        'updated_at'     => now(),
    ]);
    return response()->json(['status' => 200, 'message' => 'OK']);
}


   
    // private function fetchRadiantValues($zone, $location, $dateRange)
    // {
    //     if (!$zone || !$location || !$dateRange) {
    //         return null;
    //     }

    //     return DB::table('income_reconciliation_table')
    //         ->where('zone_name', $zone)
    //         ->where('location_name', $location)
    //         ->where('date_range', $dateRange)
    //         ->first();
    // }
  public function fetchRadiant(Request $request)
    {
        $zone      = $request->zone_name;
        $location  = $request->location_name;
        $date      = $request->date_range;

        if (!$zone || !$location || !$date) {
            return response()->json(['status' => 400, 'data' => null]);
        }

        $data = DB::table('income_reconciliation_table')
            ->where('zone_name', $zone)
            ->where('location_name', $location)
            ->where('date_range', $date)
            ->first();

        return response()->json(['status' => 200, 'data' => $data]);
    }

   

}
