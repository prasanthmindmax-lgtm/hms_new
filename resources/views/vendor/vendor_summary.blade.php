<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
  /* Account accordion card */
  .vs-account-card {
    border: 1px solid #eaedf3;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 12px;
    background: #fff;
    box-shadow: 0 1px 4px rgba(79,110,247,.06);
  }
  .vs-account-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    background: linear-gradient(135deg, #4f6ef7 0%, #7c3aed 100%);
    cursor: pointer;
    user-select: none;
    transition: opacity .15s;
  }
  .vs-account-header:hover { opacity: .92; }
  .vs-account-left { display: flex; align-items: center; gap: 10px; }
  .vs-account-index {
    width: 24px; height: 24px; border-radius: 50%;
    background: rgba(255,255,255,.25); color: #fff;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
  }
  .vs-account-name { color: #fff; font-size: 13px; font-weight: 700; letter-spacing: .4px; }
  .vs-account-pills { display: flex; gap: 8px; align-items: center; }
  .vs-pill {
    background: rgba(255,255,255,.18);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    white-space: nowrap;
  }
  .vs-pill-paid  { background: rgba(16,185,129,.3); }
  .vs-pill-due   { background: rgba(239,68,68,.3); }
  .vs-chevron { color: rgba(255,255,255,.8); transition: transform .25s; }
  .vs-chevron.open { transform: rotate(180deg); }

  .vs-account-body { display: none; }
  .vs-vendor-table { width: 100%; border-collapse: collapse; }
  .vs-vendor-table thead tr { background: #f8f9fc; }
  .vs-vendor-table th {
    padding: 10px 16px; font-size: 11px; font-weight: 700;
    color: #6b7280; text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid #eaedf3;
  }
  .vs-vendor-table td {
    padding: 10px 16px; font-size: 13px; color: #374151;
    border-bottom: 1px solid #f3f4f6;
  }
  .vs-vendor-table tbody tr:hover { background: #f8f9ff; }
  .vs-vendor-table tbody tr:last-child td { border-bottom: none; }
  .vs-vendor-link { color: #4f6ef7; font-weight: 600; text-decoration: none; }
  .vs-vendor-link:hover { text-decoration: underline; }
  .vs-amount { font-weight: 600; text-align: right; }
  .vs-amount-paid { color: #059669; }
  .vs-amount-due  { color: #dc2626; }
  .vs-footer-row td {
    background: #f8f9fc; font-weight: 700; font-size: 12px;
    color: #374151; border-top: 2px solid #eaedf3 !important;
    border-bottom: none !important;
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
          <i class="bi bi-people"></i> Vendor Summary
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

      {{-- ── Stats ── --}}
      <div class="qd-stats" id="statsSection">
        @php
          $grandBills = collect($summaryData)->sum('total_bills');
          $grandPaid  = collect($summaryData)->sum('total_paid');
          $grandDue   = collect($summaryData)->sum('total_due');
        @endphp
        <div class="qd-stat-card qd-stat-blue qd-stat-active" data-stat-filter="" title="Show all bills">
          <div class="qd-stat-icon"><i class="bi bi-receipt"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total Bills</div>
            <div class="qd-stat-value">₹{{ number_format($grandBills, 2) }}</div>
            <div class="qd-stat-sub">Grand total across all accounts</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-green" data-stat-filter="paid" title="Filter: Paid bills only">
          <div class="qd-stat-icon"><i class="bi bi-check-circle"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total Paid</div>
            <div class="qd-stat-value">₹{{ number_format($grandPaid, 2) }}</div>
            <div class="qd-stat-sub">Amount paid so far</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-red" data-stat-filter="due" title="Filter: Due/pending bills only">
          <div class="qd-stat-icon"><i class="bi bi-clock-history"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total Due</div>
            <div class="qd-stat-value">₹{{ number_format($grandDue, 2) }}</div>
            <div class="qd-stat-sub">Outstanding balance</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-purple" data-stat-filter="" title="Show all accounts">
          <div class="qd-stat-icon"><i class="bi bi-layers"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Accounts</div>
            <div class="qd-stat-value">{{ collect($summaryData)->count() }}</div>
            <div class="qd-stat-sub">Nature of payment types</div>
          </div>
        </div>
      </div>

      {{-- ── Filters ── --}}
      <div class="qd-filters" id="filtersSection">
        <div class="qd-filter-row">
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

      {{-- ── Applied filters bar ── --}}
      <div class="qd-applied-bar">
        <strong>Applied Filters:</strong>
        <div id="filter-summary"></div>
      </div>

      {{-- ── Summary Content ── --}}
      <div id="vendor_summary" style="padding: 8px 4px;">
        @include('vendor.vendor_summary_dynamic', ['summaryData' => $summaryData])
      </div>

    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function () {

  // ── Stats / Filter toggles ──
  $('#toggleStats').on('click', function () {
    const $s = $('#statsSection'), $i = $('#statsChevron');
    $s.is(':visible') ? ($s.slideUp(200), $i.removeClass('bi-chevron-up').addClass('bi-chevron-down'))
                      : ($s.slideDown(200), $i.removeClass('bi-chevron-down').addClass('bi-chevron-up'));
  });
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
    loadSummary();
  });

  // ── Accordion toggle (delegated for AJAX reloads) ──
  $(document).on('click', '.vs-account-header', function () {
    const $body    = $(this).next('.vs-account-body');
    const $chevron = $(this).find('.vs-chevron');
    $('.vs-account-body').not($body).slideUp(200);
    $('.vs-chevron').not($chevron).removeClass('open');
    $body.stop(true, true).slideToggle(250);
    $chevron.toggleClass('open');
  });

  // ── Populate dropdowns ──
  const Tblvendor  = @json($Tblvendor);
  const Tblaccount = @json($Tblaccount);

  Tblvendor.forEach(v => {
    $('.vendor-list').append(`<div data-value="${v.display_name}" data-id="${v.id}">${v.display_name}</div>`);
  });
  Tblaccount.forEach(a => {
    $('.account-list').append(`<div data-value="${a.name}" data-id="${a.id}">${a.name}</div>`);
  });

  // ── Dropdown open / search / multiselect ──
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

  // ── Filter state ──
  let filters = { vendor_id:'', vendor_name:'', nature_id:'', nature_name:'' };

  function renderSummaryBar() {
    let html = '';
    if (filters.vendor_id)  html += `<span class="filter-badge remove-icon" data-type="vendor"><i class="bi bi-person me-1"></i>${filters.vendor_name} &times;</span>`;
    if (filters.nature_id)  html += `<span class="filter-badge remove-icon" data-type="nature"><i class="bi bi-tag me-1"></i>${filters.nature_name} &times;</span>`;
    if (html) html += `<span class="filter-badge filter-clear" id="clear-all">Clear all</span>`;
    $('#filter-summary').html(html);
  }

  function loadSummary() {
    $.ajax({
      url:'{{ route("superadmin.vendorSummary") }}', type:'GET',
      data:{ vendor_id:filters.vendor_id, nature_id:filters.nature_id, stat_filter:statFilter },
      success: function (data) { $('#vendor_summary').html(data); renderSummaryBar(); }
    });
  }

  function setupMultiSelect(selectorInput, selectorHidden) {
    $(document).on('click', selectorHidden, function () {
      const ids = $(this).val(), text = $(selectorInput).val();
      if (selectorHidden === '.vendor_id')  { filters.vendor_id = ids; filters.vendor_name = text; }
      else if (selectorHidden === '.nature_id') { filters.nature_id = ids; filters.nature_name = text; }
      loadSummary();
    });
  }
  $('.vendor_id').on('click',  function () { setupMultiSelect('.vendor-search-input', '.vendor_id'); });
  $('.nature_id').on('click',  function () { setupMultiSelect('.nature-search-input', '.nature_id'); });

  $('#filter-summary').on('click', '.remove-icon', function () {
    const t = $(this).data('type');
    if (t === 'vendor') { filters.vendor_id=''; filters.vendor_name=''; $('.vendor_id').val(''); $('.vendor-search-input').val(''); $('.vendor-list div').removeClass('selected'); }
    else if (t === 'nature') { filters.nature_id=''; filters.nature_name=''; $('.nature_id').val(''); $('.nature-search-input').val(''); $('.account-list div').removeClass('selected'); }
    loadSummary();
  });
  $('#filter-summary').on('click', '#clear-all', function () {
    filters = { vendor_id:'', vendor_name:'', nature_id:'', nature_name:'' };
    statFilter = '';
    $('.vendor-search-input,.nature-search-input').val('');
    $('.vendor_id,.nature_id').val('');
    $('.dropdown-list div').removeClass('selected');
    $('.qd-stat-card').removeClass('qd-stat-active');
    $('.qd-stat-card[data-stat-filter=""]').first().addClass('qd-stat-active');
    loadSummary();
  });

});
</script>

@include('superadmin.superadminfooter')
</body>
</html>
