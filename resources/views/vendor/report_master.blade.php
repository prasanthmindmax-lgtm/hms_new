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
  .rm-chart-card {
    background: #fff;
    border: 1px solid #eaedf3;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 20px;
  }
  .rm-chart-title {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .rm-expense-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    border-radius: 8px;
    margin-bottom: 6px;
    border-left: 4px solid #4e73df;
    background: #f8f9ff;
    cursor: pointer;
    transition: background .15s;
  }
  .rm-expense-item:hover { background: #eef0ff; }
  .rm-expense-left { display: flex; align-items: center; gap: 8px; }
  .rm-expense-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
  .rm-expense-label { font-size: 12px; font-weight: 500; color: #374151; }
  .rm-expense-amount { font-size: 12px; font-weight: 700; color: #374151; }
</style>

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
          <i class="bi bi-bar-chart-line"></i> Financial Dashboard
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

          <div class="qd-filter-group">
            <label>Search</label>
            <div class="qd-search-wrap" style="margin-top:0;">
              <i class="bi bi-search"></i>
              <input type="text" class="universal_search" placeholder="Search...">
            </div>
          </div>
        </div>
      </div>

      {{-- Applied filters bar --}}
      <div class="qd-applied-bar">
        <strong>Applied Filters:</strong>
        <div id="filter-summary"></div>
      </div>

      {{-- ── Stats Boxes ── --}}
      <div class="qd-stats" id="statsSection" style="border-bottom:1px solid #eaedf3;margin-bottom:0;">
        <div class="qd-stat-card qd-stat-blue" style="cursor:default;">
          <div class="qd-stat-icon"><i class="bi bi-cash-coin"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total Income</div>
            <div class="qd-stat-value" data-stat-key="total_income">₹0</div>
            <div class="qd-stat-sub">Billing collections</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-red" style="cursor:default;">
          <div class="qd-stat-icon"><i class="bi bi-credit-card"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total Expense</div>
            <div class="qd-stat-value" data-stat-key="total_expense">₹0</div>
            <div class="qd-stat-sub">Bills raised</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-green" style="cursor:default;">
          <div class="qd-stat-icon"><i class="bi bi-graph-up"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Net</div>
            <div class="qd-stat-value" data-stat-key="net_amount">₹0</div>
            <div class="qd-stat-sub">Income − Expense</div>
          </div>
        </div>
      </div>

      {{-- ── Charts Section ── --}}
      <div style="padding: 10px 10px;">

        {{-- Monthly Line Chart --}}
        <div class="rm-chart-card">
          <div class="rm-chart-title"><i class="bi bi-graph-up-arrow"></i> Monthly Income vs Expense</div>
          <canvas id="incomeChart" height="100"></canvas>
        </div>

        {{-- Two side-by-side charts --}}
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="rm-chart-card" style="margin-bottom:0;">
              <div class="rm-chart-title"><i class="bi bi-bar-chart"></i> Income by Payment Type</div>
              <div style="height:260px;"><canvas id="paymentTypeBarChart"></canvas></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="rm-chart-card" style="margin-bottom:0;">
              <div class="rm-chart-title"><i class="bi bi-bar-chart-steps"></i> Monthly Income vs Expense (Bar)</div>
              <div style="height:260px;"><canvas id="incomeExpenseChart"></canvas></div>
            </div>
          </div>
        </div>

        {{-- Top Expenses --}}
        <div class="rm-chart-card">
          <div class="rm-chart-title"><i class="bi bi-pie-chart"></i> Top Expenses</div>
          <div class="row align-items-start">
            <div class="col-md-5 d-flex justify-content-center">
              <div style="max-width:320px;width:100%;"><canvas id="expenseChart" height="260"></canvas></div>
            </div>
            <div class="col-md-7" style="max-height:360px;overflow-y:auto;">
              <div id="expenseListContainer"></div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

<script>
const reportDetailsRoute = "{{ url('/superadmin/reports/details') }}";
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

// Chart color palette
const chartColors = [
  '#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#fd7e14','#20c997',
  '#6610f2','#17a2b8','#ff6384','#6f42c1','#ffc107','#198754','#0dcaf0',
  '#8e44ad','#ff9f40','#00b894','#d63031','#0984e3'
];

let incomeExpenseLineChart, paymentTypeBarChart, incomeExpenseBarChart, expenseChart;

$(document).ready(function () {
  // Reset date on load
  $('#data_values').text('All Dates');
  $('.data_values').val('');

  // Toggle stats
  $('#toggleStats').on('click', function () {
    const $s = $('#statsSection'), $i = $('#statsChevron');
    $s.is(':visible') ? ($s.slideUp(200), $i.removeClass('bi-chevron-up').addClass('bi-chevron-down'))
                      : ($s.slideDown(200), $i.removeClass('bi-chevron-down').addClass('bi-chevron-up'));
  });

  // Toggle filters
  $('#toggleFilters').on('click', function () {
    const $s = $('#filtersSection'), $i = $('#filtersChevron');
    $s.is(':visible') ? ($s.slideUp(200), $i.removeClass('bi-chevron-up').addClass('bi-chevron-down'))
                      : ($s.slideDown(200), $i.removeClass('bi-chevron-down').addClass('bi-chevron-up'));
  });

  // Dropdown open
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
    $dropdown.css({ position:'absolute', top: offset.top+$input.outerHeight(), left: offset.left, width: $input.outerWidth(), zIndex: 9999 }).show();
    $dropdown.find('.inner-search').focus();
  });
  $(document).on('keyup', '.inner-search', function () {
    const v = $(this).val().toLowerCase();
    $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function () {
      $(this).toggle($(this).text().toLowerCase().includes(v));
    });
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
    $dropdown.find('.dropdown-list.multiselect div.selected').each(function () {
      items.push($(this).text().trim()); ids.push($(this).data('id'));
    });
    wrapper.find('.dropdown-search-input').val(items.join(', '));
    const $h = wrapper.find('input[type="hidden"]'); $h.val(ids.join(',')); $h.trigger('click');
  }

  // Zone → Branch
  $('.zone_id').on('click', function () {
    const id = $('.zone_id').val(); if (!id) return;
    const fd = new FormData(); fd.append('id', id);
    $.ajax({
      url: '{{ route("superadmin.getbranchfetch") }}', method:'POST', data:fd, processData:false, contentType:false,
      headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
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
  var allowDateFilter = false;

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
    if (filters.nature_id)
      html += `<span class="filter-badge remove-icon" data-type="nature"><i class="bi bi-tag me-1"></i>${filters.nature_name} &times;</span>`;
    if (filters.universal_search)
      html += `<span class="filter-badge remove-icon" data-type="search"><i class="bi bi-search me-1"></i>${filters.universal_search} &times;</span>`;
    if (html) html += `<span class="filter-badge filter-clear" id="clear-all">Clear all</span>`;
    $('#filter-summary').html(html);
  }

  function getCurrentFilters() {
    let dateVal = $('.data_values').val(); let date_from='', date_to='';
    if (allowDateFilter && dateVal && dateVal.includes(' to ')) {
      date_from = dateVal.split(' to ')[0]; date_to = dateVal.split(' to ')[1];
    }
    return { date_from, date_to, zone_id: filters.zone_id, branch_id: filters.branch_id, company_id: filters.company_id, vendor_id: filters.vendor_id, nature_id: filters.nature_id, state_id: filters.state_id, universal_search: filters.universal_search };
  }

  function setupMultiSelect(selectorInput, selectorHidden) {
    var ns = 'click.ms' + selectorHidden.replace(/[^a-zA-Z0-9]/g, '');
    $(document).off(ns, selectorHidden).on(ns, selectorHidden, function () {
      const ids = $(this).val(), text = $(selectorInput).val();
      if (selectorHidden==='.zone_id')         { filters.zone_id=ids; filters.zone_name=text; }
      else if (selectorHidden==='.branch_id')  { filters.branch_id=ids; filters.branch_name=text; }
      else if (selectorHidden==='.company_id') { filters.company_id=ids; filters.company_name=text; }
      else if (selectorHidden==='.vendor_id')  { filters.vendor_id=ids; filters.vendor_name=text; }
      else if (selectorHidden==='.state_id')   { filters.state_id=ids; filters.state_name=text; }
      else if (selectorHidden==='.nature_id')  { filters.nature_id=ids; filters.nature_name=text; }
      loadAllChartData(); renderSummary();
    });
  }
  setupMultiSelect('.zone-search-input',    '.zone_id');
  setupMultiSelect('.branch-search-input',  '.branch_id');
  setupMultiSelect('.company-search-input', '.company_id');
  setupMultiSelect('.vendor-search-input',  '.vendor_id');
  setupMultiSelect('.state-search-input',   '.state_id');
  setupMultiSelect('.nature-search-input',  '.nature_id');

  $('.universal_search').on('keyup', function () { filters.universal_search=$(this).val(); loadAllChartData(); renderSummary(); });
  $('.data_values').on('change', function () {
    allowDateFilter = true;
    const dr = $(this).val();
    if (dr && dr.includes(' to ')) { filters.date_from = dr.split(' to ')[0]; filters.date_to = dr.split(' to ')[1]; }
    else { filters.date_from=''; filters.date_to=''; }
    loadAllChartData(); renderSummary();
  });

  $('#filter-summary').on('click', '.remove-icon', function () {
    const t = $(this).data('type');
    if      (t==='date')    { filters.date_from=''; filters.date_to=''; allowDateFilter=false; $('#data_values').text('All Dates'); $('.data_values').val(''); }
    else if (t==='company') { filters.company_id=''; filters.company_name=''; $('.company_id').val(''); $('.company-search-input').val(''); $('.company-list div').removeClass('selected'); }
    else if (t==='state')   { filters.state_id=''; filters.state_name=''; $('.state_id').val(''); $('.state-search-input').val(''); $('.state-list div').removeClass('selected'); }
    else if (t==='zone')    { filters.zone_id=''; filters.zone_name=''; $('.zone_id').val(''); $('.zone-search-input').val(''); $('.zone-list div').removeClass('selected'); }
    else if (t==='branch')  { filters.branch_id=''; filters.branch_name=''; $('.branch_id').val(''); $('.branch-search-input').val(''); $('.branch-list div').removeClass('selected'); }
    else if (t==='vendor')  { filters.vendor_id=''; filters.vendor_name=''; $('.vendor_id').val(''); $('.vendor-search-input').val(''); $('.vendor-list div').removeClass('selected'); }
    else if (t==='nature')  { filters.nature_id=''; filters.nature_name=''; $('.nature_id').val(''); $('.nature-search-input').val(''); $('.account-list div').removeClass('selected'); }
    else if (t==='search')  { filters.universal_search=''; $('.universal_search').val(''); }
    loadAllChartData(); renderSummary();
  });
  $('#filter-summary').on('click', '#clear-all', function () {
    filters = { date_from:'', date_to:'', zone_id:'', zone_name:'', branch_id:'', branch_name:'', company_id:'', company_name:'', vendor_id:'', vendor_name:'', state_id:'', state_name:'', nature_id:'', nature_name:'', universal_search:'' };
    allowDateFilter = false;
    $('.zone-search-input,.branch-search-input,.company-search-input,.vendor-search-input,.state-search-input,.nature-search-input').val('');
    $('.zone_id,.branch_id,.company_id,.vendor_id,.state_id,.nature_id').val('');
    $('.data_values').val(''); $('#data_values').text('All Dates'); $('.universal_search').val('');
    $('.dropdown-list div').removeClass('selected');
    loadAllChartData(); renderSummary();
  });

  // ── Init Charts ──
  initCharts();
  loadAllChartData();

  function initCharts() {
    incomeExpenseLineChart = new Chart(document.getElementById('incomeChart').getContext('2d'), {
      type: 'line',
      data: { labels: [], datasets: [
        { label:'Income', data:[], borderColor:'rgb(75,192,192)', backgroundColor:'rgba(75,192,192,.1)', tension:.3, fill:true, borderWidth:3 },
        { label:'Expense', data:[], borderColor:'rgb(255,99,132)', backgroundColor:'rgba(255,99,132,.1)', tension:.3, fill:true, borderWidth:3 }
      ]},
      options: { responsive:true, maintainAspectRatio:true,
        plugins: {
          legend:{display:true,position:'top'},
          tooltip:{callbacks:{label:c=>`${c.dataset.label}: ₹${fmtIN(c.parsed.y)}`}},
          datalabels: { display: false }
        },
        scales: { y:{ beginAtZero:true, ticks:{callback:v=>'₹'+fmtIN(v)} } },
        interaction: { intersect:false, mode:'index' }
      }
    });
    paymentTypeBarChart = new Chart(document.getElementById('paymentTypeBarChart').getContext('2d'), {
      type:'bar', data:{ labels:[], datasets:[{ label:'Income (₹)', data:[], backgroundColor:chartColors.slice(0,6).map(c=>c+'bb'), borderWidth:1 }] },
      options:{ responsive:true, maintainAspectRatio:false,
        plugins:{
          legend:{display:true,position:'top'},
          tooltip:{callbacks:{label:c=>`₹${fmtIN(c.parsed.y)}`}},
          datalabels: { display: false }
        },
        scales:{ y:{ beginAtZero:true, ticks:{callback:v=>'₹'+fmtIN(v)} } }
      }
    });
    incomeExpenseBarChart = new Chart(document.getElementById('incomeExpenseChart').getContext('2d'), {
      type:'bar', data:{ labels:[], datasets:[
        { label:'Income', data:[], backgroundColor:'rgba(75,192,192,.7)', borderColor:'rgba(75,192,192,1)', borderWidth:1 },
        { label:'Expense', data:[], backgroundColor:'rgba(255,99,132,.7)', borderColor:'rgba(255,99,132,1)', borderWidth:1 }
      ]},
      options:{ responsive:true, maintainAspectRatio:false,
        plugins:{
          legend:{display:true,position:'top'},
          tooltip:{callbacks:{label:c=>`${c.dataset.label}: ₹${fmtIN(c.parsed.y)}`}},
          datalabels: { display: false }
        },
        scales:{ y:{ beginAtZero:true, ticks:{callback:v=>'₹'+fmtIN(v)} } }
      }
    });
    expenseChart = new Chart(document.getElementById('expenseChart').getContext('2d'), {
      type:'doughnut', data:{ labels:[], datasets:[{ label:'Expenses', data:[], backgroundColor:chartColors, borderColor:'#fff', borderWidth:2, hoverOffset:10 }] },
      options:{ cutout:'65%', responsive:true, maintainAspectRatio:false,
        layout: { padding: 40 },
        plugins:{
          legend:{display:false},
          tooltip:{ backgroundColor:'rgba(0,0,0,.8)', callbacks:{label:c=>`${c.label}: ₹${fmtIN(c.raw)} (${((c.raw/c.dataset.data.reduce((a,b)=>a+b,0))*100).toFixed(1)}%)`} },
          datalabels:{
            display: true,
            anchor: 'end',
            align: 'end',
            offset: 8,
            formatter: (value, ctx) => {
              const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
              if (!total || value === 0) return '';
              const pct = (value / total) * 100;
              // All slices in chart are >= 3%; show their percentage
              return pct >= 1 ? pct.toFixed(1) + '%' : '';
            },
            color: '#374151',
            font: { weight: 'bold', size: 11 },
          }
        }
      },
      plugins: [ChartDataLabels]
    });
  }

  function loadAllChartData() {
    $.ajax({
      url:'{{ route("superadmin.getAllCharts") }}', type:'GET', data: getCurrentFilters(),
      success: function (res) {
        updateLineChart(res.income_vs_expense);
        updatePaymentTypeChart(res.payment_type_income);
        updateBarChart(res.income_vs_expense);
        updateExpenseChart(res.top_expenses);
        updateExpenseList(res.top_expenses);
        if (res.stats) {
          $.each(res.stats, function(key, val) {
            var $el = $('[data-stat-key="' + key + '"]');
            if (key === 'total_bills') {
              $el.text(val);
            } else {
              var prefix = parseFloat(val) < 0 ? '-₹' : '₹';
              $el.text(prefix + fmtIN(Math.abs(val)));
            }
          });
        }
      },
      error: function () { toastr.error("Failed to load chart data"); }
    });
  }

  function updateLineChart(data) {
    if (!data?.months) return;
    const months = data.months.map(m=>monthName(m));
    incomeExpenseLineChart.data.labels = months;
    incomeExpenseLineChart.data.datasets[0].data = data.months.map(m=>data.income[m]||0);
    incomeExpenseLineChart.data.datasets[1].data = data.months.map(m=>data.expense[m]||0);
    incomeExpenseLineChart.update();
  }
  function updatePaymentTypeChart(data) {
    if (!data?.payment_types) return;
    paymentTypeBarChart.data.labels = data.payment_types;
    paymentTypeBarChart.data.datasets[0].data = data.income;
    paymentTypeBarChart.update();
  }
  function updateBarChart(data) {
    if (!data?.months) return;
    const months = data.months.map(m=>monthName(m));
    incomeExpenseBarChart.data.labels = months;
    incomeExpenseBarChart.data.datasets[0].data = data.months.map(m=>data.income[m]||0);
    incomeExpenseBarChart.data.datasets[1].data = data.months.map(m=>data.expense[m]||0);
    incomeExpenseBarChart.update();
  }
  function updateExpenseChart(data) {
    if (!data?.labels) return;
    const total = (data.values || []).reduce((s, v) => s + (parseFloat(v) || 0), 0);
    if (!total) {
      expenseChart.data.labels = [];
      expenseChart.data.datasets[0].data = [];
      expenseChart.data.datasets[0].backgroundColor = [];
      expenseChart.update(); return;
    }
    const chartLabels = [], chartValues = [], chartBg = [];
    let othersTotal = 0;
    data.labels.forEach((label, i) => {
      const val = parseFloat(data.values[i]) || 0;
      if ((val / total) * 100 >= 3) {
        chartLabels.push(label);
        chartValues.push(val);
        chartBg.push(chartColors[i % chartColors.length]);
      } else {
        othersTotal += val;
      }
    });
    if (othersTotal > 0) {
      chartLabels.push('Others');
      chartValues.push(othersTotal);
      chartBg.push('#9ca3af');
    }
    expenseChart.data.labels = chartLabels;
    expenseChart.data.datasets[0].data = chartValues;
    expenseChart.data.datasets[0].backgroundColor = chartBg;
    expenseChart.update();
  }
  function updateExpenseList(data) {
    if (!data) return;
    let expenses = [];
    if (data.labels && data.values) {
      expenses = data.labels.map((label,i) => ({ account:label, total_amount:parseFloat(data.values[i])||0, color:chartColors[i%chartColors.length] }));
    }
    expenses.sort((a,b)=>b.total_amount-a.total_amount);
    const grandTotal = expenses.reduce((s,e)=>s+e.total_amount,0);
    let html = '';
    expenses.forEach((e) => {
      const pct = grandTotal > 0 ? ((e.total_amount / grandTotal) * 100).toFixed(1) : '0.0';
      const isGrouped = parseFloat(pct) < 3;
      html += `<div class="rm-expense-item" data-type="${e.account}" style="border-left-color:${e.color};">
        <div class="rm-expense-left">
          <div class="rm-expense-dot" style="background:${e.color};"></div>
          <span class="rm-expense-label">${e.account}</span>
          ${isGrouped ? '<span style="font-size:10px;color:#9ca3af;margin-left:4px;background:#f3f4f6;padding:1px 5px;border-radius:4px;">in Others</span>' : ''}
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
          <span style="font-size:11px;color:#6b7280;">${pct}%</span>
          <span class="rm-expense-amount">₹${fmtIN(e.total_amount)}</span>
        </div>
      </div>`;
    });
    $('#expenseListContainer').html(html || '<div class="text-center text-muted py-3">No data</div>');
  }

  $(document).on('click', '.rm-expense-item', function () {
    const type = $(this).data('type');
    const f = getCurrentFilters();
    const params = new URLSearchParams({ date_from:f.date_from, date_to:f.date_to, zone_id:f.zone_id, branch_id:f.branch_id, company_id:f.company_id, vendor_id:f.vendor_id, nature_id:f.nature_id, universal_search:f.universal_search });
    window.location.href = `${reportDetailsRoute}/${encodeURIComponent(type)}?${params.toString()}`;
  });

  function fmtIN(amount) {
    if (!amount) return '0';
    return parseFloat(amount).toLocaleString('en-IN', { maximumFractionDigits:2, minimumFractionDigits:0 });
  }
  function monthName(num) {
    return ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][num-1];
  }
});
</script>

<script>
function cb(start, end, label) {
  if (label === 'All Dates') {
    $('#data_values').text('All Dates');
    $('.data_values').val('');
  } else {
    const fmt = start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY');
    $('#data_values').text(fmt);
    $('.data_values').val(start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY'));
  }
  $('.data_values').trigger('change');
}
</script>

@include('superadmin.superadminfooter')
</body>
</html>
