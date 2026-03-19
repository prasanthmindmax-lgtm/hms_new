<!doctype html>
<html lang="en">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @include('superadmin.superadminhead')

  <link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
  <link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
  <style>
    .rv-tab {
      padding: 9px 22px;
      font-size: 13px;
      font-weight: 600;
      border: none;
      background: transparent;
      color: #6b7280;
      border-bottom: 2px solid transparent;
      margin-bottom: -2px;
      cursor: pointer;
      transition: color .15s, border-color .15s;
    }
    .rv-tab:hover { color: #4f6ef7; }
    .rv-tab.active {
      color: #4f6ef7;
      border-bottom: 2px solid #4f6ef7;
    }
    .rv-tab-content { display: none; }
    .rv-tab-content.rv-active { display: block; }
  </style>
</head>

<body style="overflow-x: hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    <div class="qd-card">

      {{-- ── Header ── --}}
      <div class="qd-header">
        <div class="qd-header-title">
          <i class="bi bi-receipt-cutoff"></i>
          Expenses — <span style="color:#4f6ef7;font-weight:700;">{{ strtoupper($type) }}</span>
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
          <a href="{{ request()->fullUrlWithQuery(['export'=>'excel']) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
          </a>
          <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
          </a>
        </div>
      </div>

      {{-- ── Filters ── --}}
      <div class="qd-filters" id="filtersSection">
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

          <div class="qd-filter-group tax-dropdown-wrapper nature-section">
            <label>Nature of Payment</label>
            <input type="text" class="form-control nature-search-input dropdown-search-input" placeholder="Select Nature" readonly>
            <input type="hidden" name="nature_id" class="nature_id">
            <div class="dropdown-menu tax-dropdown">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Nature..."></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
              </div>
              <div class="dropdown-list multiselect account-list"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Search bar --}}
      <div class="qd-search-row">
        <div class="qd-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" class="universal_search" placeholder="Search bill, vendor, zone, branch...">
        </div>
      </div>

      {{-- Applied filters bar --}}
      <div class="qd-applied-bar">
        <strong>Applied Filters:</strong>
        <div id="filter-summary"></div>
      </div>

      {{-- Main content with AJAX refresh --}}
      <div id="report-body">
        @include('vendor.partials.table.report_view_rows', [
          'details'            => $details,
          'totalInvoiceAmount' => $totalInvoiceAmount,
          'totalTDS'           => $totalTDS,
          'totalGST'           => $totalGST,
          'totalFinalAmount'   => $totalFinalAmount,
          'groupedVendors'     => $groupedVendors
        ])
      </div>

    </div>
  </div>
</div>

{{-- Monthly Summary Modal --}}
<div class="modal fade" id="vendorBillModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:linear-gradient(135deg,#4f6ef7,#7c3aed);color:#fff;">
        <h5 class="modal-title" id="vendorBillModalLabel">Vendor Monthly Summary</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <table class="table table-hover mb-0">
          <thead style="background:#f8f9fc;">
            <tr>
              <th style="padding:12px 16px;font-size:12px;font-weight:600;color:#6b7280;">MONTH</th>
              <th style="padding:12px 16px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;">TOTAL BILL AMOUNT</th>
            </tr>
          </thead>
          <tbody id="bill-summary-body">
            <tr><td colspan="2" class="text-center text-muted py-4">Select a vendor...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

<script>
$(document).ready(function () {
  // Reset date on load
  $('#data_values').text('All Dates');
  $('.data_values').val('');

  // Toggle Stats
  $('#toggleStats').on('click', function () {
    const $s = $('#report-body').find('.qd-stats'), $i = $('#statsChevron');
    if ($s.length) {
      $s.is(':visible') ? ($s.slideUp(200), $i.removeClass('bi-chevron-up').addClass('bi-chevron-down'))
                        : ($s.slideDown(200), $i.removeClass('bi-chevron-down').addClass('bi-chevron-up'));
    }
  });

  // Toggle Filters
  $('#toggleFilters').on('click', function () {
    const $s = $('#filtersSection'), $i = $('#filtersChevron');
    $s.is(':visible') ? ($s.slideUp(200), $i.removeClass('bi-chevron-up').addClass('bi-chevron-down'))
                      : ($s.slideDown(200), $i.removeClass('bi-chevron-down').addClass('bi-chevron-up'));
  });

  // Populate dropdowns
  const TblZonesModel = @json($TblZonesModel);
  const Tblcompany    = @json($Tblcompany);
  const Tblvendor     = @json($Tblvendor);
  const Tblaccount    = @json($Tblaccount);

  (Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data || [])).forEach(z => {
    $('.zone-list').append(`<div data-id="${z.id}">${z.name}</div>`);
  });
  (Tblcompany.data || []).forEach(c => {
    $('.company-list').append(`<div data-value="${c.company_name}" data-id="${c.id}">${c.company_name}</div>`);
  });
  Tblvendor.forEach(v => {
    $('.vendor-list').append(`<div data-value="${v.display_name}" data-id="${v.id}">${v.display_name}</div>`);
  });
  Tblaccount.forEach(a => {
    $('.account-list').append(`<div data-value="${a.name}" data-id="${a.id}">${a.name}</div>`);
  });

  // Dropdown interactions
  $(document).on('click', '.dropdown-search-input', function (e) {
    e.stopPropagation();
    $('.dropdown-menu.tax-dropdown').hide();
    const $input = $(this);
    let $dropdown = $input.data('dropdown');
    if (!$dropdown) { $dropdown = $input.siblings('.dropdown-menu').clone(true); $('body').append($dropdown); $input.data('dropdown', $dropdown); }
    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
    const offset = $input.offset();
    $dropdown.css({ position:'absolute', top:offset.top+$input.outerHeight(), left:offset.left, width:$input.outerWidth(), zIndex:9999 }).show();
    $dropdown.find('.inner-search').focus();
  });
  $(document).on('keyup', '.inner-search', function () {
    const v = $(this).val().toLowerCase();
    $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function () { $(this).toggle($(this).text().toLowerCase().includes(v)); });
  });
  $(document).on('click', '.dropdown-list.multiselect div', function (e) {
    e.stopPropagation(); $(this).toggleClass('selected'); updateMultiSelection($(this).closest('.tax-dropdown'));
  });
  $(document).on('click', '.select-all', function (e) {
    e.stopPropagation();
    const $d = $(this).closest('.tax-dropdown'); $d.find('.dropdown-list.multiselect div').addClass('selected'); updateMultiSelection($d);
  });
  $(document).on('click', '.deselect-all', function (e) {
    e.stopPropagation();
    const $d = $(this).closest('.tax-dropdown'); $d.find('.dropdown-list.multiselect div').removeClass('selected'); updateMultiSelection($d);
  });
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.tax-dropdown-wrapper').length && !$(e.target).closest('.tax-dropdown').length)
      $('.dropdown-menu.tax-dropdown').hide();
  });
  function updateMultiSelection($dropdown) {
    const wrapper = $dropdown.data('wrapper'); if (!wrapper) return;
    const items = [], ids = [];
    $dropdown.find('.dropdown-list.multiselect div.selected').each(function () { items.push($(this).text().trim()); ids.push($(this).data('id')); });
    wrapper.find('.dropdown-search-input').val(items.join(', '));
    const $h = wrapper.find('input[type="hidden"]'); $h.val(ids.join(',')); $h.trigger('click');
  }

  // Zone → Branch fetch
  $('.zone_id').on('click', function () {
    const id = $('.zone_id').val(); if (!id) return;
    const fd = new FormData(); fd.append('id', id);
    $.ajax({
      url:'{{ route("superadmin.getbranchfetch") }}', method:'POST', data:fd, processData:false, contentType:false,
      headers:{'X-CSRF-TOKEN': $('input[name="_token"]').val()},
      success: function (res) {
        if (res.branch !== '') {
          $('.branch-list div').remove();
          res.branch.forEach(b => $('.branch-list').append(`<div data-id="${b.id}">${b.name}</div>`));
        }
      }
    });
  });
  $('.zone-list div').on('click', function () { $('.branch-search-input').val(''); $('.branch_id').val(''); });

  // Filter state
  let filters = { date_from:'', date_to:'', zone_id:'', zone_name:'', branch_id:'', branch_name:'', company_id:'', company_name:'', vendor_id:'', vendor_name:'', state_id:'', state_name:'', nature_id:'', nature_name:'', universal_search:'' };

  function renderSummary() {
    let html = '';
    if (filters.date_from && filters.date_to)
      html += `<span class="filter-badge remove-icon" data-type="date"><i class="bi bi-calendar3 me-1"></i>${filters.date_from} → ${filters.date_to} &times;</span>`;
    if (filters.company_id)  html += `<span class="filter-badge remove-icon" data-type="company"><i class="bi bi-building me-1"></i>${filters.company_name} &times;</span>`;
    if (filters.state_id)    html += `<span class="filter-badge remove-icon" data-type="state"><i class="bi bi-map me-1"></i>${filters.state_name} &times;</span>`;
    if (filters.zone_id)     html += `<span class="filter-badge remove-icon" data-type="zone"><i class="bi bi-geo-alt me-1"></i>${filters.zone_name} &times;</span>`;
    if (filters.branch_id)   html += `<span class="filter-badge remove-icon" data-type="branch"><i class="bi bi-diagram-3 me-1"></i>${filters.branch_name} &times;</span>`;
    if (filters.vendor_id)   html += `<span class="filter-badge remove-icon" data-type="vendor"><i class="bi bi-person me-1"></i>${filters.vendor_name} &times;</span>`;
    if (filters.nature_id)   html += `<span class="filter-badge remove-icon" data-type="nature"><i class="bi bi-tag me-1"></i>${filters.nature_name} &times;</span>`;
    if (filters.universal_search) html += `<span class="filter-badge remove-icon" data-type="search"><i class="bi bi-search me-1"></i>${filters.universal_search} &times;</span>`;
    if (html) html += `<span class="filter-badge filter-clear" id="clear-all">Clear all</span>`;
    $('#filter-summary').html(html);
  }

  function getType() {
    let url = window.location.href;
    return decodeURIComponent(url.split('/details/')[1].split('?')[0]);
  }

  function loadData(page=1, perPage=$('#per_page').val()) {
    $.ajax({
      url: "{{ route('superadmin.reportdetails', ['type' => '__TYPE__']) }}".replace('__TYPE__', encodeURIComponent(getType())),
      type:'GET',
      data: { per_page:perPage, page, date_from:filters.date_from, date_to:filters.date_to, zone_id:filters.zone_id, branch_id:filters.branch_id, company_id:filters.company_id, vendor_id:filters.vendor_id, nature_id:filters.nature_id, state_name:filters.state_name, universal_search:filters.universal_search },
      success: function (data) { $('#report-body').html(data); renderSummary(); }
    });
  }

  function setupMultiSelect(selectorInput, selectorHidden) {
    $(document).on('click', selectorHidden, function () {
      const ids = $(this).val(), text = $(selectorInput).val();
      if (selectorHidden==='.zone_id')    { filters.zone_id=ids; filters.zone_name=text; }
      else if (selectorHidden==='.branch_id')  { filters.branch_id=ids; filters.branch_name=text; }
      else if (selectorHidden==='.company_id') { filters.company_id=ids; filters.company_name=text; }
      else if (selectorHidden==='.vendor_id')  { filters.vendor_id=ids; filters.vendor_name=text; }
      else if (selectorHidden==='.state_id')   { filters.state_id=ids; filters.state_name=text; }
      else if (selectorHidden==='.nature_id')  { filters.nature_id=ids; filters.nature_name=text; }
      loadData();
    });
  }
  $('.zone_id').on('click',    function () { setupMultiSelect('.zone-search-input', '.zone_id'); });
  $('.branch_id').on('click',  function () { setupMultiSelect('.branch-search-input', '.branch_id'); });
  $('.company_id').on('click', function () { setupMultiSelect('.company-search-input', '.company_id'); });
  $('.vendor_id').on('click',  function () { setupMultiSelect('.vendor-search-input', '.vendor_id'); });
  $('.state_id').on('click',   function () { setupMultiSelect('.state-search-input', '.state_id'); });
  $('.nature_id').on('click',  function () { setupMultiSelect('.nature-search-input', '.nature_id'); });

  $('.universal_search').on('keyup', function () { filters.universal_search=$(this).val(); loadData(); });
  $('.data_values').on('change', function () {
    const dr = $(this).val();
    if (dr && dr.includes(' to ')) { filters.date_from=dr.split(' to ')[0]; filters.date_to=dr.split(' to ')[1]; }
    else { filters.date_from=''; filters.date_to=''; }
    loadData();
  });

  $('#filter-summary').on('click', '.remove-icon', function () {
    const t = $(this).data('type');
    if      (t==='date')    { filters.date_from=''; filters.date_to=''; $('#data_values').text('All Dates'); $('.data_values').val(''); }
    else if (t==='company') { filters.company_id=''; filters.company_name=''; $('.company_id').val(''); $('.company-search-input').val(''); $('.company-list div').removeClass('selected'); }
    else if (t==='state')   { filters.state_id=''; filters.state_name=''; $('.state_id').val(''); $('.state-search-input').val(''); $('.state-list div').removeClass('selected'); }
    else if (t==='zone')    { filters.zone_id=''; filters.zone_name=''; $('.zone_id').val(''); $('.zone-search-input').val(''); $('.zone-list div').removeClass('selected'); }
    else if (t==='branch')  { filters.branch_id=''; filters.branch_name=''; $('.branch_id').val(''); $('.branch-search-input').val(''); $('.branch-list div').removeClass('selected'); }
    else if (t==='vendor')  { filters.vendor_id=''; filters.vendor_name=''; $('.vendor_id').val(''); $('.vendor-search-input').val(''); $('.vendor-list div').removeClass('selected'); }
    else if (t==='nature')  { filters.nature_id=''; filters.nature_name=''; $('.nature_id').val(''); $('.nature-search-input').val(''); $('.account-list div').removeClass('selected'); }
    else if (t==='search')  { filters.universal_search=''; $('.universal_search').val(''); }
    loadData();
  });
  $('#filter-summary').on('click', '#clear-all', function () {
    filters = { date_from:'', date_to:'', zone_id:'', zone_name:'', branch_id:'', branch_name:'', company_id:'', company_name:'', vendor_id:'', vendor_name:'', state_id:'', state_name:'', nature_id:'', nature_name:'', universal_search:'' };
    $('.zone-search-input,.branch-search-input,.company-search-input,.vendor-search-input,.state-search-input,.nature-search-input').val('');
    $('.zone_id,.branch_id,.company_id,.vendor_id,.state_id,.nature_id').val('');
    $('.data_values').val(''); $('#data_values').text('All Dates'); $('.universal_search').val('');
    $('.dropdown-list div').removeClass('selected');
    loadData();
  });

  // Pagination
  $(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    const params = new URLSearchParams($(this).attr('href').split('?')[1]);
    loadData(params.get('page')||1, $('#per_page').val());
  });
  $(document).on('change', '#per_page', function () { loadData(1, $(this).val()); });

  // Tab switching (delegated — works after AJAX reload)
  $(document).on('click', '.rv-tab', function () {
    const $tabs    = $(this).closest('.rv-tab-bar').find('.rv-tab');
    const $contents = $('#report-body').find('.rv-tab-content');
    $tabs.removeClass('active');
    $(this).addClass('active');
    $contents.removeClass('rv-active');
    $('#' + $(this).data('tab')).addClass('rv-active');
  });

  // Vendor monthly summary modal
  $(document).on('click', '.view-vendor-bills', function () {
    const vendorId   = $(this).data('vendor-id');
    const vendorName = $(this).data('vendor-name');
    const accountName = $(this).data('account-name');
    $('#vendorBillModalLabel').text(vendorName + ' — Monthly Summary');
    $('#bill-summary-body').html('<tr><td colspan="2" class="text-center text-muted py-3">Loading...</td></tr>');
    $('#vendorBillModal').modal('show');
    $.ajax({
      url:'{{ route("superadmin.vendorMonthlySummary") }}', type:'GET',
      data:{ vendor_id:vendorId, account:accountName },
      success: function (res) { $('#bill-summary-body').html(res.html); },
      error: function () { $('#bill-summary-body').html('<tr><td colspan="2" class="text-center text-danger py-3">Failed to load</td></tr>'); }
    });
  });
});
</script>

<script>
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

@include('superadmin.superadminfooter')
</body>
</html>
