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
    // ✅ 1. Validate required fields
    if (!$request->zone_name || !$request->location_name || !$request->date_range) {
        return response()->json([
            'status'  => 422,
            'message' => 'Zone, Location and Date are required'
        ]);
    }

    // ✅ 2. Prepare data payload
    $dataPayload = [
        'zone_name'     => $request->zone_name,

        'cash_radiant'  => $request->cash_radiant ?? 0,
        'cash_bank'     => $request->cash_bank ?? 0,

        'card_radiant'  => $request->card_radiant ?? 0,
        'card_bank'     => $request->card_bank ?? 0,

        'upi_radiant'   => $request->upi_radiant ?? 0,
        'upi_bank'      => $request->upi_bank ?? 0,

        'neft_bank'     => $request->neft_bank ?? 0,

        'updated_at'    => now(),
    ];

    // ✅ 3. Check if record already exists
    $existing = DB::table('income_reconciliation_table')
        ->where('location_name', $request->location_name)
        ->where('date_range', $request->date_range)
        ->first();

    if ($existing) {

        // 🔁 UPDATE
        DB::table('income_reconciliation_table')
            ->where('id', $existing->id)
            ->update($dataPayload);

        $data = DB::table('income_reconciliation_table')
            ->where('id', $existing->id)
            ->first();

        return response()->json([
            'status'  => 200,
            'message' => 'Updated successfully',
            'data'    => $data
        ]);

    } else {

        // ➕ INSERT
        $id = DB::table('income_reconciliation_table')->insertGetId(array_merge([
            'zone_name'     => $request->zone_name,
            'location_name' => $request->location_name,
            'date_range'    => $request->date_range,
            'created_at'    => now(),
        ], $dataPayload));

        $data = DB::table('income_reconciliation_table')->where('id', $id)->first();

        return response()->json([
            'status'  => 200,
            'message' => 'Inserted successfully',
            'data'    => $data
        ]);
    }


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
        // $zone      = $request->zone_name;
        $location  = $request->location_name;
        $date      = $request->date_range;

        if (!$location || !$date) {
            return response()->json(['status' => 400, 'data' => null]);
        }

        $data = DB::table('income_reconciliation_table')
            ->where('location_name', $location)
            ->where('date_range', $date)
            ->first();

        return response()->json(['status' => 200, 'data' => $data]);
    }

public function uploadFile(Request $request)
{
    $columnMap = [
        'cash_radiant' => 'cash_radiant_file',
        'cash_bank'    => 'cash_bank_file',
        'card_radiant' => 'card_radiant_file',
        'card_bank'    => 'card_bank_file',
        'upi_radiant'  => 'upi_radiant_file',
        'upi_bank'     => 'upi_bank_file',
        'neft_bank'    => 'neft_bank_file',
    ];

    if (!$request->hasFile('file') || !isset($columnMap[$request->field])) {
        return response()->json(['status' => 400, 'msg' => 'Invalid request']);
    }

    $file = $request->file('file');

    $filename = time().'_'.$file->getClientOriginalName();

    $path = $file->move(public_path('radiant_files'), $filename);



    $location = trim($request->location_name);
    $date     = trim($request->date_range);

    // Check if record exists
    $row = DB::table('income_reconciliation_table')

        ->where('location_name', $location)
        ->where('date_range', $date)
        ->first();

    if ($row) {
        // 🔁 UPDATE
        DB::table('income_reconciliation_table')
            ->where('id', $row->id)
            ->update([
                $columnMap[$request->field] => $filename,
                'updated_at' => now()
            ]);
    } else {
        // ➕ INSERT
        DB::table('income_reconciliation_table')
            ->insert([

                'location_name' => $location,
                'date_range' => $date,

                $columnMap[$request->field] => $filename,

                // default amounts
                'cash_radiant' => 0,
                'cash_bank' => 0,
                'card_radiant' => 0,
                'card_bank' => 0,
                'upi_radiant' => 0,
                'upi_bank' => 0,
                'neft_bank' => 0,

                'created_at' => now(),
                'updated_at' => now()
            ]);
    }

    return response()->json([
        'status' => 200,
        'path' => $filename
    ]);
}


}
