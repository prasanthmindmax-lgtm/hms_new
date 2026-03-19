<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
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
        dd($customers);
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
                @if(!empty($customers))
                    @foreach ($customers as $customer)
                        <div class="row">
                            <div class="col-12">
                                <div class="customer-form-container">
                                    <h1 class="customer-form-header">New Customer</h1>
                                    <input type="hidden" class="customer-form-input" name="id" value={{$customer->id}}>

                                    {{-- <div class="customer-prefill-section">
                                        <input type="checkbox" class="customer-prefill-checkbox" id="customerPrefill" name="prefill_from_gst">
                                        <label for="customerPrefill" class="customer-prefill-label">Prefill Customer details from the GST portal using the Customer's GSTIN.</label>
                                        <a href="#" class="customer-prefill-button">Prefill ></a>
                                    </div> --}}

                                    <div class="customer-form-section">
                                        <div class="customer-section-title">Customer Type</div>
                                        <div class="customer-radio-group">
                                            <div class="customer-radio-option">
                                                <input type="radio" class="customer-radio-input" id="customerTypeBusiness" name="customer_type" value="business" checked>
                                                <label for="customerTypeBusiness">Business</label>
                                            </div>
                                            <div class="customer-radio-option">
                                                <input type="radio" class="customer-radio-input" id="customerTypeIndividual" name="customer_type" value="individual">
                                                <label for="customerTypeIndividual">Individual</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Primary Contact</div>
                                        <div class="col-md-3 col-sm-12">
                                            <div class="customer-form-group">
                                                <label class="customer-form-label">Salutation</label>
                                                <select class="customer-form-select" name="primary_contact_salutation" value={{$customer->customer_salutation}}>
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
                                                <input type="text" class="customer-form-input" id="first_name" name="primary_contact_first_name" value={{$customer->customer_first_name}}>
                                                <span class="error_first_name" style="color:red"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="customer-form-group">
                                                <label class="customer-form-label">Last Name</label>
                                                <input type="text" class="customer-form-input" name="primary_contact_last_name" value={{$customer->customer_last_name}}>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Company Name</div>
                                        <div class="col-12 col-md-6">
                                            <div class="customer-form-group">
                                                <input type="text" class="customer-display-name-input" placeholder="Select or type to add" name="company_name" value={{$customer->company_name}}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Display Name</div>
                                        <div class="col-12 col-md-6">
                                            <div class="customer-form-group">
                                                <input type="text" class="customer-form-input" id="display_name" name="display_name" value="{{ $customer->display_name }}">
                                                <span class="error_display_name" style="color:red"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Email Address</div>
                                        <div class="col-12 col-md-6">
                                            <div class="customer-form-group">
                                                <input type="email" class="customer-form-input" name="email" id="email" value={{$customer->email}}>
                                                <span class="error_email" style="color:red"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row customer-form-section">
                                        <div class="customer-section-title">Phone</div>
                                        <div class="col-md-4 col-sm-12 ">
                                            <div class="customer-form-group">
                                                <label class="customer-form-label">Work Phone</label>
                                                <input type="tel" class="customer-form-input" name="work_phone" value={{$customer->work_phone}}>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="customer-form-group">
                                                <label class="customer-form-label">Mobile</label>
                                                <input type="tel" class="customer-form-input" name="mobile" value={{$customer->mobile}}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-container">
                                    <div class="form-tabs">
                                        <div class="form-tab active" data-tab="other-details">Other Details</div>
                                        <div class="form-tab" data-tab="address">Address</div>
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
                                                    <input type="text" name="pan" id="pan_number" value={{$customer->pan_number}}>
                                                    <span class="error_pan_number" style="color:red"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Currency</span>
                                                </div>
                                                <div class="checkbox-item col-md-8">
                                                    <input type="checkbox" id="default-currency" name="default_currency" checked>
                                                    <label for="default-currency">INR - Indian Rupee</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row form-section ">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Opening Balance</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="text" placeholder="INR" name="opening_balance" value={{$customer->opening_balance}}>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row form-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Payment Terms</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <select name="payment_terms" value={{$customer->payment_terms}}>
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
                                                    <input type="checkbox" id="enable-portal" name="enable_portal">
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
                                                    <select name="portal_language" value={{$customer->portal_language}}>
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
                                                    <input type="url" placeholder="ex: www.zylker.com" name="website" value={{$customer->website}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Department</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="text" name="department" value={{$customer->website}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Designation</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="text" name="designation" value={{$customer->department}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Twitter</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="url" placeholder="http://www.twitter.com/" name="twitter" value={{$customer->twitter}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Skype Name/Number</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="text" name="skype" value={{$customer->skype}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-section additional-details-section">
                                            <div class="col-12 col-md-6 row align-items-center mb-3">
                                                <div class="section-title col-md-4">
                                                    <span>Facebook</span>
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <input type="url" placeholder="http://www.facebook.com/" name="facebook" value={{$customer->facebook}}>
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
                                                                    <input type="text" id="billing_attention" name="billing_attention" class="form-control custom-input" value={{$customer->billingAddress->attention}}>
                                                                </div>

                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Country/Region</label>
                                                            <div class="col-md-8">
                                                                <select class="billing-field" name="billing_country" value={{$customer->billingAddress->country}}>
                                                                    <option value="India">India</option>
                                                                    <option value="United States">United States</option>
                                                                    <option value="United Kingdom">United Kingdom</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">address</label>
                                                            <div class="col-md-8">
                                                                <textarea class="billing-field" name="billing_address">{{$customer->billingAddress->address}}</textarea>
                                                                {{-- <input type="text" class="billing-field" name="billing_address" value={{$customer->billingAddress->address}}> --}}
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">City</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="billing-field" name="billing_city" value={{$customer->billingAddress->city}}>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">State</label>
                                                            <div class="col-md-8">
                                                                <select class="billing-field" name="billing_state" value={{$customer->billingAddress->state}}>
                                                                    <option value="Maharashtra">Maharashtra</option>
                                                                    <option value="Delhi">Delhi</option>
                                                                    <option value="Karnataka">Karnataka</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Zip Code</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="billing-field" name="billing_zip_code" value={{$customer->billingAddress->zip_code}} >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">phone</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="billing-field" name="billing_phone" value={{$customer->billingAddress->phone}} >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Fax Number</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="billing-field" name="billing_fax" value={{$customer->billingAddress->fax}} >
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
                                                                <input type="text" class="shipping-field" name="shipping_attention" value={{$customer->shippingAddress->attention }}>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Country/Region</label>
                                                            <div class="col-md-8">
                                                                <select class="shipping-field" name="shipping_country" value={{$customer->shippingAddress->country}}>
                                                                    <option value="India">India</option>
                                                                    <option value="United States">United States</option>
                                                                    <option value="United Kingdom">United Kingdom</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Address</label>
                                                            <div class="col-md-8">
                                                                <textarea class="shipping-field" name="shipping_address">{{$customer->shippingAddress->address}}</textarea>
                                                                {{-- <input type="text" class="shipping-field" name="shipping_address" value={{$customer->shippingAddress->address}}> --}}
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">City</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="shipping-field" name="shipping_city" value={{$customer->shippingAddress->city}}>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">State</label>
                                                            <div class="col-md-8">
                                                                <select class="shipping-field" name="shipping_state" value={{$customer->shippingAddress->state}}>
                                                                    <option value="Maharashtra">Maharashtra</option>
                                                                    <option value="Delhi">Delhi</option>
                                                                    <option value="Karnataka">Karnataka</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Zip Code</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="shipping-field" name="shipping_zip_code" value={{$customer->shippingAddress->zip_code}}>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">phone</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="shipping-field" name="shipping_phone" value={{$customer->shippingAddress->phone}}>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center mb-3">
                                                            <label class="col-md-4 col-form-label">Fax Number</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="shipping-field" name="shipping_fax" value={{$customer->shippingAddress->fax}}>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                    <!-- Remarks Tab -->
                                    <div class="tab-content" id="remarks">
                                        <div class="row">
                                            <div class="col-12 col-md-8">
                                                <div class="form-section">
                                                    <div class="section-title">
                                                        <span>Remarks (For Internal Use)</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea name="remarks">{{ $customer->remarks }}</textarea>
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
                            <h1 class="customer-form-header">New Customer</h1>
                            {{-- <div class="customer-prefill-section">
                                <input type="checkbox" class="customer-prefill-checkbox" id="customerPrefill" name="prefill_from_gst">
                                <label for="customerPrefill" class="customer-prefill-label">Prefill Customer details from the GST portal using the Customer's GSTIN.</label>
                                <a href="#" class="customer-prefill-button">Prefill ></a>
                            </div> --}}

                            <div class="customer-form-section">
                                <div class="customer-section-title">Customer Type</div>
                                <div class="customer-radio-group">
                                    <div class="customer-radio-option">
                                        <input type="radio" class="customer-radio-input" id="customerTypeBusiness" name="customer_type" value="business" checked>
                                        <label for="customerTypeBusiness">Business</label>
                                    </div>
                                    <div class="customer-radio-option">
                                        <input type="radio" class="customer-radio-input" id="customerTypeIndividual" name="customer_type" value="individual">
                                        <label for="customerTypeIndividual">Individual</label>
                                    </div>
                                </div>
                            </div>

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
                                        <input type="text" class="customer-form-input" id="first_name" name="primary_contact_first_name" >
                                        <span class="error_first_name" style="color:red"></span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="customer-form-group">
                                        <label class="customer-form-label">Last Name</label>
                                        <input type="text" class="customer-form-input" name="primary_contact_last_name" >
                                    </div>
                                </div>
                            </div>

                            <div class="row customer-form-section">
                                <div class="customer-section-title">Company Name</div>
                                <div class="col-12 col-md-6">
                                    <div class="customer-form-group">
                                        <input type="text" class="customer-display-name-input" id="company_name" placeholder="Select or type to add" name="company_name" >

                                    </div>
                                </div>
                            </div>
                            <div class="row customer-form-section">
                                <div class="customer-section-title">Display Name</div>
                                <div class="col-12 col-md-6">
                                    <div class="customer-form-group">
                                        <input type="text" class="customer-form-input" id="display_name" name="display_name" >
                                        <span class="error_display_name" style="color:red"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row customer-form-section">
                                <div class="customer-section-title">Email Address</div>
                                <div class="col-12 col-md-6">
                                    <div class="customer-form-group">
                                        <input type="email" id="email" class="customer-form-input" name="email" >
                                        <span class="error_email" style="color:red"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row customer-form-section">
                                <div class="customer-section-title">Phone</div>
                                <div class="col-md-4 col-sm-12 ">
                                    <div class="customer-form-group">
                                        <label class="customer-form-label">Work Phone</label>
                                        <input type="tel" class="customer-form-input" name="work_phone" >

                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="customer-form-group">
                                        <label class="customer-form-label">Mobile</label>
                                        <input type="tel" class="customer-form-input" name="mobile"  id="phone_number">
                                        <span class="error_phone_number" style="color:red"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-container">
                            <div class="form-tabs">
                                <div class="form-tab active" data-tab="other-details">Other Details</div>
                                <div class="form-tab" data-tab="address">Address</div>
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
                                            <input type="text" name="pan" id="pan_number">
                                            <span class="error_pan_number" style="color:red"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-section">
                                    <div class="col-12 col-md-6">
                                        <div class="section-title">
                                            <span>Currency</span>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="default-currency" name="default_currency" checked>
                                            <label for="default-currency">INR - Indian Rupee</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Opening Balance</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" placeholder="INR" name="opening_balance" >
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Payment Terms</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <select name="payment_terms">
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
                                            <input type="checkbox" id="enable-portal" name="enable_portal">
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
                                            <select name="portal_language" >
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
                                            <input type="url" placeholder="ex: www.zylker.com" name="website">
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Department</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" name="department" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Designation</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" name="designation" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Twitter</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="url" placeholder="http://www.twitter.com/" name="twitter">
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Skype Name/Number</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" name="skype" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-section additional-details-section">
                                    <div class="col-12 col-md-6 row align-items-center mb-3">
                                        <div class="section-title col-md-4">
                                            <span>Facebook</span>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="url" placeholder="http://www.facebook.com/" name="facebook" >
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
                                                        <input type="text" id="billing_attention" name="billing_attention" class="form-control custom-input" value="{{ old('billing_attention', $customer->billing_attention ?? '') }}">
                                                    </div>
                                                    {{-- <input type="text" class="col-md-8 billing-field" name="billing_attention" > --}}

                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Country/Region</label>
                                                <div class="col-md-8">
                                                    <select class="billing-field" name="billing_country" >
                                                        <option value="India">India</option>
                                                        <option value="United States">United States</option>
                                                        <option value="United Kingdom">United Kingdom</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Address</label>
                                                <div class="col-md-8">
                                                    <textarea class="billing-field" name="billing_address"></textarea>
                                                    {{-- <input type="text" class="billing-field" name="billing_address" > --}}
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">City</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="billing-field" name="billing_city" >
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">State</label>
                                                <div class="col-md-8">
                                                    <select class="billing-field" name="billing_state" >
                                                        <option value="Maharashtra">Maharashtra</option>
                                                        <option value="Delhi">Delhi</option>
                                                        <option value="Karnataka">Karnataka</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Zip Code</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="billing-field" name="billing_zip_code" >
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">phone</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="billing-field" name="billing_phone" >
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Fax Number</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="billing-field" name="billing_fax" >
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
                                                    <input type="text" class="shipping-field" name="shipping_attention" >
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Country/Region</label>
                                                <div class="col-md-8">
                                                    <select class="shipping-field" name="shipping_country" >
                                                        <option value="India">India</option>
                                                        <option value="United States">United States</option>
                                                        <option value="United Kingdom">United Kingdom</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Address</label>
                                                <div class="col-md-8">
                                                    <textarea class="shipping-field" name="shipping_address"></textarea>
                                                    {{-- <input type="text" class="shipping-field" name="shipping_address" > --}}
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">City</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="shipping-field" name="shipping_city">
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">State</label>
                                                <div class="col-md-8">
                                                    <select class="shipping-field" name="shipping_state" >
                                                        <option value="Maharashtra">Maharashtra</option>
                                                        <option value="Delhi">Delhi</option>
                                                        <option value="Karnataka">Karnataka</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Zip Code</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="shipping-field" name="shipping_zip_code">
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">phone</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="shipping-field" name="shipping_phone">
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center mb-3">
                                                <label class="col-md-4 col-form-label">Fax Number</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="shipping-field" name="shipping_fax" >
                                                </div>
                                            </div>
                                        </div>
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
<script src="{{ asset('/assets/js/purchase/vendor.js') }}"></script>
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
             @if(!empty($customer))
                let existingDocuments = {!! json_encode($customer->documents) !!};
                const existingContacts = @json($customer->contacts ?? []);
                const isEditMode = existingContacts.length > 0;
            @else
                let existingDocuments = [];
                const existingContacts = [];
                const isEditMode = false;

            @endif
            // If it's still a stringified JSON, parse it
            if (typeof existingDocuments === 'string') {
                try {
                    existingDocuments = JSON.parse(existingDocuments);
                } catch (e) {
                    existingDocuments = [];
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
                                <a href="/path/to/uploaded/files/${file}" target="_blank">${file}</a>
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

        $(document).ready(function() {
            // Hide additional details container initially
            $('#additional-details-container').hide();
            $('.remove-additional-details').hide();

            // Tab switching functionality
            $('.close').on('click', function() {
                window.location.href = "{{ route('superadmin.getcustomer') }}";
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
            $('#add-contact-person').on('click', function() {
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
                            <div>
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
                    let email = $('#email').val().trim();
                    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (email === "") {
                        $('.error_email').text('Enter Your Email');
                        isValid = false;
                    } else if (!emailPattern.test(email)) {
                        $('.error_email').text('Enter a valid Email Address');
                        isValid = false;
                    } else {
                        $('.error_email').text('');
                    }

                    // Phone number validation
                    let phone = $('#phone_number').val().trim();
                    let phonePattern = /^[6-9]\d{9}$/;

                    if (phone === "") {
                        $('.error_phone_number').text('Enter the Mobile Number');
                        isValid = false;
                    } else if (!phonePattern.test(phone)) {
                        $('.error_phone_number').text('Enter a valid 10-digit Mobile Number');
                        isValid = false;
                    } else {
                        $('.error_phone_number').text('');
                    }


                    if ($('#pan_number').val() === "") {
                        $('.error_pan_number').text('Enter Pan Number');
                        isValid = false;
                    }

                    let panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
                    let panNumber = $('#pan_number').val().toUpperCase();
                    if (!panPattern.test(panNumber)) {
                        $('.error_pan_number').text('Invalid PAN Number');
                        isValid = false;
                    } else {
                        $('.error_pan_number').text('');
                    }



                    if (!isValid) {
                        return;
                    }

                // Append all form data
                $('input, select, textarea').each(function() {
                    if ($(this).attr('type') !== 'file' && $(this).attr('type') !== 'button' && $(this).attr('type') !== 'submit') {
                        const name = $(this).attr('name');
                        const value = $(this).val();

                        if (name) {
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

                // Show loading indicator
                const saveBtn = $(this);
                saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                // Send data to server
                $.ajax({
                    url: '{{ route("superadmin.savecustomer") }}',
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
                            window.location.href = "{{ route('superadmin.getcustomer') }}";
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

    </script>
    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>