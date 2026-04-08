<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />

<body style="overflow-x: hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    <div class="qd-card tk-tickets-page">

      <div class="tk-hero">
        <div class="tk-hero-inner">
          <h1 class="tk-hero-title"><i class="bi bi-ticket-perforated" aria-hidden="true"></i> Ticket Management</h1>
        </div>
        <div class="tk-hero-actions">
          <button type="button" class="tk-btn-export" id="btnExportTickets" title="Export filtered list to Excel">
            <i class="bi bi-file-earmark-spreadsheet" aria-hidden="true"></i>Export Excel
          </button>
          <button type="button" class="tk-btn-raise" id="btnNewTicket">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>Raise ticket
          </button>
        </div>
      </div>

      <div class="tk-dash-body">
        <div class="tk-stats-row" role="toolbar" aria-label="Ticket status summary">
          <button type="button" class="tk-stat-card tk-stat-total tk-stat-active" data-status="" title="Show all statuses" aria-pressed="true">
            <span class="tk-stat-ic" aria-hidden="true"><i class="bi bi-layers"></i></span>
            <span class="tk-stat-lbl">Total</span>
            <span class="tk-stat-num" id="stat-total">—</span>
            <span class="tk-stat-hint">In selected view scope</span>
          </button>
          <button type="button" class="tk-stat-card tk-stat-open" data-status="open" title="Filter: Open" aria-pressed="false">
            <span class="tk-stat-ic" aria-hidden="true"><i class="bi bi-circle-fill" style="font-size:10px;"></i></span>
            <span class="tk-stat-lbl">Open</span>
            <span class="tk-stat-num" id="stat-open">—</span>
            <span class="tk-stat-hint">Awaiting action</span>
          </button>
          <button type="button" class="tk-stat-card tk-stat-in_progress" data-status="in_progress" title="Filter: In progress" aria-pressed="false">
            <span class="tk-stat-ic" aria-hidden="true"><i class="bi bi-arrow-repeat"></i></span>
            <span class="tk-stat-lbl">In progress</span>
            <span class="tk-stat-num" id="stat-in_progress">—</span>
            <span class="tk-stat-hint">Being worked</span>
          </button>
          <button type="button" class="tk-stat-card tk-stat-closed" data-status="closed" title="Filter: Closed" aria-pressed="false">
            <span class="tk-stat-ic" aria-hidden="true"><i class="bi bi-check2-circle"></i></span>
            <span class="tk-stat-lbl">Closed</span>
            <span class="tk-stat-num" id="stat-closed">—</span>
            <span class="tk-stat-hint">Completed</span>
          </button>
          <button type="button" class="tk-stat-card tk-stat-cancelled" data-status="cancelled" title="Filter: Cancelled" aria-pressed="false">
            <span class="tk-stat-ic" aria-hidden="true"><i class="bi bi-slash-circle"></i></span>
            <span class="tk-stat-lbl">Cancelled</span>
            <span class="tk-stat-num" id="stat-cancelled">—</span>
            <span class="tk-stat-hint">Withdrawn</span>
          </button>
        </div>

        <div class="tk-filter-shell tk-filter-qd">
          <div class="tk-filter-head">
            <div class="tk-filter-title"><i class="bi bi-sliders2" aria-hidden="true"></i> Refine list</div>
            <span class="tk-showing-pill" id="tkShowingPill">Rows <strong id="stat-showing-range">—</strong> of <strong id="stat-filtered-total">—</strong></span>
          </div>
          <div class="qd-filters tk-ticket-qd-filters">
            <div class="qd-filter-row">
              <div class="qd-filter-group">
                <label><i class="bi bi-calendar3 me-1"></i>Date range</label>
                <div class="qd-date-wrap" id="tkTicketReportRange">
                  <i class="fa fa-calendar"></i>
                  <span id="tkTicketDateLabel">All Dates</span>
                  <i class="fa fa-caret-down" style="margin-left:auto;"></i>
                  <input type="hidden" class="tk-ticket-data-values" value="">
                </div>
              </div>
              <div class="qd-filter-group tax-dropdown-wrapper tk-ticket-filter-tax tk-ticket-dept-wrap">
                <label>To department</label>
                <input type="text" class="form-control tk-dept-search-input dropdown-search-input" placeholder="All departments" value="All departments" readonly autocomplete="off">
                <input type="hidden" id="departmentFilter" name="to_department_id" value="">
                <div class="dropdown-menu tax-dropdown tk-ticket-filter-dd">
                  <div class="inner-search-container">
                    <input type="text" class="inner-search" placeholder="Search department…" autocomplete="off">
                  </div>
                  <div class="tk-ticket-dd-actions">
                    <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                  </div>
                  <div class="dropdown-list multiselect tk-ticket-dept-list">
                    @foreach($departments as $dept)
                      <div data-value="{{ $dept->name }}" data-id="{{ $dept->id }}">{{ $dept->name }}</div>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="qd-filter-group tax-dropdown-wrapper tk-ticket-filter-tax tk-ticket-status-wrap">
                <label>Status</label>
                <input type="text" class="form-control tk-status-search-input dropdown-search-input" placeholder="All statuses" value="All statuses" readonly autocomplete="off">
                <input type="hidden" id="statusFilter" name="ticket_status" value="">
                <div class="dropdown-menu tax-dropdown tk-ticket-filter-dd">
                  <div class="inner-search-container">
                    <input type="text" class="inner-search" placeholder="Search status…" autocomplete="off">
                  </div>
                  <div class="tk-ticket-dd-actions">
                    <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                  </div>
                  <div class="dropdown-list multiselect tk-ticket-status-list">
                    @foreach($statuses as $st)
                      <div data-value="{{ $st }}" data-id="{{ $st }}">{{ ucwords(str_replace('_',' ', $st)) }}</div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="qd-search-row tk-ticket-search-row">
            <div class="tk-ticket-search-left">
              <div class="qd-search-wrap">
                <i class="bi bi-search"></i>
                <input type="search" id="ticketUniversalSearch" placeholder="Search tickets…" autocomplete="off">
              </div>
            </div>
            <div class="tk-filter-actions">
              <button type="button" class="tk-refresh-btn" id="btnRefresh"><i class="bi bi-arrow-clockwise" aria-hidden="true"></i>Refresh</button>
              <button type="button" class="tk-refresh-btn tk-btn-clear-filters" id="btnClearFilters"><i class="bi bi-x-lg" aria-hidden="true"></i>Clear filters</button>
            </div>
          </div>
          <div class="qd-applied-bar tk-ticket-applied-bar">
            <span class="applied-label">Filters:</span>
            <div id="tkTicketFilterChips" class="tk-ticket-filter-chips"></div>
          </div>
        </div>

        <div class="tk-table-card">
      <div class="qdt-wrap" style="overflow-x:auto;">
        <table class="qdt-table" id="ticketsTable">
          <thead class="qdt-head">
            <tr>
              <th>TICKET NO</th>
              <th>DATE</th>
              <th>LOCATION</th>
              <th>FROM DEPARTMENT</th>
              <th>TO DEPARTMENT</th>
              <th>CATEGORY</th>
              <th>PRIORITY</th>
              <th>STATUS</th>
              <th>RAISED BY</th>
              <th>CLOSED BY</th>
              <th>CLOSED AT</th>
              <th>TAT</th>
              <th title="Compares category SLA (HH:MM allowance from raised to closed) with actual time to close">SLA VS ACTUAL</th>
              <th class="text-center">ACTION</th>
            </tr>
          </thead>
          <tbody id="ticketsBody">
            <tr class="qdt-row"><td colspan="14" class="text-center py-5 tk-tk-empty">Loading tickets…</td></tr>
          </tbody>
        </table>
      </div>
        </div>

        <div class="tk-pagination-wrap" id="tkPaginationWrap">
          <div class="tk-pagination-inner">
            <div class="tk-pagination-meta">
              <label class="tk-per-page-label" for="ticketPerPage">Rows per page</label>
              <select id="ticketPerPage" class="tk-per-page-select" aria-label="Rows per page">
                <option value="10">10</option>
                <option value="15" selected>15</option>
                <option value="25">25</option>
                <option value="50">50</option>
              </select>
            </div>
            <nav class="tk-pagination-pills" id="tkPaginationNav" aria-label="Ticket table pages">
              <button type="button" class="tk-page-arrow" id="tkPagePrev" aria-label="Previous page">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
              </button>
              <div class="tk-page-nums" id="tkPageNumbers" role="list"></div>
              <button type="button" class="tk-page-arrow" id="tkPageNext" aria-label="Next page">
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
              </button>
              <span class="tk-page-fraction" id="tkPageFraction" aria-live="polite">
                Page <span id="tkPageCurDisp">—</span> / <span id="tkPageLastDisp">—</span>
              </span>
            </nav>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

<div class="sm-modal-overlay" id="modalOverlay"></div>

<div class="sm-modal" id="raiseModal" role="dialog" aria-modal="true" aria-labelledby="raiseModalTitle">
  <div class="sm-modal-box wide raise-box tk-modal-font">
    <div class="raise-head">
      <div class="raise-head-inner">
        <div class="raise-title-wrap">
          <div class="raise-icon" aria-hidden="true"><i class="bi bi-ticket-perforated"></i></div>
          <div>
            <h2 class="raise-title" id="raiseModalTitle">Raise ticket</h2>
          </div>
        </div>
        <button type="button" class="sm-modal-close close-modal" aria-label="Close dialog">&times;</button>
      </div>
    </div>

    <form id="raiseForm" enctype="multipart/form-data">
      <input type="hidden" id="raise_ticket_id" value="" autocomplete="off">
      <div class="raise-form-body raise-form-premium">
        <p id="raise_edit_hint" class="tk-tk-muted" style="display:none; font-size:13px; margin:0 0 12px; font-weight:600;">
          You can edit while the ticket is <strong>Open</strong> only. After it moves to <strong>In progress</strong>, changes are not allowed.
        </p>
        <div class="tk-section">
          <div class="tk-section-head"><i class="bi bi-diagram-3"></i> Routing</div>
          <div class="sm-form-row raise-field-row" style="margin-bottom:0;">
            <div class="sm-form-group">
              <label for="raise_location_id">Location <span class="tk-req" aria-hidden="true">*</span></label>
              <select name="location_id" id="raise_location_id" class="form-control" required>
                <option value="">Select location</option>
                @foreach($locations as $loc)
                  <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="sm-form-group">
              <label for="raise_from_dept">From department <span class="tk-req" aria-hidden="true">*</span></label>
              <select name="from_department_id" id="raise_from_dept" class="form-control" required>
                <option value="">Select</option>
                @foreach($departments as $d)
                  <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="sm-form-row raise-field-row" style="margin-bottom:0; margin-top:4px;">
            <div class="sm-form-group">
              <label for="raise_to_dept">To department <span class="tk-req" aria-hidden="true">*</span></label>
              <select name="to_department_id" id="raise_to_dept" class="form-control" required>
                <option value="">Select</option>
                @foreach($departments as $d)
                  <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="sm-form-group">
              <label for="raise_category_id">Category <span class="tk-req" aria-hidden="true">*</span></label>
              <select name="ticket_category_id" id="raise_category_id" class="form-control" required disabled>
                <option value="">Choose “To department” first</option>
              </select>
            </div>
          </div>
        </div>

        <div class="tk-section">
          <div class="tk-section-head"><i class="bi bi-card-text"></i> Ticket details</div>
          <div class="sm-form-row raise-field-row" style="margin-bottom:0;">
            <div class="sm-form-group">
              <label for="raise_priority">Priority <span class="tk-req" aria-hidden="true">*</span></label>
              <select name="priority" id="raise_priority" class="form-control" required>
                @foreach($priorities as $p)
                  <option value="{{ $p }}" @if($p==='medium') selected @endif>{{ ucfirst($p) }}</option>
                @endforeach
              </select>
            </div>
            <div class="sm-form-group">
              <label for="raise_subject">Subject <span class="tk-req" aria-hidden="true">*</span></label>
              <input type="text" name="subject" id="raise_subject" class="form-control" maxlength="500" required placeholder="e.g. Printer not working — Ward 3">
            </div>
          </div>
          <div class="sm-form-row raise-field-row" style="margin-bottom:0; margin-top:4px;">
            <div class="sm-form-group" style="flex:1 1 100%; min-width:100%;">
              <label for="raise_description">Description</label>
              <textarea name="description" id="raise_description" class="form-control" rows="4" placeholder="What happened, when, and any steps already tried"></textarea>
            </div>
          </div>
        </div>

        <div class="tk-section">
          <div class="tk-section-head">
            <i class="bi bi-paperclip"></i> Attachments
            <span class="tk-sec-optional">(optional)</span>
          </div>
          <div class="tk-attach-panel">
            <div class="tk-file-list-card" id="raise_file_list_card" aria-live="polite">
              <div class="tk-file-list-title">
                <span>Selected files</span>
                <span id="raise_file_count_badge" aria-live="polite">0</span>
              </div>
              <ul class="tk-file-list" id="raise_file_list"></ul>
            </div>
            <div class="tk-file-zone" id="raise_file_zone" role="group" aria-label="Add attachments">
              <input type="file" name="attachments[]" id="raise_attachments" class="tk-file-input-hidden" multiple accept=".pdf,.png,.jpg,.jpeg,.gif,.webp,.doc,.docx,.xls,.xlsx" tabindex="-1">
              <button type="button" class="tk-file-zone-trigger" id="raise_file_pick_btn" aria-describedby="raise_file_hint">
                <span class="tk-file-zone-inner">
                  <i class="bi bi-cloud-arrow-up" aria-hidden="true"></i>
                  <span class="tk-file-main" id="raise_file_zone_title">Add files</span>
                  <span class="tk-file-sub" id="raise_file_hint">PDF, images, Word, Excel — up to 10 MB each. Click to browse; you can add more after selecting.</span>
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="raise-footer">
        <button type="button" class="sm-btn-cancel close-modal">Cancel</button>
        <button type="submit" class="sm-btn-primary" id="raiseSubmitBtn"><i class="bi bi-send-fill me-1"></i>Submit ticket</button>
      </div>
    </form>
  </div>
</div>

<div class="sm-modal" id="detailModal" role="dialog" aria-modal="true" aria-labelledby="detailModalTitle">
  <div class="sm-modal-box wide detail-ticket-box tk-modal-font">
    <div class="detail-head">
      <div class="detail-head-inner">
        <div class="detail-head-left">
          <h2 class="detail-head-title" id="detailModalTitle">
            <i class="bi bi-eye" aria-hidden="true"></i>
            <span>Ticket <span class="detail-head-ref" id="detail_ref"></span></span>
          </h2>
          <p class="detail-head-meta">
            <span id="detail_head_status" class="tk-head-status-pill" aria-live="polite"></span>
          </p>
        </div>
        <button type="button" class="sm-modal-close close-modal" aria-label="Close dialog">&times;</button>
      </div>
    </div>
    <input type="hidden" id="detail_id">
    <div class="detail-body tk-detail-body">
      <div class="tk-detail-cards">
        <section class="tk-detail-card" aria-labelledby="tk-detail-overview-heading">
          <h3 class="tk-detail-card-title" id="tk-detail-overview-heading">
            <i class="bi bi-layout-text-sidebar-reverse" aria-hidden="true"></i>
            Overview
          </h3>
          <div class="tk-meta-grid tk-meta-grid--dense">
            <div class="tk-meta-cell">
              <span class="tk-meta-label">Location</span>
              <div class="tk-meta-value" id="detail_location"></div>
            </div>
            <div class="tk-meta-cell">
              <span class="tk-meta-label">From → To</span>
              <div class="tk-meta-value" id="detail_depts"></div>
            </div>
            <div class="tk-meta-cell">
              <span class="tk-meta-label">Category</span>
              <div class="tk-meta-value" id="detail_category"></div>
            </div>
            <div class="tk-meta-cell">
              <span class="tk-meta-label">Priority</span>
              <div class="tk-meta-value" id="detail_priority"></div>
            </div>
            <div class="tk-meta-cell">
              <span class="tk-meta-label">Raised by</span>
              <div class="tk-meta-value" id="detail_raised_by"></div>
            </div>
            <div class="tk-meta-cell">
              <span class="tk-meta-label">Raised at</span>
              <div class="tk-meta-value" id="detail_raised_at"></div>
            </div>
            <div class="tk-meta-cell">
              <span class="tk-meta-label">Status updated by</span>
              <div class="tk-meta-value" id="detail_status_by"></div>
            </div>
            <div class="tk-meta-cell">
              <span class="tk-meta-label">Status updated at</span>
              <div class="tk-meta-value" id="detail_status_at"></div>
            </div>
            <div class="tk-meta-cell">
              <span class="tk-meta-label">Time to close</span>
              <div class="tk-meta-value tk-meta-empty" id="detail_time_to_close">—</div>
            </div>
          </div>
          <div class="tk-detail-prose-stack">
            <div class="tk-prose-block">
              <span class="tk-meta-label">Subject</span>
              <p class="tk-prose-subject" id="detail_subject"></p>
            </div>
            <div class="tk-prose-block tk-prose-block--desc">
              <span class="tk-meta-label">Description</span>
              <div class="tk-prose-desc" id="detail_description"></div>
            </div>
            <div id="detail_closed_note_wrap" class="tk-prose-block tk-prose-block--desc tk-closed-note-wrap is-empty">
              <span class="tk-meta-label">Resolution note</span>
              <div class="tk-prose-desc" id="detail_closed_note"></div>
            </div>
          </div>
        </section>

        <section class="tk-detail-card" aria-labelledby="tk-detail-files-heading">
          <h3 class="tk-detail-card-title" id="tk-detail-files-heading">
            <i class="bi bi-paperclip" aria-hidden="true"></i>
            Attachments
          </h3>
          <div id="detail_files" class="tk-detail-files"></div>
        </section>

        <section class="tk-detail-card" aria-labelledby="tk-detail-activity-heading">
          <h3 class="tk-detail-card-title" id="tk-detail-activity-heading">
            <i class="bi bi-clock-history" aria-hidden="true"></i>
            Activity
          </h3>
          <ul id="detail_timeline" class="tk-timeline" aria-live="polite">
            <li class="tk-timeline-loading">Loading activity…</li>
          </ul>
        </section>
      </div>
    </div>
    <div class="detail-footer-actions tk-detail-footer">
      <div class="tk-detail-footer-actions-row">
        <button type="button" class="sm-btn-cancel close-modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="sm-modal" id="quickStatusModal" role="dialog" aria-modal="true" aria-labelledby="quickStatusTitle">
  <div class="sm-modal-box tk-quick-status-box tk-modal-font">
    <div class="tk-quick-status-head">
      <h2 class="tk-quick-status-title" id="quickStatusTitle">
        <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
        <span>Update status — <span id="quick_status_ref"></span></span>
      </h2>
      <button type="button" class="tk-quick-status-close" id="btnQuickStatusCloseX" aria-label="Close dialog">&times;</button>
    </div>
    <div class="tk-quick-status-body">
      <input type="hidden" id="quick_status_ticket_id" value="">
      <div class="sm-form-group">
        <label for="quick_status_select">Status</label>
        <select id="quick_status_select" class="form-control">
          @foreach($statuses as $st)
            <option value="{{ $st }}">{{ ucwords(str_replace('_',' ', $st)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="sm-form-group">
        <label for="quick_status_note" id="quick_status_note_label">Solution <small class="tk-tk-muted">(optional)</small></label>
        <textarea id="quick_status_note" class="form-control" rows="3" placeholder="Describe the resolution when closing…" autocomplete="off"></textarea>
      </div>
    </div>
    <div class="tk-quick-status-footer">
      <button type="button" class="sm-btn-cancel" id="btnQuickStatusCancel">Cancel</button>
      <button type="button" class="sm-btn-primary" id="btnQuickStatusSave">Save status</button>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
(function () {
  const routes = {
    data: @json(route('superadmin.tickets.data')),
    store: @json(route('superadmin.tickets.store')),
    update: @json(route('superadmin.tickets.update')),
    categories: @json(route('superadmin.tickets.categories')),
    status: @json(route('superadmin.tickets.status')),
    timeline: @json(url('/superadmin/tickets')),
    export: @json(route('superadmin.tickets.export')),
    attachmentView: @json(route('superadmin.tickets.attachment')),
  };
  const csrf = $('meta[name="csrf-token"]').attr('content');
  const raiseFileHintDefault =
    'PDF, images, Word, Excel — up to 10 MB each. Click to browse; you can add more after selecting.';

  var TK_DEPT_ALL_LBL = 'All departments';
  var TK_STATUS_ALL_LBL = 'All statuses';

  var ticketPage = 1;
  var ticketPerPage = 15;

  function ticketParseDateRangeApi() {
    var v = ($('.tk-ticket-data-values').val() || '').trim();
    if (!v || v.indexOf(' to ') === -1) {
      return { from: '', to: '' };
    }
    var parts = v.split(' to ');
    var m1 = moment(parts[0].trim(), 'DD/MM/YYYY', true);
    var m2 = moment(parts[1].trim(), 'DD/MM/YYYY', true);
    if (!m1.isValid() || !m2.isValid()) {
      return { from: '', to: '' };
    }
    return { from: m1.format('YYYY-MM-DD'), to: m2.format('YYYY-MM-DD') };
  }

  function renderTicketFilterChips() {
    var html = '';
    var v = ($('.tk-ticket-data-values').val() || '').trim();
    if (v && v.indexOf(' to ') !== -1) {
      var label = $('#tkTicketDateLabel').text().trim();
      html +=
        '<span class="filter-badge remove-icon" data-tk-chip="date">' +
        $('<div>').text(label).html() +
        '</span>';
    }
    var deptHidden = ($('#departmentFilter').val() || '').trim();
    if (deptHidden) {
      var deptShown = ($('.tk-ticket-dept-wrap .tk-dept-search-input').val() || '').trim();
      if (!deptShown || deptShown === TK_DEPT_ALL_LBL) {
        deptShown = deptHidden;
      }
      html +=
        '<span class="filter-badge remove-icon" data-tk-chip="dept">' +
        $('<div>').text(deptShown).html() +
        '</span>';
    }
    var stHidden = ($('#statusFilter').val() || '').trim();
    if (stHidden) {
      var stShown = ($('.tk-ticket-status-wrap .tk-status-search-input').val() || '').trim();
      if (!stShown || stShown === TK_STATUS_ALL_LBL) {
        stShown = stHidden;
      }
      html +=
        '<span class="filter-badge remove-icon" data-tk-chip="status">' +
        $('<div>').text(stShown).html() +
        '</span>';
    }
    var q = ($('#ticketUniversalSearch').val() || '').trim();
    if (q !== '') {
      html +=
        '<span class="filter-badge remove-icon" data-tk-chip="search">' +
        $('<div>').text(q).html() +
        '</span>';
    }
    if (html) {
      html +=
        '<span class="filter-badge filter-clear" id="tkTicketClearAllChips">Clear all</span>';
    }
    $('#tkTicketFilterChips').html(html);
  }

  function initTicketDateRangePicker() {
    var $el = $('#tkTicketReportRange');
    if (!$el.length || typeof $.fn.daterangepicker !== 'function' || typeof moment === 'undefined') {
      return;
    }
    if ($el.data('daterangepicker')) {
      $el.data('daterangepicker').remove();
    }
    $el.daterangepicker(
      {
        startDate: moment(),
        endDate: moment(),
        autoUpdateInput: false,
        opens: 'right',
        alwaysShowCalendars: true,
        showDropdowns: true,
        parentEl: '.tk-tickets-page',
        locale: { cancelLabel: 'Clear', format: 'DD/MM/YYYY' },
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [
            moment().subtract(1, 'month').startOf('month'),
            moment().subtract(1, 'month').endOf('month'),
          ],
        },
      },
      function () {}
    );
    $el.off('apply.daterangepicker.tk cancel.daterangepicker.tk');
    $el.on('apply.daterangepicker.tk', function (ev, picker) {
      $('#tkTicketDateLabel').text(
        picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY')
      );
      $('.tk-ticket-data-values').val(
        picker.startDate.format('DD/MM/YYYY') + ' to ' + picker.endDate.format('DD/MM/YYYY')
      );
      fetchTickets({ resetPage: true });
      renderTicketFilterChips();
    });
    $el.on('cancel.daterangepicker.tk', function () {
      $('#tkTicketDateLabel').text('All Dates');
      $('.tk-ticket-data-values').val('');
      fetchTickets({ resetPage: true });
      renderTicketFilterChips();
    });
  }

  function formatFileSize(bytes) {
    if (bytes == null || bytes === 0) return '0 B';
    var u = ['B', 'KB', 'MB', 'GB'];
    var i = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), u.length - 1);
    return (bytes / Math.pow(1024, i)).toFixed(i > 0 ? 1 : 0) + ' ' + u[i];
  }

  function ticketAttachmentIsOfficeDoc(name) {
    return /\.(doc|docx|xls|xlsx)$/i.test(String(name || ''));
  }

  function fileIconSpec(fileName) {
    var ext = (String(fileName).split('.').pop() || '').toLowerCase();
    if (['png', 'jpg', 'jpeg', 'gif', 'webp'].indexOf(ext) >= 0) {
      return { mod: 'tk-ic-img', icon: 'bi-file-earmark-image' };
    }
    if (ext === 'pdf') {
      return { mod: '', icon: 'bi-file-earmark-pdf' };
    }
    if (['doc', 'docx'].indexOf(ext) >= 0) {
      return { mod: 'tk-ic-doc', icon: 'bi-file-earmark-word' };
    }
    if (['xls', 'xlsx'].indexOf(ext) >= 0) {
      return { mod: 'tk-ic-doc', icon: 'bi-file-earmark-excel' };
    }
    return { mod: '', icon: 'bi-file-earmark' };
  }

  function renderRaiseAttachments() {
    var el = document.getElementById('raise_attachments');
    if (!el) return;
    var $card = $('#raise_file_list_card');
    var $list = $('#raise_file_list');
    var $hint = $('#raise_file_hint');
    var $zone = $('#raise_file_zone');
    var $title = $('#raise_file_zone_title');
    var $badge = $('#raise_file_count_badge');

    $list.empty();
    if (!el.files || !el.files.length) {
      $card.removeClass('is-visible');
      $zone.removeClass('tk-has-files');
      $hint.text(raiseFileHintDefault);
      $title.text('Add files');
      $badge.text('0');
      return;
    }

    var n = el.files.length;
    $card.addClass('is-visible');
    $zone.addClass('tk-has-files');
    $title.text('Add more files');
    $hint.text(
      'Selected ' +
        n +
        ' file' +
        (n === 1 ? '' : 's') +
        '. Add more below or remove items from the list.'
    );
    $badge.text(String(n));

    for (var i = 0; i < n; i++) {
      var f = el.files[i];
      var spec = fileIconSpec(f.name);
      var mod = spec.mod ? ' ' + spec.mod : '';
      var nameEsc = $('<div>').text(f.name).html();
      var sizeStr = formatFileSize(f.size);
      $list.append(
        '<li class="tk-file-item">' +
          '<span class="tk-file-item-ic' +
          mod +
          '"><i class="bi ' +
          spec.icon +
          '" aria-hidden="true"></i></span>' +
          '<span class="tk-file-item-main">' +
          '<span class="tk-file-item-name">' +
          nameEsc +
          '</span>' +
          '<span class="tk-file-item-meta">' +
          $('<div>').text(sizeStr).html() +
          '</span>' +
          '</span>' +
          '<button type="button" class="tk-file-remove" data-index="' +
          i +
          '" title="Remove file" aria-label="Remove file">' +
          '<i class="bi bi-x-lg" aria-hidden="true"></i></button>' +
          '</li>'
      );
    }
  }

  function openModal(sel) {
    $(sel).addClass('show');
    $('#modalOverlay').addClass('show');
    $('body').css('overflow','hidden');
  }
  function resetRaiseModalChrome() {
    $('#raise_ticket_id').val('');
    $('#raiseModalTitle').text('Raise ticket');
    $('#raiseSubmitBtn').html('<i class="bi bi-send-fill me-1"></i>Submit ticket');
    $('#raise_edit_hint').hide();
  }

  function closeModal() {
    var wasRaise = $('#raiseModal').hasClass('show');
    $('.sm-modal').removeClass('show');
    $('#modalOverlay').removeClass('show');
    $('body').css('overflow', 'auto');
    if (wasRaise) {
      resetRaiseModalChrome();
    }
  }

  function closeQuickStatusModalOnly() {
    $('#quickStatusModal').removeClass('show');
    if (!$('.sm-modal.show').length) {
      $('#modalOverlay').removeClass('show');
      $('body').css('overflow', 'auto');
    }
  }

  function priorityClass(p) {
    return 'tk-pill tk-p-' + (p || 'medium');
  }

  function statusRowClass(st) {
    var s = (st || '').replace(/_/g, '-');
    if (!s) return 'tk-row-status';
    return 'tk-row-status tk-rs-' + s;
  }

  function statusRowLabel(st) {
    if (!st) return '';
    var spaced = String(st).replace(/_/g, ' ');
    return spaced.replace(/\b[a-z]/g, function (ch) { return ch.toUpperCase(); });
  }

  function tkEscapeHtml(s) {
    return $('<div>').text(s == null ? '' : String(s)).html();
  }

  function setDetailTimeToClose(raw) {
    var v = (raw || '').trim();
    var $el = $('#detail_time_to_close');
    if (!v) {
      $el.text('—').addClass('tk-meta-empty');
    } else {
      $el.text(v).removeClass('tk-meta-empty');
    }
  }

  function setDetailClosedNote(raw) {
    var v = (raw || '').trim();
    var $wrap = $('#detail_closed_note_wrap');
    var $el = $('#detail_closed_note');
    if (!v) {
      $el.text('');
      $wrap.addClass('is-empty');
    } else {
      $el.text(v);
      $wrap.removeClass('is-empty');
    }
  }

  function applyStatusSuccessToTicketCache(tid, res) {
    if (!ticketById[tid] || !res.ticket) return;
    ticketById[tid].status = res.ticket.status;
    ticketById[tid].status_updated_by_name = res.ticket.status_updated_by_name;
    ticketById[tid].status_updated_at = res.ticket.status_updated_at;
    if (typeof res.ticket.time_to_close !== 'undefined') {
      ticketById[tid].time_to_close = res.ticket.time_to_close;
    }
    if (typeof res.ticket.sla_vs_actual !== 'undefined') {
      ticketById[tid].sla_vs_actual = res.ticket.sla_vs_actual;
    }
    if (typeof res.ticket.sla_vs_actual_kind !== 'undefined') {
      ticketById[tid].sla_vs_actual_kind = res.ticket.sla_vs_actual_kind;
    }
    if (typeof res.ticket.closed_status_note !== 'undefined') {
      ticketById[tid].closed_status_note = res.ticket.closed_status_note;
    }
    if (typeof ticketById[tid].is_creator !== 'undefined') {
      ticketById[tid].can_edit =
        !!ticketById[tid].is_creator && String(ticketById[tid].status || '') === 'open';
    }
  }

  function syncDetailModalIfOpenForTicket(tid, res) {
    if (String($('#detail_id').val()) !== String(tid) || !res.ticket) return;
    $('#detail_head_status').text(statusRowLabel(res.ticket.status));
    $('#detail_status_by').text(res.ticket.status_updated_by_name || '—');
    $('#detail_status_at').text(res.ticket.status_updated_at || '—');
    if (typeof res.ticket.time_to_close !== 'undefined') {
      setDetailTimeToClose(res.ticket.time_to_close);
    }
    if (typeof res.ticket.closed_status_note !== 'undefined') {
      setDetailClosedNote(res.ticket.closed_status_note);
    }
  }

  function syncQuickStatusSolutionLabel() {
    var st = String($('#quick_status_select').val() || '');
    var $lbl = $('#quick_status_note_label');
    var $ta = $('#quick_status_note');
    if (st === 'closed') {
      $lbl.html(
        'Solution <span class="text-danger" title="Required">*</span> <small class="tk-tk-muted">(required)</small>'
      );
      $ta.attr('aria-required', 'true');
    } else {
      $lbl.html('Solution <small class="tk-tk-muted">(optional)</small>');
      $ta.removeAttr('aria-required');
    }
  }

  function submitTicketStatusUpdate(ticketId, status, note, options) {
    options = options || {};
    var idStr = String(ticketId);
    var st = String(status || '');
    var n = note != null ? String(note).trim() : '';
    if (st === 'closed' && n === '') {
      toastr.warning('Enter a solution note when closing the ticket.');
      $('#quick_status_note').focus();
      return;
    }
    $.ajax({
      url: routes.status,
      type: 'POST',
      data: { _token: csrf, id: idStr, status: status, note: note },
      success: function (res) {
        if (res.success) {
          var tid = parseInt(idStr, 10);
          applyStatusSuccessToTicketCache(tid, res);
          syncDetailModalIfOpenForTicket(tid, res);
          $('#quick_status_note').val('');
          toastr.success(res.message || 'Updated');
          if ($('#detailModal').hasClass('show') && String($('#detail_id').val()) === idStr) {
            loadTicketTimeline(tid);
          }
          fetchTickets();
          if (options.closeQuickModal) {
            closeQuickStatusModalOnly();
          }
        } else {
          toastr.error(res.message || 'Error');
        }
      },
      error: function (xhr) {
        var j = xhr.responseJSON;
        if (j && j.errors && j.errors.note && j.errors.note.length) {
          toastr.error(j.errors.note[0]);
          return;
        }
        if (j && j.message) {
          toastr.error(j.message);
        } else {
          toastr.error('Update failed');
        }
      }
    });
  }

  function renderTicketTimeline(items) {
    var $ul = $('#detail_timeline');
    $ul.empty();
    if (!items || !items.length) {
      $ul.append('<li class="tk-timeline-empty">No activity yet.</li>');
      return;
    }
    items.forEach(function (it) {
      if (it.type === 'status') {
        var badge = it.synthetic ? 'Raised' : 'Status';
        var meta =
          '<span class="tk-tl-who">' +
          tkEscapeHtml(it.user_name || '—') +
          '</span><time>' +
          tkEscapeHtml(it.created_at || '') +
          '</time><span class="tk-tl-badge tk-tl-badge--status">' +
          tkEscapeHtml(badge) +
          '</span>';
        var changeLine = '';
        if (it.synthetic) {
          changeLine =
            '<div class="tk-timeline-status-change">Ticket raised</div>';
        } else {
          var fromL = it.from_status ? statusRowLabel(it.from_status) : '—';
          var toL = statusRowLabel(it.to_status || '');
          changeLine =
            '<div class="tk-timeline-status-change">' +
            tkEscapeHtml(fromL) +
            ' → ' +
            '<span class="' +
            statusRowClass(it.to_status) +
            '">' +
            tkEscapeHtml(toL) +
            '</span></div>';
        }
        var noteBlock = '';
        if (it.note) {
          noteBlock =
            '<div class="tk-timeline-note"><strong>Note:</strong> ' +
            tkEscapeHtml(it.note) +
            '</div>';
        }
        $ul.append(
          '<li class="tk-timeline-item">' +
            '<span class="tk-timeline-dot" aria-hidden="true"></span>' +
            '<div class="tk-timeline-card">' +
            '<div class="tk-timeline-meta">' +
            meta +
            '</div>' +
            changeLine +
            noteBlock +
            '</div></li>'
        );
      }
    });
  }

  function loadTicketTimeline(ticketId) {
    var $ul = $('#detail_timeline');
    $ul.html('<li class="tk-timeline-loading">Loading activity…</li>');
    $.get(routes.timeline + '/' + ticketId + '/timeline', function (res) {
      if (res.success) {
        renderTicketTimeline(res.items || []);
      } else {
        $ul.html('<li class="tk-timeline-empty">Could not load timeline.</li>');
      }
    }).fail(function () {
      $ul.html('<li class="tk-timeline-empty">Could not load timeline.</li>');
    });
  }

  function priorityLabel(p) {
    if (!p) return '';
    var s = String(p);
    return s.charAt(0).toUpperCase() + s.slice(1).toLowerCase();
  }

  /** Single-line “name · datetime” for table cells (no wrap). */
  function closedByOneLine(name, at) {
    var ne = name ? String(name).trim() : '';
    var ae = at ? String(at).trim() : '';
    var neH = ne ? $('<div>').text(ne).html() : '';
    var aeH = ae ? $('<div>').text(ae).html() : '';
    if (!ne && !ae) {
      return '<span class="tk-tk-muted">—</span>';
    }
    if (!ne) {
      return '<span class="tk-td-closedby-inline">' + aeH + '</span>';
    }
    if (!ae) {
      return '<span class="tk-td-closedby-inline">' + neH + '</span>';
    }
    return (
      '<span class="tk-td-closedby-inline">' +
      neH +
      '<span class="tk-tk-sep">·</span>' +
      aeH +
      '</span>'
    );
  }

  function renderStats(stats, pagination) {
    if (!stats) return;
    $('#stat-total').text(stats.total != null ? stats.total : '—');
    var bs = stats.by_status || {};
    $('#stat-open').text(bs.open != null ? bs.open : 0);
    $('#stat-in_progress').text(bs.in_progress != null ? bs.in_progress : 0);
    $('#stat-closed').text(bs.closed != null ? bs.closed : 0);
    $('#stat-cancelled').text(bs.cancelled != null ? bs.cancelled : 0);
    if (pagination && pagination.total != null) {
      var t = pagination.total;
      if (t === 0) {
        $('#stat-showing-range').text('0–0');
        $('#stat-filtered-total').text('0');
      } else {
        var a = pagination.from;
        var b = pagination.to;
        $('#stat-showing-range').text(
          (a != null ? a : '—') + '–' + (b != null ? b : '—')
        );
        $('#stat-filtered-total').text(String(t));
      }
    } else {
      $('#stat-showing-range').text('—');
      $('#stat-filtered-total').text('—');
    }
  }

  function buildPaginationWindow(cur, last) {
    var out = [];
    if (last < 1) {
      return out;
    }
    if (last <= 9) {
      var k;
      for (k = 1; k <= last; k++) {
        out.push(k);
      }
      return out;
    }
    function pushEllipsis() {
      if (!out.length) {
        return;
      }
      if (out[out.length - 1] === 'ellipsis') {
        return;
      }
      out.push('ellipsis');
    }
    out.push(1);
    var start;
    var end;
    if (cur <= 3) {
      start = 2;
      end = Math.min(3, last - 1);
    } else if (cur >= last - 2) {
      start = Math.max(2, last - 2);
      end = last - 1;
    } else {
      start = cur - 1;
      end = cur + 1;
    }
    if (start > 2) {
      pushEllipsis();
    }
    var j;
    for (j = start; j <= end; j++) {
      out.push(j);
    }
    if (end < last - 1) {
      pushEllipsis();
    }
    if (last > 1) {
      out.push(last);
    }
    return out;
  }

  function renderPagination(p) {
    var $nums = $('#tkPageNumbers');
    $nums.empty();
    if (!p) {
      $('#tkPagePrev, #tkPageNext').prop('disabled', true);
      $('#tkPageCurDisp').text('—');
      $('#tkPageLastDisp').text('—');
      return;
    }
    var cur = p.current_page != null ? p.current_page : 1;
    var last = p.last_page != null ? p.last_page : 1;
    var total = p.total != null ? p.total : 0;
    $('#tkPageCurDisp').text(String(cur));
    $('#tkPageLastDisp').text(String(last));
    var dis = total === 0;
    $('#tkPagePrev').prop('disabled', dis || cur <= 1);
    $('#tkPageNext').prop('disabled', dis || cur >= last);
    if (last < 1) {
      return;
    }
    if (dis) {
      $nums.append(
        '<button type="button" class="tk-page-num is-active" disabled aria-current="page" aria-label="Page 1">1</button>'
      );
      return;
    }
    var items = buildPaginationWindow(cur, last);
    var idx;
    for (idx = 0; idx < items.length; idx++) {
      var item = items[idx];
      if (item === 'ellipsis') {
        $nums.append('<span class="tk-page-ellipsis" aria-hidden="true">\u2026</span>');
      } else {
        var n = item;
        var active = n === cur;
        var btn =
          '<button type="button" role="listitem" class="tk-page-num' +
          (active ? ' is-active' : '') +
          '" data-page="' +
          n +
          '"' +
          (active ? ' disabled aria-current="page"' : '') +
          ' aria-label="Page ' +
          n +
          '">' +
          n +
          '</button>';
        $nums.append(btn);
      }
    }
  }

  function syncStatCards() {
    var raw = ($('#statusFilter').val() || '').trim();
    var parts = raw
      ? raw
          .split(',')
          .map(function (s) {
            return s.trim();
          })
          .filter(Boolean)
      : [];
    $('.tk-stat-card').removeClass('tk-stat-active').attr('aria-pressed', 'false');
    $('.tk-stat-card').each(function () {
      var ds = $(this).attr('data-status');
      if (typeof ds === 'undefined' || ds === null) ds = '';
      if (parts.length === 0 && ds === '') {
        $(this).addClass('tk-stat-active').attr('aria-pressed', 'true');
      } else if (parts.length === 1 && ds === parts[0]) {
        $(this).addClass('tk-stat-active').attr('aria-pressed', 'true');
      }
    });
  }

  function loadCategories(deptId, done) {
    const $cat = $('#raise_category_id');
    $cat.prop('disabled', true).html('<option value="">Loading…</option>');
    if (!deptId) {
      $cat.html('<option value="">Choose “To department” first</option>');
      if (typeof done === 'function') {
        done();
      }
      return;
    }
    $.get(routes.categories, { department_id: deptId }, function (res) {
      if (!res.success) {
        $cat.html('<option value="">No categories</option>');
        if (typeof done === 'function') {
          done();
        }
        return;
      }
      let html = '<option value="">Select category</option>';
      (res.categories || []).forEach(function (c) {
        html += '<option value="' + c.id + '">' + $('<div>').text(c.name).html() + '</option>';
      });
      $cat.html(html).prop('disabled', false);
      if (typeof done === 'function') {
        done();
      }
    }).fail(function () {
      $cat.html('<option value="">Failed to load</option>');
      if (typeof done === 'function') {
        done();
      }
    });
  }

  let ticketById = {};

  function renderTable(rows) {
    const $tb = $('#ticketsBody');
    $tb.empty();
    ticketById = {};
    (rows || []).forEach(function (t) { ticketById[t.id] = t; });
    if (!rows.length) {
      $tb.append('<tr class="qdt-row"><td colspan="14" class="text-center py-5 tk-tk-empty">No tickets match your filters.</td></tr>');
      return;
    }
    rows.forEach(function (t) {
      const pr = '<span class="' + priorityClass(t.priority) + '">' + $('<div>').text(priorityLabel(t.priority)).html() + '</span>';
      const stRaw = statusRowLabel(t.status);
      const st = '<span class="' + statusRowClass(t.status) + '">' + $('<div>').text(stRaw).html() + '</span>';
      const btn =
        '<button type="button" class="tk-btn-view btn-view" data-id="' +
        t.id +
        '"><i class="bi bi-eye" aria-hidden="true"></i> View</button>';
      const statusBtn = t.can_update_status
        ? '<button type="button" class="tk-btn-status btn-quick-status" data-id="' +
          t.id +
          '" title="Update status"><i class="bi bi-arrow-repeat" aria-hidden="true"></i> Update Status</button>'
        : '';
      const editBtn = t.can_edit
        ? '<button type="button" class="tk-btn-edit-ticket btn-edit-ticket" data-id="' +
          t.id +
          '" title="Edit ticket (open only)"><i class="bi bi-pencil" aria-hidden="true"></i> Edit</button>'
        : '';
      const raisedCell = '<div class="tk-tk-stack-primary">' + $('<div>').text(t.created_by_name || '').html() + '</div>';
      const isClosed = String(t.status || '') === 'closed';
      const statusByCell = isClosed
        ? '<div class="tk-tk-stack-primary">' + $('<div>').text(t.status_updated_by_name || '—').html() + '</div>'
        : '<div class="tk-tk-stack-primary"><span class="tk-tk-muted">—</span></div>';
      const statusAtCell = isClosed
        ? '<div class="tk-tk-stack-primary">' + $('<div>').text(t.status_updated_at || '—').html() + '</div>'
        : '<div class="tk-tk-stack-primary"><span class="tk-tk-muted">—</span></div>';

      const ttc = (t.time_to_close || '').trim();
      const ttcCell = ttc
        ? '<span class="tk-tk-ttc">' + $('<div>').text(ttc).html() + '</span>'
        : '<span class="tk-tk-ttc tk-tk-ttc-empty">—</span>';
      const slaKind = String(t.sla_vs_actual_kind || 'na').replace(/[^a-z_]/g, '') || 'na';
      const slaTxt = t.sla_vs_actual != null && String(t.sla_vs_actual).trim() !== '' ? String(t.sla_vs_actual) : '—';
      const slaCell =
        '<span class="tk-tk-sla tk-sla-' + slaKind + '">' + $('<div>').text(slaTxt).html() + '</span>';
      $tb.append(
        '<tr class="qdt-row">' +
        '<td><span class="tk-tk-ref">' + $('<div>').text(t.ticket_no).html() + '</span></td>' +
        '<td class="tk-td-datetime">' + $('<div>').text(t.created_at).html() + '</td>' +
        '<td>' + $('<div>').text(t.location_name).html() + '</td>' +
        '<td><span class="tk-tk-route">' + $('<div>').text(t.from_department_name || '').html() + '</span></td>' +
        '<td><span class="tk-tk-route">' + $('<div>').text(t.to_department_name || '').html() + '</span></td>' +
            '<td>' + $('<div>').text(t.category_name).html() + '</td>' +
        '<td>' + pr + '</td>' +
        '<td>' + st + '</td>' +
        '<td>' + raisedCell + '</td>' +
        '<td>' + statusByCell + '</td>' +
        '<td>' + statusAtCell + '</td>' +
        '<td>' + ttcCell + '</td>' +
        '<td>' + slaCell + '</td>' +
        '<td class="text-center tk-action-cell">' +
        btn +
        editBtn +
        statusBtn +
        '</td>' +
        '</tr>'
      );
    });
  }

  function getTicketScope() {
    var $el = $('#scopeFilter');
    if (!$el.length) {
      return 'all';
    }
    var v = $el.val();
    if (v === null || v === undefined || v === '') {
      return 'all';
    }
    return v;
  }

  function syncTicketDeptFilterUi() {
    var raw = String($('#departmentFilter').val() || '').trim();
    var ids = raw
      ? raw
          .split(',')
          .map(function (s) {
            return s.trim();
          })
          .filter(Boolean)
      : [];
    var $wrap = $('.tk-tickets-page .tk-ticket-dept-wrap');
    if (!$wrap.length) return;
    var $list = $wrap.find('.tk-ticket-dept-list');
    $list.children('div').removeClass('selected');
    if (!ids.length) {
      $wrap.find('.tk-dept-search-input').val(TK_DEPT_ALL_LBL);
      return;
    }
    var labels = [];
    ids.forEach(function (id) {
      var $item = $list.children('div').filter(function () {
        return String($(this).attr('data-id')) === String(id);
      });
      if ($item.length) {
        $item.first().addClass('selected');
        labels.push($item.first().text().trim());
      }
    });
    $wrap.find('.tk-dept-search-input').val(labels.length ? labels.join(', ') : TK_DEPT_ALL_LBL);
  }

  function syncTicketStatusFilterUi() {
    var raw = String($('#statusFilter').val() || '').trim();
    var parts = raw
      ? raw
          .split(',')
          .map(function (s) {
            return s.trim();
          })
          .filter(Boolean)
      : [];
    var $wrap = $('.tk-tickets-page .tk-ticket-status-wrap');
    if (!$wrap.length) return;
    var $list = $wrap.find('.tk-ticket-status-list');
    $list.children('div').removeClass('selected');
    if (!parts.length) {
      $wrap.find('.tk-status-search-input').val(TK_STATUS_ALL_LBL);
      return;
    }
    var labels = [];
    parts.forEach(function (st) {
      var $item = $list.children('div').filter(function () {
        return String($(this).attr('data-id')) === String(st);
      });
      if ($item.length) {
        $item.first().addClass('selected');
        labels.push($item.first().text().trim());
      }
    });
    $wrap.find('.tk-status-search-input').val(labels.length ? labels.join(', ') : TK_STATUS_ALL_LBL);
  }

  /** Read selection from body-cloned menu, sync hidden + visible, reload grid (do not rely on .trigger('change') alone). */
  function ticketFilterUpdateMultiSelection($dropdown) {
    var $wrapper = $dropdown.data('wrapper');
    if (!$wrapper || !$wrapper.length) {
      return;
    }
    var selectedItems = [];
    var selectedIds = [];
    $dropdown
      .find('.tk-ticket-dept-list > div.selected, .tk-ticket-status-list > div.selected')
      .each(function () {
        selectedItems.push($(this).text().trim());
        var rid = $(this).attr('data-id');
        if (rid !== undefined && rid !== null && String(rid) !== '') {
          selectedIds.push(String(rid));
        }
      });
    var $visible = $wrapper.find('.dropdown-search-input');
    var idsCsv = selectedIds.join(',');
    if ($wrapper.hasClass('tk-ticket-dept-wrap')) {
      $visible.val(selectedItems.length ? selectedItems.join(', ') : TK_DEPT_ALL_LBL);
      $('#departmentFilter').val(idsCsv);
      syncTicketDeptFilterUi();
      renderTicketFilterChips();
      fetchTickets({ resetPage: true });
    } else if ($wrapper.hasClass('tk-ticket-status-wrap')) {
      $visible.val(selectedItems.length ? selectedItems.join(', ') : TK_STATUS_ALL_LBL);
      $('#statusFilter').val(idsCsv);
      syncTicketStatusFilterUi();
      syncStatCards();
      renderTicketFilterChips();
      fetchTickets({ resetPage: true });
    }
  }

  /* Ticket filters: same flow as quotation/pettycash dashboard (clone to body, absolute+offset, document delegates, menu stopPropagation) */
  function closeTicketFilterDropdowns() {
    $('.dropdown-menu.tax-dropdown.tk-ticket-filter-dd').removeClass('show').hide();
  }

  function ticketFilterSyncCloneSelection($dropdown) {
    var $w = $dropdown.data('wrapper');
    if (!$w || !$w.length) return;
    if ($w.hasClass('tk-ticket-dept-wrap')) {
      var raw = String($('#departmentFilter').val() || '').trim();
      var ids = raw
        ? raw
            .split(',')
            .map(function (s) {
              return s.trim();
            })
            .filter(Boolean)
        : [];
      $dropdown.find('.tk-ticket-dept-list').children('div').removeClass('selected');
      ids.forEach(function (id) {
        $dropdown.find('.tk-ticket-dept-list').children('div').each(function () {
          if (String($(this).attr('data-id')) === String(id)) {
            $(this).addClass('selected');
          }
        });
      });
    } else if ($w.hasClass('tk-ticket-status-wrap')) {
      var rawSt = String($('#statusFilter').val() || '').trim();
      var sts = rawSt
        ? rawSt
            .split(',')
            .map(function (s) {
              return s.trim();
            })
            .filter(Boolean)
        : [];
      $dropdown.find('.tk-ticket-status-list').children('div').removeClass('selected');
      sts.forEach(function (st) {
        $dropdown.find('.tk-ticket-status-list').children('div').each(function () {
          if (String($(this).attr('data-id')) === String(st)) {
            $(this).addClass('selected');
          }
        });
      });
    }
  }

  function ticketFilterDdCleanup() {
    $('body > .dropdown-menu.tax-dropdown.tk-ticket-filter-dd').remove();
    $('.tk-tickets-page .tk-ticket-filter-tax .dropdown-search-input').removeData('tkTicketFilterDropdown');
  }
  ticketFilterDdCleanup();

  $(document).on('click.tkTkFilter', '.tk-tickets-page .tk-ticket-filter-tax .dropdown-search-input', function (e) {
    e.stopPropagation();
    $('.dropdown-menu.tax-dropdown.tk-ticket-filter-dd').hide();

    var $input = $(this);
    var $dropdown = $input.data('tkTicketFilterDropdown');
    if (!$dropdown || !$dropdown.length) {
      $dropdown = $input.siblings('.dropdown-menu').clone(true);
      $('body').append($dropdown);
      $input.data('tkTicketFilterDropdown', $dropdown);
    }

    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));

    var offset = $input.offset();
    $dropdown.css({
      position: 'absolute',
      top: offset.top + $input.outerHeight(),
      left: offset.left,
      width: $input.outerWidth(),
      zIndex: 10050
    });
    $dropdown.addClass('show').show();

    $dropdown.find('.inner-search').val('');
    $dropdown.find('.dropdown-list div').show();
    ticketFilterSyncCloneSelection($dropdown);
    $dropdown.find('.inner-search').trigger('focus');
  });

  $(document).on('click.tkTkFilter', '.dropdown-menu.tk-ticket-filter-dd .tk-ticket-dept-list > div', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $row = $(this);
    var $dd = $row.closest('.dropdown-menu');
    $row.toggleClass('selected');
    ticketFilterUpdateMultiSelection($dd);
  });

  $(document).on('click.tkTkFilter', '.dropdown-menu.tk-ticket-filter-dd .tk-ticket-status-list > div', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $row = $(this);
    var $dd = $row.closest('.dropdown-menu');
    $row.toggleClass('selected');
    ticketFilterUpdateMultiSelection($dd);
  });

  $(document).on('click.tkTkFilter', '.dropdown-menu.tk-ticket-filter-dd .select-all', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $dd = $(this).closest('.dropdown-menu');
    var $w = $dd.data('wrapper');
    if (!$w || !$w.length) return;
    if ($w.hasClass('tk-ticket-dept-wrap')) {
      $dd.find('.tk-ticket-dept-list').children('div').addClass('selected');
    } else if ($w.hasClass('tk-ticket-status-wrap')) {
      $dd.find('.tk-ticket-status-list').children('div').addClass('selected');
    }
    ticketFilterUpdateMultiSelection($dd);
  });

  $(document).on('click.tkTkFilter', '.dropdown-menu.tk-ticket-filter-dd .deselect-all', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $dd = $(this).closest('.dropdown-menu');
    var $w = $dd.data('wrapper');
    if (!$w || !$w.length) return;
    if ($w.hasClass('tk-ticket-dept-wrap')) {
      $dd.find('.tk-ticket-dept-list').children('div').removeClass('selected');
    } else if ($w.hasClass('tk-ticket-status-wrap')) {
      $dd.find('.tk-ticket-status-list').children('div').removeClass('selected');
    }
    ticketFilterUpdateMultiSelection($dd);
  });

  $(document).on('keyup.tkTkFilter', '.dropdown-menu.tk-ticket-filter-dd .inner-search', function () {
    var q = $(this).val().toLowerCase();
    $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
    });
  });

  $(document).on('click.tkTkFilter', function (e) {
    if (
      !$(e.target).closest('.tk-tickets-page .tax-dropdown-wrapper.tk-ticket-filter-tax').length &&
      !$(e.target).closest('.dropdown-menu.tax-dropdown.tk-ticket-filter-dd').length
    ) {
      closeTicketFilterDropdowns();
    }
  });

  function closeTicketFilterOnScroll() {
    if ($('body > .dropdown-menu.tax-dropdown.tk-ticket-filter-dd:visible').length) {
      closeTicketFilterDropdowns();
    }
  }
  $(window).on('scroll.tkTkFilter resize.tkTkFilter', closeTicketFilterOnScroll);
  $('.pc-content, .pc-container').on('scroll.tkTkFilter', closeTicketFilterOnScroll);

  function fetchTickets(opts) {
    opts = opts || {};
    if (opts.resetPage) {
      ticketPage = 1;
    } else if (opts.page != null) {
      ticketPage = Math.max(1, parseInt(opts.page, 10) || 1);
    }
    ticketPerPage = parseInt($('#ticketPerPage').val(), 10) || 15;

    var statusParam =
      Object.prototype.hasOwnProperty.call(opts, 'statusOverride') && opts.statusOverride !== undefined
        ? opts.statusOverride
        : ($('#statusFilter').val() || '');

    var dr = ticketParseDateRangeApi();
    $('#ticketsBody').html('<tr class="qdt-row"><td colspan="14" class="text-center py-5 tk-tk-empty">Loading tickets…</td></tr>');
    $.get(routes.data, {
      scope: getTicketScope(),
      status: statusParam,
      to_department_id: $('#departmentFilter').val() || '',
      date_from: dr.from,
      date_to: dr.to,
      universal_search: ($('#ticketUniversalSearch').val() || '').trim(),
      page: ticketPage,
      per_page: ticketPerPage
    }, function (res) {
      if (res.success) {
        if (res.pagination && res.pagination.current_page) {
          ticketPage = res.pagination.current_page;
        }
        renderTable(res.tickets || []);
        renderStats(res.stats, res.pagination);
        renderPagination(res.pagination);
        syncStatCards();
        renderTicketFilterChips();
      } else {
        toastr.error('Could not load tickets');
      }
    }).fail(function () { toastr.error('Could not load tickets'); });
  }

  var ticketSearchTimer = null;
  $('#btnRefresh').on('click', function () { fetchTickets(); });
  $('#btnExportTickets').on('click', function () {
    var dr = ticketParseDateRangeApi();
    var qs = $.param({
      scope: getTicketScope(),
      status: $('#statusFilter').val() || '',
      to_department_id: $('#departmentFilter').val() || '',
      date_from: dr.from,
      date_to: dr.to,
      universal_search: ($('#ticketUniversalSearch').val() || '').trim(),
    });
    window.location.href = routes.export + (qs ? '?' + qs : '');
  });
  $('#btnClearFilters').on('click', function () {
    $('#tkTicketDateLabel').text('All Dates');
    $('.tk-ticket-data-values').val('');
    $('#ticketUniversalSearch').val('');
    $('#departmentFilter').val('');
    $('#statusFilter').val('');
    syncTicketDeptFilterUi();
    syncTicketStatusFilterUi();
    var drp = $('#tkTicketReportRange').data('daterangepicker');
    if (drp) {
      drp.setStartDate(moment());
      drp.setEndDate(moment());
    }
    syncStatCards();
    fetchTickets({ resetPage: true });
    renderTicketFilterChips();
  });
  $('#departmentFilter').on('change', function () {
    syncTicketDeptFilterUi();
    renderTicketFilterChips();
    fetchTickets({ resetPage: true });
  });
  $('#ticketUniversalSearch').on('input', function () {
    clearTimeout(ticketSearchTimer);
    ticketSearchTimer = setTimeout(function () {
      fetchTickets({ resetPage: true });
    }, 450);
  });
  $('#tkTicketFilterChips').on('click', '.filter-badge.remove-icon[data-tk-chip]', function () {
    var t = $(this).attr('data-tk-chip');
    if (t === 'date') {
      $('#tkTicketDateLabel').text('All Dates');
      $('.tk-ticket-data-values').val('');
      var drp = $('#tkTicketReportRange').data('daterangepicker');
      if (drp) {
        drp.setStartDate(moment());
        drp.setEndDate(moment());
      }
    } else if (t === 'search') {
      $('#ticketUniversalSearch').val('');
    } else if (t === 'dept') {
      $('#departmentFilter').val('');
      syncTicketDeptFilterUi();
    } else if (t === 'status') {
      $('#statusFilter').val('');
      syncTicketStatusFilterUi();
      syncStatCards();
    }
    renderTicketFilterChips();
    fetchTickets({ resetPage: true });
  });
  $('#tkTicketFilterChips').on('click', '#tkTicketClearAllChips', function () {
    $('#tkTicketDateLabel').text('All Dates');
    $('.tk-ticket-data-values').val('');
    $('#ticketUniversalSearch').val('');
    $('#departmentFilter').val('');
    $('#statusFilter').val('');
    syncTicketDeptFilterUi();
    syncTicketStatusFilterUi();
    var drp = $('#tkTicketReportRange').data('daterangepicker');
    if (drp) {
      drp.setStartDate(moment());
      drp.setEndDate(moment());
    }
    syncStatCards();
    fetchTickets({ resetPage: true });
  });
  $('#scopeFilter').on('change', function () {
    fetchTickets({ resetPage: true });
  });
  $('#statusFilter').on('change', function () {
    syncTicketStatusFilterUi();
    syncStatCards();
    renderTicketFilterChips();
    fetchTickets({ resetPage: true });
  });
  $('#ticketPerPage').on('change', function () {
    fetchTickets({ resetPage: true });
  });
  $('#tkPagePrev').on('click', function () {
    fetchTickets({ page: ticketPage - 1 });
  });
  $('#tkPageNext').on('click', function () {
    fetchTickets({ page: ticketPage + 1 });
  });
  $(document).on('click', '.tk-page-num:not(.is-active)', function () {
    var pg = parseInt($(this).data('page'), 10);
    if (!isNaN(pg)) {
      fetchTickets({ page: pg });
    }
  });
  $(document).on('click', '.tk-stat-card', function (e) {
    e.preventDefault();
    var s = $(this).attr('data-status');
    if (typeof s === 'undefined' || s === null) {
      s = '';
    }
    $('#statusFilter').val(s);
    syncTicketStatusFilterUi();
    syncStatCards();
    renderTicketFilterChips();
    fetchTickets({ resetPage: true, statusOverride: s });
  });

  $('#raise_to_dept').on('change', function () {
    loadCategories($(this).val());
  });

  $('#raise_attachments').on('change', function () {
    renderRaiseAttachments();
  });

  $('#raise_file_pick_btn').on('click', function () {
    var input = document.getElementById('raise_attachments');
    if (input) input.click();
  });

  $(document).on('click', '.tk-file-remove', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var idx = parseInt($(this).data('index'), 10);
    var input = document.getElementById('raise_attachments');
    if (!input || !input.files || input.files.length <= idx || isNaN(idx)) return;
    var dt = new DataTransfer();
    for (var i = 0; i < input.files.length; i++) {
      if (i !== idx) {
        dt.items.add(input.files[i]);
      }
    }
    input.files = dt.files;
    renderRaiseAttachments();
  });

  $('#btnNewTicket').on('click', function () {
    resetRaiseModalChrome();
    $('#raiseForm')[0].reset();
    $('#raise_category_id').html('<option value="">Choose “To department” first</option>').prop('disabled', true);
    renderRaiseAttachments();
    openModal('#raiseModal');
  });

  $(document).on('click', '.btn-edit-ticket', function () {
    var t = ticketById[$(this).data('id')];
    if (!t || !t.can_edit) {
      toastr.warning('This ticket can only be edited while it is Open and you raised it.');
      return;
    }
    $('#raiseForm')[0].reset();
    $('#raise_ticket_id').val(String(t.id));
    $('#raiseModalTitle').text('Edit ticket');
    $('#raiseSubmitBtn').html('<i class="bi bi-check-lg me-1"></i>Save changes');
    $('#raise_edit_hint').show();
    $('#raise_location_id').val(String(t.location_id || ''));
    $('#raise_from_dept').val(String(t.from_department_id || ''));
    $('#raise_to_dept').val(String(t.to_department_id || ''));
    $('#raise_priority').val(t.priority || 'medium');
    $('#raise_subject').val(t.subject || '');
    $('#raise_description').val(t.description || '');
    renderRaiseAttachments();
    var catId = String(t.ticket_category_id || '');
    loadCategories(t.to_department_id, function () {
      if (catId) {
        $('#raise_category_id').val(catId);
      }
    });
    openModal('#raiseModal');
  });

  $('#raiseForm').on('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    var editId = ($('#raise_ticket_id').val() || '').trim();
    var url = editId ? routes.update : routes.store;
    if (editId) {
      fd.append('id', editId);
    }
    $.ajax({
      url: url,
      type: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      headers: { 'X-CSRF-TOKEN': csrf },
      success: function (res) {
        if (res.success) {
          toastr.success(res.message || 'Saved');
          closeModal();
          fetchTickets({ resetPage: true });
        } else {
          toastr.error(res.message || 'Error');
        }
      },
      error: function (xhr) {
        const j = xhr.responseJSON;
        if (j && j.errors) {
          Object.keys(j.errors).forEach(function (k) { toastr.error(j.errors[k][0]); });
        } else if (j && j.message) {
          toastr.error(j.message);
        } else {
          toastr.error('Save failed');
        }
      }
    });
  });

  $(document).on('click', '.btn-view', function () {
    const t = ticketById[$(this).data('id')];
    if (!t) return;
    $('#detail_id').val(t.id);
    $('#detail_ref').text(t.ticket_no);
    $('#detail_head_status').text(statusRowLabel(t.status));
    $('#detail_location').text(t.location_name);
    $('#detail_depts').text((t.from_department_name || '') + ' → ' + (t.to_department_name || ''));
    $('#detail_category').text(t.category_name);
    $('#detail_priority').html(
      '<span class="' +
        priorityClass(t.priority) +
        '">' +
        $('<div>').text(priorityLabel(t.priority)).html() +
        '</span>'
    );
    $('#detail_raised_by').text(t.created_by_name || '—');
    $('#detail_raised_at').text(t.created_at || '—');
    $('#detail_status_by').text(t.status_updated_by_name || '—');
    $('#detail_status_at').text(t.status_updated_at || '—');
    setDetailTimeToClose(t.time_to_close);
    setDetailClosedNote(t.closed_status_note);
    $('#detail_subject').text(t.subject || '');
    $('#detail_description').text(t.description || '');
    let filesHtml = '<p class="tk-files-empty">No attachments</p>';
    if (t.attachments && t.attachments.length) {
      filesHtml = t.attachments
        .map(function (p) {
          const name = (p.split('/').pop() || 'file').trim();
          const directUrl = routes.attachmentView + '?f=' + encodeURIComponent(name);
          const spec = fileIconSpec(name);
          const mod = spec.mod ? ' ' + spec.mod : '';
          const isOffice = ticketAttachmentIsOfficeDoc(name);
          const href = isOffice ? '#' : directUrl;
          const officeClass = isOffice ? ' tk-attachment-office' : '';
          const dataEnc = isOffice ? ' data-fenc="' + encodeURIComponent(name) + '"' : '';
          return (
            '<a href="' +
            href +
            '"' +
            dataEnc +
            ' class="detail-file-link' +
            officeClass +
            '"' +
            (isOffice ? '' : ' target="_blank" rel="noopener noreferrer"') +
            ' title="Open in new tab">' +
            '<span class="tk-file-item-ic' +
            mod +
            '"><i class="bi ' +
            spec.icon +
            '" aria-hidden="true"></i></span>' +
            '<span>' +
            $('<div>').text(name).html() +
            '</span></a>'
          );
        })
        .join('');
    }
    $('#detail_files').html(filesHtml);
    loadTicketTimeline(t.id);
    openModal('#detailModal');
  });

  $(document).on('click', '.detail-file-link.tk-attachment-office', function (e) {
    e.preventDefault();
    var enc = $(this).attr('data-fenc');
    if (!enc) {
      return;
    }
    var name = decodeURIComponent(enc);
    $.get(routes.attachmentView, { office: 1, f: name })
      .done(function (res) {
        if (res && res.viewer_url) {
          window.open(res.viewer_url, '_blank', 'noopener,noreferrer');
        } else {
          window.open(routes.attachmentView + '?f=' + encodeURIComponent(name), '_blank', 'noopener,noreferrer');
        }
      })
      .fail(function () {
        window.open(routes.attachmentView + '?f=' + encodeURIComponent(name), '_blank', 'noopener,noreferrer');
      });
  });

  $(document).on('click', '.btn-quick-status', function () {
    var id = $(this).data('id');
    var t = ticketById[id];
    if (!t || !t.can_update_status) {
      return;
    }
    $('#quick_status_ticket_id').val(t.id);
    $('#quick_status_ref').text(t.ticket_no);
    $('#quick_status_select').val(t.status);
    $('#quick_status_note').val('');
    syncQuickStatusSolutionLabel();
    openModal('#quickStatusModal');
  });

  $(document).on('change', '#quick_status_select', function () {
    syncQuickStatusSolutionLabel();
  });

  $('#btnQuickStatusSave').on('click', function () {
    var id = $('#quick_status_ticket_id').val();
    if (!id) {
      return;
    }
    submitTicketStatusUpdate(
      id,
      $('#quick_status_select').val(),
      ($('#quick_status_note').val() || '').trim(),
      { closeQuickModal: true }
    );
  });

  $('#btnQuickStatusCancel, #btnQuickStatusCloseX').on('click', function () {
    closeQuickStatusModalOnly();
  });

  $(document).on('click', '.close-modal, #modalOverlay', closeModal);

  initTicketDateRangePicker();
  renderTicketFilterChips();
  syncTicketDeptFilterUi();
  syncTicketStatusFilterUi();
  fetchTickets();
})();
</script>

@include('superadmin.superadminfooter')
</body>
</html>
