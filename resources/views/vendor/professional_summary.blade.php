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
  .preview-card {
    background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 10px;
    padding: 12px; margin: 10px; width: 150px; text-align: center;
    font-size: 12px; overflow-wrap: break-word; cursor: pointer;
  }
  .preview-card img { max-width: 100%; max-height: 100px; object-fit: cover; margin-bottom: 8px; }
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
          <i class="bi bi-person-badge"></i> Professional Summary
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
        <div class="qd-stat-card qd-stat-blue qd-stat-active" data-stat-filter="" title="Show all records">
          <div class="qd-stat-icon"><i class="bi bi-receipt"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total Invoice</div>
            <div class="qd-stat-value" data-stat-key="totalInvoice">{{ $invoiceSummaryCalculation['totalInvoice'] ?? '₹0' }}</div>
            <div class="qd-stat-sub">Sub total of all bills</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-purple" data-stat-filter="" title="Total Final Invoice">
          <div class="qd-stat-icon"><i class="bi bi-file-earmark-check"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total Final Invoice</div>
            <div class="qd-stat-value" data-stat-key="totalFullinvoice">{{ $invoiceSummaryCalculation['totalFullinvoice'] ?? '₹0' }}</div>
            <div class="qd-stat-sub">Grand total of all bills</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-red" data-stat-filter="pending" title="Filter: Pending (Due to Pay)">
          <div class="qd-stat-icon"><i class="bi bi-clock-history"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Pending Invoice</div>
            <div class="qd-stat-value" data-stat-key="pendingInvoice">{{ $invoiceSummaryCalculation['pendingInvoice'] ?? '₹0' }}</div>
            <div class="qd-stat-sub">Due to Pay</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-green" data-stat-filter="paid" title="Filter: Paid invoices">
          <div class="qd-stat-icon"><i class="bi bi-check-circle"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Paid Invoice</div>
            <div class="qd-stat-value" data-stat-key="paidInvoice">{{ $invoiceSummaryCalculation['paidInvoice'] ?? '₹0' }}</div>
            <div class="qd-stat-sub">Fully paid</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-orange" data-stat-filter="" title="Total Tax + GST">
          <div class="qd-stat-icon"><i class="bi bi-calculator"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Taxable Amount</div>
            <div class="qd-stat-value" data-stat-key="totalTaxAndGst">{{ $invoiceSummaryCalculation['totalTaxAndGst'] ?? '₹0' }}</div>
            <div class="qd-stat-sub">TDS + GST</div>
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

      {{-- ── Table ── --}}
      <div class="qd-table-wrap" id="tds-body">
        @include('vendor.partials.table.professional_summary_rows', [
          'billlist'                  => $billlist,
          'invoiceSummaryCalculation' => $invoiceSummaryCalculation,
          'perPage'                   => $perPage
        ])
              </div>

    </div>
  </div>
            </div>

{{-- ── Bill Detail Modal (zoho-modal, original) ── --}}
            <div class="zoho-modal" id="billDetailModal">
              <div class="zoho-modal-content">
                <div class="zoho-modal-header">
      <div class="zoho-modal-title">Bill Details</div>
                  <div class="zoho-modal-actions">
        <button class="zoho-btn zoho-btn-primary print-btn"><i class="bi bi-printer"></i> Print</button>
        <button class="zoho-btn zoho-btn-primary pdf-btn"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
        <button class="zoho-btn zoho-btn-icon close-modal"><i class="bi bi-x-lg"></i></button>
                  </div>
                </div>
                <div class="zoho-modal-body">
                  <div class="bill-header">
        <div><span class="bill-paid-status"></span></div>
        <div><a href="#" class="bill-pdf-link">Show PDF View</a></div>
                    </div>
                  <div class="bill-divider"></div>
                    <div class="bill_container">
                      <div class="zoho-section">
                        <div class="zoho-section-title">BILL</div>
                        <div class="bill-info-section">
            <div class="bill-info-row"><div class="bill-info-label">ORDER NUMBER</div><div class="bill-info-value" id="order-number">—</div></div>
            <div class="bill-info-row"><div class="bill-info-label">BILL DATE</div><div class="bill-info-value" id="bill-date">—</div></div>
            <div class="bill-info-row"><div class="bill-info-label">DUE DATE</div><div class="bill-info-value" id="due-date">—</div></div>
            <div class="bill-info-row"><div class="bill-info-label">PAYMENT TERMS</div><div class="bill-info-value" id="payment-terms">—</div></div>
            <div class="bill-info-row total-row"><div class="bill-info-label">TOTAL</div><div class="bill-info-value total-amount">—</div></div>
                          </div>
                          </div>
                    <div class="zoho-section">
                      <div class="zoho-section-title">VENDOR ADDRESS</div>
                      <div class="zoho-address-block">
            <div class="vendor-name"></div>
            <div class="vendor-street"></div>
            <div class="vendor-city-state"></div>
            <div class="vendor-country-zip"></div>
            <div class="vendor-phone"></div>
                      </div>
                    </div>
                  </div>
                  <div class="zoho-section">
        <div class="zoho-section-title">ITEMS &amp; DESCRIPTION</div>
                    <table class="zoho-items-table">
                      <thead>
                        <tr>
              <th>ITEMS &amp; DESCRIPTION</th>
                          <th>CUSTOMER DETAILS</th>
                          <th>QUANTITY</th>
                          <th>RATE</th>
                          <th>GST</th>
                          <th>GST AMOUNT</th>
                          <th>AMOUNT</th>
                        </tr>
                      </thead>
          <tbody id="bill-items"></tbody>
                    </table>
                  </div>
                  <div class="zoho-section">
                    <div class="zoho-totals-grid">
          <div class="zoho-total-row"><span>Sub Total</span><span id="sub-total">—</span></div>
          <div class="zoho-total-row"><span>Discount (-)</span><span id="discount">—</span></div>
          <div class="zoho-total-row gst-breakdown"></div>
          <div class="zoho-total-row"><span>TDS (-)</span><span id="tds_amount">—</span></div>
          <div class="zoho-total-row zoho-total-amount"><span>Total</span><span id="grand-total">—</span></div>
                      </div>
                      </div>
                  <div class="zoho-section">
                    <div class="zoho-section-title">Notes</div>
        <div class="zoho-notes-content" id="notes">—</div>
                  </div>
      <div class="upload_doc d-flex flex-wrap"></div>
                  </div>
                  </div>
                </div>
            <div class="zoho-modal-overlay" id="modalOverlay"></div>

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
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

<script>
    $(document).ready(function () {

  // ── Reset date on load ──
  $('#data_values').text('All Dates');
  $('.data_values').val('');

  // ── Toggle Stats / Filters ──
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
    loadData();
  });

  // ── Populate dropdowns ──
    const TblZonesModel = @json($TblZonesModel);
  const Tblcompany    = @json($Tblcompany);
  const Tblvendor     = @json($Tblvendor);
  const accounts      = @json($accounts);

  (Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data || [])).forEach(z => {
    $('.zone-list').append(`<div data-id="${z.id}">${z.name}</div>`);
  });
  (Tblcompany.data || []).forEach(c => {
    $('.company-list').append(`<div data-value="${c.company_name}" data-id="${c.id}">${c.company_name}</div>`);
  });
  Tblvendor.forEach(v => {
    $('.vendor-list').append(`<div data-value="${v.display_name}" data-id="${v.id}">${v.display_name}</div>`);
  });
  accounts.forEach(a => {
    $('.account-list').append(`<div data-value="${a.name}" data-id="${a.id}">${a.name}</div>`);
  });

  // ── Dropdown open/search/multiselect ──
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
    $dropdown.css({ position:'absolute', top: offset.top + $input.outerHeight(), left: offset.left, width: $input.outerWidth(), zIndex: 9999 }).show();
            $dropdown.find('.inner-search').focus();
          });
          $(document).on('keyup', '.inner-search', function () {
    const v = $(this).val().toLowerCase();
            $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1);
            });
          });
          $(document).on('click', '.dropdown-list.multiselect div', function (e) {
    e.stopPropagation(); $(this).toggleClass('selected');
    updateMultiSelection($(this).closest('.tax-dropdown'));
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
  $(document).on('click', '.dropdown-menu.tax-dropdown', function (e) { e.stopPropagation(); });

          function updateMultiSelection($dropdown) {
            const wrapper = $dropdown.data('wrapper');
            if (!wrapper) return;
    const items = [], ids = [];
            $dropdown.find('.dropdown-list.multiselect div.selected').each(function () {
      items.push($(this).text().trim()); ids.push($(this).data('id'));
    });
    wrapper.find('.dropdown-search-input').val(items.join(', '));
    const $h = wrapper.find('input[type="hidden"]');
    $h.val(ids.join(',')); $h.trigger('click');
  }

  // Zone → Branch fetch
          $('.zone_id').on('click', function () {
    const id = $('.zone_id').val(); if (!id) return;
    const fd = new FormData(); fd.append('id', id);
            $.ajax({
      url: '{{ route("superadmin.getbranchfetch") }}', method: 'POST', data: fd, processData: false, contentType: false,
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
    state_id:'', state_name:'', nature_id:'', nature_name:'', universal_search:''
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
    if (filters.nature_id)
      html += `<span class="filter-badge remove-icon" data-type="nature"><i class="bi bi-tag me-1"></i>${filters.nature_name} &times;</span>`;
    if (filters.universal_search)
      html += `<span class="filter-badge remove-icon" data-type="search"><i class="bi bi-search me-1"></i>${filters.universal_search} &times;</span>`;
    if (html) html += `<span class="filter-badge filter-clear" id="clear-all">Clear all</span>`;
    $('#filter-summary').html(html);
  }

  function loadData(page = 1, perPage = $('#per_page').val()) {
    appliedFilters = {
      per_page: perPage, page,
      date_from: filters.date_from, date_to: filters.date_to,
      zone_id: filters.zone_id, branch_id: filters.branch_id,
      company_id: filters.company_id, vendor_id: filters.vendor_id,
      nature_id: filters.nature_id, state_name: filters.state_name,
      universal_search: filters.universal_search,
      stat_filter: statFilter
    };
    $.ajax({
      url: '{{ route("superadmin.getprofessionalsummary") }}', type: 'GET', data: appliedFilters,
      success: function (data) {
        if (data && typeof data === 'object' && data.html !== undefined) {
          $('#tds-body').html(data.html);
          if (data.stats) {
            $.each(data.stats, function(key, val) {
              // Professional stats are already formatted strings (₹ formatted)
              $('[data-stat-key="' + key + '"]').text(val);
            });
          }
        } else {
          $('#tds-body').html(data);
        }
        renderSummary();
      }
    });
  }

  function setupMultiSelect(selectorInput, selectorHidden) {
    var ns = 'click.ms' + selectorHidden.replace(/[^a-zA-Z0-9]/g, '');
    $(document).off(ns, selectorHidden).on(ns, selectorHidden, function () {
      const ids  = $(this).val();
      const text = $(selectorInput).val();
      if      (selectorHidden === '.zone_id')    { filters.zone_id    = ids; filters.zone_name    = text; }
      else if (selectorHidden === '.branch_id')  { filters.branch_id  = ids; filters.branch_name  = text; }
      else if (selectorHidden === '.company_id') { filters.company_id = ids; filters.company_name = text; }
      else if (selectorHidden === '.vendor_id')  { filters.vendor_id  = ids; filters.vendor_name  = text; }
      else if (selectorHidden === '.state_id')   { filters.state_id   = ids; filters.state_name   = text; }
      else if (selectorHidden === '.nature_id')  { filters.nature_id  = ids; filters.nature_name  = text; }
      loadData();
    });
  }
  setupMultiSelect('.zone-search-input',    '.zone_id');
  setupMultiSelect('.branch-search-input',  '.branch_id');
  setupMultiSelect('.company-search-input', '.company_id');
  setupMultiSelect('.vendor-search-input',  '.vendor_id');
  setupMultiSelect('.state-search-input',   '.state_id');
  setupMultiSelect('.nature-search-input',  '.nature_id');

  $('.universal_search').on('keyup', function () { filters.universal_search = $(this).val(); loadData(); });

  $('.data_values').on('change', function () {
    const dr = $(this).val();
    if (dr && dr.includes(' to ')) {
      const p = dr.split(' to ');
      filters.date_from = p[0].trim(); filters.date_to = p[1].trim();
    } else { filters.date_from = ''; filters.date_to = ''; }
    loadData();
  });

  // Remove filter badge
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
    statFilter = '';
    $('.zone-search-input,.branch-search-input,.company-search-input,.vendor-search-input,.state-search-input,.nature-search-input').val('');
    $('.zone_id,.branch_id,.company_id,.vendor_id,.state_id,.nature_id').val('');
    $('.data_values').val(''); $('#data_values').text('All Dates');
    $('.universal_search').val(''); $('.dropdown-list div').removeClass('selected');
    $('.qd-stat-card').removeClass('qd-stat-active');
    $('.qd-stat-card[data-stat-filter=""]').first().addClass('qd-stat-active');
    loadData();
  });

  // Pagination
  $(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    const params = new URLSearchParams($(this).attr('href').split('?')[1]);
    loadData(params.get('page') || 1, $('#per_page').val());
  });
  $(document).on('change', '#per_page', function () { loadData(1, $(this).val()); });

  // Select all checkbox
  $(document).on('change', '#selectAll', function () { $('.bill-checkbox').prop('checked', $(this).prop('checked')); });

  // Export
  $('#downloadExcelBtn').on('click', function (e) {
    e.preventDefault();
    let ids = [];
    $('.bill-checkbox:checked').each(function () { ids.push($(this).closest('tr').data('id')); });
    const params = new URLSearchParams(appliedFilters);
    params.set('bill_ids', ids.join(','));
    window.location.href = "{{ route('professional.summary.download') }}?" + params.toString();
  });

  // ── Row click → zoho-modal ──
  $(document).on('click', '.customer-row', function (e) {
    if ($(e.target).is('input[type="checkbox"]')) return;
    if ($(e.target).closest('.documentclk').length) return;

    const bill   = $(this).data('allbill');
    const addr   = $(this).data('vendor-address');
    const items  = $(this).data('items');
    const vendor = $(this).data('vendor');

    $('.zoho-modal-title').text(bill.bill_number || 'Bill Details');
    $('#order-number').text(bill.order_number || '—');
    $('#bill-date').text(bill.bill_date || '—');
    $('#due-date').text(bill.due_date || '—');
    $('#payment-terms').text(bill.payment_terms || '—');
    $('.total-amount').text(formatCurrency(bill.grand_total_amount));
    $('#sub-total').text(formatCurrency(bill.sub_total_amount));
    $('#discount').text(formatCurrency(bill.discount_amount));
    $('#tds_amount').text(formatCurrency(bill.tax_amount));
    $('#grand-total').text(formatCurrency(bill.grand_total_amount));
    $('#notes').text(bill.note || 'No notes');

    if (addr) {
      $('.vendor-name').text(bill.vendor_name || '—');
      $('.vendor-street').text(addr.address || '—');
      $('.vendor-city-state').text(`${addr.city || ''}, ${addr.state || ''}`);
      $('.vendor-country-zip').text(`${addr.country || ''} - ${addr.zip_code || ''}`);
      $('.vendor-phone').text(addr.phone || '—');
    }

    // Items
    const itemsHtml = (items || []).map(item => `
      <tr>
        <td>${item.item_details || '—'}</td>
        <td>${bill.vendor_name || '—'}</td>
        <td>${item.quantity || 1}</td>
        <td>${item.rate || '0.00'}</td>
        <td>${item.gst_name || '—'}</td>
        <td>${formatCurrency(item.gst_amount)}</td>
        <td>${formatCurrency(item.amount)}</td>
      </tr>`).join('');
    $('#bill-items').html(itemsHtml || '<tr><td colspan="7" class="text-center text-muted">No items</td></tr>');

    // GST breakdown
    let gstMap = {}, gstHtml = '';
    (items || []).forEach(item => {
      if (item.gst_rate > 0) {
        if (item.cgst_amount > 0) {
          const r = item.gst_rate / 2, amt = item.gst_amount / 2;
          gstMap[`CGST ${r}%`] = (gstMap[`CGST ${r}%`] || 0) + amt;
          gstMap[`SGST ${r}%`] = (gstMap[`SGST ${r}%`] || 0) + amt;
        } else {
          gstMap[`IGST ${item.gst_rate}%`] = (gstMap[`IGST ${item.gst_rate}%`] || 0) + parseFloat(item.gst_amount || 0);
        }
      }
    });
    Object.entries(gstMap).forEach(([label, amt]) => {
      gstHtml += `<div class="tax_show"><label class="gst_label">${label}</label><div class="gst-amount">${formatCurrency(amt)}</div></div>`;
    });
    $('.gst-breakdown').html(gstHtml);

    // Documents
    try {
      const docs = typeof bill.documents === 'string' ? JSON.parse(bill.documents) : (bill.documents || []);
      $('.upload_doc').empty();
      (docs || []).forEach(filename => {
        const ext = filename.split('.').pop().toLowerCase();
        const icon = ['jpg','jpeg','png','gif','webp'].includes(ext) ? `../public/uploads/vendor/bill/${filename}`
                   : ext === 'pdf' ? 'https://cdn-icons-png.flaticon.com/512/337/337946.png'
                                   : 'https://cdn-icons-png.flaticon.com/512/564/564619.png';
        const fa = JSON.stringify([`../public/uploads/vendor/bill/${filename}`]);
        $('.upload_doc').append(`<div class="preview-card documentclk" data-filetype="documents" data-files='${fa}'><img src="${icon}" style="height:60px;"><div>${filename}</div></div>`);
      });
    } catch(e) {}

    $('#billDetailModal').data('bill-id', bill.id).addClass('show loading');
    $('#modalOverlay').addClass('show');
    $('body').css('overflow', 'hidden');
    setTimeout(function () { $('#billDetailModal').removeClass('loading'); }, 300);
  });

  $(document).on('click', '.close-modal, #modalOverlay', function (e) {
    e.stopPropagation();
    $('#billDetailModal').removeClass('show');
    $('#modalOverlay').removeClass('show');
    $('body').css('overflow', 'auto');
  });
  $(document).on('keyup', function (e) {
    if (e.key === 'Escape') { $('#billDetailModal').removeClass('show'); $('#modalOverlay').removeClass('show'); $('body').css('overflow','auto'); }
  });

  // PDF
  $(document).on('click', '.pdf-btn', function () {
    const id = $('#billDetailModal').data('bill-id');
    $.ajax({ url:'{{ route("superadmin.getpurchasepdf") }}', method:'GET', data:{id}, xhrFields:{responseType:'blob'},
      success: function(data) {
        const link = document.createElement('a');
        link.href = URL.createObjectURL(new Blob([data],{type:'application/pdf'}));
        link.download = `professional_${id}.pdf`;
        document.body.appendChild(link); link.click(); document.body.removeChild(link);
      }
    });
  });
  // Print
  $(document).on('click', '.print-btn', function () {
    const id = $('#billDetailModal').data('bill-id');
    $.ajax({ url:'{{ route("superadmin.getpurchaseprint") }}', method:'GET', data:{id}, xhrFields:{responseType:'blob'},
      success: function(r) { window.open(URL.createObjectURL(new Blob([r],{type:'application/pdf'})),'_blank'); }
    });
  });

  // Document modal
  $(document).on('click', '.documentclk', function (e) {
    e.stopPropagation();
    $('#documentModal1').modal('show');
    let fileArray = [];
    try { fileArray = JSON.parse($(this).attr('data-files')); if (typeof fileArray==='string') fileArray=JSON.parse(fileArray); } catch(er){return;}
    if (!Array.isArray(fileArray)||!fileArray.length) return;
    $('#pdfmain').attr('src', fileArray[0]);
    let views = '';
    fileArray.forEach(file => {
      views += `<button style="font-size:11px;" type="button" class="btn btn-primary pdf-file-btn mb-1" data-filepath="${file}">${file.split('/').pop()}</button>`;
    });
    $('#image_pdfs').html(views);
  });
  $(document).on('click', '.pdf-file-btn', function () {
    $('.pdf-file-btn').removeClass('active'); $(this).addClass('active');
    $('#pdfmain').attr('src', $(this).data('filepath'));
  });

  function formatCurrency(amount) {
    if (!amount && amount !== 0) return '₹0.00';
    const n = typeof amount === 'string' ? parseFloat(amount.replace(/,/g,'')) : amount;
    return '₹' + n.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
  }

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
