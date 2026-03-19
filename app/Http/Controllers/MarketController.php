<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\TicketModel;
use App\Models\doctordetails;
use App\Models\documentdetails;
use App\Models\meetingdetails;
use App\Models\patientdetails;
use App\Models\ImageModel;
use App\Models\StatusModel;
use App\Models\PriorityModel;
use App\Models\CategoryModel;
use App\Models\LocationModel;
use App\Models\SubCategoryModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\TicketActivitiesModel;
use Carbon\Carbon;
use App\Models\HrmUsers;
use App\Models\UserProfile;
use App\Models\UserDesignations;
use App\Models\UserDepartments;
use App\Models\AdminUserDepartments;
use DataTables;
use DB;

class MarketController extends Controller
{
    public function dashboard(){
        $admin = auth()->user();
        return view('referral.dashboard', ['admin' => $admin]);
    }

    public function referral(){
        $admin = auth()->user();
        return view('referral.referral', ['admin' => $admin]);
    }

    public function doctoradded(Request $request)
    {

        $userfullname = auth()->user()->user_fullname;

       //dd($userfullname);

        $validatedData = $request->validate([
            'doctor_name' => 'required|string|max:255',
                    'empolyee_name' => 'required|string|max:255',
                    'special' => 'required|string|max:255',
                    'hopsital_name' => 'required|string|max:255',
                    'address' => 'required|string|max:255',
                    'city' => 'required|string|max:255',
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
                'userfullname' => $userfullname,
            ]));

            return response()->json(['success' => true, 'message' => 'Doctor saved successfully!']);

    }

    public function fetch()
    {
        $username = auth()->user()->username;
        $doctordetails = doctordetails::where('empolyee_name', $username)
    ->orderBy('created_at', 'desc')
    ->get();
        return response()->json($doctordetails);
    }


    public function fetchfitter(Request $request)
    {
        $username = auth()->user()->username;
        $datefiltervalue = $request->input('datefiltervalue');

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        $data = doctordetails::whereBetween('created_at', [$startdates, $enddates])
        ->where('empolyee_name', $username)
        ->get();
        return response()->json($data);
    }

    public function fetchmorefitter(Request $request)
    {
        $username = auth()->user()->username;
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

                        $query = doctordetails::query();

                        // Add whereBetween for created_at
                        $query->whereBetween('created_at', [$startdates, $enddates]);
                        $query->where('empolyee_name', $username);

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
        $username = auth()->user()->username;
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

        $query = doctordetails::query();

        // Add whereBetween for created_at
        $query->whereBetween('created_at', [$startdates, $enddates]);
        $query->where('empolyee_name', $username);

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
        $username = auth()->user()->username;
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
        $query = doctordetails::query();

        // Add whereBetween for created_at
        $query->whereBetween('created_at', [$startdates, $enddates]);
        $query->where('empolyee_name', $username);


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
        $username = auth()->user()->username;
        $datefilltervalue = $request->input('datefilltervalue');
        // dd($datefilltervalue);
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
        $query = doctordetails::query();

        // Add whereBetween for created_at
        $query->whereBetween('created_at', [$startdates, $enddates]);
        $query->where('empolyee_name', $username);

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
        $username = auth()->user()->username;
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
        $updated = doctordetails::where('id', $idviews)
        ->where('empolyee_name',$username)
        ->update($parsedData);

        // Return response based on the result of the update
        if ($updated) {
            return response()->json(['success' => 'Doctor details updated successfully']);
        } else {
            return response()->json(['error' => 'Update failed or no changes made'], 400);
        }
    }

    public function doctordetailsid(Request $request)
    {
        $username = auth()->user()->username;
        $idviews = $request->input('idviews');

         // Start the query
         $query = doctordetails::query();

         // Add whereBetween for created_at
         $query->where('id', [$idviews]);
         $query->where('empolyee_name',$username);


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
        $username = auth()->user()->username;
        $meetingvalue = $request->input('meetingvalue');

        $query = doctordetails::query();

        // Add whereBetween for created_at
        $query->where('id', [$meetingvalue]);
        $query->where('empolyee_name',$username);


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

        $userfullname = auth()->user()->user_fullname;

         // Get the input array
    $meetinginsertvalue = $request->input('meetinginsertvalue');

    // Convert the array values to an associative array
    $data = [];
    foreach ($meetinginsertvalue as $item) {
        // Split the string into key-value pairs
        [$key, $value] = explode('=', $item);
        $data[$key] = trim($value, "'");
    }

    $data['userfullname'] = $userfullname;

    // Insert the data into the database
    DB::table('ref_meeting_log')->insert($data);

    // Optional: Return a success response
    return response()->json(['message' => 'Meeting data inserted successfully']);


    }

    public function meetingallviews()
    {
        $username = auth()->user()->username;
        $doctordetails = DB::table('ref_meeting_log')
        ->where('empolyee_name',$username)
        ->orderBy('id', 'desc')->get();
        return response()->json($doctordetails);

    }

    // meeting date fitters

    public function meetingdatefitter(Request $request)
    {
        $username = auth()->user()->username;
        $datefilltervalue = $request->input('datefilltervalue');
        $dates = explode(' - ', $datefilltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDateview = $dates[1];    // "04/01/2025"
        $endDate = substr($endDateview, 0, 10);
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        $data = meetingdetails::whereBetween('created_at', [$startdates, $enddates])
        ->where('empolyee_name',$username)
        ->get();
        return response()->json($data);
    }

    public function meetingmorefitter(Request $request)
    {
        $username = auth()->user()->username;
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

        $query = meetingdetails::query();

        // Add whereBetween for created_at
        $query->whereBetween('created_at', [$startdates, $enddates]);
        $query->where('empolyee_name',$username);

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
    $username = auth()->user()->username;
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

    $query = meetingdetails::query();

    // Add whereBetween for created_at
    $query->whereBetween('created_at', [$startdates, $enddates]);
    $query->where('empolyee_name',$username);

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
    $username = auth()->user()->username;
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
    $query = meetingdetails::query();

    // Add whereBetween for created_at
    $query->whereBetween('created_at', [$startdates, $enddates]);
    $query->where('empolyee_name',$username);

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
    $username = auth()->user()->username;
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

    $query = meetingdetails::query();

    // Add whereBetween for created_at
    $query->whereBetween('created_at', [$startdates, $enddates]);
    $query->where('empolyee_name',$username);

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
    $username = auth()->user()->username;
    $meetingvalue = $request->input('meetingvalue');

        $query = doctordetails::query();

        // Add whereBetween for created_at
        $query->where('id', [$meetingvalue]);
        $query->where('empolyee_name',$username);


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
    $username = auth()->user()->username;
    $doctordetails = DB::table('ref_patient_details')
    ->where('empolyee_name',$username)
    ->orderBy('id', 'desc')->get();
        return response()->json($doctordetails);
}

public function patientdatefitter(Request $request)
{
    $username = auth()->user()->username;
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
        $data = patientdetails::whereBetween('created_at', [$startdates, $enddates])
        ->where('empolyee_name',$username)
        ->get();
        return response()->json($data);
}

public function patientmorefitter(Request $request)
{
    $username = auth()->user()->username;
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
        $query->where('empolyee_name', $username);

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
    $username = auth()->user()->username;
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
    $qyery->where('empolyee_name', $username);

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
    $username = auth()->user()->username;
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
    $query->where('empolyee_name',$username);

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
    $username = auth()->user()->username;
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
    $query->where('empolyee_name' , $username);

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
    $username = auth()->user()->username;
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

public function branchfetchviews()
{
   $documents = DB::table('hms_branch')->get();
   return response()->json($documents);
}

public function zonefetchviews()
{
   $documents = DB::table('hms_zone')->get();
   return response()->json($documents);
}

public function marketernamesurls()
{
   $documents = DB::table('tbl_hms_users')
   ->where('role', 'marketer') // Filter users with the role 'marketer'
   ->select('user_fullname','role')
   ->get();
   return response()->json($documents);
}


}
