<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;

class IncomeController extends Controller
{
// public function storeRadiant(Request $request)
// {
//     dd($request);
//      if ($request->has('mocdata') && is_array($request->mocdata) && count($request->mocdata) > 0) {
//         // Loop through each row and store/update
//         $branch = $request->branch_name; // full branch name
//         $column = $request->column;      // column to sum
//         $from   = Carbon::parse($request->from_date);
//         $to     = Carbon::parse($request->to_date);

//         $period = CarbonPeriod::create($from, $to);
//         $dates = [];
//         foreach ($period as $d) {
//             $dates[] = $d->format('d/m/Y');
//         }
//         foreach ($request->mocdata as $row) {
//             $dateRange = Carbon::createFromFormat('Y-m-d', $row['billdate'])->format('d/m/Y');
//             $location = DB::table('tbl_locations')->where('name', $row['zone_name'])->first();
//             $zone = DB::table('tblzones')->where('id', $location->zone_id)->first();

//             DB::table('income_reconciliation_table')
//                 ->updateOrInsert(
//                     [
//                         'zone_name'     => $zone->name,
//                         'location_name' => $row['area'],
//                         'date_range'    => $dateRange,
//                     ],
//                     [
//                         'cash_moc_amt'   => $row['total_cash'] ?? 0,
//                         'card_moc_amt'   => $row['total_card'] ?? 0,
//                         'upi_moc_amt'    => $row['total_upi'] ?? 0,
//                         'neft_moc_amt'   => $row['total_neft'] ?? 0,
//                         'total_moc_amt'  => $row['total_amount'] ?? 0,
//                         'updated_at'     => now(),
//                         'created_at'     => now()
//                     ]
//                 );
//         }

//         // ===== AFTER INSERT: calculate total + list for full range =====
//         $totallist = DB::table('income_reconciliation_table')
//             ->where('location_name', $branch)
//             ->whereIn('date_range', $dates)
//             ->select('date_range', DB::raw("$column as amount"))
//             ->orderBy('date_range')
//             ->get();

//         $totalAmount = DB::table('income_reconciliation_table')
//             ->where('location_name', $branch)
//             ->whereIn('date_range', $dates)
//             ->sum($column);
//         // dd($totallist,$totalAmount);
//         return response()->json([
//             'status'        => 200,
//             'message'       => 'MOC data stored & totals recalculated',
//             'coming_dates'  => $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y'),
//             'totallist'     => $totallist,
//             'total_amount'  => $totalAmount,
//         ]);
//     }else {
//            // ✅ 1. Validate required fields
//             if (!$request->location_name || !$request->date_range) {
//                 return response()->json([
//                     'status'  => 422,
//                     'message' => 'Zone, Location and Date are required'
//                 ]);
//             }
//             // dd($request);
//         // ✅ 2. Prepare data payload
//         $dataPayload = [
//             'zone_name'     => $request->zone_name,
//             'cash_moc_amt'  => $request->cash_mocdoc ?? 0,
//             'cash_radiant'  => $request->cash_radiant ?? 0,
//             'cash_radiant_diff'  => $request->cash_radiant_diff ?? 0,
//             'cash_date_filter'  => $request->cash_date_filter,
//             'cash_date_amt_filter'  => $request->cash_date_amt_filter ?? 0,
//             'cash_bank'     => $request->cash_bank ?? 0,
//             'cash_bank_diff'     => $request->cash_bank_diff ?? 0,
//             'card_moc_amt'  => $request->card_mocdoc ?? 0,
//             'card_radiant'  => $request->card_radiant ?? 0,
//             'card_radiant_diff'  => $request->card_radiant_diff ?? 0,
//             'card_date_filter'  => $request->card_date_filter,
//             'card_date_amt_filter'     => $request->card_date_amt_filter ?? 0,
//             'upi_moc_amt'   => $request->upi_mocdoc ?? 0,
//             'upi_radiant'   => $request->upi_radiant ?? 0,
//             'upi_radiant_diff'   => $request->upi_radiant_diff ?? 0,
//             'upi_date_filter'   => $request->upi_date_filter,
//             'upi_date_amt_filter'      => $request->upi_date_amt_filter ?? 0,
//             'neft_moc_amt'     => $request->neft_mocdoc ?? 0,
//             'neft_bank'     => $request->neft_bank ?? 0,
//             'neft_bank_diff'     => $request->neft_bank_diff ?? 0,
//             'neft_date_filter'     => $request->neft_date_filter,
//             'neft_date_amt_filter'     => $request->neft_date_amt_filter ?? 0,
//             'bank_stmt_charge'     => $request->bank_stmt_charge ?? 0,
//             'bank_stmt_amount'     => $request->bank_stmt_amount ?? 0,
//             'bank_stmt_diff'     => $request->bank_stmt_diff ?? 0,
//             'status'     => 1,
//             'updated_at'    => now(),
//         ];

//         // ✅ 3. Check if record already exists
//         $existing = DB::table('income_reconciliation_table')
//             ->where('location_name', $request->location_name)
//             ->where('date_range', $request->date_range)
//             ->first();

//         if ($existing) {
//             // 🔁 UPDATE
//             DB::table('income_reconciliation_table')
//                 ->where('id', $existing->id)
//                 ->update($dataPayload);

//             $data = DB::table('income_reconciliation_table')
//                 ->where('id', $existing->id)
//                 ->first();
//             // dd($data);
//             return response()->json([
//                 'status'  => 200,
//                 'message' => 'Updated successfully',
//                 'data'    => $data
//             ]);

//         } else {
//             // ➕ INSERT
//             $id = DB::table('income_reconciliation_table')->insertGetId(array_merge([
//                 'zone_name'     => $request->zone_name,
//                 'location_name' => $request->location_name,
//                 'date_range'    => $request->date_range,
//                 'created_at'    => now(),
//             ], $dataPayload));

//             $data = DB::table('income_reconciliation_table')->where('id', $id)->first();
//             // dd($data);
//             return response()->json([
//                 'status'  => 200,
//                 'message' => 'Inserted successfully',
//                 'data'    => $data
//             ]);
//         }
//     }
// }

public function storeRadiant(Request $request)
{
    /* ---------------------------------------------
       1️⃣ BASIC VALIDATION
    --------------------------------------------- */
    if (!$request->location_name || !$request->date_range) {
        return response()->json([
            'status'  => 422,
            'message' => 'Location & Date range are required'
        ]);
    }

    if (empty($request->zone_name)) {

        // Get location
        $location = DB::table('tbl_locations')
            ->where('name', $request->location_name)
            ->first();

        if (!$location) {
            return response()->json([
                'status'  => 404,
                'message' => 'Location not found'
            ]);
        }

        // Get zone using zone_id
        $zone = DB::table('tblzones')
            ->where('id', $location->zone_id)
            ->first();

        if (!$zone) {
            return response()->json([
                'status'  => 404,
                'message' => 'Zone not found'
            ]);
        }

        $request->merge([
            'zone_name' => $zone->name
        ]);
    }
    /* ---------------------------------------------
       2️⃣ HANDLE MULTIPLE NORMAL FILES
    --------------------------------------------- */
    $uploadedFiles = [];
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {

            if (!$file->isValid()) continue;

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('radiant_files'), $filename);

            $uploadedFiles[] = $filename;
        }
    }

    /* ---------------------------------------------
       3️⃣ HANDLE REMARK FILE (SINGLE)
    --------------------------------------------- */
    $remarkFile = null;

    if ($request->hasFile('remark_file')) {

        $file = $request->file('remark_file');

        if ($file->isValid()) {
            $remarkFile = time() . '_remark_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('radiant_files'), $remarkFile);
        }
    }
    // dd($uploadedFiles,$remarkFile);
    /* ---------------------------------------------
       4️⃣ PREPARE DATA PAYLOAD
    --------------------------------------------- */
    $data = [
        'zone_name'              => $request->zone_name,
        'location_name'          => $request->location_name,
        'date_range'             => $request->date_range,

        'moc_cash_amt'           => $request->cash_mocdoc ?? 0,
        'moc_card_amt'           => $request->card_mocdoc ?? 0,
        'moc_upi_amt'            => $request->upi_mocdoc ?? 0,
        'moc_total_upi_card'     => $request->total_upi_card ?? 0,
        'moc_neft_amt'           => $request->neft_mocdoc ?? 0,
        'moc_other_amt'          => $request->other_mocdoc ?? 0,
        'moc_overall_total'      => $request->total_moc ?? 0,

        'date_collection'        => $request->date_collection,
        'collection_amount'     => $request->collection_amount,
        'date_deposited'         => $request->date_deposited,
        'deposite_amount'        => $request->deposite_amount,
        'cash_utr_number'        => $request->cash_utr_number,

        'mespos_card'            => $request->mespos_card,
        'mespos_upi'             => $request->mespos_upi,
        'date_settlement'        => $request->date_settlement,

        'bank_chargers'          => $request->bank_chargers,
        'bank_upi_card'          => $request->bank_upi_card,
        'bank_neft'              => $request->bank_neft,
        'bank_others'            => $request->bank_others,
        'ban_upi_card_utr'            => $request->bank_upi_card_utr,
        'bank_neft_utr'            => $request->bank_neft_utr,
        'bank_other_utr'            => $request->bank_other_utr,

        'radiant_diff'           => $request->radiant_diff ?? 0,
        'cash_diff'              => $request->cash_diff ?? 0,
        'card_upi_diff'          => $request->card_upi_diff ?? 0,
        'neft_others_diff'       => $request->neft_others_diff ?? 0,

        'remark'                 => $request->remark,
        'remark_files'           => $remarkFile,
        'deposite_amount_attachment'         => !empty($uploadedFiles) ? json_encode($uploadedFiles) : null,

        'updated_at'             => now(),
    ];

    /* ---------------------------------------------
       5️⃣ INSERT OR UPDATE
    --------------------------------------------- */
    $existing = DB::table('income_reconciliation_table')
        ->where('location_name', $request->location_name)
        ->where('date_range', $request->date_range)
        ->first();

    if ($existing) {

        DB::table('income_reconciliation_table')
            ->where('id', $existing->id)
            ->update($data);

        return response()->json([
            'status'  => 200,
            'message' => 'Updated successfully',
            'files' => $uploadedFiles,        // multiple files
            'remark_file' => $remarkFile      // single file
        ]);
    }

    DB::table('income_reconciliation_table')->insert(
        array_merge($data, ['created_at' => now()])
    );

    return response()->json([
        'status'  => 200,
        'message' => 'Inserted successfully',
        'files' => $uploadedFiles,        // multiple files
        'remark_file' => $remarkFile      // single file
    ]);
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
    // dd($request);
    $columnMap = [
        'cash_radiant' => 'cash_radiant_file',
        'cash_bank'    => 'cash_bank_file',
        'card_radiant' => 'card_radiant_file',
        'upi_radiant'  => 'upi_radiant_file',
        'upi_bank'     => 'upi_bank_file',
        'neft_bank'    => 'neft_bank_file',
        'bank_stmt_charge'    => 'bank_stmt_charge_file',
        'bank_stmt_amount'    => 'bank_stmt_amount_file',
    ];
    $remarkMap = [
    'cash_radiant' => 'cash_radiant_remark',
    'cash_bank'    => 'cash_bank_remark',
    'card_radiant' => 'card_radiant_remark',
    'upi_radiant'  => 'upi_radiant_remark',
    'bank_stmt_charge'     => 'bank_stmt_charge_remark',
    'bank_stmt_amount'     => 'bank_stmt_amount_remark',
    'neft_bank'    => 'neft_radiant_remark',
];


    if (!$request->hasFile('file') || !isset($columnMap[$request->field])) {
        return response()->json(['status' => 400, 'msg' => 'Invalid request']);
    }

    $file = $request->file('file');

    $filename = time().'_'.$file->getClientOriginalName();

    $path = $file->move(public_path('radiant_files'), $filename);
    $remark = trim($request->remark ?? '');



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
                $remarkMap[$request->field] => $remark,
                'updated_at' => now()
            ]);
    } else {
        // ➕ INSERT
        DB::table('income_reconciliation_table')
            ->insert([

                'location_name' => $location,
                'date_range' => $date,

                $columnMap[$request->field] => $filename,
                $remarkMap[$request->field] => $remark,
                // default amounts
                'cash_radiant' => 0,
                'cash_bank' => 0,
                'card_radiant' => 0,
                'upi_radiant' => 0,
                'bank_stmt_charge' => 0,
                'bank_stmt_amount' => 0,
                'neft_bank' => 0,

                'created_at' => now(),
                'updated_at' => now()
            ]);
    }

    return response()->json([
        'status' => 200,
        'path' => $filename,
        'remark' => $remark,
    ]);
}
public function verify(Request $request)
{
    $branch = $request->branch;
    $date   = $request->date;

    $data=DB::table('income_reconciliation_table')
        ->where('location_name', $branch)
        ->where('date_range', $date) // ✅ NOT whereIn
        ->update([
            'verified_status' => 1
        ]);
        if($data){
            return response()->json([
                'status' => true,
                'message' => 'Verified successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'No Data Found'
            ]);

        }
}



}
