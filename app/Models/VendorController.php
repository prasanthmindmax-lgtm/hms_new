<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\TicketModel;
use App\Models\TicketDetails;
use App\Models\ImageModel;
use App\Models\StatusModel;
use App\Models\PriorityModel;
use App\Models\CategoryModel;
use App\Models\TblUserDepartments;
use App\Models\LocationModel;
use App\Models\SubCategoryModel;
use App\Models\TicketActivitiesModel;
use App\Models\TicketActivityModel;
use App\Models\TblLocationModel;
use App\Models\User;
use App\Models\HrmUsers;
use App\Models\UserProfile;
use App\Models\UserDesignations;
use App\Models\UserDepartments;
use App\Models\AdminUserDepartments;
use App\Models\Customer;
use App\Models\Tblcustomer;
use App\Models\TblBilling;
use App\Models\TblShipping;
use App\Models\TblContact;
use App\Models\Tblbankdetails;
use App\Models\Tblvendor;
use App\Models\Tbltdstax;
use App\Models\Tbltcstax;
use App\Models\Tblgsttax;
use App\Models\Tblbill;
use App\Models\TblBillLines;
use App\Models\Tblbillpay;
use App\Models\TblBillPayLines;
use App\Models\TblPurchaseorder;
use App\Models\TblPurchaseorderLines;
use App\Models\Tblneft;
use App\Models\Tblneftlines;
use App\Models\TblQuotation;
use App\Models\TblQuotationLines;
use App\Models\Tblgrn;
use App\Models\TblgrnLines;
use App\Models\Tblnaturepayment;
use App\Models\Tblaccount;
use App\Models\TblZonesModel;
use App\Models\TblPoEmail;
use App\Models\Tbltdssection;
use App\Models\TblVendorHistory;
use App\Models\Tblcompany;
use App\Models\TblVendortype;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use TCPDF;
use App\Imports\QuotationImport;
use App\Imports\VendorImport;
use App\Imports\billImport;
use App\Imports\PurchaseImport;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\VendorTemplateExport;
use App\Exports\QuotationTemplateExport;
use App\Exports\BillTemplateExport;
use App\Exports\PurchaseTemplateExport;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Illuminate\Support\Facades\Mail;

class VendorController extends Controller
{

    public function getcustomer()
    {

        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();

        $customers = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
        return view('vendor.customer', ['admin' => $admin,'locations' => $locations,'customers' => $customers]);
    }
    public function getcustomercreate()
    {
        $id="";
        if(isset($_GET['id'])){
            $id=$_GET['id'];
        }
        // dd($id);
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        if($id !==""){
            $customers = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->where('id',$id)->get();
            return view('vendor.create_customer', ['admin' => $admin,'locations' => $locations,'customers' => $customers]);
        }else{
            return view('vendor.create_customer', ['admin' => $admin,'locations' => $locations]);
        }
    }

public function saveCustomer(Request $request)
{
    $isUpdate = $request->filled('id'); // Check if ID exists
    $now = now();
    $user_id = auth()->user()->id;
    $data = [
        'user_id' => $user_id,
        'customer_type' => $request->customer_type,
        'customer_salutation' => $request->primary_contact_salutation,
        'customer_first_name' => $request->primary_contact_first_name,
        'customer_last_name' => $request->primary_contact_last_name,
        'company_name' => $request->company_name,
        'display_name' => $request->display_name,
        'email' => $request->email,
        'work_phone' => $request->work_phone,
        'mobile' => $request->mobile,
        'pan_number' => $request->pan,
        'opening_balance' => $request->opening_balance,
        'payment_terms' => $request->payment_terms,
        'portal_language' => $request->portal_language,
        'website' => $request->website,
        'department' => $request->department,
        'designation' => $request->designation,
        'twitter' => $request->twitter,
        'skype' => $request->skype,
        'facebook' => $request->facebook,
        'remarks' => $request->remarks,
        'updated_at' => $now,
    ];


    if (!$isUpdate) {
        $data['updated_at'] = $now;
    }

    // Handle file uploads
    if ($request->hasFile('documents')) {
        $fileNames = [];
        $uploadPath = public_path('uploads/customers');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        foreach ($request->file('documents') as $file) {
            $originalName = $file->getClientOriginalName();
            $uniqueFileName = time() . '_' . $originalName;
            $file->move($uploadPath, $uniqueFileName);
            $fileNames[] = $uniqueFileName;
        }

        $data['documents'] = json_encode($fileNames);
    }

    if ($isUpdate) {
        $customer = Tblcustomer::findOrFail($request->id);
        $customer->update($data);
        $customer_id = $customer->id;

        // Update billing
        TblBilling::updateOrCreate(
            ['customer_id' => $customer_id],
            [
                'attention' => $request->billing_attention,
                'country' => $request->billing_country,
                'address' => $request->billing_address,
                'city' => $request->billing_city,
                'state' => $request->billing_state,
                'zip_code' => $request->billing_zip_code,
                'phone' => $request->billing_phone,
                'fax' => $request->billing_fax,
                'updated_at' => $now
            ]
        );

        // Update shipping
        TblShipping::updateOrCreate(
            ['customer_id' => $customer_id],
            [
                'attention' => $request->shipping_attention,
                'country' => $request->shipping_country,
                'address' => $request->shipping_address,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'zip_code' => $request->shipping_zip_code,
                'phone' => $request->shipping_phone,
                'fax' => $request->shipping_fax,
                'updated_at' => $now
            ]
        );

        // Update or insert contact persons
        if ($request->has('contact_persons')) {
            foreach ($request->contact_persons as $contactData) {
                $contactValues = [
                    'customer_id' => $customer_id,
                    'salutation' => $contactData['salutation'] ?? null,
                    'first_name' => $contactData['first_name'] ?? null,
                    'last_name' => $contactData['last_name'] ?? null,
                    'email' => $contactData['email'] ?? null,
                    'work_phone' => $contactData['work_phone'] ?? null,
                    'mobile' => $contactData['mobile'] ?? null,
                    'updated_at' => $now,
                ];

                if (!empty($contactData['id'])) {
                    TblContact::where('id', $contactData['id'])
                        ->where('customer_id', $customer_id)
                        ->update($contactValues);
                } else {
                    $contactValues['created_at'] = $now;
                    TblContact::create($contactValues);
                }
            }
        }
    } else {
        // Create new customer
        $customer = Tblcustomer::create($data);
        $customer_id = $customer->id;

        // Insert billing
        TblBilling::create([
            'customer_id' => $customer_id,
            'attention' => $request->billing_attention,
            'country' => $request->billing_country,
            'address' => $request->billing_address,
            'city' => $request->billing_city,
            'state' => $request->billing_state,
            'zip_code' => $request->billing_zip_code,
            'phone' => $request->billing_phone,
            'fax' => $request->billing_fax,
            'created_at' => $now,
        ]);

        // Insert shipping
        TblShipping::create([
            'customer_id' => $customer_id,
            'attention' => $request->shipping_attention,
            'country' => $request->shipping_country,
            'address' => $request->shipping_address,
            'city' => $request->shipping_city,
            'state' => $request->shipping_state,
            'zip_code' => $request->shipping_zip_code,
            'phone' => $request->shipping_phone,
            'fax' => $request->shipping_fax,
            'created_at' => $now,
        ]);

        // Insert contact persons
        if ($request->has('contact_persons')) {
            foreach ($request->contact_persons as $contactData) {
                TblContact::create([
                    'customer_id' => $customer_id,
                    'salutation' => $contactData['salutation'] ?? null,
                    'first_name' => $contactData['first_name'] ?? null,
                    'last_name' => $contactData['last_name'] ?? null,
                    'email' => $contactData['email'] ?? null,
                    'work_phone' => $contactData['work_phone'] ?? null,
                    'mobile' => $contactData['mobile'] ?? null,
                    'created_at' => $now,
                ]);
            }
        }
    }

    return response()->json([
        'success' => true,
        'message' => $isUpdate ? 'Customer Data updated successfully!' : 'Customer Data saved successfully!'
    ]);
}


// public function getvendor()
//     {
//         $admin = auth()->user();
//         $limit_access=$admin->access_limits;
//         $locations = TblLocationModel::all();

//         $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts','bankdetails'])->get();
//         return view('vendor.vendor', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor]);
//     }
public function getvendor(Request $request)
{

    $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::orderBy('id', 'asc')->get();
        // $query = Tblneft::with(['Tblvendor','BillLines','Tblbankdetails','Tblbillpay','BillLines.Tblbilllines'])->orderBy('id', 'desc');
        $query = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails','history'])->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('vendor_id')) {
            $query->where('id', $request->vendor_id);
        }

        // Run query AFTER filters
        $vendor = $query->paginate($perPage)->appends($request->all());
        // dd($vendor);
        $Tblvendor = Tblvendor::orderBy('id', 'asc')->get();
        // If AJAX request, return only table rows
        if ($request->ajax()) {
            return view('vendor.partials.table.vendor_rows', compact('vendor','perPage'))->render();
        }

        // Normal page load
        return view('vendor.vendor', [
            'admin' => $admin,
            'locations' => $locations,
            'vendor' => $vendor,
            'perPage' => $perPage,
            // 'Tblcompany' => $Tblcompany,
            'Tblvendor' => $Tblvendor,
            // 'purchaselist' => $purchaselist,
            // 'limit_access' => $limit_access
        ]);

    // $admin = auth()->user();
    // $limit_access = $admin->access_limits;
    // $locations = TblLocationModel::all();
    // $perPage = $request->get('per_page', 10);

    // $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails','history'])
    //     ->orderBy('id', 'desc')
    //     ->paginate($perPage)
    //     ->appends(['per_page' => $perPage]);
    // // dd($vendor);
    // // $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->orderBy('id', 'desc')->paginate(10);
    // return view('vendor.vendor', [
    //     'admin' => $admin,
    //     'locations' => $locations,
    //     'vendor' => $vendor,
    //     'perPage' => $perPage,

    // ]);
}

public function getvendorcreate()
    {
        $id="";
        if(isset($_GET['id'])){
            $id=$_GET['id'];
        }
        // dd($id);
        $count = Tblvendor::count(); // Get total number of rows
        $nextNumber = $count + 1;  // Next serial number
        $serial = 'VEN-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10);
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $TblVendortype = TblVendortype::orderBy('id', 'asc')->get();
        if($id !==""){
            $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts','bankdetails','tdstax'])->where('id',$id)->get();
            // dd($vendor);
            return view('vendor.vendor_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'Tbltdstax' => $Tbltdstax,'Tbltdssection' => $Tbltdssection,'TblVendortype' => $TblVendortype]);
        }else{
            return view('vendor.vendor_create', ['admin' => $admin,'locations' => $locations,'serial' => $serial,'Tbltdstax' => $Tbltdstax,'Tbltdssection' => $Tbltdssection,'TblVendortype' => $TblVendortype]);
        }
    }
    public function savevendor(Request $request)
{
    // dd($request);
    $isUpdate = $request->filled('id'); // Check if ID exists
    $now = now();
    $user_id = auth()->user()->id;
    $admin = auth()->user();
    $data = [
        'user_id' => $user_id,
        'vendor_id' => $request->vendor_id,
        'vendor_salutation' => $request->primary_contact_salutation,
        'vendor_first_name' => $request->primary_contact_first_name,
        'vendor_last_name' => $request->primary_contact_last_name,
        'company_name' => $request->company_name,
        'display_name' => $request->display_name,
        'email' => $request->email,
        'work_phone' => $request->work_phone,
        'reference' => $request->reference_name,
        'mobile' => $request->mobile,
        'pan_number' => $request->pan,
        'gst_number' => $request->gst_number,
        'vendor_type_name' => $request->vendor_type_name,
        'vendor_type_id' => $request->vendor_type_id,
        'opening_balance' => $request->opening_balance,
        'payment_terms' => $request->payment_terms,
        'portal_language' => $request->portal_language,
        'website' => $request->website,
        'department' => $request->department,
        'designation' => $request->designation,
        'twitter' => $request->twitter,
        'skype' => $request->skype,
        'facebook' => $request->facebook,
        'tds_tax_id' => $request->tds_tax_id,
        'tds_amount' => $request->tds_amount,
        'tds_tax_name' => $request->tds_tax_name,
        'remarks' => $request->remarks,
        'updated_at' => $now,
    ];
    if (!$isUpdate) {
        $data['updated_at'] = $now;
    }

    // dd($data);
    // Handle document uploads
    if ($request->hasFile('documents')) {
        $fileNames = [];
        $uploadPath = public_path('uploads/customers');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        foreach ($request->file('documents') as $file) {
            $originalName = $file->getClientOriginalName();
            $uniqueFileName = time() . '_' . $originalName;
            $file->move($uploadPath, $uniqueFileName);
            $fileNames[] = $uniqueFileName;
        }

        // Merge with existing files
        $existingFiles = [];
        if ($request->filled('existing_files')) {
            $existingFiles = json_decode($request->existing_files, true) ?? [];
        }

        // Filter out removed files
        $removedFiles = $request->removed_documents ?? [];
        $existingFiles = array_diff($existingFiles, $removedFiles);

        $data['documents'] = json_encode(array_merge($existingFiles, $fileNames));
    } elseif ($request->filled('existing_files')) {
        // Only existing files (no new uploads)
        $existingFiles = json_decode($request->existing_files, true) ?? [];
        $removedFiles = $request->removed_documents ?? [];
        $data['documents'] = json_encode(array_diff($existingFiles, $removedFiles));
    }

    // Handle PAN uploads
    if ($request->hasFile('pan_upload')) {
        $fileNames = [];
        $uploadPath = public_path('uploads/customers');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        foreach ($request->file('pan_upload') as $file) {
            $originalName = $file->getClientOriginalName();
            $uniqueFileName = time() . '_' . $originalName;
            $file->move($uploadPath, $uniqueFileName);
            $fileNames[] = $uniqueFileName;
        }

        // Merge with existing PAN files
        $existingFilespan = [];
        if ($request->filled('existing_files_pan')) {
            $existingFilespan = json_decode($request->existing_files_pan, true) ?? [];
        }

        // Filter out removed PAN files
        $removedPanFiles = $request->removed_pan_files ?? [];
        $existingFilespan = array_diff($existingFilespan, $removedPanFiles);

        $data['pan_upload'] = json_encode(array_merge($existingFilespan, $fileNames));
    } elseif ($request->filled('existing_files_pan')) {
        // Only existing PAN files (no new uploads)
        $existingFilespan = json_decode($request->existing_files_pan, true) ?? [];
        $removedPanFiles = $request->removed_pan_files ?? [];
        $data['pan_upload'] = json_encode(array_diff($existingFilespan, $removedPanFiles));
    }

    if ($isUpdate) {
        $vendor = Tblvendor::findOrFail($request->id);
        $vendor->update($data);
        $vendor_id = $vendor->id;
        // Update billing
        $Billing=TblBilling::updateOrCreate(
            ['vendor_id' => $vendor_id],
            [
                'attention' => $request->billing_attention,
                'country' => $request->billing_country,
                'address' => $request->billing_address,
                'city' => $request->billing_city,
                'state' => $request->billing_state,
                'zip_code' => $request->billing_zip_code,
                'phone' => $request->billing_phone,
                'fax' => $request->billing_fax,
                'updated_at' => $now
            ]
        );
        // dd($Billing);

        // Update shipping
        $Shipping=TblShipping::updateOrCreate(
            ['vendor_id' => $vendor_id],
            [
                'attention' => $request->shipping_attention,
                'country' => $request->shipping_country,
                'address' => $request->shipping_address,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'zip_code' => $request->shipping_zip_code,
                'phone' => $request->shipping_phone,
                'fax' => $request->shipping_fax,
                'updated_at' => $now
            ]
        );
        if ($request->has('contact_persons')) {
            foreach ($request->contact_persons as $contactData) {
                $contactValues = [
                    'customer_id' => $customer_id ?? null ,
                    'vendor_id' => $vendor_id,
                    'salutation' => $contactData['salutation'] ?? null,
                    'first_name' => $contactData['first_name'] ?? null,
                    'last_name' => $contactData['last_name'] ?? null,
                    'email' => $contactData['email'] ?? null,
                    'work_phone' => $contactData['work_phone'] ?? null,
                    'mobile' => $contactData['mobile'] ?? null,
                    'updated_at' => $now,
                ];

                if (!empty($contactData['id'])) {
                    TblContact::where('id', $contactData['id'])
                        ->where('vendor_id', $vendor_id)
                        ->update($contactValues);

                } else {
                    $contactValues['created_at'] = $now;
                    TblContact::create($contactValues);

                }
            }

        }
        if ($request->has('bank_details')) {
            foreach ($request->bank_details as $index => $bankData) {
                $bankdetails = [
                    'vendor_id' => $vendor_id,
                    'account_holder_name' => $bankData['account_holder_name'] ?? null,
                    'bank_name' => $bankData['bank_name'] ?? null,
                    'accont_number' => $bankData['account_number'] ?? null,
                    'ifsc_code' => $bankData['ifsc'] ?? null,
                    'created_at' => $now,
                ];

                $fileNames = [];

                if ($request->hasFile("bank_details.$index.bank_uploads")) {
                    $uploadPath = public_path('uploads/customers');

                    if (!File::exists($uploadPath)) {
                        File::makeDirectory($uploadPath, 0755, true);
                    }

                    foreach ($request->file("bank_details.$index.bank_uploads") as $file) {
                        $originalName = $file->getClientOriginalName();
                        $uniqueFileName = time() . '_' . $originalName;
                        $file->move($uploadPath, $uniqueFileName);
                        $fileNames[] = $uniqueFileName;
                    }

                    // Save the file names as JSON (optional)
                    $existingFilesbank = $bankData['existing_files'] ?? [];
                    if (!is_array($existingFilesbank)) {
                        $existingFilesbank = json_decode($existingFilesbank, true) ?? [];
                    }
                    $mergedFilesbank = array_merge($existingFilesbank, $fileNames);
                    $bankdetails['bank_uploads'] = json_encode($mergedFilesbank);
                }
                // $bankdetails['bank_uploads'] = json_encode($fileNames);
                if (!empty($bankData['id'])) {
                    Tblbankdetails::where('id', $bankData['id'])
                        ->where('vendor_id', $vendor_id)
                        ->update($bankdetails);

                } else {
                    Tblbankdetails::create($bankdetails);

                }
            }

        }
        $history = [
                'vendor_id'   => $vendor_id,
                'name'        => 'Contact Updated',
                'description' => "Contact updated by {$admin->email}",
                'date' => now()->toDateString(),
                'time' => now()->format('h:i A')
            ];
        TblVendorHistory::create($history);

    } else {
        // Create new customer
        $vendor = Tblvendor::create($data);
            $vendor_id = $vendor->id;
            // dd($vendor_id);
        // Insert billing
        $Billing=TblBilling::create([
            'customer_id' => $customer_id ?? null,
            'vendor_id' => $vendor_id ,
            'attention' => $request->billing_attention,
            'country' => $request->billing_country,
            'address' => $request->billing_address,
            'city' => $request->billing_city,
            'state' => $request->billing_state,
            'zip_code' => $request->billing_zip_code,
            'phone' => $request->billing_phone,
            'fax' => $request->billing_fax,
            'created_at' => $now
        ]);
        // Insert shipping
        $Shipping=TblShipping::create([
            'customer_id' => $customer_id ?? null,
            'vendor_id' => $vendor_id,
            'attention' => $request->shipping_attention,
            'country' => $request->shipping_country,
            'address' => $request->shipping_address,
            'city' => $request->shipping_city,
            'state' => $request->shipping_state,
            'zip_code' => $request->shipping_zip_code,
            'phone' => $request->shipping_phone,
            'fax' => $request->shipping_fax,
            'created_at' => $now
        ]);
        if(!empty($Shipping) && !empty($Billing)){
                $history = [
                    'vendor_id'   => $vendor_id,
                    'name'        => 'Contact added',
                    'description' => "Address created by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
        }

        // Insert contact persons
        if ($request->has('contact_persons')) {
            foreach ($request->contact_persons as $contactData) {
                if($contactData['first_name'] !== "" && $contactData['first_name'] !==null){
                    TblContact::create([
                        'customer_id' => $customer_id ?? null,
                        'vendor_id' => $vendor_id,
                        'salutation' => $contactData['salutation'] ?? null,
                        'first_name' => $contactData['first_name'] ?? null,
                        'last_name' => $contactData['last_name'] ?? null,
                        'email' => $contactData['email'] ?? null,
                        'work_phone' => $contactData['work_phone'] ?? null,
                        'mobile' => $contactData['mobile'] ?? null,
                        'created_at' => $now,
                    ]);
                    $history = [
                        'vendor_id'   => $vendor_id,
                        'name'        => 'Contact Person added',
                        'description' => "Contact person {$contactData['first_name']} has been created by {$admin->email}",
                        'date' => now()->toDateString(),
                        'time' => now()->format('h:i A')
                    ];
                    TblVendorHistory::create($history);

                }
            }
        }
       if ($request->has('bank_details')) {
            foreach ($request->bank_details as $index => $bankData) {

                // Prepare bank data array
                $bankdetails = [
                    'vendor_id' => $vendor_id,
                    'account_holder_name' => $bankData['account_holder_name'] ?? null,
                    'bank_name' => $bankData['bank_name'] ?? null,
                    'accont_number' => $bankData['account_number'] ?? null,
                    'ifsc_code' => $bankData['ifsc'] ?? null,
                    'created_at' => $now,
                ];

                // Handle file uploads (optional)
                if ($request->hasFile("bank_details.$index.bank_uploads")) {
                    $fileNames = [];
                    $uploadPath = public_path('uploads/customers');

                    if (!File::exists($uploadPath)) {
                        File::makeDirectory($uploadPath, 0755, true);
                    }

                    foreach ($request->file("bank_details.$index.bank_uploads") as $file) {
                        $originalName = $file->getClientOriginalName();
                        $uniqueFileName = time() . '_' . uniqid() . '_' . $originalName;
                        $file->move($uploadPath, $uniqueFileName);
                        $fileNames[] = $uniqueFileName;
                    }

                    // Save filenames as JSON (make sure `bank_uploads` column exists)
                    $bankdetails['bank_uploads'] = json_encode($fileNames);
                }

                // Create the record
                Tblbankdetails::create($bankdetails);
                $history = [
                            'vendor_id'   => $vendor_id,
                            'name'        => 'Bank Details added',
                            'description' => "Bank details created by {$admin->email}",
                            'date' => now()->toDateString(),
                            'time' => now()->format('h:i A')
                        ];
                TblVendorHistory::create($history);
            }
        }

    }

    return response()->json([
        'success' => true,
        'message' => $isUpdate ? 'Vendor Data updated successfully!' : 'Vendor Data saved successfully!'
    ]);
}

public function vendortemplate()
    {
        return Excel::download(new VendorTemplateExport, 'vendor_template.xlsx');
    }
public function importvendorExcel(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    Excel::import(new VendorImport, $request->file('file'));

   return response()->json([
        'status' => 'success',
        'message' => 'Vendor data imported successfully!'
    ]);
}
public function exportvendor(Request $request)
    {
        ini_set('memory_limit', '1024M');

        // Get IDs and format from request
        $ids = $request->input('ids', []);
        $format = $request->input('format', 'xlsx'); // default to xlsx

        // Get data: filter by IDs if provided
        $query = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts','bankdetails']);
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }
        $vendor = $query->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $headers =['vendorId','Salutation','First Name','Last Name','company Name','display Name','EmailID','Phone','MobilePhone','PAN Number','Payment Terms',
                'Website','Opening Balance','Skype Identity','Department','Designation','Facebook','Twitter','Billing Attention','Billing Address','Billing City',
                'Billing State','Billing Country','Billing Code','Billing Phone','Billing Fax','Shipping Attention','Shipping Address','Shipping City','Shipping State',
                'Shipping Country','Shipping Code','Shipping Phone','Shipping Fax','Vendor Bank Holder Name','Vendor Bank Account Number','Vendor Bank Name','Vendor Bank IFSC Code'];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        $row = 2;
       foreach ($vendor as $vendor) {
            // dd($vendor);
            $sheet->setCellValue('A' . $row, $vendor->vendor_id);
            $sheet->setCellValue('B' . $row, $vendor->vendor_salutation);
            $sheet->setCellValue('C' . $row, $vendor->vendor_first_name);
            $sheet->setCellValue('D' . $row, $vendor->vendor_last_name);
            $sheet->setCellValue('E' . $row, $vendor->company_name);
            $sheet->setCellValue('F' . $row, $vendor->display_name);
            $sheet->setCellValue('G' . $row, $vendor->email);
            $sheet->setCellValue('H' . $row, $vendor->work_phone);
            $sheet->setCellValue('I' . $row, $vendor->mobile);
            $sheet->setCellValue('J' . $row, $vendor->pan_number);
            $sheet->setCellValue('K' . $row, $vendor->payment_terms);
            $sheet->setCellValue('L' . $row, $vendor->website);
            $sheet->setCellValue('M' . $row, $vendor->opening_balance);
            $sheet->setCellValue('N' . $row, $vendor->skype);
            $sheet->setCellValue('O' . $row, $vendor->department);
            $sheet->setCellValue('P' . $row, $vendor->designation);
            $sheet->setCellValue('Q' . $row, $vendor->facebook);
            $sheet->setCellValue('R' . $row, $vendor->twitter);
            $sheet->setCellValue('S' . $row, optional($vendor->billingAddress)->attention);
            $sheet->setCellValue('T' . $row, optional($vendor->billingAddress)->address);
            $sheet->setCellValue('U' . $row, optional($vendor->billingAddress)->city);
            $sheet->setCellValue('V' . $row, optional($vendor->billingAddress)->state);
            $sheet->setCellValue('W' . $row, optional($vendor->billingAddress)->country);
            $sheet->setCellValue('X' . $row, optional($vendor->billingAddress)->zip_code);
            $sheet->setCellValue('Y' . $row, optional($vendor->billingAddress)->phone);
            $sheet->setCellValue('Z' . $row, optional($vendor->billingAddress)->fax);
            $sheet->setCellValue('AA' . $row, optional($vendor->shippingAddress)->attention);
            $sheet->setCellValue('AB' . $row, optional($vendor->shippingAddress)->address);
            $sheet->setCellValue('AC' . $row, optional($vendor->shippingAddress)->city);
            $sheet->setCellValue('AD' . $row, optional($vendor->shippingAddress)->state);
            $sheet->setCellValue('AE' . $row, optional($vendor->shippingAddress)->country);
            $sheet->setCellValue('AF' . $row, optional($vendor->shippingAddress)->zip_code);
            $sheet->setCellValue('AG' . $row, optional($vendor->shippingAddress)->phone);
            $sheet->setCellValue('AH' . $row, optional($vendor->shippingAddress)->fax);
            $sheet->setCellValue('AJ' . $row, optional($vendor->bankdetails[0])->account_holder_name);
            $sheet->setCellValue('AK' . $row, optional($vendor->bankdetails[0])->accont_number);
            $sheet->setCellValue('AL' . $row, optional($vendor->bankdetails[0])->bank_name);
            $sheet->setCellValue('AM' . $row, optional($vendor->bankdetails[0])->ifsc_code);

            $row++;
        }


        // Generate and return file
        $filename = 'Vendor_export.' . $format;
        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        if ($format === 'csv') {
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            $writer->setSheetIndex(0);
        } else {
            $writer = new Xlsx($spreadsheet);
        }

        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

   public function gettranscationvendor(Request $request)
{
    $id = $request->id;
    $type = $request->type;
    $page = $request->page;
    // Set pagination page name based on type
    $pageName = $type ? $type . '_page' : 'page';

    // Initialize variables
    $Tblbill = null;
    $TblQuotation = null;
    $TblPurchaseorder = null;
    $Tblgrn = null;
    $Tblbillpay = null;

    // Fetch data based on the requested type
    if ($type) {
        switch ($type) {
            case 'quotation':
                $TblQuotation = TblQuotation::with(['BillLines', 'Tblvendor', 'TblBilling'])
                            ->where('vendor_id', $id)
                            ->orderBy('id', 'desc')
                            ->paginate(10, ['*'], $pageName);
                $TblQuotation->appends(['id' => $id, 'type' => $type]);
                break;

            case 'purchase':
                $TblPurchaseorder = TblPurchaseorder::with(['BillLines', 'Tblvendor', 'TblBilling'])
                            ->where('vendor_id', $id)
                            ->orderBy('id', 'desc')
                            ->paginate(10, ['*'], $pageName);
                $TblPurchaseorder->appends(['id' => $id, 'type' => $type]);
                break;

            case 'bill':
                $Tblbill = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])
                            ->where('vendor_id', $id)
                            ->orderBy('id', 'desc')
                            ->paginate(10, ['*'], $pageName);
                $Tblbill->appends(['id' => $id, 'type' => $type]);
                break;

            case 'billpay':
                $Tblbillpay = Tblbillpay::with(['BillLines','BillLines.Bill','BillLines.BillLines','Tblvendor','TblBilling','Tblbankdetails'])
                            ->where('vendor_id', $id)
                            ->orderBy('id', 'desc')
                            ->paginate(10, ['*'], $pageName);
                $Tblbillpay->appends(['id' => $id, 'type' => $type]);
                break;

            case 'grn':
                $Tblgrn = Tblgrn::with(['BillLines','Tblvendor','TblBilling'])
                            ->where('vendor_id', $id)
                            ->orderBy('id', 'desc')
                            ->paginate(10, ['*'], $pageName);
                $Tblgrn->appends(['id' => $id, 'type' => $type]);
                break;
        }
    } else {
        $TblQuotation = TblQuotation::with(['BillLines', 'Tblvendor', 'TblBilling'])
                    ->where('vendor_id', $id)
                    ->orderBy('id', 'desc')
                    ->paginate(10, ['*'], 'quotation_page');

        $TblPurchaseorder = TblPurchaseorder::with(['BillLines', 'Tblvendor', 'TblBilling'])
                    ->where('vendor_id', $id)
                    ->orderBy('id', 'desc')
                    ->paginate(10, ['*'], 'purchase_page');

        $Tblbill = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])
                    ->where('vendor_id', $id)
                    ->orderBy('id', 'desc')
                    ->paginate(10, ['*'], 'bill_page');

        $Tblbillpay = Tblbillpay::with(['BillLines','BillLines.Bill','BillLines.BillLines','Tblvendor','TblBilling','Tblbankdetails'])
                    ->where('vendor_id', $id)
                    ->orderBy('id', 'desc')
                    ->paginate(10, ['*'], 'billpay_page');

        $Tblgrn = Tblgrn::with(['BillLines','Tblvendor','TblBilling'])
                    ->where('vendor_id', $id)
                    ->orderBy('id', 'desc')
                    ->paginate(10, ['*'], 'grn_page');

    }

    $viewData = [
        'TblQuotation' => $TblQuotation,
        'TblPurchaseorder' => $TblPurchaseorder,
        'Tblgrn' => $Tblgrn,
        'Tblbill' => $Tblbill,
        'Tblbillpay' => $Tblbillpay,
        'id' => $id,
        'type' => $type
    ];

    // Render partials separately
    $html = view('vendor.partials.transcation_vendor', $viewData)->render();
    $statement = view('vendor.statement.statement', compact('id'))->render();

    if ($request->ajax()) {
        return response()->json([
            'html' => $html,
            'statement' => $statement
        ]);
    }

    return $html;
}
public function getvendorchart(Request $request)
{
    $id = $request->id;

    // Month-wise grand totals
    $totals = Tblbill::selectRaw("DATE_FORMAT(created_at, '%b') as month, SUM(grand_total_amount) as total")
        ->where('vendor_id', $id)
        ->groupBy('month')
        ->orderByRaw("MIN(created_at)")
        ->pluck('total', 'month');

    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $data = [];
    foreach ($months as $m) {
        $data[] = isset($totals[$m]) ? (float) $totals[$m] : 0;
    }

    // Total partially paid
    $balance = Tblbill::where('vendor_id', $id)
        ->sum('partially_payment');

    return response()->json([
        'months'   => $months,
        'amounts'  => $data,
        'balance'  => $balance
    ]);
}


public function showStatement(Request $request)
{
    // dd($request);
    $id = $request->id;
    $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])
        ->where('vendor_id', $id);

    if ($request->filled('date_from') && $request->filled('date_to')) {
        $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
        $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
    } else {
        // Default to current month
        $from = Carbon::now()->startOfMonth();
        $to   = Carbon::now()->endOfMonth();
    }

    $query->whereBetween('created_at', [$from, $to]);
    $bills = $query->orderBy('id', 'asc')->get();
    // dd($bills);
    return view('vendor.statement.statement_view', compact('bills','id','from','to'))->render();
}

public function statementprint(Request $request, $id)
{

    $billsQuery = Tblbill::with(['BillLines', 'Tblvendor', 'TblBilling', 'Tblbankdetails'])
        ->where('vendor_id', $id);
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
        $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
    } else {
        $from = Carbon::now()->startOfMonth();
        $to   = Carbon::now()->endOfMonth();
    }

    $billsQuery->whereBetween('created_at', [$from, $to]);
    $bills = $billsQuery->orderBy('id', 'asc')->get();
    if ($bills->isEmpty()) {
        abort(404, 'No bills found in this period.');
    }
    $vendor  = $bills->first()->Tblvendor ?? null;
    $billing = $bills->first()->TblBilling ?? null;

    $billed  = $bills->sum('grand_total_amount');
    $paid    = $bills->sum('tax_amount');
    $balance = $billed - $paid;

    $runningBalance = 0;
    $transactions   = [];
    foreach ($bills as $bill) {
        $grandTotal = (float) $bill->grand_total_amount;
        $runningBalance += $grandTotal;

        $transactions[] = [
            'date'     => $bill->bill_date ? $bill->bill_date : $bill->created_at->format('d/m/Y'),
            'type'     => 'Bill',
            'details'  => ($bill->bill_gen_number ?? $bill->bill_number ?? '') .
                         ($bill->due_date ? ' - due on ' . $bill->due_date : ''),
            'amount'   => $grandTotal,
            'payment'  => 0,
            'balance'  => $runningBalance
        ];

        if (!empty($bill->tax_type) && (float) $bill->tax_amount > 0) {
            $runningBalance -= (float) $bill->tax_amount;
            $transactions[] = [
                'date'     => $bill->bill_date ? $bill->bill_date : $bill->created_at->format('d/m/Y'),
                'type'     => $bill->tax_type ?? 'TDS',
                'details'  => 'Bill Number - ' . ($bill->bill_number ?? $bill->bill_gen_number ?? $bill->id),
                'amount'   => 0,
                'payment'  => (float) $bill->tax_amount,
                'balance'  => $runningBalance
            ];
        }
    }

    $pdf = new TCPDF();
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(15, 15, 15);
    $pdf->AddPage();

    include(resource_path('views/vendor/statement/statementprint.blade.php'));
    if($request->download=='pdf'){
        return response($pdf->Output('statement.pdf', 'S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="statement.pdf"');
    }else{
        return response($pdf->Output('statement.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

}




//bill making

    public function getbill(Request $request)
    {
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::orderBy('id', 'asc')->get();
        $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
         if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        $billlist = $query->paginate($perPage)->appends($request->all());

        if ($request->ajax()) {
            // Render ONLY the tbody rows from a partial view
            return view('vendor.partials.table.bill_rows', compact('billlist','perPage'))->render();
        }

        // $billlist = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])->orderBy('id', 'desc')->paginate($perPage)->appends(['per_page' => $perPage]);
        return view('vendor.bill_dashboard', ['admin' => $admin,'locations' => $locations,'billlist' => $billlist,'perPage' => $perPage,'TblZonesModel' => $TblZonesModel,'Tblcompany' => $Tblcompany, 'Tblvendor' => $Tblvendor]);
    }
    // public function getbillcreate(Request $request)
    // {
    //     $id="";
    //     if(isset($_GET['id'])){
    //         $id=$_GET['id'];
    //     }

    //     $admin = auth()->user();
    //     $limit_access=$admin->access_limits;
    //     $perPage = $request->get('per_page', 10);
    //     $count = Tblbill::count();
    //     $nextNumber = $count + 1;
    //     $serial = 'BILL-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    //     $locations = TblLocationModel::all();
    //     $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10);
    //     $Tbltcstax = Tbltcstax::all();
    //     $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);
    //     $Tblaccount = Tblaccount::orderBy('id', 'desc')->paginate(10);
    //     $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
    //     $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->get();
    //     $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
    //     $purchaselist = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling'])->where('approval_status', 1)->where('bill_status', 0)->orderBy('id','desc')->paginate(10);
    //     $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])->where('approval_status', 1)->where('po_status', 0)->orderBy('id','desc')->paginate(10);

    //     // If request is AJAX → return only table partial
    //     if ($request->ajax()) {
    //         return view('vendor.partials.purchase_table', compact('purchaselist', 'perPage'))->render();
    //     }
    //     if($id !==""){
    //         $bill = Tblbill::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('id',$id)->get();
    //         return view('vendor.bill_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'Tblgsttax' => $Tblgsttax,'Tblaccount' => $Tblaccount,'TblQuotation' => $TblQuotation,'TblZonesModel' => $TblZonesModel,'purchaselist' => $purchaselist,'bill' => $bill,'perPage' => $perPage]);
    //     }else{
    //         return view('vendor.bill_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'purchaselist' => $purchaselist,'TblQuotation' => $TblQuotation,'Tbltcstax' => $Tbltcstax,'Tblaccount' => $Tblaccount,'TblZonesModel' => $TblZonesModel,'serial' => $serial,'Tblgsttax' => $Tblgsttax,'perPage' => $perPage]);
    //     }
    //     // return view('vendor.bill_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax]);

    // }
    public function getbillcreate(Request $request)
    {
        $id = $request->get('id', '');
        $type = $request->get('type', '');
        $perPage = $request->get('per_page', 10);

        $admin = auth()->user();
        $limit_access = $admin->access_limits;
        $count = Tblbill::count();
        $nextNumber = $count + 1;
        $serial = 'BILL-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $locations = TblLocationModel::all();
        $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10);
        $Tbltcstax = Tbltcstax::all();
        $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);
         $gsttax = Tblgsttax::orderBy('id', 'desc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'desc')->get();
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->get();
        $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $purchaselist = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling'])
            ->where('approval_status', 1)
            ->where('bill_status', 0)
            ->orderBy('id','desc')
            ->paginate($perPage);

        $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])
            ->where('approval_status', 1)
            ->where('po_status', 0)
            ->orderBy('id','desc')
            ->paginate($perPage);

        // ✅ Handle AJAX requests separately
        if ($request->ajax()) {
            if ($request->get('type') === 'purchase') {
                return view('vendor.partials.purchase_table', compact('purchaselist', 'perPage'))->render();
            }
            if ($request->get('type') === 'quotation') {
                return view('vendor.partials.quotation_table', compact('TblQuotation', 'perPage'))->render();
            }
        }

        if ($id !== "") {
            $bill = Tblbill::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('id',$id)->get();
            if($type == 'edit'){
                return view('vendor.bill_create', compact(
                    'admin','locations','vendor','customer',
                    'Tbltdstax','Tbltcstax','Tblgsttax','Tblaccount','Tbltdssection',
                    'TblQuotation','TblZonesModel','purchaselist','bill','perPage','Tblcompany','gsttax','type'
                ));
            }else{
                return view('vendor.bill_create', compact(
                    'admin','locations','vendor','customer',
                    'Tbltdstax','Tbltcstax','Tblgsttax','Tblaccount','Tbltdssection',
                    'TblQuotation','TblZonesModel','serial','purchaselist','bill','perPage','Tblcompany','gsttax','type'
                ));

            }
        } else {
            return view('vendor.bill_create', compact(
                'admin','locations','vendor','customer',
                'Tbltdstax','Tbltcstax','Tblgsttax','Tblaccount','Tbltdssection',
                'TblQuotation','TblZonesModel','purchaselist','serial','perPage','Tblcompany','gsttax','type'
            ));
        }
    }

   public function gettdssave(Request $request)
    {
        // dd($request);
        $id = $request->id;
        $data = [
            "tax_name" => $request->name,
            "tax_rate" => $request->rate,
            "section_id" => $request->section_id,
            "section_name" => $request->section_name,
            "tax_start_date" => $request->start_date,
            "tax_end_date" => $request->end_date,
            "created_at" => now(),
        ];

        if ($id !== "" && $id !==null) {
            unset($data['created_at']);
            Tbltdstax::where('id', $id)->update($data);
        } else {
            Tbltdstax::create($data);
        }
        $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'tdstax' => $Tbltdstax,
            'message' => $id!==""? 'TDS Tax Data Updated successfully!':'TDS Tax Data saved successfully!'
        ]);
    }
    public function gettdssectionsave(Request $request)
    {
        // dd($request);
        $id = $request->id;
        $admin = auth()->user();
        $data=[
            "name"=>$request->name,
            "user_id"=>$admin->id,
            "created_by"=>$admin->user_fullname,
            "created_at"=>now(),
        ];
        // dd($data);
        if ($id !== "" && $id!==null) {
            unset($data['created_at']);
            $sts=Tbltdssection::where('id', $id)->update($data);
        } else {
            $sts=Tbltdssection::create($data);
        }
        $Tbltdssection = Tbltdssection::orderBy('id', 'desc')->paginate(10);
        return response()->json([
            'success' => true,
            'Tbltdssection' => $Tbltdssection,
            'message' => $id!==""? 'Section Data Updated successfully!':'Section Data saved successfully!'
        ]);

    }

    public function gettcssave(Request $request)
    {
        $data=[
            "tax_name"=>$request->name,
            "tax_rate"=>$request->rate,
            "tax_start_date"=>$request->start_date,
            "tax_end_date"=>$request->end_date,
            "created_at"=>now(),
        ];
        Tbltcstax::create($data);

         return response()->json([
        'success' => true,
        'message' => 'TCS Tax Data saved successfully!'
    ]);
    }
    public function getgstsave(Request $request)
    {
        $id = $request->id;
        $data=[
            "tax_name"=>$request->name,
            "tax_rate"=>$request->rate,
            "tax_type"=>$request->tax_type,
            "tax_start_date"=>$request->start_date,
            "tax_end_date"=>$request->end_date,
            "created_at"=>now(),
        ];
        // dd($data);
        if ($id !== "" && $id !==null) {
            unset($data['created_at']);
            Tblgsttax::where('id', $id)->update($data);
        } else {
            Tblgsttax::create($data);
        }
        $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);
        return response()->json([
            'success' => true,
            'gsttax' => $Tblgsttax,
            'message' => $id!==""? 'TDS Tax Data Updated successfully!':'TDS Tax Data saved successfully!'
        ]);

    }
    public function savebill(Request $request)
    {
        // dd($request);
        $isUpdate = $request->filled('id'); // Check if ID exists
        $now = now();
        $user_id = auth()->user()->id;
        $admin = auth()->user();
        $data = [
            'user_id' => $user_id,
            'vendor_id' => $request->vendor_id,
            'quotation_id' => $request->quotation_id,
            'purchase_id' => $request->purchase_id,
            'vendor_name' => $request->vendor_name,
            'bill_number' => $request->bill_number,
            'bill_gen_number' => $request->bill_gen_number,
            'order_number' => $request->order_number,
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            'zone_id' => $request->zone_id,
            'zone_name' => $request->zone,
            'branch_name' => $request->branch,
            'branch_id' => $request->branch_id,
            'company_name' => $request->company_name,
            'company_id' => $request->company_id,
            'payment_terms' => $request->payment_terms,
            'subject' => $request->subject,
            'discount_percent' => $request->discount_percent,
            'discount_type' => $request->discount_type,
            'discount_amount' => $this->cleanCurrency($request->discount_amount),
            'adjustment_value' => $request->adjustment_value,
            'tds_tax_id' => $request->tds_tax_id,
            'tcs_tax_id' => $request->tcs_tax_id,
            'tax_type' => $request->tax_type,
            'tax_name' =>  $request->tds_tax_name ?? $request->tcs_tax_name,
            'tax_rate' =>  $request->tds_tax_selected ?? $request->tcs_tax_selected ,
            'tax_amount' => $this->cleanCurrency($request->tax_amount),
            'sub_total_amount' => $this->cleanCurrency($request->sub_total_amount),
            'adjustment_amount' => $this->cleanCurrency($request->adjustment_amount),
            'grand_total_amount' => $this->cleanCurrency($request->grand_total_amount),
            'balance_amount' => $this->cleanCurrency($request->grand_total_amount),
            'status' => $request->save_status,
            'note' => $request->note,
            'created_at' => $now,
        ];
        // dd($data);
        if (!$isUpdate) {
            $data['updated_at'] = $now;
        }
        if($request->quotation_id !==""){
            TblQuotation::where('id', $request->quotation_id)->update([
                    'bill_status' => 1
                ]);
        }
        if($request->purchase_id !==""){
            TblPurchaseorder::where('id', $request->purchase_id)->update([
                    'bill_status' => 1
                ]);
        }

       $fileNames = [];

        // 1. Handle new file uploads
        if ($request->hasFile('uploads')) {
            $uploadPath = public_path('uploads/vendor/bill');

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            foreach ($request->file('uploads') as $file) {
                $originalName = $file->getClientOriginalName();
                $uniqueFileName = time() . '_' . $originalName;
                $file->move($uploadPath, $uniqueFileName);
                $fileNames[] = $uniqueFileName;
            }
        }

        // 2. Get existing files from hidden input (if any)
        $existingFiles = $request->existing_files ?? []; // from hidden input
        if (!is_array($existingFiles)) {
            $existingFiles = json_decode($existingFiles, true) ?? [];
        }

        // 3. Merge both arrays
        $mergedFiles = array_merge($existingFiles, $fileNames);

        // 4. Save merged list to DB
        $data['documents'] = json_encode($mergedFiles);
          if ($isUpdate) {
            $bill = Tblbill::findOrFail($request->id);
            $bill->update($data);
            if(!empty($bill)){
                $vendor = Tblvendor::where('id', $bill->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'Bill Updated',
                    'description' => "Bill {$bill->bill_number} of amount ₹{$bill->grand_total_amount} updated by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
            $bill_id = $bill->id;


            // Update or insert contact persons
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    $linesDatas = [
                        'bill_id' => $bill_id,
                        'item_details' => $linesData['item_details'] ?? null,
                        'account' => $linesData['account_name'] ?? null,
                        'account_id' => $linesData['account_id'] ?? null,
                        'quantity' => $linesData['quantity'] ?? null,
                        'rate' => $linesData['rate'] ?? null,
                        'customer' => $linesData['customer'] ?? null,
                        'gst_name' => $linesData['gst_name'] ?? null,
                        'gst_rate' => $linesData['gst_tax_selected'] ?? null,
                        'gst_type' => $linesData['gst_tax_type'] ?? null,
                        'cgst_amount' => $linesData['cgst_amount'] ?? null,
                        'sgst_amount' => $linesData['sgst_amount'] ?? null,
                        'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
                        'gst_amount' => $linesData['gst_amount'] ?? null,
                        'amount' => $linesData['amount'] ?? null,
                        'updated_at' => $now,
                    ];

                    if (!empty($linesData['id'])) {
                        TblBillLines::where('id', $linesData['id'])
                            ->where('bill_id', $bill_id)
                            ->update($linesDatas);
                    } else {
                        $contactValues['created_at'] = $now;
                        TblBillLines::create($linesDatas);
                    }
                }
            }


        } else {
            // Create new customer
            $bill = Tblbill::create($data);
            if(!empty($bill)){
                $vendor = Tblvendor::where('id', $bill->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'Bill Created',
                    'description' => "Bill {$bill->bill_number} of amount ₹{$bill->grand_total_amount} created by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
                $bill_id = $bill->id;
                // dd($vendor_id);
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    TblBillLines::create([
                        'bill_id' => $bill_id,
                        'item_details' => $linesData['item_details'] ?? null,
                        'account' => $linesData['account_name'] ?? null,
                        'account_id' => $linesData['account_id'] ?? null,
                        'quantity' => $linesData['quantity'] ?? null,
                        'rate' => $linesData['rate'] ?? null,
                        'customer' => $linesData['customer'] ?? null,
                        'gst_name' => $linesData['gst_name'] ?? null,
                        'gst_rate' => $linesData['gst_tax_selected'] ?? null,
                        'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
                        'gst_type' => $linesData['gst_tax_type'] ?? null,
                        'cgst_amount' => $linesData['cgst_amount'] ?? null,
                        'sgst_amount' => $linesData['sgst_amount'] ?? null,
                        'gst_amount' => $linesData['gst_amount'] ?? null,
                        'amount' => $linesData['amount'] ?? null,
                        'created_at' => $now,
                    ]);
                }
            }

        }
         return response()->json([
            'success' => true,
            'message' => $isUpdate ? 'Bill Data updated successfully!' : 'Bill Data saved successfully!'
        ]);
    }

// public function getbillprint(Request $request)
// {
//     $billId = $request->id;

//     $bill = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])->findOrFail($billId);

//     // dd($bill);
//     $pdf = new TCPDF();
//     $pdf->SetMargins(10, 10, 10);
//     $pdf->AddPage();

//     $html = view('vendor.dynamicprint', compact('bill','pdf'))->render();
//     $pdf->writeHTML($html, true, false, true, false, '');

//     // Return the PDF as a binary stream (for AJAX)
//     return response($pdf->Output('bill_'.$bill->id.'.pdf', 'S'))
//         ->header('Content-Type', 'application/pdf');
// }
public function getbillprint(Request $request)
{
    $billId = $request->id;

    $bill = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails','TblCompany'])->findOrFail($billId);

    $pdf = new TCPDF();
    $pdf->setPrintHeader(false); // disable default header line
    $pdf->setPrintFooter(false); // optional
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Execute raw TCPDF drawing code
    include(resource_path('views/vendor/dynamicprint.blade.php'));

    return response($pdf->Output('bill_'.$bill->id.'.pdf', 'S'))
        ->header('Content-Type', 'application/pdf');
}

public function getbillpdf(Request $request)
{
    $billId = $request->id;
    $bill = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])->findOrFail($billId);

    $pdf = new TCPDF();
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Execute raw TCPDF drawing code
    include(resource_path('views/vendor/dynamicprint.blade.php'));

    // Force download the PDF
    return response($pdf->Output('bill_'.$bill->bill_number.'.pdf', 'D'))
        ->header('Content-Type', 'application/pdf');
}


public function billtemplate()
    {
        return Excel::download(new BillTemplateExport, 'bill_template.xlsx');
    }
public function importbillExcel(Request $request)
{
     $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    Excel::import(new billImport, $request->file('file'));

   return response()->json([
        'status' => 'success',
        'message' => 'bill data imported successfully!'
    ]);
}

// bill made
 public function getbillmade(Request $request)
    {
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $count = Tblneft::count(); // Get total number of rows
        $nextNumber = $count + 1;  // Next serial number
        $serial = 'NEFT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::orderBy('id', 'asc')->get();

        $query = Tblbillpay::with(['BillLines','BillLines.Bill','BillLines.BillLines','Tblvendor','TblBilling','Tblbankdetails'])->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
         if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $billpaylist = $query->paginate($perPage)->appends($request->all());

        if ($request->ajax()) {
            // Render ONLY the tbody rows from a partial view
            return view('vendor.partials.table.bill_made_rows', compact('billpaylist','perPage'))->render();
        }

        // $billpaylist = Tblbillpay::with(['BillLines','BillLines.Bill','BillLines.BillLines','Tblvendor','TblBilling','Tblbankdetails'])->paginate($perPage)->appends(['per_page' => $perPage]);
        // dd($billpaylist);
        return view('vendor.bill_made_dashboard', ['admin' => $admin,'locations' => $locations,'billpaylist' => $billpaylist,'serial' => $serial,'perPage' => $perPage,'TblZonesModel' => $TblZonesModel, 'Tblvendor' => $Tblvendor,'Tblcompany' => $Tblcompany]);
    }
    public function getbillmadecreate()
    {
        $id="";
        if(isset($_GET['id'])){
            $id=$_GET['id'];
        }
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $Tbltdstax = Tbltdstax::all();
        $Tbltcstax = Tbltcstax::all();
        $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts','bankdetails'])->get();
        $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
         if($id !==""){
            $billpay = Tblbillpay::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('id',$id)->get();
            return view('vendor.bill_made_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'TblZonesModel' => $TblZonesModel,'billpay' => $billpay,'Tblcompany' => $Tblcompany]);
        }else{
            return view('vendor.bill_made_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'TblZonesModel' => $TblZonesModel,'Tblcompany' => $Tblcompany]);
        }

    }
    public function getDetails(Request $request)
    {
        $vendor = Tblbill::with(['Tblvendor', 'BillLines'])
                    ->where('vendor_id', $request->vendor_id)->where('balance_amount', '>', 0)
                    ->get()
                    ->toArray();
        // dd($vendor);

        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }

        return response()->json([
            'vendor' => $vendor,
        ]);
    }
    public function savebillmade(Request $request)
    {
        // dd($request);
        $isUpdate = $request->filled('id'); // Check if ID exists
        $now = now();
        $user_id = auth()->user()->id;
        $admin = auth()->user();
        $data = [
            'user_id' => $user_id,
            'vendor_id' => $request->vendor_id,
            'vendor_name' => $request->vendor_name,
            'zone_id' => $request->zone_id,
            'zone_name' => $request->zone,
            'branch_name' => $request->branch,
            'branch_id' => $request->branch_id,
            'company_name' => $request->company_name,
            'company_id' => $request->company_id,
            'payment' => $request->payment,
            'payment_made' => $request->payment_made,
            'payment_date' => $request->payment_date,
            'payment_mode' => $request->payment_mode,
            'paid_through' => $request->paid_through,
            'reference' => $request->reference,
            'remark' => $request->remark,
            'save_status' => $request->save_status,
            'amount_paid' => $this->cleanCurrency($request->amount_paid),
            'amount_used' => $this->cleanCurrency($request->amount_used),
            'amount_refunded' => $this->cleanCurrency($request->amount_refunded),
            'amount_excess' => $this->cleanCurrency($request->amount_excess),
            'note' => $request->note,
            'created_at' => $now,
        ];
        if (!$isUpdate) {
            $data['updated_at'] = $now;
        }
        // dd($data);
       $fileNames = [];

        // 1. Handle new file uploads
        if ($request->hasFile('uploads')) {
            $uploadPath = public_path('uploads/vendor/bill');

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            foreach ($request->file('uploads') as $file) {
                $originalName = $file->getClientOriginalName();
                $uniqueFileName = time() . '_' . $originalName;
                $file->move($uploadPath, $uniqueFileName);
                $fileNames[] = $uniqueFileName;
            }
        }

        // 2. Get existing files from hidden input (if any)
        $existingFiles = $request->existing_files ?? []; // from hidden input
        if (!is_array($existingFiles)) {
            $existingFiles = json_decode($existingFiles, true) ?? [];
        }

        // 3. Merge both arrays
        $mergedFiles = array_merge($existingFiles, $fileNames);

        // 4. Save merged list to DB
        $data['documents'] = json_encode($mergedFiles);
        // dd($isUpdate);
          if ($isUpdate) {
            $bill = Tblbillpay::findOrFail($request->id);
            $bill->update($data);
            if(!empty($bill)){
                $vendor = Tblvendor::where('id', $bill->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'Payments Made Updated',
                    'description' => "Payment of amount ₹{$bill->grand_total_amount} made and applied for {$bill->bill_number} updare by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
            $bill_pay_id = $bill->id;


            // Update or insert contact persons
            if ($request->has('vendors')) {
                foreach ($request->vendors as $linesData) {
                    $bill = Tblbill::find($linesData['bill_id']);
                    $balance = $bill->grand_total_amount - $linesData['amount'];
                    $partial =  $linesData['amount'];

                    Tblbill::where('id', $linesData['id'])->update([
                        'partially_payment' => $partial,
                        'balance_amount' => $balance
                    ]);
                    $linesDatas = [
                       'bill_pay_id' => $bill_pay_id,
                        'bill_id' => $linesData['bill_id'] ?? null,
                        'bill_date' => $linesData['bill_date'] ?? null,
                        'due_date' => $linesData['due_date'] ?? null,
                        'bill_number' => $linesData['bill_number'] ?? null,
                        'grand_total_amount' => $linesData['grand_total_amount'] ?? null,
                        'balance_amount' => $linesData['balance_amount'] ?? null,
                        'payment_date' => $linesData['payment_date'] ?? null,
                        'amount' => $linesData['amount'] ?? null,
                        'created_at' => $now,
                    ];

                    if (!empty($linesData['id'])) {
                        TblBillPayLines::where('id', $linesData['id'])
                            ->where('bill_pay_id', $bill_pay_id)
                            ->update($linesDatas);
                    } else {
                        $contactValues['created_at'] = $now;
                        TblBillPayLines::create($linesDatas);
                    }
                }
            }


        } else {
            // Create new customer
            $bill = Tblbillpay::create($data);
            if(!empty($bill)){
                $vendor = Tblvendor::where('id', $bill->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'Payments Made added',
                    'description' => "Payment of amount ₹{$bill->grand_total_amount} made and applied for {$bill->bill_number} by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
                $bill_pay_id = $bill->id;
            if ($request->has('vendors')) {
                foreach ($request->vendors as $linesData) {
                    if($linesData['amount'] !=="0" && $linesData['amount'] !==null){
                        $bill = Tblbill::find($linesData['id']);
                            $balance = $bill->balance_amount - $linesData['amount'];
                            $partial = $bill->partially_payment + $linesData['amount'];
                            if($balance==0){
                                Tblbill::where('id', $linesData['id'])->update([
                                    'partially_payment' => $partial,
                                    'balance_amount' => $balance,
                                    'bill_made_status' => 1
                                ]);
                            }else{
                                Tblbill::where('id', $linesData['id'])->update([
                                    'partially_payment' => $partial,
                                    'balance_amount' => $balance
                                ]);
                            }
                        TblBillPayLines::create([
                            'bill_pay_id' => $bill_pay_id,
                            'bill_id' => $linesData['id'] ?? null,
                            'bill_date' => $linesData['bill_date'] ?? null,
                            'due_date' => $linesData['due_date'] ?? null,
                            'bill_number' => $linesData['bill_number'] ?? null,
                            'grand_total_amount' => $linesData['grand_total_amount'] ?? null,
                            'balance_amount' => $linesData['balance_amount'] ?? null,
                            'payment_date' => $linesData['payment_date'] ?? null,
                            'amount' => $linesData['amount'] ?? null,
                            'created_at' => $now,
                        ]);
                    }

                }
            }

        }
         return response()->json([
            'success' => true,
            'message' => $isUpdate ? 'Bill Pay Data updated successfully!' : 'Bill Pay Data saved successfully!'
        ]);
    }
public function getbillmadeprint(Request $request)
{
    $billmadeId = $request->id;
    $billmade = Tblbillpay::with(['BillLines','Tblvendor','TblBilling','TblCompany'])->findOrFail($billmadeId);

    $pdf = new TCPDF();
    $pdf->setPrintHeader(false); // disable default header line
    $pdf->setPrintFooter(false); // optional
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Execute raw TCPDF drawing code
    include(resource_path('views/vendor/billmadeprint.blade.php'));

    // Force download the PDF
    return response($pdf->Output('bill_'.$billmade->bill_number.'.pdf', 'D'))
        ->header('Content-Type', 'application/pdf');
}
public function getbillmadepdf(Request $request)
{
    $billmadeId = $request->id;
    $billmade = Tblbillpay::with(['BillLines','Tblvendor','TblBilling'])->findOrFail($billmadeId);

    $pdf = new TCPDF();
    $pdf->setPrintHeader(false); // disable default header line
    $pdf->setPrintFooter(false); // optional
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Execute raw TCPDF drawing code
    include(resource_path('views/vendor/billmadeprint.blade.php'));

    // Force download the PDF
    return response($pdf->Output('bill_'.$billmade->bill_number.'.pdf', 'D'))
        ->header('Content-Type', 'application/pdf');
}
// purchase order
public function getpurchaseorder(Request $request)
    {
        // dd($request);
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $TblPoEmail = TblPoEmail::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::orderBy('id', 'asc')->get();


        $query = TblPurchaseorder::with(['BillLines', 'Tblvendor', 'TblBilling'])->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
         if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        $purchaselist = $query->paginate($perPage)->appends($request->all());

        if ($request->ajax()) {
            // Render ONLY the tbody rows from a partial view
            return view('vendor.partials.table.purchase_rows', compact('purchaselist','perPage'))->render();
        }

        // $purchaselist = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling'])->orderBy('id', 'desc')->paginate($perPage)->appends(['per_page' => $perPage]);
        return view('vendor.purchase_bashboard', ['admin' => $admin,'locations' => $locations,'purchaselist' => $purchaselist,'perPage' => $perPage,'TblZonesModel' => $TblZonesModel,'TblPoEmail' => $TblPoEmail,'Tblcompany' => $Tblcompany, 'Tblvendor' => $Tblvendor]);
    }
    //  public function getpurchasecreate(Request $request)
    // {
    //     $id="";
    //     if(isset($_GET['id'])){
    //         $id=$_GET['id'];
    //     }
    //     $perPage = $request->get('per_page', 10);
    //     $admin = auth()->user();
    //     $limit_access=$admin->access_limits;
    //     $count = TblPurchaseorder::count(); // Get total number of rows
    //     $nextNumber = $count + 1;  // Next serial number
    //     $purchase_id = 'PO-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    //     $locations = TblLocationModel::all();
    //     $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10);
    //     $Tbltcstax = Tbltcstax::all();
    //     $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);
    //     $Tblaccount = Tblaccount::orderBy('id', 'desc')->paginate(10);
    //      $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
    //     $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->get();
    //     $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
    //     // $purchaselist = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling'])->where('approval_status', 1)->where('bill_status', 0)->orderBy('id','desc')->paginate(10);
    //     $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])->where('approval_status', 1)->where('po_status', 0)->orderBy('id','desc')->paginate(10);
    //     $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts','bankdetails'])->get();
    //     $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
    //     // $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])->where('approval_status',1)->get();
    //      if($id !==""){
    //         $purchase_id="";
    //         $purchase = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('id',$id)->get();
    //         return view('vendor.purchase_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'Tblgsttax' => $Tblgsttax,'Tblaccount' => $Tblaccount,'purchase_id' => $purchase_id,'TblZonesModel' => $TblZonesModel,'TblQuotation' => $TblQuotation,'purchase' => $purchase,'perPage' => $perPage]);
    //     }else{
    //         return view('vendor.purchase_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'Tblgsttax' => $Tblgsttax,'Tblaccount' => $Tblaccount,'purchase_id' => $purchase_id,'TblZonesModel' => $TblZonesModel,'TblQuotation' => $TblQuotation,'perPage' => $perPage]);
    //     }

    // }
    public function getpurchasecreate(Request $request)
    {
        $id = $request->get('id', '');
        $type = $request->get('type', '');
        $perPage = $request->get('per_page', 10);

        $admin = auth()->user();
        $limit_access = $admin->access_limits;
        $count = TblPurchaseorder::count();
        $nextNumber = $count + 1;
        $purchase_id = 'PO-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $locations = TblLocationModel::all();
        $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10);
        $Tbltcstax = Tbltcstax::all();
        $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);
        $gsttax = Tblgsttax::orderBy('id', 'desc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'desc')->get();
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->get();
        $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
       $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])->where('approval_status', 1)->where('po_status', 0)->orderBy('id','desc')->paginate(10);

        // If request is AJAX → return only table partial
        if ($request->ajax()) {
            return view('vendor.partials.quotation_table', compact('TblQuotation', 'perPage'))->render();
        }

        if($id !==""){
            if($type =='edit'){
                $purchase_id="";
                $purchase = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('id',$id)->get();
                return view('vendor.purchase_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'Tblgsttax' => $Tblgsttax,'Tblaccount' => $Tblaccount,'purchase_id' => $purchase_id,'TblZonesModel' => $TblZonesModel,'TblQuotation' => $TblQuotation,'purchase' => $purchase,'perPage' => $perPage,'Tbltdssection' => $Tbltdssection,'Tblcompany' => $Tblcompany,'gsttax' => $gsttax,'type' => $type]);
            }else{
                $purchase = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('id',$id)->get();
                return view('vendor.purchase_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'Tblgsttax' => $Tblgsttax,'Tblaccount' => $Tblaccount,'purchase_id' => $purchase_id,'TblZonesModel' => $TblZonesModel,'TblQuotation' => $TblQuotation,'purchase' => $purchase,'perPage' => $perPage,'Tbltdssection' => $Tbltdssection,'Tblcompany' => $Tblcompany,'gsttax' => $gsttax,'type' => $type]);
            }
        }else{
            return view('vendor.purchase_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'Tblgsttax' => $Tblgsttax,'Tblaccount' => $Tblaccount,'purchase_id' => $purchase_id,'TblZonesModel' => $TblZonesModel,'TblQuotation' => $TblQuotation,'perPage' => $perPage,'Tbltdssection' => $Tbltdssection,'Tblcompany' => $Tblcompany,'gsttax' => $gsttax,'type' => $type]);
        }
    }

    function cleanCurrency($value) {
        // Remove ₹, commas, and optional decimal part
        $clean = str_replace(['₹', ',', '-'], '', $value);
        return (int) floatval($clean);
    }
     public function savepurchaseorder(Request $request)
    {
        // dd($request);
        $isUpdate = $request->filled('id'); // Check if ID exists
        $now = now();
        $user_id = auth()->user()->id;
        $admin = auth()->user();
        $data = [
            'user_id' => $user_id,
            'vendor_id' => $request->vendor_id,
            'quotation_id' => $request->quotation_id,
            'vendor_name' => $request->vendor_name,
            'zone_id' => $request->zone_id,
            'zone_name' => $request->zone,
            'branch_name' => $request->branch,
            'branch_id' => $request->branch_id,
            'company_name' => $request->company_name,
            'company_id' => $request->company_id,
            'purchase_order_number' => $request->purchase_order,
            'purchase_gen_order' => $request->purchase_gen_order,
            'delivery_address' => $request->delivery_address,
            'order_number' => $request->order_number,
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            'payment_terms' => $request->payment_terms,
            'subject' => $request->subject,
            'discount_percent' => $request->discount_percent,
            'discount_type' => $request->discount_type,
            'discount_amount' => $this->cleanCurrency($request->discount_amount),
            'adjustment_value' => $request->adjustment_value,
            'tds_tax_id' => $request->tds_tax_id,
            'tcs_tax_id' => $request->tcs_tax_id,
            'tax_type' => $request->tax_type,
            'tax_name' => $request->tcs_tax_name ?: $request->tds_tax_name,
            'tax_rate' => $request->tcs_tax_selected ?: $request->tds_tax_selected,
            'tax_amount' => $this->cleanCurrency($request->tax_amount),
            'sub_total_amount' => $this->cleanCurrency($request->sub_total_amount),
            'adjustment_amount' => $this->cleanCurrency($request->adjustment_amount),
            'grand_total_amount' => $this->cleanCurrency($request->grand_total_amount),
            'balance_amount' => $this->cleanCurrency($request->grand_total_amount),
            'save_status' => $request->save_status,
            'note' => $request->note,
            'created_at' => $now,
        ];
        if (!$isUpdate) {
            $data['updated_at'] = $now;
        }
        if($request->quotation_id !==""){
                TblQuotation::where('id', $request->quotation_id)->update([
                        'po_status' => 1
                    ]);
        }

       $fileNames = [];

        // 1. Handle new file uploads
        if ($request->hasFile('uploads')) {
            $uploadPath = public_path('uploads/vendor/bill');

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            foreach ($request->file('uploads') as $file) {
                $originalName = $file->getClientOriginalName();
                $uniqueFileName = time() . '_' . $originalName;
                $file->move($uploadPath, $uniqueFileName);
                $fileNames[] = $uniqueFileName;
            }
        }

        // 2. Get existing files from hidden input (if any)
        $existingFiles = $request->existing_files ?? []; // from hidden input
        if (!is_array($existingFiles)) {
            $existingFiles = json_decode($existingFiles, true) ?? [];
        }

        // 3. Merge both arrays
        $mergedFiles = array_merge($existingFiles, $fileNames);

        // 4. Save merged list to DB
        $data['documents'] = json_encode($mergedFiles);
          if ($isUpdate) {
            $purchaseorder = TblPurchaseorder::findOrFail($request->id);
            $purchaseorder->update($data);
            if(!empty($purchaseorder)){
                $vendor = Tblvendor::where('id', $purchaseorder->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'PO Updated',
                    'description' => "PO {$purchaseorder->purchase_order_number} of amount ₹{$purchaseorder->grand_total_amount} updated by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
            $purchaseorder_id = $purchaseorder->id;

            // Update or insert contact persons
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    $linesDatas = [
                        'purchase_order_id' => $purchaseorder_id,
                        'item_details' => $linesData['item_details'] ?? null,
                        'account' => $linesData['account_name'] ?? null,
                        'account_id' => $linesData['account_id'] ?? null,
                        'quantity' => $linesData['quantity'] ?? null,
                        'rate' => $linesData['rate'] ?? null,
                        'customer' => $linesData['customer'] ?? null,
                        'gst_name' => $linesData['gst_name'] ?? null,
                        'gst_rate' => $linesData['gst_tax_selected'] ?? null,
                        'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
                        'gst_type' => $linesData['gst_tax_type'] ?? null,
                        'cgst_amount' => $linesData['cgst_amount'] ?? null,
                        'sgst_amount' => $linesData['sgst_amount'] ?? null,
                        'gst_amount' => $linesData['gst_amount'] ?? null,
                        'amount' => $linesData['amount'] ?? null,
                        'updated_at' => $now,
                    ];

                    if (!empty($linesData['id']) && $linesData['id']!==null) {
                        TblPurchaseorderLines::where('id', $linesData['id'])
                            ->where('purchase_order_id', $purchaseorder_id)
                            ->update($linesDatas);
                    } else {
                        $contactValues['created_at'] = $now;
                        TblPurchaseorderLines::create($linesDatas);
                    }
                }
            }


        } else {
            // Create new customer
            $purchaseorder = TblPurchaseorder::create($data);
            if(!empty($purchaseorder)){
                $vendor = Tblvendor::where('id', $purchaseorder->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'PO Created',
                    'description' => "PO {$purchaseorder->purchase_order_number} of amount ₹{$purchaseorder->grand_total_amount} created by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
            $purchaseorder_id = $purchaseorder->id;
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    TblPurchaseorderLines::create([
                        'purchase_order_id' => $purchaseorder_id,
                        'item_details' => $linesData['item_details'] ?? null,
                        'account' => $linesData['account_name'] ?? null,
                        'account_id' => $linesData['account_id'] ?? null,
                        'quantity' => $linesData['quantity'] ?? null,
                        'rate' => $linesData['rate'] ?? null,
                        'customer' => $linesData['customer'] ?? null,
                        'gst_name' => $linesData['gst_name'] ?? null,
                        'gst_rate' => $linesData['gst_tax_selected'] ?? null,
                        'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
                        'gst_type' => $linesData['gst_tax_type'] ?? null,
                        'cgst_amount' => $linesData['cgst_amount'] ?? null,
                        'sgst_amount' => $linesData['sgst_amount'] ?? null,
                        'gst_amount' => $linesData['gst_amount'] ?? null,
                        'amount' => $linesData['amount'] ?? null,
                        'created_at' => $now,
                    ]);
                }
            }

        }
         return response()->json([
            'success' => true,
            'message' => $isUpdate ? 'Purchase Order Data updated successfully!' : 'Purchase Order Data saved successfully!'
        ]);
    }
    //   public function PurchaseApprover(Request $request)
    // {
    //     $approver_id=$request->approver_id;
    //     $status=$request->value;
    //      if($approver_id !==""){
    //         if($status ==="Approve"){
    //             TblPurchaseorder::where('id', $approver_id)->update([
    //                 'approval_status' => 1
    //             ]);
    //             $TblPoEmail = TblPoEmail::orderBy('id', 'asc');
    //             return response()->json(['success' => true, 'message' => 'Approval data updated successfully!']);
    //         }else{
    //             TblPurchaseorder::where('id', $approver_id)->update([
    //                 'reject_status' => 1
    //             ]);
    //             return response()->json(['success' => true, 'message' => 'Rejected data updated successfully!']);
    //         }
    //     }
    // }
    public function PurchaseApprover(Request $request)
    {
        $approver_id = $request->approver_id;
        $status = $request->value;

        if ($approver_id !== "") {
            $purchase = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])
                ->find($approver_id);

            if (!$purchase) {
                return response()->json(['success' => false, 'message' => 'Purchase order not found']);
            }

            $emails = TblPoEmail::pluck('email')->toArray();
            if (!$emails) {
                return response()->json(['success' => false, 'message' => 'No recipient emails found']);
            }

            if ($status === "Approve") {
                TblPurchaseorder::where('id', $approver_id)->update([
                    'approval_status' => 1
                ]);
                Mail::send('vendor.emails.purchase_order', [
                    'purchase' => $purchase,
                    'status'   => 'Approved'
                ], function ($message) use ($emails, $approver_id) {
                    $message->to($emails)->subject("Purchase Order #{$approver_id} Approved");
                });

                return response()->json(['success' => true, 'message' => 'Approval data updated & email sent successfully!']);
            } else {
                TblPurchaseorder::where('id', $approver_id)->update([
                    'reject_status' => 1
                ]);
                Mail::send('vendor.emails.purchase_order', [
                    'purchase' => $purchase,
                    'status'   => 'Rejected'
                ], function ($message) use ($emails, $approver_id) {
                    $message->to($emails)->subject("Purchase Order #{$approver_id} Rejected");
                });

                return response()->json(['success' => true, 'message' => 'Rejected data updated & email sent successfully!']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Invalid approver id']);
    }
    public function getpurchasefetch(Request $request)
    {
        $ids = json_decode($request->input('selected_ids'), true);
        $purchase = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])->whereIn('id',$ids)->get();
         return response()->json([
                    'message' => 'Purchase orders fetched successfully.',
                    'purchase' => $purchase
                ]);

    }

    public function getpurchaseprint(Request $request)
{
    $purchaseId = $request->id;

    $purchase = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling','TblCompany'])->findOrFail($purchaseId);
    // dd($purchase);
    $pdf = new TCPDF();
    $pdf->setPrintHeader(false); // disable default header line
    $pdf->setPrintFooter(false); // optional
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Execute raw TCPDF drawing code
    include(resource_path('views/vendor/purchaseprint.blade.php'));

    return response($pdf->Output('Purchase_'.$purchase->id.'.pdf', 'S'))
        ->header('Content-Type', 'application/pdf');
}
public function getpurchasepdf(Request $request)
{
    $purchaseId = $request->id;
    $purchase = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling'])->findOrFail($purchaseId);

    $pdf = new TCPDF();
    $pdf->setPrintHeader(false); // disable default header line
    $pdf->setPrintFooter(false); // optional
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Execute raw TCPDF drawing code
    include(resource_path('views/vendor/purchaseprint.blade.php'));

    // Force download the PDF
    return response($pdf->Output('Purchase_'.$purchase->purchase_order_number.'.pdf', 'D'))
        ->header('Content-Type', 'application/pdf');
}

public function purchasetemplate()
    {
        return Excel::download(new PurchaseTemplateExport, 'purchase_template.xlsx');
    }
public function importpurchaseExcel(Request $request)
{
     $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    Excel::import(new PurchaseImport, $request->file('file'));

   return response()->json([
        'status' => 'success',
        'message' => 'Purchase data imported successfully!'
    ]);
}

// neft create
public function getneftdashboard(Request $request)
    {
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::orderBy('id', 'asc')->get();
        // $query = Tblneft::with(['Tblvendor','BillLines','Tblbankdetails','Tblbillpay','BillLines.Tblbilllines'])->orderBy('id', 'desc');
      $query = Tblneft::with([
                'Tblvendor',
                'Tblbankdetails',
                'Tblbillpay',
                'BillLines.Tblbilllines',
                'BillLines.alreadypaid',
                'BillLines.Bill:id,bill_gen_number,purchase_id,quotation_id',
                'BillLines.Bill.Purchase:id,purchase_gen_order,quotation_id',
                'BillLines.Bill.Purchase.quotation:id,quotation_gen_no'
            ])
            ->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Run query AFTER filters
        $purchaselist = $query->paginate($perPage)->appends($request->all());

        // If AJAX request, return only table rows
        if ($request->ajax()) {
            return view('vendor.partials.table.neft_rows', compact('purchaselist','perPage','limit_access'))->render();
        }

        // Normal page load
        return view('vendor.neft_bashboard', [
            'admin' => $admin,
            'TblZonesModel' => $TblZonesModel,
            'locations' => $locations,
            'Tblcompany' => $Tblcompany,
            'Tblvendor' => $Tblvendor,
            'purchaselist' => $purchaselist,
            'perPage' => $perPage,
            'limit_access' => $limit_access
        ]);

        // return view('vendor.neft_bashboard', ['admin' => $admin,'locations' => $locations,'purchaselist' => $purchaselist,'limit_access' => $limit_access,'perPage' => $perPage]);
    }

     public function getneftcreate()
    {
        $id="";
        if(isset($_GET['id'])){
            $id=$_GET['id'];
        }
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $Tbltdstax = Tbltdstax::all();
        $Tbltcstax = Tbltcstax::all();
        $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts','bankdetails'])->get();
        $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
         if($id !==""){
            $purchase = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('id',$id)->get();
            return view('vendor.neft_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'purchase' => $purchase]);
        }else{
            return view('vendor.neft_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax]);
        }
        // return view('vendor.bill_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax]);

    }
    public function saveneft(Request $request)
{
    // dd($request);
    $isUpdate = $request->filled('id'); // Check if ID exists
    $now = now();
    $user_id = auth()->user()->id;
    $admin = auth()->user();
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

    // File uploads - handle new uploads
    // $panPaths = $request->hasFile('pan_upload') ? handleUploads($request->file('pan_upload'), 'neft') : $request->existing_pan_file;
    // $invoicePaths = $request->hasFile('invoice_upload') ? handleUploads($request->file('invoice_upload'), 'neft') : $request->existing_invoice_upload;
    // $bankPaths = $request->hasFile('bank_upload') ? handleUploads($request->file('bank_upload'), 'neft') : $request->existing_bank_upload;
    // $poPaths = $request->hasFile('po_upload') ? handleUploads($request->file('po_upload'), 'neft') : $request->existing_po_upload;
    // $poSignedPaths = $request->hasFile('po_signed_upload') ? handleUploads($request->file('po_signed_upload'), 'neft') : $request->existing_po_signed_upload;
    // $poDeliveryPaths = $request->hasFile('po_delivery_upload') ? handleUploads($request->file('po_delivery_upload'), 'neft') : $request->existing_po_delivery_upload;

    $panPaths = json_encode(array_merge(
        $request->hasFile('pan_upload') ? json_decode(handleUploads($request->file('pan_upload'), 'neft'), true) : [],
        is_array($request->existing_pan_file) ? $request->existing_pan_file : json_decode($request->existing_pan_file, true) ?? []
    ));

    $invoicePaths = json_encode(array_merge(
        $request->hasFile('invoice_upload') ? json_decode(handleUploads($request->file('invoice_upload'), 'neft'), true) : [],
        is_array($request->existing_invoice_upload) ? $request->existing_invoice_upload : json_decode($request->existing_invoice_upload, true) ?? []
    ));

    // Repeat for other file types...
    $bankPaths = json_encode(array_merge(
        $request->hasFile('bank_upload') ? json_decode(handleUploads($request->file('bank_upload'), 'neft'), true) : [],
        is_array($request->existing_bank_upload) ? $request->existing_bank_upload : json_decode($request->existing_bank_upload, true) ?? []
    ));

    $poPaths = json_encode(array_merge(
        $request->hasFile('po_upload') ? json_decode(handleUploads($request->file('po_upload'), 'neft'), true) : [],
        is_array($request->existing_po_upload) ? $request->existing_po_upload : json_decode($request->existing_po_upload, true) ?? []
    ));

    $poSignedPaths = json_encode(array_merge(
        $request->hasFile('po_signed_upload') ? json_decode(handleUploads($request->file('po_signed_upload'), 'neft'), true) : [],
        is_array($request->existing_po_signed_upload) ? $request->existing_po_signed_upload : json_decode($request->existing_po_signed_upload, true) ?? []
    ));

    $poDeliveryPaths = json_encode(array_merge(
        $request->hasFile('po_delivery_upload') ? json_decode(handleUploads($request->file('po_delivery_upload'), 'neft'), true) : [],
        is_array($request->existing_po_delivery_upload) ? $request->existing_po_delivery_upload : json_decode($request->existing_po_delivery_upload, true) ?? []
    ));

    // Payment methods as comma-separated string
    $paymentMethods = $request->has('payment_method') ? implode(',', $request->payment_method) : null;
    $amount=0;
    $data = [
        'serial_number' => $request->serial_number,
        'branch_id' => $request->branch_id,
        'user_id' => $request->users_id,
        'bill_pay_id' => $request->bill_pay_id,
        'created_by' => $request->created_by,
        'vendor' => $request->vendor_name,
        'vendor_id' => $request->vendor_id,
        'nature_payment' => $request->nature_payment,
        'payment_status' => $request->payment_status,
        'payment_method' => $paymentMethods,
        'utr_number' => $request->utr_number,
        'pan_number' => $request->pan_number,
        'account_number' => $request->account_number,
        'ifsc_code' => $request->ifsc_code,
        'checker_status' => $request->checker_status ?? 0,
        'approval_status' => $request->approval_status ?? 0,
        'pan_upload' => $panPaths,
        'invoice_upload' => $invoicePaths,
        'bank_upload' => $bankPaths,
        'po_upload' => $poPaths,
        'po_signed_upload' => $poSignedPaths,
        'po_delivery_upload' => $poDeliveryPaths,
        'created_at' => now(),
    ];
    // dd($data);
     if ($isUpdate) {
            $neft = Tblneft::findOrFail($request->id);
            $neft->update($data);
            if(!empty($neft)){
                $vendor = Tblvendor::where('id', $neft->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'NEFT Updated',
                    'description' => "NEFT Generated ₹{$neft->serial_number} updated by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
            $neft_id = $neft->id;

            // Update or insert contact persons
            if ($request->has('account')) {
                foreach ($request->account as $linesData) {
                    $linesDatas = [
                        'neft_id' => $neft_id,
                        'bill_id' => $linesData['bill_id'] ?? null,
                        'bill_pay_id' => $linesData['bill_pay_id'] ?? null,
                        'bill_pay_lines_id' => $linesData['bill_pay_lines_id'] ?? null,
                        'invoice_amount' => $linesData['invoice_amount'] ?? null,
                        'already_paid' => $linesData['already_paid'] ?? null,
                        'tds_tax_name' => $linesData['tds_tax_name'] ?? null,
                        'tds_tax_id' => $linesData['tds_tax_id'] ?? null,
                        'tax_amount' => $linesData['tax_amount'] ?? null,
                        'gst_name' => $linesData['gst_name'] ?? null,
                        'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
                        'gst_amount' => $linesData['gst_amount'] ?? null,
                        'only_payable' => $linesData['only_payable'] ?? null,
                        'updated_at' => $now,
                    ];

                    if (!empty($linesData['id'])) {
                        Tblneftlines::where('id', $linesData['id'])
                            ->where('neft_id', $neft_id)
                            ->update($linesDatas);
                    } else {
                        $contactValues['created_at'] = $now;
                        Tblneftlines::create($linesDatas);
                    }
                }
            }


        } else {
            // Create new customer
            $neft = Tblneft::create($data);
            if(!empty($neft)){
                $vendor = Tblvendor::where('id', $neft->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'NEFT added',
                    'description' => "NEFT Generated ₹{$neft->serial_number} created by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
            $neft_id = $neft->id;
            if ($request->has('account')) {
                foreach ($request->account as $linesData) {
                    Tblneftlines::create([
                        'neft_id' => $neft_id,
                        'bill_id' => $linesData['bill_id'] ?? null,
                        'bill_pay_id' => $linesData['bill_pay_id'] ?? null,
                        'bill_pay_lines_id' => $linesData['bill_pay_lines_id'] ?? null,
                        'invoice_amount' => $linesData['invoice_amount'] ?? null,
                        'already_paid' => $linesData['already_paid'] ?? null,
                        'tds_tax_name' => $linesData['tds_tax_name'] ?? null,
                        'tds_tax_id' => $linesData['tds_tax_id'] ?? null,
                        'tax_amount' => $linesData['tax_amount'] ?? null,
                        'gst_name' => $linesData['gst_name'] ?? null,
                        'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
                        'gst_amount' => $linesData['gst_amount'] ?? null,
                        'only_payable' => $linesData['only_payable'] ?? null,
                        'created_at' => $now,
                    ]);
                // $amount.=$linesData['already_paid'];
                }

            }
            // Tblneft::where('id',$neft_id)->update(['amount_paid',$amount]);

        }

        return response()->json([
                    'success' => true,
                    'message' => $isUpdate ? 'NEFT Data updated successfully!' : 'NEFT Data saved successfully!'
                ]);
    }
    public function CheckerAndApprover(Request $request)
    {
        // dd($request);
        $id=$request->id;
        $approver_id=$request->approver_id;
        if($id !=="" && $id !==null){
            Tblneft::where('id', $id)->update([
                'checker_status' => 1
            ]);
            return response()->json(['success' => true, 'message' => 'checker data updated successfully!']);
        }else if($approver_id !==""){
            Tblneft::where('id', $approver_id)->update([
                'approval_status' => 1
            ]);
            return response()->json(['success' => true, 'message' => 'Approval data updated successfully!']);
        }
    }
    public function exportUsers(Request $request)
    {
        ini_set('memory_limit', '1024M');

        // Get IDs and format from request
        $ids = $request->input('ids', []);
        $format = $request->input('format', 'xlsx'); // default to xlsx

        // Get data: filter by IDs if provided
        $query = Tblneft::with(['Tblvendor','BillLines','BillLines.alreadypaid','Tblbankdetails','Tblbillpay']);

        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }
        $nefts = $query->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $headers = ['PYMT_PROD_TYPE_CODE','PYMT_MODE','DEBIT_ACC_NO','BNF_NAME','BENE_ACC_NO','BENE_IFSC','AMOUNT',
            'DEBIT_NARR', 'CREDIT_NARR', 'MOBILE_NUM', 'EMAIL_ID', 'REMARK','PAN NO','ACCOUNT','IFSC','INVOICE',
            'ALREADY PAID', 'PYMT_DATE', 'REF_NO', 'ADDL_INFO1','ADDL_INFO2','ADDL_INFO3','ADDL_INFO4','ADDL_INFO5'];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        $row = 2;

    $amount=0;
    foreach ($nefts as $neft) {
        $bankDetail = $neft->Tblbankdetails[0] ?? null;
        foreach ($neft->BillLines as $bill_line) {
            // dd($bill_line->alreadypaid);
            $sheet->setCellValue('A' . $row, 'PAB_VENDOR');
            $sheet->setCellValue('B' . $row, 'NEFT');
            $sheet->setCellValueExplicit('C' . $row, '777705777724',DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, optional($neft->Tblvendor)->display_name);
            // $sheet->setCellValue('B' . $row, optional($bankDetail)->accont_number);
           $sheet->setCellValueExplicit(
                'E' . $row,
                "'" . (string) optional($bankDetail)->accont_number,
                DataType::TYPE_STRING
            );
            $sheet->setCellValue('F' . $row, optional($bankDetail)->ifsc_code);
            $sheet->setCellValue('G' . $row, $bill_line->already_paid);
            $sheet->setCellValue('H' . $row, 'NA');
            $sheet->setCellValue('I' . $row, 'NA');
            $sheet->setCellValue('J' . $row, '1234567914');
            $sheet->setCellValue('K' . $row, 'payment@gmail.com');
            $sheet->setCellValue('L' . $row, optional($neft->Tblbillpay)->remark);
            $sheet->setCellValue('M' . $row, optional($neft->Tblvendor)->pan_number);
            $sheet->setCellValueExplicit('N' . $row,"'" . (string) optional($bankDetail)->accont_number,DataType::TYPE_STRING);
            $sheet->setCellValue('O' . $row, optional($bankDetail)->ifsc_code);
            $sheet->setCellValue('P' . $row, $bill_line->invoice_amount);

            foreach ($bill_line->alreadypaid as $payment) {
                $amount = $bill_line->alreadypaid
                ->where('bill_id', $bill_line->bill_id)
                ->where('id', '!=', $bill_line->bill_pay_lines_id)
                ->where('id', '<', $payment->id)
                ->sum('amount');
            }
            // dd($amount);
            $sheet->setCellValue('Q' . $row, $amount);
            $sheet->setCellValue('R' . $row, \Carbon\Carbon::parse($neft->created_at)->format('d-m-Y'));
            $sheet->setCellValue('S' . $row, 'NA');
            $sheet->setCellValue('T' . $row, 'NA');
            $sheet->setCellValue('U' . $row, 'NA');
            $sheet->setCellValue('V' . $row, 'NA');
            $sheet->setCellValue('W' . $row, 'NA');
            $sheet->setCellValue('X' . $row, 'NA');

            // $sheet->setCellValue('J' . $row, $bill_line->only_payable);

            $row++;
        }
    }


        // Generate and return file
        $filename = 'NEFT_export.' . $format;
        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        if ($format === 'csv') {
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            $writer->setSheetIndex(0);
        } else {
            $writer = new Xlsx($spreadsheet);
        }

        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

// quotation
// public function getquotation(Request $request)
//     {
//         $admin = auth()->user();
//         $limit_access=$admin->access_limits;
//         $locations = TblLocationModel::all();
//         $perPage = $request->get('per_page', 10);
//         $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
//         $quotationlist = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])->orderBy('id', 'desc')->paginate($perPage)->appends(['per_page' => $perPage]);
//         return view('vendor.quotation_bashboard', ['admin' => $admin,'locations' => $locations,'quotationlist' => $quotationlist,'perPage' => $perPage,'TblZonesModel' => $TblZonesModel]);
//     }
public function getquotation(Request $request)
{
    // dd($request);
    $admin = auth()->user();
    $locations = TblLocationModel::all();
    $perPage = $request->get('per_page', 10);
    $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
    $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
    $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
    $Tblvendor = Tblvendor::orderBy('id', 'asc')->get();

    $query = TblQuotation::with(['BillLines', 'Tblvendor', 'TblBilling'])->orderBy('id', 'desc');

    // // Apply filters
    // if ($request->filled('date_from') && $request->filled('date_to')) {
    //     $query->whereBetween('bill_date', [$request->date_from, $request->date_to]);
    // }
    if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
    }
    if ($request->filled('zone_id')) {
        $query->where('zone_id', $request->zone_id);
    }
    if ($request->filled('branch_id')) {
        $query->where('branch_id', $request->branch_id);
    }
    if ($request->filled('company_id')) {
        $query->where('company_id', $request->company_id);
    }
    if ($request->filled('vendor_id')) {
        $query->where('vendor_id', $request->vendor_id);
    }

    $quotationlist = $query->paginate($perPage)->appends($request->all());
    // dd($quotationlist);
    if ($request->ajax()) {
        // Render ONLY the tbody rows from a partial view
        return view('vendor.partials.table.quotation_rows', compact('quotationlist','perPage'))->render();
    }

    return view('vendor.quotation_bashboard', [
        'admin' => $admin,
        'TblZonesModel' => $TblZonesModel,
        'locations' => $locations,
        'Tblcompany' => $Tblcompany,
        'Tblvendor' => $Tblvendor,
        'quotationlist' => $quotationlist,
        'perPage' => $perPage
    ]);
}

    public function getquotationcreate()
    {
        $id="";
        $type="";
        if(isset($_GET['id'])){
            $id=$_GET['id'];
        }
        if(isset($_GET['type'])){
            $type=$_GET['type'];
        }
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $count = TblQuotation::count(); // Get total number of rows
        $nextNumber = $count + 1;  // Next serial number
        $quotation_id = 'QO-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $locations = TblLocationModel::all();
        // $Tbltdstax = Tbltdstax::all();
        // $Tbltcstax = Tbltcstax::all();
        // $Tblgsttax = Tblgsttax::all();
        // $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts','bankdetails'])->get();
        // $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
        $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10);
        $Tbltcstax = Tbltcstax::all();
        $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);
        $gsttax = Tblgsttax::orderBy('id', 'desc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'desc')->get();
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails','tdstax'])->get();
        // dd($vendor);
        $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
         if($id !==""){
            if($type =='edit'){
                $quotation_id="";
                $quotation = TblQuotation::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('id',$id)->get();
                return view('vendor.quotation_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'Tblgsttax' => $Tblgsttax,'Tblaccount' => $Tblaccount,'TblZonesModel' => $TblZonesModel,'quotation_id' => $quotation_id,'quotation' => $quotation,'Tbltdssection' => $Tbltdssection,'Tblcompany' => $Tblcompany,'gsttax' => $gsttax,'type' => $type]);
            }else{
                $quotation = TblQuotation::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('id',$id)->get();
                return view('vendor.quotation_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'Tblgsttax' => $Tblgsttax,'Tblaccount' => $Tblaccount,'TblZonesModel' => $TblZonesModel,'quotation_id' => $quotation_id,'quotation' => $quotation,'Tbltdssection' => $Tbltdssection,'Tblcompany' => $Tblcompany,'gsttax' => $gsttax,'type' => $type]);
            }
        }else{
            return view('vendor.quotation_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'Tblgsttax' => $Tblgsttax,'Tblaccount' => $Tblaccount,'TblZonesModel' => $TblZonesModel,'quotation_id' => $quotation_id,'Tbltdssection' => $Tbltdssection,'Tblcompany' => $Tblcompany,'gsttax' => $gsttax,'type' => $type]);
        }
        // return view('vendor.bill_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax]);

    }
     public function savequotation(Request $request)
    {
        // dd($request);
        $isUpdate = $request->filled('id'); // Check if ID exists
        $now = now();
        $user_id = auth()->user()->id;
        $admin = auth()->user();
        $data = [
            'user_id' => $user_id,
            'vendor_id' => $request->vendor_id,
            'vendor_name' => $request->vendor_name,
            'zone_id' => $request->zone_id,
            'zone_name' => $request->zone,
            'branch_name' => $request->branch,
            'branch_id' => $request->branch_id,
            'company_name' => $request->company_name,
            'company_id' => $request->company_id,
            'quotation_no' => $request->quotation_no,
            'quotation_gen_no' => $request->quotation_gen_no,
            'delivery_address' => $request->delivery_address,
            'order_number' => $request->order_number,
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            'payment_terms' => $request->payment_terms,
            'subject' => $request->subject,
            'discount_percent' => $request->discount_percent,
            'discount_type' => $request->discount_type,
            'discount_amount' => $this->cleanCurrency($request->discount_amount),
            'adjustment_value' => $request->adjustment_value,
            'tds_tax_id' => $request->tds_tax_id,
            'tcs_tax_id' => $request->tcs_tax_id,
            'tax_type' => $request->tax_type,
            'tax_name' => $request->tcs_tax_name ?: $request->tds_tax_name,
            'tax_rate' => $request->tcs_tax_selected ?: $request->tds_tax_selected,
            'tax_amount' => $this->cleanCurrency($request->tax_amount),
            'sub_total_amount' => $this->cleanCurrency($request->sub_total_amount),
            'adjustment_amount' => $this->cleanCurrency($request->adjustment_amount),
            'grand_total_amount' => $this->cleanCurrency($request->grand_total_amount),
            'balance_amount' => $this->cleanCurrency($request->grand_total_amount),
            'status' => $request->save_status,
            'note' => $request->note,
            'created_at' => $now,
        ];
        // dd($data);
        if (!$isUpdate) {
            $data['updated_at'] = $now;
        }

       $fileNames = [];
        if ($request->hasFile('uploads')) {
            $uploadPath = public_path('uploads/vendor/bill');

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            foreach ($request->file('uploads') as $file) {
                $originalName = $file->getClientOriginalName();
                $uniqueFileName = time() . '_' . $originalName;
                $file->move($uploadPath, $uniqueFileName);
                $fileNames[] = $uniqueFileName;
            }
        }
        // 2. Get existing files from hidden input (if any)
        $existingFiles = $request->existing_files ?? []; // from hidden input
        if (!is_array($existingFiles)) {
            $existingFiles = json_decode($existingFiles, true) ?? [];
        }

        // 3. Merge both arrays
        $mergedFiles = array_merge($existingFiles, $fileNames);

        // 4. Save merged list to DB
        $data['documents'] = json_encode($mergedFiles);
          if ($isUpdate) {
            $quotation = TblQuotation::findOrFail($request->id);
            $quotation->update($data);
            if(!empty($quotation)){
                $vendor = Tblvendor::where('id', $quotation->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'Quotation Updated',
                    'description' => "Quotation {$quotation->quotation_no} of amount ₹{$quotation->grand_total_amount} updated by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
            $quotation_id = $quotation->id;


            // Update or insert contact persons
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    $linesDatas = [
                        'quotation_id' => $quotation_id,
                        'item_details' => $linesData['item_details'] ?? null,
                        'account' => $linesData['account_name'] ?? null,
                        'account_id' => $linesData['account_id'] ?? null,
                        'quantity' => $linesData['quantity'] ?? null,
                        'rate' => $linesData['rate'] ?? null,
                        'customer' => $linesData['customer'] ?? null,
                        'gst_name' => $linesData['gst_name'] ?? null,
                        'gst_rate' => $linesData['gst_tax_selected'] ?? null,
                        'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
                        'gst_type' => $linesData['gst_tax_type'] ?? null,
                        'cgst_amount' => $linesData['cgst_amount'] ?? null,
                        'sgst_amount' => $linesData['sgst_amount'] ?? null,
                        'gst_amount' => $linesData['gst_amount'] ?? null,
                        'amount' => $linesData['amount'] ?? null,
                        'updated_at' => $now,
                    ];

                    if (!empty($linesData['id'])) {
                        TblQuotationLines::where('id', $linesData['id'])
                            ->where('quotation_id', $quotation_id)
                            ->update($linesDatas);
                    } else {
                        $contactValues['created_at'] = $now;
                        TblQuotationLines::create($linesDatas);
                    }
                }
            }


        } else {
            // Create new customer
            $quotation = TblQuotation::create($data);
            // dd($quotation);
            if(!empty($quotation)){
                $vendor = Tblvendor::where('id', $quotation->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'Quotation Created',
                    'description' => "Quotation {$quotation->quotation_no} of amount ₹{$quotation->grand_total_amount} created by {$admin->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
            $quotation_id = $quotation->id;
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    TblQuotationLines::create([
                        'quotation_id' => $quotation_id,
                        'item_details' => $linesData['item_details'] ?? null,
                        'account' => $linesData['account_name'] ?? null,
                        'account_id' => $linesData['account_id'] ?? null,
                        'quantity' => $linesData['quantity'] ?? null,
                        'rate' => $linesData['rate'] ?? null,
                        'customer' => $linesData['customer'] ?? null,
                        'gst_name' => $linesData['gst_name'] ?? null,
                        'gst_rate' => $linesData['gst_tax_selected'] ?? null,
                        'gst_type' => $linesData['gst_tax_type'] ?? null,
                        'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
                        'cgst_amount' => $linesData['cgst_amount'] ?? null,
                        'sgst_amount' => $linesData['sgst_amount'] ?? null,
                        'gst_amount' => $linesData['gst_amount'] ?? null,
                        'amount' => $linesData['amount'] ?? null,
                        'created_at' => $now,
                    ]);
                }
            }

        }
         return response()->json([
            'success' => true,
            'message' => $isUpdate ? 'Quotation Data updated successfully!' : 'Quotation Data saved successfully!'
        ]);
    }
     public function QuotationApprover(Request $request)
    {
        $approver_id=$request->approver_id;
        $status=$request->value;
         if($approver_id !==""){
            if($status ==="Approve"){
                TblQuotation::where('id', $approver_id)->update([
                    'approval_status' => 1
                ]);
                return response()->json(['success' => true, 'message' => 'Approval data updated successfully!']);
            }else{
                TblQuotation::where('id', $approver_id)->update([
                    'reject_status' => 1
                ]);
                return response()->json(['success' => true, 'message' => 'Rejected data updated successfully!']);
            }
        }
    }
    public function getquotationfetch(Request $request)
    {
        $ids = json_decode($request->input('selected_ids'), true);
        $quotation = TblQuotation::with(['TblBilling', 'BillLines', 'Tblvendor'])->whereIn('id',$ids)->get();
         return response()->json([
                    'message' => 'quotation fetched successfully.',
                    'quotation' => $quotation
                ]);
    }
    public function getquotationprint(Request $request)
{
    $quotationId = $request->id;

    $quotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling','TblCompany'])->findOrFail($quotationId);
    // dd($quotation);
    $pdf = new TCPDF();
    $pdf->setPrintHeader(false); // disable default header line
    $pdf->setPrintFooter(false); // optional
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Execute raw TCPDF drawing code
    include(resource_path('views/vendor/quotationprint.blade.php'));

    return response($pdf->Output('Quotation_'.$quotation->id.'.pdf', 'S'))
        ->header('Content-Type', 'application/pdf');
}
public function getquotationpdf(Request $request)
{
    $quotationId = $request->id;
    $quotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])->findOrFail($quotationId);

    $pdf = new TCPDF();
    $pdf->setPrintHeader(false); // disable default header line
    $pdf->setPrintFooter(false); // optional
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Execute raw TCPDF drawing code
    include(resource_path('views/vendor/quotationprint.blade.php'));

    // Force download the PDF
    return response($pdf->Output('quotation_'.$quotation->quotation_no.'.pdf', 'D'))
        ->header('Content-Type', 'application/pdf');
}
public function importQuotationExcel(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    Excel::import(new QuotationImport, $request->file('file'));

    return back()->with('success', 'Quotation data imported successfully!');
}
 public function getTdsTaxes(Request $request)
{
    $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10);
    if ($request->ajax()) {
        return response()->json([
            'html' => view('vendor.partials.tds_table', compact('Tbltdstax'))->render()
        ]);
    }
    return view('vendor.partials.tds_table', compact('Tbltdstax'))->render();
}

public function getGstTaxes(Request $request)
{
    // dd($request);
    $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);

    if ($request->ajax()) {
        $view = view('vendor.partials.gst_table', compact('Tblgsttax'))->render();
        return response()->json(['html' => $view]);
        // return response()->json([
        //     'html' => view('vendor.partials.gst_table', compact('Tblgsttax'))->render()
        // ]);
    }

    return view('vendor.partials.gst_table', compact('Tblgsttax'));
}

public function getPurchases(Request $request)
{
    $purchaselist = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling'])
                    ->where('approval_status',1)->where('bill_status',0)
                    ->orderBy('id','desc')
                    ->paginate(10);
    return view('vendor.partials.purchase_table', compact('purchaselist'))->render();
}

public function getQuotations(Request $request)
{
    $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])
                    ->where('approval_status',1)->where('po_status',0)
                    ->orderBy('id','desc')
                    ->paginate(10);
    return view('vendor.partials.quotation_table', compact('TblQuotation'))->render();
}
//tds tax
public function gettdsdashboard(Request $request)
{
    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $locations = TblLocationModel::all();
    $perPage = $request->get('per_page', 10);

    $tdstax = Tbltdstax::orderBy('id', 'desc')
        ->paginate($perPage)
        ->appends(['per_page' => $perPage]);

    // $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->orderBy('id', 'desc')->paginate(10);
    return view('vendor.tdstax', [
        'admin' => $admin,
        'locations' => $locations,
        'tdstax' => $tdstax,
        'perPage' => $perPage,
    ]);
}
public function getgstdashboard(Request $request)
{
    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $locations = TblLocationModel::all();
    $perPage = $request->get('per_page', 10);

    $gsttax = Tblgsttax::orderBy('id', 'desc')
        ->paginate($perPage)
        ->appends(['per_page' => $perPage]);

    // $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->orderBy('id', 'desc')->paginate(10);
    return view('vendor.gsttax', [
        'admin' => $admin,
        'locations' => $locations,
        'gsttax' => $gsttax,
        'perPage' => $perPage,
    ]);
}
public function getnaturedashboard(Request $request)
{
    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $locations = TblLocationModel::all();
    $perPage = $request->get('per_page', 10);

    $natureofpayment = Tblnaturepayment::orderBy('id', 'desc')
        ->paginate($perPage)
        ->appends(['per_page' => $perPage]);

    // $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->orderBy('id', 'desc')->paginate(10);
    return view('vendor.natureofpayment', [
        'admin' => $admin,
        'locations' => $locations,
        'natureofpayment' => $natureofpayment,
        'perPage' => $perPage,
    ]);
}
public function getnaturesave(Request $request)
    {
        // dd($request);
        $id = $request->id;
        $admin = auth()->user();
        $data=[
            "name"=>$request->name,
            "description"=>$request->description,
            "user_id"=>$admin->id,
            "created_by"=>$admin->user_fullname,
            "created_at"=>now(),
        ];
        if ($id !== "" && $id!==null) {
            unset($data['created_at']);
            $sts=Tblnaturepayment::where('id', $id)->update($data);
        } else {
            $sts=Tblnaturepayment::create($data);
        }
        return response()->json([
            'success' => true,
            'message' => $id!==""? 'Nature Of Payment Data Updated successfully!':'Nature Of Payment Data saved successfully!'
        ]);

    }
//GRN
public function getgrndashboard(Request $request)
    {
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::orderBy('id', 'asc')->get();

        $query = Tblgrn::with(['BillLines','Tblvendor','TblBilling'])->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
         if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $grnlist = $query->paginate($perPage)->appends($request->all());

        if ($request->ajax()) {
            // Render ONLY the tbody rows from a partial view
            return view('vendor.partials.table.grn_rows', compact('grnlist','perPage'))->render();
        }

        $grnlist = Tblgrn::with(['BillLines','Tblvendor','TblBilling'])->orderBy('id', 'desc')->paginate($perPage)->appends(['per_page' => $perPage]);
        return view('vendor.grn_bashboard', ['admin' => $admin,'locations' => $locations,'grnlist' => $grnlist,'perPage' => $perPage,'TblZonesModel' => $TblZonesModel,'Tblcompany' => $Tblcompany, 'Tblvendor' => $Tblvendor]);
    }
// public function getgrnconvert(Request $request)
//     {
//         $admin = auth()->user();
//         $limit_access=$admin->access_limits;
//         $locations = TblLocationModel::all();
//         $perPage = $request->get('per_page', 10);
//         $purchaselist = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling'])->orderBy('id', 'desc')->where('grn_status',0)->paginate($perPage)->appends(['per_page' => $perPage]);
//         $billlist = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])->orderBy('id', 'desc')->where('grn_status',0)->paginate($perPage)->appends(['per_page' => $perPage]);
//         return view('vendor.grn_convert', ['admin' => $admin,'locations' => $locations,'purchaselist' => $purchaselist,'billlist' => $billlist,'perPage' => $perPage]);
//     }
public function getgrnconvert(Request $request)
{
    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $locations = TblLocationModel::all();

    $perPage = $request->get('per_page', 10);
    $activeTable = $request->get('table', 'po'); // Get which table is active

    // Load PO data
    $purchaselist = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling'])
        ->orderBy('id', 'desc')
        ->where('grn_status', 0)
        ->paginate($perPage, ['*'], 'po_page')
        ->appends([
            'per_page' => $perPage,
            'table' => $activeTable
        ]);

    // Load Bill data
    $billlist = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])
        ->orderBy('id', 'desc')
        ->where('grn_status', 0)
        ->paginate($perPage, ['*'], 'bill_page')
        ->appends([
            'per_page' => $perPage,
            'table' => $activeTable
        ]);

    return view('vendor.grn_convert', [
        'admin' => $admin,
        'locations' => $locations,
        'purchaselist' => $purchaselist,
        'billlist' => $billlist,
        'perPage' => $perPage,
        'activeTable' => $activeTable
    ]);
}
public function getgrncreate()
{
    $id = request()->query('id', '');
    $type = request()->query('type', '');

    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $count = Tblgrn::count();
    $nextNumber = $count + 1;
    $grn_id = 'GRN-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

    $locations = TblLocationModel::all();
    $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10);
    $Tbltcstax = Tbltcstax::all();
    $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);
    $gsttax = Tblgsttax::orderBy('id', 'desc')->get();
    $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
    $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
    $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->get();
    $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
    $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])
        ->where('approval_status', 1)->where('po_status', 0)
        ->orderBy('id', 'desc')->paginate(10);

    $grndata = null;
    $grnedit = null;

    if ($id !== "") {
        $purchase_id = "";

        if ($type === "po") {
            $grndata = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])
                           ->where('id', $id)
                           ->first();
        } elseif ($type === "bill") {
            $grndata = Tblbill::with(['TblBilling', 'BillLines', 'Tblvendor', 'Tblbankdetails'])
                        ->where('id', $id)
                        ->first();
        }
        else{
            $grnedit = Tblgrn::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('id',$id)->get();
        }
    }

    // dd($grnedit);
    return view('vendor.grn_create', [
        'admin' => $admin,
        'locations' => $locations,
        'vendor' => $vendor,
        'customer' => $customer,
        'Tbltdstax' => $Tbltdstax,
        'Tbltcstax' => $Tbltcstax,
        'Tblgsttax' => $Tblgsttax,
        'TblZonesModel' => $TblZonesModel,
        'grn_id' => $grn_id,
        'TblQuotation' => $TblQuotation,
        'grnedit' => $grnedit,
        'grndata' => $grndata,
        'type' => $type,
        'Tblcompany' => $Tblcompany,
        'gsttax' => $gsttax
    ]);
}
public function savegrn(Request $request)
    {
        // dd($request);
        $isUpdate = $request->filled('id'); // Check if ID exists
        $now = now();
        $user_id = auth()->user()->id;
        $admin = auth()->user();

        $data = [
            'user_id' => $user_id,
            'vendor_id' => $request->vendor_id,
            'purchase_id' => $request->purchase_id,
            'bill_id' => $request->bill_id,
            'vendor_name' => $request->vendor_name,
            'zone_id' => $request->zone_id,
            'zone_name' => $request->zone,
            'branch_name' => $request->branch,
            'branch_id' => $request->branch_id,
            'company_name' => $request->company_name,
            'company_id' => $request->company_id,
            'grn_number' => $request->grn_number,
            'order_number' => $request->order_number,
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            'payment_terms' => $request->payment_terms,
            'subject' => $request->subject,
            'save_status' => $request->save_status,
            'note' => $request->note,
            'qc_ststus' => $request->qc_ststus,
            'created_at' => $now,
        ];
        if (!$isUpdate) {
            $data['updated_at'] = $now;
        }
        if($request->purchase_id !==""){
                TblPurchaseorder::where('id', $request->purchase_id)->update([
                        'grn_status' => 1
                    ]);
        }else if($request->bill_id !==""){
            Tblbill::where('id', $request->bill_id)->update([
                    'grn_status' => 1
                ]);
        }

       $fileNames = [];

        // 1. Handle new file uploads
        if ($request->hasFile('uploads')) {
            $uploadPath = public_path('uploads/vendor/grn');

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            foreach ($request->file('uploads') as $file) {
                $originalName = $file->getClientOriginalName();
                $uniqueFileName = time() . '_' . $originalName;
                $file->move($uploadPath, $uniqueFileName);
                $fileNames[] = $uniqueFileName;
            }
        }

        // 2. Get existing files from hidden input (if any)
        $existingFiles = $request->existing_files ?? []; // from hidden input
        if (!is_array($existingFiles)) {
            $existingFiles = json_decode($existingFiles, true) ?? [];
        }

        // 3. Merge both arrays
        $mergedFiles = array_merge($existingFiles, $fileNames);

        // 4. Save merged list to DB
        $data['documents'] = json_encode($mergedFiles);
        // dd($data);
          if ($isUpdate) {
            $grn = Tblgrn::findOrFail($request->id);
            $grn->update($data);
            if(!empty($grn)){
                $vendor = Tblvendor::where('id', $grn->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'GRN Updated',
                    'description' => "GRN Generated ₹{$grn->grn_number} updated by {$grn->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
            $grn_id = $grn->id;

            // Update or insert contact persons
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    $linesDatas = [
                        'grn_id' => $grn_id,
                        'item_details' => $linesData['item_details'] ?? null,
                        'quantity' => $linesData['quantity'] ?? null,
                        'receivable_quantity' => $linesData['receivable_quantity'] ?? null,
                        'acceptable_quantity' => $linesData['acceptable_quantity'] ?? null,
                        'reject_quantity' => $linesData['reject_quantity'] ?? null,
                        'balance_quantity' => $linesData['balance_quantity'] ?? null,
                        'updated_at' => $now,
                    ];

                    if (!empty($linesData['id'])) {
                        TblgrnLines::where('id', $linesData['id'])
                            ->where('grn_id', $grn_id)
                            ->update($linesDatas);
                    } else {
                        $contactValues['created_at'] = $now;
                        TblgrnLines::create($linesDatas);
                    }
                }
            }
        } else {
            // Create new customer
            $grn = Tblgrn::create($data);
            if(!empty($grn)){
                $vendor = Tblvendor::where('id', $grn->vendor_id)->first();
                $history = [
                    'vendor_id'   => $vendor->id,
                    'name'        => 'GRN Added',
                    'description' => "GRN Generated ₹{$grn->grn_number} created by {$grn->email}",
                    'date' => now()->toDateString(),
                    'time' => now()->format('h:i A')
                ];
                TblVendorHistory::create($history);
            }
            $grn_id = $grn->id;
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    TblgrnLines::create([
                        'grn_id' => $grn_id,
                        'item_details' => $linesData['item_details'] ?? null,
                        'quantity' => $linesData['quantity'] ?? null,
                        'receivable_quantity' => $linesData['receivable_quantity'] ?? null,
                        'acceptable_quantity' => $linesData['acceptable_quantity'] ?? null,
                        'reject_quantity' => $linesData['reject_quantity'] ?? null,
                        'balance_quantity' => $linesData['balance_quantity'] ?? null,
                        'created_at' => $now,
                    ]);
                }
            }
        }
         return response()->json([
            'success' => true,
            'message' => $isUpdate ? 'GRN Data updated successfully!' : 'GRN Data saved successfully!'
        ]);
    }
    //account
    public function getaccountsave(Request $request)
    {
        $id = $request->id;
        $data=[
            "name"=>$request->name,
            "code"=>$request->code,
            "description"=>$request->description,
            "created_at"=>now(),
        ];
        // dd($data);
        if ($id !== "" && $id !==null) {
            unset($data['created_at']);
            Tblaccount::where('id', $id)->update($data);
        } else {
            Tblaccount::create($data);
        }
         $Tblaccount = Tblaccount::orderBy('id', 'desc')->paginate(10);
        return response()->json([
            'success' => true,
            'account' => $Tblaccount,
            'message' => $id!==""? 'Account Data Updated successfully!':'Account Data saved successfully!'
        ]);

    }
    //branch
    public function getbranchfetch(Request $request)
    {
        $id = $request->id;
        $branch = DB::table('tbl_locations')->where('zone_id',$id)->get();
         return response()->json([
                    'message' => 'branch fetched successfully.',
                    'branch' => $branch
                ]);
    }


public function getpoemailsave(Request $request)
    {
        // dd($request);
        $id = $request->id;
        $admin = auth()->user();
        $data=[
            "email"=>$request->name,
            "user_id"=>$admin->id,
            "created_by"=>$admin->user_fullname,
            "created_at"=>now(),
        ];
        if ($id !== "" && $id!==null) {
            unset($data['created_at']);
            $TblPoEmail=TblPoEmail::where('id', $id)->update($data);
        } else {
            $TblPoEmail=TblPoEmail::create($data);
        }
        $TblPoEmail = TblPoEmail::orderBy('id', 'asc')->paginate(10);

        return response()->json([
            'success' => true,
            'TblPoEmail' => $TblPoEmail,
            'message' => $id!==""? 'PO Email Data Updated successfully!':'PO Email Data saved successfully!'
        ]);

    }

    // bank dashboard
    private function getTransactions()
    {
        $data = Storage::get('transactions.json');
        return collect(json_decode($data, true)['transactions']);
    }

    private function saveTransactions($transactions)
    {
        Storage::put('transactions.json', json_encode(['transactions' => $transactions], JSON_PRETTY_PRINT));
    }

   public function index(Request $request)
{
    $admin = auth()->user();
    $locations = TblLocationModel::all();

    $transactions = $this->getTransactions();
    // dd($request);
    // Nature filter
    if ($request->filled('nature')) {
        $transactions = $transactions->where('nature', $request->nature);
    }

    if ($request->filled('date')) {
        // Split the range by "to"
        [$start, $end] = explode(' to ', $request->date);

        // Convert d/m/Y -> Y-m-d
        $startDate = Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
        $endDate   = Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();

        // Assuming transactions have 'date' in Y-m-d
        $transactions = $transactions->filter(function($t) use ($startDate, $endDate) {
            return Carbon::parse($t['date'])->between($startDate, $endDate);
        });
    }
    // Chart Data
    $natureSummary = $transactions->groupBy('nature')->map(fn($group) => $group->sum('amount'));
    $creditTotal = $transactions->where('type', 'CREDIT')->sum('amount');
    $debitTotal  = abs($transactions->where('type', 'DEBIT')->sum('amount'));

    $natures = $this->getTransactions()->pluck('nature')->unique();

    // Check AJAX
    if ($request->ajax()) {
        $html = view('vendor.partials.transactions', [
            'transactions' => $transactions->values(),
        ])->render();

        return response()->json([
            'html'       => $html,
            'chartData'  => [
                'natureSummary' => $natureSummary,
                'creditTotal'   => $creditTotal,
                'debitTotal'    => $debitTotal,
            ]
        ]);
    }

    return view('vendor.bank_dashboard', compact(
        'transactions', 'admin', 'locations', 'natureSummary',
        'creditTotal', 'debitTotal', 'natures'
    ));
}




    public function approve($id)
    {
        $transactions = $this->getTransactions();

        $transactions = $transactions->map(function ($t) use ($id) {
            if ($t['id'] == $id) {
                $t['status'] = 'Paid';
            }
            return $t;
        });

        $this->saveTransactions($transactions);

        return back()->with('success', 'Payment Approved!');
    }

    public function export()
    {
        $transactions = $this->getTransactions()->where('status', 'Queued');

        $filename = 'payments_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen(storage_path('app/' . $filename), 'w');

        fputcsv($handle, ['Date', 'Description', 'Amount', 'Nature', 'Status']);
        foreach ($transactions as $t) {
            fputcsv($handle, [$t['date'], $t['description'], $t['amount'], $t['nature'], $t['status']]);
        }
        fclose($handle);

        return response()->download(storage_path('app/' . $filename));
    }
// tds summary
public function gettdssummary(Request $request)
    {
        // dd($request);
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])->orderBy('id', 'desc');

        if ($request->filled('section_id')) {
            $tdsids = Tbltdstax::where('section_id', $request->section_id)->pluck('id')->toArray();
        }

        if (!empty($tdsids)) {
            $query->whereIn('tds_tax_id', $tdsids);
        }
        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $billlist = $query->paginate($perPage)->appends($request->all());
        if ($request->ajax()) {
            return view('vendor.partials.table.tds_summary_rows', compact('billlist','perPage'))->render();
        }
        $billlist = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])->orderBy('id', 'desc')->paginate($perPage)->appends(['per_page' => $perPage]);

        return view('vendor.tds_summary', ['admin' => $admin,'locations' => $locations,'billlist' => $billlist,'perPage' => $perPage,'TblZonesModel' => $TblZonesModel,'Tbltdssection' => $Tbltdssection]);
    }

    public function getcompany(Request $request)
{
    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $locations = TblLocationModel::all();
    $perPage = $request->get('per_page', 10);

    $Tblcompany = Tblcompany::orderBy('id', 'desc')
        ->paginate($perPage)
        ->appends(['per_page' => $perPage]);

    // $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->orderBy('id', 'desc')->paginate(10);
    return view('vendor.Company_dashboard', [
        'admin' => $admin,
        'locations' => $locations,
        'Tblcompany' => $Tblcompany,
        'perPage' => $perPage,
    ]);
}
public function getcompanysave(Request $request)
    {
        // dd($request);
        $id = $request->company_id;
        $admin = auth()->user();
        $data=[
            "company_name"=>$request->company_name,
            "reg_number"=>$request->reg_number,
            "address"=>$request->address,
            "email"=>$request->email,
            "phone"=>$request->phone,
            "gst_number"=>$request->gst_number,
            "website"=>$request->website,
            "city"=>$request->city_name,
            "state"=>$request->state,
            "country"=>$request->country,
            "zip_code"=>$request->zip_code,
            "created_at"=>now(),
        ];
        if ($request->hasFile('logo_upload')) {
            $uploadPath = public_path('uploads/vendor/company');

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file = $request->file('logo_upload'); // only one file
            $originalName = $file->getClientOriginalName();
            $uniqueFileName = time() . '_' . $originalName;
            $file->move($uploadPath, $uniqueFileName);

            $data['logo_upload'] = $uniqueFileName; // save single file name
        }

        // dd($data);
        if ($id !== "" && $id!==null) {
            unset($data['created_at']);
            Tblcompany::where('id', $id)->update($data);
        } else {
            Tblcompany::create($data);
        }
        return response()->json([
            'success' => true,
            'message' => $id!==""? 'Company Details Data Updated successfully!':'Company Details Data saved successfully!'
        ]);

    }
    public function getvendortype(Request $request)
{
    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $locations = TblLocationModel::all();
    $perPage = $request->get('per_page', 10);

    $natureofpayment = TblVendortype::orderBy('id', 'desc')
        ->paginate($perPage)
        ->appends(['per_page' => $perPage]);

    // $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->orderBy('id', 'desc')->paginate(10);
    return view('vendor.vendor_type', [
        'admin' => $admin,
        'locations' => $locations,
        'natureofpayment' => $natureofpayment,
        'perPage' => $perPage,
    ]);
}
public function getvendortypesave(Request $request)
    {
        // dd($request);
        $id = $request->id;
        $admin = auth()->user();
        $data=[
            "name"=>$request->name,
            "description"=>$request->description,
            "user_id"=>$admin->id,
            "created_by"=>$admin->user_fullname,
            "created_at"=>now(),
        ];
        if ($id !== "" && $id!==null) {
            unset($data['created_at']);
            $sts=TblVendortype::where('id', $id)->update($data);
        } else {
            $sts=TblVendortype::create($data);
        }
        return response()->json([
            'success' => true,
            'message' => $id!==""? 'Vendor Type Data Updated successfully!':'Vendor Type Data saved successfully!'
        ]);

    }

}
