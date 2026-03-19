<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
    </style>
    <script>
        function rowClick(event) {
            // Remove the 'selected' class from any currently selected row
            const selectedRows = document.querySelectorAll('.selected');
            selectedRows.forEach(row => row.classList.remove('selected'));

            // Add the 'selected' class to the clicked row
            const clickedRow = event.currentTarget;
            clickedRow.classList.add('selected');
        }
    </script>
  <body style="overflow-x: hidden;">

    {{-- @php
        dd($serial);
    @endphp --}}
    <div class="page-loader">
      <div class="bar"></div>
    </div>
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')
    <!-- [ Header ] end -->
    <div class="pc-container">
        <div class="pc-content">
            <div class="container py-4">
                @if(!empty($vendor))
                    @foreach ($vendor as $vendor)
                        <div class="row">
                            <div class="col-12">
                                <div class="customer-form-container">
                                    <input type="hidden" class="customer-form-input" name="id" value={{$vendor->id}}>
                                    <input type="hidden" class="customer-form-input" name="vendor_id" value={{$vendor->vendor_id ?? ''}}>
                                    <h1 class="customer-form-header">New Vendor</h1>

                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Primary Contact</div>
                                        <div class="col-md-3 col-sm-12">
                                            <div class="customer-form-group">
                                                <label class="customer-form-label">Salutation</label>
                                                <select class="customer-form-select" name="primary_contact_salutation" value={{$vendor->vendor_salutation}}>
                                                    <option value="Mr.">Mr.</option>
                                                    <option value="Mrs.">Mrs.</option>
                                                    <option value="Ms.">Ms.</option>
                                                    <option value="Dr.">Dr.</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="customer-form-group">
                                                <label class="customer-form-label">First Name</label>
                                                <input type="text" class="customer-form-input" id="first_name" name="primary_contact_first_name" autocomplete="off" autocorrect="off"  value={{$vendor->vendor_first_name}}>
                                                <span class="error_first_name" style="color:red"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="customer-form-group">
                                                <label class="customer-form-label">Last Name</label>
                                                <input type="text" class="customer-form-input" name="primary_contact_last_name" autocomplete="off" autocorrect="off" value={{$vendor->vendor_last_name}}>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Company Name</div>
                                        <div class="col-12 col-md-6">
                                            <div class="customer-form-group">
                                                <input type="text" class="customer-display-name-input" placeholder="Select or type to add" name="company_name" autocomplete="off" autocorrect="off" value={{$vendor->company_name}}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Display Name</div>
                                        <div class="col-12 col-md-6">
                                            <div class="customer-form-group">
                                                <input type="text" class="customer-form-input" id="display_name" name="display_name" autocomplete="off" autocorrect="off" value="{{ $vendor->display_name }}">
                                                <span class="error_display_name" style="color:red"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Email Address</div>
                                        <div class="col-12 col-md-6">
                                            <div class="customer-form-group">
                                                <input type="email" class="customer-form-input" name="email" id="email" autocomplete="off" autocorrect="off" value={{$vendor->email}}>
                                                <span class="error_email" style="color:red"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Phone</div>
                                        <div class="col-md-4 col-sm-12 ">
                                            <div class="customer-form-group">
                                                <label class="customer-form-label">Work Phone</label>
                                                <input type="tel" class="customer-form-input" name="work_phone" autocomplete="off" autocorrect="off" value={{$vendor->work_phone}}>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="customer-form-group">
                                                <label class="customer-form-label">Mobile</label>
                                                <input type="tel" class="customer-form-input" name="mobile" autocomplete="off" autocorrect="off" value={{$vendor->mobile}}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Reference</div>
                                        <div class="col-12 col-md-6">
                                            <div class="customer-form-group">
                                                <input type="text" class="customer-form-input" id="reference_name" name="reference_name" autocomplete="off" autocorrect="off" value="{{ $vendor->reference }}">
                                                <span class="error_reference_name" style="color:red"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-container">
                                    <div class="form-tabs">
                                        <div class="form-tab active" data-tab="other-details">Other Details</div>
                                        <div class="form-tab" data-tab="tds_master">TDS</div>
                                        <div class="form-tab" data-tab="address">Address</div>
                                        <div class="form-tab" data-tab="bank_details">Bank Details</div>
                                        <div class="form-tab" data-tab="contact-persons">Contact Persons</div>
                                        <div class="form-tab" data-tab="remarks">Remarks</div>
                                    </div>

                                    <!-- Other Details Tab -->
                                    <div class="tab-content active" id="other-details">
                                        <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>PAN</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="text" name="pan" id="pan_number" autocomplete="off" autocorrect="off" value={{$vendor->pan_number}}>
                                                    <span class="error_pan_number" style="color:red"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>PAN Upload</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <div class="upload-section">
                                                        <label class="upload-title">Attach File(s) to Bill</label>

                                                        <!-- Hidden file input -->
                                                        <input type="file" id="fileInput" name="pan_uploads[]" multiple  style="display: none;" />
                                                        <input type="hidden" name="existing_files_pan" id="existingFilesInput">
                                                        <!-- Upload buttons -->
                                                        <div class="upload-box">
                                                        <button type="button" class="upload-btn" id="uploadTrigger">📤 Upload File</button>
                                                        </div>
                                                        <p class="upload-hint">You can upload a maximum of 5 files, 10MB each</p>

                                                        <!-- Display uploaded file names -->
                                                        <ul class="file-list" id="fileList"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 row align-items-center mb-3">
                                            <div class="section-title col-md-4">
                                                <span>GST</span>
                                            </div>
                                            <div class="form-group col-md-8">
                                                <input type="text" name="gst_number" id="gst_number" autocomplete="off" autocorrect="off" value={{$vendor->gst_number}}>
                                                <span class="error_gst_number" style="color:red"></span>
                                            </div>
                                        </div>
                                        <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Vendor Type</span>
                                                </div>
                                                <div class="tax-dropdown-wrapper tds-tax-section" style="width:340px;">
                                                    <input type="text" class="form-control vendor-search-input" name="tds_tax_name" placeholder="Select a Vendor Type" autocomplete="off" autocorrect="off" value={{$vendor->vendor_type_name}}  readonly>
                                                    <input type="hidden" name="vendor_type_name" class="vendor_type_name" id="vendor_type_name" >
                                                    <input type="hidden" name="vendor_type_id" class="vendor_type_id" value={{$vendor->vendor_type_id}}>
                                                    <div class="dropdown-menu tax-dropdown">
                                                    <div class="vendor-list">

                                                    </div>
                                                    {{-- <div class="manage-tds-link">⚙️ Manage TDS</div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        {{-- <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Currency</span>
                                                </div>
                                                <div class="checkbox-item col-md-8">
                                                    <input type="checkbox" id="default-currency" name="default_currency" checked>
                                                    <label for="default-currency">INR - Indian Rupee</label>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <div class="row form-section ">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Opening Balance</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="text" placeholder="INR" name="opening_balance" autocomplete="off" autocorrect="off" value={{$vendor->opening_balance}}>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Payment Terms</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <select name="payment_terms" value={{$vendor->payment_terms}}>
                                                        <option value="Due on Receipt">Due on Receipt</option>
                                                        <option value="Net 15">Net 15</option>
                                                        <option value="Net 30">Net 30</option>
                                                        <option value="Net 60">Net 60</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Enable Portal?</span>
                                                </div>
                                                <div class="checkbox-item col-md-8">
                                                    <input type="checkbox" id="enable-portal" name="enable_portal" autocomplete="off" autocorrect="off">
                                                    <label for="enable-portal">Allow portal access for this customer</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Portal Language</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <select name="portal_language" autocomplete="off" autocorrect="off" value={{$vendor->portal_language}}>
                                                        <option value="English">English</option>
                                                        <option value="Hindi">Hindi</option>
                                                        <option value="Spanish">Spanish</option>
                                                        <option value="French">French</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    <div class="row form-section">
                                        <div class="col-12 col-md-6 row align-items-center mb-3">
                                            <div class="section-title ">
                                                <span>Documents</span>
                                            </div>
                                            <div class="file-upload" id="file-upload-container">
                                                <input type="file" id="file-upload-input" style="display: none;"  accept=".pdf,.jpg,.jpeg,.png,.gif" name="documents[]" multiple>
                                                <button class="upload-button" id="upload-button" type="button">Upload File</button>
                                                <div class="hint-text">You can upload a maximum of 10 files, 10MB each</div>
                                                <div id="file-list" class="mt-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="additional-details-container">
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Website URL</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="url" placeholder="ex: www.zylker.com" name="website" autocomplete="off" autocorrect="off" value={{$vendor->website}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Department</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="text" name="department" autocomplete="off" autocorrect="off" value={{$vendor->website}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Designation</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="text" name="designation" autocomplete="off" autocorrect="off" value={{$vendor->department}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Twitter</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="url" placeholder="http://www.twitter.com/" name="twitter" autocomplete="off" autocorrect="off" value={{$vendor->twitter}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Skype Name/Number</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="text" name="skype" autocomplete="off" autocorrect="off" value={{$vendor->skype}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Facebook</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="url" placeholder="http://www.facebook.com/" autocomplete="off" autocorrect="off" name="facebook" value={{$vendor->facebook}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section">
                                            <div class="col-12">
                                                <button class="btn btn-sm btn-outline-danger remove-additional-details" type="button">
                                                    <i class="fas fa-times"></i> Remove these details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="row form-section">
                                            <div class="col-12 col-md-6">
                                                <button class="add-button" id="add-more-details" type="button">
                                                    <i class="fas fa-plus"></i> Add more details
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address Tab -->
                                        <div class="tab-content" id="address">
                                            <div class="row">
                                                <!-- Billing Address (Left Side) -->
                                                <div class="col-lg-6 col-md-12">
                                                    <div class="address-section">
                                                        <div class="address-header">
                                                            <h3>Billing Address</h3>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                                <label class="col-md-4 col-form-label">Attention</label>
                                                                <div class="col-md-8">
                                                                    <input type="text" id="billing_attention" name="billing_attention" class="form-control custom-input" autocomplete="off" autocorrect="off" value={{$vendor->billingAddress->attention}}>
                                                                </div>

                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Country/Region</label>
                                                            <div class="col-md-8">
                                                                <select class="billing-field" name="billing_country" autocomplete="off" autocorrect="off" value={{$vendor->billingAddress->country}}>
                                                                    <option value="India">India</option>
                                                                    <option value="United States">United States</option>
                                                                    <option value="United Kingdom">United Kingdom</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">address</label>
                                                            <div class="col-md-8">
                                                                <textarea class="billing-field" autocomplete="off" autocorrect="off" name="billing_address">{{$vendor->billingAddress->address}}</textarea>
                                                                {{-- <input type="text" class="billing-field" name="billing_address" value={{$vendor->billingAddress->address}}> --}}
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">City</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="billing-field" name="billing_city" autocomplete="off" autocorrect="off" value={{$vendor->billingAddress->city}}>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">State</label>
                                                            <!-- <div class="col-md-8">
                                                                <select class="billing-field" name="billing_state" autocomplete="off" autocorrect="off" value={{$vendor->billingAddress->state}}>
                                                                    <option value="Tamil Nadu">Tamil Nadu</option>
                                                                    <option value="Kerala">Kerala</option>
                                                                    <option value="Bengalore">Bengalore</option>
                                                                    <option value="Maharashtra">Maharashtra</option>
                                                                    <option value="Delhi">Delhi</option>
                                                                    <option value="Karnataka">Karnataka</option>
                                                                </select>
                                                            </div> -->
                                                            <div class="col-md-8">
                                                                <select class="billing-field" name="billing_state" id="billing_state"></select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Zip Code</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="billing-field" name="billing_zip_code" autocomplete="off" autocorrect="off" value={{$vendor->billingAddress->zip_code}} >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">phone</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="billing-field" name="billing_phone" autocomplete="off" autocorrect="off" value={{$vendor->billingAddress->phone}} >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Fax Number</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="billing-field" name="billing_fax" autocomplete="off" autocorrect="off" value={{$vendor->billingAddress->fax}} >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Shipping Address (Right Side) -->
                                                <div class="col-lg-6 col-md-12">
                                                    <div class="address-section">
                                                        <div class="address-header">
                                                            <h3>Shipping Address</h3>
                                                            <button class="copy-button" id="copy-billing" type="button">
                                                                <i class="fas fa-copy"></i> Copy from Billing
                                                            </button>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Attention</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="shipping-field" name="shipping_attention" autocomplete="off" autocorrect="off" value={{$vendor->shippingAddress->attention }}>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Country/Region</label>
                                                            <div class="col-md-8">
                                                                <select class="shipping-field" name="shipping_country" autocomplete="off" autocorrect="off" value={{$vendor->shippingAddress->country}}>
                                                                    <option value="India">India</option>
                                                                    <option value="United States">United States</option>
                                                                    <option value="United Kingdom">United Kingdom</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Address</label>
                                                            <div class="col-md-8">
                                                                <textarea class="shipping-field" autocomplete="off" autocorrect="off" name="shipping_address">{{$vendor->shippingAddress->address}}</textarea>
                                                                {{-- <input type="text" class="shipping-field" name="shipping_address" value={{$vendor->shippingAddress->address}}> --}}
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">City</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="shipping-field" name="shipping_city" autocomplete="off" autocorrect="off" value={{$vendor->shippingAddress->city}}>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">State</label>
                                                            <!-- <div class="col-md-8">
                                                                <select class="shipping-field" name="shipping_state" autocomplete="off" autocorrect="off" value={{$vendor->shippingAddress->state}}>
                                                                    <option value="Tamil Nadu">Tamil Nadu</option>
                                                                    <option value="Bengalore">Bengalore</option>
                                                                    <option value="Maharashtra">Maharashtra</option>
                                                                    <option value="Delhi">Delhi</option>
                                                                    <option value="Karnataka">Karnataka</option>
                                                                </select>
                                                            </div> -->
                                                            <div class="col-md-8">
                                                                <select class="shipping-field" name="shipping_state" id="shipping_state"></select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Zip Code</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="shipping-field" name="shipping_zip_code" autocomplete="off" autocorrect="off" value={{$vendor->shippingAddress->zip_code}}>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">phone</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="shipping-field" name="shipping_phone" autocomplete="off" autocorrect="off" value={{$vendor->shippingAddress->phone}}>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Fax Number</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="shipping-field" name="shipping_fax" autocomplete="off" autocorrect="off" value={{$vendor->shippingAddress->fax}}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Bank Details Tab -->
                                            <div class="tab-content" id="bank_details">
                                                <div class="row">
                                                <div id="bank-container">

                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <button type="button" class="btn btn-primary" id="add-bank" onclick="addBankSection()">+ Add Bank Details</button>
                                                </div>

                                                </div>
                                            </div>



                                    <!-- Contact Persons Tab -->
                                    <div class="tab-content" id="contact-persons">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="contact-persons-container">
                                                    <!-- Contact person rows will be added here -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button class="add-button" id="add-contact-person" type="button">
                                                    <i class="fas fa-plus"></i> Add Contact Person
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- TDS Tab -->
                                    <div class="tab-content" id="tds_master">
                                        <div class="row">
                                            <div id="bank-container">

                                                <div class="row form-section">
                                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                                        <div class="section-title col-md-4">
                                                            <span>Tds Tax</span>
                                                        </div>
                                                        <div class="tax-dropdown-wrapper tds-tax-section" style="width:340px;">
                                                            <input type="text" class="form-control tax-search-input" name="tds_tax_name" placeholder="Select a Tax" autocomplete="off" autocorrect="off" value="{{ $vendor->tds_tax_name }}"  readonly>
                                                            <input type="hidden" name="tds_tax_selected" class="selected-tds-tax" id="tds_tax_value" >
                                                            <input type="hidden" name="tds_tax_id" class="tds-tax-id" value={{$vendor->tds_tax_id}}>
                                                            <div class="dropdown-menu tax-dropdown">
                                                            <div class="tax-list">

                                                            </div>
                                                            <div class="manage-tds-link">⚙️ Manage TDS</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row form-section">
                                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                                        <div class="section-title col-md-4">
                                                            <span>Tds Amount</span>
                                                        </div>
                                                        <div class="form-group col-md-8">
                                                            <input type="number" placeholder="INR" name="tds_amount" autocomplete="off" autocorrect="off" value={{$vendor->tds_amount}}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Remarks Tab -->
                                    <div class="tab-content" id="remarks">
                                        <div class="row">
                                            <div class="col-12 col-md-8">
                                                <div class="form-section">
                                                    <div class="section-title">
                                                        <span>Remarks (For Internal Use)</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea name="remarks">{{ $vendor->remarks }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="row mt-3 p-2 justify-content-end">
                                <div class="col-auto">
                                    <button class="btn btn-success" id="save-customer">Save</button>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-danger close" type="button">Close</button>
                                </div>
                            </div>

                            </div>
                        </div>
                    @endforeach
                @else
                <div class="row">
                    <div class="col-12">
                        <div class="customer-form-container">
                            <h1 class="customer-form-header">New Vendor</h1>
                            <input type="hidden" class="customer-form-input" name="vendor_id" value={{$serial ?? ''}}>

                            {{-- <div class="customer-prefill-section">
                                <input type="checkbox" class="customer-prefill-checkbox" id="customerPrefill" name="prefill_from_gst">
                                <label for="customerPrefill" class="customer-prefill-label">Prefill Customer details from the GST portal using the Customer's GSTIN.</label>
                                <a href="#" class="customer-prefill-button">Prefill ></a>
                            </div> --}}



                            <div class="row customer-form-section">
                                <div class="customer-section-title">Primary Contact</div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="customer-form-group">
                                        <label class="customer-form-label">Salutation</label>
                                        <select class="customer-form-select" name="primary_contact_salutation" >
                                            <option value="Mr.">Mr.</option>
                                            <option value="Mrs.">Mrs.</option>
                                            <option value="Ms.">Ms.</option>
                                            <option value="Dr.">Dr.</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="customer-form-group">
                                        <label class="customer-form-label">First Name</label>
                                        <input type="text" class="customer-form-input" id="first_name" autocomplete="off" autocorrect="off" name="primary_contact_first_name" >
                                        <span class="error_first_name" style="color:red"></span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="customer-form-group">
                                        <label class="customer-form-label">Last Name</label>
                                        <input type="text" class="customer-form-input" autocomplete="off" autocorrect="off" name="primary_contact_last_name" >
                                    </div>
                                </div>
                            </div>

                            <div class="row customer-form-section">
                                <div class="customer-section-title">Company Name</div>
                                <div class="col-12 col-md-6">
                                    <div class="customer-form-group">
                                        <input type="text" class="customer-display-name-input" id="company_name" placeholder="Select or type to add" autocomplete="off" autocorrect="off" name="company_name" >

                                    </div>
                                </div>
                            </div>
                            <div class="row customer-form-section">
                                <div class="customer-section-title">Display Name</div>
                                <div class="col-12 col-md-6">
                                    <div class="customer-form-group">
                                        <input type="text" class="customer-form-input" id="display_name" autocomplete="off" autocorrect="off" name="display_name" >
                                        <span class="error_display_name" style="color:red"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row customer-form-section">
                                <div class="customer-section-title">Email Address</div>
                                <div class="col-12 col-md-6">
                                    <div class="customer-form-group">
                                        <input type="email" id="email" class="customer-form-input" autocomplete="off" autocorrect="off" name="email" >
                                        <span class="error_email" style="color:red"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row customer-form-section">
                                <div class="customer-section-title">Phone</div>
                                <div class="col-md-4 col-sm-12 ">
                                    <div class="customer-form-group">
                                        <label class="customer-form-label">Work Phone</label>
                                        <input type="tel" class="customer-form-input" name="work_phone" autocomplete="off" autocorrect="off" >

                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="customer-form-group">
                                        <label class="customer-form-label">Mobile</label>
                                        <input type="tel" class="customer-form-input" name="mobile"  id="phone_number" autocomplete="off" autocorrect="off">
                                        <span class="error_phone_number" style="color:red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row customer-form-section">
                                <div class="customer-section-title">Reference</div>
                                <div class="col-12 col-md-6">
                                    <div class="customer-form-group">
                                        <input type="text" class="customer-form-input" id="reference_name" name="reference_name" autocomplete="off" autocorrect="off" >
                                        <span class="error_reference_name" style="color:red"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-container">
                            <div class="form-tabs">
                                <div class="form-tab active" data-tab="other-details">Other Details</div>
                                <div class="form-tab" data-tab="tds_master">TDS</div>
                                <div class="form-tab" data-tab="address">Address</div>
                                <div class="form-tab" data-tab="bank_details">Bank Details</div>
                                <div class="form-tab" data-tab="contact-persons">Contact Persons</div>
                                <div class="form-tab" data-tab="remarks">Remarks</div>
                            </div>

                            <!-- Other Details Tab -->
                            <div class="tab-content active" id="other-details">
                                <div class="row form-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>PAN</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" name="pan" id="pan_number" autocomplete="off" autocorrect="off">
                                            <span class="error_pan_number" style="color:red"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>PAN Upload</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <div class="upload-section">
                                                <label class="upload-title">Attach File(s) to Bill</label>

                                                <!-- Hidden file input -->
                                                <input type="file" id="fileInput" name="pan_uploads[]" multiple style="display: none;" />
                                                <input type="hidden" name="existing_files" id="existingFilesInput">
                                                <!-- Upload buttons -->
                                                <div class="upload-box">
                                                <button type="button" class="upload-btn" id="uploadTrigger">📤 Upload File</button>
                                                </div>
                                                <p class="upload-hint">You can upload a maximum of 5 files, 10MB each</p>

                                                <!-- Display uploaded file names -->
                                                <ul class="file-list" id="fileList"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>GST</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" name="gst_number" id="gst_number" autocomplete="off" autocorrect="off">
                                            <span class="error_gst_number" style="color:red"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Vendor Type</span>
                                        </div>
                                        <div class="tax-dropdown-wrapper tds-tax-section" style="width:340px;">
                                            <input type="text" class="form-control vendor-search-input" name="tds_tax_name" autocomplete="off" autocorrect="off" placeholder="Select a Vendor Type"  readonly>
                                            <input type="hidden" name="vendor_type_name" class="vendor_type_name" id="vendor_type_name" >
                                            <input type="hidden" name="vendor_type_id" class="vendor_type_id" >
                                            <div class="dropdown-menu tax-dropdown">
                                            <div class="vendor-list">

                                            </div>
                                            {{-- <div class="manage-tds-link">⚙️ Manage TDS</div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="row form-section">
                                    <div class="col-12 col-md-6">
                                        <div class="section-title">
                                            <span>Currency</span>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="default-currency" name="default_currency" checked>
                                            <label for="default-currency">INR - Indian Rupee</label>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="row form-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Opening Balance</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" placeholder="INR" name="opening_balance" autocomplete="off" autocorrect="off" >
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Payment Terms</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <select name="payment_terms" autocomplete="off" autocorrect="off">
                                                <option value="Due on Receipt">Due on Receipt</option>
                                                <option value="Net 15">Net 15</option>
                                                <option value="Net 30">Net 30</option>
                                                <option value="Net 60">Net 60</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Enable Portal?</span>
                                        </div>
                                        <div class="checkbox-item col-md-8">
                                            <input type="checkbox" id="enable-portal" name="enable_portal" autocomplete="off" autocorrect="off">
                                            <label for="enable-portal">Allow portal access for this customer</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Portal Language</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <select name="portal_language" autocomplete="off" autocorrect="off">
                                                <option value="English">English</option>
                                                <option value="Hindi">Hindi</option>
                                                <option value="Spanish">Spanish</option>
                                                <option value="French">French</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            <div class="row form-section">
                                <div class="col-12 col-md-6 row align-items-center mb-3">
                                    <div class="section-title">
                                        <span>Documents</span>
                                    </div>
                                    <div class="file-upload" id="file-upload-container">
                                        <input type="file" id="file-upload-input" style="display: none;" accept=".pdf,.jpg,.jpeg,.png,.gif" name="documents[]" multiple>
                                        <button class="upload-button" id="upload-button" type="button">Upload File</button>
                                        <div class="hint-text">You can upload a maximum of 10 files, 10MB each</div>
                                        <div id="file-list" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="additional-details-container">
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Website URL</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="url" placeholder="ex: www.zylker.com" name="website" autocomplete="off" autocorrect="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Department</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" name="department" autocomplete="off" autocorrect="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Designation</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" name="designation" autocomplete="off" autocorrect="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Twitter</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="url" placeholder="http://www.twitter.com/" name="twitter" autocomplete="off" autocorrect="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Skype Name/Number</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" name="skype" autocomplete="off" autocorrect="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Facebook</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="url" placeholder="http://www.facebook.com/" name="facebook" autocomplete="off" autocorrect="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section">
                                    <div class="col-12">
                                        <button class="btn btn-sm btn-outline-danger remove-additional-details" type="button">
                                            <i class="fas fa-times"></i> Remove these details
                                        </button>
                                    </div>
                                </div>
                            </div>
                                <div class="col-12 col-md-6">
                                        <button class="add-button" id="add-more-details" type="button">
                                            <i class="fas fa-plus"></i> Add more details
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Tab -->
                            <div class="tab-content" id="address">
                                <div class="row">
                                    <!-- Billing Address (Left Side) -->
                                    <div class="col-lg-6 col-md-12">
                                        <div class="address-section">
                                            <div class="address-header">
                                                <h3>Billing Address</h3>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                    <label class="col-md-4 col-form-label">Attention</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="billing_attention" name="billing_attention" class="form-control custom-input" autocomplete="off" autocorrect="off">
                                                    </div>
                                                    {{-- <input type="text" class="col-md-8 billing-field" name="billing_attention" > --}}

                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Country/Region</label>
                                                <div class="col-md-8">
                                                    <select class="billing-field" name="billing_country" autocomplete="off" autocorrect="off">
                                                        <option value="India">India</option>
                                                        <option value="United States">United States</option>
                                                        <option value="United Kingdom">United Kingdom</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Address</label>
                                                <div class="col-md-8">
                                                    <textarea class="billing-field" autocomplete="off" autocorrect="off" name="billing_address"></textarea>
                                                    {{-- <input type="text" class="billing-field" name="billing_address" > --}}
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">City</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="billing-field" name="billing_city" autocomplete="off" autocorrect="off">
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">State</label>
                                                <!-- <div class="col-md-8">
                                                    <select class="billing-field" autocomplete="off" autocorrect="off" name="billing_state" >
                                                        <option value="Tamil Nadu">Tamil Nadu</option>
                                                        <option value="Kerala">Kerala</option>
                                                        <option value="Bengalore">Bengalore</option>
                                                        <option value="Maharashtra">Maharashtra</option>
                                                        <option value="Delhi">Delhi</option>
                                                        <option value="Karnataka">Karnataka</option>
                                                        <option value="Andhra Pradesh">Andhra Pradesh</option>
                                                    </select>
                                                </div> -->
                                                <div class="col-md-8">
                                                    <select class="billing-field" name="billing_state" id="billing_state"></select>
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Zip Code</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="billing-field" name="billing_zip_code" autocomplete="off" autocorrect="off">
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">phone</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="billing-field" name="billing_phone" autocomplete="off" autocorrect="off">
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Fax Number</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="billing-field" name="billing_fax" autocomplete="off" autocorrect="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Shipping Address (Right Side) -->
                                    <div class="col-lg-6 col-md-12">
                                        <div class="address-section">
                                            <div class="address-header">
                                                <h3>Shipping Address</h3>
                                                <button class="copy-button" id="copy-billing" type="button">
                                                    <i class="fas fa-copy"></i> Copy from Billing
                                                </button>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Attention</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="shipping-field" name="shipping_attention" autocomplete="off" autocorrect="off" >
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Country/Region</label>
                                                <div class="col-md-8">
                                                    <select class="shipping-field" autocomplete="off" autocorrect="off" name="shipping_country" >
                                                        <option value="India">India</option>
                                                        <option value="United States">United States</option>
                                                        <option value="United Kingdom">United Kingdom</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Address</label>
                                                <div class="col-md-8">
                                                    <textarea class="shipping-field" name="shipping_address" autocomplete="off" autocorrect="off"></textarea>
                                                    {{-- <input type="text" class="shipping-field" name="shipping_address" > --}}
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">City</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="shipping-field" autocomplete="off" autocorrect="off" name="shipping_city">
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">State</label>
                                                <!-- <div class="col-md-8">
                                                    <select class="shipping-field" autocomplete="off" autocorrect="off" name="shipping_state" >
                                                        <option value="Tamil Nadu">Tamil Nadu</option>
                                                        <option value="Bengalore">Bengalore</option>
                                                        <option value="Maharashtra">Maharashtra</option>
                                                        <option value="Delhi">Delhi</option>
                                                        <option value="Karnataka">Karnataka</option>
                                                        <option value="Andhra Pradesh">Andhra Pradesh</option>
                                                    </select>
                                                </div> -->
                                                <div class="col-md-8">
                                                    <select class="shipping-field" name="shipping_state" id="shipping_state"></select>
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Zip Code</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="shipping-field" name="shipping_zip_code" autocomplete="off" autocorrect="off">
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">phone</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="shipping-field" name="shipping_phone" autocomplete="off" autocorrect="off">
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Fax Number</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="shipping-field" name="shipping_fax" autocomplete="off" autocorrect="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Bank Details Tab -->
                            <div class="tab-content" id="bank_details">
                                <div class="row">
                                  <div id="bank-container">
                                    <div class="bank-section col-12 col-md-8" data-index="0">
                                      <div class="form-group row align-items-center mb-3">
                                        <label class="col-md-4">Account Holder Name</label>
                                        <div class="col-md-8">
                                          <input type="text" name="bank_details[0][account_holder_name]" autocomplete="off" autocorrect="off">
                                        </div>
                                      </div>
                                      <div class="form-group row align-items-center mb-3">
                                        <label class="col-md-4">Bank Name</label>
                                        <div class="col-md-8">
                                          <input type="text" name="bank_details[0][bank_name]" autocomplete="off" autocorrect="off">
                                        </div>
                                      </div>
                                      <div class="form-group row align-items-center mb-3">
                                        <label class="col-md-4">Account Number<span style="color:red;">*</span></label>
                                        <div class="col-md-8">
                                          <input type="text" name="bank_details[0][account_number]" autocomplete="off" autocorrect="off">
                                          <div class="text-danger d-none error-account-number">⚠️ Please enter the Account Number.</div>
                                        </div>
                                      </div>
                                      <div class="form-group row align-items-center mb-3">
                                        <label class="col-md-4">Re-enter Account Number<span style="color:red;">*</span></label>
                                        <div class="col-md-8">
                                          <input type="text" name="bank_details[0][re_account_number]" autocomplete="off" autocorrect="off">
                                        </div>
                                      </div>
                                      <div class="form-group row align-items-center mb-3">
                                        <label class="col-md-4">IFSC<span style="color:red;">*</span></label>
                                        <div class="col-md-8">
                                          <input type="text" name="bank_details[0][ifsc]" autocomplete="off" autocorrect="off">
                                          <div class="text-danger d-none error-ifsc">⚠️ Enter the IFSC.</div>
                                        </div>
                                      </div>
                                      <div class="form-group row align-items-center mb-3">
                                        <label class="col-md-4">Bank Upload <span style="color:red;">*</span></label>
                                        <div class="form-group col-md-8">
                                            <div class="upload-section">
                                                <label class="upload-title">Attach File(s) to Bill</label>

                                                <!-- Hidden file input -->
                                                <input type="file" class="fileInputbank" data-index="0" name="bank_details[0]bank_uploads_0[]" multiple style="display: none;" />
                                                <input type="hidden" name="existing_files_bank" id="existingFilesBank">
                                                <!-- Upload buttons -->
                                                <div class="upload-box">
                                                <button type="button" class="upload-btn uploadTriggerbank" data-index="0">📤 Upload File</button>
                                                </div>
                                                <p class="upload-hint">You can upload a maximum of 5 files, 10MB each</p>

                                                <!-- Display uploaded file names -->
                                                <ul class="file-list fileListbank" data-index="0"></ul>
                                            </div>
                                        </div>
                                      </div>

                                      <!-- remove button injected via JS for clones -->
                                    </div>
                                  </div>
                                  <div class="col-md-12 mt-3">
                                    <button type="button" class="btn btn-primary" id="add-bank" onclick="addBankSection()">+ Add Bank Details</button>
                                  </div>

                                </div>
                              </div>


                            <!-- Contact Persons Tab -->
                            <div class="tab-content" id="contact-persons">
                                <div class="row">
                                    <div class="col-12">
                                        <div id="contact-persons-container">
                                            <!-- Contact person rows will be added here -->
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="add-button" id="add-contact-person" type="button">
                                            <i class="fas fa-plus"></i> Add Contact Person
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- TDS Tab -->
                            <div class="tab-content" id="tds_master">
                                <div class="row">
                                    <div id="bank-container">

                                         <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Tds Tax</span>
                                                </div>
                                                <div class="tax-dropdown-wrapper tds-tax-section" style="width:340px;">
                                                    <input type="text" class="form-control tax-search-input" name="tds_tax_name" autocomplete="off" autocorrect="off" placeholder="Select a Tax" readonly>
                                                    <input type="hidden" name="tds_tax_selected" class="selected-tds-tax" id="tds_tax_value">
                                                    <input type="hidden" name="tds_tax_id" class="tds-tax-id">
                                                    <div class="dropdown-menu tax-dropdown">
                                                    <div class="tax-list">

                                                    </div>
                                                    <div class="manage-tds-link">⚙️ Manage TDS</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Tds Amount</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="number" placeholder="INR" name="tds_amount" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- Remarks Tab -->
                            <div class="tab-content" id="remarks">
                                <div class="row">
                                    <div class="col-12 col-md-8">
                                        <div class="form-section">
                                            <div class="section-title">
                                                <span>Remarks (For Internal Use)</span>
                                            </div>
                                            <div class="form-group">
                                                <textarea name="remarks"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="row mt-3 p-2 justify-content-end">
                        <div class="col-auto">
                            <button class="btn btn-success" id="save-customer">Save</button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-danger close" type="button">Close</button>
                        </div>
                    </div>

                    </div>
                @endif
                {{-- tds modal --}}
                <!-- Manage TDS Modal -->
                <div id="tdsModal" class="tds-modal">
                <div class="tds-modal-content">
                    <div class="tds-modal-header">
                    <h4>Manage TDS</h4>
                    <span class="close-modal">&times;</span>
                    </div>

                    <div class="tds-modal-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h5>TDS taxes</h5>
                        <div>
                        <button type="button" class="btn-new-tds">+ New TDS Tax</button>
                        </div>
                    </div>
                    <div class="gst-content">
                        @include('vendor.partials.tds_table', ['Tbltdstax' => $Tbltdstax])
                    </div>
                    </div>
                </div>
                </div>

                <!-- New TCS Tax Modal -->
                <div id="newTdsModal" class="tds-modal">
                <div class="tds-modal-content" style="max-width: 600px;">
                    <div class="tds-modal-header">
                    <h4>New TDS</h4>
                    <span class="close-new-modal" style="font-size: 2rem;cursor:pointer">&times;</span>
                    </div>

                    <div class="tds-modal-body">
                    <form>
                        <div style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label>Tax Name* <span style="color: red">*</span></label>
                            <input type="text" class="form-control tds_name" autocomplete="off" autocorrect="off"/>
                        </div>
                        <div style="flex: 1;">
                            <label>Rate (%) <span style="color: red">*</span></label>
                            <input type="text" class="form-control tds_rate" autocomplete="off" autocorrect="off"/>
                        </div>
                        </div>
                        <div class="mt-2" style="display: flex; gap: 10px;flex-direction:column;">
                        <label>Section<span style="color: red">*</span></label>
                            <div class="tax-dropdown-wrapper tdssection">
                                <input type="text" class="form-control tax-section-input" autocomplete="off" autocorrect="off" name="tds_section_name" placeholder="Select a Tax" readonly>
                                <input type="hidden" name="tds_section_id" class="tds_section_id">
                                <div class="dropdown-menu tax-dropdown">
                                    <div class="tds-section-list">
                                    </div>
                                    <div class="manage-tds-section">⚙️ Manage Section</div>
                                </div>
                            </div>
                        </div>

                        <h5 style="margin: 10px 0;">Applicable Period</h5>
                        <div style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label>Start Date</label>
                            <input type="text" class="form-control datepicker tds_start_date" autocomplete="off" autocorrect="off" placeholder="dd/MM/yyyy" />
                        </div>
                        <div style="flex: 1;">
                            <label>End Date</label>
                            <input type="text" class="form-control datepicker tds_end_date" autocomplete="off" autocorrect="off" placeholder="dd/MM/yyyy" />
                        </div>
                        </div>
                        <br />
                        <button class="btn-save tds_save" type="submit">Save</button>
                        <button class="btn-cancel close-new-modal" type="button">Cancel</button>
                    </form>
                    </div>
                </div>
                </div>
                <!-- New TDS Section Modal -->
                <!-- Manage TDS Modal -->
                <div id="SectionModal" class="tds-modal">
                <div class="tds-modal-content">
                    <div class="tds-modal-header">
                    <h4>Manage Section</h4>
                    <span class="close-sec-modal">&times;</span>
                    </div>
                    <div class="tds-modal-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h5>Section</h5>
                        <div>
                        <button type="button" class="btn-new-section">+ New Section</button>
                        </div>
                    </div>
                    <div class="gst-content">
                        @include('vendor.partials.section_table', ['Tbltdssection' => $Tbltdssection])
                    </div>
                    </div>
                </div>
                </div>

                <div id="newsectionModal" class="tds-modal">
                <div class="tds-modal-content" style="max-width: 600px;">
                    <div class="tds-modal-header">
                    <h4>New Section</h4>
                    <span class="close-section-modal" style="font-size: 2rem;cursor:pointer">&times;</span>
                    </div>

                    <div class="tds-modal-body">
                    <form>
                        <div style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label>Name<span style="color: red">*</span></label>
                            <input type="text" class="form-control section_name" autocomplete="off" autocorrect="off"/>
                            <input type="hidden" class="form-control section_id" />
                        </div>
                        </div>
                        <br />
                        <button class="btn-save section_save" type="submit">Save</button>
                        <button class="btn-cancel close-section-modal" type="button">Cancel</button>
                    </form>
                    </div>
                </div>
                </div>
                <div
                    class="modal fade"
                    id="documentModal1"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                            <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Document Management system</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                            </div>
                            <div class="row">
                            <div class="col-sm-3"><br>
                                    <div class="btn-group-vertical w-100" id="image_pdfs" style="
                                        margin-left: 11px;
                                    ">
                                    <button type="button" class="btn btn-primary">Tab 1</button>
                                    <button type="button" class="btn btn-primary">Tab 2</button>
                                    <button type="button" class="btn btn-primary">Tab 3</button>
                                    <!-- More tabs if needed -->
                                    </div>
                                    </div>
                                        <div class="col-sm-9">
                                        <embed id="pdfmain" src="" width="100%" height="600px" />
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- tds modal --}}
                </div>

            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
{{-- <script src="{{ asset('/assets/js/purchase/vendor.js') }}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@if ($errors->any())
<script>
    $(document).ready(function () {
        $('#exampleModal').modal('show');
    });
</script>
@endif
 <script>
    toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
             @if(!empty($vendor))
                let existingDocuments = {!! json_encode($vendor->documents) !!};
                let existingpan_upload = {!! json_encode($vendor->pan_upload) !!};
                const existingContacts = @json($vendor->contacts ?? []);
                const existingbankdetails = @json($vendor->bankdetails ?? []);
                const isEditMode = existingContacts.length > 0;
            @else
                let existingDocuments = [];
                let existingpan_upload = [];
                const existingContacts = [];
                const existingbankdetails = [];
                const isEditMode = false;

            @endif

            const states = [
                "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar",
                "Chhattisgarh", "Goa", "Gujarat", "Haryana",
                "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala",
                "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya",
                "Mizoram", "Nagaland", "Odisha", "Punjab",
                "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana",
                "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal",
                "Delhi", "Puducherry"
            ];

            let select = document.getElementById("shipping_state");
            select.innerHTML = `<option value="">-- Select State --</option>`;

            for (let i = 0; i < states.length; i++) {
                select.innerHTML += `<option value="${states[i]}">${states[i]}</option>`;
            }
            let select11 = document.getElementById("billing_state");
            select11.innerHTML = `<option value="">-- Select State --</option>`;

            for (let i = 0; i < states.length; i++) {
                select11.innerHTML += `<option value="${states[i]}">${states[i]}</option>`;
            }
            // If it's still a stringified JSON, parse it
            if (typeof existingDocuments === 'string'  || typeof existingpan_upload === 'string') {
                try {
                    existingDocuments = JSON.parse(existingDocuments);
                    existingpan_upload = JSON.parse(existingpan_upload);
                } catch (e) {
                    existingDocuments = [];
                    existingpan_upload = [];
                    console.error("Invalid JSON in documents field", e);
                }
            }

            $(document).ready(function () {

                const fileList = $('#file-list');

                existingDocuments.forEach(function (file, index) {
                    const extension = file.split('.').pop().toLowerCase();
                    const isPdf = extension === 'pdf';

                    const fileItem = $(`
                        <div class="file-item d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <i class="fas ${isPdf ? 'fa-file-pdf text-danger' : 'fa-file-image text-primary'} me-2"></i>
                                <a href="/uploads/customers/${file}" target="_blank">${file}</a>
                            </div>
                            <button class="btn btn-sm btn-outline-danger remove-existing-file" data-filename="${file}" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);

                    fileList.append(fileItem);
                });

                $(document).on('click', '.remove-existing-file', function () {
                    const filename = $(this).data('filename');
                    $(this).parent().remove();

                    const removedInput = $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'removed_documents[]')
                        .val(filename);
                    $('#file-upload-container').append(removedInput);
                });


                const fileListpan = $('#fileList');
                fileListpan.empty();
                let existingFilespan = [];
                // If existing files present (edit mode)
                if (Array.isArray(existingpan_upload) && existingpan_upload.length > 0) {
                     existingFilespan = [...existingpan_upload];
                    existingpan_upload.forEach((filename, index) => {
                        const li = $(`
                            <li>
                                <a href="/uploads/customers/${filename}" target="_blank">${filename}</a>
                                <span class="remove-existing-file" data-index="${index}" data-filename="${filename}">❌</span>
                            </li>
                        `);
                        fileListpan.append(li);
                    });
                }
                $('#existingFilesInput').val(JSON.stringify(existingFilespan));
                // Handle PAN file removal
            $(document).on('click', '.remove-existing-file', function() {
                const filename = $(this).data('filename');

                // Remove from UI
                $(this).closest('.file-item').remove();

                // Remove from our array
                existingFilespan = existingFilespan.filter(file => file !== filename);

                // Update the hidden input
                $('#existingFilesInput').val(JSON.stringify(existingFilespan));

                // Optionally add to removed files list
                const removedInput = $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'removed_pan_files[]')
                    .val(filename);
                $('#file-upload-container').append(removedInput);
            });


            });
            $(document).ready(function () {
                const selectedFilesMap = {}; // For new files
                const existingFilesMap = {}; // For existing files from server

                if (existingbankdetails.length > 0) {
                    $('.bank-section').remove(); // remove default one
                    console.log("existingbankdetails", existingbankdetails);

                    existingbankdetails.forEach((bank, index) => {
                        const fileArray = bank.bank_uploads ? JSON.parse(bank.bank_uploads) : [];
                        console.log("fileArray", fileArray);

                        // Store existing files
                        existingFilesMap[index] = fileArray;

                        // Create the file list HTML for existing files
                        let fileListHtml = '';
                        fileArray.forEach((file, i) => {
                            fileListHtml += `

                            <li class="existing-file">
                                <a href="/uploads/customers/${file}" target="_blank">${file}</a>
                                <span class="remove-existing-file" data-index="${index}" data-file-index="${i}">❌</span>
                            </li>
                            `;
                        });
                        const bankSection = $(`
                                    <div class="bank-section col-12 col-md-6" data-index="${index}">
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-md-4">Account Holder Name</label>
                                            <div class="col-md-8">
                                                <input type="hidden" name="bank_details[${index}][id]" value="${bank.id || ''}">
                                                <input type="text" name="bank_details[${index}][account_holder_name]" value="${bank.account_holder_name || ''}">
                                            </div>
                                        </div>
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-md-4">Bank Name</label>
                                            <div class="col-md-8">
                                                <input type="text" name="bank_details[${index}][bank_name]" value="${bank.bank_name || ''}">
                                            </div>
                                        </div>
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-md-4">Account Number<span style="color:red;">*</span></label>
                                            <div class="col-md-8">
                                                <input type="text" name="bank_details[${index}][account_number]" value="${bank.accont_number || ''}">
                                            </div>
                                        </div>
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-md-4">Re-enter Account Number<span style="color:red;">*</span></label>
                                            <div class="col-md-8">
                                                <input type="text" name="bank_details[${index}][re_account_number]" value="${bank.accont_number || ''}">
                                            </div>
                                        </div>
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-md-4">IFSC<span style="color:red;">*</span></label>
                                            <div class="col-md-8">
                                                <input type="text" name="bank_details[${index}][ifsc]" value="${bank.ifsc_code || ''}">
                                            </div>
                                        </div>
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-md-4">Upload Documents<span style="color:red;">*</span></label>
                                            <div class="form-group col-md-8">
                                                <div class="upload-section">
                                                    <label class="upload-title">Attach File(s) to Bill</label>
                                                    <!-- Hidden file input -->
                                                    <input type="file" class="fileInputbank" data-index="${index}" name="bank_details[${index}]bank_uploads_${index}[]" multiple style="display: none;" />
                                                    <input type="hidden" name="bank_details[${index}][existing_files]" value='${JSON.stringify(fileArray)}'>
                                                    <!-- Upload buttons -->
                                                    <div class="upload-box">
                                                    <button type="button" class="upload-btn uploadTriggerbank" data-index="${index}">📤 Upload File</button>
                                                    </div>
                                                    <p class="upload-hint">You can upload a maximum of 5 files, 10MB each</p>
                                                    <!-- Display uploaded file names -->
                                                    <ul class="file-list fileListbank" data-index="${index}">${fileListHtml}</ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`);

                        $('#bank-container').append(bankSection);
                    });
                } else {
                    // Edit mode but no bank details yet — add one empty section so user can enter bank details
                    const emptyBankSection = $(`
                        <div class="bank-section col-12 col-md-6" data-index="0">
                            <div class="form-group row align-items-center mb-3">
                                <label class="col-md-4">Account Holder Name</label>
                                <div class="col-md-8">
                                    <input type="hidden" name="bank_details[0][id]" value="">
                                    <input type="text" name="bank_details[0][account_holder_name]" value="" autocomplete="off" autocorrect="off">
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-3">
                                <label class="col-md-4">Bank Name</label>
                                <div class="col-md-8">
                                    <input type="text" name="bank_details[0][bank_name]" value="" autocomplete="off" autocorrect="off">
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-3">
                                <label class="col-md-4">Account Number<span style="color:red;">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="bank_details[0][account_number]" value="" autocomplete="off" autocorrect="off">
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-3">
                                <label class="col-md-4">Re-enter Account Number<span style="color:red;">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="bank_details[0][re_account_number]" value="" autocomplete="off" autocorrect="off">
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-3">
                                <label class="col-md-4">IFSC<span style="color:red;">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="bank_details[0][ifsc]" value="" autocomplete="off" autocorrect="off">
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-3">
                                <label class="col-md-4">Upload Documents<span style="color:red;">*</span></label>
                                <div class="form-group col-md-8">
                                    <div class="upload-section">
                                        <label class="upload-title">Attach File(s) to Bill</label>
                                        <input type="file" class="fileInputbank" data-index="0" name="bank_details[0]bank_uploads_0[]" multiple style="display: none;" />
                                        <input type="hidden" name="bank_details[0][existing_files]" value="[]">
                                        <div class="upload-box">
                                            <button type="button" class="upload-btn uploadTriggerbank" data-index="0">📤 Upload File</button>
                                        </div>
                                        <p class="upload-hint">You can upload a maximum of 5 files, 10MB each</p>
                                        <ul class="file-list fileListbank" data-index="0"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    $('#bank-container').append(emptyBankSection);
                }

                // Trigger file input when Upload button is clicked
                $(document).on('click', '.uploadTriggerbank', function () {
                    const index = $(this).data('index');
                    $(`.fileInputbank[data-index="${index}"]`).click();
                });

                // Handle file selection
                $(document).on('change', '.fileInputbank', function () {
                    const index = $(this).data('index');
                    const fileListEl = $(`.fileListbank[data-index="${index}"]`);

                    // Initialize if not exists
                    if (!selectedFilesMap[index]) {
                        selectedFilesMap[index] = [];
                    }

                    // Add new files
                    const newFiles = Array.from(this.files);
                    selectedFilesMap[index] = selectedFilesMap[index].concat(newFiles);

                    // Display all files (existing + new)
                    renderFileList(index);
                });

                // Remove an existing file
                $(document).on('click', '.remove-existing-file', function () {
                    const sectionIndex = $(this).data('index');
                    const fileIndex = $(this).data('file-index');

                    // Remove from existing files
                    existingFilesMap[sectionIndex].splice(fileIndex, 1);

                    // Update the hidden input with remaining existing files
                    $(`input[name="bank_details[${sectionIndex}][existing_files]"]`)
                        .val(JSON.stringify(existingFilesMap[sectionIndex]));

                    renderFileList(sectionIndex);
                });

                // Remove a newly added file
                $(document).on('click', '.remove-new-file', function () {
                    const sectionIndex = $(this).data('index');
                    const fileIndex = $(this).data('file-index');

                    // Remove from selected files
                    selectedFilesMap[sectionIndex].splice(fileIndex, 1);

                    // Update file input
                    const dt = new DataTransfer();
                    selectedFilesMap[sectionIndex].forEach(file => dt.items.add(file));
                    $(`.fileInputbank[data-index="${sectionIndex}"]`)[0].files = dt.files;

                    renderFileList(sectionIndex);
                });

                function renderFileList(index) {
                    const fileListEl = $(`.fileListbank[data-index="${index}"]`);
                    fileListEl.empty();

                    // Add existing files (from DB)
                    if (existingFilesMap[index]) {
                        existingFilesMap[index].forEach((file, i) => {
                            const li = $(`
                                <li class="existing-file">
                                    <span class="file-preview" data-type="existing" data-index="${index}" data-file-index="${i}" style="cursor:pointer; color:blue; text-decoration:underline;">
                                        ${file}
                                    </span>
                                    <span class="remove-existing-file" data-index="${index}" data-file-index="${i}">❌</span>
                                </li>
                            `);
                            fileListEl.append(li);
                        });
                    }

                    // Add new files (uploaded now)
                    if (selectedFilesMap[index]) {
                        selectedFilesMap[index].forEach((file, i) => {
                            const li = $(`
                                <li class="new-file">
                                    <span class="file-preview" data-type="new" data-index="${index}" data-file-index="${i}" style="cursor:pointer; color:blue;">
                                        ${file.name}
                                    </span>
                                    <span class="remove-new-file" data-index="${index}" data-file-index="${i}">❌</span>
                                </li>
                            `);
                            fileListEl.append(li);
                        });
                    }
                }
                // Handle preview click (both existing + new files)
                $(document).on('click', '.file-preview', function () {
                    const sectionIndex = $(this).data('index');
                    const fileIndex = $(this).data('file-index');
                    const fileType = $(this).data('type');

                    if (fileType === "existing") {
                        // For existing files (already stored in DB)
                        const fileUrl = existingFilesMap[sectionIndex][fileIndex];

                        // Just set into <embed> (assuming they are stored URLs)
                        $('#pdfmain').replaceWith(`<embed id="pdfmain" src="${fileUrl}" width="100%" height="600px" />`);

                        const modal = new bootstrap.Modal(document.getElementById('documentModal1'));
                        modal.show();

                    } else {
                        // For newly uploaded files (local File object)
                        const file = selectedFilesMap[sectionIndex][fileIndex];
                        if (!file) return;

                        const reader = new FileReader();

                        reader.onload = function (e) {
                            const fileURL = e.target.result;
                            let previewHTML = '';

                            if (file.type.startsWith("image/")) {
                                previewHTML = `<img src="${fileURL}" class="img-fluid rounded" style="max-height:600px;">`;
                                $('#pdfmain').replaceWith(`<div id="pdfmain" class="text-center">${previewHTML}</div>`);
                            } else if (file.type === "application/pdf") {
                                $('#pdfmain').replaceWith(`<embed id="pdfmain" src="${fileURL}" width="100%" height="600px" />`);
                            } else if (file.type.startsWith("text/")) {
                                previewHTML = `<pre style="max-height:600px; overflow:auto; text-align:left;">${e.target.result}</pre>`;
                                $('#pdfmain').replaceWith(`<div id="pdfmain">${previewHTML}</div>`);
                            } else {
                                $('#pdfmain').replaceWith(`<div id="pdfmain"><p class="text-muted">Preview not supported for this file type.</p></div>`);
                            }

                            const modal = new bootstrap.Modal(document.getElementById('documentModal1'));
                            modal.show();
                        };

                        // Read file content depending on type
                        if (file.type.startsWith("text/")) {
                            reader.readAsText(file);
                        } else {
                            reader.readAsDataURL(file);
                        }
                    }
                });


            });
            // $(document).ready(function () {
            //      const selectedFilesMap = {};
            //     if (existingbankdetails.length > 0) {
            //         $('.bank-section').remove(); // remove default one
            //         console.log("existingbankdetails", existingbankdetails);

            //         existingbankdetails.forEach((bank, index) => {
            //             const fileArray = bank.bank_uploads ? JSON.parse(bank.bank_uploads) : [];
            //             console.log("fileArray",fileArray);

            //             // Create the file list HTML for existing files
            //             let fileListHtml = '';
            //             fileArray.forEach((file, i) => {
            //                 fileListHtml += `
            //                 <li>
            //                     ${file}
            //                     <span class="remove-file-bank" data-index="${index}" data-file-index="${i}">❌</span>
            //                 </li>
            //                 `;
            //             });

            //             const bankSection = $(`
            //             <div class="bank-section col-12 col-md-6" data-index="${index}">
            //                 <div class="form-group row align-items-center mb-3">
            //                     <label class="col-md-4">Account Holder Name</label>
            //                     <div class="col-md-8">
            //                         <input type="hidden" name="bank_details[${index}][id]" value="${bank.id || ''}">
            //                         <input type="text" name="bank_details[${index}][account_holder_name]" value="${bank.account_holder_name || ''}">
            //                     </div>
            //                 </div>
            //                 <div class="form-group row align-items-center mb-3">
            //                     <label class="col-md-4">Bank Name</label>
            //                     <div class="col-md-8">
            //                         <input type="text" name="bank_details[${index}][bank_name]" value="${bank.bank_name || ''}">
            //                     </div>
            //                 </div>
            //                 <div class="form-group row align-items-center mb-3">
            //                     <label class="col-md-4">Account Number<span style="color:red;">*</span></label>
            //                     <div class="col-md-8">
            //                         <input type="text" name="bank_details[${index}][account_number]" value="${bank.accont_number || ''}">
            //                     </div>
            //                 </div>
            //                 <div class="form-group row align-items-center mb-3">
            //                     <label class="col-md-4">Re-enter Account Number<span style="color:red;">*</span></label>
            //                     <div class="col-md-8">
            //                         <input type="text" name="bank_details[${index}][re_account_number]" value="${bank.accont_number || ''}">
            //                     </div>
            //                 </div>
            //                 <div class="form-group row align-items-center mb-3">
            //                     <label class="col-md-4">IFSC<span style="color:red;">*</span></label>
            //                     <div class="col-md-8">
            //                         <input type="text" name="bank_details[${index}][ifsc]" value="${bank.ifsc_code || ''}">
            //                     </div>
            //                 </div>
            //                 <div class="form-group row align-items-center mb-3">
            //                     <label class="col-md-4">Upload Documents<span style="color:red;">*</span></label>
            //                     <div class="form-group col-md-8">
            //                         <div class="upload-section">
            //                             <label class="upload-title">Attach File(s) to Bill</label>
            //                             <!-- Hidden file input -->
            //                             <input type="file" class="fileInputbank" data-index="${index}" name="bank_details[${index}]bank_uploads_${index}[]" multiple style="display: none;" />
            //                             <input type="hidden" name="bank_details[${index}][existing_files]" value='${JSON.stringify(fileArray)}'>
            //                             <!-- Upload buttons -->
            //                             <div class="upload-box">
            //                             <button type="button" class="upload-btn uploadTriggerbank" data-index="${index}">📤 Upload File</button>
            //                             </div>
            //                             <p class="upload-hint">You can upload a maximum of 5 files, 10MB each</p>
            //                             <!-- Display uploaded file names -->
            //                             <ul class="file-list fileListbank" data-index="${index}">${fileListHtml}</ul>
            //                         </div>
            //                     </div>
            //                 </div>
            //             </div>`);

            //             $('#bank-container').append(bankSection);

            //             // Store the existing files in the selectedFilesMap
            //             if (fileArray.length > 0) {
            //                 // Convert the file info objects to File objects if needed
            //                 // Note: In a real edit scenario, you might need to handle this differently
            //                 // since you can't recreate File objects from just the names
            //                 selectedFilesMap[index] = fileArray;
            //             }
            //         });
            //     }
            //      // Store file selections by index


            //     // Trigger file input when Upload button is clicked
            //     $(document).on('click', '.uploadTriggerbank', function () {
            //     const index = $(this).data('index');
            //     $(`.fileInputbank[data-index="${index}"]`).click();
            //     });

            //     // Handle file selection
            //     $(document).on('change', '.fileInputbank', function () {
            //     const index = $(this).data('index');
            //     const fileListEl = $(`.fileListbank[data-index="${index}"]`);
            //     fileListEl.empty(); // clear current list

            //     const files = Array.from(this.files);
            //     selectedFilesMap[index] = files;

            //     $.each(files, function (i, file) {
            //         const li = $(`
            //         <li>
            //             ${file.name}
            //             <span class="remove-file-bank" data-index="${index}" data-file-index="${i}">❌</span>
            //         </li>
            //         `);
            //         fileListEl.append(li);
            //     });
            //     });

            //     // Remove a file
            //     $(document).on('click', '.remove-file-bank', function () {
            //         const sectionIndex = $(this).data('index');
            //         const fileIndex = $(this).data('file-index');

            //         // ✅ Ensure the array exists first
            //         if (!Array.isArray(selectedFilesMap[sectionIndex])) {
            //             return;
            //         }

            //         selectedFilesMap[sectionIndex].splice(fileIndex, 1);

            //         // Rebuild DataTransfer object
            //         const dt = new DataTransfer();
            //         selectedFilesMap[sectionIndex].forEach(file => dt.items.add(file));
            //         $(`.fileInputbank[data-index="${sectionIndex}"]`)[0].files = dt.files;

            //         // Re-render the file list
            //         const fileListEl = $(`.fileListbank[data-index="${sectionIndex}"]`);
            //         fileListEl.empty();
            //         selectedFilesMap[sectionIndex].forEach((file, i) => {
            //             const li = $(`
            //                 <li>
            //                     ${file.name}
            //                     <span class="remove-file-bank" data-index="${sectionIndex}" data-file-index="${i}">❌</span>
            //                 </li>
            //             `);
            //             fileListEl.append(li);
            //         });
            //     });

            // });


        $(document).ready(function() {
            // Hide additional details container initially
            $('#additional-details-container').hide();
            $('.remove-additional-details').hide();

            // Tab switching functionality
            $('.close').on('click', function() {
                window.location.href = "{{ route('superadmin.getvendor') }}";
            });

            $('.form-tab').on('click', function() {
                $('.form-tab').removeClass('active');
                $(this).addClass('active');
                const tabId = $(this).data('tab');
                $('.tab-content').removeClass('active');
                $('#' + tabId).addClass('active');
            });

          // Copy billing address to shipping address
            $('#copy-billing').on('click', function() {
                // Copy each field individually to ensure proper mapping
                $('[name="billing_attention"]').val() ? $('[name="shipping_attention"]').val($('[name="billing_attention"]').val()) : '';
                $('[name="billing_country"]').val() ? $('[name="shipping_country"]').val($('[name="billing_country"]').val()) : '';
                $('[name="billing_address"]').val() ? $('[name="shipping_address"]').val($('[name="billing_address"]').val()) : '';
                $('[name="billing_city"]').val() ? $('[name="shipping_city"]').val($('[name="billing_city"]').val()) : '';
                $('[name="billing_state"]').val() ? $('[name="shipping_state"]').val($('[name="billing_state"]').val()) : '';
                $('[name="billing_zip_code"]').val() ? $('[name="shipping_zip_code"]').val($('[name="billing_zip_code"]').val()) : '';
                $('[name="billing_phone"]').val() ? $('[name="shipping_phone"]').val($('[name="billing_phone"]').val()) : '';
                $('[name="billing_fax"]').val() ? $('[name="shipping_fax"]').val($('[name="billing_fax"]').val()) : '';

                // Visual feedback
                const $btn = $(this);
                $btn.html('<i class="fas fa-check"></i> Copied!');
                $btn.addClass('copied');

                setTimeout(() => {
                    $btn.html('<i class="fas fa-copy"></i> Copy from Billing');
                    $btn.removeClass('copied');
                }, 2000);
            });

            // Add contact person functionality
            $(document).on('click', '#add-contact-person', function() {
                // Add header row if it doesn't exist

                if ($('#contact-persons-header').length === 0) {
                    const headerRow = $(`
                        <div class="row contact-person-header" id="contact-persons-header">
                            <div class="col-md-2 col-sm-2">
                                <div class="form-group">
                                    <label class="customer-form-label">Salutation</label>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">First Name</label>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">Last Name</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">Email Address</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">Work Phone</label>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">Mobile</label>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">Action</label>
                                </div>
                            </div>
                        </div>
                    `);
                    $('#contact-persons-container').prepend(headerRow);
                }

                // Generate a unique index for the contact person
                const contactIndex = $('.contact-person-row').length;

                // Add new contact person row
                const contactRow = $(`
                    <div class="row contact-person-row">
                        <div class="col-md-1 col-sm-1">
                            <div class="form-group">
                                <select class="form-select" name="contact_persons[${contactIndex}][salutation]">
                                    <option value="Mr.">Mr.</option>
                                    <option value="Mrs.">Mrs.</option>
                                    <option value="Ms.">Ms.</option>
                                    <option value="Dr.">Dr.</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="First Name" name="contact_persons[${contactIndex}][first_name]">
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Last Name" name="contact_persons[${contactIndex}][last_name]">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-1">
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Email Address" name="contact_persons[${contactIndex}][email]">
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <div class="form-group">
                                <input type="tel" class="form-control" placeholder="Work Phone" name="contact_persons[${contactIndex}][work_phone]">
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <div class="form-group">
                                <input type="tel" class="form-control" placeholder="Mobile" name="contact_persons[${contactIndex}][mobile]">
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <div class="remove-contact text-center">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    </div>
                `);

                contactRow.find('.remove-contact').on('click', function() {
                    $(this).closest('.contact-person-row').remove();
                    // Remove header if no more contact rows exist
                    if ($('.contact-person-row').length === 0) {
                        $('#contact-persons-header').remove();
                    }
                });

                $('#contact-persons-container').append(contactRow);
            });
            $(document).on('click', '.remove-contact', function () {
                $(this).closest('.contact-person-row').remove();

                // Remove header if no more rows
                if ($('.contact-person-row').length === 0) {
                    $('#contact-persons-header').remove();
                }
            });

            $('#file-upload-container').on('click', function(e) {
                // Prevent triggering when clicking on child elements (like the button)
                if (e.target === this) {
                    $('#file-upload-input').click();
                }
            });

            // Click handler for the upload button
            $('#upload-button').on('click', function(e) {
                e.stopPropagation(); // Prevent triggering the container click
                $('#file-upload-input').click();
            });

            // Handle file selection
            $('#file-upload-input').on('change', function() {
                const files = this.files;
                const fileList = $('#file-list');
                fileList.empty();

                if (files.length > 10) {
                    alert('You can upload a maximum of 10 files');
                    $(this).val('');
                    return;
                }

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];

                    // Check file size (10MB limit)
                    if (file.size > 10 * 1024 * 1024) {
                        alert(`File "${file.name}" exceeds the 10MB limit`);
                        continue;
                    }

                    // Check file type
                    const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        alert(`File "${file.name}" is not a valid type (PDF, JPG, PNG, GIF)`);
                        continue;
                    }
                    // Display file info
                        const fileItem = $(`
                            <div class="file-item d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <div class="file-preview" data-index="${i}" style="cursor:pointer;">
                                    <i class="fas ${file.type === 'application/pdf' ? 'fa-file-pdf text-danger' : 'fa-file-image text-primary'} me-2"></i>
                                    <span>${file.name}</span>
                                    <small class="text-muted ms-2">(${(file.size / (1024 * 1024)).toFixed(2)} MB)</small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger remove-file" data-index="${i}" type="button">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `);


                    fileList.append(fileItem);
                }

                // Remove file handler
                $('.remove-file').on('click', function(e) {
                    e.stopPropagation();
                    const index = $(this).data('index');
                    const files = $('#file-upload-input')[0].files;
                    const newFiles = Array.from(files).filter((_, i) => i !== index);

                    // Create new FileList (since we can't modify the original)
                    const dataTransfer = new DataTransfer();
                    newFiles.forEach(file => dataTransfer.items.add(file));
                    $('#file-upload-input')[0].files = dataTransfer.files;

                    $(this).parent().remove();
                });
                // Preview handler
                $('#file-list').on('click', '.file-preview', function () {
                    const index = $(this).data('index');
                    const file = $('#file-upload-input')[0].files[index];
                    if (!file) return;

                    const reader = new FileReader();

                    reader.onload = function (e) {
                        const fileURL = e.target.result;
                        let previewHTML = '';

                        if (file.type.startsWith("image/")) {
                            previewHTML = `<img src="${fileURL}" class="img-fluid rounded" style="max-height:600px;">`;
                            $('#pdfmain').replaceWith(`<div id="pdfmain" class="text-center">${previewHTML}</div>`);
                        } else if (file.type === "application/pdf") {
                            $('#pdfmain').replaceWith(`<embed id="pdfmain" src="${fileURL}" width="100%" height="600px" />`);
                        } else {
                            previewHTML = `<p class="text-muted">Preview not supported for this file type.</p>`;
                            $('#pdfmain').replaceWith(`<div id="pdfmain">${previewHTML}</div>`);
                        }

                        const modal = new bootstrap.Modal(document.getElementById('documentModal1'));
                        modal.show();
                    };

                    reader.readAsDataURL(file);
                });

            });
            // $('#uploadTrigger').on('click', function () {
            //     $('#fileInput').click();
            //   });

            //   // On file input change
            //   $('#fileInput').on('change', function () {
            //     const fileList = $('#fileList');

            //     const files = Array.from(this.files);

            //     // Store selected files temporarily
            //     window.selectedFiles = files;

            //     $.each(files, function (index, file) {
            //       const li = $(`
            //         <li>
            //           ${file.name}
            //           <span class="remove-file" data-index="${index}">❌</span>
            //         </li>
            //       `);
            //       fileList.append(li);
            //     });
            //   });

            //   // Remove file from list visually
            //   $('#fileList').on('click', '.remove-file', function () {
            //     const index = $(this).data('index');
            //     window.selectedFiles.splice(index, 1);

            //     // Rebuild the list after removal
            //     $('#fileInput').val('');
            //     const newFileList = window.selectedFiles;
            //     $('#fileList').empty();

            //     $.each(newFileList, function (i, file) {
            //       const li = $(`
            //         <li>
            //           ${file.name}
            //           <span class="remove-file" data-index="${i}">❌</span>
            //         </li>
            //       `);
            //       $('#fileList').append(li);
            //     });
            //   });
            $('#uploadTrigger').on('click', function () {
                  $('#fileInput').click();
                });

                // On file input change
                $('#fileInput').on('change', function () {
                  const fileList = $('#fileList');
                  fileList.empty(); // Clear previous entries

                  const files = Array.from(this.files);

                  // Store selected files temporarily
                  window.selectedFiles = files;

                  $.each(files, function (index, file) {
                    const li = $(`
                      <li>
                        <span class="file-name" data-index="${index}" style="cursor:pointer; color:blue;">
                          ${file.name}
                        </span>
                        <span class="remove-file" data-index="${index}"> ❌</span>
                      </li>
                    `);
                    fileList.append(li);
                  });
                });

                // Remove file from list visually
                $('#fileList').on('click', '.remove-file', function () {
                  const index = $(this).data('index');
                  window.selectedFiles.splice(index, 1);

                  // Rebuild the list after removal
                  $('#fileInput').val('');
                  const newFileList = window.selectedFiles;
                  $('#fileList').empty();

                  $.each(newFileList, function (i, file) {
                    const li = $(`
                      <li>
                        <span class="file-name" data-index="${i}" style="cursor:pointer; color:blue; text-decoration:underline;">
                          ${file.name}
                        </span>
                        <span class="remove-file" data-index="${i}"> ❌</span>
                      </li>
                    `);
                    $('#fileList').append(li);
                  });
                });
                // When clicking file name -> open your modal and preview
                $('#fileList').on('click', '.file-name', function () {
                  const index = $(this).data('index');
                  const file = window.selectedFiles[index];
                  if (!file) return;

                  const reader = new FileReader();

                  reader.onload = function (e) {
                    const fileURL = e.target.result;
                    let previewHTML = '';

                    if (file.type.startsWith("image/")) {
                      // Show image in right panel
                      previewHTML = `<img src="${fileURL}" class="img-fluid rounded" style="max-height:600px;">`;
                      $('#pdfmain').replaceWith(`<div id="pdfmain" class="text-center">${previewHTML}</div>`);
                    } else if (file.type === "application/pdf") {
                      // Show PDF inside embed
                      $('#pdfmain').replaceWith(`<embed id="pdfmain" src="${fileURL}" width="100%" height="600px" />`);
                    } else if (file.type.startsWith("text/")) {
                      // Show text inside <pre>
                      previewHTML = `<pre style="max-height:600px; overflow:auto; text-align:left;">${e.target.result}</pre>`;
                      $('#pdfmain').replaceWith(`<div id="pdfmain">${previewHTML}</div>`);
                    } else {
                      $('#pdfmain').replaceWith(`<div id="pdfmain"><p class="text-muted">Preview not supported for this file type.</p></div>`);
                    }

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('documentModal1'));
                    modal.show();
                  };

                  // Use correct read mode
                  if (file.type.startsWith("text/")) {
                    reader.readAsText(file);
                  } else {
                    reader.readAsDataURL(file);
                  }
                });


            $('#add-more-details').on('click', function() {
                $('#additional-details-container').show();
                // Disable the add button after adding once
                $(this).hide();
                $('.remove-additional-details').show();
            });

            // Remove additional details
            $(document).on('click', '.remove-additional-details', function() {
                $('#additional-details-container').hide();
                $(this).hide();
                $('#add-more-details').show();
            });


            // Handle Save button click
            $('#save-customer').on('click', function() {
                // Create FormData object to handle file uploads
                const formData = new FormData();
                let isValid = true;

                    if ($('#first_name').val() === "") {
                        $('.error_first_name').text('Name Required');
                        isValid = false;
                    }
                    if ($('#display_name').val() === "") {
                        $('.error_display_name').text('Enter the Display Name');
                        isValid = false;
                    }
                    // let email = $('#email').val().trim();
                    // let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    // if (email === "") {
                    //     $('.error_email').text('Enter Your Email');
                    //     isValid = false;
                    // } else if (!emailPattern.test(email)) {
                    //     $('.error_email').text('Enter a valid Email Address');
                    //     isValid = false;
                    // } else {
                    //     $('.error_email').text('');
                    // }

                    // Phone number validation
                    // let phone = $('#phone_number').val().trim();
                    // let phonePattern = /^[6-9]\d{10}$/;

                    // if (phone === "") {
                    //     $('.error_phone_number').text('Enter the Mobile Number');
                    //     isValid = false;
                    // } else if (!phonePattern.test(phone)) {
                    //     $('.error_phone_number').text('Enter a valid 10-digit Mobile Number');
                    //     isValid = false;
                    // } else {
                    //     $('.error_phone_number').text('');
                    // }


                    // if ($('#pan_number').val() === "") {
                    //     $('.error_pan_number').text('Enter Pan Number');
                    //     isValid = false;
                    // }

                    // let panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
                    // let panNumber = $('#pan_number').val().toUpperCase();
                    // if (!panPattern.test(panNumber)) {
                    //     $('.error_pan_number').text('Invalid PAN Number');
                    //     isValid = false;
                    // } else {
                    //     $('.error_pan_number').text('');
                    // }

                    // if (!isValid) {
                    //     return;
                    // }

                // Append all form data
                $('input, select, textarea').each(function() {
                    if ($(this).attr('type') !== 'file' && $(this).attr('type') !== 'button' && $(this).attr('type') !== 'submit') {
                        const name = $(this).attr('name');
                        const value = $(this).val();

                        // Skip _method field to prevent PUT/PATCH method errors
                        if (name && name !== '_method') {
                            // Handle checkboxes
                            if ($(this).attr('type') === 'checkbox' ||$(this).attr('type') === 'radio') {
                                formData.append(name, $(this).is(':checked') ? '1' : '0');
                            } else {
                                formData.append(name, value);
                            }
                        }
                    }
                });

                // Append files
                const fileInput = $('#file-upload-input')[0];
                if (fileInput.files.length > 0) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        formData.append('documents[]', fileInput.files[i]);
                    }
                }
                const fileInputpan = $('#fileInput')[0];
                if (fileInputpan.files.length > 0) {
                    for (let i = 0; i < fileInputpan.files.length; i++) {
                        formData.append('pan_upload[]', fileInputpan.files[i]);
                    }
                }
                $('.fileInputbank').each(function () {
                    const index = $(this).data('index'); // e.g., 0, 1, 2...
                    const input = this;

                    if (input.files.length > 0) {
                        for (let i = 0; i < input.files.length; i++) {
                            formData.append(`bank_details[${index}][bank_uploads][]`, input.files[i]);
                        }
                    }
                });
                formData.append('tds_tax_name', $('.tax-search-input').val());
                // Show loading indicator
                const saveBtn = $(this);
                saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                // Send data to server
                $.ajax({
                    url: '{{ route("superadmin.savevendor") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                         toastr.success(response.message);
                        // Handle success
                        saveBtn.html('<i class="fas fa-check"></i> Saved!').removeClass('btn-success').addClass('btn-primary');
                        setTimeout(() => {
                            window.location.href = "{{ route('superadmin.getvendor') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        // Handle error
                        saveBtn.prop('disabled', false).html('Save');
                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMessage = xhr.responseText;
                        }
                        alert(errorMessage);
                    }
                });
            });
        });
        $(document).ready(function () {
            if (isEditMode) {
                // Edit mode — show only the existing contact rows (already in your existing code)
                if (existingContacts.length > 0) {
                    if ($('#contact-persons-header').length === 0) {
                        const headerRow = $(`
                                        <div class="row contact-person-header" id="contact-persons-header">
                                            <div class="col-md-2 col-sm-2"><label class="customer-form-label">Salutation</label></div>
                                            <div class="col-md-1 col-sm-1"><label class="customer-form-label">First Name</label></div>
                                            <div class="col-md-1 col-sm-1"><label class="customer-form-label">Last Name</label></div>
                                            <div class="col-md-2 col-sm-1"><label class="customer-form-label">Email Address</label></div>
                                            <div class="col-md-2 col-sm-1"><label class="customer-form-label">Work Phone</label></div>
                                            <div class="col-md-1 col-sm-1"><label class="customer-form-label">Mobile</label></div>
                                            <div class="col-md-1 col-sm-1"><label class="customer-form-label">Action</label></div>
                                        </div>
                                    `);
                        $('#contact-persons-container').prepend(headerRow);
                    }

                    existingContacts.forEach((contact, index) => {
                        const contactRow = $(`
                                        <div class="row contact-person-row">
                                            <div class="col-md-1 col-sm-1">
                                                <div class="form-group">
                                                    <input type="hidden" class="form-control"  name="contact_persons[${index}][id]" value="${contact.id}">
                                                    <select class="form-select" name="contact_persons[${index}][salutation]">
                                                        <option value="Mr." ${contact.salutation === 'Mr.' ? 'selected' : ''}>Mr.</option>
                                                        <option value="Mrs." ${contact.salutation === 'Mrs.' ? 'selected' : ''}>Mrs.</option>
                                                        <option value="Ms." ${contact.salutation === 'Ms.' ? 'selected' : ''}>Ms.</option>
                                                        <option value="Dr." ${contact.salutation === 'Dr.' ? 'selected' : ''}>Dr.</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" placeholder="First Name" name="contact_persons[${index}][first_name]" value="${contact.first_name}">
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" placeholder="Last Name" name="contact_persons[${index}][last_name]" value="${contact.last_name}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-1">
                                                <div class="form-group">
                                                    <input type="email" class="form-control" placeholder="Email Address" name="contact_persons[${index}][email]" value="${contact.email}">
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <div class="form-group">
                                                    <input type="tel" class="form-control" placeholder="Work Phone" name="contact_persons[${index}][work_phone]" value="${contact.work_phone}">
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <div class="form-group">
                                                    <input type="tel" class="form-control" placeholder="Mobile" name="contact_persons[${index}][mobile]" value="${contact.mobile}">
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <div class="remove-contact text-center">
                                                    <i class="fas fa-times"></i>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                        $('#contact-persons-container').append(contactRow);
                    });
                }
            } else {
                // Create mode — add one empty row
                $('#add-contact-person').trigger('click');
            }
          });

          function addBankSection() {
                const container = document.getElementById('bank-container');
                const currentCount = container.querySelectorAll('.bank-section').length;
                const first = container.querySelector('.bank-section');
                const newSection = first.cloneNode(true);

                // Update data-index
                newSection.dataset.index = currentCount;

                // Clear all inputs and update names
                newSection.querySelectorAll('input').forEach(inp => {
                    if (inp.type === 'file') {
                    inp.value = '';
                    inp.setAttribute('data-index', currentCount);
                    inp.setAttribute('name', `bank_uploads_${currentCount}[]`);
                    } else {
                    inp.value = '';
                    const oldName = inp.getAttribute('name');
                    if (oldName) {
                        const newName = oldName.replace(/\[\d+\]/, `[${currentCount}]`);
                        inp.setAttribute('name', newName);
                    }
                    }
                });

                // Update upload trigger and file list attributes
                const uploadBtn = newSection.querySelector('.uploadTriggerbank');
                if (uploadBtn) {
                    uploadBtn.setAttribute('data-index', currentCount);
                }

                const fileList = newSection.querySelector('.fileListbank');
                if (fileList) {
                    fileList.setAttribute('data-index', currentCount);
                    fileList.innerHTML = ''; // clear any previous preview
                }

                // Remove existing remove button if any
                const existingRemove = newSection.querySelector('.remove-btn');
                if (existingRemove) existingRemove.remove();

                // Add a new remove button
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger mt-2 remove-btn';
                removeBtn.innerText = 'Remove';
                removeBtn.addEventListener('click', () => newSection.remove());
                newSection.appendChild(removeBtn);

                // Append the updated section
                container.appendChild(newSection);
                }


            $(document).ready(function () {
                const Tbltdstaxs = @json($Tbltdstax);
                const Tbltdssection = @json($Tbltdssection);
                const TblVendortype = @json($TblVendortype);
                Tbltdstaxs.data.forEach(Tbltdstax => {
                        const item = $(`
                            <div data-value="${Tbltdstax.tax_rate}" data-id="${Tbltdstax.id}">${Tbltdstax.tax_name}  [${Tbltdstax.tax_rate}%]</div>
                        `);
                        $('.tax-list').append(item);
                    });
                    Tbltdssection.data.forEach(Tbltdssection => {
                        const item = $(`
                        <div data-value="${Tbltdssection.name}" data-id="${Tbltdssection.id}">${Tbltdssection.name}</div>
                        `);
                        $('.tds-section-list').append(item);
                    });
                    TblVendortype.forEach(TblVendortype => {
                            const item = $(`
                                <div data-value="${TblVendortype.name}" data-id="${TblVendortype.id}">${TblVendortype.name}</div>
                            `);
                            $('.vendor-list').append(item);
                        });
                    // tds dropdown
                $(document).on('click', '.tax-search-input', function (e) {
                    e.stopPropagation();
                    $(this).val('');
                    $('.dropdown-menu.tax-dropdown').hide();

                    const $input = $(this);
                    let $dropdown = $input.data('dropdown');

                    if (!$dropdown) {
                        // Clone so each row keeps its own dropdown
                        $dropdown = $input.siblings('.dropdown-menu').clone(true);
                        $('body').append($dropdown);
                        $input.data('dropdown', $dropdown);
                    }

                    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
                    $dropdown.data('row', $input.closest('tr'));

                    const offset = $input.offset();
                    $dropdown.css({
                        position: 'absolute',
                        top: offset.top + $input.outerHeight(),
                        left: offset.left,
                        width: $input.outerWidth(),
                        zIndex: 999
                    }).show();
                    $(this).removeAttr('readonly');
                });

                // Select GST item
                $(document).on('click', '.tax-list div', function () {
                    const selectedText = $(this).text().trim();
                    const selecteddata = $(this).data('value');
                    const selectedid = $(this).data('id');

                    console.log("selectedText",selectedText);
                    console.log("selecteddata",selecteddata);
                    console.log("selectedid",selectedid);


                    const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                    const wrapper = $dropdown.data('wrapper');
                    if (!wrapper) {
                        console.warn("Wrapper or row not found — GST selection failed.");
                        $dropdown.hide();
                        return;
                    }
                    wrapper.find('.tax-search-input').val(selectedText);
                    if (wrapper.hasClass('tds-tax-section')) {
                        wrapper.find('.selected-tds-tax').val(selecteddata).trigger('change');
                    }
                    wrapper.find('.tds-tax-id').val(selectedid);

                    $dropdown.hide();
                });
                // TDS Section Dropdown
                $(document).on('click', '.tax-section-input', function (e) {
                    e.stopPropagation();
                    $(this).val('');
                    const $input = $(this);
                    if ($input.hasClass('tds-locked')) {
                        e.preventDefault();
                        return; // don't open dropdown when locked
                      }
                    let $dropdown = $input.data('dropdown');

                    if (!$dropdown || !$dropdown.length) {
                        // Look for correct source dropdown (changed class name)
                        const $source = $input.siblings('.dropdown-menu.tax-dropdown');

                        if (!$source.length) {
                            console.warn("No .dropdown-menu.tax-dropdown found next to input");
                            return;
                        }

                        // Clone dropdown so each row has its own instance
                        $dropdown = $source.clone(true);
                        $dropdown.css("display", "block"); // make it visible

                        $('body').append($dropdown);
                        $input.data('dropdown', $dropdown);
                    }

                    // Attach reference to wrapper
                    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
                    $dropdown.data('row', $input.closest('tr'));

                    // Position dropdown under input
                    const offset = $input.offset();
                    $dropdown.css({
                        position: 'absolute',
                        top: offset.top + $input.outerHeight(),
                        left: offset.left,
                        width: $input.outerWidth(),
                        zIndex: 9999
                    }).show();
                    $(this).removeAttr('readonly');
                });

                // Select TDS Item
                $(document).on('click', '.tds-section-list div', function () {
                    const selectedText = $(this).text().trim();
                    const selectedId = $(this).data('id');

                    const $dropdown = $(this).closest('.dropdown-menu');
                    const wrapper = $dropdown.data('wrapper');

                    if (!wrapper) {
                        console.warn("Wrapper not found — TDS selection failed.");
                        $dropdown.hide();
                        return;
                    }

                    // Fill inputs
                    wrapper.find('.tax-section-input').val(selectedText);  // visible input
                    wrapper.find('.tds_section_id').val(selectedId);       // hidden input

                    $dropdown.hide();
                });

                $(document).on('click', '.vendor-search-input', function (e) {
                    e.stopPropagation();
                    $(this).val('');
                    $('.dropdown-menu.tax-dropdown').hide();

                    const $input = $(this);
                    let $dropdown = $input.data('dropdown');

                    if (!$dropdown) {
                        // Clone so each row keeps its own dropdown
                        $dropdown = $input.siblings('.dropdown-menu').clone(true);
                        $('body').append($dropdown);
                        $input.data('dropdown', $dropdown);
                    }

                    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
                    $dropdown.data('row', $input.closest('tr'));

                    const offset = $input.offset();
                    $dropdown.css({
                        position: 'absolute',
                        top: offset.top + $input.outerHeight(),
                        left: offset.left,
                        width: $input.outerWidth(),
                        zIndex: 999
                    }).show();
                    $(this).removeAttr('readonly');
                });

                // Select GST item
                $(document).on('click', '.vendor-list div', function () {
                    const selectedText = $(this).text().trim();
                    const selecteddata = $(this).data('value');
                    const selectedid = $(this).data('id');
                    console.log("selecteddata",selecteddata);

                    const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                    const wrapper = $dropdown.data('wrapper');
                    if (!wrapper) {
                        console.warn("Wrapper or row not found — GST selection failed.");
                        $dropdown.hide();
                        return;
                    }
                    wrapper.find('.vendor-search-input').val(selectedText);
                    if (wrapper.hasClass('tds-tax-section')) {
                        wrapper.find('.selected-tds-tax').val(selecteddata);
                    }
                    if (wrapper.hasClass('tds-tax-section')) {
                        wrapper.find('.vendor_type_name').val(selecteddata);
                    }
                    wrapper.find('.vendor_type_id').val(selectedid);

                    $dropdown.hide();
                });
            //    // TDS Section Dropdown
            //     $(document).on('click', '.tax-section-input', function () {
            //         const $input = $(this);
            //         let $dropdown = $input.data('dropdown');

            //         if (!$dropdown || !$dropdown.length) {
            //             // Look for correct source dropdown (changed class name)
            //             const $source = $input.siblings('.dropdown-menu.tax-dropdown');

            //             if (!$source.length) {
            //                 console.warn("No .dropdown-menu.tax-dropdown found next to input");
            //                 return;
            //             }

            //             // Clone dropdown so each row has its own instance
            //             $dropdown = $source.clone(true);
            //             $dropdown.css("display", "block"); // make it visible

            //             $('body').append($dropdown);
            //             $input.data('dropdown', $dropdown);
            //         }

            //         // Attach reference to wrapper
            //         $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
            //         $dropdown.data('row', $input.closest('tr'));

            //         // Position dropdown under input
            //         const offset = $input.offset();
            //         $dropdown.css({
            //             position: 'absolute',
            //             top: offset.top + $input.outerHeight(),
            //             left: offset.left,
            //             width: $input.outerWidth(),
            //             zIndex: 9999
            //         }).show();
            //     });

            //     // Select TDS Item
            //     $(document).on('click', '.tds-section-list div', function () {
            //         const selectedText = $(this).text().trim();
            //         const selectedId = $(this).data('id');

            //         const $dropdown = $(this).closest('.dropdown-menu');
            //         const wrapper = $dropdown.data('wrapper');

            //         if (!wrapper) {
            //             console.warn("Wrapper not found — TDS selection failed.");
            //             $dropdown.hide();
            //             return;
            //         }

            //         // Fill inputs
            //         wrapper.find('.tax-section-input').val(selectedText);  // visible input
            //         wrapper.find('.tds_section_id').val(selectedId);       // hidden input

            //         $dropdown.hide();
            //     });
                // Search filter
                  // Search filter
                  $('.tax-search-input').on('keyup', function () {
                    const searchText = $(this).val().toLowerCase();
                    const $dropdown = $(this).data('dropdown');

                    if ($dropdown) {
                      const list = $dropdown.find('.tax-list div');
                      list.each(function () {
                        const itemText = $(this).text().toLowerCase();
                        $(this).toggle(itemText.includes(searchText));
                      });
                    }
                  });
                  $('.tax-section-input').on('keyup', function () {
                    const searchText = $(this).val().toLowerCase();
                    const $dropdown = $(this).data('dropdown');

                    if ($dropdown) {
                      const list = $dropdown.find('.tds-section-list div');
                      list.each(function () {
                        const itemText = $(this).text().toLowerCase();
                        $(this).toggle(itemText.includes(searchText));
                      });
                    }
                  });
                  $('.vendor-search-input').on('keyup', function () {
                    const searchText = $(this).val().toLowerCase();
                    const $dropdown = $(this).data('dropdown');

                    if ($dropdown) {
                      const list = $dropdown.find('.vendor-list div');
                      list.each(function () {
                        const itemText = $(this).text().toLowerCase();
                        $(this).toggle(itemText.includes(searchText));
                      });
                    }
                  });


                 $(document).on('click', function (e) {
                    if (!$(e.target).closest('.tax-dropdown-wrapper').length) {
                      $('.dropdown-menu.tax-dropdown').hide();
                    }
                  });
                  $(document).on('click', '.dropdown-menu.tax-dropdown', function (e) {
                      e.stopPropagation();
                  });

                  $(document).on('click', '.manage-tds-link', function () {
                    $('#tdsModal').fadeIn();
                    $('body').addClass('no-scroll'); // Disable background scroll
                    $('.dropdown-menu.tax-dropdown').hide();
                  });
                  $(document).on('click', '.close-modal', function () {
                    $('#tdsModal').fadeOut();
                    $('body').removeClass('no-scroll'); // Re-enable scroll
                  });
                   $(document).on('click', '.btn-new-tds', function () {
                    $('#tdsModal').hide();
                    $('#newTdsModal').fadeIn();
                  });
                   // Close New TDS modal
                  $(document).on('click', '.close-new-modal', function () {
                    $('#newTdsModal').fadeOut();
                    $('#tdsModal').fadeIn(); // Go back to manage TDS
                  });
                  $(document).on('click', '.tds_save', function () {
                    const formData = new FormData();
                    formData.append('name', $('.tds_name').val());
                    formData.append('rate', $('.tds_rate').val());
                    formData.append('section_name', $('.tax-section-input').val());
                    formData.append('section_id', $('.tds_section_id').val());
                    formData.append('start_date', $('.tds_start_date').val());
                    formData.append('end_date', $('.tds_end_date').val());
                     $.ajax({
                        url: '{{ route("superadmin.gettdssave") }}', // Update with your actual endpoint
                        type: "POST",
                        data: formData,
                        processData: false, // Prevent processing of the data
                        contentType: false, // Prevent setting content-type header
                        success: function (response) {
                            if (response.success) {
                               $('#newTdsModal').fadeOut();
                                $('#tdsModal').fadeOut();
                                $('body').removeClass('no-scroll');

                              // Clear old list before appending fresh data
                              $('.tax-list').empty();

                              response.tdstax.data.forEach(Tbltdstax => {
                              const item = $(`
                                  <div data-value="${Tbltdstax.tax_rate}" data-id="${Tbltdstax.id}">${Tbltdstax.tax_name}  [${Tbltdstax.tax_rate}%]</div>
                              `);
                              $('.tax-list').append(item);
                          });

                              // Clear inputs
                              $('.tds_name').val('');
                              $('.tds_rate').val('');
                              $('.tds_start_date').val('');
                              $('.tds_end_date').val('');
                          }
                        },
                        error: function (error) {
                            if (error.responseJSON && error.responseJSON.errors) {
                                // Display validation errors
                                $.each(error.responseJSON.errors, function(key, value) {
                                    $('.error_' + key).text(value[0]);
                                });
                            } else {
                                console.error(error.responseJSON);
                            }
                        },
                    });
                  });
                  // tds section
                  $(document).on('click', '.manage-tds-section', function () {
                    $('#SectionModal').fadeIn();
                    $('body').addClass('no-scroll'); // Disable background scroll
                    $('.dropdown-menu.tax-dropdown').hide();
                  });
                  $(document).on('click', '.close-sec-modal', function () {
                    $('#SectionModal').fadeOut();
                    $('body').removeClass('no-scroll'); // Re-enable scroll
                  });
                   $(document).on('click', '.btn-new-section', function () {
                    $('#SectionModal').hide();
                    $('#newsectionModal').fadeIn();
                  });
                   // Close New TDS modal
                  $(document).on('click', '.close-section-modal', function () {
                    $('#newsectionModal').fadeOut();
                    $('#SectionModal').fadeIn(); // Go back to manage TDS
                  });
                  $(document).on('click', '.section_save', function (e) {
                    e.preventDefault();
                    const formData = new FormData();
                    formData.append('name', $('.section_name').val());
                    formData.append('id', $('.section_id').val());
                     $.ajax({
                        url: '{{ route("superadmin.gettdssectionsave") }}', // Update with your actual endpoint
                        type: "POST",
                        data: formData,
                        processData: false, // Prevent processing of the data
                        contentType: false, // Prevent setting content-type header
                        success: function (response) {
                            if (response.success) {
                               $('#newsectionModal').fadeOut();
                                $('#SectionModal').fadeOut();
                                $('body').removeClass('no-scroll');

                              // Clear old list before appending fresh data
                              $('.tds-section-list').empty();

                              response.Tbltdssection.data.forEach(Tbltdssection => {
                              const item = $(`
                                  <div data-value="${Tbltdssection.name}" data-id="${Tbltdssection.id}">${Tbltdssection.name}</div>
                              `);
                              $('.tds-section-list').append(item);
                          });
                              // Clear inputs
                              $('.section_name').val('');
                              $('.section_id').val('');
                          }
                        },
                        error: function (error) {
                            if (error.responseJSON && error.responseJSON.errors) {
                                // Display validation errors
                                $.each(error.responseJSON.errors, function(key, value) {
                                    $('.error_' + key).text(value[0]);
                                });
                            } else {
                                console.error(error.responseJSON);
                            }
                        },
                    });
                  });
                  $(document).on('click', '.edit-section', function () {
                    var row = $(this).closest('tr');
                    var section_name = row.find('.section_name_table').val();
                    var section_id = row.find('.section_id_table').val();
                    $('#newsectionModal').fadeIn();
                    $('.section_name').val(section_name);
                    $('.section_id').val(section_id);
                  });
            });


    </script>
    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>