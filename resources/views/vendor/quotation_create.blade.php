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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
  .gst_seperate span{
    margin:0;
    font-size:10px;
  }


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
{{--
    @php
        dd($vendor);
    @endphp --}}
    <div class="page-loader">
      <div class="bar"></div>
    </div>
    {{-- @php
        dd($bill[0]->BillLines);
    @endphp --}}
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')
    <!-- [ Header ] end -->
    <div class="pc-container">
      <div class="pc-content">
        <div class="container">
          <h1>New Quotation</h1>

          <form id="billForm" method="POST" action="">
              @csrf

            <div class="container mt-4">
                <div class="row mb-3 align-items-start">
                    <!-- Vendor Name -->
                    <div class="col-md-6">
                        <div class="row mb-2 align-items-start">
                            <label for="vendor-search" class="col-md-4  fw-semibold">Vendor Name*</label>

                            <input type="hidden" name="id" id="id">
                            <div class="col-md-8">
                                <div class="search-dropdown">
                                    <input type="text" id="vendor-search" class="form-control search-input" name="vendor_name" placeholder="Search vendor..." autocomplete="off">
                                    <div class="dropdown-menu" id="vendor-dropdown">
                                        <div class="search-box">
                                            <input type="text" placeholder="Search" class="inner-search form-control mb-2">
                                        </div>
                                        <div class="vendor-list"></div>
                                        <div class="new-vendor-option">
                                            <div class="vendor-item new-vendor">
                                                <div class="vendor-name"><a href="{{ route("superadmin.getvendorcreate") }}">
                                                  <i class="fa fa-plus"></i> New Vendor</a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="address_container">
                                  <div id="billing-address" class="billing-address-section mt-3 text-muted small">
                                      <!-- Filled via JS -->
                                  </div>
                                  <div id="shipping-address" class="shipping-address-section mt-3 text-muted small">
                                      <!-- Filled via JS -->
                                  </div>
                                </div>
                                <input type="hidden" id="selected-vendor-id" name="vendor_id">
                                <span class="error_vendor_name" style="color:red"></span>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Row 1: Subject -->
                <div class="row mb-3">
                    <label for="delivery_address" class="col-md-2 ">Delivery Address</label>
                    <div class="col-md-4">
                        {{-- <textarea class="form-control" id="delivery_address" autocomplete="off" autocorrect="off" name="delivery_address" placeholder="Address" rows="2" maxlength="250"></textarea> --}}
                        <div class="tax-dropdown-wrapper delivery-section" style="width:342px">
                            <input type="text" class="form-control delivery-search-input" name="delivery_text" autocomplete="off" autocorrect="off" placeholder="Select a address" readonly>
                            <input type="hidden" name="delivery_id" class="delivery_id">
                            <input type="hidden" name="delivery_name" class="delivery_name">
                            <div class="dropdown-menu tax-dropdown">
                              <div class="delivery-list">

                              </div>
                              <div class="manage-delivery-link">⚙️ Manage Delivery Address</div>
                            </div>
                          </div>
                        <span class="error_delivery" style="color:red"></span>
                    </div>
                </div>

                <!-- Row 2: Bill#, Order Number -->
                <div class="row mb-3">
                    <label for="quotation_gen_no" class="col-md-2 ">Quotation Generate*</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="quotation_gen_no" name="quotation_gen_no" value="{{$quotation_id}}" autocomplete="off" autocorrect="off" readonly required>
                        <span class="error_quotation" style="color:red"></span>
                    </div>

                </div>
                <!-- Row 2: Bill#, Order Number -->
                <div class="row mb-3">
                    <label for="quotation_no" class="col-md-2 ">Quotation no*</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="quotation_no" name="quotation_no" autocomplete="off" autocorrect="off" required>
                        <span class="error_quotation" style="color:red"></span>
                    </div>

                </div>
                <!-- Row 3: Bill#, Order Number -->
                <div class="row mb-3">
                    <label for="order_number" class="col-md-2 ">Order Number</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="order_number" name="order_number" autocomplete="off" autocorrect="off">
                        <span class="error_order_no" style="color:red"></span>
                    </div>
                </div>

                <!-- Row 4: Bill Date, Due Date -->
                <div class="row mb-3">
                    <label for="bill_date" class="col-md-2 ">Bill Date*</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control datepicker" autocomplete="off" autocorrect="off" id="bill_date" name="bill_date" placeholder="dd/MM/yyyy" required>
                        <span class="error_bill_date" style="color:red"></span>
                    </div>
                </div>

                <!-- Row 5: Payment Terms -->
                <div class="row mb-3">
                    <label for="due_date" class="col-md-2 ">Due Date</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control datepicker" id="due_date" name="due_date" autocomplete="off" autocorrect="off">
                        <span class="error_due_date" style="color:red"></span>
                    </div>

                    <label for="payment_terms" class="col-md-2 ">Payment Terms</label>
                    <div class="col-md-4">
                        <select class="form-select" id="payment_terms" autocomplete="off" autocorrect="off" name="payment_terms">
                            <option value="Due on Receipt" selected>Due on Receipt</option>
                            <option value="Net 15">Net 15</option>
                            <option value="Net 30">Net 30</option>
                            <option value="Net 60">Net 60</option>
                        </select>
                    </div>
                </div>
                <!-- Row 5: Payment Terms -->
                <div class="row mb-3">
                    <label for="zone" class="col-md-2 ">Zones</label>
                    <div class="col-md-4">
                        {{-- <input type="text" class="form-control datepicker" id="due_date" name="due_date" > --}}
                      <div class="tax-dropdown-wrapper account-section" style="width:343px">
                          <input type="text" class="form-control zone-search-input" autocomplete="off" autocorrect="off" name="zone" placeholder="Select a Zones" readonly>
                          <input type="hidden" name="zone_id" class="zone_id">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="zone-list">
                            </div>
                          </div>
                          <span class="error_zone" style="color:red"></span>
                        </div>
                    </div>

                    <label for="branch" class="col-md-2 ">Branch</label>
                    <div class="col-md-4">
                        {{-- <input type="text" class="form-control datepicker" id="due_date" name="due_date" > --}}
                      <div class="tax-dropdown-wrapper account-section" style="width:343px">
                          <input type="text" class="form-control branch-search-input" autocomplete="off" autocorrect="off" name="branch" placeholder="Select a branch" readonly>
                          <input type="hidden" name="branch_id" class="branch_id">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="branch-list">
                            </div>
                          </div>
                          <span class="error_branch" style="color:red"></span>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="zone" class="col-md-2 ">Company</label>
                    <div class="col-md-4">
                        {{-- <input type="text" class="form-control datepicker" id="due_date" name="due_date" > --}}
                      <div class="tax-dropdown-wrapper company-section" style="width:343px">
                          <input type="text" class="form-control company-search-input" autocomplete="off" autocorrect="off" name="company_name" placeholder="Select a Company" readonly>
                          <input type="hidden" name="company_id" class="company_id">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="company-list">
                            </div>
                          </div>
                          <span class="error_zone" style="color:red"></span>
                        </div>
                    </div>
                </div>

                <!-- Row 6: Subject -->
                <div class="row mb-3">
                    <label for="subject" class="col-md-2 ">Subject</label>
                    <div class="col-md-4">
                        <textarea class="form-control" id="subject" name="subject" autocomplete="off" autocorrect="off" placeholder="Enter a subject within 250 characters" rows="2" maxlength="250"></textarea>
                    </div>
                </div>
            </div>
            <div class="card">
              <div class="card-header">
                <h3>Item Table</h3>
              </div>
              <div class="card-body" >
                <div style="overflow-x:scroll">

                  <table class="table item-table">
                    <thead>
                      <tr>
                        <th>ITEM DETAILS</th>
                        <th>ACCOUNT</th>
                        <th>QUANTITY</th>
                        <th>RATE</th>
                        <th>CUSTOMER DETAILS</th>
                        <th>GST</th>
                        {{-- <th>CGST</th>
                        <th>SGST</th>
                        <th>GST Amount</th> --}}
                        <th>AMOUNT</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="item-row">
                        <td>
                          <input type="hidden" name="linesdata[0][id]" class="form-control item-id" style="width:150px">
                          {{-- <input type="text" name="linesdata[0][item_details]" class="form-control item-details" style="width:200px" autocomplete="off" autocorrect="off" placeholder="Type or click to select an item"> --}}
                          <textarea name="linesdata[0][item_details]" class="form-control item-details" autocomplete="off" autocorrect="off" style="width:200px;height: 70px;" placeholder="Type or click to select an item" rows="3"></textarea>

                        </td>

                        <td>
                          <div class="tax-dropdown-wrapper account-section" style="width:150px">
                            <input type="text" class="form-control account-search-input" autocomplete="off" autocorrect="off" name="linesdata[0][account]" placeholder="Select a account" readonly>
                            <input type="hidden" name="linesdata[0][account_id]" class="account_id">
                            <input type="hidden" name="linesdata[0][account_name]" class="account_name">
                            <div class="dropdown-menu tax-dropdown">
                              <div class="account-list">

                              </div>
                              <div class="manage-account-link">⚙️ Manage Account</div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <input type="number" name="linesdata[0][quantity]" class="form-control quantity" value="1.00" min="0" step="0.01"  style="width:100px">
                        </td>
                        <td>
                          <input type="number" name="linesdata[0][rate]" class="form-control rate" value="0.00" min="0" step="0.01" style="width:100px">
                        </td>
                        <td>
                            <div class="search-customer-dropdown" data-row-index="0" >
                                <input type="text" class="form-control customer-search"
                                      placeholder="Search customer..." style="width:110px" value="Aravind's IVF" readonly>
                                <input type="hidden" class="selected-customer-id" id="selected-customer-id" value="Aravind's IVF" name="linesdata[0][customer]">

                                {{-- <div class="dropdown-menu customer-dropdown" >
                                    <div class="inner-search-container">
                                        <input type="text" class="inner-search" placeholder="Search...">
                                    </div>
                                    <div class="customer-list"></div>
                                </div> --}}
                            </div>
                        </td>
                        <td>
                          <div class="gst_seperate"></div>
                          <div class="tax-dropdown-wrapper gst-section" style="width:150px">
                            <input type="text" class="form-control gst-search-input" autocomplete="off" autocorrect="off" name="linesdata[0][gst_name]" placeholder="Select a Tax" readonly>
                            <input type="hidden" name="linesdata[0][gst_tax_selected]" class="selected-gst-tax" id="gst_value">
                            <input type="hidden" name="linesdata[0][gst_tax_type]" class="gst_tax_type" id="gst_tax_type">
                            <input type="hidden" name="linesdata[0][gst_tax_id]" class="gst-tax-id">
                            <div class="dropdown-menu tax-dropdown">
                              <div class="inner-search-container">
                              <div class="tax-gst-list">

                              </div>
                              <div class="manage-gst-link">⚙️ Manage GST</div>
                            </div>
                          </div>

                        </td>

                        <td>
                          <input type="text" name="linesdata[0][amount]" class="form-control amount" autocomplete="off" autocorrect="off" value="0.00" readonly style="width:100px">
                          <input type="hidden" name="linesdata[0][cgst_amount]" class="form-control cgst_amount" value="0.00" readonly style="width:100px">
                          <input type="hidden" name="linesdata[0][sgst_amount]" class="form-control sgst_amount" value="0.00" readonly style="width:100px">
                          <input type="hidden" name="linesdata[0][gst_amount]" class="form-control gst_amount" value="0.00" readonly style="width:100px">
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="text-start mb-3">
                  <button type="button" class="btn btn-primary add-row">Add New Row</button>
                </div>

                <div class="row d-flex justify-content-end">
                  <div class="invoice-totals-box">
                    <!-- Subtotal -->
                    <div class="sub-total-section">
                      <div class="label">Sub Total</div>
                      <div class="value sub-total-amount">0.00</div>
                    </div>

                    <!-- Discount -->
                    <div class="form-row discount-row">
                      <div>
                        <label>Discount</label>
                        <div>
                          <span class="discount-toggle" style="color:#007bff;cursor:pointer;font-size: 9px;">Apply Before Tax</span>
                        </div>
                      </div>
                      <div class="input-wrapper d-flex" style="width:240px">
                        <input type="number" name="discount_percent" class="discount-percent form-control" min="0" max="100" style="width:70%;">
                        <select name="discount_type" class="discount_type form-control" style="width:30%;">
                          <option value="percent">%</option>
                          <option value="money">₹</option>
                        </select>
                      </div>
                      <div class="discount-amount">0.00</div>
                    </div>

                    <!-- Adjustment -->
                    <div class="form-row adjustment-row">
                      <label>Adjustment</label>
                      <div>
                        <input type="number" class="adjustment-value form-control" name="adjustment_value" placeholder="Enter adjustment">
                      </div>
                      <div class="adjustment-amount">0.00</div>
                    </div>
                    <div class="adjustment_reason">
                      <label>Adjustment Reason</label>
                      <div>
                        <input type="text" class="adjustment-reason form-control" name="adjustment_reason" placeholder="Reason for Adjustment">
                      </div>
                      <div class=""></div>
                    </div>
                    <!-- Adjustment -->
                    <div class="gst_calculate_show">

                    </div>

                   <!-- Radio selector -->
                    <div class="form-row tax-row">
                      <label><input type="radio" name="tax_type" value="TDS" checked> TDS</label>
                      {{-- <label><input type="radio" name="tax_type" value="TCS"> TCS</label> --}}

                      <!-- TDS TAX SELECT -->
                      <div class="tax-dropdown-wrapper tds-tax-section">
                        <input type="text" class="form-control tax-search-input" autocomplete="off" autocorrect="off" name="tds_tax_name" placeholder="Select a Tax" readonly>
                        <input type="hidden" name="tds_tax_selected" class="selected-tds-tax" id="tds_tax_value">
                        <input type="hidden" name="tds_tax_id" class="tds-tax-id">
                        <div class="dropdown-menu tax-dropdown">
                          <div class="tax-list">

                          </div>
                          <div class="manage-tds-link">⚙️ Manage TDS</div>
                        </div>
                      </div>

                      <!-- TCS TAX SELECT -->
                      <div class="tax-dropdown-wrapper tcs-tax-section" >
                        <input type="text" class="form-control tax-tcs-search-input" autocomplete="off" autocorrect="off" name="tcs_tax_name" placeholder="Select a Tax" readonly>
                        <input type="hidden" name="tcs_tax_selected" class="selected-tcs-tax">
                        <input type="hidden" name="tcs_tax_id" class="tcs-tax-id">
                        <div class="dropdown-menu tax-dropdown">
                          <div class="inner-search-container">
                            <input type="text" class="tax-inner-search" placeholder="Search...">
                          </div>
                          <div class="tax-tcs-list">

                          </div>
                          <div class="manage-tcs-link">⚙️ Manage TCS</div>
                        </div>
                      </div>
                      <div>
                        <div class="tax-amount" id="tax-amount" style="width: 80px;">0.00</div>
                      </div>
                    </div>
                    <div class="export_chargers">
                      <div class="export_chargers">
                        <input type="text" name="export_name" class="export_name" placeholder="Export chargers Name">
                        <input type="text" name="export_amount" class="export_amount" placeholder="Enter Amount">
                      </div>
                       <div>
                        <div class="export-amount" id="export-amount" style="width: 80px;">0.00</div>
                      </div>
                    </div>
                    {{-- <div class="form-row tax-row">
                      <label><input type="radio" name="gst_type" value="GST" checked> GST</label>
                      <label><input type="radio" name="gst_type" value="IGST"> IGST</label>

                      <!-- GST SELECT -->
                      <div class="tax-dropdown-wrapper gst-section">
                        <input type="text" class="form-control gst-search-input" name="gst_name" placeholder="Select a Tax" readonly>
                        <input type="hidden" name="gst_tax_selected" class="selected-gst-tax" id="gst_value">
                        <input type="hidden" name="gst_tax_id" class="gst-tax-id">
                        <div class="dropdown-menu tax-dropdown">
                          <div class="inner-search-container">
                            <input type="text" class="gst-inner-search" placeholder="Search...">
                          </div>
                          <div class="tax-gst-list">

                          </div>
                          <div class="manage-gst-link">⚙️ Manage GST</div>
                        </div>
                      </div>
                      <div>
                        <div class="gst-amount" style="width: 80px;">0.00</div>
                      </div>
                    </div> --}}
                    {{-- <div class="gst_show_container">
                      <div class="gst_container">
                        <div>
                          <span>CGST <span class="cgst_percent"></span></span>
                        </div>
                        <div class="cgst-amount" style="width: 80px;">0.00</div>
                      </div>
                      <div class="gst_container">
                        <div>
                          <span>SGST <span class="cgst_percent"></span></span>
                        </div>
                        <div class="sgst-amount" style="width: 80px;">0.00</div>
                      </div>
                    </div> --}}

                    <div class="esi-row tax_show_single" >
                        <label>ESI</label>
                        <div class="esi-display-amount">₹0.00</div>
                    </div>

                    <div class="pf-row tax_show_single" >
                        <label>PF</label>
                        <div class="pf-display-amount">₹0.00</div>
                    </div>

                    <div class="other-row tax_show_single" >
                        <label>Other</label>
                        <div class="other-display-amount">₹0.00</div>
                    </div>

                    {{-- <div class="other-reason-row tax_show_single" style="display:none;">
                        <label>Reason</label>
                        <div class="other-reason-display"></div>
                    </div> --}}


                    <span class="open-other-charges"
                          style="color:#007bff; cursor:pointer; font-size: 9px; margin-top:6px; display:inline-block;">
                        Add ESI / PF / Others
                    </span>
                    <hr>

                    <!-- Grand Total -->
                    <div class="grand-total-section">
                      <div class="label">Total ( ₹ )</div>
                      <div class="value grand-total-amount">0.00</div>
                    </div>
                  </div>
                </div>

                <div class="notes-upload-wrapper">
                  <!-- Notes Section -->
                  <div class="notes-section">
                    <label for="notes">Notes</label>
                    <textarea id="notes" class="notes-textarea" name="note" placeholder="Enter your note..."></textarea>
                    <p class="note-hint">It will not be shown in PDF</p>
                  </div>

                  <!-- Upload Section -->
                  <div class="upload-section">
                    <label class="upload-title">Attach File(s) to Quotation</label>

                    <!-- Hidden file input -->
                    <input type="file" id="fileInput" name="uploads[]" multiple style="display: none;" />
                    <input type="hidden" name="existing_files" id="existingFilesInput">
                    <!-- Upload buttons -->
                    <div class="upload-box">
                      <button type="button" class="upload-btn" id="uploadTrigger">📤 Upload File</button>
                      <button type="button" class="upload-dropdown">▼</button>
                    </div>

                    <!-- Upload hint -->
                    <p class="upload-hint">You can upload a maximum of 5 files, 10MB each</p>

                    <!-- Display uploaded file names -->
                    <ul class="file-list" id="fileList"></ul>
                  </div>
                </div>

              </div>
            </div>
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
                      {{-- <button class="btn-new-tds-group">+ New TDS Group</button> --}}
                    </div>
                  </div>
                  {{-- <table class="tds-table">
                    <!-- Example content -->
                    <thead>
                      <tr><th>TAX NAME</th><th>RATE (%)</th><th>STATUS</th></tr>
                    </thead>
                    <tbody>
                      @foreach ($Tbltdstax as $tax)
                      @php
                          $start = \Carbon\Carbon::createFromFormat('d/m/Y', $tax->tax_start_date);
                          $end = \Carbon\Carbon::createFromFormat('d/m/Y', $tax->tax_end_date);
                          $today = \Carbon\Carbon::today();

                          if ($today->toDateString() < $start->toDateString()) {
                              $status = 'Upcoming';
                          } elseif ($today->toDateString() > $end->toDateString()) {
                              $status = 'Expired';
                          } else {
                              $status = 'Active'; // Same day or in between
                          }
                      @endphp

                      <tr>
                        <td>{{ $tax->tax_name }}</td>
                        <td>{{ $tax->tax_rate }}</td>
                        <td class="{{ strtolower($status) }}">{{ $status }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table> --}}
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
                        <span class="tds_name_error" style="color: red"></span>
                        <input type="text" class="form-control tds_name" autocomplete="off" autocorrect="off" />
                      </div>
                      <div style="flex: 1;">
                        <label>Rate (%) <span style="color: red">*</span></label>
                        <span class="tds_rate_error" style="color: red" ></span>
                        <input type="text" class="form-control tds_rate" autocomplete="off" autocorrect="off"/>
                      </div>
                    </div>
                    <div class="mt-2" style="display: flex; gap: 10px;flex-direction:column;">
                       <label>Section<span style="color: red">*</span></label>
                        <div class="tax-dropdown-wrapper tdssection">
                          <input type="text" class="form-control tax-section-input" name="tds_section_name" placeholder="Select a Tax" readonly>
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
                        <span class="section_name_error" style="color: red"></span>
                        <input type="text" class="form-control section_name" autocomplete="off" autocorrect="off" />
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
            {{-- tds modal --}}
            <!-- Manage TDS Modal -->
            <div id="tcsModal" class="tds-modal">
              <div class="tds-modal-content">
                <div class="tds-modal-header">
                  <h4>Manage TCS</h4>
                  <span class="close-modal-tcs">&times;</span>
                </div>

                <div class="tds-modal-body">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h5>TCS taxes</h5>
                    <div>
                      <button type="button" class="btn-new-tcs">+ New TCS Tax</button>
                      {{-- <button class="btn-new-tds-group">+ New TDS Group</button> --}}
                    </div>
                  </div>
                  <table class="tds-table">
                    <!-- Example content -->
                    <thead>
                      <tr><th>TAX NAME</th><th>RATE (%)</th><th>STATUS</th></tr>
                    </thead>
                    <tbody>
                      @foreach ($Tbltcstax as $tax)
                      @php
                          $start = \Carbon\Carbon::createFromFormat('d/m/Y', $tax->tax_start_date);
                          $end = \Carbon\Carbon::createFromFormat('d/m/Y', $tax->tax_end_date);
                          $today = \Carbon\Carbon::today();

                          if ($today->toDateString() < $start->toDateString()) {
                              $status = 'Upcoming';
                          } elseif ($today->toDateString() > $end->toDateString()) {
                              $status = 'Expired';
                          } else {
                              $status = 'Active'; // Same day or in between
                          }
                      @endphp

                      <tr>
                        <td>{{ $tax->tax_name }}</td>
                        <td>{{ $tax->tax_rate }}</td>
                        <td class="{{ strtolower($status) }}">{{ $status }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- New TDS Tax Modal -->
            <div id="newTcsModal" class="tds-modal">
              <div class="tds-modal-content" style="max-width: 600px;">
                <div class="tds-modal-header">
                  <h4>New TCS</h4>
                  <span class="close-new-modal-tcs" style="font-size: 2rem;cursor:pointer">&times;</span>
                </div>

                <div class="gst-modal-body">
                  <form>
                    <div style="display: flex; gap: 10px;">
                      <div style="flex: 1;">
                        <label>Tax Name* <span style="color: red">*</span></label>
                        <input type="text" class="form-control tcs_name" autocomplete="off" autocorrect="off" />
                      </div>
                      <div style="flex: 1;">
                        <label>Rate (%) <span style="color: red">*</span></label>
                        <input type="text" class="form-control tcs_rate" autocomplete="off" autocorrect="off" />
                      </div>
                    </div>

                    <h5 style="margin: 10px 0;">Applicable Period</h5>
                    <div style="display: flex; gap: 10px;">
                      <div style="flex: 1;">
                        <label>Start Date</label>
                        <input type="text" class="form-control datepicker tcs_start_date" autocomplete="off" autocorrect="off" placeholder="dd/MM/yyyy" />
                      </div>
                      <div style="flex: 1;">
                        <label>End Date</label>
                        <input type="text" class="form-control datepicker tcs_end_date" autocomplete="off" autocorrect="off" placeholder="dd/MM/yyyy" />
                      </div>
                    </div>
                    <br />
                    <button class="btn-save tcs_save" type="submit">Save</button>
                    <button class="btn-cancel close-new-modal-tcs" type="button">Cancel</button>
                  </form>
                </div>
              </div>
            </div>

             {{-- gst modal --}}
            <!-- Manage gst Modal -->
            <div id="gstModal" class="tds-modal">
              <div class="tds-modal-content">
                <div class="tds-modal-header">
                  <h4>Manage GST</h4>
                  <span class="close-modal-gst">&times;</span>
                </div>

                <div class="gst-modal-body">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h5>GST taxes</h5>
                    <div>
                      <button type="button" class="btn-new-gst">+ New GST Tax</button>
                      {{-- <button class="btn-new-tds-group">+ New TDS Group</button> --}}
                    </div>
                  </div>

                  <div class="gst-content-over">
                    @include('vendor.partials.gst_table', ['Tblgsttax' => $Tblgsttax])
                  </div>
                </div>
              </div>
            </div>

            <!-- New TDS Tax Modal -->
            <div id="newgstModal" class="tds-modal">
              <div class="tds-modal-content" style="max-width: 600px;">
                <div class="tds-modal-header">
                  <h4>New GST</h4>
                  <span class="close-new-modal-gst" style="font-size: 2rem;cursor:pointer">&times;</span>
                </div>

                <div class="tds-modal-body">
                  <form>
                    <div style="display: flex; gap: 10px;">
                      <div style="flex: 1;">
                        <label>Tax Name* <span style="color: red">*</span></label>
                        <span class="gst_name_error" style="color: red"></span>
                        <input type="text" class="form-control gst_name" autocomplete="off" autocorrect="off"/>
                      </div>
                      <div style="flex: 1;">
                        <label>Rate (%) <span style="color: red">*</span></label>
                        <span class="gst_rate_error" style="color: red"></span>
                        <input type="text" class="form-control gst_rate" autocomplete="off" autocorrect="off"/>
                      </div>
                    </div>
                     <br/>
                    <div class="col-sm-4" style="display: flex; gap: 10px; ">
                      <div style="flex: 1;">
                        <label>Tax Type <span style="color: red">*</span></label>
                        <select name="gst_type" class="gst_type" id="gst_type" autocomplete="off" autocorrect="off">
                            <option value="GST">GST</option>
                            <option value="IGST">IGST</option>
                        </select>
                      </div>

                    </div>

                    <h5 style="margin: 10px 0;">Applicable Period</h5>
                    <div style="display: flex; gap: 10px;">
                      <div style="flex: 1;">
                        <label>Start Date</label>
                        <input type="text" class="form-control datepicker gst_start_date" autocomplete="off" autocorrect="off" placeholder="dd/MM/yyyy" />
                      </div>
                      <div style="flex: 1;">
                        <label>End Date</label>
                        <input type="text" class="form-control datepicker gst_end_date" autocomplete="off" autocorrect="off" placeholder="dd/MM/yyyy" />
                      </div>
                    </div>
                    <br />
                    <button class="btn-save gst_save" type="submit">Save</button>
                    <button class="btn-cancel close-new-modal-gst" type="button">Cancel</button>
                  </form>
                </div>
              </div>
            </div>

            <!-- New Account Modal -->
            <div id="newaccModal" class="tds-modal">
              <div class="tds-modal-content" style="max-width: 600px;">
                <div class="tds-modal-header">
                  <h4>New Account</h4>
                  <span class="close-new-modal-acc" style="font-size: 2rem;cursor:pointer">&times;</span>
                </div>

                <div class="tds-modal-body">
                  <form>
                    <div style="display: flex; gap: 10px;">
                      <div style="flex: 1;">
                        <label>Account Name <span style="color: red">*</span></label>
                        <span class="acc_name_error" style="color: red"></span>
                        <input type="text" class="form-control acc_name" autocomplete="off" autocorrect="off" />
                      </div>
                      <div style="flex: 1;">
                        <label>Account Code</label>
                        <input type="text" class="form-control account_code" autocomplete="off" autocorrect="off" />
                      </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                       <div style="flex: 1;">
                        <label>Description</label>
                        <textarea name="form-control account_description" id="account_description" autocomplete="off" autocorrect="off" cols="10" rows="3"></textarea>
                      </div>
                    </div>
                    <br />
                    <button class="btn-save acc_save" type="submit">Save</button>
                    <button class="btn-cancel close-new-modal-acc" type="button">Cancel</button>
                  </form>
                </div>
              </div>
            </div>
            <!-- New Account Modal -->
            <div id="newdeliveryModal" class="tds-modal">
              <div class="tds-modal-content" style="max-width: 600px;">
                <div class="tds-modal-header">
                  <h4>New Delivery address</h4>
                  <span class="close-new-modal-delivery" style="font-size: 2rem;cursor:pointer">&times;</span>
                </div>

                <div class="tds-modal-body">
                  <form>

                    <div style="display: flex; gap: 10px;">
                       <div style="flex: 1;">
                        <label>Address</label>
                        <span class="delivery_address__modal_error" style="color: red"></span>
                        <textarea name="form-control delivery_address_modal" id="delivery_address_modal" autocomplete="off" autocorrect="off" cols="10" rows="3"></textarea>
                      </div>
                    </div>
                    <br />
                    <button class="btn-save delivery_save" type="submit">Save</button>
                    <button class="btn-cancel close-new-modal-delivery" type="button">Cancel</button>
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
          <div class="modal fade" id="otherChargesModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">

                <div class="modal-header">
                  <h5 class="modal-title">Add Other Charges</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                  <!-- ESI -->
                  <div class="mb-2">
                      <label>ESI</label>
                      <div class="d-flex gap-2">
                          <input type="number" class="form-control esi_value" name="esi_value" placeholder="Enter ESI">
                          <select class="form-control esi_type" name="esi_type">
                              <option value="percent">%</option>
                              <option value="money">₹</option>
                          </select>
                      </div>
                  </div>

                  <!-- PF -->
                  <div class="mb-2">
                      <label>PF</label>
                      <div class="d-flex gap-2">
                          <input type="number" class="form-control pf_value" name="pf_value" placeholder="Enter PF">
                          <select class="form-control pf_type" name="pf_type">
                              <option value="percent">%</option>
                              <option value="money">₹</option>
                          </select>
                      </div>
                  </div>

                  <!-- Other -->
                  <div class="mb-2">
                      <label>Other</label>
                      <div class="d-flex gap-2">
                          <input type="number" class="form-control other_value" name="other_value" placeholder="Enter Other">
                          <select class="form-control other_type" name="other_type">
                              <option value="percent">%</option>
                              <option value="money">₹</option>
                          </select>
                      </div>
                  </div>

                  <div class="mb-2">
                      <label>Reason</label>
                      <input type="text" class="form-control other_reason" name="other_reason" placeholder="Enter Reason">
                  </div>

              </div>


                <div class="modal-footer">
                  <button class="btn btn-primary save-other-charges">Save</button>
                </div>

              </div>
            </div>
          </div>
          {{-- <!-- Preview Modal -->
          <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">File Preview</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="previewContent">
                  <!-- Preview content goes here -->
                </div>
              </div>
            </div>
          </div> --}}



          </form>
            <div class="action-buttons">
              <button type="button" id="saveDraftBtn" class="btn draft-btn">Save as Draft</button>
              <button type="button" id="saveOpenBtn" class="btn open-btn">Save as Open</button>
              <button type="button" class="btn cancel-btn">Cancel</button>
            </div>
          

      </div>
      </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
  let rowCount = 1; // Start at 1 since we have the initial row

    // Add new row
    $('.add-row').on('click',function(e) {
      e.preventDefault();
      const newRow = $('.item-row:first').clone();

      // Update all names with new index
      newRow.find('input, select ,textarea').each(function() {
        const name = $(this).attr('name');
        if (name) {
          $(this).attr('name', name.replace(/\[\d+\]/, '[' + rowCount + ']'));
        }
      });

      // Clear values
      newRow.find('.item-id').val('');
      newRow.find('.item-details').val('');
      newRow.find('.account-search-input').val('');
      newRow.find('.gst-search-input').val('');
      newRow.find('.cgst_amount').val(0);
      newRow.find('.sgst_amount').val(0);
      newRow.find('.gst_amount').val(0);
      newRow.find('.customer-search').val("Aravind's IVF");
      newRow.find('.quantity').val('1.00');
      newRow.find('.rate').val('0.00');
      newRow.find('.amount').val('0.00');
      newRow.find('select').prop('selectedIndex', 0);
      const gstList = $('.item-row:first .tax-gst-list').html();
      newRow.find('.tax-gst-list').html(gstList);
      $('.item-table tbody').append(newRow);
      // initCustomerSearch(newRow.find('.search-customer-dropdown'));
      rowCount++;

      // Focus on the first input of the new row
      newRow.find('.item-details').focus();
    });
  // Remove row
    $(document).on('click', '.remove-row', function() {
      if ($('.item-row').length > 1) {
        $(this).closest('tr').remove();
        calculateSubTotal();
        reindexRows(); // Reindex after removal
      } else {
        alert("You must have at least one row");
      }
    });

    // Reindex all rows to maintain consecutive numbering
    function reindexRows() {
      $('.item-row').each(function(index) {
        $(this).find('input, select').each(function() {
          const name = $(this).attr('name');
          if (name) {
            $(this).attr('name', name.replace(/\[\d+\]/, '[' + index + ']'));
          }
        });
      });
      rowCount = $('.item-row').length;
    }
  const $discountRow = $('.discount-row');
      const $tdsSection = $('.tax-row');
      const $adjustmentSection = $('.adjustment-row');
      let isAfterTax = false;

      $(document).on('click', '.discount-toggle', function() {
        if (!isAfterTax) {
          // Move below TDS
          $discountRow.fadeOut(150, function() {
            $discountRow.insertAfter($tdsSection).fadeIn(150);
          });
          $('.discount-toggle').text('Apply After Tax');
          isAfterTax = true;
        } else {
          // Move back to original position (before GST)
          $discountRow.fadeOut(150, function() {
            $discountRow.insertBefore($adjustmentSection).fadeIn(150);
          });
          $('.discount-toggle').text('Apply Before Tax');
          isAfterTax = false;
        }
        calculateFinalTotals();
        gstcalculate();
      });

      $(document).on('input', '.export_amount', function() {
        var name = $('.export_name').val().trim();

        if (name === '') {
            toastr.error('Export Name is required');
            $('.export_name').focus();
            $(this).val(''); // clear amount until name entered
            return false;
        }
      });
  function gstcalculate(changedRow) {

    const summary = {};

    // STEP 1 — calculate gross total before discount (for proportional distribution)
    let grossTotal = 0;
    $('.selected-gst-tax').each(function () {
        const row = $(this).closest('tr, .item-row, .invoice-row, .row');
        const q = parseFloat(row.find('.quantity').val()) || 0;
        const r = parseFloat(row.find('.rate').val()) || 0;
        grossTotal += (q * r);
    });

    const discountInput = parseFloat($('.discount-percent').val()) || 0;
    const discountType = $('.discount_type').val();

    // STEP 2 — iterate item rows
    $('.selected-gst-tax').each(function () {
        const $select = $(this);
        const row = $select.closest('tr, .item-row, .invoice-row, .row');

        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const rate = parseFloat(row.find('.rate').val()) || 0;
        const gst_percent = parseFloat($select.val()) || 0;
        const gst_type = (row.find('.gst_tax_type').val() || '').toString().toUpperCase() || 'GST';

        let amount = quantity * rate;
        let discountAmount = 0;

        // STEP 3 — Discount calculation FIX
        if (discountType === "percent") {
            discountAmount = amount * (discountInput / 100);          // normal row-wise %
        } else {
            const proportion = grossTotal > 0 ? amount / grossTotal : 0;
            discountAmount = discountInput * proportion;              // proportional ₹ discount
        }

        if (!isAfterTax) {
            amount -= discountAmount;
        }

        const gst_amount = (amount * gst_percent) / 100;

        // STEP 4 — GST Split
        if (gst_type === 'GST') {
            const cgst_amount = gst_amount / 2;
            const sgst_amount = gst_amount / 2;

            if (row.find('.cgst_amount').length) row.find('.cgst_amount').val(cgst_amount.toFixed(2));
            if (row.find('.sgst_amount').length) row.find('.sgst_amount').val(sgst_amount.toFixed(2));

            const keyC = `CGST_${(gst_percent / 2).toFixed(2)}`;
            const keyS = `SGST_${(gst_percent / 2).toFixed(2)}`;

            summary[keyC] = (summary[keyC] || 0) + cgst_amount;
            summary[keyS] = (summary[keyS] || 0) + sgst_amount;
        }
        else if (gst_type === 'IGST') {
            if (row.find('.cgst_amount').length) row.find('.cgst_amount').val('0.00');
            if (row.find('.sgst_amount').length) row.find('.sgst_amount').val('0.00');

            const keyI = `IGST_${gst_percent.toFixed(2)}`;
            summary[keyI] = (summary[keyI] || 0) + gst_amount;
        }
        else {
            if (row.find('.cgst_amount').length) row.find('.cgst_amount').val('0.00');
            if (row.find('.sgst_amount').length) row.find('.sgst_amount').val('0.00');
        }
    });

    // STEP 5 — Update summary UI
    const $container = $('.gst_calculate_show');
    $container.empty();

    const orderedKeys = Object.keys(summary).sort((a, b) => {
        const [typeA, pctA] = a.split('_');
        const [typeB, pctB] = b.split('_');
        if (pctA === pctB) {
            const order = { 'CGST': 0, 'SGST': 1, 'IGST': 2 };
            return (order[typeA] || 9) - (order[typeB] || 9);
        }
        return parseFloat(pctA) - parseFloat(pctB);
    });

    orderedKeys.forEach(key => {
        const [type, percent] = key.split('_');
        const amount = summary[key] || 0;
        $container.append(`
            <div class="tax_show" data-type="${type}" data-percent="${percent}">
              <label>${type} [${percent}%]</label>
              <div class="${type.toLowerCase()}-amount">₹ ${Number(amount).toFixed(2)}</div>
            </div>
        `);
    });
}
  function calculateSubTotal() {
    let subTotal = 0;
    let gst_amount = 0;

    $('.item-row').each(function() {
        const quantity = parseFloat($(this).find('.quantity').val()) || 0;
        const rate = parseFloat($(this).find('.rate').val()) || 0;
        const amount = quantity * rate;

        subTotal += amount;

        const gst_percent = parseFloat($(this).find('.selected-gst-tax').val()) || 0;
        const rowGstAmount = (amount * gst_percent) / 100;
        gst_amount += rowGstAmount;
    });


    const total_with_gst = subTotal;

    $('.sub-total-amount').text('₹' + total_with_gst.toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));
    $('.grand-total-amount').text('₹' + total_with_gst.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));


    return total_with_gst; // <-- Already includes GST
}

// function calculateFinalTotals() {
//     var subTotal = calculateSubTotal(); // Already includes GST
//     const discountPercent = parseFloat($('.discount-percent').val()) || 0;
//     const discountType = $('.discount_type').val();
//     const exportamount = parseFloat($('.export_amount').val()) || 0;
//     const esiAmt = parseFloat($('.esi-row').attr('data-esi')) || 0;
//     const pfAmt = parseFloat($('.pf-row').attr('data-pf')) || 0;
//     const otherAmt = parseFloat($('.other-row').attr('data-other')) || 0;
//     console.log("discountType",discountType);



//     const taxtdsRate = parseFloat($('.selected-tds-tax').val().trim());
//     let total_gst = 0;
//     // $(".gst_amount").each(function(index) {
//     //     total_gst += parseFloat($(this).val());
//     // });
//     $('.gst_calculate_show .sgst-amount, .gst_calculate_show .cgst-amount, .gst_calculate_show .igst-amount')
//     .each(function () {
//         let value = $(this).text().replace("₹", "").trim();
//         total_gst += parseFloat(value) || 0;
//     });
//     console.log("total_gst",total_gst);
//     const checkgrandTotal = subTotal  + total_gst ;
//       let discountAmount=0;
//       if(isAfterTax){
//         if(discountType==="percent"){
//           discountAmount = checkgrandTotal * (discountPercent / 100);
//         }else{
//           discountAmount = discountPercent;
//         }
//       }else{
//         if(discountType==="percent"){
//           discountAmount = subTotal * (discountPercent / 100);
//         }else{
//           discountAmount = discountPercent;
//         }

//       }
//       subTotal = subTotal - discountAmount;
//     const taxtcsRate = parseFloat($('.selected-tcs-tax').val()) || 0;
//     let taxtdsAmount = 0;
//     let taxtcsAmount = 0;
//     let taxLabel = '';
//     let taxDisplayAmount = 0;

//     if (taxtcsRate > 0) {
//         taxtcsAmount = subTotal * (taxtcsRate / 100);
//         taxLabel = '₹';
//         taxDisplayAmount = taxtcsAmount;
//     } else if (taxtdsRate >= 0) {
//         taxtdsAmount = subTotal * (taxtdsRate / 100);
//         taxLabel = '- ₹';
//         taxDisplayAmount = taxtdsAmount;
//     }
//     console.log("taxDisplayAmount",taxDisplayAmount);


//     const adjustment = parseFloat($('.adjustment-value').val()) || 0;

//     const grandTotal = subTotal
//                     - taxtdsAmount
//                     + taxtcsAmount
//                     + adjustment
//                     + total_gst
//                     + exportamount
//                     - esiAmt
//                     - pfAmt
//                     - otherAmt;
//     // if (vendor.tds_amount !== null && vendor.tds_amount !== 0) {
//     //  }else{
//     //    $('#tax-amount').text('₹0.00');
//     //  }
//     $('.discount-amount').text('₹' + discountAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
//     $('#tax-amount').text(taxLabel + taxDisplayAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
//     $('.adjustment-amount').text('₹' + adjustment.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
//     $('.export-amount').text('₹' + exportamount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
//     $('.grand-total-amount').text('₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

//   }
function calculateTotalGST() {
    let totalGST = 0;

    $('.item-row').each(function () {
        const $row = $(this);
        const quantity = parseFloat($row.find('.quantity').val()) || 0;
        const rate = parseFloat($row.find('.rate').val()) || 0;
        const gstPercent = parseFloat($row.find('.selected-gst-tax').val()) || 0;
        const gstType = ($row.find('.gst_tax_type').val() || 'GST').toUpperCase();

        let rowAmount = quantity * rate;

        // Apply discount BEFORE tax if required
        const discountPercent = parseFloat($('.discount-percent').val()) || 0;
        const discountType = $('.discount_type').val();
        if (!isAfterTax) { // discount BEFORE tax
            let discountAmount = 0;
            if (discountType === 'percent') discountAmount = rowAmount * (discountPercent / 100);
            else discountAmount = discountPercent * (rowAmount / calculateSubTotal()); // proportional
            rowAmount -= discountAmount;
        }

        // GST calculation
        let gstAmount = (rowAmount * gstPercent) / 100;

        totalGST += gstAmount; // accumulate GST for all rows
    });

    return totalGST;
}

function calculateFinalTotals() {
    var subTotal = calculateSubTotal(); // Already includes GST
    const discountPercent = parseFloat($('.discount-percent').val()) || 0;
    const discountType = $('.discount_type').val();
    const exportamount = parseFloat($('.export_amount').val()) || 0;
    const esiAmt = parseFloat($('.esi-row').attr('data-esi')) || 0;
    const pfAmt = parseFloat($('.pf-row').attr('data-pf')) || 0;
    const otherAmt = parseFloat($('.other-row').attr('data-other')) || 0;
    console.log("discountType",discountType);



    const taxtdsRate = parseFloat($('.selected-tds-tax').val().trim());
    const total_gst = calculateTotalGST();

    const checkgrandTotal = subTotal  + total_gst ;
    var discountAmount=0;
    console.log("isAfterTax",isAfterTax);

    if(isAfterTax){
      if(discountType==="percent"){
        discountAmount = checkgrandTotal * (discountPercent / 100);
      }else{
        discountAmount = discountPercent;
      }
    }else{
      if(discountType==="percent"){
        discountAmount = subTotal * (discountPercent / 100);
      }else{
        discountAmount = discountPercent;
      }

    }
    console.log("discountAmount",discountAmount);

    subTotal = subTotal - discountAmount;

    const taxtcsRate = parseFloat($('.selected-tcs-tax').val()) || 0;
    let taxtdsAmount = 0;
    let taxtcsAmount = 0;
    let taxLabel = '';
    let taxDisplayAmount = 0;

    if (taxtcsRate > 0) {
        taxtcsAmount = subTotal * (taxtcsRate / 100);
        taxLabel = '₹';
        taxDisplayAmount = taxtcsAmount;
    } else if (taxtdsRate >= 0) {
        taxtdsAmount = subTotal * (taxtdsRate / 100);
        taxLabel = '- ₹';
        taxDisplayAmount = taxtdsAmount;
    }


    const adjustment = parseFloat($('.adjustment-value').val()) || 0;
    console.log("subTotal",subTotal);
    console.log("taxtdsAmount",taxtdsAmount);
    console.log("adjustment",adjustment);
    console.log("total_gst",total_gst);
    console.log("exportamount",exportamount);
    console.log("esiAmt",esiAmt);
    console.log("pfAmt",pfAmt);
    console.log("otherAmt",otherAmt);

    const grandTotal = subTotal
                    - taxtdsAmount
                    + taxtcsAmount
                    + adjustment
                    + total_gst
                    + exportamount
                    - esiAmt
                    - pfAmt
                    - otherAmt;
    // if (vendor.tds_amount !== null && vendor.tds_amount !== 0) {
    //  }else{
    //    $('#tax-amount').text('₹0.00');
    //  }
    $('.discount-amount').text('₹' + discountAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('#tax-amount').text(taxLabel + taxDisplayAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('.adjustment-amount').text('₹' + adjustment.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('.export-amount').text('₹' + exportamount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('.grand-total-amount').text('₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

  }
</script>
 <script>
  $(document).ready(function() {
  const $discountRow = $('.discount-row');
  const $tdsSection = $('.tax-row');
  const $adjustmentSection = $('.adjustment-row');
  let isAfterTax = false;

  $(document).on('click', '.discount-toggle', function() {
    if (!isAfterTax) {
      // Move below TDS
      $discountRow.fadeOut(150, function() {
        $discountRow.insertAfter($tdsSection).fadeIn(150);
      });
      $('.discount-toggle').text('Apply After Tax');
      isAfterTax = true;
    } else {
      // Move back to original position (before GST)
      $discountRow.fadeOut(150, function() {
        $discountRow.insertBefore($adjustmentSection).fadeIn(150);
      });
      $('.discount-toggle').text('Apply Before Tax');
      isAfterTax = false;
    }
    calculateFinalTotals();
  });
   $(document).on('click', '.open-other-charges', function() {
        $('#otherChargesModal').modal('show');
    });
  $(document).on('click', '.save-other-charges', function() {

        const subTotal = calculateSubTotal();

        let esiVal = parseFloat($('.esi_value').val()) || 0;
        let esiType = $('.esi_type').val();

        let pfVal = parseFloat($('.pf_value').val()) || 0;
        let pfType = $('.pf_type').val();

        let otherVal = parseFloat($('.other_value').val()) || 0;
        let otherType = $('.other_type').val();

        const reason = $('.other_reason').val();

        // ---- CALCULATE INDIVIDUAL TOTALS ----
        let esiAmt = (esiType === "percent") ? (subTotal * esiVal / 100) : esiVal;
        let pfAmt = (pfType === "percent") ? (subTotal * pfVal / 100) : pfVal;
        let otherAmt = (otherType === "percent") ? (subTotal * otherVal / 100) : otherVal;

        // ---- DISPLAY VALUES ----
        if (esiAmt > 0) {
            $('.esi-row').show();
            $('.esi-display-amount').text("₹" + esiAmt.toFixed(2));
        }

        if (pfAmt > 0) {
            $('.pf-row').show();
            $('.pf-display-amount').text("₹" + pfAmt.toFixed(2));
        }

        if (otherAmt > 0) {
            $('.other-row').show();
            $('.other-display-amount').text("₹" + otherAmt.toFixed(2));
        }

        if (reason.trim() !== "") {
            $('.other-reason-row').show();
            $('.other-reason-display').text(reason);
        }

        // store values to calculate total
        $('.esi-row').attr('data-esi', esiAmt);
        $('.pf-row').attr('data-pf', pfAmt);
        $('.other-row').attr('data-other', otherAmt);

        $('#otherChargesModal').modal('hide');

        calculateFinalTotals();
    });
  $(document).on('input', '.export_amount', function() {
    var name = $('.export_name').val().trim();

    if (name === '') {
        toastr.error('Export Name is required');
        $('.export_name').focus();
        $(this).val(''); // clear amount until name entered
        return false;
    }
  });


    toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

            $(document).on('click', '#gstModal .pagination a', function(e) {
              e.preventDefault();
              e.stopImmediatePropagation();
              var href = $(this).attr('href');
              var page = href && href.split('page=')[1] ? href.split('page=')[1] : 1;
              $.ajax({
                  url: '{{ route("ajax.gst") }}?page=' + page,
                  type: 'GET',
                  dataType: 'json',
                  success: function(response) {
                      $('#gstModal .gst-content-over').html(response.html);
                  },
                  error: function(xhr) {
                      console.error('GST pagination error:', xhr.responseText);
                  }
              });
          });
             $(document).on('click', '#tdsModal .pagination a', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var href = $(this).attr('href');
                var page = href && href.split('page=')[1] ? href.split('page=')[1] : 1;

                $.ajax({
                    url: '{{ route("ajax.tds") }}?page=' + page,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // Only replace the table area, preserving the "New TDS Tax" header button
                        $('#tdsModal .gst-content').html(response.html);
                        $('#tdsModal .gst-content').scrollTop(0);
                    },
                    error: function(xhr) {
                        console.error('TDS pagination error:', xhr.responseText);
                    }
                });
            });
              let vendor;
             $(document).ready(function () {
                $('.tcs-tax-section').hide();
                const vendors = @json($vendor);
                const Tbltdstaxs = @json($Tbltdstax); // paginated — for modal table
                const tdstax     = @json($tdstax);     // all records — for TDS dropdown
                const Tbltcstaxs = @json($Tbltcstax);
                const gsttax = @json($gsttax);
                const Tblaccount = @json($Tblaccount);
                const TblZonesModel = @json($TblZonesModel);
                const Tbltdssection = @json($Tbltdssection);
                const Tblcompany = @json($Tblcompany);
                const TblDeliveryAddress = @json($TblDeliveryAddress);
                // Populate TDS dropdown with ALL records so every tax is selectable
                tdstax.forEach(function(tax) {
                        const item = $(`
                            <div data-value="${tax.tax_rate}" data-id="${tax.id}">${tax.tax_name}  [${tax.tax_rate}%]</div>
                        `);
                        $('.tax-list').append(item);
                    });
                Tbltcstaxs.forEach(Tbltcstax => {
                        const item = $(`
                            <div data-value="${Tbltcstax.tax_rate}" data-id="${Tbltcstax.id}">${Tbltcstax.tax_name}  [${Tbltcstax.tax_rate}%]</div>
                        `);
                        $('.tax-tcs-list').append(item);
                    });
                    gsttax.forEach(gsttax => {
                        const item = $(`
                            <div data-type="${gsttax.tax_type}" data-value="${gsttax.tax_rate}" data-id="${gsttax.id}">${gsttax.tax_name}  [${gsttax.tax_rate}%]</div>
                        `);
                        $('.tax-gst-list').append(item);
                    });
                    Tblaccount.forEach(Tblgsttax => {
                      const item = $(`
                      <div data-id="${Tblgsttax.id}">${Tblgsttax.name} </div>
                      `);
                      $('.account-list').append(item);
                    });
                    TblDeliveryAddress.forEach(TblDeliveryAddress => {
                      const item = $(`
                      <div data-id="${TblDeliveryAddress.id}">${TblDeliveryAddress.address} </div>
                      `);
                      $('.delivery-list').append(item);
                    });
                    (Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data || [])).forEach(locations => {
                      const item = $(`
                      <div data-id="${locations.id}">${locations.name} </div>
                      `);
                      $('.zone-list').append(item);
                    });
                    Tbltdssection.data.forEach(Tbltdssection => {
                        const item = $(`
                            <div data-value="${Tbltdssection.name}" data-id="${Tbltdssection.id}">${Tbltdssection.name}</div>
                        `);
                        $('.tds-section-list').append(item);
                    });
                    Tblcompany.data.forEach(Tblcompany => {
                        const item = $(`
                            <div data-value="${Tblcompany.company_name}" data-id="${Tblcompany.id}">${Tblcompany.company_name}</div>
                        `);
                        $('.company-list').append(item);
                    });
                //  initCustomerSearch($('.item-row:first .search-customer-dropdown'));
                const $searchInput = $('#vendor-search');
                const $dropdown = $('#vendor-dropdown');
                const $innerSearch = $('.inner-search');
                const $vendorList = $('.vendor-list');

                function renderVendors(filter = '') {
                    $vendorList.empty();
                    const filtered = vendors.filter(v =>
                        (v.vendor_first_name + ' ' + v.vendor_last_name).toLowerCase().includes(filter.toLowerCase()) ||
                        (v.email || '').toLowerCase().includes(filter.toLowerCase()) ||
                        (v.company_name || '').toLowerCase().includes(filter.toLowerCase())
                    );

                    if (!filtered.length) {
                        $vendorList.append('<div class="vendor-item">No vendors found</div>');
                        return;
                    }

                    filtered.forEach(vendor => {
                        const item = $(`
                            <div class="vendor-item" data-id="${vendor.id}">
                                <div class="vendor-name">${vendor.vendor_first_name} ${vendor.vendor_last_name || ''}</div>
                                <div class="vendor-details">
                                    ${vendor.email ? `<span class="vendor-email"><i class="fa fa-envelope"></i> ${vendor.email}</span>` : ''}
                                    ${vendor.company_name ? `<span class="vendor-other"><i class="fa fa-building"></i> ${vendor.company_name}</span>` : ''}
                                </div>
                            </div>
                        `);
                        $vendorList.append(item);
                    });
                }

                // Show dropdown
                $searchInput.on('focus click', function () {
                    $dropdown.addClass('show');
                    $innerSearch.focus();
                });

                // Hide dropdown when clicking outside
                $(document).on('click', function (e) {
                    if (!$(e.target).closest('.search-dropdown').length) {
                        $dropdown.removeClass('show');
                    }
                });

                // Filter on input
                $innerSearch.on('input', function () {
                    renderVendors($(this).val());
                });

                // Select vendor
                $vendorList.on('click', '.vendor-item', function () {
                  $('.vendor-item').removeClass('selected');
                  const vendorId = $(this).data('id');
                   vendor = vendors.find(v => v.id == vendorId);

                  // Fill input
                  $searchInput.val(`${vendor.display_name}`);
                  $('#selected-vendor-id').val(vendorId);
                  $dropdown.removeClass('show');
                  $(this).addClass('selected');
                  // Fill billing address
                  if (vendor.billing_address) {
                      const b = vendor.billing_address;
                      const billingHtml = `
                          <div><strong>BILLING ADDRESS <i class="fa fa-link"></i></strong></div>
                          <div>${b.address ?? ''}, ${b.city ?? ''}</div>
                          <div>${b.state ?? ''} ${b.pincode ?? ''}</div>
                          <div>${b.country ?? ''}</div>
                          <div>Phone: ${b.phone ?? ''}</div>
                      `;
                      $('#billing-address').show();
                      $('#billing-address').html(billingHtml);
                  } else {
                      $('#billing-address').html('<div><strong>BILLING ADDRESS</strong></div><div>No billing address available.</div>');
                  }
                  if (vendor.shipping_address) {
                      const b = vendor.shipping_address;
                      const shippingHtml = `
                          <div><strong>SHIPPING ADDRESS <i class="fa fa-link"></i></strong></div>
                          <div>${b.address ?? ''}, ${b.city ?? ''}</div>
                          <div>${b.state ?? ''} ${b.pincode ?? ''}</div>
                          <div>${b.country ?? ''}</div>
                          <div>Phone: ${b.phone ?? ''}</div>
                      `;
                      $('#shipping-address').show();
                      $('#shipping-address').html(shippingHtml);
                  } else {
                      $('#shipping-address').html('<div><strong>BILLING ADDRESS</strong></div><div>No billing address available.</div>');
                  }
                  console.log("vendor",vendor);
                  calculateSubTotal();
                  calculateFinalTotals();
                });



                // New vendor handler
                $('.new-vendor-option .vendor-item').on('click', function () {
                    $searchInput.val('');
                    $('#selected-vendor-id').val('new');
                    $dropdown.removeClass('show');
                    $('.vendor-item').removeClass('selected');
                    $('#billing-address').hide();
                    // Handle modal or redirect for new vendor creation
                });

                // Initial render
                renderVendors();
                 flatpickr('.datepicker', {
                    dateFormat: 'd/m/Y',
                    allowInput: true
                });

                $(document).on('input change', '.quantity, .rate, .selected-gst-tax,.gst_tax_type', function() {
                  console.log("vendor_out",vendor);
                  const row = $(this).closest('tr');
                    const quantity = parseFloat(row.find('.quantity').val()) || 0;
                    const rate = parseFloat(row.find('.rate').val()) || 0;
                    const gst_percent = parseFloat(row.find('.selected-gst-tax').val()) || 0;
                    const gst_type = row.find('.gst_tax_type').val();

                    const amount = quantity * rate;
                    let gst_amount = (amount * gst_percent) / 100;
                    let html = '';
                    if (gst_type === "GST") {
                        let cgst_percent = gst_percent / 2;
                        let sgst_percent = gst_percent / 2;
                        let cgst_amount_in = gst_amount / 2;
                        let sgst_amount_in = gst_amount / 2;
                        row.find('.cgst_amount').val(cgst_amount_in.toFixed(2));
                        row.find('.sgst_amount').val(sgst_amount_in.toFixed(2));
                    }
                    else if (gst_type === "IGST") {
                      row.find('.cgst_amount').val(0);
                        row.find('.sgst_amount').val(0);

                    }

                    row.find('.amount').val(amount.toFixed(2));
                    row.find('.gst_amount').val(gst_amount.toFixed(2));

                });


                  $(document).on('input', '.quantity, .rate ', function() {
                    calculateSubTotal();
                    calculateFinalTotals();
                  });

                  // Calculate when discount or tax changes
                  $('.discount-percent, .discount_type, .tax-select, .adjustment-value,.selected-tds-tax,.selected-tcs-tax,.discount_type,.export_amount').on('input change', function () {
                    calculateFinalTotals();
                  });
                  $('.discount-percent, .discount_type').on('input change', function () {
                    gstcalculate();
                  });
                 $(document).on('input change', '.selected-gst-tax', function () {
                    calculateSubTotal();
                });

                  $('input[name="tax_type"]').on('change', function () {
                    if ($(this).val() === 'TDS') {
                      $('.tds-tax-section').show();
                      $('.tcs-tax-section').hide();
                       $('.selected-tcs-tax').val('');
                      $('.tax-tcs-search-input').val('');
                    } else {
                      $('.tcs-tax-section').show();
                      $('.tds-tax-section').hide();
                      $('.selected-tds-tax').val('');
                      $('.tax-search-input').val('');
                    }
                    $('.dropdown-menu.tax-dropdown').hide(); // Close any open dropdown
                  });
                  $('input[name="gst_type"]').on('change', function () {
                    if ($(this).val() === 'GST') {
                      $('.gst_show_container').show();
                      $('.selected-gst-tax').trigger('change');
                    } else {
                      $('.selected-gst-tax').trigger('change');
                      $('.gst_show_container').hide();
                    }
                    $('.dropdown-menu.tax-dropdown').hide(); // Close any open dropdown
                  });

                //   // Show dropdown on input click
                //   $('.tax-search-input').on('click', function () {
                //     $('.dropdown-menu.tax-dropdown').hide(); // hide others
                //     $(this).siblings('.dropdown-menu').toggle();
                //   });
                //   // Show dropdown on input click
                //   $('.tax-tcs-search-input').on('click', function () {
                //     $('.dropdown-menu.tax-dropdown').hide(); // hide others
                //     $(this).siblings('.dropdown-menu').toggle();
                //   });
                //   // Show dropdown on input click
                // //  $(document).on('click', '.gst-search-input', function () {
                // //     $('.dropdown-menu.tax-dropdown').hide(); // hide all
                // //     $(this).siblings('.dropdown-menu').toggle(); // show current
                // //   });

                // tds dropdown
                $(document).on('click', '.tax-search-input', function (e) {
                    e.stopPropagation();
                    $(this).val('');
                    $('.dropdown-menu.tax-dropdown').hide();

                    const $input = $(this);
                    if ($input.hasClass('tds-locked')) {
                        e.preventDefault();
                        return; // don't open dropdown when locked
                      }
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

                  // Open GST dropdown
                  $(document).on('click', '.gst-search-input', function (e) {
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
                  $(document).on('click', '.tax-gst-list div', function () {
                      const selectedText = $(this).text().trim();
                      const selecteddata = $(this).data('value');
                      const selectedid = $(this).data('id');
                      const selectedtype = $(this).data('type');

                      const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                      const wrapper = $dropdown.data('wrapper');
                      const row = $dropdown.data('row');

                      if (!wrapper || !row) {
                          console.warn("Wrapper or row not found — GST selection failed.");
                          $dropdown.hide();
                          return;
                      }
                      wrapper.find('.gst-search-input').val(selectedText);
                      if (wrapper.hasClass('gst-section')) {
                          row.find('.selected-gst-tax').val(selecteddata).trigger('change');
                      }
                      row.find('.gst-tax-id').val(selectedid);
                      row.find('.gst_tax_type').val(selectedtype).trigger('change');

                      $dropdown.hide();
                      $('.dropdown-menu.tax-dropdown').hide();
                      gstcalculate(row);
                      calculateFinalTotals();
                  });

                  // Hide dropdown on outside click
                  $(document).on('click', function () {
                      $('.dropdown-menu.tax-dropdown').hide();
                  });


                  //account
                  $(document).on('click', '.account-search-input', function (e) {
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
                  $(document).on('click', '.account-list div', function () {
                      const selectedText = $(this).text().trim();
                      const selecteddata = $(this).data('value');
                      const selectedid = $(this).data('id');
                      const selectedtype = $(this).data('type');
                      console.log("selectedText",selectedText);

                      const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                      const wrapper = $dropdown.data('wrapper');
                      const row = $dropdown.data('row');

                      if (!wrapper || !row) {
                          console.warn("Wrapper or row not found — GST selection failed.");
                          $dropdown.hide();
                          return;
                      }
                      wrapper.find('.account-search-input').val(selectedText);
                      row.find('.account_id').val(selectedid);
                      row.find('.account_name').val(selectedText);

                      $dropdown.hide();
                  });
                  //delivery
                  $(document).on('click', '.delivery-search-input', function (e) {
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
                  $(document).on('click', '.delivery-list div', function () {
                      const selectedText = $(this).text().trim();
                      const selecteddata = $(this).data('value');
                      const selectedid = $(this).data('id');
                      const selectedtype = $(this).data('type');
                      console.log("selectedText",selectedText);

                      const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                      const wrapper = $dropdown.data('wrapper');
                      const row = $dropdown.data('row');

                      if (!wrapper || !row) {
                          console.warn("Wrapper or row not found — GST selection failed.");
                          $dropdown.hide();
                          return;
                      }
                      wrapper.find('.delivery-search-input').val(selectedText);
                      wrapper.find('.delivery_id').val(selectedid);
                      wrapper.find('.delivery_name').val(selectedText);

                      $dropdown.hide();
                  });

                  //zone
                  $(document).on('click', '.zone-search-input', function (e) {
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
                  $(document).on('click', '.zone-list div', function () {
                      const selectedText = $(this).text().trim();
                      const selecteddata = $(this).data('value');
                      const selectedid = $(this).data('id');

                      const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                      const wrapper = $dropdown.data('wrapper');
                      const row = $dropdown.data('row');

                      if (!wrapper || !row) {
                          console.warn("Wrapper or row not found — GST selection failed.");
                          $dropdown.hide();
                          return;
                      }
                      wrapper.find('.zone-search-input').val(selectedText);
                      wrapper.find('.zone_id').val(selectedid).trigger('click');

                      $dropdown.hide();
                  });
                  //branch
                  $(document).on('click', '.branch-search-input', function (e) {
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
                  $(document).on('click', '.branch-list div', function () {
                      const selectedText = $(this).text().trim();
                      const selecteddata = $(this).data('value');
                      const selectedid = $(this).data('id');

                      const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                      const wrapper = $dropdown.data('wrapper');
                      const row = $dropdown.data('row');

                      if (!wrapper || !row) {
                          console.warn("Wrapper or row not found — GST selection failed.");
                          $dropdown.hide();
                          return;
                      }
                      wrapper.find('.branch-search-input').val(selectedText);
                      wrapper.find('.branch_id').val(selectedid);

                      $dropdown.hide();
                  });
                  //company
                  $(document).on('click', '.company-search-input', function (e) {
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
                  $(document).on('click', '.company-list div', function () {
                      const selectedText = $(this).text().trim();
                      const selecteddata = $(this).data('value');
                      const selectedid = $(this).data('id');

                      const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                      const wrapper = $dropdown.data('wrapper');
                      const row = $dropdown.data('row');

                      if (!wrapper || !row) {
                          console.warn("Wrapper or row not found — GST selection failed.");
                          $dropdown.hide();
                          return;
                      }
                      wrapper.find('.company-search-input').val(selectedText);
                      wrapper.find('.company_id').val(selectedid);
                      $dropdown.hide();
                  });

                  $('.zone_id').on('click', function () {
                    var id=$('.zone_id').val();
                    let formData = new FormData();
                    formData.append('id',id);
                    $.ajax({
                        url: '{{ route("superadmin.getbranchfetch") }}',
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        success: function (response) {
                          toastr.success(response.message);
                          if(response.branch !==""){
                            $('.branch-list div').remove();
                            response.branch.forEach(branch => {
                                const item = $(`
                                  <div data-id="${branch.id}">${branch.name} </div>
                                `);
                                $('.branch-list').append(item);
                            });
                          }
                        },
                        error: function (xhr) {
                            console.error("Error saving form:", xhr);
                            toastr.error("Something went wrong.");
                        }
                    });
                  });
                  $('.zone-list div').on('click',function(){
                    $('.branch-search-input').val('');
                    $('.branch_id').val('');
                  })
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
                  $('.zone-search-input').on('keyup', function () {
                    const searchText = $(this).val().toLowerCase();
                    const $dropdown = $(this).data('dropdown');

                    if ($dropdown) {
                      const list = $dropdown.find('.zone-list div');
                      list.each(function () {
                        const itemText = $(this).text().toLowerCase();
                        $(this).toggle(itemText.includes(searchText));
                      });
                    }
                  });
                  $('.branch-search-input').on('keyup', function () {
                    const searchText = $(this).val().toLowerCase();
                    const $dropdown = $(this).data('dropdown');

                    if ($dropdown) {
                      const list = $dropdown.find('.branch-list div');
                      list.each(function () {
                        const itemText = $(this).text().toLowerCase();
                        $(this).toggle(itemText.includes(searchText));
                      });
                    }
                  });
                  $('.company-search-input').on('keyup', function () {
                    const searchText = $(this).val().toLowerCase();
                    const $dropdown = $(this).data('dropdown');

                    if ($dropdown) {
                      const list = $dropdown.find('.company-list div');
                      list.each(function () {
                        const itemText = $(this).text().toLowerCase();
                        $(this).toggle(itemText.includes(searchText));
                      });
                    }
                  });

                //   $('.gst-search-input').on('keyup', function () {
                //     const searchText = $(this).val().toLowerCase();
                //     const $dropdown = $(this).data('dropdown');
                //     if ($dropdown) {
                //       const list = $dropdown.find('.tax-gst-list div');
                //       list.each(function () {
                //         const itemText = $(this).text().toLowerCase();
                //         $(this).toggle(itemText.includes(searchText));
                //       });
                //     }
                //   });
                //   $('.account-search-input').on('keyup', function () {
                //     console.log(1213);

                //     const searchText = $(this).val().toLowerCase();
                //     const $dropdown = $(this).data('dropdown');
                //     if ($dropdown) {
                //       const list = $dropdown.find('.account-list div');
                //       list.each(function () {
                //         const itemText = $(this).text().toLowerCase();
                //         $(this).toggle(itemText.includes(searchText));
                //       });
                //     }
                //   });
                  $(document).on('keyup','.gst-search-input', function () {
                      const searchText = $(this).val().toLowerCase();
                      const $dropdown = $(this).data('dropdown');
                      if ($dropdown) {
                        const list = $dropdown.find('.tax-gst-list div');
                        list.each(function () {
                          const itemText = $(this).text().toLowerCase();
                          $(this).toggle(itemText.includes(searchText));
                        });
                      }
                    });
                    $(document).on('keyup','.account-search-input', function () {
                        const searchText = $(this).val().toLowerCase();
                        const $dropdown = $(this).data('dropdown');
                        if ($dropdown) {
                        const list = $dropdown.find('.account-list div');
                        list.each(function () {
                            const itemText = $(this).text().toLowerCase();
                            $(this).toggle(itemText.includes(searchText));
                        });
                        }
                    });
                  $('.delivery-search-input').on('keyup', function () {
                    const searchText = $(this).val().toLowerCase();
                    const $dropdown = $(this).data('dropdown');
                    if ($dropdown) {
                      const list = $dropdown.find('.delivery-list div');
                      list.each(function () {
                        const itemText = $(this).text().toLowerCase();
                        $(this).toggle(itemText.includes(searchText));
                      });
                    }
                  });


                  // // Select item from dropdown
                  // $('.tax-list div').on('click', function () {
                  //   const selectedText = $(this).text().trim();
                  //   const selecteddata = $(this).data('value');
                  //   const selectedid = $(this).data('id');
                  //   const wrapper = $(this).closest('.tax-dropdown-wrapper');
                  //   wrapper.find('.tax-search-input').val(selectedText);
                  //   if (wrapper.hasClass('tds-tax-section')) {
                  //     wrapper.find('.selected-tds-tax').val(selecteddata).trigger('change');
                  //   }
                  //   wrapper.find('.tds-tax-id').val(selectedid);
                  //   wrapper.find('.dropdown-menu').hide();
                  // });
                  // $('.tax-tcs-list div').on('click', function () {
                  //   const selectedText = $(this).text().trim();
                  //   const selecteddata = $(this).data('value');
                  //   const selectedid = $(this).data('id');
                  //   const wrapper = $(this).closest('.tax-dropdown-wrapper');
                  //   wrapper.find('.tax-tcs-search-input').val(selectedText);
                  //   if (wrapper.hasClass('tcs-tax-section')) {
                  //     wrapper.find('.selected-tcs-tax').val(selecteddata).trigger('change');
                  //   }
                  //   wrapper.find('.tcs-tax-id').val(selectedid);
                  //   wrapper.find('.dropdown-menu').hide();
                  // });
                  // $('.tax-gst-list div').on('click', function () {
                  //   const selectedText = $(this).text().trim();
                  //   const selecteddata = $(this).data('value');
                  //   const selectedid = $(this).data('id');
                  //   const wrapper = $(this).closest('.tax-dropdown-wrapper');
                  //   wrapper.find('.gst-search-input').val(selectedText);
                  //   if (wrapper.hasClass('gst-section')) {
                  //     wrapper.find('.selected-gst-tax').val(selecteddata).trigger('change');
                  //   }
                  //   wrapper.find('.gst-tax-id').val(selectedid);
                  //   wrapper.find('.dropdown-menu').hide();
                  // });
                  //  $('#openPurchaseBtn').click(function() {
                  //   $('#purchaseModal').modal('show');
                  // });
                  // $(document).on('click', '.tax-gst-list div', function () {
                  //     const selectedText = $(this).text().trim();
                  //     const selecteddata = $(this).data('value');
                  //     const selectedid = $(this).data('id');
                  //     const selectedtype = $(this).data('type');
                  //     const wrapper = $(this).closest('.tax-dropdown-wrapper');
                  //     const row = $(this).closest('tr');
                  //     wrapper.find('.gst-search-input').val(selectedText);

                  //     if (wrapper.hasClass('gst-section')) {
                  //        row.find('.selected-gst-tax').val(selecteddata).trigger('change');
                  //     }
                  //     row.find('.gst-tax-id').val(selectedid);

                  //     // ✅ Set GST type in hidden field
                  //     row.find('.gst_tax_type').val(selectedtype).trigger('change');
                  //     wrapper.find('.dropdown-menu').hide();
                  //   });


                  // Optional: Click outside to close dropdown
                  $(document).on('click', function (e) {
                    if (!$(e.target).closest('.tax-dropdown-wrapper').length) {
                      $('.dropdown-menu.tax-dropdown').hide();
                    }
                  });
                  $(document).on('click', '.dropdown-menu.tax-dropdown', function (e) {
                      e.stopPropagation();
                  });
                  // $('.dropdown-menu.tax-dropdown').on('click',function (e) {
                  //     e.stopPropagation();
                  // });
                  // tds tax
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
                    let status=true;
                    if($('.tds_name').val() ===''){
                      $('.tds_name_error').text("TDS Name Required");
                      status=false;
                    }
                    if($('.tds_rate').val() ===''){
                      status=false;
                      $('.tds_rate_error').text("TDS Rate Required");
                    }
                    if(!status){
                      return;
                    }
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

                              // Clear old list before appending fresh data (use all records from tdstax_all)
                              $('.tax-list').empty();

                              const allTds = response.tdstax_all || (response.tdstax && response.tdstax.data) || [];
                              allTds.forEach(function(tax) {
                                  const item = $(`
                                      <div data-value="${tax.tax_rate}" data-id="${tax.id}">${tax.tax_name}  [${tax.tax_rate}%]</div>
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
                    let status=true;
                    if($('.section_name').val() === ''){
                      $('.section_name_error').text("Name Required");
                      status=false;
                    }
                    if(!status){
                      return;
                    }
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

                  // tcs tax
                  $(document).on('click', '.manage-tcs-link', function () {
                    $('#tcsModal').fadeIn();
                    $('body').addClass('no-scroll'); // Disable background scroll
                    $('.dropdown-menu.tax-dropdown').hide();
                  });

                  $(document).on('click', '.close-modal-tcs', function () {
                    $('#tcsModal').fadeOut();
                    $('body').removeClass('no-scroll'); // Re-enable scroll
                  });

                  $(document).on('click', '.btn-new-tcs', function () {
                    $('#tcsModal').hide();
                    $('#newTcsModal').fadeIn();
                  });

                  $(document).on('click', '.close-new-modal-tcs', function () {
                    $('#newTcsModal').fadeOut();
                    $('#tcsModal').fadeIn(); // Go back to manage TDS
                  });

                  $(window).on('click', function (e) {
                    if ($(e.target).is('#tdsModal')) {
                      $('#tdsModal').fadeOut();
                      $('body').removeClass('no-scroll');
                    }
                    if ($(e.target).is('#tcsModal')) {
                      $('#tcsModal').fadeOut();
                      $('body').removeClass('no-scroll');
                    }
                  });
                  $(document).on('click', '.tcs_save', function () {
                    const formData = new FormData();
                    formData.append('name', $('.tcs_name').val());
                    formData.append('rate', $('.tcs_rate').val());
                    formData.append('start_date', $('.tcs_start_date').val());
                    formData.append('end_date', $('.tcs_end_date').val());
                     $.ajax({
                        url: '{{ route("superadmin.gettcssave") }}', // Update with your actual endpoint
                        type: "POST",
                        data: formData,
                        processData: false, // Prevent processing of the data
                        contentType: false, // Prevent setting content-type header
                        success: function (response) {
                            if (response.success) {
                                $('#newTcsModal').fadeOut();
                                $('#tcsModal').fadeOut();
                                $('body').removeClass('no-scroll');
                            }
                        },
                        error: function (error) {
                            if (error.responseJSON && error.responseJSON.errors) {
                                $.each(error.responseJSON.errors, function(key, value) {
                                    $('.error_' + key).text(value[0]);
                                });
                            } else {
                                console.error(error.responseJSON);
                            }
                        },
                    });
                  });
                  // gst tax
                  $(document).on('click', '.manage-gst-link', function () {
                    $('#gstModal').fadeIn();
                    $('body').addClass('no-scroll'); // Disable background scroll
                    $('.dropdown-menu.tax-dropdown').hide();
                  });

                  $(document).on('click', '.close-modal-gst', function () {
                    $('#gstModal').fadeOut();
                    $('body').removeClass('no-scroll'); // Re-enable scroll
                  });

                  $(document).on('click', '.btn-new-gst', function () {
                    $('#gstModal').hide();
                    $('#newgstModal').fadeIn();
                  });

                  $(document).on('click', '.close-new-modal-gst', function () {
                    $('#newgstModal').fadeOut();
                    $('#gstModal').fadeIn(); // Go back to manage TDS
                  });

                  $(window).on('click', function (e) {
                    if ($(e.target).is('#tdsModal')) {
                      $('#tdsModal').fadeOut();
                      $('body').removeClass('no-scroll');
                    }
                    if ($(e.target).is('#tcsModal')) {
                      $('#tcsModal').fadeOut();
                      $('body').removeClass('no-scroll');
                    }
                    if ($(e.target).is('#gstModal')) {
                      $('#gstModal').fadeOut();
                      $('body').removeClass('no-scroll');
                    }
                  });
                  $(document).on('click', '.gst_save', function (e) {
                    let status=true;
                    if($('.gst_name').val() ===''){
                      $('.gst_name_error').text("GST Name Required");
                      status=false;
                    }
                    if($('.gst_rate').val() ===''){
                      $('.gst_rate_error').text("GST Rate Required");
                      status=false;
                    }
                    if(!status){
                      return;
                    }
                   const formData = new FormData();
                    formData.append('name', $('.gst_name').val());
                    formData.append('rate', $('.gst_rate').val());
                    formData.append('tax_type', $('.gst_type').val());
                    formData.append('start_date', $('.gst_start_date').val());
                    formData.append('end_date', $('.gst_end_date').val());
                     $.ajax({
                        url: '{{ route("superadmin.getgstsave") }}',// Update with your actual endpoint
                        type: "POST",
                        data: formData,
                        processData: false, // Prevent processing of the data
                        contentType: false, // Prevent setting content-type header
                        success: function (response) {

                           if (response.success) {
                               $('#newgstModal').fadeOut();
                                $('#gstModal').fadeOut();
                                $('body').removeClass('no-scroll');

                              // Clear old list before appending fresh data
                              $('.tax-gst-list').empty();

                              response.gsttax.data.forEach(Tblgsttax => {
                                  const item = $(`
                                      <div data-type="${Tblgsttax.tax_type}" data-value="${Tblgsttax.tax_rate}" data-id="${Tblgsttax.id}">${Tblgsttax.tax_name}  [${Tblgsttax.tax_rate}%]</div>
                                  `);
                                  $('.tax-gst-list').append(item);
                              });

                              // Clear inputs
                              $('.gst_name').val('');
                              $('.gst_rate').val('');
                              $('.gst_start_date').val('');
                              $('.gst_end_date').val('');
                              $('.gst_type').val('');
                          }

                        },
                        error: function (error) {
                            if (error.responseJSON && error.responseJSON.errors) {
                                $.each(error.responseJSON.errors, function(key, value) {
                                    $('.error_' + key).text(value[0]);
                                });
                            } else {
                                console.error(error.responseJSON);
                            }
                        },
                    });
                  });


                   // account tax
                  $(document).on('click', '.manage-account-link', function () {
                    $('#newaccModal').fadeIn();
                    $('body').addClass('no-scroll'); // Disable background scroll
                    $('.dropdown-menu.tax-dropdown').hide();
                  });
                  $(document).on('click', '.close-new-modal-acc', function () {
                    $('#newaccModal').fadeOut();
                    $('body').removeClass('no-scroll');
                  });
                  $(document).on('click', '.acc_save', function (e) {
                    e.preventDefault();

                    let status = true;
                    $('.acc_name_error').text(''); // clear previous errors

                    if ($('.acc_name').val() === '') {
                        $('.acc_name_error').text("Account Name Required");
                        status = false;
                    }

                    if (!status) return;

                    const formData = new FormData();
                    formData.append('name', $('.acc_name').val());
                    formData.append('code', $('.account_code').val());
                    formData.append('description', $('#account_description').val());

                    $.ajax({
                        url: '{{ route("superadmin.getaccountsave") }}',
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message);

                                $('#newaccModal').fadeOut();
                                $('body').removeClass('no-scroll');

                                // Clear old list before appending fresh data
                                $('.account-list').empty();

                                response.account.data.forEach(function (acc) {
                                    const newAcc = $(`
                                        <div data-id="${acc.id}">
                                            ${acc.name}
                                        </div>
                                    `);
                                    $('.account-list').append(newAcc);
                                });

                                // Clear inputs
                                $('.acc_name').val('');
                                $('.account_code').val('');
                                $('#account_description').val('');
                            } else {
                                toastr.warning(response.message);
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 409 && xhr.responseJSON && xhr.responseJSON.message) {
                                // ⚠️ Duplicate name error
                                toastr.error(xhr.responseJSON.message);
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                // ⚠️ Validation errors
                                $.each(xhr.responseJSON.errors, function (key, value) {
                                    toastr.warning(value[0]);
                                });
                            } else {
                                toastr.error('An unexpected error occurred. Please try again.');
                                console.error(xhr.responseJSON);
                            }
                        },
                    });
                });
                $(document).on('click', '.manage-delivery-link', function () {
                    $('#newdeliveryModal').fadeIn();
                    $('body').addClass('no-scroll'); // Disable background scroll
                    $('.dropdown-menu.tax-dropdown').hide();
                  });
                  $(document).on('click', '.close-new-modal-delivery', function () {
                    $('#newdeliveryModal').fadeOut();
                    $('body').removeClass('no-scroll');
                  });
                  $(document).on('click', '.delivery_save', function (e) {
                    e.preventDefault();

                    let status = true;
                    $('#delivery_address__modal_error').val(''); // clear previous errors

                    if ($('#delivery_address_modal').val() === '') {
                        $('.delivery_address__modal_error').text("Address Required");
                        status = false;
                    }

                    if (!status) return;

                    const formData = new FormData();
                    formData.append('address', $('#delivery_address_modal').val());

                    $.ajax({
                        url: '{{ route("superadmin.getdeliverysave") }}',
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message);

                                $('#newdeliveryModal').fadeOut();
                                $('body').removeClass('no-scroll');

                                // Clear old list before appending fresh data
                                $('.delivery-list').empty();

                                response.data.forEach(function (delivery) {
                                    const newdelivery = $(`
                                        <div data-id="${delivery.id}">
                                            ${delivery.address}
                                        </div>
                                    `);
                                    $('.delivery-list').append(newdelivery);
                                });

                                $('#delivery_address_modal').val('');
                            } else {
                                toastr.warning(response.message);
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 409 && xhr.responseJSON && xhr.responseJSON.message) {
                                // ⚠️ Duplicate name error
                                toastr.error(xhr.responseJSON.message);
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                // ⚠️ Validation errors
                                $.each(xhr.responseJSON.errors, function (key, value) {
                                    toastr.warning(value[0]);
                                });
                            } else {
                                toastr.error('An unexpected error occurred. Please try again.');
                                console.error(xhr.responseJSON);
                            }
                        },
                    });
                });


            });
});

           $(document).ready(function () {

              // Open file input on upload button click
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


            });



              $(document).ready(function () {

                function submitBillForm(saveStatus, $btn) {
                    $btn.prop('disabled', true);
                    let isValid = true;
                    if ($('.search-input').val() === "") {
                        $('.error_vendor_name').text('Vendor Required');
                        isValid = false;
                    }
                    if ($('.delivery-search-input').val() === "") {
                        $('.error_delivery').text('Delivery Address Required');
                        isValid = false;
                    }
                    if ($('#order_number').val() === "") {
                        $('.error_order_no').text('Enter Order No ');
                        isValid = false;
                    }
                    if ($('#bill_date').val() === "") {
                        $('.error_bill_date').text('Bill Date Required');
                        isValid = false;
                    }
                    if ($('#due_date').val() === "") {
                        $('.error_due_date').text('Due Date Required');
                        isValid = false;
                    }
                    if ($('.zone-search-input').val() === "") {
                        $('.error_zone').text('Zone Required');
                        isValid = false;
                    }
                    if ($('.branch-search-input').val() === "") {
                        $('.error_branch').text('Branch Required');
                        isValid = false;
                    }
                    if (!isValid) {
                        return;
                    }

                    // Save original button text
                    let originalText = $btn.html();
                    // Show loader on button
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                    // Append status to the form before serialize
                    $('#billForm').append('<input type="hidden" name="save_status" value="' + saveStatus + '" />');

                    let formData = new FormData($('#billForm')[0]);
                    formData.append('esi_type', $('.esi_type').val());
                    formData.append('esi_value', $('.esi_value').val());
                    formData.append('esi_amount', $('.esi-display-amount').text());
                    formData.append('pf_type', $('.pf_type').val());
                    formData.append('pf_value', $('.pf_value').val());
                    formData.append('pf_amount', $('.pf-display-amount').text());
                    formData.append('other_type', $('.other_type').val());
                    formData.append('other_value', $('.other_value').val());
                    formData.append('other_amount', $('.other-display-amount').text());
                    formData.append('other_reason', $('.other_reason').val());
                    formData.append('sub_total_amount', $('.sub-total-amount').text());
                    formData.append('discount_amount', $('.discount-amount').text());
                    formData.append('adjustment_amount', $('.adjustment-amount').text());
                    formData.append('tax_amount', $('.tax-amount').text());
                    formData.append('discount_toggle', $('.discount-toggle').text());
                    formData.append('grand_total_amount', $('.grand-total-amount').text());

                    $.ajax({
                        url: '{{ route("superadmin.savequotation") }}',
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        success: function (response) {
                            toastr.success(response.message);
                            if (response.restore_filters) {
                                sessionStorage.setItem(
                                    "restore_filters",
                                    true
                                );
                            }
                            setTimeout(() => {
                              const urlParams = new URLSearchParams(window.location.search);
                              const redirectPage = urlParams.get('redirect_page') || 1;

                              let redirectUrl =
                                  "{{ route('superadmin.getquotation') }}" +
                                  "?page=" + redirectPage +
                                  "&redirect_page=" + redirectPage;

                              console.log("redirectUrl", redirectUrl);
                              window.location.href = redirectUrl;
                          }, 1500);

                            // setTimeout(() => {
                            //     window.location.href = "{{ route('superadmin.getquotation') }}";
                            // }, 1000);
                            setTimeout(() => {
                                $btn.prop('disabled', false);
                            }, 3000);
                        },
                        error: function (xhr) {
                            console.error("Error saving form:", xhr);
                            toastr.error("Something went wrong!");
                        },
                        // complete: function () {
                        //     // Reset button back
                        //     $btn.prop('disabled', false).html(originalText);
                        // }
                    });
                }

                $('#saveDraftBtn').on('click', function () {
                    submitBillForm('draft', $(this));
                });

                $('#saveOpenBtn').on('click', function () {
                    submitBillForm('save', $(this));
                });

                $('.cancel-btn').on('click', function () {
                    window.location.href = "{{ route('superadmin.getquotation') }}";
                });
            });


          $(document).ready(function () {
            $(document).on('click', '.documentclk', function (e) {
              e.preventDefault();
               if ($(e.target).is('.remove-file1')) {
                return;
              }
              $('#documentModal1').modal('show');

              const fileType = $(this).data('filetype');
              const filesData = $(this).attr('data-files');
              let fileArray = [];
              console.log("filesData",filesData);


              try {
                fileArray = JSON.parse(filesData);
                if (typeof fileArray === 'string') {
                  fileArray = JSON.parse(fileArray);
                }
              } catch (e) {
                console.error('Invalid JSON in data-files:', filesData);
                $('#image_pdfs').html('<p>Invalid file data</p>');
                return;
              }

              if (!Array.isArray(fileArray) || fileArray.length === 0) {
                $('#image_pdfs').html('<p>No files found</p>');
                return;
              }


              let views = '';
              fileArray.forEach(file => {
                const firstFile = `../public/uploads/vendor/bill/${file}`;
                $('#pdfmain').attr('src', firstFile);
                const fileName = file.split('/').pop().trim();
                views += `<button style="font-size: 11px;" type="button" class="btn btn-primary pdf-btn" data-filepath="../uploads/vendor/bill/${file}">${fileName}</button>`;
              });

              $('#image_pdfs').html(views);
            });

            // Global handler for pdf buttons
            $(document).on('click', '.pdf-btn', function () {
              $('.pdf-btn').removeClass('active');
              $(this).addClass('active');
              const filePath = $(this).data('filepath');
              $('#pdfmain').attr('src', filePath);
            });
          });

    </script>
  @if ($type == 'edit')
    @if(!empty($quotation))
    <script>
      $(document).ready(function () {
          const quotation_header = @json($quotation);
          const quotation_lines = @json($quotation[0]->BillLines);
          console.log("quotation_header",quotation_header);

          const vendor_id = quotation_header[0].vendor_id;
          setTimeout(function () {
              const $vendorItem = $('.vendor-item[data-id="' + vendor_id + '"]');

              if ($vendorItem.length) {
                  $vendorItem.trigger('click');
              } else {
                  console.warn('Vendor item not found for ID:', vendor_id);
              }
          }, 100);
          $('#id').val(quotation_header[0].id);
          $('#quotation_no').val(quotation_header[0].quotation_no);
          $('#quotation_gen_no').val(quotation_header[0].quotation_gen_no);
          // $('#delivery_address').val(quotation_header[0].delivery_address);
          $('#order_number').val(quotation_header[0].order_number);
          $('#bill_date').val(quotation_header[0].bill_date);
          $('#due_date').val(quotation_header[0].due_date);
          $('#payment_terms').val(quotation_header[0].payment_terms);
          $('#subject').val(quotation_header[0].subject);
          $('.zone-search-input').val(quotation_header[0].zone_name);
          $('.zone_id').val(quotation_header[0].zone_id);
          $('.branch-search-input').val(quotation_header[0].branch_name);
          $('.branch_id').val(quotation_header[0].branch_id);
          $('.company-search-input').val(quotation_header[0].company_name);
          $('.company_id').val(quotation_header[0].company_id);
          $('.discount_type').val(quotation_header[0].discount_type);
          $('.delivery-search-input').val(quotation_header[0].delivery_address);
          $('.delivery_id').val(quotation_header[0].delivery_id);

          let isFirst = true;

          quotation_lines.forEach((lines, index) => {
              let currentRow;

              if (isFirst) {
                  currentRow = $('.item-row:first');
                  isFirst = false;
              } else {
                  $('.add-row').trigger('click');
                  currentRow = $('.item-row').last();
              }

              // Slight delay to ensure the row is fully ready
              setTimeout(() => {
                  currentRow.find('.item-id').val(lines.id);
                  currentRow.find('.item-details').val(lines.item_details);
                  currentRow.find('.account-search-input').val(lines.account);
                  currentRow.find('.account_name').val(lines.account);
                  currentRow.find('.account_id').val(lines.account_id);
                  currentRow.find('.quantity').val(lines.quantity);
                  currentRow.find('.rate').val(lines.rate);
                  currentRow.find('.gst-search-input').val(lines.gst_name);
                  currentRow.find('.selected-gst-tax').val(lines.gst_rate).trigger('change'); // dropdown must exist
                  currentRow.find('.gst-tax-id').val(lines.gst_tax_id);
                  currentRow.find('.gst_tax_type').val(lines.gst_type);
                  currentRow.find('.cgst_amount').val(lines.cgst_amount);
                  currentRow.find('.sgst_amount').val(lines.sgst_amount);
                  currentRow.find('.gst_amount').val(lines.gst_amount);
                  currentRow.find('.selected-customer-id').val(lines.customer);
                  currentRow.find('.amount').val(lines.amount);
                  gstcalculate(currentRow);
              }, 100); // 100ms is enough for most DOM rendering
          });


        // $('.discount-percent').val(quotation_header[0].discount_percent).trigger('change');
      function formatCurrency(value) {
          let amount = parseFloat(value || 0);
          return '₹' + amount.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          });
        }
        setTimeout(() => {
          if (quotation_header.length > 0) {
            const qh = quotation_header[0];
            if(qh.discount_tax === 'Apply After Tax'){
              $('.discount-toggle').trigger('click');
            }
            $('.discount-percent').val(qh.discount_percent).trigger('change');
            $('.discount-amount').text(formatCurrency(qh.discount_amount));
            $('.adjustment-amount').text(formatCurrency(qh.adjustment_value));
            $('.adjustment-reason').val(qh.adjustment_reason);
            $('.export_name').val(qh.export_name);
            $('.export_amount').val(qh.export_amount);
            $('.tax-amount').text(formatCurrency(qh.tax_amount));
            $('.grand-total-amount').text(formatCurrency(qh.grand_total_amount));
            $('.adjustment-value').val(qh.adjustment_value).trigger('change');

            if (qh.tax_type === "TDS") {
              $('input[name="tax_type"][value="TDS"]').prop('checked', true).trigger('change');
              $('.tax-search-input').val(qh.tax_name);
              $('.selected-tds-tax').val(qh.tax_rate).trigger('change');
              $('.tds-tax-id').val(qh.tds_tax_id);
            }
          }
        }, 100);
        $('.esi_value').val(quotation_header[0].esi_value);
        $('.esi_type').val(quotation_header[0].esi_type);
        $('.pf_value').val(quotation_header[0].pf_value);
        $('.pf_type').val(quotation_header[0].pf_type);
        $('.other_value').val(quotation_header[0].other_value);
        $('.other_type').val(quotation_header[0].other_type);
        $('.other_reason').val(quotation_header[0].other_reason);
        $('.esi-display-amount').text(formatCurrency(quotation_header[0].esi_amount));
        $('.pf-display-amount').text(formatCurrency(quotation_header[0].pf_amount));
        $('.other-display-amount').text(formatCurrency(quotation_header[0].other_amount));
        if(quotation_header[0].esi_amount !==null){
          $('.tax_show_single').css('display','block');
        }
        $('#notes').val(quotation_header[0].note);

          // From server
          window.existingFiles = JSON.parse(quotation_header[0].documents); // array of strings
          window.selectedFiles = []; // New files chosen
          $('#existingFilesInput').val(JSON.stringify(window.existingFiles));
          // Render existing files
          function renderExistingFiles() {
            $('#fileList').empty();
            $.each(window.existingFiles, function (index, name) {
              let files = Array.isArray(name) ? name : [name];
              $('#fileList').append(`
                <li class="documentclk"
                                data-filetype="document"
                                data-files="${JSON.stringify(files).replace(/"/g, '&quot;')}">
                                ${name}
                                <span class="remove-file1" data-type="existing" data-index="${index}"
                                    style="cursor:pointer; color:red;">❌</span>
                            </li>
              `);
            });
            $.each(window.selectedFiles, function (index, file) {
              let files = Array.isArray(file) ? file : [file];
              $('#fileList').append(`
                <li class="documentclk"
                    data-filetype="document"
                    data-files="${JSON.stringify(files).replace(/"/g, '&quot;')}">
                    ${file.name}
                    <span class="remove-file1" data-type="existing" data-index="${index}"
                        style="cursor:pointer; color:red;">❌</span>
                </li>
              `);
            });
            $('#existingFilesInput').val(JSON.stringify(window.existingFiles));
          }

          renderExistingFiles();

          // New file input change
          $('#fileInput').on('change', function (e) {
            window.selectedFiles = Array.from(e.target.files); // overwrite
            renderExistingFiles();
          });

          // Remove handler
          $('#fileList').on('click', '.remove-file1', function () {
            const index = $(this).data('index');
            const type = $(this).data('type');

            if (type === 'existing') {
              window.existingFiles.splice(index, 1);
            } else {
              window.selectedFiles.splice(index, 1);
              $('#fileInput').val(''); // Clear file input
            }

            renderExistingFiles();
          });

        $('#fileInput').val(window.files);



      });

    </script>
    @endif
  @elseif($type == 'clone')
    @if(!empty($quotation))
    <script>
      $(document).ready(function () {
          const quotation_header = @json($quotation);
          const quotation_lines = @json($quotation[0]->BillLines);
          const vendor_id = quotation_header[0].vendor_id;
          setTimeout(function () {
              const $vendorItem = $('.vendor-item[data-id="' + vendor_id + '"]');

              if ($vendorItem.length) {
                  $vendorItem.trigger('click');
              } else {
                  console.warn('Vendor item not found for ID:', vendor_id);
              }
          }, 100);
          // $('#delivery_address').val(quotation_header[0].delivery_address);
          $('#bill_date').val(quotation_header[0].bill_date);
          $('#due_date').val(quotation_header[0].due_date);
          $('#payment_terms').val(quotation_header[0].payment_terms);
          $('.zone-search-input').val(quotation_header[0].zone_name);
          $('.zone_id').val(quotation_header[0].zone_id);
          $('.branch-search-input').val(quotation_header[0].branch_name);
          $('.branch_id').val(quotation_header[0].branch_id);
          $('.company-search-input').val(quotation_header[0].company_name);
          $('.company_id').val(quotation_header[0].company_id);
          $('.discount_type').val(quotation_header[0].discount_type);
          $('.delivery-search-input').val(quotation_header[0].delivery_address);
          $('.delivery_id').val(quotation_header[0].delivery_id);

          let isFirst = true;
          quotation_lines.forEach((lines, index) => {
              let currentRow;
              if (isFirst) {
                  currentRow = $('.item-row:first');
                  isFirst = false;
              } else {
                  $('.add-row').trigger('click');
                  currentRow = $('.item-row').last();
              }
              // Slight delay to ensure the row is fully ready
              setTimeout(() => {
                  currentRow.find('.item-id').val(lines.id);
                  currentRow.find('.item-details').val(lines.item_details);
                  currentRow.find('.account-search-input').val(lines.account);
                  currentRow.find('.account_name').val(lines.account);
                  currentRow.find('.account_id').val(lines.account_id);
                  currentRow.find('.quantity').val(lines.quantity);
                  currentRow.find('.rate').val(lines.rate);
                  currentRow.find('.gst-search-input').val(lines.gst_name);
                  currentRow.find('.selected-gst-tax').val(lines.gst_rate).trigger('change'); // dropdown must exist
                  currentRow.find('.gst-tax-id').val(lines.gst_tax_id);
                  currentRow.find('.gst_tax_type').val(lines.gst_type);
                  currentRow.find('.cgst_amount').val(lines.cgst_amount);
                  currentRow.find('.sgst_amount').val(lines.sgst_amount);
                  currentRow.find('.gst_amount').val(lines.gst_amount);
                  currentRow.find('.selected-customer-id').val(lines.customer);
                  currentRow.find('.amount').val(lines.amount);
                  gstcalculate(currentRow);
              }, 100); // 100ms is enough for most DOM rendering
          });

        // $('.discount-percent').val(quotation_header[0].discount_percent).trigger('change');
      function formatCurrency(value) {
          let amount = parseFloat(value || 0);
          return '₹' + amount.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          });
        }
        setTimeout(() => {
          if (quotation_header.length > 0) {
            const qh = quotation_header[0];
            if(qh.discount_tax === 'Apply After Tax'){
              $('.discount-toggle').trigger('click');
            }
            $('.discount-percent').val(qh.discount_percent).trigger('change');
            $('.discount-amount').text(formatCurrency(qh.discount_amount));
            $('.adjustment-amount').text(formatCurrency(qh.adjustment_value));
            $('.adjustment-reason').val(qh.adjustment_reason);
            $('.export_name').val(qh.export_name);
            $('.export_amount').val(qh.export_amount);
            $('.tax-amount').text(formatCurrency(qh.tax_amount));
            $('.grand-total-amount').text(formatCurrency(qh.grand_total_amount));
            $('.adjustment-value').val(qh.adjustment_value).trigger('change');

            if (qh.tax_type === "TDS") {
              $('input[name="tax_type"][value="TDS"]').prop('checked', true).trigger('change');
              $('.tax-search-input').val(qh.tax_name);
              $('.selected-tds-tax').val(qh.tax_rate).trigger('change');
              $('.tds-tax-id').val(qh.tds_tax_id);
            }
          }
        }, 100);
        $('.esi_value').val(quotation_header[0].esi_value);
        $('.esi_type').val(quotation_header[0].esi_type);
        $('.pf_value').val(quotation_header[0].pf_value);
        $('.pf_type').val(quotation_header[0].pf_type);
        $('.other_value').val(quotation_header[0].other_value);
        $('.other_type').val(quotation_header[0].other_type);
        $('.other_reason').val(quotation_header[0].other_reason);
        $('.esi-display-amount').text(formatCurrency(quotation_header[0].esi_amount));
        $('.pf-display-amount').text(formatCurrency(quotation_header[0].pf_amount));
        $('.other-display-amount').text(formatCurrency(quotation_header[0].other_amount));
        if(quotation_header[0].esi_amount !==null){
          $('.tax_show_single').css('display','block');
        }
        $('#notes').val(quotation_header[0].note);

          // From server
          window.existingFiles = JSON.parse(quotation_header[0].documents); // array of strings
          window.selectedFiles = []; // New files chosen
          $('#existingFilesInput').val(JSON.stringify(window.existingFiles));
          // Render existing files
          function renderExistingFiles() {
            $('#fileList').empty();
            $.each(window.existingFiles, function (index, name) {
              let files = Array.isArray(name) ? name : [name];
              $('#fileList').append(`
                <li class="documentclk"
                    data-filetype="document"
                    data-files="${JSON.stringify(files).replace(/"/g, '&quot;')}">
                    ${name}
                    <span class="remove-file1" data-type="existing" data-index="${index}"
                        style="cursor:pointer; color:red;">❌</span>
                </li>
              `);
            });
            $.each(window.selectedFiles, function (index, file) {
              let files = Array.isArray(file) ? file : [file];
              $('#fileList').append(`
                <li class="documentclk"
                    data-filetype="document"
                    data-files="${JSON.stringify(files).replace(/"/g, '&quot;')}">
                    ${file.name}
                    <span class="remove-file1" data-type="existing" data-index="${index}"
                        style="cursor:pointer; color:red;">❌</span>
                </li>
              `);
            });
            $('#existingFilesInput').val(JSON.stringify(window.existingFiles));
          }

          renderExistingFiles();

          // New file input change
          $('#fileInput').on('change', function (e) {
            window.selectedFiles = Array.from(e.target.files); // overwrite
            renderExistingFiles();
          });

          // Remove handler
          $('#fileList').on('click', '.remove-file1', function () {
            const index = $(this).data('index');
            const type = $(this).data('type');
            if (type === 'existing') {
              window.existingFiles.splice(index, 1);
            } else {
              window.selectedFiles.splice(index, 1);
              $('#fileInput').val(''); // Clear file input
            }
            renderExistingFiles();
          });
        $('#fileInput').val(window.files);
      });

    </script>
    @endif
  @endif
    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
