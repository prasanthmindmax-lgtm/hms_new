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
use App\Models\VehicleDetails;
use App\Models\VehicleDocument;
use App\Models\VehicleType;
use App\Models\TicketModel;
use App\Models\TicketDetails;
use App\Models\ImageModel;
use App\Models\StatusModel;
use App\Models\PriorityModel;
use App\Models\CategoryModel;
use App\Models\LocationModel;
use App\Models\SubCategoryModel;
use App\Models\TicketActivitiesModel;
use App\Models\AdminUserDepartments;
use App\Models\TblUserDepartments;
use App\Models\TicketActivityModel;
use App\Models\TblZonesModel;
use App\Models\DailySummary;
use App\Models\TblBranch;
use App\Models\TblTreamentCategory;
use App\Models\CheckinModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use DataTables;
use DB;
class SuperAdminController extends Controller
{
    public function dashboard(){
        $admin = auth()->user();
        return view('superadmin.dashboard', ['admin' => $admin]);
    }
    public function referral(){
        $admin = auth()->user();
        //dd($admin);
        return view('superadmin.referral', ['admin' => $admin]);
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
                    'images.*' => 'required|nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate images
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
    public function fetch()
    {
        $doctordetails = doctordetails::orderBy('created_at', 'desc')
        ->join('branches', 'ref_doctor_details.city', '=', 'branches.id')
        ->join('zones', 'branches.zone_id', '=', 'zones.id')
        ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*')
        ->get();
        return response()->json($doctordetails);
    }
    public function fetchfitter(Request $request)
    {
        $datefiltervalue = $request->input('datefiltervalue');
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        $data = doctordetails::whereBetween('ref_doctor_details.created_at', [$startdates, $enddates])
        ->join('branches', 'ref_doctor_details.city', '=', 'branches.id')
        ->join('zones', 'branches.zone_id', '=', 'zones.id')
        ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*')
        ->get();
        return response()->json($data);
    }
    public function fetchmorefitter(Request $request)
    {
         		$fitterremovedata = $request->input('fitterremovedata');
                        $moredatefittervale = $request->input('moredatefittervale');
                        $dates = explode(' - ', $moredatefittervale);
                        $startDate = $dates[0];  // "29/12/2024"
                        $endDateview = $dates[1];    // "04/01/2025"
                        $endDate = substr($endDateview, 0, 10);
                        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
                        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
                        $startdates=$startDateFormatted." 00:00:00";
                        $enddates=$endDateFormatted." 23:59:59";
                        // Start the query
                        $query = doctordetails::query()
                        ->join('branches', 'ref_doctor_details.city', '=', 'branches.id')
                        ->join('zones', 'branches.zone_id', '=', 'zones.id')
                        ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*')
                        ->whereBetween('ref_doctor_details.created_at', [$startdates, $enddates]);
                        if (is_array($fitterremovedata)) {
                            $fitterremovedata = implode(' AND ', $fitterremovedata);
                        } elseif (!is_string($fitterremovedata)) {
                            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
                        }
                        foreach (explode(' AND ', $fitterremovedata) as $condition) {
                            [$column, $value] = explode('=', $condition);
                            // Handle columns with comma-separated values
                            $values = explode(',', trim($value, "'"));
                            $query->where(function ($q) use ($column,$values) {
                                foreach ($values as $val) {
                                    $q->orWhere($column,$val);
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

    public function fetchmorefitterdate(Request $request)
    {
        $fitterremovedata = $request->input('fitterremovedata');
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        // Start the query
        $query = doctordetails::query()
        ->join('branches', 'ref_doctor_details.city', '=', 'branches.id')
        ->join('zones', 'branches.zone_id', '=', 'zones.id')
        ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*')
        ->whereBetween('ref_doctor_details.created_at', [$startdates, $enddates]);
        if (is_array($fitterremovedata)) {
            $fitterremovedata = implode(' AND ', $fitterremovedata);
        } elseif (!is_string($fitterremovedata)) {
            return response()->json(['error' => 'Invalid fitterremovedata format'], 400);
        }
        foreach (explode(' AND ', $fitterremovedata) as $condition) {
            [$column, $value] = explode('=', $condition);
            // Handle columns with comma-separated values
            $values = explode(',', trim($value, "'"));
            $query->where(function ($q) use ($column,$values) {
                foreach ($values as $val) {
                    $q->orWhere($column,$val);
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
        $fitterremovedataall = $request->input('fitterremovedataall');
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        // Start the query
        // Start the query
        $query = doctordetails::query()
        ->join('branches', 'ref_doctor_details.city', '=', 'branches.id')
        ->join('zones', 'branches.zone_id', '=', 'zones.id')
        ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*')
        ->whereBetween('ref_doctor_details.created_at', [$startdates, $enddates]);

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
            $query->where(function ($q) use ($column,$values) {
                foreach ($values as $val) {
                    $q->orWhere($column,$val);
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
          $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        // Start the query
        // Start the query
        $query = doctordetails::query()
        ->join('branches', 'ref_doctor_details.city', '=', 'branches.id')
        ->join('zones', 'branches.zone_id', '=', 'zones.id')
        ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*')
        ->whereBetween('ref_doctor_details.created_at', [$startdates, $enddates]);
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
        ->join('zones', 'branches.zone_id', '=', 'zones.id')
        ->select('branches.Branch_name', 'zones.zone_name', 'ref_doctor_details.*')
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
    public function meetinginsert(Request $request){
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
        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
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
        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
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
            $query->where(function ($q) use ($column,$values) {
                foreach ($values as $val) {
                    $q->orWhere($column,$val);
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
    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
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
        $query->where(function ($q) use ($column,$values) {
            foreach ($values as $val) {
                $q->orWhere($column,$val);
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
    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
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
    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
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
        $query->where(function ($q) use ($column,$values) {
            foreach ($values as $val) {
                $q->orWhere($column,$val);
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
        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
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
        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
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
            $query->where(function ($q) use ($column,$values) {
                foreach ($values as $val) {
                    $q->orWhere($column,$val);
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
    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
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
        $query->where(function ($q) use ($column,$values) {
            foreach ($values as $val) {
                $q->orWhere($column,$val);
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
    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
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
    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
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
        $query->where(function ($q) use ($column,$values) {
            foreach ($values as $val) {
                $q->orWhere($column,$val);
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
    $validatedData = $request->validate([
        'zone_id' => 'required|string|max:255',
        'document_type_name' => 'required|string|max:255',
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
            // 'document_type_id'=> $request->document_type_id,
            'document_id' => 1
        ]));
        return response()->json(['success' => true, 'message' => 'Document saved successfully!']);
}
public function fetchdocument()
{
    $documents = DB::table('hms_document_manage')
    ->join('branches', 'hms_document_manage.zone_id', '=', 'branches.id')
    ->join('zones', 'branches.zone_id', '=', 'zones.id')
    ->select('hms_document_manage.*')
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
$imagePaths = array_map(function($path) {
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
    $validatedData = $request->validate([
        'user_fullname' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'password' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'email' => 'required|string|max:255',
                'mobile' => 'required|string|max:255',
        ]);
        $validatedData['password'] = Hash::make($validatedData['password']);
        $doctor = usermanagementdetails::create(array_merge($validatedData));
       return response()->json(['success' => true, 'message' => 'Doctor saved successfully!']);
}

public function licensedoc_detials(Request $request){
 
    $fitterremovedataall = $request->input('morefilltersall');    
    $datefiltervalue = $request->input('moredatefittervale');  

    $dates = explode(' - ', $datefiltervalue);
    // echo "<pre>";print_r($dates);exit;
    $startDate = $dates[0];  // "29/12/2024"
    $endDate = $dates[1];    // "04/01/2025"
   
    $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
    //echo "<pre>";print_r($dept);exit;
    $data = documentdetails::select('tbl_locations.name', 'tblzones.name as zone_name', 'hms_document_manage.*')
    ->join('tbl_locations', 'hms_document_manage.zone_id', '=', 'tbl_locations.id')
    ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
    ->whereDate('hms_document_manage.created_at', '>=', $startdates)->where('hms_document_manage.created_at', '<=', $enddates);
    
    // echo"<pre>"; print_r($data);exit;
        if($fitterremovedataall){          
                // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);           
                    $value = trim($value, "'");
                    $data->whereIn(trim($column), explode(',', $value));            
            }            
        }
        $data = $data->orderBy('hms_document_manage.created_at', 'desc')->get();  
    
    return response()->json($data);
}


public function licexpdatefilter(Request $request){
    $sec_date_type = $request->input('sec_date_type');
   
    $data= documentdetails::select('tbl_locations.name','tblzones.name as zone_name', 'hms_document_manage.*')
    ->join('tbl_locations', 'hms_document_manage.zone_id', '=', 'tbl_locations.id')
    ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id');
   
    if ($request->sec_date_type) {
        $data->where('hms_document_manage.expire_date', $request->sec_date_type);
    }
    $data = $data->orderBy('hms_document_manage.created_at', 'desc')->get();  
    // echo"<pre>"; print_r($data);exit;
    return response()->json($data);
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
                'camp_date' => 'required|string|max:255',
                'camp_type' => 'required|string|max:255',
                'camp_location' => 'required|string|max:255',
                'g_map' => 'required|string|max:255',
                'doctor_name' => 'required|string|max:255',
                'organized_by' => 'required|string|max:255',
                'camp_incharge' => 'required|string|max:255',
        ]);

        $campmanage = Campmanagement::create(array_merge($validatedData));
        return response()->json(['success' => true, 'message' => 'Doctor saved successfully!']);
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
    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
    // Start the query
    $query = Campmanagement::query();
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
        $query->where(function ($q) use ($column,$values) {
            foreach ($values as $val) {
                $q->orWhere($column,$val);
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
    $campdetails = Campmanagement::orderBy('created_at', 'desc')->get();
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
    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
    $data = Campmanagement::whereBetween('created_at', [$startdates, $enddates])->get();
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
        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        // Start the query
        $query = Campmanagement::query();
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
            $query->where(function ($q) use ($column,$values) {
                foreach ($values as $val) {
                    $q->orWhere($column,$val);
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

public function activitesadddata(Request $request)
{
    $validatedData = $request->validate([
        'camp_id' => 'required|string|max:255',
                'date_activites' => 'required|string|max:255',
                'activites' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'area_covered' => 'required|string|max:255',
                'images.*' => 'required|file|mimes:png|max:12048', // Validate images
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
    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
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
        $query->where(function ($q) use ($column,$values) {
            foreach ($values as $val) {
                $q->orWhere($column,$val);
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
    $startdates=$startDateFormatted." 00:00:00";
    $enddates=$endDateFormatted." 23:59:59";
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
        $query->where(function ($q) use ($column,$values) {
            foreach ($values as $val) {
                $q->orWhere($column,$val);
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
        'Branch' => 'required|string|max:255',
                'activites' => 'required|string|max:255',
                'cost' => 'required|string|max:255',
                'document_purchase_order.*' => 'required|file|mimes:png|max:12048', // Validate images
                'creatives.*' => 'required|file|mimes:png|max:12048', // Validate images

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
    $campdetails = Expensemanagement::orderBy('created_at', 'desc')->get();
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
    ->select('user_fullname','role_id','username')
    ->get();
    return response()->json($documents);
 }

 public function menuaccessurl()
{
    $userids = auth()->user()->username;

    $documents = DB::table('users')
        ->join('hms_menu', function ($join) {
            $join->on(DB::raw('FIND_IN_SET(hms_menu.id, users.menu_id)'), '>', DB::raw('0'));
        })
        ->where('username', $userids)
        ->select('link', 'icon', 'hms_menu.menu_name')
        ->get();

    return response()->json($documents);
}

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

        $billingList = billinglistdetails::select('id','type','paymenttype',
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
        $type = VehicleType::orderBy('id', 'asc')->get();
        $vehicle_no = VehicleDetails::select('id','vehicle_no','make')->where('vehicle_no','!=','')->orderBy('id', 'asc')->get();
        return view('superadmin.vehicle_details', compact('admin','locations','type','vehicle_no'));
    }

    public function vehicleDetails(){
        $vehicleDetails=VehicleDetails::select('vehicle_details.*', 'vehicle_details.created_at', 'tbl_locations.name', 'vehicle_type.type')->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
                ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')->orderBy('vehicle_details.created_at', 'desc')->get();
        return response()->json($vehicleDetails);
    }

    public function vehicleDocumentEdit($id){
        $vehicleDetails=VehicleDetails::where('id',$id)->first();
        return response()->json($vehicleDetails);
    }
    
    public function vehicleDocumentDetails(Request $request){
        $datefiltervalue = $request->input('moredatefittervale'); 
        $statusid = $request->input('statusid'); 
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        $query=VehicleDocument::select('vehicle_document_details.document_type','vehicle_document_details.expire_dates','vehicle_document_details.expire_date','vehicle_document_details.id as did','vehicle_document_details.document_name','vehicle_details.*', 'vehicle_type.type')
                    ->join('vehicle_details', 'vehicle_document_details.vehicle_id', '=', 'vehicle_details.id')
                    ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id');
        if($statusid == 2){                 
            $query->whereBetween('vehicle_document_details.created_at', [$startdates, $enddates]);
        } 
        $VehicleDocument = $query->orderBy('vehicle_document_details.created_at', 'desc')->get();
        return response()->json($VehicleDocument);
    }

    public function vehicleDocumentUpdate (Request $request){
        //echo "<pre>";print_r($request->all());exit;
          // Validate incoming request
          $validatedData =  $request->validate([
                'expire_date' => 'required|date',
                'image.*' => 'required|file|mimes:pdf|max:12048', // Validate images
            ]);        
           
            // Get request data
            $documentId = $request->input('id');
            $expireDate = $request->input('expire_date');
            $expireDates = $request->input('expire_dates');
            $documentType = $request->input('document_type');
            $alldocumentType = $request->input('update_documents_all');
            $documentType_view[] = $alldocumentType;

            $output = $expireDates.",".$expireDate;
            $dates = explode(',', $output);
            sort($dates);
            $latestDate = end($dates);
            $imagePath = ''; // Initialize a variable for the image path
        if ($request->hasFile('image')) { // Check if 'image' is present (change 'image' to the name of your input field)
            $image = $request->file('image'); // Get the uploaded image
            $filename = time() . '_' . $image->getClientOriginalName(); // Generate a unique file name
            $destinationPath = public_path('document_data'); // Path to store the uploaded file
            $image->move($destinationPath, $filename); // Move file to the destination folder
            $imagePath = 'document_data/' . $filename; // Save the relative path of the uploaded file
        }
        $documentType_view[] = $imagePath; 
        $cleanedArray = array_map('stripslashes', $documentType_view);        
        $cleanedArray[0] = trim($cleanedArray[0], '[]\"');
        $documentTypeJson = json_encode($cleanedArray);

        if(empty($documentId)){
            $docId=VehicleDocument::where('vehicle_id', $request->vehicle_id)->where('document_type', $request->document_type)->count();
            if($docId == 1){
                return response()->json(['success' => false, 'message' => 'Document already uploaded!']);
            }
            $vehicle_id = $request->input('vehicle_id');
             $documents = VehicleDocument::create([
                    'document_name' => json_encode($imagePath),
                    'vehicle_id' => $vehicle_id,
                    'document_type' => $documentType,
                    'expire_date' => $expireDate,
                ]);
        }else{
             $documents = VehicleDocument::where('id', $documentId)
                    ->update([
                        'document_name' => $documentTypeJson,
                        'expire_date' => $latestDate,
                        'expire_dates' => $output
                    ]);
        }
        
        return response()->json(['success' => true, 'message' => 'Document Updated successfully!']);

    }
    public function vehicleAdded(Request $request)
    {
		//echo "<pre>";print_r($request->all());exit;
        $validatedData = $request->validate([
            'make' => 'required|string|max:255',
                    'year_of_manufacture' => 'required|string|max:255',
                    'registration_number' => 'required|unique:vehicle_details,registration_number|string|max:255',
                    'engine_number' => 'required|string|max:255',
                    'chassis_number' => 'required|string|max:255',
                    'fuel_type' => 'required|string|max:255',
            ], [
                'registration_number.unique' => 'The registration number has already been taken!',
            ]);

        $vehicle_no = VehicleDetails::latest()->first();
        if($vehicle_no) {
            $vehicle_no = $vehicle_no->vehicle_no;
            $vehicle_no = (int) substr($vehicle_no, 2);
            $vehicle_no = $vehicle_no + 1;
            $vehicle_no = 'VH' . str_pad($vehicle_no, 4, '0', STR_PAD_LEFT);
        } else {
            $vehicle_no = 'VH0001';
        } 
        
       $location_id =  TblLocationModel::select('id','zone_id')->where('name', $request->branch)->first();                    
       $vehicle_type =  VehicleType::select('id')->where('type', $request->vehicle_type)->first();                    

            $data = VehicleDetails::create($request->only(['fuel_type']));
           
            VehicleDetails::updateOrCreate(['id'   => $data['id']],array_merge($validatedData, [	
                'vehicle_no' => $vehicle_no,	
                'vehicle_type' => $vehicle_type->id,	
                'branch' => $location_id->id,			
                'zone_id' => $location_id->zone_id,			
            ]));          
            
            return redirect()->back()
            ->with('success', 'Vehicle added successfully!');   
    }

    public function vehicleUpdate(Request $request)
    {
		//echo "<pre>";print_r($request->all());exit;
        $vehicle = VehicleDetails::findOrFail($request->id);
        $validatedData = $request->validate([
            'make' => 'required|string|max:255',
                    'year_of_manufacture' => 'required|string|max:255',
                    'registration_number' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('vehicle_details')->ignore($vehicle->id),
                    ],
                    'engine_number' => 'required|string|max:255',
                    'chassis_number' => 'required|string|max:255',
                    'fuel_type' => 'required|string|max:255',
            ], [
                'registration_number.unique' => 'The registration number has already been taken!',
            ]);
                
       $location_id =  TblLocationModel::select('id','zone_id')->where('name', $request->branch)->first();                    
       $vehicle_type =  VehicleType::select('id')->where('type', $request->vehicle_type)->first();
           
        VehicleDetails::updateOrCreate(['id'   => $request->id],array_merge($validatedData, [		
            'vehicle_type' => $vehicle_type->id,	
            'branch' => $location_id->id,	
            'zone_id' => $location_id->zone_id,			
        ]));          
            
            return redirect()->back()
            ->with('success', 'Vehicle updated successfully!');   
    }

    public function vehicleDocumentFilter(Request $request)
    {         
        //echo "<pre>";print_r($request->all());exit;
        $fitterremovedataall = $request->input('morefilltersall');        
        
        $query = VehicleDetails::select('vehicle_details.*', 'vehicle_details.created_at', 'tbl_locations.name', 'vehicle_type.type')->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
                 ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')
                 ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id');

        if($fitterremovedataall){ 
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);           
                if($column == 'vehicle_details.registration_number'){
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
                }else{ 
                    $value = trim($value, "'");
                    $query->whereIn(trim($column), explode(',', $value));
                }           
            }							
        }
		$query = $query->orderBy('vehicle_details.created_at', 'desc')->get();
        return response()->json($query);
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

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";  
        
        $query = VehicleDocument::select('vehicle_document_details.document_type','vehicle_document_details.expire_dates','vehicle_document_details.expire_date','vehicle_document_details.id as did','vehicle_document_details.document_name','vehicle_details.*', 'vehicle_type.type')
                ->join('vehicle_details', 'vehicle_document_details.vehicle_id', '=', 'vehicle_details.id')
                ->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
                ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
                ->whereDate('vehicle_document_details.expire_date', '>=', $startdates)->where('vehicle_document_details.expire_date', '<=', $enddates);

        if($fitterremovedataall){ 
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);           
                if($column == 'vehicle_details.registration_number'){
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
                }else{ 
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

        if($fitterremovedataall){ 
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);  
                if(trim($value, "'") == 'Petrol'){
                    $query->where(trim($column), 1);
                }else if(trim($value, "'") == 'Diesel'){
                    $query->where(trim($column), 2);
                }else if(trim($value, "'") == 'Electronic Vehicle'){
                    $query->where(trim($column), 3);
                }else if(trim($value, "'") == 'CNG'){
                    $query->where(trim($column), 4);
                }else{
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
        
        $query = VehicleDocument::select('vehicle_document_details.document_type','vehicle_document_details.expire_dates','vehicle_document_details.expire_date','vehicle_document_details.id as did','vehicle_document_details.document_name','vehicle_details.*', 'vehicle_type.type')
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

        if($fitterremovedataall){ 
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);  
                if(trim($value, "'") == 'Petrol'){
                    $query->where(trim($column), 1);
                }else if(trim($value, "'") == 'Diesel'){
                    $query->where(trim($column), 2);
                }else if(trim($value, "'") == 'Electronic Vehicle'){
                    $query->where(trim($column), 3);
                }else if(trim($value, "'") == 'CNG'){
                    $query->where(trim($column), 4);
                }else{
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

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        //echo "<pre>";print_r($dept);exit;
        $data = VehicleDocument::select('vehicle_document_details.document_type','vehicle_document_details.expire_dates','vehicle_document_details.expire_date','vehicle_document_details.id as did','vehicle_document_details.document_name','vehicle_details.*', 'vehicle_type.type')
                ->join('vehicle_details', 'vehicle_document_details.vehicle_id', '=', 'vehicle_details.id')
                ->join('tbl_locations', 'vehicle_details.branch', '=', 'tbl_locations.id')
                ->join('vehicle_type', 'vehicle_details.vehicle_type', '=', 'vehicle_type.id')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
                ->whereDate('vehicle_document_details.expire_date', '>=', $startdates)->where('vehicle_document_details.expire_date', '<=', $enddates);

            if($fitterremovedataall){           
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
            foreach($request->file('file') as $key => $file)
            {
                $fileName = time().rand(1,99).'.'.$file->extension();  
                $file->move(public_path('uploads'), $fileName);
                $files[]['name'] = $fileName;
            }

            foreach ($files as $key => $file) {
                ImageModel::updateOrCreate(
                    ['ticket_id'   => '0000'],['imgName' => $file['name'],'ticket_id' => $ticketId]
                );
            }
        }
        //$id = TicketActivitiesModel::find($ticketId);
        return response()->json(['status'=>"success",'userid'=>$ticketId]);
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
        if($subcategories) {
            return response()->json([
                'status'=>200,
                'subcategories'=> $subcategories,
            ]);
        }
        else {
            return response()->json([
                'status'=>404,
                'message'=>'No priority found.'
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
        //print_r($ticketDetails); exit;
        return view('superadmin.ticket', compact('admin','statuses','priorities','locations','categories'));
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
        
        $query = TicketDetails::select('tbl_ticket_details.*',
                'tbl_user_departments.depart_name','sub_category.sub_category_name',
                'ticket_priority.priority_name','ticket_priority.priority_color','tbl_locations.name',
                'ticket_status_master.status_name','users.user_fullname','ticket_status_master.status_color') //I need the ID parameter here
                ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                ->join('users','tbl_ticket_details.created_by','=', 'users.id')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
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
        
        $query = TicketDetails::select('tbl_ticket_details.*',
                'tbl_user_departments.depart_name','sub_category.sub_category_name',
                'ticket_priority.priority_name','ticket_priority.priority_color','tbl_locations.name',
                'ticket_status_master.status_name','users.user_fullname','ticket_status_master.status_color') //I need the ID parameter here
                ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                ->join('users','tbl_ticket_details.created_by','=', 'users.id')
                ->where('tbl_ticket_details.is_management_approve', 1)
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
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
        
        $query = TicketDetails::select('tbl_ticket_details.*',
                    'tbl_user_departments.depart_name','sub_category.sub_category_name',
                    'ticket_priority.priority_name','ticket_priority.priority_color','tbl_locations.name',
                    'ticket_status_master.status_name','tbl_account_details.fullname','ticket_status_master.status_color') //I need the ID parameter here
                    ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                    ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                    ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                    ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                    ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                    ->join('tbl_account_details','tbl_ticket_details.created_by','=', 'tbl_account_details.user_id');

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
                    'tbl_user_departments.depart_name',
                    'sub_category.sub_category_name',
                    'ticket_priority.priority_name',
                    'ticket_status_master.status_name',
                    'users.user_fullname',
                    'ticket_priority.priority_color'
                ) //I need the ID parameter here
                    ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                    ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                    ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                    ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                    ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                    ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                    ->join('users','tbl_ticket_details.created_by','=', 'users.id')
                    ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
                    ->where('tbl_ticket_details.created_by', auth()->user()->id)
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
                    'tbl_user_departments.depart_name',
                    'sub_category.sub_category_name',
                    'ticket_priority.priority_name',
                    'ticket_status_master.status_name',
                    'users.user_fullname',
                    'ticket_priority.priority_color'
                ) //I need the ID parameter here
                    ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                    ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                    ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                    ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                    ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                    ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                    ->join('users','tbl_ticket_details.created_by','=', 'users.id')
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

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";

        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'tbl_user_departments.depart_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        ) //I need the ID parameter here                         
                            ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                            ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                            ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                            ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                            ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                            ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                            ->join('users','tbl_ticket_details.created_by','=', 'users.id')
                            ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id');
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
            foreach($request->file('file') as $key => $file)
            {
                $fileName = time().rand(1,99).'.'.$file->extension();  
                $file->move(public_path('uploads'), $fileName);
                $files[]['name'] = $fileName;
            }

            foreach ($files as $key => $file) {
                ImageModel::updateOrCreate(
                    ['ticket_id'   => '0000'],['imgName' => $file['name'],'ticket_id' => $ticketId]
                );
            }
        }
        $id = TicketActivitiesModel::find($ticketId);
        return response()->json(['status'=>"success",'userid'=>$ticketId]);
    }

    public function ticketAdded(Request $request)
    {
		//echo "<pre>";print_r($request->all());exit;
        $validatedData = $request->validate([
            'location' => 'required|string|max:255',
                    'department' => 'required|string|max:255',
                    'sub_department_id' => 'required|string|max:255',
                    'target_date' => 'required|string|max:255',
                    'subject' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
                    'priority' => 'required|string|max:255',
					'images.*' => 'required|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        $ticAdmin =  AdminUserDepartments::where('depart_id', $department_id->id)->get();
        //echo "<pre>";print_r(count($ticAdmin));exit;
        if(count($ticAdmin) == 0){
            return response()->json(['status'=>"error",'errors'=>'No ticket handler for this Department!']);  
        }
        $removeDept = TblUserDepartments::where('depart_id', $ticAdmin[0]->depart_id)->delete();
        foreach($ticAdmin as $user){
            TblUserDepartments::updateOrCreate([
                'admin_user_departments_id' => $user->id,
                'user_id' => $user->user_id,
                'depart_id' => $user->depart_id,
            ]);
        }

            $data = TicketDetails::create($request->only(['sub_department_id']));
            //echo "<pre>";print_r($data);exit;
            $ticketCreate = TicketDetails::updateOrCreate(['id'   => $data['id']],array_merge($validatedData, [
                'created_by' => auth()->user()->id,	
                'ticket_no'     => $ticketNo,	
                'ticket_status' => $status,	
                'is_read' => '1',	
                'department_id' => $department_id->id,	
                'location_id' => $location_id->id,	
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

    public function myTicketFetch(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale'); 
        $statusid = $request->input('statusid'); 
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
                            'tbl_user_departments.depart_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        ) //I need the ID parameter here
                            ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                            ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                            ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                            ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                            ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                            ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                            ->join('users','tbl_ticket_details.created_by','=', 'users.id')
                            ->where('tbl_ticket_details.created_by', auth()->user()->id);
                if($statusid == 2){                 
                    $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
                } 
                $ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

    public function ticketFetch(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale'); 
        $statusid = $request->input('statusid'); 

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
            $query = TicketDetails::select('tbl_ticket_details.*',
                            'tbl_user_departments.depart_name','sub_category.sub_category_name',
                            'ticket_priority.priority_name','ticket_priority.priority_color','tbl_locations.name',
                            'ticket_status_master.status_name','users.user_fullname','ticket_status_master.status_color') //I need the ID parameter here
                            ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                            ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                            ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                            ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                            ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                            ->join('users','tbl_ticket_details.created_by','=', 'users.id')
                            ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                            ->where('tbl_ticket_details.is_management_approve', 1);
        if($statusid == 2){                 
            $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
        } 
        $ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
            return response()->json($ticketdetails);        
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

    public function allTicketFetch(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale'); 
        $statusid = $request->input('statusid'); 

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
            $query = TicketDetails::select('tbl_ticket_details.*',
                            'tbl_user_departments.depart_name','sub_category.sub_category_name',
                            'ticket_priority.priority_name','ticket_priority.priority_color','tbl_locations.name',
                            'ticket_status_master.status_name','users.user_fullname','ticket_status_master.status_color') //I need the ID parameter here
                            ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                            ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                            ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                            ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                            ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                            ->join('users','tbl_ticket_details.created_by','=', 'users.id')
                            ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id');
            if($statusid == 2){                 
                $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
            } 
            $ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
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
            'tbl_user_departments.depart_name',
            'sub_category.sub_category_name',
            'ticket_priority.priority_name',
            'ticket_status_master.status_name',
            'users.user_fullname',
            'ticket_priority.priority_color'
        ) //I need the ID parameter here
            ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
            ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
            ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
            ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
            ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
            ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
            ->join('users','tbl_ticket_details.created_by','=', 'users.id')
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

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";       
        $query = TicketDetails::select('tbl_ticket_details.*',
                    'tbl_user_departments.depart_name','sub_category.sub_category_name',
                    'ticket_priority.priority_name','ticket_priority.priority_color','tbl_locations.name',
                    'ticket_status_master.status_name','users.user_fullname','ticket_status_master.status_color') //I need the ID parameter here
                    ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                    ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                    ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                    ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                    ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                    ->join('users','tbl_ticket_details.created_by','=', 'users.id')
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
        $query = TicketDetails::select('tbl_ticket_details.*',
                    'tbl_user_departments.depart_name','sub_category.sub_category_name',
                    'ticket_priority.priority_name','ticket_priority.priority_color','tbl_locations.name',
                    'ticket_status_master.status_name','users.user_fullname','ticket_status_master.status_color') //I need the ID parameter here
                    ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                    ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                    ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                    ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                    ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                    ->join('users','tbl_ticket_details.created_by','=', 'users.id')
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

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        //echo "<pre>";print_r($dept);exit;
        $data = TicketDetails::select('tbl_ticket_details.*',
                'tbl_user_departments.depart_name','sub_category.sub_category_name',
                'ticket_priority.priority_name','ticket_priority.priority_color','tbl_locations.name',
                'ticket_status_master.status_name','users.user_fullname','ticket_status_master.status_color') //I need the ID parameter here
                ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                ->join('users','tbl_ticket_details.created_by','=', 'users.id')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
                ->where('tbl_ticket_details.is_management_approve', 1)
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

    public function allticketDateFillter(Request $request)
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
        $data = TicketDetails::select('tbl_ticket_details.*',
                'tbl_user_departments.depart_name','sub_category.sub_category_name',
                'ticket_priority.priority_name','ticket_priority.priority_color','tbl_locations.name',
                'ticket_status_master.status_name','users.user_fullname','ticket_status_master.status_color') //I need the ID parameter here
                ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                ->join('users','tbl_ticket_details.created_by','=', 'users.id')
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
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
                'tbl_user_departments.depart_name',
                'sub_category.sub_category_name',
                'ticket_priority.priority_name',
                'ticket_status_master.status_name',
                'users.user_fullname',
                'ticket_priority.priority_color'
            ) //I need the ID parameter here
                ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                ->join('tbl_user_departments', 'tbl_ticket_details.department_id', '=', 'tbl_user_departments.id')
                ->join('sub_category', 'tbl_user_departments.id', '=', 'sub_category.category_id')
                ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                ->join('users','tbl_ticket_details.created_by','=', 'users.id')
                ->where('tbl_ticket_details.created_by', auth()->user()->id)
                ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
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

    // public function payRequest() 
    // {
    //     $staffId = auth()->user()->id;
    //     $admin = auth()->user();
    //     $locations = TblLocationModel::orderBy('name', 'asc')->get();
    //     return view('superadmin.payrequest', compact('admin','locations'));
    // }

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

    try {
        // Make the API request with a 60-second timeout and SSL verification disabled
        $response = Http::timeout(60)->withoutVerifying()->get($apiUrl, [
            'api_key' => $apiKey
        ]);

        // Check if the response is successful
        if ($response->successful()) {
            $employeeData = $response->json();

            // Check if 'data' key exists and is an array
            if (isset($employeeData['data']) && is_array($employeeData['data'])) {
                $employeeData = $employeeData['data'];

                // Remove the first element if it's a header row or not required
                array_shift($employeeData);

                return view('superadmin.check_in', compact('admin', 'employeeData'));
            } else {
                return response()->json(['error' => 'Invalid API response format.'], 500);
            }
        } else {
            return response()->json(['error' => 'API responded with an error.'], 500);
        }

    } catch (\Exception $e) {
        // Handle connection issues, timeouts, and other exceptions
        return response()->json(['error' => 'Request failed: ' . $e->getMessage()], 500);
    }
}

    // public function dailySummaryDetails(Request $request){
    //     $datefiltervalue = $request->input('moredatefittervale');
    //     $statusid = $request->input('statusid');
    //     $dates = explode(' - ', $datefiltervalue);
    //     $startDate = $dates[0];  // "29/12/2024"
    //     $endDate = $dates[1];    // "04/01/2025"
    //     $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    //     $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
    //     $dailySummary = [];
    //     $startdates=$startDateFormatted." 00:00:00";
    //     $enddates=$endDateFormatted." 23:59:59";
    //     // $query= DailySummary::selectRaw('
    //     //                 name,
    //     //                 DATE_FORMAT(daily_summary.created_at, "%Y-%m-%d %H:%i:%s") as date,
    //     //                 DATE(`billdate`) as `billdate`,
    //     //                 SUM(CASE WHEN `type` = "O/P - Income" THEN `amt` ELSE 0 END) AS `opIncome`,
    //     //                 SUM(CASE WHEN `type` = "I/P - Income" THEN COALESCE(NULLIF(`amt`, 0), `advances_amt`) ELSE 0 END) AS `ipIncome`,
    //     //                 SUM(CASE WHEN `type` = "Pharmacy - Income" THEN `amt` ELSE 0 END) AS `pharmacyIncome`,
    //     //                 SUM(CASE WHEN `type` IN ("O/P - Income", "I/P - Income", "Pharmacy - Income") THEN COALESCE(NULLIF(`amt`, 0), `advances_amt`) ELSE 0 END) AS `total_amt`
    //     //             ')
    //     //             ->from('daily_summary')  // Alias for the DailySummary table
    //     //             ->join('tbl_branch', 'daily_summary.branch', '=', 'tbl_branch.location_id')
    //     //             ->whereNotNull('billdate');
    //     //     if($statusid == 2){                 
    //     //         $query->whereDate('billdate', '>=', $startdates)->whereDate('billdate', '<=', $enddates);
    //     //     } 
    //     //     $dailySummary=  $query->groupBy('branch',DB::raw('DATE(`billdate`)'))->orderBy('daily_summary.created_at', 'desc')->get();
    //     return response()->json($dailySummary);
    // }
    
    // public function dailyDateFilter(Request $request){
    //     $datefiltervalue = $request->input('datefiltervalue');
    //     $fitterremovedataall = $request->input('morefilltersall');

    //     $dates = explode(' - ', $datefiltervalue);
    //     $startDate = $dates[0];  // "29/12/2024"
    //     $endDate = $dates[1];    // "04/01/2025"
    //     $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    //     $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

    //     $startdates=$startDateFormatted." 00:00:00";
    //     $enddates=$endDateFormatted." 23:59:59";
    //     $data= DailySummary::selectRaw('
    //                     name,
    //                     DATE_FORMAT(daily_summary.created_at, "%Y-%m-%d %H:%i:%s") as date,
    //                     DATE(`billdate`) as `billdate`,
    //                     SUM(CASE WHEN `type` = "O/P - Income" THEN `amt` ELSE 0 END) AS `opIncome`,
    //                     SUM(CASE WHEN `type` = "I/P - Income" THEN COALESCE(NULLIF(`amt`, 0), `advances_amt`) ELSE 0 END) AS `ipIncome`,
    //                     SUM(CASE WHEN `type` = "Pharmacy - Income" THEN `amt` ELSE 0 END) AS `pharmacyIncome`,
    //                     SUM(CASE WHEN `type` IN ("O/P - Income", "I/P - Income", "Pharmacy - Income") THEN COALESCE(NULLIF(`amt`, 0), `advances_amt`) ELSE 0 END) AS `total_amt`
    //                 ')
    //                 ->from('daily_summary')  // Alias for the DailySummary table
    //                 ->join('tbl_branch', 'daily_summary.branch', '=', 'tbl_branch.location_id')
    //                 ->whereNotNull('billdate')
    //                 ->whereDate('billdate', '>=', $startdates)->whereDate('billdate', '<=', $enddates);

    //         if($fitterremovedataall){           
    //             // Split conditions by 'AND' and loop through them
    //                     foreach (explode(' AND ', $fitterremovedataall) as $condition) {
    //                         [$column, $value] = explode('=', $condition);           
    //                             $value = trim($value, "'");
    //                             $data->whereIn(trim($column), explode(',', $value));            
    //                     }            
    //             }
    //             $dailySummary =  $data->groupBy('branch',DB::raw('DATE(`billdate`)'))->get();
    //     return response()->json($dailySummary);
    // }

    // public function dailyBranchFilter(Request $request){
    //     $datefiltervalue = $request->input('moredatefittervale');
    //     $fitterremovedataall = $request->input('morefilltersall');
    
    //     $dates = explode(' - ', $datefiltervalue);
    //     $startDate = $dates[0];  // "29/12/2024"
    //     $endDate = $dates[1];    // "04/01/2025"
    //     $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    //     $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

    //     $startdates=$startDateFormatted." 00:00:00";
    //     $enddates=$endDateFormatted." 23:59:59";
    //     $data= DailySummary::selectRaw('
    //                     name,
    //                     DATE_FORMAT(daily_summary.created_at, "%Y-%m-%d %H:%i:%s") as date,
    //                     DATE(`billdate`) as `billdate`,
    //                     SUM(CASE WHEN `type` = "O/P - Income" THEN `amt` ELSE 0 END) AS `opIncome`,
    //                     SUM(CASE WHEN `type` = "I/P - Income" THEN COALESCE(NULLIF(`amt`, 0), `advances_amt`) ELSE 0 END) AS `ipIncome`,
    //                     SUM(CASE WHEN `type` = "Pharmacy - Income" THEN `amt` ELSE 0 END) AS `pharmacyIncome`,
    //                     SUM(CASE WHEN `type` IN ("O/P - Income", "I/P - Income", "Pharmacy - Income") THEN COALESCE(NULLIF(`amt`, 0), `advances_amt`) ELSE 0 END) AS `total_amt`
    //                 ')
    //                 ->from('daily_summary')  // Alias for the DailySummary table
    //                 ->join('tbl_branch', 'daily_summary.branch', '=', 'tbl_branch.location_id')
    //                 ->whereNotNull('billdate')
    //                 ->whereDate('billdate', '>=', $startdates)->whereDate('billdate', '<=', $enddates);

    //         if($fitterremovedataall){           
    //             // Split conditions by 'AND' and loop through them
    //                     foreach (explode(' AND ', $fitterremovedataall) as $condition) {
    //                         [$column, $value] = explode('=', $condition);           
    //                             $value = trim($value, "'");
    //                             $data->whereIn(trim($column), explode(',', $value));            
    //                     }            
    //             }
    //             $dailySummary =  $data->groupBy('branch',DB::raw('DATE(`billdate`)'))->get();
    //     return response()->json($dailySummary);
    // }    

//new save DB

private function zoneMapping(){
                            $zoneMap =      [
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
                //   echo "<pre>111111";print_r($data);    exit;
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
                        // echo "<pre>";print_r($regData);exit;
            return response()->json($regData); 
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
            set_time_limit(0); 
        //     $checkinSummary = [];
        //     $url = 'https://mocdoc.in/api/checkedin/draravinds-ivf'; 
        //     $checkinArr = $this->checkinreportAPI($request->moredatefittervale, $fitterremovedataall=null,$request->apistatus,$request->statusid,$url);
        // if($checkinArr == 1){
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
        // }       
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

    public function checkInReportEdit(Request $request){
        // echo "<pre>";print_r($request->all());exit;
          set_time_limit(0); 
        $employeeData = $this->hrmUsers();        
       $checkin_rpt = CheckinModel::where('id',$request->id)->first();
       return response()->json([
                'employee_data' => $employeeData,
                'checkin_report' => $checkin_rpt,
            ]);
    }

    public function checkInTimeLine (Request $request){
        // echo "<pre>";print_r($request->id);exit;
       $checkin_rpt = CheckinModel::all();
       return response()->json($checkin_rpt);
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
        //    echo "<pre>";print_r($request->all());exit;
         foreach ($_POST as $key => $value) {
                if (strpos($key, 'treat_amt_') === 0) {
                    $treatment_amounts[] = $value;
                }
            }
            $treatment_amt_string = implode(',', $treatment_amounts);
        //   echo "<pre>";print_r($request->all());exit;  
         $checkin_rpt =  CheckinModel::where('id', $request->income_id)->update([
            'treat_amt' => $treatment_amt_string ?? null,
            // 'location_id' => $request->location_id ?? null,
            'cc_audit_id' => $request->cc_audit_employment_id ?? null,
            'cc_employment_id' => $request->cc_employment_id ?? null,
            'treatment_category' => $request->treatment_category ?? null,
            'stage_of_treatment' => $request->stage_trt ?? null,
            'cc_name' => $request->cc_name ?? null,
            'cc_audit_name' => $request->cc_audit_name ?? null,
            'ip_phar_due' => $request->ip_phar_due ?? null,
            'next_appointment_date' => $request->next_appt_date ?? null,
            'ef_fet_ilnj' => $request->fincial_icsi ?? null,
            'od_fet_legal' => $request->od_fet_legal ?? null,
            'ed_fet_legal' => $request->ed_fet_legal ?? null,
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

    // public function checkinreportAPI($filterdate,$fitterremovedataall,$apistatus,$statusid,$url){
    //         $max_retries = 3; 
    //         $checkin_insert = "";
    //         $dates = explode(' - ', $filterdate);
    //         $startDate = $dates[0] ?? '';  // "29/12/2024"
    //         $endDate = $dates[1] ?? '';
    //         $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    //         $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
    //         $start = Carbon::createFromFormat('Y-m-d', $startDateFormatted);
    //         $end = Carbon::createFromFormat('Y-m-d', $endDateFormatted);
    //         $start_date = $start->format('Ymd').'00:00:00';
    //         $end_date = $end->format('Ymd').'23:59:59';        
    //         $locations = $this->cityArray();             
    //         foreach($locations as $k => $v){
    //             $checkin_data = $this->postCurlApi($url, $start_date, $k, $max_retries,$apistatus,$end_date);
    //             if (!empty($checkin_data['checkinlist'])) {
    //                 foreach ($checkin_data['checkinlist'] as $checkin) {
    //                    $checkinData = $this->saveCurlData($checkin, $k,$apistatus,$v,"");
    //                    $checkin_insert  = CheckinModel::insert($checkinData);
    //                 }
    //             }
    //         }
    //         return   $checkin_insert;
    //         // return  1;
    //         // echo "<pre>";print_r($checkin_insert);exit;
    // }

    public function incomeReportFetch(Request $request){
        set_time_limit(0); 
        $incomeArr = [];
    //     $url = 'https://mocdoc.in/api/get/billlist/draravinds-ivf'; 
    //     $dates = explode(' - ', $request->moredatefittervale);
    //     $startDate = $dates[0] ?? '';  
    //     $endDate = $dates[1] ?? '';
    //     $start_date = Carbon::createFromFormat('d/m/Y', $startDate);
    //     $end_date = Carbon::createFromFormat('d/m/Y', $endDate);
    //     $indates = [];
    //         while ($start_date <= $end_date) {
    //             $indates[] = $start_date->format('Ymd');
    //             $start_date->addDay();
    //         }
    //     $checkinArr = $this->incomereportAPI($request->moredatefittervale, $fitterremovedataall=null,$request->apistatus,$request->statusid,$url,$indates);
    // if($checkinArr == 1){        
    //     $checkins = CheckinModel::all();
    //      $checkins = CheckinModel::whereBetween('date', [$start_date, $end_date])->get();
    //     return response()->json($checkins);
    // }     
     $zone_locations = TblLocationModel::orderBy('name', 'asc')->get();
              
    return response()->json(['data' => $incomeArr,'dropdown' => $zone_locations]);
}
    
public function incomereportAPI($filterdate, $fitterremovedataall, $apistatus, $statusid, $url, $indates)
{   
    $result = [];
    $max_retries = 3;
    $output = []; 
    $inc_Data = [];
    $processedLocations = [];
    foreach ($indates as $dte) {
        if (strpos($fitterremovedataall, 'tblzones.name') !== false && strpos($fitterremovedataall, 'tbl_locations.name') === false) {
            preg_match("/tblzones\.name='([^']+)'/", $fitterremovedataall, $matches);
            $get_zone = $matches[1] ?? null;
            $zone_name = TblZonesModel::select('name')->where('name',$get_zone)->first();     
            $zone_location = $this->selectQuery($get_zone);
            $ilocation = $this->cityArray();
            //  echo "<pre>333333";print_r($zone_location);exit; 
            foreach ($zone_location as $zone) {
                $locationKey = array_search($zone->name, $ilocation);
                 if (!$locationKey || in_array($locationKey, $processedLocations)) {
                            continue;
                        }
                $processedLocations[] = $locationKey;
                $income_data = $this->postCurlApi($url, $dte, $locationKey, $max_retries, $apistatus, "");
                
                if (!empty($income_data['billinglist'])) {
                    foreach ($income_data['billinglist'] as $billing) {
                        $inc_Data[] = $this->saveCurlData($billing, $locationKey, $apistatus, $zone->name,$zone_name->name);                       
                        }                       
                    }   
                } 
                echo "<pre>333333";print_r($inc_Data);exit;
                    $paymentTypes = ['Cash', 'Card', 'Neft', 'Credit', 'UPI'];                    

                    foreach ($inc_Data as $row) {
                        $key = $row['billdate'] . '|' . $row['type'] . '|' . $row['area'];

                        if (!isset($result[$key])) {
                            $result[$key] = array_fill_keys($paymentTypes, 0);
                            $result[$key]['Date'] = $row['billdate'];
                            $result[$key]['Type'] = $row['type'];
                            $result[$key]['Location'] = $row['area'];
                            $result[$key]['Total'] = 0;
                        }

                        $payment = ucfirst(strtolower($row['paymenttype']));
                        if (in_array($payment, $paymentTypes)) {
                            $result[$key][$payment] += $row['amt'];
                            $result[$key]['Total'] += $row['amt'];
                        }
                    }

                    foreach ($result as $entry) {
                        $output[] = [
                            'cash_amt'    => $entry['Cash'],
                            'card_amt'    => $entry['Card'],
                            'neft_amt'    => $entry['Neft'],
                            'credit_amt'  => $entry['Credit'],
                            'upi_amt'     => $entry['UPI'],
                            'billdate'    => $entry['Date'],
                            'type'        => $entry['Type'],
                            'Location'    => $entry['Location'],
                            'total_amt'   => $entry['Total'],
                        ];
                    }
                    // echo "<pre>444444";print_r($result);
                    $output = array_values($result);
                //  echo "<pre>";print_r($result);
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
            preg_match_all("/'([^']+)'/", $fitterremovedataall, $matches);
            $dat_values = $matches[1];          
                                                      
            $zones_iid = TblZonesModel::select('id','name')->where('name',$dat_values[0])->first();                                               
            $zone_locatin_id =TblLocationModel::select('name','zone_id')->where('name',$dat_values[1])->first();   
            if($zones_iid->id == $zone_locatin_id->zone_id){
                $cilocaton = $this->cityArray();    
                $zlocaton = array_search($dat_values[1] , $cilocaton);
                $income_data = $this->postCurlApi($url, $dte, $zlocaton, $max_retries,$apistatus,""); 
                // echo "<pre>";print_r($income_data);exit;                                                             
            if (!empty($income_data['billinglist'])) {
                    foreach ($income_data['billinglist'] as $billing) {
                        $inc_Data[] = $this->saveCurlData($billing, $zlocaton,$apistatus,$zone_locatin_id->name,$zones_iid->name);  
                    }                    
                }
             }  
            foreach ($inc_Data as $item) {
                        $key = $item['type'] . '' . $item['paymenttype'] . '' . $item['billdate'];
                        if (!isset($output[$key])) {
                            $output[$key] = $item;
                        } else {
                            $output[$key]['amt'] = number_format((float)$output[$key]['amt'] + (float)$item['amt'], 2, '.', '');
                        }
                    }
                    $output = array_values($output);           
        }else{
                preg_match("/='(.*?)'/", $fitterremovedataall, $matches);
                $ilocation = $matches[1];
                $locations = $this->cityArray();               
                $location = array_search($ilocation , $locations);
                $zone_locatin_id =TblLocationModel::select('name','zone_id')->where('name',$ilocation)->first();
                if(!empty($zone_locatin_id)){
                $zones_iid = TblZonesModel::select('id','name')->where('id',$zone_locatin_id->zone_id)->first();
                $income_data = $this->postCurlApi($url, $dte, $location, $max_retries,$apistatus,"");                     
                        if (!empty($income_data['billinglist'])) {
                            foreach ($income_data['billinglist'] as $billing) {
                                $inc_Data[] = $this->saveCurlData($billing, $location,$apistatus,$zone_locatin_id->name,$zones_iid->name);  
                            }
                       } 
                    //    echo "<pre>";print_r($inc_Data);exit;
                
                foreach ($inc_Data as $item) {
                    $key = $item['type'] . '' . $item['paymenttype'] . '' . $item['billdate'];
                    if (!isset($output[$key])) {
                        $output[$key] = $item;
                    } else {
                        $output[$key]['amt'] = number_format((float)$output[$key]['amt'] + (float)$item['amt'], 2, '.', '');
                    }
                }
                $output = array_values($output); 
            }    
        }
    }
    echo "<pre>5555";print_r($output);  exit; 
    return $output;
}

private function incomeFinalArray($input1){
            $output1 = [];
            $final = [];
            foreach ($input1 as $entry) {
                $key = $entry['type'] . '|' . $entry['billdate'];
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
                    $output1[$key][$paymentKey] += $entry['amt'];
                } else {
                    $output1[$key][$paymentKey] = $entry['amt'];
                }
                $output1[$key]['total_amt'] += $entry['amt'];
            }
            $final = array_values($output1);
                // echo "<pre>31111";print_r($final);exit;
            return $final;
}

public function incomeDateFilter(Request $request){
    set_time_limit(0); 
    $fitterremovedataall = $request->input('morefilltersall'); 
    if(empty($fitterremovedataall)){
        $fitterremovedataall = "tblzones.name='TN CHENNAI'";
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

private function incomeBranchDateFiltr($fitterremovedataall,$moredatefittervale,$apistatus,$statusid){
    $incomeArray = [];
    $finalOutput = [];
    $indates = [];
    $dates = explode(' - ', $moredatefittervale);
    $startDate = $dates[0] ?? '';  
    $endDate = $dates[1] ?? '';
    $start_date = Carbon::createFromFormat('d/m/Y', $startDate);
    $end_date = Carbon::createFromFormat('d/m/Y', $endDate);
    $url = 'https://mocdoc.in/api/get/billlist/draravinds-ivf'; 
  
        while ($start_date <= $end_date) {
            $indates[] = $start_date->format('Ymd');
            $start_date->addDay();
        }
       
    $incomeArray = $this->incomereportAPI($moredatefittervale, $fitterremovedataall,$apistatus,$statusid,$url,$indates);  
    echo "<pre>111111";print_r($incomeArray);exit;  
    $finalOutput = $this->incomeFinalArray($incomeArray);
    
    preg_match("/'([^']+)'/", $fitterremovedataall, $matches);    
    $state = $matches[1]; 
    if(empty($finalOutput)){
        if (strpos($fitterremovedataall, "tblzones.name=") !== false){
             $zone_locations = $this->selectQuery($state);
        }else{
            $zone_locations = TblLocationModel::orderBy('name', 'asc')->get();
        }        
        return response()->json(['data' => $finalOutput,'dropdown' => $zone_locations]);
    }
     
    $totalCash = 0;
    $totalUpi = 0;
    $totalCard = 0;
    $totalNeft = 0;
    $totalTotal = 0;
    if (strpos($fitterremovedataall, "tblzones.name=") !== false && strpos($fitterremovedataall, "tbl_locations.name=") !== false) {      
        preg_match("/(tbl_income\.category)\s*=\s*'([^']+)'/", $fitterremovedataall, $inc_category);
        // echo "<pre>";print_r($inc_category);exit;
        $finalOutput = $this->incomeCategory($inc_category,$finalOutput); 
       
            foreach ($finalOutput as $key=>$item) {
                $totalCash += isset($item['cash_amt']) ? $item['cash_amt'] : 0;
                $totalUpi += isset($item['upi_amt']) ? $item['upi_amt'] : 0;
                $totalCard += isset($item['card_amt']) ? $item['card_amt'] : 0;
                $totalNeft += isset($item['neft_amt']) ? $item['neft_amt'] : 0;
                $totalTotal += isset($item['total_amt']) ? $item['total_amt'] : 0;
                $finalOutput[$key]['zone_type'] = 2;
            }
            $finalOutput[0]['total_cash_amt'] = $totalCash;
            $finalOutput[0]['total_upi_amt'] = $totalUpi;
            $finalOutput[0]['total_card_amt'] = $totalCard;
            $finalOutput[0]['total_neft_amt'] = $totalNeft;
            $finalOutput[0]['total_total_amt'] = $totalTotal;

            // if (isset($inc_category[2]) && $inc_category[2] == 'Consolidated') {
                $newArray = [
                    "type" => "Consolidated",
                    "billdate" => $finalOutput[0]['billdate'] ?? '',
                    "area" => $finalOutput[0]['area'] ?? '',
                    "zone_name" => $finalOutput[0]['zone_name'] ?? '',
                    "total_cash_amt" => $totalCash ?? 0,
                    "total_upi_amt" => $totalUpi ?? 0,
                    "total_card_amt" => $totalCard ?? 0,
                    "total_neft_amt" => $totalNeft ?? 0,
                    "total_total_amt" => $totalTotal ?? 0,
                    "zone_type" => $finalOutput[0]['zone_type'] ?? '',
                ];                    
                        array_push($finalOutput, $newArray);
                // }
                       
            $zone_locations = $this->selectQuery($state);
            return response()->json(['data' => $finalOutput,'dropdown' => $zone_locations]);
    } else {  
            preg_match("/tblzones\.name\s*=\s*'[^']*'/", $fitterremovedataall, $matches);
        if (!empty($matches)) {               
            preg_match("/(tbl_income\.category)\s*=\s*'([^']+)'/", $fitterremovedataall, $inc_category);  
            if (isset($inc_category[2]) && $inc_category[2] != 'All' && $inc_category[2] != 'Consolidated') {
              $finalOutput = $this->incomeCategory($inc_category,$finalOutput);
            }
            foreach ($finalOutput as $key =>$item) {
                $totalCash += isset($item['cash_amt']) ? $item['cash_amt'] : 0;
                $totalUpi += isset($item['upi_amt']) ? $item['upi_amt'] : 0;
                $totalCard += isset($item['card_amt']) ? $item['card_amt'] : 0;
                $totalNeft += isset($item['neft_amt']) ? $item['neft_amt'] : 0;
                $totalTotal += isset($item['total_amt']) ? $item['total_amt'] : 0;
                $finalOutput[$key]['zone_type'] = 1;
            }
            $finalOutput[0]['total_cash_amt'] = $totalCash;
            $finalOutput[0]['total_upi_amt'] = $totalUpi;
            $finalOutput[0]['total_card_amt'] = $totalCard;
            $finalOutput[0]['total_neft_amt'] = $totalNeft;
            $finalOutput[0]['total_total_amt'] = $totalTotal; 
            // if (isset($inc_category[2]) && $inc_category[2] == 'Consolidated') {
                $newArray = [
                    "type" => "Consolidated",
                    "billdate" => $finalOutput[0]['billdate'] ?? '',
                    "area" => $finalOutput[0]['area'] ?? '',
                    "zone_name" => $finalOutput[0]['zone_name'] ?? '',
                    "total_cash_amt" => $totalCash ?? 0,
                    "total_upi_amt" => $totalUpi ?? 0,
                    "total_card_amt" => $totalCard ?? 0,
                    "total_neft_amt" => $totalNeft ?? 0,
                    "total_total_amt" => $totalTotal ?? 0,
                    "zone_type" => $finalOutput[0]['zone_type'] ?? '',
                ];                
                array_push($finalOutput, $newArray);
        // }
            $zone_locations = $this->selectQuery($state);
            return response()->json(['data' => $finalOutput,'dropdown' => $zone_locations]);
        }else{
            preg_match("/='(.*?)'/", $fitterremovedataall, $matches);
            $ilocation = $matches[1];
            if (strpos($fitterremovedataall, "tblzones.name=") !== false){
                $zone_locaton =TblLocationModel::select('zone_id')->where('name',$ilocation)->first();
                $zone_location =TblLocationModel::where('zone_id',$zone_locaton->zone_id)->get();
           }else{
               $zone_location = TblLocationModel::orderBy('name', 'asc')->get();
           } 
           preg_match("/(tbl_income\.category)\s*=\s*'([^']+)'/", $fitterremovedataall, $inc_category);
           $finalOutput = $this->incomeCategory($inc_category,$finalOutput);
           
            foreach ($finalOutput as $key =>$item) {
                $totalCash += isset($item['cash_amt']) ? $item['cash_amt'] : 0;
                $totalUpi += isset($item['upi_amt']) ? $item['upi_amt'] : 0;
                $totalCard += isset($item['card_amt']) ? $item['card_amt'] : 0;
                $totalNeft += isset($item['neft_amt']) ? $item['neft_amt'] : 0;
                $totalTotal += isset($item['total_amt']) ? $item['total_amt'] : 0;
                $finalOutput[$key]['zone_type'] = 2;
            }
            $finalOutput[0]['total_cash_amt'] = $totalCash;
            $finalOutput[0]['total_upi_amt'] = $totalUpi;
            $finalOutput[0]['total_card_amt'] = $totalCard;
            $finalOutput[0]['total_neft_amt'] = $totalNeft;
            $finalOutput[0]['total_total_amt'] = $totalTotal;

            // if (isset($inc_category[2]) && $inc_category[2] == 'Consolidated') {
                $newArray = [
                    "type" => "Consolidated",
                    "billdate" => $finalOutput[0]['billdate'] ?? '',
                    "area" => $finalOutput[0]['area'] ?? '',
                    "zone_name" => $finalOutput[0]['zone_name'] ?? '',
                    "total_cash_amt" => $totalCash ?? 0,
                    "total_upi_amt" => $totalUpi ?? 0,
                    "total_card_amt" => $totalCard ?? 0,
                    "total_neft_amt" => $totalNeft ?? 0,
                    "total_total_amt" => $totalTotal ?? 0,
                    "zone_type" => $finalOutput[0]['zone_type'] ?? '',
                ];                    
                array_push($finalOutput, $newArray);
        // }
           
            return response()->json(['data' => $finalOutput,'dropdown' => $zone_location]);
        }
    }
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
        }else{
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

    private function postCurlApi($url, $curr_date, $location_id, $max_retries,$apistatus,$end_date)
    {
        if($apistatus == 'checkinreport'){
                $post_fields = "startdate={$curr_date}&enddate={$end_date}&entitylocation={$location_id}";
                $head_fields = [
                                'md-authorization: MD 7b40af0edaf0ad75:jR1+YyQZVWCIIaXlgxt1z8uixQ4=',
                                'Date: Mon, 31 Mar 2025 08:05:38 GMT',
                                'Content-Type: application/x-www-form-urlencoded'
                            ];        
        }elseif($apistatus == 'registrationreport'){
                $dte = substr($curr_date, 0, 8);       
                $post_fields = "registrationdate={$dte}";
                // echo $post_fields;exit;
                $head_fields = [
                                'md-authorization: MD 7b40af0edaf0ad75:zzJIrJPzgSOMhucj/1bXawbz+GI=',
                                'Date: Fri, 11 Apr 2025 06:18:59 GMT',
                                'Content-Type: application/x-www-form-urlencoded',
                                'Cookie: SRV=s1; vid3=CvAABmf4wWdOP+VJBV+AAg=='
                            ];
    }else{
                $post_fields = "date={$curr_date}&entitylocation={$location_id}";
                $head_fields = [
                        'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
                        'Date: Fri, 07 Mar 2025 10:07:52 GMT',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Cookie: SRV=s1'
                    ];
            }

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
            'AFTPR' => 'Arani',
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

    private function cityMapping(){
        $cityMapping = [
            'Chennai - Urapakkam' => 'Chennai',
            'Chennai - Sholinganallur' => 'Chennai',
            'Chennai - Madipakkam' => 'Chennai',
            'Chennai - Vadapalani' => 'Chennai',
            'Chennai - Tambaram' => 'Chennai',
            'Corporate Office - Guindy' => 'Chennai',
            'Kanchipuram' => 'Kanchipuram',
            'Thiruvallur' => 'Thiruvallur',
            'Chengalpattu' => 'Chengalpattu',
            'Coimbatore - Sundarapuram' => 'Coimbatore',
            'Coimbatore - Thudiyalur' => 'Coimbatore',
            'Coimbatore - Ganapathy' => 'Coimbatore',
            'Kerala - Kozhikode' => 'kozhikode',
            'Varadhambalayam' => 'Erode',
            'Bengaluru - Electronic City' => 'bangalore',
            'Bengaluru - Hebbal' => 'bangalore',
            'Bengaluru - Konanakunte' => 'bangalore',
            'KARNATAKA' => 'bangalore',
            'Bengaluru - Dasarahalli' => 'bangalore',
            'Trichy' => 'Tiruchirappalli',
            'Tanjore' => 'Thanjavur',
            'Kerala - Palakkad' => 'Palakkad',
            'Aathur' => 'Attur',
            'Thirupathur' => 'Thirupattur',
            'Hosur' => 'Hosur',
            'Salem' => 'Salem',
            'Harur' => 'Harur',
            'Kallakurichi' => 'Kallakurichi',
            'Thiruvannamalai' => 'Thiruvannamalai',
            'Namakal' => 'NAMAKKAL',
            'Pennagaram' => 'Pennagaram',
            'Tirupati' => 'Tirupati',
            'Vellore' => 'Vellore',
            'Thirupathur' => 'Thirupathur',
            'Madurai' => 'Madurai',
            'Villupuram' => 'Villupuram',
            'Nagapattinam' => 'Nagapattinam',
            'Sivakasi' => 'Sivakasi',
        ];

        return  $cityMapping;
    }

    private function selectQuery($city){
                $zone_id = TblZonesModel::select('id')->where('name',$city)->first();                 
                $zone_locations = TblLocationModel::where('zone_id',$zone_id->id)->where('status',1)->orderBy('name', 'asc')->get();
                return $zone_locations;
    }
    
    private function selectZoneDoctor($city){
                $zone_id = TblZonesModel::select('id')->where('name',$city)->first();          
                $doctor_name = CheckinModel::select('doctor_name')->where('zone_id',$zone_id->id)->distinct()->orderBy('doctor_name', 'asc')->get();
                return $doctor_name;
    }

    public function dailyDateFilter(Request $request){
        set_time_limit(0); 
        $datefiltervalue = $request->input('datefiltervalue');
        $fitterremovedataall = $request->input('morefilltersall');
        $dailySummary = $this->ddailySummaryAPI($datefiltervalue, $fitterremovedataall,$request->apistatus,2);
        // echo "<pre>";print_r($dailySummary);exit;
        // $val = explode('=', $fitterremovedataall);  
        // $city = !empty(trim($val[0], "'")) ? trim($val[1], "'") : 'Kerala - Palakkad'; 
        $val = explode('=', $fitterremovedataall);                 
        if (strpos($fitterremovedataall, 'tbl_branch.zone') !== false && strpos($fitterremovedataall, 'tbl_branch.name') !== false) {
            $city = trim($val[2], "'");
        }elseif (strpos($fitterremovedataall, 'tbl_branch.zone') !== false && strpos($fitterremovedataall, 'tbl_branch.name') === false) {   
            preg_match("/'([^']+)'/", $fitterremovedataall, $matches);
            $city = $matches[1];
            $status_id = 1;
        }else{             
            $city = !empty(trim($val[0], "'")) ? trim($val[1], "'") : ''; 
        } 
        // echo $city;exit;
        $finalResult = [];
        if($request->apistatus == 'checkinreport'){  
            if( isset($status_id) == 1){               
                usort($dailySummary, function($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });
                               
                $zone_locations = $this->selectQuery($city);
                // echo "<pre>3333333";print_r($zone_locations);exit;
                $cityMapping =   $this->cityMapping();
                $allowedCities = [];
                    foreach ($zone_locations as $city) {
                            $allowedCities[] = $city->name;                        
                    }
                  
                    $allow_Cities = [];
                    foreach ($allowedCities as $loc) {
                        if (isset($cityMapping[$loc])) {
                            $allow_Cities[] = $cityMapping[$loc];
                        } else {
                            $allow_Cities[] = $loc; 
                        }
                    }
                    $filtered_dt = array_filter($dailySummary, function ($entry) use ($allow_Cities) { 
                        $cityName = strtok($entry['city'], '-'); 
                        $normalizedCityName = strtolower(trim($cityName));
                        return in_array($normalizedCityName, array_map('strtolower', $allow_Cities));
                    });
                    
                $filtered_dt = array_values($filtered_dt);
                // echo "<pre>379";print_r($filtered_dt);exit;
                $finalResult = $filtered_dt;
            }else{
                $cityMapping =   $this->cityMapping();
                if (isset($cityMapping[$city])) {
                    $city = $cityMapping[$city];
                }
                if (is_array($dailySummary) && !empty($dailySummary)) {
                        usort($dailySummary, function($a, $b) {
                            return strtotime($a['date']) - strtotime($b['date']);
                        });
                        $filteredArray = array_filter($dailySummary, function ($entry) use ($city) {
                            return stripos($entry['city'], $city) === 0;
                        });            
                        $finalResult = array_values($filteredArray);                   
                    }
            }
        }elseif($request->apistatus == 'regreport'){  
                $flattened = [];
                            foreach ($dailySummary as $regreport) {
                                foreach ($regreport as $entry) {
                                    $flattened[] = $entry;
                                }
                            }
                            // echo "<pre>";print_r($flattened);exit;
                            $areaMapping =   $this->cityCode();

                        foreach ($flattened as &$item) {
                            $prefix = explode('-', $item['phid'])[0];
                        
                            if (isset($areaMapping[$prefix])) {
                                $item['area'] = $areaMapping[$prefix];
                            } else {
                                $item['area'] = 'Unknown'; 
                            }
                        }
                        // echo "<pre>";print_r($city);exit;
                        $area_fip=array_flip($areaMapping);    
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
                        }else{
                            $finalResult =  $flattened;
                        }                        
            }else{
                $totalOpIncome = 0;
                $totalIpIncome = 0;
                $totalPharmacyIncome = 0;
                usort($dailySummary, function($a, $b) {
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

    public function dailyBranchFilter(Request $request){
        set_time_limit(0); 
        $datefiltervalue = $request->input('moredatefittervale');
        $fitterremovedataall = $request->input('morefilltersall');
        $dailySummary = $this->ddailySummaryAPI($datefiltervalue, $fitterremovedataall,$request->apistatus,2);
        //  echo "<pre>11111";print_r($request->apistatus);exit;
        $val = explode('=', $fitterremovedataall);                 
        if (strpos($fitterremovedataall, 'tbl_branch.zone') !== false && strpos($fitterremovedataall, 'tbl_branch.name') !== false) {
                preg_match("/'([^']+)'/", $fitterremovedataall, $matches);
                $city_area = $matches[1];
                $city = trim($val[2], "'");
        }elseif (strpos($fitterremovedataall, 'tbl_branch.zone') !== false && strpos($fitterremovedataall, 'tbl_branch.name') === false) { 
                preg_match("/'([^']+)'/", $fitterremovedataall, $matches);
                $city = $matches[1];
                $city_area = $matches[1];
                $status_id = 1;
        }else{
            $city = trim($val[1], "'");
            if($request->apistatus == ""){
                $z_lc = TblLocationModel::select('zone_id')->where('name',$city)->first();
                $zne_id = TblZonesModel::select('name')->where('id',$z_lc->zone_id)->first(); 
                $city_area = $zne_id->name; 
            }
        }
        // echo $city_area;exit;
        $finalResult = [];
       
        if($request->apistatus == 'checkinreport'){  
            if( isset($status_id) == 1){               
                usort($dailySummary, function($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });
               
                $zone_locations = $this->selectQuery($city);
                $cityMapping =   $this->cityMapping();
                $allowedCities = [];
                    foreach ($zone_locations as $city) {
                            $allowedCities[] = $city->name;                        
                    }
                  
                    $allow_Cities = [];
                    foreach ($allowedCities as $loc) {
                        if (isset($cityMapping[$loc])) {
                            $allow_Cities[] = $cityMapping[$loc];
                        } else {
                            $allow_Cities[] = $loc; 
                        }
                    }
                    $filtered_dt = array_filter($dailySummary, function ($entry) use ($allow_Cities) { 
                        $cityName = strtok($entry['city'], '-'); 
                        $normalizedCityName = strtolower(trim($cityName));
                        return in_array($normalizedCityName, array_map('strtolower', $allow_Cities));
                    });
                    
                $filtered_dt = array_values($filtered_dt);
                $result = empty($filtered_dt) ? [] : $filtered_dt;       
                return response()->json(['data' => $result,'dropdown' => $zone_locations]);
            }else{               
                $cityMapping =   $this->cityMapping();
                if (isset($cityMapping[$city])) {
                    $city = $cityMapping[$city];
                }              
                if (is_array($dailySummary) && !empty($dailySummary)) {
                        usort($dailySummary, function($a, $b) {
                            return strtotime($a['date']) - strtotime($b['date']);
                        });
                        $filteredArray = array_filter($dailySummary, function ($entry) use ($city) {
                            return stripos($entry['city'], $city) === 0;
                        });            
                        $finalResult = array_values($filteredArray);                   
                    }
            if (strpos($fitterremovedataall, 'tbl_branch.zone') !== false && strpos($fitterremovedataall, 'tbl_branch.name') !== false) {
                    $zone_locations = $this->selectQuery($city_area); 
                    $result = empty($finalResult) ? [] : $finalResult;       
                    return response()->json(['data' => $result,'dropdown' => $zone_locations]);
                }
            }
        }elseif($request->apistatus == 'regreport'){  
                        $flattened = [];
                        foreach ($dailySummary as $regreport) {
                            foreach ($regreport as $entry) {
                                $flattened[] = $entry;
                            }
                        }
            $areaMapping =   $this->cityCode();
                    foreach ($flattened as &$item) {
                        $prefix = explode('-', $item['phid'])[0];                    
                        if (isset($areaMapping[$prefix])) {
                            $item['area'] = $areaMapping[$prefix];
                        } else {
                            $item['area'] = 'Unknown'; 
                        }
                    }                 
                    $area_fip=array_flip($areaMapping);                                  
                    $search = $area_fip[$city];
                    // echo "<pre>379";print_r($search);exit;   
                    $results = [];
                    foreach ($flattened as $entry) {
                       $phidPrefix = explode('-', $entry['phid'])[0];                        
                        if ($phidPrefix === $search) {
                            $results[] = $entry;
                        }
                    }
                  
                    $finalResult = $results;
                }else{
                    $totalOpIncome = 0;
                    $totalIpIncome = 0;
                    $totalPharmacyIncome = 0;
                    usort($dailySummary, function($a, $b) {
                        return strtotime($a['billdate']) - strtotime($b['billdate']);
                    });
                    // echo "<pre>";print_r($dailySummary);exit;
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
                    $zone_locations = $this->selectQuery($city_area);
                    $result = empty($finalResult) ? [] : $finalResult;       
                    return response()->json(['data' => $result,'dropdown' => $zone_locations]);
        }
        $result = empty($finalResult) ? [] : $finalResult;       
        return response()->json($result);
    }

public function dailySummaryDetails(Request $request){
    set_time_limit(0); 
    $datefiltervalue = $request->input('moredatefittervale');
    $statusid = $request->input('statusid');
    $dailySummary = [];
    if($statusid==1 && !empty($request->apistatus) ){
        $dailySummary = $this->ddailySummaryAPI($datefiltervalue, $fitterremovedataall=null,$request->apistatus,$statusid);
    }
    // echo "<pre>";print_r($dailySummary);exit;
    $finalResult = [];

        if($request->apistatus == 'checkinreport' && !empty($dailySummary)){
            // $cityMapping =   $this->cityMapping();
            // $city = 'Palakkad';
            // if (isset($cityMapping[$city])) {
            //     $city = $cityMapping[$city];
            // }
            // if (is_array($dailySummary) && !empty($dailySummary)) {
            //         usort($dailySummary, function($a, $b) {
            //             return strtotime($a['date']) - strtotime($b['date']);
            //         });
            //         $filteredArray = array_filter($dailySummary, function ($entry) use ($city) {
            //             return stripos($entry['city'], $city) === 0;
            //         });            
            //         $finalResult = array_values($filteredArray);
            //         // echo "<pre>";print_r($finalResult);exit;
            //     }
        }elseif($request->apistatus == 'regreport'){  
            $flattened = [];
            // echo "<pre>";print_r($dailySummary);exit;
            foreach ($dailySummary as $regreport) {
                foreach ($regreport as $entry) {
                    $flattened[] = $entry;
                }
            }
            $areaMapping =   $this->cityCode();

            foreach ($flattened as &$item) {
                $prefix = explode('-', $item['phid'])[0];            
                if (isset($areaMapping[$prefix])) {
                    $item['area'] = $areaMapping[$prefix];
                } else {
                    $item['area'] = 'Unknown'; 
                }
            }
            $finalResult = $flattened;
         }else{
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
                    'opIncome' => round($entry['O/P - Income'],2),
                    'ipIncome' => round($entry['I/P - Income'],2),
                    'pharmacyIncome' => round($entry['Pharmacy - Income'],2),
                    'totalOpIncome' => round($totalOpIncome,2),
                    'totalIpIncome' => round($totalIpIncome,2),
                    'totalPharmacyIncome' => round($totalPharmacyIncome,2),
                    'total_amt' => round($entry['O/P - Income'] + $entry['I/P - Income'] + $entry['Pharmacy - Income'],2)
                ];
            
                $finalResult[] = $newEntry;
            }
        }
        $result = empty($finalResult) ? [] : $finalResult;       
        return response()->json($result);
}

public function registrationView(Request $request){   
    $phid = explode('-',$request->phid)[0];
    $dailySummary = $this->ddailySummaryAPI($request->cdate, "","regreportview",$phid);
    $dailySummary = $dailySummary[0];
    //  echo "<pre>";print_r($dailySummary);exit;
    foreach ($dailySummary as &$item) {
        $item['city'] = $request->city; 
    }   
    $ph_id = $request->ph_id;
    if($request->status_id == 1 && !empty($ph_id))   { 
        $matched = array_filter($dailySummary, function($item) use ($ph_id) {
            return stripos($item['phid'], $ph_id) !== false;
        });
        $dailySummary = array_values($matched);        
    }
    return response()->json($dailySummary);
}

private function cityArray(){
    $locations = array("location1" => "Kerala - Palakkad", "location7" => "Erode", "location14" => "Tiruppur","location6" => "Kerala - Kozhikode", "location20" => "Coimbatore - Ganapathy", "location21" => "Hosur", "location22" => "Chennai - Sholinganallur","location23" => "Chennai - Urapakkam", "location24" => "Chennai - Madipakkam", "location26" => "Kanchipuram", "location27" => "Coimbatore - Sundarapuram", "location28" => "Trichy","location29" => "Thiruvallur", "location30" => "Pollachi", "location31" => "Bengaluru - Electronic City","location32" => "Bengaluru - Konanakunte", "location33" => "Chennai - Tambaram", "location34" => "Tanjore", "location36" => "Harur", "location39" => "Coimbatore - Thudiyalur", "location40" => "Madurai","location41" => "Bengaluru - Hebbal", "location42" => "Kallakurichi", "location43" => "Vellore","location44" => "Tirupati","location45" => "Aathur", "location46" => "Namakal", "location47" => "Bengaluru - Dasarahalli","location48" => "Chengalpattu", "location49" => "Chennai - Vadapalani", "location50" => "Pennagaram","location51" => "Thirupathur", "location52" => "Sivakasi", "location13" => "Salem");

    return $locations;
}

public function ddailySummaryAPI($date,$dt,$apistatus,$statusid)
{
    if($apistatus == 'checkinreport'){
             $url = 'https://mocdoc.in/api/checkedin/draravinds-ivf'; 
    }
    elseif($apistatus == 'regreport' || $apistatus == 'regreportview'){
        $url = 'https://mocdoc.com/api/get/ptlist/draravinds-ivf'; 
    }
    else{        
            $url = 'https://mocdoc.in/api/get/billlist/draravinds-ivf'; 
    }
    $max_retries = 5; 

   if($apistatus != 'regreportview'){
            $dates = explode(' - ', $date);
            $startDate = $dates[0] ?? '';  // "29/12/2024"
            $endDate = $dates[1] ?? '';
            $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        // $curr_date=$startDateFormatted; 
            $start = Carbon::createFromFormat('Y-m-d', $startDateFormatted);
            $end = Carbon::createFromFormat('Y-m-d', $endDateFormatted);
            //echo $date = Carbon::parse($start);exit;
            $start_date = $start->format('Ymd').'00:00:00';
            $end_date = $end->format('Ymd').'23:59:59';
            $dates = [];
            while ($start <= $end) {
                $dates[] = $start->format('Ymd');
                $start->modify('+1 day');
            }
            $val = explode('=', $dt);                
            $value = !empty(trim($val[0], "'")) ? trim($val[1], "'") : '';        
            $locations = $this->cityArray();               
            $location = array_search($value , $locations);
            // echo "<pre>";print_r($location);exit;  
    }
   $billingData = [];
   $billdate = null;
        if($apistatus == 'checkinreport' && $statusid != 1){
            if (strpos($dt, 'tbl_branch.zone') !== false && strpos($dt, 'tbl_branch.name') === false) { 
                    preg_match_all("/'([^']+)'/", $dt, $matches);
                    $dt_values = $matches[1]; 
                    $zone_location = $this->selectQuery($dt_values[0]); 
                  
                    foreach($zone_location as $zone_loc){
                        $clocation = $this->cityArray(); 
                        $zlocation = array_search($zone_loc->name , $clocation);                       
                        $data = $this->getBillingData($url, $start_date, $zlocation, $max_retries,$apistatus,$end_date);
                       
                        if (!empty($data['checkinlist'])) {
                            foreach ($data['checkinlist'] as $billing) {              
                                $billingData[] = $this->prepareBillingData($billing, $zlocation,$apistatus); 
                                //   echo "<pre>3333333333333";print_r($billingData);  
                            }                       
                        }
                    }  // exit;
                    return $billingData;
                    // echo "<pre>3333333333333";print_r($billingData);                exit;
              }elseif (strpos($dt, 'tbl_branch.zone') !== false && strpos($dt, 'tbl_branch.name') !== false) {
                        $value = trim($val[2], "'");
                        preg_match_all("/'([^']+)'/", $dt, $matches);
                        $dt_values = $matches[1];                                                        
                        $zones_id = TblZonesModel::select('id')->where('name',$dt_values[0])->get();                                               
                        $zone_location_id =TblLocationModel::select('zone_id')->where('name',$dt_values[1])->get();                                
                                        
                            if($zones_id[0]->id == $zone_location_id[0]->zone_id){
                                $clocaton = $this->cityArray(); 
                                $zlocaton = array_search($dt_values[1] , $clocaton);
                                $data = $this->getBillingData($url, $start_date, $zlocaton, $max_retries,$apistatus,$end_date); 
                                                                                             
                                if (!empty($data['checkinlist'])) {
                                    foreach ($data['checkinlist'] as $billing) {
                                        $billingData[] = $this->prepareBillingData($billing, $zlocaton,$apistatus);  
                                    }
                                    // exit;
                                }
                            }else{
                                // $dailySummary = [];
                                // $dailySummary['status'] = "false";
                                // return $dailySummary;
                            } 
                            // echo "<pre>";print_r($billingData); exit;
                            return $billingData;
              }
              else{
                $data = $this->getBillingData($url, $start_date, $location, $max_retries,$apistatus,$end_date);
                
                if (!empty($data['checkinlist'])) {
                    foreach ($data['checkinlist'] as $billing) {               
                        $billingData[] = $this->prepareBillingData($billing, $location,$apistatus);  
                    }
                    return $billingData;
                    // echo "<pre>";print_r($billingData);                exit;
                }
        }
        }else if($apistatus == 'checkinreport' && $statusid == 1){
                $data = $this->getBillingData($url, $start_date, 'location1', $max_retries,$apistatus,$end_date);
                // echo "<pre>";print_r($data);                exit;
                    if (!empty($data['checkinlist'])) {
                        foreach ($data['checkinlist'] as $billing) {
                            $billingData[] = $this->prepareBillingData($billing, $location,$apistatus);  
                        }
                    }
            return $billingData;
        }else if($apistatus == 'regreportview'){           
                $data = $this->getBillingData($url, $date, "",$max_retries,$apistatus,"");
                if (!empty($data['ptlist'])) {                          
                            $billingData[] = $this->prepareBillingData($data['ptlist'], $statusid ,$apistatus);                              
                    }         
            return $billingData;
        }else if($apistatus == 'regreport'){
            foreach ($dates as $dat) {
                $data = $this->getBillingData($url, $dat, $location, $max_retries,$apistatus,$end_date);
                if (!empty($data['ptlist'])) {                          
                            $billingData[] = $this->prepareBillingData($data['ptlist'], $location,$apistatus);                              
                    }                       
                }                
                // echo "<pre>";print_r($billingData);                exit;    
            return $billingData;
        }else{
                foreach ($dates as $dat) {
                    if (strpos($dt, 'tbl_branch.zone') !== false && strpos($dt, 'tbl_branch.name') === false) {     
                                preg_match_all("/'([^']+)'/", $dt, $matches);
                                $dt_values = $matches[1];  
                                $zone_location = $this->selectQuery($dt_values[0]); 
                                foreach($zone_location as $zone_loc){
                                    $clocation = $this->cityArray(); 
                                    $zlocation = array_search($zone_loc->name , $clocation);
                                    $data = $this->getBillingData($url, $dat, $zlocation, $max_retries,$apistatus,"");                             
                                    if (!empty($data['billinglist'])) {
                                        foreach ($data['billinglist'] as $billing) {
                                            $billingData[] = $this->prepareBillingData($billing, $location,$apistatus);  
                                        }
                                    }
                                }                                 
                                        // echo "<pre>";print_r($billingData);echo "<br>";                             
                            }elseif (strpos($dt, 'tbl_branch.zone') !== false && strpos($dt, 'tbl_branch.name') !== false) {
                                $value = trim($val[2], "'");
                                preg_match_all("/'([^']+)'/", $dt, $matches);
                                $dt_values = $matches[1];                                                         
                                $zones_id = TblZonesModel::select('id')->where('name',$dt_values[0])->get();                                               
                                $zone_location_id = TblLocationModel::select('zone_id')->where('name',$dt_values[1])->get();                                
                                                
                                    if($zones_id[0]->id == $zone_location_id[0]->zone_id){
                                        $clocaton = $this->cityArray(); 
                                        $zlocaton = array_search($dt_values[1] , $clocaton);
                                        $data = $this->getBillingData($url, $dat,$zlocaton , $max_retries,$apistatus,""); 
                                                                     
                                        if (!empty($data['billinglist'])) {
                                            foreach ($data['billinglist'] as $billing) {
                                                $billingData[] = $this->prepareBillingData($billing, $dt_values[1],$apistatus);  
                                            }
                                            // exit;
                                        }
                                    }else{
                                        // $dailySummary = [];
                                        // $dailySummary['status'] = "false";
                                        // return $dailySummary;
                                    }  
                            }else{
                                $data = $this->getBillingData($url, $dat, $location, $max_retries,$apistatus,"");                     
                                if (!empty($data['billinglist'])) {
                                    foreach ($data['billinglist'] as $billing) {
                                        $billingData[] = $this->prepareBillingData($billing, $location,$apistatus);  
                                    }
                            }
                        }
                }
                // exit;
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
   
}

// Function to handle cURL and retries
private function getBillingData($url, $curr_date, $location_id, $max_retries,$apistatus,$end_date)
{
    $retry_count = 0;
    $delay = 2;
    //echo $curr_date->format('Y-m-d H:i:s');exit;
    if($apistatus == 'checkinreport'){
        $post_fields = "startdate={$curr_date}&enddate={$end_date}&entitylocation={$location_id}";
        $head_fields = [
                        'md-authorization: MD 7b40af0edaf0ad75:jR1+YyQZVWCIIaXlgxt1z8uixQ4=',
                        'Date: Mon, 31 Mar 2025 08:05:38 GMT',
                        'Content-Type: application/x-www-form-urlencoded'
                      ];
    }elseif($apistatus == 'regreport' || $apistatus =='regreportview'){
        $dte = substr($curr_date, 0, 8);       
        $post_fields = "registrationdate={$dte}";
        // echo $post_fields;exit;
        $head_fields = [
                        'md-authorization: MD 7b40af0edaf0ad75:zzJIrJPzgSOMhucj/1bXawbz+GI=',
                        'Date: Fri, 11 Apr 2025 06:18:59 GMT',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Cookie: SRV=s1; vid3=CvAABmf4wWdOP+VJBV+AAg=='
                     ];
    }else{
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
            CURLOPT_HTTPHEADER =>$head_fields,
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

private function prepareBillingData($billing, $location_id,$apistatus)
{
    if($apistatus == 'checkinreport'){
        $cdate = !empty($billing['date'])
            ? Carbon::createFromFormat('Ymd', $billing['date'])->format('Y-m-d')
            : null;
        $dob = !empty($billing['patient']['dob'])
                ? Carbon::createFromFormat('Ymd', $billing['patient']['dob'])->format('Y-m-d')
                : null;
            return [
                'date' => $cdate." ".$billing['start'],
                'purpose' => $billing['purpose'] ?? '',
                'name' => $billing['patient']['name'] ?? '',
                'mobile' => $billing['patient']['mobile'] ?? '',
                'dob' => $dob,
                'ptsource' => $billing['patient']['ptsource'] ?? '',
                'city' => $billing['patient']['address']['city'] ?? '',
            ];
    }elseif($apistatus == 'regreport'){
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
        
        }elseif($apistatus == 'regreportview'){  
                 $afomrItems = [];                          
                foreach ($billing as $item) {
                    if (isset($item['phid']) && strpos($item['phid'], $location_id) === 0) {
                        $afomrItems[] = $item;
                    }
                }     
                return $afomrItems;
                // echo "<pre>";print_r($afomrItems);exit;        
        }else{
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

//     public function dailySummaryAPI()
// {
//     $url = 'https://mocdoc.in/api/get/billlist/draravinds-ivf'; // Hardcoded URL
//     $curr_date = '20250325';
//     $locations = TblBranch::select('location_id')->get(); // Fetch locations dynamically

//     $max_retries = 5;
//     set_time_limit(0); 

//     // Loop through each location
//     foreach ($locations as $location) {
//         $data = $this->getBillingData($url, $curr_date, $location->location_id, $max_retries);
//         //echo "<pre>";print_r($data);exit;
//         if (!empty($data['billinglist'])) {
//             foreach ($data['billinglist'] as $billing) {
//                 $billingData = $this->prepareBillingData($billing, $location->location_id);
//                 DailySummary::insert($billingData);
//             }
//         }
//     }

//     return response()->json(['message' => 'Daily summary data successfully updated']);
// }

// Function to handle cURL and retries
// private function getBillingData($url, $curr_date, $location_id, $max_retries)
// {
//     $retry_count = 0;
//     $delay = 2;

//     while ($retry_count < $max_retries) {
//         $curl = curl_init();
//         curl_setopt_array($curl, [
//             CURLOPT_URL => $url,
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_ENCODING => '',
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 0,
//             CURLOPT_FOLLOWLOCATION => true,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => 'POST',
//             CURLOPT_POSTFIELDS => "date={$curr_date}&entitylocation={$location_id}",
//             CURLOPT_HTTPHEADER => [
//                 'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
//                 'Date: Fri, 07 Mar 2025 10:07:52 GMT',
//                 'Content-Type: application/x-www-form-urlencoded',
//                 'Cookie: SRV=s1'
//             ]
//         ]);

//         $response = curl_exec($curl);
//         $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

//         if (curl_errno($curl)) {
//             echo "cURL Error: " . curl_error($curl);
//             break;
//         }

//         if ($http_status == 200) {
//             curl_close($curl);
//             return json_decode($response, true);
//         }

//         if ($http_status == 429) {
//             sleep($delay);
//             $retry_count++;
//             $delay *= 2;
//         } else {
//             curl_close($curl);
//             break;
//         }
//     }

//     return []; // Return empty data if failed to fetch
// }

// Function to prepare billing data
// private function prepareBillingData($billing, $location_id)
// {
//     // Parse billdate
//     $billdates = !empty($billing['billdate'])
//         ? Carbon::createFromFormat('YmdH:i:s', $billing['billdate'])->format('Y-m-d H:i:s')
//         : null;

//     // Parse dob
//     $dob = !empty($billing['dob']) && preg_match('/^\d{8}$/', $billing['dob'])
//         ? Carbon::createFromFormat('Ymd', $billing['dob'])->toDateString()
//         : null;

//     // Handle I/P Income and advances
//     if ($billing['type'] === 'I/P - Income' && $billing['amt'] == 0) {
//         if (!empty($billing['advances']) && !empty($billing['advances'][0]['amt'])) {
//             $billing['advances_amt'] = $billing['advances'][0]['amt'];
//             $billing['receiptno'] = $billing['advances'][0]['receiptno'];
//             $billing['receivedby'] = $billing['advances'][0]['receivedby'];
//             $billing['receivedbyid'] = $billing['advances'][0]['receivedbyid'];
//         }
//     }

//     // Prepare data array for insertion
//     return [
//         'type' => $billing['type'] ?? '',
//         'branch' => $location_id,
//         'paymenttype' => $billing['paymenttype'] ?? '',
//         'amt' => $billing['amt'] ?? 0,
//         'billno' => $billing['billno'] ?? '',
//         'billdate' => $billdates,
//         'user' => $billing['user'] ?? '',
//         'userid' => $billing['userid'] ?? '',
//         'phid' => $billing['phid'] ?? '',
//         'gender' => $billing['gender'] ?? '',
//         'age' => $billing['age'] ?? '',
//         'mobile' => $billing['mobile'] ?? '',
//         'ptsource' => $billing['ptsource'] ?? '',
//         'isdcode' => $billing['isdcode'] ?? '',
//         'dob' => $dob,
//         'email' => $billing['email'] ?? '',
//         'patientname' => $billing['patientname'] ?? '',
//         'grandprodvalue' => $billing['grandprodvalue'] ?? 0,
//         'grandtax' => $billing['grandtax'] ?? 0,
//         'granddiscountvalue' => $billing['granddiscountvalue'] ?? 0,
//         'discountamt' => $billing['discountamt'] ?? 0,
//         'grandtotal' => $billing['grandtotal'] ?? 0,
//         'consultant' => $billing['consultant'] ?? '',
//         'consultantkey' => $billing['consultantkey'] ?? '',
//         'billtype' => $billing['billtype'] ?? '',
//         'patientkey' => $billing['patientkey'] ?? '',
//         'billkey' => $billing['billkey'] ?? '',
//         'opno' => $billing['opno'] ?? '',
//         'advances_amt' => $billing['advances_amt'] ?? 0,
//         'receiptno' => $billing['receiptno'] ?? '',
//         'receivedby' => $billing['receivedby'] ?? '',
//         'receivedbyid' => $billing['receivedbyid'] ?? '',
//         'ipno' => $billing['ipno'] ?? '',
//     ];
// }


    // public function dailySummaryAPI()
    // {
    //     $url = 'https://mocdoc.in/api/get/billlist/draravinds-ivf'; // Hardcoded URL
    //     $curr_date = '20250315';
    //     //$locations = ['location23', 'location23', 'location28']; // Hardcoded location
    //     $locations = TblBranch::select('location_id')->get(); // Hardcoded location
   
    //     $max_retries = 5;
    //     set_time_limit(0);
    //     foreach ($locations as $location) {
    //         echo "<pre>"; print_r("Location: $location->location_id"); // Debugging each location
       
    //         $retry_count = 0;
    //         $delay = 2; 
        
    //         while ($retry_count < $max_retries) {
    //             // Initialize cURL
    //             $curl = curl_init();        
    //             // Set cURL options
    //             curl_setopt_array($curl, array(
    //                 CURLOPT_URL => $url,
    //                 CURLOPT_RETURNTRANSFER => true,
    //                 CURLOPT_ENCODING => '',
    //                 CURLOPT_MAXREDIRS => 10,
    //                 CURLOPT_TIMEOUT => 0,
    //                 CURLOPT_FOLLOWLOCATION => true,
    //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //                 CURLOPT_CUSTOMREQUEST => 'POST',
    //                 CURLOPT_POSTFIELDS => 'date=' . $curr_date . '&entitylocation=' . $location->location_id,
    //                 CURLOPT_HTTPHEADER => array(
    //                     'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
    //                     'Date: Fri, 07 Mar 2025 10:07:52 GMT',
    //                     'Content-Type: application/x-www-form-urlencoded',
    //                     'Cookie: SRV=s1'
    //                 ),
    //             ));
        
    //             $response = curl_exec($curl);
    //             $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
    //             if (curl_errno($curl)) {
    //                 echo "cURL Error: " . curl_error($curl);
    //                 break;
    //             }
        
    //             if ($http_status == 200) {
    //                 curl_close($curl); 
    //                 $data = json_decode($response, true); 
    //                 break; 
    //             }        
                
    //             if ($http_status == 429) {
    //                 sleep($delay); 
    //                 $retry_count++; 
    //                 $delay *= 2; 
    //             } else {
    //                 curl_close($curl);
    //                 break; 
    //             }
    //         }  
    //     if (!empty($data['billinglist'])) {          
    //         echo "<pre>"; print_r($data['billinglist']);
    //         foreach ($data['billinglist'] as $billing) {
    //             // Handle I/P Income and advances
    //             if ($billing['type'] === 'I/P - Income' && $billing['amt'] == 0) {
    //                 if (!empty($billing['advances']) && !empty($billing['advances'][0]['amt'])) {
    //                     $billing['advances_amt'] = $billing['advances'][0]['amt'];
    //                     $billing['receiptno'] = $billing['advances'][0]['receiptno'];
    //                     $billing['receivedby'] = $billing['advances'][0]['receivedby'];
    //                     $billing['receivedbyid'] = $billing['advances'][0]['receivedbyid'];
    //                 }
    //             }

    //             // Parse billdate
    //             $billdates = !empty($billing['billdate']) 
    //                 ? Carbon::createFromFormat('YmdH:i:s', $billing['billdate'])->format('Y-m-d H:i:s') 
    //                 : null;

    //             // Parse dob
    //             $dob = !empty($billing['dob']) && preg_match('/^\d{8}$/', $billing['dob'])
    //                 ? Carbon::createFromFormat('Ymd', $billing['dob'])->toDateString()
    //                 : null;

    //             // Prepare the data for insertion
    //             $billingData = [
    //                 'type' => $billing['type'] ?? '',
    //                 'branch' => $location->location_id ?? '',
    //                 'paymenttype' => $billing['paymenttype'] ?? '',
    //                 'amt' => $billing['amt'] ?? 0,
    //                 'billno' => $billing['billno'] ?? '',
    //                 'billdate' => $billdates,
    //                 'user' => $billing['user'] ?? '',
    //                 'userid' => $billing['userid'] ?? '',
    //                 'phid' => $billing['phid'] ?? '',
    //                 'gender' => $billing['gender'] ?? '',
    //                 'age' => $billing['age'] ?? '',
    //                 'mobile' => $billing['mobile'] ?? '',
    //                 'ptsource' => $billing['ptsource'] ?? '',
    //                 'isdcode' => $billing['isdcode'] ?? '',
    //                 'dob' => $dob,
    //                 'email' => $billing['email'] ?? '',
    //                 'patientname' => $billing['patientname'] ?? '',
    //                 'grandprodvalue' => $billing['grandprodvalue'] ?? 0,
    //                 'grandtax' => $billing['grandtax'] ?? 0,
    //                 'granddiscountvalue' => $billing['granddiscountvalue'] ?? 0,
    //                 'discountamt' => $billing['discountamt'] ?? 0,
    //                 'grandtotal' => $billing['grandtotal'] ?? 0,
    //                 'consultant' => $billing['consultant'] ?? '',
    //                 'consultantkey' => $billing['consultantkey'] ?? '',
    //                 'billtype' => $billing['billtype'] ?? '',
    //                 'patientkey' => $billing['patientkey'] ?? '',
    //                 'billkey' => $billing['billkey'] ?? '',
    //                 'opno' => $billing['opno'] ?? '',
    //                 'advances_amt' => $billing['advances_amt'] ?? 0,
    //                 'receiptno' => $billing['receiptno'] ?? '',
    //                 'receivedby' => $billing['receivedby'] ?? '',
    //                 'receivedbyid' => $billing['receivedbyid'] ?? '',
    //                 'ipno' => $billing['ipno'] ?? '',
    //             ];
    //             DailySummary::insert($billingData);
    //         }            
    //     }
    // } 
    //     return response()->json(['message' => 'Daily summary data successfully updated']);        
    // }
	
    //keerthi
    public function securityDailyDocument(){
        $admin = auth()->user();
        return view('superadmin.securitydaily_document',['admin' =>$admin]);
    }

    private function saveCurlDatas($checkin)
    { 
       
          
            return [
                    'name' => $checkin['patient']['name'] ?? '',
                    'mobile' => $checkin['patient']['mobile'] ?? '',
                    'phid' => $checkin['patient']['phid'] ?? '',
                    'consultingdr_name' => $checkin['consultingdr_name'] ?? '',
                    'entitylocation' => $checkin['entitylocation'] ?? '',
                ];

	}

    public function mrdnoapiurl(Request $request){
    set_time_limit(0);
        $url = 'https://mocdoc.in/api/checkedin/draravinds-ivf'; 
        $max_retries = 5; // limit to prevent infinite loop
        $locations = $this->cityArray();
        $end_date = date('Ymd').'23:59:59';
        $start_date = date('Ymd', strtotime('-10 days')).'00:00:00';
        // $start_date = date('Ymd', strtotime('-1 day')).'00:00:00';
        $checkinDatas = [];
        foreach ($locations as $locationId => $locationName) {
             $checkin_data = $this->postCurlApi($url, $start_date, $locationId, $max_retries,"checkinreport",$end_date);            
            //  $checkin_data = $this->postCurlApi($url, $start_date, "location24", $max_retries,"checkinreport",$end_date);            
            if (!empty($checkin_data['checkinlist'])) {
                foreach ($checkin_data['checkinlist'] as $checkin) {
                   $checkinDatas[] = $this->saveCurlDatas($checkin);
                //    echo "<pre>";print_r($checkinData);
                }
            }
        }
        // echo"<pre>"; print_r($checkinDatas);exit;
        $targetPhid = $request->mrd_data_type;
        $result = array_filter($checkinDatas, function ($item) use ($targetPhid) {
                return $item['phid'] === $targetPhid;
            });
            $result = array_values($result);
        // $result = json_decode($result);
            //   echo"<pre>"; print_r($result);exit;
            return response()->json($result);
        }

    public function discountform_detials(Request $request)
{
    $fitterremovedataall = $request->input('morefilltersall');    
    $datefiltervalue = $request->input('moredatefittervale');  
    $mrdno = $request->input('mrodnofilter');

    // Handle date range
    $dates = explode(' - ', $datefiltervalue);
    $startDate = $dates[0] ?? '';
    $endDate = $dates[1] ?? '';

    $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
    $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

    $start = Carbon::createFromFormat('Y-m-d', $startDateFormatted);
    $end = Carbon::createFromFormat('Y-m-d', $endDateFormatted);

    $start_date = $start->format('Ymd') . '00:00:00';
    $end_date = $end->format('Ymd') . '23:59:59';

    // Get location id
    $val = explode('=', $fitterremovedataall);                
    $value = !empty(trim($val[0], "'")) ? trim($val[1], "'") : '';        
    $locations = $this->cityArray();              
    $location = array_search($value , $locations);       

    // Get check-in data from external API
    $apiData = $this->mrdnoapi($start_date, $location, $end_date);
    $checkinData = [];

    if (!empty($apiData['checkinlist'])) {
        foreach ($apiData['checkinlist'] as $checkin) {
            $checkinData[] = $this->saveCurlData($checkin);
        }
    }
     
    return response()->json([
        'checkinData' => $checkinData,
        // 'discountForms' => $discountForms
    ]);
}

 public function discountDocument(){
        $admin = auth()->user();
        return view('superadmin.discountform_document',['admin' =>$admin]);
    }
}