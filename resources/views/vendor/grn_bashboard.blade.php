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
.preview-card {
  background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 10px;
  padding: 12px; margin: 10px; width: 150px; text-align: center;
  font-size: 12px; overflow-wrap: break-word; cursor: pointer;
}
.preview-card img { max-width:100%; max-height:100px; object-fit:cover; margin-bottom:8px; }
#documentModal1 { z-index: 999999; }

/* GRN detail side panel */
.grn-panel-backdrop {
  position:fixed; inset:0; background:rgba(15,23,42,.45);
  z-index:1040; opacity:0; pointer-events:none; transition:opacity .25s;
}
.grn-panel-backdrop.show { opacity:1; pointer-events:auto; }
.grn-panel {
  position:fixed; top:0; right:0; width:65%; max-width:950px;
  height:100vh; background:#f8f9fc;
  box-shadow:-6px 0 32px rgba(0,0,0,.14);
  z-index:1050; display:flex; flex-direction:column;
  transform:translateX(100%);
  transition:transform .28s cubic-bezier(.4,0,.2,1);
  overflow:hidden;
}
.grn-panel.show { transform:translateX(0); }
.grn-panel-header {
  display:flex; align-items:center; justify-content:space-between;
  padding:14px 20px; background:#fff;
  border-bottom:1px solid #e5e9f0; flex-shrink:0; gap:10px;
}
.grn-panel-title { font-size:15px; font-weight:700; color:#1e293b; }
.grn-panel-badge { font-size:11px; padding:3px 10px; border-radius:20px; }
.grn-panel-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.grn-close-btn {
  background:none; border:none; cursor:pointer; font-size:18px;
  color:#64748b; padding:4px 8px; border-radius:6px;
  transition:background .15s,color .15s;
}
.grn-close-btn:hover { background:#f1f5f9; color:#1e293b; }
.grn-panel-body { flex:1; overflow-y:auto; padding:20px; }
.grn-info-card {
  background:#fff; border-radius:10px; border:1px solid #e5e9f0;
  padding:16px; margin-bottom:14px;
}
.grn-info-card-title {
  font-size:11px; font-weight:700; text-transform:uppercase;
  letter-spacing:.6px; color:#475569; margin-bottom:12px;
  display:flex; align-items:center; gap:6px;
}
.grn-info-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px 20px; }
.grn-info-item .grn-label { font-size:11px; color:#94a3b8; margin-bottom:2px; }
.grn-info-item .grn-val { font-size:13px; font-weight:600; color:#1e293b; }
.grn-items-table { width:100%; border-collapse:collapse; font-size:12.5px; }
.grn-items-table th {
  background:#f8fafc; color:#64748b; font-size:11px; font-weight:700;
  text-transform:uppercase; letter-spacing:.4px;
  padding:8px 10px; border-bottom:1px solid #e5e9f0; text-align:left;
}
.grn-items-table td { padding:9px 10px; border-bottom:1px solid #f1f5f9; color:#1e293b; }
.grn-items-table tbody tr:last-child td { border-bottom:none; }
.grn-items-table tbody tr:hover td { background:#f8fafc; }
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
          <i class="bi bi-box-seam"></i>
          GRN Dashboard
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
          <a href="{{ route('superadmin.getgrnconvert') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>New GRN
          </a>
        </div>
      </div>

      {{-- ── Stats ── --}}
      <div class="qd-stats" id="statsSection">
        <div class="qd-stat-card qd-stat-blue qd-stat-active" data-stat-filter="" title="Show all">
          <div class="qd-stat-icon"><i class="bi bi-box-seam"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total GRN</div>
            <div class="qd-stat-value" data-stat-key="total">{{ $stats['total'] }}</div>
            <div class="qd-stat-sub">&nbsp;</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-green" data-stat-filter="approved" title="Filter: Approved">
          <div class="qd-stat-icon"><i class="bi bi-check-circle"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Approved</div>
            <div class="qd-stat-value" data-stat-key="approved">{{ $stats['approved'] }}</div>
            <div class="qd-stat-sub">&nbsp;</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-orange" data-stat-filter="pending" title="Filter: Pending">
          <div class="qd-stat-icon"><i class="bi bi-clock"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Pending</div>
            <div class="qd-stat-value" data-stat-key="pending">{{ $stats['pending'] }}</div>
            <div class="qd-stat-sub">&nbsp;</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-red" data-stat-filter="rejected" title="Filter: Rejected">
          <div class="qd-stat-icon"><i class="bi bi-x-circle"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Rejected</div>
            <div class="qd-stat-value" data-stat-key="rejected">{{ $stats['rejected'] }}</div>
            <div class="qd-stat-sub">&nbsp;</div>
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
              <i class="fa fa-calendar"></i>
              <span id="data_values">All Dates</span>
              <i class="fa fa-caret-down" style="margin-left:auto;"></i>
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

          <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
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
          <input type="text" class="universal_search" placeholder="Search GRN no, vendor, zone, branch...">
        </div>
      </div>

      {{-- ── Applied filters ── --}}
      <div class="qd-applied-bar">
        <span class="applied-label">Filters:</span>
        <div id="filter-summary" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
      </div>

      {{-- ── Table ── --}}
      <div class="qd-table-wrap">
        <div id="grn-body">
          @include('vendor.partials.table.grn_rows', ['grnlist' => $grnlist, 'perPage' => $perPage])
        </div>
      </div>

    </div>{{-- /qd-card --}}

  </div>
</div>

{{-- ── GRN Detail Panel ── --}}
<div class="grn-panel-backdrop" id="grnPanelBackdrop"></div>
<div class="grn-panel" id="grnDetailPanel">

  <div class="grn-panel-header">
    <div class="d-flex align-items-center gap-3">
      <div class="grn-panel-title" id="panel-grn-number">GRN Details</div>
      <span class="grn-panel-badge bg-warning text-dark" id="panel-approval-badge">Pending</span>
    </div>
    <div class="grn-panel-actions">
      <button class="btn btn-sm btn-outline-secondary edit-btn">
        <i class="bi bi-pencil me-1"></i>Edit
      </button>
      <button class="btn btn-sm btn-outline-primary print-btn">
        <i class="bi bi-printer me-1"></i>Print
      </button>
      <button class="btn btn-sm btn-outline-danger pdf-btn-panel">
        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
      </button>
      <button class="grn-close-btn" id="closeGrnPanel">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
  </div>

  <div class="grn-panel-body">

    {{-- Basic Info --}}
    <div class="grn-info-card">
      <div class="grn-info-card-title"><i class="bi bi-info-circle"></i>GRN Information</div>
      <div class="grn-info-grid">
        <div class="grn-info-item">
          <div class="grn-label">GRN Number</div>
          <div class="grn-val" id="panel-grn-no">—</div>
        </div>
        <div class="grn-info-item">
          <div class="grn-label">Order Number</div>
          <div class="grn-val" id="panel-order-no">—</div>
        </div>
        <div class="grn-info-item">
          <div class="grn-label">Invoice Date</div>
          <div class="grn-val" id="panel-bill-date">—</div>
        </div>
        <div class="grn-info-item">
          <div class="grn-label">Due Date</div>
          <div class="grn-val" id="panel-due-date">—</div>
        </div>
        <div class="grn-info-item">
          <div class="grn-label">Payment Terms</div>
          <div class="grn-val" id="panel-payment-terms">—</div>
        </div>
        <div class="grn-info-item">
          <div class="grn-label">Zone / Branch</div>
          <div class="grn-val" id="panel-zone-branch">—</div>
        </div>
      </div>
    </div>

    {{-- Vendor Info --}}
    <div class="grn-info-card">
      <div class="grn-info-card-title"><i class="bi bi-person-circle"></i>Vendor Details</div>
      <div class="grn-info-grid">
        <div class="grn-info-item">
          <div class="grn-label">Vendor Name</div>
          <div class="grn-val vendor-name-panel">—</div>
        </div>
        <div class="grn-info-item">
          <div class="grn-label">Phone</div>
          <div class="grn-val vendor-phone-panel">—</div>
        </div>
        <div class="grn-info-item" style="grid-column:1/-1;">
          <div class="grn-label">Address</div>
          <div class="grn-val vendor-address-panel" style="font-weight:400; color:#475569;">—</div>
        </div>
      </div>
    </div>

    {{-- Items Table --}}
    <div class="grn-info-card">
      <div class="grn-info-card-title"><i class="bi bi-list-ul"></i>Items & Description</div>
      <div style="overflow-x:auto;">
        <table class="grn-items-table">
          <thead>
            <tr>
              <th>Item Description</th>
              <th>Qty</th>
              <th>Receivable</th>
              <th>Acceptable</th>
              <th>Rejected</th>
              <th>Balance</th>
            </tr>
          </thead>
          <tbody id="grn-panel-items">
            <tr><td colspan="6" class="text-center text-muted py-3">No items</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    {{-- Notes --}}
    <div class="grn-info-card">
      <div class="grn-info-card-title"><i class="bi bi-sticky"></i>Notes</div>
      <div id="panel-notes" class="text-muted" style="font-size:13px;">—</div>
    </div>

    {{-- Documents --}}
    <div class="grn-info-card">
      <div class="grn-info-card-title"><i class="bi bi-paperclip"></i>GRN Documents</div>
      <div class="document-preview-bill d-flex flex-wrap gap-2 mt-1"></div>
    </div>

  </div>
</div>

{{-- Document viewer modal --}}
<div class="modal fade" id="documentModal1" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:999999;">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#080fd399; height:0;">
        <h5 class="modal-title" style="color:#fff; font-size:12px;">Document Viewer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background:#fff;"></button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

<script>
$(document).ready(function () {
  // Reset date display to "All Dates" on page load
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
    loadGrn();
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
    const items = [], ids = [];
    $dropdown.find('.dropdown-list.multiselect div.selected').each(function () {
      items.push($(this).text().trim());
      ids.push($(this).data('id'));
    });
    const $visibleInput = wrapper.find('.dropdown-search-input');
    const $hiddenInput  = wrapper.find('input[type="hidden"]');
    $visibleInput.val(items.join(', '));
    $hiddenInput.val(ids.join(','));
    $hiddenInput.trigger('click');
  }

  // ── Zone → Branch fetch ──
  $('.zone_id').on('click', function () {
    var id = $('.zone_id').val();
    if (!id) return;
    let formData = new FormData();
    formData.append('id', id);
    $.ajax({
      url: '{{ route("superadmin.getbranchfetch") }}',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
      success: function (res) {
        if (res.branch !== '') {
          $('.branch-list div').remove();
          res.branch.forEach(b => $('.branch-list').append(`<div data-id="${b.id}">${b.name}</div>`));
        }
      }
    });
  });
  $('.zone-list div').on('click', function () {
    $('.branch-search-input').val('');
    $('.branch_id').val('');
  });

  // ── AJAX filter state & loader ──
  let filters = {
    date_from:'', date_to:'', zone_id:'', zone_name:'',
    branch_id:'', branch_name:'', company_id:'', company_name:'',
    vendor_id:'', vendor_name:'', state_id:'', state_name:'',
    universal_search:''
  };

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
    if (statFilter)
      html += `<span class="filter-badge remove-icon" data-type="stat"><i class="bi bi-funnel me-1"></i>${statFilter} &times;</span>`;
    if (html) html += `<span class="filter-badge filter-clear" id="clear-all">Clear all</span>`;
    $('#filter-summary').html(html);
  }

  function loadGrn(page = 1, perPage = $('#per_page').val()) {
    $.ajax({
      url: '{{ route("superadmin.getgrndashboard") }}',
      type: 'GET',
      data: {
        per_page: perPage, page,
        date_from: filters.date_from, date_to: filters.date_to,
        zone_id: filters.zone_id, branch_id: filters.branch_id,
        company_id: filters.company_id, vendor_id: filters.vendor_id,
        state_name: filters.state_name,
        universal_search: filters.universal_search,
        stat_filter: statFilter
      },
      success: function (data) {
        if (typeof data === 'object' && data.html !== undefined) {
          $('#grn-body').html(data.html);
          if (data.stats) {
            $.each(data.stats, function(key, val) {
              $('[data-stat-key="' + key + '"]').text(val);
            });
          }
        } else {
          $('#grn-body').html(data);
        }
        renderSummary();
      }
    });
  }

  // ── Multiselect → filter wire-up (same pattern as quotation dashboard) ──
  function setupMultiSelect(selectorInput, selectorHidden) {
    var ns = 'click.ms' + selectorHidden.replace(/[^a-zA-Z0-9]/g, '');
    $(document).off(ns, selectorHidden).on(ns, selectorHidden, function () {
      const selectedIds  = $(this).val();
      const selectedText = $(selectorInput).val();
      if (selectorHidden === '.zone_id')         { filters.zone_id = selectedIds; filters.zone_name = selectedText; }
      else if (selectorHidden === '.branch_id')  { filters.branch_id = selectedIds; filters.branch_name = selectedText; }
      else if (selectorHidden === '.company_id') { filters.company_id = selectedIds; filters.company_name = selectedText; }
      else if (selectorHidden === '.vendor_id')  { filters.vendor_id = selectedIds; filters.vendor_name = selectedText; }
      else if (selectorHidden === '.state_id')   { filters.state_id = selectedIds; filters.state_name = selectedText; }
      loadGrn();
    });
  }
  setupMultiSelect('.zone-search-input', '.zone_id');
  setupMultiSelect('.branch-search-input', '.branch_id');
  setupMultiSelect('.company-search-input', '.company_id');
  setupMultiSelect('.vendor-search-input', '.vendor_id');
  setupMultiSelect('.state-search-input', '.state_id');

  $('.universal_search').on('keyup', function () {
    filters.universal_search = $(this).val();
    loadGrn();
  });

  // Date change — triggered by quotation_search.js cb()
  $('.data_values').on('change', function () {
    let dateRange = $(this).val();
    if (dateRange && dateRange.includes(' to ')) {
      let parts = dateRange.split(' to ');
      filters.date_from = parts[0].trim();
      filters.date_to   = parts[1].trim();
    } else {
      filters.date_from = '';
      filters.date_to   = '';
    }
    loadGrn();
  });

  // Remove filter badge
  $('#filter-summary').on('click', '.remove-icon', function () {
    const type = $(this).data('type');
    if (type === 'date')    { filters.date_from=''; filters.date_to=''; $('#data_values').text('All Dates'); $('.data_values').val(''); }
    else if (type==='zone')    { filters.zone_id=''; filters.zone_name=''; $('.zone_id').val(''); $('.zone-search-input').val(''); $('.zone-list div').removeClass('selected'); }
    else if (type==='branch')  { filters.branch_id=''; filters.branch_name=''; $('.branch_id').val(''); $('.branch-search-input').val(''); $('.branch-list div').removeClass('selected'); }
    else if (type==='company') { filters.company_id=''; filters.company_name=''; $('.company_id').val(''); $('.company-search-input').val(''); $('.company-list div').removeClass('selected'); }
    else if (type==='vendor')  { filters.vendor_id=''; filters.vendor_name=''; $('.vendor_id').val(''); $('.vendor-search-input').val(''); $('.vendor-list div').removeClass('selected'); }
    else if (type==='state')   { filters.state_id=''; filters.state_name=''; $('.state_id').val(''); $('.state-search-input').val(''); $('.state-list div').removeClass('selected'); }
    else if (type==='search')  { filters.universal_search=''; $('.universal_search').val(''); }
    else if (type==='stat')    { statFilter=''; $('.qd-stat-card').removeClass('qd-stat-active'); $('.qd-stat-card[data-stat-filter=""]').addClass('qd-stat-active'); }
    loadGrn();
  });
  $('#filter-summary').on('click', '#clear-all', function () {
    filters = { date_from:'', date_to:'', zone_id:'', zone_name:'', branch_id:'', branch_name:'', company_id:'', company_name:'', vendor_id:'', vendor_name:'', state_id:'', state_name:'', universal_search:'' };
    statFilter = '';
    $('.zone-search-input,.branch-search-input,.company-search-input,.vendor-search-input,.state-search-input').val('');
    $('.zone_id,.branch_id,.company_id,.vendor_id,.state_id').val('');
    $('.data_values').val(''); $('#data_values').text('All Dates');
    $('.universal_search').val('');
    $('.dropdown-list div').removeClass('selected');
    $('.qd-stat-card').removeClass('qd-stat-active'); $('.qd-stat-card[data-stat-filter=""]').addClass('qd-stat-active');
    loadGrn();
  });

  // Pagination
  $(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    const params = new URLSearchParams($(this).attr('href').split('?')[1]);
    loadGrn(params.get('page') || 1, $('#per_page').val());
  });
  $(document).on('change', '#per_page', function () { loadGrn(1, $(this).val()); });

  // ── Row click → open side panel ──
  $(document).on('click', '.customer-row', function (e) {
    if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('.approver') || $(e.target).is('.bi')) return;

    const d = {
      id:            $(this).data('id'),
      grn_number:    $(this).data('grn_number'),
      order_number:  $(this).data('order-number'),
      vendor_name:   $(this).data('vendor-name'),
      vendor_address:$(this).data('vendor-address'),
      vendor:        $(this).data('vendor'),
      bill_date:     $(this).data('bill-date'),
      due_date:      $(this).data('due-date'),
      payment_terms: $(this).data('payment-terms'),
      note:          $(this).data('note'),
      grn_all:       $(this).data('grn_all'),
      items:         $(this).data('items'),
      approval_status: $(this).data('approval_status'),
      zone_name:     $(this).data('zone-name'),
      branch_name:   $(this).data('branch-name'),
    };
    populatePanel(d);
    openPanel();
  });

  function openPanel() {
    $('#grnDetailPanel').addClass('show');
    $('#grnPanelBackdrop').addClass('show');
    $('body').css('overflow','hidden');
  }
  function closePanel() {
    $('#grnDetailPanel').removeClass('show');
    $('#grnPanelBackdrop').removeClass('show');
    $('body').css('overflow','auto');
  }
  $('#closeGrnPanel, #grnPanelBackdrop').on('click', closePanel);
//   $('#grnDetailPanel').on('click', function (e) { e.stopPropagation(); });
  $('#grnDetailPanel').on('click', function (e) {
        if (!$(e.target).closest('button, a').length) {
            e.stopPropagation();
        }
    });
  $(document).on('keyup', function (e) { if (e.key === 'Escape') closePanel(); });

  function populatePanel(d) {
    $('#grnDetailPanel').data('grn-id', d.id);
    $('#panel-grn-number').text(d.grn_number || 'GRN Details');
    $('#panel-grn-no').text(d.grn_number || '—');
    $('#panel-order-no').text(d.order_number || '—');
    $('#panel-bill-date').text(d.bill_date || '—');
    $('#panel-due-date').text(d.due_date || '—');
    $('#panel-payment-terms').text(d.payment_terms || '—');
    $('#panel-zone-branch').text([(d.zone_name||''), (d.branch_name||'')].filter(Boolean).join(' / ') || '—');
    $('#panel-notes').text(d.note || 'No notes');

    // Approval badge
    const $badge = $('#panel-approval-badge');
    if (d.approval_status == 1) {
      $badge.removeClass('bg-warning bg-danger text-dark').addClass('bg-success text-white').text('Approved');
      $('.edit-btn').hide();
    } else {
      $badge.removeClass('bg-success bg-danger text-white').addClass('bg-warning text-dark').text('Pending');
      $('.edit-btn').show();
    }

    // Vendor
    const addr = d.vendor_address;
    $('.vendor-name-panel').text(d.vendor_name || '—');
    if (addr) {
      $('.vendor-address-panel').text([addr.address, addr.city, addr.state, addr.country, addr.zip_code].filter(Boolean).join(', '));
      $('.vendor-phone-panel').text(addr.phone || '—');
    } else {
      $('.vendor-address-panel').text('—');
      $('.vendor-phone-panel').text('—');
    }

    // Items
    const items = d.items || [];
    if (items.length) {
      const rows = items.map(item => `
        <tr>
          <td>${item.item_details || '—'}</td>
          <td>${item.quantity || 0}</td>
          <td>${item.receivable_quantity || 0}</td>
          <td>${item.acceptable_quantity || 0}</td>
          <td>${item.reject_quantity || 0}</td>
          <td>${item.balance_quantity || 0}</td>
        </tr>`).join('');
      $('#grn-panel-items').html(rows);
    } else {
      $('#grn-panel-items').html('<tr><td colspan="6" class="text-center text-muted py-3">No items found</td></tr>');
    }

    // Documents
    $('.document-preview-bill').empty();
    if (d.grn_all && d.grn_all.documents) {
      try {
        const docs = JSON.parse(d.grn_all.documents);
        docs.forEach(filename => generatePreviewHtml(filename, '../public/uploads/vendor/grn/', '.document-preview-bill', ''));
      } catch(e) {}
    }
  }

  function generatePreviewHtml(filename, basePath, containerId, title) {
    const ext = filename.split('.').pop().toLowerCase();
    const fileArray = JSON.stringify([basePath + filename]);
    let iconUrl = ['jpg','jpeg','png','gif','webp'].includes(ext)
      ? `${basePath}${filename}`
      : ext === 'pdf'
        ? 'https://cdn-icons-png.flaticon.com/512/337/337946.png'
        : 'https://cdn-icons-png.flaticon.com/512/564/564619.png';

    $(containerId).append(`
      <div>
        ${title ? '<h6>' + title + '</h6>' : ''}
        <div class="preview-card documentclk" data-filetype="documents" data-files="${fileArray.replace(/"/g,'&quot;')}">
          <img src="${iconUrl}" alt="file" style="height:60px;">
          <div>${filename}</div>
        </div>
      </div>`);
  }

  // Edit & PDF from panel
  $(document).on('click', '.edit-btn', function () {
    const id = $('#grnDetailPanel').data('grn-id');
    window.location.href = "{{ route('superadmin.getgrncreate') }}" + "?id=" + id;
  });
  $(document).on('click', '.pdf-btn-panel', function () {
    const id = $('#grnDetailPanel').data('grn-id');
    $.ajax({
      url: '{{ route("superadmin.getpurchasepdf") }}', method:'GET', data:{ id },
      xhrFields:{ responseType:'blob' },
      success: function(data) {
        const url = window.URL.createObjectURL(new Blob([data],{type:'application/pdf'}));
        const a = document.createElement('a'); a.href=url; a.download=`grn_${id}.pdf`;
        document.body.appendChild(a); a.click(); a.remove(); window.URL.revokeObjectURL(url);
      }
    });
  });
  $(document).on('click', '.print-btn', function () {
    const id = $('#grnDetailPanel').data('grn-id');
    $.ajax({
      url:'{{ route("superadmin.getpurchaseprint") }}', method:'GET', data:{id},
      xhrFields:{ responseType:'blob' },
      success: function(res) {
        window.open(URL.createObjectURL(new Blob([res],{type:'application/pdf'})), '_blank');
      }
    });
  });

  // Document modal
  $(document).on('click', '.documentclk', function () {
    $('#documentModal1').modal('show');
    const filesData = $(this).attr('data-files');
    let fileArray = [];
    try {
      fileArray = JSON.parse(filesData);
      if (typeof fileArray === 'string') fileArray = JSON.parse(fileArray);
    } catch(e) { return; }
    if (!Array.isArray(fileArray) || !fileArray.length) return;
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

});
</script>

<script>
// Override cb so "All Dates" preset clears the filter
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
