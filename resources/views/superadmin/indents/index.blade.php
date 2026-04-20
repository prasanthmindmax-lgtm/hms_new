<!doctype html>
<html lang="en">
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/indents.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
</head>

<body style="overflow-x: hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container in-page">
  <div class="pc-content">
    <div class="qd-card tk-tickets-page">
      <div class="tk-hero">
        <div class="tk-hero-inner">
          <h1 class="tk-hero-title"><i class="bi bi-box-seam" aria-hidden="true"></i> Store indents</h1>
        </div>
        <div class="tk-hero-actions">
          <a href="{{ route('superadmin.indents.create') }}" class="tk-btn-raise" style="text-decoration:none;"><i class="bi bi-plus-lg"></i> New indent</a>
        </div>
      </div>

      <div class="tk-dash-body">
        <div class="tk-stats-row" role="toolbar" id="inStatRow">
          @php
            $statMeta = [
                'pending' => ['class' => 'tk-stat-open', 'icon' => 'bi-clock-history'],
                'approved' => ['class' => 'tk-stat-in_progress', 'icon' => 'bi-check2'],
                'issued' => ['class' => 'tk-stat-closed', 'icon' => 'bi-check2-circle'],
                'rejected' => ['class' => 'tk-stat-cancelled', 'icon' => 'bi-slash-circle'],
            ];
          @endphp
          <button type="button" class="tk-stat-card in-stat-card tk-stat-total in-stat-active tk-stat-active" data-status="ALL" id="in-stat-all" style="cursor:pointer;" aria-pressed="true">
            <span class="tk-stat-ic"><i class="bi bi-layers"></i></span>
            <span class="tk-stat-lbl">All</span>
            <span class="tk-stat-num" data-stat="total">0</span>
            <span class="tk-stat-hint">All indents</span>
          </button>
          @foreach($statuses as $st)
            @php $m = $statMeta[$st] ?? ['class' => 'tk-stat-open', 'icon' => 'bi-dot']; @endphp
            <button type="button" class="tk-stat-card in-stat-card {{ $m['class'] }}" data-status="{{ $st }}" id="in-stat-{{ $st }}" style="cursor:pointer;" aria-pressed="false">
              <span class="tk-stat-ic"><i class="bi {{ $m['icon'] }}"></i></span>
              <span class="tk-stat-lbl">{{ ucwords(str_replace('_', ' ', $st)) }}</span>
              <span class="tk-stat-num" data-stat="{{ $st }}">0</span>
              <span class="tk-stat-hint">{{ ucwords(str_replace('_', ' ', $st)) }} indents</span>
            </button>
          @endforeach
        </div>

        <div class="tk-filter-shell tk-filter-qd">
          <div class="tk-filter-head">
            <div class="tk-filter-title"><i class="bi bi-sliders2" aria-hidden="true"></i> Refine list</div>
          </div>
          <div class="qd-filters tk-ticket-qd-filters">
            <div class="qd-filter-row">
              <div class="qd-filter-group">
                <label><i class="bi bi-calendar3 me-1"></i>Date range</label>
                <div class="qd-date-wrap" id="inDateRange" style="cursor:pointer;">
                  <i class="fa fa-calendar"></i>
                  <span id="inDateLabel">All dates</span>
                  <i class="fa fa-caret-down" style="margin-left:auto;"></i>
                  <input type="hidden" id="inDateValues" value="">
                </div>
              </div>
              <div class="qd-filter-group" style="padding-top: 5px;">
                <label>Department</label>
                <select id="filterDept" class="form-control form-control-sm">
                  <option value="">All</option>
                  @foreach($departments as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="qd-filter-group" style="padding-top: 5px;">
                <label>Status</label>
                <select id="filterStatus" class="form-control form-control-sm">
                  <option value="">All</option>
                  @foreach($statuses as $st)
                    <option value="{{ $st }}">{{ ucwords(str_replace('_', ' ', $st)) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="qd-search-row tk-ticket-search-row">
            <div class="tk-ticket-search-left">
              <div class="qd-search-wrap">
                <i class="bi bi-search"></i>
                <input type="search" id="inSearch" placeholder="Indent no, remarks…" autocomplete="off">
              </div>
            </div>
          </div>
        </div>

        <div class="tk-table-card">
          <div class="qdt-wrap" style="overflow-x:auto;">
            <table class="qdt-table">
              <thead class="qdt-head">
                <tr>
                  <th>INDENT</th>
                  <th>DATE</th>
                  <th>FROM → TO</th>
                  <th>BRANCH</th>
                  <th>STATUS</th>
                  <th>ITEM</th>
                  <th class="text-end">ACTIONS</th>
                </tr>
              </thead>
              <tbody id="inTableBody"><tr><td colspan="7" class="text-center tk-tk-empty py-4">Loading…</td></tr></tbody>
            </table>
          </div>
        </div>

        {{-- Side detail panel --}}
        <div class="zoho-modal-overlay" id="indentModalOverlay"></div>
        <div class="zoho-modal" id="indentDetailModal">
          <div class="zoho-modal-content">
            <div class="zoho-modal-header">
              <div class="zoho-modal-title" id="inDetTitle">Indent</div>
              <div class="zoho-modal-actions">
                <button type="button" class="zoho-btn zoho-btn-icon in-det-close" title="Close" aria-label="Close">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            </div>
            <div class="zoho-modal-body">
              <div id="inDetErr" class="alert alert-danger py-2 d-none small mb-3"></div>
              <div id="inDetLoading" class="text-center py-5 text-muted">
                <div class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></div>
                <div class="mt-2 small">Loading indent…</div>
              </div>
              <div id="inDetContent" class="d-none">
                <div class="zoho-section">
                  <div class="zoho-section-title">Indent summary</div>
                  <div class="bill-info-section">
                    <div class="bill-info-row"><div class="bill-info-label">INDENT NO</div><div class="bill-info-value" id="inDetIndentNo">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">STATUS</div><div class="bill-info-value" id="inDetStatus">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">CREATED</div><div class="bill-info-value" id="inDetCreated">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">UPDATED</div><div class="bill-info-value" id="inDetUpdated">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">REQUIRED DATE</div><div class="bill-info-value" id="inDetRequired">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">PURPOSE</div><div class="bill-info-value" id="inDetPurpose">—</div></div>
                  </div>
                </div>
                <div class="zoho-section">
                  <div class="zoho-section-title">Location &amp; department</div>
                  <div class="bill-info-section">
                    <div class="bill-info-row"><div class="bill-info-label">COMPANY</div><div class="bill-info-value" id="inDetCompany">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">ZONE</div><div class="bill-info-value" id="inDetZone">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">BRANCH</div><div class="bill-info-value" id="inDetBranch">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">FROM DEPT</div><div class="bill-info-value" id="inDetFromDept">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">TO DEPT</div><div class="bill-info-value" id="inDetToDept">—</div></div>
                  </div>
                </div>
                <div class="zoho-section">
                  <div class="zoho-section-title">People &amp; workflow</div>
                  <div class="bill-info-section">
                    <div class="bill-info-row"><div class="bill-info-label">RAISED BY</div><div class="bill-info-value" id="inDetCreator">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">APPROVED BY</div><div class="bill-info-value" id="inDetApprover">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">APPROVED AT</div><div class="bill-info-value" id="inDetApprovedAt">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">LAST STATUS BY</div><div class="bill-info-value" id="inDetLastBy">—</div></div>
                  </div>
                </div>
                <div class="zoho-section d-none" id="inDetRejectSection">
                  <div class="zoho-section-title">Rejection</div>
                  <div class="bill-info-section">
                    <div class="bill-info-row"><div class="bill-info-label">REJECTED BY</div><div class="bill-info-value" id="inDetRejector">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">REJECTED AT</div><div class="bill-info-value" id="inDetRejectedAt">—</div></div>
                    <div class="bill-info-row"><div class="bill-info-label">REASON</div><div class="bill-info-value" id="inDetRejectReason">—</div></div>
                  </div>
                </div>
                <div class="zoho-section">
                  <div class="zoho-section-title">Remarks</div>
                  <div class="zoho-notes-content" id="inDetRemarks">—</div>
                </div>
                <div class="zoho-section">
                  <div class="zoho-section-title">Line items</div>
                  <table class="zoho-items-table">
                    <thead>
                      <tr>
                        <th>ITEM</th>
                        <th>CATEGORY</th>
                        <th style="text-align:right;">REQUESTED</th>
                        <th style="text-align:right;">ISSUED</th>
                        <th style="text-align:right;">REMAINING</th>
                        <th>STORE / AVAIL</th>
                      </tr>
                    </thead>
                    <tbody id="inDetLinesBody"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="tk-pagination-wrap" style="padding-top: 5px; padding-bottom: 5px;">
          <div class="tk-pagination-inner" style="display:flex; align-items:center; justify-content: space-between;">
            <div class="tk-pagination-meta" style="flex:none; margin-right: 15px;">
              <label class="tk-per-page-label" for="inPerPage">Rows per page</label>
              <select id="inPerPage" class="tk-per-page-select" style="width: auto; display: inline-block;">
                @foreach([10,15,25,50] as $n)<option value="{{ $n }}" {{ $n===15?'selected':'' }}>{{ $n }}</option>@endforeach
              </select>
            </div>
            <div style="flex:1; display:flex; justify-content:flex-end;">
              <div id="inPagination"></div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- Reject pending indent --}}
<div class="modal fade" id="inStatusModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="inStatusTitle">Reject indent</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="inStatusIndentId">
        <div class="mb-2">
          <label class="form-label">Rejection reason <span class="text-danger">*</span></label>
          <textarea id="inRejectReason" class="form-control" rows="3" placeholder="Required"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="inBtnConfirmReject">Reject indent</button>
      </div>
    </div>
  </div>
</div>

{{-- Dispatch stock from store --}}
<div class="modal fade" id="inIssueModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Dispatch stock</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="inIssueIndentId">
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>Item</th><th>Requested</th><th>Issued</th><th>Remaining</th><th>Store avail</th><th>Issue now</th></tr></thead>
            <tbody id="inIssueBody"></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="inBtnSubmitIssue">Record dispatch</button>
      </div>
    </div>
  </div>
</div>

{{-- History --}}
<div class="modal fade" id="inHistModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Audit log</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="inHistoryBody"></div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
(function () {
  const routes = {
    data: @json(route('superadmin.indents.data')),
    detail: function (id) { return @json(url('/superadmin/indents')) + '/' + id + '/detail'; },
    status: @json(url('/superadmin/indents')),
    issue: @json(url('/superadmin/indents')),
    history: @json(url('/superadmin/indents')),
  };
  const csrf = $('meta[name="csrf-token"]').attr('content');
  let page = 1;
  let activeStatus = '';

  function parseDateRange() {
    var v = ($('#inDateValues').val() || '').trim();
    if (!v || v.indexOf(' to ') === -1) return { from: '', to: '' };
    var p = v.split(' to ');
    var m1 = moment(p[0].trim(), 'DD/MM/YYYY', true);
    var m2 = moment(p[1].trim(), 'DD/MM/YYYY', true);
    if (!m1.isValid() || !m2.isValid()) return { from: '', to: '' };
    return { from: m1.format('YYYY-MM-DD'), to: m2.format('YYYY-MM-DD') };
  }

  function fetchIndents(resetPage) {
    if (resetPage) page = 1;
    const dr = parseDateRange();
    const params = {
      page: page,
      per_page: $('#inPerPage').val(),
      department_id: $('#filterDept').val() || undefined,
      status: (activeStatus !== '' ? activeStatus : ($('#filterStatus').val() || undefined)),
      date_from: dr.from || undefined,
      date_to: dr.to || undefined,
      universal_search: ($('#inSearch').val() || '').trim() || undefined,
    };
    $.get(routes.data, params, function (res) {
      if (!res.success) {
        toastr.error('Could not load indents');
        return;
      }
      const stats = res.stats || {};
      const by = stats.by_status || {};
      let total = 0;
      Object.keys(by).forEach(function (k) {
        total += parseInt(by[k], 10) || 0;
        $('[data-stat="' + k + '"]').text(by[k]);
      });
      $('[data-stat="total"]').text(stats.total != null ? stats.total : total);

      let html = '';
      (res.indents || []).forEach(function (r) {
        const linesSummary = (r.lines || []).map(function (l) {
          return $('<div>').text(l.item_name + ' × ' + l.quantity_requested).html();
        }).join('<br>');

        let actBtns = '<button type="button" class="btn btn-sm in-btn-hist" style="font-size:11px;padding:4px 12px;border:1px solid #6c757d;color:#6c757d;border-radius:6px;background:#fff;margin-left:4px;" data-id="' + r.id + '">Log</button>';
        if (r.can_issue) {
          actBtns += ' <button type="button" class="btn btn-sm in-btn-approve" style="font-size:11px;padding:4px 12px;border:1px solid #4f6ef7;color:#4f6ef7;border-radius:6px;background:#fff;margin-left:4px;" data-id="' + r.id + '">Approve</button>';
        }
        if (r.can_reject) {
          actBtns += ' <button type="button" class="btn btn-sm in-btn-rej" style="font-size:11px;padding:4px 12px;border:1px solid #dc3545;color:#dc3545;border-radius:6px;background:#fff;margin-left:4px;" data-id="' + r.id + '">Reject</button>';
        }
        if (r.can_dispatch) {
          actBtns += ' <button type="button" class="btn btn-sm in-btn-dispatch" style="font-size:11px;padding:4px 12px;border:1px solid #198754;color:#198754;border-radius:6px;background:#fff;margin-left:4px;" data-id="' + r.id + '">Dispatch</button>';
        }

        let bClass = 'bg-secondary';
        const ls = (r.status || '').toLowerCase();
        if(ls === 'pending') bClass = 'bg-warning text-dark';
        else if(ls === 'approved') bClass = 'bg-primary';
        else if(ls === 'issued') bClass = 'bg-success';
        else if(ls === 'rejected') bClass = 'bg-danger';

        var fn = (r.from_department_name || '').trim();
        var tn = (r.to_department_name || '').trim();
        var deptPair = (!fn && !tn) ? '—' : $('<div>').text((fn || '—') + ' → ' + (tn || '—')).html();
        html += '<tr class="qdt-row in-indent-row" data-id="' + r.id + '" style="cursor:pointer;">' +
          '<td class="qdt-mono" style="font-weight: 600; font-size: 13px;">' + $('<div>').text(r.indent_no).html() + '</td>' +
          '<td>' + $('<div>').text(r.created_at || '').html() + '</td>' +
          '<td class="small">' + deptPair + '</td>' +
          '<td>' + $('<div>').text(r.branch_name || '').html() + '</td>' +
          '<td><span class="badge ' + bClass + '">' + $('<div>').text(r.status).html() + '</span></td>' +
          '<td class="small">' + (linesSummary || '—') + '</td>' +
          '<td class="text-end in-table-actions">' + actBtns + '</td></tr>';
      });
      if (!html) html = '<tr class="qdt-row"><td colspan="7" class="text-center tk-tk-empty py-4">No indents found.</td></tr>';
      $('#inTableBody').html(html);

      const pg = res.pagination || {};
      let pag = '';
      if (pg.last_page > 1) {
        pag += '<div class="tk-page-nums" role="list" style="margin:0;">';
        for (let i = 1; i <= pg.last_page; i++) {
          pag += '<button type="button" class="tk-page-num in-page-btn" aria-current="' + (i === pg.current_page ? 'page' : 'false') + '" data-p="' + i + '">' + i + '</button> ';
        }
        pag += '</div>';
      }
      $('#inPagination').html(pag);
    });
  }

  $('#inStatRow').on('click', '.in-stat-card', function () {
    $('#inStatRow .in-stat-card').removeClass('in-stat-active tk-stat-active');
    $(this).addClass('in-stat-active tk-stat-active');
    var s = $(this).attr('data-status');
    activeStatus = (s === 'ALL' || typeof s === 'undefined' || s === '') ? '' : s;
    $('#filterStatus').val(activeStatus);
    fetchIndents(true);
  });

  $('#filterDept, #filterStatus, #inPerPage').on('change', function () {
    activeStatus = '';
    $('#inStatRow .in-stat-card').removeClass('in-stat-active tk-stat-active');
    $('#in-stat-all').addClass('in-stat-active tk-stat-active');
    fetchIndents(true);
  });
  $('#inSearch').on('keyup', function (e) {
    if (e.key === 'Enter') fetchIndents(true);
  });

  $(document).on('click', '.in-page-btn', function () {
    page = parseInt($(this).data('p'), 10) || 1;
    fetchIndents(false);
  });

  if ($.fn.daterangepicker) {
    $('#inDateRange').daterangepicker(
      { autoUpdateInput: false, locale: { cancelLabel: 'Clear', format: 'DD/MM/YYYY' } },
      function () {}
    );
    $('#inDateRange').on('apply.daterangepicker', function (ev, picker) {
      $('#inDateLabel').text(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
      $('#inDateValues').val(picker.startDate.format('DD/MM/YYYY') + ' to ' + picker.endDate.format('DD/MM/YYYY'));
      fetchIndents(true);
    });
    $('#inDateRange').on('cancel.daterangepicker', function () {
      $('#inDateLabel').text('All dates');
      $('#inDateValues').val('');
      fetchIndents(true);
    });
  }

  let statusModal, issueModal, histModal;

  function inDetEsc(s) {
    return $('<div>').text(s == null || s === '' ? '—' : String(s)).html();
  }
  function inDetFmtDt(v) {
    if (v == null || v === '') return '—';
    var m = moment(v);
    return m.isValid() ? m.format('DD/MM/YYYY HH:mm') : String(v);
  }
  function inDetFmtDateOnly(v) {
    if (v == null || v === '') return '—';
    var m = moment(v, [moment.ISO_8601, 'YYYY-MM-DD'], true);
    if (!m.isValid()) m = moment(v);
    return m.isValid() ? m.format('DD/MM/YYYY') : String(v);
  }
  function inDetPerson(u) {
    if (!u || typeof u !== 'object') return '—';
    return u.user_fullname || u.name || u.username || '—';
  }
  function closeIndentDetailModal() {
    $('#indentDetailModal').removeClass('show loading');
    $('#indentModalOverlay').removeClass('show');
    $('body').css('overflow', 'auto');
  }
  function openIndentDetailModal(id) {
    $('#inDetErr').addClass('d-none').text('');
    $('#inDetContent').addClass('d-none');
    $('#inDetLoading').removeClass('d-none');
    $('#indentDetailModal').addClass('show loading').data('indent-id', id);
    $('#indentModalOverlay').addClass('show');
    $('body').css('overflow', 'hidden');
    $('#inDetTitle').text('Indent #' + id);

    $.get(routes.detail(id), function (res) {
      $('#indentDetailModal').removeClass('loading');
      $('#inDetLoading').addClass('d-none');
      if (!res.success || !res.indent) {
        $('#inDetErr').removeClass('d-none').text(res.message || 'Could not load indent.');
        return;
      }
      var ind = res.indent;
      $('#inDetTitle').text(ind.indent_no ? String(ind.indent_no) : ('Indent #' + ind.id));
      $('#inDetIndentNo').html(inDetEsc(ind.indent_no));
      $('#inDetStatus').html(inDetEsc(ind.status));
      $('#inDetCreated').html(inDetFmtDt(ind.created_at));
      $('#inDetUpdated').html(inDetFmtDt(ind.updated_at));
      $('#inDetRequired').html(ind.required_date ? inDetFmtDateOnly(ind.required_date) : '—');
      $('#inDetPurpose').html(ind.purpose != null && ind.purpose !== '' ? inDetEsc(ind.purpose) : '—');
      $('#inDetCompany').html(inDetEsc(ind.company && ind.company.company_name));
      $('#inDetZone').html(inDetEsc(ind.zone && ind.zone.name));
      $('#inDetBranch').html(inDetEsc(ind.branch && ind.branch.name));
      var fdn = (ind.from_department && ind.from_department.name) ? String(ind.from_department.name) : '';
      var tdn = (ind.to_department && ind.to_department.name) ? String(ind.to_department.name) : '';
      $('#inDetFromDept').text(fdn || '—');
      $('#inDetToDept').text(tdn || '—');
      $('#inDetCreator').html(inDetPerson(ind.creator));
      $('#inDetApprover').html(inDetPerson(ind.approver));
      $('#inDetApprovedAt').html(inDetFmtDt(ind.approved_at));
      $('#inDetLastBy').html(inDetPerson(ind.last_status_by));

      if (ind.status === 'rejected' && (ind.rejection_reason || ind.rejector)) {
        $('#inDetRejectSection').removeClass('d-none');
        $('#inDetRejector').html(inDetPerson(ind.rejector));
        $('#inDetRejectedAt').html(inDetFmtDt(ind.rejected_at));
        $('#inDetRejectReason').html(ind.rejection_reason ? inDetEsc(ind.rejection_reason) : '—');
      } else {
        $('#inDetRejectSection').addClass('d-none');
      }

      $('#inDetRemarks').css('white-space', 'pre-wrap').text(ind.remarks != null && ind.remarks !== '' ? ind.remarks : '—');

      var lines = ind.lines || [];
      var lh = '';
      if (!lines.length) {
        lh = '<tr><td colspan="6" class="text-muted">No line items.</td></tr>';
      } else {
        lines.forEach(function (l) {
          var req = parseFloat(l.quantity_requested, 10) || 0;
          var iss = parseFloat(l.quantity_issued, 10) || 0;
          var rem = Math.max(0, req - iss);
          var cs = l.consumable_store;
          var storeTxt = '—';
          if (cs) {
            var nm = cs.item_name || '';
            var q = cs.quantity != null ? parseFloat(cs.quantity, 10) : '';
            storeTxt = (nm || '—') + (q !== '' && !isNaN(q) ? ' (avail: ' + q + ')' : '');
          }
          lh += '<tr>' +
            '<td class="row_first">' + inDetEsc(l.item_name) + '</td>' +
            '<td>' + inDetEsc(l.item_category) + '</td>' +
            '<td style="text-align:right;">' + inDetEsc(req) + '</td>' +
            '<td style="text-align:right;">' + inDetEsc(iss) + '</td>' +
            '<td style="text-align:right;">' + inDetEsc(rem) + '</td>' +
            '<td><span class="small">' + inDetEsc(storeTxt) + '</span></td>' +
            '</tr>';
        });
      }
      $('#inDetLinesBody').html(lh);
      $('#inDetContent').removeClass('d-none');
    }).fail(function (xhr) {
      $('#indentDetailModal').removeClass('loading');
      $('#inDetLoading').addClass('d-none');
      var j = xhr.responseJSON;
      $('#inDetErr').removeClass('d-none').text((j && j.message) || 'Could not load indent.');
    });
  }

  $(document).on('click', '.in-indent-row', function (e) {
    if ($(e.target).closest('.in-table-actions').length) return;
    if ($(e.target).closest('button').length) return;
    if ($(e.target).closest('a').length) return;
    var id = $(this).data('id');
    if (!id) return;
    openIndentDetailModal(id);
  });
  $(document).on('click', '.in-det-close, #indentModalOverlay', function (e) {
    e.stopPropagation();
    closeIndentDetailModal();
  });
  $(document).on('keyup.indentDet', function (e) {
    if (e.key === 'Escape' && $('#indentDetailModal').hasClass('show')) {
      closeIndentDetailModal();
    }
  });

  $(function () {
    statusModal = new bootstrap.Modal(document.getElementById('inStatusModal'));
    issueModal = new bootstrap.Modal(document.getElementById('inIssueModal'));
    histModal = new bootstrap.Modal(document.getElementById('inHistModal'));
    fetchIndents(true);
  });

  $(document).on('click', '.in-btn-rej', function () {
    $('#inStatusIndentId').val($(this).data('id'));
    $('#inRejectReason').val('');
    statusModal.show();
  });

  $('#inBtnConfirmReject').on('click', function () {
    const id = $('#inStatusIndentId').val();
    const reason = ($('#inRejectReason').val() || '').trim();
    if (!reason) {
      toastr.error('Enter rejection reason');
      return;
    }
    $.ajax({
      url: routes.status + '/' + id + '/status',
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
      data: { status: 'rejected', rejection_reason: reason },
      success: function (res) {
        if (res.success) {
          toastr.success(res.message || 'Rejected');
          statusModal.hide();
          fetchIndents(false);
        } else toastr.error(res.message || 'Failed');
      },
      error: function (xhr) {
        const j = xhr.responseJSON;
        toastr.error((j && j.message) || 'Failed');
      },
    });
  });

  $(document).on('click', '.in-btn-approve', function () {
    var id = $(this).data('id');
    if (!id) return;
    if (!window.confirm('Approve this indent?')) return;
    $.ajax({
      url: routes.status + '/' + id + '/status',
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
      data: { status: 'approved' },
      success: function (res) {
        if (res.success) {
          toastr.success(res.message || 'Indent approved.');
          fetchIndents(false);
        } else toastr.error(res.message || 'Failed');
      },
      error: function (xhr) {
        var j = xhr.responseJSON;
        toastr.error((j && j.message) || 'Failed');
      },
    });
  });

  $(document).on('click', '.in-btn-dispatch', function () {
    const id = $(this).data('id');
    $('#inIssueIndentId').val(id);
    $.get(routes.detail(id), function (res) {
      if (!res.success || !res.indent) {
        toastr.error('Could not load indent');
        return;
      }
      const lines = res.indent.lines || [];
      let h = '';
      lines.forEach(function (l) {
        const req = parseFloat(l.quantity_requested, 10);
        const iss = parseFloat(l.quantity_issued, 10);
        const avail = l.consumable_store ? parseFloat(l.consumable_store.quantity, 10) : 0;
        const rem = Math.max(0, req - iss);
        if (rem <= 0) return;
        h += '<tr data-line-id="' + l.id + '">' +
          '<td>' + $('<div>').text(l.item_name).html() + '</td>' +
          '<td>' + req + '</td>' +
          '<td>' + iss + '</td>' +
          '<td>' + rem + '</td>' +
          '<td>' + avail + '</td>' +
          '<td><input type="number" step="0.01" class="form-control form-control-sm in-issue-qty" max="' + rem + '" placeholder="Qty"></td></tr>';
      });
      if (!h) h = '<tr><td colspan="6">Nothing left to issue.</td></tr>';
      $('#inIssueBody').html(h);
      issueModal.show();
    });
  });

  $('#inBtnSubmitIssue').on('click', function () {
    const id = $('#inIssueIndentId').val();
    const lines = [];
    $('#inIssueBody tr').each(function () {
      const lid = $(this).data('line-id');
      const q = parseFloat($(this).find('.in-issue-qty').val(), 10);
      if (lid && !isNaN(q) && q > 0) lines.push({ id: lid, issue_qty: q });
    });
    if (!lines.length) {
      toastr.error('Enter issue quantities.');
      return;
    }
    $.ajax({
      url: routes.issue + '/' + id + '/issue',
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
      contentType: 'application/json',
      data: JSON.stringify({ lines: lines }),
      success: function (res) {
        if (res.success) {
          toastr.success(res.message || 'Issued');
          issueModal.hide();
          fetchIndents(false);
        } else toastr.error(res.message || 'Failed');
      },
      error: function (xhr) {
        const j = xhr.responseJSON;
        toastr.error((j && j.message) || (j && j.errors && j.errors.lines && j.errors.lines[0]) || 'Failed');
      },
    });
  });

  $(document).on('click', '.in-btn-hist', function () {
    const id = $(this).data('id');
    $.get(routes.history + '/' + id + '/history', function (res) {
      if (!res.success) {
        toastr.error('Could not load history');
        return;
      }
      let h = '<ul class="list-unstyled mb-0">';
      (res.items || []).forEach(function (it) {
        h += '<li class="mb-2 border-bottom pb-2"><strong>' + $('<div>').text(it.action).html() + '</strong> · ' +
          $('<div>').text(it.user_name).html() + ' · ' + $('<div>').text(it.created_at).html();
        if (it.payload) {
          h += '<pre class="small mb-0 mt-1" style="white-space:pre-wrap;">' + $('<div>').text(JSON.stringify(it.payload, null, 2)).html() + '</pre>';
        }
        h += '</li>';
      });
      h += '</ul>';
      $('#inHistoryBody').html(h || '<p class="text-muted mb-0">No entries.</p>');
      histModal.show();
    });
  });
})();
</script>

@include('superadmin.superadminfooter')
</body>
</html>
