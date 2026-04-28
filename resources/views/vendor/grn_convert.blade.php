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
  .grn-bill-convert-only #po-table { display: none !important; }
  .grn-bill-convert-only #bill-table { display: block !important; }
  .grn-bill-convert-only .converter { flex: 1; min-width: 0; max-width: 480px; margin-right: 12px; }
  .grn-convert-search-pill { position: relative; width: 100%; max-width: 100%; }
  .grn-convert-search-pill i.bi-search {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    color: #94a3b8; font-size: 15px; pointer-events: none; z-index: 1;
  }
  .grn-convert-search-pill .form-control {
    border-radius: 8px; border: 1px solid #c7d2e0; background: #fff;
    padding: 0.4rem 0.85rem 0.4rem 2.35rem; font-size: 14px; color: #1e293b;
  }
  .grn-convert-search-pill .form-control::placeholder { color: #94a3b8; }
  .grn-convert-search-pill .form-control:focus { border-color: #94a3b8; box-shadow: 0 0 0 2px rgba(148, 163, 184, 0.2); }
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

            <div class="container-box grn-bill-convert-only">
                <div class="header-bar d-flex justify-content-between align-items-center">
                  <div class="converter">
                    <div class="grn-convert-search-pill">
                      <i class="bi bi-search" aria-hidden="true"></i>
                      <input type="search" class="form-control" id="grn-convert-search" name="q"
                        placeholder="Search Bill No, Vendor Name, Reference Number…"
                        value="{{ e($q ?? request('q', '')) }}"
                        autocomplete="off">
                    </div>
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
                <div id="grn-ajax-bill">
                @fragment('grn-bill-table-ajax')
                    @php
                    $q = $q ?? request('q', '');
                    $perPage = $perPage ?? 10;
                    $__billAppend = array_merge(
                        ['table' => 'bill', 'per_page' => $perPage],
                        (string) $q !== '' ? ['q' => $q] : []
                    );
                    @endphp
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
                   @if($billlist->total() > 10)
                    <div class="d-flex justify-content-between">
                        <div class="mt-3">
                            {{ $billlist->appends($__billAppend)->links('pagination::bootstrap-4') }}
                        </div>
                        <div>
                            <form method="GET" id="perPageFormGrnBill" onsubmit="return false;">
                                <input type="hidden" name="table" value="{{ $activeTable ?? 'bill' }}">
                                <select name="per_page" class="form-control form-control-sm" style="width: 70px; display: inline-block;">
                                    @foreach([10, 25, 50, 100,250,500] as $size)
                                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                    @endforeach
                                </select>
                                <span>entries</span>
                            </form>
                        </div>
                    </div>
                    @endif
                @endfragment
                </div>
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
    // Bill data only: keep bill table visible, PO table hidden
    $('#po-table').hide();
    $('#bill-table').show();

    var grnFetchController = null;
    var grnRequestId = 0;
    function grnAsyncReplace(url) {
      if (!document.getElementById('grn-ajax-bill')) return;
      if (grnFetchController) {
        grnFetchController.abort();
      }
      grnRequestId += 1;
      var thisReq = grnRequestId;
      grnFetchController = new AbortController();
      var c = grnFetchController;
      return fetch(String(url), {
        signal: c.signal,
        credentials: 'same-origin',
        cache: 'no-store',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      })
        .then(function (r) {
          if (thisReq !== grnRequestId) return;
          if (!r.ok) {
            throw new Error('Network response was not ok');
          }
          if ((r.headers.get('content-type') || '').indexOf('application/json') === -1) {
            throw new Error('Expected JSON from server');
          }
          return r.json();
        })
        .then(function (d) {
          if (thisReq !== grnRequestId) { return; }
          if (d == null) { return; }
          if (d.html) {
            document.getElementById('grn-ajax-bill').innerHTML = d.html;
          }
          var u2 = new URL(String(url), window.location.origin);
          u2.searchParams.delete('grn_async');
          window.history.replaceState(null, '', u2);
        })
        .catch(function (err) {
          if (err && err.name === 'AbortError') { return; }
          if (typeof console !== 'undefined' && console.error) {
            console.error('grn async:', err);
          }
        });
    }
    var grnSearchT = null;
    $(document).on('input', '#grn-convert-search', function () {
      clearTimeout(grnSearchT);
      var $in = $(this);
      grnSearchT = setTimeout(function () {
        var t = $in.val() != null ? String($in.val()).trim() : '';
        var u = new URL(window.location.href);
        u.searchParams.set('table', 'bill');
        if (t.length) { u.searchParams.set('q', t); } else { u.searchParams.delete('q'); }
        u.searchParams.delete('bill_page');
        u.searchParams.set('grn_async', '1');
        u.searchParams.delete('po_page');
        grnAsyncReplace(u.toString());
      }, 300);
    });
    $(document).on('click', '#grn-ajax-bill .pagination a', function (e) {
      e.preventDefault();
      if (!this.getAttribute('href')) return;
      var u = new URL(this.getAttribute('href'), window.location.origin);
      u.searchParams.set('grn_async', '1');
      u.searchParams.set('table', 'bill');
      grnAsyncReplace(u.toString());
    });
    $(document).on('change', '#grn-ajax-bill select[name=per_page]', function () {
      var u = new URL(window.location.href);
      u.searchParams.set('per_page', $(this).val());
      u.searchParams.set('table', 'bill');
      u.searchParams.delete('bill_page');
      u.searchParams.set('grn_async', '1');
      grnAsyncReplace(u.toString());
    });
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
