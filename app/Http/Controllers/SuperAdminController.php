<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\doctordetails;
use App\Models\documentdetails;
use App\Models\usermanagementdetails;
use App\Models\Campmanagement;
use App\Models\Activitiesmanage;
use App\Models\Expensemanagement;
use App\Models\meetingdetails;
use App\Models\patientdetails;
use App\Models\incomedetails;
use App\Models\billinglistdetails;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use App\Models\VehicleDetails;
use App\Models\VehicleDocument;
use App\Models\VehicleType;
use App\Models\SubCategoryModel;
use App\Models\TicketActivitiesModel;
use App\Models\AdminUserDepartments;
use App\Models\TblUserDepartments;
use App\Models\TicketActivityModel;
use App\Models\TicketDetails;
use App\Models\TblRegularAudit;
use App\Models\ImageModel;
use App\Models\StatusModel;
use App\Models\PriorityModel;
use App\Models\CategoryModel;
use App\Models\LocationModel;
use App\Models\TicketChat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\securitydetailsModel;
use App\Models\attendancedetailsModel;
use App\Models\VehicleInsurance;
use App\Models\VehicleServiceDetails;
use App\Models\TravelBooking;
use App\Models\TblNEFTmodule;
use App\Models\DoctypenameModel;
use App\Models\CheckinModel;
use App\Models\TblTreamentCategory;
use App\Models\InvestigationsModel;
use App\Models\InvestHysteroToPodModel;
use App\Models\InvestUltrasongramToHysteroscopytModel;
use App\Models\InvestEndometrialbiopsyToSonohysterogram;
use App\Models\InvestAppearanceToMobility;
use App\Models\InvestiMorphology;
use App\Models\InvestMaleLocalToVasModel;
use App\Models\InvestSemenToPenileDopplerModel;
use App\Models\InvestFemaleFactorsBloodGroupModel;
use App\Models\CancelbillFormModel;
use App\Models\DiscountFormModel;
use App\Models\RefundFormModel;
use App\Models\RefundFormsignModel;
use App\Models\CampLeadsModel;
use App\Models\CampActivityModel;
use App\Models\Tblpurchase;
use App\Models\Tblvendor;
use App\Models\TblDepartmentsUser;
use App\Models\User;

//new//
use Carbon\Carbon;
use DataTables;
use DB;
use DateTime;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $admin = auth()->user();
        return view('superadmin.dashboard', ['admin' => $admin]);
    }
    // public function referral()
    // {
    //     $admin = auth()->user();
    //     //dd($admin);
    //     return view('superadmin.referral', ['admin' => $admin]);
    // }
        public function referral()
    {
        $admin = auth()->user();
        $locations = TblLocationModel::all();
        // dd($location);
        // dd($admin);
        return view('superadmin.referral', ['admin' => $admin ,'locations' => $locations]);
    }
    public function documentmanagement()
    {
        $admin = auth()->user();
        return view('superadmin.document-management', ['admin' => $admin]);
    }
    public function camp()
    {
        $admin = auth()->user();
        return view('superadmin.camp', ['admin' => $admin]);
    }
    public function usermanagent()
    {
        $admin = auth()->user();
        return view('superadmin.usermanagent', ['admin' => $admin]);
    }
    public function Income_reconciliation()
    {
        $admin = auth()->user();
        return view('superadmin.Income_reconciliation', ['admin' => $admin]);
    }
    public function billlistoverall()
    {
        $admin = auth()->user();
        return view('superadmin.bill_list', ['admin' => $admin]);
    }
    public function bill_overall_list()
    {
        $admin = auth()->user();
        // $billid = $request->query('billid');
        return view('superadmin.bill_overall_list', compact('admin'));
    }

    public function patientdashboard()
    {
        $admin = auth()->user();
        $phid = request()->query('phid');
        $phid_id = CheckinModel::where('phid', $phid)->first();
        // echo "<pre>";print_r($phid_id);exit;
        return view('superadmin.patientdashboard', compact('admin','phid_id'));
    }

    public function Daily_summary()
    {
        $admin = auth()->user();
        return view('superadmin.Daily_summary', compact('admin'));
    }

    public function adminDailyDocument()
    {
        $admin = auth()->user();
        return view('superadmin.admindaily_document', ['admin' => $admin]);
    }
    public function securityDailyDocument()
    {
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        $locations = TblLocationModel::orderBy('name', 'asc')->get();
        $securitydata = securitydetailsModel::orderBy('sec_id', 'asc')->where('status', 1)->get();
        return view('superadmin.securitydaily_document', compact('admin', 'locations', 'securitydata'));
    }

    public function dailyAttendanceDocument()
    {
        $admin = auth()->user();
        return view('superadmin.dailyattendance_document', ['admin' => $admin]);
    }
    public function discountDocument()
    {
        $admin = auth()->user();
        return view('superadmin.discountform_document', ['admin' => $admin]);
    }
    public function discountDocumentNew()
    {
        $admin = auth()->user();
        return view('superadmin.discount_dashboard', ['admin' => $admin]);
    }
    public function generalDocument()
    {
        $admin = auth()->user();
        return view('superadmin.general_document', ['admin' => $admin]);
    }

    public function campupdatedversion()
    {
        $admin = auth()->user();
        return view('superadmin.campupdatedversion', ['admin' => $admin]);
    }


    public function doctoradded(Request $request)
    {
        $validatedData = $request->validate([
            'doctor_name' => 'required|string|max:255',
            'empolyee_name' => 'required|string|max:255',
            'special' => 'required|string|max:255',
            'hopsital_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|integer|max:255',
            'doc_contact' => 'required|string|max:255',
            'hpl_contact' => 'required|string|max:255',
            'images.*' => 'required|nullable|image|mimes:jpeg,png,jpg,gif', // Validate images
            'hospital_link' => 'required|string|max:255',
            'map_link' => 'required|string|max:255',
        ]);
        $imagePaths = [];
        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('doctor_images'); // Path to public/uploads/doctor_images
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $imagePaths[] = 'doctor_images/' . $filename; // Save relative path
            }
        }
        $doctor = doctordetails::create(array_merge($validatedData, [
            'image_paths' => json_encode($imagePaths),
        ]));
        return response()->json(['success' => true, 'message' => 'Doctor saved successfully!']);
    }
   
public function fetch(Request $request)
{
    $user = auth()->user();
    $userid = $user->id;
    $user_name = $user->username;



    $accessLog = DB::table('access_log')
                 ->where('employee_id', $userid)
                 ->first();


                //  dd($accessLog);

    if (!$accessLog) {
        return response()->json(['error' => 'Access privileges not found'], 403);
    }

    $access_limit = $accessLog->access_limits;
    $access_heads = $user->access_heads;


     $doctordetails = doctordetails::query()
        ->join('tbl_locations', 'ref_doctor_details.city', '=', 'tbl_locations.id')
        ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->join('users', 'users.username', '=', 'ref_doctor_details.empolyee_name')
       ->select('tbl_locations.name as location_name', 'tblzones.name as zone_name', 'ref_doctor_details.*', 'user_fullname');

        $ref_doctor_id = $request->input('ref_doctor_id');

        if($ref_doctor_id != ""){
        $doctordetails->where('ref_doctor_details.id', $ref_doctor_id);
        }

    switch ($access_limit) {
        case 1:
            break;

        case 2:
       $doctordetails->where(function($query) use ($user,$user_name) {
                            $query->whereIn('ref_doctor_details.empolyee_name', function($subQuery) use ($user) {
                                $subQuery->select('username')
                                        ->from('users')
                                        ->where('reporting_manager', $user->id);
    })
    ->orWhere('ref_doctor_details.empolyee_name', $user_name);
});

            break;

        case 3:
           $subQuery = DB::table('users')
                      ->select('username')
                        ->where('id', $user->id);

                    $doctordetails->whereIn('ref_doctor_details.empolyee_name', $subQuery);
            break;

        default:
            return response()->json(['error' => 'Unauthorized access'], 403);
    }

    $results = $doctordetails->orderBy('ref_doctor_details.created_at', 'desc')->get();

    return response()->json($results);
}
public function fetchfitter(Request $request)
{
    $userids = auth()->user()->username;
    $user = auth()->user();
    $userid = $user->id;
    $accessLog = DB::table('access_log')
            ->where('employee_id', $userid)
            ->first();
    $access_limit = $accessLog->access_limits;
    $fitterremovedata = $request->input('fitterremovedata');
    $datefiltervalue = $request->input('datefiltervalue');
    $dates = explode(' - ', $datefiltervalue);
    $startDate = $dates[0];
    $endDateview = $dates[1];
    $endDate = substr($endDateview, 0, 10);
    $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
    $startdates = $startDateFormatted . " 00:00:00";
    $enddates = $endDateFormatted . " 23:59:59";

    $zoneFilter = null;
    $branchFilter = null;
    $userFilter = null;
    $specialFilter = null;
    $zonalheadFilter = null;

    if ($fitterremovedata) {
        foreach ($fitterremovedata as $condition) {
            if (!str_contains($condition, '=')) continue;

            [$column, $value] = explode('=', $condition, 2);
            $column = trim($column, " '");
            $value = trim($value, " '\"");

            switch ($column) {
                case 'zone_name':
                    $zone = DB::table('tblzones')->where('name', $value)->first();
                    if ($zone) $zoneFilter = $zone->id;
                    break;

                case 'branch_name':
                    $branch = DB::table('tbl_locations')->where('name', $value)->first();
                    if ($branch) $branchFilter = $branch->id;
                    break;

                case 'userfullname':
                    $user_name = DB::table('users')->where('user_fullname', $value)->first();
                    if ($user_name) $userFilter = $user_name->id;
                    break;

                case 'special':
                    $specialFilter = $value;
                    break;

                case 'zonal_head':
                    $zonalhead_name = DB::table('users')->where('user_fullname', $value)->first();
                    if ($zonalhead_name) $zonalheadFilter = $zonalhead_name->id;
                    break;
            }
        }
    }


        $query = doctordetails::query()
            ->join('tbl_locations', 'ref_doctor_details.city', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->join('users', 'ref_doctor_details.empolyee_name', '=', 'users.username')
            ->whereBetween('ref_doctor_details.created_at', [$startDateFormatted, $endDateFormatted])
         ->select('tbl_locations.name as location_name', 'tblzones.name as zone_name', 'ref_doctor_details.*', 'user_fullname');



    switch ($access_limit) {
        case 1:
            break;

        case 2:
            $query->where(function($query) use ($user) {
                $query->whereIn('ref_doctor_details.empolyee_name', function($subQuery) use ($user) {
                    $subQuery->select('username')
                    ->from('users')
                    ->where('reporting_manager', $user->id);
                })
                ->orWhere('ref_doctor_details.empolyee_name', $user->username);
            });
            break;

        case 3:
            $subQuery = DB::table('users')
                      ->select('username')
                      ->where('id', $user->id);
            $query->whereIn('ref_doctor_details.empolyee_name', $subQuery);
            break;

        default:
            return response()->json(['error' => 'Unauthorized access'], 403);
    }
    if ($zoneFilter) {
        $query->where('tblzones.id', $zoneFilter);
    }
    if ($branchFilter) {
        $query->where('tbl_locations.id', $branchFilter);
    }
    if ($userFilter) {
        $query->where('users.id', $userFilter);
    }
    if ($zonalheadFilter) {
        $query->where(function($query) use ($zonalheadFilter) {
            $query->whereIn('ref_doctor_details.empolyee_name', function($subQuery) use ($zonalheadFilter) {
                $subQuery->select('username')
                ->from('users')
                ->where('reporting_manager', $zonalheadFilter);
            })
            ->orWhere('ref_doctor_details.empolyee_name', DB::table('users')->where('id', $zonalheadFilter)->value('username'));
        });
    }

    if ($specialFilter) {
        if ($specialFilter === 'Others') {
            $excludedSpecials = [
                'ALLOPATHY DR', 'VHN', 'ALLOPATHY HOSPITAL',
                'ALLOPATHY CLINIC', 'AYUSH CLINIC', 'AYUSH DR', 'AGENT'
            ];
            $query->whereNotIn('ref_doctor_details.special', $excludedSpecials);
        } else {
            $query->where('ref_doctor_details.special', $specialFilter);
        }
    }

    $data = $query->get();
    return response()->json($data);
}
public function fetchmorefitter(Request $request)
{
    $userids = auth()->user()->username;
     $user = auth()->user();
     $userid = $user->id;
     $accessLog = DB::table('access_log')
             ->where('employee_id', $userid)
             ->first();
    $access_limit = $accessLog->access_limits;
    $fitterremovedata = $request->input('fitterremovedata');
    $moredatefittervale = $request->input('moredatefittervale');
    $dates = explode(' - ', $moredatefittervale);
    $startDate = $dates[0];
    $endDateview = $dates[1];
    $endDate = substr($endDateview, 0, 10);
    $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
    $startdates = $startDateFormatted . " 00:00:00";
    $enddates = $endDateFormatted . " 23:59:59";

    $zoneFilter = null;
    $branchFilter = null;
    $userFilter = null;
    $specialFilter = null;
    $zonalheadFilter = null;
    $zonalheadname = null;

   if($fitterremovedata!="" && $fitterremovedata!=null){

      foreach ($fitterremovedata as $condition) {
            if (!str_contains($condition, '=')) continue;

            [$column, $value] = explode('=', $condition, 2);
            $column = trim($column, " '");
            $value = trim($value, " '\"");

            switch ($column) {
                case 'zone_name':
                    $zone = DB::table('tblzones')->where('name', $value)->first();
                    if ($zone) $zoneFilter = $zone->id;
                    break;

                case 'branch_name':
                    $branch = DB::table('tbl_locations')->where('name', $value)->first();
                    if ($branch) $branchFilter = $branch->id;
                    break;

                case 'userfullname':

                    $user_name = DB::table('users')->where('user_fullname', $value)->first();
                    if ($user_name) $userFilter = $user_name->id;
                    break;

                case 'special':
                $specialFilter = $value;
                break;

                case 'zonal_head':

                    $zonalhead_name = DB::table('users')->where('user_fullname', $value)->first();
                if ($zonalhead_name) {
                $zonalheadFilter = $zonalhead_name->id;
                $zonalheadname = $zonalhead_name->username;
            }
                    break;


            }
        }

   }



    $query = doctordetails::query()
        ->leftjoin('tbl_locations', 'ref_doctor_details.city', '=', 'tbl_locations.id')
        ->leftjoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->leftjoin('users', 'ref_doctor_details.empolyee_name', '=', 'users.username')
        ->whereBetween('ref_doctor_details.created_at', [$startDateFormatted, $endDateFormatted])
     ->select('tbl_locations.name as location_name', 'tblzones.name as zone_name', 'ref_doctor_details.*', 'user_fullname');



        switch ($access_limit) {
                case 1:
                    break;

                // case 2:
                //         $query->where(function($query) use ($user) {
                //             $query->whereIn('ref_doctor_details.empolyee_name', function($subQuery) use ($user) {
                //                 $subQuery->select('username')
                //                 ->from('users')
                //                 ->where('reporting_manager', $user->id);
                //             })
                //             ->orWhere('ref_doctor_details.empolyee_name', $user->id);
                //         });

                //     break;
                case 2:
                    $branchIds = [];
            
                    if (!empty($user->zone_id)) {
                        $zoneBranchIds = DB::table('tbl_locations')
                            ->where('zone_id', $user->zone_id)
                            ->pluck('id')
                            ->toArray();
            
                        $branchIds = array_merge($branchIds, $zoneBranchIds);
                    }
            
                    if (!empty($user->multi_location)) {
                        $multiLocationIds = array_map(
                            'intval',
                            explode(',', $user->multi_location)
                        );
            
                        $branchIds = array_merge($branchIds, $multiLocationIds);
                    }
            
                    $branchIds = array_unique($branchIds);
            
                    if (!empty($branchIds)) {
                        $query->whereIn('ref_doctor_details.city', $branchIds);
                    }
            
                    break;

                case 3:
                        $subQuery = DB::table('users')
                                    ->select('username')
                                        ->where('id', $user->id);

                        $query->whereIn('ref_doctor_details.empolyee_name', $subQuery);
                break;

                default:
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
    if ($zoneFilter) {
        $query->where('tblzones.id', $zoneFilter);
    }

    if ($branchFilter) {
        $query->where('tbl_locations.id', $branchFilter);
    }

    if ($userFilter) {

        $query->where('users.id', $userFilter);
    }

     if ($specialFilter) {
        if ($specialFilter === 'Others') {
        $excludedSpecials = [
                'ALLOPATHY DR', 'VHN', 'ALLOPATHY HOSPITAL',
                'ALLOPATHY CLINIC', 'AYUSH CLINIC', 'AYUSH DR', 'AGENT'
            ];
            $query->whereNotIn('ref_doctor_details.special', $excludedSpecials);
        } else {
            $query->where('ref_doctor_details.special', $specialFilter);
        }
    }
}
    public function fetchmorefitterdate(Request $request)
    {
        $userids = auth()->user()->username;
        $access_limit = auth()->user()->access_limits;

        $fitterremovedata = $request->input('fitterremovedata');
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = doctordetails::query()
            ->join('branches', 'ref_doctor_details.city', '=', 'branches.id')
            ->join('users', 'users.username', '=', 'ref_doctor_details.empolyee_name')
            ->join('zones', 'branches.zone_id', '=', 'zones.id')
            ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*','user_fullname');

                  // Apply conditions based on access limit
        if ($access_limit == 1) {
            $query->whereBetween('ref_doctor_details.created_at', [$startdates, $enddates]);
        } else {
            $query->whereBetween('ref_doctor_details.created_at', [$startdates, $enddates])
                ->where('ref_doctor_details.empolyee_name', $userids);
        }


        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function fetchmorefitterremove(Request $request)
    {
        $userids = auth()->user()->username;
        $access_limit = auth()->user()->access_limits;

        $fitterremovedataall = $request->input('fitterremovedataall');
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        // Start the query
        $query = doctordetails::query()
            ->join('branches', 'ref_doctor_details.city', '=', 'branches.id')
            ->join('users', 'users.username', '=', 'ref_doctor_details.empolyee_name')
            ->join('zones', 'branches.zone_id', '=', 'zones.id')
            ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*','user_fullname');

            if ($access_limit == 1) {
                $query->whereBetween('ref_doctor_details.created_at', [$startdates, $enddates]);
            } else {
                $query->whereBetween('ref_doctor_details.created_at', [$startdates, $enddates])
                    ->where('ref_doctor_details.empolyee_name', $userids);
            }

        if (is_array($fitterremovedataall)) {
            $fitterremovedataall = implode(' AND ', $fitterremovedataall);
        } elseif (!is_string($fitterremovedataall)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }

        // Split conditions by 'AND' and loop through them
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function fetchmorefitterdateclr(Request $request)
    {
        // dd($request);
        $userids = auth()->user()->username;
        $access_limit = auth()->user()->access_limits;

        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        // Start the query
        $query = doctordetails::query()
            ->join('branches', 'ref_doctor_details.city', '=', 'branches.id')
            ->join('users', 'users.username', '=', 'ref_doctor_details.empolyee_name')
            ->join('zones', 'branches.zone_id', '=', 'zones.id')
            ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*','user_fullname');

            if ($access_limit == 1) {
                $query->whereBetween('ref_doctor_details.created_at', [$startdates, $enddates]);
            } else {
                $query->whereBetween('ref_doctor_details.created_at', [$startdates, $enddates])
                    ->where('ref_doctor_details.empolyee_name', $userids);
            }

        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function doctordetailsedit(Request $request)
    {
         $editsArray = $request->input('editsArray');
        $idviews = $request->input('idviews');
        // Initialize an empty associative array
        $parsedData = [];
        // Iterate through the editsArray and convert each string to a key-value pair
        foreach ($editsArray as $edit) {
            // Split each string by '=' to separate the key and value
            $parts = explode('=', $edit);
            if (count($parts) == 2) {
                // Remove any quotes and add to the associative array
                $key = trim($parts[0], " \t\n\r\0\x0B'"); // Trim any extra spaces or quotes
                $value = trim($parts[1], " \t\n\r\0\x0B'"); // Trim extra spaces or quotes
                $parsedData[$key] = $value;
            }
        }
        // Perform the update operation
        $updated = doctordetails::where('id', $idviews)->update($parsedData);
        // Return response based on the result of the update
        if ($updated) {
            return response()->json(['success' => 'Doctor details updated successfully']);
        } else {
            return response()->json(['error' => 'Update failed or no changes made'], 400);
        }
    }
    public function doctordetailsid(Request $request)
    {
        $idviews = $request->input('idviews');
        // Start the query
        $query = doctordetails::query()
            ->join('branches', 'ref_doctor_details.city', '=', 'branches.id')
            ->join('users', 'users.username', '=', 'ref_doctor_details.empolyee_name')
            ->join('zones', 'branches.zone_id', '=', 'zones.id')
            ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*','user_fullname')
            ->where('ref_doctor_details.id', [$idviews]);
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function meetingid(Request $request)
    {
        $meetingvalue = $request->input('meetingvalue');
        $query = doctordetails::query();
        // Add whereBetween for created_at
        $query->where('id', [$meetingvalue]);
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function meetinginsert(Request $request)
    {
        // Get the input array
        $meetinginsertvalue = $request->input('meetinginsertvalue');
        // Convert the array values to an associative array
        $data = [];
        foreach ($meetinginsertvalue as $item) {
            // Split the string into key-value pairs
            [$key, $value] = explode('=', $item);
            $data[$key] = trim($value, "'");
        }
        //dd($data);
        // Insert the data into the database
        DB::table('ref_meeting_log')->insert($data);
        // Optional: Return a success response
        return response()->json(['message' => 'Meeting data inserted successfully']);
    }
    public function meetingallviews()
    {
        $doctordetails = DB::table('ref_meeting_log')->orderBy('id', 'desc')->get();
        return response()->json($doctordetails);
    }
    // meeting date fitters
    public function meetingdatefitter(Request $request)
    {
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        $data = meetingdetails::whereBetween('ref_meeting_log.created_at', [$startdates, $enddates])
            ->join('branches', 'ref_meeting_log.city', '=', 'branches.id')
            ->join('zones', 'branches.zone_id', '=', 'zones.id')
            ->select('branches.Branch_name', 'zones.zone_name', 'ref_meeting_log.*')
            ->get();
        return response()->json($data);
    }
    public function meetingmorefitter(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $moredatefittervale = $request->input('moredatefittervale');
        $dates = explode(' - ', $moredatefittervale);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = meetingdetails::query()
            ->join('branches', 'ref_meeting_log.city', '=', 'branches.id')
            ->join('zones', 'branches.zone_id', '=', 'zones.id')
            ->select('branches.Branch_name', 'zones.zone_name', 'ref_meeting_log.*')
            ->whereBetween('ref_meeting_log.created_at', [$startdates, $enddates]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function meetingremovefitter(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = meetingdetails::query()
            ->join('branches', 'ref_meeting_log.city', '=', 'branches.id')
            ->join('zones', 'branches.zone_id', '=', 'zones.id')
            ->select('branches.Branch_name', 'zones.zone_name', 'ref_meeting_log.*')
            ->whereBetween('ref_meeting_log.created_at', [$startdates, $enddates]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function meetingclrfitter(Request $request)
    {
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        // Start the query
        $query = meetingdetails::query()
            ->join('branches', 'ref_meeting_log.city', '=', 'branches.id')
            ->join('zones', 'branches.zone_id', '=', 'zones.id')
            ->select('branches.Branch_name', 'zones.zone_name', 'ref_meeting_log.*')
            ->whereBetween('ref_meeting_log.created_at', [$startdates, $enddates]);
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function meetingdateandfitter(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = meetingdetails::query()
            ->join('branches', 'ref_meeting_log.city', '=', 'branches.id')
            ->join('zones', 'branches.zone_id', '=', 'zones.id')
            ->select('branches.Branch_name', 'zones.zone_name', 'ref_meeting_log.*')
            ->whereBetween('ref_meeting_log.created_at', [$startdates, $enddates]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function patientid(Request $request)
    {
        $meetingvalue = $request->input('meetingvalue');
        $query = doctordetails::query()
            ->join('branches', 'ref_meeting_log.city', '=', 'branches.id')
            ->join('zones', 'branches.zone_id', '=', 'zones.id')
            ->select('branches.Branch_name', 'zones.zone_name', 'ref_meeting_log.*')
            ->where('id', [$meetingvalue]);
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function patientinsert(Request $request)
    {
        // Get the input array
        $patientinsertvalue = $request->input('patientinsertvalue');
        // Convert the array values to an associative array
        $data = [];
        foreach ($patientinsertvalue as $item) {
            // Split the string into key-value pairs
            [$key, $value] = explode('=', $item);
            $data[$key] = trim($value, "'");
        }
        //dd($data);
        // Insert the data into the database
        DB::table('ref_patient_details')->insert($data);
        // Optional: Return a success response
        return response()->json(['message' => 'Meeting data inserted successfully']);
    }
    public function patientallviews(Request $request)
    {
        $doctordetails = DB::table('ref_patient_details')->orderBy('id', 'desc')->get();
        return response()->json($doctordetails);
    }

    public function branchselectvalue(Request $request)
    {
        dd($request);
    }
    public function patientdatefitter(Request $request)
    {
        $datefilltervalue = $request->input('datefilltervalue');
        //dd($datefilltervalue);
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        $data = patientdetails::whereBetween('created_at', [$startdates, $enddates])->get();
        return response()->json($data);
    }
    public function patientmorefitter(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $moredatefittervale = $request->input('moredatefittervale');
        $dates = explode(' - ', $moredatefittervale);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = patientdetails::query();
        // Add whereBetween for created_at
        $query->whereBetween('created_at', [$startdates, $enddates]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function patientremovefitter(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = patientdetails::query();
        // Add whereBetween for created_at
        $query->whereBetween('created_at', [$startdates, $enddates]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function patientclrfitter(Request $request)
    {
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        // Start the query
        $query = patientdetails::query();
        // Add whereBetween for created_at
        $query->whereBetween('created_at', [$startdates, $enddates]);
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function patientdateandfitter(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = patientdetails::query();
        // Add whereBetween for created_at
        $query->whereBetween('created_at', [$startdates, $enddates]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function patientpop(Request $request)
    {
        $referral_id = $request->input('referral_id');
        $query = patientdetails::query();
        // Add whereBetween for created_at
        $query->where('ref_doctor_id', [$referral_id]);
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
    public function meetingpop(Request $request)
    {
        $referral_id = $request->input('referral_id');
        $query = meetingdetails::query();
        // Add whereBetween for created_at
        $query->where('ref_doctor_id', [$referral_id]);
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }
   public function documentadded(Request $request)
{
    $username = auth()->user()->user_fullname;
    $validatedData = $request->validate([
        'zone_id' => 'required|string|max:255',
                'document_type_name' => 'required|string|max:255',
                'document_type_id' => 'required|string|max:255',
                'expire_date' => 'nullable|string|max:255',
                'images.*' => 'required|file|mimes:pdf|max:1048576', // Validate images
        ]);
    if ($request->hasFile('images')) {
    foreach ($request->file('images') as $image) {
        $originalName = $image->getClientOriginalName();
        $sanitized = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);
        $sanitized = preg_replace('/_+/', ' ', $sanitized);
        $filename = time() . '_' . $sanitized;
        $destinationPath = public_path('document_data');
        $image->move($destinationPath, $filename);
        $imagePaths[] = 'document_data/' . $filename;
    }
}

        $document = documentdetails::create(array_merge($validatedData, [
            'zone_id' => $request->zone_id,
            'document_type_id' => $request->document_type_id,
            'document_type' => json_encode($imagePaths),
            'created_by' => $username,
            'document_id' => 1
        ]));
        return response()->json(['success' => true, 'message' => 'Document saved successfully!']);
}
    public function fetchdocument()
    {
        $documents = DB::table('hms_document_manage')
            ->join('tbl_locations', 'hms_document_manage.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_document_manage.*')
            ->where('hms_document_manage.document_id', '=', 1)
            ->get();
        return response()->json($documents);
    }
    public function documentupdated(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'expire_date' => 'required|date',
            'document_type' => 'required|string',
            'images.*' => 'required|file|mimes:pdf|max:12048',
        ]);

        $documentId = $request->input('id');
        $expireDate = $request->input('expire_date');
        $documentType = $request->input('document_type');
        // $expire_dates = $request->input('expire_dates');
        $documentType_view[] = $documentType;

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('document_data'); // Path to public/uploads/doctor_images
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $imagePaths[] = 'document_data/' . $filename; // Save relative path
            }
        }
        $imagePaths = array_map(function ($path) {
            return str_replace(['\"', '\\/', '\\'], ['', '/', '/'], $path);
        }, $imagePaths);
        $documentType_view[] = implode(',', $imagePaths);
        // $output = $expire_dates.",".$expireDate;
        $cleanedArray = array_map('stripslashes', $documentType_view);
        $cleanedArray[0] = trim($cleanedArray[0], '[]\"');
        $documentTypeJson = json_encode($cleanedArray);
        $documents = DocumentDetails::where('id', $documentId)
            ->update([
                'document_type' => $documentTypeJson,
                'expire_date' => $expireDate
            ]);
        return response()->json(['success' => true, 'message' => 'Document Updated successfully!']);
    }

    public function branchurls()
    {
        $documents = DB::table('zones')->get();
        return response()->json($documents);
    }
    public function usermanagentadded(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'user_fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'category' => 'required|string|max:255',

        ]);
        $validatedData['password'] = Hash::make($validatedData['password']);
        // dd($validatedData);
        $doctor = usermanagementdetails::create(array_merge($validatedData));
        // dd($doctor);
        return response()->json(['success' => true, 'message' => 'Doctor saved successfully!']);
    }
    public function userdetails()
    {
        $usersdetails = usermanagementdetails::orderBy('created_at', 'desc')->get();
        return response()->json($usersdetails);
    }

    public function campdetailsadded(Request $request)
{
    $validatedData = $request->validate([
        'Branch' => 'required|string|max:255',
        'Camp_Date' => 'required|string|max:255',
        'Camp_enddate' => 'required|string|max:255',
        'Camp_Centre_Name' => 'required|string|max:255',
        'Camp_Location' => 'required|string|max:255',
        'Camp_Executives' => 'required|string|max:255',
        'Digital_Marketing_coordinator' => 'required|string|max:255',
        'Digital_Marketing_Cost' => 'required|numeric',
        'Digi_Days' => 'required|numeric',
        'Total_Cost' => 'required|numeric',
        'Budget_For_Auto' => 'required|numeric',
        'Auto_Cost' => 'required|numeric',
        'Auto_Days' => 'required|numeric',
        'Auto_Total_Cost' => 'required|numeric', // fixed name (no space)
        'Budget_For_Snacks' => 'required|numeric',
        'Snacks_Cost' => 'required|numeric',
        'Notices_img.*' => 'required|file|mimes:jpeg,jpg,png|max:12048', // Validate images
        'Banner_img.*' => 'required|file|mimes:jpeg,jpg,png|max:12048', // Validate images
        'Notices_Cost' => 'required|numeric',
        'Notices_Count' => 'required|numeric',
        'Banner_Cost' => 'required|numeric',
        'Banner_Count' => 'required|numeric',
        'Dr_attended' => 'required|string|max:255',
    ]);

      $imagePaths = [];
        $camp_notices = [];

        // Handle image uploads
        if ($request->hasFile('Notices_img')) {
            foreach ($request->file('Notices_img') as $image) {
                $filename = uniqid() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('camp_expenses'); // Path to public/camp_expenses
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $imagePaths[] = 'camp_expenses/' . $filename; // Save relative path
            }
        }

        if ($request->hasFile('Banner_img')) {
            foreach ($request->file('Banner_img') as $image) {
                $filename = uniqid() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('camp_expenses'); // Path to public/camp_expenses
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $camp_notices[] = 'camp_expenses/' . $filename; // Save relative path
            }
        }

    // Create camp entry
    $camp = Campmanagement::create(array_merge(
        $validatedData,
        ['Auto_Total_Cost' => $request->input('Auto_Total_Cost')], // explicitly added if renamed
        ['Notices_img' => json_encode($imagePaths)],
        ['Banner_img' => json_encode($imagePaths)]
    ));

    return response()->json([
        'success' => true,
        'message' => 'Camp details saved successfully!'
    ]);
}


    public function campdateanddataftters(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $moredatefittervale = $request->input('moredatefittervale');
        $dates = explode(' - ', $moredatefittervale);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = Campmanagement::query()
         ->join('branches', 'Camp_management_system.Branch', '=', 'branches.branch_name')
        ->join('zones', 'branches.zone_id', '=', 'zones.id')
        ->select(
            'Camp_management_system.*',
            'branches.branch_name as branchnames',
            'zones.zone_name'
        );
        $query->whereBetween('Camp_management_system.Camp_Date', [$startDateFormatted, $endDateFormatted]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
       //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }

    public function campalldetails()
    {
        $campdetails = Campmanagement::orderBy('Camp_Date', 'desc')->get();
        return response()->json($campdetails);
    }

    public function campdatefitters(Request $request)
    {
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        $data = Campmanagement::whereBetween('Camp_Date', [$startDateFormatted, $endDateFormatted])->get();
        return response()->json($data);
    }

    public function campactivitespopuop(Request $request)
    {
        $useridss = $request->input('useridss');
        $data = Campmanagement::where('id', $useridss)->get();
        return response()->json($data);
    }

    public function campexpensivepopuop(Request $request)
    {
        $useridss = $request->input('useridss');
        $data = Campmanagement::where('id', $useridss)->get();
        return response()->json($data);
    }

    public function activitedatasave(Request $request){
     $username = auth()->user()->user_fullname;
      $validatedData = $request->validate([
                'campa_days' => 'required|string|max:255',
                'campa_name' => 'required|string|max:255',
                'campa_budget' => 'required|string|max:255',
                'campa_login_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
                'campa_logout_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
                'campa_loc_track' => 'nullable|string|max:255',
                'campa_description' => 'required|string|max:255',
                'campa_zone_id' => 'required|string|max:255',
                'imagesnotes.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:12048', // Validate images
                'imagesbanner.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:12048',
        ]);
        $sec_phone = $request->input('sec_phone');
        $imagePaths = [];
         $imagePathsbanner = [];
        if ($request->hasFile('imagesnotes')) {
            foreach ($request->file('imagesnotes') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('camp_activites'); // Path to public/uploads/doctor_images
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $imagePaths[] = 'camp_activites/' . $filename; // Save relative path
            }
        }
         if ($request->hasFile('imagesbanner')) {
            foreach ($request->file('imagesbanner') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('camp_activites'); // Path to public/uploads/doctor_images
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $imagePathsbanner[] = 'camp_activites/' . $filename; // Save relative path
            }
        }
        $loginTime = $request->input('campa_login_time');
        $logoutTime = $request->input('campa_logout_time');
        CampActivityModel::create(array_merge($validatedData, [
            'campa_login_time' => $loginTime,
            'campa_logout_time' => $logoutTime,
            'campa_zone_id' => $request->campa_zone_id,
            'campa_notes_img' => json_encode($imagePaths),
            'campa_banner_img' => json_encode($imagePathsbanner),
            'created_by' => $username,
            'status' => 1
        ]));
        return response()->json(['success' => true, 'message' => 'Security saved successfully!']);
}

public function activitydatafilter(Request $request){
   $fitterremovedataall = $request->input('morefilltersallact');
    $datefiltervalue = $request->input('moredatefittervalact');
    $dates = explode(' - ', $datefiltervalue);
    $startDate = $dates[0];  // "29/12/2024"
    $endDate = $dates[1];    // "04/01/2025"

    $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
    $data = CampActivityModel::select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_camp_activites.*')
    ->join('tbl_locations', 'hms_camp_activites.campa_zone_id', '=', 'tbl_locations.id')
    ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
    ->whereDate('hms_camp_activites.created_at', '>=', $startdates)->where('hms_camp_activites.created_at', '<=', $enddates);
        if($fitterremovedataall){
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                    $value = trim($value, "'");
                    $data->whereIn(trim($column), explode(',', $value));
            }
        }
        $data = $data->orderBy('hms_camp_activites.created_at', 'desc')->get();
    return response()->json($data);
}

public function leadsadddata(Request $request)
{
    $username = auth()->user()->user_fullname;
    $validatedData = $request->validate([
    'camp_zone_id' => 'required|string|max:255',
    'camp_name' => 'required|string|max:255',
    'camp_wife_name' => 'nullable|string|max:255',
    'camp_husband_name' => 'nullable|string|max:255',
    'camp_wife_age' => 'nullable|string|max:255',
    'camp_husband_age' => 'nullable|string|max:255',
    'camp_wife_mobile' => 'nullable|string|max:255',
    'camp_husband_mobile' => 'nullable|string|max:255',
    'camp_marriage_at' => 'nullable|string|max:255',
    'camp_married_years' => 'nullable|string|max:255',
    'camp_city' => 'nullable|string|max:255',
    'camp_address' => 'nullable|string|max:255',
    'camp_state' => 'nullable|string|max:255',
    'camp_email' => 'nullable|string|max:255',
    'camp_country' => 'nullable|string|max:255',
    'camp_wife_mrdno' => 'nullable|string|max:255',
    'camp_hus_mrdno' => 'nullable|string|max:255',
     'capm_walkindate' => 'nullable|string|max:255',
     'camp_zipcode' => 'nullable|string|max:255',
     'camp_profile_group' => 'nullable|string|max:255',
     'camp_for_fertility' => 'nullable|string|max:255',
     'camp_prefered_call' => 'nullable|string|max:255',
     'camp_prefered_language' => 'nullable|string|max:255',
     'camp_description' => 'nullable|string|max:255',
        ]);

    CampLeadsModel::create(array_merge($validatedData, [
            'camp_zone_id' => $request->camp_zone_id,
            'created_by' => $username,
            'status' => 1
        ]));
        return response()->json(['success' => true, 'message' => 'Camp Lead saved successfully!']);
}
public function leadsfilter(Request $request){
    $fitterremovedataall = $request->input('morefilltersall');
    $datefiltervalue = $request->input('moredatefittervale');
    $dates = explode(' - ', $datefiltervalue);
    $startDate = $dates[0];  // "29/12/2024"
    $endDate = $dates[1];    // "04/01/2025"

    $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
    $data = CampLeadsModel::select('tbl_locations.name as location_name', 'tblzones.name as zone_name', 'camp_management_system.Camp_Centre_Name','hms_camp_leads.*')
    ->join('tbl_locations', 'hms_camp_leads.camp_zone_id', '=', 'tbl_locations.id')
    ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
    ->join('camp_management_system', 'tbl_locations.name', '=','camp_management_system.Branch')
    ->whereDate('hms_camp_leads.created_at', '>=', $startdates)->where('hms_camp_leads.created_at', '<=', $enddates);
        if($fitterremovedataall){
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                    $value = trim($value, "'");
                    $data->whereIn(trim($column), explode(',', $value));
            }
        }
        $data = $data->orderBy('hms_camp_leads.created_at', 'desc')->get();
    return response()->json($data);
}



    public function campdateandsearchfitters(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = Campmanagement::query()
        ->join('branches', 'Camp_management_system.Branch', '=', 'branches.branch_name')
        ->join('zones', 'branches.zone_id', '=', 'zones.id')
        ->select(
            'Camp_management_system.*',
            'branches.branch_name as branchnames',
            'zones.zone_name'
        );
        // Add whereBetween for created_at
        $query->whereBetween('Camp_management_system.Camp_Date', [$startDateFormatted, $endDateFormatted]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }

   public function campfetchurlfitters(Request $request)
{
    $fitterremovedata = $request->input('fitterremovedata');  // example: zone_name=AP,Vellore
    $datefilltervalue = $request->input('moredatefittervale'); // "15/04/2025 - 14/05/2025"

    // Convert date range to Y-m-d
    [$startDate, $endDateRaw] = explode(' - ', $datefilltervalue);
    $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', trim($startDate))->format('Y-m-d');
    $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', trim($endDateRaw))->format('Y-m-d');

    $startDateTime = $startDateFormatted . ' 00:00:00';
    $endDateTime = $endDateFormatted . ' 23:59:59';

    // Start the query with proper JOINs
    $query = Campmanagement::query()
        ->join('branches', 'Camp_management_system.Branch', '=', 'branches.branch_name')
         ->join('zones', 'branches.zone_id', '=', 'zones.id')
        ->select(
            'Camp_management_system.*',
            'branches.branch_name as branchnames',
            'zones.zone_name'
        )
        ->whereBetween('Camp_management_system.Camp_Date', [$startDateFormatted, $endDateFormatted]);

    // Map input keys to actual DB columns
    $columnMap = [
        'zone_name' => 'zones.zone_name',
        'branches' => 'branches.name',
    ];

    // Parse filters
    if (is_array($fitterremovedata)) {
        $fitterremovedata = implode(' AND ', $fitterremovedata);
    } elseif (!is_string($fitterremovedata)) {
        return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
    }

    foreach (explode(' AND ', $fitterremovedata) as $condition) {
        if (strpos($condition, '=') === false) continue;

        [$column, $value] = explode('=', $condition, 2);
        $column = trim($column);
        $value = trim($value, "' ");

        // Handle multiple values
        $values = explode(',', $value);
        $dbColumn = $columnMap[$column] ?? $column;  // Use mapped column name if exists

        $query->where(function ($q) use ($dbColumn, $values) {
            foreach ($values as $val) {
                $q->orWhere($dbColumn, trim($val));
            }
        });
    }

    // 🔍 See final SQL query with bindings filled
    $sql = $query->toSql();
    $bindings = $query->getBindings();

    $finalSql = $sql;
    foreach ($bindings as $binding) {
        $escaped = is_numeric($binding) ? $binding : "'" . addslashes($binding) . "'";
        $finalSql = preg_replace('/\?/', $escaped, $finalSql, 1);
    }

    //dd($finalSql);  // View full SQL

    // If you want actual data instead:
    // $results = $query->get();
    // return response()->json($results);
}




    public function activitesadddata(Request $request)
    {
        $validatedData = $request->validate([
            'camp_id' => 'required|string|max:255',
            'date_activites' => 'required|string|max:255',
            'activites' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'area_covered' => 'required|string|max:255',
            'images.*' => 'required|file|mimes:jpeg,jpg,png|max:12048', // Validate images
        ]);
        $imagePaths = [];
        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('camp_activites'); // Path to public/uploads/doctor_images
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $imagePaths[] = 'camp_activites/' . $filename; // Save relative path
            }
        }
        $doctor = Activitiesmanage::create(array_merge($validatedData, [
            'images' => json_encode($imagePaths),
        ]));
        return response()->json(['success' => true, 'message' => 'Doctor saved successfully!']);
    }

    public function activitesalldetails()
    {
        $documents = DB::table('hms_camp_activites')
            ->join('hms_camp_management', 'hms_camp_activites.camp_id', '=', 'hms_camp_management.id')
            ->select('hms_camp_management.camp_type', 'hms_camp_management.Branch', 'hms_camp_activites.*')
            ->get();
        return response()->json($documents);
    }

    public function activitesdatefitters(Request $request)
    {
        $datefiltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefiltervalue);

        // Parse start and end dates using Carbon
        $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
        $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();

        $data = Activitiesmanage::whereBetween('hms_camp_activites.created_at', [$startDate, $endDate])
            ->join('hms_camp_management', 'hms_camp_activites.camp_id', '=', 'hms_camp_management.id')
            ->select('hms_camp_management.camp_type', 'hms_camp_management.Branch', 'hms_camp_activites.*')
            ->get();
        return response()->json($data);
    }

   public function storeInline(Request $request)
{
    $requestData = $request->all();

    $request->validate([
        'camp_id' => 'required|integer',
        'expenses' => 'required|array|min:1',
        'expenses.*.day' => 'required|string',
        'expenses.*.expense' => 'required|string',
        'expenses.*.remarks' => 'nullable|string',
        'expenses.*.doctor' => 'nullable|string',
        'expenses.*.branch' => 'nullable|string',
        'expenses.*.centre' => 'nullable|string',
    ]);

    $insertData = [];

    foreach ($request->expenses as $row) {
        $insertData[] = [
            'day' => $row['day'],
            'expense' => $row['expense'],
            'remarks' => $row['remarks'],
            'doctor' => $row['doctor'],
            'branch' => $row['branch'],
            'centre' => $row['centre'],
            'camp_id' => $request->camp_id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    Expensemanagement::insert($insertData);

    return response()->json(['success' => true, 'message' => 'Expenses saved successfully']);
}


    public function activitesdatanaddatefitters(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $moredatefittervale = $request->input('moredatefittervale');
        $dates = explode(' - ', $moredatefittervale);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = Activitiesmanage::query();
        // Add whereBetween for created_at
        $query->whereBetween('hms_camp_activites.created_at', [$startdates, $enddates]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query
            ->join('hms_camp_management', 'hms_camp_activites.camp_id', '=', 'hms_camp_management.id')
            ->select('hms_camp_management.camp_type', 'hms_camp_management.Branch', 'hms_camp_activites.*')
            ->get();
        return response()->json($doctorDetails);
    }

    public function activitesdateandfittertexts(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        // Start the query
        $query = Activitiesmanage::query();
        // Add whereBetween for created_at
        $query->whereBetween('hms_camp_activites.created_at', [$startdates, $enddates]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column, $values) {
                foreach ($values as $val) {
                    $q->orWhere($column, $val);
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query
            ->join('hms_camp_management', 'hms_camp_activites.camp_id', '=', 'hms_camp_management.id')
            ->select('hms_camp_management.camp_type', 'hms_camp_management.Branch', 'hms_camp_activites.*')
            ->get();
        return response()->json($doctorDetails);
    }

    public function expensesadddata(Request $request)
    {

        $validatedData = $request->validate([
            'camp_id' => 'required|string|max:255',
            'activites' => 'required|string|max:255',
            'cost' => 'required|string|max:255',
            'document_purchase_order.*' => 'required|file|mimes:pdf|max:12048', // Validate images
            'creatives.*' => 'required|file|mimes:pdf|max:12048', // Validate images

        ]);
        $imagePaths = [];
        $creativesviews = [];

        // Handle image uploads
        if ($request->hasFile('document_purchase_order')) {
            foreach ($request->file('document_purchase_order') as $image) {
                $filename = uniqid() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('camp_expenses'); // Path to public/camp_expenses
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $imagePaths[] = 'camp_expenses/' . $filename; // Save relative path
            }
        }

        if ($request->hasFile('creatives')) {
            foreach ($request->file('creatives') as $image) {
                $filename = uniqid() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('camp_expenses'); // Path to public/camp_expenses
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $creativesviews[] = 'camp_expenses/' . $filename; // Save relative path
            }
        }

        $doctor = Expensemanagement::create(array_merge($validatedData, [
            'document_purchase_order' => json_encode($imagePaths),
            'creatives' => json_encode($creativesviews),
        ]));
        return response()->json(['success' => true, 'message' => 'Doctor saved successfully!']);
    }

    public function expensesalldetails()
{
    $campdetails = DB::table('Camp_management_system')
        ->leftJoin('hms_camp_expenses', 'Camp_management_system.id', '=', 'hms_camp_expenses.camp_id') // Assuming 'id' is PK in camp table
        ->select('Camp_management_system.*','Camp_management_system.id as camids', 'hms_camp_expenses.*')
        ->orderBy('Camp_management_system.created_at', 'desc')
        ->get();

    return response()->json($campdetails);
}

    public function branchfetchviews()
    {
        $documents = DB::table('branches')
            ->orderBy('branch_name', 'ASC')
            ->get();
        return response()->json($documents);
    }

    public function zonefetchviews()
    {
        $documents = DB::table('zones')->get();
        return response()->json($documents);
    }

    public function marketernamesurls()
    {
        $documents = DB::table('users')
            ->where('role_id', 2) // Filter users with the role 'marketer'
            ->orderBy('user_fullname', 'ASC')
            ->select('user_fullname', 'role_id', 'username')
            ->get();
        return response()->json($documents);
    }
    public function menuaccessurl()
    {
        $userId = auth()->user()->id;
    
        $accessibleMenuIds = DB::table('user_menus')
            ->where('user_id', $userId)
            ->where('status','1')
            ->pluck('menu_id')
            ->toArray();
    
        // Main Menus
        $mainMenus = DB::table('menus')
            ->where('main_menu', 1)
            ->whereIn('id', $accessibleMenuIds)
            ->get();
        // dd($mainMenus);
        $mainMenus = $mainMenus->map(function ($menu) use ($accessibleMenuIds) {
    
            // Level 2
            $children = DB::table('menus')
                ->where('sub_menus', $menu->id)
                ->whereIn('id', $accessibleMenuIds)
                ->get();
    
            $children = $children->map(function ($child) use ($accessibleMenuIds) {
    
                // Level 3
                $subChildren = DB::table('menus')
                    ->where('sub_menus', $child->id)
                    ->whereIn('id', $accessibleMenuIds)
                    ->get();
    
                $child->children = $subChildren;
                return $child;
            });
    
            $menu->children = $children;
            return $menu;
        });
    
        return response()->json($mainMenus);
    }
    // public function menuaccessurl()
    // {
    //     $userId = auth()->user()->id;
    
    //     // Step 1: Get all menu IDs the user has access to
    //     $accessibleMenuIds = DB::table('user_menus')
    //         ->where('user_id', $userId)
    //         ->where('status','1')
    //         ->pluck('menu_id');
    
    //     // Step 2: Get only top-level (main_menu = 1) menus the user has access to
    //     $userMenus = DB::table('menus')
    //         ->whereIn('id', $accessibleMenuIds)
    //         ->where('main_menu', 1)
    //         ->select(
    //             'id',
    //             'menu_name',
    //             'route',
    //             'icon',
    //             'sub_menus',
    //             'dropdown',
    //             'main_menu',
    //             'active_ids'
    //         )
    //         ->get();
    
    //     // Step 3: For each top-level menu, get its children (that are ALSO in user_menus)
    //     $menusWithChildren = $userMenus->map(function ($menu) use ($accessibleMenuIds) {
    //         $children = DB::table('menus')
    //             ->where('sub_menus', $menu->id)
    //             ->whereIn('id', $accessibleMenuIds) // Only include accessible submenus
    //             ->select('id', 'menu_name', 'route', 'icon', 'sub_menus', 'main_menu', 'dropdown', 'active_ids')
    //             ->get();
    
    //         $menu->children = $children;
    //         return $menu;
    //     });
    
    //     return response()->json($menusWithChildren);
    // }
    

    public function incomefetchdetails()
    {
        $incomedetails = incomedetails::orderBy('created_at', 'desc')->get();
        return response()->json($incomedetails);
    }


    public function incomeupdatedetails(Request $request)
    {

        $branch_id = auth()->user()->branch_id;
        //dd($branch_id);
        // Collect input data
        $dateviewsall = $request->input('dateviewsall');
        $moc_dc_all = $request->input('moc_dc_all');
        $radiants_all = $request->input('radiants_all');
        $bank_st_all = $request->input('bank_st_all');
        $moc_card_all = $request->input('moc_card_all');
        $orange_card_all = $request->input('orange_card_all');
        $bank_st_card_all = $request->input('bank_st_card_all');
        $moc_upi_all = $request->input('moc_upi_all');
        $ornage_upi_all = $request->input('ornage_upi_all');
        $bank_st_upi = $request->input('bank_st_upi');
        $moc_doc_neft_all = $request->input('moc_doc_neft_all');
        $bank_st_neft_all = $request->input('bank_st_neft_all');

        // Perform the update query
        $affectedRows = DB::table('Income_reconciliation')
            ->where('income_date', $dateviewsall)
            ->update([
                'moc_doc_cash' => $moc_dc_all,
                'radiant_cash' => $radiants_all,
                'bank_st_cash' => $bank_st_all,
                'moc_doc_card' => $moc_card_all,
                'orange_card' => $orange_card_all,
                'bank_st_card' => $bank_st_card_all,
                'moc_doc_upi' => $moc_upi_all,
                'orange_upi' => $ornage_upi_all,
                'bank_st_upi' => $bank_st_upi,
                'moc_doc_neft' => $moc_doc_neft_all,
                'bank_st_neft' => $bank_st_neft_all,
            ]);

        // Check if the update was successful
        if ($affectedRows > 0) {
            return response()->json(['success' => true, 'message' => 'Income details updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'No records updated']);
        }
    }

    public function timeLine()
    {
        return view('superadmin.timeline');
    }

    public function billalldetails()
    {

        $billingList = billinglistdetails::select(
            'id',
            'type',
            'paymenttype',
            DB::raw("SUM(CASE WHEN paymenttype = 'Card' THEN amt ELSE 0 END) AS Card"),
            DB::raw("SUM(CASE WHEN paymenttype = 'Cash' THEN amt ELSE 0 END) AS Cash"),
            DB::raw("SUM(CASE WHEN paymenttype = 'Cheque' THEN amt ELSE 0 END) AS Cheque"),
            DB::raw("SUM(CASE WHEN paymenttype = 'DD' THEN amt ELSE 0 END) AS DD"),
            DB::raw("SUM(CASE WHEN paymenttype = 'Neft' THEN amt ELSE 0 END) AS Neft"),
            DB::raw("SUM(CASE WHEN paymenttype = 'Credit' THEN amt ELSE 0 END) AS Credit"),
            DB::raw("SUM(CASE WHEN paymenttype = 'UPI' THEN amt ELSE 0 END) AS UPI")
        )
            ->where('Location', 'location43')
            ->groupBy('type')
            ->get();

        return response()->json($billingList);
    }

    public function bill_overall_list_get(Request $request)
    {
        $clicked_data = $request->input('clicked_data');
        $cashtype = $request->input('cashtype');

        $billingListall = billinglistdetails::where('type', $cashtype)
            ->where('paymenttype', $clicked_data)
            ->where('Location', 'location43')
            ->get();

        return response()->json($billingListall);
    }

    public function getVehicle()
    {
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        $locations = TblLocationModel::orderBy('name', 'asc')->get();
        $type = VehicleType::where('status', 0)->orderBy('id', 'asc')->get();
        // dd($type);
        $vehicle_no = VehicleDetails::select('id', 'vehicle_no', 'make')->where('vehicle_no', '!=', '')->orderBy('id', 'asc')->get();
        // dd($vehicle_no);
        return view('superadmin.vehicle_details', compact('admin', 'locations', 'type', 'vehicle_no'));
    }

    // public function vehicleDetails(){
    //     $vehicleDetails=VehicleDetails::select('vehicle_details.*', 'vehicle_details.created_at', 'tbl_locations.name', 'vehicle_type.type')->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
    //             ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')->orderBy('vehicle_details.created_at', 'desc')->get();
    //     return response()->json($vehicleDetails);
    // }

    public function vehicleDetails(){
        // $vehicleDetails = VehicleDetails::select(
        //     'vehicle_details.*',
        //     'vehicle_details.id as vehicle_id',
        //     'vehicle_details.created_at',
        //     'tbl_locations.id as location_id',
        //     'tbl_locations.name as branch_name',
        //     'vehicle_type.id as vehicle_type_id',
        //     'vehicle_type.type as vehicle_type',
        //     'vehicle_insurance_details.id as insurance_id',
        //     'vehicle_insurance_details.*',
        //     'vehicle_activities.id as activity_id',
        //     'vehicle_activities.*',
        //     'vehicle_service_details.id as service_id',
        //     'vehicle_service_details.*',
        //     'vehicle_document_details.id as document_id',
        //     'vehicle_document_details.document_name',
        //     'tblzones.id as zoneid',
        //     'tblzones.*'
        // )
        //     ->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
        //     ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')
        //     ->join('tblzones', 'vehicle_details.zone_id', '=', 'tblzones.id')
        //     ->leftJoin('vehicle_insurance_details', 'vehicle_details.id', '=', 'vehicle_insurance_details.vehicle_id')
        //     ->leftJoin('vehicle_activities', 'vehicle_details.id', '=', 'vehicle_activities.vehicle_id')
        //     ->leftJoin('vehicle_service_details', 'vehicle_details.id', '=', 'vehicle_service_details.vehicle_id')
        //     ->leftJoin('vehicle_document_details', 'vehicle_details.id', '=', 'vehicle_document_details.vehicle_id')
        //     ->orderBy('vehicle_details.created_at', 'desc')
        //     ->get();
        $vehicleDetails = VehicleDetails::select(
            'vehicle_details.*',
            'vehicle_details.id as vehicle_id',
            'vehicle_details.created_at',
            'tbl_locations.id as location_id',
            'tbl_locations.name as branch_name',
            'vehicle_type.id as vehicle_type_id',
            'vehicle_type.type as vehicle_type',
            'vehicle_insurance_details.id as insurance_id',
            'vehicle_insurance_details.*',
            'vehicle_activities.id as activity_id',
            'vehicle_activities.*',
            'vehicle_service_details.id as service_id',
            'vehicle_service_details.*',
            // 'vehicle_document_details.id as document_id',
            // 'vehicle_document_details.document_name',
            'tblzones.id as zoneid',
            'tblzones.*'
        )
            ->selectRaw("(select count(*) from vehicle_document_details where vehicle_document_details.vehicle_id = vehicle_details.id) as document_count")
            ->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
            ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')
            ->join('tblzones', 'vehicle_details.zone_id', '=', 'tblzones.id')
            ->leftJoin('vehicle_activities', 'vehicle_details.id', '=', 'vehicle_activities.vehicle_id')
            // ->leftJoin('vehicle_document_details', 'vehicle_details.id', '=', 'vehicle_document_details.vehicle_id')
            ->leftJoin('vehicle_insurance_details', function ($join) {
                $join->on('vehicle_details.id', '=', 'vehicle_insurance_details.vehicle_id')
                    ->where('vehicle_insurance_details.updated_at', '=', function ($query) {
                        $query->selectRaw('MAX(updated_at)')
                            ->from('vehicle_insurance_details')
                            ->whereColumn('vehicle_insurance_details.vehicle_id', 'vehicle_details.id');
                    });
            })
            ->leftJoin('vehicle_service_details', function ($join) {
                $join->on('vehicle_details.id', '=', 'vehicle_service_details.vehicle_id')
                    ->where('vehicle_service_details.updated_at', '=', function ($query) {
                        $query->selectRaw('MAX(updated_at)')
                            ->from('vehicle_service_details')
                            ->whereColumn('vehicle_service_details.vehicle_id', 'vehicle_details.id');
                    });
            })
            ->orderBy('vehicle_details.created_at', 'desc')
            ->get();
        // dd($vehicleDetails);
        // echo "<pre>";
        // print_r($vehicleDetails);
        // exit;
        return response()->json($vehicleDetails);
    }

    public function vehicleDocumentEdit(Request $request){



        $id = $request->id;
        $insurance_id = $request->insurance_id;



        $vehicleDetails = VehicleDetails::where('id', $id)
            ->with('vehicleType', 'location', 'serviceDetails') // exclude insuranceDetails here
            ->first();

         // Only fetch and attach insuranceDetails if insurance_id is not null
        if (!is_null($insurance_id)) {
            $insuranceDetails = VehicleInsurance::where('id', $insurance_id)->first();
            $vehicleDetails->setRelation('insuranceDetails', $insuranceDetails);
        } else {
            // You can also explicitly set it to null if needed
            $vehicleDetails->setRelation('insuranceDetails', null);
        }

        // dd($vehicleDetails);

        return response()->json($vehicleDetails);
    }

    public function vehicleDocumentView(Request $request){
        $vehicle_id = $request->input('vehicle_id');
        $vehicle_document_view = VehicleDocument::where('vehicle_id', $vehicle_id)->orderBy('created_at', 'desc')->get();
        return response()->json($vehicle_document_view);
    }

    public function vehicleInsuranceDocument(Request $request){
        $vehicle_id = $request->input('vehicle_id');
        $vehicle_insurance = VehicleInsurance::where('vehicle_id', $vehicle_id)->orderBy('created_at', 'desc')->get();
        return response()->json($vehicle_insurance);
    }

    public function vehicleDocumentDetails(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale');
        $statusid = $request->input('statusid');
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        $query = VehicleDocument::select('vehicle_document_details.document_type', 'vehicle_document_details.expire_dates', 'vehicle_document_details.expire_date', 'vehicle_document_details.id as did', 'vehicle_document_details.document_name', 'vehicle_details.*', 'vehicle_type.type')
            ->join('vehicle_details', 'vehicle_document_details.vehicle_id', '=', 'vehicle_details.id')
            ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id');
        if ($statusid == 2) {
            $query->whereBetween('vehicle_document_details.created_at', [$startdates, $enddates]);
        }
        $VehicleDocument = $query->orderBy('vehicle_document_details.created_at', 'desc')->get();
        return response()->json($VehicleDocument);
    }


    public function vehicleDocumentUpdate(Request $request){
       // dd($request->all());
       $validatedData = $request->validate([
        'vehicle_id' => 'required|integer',
        'document_type' => 'required|string',
        'expire_date' => 'required|date',
        'image.*' => 'required|file|mimes:pdf|max:12048',
    ]);

    $vehicle_id = $request->input('vehicle_id');
    $documentType = $request->input('document_type');
    $expireDate = $request->input('expire_date');
    $expireDates = $request->input('expire_dates', '');
    $documentId = $request->input('id'); // Optional

    // Combine and sort expire dates
    $dates = array_filter(explode(',', $expireDates . ',' . $expireDate));
    sort($dates);
    $latestDate = end($dates);
    $all_file_paths = [];

    // Initialize $existingDocument to null
    $existingDocument = null;

    if ($request->hasFile('image')) {
        // If a document exists and has file(s), delete old ones
        $existingDocument = VehicleDocument::where('vehicle_id', $vehicle_id)
            ->where('document_type', $documentType)
            ->first();

        if ($existingDocument && $existingDocument->document_name) {
            $oldFiles = json_decode($existingDocument->document_name, true);
            foreach ($oldFiles as $oldFile) {
                $oldFilePath = public_path($oldFile);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); // Delete old file
                }
            }
        }

        // Upload new PDFs
        foreach ($request->file('image') as $img) {
            $filename = time() . '_' . $img->getClientOriginalName();
            $destinationPath = public_path('document_data');
            $img->move($destinationPath, $filename);
            $all_file_paths[] = 'document_data/' . $filename;
        }
    }

    // Use updateOrCreate to either update an existing document or create a new one
    VehicleDocument::Create(
        [
            'vehicle_id' => $vehicle_id,
            'document_name' => json_encode($all_file_paths),
            'expire_date' => $latestDate,
            'expire_dates' => implode(',', $dates),
            'document_type' => $documentType,
        ]
    );

    return response()->json(['success' => true, 'message' => 'Document saved successfully!']);
    }


   public function vehicleAdded(Request $request){
    // dd($request->all());
    // echo "<pre>";print_r($request->all());exit;
    $validatedData = $request->validate([
        'make' => 'required|string|max:255',
        'year_of_manufacture' => 'required|string|max:255',
        'registration_number' => 'required|unique:vehicle_details,registration_number|string|max:255',
        'registration_owner' => 'nullable|string|max:255',
        'rto_location' => 'nullable|string|max:255',
        'engine_number' => 'required|string|max:255',
        'chassis_number' => 'required|string|max:255',
        'fuel_type' => 'required|string|max:255',
        'cluster_name' => 'nullable|string|max:255',
        'vehicle_number' => 'nullable|string|max:255',
        'expiry_date' => 'nullable|string|max:255',
        'company_name' => 'nullable|string|max:255',
        'renewal_date' => 'nullable|string|max:255',
        'policy_details' => 'nullable|string|max:255',
        'thirdparty_company_name' => 'nullable|string|max:255',
        'thirdparty_expiry_date' => 'nullable|string|max:255',
        'thirdparty_renewal_date' => 'nullable|string|max:255',
        'thirdparty_policy_details' => 'nullable|string|max:255',
        'vehicle_incharge' => 'nullable|string|max:255',
        'vehicle_incharge_admin' => 'nullable|string|max:255',
        'payment' => 'nullable|string|max:255',
        'last_service' => 'nullable|string|max:255',
        'last_tyre_change' => 'nullable|string|max:255',
        'files' => 'nullable|array',
        'files.*' => 'mimes:pdf|max:2048',

    ], [
        'registration_number.unique' => 'The registration number has already been taken!',
    ]);
    // dd($validatedData);
    $imagePaths = [];
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            if ($file->isValid()) {
                // $filename = time() . '_' . $file->getClientOriginalName();
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
                $destinationPath = public_path('insurance_image');

                $file->move($destinationPath, $filename); // Move file to destination
                // dd($file);
                $imagePaths[] = 'insurance_image/' . $filename; // Save relative path
            }
        }
    }

    $prefixMap = [
        'Car' => 'VC',
        'Bike' => 'VB',
        'Auto' => 'VA',
        'Electric Car' => 'VCE',
        'Electric Bike' => 'VBE',
        'Electric Auto' => 'VAE',
    ];

    $prefix = $prefixMap[$request->vehicle_type] ?? 'VH'; // fallback if type is not in map

    $lastVehicle = VehicleDetails::where('vehicle_no', 'like', $prefix . '%')->orderByDesc('id')->first();

    if ($lastVehicle && preg_match('/\d+$/', $lastVehicle->vehicle_no, $matches)) {
        $nextNumber = (int) $matches[0] + 1;
    } else {
        $nextNumber = 1;
    }

    $vehicle_no = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    $location_id =  TblLocationModel::select('id', 'zone_id')->where('name', $request->branch)->first();
    $vehicle_type =  VehicleType::select('id')->where('type', $request->vehicle_type)->first();
    $data = VehicleDetails::create($request->only(['fuel_type']));
    VehicleDetails::updateOrCreate(['id'   => $data['id']], array_merge($validatedData, [
        'vehicle_no' => $vehicle_no,
        'vehicle_type' => $vehicle_type->id,
        'branch' => $location_id->id,
        'zone_id' => $location_id->zone_id,
        'insurance_expiry_date' => $request->expiry_date,
    ]));

    VehicleInsurance::Create([
        'vehicle_id' => $data['id'],
        'company_name' => $request->company_name,
        'expiry_date' => $request->expiry_date,
        'renewal_date' => $request->renewal_date,
        'policy_details' => $request->policy_details,
        'payment' => $request->payment,
        'thirdparty_company_name' => $request->thirdparty_company_name,
        'thirdparty_expiry_date' => $request->thirdparty_expiry_date,
        'thirdparty_renewal_date' => $request->thirdparty_renewal_date,
        'thirdparty_policy_details' => $request->thirdparty_policy_details,
        'image_paths' => implode(',', $imagePaths),

    ]);
    VehicleServiceDetails::Create([
        'vehicle_id' => $data['id'],
        'last_service' => $request->last_service,
        'last_tyre_change' => $request->last_tyre_change,
    ]);

    return redirect()->back()
        ->with('success', 'Vehicle added successfully!');
}



    public function vehicleUpdate(Request $request){

        $vehicle = VehicleDetails::findOrFail($request->id);
        // dd($vehicle);
        $validatedData = $request->validate([
            'make' => 'required|string|max:255',
            'year_of_manufacture' => 'required|string|max:255',
            'registration_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vehicle_details')->ignore($vehicle->id),
            ],
            'registration_owner' => 'nullable|string|max:255',
            'rto_location' => 'nullable|string|max:255',
            'engine_number' => 'required|string|max:255',
            'chassis_number' => 'required|string|max:255',
            'fuel_type' => 'required|string|max:255',
            'cluster_name' => 'nullable|string|max:255',
            'vehicle_number' => 'nullable|string|max:255',

            'vehicle_incharge' => 'nullable|string|max:255',
            'vehicle_incharge_admin' => 'nullable|string|max:255',

            // 'files' => 'nullable|array',
            // 'files.*' => 'file|mimes:pdf|max:10240', // max size in kilobytes (e.g., 10MB)

        ], [
            'registration_number.unique' => 'The registration number has already been taken!',
        ]);

        $location_id = TblLocationModel::select('id', 'zone_id')->where('name', $request->branch)->first();
        $vehicle_type = VehicleType::select('id')->where('type', $request->vehicle_type)->first();
        $imagePaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    // $filename = time() . '_' . $file->getClientOriginalName();
                    $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
                    $destinationPath = public_path('insurance_image');

                    $file->move($destinationPath, $filename); // Move file to destination
                    // dd($file);
                    $imagePaths[] = 'insurance_image/' . $filename; // Save relative path
                }
            }
        }

        // Update vehicle details
        VehicleDetails::where('id', $vehicle->id)->update(array_merge($validatedData, [
            'vehicle_type' => $vehicle_type->id ?? null,
            'branch' => $location_id->id ?? null,
            'zone_id' => $location_id->zone_id ?? null,
        ]));


            $updateData = [
                'vehicle_id' => $vehicle->id,
                'company_name' => $request->company_name,
                'expiry_date' => $request->expiry_date,
                'renewal_date' => $request->renewal_date,
                'policy_details' => $request->policy_details,
                'payment' => $request->payment,
                'thirdparty_company_name' => $request->thirdparty_company_name,
                'thirdparty_expiry_date' => $request->thirdparty_expiry_date,
                'thirdparty_renewal_date' => $request->thirdparty_renewal_date,
                'thirdparty_policy_details' => $request->thirdparty_policy_details,
            ];

            // Only include image_paths if new files were uploaded
            if (!empty($imagePaths)) {
                $updateData['image_paths'] = implode(',', $imagePaths);
            }

            // Perform the update
            VehicleInsurance::where('id', $request->insurance_id)->update($updateData);



        VehicleServiceDetails::where('id', $request->service_id)->update([
                'vehicle_id' => $vehicle->id,
                'last_service' => $request->last_service,
                'last_tyre_change' => $request->last_tyre_change,
        ]);


        return redirect()->back()->with('success', 'Vehicle updated successfully!');
    }


    public function addInsuranceExisting(Request $request){
        // dd($request->all());
        $validatedData = $request->validate([
            'expiry_date' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'renewal_date' => 'nullable|string|max:255',
            'policy_details' => 'nullable|string|max:255',
            'thirdparty_company_name' => 'nullable|string|max:255',
            'thirdparty_expiry_date' => 'nullable|string|max:255',
            'thirdparty_renewal_date' => 'nullable|string|max:255',
            'thirdparty_policy_details' => 'nullable|string|max:255',
            'payment' => 'nullable|string|max:255',
            'files' => 'nullable|array',
            'files.*' => 'mimes:pdf|max:2048',

        ]);
        //  dd($validatedData);
            $imagePaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    // $filename = time() . '_' . $file->getClientOriginalName();
                    $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
                    $destinationPath = public_path('insurance_image');

                    $file->move($destinationPath, $filename); // Move file to destination
                    // dd($file);
                    $imagePaths[] = 'insurance_image/' . $filename; // Save relative path
                }
            }
        }


            VehicleInsurance::Create([
                'vehicle_id' => $request->vehicle_id,
                'company_name' => $request->company_name,
                'expiry_date' => $request->expiry_date,
                'renewal_date' => $request->renewal_date,
                'policy_details' => $request->policy_details,
                'payment' => $request->payment,
                'thirdparty_company_name' => $request->thirdparty_company_name,
                'thirdparty_expiry_date' => $request->thirdparty_expiry_date,
                'thirdparty_renewal_date' => $request->thirdparty_renewal_date,
                'thirdparty_policy_details' => $request->thirdparty_policy_details,
                'image_paths' => implode(',', $imagePaths),

            ]);

            return redirect()->back()
            ->with('success', 'New Insurance Details for Existing Vehicle is Added successfully!');
    }


    public function vehicleDocumentFilter(Request $request)
{
    // dd($request->all());
    $filterDataAll = $request->input('morefilltersall');
    $moreDateFilterValue = $request->input('moredatefittervale');
    // dd($filterDataAll);

    $dates = explode(' - ', $moreDateFilterValue);
    $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d') . ' 00:00:00';
    $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d') . ' 23:59:59';

    $query = VehicleDetails::select(
        'vehicle_details.*',
        'vehicle_details.id as vehicle_id',
        'vehicle_details.created_at',
        'tbl_locations.id as location_id',
        'tbl_locations.name as branch_name',
        'vehicle_type.id as vehicle_type_id',
        'vehicle_type.type as vehicle_type',
        'vehicle_insurance_details.id as insurance_id',
        'vehicle_insurance_details.*',
        'vehicle_activities.id as activity_id',
        'vehicle_activities.*',
        'vehicle_service_details.id as service_id',
        'vehicle_service_details.*',
        'tblzones.id as zoneid',
        'tblzones.*'
    )
        ->selectRaw("(select count(*) from vehicle_document_details where vehicle_document_details.vehicle_id = vehicle_details.id) as document_count")
        ->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
        ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')
        ->join('tblzones', 'vehicle_details.zone_id', '=', 'tblzones.id')
        ->leftJoin('vehicle_activities', 'vehicle_details.id', '=', 'vehicle_activities.vehicle_id')
        ->leftJoin('vehicle_insurance_details', function ($join) {
            $join->on('vehicle_details.id', '=', 'vehicle_insurance_details.vehicle_id')
                ->where('vehicle_insurance_details.updated_at', '=', function ($query) {
                    $query->selectRaw('MAX(updated_at)')
                        ->from('vehicle_insurance_details')
                        ->whereColumn('vehicle_insurance_details.vehicle_id', 'vehicle_details.id');
                });
        })
        ->leftJoin('vehicle_service_details', function ($join) {
            $join->on('vehicle_details.id', '=', 'vehicle_service_details.vehicle_id')
                ->where('vehicle_service_details.updated_at', '=', function ($query) {
                    $query->selectRaw('MAX(updated_at)')
                        ->from('vehicle_service_details')
                        ->whereColumn('vehicle_service_details.vehicle_id', 'vehicle_details.id');
                });
        })
        ->orderBy('vehicle_details.created_at', 'desc')
        ->whereBetween('vehicle_details.created_at', [$startDate, $endDate]);


    if ($filterDataAll) {
        // foreach (explode(' AND ', $filterDataAll) as $condition) {
        //     [$column, $value] = explode('=', $condition);
        //     // dd($condition);

        //     $column = trim($column);
        //     $value = trim($value, "'");

        //     if ($column === 'vehicle_details.registration_number') {
        //         $entries = explode(",", $value);
        //         $ids = [];

        //         foreach ($entries as $entry) {
        //             $parts = explode(' - ', $entry);
        //             $ids[] = trim($parts[0]);
        //         }

        //         $query->whereIn($column, $ids);
        //     } else {
        //         $query->whereIn($column, explode(',', $value));
        //     }
        // }
        foreach (explode(' AND ', $filterDataAll) as $condition) {
            [$column, $value] = explode('=', $condition);

            $column = trim($column);
            $value = trim($value, "'");

            // Map unqualified column names to fully qualified ones if needed
            switch ($column) {
                case 'vehicle_id':
                    $column = 'vehicle_details.id'; // correct actual column
                    break;
                case 'registration_number':
                    $column = 'vehicle_details.registration_number';
                    break;
                // add more mappings as needed
            }

            if ($column === 'vehicle_details.registration_number') {
                $entries = explode(",", $value);
                $ids = [];

                foreach ($entries as $entry) {
                    $parts = explode(' - ', $entry);
                    $ids[] = trim($parts[0]);
                }

                $query->whereIn($column, $ids);
            } else {
                $query->whereIn($column, explode(',', $value));
            }
        }

    }

    // If you want to debug the SQL:
    $sql = str_replace_array('?', $query->getBindings(), $query->toSql());

    // Otherwise, actually fetch the data:
    $data = $query->orderBy('vehicle_details.created_at', 'desc')->get();
    // dd($data);

    return response()->json($data);
}

public function fetchUrlmorefittervechicle(Request $request)
{

    $fitterremovedata = $request->input('fitterremovedata');
    $moredatefittervale = $request->input('moredatefittervale');
    $dates = explode(' - ', $moredatefittervale);
    $startDate = $dates[0];  // "29/12/2024"
    $endDateview = $dates[1];    // "04/01/2025"
    $endDate = substr($endDateview, 0, 10);
    $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
    $startdates = $startDateFormatted . " 00:00:00";
    $enddates = $endDateFormatted . " 23:59:59";
    // Start the query

    $query = VehicleDetails::query()
        ->join('branches', 'vehicle_details.branch', '=', 'branches.id')
        ->join('zones', 'branches.zone_id', '=', 'zones.id')
        ->leftJoin('vehicle_insurance_details', 'vehicle_details.id', '=', 'vehicle_insurance_details.vehicle_id')
        ->leftJoin('vehicle_activities', 'vehicle_details.id', '=', 'vehicle_activities.vehicle_id')
        ->leftJoin('vehicle_service_details', 'vehicle_details.id', '=', 'vehicle_service_details.vehicle_id')
        ->leftJoin('vehicle_document_details', 'vehicle_details.id', '=', 'vehicle_document_details.vehicle_id')

        ->select('branches.Branch_name', 'zones.zone_name', 'vehicle_details.*')
        ->whereBetween('vehicle_details.created_at', [$startdates, $enddates]);


    if (is_array($fitterremovedata)) {
        $fitterremovedata = implode(' AND ', $fitterremovedata);
    } elseif (!is_string($fitterremovedata)) {
        return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
    }
    foreach (explode(' AND ', $fitterremovedata) as $condition) {
        [$column, $value] = explode('=', $condition);
        // Handle columns with comma-separated values
        $values = explode(',', trim($value, "'"));
        $query->where(function ($q) use ($column, $values) {
            foreach ($values as $val) {
                $q->orWhere($column, $val);
            }
        });
    }
    // Get the raw SQL query and bindings
    $sqlQuery = $query->toSql();
    $bindings = $query->getBindings();
    // Ensure bindings are safely quoted and replace placeholders
    $formattedQuery = $sqlQuery;
    foreach ($bindings as $binding) {
        $escapedBinding = "'" . addslashes($binding) . "'";
        $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
    }
    //dd($formattedQuery); // Dump the fully formatted query
    $doctorDetails = $query->get();
    return response()->json($doctorDetails);

}


    public function vehicleDocumentUrlFilter(Request $request)
    {
        //echo "<pre>";print_r($request->all());exit;
        $fitterremovedataall = $request->input('morefilltersall');
        $datefiltervalue = $request->input('moredatefittervale');
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";

        $query = VehicleDocument::select('vehicle_document_details.document_type', 'vehicle_document_details.expire_dates', 'vehicle_document_details.expire_date', 'vehicle_document_details.id as did', 'vehicle_document_details.document_name', 'vehicle_details.*', 'vehicle_type.type')
            ->join('vehicle_details', 'vehicle_document_details.vehicle_id', '=', 'vehicle_details.id')
            ->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
            ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->whereDate('vehicle_document_details.created_at', '>=', $startdates)->where('vehicle_document_details.created_at', '<=', $enddates);

        if ($fitterremovedataall) {
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                if ($column == 'vehicle_details.registration_number') {
                    $entries = explode(",", $value);
                    // Initialize an array to hold the cleaned results
                    $ids = [];
                    foreach ($entries as $entry) {
                        // Split each entry by the dash and get the first part (before the space and dash)
                        $parts = explode(" - ", $entry);
                        $ids[] = $parts[0];  // Store the ID part
                    }
                    // Join the IDs into a single string separated by commas
                    $result = implode(",", $ids);
                    $result = trim($result, "'");
                    $query->whereIn(trim($column), explode(',', $result));
                } else {
                    $value = trim($value, "'");
                    $query->whereIn(trim($column), explode(',', $value));
                }
            }
        }
        $query = $query->orderBy('vehicle_document_details.created_at', 'desc')->get();
        return response()->json($query);
    }

    public function vehicleMoreFilter(Request $request)
    {
        //echo "<pre>";print_r($request->all());exit;
        $vehicle_type = $request->input('vehicle_type');
        $fuel_type = $request->input('fuel_type');
        $fitterremovedataall = $request->input('fitterremovedataall');

        $query = VehicleDetails::select('vehicle_details.*', 'vehicle_details.created_at', 'tbl_locations.name', 'vehicle_type.type')->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
            ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id');

        if ($request->vehicle_type) {
            $query->where('vehicle_details.vehicle_type', $request->vehicle_type);
        }

        if ($request->fuel_type) {
            $query->where('vehicle_details.fuel_type', $request->fuel_type);
        }

        if ($fitterremovedataall) {
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                if (trim($value, "'") == 'Petrol') {
                    $query->where(trim($column), 1);
                } else if (trim($value, "'") == 'Diesel') {
                    $query->where(trim($column), 2);
                } else if (trim($value, "'") == 'Electronic Vehicle') {
                    $query->where(trim($column), 3);
                } else if (trim($value, "'") == 'CNG') {
                    $query->where(trim($column), 4);
                } else {
                    $value = trim($value, "'");
                    $query->where(trim($column), explode(',', $value));
                }
            }
        }

        $query = $query->orderBy('vehicle_details.created_at', 'desc')->get();
        return response()->json($query);
    }

    public function vehicleDocumentMoreFilter(Request $request)
    {
        //echo "<pre>";print_r($request->all());exit;
        $vehicle_type = $request->input('vehicle_type');
        $fuel_type = $request->input('fuel_type');
        $fitterremovedataall = $request->input('fitterremovedataall');

        $query = VehicleDocument::select('vehicle_document_details.document_type', 'vehicle_document_details.expire_dates', 'vehicle_document_details.expire_date', 'vehicle_document_details.id as did', 'vehicle_document_details.document_name', 'vehicle_details.*', 'vehicle_type.type')
            ->join('vehicle_details', 'vehicle_document_details.vehicle_id', '=', 'vehicle_details.id')
            ->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
            ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id');

        if ($request->vehicle_type) {
            $query->where('vehicle_details.vehicle_type', $request->vehicle_type);
        }

        if ($request->fuel_type) {
            $query->where('vehicle_details.fuel_type', $request->fuel_type);
        }

        if ($fitterremovedataall) {
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                if (trim($value, "'") == 'Petrol') {
                    $query->where(trim($column), 1);
                } else if (trim($value, "'") == 'Diesel') {
                    $query->where(trim($column), 2);
                } else if (trim($value, "'") == 'Electronic Vehicle') {
                    $query->where(trim($column), 3);
                } else if (trim($value, "'") == 'CNG') {
                    $query->where(trim($column), 4);
                } else {
                    $value = trim($value, "'");
                    $query->where(trim($column), explode(',', $value));
                }
            }
        }

        $query = $query->orderBy('vehicle_details.created_at', 'desc')->get();
        return response()->json($query);
    }

    public function vehicleDateFillter(Request $request)
    {
        $datefiltervalue = $request->input('datefiltervalue');
        $fitterremovedataall = $request->input('morefilltersall');

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        //echo "<pre>";print_r($dept);exit;
        $data = VehicleDocument::select('vehicle_document_details.document_type', 'vehicle_document_details.expire_dates', 'vehicle_document_details.expire_date', 'vehicle_document_details.id as did', 'vehicle_document_details.document_name', 'vehicle_details.*', 'vehicle_type.type')
            ->join('vehicle_details', 'vehicle_document_details.vehicle_id', '=', 'vehicle_details.id')
            ->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
            ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->whereDate('vehicle_document_details.expire_date', '>=', $startdates)->where('vehicle_document_details.expire_date', '<=', $enddates);

        if ($fitterremovedataall) {
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                $value = trim($value, "'");
                $data->whereIn(trim($column), explode(',', $value));
            }
        }
        $data = $data->orderBy('vehicle_document_details.created_at', 'desc')->get();

        return response()->json($data);
    }

    public function storeManagemenetImage(Request $request)
    {
        $files = [];
        $ticketId = $request->userid ?? '';
        if ($request->file('file')) {
            foreach ($request->file('file') as $key => $file) {
                $fileName = time() . rand(1, 99) . '.' . $file->extension();
                $file->move(public_path('uploads'), $fileName);
                $files[]['name'] = $fileName;
            }

            foreach ($files as $key => $file) {
                ImageModel::updateOrCreate(
                    ['ticket_id'   => '0000'],
                    ['imgName' => $file['name'], 'ticket_id' => $ticketId]
                );
            }
        }
        //$id = TicketActivitiesModel::find($ticketId);
        return response()->json(['status' => "success", 'userid' => $ticketId]);
    }

    public function ticketActivity($id)
    {
        $ticketDetail = TicketDetails::where('ticket_no', $id)->first();
        $ticketActivities = TicketActivityModel::where('ticket_id', $ticketDetail->id)->get();
        //echo "<pre>";print_r($ticketDetail);exit;
        $admin = auth()->user();
        return view('superadmin.ticketActivity', compact('ticketDetail', 'admin', 'ticketActivities'));
    }

    public function subDepartmentBasedId(Request $request)
    {
        $subcategories = SubCategoryModel::where('category_id', $request->category)->get();
        if ($subcategories) {
            return response()->json([
                'status' => 200,
                'subcategories' => $subcategories,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No priority found.'
            ]);
        }
    }

    public function getTicket()
    {
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        $statuses = StatusModel::get();
        $priorities = PriorityModel::get();
        $categories = CategoryModel::where('dept_status', 1)->get();
        //$ticketDetails = TicketModel::orderBy('id', 'desc')->get();
        $locations = TblLocationModel::orderBy('name', 'asc')->get();
        $statustable = StatusModel::orderBy('id','asc')->get();
        // dd($status);
        //print_r($ticketDetails); exit;
        return view('superadmin.ticket', compact('admin', 'statuses', 'priorities', 'locations', 'categories','statustable'));
    }
    public function getTicketMaster()
    {
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        $statuses = StatusModel::get();
        $priorities = PriorityModel::get();
        $categories = CategoryModel::where('dept_status', 1)->get();
        //$ticketDetails = TicketModel::orderBy('id', 'desc')->get();
        $locations = TblLocationModel::orderBy('name', 'asc')->get();
        $statustable = StatusModel::orderBy('id','asc')->get();
        // dd($status);
        //print_r($ticketDetails); exit;
        return view('superadmin.ticketmaster', compact('admin', 'statuses', 'priorities', 'locations', 'categories','statustable'));
    }
    public function updateStatus(Request $request)
    {
        // dd($request);
        $ticket = TicketDetails::find($request->id);
        if ($ticket) {

            $ticket->ticket_status = $request->status_id;
            if($request->status ==="Approved"){
                $ticket->is_management_approve = 1;
            }
            $ticket->save();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }


    public function sendMessage1(Request $request)
    {
        // dd($request);
        $request->validate([
            'ticket_id' => 'required|integer',
            'message' => 'nullable|string',
        ]);

        $chat = TicketChat::create([
            'ticket_id' => $request->ticket_id,
            'user_id'   => auth()->id(),
            'message'   => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $chat->id,
                'message' => $chat->message,
                'created_at' => $chat->created_at->toDateTimeString(),
            ]
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer|exists:tbl_ticket_details,id',
            'message' => 'nullable|string|max:2000',
            'file' => 'nullable|file|max:20480', // 20 MB
        ]);

        $ticketId = $request->ticket_id;
        $message = $request->message;
        $file = $request->file('file');

        $path = null;
        $fileName = null;

        if ($file) {
            $uploadPath = public_path('uploads/customers');

            // Create directory if it doesn't exist
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $originalName = $file->getClientOriginalName();
            $uniqueName = time() . '_' . preg_replace('/\s+/', '_', $originalName);

            // Move the file to public/uploads/customers
            $file->move($uploadPath, $uniqueName);

            // Save relative path for URL
            $path = 'uploads/customers/' . $uniqueName;
            $fileName = $originalName;
        }

        // Create chat
        $chat = TicketChat::create([
            'ticket_id' => $ticketId,
            'user_id'   => Auth::id(),
            'message'   => $message,
            'file_path' => $path,
            'file_name' => $fileName,
        ]);

        return response()->json([
            'success'   => true,
            'data'      => $chat->load('sender'),
            'file_url'  => $path ? asset($path) : null, // Correct URL
        ]);
    }

    /**
     * Get chat history for a ticket
     */
    public function getHistory($ticket_id)
    {
        $messages = TicketChat::where('ticket_id', $ticket_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                // dd($msg->user_id);
                $user = \DB::table('users')->where('id', $msg->user_id)->first();
                return [
                    'id' => $msg->id,
                    'sender' => $msg->user_id === auth()->id() ? 'you' : 'other',
                    'message' => $msg->message,
                    'file_url' => $msg->file_url,
                    'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                    'user_name' => $user ? $user->user_fullname : '',
                ];
            });
        // dd($messages);
        return response()->json($messages);
    }


    public function fetchticketfitter(Request $request)
    {
        $fitterremovedataall = $request->input('morefilltersall');
        $datefiltervalue = $request->input('moredatefittervale');

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";

        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                      ->where('tbl_ticket_details.is_management_approve', 1)
                      ->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);

        if($fitterremovedataall){
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                    $value = trim($value, "'");
                    //echo "<pre>";print_r($value);
                    $query->whereIN(trim($column), explode(',', $value));
            }
        }
		$ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

     public function fetchticketfitterremove(Request $request)
    {
        $fitterremovedataall = $request->input('fitterremovedataall');
        $datefiltervalue = $request->input('moredatefittervale');
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";

        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                ->where('tbl_ticket_details.is_management_approve', 1)
                ->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);

                             // Split conditions by 'AND' and loop through them
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
            if($column == 'tbl_locations.name'){
                $value = preg_replace("/(\w)-(\w)/", "$1 - $2", $value);
                $query->where(trim($column), trim($value, " '"));
            }else{
                $value = trim($value, "'");
                $query->whereIN(trim($column), explode(',', $value));
            }
        }

		$ticketdetails = $query->groupBy('tbl_ticket_details.ticket_no')->orderBy('created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

     public function fetchallfitterremove(Request $request)
    {
        $fitterremovedataall = $request->input('fitterremovedataall');
        $role_id = auth()->user()->role_id;
        $access_limits = auth()->user()->access_limits;
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id');
                        if($role_id == 1 && $access_limits == 1){
                            $query->where('tbl_ticket_details.is_management_approve', 1);
                        }else if($role_id == 1 && $access_limits == 2){
                            $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
                                $dept = array();
                                foreach($depart_id as $depart){
                                    $dept[] = $depart->depart_id;
                                }
							 $query->join('tbl_department_user', 'tblzones.id', '=', 'tbl_department_user.zone_id');
                            $query->where('admin_user_departments.user_id', auth()->user()->id);
                            $query->whereIn('admin_user_departments.depart_id',$dept);
                        }
                             // Split conditions by 'AND' and loop through them
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
            if($column == 'tbl_locations.name'){
                $value = preg_replace("/(\w)-(\w)/", "$1 - $2", $value);
                $query->where(trim($column), trim($value, " '"));
            }else{
                $value = trim($value, "'");
                $query->whereIN(trim($column), explode(',', $value));
            }
        }

		$ticketdetails = $query->groupBy('tbl_ticket_details.ticket_no')->orderBy('created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

    public function fetchmyticketfitter(Request $request)
    {
        $fitterremovedataall = $request->input('morefilltersall');
        $datefiltervalue = $request->input('moredatefittervale');

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";

        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                    ->where('tbl_ticket_details.created_by', auth()->user()->id)
                    ->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);

        if($fitterremovedataall){
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                    $value = trim($value, "'");
                    $query->whereIN(trim($column),explode(',', $value));
            }
        }
		$ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

      public function fetchmyticketfitterremove(Request $request)
    {
        $fitterremovedataall = $request->input('fitterremovedataall');
        $datefiltervalue = $request->input('moredatefittervale');

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";

        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                    ->where('tbl_ticket_details.created_by', auth()->user()->id)
                    ->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
                             // Split conditions by 'AND' and loop through them
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
            if($column == 'tbl_locations.name'){
                $value = preg_replace("/(\w)-(\w)/", "$1 - $2", $value);
                $query->where(trim($column), trim($value, " '"));
            }else{
                $value = trim($value, "'");
                $query->whereIN(trim($column), explode(',', $value));
            }
        }

		$ticketdetails = $query->groupBy('tbl_ticket_details.ticket_no')->orderBy('created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

    public function fetchallticketfitter(Request $request)
    {
        $fitterremovedataall = $request->input('morefilltersall');
        $moredatefittervale = $request->input('moredatefittervale');
        $dates = explode(' - ', $moredatefittervale);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $role_id = auth()->user()->role_id;
        $access_limits = auth()->user()->access_limits;
        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";

        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->leftjoin('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->leftjoin('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->leftjoin('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->leftjoin('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->leftjoin('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->leftjoin('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->leftjoin('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->leftjoin('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->leftjoin('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id');
                        if($role_id == 1 && $access_limits == 1){
                            $query->where('tbl_ticket_details.is_management_approve', 1);
                        }else if($role_id == 1 && $access_limits == 2){
                            $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
                                $dept = array();
                                foreach($depart_id as $depart){
                                    $dept[] = $depart->depart_id;
                                }
							$query->leftjoin('tbl_department_user', 'tblzones.id', '=', 'tbl_department_user.zone_id');
                            $query->where('admin_user_departments.user_id', auth()->user()->id);
                            $query->whereIn('admin_user_departments.depart_id',$dept);
                        }
        // Add whereBetween for created_at
        $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
                             // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                    $value = trim($value, "'");
                    $query->whereIn(trim($column), explode(',', $value));
            }
		$ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();

        return response()->json($ticketdetails);
    }

    public function storeImage(Request $request)
    {
        $files = [];
        $ticketId = $request->userid ?? '';
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $key => $file) {
                $fileName = time() . rand(1, 99) . '.' . $file->extension();
                $file->move(public_path('uploads'), $fileName);
                $files[]['name'] = $fileName;
            }

            foreach ($files as $key => $file) {
                ImageModel::updateOrCreate(
                    ['ticket_id'   => '0000'],
                    ['imgName' => $file['name'], 'ticket_id' => $ticketId]
                );
            }
        }
        $id = TicketActivitiesModel::find($ticketId);
        return response()->json(['status' => "success", 'userid' => $ticketId]);
    }

   	public function ticketAdded(Request $request)
    {
        // dd($request);
		// echo "<pre>";print_r($request->all());exit;
        $validatedData = $request->validate([
            'location' => 'required|string|max:255',
                    'department' => 'required|string|max:255',
                    'sub_department_id' => 'required|string|max:255',
                    'target_date' => 'required|string|max:255',
                    'subject' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
                    'priority' => 'required|string|max:255',
					'files.*' => 'nullable|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            ]);
        $status = '1';
        $ticketDetail = TicketDetails::latest()->first();
        if($ticketDetail) {
            $ticketNo = $ticketDetail->ticket_no + 1;
        } else {
            $ticketNo = 1000;
        }

        $location_id =  TblLocationModel::select('id','zone_id')->where('name', $request->location)->first();
        $department_id =  CategoryModel::select('id')->where('depart_name', $request->department)->first();
        $from_department_id =  CategoryModel::select('id')->where('depart_name', $request->from_department)->first();
        $ticAdmin =  AdminUserDepartments::select('admin_user_departments.id','admin_user_departments.user_id','admin_user_departments.depart_id','users.zone_id')->join('users', 'admin_user_departments.user_id', '=', 'users.id')
                    ->where('admin_user_departments.depart_id', $department_id->id)
                    ->get();
        // echo  "<pre>";print_r($ticAdmin);exit;

        if(count($ticAdmin) == 0){
            return response()->json(['status'=>"error",'errors'=>'No ticket handler for this Department!']);
        }
        $removeDept = TblUserDepartments::where('depart_id', $ticAdmin[0]->depart_id)->delete();
        foreach($ticAdmin as $user){
            TblUserDepartments::updateOrCreate([
                'admin_user_departments_id' => $user->id,
                'user_id' => $user->user_id,
                'depart_id' => $user->depart_id,
                'zone_id' => $user->zone_id,
            ]);
        }
        // dd($request->hasFile('files'));
        $fileNames = [];
        if ($request->hasFile('files')) {
            $uploadPath = public_path('ticket_attachments');

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $uniqueFileName = time() . '_' . $originalName;
                $file->move($uploadPath, $uniqueFileName);
                $fileNames[] = $uniqueFileName;
            }
        }


            $data = TicketDetails::create($request->only(['sub_department_id']));
            //echo "<pre>";print_r($data);exit;
            $ticketCreate = TicketDetails::updateOrCreate(['id'   => $data['id']],array_merge($validatedData, [
                'created_by' => auth()->user()->id,
                'ticket_no'     => $ticketNo,
                'ticket_status' => $status,
                'is_read' => '1',
                'department_id' => $department_id->id,
                'from_department_id' => $from_department_id->id,
                'location_id' => $location_id->id,
                'image_paths' => $fileNames,
                'zone_id' => $location_id->zone_id,
            ]));

            $ticketupdate = TicketActivityModel::updateOrCreate(
                ['id'   => '0000'],
                [
                    'ticket_id' => $data['id'],
                    'ticket_status' => $status,
                    'staff_id' => auth()->user()->id,
                    'priotity_level' => $request->priority,
                    'description' => $request->description,
                    'department_id' => $department_id->id,
                    'sub_department_id' => $request->sub_department_id,
                    'created_by' => auth()->user()->id
                ]
            );

            return response()->json(['status'=>"success", 'user_id'=>$ticketupdate['id'], 'ticketId' => $ticketCreate['id']]);
    }
   	public function ticketMasterAdded(Request $request)
    {
        // dd($request);
		// echo "<pre>";print_r($request->all());exit;
        $validatedData = $request->validate([
            'location' => 'required|string|max:255',
                    'from_department' => 'required|string|max:255',
                    'user_id' => 'required|string|max:255',
            ]);
            // dd($validatedData);
        $id = $request->ticketmasterId;

        // Fetch related models based on names
        $location = TblLocationModel::select('id', 'zone_id')->where('name', $request->location)->first();
        $department = CategoryModel::select('id')->where('depart_name', $request->from_department)->first();
        $user = usermanagementdetails::select('id')->where('user_fullname', $request->user_id)->first();

        if (empty($id)) {
            $adminDept = AdminUserDepartments::create([
                'user_id' => $user->id,
                'depart_id' => $department->id,
            ]);

            TblDepartmentsUser::create([
                'admin_user_departments_id' => $adminDept->id,
                'user_id' => $user->id,
                'depart_id' => $department->id,
                'zone_id' => $location->zone_id,
                'branch_id' => $location->id,
            ]);
        } else {
            $existing = TblDepartmentsUser::find($id);

            if ($existing) {
                $existing->update([
                    'user_id' => $user->id,
                    'depart_id' => $department->id,
                    'zone_id' => $location->zone_id,
                    'branch_id' => $location->id,
                ]);
                AdminUserDepartments::where('id', $existing->admin_user_departments_id)->update([
                    'user_id' => $user->id,
                    'depart_id' => $department->id,
                ]);
            } else {
                return back()->with('error', 'Record not found for update.');
            }
        }

        return response()->json([
            'status' => "success",
            'message' => $id == "" ? "Created Successfully" : "Updated Successfully"
        ]);
    }

    //  public function myTicketFetch(Request $request)
    // {
    //     // dd($request);
    //     $datefiltervalue = $request->input('moredatefittervale');
    //     $statusid = $request->input('statusid');
    //     $dates = explode(' - ', $datefiltervalue);
    //     $startDate = $dates[0];  // "29/12/2024"
    //     $endDate = $dates[1];    // "04/01/2025"
    //     $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    //     $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

    //     $startdates=$startDateFormatted." 00:00:00";
    //     $enddates=$endDateFormatted." 23:59:59";
    //     $query = TicketDetails::select(
    //                         'tbl_ticket_details.*',
    //                         'tbl_locations.name',
    //                         'department.depart_name as depart_name',            // or: as department_name
    //                         'from_department.depart_name as from_department_name',
    //                         'sub_category.sub_category_name',
    //                         'ticket_priority.priority_name',
    //                         'ticket_status_master.status_name',
    //                         'users.user_fullname',
    //                         'ticket_priority.priority_color'
    //                     )
    //                     ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
    //                     ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
    //                     ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
    //                     ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
    //                     ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
    //                     ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
    //                     ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
    //                     ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
    //                     ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
    //                     ->where('tbl_ticket_details.created_by', auth()->user()->id);

    //                     if($statusid == 2){
    //                         $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
    //                     }
    //                     $ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
    //                     // dd($ticketdetails);
    //     return response()->json($ticketdetails);
    // }
    public function myTicketFetch(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale');
        $statusid = $request->input('statusid');
        $dates = explode(' - ', $datefiltervalue);

        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');

        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";

       $ticketdetails = TicketDetails::select(
        'tbl_ticket_details.*',
        'tbl_locations.name',
        'department.depart_name as depart_name',
        'from_department.depart_name as from_department_name',
        'sub_category.sub_category_name',
        'ticket_priority.priority_name',
        'ticket_status_master.status_name',
        'users.user_fullname',
        'ticket_priority.priority_color'
    )
    ->leftJoin('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
    ->leftJoin('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
    ->leftJoin('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
    ->leftJoin('sub_category', 'tbl_ticket_details.sub_department_id', '=', 'sub_category.id')
    ->leftJoin('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
    ->leftJoin('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
    ->leftJoin('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
    ->leftJoin('users', 'tbl_ticket_details.created_by', '=', 'users.id')
    ->leftJoin('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
    ->where('tbl_ticket_details.created_by', auth()->user()->id)
    ->distinct('tbl_ticket_details.id')
    ->orderBy('tbl_ticket_details.created_at', 'desc')
    ->get();


        if ($statusid == 2) {
            $ticketdetails->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
        }


        return response()->json($ticketdetails);
    }
    //santh
    public function myTicketMasterFetch(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale');

        $statusid = $request->input('statusid');
        $dates = explode(' - ', $datefiltervalue);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');

        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";

       $ticketmasterdetails = TblDepartmentsUser::select(
            'tbl_department_user.*',
            'users.user_fullname',
            'tblzones.name as zone_name',
            'tbl_locations.name as branch_name',
            'department.depart_name as depart_name',

        )
        ->leftJoin('tblzones', 'tbl_department_user.zone_id', '=', 'tblzones.id')
        ->leftJoin('tbl_locations', 'tbl_department_user.branch_id', '=', 'tbl_locations.id')
        ->leftJoin('users', 'tbl_department_user.user_id', '=', 'users.id')
        ->leftJoin('tbl_user_departments as department', 'tbl_department_user.depart_id', '=', 'department.id')

        ->orderBy('tbl_department_user.created_at', 'desc')
        ->get();

        if (!empty($datefiltervalue)) {
            $ticketmasterdetails->whereBetween('tbl_department_user.created_at', [$startdates, $enddates]);
        }


        return response()->json($ticketmasterdetails);
    }
    public function myticketmasterdate(Request $request)
    {
        $datefiltervalue = $request->input('datefiltervalue');
        $statusid = $request->input('statusid');
        $dates = explode(' - ', $datefiltervalue);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');

        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";

       $query = TblDepartmentsUser::select(
                'tbl_department_user.*',
                'users.user_fullname',
                'tblzones.name as zone_name',
                'tbl_locations.name as branch_name',
                'department.depart_name as depart_name',
            )
            ->leftJoin('tblzones', 'tbl_department_user.zone_id', '=', 'tblzones.id')
            ->leftJoin('tbl_locations', 'tbl_department_user.branch_id', '=', 'tbl_locations.id')
            ->leftJoin('users', 'tbl_department_user.user_id', '=', 'users.id')
            ->leftJoin('tbl_user_departments as department', 'tbl_department_user.depart_id', '=', 'department.id')
            ->orderBy('tbl_department_user.created_at', 'desc');
        if (!empty($datefiltervalue)) {
            $query->whereBetween('tbl_department_user.created_at', [$startdates, $enddates]);
        }
        $ticketmasterdetails = $query->get();
        // dd($ticketmasterdetails);
        return response()->json($ticketmasterdetails);
    }
    public function fetchmyticketmasterfitter(Request $request)
    {
        $fitterremovedataall = $request->input('morefilltersall');
        $datefiltervalue = $request->input('moredatefittervale');

        // --- Date Range ---
        $startdates = $enddates = null;
        if (!empty($datefiltervalue)) {
            $dates = explode(' - ', $datefiltervalue);
            $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->format('Y-m-d');
            $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->format('Y-m-d');

            $startdates = $startDateFormatted . " 00:00:00";
            $enddates = $endDateFormatted . " 23:59:59";
        }

        // --- Base Query ---
        $query = TblDepartmentsUser::select(
                'tbl_department_user.*',
                'tbl_locations.name as branch_name',
                'department.depart_name as depart_name',
                'users.user_fullname as user_fullname',
                'tblzones.name as zone_name'
            )
            ->leftJoin('tbl_locations', 'tbl_department_user.branch_id', '=', 'tbl_locations.id')
            ->leftJoin('tbl_user_departments as department', 'tbl_department_user.depart_id', '=', 'department.id')
            ->leftJoin('users', 'tbl_department_user.user_id', '=', 'users.id')
            ->leftJoin('tblzones', 'tbl_department_user.zone_id', '=', 'tblzones.id');

        // --- Date Filter ---
        if (!empty($startdates) && !empty($enddates)) {
            $query->whereBetween('tbl_department_user.created_at', [$startdates, $enddates]);
        }

        // --- Dynamic Filters ---
        if (!empty($fitterremovedataall)) {
            $conditions = explode(' AND ', $fitterremovedataall);

            foreach ($conditions as $condition) {
                $parts = explode('=', $condition);
                if (count($parts) == 2) {
                    $column = trim($parts[0]);
                    $value = trim($parts[1], " '");
                    // ✅ Handle multiple values
                    if (strpos($value, ',') !== false) {
                        $values = array_map('trim', explode(',', $value));
                        $query->whereIn($column, $values);
                    } else {
                        // ✅ Use LIKE for name-based fields
                        if (in_array($column, [
                            'tbl_locations.name',
                            'tblzones.name',
                            'users.user_fullname',
                            'department.depart_name'
                        ])) {
                            $query->where($column, 'LIKE', "%{$value}%");
                        } else {
                            $query->where($column, $value);
                        }
                    }
                }
            }
        }

        $results = $query->orderBy('tbl_department_user.created_at', 'desc')->get();

        return response()->json($results);
    }



    public function ticketFetch(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale');
        $statusid = $request->input('statusid');
        $user_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        $access_limits = auth()->user()->access_limits;
        // echo "<pre>";print_r($access_limits);exit;
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
            $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'users.role_id',
                            'ticket_priority.priority_color'
                        )
                        ->leftJoin('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->leftJoin('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->leftJoin('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->leftJoin('sub_category', 'department.id', '=', 'sub_category.category_id')// double-check this relation
                        ->leftJoin('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->leftJoin('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->leftJoin('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->leftJoin('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->leftJoin('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id');
                        if($role_id == 1 && $access_limits == 1){
                            $query->where('tbl_ticket_details.is_management_approve', 1);
                        }else if($role_id == 1 && $access_limits == 2){
                            // $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
                            //     $dept = array();
                            //     foreach($depart_id as $depart){
                            //         $dept[] = $depart->depart_id;
                            //     }
							// $query->join('tbl_department_user', 'tblzones.id', '=', 'tbl_department_user.zone_id');
                            // $query->where('admin_user_departments.user_id', auth()->user()->id);
                            // $query->whereIn('admin_user_departments.depart_id',$dept);
                            $depart_id = TblUserDepartments::where('user_id', $user_id)
                                        ->pluck('depart_id')
                                        ->toArray();
                            $query ->whereIn('tbl_ticket_details.department_id', $depart_id);
                        }
                        if($statusid == 2){
                            $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
                        }
        $ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        // dd($ticketdetails);
        return response()->json($ticketdetails);
    }

    public function adminApproveActivity($id)
    {
        if ($id) {
            TicketDetails::where('id', '=', $id)
                ->update(array('is_management_approve' => 1,'ticket_status' => 5, 'is_read' => 5));
            $ticketDetail = TicketDetails::find($id);
            $ticketActivity = TicketActivityModel::where('ticket_id',$id)->first();
            //echo "<pre>"; print_r($ticketActivity->staff_id); exit;
            TicketActivityModel::updateOrCreate(
                ['id'   => '0000'],
                [
                    'ticket_id'     => $id,
                    'ticket_status' => $ticketDetail->ticket_status,
                    'staff_id' => $ticketActivity->staff_id,
                    'priotity_level' => $ticketDetail->priority,
                    'created_by' => auth()->user()->id,
                    'description' => 'This ticket has been sent to management for approval'
                ]
            );

            return response()->json([
                'status' => 200,
                'message' => 'Approved status sent successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Approved request failed.'
            ]);
        }
    }

     public function adminReplyActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_status' => 'required|max:191'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages()
            ]);
        } else {
            $activity = TicketActivityModel::create($request->only(['staff_id', 'priotity_level', 'ticket_status', 'description', 'ticket_id', 'department_id', 'sub_department_id', 'created_by']));

            $ticStatusUpdate = TicketDetails::where('id', $request->ticket_id)
                ->update([
                    'ticket_status' => $request->ticket_status,
                    'is_read' => 3,
                ]);

            //Log::info('Rows affected: ' . $ticStatusUpdate);
            if (!$activity) {
                return response()->json(['status' => "error"]);
            }
            return response()->json(['status' => "success", 'user_id' => $activity['id']]);
        }
    }

    public function manageReplyActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_status'=>'required|max:191'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=>400,
                'errors'=>$validator->messages()
            ]);
        }
        else
        {
            $activity = TicketActivityModel::create($request->only(['staff_id', 'priotity_level', 'ticket_status', 'description', 'ticket_id','created_by']));

            //$ticStatus = ($request->ticket_status == 9) ? '1' : '0';
            //$ticStatusUpdate = TicketModel::where('id', $request->ticket_id)->update(array());
            $statusUpdate = TicketDetails::where('id', $request->ticket_id)
                            ->update(['is_management_approve' => 0,
                            'ticket_status' => $request->ticket_status, 'is_read' => 1]);
            if (!$activity) {
                return response()->json(['status'=>"error"]);
            }
            return response()->json(['status'=>"success", 'user_id'=>$activity['id']]);
        }
    }

    public function staffReplyActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description'=>'required|max:191'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=>400,
                'errors'=>$validator->messages()
            ]);
        }
        else
        {
            $activity = TicketActivityModel::create($request->only(['created_by', 'priotity_level', 'ticket_status', 'description', 'ticket_id', 'department_id','sub_department_id','staff_id']));

            if (!$activity) {
                return response()->json(['status'=>"error"]);
            }
            return response()->json(['status'=>"success", 'user_id'=>$activity['id']]);
        }
    }

   public function allTicketFetch(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale');
        $statusid = $request->input('statusid');
        $user_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        $access_limits = auth()->user()->access_limits;
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        // $dept_user_ids = AdminUserDepartments::where('user_id', $user_id)
        //     ->pluck('depart_id')
        //     ->toArray();

        $query = TicketDetails::select(
                    'tbl_ticket_details.*',
                    'tbl_locations.name',
                    'tblzones.name as zone_name',
                    'department.depart_name as depart_name',
                    'from_department.depart_name as from_department_name',
                    'sub_category.sub_category_name',
                    'ticket_priority.priority_name',
                    'ticket_status_master.status_name',
                    'users.user_fullname',
                    'ticket_priority.priority_color'
                )
                ->leftJoin('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                ->leftJoin('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                ->leftJoin('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                ->leftJoin('sub_category', 'tbl_ticket_details.sub_department_id', '=', 'sub_category.id')
                ->leftJoin('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                ->leftJoin('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                // ->leftJoin('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id') // REMOVE THIS
                ->leftJoin('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                ->leftJoin('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id');
            if ($role_id == 1 && $access_limits == 2) {
                $depart_id = TblUserDepartments::where('user_id', $user_id)
                    ->pluck('depart_id')
                    ->toArray();
                    // dd($depart_id);
                // $query->join('tbl_department_user', 'tblzones.id', '=', 'tbl_department_user.zone_id')
                //     ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id') // move inside this block only
                //     ->where('admin_user_departments.user_id', $user_id)
                //     ->whereIn('tbl_ticket_details.department_id', $depart_id);
                $query ->whereIn('tbl_ticket_details.department_id', $depart_id);
            }
            // if($role_id == 1 && $access_limits == 2){
            //     $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
            //     $dept = array();
            //     foreach($depart_id as $depart){
            //         $dept[] = $depart->depart_id;
            //     }
			// 	$query->join('tbl_department_user', 'tblzones.id', '=', 'tbl_department_user.zone_id');
            //     $query->where('admin_user_departments.user_id', auth()->user()->id);
            //     $query->whereIn('admin_user_departments.depart_id',$dept);
            // }
            if($statusid == 2){
                $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
            }
            $ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
            // dd($ticketdetails);
            return response()->json($ticketdetails);
    }

   public function myticketFillter(Request $request)
    {
        $datefiltervalue = $request->input('dateVal');
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
            ->where('tbl_ticket_details.created_by', auth()->user()->id)
            ->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates])
            ->groupBy('tbl_ticket_details.id');

		if ($request->statusValues) {
                $query->whereIN('ticket_status', explode(',', $request->statusValues));
            }

		if ($request->priorityValues) {
			$query->whereIN('priority', explode(',', $request->priorityValues));
		}

		if ($request->location_id) {
                $query->where('location_id', $request->location_id);
            }

		if ($request->dateType) {
                $startDate = $request->startDate;
                $endDate = $request->endDate;
                if ($request->dateType == 1) {
                    $query->whereDate('tbl_ticket_details.created_at', '>=', $startDate)->where('tbl_ticket_details.created_at', '<=', $endDate);
                } else if($request->dateType == 2) {
                    $query->whereDate('tbl_ticket_details.updated_at', '>=', $startDate)->where('tbl_ticket_details.updated_at', '<=', $endDate);
                } else if($request->dateType == 3) {
                    $query->whereDate('tbl_ticket_details.target_date', '>=', $startDate)->where('tbl_ticket_details.target_date', '<=', $endDate);
                }
            }
		$ticketdetails = $query->orderBy('tbl_ticket_details.created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

     public function allticketFillter(Request $request)
    {
        $datefiltervalue = $request->input('dateVal');
        $role_id = auth()->user()->role_id;
        $access_limits = auth()->user()->access_limits;
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id');
                        if($role_id == 1 && $access_limits == 1){
                            $query->where('tbl_ticket_details.is_management_approve', 1);
                        }else if($role_id == 1 && $access_limits == 2){
                            $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
                                $dept = array();
                                foreach($depart_id as $depart){
                                    $dept[] = $depart->depart_id;
                                }
							$query->join('tbl_department_user', 'tblzones.id', '=', 'tbl_department_user.zone_id');
                            $query->where('admin_user_departments.user_id', auth()->user()->id);
                            $query->whereIn('admin_user_departments.depart_id',$dept);
                        }
                     $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
                     $query->groupBy('tbl_ticket_details.ticket_no');

		if ($request->statusValues) {
                $query->whereIN('ticket_status', explode(',', $request->statusValues));
            }

		if ($request->priorityValues) {
			$query->whereIN('priority', explode(',', $request->priorityValues));
		}

		if ($request->location_id) {
                $query->where('location_id', $request->location_id);
            }

		if ($request->dateType) {
                $startDate = $request->startDate;
                $endDate = $request->endDate;
                if ($request->dateType == 1) {
                    $query->whereDate('tbl_ticket_details.created_at', '>=', $startDate)->where('tbl_ticket_details.created_at', '<=', $endDate);
                } else if($request->dateType == 2) {
                    $query->whereDate('tbl_ticket_details.updated_at', '>=', $startDate)->where('tbl_ticket_details.updated_at', '<=', $endDate);
                } else if($request->dateType == 3) {
                    $query->whereDate('tbl_ticket_details.target_date', '>=', $startDate)->where('tbl_ticket_details.target_date', '<=', $endDate);
                }
            }
		$ticketdetails = $query->orderBy('tbl_ticket_details.created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

   public function ticketFillter(Request $request)
    {
        $datefiltervalue = $request->input('dateVal');

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                    ->where('tbl_ticket_details.is_management_approve', 1)
                    ->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates])
                    ->groupBy('tbl_ticket_details.ticket_no');

		if ($request->statusValues) {
                $query->whereIN('ticket_status', explode(',', $request->statusValues));
            }

		if ($request->priorityValues) {
			$query->whereIN('priority', explode(',', $request->priorityValues));
		}

		if ($request->location_id) {
                $query->where('location_id', $request->location_id);
            }

		if ($request->dateType) {
                $startDate = $request->startDate;
                $endDate = $request->endDate;
                if ($request->dateType == 1) {
                    $query->whereDate('tbl_ticket_details.created_at', '>=', $startDate)->where('tbl_ticket_details.created_at', '<=', $endDate);
                } else if($request->dateType == 2) {
                    $query->whereDate('tbl_ticket_details.updated_at', '>=', $startDate)->where('tbl_ticket_details.updated_at', '<=', $endDate);
                } else if($request->dateType == 3) {
                    $query->whereDate('tbl_ticket_details.target_date', '>=', $startDate)->where('tbl_ticket_details.target_date', '<=', $endDate);
                }
            }
		$ticketdetails = $query->orderBy('tbl_ticket_details.created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

     public function ticketDateFillter(Request $request)
    {
        $datefiltervalue = $request->input('datefiltervalue');
        $fitterremovedataall = $request->input('morefilltersall');
        $role_id = auth()->user()->role_id;
        $access_limits = auth()->user()->access_limits;
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        //echo "<pre>";print_r($dept);exit;
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id');
                    if($role_id == 1 && $access_limits == 1){
                            $query->where('tbl_ticket_details.is_management_approve', 1);
                        }else if($role_id == 1 && $access_limits == 2){
                            $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
                                $dept = array();
                                foreach($depart_id as $depart){
                                    $dept[] = $depart->depart_id;
                                }
							$query->join('tbl_department_user', 'tblzones.id', '=', 'tbl_department_user.zone_id');
                            $query->where('admin_user_departments.user_id', auth()->user()->id);
                            $query->whereIn('admin_user_departments.depart_id',$dept);
                        }else{
                            $query->where('tbl_ticket_details.created_by', auth()->user()->id);
                        }
                $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);

        if($fitterremovedataall){
            // Split conditions by 'AND' and loop through them
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
                $value = trim($value, "'");
                $query->whereIn(trim($column), explode(',', $value));
        }
    }
       $data = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        return response()->json($data);
    }

      public function allticketDateFillter(Request $request)
    {
        $datefiltervalue = $request->input('datefiltervalue');
        $fitterremovedataall = $request->input('morefilltersall');
        $role_id = auth()->user()->role_id;
        $access_limits = auth()->user()->access_limits;
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        //echo "<pre>";print_r($dept);exit;
        $data = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->leftjoin('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->leftjoin('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->leftjoin('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->leftjoin('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->leftjoin('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->leftjoin('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->leftjoin('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->leftjoin('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->leftjoin('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id');
                        if($role_id == 1 && $access_limits == 2){
                            $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
                                $dept = array();
                                foreach($depart_id as $depart){
                                    $dept[] = $depart->depart_id;
                                }
							$data->leftjoin('tbl_department_user', 'tblzones.id', '=', 'tbl_department_user.zone_id');
                            $data->where('admin_user_departments.user_id', auth()->user()->id);
                            $data->whereIn('admin_user_departments.depart_id',$dept);
                        }
                $data->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
                // dd($data);
        if($fitterremovedataall){
            // Split conditions by 'AND' and loop through them
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
                $value = trim($value, "'");
                $data->whereIn(trim($column), explode(',', $value));
        }
    }
       $data = $data->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        return response()->json($data);
    }

    public function myticketDateFillter(Request $request)
    {
        $datefiltervalue = $request->input('datefiltervalue');
        $fitterremovedataall = $request->input('morefilltersall');

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        //echo "<pre>";print_r($dept);exit;
        $data = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                        ->where('tbl_ticket_details.created_by', auth()->user()->id)
                        ->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
        if($fitterremovedataall){
            // Split conditions by 'AND' and loop through them
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
                $value = trim($value, "'");
                $data->whereIn(trim($column), explode(',', $value));
        }
    }
               $data = $data->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        return response()->json($data);
    }

    public function dailySummary()
    {
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        //$locations = TblLocationModel::orderBy('name', 'asc')->get();
        return view('superadmin.daily_summary', compact('admin'));
    }

    public function registrationReport()
    {
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.registration_report', compact('admin'));
    }

   public function checkIn()
    {
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        $apiUrl = 'https://app.draravindsivf.com/hrms/employee_details_api.php';
        $apiKey = '3x@MpL3-K3Y-98fG_2025!';
         $response = Http::timeout(60)->withoutVerifying()->get($apiUrl, [
                'api_key' => $apiKey
            ]);

        if ($response->successful()) {
             $employeeData = $response->json();
        } else {
            return response()->json(['error' => 'API call failed'], 500);
        }
        $employeeData = $employeeData['data'];
        array_shift($employeeData);
        // echo "<pre>";print_r($employeeData['data']);exit;
        //$locations = TblLocationModel::orderBy('name', 'asc')->get();
        return view('superadmin.check_in', compact('admin','employeeData'));
    }
    public function dailyDateFilter(Request $request)
    {
        set_time_limit(0);
        $datefiltervalue = $request->input('datefiltervalue');
        $fitterremovedataall = $request->input('morefilltersall');
        $dailySummary = $this->ddailySummaryAPI($datefiltervalue, $fitterremovedataall, $request->apistatus, 2);
        // echo "<pre>";print_r($dailySummary);exit;
        $val = explode('=', $fitterremovedataall);
        $city = !empty(trim($val[0], "'")) ? trim($val[1], "'") : 'Kerala - Palakkad';
        $finalResult = [];
        if ($request->apistatus == 'checkinreport') {

            // $city = trim($val[1], "'");
            $cityMapping = [
                'Chennai - Urapakkam' => 'Chennai',
                'Chennai - Sholinganallur' => 'Chennai',
                'Chennai - Madipakkam' => 'Chennai',
                'Chennai - Vadapalani' => 'Chennai',
                'Chennai - Tambaram' => 'Chennai',
                'Coimbatore - Sundarapuram' => 'Coimbatore',
                'Coimbatore - Thudiyalur' => 'Coimbatore',
                'Coimbatore - Ganapathy' => 'Coimbatore',
                'Kerala - Kozhikode' => 'kozhikode',
                'Varadhambalayam' => 'Erode',
                'Electronic City' => 'bangalore',
                'Konanakunte' => 'bangalore',
                'Dasarahalli' => 'bangalore',
                'Trichy' => 'Tiruchirappalli',
                'Tanjore' => 'Thanjavur',
                'Namakal' => 'NAMAKKAL',
                'Kerala - Palakkad' => 'Palakkad',
            ];
            if (isset($cityMapping[$city])) {
                $city = $cityMapping[$city];
            }
            if (is_array($dailySummary) && !empty($dailySummary)) {
                usort($dailySummary, function ($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });
                $filteredArray = array_filter($dailySummary, function ($entry) use ($city) {
                    return stripos($entry['city'], $city) === 0;
                });
                $finalResult = array_values($filteredArray);
                // echo "<pre>";print_r($finalResult);exit;
            }
        } elseif ($request->apistatus == 'regreport') {
            $flattened = [];
            foreach ($dailySummary as $regreport) {
                foreach ($regreport as $entry) {
                    $flattened[] = $entry;
                }
            }
            // echo "<pre>";print_r($flattened);exit;
            $areaMapping = [
                'AFHARU' => 'Harur',
                'AFMDU' => 'Madurai',
                'AFVPAL' => 'Vepanapalli',
                'AFCHENG' => 'Chengalpattu',
                'AFURP' => 'Urapakkam',
                'AFERD' => 'Erode',
                'AFKAL' => 'Kallakurichi',
                'AFNKL' => 'Nagapattinam',
                'AFSLM' => 'Salem',
                'AFHZR' => 'Hosur',
                'AFTRY' => 'Trichy',
                'AFTHR' => 'Thiruporur',
                'AFSPM' => 'Sivagangai',
                'AFVEL' => 'Vellore',
                'AFOMR' => 'Old Mahabalipuram Road',
                'AFCPK' => 'Coimbatore',
                'AFKONA' => 'Konavattam',
                'AFCBR' => 'Chidambaram',
                'AFTPR' => 'Tirupathur',
                'AFTPT' => 'Tirupathur',
                'AFTAM' => 'Tiruvannamalai',
                'AFSTY' => 'Sathyamangalam',
                'AFDAS' => 'Dindigul',
                'AFHBL' => 'Hebbal',
                'AFTAN' => 'Tirunelveli',
                'AFTPR' => 'Tirupathur',
                'AFTHI' => 'Thiruvannamalai',
                'AFPOL' => 'Pollachi',
                'AFMDU' => 'Madurai',
                'AFKAN' => 'Bangalore',
                'AFECT' => 'Echanari',
                'Aathur' => 'Aathur',
                'Coimbatore - Sundarapuram' => 'Coimbatore - Sundarapuram',
                'Coimbatore - Thudiyalur' => 'Coimbatore - Thudiyalur',
                'Kerala - Kozhikode' => 'Kerala - Kozhikode',
                'Karur' => 'Karur',
                'Tiruppur' => 'Tiruppur',
                'Kerala - Palakkad' => 'Kerala - Palakkad',
                'Pennagaram' => 'Pennagaram',
                'Tanjore' => 'Tanjore',
                'Villupuram' => 'Villupuram',
                'Thiruvallur' => 'Thiruvallur',
                'Chennai - HO - Guindy' => 'Chennai - HO - Guindy',
                'Chennai - Madipakkam' => 'Chennai - Madipakkam',
                'Chennai - Sholinganallur' => 'Chennai - Sholinganallur',
                'Chennai - Tambaram' => 'Chennai - Tambaram',
                'AFURP' => 'Chennai - Urapakkam',
                'Chennai - Vadapalani' => 'Chennai - Vadapalani',
                'Coimbatore - Ganapathy' => 'Coimbatore - Ganapathy',
            ];

            foreach ($flattened as &$item) {
                $prefix = explode('-', $item['phid'])[0];

                if (isset($areaMapping[$prefix])) {
                    $item['area'] = $areaMapping[$prefix];
                } else {
                    $item['area'] = 'Unknown';
                }
            }
            // echo "<pre>";print_r($city);exit;
            $area_fip = array_flip($areaMapping);
            if (isset($area_fip[$city])) {
                $search = $area_fip[$city];
                $results = [];
                foreach ($flattened as $entry) {
                    $phidPrefix = explode('-', $entry['phid'])[0];
                    if ($phidPrefix === $search) {
                        $results[] = $entry;
                    }
                }
                $finalResult = $results;
            } else {
                $finalResult =  $flattened;
            }
        } else {
            $totalOpIncome = 0;
            $totalIpIncome = 0;
            $totalPharmacyIncome = 0;
            usort($dailySummary, function ($a, $b) {
                return strtotime($a['billdate']) - strtotime($b['billdate']);
            });
            foreach ($dailySummary as $entr) {
                $totalOpIncome += $entr['O/P - Income'];
                $totalIpIncome += $entr['I/P - Income'];
                $totalPharmacyIncome += $entr['Pharmacy - Income'];
            }

            $finalResult = array_map(function ($entry) use ($totalOpIncome, $totalIpIncome, $totalPharmacyIncome) {
                return [
                    'name' => $entry['name'],
                    'billdate' => $entry['billdate'],
                    'opIncome' => round($entry['O/P - Income'], 2),
                    'ipIncome' => round($entry['I/P - Income'], 2),
                    'pharmacyIncome' => round($entry['Pharmacy - Income'], 2),
                    'totalOpIncome' => round($totalOpIncome, 2),
                    'totalIpIncome' => round($totalIpIncome, 2),
                    'totalPharmacyIncome' => round($totalPharmacyIncome, 2),
                    'total_amt' => round($entry['O/P - Income'] + $entry['I/P - Income'] + $entry['Pharmacy - Income'], 2)
                ];
            }, $dailySummary);
        }
        $result = empty($finalResult) ? [] : $finalResult;
        return response()->json($result);
    }

    public function dailyBranchFilter(Request $request)
    {
        set_time_limit(0);
        $datefiltervalue = $request->input('moredatefittervale');
        $fitterremovedataall = $request->input('morefilltersall');
        $dailySummary = $this->ddailySummaryAPI($datefiltervalue, $fitterremovedataall, $request->apistatus, 2);
        $val = explode('=', $fitterremovedataall);
        $city = trim($val[1], "'");
        $finalResult = [];

        if ($request->apistatus == 'checkinreport') {
            // echo "<pre>";print_r($dailySummary);exit;

            $cityMapping = [
                'Chennai - Urapakkam' => 'Chennai',
                'Chennai - Sholinganallur' => 'Chennai',
                'Chennai - Madipakkam' => 'Chennai',
                'Chennai - Vadapalani' => 'Chennai',
                'Chennai - Tambaram' => 'Chennai',
                'Coimbatore - Sundarapuram' => 'Coimbatore',
                'Coimbatore - Thudiyalur' => 'Coimbatore',
                'Coimbatore - Ganapathy' => 'Coimbatore',
                'Kerala - Kozhikode' => 'kozhikode',
                'Varadhambalayam' => 'Erode',
                'Electronic City' => 'bangalore',
                'Konanakunte' => 'bangalore',
                'Dasarahalli' => 'bangalore',
                'Trichy' => 'Tiruchirappalli',
                'Tanjore' => 'Thanjavur',
                'Namakal' => 'NAMAKKAL',
            ];
            if (isset($cityMapping[$city])) {
                $city = $cityMapping[$city];
            }
            if (is_array($dailySummary) && !empty($dailySummary)) {
                usort($dailySummary, function ($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });
                $filteredArray = array_filter($dailySummary, function ($entry) use ($city) {
                    return stripos($entry['city'], $city) === 0;
                });
                $finalResult = array_values($filteredArray);
            }
        } elseif ($request->apistatus == 'regreport') {
            $flattened = [];
            foreach ($dailySummary as $regreport) {
                foreach ($regreport as $entry) {
                    $flattened[] = $entry;
                }
            }
            // echo "<pre>";print_r($flattened);exit;
            $areaMapping = [
                'AFHARU' => 'Harur',
                'AFMDU' => 'Madurai',
                'AFVPAL' => 'Vepanapalli',
                'AFCHENG' => 'Chengalpattu',
                'AFURP' => 'Urapakkam',
                'AFERD' => 'Erode',
                'AFKAL' => 'Kallakurichi',
                'AFNKL' => 'Nagapattinam',
                'AFSLM' => 'Salem',
                'AFHZR' => 'Hosur',
                'AFTRY' => 'Trichy',
                'AFTHR' => 'Thiruporur',
                'AFSPM' => 'Sivagangai',
                'AFVEL' => 'Vellore',
                'AFOMR' => 'Old Mahabalipuram Road',
                'AFCPK' => 'Coimbatore',
                'AFKONA' => 'Konavattam',
                'AFCBR' => 'Chidambaram',
                'AFTPR' => 'Tirupathur',
                'AFTPT' => 'Tirupathur',
                'AFTAM' => 'Tiruvannamalai',
                'AFSTY' => 'Sathyamangalam',
                'AFDAS' => 'Dindigul',
                'AFHBL' => 'Hebbal',
                'AFTAN' => 'Tirunelveli',
                'AFTPR' => 'Tirupathur',
                'AFTHI' => 'Thiruvannamalai',
                'AFPOL' => 'Pollachi',
                'AFMDU' => 'Madurai',
                'AFKAN' => 'Bangalore',
                'AFECT' => 'Echanari',
                'Aathur' => 'Aathur',
                'Coimbatore - Sundarapuram' => 'Coimbatore - Sundarapuram',
                'Coimbatore - Thudiyalur' => 'Coimbatore - Thudiyalur',
                'Kerala - Kozhikode' => 'Kerala - Kozhikode',
                'Karur' => 'Karur',
                'Kerala - Palakkad' => 'Kerala - Palakkad',
                'Pennagaram' => 'Pennagaram',
                'Tanjore' => 'Tanjore',
                'Villupuram' => 'Villupuram',
                'Tiruppur' => 'Tiruppur',
                'Thiruvallur' => 'Thiruvallur',
                'Chennai - HO - Guindy' => 'Chennai - HO - Guindy',
                'Chennai - Madipakkam' => 'Chennai - Madipakkam',
                'Chennai - Sholinganallur' => 'Chennai - Sholinganallur',
                'Chennai - Tambaram' => 'Chennai - Tambaram',
                'AFURP' => 'Chennai - Urapakkam',
                'Chennai - Vadapalani' => 'Chennai - Vadapalani',
                'Coimbatore - Ganapathy' => 'Coimbatore - Ganapathy',
            ];

            foreach ($flattened as &$item) {
                $prefix = explode('-', $item['phid'])[0];

                if (isset($areaMapping[$prefix])) {
                    $item['area'] = $areaMapping[$prefix];
                } else {
                    $item['area'] = 'Unknown';
                }
            }

            $area_fip = array_flip($areaMapping);
            $search = $area_fip[$city];
            $results = [];
            foreach ($flattened as $entry) {
                $phidPrefix = explode('-', $entry['phid'])[0];
                if ($phidPrefix === $search) {
                    $results[] = $entry;
                }
            }

            $finalResult = $results;
        } else {
            $totalOpIncome = 0;
            $totalIpIncome = 0;
            $totalPharmacyIncome = 0;
            usort($dailySummary, function ($a, $b) {
                return strtotime($a['billdate']) - strtotime($b['billdate']);
            });
            // echo "<pre>";print_r($dailySummary);exit;
            foreach ($dailySummary as $entr) {
                $totalOpIncome += $entr['O/P - Income'];
                $totalIpIncome += $entr['I/P - Income'];
                $totalPharmacyIncome += $entr['Pharmacy - Income'];
            }

            // foreach ($dailySummary as $entry) {
            //     // Initialize the new entry
            //     $newEntry = [
            //         'name' => $entry['name'],
            //         'billdate' => $entry['billdate'],
            //         'opIncome' => round($entry['O/P - Income'],2),
            //         'ipIncome' => round($entry['I/P - Income'],2),
            //         'pharmacyIncome' => round($entry['Pharmacy - Income'],2),
            //         'totalOpIncome' => round($totalOpIncome,2),
            //         'totalIpIncome' => round($totalIpIncome,2),
            //         'totalPharmacyIncome' => round($totalPharmacyIncome,2),
            //         'total_amt' => round($entry['O/P - Income'] + $entry['I/P - Income'] + $entry['Pharmacy - Income'],2)
            //     ];

            //     $finalResult[] = $newEntry;
            // }

            $finalResult = array_map(function ($entry) use ($totalOpIncome, $totalIpIncome, $totalPharmacyIncome) {
                return [
                    'name' => $entry['name'],
                    'billdate' => $entry['billdate'],
                    'opIncome' => round($entry['O/P - Income'], 2),
                    'ipIncome' => round($entry['I/P - Income'], 2),
                    'pharmacyIncome' => round($entry['Pharmacy - Income'], 2),
                    'totalOpIncome' => round($totalOpIncome, 2),
                    'totalIpIncome' => round($totalIpIncome, 2),
                    'totalPharmacyIncome' => round($totalPharmacyIncome, 2),
                    'total_amt' => round($entry['O/P - Income'] + $entry['I/P - Income'] + $entry['Pharmacy - Income'], 2)
                ];
            }, $dailySummary);
        }
        $result = empty($finalResult) ? [] : $finalResult;
        return response()->json($result);
    }

    public function dailySummaryDetails(Request $request)
    {
        set_time_limit(0);
        $datefiltervalue = $request->input('moredatefittervale');
        $statusid = $request->input('statusid');
        $dailySummary = [];
        if ($statusid == 1 && !empty($request->apistatus)) {
            $dailySummary = $this->ddailySummaryAPI($datefiltervalue, $fitterremovedataall = null, $request->apistatus, $statusid);
        }
        // echo "<pre>";print_r($dailySummary);exit;
        $finalResult = [];

        if ($request->apistatus == 'checkinreport' && !empty($dailySummary)) {
            usort($dailySummary, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            $finalResult = $dailySummary;
        } elseif ($request->apistatus == 'regreport') {
            $flattened = [];
            // echo "<pre>";print_r($dailySummary);exit;
            foreach ($dailySummary as $regreport) {
                foreach ($regreport as $entry) {
                    $flattened[] = $entry;
                }
            }
            $areaMapping = [
                'AFHARU' => 'Harur',
                'AFMDP' => 'Madurai',
                'AFVPAL' => 'Vepanapalli',
                'AFCHENG' => 'Chengalpattu',
                'AFURP' => 'Urapakkam',
                'AFERD' => 'Erode',
                'AFKAL' => 'Kallakurichi',
                'AFNKL' => 'Nagapattinam',
                'AFSLM' => 'Salem',
                'AFHZR' => 'Hosur',
                'AFTRY' => 'Trichy',
                'AFTHR' => 'Thiruporur',
                'AFSPM' => 'Sivagangai',
                'AFVEL' => 'Vellore',
                'AFOMR' => 'Old Mahabalipuram Road',
                'AFCPK' => 'Coimbatore',
                'AFKONA' => 'Konavattam',
                'AFCBR' => 'Chidambaram',
                'AFTPR' => 'Tirupathur',
                'AFTPT' => 'Tirupathur',
                'AFTAM' => 'Tiruvannamalai',
                'AFSTY' => 'Sathyamangalam',
                'AFDAS' => 'Dindigul',
                'AFHBL' => 'Hebbal',
                'AFTAN' => 'Tirunelveli',
                'AFTPR' => 'Tirupathur',
                'AFTHI' => 'Thiruvannamalai',
                'AFPOL' => 'Pollachi',
                'AFMDU' => 'Madurai',
                'AFKAN' => 'Bangalore',
                'Tiruppur' => 'Tiruppur',
                'AFECT' => 'Echanari'
            ];

            foreach ($flattened as &$item) {
                $prefix = explode('-', $item['phid'])[0];
                if (isset($areaMapping[$prefix])) {
                    $item['area'] = $areaMapping[$prefix];
                } else {
                    $item['area'] = 'Unknown';
                }
            }
            $finalResult = $flattened;
        } else {
            $totalOpIncome = 0;
            $totalIpIncome = 0;
            $totalPharmacyIncome = 0;
            foreach ($dailySummary as $entr) {
                $totalOpIncome += $entr['O/P - Income'];
                $totalIpIncome += $entr['I/P - Income'];
                $totalPharmacyIncome += $entr['Pharmacy - Income'];
            }

            foreach ($dailySummary as $entry) {
                // Initialize the new entry
                $newEntry = [
                    'name' => $entry['name'],
                    'billdate' => $entry['billdate'],
                    'opIncome' => round($entry['O/P - Income'], 2),
                    'ipIncome' => round($entry['I/P - Income'], 2),
                    'pharmacyIncome' => round($entry['Pharmacy - Income'], 2),
                    'totalOpIncome' => round($totalOpIncome, 2),
                    'totalIpIncome' => round($totalIpIncome, 2),
                    'totalPharmacyIncome' => round($totalPharmacyIncome, 2),
                    'total_amt' => round($entry['O/P - Income'] + $entry['I/P - Income'] + $entry['Pharmacy - Income'], 2)
                ];

                $finalResult[] = $newEntry;
            }
        }
        $result = empty($finalResult) ? [] : $finalResult;
        return response()->json($result);
    }

    public function registrationView(Request $request)
    {
        $phid = explode('-', $request->phid)[0];
        $dailySummary = $this->ddailySummaryAPI($request->cdate, "", "regreportview", $phid);
        $dailySummary = $dailySummary[0];
        foreach ($dailySummary as &$item) {
            $item['city'] = $request->city;
        }
        return response()->json($dailySummary);
    }

    public function registrationFetch(Request $request){
            set_time_limit(0);
            $max_retries = 5;
            $regData = [];
            $apistatus = "registrationreport";
            $url = 'https://mocdoc.com/api/get/ptlist/draravinds-ivf';
            $dates = explode(' - ', $request->moredatefittervale);
            $startDate = $dates[0] ?? '';  // "29/12/2024"
            $endDate = $dates[1] ?? '';
            $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
            $start = Carbon::createFromFormat('Y-m-d', $startDateFormatted);
            $end = Carbon::createFromFormat('Y-m-d', $endDateFormatted);
            $dates = [];
            while ($start <= $end) {
                $dates[] = $start->format('Ymd');
                $start->modify('+1 day');
            }
             foreach ($dates as $date) {
                $data = $this->postCurlApi($url, $date, "", $max_retries,$apistatus,"");
                //   echo "<pre>--";print_r($date);    exit;
                foreach ($data['ptlist'] as $reg) {
                   $regData[] = $this->saveCurlData($reg, "",$apistatus,"Kanchipuram","");
                }
            }

                $areaMapping =   $this->cityCode();
                foreach ($regData as &$item) {
                    $prefix = explode('-', $item['phid'])[0];
                    if (isset($areaMapping[$prefix])) {
                        $item['area'] = $areaMapping[$prefix];
                    } else {
                        $item['area'] = 'Unknown';
                    }
                }
               $branch =  TblLocationModel::where('status',1)->orderBy('name', 'asc')->get();
             return response()->json(['data' => $regData,'dropdown' => $branch]);
    }

      public function registrationFetchBranch(Request $request){
          $branch = [];
          $zone =  TblZonesModel::where('name',$request->zone)->get();
        if(count($zone)>0){
          $branch =  TblLocationModel::where('zone_id',$zone[0]->id)->where('status',1)->orderBy('name', 'asc')->get();
        }else{
             $branch =  TblLocationModel::where('status',1)->orderBy('name', 'asc')->get();
        }
            return response()->json($branch);
    }

    private function cityCode(){
        $areaMapping = [
            'AFHARU' => 'Harur',
            'AFMDU' => 'Madurai',
            'AFVPAL' => 'Vepanapalli',
            'AFCHENG' => 'Chengalpattu',
            'AFERD' => 'Erode',
            'AFKAL' => 'Kallakurichi',
            'AFNKL' => 'Nagapattinam',
            'AFSLM' => 'Salem',
            'AFHZR' => 'Hosur',
            'AFTRY' => 'Trichy',
            'AFTHR' => 'Thiruporur',
            'AFSPM' => 'Sivagangai',
            'AFVEL' => 'Vellore',
            'AFOMR' => 'Old Mahabalipuram Road',
            'AFCPK' => 'Coimbatore - Ganapathy',
            'AFKONA' => 'Bengaluru - Konanakunte',
            'AFCBR' => 'Chidambaram',
            'AFTPR' => 'Tiruppur',
            'AFTPT' => 'Thirupathur',
            'AFTAM' => 'Thiruvannamalai',
            'AFSTY' => 'Sathyamangalam',
            'AFDAS' => 'Dindigul',
            'AFHBL' => 'Bengaluru - Hebbal',
            'AFTAN' => 'Tirunelveli',
            'AFTHI' => 'Tiruvannamalai',
            'AFATR' => 'Aathur',
            'AFPOL' => 'Pollachi',
            'AFMDP' => 'Mettupalayam',
            'AFKAN' => 'Bangalore',
            'AFECT' => 'Echanari',
            'Coimbatore - Sundarapuram' => 'Coimbatore - Sundarapuram',
            'Coimbatore - Thudiyalur' => 'Coimbatore - Thudiyalur',
            'Kerala - Kozhikode' => 'Kerala - Kozhikode',
            'Karur' => 'Karur',
            'Tiruppur' => 'Tiruppur',
            'Kerala - Palakkad' => 'Kerala - Palakkad',
            'Pennagaram' => 'Pennagaram',
            'Tanjore' => 'Tanjore',
            'Kanchipuram' => 'Kanchipuram',
            'Villupuram' => 'Villupuram',
            'Thiruvallur' => 'Thiruvallur',
            'Corporate Office - Guindy' => 'Corporate Office - Guindy',
            'Chennai - Madipakkam' => 'Chennai - Madipakkam',
            'Chennai - Sholinganallur' => 'Chennai - Sholinganallur',
            'Chennai - Tambaram' => 'Chennai - Tambaram',
            'AFURP' => 'Chennai - Urapakkam',
            'Chennai - Vadapalani' => 'Chennai - Vadapalani',
          ];

          return  $areaMapping;
    }

    public function ddailySummaryAPI($date, $dt, $apistatus, $statusid)
    {
        if ($apistatus == 'checkinreport') {
            $url = 'https://mocdoc.in/api/checkedin/draravinds-ivf';
        } elseif ($apistatus == 'regreport' || $apistatus == 'regreportview') {
            $url = 'https://mocdoc.com/api/get/ptlist/draravinds-ivf';
        } else {
            $url = 'https://mocdoc.in/api/get/billlist/draravinds-ivf';
        }
        $max_retries = 5;
        // $locations = TblBranch::select('location_id')->get(); // Fetch locations dynamically
        if ($apistatus != 'regreportview') {
            $dates = explode(' - ', $date);
            $startDate = $dates[0] ?? '';  // "29/12/2024"
            $endDate = $dates[1] ?? '';
            $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
            // $curr_date=$startDateFormatted;
            $start = Carbon::createFromFormat('Y-m-d', $startDateFormatted);
            $end = Carbon::createFromFormat('Y-m-d', $endDateFormatted);
            //echo $date = Carbon::parse($start);exit;
            $start_date = $start->format('Ymd') . '00:00:00';
            $end_date = $end->format('Ymd') . '23:59:59';
            $dates = [];
            while ($start <= $end) {
                $dates[] = $start->format('Ymd');
                $start->modify('+1 day');
            }


            $val = explode('=', $dt);
            //echo "<pre>";print_r($val);exit;
            $value = !empty(trim($val[0], "'")) ? trim($val[1], "'") : 'Kerala - Palakkad';

            $locations = array("location1" => "Kerala - Palakkad", "location7" => "Erode", "location14" => "Tiruppur", "location6" => "Kerala - Kozhikode", "location17" => "TIRUPPUR IP", "location18" => "KUMALAN KUTTAI - ERODE", "location20" => "Coimbatore - Ganapathy", "location21" => "Hosur", "location22" => "Chennai - Sholinganallur", "location23" => "Chennai - Urapakkam", "location24" => "Chennai - Madipakkam", "location25" => "Agraharam - SALEM", "location26" => "Kanchipuram", "location27" => "Coimbatore - Sundarapuram", "location28" => "Trichy", "location29" => "Thiruvallur", "location30" => "Pollachi", "location31" => "Electronic City", "location32" => "Konappana - Electronic City", "location33" => "Chennai - Tambaram", "location34" => "Tanjore", "location35" => "Konanakunte", "location36" => "Harur", "location37" => "COIMBATORE", "location38" => "Varadhambalayam", "location39" => "Coimbatore - Thudiyalur", "location40" => "Madurai", "location41" => "HEBBAL", "location42" => "Kallakurichi", "location43" => "Vellore", "location44" => "Tirupati", "location45" => "Aathur", "location46" => "Namakal", "location47" => "Dasarahalli", "location48" => "Chengalpattu", "location49" => "Chennai - Vadapalani", "location50" => "Pennagaram", "location51" => "Thirupathur", "location52" => "Sivakasi", "location13" => "Salem");
            $location = array_search($value, $locations);
        }
        $billingData = [];
        $billdate = null;
        if ($apistatus == 'checkinreport' && $statusid != 1) {
            $data = $this->getBillingData($url, $start_date, $location, $max_retries, $apistatus, $end_date);
            //echo "<pre>";print_r($data);exit;
            if (!empty($data['checkinlist'])) {
                foreach ($data['checkinlist'] as $billing) {
                    //echo "<pre>";print_r($billing['patient']);
                    $billingData[] = $this->prepareBillingData($billing, $location, $apistatus);
                }
                return $billingData;
                // echo "<pre>";print_r($billingData);exit;
            }
        } else if ($apistatus == 'checkinreport' && $statusid == 1) {
            //$locatio=array_flip($locations);
            // echo "<pre>";print_r($locations);exit;
            // foreach ($locatio as $loc) {
            $data = $this->getBillingData($url, $start_date, 'location1', $max_retries, $apistatus, $end_date);
            // echo "<pre>";print_r($data);exit;
            if (!empty($data['checkinlist'])) {
                foreach ($data['checkinlist'] as $billing) {
                    $billingData[] = $this->prepareBillingData($billing, $location, $apistatus);
                }
            }
            // }
            return $billingData;
        } else if ($apistatus == 'regreportview') {
            $data = $this->getBillingData($url, $date, "", $max_retries, $apistatus, "");
            if (!empty($data['ptlist'])) {
                $billingData[] = $this->prepareBillingData($data['ptlist'], $statusid, $apistatus);
            }
            return $billingData;
        } else if ($apistatus == 'regreport') {
            foreach ($dates as $dat) {
                $data = $this->getBillingData($url, $dat, $location, $max_retries, $apistatus, $end_date);
                if (!empty($data['ptlist'])) {
                    $billingData[] = $this->prepareBillingData($data['ptlist'], $location, $apistatus);
                }
            }
            // echo "<pre>";print_r($billingData);                exit;
            return $billingData;
        } else {
            foreach ($dates as $dat) {
                $data = $this->getBillingData($url, $dat, $location, $max_retries, $apistatus, "");
                if (!empty($data['billinglist'])) {
                    foreach ($data['billinglist'] as $billing) {
                        $billingData[] = $this->prepareBillingData($billing, $location, $apistatus);
                    }
                }
            }
            // echo "<pre>";print_r($billingData);                exit;
            $typeTotals = [];
            foreach ($billingData as $entry) {
                $billdate = substr($entry['billdate'], 0, 10);
                if (!empty($billdate)) {
                    if (!isset($typeTotals[$billdate])) {
                        $typeTotals[$billdate] = [
                            'name' => $value,
                            'billdate' => $billdate,
                            'O/P - Income' => 0.00,
                            'I/P - Income' => 0.00,
                            'Pharmacy - Income' => 0.00
                        ];
                    }
                    if ($entry['type'] === "I/P - Income" && $entry['amt'] == 0) {
                        $typeTotals[$billdate]["I/P - Income"] += $entry['advances_amt'];
                    } else {
                        $typeTotals[$billdate][$entry['type']] += $entry['amt'];
                    }
                }
            }
            $result = array_values($typeTotals);

            return $result;
        }
        //echo "<pre>";print_r($billingData);  exit;

    }

    // Function to handle cURL and retries
    private function getBillingData($url, $curr_date, $location_id, $max_retries, $apistatus, $end_date)
    {
        $retry_count = 0;
        $delay = 2;
        //echo $curr_date->format('Y-m-d H:i:s');exit;
        if ($apistatus == 'checkinreport') {
            $post_fields = "startdate={$curr_date}&enddate={$end_date}&entitylocation={$location_id}";
            $head_fields = [
                'md-authorization: MD 7b40af0edaf0ad75:jR1+YyQZVWCIIaXlgxt1z8uixQ4=',
                'Date: Mon, 31 Mar 2025 08:05:38 GMT',
                'Content-Type: application/x-www-form-urlencoded'
            ];
        } elseif ($apistatus == 'regreport' || $apistatus == 'regreportview') {
            $dte = substr($curr_date, 0, 8);
            $post_fields = "registrationdate={$dte}";
            // echo $post_fields;exit;
            $head_fields = [
                'md-authorization: MD 7b40af0edaf0ad75:zzJIrJPzgSOMhucj/1bXawbz+GI=',
                'Date: Fri, 11 Apr 2025 06:18:59 GMT',
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: SRV=s1; vid3=CvAABmf4wWdOP+VJBV+AAg=='
            ];
        } else {
            $post_fields = "date={$curr_date}&entitylocation={$location_id}";
            $head_fields = [
                'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
                'Date: Fri, 07 Mar 2025 10:07:52 GMT',
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: SRV=s1'
            ];
        }
        while ($retry_count < $max_retries) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $post_fields,
                CURLOPT_HTTPHEADER => $head_fields,
            ));

            $response = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if (curl_errno($curl)) {
                echo "cURL Error: " . curl_error($curl);
                break;
            }
            //  echo "<pre>";print_r($response);exit;
            if ($http_status == 200) {
                curl_close($curl);
                return json_decode($response, true);
            }

            if ($http_status == 429) {
                sleep($delay);
                $retry_count++;
                $delay *= 2;
            } else {
                curl_close($curl);
                break;
            }
        }

        return []; // Return empty data if failed to fetch
    }

    private function prepareBillingData($billing, $location_id, $apistatus)
    {
        if ($apistatus == 'checkinreport') {
            $cdate = !empty($billing['date'])
                ? Carbon::createFromFormat('Ymd', $billing['date'])->format('Y-m-d')
                : null;
            $dob = !empty($billing['patient']['dob'])
                ? Carbon::createFromFormat('Ymd', $billing['patient']['dob'])->format('Y-m-d')
                : null;
            return [
                'date' => $cdate . " " . $billing['start'],
                'purpose' => $billing['purpose'] ?? '',
                'name' => $billing['patient']['name'] ?? '',
                'mobile' => $billing['patient']['mobile'] ?? '',
                'dob' => $dob,
                'ptsource' => $billing['patient']['ptsource'] ?? '',
                'city' => $billing['patient']['address']['city'] ?? '',
            ];
        } elseif ($apistatus == 'regreport') {
            $seenPhids = [];
            $groupedByPrefix = [];
            foreach ($billing as $item) {
                if (!isset($item['phid']) || empty($item['phid'])) continue;

                $phid = $item['phid'];

                if (in_array($phid, $seenPhids)) continue;
                $seenPhids[] = $phid;

                $parts = explode('-', $phid);
                $prefix = $parts[0];

                if (isset($groupedByPrefix[$prefix])) {
                    $groupedByPrefix[$prefix]['count'] += 1;
                } else {
                    $groupedByPrefix[$prefix] = [
                        'created_at' => $item['created_at'] ?? '',
                        'phid' => $phid,
                        'count' => 1
                    ];
                }
            }

            $result = array_values($groupedByPrefix);
            return $result;
        } elseif ($apistatus == 'regreportview') {
            $afomrItems = [];
            foreach ($billing as $item) {
                if (isset($item['phid']) && strpos($item['phid'], $location_id) === 0) {
                    $afomrItems[] = $item;
                }
            }
            return $afomrItems;
            // echo "<pre>";print_r($afomrItems);exit;
        } else {
            $billdates = !empty($billing['billdate'])
                ? Carbon::createFromFormat('YmdH:i:s', $billing['billdate'])->format('Y-m-d H:i:s')
                : null;

            // Handle I/P Income and advances
            if ($billing['type'] === 'I/P - Income' && $billing['amt'] == 0) {
                if (!empty($billing['advances']) && !empty($billing['advances'][0]['amt'])) {
                    $billing['advances_amt'] = $billing['advances'][0]['amt'];
                }
            }

            // Prepare data array for insertion
            return [
                'type' => $billing['type'] ?? '',
                'branch' => $location_id,
                'amt' => $billing['amt'] ?? 0,
                'billdate' => $billdates,
                'advances_amt' => $billing['advances_amt'] ?? 0,
            ];
        }
    }


    // keerthika code

    public function admin_fetchdocument()
    {
        $documents = DB::table('hms_document_manage')
            ->join('tbl_locations', 'hms_document_manage.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_document_manage.*')
            ->where('hms_document_manage.document_id', '=', 3)
            ->get();
        return response()->json($documents);
    }

    public function security_fetchdocument()
    {
        $query = securitydetailsModel::select('tbl_locations.name', 'tblzones.name as zone_name', 'security_details.*')
            ->join('tbl_locations', 'security_details.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->where('security_details.status', '=', 1)
            ->get();
        return response()->json($query);
    }


    public function attendance_fetchdocument()
    {
        $documents = DB::table('hms_document_manage')
            ->join('tbl_locations', 'hms_document_manage.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_document_manage.*')
            ->where('hms_document_manage.document_id', '=', 5)
            ->get();
        return response()->json($documents);
    }
    public function discount_fetchdocument()
    {
        $documents = DB::table('hms_document_manage')
            ->join('tbl_locations', 'hms_document_manage.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_document_manage.*')
            ->where('hms_document_manage.document_id', '=', 6)
            ->get();
        return response()->json($documents);
    }
    public function general_fetchdocument()
    {
        $documents = DB::table('hms_document_manage')
            ->join('tbl_locations', 'hms_document_manage.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_document_manage.*')
            ->where('hms_document_manage.document_id', '=', 7)
            ->get();
        return response()->json($documents);
    }
    public function admindaily_documentadded(Request $request)
    {
        $validatedData = $request->validate([
            'zone_id' => 'required|string|max:255',
            'document_name' => 'required|string|max:255',
            'expire_date' => 'required|string|max:255',
            'images.*' => 'required|file|mimes:pdf|max:12048', // Validate images
        ]);
        $imagePaths = [];
        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('document_data'); // Path to public/uploads/doctor_images
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $imagePaths[] = 'document_data/' . $filename; // Save relative path
            }
        }
        $doctor = documentdetails::create(array_merge($validatedData, [
            'zone_id' => $request->zone_id,
            'document_type' => json_encode($imagePaths),
            'document_id' => 3
        ]));
        return response()->json(['success' => true, 'message' => 'Document saved successfully!']);
    }
    public function security_documentadded(Request $request)
    {
        $validatedData = $request->validate([
            'zone_id' => 'required|string|max:255',
            'sec_name' => 'required|string|max:255',
            'sec_address' => 'required|string|max:255',
            'sec_shift' => 'required|string|max:255',
            'sec_joining_date' => 'required|string|max:255',
            'images.*' => 'required|file|mimes:pdf|max:12048',
        ]);
        $sec_phone = $request->input('sec_phone');
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                $destinationPath = public_path('document_data'); // Path to public/uploads/doctor_images
                $image->move($destinationPath, $filename); // Move file to the destination folder
                $imagePaths[] = 'document_data/' . $filename; // Save relative path
            }
        }
        $doctor = securitydetailsModel::create(array_merge($validatedData, [
            'zone_id' => $request->zone_id,
            'sec_id_proof' => json_encode($imagePaths),
            'sec_phone' => $sec_phone,
            'status' => 1
        ]));
        return response()->json(['success' => true, 'message' => 'Security saved successfully!']);
    }
   public function attendance_documentadd(Request $request)
{
    $username = auth()->user()->user_fullname;
    $validatedData = $request->validate([
                'zone_id' => 'required|string|max:255',
                'att_from_date' => 'required|string|max:255',
                'att_to_date' => 'required|string|max:255',
                'images.*' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:12048', // Validate images
        ]);
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $destinationPath = public_path('attendance_data');
                $image->move($destinationPath, $filename);
                $imagePaths[] = 'attendance_data/' . $filename;
            }
        }
        $doctor = attendancedetailsModel::create(array_merge($validatedData, [
            'zone_id' => $request->zone_id,
            'att_document' => json_encode($imagePaths),
            'created_by' => $username,
            'att_status' => 1
        ]));
        return response()->json(['success' => true, 'message' => 'Attendance Document saved successfully!']);
}
//     public function discount_documentadded(Request $request)
//     {
//         $admin = auth()->user();
//     // Validate request
//     $validatedData = $request->validate([
//         'dis_zone_id' => 'required|string|max:255',
//         'dis_wife_name' => 'required|string|max:255',
//         'dis_wife_mrd_no' => 'required|string|max:255',
//         'dis_husband_name' =>'required|string|max:255',
//         'dis_husband_mrd_no'=> 'required|string|max:255',
//         'dis_service_name'=> 'required|string|max:255',
//         'dis_total_bill'=> 'required|string|max:255',
//         'dis_expected_request'=>'required|string|max:255',
//         'dis_post_discount' => 'required|string|max:255',
//         'dis_patient_ph' => 'required|string|max:255',
//         'dis_counselled_by' => 'required|string|max:255',
//         'dis_final_auth' => 'required|string|max:255',
//         'dis_branch_no' => 'required|string|max:255',
//         'dis_auth_by' => 'required|string|max:255',
//         'dis_wife_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
//         'dis_husband_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
//         'dis_drsign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
//         'dis_cc_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
//         'dis_admin_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
//         'dis_form_status' => 'required|string|max:255',

//     ]);
//     // dd($validatedData);
//     $imagePaths = [];
//     $signatureFields = ['dis_wife_sign', 'dis_husband_sign', 'dis_drsign', 'dis_cc_sign', 'dis_admin_sign'];

//     foreach ($signatureFields as $field) {
//         if ($request->hasFile($field)) {
//             $image = $request->file($field);
//             $filename = time() . '_' . $image->getClientOriginalName();
//             $destinationPath = public_path('discount_form');
//             $image->move($destinationPath, $filename);
//             $imagePaths[$field] = json_encode(['discount_form/' . $filename]);
//         } elseif ($request->has($field . '_base64')) {
//             $imageData = $request->input($field . '_base64');
//             $filename = time() . '_' . $field . '.png';
//             $destinationPath = public_path('discount_form');
//             file_put_contents($destinationPath . '/' . $filename, base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData)));
//             $imagePaths[$field] = json_encode(['discount_form/' . $filename]);
//         } else {
//             $imagePaths[$field] = json_encode([]); // empty array if nothing uploaded
//         }
//     }
//     DiscountFormModel::create(array_merge($validatedData, [
//         'dis_sno' => $request->dis_sno,
//         'dis_zone_id' => $request->dis_zone_id,
//         'dis_s_no' => $request->dis_s_no,
//         'dis_post_discount' =>$request->dis_post_discount,
//         'dis_patient_ph' => $request->dis_patient_ph,
//         'dis_wife_sign' => $imagePaths['dis_wife_sign'],
//         'dis_husband_sign' => $imagePaths['dis_husband_sign'],
//         'dis_drsign' => $imagePaths['dis_drsign'],
//         'dis_cc_sign' => $imagePaths['dis_cc_sign'],
//         'dis_admin_sign' => $imagePaths['dis_admin_sign'],
//         'created_by' => $admin->id,
//         'status' => 1
//     ]));

//     return response()->json(['success' => true, 'message' => 'Discountform saved successfully!']);
// }
public function discount_documentadded(Request $request)
{
    $admin = auth()->user();

    $validatedData = $request->validate([
        'dis_zone_id' => 'required|string|max:255',
        'dis_wife_name' => 'required|string|max:255',
        'dis_wife_mrd_no' => 'required|string|max:255',
        'dis_husband_name' => 'required|string|max:255',
        'dis_husband_mrd_no' => 'required|string|max:255',
        'dis_service_name' => 'required|string|max:255',
        'dis_total_bill' => 'required|string|max:255',
        'dis_expected_request' => 'required|string|max:255',
        'dis_post_discount' => 'required|string|max:255',
        'dis_patient_ph' => 'required|string|max:255',
        'dis_counselled_by' => 'nullable|string|max:500',
        'dis_final_auth' => 'nullable|string|max:255',
        'dis_branch_no' => 'nullable|string|max:255',
        'dis_auth_by' => 'nullable|string|max:255',
        'dis_approved_by' => 'nullable|string|max:255',
        'dis_form_status' => 'required|string|max:255',

        'dis_wife_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'dis_husband_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'dis_drsign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'dis_cc_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'dis_admin_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
    ]);

    // Mandatory by access: branch_no and auth_by for access_limits 2; approved_by for 1
    if ($admin->access_limits == 2) {
        if (empty($request->dis_branch_no) || empty($request->dis_auth_by)) {
            return response()->json(['success' => false, 'message' => 'B.R. No. and Authorised By are required for your role.']);
        }
    }
    if ($admin->access_limits == 1) {
        if (empty($request->dis_approved_by)) {
            return response()->json(['success' => false, 'message' => 'Final Approved By is required.']);
        }
    }

    // Duplication: same month + same package (service_name) + same couple = do not save
    $wifeMrd = $request->dis_wife_mrd_no;
    $husMrd = $request->dis_husband_mrd_no;
    $package = $request->dis_service_name;
    $startOfMonth = \Carbon\Carbon::now()->startOfMonth()->toDateTimeString();
    $endOfMonth = \Carbon\Carbon::now()->endOfMonth()->toDateTimeString();
    $exists = DiscountFormModel::where('dis_wife_mrd_no', $wifeMrd)
        ->where('dis_husband_mrd_no', $husMrd)
        ->where('dis_service_name', $package)
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->exists();
    if ($exists) {
        return response()->json([
            'success' => false,
            'message' => 'A discount form for this couple with the same package already exists in the current month. Cannot save duplicate.'
        ]);
    }

    // ---------- SIGNATURE HANDLING ----------
    $imagePaths = [];
    $signatureFields = [
        'dis_wife_sign',
        'dis_husband_sign',
        'dis_drsign',
        'dis_cc_sign',
        'dis_admin_sign'
    ];

    foreach ($signatureFields as $field) {
        if ($request->hasFile($field)) {
            $image = $request->file($field);
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('discount_form'), $filename);
            $imagePaths[$field] = json_encode(['discount_form/' . $filename]);
        } elseif ($request->filled($field . '_base64')) {
            $filename = time() . '_' . $field . '.png';
            file_put_contents(
                public_path('discount_form/' . $filename),
                base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $request->input($field . '_base64')))
            );
            $imagePaths[$field] = json_encode(['discount_form/' . $filename]);
        } else {
            $imagePaths[$field] = json_encode([]);
        }
    }

    // ---------- ATTACHMENTS ----------
    $attachmentPaths = [];
    if ($request->hasFile('discount_attachments')) {
        foreach ($request->file('discount_attachments') as $file) {
            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('discount_form'), $filename);
            $attachmentPaths[] = 'discount_form/' . $filename;
        }
    }

    // ---------- COUNSELLED BY (main + include/not include) ----------
    $mainCounselled = $request->filled('dis_counselled_by') ? trim($request->dis_counselled_by) : null;
    $includeVal = $request->filled('dis_counselled_by_include') ? $request->dis_counselled_by_include : null;
    $notIncludeVal = $request->filled('dis_counselled_by_not_include') ? $request->dis_counselled_by_not_include : null;
    $disCounselledBy = trim(implode(' / ', array_filter([$mainCounselled, $includeVal, $notIncludeVal])));

    // ---------- SAVE ----------
    $model = new DiscountFormModel();
    $model->fill($validatedData);
    $model->dis_counselled_by = $disCounselledBy ?: null;
    $model->dis_counselled_by_include = $includeVal ? json_encode([$includeVal]) : null;
    $model->dis_counselled_by_not_include = $notIncludeVal ? json_encode([$notIncludeVal]) : null;
    $model->dis_wife_sign    = $imagePaths['dis_wife_sign'];
    $model->dis_husband_sign = $imagePaths['dis_husband_sign'];
    $model->dis_drsign       = $imagePaths['dis_drsign'];
    $model->dis_cc_sign      = $imagePaths['dis_cc_sign'];
    $model->dis_admin_sign   = $imagePaths['dis_admin_sign'];
    $model->dis_attachments  = count($attachmentPaths) ? json_encode($attachmentPaths) : null;
    $model->created_by = $admin->id;
    $model->status = 1;
    $model->save();

    return response()->json([
        'success' => true,
        'message' => 'Discount form saved successfully!'
    ]);
}

public function discountform_detials(Request $request)
{
    $admin = auth()->user();
    // dd($request);
    set_time_limit(0);

    $fitterremovedataall = $request->input('morefilltersall');
    $datefiltervalue = $request->input('moredatefittervale');
    $mrdno = $request->input('mrodnofilter');

    // Handle date range
    $dates = explode(' - ', $datefiltervalue);
    $startDate = $dates[0] ?? '';
    $endDate = $dates[1] ?? '';
    $startDateFormatted = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    $endDateFormatted = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
    $start_date = Carbon::parse($startDateFormatted)->format('Ymd') . '00:00:00';
    $end_date = Carbon::parse($endDateFormatted)->format('Ymd') . '23:59:59';

    $moclocations = $this->cityArray();
    $user_location = $this->getAccessibleLocations($admin);
    $locations = [];

    foreach ($user_location as $dbId => $dbName) {
        foreach ($moclocations as $mocKey => $mocName) {
            if (trim($mocName) === trim($dbName)) {
                $locations[$mocKey] = $mocName;
            }
        }
    }
    // dd($locations);
    $checkinData = [];

    // Extract quoted values from the filter string
    preg_match_all("/'([^']+)'/", $fitterremovedataall, $matches);
    $values = $matches[1];
    // Parse presence of filters
    $hasZone = strpos($fitterremovedataall, 'tblzones.name') !== false;
    $hasLocation = strpos($fitterremovedataall, 'tbl_locations.name') !== false;
    $hasPhid = strpos($fitterremovedataall, 'phid') !== false;
    // dd($hasZone , $hasLocation , $hasPhid);
    // Case 1: Zone + Location + PHID
    if ($hasZone && $hasLocation && $hasPhid) {
        $zoneName = $values[0];
        $locationName = $values[1];

        $zone = TblZonesModel::where('name', $zoneName)->first();
        $location = TblLocationModel::where('name', $locationName)->where('zone_id', $zone->id)->first();

        if ($location) {
            $locationKey = array_search($location->name, $locations);
            $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

            foreach ($apiData['checkinlist'] ?? [] as $checkin) {
                if ($checkin['patient']['phid'] == $mrdno) {
                    $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $locationName, $zoneName, $location->id);
                }
            }
        }


    } // Case: Zone + Location (without PHID)
  elseif ($hasZone && $hasLocation && !$hasPhid) {
    $zoneName = $values[0];
    $locationName = $values[1];

    $zone = TblZonesModel::where('name', $zoneName)->first();
    $location = TblLocationModel::where('name', $locationName)->where('zone_id', $zone->id)->first();

    if ($location) {
        $locationKey = array_search($location->name, $locations);
        $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

        foreach ($apiData['checkinlist'] ?? [] as $checkin) {
            $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $locationName, $zoneName, $location->id);
        }
       }
        // Case 2: Location + PHID
   }elseif ($hasLocation && $hasPhid && !$hasZone) {
        $locationName = $values[0];

        $location = TblLocationModel::where('name', $locationName)->first();
        $zone = TblZonesModel::find($location->zone_id);
        $zoneName = $zone->name ?? '';
        $locationKey = array_search($locationName, $locations);

        $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

        foreach ($apiData['checkinlist'] ?? [] as $checkin) {
            if ($checkin['patient']['phid'] == $mrdno) {
                $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $locationName, $zoneName, $location->id);
            }
        }

    // Case 3: Zone + PHID
    } elseif ($hasZone && $hasPhid && !$hasLocation) {
        $zoneName = $values[0];
        $zoneLocations = $this->selectQuerylocation($zoneName);

        foreach ($zoneLocations as $zoneLocation) {
            $locationKey = array_search($zoneLocation->name, $locations);
            $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

            foreach ($apiData['checkinlist'] ?? [] as $checkin) {
                if ($checkin['patient']['phid'] == $mrdno) {
                    $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $zoneLocation->name, $zoneName,  $zoneLocation->id);
                }
            }
        }

    // Case 4: Only Location
    } elseif ($hasLocation && !$hasZone && !$hasPhid) {
        $locationName = $values[0];

        $location = TblLocationModel::where('name', $locationName)->first();
        $zone = TblZonesModel::find($location->zone_id);
        $zoneName = $zone->name ?? '';
        $locationKey = array_search($locationName, $locations);

        $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);
        foreach ($apiData['checkinlist'] ?? [] as $checkin) {
            $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $locationName, $zoneName,$location->id);
        }

    // Case 5: Only Zone
    } elseif ($hasZone && !$hasLocation && !$hasPhid) {
        $zoneName = $values[0];
        $zoneLocations = $this->selectQuerylocation($zoneName);
        foreach ($zoneLocations as $zoneLocation) {
            $locationKey = array_search($zoneLocation->name, $locations);
            $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

            foreach ($apiData['checkinlist'] ?? [] as $checkin) {
                $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $zoneLocation->name, $zoneName,  $zoneLocation->id);

            }
        }

    // Case 6: Only PHID
    } elseif ($hasPhid && !$hasZone && !$hasLocation) {
        foreach ($locations as $key => $locationName) {
            $location = TblLocationModel::where('name', $locationName)->first();
            $zone = TblZonesModel::find($location->zone_id);
            $zoneName = $zone->name ?? '';

            $apiData = $this->mrdnoapi($start_date, $key, 5, $end_date);

            foreach ($apiData['checkinlist'] ?? [] as $checkin) {
                if ($checkin['patient']['phid'] == $mrdno) {
                    $checkinData[] = $this->saveCurlDatas($checkin, $key, $locationName, $zoneName, $location->id);
                }
            }
        }

    // Case 7: No filter — use default location
    }else {
        $admin = auth()->user();
        $location = $this->getDefaultLocationByAccess($admin);
        if (!$location) {
            return;
        }
        $zone = TblZonesModel::find($location->zone_id);
        $zoneName = $zone->name ?? '';
        $locationKey = array_search($location->name, $locations, true);
        if (!$locationKey) {
            return; // location not mapped in mocdoc
        }
        $apiData = $this->mrdnoapi($start_date,$locationKey,5,$end_date);
        foreach ($apiData['checkinlist'] ?? [] as $checkin) {
            $checkinData[] = $this->saveCurlDatas(
                $checkin,
                $locationKey,
                $location->name,
                $zoneName,
                $location->id
            );
        }
    }
    // else {
    //     $defaultLocation = 'Chengalpattu';
    //     $locationKey = array_search($defaultLocation, $locations);
    //     $location = TblLocationModel::where('name', $defaultLocation)->first();
    //     $zone = TblZonesModel::find($location->zone_id);
    //     $zoneName = $zone->name ?? '';

    //     $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

    //     foreach ($apiData['checkinlist'] ?? [] as $checkin) {
    //         $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $defaultLocation, $zoneName, $location->id);
    //     }
    // }

     // Build the base query
        $query = DiscountFormModel::select(
            'tbl_locations.name as location_name',
            'tblzones.name as zone_name',
            'hms_discount_form.*'
        )
        ->join('tbl_locations', 'hms_discount_form.dis_zone_id', '=', 'tbl_locations.id')
        ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->whereDate('hms_discount_form.created_at', '>=', $startDateFormatted)
        ->whereDate('hms_discount_form.created_at', '<=', $endDateFormatted);

        // Apply filters BEFORE executing the query
        if ($hasZone && isset($values[0])) {
            $zoneName = $values[0];
            $query->where('tblzones.name', $zoneName);
        }

        if ($hasLocation && isset($values[1])) {
            $locationName = $values[1];
            $query->where('tbl_locations.name', $locationName);
        } elseif ($hasLocation && isset($values[0]) && !$hasZone) {
            $locationName = $values[0];
            $query->where('tbl_locations.name', $locationName);

            $location = TblLocationModel::where('name', $locationName)->first();
            if ($location) {
                $zone = TblZonesModel::find($location->zone_id);
                if ($zone) {
                    $zoneName = $zone->name;
                    $query->where('tblzones.name', $zoneName);
                }
            }
        } elseif (!$hasZone && !$hasLocation) {
            // No zone/location filters applied — use default location
            $defaultLocation = 'Chennai - Sholinganallur';
            $location = TblLocationModel::where('name', $defaultLocation)->first();
            if ($location) {
                $zone = TblZonesModel::find($location->zone_id);
                $zoneName = $zone->name ?? '';
                $query->where('tbl_locations.name', $defaultLocation);
                $query->where('tblzones.name', $zoneName);
            }
        }


        if ($mrdno) {
            $query->where(function ($q) use ($mrdno) {
                $q->where('dis_wife_mrd_no', $mrdno)
                ->Where('dis_husband_mrd_no', $mrdno);
            });
        }

        $existingData = $query->get()->toArray();

        $mergedData = [];

        // Step 1: Build a lookup of all mrd numbers from DB
        $mrdLookup = [];
        // dd($existingData);
        foreach ($existingData as $record) {

            $record['is_saved'] = true;
            $mergedData[] = $record;

            // Use both MRD numbers for matching
            if (!empty($record['dis_wife_mrd_no'])) {
                $mrdLookup[$record['dis_wife_mrd_no']] = true;
            }
            if (!empty($record['dis_husband_mrd_no'])) {
                $mrdLookup[$record['dis_husband_mrd_no']] = true;
            }
        }

        // Step 2: Add API data only if PHID not matched with any DB MRD
        foreach ($checkinData as $apiEntry) {
            $phid = $apiEntry['phid'] ?? null;

            // Add only if PHID is NOT in any of the DB MRD fields
            if ($phid && !isset($mrdLookup[$phid])) {
                $apiEntry['is_saved'] = false;
                $mergedData[] = $apiEntry;
            }
        }

// Return the merged response
return response()->json([
    'checkinData' => array_values($mergedData),
]);
}
private function getDefaultLocationByAccess($admin)
{
    if ($admin->access_limits == 1) {
        // Super admin: first zone → first branch
        $zone = TblZonesModel::orderBy('id')->first();
        return $zone
            ? TblLocationModel::where('zone_id', $zone->id)->orderBy('id')->first()
            : null;
    }

    if ($admin->access_limits == 2) {
        // Zone admin: admin zone → first branch
        return TblLocationModel::where('zone_id', $admin->zone_id)
            ->orderBy('id')
            ->first();
    }

    // Branch user
    return TblLocationModel::find($admin->branch_id);
}


private function selectQuerylocation($city)
{
    $zone = TblZonesModel::select('id')->where('name', $city)->first();
    if (!$zone) {
        return collect(); // Return an empty Laravel collection instead of []
    }
    return TblLocationModel::where('zone_id', $zone->id)
                           ->where('status', 1)
                           ->orderBy('name', 'asc')
                           ->get(); // This is a collection
}

public function mrdnoapi($curr_date, $location_id, $max_retries, $end_date){

 $retry = 0;
            $backoff = 1;

            do {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://mocdoc.in/api/checkedin/draravinds-ivf',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => "startdate={$curr_date}&enddate={$end_date}&entitylocation={$location_id}",
                    CURLOPT_HTTPHEADER =>  array('md-authorization: MD 7b40af0edaf0ad75:jR1+YyQZVWCIIaXlgxt1z8uixQ4=',
                        'Date: Mon, 31 Mar 2025 08:05:38 GMT',
                        'Content-Type: application/x-www-form-urlencoded'),
                ));

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                if ($httpCode === 429) {
                    sleep($backoff);
                    $backoff *= 2;
                    $retry++;
                } else {
                    return json_decode($response, true);
                }
            } while ($retry < $max_retries);

            sleep(1);
}

private function postCurlApi($url, $curr_date, $location_id, $max_retries,$apistatus,$end_date)
    {
        // dd($url, $curr_date, $location_id, $max_retries,$apistatus,$end_date);
        if($apistatus == 'checkinreport'){
                $post_fields = "startdate={$curr_date}&enddate={$end_date}&entitylocation={$location_id}";
                $head_fields = [
                                'md-authorization: MD 7b40af0edaf0ad75:jR1+YyQZVWCIIaXlgxt1z8uixQ4=',
                                'Date: Mon, 31 Mar 2025 08:05:38 GMT',
                                'Content-Type: application/x-www-form-urlencoded'
                            ];
            }
            elseif($apistatus == 'registrationreport'){
                $dte = substr($curr_date, 0, 8);
                $post_fields = "registrationdate={$dte}";
                // echo $post_fields;exit;
                $head_fields = [
                                'md-authorization: MD 7b40af0edaf0ad75:zzJIrJPzgSOMhucj/1bXawbz+GI=',
                                'Date: Fri, 11 Apr 2025 06:18:59 GMT',
                                'Content-Type: application/x-www-form-urlencoded',
                                'Cookie: SRV=s1; vid3=CvAABmf4wWdOP+VJBV+AAg=='
                            ];
    }

    elseif($apistatus == 'checkintimeline'){
                $post_fields = "";
                $head_fields = [
                                'Authorization: MD 7b40af0edaf0ad75:'.$end_date,
                                'Date: Wed, 31 May 2025 10:00:00 GMT',
                                'Content-Type: application/x-www-form-urlencoded'
                            ];
        }
    else{
        // dd(12);
                $post_fields = "date={$curr_date}&entitylocation={$location_id}";
                $head_fields = [
                        'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
                        'Date: Fri, 07 Mar 2025 10:07:52 GMT',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Cookie: SRV=s1'
                    ];
            }
            // dd($post_fields,$head_fields);
            $retry = 0;
            $backoff = 1;
            do {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $post_fields,
                    CURLOPT_HTTPHEADER =>  $head_fields,
                ));

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                // dd($response);
                if ($httpCode === 429) {
                    sleep($backoff);
                    $backoff *= 2;
                    $retry++;
                } else {
                    return json_decode($response, true);
                }
            } while ($retry < $max_retries);

            sleep(1);
    }


     public function startMrdProcessing(Request $request)
{
    set_time_limit(0);
    $ph_id = $request->ph_id;
    $cacheKey = 'progress_' . $ph_id;
    Cache::put($cacheKey, 10);  // Start progress at 10%

    $locations = $this->cityArray();
    $end_date = date('Ymd') . '23:59:59';
    $start_date = date('Ymd', strtotime('-10 days')) . '00:00:00';
    $checkinDatas = [];
    $total = count($locations);
    $count = 0;

    $progressPoints = [10, 20, 30, 40, 50, 60, 70, 80, 90];
    $currentProgressIndex = 0;

    foreach ($locations as $locationId => $locationName) {
        $count++;
        $percentage = intval(($count / $total) * 100);
        while (
            $currentProgressIndex < count($progressPoints) &&
            $percentage >= $progressPoints[$currentProgressIndex]
        ) {
            Cache::put($cacheKey, $progressPoints[$currentProgressIndex]);
            $currentProgressIndex++;
        }

        $checkin_data = $this->postCurlApi($start_date, $locationId, 5, $end_date);
        if (!empty($checkin_data['checkinlist'])) {
            foreach ($checkin_data['checkinlist'] as $checkin) {
                $checkinDatas[] = $this->saveCurlDatas($checkin);
            }
        }
    }

    Cache::put("data_$ph_id", $checkinDatas);
    Cache::put($cacheKey, 100);

    return response()->json(['status' => 'processing started']);
}

public function getProgress(Request $request)
{
    $ph_id = $request->ph_id;
    $progress = Cache::get('progress_' . $ph_id, 0);
    return response()->json(['progress' => $progress]);
}

public function getMrdFinalResult(Request $request)
{
    $ph_id = $request->ph_id;
    $allData = Cache::get("data_$ph_id", []);
    $filtered = array_values(array_filter($allData, function ($item) use ($ph_id) {
        return $item['phid'] === $ph_id;
    }));
    return response()->json($filtered);
}


public function discountform_edit(Request $request){
    $id = $request->input('dis_id');
    $securityDetails=DiscountFormModel::where('dis_id',$id)->first();
    return response()->json($securityDetails);
}

public function discountformeditsave(Request $request){
    // dd($request->all());
    $admin = auth()->user();
    $disId = $request->input('dis_id');
    $disId = is_string($disId) ? trim($disId) : $disId;
    if ($disId !== null && $disId !== '') {
        $disId = (int) $disId;
    } else {
        $disId = null;
    }
    $wifemrdno = $request->input('dis_wife_mrd_no');
    $husmrdno = $request->input('dis_husband_mrd_no');

    $validatedData = $request->validate([
        'dis_id' => 'nullable|integer',
        'dis_zone_id' => 'required|string|max:255',
        'dis_wife_mrd_no' => 'required|string|max:255',
        'dis_husband_mrd_no' => 'required|string|max:255',
        'dis_service_name'=> 'nullable|string|max:255',
        'dis_wife_name'=> 'nullable|string|max:255',
        'dis_husband_name'=> 'nullable|string|max:255',
        'dis_total_bill'=> 'nullable|string|max:255',
        'dis_expected_request'=>'nullable|string|max:255',
        'dis_post_discount' => 'nullable|string|max:255',
        'dis_patient_ph' => 'nullable|string|max:255',
        'dis_counselled_by' => 'nullable|string|max:500',
        'dis_final_auth' => 'nullable|string|max:255',
        'dis_branch_no' => 'nullable|string|max:255',
        'dis_auth_by' => 'nullable|string|max:255',
        'dis_approved_by' => 'nullable|string|max:255',
        'dis_wife_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'dis_husband_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'dis_drsign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'dis_cc_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'dis_admin_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'dis_form_status' => 'nullable|string|max:255',
    ]);

    if ($admin->access_limits == 2) {
        if (empty($request->dis_branch_no) || empty($request->dis_auth_by)) {
            return response()->json(['success' => false, 'message' => 'B.R. No. and Authorised By are required for your role.']);
        }
    }
    if ($admin->access_limits == 1) {
        if (empty($request->dis_approved_by)) {
            return response()->json(['success' => false, 'message' => 'Final Approved By is required.']);
        }
    }

    $record = null;
    $isUpdate = false;
    if ($disId !== null && $disId > 0) {
        $record = DiscountFormModel::where('dis_id', $disId)->first();
        if (!$record) {
            $record = DiscountFormModel::where('dis_wife_mrd_no', $wifemrdno)
                ->where('dis_husband_mrd_no', $husmrdno)
                ->first();
        }
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Record not found for update.']);
        }
        $isUpdate = true;
    }

    $imagePaths = [];
    $signatureFields = ['dis_wife_sign', 'dis_husband_sign', 'dis_drsign', 'dis_cc_sign', 'dis_admin_sign'];
    foreach ($signatureFields as $field) {
        if ($request->hasFile($field)) {
            $image = $request->file($field);
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('discount_form'), $filename);
            $imagePaths[$field] = json_encode(['discount_form/' . $filename]);
        } elseif ($request->filled($field . '_base64')) {
            $filename = time() . '_' . $field . '.png';
            file_put_contents(
                public_path('discount_form/' . $filename),
                base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $request->input($field . '_base64')))
            );
            $imagePaths[$field] = json_encode(['discount_form/' . $filename]);
        } else {
            $imagePaths[$field] = ($record && $record->$field) ? $record->$field : json_encode([]);
        }
    }

    $attachmentPaths = [];
    if ($record && $record->dis_attachments) {
        $attachmentPaths = is_string($record->dis_attachments) ? json_decode($record->dis_attachments, true) : (array) $record->dis_attachments;
        if (!is_array($attachmentPaths)) $attachmentPaths = [];
    }
    if ($request->hasFile('discount_attachments')) {
        foreach ($request->file('discount_attachments') as $file) {
            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('discount_form'), $filename);
            $attachmentPaths[] = 'discount_form/' . $filename;
        }
    }

    $mainCounselled = $request->filled('dis_counselled_by') ? trim($request->dis_counselled_by) : null;
    $includeVal = $request->filled('dis_counselled_by_include') ? $request->dis_counselled_by_include : null;
    $notIncludeVal = $request->filled('dis_counselled_by_not_include') ? $request->dis_counselled_by_not_include : null;
    $disCounselledBy = trim(implode(' / ', array_filter([$mainCounselled, $includeVal, $notIncludeVal])));

    $dataToSave = array_merge($validatedData, [
        'dis_counselled_by' => $disCounselledBy ?: null,
        'dis_counselled_by_include' => $includeVal ? json_encode([$includeVal]) : null,
        'dis_counselled_by_not_include' => $notIncludeVal ? json_encode([$notIncludeVal]) : null,
        'dis_wife_sign' => $imagePaths['dis_wife_sign'],
        'dis_husband_sign' => $imagePaths['dis_husband_sign'],
        'dis_drsign' => $imagePaths['dis_drsign'],
        'dis_cc_sign' => $imagePaths['dis_cc_sign'],
        'dis_admin_sign' => $imagePaths['dis_admin_sign'],
        'dis_counselled_by_include' => $request->dis_counselled_by_include,
        'dis_counselled_by_not_include' => $request->dis_counselled_by_not_include,
        'dis_attachments' => count($attachmentPaths) ? json_encode($attachmentPaths) : null,
        'created_by' => $admin->id,
        'status' => 1,
    ]);

    if ($isUpdate) {
        $record->fill($dataToSave);
        $record->save();
    } else {
        $record = DiscountFormModel::create($dataToSave);
    }

    $updatedRecord = DB::table('hms_discount_form')
        ->select(
            'hms_discount_form.*',
            'tbl_locations.name as location_name',
            'tblzones.name as zone_name',
            'users.user_fullname as username',
            'users.username as userid'
        )
        ->leftJoin('tbl_locations', 'hms_discount_form.dis_zone_id', '=', 'tbl_locations.id')
        ->leftJoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->leftJoin('users', 'hms_discount_form.created_by', '=', 'users.id')
        ->where('hms_discount_form.dis_id', $record->dis_id)
        ->first();

    return response()->json([
        'success' => true,
        'message' => 'Discount form saved successfully!',
        'updatedRecord' => $updatedRecord
    ]);
}
    public function general_documentadded(Request $request)
    {
        $validatedData = $request->validate([
            'zone_id' => 'required|string|max:255',
            'document_name' => 'required|string|max:255',
            'expire_date' => 'required|string|max:255',
            'images.*' => 'required|file|mimes:pdf|max:12048',
        ]);
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $destinationPath = public_path('document_data');
                $image->move($destinationPath, $filename);
                $imagePaths[] = 'document_data/' . $filename;
            }
        }
        $doctor = documentdetails::create(array_merge($validatedData, [
            'zone_id' => $request->zone_id,
            'document_type' => json_encode($imagePaths),
            'document_id' => 7
        ]));
        return response()->json(['success' => true, 'message' => 'Document saved successfully!']);
    }

    public function securityEdit(Request $request)
    {
        $id = $request->input('sec_id');
        $securityDetails = securitydetailsModel::where('sec_id', $id)->first();
        return response()->json($securityDetails);
    }
    public function edit_security_data(Request $request)
    {
        //echo "<pre>";print_r($request->all());exit;

        $secId = $request->input('sec_id');
        $location = $request->input('zone_id');
        $sec_name = $request->input('sec_name');
        $sec_phone = $request->input('sec_phone');
        $sec_address = $request->input('sec_address');
        $sec_shift = $request->input('sec_shift');
        $sec_joining_date = $request->input('sec_joining_date');
        // echo "<pre>"; print_r($location); exit;
        $imagePaths = [];
        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $destinationPath = public_path('document_data');
                $image->move($destinationPath, $filename);
                $imagePaths[] = 'document_data/' . $filename;
            }
        }

        $documents = securitydetailsModel::where('sec_id', $secId)
            ->update([
                'sec_id_proof' => json_encode($imagePaths),
                'zone_id' => $location,
                'sec_name' => $sec_name,
                'sec_phone' => $sec_phone,
                'sec_address' => $sec_address,
                'sec_shift' => $sec_shift,
                'sec_joining_date' => $sec_joining_date,
            ]);


        return response()->json(['success' => true, 'message' => 'Security Updated successfully!']);
    }
    public function delete_security_data(Request $request)
    {
        $id = $request->input('sec_id');
        $securityDetails = securitydetailsModel::where('sec_id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Security Deleted Successfully']);
    }
    public function securityfillterreport(Request $request)
    {

        $fitterremovedataall = $request->input('morefilltersall');
        $datefiltervalue = $request->input('moredatefittervale');

        $dates = explode(' - ', $datefiltervalue);
        // echo "<pre>";print_r($dates);exit;
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"

        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        $data = securitydetailsModel::select('tbl_locations.name', 'tblzones.name as zone_name', 'security_details.*')
            ->join('tbl_locations', 'security_details.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->whereDate('security_details.created_at', '>=', $startdates)->where('security_details.created_at', '<=', $enddates);

        // dd($data);
        // echo"<pre>"; print_r($data);exit;

        if ($fitterremovedataall) {
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                $value = trim($value, "'");
                $data->whereIn(trim($column), explode(',', $value));
            }
        }
        $data = $data->orderBy('security_details.created_at', 'desc')->get();

        return response()->json($data);
    }
    public function securityshiftdata(Request $request)
    {
        $sec_shift_type = $request->input('sec_shift_type');

        $data = securitydetailsModel::select('tbl_locations.name', 'tblzones.name as zone_name', 'security_details.*')
            ->join('tbl_locations', 'security_details.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id');

        if ($request->sec_shift_type) {
            $data->where('security_details.sec_shift', $request->sec_shift_type);
        }
        $data = $data->orderBy('security_details.created_at', 'desc')->get();
        // echo"<pre>"; print_r($data);exit;
        return response()->json($data);
    }

    public function securityDetails(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale');
        $statusid = $request->input('statusid');
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";

        $query = securitydetailsModel::select('tbl_locations.name', 'tblzones.name as zone_name', 'security_details.*')
            ->join('tbl_locations', 'security_details.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id');
        if ($statusid == 2) {
            $query->whereBetween('security_details.created_at', [$startdates, $enddates]);
        }
        $securityDocument = $query->orderBy('security_details.created_at', 'desc')->get();
        return response()->json($securityDocument);
    }
    public function att_detials_edit(request $request)
    {
        $id = $request->input('att_id');
        $attDetails = attendancedetailsModel::where('att_id', $id)->first();
        return response()->json($attDetails);
    }

    public function licexpdatefilter(Request $request)
    {
        $sec_date_type = $request->input('sec_date_type');

        $data = documentdetails::select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_document_manage.*')
            ->join('tbl_locations', 'hms_document_manage.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id');

        if ($request->sec_date_type) {
            $data->where('hms_document_manage.expire_date', $request->sec_date_type);
        }
        $data = $data->orderBy('hms_document_manage.created_at', 'desc')->get();
        // echo"<pre>"; print_r($data);exit;
        return response()->json($data);
    }

    public function attendancedatefilter(Request $request)
    {
        $sec_date_type = $request->input('sec_date_type');

        $data = attendancedetailsModel::select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_attendance.*')
            ->join('tbl_locations', 'hms_attendance.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id');

        if ($request->sec_date_type) {
            $data->where('hms_attendance.att_from_date', $request->sec_date_type);
        }
        $data = $data->orderBy('hms_attendance.created_at', 'desc')->get();
        // echo"<pre>"; print_r($data);exit;
        return response()->json($data);
    }
    public function attendance_detials(Request $request)
    {

        $fitterremovedataall = $request->input('morefilltersall');
        $datefiltervalue = $request->input('moredatefittervale');

        $dates = explode(' - ', $datefiltervalue);
        // echo "<pre>";print_r($dates);exit;
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"

        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        //echo "<pre>";print_r($dept);exit;
        $data = attendancedetailsModel::select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_attendance.*')
            ->join('tbl_locations', 'hms_attendance.zone_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->whereDate('hms_attendance.created_at', '>=', $startdates)->where('hms_attendance.created_at', '<=', $enddates);

        // echo"<pre>"; print_r($data);exit;
        if ($fitterremovedataall) {
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                $value = trim($value, "'");
                $data->whereIn(trim($column), explode(',', $value));
            }
        }
        $data = $data->orderBy('hms_attendance.created_at', 'desc')->get();

        return response()->json($data);
    }

    public function edit_attendance(Request $request)
    {
        $attId = $request->input('att_id');
        $location = $request->input('zone_id');
        $attfromdate = $request->input('att_from_date');
        $atttodate = $request->input('att_to_date');

        // Validate the request
        $validatedData = $request->validate([
            'images.*' => 'mimes:jpeg,jpg,png,gif|max:2048', // max 2MB per image
        ], [
            'images.*.mimes' => 'Only jpeg, jpg, png, and gif files are allowed.',
            'images.*.max' => 'Each image must be less than 2MB.',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $destinationPath = public_path('attendance_data');
                    $image->move($destinationPath, $filename);
                    $imagePaths[] = 'attendance_data/' . $filename;
                }
            }
        }

        $documents = attendancedetailsModel::where('att_id', $attId)
            ->update([

                'att_document' => json_encode($imagePaths),
                'zone_id' => $location,
                'att_from_date' => $attfromdate,
                'att_to_date' => $atttodate,
            ]);

        return response()->json(['success' => true, 'message' => 'Attendance Details Updated successfully!']);
    }

    public function attendance_delete(Request $request)
    {
        $id = $request->input('att_id');
        $securityDetails = attendancedetailsModel::where('att_id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Security Deleted Successfully']);
    }

    // public function licensedoc_detials(Request $request)
    // {

    //     $fitterremovedataall = $request->input('morefilltersall');
    //     $datefiltervalue = $request->input('moredatefittervale');

    //     $dates = explode(' - ', $datefiltervalue);
    //     // echo "<pre>";print_r($dates);exit;
    //     $startDate = $dates[0];  // "29/12/2024"
    //     $endDate = $dates[1];    // "04/01/2025"

    //     $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    //     $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

    //     $startdates=$startDateFormatted." 00:00:00";
    //     $enddates=$endDateFormatted." 23:59:59";
    //     $data = documentdetails::select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_document_typename.doc_type','hms_document_typename.doc_name', 'hms_document_manage.*')
    //     ->join('tbl_locations', 'hms_document_manage.zone_id', '=', 'tbl_locations.id')
    //     ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
    //     ->join('hms_document_typename', 'hms_document_typename.doc_id', '=', 'hms_document_manage.document_type_id')
    //     ->whereDate('hms_document_manage.created_at', '>=', $startdates)->where('hms_document_manage.created_at', '<=', $enddates);

    //         if($fitterremovedataall){
    //                 // Split conditions by 'AND' and loop through them
    //             foreach (explode(' AND ', $fitterremovedataall) as $condition) {
    //                 [$column, $value] = explode('=', $condition);
    //                     $value = trim($value, "'");
    //                     $data->whereIn(trim($column), explode(',', $value));
    //             }
    //         }
    //         $data = $data->orderBy('hms_document_manage.created_at', 'desc')->get();

    //     return response()->json($data);
    // }
    public function licensedoc_detials(Request $request)
    {
        $admin = Auth::user();
        
        $fitterremovedataall = $request->input('morefilltersall');
        $datefiltervalue = $request->input('moredatefittervale');

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];
        $endDate = $dates[1];

        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        
        $data = documentdetails::select(
                'tbl_locations.name', 
                'tbl_locations.id as location_id',
                'tblzones.name as zone_name', 
                'tblzones.id as zone_id',
                'hms_document_typename.doc_type',
                'hms_document_typename.doc_name', 
                'hms_document_manage.*'
            )
            ->join('tbl_locations', 'hms_document_manage.zone_id', '=', 'tbl_locations.id') // zone_id = location_id
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->join('hms_document_typename', 'hms_document_typename.doc_id', '=', 'hms_document_manage.document_type_id')
            ->whereDate('hms_document_manage.created_at', '>=', $startdates)
            ->where('hms_document_manage.created_at', '<=', $enddates);

        /* =========================
        ACCESS-BASED FILTERING
        Note: hms_document_manage.zone_id actually stores location/branch ID
        ========================== */
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            // SUPERADMIN (1) / AUDITOR (4) → ALL DATA
            // No additional filtering needed
            
        } elseif ($admin->access_limits == 2) {
            // ZONAL ADMIN (2) → Zone branches + multi-location branches
            $branchIds = [];

            if (!empty($admin->zone_id)) {
                // Get all branches in user's zone
                $zoneBranchIds = DB::table('tbl_locations')
                    ->where('zone_id', $admin->zone_id)
                    ->pluck('id')
                    ->toArray();
                $branchIds = array_merge($branchIds, $zoneBranchIds);
            }

            if (!empty($admin->multi_location)) {
                // Add multi-location branches
                $multiLocationIds = array_map('intval', explode(',', $admin->multi_location));
                $branchIds = array_merge($branchIds, $multiLocationIds);
            }

            $branchIds = array_unique($branchIds);

            if (!empty($branchIds)) {
                // Filter by location IDs (stored in zone_id column)
                $data->whereIn('hms_document_manage.zone_id', $branchIds);
            } else {
                // No access to any branches - return empty
                $data->whereRaw('1 = 0');
            }
            
        } elseif ($admin->access_limits == 3) {
            // ADMIN (3) → Own branch + multi-location branches only
            $branchIds = [$admin->branch_id];
            
            if (!empty($admin->multi_location)) {
                $multiLocationIds = array_map('intval', explode(',', $admin->multi_location));
                $branchIds = array_unique(array_merge($branchIds, $multiLocationIds));
            }
            
            if (!empty($branchIds)) {
                // Filter by location IDs (stored in zone_id column)
                $data->whereIn('hms_document_manage.zone_id', $branchIds);
            } else {
                // No access - return empty
                $data->whereRaw('1 = 0');
            }
            
        } elseif ($admin->access_limits == 5) {
            // USER (5) → Own records only
            $data->where('hms_document_manage.created_by', $admin->id);
        }

        /* =========================
        ADDITIONAL FILTERS
        ========================== */
        if ($fitterremovedataall) {
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                $value = trim($value, "'");
                $data->whereIn(trim($column), explode(',', $value));
            }
        }
        
        $data = $data->orderBy('hms_document_manage.created_at', 'desc')->get();

        return response()->json($data);
    }

    public function doctypename(Request $request)
    {
    $docTypes = $request->input('doc_type');
    $docNames = $request->input('doc_name');

    $count = count($docTypes);

    $errors = [];

    for ($i = 0; $i < $count; $i++) {
        $validator = \Validator::make([
            'doc_type' => $docTypes[$i],
            'doc_name' => $docNames[$i],
        ], [
            'doc_type' => 'required|string|max:255',
            'doc_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            $errors[] = $validator->errors()->all();
            continue;
        }

        DoctypenameModel::create([
            'doc_type' => $docTypes[$i],
            'doc_name' => $docNames[$i],
            'status' => 1
        ]);
    }

    if (!empty($errors)) {
        return response()->json([
            'success' => false,
            'message' => 'Some entries failed validation.',
            'errors' => $errors
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'All valid Document Type & Name entries added successfully!'
    ]);
}

public function getdocnametype() {
    $allDocs = DoctypenameModel::select('doc_id', 'doc_type', 'doc_name')->get();

    $grouped = $allDocs->groupBy('doc_type')->map(function ($items) {
        return $items->map(function ($item) {
            return [
                'id' => $item->doc_id,
                'name' => $item->doc_name,
            ];
        })->unique('id')->values();
    });

    return response()->json($grouped);
}

    // keerthika code end.....


    //ragavi code

    public function gettravel()
    {
        $staffId = auth()->user()->id;
        $admin = auth()->user();

        $locations = TblLocationModel::orderBy('name', 'asc')->get();
        // dd($locations);
        $type = VehicleType::where('status', 0)->orderBy('id', 'asc')->get();
        $vehicle_no = VehicleDetails::select('id', 'vehicle_no', 'make')->where('vehicle_no', '!=', '')->orderBy('id', 'asc')->get();
        return view('superadmin.travel_booking', compact('admin', 'locations', 'type', 'vehicle_no'));
    }

    public function travelDetails()
    {
         // $travelDetails = TravelBooking::get();
         $travelDetails = TravelBooking::select(
            'travel_booking.*',
            'tbl_locations.id as location_id',
            'tbl_locations.name as branch_name'
        )
            ->join('tbl_locations', 'travel_booking.branch', '=', 'tbl_locations.id')->get();
        // dd($travelDetails);
        return response()->json($travelDetails);
    }

    public function travelAdd(Request $request)
    {
         // dd($request->all());
         $imagePaths = [];
         if ($request->hasFile('travel_img')) {
             foreach ($request->file('travel_img') as $file) {
                 if ($file->isValid()) {
                    //  $filename = time() . '_' . $file->getClientOriginalName();
                     $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
                     $destinationPath = public_path('travel_image');

                     $file->move($destinationPath, $filename); // Move file to destination
                     // dd($file);
                     $imagePaths[] = 'travel_image/' . $filename; // Save relative path
                 }
             }
         }
         $location_id_travel =  TblLocationModel::select('id', 'zone_id')->where('name', $request->branch)->first();
         // dd($location_id_travel->id);
         $store_travel = new TravelBooking();
         $store_travel->date_of_travel = $request->date_of_travel;
         $store_travel->branch = $location_id_travel->id;
         $store_travel->date_of_booking = $request->date_of_booking;
         $store_travel->person_name = $request->person_name;
         $store_travel->emp_id = $request->emp_id;
         $store_travel->designation = $request->designation;
         $store_travel->booked_via = $request->booked_via;
         $store_travel->mode_of_travel = $request->mode_of_travel;
         $store_travel->class = $request->class;
         $store_travel->purpose = $request->purpose;
         $store_travel->from = $request->from;
         $store_travel->to = $request->to;
         $store_travel->amount_incurred = $request->amount_incurred;
         $store_travel->invoice_number = $request->invoice_number;
         $store_travel->travel_pnr_ref = $request->travel_pnr_ref;
         $store_travel->travel_status = $request->travel_status;
         $store_travel->approved_by = $request->approved_by;
         $store_travel->refund_status = $request->refund_status;
         $store_travel->refund_amound = $request->refund_amound;
         $store_travel->travel_img = implode(',', $imagePaths);
         // dd($store_travel);
         $store_travel->save();
         return redirect()->back()
             ->with('success', 'Travel added successfully!');
    }

    public function travelEdit(Request $request)
    {
        $id = $request->input('id');
        $travel_edit = TravelBooking::where('id', $id)->first();
        //  dd($travel_edit);
        return response()->json($travel_edit);
    }


    public function travelUpdate(Request $request)
    {
         // dd($request->all());

         $location_id_travel =  TblLocationModel::select('id', 'zone_id')->where('name', $request->branch)->first();
         $imagePaths = [];
         if ($request->hasFile('travel_img')) {
             foreach ($request->file('travel_img') as $file) {
                 if ($file->isValid()) {
                    //  $filename = time() . '_' . $file->getClientOriginalName();
                    $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
                     $destinationPath = public_path('travel_image');

                     $file->move($destinationPath, $filename); // Move file to destination
                     // dd($file);
                     $imagePaths[] = 'travel_image/' . $filename; // Save relative path
                 }
             }
         }
         $edit_travel = TravelBooking::where('id', $request->id)->first();
         $edit_travel->date_of_travel = $request->date_of_travel;
         $edit_travel->date_of_booking = $request->date_of_booking;
         $edit_travel->branch = $location_id_travel->id;
         $edit_travel->person_name = $request->person_name;
         $edit_travel->emp_id = $request->emp_id;
         $edit_travel->designation = $request->designation;
         $edit_travel->booked_via = $request->booked_via;
         $edit_travel->mode_of_travel = $request->mode_of_travel;
         $edit_travel->class = $request->class;
         $edit_travel->purpose = $request->purpose;
         $edit_travel->from = $request->from;
         $edit_travel->to = $request->to;
         $edit_travel->amount_incurred = $request->amount_incurred;
         $edit_travel->invoice_number = $request->invoice_number;
         $edit_travel->travel_pnr_ref = $request->travel_pnr_ref;
         $edit_travel->travel_status = $request->travel_status;
         $edit_travel->approved_by = $request->approved_by;
         $edit_travel->refund_status = $request->refund_status;
         $edit_travel->refund_amound = $request->refund_amound;
         if (!empty($request->hasFile('travel_img'))) {
             $edit_travel->travel_img = implode(',', $imagePaths);
         }

         // dd($edit_travel);
         $edit_travel->update();
         return redirect()->back()
             ->with('success', 'Travel Updated successfully!');
    }


    public function travelFilter(Request $request)
    {
         // dd($request->all());
        // $fitter = $request->input('morefilltersall');

        // $query = TravelBooking::select(
        //     'travel_booking.*',
        //     'tbl_locations.id as location_id',
        //     'tbl_locations.name as branch_name'
        // )
        //     ->join('tbl_locations', 'travel_booking.branch', '=', 'tbl_locations.id');
        // dd($query);

        $fitterremovedataall = $request->input('morefilltersall');

        $moreDateFilterValue = $request->input('moredatefittervale');

        $dates = explode(' - ', $moreDateFilterValue);
        $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d') . ' 00:00:00';
        $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d') . ' 23:59:59';

        $query = TravelBooking::select(
            'travel_booking.*',
            'tbl_locations.id as location_id',
            'tbl_locations.name as branch_name',
            'tblzones.id as zone_id',
            'tblzones.name as zone_name',

        )
            ->join('tbl_locations', 'travel_booking.branch', '=', 'tbl_locations.id')
            ->join('tblzones', 'travel_booking.zone_id', '=', 'tblzones.id')
            ->orderBy('travel_booking.created_at', 'desc')
            ->whereBetween('travel_booking.created_at', [$startDate, $endDate]);

        if ($fitterremovedataall) {
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);
                $value = trim($value, "'");
                $query->whereIn(trim($column), explode(',', $value));
            }
        }

        $query = $query->orderBy('travel_booking.created_at', 'desc')->get();
        // dd($query);

        return response()->json($query);
    }

     // ragavi code end

        public function dateaddedinsetedviews(Request $request)
        {
            try {

                $branchvalue = $request->input('branchvalue');
                $adddates = $request->input('adddates');
                $date = Carbon::parse($adddates)->format('Ymd');

                $userids = auth()->user()->username;

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://mocdoc.in/api/get/billlist/draravinds-ivf',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => 'date=' . $date  . '&entitylocation=' . $branchvalue,
                    CURLOPT_HTTPHEADER => array(
                        'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
                        'Date: Fri, 07 Mar 2025 10:07:52 GMT',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Cookie: SRV=s1'
                    ),
                ));

                $response = curl_exec($curl);
                if ($response === false) {
                    return response()->json(['success' => false, 'message' => curl_error($curl)], 500);
                }
                curl_close($curl);

                $decoded = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json(['success' => false, 'message' => 'Invalid JSON'], 500);
                }

                $paymentTotals = [];

                if (isset($decoded['billinglist']) && is_array($decoded['billinglist'])) {
                    foreach ($decoded['billinglist'] as $item) {
                        $amount = isset($item['amt']) ? intval($item['amt']) : 0;
                        $paymentType = isset($item['paymenttype']) ? $item['paymenttype'] : 'Unknown';

                        if (!isset($paymentTotals[$paymentType])) {
                            $paymentTotals[$paymentType] = 0;
                        }

                        $paymentTotals[$paymentType] += $amount;
                    }
                }

                // Get totals
                $cardTotal = $paymentTotals['Card'] ?? 0;
                $upiTotal = $paymentTotals['UPI'] ?? 0;
                $cashTotal = $paymentTotals['Cash'] ?? 0;
                $neftTotal = $paymentTotals['Neft'] ?? 0;

                // Insert into DB
                $query = incomedetails::create([
                    'income_date'    => $adddates,
                    'moc_doc_cash'   => $cashTotal,
                    'moc_doc_card'   => $cardTotal,
                    'moc_doc_upi'    => $upiTotal,
                    'moc_doc_neft'   => $neftTotal,
                    'branch'         => $branchvalue,
                    'created_by'     => $userids,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ]);

                return response()->json(['success' => true, 'data' => $query]);

            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }


        }

        private function selectQuery($city){
            $zone_id = TblZonesModel::select('id')->where('name',$city)->first();
            $zone_locations = TblLocationModel::where('zone_id',$zone_id->id)->get();
            return $zone_locations;
        }

                private function zoneMapping(){
                        $zoneMap =[
                                        'location1' => 5,
                                        'location7' => 9,
                                        'location14' => 9,
                                        'location6' => 5,
                                        'location20' => 7,
                                        'location21' => 4,
                                        'location22' => 2,
                                        'location23' => 2,
                                        'location24' => 2,
                                        'location26' => 2,
                                        'location27' => 7,
                                        'location28' => 6,
                                        'location29' => 2,
                                        'location30' => 7,
                                        'location31' => 3,
                                        'location32' => 3,
                                        'location33' => 2,
                                        'location34' => 6,
                                        'location36' => 4,
                                        'location39' => 7,
                                        'location40' => 6,
                                        'location41' => 3,
                                        'location42' => 4,
                                        'location43' => 8,
                                        'location44' => 8,
                                        'location45' => 4,
                                        'location46' => 4,
                                        'location47' => 3,
                                        'location48' => 2,
                                        'location49' => 2,
                                        'location50' => 4,
                                        'location51' => 8,
                                        'location52' => 6,
                                        'location13' => 4,
                                    ];
                                    return  $zoneMap;
    }

    public function getEmployeeCCName($branchName, $employeeData, $CCbranchMap) {
        if (!isset($CCbranchMap[$branchName])) {
            return []; // branch name not found
        }
        $targetId = $CCbranchMap[$branchName];
        foreach ($employeeData as $employee) {
            if ($employee['employment_id'] === $targetId) {
                return $employee; // return the matching employee array
            }
        }
        return []; // no match found
    }

 public function checkInBranchFilter(Request $request){
        $fitterremovedataall = $request->input('morefilltersall');
        $dates = explode(' - ', $request->moredatefittervale);
        $startDate = $dates[0] ?? '';
        $endDate = $dates[1] ?? '';
        $start_date = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $end_date = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $query = CheckinModel::select('tbl_checkin_report.*')->join('tblzones', 'tbl_checkin_report.zone_id', '=', 'tblzones.id')
                 ->join('tbl_locations', 'tbl_checkin_report.branch_id', '=', 'tbl_locations.id');
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
                $value = trim($value, "'");
            if ($column == 'tbl_checkin_report.treatment_category') {
                     $query->where(trim($column), 'LIKE', '%' . $value . '%');
                }elseif ($column == 'tbl_checkin_report.patient_age') {
                    $startAge = (int)$value;
                    // echo $startAge;exit;
                    $endAge = $startAge + 9;
                    $query->whereBetween($column, [$startAge, $endAge]);
                }else{
                    $query->where(trim($column), explode(',', $value));
                }
        }
        $checkins = $query->whereBetween('checkin_date', [$start_date, $end_date])->get();

        $employeeData = $this->hrmUsers();
        $employeeData = $employeeData['data'];
        array_shift($employeeData);
        preg_match("/tbl_locations\.name\s*=\s*'([^']*)'/", $fitterremovedataall, $ccname);
        // echo ;exit;
        if (!empty($ccname)) {
                $CCbranchMap = $this->CCName();
                $cc_name_result = $this->getEmployeeCCName($ccname[1], $employeeData, $CCbranchMap);
                $employeeData = [$cc_name_result];
        }
        preg_match("/tblzones\.name\s*=\s*'[^']*'/", $fitterremovedataall, $matches);
        if (!empty($matches)) {
            preg_match("/'([^']+)'/", $fitterremovedataall, $matches);

            $state = $matches[1];
            $zone_locations = $this->selectQuery($state);
            $zone_doctors = $this->selectZoneDoctor($state);
            $employeeData = array_values($employeeData);
            return response()->json(['data' => $checkins,'dropdown' => $zone_locations,'doctor_name' => $zone_doctors,'hrm_users' => $employeeData]);
        }else{
            $zone_locations = TblLocationModel::orderBy('name', 'asc')->get();
            $doctor_name = CheckinModel::select('doctor_name')->where('doctor_name','!=','')->distinct()->orderBy('doctor_name', 'asc')->get();
            return response()->json(['data' => $checkins,'dropdown' => $zone_locations,'doctor_name' => $doctor_name,'hrm_users' => $employeeData]);
        }
    }

private function CCName(){
        $branchMap = [
                    "Aathur" => "11571",
                    "Assam" => "40",
                    "Bangladesh" => "13",
                    "Bengaluru - Dasarahalli" => "10592",
                    "Bengaluru - Electronic City" => "10592",
                    "Bengaluru - Hebbal" => "12246",
                    "Bengaluru - Konanakunte" => "10592",
                    "Chengalpattu" => "11102",
                    "Chennai - Madipakkam" => "12093",
                    "Chennai - Urapakkam" => "11972",
                    "Chennai - Sholinganallur" => "11727",
                    "Chennai - Tambaram" => "30006",
                    "Chennai - Vadapalani" => "12093",
                    "Coimbatore - Ganapathy" => "11605",
                    "Coimbatore - Sundarapuram" => "11308",
                    "Coimbatore - Thudiyalur" => "10055",
                    "Corporate Office - Guindy" => "44",
                    "Erode" => "10969",
                    "Harur" => "10965",
                    "Hosur" => "11965",
                    "Kallakurichi" => "11159",
                    "Kanchipuram" => "10395",
                    "Karur" => "29",
                    "Kerala - Kozhikode" => "11909",
                    "Kerala - Palakkad" => "11526",
                    "Madurai" => "11710",
                    "Nagapattinam" => "45",
                    "Namakal" => "11891",
                    "Pennagaram" => "11882",
                    "Pollachi" => "11310",
                    "Salem" => "10737",
                    "Sathyamangalam" => "10969",
                    "Sivakasi" => "11710",
                    "Sri Lanka" => "12",
                    "Tanjore" => "11486",
                    "Thirupathur" => "10154",
                    "Thiruvallur" => "12082",
                    "Thiruvannamalai" => "49",
                    "Tirupati" => "12215",
                    "Tiruppur" => "10302",
                    "Trichy" => "10101",
                    "Vellore" => "12215",
                    "Villupuram" => "42",
                ];
                return $branchMap;
        }

    private function selectZoneDoctor($city){
                $zone_id = TblZonesModel::select('id')->where('name',$city)->first();
                $doctor_name = CheckinModel::select('doctor_name')->where('zone_id',$zone_id->id)->distinct()->orderBy('doctor_name', 'asc')->get();
                return $doctor_name;
    }

    public function checkInDateFilter(Request $request){
            $fitterremovedataall = $request->input('morefilltersall');
            $dates = explode(' - ', $request->datefiltervalue);
            $startDate = $dates[0] ?? '';
            $endDate = $dates[1] ?? '';
            $start_date = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $end_date = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
            // echo $start_date;exit;
            $query = CheckinModel::select('tbl_checkin_report.*')->join('tblzones', 'tbl_checkin_report.zone_id', '=', 'tblzones.id')
                 ->join('tbl_locations', 'tbl_checkin_report.branch_id', '=', 'tbl_locations.id');
            if(!empty($fitterremovedataall)){
                foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                    [$column, $value] = explode('=', $condition);
                        $value = trim($value, "'");
                    if ($column == 'tbl_checkin_report.treatment_category') {
                            $query->where(trim($column), 'LIKE', '%' . $value . '%');
                        }elseif ($column == 'tbl_checkin_report.patient_age') {
                            $startAge = (int)$value;
                            $endAge = $startAge + 9;
                            $query->whereBetween($column, [$startAge, $endAge]);
                        }else{
                            $query->where(trim($column), explode(',', $value));
                        }
                }
            }
                $checkins = $query->whereBetween('checkin_date', [$start_date, $end_date])->get();
                return response()->json($checkins);
    }

   public function checkinfetchDetails(Request $request){

            $dates = explode(' - ', $request->moredatefittervale);
            $startDate = $dates[0] ?? '';
            $endDate = $dates[1] ?? '';
            $start_date = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $end_date = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
            // echo "<pre>";print_r($start_date);exit;
            $checkins = CheckinModel::whereBetween('checkin_date', [$start_date, $end_date])->get();
            $zone_locations = TblLocationModel::orderBy('name', 'asc')->get();
            $doctor_name = CheckinModel::select('doctor_name')->where('doctor_name','!=','')->distinct()->orderBy('doctor_name', 'asc')->get();
            $employeeData = $this->hrmUsers();
            $employeeData = $employeeData['data'];
            array_shift($employeeData);

            return response()->json(['data' => $checkins,'dropdown' => $zone_locations,'doctor_name' => $doctor_name,'hrm_users' => $employeeData]);

    }
    //vasanth
      public function masteraccess()
    {
        $admin = auth()->user();
        $locations = TblLocationModel::all();
        return view('superadmin.masteraccess', ['admin' => $admin,'locations' => $locations]);
    }

    // public function getEmployeeData()
    // {
    //     $employeeData = $this->hrmUsers();
    //     dd($employeeData);
    //     if (isset($employeeData['error'])) {
    //         return response()->json(['error' => 'Failed to fetch employee data'], 500);
    //     }

    //     $employees = $employeeData['data'];
    //     array_shift($employees); // Remove header if needed

    //     // Fetch zone and branch names from DB
    //     $zones = DB::table('tblzones')->pluck('name', 'id');
    //     $branches = DB::table('tbl_locations')->pluck('name', 'id');

    //     // Transform the data
    //     $formattedData = array_map(function($employee) use ($zones, $branches) {
    //         return [
    //             'id' => $employee['employment_id'],
    //             'user' => [
    //                 'id' => $employee['employment_id'],
    //                 'name' => $employee['fullname'],
    //                 'email' => '', // Add email if available
    //             ],
    //             'role' => [
    //                 'name' => $employee['designation_name'] ?? 'Not specified',
    //             ],
    //             'permissions' => [], // Empty for now
    //             'created_at' => now()->toDateTimeString(), // Use actual created date if available
    //             'zone_name' => $zones[$employee['zone_id']] ?? 'Unknown Zone',
    //             'branch_name' => $branches[$employee['branch_id']] ?? 'Unknown Branch',
    //             'zone_id' => $employee['zone_id'] ?? null,
    //             'branch_id' => $employee['branch_id'] ?? null,
    //         ];
    //     }, $employees);
    // // dd($formattedData);
    //     return response()->json([
    //         'data' => $formattedData,
    //         'total' => count($formattedData)
    //     ]);
    // }



    // public function updateUserStatus(Request $request)
    // {
    //     // dd($request);
    //     $request->validate([
    //         'user_id' => 'required|integer|exists:users,id',
    //         'active_status' => 'required|in:0,1',
    //     ]);

    //     DB::table('users')
    //         ->where('id', $request->user_id)
    //         ->update(['active_status' => $request->active_status]);

    //     return response()->json(['success' => true]);
    // }
    public function getEmployeeData()
    {
        $employeeData = $this->hrmUsers();

        if (isset($employeeData['error'])) {
            return response()->json(['error' => 'Failed to fetch employee data'], 500);
        }
        // dd($employeeData);
        $employees = $employeeData['data'];
        array_shift($employees); // remove header row if needed

        // Fetch zones and branches
        $zones = DB::table('tblzones')->pluck('name', 'id');
        $branches = DB::table('tbl_locations')->pluck('name', 'id');

        // Get all users (match username with employment_id)
        $users = DB::table('users')->get(['id', 'user_fullname', 'username', 'email', 'role_id', 'branch_id', 'zone_id', 'active_status', 'status_changed_on', 'status_modified_by',
        'multi_location', 'multi_location_name', 'premission_created_by', 'permission_modified_at']);
        $usersByUsername = $users->keyBy('username');
        // Build helper lookup for modifier names
        $usersById = $users->keyBy('id');
        foreach ($users as $user) {

            if (!empty($user->premission_created_by)&& isset($usersById[$user->premission_created_by])) {
                $user->created_by_username =$usersById[$user->premission_created_by]->username;
            } else {
                $user->created_by_username = '-';
            }
        }
        $activeCount = $users->where('active_status', 0)->count();   // active
        $inactiveCount = $users->where('active_status', 1)->count(); // inactive
        $formattedData = array_map(function ($employee) use ($zones, $branches, $usersByUsername, $usersById) {

            // Match user by employment_id or fallback to "Aravind"
            if ($employee['employment_id'] === '00000') {
                $user = $usersByUsername['Aravind'] ?? collect($usersByUsername)->firstWhere('user_fullname', 'Aravind');
            } else {
                $user = $usersByUsername[$employee['employment_id']] ?? null;
            }

            // Get modifier name using status_modified_by (if exists)
            $modifierName = null;
            if (!empty($user->status_modified_by)) {
                $modifier = $usersById[$user->status_modified_by] ?? null;
                $modifierName = $modifier->user_fullname ?? null;
            }

            return [
                'id' => $employee['employment_id'],
                'user' => [
                    'id' => $employee['employment_id'],
                    'name' => $employee['fullname'],
                    'email' => '', // Add email if available
                ],
                'role' => [
                    'name' => $employee['designation_name'] ?? 'Not specified',
                ],
                'permissions' => [],
                'created_at' => now()->toDateTimeString(),
                'zone_name' => $zones[$employee['zone_id']] ?? 'Unknown Zone',
                'branch_name' => $branches[$employee['branch_id']] ?? 'Unknown Branch',
                'zone_id' => $employee['zone_id'] ?? null,
                'branch_id' => $employee['branch_id'] ?? null,

                // From local users table (matched or fallback)
                'user_id' => $user->id ?? null,
                'multi_location' => $user->multi_location ?? null,
                'multi_location_name' => $user->multi_location_name ?? null,
                'premission_created_by' => $user->premission_created_by ?? null,
                'permission_modified_at' => $user->permission_modified_at ?? null,
                'created_by_username' => $user->created_by_username ?? null,
                'user_fullname' => $user->user_fullname ?? null,
                'username' => $user->username ?? null,
                'email' => $user->email ?? null,
                'role_id' => $user->role_id ?? null,
                'active_status' => $user->active_status ?? null,
                'status_changed_on' => $user->status_changed_on ?? null,
                'status_modified_by' => $user->status_modified_by ?? null,
                'status_modified_name' => $modifierName , // ✅ Added readable name
            ];
        }, $employees);

        // dd($formattedData);

        return response()->json([
            'data' => $formattedData,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
            'total' => count($formattedData)

        ]);
    }
    public function updateUserStatus(Request $request)
    {
        // dd($request);
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'active_status' => 'required|in:0,1',
            'status_date' => 'required|date',
        ]);

        DB::table('users')
            ->where('id', $request->user_id)
            ->update([
                'active_status' => $request->active_status,
                'status_changed_on' => $request->status_date, // make sure this column exists
                'status_modified_by' => auth()->user()->id, // make sure this column exists
            ]);

        return response()->json(['success' => true]);
    }



    public function getMenuPermissions(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $menus = DB::table('menus')->get();

        if($employeeId == 00000) {
            $user_data = DB::table('users')->where('username','Aravind')->first();
        } else {
            $user_data = DB::table('users')->where('username', $employeeId)->first();
        }

        $accessLog = $user_data ? DB::table('access_log')->where('employee_id', $user_data->id)->first() : null;

        // Get all users for reporting manager dropdown
        $managers = DB::table('users')
            ->select('id', 'username','user_fullname')
            ->where('id', '!=', $user_data ? $user_data->id : 0)
            ->get();
        if (!$user_data) {
            return response()->json([
                'menus' => $menus,
                'user_permissions' => [],
                'role_id' => 3,
                'email' => '',
                'managers' => $managers,
                'reporting_manager' => null,  // Match your column name
                'zonal_head' => 0
            ]);
        }

        $userPermissions = DB::table('user_menus')
            ->where('user_id', $user_data->id)
            ->where('status','1')
            ->pluck('menu_id')
            ->toArray();

            // dd($user_data->reporting_manager);

        return response()->json([
            'user_data' => $user_data,
            'menus' => $menus,
            'user_permissions' => $userPermissions,
            'role_id' => $accessLog ? $accessLog->access_limits : 3,
            'email' => $user_data->email,
            'managers' => $managers,
            'reporting_manager' => $user_data->reporting_manager,
            'zonal_head' => $user_data->zonal_head ?? 0
        ]);
    }

    public function savePermissions(Request $request)
    {

        // dd($request->input('zone_id'));
        // $validated = $request->validate([
        //     'employee_id' => 'required',
        //     'role_id' => 'required',
        //     'email' => 'required|email',
        //     'reporting_manager' => 'required',
        //     'zonal_head' => 'required',
        //     'branch_id' => 'required',
        //     'zone_id' => 'required',
        //     'menus' => 'array'
        // ]);
        $admin = auth()->user();
        $employeeId = $request->input('employee_id');
        $employee_Name = $request->input('employee_name');
        $employeeName = ($employee_Name === 'Dr. Aravind MD') ? 'Aravind' : $employee_Name;
        $username = ($employeeId === '00000') ? 'Aravind' : $employeeId;
        $roleId = $request->input('role_id');
        $email = $request->input('email');
        $password = $request->input('password');
        $reportingManager = $request->input('reporting_manager');
        $zonalHead = $request->input('zonal_head');
        $branchId = $request->input('branch_id');
        $zoneId = $request->input('zone_id');
        $multiLocId = $request->input('multiLocId');
        $multiLocNames = $request->input('multiLocNames');
        $menus = $request->input('menus', []);


        // dd($request);
        // Prepare user data
        $userData = [
            'username' => $username,
            'user_fullname' => $employeeName,
            'role_id' =>'1',
            'email' => $email,
            'reporting_manager' => $reportingManager,
            'zonal_head' => $zonalHead,
            'branch_id' => $branchId,
            'access_limits' => $roleId,
            'city' => $branchId,
            'zone_id' => $zoneId,
            'multi_location' => $multiLocId,
            'multi_location_name' => $multiLocNames,
            'premission_created_by' => $admin->id,
            'permission_modified_at' => now(),
            'updated_at' => now()
        ];
        if (!empty($password)) {
            $userData['password'] = Hash::make($password);
        }
        // dd($userData['password']);
        // Handle users table
        // dd($username);
        $user = DB::table('users')
            ->where('username',$username)
            ->first();
        // dd($user);
        if ($user) {
            DB::table('users')
                ->where('username', $username)
                ->update($userData);
            $userId = $user->id;
        } else {
            $userId = DB::table('users')->insertGetId(array_merge([
                'username' => $username,
                'created_at' => now(),
                'password' => Hash::make($password ?: Str::random(8))
            ], $userData));
        }

        // Handle access_log table according to new structure
        $accessLogData = [
            'employee_id' => $userId, // Using users table primary key id
            'zone_id' => $zoneId,
            'branch_id' => $branchId,
            'access_limits' => $roleId,
            // 'updated_at' => now()
        ];

        // Check if record exists by employee_id (users.id)
        $existingAccessLog = DB::table('access_log')
            ->where('employee_id', $userId)
            ->first();

        if ($existingAccessLog) {
            DB::table('access_log')
                ->where('employee_id', $userId)
                ->update($accessLogData);
        } else {
            // $accessLogData['created_at'] = now();
            DB::table('access_log')->insert($accessLogData);
        }

        // Handle user_menus
        DB::table('user_menus')
            ->where('user_id', $userId)
            ->update(['status' => 0]);

        foreach ($menus as $menuId) {
            DB::table('user_menus')->updateOrInsert(
                ['user_id' => $userId, 'menu_id' => $menuId],
                ['status' => 1]
            );
        }

        return response()->json(['success' => true]);
    }


    public function checkinCCName(Request $request){
          set_time_limit(0);
        $employeeData = $this->hrmUsers();
        $employeeData = $employeeData['data'];
        array_shift($employeeData);
        $searchId = $request->empid;

        $result = array_filter($employeeData, function ($item) use ($searchId) {
            return $item['employment_id'] === $searchId;
        });
       $result = array_values($result);
       return response()->json($result);
    }

    private function hrmUsers(){
            $apiUrl = 'https://app.draravindsivf.com/hrms/employee_details_api.php';
            $apiKey = '3x@MpL3-K3Y-98fG_2025!';
            $response = Http::timeout(60)->withoutVerifying()->get($apiUrl, [
                'api_key' => $apiKey
            ]);

            if ($response->successful()) {
                $employeeData = $response->json();
            } else {
                return response()->json(['error' => 'API call failed'], 500);
            }
            return $employeeData;
    }

    public function checkinLastNextFetch(Request $request){

            $dates = explode(' - ', $request->moredatefittervale);
            $startDate = $dates[0] ?? '';
            $endDate = $dates[1] ?? '';
            $start_date = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $end_date = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
            // echo "<pre>";print_r($start_date);exit;
            $checkins = CheckinModel::whereBetween('checkin_date', [$start_date, $end_date])->get();

            return response()->json(['last_data' => $checkins,'next_data' => $checkins]);

    }

    public function checkinNextDateFetch(Request $request){

            $dates = explode(' - ', $request->datefiltervalue);
            $startDate = $dates[0] ?? '';
            $endDate = $dates[1] ?? '';
            $start_date = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $end_date = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
            // echo "<pre>";print_r($start_date);exit;
            $checkins = CheckinModel::whereBetween('checkin_date', [$start_date, $end_date])->get();

           return response()->json($checkins);

    }

    public function checkinLastDateFetch(Request $request){

            $dates = explode(' - ', $request->datefiltervalue);
            $startDate = $dates[0] ?? '';
            $endDate = $dates[1] ?? '';
            $start_date = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $end_date = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
            // echo "<pre>";print_r($start_date);exit;
            $checkins = CheckinModel::whereBetween('checkin_date', [$start_date, $end_date])->get();

            return response()->json($checkins);

    }

    public function checkinUrlApi(){
        set_time_limit(0);
        $checkinSummary = [];
        $current_date = date('d/m/Y');
        $delete_date = date('Y-m-d');
        $url = 'https://mocdoc.in/api/checkedin/draravinds-ivf';
        CheckinModel::whereDate('checkin_date', $delete_date)->delete();
        $checkinArr = $this->checkinreportAPI($current_date, $fitterremovedataall=null,"checkinreport",1,$url);
        // echo "<pre>";print_r($checkinArr);exit;
    }

    public function checkInReportEdit(Request $request){
        // echo "<pre>";print_r($request->all());exit;
          set_time_limit(0);
        $employeeData = $this->hrmUsers();
       $checkin_rpt = CheckinModel::where('id',$request->id)->first();
       $auth = $this->generateHmac($checkin_rpt->phid);
        // echo "<pre>";print_r($auth);exit;
        $url = 'https://mocdoc.com/api/get/patienttimeline/draravinds-ivf/'.$checkin_rpt->phid;
        $checkin_timeline = $this->postCurlApi($url, "", "", 3,"checkintimeline",$auth);
        $data = array_values($checkin_timeline);
        $totalAmountPayable = 0;
        $billItemNames = [];

        foreach ($data as $date => $sections) {
            // --- Handle OP section ---
            if (isset($sections['OP'])) {
                foreach ($sections['OP'] as $key => $entry) {

                    // Check for OP Bill
                    if (isset($entry['op_bill'])) {
                        $opBill = $entry['op_bill'];

                        // Add amountpayable from OP
                        if (isset($opBill['amountpayable'])) {
                            $totalAmountPayable += (float)$opBill['amountpayable'];
                        }

                        // Extract bill item names from OP
                        if (isset($opBill['billitems'])) {
                            foreach ($opBill['billitems'] as $item) {
                                if (isset($item['name'])) {
                                    $billItemNames[] = $item['name'];
                                }
                            }
                        }
                    }

                    // Check for Pharmacy bills
                    if ($key === 'pharmacy_bills') {
                        foreach ($entry as $pharmacyBill) {
                            // Add amountpayable from Pharmacy
                            if (isset($pharmacyBill['amountpayable'])) {
                                $totalAmountPayable += (float)$pharmacyBill['amountpayable'];
                            }

                            // Extract bill item names from Pharmacy
                            if (isset($pharmacyBill['billitems'])) {
                                foreach ($pharmacyBill['billitems'] as $product) {
                                    if (isset($product['name'])) {
                                        $billItemNames[] = $product['name'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return response()->json([
            'total_amount_payable' => number_format($totalAmountPayable, 2),
            'bill_item_names' => $billItemNames,
            'checkin_report' => $checkin_rpt,
        ]);
    }

    private function checkInFileUpload($imagePaths, $documentType_view) {
        $cleanPaths = [];
        foreach ($imagePaths as $pathGroup) {
            $pathGroup = str_replace(['\"', '\\/', '\\'], ['', '/', '/'], $pathGroup);
            $pathGroup = trim($pathGroup, '[]"');
            if (empty($pathGroup)) {
                continue;
            }
            $paths = explode(',', $pathGroup);
            foreach ($paths as $path) {
                $path = trim($path);
                if (!empty($path)) {
                    $cleanPaths[] = $path;
                }
            }
        }
        $allPaths = array_merge($documentType_view, $cleanPaths);
        $allPaths = array_filter($allPaths, function($p) {
            return trim($p) !== '';
        });
        return json_encode(array_values($allPaths));
    }

   public function checkInTimeLine (Request $request){
        set_time_limit(0);
        $url = 'https://mocdoc.com/api/get/patienttimeline/draravinds-ivf/'.$request->phid;
        $checkin_timeline = $this->postCurlApi($url, "", "", 3,"checkintimeline",$request->authorization);
        $checkin_timeline = array_values($checkin_timeline);
        //   echo "<pre>1111111";print_r($checkin_timeline);exit;

        $opBills = [];
        $ipBills = [];
        $pharmacyBills = [];
        $mainBill = [];

        foreach ($checkin_timeline as $dateKey => $entry) {
            // --- OP ---
            if (isset($entry['OP'])) {
                foreach ($entry['OP'] as $key => $value) {
                    // op_bill
                    if (isset($value['op_bill'])) {
                        $opBills[$key] = $value['op_bill'];
                        $mainBill["op_$key"] = $value['op_bill'];
                    }
                    // pharmacy_bills under OP
                    if ($key === 'pharmacy_bills' && is_array($value)) {
                        foreach ($value as $phKey => $phData) {
                            $pharmacyBills[$phKey] = $phData;
                            $mainBill["pharmacy_$phKey"] = $phData;
                        }
                    }
                }
            }

            // --- IP ---
            if (isset($entry['IP'])) {
                foreach ($entry['IP'] as $key => $value) {
                    // main_bill under IP
                    if (isset($value['main_bill'])) {
                        $ipBills[$key] = $value['main_bill'];
                        $mainBill["ip_$key"] = $value['main_bill'];
                    }

                    // pharmacy_bills under IP
                    if (isset($entry['IP']['pharmacy_bills']) && is_array($entry['IP']['pharmacy_bills'])) {
                        foreach ($entry['IP']['pharmacy_bills'] as $phKey => $phData) {
                            $pharmacyBills[$phKey] = $phData;
                            $mainBill["pharmacy_$phKey"] = $phData;
                        }
                    }
                }

                // If pharmacy_bills is a sibling to main_bill in IP
                if (isset($entry['IP']['pharmacy_bills'])) {
                    foreach ($entry['IP']['pharmacy_bills'] as $phKey => $phData) {
                        $pharmacyBills[$phKey] = $phData;
                        $mainBill["pharmacy_$phKey"] = $phData;
                    }
                }
            }
        }
        foreach ($mainBill as &$bill) {
					if (is_array($bill)) {
						$bill['phid'] = $request->phid;
					}
				}
				unset($bill);
        // echo "<pre>1134";print_r($mainBill);exit;
        $finalBills = [];
        if($request->type == 'amountdetails'){
            // echo "<pre>1134";print_r($mainBill);exit;
            $allBillItems = [];
            $searchBillNo=$request->billno;
            $searchPhid=$request->phid;
            $checkin_rpt = CheckinModel::where('phid',$searchPhid)->first();
            $filtered = array_filter($mainBill, function($item) use ($searchBillNo, $searchPhid) {
                    return isset($item['bill_no'], $item['phid']) &&
                        $item['bill_no'] === $searchBillNo  &&
                        $item['phid'] === $searchPhid;
                });

                foreach ($filtered as $bill) {
                    if (isset($bill['billitems']) && is_array($bill['billitems'])) {
                        foreach ($bill['billitems'] as $item) {
                            $allBillItems[] = $item;
                        }
                    }
                }
              $finalBills = $allBillItems;
              if ($checkin_rpt) {
                    $finalBills[] = $checkin_rpt;
                }
        }else{
            foreach ($mainBill as $bill) {
            $services = [];
            if (isset($bill['billitems']) && is_array($bill['billitems'])) {
                $count = 1;
                foreach ($bill['billitems'] as $item) {
                    if (isset($item['name'])) {
                        $services[] = $count . '. ' . $item['name'];
                        $count++;
                    } elseif (isset($item['prodvalue']) && isset($item['prodvalue'])) {
                        continue;
                    }
                }
            }
            $bill['services'] = implode("<br>", $services);
            unset($bill['billitems']);
            $finalBills[] = $bill;
        }
            // echo "<pre>";print_r($finalBills);exit;
        }
        return response()->json(array_values($finalBills));
    }

    public function checkinTreatmentAmt(Request $request){
                    // echo "<pre>";print_r($request->selectedValues);exit;
            $selectedValues = $request->input('selectedValues');
            $categories = TblTreamentCategory::whereIn('name', $selectedValues)->get(['id','name', 'amount']);

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
    }

     public function checkInReportUpdate(Request $request){

            $documentType_view1 = [];
            $documentType_view2 = [];
            $documentType_view3 = [];
            $documentType_view4 = [];
            $documentType_view5 = [];
            $documentType_view6 = [];

            $imagePaths1 = [];
            $imagePaths2 = [];
            $imagePaths3 = [];
            $imagePaths4 = [];
            $imagePaths5 = [];
            $imagePaths6 = [];
            // echo "<pre>";print_r($documentType_view3);exit;
            if ($request->hasFile('blue_book_pdfs')) {
                foreach ($request->file('blue_book_pdfs') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                    $destinationPath = public_path('document_data'); // Path to public/uploads/doctor_images
                    $image->move($destinationPath, $filename); // Move file to the destination folder
                    $imagePaths1[] = 'document_data/' . $filename; // Save relative path
                }
                $file_dt1 = $this->checkInFileUpload($imagePaths1,$documentType_view1);
            }else{
                $data = CheckinModel::select('blue_book_pdfs')->where('id', $request->income_id)->first();
                $file_dt1 = $data->blue_book_pdfs;
            }

            if ($request->hasFile('consent_pdfs')) {
                foreach ($request->file('consent_pdfs') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                    $destinationPath = public_path('document_data'); // Path to public/uploads/doctor_images
                    $image->move($destinationPath, $filename); // Move file to the destination folder
                    $imagePaths2[] = 'document_data/' . $filename; // Save relative path
                }
                 $file_dt2 = $this->checkInFileUpload($imagePaths2,$documentType_view2);
            }else{
                $data = CheckinModel::select('consent_pdfs')->where('id', $request->income_id)->first();
                $file_dt2 = $data->consent_pdfs;
            }

            if ($request->hasFile('fs_study_pdfs')) {
                foreach ($request->file('fs_study_pdfs') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                    $destinationPath = public_path('document_data'); // Path to public/uploads/doctor_images
                    $image->move($destinationPath, $filename); // Move file to the destination folder
                    $imagePaths3[] = 'document_data/' . $filename; // Save relative path
                }
                 $file_dt3 = $this->checkInFileUpload($imagePaths3,$documentType_view3);
            }else{
                $data = CheckinModel::select('fs_study_pdfs')->where('id', $request->income_id)->first();
                $file_dt3 = $data->fs_study_pdfs;
            }

            if ($request->hasFile('inj_pdfs')) {
                foreach ($request->file('inj_pdfs') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                    $destinationPath = public_path('document_data'); // Path to public/uploads/doctor_images
                    $image->move($destinationPath, $filename); // Move file to the destination folder
                    $imagePaths4[] = 'document_data/' . $filename; // Save relative path
                }
                 $file_dt4 = $this->checkInFileUpload($imagePaths4,$documentType_view4);
            }else{
                $data = CheckinModel::select('inj_pdfs')->where('id', $request->income_id)->first();
                $file_dt4 = $data->inj_pdfs;
            }

            if ($request->hasFile('trigger_used_pdfs')) {
                foreach ($request->file('trigger_used_pdfs') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName(); // Unique file name
                    $destinationPath = public_path('document_data'); // Path to public/uploads/doctor_images
                    $image->move($destinationPath, $filename); // Move file to the destination folder
                    $imagePaths5[] = 'document_data/' . $filename; // Save relative path
                }
                $file_dt5 = $this->checkInFileUpload($imagePaths5,$documentType_view5);
            }else{
                $data = CheckinModel::select('trigger_used_pdfs')->where('id', $request->income_id)->first();
                $file_dt5 = $data->trigger_used_pdfs;
            }

            if ($request->hasFile('antag_doses_pdfs')) {
                foreach ($request->file('antag_doses_pdfs') as $image1) {
                    $filename1 = time() . '_' . $image1->getClientOriginalName(); // Unique file name
                    $destinationPath1 = public_path('document_data'); // Path to public/uploads/doctor_images
                    $image1->move($destinationPath1, $filename1); // Move file to the destination folder
                    $imagePaths6[] = 'document_data/' . $filename1; // Save relative path
                }
                $file_dt6 = $this->checkInFileUpload($imagePaths6,$documentType_view6);
            }else{
                $data = CheckinModel::select('antag_doses_pdfs')->where('id', $request->income_id)->first();
                $file_dt6 = $data->antag_doses_pdfs;
            }

            $checkin_rpt =  CheckinModel::where('id', $request->income_id)->update([
                 'blue_book_pdfs' => $file_dt1,
                 'consent_pdfs' => $file_dt2,
                 'fs_study_pdfs' => $file_dt3,
                 'inj_pdfs' => $file_dt4,
                 'trigger_used_pdfs' => $file_dt5,
                 'antag_doses_pdfs' => $file_dt6,
                 'category' => $request->category ?? null,
                 'wife_name' => $request->wife_name ?? null,
                 'wife_mrd_number' => $request->w_mrd_no ?? null,
                 'husband_name' => $request->husband_name ?? null,
                 'husband_mrd_number' => $request->h_mrd_no ?? null,
                 'procedure_name' => $request->procedure_name ?? null,
                 'cycle_no' => $request->cycle_no ?? null,
                 'package_type' => $request->package_type ?? null,
                 'package_amount' => $request->package_amt ?? null,
                 'fs_study_injections_used' => $request->fs_study ?? null,
                 'antag_doses_till_trigger' => $request->antag_doses ?? null,
                 'trigger_used' => $request->trigger_used ?? null,
                 'actual_discount' => $request->actual_discount ?? null,
                 'expected_discount' => $request->expected_discount ?? null,
                 'approved_discount' => $request->approved_discount ?? null,
                 'paid_status' => $request->paid_status ?? null,
                 'consent_type_pdf' => $request->consent_type_pdf ?? null,
                 'blue_book_pdf' => $request->blue_book_pdf ?? null,
                 'cc_handled' => $request->cc_handled ?? null,
                 'aft_discount' => $request->aft_discount ?? null,
                 'loan_management' => $request->loan_management ?? null,
                 'consultant_name' => $request->consultant_name ?? null,
                 'crm_incharge' => $request->crm_incharge ?? null,
            ]);

        return redirect()->back()
            ->with('success','Checkin report updated successfully!');
    }

    public function checkinreportAPI($filterdate,$fitterremovedataall,$apistatus,$statusid,$url){
        $max_retries = 3;
        $checkin_insert = "";
        $startDate = $filterdate ?? '';  // "29/12/2024"
        $endDate = $filterdate ?? '';
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate);
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate);
        $start_date = $startDateFormatted->format('Ymd').'00:00:00';
        $end_date = $endDateFormatted->format('Ymd').'23:59:59';
        $locations = $this->cityArray();

        foreach($locations as $k => $v){
            $checkin_data = $this->postCurlApi($url, $start_date, $k, $max_retries,$apistatus,$end_date);
            if (!empty($checkin_data['checkinlist'])) {
                foreach ($checkin_data['checkinlist'] as $checkin) {
                   $checkinData = $this->saveCurlData($checkin, $k,$apistatus,$v,"");
                //    echo "<pre>";print_r($checkinData);
                   $checkin_insert  = CheckinModel::insert($checkinData);
                }
            }
        }
        return   $checkin_insert;
        // return  1;
        // exit;
    }

 private function cityArray(){
    $locations = array("location1" => "Kerala - Palakkad", "location7" => "Erode", "location14" => "Tiruppur","location6" => "Kerala - Kozhikode", "location20" => "Coimbatore - Ganapathy", "location21" => "Hosur", "location22" => "Chennai - Sholinganallur","location23" => "Chennai - Urapakkam", "location24" => "Chennai - Madipakkam", "location26" => "Kanchipuram", "location27" => "Coimbatore - Sundarapuram", "location28" => "Trichy","location29" => "Thiruvallur", "location30" => "Pollachi", "location31" => "Bengaluru - Electronic City","location32" => "Bengaluru - Konanakunte", "location33" => "Chennai - Tambaram", "location34" => "Tanjore", "location36" => "Harur", "location39" => "Coimbatore - Thudiyalur", "location40" => "Madurai","location41" => "Bengaluru - Hebbal", "location42" => "Kallakurichi", "location43" => "Vellore","location44" => "Tirupati","location45" => "Aathur", "location46" => "Namakal", "location47" => "Bengaluru - Dasarahalli","location48" => "Chengalpattu", "location49" => "Chennai - Vadapalani", "location50" => "Pennagaram","location51" => "Thirupathur", "location52" => "Sivakasi", "location13" => "Salem", "location54" => "Nagapattinam", "location56" => "Krishnagiri", "location57" => "Karur");
    return $locations;
}


// public function incomeReportFetch(Request $request){
//     set_time_limit(0);
//     $incomeArr = [];
//     $zone_locations = TblLocationModel::orderBy('name', 'asc')->get();
//     return response()->json(['data' => $incomeArr,'dropdown' => $zone_locations]);
// }
public function incomeReportFetch(Request $request)
{
    set_time_limit(0);

    $admin = auth()->user();

    $incomeArr = [];
    $zones = collect();
    $locations = collect();

    if ($admin->access_limits == 1 || $admin->access_limits == 4) {

        // Access limit 1 → All zones & all locations
        $zones = TblZonesModel::select('name', 'id')->get();
        $locations = TblLocationModel::select('*')
                        ->orderBy('name', 'asc')
                        ->get();

    } elseif ($admin->access_limits == 2) {

        // Access limit 2 → Only user's zone locations
        $zones = TblZonesModel::select('*')
                    ->where('id', $admin->zone_id)
                    ->get();

        $locations = TblLocationModel::select('*')
                        ->where('zone_id', $admin->zone_id)
                        ->orderBy('name', 'asc')
                        ->get();

    } elseif ($admin->access_limits == 3 || $admin->access_limits == 5) {
        // Other access → Only user branch
        $zones = TblZonesModel::select('*')
                    ->where('id', $admin->zone_id)
                    ->get();

        $locations = TblLocationModel::select('*')
                        ->where('id', $admin->branch_id)
                        ->get();
    }
    return response()->json([
        'data'      => $incomeArr,
        'zones'     => $zones,
        'dropdown'  => $locations
    ]);
}

public function incomereportAPI($filterdate, $fitterremovedataall, $apistatus, $statusid, $url, $indates)
{
    // dd($filterdate, $fitterremovedataall, $apistatus, $statusid, $url, $indates);
    $max_retries = 3;
    $output = [];
    $inc_Data = [];
    $status_id = 0;
    foreach ($indates as $dte) {
        if (strpos($fitterremovedataall, 'tblzones.name') !== false && strpos($fitterremovedataall, 'tbl_locations.name') === false) {
        //    dd(1);
            preg_match("/tblzones\.name='([^']+)'/", $fitterremovedataall, $matches);
            // dd($matches);
            $get_zone = $matches[1] ?? null;
            $zone_array = explode(',', $get_zone);
            $zone_array = array_map('trim', $zone_array);
            $zone_name = TblZonesModel::select('name')->whereIn('name', $zone_array)->get();
            // dd($zone_name);
            foreach ($zone_name as $zonen) {
                $zone_location = $this->selectQuery($zonen->name);
                // dd($zone_location);
            foreach ($zone_location as $zone) {
                    $selectedZoneId = $zone->zone_id;
                    $selectedBranchId = $zone->id;
                    $ilocation = $this->cityArray();
                    // dd($selectedZoneId,$selectedBranchId,$ilocation);
                    $zlocation = array_search($zone->name, $ilocation);
                    // dd($zlocation);
                    $income_data = $this->postCurlApi($url, $dte, $zlocation, $max_retries, $apistatus, "");
                    // dd($income_data);
                    //  echo "<pre>";print_r($zone_location);exit;
                    if (!empty($income_data['billinglist'])) {
                        foreach ($income_data['billinglist'] as $billing) {
                            $inc_Data[] = $this->saveCurlData($billing, $zlocation, $apistatus, $zone->name,$zonen->name);
                        }
                }
            }
        }
        // dd($inc_Data);
                $status_id = 1;
                // foreach ($inc_Data as $item) {
                //         $key = $item['type'] . '' . $item['paymenttype'] . '' . $item['billdate'];
                //         if (!isset($output[$key])) {
                //             $output[$key] = $item;
                //         } else {
                //             $output[$key]['amt'] = number_format((float)$output[$key]['amt'] + (float)$item['amt'], 2, '.', '');
                //         }
                //     }
                //     $output = array_values($output);

        }
        elseif (strpos($fitterremovedataall, 'tblzones.name') !== false && strpos($fitterremovedataall, 'tbl_locations.name') !== false) {
        //    dd(2);
            preg_match_all("/'([^']+)'/", $fitterremovedataall, $matches);
                $dat_values = $matches[1];
                $tot_branch = $dat_values[1];
                $locations = array_map('trim', explode(',', $tot_branch));
             foreach ($locations as $branch) {
                $cilocaton = $this->cityArray();
                $zlocaton = array_search($branch , $cilocaton);
                $income_data = $this->postCurlApi($url, $dte, $zlocaton, $max_retries,$apistatus,"");
            if (!empty($income_data['billinglist'])) {
                    foreach ($income_data['billinglist'] as $billing) {
                        $inc_Data[] = $this->saveCurlData($billing, $zlocaton,$apistatus,$branch,$branch);
                    }
                }
            }
        }else{
                 preg_match("/='(.*?)'/", $fitterremovedataall, $matches);
                //  dd($matches[1]);
                 $ilocation = $matches[1];
                 $ilocation = array_map('trim', explode(',', $ilocation));
                foreach ($ilocation as $branch) {
                    $cilocaton = $this->cityArray();
                    $zlocaton = array_search($branch , $cilocaton);
                    $income_data = $this->postCurlApi($url, $dte, $zlocaton, $max_retries,$apistatus,"");
                if (!empty($income_data['billinglist'])) {
                        foreach ($income_data['billinglist'] as $billing) {
                            $inc_Data[] = $this->saveCurlData($billing, $zlocaton,$apistatus,$branch,$branch);
                        }
                    }
                }
            }
     }

    if($status_id == 1){
        $output = $this->zoneWiseData($inc_Data);
        // dd(88);
    }else{
                    foreach ($inc_Data as $item) {
                        $key = $item['type'] . '' .$item['area'] . '' . $item['paymenttype'] . '' . $item['billdate'];
                        if (!isset($output[$key])) {
                            $output[$key] = $item;
                        } else {
                            $output[$key]['amt'] = number_format((float)$output[$key]['amt'] + (float)$item['amt'], 2, '.', '');
                        }
                    }
                    $output = array_values($output);
    }
    return $output;
}

private function incomeZoneArray($data){
    $final = [];
        foreach ($data as $row) {
            $billdate = $row['billdate'];
            $type = $row['type'];
            $area = $row['area'];
            $paymenttype = $row['paymenttype'];
            $amt = (float)$row['amt'];

            $key = "$billdate|$type|$area";

            if (!isset($final[$key])) {
                $final[$key] = [
                    'billdate' => \DateTime::createFromFormat('Ymd', $billdate)->format('Y-m-d'),
                    'type' => $type,
                    'area' => $area,
                    'cash_amt' => 0,
                    'card_amt' => 0,
                    'neft_amt' => 0,
                    'credit_amt' => 0,
                    'upi_amt' => 0,
                    'total_amt' => 0,
                ];
            }

            switch (strtolower($paymenttype)) {
                 case 'cash':
                    $final[$key]['cash_amt'] = number_format($final[$key]['cash_amt'] + $amt, 2, '.', '');
                    break;
                case 'card':
                    $final[$key]['card_amt'] = number_format($final[$key]['card_amt'] + $amt, 2, '.', '');
                    break;
                case 'neft':
                    $final[$key]['neft_amt'] = number_format($final[$key]['neft_amt'] + $amt, 2, '.', '');
                    break;
                case 'credit':
                    $final[$key]['credit_amt'] = number_format($final[$key]['credit_amt'] + $amt, 2, '.', '');
                    break;
                case 'upi':
                    $final[$key]['upi_amt'] = number_format($final[$key]['upi_amt'] + $amt, 2, '.', '');
                    break;
                default:
                    // handle unknown payment types if needed
                    break;
            }

            $final[$key]['total_amt'] = number_format($final[$key]['cash_amt'] + $final[$key]['card_amt'] + $final[$key]['neft_amt'] + $final[$key]['credit_amt'] + $final[$key]['upi_amt'], 2, '.', '');
        }
        $final = array_values($final);
        return $final;
    }

private function zoneWiseData($data){
        $arr = [];
        $output1 = [];
       foreach ($data as $item) {
        // dd($item);
                        $key = $item['type'] . '' . $item['area'] . '' . $item['paymenttype'] . '' . $item['billdate'];
                        if (!isset($output1[$key])) {
                            $output1[$key] = $item;
                        } else {
                            $output1[$key]['amt'] = number_format((float)$output1[$key]['amt'] + (float)$item['amt'], 2, '.', '');
                        }
                    }
                    // dd($output1);
                   $arr = array_values($output1);
                   $arr['status_id'] =1;
                //    dd($arr);
            // echo "<pre>";print_r($output);exit;
            return $arr;
}

public function incomeDateFilter(Request $request){
    set_time_limit(0);
    $fitterremovedataall = $request->input('morefilltersall');
    if(empty($fitterremovedataall)){
        $fitterremovedataall = "tblzones.name='CHENNAI'";
    }
    $moredatefittervale = $request->datefiltervalue;
    return $this->incomeBranchDateFiltr($fitterremovedataall,$moredatefittervale,"","");
    // echo "<pre>";print_r($branch_fltr);exit;
}

public function incomeBranchFilter(Request $request){
    set_time_limit(0);
    $fitterremovedataall = $request->input('morefilltersall');
    $moredatefittervale = $request->moredatefittervale;
    return $this->incomeBranchDateFiltr($fitterremovedataall,$moredatefittervale,"","");
    // echo "<pre>";print_r($branch_fltr);exit;
}

// private function incomeBranchDateFiltr($fitterremovedataall,$moredatefittervale,$apistatus,$statusid){
//     $incomeArray = [];
//     $finalOutput = [];
//     $indates = [];
//     $dates = explode(' - ', $moredatefittervale);
//     $startDate = $dates[0] ?? '';
//     $endDate = $dates[1] ?? '';
//     $start_date = Carbon::createFromFormat('d/m/Y', $startDate);
//     $end_date = Carbon::createFromFormat('d/m/Y', $endDate);
//     $url = 'https://mocdoc.in/api/get/billlist/draravinds-ivf';

//         while ($start_date <= $end_date) {
//             $indates[] = $start_date->format('Ymd');
//             $start_date->addDay();
//         }
//         // dd($moredatefittervale, $fitterremovedataall,$apistatus,$statusid,$url,$indates);
//     $incomeArray = $this->incomereportAPI($moredatefittervale, $fitterremovedataall,$apistatus,$statusid,$url,$indates);
//         // dd($incomeArray);
//     if (array_key_exists('status_id', $incomeArray)) {
//             unset($incomeArray['status_id']);
//          $finalOutput = $this->incomeZoneArray($incomeArray);
//     } else{
//          $finalOutput = $this->incomeFinalArray($incomeArray);
//     }
//     preg_match("/'([^']+)'/", $fitterremovedataall, $matches);
//     $state = $matches[1];
//     //  echo "<pre>11111111ee";print_r($state);exit;
//     if(empty($finalOutput)){
//         if (strpos($fitterremovedataall, "tblzones.name=") !== false){
//              $zone_locations = $this->zoneQuery($state);
//         }else{
//             $zone_locations = TblLocationModel::orderBy('name', 'asc')->get();
//         }
//         return response()->json(['data' => $finalOutput,'dropdown' => $zone_locations]);
//     }

//     $totalCash = 0;
//     $totalUpi = 0;
//     $totalCard = 0;
//     $totalNeft = 0;
//     $totalTotal = 0;
//     if (strpos($fitterremovedataall, "tblzones.name=") !== false && strpos($fitterremovedataall, "tbl_locations.name=") !== false) {
//         preg_match("/(tbl_income\.category)\s*=\s*'([^']+)'/", $fitterremovedataall, $inc_category);

//         $finalOutput = $this->incomeCategory($inc_category,$finalOutput);
//         dd($finalOutput);
//             foreach ($finalOutput as $key=>$item) {
//                 $totalCash += isset($item['cash_amt']) ? $item['cash_amt'] : 0;
//                 $totalUpi += isset($item['upi_amt']) ? $item['upi_amt'] : 0;
//                 $totalCard += isset($item['card_amt']) ? $item['card_amt'] : 0;
//                 $totalNeft += isset($item['neft_amt']) ? $item['neft_amt'] : 0;
//                 $totalTotal += isset($item['total_amt']) ? $item['total_amt'] : 0;
//                 $finalOutput[$key]['zone_type'] = 2;
//             }
//             $finalOutput[0]['total_cash_amt'] = number_format($totalCash, 2, '.', '');
//             $finalOutput[0]['total_upi_amt'] = number_format($totalUpi, 2, '.', '');
//             $finalOutput[0]['total_card_amt'] = number_format($totalCard, 2, '.', '');
//             $finalOutput[0]['total_neft_amt'] = number_format($totalNeft, 2, '.', '');
//             $finalOutput[0]['total_total_amt'] = number_format($totalTotal, 2, '.', '');

//             // if (isset($inc_category[2]) && $inc_category[2] == 'Consolidated') {
//                 $newArray = [
//                     "type" => "Consolidated",
//                     "billdate" => $finalOutput[0]['billdate'] ?? '',
//                     "area" => 'All',
//                     "zone_name" => $finalOutput[0]['zone_name'] ?? '',
//                     "total_cash_amt" => number_format($totalCash, 2, '.', '') ?? 0,
//                     "total_upi_amt" => number_format($totalUpi, 2, '.', '') ?? 0,
//                     "total_card_amt" => number_format($totalCard, 2, '.', '') ?? 0,
//                     "total_neft_amt" => number_format($totalNeft, 2, '.', '') ?? 0,
//                     "total_total_amt" => number_format($totalTotal, 2, '.', '') ?? 0,
//                     "zone_type" => $finalOutput[0]['zone_type'] ?? '',
//                 ];
//                         array_push($finalOutput, $newArray);
//                 // }

//             $zone_locations = $this->zoneQuery($state);
//             return response()->json(['data' => $finalOutput,'dropdown' => $zone_locations]);
//     } else {
//         dd($state);
//             preg_match("/tblzones\.name\s*=\s*'[^']*'/", $fitterremovedataall, $matches);
//         if (!empty($matches)) {
//             preg_match("/(tbl_income\.category)\s*=\s*'([^']+)'/", $fitterremovedataall, $inc_category);
//             if (isset($inc_category[2]) && $inc_category[2] != 'All' && $inc_category[2] != 'Consolidated') {
//               $finalOutput = $this->incomeCategory($inc_category,$finalOutput);
//             }
//             foreach ($finalOutput as $key =>$item) {
//                 $totalCash += isset($item['cash_amt']) ? $item['cash_amt'] : 0;
//                 $totalUpi += isset($item['upi_amt']) ? $item['upi_amt'] : 0;
//                 $totalCard += isset($item['card_amt']) ? $item['card_amt'] : 0;
//                 $totalNeft += isset($item['neft_amt']) ? $item['neft_amt'] : 0;
//                 $totalTotal += isset($item['total_amt']) ? $item['total_amt'] : 0;
//                 $finalOutput[$key]['zone_type'] = 2;
//             }
//              $finalOutput[0]['total_cash_amt'] = number_format($totalCash, 2, '.', '');
//             $finalOutput[0]['total_upi_amt'] = number_format($totalUpi, 2, '.', '');
//             $finalOutput[0]['total_card_amt'] = number_format($totalCard, 2, '.', '');
//             $finalOutput[0]['total_neft_amt'] = number_format($totalNeft, 2, '.', '');
//             $finalOutput[0]['total_total_amt'] = number_format($totalTotal, 2, '.', '');
//             // if (isset($inc_category[2]) && $inc_category[2] == 'Consolidated') {
//                 $newArray = [
//                     "type" => "Consolidated",
//                     "billdate" => $finalOutput[0]['billdate'] ?? '',
//                     "area" =>  'All',
//                     "zone_name" => $finalOutput[0]['zone_name'] ?? '',
//                      "total_cash_amt" => number_format($totalCash, 2, '.', '') ?? 0,
//                     "total_upi_amt" => number_format($totalUpi, 2, '.', '') ?? 0,
//                     "total_card_amt" => number_format($totalCard, 2, '.', '') ?? 0,
//                     "total_neft_amt" => number_format($totalNeft, 2, '.', '') ?? 0,
//                     "total_total_amt" => number_format($totalTotal, 2, '.', '') ?? 0,
//                     "zone_type" => $finalOutput[0]['zone_type'] ?? '',
//                 ];
//                 array_push($finalOutput, $newArray);
//         // }
//             $zone_locations = $this->zoneQuery($state);
//             return response()->json(['data' => $finalOutput,'dropdown' => $zone_locations]);
//         }else{
//             preg_match("/='(.*?)'/", $fitterremovedataall, $matches);
//             $ilocation = $matches[1];
//             if (strpos($fitterremovedataall, "tblzones.name=") !== false){
//                 $zone_locaton =TblLocationModel::select('zone_id')->where('name',$ilocation)->first();
//                 $zone_location =TblLocationModel::where('zone_id',$zone_locaton->zone_id)->get();
//            }else{
//                $zone_location = TblLocationModel::orderBy('name', 'asc')->get();
//            }
//            preg_match("/(tbl_income\.category)\s*=\s*'([^']+)'/", $fitterremovedataall, $inc_category);
//            $finalOutput = $this->incomeCategory($inc_category,$finalOutput);

//             foreach ($finalOutput as $key =>$item) {
//                 $totalCash += isset($item['cash_amt']) ? $item['cash_amt'] : 0;
//                 $totalUpi += isset($item['upi_amt']) ? $item['upi_amt'] : 0;
//                 $totalCard += isset($item['card_amt']) ? $item['card_amt'] : 0;
//                 $totalNeft += isset($item['neft_amt']) ? $item['neft_amt'] : 0;
//                 $totalTotal += isset($item['total_amt']) ? $item['total_amt'] : 0;
//                 $finalOutput[$key]['zone_type'] = 2;
//             }
//             $finalOutput[0]['total_cash_amt'] = number_format($totalCash, 2, '.', '');
//             $finalOutput[0]['total_upi_amt'] = number_format($totalUpi, 2, '.', '');
//             $finalOutput[0]['total_card_amt'] = number_format($totalCard, 2, '.', '');
//             $finalOutput[0]['total_neft_amt'] = number_format($totalNeft, 2, '.', '');
//             $finalOutput[0]['total_total_amt'] = number_format($totalTotal, 2, '.', '');

//             // if (isset($inc_category[2]) && $inc_category[2] == 'Consolidated') {
//                 $newArray = [
//                     "type" => "Consolidated",
//                     "billdate" => $finalOutput[0]['billdate'] ?? '',
//                     "area" =>  'All',
//                     "zone_name" => $finalOutput[0]['zone_name'] ?? '',
//                      "total_cash_amt" => number_format($totalCash, 2, '.', '') ?? 0,
//                     "total_upi_amt" => number_format($totalUpi, 2, '.', '') ?? 0,
//                     "total_card_amt" => number_format($totalCard, 2, '.', '') ?? 0,
//                     "total_neft_amt" => number_format($totalNeft, 2, '.', '') ?? 0,
//                     "total_total_amt" => number_format($totalTotal, 2, '.', '') ?? 0,
//                     "zone_type" => $finalOutput[0]['zone_type'] ?? '',
//                 ];
//                 array_push($finalOutput, $newArray);
//         // }

//             return response()->json(['data' => $finalOutput,'dropdown' => $zone_location]);
//         }
//     }
// }
private function incomeBranchDateFiltr($fitterremovedataall, $moredatefittervale, $apistatus, $statusid)
{
    // dd($fitterremovedataall);
    $user = auth()->user();
    $approver = DB::table('cancel_bill_approver')->where('user_id', $user->id)->first();

    // 1. Parse date range
    $dates = explode(' - ', $moredatefittervale);
    $startDate = $dates[0] ?? '';
    $endDate = $dates[1] ?? '';
    $start_date = Carbon::createFromFormat('d/m/Y', $startDate);
    $end_date = Carbon::createFromFormat('d/m/Y', $endDate);
    $dateRangeStr = $startDate . ' - ' . $endDate;

    // 2. Generate all dates in range
    $allDates = [];
    $allDatesFormatted = [];
    $currentDate = clone $start_date;

    while ($currentDate <= $end_date) {
        $dateFormatted = $currentDate->format('Ymd');
        $dateKey = $currentDate->format('Y-m-d');
        $allDates[] = $dateFormatted;
        $allDatesFormatted[] = $dateKey;
        $currentDate->addDay();
    }

    // 3. Get data from API
    $url = 'https://mocdoc.in/api/get/billlist/draravinds-ivf';
    $incomeArray = $this->incomereportAPI($moredatefittervale, $fitterremovedataall, $apistatus, $statusid, $url, $allDates);
    // dd($incomeArray);
    // 4. Process API response
    $finalOutput = [];
    if (array_key_exists('status_id', $incomeArray)) {
        unset($incomeArray['status_id']);
        $finalOutput = $this->incomeZoneArray($incomeArray);
    } else {
        $finalOutput = $this->incomeFinalArray($incomeArray);
    }
    // dd($finalOutput);
    // 5. Apply category filter if exists
    preg_match("/(tbl_income\.category)\s*=\s*'([^']+)'/", $fitterremovedataall, $inc_category);
    if (!empty($inc_category) && isset($inc_category[2])) {
        $finalOutput = $this->incomeCategory($inc_category, $finalOutput);
    }

    // 6. Get state/zone information
    preg_match("/'([^']+)'/", $fitterremovedataall, $matches);
    $state = $matches[1] ?? '';

    // 7. Check filter types
    $isZoneFilter = strpos($fitterremovedataall, "tblzones.name=") !== false;
    $isBranchFilter = strpos($fitterremovedataall, "tbl_locations.name=") !== false;
    // dd($fitterremovedataall, $isZoneFilter, $isBranchFilter, $state);
    // 8. Get selected branches based on filter
    $selectedBranches = $this->getSelectedBranches($fitterremovedataall, $isZoneFilter, $isBranchFilter, $state);
    // dd($selectedBranches);
    // 9. Get zone locations for dropdown
    $zone_locations = $this->zoneQuery($state);
    // dd($zone_locations);
    // 10. Prepare data based on the 4 cases
    $resultData = [];
    if (empty($finalOutput)) {
        // dd(1);
        // Case 1: No data from API
        $resultData = $this->handleNoDataCase($selectedBranches, $zone_locations, $state, $dateRangeStr, $allDatesFormatted);
    } elseif ($isZoneFilter && $isBranchFilter) {
        // Case 2: Both zone and branch filter (specific branch within a zone)
        $resultData = $this->handleZoneBranchFilter($finalOutput, $selectedBranches, $zone_locations, $state, $dateRangeStr, $allDatesFormatted);
    } elseif ($isZoneFilter) {
        // Case 3: Only zone filter (show all branches in zone)
        $resultData = $this->handleZoneOnlyFilter($finalOutput, $zone_locations, $state, $dateRangeStr, $allDatesFormatted);
    } else {
        // Case 4: Specific branch filter (not zone-based)
        $resultData = $this->handleBranchOnlyFilter($finalOutput, $selectedBranches, $zone_locations, $state, $dateRangeStr, $allDatesFormatted);
    }
    // dd($resultData);
    return response()->json([
        'data' => $resultData,
        'dropdown' => $zone_locations,
        'isApprover' => (bool) $approver
    ]);
}

/**
 * Helper: Get selected branches from filter string
 */
private function getSelectedBranches($filterString, $isZoneFilter, $isBranchFilter, $state)
{
    $selectedBranches = [];

    if ($isBranchFilter && preg_match("/tbl_locations\.name\s*=\s*'([^']*)'/", $filterString, $branchMatches)) {
        $selectedBranches = array_map('trim', explode(',', $branchMatches[1] ?? ''));
    } elseif ($isZoneFilter) {
        // Get all branches in the zone
        $zone_locations = $this->zoneQuery($state);
        $selectedBranches = $zone_locations->pluck('name')->toArray();
    }

    return $selectedBranches;
}

/**
 * Helper: Create zero data entry for a branch
 */
private function createZeroBranchData($branchName, $zoneName, $date)
{
    return [
        'type' => 'Branch',
        'billdate' => $date,
        'area' => $branchName,
        'zone_name' => $zoneName,
        'cash_amt' => '0.00',
        'upi_amt' => '0.00',
        'card_amt' => '0.00',
        'neft_amt' => '0.00',
        'other_amt' => '0.00',
        'total_amt' => '0.00',
        'zone_type' => 2,
    ];
}

/**
 * Case 1: No data from API
 */
private function handleNoDataCase($selectedBranches, $zone_locations, $state, $dateRangeStr, $allDatesFormatted)
{
    $result = [];

    foreach ($selectedBranches as $branchName) {
        $branchData = $zone_locations->firstWhere('name', $branchName);

        // Create entry for each date
        foreach ($allDatesFormatted as $date) {
            $result[] = $this->createZeroBranchData(
                $branchName,
                $branchData->zone_name ?? $state,
                $date
            );
        }
    }
    return $result;
}

/**
 * Case 2: Both zone and branch filter
 */
private function handleZoneBranchFilter($finalOutput, $selectedBranches, $zone_locations, $state, $dateRangeStr, $allDatesFormatted)
{
    return $this->prepareDateWiseData($finalOutput, $selectedBranches, $zone_locations, $state, $allDatesFormatted);
}

/**
 * Case 3: Only zone filter
 */
private function handleZoneOnlyFilter($finalOutput, $zone_locations, $state, $dateRangeStr, $allDatesFormatted)
{
    $selectedBranches = $zone_locations->pluck('name')->toArray();
    return $this->prepareDateWiseData($finalOutput, $selectedBranches, $zone_locations, $state, $allDatesFormatted);
}

/**
 * Case 4: Specific branch filter
 */
private function handleBranchOnlyFilter($finalOutput, $selectedBranches, $zone_locations, $state, $dateRangeStr, $allDatesFormatted)
{
    return $this->prepareDateWiseData($finalOutput, $selectedBranches, $zone_locations, $state, $allDatesFormatted);
}

/**
 * Core function to prepare date-wise data for all cases
 */
// private function prepareDateWiseData($apiData, $selectedBranches, $zone_locations, $state, $allDatesFormatted)
// {
//     // 1. Index API data by date and branch for quick lookup
//    $apiDataIndex = [];

//     foreach ($apiData as $item) {

//         $branchName = $item['area'] ?? $item['zone_name'] ?? '';
//         $billDate   = $item['billdate'] ?? '';

//         if (!$branchName || !$billDate) {
//             continue;
//         }

//         // Handle YYYYMMDD format safely
//         $dateKey = strlen($billDate) === 8
//             ? DateTime::createFromFormat('Ymd', $billDate)->format('Y-m-d')
//             : date('Y-m-d', strtotime($billDate));

//         // Initialize if not exists
//         if (!isset($apiDataIndex[$dateKey][$branchName])) {
//             $apiDataIndex[$dateKey][$branchName] = [
//                 'date'       => $dateKey,
//                 'branch'     => $branchName,
//                 'total_amt'  => 0,
//                 'cash_amt'   => 0,
//                 'upi_amt'    => 0,
//                 'card_amt'   => 0,
//                 'neft_amt'   => 0,
//                 'other_amt'   => 0,
//             ];
//         }

//         // 🔹 SUM VALUES
//         $apiDataIndex[$dateKey][$branchName]['total_amt'] += (float) ($item['total_amt'] ?? 0);
//         $apiDataIndex[$dateKey][$branchName]['cash_amt']  += (float) ($item['cash_amt'] ?? 0);
//         $apiDataIndex[$dateKey][$branchName]['upi_amt']   += (float) ($item['upi_amt'] ?? 0);
//         $apiDataIndex[$dateKey][$branchName]['card_amt']  += (float) ($item['card_amt'] ?? 0);
//         $apiDataIndex[$dateKey][$branchName]['neft_amt']  += (float) ($item['neft_amt'] ?? 0);
//         $apiDataIndex[$dateKey][$branchName]['other_amt'] += (float) (
//                                                                         isset($item['cheque_amt']) ? $item['cheque_amt'] :
//                                                                         (isset($item['dd_amt']) ? $item['dd_amt'] :
//                                                                         (isset($item['credit_amt']) ? $item['credit_amt'] : 0))
//                                                                     );
//     }


//     // 2. Prepare result with all dates and branches
//     $result = [];
//     $totals = ['cash' => 0, 'upi' => 0, 'card' => 0, 'neft' => 0, 'total' => 0];

//     foreach ($allDatesFormatted as $date) {
//         foreach ($selectedBranches as $branchName) {
//             $branchData = $zone_locations->firstWhere('name', $branchName);
//             $zoneName = $branchData->zone_name ?? $state;

//             // Check if we have API data for this date and branch
//             if (isset($apiDataIndex[$date][$branchName])) {
//                 $apiItem = $apiDataIndex[$date][$branchName];
//                 $otherAmt =
//                     (float)($apiItem['cheque_amt'] ?? 0) +
//                     (float)($apiItem['dd_amt'] ?? 0) +
//                     (float)($apiItem['credit_amt'] ?? 0);

//                 $entry = [
//                     'type' => 'Branch',
//                     'billdate' => $date,
//                     'area' => $branchName,
//                     'zone_name' => $zoneName,
//                     'cash_amt' => $apiItem['cash_amt'] ?? '0.00',
//                     'upi_amt' => $apiItem['upi_amt'] ?? '0.00',
//                     'card_amt' => $apiItem['card_amt'] ?? '0.00',
//                     'neft_amt' => $apiItem['neft_amt'] ?? '0.00',
//                     'other_amt'  => number_format($otherAmt, 2, '.', ''),
//                     'total_amt' => $apiItem['total_amt'] ?? '0.00',
//                     'zone_type' => 2,
//                 ];
//             } else {
//                 // No data for this date/branch
//                 $entry = $this->createZeroBranchData($branchName, $zoneName, $date);
//             }

//             // Add to result
//             $result[] = $entry;

//             // Update totals
//             $totals['cash'] += floatval($entry['cash_amt']);
//             $totals['upi'] += floatval($entry['upi_amt']);
//             $totals['card'] += floatval($entry['card_amt']);
//             $totals['neft'] += floatval($entry['neft_amt']);
//             $totals['total'] += floatval($entry['total_amt']);
//         }
//     }

//     // 3. Add consolidated row
//     if (!empty($result)) {
//         $firstDate = $allDatesFormatted[0];
//         $lastDate = end($allDatesFormatted);

//         $result[] = [
//             "type" => "Consolidated",
//             "billdate" => $firstDate . ' - ' . $lastDate,
//             "area" => 'All',
//             "zone_name" => $state,
//             "total_cash_amt" => number_format($totals['cash'], 2, '.', ''),
//             "total_upi_amt" => number_format($totals['upi'], 2, '.', ''),
//             "total_card_amt" => number_format($totals['card'], 2, '.', ''),
//             "total_neft_amt" => number_format($totals['neft'], 2, '.', ''),
//             "total_total_amt" => number_format($totals['total'], 2, '.', ''),
//             "zone_type" => 2,
//         ];
//     }

//     return $result;
// }
private function prepareDateWiseData(
    $apiData,
    $selectedBranches,
    $zone_locations,
    $state,
    $allDatesFormatted
) {
    /* ----------------------------------------------------
       1. Index API data by date + branch
    ---------------------------------------------------- */
    $apiDataIndex = [];

    foreach ($apiData as $item) {

        $branchName = $item['area'] ?? $item['zone_name'] ?? '';
        $billDate   = $item['billdate'] ?? '';

        if (!$branchName || !$billDate) {
            continue;
        }

        // Normalize date
        $dateKey = strlen($billDate) === 8
            ? DateTime::createFromFormat('Ymd', $billDate)->format('Y-m-d')
            : date('Y-m-d', strtotime($billDate));

        if (!isset($apiDataIndex[$dateKey][$branchName])) {
            $apiDataIndex[$dateKey][$branchName] = [
                'date'        => $dateKey,
                'branch'      => $branchName,
                'total_amt'   => 0,
                'cash_amt'    => 0,
                'upi_amt'     => 0,
                'card_amt'    => 0,
                'neft_amt'    => 0,
                'cheque_amt'  => 0,
                'dd_amt'      => 0,
                'credit_amt'  => 0,
                'other_amt'   => 0,
            ];
        }

        // Normalize values safely
        $cash   = (float)($item['cash_amt']   ?? 0);
        $upi    = (float)($item['upi_amt']    ?? 0);
        $card   = (float)($item['card_amt']   ?? 0);
        $neft   = (float)($item['neft_amt']   ?? 0);
        $cheque = (float)($item['cheque_amt'] ?? 0);
        $dd     = (float)($item['dd_amt']     ?? 0);
        $credit = (float)($item['credit_amt'] ?? 0);
        $total  = (float)($item['total_amt']  ?? 0);

        // Sum values
        $apiDataIndex[$dateKey][$branchName]['cash_amt']   += $cash;
        $apiDataIndex[$dateKey][$branchName]['upi_amt']    += $upi;
        $apiDataIndex[$dateKey][$branchName]['card_amt']   += $card;
        $apiDataIndex[$dateKey][$branchName]['neft_amt']   += $neft;
        $apiDataIndex[$dateKey][$branchName]['cheque_amt'] += $cheque;
        $apiDataIndex[$dateKey][$branchName]['dd_amt']     += $dd;
        $apiDataIndex[$dateKey][$branchName]['credit_amt'] += $credit;
        $apiDataIndex[$dateKey][$branchName]['total_amt']  += $total;

        // ✅ FINAL other_amt = cheque + dd + credit + neft
        $apiDataIndex[$dateKey][$branchName]['other_amt']
            += ($cheque + $dd + $credit);
    }

    /* ----------------------------------------------------
       2. Prepare result rows
    ---------------------------------------------------- */
    $result = [];
    $totals = [
        'cash'  => 0,
        'upi'   => 0,
        'card'  => 0,
        'neft'  => 0,
        'other' => 0,
        'total' => 0,
    ];

    foreach ($allDatesFormatted as $date) {
        foreach ($selectedBranches as $branchName) {

            $branchData = $zone_locations->firstWhere('name', $branchName);
            $zoneName   = $branchData->zone_name ?? $state;

            if (isset($apiDataIndex[$date][$branchName])) {
                $apiItem = $apiDataIndex[$date][$branchName];

                $entry = [
                    'type'       => 'Branch',
                    'billdate'   => $date,
                    'area'       => $branchName,
                    'zone_name'  => $zoneName,
                    'cash_amt'   => number_format($apiItem['cash_amt'], 2, '.', ''),
                    'upi_amt'    => number_format($apiItem['upi_amt'], 2, '.', ''),
                    'card_amt'   => number_format($apiItem['card_amt'], 2, '.', ''),
                    'neft_amt'   => number_format($apiItem['neft_amt'], 2, '.', ''),
                    'other_amt'  => number_format($apiItem['other_amt'], 2, '.', ''),
                    'total_amt'  => number_format($apiItem['total_amt'], 2, '.', ''),
                    'zone_type'  => 2,
                ];
            } else {
                $entry = $this->createZeroBranchData($branchName, $zoneName, $date);
            }

            $result[] = $entry;

            // Update totals
            $totals['cash']  += (float)$entry['cash_amt'];
            $totals['upi']   += (float)$entry['upi_amt'];
            $totals['card']  += (float)$entry['card_amt'];
            $totals['neft']  += (float)$entry['neft_amt'];
            $totals['other'] += (float)$entry['other_amt'];
            $totals['total'] += (float)$entry['total_amt'];
        }
    }

    /* ----------------------------------------------------
       3. Consolidated row
    ---------------------------------------------------- */
    if (!empty($result)) {
        $firstDate = $allDatesFormatted[0];
        $lastDate  = end($allDatesFormatted);

        $result[] = [
            'type'              => 'Consolidated',
            'billdate'          => $firstDate . ' - ' . $lastDate,
            'area'              => 'All',
            'zone_name'         => $state,
            'total_cash_amt'    => number_format($totals['cash'], 2, '.', ''),
            'total_upi_amt'     => number_format($totals['upi'], 2, '.', ''),
            'total_card_amt'    => number_format($totals['card'], 2, '.', ''),
            'total_neft_amt'    => number_format($totals['neft'], 2, '.', ''),
            'total_other_amt'   => number_format($totals['other'], 2, '.', ''),
            'total_total_amt'   => number_format($totals['total'], 2, '.', ''),
            'zone_type'         => 2,
        ];
    }

    return $result;
}

private function incomeFinalArray($input1){
            $output1 = [];
            $final = [];
            foreach ($input1 as $entry) {
                $key = $entry['type'] . '|' . $entry['area'] . '|' . $entry['billdate'];
                if (!isset($output1[$key])) {
                    $output1[$key] = [
                        'type' => $entry['type'],
                        'zone_name' => $entry['zone_name'],
                        'billdate' => $entry['billdate'],
                        'branch' => $entry['branch'],
                        'area' => $entry['area'],
                        'branch_id' => $entry['branch_id'],
                        'zone_id' => $entry['zone_id'],
                        'total_amt' => 0
                    ];
                }
                $paymentKey = strtolower($entry['paymenttype']) . '_amt';
                if (isset($output1[$key][$paymentKey])) {
                    $output1[$key][$paymentKey] +=  number_format($entry['amt'], 2, '.', '');
                } else {
                    $output1[$key][$paymentKey] = number_format($entry['amt'], 2, '.', '');
                }
                $output1[$key]['total_amt'] += number_format($entry['amt'], 2, '.', '');
            }
            $final = array_values($output1);
                // echo "<pre>31111";print_r($final);exit;
            return $final;
}

//  private function zoneQuery($city){
//                 $zone_names = array_map('trim', explode(',', $city));
//                 $zone_ids = TblZonesModel::whereIn('name', $zone_names)->pluck('id');
//                 $zone_locations = TblLocationModel::whereIn('zone_id', $zone_ids)
//                     ->where('status', 1)
//                     ->orderBy('name', 'asc')
//                     ->get();
//                 return $zone_locations;
//     }
    private function zoneQuery($city)
    {
        $admin = auth()->user();
        if ($admin->access_limits == 1) {

            $zone_names = array_map('trim', explode(',', $city));

            $zone_ids = TblZonesModel::whereIn('name', $zone_names)
                ->pluck('id');

            return TblLocationModel::whereIn('zone_id', $zone_ids)
                ->where('status', 1)
                ->orderBy('name', 'asc')
                ->get();
        }

        // Access limit 2 → Zone based restriction
        if ($admin->access_limits == 2) {

            return TblLocationModel::where('zone_id', $admin->zone_id)
                ->where('status', 1)
                ->orderBy('name', 'asc')
                ->get();
        }

        // Access limit 3 → Branch only
        return TblLocationModel::where('id', $admin->branch_id)
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
    }


    private function incomeCategory($inc_category,$finalOutput){
        if (!empty($inc_category) && isset($inc_category[2]) && $inc_category[2] != 'Consolidated' && $inc_category[2] != 'All') {
            $filterType = $inc_category[2];
            $filtered = array_filter($finalOutput, function($itm) use ($filterType) {
                return $itm['type'] === $filterType;
            });
            $finalOutput = array_values($filtered);
        }
        return $finalOutput;
    }

    private function saveCurlData($checkin, $location_id,$apistatus,$area,$zonename)
    {
            $branch_name = TblLocationModel::select('id')->where('name',$area)->first();
            $branch_id = $branch_name->id;
            $map_zone = $this->zoneMapping();

        if($apistatus == 'checkinreport'){
            $locationKey = $checkin['entitylocation'];
            $zoneId = $map_zone[$locationKey] ?? null;
            $cdate = !empty($checkin['date'])
                ? Carbon::createFromFormat('Ymd', $checkin['date'])->format('Y-m-d')
                : null;
            $dob = !empty($checkin['patient']['dob'])
                    ? Carbon::createFromFormat('Ymd', $checkin['patient']['dob'])->format('Y-m-d')
                    : null;
            if(!empty($checkin['consultingdr_name'])) {
                $doctor_name =  strip_tags($checkin['consultingdr_name']) ?? '';
                $doctor_name = preg_split('/\sM\.B\.B\.S/', $doctor_name);
                $doctor_name = trim($doctor_name[0]);
            }
            if (!empty($checkin['patient']['age'])) {
                if (preg_match('/(\d+)\s*Years/', $checkin['patient']['age'], $matches)) {
                    $patient_age = $matches[1];
                } else {
                    $patient_age = null; // or handle the case when age isn't in the expected format
                }
            }
            return [
                    'checkin_date' => $cdate ?? '',
                    'doctor_name' => $doctor_name ?? '',
                    'patient_age' => $patient_age ?? '',
                    'zone_id' => $zoneId ?? '',
                    'branch_id' => $branch_id ?? '',
                    'patient_area' => $area ?? '',
                    'checkinkey' => $checkin['checkinkey'] ?? '',
                    'speciality' => $checkin['speciality'] ?? '',
                    'name' => $checkin['patient']['name'] ?? '',
                    'mobile' => $checkin['patient']['mobile'] ?? '',
                    'age' => $checkin['patient']['age'] ?? '',
                    'phid' => $checkin['patient']['phid'] ?? '',
                    'title' => $checkin['patient']['title'] ?? '',
                    'gender' => $checkin['patient']['gender'] ?? '',
                    'lname' => $checkin['patient']['lname'] ?? '',
                    'ptsource' => $checkin['patient']['ptsource'] ?? '',
                    'dob' => $dob ?? '',
                    'familyid' => $checkin['patient']['familyid'] ?? '',
                    'isdcode' => $checkin['patient']['isdcode'] ?? '',
                    'address2' => $checkin['patient']['address']['address2'] ?? '',
                    'relationship' => $checkin['patient']['address']['relationship'] ?? '',
                    'street' => $checkin['patient']['address']['street'] ?? '',
                    'area' => $checkin['patient']['address']['area'] ?? '',
                    'landmark' => $checkin['patient']['address']['landmark'] ?? '',
                    'city' => $checkin['patient']['address']['city'] ?? '',
                    'state' => $checkin['patient']['address']['state'] ?? '',
                    'zip' => $checkin['patient']['address']['zip'] ?? '',
                    'country' => $checkin['patient']['address']['country'] ?? '',
                    'created_att' => $checkin['created_at'] ?? '',
                    'updated_att' => $checkin['updated_att'] ?? '',
                    'co_user_dt' => $checkin['co_user_dt'] ?? '',
                    'co_user_name' => $checkin['co_user_name'] ?? '',
                    'co_user' => $checkin['co_user'] ?? '',
                    'credit_provider' => $checkin['credit_provider'] ?? '',
                    'natureofvisit' => $checkin['natureofvisit'] ?? '',
                    'unregistered_dr' => $checkin['unregistered_dr'] ?? '',
                    'referredbykey' => $checkin['referredbykey'] ?? '',
                    'referred_by' => $checkin['referred_by'] ?? '',
                    'token' => $checkin['token'] ?? '',
                    'createdby' => $checkin['createdby'] ?? '',
                    'createdby_name' => $checkin['createdby_name'] ?? '',
                    'start' => $checkin['start'] ?? '',
                    'bookeddr' => $checkin['bookeddr'] ?? '',
                    'bookeddr_name' => $checkin['bookeddr_name'] ?? '',
                    'consultingdr' => $checkin['consultingdr'] ?? '',
                    'consultingdr_name' => $checkin['consultingdr_name'] ?? '',
                    'entitykey' => $checkin['entitykey'] ?? '',
                    'apptkey' => $checkin['apptkey'] ?? '',
                    'entitylocation' => $checkin['entitylocation'] ?? '',
                    'branch' => $checkin['branch'] ?? '',
                    'opno' => $checkin['opno'] ?? '',
                    'hadmlc' => $checkin['hadmlc'] ?? '',
                    'hadfood' => $checkin['hadfood'] ?? '',
                    'dr_dept' => $checkin['dr_dept'] ?? '',
                    'type' => $checkin['type'] ?? '',
                    'purpose' => $checkin['purpose'] ?? '',
                    'created_at' => now() ?? '',
                ];
        }else if($apistatus == 'registrationreport'){
                $dob = !empty($checkin['dob'])
                    ? Carbon::createFromFormat('Ymd', $checkin['dob'])->format('Y-m-d')
                    : null;
                $registrationdate = !empty($checkin['registrationdate'])
                    ? Carbon::createFromFormat('Ymd', $checkin['registrationdate'])->format('Y-m-d')
                    : null;
                return [
                    'mobile' => $checkin['mobile'] ?? '',
                    'age' => $checkin['agey'] ?? '',
                    'dob' => $dob ?? '',
                    'name' => $checkin['name'] ?? '',
                    'phid' => $checkin['phid'] ?? '',
                    'created_at' => $checkin['created_at'] ?? '',
                    'gender' => $checkin['gender'] ?? '',
                    'registrationdate' => $registrationdate ?? '',
                ];
        }

        else{
            if($checkin['type'] == 'Advance'  && $checkin['billtype'] == 'I/P'){
               $billdate = isset($checkin['receivedat']) ? substr($checkin['receivedat'], 0, 8) : '';
               $billtype = 'I/P - Income';
            }else{
                $billdate = isset($checkin['billdate']) ? substr($checkin['billdate'], 0, 8) : '';
                $billtype = $checkin['type'] ?? '';
            }
                return [
                    'type' => $billtype,
                    'zone_name' => $zonename,
                    'billdate' => $billdate,
                    'billtype' => $checkin['billtype'] ?? '',
                    'paymenttype' => $checkin['paymenttype'] ?? '',
                    'branch' => $location_id,
                    'area' => $area,
                    'branch_id' => $branch_id ?? '',
                    'zone_id' => $map_zone[$location_id] ?? '',
                    'amt' => $checkin['amt'] ?? 0,
                ];
        }
    }



   public function getart(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();

        $locations = TblLocationModel::orderBy('name', 'asc')->get();
        // dd($locations);
        return view('superadmin.art_module', compact('admin', 'locations'));
    }

    public function profile(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.profile', compact('admin'));

    }

    public function outcome(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.outcome', compact('admin'));

    }


    public function embryology(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryology', compact('admin'));

    }
    public function profileOdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.profile_odicsi', compact('admin'));

    }
    public function embryoTransfer(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryo_transfer', compact('admin'));

    }

    public function ovarian_stimulation(){
        // dd('hi');
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        $locations = TblLocationModel::orderBy('name', 'asc')->get();
        return view('superadmin.ovarian_stimulation', compact('admin', 'locations'));

    }

     public function eggPickup(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.egg_pickup', compact('admin'));

    }
    public function embryoFreezing(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryo_freezing', compact('admin'));

    }
    public function investigation(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.investigation', compact('admin'));

    }
    public function storeInvestigation(Request $request){
        // dd($request->all());
        $store=new InvestigationsModel();
        $store->usg=$request->usg;
        $store->hsg=$request->hsg;
        $store->hysteroscopy=$request->hysteroscopy;
        $store->trans_vaginal_sonography=$request->trans_vaginal_sonography;
        $store->hysterosalpingography=$request->hysterosalpingography;
        $store->others=$request->others;
        $store->save();
        // dd($store);

        $store_female_blood=new InvestFemaleFactorsBloodGroupModel();
        $store_female_blood->investigation_id=$store->id;
        $store_female_blood->female_blood_group=$request->female_blood_group;
        $store_female_blood->female_hb=$request->female_hb;
        $store_female_blood->female_anti_tpo=$request->female_anti_tpo;
        $store_female_blood->female_rbs=$request->female_rbs;
        $store_female_blood->female_acl=$request->female_acl;
        $store_female_blood->female_hiv=$request->female_hiv;
        $store_female_blood->female_la=$request->female_la;
        $store_female_blood->female_hbsag=$request->female_hbsag;
        $store_female_blood->female_aprtt=$request->female_aprtt;
        $store_female_blood->female_tsh=$request->female_tsh;
        $store_female_blood->female_ana=$request->female_ana;
        $store_female_blood->female_prl=$request->female_prl;
        $store_female_blood->karyo_type=$request->karyo_type;
        $store_female_blood->female_blood_urea=$request->female_blood_urea;
        $store_female_blood->female_fsh=$request->female_fsh;
        $store_female_blood->female_serum=$request->female_serum;
        $store_female_blood->female_lh=$request->female_lh;
        $store_female_blood->female_creatine=$request->female_creatine;
        $store_female_blood->female_testosterone=$request->female_testosterone;
        $store_female_blood->female_fbs=$request->female_fbs;
        $store_female_blood->female_hba1c=$request->female_hba1c;
        $store_female_blood->female_estradiol=$request->female_estradiol;
        $store_female_blood->female_urine_routine=$request->female_urine_routine;
        $store_female_blood->female_haemoglobin=$request->female_haemoglobin;
        $store_female_blood->female_pcv=$request->female_pcv;
        $store_female_blood->female_ft4=$request->female_ft4;
        $store_female_blood->female_platelet_count=$request->female_platelet_count;
        $store_female_blood->female_prolactin=$request->female_prolactin;
        $store_female_blood->female_gtt=$request->female_gtt;
        $store_female_blood->female_insulin=$request->female_insulin;
        $store_female_blood->female_bun=$request->female_bun;
        $store_female_blood->female_shbg=$request->female_shbg;
        $store_female_blood->female_creatinine=$request->female_creatinine;
        $store_female_blood->female_dheas=$request->female_dheas;
        $store_female_blood->female_17_ohp=$request->female_17_ohp;
        $store_female_blood->female_cortisol=$request->female_cortisol;
        $store_female_blood->female_hcv=$request->female_hcv;
        $store_female_blood->female_karyotyping=$request->female_karyotyping;
        $store_female_blood->female_vitd3=$request->female_vitd3;
        $store_female_blood->female_ldl=$request->female_ldl;
        $store_female_blood->female_lac=$request->female_lac;
        $store_female_blood->female_triglycerides=$request->female_triglycerides;
        $store_female_blood->female_anti_β2gpab=$request->female_anti_β2gpab;
        $store_female_blood->female_chlamydia=$request->female_chlamydia;
        $store_female_blood->female_rubella=$request->female_rubella;
        $store_female_blood->female_other=$request->female_other;
        $store_female_blood->female_ppbs=$request->female_ppbs;
        $store_female_blood->female_diagnosis=$request->female_diagnosis;
        $store_female_blood->female_trial_et=$request->female_trial_et;
        $store_female_blood->save();


        $store_investhystero=new InvestHysteroToPodModel();
        $store_investhystero->investigation_id=$store->id;
        $store_investhystero->female_contour=$request->female_contour;
        $store_investhystero->female_cavity=$request->female_cavity;
        $store_investhystero->female_right_tube=$request->female_right_tube;
        $store_investhystero->female_left_tube=$request->female_left_tube;
        $store_investhystero->female_uterus_position=$request->female_uterus_position;
        $store_investhystero->female_uterus_size=$request->female_uterus_size;
        $store_investhystero->female_tube_rt=$request->female_tube_rt;
        $store_investhystero->female_tube_lt=$request->female_tube_lt;
        $store_investhystero->female_ovaries_rt=$request->female_ovaries_rt;
        $store_investhystero->female_ovaries_lt=$request->female_ovaries_lt;
        $store_investhystero->female_ovarian_drilling_done=$request->female_ovarian_drilling_done;
        $store_investhystero->female_chromotubation_rt=$request->female_chromotubation_rt;
        $store_investhystero->female_chromotubation_lt=$request->female_chromotubation_lt;
        $store_investhystero->female_endometriosis=$request->female_endometriosis;
        $store_investhystero->female_adhesions=$request->female_adhesions;
        $store_investhystero->female_tb=$request->female_tb;
        $store_investhystero->female_pid=$request->female_pid;
        $store_investhystero->female_upper_abdomen=$request->female_upper_abdomen;
        $store_investhystero->save();


        $store_investUltrasongram=new InvestUltrasongramToHysteroscopytModel();
        $store_investUltrasongram->investigation_id=$store->id;
        $store_investUltrasongram->female_sono_uterus_size=$request->female_sono_uterus_size  ;
        $store_investUltrasongram->female_endometrium=$request->female_endometrium  ;
        $store_investUltrasongram->female_myometrium=$request->female_myometrium;
        $store_investUltrasongram->female_right_ovary_volume=$request->female_right_ovary_volume;
        $store_investUltrasongram->female_left_ovary_volume=$request->female_left_ovary_volume;
        $store_investUltrasongram->female_right_ovary_fnpo=$request->female_right_ovary_fnpo;
        $store_investUltrasongram->female_left_ovary_fnpo=$request->female_left_ovary_fnpo;
        $store_investUltrasongram->female_right_ovary_stromal_ri=$request->female_right_ovary_stromal_ri;
        $store_investUltrasongram->female_left_ovary_stromal_ri=$request->female_left_ovary_stromal_ri;
        $store_investUltrasongram->female_right_ovary_stromal_psv=$request->female_right_ovary_stromal_psv;
        $store_investUltrasongram->female_left_ovary_stromal_psv=$request->female_left_ovary_stromal_psv;
        $store_investUltrasongram->female_right_ovary_accessibility=$request->female_right_ovary_accessibility;
        $store_investUltrasongram->female_left_ovary_accessibility=$request->female_left_ovary_accessibility;
        $store_investUltrasongram->female_cannulation=$request->female_cannulation;
        $store_investUltrasongram->female_cervical_canal=$request->female_cervical_canal;
        $store_investUltrasongram->female_internal_os=$request->female_internal_os;
        $store_investUltrasongram->female_cervix_dilated=$request->female_cervix_dilated;
        $store_investUltrasongram->female_uterine_cavity=$request->female_uterine_cavity;
        $store_investUltrasongram->female_uterine_cavity_abnormal=$request->female_uterine_cavity_abnormal;
        $store_investUltrasongram->female_ostia_right=$request->female_ostia_right;
        $store_investUltrasongram->female_ostia_left=$request->female_ostia_left;
        $store_investUltrasongram->save();

        // dd($store_female_blood);

        $store_investEndometrialbiopsy=new InvestEndometrialbiopsyToSonohysterogram();
        $store_investEndometrialbiopsy->investigation_id=$store->id;
        $store_investEndometrialbiopsy->female_hbe=$request->female_hbe;
        $store_investEndometrialbiopsy->female_tb_pcr=$request->female_tb_pcr;
        $store_investEndometrialbiopsy->female_culture_for_afb=$request->female_culture_for_afb;
        $store_investEndometrialbiopsy->female_laparotomy=$request->female_laparotomy;
        $store_investEndometrialbiopsy->female_sono_cannulation=$request->female_sono_cannulation;
        $store_investEndometrialbiopsy->female_sono_cavity_distension=$request->female_sono_cavity_distension;
        $store_investEndometrialbiopsy->female_sono_endometrium=$request->female_sono_endometrium;
        $store_investEndometrialbiopsy->female_sono_cavity=$request->female_sono_cavity;
        $store_investEndometrialbiopsy->female_uterocervical_angle=$request->female_uterocervical_angle;
        $store_investEndometrialbiopsy->female_sono_cavity_length=$request->female_sono_cavity_length;
        $store_investEndometrialbiopsy->female_sono_cervical_length=$request->female_sono_cervical_length;
        $store_investEndometrialbiopsy->female_sono_pod_fluid=$request->female_sono_pod_fluid;
        $store_investEndometrialbiopsy->save();

        $store_investAppearanceToMobility=new InvestAppearanceToMobility();
        $store_investAppearanceToMobility->investigation_id=$store->id;
        $store_investAppearanceToMobility->male_color=$request->male_color;
        $store_investAppearanceToMobility->male_appear_liquefaction=$request->male_appear_liquefaction;
        $store_investAppearanceToMobility->male_appear_semen_ph=$request->male_appear_semen_ph;
        $store_investAppearanceToMobility->male_appear_viscosity=$request->male_appear_viscosity;
        $store_investAppearanceToMobility->male_sperm_count_volume=$request->male_sperm_count_volume;
        $store_investAppearanceToMobility->male_sperm_count_concentration=$request->male_sperm_count_concentration;
        $store_investAppearanceToMobility->male_sperm_count_total_concentration=$request->male_sperm_count_total_concentration;
        $store_investAppearanceToMobility->male_sperm_count_ejaculate_volume=$request->male_sperm_count_ejaculate_volume;
        $store_investAppearanceToMobility->male_total_motility=$request->male_total_motility;
        $store_investAppearanceToMobility->male_progressive_motility=$request->male_progressive_motility;
        $store_investAppearanceToMobility->male_non_progressive_motility=$request->male_non_progressive_motility;
        $store_investAppearanceToMobility->male_immotile_mobility=$request->male_immotile_mobility;
        $store_investAppearanceToMobility->save();


        $store_investInvestiMorphology=new InvestiMorphology();
        $store_investInvestiMorphology->investigation_id=$store->id;
        $store_investInvestiMorphology->male_normal_forms=$request->male_normal_forms;
        $store_investInvestiMorphology->male_abnormal_forms=$request->male_abnormal_forms;
        $store_investInvestiMorphology->male_head_defects=$request->male_head_defects;
        $store_investInvestiMorphology->male_tail_defects=$request->male_tail_defects;
        $store_investInvestiMorphology->male_mid_piece_defects=$request->male_mid_piece_defects;
        $store_investInvestiMorphology->male_impression=$request->male_impression;
        $store_investInvestiMorphology->male_blood_group=$request->male_blood_group;
        $store_investInvestiMorphology->male_hb=$request->male_hb;
        $store_investInvestiMorphology->male_rbs=$request->male_rbs;
        $store_investInvestiMorphology->male_hbsag=$request->male_hbsag;
        $store_investInvestiMorphology->male_hiv=$request->male_hiv;
        $store_investInvestiMorphology->male_tsh=$request->male_tsh;
        $store_investInvestiMorphology->male_prl=$request->male_prl;
        $store_investInvestiMorphology->male_fsh=$request->male_fsh;
        $store_investInvestiMorphology->male_lh=$request->male_lh;
        $store_investInvestiMorphology->male_testosterone=$request->male_testosterone;
        $store_investInvestiMorphology->male_anti_hcv=$request->male_anti_hcv;
        $store_investInvestiMorphology->male_fbs=$request->male_fbs;
        $store_investInvestiMorphology->male_ppbs=$request->male_ppbs;
        $store_investInvestiMorphology->male_hba1c=$request->male_hba1c;
        $store_investInvestiMorphology->male_estradiol=$request->male_estradiol;
        $store_investInvestiMorphology->male_cftr=$request->male_cftr;
        $store_investInvestiMorphology->male_t_e_2=$request->male_t_e_2;
        $store_investInvestiMorphology->male_aurk_c=$request->male_aurk_c;
        $store_investInvestiMorphology->male_free_t4=$request->male_free_t4;
        $store_investInvestiMorphology->male_vdrl=$request->male_vdrl;
        $store_investInvestiMorphology->male_dpy19l2=$request->male_dpy19l2;
        $store_investInvestiMorphology->male_s_prolactin=$request->male_s_prolactin;
        $store_investInvestiMorphology->male_dnah1=$request->male_dnah1;
        $store_investInvestiMorphology->male_vit_d3=$request->male_vit_d3;
        $store_investInvestiMorphology->male_dnah5_dnai1=$request->male_dnah5_dnai1;
        $store_investInvestiMorphology->male_microdeletion=$request->male_microdeletion;
        $store_investInvestiMorphology->male_diagnosis=$request->male_diagnosis;
        $store_investInvestiMorphology->male_usg_scrotum=$request->male_usg_scrotum;
        $store_investInvestiMorphology->save();


        $store_investMaleLocalToVas=new InvestMaleLocalToVasModel();
        $store_investMaleLocalToVas->investigation_id=$store->id;
        $store_investMaleLocalToVas->male_prepuce_phimosis=$request->male_prepuce_phimosis;
        $store_investMaleLocalToVas->male_external_urethral_meatus=$request->male_external_urethral_meatus;
        $store_investMaleLocalToVas->male_scrotum=$request->male_scrotum;
        $store_investMaleLocalToVas->male_inguinal_iymphadenopathy=$request->male_inguinal_iymphadenopathy;
        $store_investMaleLocalToVas->male_testis_volume_size_right=$request->male_testis_volume_size_right;
        $store_investMaleLocalToVas->male_testis_volume_size_left=$request->male_testis_volume_size_left;
        $store_investMaleLocalToVas->male_testis_consistency_right=$request->male_testis_consistency_right;
        $store_investMaleLocalToVas->male_testis_consistency_left=$request->male_testis_consistency_left;
        $store_investMaleLocalToVas->male_testis_tenderness_right=$request->male_testis_tenderness_right;
        $store_investMaleLocalToVas->male_testis_tenderness_left=$request->male_testis_tenderness_left;
        $store_investMaleLocalToVas->male_epididymis_right=$request->male_epididymis_right;
        $store_investMaleLocalToVas->male_epididymis_left=$request->male_epididymis_left;
        $store_investMaleLocalToVas->male_epididymis_tenderness_right=$request->male_epididymis_tenderness_right;
        $store_investMaleLocalToVas->male_epididymis_tenderness_left=$request->male_epididymis_tenderness_left;
        $store_investMaleLocalToVas->male_epididymis_size_right=$request->male_epididymis_size_right;
        $store_investMaleLocalToVas->male_epididymis_size_left=$request->male_epididymis_size_left;
        $store_investMaleLocalToVas->male_vas_right=$request->male_vas_right;
        $store_investMaleLocalToVas->male_vas_left=$request->male_vas_left;
        $store_investMaleLocalToVas->male_vas_normal_right=$request->male_vas_normal_right;
        $store_investMaleLocalToVas->male_vas_normal_left=$request->male_vas_normal_left;
        $store_investMaleLocalToVas->male_vas_absent_right=$request->male_vas_absent_right;
        $store_investMaleLocalToVas->male_vas_absent_left=$request->male_vas_absent_left;
        $store_investMaleLocalToVas->male_vas_thickened_right=$request->male_vas_thickened_right;
        $store_investMaleLocalToVas->male_vas_thickened_left=$request->male_vas_thickened_left;
        $store_investMaleLocalToVas->male_vas_varicocele_right=$request->male_vas_varicocele_right;
        $store_investMaleLocalToVas->male_vas_varicocele_left=$request->male_vas_varicocele_left;
        $store_investMaleLocalToVas->male_vas_pr=$request->male_vas_pr;
        $store_investMaleLocalToVas->save();


        $store_investSemenToPenileDoppler=new InvestSemenToPenileDopplerModel();
        $store_investSemenToPenileDoppler->investigation_id=$store->id;
        $store_investSemenToPenileDoppler->male_semen_volume=$request->male_semen_volume;
        $store_investSemenToPenileDoppler->male_semen_ph=$request->male_semen_ph;
        $store_investSemenToPenileDoppler->male_semen_conc=$request->male_semen_conc;
        $store_investSemenToPenileDoppler->male_semen_semen_motility=$request->male_semen_semen_motility;
        $store_investSemenToPenileDoppler->male_semen_morphology=$request->male_semen_morphology;
        $store_investSemenToPenileDoppler->male_semen_fructose=$request->male_semen_fructose;
        $store_investSemenToPenileDoppler->male_semen_tmsc_tfmsc=$request->male_semen_tmsc_tfmsc;
        $store_investSemenToPenileDoppler->male_semen_liq=$request->male_semen_liq;
        $store_investSemenToPenileDoppler->male_semen_other_findings=$request->male_semen_other_findings;
        $store_investSemenToPenileDoppler->male_semen_dna_fragmentation=$request->male_semen_dna_fragmentation;
        $store_investSemenToPenileDoppler->male_semen_culture_sensitivity=$request->male_semen_culture_sensitivity;
        $store_investSemenToPenileDoppler->male_semen_testicular_biopsy=$request->male_semen_testicular_biopsy;
        $store_investSemenToPenileDoppler->male_semen_scrotal_ultrasound=$request->male_semen_scrotal_ultrasound;
        $store_investSemenToPenileDoppler->male_trus_ed_right=$request->male_trus_ed_right;
        $store_investSemenToPenileDoppler->male_trus_ed_left=$request->male_trus_ed_left;
        $store_investSemenToPenileDoppler->male_trus_sv_right=$request->male_trus_sv_right;
        $store_investSemenToPenileDoppler->male_trus_sv_left=$request->male_trus_sv_left;
        $store_investSemenToPenileDoppler->male_trus_vas_right=$request->male_trus_vas_right;
        $store_investSemenToPenileDoppler->male_trus_vas_left=$request->male_trus_vas_left;
        $store_investSemenToPenileDoppler->male_penile_doppler_psv_right=$request->male_penile_doppler_psv_right;
        $store_investSemenToPenileDoppler->male_penile_doppler_psv_left=$request->male_penile_doppler_psv_left;
        $store_investSemenToPenileDoppler->male_penile_doppler_edv_right=$request->male_penile_doppler_edv_right;
        $store_investSemenToPenileDoppler->male_penile_doppler_edv_left=$request->male_penile_doppler_edv_left;
        $store_investSemenToPenileDoppler->male_penile_doppler_impression=$request->male_penile_doppler_impression;
        $store_investSemenToPenileDoppler->save();
        return redirect()->route('superadmin.art');


    }

    public function artIndex(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.art_index', compact('admin'));

    }

    public function profileIvf(Request $request){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        // return view('superadmin.profile_index', compact('admin'));
        if ($request->ajax()) {
            return view('superadmin.profile_ivf', compact('admin')); // This partial contains filter + datatable
        }
        return redirect()->route('superadmin.art_index');
    }

    public function ovarianIvf(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.ovarian_ivf', compact('admin'));

    }
    public function eggpickIvf(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.eggpick_ivf', compact('admin'));

    }
    public function embryologyIvf(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryology_ivf', compact('admin'));

    }
    public function embryologyFreezindIvf(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryology_Freezind_ivf', compact('admin'));

    }
    public function embryologyTransferIvf(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryology_transfer_ivf', compact('admin'));

    }
    public function outcomeIvf(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.outcome_ivf', compact('admin'));

    }



    public function ovarianOdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.ovarian_odicsi', compact('admin'));

    }
    public function eggpickOdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.eggpick_odicsi', compact('admin'));

    }
    public function embryologyOdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryology_odicsi', compact('admin'));

    }
    public function embryologyFreezindOdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryology_Freezind_odicsi', compact('admin'));

    }
    public function embryologyTransferOdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryology_transfer_odicsi', compact('admin'));

    }
    public function outcomeOdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.outcome_odicsi', compact('admin'));

    }



    public function profileEdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.profile_edicsi', compact('admin'));

    }
    public function ovarianEdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.ovarian_edicsi', compact('admin'));

    }
    public function eggpickEdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.eggpick_edicsi', compact('admin'));

    }
    public function embryologyEdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryology_edicsi', compact('admin'));

    }
    public function embryologyFreezindEdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryology_Freezind_edicsi', compact('admin'));

    }
    public function embryologyTransferEdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.embryology_transfer_edicsi', compact('admin'));

    }
    public function outcomeEdicsi(){
        $staffId = auth()->user()->id;
        $admin = auth()->user();
        return view('superadmin.outcome_edicsi', compact('admin'));

    }


    private function saveCurlDatas($checkin,$location_id,$locationName,$zoneName, $locationId)
    {

            $cdate = !empty($checkin['date'])
                ? Carbon::createFromFormat('Ymd', $checkin['date'])->format('Y-m-d')
                : null;

            return [
                'date' => $cdate ?? '',

                    'name' => $checkin['patient']['name'] ?? '',
                    'mobile' => $checkin['patient']['mobile'] ?? '',
                    'phid' => $checkin['patient']['phid'] ?? '',
                    'consultingdr_name' => $checkin['consultingdr_name'] ?? '',
                    'entitylocation' => $checkin['entitylocation'] ?? '',
                    'gender' => $checkin['patient']['gender'] ?? '',
                    'lname' => $checkin['patient']['lname'] ?? '',
                    'familyid' => $checkin['patient']['familyid'] ?? '',
                    'city' => $checkin['patient']['address']['city'] ?? '',
                    'zone' => $zoneName ?? '',
                    'branch' => $locationName ?? '',
                    'locationid'=> $locationId ?? '',
                    'age' => $checkin['patient']['age'] ?? '',
                    'opno' => $checkin['opno'] ?? '',
                ];

	}


    public function discountform_data(Request $request){
      $wifeMRD = $request->input('dis_wife_mrd_no');
    $husMRD = $request->input('dis_husband_mrd_no');
    // Only check for exact match on wife's MRD
    $record = DiscountFormModel::where('dis_wife_mrd_no', $wifeMRD)->where('dis_husband_mrd_no',$husMRD)->first();
    return response()->json([$record]);
}
// public function disformsave_data(Request $request){
// $admin = auth()->user();
// $fitterremovedataall = $request->input('morefilltersall');
//  $datefiltervalue = $request->input('moredatefittervale');
//   $phid = $request->input('mrodnofilter');
//     $dates = explode(' - ', $datefiltervalue);
//     $startDate = $dates[0];  // "29/12/2024"
//     $endDate = $dates[1];    // "04/01/2025"

//     $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
//     $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

//     $startdates=$startDateFormatted." 00:00:00";
//     $enddates=$endDateFormatted." 23:59:59";
//     $data = DiscountFormModel::select('tbl_locations.name as location_name', 'tblzones.name as zone_name', 'hms_discount_form.*')
//     ->join('tbl_locations', 'hms_discount_form.dis_zone_id', '=', 'tbl_locations.id')
//     ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
//     ->whereDate('hms_discount_form.created_at', '>=', $startdates)->where('hms_discount_form.created_at', '<=', $enddates);

//         if($fitterremovedataall){
//             foreach (explode(' AND ', $fitterremovedataall) as $condition) {
//                   [$column, $value] = explode('=', $condition);
//             $column = trim($column);
//             $value = trim($value, "'");

//             // Skip 'phid' — it's not a real DB column
//             if ($column === 'phid') {
//                 continue;
//             }
//             $data->whereIn($column, explode(',', $value));
//             }
//         }
//     if (!empty($phid)) {
//         $data->where(function ($query) use ($phid) {
//             $query->where('dis_wife_mrd_no', 'LIKE', "%$phid%")
//                   ->orWhere('dis_husband_mrd_no', 'LIKE', "%$phid%");
//         });
//     }

//         $data = $data->orderBy('hms_discount_form.created_at', 'desc')->get();
//        return response()->json($data);
// }
// public function disformsave_data(Request $request){
//     $admin = auth()->user();
//     $fitterremovedataall = $request->input('morefilltersall');
//     $datefiltervalue = $request->input('moredatefittervale');
//     $phid = $request->input('mrodnofilter');

//     // Parse date range
//     // $dates = explode(' - ', $datefiltervalue);
//     // $startDate = $dates[0];  // "29/12/2024"
//     // $endDate = $dates[1];    // "04/01/2025"
//     // $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
//     // $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
//     // $startdates = $startDateFormatted . " 00:00:00";
//     // $enddates = $endDateFormatted . " 23:59:59";
//     $applyDateFilter = false;

//     if (!empty($datefiltervalue)) {
//         if (!empty($fitterremovedataall) || !empty($phid)) {
//             $applyDateFilter = true;
//         }
//     }

//     // Base query
//     $data = DiscountFormModel::select(
//         'tbl_locations.name as location_name',
//         'tblzones.name as zone_name',
//         'users.user_fullname as username',
//         'users.username as userid',
//         'hms_discount_form.*'
//     )
//     ->join('tbl_locations', 'hms_discount_form.dis_zone_id', '=', 'tbl_locations.id')
//     ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
//     ->join('users', 'hms_discount_form.created_by', '=', 'users.id');
//     // ->whereDate('hms_discount_form.created_at', '>=', $startdates)
//     // ->where('hms_discount_form.created_at', '<=', $enddates);

//     // ============================================
//     // ROLE-BASED ACCESS CONTROL
//     // ============================================
//     /* 🔐 ACCESS CONTROL */
//     if ($admin->access_limits == 1 || $admin->access_limits == 4) {
//         // ✅ SUPERADMIN / APPROVER → ALL DATA
//         \Log::info('Approver/Superadmin access - All cancel bills visible');

//     } elseif ($admin->access_limits == 2) {
//         // ✅ ADMIN → zone branches + multi-location branches

//         $branchIds = [];

//         /* 1️⃣ Branches under admin's zone */
//         if (!empty($admin->zone_id)) {
//             $zoneBranchIds = DB::table('tbl_locations')
//                 ->where('zone_id', $admin->zone_id)
//                 ->pluck('id')
//                 ->toArray();

//             $branchIds = array_merge($branchIds, $zoneBranchIds);
//         }

//         /* 2️⃣ Multi-location branches (comma-separated IDs) */
//         if (!empty($admin->multi_location)) {
//             $multiLocationIds = array_map(
//                 'intval',
//                 explode(',', $admin->multi_location)
//             );

//             $branchIds = array_merge($branchIds, $multiLocationIds);
//         }

//         /* 3️⃣ Remove duplicates */
//         $branchIds = array_unique($branchIds);

//         /* 4️⃣ Apply filter */
//         if (!empty($branchIds)) {
//             $data->whereIn('hms_discount_form.dis_zone_id', $branchIds);
//         }
//     } elseif ($admin->access_limits == 3) {
//         $branchIds = [];
//         if (!empty($user->multi_location)) {
//             $multiLocationIds = array_map(
//                 'intval',
//                 explode(',', $admin->multi_location)
//             );

//             $branchIds = array_merge($branchIds, $multiLocationIds);
//         }
//         $branchIds = array_unique($branchIds);
//         if (!empty($branchIds)) {
//             $data->whereIn('hms_discount_form.dis_zone_id', $branchIds);
//         }
//         // NORMAL USER → own records only
//         $data->where('hms_discount_form.created_by', $admin->id);
//     }
//     if ($applyDateFilter) {
//         $dates = explode(' - ', $datefiltervalue);
//         if (count($dates) === 2) {
//             $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))
//                             ->startOfDay();
//             $endDate   = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))
//                             ->endOfDay();

//             $data->whereBetween('hms_discount_form.created_at', [$startDate, $endDate]);
//         }
//     }

//     if ($fitterremovedataall) {
//         foreach (explode(' AND ', $fitterremovedataall) as $condition) {
//             [$column, $value] = explode('=', $condition);
//             $column = trim($column);
//             $value = trim($value, "'");
//             // Skip 'phid' — it's not a real DB column
//             if ($column === 'phid') {
//                 continue;
//             }
//             $data->whereIn($column, explode(',', $value));
//         }
//     }

//     // Apply PHID filter (MRD number search)
//     if (!empty($phid)) {
//         $data->where(function ($query) use ($phid) {
//             $query->where('dis_wife_mrd_no', 'LIKE', "%$phid%")
//                   ->orWhere('dis_husband_mrd_no', 'LIKE', "%$phid%");
//         });
//     }

//     // Execute query and return results
//     // $data = $data->orderBy('hms_discount_form.created_at', 'desc')->get();
//     $data = $data->orderBy('hms_discount_form.created_at', 'desc')
//     ->get();
//     $query = clone $data; // $data must still be query builder
//     $pendingColumnMap = [
//         1 => 'final_approver',  // Super Admin
//         2 => 'zonal_approver',  // Zonal Admin
//         3 => 'admin_approver',  // Admin
//         4 => 'audit_approver',  // Auditor
//     ];

//     $pendingColumn = $pendingColumnMap[$admin->access_limits] ?? null;
//     $totalRaised = (clone $query)->count();

//     $adminApproved = (clone $query)->where('admin_approver', 1)->count();
//     $zonalApproved = (clone $query)->where('zonal_approver', 1)->count();
//     $auditApproved = (clone $query)->where('audit_approver', 1)->count();
//     $finalApproved = (clone $query)->where('final_approver', 1)->count();

//     $pendingFinal = 0;
//     if ($pendingColumn) {
//         $pendingFinal = (clone $query)
//             ->where($pendingColumn, 0)
//             ->count();
//     }
//     return response()->json([
//         'data' => $data,
//         'counts' => [
//             'total_raised'   => $totalRaised,
//             'admin_approved' => $adminApproved,
//             'zonal_approved' => $zonalApproved,
//             'audit_approved' => $auditApproved,
//             'final_approved' => $finalApproved,
//             'pending'        => $pendingFinal,
//         ],
//     ]);

// }

public function disformsave_data(Request $request){
    $admin = auth()->user();
    $fitterremovedataall = $request->input('morefilltersall');
    $datefiltervalue = trim((string) $request->input('moredatefittervale', ''));
    $phid = $request->input('mrodnofilter');
    $statusFilter = $request->input('status_filter'); // approved, pending, rejected

    // Match refundform_data: empty / "All" = no date filter; valid d/m/Y range = filter (no extra gate on fitter/phid)
    $applyDateFilter = false;
    $startDate = null;
    $endDate = null;
    if ($datefiltervalue !== '' && strtolower($datefiltervalue) !== 'all') {
        $dates = array_map('trim', explode(' - ', $datefiltervalue));
        $startStr = $dates[0] ?? '';
        $endStr = (isset($dates[1]) && $dates[1] !== '') ? $dates[1] : $startStr;
        if ($startStr !== '') {
            try {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $startStr)->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $endStr)->endOfDay();
                if ($endDate->lt($startDate)) {
                    $endDate = $startDate->copy()->endOfDay();
                }
                $applyDateFilter = true;
            } catch (\Exception $e) {
                \Log::warning('disformsave_data: invalid moredatefittervale, loading all (no date filter)', [
                    'moredatefittervale' => $datefiltervalue,
                    'error' => $e->getMessage(),
                ]);
                $applyDateFilter = false;
                $startDate = null;
                $endDate = null;
            }
        }
    }
    if (!empty($fitterremovedataall) && empty($statusFilter)) {
        if (preg_match("/status_filter='(.*?)'/", $fitterremovedataall, $matches)) {
            $statusFilter = $matches[1];
        }
    }

    $data = DiscountFormModel::select(
        'tbl_locations.name as location_name',
        'tblzones.name as zone_name',
        'users.user_fullname as username',
        'users.username as userid',
        'hms_discount_form.*',
        'admin_approver_user.user_fullname as admin_approver_name',
        'zonal_approver_user.user_fullname as zonal_approver_name',
        'audit_approver_user.user_fullname as audit_approver_name',
        'final_approver_user.user_fullname as final_approver_name'
    )
    ->leftJoin('tbl_locations', 'hms_discount_form.dis_zone_id', '=', 'tbl_locations.id')
    ->leftJoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
    ->leftJoin('users', 'hms_discount_form.created_by', '=', 'users.id')
    ->leftJoin('users as admin_approver_user', 'hms_discount_form.admin_head_id', '=', 'admin_approver_user.id')
    ->leftJoin('users as zonal_approver_user', 'hms_discount_form.zonal_head_id', '=', 'zonal_approver_user.id')
    ->leftJoin('users as audit_approver_user', 'hms_discount_form.audit_head_id', '=', 'audit_approver_user.id')
    ->leftJoin('users as final_approver_user', 'hms_discount_form.final_approver_id', '=', 'final_approver_user.id');

    // ============================================
    // ROLE-BASED ACCESS CONTROL
    // ============================================
    if ($admin->access_limits == 1 || $admin->access_limits == 4) {
        // SUPERADMIN / APPROVER → ALL DATA
        \Log::info('Approver/Superadmin access - All cancel bills visible');

    } elseif ($admin->access_limits == 2) {
        // ADMIN → zone branches + multi-location branches
        $branchIds = [];

        if (!empty($admin->zone_id)) {
            $zoneBranchIds = DB::table('tbl_locations')
                ->where('zone_id', $admin->zone_id)
                ->pluck('id')
                ->toArray();
            $branchIds = array_merge($branchIds, $zoneBranchIds);
        }

        if (!empty($admin->multi_location)) {
            $multiLocationIds = array_map(
                'intval',
                explode(',', $admin->multi_location)
            );
            $branchIds = array_merge($branchIds, $multiLocationIds);
        }

        $branchIds = array_unique($branchIds);

        if (!empty($branchIds)) {
            $data->whereIn('hms_discount_form.dis_zone_id', $branchIds);
        }
    } elseif ($admin->access_limits == 3 || $admin->access_limits == 5) {
        $branchIds = [];
        if (!empty($admin->multi_location)) {
            $multiLocationIds = array_map(
                'intval',
                explode(',', $admin->multi_location)
            );
            $branchIds = array_merge($branchIds, $multiLocationIds);
        }
        $branchIds = array_unique($branchIds);
        if (!empty($branchIds)) {
            $data->whereIn('hms_discount_form.dis_zone_id', $branchIds);
        }
        // NORMAL USER → own records only
        $data->where('hms_discount_form.created_by', $admin->id);
    }

    if ($applyDateFilter && $startDate && $endDate) {
        $data->whereBetween('hms_discount_form.created_at', [$startDate, $endDate]);
    }

    // Apply additional filters
    if (!empty($fitterremovedataall)) {
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
            $column = trim($column);
            $value = trim($value, "'");
            if ($column === 'phid' || $column === 'status_filter') {
                continue;
            }
            $data->whereIn($column, explode(',', $value));
        }
    }

    // Apply PHID filter (MRD number search)
    if (!empty($phid)) {
        $data->where(function ($query) use ($phid) {
            $query->where('dis_wife_mrd_no', 'LIKE', "%$phid%")
                  ->orWhere('dis_husband_mrd_no', 'LIKE', "%$phid%");
        });
    }
    if (!empty($statusFilter)) {
        $pendingColumnMap = [
            1 => 'final_approver',
            2 => 'zonal_approver',
            3 => 'admin_approver',
            4 => 'audit_approver',
        ];
        $pendingCol = $pendingColumnMap[$admin->access_limits] ?? 'final_approver';
        $colPrefix = 'hms_discount_form.';
        if (in_array($statusFilter, ['approved', 'pending', 'rejected'])) {
            $val = $statusFilter === 'approved' ? 1 : ($statusFilter === 'rejected' ? 2 : 0);
            $data->where($colPrefix . $pendingCol, '=', $val);
        } elseif (preg_match('/^(final|admin|zonal|audit)_(approved|pending|rejected)$/', $statusFilter, $m)) {
            $col = $m[1] . '_approver';
            if ($m[1] === 'final') $col = 'final_approver';
            elseif ($m[1] === 'admin') $col = 'admin_approver';
            elseif ($m[1] === 'zonal') $col = 'zonal_approver';
            else $col = 'audit_approver';
            $val = $m[2] === 'approved' ? 1 : ($m[2] === 'rejected' ? 2 : 0);
            $data->where($colPrefix . $col, '=', $val);
        }
    }

    // Clone query for counting before getting results
    $baseQuery = clone $data;

    // Get results
    $results = $data->orderBy('hms_discount_form.created_at', 'desc')->get();
    // ============================================
    // CALCULATE STATISTICS
    // ============================================
    $pendingColumnMap = [
        1 => 'final_approver',  // Super Admin
        2 => 'zonal_approver',  // Zonal Admin
        3 => 'admin_approver',  // Admin
        4 => 'audit_approver',  // Auditor
    ];

    $pendingColumn = $pendingColumnMap[$admin->access_limits] ?? null;

    // Total counts
    $totalRaised = (clone $baseQuery)->count();
    $adminApproved = (clone $baseQuery)->where('admin_approver', 1)->count();
    $zonalApproved = (clone $baseQuery)->where('zonal_approver', 1)->count();
    $auditApproved = (clone $baseQuery)->where('audit_approver', 1)->count();
    $finalApproved = (clone $baseQuery)->where('final_approver', 1)->count();

    // Pending count based on user role
    $pendingFinal = 0;
    if ($pendingColumn) {
        $pendingFinal = (clone $baseQuery)
            ->where($pendingColumn, 0)
            ->count();
    }

    // Total discount amount: only from finally approved records (final_approver = 1)
    $totalDiscountAmount = (clone $baseQuery)
        ->where('final_approver', 1)
        ->get()
        ->sum(function ($row) {
            $value = $row->dis_post_discount ?? '0';
            return (float) preg_replace('/[^0-9.]/', '', $value);
        });

    // Additional statistics for compliance-style display
    $statistics = [
        'total_raised'   => $totalRaised,
        'admin_approved' => $adminApproved,
        'zonal_approved' => $zonalApproved,
        'audit_approved' => $auditApproved,
        'final_approved' => $finalApproved,
        'pending'        => $pendingFinal,
        'total_discount_amount' => round($totalDiscountAmount, 2),
        // Rejected count (if you have a rejected status column)
        // 'rejected'       => (clone $baseQuery)->where('status', 'rejected')->count() ?? 0,
        // Processing count
        'processing'     => $totalRaised - ($finalApproved + $pendingFinal),
    ];

    return response()->json([
        'data' => $results,
        'counts' => $statistics,
        'total_discount_amount' => round($totalDiscountAmount, 2),
        'statistics' => [
            [
                'label' => 'Total Raised',
                'count' => $totalRaised,
                'color' => '#FF6B6B', // Red
                'icon' => 'file-text'
            ],
            [
                'label' => 'Admin Approved',
                'count' => $adminApproved,
                'color' => '#4ECDC4', // Teal
                'icon' => 'check-circle'
            ],
            [
                'label' => 'Zonal Approved',
                'count' => $zonalApproved,
                'color' => '#45B7D1', // Blue
                'icon' => 'check-circle'
            ],
            [
                'label' => 'Audit Approved',
                'count' => $auditApproved,
                'color' => '#96CEB4', // Green
                'icon' => 'shield-check'
            ],
            [
                'label' => 'Final Approved',
                'count' => $finalApproved,
                'color' => '#51CF66', // Light Green
                'icon' => 'check-double'
            ],
            [
                'label' => 'Pending',
                'count' => $pendingFinal,
                'color' => '#FFA94D', // Orange
                'icon' => 'clock'
            ],
            [
                'label' => 'Total Discount (Final Approved)',
                'count' => round($totalDiscountAmount, 2),
                'amount' => round($totalDiscountAmount, 2),
                'color' => '#9B59B6', // Purple
                'icon' => 'currency-rupee'
            ],
        ]
    ]);
}
public function discount_datatdadded(Request $request)
{
    // Validate request
    $validatedData = $request->validate([
        'dis_zone_id' => 'required|string|max:255',
        'dis_wife_name' => 'nullable|string|max:255',
        'dis_wife_mrd_no' => 'nullable|string|max:255',
        'dis_husband_name' =>'nullable|string|max:255',
        'dis_husband_mrd_no'=> 'nullable|string|max:255',
        'dis_service_name'=> 'required|string|max:255',
        'dis_total_bill'=> 'required|string|max:255',
        'dis_expected_request'=>'required|string|max:255',
        'dis_post_discount' => 'required|string|max:255',
        'dis_patient_ph' => 'required|string|max:255',
        'dis_counselled_by' => 'required|string|max:255',
        'dis_final_auth' => 'required|string|max:255',
        'dis_branch_no' => 'required|string|max:255',
        'dis_auth_by' => 'required|string|max:255',
        'dis_approved_by' => 'required|string|max:255',
        'dis_form_status' => 'required|string|max:255',
    ]);

    DiscountFormModel::create(array_merge($validatedData, [
        'dis_zone_id' => $request->dis_zone_id,
        'dis_post_discount' =>$request->dis_post_discount,
        'dis_patient_ph' => $request->dis_patient_ph,
        'status' => 1
    ]));

    return response()->json(['success' => true, 'message' => 'Discountform saved successfully!']);
}

//cancel bill
// public function cancelsave_data(Request $request)
// {
//     $user = auth()->user();

//     // check approver
//     $approver = DB::table('cancel_bill_approver')
//         ->where('user_id', $user->id)
//         ->first();

//     $fitterremovedataall = $request->input('morefilltersall');
//     $datefiltervalue     = $request->input('moredatefittervale');

//     $dates = explode(' - ', $datefiltervalue);
//     $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
//     $endDate   = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();

//     $query = CancelbillFormModel::select(
//             'tbl_locations.name as location_name',
//             'tblzones.name as zone_name',
//             'hms_cancelbill_form.*'
//         )
//         ->join('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
//         ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
//         ->whereBetween('hms_cancelbill_form.can_date', [$startDate, $endDate]);
//     // 🔑 IMPORTANT LOGIC
//     if (!$approver && $user->id !== 102) {
//         // not an approver → show only own bills
//         $query->where('hms_cancelbill_form.created_by', $user->id);
//     }
//     // if (!$approver ) {
//     //     // not an approver → show only own bills
//     //     $query->where('hms_cancelbill_form.created_by', $user->id);
//     // }
//     // additional filters
//     if ($fitterremovedataall) {
//         foreach (explode(' AND ', $fitterremovedataall) as $condition) {
//             [$column, $value] = explode('=', $condition);
//             $query->whereIn(trim($column), explode(',', trim($value, "'")));
//         }
//     }

//     $data = $query->orderBy('hms_cancelbill_form.created_at', 'desc')->get();
//     return response()->json([
//         'data'       => $data,
//         'isApprover' => (bool) $approver
//     ]);
// }
public function cancelsave_data(Request $request)
{
    $user = auth()->user();

    $fitterremovedataall = $request->input('morefilltersall');
    $datefiltervalue     = $request->input('moredatefittervale');

    // 🔐 Check approver
    $isApprover = DB::table('cancel_bill_approver')
        ->where('user_id', $user->id)
        ->exists();

    // ============================================
    // BASE QUERY
    // ============================================
    $data = CancelbillFormModel::select(
            'tbl_locations.name as location_name',
            'tblzones.name as zone_name',
            'users.user_fullname as username',
            'users.username as userid',
            'hms_cancelbill_form.*'
        )
        ->leftjoin('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
        ->leftjoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->leftjoin('users', 'hms_cancelbill_form.created_by', '=', 'users.id');

    // ============================================
    // ACCESS CONTROL
    // ============================================
    if ($user->access_limits == 1 || $isApprover || $user->access_limits == 4) {
        // SUPERADMIN / APPROVER → ALL DATA

    } elseif ($user->access_limits == 2) {
        // ADMIN
        $branchIds = [];

        if (!empty($user->zone_id)) {
            $branchIds = array_merge(
                $branchIds,
                DB::table('tbl_locations')
                    ->where('zone_id', $user->zone_id)
                    ->pluck('id')
                    ->toArray()
            );
        }

        if (!empty($user->multi_location)) {
            $branchIds = array_merge(
                $branchIds,
                array_map('intval', explode(',', $user->multi_location))
            );
        }

        if (!empty($branchIds)) {
            $data->whereIn('hms_cancelbill_form.can_zone_id', array_unique($branchIds));
        }

        $data->where('hms_cancelbill_form.created_by', $user->id);

    } elseif ($user->access_limits == 3) {
        // NORMAL USER
        if (!empty($user->multi_location)) {
            $data->whereIn(
                'hms_cancelbill_form.can_zone_id',
                array_map('intval', explode(',', $user->multi_location))
            );
        }

        $data->where('hms_cancelbill_form.created_by', $user->id);
    }

    // ============================================
    // DATE FILTER (can_date)
    // ============================================
    if (!empty($datefiltervalue)) {
        $dates = explode(' - ', $datefiltervalue);
        if (count($dates) === 2) {
            $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
            $endDate   = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();

            $data->whereBetween('hms_cancelbill_form.can_date', [$startDate, $endDate]);
        }
    }

    // ============================================
    // EXTRA FILTERS
    // ============================================
    // if ($fitterremovedataall) {
    //     foreach (explode(' AND ', $fitterremovedataall) as $condition) {
    //         [$column, $value] = explode('=', $condition);
    //         if ($column === 'phid' || str_ends_with($column, '.phid')) {
    //             if (!empty($value)) {
    //                 $data->where('hms_cancelbill_form.can_mrdno', 'LIKE', '%' . $value . '%');
    //             }
    //             continue;
    //         }
    //         $data->whereIn(
    //             trim($column),
    //             explode(',', trim($value, "'"))
    //         );
    //     }
    // }
    if ($fitterremovedataall) {
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            $parts = explode('=', $condition, 2);
            if (count($parts) < 2) continue;
            $column = trim($parts[0]);
            $value  = trim($parts[1], "'");
            // phid is MRD - hms_cancelbill_form uses can_mrdno, not phid
            if ($column === 'phid' || str_ends_with($column, '.phid')) {
                if (!empty($value)) {
                    $data->where('hms_cancelbill_form.can_mrdno', 'LIKE', '%' . $value . '%');
                }
                continue;
            }

            $data->whereIn($column, explode(',', $value));
        }
    }

    // ============================================
    // CLONE FOR COUNTS
    // ============================================
    $baseQuery = clone $data;

    $results = $data
        ->orderBy('hms_cancelbill_form.created_at', 'desc')
        ->get();

    // ============================================
    // COUNTS
    // ============================================
    $pendingColumnMap = [
        1 => 'final_approver',
        2 => 'zonal_approver',
        3 => 'admin_approver',
        4 => 'audit_approver',
    ];

    $pendingColumn = $pendingColumnMap[$user->access_limits] ?? null;

    $totalRaised   = (clone $baseQuery)->count();
    $adminApproved = (clone $baseQuery)->where('admin_approver', 1)->count();
    $zonalApproved = (clone $baseQuery)->where('zonal_approver', 1)->count();
    $auditApproved = (clone $baseQuery)->where('audit_approver', 1)->count();
    $finalApproved = (clone $baseQuery)->where('final_approver', 1)->count();

    $pendingFinal = 0;
    if ($pendingColumn) {
        $pendingFinal = (clone $baseQuery)
            ->where($pendingColumn, 0)
            ->count();
    }
    $totalAmountCancelled = (clone $baseQuery)
        ->where('final_approver', 1)
        ->get()
        ->sum(fn ($r) => (float) ($r->can_total ?? 0));

    // ============================================
    // RESPONSE
    // ============================================
    return response()->json([
        'data'       => $results,
        'isApprover' => $isApprover,
        'counts'     => [
            'total_raised'   => $totalRaised,
            'admin_approved' => $adminApproved,
            'zonal_approved' => $zonalApproved,
            'audit_approved' => $auditApproved,
            'final_approved' => $finalApproved,
            'pending'        => $pendingFinal,
            'processing'     => $totalRaised - ($finalApproved + $pendingFinal),
            'total_cancel_amount' => round($totalAmountCancelled, 2),
        ],
        'statistics' => [
            [
                'label' => 'Total Raised',
                'count' => $totalRaised,
                'color' => '#FF6B6B',
                'icon'  => 'file-text'
            ],
            [
                'label' => 'Admin Approved',
                'count' => $adminApproved,
                'color' => '#4ECDC4',
                'icon'  => 'check-circle'
            ],
            [
                'label' => 'Zonal Approved',
                'count' => $zonalApproved,
                'color' => '#45B7D1',
                'icon'  => 'check-circle'
            ],
            [
                'label' => 'Audit Approved',
                'count' => $auditApproved,
                'color' => '#96CEB4',
                'icon'  => 'shield-check'
            ],
            [
                'label' => 'Final Approved',
                'count' => $finalApproved,
                'color' => '#51CF66',
                'icon'  => 'check-double'
            ],
            [
                'label' => 'Pending',
                'count' => $pendingFinal,
                'color' => '#FFA94D',
                'icon'  => 'clock'
            ],
            [
                'label' => 'Total Cancelled',
                'count' => round($totalAmountCancelled, 2),
                'color' => '#9775FA',
                'icon'  => 'currency-rupee'
            ],
        ]
    ]);
}

// public function approveReject(Request $request)
//     {
//         // dd($request);
//         $request->validate([
//             'id'     => 'required|integer',
//             'status' => 'required|in:1,2'
//         ]);

//         DB::table('hms_cancelbill_form')
//             ->where('can_id', $request->id)
//             ->update([
//                 'approve_status' => $request->status,
//                 'approved_by'     => auth()->id(),
//                 'updated_at'     => now()
//             ]);

//         return response()->json([
//             'success' => true,
//             'message' => $request->status == 1
//                 ? 'Bill approved successfully'
//                 : 'Bill rejected successfully'
//         ]);
//     }
public function approveReject(Request $request)
{
    // dd($request);
    $admin = auth()->user();

    $rules = [
        'id'     => 'required|integer',
        'status' => 'required|in:1,2'
    ];
    if ($request->status == 2) {
        $rules['reject_reason'] = 'required|string|max:2000';
    }
    $request->validate($rules);

    // ============================================
    // MAP ACCESS LEVEL → APPROVAL COLUMN
    // ============================================
    $approvalMap = [
        1 => ['column' => 'final_approver', 'id_column' => 'final_approver_id'],
        2 => ['column' => 'zonal_approver', 'id_column' => 'zonal_head_id'],
        3 => ['column' => 'admin_approver', 'id_column' => 'admin_head_id'],
        4 => ['column' => 'audit_approver', 'id_column' => 'audit_head_id'],
    ];

    if (!isset($approvalMap[$admin->access_limits])) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action'
        ], 403);
    }

    $approval = $approvalMap[$admin->access_limits];

    // ============================================
    // UPDATE CANCEL RECORD
    // ============================================
    $updateData = [
        $approval['column']    => $request->status,
        $approval['id_column'] => $admin->id,
        'updated_at'           => now()
    ];
    if ($request->status == 2 && $request->filled('reject_reason')) {
        $updateData['reject_reason'] = $request->reject_reason;
    }
    $updated = DB::table('hms_cancelbill_form')
        ->where('can_id', $request->id)
        ->update($updateData);

    if (!$updated) {
        return response()->json([
            'success' => false,
            'message' => 'Record not found or update failed'
        ], 404);
    }

    // ============================================
    // FETCH UPDATED RECORD
    // ============================================
    $updatedRecord = DB::table('hms_cancelbill_form')
        ->select(
            'hms_cancelbill_form.*',
            'tbl_locations.name as location_name',
            'tblzones.name as zone_name',
            'users.user_fullname as username'
        )
        ->join('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
        ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->join('users', 'hms_cancelbill_form.created_by', '=', 'users.id')
        ->where('hms_cancelbill_form.can_id', $request->id)
        ->first();

    // ============================================
    // BASE QUERY FOR STATISTICS (SAME AS LIST PAGE)
    // ============================================
    $baseQuery = DB::table('hms_cancelbill_form')
        ->join('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
        ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->join('users', 'hms_cancelbill_form.created_by', '=', 'users.id');

    // --------------------------------------------
    // ROLE BASED FILTERS (MATCH cancelformsave_data)
    // --------------------------------------------
    if ($admin->access_limits == 1) {
        // Super admin → all
    }
    elseif ($admin->access_limits == 2) {
        $branchIds = [];

        if (!empty($admin->zone_id)) {
            $branchIds = DB::table('tbl_locations')
                ->where('zone_id', $admin->zone_id)
                ->pluck('id')->toArray();
        }

        if (!empty($admin->multi_location)) {
            $branchIds = array_merge(
                $branchIds,
                array_map('intval', explode(',', $admin->multi_location))
            );
        }

        $branchIds = array_unique($branchIds);

        if (!empty($branchIds)) {
            $baseQuery->whereIn('hms_cancelbill_form.can_zone_id', $branchIds);
        }
    }
    elseif ($admin->access_limits == 3) {
        $branchIds = [];

        if (!empty($admin->multi_location)) {
            $branchIds = array_map('intval', explode(',', $admin->multi_location));
        }

        if (!empty($branchIds)) {
            $baseQuery->whereIn('hms_cancelbill_form.can_zone_id', $branchIds);
        }

        $baseQuery->where('hms_cancelbill_form.created_by', $admin->id);
    }

    // ============================================
    // STATISTICS
    // ============================================
    $statistics = [
        'total_raised'   => (clone $baseQuery)->count(),
        'admin_approved' => (clone $baseQuery)->where('admin_approver', 1)->count(),
        'zonal_approved' => (clone $baseQuery)->where('zonal_approver', 1)->count(),
        'audit_approved' => (clone $baseQuery)->where('audit_approver', 1)->count(),
        'final_approved' => (clone $baseQuery)->where('final_approver', 1)->count(),
    ];

    $pendingColumnMap = [
        1 => 'final_approver',
        2 => 'zonal_approver',
        3 => 'admin_approver',
        4 => 'audit_approver',
    ];

    $pendingColumn = $pendingColumnMap[$admin->access_limits] ?? null;

    $statistics['pending'] = $pendingColumn
        ? (clone $baseQuery)->where($pendingColumn, 0)->count()
        : 0;

    $totalAmountCancelled = (clone $baseQuery)
        ->where('final_approver', 1)
        ->get()
        ->sum(fn ($r) => (float) ($r->can_total ?? 0));
    $statistics['total_discount_amount'] = round($totalAmountCancelled, 2);

    // ============================================
    // FRONTEND FRIENDLY FORMAT
    // ============================================
    $formattedStats = [
        ['label' => 'Total Raised',   'count' => $statistics['total_raised'],   'icon' => 'file-text'],
        ['label' => 'Admin Approved', 'count' => $statistics['admin_approved'], 'icon' => 'check-circle'],
        ['label' => 'Zonal Approved', 'count' => $statistics['zonal_approved'], 'icon' => 'check-circle'],
        ['label' => 'Audit Approved', 'count' => $statistics['audit_approved'], 'icon' => 'shield-check'],
        ['label' => 'Final Approved', 'count' => $statistics['final_approved'], 'icon' => 'check-double'],
        ['label' => 'Pending',        'count' => $statistics['pending'],        'icon' => 'clock'],
        ['label' => 'Total Cancelled', 'count' => $statistics['total_discount_amount'], 'icon' => 'currency-rupee'],
    ];

    return response()->json([
        'success'        => true,
        'message'        => $request->status == 1
            ? 'Cancel bill approved successfully'
            : 'Cancel bill rejected successfully',
        'record'         => $updatedRecord,
        'statistics'     => $formattedStats,
        'counts'         => $statistics,
        'approval_type'  => $approval['column'],
        'approver_name'  => $admin->user_fullname ?? $admin->name
    ]);
}

public function cancelbill_data(Request $request){
 $billno= $request->input('bill_no');
 $data = CancelbillFormModel::select('tbl_locations.name as location_name', 'tblzones.name as zone_name', 'hms_cancelbill_form.*')
    ->join('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
    ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
 ->where('can_bill_no', $billno)->first();
//  dd($data);
 return response()->json([$data]);
}

public function cancelbilladd(Request $request)
{
    $admin = auth()->user();
    $can_bill = $request->input('can_bill_no');
    // echo $can_bill; exit;
    $validated = $request->validate([
        'can_zone_id' => 'required|numeric',
        'can_op_no' => 'required|numeric',
        'can_token_no' => 'required|numeric',
        'can_bill_no' => 'required|string|max:255',
        'can_consultant' => 'required|string|max:255',
        'can_date' => 'required|string|max:255',
        'can_name' => 'required|string|max:255',
        'can_mrdno' => 'required|string|max:255',
        'can_age' => 'required|string|max:255',
        'can_gender' => 'required|string|max:255',
        'can_mobile' => 'required|string|max:255',
        'can_payment_type' => 'required|string|max:255',
        'can_payment_details' => 'required|string|max:255',
        'can_form_status' => 'required|string|max:255',
        'can_total' => 'required|numeric',
        'can_previous_alance' => 'required|string|max:255',
        'can_amount_receivable' => 'required|numeric',
        'can_amount_received' => 'required|numeric',
        'can_advance' => 'required|numeric',
        'can_amount_word' => 'required|string|max:255',
        'can_advance_word' => 'required|string|max:255',
        'can_prepared_by' => 'required|string|max:255',
        'can_reason' => 'required|string|max:255',
        'can_zonal_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'can_admin_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'can_sno' => 'required|array',
        'can_particulars' => 'required|array',
        'can_qty' => 'required|array',
        'can_rate' => 'required|array',
        'can_tax' => 'required|array',
        'can_amount' => 'required|array',
    ]);

    $imagePaths = [];
    $signatureFields = ['can_zonal_sign', 'can_admin_sign'];

    foreach ($signatureFields as $field) {
        if ($request->hasFile($field)) {
            $image = $request->file($field);
            $filename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('cancel_form');
            $image->move($destinationPath, $filename);
            $imagePaths[$field] = 'cancel_form/' . $filename;
        } else {
            $existing = CancelbillFormModel::where('can_bill_no', $can_bill)->value($field);
            $imagePaths[$field] = $existing ?: json_encode([]);
        }
    }

    // Encode arrays
    $validated['can_sno'] = json_encode(array_map('intval', $validated['can_sno']));
    $validated['can_qty'] = json_encode(array_map('floatval', $validated['can_qty']));
    $validated['can_rate'] = json_encode(array_map('floatval', $validated['can_rate']));
    $validated['can_tax'] = json_encode(array_map('floatval', $validated['can_tax']));
    $validated['can_amount'] = json_encode(array_map('floatval', $validated['can_amount']));
    $validated['can_particulars'] = json_encode($validated['can_particulars']);

    // Set image paths
    $validated['can_admin_sign'] = isset($imagePaths['can_admin_sign']) ? json_encode([$imagePaths['can_admin_sign']]) : null;
    $validated['can_zonal_sign'] = isset($imagePaths['can_zonal_sign']) ? json_encode([$imagePaths['can_zonal_sign']]) : null;

    $validated['status'] = 1;
    $validated['created_by'] = $admin->id;

    // CancelbillFormModel::updateOrCreate(
    //     ['can_bill_no' => $can_bill],
    //     $validated
    // );
    $model = CancelbillFormModel::firstOrNew([
        'can_bill_no' => $can_bill
    ]);

    // Fill all validated fields
    $model->fill($validated);

    // FORCE overwrite every time (create + update)
    $model->created_by = auth()->id();
    $model->status = 1;

    // Save
    $model->save();

    return response()->json([
        'success' => true,
        'message' => 'Cancel Bill Form saved successfully!',
        'updatedRecord' => $this->cancelBillRecordForGrid($model->can_id),
    ]);
}

public function checkinapi($curr_date, $location_id, $max_retries, $end_date){

 $retry = 0;
            $backoff = 1;

            do {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://mocdoc.in/api/checkedin/draravinds-ivf',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => "startdate={$curr_date}&enddate={$end_date}&entitylocation={$location_id}",
                    CURLOPT_HTTPHEADER =>  array('md-authorization: MD 7b40af0edaf0ad75:jR1+YyQZVWCIIaXlgxt1z8uixQ4=',
                        'Date: Mon, 31 Mar 2025 08:05:38 GMT',
                        'Content-Type: application/x-www-form-urlencoded'),
                ));

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                if ($httpCode === 429) {
                    sleep($backoff);
                    $backoff *= 2;
                    $retry++;
                } else {
                    return json_decode($response, true);
                }
            } while ($retry < $max_retries);

            sleep(1);
}

private function getTimelineApi($phid,$hmac)
{
    $retry_count = 0;
    $delay = 2;
     $url = "https://mocdoc.com/api/get/patienttimeline/draravinds-ivf/" . $phid;
    while ($retry_count < 5) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                        'Authorization: MD 7b40af0edaf0ad75:'.$hmac,
                        'Date: Wed, 31 May 2025 10:00:00 GMT',
                        'Content-Type: application/x-www-form-urlencoded'
        ),
          ));

        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            echo "cURL Error: " . curl_error($curl);
            break;
        }
//  echo "<pre>";print_r($response);exit;
        if ($http_status == 200) {
            curl_close($curl);
            return json_decode($response, true);
        }

        if ($http_status == 429) {
            sleep($delay);
            $retry_count++;
            $delay *= 2;
        } else {
            curl_close($curl);
            break;
        }
    }

    return []; // Return empty data if failed to fetch
}

private function selectQueryzone($city)
{
    $zone = TblZonesModel::select('id')->where('name', $city)->first();
    if (!$zone) {
        return collect(); // Return an empty Laravel collection instead of []
    }
    return TblLocationModel::where('zone_id', $zone->id)
                           ->where('status', 1)
                           ->orderBy('name', 'asc')
                           ->get(); // This is a collection
}

private function savecurld($checkin,$location_id,$locationName,$zoneName, $locationId)
    {

            $cdate = !empty($checkin['date'])
                ? Carbon::createFromFormat('Ymd', $checkin['date'])->format('Y-m-d')
                : null;

            return [
                'date' => $cdate ?? '',
                    'name' => $checkin['patient']['name'] ?? '',
                    'mobile' => $checkin['patient']['mobile'] ?? '',
                    'phid' => $checkin['patient']['phid'] ?? '',
                    'consultingdr_name' => $checkin['consultingdr_name'] ?? '',
                    'entitylocation' => $checkin['entitylocation'] ?? '',
                    'gender' => $checkin['patient']['gender'] ?? '',
                    'lname' => $checkin['patient']['lname'] ?? '',
                    'familyid' => $checkin['patient']['familyid'] ?? '',
                    'city' => $checkin['patient']['address']['city'] ?? '',
                    'zone' => $zoneName ?? '',
                    'branch' => $locationName ?? '',
                    'locationid'=> $locationId ?? '',
                    'age' => $checkin['patient']['age'] ?? '',
                    'opno' => $checkin['opno'] ?? '',
                ];

	}

    private const SECRET = 'd6401b40cebeda6f22c7e7ee1efe0ed4';
    private function calculateHMAC(string $secret, string $stringToSign): string
    {
        $hash = hash_hmac('sha1', $stringToSign, $secret, true); // true = raw binary output
        return base64_encode($hash);
    }

    public function hmac(string $phid): string
    {
        $verb = "POST";
        $contentMd5 = "";
        $contentType = "application/x-www-form-urlencoded";
        $date = "Wed, 31 May 2025 10:00:00 GMT"; // You can replace this with `gmdate(...)` for dynamic
        $path = "/api/get/patienttimeline/draravinds-ivf/" . $phid;

        $stringToSign = $verb . "\n" .
                        $contentMd5 . "\n" .
                        $contentType . "\n" .
                        $date . "\n" .
                        "\n" .
                        strtolower($path);

        return $this->calculateHMAC(self::SECRET, $stringToSign);
    }

    public function cancelbillform_data(Request $request)
{
    $admin = auth()->user();
    set_time_limit(0);
    $fitterremovedataall = $request->input('morefilltersall');
    $datefiltervalue = $request->input('moredatefittervale');
    $mrdno = $request->input('mrodnofilter');

    // Date formatting
    $dates = explode(' - ', $datefiltervalue);
    $startDateFormatted = Carbon::createFromFormat('d/m/Y', $dates[0] ?? '01/01/1970')->format('Y-m-d');
    $endDateFormatted = Carbon::createFromFormat('d/m/Y', $dates[1] ?? date('d/m/Y'))->format('Y-m-d');
    $start_date = Carbon::parse($startDateFormatted)->format('Ymd') . '00:00:00';
    $end_date = Carbon::parse($endDateFormatted)->format('Ymd') . '23:59:59';

    $locations = $this->cityArray();
    $user_location = $this->getAccessibleLocations($admin);

    $finalLocations = [];

    foreach ($user_location as $dbId => $dbName) {
        foreach ($locations as $mocKey => $mocName) {
            if (trim($mocName) === trim($dbName)) {
                $finalLocations[$mocKey] = $mocName;
            }
        }
    }

    // dd($finalLocations);
    // Extract filter values
    preg_match_all("/'([^']+)'/", $fitterremovedataall, $matches);
    $values = $matches[1];

    $hasZone = strpos($fitterremovedataall, 'tblzones.name') !== false;
    $hasLocation = strpos($fitterremovedataall, 'tbl_locations.name') !== false;
    $hasPhid = strpos($fitterremovedataall, 'phid') !== false;

    // 1. Fetch API check-in data
    $checkinData = $this->getCheckinData($finalLocations, $hasZone, $hasLocation, $hasPhid, $values, $start_date, $end_date, $mrdno);
    // dd($checkinData);
    // 2. Fetch existing DB saved data
    $existingData = $this->getExistingCancelBills($hasZone, $hasLocation, $values, $startDateFormatted, $endDateFormatted, $mrdno);

    // 3. Build lookup for saved bills
    $savedBillMap = [];
    foreach ($existingData as $record) {
        if (!empty($record['can_bill_no'])) {
            $savedBillMap[$record['can_bill_no']] = $record;
            $savedBillMap[$record['can_bill_no']]['is_saved'] = true;
        }
    }
    // dd($savedBillMap);
    $mergedData = [];

    // 4. Flatten API check-in data: 1 entry per bill
    foreach ($checkinData as $patient) {
        if (!empty($patient['main_bills']) && is_array($patient['main_bills'])) {
            foreach ($patient['main_bills'] as $bill) {
                $billNo = $bill['bill_no'] ?? null;
                if (!$billNo) continue;

                if (isset($savedBillMap[$billNo])) {
                    // Already saved: take DB version
                    $mergedData[] = $savedBillMap[$billNo];
                    unset($savedBillMap[$billNo]); // prevent duplicates
                } else {
                    // Not saved: use API version
                    $mergedData[] = [
                        'name' => $patient['name'] ?? '',
                        'phid' => $patient['phid'] ?? '',
                        'mobile' => $patient['mobile'] ?? '',
                        'opno' => $patient['opno'] ?? '',
                        'age' => $patient['age'] ?? '',
                        'gender' => $patient['gender'] ?? '',
                        'branch' => $patient['branch'] ?? '',
                        'city' => $patient['city'] ?? '',
                        'zone' => $patient['zone'] ?? '',
                        'consultingdr_name' => $patient['consultingdr_name'] ?? '',
                        'date' => $patient['date'] ?? '',
                        'locationid' => $patient['locationid'] ?? '',
                        'location_name' => $patient['branch'] ?? '',
                        'zone_name' => $patient['zone'] ?? '',
                        'bill_no' => $bill['bill_no'] ?? '',
                        'billdate' => $bill['billdate'] ?? '',
                        'billtype' => $bill['billtype'] ?? '',
                        'amountpayable' => $bill['amountpayable'] ?? '',
                        'amountreceived' => $bill['amountreceived'] ?? '',
                        'paymenttype' => $bill['paymenttype'] ?? '',
                        'prev_balance' => $bill['prev_balance'] ?? '',
                        'billitems' => $bill['billitems'] ?? [],
                        'consultant' => $bill['consultant'] ?? '',
                        'is_saved' => false
                    ];
                }
            }
        }
    }

    // 5. Append any saved bills that were not found in API (if any remain in $savedBillMap)
    foreach ($savedBillMap as $billRecord) {
        $billRecord['is_saved'] = true;
        $mergedData[] = $billRecord;
    }

    return response()->json(['checkinData' => $mergedData]);
}
private function getAccessibleLocations($admin)
{
    if ($admin->access_limits == 1) {
        // ALL locations
        return TblLocationModel::orderBy('name')->pluck('name', 'id')->toArray();
    }

    if ($admin->access_limits == 2) {
        // ZONE based locations
        return TblLocationModel::where('zone_id', $admin->zone_id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    if ($admin->access_limits == 3) {
        // ONLY OWN BRANCH
        return TblLocationModel::where('id', $admin->branch_id)
            ->pluck('name', 'id')
            ->toArray();
    }

    return [];
}

private function getCheckinData($locations, $hasZone, $hasLocation, $hasPhid, $values, $startDate, $endDate, $mrdno)
{
    
    $checkinData = [];
    $finalResults = [];
    $admin = auth()->user();
    // Logic simplified for all 7 cases
    // Build a list of [location, zone] pairs to check
    $locationZonePairs = [];

    if ($hasZone && $hasLocation) {
        $zoneName = $values[0];
        $locationName = $values[1];
        $location = TblLocationModel::where('name', $locationName)->first();
        if ($location && TblZonesModel::find($location->zone_id)->name === $zoneName) {
            $locationZonePairs[] = [$locationName, $zoneName, $location->id];
        }
    } elseif ($hasLocation) {
        $locationName = $values[0];
        $location = TblLocationModel::where('name', $locationName)->first();
        $zone = TblZonesModel::find($location->zone_id);
        if ($location) {
            $locationZonePairs[] = [$locationName, $zone->name ?? '', $location->id];
        }
    } elseif ($hasZone) {
        $zoneName = $values[0];
        $zoneLocations = $this->selectQueryzone($zoneName);
        foreach ($zoneLocations as $loc) {
            $locationZonePairs[] = [$loc->name, $zoneName, $loc->id];
        }
    } elseif ($hasPhid && !$hasLocation && !$hasZone) {
        foreach ($locations as $locName) {
            $location = TblLocationModel::where('name', $locName)->first();
            if ($location) {
                $zone = TblZonesModel::find($location->zone_id);
                $locationZonePairs[] = [$locName, $zone->name ?? '', $location->id];
            }
        }
    }else {
            if ($admin->access_limits == 1) {
                $zone = TblZonesModel::orderBy('id')->first();
                if ($zone) {
                    $location = TblLocationModel::where('zone_id', $zone->id)
                        ->orderBy('id')
                        ->first();
                }
            } elseif ($admin->access_limits == 2) {
                $zone = TblZonesModel::find($admin->zone_id);
                if ($zone) {
                    $location = TblLocationModel::where('zone_id', $zone->id)
                        ->orderBy('id')
                        ->first();
                }
            } else {
                $location = TblLocationModel::find($admin->branch_id);
                $zone = $location
                    ? TblZonesModel::find($location->zone_id)
                    : null;
            }
            if (!empty($location)) {
                $locationZonePairs[] = [
                    $location->name,
                    $zone->name ?? '',
                    $location->id
                ];
            }
        }
    // else {

    //     // Default
    //     $default = 'Chennai - Sholinganallur';
    //     $location = TblLocationModel::where('name', $default)->first();
    //     $zone = TblZonesModel::find($location->zone_id);
    //     $locationZonePairs[] = [$default, $zone->name ?? '', $location->id];
    // }

    foreach ($locationZonePairs as [$locName, $zoneName, $locId]) {
        $locKey = array_search($locName, $locations);
        $apiData = $this->checkinapi($startDate, $locKey, 5, $endDate);

        foreach ($apiData['checkinlist'] ?? [] as $checkin) {
            if (!$hasPhid || ($checkin['patient']['phid'] == $mrdno)) {
                $checkinEntry = $this->savecurld($checkin, $locKey, $locName, $zoneName, $locId);
                $phid = $checkinEntry['phid'] ?? null;

                if ($phid) {
                    $hmac = $this->hmac($phid);
                    $timeline = $this->getTimelineApi($phid, $hmac);
                    $checkinEntry['main_bills'] = $this->extractBills($timeline);
                }

                $checkinData[] = $checkinEntry;
            }
        }
    }

    return $checkinData;
}

private function extractBills($timelineData)
{
    $timelineData = array_values($timelineData);
    $mainBill = [];

    foreach ($timelineData as $entry) {
        foreach (['OP', 'IP'] as $type) {
            if (isset($entry[$type]) && is_array($entry[$type])) {
                foreach ($entry[$type] as $key => $value) {
                    if (isset($value['op_bill']) || isset($value['main_bill'])) {
                        $billType = ($type === 'OP') ? 'op' : 'ip';
                        $mainBill["{$billType}_$key"] = $value['op_bill'] ?? $value['main_bill'];
                    }

                    if ($key === 'pharmacy_bills' && is_array($value)) {
                        foreach ($value as $phKey => $phData) {
                            $mainBill["pharmacy_$phKey"] = $phData;
                        }
                    }
                }

                if (isset($entry[$type]['pharmacy_bills'])) {
                    foreach ($entry[$type]['pharmacy_bills'] as $phKey => $phData) {
                        $mainBill["pharmacy_$phKey"] = $phData;
                    }
                }
            }
        }
    }

    return array_values($mainBill);
}

private function getExistingCancelBills($hasZone, $hasLocation, $values, $startDate, $endDate, $mrdno)
{
    $query = CancelbillFormModel::select(
        'tbl_locations.name as location_name',
        'tblzones.name as zone_name',
        'hms_cancelbill_form.*'
    )
        ->join('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
        ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->whereDate('hms_cancelbill_form.created_at', '>=', $startDate)
        ->whereDate('hms_cancelbill_form.created_at', '<=', $endDate);

    if ($hasZone && isset($values[0])) {
        $query->where('tblzones.name', $values[0]);
    }

    if ($hasLocation && isset($values[1])) {
        $query->where('tbl_locations.name', $values[1]);
    }
    // if ($mrdno) {
    //     $query->where('can_mrdno', $mrdno);
    // }
    return $query->get()->toArray();
}

// public function cancelformsave_data(Request $request)
// {
//     $user = auth()->user();

//     // check approver
//     $approver = DB::table('cancel_bill_approver')
//         ->where('user_id', $user->id)
//         ->first();
//     // $fitterremovedataall = $request->input('morefilltersall');
//     // $datefiltervalue     = $request->input('moredatefittervale');

//     // $dates = explode(' - ', $datefiltervalue);
//     // $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
//     // $endDate   = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();

//     $query = CancelbillFormModel::select(
//             'tbl_locations.name as location_name',
//             'tblzones.name as zone_name',
//             'hms_cancelbill_form.*'
//         )
//         ->join('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
//         ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id');
//         // ->whereBetween('hms_cancelbill_form.can_date', [$startDate, $endDate]);
//     // 🔑 IMPORTANT LOGIC

//     // if (!$approver) {
//     //     // not an approver → show only own bills
//     //     $query->where('hms_cancelbill_form.created_by', $user->id);
//     // }
//     if ($user->access_limits == 1 || $approver) {
//         // ✅ SUPERADMIN: Access to ALL locations, branches, and ALL created_by records
//         // No additional filters needed
//         // $data->where('hms_discount_form.zonal_approver', 1);
//         \Log::info('Superadmin access - All data visible');
//     } elseif ($user->access_limits == 2) {
//         // ✅ ADMIN: Access only to branches under their zone
//         if (!empty($user->zone_id)) {
//             $query->where('tblzones.id', $user->zone_id);

//             // Admin sees all records created by users in their zone's branches
//             // Get all branch IDs under admin's zone
//             $branchIds = \DB::table('tbl_locations')
//                 ->where('zone_id', $user->zone_id)
//                 ->pluck('id')
//                 ->toArray();
//             // // Filter by created_by (users in admin's zone)
//             if (!empty($branchIds)) {
//                 $query->whereIn('hms_cancelbill_form.can_zone_id', $branchIds);
//             } else {
//                 // If no users found in zone, return empty
//                 return response()->json([]);
//             }

//             // \Log::info('Admin access - Zone ID: ' . $admin->zone_id . ', User IDs: ' . implode(',', $userIds));
//         } else {
//             // If admin has no zone assigned, return empty result
//             return response()->json([]);
//         }

//     } elseif ($user->access_limits == 3) {
//         $query->where('hms_cancelbill_form.created_by', $user->id);
//         \Log::info('User access - Branch ID: ' . $user->branch_id . ', Created By: ' . $user->id);
//     }
//     // additional filters
//     // if ($fitterremovedataall) {
//     //     foreach (explode(' AND ', $fitterremovedataall) as $condition) {
//     //         [$column, $value] = explode('=', $condition);
//     //         $query->whereIn(trim($column), explode(',', trim($value, "'")));
//     //     }
//     // }

//     $data = $query->orderBy('hms_cancelbill_form.created_at', 'desc')->get();
//     // dd($data);
//     return response()->json([
//         'data'       => $data,
//         'isApprover' => (bool) $approver
//     ]);
// }
// public function cancelformsave_data(Request $request)
// {
//     $user = auth()->user();

//     // check approver (boolean)
//     $isApprover = DB::table('cancel_bill_approver')
//         ->where('user_id', $user->id)
//         ->exists();

//     $query = CancelbillFormModel::select(
//             'tbl_locations.name as location_name',
//             'tblzones.name as zone_name',
//             'hms_cancelbill_form.*'
//         )
//         ->join('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
//         ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id');

//     /* 🔐 ACCESS CONTROL */
//     if ($user->access_limits == 1 || $isApprover) {
//         // ✅ SUPERADMIN / APPROVER → ALL DATA
//         \Log::info('Approver/Superadmin access - All cancel bills visible');

//     } elseif ($user->access_limits == 2) {
//         // ✅ ADMIN → zone branches + multi-location branches

//         $branchIds = [];

//         /* 1️⃣ Branches under admin's zone */
//         if (!empty($user->zone_id)) {
//             $zoneBranchIds = DB::table('tbl_locations')
//                 ->where('zone_id', $user->zone_id)
//                 ->pluck('id')
//                 ->toArray();

//             $branchIds = array_merge($branchIds, $zoneBranchIds);
//         }

//         /* 2️⃣ Multi-location branches (comma-separated IDs) */
//         if (!empty($user->multi_location)) {
//             $multiLocationIds = array_map(
//                 'intval',
//                 explode(',', $user->multi_location)
//             );

//             $branchIds = array_merge($branchIds, $multiLocationIds);
//         }

//         /* 3️⃣ Remove duplicates */
//         $branchIds = array_unique($branchIds);

//         /* 4️⃣ Apply filter */
//         if (!empty($branchIds)) {
//             $query->whereIn('hms_cancelbill_form.can_zone_id', $branchIds);
//         } else {
//             return response()->json([
//                 'data' => [],
//                 'isApprover' => $isApprover
//             ]);
//         }
//     } elseif ($user->access_limits == 3) {
//         $branchIds = [];
//         if (!empty($user->multi_location)) {
//             $multiLocationIds = array_map(
//                 'intval',
//                 explode(',', $user->multi_location)
//             );

//             $branchIds = array_merge($branchIds, $multiLocationIds);
//         }
//         $branchIds = array_unique($branchIds);
//         if (!empty($branchIds)) {
//             $query->whereIn('hms_cancelbill_form.can_zone_id', $branchIds);
//         }
//         // NORMAL USER → own records only
//         $query->where('hms_cancelbill_form.created_by', $user->id);
//     }

//     $data = $query
//         ->orderBy('hms_cancelbill_form.created_at', 'desc')
//         ->get();
//     return response()->json([
//         'data'       => $data,
//         'isApprover' => $isApprover
//     ]);
// }
public function cancelformsave_data(Request $request)
{
    $user = auth()->user();
    $fitterremovedataall = $request->input('morefilltersall');
    $datefiltervalue     = $request->input('moredatefittervale');

    $applyDateFilter = false;

    if (!empty($datefiltervalue)) {
        if (!empty($fitterremovedataall) ) {
            $applyDateFilter = true;
        }
    }

    // 🔐 Check approver
    $isApprover = DB::table('cancel_bill_approver')
        ->where('user_id', $user->id)
        ->exists();

    // ============================================
    // BASE QUERY
    // ============================================
    $data = CancelbillFormModel::select(
        'tbl_locations.name as location_name',
        'tblzones.name as zone_name',
        'users.user_fullname as username',
        'users.username as userid',
        'hms_cancelbill_form.*',
        'admin_approver_user.user_fullname as admin_approver_name',
        'zonal_approver_user.user_fullname as zonal_approver_name',
        'audit_approver_user.user_fullname as audit_approver_name',
        'final_approver_user.user_fullname as final_approver_name'
    )
    ->leftjoin('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
    ->leftjoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
    ->leftjoin('users', 'hms_cancelbill_form.created_by', '=', 'users.id')
    ->leftJoin('users as admin_approver_user', 'hms_cancelbill_form.admin_head_id', '=', 'admin_approver_user.id')
    ->leftJoin('users as zonal_approver_user', 'hms_cancelbill_form.zonal_head_id', '=', 'zonal_approver_user.id')
    ->leftJoin('users as audit_approver_user', 'hms_cancelbill_form.audit_head_id', '=', 'audit_approver_user.id')
    ->leftJoin('users as final_approver_user', 'hms_cancelbill_form.final_approver_id', '=', 'final_approver_user.id');

    if ($user->access_limits == 1 || $isApprover || $user->access_limits == 4) {
        // SUPERADMIN / APPROVER → ALL DATA
        \Log::info('Approver/Superadmin access - All cancel bills visible');

    } elseif ($user->access_limits == 2) {
        // ADMIN → zone + multi-location branches
        $branchIds = [];

        if (!empty($user->zone_id)) {
            $zoneBranchIds = DB::table('tbl_locations')
                ->where('zone_id', $user->zone_id)
                ->pluck('id')
                ->toArray();

            $branchIds = array_merge($branchIds, $zoneBranchIds);
        }

        if (!empty($user->multi_location)) {
            $multiLocationIds = array_map(
                'intval',
                explode(',', $user->multi_location)
            );

            $branchIds = array_merge($branchIds, $multiLocationIds);
        }

        $branchIds = array_unique($branchIds);
        if (!empty($branchIds)) {
            $data->whereIn('hms_cancelbill_form.can_zone_id', $branchIds);
        }

        $data->where('hms_cancelbill_form.created_by', $user->id);
        
    } elseif ($user->access_limits == 3 && $user->access_limits == 5) {
        // NORMAL USER
        $branchIds = [];

        if (!empty($user->multi_location)) {
            $multiLocationIds = array_map(
                'intval',
                explode(',', $user->multi_location)
            );

            $branchIds = array_merge($branchIds, $multiLocationIds);
        }

        $branchIds = array_unique($branchIds);

        if (!empty($branchIds)) {
            $data->whereIn('hms_cancelbill_form.can_zone_id', $branchIds);
        }

    }

    if ($applyDateFilter) {
        $dates = explode(' - ', $datefiltervalue);
        if (count($dates) === 2) {
            $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
            $endDate   = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();

            $data->whereBetween('hms_cancelbill_form.can_date', [$startDate, $endDate]);
        }
    }

    if ($fitterremovedataall) {
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            $parts = explode('=', $condition, 2);
            if (count($parts) < 2) continue;
            $column = trim($parts[0]);
            $value  = trim($parts[1], "'");
            // phid is MRD - hms_cancelbill_form uses can_mrdno, not phid
            if ($column === 'phid' || str_ends_with($column, '.phid')) {
                if (!empty($value)) {
                    $data->where('hms_cancelbill_form.can_mrdno', 'LIKE', '%' . $value . '%');
                }
                continue;
            }

            $data->whereIn($column, explode(',', $value));
        }
    }

    $statusFilter = $request->input('status_filter');
    if (!empty($statusFilter)) {
        $pendingColumnMap = [
            1 => 'final_approver',
            2 => 'zonal_approver',
            3 => 'admin_approver',
            4 => 'audit_approver',
        ];
        $pendingCol = $pendingColumnMap[$user->access_limits] ?? 'final_approver';
        $colPrefix = 'hms_cancelbill_form.';
        if (in_array($statusFilter, ['approved', 'pending', 'rejected'])) {
            $val = $statusFilter === 'approved' ? 1 : ($statusFilter === 'rejected' ? 2 : 0);
            $data->where($colPrefix . $pendingCol, '=', $val);
        } elseif (preg_match('/^(final|admin|zonal|audit)_(approved|pending|rejected)$/', $statusFilter, $m)) {
            $col = $m[1] . '_approver';
            if ($m[1] === 'final') $col = 'final_approver';
            elseif ($m[1] === 'admin') $col = 'admin_approver';
            elseif ($m[1] === 'zonal') $col = 'zonal_approver';
            else $col = 'audit_approver';
            $val = $m[2] === 'approved' ? 1 : ($m[2] === 'rejected' ? 2 : 0);
            $data->where($colPrefix . $col, '=', $val);
        }
    }

    // Clone before execution
    $baseQuery = clone $data;
    $results = $data
        ->orderBy('hms_cancelbill_form.created_at', 'desc')
        ->get();
    $pendingColumnMap = [
        1 => 'final_approver',
        2 => 'zonal_approver',
        3 => 'admin_approver',
        4 => 'audit_approver',
    ];

    $pendingColumn = $pendingColumnMap[$user->access_limits] ?? null;

    $totalRaised   = (clone $baseQuery)->count();
    $adminApproved = (clone $baseQuery)->where('admin_approver', 1)->count();
    $zonalApproved = (clone $baseQuery)->where('zonal_approver', 1)->count();
    $auditApproved = (clone $baseQuery)->where('audit_approver', 1)->count();
    $finalApproved = (clone $baseQuery)->where('final_approver', 1)->count();

    $pendingFinal = 0;
    if ($pendingColumn) {
        $pendingFinal = (clone $baseQuery)
            ->where($pendingColumn, 0)
            ->count();
    }

    // ============================================
    // RESPONSE
    // ============================================
    $totalAmountCancelled = (clone $baseQuery)
        ->where('final_approver', 1)
        ->get()
        ->sum(fn ($r) => (float) ($r->can_total ?? 0));

    return response()->json([
        'data'       => $results,
        'isApprover' => $isApprover,
        'counts'     => [
            'total_raised'         => $totalRaised,
            'admin_approved'       => $adminApproved,
            'zonal_approved'       => $zonalApproved,
            'audit_approved'       => $auditApproved,
            'final_approved'       => $finalApproved,
            'pending'              => $pendingFinal,
            'processing'           => $totalRaised - ($finalApproved + $pendingFinal),
            'total_cancel_amount' => round($totalAmountCancelled, 2),
        ],
        'statistics' => [
            [
                'label' => 'Total Raised',
                'count' => $totalRaised,
                'color' => '#FF6B6B',
                'icon'  => 'file-text'
            ],
            [
                'label' => 'Admin Approved',
                'count' => $adminApproved,
                'color' => '#4ECDC4',
                'icon'  => 'check-circle'
            ],
            [
                'label' => 'Zonal Approved',
                'count' => $zonalApproved,
                'color' => '#45B7D1',
                'icon'  => 'check-circle'
            ],
            [
                'label' => 'Audit Approved',
                'count' => $auditApproved,
                'color' => '#96CEB4',
                'icon'  => 'shield-check'
            ],
            [
                'label' => 'Final Approved',
                'count' => $finalApproved,
                'color' => '#51CF66',
                'icon'  => 'check-double'
            ],
            [
                'label' => 'Pending',
                'count' => $pendingFinal,
                'color' => '#FFA94D',
                'icon'  => 'clock'
            ],
            [
                'label' => 'Total Cancelled',
                'count' => round($totalAmountCancelled, 2),
                'color' => '#9775FA',
                'icon'  => 'currency-rupee'
            ],
        ]
    ]);
}

public function refundformapi_detials(Request $request)
{
    set_time_limit(0);

    $fitterremovedataall = $request->input('morefilltersall');
    $datefiltervalue = $request->input('moredatefittervale');
    $mrdno = $request->input('mrodnofilter');

    // Handle date range
    $dates = explode(' - ', $datefiltervalue);
    $startDate = $dates[0] ?? '';
    $endDate = $dates[1] ?? '';
    $startDateFormatted = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    $endDateFormatted = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
    $start_date = Carbon::parse($startDateFormatted)->format('Ymd') . '00:00:00';
    $end_date = Carbon::parse($endDateFormatted)->format('Ymd') . '23:59:59';

    $locations = $this->cityArray();
    $checkinData = [];

    // Extract quoted values from the filter string
    preg_match_all("/'([^']+)'/", $fitterremovedataall, $matches);
    $values = $matches[1];

    // Parse presence of filters
    $hasZone = strpos($fitterremovedataall, 'tblzones.name') !== false;
    $hasLocation = strpos($fitterremovedataall, 'tbl_locations.name') !== false;
    $hasPhid = strpos($fitterremovedataall, 'phid') !== false;
    // Case 1: Zone + Location + PHID
    if ($hasZone && $hasLocation && $hasPhid) {
        $zoneName = $values[0];
        $locationName = $values[1];

        $zone = TblZonesModel::where('name', $zoneName)->first();
        $location = TblLocationModel::where('name', $locationName)->where('zone_id', $zone->id)->first();

        if ($location) {
            $locationKey = array_search($location->name, $locations);
            $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

            foreach ($apiData['checkinlist'] ?? [] as $checkin) {
                if ($checkin['patient']['phid'] == $mrdno) {
                    $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $locationName, $zoneName, $location->id);
                }
            }
        }


    } // Case: Zone + Location (without PHID)
  elseif ($hasZone && $hasLocation && !$hasPhid) {
    $zoneName = $values[0];
    $locationName = $values[1];

    $zone = TblZonesModel::where('name', $zoneName)->first();
    $location = TblLocationModel::where('name', $locationName)->where('zone_id', $zone->id)->first();

    if ($location) {
        $locationKey = array_search($location->name, $locations);
        $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

        foreach ($apiData['checkinlist'] ?? [] as $checkin) {
            $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $locationName, $zoneName, $location->id);
        }
       }
        // Case 2: Location + PHID
   }elseif ($hasLocation && $hasPhid && !$hasZone) {
        $locationName = $values[0];

        $location = TblLocationModel::where('name', $locationName)->first();
        $zone = TblZonesModel::find($location->zone_id);
        $zoneName = $zone->name ?? '';
        $locationKey = array_search($locationName, $locations);

        $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

        foreach ($apiData['checkinlist'] ?? [] as $checkin) {
            if ($checkin['patient']['phid'] == $mrdno) {
                $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $locationName, $zoneName, $location->id);
            }
        }

    // Case 3: Zone + PHID
    } elseif ($hasZone && $hasPhid && !$hasLocation) {
        $zoneName = $values[0];
        $zoneLocations = $this->selectQuerylocation($zoneName);

        foreach ($zoneLocations as $zoneLocation) {
            $locationKey = array_search($zoneLocation->name, $locations);
            $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

            foreach ($apiData['checkinlist'] ?? [] as $checkin) {
                if ($checkin['patient']['phid'] == $mrdno) {
                    $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $zoneLocation->name, $zoneName,  $zoneLocation->id);
                }
            }
        }

    // Case 4: Only Location
    } elseif ($hasLocation && !$hasZone && !$hasPhid) {
        $locationName = $values[0];

        $location = TblLocationModel::where('name', $locationName)->first();
        $zone = TblZonesModel::find($location->zone_id);
        $zoneName = $zone->name ?? '';
        $locationKey = array_search($locationName, $locations);

        $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);
        foreach ($apiData['checkinlist'] ?? [] as $checkin) {
            $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $locationName, $zoneName,$location->id);
        }

    // Case 5: Only Zone
    } elseif ($hasZone && !$hasLocation && !$hasPhid) {
        $zoneName = $values[0];
        $zoneLocations = $this->selectQuerylocation($zoneName);

        foreach ($zoneLocations as $zoneLocation) {
            $locationKey = array_search($zoneLocation->name, $locations);
            $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

            foreach ($apiData['checkinlist'] ?? [] as $checkin) {
                $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $zoneLocation->name, $zoneName,  $zoneLocation->id);
            }
        }

    // Case 6: Only PHID
    } elseif ($hasPhid && !$hasZone && !$hasLocation) {
        foreach ($locations as $key => $locationName) {
            $location = TblLocationModel::where('name', $locationName)->first();
            $zone = TblZonesModel::find($location->zone_id);
            $zoneName = $zone->name ?? '';

            $apiData = $this->mrdnoapi($start_date, $key, 5, $end_date);

            foreach ($apiData['checkinlist'] ?? [] as $checkin) {
                if ($checkin['patient']['phid'] == $mrdno) {
                    $checkinData[] = $this->saveCurlDatas($checkin, $key, $locationName, $zoneName, $location->id);
                }
            }
        }

    // Case 7: No filter — use default location
    } else {
        $defaultLocation = 'Chennai - Sholinganallur';
        $locationKey = array_search($defaultLocation, $locations);
        $location = TblLocationModel::where('name', $defaultLocation)->first();
        $zone = TblZonesModel::find($location->zone_id);
        $zoneName = $zone->name ?? '';

        $apiData = $this->mrdnoapi($start_date, $locationKey, 5, $end_date);

        foreach ($apiData['checkinlist'] ?? [] as $checkin) {
            $checkinData[] = $this->saveCurlDatas($checkin, $locationKey, $defaultLocation, $zoneName, $location->id);
        }
    }

  // Build the base query
$query = RefundFormModel::select(
    'tbl_locations.name as location_name',
    'tblzones.name as zone_name',
    'hms_refund_form.*'
)
->join('tbl_locations', 'hms_refund_form.ref_zone_id', '=', 'tbl_locations.id')
->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
->whereDate('hms_refund_form.created_at', '>=', $startDateFormatted)
->whereDate('hms_refund_form.created_at', '<=', $endDateFormatted);

// Apply filters BEFORE executing the query
if ($hasZone && isset($values[0])) {
    $zoneName = $values[0];
    $query->where('tblzones.name', $zoneName);
}

if ($hasLocation && isset($values[1])) {
    $locationName = $values[1];
    $query->where('tbl_locations.name', $locationName);
} elseif ($hasLocation && isset($values[0]) && !$hasZone) {
    $locationName = $values[0];
    $query->where('tbl_locations.name', $locationName);

    $location = TblLocationModel::where('name', $locationName)->first();
    if ($location) {
        $zone = TblZonesModel::find($location->zone_id);
        if ($zone) {
            $zoneName = $zone->name;
            $query->where('tblzones.name', $zoneName);
        }
    }
} elseif (!$hasZone && !$hasLocation) {
    // No zone/location filters applied — use default location
    $defaultLocation = 'Chennai - Sholinganallur';
    $location = TblLocationModel::where('name', $defaultLocation)->first();
    if ($location) {
        $zone = TblZonesModel::find($location->zone_id);
        $zoneName = $zone->name ?? '';
        $query->where('tbl_locations.name', $defaultLocation);
        $query->where('tblzones.name', $zoneName);
    }
}

if ($mrdno) {
    $query->where(function ($q) use ($mrdno) {
        $q->where('ref_wife_mrd_no', $mrdno)
          ->orWhere('ref_husband_mrd_no', $mrdno);
    });
}

$existingData = $query->get()->toArray();
$mergedData = [];
// Step 1: Build a lookup of all mrd numbers from DB
$mrdLookup = [];
foreach ($existingData as $record) {
    $record['is_saved'] = true;
    $mergedData[] = $record;
    if (!empty($record['ref_wife_mrd_no'])) {
        $mrdLookup[$record['ref_wife_mrd_no']] = true;
    }
    if (!empty($record['ref_husband_mrd_no'])) {
        $mrdLookup[$record['ref_husband_mrd_no']] = true;
    }
}

// Step 2: Add API data only if PHID not matched with any DB MRD
foreach ($checkinData as $apiEntry) {
    $phid = $apiEntry['phid'] ?? null;
    if ($phid && !isset($mrdLookup[$phid])) {
        $apiEntry['is_saved'] = false;
        $mergedData[] = $apiEntry;
    }
}

return response()->json([
    'checkinData' => array_values($mergedData),
]);
}

public function refundform_data(Request $request){
    // Single record by MRD (for backward compatibility)
    if ($request->filled('ref_wife_mrd_no') && $request->filled('ref_husband_mrd_no')) {
        $record = RefundFormModel::where('ref_wife_mrd_no', $request->ref_wife_mrd_no)->where('ref_husband_mrd_no', $request->ref_husband_mrd_no)->first();
        return response()->json([$record]);
    }

    // Dashboard saved list + stats. Initially show all data; when moredatefittervale is sent, filter by date.
    $user = auth()->user();
    $fitterremovedataall = $request->input('morefilltersall');
    $datefiltervalue = trim((string) $request->input('moredatefittervale', ''));
    $phid = $request->input('mrodnofilter');

    $applyDateFilter = false;
    $startDate = null;
    $endDate = null;
    if ($datefiltervalue !== '' && strtolower($datefiltervalue) !== 'all') {
        $dates = array_map('trim', explode(' - ', $datefiltervalue));
        $startStr = $dates[0] ?? '';
        $endStr = (isset($dates[1]) && $dates[1] !== '') ? $dates[1] : $startStr;
        if ($startStr !== '') {
            try {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $startStr)->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $endStr)->endOfDay();
                if ($endDate->lt($startDate)) {
                    $endDate = $startDate->copy()->endOfDay();
                }
                $applyDateFilter = true;
            } catch (\Exception $e) {
                \Log::warning('refundform_data: invalid moredatefittervale, loading all (no date filter)', [
                    'moredatefittervale' => $datefiltervalue,
                    'error' => $e->getMessage(),
                ]);
                $applyDateFilter = false;
                $startDate = null;
                $endDate = null;
            }
        }
    }

    $data = RefundFormModel::select(
        'tbl_locations.name as location_name',
        'tblzones.name as zone_name',
        'users.user_fullname as created_by_name',
        'hms_refund_form.*',
        'admin_approver_user.user_fullname as admin_approver_name',
        'zonal_approver_user.user_fullname as zonal_approver_name',
        'audit_approver_user.user_fullname as audit_approver_name',
        'final_approver_user.user_fullname as final_approver_name'
    )
        ->leftJoin('tbl_locations', 'hms_refund_form.ref_zone_id', '=', 'tbl_locations.id')
        ->leftJoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->leftJoin('users', 'hms_refund_form.created_by', '=', 'users.id')
        ->leftJoin('users as admin_approver_user', 'hms_refund_form.admin_approved_by', '=', 'admin_approver_user.id')
        ->leftJoin('users as zonal_approver_user', 'hms_refund_form.zonal_approved_by', '=', 'zonal_approver_user.id')
        ->leftJoin('users as audit_approver_user', 'hms_refund_form.audit_approved_by', '=', 'audit_approver_user.id')
        ->leftJoin('users as final_approver_user', 'hms_refund_form.final_approved_by', '=', 'final_approver_user.id');

    if ($applyDateFilter && $startDate && $endDate) {
        $data->whereBetween('hms_refund_form.created_at', [$startDate, $endDate]);
    }

    if ($user->access_limits == 2 || $user->access_limits == 4) {
        $branchIds = [];
        if (!empty($user->zone_id)) {
            $branchIds = DB::table('tbl_locations')->where('zone_id', $user->zone_id)->pluck('id')->toArray();
        }
        if (!empty($user->multi_location)) {
            $branchIds = array_merge($branchIds, array_map('intval', explode(',', $user->multi_location)));
        }
        $branchIds = array_unique($branchIds);
        if (!empty($branchIds)) {
            $data->whereIn('hms_refund_form.ref_zone_id', $branchIds);
        }
    } elseif ($user->access_limits == 3 || $user->access_limits == 5) {
        $branchIds = !empty($user->multi_location) ? array_map('intval', explode(',', $user->multi_location)) : [];
        if (!empty($branchIds)) {
            $data->whereIn('hms_refund_form.ref_zone_id', $branchIds);
        }
        $data->where('hms_refund_form.created_by', $user->id);
    }

    if ($fitterremovedataall) {
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            $parts = explode('=', $condition, 2);
            if (count($parts) < 2) continue;
            $col = trim($parts[0]);
            $val = trim($parts[1], "'");
            if ($col === 'phid' || str_ends_with($col, '.phid')) continue;
            $data->whereIn($col, explode(',', $val));
        }
    }
    if (!empty($phid)) {
        $data->where(function ($q) use ($phid) {
            $q->where('ref_wife_mrd_no', 'LIKE', "%$phid%")->orWhere('ref_husband_mrd_no', 'LIKE', "%$phid%");
        });
    }

    $statusFilter = $request->input('status_filter');
    if (!empty($statusFilter)) {
        $pendingColumnMap = [1 => 'final_approver', 2 => 'zonal_approver', 3 => 'admin_approver', 4 => 'audit_approver'];
        $pendingCol = $pendingColumnMap[$user->access_limits] ?? 'final_approver';
        $colPrefix = 'hms_refund_form.';
        if (in_array($statusFilter, ['approved', 'pending', 'rejected'])) {
            $val = $statusFilter === 'approved' ? 1 : ($statusFilter === 'rejected' ? 2 : 0);
            $data->where($colPrefix . $pendingCol, '=', $val);
        } elseif (preg_match('/^(final|admin|zonal|audit)_(approved|pending|rejected)$/', $statusFilter, $m)) {
            $col = $m[1] . '_approver';
            if ($m[1] === 'final') $col = 'final_approver';
            elseif ($m[1] === 'admin') $col = 'admin_approver';
            elseif ($m[1] === 'zonal') $col = 'zonal_approver';
            else $col = 'audit_approver';
            $val = $m[2] === 'approved' ? 1 : ($m[2] === 'rejected' ? 2 : 0);
            $data->where($colPrefix . $col, '=', $val);
        }
    }

    $baseQuery = clone $data;
    $results = $data->orderBy('hms_refund_form.created_at', 'desc')->get();

    $pendingColumnMap = [1 => 'final_approver', 2 => 'zonal_approver', 3 => 'admin_approver', 4 => 'audit_approver'];
    $pendingCol = $pendingColumnMap[$user->access_limits] ?? null;

    $totalRaised = (clone $baseQuery)->count();
    $adminApproved = (clone $baseQuery)->where('admin_approver', 1)->count();
    $zonalApproved = (clone $baseQuery)->where('zonal_approver', 1)->count();
    $auditApproved = (clone $baseQuery)->where('audit_approver', 1)->count();
    $finalApproved = (clone $baseQuery)->where('final_approver', 1)->count();
    $pendingFinal = $pendingCol ? (clone $baseQuery)->where($pendingCol, 0)->count() : 0;
    $totalRefundAmount = (clone $baseQuery)->where('final_approver', 1)->get()->sum(fn($r) => (float)($r->ref_final_auth ?? 0));

    $isApprover = in_array((int)$user->access_limits, [1, 2, 3, 4], true);

    return response()->json([
        'data' => $results,
        'isApprover' => $isApprover,
        'counts' => [
            'total_raised' => $totalRaised,
            'admin_approved' => $adminApproved,
            'zonal_approved' => $zonalApproved,
            'audit_approved' => $auditApproved,
            'final_approved' => $finalApproved,
            'pending' => $pendingFinal,
            'total_refund_amount' => round($totalRefundAmount, 2),
        ],
        'statistics' => [
            ['label' => 'Total Raised', 'count' => $totalRaised],
            ['label' => 'Admin Approved', 'count' => $adminApproved],
            ['label' => 'Zonal Approved', 'count' => $zonalApproved],
            ['label' => 'Audit Approved', 'count' => $auditApproved],
            ['label' => 'Final Approved', 'count' => $finalApproved],
            ['label' => 'Pending', 'count' => $pendingFinal],
            ['label' => 'Total Refund', 'count' => round($totalRefundAmount, 2)],
        ]
    ]);
}


public function refundform_edit(Request $request){
    // Fetch by ref_id (for dashboard view/edit/print)
    if ($request->filled('ref_id')) {
        $record = RefundFormModel::select('tbl_locations.name as location_name', 'tblzones.name as zone_name', 'hms_refund_form.*')
            ->leftJoin('tbl_locations', 'hms_refund_form.ref_zone_id', '=', 'tbl_locations.id')
            ->leftJoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->where('hms_refund_form.ref_id', $request->ref_id)
            ->first();
        return response()->json([
            'exists' => $record ? true : false,
            'record' => $record
        ]);
    }
    $wifeMRD = $request->input('ref_wife_mrd_no');
    $husMRD = $request->input('ref_husband_mrd_no');
    $record = RefundFormModel::where('ref_wife_mrd_no', $wifeMRD)->where('ref_husband_mrd_no', $husMRD)->first();
    return response()->json([
        'exists' => $record ? true : false,
        'record' => $record
    ]);
}

/**
 * Add or update refund form. Used by refund dashboard and old refund form.
 * Sets created_by on create.
 */
public function refund_documentadded(Request $request)
{
    $user = auth()->user();

    $validatedData = $request->validate([
        'ref_zone_id' => 'required|string|max:255',
        'ref_wife_mrd_no' => 'nullable|string|max:255',
        'ref_husband_mrd_no' => 'nullable|string|max:255',
        'ref_service_name' => 'nullable|string|max:255',
        'ref_wife_name' => 'nullable|string|max:255',
        'ref_husband_name' => 'nullable|string|max:255',
        'ref_patient_ph' => 'nullable|string|max:255',
        'ref_total_bill' => 'nullable|string|max:255',
        'ref_expected_request' => 'nullable|string|max:255',
        'ref_counselled_by' => 'nullable|string|max:255',
        'ref_final_auth' => 'nullable|string|max:255',
        'ref_branch_no' => 'nullable|string|max:255',
        'ref_auth_by' => 'nullable|string|max:255',
        'ref_approved_by' => 'nullable|string|max:255',
        'ref_form_status' => 'nullable|string|max:255',

        'ref_wife_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'ref_husband_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'ref_drsign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'ref_cc_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'ref_admin_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'ref_zonal_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
    ]);

    $signatureFields = [
        'ref_wife_sign',
        'ref_husband_sign',
        'ref_drsign',
        'ref_cc_sign',
        'ref_admin_sign',
        'ref_zonal_sign'
    ];

    $imagePaths = [];

    foreach ($signatureFields as $field) {

        if ($request->hasFile($field)) {

            $image = $request->file($field);
            $filename = time().'_'.$field.'_'.uniqid().'.'.$image->getClientOriginalExtension();

            $destination = public_path('refund_form');

            if (!is_dir($destination)) {
                mkdir($destination,0755,true);
            }

            $image->move($destination,$filename);

            $imagePaths[$field] = json_encode(['refund_form/'.$filename]);

        } else {
            $imagePaths[$field] = json_encode([]);
        }
    }

    // ---------- SAVE ----------
    $model = new RefundFormModel();

    $model->fill($validatedData);

    $model->ref_wife_sign    = $imagePaths['ref_wife_sign'];
    $model->ref_husband_sign = $imagePaths['ref_husband_sign'];
    $model->ref_drsign       = $imagePaths['ref_drsign'];
    $model->ref_cc_sign      = $imagePaths['ref_cc_sign'];
    $model->ref_admin_sign   = $imagePaths['ref_admin_sign'];
    $model->ref_zonal_sign   = $imagePaths['ref_zonal_sign'];

    $model->created_by = $user->id;
    $model->status = 1;

    $model->save();

    return response()->json([
        'success' => true,
        'message' => 'Refund form saved successfully!'
    ]);
}
public function refundformeditsave(Request $request)
{
    $user = auth()->user();
    $refId = $request->input('ref_id');

    $validatedData = $request->validate([
        'ref_zone_id' => 'required|string|max:255',
        'ref_wife_mrd_no' => 'nullable|string|max:255',
        'ref_husband_mrd_no' => 'nullable|string|max:255',
        'ref_service_name' => 'nullable|string|max:255',
        'ref_wife_name' => 'nullable|string|max:255',
        'ref_husband_name' => 'nullable|string|max:255',
        'ref_patient_ph' => 'nullable|string|max:255',
        'ref_total_bill' => 'nullable|string|max:255',
        'ref_expected_request' => 'nullable|string|max:255',
        'ref_counselled_by' => 'nullable|string|max:255',
        'ref_final_auth' => 'nullable|string|max:255',
        'ref_branch_no' => 'nullable|string|max:255',
        'ref_auth_by' => 'nullable|string|max:255',
        'ref_approved_by' => 'nullable|string|max:255',
        'ref_form_status' => 'nullable|string|max:255',

        'ref_wife_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'ref_husband_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'ref_drsign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'ref_cc_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'ref_admin_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
        'ref_zonal_sign' => 'nullable|mimes:jpeg,png,jpg|max:12048',
    ]);

    $model = RefundFormModel::find($refId);

    if(!$model){
        return response()->json([
            'success'=>false,
            'message'=>'Refund form not found'
        ]);
    }

    $signatureFields = [
        'ref_wife_sign',
        'ref_husband_sign',
        'ref_drsign',
        'ref_cc_sign',
        'ref_admin_sign',
        'ref_zonal_sign'
    ];

    $imagePaths = [];

    foreach ($signatureFields as $field) {

        if ($request->hasFile($field)) {

            $image = $request->file($field);
            $filename = time().'_'.$field.'_'.uniqid().'.'.$image->getClientOriginalExtension();

            $destination = public_path('refund_form');

            if (!is_dir($destination)) {
                mkdir($destination,0755,true);
            }

            $image->move($destination,$filename);

            $imagePaths[$field] = json_encode(['refund_form/'.$filename]);

        } else {

            $imagePaths[$field] = $model->$field;
        }
    }

    // ---------- UPDATE ----------
    $model->fill($validatedData);

    $model->ref_wife_sign    = $imagePaths['ref_wife_sign'];
    $model->ref_husband_sign = $imagePaths['ref_husband_sign'];
    $model->ref_drsign       = $imagePaths['ref_drsign'];
    $model->ref_cc_sign      = $imagePaths['ref_cc_sign'];
    $model->ref_admin_sign   = $imagePaths['ref_admin_sign'];
    $model->ref_zonal_sign   = $imagePaths['ref_zonal_sign'];

    $model->updated_by = $user->id;

    $model->save();

    return response()->json([
        'success' => true,
        'message' => 'Refund form updated successfully!',
        'updatedRecord' => $this->refundFormRecordForGrid($model->ref_id),
    ]);
}

/**
 * Single refund row for saved grid (same shape as refundform_data items).
 */
private function refundFormRecordForGrid($refId)
{
    if (empty($refId)) {
        return null;
    }

    return RefundFormModel::select(
        'tbl_locations.name as location_name',
        'tblzones.name as zone_name',
        'users.user_fullname as created_by_name',
        'hms_refund_form.*',
        'admin_approver_user.user_fullname as admin_approver_name',
        'zonal_approver_user.user_fullname as zonal_approver_name',
        'audit_approver_user.user_fullname as audit_approver_name',
        'final_approver_user.user_fullname as final_approver_name'
    )
        ->leftJoin('tbl_locations', 'hms_refund_form.ref_zone_id', '=', 'tbl_locations.id')
        ->leftJoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->leftJoin('users', 'hms_refund_form.created_by', '=', 'users.id')
        ->leftJoin('users as admin_approver_user', 'hms_refund_form.admin_approved_by', '=', 'admin_approver_user.id')
        ->leftJoin('users as zonal_approver_user', 'hms_refund_form.zonal_approved_by', '=', 'zonal_approver_user.id')
        ->leftJoin('users as audit_approver_user', 'hms_refund_form.audit_approved_by', '=', 'audit_approver_user.id')
        ->leftJoin('users as final_approver_user', 'hms_refund_form.final_approved_by', '=', 'final_approver_user.id')
        ->where('hms_refund_form.ref_id', $refId)
        ->first();
}

/**
 * Single cancel bill row for saved grid (same shape as cancelformsave_data items).
 */
private function cancelBillRecordForGrid($canId)
{
    if (empty($canId)) {
        return null;
    }

    return CancelbillFormModel::select(
        'tbl_locations.name as location_name',
        'tblzones.name as zone_name',
        'users.user_fullname as username',
        'users.username as userid',
        'hms_cancelbill_form.*',
        'admin_approver_user.user_fullname as admin_approver_name',
        'zonal_approver_user.user_fullname as zonal_approver_name',
        'audit_approver_user.user_fullname as audit_approver_name',
        'final_approver_user.user_fullname as final_approver_name'
    )
        ->leftjoin('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
        ->leftjoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->leftjoin('users', 'hms_cancelbill_form.created_by', '=', 'users.id')
        ->leftJoin('users as admin_approver_user', 'hms_cancelbill_form.admin_head_id', '=', 'admin_approver_user.id')
        ->leftJoin('users as zonal_approver_user', 'hms_cancelbill_form.zonal_head_id', '=', 'zonal_approver_user.id')
        ->leftJoin('users as audit_approver_user', 'hms_cancelbill_form.audit_head_id', '=', 'audit_approver_user.id')
        ->leftJoin('users as final_approver_user', 'hms_cancelbill_form.final_approver_id', '=', 'final_approver_user.id')
        ->where('hms_cancelbill_form.can_id', $canId)
        ->first();
}

public function refformsave_data(Request $request) {
    $fitterremovedataall = $request->input('morefilltersall');
    $datefiltervalue = $request->input('moredatefittervale');
    $phid = $request->input('mrodnofilter');

    // Parse and format dates
    [$startDate, $endDate] = explode(' - ', $datefiltervalue);
    $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

    $startdates = $startDateFormatted . " 00:00:00";
    $enddates = $endDateFormatted . " 23:59:59";

    // Base query
    $data = RefundFormModel::select('tbl_locations.name as location_name', 'tblzones.name as zone_name', 'hms_refund_form.*')
        ->join('tbl_locations', 'hms_refund_form.ref_zone_id', '=', 'tbl_locations.id')
        ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->whereBetween('hms_refund_form.created_at', [$startdates, $enddates]);

    // Apply filters from morefilltersall (excluding 'phid')
    if ($fitterremovedataall) {
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
            $column = trim($column);
            $value = trim($value, "'");

            // Skip 'phid' — it's not a real DB column
            if ($column === 'phid') {
                continue;
            }
            $data->whereIn($column, explode(',', $value));
        }
    }

    // Apply phid filter on ref_wife_mrd_no or ref_husband_mrd_no
    if (!empty($phid)) {
        $data->where(function ($query) use ($phid) {
            $query->where('ref_wife_mrd_no', 'LIKE', "%$phid%")
                  ->orWhere('ref_husband_mrd_no', 'LIKE', "%$phid%");
        });
    }

    $data = $data->orderBy('hms_refund_form.created_at', 'desc')->get();
    return response()->json($data);
}

  public function refundformDocument(){
    $admin = auth()->user();
    return view('superadmin.refundform',['admin' =>$admin]);
}


public function cancelbillform(){
    $admin = auth()->user();
    $approver = DB::table('cancel_bill_approver')
        ->where('user_id', $admin->id)
        ->first();
    return view('superadmin.cancelbillform',['admin'=>$admin,'isApprover' => (bool) $approver]);
}

public function cancelbill_dashboard(){
    $admin = auth()->user();
    $approver = DB::table('cancel_bill_approver')
        ->where('user_id', $admin->id)
        ->first();
    return view('superadmin.cancelbill_dashboard',['admin'=>$admin,'isApprover' => (bool) $approver]);
}


	public function generateHmac($phid)
    {
        $secret = 'd6401b40cebeda6f22c7e7ee1efe0ed4';
        $verb = "POST";
        $contentMd5 = "";
        $contentType = "application/x-www-form-urlencoded";
        $date = "Wed, 31 May 2025 10:00:00 GMT";
        $path = "/api/get/patienttimeline/draravinds-ivf/" . $phid;

        // Create string to sign
        $stringToSign = $verb . "\n" .
                        $contentMd5 . "\n" .
                        $contentType . "\n" .
                        $date . "\n" .
                        "\n" .
                        strtolower($path);

        // Generate HMAC
        $hash = hash_hmac('sha1', $stringToSign, $secret, true);
        $signature = base64_encode($hash);

        // Return the HMAC as JSON
        return $signature;
    }


 public function getSample()
    {
         $admin = auth()->user();
        $locations = TblLocationModel::all();
        // dd($location);
        // dd($admin);
        return view('superadmin.sample', ['admin' => $admin ,'locations' => $locations]);
    }






public function Samplesave(Request $request)
    {

        $validator = Validator::make($request->all(), [
        'serial_number' => 'required',
        'neft_amount' => 'required|numeric|min:1',
        'pan_number' => ['required', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
        'ifsc_code' => ['required', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        'account_number' => 'required|numeric',
        'pan_upload' => 'required|max:5120',
        'invoice_upload' => 'required|max:5120',
        'bank_upload' => 'required|max:5120',
    ], [
        'pan_number.regex' => 'PAN must be 10-character alphanumeric (e.g., ABCDE1234F).',
        'ifsc_code.regex' => 'IFSC must be 11-character alphanumeric (e.g., HDFC0001234).',
        'pan_upload.max' => 'PAN file size must be below 5MB.',
        'invoice_upload.max' => 'Invoice file size must be below 5MB.',
        'bank_upload.max' => 'Bank document size must be below 5MB.',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }
    // dd($request);
           function handleUploads($files, $folder) {
                $storedFiles = [];

                $uploadPath = public_path("uploads/{$folder}");
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0777, true);
                }

                foreach ($files as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $file->move($uploadPath, $fileName);

                    $storedFiles[] = $fileName;
                }

                return json_encode($storedFiles); // Store array of file names
            }

            $panPaths = $request->hasFile('pan_upload') ? handleUploads($request->file('pan_upload'), 'pan') : null;
            $invoicePaths = $request->hasFile('invoice_upload') ? handleUploads($request->file('invoice_upload'), 'invoice') : null;
            $bankPaths = $request->hasFile('bank_upload') ? handleUploads($request->file('bank_upload'), 'bank') : null;

            $data = [
                'serial_number' => $request->serial_number,
                'created_by' => $request->created_by,
                'vendor' => $request->vendor,
                'description' => $request->description,
                'neft_amount' => $request->neft_amount,
                'pan_number' => $request->pan_number,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'invoice_amount' => $request->invoice_amount,
                'aressio_paid' => $request->aressio_paid,
                'already_paid' => $request->already_paid,
                'pan_upload' => $panPaths,
                'invoice_upload' => $invoicePaths,
                'bank_upload' => $bankPaths,
                'created_at' => now(),
            ];

            TblNEFTmodule::insert($data);
         return redirect()->back()
            ->with('success', 'NEFT module inserted successfully!');
    }
    // //vasanth
    // public function getMarketersByZone(Request $request)
    // {
    //     $marketers = usermanagementdetails::where('zone_id', $request->zone_id)
    //         ->select('user_fullname', 'zone_id')
    //         ->get();
    //     return response()->json($marketers);
    // }

    // public function getAllMarketers()
    // {
    //     $marketers = usermanagementdetails::select('user_fullname', 'zone_id')
    //         ->get();
    //     return response()->json($marketers);
    // }


    public function getVendor()
{
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
    //  dd($admin);
        $locations = TblLocationModel::all();
    return view('superadmin.vendor', ['admin' => $admin,'locations' => $locations]);
}

public function vendorsave(Request $request)
{
    // dd($request);
    function handleUploads($files, $folder) {
        $storedFiles = [];

        if (!$files) return null;

        $uploadPath = public_path("uploads/{$folder}");
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true);
        }

        $files = is_array($files) ? $files : [$files];

        foreach ($files as $file) {
            if (!$file || !is_object($file)) continue;

            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $storedFiles[] = $fileName;
        }

        return !empty($storedFiles) ? json_encode($storedFiles) : null;
    }

    $panPaths = $request->hasFile('pan_upload') ? handleUploads($request->file('pan_upload'), 'vendor') : explode(',', $request->existing_pan_file);
    $bankPaths = $request->hasFile('bank_upload') ? handleUploads($request->file('bank_upload'), 'vendor') : explode(',', $request->existing_bank_upload);
    $companyPaths = $request->hasFile('upload_company_register') ? handleUploads($request->file('upload_company_register'), 'vendor') : explode(',', $request->existing_upload_company_register);

    $data = [
        'vendor_name' => $request->vendor_name,
        'company_name' => $request->company_name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'address' => $request->address,
        'gst_tax_id' => $request->gst_tax_id,
        'pan_number' => $request->pan_number,
        'account_number' => $request->account_number,
        'ifsc_code' => $request->ifsc_code,
        'business_name' => $request->business_name,
        'contact_name' => $request->contact_name ,
        'pan_upload' => $panPaths,
        'bank_upload' => $bankPaths,
        'upload_company_register' => $companyPaths,
        'created_at' => now(),
    ];
    if ($request->filled('id')) {
        Tblvendor::where('id', $request->id)->update($data);
    } else {
        Tblvendor::insert($data);
    }

    return response()->json(['success' => true, 'message' => 'Vendor data saved successfully!']);
}
public function vendorfetch()
{
    $userids = auth()->user()->username;
    $userid = auth()->user()->id;
    $access_limit = auth()->user()->access_limits;
    $access_heads = auth()->user()->access_heads;
    if($access_limit==1 || $access_limit==2){
        $Tblvendor = Tblvendor::orderBy('created_at', 'desc')->get();
    }
    else{
        $Tblvendor = Tblvendor::orderBy('created_at', 'desc')->where('user_id',$userid)->get();

    }
    return response()->json($Tblvendor);
}

    public function getPurchaseMaker()
{
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
    //  dd($admin);
        $locations = TblLocationModel::all();
    return view('superadmin.purchase_maker', ['admin' => $admin,'locations' => $locations]);
}

public function purchasesave(Request $request)
{
    // dd($request->pan_upload);
    function handleUploads($files, $folder) {
        $storedFiles = [];

        if (!$files) return null;

        $uploadPath = public_path("uploads/{$folder}");
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true);
        }

        $files = is_array($files) ? $files : [$files];

        foreach ($files as $file) {
            if (!$file || !is_object($file)) continue;

            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $storedFiles[] = $fileName;
        }

        return !empty($storedFiles) ? json_encode($storedFiles) : null;
    }

    // File uploads
    $panPaths = $request->hasFile('pan_upload') ? handleUploads($request->file('pan_upload'), 'pan') : null;
    $invoicePaths = $request->hasFile('invoice_upload') ? handleUploads($request->file('invoice_upload'), 'invoice') : null;
    $bankPaths = $request->hasFile('bank_upload') ? handleUploads($request->file('bank_upload'), 'bank') : null;
    $poPaths = $request->hasFile('po_upload') ? handleUploads($request->file('po_upload'), 'po') : null;
    $poSignedPaths = $request->hasFile('po_signed_upload') ? handleUploads($request->file('po_signed_upload'), 'po_signed') : null;
    $poDeliveryPaths = $request->hasFile('po_delivery_upload') ? handleUploads($request->file('po_delivery_upload'), 'po_delivery') : null;

    // Payment methods as comma-separated string
    $paymentMethods = $request->has('payment_method') ? implode(',', $request->payment_method) : null;
    // dd($request->user_id);
    $data = [
        'serial_number' => $request->serial_number,
        'branch_id' => $request->branch_id,
        'user_id' => $request->user_id,
        'created_by' => $request->created_by,
        'vendor' => $request->vendor,
        'nature_payment' => $request->nature_payment,
        'payment_status' => $request->payment_status,
        'payment_method' => $paymentMethods,
        'utr_number' => $request->utr_number,
        'pan_number' => $request->pan_number,
        'account_number' => $request->account_number,
        'ifsc_code' => $request->ifsc_code,
        'invoice_amount' => $request->invoice_amount,
        'already_paid' => $request->already_paid ?? 0,
        'checker_status' => $request->checker_status ?? 0 ,
        'approval_status' => $request->approval_status ?? 0,
        'pan_upload' => $panPaths ?? explode(',', $request->existing_pan_file),
        'invoice_upload' => $invoicePaths ?? explode(',', $request->existing_invoice_upload),
        'bank_upload' => $bankPaths ?? explode(',', $request->existing_bank_upload),
        'po_upload' => $poPaths ??explode(',', $request->existing_po_upload),
        'po_signed_upload' => $poSignedPaths ?? explode(',', $request->existing_po_signed_upload),
        'po_delivery_upload' => $poDeliveryPaths ?? explode(',', $request->existing_po_delivery_upload),
        'created_at' => now(),
    ];

    if ($request->filled('id')) {
        Tblpurchase::where('id', $request->id)->update($data);
    } else {
        Tblpurchase::insert($data);
    }

    return response()->json(['success' => true, 'message' => 'Purchase data saved successfully!']);
}

public function purchasefetch()
    {
        $userids = auth()->user()->username;
        $userid = auth()->user()->id;
        $access_limit = auth()->user()->access_limits;
        $access_heads = auth()->user()->access_heads;
        if($access_limit==1 || $access_limit==2){
            $Tblpurchase = Tblpurchase::orderBy('created_at', 'desc')->get();
        }
        else{
            $Tblpurchase = Tblpurchase::orderBy('created_at', 'desc')->where('user_id',$userid)->get();

        }
        return response()->json($Tblpurchase);
    }

      public function getPurchaseChecker()
    {
         $admin = auth()->user();
         $locations = TblLocationModel::all();
        return view('superadmin.purchase_checker', ['admin' => $admin,'locations' => $locations]);
    }
    public function purchaseCheckerfetch()
    {
        $userids = auth()->user()->username;
        $access_limit = auth()->user()->access_limits;
        $access_heads = auth()->user()->access_heads;
        $Tblpurchase = Tblpurchase::orderBy('created_at', 'desc')->where('checker_status',0)->get();
        return response()->json($Tblpurchase);
    }
    public function purchasecheckersave(Request $request)
    {
        Tblpurchase::where('id', $request->id)->update([
            'checker_status' => $request->checker_status
        ]);
        return response()->json(['success' => true, 'message' => 'Purchase data saved successfully!']);
    }
      public function getPurchaseApprover()
    {
         $admin = auth()->user();
         $locations = TblLocationModel::all();
        return view('superadmin.purchase_approver', ['admin' => $admin,'locations' => $locations]);
    }
    public function purchaseapproverfetch()
    {
        $userids = auth()->user()->username;
        $access_limit = auth()->user()->access_limits;
        $access_heads = auth()->user()->access_heads;
        $Tblpurchase = Tblpurchase::orderBy('created_at', 'desc')->where('checker_status',1)->get();
        return response()->json($Tblpurchase);
    }
    public function purchaseapproversave(Request $request)
    {
        Tblpurchase::where('id', $request->id)->update([
            'approval_status' => $request->approval_status
        ]);
        return response()->json(['success' => true, 'message' => 'Purchase data saved successfully!']);
    }
      public function fetchmorefitterpurchase(Request $request)
    {
        $userids = auth()->user()->username;
        $access_limit = auth()->user()->access_limits;

        $fitterremovedata = $request->input('fitterremovedata');
        $moredatefittervale = $request->input('moredatefittervale');
        $type = $request->input('type');
        $dates = explode(' - ', $moredatefittervale);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";
        if($type == 1){
            $query = Tblpurchase::query()
                ->join('tbl_locations', 'tbl_purchase_maker.branch_id', '=', 'tbl_locations.id')
                // ->join('users', 'users.username', '=', 'ref_doctor_details.empolyee_name')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
                ->select('tbl_locations.name', 'tblzones.name', 'tbl_purchase_maker.*');
        }else if($type == 2){
            $query = Tblpurchase::query()
                ->join('tbl_locations', 'tbl_purchase_maker.branch_id', '=', 'tbl_locations.id')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
                ->where('tbl_purchase_maker.checker_status', 0)
                ->where('tbl_purchase_maker.approval_status', 0)
                ->select('tbl_locations.name', 'tblzones.name', 'tbl_purchase_maker.*');
        }else{
            $query = Tblpurchase::query()
                ->join('tbl_locations', 'tbl_purchase_maker.branch_id', '=', 'tbl_locations.id')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
                ->where('tbl_purchase_maker.checker_status', 1)
                ->select('tbl_locations.name', 'tblzones.name', 'tbl_purchase_maker.*');

        }

        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));



            $query->where(function ($q) use ($column, $values) {
                if($column=='zone_name'){
                    foreach ($values as $val) {
                        $q->orWhere('tblzones.name', $val);
                    }
                }else if($column=='branch_name'){
                     foreach ($values as $val) {
                        $q->orWhere('tbl_locations.name', $val);
                    }
                }
            });
        }
        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        // dd($sqlQuery);
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        // dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }

    public function fetchmorefitterdateclrpurchase(Request $request)
    {
        $userids = auth()->user()->username;
        $access_limit = auth()->user()->access_limits;

        $datefilltervalue = $request->input('datefilltervalue');
        $type = $request->input('type');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";

       if($type == 1){

            $query = Tblpurchase::query()
                ->join('tbl_locations', 'tbl_purchase_maker.branch_id', '=', 'tbl_locations.id')
                // ->join('users', 'users.username', '=', 'ref_doctor_details.empolyee_name')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
                ->select('tbl_locations.name', 'tblzones.name', 'tbl_purchase_maker.*');
        }else if($type == 2){
            $query = Tblpurchase::query()
                ->join('tbl_locations', 'tbl_purchase_maker.branch_id', '=', 'tbl_locations.id')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
                ->where('tbl_purchase_maker.checker_status', 0)
                ->where('tbl_purchase_maker.approval_status', 0)
                ->select('tbl_locations.name', 'tblzones.name', 'tbl_purchase_maker.*');
        }else{
            $query = Tblpurchase::query()
                ->join('tbl_locations', 'tbl_purchase_maker.branch_id', '=', 'tbl_locations.id')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
                ->where('tbl_purchase_maker.checker_status', 1)
                ->select('tbl_locations.name', 'tblzones.name', 'tbl_purchase_maker.*');

        }

        // Get the raw SQL query and bindings
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        // Ensure bindings are safely quoted and replace placeholders
        $formattedQuery = $sqlQuery;
        foreach ($bindings as $binding) {
            $escapedBinding = "'" . addslashes($binding) . "'";
            $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
        }
        //dd($formattedQuery); // Dump the fully formatted query
        $doctorDetails = $query->get();
        return response()->json($doctorDetails);
    }

     public function purchasefetchfitter(Request $request)
    {
        $userids = auth()->user()->username;
        $access_limit = auth()->user()->access_limits;

        $datefiltervalue = $request->input('datefiltervalue');
        $type = $request->input('type');
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates = $startDateFormatted . " 00:00:00";
        $enddates = $endDateFormatted . " 23:59:59";

        if ($access_limit == 1) {
            $data = Tblpurchase::whereBetween('tbl_purchase_maker.created_at', [$startdates, $enddates]);
        } else {
            $data = Tblpurchase::whereBetween('tbl_purchase_maker.created_at', [$startdates, $enddates])
                ->where('empolyee_name', $userids);
        }

        if($type == 1){
                $data = Tblpurchase::whereBetween('tbl_purchase_maker.created_at', [$startdates, $enddates]);

        }else if($type == 2){
                $data = Tblpurchase::whereBetween('tbl_purchase_maker.created_at', [$startdates, $enddates])
                ->where('tbl_purchase_maker.checker_status', 0)
                ->where('tbl_purchase_maker.approval_status', 0);

        }else{
            $data = Tblpurchase::whereBetween('tbl_purchase_maker.created_at', [$startdates, $enddates])
                ->where('tbl_purchase_maker.checker_status', 1);
        }

        $data = $data
            ->join('tbl_locations', 'tbl_purchase_maker.branch_id', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->select('tbl_locations.name', 'tblzones.name', 'tbl_purchase_maker.*')
            ->get();
            // dd($data);
        return response()->json($data);
    }
    //  public function filterpurchasecheker(Request $request)
    // {

    //     $userids = auth()->user()->username;
    //     $access_limit = auth()->user()->access_limits;

    //     $fitterremovedata = $request->input('fitterremovedata');
    //     $moredatefittervale = $request->input('moredatefittervale');
    //     $dates = explode(' - ', $moredatefittervale);
    //     $startDate = $dates[0];  // "29/12/2024"
    //     $endDateview = $dates[1];    // "04/01/2025"
    //     $endDate = substr($endDateview, 0, 10);
    //     $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    //     $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
    //     $startdates = $startDateFormatted . " 00:00:00";
    //     $enddates = $endDateFormatted . " 23:59:59";
    //     // Start the query

    //     $query = Tblpurchase::query()
    //             ->join('tbl_locations', 'tbl_purchase_maker.branch_id', '=', 'tbl_locations.id')
    //             ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
    //             ->where('tbl_purchase_maker.checker_status', 0)
    //             ->where('tbl_purchase_maker.approval_status', 0)
    //             ->select(
    //                 'tbl_locations.name as location_name',
    //                 'tblzones.name as zone_name',
    //                 'tbl_purchase_maker.*'
    //             );

    //     if (is_array($fitterremovedata)) {
    //         $fitterremovedata = implode(' AND ', $fitterremovedata);
    //     } elseif (!is_string($fitterremovedata)) {
    //         return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
    //     }
    //     foreach (explode(' AND ', $fitterremovedata) as $condition) {
    //         [$column, $value] = explode('=', $condition);
    //         // Handle columns with comma-separated values
    //         $values = explode(',', trim($value, "'"));



    //         $query->where(function ($q) use ($column, $values) {
    //             if($column=='zone_name'){
    //                 foreach ($values as $val) {
    //                     $q->orWhere('tblzones.name', $val);
    //                 }
    //             }else if($column=='branch_name'){
    //                  foreach ($values as $val) {
    //                     $q->orWhere('tbl_locations.name', $val);
    //                 }
    //             }
    //         });
    //     }
    //     // Get the raw SQL query and bindings
    //     $sqlQuery = $query->toSql();
    //     // dd($sqlQuery);
    //     $bindings = $query->getBindings();
    //     // Ensure bindings are safely quoted and replace placeholders
    //     $formattedQuery = $sqlQuery;
    //     foreach ($bindings as $binding) {
    //         $escapedBinding = "'" . addslashes($binding) . "'";
    //         $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
    //     }
    //     // dd($formattedQuery); // Dump the fully formatted query
    //     $doctorDetails = $query->get();
    //     return response()->json($doctorDetails);
    // }
    // public function fitterdateclrpurchasechecker(Request $request)
    // {
    //     $userids = auth()->user()->username;
    //     $access_limit = auth()->user()->access_limits;

    //     $datefilltervalue = $request->input('datefilltervalue');
    //     $dates = explode(' - ', $datefilltervalue);
    //     $startDate = $dates[0];  // "29/12/2024"
    //     $endDateview = $dates[1];    // "04/01/2025"
    //     $endDate = substr($endDateview, 0, 10);
    //     $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    //     $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
    //     $startdates = $startDateFormatted . " 00:00:00";
    //     $enddates = $endDateFormatted . " 23:59:59";
    //     // Start the query
    //     // Start the query
    //     $query = Tblpurchase::query()
    //         ->join('tbl_locations', 'tbl_purchase_maker.branch_id', '=', 'tbl_locations.id')
    //         // ->join('users', 'users.username', '=', 'ref_doctor_details.empolyee_name')
    //         ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
    //         ->where('tbl_purchase_maker.checker_status', 0)
    //         ->where('tbl_purchase_maker.approval_status', 0)
    //         ->select('tbl_locations.name', 'tblzones.name', 'tbl_purchase_maker.*');

    //     // Get the raw SQL query and bindings
    //     $sqlQuery = $query->toSql();
    //     $bindings = $query->getBindings();
    //     // Ensure bindings are safely quoted and replace placeholders
    //     $formattedQuery = $sqlQuery;
    //     foreach ($bindings as $binding) {
    //         $escapedBinding = "'" . addslashes($binding) . "'";
    //         $formattedQuery = preg_replace('/\?/', $escapedBinding, $formattedQuery, 1);
    //     }
    //     //dd($formattedQuery); // Dump the fully formatted query
    //     $doctorDetails = $query->get();
    //     return response()->json($doctorDetails);
    // }
    public function getMarketersByZone(Request $request)
    {
        $employee_ids = DB::table('access_log')
        ->where('zone_id', $request->zone_id)
        ->pluck('employee_id');



        $marketers = DB::table('users')
            ->whereIn('id', $employee_ids)
            ->select('user_fullname', 'zone_id')
            ->get();





        // $marketers = usermanagementdetails::where('zone_id', $request->zone_id)
        //     ->select('user_fullname', 'zone_id')
        //     ->get();
        return response()->json($marketers);
    }

    public function getAllMarketers()
    {
        $marketers = usermanagementdetails::select('user_fullname', 'zone_id')
            ->get();
        return response()->json($marketers);
    }

    public function getAllZones()
    {
        $admin = Auth::user();
        
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            // Superadmin (1) or Auditor (4) → All zones
            $zones = TblZonesModel::select('name', 'id')
                ->orderBy('name')
                ->get();
                
        } elseif ($admin->access_limits == 2) {
            // Zonal Admin (2) → Own zone + zones from multi-locations
            $zoneIds = [$admin->zone_id];
            
            if (!empty($admin->multi_location)) {
                $multiLocations = explode(',', $admin->multi_location);
                
                // Get zone IDs from multi-locations
                $multiZoneIds = TblLocationModel::whereIn('id', $multiLocations)
                    ->pluck('zone_id')
                    ->unique()
                    ->toArray();
                
                $zoneIds = array_unique(array_merge($zoneIds, $multiZoneIds));
            }
            
            $zones = TblZonesModel::select('name', 'id')
                ->whereIn('id', $zoneIds)
                ->orderBy('name')
                ->get();
                
        } elseif ($admin->access_limits == 3) {
            // Admin (3) → Zones derived from own branch + multi-locations
            $branchIds = [$admin->branch_id];
            
            if (!empty($admin->multi_location)) {
                $multiLocations = explode(',', $admin->multi_location);
                $branchIds = array_unique(array_merge($branchIds, $multiLocations));
            }
            
            $zoneIds = TblLocationModel::whereIn('id', $branchIds)
                ->pluck('zone_id')
                ->unique()
                ->toArray();
            
            $zones = TblZonesModel::select('name', 'id')
                ->whereIn('id', $zoneIds)
                ->orderBy('name')
                ->get();
                
        } else {
            // User (5+) → No zones
            $zones = collect();
        }
        
        return response()->json($zones);
    }
    
    public function getAllBranches()
    {
        $admin = Auth::user();
        
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            // Superadmin (1) or Auditor (4) → All branches
            $locations = TblLocationModel::select('name', 'id', 'zone_id')
                ->orderBy('name')
                ->get();
                
        } elseif ($admin->access_limits == 2) {
            // Zonal Admin (2) → Zone branches + multi-location branches
            $branchIds = [];
            
            // Get branches from user's zone
            $zoneBranches = TblLocationModel::where('zone_id', $admin->zone_id)
                ->pluck('id')
                ->toArray();
            
            $branchIds = $zoneBranches;
            
            // Add multi-locations
            if (!empty($admin->multi_location)) {
                $multiLocations = array_map('intval', explode(',', $admin->multi_location));
                $branchIds = array_unique(array_merge($branchIds, $multiLocations));
            }
            
            $locations = TblLocationModel::select('name', 'id', 'zone_id')
                ->whereIn('id', $branchIds)
                ->orderBy('name')
                ->get();
                
        } elseif ($admin->access_limits == 3) {
            // Admin (3) → Own branch + multi-location branches
            $branchIds = [$admin->branch_id];
            
            if (!empty($admin->multi_location)) {
                $multiLocations = array_map('intval', explode(',', $admin->multi_location));
                $branchIds = array_unique(array_merge($branchIds, $multiLocations));
            }
            
            $locations = TblLocationModel::select('name', 'id', 'zone_id')
                ->whereIn('id', $branchIds)
                ->orderBy('name')
                ->get();
                
        } else {
            // User (5+) → No branches
            $locations = collect();
        }
        
        return response()->json($locations);
    }


    public function getMarketersByZonalHead(Request $request)
{
    // Get usernames of users reporting to this zonal head
    $reportingUsers = DB::table('users')
        ->where('reporting_manager', $request->zonal_head_id)
        ->orWhere('id', $request->zonal_head_id)
        ->pluck('username');

    // Get marketer details
    $marketers = DB::table('users')
        ->whereIn('username', $reportingUsers)
        ->select('user_fullname')
        ->get();

    return response()->json($marketers);
}


public function getZonalHeadsByZone(Request $request)
{
    $zoneId = $request->input('zone_id');

    $zonalHeads = DB::table('users')
        ->where('zonal_head', '1')
        ->when($zoneId, function($query) use ($zoneId) {
            return $query->where('zone_id', $zoneId);
        })
        ->select('user_fullname', 'id', 'zone_id')
        ->get();

    return response()->json($zonalHeads);
}

public function getAllZonalHeads()
{
    $zonalHeads = DB::table('users')
        ->where('zonal_head', '1')
        ->select('user_fullname', 'id', 'zone_id')
        ->get();

    return response()->json($zonalHeads);
}

// public function discountapproveReject(Request $request)
//     {
//         $admin = auth()->user();
//         $request->validate([
//             'id'     => 'required|integer',
//             'status' => 'required|in:1,2'
//         ]);
//         if($admin->access_limits == 2){
//             DB::table('hms_discount_form')
//                 ->where('dis_id', $request->id)
//                 ->update([
//                     'zonal_approver' => $request->status,
//                     'zonal_head_id'     => auth()->id(),
//                     'updated_at'     => now()
//                 ]);
//         }else if($admin->access_limits == 1){
//             DB::table('hms_discount_form')
//                 ->where('dis_id', $request->id)
//                 ->update([
//                     'final_approver' => $request->status,
//                     'final_approver_id'     => auth()->id(),
//                     'updated_at'     => now()
//                 ]);
//         }else if($admin->access_limits == 3){
//                      DB::table('hms_discount_form')
//                     ->where('dis_id', $request->id)
//                     ->update([
//                         'admin_approver' => $request->status,
//                         'admin_head_id'     => auth()->id(),
//                         'updated_at'     => now()
//                     ]);
//         }else if($admin->access_limits == 1){
//                      DB::table('hms_discount_form')
//                     ->where('dis_id', $request->id)
//                     ->update([
//                         'admin_approver' => $request->status,
//                         'admin_head_id'     => auth()->id(),
//                         'updated_at'     => now()
//                     ]);
//         }else if($admin->access_limits == 4){
//                      DB::table('hms_discount_form')
//                     ->where('dis_id', $request->id)
//                     ->update([
//                         'audit_approver' => $request->status,
//                         'audit_head_id'     => auth()->id(),
//                         'updated_at'     => now()
//                     ]);
//         }
//         return response()->json([
//             'success' => true,
//             'message' => $request->status == 1
//                 ? 'Discount approved successfully'
//                 : 'Discount rejected successfully'
//         ]);
//     }

   // ============================================
// UPDATED PHP CONTROLLER
// ============================================

public function discountapproveReject(Request $request)
{
    $admin = auth()->user();
    // dd($request);
    $rules = [
        'id'     => 'required|integer',
        'status' => 'required|in:1,2'
    ];
    if ($request->status == 2) {
        $rules['reject_reason'] = 'required|string|max:2000';
    }
    $request->validate($rules);

    // Map access levels to approval columns
    $approvalMap = [
        1 => ['column' => 'final_approver', 'id_column' => 'final_approver_id'],
        2 => ['column' => 'zonal_approver', 'id_column' => 'zonal_head_id'],
        3 => ['column' => 'admin_approver', 'id_column' => 'admin_head_id'],
        4 => ['column' => 'audit_approver', 'id_column' => 'audit_head_id'],
    ];

    // Check if admin has valid access level
    if (!isset($approvalMap[$admin->access_limits])) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action'
        ], 403);
    }

    $approval = $approvalMap[$admin->access_limits];

    // Update the discount record via model (approve/reject)
    $updateData = [
        $approval['column']    => $request->status,
        $approval['id_column'] => auth()->id(),
        'updated_at'          => now()
    ];
    if ($request->status == 2 && $request->filled('reject_reason')) {
        $updateData['reject_reason'] = $request->reject_reason;
    }
    $updated = DiscountFormModel::where('dis_id', $request->id)
        ->update($updateData);

    if (!$updated) {
        return response()->json([
            'success' => false,
            'message' => 'Record not found or update failed'
        ], 404);
    }

    // Fetch the updated record with all necessary data
    $updatedRecord = DB::table('hms_discount_form')
        ->select(
            'hms_discount_form.*',
            'tbl_locations.name as location_name',
            'tblzones.name as zone_name',
            'users.user_fullname as username'
        )
        ->leftjoin('tbl_locations', 'hms_discount_form.dis_zone_id', '=', 'tbl_locations.id')
        ->leftjoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->leftjoin('users', 'hms_discount_form.created_by', '=', 'users.id')
        ->where('hms_discount_form.dis_id', $request->id)
        ->first();

    // Get updated statistics
    $baseQuery = DB::table('hms_discount_form')
        ->select('hms_discount_form.*')
        ->leftjoin('tbl_locations', 'hms_discount_form.dis_zone_id', '=', 'tbl_locations.id')
        ->leftjoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
        ->leftjoin('users', 'hms_discount_form.created_by', '=', 'users.id');

    // Apply role-based filters (same as your disformsave_data function)
    if ($admin->access_limits == 1 || $admin->access_limits == 4) {
        // All data
    } elseif ($admin->access_limits == 2) {
        $branchIds = [];
        if (!empty($admin->zone_id)) {
            $zoneBranchIds = DB::table('tbl_locations')
                ->where('zone_id', $admin->zone_id)
                ->pluck('id')->toArray();
            $branchIds = array_merge($branchIds, $zoneBranchIds);
        }
        if (!empty($admin->multi_location)) {
            $multiLocationIds = array_map('intval', explode(',', $admin->multi_location));
            $branchIds = array_merge($branchIds, $multiLocationIds);
        }
        $branchIds = array_unique($branchIds);
        if (!empty($branchIds)) {
            $baseQuery->whereIn('hms_discount_form.dis_zone_id', $branchIds);
        }
    } elseif ($admin->access_limits == 3) {
        $branchIds = [];
        if (!empty($admin->multi_location)) {
            $multiLocationIds = array_map('intval', explode(',', $admin->multi_location));
            $branchIds = array_merge($branchIds, $multiLocationIds);
        }
        $branchIds = array_unique($branchIds);
        if (!empty($branchIds)) {
            $baseQuery->whereIn('hms_discount_form.dis_zone_id', $branchIds);
        }
        $baseQuery->where('hms_discount_form.created_by', $admin->id);
    }

    // Calculate updated statistics (same structure as disformsave_data for stats box)
    $statistics = [
        'total_raised'     => (clone $baseQuery)->count(),
        'admin_approved'   => (clone $baseQuery)->where('admin_approver', 1)->count(),
        'zonal_approved'   => (clone $baseQuery)->where('zonal_approver', 1)->count(),
        'audit_approved'   => (clone $baseQuery)->where('audit_approver', 1)->count(),
        'final_approved'   => (clone $baseQuery)->where('final_approver', 1)->count(),
    ];

    $pendingColumnMap = [
        1 => 'final_approver',
        2 => 'zonal_approver',
        3 => 'admin_approver',
        4 => 'audit_approver',
    ];

    $pendingColumn = $pendingColumnMap[$admin->access_limits] ?? null;
    if ($pendingColumn) {
        $statistics['pending'] = (clone $baseQuery)->where($pendingColumn, 0)->count();
    }

    // Total discount amount: only from finally approved records (final_approver = 1)
    $totalDiscountAmount = (clone $baseQuery)
        ->where('final_approver', 1)
        ->get()
        ->sum(function ($row) {
            $value = $row->dis_post_discount ?? '0';
            return (float) preg_replace('/[^0-9.]/', '', $value);
        });
    $statistics['total_discount_amount'] = round($totalDiscountAmount, 2);

    // Format statistics for frontend
    $formattedStats = [
        ['label' => 'Total Raised', 'count' => $statistics['total_raised'], 'icon' => 'file-text'],
        ['label' => 'Admin Approved', 'count' => $statistics['admin_approved'], 'icon' => 'check-circle'],
        ['label' => 'Zonal Approved', 'count' => $statistics['zonal_approved'], 'icon' => 'check-circle'],
        ['label' => 'Audit Approved', 'count' => $statistics['audit_approved'], 'icon' => 'shield-check'],
        ['label' => 'Final Approved', 'count' => $statistics['final_approved'], 'icon' => 'check-double'],
        ['label' => 'Pending', 'count' => $statistics['pending'] ?? 0, 'icon' => 'clock'],
    ];

    return response()->json([
        'success' => true,
        'message' => $request->status == 1
            ? 'Discount approved successfully'
            : 'Discount rejected successfully',
        'record' => $updatedRecord,
        'statistics' => $formattedStats,
        'counts' => $statistics,
        'total_discount_amount' => $statistics['total_discount_amount'],
        'approval_type' => $approval['column'],
        'approver_name' => $admin->user_fullname ?? $admin->name
    ]);
}




//new refund
public function refundbill_dashboard()
{
    $admin = auth()->user();
    $approver = null;
    return view('superadmin.refundbill_dashboard', ['admin' => $admin, 'isApprover' => (bool) $approver]);
}

// Method 2: Add or Edit Refund Form
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// public function refundbill_add(Request $request)
// {
//     try {
//         // $admin = auth()->user();
//         $user = auth()->user();

//         // Handle signature uploads/canvas
//         $imagePaths = [];
//         $signatureFields = ['ref_wife_sign', 'ref_husband_sign', 'ref_drsign', 'ref_admin_sign', 'ref_zonal_sign'];
        
//         foreach ($signatureFields as $field) {
//             if ($request->hasFile($field)) {
//                 $file = $request->file($field);
//                 $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
//                 $destinationPath = public_path('refund_form');
//                 if (!file_exists($destinationPath)) {
//                     mkdir($destinationPath, 0755, true);
//                 }
//                 $file->move($destinationPath, $filename);
//                 $imagePaths[$field] = json_encode(['refund_form/' . $filename]);
//             }
//         }

//         // Check if edit or new
//         if ($request->has('ref_id') && $request->ref_id) {
//             // UPDATE existing record
//             $refund = RefundFormModel::find($request->ref_id);
//             if (!$refund) {
//                 return response()->json(['success' => false, 'message' => 'Refund form not found.']);
//             }

//             $refund->ref_zone_id = $request->ref_zone_id;
//             $refund->ref_wife_name = $request->ref_wife_name;
//             $refund->ref_wife_mrd_no = $request->ref_wife_mrd_no;
//             $refund->ref_husband_name = $request->ref_husband_name;
//             $refund->ref_husband_mrd_no = $request->ref_husband_mrd_no;
//             $refund->ref_service_name = $request->ref_service_name;
//             $refund->ref_total_bill = $request->ref_total_bill;
//             $refund->ref_expected_request = $request->ref_expected_request;
//             $refund->ref_form_status = $request->ref_form_status;
//             $refund->ref_counselled_by = $request->ref_counselled_by;
//             $refund->ref_final_auth = $request->ref_final_auth;
//             $refund->ref_branch_no = $request->ref_branch_no;
//             $refund->ref_auth_by = $request->ref_auth_by;
//             $refund->ref_patient_ph = $request->ref_patient_ph;
//             $refund->ref_approved_by = $request->ref_approved_by ?? $request->ref_approveded_by;

//             foreach ($imagePaths as $field => $path) {
//                 $refund->{$field} = $path;
//             }

//             $refund->save();

//             return response()->json(['success' => true, 'message' => 'Refund form updated successfully!']);
//         } else {
//             // CREATE new record
//             $dataToSave = [
//                 'ref_zone_id' => $request->ref_zone_id,
//                 'ref_wife_name' => $request->ref_wife_name,
//                 'ref_wife_mrd_no' => $request->ref_wife_mrd_no,
//                 'ref_husband_name' => $request->ref_husband_name,
//                 'ref_husband_mrd_no' => $request->ref_husband_mrd_no,
//                 'ref_service_name' => $request->ref_service_name,
//                 'ref_total_bill' => $request->ref_total_bill,
//                 'ref_expected_request' => $request->ref_expected_request,
//                 'ref_form_status' => $request->ref_form_status,
//                 'ref_counselled_by' => $request->ref_counselled_by,
//                 'ref_final_auth' => $request->ref_final_auth,
//                 'ref_branch_no' => $request->ref_branch_no,
//                 'ref_auth_by' => $request->ref_auth_by,
//                 'ref_patient_ph' => $request->ref_patient_ph,
//                 'ref_approved_by' => $request->ref_approved_by ?? $request->ref_approveded_by,
//                 'created_by' => $user->id,  // Track WHO created
//             ];

//             foreach ($imagePaths as $field => $path) {
//                 $dataToSave[$field] = $path;
//             }

//             RefundFormModel::create($dataToSave);

//             return response()->json(['success' => true, 'message' => 'Refund form created successfully!']);
//         }
//     } catch (\Exception $e) {
//         return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
//     }
// }

// // Method 3: Get Refund Forms (Pending Tab)
// // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// public function refundbillform_data(Request $request)
// {
//     $admin = auth()->user();
    
//     // Date range parsing
//     $dateRange = $request->moredatefittervale ?? '';
//     if ($dateRange) {
//         $dates = explode(' - ', $dateRange);
//         $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
//         $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1] ?? $dates[0]))->endOfDay();
//     } else {
//         $startDate = \Carbon\Carbon::today();
//         $endDate = \Carbon\Carbon::today()->endOfDay();
//     }

//     // Base query
//     $query = RefundFormModel::select(
//         'tblzones.name as zone_name',
//         'tbl_locations.name as location_name',
//         'hms_refund_form.*'
//     )
//     ->leftJoin('tbl_locations', 'hms_refund_form.ref_zone_id', '=', 'tbl_locations.id')
//     ->leftJoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
//     ->whereBetween('hms_refund_form.created_at', [$startDate, $endDate]);

//     // Access control based on user role
//     if ($admin->access_limits == 1) {
//         // SuperAdmin - see all
//     } else if ($admin->access_limits == 2) {
//         // Zonal - see their zone
//         $query->where('tbl_locations.zone_id', $admin->zone_id);
//     } else {
//         // Branch - see their branch(es)
//         $branchIds = [$admin->branch_id];
//         if (!empty($admin->multi_location)) {
//             $branchIds = array_merge($branchIds, explode(',', $admin->multi_location));
//         }
//         $query->whereIn('hms_refund_form.ref_zone_id', $branchIds);
//     }

//     // Apply filters
//     if ($request->morefilltersall) {
//         $filters = explode(' AND ', $request->morefilltersall);
//         foreach ($filters as $filter) {
//             if (strpos($filter, "tblzones.name='") !== false) {
//                 preg_match("/'([^']+)'/", $filter, $matches);
//                 if (isset($matches[1])) {
//                     $query->where('tblzones.name', 'LIKE', '%' . $matches[1] . '%');
//                 }
//             } else if (strpos($filter, "tbl_locations.name='") !== false) {
//                 preg_match("/'([^']+)'/", $filter, $matches);
//                 if (isset($matches[1])) {
//                     $query->where('tbl_locations.name', 'LIKE', '%' . $matches[1] . '%');
//                 }
//             }
//         }
//     }

//     // MRD filter
//     if ($request->mrodnofilter) {
//         $mrd = $request->mrodnofilter;
//         $query->where(function($q) use ($mrd) {
//             $q->where('hms_refund_form.ref_wife_mrd_no', 'LIKE', '%' . $mrd . '%')
//               ->orWhere('hms_refund_form.ref_husband_mrd_no', 'LIKE', '%' . $mrd . '%');
//         });
//     }

//     $data = $query->orderBy('hms_refund_form.created_at', 'desc')->get();

//     return response()->json(['checkinData' => $data]);
// }

// // Method 4: Get Saved Refund Forms (WITH APPROVER NAMES)
// // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// public function refundbill_savedata(Request $request)
// {
//     $admin = auth()->user();
    
//     // Date range parsing
//     $dateRange = $request->moredatefittervale ?? '';
//     if ($dateRange) {
//         $dates = explode(' - ', $dateRange);
//         $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
//         $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1] ?? $dates[0]))->endOfDay();
//     } else {
//         $startDate = \Carbon\Carbon::today();
//         $endDate = \Carbon\Carbon::today()->endOfDay();
//     }

//     // Base query with creator name AND ALL approver names
//     $query = RefundFormModel::select(
//         'tblzones.name as zone_name',
//         'tbl_locations.name as location_name',
//         'users.user_fullname as created_by_name',
//         'admin_user.user_fullname as admin_approved_by_name',    // ← Admin approver name
//         'zonal_user.user_fullname as zonal_approved_by_name',    // ← Zonal approver name
//         'audit_user.user_fullname as audit_approved_by_name',    // ← Audit approver name
//         'final_user.user_fullname as final_approved_by_name',    // ← Final approver name
//         'hms_refund_form.*'
//     )
//     ->leftJoin('tbl_locations', 'hms_refund_form.ref_zone_id', '=', 'tbl_locations.id')
//     ->leftJoin('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
//     ->leftJoin('users', 'hms_refund_form.created_by', '=', 'users.id')
//     ->leftJoin('users as admin_user', 'hms_refund_form.admin_approved_by', '=', 'admin_user.id')
//     ->leftJoin('users as zonal_user', 'hms_refund_form.zonal_approved_by', '=', 'zonal_user.id')
//     ->leftJoin('users as audit_user', 'hms_refund_form.audit_approved_by', '=', 'audit_user.id')
//     ->leftJoin('users as final_user', 'hms_refund_form.final_approved_by', '=', 'final_user.id')
//     ->whereBetween('hms_refund_form.created_at', [$startDate, $endDate]);

//     // Access control
//     if ($admin->access_limits == 1) {
//         // SuperAdmin - see all
//     } else if ($admin->access_limits == 2) {
//         // Zonal - see their zone
//         $query->where('tbl_locations.zone_id', $admin->zone_id);
//     } else {
//         // Branch - see their branch(es)
//         $branchIds = [$admin->branch_id];
//         if (!empty($admin->multi_location)) {
//             $branchIds = array_merge($branchIds, explode(',', $admin->multi_location));
//         }
//         $query->whereIn('hms_refund_form.ref_zone_id', $branchIds);
//     }

//     // Apply filters
//     if ($request->morefilltersall) {
//         $filters = explode(' AND ', $request->morefilltersall);
//         foreach ($filters as $filter) {
//             if (strpos($filter, "tblzones.name='") !== false) {
//                 preg_match("/'([^']+)'/", $filter, $matches);
//                 if (isset($matches[1])) {
//                     $query->where('tblzones.name', 'LIKE', '%' . $matches[1] . '%');
//                 }
//             } else if (strpos($filter, "tbl_locations.name='") !== false) {
//                 preg_match("/'([^']+)'/", $filter, $matches);
//                 if (isset($matches[1])) {
//                     $query->where('tbl_locations.name', 'LIKE', '%' . $matches[1] . '%');
//                 }
//             }
//         }
//     }

//     // MRD filter
//     if ($request->mrodnofilter) {
//         $mrd = $request->mrodnofilter;
//         $query->where(function($q) use ($mrd) {
//             $q->where('hms_refund_form.ref_wife_mrd_no', 'LIKE', '%' . $mrd . '%')
//               ->orWhere('hms_refund_form.ref_husband_mrd_no', 'LIKE', '%' . $mrd . '%');
//         });
//     }

//     $data = $query->orderBy('hms_refund_form.created_at', 'desc')->get();

//     // Calculate statistics
//     $total_raised = $data->count();
//     $admin_approved = $data->where('admin_approver', 1)->count();
//     $zonal_approved = $data->where('zonal_approver', 1)->count();
//     $audit_approved = $data->where('audit_approver', 1)->count();
//     $final_approved = $data->where('final_approver', 1)->count();
//     $pending = $data->where('final_approver', 0)->count();
//     $total_refund_amount = $data->where('final_approver', 1)->sum('ref_final_auth');

//     $counts = [
//         'total_raised' => $total_raised,
//         'admin_approved' => $admin_approved,
//         'zonal_approved' => $zonal_approved,
//         'audit_approved' => $audit_approved,
//         'final_approved' => $final_approved,
//         'pending' => $pending,
//         'total_refund_amount' => $total_refund_amount,
//     ];

//     // Check if user is approver
//     $isApprover = false;
//     if ($admin->access_limits == 1 || $admin->access_limits == 2 || $admin->access_limits == 3 || $admin->access_limits == 4) {
//         $isApprover = true;
//     }

//     return response()->json([
//         'data' => $data,
//         'isApprover' => $isApprover,
//         'counts' => $counts,
//         'statistics' => null
//     ]);
// }

// // Method 5: Approval Action (WITH APPROVER ID & TIMESTAMP)
// // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// public function refundbill_approval(Request $request)
// {
//     try {
//         $admin = auth()->user();
//         $id = $request->id;
//         $status = $request->status; // 1=Approved, 2=Rejected

//         $refund = RefundFormModel::find($id);
//         if (!$refund) {
//             return response()->json(['success' => false, 'message' => 'Refund form not found.']);
//         }

//         $currentTimestamp = now();

//         // Update based on user role - store STATUS + WHO + WHEN
//         if ($admin->access_limits == 1) {
//             // SuperAdmin - can approve as admin and final
//             $refund->admin_approver = $status;
//             $refund->admin_approved_by = $admin->id;        // ← WHO approved
//             $refund->admin_approved_at = $currentTimestamp; // ← WHEN approved
            
//             if ($status == 1) {
//                 $refund->final_approver = 1;
//                 $refund->final_approved_by = $admin->id;        // ← WHO gave final
//                 $refund->final_approved_at = $currentTimestamp; // ← WHEN final
//             } else if ($status == 2) {
//                 $refund->final_approver = 2;
//                 $refund->final_approved_by = $admin->id;
//                 $refund->final_approved_at = $currentTimestamp;
//             }
//         } else if ($admin->access_limits == 2) {
//             // Zonal head
//             $refund->zonal_approver = $status;
//             $refund->zonal_approved_by = $admin->id;        // ← WHO approved
//             $refund->zonal_approved_at = $currentTimestamp; // ← WHEN approved
//         } else if ($admin->access_limits == 3) {
//             // Admin
//             $refund->admin_approver = $status;
//             $refund->admin_approved_by = $admin->id;        // ← WHO approved
//             $refund->admin_approved_at = $currentTimestamp; // ← WHEN approved
//         } else if ($admin->access_limits == 4) {
//             // Auditor
//             $refund->audit_approver = $status;
//             $refund->audit_approved_by = $admin->id;        // ← WHO approved
//             $refund->audit_approved_at = $currentTimestamp; // ← WHEN approved
//         }

//         $refund->save();

//         $message = $status == 1 ? 'Refund form approved successfully!' : 'Refund form rejected!';
//         return response()->json(['success' => true, 'message' => $message]);
//     } catch (\Exception $e) {
//         return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
//     }
// }

    /**
     * Refund bill approve/reject (single route). Used by refund dashboard.
     */
    public function refundbill_approval(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
            'status' => 'required|in:1,2'
        ];
        if ($request->status == 2) {
            $rules['reject_reason'] = 'required|string|max:2000';
        }
        $request->validate($rules);
        $admin = auth()->user();
        $refund = RefundFormModel::find($request->id);
        if (!$refund) {
            return response()->json(['success' => false, 'message' => 'Refund form not found.'], 404);
        }
        $currentTimestamp = now();
        if ($admin->access_limits == 1) {
            $refund->final_approver = $request->status;
            $refund->final_approved_by = $admin->id;
            $refund->final_approved_at = $currentTimestamp;
            
        } elseif ($admin->access_limits == 2) {
            $refund->zonal_approver = $request->status;
            $refund->zonal_approved_by = $admin->id;
            $refund->zonal_approved_at = $currentTimestamp;
        } elseif ($admin->access_limits == 3) {
            $refund->admin_approver = $request->status;
            $refund->admin_approved_by = $admin->id;
            $refund->admin_approved_at = $currentTimestamp;
        } elseif ($admin->access_limits == 4) {
            $refund->audit_approver = $request->status;
            $refund->audit_approved_by = $admin->id;
            $refund->audit_approved_at = $currentTimestamp;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        if ($request->status == 2 && $request->filled('reject_reason')) {
            $refund->reject_reason = $request->reject_reason;
        }
        $refund->save();
        $message = $request->status == 1 ? 'Refund form approved successfully!' : 'Refund form rejected!';
        return response()->json(['success' => true, 'message' => $message]);
    }

}
