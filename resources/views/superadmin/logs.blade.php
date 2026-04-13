<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
/* ── Base ─────────────────────────────────────────────── */
body { overflow-x:hidden; background:#f0f2f8; }

/* ── Header card (matches Bank Statement hero) ────────── */
.log-hero {
    background: linear-gradient(135deg,#4b2fa0 0%,#6c3fc5 50%,#8b5cf6 100%);
    border-radius:16px;
    padding:28px 32px;
    color:#fff;
    margin-bottom:24px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:16px;
    box-shadow:0 4px 24px rgba(75,47,160,.25);
}
.log-hero-title { font-size:1.5rem; font-weight:800; letter-spacing:.3px; margin:0 0 4px; }
.log-hero-sub   { font-size:.88rem; opacity:.82; margin:0; }
.log-hero-btns  { display:flex; gap:10px; flex-wrap:wrap; }
.log-hero-btns .lbtn {
    background:rgba(255,255,255,.15);
    border:1.5px solid rgba(255,255,255,.55);
    color:#fff;
    border-radius:9px;
    padding:8px 20px;
    font-size:.85rem;
    font-weight:600;
    cursor:pointer;
    display:flex;
    align-items:center;
    gap:6px;
    transition:background .18s;
}
.log-hero-btns .lbtn:hover { background:rgba(255,255,255,.28); }

/* ── Stat cards ───────────────────────────────────────── */
.sc-wrap { border-radius:14px; padding:20px 22px; color:#fff; display:flex; align-items:center; gap:16px; box-shadow:0 3px 16px rgba(0,0,0,.13); position:relative; transition:transform .18s; }
.sc-wrap:hover { transform:translateY(-2px); }
.sc-icon  { font-size:2.4rem; opacity:.9; line-height:1; }
.sc-num   { font-size:2rem; font-weight:800; line-height:1.1; transition:all .25s; }
.sc-lbl   { font-size:.78rem; opacity:.85; margin-top:3px; font-weight:500; }
.sc-purple{ background:linear-gradient(135deg,#5b21b6,#7c3aed); }
.sc-pink  { background:linear-gradient(135deg,#be185d,#ec4899); }
.sc-cyan  { background:linear-gradient(135deg,#0e7490,#06b6d4); }
.sc-green { background:linear-gradient(135deg,#166534,#16a34a); }
.sc-amber { background:linear-gradient(135deg,#92400e,#f59e0b); }

/* ── Filter-active badge on stat cards ───────────────── */
.sc-badge {
    position:absolute; top:8px; right:10px;
    background:rgba(255,255,255,.28);
    border:1px solid rgba(255,255,255,.5);
    color:#fff; border-radius:12px;
    padding:1px 8px; font-size:.68rem; font-weight:700;
    display:none;
    letter-spacing:.4px;
}
.filter-active .sc-badge { display:block; }

/* ── Filter active indicator strip ───────────────────── */
.filter-active-bar {
    display:none;
    background:#ede9fe;
    border-left:4px solid #7c3aed;
    border-radius:0 8px 8px 0;
    padding:7px 16px;
    font-size:.82rem;
    color:#5b21b6;
    font-weight:600;
    align-items:center;
    gap:8px;
    margin-bottom:12px;
}
.filter-active-bar.show { display:flex; }

/* ── Table card ───────────────────────────────────────── */
.tbl-card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; }

/* ── Table card topbar ────────────────────────────────── */
.tbl-topbar {
    padding:14px 20px;
    border-bottom:1px solid #ede9f6;
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:10px;
}
.tbl-title { font-weight:700; color:#3b0764; font-size:1rem; display:flex; align-items:center; gap:6px; margin:0; }
.tbl-title .record-count { font-size:.78rem; color:#9ca3af; font-weight:400; margin-left:4px; }

.tbl-topbar-right { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.per-page-group { display:flex; align-items:center; gap:6px; font-size:.83rem; color:#555; }
.per-page-group select {
    border:1px solid #d1d5db;
    border-radius:7px;
    padding:4px 10px;
    font-size:.83rem;
    outline:none;
    background:#fff;
}

.btn-sf {
    background:#fff;
    border:1.5px solid #7c3aed;
    color:#7c3aed;
    border-radius:9px;
    padding:7px 18px;
    font-size:.83rem;
    font-weight:600;
    cursor:pointer;
    display:flex;
    align-items:center;
    gap:6px;
    transition:background .18s,color .18s;
}
.btn-sf:hover, .btn-sf.active { background:#7c3aed; color:#fff; }

.btn-export {
    background:#f3f0ff;
    border:1px solid #c4b5fd;
    color:#5b21b6;
    border-radius:9px;
    padding:7px 16px;
    font-size:.83rem;
    font-weight:600;
    cursor:pointer;
    display:flex; align-items:center; gap:5px;
    transition:background .18s;
}
.btn-export:hover { background:#ede9fe; }

/* ── Filter panel (collapsible) ───────────────────────── */
.filter-panel {
    background:#faf8ff;
    border-bottom:1px solid #ede9f6;
    padding:18px 22px;
    display:none;
}
.filter-panel.open { display:block; }
.fp-label {
    font-size:.78rem;
    font-weight:600;
    color:#6b21a8;
    margin-bottom:4px;
    display:block;
}
.fp-control {
    width:100%;
    border:1.5px solid #e2d9f3;
    border-radius:8px;
    padding:7px 12px;
    font-size:.83rem;
    background:#fff;
    color:#374151;
    outline:none;
    transition:border .15s;
}
.fp-control:focus { border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,.12); }
.fp-apply {
    background:linear-gradient(135deg,#5b21b6,#7c3aed);
    border:none; color:#fff; border-radius:8px;
    padding:8px 22px; font-size:.84rem; font-weight:600;
    cursor:pointer; display:flex; align-items:center; gap:6px;
    transition:opacity .18s;
}
.fp-apply:hover { opacity:.9; }
.fp-clear {
    background:#f3f0ff; border:1px solid #c4b5fd; color:#5b21b6;
    border-radius:8px; padding:8px 18px; font-size:.84rem; font-weight:600;
    cursor:pointer; display:flex; align-items:center; gap:5px;
    transition:background .18s;
}
.fp-clear:hover { background:#ede9fe; }

/* ── Quick date buttons ───────────────────────────────── */
.quick-date:hover  { background:#ede9fe !important; border-color:#7c3aed !important; }
.quick-date.qdactive { background:linear-gradient(135deg,#5b21b6,#7c3aed) !important; color:#fff !important; border-color:#5b21b6 !important; }

/* ── Table ────────────────────────────────────────────── */
.logs-tbl { width:100%; border-collapse:collapse; font-size:.82rem; }
.logs-tbl thead th {
    background:#4b2fa0;
    color:#fff;
    padding:11px 13px;
    font-weight:600;
    white-space:nowrap;
    position:sticky;
    top:0;
    z-index:2;
}
.logs-tbl tbody tr:nth-child(even):not(.detail-row) { background:#faf8ff; }
.logs-tbl tbody tr:hover:not(.detail-row) { background:#f3f0ff; }
.logs-tbl td { padding:9px 13px; vertical-align:top; border-bottom:1px solid #ede9f6; }
.logs-tbl td.nw { white-space:nowrap; }

/* ── Action badges ────────────────────────────────────── */
.ab { display:inline-block; padding:3px 10px; border-radius:20px; font-size:.74rem; font-weight:600; white-space:nowrap; }
.ab-Login    { background:#dcfce7; color:#166534; }
.ab-Logout   { background:#fee2e2; color:#991b1b; }
.ab-Approve  { background:#dbeafe; color:#1d4ed8; }
.ab-Reject   { background:#fef3c7; color:#92400e; }
.ab-Save,.ab-Submit,.ab-Create { background:#f3e8ff; color:#6d28d9; }
.ab-Edit,.ab-Update { background:#fffbeb; color:#b45309; }
.ab-Delete   { background:#ffe4e6; color:#9f1239; }
.ab-Search,.ab-Filter,.ab-Clear { background:#cffafe; color:#155e75; }
.ab-Export,.ab-Print,.ab-Download { background:#e0e7ff; color:#3730a3; }
.ab-default  { background:#f1f5f9; color:#475569; }

/* ── Module chip ──────────────────────────────────────── */
.mod-chip { display:inline-block; padding:3px 10px; border-radius:20px; background:#ede9fe; color:#5b21b6; font-size:.74rem; font-weight:600; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* ── IP chip ──────────────────────────────────────────── */
.ip-chip { display:inline-flex; align-items:center; gap:5px; background:#eff6ff; color:#1d4ed8; border-radius:6px; padding:3px 10px; font-size:.78rem; font-weight:700; }

/* ── Extra-data tag chips ─────────────────────────────── */
.ed-chip { display:inline-block; border-radius:5px; padding:2px 8px; font-size:.71rem; margin:1px 2px; line-height:1.6; }

/* ── Detail expand row ────────────────────────────────── */
.detail-row td { background:#f5f0ff !important; padding:14px 20px !important; font-size:.8rem; }
.detail-row pre {
    background:#2d1b69; color:#e9d5ff;
    border-radius:10px; padding:12px 16px;
    font-size:.76rem; white-space:pre-wrap;
    word-break:break-all; max-height:200px;
    overflow-y:auto; margin:0;
}
.detail-section { margin-bottom:6px; }
.detail-label { font-weight:700; color:#5b21b6; font-size:.78rem; }
.detail-val   { font-size:.79rem; color:#374151; word-break:break-all; }

/* ── Login IP banner ──────────────────────────────────── */
.login-ip-banner {
    display:inline-flex; align-items:center; gap:8px;
    background:#dbeafe; color:#1e3a8a;
    border-radius:8px; padding:6px 14px;
    font-size:.81rem; font-weight:600;
    margin-bottom:8px;
}

/* ── Pagination ───────────────────────────────────────── */
.pg { display:flex; align-items:center; gap:5px; flex-wrap:wrap; }
.pg-btn {
    border:1.5px solid #e2d9f3; background:#fff; color:#374151;
    border-radius:7px; padding:4px 12px; font-size:.8rem; cursor:pointer;
    transition:background .15s,color .15s;
}
.pg-btn.active { background:#5b21b6; color:#fff; border-color:#5b21b6; }
.pg-btn:hover:not(.active):not([disabled]) { background:#f3f0ff; color:#5b21b6; border-color:#c4b5fd; }
.pg-btn[disabled] { opacity:.45; cursor:default; }
.pg-info { font-size:.78rem; color:#9ca3af; padding:0 4px; }

/* ── Highlight ────────────────────────────────────────── */
mark { background:#fde68a; padding:0 2px; border-radius:2px; }

/* ── Loading overlay ──────────────────────────────────── */
#logsOverlay {
    display:none; position:fixed; inset:0;
    background:rgba(45,27,105,.22); z-index:9999;
    align-items:center; justify-content:center;
}
#logsOverlay .spin {
    width:48px; height:48px;
    border:5px solid rgba(255,255,255,.3);
    border-top-color:#fff;
    border-radius:50%;
    animation:_lspin .7s linear infinite;
}
@keyframes _lspin { to { transform:rotate(360deg); } }
</style>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div id="logsOverlay"><div class="spin"></div></div>

<div class="pc-container">
  <div class="pc-content">
    <div class="container-fluid" style="max-width:1600px;">

      {{-- ══ HERO HEADER ════════════════════════════════ --}}
      <div class="log-hero">
        <div>
          <p class="log-hero-title"><i class="bi bi-journal-text me-2"></i>Activity Logs</p>
          <p class="log-hero-sub">Monitor every user action — logins, searches, edits, approvals and more</p>
        </div>
        <div class="log-hero-btns">
          <button class="lbtn" id="btnRefreshStats">
            <i class="bi bi-arrow-clockwise"></i> Refresh Stats
          </button>
          <button class="lbtn" data-bs-toggle="modal" data-bs-target="#clearLogsModal" style="border-color:rgba(255,180,180,.7);background:rgba(220,38,38,.18);">
            <i class="bi bi-trash"></i> Clear Old Logs
          </button>
        </div>
      </div>

      {{-- ── Filter active bar ─────────────────────────── --}}
      <div class="filter-active-bar" id="filterActiveBar">
        <i class="bi bi-funnel-fill"></i>
        <span id="filterActiveText">Filter applied — stats updated to match results</span>
        <button onclick="$('#btnClearFilter').trigger('click')" style="margin-left:auto;background:none;border:1.5px solid #7c3aed;color:#5b21b6;border-radius:7px;padding:2px 12px;font-size:.78rem;font-weight:600;cursor:pointer;">
          <i class="bi bi-x-circle me-1"></i>Clear Filter
        </button>
      </div>

      {{-- ══ STAT CARDS ════════════════════════════════ --}}
      <div class="row g-3 mb-4" id="statCardsRow">

        <div class="col-6 col-sm-4 col-lg">
          <div class="sc-wrap sc-purple" id="sc1">
            <span class="sc-badge" id="sc1-badge">FILTERED</span>
            <div class="sc-icon" id="sc1-icon"><i class="bi bi-list-check"></i></div>
            <div>
              <div class="sc-num" id="stat-all">{{ $stats['total_all'] }}</div>
              <div class="sc-lbl" id="sc1-lbl">Total Logs</div>
            </div>
          </div>
        </div>

        <div class="col-6 col-sm-4 col-lg">
          <div class="sc-wrap sc-pink" id="sc2">
            <span class="sc-badge" id="sc2-badge">FILTERED</span>
            <div class="sc-icon" id="sc2-icon"><i class="bi bi-calendar-check"></i></div>
            <div>
              <div class="sc-num" id="stat-today">{{ $stats['total_today'] }}</div>
              <div class="sc-lbl" id="sc2-lbl">Today</div>
            </div>
          </div>
        </div>

        <div class="col-6 col-sm-4 col-lg">
          <div class="sc-wrap sc-cyan" id="sc3">
            <span class="sc-badge" id="sc3-badge">FILTERED</span>
            <div class="sc-icon" id="sc3-icon"><i class="bi bi-calendar-week"></i></div>
            <div>
              <div class="sc-num" id="stat-week">{{ $stats['total_week'] }}</div>
              <div class="sc-lbl" id="sc3-lbl">This Week</div>
            </div>
          </div>
        </div>

        <div class="col-6 col-sm-4 col-lg">
          <div class="sc-wrap sc-green" id="sc4">
            <span class="sc-badge" id="sc4-badge">FILTERED</span>
            <div class="sc-icon" id="sc4-icon"><i class="bi bi-calendar-month"></i></div>
            <div>
              <div class="sc-num" id="stat-month">{{ $stats['total_month'] }}</div>
              <div class="sc-lbl" id="sc4-lbl">Last 30 Days</div>
            </div>
          </div>
        </div>

        <div class="col-6 col-sm-4 col-lg">
          <div class="sc-wrap sc-amber" id="sc5">
            <span class="sc-badge" id="sc5-badge">FILTERED</span>
            <div class="sc-icon" id="sc5-icon"><i class="bi bi-people-fill"></i></div>
            <div>
              <div class="sc-num" id="stat-users">{{ $stats['active_users'] }}</div>
              <div class="sc-lbl" id="sc5-lbl">Active Users (30d)</div>
            </div>
          </div>
        </div>

      </div>

      {{-- ══ TABLE CARD ════════════════════════════════ --}}
      <div class="tbl-card">

        {{-- top bar --}}
        <div class="tbl-topbar">
          <h6 class="tbl-title">
            <i class="bi bi-table" style="color:#7c3aed;"></i>
            Activity Log Entries
            <span class="record-count" id="logCount"></span>
          </h6>
          <div class="tbl-topbar-right">
            <div class="per-page-group">
              Per page:
              <select id="logPerPage">
                <option value="25">25</option>
                <option value="50" selected>50</option>
                <option value="100">100</option>
                <option value="200">200</option>
              </select>
            </div>
            <div class="pg" id="paginationTop"></div>
            <button class="btn-export" id="btnExportCsv">
              <i class="bi bi-download"></i> Export CSV
            </button>
            <button class="btn-sf" id="btnToggleFilter">
              <i class="bi bi-funnel"></i> Search &amp; Filter
            </button>
          </div>
        </div>

        {{-- ── collapsible filter panel ── --}}
        <div class="filter-panel" id="filterPanel">
          <div class="row g-2 align-items-end">

            <div class="col-12">
              <label class="fp-label"><i class="bi bi-calendar3 me-1"></i>Quick Date</label>
              <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px;">
                <button class="quick-date" data-range="today"     style="background:#f3f0ff;border:1.5px solid #c4b5fd;color:#5b21b6;border-radius:7px;padding:4px 14px;font-size:.78rem;font-weight:600;cursor:pointer;">Today</button>
                <button class="quick-date" data-range="yesterday" style="background:#f3f0ff;border:1.5px solid #c4b5fd;color:#5b21b6;border-radius:7px;padding:4px 14px;font-size:.78rem;font-weight:600;cursor:pointer;">Yesterday</button>
                <button class="quick-date" data-range="last7"     style="background:#f3f0ff;border:1.5px solid #c4b5fd;color:#5b21b6;border-radius:7px;padding:4px 14px;font-size:.78rem;font-weight:600;cursor:pointer;">Last 7 Days</button>
                <button class="quick-date" data-range="last30"    style="background:#f3f0ff;border:1.5px solid #c4b5fd;color:#5b21b6;border-radius:7px;padding:4px 14px;font-size:.78rem;font-weight:600;cursor:pointer;">Last 30 Days</button>
                <button class="quick-date" data-range="week"      style="background:#f3f0ff;border:1.5px solid #c4b5fd;color:#5b21b6;border-radius:7px;padding:4px 14px;font-size:.78rem;font-weight:600;cursor:pointer;">This Week</button>
                <button class="quick-date" data-range="month"     style="background:#f3f0ff;border:1.5px solid #c4b5fd;color:#5b21b6;border-radius:7px;padding:4px 14px;font-size:.78rem;font-weight:600;cursor:pointer;">This Month</button>
              </div>
              <input type="text" id="logDateRange" class="fp-control" placeholder="Or pick custom range…" readonly autocomplete="off" style="cursor:pointer;max-width:280px;">
            </div>

            <div class="col-12 col-sm-6 col-md-3 col-lg-2">
              <label class="fp-label"><i class="bi bi-person me-1"></i>User</label>
              <select id="logUser" class="fp-control">
                <option value="">All Users</option>
                @foreach($users as $u)
                  <option value="{{ $u->user_id }}">{{ $u->user_fullname ?: $u->user_email }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-sm-6 col-md-3 col-lg-2">
              <label class="fp-label"><i class="bi bi-grid me-1"></i>Module</label>
              <select id="logModule" class="fp-control">
                <option value="">All Modules</option>
                @foreach($modules as $m)
                  <option value="{{ $m }}">{{ $m }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-sm-6 col-md-3 col-lg-2">
              <label class="fp-label"><i class="bi bi-lightning me-1"></i>Action</label>
              <select id="logAction" class="fp-control">
                <option value="">All Actions</option>
                @foreach($actions as $a)
                  <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-sm-6 col-md-3 col-lg-2">
              <label class="fp-label"><i class="bi bi-hdd-network me-1"></i>IP Address</label>
              <select id="logIp" class="fp-control">
                <option value="">All IPs</option>
                @foreach($ips as $ip)
                  <option value="{{ $ip }}">{{ $ip }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-sm-6 col-md-3 col-lg-2">
              <label class="fp-label"><i class="bi bi-search me-1"></i>Search</label>
              <input type="text" id="logSearch" class="fp-control" placeholder="name, action, detail…">
            </div>

            <div class="col-12 col-sm-auto d-flex gap-2 mt-1">
              <button class="fp-apply" id="btnApplyFilter">
                <i class="bi bi-funnel-fill"></i> Filter
              </button>
              <button class="fp-clear" id="btnClearFilter">
                <i class="bi bi-x-circle"></i> Clear
              </button>
            </div>

          </div>
        </div>

        {{-- ── table ── --}}
        <div style="overflow-x:auto;">
          <table class="logs-tbl" id="logsTable">
            <thead>
              <tr>
                <th style="width:42px;">#</th>
                <th style="width:155px;">Date &amp; Time</th>
                <th style="width:130px;">User</th>
                <th style="width:65px;">Level</th>
                <th style="width:115px;">Action</th>
                <th style="width:160px;">Module</th>
                <th style="width:200px;">Description</th>
                <th style="width:128px;"><i class="bi bi-geo-alt text-secondary"></i> Location</th>
                <th style="width:145px;">🖥️ Login IP</th>
                <th style="width:44px;text-align:center;">+</th>
              </tr>
            </thead>
            <tbody id="logsTbody">
              <tr><td colspan="10" class="text-center text-muted py-5" style="font-size:.9rem;">
                Click <strong>Search &amp; Filter</strong> and apply filters to load logs.
              </td></tr>
            </tbody>
          </table>
        </div>

        {{-- bottom pagination bar --}}
        <div class="tbl-topbar" style="justify-content:flex-end;border-top:1px solid #ede9f6;border-bottom:none;">
          <div class="pg" id="paginationBottom"></div>
        </div>

      </div>{{-- /tbl-card --}}

    </div>
  </div>
</div>

{{-- ══ CLEAR LOGS MODAL ══════════════════════════════════ --}}
<div class="modal fade" id="clearLogsModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;">
      <div class="modal-header" style="background:linear-gradient(135deg,#991b1b,#dc2626);color:#fff;border:none;">
        <h6 class="modal-title fw-bold"><i class="bi bi-trash me-2"></i>Clear Old Logs</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label fw-semibold text-secondary" style="font-size:.85rem;">Delete logs older than:</label>
        <select id="clearDays" class="form-select" style="border-radius:8px;border:1.5px solid #e2d9f3;">
          <option value="30">30 days</option>
          <option value="60">60 days</option>
          <option value="90" selected>90 days</option>
          <option value="180">180 days</option>
          <option value="365">1 year</option>
        </select>
        <small class="text-danger mt-2 d-block"><i class="bi bi-exclamation-triangle me-1"></i>This action cannot be undone.</small>
      </div>
      <div class="modal-footer" style="border:none;">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger btn-sm" id="btnClearConfirm" style="border-radius:8px;">
          <i class="bi bi-trash me-1"></i>Delete
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ── FOOTER (loads jQuery + Bootstrap JS) ── --}}
@include('superadmin.superadminfooter')

{{-- moment + daterangepicker AFTER jQuery ── --}}
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
$(document).ready(function () {

    /* ── State ──────────────────────────────────────── */
    var currentPage   = 1;
    var totalPages    = 1;
    var currentSearch = '';

    var LOG_DATA_URL  = '{{ route("superadmin.logs_data") }}';
    var LOG_STATS_URL = '{{ route("superadmin.logs_stats") }}';
    var LOG_CLEAR_URL = '{{ route("superadmin.logs_clear") }}';
    var CSRF          = '{{ csrf_token() }}';

    /* ── Date range picker ──────────────────────────── */
    $('#logDateRange').daterangepicker({
        autoUpdateInput : false,
        opens           : 'right',
        locale          : { format:'DD/MM/YYYY', cancelLabel:'Clear', applyLabel:'Apply' }
    });
    $('#logDateRange').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });
    $('#logDateRange').on('cancel.daterangepicker', function () { $(this).val(''); });

    /* ── Toggle filter panel ────────────────────────── */
    $('#btnToggleFilter').on('click', function () {
        var $p = $('#filterPanel');
        var open = $p.hasClass('open');
        $p.toggleClass('open');
        $(this).toggleClass('active', !open);
    });

    /* ── Quick date buttons ─────────────────────────── */
    $(document).on('click', '.quick-date', function () {
        var t    = $(this).data('range');
        var fmt  = 'DD/MM/YYYY';
        var from, to;
        if (t === 'today')     { from = to = moment(); }
        else if (t === 'yesterday') { from = to = moment().subtract(1,'days'); }
        else if (t === 'week') { from = moment().startOf('isoWeek'); to = moment(); }
        else if (t === 'month'){ from = moment().startOf('month'); to = moment(); }
        else if (t === 'last7'){ from = moment().subtract(6,'days'); to = moment(); }
        else if (t === 'last30'){from = moment().subtract(29,'days'); to = moment(); }
        else { $('#logDateRange').val(''); return; }
        $('#logDateRange').val(from.format(fmt) + ' - ' + to.format(fmt));
        // highlight active quick btn
        $('.quick-date').removeClass('qdactive');
        $(this).addClass('qdactive');
    });

    /* ── Clear quick btn highlight on manual picker use ─ */
    $('#logDateRange').on('apply.daterangepicker', function () {
        $('.quick-date').removeClass('qdactive');
    });

    /* ── Filter apply ───────────────────────────────── */
    $('#btnApplyFilter').on('click', function () { currentPage = 1; loadLogs(); });

    /* ── Filter clear ───────────────────────────────── */
    $('#btnClearFilter').on('click', function () {
        $('#logDateRange,#logSearch').val('');
        $('#logUser,#logModule,#logAction,#logIp').val('');
        $('.quick-date').removeClass('qdactive');
        currentPage = 1;
        resetGlobalStats();
        loadLogs();
    });

    /* ── Per page / search enter ────────────────────── */
    $('#logPerPage').on('change', function () { currentPage = 1; loadLogs(); });
    $('#logSearch').on('keyup', function (e) { if (e.key === 'Enter') { currentPage = 1; loadLogs(); } });

    /* ── Helpers ────────────────────────────────────── */
    function esc(t) {
        if (!t) return '';
        return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
    function hlText(text, kw) {
        if (!kw || !text) return esc(text);
        var re = kw.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
        return esc(text).replace(new RegExp('(' + re + ')','gi'),'<mark>$1</mark>');
    }

    function actionBadge(action) {
        if (!action) return '';
        var map = {
            'Login':'ab-Login','Logout':'ab-Logout',
            'Approve':'ab-Approve','Bulk Approve':'ab-Approve',
            'Reject':'ab-Reject','Bulk Reject':'ab-Reject','Reject Confirmed':'ab-Reject',
            'Save':'ab-Save','Submit':'ab-Submit','Create':'ab-Create',
            'Edit':'ab-Edit','Update':'ab-Update',
            'Delete':'ab-Delete',
            'Search':'ab-Search','Filter':'ab-Filter','Clear Filter':'ab-Clear',
            'Export':'ab-Export','Print':'ab-Print','Download':'ab-Download',
        };
        var cls = map[action] || 'ab-default';
        return '<span class="ab ' + cls + '">' + esc(action) + '</span>';
    }

    function levelLabel(lvl) {
        var map = {1:'Super Admin',2:'Zonal',3:'Admin',4:'Audit',5:'User'};
        if (map[lvl]) return '<span style="font-size:.74rem;font-weight:600;color:#5b21b6;">' + map[lvl] + '</span>';
        return lvl ? '<span style="font-size:.74rem;color:#999;">Level ' + lvl + '</span>' : '<span style="color:#ccc;">—</span>';
    }

    /** Coordinates from extra_data + map link (Google Maps). */
    function locationCellFromExtra(ed) {
        if (!ed || typeof ed !== 'object') {
            return '<span style="color:#d1d5db;font-size:.85rem;">—</span>';
        }
        var lat = ed.latitude != null && ed.latitude !== '' ? Number(ed.latitude) : NaN;
        var lng = ed.longitude != null && ed.longitude !== '' ? Number(ed.longitude) : NaN;
        if (!isNaN(lat) && !isNaN(lng)) {
            var q = lat + ',' + lng;
            var gmaps = 'https://www.google.com/maps?q=' + encodeURIComponent(q);
            var slat = lat.toFixed(5);
            var slng = lng.toFixed(5);
            return '<div style="font-size:.72rem;line-height:1.35;max-width:124px;">'
                + '<span style="color:#374151;word-break:break-all;">' + esc(slat) + ', ' + esc(slng) + '</span><br/>'
                + '<a href="' + esc(gmaps) + '" target="_blank" rel="noopener noreferrer" class="log-map-open" title="Open in Google Maps" '
                + 'onclick="event.stopPropagation();" style="display:inline-flex;align-items:center;gap:4px;margin-top:3px;'
                + 'padding:2px 8px 2px 6px;border-radius:6px;background:#ecfdf5;color:#047857;font-size:.7rem;font-weight:600;text-decoration:none;border:1px solid #a7f3d0;">'
                + '<i class="bi bi-geo-alt-fill" style="font-size:.85rem;"></i> Map</a></div>';
        }
        if (ed.geo_status === 'denied') {
            return '<span style="font-size:.72rem;color:#9ca3af;" title="User declined location">Not shared</span>';
        }
        if (ed.geo_status === 'timeout' || ed.geo_status === 'unavailable' || ed.geo_status === 'unsupported') {
            return '<span style="font-size:.72rem;color:#9ca3af;">—</span>';
        }
        return '<span style="color:#d1d5db;font-size:.85rem;">—</span>';
    }

    /* ── Load logs ──────────────────────────────────── */
    function loadLogs() {
        $('#logsOverlay').css('display','flex');
        $.ajax({
            url    : LOG_DATA_URL,
            method : 'GET',
            data   : {
                date_range : $('#logDateRange').val(),
                user_id    : $('#logUser').val(),
                module     : $('#logModule').val(),
                action     : $('#logAction').val(),
                ip_address : $('#logIp').val(),
                search     : $('#logSearch').val().trim(),
                per_page   : $('#logPerPage').val(),
                page       : currentPage,
            },
            success  : function (res) { renderTable(res); },
            error    : function (xhr) {
                $('#logsTbody').html('<tr><td colspan="10" class="text-center text-danger py-4">'
                    + '<i class="bi bi-exclamation-triangle me-2"></i>Failed to load logs.'
                    + (xhr.responseJSON ? ' ' + xhr.responseJSON.message : '') + '</td></tr>');
            },
            complete : function () { $('#logsOverlay').hide(); }
        });
    }

    /* ── Stat update helpers ────────────────────────── */
    // Global stat values (set once from PHP on page load)
    var globalStats = {
        total : parseInt('{{ $stats["total_all"] }}')   || 0,
        today : parseInt('{{ $stats["total_today"] }}') || 0,
        week  : parseInt('{{ $stats["total_week"] }}')  || 0,
        month : parseInt('{{ $stats["total_month"] }}') || 0,
        users : parseInt('{{ $stats["active_users"] }}') || 0,
    };

    function animateNum($el, newVal) {
        var oldVal = parseInt($el.text().replace(/,/g,'')) || 0;
        if (oldVal === newVal) { $el.text(newVal.toLocaleString()); return; }
        $({ v: oldVal }).animate({ v: newVal }, {
            duration : 400,
            step     : function () { $el.text(Math.round(this.v).toLocaleString()); },
            complete : function () { $el.text(newVal.toLocaleString()); }
        });
    }

    function updateFilteredStats(stats) {
        // Mark cards as filtered (shows FILTERED badge) — labels & icons stay the same
        $('#sc1,#sc2,#sc3,#sc4,#sc5').addClass('filter-active');
        $('#filterActiveBar').addClass('show');

        // Update numbers only — keep original labels and icons unchanged
        animateNum($('#stat-all'),   stats.total);
        animateNum($('#stat-today'), stats.today);
        animateNum($('#stat-week'),  stats.week);
        animateNum($('#stat-month'), stats.month);
        animateNum($('#stat-users'), stats.unique_users);

        // Build filter summary text
        var parts = [];
        if ($('#logDateRange').val()) parts.push('Date: ' + $('#logDateRange').val());
        if ($('#logUser').val())       parts.push('User: ' + $('#logUser option:selected').text());
        if ($('#logModule').val())     parts.push('Module: ' + $('#logModule').val());
        if ($('#logAction').val())     parts.push('Action: ' + $('#logAction').val());
        if ($('#logIp').val())         parts.push('IP: ' + $('#logIp').val());
        if ($('#logSearch').val())     parts.push('Search: "' + $('#logSearch').val() + '"');
        if (parts.length) {
            $('#filterActiveText').html('<i class="bi bi-funnel-fill me-1"></i><b>Filter active:</b> ' + parts.join(' &nbsp;|&nbsp; '));
        }
    }

    function resetGlobalStats() {
        // Remove filtered badge from all cards, hide filter bar
        $('#sc1,#sc2,#sc3,#sc4,#sc5').removeClass('filter-active');
        $('#filterActiveBar').removeClass('show');

        // Restore global numbers (labels & icons never changed)
        animateNum($('#stat-all'),   globalStats.total);
        animateNum($('#stat-today'), globalStats.today);
        animateNum($('#stat-week'),  globalStats.week);
        animateNum($('#stat-month'), globalStats.month);
        animateNum($('#stat-users'), globalStats.users);
    }

    /* ── Render table ───────────────────────────────── */
    function renderTable(res) {
        totalPages    = res.last_page;
        currentPage   = res.page;
        currentSearch = $('#logSearch').val().trim();
        var count     = res.total;
        $('#logCount').html('&nbsp;— ' + count.toLocaleString() + ' record' + (count !== 1 ? 's' : ''));

        // ── Update stat cards based on filter state ──
        if (res.is_filtered && res.stats) {
            updateFilteredStats(res.stats);
        } else {
            resetGlobalStats();
        }

        if (!res.data || res.data.length === 0) {
            $('#logsTbody').html('<tr><td colspan="10" class="text-center text-muted py-5" style="font-size:.9rem;">'
                + '<i class="bi bi-inbox me-2" style="font-size:1.3rem;"></i>No logs found for the selected filters.</td></tr>');
            renderPagination();
            return;
        }

        var offset = (currentPage - 1) * parseInt(res.per_page);
        var html   = '';

        $.each(res.data, function (i, r) {
            var rowId     = 'dr_' + r.id;
            var desc      = r.description || '';
            var descShort = desc.length > 100 ? desc.substring(0, 100) + '…' : desc;

            /* ── filter/context chips ── */
            var chips = '';
            if (r.extra_data && typeof r.extra_data === 'object') {
                var ed = r.extra_data;
                if (ed.applied_filters && typeof ed.applied_filters === 'object') {
                    for (var fk in ed.applied_filters) {
                        chips += '<span class="ed-chip" style="background:#e0f2fe;color:#0369a1;">'
                               + esc(fk) + ': <b>' + esc(String(ed.applied_filters[fk]).substring(0,25)) + '</b></span>';
                    }
                }
                if (ed.date_from && ed.date_to)
                    chips += '<span class="ed-chip" style="background:#fef9c3;color:#854d0e;">📅 ' + esc(ed.date_from) + ' → ' + esc(ed.date_to) + '</span>';
                if (ed.status_label)
                    chips += '<span class="ed-chip" style="background:#dcfce7;color:#166534;">● ' + esc(ed.status_label) + '</span>';
                if (ed.record_id)
                    chips += '<span class="ed-chip" style="background:#ede9fe;color:#5b21b6;">ID #' + esc(String(ed.record_id)) + '</span>';
                if (ed.patient)
                    chips += '<span class="ed-chip" style="background:#fff1f2;color:#9f1239;">👤 ' + esc(String(ed.patient).substring(0,35)) + '</span>';
                if (ed.count !== undefined)
                    chips += '<span class="ed-chip" style="background:#f0f9ff;color:#0c4a6e;">× ' + ed.count + '</span>';
                if (ed.tab)
                    chips += '<span class="ed-chip" style="background:#fdf4ff;color:#6b21a8;">Tab: ' + esc(ed.tab) + '</span>';
                if (ed.field && ed.value)
                    chips += '<span class="ed-chip" style="background:#ecfdf5;color:#065f46;">🔍 ' + esc(String(ed.value).substring(0,25)) + '</span>';
                if (ed.menu_label)
                    chips += '<span class="ed-chip" style="background:#eff6ff;color:#1e40af;">📌 ' + esc(ed.menu_label) + '</span>';
            }

            /* ── detail expand panel ── */
            var loginIpVal   = (r.extra_data && r.extra_data.login_ip) ? r.extra_data.login_ip : (r.ip_address || '—');
            var sessionStart = (r.extra_data && r.extra_data.session_started) ? r.extra_data.session_started : '';
            var detail = '';
            detail += '<div class="col-12">'
                    + '<span class="login-ip-banner"><i class="bi bi-pc-display"></i>'
                    + '&nbsp;Login Machine IP: <strong>' + esc(loginIpVal) + '</strong>'
                    + (sessionStart ? '&nbsp;&nbsp;|&nbsp;&nbsp;Session: ' + esc(sessionStart) : '')
                    + '</span></div>';
            var edLoc = (r.extra_data && typeof r.extra_data === 'object') ? r.extra_data : null;
            var dLat = edLoc && edLoc.latitude != null && edLoc.latitude !== '' ? Number(edLoc.latitude) : NaN;
            var dLng = edLoc && edLoc.longitude != null && edLoc.longitude !== '' ? Number(edLoc.longitude) : NaN;
            if (!isNaN(dLat) && !isNaN(dLng)) {
                var mapHref = 'https://www.google.com/maps?q=' + encodeURIComponent(dLat + ',' + dLng);
                detail += '<div class="col-12 mt-2">'
                    + '<span class="login-ip-banner" style="background:linear-gradient(90deg,#ecfdf5,#f0fdf4);border-color:#86efac;color:#14532d;">'
                    + '<i class="bi bi-geo-alt-fill"></i>&nbsp;Coordinates: <strong>' + esc(dLat.toFixed(7)) + ', ' + esc(dLng.toFixed(7)) + '</strong>'
                    + (edLoc.location_accuracy_m != null ? '&nbsp;&nbsp;±' + esc(String(edLoc.location_accuracy_m)) + ' m' : '')
                    + '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="' + esc(mapHref) + '" target="_blank" rel="noopener noreferrer" style="font-weight:700;color:#047857;">Open in Google Maps</a>'
                    + '</span></div>';
            }
            if (r.url)
                detail += '<div class="col-md-6 detail-section"><div class="detail-label">Full URL</div><div class="detail-val">' + esc(r.url) + '</div></div>';
            if (r.user_agent)
                detail += '<div class="col-md-6 detail-section"><div class="detail-label">Browser / Agent</div><div class="detail-val">' + esc(r.user_agent) + '</div></div>';
            if (desc.length > 100)
                detail += '<div class="col-12 detail-section"><div class="detail-label">Full Description</div><div class="detail-val">' + esc(desc) + '</div></div>';
            if (r.extra_data) {
                var edStr = typeof r.extra_data === 'string' ? r.extra_data : JSON.stringify(r.extra_data, null, 2);
                detail += '<div class="col-12 mt-1"><div class="detail-label mb-1">Extra Data (JSON)</div><pre>' + esc(edStr) + '</pre></div>';
            }
            if (!detail) detail = '<div class="col-12 text-muted">No extra detail.</div>';
            // console.log(r.created_at);
            
            var dateStr = r.created_at ? r.created_at.replace('T',' ').substring(0,19) : '—';
            var displayIp = r.ip_address || '—';

            html += '<tr>';
            html += '<td class="nw text-muted" style="font-size:.75rem;">' + (offset + i + 1) + '</td>';
            html += '<td class="nw" style="font-size:.78rem;">' + hlText(dateStr, currentSearch) + '</td>';
            // User cell — name + email + employee ID all together
            html += '<td>'
                  + '<div style="font-weight:700;font-size:.83rem;">' + hlText(r.user_fullname || '—', currentSearch) + '</div>'
                  + (r.user_email
                      ? '<div style="font-size:.72rem;color:#9ca3af;margin-top:1px;">' + hlText(r.user_email, currentSearch) + '</div>'
                      : '')
                  + (r.username
                      ? '<div style="margin-top:2px;"><span style="display:inline-block;background:#f3f0ff;color:#5b21b6;border-radius:5px;padding:1px 8px;font-size:.71rem;font-weight:700;letter-spacing:.4px;">' + hlText(r.username, currentSearch) + '</span></div>'
                      : '')
                  + '</td>';
            html += '<td class="nw">' + levelLabel(r.access_level) + '</td>';
            html += '<td class="nw">' + actionBadge(r.action) + '</td>';
            html += '<td><span class="mod-chip" title="' + esc(r.module) + '">' + hlText(r.module || '—', currentSearch) + '</span></td>';
            html += '<td style="max-width:280px;">'
                  + '<div style="font-size:.79rem;">' + hlText(descShort, currentSearch) + '</div>'
                  + (chips ? '<div style="margin-top:4px;line-height:2;">' + chips + '</div>' : '')
                  + '</td>';
            html += '<td style="max-width:130px;vertical-align:middle;">'
                  + locationCellFromExtra(r.extra_data && typeof r.extra_data === 'object' ? r.extra_data : null)
                  + '</td>';
            html += '<td class="nw">'
                  + '<span class="ip-chip"><i class="bi bi-wifi"></i>' + hlText(displayIp, currentSearch) + '</span>'
                  + '</td>';
            html += '<td style="text-align:center;">'
                  + '<button class="toggle-detail" data-target="' + rowId + '" title="Details" '
                  + 'style="background:#f3f0ff;border:1px solid #c4b5fd;border-radius:6px;color:#5b21b6;width:28px;height:28px;cursor:pointer;font-size:.9rem;padding:0;">'
                  + '<i class="bi bi-chevron-down"></i></button></td>';
            html += '</tr>';
            html += '<tr class="detail-row" id="' + rowId + '" style="display:none;"><td colspan="10"><div class="row g-2">' + detail + '</div></td></tr>';
        });

        $('#logsTbody').html(html);
        renderPagination();
    }

    /* ── Expand detail ──────────────────────────────── */
    $(document).on('click', '.toggle-detail', function (e) {
        e.stopPropagation();
        var $icon   = $(this).find('i');
        var $detail = $('#' + $(this).data('target'));
        if ($detail.is(':hidden')) {
            $detail.show();
            $icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
        } else {
            $detail.hide();
            $icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
        }
    });

    /* ── Pagination ─────────────────────────────────── */
    function renderPagination() {
        if (totalPages <= 1) { $('#paginationTop,#paginationBottom').html(''); return; }
        var h = '', range = 2;
        h += '<button class="pg-btn" onclick="goPage(' + (currentPage-1) + ')" ' + (currentPage===1?'disabled':'') + '><i class="bi bi-chevron-left"></i></button>';
        for (var p = 1; p <= totalPages; p++) {
            if (p===1 || p===totalPages || (p>=currentPage-range && p<=currentPage+range)) {
                h += '<button class="pg-btn ' + (p===currentPage?'active':'') + '" onclick="goPage(' + p + ')">' + p + '</button>';
            } else if (p===currentPage-range-1 || p===currentPage+range+1) {
                h += '<span class="pg-info">…</span>';
            }
        }
        h += '<button class="pg-btn" onclick="goPage(' + (currentPage+1) + ')" ' + (currentPage===totalPages?'disabled':'') + '><i class="bi bi-chevron-right"></i></button>';
        h += '<span class="pg-info">Page ' + currentPage + ' / ' + totalPages + '</span>';
        $('#paginationTop,#paginationBottom').html(h);
    }

    /* ── Refresh stats (global) ─────────────────────── */
    $('#btnRefreshStats').on('click', function () {
        var $btn = $(this).prop('disabled', true).html('<i class="bi bi-arrow-clockwise"></i> Loading…');
        $.get(LOG_STATS_URL, function (res) {
            globalStats = {
                total : res.total_all,
                today : res.total_today,
                week  : res.total_week,
                month : res.total_month,
                users : res.active_users,
            };
            // Only update display if not in filtered mode
            if (!$('#sc1').hasClass('filter-active')) {
                resetGlobalStats();
            }
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="bi bi-arrow-clockwise"></i> Refresh Stats');
        });
    });

    /* ── Clear logs ─────────────────────────────────── */
    $('#btnClearConfirm').on('click', function () {
        var $btn = $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Deleting…');
        $.ajax({
            url    : LOG_CLEAR_URL,
            method : 'POST',
            data   : { _token: CSRF, days: $('#clearDays').val() },
            success: function (res) { alert(res.message); $('#clearLogsModal').modal('hide'); loadLogs(); },
            error  : function () { alert('Failed to clear logs.'); },
            complete: function () { $btn.prop('disabled', false).html('<i class="bi bi-trash me-1"></i>Delete'); }
        });
    });

    /* ── Export CSV ─────────────────────────────────── */
    $('#btnExportCsv').on('click', function () {
        var rows = ['#,Date Time,Name,Email,Emp ID,Level,Action,Module,Description,Location,Login IP'];
        $('#logsTable tbody tr:not(.detail-row)').each(function (i) {
            var tds = $(this).find('td');
            if (tds.length < 10) return;
            var $userCell = tds.eq(2);
            rows.push([
                i + 1,
                '"' + tds.eq(1).text().trim().replace(/"/g,'""') + '"',
                '"' + $userCell.find('div').eq(0).text().trim().replace(/"/g,'""') + '"', // Name
                '"' + $userCell.find('div').eq(1).text().trim().replace(/"/g,'""') + '"', // Email
                '"' + $userCell.find('span').text().trim().replace(/"/g,'""') + '"',       // Emp ID
                '"' + tds.eq(3).text().trim().replace(/"/g,'""') + '"',
                '"' + tds.eq(4).text().trim().replace(/"/g,'""') + '"',
                '"' + tds.eq(5).text().trim().replace(/"/g,'""') + '"',
                '"' + tds.eq(6).text().trim().replace(/"/g,'""') + '"',
                '"' + tds.eq(7).text().trim().replace(/\s+/g,' ').replace(/"/g,'""') + '"',
                '"' + tds.eq(8).text().trim().replace(/"/g,'""') + '"',
            ].join(','));
        });
        var a  = document.createElement('a');
        a.href = URL.createObjectURL(new Blob([rows.join('\n')], {type:'text/csv;charset=utf-8;'}));
        a.download = 'activity_logs_' + moment().format('YYYY-MM-DD_HHmm') + '.csv';
        a.click();
    });

    /* ── goPage global ──────────────────────────────── */
    window.goPage = function (p) {
        if (p < 1 || p > totalPages) return;
        currentPage = p;
        loadLogs();
        $('html,body').animate({ scrollTop: $('.tbl-card').offset().top - 80 }, 200);
    };

    /* ── Initial load — NO date filter, show ALL data ─── */
    // Stats stay in global mode (all-time counts)
    // User applies filter to drill down and see filtered stats
    loadLogs();

}); // end ready
</script>

</body>
</html>
