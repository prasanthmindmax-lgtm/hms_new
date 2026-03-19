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
          <h1>NEFT PAYMENT</h1>

          <form id="billForm" method="POST" action="">
              @csrf

            <div class="container mt-4">

                <div class="row">

                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">SI. No</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_serial errorss"></span>
                                <input type="hidden" id="id" name="id">
                                <input type="hidden" id="branch_id" name="branch_id" value="{{ $admin?->branch_id  }}">
                                <input type="hidden" id="users_id" name="users_id" value="{{ $admin?->id}}">
                                <input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px"  required name="serial_number" id="serial_number" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Created by:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_created_by errorss"></span>
                            <input type="text" class="form-control" id="created_by" name="created_by" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Created by" value="{{ $admin?->user_fullname ?? '' }}" >
                        </div>
                    </div>
                      <div class="col-sm-3">
                        <div class="row">
                            <div class="col-sm-12 mb-3">
                                <label class="form-label required" style="font-size: 12px;font-weight: 600;">Vendor/ Employee Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_special errorss"></span>
                                <div class="col-md-12">
                                  <div class="search-dropdown">
                                      <input type="text" id="vendor-search" class="form-control search-input" name="vendor_name" placeholder="Search vendor..." autocomplete="off">
                                      <div class="dropdown-menu" id="vendor-dropdown">
                                          <div class="search-box">
                                              <input type="text" placeholder="Search" class="inner-search form-control mb-2">
                                          </div>
                                          <div class="vendor-list"></div>
                                          <div class="new-vendor-option">
                                              <div class="vendor-item new-vendor">
                                                  <div class="vendor-name"><i class="fa fa-plus"></i> New Vendor</div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>

                                  <input type="hidden" id="selected-vendor-id" name="vendor_id">
                              </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Vendor/Employee Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_vendor errorss"></span>
                            <input type="text" class="form-control" id="vendor" name="vendor" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Vendor/Employee Name" >
                        </div>
                    </div> -->
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Nature of Payment:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_nature_payment errorss"></span>
                            <!-- <input type="text" class="form-control" id="description" name="description" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Nature of Payment" > -->
                            <select class="form-control" id="nature_payment" name="nature_payment" style="height: 42px;">
                                <option value="">Select Status</option>
                                <option value="Travell Allowance" >Travell Allowance</option>
                                <option value="Expense" >Expense</option>
                                <option value="Imprest" >Imprest</option>
                                <!-- <option value="NEFT">NEFT</option>
                                <option value="RTGS">RTGS</option>
                                <option value="IDR">IDR</option>
                                <option value="Cheque">Cheque</option>
                                <option value="DD">DD</option>
                                <option value="Internet Banking">Internet Banking</option>
                                <option value="Card Swipe">Card Swipe</option> -->
                            </select>
                        </div>
                    </div>

                </div>
                <div class="row">
                      <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Invoice Amount:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_invoice_amount errorss"></span>
                            <input type="text" class="form-control" id="invoice_amount" name="invoice_amount" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Invoice Amount" >
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Already Paid:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_already_paid errorss"></span>
                            <input type="text" class="form-control" id="already_paid" name="already_paid" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Already Paid" >
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">PAN Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_pan_number errorss"></span>
                            <input type="text" class="form-control" id="pan_number" name="pan_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="PAN Number">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Account Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_account_number errorss"></span>
                            <input type="text" class="form-control" id="account_number" name="account_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Account Number">
                        </div>
                    </div>

                </div>
                <div class="row">
                      <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">TDS:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_invoice_amount errorss"></span>
                             <!-- TDS TAX SELECT -->
                            <div class="col-md-12 tax-dropdown-wrapper tds-tax-section">
                              <input type="text" class="form-control tax-search-input" name="tds_tax_name" placeholder="Select a Tax" readonly>
                              <input type="hidden" name="tds_tax_selected" class="selected-tds-tax" id="tds_tax_value">
                              <input type="hidden" name="tds_tax_id" class="tds-tax-id">
                              <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container">
                                  <input type="text" class="tax-inner-search" placeholder="Search...">
                                </div>
                                <div class="tax-list">

                                </div>
                                <div class="manage-tds-link">⚙️ Manage TDS</div>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Tax Amount</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_already_paid errorss"></span>
                            <input type="text" class="form-control" id="already_paid" name="already_paid" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Already Paid" >
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Only Payable</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_pan_number errorss"></span>
                            <input type="text" class="form-control" id="pan_number" name="pan_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="PAN Number">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Account Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_account_number errorss"></span>
                            <input type="text" class="form-control" id="account_number" name="account_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Account Number">
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">IFSC Code:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_ifsc_code errorss"></span>
                            <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="IFSC Code">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Payment Status:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_payment_status errorss"></span>
                            <select class="form-control" id="payment_status" name="payment_status" style="height: 42px;">
                                <option value="">Select Status</option>
                                <option value="Success" >Success</option>
                                <option value="Failed" >Failed</option>
                                <option value="Return" >Return</option>
                                <!-- <option value="NEFT">NEFT</option>
                                <option value="RTGS">RTGS</option>
                                <option value="IDR">IDR</option>
                                <option value="Cheque">Cheque</option>
                                <option value="DD">DD</option>
                                <option value="Internet Banking">Internet Banking</option>
                                <option value="Card Swipe">Card Swipe</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">UTR Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_utr_number errorss"></span>
                            <input type="text" class="form-control" id="utr_number" name="utr_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="UTR Number" >
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mb-3">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Payment Method:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_payment_method errorss"></span>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_neft" name="payment_method[]" value="NEFT">
                                        <label class="form-check-label" for="payment_neft" style="font-size: 12px;">NEFT</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_rtgs" name="payment_method[]" value="RTGS">
                                        <label class="form-check-label" for="payment_rtgs" style="font-size: 12px;">RTGS</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_cheque" name="payment_method[]" value="Cheque">
                                        <label class="form-check-label" for="payment_cheque" style="font-size: 12px;">Cheque</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_dd" name="payment_method[]" value="DD">
                                        <label class="form-check-label" for="payment_dd" style="font-size: 12px;">DD</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_idhs" name="payment_method[]" value="IDhS">
                                        <label class="form-check-label" for="payment_idhs" style="font-size: 12px;">IDhS</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_internet" name="payment_method[]" value="Internet Banking">
                                        <label class="form-check-label" for="payment_internet" style="font-size: 12px;">Internet Banking</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_card" name="payment_method[]" value="Card Swipe">
                                        <label class="form-check-label" for="payment_card" style="font-size: 12px;">Card Swipe</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <!-- Bank Upload -->
                  <div class="col-sm-4 mb-4">
                    <label class="form-label">Bank Proof</label><br>
                    <label class="upload-btn" for="bank_upload">
                      <i class="fas fa-upload"></i> Upload File
                    </label>
                    <input type="file" id="bank_upload" name="bank_upload[]" multiple accept="image/*,application/pdf">
                    <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                    <ul id="preview_bank_upload" style="list-style: none; padding-left: 0;"></ul>
                  </div>

                  <!-- Invoice Upload -->
                  <div class="col-sm-4 mb-4">
                    <label class="form-label">Invoice Upload</label><br>
                    <label class="upload-btn" for="invoice_upload">
                      <i class="fas fa-upload"></i> Upload File
                    </label>
                    <input type="file" id="invoice_upload" name="invoice_upload[]" multiple accept="image/*,application/pdf">
                    <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                    <ul id="preview_invoice_upload" style="list-style: none; padding-left: 0;"></ul>
                  </div>

                  <!-- PAN Upload -->
                  <div class="col-sm-4 mb-4">
                    <label class="form-label">PAN Upload</label><br>
                    <label class="upload-btn" for="pan_upload">
                      <i class="fas fa-upload"></i> Upload File
                    </label>
                    <input type="file" id="pan_upload" name="pan_upload[]" multiple accept="image/*,application/pdf">
                    <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                    <ul id="preview_pan_upload" style="list-style: none; padding-left: 0;"></ul>
                  </div>

                  <!-- PO Upload -->
                  <div class="col-sm-4 mb-4">
                    <label class="form-label">PO Attachment</label><br>
                    <label class="upload-btn" for="po_upload">
                      <i class="fas fa-upload"></i> Upload File
                    </label>
                    <input type="file" id="po_upload" name="po_upload[]" multiple accept="image/*,application/pdf">
                    <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                    <ul id="preview_po_upload" style="list-style: none; padding-left: 0;"></ul>
                  </div>

                  <!-- PO Signed Copy Upload -->
                  <div class="col-sm-4 mb-4">
                    <label class="form-label">PO Signed Copy Upload</label><br>
                    <label class="upload-btn" for="po_signed_upload">
                      <i class="fas fa-upload"></i> Upload File
                    </label>
                    <input type="file" id="po_signed_upload" name="po_signed_upload[]" multiple accept="image/*,application/pdf">
                    <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                    <ul id="preview_po_signed_upload" style="list-style: none; padding-left: 0;"></ul>
                  </div>

                  <!-- PO Delivery Copy Upload -->
                  <div class="col-sm-4 mb-4">
                    <label class="form-label">PO Delivery Copy Upload</label><br>
                    <label class="upload-btn" for="po_delivery_upload">
                      <i class="fas fa-upload"></i> Upload File
                    </label>
                    <input type="file" id="po_delivery_upload" name="po_delivery_upload[]" multiple accept="image/*,application/pdf">
                    <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                    <ul id="preview_po_delivery_upload" style="list-style: none; padding-left: 0;"></ul>
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
                  <table class="tds-table">
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
                  </table>
                </div>
              </div>
            </div>

            <!-- New TDS Tax Modal -->
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
                        <input type="text" class="form-control tds_name" />
                      </div>
                      <div style="flex: 1;">
                        <label>Rate (%) <span style="color: red">*</span></label>
                        <input type="text" class="form-control tds_rate" />
                      </div>
                    </div>

                    <h5 style="margin: 10px 0;">Applicable Period</h5>
                    <div style="display: flex; gap: 10px;">
                      <div style="flex: 1;">
                        <label>Start Date</label>
                        <input type="text" class="form-control datepicker tds_start_date" placeholder="dd/MM/yyyy" />
                      </div>
                      <div style="flex: 1;">
                        <label>End Date</label>
                        <input type="text" class="form-control datepicker tds_end_date" placeholder="dd/MM/yyyy" />
                      </div>
                    </div>
                    <br />
                    <button class="btn-save tds_save" type="submit">Save</button>
                    <button class="btn-cancel close-new-modal" type="button">Cancel</button>
                  </form>
                </div>
              </div>
            </div>
            {{-- tds modal --}}



          </form>
            <div class="action-buttons">
              <button type="button" id="saveOpenBtn" class="btn open-btn">Save</button>
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
    toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

             $(document).ready(function () {
                $('.tcs-tax-section').hide();
                const vendors = @json($vendor);
                const Tbltdstaxs = @json($Tbltdstax);
                const Tbltcstaxs = @json($Tbltcstax);
                // alert(JSON.stringify(Tbltdstaxs));
                Tbltdstaxs.forEach(Tbltdstax => {
                        const item = $(`
                            <div data-value="${Tbltdstax.tax_rate}" data-id="${Tbltdstax.id}">${Tbltdstax.tax_name}  [${Tbltdstax.tax_rate}%]</div>
                        `);
                        $('.tax-list').append(item);
                    });
                Tbltcstaxs.forEach(Tbltcstax => {
                        const item = $(`
                            <div data-value="${Tbltcstax.tax_rate}" data-id="${Tbltcstax.id}">${Tbltcstax.tax_name}  [${Tbltcstax.tax_rate}%]</div>
                        `);
                        $('.tax-tcs-list').append(item);
                    });
                //  initCustomerSearch($('.item-row:first .search-customer-dropdown'));
                const $searchInput = $('#vendor-search');
                const $dropdown = $('#vendor-dropdown');
                const $innerSearch = $('.inner-search');
                const $vendorList = $('.vendor-list');

                function renderVendors(filter = '') {
                    $vendorList.empty();
                    const filtered = vendors.filter(v =>
                        (v.vendor_first_name + ' ' + v.vendor_last_name).toLowerCase().includes(filter.toLowerCase())
                    );

                    if (!filtered.length) {
                        $vendorList.append('<div class="vendor-item">No vendors found</div>');
                        return;
                    }

                    filtered.forEach(vendor => {
                        const item = $(`
                            <div class="vendor-item" data-id="${vendor.id}">
                                <div class="vendor-name">${vendor.vendor_first_name} ${vendor.vendor_last_name || ''}</div>

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
                  // alert(12);
                  const vendorId = $(this).data('id');
                  const vendor = vendors.find(v => v.id == vendorId);

                  // Fill input
                  $searchInput.val(`${vendor.vendor_first_name} ${vendor.vendor_last_name}`);
                  $('#selected-vendor-id').val(vendorId);
                  $dropdown.removeClass('show');
                  $(this).addClass('selected');
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

                  // Calculate amount when quantity or rate changes
                  $(document).on('input', '.quantity, .rate', function() {
                    const row = $(this).closest('tr');
                    const quantity = parseFloat(row.find('.quantity').val()) || 0;
                    const rate = parseFloat(row.find('.rate').val()) || 0;
                    const amount = quantity * rate;
                    row.find('.amount').val(amount.toFixed(2));
                    // calculateTotals();
                  });
                  $(document).on('input', '.quantity, .rate', function() {
                    calculateSubTotal();
                    calculateFinalTotals();
                  });

                  // Calculate when discount or tax changes
                  $('.discount-percent, .discount-type, .tax-select, .adjustment-value,.selected-tds-tax,.selected-tcs-tax').on('input change', function () {
                    calculateFinalTotals();
                  });


                  // Show dropdown on input click
                  $('.tax-search-input').on('click', function () {
                    $('.dropdown-menu.tax-dropdown').hide(); // hide others
                    $(this).siblings('.dropdown-menu').toggle();
                  });
                  // Show dropdown on input click
                  $('.tax-tcs-search-input').on('click', function () {
                    $('.dropdown-menu.tax-dropdown').hide(); // hide others
                    $(this).siblings('.dropdown-menu').toggle();
                  });

                  // Search filter
                  $('.tax-inner-search').on('keyup', function () {
                    const searchText = $(this).val().toLowerCase();
                    const list = $(this).closest('.dropdown-menu').find('.tax-list div');

                    list.each(function () {
                      const itemText = $(this).text().toLowerCase();
                      $(this).toggle(itemText.includes(searchText));
                    });
                  });

                  // Select item from dropdown
                  $('.tax-list div').on('click', function () {
                    const selectedText = $(this).text();
                    const selecteddata = $(this).data('value');
                    const selectedid = $(this).data('id');
                    const wrapper = $(this).closest('.tax-dropdown-wrapper');
                    wrapper.find('.tax-search-input').val(selectedText);
                    if (wrapper.hasClass('tds-tax-section')) {
                      wrapper.find('.selected-tds-tax').val(selecteddata).trigger('change');
                    }
                    wrapper.find('.tds-tax-id').val(selectedid);
                    wrapper.find('.dropdown-menu').hide();
                  });
                  $('.tax-tcs-list div').on('click', function () {
                    const selectedText = $(this).text();
                    const selecteddata = $(this).data('value');
                    const selectedid = $(this).data('id');
                    const wrapper = $(this).closest('.tax-dropdown-wrapper');
                    wrapper.find('.tax-tcs-search-input').val(selectedText);
                    if (wrapper.hasClass('tcs-tax-section')) {
                      wrapper.find('.selected-tcs-tax').val(selecteddata).trigger('change');
                    }
                    wrapper.find('.tcs-tax-id').val(selectedid);
                    wrapper.find('.dropdown-menu').hide();
                  });

                  // Optional: Click outside to close dropdown
                  $(document).on('click', function (e) {
                    if (!$(e.target).closest('.tax-dropdown-wrapper').length) {
                      $('.dropdown-menu.tax-dropdown').hide();
                    }
                  });
                  // tds tax
                  $(document).on('click', '.manage-tds-link', function () {
                    $('#tdsModal').fadeIn();
                    $('body').addClass('no-scroll'); // Disable background scroll
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


            });

            function calculateSubTotal() {
              let subTotal = 0;

              // Calculate subtotal from all rows
              $('.item-row').each(function() {
                const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                const rate = parseFloat($(this).find('.rate').val()) || 0;
                const amount = quantity * rate;
                subTotal += amount;
              });

              $('.sub-total-amount').text('₹' + subTotal.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              }));

              return subTotal;
            }

            function calculateFinalTotals() {
              // alert(122);
              const subTotal = calculateSubTotal();
              const discountPercent = parseFloat($('.discount-percent').val()) || 0;
              const discountAmount = subTotal * (discountPercent / 100);

              // const taxtdsRate = parseFloat($('.selected-tds-tax').val().trim());
              const taxtcsRate = parseFloat($('.selected-tcs-tax').val()) || 0;
              const taxtdsRate = parseFloat($('#tds_tax_value').val());

              let taxtdsAmount = 0;
              let taxtcsAmount = 0;
              let taxLabel = '';
              let taxDisplayAmount = 0;

              // Either TDS or TCS is applied — not both
              if (taxtcsRate > 0) {
                  taxtcsAmount = subTotal * (taxtcsRate / 100);
                  taxLabel = '₹';
                  taxDisplayAmount = taxtcsAmount;
              } else if (taxtdsRate >= 0) {
                  taxtdsAmount = subTotal * (taxtdsRate / 100);
                  taxLabel = '- ₹'; // negative symbol for TDS
                  taxDisplayAmount = taxtdsAmount;
              }

              const adjustment = parseFloat($('.adjustment-value').val()) || 0;

              const grandTotal = subTotal - discountAmount - taxtdsAmount + taxtcsAmount + adjustment;

              // Update UI
              $('.discount-amount').text('₹' + discountAmount.toLocaleString('en-IN', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
              }));

              $('.tax-amount').text(taxLabel + taxDisplayAmount.toLocaleString('en-IN', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
              }));

              $('.adjustment-amount').text('₹' + adjustment.toLocaleString('en-IN', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
              }));

              $('.grand-total-amount').text('₹' + grandTotal.toLocaleString('en-IN', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
              }));
          }


           $(document).ready(function () {
              const previewFiles = {};

              $('input[type="file"]').on('change', function () {
                const input = $(this);
                const inputId = input.attr('id');
                const preview = $(`#preview_${inputId}`);
                const files = Array.from(this.files);

                previewFiles[inputId] = files;
                preview.empty();

                files.forEach((file, index) => {
                  const fileHtml = `
                    <li class="file-preview-item" data-index="${index}">
                      <span><i class="fas fa-file"></i> ${file.name}</span>
                      <span class="remove-file-btn" data-input="${inputId}" data-index="${index}">❌</span>
                    </li>
                  `;
                  preview.append(fileHtml);
                });
              });

              $(document).on('click', '.remove-file-btn', function () {
                const inputId = $(this).data('input');
                const index = $(this).data('index');

                previewFiles[inputId].splice(index, 1);
                const dt = new DataTransfer();
                previewFiles[inputId].forEach(file => dt.items.add(file));
                $(`#${inputId}`)[0].files = dt.files;

                const preview = $(`#preview_${inputId}`);
                preview.empty();

                previewFiles[inputId].forEach((file, i) => {
                  const fileHtml = `
                    <li class="file-preview-item" data-index="${i}">
                      <span><i class="fas fa-file"></i> ${file.name}</span>
                      <span class="remove-file-btn" data-input="${inputId}" data-index="${i}">❌</span>
                    </li>
                  `;
                  preview.append(fileHtml);
                });
              });
            });



              $(document).ready(function () {

              function submitBillForm(saveStatus) {
                  // Append status to the form before serialize
                  $('#billForm').append('<input type="hidden" name="save_status" value="' + saveStatus + '" />');
                  let formData = new FormData($('#billForm')[0]);
                  console.log("formData",formData);

                  $.ajax({
                      url: '{{ route("superadmin.saveneft") }}', // 🔁 Replace with your real backend route
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
                            window.location.href = "{{ route('superadmin.getpurchaseorder') }}";
                        }, 1500);
                      },
                      error: function (xhr) {
                          console.error("Error saving form:", xhr);
                      }
                  });
              }

              $('#saveOpenBtn').on('click', function () {
                  submitBillForm('save');
              });
              // Tab switching functionality
            $('.cancel-btn').on('click', function() {
                window.location.href = "{{ route('superadmin.getneftdashboard') }}";
            });

          });




    </script>
    @if(!empty($purchase))
    <script>
      $(document).ready(function () {
                      const purchase_header = @json($purchase);
                      const purchase_lines = @json($purchase[0]->BillLines);
                      console.log("purchase_header",purchase_header);
                      console.log("purchase_lines",purchase_lines);

                      const vendor_id = purchase_header[0].vendor_id;
                      setTimeout(function () {
                          const $vendorItem = $('.vendor-item[data-id="' + vendor_id + '"]');

                          if ($vendorItem.length) {
                              $vendorItem.trigger('click');
                          } else {
                              console.warn('Vendor item not found for ID:', vendor_id);
                          }
                      }, 100);
                      $('#id').val(purchase_header[0].id);
                      $('#purchase_order').val(purchase_header[0].purchase_order_number);
                      $('#delivery_address').val(purchase_header[0].delivery_address);
                      $('#order_number').val(purchase_header[0].order_number);
                      $('#bill_date').val(purchase_header[0].bill_date);
                      $('#due_date').val(purchase_header[0].due_date);
                      $('#payment_terms').val(purchase_header[0].payment_terms);
                      $('#subject').val(purchase_header[0].subject);

                      let isFirst = true;
                      let rowCount = 1;

                      // Loop through each bill line
                      purchase_lines.forEach((lines, index) => {
                          let currentRow;

                          if (isFirst) {
                              // Use the first row in DOM
                              currentRow = $('.item-row:first');
                              isFirst = false;
                          } else {
                              // Trigger the click to add a row
                              $('.add-row').trigger('click');

                              // Since the DOM update is synchronous, we can immediately select the last row
                              currentRow = $('.item-row').last();
                          }

                          // Now fill the row with the bill line data
                          currentRow.find('.item-id').val(lines.id);
                          currentRow.find('.item-details').val(lines.item_details);
                          currentRow.find('.account-select').val(lines.account);
                          currentRow.find('.quantity').val(lines.quantity);
                          currentRow.find('.rate').val(lines.rate);
                          currentRow.find('#selected-customer-id').val(lines.customer);
                          currentRow.find('.amount').val(lines.amount);
                      });


                    $('.discount-percent').val(purchase_header[0].discount_percent).trigger('change');
                  function formatCurrency(value) {
                      let amount = parseFloat(value || 0);
                      return '₹' + amount.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                      });
                    }

                    $('.discount-amount').text(formatCurrency(purchase_header[0].discount_amount));
                    $('.adjustment-amount').text(formatCurrency(purchase_header[0].adjustment_value));
                    $('.tax-amount').text(formatCurrency(purchase_header[0].tax_amount));
                    $('.grand-total-amount').text(formatCurrency(purchase_header[0].grand_total_amount));

                    $('.adjustment-value').val(purchase_header[0].adjustment_value).trigger('change');
                    if(purchase_header[0].tax_type =="TDS"){
                      $('input[name="tax_type"][value="' + purchase_header[0].tax_type + '"]').prop('checked', true).trigger('change');
                      $('.tax-search-input').val(purchase_header[0].tax_name);
                      $('.selected-tds-tax').val(purchase_header[0].tax_rate).trigger('change');
                      $('.tds-tax-id').val(purchase_header[0].tds_tax_id);
                    }else{
                      $('input[name="tax_type"][value="' + purchase_header[0].tax_type + '"]').prop('checked', true).trigger('change');
                      $('.tax-tcs-search-input').val(purchase_header[0].tax_name);
                      $('.selected-tcs-tax').val(purchase_header[0].tax_rate).trigger('change');
                      $('.tcs-tax-id').val(purchase_header[0].tcs_tax_id);
                    }
                    $('#notes').val(purchase_header[0].note);

                      // From server
                      window.existingFiles = JSON.parse(purchase_header[0].documents); // array of strings
                      window.selectedFiles = []; // New files chosen
                      $('#existingFilesInput').val(JSON.stringify(window.existingFiles));
                      // Render existing files
                      function renderExistingFiles() {
                        $('#fileList').empty();
                        $.each(window.existingFiles, function (index, name) {
                          $('#fileList').append(`
                            <li>
                              ${name}
                              <span class="remove-file1" data-type="existing" data-index="${index}" style="cursor:pointer; color:red;">❌</span>
                            </li>
                          `);
                        });
                        $.each(window.selectedFiles, function (index, file) {
                          $('#fileList').append(`
                            <li>
                              ${file.name}
                              <span class="remove-file1" data-type="new" data-index="${index}" style="cursor:pointer; color:red;">❌</span>
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
    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>