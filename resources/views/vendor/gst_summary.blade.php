<!doctype html>
<html lang="en">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
#documentModal1 { z-index: 999999; }
.preview-card { background:#f8f9fa; border:1px solid #ddd; border-radius:10px; padding:12px; margin:10px; width:150px; text-align:center; font-size:12px; cursor:pointer; }
.preview-card img { max-width:100%; max-height:100px; object-fit:cover; margin-bottom:8px; }
/* ── GST slide-out panel ── */
.gst-panel {
    position: fixed; top: 0; right: -780px; width: 760px; height: 100vh;
    background: #fff; box-shadow: -4px 0 24px rgba(0,0,0,.15);
    z-index: 9999; display: flex; flex-direction: column;
    transition: right .3s ease; border-left: 3px solid #2ecc71;
    overflow: hidden;
}
.gst-panel.show { right: 0; }
.gst-panel-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:9998; }
.gst-panel-backdrop.show { display:block; }
.gp-header { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid #e9ecef; background:#f8f9fa; flex-shrink:0; }
.gp-title  { font-size:15px; font-weight:700; color:#1a1a2e; }
.gp-actions { display:flex; gap:8px; align-items:center; }
.gp-body { flex:1; overflow-y:auto; padding:18px 20px; }
.gp-section { background:#fff; border:1px solid #e9ecef; border-radius:8px; padding:14px; margin-bottom:14px; }
.gp-section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; margin-bottom:10px; padding-bottom:6px; border-bottom:1px solid #f0f0f0; }
.gp-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #f8f9fa; font-size:13px; }
.gp-row:last-child { border-bottom:none; }
.gp-label { color:#6c757d; font-weight:500; }
.gp-val   { color:#1a1a2e; font-weight:600; text-align:right; max-width:60%; }
.gp-items-table { width:100%; border-collapse:collapse; font-size:12px; }
.gp-items-table th { background:#f8f9fa; color:#6c757d; font-size:10px; text-transform:uppercase; padding:7px 10px; border-bottom:2px solid #e9ecef; }
.gp-items-table td { padding:7px 10px; border-bottom:1px solid #f5f5f5; }
.gp-totals { display:flex; flex-direction:column; gap:4px; font-size:13px; }
.gp-total-row { display:flex; justify-content:space-between; padding:4px 0; }
.gp-total-row.grand { font-weight:700; font-size:14px; border-top:2px solid #e9ecef; padding-top:8px; color:#2ecc71; }
.gp-gst-line { display:flex; justify-content:space-between; padding:3px 0; font-size:12px; color:#555; }
</style>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>

    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
    <div class="qd-card">

      {{-- ── Header ── --}}
      <div class="qd-header">
        <div class="qd-header-title">
          <i class="bi bi-percent"></i> GST Summary
                    </div>
        <div class="qd-header-actions">
          <button class="btn btn-sm qd-toggle-btn" id="toggleStats">
            <i class="bi bi-bar-chart-line me-1"></i>Stats
            <i class="bi bi-chevron-up qd-toggle-icon" id="statsChevron"></i>
          </button>
          <button class="btn btn-sm qd-toggle-btn" id="toggleFilters">
            <i class="bi bi-funnel me-1"></i>Filter
            <i class="bi bi-chevron-up qd-toggle-icon" id="filtersChevron"></i>
          </button>
          <button class="btn btn-success btn-sm" id="downloadExcelBtn">
            <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
          </button>
                          </div>
                      </div>

      {{-- ── Stats ── --}}
      <div class="qd-stats" id="statsSection">
        <div class="qd-stat-card qd-stat-blue qd-stat-active" data-stat-filter="" title="Show all GST bills">
          <div class="qd-stat-icon"><i class="bi bi-percent"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total GST Amount</div>
            <div class="qd-stat-value" data-stat-key="total_gst">₹{{ number_format($gstSummaryCalculation['total_gst'] ?? 0, 2) }}</div>
            <div class="qd-stat-sub">All bills with GST</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-orange" data-stat-filter="month" title="This Month GST">
          <div class="qd-stat-icon"><i class="bi bi-calendar-month"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">This Month GST</div>
            <div class="qd-stat-value" data-stat-key="this_month_gst">₹{{ number_format($gstSummaryCalculation['this_month_gst'] ?? 0, 2) }}</div>
            <div class="qd-stat-sub">Current month</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-red" data-stat-filter="pending" title="Filter: Pending GST">
          <div class="qd-stat-icon"><i class="bi bi-clock-history"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Pending GST</div>
            <div class="qd-stat-value" data-stat-key="pending_gst">₹{{ number_format($gstSummaryCalculation['pending_gst'] ?? 0, 2) }}</div>
            <div class="qd-stat-sub">Due to pay</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-green" data-stat-filter="paid" title="Filter: Paid GST">
          <div class="qd-stat-icon"><i class="bi bi-check-circle"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Paid GST</div>
            <div class="qd-stat-value" data-stat-key="paid_gst">₹{{ number_format($gstSummaryCalculation['paid_gst'] ?? 0, 2) }}</div>
            <div class="qd-stat-sub">Fully paid</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-purple" data-stat-filter="partial" title="Filter: Partially Paid GST">
          <div class="qd-stat-icon"><i class="bi bi-hourglass-split"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Partially Paid</div>
            <div class="qd-stat-value" data-stat-key="partial_gst">₹{{ number_format($gstSummaryCalculation['partial_gst'] ?? 0, 2) }}</div>
            <div class="qd-stat-sub">Partially payed</div>
          </div>
        </div>
      </div>

      {{-- ── Filters ── --}}
      <div class="qd-filters" id="filtersSection">
        {{-- Row 1 --}}
        <div class="qd-filter-row">
          <div class="qd-filter-group">
            <label><i class="bi bi-calendar3 me-1"></i>Date Range</label>
            <div class="qd-date-wrap" id="reportrange">
              <i class="bi bi-calendar3"></i>
              <span id="data_values">All Dates</span>
              <i class="bi bi-caret-down-fill" style="margin-left:auto;"></i>
                                <input type="hidden" class="data_values">
                        </div>
                    </div>

          <div class="qd-filter-group tax-dropdown-wrapper company-section">
            <label>Company</label>
                      <input type="text" class="form-control company-search-input dropdown-search-input" placeholder="Select Company" readonly>
                      <input type="hidden" name="company_id" class="company_id">
                      <div class="dropdown-menu tax-dropdown">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Company..."></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                        </div>
              <div class="dropdown-list multiselect company-list"></div>
                          </div>
                        </div>

          <div class="qd-filter-group tax-dropdown-wrapper state-section">
            <label>State</label>
            <input type="text" class="form-control state-search-input dropdown-search-input" placeholder="Select State" readonly>
                      <input type="hidden" name="state_id" class="state_id">
                      <div class="dropdown-menu tax-dropdown">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search State..."></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                          </div>
                        <div class="dropdown-list multiselect state-list">
                          <div data-value="Tamil Nadu" data-id="1">Tamil Nadu</div>
                <div data-value="Karnataka" data-id="2">Karnataka</div>
                          <div data-value="Kerala" data-id="3">Kerala</div>
                <div data-value="Andra Pradesh" data-id="4">Andra Pradesh</div>
                <div data-value="International" data-id="5">International</div>
                        </div>
                      </div>
                    </div>

          <div class="qd-filter-group tax-dropdown-wrapper zone-section">
            <label>Zone</label>
                      <input type="text" class="form-control zone-search-input dropdown-search-input" placeholder="Select Zone" readonly>
                      <input type="hidden" name="zone_id" class="zone_id">
                      <div class="dropdown-menu tax-dropdown">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Zone..."></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                        </div>
              <div class="dropdown-list multiselect zone-list"></div>
                          </div>
                        </div>
                      </div>

        {{-- Row 2 --}}
        <div class="qd-filter-row" style="margin-top:10px;">
          <div class="qd-filter-group tax-dropdown-wrapper branch-section">
            <label>Branch</label>
                        <input type="text" class="form-control branch-search-input dropdown-search-input" placeholder="Select Branch" readonly>
                        <input type="hidden" name="branch_id" class="branch_id">
                        <div class="dropdown-menu tax-dropdown">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Branch..."></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                          </div>
              <div class="dropdown-list multiselect branch-list"></div>
                        </div>
                      </div>

          <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
            <label>Vendor</label>
                      <input type="text" class="form-control vendor-search-input dropdown-search-input" placeholder="Select Vendor" readonly>
                      <input type="hidden" name="vendor_id" class="vendor_id">
                      <div class="dropdown-menu tax-dropdown">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Vendor..."></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                        </div>
              <div class="dropdown-list multiselect vendor-list"></div>
                          </div>
                        </div>
                      </div>
                    </div>

      {{-- ── Search bar ── --}}
      <div class="qd-search-row">
        <div class="qd-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" class="universal_search" placeholder="Search bill no, vendor, zone, branch...">
                    </div>
                  </div>

      {{-- ── Applied filters bar ── --}}
      <div class="qd-applied-bar">
                      <strong>Applied Filters:</strong>
        <div id="filter-summary"></div>
              </div>

      {{-- ── Table body ── --}}
      <div class="qd-table-wrap" id="tds-body">
        @include('vendor.partials.table.gst_summary_rows', ['billlist' => $billlist, 'gstSummaryCalculation' => $gstSummaryCalculation, 'perPage' => $perPage])
            </div>

                  </div>
                    </div>
                  </div>

{{-- ── GST Detail Panel ── --}}
<div class="gst-panel" id="gstDetailPanel">
  <div class="gp-header">
    <div class="gp-title" id="gp-bill-number">Bill Details</div>
    <div class="gp-actions">
      <button class="btn btn-sm btn-outline-secondary" id="gp-print-btn"><i class="bi bi-printer"></i> Print</button>
      <button class="btn btn-sm btn-outline-primary"   id="gp-pdf-btn"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
      <button class="btn btn-sm btn-outline-danger" id="closeGstPanel"><i class="bi bi-x-lg"></i></button>
                          </div>
                          </div>
  <div class="gp-body">
    {{-- Bill Info --}}
    <div class="gp-section">
      <div class="gp-section-title">Bill Info</div>
      <div class="gp-row"><span class="gp-label">Bill Number</span><span class="gp-val" id="gp-bill-no">—</span></div>
      <div class="gp-row"><span class="gp-label">Order / Reference</span><span class="gp-val" id="gp-order-no">—</span></div>
      <div class="gp-row"><span class="gp-label">Bill Date</span><span class="gp-val" id="gp-bill-date">—</span></div>
      <div class="gp-row"><span class="gp-label">Due Date</span><span class="gp-val" id="gp-due-date">—</span></div>
      <div class="gp-row"><span class="gp-label">Payment Terms</span><span class="gp-val" id="gp-pay-terms">—</span></div>
      <div class="gp-row"><span class="gp-label">Bill Status</span><span class="gp-val" id="gp-bill-status">—</span></div>
                          </div>
    {{-- Vendor --}}
    <div class="gp-section">
      <div class="gp-section-title">Vendor</div>
      <div class="gp-row"><span class="gp-label">Name</span><span class="gp-val" id="gp-vendor-name">—</span></div>
      <div class="gp-row"><span class="gp-label">Address</span><span class="gp-val" id="gp-vendor-addr">—</span></div>
                          </div>
    {{-- Totals --}}
    <div class="gp-section">
      <div class="gp-section-title">Totals</div>
      <div class="gp-totals">
        <div class="gp-total-row"><span>Sub Total</span><span id="gp-sub-total">—</span></div>
        <div class="gp-total-row"><span>Discount</span><span id="gp-discount">—</span></div>
        <div id="gp-gst-breakdown"></div>
        <div class="gp-total-row"><span>TDS (-)</span><span id="gp-tds-amount">—</span></div>
        <div class="gp-total-row grand"><span>Grand Total</span><span id="gp-grand-total">—</span></div>
                          </div>
                        </div>
    {{-- Items --}}
    <div class="gp-section">
      <div class="gp-section-title">Items &amp; Description</div>
      <table class="gp-items-table">
                      <thead>
                        <tr>
            <th>Item / Description</th>
            <th>Qty</th>
            <th>Rate</th>
                          <th>GST</th>
            <th>GST Amt</th>
            <th>Amount</th>
                        </tr>
                      </thead>
        <tbody id="gp-items-body"><tr><td colspan="6" class="text-center text-muted">—</td></tr></tbody>
                    </table>
                  </div>
    {{-- Documents --}}
    <div class="gp-section">
      <div class="gp-section-title">Documents</div>
      <div class="d-flex flex-wrap" id="gp-docs"></div>
                      </div>
                      </div>
                      </div>
<div class="gst-panel-backdrop" id="gstBackdrop"></div>

{{-- ── Document Modal ── --}}
<div class="modal fade" id="documentModal1" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
                      <div class="modal-content">
      <div class="modal-header" style="background-color:#080fd399;height:0px;">
        <h5 class="modal-title" style="color:#fff;font-size:12px;">Document Viewer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color:#fff;"></button>
                          </div>
                          <div class="row">
                          <div class="col-sm-3"><br>
          <div class="btn-group-vertical w-100" id="image_pdfs" style="margin-left:11px;"></div>
                                  </div>
                                      <div class="col-sm-9">
                                      <embed id="pdfmain" src="" width="100%" height="600px" />
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
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

<script>
    $(document).ready(function () {

  // Reset date display on load
  $('#data_values').text('All Dates');
  $('.data_values').val('');

  // ── Toggle Stats ──
  $('#toggleStats').on('click', function () {
    const $s = $('#statsSection'), $i = $('#statsChevron');
    $s.is(':visible') ? ($s.slideUp(200), $i.removeClass('bi-chevron-up').addClass('bi-chevron-down'))
                      : ($s.slideDown(200), $i.removeClass('bi-chevron-down').addClass('bi-chevron-up'));
  });

  // ── Toggle Filters ──
  $('#toggleFilters').on('click', function () {
    const $s = $('#filtersSection'), $i = $('#filtersChevron');
    $s.is(':visible') ? ($s.slideUp(200), $i.removeClass('bi-chevron-up').addClass('bi-chevron-down'))
                      : ($s.slideDown(200), $i.removeClass('bi-chevron-down').addClass('bi-chevron-up'));
  });

  // ── Stat card click filter ──
  let statFilter = '';
  $(document).on('click', '.qd-stat-card[data-stat-filter]', function () {
    $('.qd-stat-card').removeClass('qd-stat-active');
    $(this).addClass('qd-stat-active');
    statFilter = $(this).data('stat-filter');
    loadGst();
  });

  // ── Populate dropdowns ──
  const TblZonesModel = @json($TblZonesModel);
  const Tblcompany    = @json($Tblcompany);
  const Tblvendor     = @json($Tblvendor);

  (Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data||[])).forEach(z => {
    $('.zone-list').append(`<div data-id="${z.id}">${z.name}</div>`);
  });
  (Tblcompany.data||[]).forEach(c => {
    $('.company-list').append(`<div data-value="${c.company_name}" data-id="${c.id}">${c.company_name}</div>`);
  });
  Tblvendor.forEach(v => {
    $('.vendor-list').append(`<div data-value="${v.display_name}" data-id="${v.id}">${v.display_name}</div>`);
  });

  // ── Dropdown open ──
          $(document).on('click', '.dropdown-search-input', function (e) {
            e.stopPropagation();
    $('.dropdown-menu.tax-dropdown').hide();
            const $input = $(this);
            let $dropdown = $input.data('dropdown');
            if (!$dropdown) {
              $dropdown = $input.siblings('.dropdown-menu').clone(true);
              $('body').append($dropdown);
              $input.data('dropdown', $dropdown);
            }
            $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
            const offset = $input.offset();
    $dropdown.css({ position:'absolute', top:offset.top+$input.outerHeight(), left:offset.left, width:$input.outerWidth(), zIndex:9999 }).show();
            $dropdown.find('.inner-search').focus();
          });

          $(document).on('keyup', '.inner-search', function () {
    const v = $(this).val().toLowerCase();
            $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1);
            });
          });

          $(document).on('click', '.dropdown-list.multiselect div', function (e) {
            e.stopPropagation();
            $(this).toggleClass('selected');
    updateMultiSelection($(this).closest('.tax-dropdown'));
          });
          $(document).on('click', '.select-all', function (e) {
            e.stopPropagation();
    const $d = $(this).closest('.tax-dropdown');
    $d.find('.dropdown-list.multiselect div').addClass('selected');
    updateMultiSelection($d);
  });
          $(document).on('click', '.deselect-all', function (e) {
            e.stopPropagation();
    const $d = $(this).closest('.tax-dropdown');
    $d.find('.dropdown-list.multiselect div').removeClass('selected');
    updateMultiSelection($d);
  });
          $(document).on('click', function (e) {
            if (!$(e.target).closest('.tax-dropdown-wrapper').length && !$(e.target).closest('.tax-dropdown').length) {
              $('.dropdown-menu.tax-dropdown').hide();
            }
          });
  $(document).on('click', '.dropdown-menu.tax-dropdown', function (e) { e.stopPropagation(); });

          function updateMultiSelection($dropdown) {
            const wrapper = $dropdown.data('wrapper');
            if (!wrapper) return;
    const selectedItems = [], selectedIds = [];
            $dropdown.find('.dropdown-list.multiselect div.selected').each(function () {
              selectedItems.push($(this).text().trim());
              selectedIds.push($(this).data('id'));
            });
    wrapper.find('.dropdown-search-input').val(selectedItems.join(', '));
    const $hidden = wrapper.find('input[type="hidden"]');
    $hidden.val(selectedIds.join(','));
    $hidden.trigger('click');
  }

  // ── Zone → Branch fetch ──
          $('.zone_id').on('click', function () {
    var id = $('.zone_id').val();
    if (!id) return;
    let fd = new FormData(); fd.append('id', id);
            $.ajax({
      url: '{{ route("superadmin.getbranchfetch") }}', method: 'POST',
      data: fd, processData: false, contentType: false,
      headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
      success: function (res) {
        if (res.branch !== '') {
                    $('.branch-list div').remove();
          res.branch.forEach(b => $('.branch-list').append(`<div data-id="${b.id}">${b.name}</div>`));
        }
                }
            });
          });
  $('.zone-list div').on('click', function () { $('.branch-search-input').val(''); $('.branch_id').val(''); });

  // ── Filter state ──
              let filters = {
    date_from:'', date_to:'', zone_id:'', zone_name:'', branch_id:'', branch_name:'',
    company_id:'', company_name:'', vendor_id:'', vendor_name:'',
    state_id:'', state_name:'', universal_search:''
  };
  var appliedFilters = {};

              function renderSummary() {
    let html = '';
    if (filters.date_from && filters.date_to)
      html += `<span class="filter-badge remove-icon" data-type="date"><i class="bi bi-calendar3 me-1"></i>${filters.date_from} → ${filters.date_to} &times;</span>`;
    if (filters.company_id)
      html += `<span class="filter-badge remove-icon" data-type="company"><i class="bi bi-building me-1"></i>${filters.company_name} &times;</span>`;
    if (filters.state_id)
      html += `<span class="filter-badge remove-icon" data-type="state"><i class="bi bi-map me-1"></i>${filters.state_name} &times;</span>`;
    if (filters.zone_id)
      html += `<span class="filter-badge remove-icon" data-type="zone"><i class="bi bi-geo-alt me-1"></i>${filters.zone_name} &times;</span>`;
    if (filters.branch_id)
      html += `<span class="filter-badge remove-icon" data-type="branch"><i class="bi bi-diagram-3 me-1"></i>${filters.branch_name} &times;</span>`;
    if (filters.vendor_id)
      html += `<span class="filter-badge remove-icon" data-type="vendor"><i class="bi bi-person me-1"></i>${filters.vendor_name} &times;</span>`;
    if (filters.universal_search)
      html += `<span class="filter-badge remove-icon" data-type="search"><i class="bi bi-search me-1"></i>${filters.universal_search} &times;</span>`;
    if (html) html += `<span class="filter-badge filter-clear" id="clear-all">Clear all</span>`;
    $('#filter-summary').html(html);
  }

  function loadGst(page = 1, perPage = $('#per_page').val()) {
    appliedFilters = {
      per_page: perPage, page,
      date_from: filters.date_from, date_to: filters.date_to,
      zone_id: filters.zone_id, branch_id: filters.branch_id,
      company_id: filters.company_id, vendor_id: filters.vendor_id,
      state_name: filters.state_name, universal_search: filters.universal_search,
      stat_filter: statFilter
    };
    $.ajax({
      url: '{{ route("superadmin.getgstsummary") }}',
      type: 'GET', data: appliedFilters,
      success: function (data) {
        if (data && typeof data === 'object' && data.html !== undefined) {
          $('#tds-body').html(data.html);
          if (data.stats) {
            $.each(data.stats, function(key, val) {
              $('[data-stat-key="' + key + '"]').text('₹' + parseFloat(val || 0).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2}));
            });
          }
        } else {
          $('#tds-body').html(data);
        }
        renderSummary();
      }
    });
  }

  // ── Multiselect → filter wire-up ──
  function setupMultiSelect(selectorInput, selectorHidden) {
    var ns = 'click.ms' + selectorHidden.replace(/[^a-zA-Z0-9]/g, '');
    $(document).off(ns, selectorHidden).on(ns, selectorHidden, function () {
      const selectedIds  = $(this).val();
      const selectedText = $(selectorInput).val();
      if (selectorHidden === '.zone_id')         { filters.zone_id    = selectedIds; filters.zone_name    = selectedText; }
      else if (selectorHidden === '.branch_id')  { filters.branch_id  = selectedIds; filters.branch_name  = selectedText; }
      else if (selectorHidden === '.company_id') { filters.company_id = selectedIds; filters.company_name = selectedText; }
      else if (selectorHidden === '.vendor_id')  { filters.vendor_id  = selectedIds; filters.vendor_name  = selectedText; }
      else if (selectorHidden === '.state_id')   { filters.state_id   = selectedIds; filters.state_name   = selectedText; }
      loadGst();
    });
  }
  setupMultiSelect('.zone-search-input',    '.zone_id');
  setupMultiSelect('.branch-search-input',  '.branch_id');
  setupMultiSelect('.company-search-input', '.company_id');
  setupMultiSelect('.vendor-search-input',  '.vendor_id');
  setupMultiSelect('.state-search-input',   '.state_id');

  $('.universal_search').on('keyup', function () {
    filters.universal_search = $(this).val(); loadGst();
  });

  // Date change — triggered by quotation_search.js cb()
  $('.data_values').on('change', function () {
    let dateRange = $(this).val();
    if (dateRange && dateRange.includes(' to ')) {
      let parts = dateRange.split(' to ');
      filters.date_from = parts[0].trim(); filters.date_to = parts[1].trim();
    } else { filters.date_from = ''; filters.date_to = ''; }
    loadGst();
  });

  // Remove filter badge
  $('#filter-summary').on('click', '.remove-icon', function () {
    const type = $(this).data('type');
    if (type==='date')    { filters.date_from=''; filters.date_to=''; $('#data_values').text('All Dates'); $('.data_values').val(''); }
    else if (type==='zone')    { filters.zone_id=''; filters.zone_name=''; $('.zone_id').val(''); $('.zone-search-input').val(''); $('.zone-list div').removeClass('selected'); }
    else if (type==='branch')  { filters.branch_id=''; filters.branch_name=''; $('.branch_id').val(''); $('.branch-search-input').val(''); $('.branch-list div').removeClass('selected'); }
    else if (type==='company') { filters.company_id=''; filters.company_name=''; $('.company_id').val(''); $('.company-search-input').val(''); $('.company-list div').removeClass('selected'); }
    else if (type==='vendor')  { filters.vendor_id=''; filters.vendor_name=''; $('.vendor_id').val(''); $('.vendor-search-input').val(''); $('.vendor-list div').removeClass('selected'); }
    else if (type==='state')   { filters.state_id=''; filters.state_name=''; $('.state_id').val(''); $('.state-search-input').val(''); $('.state-list div').removeClass('selected'); }
    else if (type==='search')  { filters.universal_search=''; $('.universal_search').val(''); }
    loadGst();
  });
  $('#filter-summary').on('click', '#clear-all', function () {
    filters = { date_from:'', date_to:'', zone_id:'', zone_name:'', branch_id:'', branch_name:'', company_id:'', company_name:'', vendor_id:'', vendor_name:'', state_id:'', state_name:'', universal_search:'' };
    statFilter = '';
    $('.zone-search-input,.branch-search-input,.company-search-input,.vendor-search-input,.state-search-input').val('');
    $('.zone_id,.branch_id,.company_id,.vendor_id,.state_id').val('');
    $('.data_values').val(''); $('#data_values').text('All Dates');
    $('.universal_search').val(''); $('.dropdown-list div').removeClass('selected');
    $('.qd-stat-card').removeClass('qd-stat-active');
    $('.qd-stat-card[data-stat-filter=""]').addClass('qd-stat-active');
    loadGst();
  });

  // Pagination
  $(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    const params = new URLSearchParams($(this).attr('href').split('?')[1]);
    loadGst(params.get('page') || 1, $('#per_page').val());
  });
  $(document).on('change', '#per_page', function () { loadGst(1, $(this).val()); });

  // ── Export Excel ──
  $('#downloadExcelBtn').on('click', function (e) {
    e.preventDefault();
    let ids = [];
    $('.bill-checkbox:checked').each(function () { ids.push($(this).closest('tr').data('id')); });
    let params = new URLSearchParams(appliedFilters);
    params.set('bill_ids', ids.join(','));
    window.location.href = "{{ route('gst.summary.download') }}?" + params.toString();
  });

  // ── GST Detail Panel ──
  function openGstPanel() { $('#gstDetailPanel').addClass('show'); $('#gstBackdrop').addClass('show'); $('body').css('overflow','hidden'); }
  function closeGstPanel() { $('#gstDetailPanel').removeClass('show'); $('#gstBackdrop').removeClass('show'); $('body').css('overflow','auto'); }
  $('#closeGstPanel, #gstBackdrop').on('click', closeGstPanel);

  function fmt(v) {
    if (!v && v !== 0) return '₹0.00';
    const n = typeof v === 'string' ? parseFloat(v.replace(/,/g,'')) : v;
    return '₹' + n.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
  }

  $(document).on('click', '.customer-row', function (e) {
    if ($(e.target).is('input[type="checkbox"]')) return;
    if ($(e.target).closest('.vendor_link').length) return;
    if ($(e.target).closest('.documentclk').length) return;

    const bill  = $(this).data('allbill');
    const addr  = $(this).data('vendor-address');
    const items = $(this).data('items');

    $('#gp-bill-number').text(bill.bill_number || 'Bill Details');
    $('#gp-bill-no').text(bill.bill_number || '—');
    $('#gp-order-no').text(bill.order_number || '—');
    $('#gp-bill-date').text(bill.bill_date || '—');
    $('#gp-due-date').text(bill.due_date || '—');
    $('#gp-pay-terms').text(bill.payment_terms || '—');

    const statusMap = {
      'paid':'<span class="qd-badge qd-badge-approved">Paid</span>',
      'due to pay':'<span class="qd-badge qd-badge-rejected">Due to Pay</span>',
      'partially payed':'<span class="qd-badge qd-badge-pending">Partially Payed</span>'
    };
    $('#gp-bill-status').html(statusMap[(bill.bill_status||'').toLowerCase()] || bill.bill_status || '—');

    if (addr) $('#gp-vendor-addr').text(`${addr.address||''}, ${addr.city||''}, ${addr.state||''} - ${addr.zip_code||''}`);
    $('#gp-vendor-name').text(bill.vendor_name || '—');
    $('#gp-sub-total').text(fmt(bill.sub_total_amount));
    $('#gp-discount').text(fmt(bill.discount_amount));
    $('#gp-tds-amount').text(fmt(bill.tax_amount));
    $('#gp-grand-total').text(fmt(bill.grand_total_amount));

    // GST breakdown
    let gstHtml = '';
    if (Array.isArray(items)) {
      const gstMap = {};
      items.forEach(i => {
        if (parseFloat(i.gst_amount) > 0) {
          const label = i.gst_name || `GST ${i.gst_rate}%`;
          gstMap[label] = (gstMap[label] || 0) + parseFloat(i.gst_amount);
        }
      });
      Object.entries(gstMap).forEach(([k, v]) => {
        gstHtml += `<div class="gp-gst-line"><span>${k}</span><span>${fmt(v)}</span></div>`;
      });
    }
    $('#gp-gst-breakdown').html(gstHtml);

    // Items
    if (Array.isArray(items) && items.length) {
      $('#gp-items-body').html(items.map(i => `
        <tr>
          <td>${i.item_details||'—'}</td>
          <td>${i.quantity||1}</td>
          <td>${i.rate||'0.00'}</td>
          <td>${i.gst_name||'—'}</td>
          <td>${fmt(i.gst_amount)}</td>
          <td>${fmt(i.amount)}</td>
        </tr>`).join(''));
    } else {
      $('#gp-items-body').html('<tr><td colspan="6" class="text-center text-muted">No items</td></tr>');
    }

    // Documents
    let docsHtml = '';
    try {
      const docs = typeof bill.documents === 'string' ? JSON.parse(bill.documents) : (bill.documents||[]);
      if (Array.isArray(docs) && docs.length) {
        docs.forEach(f => {
          const ext = f.split('.').pop().toLowerCase();
          const billBase = '{{ asset("uploads/vendor/bill") }}/';
          const icon = ['jpg','jpeg','png','gif','webp'].includes(ext)
            ? `${billBase}${f}`
            : ext === 'pdf' ? 'https://cdn-icons-png.flaticon.com/512/337/337946.png'
                            : 'https://cdn-icons-png.flaticon.com/512/564/564619.png';
          const fa = JSON.stringify([`${billBase}${f}`]);
          docsHtml += `<div class="preview-card documentclk" data-filetype="documents" data-files='${fa}'>`
                    + `<img src="${icon}" style="height:50px;"><div>${f}</div></div>`;
        });
      } else { docsHtml = '<span class="text-muted small">No documents</span>'; }
    } catch(e) { docsHtml = '<span class="text-muted small">No documents</span>'; }
    $('#gp-docs').html(docsHtml);

    $('#gstDetailPanel').data('bill-id', bill.id);
    openGstPanel();
  });

  // PDF / Print
  $('#gp-pdf-btn').on('click', function () {
    const id = $('#gstDetailPanel').data('bill-id');
    $.ajax({ url:'{{ route("superadmin.getbillpdf") }}', method:'GET', data:{id}, xhrFields:{responseType:'blob'},
      success: function(data) {
        const link = document.createElement('a');
        link.href = URL.createObjectURL(new Blob([data],{type:'application/pdf'}));
        link.download = `gst_bill_${id}.pdf`; document.body.appendChild(link); link.click(); document.body.removeChild(link);
      }
    });
  });
  $('#gp-print-btn').on('click', function () {
    const id = $('#gstDetailPanel').data('bill-id');
    $.ajax({ url:'{{ route("superadmin.getbillprint") }}', method:'GET', data:{id}, xhrFields:{responseType:'blob'},
      success: function(r) { $('#pdfFrame').attr('src', URL.createObjectURL(new Blob([r],{type:'application/pdf'}))); $('#pdfPreviewModal').modal('show'); }
    });
  });
  $(document).on('click', '.modal-close-fallback', function(e) {
    e.preventDefault();
    try { if (typeof bootstrap !== 'undefined') { var m = bootstrap.Modal.getInstance(document.getElementById('pdfPreviewModal')) || new bootstrap.Modal(document.getElementById('pdfPreviewModal')); m.hide(); return; } } catch(err){}
    if ($.fn && $.fn.modal) { $('#pdfPreviewModal').modal('hide'); return; }
    $('#pdfPreviewModal').hide(); $('.modal-backdrop').remove(); $('body').removeClass('modal-open');
  });

  // Document viewer
  $(document).on('click', '.documentclk', function (e) {
    e.stopPropagation();
    $('#documentModal1').modal('show');
    const filesData = $(this).attr('data-files');
    let fileArray = [];
    try { fileArray = JSON.parse(filesData); if (typeof fileArray==='string') fileArray=JSON.parse(fileArray); } catch(er){return;}
    if (!Array.isArray(fileArray)||!fileArray.length) return;
    $('#pdfmain').attr('src', fileArray[0]);
    let views = '';
    fileArray.forEach(file => {
      const fn = file.split('/').pop().trim();
      views += `<button style="font-size:11px;" type="button" class="btn btn-primary pdf-btn mb-1" data-filepath="${file}">${fn}</button>`;
    });
    $('#image_pdfs').html(views);
  });
  $(document).on('click', '.pdf-btn', function () {
    if (!$(this).data('filepath')) return;
    $('.pdf-btn').removeClass('active'); $(this).addClass('active');
    $('#pdfmain').attr('src', $(this).data('filepath'));
  });

  // Select All checkbox
  $(document).on('change', '#selectAll', function () {
    $('.bill-checkbox').prop('checked', $(this).prop('checked'));
  });

});
</script>

<script>
// Override cb for "All Dates" preset support
function cb(start, end, label) {
    if (label === 'All Dates') {
        $('#data_values').text('All Dates');
        $('.data_values').val('');
    } else {
        $('#data_values').text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('.data_values').val(start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY'));
    }
    $('.data_values').trigger('change');
}
</script>

{{-- PDF Preview Modal --}}
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document" style="max-width:90%;">
    <div class="modal-content">
      <div class="modal-header position-relative">
        <h5 class="modal-title">Bill Preview</h5>
        <button type="button" class="modal-close-fallback" aria-label="Close preview"
          style="position:absolute;right:1rem;top:0.6rem;font-size:1.4rem;background:none;border:0;">&times;</button>
      </div>
      <div class="modal-body p-0">
        <iframe id="pdfFrame" src="" width="100%" height="600px" style="border:none;"></iframe>
      </div>
    </div>
  </div>
</div>

@include('superadmin.superadminfooter')
</body>
</html>
