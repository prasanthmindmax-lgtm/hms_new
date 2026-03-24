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
  .rzc-card {
    background: #fff;
    border: 1px solid #eaedf3;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 20px;
  }
  .rzc-card-title {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .rzc-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 14px;
    margin-bottom: 14px;
  }
  .rzc-legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: #374151;
    font-weight: 500;
  }
  .rzc-legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 3px;
    flex-shrink: 0;
  }
  .rzc-date-badge {
    font-size: 11px;
    color: #6b7280;
    background: #f3f4f6;
    border-radius: 6px;
    padding: 3px 10px;
    margin-left: auto;
  }
  .rzc-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 180px;
    color: #9ca3af;
    font-size: 13px;
    gap: 8px;
  }
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
          <i class="bi bi-bar-chart-steps"></i> Income by Payment Type — Zone &amp; Branch
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
        </div>
      </div>

      {{-- Applied filters bar --}}
      <div class="qd-applied-bar">
        <strong>Applied Filters:</strong>
        <div id="filter-summary"></div>
      </div>

      {{-- ── Stats Section (dynamic) ── --}}
      <div id="statsSection" style="border-bottom:1px solid #eaedf3;"></div>

      {{-- ── Charts Section ── --}}
      <div style="padding:10px;">

        {{-- Zone Chart --}}
        <div class="rzc-card">
          <div class="rzc-card-title">
            <i class="bi bi-geo-alt"></i> Income by Payment Type — Zone Wise
            <span class="rzc-date-badge" id="zoneDateBadge"></span>
          </div>
          <div class="rzc-legend" id="zoneLegend"></div>
          <div id="zoneChartWrap" style="max-height:420px;overflow-y:auto;overflow-x:hidden;border:1px solid #f0f0f0;border-radius:8px;">
            <div class="rzc-loading" id="zoneLoading"><i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite;"></i> Loading chart…</div>
          </div>
        </div>

        {{-- Branch Chart --}}
        <div class="rzc-card">
          <div class="rzc-card-title">
            <i class="bi bi-diagram-3"></i> Income by Payment Type — Branch Wise
            <span class="rzc-date-badge" id="branchDateBadge"></span>
          </div>
          <div class="rzc-legend" id="branchLegend"></div>
          <div id="branchChartWrap" style="max-height:520px;overflow-y:auto;overflow-x:hidden;border:1px solid #f0f0f0;border-radius:8px;">
            <div class="rzc-loading" id="branchLoading"><i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite;"></i> Loading chart…</div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

<script>
const TblZonesModel = @json($TblZonesModel);
const locations     = @json($locations);

// Populate zone list
(Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data || [])).forEach(z => {
  $('.zone-list').append(`<div data-id="${z.id}" data-value="${z.name}">${z.name}</div>`);
});
// Populate branch list
(Array.isArray(locations) ? locations : (locations.data || [])).forEach(b => {
  $('.branch-list').append(`<div data-id="${b.id}" data-value="${b.name}">${b.name}</div>`);
});

let zoneChart   = null;
let branchChart = null;

$(document).ready(function () {
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

  // ── Dropdown interactions (same pattern as other dashboards) ──
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
      $(this).toggle($(this).text().toLowerCase().includes(v));
    });
  });
  $(document).on('click', '.dropdown-list.multiselect div', function (e) {
    e.stopPropagation(); $(this).toggleClass('selected'); updateMultiSelection($(this).closest('.tax-dropdown'));
  });
  $(document).on('click', '.select-all', function (e) {
    e.stopPropagation();
    const $d = $(this).closest('.tax-dropdown');
    $d.find('.dropdown-list.multiselect div').addClass('selected'); updateMultiSelection($d);
  });
  $(document).on('click', '.deselect-all', function (e) {
    e.stopPropagation();
    const $d = $(this).closest('.tax-dropdown');
    $d.find('.dropdown-list.multiselect div').removeClass('selected'); updateMultiSelection($d);
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

  // ── Filter state ──
  let filters = { date_from:'', date_to:'', zone_id:'', zone_name:'', branch_id:'', branch_name:'', state_id:'', state_name:'' };
  var allowDateFilter = false;

  function renderSummary() {
    let html = '';
    if (filters.date_from && filters.date_to)
      html += `<span class="filter-badge remove-icon" data-type="date"><i class="bi bi-calendar3 me-1"></i>${filters.date_from} → ${filters.date_to} &times;</span>`;
    if (filters.state_id)
      html += `<span class="filter-badge remove-icon" data-type="state"><i class="bi bi-map me-1"></i>${filters.state_name} &times;</span>`;
    if (filters.zone_id)
      html += `<span class="filter-badge remove-icon" data-type="zone"><i class="bi bi-geo-alt me-1"></i>${filters.zone_name} &times;</span>`;
    if (filters.branch_id)
      html += `<span class="filter-badge remove-icon" data-type="branch"><i class="bi bi-diagram-3 me-1"></i>${filters.branch_name} &times;</span>`;
    if (html) html += `<span class="filter-badge filter-clear" id="clear-all">Clear all</span>`;
    $('#filter-summary').html(html);
  }

  // ── setupMultiSelect (namespaced — fires only once per selector) ──
  function setupMultiSelect(selectorInput, selectorHidden) {
    var ns = 'click.ms' + selectorHidden.replace(/[^a-zA-Z0-9]/g, '');
    $(document).off(ns, selectorHidden).on(ns, selectorHidden, function () {
      const ids = $(this).val(), text = $(selectorInput).val();
      if      (selectorHidden === '.zone_id')   { filters.zone_id=ids;   filters.zone_name=text; }
      else if (selectorHidden === '.branch_id') { filters.branch_id=ids; filters.branch_name=text; }
      else if (selectorHidden === '.state_id')  { filters.state_id=ids;  filters.state_name=text; }
      loadCharts(); renderSummary();
    });
  }
  setupMultiSelect('.zone-search-input',   '.zone_id');
  setupMultiSelect('.branch-search-input', '.branch_id');
  setupMultiSelect('.state-search-input',  '.state_id');

  // Date filter
  $('.data_values').on('change', function () {
    allowDateFilter = true;
    const dr = $(this).val();
    if (dr && dr.includes(' to ')) {
      filters.date_from = dr.split(' to ')[0];
      filters.date_to   = dr.split(' to ')[1];
    } else { filters.date_from = ''; filters.date_to = ''; }
    loadCharts(); renderSummary();
  });

  // Remove filter badges
  $('#filter-summary').on('click', '.remove-icon', function () {
    const t = $(this).data('type');
    if      (t==='date')   { filters.date_from=''; filters.date_to=''; allowDateFilter=false; $('#data_values').text('All Dates'); $('.data_values').val(''); }
    else if (t==='state')  { filters.state_id='';  filters.state_name='';  $('.state_id').val('');  $('.state-search-input').val('');  $('.state-list div').removeClass('selected'); }
    else if (t==='zone')   { filters.zone_id='';   filters.zone_name='';   $('.zone_id').val('');   $('.zone-search-input').val('');   $('.zone-list div').removeClass('selected'); }
    else if (t==='branch') { filters.branch_id=''; filters.branch_name=''; $('.branch_id').val(''); $('.branch-search-input').val(''); $('.branch-list div').removeClass('selected'); }
    loadCharts(); renderSummary();
  });
  $('#filter-summary').on('click', '#clear-all', function () {
    filters = { date_from:'', date_to:'', zone_id:'', zone_name:'', branch_id:'', branch_name:'', state_id:'', state_name:'' };
    allowDateFilter = false;
    $('.zone-search-input,.branch-search-input,.state-search-input').val('');
    $('.zone_id,.branch_id,.state_id').val('');
    $('.data_values').val(''); $('#data_values').text('All Dates');
    $('.dropdown-list div').removeClass('selected');
    loadCharts(); renderSummary();
  });

  // ── State: active payment type filter & cached response ──
  let activePayType = null;
  let lastRes       = null;

  // ── Load chart data ──────────────────────────────────────
  function loadCharts() {
    activePayType = null;               // reset filter on any reload
    $('#zoneLoading').show().html('<i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite;"></i> Loading…');
    $('#branchLoading').show().html('<i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite;"></i> Loading…');

    $.ajax({
      url:  '{{ route("superadmin.zonePaymentChartData") }}',
      type: 'GET',
      data: {
        date_from: filters.date_from,
        date_to:   filters.date_to,
        zone_id:   filters.zone_id,
        branch_id: filters.branch_id,
        state_id:  filters.state_id,
      },
      success: function (res) {
        lastRes = res;
        renderAll(res, null);
      },
      error: function () { toastr.error('Failed to load chart data'); }
    });
  }

  // ── Render stats + legend + charts (with optional PT filter) ──
  function renderAll(res, payType) {
    buildStats(res.payment_totals, res.grand_total, res.payment_types, res.type_colors, payType);
    buildLegend(res.payment_types, res.type_colors, '#zoneLegend',   payType);
    buildLegend(res.payment_types, res.type_colors, '#branchLegend', payType);
    $('#zoneDateBadge,#branchDateBadge').text(res.date_range);
    buildStackedBar('zoneChart',   '#zoneChartWrap',   filterChartData(res.zone_chart,   payType));
    buildStackedBar('branchChart', '#branchChartWrap', filterChartData(res.branch_chart, payType));
  }

  // ── Client-side filter: keep only selected payment type dataset ──
  function filterChartData(chartData, payType) {
    if (!payType || !chartData) return chartData;
    return Object.assign({}, chartData, {
      datasets: chartData.datasets.filter(ds => ds.label === payType)
    });
  }

  // ── Stat card click ──────────────────────────────────────
  $(document).on('click', '.rzc-stat-card', function () {
    if (!lastRes) return;
    const clicked = $(this).data('pt') || null;
    activePayType = (activePayType === clicked && clicked !== null) ? null : clicked;
    renderAll(lastRes, activePayType);
  });

  // ── Payment Type Stat Cards ──────────────────────────────
  const ptIcons = {
    'Cash':'bi-cash-coin', 'Card':'bi-credit-card-2-front', 'Cheque':'bi-file-earmark-text',
    'NEFT':'bi-bank','UPI':'bi-phone','DD':'bi-file-earmark-ruled',
    'RTGS':'bi-arrow-left-right','IMPS':'bi-lightning-charge','Online':'bi-globe',
  };

  function buildStats(paymentTotals, grandTotal, payTypes, typeColors, activePayType) {
    if (!paymentTotals) return;
    typeColors = typeColors || {};

    // Grand Total card — active when no PT filter
    const gtActive  = !activePayType;
    const gtShadow  = gtActive ? 'box-shadow:0 0 0 2px #1a56db;' : '';
    const gtOpacity = (!activePayType || gtActive) ? '' : 'opacity:0.45;';
    let html = `<div class="qd-stats" style="display:flex;flex-wrap:wrap;">
    <div class="rzc-stat-card qd-stat-card qd-stat-blue" data-pt="" style="cursor:pointer;${gtShadow}${gtOpacity}">
      <div class="qd-stat-icon"><i class="bi bi-currency-rupee"></i></div>
      <div class="qd-stat-body">
        <div class="qd-stat-label">Grand Total</div>
        <div class="qd-stat-value">₹${fmtIN(grandTotal)}</div>
        <div class="qd-stat-sub">All payment types</div>
      </div>
    </div>`;

    payTypes.forEach((pt) => {
      const amount   = paymentTotals[pt] || 0;
      const hex      = typeColors[pt] || '#858796';
      const icon     = ptIcons[pt] || 'bi-wallet2';
      const isActive = activePayType === pt;
      const shadow   = isActive   ? `box-shadow:0 0 0 2px ${hex};` : '';
      const opacity  = (!activePayType || isActive) ? '' : 'opacity:0.45;';
      html += `<div class="rzc-stat-card qd-stat-card" data-pt="${pt}"
          style="cursor:pointer;border-left:4px solid ${hex};${shadow}${opacity}">
        <div class="qd-stat-icon" style="color:${hex};"><i class="bi ${icon}"></i></div>
        <div class="qd-stat-body">
          <div class="qd-stat-label">${pt}</div>
          <div class="qd-stat-value" style="color:${hex};">₹${fmtIN(amount)}</div>
          <div class="qd-stat-sub">${grandTotal > 0 ? ((amount/grandTotal)*100).toFixed(1)+'% of total' : '—'}</div>
        </div>
      </div>`;
    });

    html += '</div>';
    $('#statsSection').html(html);
  }

  // ── Build stacked horizontal bar chart ─────────────────
  function buildStackedBar(canvasId, wrapSelector, chartData) {
    const $wrap   = $(wrapSelector);
    const loadingId = canvasId === 'zoneChart' ? '#zoneLoading' : '#branchLoading';

    if (!chartData || !chartData.labels || chartData.labels.length === 0) {
      $(loadingId).show().html('No data available for the selected filters.');
      return;
    }

    // Destroy previous chart if any
    const existing = Chart.getChart(canvasId);
    if (existing) existing.destroy();

    // Remove old canvas and create fresh — ensures exact height with no inheritance
    $wrap.find('canvas').remove();
    const BAR_ROW_H = 46; // px per row — fixed regardless of count
    const canvasH   = chartData.labels.length * BAR_ROW_H + 60;
    const $canvas   = $('<canvas>').attr('id', canvasId)
                        .css({ display: 'block', width: '100%', height: canvasH + 'px' });
    $(loadingId).hide().after($canvas);

    // Fixed bar thickness so single-row bars never stretch
    const datasets = chartData.datasets.map(ds =>
      Object.assign({}, ds, { barThickness: 28, maxBarThickness: 30 })
    );

    new Chart(document.getElementById(canvasId).getContext('2d'), {
      type: 'bar',
      data: {
        labels: chartData.labels.map(l => l + '   ₹' + fmtIN(chartData.totals[l] || 0)),
        datasets: datasets,
      },
      options: {
        indexAxis: 'y',
        responsive: false,          // false = Chart.js respects our CSS size exactly
        maintainAspectRatio: false,
        animation: { duration: 400 },
        layout: { padding: { top: 4, bottom: 4, left: 0, right: 12 } },
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: { label: c => `${c.dataset.label}: ₹${fmtIN(c.raw)}` }
          }
        },
        scales: {
          x: {
            stacked: true,
            beginAtZero: true,
            ticks: { callback: v => '₹' + fmtIN(v), font: { size: 10 }, maxRotation: 0 },
            grid: { color: '#f0f0f0' }
          },
          y: {
            stacked: true,
            ticks: { font: { size: 11, weight: '500' } }
          }
        }
      },
      plugins: [{
        afterDatasetsDraw(chart) {
          const ctx = chart.ctx;
          chart.data.datasets.forEach((dataset, i) => {
            chart.getDatasetMeta(i).data.forEach((bar, j) => {
              const value = dataset.data[j];
              if (!value || value < 1) return;
              const { x, y, width } = bar.getProps(['x','y','width'], true);
              if (width < 32) return;
              const label  = '₹' + fmtIN(value);
              ctx.save();
              ctx.font = 'bold 10px sans-serif';
              ctx.fillStyle = '#fff';
              ctx.textAlign = 'center';
              ctx.textBaseline = 'middle';
              if (width > ctx.measureText(label).width + 6)
                ctx.fillText(label, x - width / 2, y);
              ctx.restore();
            });
          });
        }
      }]
    });
  }

  // ── Legend — uses exact same colours as the chart bars ──
  function buildLegend(payTypes, typeColors, selector, activePayType) {
    typeColors = typeColors || {};
    let html = '';
    payTypes.forEach((pt) => {
      const c       = typeColors[pt] || '#858796';
      const opacity = (!activePayType || activePayType === pt) ? '1' : '0.35';
      html += `<div class="rzc-legend-item" style="opacity:${opacity};">
        <div class="rzc-legend-dot" style="background:${c};"></div>
        <span>${pt}</span>
      </div>`;
    });
    $(selector).html(html);
  }

  // ── Formatting helpers ───────────────────────────────────
  function fmtIN(v) {
    if (!v) return '0';
    return parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits:0, maximumFractionDigits:2 });
  }
  function fmtINShort(v) {
    v = parseFloat(v) || 0;
    if (v >= 10000000) return (v/10000000).toFixed(1) + 'Cr';
    if (v >= 100000)   return (v/100000).toFixed(1) + 'L';
    if (v >= 1000)     return (v/1000).toFixed(1) + 'K';
    return v.toFixed(0);
  }

  // ── Initial load ─────────────────────────────────────────
  loadCharts();
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
