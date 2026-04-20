<?php

namespace App\Http\Controllers;

use App\Exports\BillPaymentTemplateExport;
use App\Exports\BillsExport;
use App\Exports\BillTemplateExport;
use App\Exports\GstSummaryExport;
use App\Exports\ProfessionalSummaryExport;
use App\Exports\PurchaseTemplateExport;
use App\Exports\QuotationTemplateExport;
use App\Exports\TdsDetailedExport;
use App\Exports\TdsFyWiseExport;
use App\Exports\TdsReportExport;
use App\Exports\TdsSummaryExport;
use App\Exports\VendorIncomeExport;
use App\Exports\VendorTemplateExport;
use App\Imports\billImport;
use App\Imports\BillPaymentImport;
use App\Imports\PurchaseImport;
use App\Imports\QuotationImport;
use App\Imports\VendorImport;
use App\Models\AdminUserDepartments;
use App\Models\BillCategory;
use App\Models\BillingListModel;
use App\Models\CategoryModel;
use App\Models\ConsumableStore;
use App\Models\Customer;
use App\Models\CancelbillFormModel;
use App\Models\DiscountFormModel;
use App\Models\ExpenseCategory;
use App\Models\ExpenseReport;
use App\Models\ExpenseType;
use App\Models\HrmUsers;
use App\Models\ImageModel;
use App\Models\LocationModel;
use App\Models\RefundFormModel;
use App\Models\PriorityModel;
use App\Models\StatusModel;
use App\Models\SubCategoryModel;
use App\Models\Tblaccount;
use App\Models\Tblbankdetails;
use App\Models\Tblbill;
use App\Models\TblBilling;
use App\Models\TblBillLines;
use App\Models\Tblbillpay;
use App\Models\TblBillPayLines;
use App\Models\Tblcompany;
use App\Models\TblContact;
use App\Models\Tblcustomer;
use App\Models\TblDeliveryAddress;
use App\Models\Tblgrn;
use App\Models\TblgrnLines;
use App\Models\Tblgsttax;
use App\Models\TblLocationModel;
use App\Models\Tblnaturepayment;
use App\Models\Tblneft;
use App\Models\Tblneftlines;
use App\Models\TblPoEmail;
use App\Models\TblPurchaseorder;
use App\Models\TblPurchaseorderLines;
use App\Models\TblQuotation;
use App\Models\TblQuotationLines;
use App\Models\TblShipping;
use App\Models\Tbltcstax;
use App\Models\Tbltdssection;
use App\Models\Tbltdstax;
use App\Models\TblUserDepartments;
use App\Models\Tblvendor;
use App\Models\TblVendorHistory;
use App\Models\TblVendortype;
use App\Models\TblZonesModel;
use App\Models\TicketActivitiesModel;
use App\Models\Department;
use App\Models\PettyCashHistory;
use App\Models\TicketActivityModel;
use App\Models\TicketDetails;
use App\Models\TicketModel;
use App\Models\User;
use App\Models\UserDepartments;
use App\Models\UserDesignations;
use App\Models\usermanagementdetails;
use App\Models\UserProfile;
use App\Providers\RouteServiceProvider;
use App\Support\MocdocLocationKeys;
use Barryvdh\DomPDF\Facade\Pdf;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Log;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF;

class VendorController extends Controller
{
    /**
     * Normalize a bill / PO / quotation line from request data.
     * Returns null for empty new rows (skip insert). Coerces quantity/rate for NOT NULL columns.
     * Uses account_name or account for the stored account label.
     *
     * @return array{line_id: int|string|null, columns: array<string, mixed>}|null
     */
    private function prepareVendorLinePayload(array $linesData): ?array
    {
        $rawId = $linesData['id'] ?? null;
        $hasId = $rawId !== null && $rawId !== '' && !(is_string($rawId) && trim($rawId) === '');

        $itemDetails = isset($linesData['item_details']) ? trim((string) $linesData['item_details']) : '';

        $q = $linesData['quantity'] ?? null;
        if ($q === '') {
            $q = null;
        }

        if (! $hasId) {
            if ($itemDetails === '' && $q === null) {
                return null;
            }
        }

        $quantity = $q === null ? (($itemDetails !== '') ? 1.0 : 0.0) : (float) $q;

        $r = $linesData['rate'] ?? null;
        if ($r === '' || $r === null) {
            $rate = 0.0;
        } else {
            $rate = (float) $r;
        }

        $account = $linesData['account_name'] ?? $linesData['account'] ?? null;
        if (is_string($account)) {
            $account = trim($account) === '' ? null : $account;
        }

        $accountId = $linesData['account_id'] ?? null;
        if ($accountId === '') {
            $accountId = null;
        }

        $columns = [
            'item_details' => $itemDetails !== '' ? $itemDetails : null,
            'account' => $account,
            'account_id' => $accountId,
            'quantity' => $quantity,
            'rate' => $rate,
            'customer' => $linesData['customer'] ?? null,
            'gst_name' => $linesData['gst_name'] ?? null,
            'gst_rate' => $linesData['gst_tax_selected'] ?? null,
            'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
            'gst_type' => $linesData['gst_tax_type'] ?? null,
            'cgst_amount' => $linesData['cgst_amount'] ?? null,
            'sgst_amount' => $linesData['sgst_amount'] ?? null,
            'gst_amount' => $linesData['gst_amount'] ?? null,
            'amount' => $linesData['amount'] ?? null,
        ];

        return [
            'line_id' => $hasId ? $rawId : null,
            'columns' => $columns,
        ];
    }

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
    $admin        = auth()->user();
    $limit_access = $admin->access_limits;

    // Validate perPage to prevent bypassing pagination
    $perPage = (int) $request->get('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50, 100, 250, 500]) ? $perPage : 10;

    // Build base query
    $query = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails', 'history'])
                ->orderBy('id', 'desc');

    // ── Filters ──────────────────────────────────────────────
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
        $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
        $query->whereBetween('created_at', [$from, $to]);
    }
    if ($request->filled('zone_id')) {
        $query->whereIn('zone_id', explode(',', $request->zone_id));
    }
    if ($request->filled('branch_id')) {
        $query->whereIn('branch_id', explode(',', $request->branch_id));
    }
    if ($request->filled('company_id')) {
        $query->whereIn('company_id', explode(',', $request->company_id));
    }
    if ($request->filled('vendor_id')) {
        $query->whereIn('id', explode(',', $request->vendor_id));
    }
    if ($request->filled('active_status')) {
        $query->where('active_status', $request->active_status);
    }
    if ($request->filled('universal_search')) {
        $search = $request->universal_search;
        $query->where(function ($q) use ($search) {
            $q->where('vendor_id',         'like', "%{$search}%")
              ->orWhere('vendor_first_name','like', "%{$search}%")
              ->orWhere('vendor_last_name', 'like', "%{$search}%")
              ->orWhere('company_name',     'like', "%{$search}%")
              ->orWhere('display_name',     'like', "%{$search}%")
              ->orWhere('mobile',           'like', "%{$search}%")
              ->orWhere('pan_number',       'like', "%{$search}%")
              ->orWhere('gst_number',       'like', "%{$search}%")
              ->orWhere('website',          'like', "%{$search}%")
              ->orWhere('department',       'like', "%{$search}%")
              ->orWhere('reference',        'like', "%{$search}%");
        });
    }

    // Paginate — exclude 'page' from appends so the paginator controls its own page links
    $vendor = $query->paginate($perPage)
                    ->appends($request->except('page'));

    // ── AJAX: return only the table partial ──────────────────
    if ($request->ajax()) {
        return view('vendor.partials.table.vendor_rows', compact('vendor', 'perPage'))->render();
    }

    // ── Full page load: fetch extra data only when needed ────
    $locations  = TblLocationModel::all();
    $Tblvendor  = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
    $allVendors = Tblvendor::orderBy('id', 'desc')->get();

    return view('vendor.vendor', [
        'admin'        => $admin,
        'locations'    => $locations,
        'vendor'       => $vendor,
        'perPage'      => $perPage,
        'Tblvendor'    => $Tblvendor,
        'allVendors'   => $allVendors,
        'limit_access' => $limit_access,
    ]);
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
    // Get the last vendor row
    $lastVendor = Tblvendor::orderBy('id', 'desc')->first();

    if ($lastVendor && preg_match('/VEN-(\d+)/', $lastVendor->vendor_id, $matches)) {
        // Extract number and increment
        $nextNumber = intval($matches[1]) + 1;
        $vendor_id = 'VEN-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    } else {
        // First vendor when table is empty
        $vendor_id = 'VEN-001';
    }
    $data = [
        'user_id' => $user_id,
        'vendor_id' => $vendor_id,
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
        'restore_filters' => $isUpdate ? true : false,
        'message' => $isUpdate ? 'Vendor Data updated successfully!' : 'Vendor Data saved successfully!'
    ]);
}
public function vendordelete(Request $request)
{
    $id = $request->id;
    DB::transaction(function () use ($id) {
        TblContact::where('vendor_id', $id)->delete();
        TblBilling::where('vendor_id', $id)->delete();
        TblShipping::where('vendor_id', $id)->delete();
        Tblvendor::where('id', $id)->delete();
    });

    return response()->json(['message' => 'Vendor and all linked records deleted successfully']);
}

public function toggleVendorStatus(Request $request)
{
    $vendor = Tblvendor::findOrFail($request->id);
    $vendor->active_status = $vendor->active_status == 0 ? 1 : 0;
    $vendor->save();

    return response()->json([
        'success' => true,
        'active_status' => $vendor->active_status,
        'message' => $vendor->active_status == 0 ? 'Vendor marked as Active' : 'Vendor marked as Inactive',
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
                'Shipping Country','Shipping Code','Shipping Phone','Shipping Fax','Vendor Bank Holder Name','Vendor Bank Account Number','Vendor Bank Name','Vendor Bank IFSC Code','GSTIN Number'];
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
            // $sheet->setCellValue('AJ' . $row, optional($vendor->bankdetails[0])->account_holder_name);
            // $sheet->setCellValue('AK' . $row, optional($vendor->bankdetails[0])->accont_number);
            // $sheet->setCellValue('AL' . $row, optional($vendor->bankdetails[0])->bank_name);
            // $sheet->setCellValue('AM' . $row, optional($vendor->bankdetails[0])->ifsc_code);
            $bank = $vendor->bankdetails->first() ?? null;

            $sheet->setCellValue('AJ' . $row, $bank->account_holder_name ?? '-');
            $sheet->setCellValue('AK' . $row, $bank->accont_number ?? '-');
            $sheet->setCellValue('AL' . $row, $bank->bank_name ?? '-');
            $sheet->setCellValue('AM' . $row, $bank->ifsc_code ?? '-');
            $sheet->setCellValue('AM' . $row, $vendor->gst_number ?? '-');


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
                            ->where('delete_status',0)
                            ->paginate(10, ['*'], $pageName);
                $TblQuotation->appends(['id' => $id, 'type' => $type]);
                break;

            case 'purchase':
                $TblPurchaseorder = TblPurchaseorder::with(['BillLines', 'Tblvendor', 'TblBilling'])
                            ->where('vendor_id', $id)
                            ->orderBy('id', 'desc')
                            ->where('delete_status',0)
                            ->paginate(10, ['*'], $pageName);
                $TblPurchaseorder->appends(['id' => $id, 'type' => $type]);
                break;

            case 'bill':
                $Tblbill = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])
                    ->where('vendor_id', $id)
                    ->orderBy('id', 'desc')
                    ->where('delete_status',0)
                    ->paginate(10, ['*'], $pageName);

                $Tblbill->appends(['id' => $id, 'type' => $type]);
                break;

            case 'billpay':
                $Tblbillpay = Tblbillpay::with(['BillLines','BillLines.Bill','BillLines.BillLines','Tblvendor','TblBilling','Tblbankdetails'])
                            ->where('vendor_id', $id)
                            ->orderBy('id', 'desc')
                            ->where('delete_status',0)
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
        ->where('delete_status',0)
        ->orderBy('id', 'desc');
    $quotationCount = $TblQuotation->count();
    $TblQuotation = $TblQuotation->paginate(10, ['*'], 'quotation_page');


    $TblPurchaseorder = TblPurchaseorder::with(['BillLines', 'Tblvendor', 'TblBilling'])
        ->where('vendor_id', $id)
        ->where('delete_status',0)
        ->orderBy('id', 'desc');
    $purchaseOrderCount = $TblPurchaseorder->count();
    $TblPurchaseorder = $TblPurchaseorder->paginate(10, ['*'], 'purchase_page');


    $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])
        ->where('vendor_id', $id)
        ->where('delete_status',0)
        ->orderBy('id', 'desc');
    $billcount = $query->count();
    $Tblbill = $query->paginate(10, ['*'], 'bill_page');

    // 👉 Calculate totals for this vendor (across all bills)
    $totalAmountSum = Tblbill::where('vendor_id', $id)->where('delete_status',0)->sum('grand_total_amount');
    $partialPaidSum = Tblbill::where('vendor_id', $id)->where('delete_status',0)->sum('partially_payment');
    $dueAmountSum = $totalAmountSum - $partialPaidSum;


    $Tblbillpay = Tblbillpay::with(['BillLines','BillLines.Bill','BillLines.BillLines','Tblvendor','TblBilling','Tblbankdetails'])
    ->where('vendor_id', $id)
    ->where('delete_status',0)
    ->orderBy('id', 'desc');
    $billPayCount = $Tblbillpay->count();
    $Tblbillpay = $Tblbillpay->paginate(10, ['*'], 'billpay_page');
    $billtotalAmountSum = Tblbillpay::where('vendor_id', $id)->where('delete_status',0)->sum('amount_used');


    $Tblgrn = Tblgrn::with(['BillLines','Tblvendor','TblBilling'])
        ->where('vendor_id', $id)
        ->orderBy('id', 'desc');
    $grnCount = $Tblgrn->count();
    $Tblgrn = $Tblgrn->paginate(10, ['*'], 'grn_page');


    // ✅ Final data to send to view
    $viewData = [
        'TblQuotation' => $TblQuotation,
        'TblPurchaseorder' => $TblPurchaseorder,
        'Tblgrn' => $Tblgrn,
        'Tblbill' => $Tblbill,
        'Tblbillpay' => $Tblbillpay,
        'id' => $id,
        'type' => $type,
        'perPage' => 10,
        'totalAmountSum' => $totalAmountSum,
        'partialPaidSum' => $partialPaidSum,
        'dueAmountSum' => $dueAmountSum,
        'billcount' => $billcount,
        'quotationCount' => $quotationCount,
        'purchaseOrderCount' => $purchaseOrderCount,
        'billPayCount' => $billPayCount,
        'grnCount' => $grnCount,
        'billtotalAmountSum' => $billtotalAmountSum,
    ];
}
    // dd($billcount);
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
public function gettranscationvendorpagination(Request $request)
{
    $id = $request->id;
    $type = $request->type;
    $page = $request->page ?? 1;
    $perPage = $request->get('per_page', 10);

    // Set pagination page name based on type (important for multiple paginators on same page)
    $pageName = $type ? $type . '_page' : 'page';

    $data = null;

    switch ($type) {
        case 'quotation':
            $data = TblQuotation::with(['BillLines', 'Tblvendor', 'TblBilling'])
                ->where('vendor_id', $id)
                ->where('delete_status',0)
                ->orderByDesc('id')
                ->paginate($perPage, ['*'], $pageName, $page);
            break;

        case 'purchase':
            $data = TblPurchaseorder::with(['BillLines', 'Tblvendor', 'TblBilling'])
                ->where('vendor_id', $id)
                ->where('delete_status',0)
                ->orderByDesc('id')
                ->paginate($perPage, ['*'], $pageName, $page);
            break;

        case 'bill':
            $data = Tblbill::with(['BillLines', 'Tblvendor', 'TblBilling', 'Tblbankdetails'])
                ->where('vendor_id', $id)
                ->where('delete_status',0)
                ->orderByDesc('id')
                ->paginate($perPage, ['*'], $pageName, $page);
            break;

        case 'billpay':
            $data = Tblbillpay::with(['BillLines', 'BillLines.Bill', 'BillLines.BillLines', 'Tblvendor', 'TblBilling', 'Tblbankdetails'])
                ->where('vendor_id', $id)
                ->where('delete_status',0)
                ->orderByDesc('id')
                ->paginate($perPage, ['*'], $pageName, $page);
            break;

        case 'grn':
            $data = Tblgrn::with(['BillLines', 'Tblvendor', 'TblBilling'])
                ->where('vendor_id', $id)
                ->orderByDesc('id')
                ->paginate($perPage, ['*'], $pageName, $page);
            break;

        default:
            return response()->json(['html' => '<p>Invalid table type</p>']);
    }

    // Keep parameters for pagination link generation
    $data->appends([
        'id' => $id,
        'type' => $type,
        'per_page' => $perPage,
    ]);

    // Map type to blade variable names
    $viewData = [
        'TblQuotation' => $type === 'quotation' ? $data : null,
        'TblPurchaseorder' => $type === 'purchase' ? $data : null,
        'Tblgrn' => $type === 'grn' ? $data : null,
        'Tblbill' => $type === 'bill' ? $data : null,
        'Tblbillpay' => $type === 'billpay' ? $data : null,
        'id' => $id,
        'type' => $type,
        'perPage' => $perPage,
    ];
    // dd($type);
    if ($request->ajax()) {
        $viewName = 'vendor.transactionvendor.partials.' . $type . '_table';
        try {
            $html = view($viewName, $viewData)->render();
        } catch (\Exception $e) {
            $html = '<p>Error loading table: ' . $e->getMessage() . '</p>';
        }

        return response()->json([
            'html' => $html,
            'pagination' => (string) $data->appends(['id' => $id, 'type' => $type])->links('pagination::bootstrap-4'),
        ]);
    }


    // Fallback (non-AJAX load)
    // return view('vendor.partials.transcation_vendor', $viewData);
}

public function getvendorchart(Request $request)
{
    $id = $request->id;
    $financialYear = $request->input('financial_year', '');

    // Indian FY: April–March. e.g. "2024-25" => 2024-04-01 to 2025-03-31
    if (preg_match('/^(\d{4})-(\d{2})$/', $financialYear, $m)) {
        $startYear = (int) $m[1];
        $endYear = $startYear + 1;
        $fyStart = Carbon::createFromDate($startYear, 4, 1)->startOfDay();
        $fyEnd = Carbon::createFromDate($endYear, 3, 31)->endOfDay();
    } else {
        $now = Carbon::now();
        $fyStart = $now->month >= 4
            ? Carbon::createFromDate($now->year, 4, 1)->startOfDay()
            : Carbon::createFromDate($now->year - 1, 4, 1)->startOfDay();
        $fyEnd = $fyStart->copy()->addYear()->subDay()->endOfDay();
    }

    $query = Tblbill::where('vendor_id', $id)->where('delete_status', 0)
        ->whereBetween('created_at', [$fyStart, $fyEnd]);

    $totals = (clone $query)
        ->selectRaw("MONTH(created_at) as mn, SUM(grand_total_amount) as total")
        ->groupBy('mn')
        ->pluck('total', 'mn');

    $fyMonths = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];
    $monthNums = [4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3];
    $data = [];
    foreach ($monthNums as $mn) {
        $data[] = isset($totals[$mn]) ? (float) $totals[$mn] : 0;
    }

    $totalIncome = (clone $query)->sum('grand_total_amount');
    $balance = Tblbill::where('vendor_id', $id)->where('delete_status', 0)->sum('partially_payment');

    return response()->json([
        'months'        => $fyMonths,
        'amounts'       => $data,
        'balance'       => $balance,
        'total_income'  => $totalIncome,
        'fy_label'      => $fyStart->format('Y') . '-' . $fyEnd->format('y'),
    ]);
}


public function showStatement(Request $request)
{
    // dd($request);
    $id = $request->id;
    $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])->where('delete_status',0)
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

// public function statementprint(Request $request, $id)
// {

//     $billsQuery = Tblbill::with(['BillLines', 'Tblvendor', 'TblBilling', 'Tblbankdetails'])->where('delete_status',0)
//         ->where('vendor_id', $id);
//     if ($request->filled('date_from') && $request->filled('date_to')) {
//         $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
//         $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
//     } else {
//         $from = Carbon::now()->startOfMonth();
//         $to   = Carbon::now()->endOfMonth();
//     }

//     $billsQuery->whereBetween('created_at', [$from, $to]);
//     $bills = $billsQuery->orderBy('id', 'asc')->get();
//     if ($bills->isEmpty()) {
//         abort(404, 'No bills found in this period.');
//     }
//     $vendor  = $bills->first()->Tblvendor ?? null;
//     $billing = $bills->first()->TblBilling ?? null;

//     $billed  = $bills->sum('grand_total_amount');
//     $paid    = $bills->sum('tax_amount');
//     $balance = $billed - $paid;

//     $runningBalance = 0;
//     $transactions   = [];
//     foreach ($bills as $bill) {
//         $grandTotal = (float) $bill->grand_total_amount;
//         $runningBalance += $grandTotal;

//         $transactions[] = [
//             'date'     => $bill->bill_date ? $bill->bill_date : $bill->created_at->format('d/m/Y'),
//             'type'     => 'Bill',
//             'details'  => ($bill->bill_gen_number ?? $bill->bill_number ?? '') .
//                          ($bill->due_date ? ' - due on ' . $bill->due_date : ''),
//             'amount'   => $grandTotal,
//             'payment'  => 0,
//             'balance'  => $runningBalance
//         ];

//         if (!empty($bill->tax_type) && (float) $bill->tax_amount > 0) {
//             $runningBalance -= (float) $bill->tax_amount;
//             $transactions[] = [
//                 'date'     => $bill->bill_date ? $bill->bill_date : $bill->created_at->format('d/m/Y'),
//                 'type'     => $bill->tax_type ?? 'TDS',
//                 'details'  => 'Bill Number - ' . ($bill->bill_number ?? $bill->bill_gen_number ?? $bill->id),
//                 'amount'   => 0,
//                 'payment'  => (float) $bill->tax_amount,
//                 'balance'  => $runningBalance
//             ];
//         }
//     }

//     $pdf = new TCPDF();
//     $pdf->setPrintHeader(false);
//     $pdf->setPrintFooter(false);
//     $pdf->SetMargins(15, 15, 15);
//     $pdf->AddPage();

//     include(resource_path('views/vendor/statement/statementprint.blade.php'));
//     if($request->download=='pdf'){
//         return response($pdf->Output('statement.pdf', 'S'))
//                 ->header('Content-Type', 'application/pdf')
//                 ->header('Content-Disposition', 'attachment; filename="statement.pdf"');
//     }else{
//         return response($pdf->Output('statement.pdf', 'S'))
//             ->header('Content-Type', 'application/pdf');
//     }

// }


public function statementprint(Request $request, $id)
{
    $billsQuery = Tblbill::with(['BillLines', 'Tblvendor', 'TblBilling', 'Tblbankdetails'])
        ->where('delete_status', 0)
        ->where('vendor_id', $id);

    if ($request->filled('date_from') && $request->filled('date_to')) {
        // Fix: Use createFromFormat to parse the custom date format
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

        // Fix: Handle bill_date if it exists and format it properly
        $billDate = '';
        if ($bill->bill_date) {
            try {
                // Try to parse bill_date if it's in d/m/Y format
                $billDate = Carbon::createFromFormat('d/m/Y', $bill->bill_date)->format('d/m/Y');
            } catch (\Exception $e) {
                // If parsing fails, use the original or created_at
                $billDate = $bill->bill_date ?? $bill->created_at->format('d/m/Y');
            }
        } else {
            $billDate = $bill->created_at->format('d/m/Y');
        }

        $transactions[] = [
            'date'     => $billDate,
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
                'date'     => $billDate,
                'type'     => $bill->tax_type ?? 'TDS',
                'details'  => 'Bill Number - ' . ($bill->bill_number ?? $bill->bill_gen_number ?? $bill->id),
                'amount'   => 0,
                'payment'  => (float) $bill->tax_amount,
                'balance'  => $runningBalance
            ];
        }
    }

    // Prepare data for the view
    $data = [
        'bills' => $bills,
        'vendor' => $vendor,
        'billing' => $billing,
        'billed' => $billed,
        'paid' => $paid,
        'balance' => $balance,
        'transactions' => $transactions,
        'from' => $from,
        'to' => $to,
        'request' => $request
    ];

    // Load the view and generate PDF
    $pdf = PDF::loadView('vendor.statement.statementprint', $data);
    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'defaultFont' => 'dejavusans',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true
    ]);

    // Handle PDF output
    if ($request->download == 'pdf') {
        return $pdf->download('statement-'.($vendor->display_name ?? 'vendor').'-'.$from->format('d-m-Y').'.pdf');
    } else {
        return $pdf->stream('statement-'.($vendor->display_name ?? 'vendor').'-'.$from->format('d-m-Y').'.pdf');
    }
}
//bill making

    public function getbill(Request $request)
    {
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'asc')->get();
        $Tblcategory = BillCategory::orderBy('id', 'asc')->get();
        $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails','Purchase','Purchase.quotation','billPayments','TblTDSsection.section','category'])->where('delete_status',0)->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            // $query->whereBetween('created_at', [$from, $to]);
            $query->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
        }
        if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Karnataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }
        if ($request->filled('zone_id')) {
            $ids = explode(',', $request->zone_id);
            $query->whereIn('zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $ids = explode(',', $request->branch_id);
            $query->whereIn('branch_id', $ids);
        }
        if ($request->filled('company_id')) {
            $ids = explode(',', $request->company_id);
            $query->whereIn('company_id', $ids);
        }
        if ($request->filled('vendor_id')) {
            $ids = explode(',', $request->vendor_id);
            $query->whereIn('vendor_id', $ids);
        }
        if ($request->filled('status_name')) {
            $statuses = array_map('trim', explode(',', $request->status_name));
            foreach ($statuses as $status) {
                if (strtolower($status) === 'asset') {
                    $query->where('asset_status', 1);
                } elseif (strtolower($status) === 'overdue') {
                    $today = now()->startOfDay();
                    $query->where(function($q) use ($today) {
                        $q->whereRaw("STR_TO_DATE(due_date, '%d/%m/%Y') < ?", [$today])
                          ->where('bill_status', '!=', 'paid');
                    });
                } else {
                    $query->where(function ($q) use ($status) {
                        $q->orWhere('status', 'LIKE', '%' . $status . '%')
                          ->orWhere('bill_status', 'LIKE', '%' . $status . '%');
                    });
                }
            }
        }
        if ($request->filled('category_id')) {
            $ids = explode(',', $request->category_id);
            $query->whereIn('bill_category', $ids);
        }
        if ($request->filled('nature_id')) {
            $ids = explode(',', $request->nature_id);

            $query->whereHas('BillLines', function ($q) use ($ids) {
                $q->whereIn('account_id', $ids);
            });
        }
        if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                ->orWhere('zone_name', 'like', "%{$search}%")
                ->orWhere('branch_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('bill_gen_number', 'like', "%{$search}%")
                ->orWhere('bill_number', 'like', "%{$search}%")
                ->orWhere('order_number', 'like', "%{$search}%")
                ->orWhere('bill_date', 'like', "%{$search}%")
                ->orWhere('sub_total_amount', 'like', "%{$search}%")
                ->orWhere('tax_type', 'like', "%{$search}%")
                ->orWhere('grand_total_amount', 'like', "%{$search}%")
                ->orWhere('due_date', 'like', "%{$search}%");
                // ->orWhere('company_name', 'like', "%{$search}%");
            });
        }
        // Compute filtered stats before pagination
        $filteredAll = (clone $query)->get();
        $today = now()->startOfDay();
        $filteredStats = [
            'total'              => $filteredAll->count(),
            'total_amount'       => $filteredAll->sum('grand_total_amount'),
            'paid'               => $filteredAll->where('bill_status', 'paid')->count(),
            'paid_amount'        => $filteredAll->where('bill_status', 'paid')->sum('grand_total_amount'),
            'partially'          => $filteredAll->whereIn('bill_status', ['partially payed','partially_payed','Partially Payed'])->count(),
            'partially_amount'   => $filteredAll->whereIn('bill_status', ['partially payed','partially_payed','Partially Payed'])->sum('grand_total_amount'),
            'asset'              => $filteredAll->where('asset_status', 1)->count(),
            'asset_amount'       => $filteredAll->where('asset_status', 1)->sum('grand_total_amount'),
            'overdue' => $filteredAll->filter(function($b) use ($today) {
                try {
                    $dueDate = $b->due_date
                        ? \Carbon\Carbon::createFromFormat('d/m/Y', $b->due_date)->startOfDay()
                        : \Carbon\Carbon::create(2100, 1, 1);
                } catch (\Exception $e) { return false; }
                return strtolower($b->bill_status ?? '') !== 'paid' && $dueDate->lt($today);
            })->count(),

            'overdue_amount' => $filteredAll->filter(function($b) use ($today) {
                try {
                    $dueDate = $b->due_date
                        ? \Carbon\Carbon::createFromFormat('d/m/Y', $b->due_date)->startOfDay()
                        : \Carbon\Carbon::create(2100, 1, 1);
                } catch (\Exception $e) { return false; }
                return strtolower($b->bill_status ?? '') !== 'paid' && $dueDate->lt($today);
            })->sum('grand_total_amount'),
        ];

        $billlist = $query->paginate($perPage)->appends($request->all());
        $allBills = Tblbill::orderBy('id','desc')->where('delete_status',0)->get();

        // Overall stats (unfiltered)
        $stats = [
            'total'            => $allBills->count(),
            'total_amount'     => $allBills->sum('grand_total_amount'),
            'paid'             => $allBills->where('bill_status', 'paid')->count(),
            'paid_amount'      => $allBills->where('bill_status', 'paid')->sum('grand_total_amount'),
            'partially'        => $allBills->whereIn('bill_status', ['partially payed','partially_payed','Partially Payed'])->count(),
            'partially_amount' => $allBills->whereIn('bill_status', ['partially payed','partially_payed','Partially Payed'])->sum('grand_total_amount'),
            'asset'            => $allBills->where('asset_status', 1)->count(),
            'asset_amount'     => $allBills->where('asset_status', 1)->sum('grand_total_amount'),
            'overdue'          => $allBills->filter(function($b) use ($today) {
                                        try { $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $b->due_date ?? '01/01/2100')->startOfDay(); }
                                        catch(\Exception $e) { return false; }
                                        return strtolower($b->bill_status ?? '') !== 'paid' && $dueDate->lt($today);
                                    })->count(),
            'overdue_amount'   => $allBills->filter(function($b) use ($today) {
                                        try { $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $b->due_date ?? '01/01/2100')->startOfDay(); }
                                        catch(\Exception $e) { return false; }
                                        return strtolower($b->bill_status ?? '') !== 'paid' && $dueDate->lt($today);
                                    })->sum('grand_total_amount'),
        ];

        if ($request->ajax()) {
            $html = view('vendor.partials.table.bill_rows', compact('billlist','perPage','limit_access'))->render();
            return response()->json(['html' => $html, 'stats' => $filteredStats]);
        }

        return view('vendor.bill_dashboard', [
            'admin'        => $admin,
            'limit_access' => $limit_access,
            'locations'    => $locations,
            'billlist'     => $billlist,
            'allBills'     => $allBills,
            'stats'        => $stats,
            'perPage'      => $perPage,
            'TblZonesModel'=> $TblZonesModel,
            'Tblcompany'   => $Tblcompany,
            'Tblvendor'    => $Tblvendor,
            'Tblaccount'   => $Tblaccount,
            'Tblcategory'  => $Tblcategory,
        ]);
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
    //     $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
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
        // $count = Tblbill::count();
        // $nextNumber = $count + 1;
        // $serial = 'BILL-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $lastRecord = Tblbill::orderBy('id', 'DESC')->where('company_id',1)->first();
        // dd(isset($lastRecord->bill_gen_number));
        if ($lastRecord && isset($lastRecord->bill_gen_number)) {
            $lastNumber = (int) str_replace('BILL-', '', $lastRecord->bill_gen_number);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $serial = 'BILL-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        // dd($serial);
        $locations = TblLocationModel::all();
        $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10); // for TDS modal table
        $tdstax    = Tbltdstax::orderBy('id', 'desc')->get();        // for TDS dropdown (all records)
        $Tbltcstax = Tbltcstax::all();
        $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);
         $gsttax = Tblgsttax::orderBy('id', 'desc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'desc')->get();
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tbldepartment = Department::orderBy('id', 'asc')->get();
        $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->where('active_status', 0)->get();
        $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        // dd($TblZonesModel,$Tblaccount);
        $purchaselist = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling'])
            ->where('approval_status', 1)
            ->where('bill_status', 0)
            ->where('delete_status',0)
            ->orderBy('id','desc')
            ->paginate($perPage);

        $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])
            ->where('approval_status', 1)
            ->where('po_status', 0)
            ->where('delete_status',0)
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

        // Load category data for dropdowns
        $billcategories = BillCategory::orderBy('name', 'asc')->get();

        if ($id !== "") {
            $bill = Tblbill::with(['TblBilling', 'BillLines', 'Tblvendor', 'Quotation', 'Purchase'])->where('delete_status',0)->where('id',$id)->get();
            if($type == 'edit'){
                return view('vendor.bill_create', compact(
                    'admin','locations','vendor','customer',
                    'Tbltdstax','tdstax','Tbltcstax','Tblgsttax','Tblaccount','Tbltdssection',
                    'TblQuotation','TblZonesModel','purchaselist','bill','perPage','Tblcompany','gsttax','type', 'billcategories', 'Tbldepartment'
                ));
            }else{
                return view('vendor.bill_create', compact(
                    'admin','locations','vendor','customer',
                    'Tbltdstax','tdstax','Tbltcstax','Tblgsttax','Tblaccount','Tbltdssection',
                    'TblQuotation','TblZonesModel','serial','purchaselist','bill','perPage','Tblcompany','gsttax','type', 'billcategories', 'Tbldepartment'
                ));

            }
        } else {
            return view('vendor.bill_create', compact(
                'admin','locations','vendor','customer',
                'Tbltdstax','tdstax','Tbltcstax','Tblgsttax','Tblaccount','Tbltdssection',
                'TblQuotation','TblZonesModel','purchaselist','serial','perPage','Tblcompany','gsttax','type','billcategories', 'Tbldepartment'
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
        $tdstax    = Tbltdstax::orderBy('id', 'desc')->get(); // all records for dropdown

        return response()->json([
            'success'   => true,
            'tdstax'    => $Tbltdstax,    // paginated — for modal table HTML
            'tdstax_all'=> $tdstax,        // all records — for dropdown list
            'message'   => $id!==""? 'TDS Tax Data Updated successfully!':'TDS Tax Data saved successfully!'
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
    private function generateDocumentNumber($companyId, $companyName, $type)
    {
        // normalize company name
        $normalizedName = strtoupper(preg_replace("/[^A-Z0-9]/", "", $companyName));
        $ivfCompany = strtoupper(preg_replace("/[^A-Z0-9]/", "", "Dr.Aravind's IVF Private Limited"));

        $isIvfCompany = ($normalizedName === $ivfCompany);

        // map type to model + column + code
        $typeConfig = [
            'po' => [
                'model'  => \App\Models\TblPurchaseorder::class,
                'column' => 'purchase_gen_order',
                'code'   => 'PO',
            ],
            'bill' => [
                'model'  => \App\Models\Tblbill::class,
                'column' => 'bill_gen_number',
                'code'   => 'BILL',
            ],
            'quotation' => [
                'model'  => \App\Models\TblQuotation::class,
                'column' => 'quotation_gen_no',
                'code'   => 'QO',
            ],
        ];

        if (!isset($typeConfig[$type])) {
            throw new \Exception('Invalid document type');
        }

        $modelClass = $typeConfig[$type]['model'];
        $column     = $typeConfig[$type]['column'];
        $docCode    = $typeConfig[$type]['code'];

        // prefix (skip for IVF)
        $prefix = $isIvfCompany ? '' : $this->getCompanyPrefix($companyName);

        // get last record from correct table
        $lastRecord = $modelClass::where('company_id', $companyId)
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastRecord && $lastRecord->$column) {
            preg_match('/(\d+)$/', $lastRecord->$column, $matches);
            $nextNumber = isset($matches[1]) ? ((int)$matches[1] + 1) : 1;
        } else {
            $nextNumber = 1;
        }
        // dd($nextNumber);
        // build document number
        if ($isIvfCompany) {
            return $docCode . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        return $prefix . '-' . $docCode . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    private function getCompanyPrefix(string $companyName): string
    {
        // Remove special characters
        $cleanName = preg_replace('/[^A-Za-z0-9 ]/', '', $companyName);

        // Split words
        $words = array_filter(explode(' ', $cleanName));

        // Remove common useless words
        $skipWords = [
            'PRIVATE', 'LIMITED', 'LTD', 'PVT',
            'HOSPITAL', 'HOSPITALS',
            'CLINIC', 'CLINICS',
            'CENTER', 'CENTRE',
            'MEDICAL', 'HEALTH',
            'CARE', 'SERVICES'
        ];

        $prefix = '';

        foreach ($words as $word) {
            $word = strtoupper($word);

            if (!in_array($word, $skipWords)) {
                $prefix .= $word[0]; // take first letter
            }

            // Limit prefix length to 3
            if (strlen($prefix) >= 3) {
                break;
            }
        }

        // Fallback if still empty
        if (strlen($prefix) === 0) {
            $prefix = strtoupper(substr($cleanName, 0, 3));
        }

        return $prefix;
    }

    public function savebill(Request $request)
    {
        // dd($request);
        $isUpdate = $request->filled('id');
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
            'order_number' => $request->order_number,
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            'zone_id' => $request->zone_id,
            'zone_name' => $request->zone,
            'department_id' => $request->department_id,
            'branch_name' => $request->branch,
            'branch_id' => $request->branch_id,
            'company_name' => $request->company_name,
            'company_id' => $request->company_id,
            'bill_category' => $request->bill_category,
            'payment_terms' => $request->payment_terms,
            'subject' => $request->subject,
            'discount_percent' => $request->discount_percent,
            'discount_type' => $request->discount_type,
            'discount_amount' => $this->cleanCurrency($request->discount_amount),
            'discount_tax' => $request->discount_toggle,
            'adjustment_value' => $request->adjustment_value,
            'adjustment_reason' => $request->adjustment_reason,
            'tds_tax_id' => $request->tds_tax_id,
            'tcs_tax_id' => $request->tcs_tax_id,
            'tax_type' => $request->tax_type,
            'tax_name' =>  $request->tds_tax_name ?? $request->tcs_tax_name,
            'tax_rate' =>  $request->tds_tax_selected ?? $request->tcs_tax_selected ,
            'tax_amount' => $this->cleanCurrency($request->tax_amount),
            'export_name' => $request->export_name,
            'export_amount' => $request->export_amount,
            'timeline_date' => $request->timeline_date,
            'loading_unloading_name' => $request->loading_unloading_name,
            'loading_unloading_amount' => $request->loading_unloading_amount,
            'esi_type' => $request->esi_type,
            'esi_value' => $request->esi_value,
            'pf_type' => $request->pf_type,
            'pf_value' => $request->pf_value,
            'other_type' => $request->other_type,
            'other_value' => $request->other_value,
            'other_reason' => $request->other_reason,
            'esi_amount' => $this->cleanCurrency($request->esi_amount),
            'pf_amount' => $this->cleanCurrency($request->pf_amount),
            'other_amount' => $this->cleanCurrency($request->other_amount),
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
        if ($request->filled('quotation_id')) {
            TblQuotation::where('id', $request->quotation_id)->update([
                    'bill_status' => 1
                ]);
        }
        if ($request->filled('purchase_id')) {
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

            $data['bill_gen_number'] = $request->bill_gen_number;
            $bill = Tblbill::findOrFail($request->id);
            // Keep linkage when the edit form does not post hidden quotation/purchase IDs (e.g. relation missing on load).
            if (! $request->filled('quotation_id')) {
                $data['quotation_id'] = $bill->quotation_id;
            }
            if (! $request->filled('purchase_id')) {
                $data['purchase_id'] = $bill->purchase_id;
            }
            $grandTotal = (float) $this->cleanCurrency($request->grand_total_amount);
            $partial    = (float) ($bill->partially_payment ?? 0);

            $balanceamount = $grandTotal - $partial;
            $data['balance_amount'] = max(0, $balanceamount);

            // Recalculate bill_status based on updated grand_total vs payments made
            if ($balanceamount <= 0) {
                $data['bill_status'] = 'Paid';
            } elseif ($partial > 0) {
                $data['bill_status'] = 'Partially Payed';
            } else {
                $data['bill_status'] = 'Due to Pay';
            }
            // ── Append to edit_history JSON column ──
            $existingHistory = json_decode($bill->edit_history ?? '[]', true) ?: [];
            $roles = [1=>'Superadmin',2=>'Zonal Admin',3=>'Admin',4=>'Auditor',5=>'User'];
            $existingHistory[] = [
                'edited_by' => $admin->user_fullname ?? $admin->email,
                'role'      => $roles[$admin->access_limits] ?? 'User',
                'edited_at' => now()->format('d/m/Y h:i A'),
                'status'    => $request->save_status ?? $bill->status,
                'amount'    => $request->grand_total_amount ?? $bill->grand_total_amount,
            ];
            $data['edit_history'] = json_encode($existingHistory);
            // dd($data);
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
            $incomingLineIds = collect($request->linesdata ?? [])
                ->map(function ($row) {
                    if (! is_array($row)) {
                        return null;
                    }
                    $p = $this->prepareVendorLinePayload($row);

                    return $p['line_id'] ?? null;
                })
                ->filter()
                ->values()
                ->toArray();
            // Delete removed rows
            TblBillLines::where('bill_id', $bill_id)
                ->whereNotIn('id', $incomingLineIds)
                ->delete();

            // Update or insert contact persons
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    if (! is_array($linesData)) {
                        continue;
                    }
                    $prepared = $this->prepareVendorLinePayload($linesData);
                    if ($prepared === null) {
                        continue;
                    }
                    $linesDatas = array_merge($prepared['columns'], [
                        'bill_id' => $bill_id,
                        'updated_at' => $now,
                    ]);

                    if (! empty($prepared['line_id'])) {
                        TblBillLines::where('id', $prepared['line_id'])
                            ->where('bill_id', $bill_id)
                            ->update($linesDatas);
                    } else {
                        $linesDatas['created_at'] = $now;
                        TblBillLines::create($linesDatas);
                    }
                }
            }


        } else {
            $billNumber = $this->generateDocumentNumber(
                $request->company_id,
                $request->company_name,
                'bill'
            );
            $data['bill_gen_number'] = $billNumber;
            // Create new customer
            $data['bill_status']='Due to Pay';

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
                    if (! is_array($linesData)) {
                        continue;
                    }
                    $prepared = $this->prepareVendorLinePayload($linesData);
                    if ($prepared === null) {
                        continue;
                    }
                    $linesDatas = array_merge($prepared['columns'], [
                        'bill_id' => $bill_id,
                        'created_at' => $now,
                    ]);
                    TblBillLines::create($linesDatas);
                }
            }

        }
         return response()->json([
            'success' => true,
            'restore_filters' => $isUpdate ? true : false,
            'message' => $isUpdate ? 'Bill Data updated successfully!' : 'Bill Data saved successfully!'
        ]);
    }

    public function AssetUpdateStatus(Request $request)
    {
        // dd($request);
        $bill = Tblbill::find($request->id);

        if ($bill) {
            $bill->asset_status = 1;
            $bill->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
    public function getasset(Request $request)
    {
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'asc')->get();
        $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails','Purchase','Purchase.quotation','billPayments'])->orderBy('id', 'desc')->where('delete_status',0)->where('asset_status',1);

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            // $query->whereBetween('created_at', [$from, $to]);
            $query->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
        }
        if ($request->filled('zone_id')) {
            $ids = explode(',', $request->zone_id);
            $query->whereIn('zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $ids = explode(',', $request->branch_id);
            $query->whereIn('branch_id', $ids);
        }
        if ($request->filled('company_id')) {
            $ids = explode(',', $request->company_id);
            $query->whereIn('company_id', $ids);
        }
        if ($request->filled('vendor_id')) {
            $ids = explode(',', $request->vendor_id);
            $query->whereIn('vendor_id', $ids);
        }
        if ($request->filled('status_name')) {
            $statuses = array_map('trim', explode(',', $request->status_name));
            $query->where(function ($q) use ($statuses) {
                foreach ($statuses as $status) {
                    $q->orWhere('status', 'LIKE', '%' . $status . '%');
                }
            });
        }
        if ($request->filled('nature_id')) {
            $ids = explode(',', $request->nature_id);

            $query->whereHas('BillLines', function ($q) use ($ids) {
                $q->whereIn('account_id', $ids);
            });
        }
        if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                ->orWhere('zone_name', 'like', "%{$search}%")
                ->orWhere('branch_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('bill_gen_number', 'like', "%{$search}%")
                ->orWhere('bill_number', 'like', "%{$search}%")
                ->orWhere('order_number', 'like', "%{$search}%")
                ->orWhere('bill_date', 'like', "%{$search}%")
                ->orWhere('sub_total_amount', 'like', "%{$search}%")
                ->orWhere('tax_type', 'like', "%{$search}%")
                ->orWhere('grand_total_amount', 'like', "%{$search}%")
                ->orWhere('due_date', 'like', "%{$search}%");
                // ->orWhere('company_name', 'like', "%{$search}%");
            });
        }
        // Stats calculated from filtered query (before stat_filter is applied)
        $filteredAssetQuery = clone $query;
        $stats = [
            'total'        => (clone $filteredAssetQuery)->count(),
            'paid'         => (clone $filteredAssetQuery)->where('bill_status', 'paid')->count(),
            'pending'      => (clone $filteredAssetQuery)->where(function ($q) {
                                  $q->where('bill_status', '!=', 'paid')->orWhereNull('bill_status');
                              })->count(),
            'total_amount' => (clone $filteredAssetQuery)->sum('grand_total_amount'),
        ];

        // Apply stat_filter AFTER stats calculation
        if ($request->filled('stat_filter')) {
            $sf = $request->stat_filter;
            if ($sf === 'paid') {
                $query->where('bill_status', 'paid');
            } elseif ($sf === 'pending') {
                $query->where(function ($q) {
                    $q->where('bill_status', '!=', 'paid')->orWhereNull('bill_status');
                });
            }
        }

        $billlist = $query->paginate($perPage)->appends($request->all());

        if ($request->ajax()) {
            $html = view('vendor.partials.table.bill_rows', compact('billlist','perPage'))->render();
            return response()->json(['html' => $html, 'stats' => $stats]);
        }

        return view('vendor.asset_dashboard', [
            'admin'         => $admin,
            'locations'     => $locations,
            'billlist'      => $billlist,
            'perPage'       => $perPage,
            'TblZonesModel' => $TblZonesModel,
            'Tblcompany'    => $Tblcompany,
            'Tblvendor'     => $Tblvendor,
            'Tblaccount'    => $Tblaccount,
            'stats'         => $stats,
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
// public function getbillprint(Request $request)
// {
//     $billId = $request->id;

//     $bill = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails','TblCompany'])->findOrFail($billId);

//     $pdf = new TCPDF();
//     $pdf->setPrintHeader(false); // disable default header line
//     $pdf->setPrintFooter(false); // optional
//     $pdf->SetMargins(10, 10, 10);
//     $pdf->AddPage();

//     // Execute raw TCPDF drawing code
//     include(resource_path('views/vendor/dynamicprint.blade.php'));

//     return response($pdf->Output('bill_'.$bill->id.'.pdf', 'S'))
//         ->header('Content-Type', 'application/pdf');
// }

// public function getbillpdf(Request $request)
// {
//     $billId = $request->id;
//     $bill = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])->findOrFail($billId);

//     $pdf = new TCPDF();
//     $pdf->setPrintHeader(false);
//     $pdf->setPrintFooter(false);
//     $pdf->SetMargins(10, 10, 10);
//     $pdf->AddPage();

//     // Execute raw TCPDF drawing code
//     include(resource_path('views/vendor/dynamicprint.blade.php'));

//     // Force download the PDF
//     return response($pdf->Output('bill_'.$bill->bill_number.'.pdf', 'D'))
//         ->header('Content-Type', 'application/pdf');
// }
public function getbillprint(Request $request)
{
    $billId = $request->id;
    $bill = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails','TblTDSsection.section'])->findOrFail($billId);
    $pdf = PDF::loadView('vendor.pdf.billprint', compact('bill'));

    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'dejavusans',
        'isPhpEnabled' => true,
        'isFontSubsettingEnabled' => true,
    ]);
    return $pdf->stream('Bill_' . $bill->bill_gen_number . '.pdf');

}
public function getbillpdf(Request $request)
{
    $billId = $request->id;
    $bill = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails','TblTDSsection.section'])->findOrFail($billId);
    $pdf = PDF::loadView('vendor.pdf.billprint', compact('bill'));

    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'dejavusans',
        'isPhpEnabled' => true,
        'isFontSubsettingEnabled' => true,
    ]);

    return $pdf->download('Bill_' . $bill->bill_gen_number . '.pdf');
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

    $import = new billImport();
    Excel::import($import, $request->file('file'));

    $existing = (int) $import->existingCount;
    $inserted = (int) $import->insertedCount;
    $parts = [];
    if ($existing > 0) {
        $parts[] = $existing . ' already existing (skipped)';
    }
    if ($inserted > 0) {
        $parts[] = $inserted . ' imported';
    }
    $message = count($parts) ? implode(', ', $parts) . '.' : 'No new bills to import.';

    return response()->json([
        'status' => 'success',
        'message' => $message,
        'existing_count' => $existing,
        'inserted_count' => $inserted,
    ]);
}
public function billMadetemplate()
    {
        return Excel::download(new BillPaymentTemplateExport, 'billMade_template.xlsx');
    }
public function importbillMadeExcel(Request $request)
{
     $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    Excel::import(new BillPaymentImport, $request->file('file'));

   return response()->json([
        'status' => 'success',
        'message' => 'bill Made data imported successfully!'
    ]);
}

// bill made
 public function getbillmade(Request $request)
    {
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $lastRecord = Tblneft::orderBy('id', 'DESC')->first();
        if ($lastRecord && isset($lastRecord->serial_number)) {
            $lastNumber = (int) str_replace('NEFT-', '', $lastRecord->serial_number);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $serial = 'NEFT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'asc')->get();
        $query = Tblbillpay::with(['BillLines','BillLines.Bill','BillLines.BillLines','BillLines.aleardypay','Tblvendor','TblBilling','Tblbankdetails','bankStatement'])->where('delete_status',0)->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            // $query->whereBetween('created_at', [$from, $to]);
            $query->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
        }
        if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Karnataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }
         if ($request->filled('zone_id')) {
            $ids = explode(',', $request->zone_id);
            $query->whereIn('zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $ids = explode(',', $request->branch_id);
            $query->whereIn('branch_id', $ids);
        }
        if ($request->filled('company_id')) {
            $ids = explode(',', $request->company_id);
            $query->whereIn('company_id', $ids);
        }
        if ($request->filled('vendor_id')) {
            $ids = explode(',', $request->vendor_id);
            $query->whereIn('vendor_id', $ids);
        }
         if ($request->filled('status_name')) {
            $statuses = array_map('trim', explode(',', $request->status_name));
            $query->where(function ($q) use ($statuses) {
                foreach ($statuses as $status) {
                    $q->orWhere('save_status', 'LIKE', '%' . $status . '%');
                    $q->orWhere('bank_statement_status', 'LIKE', '%' . $status . '%');
                }
            });
        }
        if ($request->filled('nature_id')) {
            $ids = array_map('trim', explode(',', $request->nature_id));
            $query->whereHas('BillLines.BillLines', function ($q) use ($ids) {
                $q->whereIn('account_id', $ids);
            });
        }
        if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                ->orWhere('zone_name', 'like', "%{$search}%")
                ->orWhere('branch_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('payment_mode', 'like', "%{$search}%")
                ->orWhere('paid_through', 'like', "%{$search}%")
                ->orWhere('created_at', 'like', "%{$search}%")
                ->orWhere('save_status', 'like', "%{$search}%");
                // ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Filtered stats (before pagination)
        $filteredAll = (clone $query)->get();
        $filteredStats = [
            'total'            => $filteredAll->count(),
            'total_amount'     => $filteredAll->sum('amount_paid'),
            'paid'             => $filteredAll->where('bank_statement_status', 'Paid')->count(),
            'paid_amount'      => $filteredAll->where('bank_statement_status', 'Paid')->sum('amount_paid'),
            'partially'        => $filteredAll->where('bank_statement_status', 'Partially')->count(),
            'partially_amount' => $filteredAll->where('bank_statement_status', 'Partially')->sum('amount_paid'),
            'pending'          => $filteredAll->where('bank_statement_status', 'Pending')->count(),
            'pending_amount'   => $filteredAll->where('bank_statement_status', 'Pending')->sum('amount_paid'),
            'neft'             => $filteredAll->where('payment', 'NEFT')->count(),
            'neft_amount'      => $filteredAll->where('payment', 'NEFT')->sum('amount_paid'),
        ];

        $billpaylist = $query->paginate($perPage)->appends($request->all());
        $allBillpays = Tblbillpay::orderBy('id','desc')->where('delete_status',0)->get();

        // Overall stats (unfiltered)
        $stats = [
            'total'            => $allBillpays->count(),
            'total_amount'     => $allBillpays->sum('amount_paid'),
            'paid'             => $allBillpays->where('bank_statement_status', 'Paid')->count(),
            'paid_amount'      => $allBillpays->where('bank_statement_status', 'Paid')->sum('amount_paid'),
            'partially'        => $allBillpays->where('bank_statement_status', 'Partially')->count(),
            'partially_amount' => $allBillpays->where('bank_statement_status', 'Partially')->sum('amount_paid'),
            'pending'          => $allBillpays->where('bank_statement_status', 'Pending')->count(),
            'pending_amount'   => $allBillpays->where('bank_statement_status', 'Pending')->sum('amount_paid'),
            'neft'             => $allBillpays->where('payment', 'NEFT')->count(),
            'neft_amount'      => $allBillpays->where('payment', 'NEFT')->sum('amount_paid'),
        ];

        if ($request->ajax()) {
            $html = view('vendor.partials.table.bill_made_rows', compact('billpaylist','perPage','limit_access'))->render();
            return response()->json(['html' => $html, 'stats' => $filteredStats]);
        }

        return view('vendor.bill_made_dashboard', [
            'admin'         => $admin,
            'limit_access'  => $limit_access,
            'locations'     => $locations,
            'billpaylist'   => $billpaylist,
            'allBillpays'   => $allBillpays,
            'stats'         => $stats,
            'serial'        => $serial,
            'perPage'       => $perPage,
            'TblZonesModel' => $TblZonesModel,
            'Tblvendor'     => $Tblvendor,
            'Tblcompany'    => $Tblcompany,
            'Tblaccount'    => $Tblaccount,
        ]);
    }
    public function getbillmadecreate()
    {
        $id="";
        if(isset($_GET['id'])){
            $id=$_GET['id'];
        }
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        // $count = Tblbillpay::count();
        // $nextNumber = $count + 1;
        // $serial = 'PAYMENT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $lastRecord = Tblbillpay::orderBy('id', 'DESC')->first();
        if ($lastRecord && isset($lastRecord->payment_gen_order)) {
            $lastNumber = (int) str_replace('PAYMENT-', '', $lastRecord->payment_gen_order);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $serial = 'PAYMENT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $locations = TblLocationModel::all();
        $Tbltdstax = Tbltdstax::all();
        $Tbltcstax = Tbltcstax::all();
        $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts','bankdetails'])->get();
        $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
         if($id !==""){
            $billpay = Tblbillpay::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('delete_status',0)->where('id',$id)->get();
            return view('vendor.bill_made_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'TblZonesModel' => $TblZonesModel,'billpay' => $billpay,'Tblcompany' => $Tblcompany,'serial' => $serial]);
        }else{
            return view('vendor.bill_made_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'TblZonesModel' => $TblZonesModel,'Tblcompany' => $Tblcompany,'serial' => $serial]);
        }

    }
    public function getDetails(Request $request)
    {
        $query = Tblbill::with(['Tblvendor','BillLines'])->where('delete_status',0)
            ->where('balance_amount', '>', 0);

        // If vendor selected — still filter by vendor
        if ($request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // If user typed search text
        if ($request->filter_search) {

            $search = $request->filter_search;

            $query->where(function($q) use ($search){

                $q->where('bill_number', 'like', "%{$search}%")
                ->orWhere('bill_gen_number', 'like', "%{$search}%")
                ->orWhere('bill_date', 'like', "%{$search}%")
                ->orWhere('due_date', 'like', "%{$search}%")
                ->orWhere('grand_total_amount', 'like', "%{$search}%")
                ->orWhere('balance_amount', 'like', "%{$search}%")
                ->orWhereHas('Tblvendor', function($v) use ($search){
                    $v->where('display_name','like',"%{$search}%");
                });
            });
        }

        $vendor = $query->get();

        if ($vendor->isEmpty()) {
            return response()->json(['vendor' => []]); // safer
        }

        return response()->json([
            'vendor' => $vendor
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
            'payment_gen_order' => $request->payment_gen_order,
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

                    // Tblbill::where('id', $linesData['id'])->update([
                    //     'partially_payment' => $partial,
                    //     'balance_amount' => $balance
                    // ]);
                    if($balance==0){
                        Tblbill::where('id', $linesData['id'])->update([
                            'partially_payment' => $partial,
                            'balance_amount' => $balance,
                            'bill_made_status' => 1,
                            'bill_status' => 'Paid',
                        ]);
                    }else{
                        Tblbill::where('id', $linesData['id'])->update([
                            'partially_payment' => $partial,
                            'balance_amount' => $balance,
                            'bill_status' => 'Partially Payed',
                        ]);
                    }
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
                                    'bill_made_status' => 1,
                                    'bill_status' => 'Paid',
                                ]);
                            }else{
                                Tblbill::where('id', $linesData['id'])->update([
                                    'partially_payment' => $partial,
                                    'balance_amount' => $balance,
                                    'bill_status' => 'Partially Payed',
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
            'restore_filters' => $isUpdate ? true : false,
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
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $TblPoEmail = TblPoEmail::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'asc')->get();

        $query = TblPurchaseorder::with(['BillLines', 'Tblvendor', 'TblBilling','quotation'])->where('delete_status',0)->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            // $query->whereBetween('created_at', [$from, $to]);
            $query->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
        }
        if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Karnataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }
        if ($request->filled('zone_id')) {
            $ids = explode(',', $request->zone_id);
            $query->whereIn('zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $ids = explode(',', $request->branch_id);
            $query->whereIn('branch_id', $ids);
        }
        if ($request->filled('company_id')) {
            $ids = explode(',', $request->company_id);
            $query->whereIn('company_id', $ids);
        }
        if ($request->filled('vendor_id')) {
            $ids = explode(',', $request->vendor_id);
            $query->whereIn('vendor_id', $ids);
        }
        if ($request->filled('status_name')) {
            $statuses = array_map('trim', explode(',', $request->status_name));
            $query->where(function ($q) use ($statuses) {
                foreach ($statuses as $status) {
                    $q->orWhere('status', 'LIKE', '%' . $status . '%');
                    if($status ==='Reject'){
                        $q->orWhere('reject_status', 1);
                    }elseif($status ==='Approved'){
                        $q->orWhere('approval_status', 1);
                    }elseif($status ==='Pending'){
                       $q->orWhere(function($sub) {
                            $sub->where('approval_status', 0)
                                ->where('reject_status', 0);
                        });
                    }
                }
            });
        }
        if ($request->filled('nature_id')) {
            $ids = explode(',', $request->nature_id);

            $query->whereHas('BillLines', function ($q) use ($ids) {
                $q->whereIn('account_id', $ids);
            });
        }
        if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                ->orWhere('zone_name', 'like', "%{$search}%")
                ->orWhere('branch_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('purchase_gen_order', 'like', "%{$search}%")
                ->orWhere('purchase_order_number', 'like', "%{$search}%")
                ->orWhere('order_number', 'like', "%{$search}%")
                ->orWhere('bill_date', 'like', "%{$search}%")
                ->orWhere('due_date', 'like', "%{$search}%");
                // ->orWhere('company_name', 'like', "%{$search}%");
            });
        }
        if ($request->sort_column && $request->sort_direction) {

            if ($request->sort_column === 'location') {
                $query->orderBy('zone_name', $request->sort_direction);
                // OR branch_name if needed
            }

            if ($request->sort_column === 'po_number') {
                $query->orderBy('purchase_gen_order', $request->sort_direction);
            }
        }
        // Compute filtered stats before pagination (clone the query)
        $filteredAll = (clone $query)->get();
        $filteredStats = [
            'total'           => $filteredAll->count(),
            'total_amount'    => $filteredAll->sum('grand_total_amount'),
            'approved'        => $filteredAll->where('approval_status', 1)->count(),
            'approved_amount' => $filteredAll->where('approval_status', 1)->sum('grand_total_amount'),
            'pending'         => $filteredAll->where('approval_status', 0)->where('reject_status', 0)->count(),
            'pending_amount'  => $filteredAll->where('approval_status', 0)->where('reject_status', 0)->sum('grand_total_amount'),
            'rejected'        => $filteredAll->where('reject_status', 1)->count(),
            'draft'           => $filteredAll->where('status', 'draft')->count(),
        ];

        $purchaselist = $query->paginate($perPage)->appends($request->all());
         $allpurchase = TblPurchaseorder::orderBy('id','desc')->where('delete_status',0)->get();

        // Overall stats (unfiltered)
        $stats = [
            'total'          => $allpurchase->count(),
            'total_amount'   => $allpurchase->sum('grand_total_amount'),
            'approved'       => $allpurchase->where('approval_status', 1)->count(),
            'approved_amount'=> $allpurchase->where('approval_status', 1)->sum('grand_total_amount'),
            'pending'        => $allpurchase->where('approval_status', 0)->where('reject_status', 0)->count(),
            'pending_amount' => $allpurchase->where('approval_status', 0)->where('reject_status', 0)->sum('grand_total_amount'),
            'rejected'       => $allpurchase->where('reject_status', 1)->count(),
            'draft'          => $allpurchase->where('status', 'draft')->count(),
        ];

        if ($request->ajax()) {
            $html = view('vendor.partials.table.purchase_rows', compact('purchaselist','perPage','limit_access'))->render();
            return response()->json(['html' => $html, 'stats' => $filteredStats]);
        }

        return view('vendor.purchase_bashboard', [
            'admin'         => $admin,
            'limit_access'  => $limit_access,
            'locations'     => $locations,
            'purchaselist'  => $purchaselist,
            'allpurchase'   => $allpurchase,
            'stats'         => $stats,
            'perPage'       => $perPage,
            'TblZonesModel' => $TblZonesModel,
            'TblPoEmail'    => $TblPoEmail,
            'Tblcompany'    => $Tblcompany,
            'Tblvendor'     => $Tblvendor,
            'Tblaccount'    => $Tblaccount,
        ]);
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
    //      $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
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
        // $count = TblPurchaseorder::count();
        // $nextNumber = $count + 1;
        // $purchase_id = 'PO-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $lastRecord = TblPurchaseorder::orderBy('id', 'DESC')->where('company_id',1)->first();
        if ($lastRecord && isset($lastRecord->purchase_gen_order)) {
            $lastNumber = (int) str_replace('PO-', '', $lastRecord->purchase_gen_order);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $purchase_id = 'PO-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $locations = TblLocationModel::all();
        $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10); // for TDS modal table (paginated)
        $tdstax    = Tbltdstax::orderBy('id', 'desc')->get();        // for TDS dropdown (all records)
        $Tbltcstax = Tbltcstax::all();
        $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);
        $gsttax = Tblgsttax::orderBy('id', 'desc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'desc')->get();
        $TblDeliveryAddress = TblDeliveryAddress::orderBy('id', 'desc')->get();
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->get();
        $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
       $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])->where('approval_status', 1)->where('po_status', 0)->where('delete_status',0)->orderBy('id','desc')->paginate(10);

        // If request is AJAX → return only table partial
        if ($request->ajax()) {
            return view('vendor.partials.quotation_table', compact('TblQuotation', 'perPage'))->render();
        }

        $commonData = [
            'admin' => $admin, 'locations' => $locations, 'vendor' => $vendor,
            'customer' => $customer, 'Tbltdstax' => $Tbltdstax, 'tdstax' => $tdstax,
            'Tbltcstax' => $Tbltcstax, 'Tblgsttax' => $Tblgsttax,
            'Tblaccount' => $Tblaccount, 'purchase_id' => $purchase_id,
            'TblZonesModel' => $TblZonesModel, 'TblQuotation' => $TblQuotation,
            'perPage' => $perPage, 'Tbltdssection' => $Tbltdssection,
            'Tblcompany' => $Tblcompany, 'gsttax' => $gsttax, 'type' => $type,
            'TblDeliveryAddress' => $TblDeliveryAddress,
        ];

        if($id !==""){
            if($type =='edit'){
                $purchase_id="";
                $purchase = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('delete_status',0)->where('id',$id)->get();
                return view('vendor.purchase_create', array_merge($commonData, ['purchase' => $purchase, 'purchase_id' => $purchase_id]));
            }else{
                $purchase = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('delete_status',0)->where('id',$id)->get();
                return view('vendor.purchase_create', array_merge($commonData, ['purchase' => $purchase]));
            }
        }else{
            return view('vendor.purchase_create', $commonData);
        }
    }

    function cleanCurrency($value) {
        $clean = str_replace(['₹', ',','-'], '', $value);
        return round(floatval($clean), 2);
    }
     public function savepurchaseorder(Request $request)
    {
        $isUpdate = $request->filled('id'); // Check if ID exists
        $now = now();
        $user_id = auth()->user()->id;
        $admin = auth()->user();
        // $poNumber = $this->generateDocumentNumber(
        //     $request->company_id,
        //     $request->company_name,
        //     'po'
        // );
        // dd($poNumber);
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
            // 'purchase_gen_order' => $poNumber,
            'delivery_id' => $request->delivery_id,
            'delivery_address' => $request->delivery_text,
            'order_number' => $request->order_number,
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            'payment_terms' => $request->payment_terms,
            'subject' => $request->subject,
            'discount_percent' => $request->discount_percent,
            'discount_type' => $request->discount_type,
            'discount_amount' => $this->cleanCurrency($request->discount_amount),
            'discount_tax' => $request->discount_toggle,
            'adjustment_value' => $request->adjustment_value,
            'adjustment_reason' => $request->adjustment_reason,
            'tds_tax_id' => $request->tds_tax_id,
            'tcs_tax_id' => $request->tcs_tax_id,
            'tax_type' => $request->tax_type,
            'tax_name' => $request->tcs_tax_name ?: $request->tds_tax_name,
            'tax_rate' => $request->tcs_tax_selected ?: $request->tds_tax_selected,
            'tax_amount' => $this->cleanCurrency($request->tax_amount),
            'export_name' => $request->export_name,
            'export_amount' => $request->export_amount,
            'loading_unloading_name' => $request->loading_unloading_name,
            'loading_unloading_amount' => $request->loading_unloading_amount,
            'timeline_date' => $request->timeline_date,
            'esi_type' => $request->esi_type,
            'esi_value' => $request->esi_value,
            'pf_type' => $request->pf_type,
            'pf_value' => $request->pf_value,
            'other_type' => $request->other_type,
            'other_value' => $request->other_value,
            'other_reason' => $request->other_reason,
            'Tax_in_ex' => $request->taxModeBadge,
            'esi_amount' => $this->cleanCurrency($request->esi_amount),
            'pf_amount' => $this->cleanCurrency($request->pf_amount),
            'other_amount' => $this->cleanCurrency($request->other_amount),
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
        if ($request->filled('quotation_id')) {
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
             $data['purchase_gen_order'] = $request->purchase_gen_order;
            $purchaseorder = TblPurchaseorder::findOrFail($request->id);
            $grandTotal = (float) $this->cleanCurrency($request->grand_total_amount);
            $partial    = (float) ($purchaseorder->partially_payment ?? 0);
            $data['balance_amount'] = max(0, $grandTotal - $partial);

            // ── Append to edit_history JSON column ──
            $existingHistory = json_decode($purchaseorder->edit_history ?? '[]', true) ?: [];
            $roles = [
                1 => 'Superadmin',
                2 => 'Zonal Admin',
                3 => 'Admin',
                4 => 'Auditor',
                5 => 'User',
            ];
            $existingHistory[] = [
                'edited_by' => $admin->user_fullname ?? $admin->email,
                'role'      => $roles[$admin->access_limits] ?? 'User',
                'edited_at' => now()->format('d/m/Y h:i A'),
                'status'    => $request->save_status ?? $purchaseorder->status,
                'amount'    => $request->grand_total_amount ?? $purchaseorder->grand_total_amount,
            ];
            $data['edit_history'] = json_encode($existingHistory);

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
            $incomingLineIds = collect($request->linesdata ?? [])
                ->map(function ($row) {
                    if (! is_array($row)) {
                        return null;
                    }
                    $p = $this->prepareVendorLinePayload($row);

                    return $p['line_id'] ?? null;
                })
                ->filter()
                ->values()
                ->toArray();
            // Delete removed rows
            TblPurchaseorderLines::where('purchase_order_id', $purchaseorder_id)
                ->whereNotIn('id', $incomingLineIds)
                ->delete();

            // Update or insert contact persons
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    if (! is_array($linesData)) {
                        continue;
                    }
                    $prepared = $this->prepareVendorLinePayload($linesData);
                    if ($prepared === null) {
                        continue;
                    }
                    $linesDatas = array_merge($prepared['columns'], [
                        'purchase_order_id' => $purchaseorder_id,
                        'updated_at' => $now,
                    ]);

                    if (! empty($prepared['line_id'])) {
                        TblPurchaseorderLines::where('id', $prepared['line_id'])
                            ->where('purchase_order_id', $purchaseorder_id)
                            ->update($linesDatas);
                    } else {
                        $linesDatas['created_at'] = $now;
                        TblPurchaseorderLines::create($linesDatas);
                    }
                }
            }


        } else {
            $poNumber = $this->generateDocumentNumber(
                $request->company_id,
                $request->company_name,
                'po'
            );

            $data['purchase_gen_order'] = $poNumber;
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
                    if (! is_array($linesData)) {
                        continue;
                    }
                    $prepared = $this->prepareVendorLinePayload($linesData);
                    if ($prepared === null) {
                        continue;
                    }
                    $linesDatas = array_merge($prepared['columns'], [
                        'purchase_order_id' => $purchaseorder_id,
                        'created_at' => $now,
                    ]);
                    TblPurchaseorderLines::create($linesDatas);
                }
            }

        }
         return response()->json([
            'success' => true,
            'restore_filters' => $isUpdate ? true : false,
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

            // Only active PO email configs (menu_type stores JSON array of menu names)
            $poEmailConfigs = TblPoEmail::where('status', 1)
                ->where(function ($q) {
                    $q->where('menu_type', 'like', '%Accounts Book%')
                      ->orWhere('menu_type', 'Accounts Book');
                })
                ->get();

            $toEmails = $poEmailConfigs->map(fn($r) => $r->to_email ?: $r->email)
                ->filter()->unique()->values()->toArray();

            if (!$toEmails) {
                return response()->json(['success' => false, 'message' => 'No active recipient emails found for Purchase Order']);
            }

            // Merge all CC emails from all active PO configs
            $ccEmails = $poEmailConfigs->flatMap(function ($r) {
                $cc = is_string($r->cc_emails) ? json_decode($r->cc_emails, true) : ($r->cc_emails ?? []);
                return is_array($cc) ? $cc : [];
            })->filter()->unique()->values()->toArray();

            if ($status === "Approve") {
                TblPurchaseorder::where('id', $approver_id)->update([
                    'approval_status' => 1
                ]);
                Mail::send('vendor.emails.purchase_order', [
                    'purchase' => $purchase,
                    'status'   => 'Approved'
                ], function ($message) use ($toEmails, $ccEmails, $purchase) {
                    $message->to($toEmails)
                            ->subject("Purchase Order #{$purchase->purchase_gen_order} Approved");
                    if ($ccEmails) {
                        $message->cc($ccEmails);
                    }
                });

                return response()->json(['success' => true, 'message' => 'Approval data updated & email sent successfully!']);
            } else {
                TblPurchaseorder::where('id', $approver_id)->update([
                    'reject_status' => 1
                ]);
                Mail::send('vendor.emails.purchase_order', [
                    'purchase' => $purchase,
                    'status'   => 'Rejected'
                ], function ($message) use ($toEmails, $ccEmails, $approver_id) {
                    $message->to($toEmails)
                            ->subject("Purchase Order #{$approver_id} Rejected");
                    if ($ccEmails) {
                        $message->cc($ccEmails);
                    }
                });

                return response()->json(['success' => true, 'message' => 'Rejected data updated & email sent successfully!']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Invalid approver id']);
    }
    public function getpurchasefetch(Request $request)
    {
        $ids = json_decode($request->input('selected_ids'), true);
        $purchase = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('delete_status',0)->whereIn('id',$ids)->get();
         return response()->json([
                    'message' => 'Purchase orders fetched successfully.',
                    'purchase' => $purchase
                ]);

    }

//     public function getpurchaseprint(Request $request)
// {
//     $purchaseId = $request->id;

//     $purchase = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling','TblCompany'])->findOrFail($purchaseId);
//     // dd($purchase);
//     $pdf = new TCPDF();
//     $pdf->setPrintHeader(false); // disable default header line
//     $pdf->setPrintFooter(false); // optional
//     $pdf->SetMargins(10, 10, 10);
//     $pdf->AddPage();

//     // Execute raw TCPDF drawing code
//     include(resource_path('views/vendor/purchaseprint.blade.php'));

//     return response($pdf->Output('Purchase_'.$purchase->id.'.pdf', 'S'))
//         ->header('Content-Type', 'application/pdf');
// }
// public function getpurchasepdf(Request $request)
// {
//     $purchaseId = $request->id;
//     $purchase = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling'])->findOrFail($purchaseId);

//     $pdf = new TCPDF();
//     $pdf->setPrintHeader(false); // disable default header line
//     $pdf->setPrintFooter(false); // optional
//     $pdf->SetMargins(10, 10, 10);
//     $pdf->AddPage();

//     // Execute raw TCPDF drawing code
//     include(resource_path('views/vendor/purchaseprint.blade.php'));

//     // Force download the PDF
//     return response($pdf->Output('Purchase_'.$purchase->purchase_order_number.'.pdf', 'D'))
//         ->header('Content-Type', 'application/pdf');
// }

public function getpurchaseprint(Request $request)
{

    $purchaseId = $request->id;

    $purchase = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling','TblCompany'])->findOrFail($purchaseId);

    $pdf = PDF::loadView('vendor.pdf.purchaseprint', compact('purchase'));

    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'dejavusans',
        'isPhpEnabled' => true,
        'isFontSubsettingEnabled' => true,
    ]);
    return $pdf->stream('Purchase_' . $purchase->purchase_gen_order . '.pdf');

}
public function getpurchasepdf(Request $request)
{
    $purchaseId = $request->id;

    $purchase = TblPurchaseorder::with(['BillLines','Tblvendor','TblBilling','TblCompany'])->findOrFail($purchaseId);
    $pdf = PDF::loadView('vendor.pdf.quotationprint', compact('quotation'));

    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'dejavusans',
        'isPhpEnabled' => true,
        'isFontSubsettingEnabled' => true,
    ]);

    // Download the PDF with a filename
    return $pdf->download('Purchase_' . $purchase->purchase_gen_order . '.pdf');
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
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'asc')->get();
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
            ])->where('delete_status',0)
            ->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            // $query->whereBetween('created_at', [$from, $to]);
            $query->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
        }
        if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Karanataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }
        if ($request->filled('zone_id')) {
            $ids = explode(',', $request->zone_id);
            $query->whereIn('zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $ids = explode(',', $request->branch_id);
            $query->whereIn('branch_id', $ids);
        }
        if ($request->filled('company_id')) {
            $ids = explode(',', $request->company_id);
            $query->whereIn('company_id', $ids);
        }
        if ($request->filled('vendor_id')) {
            $ids = explode(',', $request->vendor_id);
            $query->whereIn('vendor_id', $ids);
        }
        if ($request->filled('status_name')) {
            $statuses = array_map('trim', explode(',', $request->status_name));

            $query->where(function ($q) use ($statuses) {
                foreach ($statuses as $status) {
                    if ($status === 'Checking Status') {
                        $q->orWhere('checker_status', 1);
                    } elseif ($status === 'Approver Status') {
                        $q->orWhere('approval_status', 1);
                    }
                }
            });
        }

        if ($request->filled('nature_name')) {
            $names = explode(',', $request->nature_name);
            $query->whereIn('nature_payment', $names);
        }
        if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function($q) use ($search) {
                $q->where('vendor_id', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")
                ->orWhere('branch_name', 'like', "%{$search}%")
                ->orWhere('zone_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('vendor', 'like', "%{$search}%")
                ->orWhere('nature_payment', 'like', "%{$search}%")
                ->orWhere('payment_status', 'like', "%{$search}%")
                ->orWhere('payment_method', 'like', "%{$search}%")
                ->orWhere('utr_number', 'like', "%{$search}%")
                ->orWhere('ifsc_code', 'like', "%{$search}%")
                ->orWhere('account_number', 'like', "%{$search}%")
                ->orWhere('pan_number', 'like', "%{$search}%");
                // ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Compute filtered stats before pagination
        $filteredAll = (clone $query)->get();
        $filteredStats = [
            'total'            => $filteredAll->count(),
            'total_amount'     => $filteredAll->sum(fn($n) => $n->BillLines->sum('only_payable')),
            'checked'          => $filteredAll->where('checker_status', 1)->count(),
            'approved'         => $filteredAll->where('approval_status', 1)->count(),
            'pending'          => $filteredAll->where('checker_status', 0)->count(),
            'success'          => $filteredAll->where('payment_status', 'Success')->count(),
            'success_amount'   => $filteredAll->where('payment_status', 'Success')->sum(fn($n) => $n->BillLines->sum('only_payable')),
        ];

        // Run query AFTER filters
        $purchaselist = $query->paginate($perPage)->appends($request->all());

        // Overall stats (unfiltered)
        $allNefts = Tblneft::where('delete_status', 0)->get();
        $stats = [
            'total'          => $allNefts->count(),
            'total_amount'   => $allNefts->sum(fn($n) => $n->BillLines->sum('only_payable')),
            'checked'        => $allNefts->where('checker_status', 1)->count(),
            'approved'       => $allNefts->where('approval_status', 1)->count(),
            'pending'        => $allNefts->where('checker_status', 0)->count(),
            'success'        => $allNefts->where('payment_status', 'Success')->count(),
            'success_amount' => $allNefts->where('payment_status', 'Success')->sum(fn($n) => $n->BillLines->sum('only_payable')),
        ];

        // If AJAX request, return JSON with html + stats
        if ($request->ajax()) {
            $html = view('vendor.partials.table.neft_rows', compact('purchaselist','perPage','limit_access'))->render();
            return response()->json(['html' => $html, 'stats' => $filteredStats]);
        }

        // Normal page load
        return view('vendor.neft_bashboard', [
            'admin'         => $admin,
            'TblZonesModel' => $TblZonesModel,
            'locations'     => $locations,
            'Tblcompany'    => $Tblcompany,
            'Tblvendor'     => $Tblvendor,
            'purchaselist'  => $purchaselist,
            'perPage'       => $perPage,
            'limit_access'  => $limit_access,
            'stats'         => $stats,
            'Tblaccount'    => $Tblaccount,
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
            $purchase = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('delete_status',0)->where('id',$id)->get();
            return view('vendor.neft_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax,'purchase' => $purchase]);
        }else{
            return view('vendor.neft_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax]);
        }
        // return view('vendor.bill_create', ['admin' => $admin,'locations' => $locations,'vendor' => $vendor,'customer' => $customer,'Tbltdstax' => $Tbltdstax,'Tbltcstax' => $Tbltcstax]);

    }
//     public function saveneft(Request $request)
// {
//     // dd($request);
//     $isUpdate = $request->filled('id'); // Check if ID exists
//     $now = now();
//     $user_id = auth()->user()->id;
//     $admin = auth()->user();
//     function handleUploads($files, $folder) {
//         $storedFiles = [];

//         if (!$files) return null;

//         $uploadPath = public_path("uploads/{$folder}");
//         if (!File::exists($uploadPath)) {
//             File::makeDirectory($uploadPath, 0777, true);
//         }

//         $files = is_array($files) ? $files : [$files];

//         foreach ($files as $file) {
//             if (!$file || !is_object($file)) continue;

//             $originalName = $file->getClientOriginalName();
//             $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
//             $file->move($uploadPath, $fileName);
//             $storedFiles[] = $fileName;
//         }

//         return !empty($storedFiles) ? json_encode($storedFiles) : null;
//     }

//     // File uploads - handle new uploads
//     // $panPaths = $request->hasFile('pan_upload') ? handleUploads($request->file('pan_upload'), 'neft') : $request->existing_pan_file;
//     // $invoicePaths = $request->hasFile('invoice_upload') ? handleUploads($request->file('invoice_upload'), 'neft') : $request->existing_invoice_upload;
//     // $bankPaths = $request->hasFile('bank_upload') ? handleUploads($request->file('bank_upload'), 'neft') : $request->existing_bank_upload;
//     // $poPaths = $request->hasFile('po_upload') ? handleUploads($request->file('po_upload'), 'neft') : $request->existing_po_upload;
//     // $poSignedPaths = $request->hasFile('po_signed_upload') ? handleUploads($request->file('po_signed_upload'), 'neft') : $request->existing_po_signed_upload;
//     // $poDeliveryPaths = $request->hasFile('po_delivery_upload') ? handleUploads($request->file('po_delivery_upload'), 'neft') : $request->existing_po_delivery_upload;

//     $panPaths = json_encode(array_merge(
//         $request->hasFile('pan_upload') ? json_decode(handleUploads($request->file('pan_upload'), 'neft'), true) : [],
//         is_array($request->existing_pan_file) ? $request->existing_pan_file : json_decode($request->existing_pan_file, true) ?? []
//     ));

//     $invoicePaths = json_encode(array_merge(
//         $request->hasFile('invoice_upload') ? json_decode(handleUploads($request->file('invoice_upload'), 'neft'), true) : [],
//         is_array($request->existing_invoice_upload) ? $request->existing_invoice_upload : json_decode($request->existing_invoice_upload, true) ?? []
//     ));

//     // Repeat for other file types...
//     $bankPaths = json_encode(array_merge(
//         $request->hasFile('bank_upload') ? json_decode(handleUploads($request->file('bank_upload'), 'neft'), true) : [],
//         is_array($request->existing_bank_upload) ? $request->existing_bank_upload : json_decode($request->existing_bank_upload, true) ?? []
//     ));

//     $poPaths = json_encode(array_merge(
//         $request->hasFile('po_upload') ? json_decode(handleUploads($request->file('po_upload'), 'neft'), true) : [],
//         is_array($request->existing_po_upload) ? $request->existing_po_upload : json_decode($request->existing_po_upload, true) ?? []
//     ));

//     $poSignedPaths = json_encode(array_merge(
//         $request->hasFile('po_signed_upload') ? json_decode(handleUploads($request->file('po_signed_upload'), 'neft'), true) : [],
//         is_array($request->existing_po_signed_upload) ? $request->existing_po_signed_upload : json_decode($request->existing_po_signed_upload, true) ?? []
//     ));

//     $poDeliveryPaths = json_encode(array_merge(
//         $request->hasFile('po_delivery_upload') ? json_decode(handleUploads($request->file('po_delivery_upload'), 'neft'), true) : [],
//         is_array($request->existing_po_delivery_upload) ? $request->existing_po_delivery_upload : json_decode($request->existing_po_delivery_upload, true) ?? []
//     ));

//     // Payment methods as comma-separated string
//     $paymentMethods = $request->has('payment_method') ? implode(',', $request->payment_method) : null;
//     $amount=0;
//     $data = [
//         'serial_number' => $request->serial_number,
//         'branch_id' => $request->branch_id,
//         'branch_name' => $request->branch_name,
//         'zone_id' => $request->zone_id,
//         'zone_name' => $request->zone_name,
//         'company_id' => $request->company_id,
//         'company_name' => $request->company_name,
//         'user_id' => $request->users_id,
//         'bill_pay_id' => $request->bill_pay_id,
//         'created_by' => $request->created_by,
//         'vendor' => $request->vendor_name,
//         'vendor_id' => $request->vendor_id,
//         'nature_payment' => $request->nature_payment,
//         'payment_status' => $request->payment_status,
//         'payment_method' => $paymentMethods,
//         'utr_number' => $request->utr_number,
//         'pan_number' => $request->pan_number,
//         'account_number' => $request->account_number,
//         'ifsc_code' => $request->ifsc_code,
//         'checker_status' => $request->checker_status ?? 0,
//         'approval_status' => $request->approval_status ?? 0,
//         'pan_upload' => $panPaths,
//         'invoice_upload' => $invoicePaths,
//         'bank_upload' => $bankPaths,
//         'po_upload' => $poPaths,
//         'po_signed_upload' => $poSignedPaths,
//         'po_delivery_upload' => $poDeliveryPaths,
//         'created_at' => now(),
//     ];
//     // dd($data);
//      if ($isUpdate) {
//             $neft = Tblneft::findOrFail($request->id);
//             $neft->update($data);
//             if(!empty($neft)){
//                 $vendor = Tblvendor::where('id', $neft->vendor_id)->first();
//                 $history = [
//                     'vendor_id'   => $vendor->id,
//                     'name'        => 'NEFT Updated',
//                     'description' => "NEFT Generated ₹{$neft->serial_number} updated by {$admin->email}",
//                     'date' => now()->toDateString(),
//                     'time' => now()->format('h:i A')
//                 ];
//                 TblVendorHistory::create($history);
//             }
//             $neft_id = $neft->id;

//             // Update or insert contact persons
//             if ($request->has('account')) {
//                 foreach ($request->account as $linesData) {
//                     $linesDatas = [
//                         'neft_id' => $neft_id,
//                         'bill_id' => $linesData['bill_id'] ?? null,
//                         'bill_pay_id' => $linesData['bill_pay_id'] ?? null,
//                         'bill_pay_lines_id' => $linesData['bill_pay_lines_id'] ?? null,
//                         'invoice_amount' => $linesData['invoice_amount'] ?? null,
//                         'already_paid' => $linesData['already_paid'] ?? null,
//                         'tds_tax_name' => $linesData['tds_tax_name'] ?? null,
//                         'tds_tax_id' => $linesData['tds_tax_id'] ?? null,
//                         'tax_amount' => $linesData['tax_amount'] ?? null,
//                         'gst_name' => $linesData['gst_name'] ?? null,
//                         'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
//                         'gst_amount' => $linesData['gst_amount'] ?? null,
//                         'only_payable' => $linesData['only_payable'] ?? null,
//                         'updated_at' => $now,
//                     ];

//                     if (!empty($linesData['id'])) {
//                         Tblneftlines::where('id', $linesData['id'])
//                             ->where('neft_id', $neft_id)
//                             ->update($linesDatas);
//                     } else {
//                         $contactValues['created_at'] = $now;
//                         Tblneftlines::create($linesDatas);
//                     }
//                 }
//             }


//         } else {
//             // Create new customer
//             $neft = Tblneft::create($data);
//             if(!empty($neft)){
//                 $vendor = Tblvendor::where('id', $neft->vendor_id)->first();
//                 $history = [
//                     'vendor_id'   => $vendor->id,
//                     'name'        => 'NEFT added',
//                     'description' => "NEFT Generated ₹{$neft->serial_number} created by {$admin->email}",
//                     'date' => now()->toDateString(),
//                     'time' => now()->format('h:i A')
//                 ];
//                 TblVendorHistory::create($history);
//             }
//             $neft_id = $neft->id;
//             if ($request->has('account')) {
//                 foreach ($request->account as $linesData) {
//                     Tblneftlines::create([
//                         'neft_id' => $neft_id,
//                         'bill_id' => $linesData['bill_id'] ?? null,
//                         'bill_pay_id' => $linesData['bill_pay_id'] ?? null,
//                         'bill_pay_lines_id' => $linesData['bill_pay_lines_id'] ?? null,
//                         'invoice_amount' => $linesData['invoice_amount'] ?? null,
//                         'already_paid' => $linesData['already_paid'] ?? null,
//                         'tds_tax_name' => $linesData['tds_tax_name'] ?? null,
//                         'tds_tax_id' => $linesData['tds_tax_id'] ?? null,
//                         'tax_amount' => $linesData['tax_amount'] ?? null,
//                         'gst_name' => $linesData['gst_name'] ?? null,
//                         'gst_tax_id' => $linesData['gst_tax_id'] ?? null,
//                         'gst_amount' => $linesData['gst_amount'] ?? null,
//                         'only_payable' => $linesData['only_payable'] ?? null,
//                         'created_at' => $now,
//                     ]);
//                 // $amount.=$linesData['already_paid'];
//                 }

//             }
//             // Tblneft::where('id',$neft_id)->update(['amount_paid',$amount]);

//         }

//         return response()->json([
//                     'success' => true,
//                     'message' => $isUpdate ? 'NEFT Data updated successfully!' : 'NEFT Data saved successfully!'
//                 ]);
//     }
public function saveneft(Request $request)
{
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
    $panPaths = json_encode(array_merge(
        $request->hasFile('pan_upload') ? json_decode(handleUploads($request->file('pan_upload'), 'neft'), true) : [],
        is_array($request->existing_pan_file) ? $request->existing_pan_file : json_decode($request->existing_pan_file, true) ?? []
    ));

    $invoicePaths = json_encode(array_merge(
        $request->hasFile('invoice_upload') ? json_decode(handleUploads($request->file('invoice_upload'), 'neft'), true) : [],
        is_array($request->existing_invoice_upload) ? $request->existing_invoice_upload : json_decode($request->existing_invoice_upload, true) ?? []
    ));

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

    $data = [
        'serial_number' => $request->serial_number,
        'branch_id' => $request->branch_id,
        'branch_name' => $request->branch_name,
        'zone_id' => $request->zone_id,
        'zone_name' => $request->zone_name,
        'company_id' => $request->company_id,
        'company_name' => $request->company_name,
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

    if ($isUpdate) {
        $neft = Tblneft::findOrFail($request->id);

        // Log edit history to tbl_neft if column exists
        if (\Illuminate\Support\Facades\Schema::hasColumn('tbl_neft', 'edit_history')) {
            $existingHistory = json_decode($neft->edit_history ?? '[]', true) ?: [];
            $existingHistory[] = [
                'edited_by' => $admin->user_fullname ?? $admin->email,
                'role'      => $admin->access_limits == 1 ? 'Admin' : 'User',
                'edited_at' => now()->format('d/m/Y h:i A'),
                'status'    => $request->payment_status ?? '-',
                'amount'    => $request->account[0]['only_payable'] ?? 0,
            ];
            $data['edit_history'] = json_encode($existingHistory);
        }

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
                    $linesDatas['created_at'] = $now;
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
            }
        }
    }

     $updatedNeft = Tblneft::with([
                        'Tblvendor',
                        'Tblbankdetails',
                        'Tblbillpay',
                        'BillLines.Tblbilllines',
                        'BillLines.alreadypaid',
                        'BillLines.Bill:id,bill_gen_number,purchase_id,quotation_id',
                        'BillLines.Bill.Purchase:id,purchase_gen_order,quotation_id',
                        'BillLines.Bill.Purchase.quotation:id,quotation_gen_no'
                    ])->findOrFail($neft->id);

    return response()->json([
        'success' => true,
        'restore_filters' => $isUpdate ? true : false,
        'message' => $isUpdate
            ? 'NEFT Data updated successfully!'
            : 'NEFT Data saved successfully!',
        'data' => $updatedNeft
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
//         $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
//         $quotationlist = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])->orderBy('id', 'desc')->paginate($perPage)->appends(['per_page' => $perPage]);
//         return view('vendor.quotation_bashboard', ['admin' => $admin,'locations' => $locations,'quotationlist' => $quotationlist,'perPage' => $perPage,'TblZonesModel' => $TblZonesModel]);
//     }
public function getquotation(Request $request)
{
    $admin = auth()->user();
    $locations = TblLocationModel::all();
    $perPage = $request->get('per_page', 10);
    $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
    $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
    $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
    $Tblaccount = Tblaccount::orderBy('id', 'asc')->get();
    $query = TblQuotation::with(['BillLines', 'Tblvendor', 'TblBilling'])->where('delete_status',0)->orderBy('id', 'desc');

    // // Apply filters
    // if ($request->filled('date_from') && $request->filled('date_to')) {
    //     $query->whereBetween('bill_date', [$request->date_from, $request->date_to]);
    // }
    if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
            // $query->whereBetween('created_at', [$from, $to]);
    }
    if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Karnataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }
    if ($request->filled('zone_id')) {
        $ids = explode(',', $request->zone_id);
        $query->whereIn('zone_id', $ids);
    }
    if ($request->filled('branch_id')) {
        $ids = explode(',', $request->branch_id);
        $query->whereIn('branch_id', $ids);
    }
    if ($request->filled('company_id')) {
        $ids = explode(',', $request->company_id);
        $query->whereIn('company_id', $ids);
    }
    if ($request->filled('vendor_id')) {
        $ids = explode(',', $request->vendor_id);
        $query->whereIn('vendor_id', $ids);
    }
    if ($request->filled('status_name')) {
            $statuses = array_map('trim', explode(',', $request->status_name));
            $query->where(function ($q) use ($statuses) {
                foreach ($statuses as $status) {
                    $q->orWhere('status', 'LIKE', '%' . $status . '%');
                    if($status ==='Reject'){
                        $q->orWhere('reject_status', 1);
                    }elseif($status ==='Approved'){
                        $q->orWhere('approval_status', 1);
                    }elseif($status ==='Pending'){
                       $q->orWhere(function($sub) {
                            $sub->where('approval_status', 0)
                                ->where('reject_status', 0);
                        });
                    }
                }
            });
        }
        if ($request->filled('nature_id')) {
            $ids = explode(',', $request->nature_id);

            $query->whereHas('BillLines', function ($q) use ($ids) {
                $q->whereIn('account_id', $ids);
            });
        }
    if ($request->filled('universal_search')) {
        $search = $request->universal_search;
        $query->where(function($q) use ($search) {
            $q->where('vendor_name', 'like', "%{$search}%")
            ->orWhere('zone_name', 'like', "%{$search}%")
            ->orWhere('branch_name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->orWhere('quotation_gen_no', 'like', "%{$search}%")
            ->orWhere('quotation_no', 'like', "%{$search}%")
            ->orWhere('order_number', 'like', "%{$search}%")
            ->orWhere('bill_date', 'like', "%{$search}%")
            ->orWhere('due_date', 'like', "%{$search}%");
            // ->orWhere('company_name', 'like', "%{$search}%");
        });
    }

    // Compute filtered stats before pagination (clone the query)
    $filteredAll = (clone $query)->get();
    $filteredStats = [
        'total'           => $filteredAll->count(),
        'total_amount'    => $filteredAll->sum('grand_total_amount'),
        'approved'        => $filteredAll->where('approval_status', 1)->count(),
        'approved_amount' => $filteredAll->where('approval_status', 1)->sum('grand_total_amount'),
        'pending'         => $filteredAll->where('approval_status', 0)->where('reject_status', 0)->count(),
        'pending_amount'  => $filteredAll->where('approval_status', 0)->where('reject_status', 0)->sum('grand_total_amount'),
        'rejected'        => $filteredAll->where('reject_status', 1)->count(),
        'draft'           => $filteredAll->where('status', 'draft')->count(),
    ];

    $quotationlist = $query->paginate($perPage)->appends($request->all());
    $limit_access  = $admin->access_limits;
    if ($request->ajax()) {
        $html = view('vendor.partials.table.quotation_rows', compact('quotationlist','perPage','limit_access'))->render();
        return response()->json(['html' => $html, 'stats' => $filteredStats]);
    }
    $allquotation = TblQuotation::orderBy('id','desc')->where('delete_status',0)->get();

    $stats = [
        'total'          => $allquotation->count(),
        'total_amount'   => $allquotation->sum('grand_total_amount'),
        'approved'       => $allquotation->where('approval_status', 1)->count(),
        'pending'        => $allquotation->where('approval_status', 0)->where('reject_status', 0)->count(),
        'rejected'       => $allquotation->where('reject_status', 1)->count(),
        'draft'          => $allquotation->where('status', 'draft')->count(),
        'approved_amount'=> $allquotation->where('approval_status', 1)->sum('grand_total_amount'),
        'pending_amount' => $allquotation->where('approval_status', 0)->where('reject_status', 0)->sum('grand_total_amount'),
    ];

    return view('vendor.quotation_bashboard', [
        'admin'         => $admin,
        'limit_access'  => $limit_access,
        'TblZonesModel' => $TblZonesModel,
        'locations'     => $locations,
        'Tblcompany'    => $Tblcompany,
        'Tblvendor'     => $Tblvendor,
        'quotationlist' => $quotationlist,
        'allquotation'  => $allquotation,
        'stats'         => $stats,
        'perPage'       => $perPage,
        'Tblaccount'    => $Tblaccount
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
        // $count = TblQuotation::count(); // Get total number of rows
        // $nextNumber = $count + 1;  // Next serial number
        // $quotation_id = 'QO-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $lastRecord = TblQuotation::orderBy('id', 'DESC')->where('company_id',1)->first();
        if ($lastRecord && isset($lastRecord->quotation_gen_no)) {
            $lastNumber = (int) str_replace('QO-', '', $lastRecord->quotation_gen_no);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $quotation_id = 'QO-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $locations = TblLocationModel::all();
        // $Tbltdstax = Tbltdstax::all();
        // $Tbltcstax = Tbltcstax::all();
        // $Tblgsttax = Tblgsttax::all();
        // $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts','bankdetails'])->get();
        // $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
        $Tbltdstax = Tbltdstax::orderBy('id', 'desc')->paginate(10); // for TDS modal table
        $tdstax    = Tbltdstax::orderBy('id', 'desc')->get();        // for TDS dropdown (all records)
        $Tbltcstax = Tbltcstax::all();
        $Tblgsttax = Tblgsttax::orderBy('id', 'desc')->paginate(10);
        $gsttax = Tblgsttax::orderBy('id', 'desc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'desc')->get();
        $TblDeliveryAddress = TblDeliveryAddress::orderBy('id', 'desc')->get();
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails','tdstax'])->get();
        // dd($vendor);
        $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
        $commonData = [
            'admin' => $admin, 'locations' => $locations, 'vendor' => $vendor,
            'customer' => $customer, 'Tbltdstax' => $Tbltdstax, 'tdstax' => $tdstax,
            'Tbltcstax' => $Tbltcstax, 'Tblgsttax' => $Tblgsttax,
            'Tblaccount' => $Tblaccount, 'TblZonesModel' => $TblZonesModel,
            'quotation_id' => $quotation_id, 'Tbltdssection' => $Tbltdssection,
            'Tblcompany' => $Tblcompany, 'gsttax' => $gsttax, 'type' => $type,
            'TblDeliveryAddress' => $TblDeliveryAddress,
        ];
        if($id !==""){
            if($type =='edit'){
                $quotation_id="";
                $quotation = TblQuotation::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('delete_status',0)->where('id',$id)->get();
                return view('vendor.quotation_create', array_merge($commonData, ['quotation' => $quotation, 'quotation_id' => $quotation_id]));
            }else{
                $quotation = TblQuotation::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('delete_status',0)->where('id',$id)->get();
                return view('vendor.quotation_create', array_merge($commonData, ['quotation' => $quotation]));
            }
        }else{
            return view('vendor.quotation_create', $commonData);
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

        // dd($qoNumber);
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
            // 'quotation_gen_no' => $qoNumber,
            'delivery_address' => $request->delivery_text,
            'delivery_id' => $request->delivery_id,
            'order_number' => $request->order_number,
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            'payment_terms' => $request->payment_terms,
            'subject' => $request->subject,
            'discount_percent' => $request->discount_percent,
            'discount_type' => $request->discount_type,
            'discount_tax' => $request->discount_toggle,
            'discount_amount' => $this->cleanCurrency($request->discount_amount),
            'adjustment_value' => $request->adjustment_value,
            'adjustment_reason' => $request->adjustment_reason,
            'tds_tax_id' => $request->tds_tax_id,
            'tcs_tax_id' => $request->tcs_tax_id,
            'tax_type' => $request->tax_type,
            'tax_name' => $request->tcs_tax_name ?: $request->tds_tax_name,
            'tax_rate' => $request->tcs_tax_selected ?: $request->tds_tax_selected,
            'tax_amount' => $this->cleanCurrency($request->tax_amount),
            'export_name' => $request->export_name,
            'export_amount' => $request->export_amount,
            'loading_unloading_name' => $request->loading_unloading_name,
            'loading_unloading_amount' => $request->loading_unloading_amount,
            'timeline_date' => $request->timeline_date,
             'esi_type' => $request->esi_type,
            'esi_value' => $request->esi_value,
            'pf_type' => $request->pf_type,
            'pf_value' => $request->pf_value,
            'other_type' => $request->other_type,
            'other_value' => $request->other_value,
            'other_reason' => $request->other_reason,
            'esi_amount' => $this->cleanCurrency($request->esi_amount),
            'pf_amount' => $this->cleanCurrency($request->pf_amount),
            'other_amount' => $this->cleanCurrency($request->other_amount),
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
            $data['quotation_gen_no'] = $request->quotation_gen_no;
            $quotation = TblQuotation::findOrFail($request->id);
            $grandTotal = (float) $this->cleanCurrency($request->grand_total_amount);
            $partial    = (float) ($quotation->partially_payment ?? 0);
            $data['balance_amount'] = max(0, $grandTotal - $partial);

            // ── Append to edit_history JSON column ──
            $existingHistory = json_decode($quotation->edit_history ?? '[]', true) ?: [];
            $roles = [
                1 => 'Superadmin',
                2 => 'Zonal Admin',
                3 => 'Admin',
                4 => 'Auditor',
                5 => 'User',
            ];

            $existingHistory[] = [
                'edited_by' => $admin->user_fullname ?? $admin->email,
                'role' => $roles[$admin->access_limits] ?? 'User',
                'edited_at' => now()->format('d/m/Y h:i A'),
                'status' => $request->save_status ?? $quotation->status,
                'amount' => $request->grand_total_amount ?? $quotation->grand_total_amount,
            ];

            $data['edit_history'] = json_encode($existingHistory);
            // dd($data);
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
            $incomingLineIds = collect($request->linesdata ?? [])
                ->map(function ($row) {
                    if (! is_array($row)) {
                        return null;
                    }
                    $p = $this->prepareVendorLinePayload($row);

                    return $p['line_id'] ?? null;
                })
                ->filter()
                ->values()
                ->toArray();
            // Delete removed rows
            TblQuotationLines::where('quotation_id', $quotation_id)
                ->whereNotIn('id', $incomingLineIds)
                ->delete();

            // Update or insert contact persons
            if ($request->has('linesdata')) {
                foreach ($request->linesdata as $linesData) {
                    if (! is_array($linesData)) {
                        continue;
                    }
                    $prepared = $this->prepareVendorLinePayload($linesData);
                    if ($prepared === null) {
                        continue;
                    }
                    $linesDatas = array_merge($prepared['columns'], [
                        'quotation_id' => $quotation_id,
                        'updated_at' => $now,
                    ]);

                    if (! empty($prepared['line_id'])) {
                        TblQuotationLines::where('id', $prepared['line_id'])
                            ->where('quotation_id', $quotation_id)
                            ->update($linesDatas);
                    } else {
                        $linesDatas['created_at'] = $now;
                        TblQuotationLines::create($linesDatas);
                    }
                }
            }


        } else {
            $qoNumber = $this->generateDocumentNumber(
                $request->company_id,
                $request->company_name,
                'quotation'
            );
            $data['quotation_gen_no'] = $qoNumber;
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
                    if (! is_array($linesData)) {
                        continue;
                    }
                    $prepared = $this->prepareVendorLinePayload($linesData);
                    if ($prepared === null) {
                        continue;
                    }
                    $linesDatas = array_merge($prepared['columns'], [
                        'quotation_id' => $quotation_id,
                        'created_at' => $now,
                    ]);
                    TblQuotationLines::create($linesDatas);
                }
            }

        }
         return response()->json([
            'success' => true,
            'restore_filters' => $isUpdate ? true : false,
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
        $quotation = TblQuotation::with(['TblBilling', 'BillLines', 'Tblvendor'])->where('delete_status',0)->whereIn('id',$ids)->get();
         return response()->json([
                    'message' => 'quotation fetched successfully.',
                    'quotation' => $quotation
                ]);
    }
//     public function getquotationprint(Request $request)
// {
//     $quotationId = $request->id;

//     $quotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling','TblCompany'])->findOrFail($quotationId);
//     // dd($quotation);
//     $pdf = new TCPDF();
//     $pdf->setPrintHeader(false); // disable default header line
//     $pdf->setPrintFooter(false); // optional
//     $pdf->SetMargins(10, 10, 10);
//     $pdf->AddPage();

//     // Execute raw TCPDF drawing code
//     include(resource_path('views/vendor/quotationprint.blade.php'));

//     return response($pdf->Output('Quotation_'.$quotation->id.'.pdf', 'S'))
//         ->header('Content-Type', 'application/pdf');
// }

// public function getquotationpdf(Request $request)
// {
//     $quotationId = $request->id;
//     $quotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])->findOrFail($quotationId);

//     $pdf = new TCPDF();
//     $pdf->setPrintHeader(false); // disable default header line
//     $pdf->setPrintFooter(false); // optional
//     $pdf->SetMargins(10, 10, 10);
//     $pdf->AddPage();

//     // Execute raw TCPDF drawing code
//     include(resource_path('views/vendor/quotationprint.blade.php'));

//     // Force download the PDF
//     return response($pdf->Output('quotation_'.$quotation->quotation_no.'.pdf', 'D'))
//         ->header('Content-Type', 'application/pdf');
// }
public function getquotationprint(Request $request)
{
    $quotationId = $request->id;

    $quotation = TblQuotation::with([
        'BillLines',
        'Tblvendor',
        'TblBilling',
        'TblCompany'
    ])->findOrFail($quotationId);

    $pdf = PDF::loadView(
        'vendor.pdf.quotationprint',
        [
            'quotation' => $quotation,
            'isPdf' => true   // ✅ IMPORTANT
        ]
    );

    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'dejavusans',
        'isPhpEnabled' => true,
        'isFontSubsettingEnabled' => true,
    ]);

    return $pdf->stream('Quotation_' . $quotation->quotation_gen_no . '.pdf');
}
public function getquotationpdf(Request $request)
{
    $quotationId = $request->id;

    $quotation = TblQuotation::with([
        'BillLines',
        'Tblvendor',
        'TblBilling',
        'TblCompany'
    ])->findOrFail($quotationId);

    $pdf = PDF::loadView(
        'vendor.pdf.quotationprint',
        [
            'quotation' => $quotation,
            'isPdf' => true   // ✅ IMPORTANT
        ]
    );

    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'dejavusans',
        'isPhpEnabled' => true,
        'isFontSubsettingEnabled' => true,
    ]);

    return $pdf->download('Quotation_' . $quotation->quotation_gen_no . '.pdf');
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
                    ->where('approval_status',1)->where('bill_status',0)->where('delete_status',0)
                    ->orderBy('id','desc')
                    ->paginate(10);
    return view('vendor.partials.purchase_table', compact('purchaselist'))->render();
}

public function getQuotations(Request $request)
{
    $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])
                    ->where('approval_status',1)->where('po_status',0)->where('delete_status',0)
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

    $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->get();

    return view('vendor.tdstax', [
        'admin'          => $admin,
        'locations'      => $locations,
        'tdstax'         => $tdstax,
        'perPage'        => $perPage,
        'Tbltdssection'  => $Tbltdssection,
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
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();

        $query = Tblgrn::with(['BillLines','Tblvendor','TblBilling'])->orderBy('id', 'desc');

        // Apply filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            // $query->whereBetween('created_at', [$from, $to]);
            $query->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
        }
        if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Karnataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }
        if ($request->filled('zone_id')) {
            $ids = explode(',', $request->zone_id);
            $query->whereIn('zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $ids = explode(',', $request->branch_id);
            $query->whereIn('branch_id', $ids);
        }
        if ($request->filled('company_id')) {
            $ids = explode(',', $request->company_id);
            $query->whereIn('company_id', $ids);
        }
        if ($request->filled('vendor_id')) {
            $ids = explode(',', $request->vendor_id);
            $query->whereIn('vendor_id', $ids);
        }
        if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function($q) use ($search) {
                $q->where('vendor_id', 'like', "%{$search}%")
                ->orWhere('vendor_name', 'like', "%{$search}%")
                ->orWhere('zone_name', 'like', "%{$search}%")
                ->orWhere('branch_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('grn_number', 'like', "%{$search}%")
                ->orWhere('order_number', 'like', "%{$search}%")
                ->orWhere('payment_terms', 'like', "%{$search}%")
                ->orWhere('qc_ststus', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('due_date', 'like', "%{$search}%");
            });
        }
        // Stats calculated from filtered query BEFORE stat_filter is applied
        $filteredQuery = clone $query;
        $stats = [
            'total'    => (clone $filteredQuery)->count(),
            'approved' => (clone $filteredQuery)->where('approval_status', 1)->count(),
            'pending'  => (clone $filteredQuery)->where('approval_status', 0)->where('reject_status', 0)->count(),
            'rejected' => (clone $filteredQuery)->where('reject_status', 1)->count(),
        ];

        if ($request->filled('stat_filter')) {
            $sf = $request->stat_filter;
            if ($sf === 'approved') {
                $query->where('approval_status', 1);
            } elseif ($sf === 'pending') {
                $query->where('approval_status', 0)->where('reject_status', 0);
            } elseif ($sf === 'rejected') {
                $query->where('reject_status', 1);
            }
        }

        $grnlist = $query->paginate($perPage)->appends($request->all());

        if ($request->ajax()) {
            $html = view('vendor.partials.table.grn_rows', compact('grnlist','perPage'))->render();
            return response()->json(['html' => $html, 'stats' => $stats]);
        }

        return view('vendor.grn_bashboard', [
            'admin'         => $admin,
            'locations'     => $locations,
            'grnlist'       => $grnlist,
            'perPage'       => $perPage,
            'TblZonesModel' => $TblZonesModel,
            'Tblcompany'    => $Tblcompany,
            'Tblvendor'     => $Tblvendor,
            'stats'         => $stats,
        ]);
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
        ->where('delete_status',0)
        ->paginate($perPage, ['*'], 'po_page')
        ->appends([
            'per_page' => $perPage,
            'table' => $activeTable
        ]);

    // Load Bill data
    $billlist = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])
        ->orderBy('id', 'desc')
        ->where('grn_status', 0)
        ->where('delete_status',0)
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
    $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
    $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
    $Tbldepartment = Department::orderBy('id', 'asc')->get();
    $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->get();
    $customer = Tblcustomer::with(['billingAddress', 'shippingAddress', 'contacts'])->get();
    $TblQuotation = TblQuotation::with(['BillLines','Tblvendor','TblBilling'])
        ->where('approval_status', 1)->where('po_status', 0)->where('delete_status',0)
        ->orderBy('id', 'desc')->paginate(10);
    $users = usermanagementdetails::orderBy('id')->get();

    $grndata = null;
    $grnedit = null;

    if ($id !== "") {
        $purchase_id = "";

        if ($type === "po") {
            $grndata = TblPurchaseorder::with(['TblBilling', 'BillLines', 'Tblvendor'])
                           ->where('id', $id)
                           ->where('delete_status',0)
                           ->first();
        } elseif ($type === "bill") {
            $grndata = Tblbill::with(['TblBilling', 'BillLines', 'Tblvendor', 'Tblbankdetails'])->where('delete_status',0)
                        ->where('id', $id)
                        ->first();
        }
        else{
            $grnedit = Tblgrn::with(['TblBilling', 'BillLines', 'Tblvendor', 'QcCheckedBy'])->where('id',$id)->get();
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
        'Tbldepartment' => $Tbldepartment,
        'gsttax' => $gsttax,
        'users' => $users,
    ]);
}
public function savegrn(Request $request)
    {
        // dd($request);
        $isUpdate = $request->filled('id'); // Check if ID exists
        $now = now();
        $user_id = auth()->user()->id;
        $admin = auth()->user();

        $request->validate([
            'department_id'   => 'required|integer|exists:departments,id',
        ]);

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
            'department_id' => $request->department_id,
            'grn_number' => $request->grn_number,
            'order_number' => $request->order_number,
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            'payment_terms' => $request->payment_terms,
            'subject' => $request->subject,
            'save_status' => $request->save_status,
            'note' => $request->note,
            'qc_ststus' => $request->qc_ststus,
            'qc_checked_by' => $request->qc_checked_by,
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

        // Post GRN lines to Consumable Store when saved as Open, not draft.
        if ($request->input('save_status') === 'save' && $request->has('linesdata')) {
            ConsumableStore::where('grn_id', $grn_id)->delete();
            $deptId = $request->department_id;
            foreach ($request->linesdata as $linesData) {
                $itemName = trim((string) ($linesData['item_details'] ?? ''));
                if ($itemName === '') {
                    continue;
                }
                $qty = (float) ($linesData['acceptable_quantity'] ?? $linesData['receivable_quantity'] ?? $linesData['quantity'] ?? 0);
                ConsumableStore::create([
                    'grn_id' => $grn_id,
                    'grn_number' => $grn->grn_number,
                    'department_id' => $deptId,
                    'item_name' => $itemName,
                    'quantity' => $qty,
                ]);
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


        // Check for duplicate name (excluding the same record when updating)
        $duplicate = Tblaccount::where('name', $request->name)
            ->when($id, function ($query) use ($id) {
                $query->where('id', '!=', $id);
            })
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'Account name already exists. Please choose another name.',
            ], 409); // 409 = Conflict
        }

        // Prepare data
        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
        ];

        // Create or update
        if (!empty($id)) {
            Tblaccount::where('id', $id)->update($data);
            $message = 'Account Data Updated successfully!';
        } else {
            $data['created_at'] = now();
            Tblaccount::create($data);
            $message = 'Account Data saved successfully!';
        }

        // Return updated list
        $Tblaccount = Tblaccount::orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'account' => $Tblaccount,
            'message' => $message,
        ]);
    }
    //account
    // public function getdeliverysave(Request $request)
    // {
    //     // Get the address from request
    //     $address = $request->input('address');
    //     $existingAddress = TblDeliveryAddress::where('address', $address)->first();

    //     if ($existingAddress) {
    //         // Address already exists
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Address already exists',
    //             'data' => $existingAddress
    //         ]);
    //     }
    //     $userId = auth()->user();
    //     // Create new address
    //     $newAddress = TblDeliveryAddress::create([
    //         'address' => $address,
    //         'created_by' => $userId->id,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);
    //     $TblDeliveryAddress = TblDeliveryAddress::orderBy('id', 'desc')->get();
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Address saved successfully',
    //         'data' => $TblDeliveryAddress
    //     ]);
    // }

    public function getdeliverysave(Request $request)
    {
        // Get the address and ID from request
        $address = $request->input('address');
        $id = $request->input('id');

        $userId = auth()->user();

        // Check if we're updating an existing record
        if ($id) {
            // Find the existing address
            $existingAddress = TblDeliveryAddress::find($id);

            if (!$existingAddress) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found'
                ]);
            }

            // Check if another address with the same name exists (excluding current record)
            $duplicateAddress = TblDeliveryAddress::where('address', $address)
                ->where('id', '!=', $id)
                ->first();

            if ($duplicateAddress) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address already exists with this name'
                ]);
            }

            // Update the existing address
            $existingAddress->update([
                'address' => $address,
                'updated_by' => $userId->id, // You might need to add this column
                'updated_at' => now(),
            ]);

            $message = 'Address updated successfully';
            $addressData = $existingAddress;
        } else {
            // Create new address - check for duplicate
            $existingAddress = TblDeliveryAddress::where('address', $address)->first();

            if ($existingAddress) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address already exists'
                ]);
            }

            // Create new address
            $addressData = TblDeliveryAddress::create([
                'address' => $address,
                'created_by' => $userId->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $message = 'Address saved successfully';
        }

        // Get all addresses for refresh
        $allAddresses = TblDeliveryAddress::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $addressData,
            'all_addresses' => $allAddresses
        ]);
    }

    //branch
    public function getbranchfetch(Request $request)
    {
        $id = $request->id;
        $ids = explode(',', $id);
        $branch = DB::table('tbl_locations')->whereIn('zone_id',$ids)->get();
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
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
         $now = Carbon::now();
        $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails','TblTDSsection','TblTDSsection.section'])->where('delete_status',0)->orderBy('id', 'desc');

        $tdsids = [];
        if ($request->filled('section_id')) {
            $sectionIds = array_filter(array_map('trim', explode(',', $request->section_id)));
            if (!empty($sectionIds)) {
                $tdsids = Tbltdstax::whereIn('section_id', $sectionIds)->pluck('id')->toArray();
            }
        }
        if (!empty($tdsids)) {
            $query->whereIn('tds_tax_id', $tdsids);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
                $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
                $query->whereRaw("STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ?", [$from, $to]);
            } catch (\Exception $e) { }
        }
        if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Karnataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }
        if ($request->filled('zone_id')) {
            $ids = array_filter(explode(',', $request->zone_id));
            if (!empty($ids)) $query->whereIn('zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $ids = array_filter(explode(',', $request->branch_id));
            if (!empty($ids)) $query->whereIn('branch_id', $ids);
        }
        if ($request->filled('company_id')) {
            $ids = array_filter(explode(',', $request->company_id));
            if (!empty($ids)) $query->whereIn('company_id', $ids);
        }
        if ($request->filled('vendor_id')) {
            $ids = array_filter(explode(',', $request->vendor_id));
            if (!empty($ids)) $query->whereIn('vendor_id', $ids);
        }
         if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                ->orWhere('zone_name', 'like', "%{$search}%")
                ->orWhere('branch_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('bill_gen_number', 'like', "%{$search}%")
                ->orWhere('bill_number', 'like', "%{$search}%")
                ->orWhere('order_number', 'like', "%{$search}%")
                ->orWhere('bill_date', 'like', "%{$search}%")
                ->orWhere('sub_total_amount', 'like', "%{$search}%")
                ->orWhere('tax_type', 'like', "%{$search}%")
                ->orWhere('grand_total_amount', 'like', "%{$search}%")
                ->orWhere('due_date', 'like', "%{$search}%");
            });
        }
        // Stats always computed on the user-applied filters (without stat_filter)
        $filteredQuery = clone $query;
        $totalTds = (clone $filteredQuery)->where('tax_amount', '>', 0)->sum('tax_amount');

        $thisMonthTds = (clone $filteredQuery)
            ->where('tax_amount', '>', 0)
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('tax_amount');

        $pendingTds = (clone $filteredQuery)
            ->where('tax_amount', '>', 0)
            ->where('tds_paid_status', 'Not Paid')
            ->sum('tax_amount');

        $paidTds = (clone $filteredQuery)
            ->where('tax_amount', '>', 0)
            ->where('tds_paid_status', 'Paid')
            ->sum('tax_amount');

        $tdsSummaryCalculation = [
            'total_tds'      => $totalTds,
            'this_month_tds' => $thisMonthTds,
            'pending_tds'    => $pendingTds,
            'paid_tds'       => $paidTds,
        ];

        // Apply stat_filter AFTER stats calculation
        $query->where('tax_amount', '>', 0);
        $sf = $request->get('stat_filter', ''); // default '' = all data
        if ($sf === 'paid') {
            $query->where('tds_paid_status', 'Paid');
        } elseif ($sf === 'pending') {
            $query->where('tds_paid_status', 'Not Paid');
        } elseif ($sf === 'month') {
            $query->where('tds_paid_status', 'Not Paid')
                  ->whereYear('created_at', $now->year)
                  ->whereMonth('created_at', $now->month);
        }
        // $sf === '' → no paid_status filter, show all

        $billlist = $query->paginate($perPage)->appends($request->all());
        if ($request->ajax()) {
            $html = view('vendor.partials.table.tds_summary_rows', compact('billlist','perPage','tdsSummaryCalculation'))->render();
            return response()->json(['html' => $html, 'stats' => $tdsSummaryCalculation]);
        }

        return view('vendor.tds_summary', ['admin' => $admin,'locations' => $locations,'billlist' => $billlist,'perPage' => $perPage,'TblZonesModel' => $TblZonesModel,'Tbltdssection' => $Tbltdssection,'Tblvendor' => $Tblvendor,'Tblcompany' => $Tblcompany,'tdsSummaryCalculation' => $tdsSummaryCalculation]);
    }
    public function downloadTdsSummary(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'xlsx';
        }
        $fileName = 'TDS_Summary_' . now()->format('Y_m_d_His') . '.' . $format;
        $writerType = $format === 'csv' ? ExcelExcel::CSV : ExcelExcel::XLSX;

        return Excel::download(
            new TdsSummaryExport($request->all(), $format),
            $fileName,
            $writerType,
            ['Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
// tds Report
public function gettdsreport(Request $request)
    {
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
         $now = Carbon::now();
        $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails','TblTDSsection','TblTDSsection.section'])->where('delete_status',0)->orderBy('id', 'desc');

        $tdsids = [];
        if ($request->filled('section_id')) {
            $sectionIds = array_filter(array_map('trim', explode(',', $request->section_id)));
            if (!empty($sectionIds)) {
                $tdsids = Tbltdstax::whereIn('section_id', $sectionIds)->pluck('id')->toArray();
            }
        }
        if (!empty($tdsids)) {
            $query->whereIn('tds_tax_id', $tdsids);
        }
        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
                $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
                $query->whereRaw("STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ?", [$from, $to]);
            } catch (\Exception $e) { /* invalid date */ }
        }
        if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Karnataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }
        if ($request->filled('zone_id')) {
            $ids = array_filter(explode(',', $request->zone_id));
            if (!empty($ids)) $query->whereIn('zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $ids = array_filter(explode(',', $request->branch_id));
            if (!empty($ids)) $query->whereIn('branch_id', $ids);
        }
        if ($request->filled('company_id')) {
            $ids = array_filter(explode(',', $request->company_id));
            if (!empty($ids)) $query->whereIn('company_id', $ids);
        }
        if ($request->filled('vendor_id')) {
            $ids = array_filter(explode(',', $request->vendor_id));
            if (!empty($ids)) $query->whereIn('vendor_id', $ids);
        }
        if ($request->filled('financial_name')) {
            $financialYears = array_filter(explode(',', $request->financial_name));

            $query->where(function ($query) use ($financialYears) {
                foreach ($financialYears as $fy) {
                    [$startYear, $endYear] = explode('-', $fy);

                    $fyStart = "$startYear-04-01";
                    $fyEnd   = "$endYear-03-31";
                    $query->orWhereBetween(
                        DB::raw("STR_TO_DATE(bill_date, '%d/%m/%Y')"),
                        [$fyStart, $fyEnd]
                    );
                }
            });
        }
        if ($request->filled('quarter_name')) {
            $quarterIds = array_filter(explode(',', $request->quarter_name));
            $query->whereIn('tds_quarter', $quarterIds);
        }
         if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                ->orWhere('zone_name', 'like', "%{$search}%")
                ->orWhere('branch_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('bill_gen_number', 'like', "%{$search}%")
                ->orWhere('bill_number', 'like', "%{$search}%")
                ->orWhere('order_number', 'like', "%{$search}%")
                ->orWhere('bill_date', 'like', "%{$search}%")
                ->orWhere('sub_total_amount', 'like', "%{$search}%")
                ->orWhere('tax_type', 'like', "%{$search}%")
                ->orWhere('grand_total_amount', 'like', "%{$search}%")
                ->orWhere('tds_challan_no', 'like', "%{$search}%")
                ->orWhere('tds_quarter', 'like', "%{$search}%")
                ->orWhere('tds_pay_date', 'like', "%{$search}%")
                ->orWhere('due_date', 'like', "%{$search}%");
                // ->orWhere('company_name', 'like', "%{$search}%");
                $q->orWhereHas('Tblvendor', function ($vendorQuery) use ($search) {
                    $vendorQuery->where('pan_number', 'like', "%{$search}%");
                });
            });
        }
        $filteredQuery = clone $query;
        $totalTds = (clone $filteredQuery)->sum('tax_amount');

        $thisMonthTds = (clone $filteredQuery)
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('tax_amount');

        $pendingTds = (clone $filteredQuery)
            ->where('tds_paid_status', 'Not Paid')
            ->sum('tax_amount');

        $paidTds = (clone $filteredQuery)
            ->where('tds_paid_status', 'Paid')
            ->sum('tax_amount');

        $tdsSummaryCalculation = [
                'total_tds'       => $totalTds,
                'this_month_tds'  => $thisMonthTds,
                'pending_tds'     => $pendingTds,
                'paid_tds'        => $paidTds,
            ];

        // Apply tax_amount filter
        $query->where('tax_amount', '>', 0);

        // Apply stat_filter AFTER stats calculation
        $sf = $request->get('stat_filter', '');
        if ($sf === 'paid') {
            $query->where('tds_paid_status', 'Paid');
        } elseif ($sf === 'pending') {
            $query->where('tds_paid_status', 'Not Paid');
        } elseif ($sf === 'month') {
            $query->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month);
        }
        // $sf === '' → show all (no tds_paid_status filter)

        $billlist = $query->where('delete_status',0)->where('tds_paid_status','Paid')->where('tax_amount', '>', 0)->paginate($perPage)->appends($request->all());
        if ($request->ajax()) {
            $html = view('vendor.partials.table.tds_report_rows', compact('billlist','perPage','tdsSummaryCalculation'))->render();
            return response()->json(['html' => $html, 'stats' => $tdsSummaryCalculation]);
        }

        return view('vendor.tds_report', ['admin' => $admin,'locations' => $locations,'billlist' => $billlist,'perPage' => $perPage,'TblZonesModel' => $TblZonesModel,'Tbltdssection' => $Tbltdssection,'Tblvendor' => $Tblvendor,'Tblcompany' => $Tblcompany,'tdsSummaryCalculation' => $tdsSummaryCalculation]);
    }

    public function downloadTdsReport(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'xlsx';
        }
        $fileName = 'TDS_Report_' . now()->format('Y_m_d_His') . '.' . $format;
        $writerType = $format === 'csv' ? ExcelExcel::CSV : ExcelExcel::XLSX;

        return Excel::download(
            new TdsReportExport($request, $format),
            $fileName,
            $writerType,
            ['Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
    public function downloadFyExcel(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'xlsx';
        }
        $writerType = $format === 'csv' ? ExcelExcel::CSV : ExcelExcel::XLSX;
        $contentType = $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

        if ((int) $request->type === 1) {
            $fileName = 'TDS_FY_WISE_REPORT_' . now()->format('Y_m_d_His') . '.' . $format;
            return Excel::download(
                new TdsFyWiseExport($request, $format),
                $fileName,
                $writerType,
                ['Content-Type' => $contentType]
            );
        }
        $fileName = 'TDS_UTR_REPORT_' . now()->format('Y_m_d_His') . '.' . $format;
        return Excel::download(
            new TdsDetailedExport($request, $format),
            $fileName,
            $writerType,
            ['Content-Type' => $contentType]
        );
    }
// gst summary
public function getgstsummary(Request $request)
    {
        $admin = auth()->user();
        $limit_access=$admin->access_limits;
        $locations = TblLocationModel::all();
        $perPage = $request->get('per_page', 10);
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
        $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
        $now = Carbon::now();
        $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])
                ->whereHas('BillLines', function ($q) {
                    $q->whereRaw('CAST(gst_amount AS DECIMAL(10,2)) > 0');
                })
                ->where('delete_status',0)
                ->orderBy('id', 'desc');


        if ($request->filled('section_id')) {
            $tdsids = Tbltdstax::where('section_id', $request->section_id)->pluck('id')->toArray();
        }
        if (!empty($tdsids)) {
            $query->whereIn('tds_tax_id', $tdsids);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            // $query->whereBetween('created_at', [$from, $to]);
            $query->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
        }
        if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Karnataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }

        if ($request->filled('zone_id')) {
            $ids = explode(',', $request->zone_id);
            $query->whereIn('zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $ids = explode(',', $request->branch_id);
            $query->whereIn('branch_id', $ids);
        }
        if ($request->filled('company_id')) {
            $ids = explode(',', $request->company_id);
            $query->whereIn('company_id', $ids);
        }
        if ($request->filled('vendor_id')) {
            $ids = explode(',', $request->vendor_id);
            $query->whereIn('vendor_id', $ids);
        }
         if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                ->orWhere('zone_name', 'like', "%{$search}%")
                ->orWhere('branch_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('bill_gen_number', 'like', "%{$search}%")
                ->orWhere('bill_number', 'like', "%{$search}%")
                ->orWhere('order_number', 'like', "%{$search}%")
                ->orWhere('bill_date', 'like', "%{$search}%")
                ->orWhere('sub_total_amount', 'like', "%{$search}%")
                ->orWhere('tax_type', 'like', "%{$search}%")
                ->orWhere('grand_total_amount', 'like', "%{$search}%")
                ->orWhere('due_date', 'like', "%{$search}%");
                // ->orWhere('company_name', 'like', "%{$search}%");
            });
        }
        // --- GST totals from BillLines ---

        $filteredQuery = clone $query;
        $allBills = (clone $filteredQuery)->get();

        $totalGst = 0;
        $thisMonthGst = 0;
        $pendingGst = 0;
        $paidGst = 0;
        $partialGst = 0;

        foreach ($allBills as $bill) {
            // dd($bill);
            $gstSum = $bill->BillLines->sum('gst_amount');

            $totalGst += $gstSum;

            if ($bill->created_at->year == $now->year && $bill->created_at->month == $now->month) {
                $thisMonthGst += $gstSum;
            }
            if ($bill->bill_status == 'Due to Pay') {
                $pendingGst += $gstSum;
            } elseif ($bill->bill_status == 'Paid') {
                $paidGst += $gstSum;
            } elseif ($bill->bill_status == 'Partially Payed') {
                $partialGst += $gstSum;
            }
        }
        $gstSummaryCalculation = [
                'total_gst'       => $totalGst,
                'this_month_gst'  => $thisMonthGst,
                'pending_gst'     => $pendingGst,
                'paid_gst'        => $paidGst,
                'partial_gst'     => $partialGst,
            ];

        // Apply stat_filter AFTER calculating stats
        $sf = $request->get('stat_filter', '');
        if ($sf === 'pending') {
            $query->where('bill_status', 'Due to Pay');
        } elseif ($sf === 'paid') {
            $query->where('bill_status', 'Paid');
        } elseif ($sf === 'partial') {
            $query->where('bill_status', 'Partially Payed');
        } elseif ($sf === 'month') {
            $query->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month);
        }
        // $sf === '' → show all

        $billlist = $query->paginate($perPage)->appends($request->all());
        if ($request->ajax()) {
            $html = view('vendor.partials.table.gst_summary_rows', compact('billlist','perPage','gstSummaryCalculation'))->render();
            return response()->json(['html' => $html, 'stats' => $gstSummaryCalculation]);
        }

        return view('vendor.gst_summary', ['admin' => $admin,'locations' => $locations,'billlist' => $billlist,'perPage' => $perPage,'TblZonesModel' => $TblZonesModel,'Tbltdssection' => $Tbltdssection,'Tblvendor' => $Tblvendor,'Tblcompany' => $Tblcompany,'gstSummaryCalculation' => $gstSummaryCalculation]);
    }
    public function downloadGstSummary(Request $request)
    {
        $fileName = 'GST_Summary_' . now()->format('Y_m_d_His') . '.xlsx';
        return Excel::download(new GstSummaryExport($request), $fileName);
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

   public function reportindex(Request $request)
{
    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $locations = TblLocationModel::all();
    $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
    $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
    $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
    $Tblaccount = Tblaccount::orderBy('id', 'asc')->get();

    // ---------- Build the bills query for the full-page result (with relations) ----------
    $billsQuery = Tblbill::with([
        'BillLines',
        'Tblvendor',
        'TblBilling',
        'Tblbankdetails',
        'Purchase',
        'Purchase.quotation',
        'billPayments'
    ])->where('delete_status',0)->orderBy('id', 'desc');


    // Apply filters
        // if ($request->filled('date_from') && $request->filled('date_to')) {
        //     $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
        //     $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
        //     $billsQuery->whereBetween('created_at', [$from, $to]);
        // }
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->format('Y-m-d');
        $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->format('Y-m-d');
        $billsQuery->whereRaw("
            STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ?
        ", [$from, $to]);
    }

    if ($request->filled('zone_id')) {
        $zoneIds = explode(',', $request->zone_id);
        $billsQuery->whereIn('zone_id', $zoneIds);
    }

    if ($request->filled('branch_id')) {
        $branchIds = explode(',', $request->branch_id);
        $billsQuery->whereIn('branch_id', $branchIds);
    }

    if ($request->filled('company_id')) {
        $companyIds = explode(',', $request->company_id);
        $billsQuery->whereIn('company_id', $companyIds);
    }

    if ($request->filled('vendor_id')) {
        $vendorIds = explode(',', $request->vendor_id);
        $billsQuery->whereIn('vendor_id', $vendorIds);
    }

    if ($request->filled('universal_search')) {
        $search = $request->universal_search;
        $billsQuery->where(function ($q) use ($search) {
            $q->where('bill_number', 'like', "%{$search}%")
              ->orWhereHas('Tblvendor', function ($q2) use ($search) {
                  $q2->where('vendor_name', 'like', "%{$search}%");
              })
              ->orWhereHas('BillLines', function ($q3) use ($search) {
                  $q3->where('description', 'like', "%{$search}%");
              });
        });
    }

    if ($request->filled('nature_id')) {
        $natureIds = explode(',', $request->nature_id);
        $billsQuery->whereHas('BillLines', function ($q) use ($natureIds) {
            $q->whereIn('account_id', $natureIds);
        });
    }

    $bills = $billsQuery->paginate(15)->withQueryString();

    $lineAgg = TblBillLines::select(
            'bill_lines_tbl.*',
            'bill_lines_tbl.account',
            DB::raw('SUM(bill_lines_tbl.amount) as total_amount')
        )
        ->join('bill_tbl', 'bill_lines_tbl.bill_id', '=', 'bill_tbl.id')
        ->groupBy('bill_lines_tbl.account'); // or other columns you group by

    // Apply filters
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
        $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
        $lineAgg->whereBetween('bill_tbl.created_at', [$from, $to]);
    }
    if ($request->filled('zone_id')) {
        $zoneIds = explode(',', $request->zone_id);
        $lineAgg->whereIn('bill_tbl.zone_id', $zoneIds);
    }
    if ($request->filled('branch_id')) {
        $branchIds = explode(',', $request->branch_id);
        $lineAgg->whereIn('bill_tbl.branch_id', $branchIds);
    }
    if ($request->filled('company_id')) {
        $companyIds = explode(',', $request->company_id);
        $lineAgg->whereIn('bill_tbl.company_id', $companyIds);
    }
    if ($request->filled('vendor_id')) {
        $vendorIds = explode(',', $request->vendor_id);
        $lineAgg->whereIn('bill_tbl.vendor_id', $vendorIds);
    }
    if ($request->filled('universal_search')) {
        $s = $request->universal_search;
        $lineAgg->where(function ($q) use ($s) {
            $q->where('bill_lines_tbl.description', 'like', "%{$s}%")
              ->orWhere('bill_tbl.bill_number', 'like', "%{$s}%")
              // optionally search vendor name via join if needed:
              ->orWhereExists(function ($sub) use ($s) {
                  $sub->select(DB::raw(1))
                      ->from('tbl_vendors')
                      ->whereRaw('tbl_vendors.id = tbl_bills.vendor_id')
                      ->where('vendor_name', 'like', "%{$s}%");
              });
        });
    }
    if ($request->filled('nature_id')) {
        $natureIds = explode(',', $request->nature_id);
        $lineAgg->whereIn('bill_lines_tbl.account_id', $natureIds); // <-- important: use account_id
    }

    $topExpenses = $lineAgg
        ->groupBy('bill_lines_tbl.account') // or account_id if you want ID grouping
        ->orderByDesc('total_amount')
        ->get();

    if ($request->ajax()) {
        return response()->json([
            'labels' => $topExpenses->pluck('account'),
            'values' => $topExpenses->pluck('total_amount'),
            'list'   => $topExpenses->map(function($item) {
                return [
                    'account' => $item->account,
                    'total_amount' => number_format($item->total_amount, 2)
                ];
            }),
        ]);
    }

    // ---------- Full page view ----------
    return view('vendor.report_master', compact(
        'topExpenses',
        'bills',
        'admin',
        'limit_access',
        'locations',
        'TblZonesModel',
        'Tblcompany',
        'Tblvendor',
        'Tblaccount'
    ));
}




   public function showExpenseDetails(Request $request, $type)
{
    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $locations = TblLocationModel::all();
    $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
    $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
    $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
    $Tblaccount = Tblaccount::orderBy('id', 'asc')->get();

    // ---------- Base Query ----------
    $detailsQuery = TblBill::with([
        'BillLines' => function ($q) use ($type) {
            $q->where('account', $type);
        },
        'Tblvendor'
    ])->whereHas('BillLines', function ($q) use ($type) {
        $q->where('account', $type);
    });


     // Apply filters
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
        $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
        $detailsQuery->whereRaw("
                STR_TO_DATE(bill_tbl.bill_date, '%d/%m/%Y') BETWEEN ? AND ?
            ", [$from, $to]);
        // $detailsQuery->whereBetween('created_at', [$from, $to]);
    }
    if($request->filled('state_name')){
        $state_name=$request->state_name;
        if ($state_name === 'Tamil Nadu') {
            $ids = ['2','4','6','7','8','9'];
            $detailsQuery->whereIn('zone_id', $ids);
        }elseif($state_name === 'Karnataka') {
            $ids = ['3'];
            $detailsQuery->whereIn('zone_id', $ids);
        }elseif($state_name === 'Kerala') {
            $ids = ['5'];
            $detailsQuery->whereIn('zone_id', $ids);
        }elseif($state_name === 'International') {
            $ids = ['10'];
            $detailsQuery->whereIn('zone_id', $ids);
        }elseif($state_name === 'Andra Pradesh') {
            $ids = ['30'];
            $detailsQuery->whereIn('branch_id', $ids);
        }
    }

    if ($request->filled('zone_id')) {
        $zoneIds = array_filter(explode(',', $request->zone_id));
        $detailsQuery->whereIn('zone_id', $zoneIds);
    }


    if ($request->filled('branch_id')) {
        $branchIds = array_filter(explode(',', $request->branch_id));
        $detailsQuery->whereIn('branch_id', $branchIds);
    }

    if ($request->filled('company_id')) {
        $companyIds = array_filter(explode(',', $request->company_id));
        $detailsQuery->whereIn('company_id', $companyIds);
    }

    if ($request->filled('vendor_id')) {
        $vendorIds = array_filter(explode(',', $request->vendor_id));
        $detailsQuery->whereIn('vendor_id', $vendorIds);
    }


    if ($request->filled('nature_id')) {
        $natureIds = array_filter(explode(',', $request->nature_id));
        $detailsQuery->whereHas('BillLines', function ($q) use ($natureIds) {
            $q->whereIn('account_id', $natureIds);
        });
    }



    if ($request->filled('universal_search')) {
        $search = $request->universal_search;
        $detailsQuery->where(function ($q) use ($search) {
            $q->where('bill_gen_number', 'like', "%{$search}%")
              ->orWhere('branch_name', 'like', "%{$search}%")
              ->orWhere('zone_name', 'like', "%{$search}%")
              ->orWhere('sub_total_amount', 'like', "%{$search}%")
              ->orWhere('grand_total_amount', 'like', "%{$search}%")
              ->orWhereHas('Tblvendor', function ($v) use ($search) {
                  $v->where('vendor_name', 'like', "%{$search}%");
              });
        });
    }

    // ---------- Execute Query ----------
    $details = $detailsQuery->orderBy('id', 'desc')->get();
    // dd($details);
    // Group bills by vendor
    $groupedVendors = [];

    foreach ($details as $bill) {
        $vendorName = $bill->Tblvendor->display_name ?? $bill->vendor_name ?? 'Unknown Vendor';

        if (!isset($groupedVendors[$vendorName])) {
            $groupedVendors[$vendorName] = [
                'vendor_id' => $bill->vendor_id,
                'total_amount' => 0,
                'bills' => []
            ];
        }

        foreach ($bill->BillLines as $line) {
            $groupedVendors[$vendorName]['total_amount'] += $line->amount ?? 0;
        }

        $groupedVendors[$vendorName]['bills'][] = $bill;
    }
    // dd($groupedVendors);
    // ---------- Compute Totals ----------
    $totalInvoiceAmount = 0;
    $totalFinalAmount = 0;
    $totalTDS = 0;
    $totalGST = 0;

    // foreach ($details as $bill) {
    //     $totalTDS           += $bill->tds_amount ?? 0;
    //     $totalInvoiceAmount += $bill->sub_total_amount ?? 0;
    //     $totalFinalAmount   += $bill->grand_total_amount ?? 0;
    //     $totalGST           += $bill->tax_amount ?? 0; // GST from bill header (tax_amount)
    // }

    $processedBills = [];

    foreach ($details as $bill) {

        if (in_array($bill->id, $processedBills)) {
            continue;
        }

        $processedBills[] = $bill->id;

        $totalInvoiceAmount += (float) $bill->sub_total_amount;
        $totalFinalAmount   += (float) $bill->grand_total_amount;
        $totalTDS           += (float) $bill->tds_amount;

        foreach ($bill->BillLines as $line) {
            $totalGST += (float) $line->gst_amount;
        }
    }
// dd($totalGST);
    // ---------- Handle Exports ----------
    if ($request->has('export')) {
        if ($request->export === 'excel') {
            return Excel::download(
                new \App\Exports\ExpenseDetailsExport(
                    $details,
                    $type,
                    $totalInvoiceAmount,
                    $totalFinalAmount,
                    $totalTDS,
                    $totalGST
                ),
                "expense_details_{$type}.xlsx"
            );
        }

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('vendor.expense_details_pdf', compact(
                'details',
                'type',
                'totalInvoiceAmount',
                'totalFinalAmount',
                'totalTDS',
                'totalGST'
            ));
            return $pdf->download("expense_details_{$type}.pdf");
        }
    }
    if ($request->ajax()) {
        return view('vendor.partials.table.report_view_rows', [
            'details' => $details,
            'type'    => $type,
            'totalInvoiceAmount' => $totalInvoiceAmount,
            'totalFinalAmount'   => $totalFinalAmount,
            'totalTDS'           => $totalTDS,
            'totalGST'           => $totalGST,
            'groupedVendors'     => $groupedVendors, // <-- ADD THIS
        ])->render();
    }


    // dd($groupedVendors,$details);
    // ---------- Return View ----------
    return view('vendor.report_view', compact(
                    'admin',
                    'limit_access',
                    'locations',
                    'details',
                    'type',
                    'totalInvoiceAmount',
                    'totalFinalAmount',
                    'totalTDS',
                    'totalGST',
                    'TblZonesModel',
                    'Tblcompany',
                    'Tblvendor',
                    'Tblaccount',
                    'groupedVendors'
                ));

}

public function printMultiple(Request $request)
{
    $billIds = $request->bill_ids ?? [];

    if (empty($billIds)) {
        return response()->json(['error' => 'No bills selected'], 400);
    }

    $bills = TblBill::with(['Tblvendor', 'TblBilling', 'TblCompany', 'BillLines'])
        ->whereIn('id', $billIds)
        ->get();

    $pdf = new \TCPDF();
    $pdf->setPrintHeader(false); // ✅ removes top line
    $pdf->setPrintFooter(false); // ✅ removes bottom line
    $pdf->SetCreator('Laravel');
    $pdf->SetAuthor('Superadmin');
    $pdf->SetTitle('Bills');
    $pdf->SetMargins(10, 50, 10);
    $pdf->SetAutoPageBreak(true, 15);

    foreach ($bills as $bill) {
        $pdf->AddPage();
        $pdf->SetY(50); // Start a bit lower (safe zone)
        include base_path('resources/views/vendor/dynamicprint.blade.php');
    }

    // Force PDF to download
    return response($pdf->Output('multi_bills.pdf', 'S'))
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="multi_bills.pdf"');
}

public function vendorSummary(Request $request)
    {
        $admin = auth()->user();
        $limit_access = $admin->access_limits;
        $locations = TblLocationModel::all();
        $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'asc')->get();
        // -------- Base query similar to your reportindex() ----------
        $billsQuery = TblBill::with([
            'BillLines',
            'Tblvendor',
            'TblBilling',
            'Tblbankdetails',
            'Purchase',
            'Purchase.quotation',
            'billPayments'
        ])->orderBy('id', 'desc');
        // dd($billsQuery);

        // Apply filters if available
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            // $billsQuery->whereBetween('created_at', [$from, $to]);
            $billsQuery->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
        }

        if ($request->filled('zone_id')) {
            $billsQuery->whereIn('zone_id', explode(',', $request->zone_id));
        }
        if ($request->filled('branch_id')) {
            $billsQuery->whereIn('branch_id', explode(',', $request->branch_id));
        }
        if ($request->filled('company_id')) {
            $billsQuery->whereIn('company_id', explode(',', $request->company_id));
        }
        if ($request->filled('vendor_id')) {
            $billsQuery->whereIn('vendor_id', explode(',', $request->vendor_id));
        }
        if ($request->filled('nature_id')) {
            $natureIds = explode(',', $request->nature_id);
            $billsQuery->whereHas('BillLines', function ($query) use ($natureIds) {
                $query->whereIn('account_id', $natureIds);
            });
        }

        // ── Apply stat_filter ──
        $statFilter = $request->get('stat_filter', '');
        if ($statFilter === 'paid') {
            $billsQuery->where('bill_status', 'Paid');
        } elseif ($statFilter === 'due') {
            $billsQuery->where('bill_status', 'Due to Pay');
        }

        // ---------------- Main Vendor Summary Grouping ----------------
        // $bills = $billsQuery->get();

        // // Flatten & group by account name
        // $vendorSummary = $bills->flatMap(function ($bill) {
        //     return $bill->BillLines->map(function ($line) use ($bill) {
        //         return [
        //             'account' => $line->account,
        //             'vendor' => $bill->Tblvendor->display_name ?? $bill->vendor_name,
        //             'vendor_id' => $bill->vendor_id,
        //             'bill_total' => $bill->grand_total_amount ?? 0,
        //             'paid' => $bill->partially_payment ?? 0,
        //             'due' => $bill->balance_amount ?? 0,
        //         ];
        //     });
        // })->groupBy('account');

        // // Compute totals per vendor under each account
        // $summaryData = $vendorSummary->map(function ($items, $account) {
        //     // dd($items, $account);
        //     $vendors = collect($items)->groupBy('vendor')->map(function ($rows, $vendorName) {
        //         // dd($rows, $vendorName);
        //         return [
        //             'vendor_name' => $vendorName,
        //             'vendor_id' => $rows[0]['vendor_id'],
        //             'bills' => $rows->sum('bill_total'),
        //             'paid' => $rows->sum('paid'),
        //             'due' => $rows->sum('due'),
        //         ];
        //     });

        //     return [
        //         'account' => $account,
        //         'vendors' => $vendors,
        //         'total_bills' => $vendors->sum('bills'),
        //         'total_paid' => $vendors->sum('paid'),
        //         'total_due' => $vendors->sum('due'),
        //     ];
        // });

        $bills = $billsQuery->get();
        $rows = collect();
        foreach ($bills as $bill) {
            $subTotal   = (float) ($bill->sub_total_amount ?? 0);
            $grandTotal = (float) ($bill->grand_total_amount ?? 0);

            if ($subTotal == 0) {
                $subTotal = 1;
            }

            foreach ($bill->BillLines as $line) {
                $lineAmount = (float) ($line->amount ?? 0);
                // ratio based on subtotal
                $ratio = $lineAmount / $subTotal;
                // split bill total to each account
                $finalAmount = $grandTotal * $ratio;

                $rows->push([
                    'account'   => $line->account,
                    'vendor'    => $bill->Tblvendor->display_name ?? $bill->vendor_name,
                    'vendor_id' => $bill->vendor_id,

                    'bill_total' => $finalAmount,
                    'paid' => 0,

                    'due'  => $finalAmount,
                ]);
            }
        }

        $vendorSummary = $rows->groupBy('account');
        $summaryData = $vendorSummary->map(function ($items, $account) {

            $vendors = collect($items)
                ->groupBy('vendor')
                ->map(function ($rows, $vendorName) {

                    return [
                        'vendor_name' => $vendorName,
                        'vendor_id' => $rows->first()['vendor_id'],

                        'bills' => $rows->sum('bill_total'),
                        'paid'  => $rows->sum('paid'),
                        'due'   => $rows->sum('due'),
                    ];
                });

            return [
                'account' => $account,
                'vendors' => $vendors,
                'total_bills' => $vendors->sum('bills'),
                'total_paid'  => $vendors->sum('paid'),
                'total_due'   => $vendors->sum('due'),
            ];
        });

        if ($request->ajax()) {
            // dd(12);
            // Render ONLY the tbody rows from a partial view
            return view('vendor.vendor_summary_dynamic', compact('summaryData'))->render();
        }
        // dd($summaryData);
        // ---------------- Return to Blade ----------------
        return view('vendor.vendor_summary', compact('summaryData','admin','limit_access','locations','Tblvendor','Tblaccount'));
    }

    public function getVendorMonthlySummary(Request $request)
    {
        // dd($request);
        $vendorId = $request->vendor_id;
        $account = $request->account;


        $bills = TblBill::with(['BillLines'])
                ->where('vendor_id', $vendorId)
                ->whereHas('BillLines', function ($query) use ($account) {
                    $query->where('account', $account);
                })
                ->get();

        // Group bills by month
        $monthlyData = $bills->groupBy(function ($bill) {
            return \Carbon\Carbon::parse($bill->created_at)->format('F Y');
        })->map(function ($monthBills) {
            $total = 0;
            foreach ($monthBills as $bill) {
                foreach ($bill->BillLines as $line) {
                    $total += $line->amount ?? 0;
                }
            }
            return $total;
        });
        // dd($monthlyData);
        // Build the HTML table body
        $html = '';
        foreach ($monthlyData as $month => $amount) {
            $html .= "<tr>
                        <td>{$month}</td>
                        <td>₹" . number_format($amount, 2) . "</td>
                    </tr>";
        }

        $total = $monthlyData->sum();
        $html .= "<tr class='table-success'>
                    <th>Total</th>
                    <th>₹" . number_format($total, 2) . "</th>
                </tr>";

        return response()->json(['html' => $html]);
    }


// ─────────────────────────────────────────────────────────────
// Zone / Branch Payment-Type Chart  (page + AJAX data)
// ─────────────────────────────────────────────────────────────
public function getZonePaymentChart(Request $request)
{
    $admin         = auth()->user();
    $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
    $Tblcompany    = Tblcompany::orderBy('id', 'asc')->paginate(10);
    $Tblvendor     = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
    $locations     = TblLocationModel::orderBy('id', 'asc')->get();

    return view('vendor.report_zone_chart', compact(
        'admin', 'TblZonesModel', 'Tblcompany', 'Tblvendor', 'locations'
    ));
}

public function getZonePaymentChartData(Request $request)
{
    // ── Date range ──────────────────────────────────────────
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
        $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
    } else {
        $now = Carbon::now();
        if ($now->month >= 4) {
            $from = Carbon::create($now->year, 4, 1)->startOfDay();
            $to   = Carbon::create($now->year + 1, 3, 31)->endOfDay();
        } else {
            $from = Carbon::create($now->year - 1, 4, 1)->startOfDay();
            $to   = Carbon::create($now->year, 3, 31)->endOfDay();
        }
    }

    // ── Base query on billing_list ───────────────────────────
    $baseQuery = BillingListModel::select(
        'location_name',
        'paymenttype',
        DB::raw('SUM(amt) as total')
    )
    ->whereBetween(
        DB::raw("STR_TO_DATE(billing_list.billdate, '%Y%m%d%H:%i:%s')"),
        [$from, $to]
    )
    ->whereNotNull('paymenttype')
    ->where('paymenttype', '!=', '')
    ->groupBy('location_name', 'paymenttype');

    // ── Optional filters ─────────────────────────────────────
    if ($request->filled('state_id')) {
        $state_ids = explode(',', $request->state_id);
        $locations = [];
        $zoneMap = ['1'=>['2','4','6','7','8','9'],'2'=>['3'],'3'=>['5'],'4'=>['10']];
        $branchMap = ['5'=>['30']];
        foreach ($state_ids as $sid) {
            if (isset($zoneMap[$sid])) {
                $zids = $zoneMap[$sid];
                $locs = TblLocationModel::whereIn('zone_id', $zids)->pluck('name')->toArray();
                $locations = array_merge($locations, $locs);
            }
            if (isset($branchMap[$sid])) {
                $locs = TblLocationModel::whereIn('id', $branchMap[$sid])->pluck('name')->toArray();
                $locations = array_merge($locations, $locs);
            }
        }
        if (!empty($locations)) {
            $baseQuery->whereIn('location_name', array_unique($locations));
        }
    }
    if ($request->filled('zone_id')) {
        $zoneIds = explode(',', $request->zone_id);
        $locNames = TblLocationModel::whereIn('zone_id', $zoneIds)->pluck('name')->toArray();
        if (!empty($locNames)) {
            $baseQuery->whereIn('location_name', $locNames);
        }
    }
    if ($request->filled('branch_id')) {
        $branchIds = explode(',', $request->branch_id);
        $locNames = TblLocationModel::whereIn('id', $branchIds)->pluck('name')->toArray();
        if (!empty($locNames)) {
            $baseQuery->whereIn('location_name', $locNames);
        }
    }

    $rows = $baseQuery->get();

    // ── All distinct payment types (for consistent colours) ──
    $allPayTypes = $rows->pluck('paymenttype')->unique()->sort()->values()->toArray();

    // ─── Zone-wise aggregation ────────────────────────────────
    $zones      = TblZonesModel::orderBy('id')->get();
    $locations  = TblLocationModel::all()->keyBy('name');

    $zoneData = [];
    foreach ($rows as $row) {
        $loc    = $locations->get($row->location_name);
        $zoneId = $loc ? $loc->zone_id : null;
        if (!$zoneId) continue;
        $zone   = $zones->firstWhere('id', $zoneId);
        $zoneName = $zone ? $zone->name : 'Unknown';
        $zoneData[$zoneName][$row->paymenttype] = ($zoneData[$zoneName][$row->paymenttype] ?? 0) + $row->total;
    }

    // ─── Branch-wise aggregation ──────────────────────────────
    $branchData = [];
    foreach ($rows as $row) {
        $loc = $locations->get($row->location_name);
        $branchName = $loc ? $loc->name : $row->location_name;
        $branchData[$branchName][$row->paymenttype] = ($branchData[$branchName][$row->paymenttype] ?? 0) + $row->total;
    }

    // Sort branches by total descending
    uasort($branchData, function($a, $b) {
        return array_sum($b) - array_sum($a);
    });

    // ── Build datasets per payment type ─────────────────────
    // Each entry uses a clearly distinct hue — no two should look similar
    $paymentColors = [
        'card'            => '#1a56db', // Bold blue
        'cash'            => '#0e9f6e', // Emerald green
        'cheque'          => '#9061f9', // Violet/purple
        'neft'            => '#ff5a1f', // Deep orange
        'upi'             => '#e02424', // Bright red
        'savedforcounter' => '#0694a2', // Teal/cyan
        'online'          => '#c27803', // Amber/gold
        'dd'              => '#d61f69', // Hot pink
        'imps'            => '#4d7c0f', // Olive green
        'rtgs'            => '#3730a3', // Indigo
        'cash counter'    => '#92400e', // Brown
        'other'           => '#6b7280', // Gray
    ];

    // Fallback palette — all maximally distinct from each other and from above
    $defaults = [
        '#1a56db','#0e9f6e','#9061f9','#ff5a1f','#e02424',
        '#0694a2','#c27803','#d61f69','#4d7c0f','#3730a3',
        '#92400e','#6b7280','#0f766e','#b45309','#7c3aed',
    ];

    $buildDatasets = function ($groupData, $payTypes) use ($paymentColors, $defaults) {
        $labels   = array_keys($groupData);
        $datasets = [];
        foreach ($payTypes as $idx => $pt) {
            $key   = strtolower(trim($pt));
            $color = $paymentColors[$key] ?? $defaults[$idx % count($defaults)];
            $data = [];
            foreach ($labels as $label) {
                $data[] = round($groupData[$label][$pt] ?? 0, 2);
            }
            $datasets[] = [
                'label'           => $pt,
                'data'            => $data,
                'backgroundColor' => $color . 'cc',
                'borderColor'     => $color,
                'borderWidth'     => 1,
            ];
        }
        // Totals per label
        $totals = [];
        foreach ($labels as $label) {
            $totals[$label] = array_sum(array_values($groupData[$label]));
        }
        return ['labels' => $labels, 'datasets' => $datasets, 'totals' => $totals];
    };

    // Build a color map for each payment type (same logic as $buildDatasets)
    $typeColors = [];
    foreach ($allPayTypes as $idx => $pt) {
        $key = strtolower(trim($pt));
        $typeColors[$pt] = $paymentColors[$key] ?? $defaults[$idx % count($defaults)];
    }

    // Grand totals per payment type (across all zones/branches)
    $paymentTotals = [];
    $grandTotal    = 0;
    foreach ($rows as $row) {
        $paymentTotals[$row->paymenttype] = ($paymentTotals[$row->paymenttype] ?? 0) + $row->total;
        $grandTotal += $row->total;
    }
    // Sort by value descending
    arsort($paymentTotals);

    return response()->json([
        'payment_types'  => $allPayTypes,
        'type_colors'    => $typeColors,          // exact color per payment type
        'zone_chart'     => $buildDatasets($zoneData, $allPayTypes),
        'branch_chart'   => $buildDatasets($branchData, $allPayTypes),
        'date_range'     => $from->format('d/m/Y') . ' – ' . $to->format('d/m/Y'),
        'payment_totals' => $paymentTotals,
        'grand_total'    => round($grandTotal, 2),
    ]);
}

public function getAllCharts(Request $request)
{
    $branchNames = [];

    // Branches filter
    if ($request->filled('zone_id')) {
        $branchNames = TblLocationModel::where('zone_id', $request->zone_id)
                        ->pluck('name')
                        ->toArray();
    }
    if ($request->filled('branch_id')) {
        $branchNames = TblLocationModel::whereIn('id', explode(',', $request->branch_id))
                        ->pluck('name')
                        ->toArray();
    }
    // dd($branchNames);
    // Date filter
    $from = $to = null;
    // if ($request->filled('date_from') && $request->filled('date_to')) {
    //     $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
    //     $to = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
    // }

    if ($request->filled('date_from') && $request->filled('date_to')) {

        $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
        $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();

    } else {
        // ✅ DEFAULT: THIS FINANCIAL YEAR (India)
        $now = Carbon::now();

        if ($now->month >= 4) {
            $from = Carbon::create($now->year, 4, 1)->startOfDay();          // Apr 1 current year
            $to   = Carbon::create($now->year + 1, 3, 31)->endOfDay();       // Mar 31 next year
        } else {
            $from = Carbon::create($now->year - 1, 4, 1)->startOfDay();      // Apr 1 last year
            $to   = Carbon::create($now->year, 3, 31)->endOfDay();           // Mar 31 current year
        }
    }

    // ---------------------------
    // Monthly Income
    // ---------------------------
    // dd($from, $to);
    $monthlyIncomeQuery = BillingListModel::select(
        DB::raw("MONTH(STR_TO_DATE(billdate, '%Y%m%d%H:%i:%s')) as month"),
        DB::raw('SUM(amt) as income')
    )->groupBy('month')->orderBy('month');

    if (!empty($branchNames)) {
        $monthlyIncomeQuery->whereIn('location_name', $branchNames);
    }
    if ($from !== null && $to !== null) {
        $monthlyIncomeQuery->whereBetween(
            DB::raw("STR_TO_DATE(billing_list.billdate, '%Y%m%d%H:%i:%s')"),[$from, $to]
        );
    }
   if ($request->has('state_id')) {

    $state_ids = is_array($request->state_id)
        ? $request->state_id
        : explode(',', $request->state_id);

    $locations = [];

    if (in_array('1', $state_ids)) {
        $locations = array_merge($locations, [
            'Chennai - Sholinganallur','Chennai - Madipakkam',
            'Chennai - Urapakkam','Kanchipuram','Thiruvallur',
            'Chennai - Tambaram','Chennai - Vadapalani',
            'Corporate Office - Guindy','Chengalpattu',
            'Chennai - Karapakkam','Hosur','Salem','Harur',
            'Kallakurichi','Thirupathur','Thiruvannamalai',
            'Aathur','Namakal','Dharmapuri','Karur',
            'Krishnagiri','Pennagaram','Perambalur',
            'Trichy','Tanjore','Madurai','Villupuram',
            'Nagapattinam','Sivakasi',
            'Coimbatore - Ganapathy','Coimbatore - Sundarapuram',
            'Pollachi','Coimbatore - Thudiyalur',
            'Tirupati','Vellore','Tiruppur','Erode','Sathyamangalam'
        ]);
    }

    if (in_array('2', $state_ids)) {
        $locations = array_merge($locations, [
            'Bengaluru - Electronic City',
            'Bengaluru - Konanakunte',
            'Bengaluru - Hebbal',
            'Bengaluru - Dasarahalli'
        ]);
    }

    if (in_array('3', $state_ids)) {
        $locations = array_merge($locations, [
            'Kerala - Palakkad','Kerala - Kozhikode'
        ]);
    }

    if (in_array('4', $state_ids)) {
        $locations = array_merge($locations, [
            'Sri Lanka','Bangladesh','International'
        ]);
    }

    if (in_array('5', $state_ids)) {
        $locations[] = 'Tirupati';
    }
    // dd($locations);
    if (!empty($locations)) {
        $monthlyIncomeQuery->whereIn('location_name', array_unique($locations));
    }
}



    $monthlyIncome = $monthlyIncomeQuery->get();

    // ---------------------------
    // Monthly Expense
    // ---------------------------
    $expenseQuery = TblBillLines::select(
        DB::raw('MONTH(STR_TO_DATE(bill_tbl.bill_date, "%d/%m/%Y")) as month'),
        DB::raw('SUM(bill_lines_tbl.amount) as expense')
    )
    ->join('bill_tbl', 'bill_lines_tbl.bill_id', '=', 'bill_tbl.id')
    ->where('delete_status',0)
    ->groupBy('month')
    ->orderBy('month');
        // dd($from,$to);
    if ($from!==null && $to!==null ) {
        $expenseQuery->whereRaw("
                STR_TO_DATE(bill_tbl.bill_date, '%d/%m/%Y') BETWEEN ? AND ?
            ", [$from, $to]);
    }
     if ($request->has('state_id')) {

        $state_ids = is_array($request->state_id)
            ? $request->state_id
            : explode(',', $request->state_id);

        $zoneIds   = [];
        $branchIds = [];

        // TN
        if (in_array('1', $state_ids)) {
            $zoneIds = array_merge($zoneIds, ['2','4','6','7','8','9']);
        }

        // Karnataka
        if (in_array('2', $state_ids)) {
            $zoneIds[] = '3';
        }

        // Kerala
        if (in_array('3', $state_ids)) {
            $zoneIds[] = '5';
        }

        // International
        if (in_array('4', $state_ids)) {
            $zoneIds[] = '10';
        }

        // Tirupati (branch-based)
        if (in_array('5', $state_ids)) {
            $branchIds[] = '30';
        }

        // APPLY FILTERS ONCE (🔥 IMPORTANT)
        $expenseQuery->where(function ($q) use ($zoneIds, $branchIds) {

            if (!empty($zoneIds)) {
                $q->whereIn('zone_id', array_unique($zoneIds));
            }

            if (!empty($branchIds)) {
                $q->orWhereIn('branch_id', array_unique($branchIds));
            }
        });
    }


    if ($request->filled('zone_id')) {
        $expenseQuery->where('bill_tbl.zone_id', $request->zone_id);
    }
    if ($request->filled('branch_id')) {
        $expenseQuery->whereIn('bill_tbl.branch_id', explode(',', $request->branch_id));
    }
    if ($request->filled('vendor_id')) {
        $vendorIds = explode(',', $request->vendor_id);
        $expenseQuery->whereIn('bill_tbl.vendor_id', $vendorIds);
    }
    if ($request->filled('universal_search')) {
        $s = $request->universal_search;
        $expenseQuery->where(function ($q) use ($s) {
            $q->where('bill_lines_tbl.description', 'like', "%{$s}%")
              ->orWhere('bill_tbl.bill_number', 'like', "%{$s}%")
              // optionally search vendor name via join if needed:
              ->orWhereExists(function ($sub) use ($s) {
                  $sub->select(DB::raw(1))
                      ->from('tbl_vendors')
                      ->whereRaw('tbl_vendors.id = tbl_bills.vendor_id')
                      ->where('vendor_name', 'like', "%{$s}%");
              });
        });
    }
    if ($request->filled('nature_id')) {
        $natureIds = explode(',', $request->nature_id);
        $expenseQuery->whereIn('bill_lines_tbl.account_id', $natureIds); // <-- important: use account_id
    }

    // dd($expenseQuery->get());
    $monthlyExpense = $expenseQuery->get();
    // dd($monthlyExpense);
    // ---------------------------
    // Income by Payment Type
    // ---------------------------
    $paymentQuery = BillingListModel::select(
        'paymenttype',
        DB::raw('SUM(amt) as total_income')
    )->groupBy('paymenttype');

    if (!empty($branchNames)) {
        $paymentQuery->whereIn('location_name', $branchNames);
    }
     if ($request->has('state_id')) {

        $state_ids = is_array($request->state_id)
            ? $request->state_id
            : explode(',', $request->state_id);

        $locations = [];

        if (in_array('1', $state_ids)) {
            $locations = array_merge($locations, [
                'Chennai - Sholinganallur','Chennai - Madipakkam',
                'Chennai - Urapakkam','Kanchipuram','Thiruvallur',
                'Chennai - Tambaram','Chennai - Vadapalani',
                'Corporate Office - Guindy','Chengalpattu',
                'Chennai - Karapakkam','Hosur','Salem','Harur',
                'Kallakurichi','Thirupathur','Thiruvannamalai',
                'Aathur','Namakal','Dharmapuri','Karur',
                'Krishnagiri','Pennagaram','Perambalur',
                'Trichy','Tanjore','Madurai','Villupuram',
                'Nagapattinam','Sivakasi',
                'Coimbatore - Ganapathy','Coimbatore - Sundarapuram',
                'Pollachi','Coimbatore - Thudiyalur',
                'Tirupati','Vellore','Tiruppur','Erode','Sathyamangalam'
            ]);
        }

        if (in_array('2', $state_ids)) {
            $locations = array_merge($locations, [
                'Bengaluru - Electronic City',
                'Bengaluru - Konanakunte',
                'Bengaluru - Hebbal',
                'Bengaluru - Dasarahalli'
            ]);
        }

        if (in_array('3', $state_ids)) {
            $locations = array_merge($locations, [
                'Kerala - Palakkad','Kerala - Kozhikode'
            ]);
        }

        if (in_array('4', $state_ids)) {
            $locations = array_merge($locations, [
                'Sri Lanka','Bangladesh','International'
            ]);
        }

        if (in_array('5', $state_ids)) {
            $locations[] = 'Tirupati';
        }
        // dd($locations);
        if (!empty($locations)) {
            $paymentQuery->whereIn('location_name', array_unique($locations));
        }
    }

     if ($from !== null && $to !== null) {
        $paymentQuery->whereBetween(
            DB::raw("STR_TO_DATE(billing_list.billdate, '%Y%m%d%H:%i:%s')"),[$from, $to]
        );
    }


    $paymentIncome = $paymentQuery->get();

    // ---------------------------
    // Top Expenses
    // ---------------------------
    $topExpenseQuery = TblBillLines::select(
        'bill_lines_tbl.account',
        DB::raw('SUM(bill_lines_tbl.amount) as total_amount')
    )->join('bill_tbl', 'bill_lines_tbl.bill_id', '=', 'bill_tbl.id')
      ->groupBy('bill_lines_tbl.account')
      ->orderByDesc('total_amount');

    if ($from && $to) {
        // $topExpenseQuery->whereBetween('bill_tbl.created_at', [$from, $to]);
        $topExpenseQuery->whereRaw("
                STR_TO_DATE(bill_tbl.bill_date, '%d/%m/%Y') BETWEEN ? AND ?
            ", [$from, $to]);
    }
     if ($request->has('state_id')) {

        $state_ids = is_array($request->state_id)
            ? $request->state_id
            : explode(',', $request->state_id);
        $zoneIds   = [];
        $branchIds = [];

        // TN
        if (in_array('1', $state_ids)) {
            $zoneIds = array_merge($zoneIds, ['2','4','6','7','8','9']);
        }

        // Karnataka
        if (in_array('2', $state_ids)) {
            $zoneIds[] = '3';
        }

        // Kerala
        if (in_array('3', $state_ids)) {
            $zoneIds[] = '5';
        }

        // International
        if (in_array('4', $state_ids)) {
            $zoneIds[] = '10';
        }

        // Tirupati (branch-based)
        if (in_array('5', $state_ids)) {
            $branchIds[] = '30';
        }
        // APPLY FILTERS ONCE (🔥 IMPORTANT)
        $topExpenseQuery->where(function ($q) use ($zoneIds, $branchIds) {

            if (!empty($zoneIds)) {
                $q->whereIn('zone_id', array_unique($zoneIds));
            }

            if (!empty($branchIds)) {
                $q->orWhereIn('branch_id', array_unique($branchIds));
            }
        });
    }


    if ($request->filled('zone_id')) {
        $topExpenseQuery->where('bill_tbl.zone_id', $request->zone_id);
    }
    if ($request->filled('branch_id')) {
        $topExpenseQuery->whereIn('bill_tbl.branch_id', explode(',', $request->branch_id));
    }
    if ($request->filled('vendor_id')) {
        $vendorIds = explode(',', $request->vendor_id);
        $topExpenseQuery->whereIn('bill_tbl.vendor_id', $vendorIds);
    }
    if ($request->filled('universal_search')) {
        $s = $request->universal_search;
        $topExpenseQuery->where(function ($q) use ($s) {
            $q->where('bill_lines_tbl.description', 'like', "%{$s}%")
              ->orWhere('bill_tbl.bill_number', 'like', "%{$s}%")
              // optionally search vendor name via join if needed:
              ->orWhereExists(function ($sub) use ($s) {
                  $sub->select(DB::raw(1))
                      ->from('tbl_vendors')
                      ->whereRaw('tbl_vendors.id = tbl_bills.vendor_id')
                      ->where('vendor_name', 'like', "%{$s}%");
              });
        });
    }
    if ($request->filled('nature_id')) {
        $natureIds = explode(',', $request->nature_id);
        $topExpenseQuery->whereIn('bill_lines_tbl.account_id', $natureIds); // <-- important: use account_id
    }

    $topExpenses = $topExpenseQuery->get();

    $months = [];
    $cursor = $from->copy()->startOfMonth();

    while ($cursor <= $to) {
        $months[] = $cursor->month;   // numeric month
        $cursor->addMonth();
    }
    $totalIncome  = $monthlyIncome->sum('income');
    $totalExpense = $monthlyExpense->sum('expense');

    // Total bills count using same filters
    $billsCountQuery = TblBill::where('delete_status', 0);
    if ($from !== null && $to !== null) {
        $billsCountQuery->whereRaw("STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ?", [$from, $to]);
    }
    if ($request->filled('zone_id')) {
        $billsCountQuery->whereIn('zone_id', explode(',', $request->zone_id));
    }
    if ($request->filled('branch_id')) {
        $billsCountQuery->whereIn('branch_id', explode(',', $request->branch_id));
    }
    if ($request->filled('vendor_id')) {
        $billsCountQuery->whereIn('vendor_id', explode(',', $request->vendor_id));
    }
    if ($request->filled('company_id')) {
        $billsCountQuery->whereIn('company_id', explode(',', $request->company_id));
    }
    $totalBills = $billsCountQuery->count();

    return response()->json([
        'monthly_income' => [
            'months' => $months,
            'income' => $monthlyIncome->pluck('income', 'month'),
        ],
        'income_vs_expense' => [
            'months' => $months,
            'income' => $monthlyIncome->pluck('income', 'month'),
            'expense' => $monthlyExpense->pluck('expense', 'month'),
        ],
        'payment_type_income' => [
            'payment_types' => $paymentIncome->pluck('paymenttype'),
            'income' => $paymentIncome->pluck('total_income'),
        ],
        'top_expenses' => [
            'labels' => $topExpenses->pluck('account'),
            'values' => $topExpenses->pluck('total_amount'),
            'list' => $topExpenses->map(function ($item) {
                return ['account' => $item->account, 'total_amount' => number_format($item->total_amount, 2)];
            }),
        ],
        'stats' => [
            'total_income'  => round($totalIncome, 2),
            'total_expense' => round($totalExpense, 2),
            'net_amount'    => round($totalIncome - $totalExpense, 2),
            'total_bills'   => $totalBills,
        ],
    ]);
}

public function billtdsupdate(Request $request)
{
    // dd($request);
    $request->validate([
        'bill_date'  => 'required|date_format:d/m/Y',
        'challan_no' => 'required|string|max:50',
        'quarter'    => 'required|string',
    ]);

    // Convert dd/mm/yyyy → yyyy-mm-dd

    // Save to DB (example table: bill_pay_tbl)
    $data = Tblbill::where('id', $request->bill_id)
            ->update([
                'tds_pay_date'  => $request->bill_date,
                'tds_utr_no'  => $request->utr_no,
                'tds_challan_no' => $request->challan_no,
                'tds_quarter'    => $request->quarter,
                'tds_paid_status'    => 'Paid',
                'updated_at' => now()
            ]);

    return response()->json([
        'status' => true,
        'message' => 'Updated successfully!'
    ]);
}
// Professional summary
public function getprofessionalsummary(Request $request)
{
    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $locations = TblLocationModel::all();
    $perPage = $request->get('per_page', 10);
    $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
    $Tbltdssection = Tbltdssection::orderBy('id', 'asc')->paginate(10);
    $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);
    $Tblvendor = Tblvendor::where('active_status', 0)->orderBy('id', 'asc')->get();
    $now = Carbon::now();

    // Define the account names from your image
    $professionalAccountNames = [
        'PROFESSIONAL FEES STAFF',
        'PROFESSIONAL FEES - DOCTORS',
        'PROFESSIONAL FEES ADVOCATE',
        'STAFF SALARY',
        'PROFESSIONAL FEES'
    ];
    $accounts = Tblaccount::whereIn('name', $professionalAccountNames)->get();
    $accountsids = Tblaccount::whereIn('name', $professionalAccountNames)->pluck('id');


    $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails','TblTDSsection','TblTDSsection.section'])
                ->whereHas('BillLines', function($q) use ($accountsids) {
                    $q->whereIn('account_id', $accountsids);
                })
                ->where('delete_status',0)
                ->orderBy('id', 'desc');

    if ($request->filled('nature_id')) {
        $ids = explode(',', $request->nature_id);

        $query->whereHas('BillLines', function ($q) use ($ids) {
            $q->whereIn('account_id', $ids);
        });
    }

    // Apply filters
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
        $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
        // $query->whereBetween('created_at', [$from, $to]);
        $query->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
    }
    if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Karnataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            }elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }
    if ($request->filled('zone_id')) {
        $ids = explode(',', $request->zone_id);
        $query->whereIn('zone_id', $ids);
    }

    if ($request->filled('branch_id')) {
        $ids = explode(',', $request->branch_id);
        $query->whereIn('branch_id', $ids);
    }

    if ($request->filled('company_id')) {
        $ids = explode(',', $request->company_id);
        $query->whereIn('company_id', $ids);
    }

    if ($request->filled('vendor_id')) {
        $ids = explode(',', $request->vendor_id);
        $query->whereIn('vendor_id', $ids);
    }

    if ($request->filled('universal_search')) {
        $search = $request->universal_search;
        $query->where(function($q) use ($search) {
            $q->where('vendor_name', 'like', "%{$search}%")
            ->orWhere('zone_name', 'like', "%{$search}%")
            ->orWhere('branch_name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->orWhere('bill_gen_number', 'like', "%{$search}%")
            ->orWhere('bill_number', 'like', "%{$search}%")
            ->orWhere('order_number', 'like', "%{$search}%")
            ->orWhere('bill_date', 'like', "%{$search}%")
            ->orWhere('sub_total_amount', 'like', "%{$search}%")
            ->orWhere('tax_type', 'like', "%{$search}%")
            ->orWhere('grand_total_amount', 'like', "%{$search}%")
            ->orWhere('bill_status', 'like', "%{$search}%")
            ->orWhere('due_date', 'like', "%{$search}%");
        });
    }

    // ── Stats calculated BEFORE stat_filter is applied ──
    $filteredQuery = clone $query;
    $totalInvoice = (clone $filteredQuery)->sum('sub_total_amount');
    $totalFullinvoice = (clone $filteredQuery)->sum('grand_total_amount');

    $pendingInvoice = (clone $filteredQuery)
        ->where('bill_status', 'Due to Pay')
        ->sum('sub_total_amount');

    $paidInvoice = (clone $filteredQuery)
        ->where('bill_status', 'Paid')
        ->sum('sub_total_amount');

    $totalTax = (clone $filteredQuery)->sum('tax_amount');
    $totalGst = (clone $filteredQuery)
                ->with('BillLines')
                ->get()
                ->flatMap(fn($bill) => $bill->BillLines)
                ->sum('gst_amount');
                $totalTaxAndGst = $totalTax + $totalGst;

    // ── Apply stat_filter to table query ──
    $statFilter = $request->get('stat_filter', '');
    if ($statFilter === 'pending') {
        $query->where('bill_status', 'Due to Pay');
    } elseif ($statFilter === 'paid') {
        $query->where('bill_status', 'Paid');
    }
                // dd($totalTax, $totalGst,$totalTaxAndGst);

    function formatIndianCurrencyFallback($amount) {
        if ($amount === null) return "₹ 0.00";

        $negative = false;
        if ($amount < 0) {
            $negative = true;
            $amount = abs($amount);
        }

        // Split integer and decimal parts
        $parts = explode('.', number_format((float)$amount, 2, '.', ''));
        $intPart = $parts[0];
        $decPart = $parts[1] ?? '00';

        // If length <= 3, simple
        $len = strlen($intPart);
        if ($len <= 3) {
            $result = $intPart;
        } else {
            // Get last 3 digits
            $last3 = substr($intPart, -3);
            $rest  = substr($intPart, 0, -3);

            // Put commas every 2 digits on the rest
            $rest = strrev($rest);
            $rest = chunk_split($rest, 2, ',');
            $rest = rtrim($rest, ',');
            $rest = strrev($rest);

            $result = $rest . ',' . $last3;
        }

        $formatted = '₹ ' . $result . '.' . $decPart;
        return $negative ? '-' . $formatted : $formatted;
    }

    // wrapper to try intl then fallback
    function formatIndianCurrencySafe($amount) {
        if (class_exists(\NumberFormatter::class)) {
            try {
                $fmt = new \NumberFormatter('en_IN', \NumberFormatter::CURRENCY);
                return $fmt->formatCurrency((float) $amount, 'INR');
            } catch (\Exception $e) {
                // fallback
            }
        }
        return formatIndianCurrencyFallback($amount);
    }

    $invoiceSummaryCalculation = [
        'totalInvoice'       => formatIndianCurrencySafe($totalInvoice),
        'totalFullinvoice'   => formatIndianCurrencySafe($totalFullinvoice),
        'pendingInvoice'     => formatIndianCurrencySafe($pendingInvoice),
        'paidInvoice'        => formatIndianCurrencySafe($paidInvoice),
        'totalTaxAndGst'     => formatIndianCurrencySafe($totalTaxAndGst),
    ];




    $billlist = $query->paginate($perPage)->appends($request->all());

    if ($request->ajax()) {
        $html = view('vendor.partials.table.professional_summary_rows', compact('billlist','perPage','invoiceSummaryCalculation'))->render();
        return response()->json(['html' => $html, 'stats' => $invoiceSummaryCalculation]);
    }

    return view('vendor.professional_summary', [
        'admin' => $admin,
        'locations' => $locations,
        'billlist' => $billlist,
        'perPage' => $perPage,
        'TblZonesModel' => $TblZonesModel,
        'Tbltdssection' => $Tbltdssection,
        'Tblvendor' => $Tblvendor,
        'Tblcompany' => $Tblcompany,
        'invoiceSummaryCalculation' => $invoiceSummaryCalculation,
        'accounts' => $accounts
    ]);
}
    public function downloadprofessionalSummary(Request $request)
    {
        $fileName = 'Professional_Summary_' . now()->format('Y_m_d_His') . '.xlsx';
        return Excel::download(new ProfessionalSummaryExport($request), $fileName);
    }

    /**
     * billing_list rows treated as final / approved (exclude cancel & refund lines from income splits).
     */
    private function incomeSummarySqlApproved(): string
    {
        return '( (billing_list.billtype IS NULL OR TRIM(billing_list.billtype) = \'\' OR LOWER(TRIM(billing_list.billtype)) NOT IN (\'cancelled\',\'cancel\',\'refund\',\'refunded\')) '
            . 'AND (billing_list.type IS NULL OR (LOWER(billing_list.type) NOT LIKE \'%cancel%\' AND LOWER(billing_list.type) NOT LIKE \'%refund%\')) )';
    }

    private function incomeSummarySqlCancel(): string
    {
        return '( LOWER(TRIM(COALESCE(billing_list.billtype,\'\'))) IN (\'cancelled\',\'cancel\') OR LOWER(COALESCE(billing_list.type,\'\')) LIKE \'%cancel%\' )';
    }

    private function incomeSummarySqlRefund(): string
    {
        return '( LOWER(TRIM(COALESCE(billing_list.billtype,\'\'))) IN (\'refund\',\'refunded\') OR LOWER(COALESCE(billing_list.type,\'\')) LIKE \'%refund%\' )';
    }

    /**
     * One row per branch: merge SQL groups that only differ by whitespace/casing or duplicate location_id keys.
     *
     * @param  Collection<int, object>  $rows
     * @return Collection<int, object>
     */
    private function incomeSummaryMergeRowsByLocationName(Collection $rows): Collection
    {
        return $rows
            ->groupBy(function ($row) {
                $name = trim((string) ($row->location_name ?? ''));

                return $name === ''
                    ? '__empty__' . (string) ($row->location_id ?? '')
                    : mb_strtolower($name);
            })
            ->map(function (Collection $grp) {
                $labelRow = $grp->sortByDesc(function ($r) {
                    return strlen(trim((string) ($r->location_name ?? '')));
                })->first();

                $sum = static fn (string $col): float => $grp->sum(fn ($r) => (float) ($r->{$col} ?? 0));

                $ids = $grp->pluck('location_id')->filter(fn ($v) => $v !== null && $v !== '')->unique()->sort()->values();

                // Overall total = payment columns only (never discount / cancel / refund).
                $totalPayments = $sum('cash') + $sum('card') + $sum('cheque') + $sum('dd')
                    + $sum('neft') + $sum('credit') + $sum('upi');

                return (object) [
                    'location_id' => $ids->first(),
                    'location_name' => (string) ($labelRow->location_name ?? ''),
                    'cash' => $sum('cash'),
                    'card' => $sum('card'),
                    'cheque' => $sum('cheque'),
                    'dd' => $sum('dd'),
                    'neft' => $sum('neft'),
                    'credit' => $sum('credit'),
                    'upi' => $sum('upi'),
                    'discount' => $sum('discount'),
                    'cancel_amt' => $sum('cancel_amt'),
                    'refund_amt' => $sum('refund_amt'),
                    'total' => $totalPayments,
                ];
            })
            ->values()
            ->sortBy(fn ($r) => mb_strtolower(trim((string) ($r->location_name ?? ''))))
            ->values();
    }

    /**
     * Bucket billing lines into OP / IP / Pharmacy / Other for service-line matrix.
     */
    private function incomeSummaryServiceBucketSql(): string
    {
        // Normalize I/P, I / P, i/p → "ip" for IP bucket; include Advance (type or billtype) under IP.
        $bt = 'LOWER(TRIM(COALESCE(billing_list.billtype,\'\')))';
        $ty = 'LOWER(TRIM(COALESCE(billing_list.type,\'\')))';
        $btNorm = 'REPLACE(REPLACE(' . $bt . ',\' \',\'\'),\'/\',\'\')';
        $tyNorm = 'REPLACE(REPLACE(' . $ty . ',\' \',\'\'),\'/\',\'\')';

        return 'CASE '
            . 'WHEN ' . $bt . ' IN (\'pharmacy\',\'store\') '
            . 'OR ' . $ty . ' LIKE \'%pharmacy%\' '
            . 'OR ' . $ty . ' LIKE \'%store%\' THEN \'Pharmacy\' '
            . 'WHEN ' . $btNorm . ' = \'ip\' OR ' . $tyNorm . ' = \'ip\' '
            . 'OR ' . $ty . ' = \'advance\' OR ' . $bt . ' = \'advance\' THEN \'IP\' '
            . 'WHEN ' . $bt . ' IN (\'op\',\'o/p\') OR ' . $ty . ' IN (\'op\',\'o/p\') '
            . 'OR REPLACE(REPLACE(' . $bt . ',\' \',\'\'),\'/\',\'\') = \'op\' '
            . 'OR REPLACE(REPLACE(' . $ty . ',\' \',\'\'),\'/\',\'\') = \'op\' THEN \'OP\' '
            . 'ELSE \'Other\' END';
    }

    /**
     * OP / IP / Pharmacy × payment columns (final approved amounts only) for branch drill-down.
     *
     * @return array<string, mixed>
     */
    private function buildIncomeBranchMatrixPayload(Request $request): array
    {
        $locationId = $request->input('location_id');
        $locationName = trim((string) $request->input('location_name', ''));
        $dateFilter = $request->input('date_filter', 'yesterday');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $ap = $this->incomeSummarySqlApproved();
        $bucket = $this->incomeSummaryServiceBucketSql();

        $base = BillingListModel::query();
        $this->incomeSummaryApplyDateToQuery($base, (string) $dateFilter, $startDate, $endDate);

        if ($locationName !== '') {
            $base->whereRaw('TRIM(billing_list.location_name) = ?', [$locationName]);
        } elseif ($locationId) {
            $base->where(function ($q) use ($locationId) {
                $q->where('billing_list.location_id', $locationId)
                    ->orWhere('billing_list.location_name', $locationId);
            });
        } else {
            return [
                'view' => 'matrix',
                'matrix_rows' => [],
                'matrix_grand_total' => 0.0,
                'rows' => [],
                'total' => 0,
                'page' => 1,
                'last_page' => 1,
                'per_page' => 0,
                'grand_total' => 0.0,
                'type_groups' => [],
            ];
        }

        $base->whereRaw($ap);

        $agg = (clone $base)->selectRaw($bucket . ' as svc_cat')
            ->selectRaw('SUM(CASE WHEN billing_list.paymenttype = "Cash" THEN billing_list.amt ELSE 0 END) as cash')
            ->selectRaw('SUM(CASE WHEN billing_list.paymenttype = "Card" THEN billing_list.amt ELSE 0 END) as card')
            ->selectRaw('SUM(CASE WHEN billing_list.paymenttype = "Cheque" THEN billing_list.amt ELSE 0 END) as cheque')
            ->selectRaw('SUM(CASE WHEN billing_list.paymenttype = "DD" THEN billing_list.amt ELSE 0 END) as dd')
            ->selectRaw('SUM(CASE WHEN billing_list.paymenttype = "Neft" THEN billing_list.amt ELSE 0 END) as neft')
            ->selectRaw('SUM(CASE WHEN billing_list.paymenttype = "Credit" THEN billing_list.amt ELSE 0 END) as credit')
            ->selectRaw('SUM(CASE WHEN billing_list.paymenttype = "UPI" THEN billing_list.amt ELSE 0 END) as upi')
            ->groupBy(DB::raw($bucket))
            ->get()
            ->keyBy('svc_cat');

        $pick = static function ($row, string $k): float {
            return round((float) ($row ? ($row->{$k} ?? 0) : 0), 2);
        };

        $matrixRows = [];
        foreach (['OP', 'IP', 'Pharmacy'] as $cat) {
            $row = $agg->get($cat);
            $cash = $pick($row, 'cash');
            $card = $pick($row, 'card');
            $cheque = $pick($row, 'cheque');
            $dd = $pick($row, 'dd');
            $neft = $pick($row, 'neft');
            $credit = $pick($row, 'credit');
            $upi = $pick($row, 'upi');
            $line = round($cash + $card + $cheque + $dd + $neft + $credit + $upi, 2);
            $matrixRows[] = [
                'category' => $cat,
                'cash' => $cash,
                'card' => $card,
                'cheque' => $cheque,
                'dd' => $dd,
                'neft' => $neft,
                'credit' => $credit,
                'upi' => $upi,
                'line_total' => $line,
            ];
        }

        $other = $agg->get('Other');
        if ($other) {
            $cash = $pick($other, 'cash');
            $card = $pick($other, 'card');
            $cheque = $pick($other, 'cheque');
            $dd = $pick($other, 'dd');
            $neft = $pick($other, 'neft');
            $credit = $pick($other, 'credit');
            $upi = $pick($other, 'upi');
            $line = round($cash + $card + $cheque + $dd + $neft + $credit + $upi, 2);
            if ($line > 0.0001) {
                $matrixRows[] = [
                    'category' => 'Others',
                    'cash' => $cash,
                    'card' => $card,
                    'cheque' => $cheque,
                    'dd' => $dd,
                    'neft' => $neft,
                    'credit' => $credit,
                    'upi' => $upi,
                    'line_total' => $line,
                ];
            }
        }

        $grand = round(array_sum(array_column($matrixRows, 'line_total')), 2);

        return [
            'view' => 'matrix',
            'matrix_rows' => $matrixRows,
            'matrix_grand_total' => $grand,
            'rows' => [],
            'total' => 0,
            'page' => 1,
            'last_page' => 1,
            'per_page' => 0,
            'grand_total' => $grand,
            'type_groups' => [],
        ];
    }

    /**
     * Resolve tbl_locations.id for income drill-down (branch display name or Mocdoc location key).
     *
     * @return int[]
     */
    private function incomeDrilldownResolveBranchIds(?string $locationName, $locationId): array
    {
        $ids = [];
        $name = trim((string) $locationName);
        if ($name !== '') {
            $loc = TblLocationModel::query()->whereRaw('TRIM(name) = ?', [$name])->first();
            if ($loc) {
                $ids[] = (int) $loc->id;
            }
        }
        if ($ids === [] && $locationId !== null && $locationId !== '') {
            $lid = (string) $locationId;
            if (ctype_digit($lid)) {
                $ids[] = (int) $lid;
            }
            $map = MocdocLocationKeys::locationKeyToNameMap();
            if (isset($map[$lid])) {
                $loc = TblLocationModel::query()->whereRaw('TRIM(name) = ?', [trim($map[$lid])])->first();
                if ($loc) {
                    $ids[] = (int) $loc->id;
                }
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }

    private static function incomeFormParseMoney($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }

        return (float) preg_replace('/[^0-9.\-]/', '', (string) $value);
    }

    /**
     * tbl_locations.id values for income rows (branch name ↔ form *_zone_id).
     *
     * @param  iterable<string|\Stringable|null>  $locationNames
     * @return int[]
     */
    private function incomeSummaryZoneIdsForLocationNames(iterable $locationNames): array
    {
        $ids = [];
        foreach ($locationNames as $nm) {
            $nm = trim((string) $nm);
            if ($nm === '') {
                continue;
            }
            $loc = TblLocationModel::query()->whereRaw('TRIM(name) = ?', [$nm])->first();
            if ($loc) {
                $ids[] = (int) $loc->id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * HMS discount / cancel / refund forms: final_approver 0 = pending, 1 = approved, 2 = rejected.
     * Request param dcr_status: approved | pending | rejected (aliases: unapproved, reject).
     */
    private function incomeSummaryDcrApproverValue(Request $request): int
    {
        $s = strtolower(trim((string) $request->input('dcr_status', 'approved')));

        return match ($s) {
            'pending', 'unapproved' => 0,
            'rejected', 'reject' => 2,
            default => 1,
        };
    }

    /**
     * Discount / cancel / refund totals per branch from HMS forms (same rules as drill-down).
     *
     * @param  int[]  $zoneIds  tbl_locations.id
     * @return array<int, array{discount: float, cancel: float, refund: float}>
     */
    private function incomeSummaryFormDcrAggregatesByZoneIds(array $zoneIds, string $dateFilter, $startDate, $endDate, int $dcrApprover = 1): array
    {
        $out = [];
        foreach ($zoneIds as $id) {
            $out[(int) $id] = ['discount' => 0.0, 'cancel' => 0.0, 'refund' => 0.0];
        }
        if ($zoneIds === []) {
            return [];
        }

        $dq = DiscountFormModel::query()
            ->whereIn('dis_zone_id', $zoneIds)
            ->where('final_approver', $dcrApprover);
        $this->incomeSummaryApplyDateToFormColumn($dq, 'hms_discount_form.created_at', $dateFilter, $startDate, $endDate);
        foreach ($dq->cursor() as $r) {
            $z = (int) ($r->dis_zone_id ?? 0);
            if (! isset($out[$z])) {
                continue;
            }
            $out[$z]['discount'] += self::incomeFormParseMoney($r->dis_post_discount ?? 0);
        }

        $rq = RefundFormModel::query()
            ->whereIn('ref_zone_id', $zoneIds)
            ->where('final_approver', $dcrApprover);
        $this->incomeSummaryApplyDateToFormColumn($rq, 'hms_refund_form.created_at', $dateFilter, $startDate, $endDate);
        foreach ($rq->cursor() as $r) {
            $z = (int) ($r->ref_zone_id ?? 0);
            if (! isset($out[$z])) {
                continue;
            }
            $out[$z]['refund'] += (float) ($r->ref_final_auth ?? 0);
        }

        $cq = CancelbillFormModel::query()
            ->whereIn('can_zone_id', $zoneIds)
            ->where('final_approver', $dcrApprover);
        $this->incomeSummaryApplyDateToCancelForm($cq, $dateFilter, $startDate, $endDate);
        foreach ($cq->cursor() as $r) {
            $z = (int) ($r->can_zone_id ?? 0);
            if (! isset($out[$z])) {
                continue;
            }
            $out[$z]['cancel'] += (float) ($r->can_total ?? 0);
        }

        foreach ($out as $k => $v) {
            $out[$k]['discount'] = round($v['discount'], 2);
            $out[$k]['cancel'] = round($v['cancel'], 2);
            $out[$k]['refund'] = round($v['refund'], 2);
        }

        return $out;
    }

    /**
     * Replace billing_list discount / cancel / refund columns with HMS form totals per branch.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $rows
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function incomeSummaryOverlayFormDcrOnRows(Collection $rows, string $dateFilter, $startDate, $endDate, int $dcrApprover = 1): Collection
    {
        $zoneIds = $this->incomeSummaryZoneIdsForLocationNames($rows->pluck('location_name'));
        if ($zoneIds === []) {
            return $rows;
        }

        $agg = $this->incomeSummaryFormDcrAggregatesByZoneIds($zoneIds, $dateFilter, $startDate, $endDate, $dcrApprover);

        $nameToZone = [];
        foreach ($rows as $r) {
            $nm = trim((string) ($r->location_name ?? ''));
            if ($nm === '') {
                continue;
            }
            $key = mb_strtolower($nm);
            if (isset($nameToZone[$key])) {
                continue;
            }
            $loc = TblLocationModel::query()->whereRaw('TRIM(name) = ?', [$nm])->first();
            if ($loc) {
                $nameToZone[$key] = (int) $loc->id;
            }
        }

        return $rows->map(function ($row) use ($agg, $nameToZone) {
            $nm = mb_strtolower(trim((string) ($row->location_name ?? '')));
            $zid = $nameToZone[$nm] ?? null;
            if ($zid !== null && isset($agg[$zid])) {
                $row->discount = $agg[$zid]['discount'];
                $row->cancel_amt = $agg[$zid]['cancel'];
                $row->refund_amt = $agg[$zid]['refund'];
            }

            return $row;
        });
    }

    /**
     * Date filter on a datetime column (created_at / can_date) matching income summary presets.
     */
    private function incomeSummaryApplyDateToFormColumn($query, string $column, string $dateFilter, $startDate, $endDate): void
    {
        if ($dateFilter === 'yesterday') {
            $y = Carbon::yesterday();
            $query->whereBetween($column, [$y->copy()->startOfDay(), $y->copy()->endOfDay()]);

            return;
        }

        if ($dateFilter === 'today') {
            $t = now();
            $query->whereBetween($column, [$t->copy()->startOfDay(), $t->copy()->endOfDay()]);

            return;
        }

        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $query->whereBetween($column, [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);

            return;
        }

        if ($dateFilter === 'this_month') {
            $query->whereBetween($column, [
                now()->copy()->startOfMonth(),
                now()->copy()->endOfMonth(),
            ]);

            return;
        }

        if ($dateFilter === 'last_2_months') {
            $query->whereBetween($column, [
                now()->subMonths(2)->startOfMonth(),
                now()->endOfMonth(),
            ]);

            return;
        }

        if ($dateFilter === 'last_3_months') {
            $query->whereBetween($column, [
                now()->subMonths(3)->startOfMonth(),
                now()->endOfMonth(),
            ]);
        }
    }

    /**
     * Cancel form: prefer business can_date, else created_at (aligned with cancel dashboard).
     */
    private function incomeSummaryApplyDateToCancelForm($query, string $dateFilter, $startDate, $endDate): void
    {
        $expr = 'COALESCE(DATE(hms_cancelbill_form.can_date), DATE(hms_cancelbill_form.created_at))';
        if ($dateFilter === 'yesterday') {
            $d = Carbon::yesterday()->toDateString();
            $query->whereRaw("$expr = ?", [$d]);

            return;
        }
        if ($dateFilter === 'today') {
            $d = now()->toDateString();
            $query->whereRaw("$expr = ?", [$d]);

            return;
        }
        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $query->whereRaw("$expr BETWEEN ? AND ?", [
                Carbon::parse($startDate)->toDateString(),
                Carbon::parse($endDate)->toDateString(),
            ]);

            return;
        }
        if ($dateFilter === 'this_month') {
            $query->whereRaw('YEAR('.$expr.') = ? AND MONTH('.$expr.') = ?', [(int) now()->year, (int) now()->month]);

            return;
        }
        if ($dateFilter === 'last_2_months') {
            $query->whereRaw($expr.' >= ?', [now()->subMonths(2)->startOfMonth()->toDateString()])
                ->whereRaw($expr.' <= ?', [now()->endOfMonth()->toDateString()]);

            return;
        }
        if ($dateFilter === 'last_3_months') {
            $query->whereRaw($expr.' >= ?', [now()->subMonths(3)->startOfMonth()->toDateString()])
                ->whereRaw($expr.' <= ?', [now()->endOfMonth()->toDateString()]);
        }
    }

    /**
     * Discount / Cancel / Refund drill-down from hms_discount_form, hms_cancelbill_form, hms_refund_form
     * (same branch linkage as discount / cancel / refund dashboards: tbl_locations via *_zone_id).
     *
     * @return array<string, mixed>
     */
    private function buildIncomeDcrDrilldownPayload(Request $request, string $kind): array
    {
        $locationId = $request->input('location_id');
        $locationName = trim((string) $request->input('location_name', ''));
        $dateFilter = (string) $request->input('date_filter', 'yesterday');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 50;

        $empty = static fn (): array => [
            'view' => 'list',
            'drill_kind' => strtolower($kind),
            'rows' => [],
            'total' => 0,
            'page' => 1,
            'last_page' => 1,
            'per_page' => $perPage,
            'grand_total' => 0.0,
            'type_groups' => [],
        ];

        $branchIds = $this->incomeDrilldownResolveBranchIds($locationName, $locationId);
        if ($branchIds === []) {
            return $empty();
        }

        $dcrApprover = $this->incomeSummaryDcrApproverValue($request);

        $formatDt = static function ($v): string {
            if (! $v) {
                return '—';
            }
            try {
                return Carbon::parse($v)->format('d/m/Y');
            } catch (\Throwable $e) {
                return '—';
            }
        };

        if ($kind === 'Discount') {
            $base = DiscountFormModel::query()
                ->select([
                    'hms_discount_form.dis_id',
                    'hms_discount_form.dis_branch_no',
                    'hms_discount_form.created_at',
                    'hms_discount_form.dis_wife_mrd_no',
                    'hms_discount_form.dis_husband_mrd_no',
                    'hms_discount_form.dis_wife_name',
                    'hms_discount_form.dis_husband_name',
                    'hms_discount_form.dis_service_name',
                    'hms_discount_form.dis_counselled_by',
                    'hms_discount_form.dis_post_discount',
                    'hms_discount_form.dis_total_bill',
                    'hms_discount_form.dis_form_status',
                    'tbl_locations.name as location_name',
                    'users.user_fullname as user_name',
                ])
                ->join('tbl_locations', 'hms_discount_form.dis_zone_id', '=', 'tbl_locations.id')
                ->leftJoin('users', 'hms_discount_form.created_by', '=', 'users.id')
                ->whereIn('hms_discount_form.dis_zone_id', $branchIds)
                ->where('hms_discount_form.final_approver', $dcrApprover);

            $this->incomeSummaryApplyDateToFormColumn($base, 'hms_discount_form.created_at', $dateFilter, $startDate, $endDate);

            $total = (clone $base)->count();
            $grandTotal = 0.0;
            foreach ((clone $base)->cursor() as $row) {
                $grandTotal += self::incomeFormParseMoney($row->dis_post_discount ?? 0);
            }

            $records = (clone $base)
                ->orderBy('hms_discount_form.created_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            $rows = $records->map(function ($r, $idx) use ($page, $perPage, $formatDt) {
                $wife = trim((string) ($r->dis_wife_name ?? ''));
                $hus = trim((string) ($r->dis_husband_name ?? ''));
                $patient = $wife !== '' && $hus !== '' ? $wife . ' / ' . $hus : ($wife !== '' ? $wife : ($hus !== '' ? $hus : '—'));
                $mrd = (string) ($r->dis_wife_mrd_no ?? '');
                if ($mrd === '') {
                    $mrd = (string) ($r->dis_husband_mrd_no ?? '');
                }
                $amt = self::incomeFormParseMoney($r->dis_post_discount ?? 0);

                return [
                    'sno' => ($page - 1) * $perPage + $idx + 1,
                    'location' => $r->location_name,
                    'date' => $formatDt($r->created_at),
                    'bill_no' => $r->dis_branch_no ? (string) $r->dis_branch_no : ('DIS-' . $r->dis_id),
                    'phid' => $mrd !== '' ? $mrd : '—',
                    'patient' => $patient,
                    'consultant' => $r->dis_counselled_by ? (string) $r->dis_counselled_by : '—',
                    'amount' => number_format($amt, 2),
                    'grand_total' => number_format(self::incomeFormParseMoney($r->dis_total_bill ?? 0), 2),
                    'discount' => number_format($amt, 2),
                    'bill_type' => 'Discount',
                    'pay_type' => (string) ($r->dis_form_status ?? '—'),
                    'user' => (string) ($r->user_name ?? '—'),
                ];
            });

            return [
                'view' => 'list',
                'drill_kind' => 'discount',
                'rows' => $rows,
                'total' => $total,
                'page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
                'per_page' => $perPage,
                'grand_total' => round($grandTotal, 2),
                'type_groups' => [],
            ];
        }

        if ($kind === 'Refund') {
            $base = RefundFormModel::query()
                ->select([
                    'hms_refund_form.ref_id',
                    'hms_refund_form.ref_branch_no',
                    'hms_refund_form.created_at',
                    'hms_refund_form.ref_wife_mrd_no',
                    'hms_refund_form.ref_husband_mrd_no',
                    'hms_refund_form.ref_wife_name',
                    'hms_refund_form.ref_husband_name',
                    'hms_refund_form.ref_service_name',
                    'hms_refund_form.ref_counselled_by',
                    'hms_refund_form.ref_total_bill',
                    'hms_refund_form.ref_final_auth',
                    'hms_refund_form.ref_form_status',
                    'tbl_locations.name as location_name',
                    'users.user_fullname as user_name',
                ])
                ->join('tbl_locations', 'hms_refund_form.ref_zone_id', '=', 'tbl_locations.id')
                ->leftJoin('users', 'hms_refund_form.created_by', '=', 'users.id')
                ->whereIn('hms_refund_form.ref_zone_id', $branchIds)
                ->where('hms_refund_form.final_approver', $dcrApprover);

            $this->incomeSummaryApplyDateToFormColumn($base, 'hms_refund_form.created_at', $dateFilter, $startDate, $endDate);

            $total = (clone $base)->count();
            $grandTotal = (float) (clone $base)->sum('ref_final_auth');

            $records = (clone $base)
                ->orderBy('hms_refund_form.created_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            $rows = $records->map(function ($r, $idx) use ($page, $perPage, $formatDt) {
                $wife = trim((string) ($r->ref_wife_name ?? ''));
                $hus = trim((string) ($r->ref_husband_name ?? ''));
                $patient = $wife !== '' && $hus !== '' ? $wife . ' / ' . $hus : ($wife !== '' ? $wife : ($hus !== '' ? $hus : '—'));
                $mrd = (string) ($r->ref_wife_mrd_no ?? '');
                if ($mrd === '') {
                    $mrd = (string) ($r->ref_husband_mrd_no ?? '');
                }
                $amt = (float) ($r->ref_final_auth ?? 0);

                return [
                    'sno' => ($page - 1) * $perPage + $idx + 1,
                    'location' => $r->location_name,
                    'date' => $formatDt($r->created_at),
                    'bill_no' => $r->ref_branch_no ? (string) $r->ref_branch_no : ('REF-' . $r->ref_id),
                    'phid' => $mrd !== '' ? $mrd : '—',
                    'patient' => $patient,
                    'consultant' => $r->ref_counselled_by ? (string) $r->ref_counselled_by : '—',
                    'amount' => number_format($amt, 2),
                    'grand_total' => number_format((float) ($r->ref_total_bill ?? 0), 2),
                    'discount' => '—',
                    'bill_type' => 'Refund',
                    'pay_type' => (string) ($r->ref_form_status ?? '—'),
                    'user' => (string) ($r->user_name ?? '—'),
                ];
            });

            return [
                'view' => 'list',
                'drill_kind' => 'refund',
                'rows' => $rows,
                'total' => $total,
                'page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
                'per_page' => $perPage,
                'grand_total' => round($grandTotal, 2),
                'type_groups' => [],
            ];
        }

        // Cancel
        $base = CancelbillFormModel::query()
            ->select([
                'hms_cancelbill_form.can_bill_no',
                'hms_cancelbill_form.can_date',
                'hms_cancelbill_form.created_at',
                'hms_cancelbill_form.can_mrdno',
                'hms_cancelbill_form.can_name',
                'hms_cancelbill_form.can_consultant',
                'hms_cancelbill_form.can_total',
                'hms_cancelbill_form.can_payment_type',
                'hms_cancelbill_form.can_form_status',
                'tbl_locations.name as location_name',
                'users.user_fullname as user_name',
            ])
            ->join('tbl_locations', 'hms_cancelbill_form.can_zone_id', '=', 'tbl_locations.id')
            ->leftJoin('users', 'hms_cancelbill_form.created_by', '=', 'users.id')
            ->whereIn('hms_cancelbill_form.can_zone_id', $branchIds)
            ->where('hms_cancelbill_form.final_approver', $dcrApprover);

        $this->incomeSummaryApplyDateToCancelForm($base, $dateFilter, $startDate, $endDate);

        $total = (clone $base)->count();
        $grandTotal = (float) (clone $base)->sum('can_total');

        $records = (clone $base)
            ->orderByDesc('hms_cancelbill_form.created_at')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $rows = $records->map(function ($r, $idx) use ($page, $perPage, $formatDt) {
            $amt = (float) ($r->can_total ?? 0);
            $bizDate = $r->can_date ?? $r->created_at;

            return [
                'sno' => ($page - 1) * $perPage + $idx + 1,
                'location' => $r->location_name,
                'date' => $formatDt($bizDate),
                'bill_no' => $r->can_bill_no ? (string) $r->can_bill_no : '—',
                'phid' => $r->can_mrdno ? (string) $r->can_mrdno : '—',
                'patient' => $r->can_name ? (string) $r->can_name : '—',
                'consultant' => $r->can_consultant ? (string) $r->can_consultant : '—',
                'amount' => number_format($amt, 2),
                'grand_total' => number_format($amt, 2),
                'discount' => '—',
                'bill_type' => 'Cancel',
                'pay_type' => (string) ($r->can_payment_type ?? $r->can_form_status ?? '—'),
                'user' => (string) ($r->user_name ?? '—'),
            ];
        });

        return [
            'view' => 'list',
            'drill_kind' => 'cancel',
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
            'per_page' => $perPage,
            'grand_total' => round($grandTotal, 2),
            'type_groups' => [],
        ];
    }

    /**
     * @return int[]
     */
    private function incomeSummaryIntListFromRequest(Request $request, string $key): array
    {
        $v = $request->input($key);
        if ($v === null || $v === '' || $v === []) {
            return [];
        }
        if (is_array($v)) {
            return array_values(array_unique(array_filter(array_map('intval', $v), static fn (int $x): bool => $x > 0)));
        }
        if (is_numeric($v)) {
            $n = (int) $v;

            return $n > 0 ? [$n] : [];
        }
        $parts = array_filter(array_map('trim', explode(',', (string) $v)));

        return array_values(array_unique(array_filter(array_map('intval', $parts), static fn (int $x): bool => $x > 0)));
    }

    /**
     * Intersected billing_list.location_id variants from state / zone / branch / single location filters.
     *
     * @return string[]|null null = no geo filter; [] = impossible match
     */
    private function incomeSummaryResolveLocationVariants(Request $request): ?array
    {
        $sets = [];

        if ($request->filled('state_id')) {
            $state_ids = $this->incomeSummaryIntListFromRequest($request, 'state_id');
            $zoneIds = [];
            $branchIds = [];
            foreach ($state_ids as $sid) {
                if ($sid === 1) {
                    $zoneIds = array_merge($zoneIds, ['2', '4', '6', '7', '8', '9']);
                }
                if ($sid === 2) {
                    $zoneIds[] = '3';
                }
                if ($sid === 3) {
                    $zoneIds[] = '5';
                }
                if ($sid === 4) {
                    $zoneIds[] = '10';
                }
                if ($sid === 5) {
                    $branchIds[] = '30';
                }
            }
            $zoneIds = array_unique($zoneIds);
            $branchIds = array_unique($branchIds);
            if ($zoneIds === [] && $branchIds === []) {
                $sets[] = [];
            } else {
                $locQuery = TblLocationModel::query();
                $locQuery->where(function ($q) use ($zoneIds, $branchIds) {
                    if ($zoneIds !== []) {
                        $q->whereIn('zone_id', $zoneIds);
                    }
                    if ($branchIds !== []) {
                        $q->orWhereIn('id', $branchIds);
                    }
                });
                $rows = $locQuery->get();
                $sets[] = MocdocLocationKeys::billingLocationIdVariantsForBranches($rows);
            }
        }

        $zonePick = array_merge(
            $this->incomeSummaryIntListFromRequest($request, 'zone_ids'),
            $this->incomeSummaryIntListFromRequest($request, 'zone_id')
        );
        if ($zonePick !== []) {
            $zonePick = array_unique($zonePick);
            $rows = TblLocationModel::whereIn('zone_id', $zonePick)->get();
            $sets[] = MocdocLocationKeys::billingLocationIdVariantsForBranches($rows);
        }

        $branchPick = array_merge(
            $this->incomeSummaryIntListFromRequest($request, 'branch_ids'),
            $this->incomeSummaryIntListFromRequest($request, 'branch_id')
        );
        if ($branchPick !== []) {
            $branchPick = array_unique($branchPick);
            $rows = TblLocationModel::whereIn('id', $branchPick)->get();
            $sets[] = MocdocLocationKeys::billingLocationIdVariantsForBranches($rows);
        }

        if ($request->filled('location')) {
            $loc = TblLocationModel::find((int) $request->input('location'));
            if ($loc) {
                $sets[] = MocdocLocationKeys::billingLocationIdVariantsForBranch($loc);
            }
        }

        if ($sets === []) {
            return null;
        }

        $out = $sets[0];
        for ($i = 1, $n = count($sets); $i < $n; $i++) {
            $out = array_values(array_intersect($out, $sets[$i]));
        }

        return $out;
    }

    private function incomeSummaryApplyDateToQuery($query, string $dateFilter, $startDate, $endDate): void
    {
        $dateCol = DB::raw("STR_TO_DATE(billing_list.billdate, '%Y%m%d%H:%i:%s')");

        if ($dateFilter === 'yesterday') {
            $y = Carbon::yesterday();
            $query->whereBetween($dateCol, [$y->copy()->startOfDay(), $y->copy()->endOfDay()]);

            return;
        }

        if ($dateFilter === 'today') {
            $t = now();
            $query->whereBetween($dateCol, [$t->copy()->startOfDay(), $t->copy()->endOfDay()]);

            return;
        }

        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $query->whereBetween($dateCol, [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59',
            ]);

            return;
        }

        if ($dateFilter === 'this_month') {
            $query->whereMonth($dateCol, now()->month);

            return;
        }

        if ($dateFilter === 'last_2_months') {
            $query->whereBetween($dateCol, [
                now()->subMonths(2)->startOfMonth(),
                now()->endOfMonth(),
            ]);

            return;
        }

        if ($dateFilter === 'last_3_months') {
            $query->whereBetween($dateCol, [
                now()->subMonths(3)->startOfMonth(),
                now()->endOfMonth(),
            ]);
        }
    }

  public function vendorincomeReport(Request $request)
    {
        $admin = auth()->user();

        $dateFilter = $request->input('date_filter');
        if ($dateFilter === null || $dateFilter === '') {
            $dateFilter = 'yesterday';
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if ($dateFilter === 'yesterday' && ! $startDate && ! $endDate) {
            $y = Carbon::yesterday()->format('Y-m-d');
            $startDate = $y;
            $endDate = $y;
        }
        if ($dateFilter === 'custom' && (! $startDate || ! $endDate)) {
            $y = Carbon::yesterday()->format('Y-m-d');
            $startDate = $startDate ?: $y;
            $endDate = $endDate ?: $y;
        }

        $locationFilter = $request->input('location');
        $perPage = (int) $request->input('perPage', 100);

        $zones = TblZonesModel::orderBy('name')->get();
        $branchOptions = TblLocationModel::orderBy('name')->get();
        $stateOptions = [
            ['id' => 1, 'name' => 'Tamil Nadu'],
            ['id' => 2, 'name' => 'Karnataka'],
            ['id' => 3, 'name' => 'Kerala'],
            ['id' => 4, 'name' => 'International'],
            ['id' => 5, 'name' => 'Tirupati (branch)'],
        ];

        $summary = [
            'total_discount' => 0.0,
            'total_cancel' => 0.0,
            'total_refund' => 0.0,
        ];

        $dcrApprover = $this->incomeSummaryDcrApproverValue($request);
        $dcrStatus = strtolower(trim((string) $request->input('dcr_status', 'approved')));
        if (! in_array($dcrStatus, ['approved', 'pending', 'rejected'], true)) {
            $dcrStatus = 'approved';
        }

        $incomeData = collect();
        $grandTotal = 0.0;

        if ($dateFilter === 'today') {
            $apiData = $this->fetchTodayApiDataForDisplay($locationFilter);
            $incomeData = $this->incomeSummaryMergeRowsByLocationName(collect($apiData));
            $incomeData = $this->incomeSummaryOverlayFormDcrOnRows($incomeData, (string) $dateFilter, $startDate, $endDate, $dcrApprover);
            $grandTotal = (float) $incomeData->sum(function ($row) {
                return (float) ($row->cash ?? 0) + (float) ($row->card ?? 0) + (float) ($row->cheque ?? 0)
                    + (float) ($row->dd ?? 0) + (float) ($row->neft ?? 0) + (float) ($row->credit ?? 0) + (float) ($row->upi ?? 0);
            });
            foreach ($incomeData as $row) {
                $summary['total_discount'] += (float) ($row->discount ?? 0);
                $summary['total_cancel'] += (float) ($row->cancel_amt ?? 0);
                $summary['total_refund'] += (float) ($row->refund_amt ?? 0);
            }
            $summary['total_discount'] = round($summary['total_discount'], 2);
            $summary['total_cancel'] = round($summary['total_cancel'], 2);
            $summary['total_refund'] = round($summary['total_refund'], 2);
            session(['today_income_export_data' => $incomeData]);
        } else {
            $ap = $this->incomeSummarySqlApproved();
            $cx = $this->incomeSummarySqlCancel();
            $rf = $this->incomeSummarySqlRefund();

            $base = BillingListModel::query();
            $this->incomeSummaryApplyDateToQuery($base, $dateFilter, $startDate, $endDate);

            $variants = $this->incomeSummaryResolveLocationVariants($request);
            if ($variants !== null) {
                if ($variants === []) {
                    $incomeData = new \Illuminate\Pagination\LengthAwarePaginator(
                        collect(),
                        0,
                        max(1, $perPage),
                        max(1, (int) $request->input('page', 1)),
                        ['path' => $request->url(), 'query' => $request->query()]
                    );
                    if ($request->ajax()) {
                        return response()->json([
                            'html' => view('vendor.partials.table.income_table_rows', compact('incomeData', 'grandTotal', 'summary'))->render(),
                            'total' => 0,
                            'summary' => $summary,
                        ]);
                    }

                    return view('vendor.income_summary', compact(
                        'incomeData', 'grandTotal', 'summary', 'admin', 'dateFilter', 'startDate', 'endDate', 'locationFilter',
                        'zones', 'branchOptions', 'stateOptions', 'dcrStatus'
                    ));
                }
                $base->whereIn('billing_list.location_id', $variants);
            }

            $query = (clone $base)->select(
                DB::raw('MIN(billing_list.location_id) as location_id'),
                'billing_list.location_name',
                DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "Cash" THEN billing_list.amt ELSE 0 END) as cash'),
                DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "Card" THEN billing_list.amt ELSE 0 END) as card'),
                DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "Cheque" THEN billing_list.amt ELSE 0 END) as cheque'),
                DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "DD" THEN billing_list.amt ELSE 0 END) as dd'),
                DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "Neft" THEN billing_list.amt ELSE 0 END) as neft'),
                DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "Credit" THEN billing_list.amt ELSE 0 END) as credit'),
                DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "UPI" THEN billing_list.amt ELSE 0 END) as upi'),
                DB::raw('SUM(CASE WHEN ' . $ap . ' THEN COALESCE(billing_list.granddiscountvalue,0) ELSE 0 END) as discount'),
                DB::raw('SUM(CASE WHEN ' . $cx . ' THEN COALESCE(billing_list.amt,0) ELSE 0 END) as cancel_amt'),
                DB::raw('SUM(CASE WHEN ' . $rf . ' THEN COALESCE(billing_list.amt,0) ELSE 0 END) as refund_amt'),
                DB::raw('SUM(CASE WHEN ' . $ap . ' THEN billing_list.amt ELSE 0 END) as total')
            );

            $collection = $query->groupBy('billing_list.location_name')
                ->orderBy('billing_list.location_name')
                ->get();
            $collection = $this->incomeSummaryMergeRowsByLocationName($collection);
            $collection = $this->incomeSummaryOverlayFormDcrOnRows($collection, (string) $dateFilter, $startDate, $endDate, $dcrApprover);

            $summary['total_discount'] = round((float) $collection->sum(fn ($r) => (float) ($r->discount ?? 0)), 2);
            $summary['total_cancel'] = round((float) $collection->sum(fn ($r) => (float) ($r->cancel_amt ?? 0)), 2);
            $summary['total_refund'] = round((float) $collection->sum(fn ($r) => (float) ($r->refund_amt ?? 0)), 2);

            $grandTotal = (float) $collection->sum(function ($row) {
                return (float) $row->cash + (float) $row->card + (float) $row->cheque + (float) $row->dd
                    + (float) $row->neft + (float) $row->credit + (float) $row->upi;
            });

            $page = max(1, (int) $request->input('page', 1));
            $slice = $collection->slice(($page - 1) * $perPage, $perPage)->values();

            $incomeData = new \Illuminate\Pagination\LengthAwarePaginator(
                $slice,
                $collection->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('vendor.partials.table.income_table_rows', compact('incomeData', 'grandTotal', 'summary'))->render(),
                'total' => $grandTotal,
                'summary' => $summary,
            ]);
        }

        return view('vendor.income_summary', compact(
            'incomeData', 'grandTotal', 'summary', 'admin', 'dateFilter', 'startDate', 'endDate', 'locationFilter',
            'zones', 'branchOptions', 'stateOptions', 'dcrStatus'
        ));
    }

    private function fetchTodayApiDataForDisplay($locationFilter = null)
    {
        $locations = array("location1" => "Kerala - Palakkad", "location7" => "Erode", "location14" => "Tiruppur","location6" => "Kerala - Kozhikode", "location20" => "Coimbatore - Ganapathy", "location21" => "Hosur", "location22" => "Chennai - Sholinganallur","location23" => "Chennai - Urapakkam", "location24" => "Chennai - Madipakkam", "location26" => "Kanchipuram", "location27" => "Coimbatore - Sundarapuram", "location28" => "Trichy","location29" => "Thiruvallur", "location30" => "Pollachi", "location31" => "Bengaluru - Electronic City","location32" => "Bengaluru - Konanakunte", "location33" => "Chennai - Tambaram", "location34" => "Tanjore", "location36" => "Harur", "location39" => "Coimbatore - Thudiyalur", "location40" => "Madurai","location41" => "Bengaluru - Hebbal", "location42" => "Kallakurichi", "location43" => "Vellore","location44" => "Tirupati","location45" => "Aathur", "location46" => "Namakal", "location47" => "Bengaluru - Dasarahalli","location48" => "Chengalpattu", "location49" => "Chennai - Vadapalani", "location50" => "Pennagaram","location51" => "Thirupathur", "location52" => "Sivakasi", "location13" => "Salem", "location54" => "Nagapattinam", "location56" => "Krishnagiri", "location57" => "Karur");

        if ($locationFilter && isset($locations[$locationFilter])) {
            $locations = [$locationFilter => $locations[$locationFilter]];
        }

        $date = now()->format('Ymd');
        $data = [];

        foreach ($locations as $locId => $locName) {
            $response = $this->postCurlApi(
                'https://mocdoc.in/api/get/billlist/draravinds-ivf',
                $date,
                $locId
            );

            if (!empty($response['billinglist'])) {
                $summary = [
                    'location_id' => $locId,
                    'location_name' => $locName,
                    'cash' => 0,
                    'card' => 0,
                    'cheque' => 0,
                    'dd' => 0,
                    'neft' => 0,
                    'credit' => 0,
                    'upi' => 0,
                    'subtotal' => 0,
                    'total' => 0,
                    'discount' => 0,
                    'cancel_amt' => 0,
                    'refund_amt' => 0,
                ];

                foreach ($response['billinglist'] as $item) {
                    $amt = (float) ($item['amt'] ?? 0);
                    $bt = strtolower(trim((string) ($item['billtype'] ?? '')));
                    $ty = strtolower((string) ($item['type'] ?? ''));
                    $isCancel = $bt === 'cancelled' || $bt === 'cancel' || str_contains($ty, 'cancel');
                    $isRefund = $bt === 'refund' || $bt === 'refunded' || str_contains($ty, 'refund');
                    if ($isCancel) {
                        $summary['cancel_amt'] += $amt;

                        continue;
                    }
                    if ($isRefund) {
                        $summary['refund_amt'] += $amt;

                        continue;
                    }

                    $summary['discount'] += (float) ($item['granddiscountvalue'] ?? 0);

                    switch ($item['paymenttype'] ?? '') {
                        case 'Cash':
                            $summary['cash'] += $amt;
                            break;
                        case 'Card':
                            $summary['card'] += $amt;
                            break;
                        case 'Cheque':
                            $summary['cheque'] += $amt;
                            break;
                        case 'DD':
                            $summary['dd'] += $amt;
                            break;
                        case 'Neft':
                            $summary['neft'] += $amt;
                            break;
                        case 'Credit':
                            $summary['credit'] += $amt;
                            break;
                        case 'UPI':
                            $summary['upi'] += $amt;
                            break;
                    }
                    $summary['subtotal'] += $amt;
                    $summary['total'] += $amt;
                }

                $data[] = (object) $summary; // behaves like Eloquent collection
            }
        }

        return $data;
    }


    private function postCurlApi($url, $curr_date, $location_id, $max_retries = 3)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

          $post_fields = "date={$curr_date}&entitylocation={$location_id}";
          $head_fields = [
                  'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
                  'Date: Fri, 07 Mar 2025 10:07:52 GMT',
                  'Content-Type: application/x-www-form-urlencoded',
                  'Cookie: SRV=s1'
              ];

        $retry = 0;
        $backoff = 1;

        do {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post_fields,
                CURLOPT_HTTPHEADER => $head_fields,
                CURLOPT_FOLLOWLOCATION => true,      // FOLLOW REDIRECTS
                CURLOPT_MAXREDIRS => 10,             // max redirects
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($response === false) {
                $error = curl_error($curl);
                Log::error("CURL error for $location_id on $curr_date: $error");
                curl_close($curl);
                $retry++;
                sleep($backoff);
                $backoff *= 2;
                continue;
            }

            curl_close($curl);

            Log::info("API called for $location_id on $curr_date, HTTP code: $httpCode, Response: $response");

            if ($httpCode == 429) {
                Log::warning("HTTP 429 rate limit hit for $location_id on $curr_date. Retrying in $backoff sec.");
                sleep($backoff);
                $backoff *= 2;
                $retry++;
            } elseif ($httpCode != 200) {
                Log::warning("API returned HTTP $httpCode for $location_id on $curr_date. Response: $response");
                return null;
            } else {
                $decoded = json_decode($response, true);
                if ($decoded === null) {
                    Log::warning("Failed to decode JSON for $location_id on $curr_date. Raw response: $response");
                }
                return $decoded;
            }

        } while ($retry < $max_retries);

        Log::error("API request failed for $location_id on $curr_date after $max_retries retries.");
        return null;
    }
   public function exportIncomeSummary(Request $request)
    {
        $dateFilter = $request->input('date_filter');
        if ($dateFilter === null || $dateFilter === '') {
            $dateFilter = 'yesterday';
        }
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if ($dateFilter === 'yesterday' && ! $startDate && ! $endDate) {
            $y = Carbon::yesterday()->format('Y-m-d');
            $startDate = $y;
            $endDate = $y;
        }
        if ($dateFilter === 'custom' && (! $startDate || ! $endDate)) {
            $y = Carbon::yesterday()->format('Y-m-d');
            $startDate = $startDate ?: $y;
            $endDate = $endDate ?: $y;
        }
        $location = $request->input('location');
        $dcrApprover = $this->incomeSummaryDcrApproverValue($request);

        if ($dateFilter === 'today') {
            if (session()->has('today_income_export_data')) {
                $incomeData = collect(session('today_income_export_data'));
            } else {
                $incomeData = collect($this->fetchTodayApiDataForDisplay($location));
            }
            $incomeData = $this->incomeSummaryMergeRowsByLocationName($incomeData);
            $incomeData = $this->incomeSummaryOverlayFormDcrOnRows($incomeData, (string) $dateFilter, $startDate, $endDate, $dcrApprover);
        } else {
            $ap = $this->incomeSummarySqlApproved();
            $cx = $this->incomeSummarySqlCancel();
            $rf = $this->incomeSummarySqlRefund();

            $base = BillingListModel::query();
            $this->incomeSummaryApplyDateToQuery($base, $dateFilter, $startDate, $endDate);

            $variants = $this->incomeSummaryResolveLocationVariants($request);
            if ($variants !== null && $variants === []) {
                $incomeData = collect();
            } else {
                if ($variants !== null) {
                    $base->whereIn('billing_list.location_id', $variants);
                }
                $query = (clone $base)->select(
                    DB::raw('MIN(billing_list.location_id) as location_id'),
                    'billing_list.location_name',
                    DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "Cash" THEN billing_list.amt ELSE 0 END) as cash'),
                    DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "Card" THEN billing_list.amt ELSE 0 END) as card'),
                    DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "Cheque" THEN billing_list.amt ELSE 0 END) as cheque'),
                    DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "DD" THEN billing_list.amt ELSE 0 END) as dd'),
                    DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "Neft" THEN billing_list.amt ELSE 0 END) as neft'),
                    DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "Credit" THEN billing_list.amt ELSE 0 END) as credit'),
                    DB::raw('SUM(CASE WHEN ' . $ap . ' AND billing_list.paymenttype = "UPI" THEN billing_list.amt ELSE 0 END) as upi'),
                    DB::raw('SUM(CASE WHEN ' . $ap . ' THEN COALESCE(billing_list.granddiscountvalue,0) ELSE 0 END) as discount'),
                    DB::raw('SUM(CASE WHEN ' . $cx . ' THEN COALESCE(billing_list.amt,0) ELSE 0 END) as cancel_amt'),
                    DB::raw('SUM(CASE WHEN ' . $rf . ' THEN COALESCE(billing_list.amt,0) ELSE 0 END) as refund_amt'),
                    DB::raw('SUM(CASE WHEN ' . $ap . ' THEN billing_list.amt ELSE 0 END) as total')
                );
                $incomeData = $this->incomeSummaryMergeRowsByLocationName(collect(
                    $query->groupBy('billing_list.location_name')
                        ->orderBy('billing_list.location_name')
                        ->get()
                ));
                $incomeData = $this->incomeSummaryOverlayFormDcrOnRows($incomeData, (string) $dateFilter, $startDate, $endDate, $dcrApprover);
            }
        }
        $fileName = 'Vendor_Income_' . now()->format('d_m_Y_His');

        if ($request->export_type == 'csv') {
            return Excel::download(new VendorIncomeExport($incomeData), $fileName . '.csv');
        }

        return Excel::download(new VendorIncomeExport($incomeData), $fileName . '.xlsx');
    }

    /**
     * Drill-down AJAX: individual billing records for a location (+ optional payment type).
     * Returns JSON { html, summary, grandTotal }
     */
    public function vendorIncomeDrilldown(Request $request)
    {
        $paymentType = $request->input('payment_type');
        if ($paymentType === null || $paymentType === '') {
            return response()->json($this->buildIncomeBranchMatrixPayload($request));
        }

        if (in_array($paymentType, ['Discount', 'Cancel', 'Refund'], true)) {
            return response()->json($this->buildIncomeDcrDrilldownPayload($request, $paymentType));
        }

        $locationId   = $request->input('location_id');
        $locationName = trim((string) $request->input('location_name', ''));
        $dateFilter  = $request->input('date_filter', 'yesterday');
        $startDate   = $request->input('start_date');
        $endDate     = $request->input('end_date');
        $page        = max(1, (int) $request->input('page', 1));
        $perPage     = 50;

        $ap = $this->incomeSummarySqlApproved();

        $query = BillingListModel::query()
            ->select(
                'billing_list.location_name',
                'billing_list.billdate',
                'billing_list.billno',
                'billing_list.phid',
                'billing_list.patientname',
                'billing_list.consultant',
                'billing_list.amt',
                'billing_list.grandtotal',
                'billing_list.granddiscountvalue',
                'billing_list.billtype',
                'billing_list.type',
                'billing_list.paymenttype',
                'billing_list.user_name'
            );

        // Date filter
        $this->incomeSummaryApplyDateToQuery($query, $dateFilter, $startDate, $endDate);

        // Location filter — prefer exact branch name (covers duplicate location_id formats in billing_list)
        if ($locationName !== '') {
            $query->whereRaw('TRIM(billing_list.location_name) = ?', [$locationName]);
        } elseif ($locationId) {
            $query->where(function ($q) use ($locationId) {
                $q->where('billing_list.location_id', $locationId)
                  ->orWhere('billing_list.location_name', $locationId);
            });
        }

        // Payment type filter (UPI = literal paymenttype UPI only — not type=Advance with Cash/Card/etc.)
        if ($paymentType) {
            $query->where('billing_list.paymenttype', $paymentType);
        }

        // Only approved rows for payment-type drill-down; for branch total show all
        if ($paymentType) {
            $query->whereRaw($ap);
        }

        $total   = (clone $query)->count();
        $records = $query->orderByRaw("STR_TO_DATE(billing_list.billdate, '%Y%m%d%H:%i:%s') DESC")
                         ->offset(($page - 1) * $perPage)
                         ->limit($perPage)
                         ->get();

        $typeGroups = [];

        // Format date helper
        $formatDate = function ($raw) {
            if (!$raw) return '—';
            try {
                return \Carbon\Carbon::createFromFormat('Ymd', substr($raw, 0, 8))->format('d/m/Y');
            } catch (\Throwable $e) {
                return substr($raw, 0, 8);
            }
        };

        $grandTotal  = $records->sum(fn ($r) => (float) $r->amt);
        $lastPage    = (int) ceil($total / $perPage);

        $rows = $records->map(function ($r, $idx) use ($formatDate, $page, $perPage) {
            return [
                'sno'         => ($page - 1) * $perPage + $idx + 1,
                'location'    => $r->location_name,
                'date'        => $formatDate($r->billdate),
                'bill_no'     => $r->billno ?: '—',
                'phid'        => $r->phid ?: '—',
                'patient'     => $r->patientname ?: '—',
                'consultant'  => $r->consultant ?: '—',
                'amount'      => number_format((float) $r->amt, 2),
                'grand_total' => number_format((float) $r->grandtotal, 2),
                'discount'    => number_format((float) $r->granddiscountvalue, 2),
                'bill_type'   => $r->billtype ?: ($r->type ?: '—'),
                'pay_type'    => $r->paymenttype ?: '—',
                'user'        => $r->user_name ?: '—',
            ];
        });

        return response()->json([
            'view'        => 'list',
            'drill_kind'  => null,
            'rows'        => $rows,
            'total'       => $total,
            'page'        => $page,
            'last_page'   => $lastPage,
            'grand_total' => round($grandTotal, 2),
            'type_groups' => $typeGroups,
            'per_page'    => $perPage,
        ]);
    }

    // public function exportBills(Request $request)
    // {
    //     $fileName = 'Bills_' . now()->format('Y_m_d_His') . '.xlsx';
    //     return Excel::download(new BillsExport($request), $fileName);
    // }

    public function exportBills(Request $request)
    {
        // 1️⃣ Get format from AJAX
        $format = $request->get('format', 'xlsx'); // csv | xlsx

        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'xlsx';
        }

        // 2️⃣ Writer type
        $writerType = $format === 'csv'
            ? ExcelExcel::CSV
            : ExcelExcel::XLSX;

        // 3️⃣ File name (JS will also set it, this is fallback)
        $fileName = 'Bills_' . now()->format('Y_m_d_His') . '.' . $format;

        // 4️⃣ Download
        return Excel::download(
            new BillsExport($request, $format),
            $fileName,
            $writerType,
            [
                'Content-Type' =>
                    $format === 'csv'
                        ? 'text/csv'
                        : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        );
    }

    /**
     * Export bills with TDS section data (all entered data + TDS columns). XLSX and CSV.
     */
    public function exportBillsTds(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'xlsx';
        }
        $writerType = $format === 'csv' ? ExcelExcel::CSV : ExcelExcel::XLSX;
        $fileName  = 'Bills_TDS_' . now()->format('Y_m_d_His') . '.' . $format;
        return Excel::download(
            new \App\Exports\BillsExportTds($request, $format),
            $fileName,
            $writerType,
            [
                'Content-Type' => $format === 'csv'
                    ? 'text/csv'
                    : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    /**
     * Export bills in Original TDS Format (matches TDS_Format.xlsx template). XLSX and CSV.
     */
    public function exportBillsTdsOriginal(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'xlsx';
        }
        $writerType = $format === 'csv' ? ExcelExcel::CSV : ExcelExcel::XLSX;
        $fileName   = 'Bills_TDS_Original_' . now()->format('Y_m_d_His') . '.' . $format;
        return Excel::download(
            new \App\Exports\NewTDSFormate($request, $format),
            $fileName,
            $writerType,
            [
                'Content-Type' => $format === 'csv'
                    ? 'text/csv'
                    : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    /**
     * Export bills with GST data (all entered data + CGST, SGST, GST %, etc.). XLSX and CSV.
     */
    public function exportBillsGst(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'xlsx';
        }
        $writerType = $format === 'csv' ? ExcelExcel::CSV : ExcelExcel::XLSX;
        $fileName  = 'Bills_GST_' . now()->format('Y_m_d_His') . '.' . $format;
        return Excel::download(
            new \App\Exports\BillsExportGst($request, $format),
            $fileName,
            $writerType,
            [
                'Content-Type' => $format === 'csv'
                    ? 'text/csv'
                    : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    /**
     * Export NEFT in same format as exportUsers (bank NEFT format).
     * Uses bill dashboard filters (date, zone, branch, company, vendor, status, search, bill_ids).
     */
    // public function exportBillsNeft(Request $request)
    // {
    //     ini_set('memory_limit', '1024M');

    //     $format = $request->input('format', 'xlsx');
    //     if (!in_array($format, ['csv', 'xlsx'])) {
    //         $format = 'xlsx';
    //     }

    //     $r = $request;

    //     // Same bill filters as bill dashboard / BillsExport → get bill IDs
    //     $billQuery = Tblbill::where('delete_status', 0)->orderBy('id', 'desc');

    //     if ($r->filled('date_from') && $r->filled('date_to')) {
    //         try {
    //             $from = Carbon::createFromFormat('d/m/Y', trim($r->date_from))->startOfDay();
    //             $to   = Carbon::createFromFormat('d/m/Y', trim($r->date_to))->endOfDay();
    //             $billQuery->whereRaw("STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ?", [$from, $to]);
    //         } catch (\Exception $e) { }
    //     }
    //     if ($r->filled('state_name')) {
    //         $state_name = $r->state_name;
    //         if ($state_name === 'Tamil Nadu') {
    //             $billQuery->whereIn('zone_id', ['2','4','6','7','8','9']);
    //         } elseif ($state_name === 'Karnataka') {
    //             $billQuery->whereIn('zone_id', ['3']);
    //         } elseif ($state_name === 'Kerala') {
    //             $billQuery->whereIn('zone_id', ['5']);
    //         } elseif ($state_name === 'International') {
    //             $billQuery->whereIn('zone_id', ['10']);
    //         } elseif ($state_name === 'Andra Pradesh') {
    //             $billQuery->whereIn('branch_id', ['30']);
    //         }
    //     }
    //     if ($r->filled('zone_id')) {
    //         $billQuery->whereIn('zone_id', array_filter(explode(',', $r->zone_id)));
    //     }
    //     if ($r->filled('branch_id')) {
    //         $billQuery->whereIn('branch_id', array_filter(explode(',', $r->branch_id)));
    //     }
    //     if ($r->filled('company_id')) {
    //         $billQuery->whereIn('company_id', array_filter(explode(',', $r->company_id)));
    //     }
    //     if ($r->filled('vendor_id')) {
    //         $billQuery->whereIn('vendor_id', array_filter(explode(',', $r->vendor_id)));
    //     }
    //     if ($r->filled('status_name')) {
    //         $statuses = array_map('trim', explode(',', $r->status_name));
    //         $billQuery->where(function ($q) use ($statuses) {
    //             foreach ($statuses as $status) {
    //                 $q->orWhere('status', 'LIKE', '%' . $status . '%')
    //                   ->orWhere('bill_status', 'LIKE', '%' . $status . '%');
    //             }
    //         });
    //     }
    //     if ($r->filled('universal_search')) {
    //         $search = $r->universal_search;
    //         $billQuery->where(function ($q) use ($search) {
    //             $q->where('vendor_name', 'like', "%{$search}%")
    //                 ->orWhere('zone_name', 'like', "%{$search}%")
    //                 ->orWhere('branch_name', 'like', "%{$search}%")
    //                 ->orWhere('company_name', 'like', "%{$search}%")
    //                 ->orWhere('bill_gen_number', 'like', "%{$search}%")
    //                 ->orWhere('bill_number', 'like', "%{$search}%")
    //                 ->orWhere('order_number', 'like', "%{$search}%")
    //                 ->orWhere('bill_date', 'like', "%{$search}%")
    //                 ->orWhere('sub_total_amount', 'like', "%{$search}%")
    //                 ->orWhere('tax_type', 'like', "%{$search}%")
    //                 ->orWhere('grand_total_amount', 'like', "%{$search}%")
    //                 ->orWhere('due_date', 'like', "%{$search}%");
    //         });
    //     }
    //     if ($r->filled('bill_ids') && is_string($r->bill_ids)) {
    //         $ids = array_filter(explode(',', $r->bill_ids));
    //         if (!empty($ids)) {
    //             $billQuery->whereIn('id', $ids);
    //         }
    //     }

    //     // Bill data only — 24 columns (A–X) exactly as per NEFT template image.
    //     $bills = $billQuery->with(['BillLines', 'Tblvendor', 'Tblvendor.bankdetails', 'Tblbankdetails'])->get();

    //     $safeVal = function ($v) {
    //         return ($v === null || $v === '' || (is_string($v) && trim($v) === '')) ? 'NA' : (string) $v;
    //     };
    //     $emptyVal = function ($v) {
    //         return ($v === null || (is_string($v) && trim((string)$v) === '')) ? '' : (string) $v;
    //     };

    //     $pymtDate = now()->format('d-m-Y');
    //     $periodMonths = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
    //     $currentYearShort = now()->format('y');

    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     // 24 columns exactly as per NEFT template image (A–X)
    //     $headers = [
    //         'PYMT_PROD_TYPE_CODE', 'PYMT_MODE', 'DEBIT_ACC_NO', 'BNF_NAME',
    //         'BENE_ACC_NO', 'BENE_IFSC', 'AMOUNT', 'DEBIT_NARR', 'CREDIT_NARR',
    //         'MOBILE_NUM', 'EMAIL_ID', 'REMARK',
    //         'PAN NO', 'ACCOUNT NO', 'IFSC CODE', 'AMOUNT', 'ALREADY PAID',
    //         'PYMT_DATE', 'REF_NO',
    //         'ADDL_INFO1', 'ADDL_INFO2', 'ADDL_INFO3', 'ADDL_INFO4', 'ADDL_INFO5'
    //     ];
    //     $col = 'A';
    //     foreach ($headers as $header) {
    //         $sheet->setCellValue($col . '1', $header);
    //         $col++;
    //     }

    //     $row = 2;
    //     foreach ($bills as $bill) {
    //         $bankDetail = $bill->Tblbankdetails ?? optional(optional($bill->Tblvendor)->bankdetails)->first();
    //         $vendor = $bill->Tblvendor;

    //         // BNF_NAME: uppercase, replace dots, strip non-AZ0-9 and spaces, collapse spaces (like JS)
    //         $bnfName = $bill->vendor_name ?: optional($vendor)->display_name;
    //         $bnfName = strtoupper(preg_replace('/\s+/', ' ', preg_replace('/[^A-Z0-9\s]/', ' ', str_replace('.', ' ', (string)$bnfName))));
    //         $bnfName = trim($bnfName) === '' ? 'NA' : trim($bnfName);

    //         $beneAccNo = $emptyVal(optional($bankDetail)->accont_number ?? '');

    //         $rawIfsc = trim((string) (optional($bankDetail)->ifsc_code ?? ''));
    //         $ifscValid = preg_match('/^[A-Z0-9]{11}$/i', $rawIfsc) === 1;
    //         $isIcici = strtoupper(substr($rawIfsc, 0, 4)) === 'ICIC';
    //         $pymtMode = $isIcici ? 'FT' : 'NEFT';
    //         $beneIfsc = $isIcici ? '' : ($ifscValid ? $rawIfsc : '');

    //         $mobile = preg_replace('/[^0-9]/', '', (string) (optional($vendor)->mobile ?? ''));
    //         if (strlen($mobile) > 10) {
    //             $mobile = substr($mobile, -10);
    //         }
    //         $mobile = $mobile !== '' ? $mobile : 'NA';

    //         $rawEmail = trim((string) (optional($vendor)->email ?? ''));
    //         $emailId = ($rawEmail !== '' && $rawEmail !== '-' && strpos($rawEmail, '@') !== false)
    //             ? $rawEmail
    //             : 'payment@gmail.com';
    //         $emailId = $safeVal($emailId);

    //         // REMARK: zone short + branch city + bill month + year + BILL (like JS zone+branch+month+year+SALARY)
    //         $zoneRaw = strtoupper(trim($bill->zone_name ?? ''));
    //         if (strpos($zoneRaw, 'KERALA') !== false) {
    //             $zoneShort = 'KL';
    //         } elseif (strpos($zoneRaw, 'KARNATAKA') !== false) {
    //             $zoneShort = 'KA';
    //         } elseif (strpos($zoneRaw, 'TAMIL') !== false || strpos($zoneRaw, 'TN') !== false) {
    //             $zoneShort = 'TN';
    //         } elseif (strpos($zoneRaw, 'ANDHRA') !== false || strpos($zoneRaw, 'AP') !== false) {
    //             $zoneShort = 'AP';
    //         } elseif (strpos($zoneRaw, 'TELANGANA') !== false) {
    //             $zoneShort = 'TS';
    //         } elseif (strpos($zoneRaw, 'MAHARASHTRA') !== false) {
    //             $zoneShort = 'MH';
    //         } elseif (strpos($zoneRaw, 'WEST') !== false) {
    //             $zoneShort = 'WES';
    //         } else {
    //             $zoneShort = substr($zoneRaw, 0, 2);
    //         }
    //         $branchParts = explode('-', $bill->branch_name ?? '');
    //         $branchCity = trim(strtoupper(preg_replace('/[^A-Z0-9\s]/', ' ', str_replace('.', ' ', $branchParts[count($branchParts) - 1] ?? $branchParts[0] ?? ''))));
    //         $fromMonthStr = '';
    //         try {
    //             if (!empty($bill->bill_date)) {
    //                 $d = \Carbon\Carbon::createFromFormat('d/m/Y', $bill->bill_date);
    //                 $fromMonthStr = $periodMonths[$d->month - 1];
    //             }
    //         } catch (\Exception $e) { }
    //         if ($fromMonthStr === '') {
    //             $fromMonthStr = $periodMonths[now()->month - 1];
    //         }
    //         $toMonthStr = $periodMonths[now()->month - 1];
    //         $remarkParts = array_filter([$zoneShort, $branchCity, $fromMonthStr, $toMonthStr, $currentYearShort, 'BILL']);
    //         $remarkBase = implode(' ', $remarkParts);
    //         $billNote = trim((string) ($bill->note ?? ''));
    //         $remark = $safeVal($billNote !== '' ? $remarkBase . ' ' . $billNote : $remarkBase);

    //         $lines = $bill->BillLines;
    //         if ($lines->isEmpty()) {
    //             $lines = collect([(object)['amount' => $bill->grand_total_amount ?? 0]]);
    //         }
    //         foreach ($lines as $line) {
    //             $lineAmount = (int) round((float) ($line->amount ?? $bill->grand_total_amount ?? 0));
    //             if ($lineAmount <= 0) {
    //                 continue;
    //             }

    //             $panNo = $safeVal(optional($vendor)->pan_number ?? '');
    //             $alreadyPaid = $bill->partially_payment ?? '';
    //             if ($alreadyPaid !== '' && $alreadyPaid !== null) {
    //                 $alreadyPaid = (int) round((float) $alreadyPaid);
    //             }
    //             $secondAmount = (int) round((float) ($bill->grand_total_amount ?? $lineAmount));

    //             $sheet->setCellValue('A' . $row, 'PAB_VENDOR');
    //             $sheet->setCellValue('B' . $row, $pymtMode);
    //             $sheet->setCellValueExplicit('C' . $row, '777706777724', DataType::TYPE_STRING);
    //             $sheet->setCellValue('D' . $row, $bnfName);
    //             $sheet->setCellValueExplicit('E' . $row, $beneAccNo, DataType::TYPE_STRING);
    //             $sheet->setCellValue('F' . $row, $beneIfsc);
    //             $sheet->setCellValue('G' . $row, $lineAmount);
    //             $sheet->setCellValue('H' . $row, 'NA');
    //             $sheet->setCellValue('I' . $row, 'NA');
    //             $sheet->setCellValue('J' . $row, $mobile);
    //             $sheet->setCellValue('K' . $row, $emailId);
    //             $sheet->setCellValue('L' . $row, $remark);
    //             $sheet->setCellValue('M' . $row, $panNo);
    //             $sheet->setCellValueExplicit('N' . $row, $beneAccNo, DataType::TYPE_STRING);
    //             $sheet->setCellValue('O' . $row, $beneIfsc);
    //             $sheet->setCellValue('P' . $row, $secondAmount);
    //             $sheet->setCellValue('Q' . $row, $alreadyPaid);
    //             $sheet->setCellValue('R' . $row, $pymtDate);
    //             $sheet->setCellValue('S' . $row, 'NA');
    //             $sheet->setCellValue('T' . $row, 'NA');
    //             $sheet->setCellValue('U' . $row, 'NA');
    //             $sheet->setCellValue('V' . $row, 'NA');
    //             $sheet->setCellValue('W' . $row, 'NA');
    //             $sheet->setCellValue('X' . $row, 'NA');
    //             $row++;
    //         }
    //     }

    //     $filename = 'NEFT_export.' . $format;
    //     $tempFile = tempnam(sys_get_temp_dir(), $filename);

    //     if ($format === 'csv') {
    //         $writer = new Csv($spreadsheet);
    //         $writer->setDelimiter(',');
    //         $writer->setEnclosure('"');
    //         $writer->setLineEnding("\r\n");
    //         $writer->setSheetIndex(0);
    //     } else {
    //         $writer = new Xlsx($spreadsheet);
    //     }
    //     $writer->save($tempFile);

    //     return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    // }
    public function exportBillsNeft(Request $request)
    {
        ini_set('memory_limit', '1024M');

        $format = $request->input('format', 'xlsx');
        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'xlsx';
        }

        $r = $request;

        // ── Bill query (same filters as bill dashboard) ─────────────────────────
        $billQuery = Tblbill::where('delete_status', 0)->orderBy('id', 'desc');

        if ($r->filled('date_from') && $r->filled('date_to')) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', trim($r->date_from))->startOfDay();
                $to   = Carbon::createFromFormat('d/m/Y', trim($r->date_to))->endOfDay();
                $billQuery->whereRaw("STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ?", [$from, $to]);
            } catch (\Exception $e) {}
        }
        if ($r->filled('state_name')) {
            $state_name = $r->state_name;
            if ($state_name === 'Tamil Nadu') {
                $billQuery->whereIn('zone_id', ['2','4','6','7','8','9']);
            } elseif ($state_name === 'Karnataka') {
                $billQuery->whereIn('zone_id', ['3']);
            } elseif ($state_name === 'Kerala') {
                $billQuery->whereIn('zone_id', ['5']);
            } elseif ($state_name === 'International') {
                $billQuery->whereIn('zone_id', ['10']);
            } elseif ($state_name === 'Andra Pradesh') {
                $billQuery->whereIn('branch_id', ['30']);
            }
        }
        if ($r->filled('zone_id')) {
            $billQuery->whereIn('zone_id', array_filter(explode(',', $r->zone_id)));
        }
        if ($r->filled('branch_id')) {
            $billQuery->whereIn('branch_id', array_filter(explode(',', $r->branch_id)));
        }
        if ($r->filled('company_id')) {
            $billQuery->whereIn('company_id', array_filter(explode(',', $r->company_id)));
        }
        if ($r->filled('vendor_id')) {
            $billQuery->whereIn('vendor_id', array_filter(explode(',', $r->vendor_id)));
        }
        if ($r->filled('status_name')) {
            $statuses = array_map('trim', explode(',', $r->status_name));
            $billQuery->where(function ($q) use ($statuses) {
                foreach ($statuses as $status) {
                    $q->orWhere('status',      'LIKE', '%' . $status . '%')
                      ->orWhere('bill_status', 'LIKE', '%' . $status . '%');
                }
            });
        }
        if ($r->filled('universal_search')) {
            $search = $r->universal_search;
            $billQuery->where(function ($q) use ($search) {
                $q->where('vendor_name',        'like', "%{$search}%")
                  ->orWhere('zone_name',         'like', "%{$search}%")
                  ->orWhere('branch_name',       'like', "%{$search}%")
                  ->orWhere('company_name',      'like', "%{$search}%")
                  ->orWhere('bill_gen_number',   'like', "%{$search}%")
                  ->orWhere('bill_number',       'like', "%{$search}%")
                  ->orWhere('order_number',      'like', "%{$search}%")
                  ->orWhere('bill_date',         'like', "%{$search}%")
                  ->orWhere('sub_total_amount',  'like', "%{$search}%")
                  ->orWhere('tax_type',          'like', "%{$search}%")
                  ->orWhere('grand_total_amount','like', "%{$search}%")
                  ->orWhere('due_date',          'like', "%{$search}%");
            });
        }
        if ($r->filled('bill_ids') && is_string($r->bill_ids)) {
            $ids = array_filter(explode(',', $r->bill_ids));
            if (!empty($ids)) {
                $billQuery->whereIn('id', $ids);
            }
        }

        $bills = $billQuery->with(['BillLines', 'Tblvendor', 'Tblvendor.bankdetails', 'Tblbankdetails'])->get();

        // ── Helpers matching JS safeVal / emptyVal exactly ──────────────────────
        // safeVal: returns 'NA' for null / '' (used for name, mobile, email, remark, date, ref, addl)
        $safeVal = function ($v): string {
            return ($v === null || $v === '' || (is_string($v) && trim($v) === '')) ? 'NA' : (string) $v;
        };
        // emptyVal: returns '' for null/missing (used for acc no, IFSC, narr fields — never 'NA')
        $emptyVal = function ($v): string {
            return ($v === null || (is_string($v) && trim((string)$v) === '')) ? '' : (string) $v;
        };

        // ── Date / period helpers (JS-identical) ────────────────────────────────
        $today            = now();
        $pymtDate         = $today->format('d-m-Y');
        $periodMonths     = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
        $toMonthStr       = $periodMonths[$today->month - 1];
        $currentYearShort = $today->format('y');


        // ── Headers — 19 columns (A–S) matching JS exactly ─────────────────────
        $headers = [
            'PYMT_PROD_TYPE_CODE', 'PYMT_MODE', 'DEBIT_ACC_NO', 'BNF_NAME',
            'BENE_ACC_NO', 'BENE_IFSC', 'AMOUNT', 'DEBIT_NARR', 'CREDIT_NARR',
            'MOBILE_NUM', 'EMAIL_ID', 'REMARK', 'PYMT_DATE', 'REF_NO',
            'ADDL_INFO1', 'ADDL_INFO2', 'ADDL_INFO3', 'ADDL_INFO4', 'ADDL_INFO5',
        ];

        // ── Collect ALL rows into a plain PHP array first ────────────────────────
        // This single array is used for both CSV (fputcsv) and XLSX (PhpSpreadsheet).
        // Every value is a plain string — no numeric types — so neither Excel nor a
        // CSV reader can auto-convert account numbers / mobile to scientific notation.
        $allRows   = [];
        $allRows[] = $headers;   // row 0 = header

        foreach ($bills as $bill) {
            $bankDetail = $bill->Tblbankdetails
                ?? optional(optional($bill->Tblvendor)->bankdetails)->first();
            $vendor = $bill->Tblvendor;

            // BNF_NAME
            $rawName = $bill->vendor_name ?: optional($vendor)->display_name ?? '';
            $bnfName = strtoupper(
                preg_replace('/\s+/', ' ',
                    preg_replace('/[^A-Z0-9\s]/', ' ',
                        str_replace('.', ' ', (string) $rawName)
                    )
                )
            );
            $bnfName = $safeVal(trim($bnfName));

            // Account number — plain string, no apostrophe
            $beneAccNo = $emptyVal(optional($bankDetail)->accont_number ?? '');

            // IFSC / payment mode
            $rawIfsc   = trim((string) (optional($bankDetail)->ifsc_code ?? ''));
            $ifscValid = (bool) preg_match('/^[A-Z0-9]{11}$/i', $rawIfsc);
            $isIcici   = strtoupper(substr($rawIfsc, 0, 4)) === 'ICIC';
            $pymtMode  = $isIcici ? 'FT'   : 'NEFT';
            $beneIfsc  = $isIcici ? ''     : ($ifscValid ? $rawIfsc : '');

            // Mobile
            $mobile = preg_replace('/[^0-9]/', '', (string) (optional($vendor)->mobile ?? ''));
            if (strlen($mobile) > 10) $mobile = substr($mobile, -10);
            $mobile = $safeVal($mobile !== '' ? $mobile : '');

            // Email
            $rawEmail = trim((string) (optional($vendor)->email ?? ''));
            $emailId  = ($rawEmail !== '' && $rawEmail !== '-' && strpos($rawEmail, '@') !== false)
                ? $rawEmail : 'payment@gmail.com';
            $emailId  = $safeVal($emailId);

            // REMARK
            $zoneRaw = strtoupper(trim((string) ($bill->zone_name ?? '')));
            if      (str_contains($zoneRaw, 'KERALA'))                                  $zoneShort = 'KL';
            elseif  (str_contains($zoneRaw, 'KARNATAKA'))                               $zoneShort = 'KA';
            elseif  (str_contains($zoneRaw, 'TAMIL') || str_contains($zoneRaw, 'TN'))   $zoneShort = 'TN';
            elseif  (str_contains($zoneRaw, 'ANDHRA') || str_contains($zoneRaw, 'AP'))  $zoneShort = 'AP';
            elseif  (str_contains($zoneRaw, 'TELANGANA'))                               $zoneShort = 'TS';
            elseif  (str_contains($zoneRaw, 'MAHARASHTRA'))                             $zoneShort = 'MH';
            elseif  (str_contains($zoneRaw, 'WEST'))                                    $zoneShort = 'WES';
            else                                                                         $zoneShort = substr($zoneRaw, 0, 2);

            $branchParts = explode('-', (string) ($bill->branch_name ?? ''));
            $branchCity  = strtoupper(trim(
                preg_replace('/\s+/', ' ',
                    preg_replace('/[^A-Z0-9\s]/', ' ',
                        str_replace('.', ' ', $branchParts[count($branchParts) - 1] ?? $branchParts[0] ?? '')
                    )
                )
            ));

            $fromMonthStr = '';
            try {
                if (!empty($bill->bill_date)) {
                    $bd = Carbon::createFromFormat('d/m/Y', $bill->bill_date);
                    $fromMonthStr = $periodMonths[$bd->month - 1];
                }
            } catch (\Exception $e) {}
            if ($fromMonthStr === '') {
                $prevMonth    = $today->month - 2;
                $prevMonth    = $prevMonth < 0 ? 11 : $prevMonth;
                $fromMonthStr = $periodMonths[$prevMonth];
            }

            $remarkParts = array_filter(
                [$zoneShort, $branchCity, $fromMonthStr, $toMonthStr, $currentYearShort, 'BILL'],
                fn($p) => $p !== null && trim($p) !== ''
            );
            $billNote   = trim((string) ($bill->note ?? ''));
            $remarkBase = implode(' ', $remarkParts);
            $remark     = $safeVal($billNote !== '' ? $remarkBase . ' ' . $billNote : $remarkBase);

            // Lines loop
            $lines = $bill->BillLines;
            if ($lines->isEmpty()) {
                $lines = collect([(object) ['amount' => $bill->grand_total_amount ?? 0]]);
            }

            foreach ($lines as $line) {
                $lineAmount = (int) round((float) ($line->amount ?? $bill->grand_total_amount ?? 0));
                if ($lineAmount <= 0) continue;

                // Every value is cast to string — consistent between XLSX and CSV
                $allRows[] = [
                    'PAB_VENDOR',           // A  PYMT_PROD_TYPE_CODE
                    $pymtMode,              // B  PYMT_MODE
                    '777705777724',         // C  DEBIT_ACC_NO  — string, never scientific notation
                    $bnfName,               // D  BNF_NAME
                    $beneAccNo,             // E  BENE_ACC_NO
                    $beneIfsc,              // F  BENE_IFSC
                    (string) $lineAmount,   // G  AMOUNT
                    'NA',                   // H  DEBIT_NARR
                    'NA',                   // I  CREDIT_NARR
                    $mobile,                // J  MOBILE_NUM
                    $emailId,               // K  EMAIL_ID
                    $remark,                // L  REMARK
                    $safeVal($pymtDate),    // M  PYMT_DATE  — dd-mm-yyyy plain string
                    'NA',                   // N  REF_NO
                    'NA',                   // O  ADDL_INFO1
                    'NA',                   // P  ADDL_INFO2
                    'NA',                   // Q  ADDL_INFO3
                    'NA',                   // R  ADDL_INFO4
                    'NA',                   // S  ADDL_INFO5
                ];
            }
        }

        $filename = 'NEFT_export.' . $format;

        // ════════════════════════════════════════════════════════════════════════
        // CSV — manually build the file so every value is wrapped in ="..." syntax.
        // This is the ONLY way to stop Excel auto-converting long numbers
        // (account numbers, mobile, debit acc) to scientific notation when the
        // user opens the CSV — fputcsv alone cannot prevent this.
        //
        // Format per cell:  ="777705777724"
        // Excel sees the =  and treats it as a formula that returns a text string,
        // so the value is stored as text and never converted.
        // ════════════════════════════════════════════════════════════════════════
        if ($format === 'csv') {
            $tempFile = tempnam(sys_get_temp_dir(), 'neft_csv_');
            $fh = fopen($tempFile, 'w');

            // UTF-8 BOM — prevents garbled characters when Excel opens the file
            fwrite($fh, "\xEF\xBB\xBF");

            foreach ($allRows as $rowIndex => $row) {
                $csvCells = [];
                foreach ($row as $value) {
                    $value = (string) $value;
                    if ($rowIndex === 0) {
                        // Header row — plain quoted string, no = prefix needed
                        $csvCells[] = '"' . str_replace('"', '""', $value) . '"';
                    } else {
                        // Data rows — ="value" forces Excel to treat as text
                        // Escape any double-quotes inside the value
                        $escaped     = str_replace('"', '""', $value);
                        $csvCells[]  = '="' . $escaped . '"';
                    }
                }
                fwrite($fh, implode(',', $csvCells) . "\r\n");
            }
            fclose($fh);

            return response()->download($tempFile, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ])->deleteFileAfterSend(true);
        }

        // ════════════════════════════════════════════════════════════════════════
        // XLSX — build spreadsheet from $allRows; all cells explicit text strings.
        // ════════════════════════════════════════════════════════════════════════
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        foreach ($allRows as $rowIdx => $row) {
            foreach ($row as $colIdx => $value) {
                $sheet->setCellValueExplicitByColumnAndRow(
                    $colIdx + 1,
                    $rowIdx + 1,
                    (string) $value,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
                $sheet->getStyleByColumnAndRow($colIdx + 1, $rowIdx + 1)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }
        }

        // Column widths (A–S)
        $colWidths = [22, 10, 18, 30, 22, 15, 14, 12, 12, 16, 32, 36, 16, 10, 10, 10, 10, 10, 10];
        foreach ($colWidths as $colIdx => $width) {
            $sheet->getColumnDimensionByColumn($colIdx + 1)->setWidth($width);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'neft_xlsx_');
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
public function getaddressdashboard(Request $request)
{
    $admin = auth()->user();
    $limit_access = $admin->access_limits;
    $locations = TblLocationModel::all();
    $perPage = $request->get('per_page', 10);

    $address = TblDeliveryAddress::with('user') // Eager load the user relationship
    ->orderBy('id', 'desc')
    ->paginate($perPage)
    ->appends(['per_page' => $perPage]);
    // dd($address);
    // $vendor = Tblvendor::with(['billingAddress', 'shippingAddress', 'contacts', 'bankdetails'])->orderBy('id', 'desc')->paginate(10);
    return view('vendor.address', [
        'admin' => $admin,
        'locations' => $locations,
        'address' => $address,
        'perPage' => $perPage,
    ]);
}
public function checkBillNumber(Request $request)
{
    $bill_number = $request->bill_number;
    $vendor_id = $request->vendor_id;

    $exists = Tblbill::where('bill_number', $bill_number)
                ->where('vendor_id', $vendor_id)
                ->where('delete_status',0)
                ->exists();

    return response()->json([
        'exists' => $exists
    ]);
}

    public function getBillCategory(Request $request)
    {
        $admin   = auth()->user();
        $perPage = $request->get('per_page', 10);

        $categories = BillCategory::orderBy('id', 'asc')->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('vendor.bill_category', [
            'admin'      => $admin,
            'categories' => $categories,
            'perPage'    => $perPage,
        ]);
    }

    public function storeBillCategory(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'is_active' => ['required', Rule::in([0, 1])],
        ]);

        $id = $request->id;

        $data = [
            'name'       => $request->name,
            'is_active'  => $request->is_active,
            'created_by' => auth()->id(),
        ];

        if (!empty($id)) {
            BillCategory::where('id', $id)->update($data);
            $message = 'Bill Category updated successfully!';
        } else {
            BillCategory::create($data);
            $message = 'Bill Category created successfully!';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function getExpenseType(Request $request)
    {
        $admin   = auth()->user();
        $perPage = $request->get('per_page', 10);

        $types = ExpenseType::orderBy('id', 'asc')->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('vendor.expense_type', [
            'admin' => $admin,
            'types' => $types,
            'perPage' => $perPage,
        ]);
    }

    public function storeExpenseType(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'is_active' => ['required', Rule::in([0, 1])],
        ]);

        $id = $request->id;

        $data = [
            'name'       => $request->name,
            'is_active'  => $request->is_active,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ];

        if (!empty($id)) {
            ExpenseType::where('id', $id)->update($data);
            $message = 'Expense Type updated successfully!';
        } else {
            ExpenseType::create($data);
            $message = 'Expense Type created successfully!';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function getExpenseCategory(Request $request)
    {
        $admin   = auth()->user();
        $perPage = $request->get('per_page', 10);

        $categories = ExpenseCategory::with('expenseType')->orderBy('id', 'asc')->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        $types = ExpenseType::where('is_active', 1)->orderBy('name')->get();

        return view('vendor.expense_category', [
            'admin'      => $admin,
            'categories' => $categories,
            'types'      => $types,
            'perPage'    => $perPage,
        ]);
    }

    public function storeExpenseCategory(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'expense_type_id' => 'required|exists:expense_types,id',
            'is_active' => ['required', Rule::in([0, 1])],
        ]);

        $id = $request->id;

        $data = [
            'name'       => $request->name,
            'expense_type_id' => $request->expense_type_id,
            'description' => $request->description,
            'is_active'  => $request->is_active,
            'created_by' => auth()->id(),
        ];

        if (!empty($id)) {
            ExpenseCategory::where('id', $id)->update($data);
            $category = ExpenseCategory::find($id);
            $message = 'Expense Category updated successfully!';
        } else {
            $category = ExpenseCategory::create($data);
            $message = 'Expense Category created successfully!';
        }

        return response()->json([
            'success'  => true,
            'message'  => $message,
            'category' => $category,
        ]);
    }

    public function getExpenseReport(Request $request)
    {
        $admin   = auth()->user();
        $perPage = $request->get('per_page', 10);

        $reports = ExpenseReport::orderBy('id', 'asc')->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        // GENERATE NEXT REPORT ID
        $last = ExpenseReport::orderBy('id', 'desc')->first();

        if ($last && $last->report_id) {
            $number = (int) str_replace('ER-', '', $last->report_id);
            $nextNumber = $number + 1;
        } else {
            $nextNumber = 1;
        }

        $nextReportId = 'ER-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return view('vendor.expense_report', [
            'admin'        => $admin,
            'reports'      => $reports,
            'perPage'      => $perPage,
            'nextReportId' => $nextReportId,
        ]);
    }

    public function storeExpenseReport(Request $request)
    {
        $reportIdRules = ['required', 'string', 'max:255'];
        if ($request->filled('id')) {
            $reportIdRules[] = Rule::unique('expense_reports', 'report_id')->ignore((int) $request->id);
        } else {
            $reportIdRules[] = Rule::unique('expense_reports', 'report_id');
        }

        $request->validate([
            'report_id' => $reportIdRules,
            'name' => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date',
        ]);

        $id = $request->id;

        $data = [
            'report_id'       => $request->report_id,
            'report_name'     => $request->name,
            'business_purpose'=> $request->business_purpose,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'trip_id'         => $request->trip_id,
            'is_active'       => 1,
            'created_by'      => auth()->id(),
        ];

        if (!empty($id)) {
            ExpenseReport::where('id', $id)->update($data);
            $message = 'Expense Report updated successfully!';
        } else {
            $report = ExpenseReport::create($data);
            $message = 'Expense Report created successfully!';
            PettyCashHistory::record((int) $report->id, 'report_created', 'Report created.');
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function getNextReportId()
    {
        $last = ExpenseReport::orderBy('id', 'desc')->first();

        if ($last && $last->report_id) {
            $number = (int) str_replace('ER-', '', $last->report_id);
            $nextNumber = $number + 1;
        } else {
            $nextNumber = 1;
        }

        $nextReportId = 'ER-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return response()->json([
            'report_id' => $nextReportId
        ]);
    }
}
