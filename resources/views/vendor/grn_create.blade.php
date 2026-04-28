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
          <h1>New GRN</h1>

          <form id="billForm" method="POST" action="" enctype="multipart/form-data">
              @csrf

            <div class="container mt-4">
                <div class="row mb-3 align-items-start">
                    <!-- Vendor Name -->
                    <div class="col-md-6">
                        <div class="row mb-2 align-items-start">
                            <label for="vendor-search" class="col-md-4  fw-semibold">Vendor Name <span class="text-danger">*</span></label>
                            <input type="hidden" name="id" id="id">
                            <div class="col-md-8">
                                <div class="search-dropdown">
                                    <input type="text" id="vendor-search" class="form-control search-input" autocomplete="off" autocorrect="off"  name="vendor_name" placeholder="Search vendor..." autocomplete="off">
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
                                <input type="hidden" id="purchase_id" name="purchase_id">
                                <input type="hidden" id="bill_id" name="bill_id">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row mb-3">
                    <label for="grn_number" class="col-md-2 ">GRN Number <span class="text-danger">*</span></label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="grn_number" name="grn_number" autocomplete="off" autocorrect="off" value={{$grn_id ?? ''}}  readonly required>
                    </div>
                </div>
                <!-- Row 2: Bill#, Order Number -->
                {{-- @if($type == "bill")
                <div class="row mb-3">
                    <label for="bill_number" class="col-md-2 ">Bill# <span class="text-danger">*</span></label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="bill_number" name="bill_number"  readonly required>
                    </div>
                </div>
                @else
                <div class="row mb-3">
                    <label for="purchase_no" class="col-md-2 ">Quotation no# <span class="text-danger">*</span></label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="purchase_no" name="purchase_no"  readonly required>
                    </div>
                </div>
                @endif --}}
                <!-- Row 3: Bill#, Order Number -->
                <div class="row mb-3">
                    <label for="order_number" class="col-md-2 ">Invoice No / Bill No</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="order_number" name="order_number" autocomplete="off" autocorrect="off">
                    </div>
                </div>

                <!-- Row 4: Bill Date, Due Date -->
                <div class="row mb-3">
                    <label for="bill_date" class="col-md-2 ">Bill Date <span class="text-danger">*</span></label>
                    <div class="col-md-4">
                        <input type="text" class="form-control datepicker" id="bill_date" name="bill_date" autocomplete="off" autocorrect="off" placeholder="dd/MM/yyyy" required>
                    </div>
                </div>
                <!-- Row 5: Payment Terms -->
                <div class="row mb-3">
                    <label for="zone" class="col-md-2 ">Zones <span class="text-danger">*</span></label>
                    <div class="col-md-4">
                        {{-- <input type="text" class="form-control datepicker" id="due_date" name="due_date" > --}}
                      <div class="tax-dropdown-wrapper account-section" style="width:343px">
                          <input type="text" class="form-control zone-search-input" name="zone" autocomplete="off" autocorrect="off" placeholder="Select a Zones" readonly>
                          <input type="hidden" name="zone_id" class="zone_id">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="inner-search-container">
                              <input type="text" class="zone-inner-search" placeholder="Search...">
                            </div>
                            <div class="zone-list">
                            </div>
                          </div>
                          <span class="error_zone" style="color:red"></span>
                        </div>
                    </div>

                    <label for="branch" class="col-md-2 ">Branch <span class="text-danger">*</span></label>
                    <div class="col-md-4">
                        {{-- <input type="text" class="form-control datepicker" id="due_date" name="due_date" > --}}
                      <div class="tax-dropdown-wrapper account-section" style="width:343px">
                          <input type="text" class="form-control branch-search-input" name="branch" autocomplete="off" autocorrect="off" placeholder="Select a branch" readonly>
                          <input type="hidden" name="branch_id" class="branch_id">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="inner-search-container">
                              <input type="text" class="branch-inner-search" placeholder="Search...">
                            </div>
                            <div class="branch-list">
                            </div>
                          </div>
                          <span class="error_branch" style="color:red"></span>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="zone" class="col-md-2 ">Group of Company</label>
                    <div class="col-md-4">
                        {{-- <input type="text" class="form-control datepicker" id="due_date" name="due_date" > --}}
                      <div class="tax-dropdown-wrapper company-section" style="width:343px">
                          <input type="text" class="form-control company-search-input" name="company_name" autocomplete="off" autocorrect="off" placeholder="Select a Company" readonly>
                          <input type="hidden" name="company_id" class="company_id">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="inner-search-container">
                              <input type="text" class="company-inner-search" placeholder="Search...">
                            </div>
                            <div class="company-list">
                            </div>
                          </div>
                          <span class="error_zone" style="color:red"></span>
                        </div>
                    </div>
                    <label for="department" class="col-md-2 ">Department <span class="text-danger">*</span></label>
                    <div class="col-md-4">
                      <div class="tax-dropdown-wrapper department-section" style="width:343px">
                          <input type="text" class="form-control department-search-input" name="department_name" autocomplete="off" autocorrect="off" placeholder="Select a Department" readonly required>
                          <input type="hidden" name="department_id" class="department_id">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="inner-search-container">
                              <input type="text" class="department-inner-search" placeholder="Search...">
                            </div>
                            <div class="department-list">
                            </div>
                          </div>
                          <span class="error_department" style="color:red"></span>
                        </div>
                    </div>
                </div>

                <!-- Row 5: Payment Terms -->
                <div class="row mb-3">

                    <label for="due_date" class="col-md-2 ">Due Date</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control datepicker" id="due_date" autocomplete="off" autocorrect="off" name="due_date" >
                    </div>

                    {{--  <label for="payment_terms" class="col-md-2 ">Payment Terms</label>
                    <div class="col-md-4">
                        <select class="form-select" id="payment_terms" autocomplete="off" autocorrect="off" name="payment_terms">
                            <option value="Due on Receipt" selected>Due on Receipt</option>
                            <option value="Net 15">Net 15</option>
                            <option value="Net 30">Net 30</option>
                            <option value="Net 60">Net 60</option>
                        </select>
                    </div>  --}}
                </div>

                <div class="row mb-3">
                <label for="qc_ststus" class="col-md-2 ">QC Status</label>
                    <div class="col-md-4">
                        <select class="form-select" id="qc_ststus" name="qc_ststus" autocomplete="off" autocorrect="off">
                            <option value="Checked" selected>Checked</option>
                            <option value="Un Checked">Un Checked</option>
                        </select>
                    </div>
                <label for="qc_checked_by_display" class="col-md-2 ">QC Checked By <span class="text-danger" id="qc_checked_by_required_mark" title="Required when QC Status is Checked">*</span></label>
                <div class="col-md-4">
                    @php
                        $grnQcUserLabel = function ($u) {
                            $n = trim((string) (data_get($u, 'user_fullname', '') ?? '')) ?: 'User';
                            $e = data_get($u, 'username');
                            $eid = ($e !== null && (string) $e !== '') ? (string) $e : (string) (data_get($u, 'id', '') ?? '');

                            return $n . ' - ' . $eid;
                        };
                    @endphp
                      <div class="tax-dropdown-wrapper user-section" style="width:343px">
                          <input type="text" class="form-control user-search-input" name="qc_user" id="qc_checked_by_display" autocomplete="off" autocorrect="off" placeholder="Select a user" readonly>
                          <input type="hidden" name="qc_checked_by" class="qc_checked_by" id="qc_checked_by" value="">
                          <div class="dropdown-menu tax-dropdown grn-qc-dropdown">
                            <div class="inner-search-container">
                              <input type="text" class="user-inner-search" placeholder="Search...">
                            </div>
                            <div class="user-list">
                                @foreach($users as $user)
                                    <div data-id="{{ (int) $user->id }}">{{ $grnQcUserLabel($user) }}</div>
                                @endforeach
                            </div>
                          </div>
                          <span class="error_qc" style="color:red"></span>
                        </div>
                </div>
            </div>

                <!-- Row 6: Subject -->
                <div class="row mb-3">
                    <label for="subject" class="col-md-2 ">Subject</label>
                    <div class="col-md-4">
                        <textarea class="form-control" id="subject" name="subject" autocomplete="off" autocorrect="off" placeholder="Enter a subject within 250 characters" rows="2" maxlength="250"></textarea>
                    </div>
                    {{-- <label for="qc_ststus" class="col-md-2 ">QC Status</label>
                    <div class="col-md-4">
                        <select class="form-select" id="qc_ststus" name="qc_ststus" autocomplete="off" autocorrect="off">
                            <option value="Checked" selected>Checked</option>
                            <option value="Un Checked">Un Checked</option>
                        </select>
                    </div> --}}
                </div>
            </div>
            <div class="card">
              <div class="card-header">
                <h3>Item Table</h3>
              </div>
              <div class="card-body">
                <table class="table item-table">
                  <thead>
                    <tr>
                      <th>ITEM DETAILS</th>
                      <th>QUANTITY</th>
                      <th>RECEIVABLE</th>
                      <th>ACCEPTABLE</th>
                      <th>REJECTED</th>
                      <th>BALANCE</th>

                    </tr>
                  </thead>
                  <tbody>
                    <tr class="item-row">
                      <td>
                        <input type="hidden" name="linesdata[0][id]" class="form-control item-id" style="width:150px">
                        <input type="text" name="linesdata[0][item_details]" class="form-control item-details" style="width:200px" autocomplete="off" autocorrect="off" placeholder="Type or click to select an item">
                      </td>

                      <td>
                        <input type="number" name="linesdata[0][quantity]" class="form-control quantity" autocomplete="off" autocorrect="off"value="1.00" min="0" step="0.01">
                      </td>
                      <td>
                        <input type="number" name="linesdata[0][receivable_quantity]" class="form-control receivable_quantity" autocomplete="off" autocorrect="off" value="0" min="0" step="1">
                      </td>
                      <td>
                        <input type="number" name="linesdata[0][acceptable_quantity]" class="form-control acceptable_quantity" autocomplete="off" autocorrect="off"  value="0" min="0" step="1">
                      </td>
                      <td>
                        <input type="number" name="linesdata[0][reject_quantity]" class="form-control reject_quantity" autocomplete="off" autocorrect="off"  value="0" min="0" step="1">
                      </td>
                      <td>
                        <input type="number" name="linesdata[0][balance_quantity]" class="form-control balance_quantity" autocomplete="off" autocorrect="off" value="0" min="0" step="1">
                      </td>

                      {{-- <td>
                        <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
                      </td> --}}
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="text-start mb-3" style="display:none">
                  <button type="button" class="btn btn-primary add-row">Add New Row</button>
                </div>
            </div>

              <div class="notes-upload-wrapper grn-create-uploads-3col">
                <!-- Notes Section -->
                <div class="notes-section">
                  <label for="notes">Notes</label>
                  <textarea id="notes" class="notes-textarea" name="note" placeholder="Enter your note..."></textarea>
                  <p class="note-hint">It will not be shown in PDF</p>
                </div>
                <div class="upload-section grn-attach-files-col">
                  <label class="upload-title">Attach File(s) to GRN</label>

                  <input type="file" id="fileInput" name="uploads[]" multiple style="display: none;" />
                  <input type="hidden" name="existing_files" id="existingFilesInput">
                  <div class="upload-box">
                    <button type="button" class="upload-btn" id="uploadTrigger">📤 Upload File</button>
                    <button type="button" class="upload-dropdown">▼</button>
                  </div>

                  <p class="upload-hint">You can upload a maximum of 5 files, 10MB each</p>

                  <ul class="file-list" id="fileList"></ul>
                </div>

                <div class="upload-section grn-video-upload grn-video-col">
                  <label class="upload-title">Video (GRN)</label>
                  <input type="file" id="grnVideoInput" name="video_uploads[]" accept="video/*" style="display: none;" />
                  <div class="upload-box">
                    <button type="button" class="upload-btn" id="grnVideoUploadTrigger">🎬 Upload video</button>
                  </div>
                  <p class="upload-hint">MP4, WebM, MOV, etc. &mdash; max 1 file, 10MB </p>
                  <ul class="file-list" id="grnVideoList"></ul>
                  <div id="grnVideoPlayerWrap" class="mt-2" style="display: none; width: 100%; max-width: 100%;">
                    <video id="grnVideoInline" class="w-100 rounded border" style="max-height: 360px; background: #0f172a;" controls playsinline>
                      <source id="grnVideoInlineSource" type="video/mp4" />
                    </video>
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
                              <div class="btn-group-vertical w-100" id="image_pdfs" style="margin-left: 11px;">
                              </div>
                              </div>
                                  <div class="col-sm-9" id="grn-modal-primary-preview" style="min-height: 320px;">
                                    <div id="grn-doc-preview-inner" class="w-100" style="min-height: 320px;"></div>
                                </div>
                      </div>
                  </div>
              </div>
          </div>
          </form>
            <div class="action-buttons">
              {{-- <button type="button" id="saveDraftBtn" class="btn draft-btn">Save as Draft</button> --}}
              <button type="button" id="saveOpenBtn" class="btn open-btn">Save as Open</button>
              <button type="button" class="btn cancel-btn">Cancel</button>
            </div>

      </div>
      </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  window.GRN_UPLOADS_BASE = @json(rtrim(asset('uploads/vendor/grn'), '/'));
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

 <script>
  function grnIsVideoFilename (name) {
    var n = String(name == null ? '' : name).split('?')[0].split('/').pop();
    return /\.(mp4|webm|ogg|mov|mkv|m4v|avi|wmv|flv)$/i.test(n);
  }
  function grnSetDocPreviewFromUrl (u, nameHint) {
    var $inner = $('#grn-doc-preview-inner');
    if (!$inner.length) {
      return;
    }
    $inner.empty();
    u = String(u == null ? '' : u);
    if (!u) {
      $inner.append($('<p class="text-muted p-2">No preview</p>'));
      return;
    }
    var hint = String(nameHint == null ? u : nameHint);
    if (grnIsVideoFilename(hint)) {
      $inner.append(
        $('<video class="w-100 rounded" style="max-height:600px;background:#0b1220" controls playsinline></video>').append(
          $('<source>').attr('src', u)
        )
      );
      return;
    }
    if (hint.toLowerCase().endsWith('.pdf') || u.toLowerCase().indexOf('.pdf?') > -1) {
      $inner.append(
        $('<embed class="w-100" type="application/pdf" width="100%" height="600px" />').attr('src', u)
      );
      return;
    }
    if (/\.(jpe?g|png|gif|webp|bmp|svg)$/i.test(hint) || (u.indexOf('data:') === 0 && u.indexOf('image/') > 0)) {
      $inner.append($('<img class="img-fluid rounded" style="max-height:600px" alt="" />').attr('src', u));
      return;
    }
    $inner.append($('<p class="text-muted p-3">Preview is not available for this type.</p>')).append(
      $('<a class="d-inline-block" target="_blank" rel="noopener">Open in new tab</a>').attr('href', u)
    );
  }
    toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
            $(document).on('input', '.receivable_quantity, .acceptable_quantity', function () {
              let row = $(this).closest('tr');

              let quantity = parseFloat(row.find('.quantity').val()) || 0;
              let receivable = parseFloat(row.find('.receivable_quantity').val()) || 0;
              let acceptable = parseFloat(row.find('.acceptable_quantity').val()) || 0;

              // Restrict receivable to not exceed quantity
              if (receivable > quantity) {
                  receivable = quantity;
                  row.find('.receivable_quantity').val(receivable);
              }

              // Restrict acceptable to not exceed receivable
              if (acceptable > receivable) {
                  acceptable = receivable;
                  row.find('.acceptable_quantity').val(acceptable);
              }

              // Auto-calculate rejected quantity
              let rejected = receivable - acceptable;
              row.find('.reject_quantity').val(rejected);

              // Optionally calculate balance quantity
              let balance = quantity - receivable;
              row.find('.balance_quantity').val(balance);
          });




             $(document).ready(function () {
                $('.tcs-tax-section').hide();
                const vendors = @json($vendor);
                const Tbltdstaxs = @json($Tbltdstax);
                const Tbltcstaxs = @json($Tbltcstax);
                const gsttax = @json($gsttax);
                const TblZonesModel = @json($TblZonesModel);
                const Tblcompany = @json($Tblcompany);
                const Tbldepartment = @json($Tbldepartment);
                // alert(JSON.stringify(Tbltdstaxs));
                Tbltdstaxs.data.forEach(Tbltdstax => {
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
                    gsttax.forEach(gsttax => {
                        const item = $(`
                            <div data-type="${gsttax.tax_type}" data-value="${gsttax.tax_rate}" data-id="${gsttax.id}">${gsttax.tax_name}  [${gsttax.tax_rate}%]</div>
                        `);
                        $('.tax-gst-list').append(item);
                    });
                     (Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data || [])).forEach(locations => {
                        const item = $(`
                          <div data-id="${locations.id}">${locations.name} </div>
                        `);
                        $('.zone-list').append(item);
                    });
                    Tblcompany.data.forEach(Tblcompany => {
                        const item = $(`
                            <div data-value="${Tblcompany.company_name}" data-id="${Tblcompany.id}">${Tblcompany.company_name}</div>
                        `);
                        $('.company-list').append(item);
                    });
                    (Array.isArray(Tbldepartment) ? Tbldepartment : (Tbldepartment.data || [])).forEach(dept => {
                        const item = $(`
                            <div data-value="${dept.name}" data-id="${dept.id}">${dept.name}</div>
                        `);
                        $('.department-list').append(item);
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
                  // alert(12);
                  $('.vendor-item').removeClass('selected');
                  const vendorId = $(this).data('id');
                  const vendor = vendors.find(v => v.id == vendorId);

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
                  let rowCount = 1;

                  $('.add-row').on('click', function (e) {
                      e.preventDefault();
                      const newRow = $('.item-row:first').clone();

                      // Update input/select names with new index
                      newRow.find('input, select').each(function () {
                          const name = $(this).attr('name');
                          if (name) {
                              $(this).attr('name', name.replace(/\[\d+\]/, '[' + rowCount + ']'));
                          }
                      });

                      // Clear values
                      newRow.find('input').val('');
                      newRow.find('.quantity').val('1.00');
                      newRow.find('.receivable_quantity').val('0');
                      newRow.find('.acceptable_quantity').val('0');
                      newRow.find('.reject_quantity').val('0');
                      newRow.find('.balance_quantity').val('0');

                      // Append to tbody
                      $('table.item-table tbody').append(newRow);

                      rowCount++;
                  });

                  // Remove row
                  $(document).on('click', '.remove-row', function() {
                    if ($('.item-row').length > 1) {
                      $(this).closest('tr').remove();
                      calculateTotals();
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
                  //department
                  $(document).on('click', '.department-search-input', function (e) {
                      e.stopPropagation();
                      $(this).val('');
                      $('.dropdown-menu.tax-dropdown').hide();

                      const $input = $(this);
                      let $dropdown = $input.data('dropdown');

                      if (!$dropdown) {
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
                  $(document).on('click', '.department-list div', function () {
                      const selectedText = $(this).text().trim();
                      const selectedid = $(this).data('id');

                      const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                      const wrapper = $dropdown.data('wrapper');
                      const row = $dropdown.data('row');

                      if (!wrapper || !row) {
                          console.warn("Wrapper or row not found — department selection failed.");
                          $dropdown.hide();
                          return;
                      }
                      wrapper.find('.department-search-input').val(selectedText);
                      wrapper.find('.department_id').val(selectedid);
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
                      // toastr.success(response.message);
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

                  $('.department-search-input').on('keyup', function () {
                    const searchText = $(this).val().toLowerCase();
                    const $dropdown = $(this).data('dropdown');

                    if ($dropdown) {
                      const list = $dropdown.find('.department-list div');
                      list.each(function () {
                        const itemText = $(this).text().toLowerCase();
                        $(this).toggle(itemText.includes(searchText));
                      });
                    }
                  });

                  $(document).on('keyup', '.department-inner-search', function () {
                    const searchText = $(this).val().toLowerCase();
                    const list = $(this).closest('.dropdown-menu.tax-dropdown').find('.department-list div');
                    list.each(function () {
                      const itemText = $(this).text().toLowerCase();
                      $(this).toggle(itemText.includes(searchText));
                    });
                  });

                  // QC checked by
                  $(document).on('click', '.user-section .user-search-input', function (e) {
                      e.stopPropagation();
                      $(this).val('');
                      $('.dropdown-menu.tax-dropdown').hide();

                      const $input = $(this);
                      let $dropdown = $input.data('dropdown');

                      if (!$dropdown) {
                          $dropdown = $input.siblings('.grn-qc-dropdown').clone(true);
                          $('body').append($dropdown);
                          $input.data('dropdown', $dropdown);
                      }

                      $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper.user-section'));
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
                  $(document).on('click', '.grn-qc-dropdown .user-list div', function () {
                      const selectedText = $(this).text().trim();
                      const selectedid = $(this).data('id');
                      const idStr = (selectedid === undefined || selectedid === null) ? '' : String(selectedid);

                      const $dropdown = $(this).closest('.grn-qc-dropdown');
                      const wrapper = $dropdown.data('wrapper');
                      const row = $dropdown.data('row');

                      if (!wrapper || !row) {
                          console.warn("Wrapper or row not found — GST selection failed.");
                          $dropdown.hide();
                          return;
                      }
                      wrapper.find('.user-search-input').val(idStr === '' ? '' : selectedText);
                      wrapper.find('.qc_checked_by').val(idStr);
                      wrapper.find('.error_qc').text('');
                      $dropdown.hide();
                  });
                  $('.user-section .user-search-input').on('keyup', function () {
                    const searchText = $(this).val().toLowerCase();
                    const $dropdown = $(this).data('dropdown');

                    if ($dropdown) {
                      const list = $dropdown.find('.user-list div');
                      list.each(function () {
                        const itemText = $(this).text().toLowerCase();
                        const idPart = String($(this).data('id') == null || $(this).data('id') === undefined ? '' : $(this).data('id')).toLowerCase();
                        $(this).toggle(itemText.includes(searchText) || idPart.includes(searchText));
                      });
                    }
                  });
                  $(document).on('keyup', '.grn-qc-dropdown .user-inner-search', function () {
                    const searchText = $(this).val().toLowerCase();
                    const list = $(this).closest('.grn-qc-dropdown').find('.user-list div');
                    list.each(function () {
                      const itemText = $(this).text().toLowerCase();
                      const idPart = String($(this).data('id') == null || $(this).data('id') === undefined ? '' : $(this).data('id')).toLowerCase();
                      $(this).toggle(itemText.includes(searchText) || idPart.includes(searchText));
                    });
                  });

                $(document).on('click', function (e) {
                    if (!$(e.target).closest('.tax-dropdown-wrapper').length) {
                      $('.dropdown-menu.tax-dropdown').hide();
                    }
                  });
                  $(document).on('click', '.dropdown-menu.tax-dropdown', function (e) {
                      e.stopPropagation();
                  });


            });
           $(document).ready(function () {
              // Open file input on upload button click
              // $('#uploadTrigger').on('click', function () {
              //   $('#fileInput').click();
              // });

              // // On file input change
              // $('#fileInput').on('change', function () {
              //   const fileList = $('#fileList');
              //   fileList.empty(); // Clear previous entries

              //   const files = Array.from(this.files);

              //   // Store selected files temporarily
              //   window.selectedFiles = files;

              //   $.each(files, function (index, file) {
              //     const li = $(`
              //       <li>
              //         ${file.name}
              //         <span class="remove-file" data-index="${index}">❌</span>
              //       </li>
              //     `);
              //     fileList.append(li);
              //   });
              // });

              // // Remove file from list visually
              // $('#fileList').on('click', '.remove-file', function () {
              //   const index = $(this).data('index');
              //   window.selectedFiles.splice(index, 1);

              //   // Rebuild the list after removal
              //   $('#fileInput').val('');
              //   const newFileList = window.selectedFiles;
              //   $('#fileList').empty();

              //   $.each(newFileList, function (i, file) {
              //     const li = $(`
              //       <li>
              //         ${file.name}
              //         <span class="remove-file" data-index="${i}">❌</span>
              //       </li>
              //     `);
              //     $('#fileList').append(li);
              //   });
              // });
              function grnRevokeVideoObjectUrl() {
                if (window._grnVideoObjectUrl) {
                  try { URL.revokeObjectURL(window._grnVideoObjectUrl); } catch (x) { /* */ }
                  window._grnVideoObjectUrl = null;
                }
              }
              window.grnRevokeVideoObjectUrl = grnRevokeVideoObjectUrl;
              window.selectedVideoFiles = window.selectedVideoFiles || [];

              $('#uploadTrigger').on('click', function () {
                  $('#fileInput').click();
                });
              $('#grnVideoUploadTrigger').on('click', function () {
                $('#grnVideoInput').click();
              });
              $('#grnVideoInput').on('change', function () {
                grnRevokeVideoObjectUrl();
                const files = Array.from(this.files || []);
                const maxBytes = 10 * 1024 * 1024;
                if (files.length && files[0].size > maxBytes) {
                  toastr.error('Video must be 10MB or smaller.');
                  $(this).val('');
                  window.selectedVideoFiles = [];
                  $('#grnVideoList').empty();
                  $('#grnVideoPlayerWrap').hide();
                  return;
                }
                window.selectedVideoFiles = files;
                const $vlist = $('#grnVideoList');
                $vlist.empty();
                if (!files.length) {
                  $('#grnVideoPlayerWrap').hide();
                  return;
                }
                const f = files[0];
                const u = URL.createObjectURL(f);
                window._grnVideoObjectUrl = u;
                $vlist.append(
                  '<li><span class="grn-video-preview-name" style="cursor:pointer;color:blue;">' + $('<div>').text(f.name).html() +
                  '</span> <span class="remove-grn-video" style="cursor:pointer;color:red">❌</span></li>'
                );
                $('#grnVideoInline source').attr('src', u).attr('type', f.type || 'video/mp4');
                $('#grnVideoInline')[0].load();
                $('#grnVideoPlayerWrap').show();
              });
              $(document).on('click', '.remove-grn-video', function (e) {
                e.stopPropagation();
                grnRevokeVideoObjectUrl();
                window.selectedVideoFiles = [];
                $('#grnVideoInput').val('');
                $('#grnVideoList').empty();
                $('#grnVideoPlayerWrap').hide();
              });
              $(document).on('click', '.grn-video-preview-name, .grn-video-new-name', function () {
                if (!window.selectedVideoFiles || !window.selectedVideoFiles[0]) {
                  return;
                }
                const file = window.selectedVideoFiles[0];
                const fileURL = URL.createObjectURL(file);
                grnSetDocPreviewFromUrl(fileURL, file.name);
                new bootstrap.Modal(document.getElementById('documentModal1')).show();
                setTimeout(function () { try { URL.revokeObjectURL(fileURL); } catch (e) {} }, 6e4);
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
                  if (!file) { return; }
                  if (file.type && String(file.type).indexOf('video/') === 0) {
                    const u = URL.createObjectURL(file);
                    grnSetDocPreviewFromUrl(u, file.name);
                    new bootstrap.Modal(document.getElementById('documentModal1')).show();
                    setTimeout(function () { try { URL.revokeObjectURL(u); } catch (e) {} }, 12e4);
                    return;
                  }
                  const reader = new FileReader();
                  reader.onload = function (e) {
                    const fileURL = e.target.result;
                    if (file.type && file.type.startsWith('text/')) {
                      $('#grn-doc-preview-inner').html(
                        '<pre style="max-height:600px;overflow:auto;text-align:left;white-space:pre-wrap;">' + $('<div>').text(e.target.result).html() + '</pre>'
                      );
                    } else {
                      grnSetDocPreviewFromUrl(fileURL, file.name);
                    }
                    new bootstrap.Modal(document.getElementById('documentModal1')).show();
                  };
                  if (file.type && file.type.startsWith('text/')) {
                    reader.readAsText(file);
                  } else {
                    reader.readAsDataURL(file);
                  }
                });
            });



              $(document).ready(function () {
              function syncQcCheckedByRequiredUi() {
                var need = $('#qc_ststus').val() === 'Checked';
                $('#qc_checked_by_required_mark').toggle(need);
              }
              $('#qc_ststus').on('change', function () {
                if ($(this).val() !== 'Checked') {
                  $('#qc_checked_by').val('');
                  $('#qc_checked_by_display').val('');
                  $('.user-section .error_qc').text('');
                }
                syncQcCheckedByRequiredUi();
              });
              syncQcCheckedByRequiredUi();
              window.GRN = window.GRN || {};
              window.GRN.syncQcCheckedByRequiredUi = syncQcCheckedByRequiredUi;

              function submitBillForm(saveStatus, $btn) {
                    let originalText = $btn.html();
                    var depId = $('.department_id').val();
                    if (!depId || String(depId).trim() === '') {
                        toastr.error('Please select a department.');
                        return;
                    }
                    if ($('#qc_ststus').val() === 'Checked') {
                        var qcb = $('#qc_checked_by').val();
                        if (qcb == null || String(qcb).trim() === '') {
                            toastr.error('Please select QC Checked By when QC Status is Checked.');
                            $('.error_qc').text('Please select a user.');
                            return;
                        }
                    }
                    $('.error_qc').text('');
                $btn.prop('disabled', true);
                    // Show loader on button
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                  // Append status to the form before serialize
                  $('#billForm').append('<input type="hidden" name="save_status" value="' + saveStatus + '" />');

                  let formData = new FormData($('#billForm')[0]);

                  $.ajax({
                      url: '{{ route("superadmin.savegrn") }}', // 🔁 Replace with your real backend route
                      method: "POST",
                      data: formData,
                      processData: false,
                      contentType: false,
                      headers: {
                          'X-CSRF-TOKEN': $('input[name="_token"]').val()
                      },
                      success: function (response) {
                          toastr.success(response.message);
                          setTimeout(() => {
                            window.location.href = "{{ route('superadmin.getgrndashboard') }}";
                        }, 1500);
                        setTimeout(() => {
                                $btn.prop('disabled', false);
                            }, 3000);
                      },
                      error: function (xhr) {
                          console.error("Error saving form:", xhr);
                          if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                              $.each(xhr.responseJSON.errors, function (k, v) {
                                  toastr.error(v[0]);
                              });
                          } else {
                              toastr.error('Something went wrong.');
                          }
                          $btn.prop('disabled', false).html(originalText);
                      },
                      // complete: function () {
                      //       // Reset button back
                      //       $btn.prop('disabled', false).html(originalText);
                      //   }
                  });
              }

              $('#saveDraftBtn').on('click', function () {
                    submitBillForm('draft', $(this));
                });

                $('#saveOpenBtn').on('click', function () {
                    submitBillForm('save', $(this));
                });
              // Tab switching functionality
            $('.cancel-btn').on('click', function() {
                window.location.href = "{{ route('superadmin.getgrnconvert') }}";
            });

          });
    </script>
<script>
$(function () {
  $(document).on('click', '.documentclk', function (e) {
    e.preventDefault();
    if ($(e.target).is('.remove-file1')) {
      return;
    }
    var filesData = $(this).attr('data-files');
    var fileArray = [];
    try {
      fileArray = JSON.parse(filesData);
      if (typeof fileArray === 'string') {
        fileArray = JSON.parse(fileArray);
      }
    } catch (err) {
      $('#image_pdfs').html('<p>Invalid file data</p>');
      return;
    }
    if (!Array.isArray(fileArray) || !fileArray.length) {
      $('#image_pdfs').html('<p>No files found</p>');
      return;
    }
    var base = (window.GRN_UPLOADS_BASE || '').replace(/\/?$/, '/') || '';
    function makeUrl (name) {
      return base + encodeURIComponent(String(name).replace(/^\/+/, ''));
    }
    var firstU = makeUrl(fileArray[0]);
    var firstN = fileArray[0];
    grnSetDocPreviewFromUrl(firstU, firstN);
    var $list = $('#image_pdfs').empty();
    fileArray.forEach(function (name) {
      var u = makeUrl(name);
      var fn = String(name).split('/').pop().split('?')[0];
      try {
        fn = decodeURIComponent(fn);
      } catch (x) { /* keep */ }
      var $b = $('<button type="button" class="btn btn-primary pdf-btn" style="font-size: 11px;"></button>');
      $b.attr('data-filepath', u);
      $b.attr('data-fname', fn);
      $b.text(fn);
      $list.append($b);
    });
    const modal = bootstrap.Modal.getOrCreateInstance ? bootstrap.Modal.getOrCreateInstance(document.getElementById('documentModal1')) : new bootstrap.Modal(document.getElementById('documentModal1'));
    modal.show();
  });
  $(document).on('click', '.pdf-btn', function () {
    $('.pdf-btn').removeClass('active');
    $(this).addClass('active');
    var fp = $(this).attr('data-filepath');
    var fn = $(this).attr('data-fname') || '';
    if (fp) {
      grnSetDocPreviewFromUrl(fp, fn);
    }
  });
});
</script>
@if(!empty($grndata))
<script>
$(document).ready(function () {
            const grnheader_header = @json($grndata);
            console.log("grnheader_header",grnheader_header);
            const type = @json($type);
            console.log("type",type);
            const grn_lines = grnheader_header.bill_lines;

            const vendor_id = grnheader_header.vendor_id;
            setTimeout(function () {
                const $vendorItem = $('.vendor-item[data-id="' + vendor_id + '"]');

                if ($vendorItem.length) {
                    $vendorItem.trigger('click');
                } else {
                    console.warn('Vendor item not found for ID:', vendor_id);
                }
            }, 100);

            if(type=="bill"){
              // $('#bill_number').val(grnheader_header.bill_number);
              $('#bill_id').val(grnheader_header.id);
              $('.delivery_add').hide();
            }else{
              // $('#purchase_no').val(grnheader_header.purchase_order_number);
              $('#purchase_id').val(grnheader_header.id);
            }
            $('#order_number').val(grnheader_header.order_number);
            $('#bill_date').val(grnheader_header.bill_date);
            $('#due_date').val(grnheader_header.due_date);
            $('#payment_terms').val(grnheader_header.payment_terms);

            let isFirst = true;

            grn_lines.forEach((lines, index) => {
                let currentRow;

                if (isFirst) {
                    currentRow = $('.item-row:first');
                    isFirst = false;
                } else {
                    $('.add-row').trigger('click');
                    currentRow = $('.item-row').last();
                }

                // Populate after slight delay to ensure row is in DOM
                setTimeout(() => {
                    currentRow.find('.item-details').val(lines.item_details).prop('readonly',true);
                    currentRow.find('.quantity').val(lines.quantity).prop('readonly',true);
                }, 50); // 50ms is enough
            });
        });

</script>
@endif
    @if(!empty($grnedit))
    <script>
      $(document).ready(function () {
                      const grnedit_header = @json($grnedit);
                      const h = grnedit_header[0];

                      const grnedit_lines = @json(
                          ($grnedit[0] ?? null) && $grnedit[0]->BillLines
                          ? $grnedit[0]->BillLines->all()
                          : []
                      );
                      const vendor_id = h.vendor_id;
                      setTimeout(function () {
                          const $vendorItem = $('.vendor-item[data-id="' + vendor_id + '"]');

                          if ($vendorItem.length) {
                              $vendorItem.trigger('click');
                          } else {
                              console.warn('Vendor item not found for ID:', vendor_id);
                          }
                      }, 100);
                      $('#id').val(h.id);
                      $('#grn_number').val(h.grn_number);
                      if ($('#delivery_address').length) {
                        $('#delivery_address').val(h.delivery_address);
                      }
                      $('#order_number').val(h.order_number);
                      $('#bill_date').val(h.bill_date);
                      $('#due_date').val(h.due_date);
                      $('#payment_terms').val(h.payment_terms);
                      (function setQcCheckedBy() {
                        var qc = h.qc_checked_by;
                        var id = '';
                        if (qc != null && qc !== '' && typeof qc === 'object' && qc.id != null) {
                          id = String(qc.id);
                        } else if (qc != null && qc !== '' && (typeof qc === 'number' || typeof qc === 'string')) {
                          id = String(qc);
                        }
                        $('#qc_checked_by').val(id);
                        var $row = id
                          ? $('#qc_checked_by').closest('.tax-dropdown-wrapper').find('.user-list [data-id="' + id + '"]')
                          : $();
                        if ($row.length) {
                          $('.user-search-input#qc_checked_by_display').val($row.text().trim());
                          return;
                        }
                        var rel = h.q_c_checked_by || h.qcCheckedBy || h.QcCheckedBy;
                        if (rel && typeof rel === 'object' && (rel.user_fullname != null || rel.id != null)) {
                          var n = String((rel.user_fullname || '').trim() || 'User');
                          var e = rel.employee_id;
                          var eid = (e != null && String(e) !== '') ? String(e) : String(rel.id != null ? rel.id : id);
                          $('#qc_checked_by_display').val(n + ' - ' + eid);
                          return;
                        }
                        if (id) {
                          $('#qc_checked_by_display').val('User #' + id);
                        } else {
                          $('#qc_checked_by_display').val('');
                        }
                      })();
                      $('#qc_ststus').val(h.qc_ststus);
                      if (window.GRN && typeof window.GRN.syncQcCheckedByRequiredUi === 'function') {
                        window.GRN.syncQcCheckedByRequiredUi();
                      }
                      $('#subject').val(h.subject);
                      $('#bill_id').val(h.bill_id);
                      $('#purchase_id').val(h.purchase_id);
                      $('.company-search-input').val(h.company_name);
                      $('.company_id').val(h.company_id);
                      var _dept = h.department || h.Department;
                      if (_dept && _dept.name) {
                        $('.department-search-input').val(_dept.name);
                      } else {
                        $('.department-search-input').val('');
                      }
                      $('.department_id').val(h.department_id != null ? h.department_id : '');

                      let isFirst = true;
                      (Array.isArray(grnedit_lines) ? grnedit_lines : []).forEach((lines, index) => {
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
                              currentRow.find('.item-details').val(lines.item_details).prop('readonly',true);
                              currentRow.find('.quantity').val(lines.quantity).prop('readonly',true);
                              currentRow.find('.receivable_quantity').val(lines.receivable_quantity);
                              currentRow.find('.acceptable_quantity').val(lines.acceptable_quantity);
                              currentRow.find('.reject_quantity').val(lines.reject_quantity);
                              currentRow.find('.balance_quantity').val(lines.balance_quantity);

                          }, 100); // 100ms is enough for most DOM rendering
                      });


                    $('#notes').val(h.note);

                      // From server
                      (function initExistingDocFiles() {
                        window.existingFiles = [];
                        var raw = h.documents;
                        if (!raw) { return; }
                        try {
                          var parsed = typeof raw === 'string' ? JSON.parse(raw) : raw;
                          if (Array.isArray(parsed)) { window.existingFiles = parsed; }
                        } catch (e) { window.existingFiles = []; }
                      })();
                      // console.log("window.existingFiles",window.existingFiles);

                      window.selectedFiles = [];
                      window.selectedVideoFiles = [];
                      $('#existingFilesInput').val(JSON.stringify(window.existingFiles));
                      function grnUpdateEditVideoPreview() {
                        if (window.grnRevokeVideoObjectUrl) {
                          window.grnRevokeVideoObjectUrl();
                        }
                        if (window.selectedVideoFiles && window.selectedVideoFiles[0]) {
                          var f = window.selectedVideoFiles[0];
                          var u = URL.createObjectURL(f);
                          window._grnVideoObjectUrl = u;
                          $('#grnVideoInline source').attr('src', u).attr('type', f.type || 'video/mp4');
                          if ($('#grnVideoInline')[0]) {
                            $('#grnVideoInline')[0].load();
                          }
                          $('#grnVideoPlayerWrap').show();
                        } else {
                          $('#grnVideoPlayerWrap').hide();
                        }
                      }
                      function renderExistingFiles() {
                        $('#fileList').empty();
                        $('#grnVideoList').empty();
                        $.each(window.existingFiles, function (index, name) {
                          var files = Array.isArray(name) ? name : [name];
                          var n = name;
                          var isVid = (typeof grnIsVideoFilename === 'function' && grnIsVideoFilename(n));
                          var li = '<li class="documentclk" data-filetype="document" data-files="' +
                            JSON.stringify(files).replace(/"/g, '&quot;') + '"><span class="grn-file-link-text">' + $('<div>').text(n).html() + '</span> ' +
                            '<span class="remove-file1" data-type="existing" data-index="' + index +
                            '" style="cursor:pointer; color:red;">❌</span></li>';
                          if (isVid) {
                            $('#grnVideoList').append(li);
                          } else {
                            $('#fileList').append(li);
                          }
                        });
                        $.each(window.selectedFiles, function (index, file) {
                          $('#fileList').append(
                            '<li><span class="file-name" data-index="' + index +
                            '" style="cursor:pointer; color:blue; text-decoration:underline;">' +
                            $('<div>').text(file.name).html() +
                            '</span><span class="remove-file1" data-type="new" data-index="' + index +
                            '" style="cursor:pointer; color:red;">❌</span></li>'
                          );
                        });
                        if (window.selectedVideoFiles && window.selectedVideoFiles[0]) {
                          var vf = window.selectedVideoFiles[0];
                          $('#grnVideoList').append(
                            '<li><span class="grn-video-new-name" style="cursor:pointer;color:blue;text-decoration:underline;">' + $('<div>').text(vf.name).html() +
                            '</span> <span class="remove-file1" data-type="newvideo" data-index="0" style="cursor:pointer;color:red">❌</span></li>'
                          );
                        }
                        grnUpdateEditVideoPreview();
                        $('#existingFilesInput').val(JSON.stringify(window.existingFiles));
                      }

                      renderExistingFiles();

                      $('#fileInput').off('change').on('change', function (e) {
                        window.selectedFiles = Array.from(e.target.files || []);
                        renderExistingFiles();
                      });
                      $('#grnVideoInput').off('change').on('change', function (e) {
                        var list = Array.from(e.target.files || []);
                        var maxBytes = 10 * 1024 * 1024;
                        if (list.length && list[0].size > maxBytes) {
                          toastr.error('Video must be 10MB or smaller.');
                          $(e.target).val('');
                          window.selectedVideoFiles = [];
                          renderExistingFiles();
                          return;
                        }
                        window.selectedVideoFiles = list;
                        renderExistingFiles();
                      });

                      $('#fileList, #grnVideoList').off('click', '.remove-file1').on('click', '.remove-file1', function () {
                        const index = $(this).data('index');
                        const type = $(this).data('type');
                        if (type === 'existing') {
                          window.existingFiles.splice(index, 1);
                        } else if (type === 'new') {
                          window.selectedFiles.splice(index, 1);
                          $('#fileInput').val('');
                        } else if (type === 'newvideo') {
                          window.selectedVideoFiles = [];
                          if (window.grnRevokeVideoObjectUrl) {
                            window.grnRevokeVideoObjectUrl();
                          }
                          $('#grnVideoInput').val('');
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
