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
  .converter{
    display: flex;
    gap: 20px;
    /* cursor: pointer; */
  }
</style>
<body style="overflow-x: hidden;">

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

            <div class="container-box">
                <div class="header-bar d-flex justify-content-between align-items-center">
                  <div class="converter">
                    <button id="po-convert" class="btn btn-outline-primary btn-sm me-2">PO Convert</button>
                    <button id="bill-convert" class="btn btn-outline-secondary btn-sm">Bill Convert</button>
                  </div>

                  <div>
                    <a id="create-grn" class="btn btn-primary btn-sm">Convert GRN</a>
                    <span class="ellipsis">⋮</span>
                  </div>
                </div>
                <div style="overflow-x: auto;" id="po-table">
                  <table class="table table-hover mb-0">
                      <thead>
                      <tr>
                          <th style="width: 30px;"><span class="filter-icon">⚙️</span></th>
                          <th>DATE</th>
                          <th>PURCHASE ORDER #</th>
                          <th>REFERENCE NUMBER</th>
                          <th>VENDOR NAME</th>
                          <th>DUE DATE</th>
                          <th>AMOUNT</th>
                          <th>STATUS</th>
                      </tr>
                      </thead>
                      <tbody>
                          @foreach ($purchaselist as $bill)
                              <tr class="customer-row" data-type="po" data-id="{{ $bill->id }}"
                                  data-purchase_order_number="{{ $bill->purchase_order_number }}"
                                  data-delivery_address="{{ $bill->delivery_address }}"
                                  data-order-number="{{ $bill->order_number }}"
                                  data-vendor-name="{{ $bill->vendor_name }}"
                                  data-vendor-address='@json($bill->TblBilling)'
                                  data-vendor='@json($bill->Tblvendor)'
                                  data-purchase_all='@json($bill)'
                                  data-bill-date="{{ $bill->bill_date }}"
                                  data-due-date="{{ $bill->due_date }}"
                                  data-approval_status="{{ $bill->approval_status }}"
                                  data-payment-terms="{{ $bill->payment_terms }}"
                                  data-discount_amount="{{ $bill->discount_amount }}"
                                  data-grand-total="{{ $bill->grand_total_amount }}"
                                  data-sub-total="{{ $bill->sub_total_amount }}"
                                  data-note="{{ $bill->note ?? 'No notes' }}"
                                  data-items='@json($bill->BillLines)'>
                                  <td><input type="checkbox" /></td>
                                  <td><a href="#" class="customer-link">{{ $bill->bill_date }}</a></td>
                                  <td>{{ $bill->purchase_order_number }}</td>
                                  <td>{{ $bill->order_number }}</td>
                                  <td>{{ $bill->vendor_name }}</td>
                                  <td>{{ $bill->due_date }}</td>
                                  <td>₹{{$bill->grand_total_amount }}</td>
                                  <td>
                                    @if($bill->approval_status == 1)
                                        <!-- Approved -->
                                        <button class="btn btn-success btn-sm" disabled>Approved</button>

                                    @elseif($bill->reject_status == 1)
                                        <!-- Rejected -->
                                        <button class="btn btn-danger btn-sm" disabled>Rejected</button>
                                    @endif

                                  </td>
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
                  <!-- ✅ Pagination Controls -->
                   @if($purchaselist->total() > 10)
                    <div class="d-flex justify-content-between">
                        <div class="mt-3">
                            {{ $purchaselist->appends(['table' => 'po', 'per_page' => $perPage])->links('pagination::bootstrap-4') }}
                        </div>
                        <div>
                            <form method="GET" id="perPageForm">
                                <input type="hidden" name="table" value="{{ $activeTable ?? 'po' }}">
                                <select name="per_page" id="per_page" class="form-control form-control-sm" style="width: 70px; display: inline-block;">
                                    @foreach([10, 25, 50, 100,250,500] as $size)
                                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                    @endforeach
                                </select>
                                <span>entries</span>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>

                <div style="overflow-x: auto;display:none;" id="bill-table" >
                  <table class="table table-hover mb-0">
                      <thead>
                      <tr>
                          <th style="width: 30px;"><span class="filter-icon">⚙️</span></th>
                          <th>DATE</th>
                          <th>BILL #</th>
                          <th>REFERENCE NUMBER</th>
                          <th>VENDOR NAME</th>
                          <th>DUE DATE</th>
                          <th>AMOUNT</th>

                      </tr>
                      </thead>
                      <tbody>
                          @foreach ($billlist as $bill)
                              <tr class="customer-row" data-type="bill" data-id="{{ $bill->id }}"
                                  data-bill-number="{{ $bill->bill_number }}"
                                  data-order-number="{{ $bill->order_number }}"
                                  data-vendor-name="{{ $bill->vendor_name }}"
                                  data-vendor-address='@json($bill->TblBilling)'
                                  data-allbill='@json($bill)'
                                  data-vendor='@json($bill->Tblvendor)'
                                  data-bank='@json($bill->Tblbankdetails)'
                                  data-bill-date="{{ $bill->bill_date }}"
                                  data-due-date="{{ $bill->due_date }}"
                                  data-payment-terms="{{ $bill->payment_terms }}"
                                  data-discount_amount="{{ $bill->discount_amount }}"
                                  data-grand-total="{{ $bill->grand_total_amount }}"
                                  data-sub-total="{{ $bill->sub_total_amount }}"
                                  data-note="{{ $bill->note ?? 'No notes' }}"
                                  data-items='@json($bill->BillLines)'>
                                  <td><input  type="checkbox" /></td>
                                  <td><a href="#"  class="customer-link">{{ $bill->bill_date }}</a></td>
                                  <td>{{ $bill->bill_number }}</td>
                                  <td>{{ $bill->order_number }}</td>
                                  <td>{{ $bill->vendor_name }}</td>
                                  <td>{{ $bill->due_date }}</td>
                                  <td>₹{{$bill->grand_total_amount }}</td>
                                  {{-- <td class="neft_modal"><i class="fas fa-money-check-alt"></i> NEFT Generate</td> --}}
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
                  <!-- ✅ Pagination Controls -->
                   @if($billlist->total() > 10)
                    <div class="d-flex justify-content-between">
                        <div class="mt-3">
                            {{ $billlist->appends(['table' => 'bill', 'per_page' => $perPage])->links('pagination::bootstrap-4') }}
                        </div>
                        <div>
                            <form method="GET" id="perPageForm">
                                <input type="hidden" name="table" value="{{ $activeTable ?? 'po' }}">
                                <select name="per_page" id="per_page" class="form-control form-control-sm" style="width: 70px; display: inline-block;">
                                    @foreach([10, 25, 50, 100,250,500] as $size)
                                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                    @endforeach
                                </select>
                                <span>entries</span>
                            </form>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@if ($errors->any())
<script>
    $(document).ready(function () {
        $('#exampleModal').modal('show');
    });
</script>
@endif

<script>
  $(document).ready(function() {
    // Handle PO/Bill convert button clicks
    $('#po-convert').click(function() {
        $('#bill-table').hide();
        $('#po-table').show();
        // Update URL parameter to remember which table is active
        updateUrlParam('table', 'po');
    });

    $('#bill-convert').click(function() {
        $('#po-table').hide();
        $('#bill-table').show();
        // Update URL parameter to remember which table is active
        updateUrlParam('table', 'bill');
    });

    // Check URL parameter on page load to show the correct table
    const urlParams = new URLSearchParams(window.location.search);
    const activeTable = urlParams.get('table');
    if (activeTable === 'bill') {
        $('#po-table').hide();
        $('#bill-table').show();
    } else {
        $('#bill-table').hide();
        $('#po-table').show();
    }

    // Handle per page changes
    $('#per_page').change(function() {
        $('#perPageForm').submit();
    });

    // Function to update URL parameter without reloading
    function updateUrlParam(key, value) {
        const url = new URL(window.location);
        url.searchParams.set(key, value);
        window.history.pushState({}, '', url);
    }
});
$(document).ready(function () {
    // $('#po-convert').on('click', function () {
    //   $('#po-table').show();
    //   $('#bill-table').hide();
    //    $('#po-table input[type="checkbox"]').prop('checked', false);
    // });

    // $('#bill-convert').on('click', function () {
    //   $('#bill-table').show();
    //   $('#po-table').hide();
    //   $('#po-table input[type="checkbox"]').prop('checked', false);
    // });
  $('#create-grn').on('click', function () {
      const checkedRow = $('table input[type="checkbox"]:checked').closest('tr');

      if (checkedRow.length === 0) {
          alert('Please select a row first.');
          return;
      }
      if (checkedRow.length > 1) {
          alert('one row only acceptable.');
          return;
      }

      const id = checkedRow.data('id');
      const type = checkedRow.data('type');

      // Directly construct the URL instead of using AJAX if you're just redirecting
      window.location.href = "{{ route('superadmin.getgrncreate') }}?id=" + id + "&type=" + type;
  });

    // Close modal handler for button and overlay
    $(document).on('click', '.close-modal, #modalOverlay', function (e) {
        e.stopPropagation();
        closeModal();
    });

    // Handle keyboard escape key to close modal
    $(document).on('keyup', function (e) {
        if (e.key === "Escape") {
            closeModal();
        }
    });

    // Currency formatter
    function formatCurrency(amount) {
        if (!amount) return '₹0.00';
        const num = typeof amount === 'string' ? parseFloat(amount.replace(/,/g, '')) : amount;
        return '₹' + num.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
});

const vendorsaveUrl = "{{ route('superadmin.vendorsave') }}";
const vendorfetch = "{{ route('superadmin.vendorfetch') }}";
</script>

<!-- [ Main Content ] end -->
@include('superadmin.superadminfooter')
</body>
<!-- [Body] end -->
</html>