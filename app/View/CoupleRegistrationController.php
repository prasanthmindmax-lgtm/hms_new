<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\CoupleRegistration;
use App\Models\CoupleAttachment;

use Illuminate\Support\Facades\DB;



class CoupleRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // ✅ validate required fields
        $request->validate([
            'husband_name' => 'required',
            'wife_name' => 'required',
            'husband_dob' => 'required|date',
            'wife_dob' => 'required|date',
            'husband_age' => 'required|integer',
            'wife_age' => 'required|integer',
            'contact_phone' => 'required',
            'infertility_type' => 'required',
            'consent_patient' => 'required'
        ]);

        // ✅ create registration
        $registration = CoupleRegistration::create($request->except('attachments'));

        // ✅ handle attachments
        if($request->hasFile('attachments')){
            foreach($request->file('attachments') as $file){
                $filename = time().'_'.$file->getClientOriginalName();
                $file->storeAs('attachments', $filename, 'public');
                

                CoupleAttachment::create([
                    'couple_id' => $registration->id,
                    'file_name' => $filename,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect('https://draravinds.com/hms/superadmin/Registerviewpage')
       ->with('success','Couple Registration saved successfully!');

    }

  public function getCouplesData()
{
    $couples = CoupleRegistration::all();

    return response()->json([
        'data' => $couples,
    
    ]);
}

 public function artIndex($id){

     $staffId = auth()->user()->id;
        $admin = auth()->user();

          // fetch couple details
        $couple = CoupleRegistration::with('attachments')->findOrFail($id);
        
        // return to blade view
        return view('superadmin.art_index', compact('couple','admin'));

    }

     public function registerivf(Request $request){

        $phid = $request->input('phid');
        $name = $request->input('name');
        $mobile = $request->input('mobile');
        $age = $request->input('age');
        $gender = $request->input('gender');
        $address = $request->input('address');
        $city = $request->input('city');
        $dob = $request->input('dob');

        $admin = auth()->user();

        // return to blade view
        return view('superadmin.registerivf', compact(
    'admin', 'phid', 'name', 'mobile', 'age', 'gender', 'address', 'city', 'dob'
));

    }

    

   public function getcheckinData()
{
    // ---- API Call ----
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
    CURLOPT_POSTFIELDS => 'startdate=2025050100:00:00&enddate=2025050823:59:59&entitylocation=location22',
    CURLOPT_HTTPHEADER => array(
        'md-authorization: MD 7b40af0edaf0ad75:jR1+YyQZVWCIIaXlgxt1z8uixQ4=',
        'Date: Mon, 31 Mar 2025 08:05:38 GMT',
        'Content-Type: application/x-www-form-urlencoded'
    ),
));

$response = curl_exec($curl);
curl_close($curl);

    $decoded = json_decode($response, true);

    

    // ✅ Extract checkinlist
    $checkins = $decoded['checkinlist'] ?? [];

    // ✅ Format for DataTables
    $formatted = [];
    foreach ($checkins as $item) {
        $patient = $item['patient'] ?? [];

        $formatted[] = [
            'checkinkey' => $item['checkinkey'] ?? '',
            'mobile'    => $patient['mobile'] ?? '',
            'age'          => $patient['age'] ?? '',
            'phid'      => $patient['phid'] ?? '',
            'gender'        => $patient['gender'] ?? '',
            'address'      => $patient['address']['street'] ?? '',
            'name'         => $patient['name'] ?? '',
            'dob'           => $patient['dob'] ?? '', // unique id for actions
            'city' => $patient['address']['city'] ?? '',
            'created_at'=> $item['created_at'] ?? '',
            'updated_at'=> $item['updated_at'] ?? '',
            
        ];
    }

    return response()->json(['data' => $formatted]);
}




}
