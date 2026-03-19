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
          <h1>Bill Made</h1>

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
                                    <input type="text" id="vendor-search" class="form-control search-input" name="vendor_name" autocomplete="off" autocorrect="off" placeholder="Search vendor..." autocomplete="off">
                                    <div class="dropdown-menu" id="vendor-dropdown">
                                        <div class="search-box">
                                            <input type="text" placeholder="Search" class="inner-search form-control mb-2">
                                        </div>
                                        <div class="vendor-list"></div>

                                    </div>
                                </div>
                                <div id="billing-address" class="billing-address-section mt-3 text-muted small">
                                    <!-- Filled via JS -->
                                </div>
                                <input type="hidden" id="selected-vendor-id" name="vendor_id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container initially_show bg-white p-4 rounded" style="display:none">
                  <!-- Row 2: Bill#, Order Number -->
                  <div class="row mb-3">
                      <label for="payment_gen_order" class="col-md-2 ">Payment Generate#*</label>
                      <div class="col-md-4">
                          <input type="text" class="form-control" id="payment_gen_order" autocomplete="off" autocorrect="off" name="payment_gen_order" value="{{$serial}}" readonly required>
                      </div>
                  </div>
                  <div class="row mb-3">
                    <div class=" row align-items-center col-md-6">
                      <label class="col-md-4 form-label">Payment #</label>
                      <div class="col-md-8">
                        <input type="text" class="form-control" id="payment" autocomplete="off" autocorrect="off" name="payment" readonly value='NEFT'>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="row align-items-center col-md-6">
                      <label class="col-md-4 form-label">Payment Made*</label>
                      <div class="col-md-8">
                        <div class="currency-input">
                          {{-- <span class="currency-label">INR</span> --}}
                          <input type="text" class="form-control" placeholder="Enter amount" id="payment_made" autocomplete="off" autocorrect="off" name="payment_made">
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="row align-items-center col-md-6">
                      <label class="col-md-4 form-label">Payment Date*</label>
                      <div class="col-md-8">
                        <input type="date" class="form-control datepicker" id="payment_date" name="payment_date" autocomplete="off" autocorrect="off">
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="row align-items-center col-md-6">
                      <label class="col-md-4 form-label">Payment Mode</label>
                      <div class="col-md-8">
                        <select class="form-select" name="payment_mode" id="payment_mode" autocomplete="off" autocorrect="off">
                          <option value="Cash">Cash</option>
                          <option value="Bank Transfer">Bank Transfer</option>
                          <option value="Cheque">Cheque</option>
                          <option value="Credit Card">Credit Card</option>
                          <option value="UPI">UPI</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="row align-items-center col-md-6">
                      <label class="col-md-4 form-label">Paid Through*</label>
                      <div class="col-md-8">
                        <select class="form-select" name="paid_through" id="payment_through" autocomplete="off" autocorrect="off">
                          <option value="Accounts Payable">Accounts Payable</option>
                          <option value="Petty Cash">Petty Cash</option>
                          <option value="Undeposited Funds">Undeposited Funds</option>
                          <option value="TCS Payable">TCS Payable</option>
                          <option value="TDS Payable">TDS Payable</option>
                          <option value="Bank Reconciliation">Bank Reconciliation</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="row align-items-center col-md-6">
                      <label class="col-md-4 form-label">Reference#</label>
                      <div class="col-md-8">
                        <input type="text" class="form-control" id="reference" name="reference" autocomplete="off" autocorrect="off">
                      </div>
                    </div>
                  </div>
                  <!-- Row 5: Payment Terms -->
                <div class="row mb-3">
                    <label for="zone" class="col-md-2 ">Zones</label>
                    <div class="col-md-4">
                        {{-- <input type="text" class="form-control datepicker" id="due_date" name="due_date" > --}}
                      <div class="tax-dropdown-wrapper account-section" style="width:300px">
                          <input type="text" class="form-control zone-search-input" name="zone" placeholder="Select a Zones" autocomplete="off" autocorrect="off" readonly>
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
                      <div class="tax-dropdown-wrapper account-section" style="width:300px">
                          <input type="text" class="form-control branch-search-input" name="branch" placeholder="Select a branch" autocomplete="off" autocorrect="off" readonly>
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
                          <input type="text" class="form-control company-search-input" name="company_name" placeholder="Select a Company" autocomplete="off" autocorrect="off" readonly>
                          <input type="hidden" name="company_id" class="company_id">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="company-list">
                            </div>
                          </div>
                          <span class="error_zone" style="color:red"></span>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="row align-items-center col-md-6">
                      <label class="col-md-4 form-label">Remark</label>
                      <div class="col-md-8">
                        <textarea class="form-control" id="remark" name="remark" autocomplete="off" autocorrect="off" placeholder="" rows="2" maxlength="250"></textarea>
                      </div>
                    </div>
                  </div>


                 <!-- Responsive Table -->
                  <div class="table-responsive mb-3">
                    <div class="filter_search">
                      <div class="clear-amount">Clear Applied Amount</div>
                      <div class="" style="width:200px">
                        <input type="text" name="filter_search" id="filter_search" placeholder="Search....">
                      </div>
                    </div>
                    <table class="table payment-table align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>Date</th>
                          <th>Bill Gen No</th>
                          <th>Bill#</th>
                          <th class="text-end">Bill Amount</th>
                          <th class="text-end">Amount Due</th>
                          <th>Payment Made on</th>
                          <th>Payment</th>
                        </tr>
                      </thead>
                      <tbody>

                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="6" class="text-end table-footer">Total :</td>
                          <td class="table-footer table-footer-amount">0.00</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>

                 <div class="container-right">
                    <div class="payment-summary-box">
                      <p><span>Amount Paid:</span> <span id="amount-paid">0.00</span></p>
                      <p><span>Amount used for Payments:</span> <span id="amount-used">0.00</span></p>
                      <p><span>Amount Refunded:</span> <span id="amount-refunded">0.00</span></p>
                      <p class="warning"><span>⚠️ Amount in Excess:</span> <span id="amount-excess">₹ 0.00</span></p>
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
                      <label class="upload-title">Attach File(s) to Bill</label>

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

                  <div class="d-flex gap-2">
                    <button type="button" id="saveDraftBtn" class="btn draft-btn">Save as Draft</button>
                    <button type="button" id="saveOpenBtn" class="btn open-btn">Save as Paid</button>
                    <button type="button" class="btn cancel-btn">Cancel</button>
                  </div>
                </div>

          </form>


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
                const TblZonesModel = @json($TblZonesModel);
                const Tblcompany = @json($Tblcompany);
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
                function numberformate(number) {
                    // Simple number formatting with commas
                    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }

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
                  $.ajax({
                    url: 'get-vendor-details', // your route URL
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        vendor_id: vendorId
                    },
                    success: function(response) {
                      $('.initially_show').show();
                      console.log(response);
                      let html = '';
                      let vendorData = []; // Array to store vendor data for backend
                      const today = new Date().toISOString().split('T')[0];

                      response.vendor.forEach((element, index) => {
                          // Build HTML row for each vendor
                          html += `<tr class="bill-row">
                                  <td>
                                      ${element.bill_date}
                                      <div class="due-date">Due Date: ${element.due_date}</div>
                                      <input type="hidden" name="vendors[${index}][id]" value="${element.id}">
                                      <input type="hidden" name="vendors[${index}][bill_date]" value="${element.bill_date}">
                                      <input type="hidden" name="vendors[${index}][due_date]" value="${element.due_date}">
                                  </td>
                                  <td>${element.bill_gen_number}</td>
                                  <td>
                                      ${element.bill_number}
                                      <input type="hidden" name="vendors[${index}][bill_number]" value="${element.bill_number}">
                                  </td>
                                  <td class="text-end">
                                      ${numberformate(element.grand_total_amount)}
                                      <input type="hidden" name="vendors[${index}][grand_total_amount]" value="${element.grand_total_amount}">
                                  </td>
                                  <td class="text-end">
                                      ${numberformate(element.balance_amount)}
                                      <input type="hidden" name="vendors[${index}][balance_amount]" value="${element.balance_amount}">
                                  </td>
                                  <td>
                                      <input type="text" class="form-control datepicker" name="vendors[${index}][payment_date]" value="${today}" />
                                  </td>
                                  <td>
                                      <input type="number" class="form-control text-end payment-amount" name="vendors[${index}][amount]" value="0" />
                                  </td>
                              </tr>
                              `;

                            });

                            // Append the HTML to your table
                            $('.payment-table tbody').html(html);

                            flatpickr('.datepicker', {
                                    dateFormat: 'd/m/Y',
                                    allowInput: true
                                });
                      // Store the vendor data in a way accessible for form submission
                      // $('#vendor-data').val(JSON.stringify(vendorData));
                      $('#billing-address').empty();
                  },
                    error: function () {
                        $('#billing-address').html('<span class="text-danger">Failed to load vendor details.</span>');
                        $('.initially_show').hide();
                      }
                });

              });
              $('#filter_search').on('keyup', function () {

                let search = $(this).val().trim();
                let vendorId = $('#selected-vendor-id').val(); // keep vendor id if selected

                $.ajax({
                    url: 'get-vendor-details',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        vendor_id: vendorId,
                        filter_search: search
                    },
                    success: function(response){

                        $('.initially_show').show();
                        let html = '';
                        const today = new Date().toISOString().split('T')[0];

                        response.vendor.forEach((element, index) => {
                            html += `<tr class="bill-row">
                                  <td>
                                      ${element.bill_date}
                                      <div class="due-date">Due Date: ${element.due_date}</div>
                                      <input type="hidden" name="vendors[${index}][id]" value="${element.id}">
                                      <input type="hidden" name="vendors[${index}][bill_date]" value="${element.bill_date}">
                                      <input type="hidden" name="vendors[${index}][due_date]" value="${element.due_date}">
                                  </td>
                                  <td>${element.bill_gen_number}</td>
                                  <td>
                                      ${element.bill_number}
                                      <input type="hidden" name="vendors[${index}][bill_number]" value="${element.bill_number}">
                                  </td>
                                  <td class="text-end">
                                      ${numberformate(element.grand_total_amount)}
                                      <input type="hidden" name="vendors[${index}][grand_total_amount]" value="${element.grand_total_amount}">
                                  </td>
                                  <td class="text-end">
                                      ${numberformate(element.balance_amount)}
                                      <input type="hidden" name="vendors[${index}][balance_amount]" value="${element.balance_amount}">
                                  </td>
                                  <td>
                                      <input type="text" class="form-control datepicker" name="vendors[${index}][payment_date]" value="${today}" />
                                  </td>
                                  <td>
                                      <input type="number" class="form-control text-end payment-amount" name="vendors[${index}][amount]" value="0" />
                                  </td>
                              </tr>`;
                        });

                        $('.payment-table tbody').html(html);

                        flatpickr('.datepicker',{ dateFormat:'d/m/Y'});
                    }
                });

            }); // debounce to avoid spam calls

              $(document).on('input', '.payment-amount', function () {
                  const row = $(this).closest('.bill-row');
                  const balance = parseFloat(row.find('input[name*="[balance_amount]"]').val()) || 0;
                  const payment = parseFloat($(this).val()) || 0;

                  if (payment > balance) {
                      $(this).val(balance); // Reset to balance
                  }
              });
              $(document).on('input', '.payment-amount', function () {
                  calculatePayments();
              });
              $(document).on('click', '.clear-amount', function () {
                $('.payment-amount').val('');
                $('.table-footer-amount').text("0.00");
                $('#amount-paid').text("0.00");
                $('#amount-used').text("0.00");
                $('#amount-excess').text("0.00");
                $('#amount-refunded').text("0.00");
              });

                function parseNumber(val) {
                    return parseFloat(val) || 0;
                }

                function calculatePayments() {
                    let totalPaid = 0;
                    let totalUsed = 0;
                    let totalamount = 0;

                    $('.bill-row').each(function (index) {
                        const row = $(this);
                        const payment = parseNumber(row.find('.payment-amount').val());
                        const balance = parseNumber(row.find('input[name*="[balance_amount]"]').val());
                        const total_amount = parseNumber(row.find('input[name*="[grand_total_amount]"]').val());
                        console.log(balance,"balance");

                        totalamount += balance;
                        totalPaid += payment;
                        // Only use up to the balance amount
                        const used = Math.min(payment, balance);
                        totalUsed += used;
                    });

                    const refund = totalPaid - totalUsed;
                    const excess = totalUsed - totalamount ;

                    // Update summary UI
                    $('.table-footer-amount').text(totalPaid.toFixed(2));
                    $('#amount-paid').text(totalamount.toFixed(2));
                    $('#amount-used').text(totalUsed.toFixed(2));
                    $('#amount-refunded').text(refund > 0 ? refund.toFixed(2) : '0.00');
                    $('#amount-excess').text(`₹ ${excess.toFixed(2)}`);
                }



                // Initial render
                renderVendors();
                 flatpickr('.datepicker', {
                    dateFormat: 'd/m/Y',
                    allowInput: true
                });


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

              $(document).on('click', function (e) {
                  if (!$(e.target).closest('.tax-dropdown-wrapper').length) {
                    $('.dropdown-menu.tax-dropdown').hide();
                  }
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
                    // Save original button text
                    let originalText = $btn.html();
                    // Show loader on button
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                  // Append status to the form before serialize
                  $('#billForm').append('<input type="hidden" name="save_status" value="' + saveStatus + '" />');

                  let formData = new FormData($('#billForm')[0]);
                  formData.append('amount_paid', $('#amount-paid').text());
                  formData.append('amount_used', $('#amount-used').text());
                  formData.append('amount_refunded', $('#amount-refunded').text());
                  formData.append('amount_excess', $('#amount-excess').text());

                  console.log("formData",formData);


                  $.ajax({
                      url: '{{ route("superadmin.savebillmade") }}', // 🔁 Replace with your real backend route
                      method: "POST",
                      data: formData,
                      processData: false,
                      contentType: false,
                      headers: {
                          'X-CSRF-TOKEN': $('input[name="_token"]').val()
                      },
                      success: function (response) {
                          toastr.success(response.message);
                        //   setTimeout(() => {
                        //     window.location.href = "{{ route('superadmin.getbillmade') }}";
                        // }, 1500);
                        if (response.restore_filters) {
                                sessionStorage.setItem(
                                    "restore_filters",
                                    true
                                );
                            }
                        setTimeout(() => {
                            const urlParams = new URLSearchParams(window.location.search);
                            const redirectPage = urlParams.get('redirect_page') || 1;

                            // ✅ STORE page in sessionStorage (this is the key)
                            sessionStorage.setItem('bill_made_page', redirectPage);

                            let redirectUrl =
                                "{{ route('superadmin.getbillmade') }}" +
                                "?page=" + redirectPage +
                                "&redirect_page=" + redirectPage;

                            console.log("redirectUrl", redirectUrl);
                            window.location.href = redirectUrl;
                        }, 1500);

                        setTimeout(() => {
                                $btn.prop('disabled', false);
                            }, 3000);
                      },
                      error: function (xhr) {
                          console.error("Error saving form:", xhr);
                      },
                      // complete: function () {
                      //       // Reset button back
                      //       $btn.prop('disabled', false).html(originalText);
                      // }
                  });
              }

              $('#saveDraftBtn').on('click', function () {
                var hasValidAmount = false;

                  $('.payment-amount').each(function() {
                      var amount = parseFloat($(this).val()) || 0;
                      if (amount > 0) {
                          hasValidAmount = true;
                          return false; // stop loop once we find one valid
                      }
                  });

                  if (hasValidAmount) {
                      submitBillForm('draft', $(this));
                  } else {
                      toastr.warning("Enter amount greater than Zero");
                  }
                });

                $('#saveOpenBtn').on('click', function () {
                  var amount=$('.payment-amount').val();
                  if(amount>0){
                    submitBillForm('save', $(this));
                  }else{
                    toastr.warning("Enter amount greater than Zero");
                  }
                });
              $('.cancel-btn').on('click', function() {
                window.location.href = "{{ route('superadmin.getbillmade') }}";
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

     @if(!empty($billpay))
    <script>
      $(document).ready(function () {
        const bill_header = @json($billpay);
        const bill_lines = @json($billpay[0]->BillLines);
        console.log("bill_header",bill_header);
        console.log("bill_lines",bill_lines);
        $('.initially_show').show();
        $('#id').val(bill_header[0].id);
        $('#vendor-search').val(bill_header[0].vendor_name);
        $('#selected-vendor-id').val(bill_header[0].vendor_id);
        $('#payment').val(bill_header[0].payment);
        $('#payment_gen_order').val(bill_header[0].payment_gen_order);
        $('#payment_made').val(bill_header[0].payment_made);
        $('#payment_mode').val(bill_header[0].payment_mode);
        $('#payment_date').val(bill_header[0].payment_date);
        $('#paid_through').val(bill_header[0].paid_through);
        $('#reference').val(bill_header[0].reference);
        $('#remark').val(bill_header[0].remark);
        $('.zone-search-input').val(bill_header[0].zone_name);
        $('.zone_id').val(bill_header[0].zone_id);
        $('.branch-search-input').val(bill_header[0].branch_name);
        $('.branch_id').val(bill_header[0].branch_id);
        $('.company-search-input').val(bill_header[0].company_name);
        $('.company_id').val(bill_header[0].company_id);
        function numberformate(number) {
            // Simple number formatting with commas
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        let html = '';
        bill_lines.forEach((element, index) => {
          // Build HTML row for each vendor
          html += `<tr class="bill-row">
                  <td>
                      ${element.bill_date}
                      <div class="due-date">Due Date: ${element.due_date}</div>
                      <input type="hidden" name="vendors[${index}][id]" value="${element.id}">
                      <input type="hidden" name="vendors[${index}][bill_id]" value="${element.bill_id}">
                      <input type="hidden" name="vendors[${index}][bill_date]" value="${element.bill_date}">
                      <input type="hidden" name="vendors[${index}][due_date]" value="${element.due_date}">
                  </td>
                  <td>
                      ${element.bill_number}
                      <input type="hidden" name="vendors[${index}][bill_number]" value="${element.bill_number}">
                  </td>
                  <td>-</td>
                  <td class="text-end">
                      ${numberformate(element.grand_total_amount)}
                      <input type="hidden" name="vendors[${index}][grand_total_amount]" value="${element.grand_total_amount}">
                  </td>
                  <td class="text-end">
                      ${numberformate(element.balance_amount)}
                      <input type="hidden" name="vendors[${index}][balance_amount]" value="${element.balance_amount}">
                  </td>
                  <td>
                      <input type="text" class="form-control datepicker" name="vendors[${index}][payment_date]" value="${element.bill_date}" />
                  </td>
                  <td>
                      <input type="number" class="form-control text-end payment-amount" name="vendors[${index}][amount]" value="${element.amount}" />
                  </td>
              </tr>
              `;

            });


            $('.payment-table tbody').html(html);
            $('.table-footer-amount').text(numberformate(bill_header[0].amount_used));
            $('#amount-paid').text(numberformate(bill_header[0].amount_used));
            $('#amount-used').text(numberformate(bill_header[0].amount_used));
            $('#notes').val(bill_header[0].note);

              // From server
              window.existingFiles = JSON.parse(bill_header[0].documents); // array of strings
              window.selectedFiles = []; // New files chosen
              $('#existingFilesInput').val(JSON.stringify(window.existingFiles));
              // Render existing files
              function renderExistingFiles() {
              $('#fileList').empty();
              $.each(window.existingFiles, function (index, name) {
                let files = Array.isArray(name) ? name : [name];
                $('#fileList').append(`
                  <li class="documentclk" data-filetype="document" data-files='${JSON.stringify(files)}'>
                    ${name}
                    <span class="remove-file1" data-type="existing" data-index="${index}" style="cursor:pointer; color:red;">❌</span>
                  </li>
                `);
              });
              $.each(window.selectedFiles, function (index, file) {
                let files = Array.isArray(file) ? file : [file];
                $('#fileList').append(`
                  <li class="documentclk" data-filetype="document" data-files='${JSON.stringify(files)}'>
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