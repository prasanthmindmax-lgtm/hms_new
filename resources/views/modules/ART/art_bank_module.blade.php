<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
/* Shell (same pattern as modules/checkin/index) */
body.art-bank-page{background:#f0f7f5!important;}
.mw{padding:20px 24px;}
/* ===== ART-Bank Module Styles ===== */
:root {
    --art-primary:   #0f7b6c;
    --art-dark:      #085f53;
    --art-light:     #e6f7f5;
    --art-accent:    #13c4a3;
    --art-purple:    #7c4dff;
    --art-orange:    #ff6d00;
    --art-red:       #e53935;
    --art-blue:      #1565c0;
}

/* --- Page Header --- */
.art-header {
    background: linear-gradient(135deg, var(--art-primary) 0%, var(--art-dark) 100%);
    color: #fff;
    padding: 26px 30px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 6px 20px rgba(15,123,108,.25);
    position: relative;
    overflow: hidden;
}
.art-header::after {
    content: '';
    position: absolute;
    right: -40px; top: -40px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,.06);
    border-radius: 50%;
}
.art-header h2 { margin: 0 0 4px; font-size: 22px; font-weight: 700; }
.art-header p  { margin: 0; font-size: 13px; opacity: .85; }
.art-badge {
    display: inline-block;
    background: rgba(255,255,255,.22);
    border-radius: 20px;
    padding: 3px 14px;
    font-size: 13px;
    font-weight: 700;
    margin-left: 10px;
    vertical-align: middle;
}

/* --- Stat Cards --- */
.art-stat-card {
    background: #fff;
    border-radius: 10px;
    padding: 16px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 20px;
    border-left: 4px solid var(--art-primary);
    transition: transform .18s, box-shadow .18s;
}
.art-stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.12); }
.art-stat-card.warning  { border-left-color: var(--art-orange); }
.art-stat-card.danger   { border-left-color: var(--art-red); }
.art-stat-card.info     { border-left-color: var(--art-blue); }
.art-stat-icon {
    width: 48px; height: 48px;
    border-radius: 10px;
    background: var(--art-light);
    color: var(--art-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.art-stat-card.warning  .art-stat-icon { background: #fff3e0; color: var(--art-orange); }
.art-stat-card.danger   .art-stat-icon { background: #fce4e4; color: var(--art-red); }
.art-stat-card.info     .art-stat-icon { background: #e3f0ff; color: var(--art-blue); }
.art-stat-val  { font-size: 26px; font-weight: 700; line-height: 1; color: #222; }
.art-stat-lbl  { font-size: 12px; color: #666; margin-top: 3px; }

/* --- Filter Panel --- */
.art-filter-panel {
    background: #fff;
    border-radius: 12px;
    padding: 20px 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    margin-bottom: 22px;
}
.art-filter-panel .filter-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--art-dark);
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--art-light);
    display: flex;
    align-items: center;
    gap: 8px;
}
.art-filter-panel .form-control {
    font-size: 13px;
    border-radius: 7px;
    border: 1px solid #d8e0ea;
    height: 36px;
    padding: 5px 10px;
}
.art-filter-panel label {
    font-size: 12px;
    font-weight: 600;
    color: #555;
    margin-bottom: 4px;
}
.btn-art-search {
    background: linear-gradient(135deg, var(--art-primary) 0%, var(--art-dark) 100%);
    color: #fff;
    border: none;
    padding: 8px 22px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}
.btn-art-search:hover { opacity: .88; transform: translateY(-1px); }
.btn-art-reset {
    background: #f4f6fb;
    color: #555;
    border: 1px solid #d8e0ea;
    padding: 8px 18px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}
.btn-art-export {
    background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%);
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

/* --- Table Container --- */
.art-table-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px 0 0;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    margin-bottom: 24px;
}
.art-table-topbar {
    padding: 0 20px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}

/* --- Show/Hide Columns --- */
.col-toggle-wrap { position: relative; display: inline-block; }
.btn-col-toggle {
    background: #fff;
    border: 1px solid #d8e0ea;
    color: #444;
    padding: 6px 14px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all .15s;
    white-space: nowrap;
}
.btn-col-toggle:hover { border-color: var(--art-primary); color: var(--art-primary); }
.btn-col-toggle .caret { font-size: 10px; }
.btn-fav {
    background: #fff8e1;
    border: 1px solid #ffe082;
    color: #f57f17;
    padding: 6px 14px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.btn-save-fav {
    background: #fff;
    border: 1px solid #d8e0ea;
    color: #555;
    padding: 6px 14px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.btn-save-fav:hover { border-color: var(--art-primary); color: var(--art-primary); }

.col-toggle-panel {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    z-index: 9999;
    background: #fff;
    border: 1px solid #d8e0ea;
    border-radius: 10px;
    box-shadow: 0 8px 28px rgba(0,0,0,.13);
    width: 270px;
    max-height: 420px;
    overflow-y: auto;
    padding: 0;
}
.col-toggle-panel::-webkit-scrollbar { width: 5px; }
.col-toggle-panel::-webkit-scrollbar-thumb { background: var(--art-accent); border-radius: 10px; }
.col-toggle-panel.open { display: block; }

.ctp-header {
    padding: 12px 14px 8px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 12px;
    font-weight: 700;
    color: var(--art-dark);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ctp-group-label {
    padding: 7px 14px 4px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #aaa;
    background: #fafafa;
    border-top: 1px solid #f0f0f0;
}
.ctp-item {
    padding: 6px 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #333;
    cursor: pointer;
    transition: background .12s;
}
.ctp-item:hover { background: #f5faf9; }
.ctp-item input[type="checkbox"] { width: 14px; height: 14px; accent-color: var(--art-primary); cursor: pointer; flex-shrink: 0; }
.ctp-item.always { color: #aaa; cursor: default; }
.ctp-item.always input { cursor: not-allowed; }
.ctp-hint { font-size: 10px; color: #bbb; margin-left: auto; white-space: nowrap; }
.art-table-topbar .show-entries {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; color: #555;
}
.art-table-topbar .show-entries select {
    width: 65px; font-size: 13px;
    border-radius: 6px; border: 1px solid #d8e0ea;
    padding: 3px 6px;
}
.art-table-topbar .search-box {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; color: #555;
}
.art-table-topbar .search-box input {
    width: 200px; font-size: 13px;
    border-radius: 6px; border: 1px solid #d8e0ea;
    padding: 5px 10px;
}

/* --- The Wide Table --- */
.art-table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.art-table-scroll::-webkit-scrollbar { height: 7px; }
.art-table-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
.art-table-scroll::-webkit-scrollbar-thumb { background: var(--art-accent); border-radius: 10px; }
.art-table-scroll::-webkit-scrollbar-thumb:hover { background: var(--art-primary); }

.art-table {
    width: 100%;
    min-width: 5320px;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 12px;
}
.art-table thead tr.group-row th {
    background: var(--art-dark);
    color: #fff;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: 8px 6px;
    border: 1px solid rgba(255,255,255,.12);
    white-space: nowrap;
}
.art-table thead tr.head-row th {
    background: #f0faf8;
    color: var(--art-dark);
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    padding: 9px 8px;
    border: 1px solid #d6ecea;
    white-space: nowrap;
    vertical-align: middle;
    position: sticky;
    top: 0;
}
/* sticky first col */
.art-table thead tr.group-row th:first-child,
.art-table thead tr.head-row th:first-child,
.art-table tbody td:first-child {
    position: sticky;
    left: 0;
    z-index: 3;
}
.art-table thead tr.group-row th:first-child { background: var(--art-dark); z-index: 4; }
.art-table thead tr.head-row th:first-child  { background: #f0faf8; z-index: 4; }
.art-table tbody td:first-child { background: #fff; z-index: 2; }

.art-table tbody td {
    padding: 9px 8px;
    border: 1px solid #e8eeee;
    text-align: center;
    vertical-align: middle;
    color: #333;
    white-space: nowrap;
}
.art-table tbody tr:hover td { background: #f5faf9; }
.art-table tbody tr:nth-child(even) td { background: #fafcfc; }
.art-table tbody tr:nth-child(even):hover td { background: #f0faf8; }

/* Coloured group headers */
.grp-basic    { background: #0f7b6c !important; }
.grp-family   { background: #5c6bc0 !important; }
.grp-docs     { background: #8e24aa !important; }
.grp-medical  { background: #e65100 !important; }
.grp-consent  { background: #2e7d32 !important; }
.grp-recip    { background: #1565c0 !important; }
.grp-ot       { background: #4527a0 !important; }
.grp-loc      { background: #00838f !important; }
.grp-rx       { background: #6d4c41 !important; }
.grp-expense  { background: #37474f !important; }

/* --- Badges & Pills --- */
.badge-status {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.badge-active   { background: #e6f7f5; color: #0f7b6c; }
.badge-inactive { background: #fce4e4; color: #c62828; }
.badge-pending  { background: #fff3e0; color: #e65100; }
.badge-verified { background: #e8f5e9; color: #2e7d32; }
.badge-unverified { background: #fce4e4; color: #c62828; }

.gender-badge {
    display: inline-block;
    padding: 2px 9px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 700;
}
.gender-f { background: #fce4ec; color: #c2185b; }
.gender-m { background: #e3f2fd; color: #1565c0; }

.tick-yes  { color: #2e7d32; font-size: 15px; }
.tick-no   { color: #c62828; font-size: 15px; }
.tick-warn { color: #e65100; font-size: 15px; }

.doc-btn {
    display: inline-block;
    padding: 2px 9px;
    border-radius: 5px;
    font-size: 10px;
    font-weight: 600;
    border: 1px solid currentColor;
    cursor: pointer;
    text-decoration: none;
}
.doc-done { color: #2e7d32; }
.doc-miss { color: #c62828; }
.doc-pend { color: #e65100; }

.photo-thumb {
    width: 36px; height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #d8e0ea;
}
.photo-placeholder {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: #f0faf8;
    color: var(--art-primary);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 16px;
    border: 2px solid #d6ecea;
}

/* Pagination */
.art-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-top: 1px solid #eef0f2;
    font-size: 13px;
    flex-wrap: wrap;
    gap: 10px;
}
.art-pagination .pg-info { color: #666; }
.art-pagination .pg-btns { display: flex; gap: 5px; }
.art-pagination .pg-btns button {
    border: 1px solid #d8e0ea;
    background: #fff;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    color: #444;
    transition: all .15s;
}
.art-pagination .pg-btns button:hover,
.art-pagination .pg-btns button.active {
    background: var(--art-primary);
    color: #fff;
    border-color: var(--art-primary);
}
.art-pagination .pg-btns button:disabled { opacity: .45; cursor: not-allowed; }

/* Loading overlay */
.art-loading {
    display: none;
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,.45);
    z-index: 9999;
    justify-content: center; align-items: center;
}
.art-spinner {
    border: 4px solid rgba(255,255,255,.3);
    border-top: 4px solid var(--art-accent);
    border-radius: 50%;
    width: 52px; height: 52px;
    animation: art-spin 1s linear infinite;
}
@keyframes art-spin { 0%{transform:rotate(0)} 100%{transform:rotate(360deg)} }
</style>
<body class="art-bank-page">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')
<div class="pc-container">
<div class="pc-content mw">

<!-- Loading Overlay -->
<div class="art-loading" id="artLoading">
    <div class="art-spinner"></div>
</div>

    <!-- ===== PAGE HEADER ===== -->
    <div class="art-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2><i class="fa fa-hospital-o"></i> ART — Donor Bank
                    <span class="art-badge">Module</span>
                </h2>
                <p>Assisted Reproductive Technology · Donor Registry &amp; Tracking</p>
            </div>
        </div>
    </div>

    <!-- ===== STAT CARDS ===== -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="art-stat-card">
                <div class="art-stat-icon"><i class="fa fa-users"></i></div>
                <div>
                    <div class="art-stat-val">248</div>
                    <div class="art-stat-lbl">Total Donors</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="art-stat-card" style="border-left-color:#c2185b;">
                <div class="art-stat-icon" style="background:#fce4ec;color:#c2185b;"><i class="fa fa-female"></i></div>
                <div>
                    <div class="art-stat-val">193</div>
                    <div class="art-stat-lbl">Female Donors</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="art-stat-card info">
                <div class="art-stat-icon"><i class="fa fa-male"></i></div>
                <div>
                    <div class="art-stat-val">55</div>
                    <div class="art-stat-lbl">Male Donors</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="art-stat-card warning">
                <div class="art-stat-icon"><i class="fa fa-clock-o"></i></div>
                <div>
                    <div class="art-stat-val">31</div>
                    <div class="art-stat-lbl">Pending Verification</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FILTER PANEL ===== -->
    <div class="art-filter-panel">
        <div class="filter-title">
            <i class="fa fa-filter" style="color:var(--art-primary);"></i> Search &amp; Filters
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Zone</label>
                    <select class="form-control" id="filterZone">
                        <option value="">— Select Zone —</option>
                        <option>North Zone</option>
                        <option>South Zone</option>
                        <option>East Zone</option>
                        <option>West Zone</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Center / Branch</label>
                    <select class="form-control" id="filterCenter">
                        <option value="">— Select Center —</option>
                        <option>Chennai - Anna Nagar</option>
                        <option>Madurai - Main</option>
                        <option>Trichy - Central</option>
                        <option>Coimbatore - RS Puram</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Donor Type</label>
                    <select class="form-control" id="filterDonorType">
                        <option value="">— All —</option>
                        <option>Female</option>
                        <option>Male</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="filterStatus">
                        <option value="">— All Status —</option>
                        <option>Active</option>
                        <option>Inactive</option>
                        <option>Pending</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Marital Status</label>
                    <select class="form-control" id="filterMarital">
                        <option value="">— All —</option>
                        <option>Married</option>
                        <option>Unmarried</option>
                        <option>Divorced</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Source</label>
                    <select class="form-control" id="filterSource">
                        <option value="">— All Sources —</option>
                        <option>WhatsApp</option>
                        <option>Facebook</option>
                        <option>Instagram</option>
                        <option>Walk-in</option>
                        <option>Referral</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Date Filter By</label>
                    <select class="form-control" id="filterDateBy">
                        <option value="register_date">Register Date</option>
                        <option value="created">Created</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Quick Range</label>
                    <select class="form-control" id="filterQuickRange">
                        <option value="">— Select Range —</option>
                        <option>Today</option>
                        <option>This Week</option>
                        <option>This Month</option>
                        <option>Last 3 Months</option>
                        <option>Custom</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" id="customDateFrom" style="display:none;">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" id="filterFrom">
                </div>
            </div>
            <div class="col-md-3 col-sm-6" id="customDateTo" style="display:none;">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" id="filterTo">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Aadhar Verified</label>
                    <select class="form-control" id="filterAadharVerified">
                        <option value="">— All —</option>
                        <option>Verified</option>
                        <option>Not Verified</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Donor Handled By</label>
                    <select class="form-control" id="filterHandledBy">
                        <option value="">— All —</option>
                        <option>Dr. Ramesh K.</option>
                        <option>Dr. Selvi M.</option>
                        <option>Dr. Anand B.</option>
                        <option>Dr. Mani G.</option>
                        <option>Dr. Bala R.</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-right" style="padding-top:6px;">
                <button class="btn-art-search" onclick="applyFilters()">
                    <i class="fa fa-search"></i> Search
                </button>
                &nbsp;
                <button class="btn-art-reset" onclick="resetFilters()">
                    <i class="fa fa-refresh"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- ===== TABLE CARD ===== -->
    <div class="art-table-card">
        <div class="art-table-topbar">
            <div class="d-flex align-items-center" style="gap:14px; flex-wrap:wrap;">
                <div class="show-entries">
                    Show
                    <select id="entriesPerPage">
                        <option>10</option>
                        <option selected>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                    entries
                </div>
                <span style="font-size:13px;color:#888;border-left:1px solid #e0e0e0;padding-left:14px;">
                    <i class="fa fa-table" style="color:var(--art-primary);"></i>
                    Total: <strong>248</strong> Donors &nbsp;|&nbsp;
                    Showing: <strong id="showingCount">1 – 6</strong>
                </span>
            </div>
            <div class="d-flex align-items-center" style="gap:8px; flex-wrap:wrap;">

                <!-- Show/Hide Columns -->
                <div class="col-toggle-wrap" id="colToggleWrap">
                    <button class="btn-col-toggle" id="btnColToggle" onclick="toggleColPanel(event)">
                        <i class="fa fa-columns"></i> Show/Hide Columns <span class="caret">&#9660;</span>
                    </button>
                    <div class="col-toggle-panel" id="colTogglePanel">
                        <div class="ctp-header">
                            <span>Columns</span>
                            <label class="ctp-item" style="padding:0;margin:0;">
                                <input type="checkbox" id="ctpSelectAll" onchange="ctpToggleAll(this.checked)"> Select All
                            </label>
                        </div>
                        <!-- Built by JS -->
                        <div id="ctpBody"></div>
                    </div>
                </div>

                <!-- Favourite -->
                <button class="btn-fav" id="btnLoadFav" onclick="ctpLoadFavourite()" title="Load saved favourite columns">
                    <i class="fa fa-star"></i> Favourite
                </button>

                <!-- Save as Favourite -->
                <button class="btn-save-fav" onclick="ctpSaveFavourite()" title="Save current column selection as favourite">
                    <i class="fa fa-floppy-o"></i> Save as Favourite
                </button>

                <!-- Quick search -->
                <div class="search-box">
                    <i class="fa fa-search" style="color:#aaa;"></i>
                    <input type="text" id="tableSearch" placeholder="Quick search…" oninput="quickSearch(this.value)">
                </div>

            </div>
        </div>

        <!-- ===== SCROLLABLE TABLE ===== -->
        <div class="art-table-scroll">
            <table class="art-table" id="artBankTable">
                <thead>
                    <!-- GROUP HEADER ROW -->
                    <tr class="group-row">
                        <th rowspan="2" class="grp-basic" style="min-width:50px;">#</th>
                        <th colspan="8" class="grp-basic">Basic Information</th>
                        <th colspan="5" class="grp-family">Marital &amp; Family</th>
                        <th colspan="7" class="grp-docs">Documents &amp; Proofs</th>
                        <th colspan="9" class="grp-medical">Medical Tests</th>
                        <th colspan="3" class="grp-consent">Consents &amp; Bonds</th>
                        <th colspan="3" class="grp-recip">Recipient Info</th>
                        <th colspan="8" class="grp-ot">OT Details</th>
                        <th colspan="3" class="grp-loc">Location</th>
                        <th colspan="3" class="grp-rx">Prescription</th>
                        <th colspan="5" class="grp-expense">Expenses</th>
                    </tr>
                    <!-- COLUMN HEADER ROW -->
                    <tr class="head-row">
                        <th style="min-width:110px;">ART-BNK ID</th>
                        <th style="min-width:90px;">Status</th>
                        <th style="min-width:105px;">Register Date</th>
                        <th style="min-width:70px;">Donor</th>
                        <th style="min-width:120px;">Location</th>
                        <th style="min-width:115px;">Phone</th>
                        <th style="min-width:65px;">Age</th>
                        <th style="min-width:70px;">Photo</th>
                        <th style="min-width:115px;">Marital Status</th>
                        <th style="min-width:110px;">Children</th>
                        <th style="min-width:90px;">Child Age</th>
                        <th style="min-width:130px;">Child Birth Cert.</th>
                        <th style="min-width:120px;">Marriage Photo</th>
                        <th style="min-width:110px;">Aadhar</th>
                        <th style="min-width:125px;">Aadhar Number</th>
                        <th style="min-width:115px;">Aadhar Verified</th>
                        <th style="min-width:130px;">Marriage Cert.</th>
                        <th style="min-width:100px;">Insurance Copy</th>
                        <th style="min-width:100px;">ART Enrol. No.</th>
                        <th style="min-width:105px;">PAN Card</th>
                        <th style="min-width:75px;">TV Scan</th>
                        <th style="min-width:90px;">Serology</th>
                        <th style="min-width:120px;">HB Electrophoresis</th>
                        <th style="min-width:75px;">Semen</th>
                        <th style="min-width:60px;">BBT</th>
                        <th style="min-width:60px;">TFT</th>
                        <th style="min-width:110px;">Cardiac Fitness</th>
                        <th style="min-width:60px;">ECG</th>
                        <th style="min-width:120px;">Informed Consent</th>
                        <th style="min-width:110px;">Donor Consent</th>
                        <th style="min-width:100px;">Donor Bond</th>
                        <th style="min-width:85px;">Egg Donor Age</th>
                        <th style="min-width:130px;">Recipient Name</th>
                        <th style="min-width:120px;">Recipient MRD</th>
                        <th style="min-width:115px;">Anesthesiologist</th>
                        <th style="min-width:90px;">IP Number</th>
                        <th style="min-width:110px;">Pre Pick-Up Photo</th>
                        <th style="min-width:115px;">Post Pick-Up Photo</th>
                        <th style="min-width:110px;">Pre Pick-Up Video</th>
                        <th style="min-width:115px;">Post Pick-Up Video</th>
                        <th style="min-width:120px;">Tubbing By (ECIID)</th>
                        <th style="min-width:100px;">OT Technical</th>
                        <th style="min-width:85px;">OPU Summary</th>
                        <th style="min-width:100px;">Zone Name</th>
                        <th style="min-width:140px;">Center Name</th>
                        <th style="min-width:160px;">Procedure Branch/Zone</th>
                        <th style="min-width:100px;">Pre-Operative</th>
                        <th style="min-width:110px;">During Operative</th>
                        <th style="min-width:100px;">Post-Operative</th>
                        <th style="min-width:130px;">Expected Expense (Travel)</th>
                        <th style="min-width:130px;">Expected Expense (Food)</th>
                        <th style="min-width:130px;">Approved Expense (Travel)</th>
                        <th style="min-width:130px;">Approved Expense (Food)</th>
                        <th style="min-width:110px;">UTR Number</th>
                    </tr>
                </thead>
                <tbody id="artTableBody">

                    @php
                    /* ============================================================
                       DUMMY DATA — 6 rows for UI preview
                       Replace with dynamic data from controller when backend is ready
                       ============================================================ */
                    $dummy = [
                        [
                            'id'           => 'ART-2026-001',
                            'status'       => 'Active',
                            'reg_date'     => '15 Jan 2026',
                            'gender'       => 'F',
                            'location'     => 'Chennai (Velachery)',
                            'phone'        => '98XX XXXX 01',
                            'age'          => 27,
                            'marital'      => 'Married',
                            'children'     => 'Have Child',
                            'child_age'    => '4 yrs',
                            'child_cert'   => 'done',
                            'marriage_photo'=> 'done',
                            'aadhar'       => 'done',
                            'aadhar_no'    => '7XXX XXXX X101',
                            'aadhar_ver'   => true,
                            'marriage_cert'=> 'done',
                            'insurance'    => 'done',
                            'art_enrol'    => 'ART-E-0012',
                            'pan'          => 'done',
                            'tv_scan'      => 'done',
                            'serology'     => 'done',
                            'hb_electrophoresis' => 'done',
                            'semen'        => '-',
                            'bbt'          => 'done',
                            'tft'          => 'done',
                            'cardiac'      => 'done',
                            'ecg'          => 'done',
                            'inf_consent'  => 'done',
                            'donor_consent'=> 'done',
                            'donor_bond'   => 'done',
                            'egg_age'      => '27 ✓',
                            'recip_name'   => 'Mrs. Kavitha R.',
                            'recip_mrd'    => 'MRD-8821',
                            'anesthesio'   => 'Dr. Ramesh K.',
                            'ip_no'        => 'IP-20260115',
                            'pre_photo'    => 'done',
                            'post_photo'   => 'done',
                            'pre_video'    => 'done',
                            'post_video'   => 'done',
                            'tubbing'      => 'ECIID-04',
                            'ot_tech'      => 'Tech-Suresh',
                            'opu'          => 'done',
                            'zone'         => 'South Zone',
                            'center'       => 'Chennai – Anna Nagar',
                            'proc_branch'  => 'Chennai – Anna Nagar',
                            'rx_pre'       => 'done',
                            'rx_during'    => 'done',
                            'rx_post'      => 'done',
                            'exp_travel'   => '₹ 1,200',
                            'exp_food'     => '₹ 600',
                            'app_travel'   => '₹ 1,200',
                            'app_food'     => '₹ 600',
                            'utr'          => 'UTR9832110041',
                        ],
                        [
                            'id'           => 'ART-2026-002',
                            'status'       => 'Active',
                            'reg_date'     => '20 Jan 2026',
                            'gender'       => 'F',
                            'location'     => 'Madurai',
                            'phone'        => '91XX XXXX 02',
                            'age'          => 31,
                            'marital'      => 'Divorced',
                            'children'     => 'Without Child',
                            'child_age'    => '-',
                            'child_cert'   => '-',
                            'marriage_photo'=> 'done',
                            'aadhar'       => 'done',
                            'aadhar_no'    => '8XXX XXXX X202',
                            'aadhar_ver'   => true,
                            'marriage_cert'=> 'done',
                            'insurance'    => 'pending',
                            'art_enrol'    => 'ART-E-0019',
                            'pan'          => 'done',
                            'tv_scan'      => 'done',
                            'serology'     => 'pending',
                            'hb_electrophoresis' => 'pending',
                            'semen'        => '-',
                            'bbt'          => 'done',
                            'tft'          => 'pending',
                            'cardiac'      => 'done',
                            'ecg'          => 'done',
                            'inf_consent'  => 'done',
                            'donor_consent'=> 'done',
                            'donor_bond'   => 'pending',
                            'egg_age'      => '31 ✓',
                            'recip_name'   => 'Mrs. Priya S.',
                            'recip_mrd'    => 'MRD-9134',
                            'anesthesio'   => 'Dr. Selvi M.',
                            'ip_no'        => 'IP-20260120',
                            'pre_photo'    => 'done',
                            'post_photo'   => 'missing',
                            'pre_video'    => 'done',
                            'post_video'   => 'missing',
                            'tubbing'      => 'ECIID-07',
                            'ot_tech'      => 'Tech-Kumar',
                            'opu'          => 'pending',
                            'zone'         => 'South Zone',
                            'center'       => 'Madurai – Main',
                            'proc_branch'  => 'Madurai – Main',
                            'rx_pre'       => 'done',
                            'rx_during'    => 'pending',
                            'rx_post'      => '-',
                            'exp_travel'   => '₹ 2,500',
                            'exp_food'     => '₹ 800',
                            'app_travel'   => '₹ 2,000',
                            'app_food'     => '₹ 800',
                            'utr'          => 'UTR8800229901',
                        ],
                        [
                            'id'           => 'ART-2026-003',
                            'status'       => 'Pending',
                            'reg_date'     => '02 Feb 2026',
                            'gender'       => 'M',
                            'location'     => 'Trichy',
                            'phone'        => '70XX XXXX 03',
                            'age'          => 29,
                            'marital'      => 'Married',
                            'children'     => 'Have Child',
                            'child_age'    => '2 yrs',
                            'child_cert'   => 'pending',
                            'marriage_photo'=> 'done',
                            'aadhar'       => 'done',
                            'aadhar_no'    => '6XXX XXXX X303',
                            'aadhar_ver'   => false,
                            'marriage_cert'=> 'done',
                            'insurance'    => 'missing',
                            'art_enrol'    => '-',
                            'pan'          => 'pending',
                            'tv_scan'      => '-',
                            'serology'     => 'done',
                            'hb_electrophoresis' => 'pending',
                            'semen'        => 'done',
                            'bbt'          => '-',
                            'tft'          => '-',
                            'cardiac'      => 'pending',
                            'ecg'          => 'pending',
                            'inf_consent'  => 'missing',
                            'donor_consent'=> 'missing',
                            'donor_bond'   => '-',
                            'egg_age'      => '-',
                            'recip_name'   => '-',
                            'recip_mrd'    => '-',
                            'anesthesio'   => '-',
                            'ip_no'        => '-',
                            'pre_photo'    => '-',
                            'post_photo'   => '-',
                            'pre_video'    => '-',
                            'post_video'   => '-',
                            'tubbing'      => '-',
                            'ot_tech'      => '-',
                            'opu'          => '-',
                            'zone'         => 'North Zone',
                            'center'       => 'Trichy – Central',
                            'proc_branch'  => '-',
                            'rx_pre'       => '-',
                            'rx_during'    => '-',
                            'rx_post'      => '-',
                            'exp_travel'   => '-',
                            'exp_food'     => '-',
                            'app_travel'   => '-',
                            'app_food'     => '-',
                            'utr'          => '-',
                        ],
                        [
                            'id'           => 'ART-2026-004',
                            'status'       => 'Active',
                            'reg_date'     => '10 Feb 2026',
                            'gender'       => 'F',
                            'location'     => 'Coimbatore',
                            'phone'        => '80XX XXXX 04',
                            'age'          => 24,
                            'marital'      => 'Unmarried',
                            'children'     => '-',
                            'child_age'    => '-',
                            'child_cert'   => '-',
                            'marriage_photo'=> '-',
                            'aadhar'       => 'done',
                            'aadhar_no'    => '5XXX XXXX X404',
                            'aadhar_ver'   => true,
                            'marriage_cert'=> '-',
                            'insurance'    => 'done',
                            'art_enrol'    => 'ART-E-0031',
                            'pan'          => 'done',
                            'tv_scan'      => 'done',
                            'serology'     => 'done',
                            'hb_electrophoresis' => 'done',
                            'semen'        => '-',
                            'bbt'          => 'done',
                            'tft'          => 'done',
                            'cardiac'      => 'done',
                            'ecg'          => 'done',
                            'inf_consent'  => 'done',
                            'donor_consent'=> 'done',
                            'donor_bond'   => 'done',
                            'egg_age'      => '24 ✓',
                            'recip_name'   => 'Mrs. Divya T.',
                            'recip_mrd'    => 'MRD-7712',
                            'anesthesio'   => 'Dr. Anand B.',
                            'ip_no'        => 'IP-20260210',
                            'pre_photo'    => 'done',
                            'post_photo'   => 'done',
                            'pre_video'    => 'done',
                            'post_video'   => 'done',
                            'tubbing'      => 'ECIID-03',
                            'ot_tech'      => 'Tech-Priya',
                            'opu'          => 'done',
                            'zone'         => 'West Zone',
                            'center'       => 'Coimbatore – RS Puram',
                            'proc_branch'  => 'Coimbatore – RS Puram',
                            'rx_pre'       => 'done',
                            'rx_during'    => 'done',
                            'rx_post'      => 'done',
                            'exp_travel'   => '₹ 1,800',
                            'exp_food'     => '₹ 500',
                            'app_travel'   => '₹ 1,800',
                            'app_food'     => '₹ 500',
                            'utr'          => 'UTR7711338820',
                        ],
                        [
                            'id'           => 'ART-2026-005',
                            'status'       => 'Inactive',
                            'reg_date'     => '18 Mar 2026',
                            'gender'       => 'F',
                            'location'     => 'Salem',
                            'phone'        => '63XX XXXX 05',
                            'age'          => 33,
                            'marital'      => 'Married',
                            'children'     => 'Have Child',
                            'child_age'    => '6 yrs',
                            'child_cert'   => 'done',
                            'marriage_photo'=> 'done',
                            'aadhar'       => 'done',
                            'aadhar_no'    => '4XXX XXXX X505',
                            'aadhar_ver'   => true,
                            'marriage_cert'=> 'done',
                            'insurance'    => 'done',
                            'art_enrol'    => 'ART-E-0044',
                            'pan'          => 'done',
                            'tv_scan'      => 'done',
                            'serology'     => 'done',
                            'hb_electrophoresis' => 'missing',
                            'semen'        => '-',
                            'bbt'          => 'done',
                            'tft'          => 'done',
                            'cardiac'      => 'done',
                            'ecg'          => 'done',
                            'inf_consent'  => 'done',
                            'donor_consent'=> 'done',
                            'donor_bond'   => 'done',
                            'egg_age'      => '33 ✓',
                            'recip_name'   => 'Mrs. Nithya P.',
                            'recip_mrd'    => 'MRD-6643',
                            'anesthesio'   => 'Dr. Mani G.',
                            'ip_no'        => 'IP-20260318',
                            'pre_photo'    => 'done',
                            'post_photo'   => 'done',
                            'pre_video'    => 'missing',
                            'post_video'   => 'missing',
                            'tubbing'      => 'ECIID-09',
                            'ot_tech'      => 'Tech-Ravi',
                            'opu'          => 'done',
                            'zone'         => 'South Zone',
                            'center'       => 'Salem – Omalur Road',
                            'proc_branch'  => 'Salem – Omalur Road',
                            'rx_pre'       => 'done',
                            'rx_during'    => 'done',
                            'rx_post'      => 'done',
                            'exp_travel'   => '₹ 3,000',
                            'exp_food'     => '₹ 700',
                            'app_travel'   => '₹ 2,500',
                            'app_food'     => '₹ 700',
                            'utr'          => 'UTR6609887712',
                        ],
                        [
                            'id'           => 'ART-2026-006',
                            'status'       => 'Active',
                            'reg_date'     => '05 Apr 2026',
                            'gender'       => 'M',
                            'location'     => 'Tirunelveli',
                            'phone'        => '94XX XXXX 06',
                            'age'          => 28,
                            'marital'      => 'Married',
                            'children'     => 'Without Child',
                            'child_age'    => '-',
                            'child_cert'   => '-',
                            'marriage_photo'=> 'done',
                            'aadhar'       => 'done',
                            'aadhar_no'    => '3XXX XXXX X606',
                            'aadhar_ver'   => true,
                            'marriage_cert'=> 'done',
                            'insurance'    => 'done',
                            'art_enrol'    => 'ART-E-0058',
                            'pan'          => 'done',
                            'tv_scan'      => '-',
                            'serology'     => 'done',
                            'hb_electrophoresis' => '-',
                            'semen'        => 'done',
                            'bbt'          => '-',
                            'tft'          => '-',
                            'cardiac'      => 'done',
                            'ecg'          => 'done',
                            'inf_consent'  => 'done',
                            'donor_consent'=> 'done',
                            'donor_bond'   => 'done',
                            'egg_age'      => '-',
                            'recip_name'   => 'Mrs. Lavanya K.',
                            'recip_mrd'    => 'MRD-5521',
                            'anesthesio'   => 'Dr. Bala R.',
                            'ip_no'        => 'IP-20260405',
                            'pre_photo'    => 'done',
                            'post_photo'   => 'done',
                            'pre_video'    => 'done',
                            'post_video'   => 'done',
                            'tubbing'      => 'ECIID-02',
                            'ot_tech'      => 'Tech-Deepa',
                            'opu'          => 'done',
                            'zone'         => 'South Zone',
                            'center'       => 'Tirunelveli – Main',
                            'proc_branch'  => 'Tirunelveli – Main',
                            'rx_pre'       => 'done',
                            'rx_during'    => 'done',
                            'rx_post'      => 'done',
                            'exp_travel'   => '₹ 4,000',
                            'exp_food'     => '₹ 900',
                            'app_travel'   => '₹ 4,000',
                            'app_food'     => '₹ 900',
                            'utr'          => 'UTR5500112299',
                        ],
                    ];

                    /* Helper functions (guarded: view may render more than once per request) */
                    if (!function_exists('artDocCell')) {
                        function artDocCell($val) {
                            if ($val === 'done')    return '<span class="doc-btn doc-done" onclick="artCantAccess()" style="cursor:pointer;"><i class="fa fa-check"></i> Done</span>';
                            if ($val === 'pending') return '<span class="doc-btn doc-pend"><i class="fa fa-clock-o"></i> Pending</span>';
                            if ($val === 'missing') return '<span class="doc-btn doc-miss"><i class="fa fa-times"></i> Missing</span>';
                            return '<span style="color:#bbb;">—</span>';
                        }
                    }
                    if (!function_exists('artStatusBadge')) {
                        function artStatusBadge($s) {
                            $map = ['Active'=>'badge-active','Inactive'=>'badge-inactive','Pending'=>'badge-pending'];
                            $cls = $map[$s] ?? 'badge-pending';
                            return "<span class=\"badge-status $cls\">$s</span>";
                        }
                    }
                    @endphp

                    @foreach($dummy as $i => $d)
                        @php
                        $sno = $i + 1;
                        $genderBadge = $d['gender'] === 'F'
                            ? '<span class="gender-badge gender-f"><i class="fa fa-female"></i> Female</span>'
                            : '<span class="gender-badge gender-m"><i class="fa fa-male"></i> Male</span>';
                        $aadharVerIcon = $d['aadhar_ver']
                            ? '<span class="tick-yes"><i class="fa fa-check-circle"></i> Verified</span>'
                            : '<span class="tick-no"><i class="fa fa-times-circle"></i> Not Verified</span>';
                        $photoEl = '<div class="photo-placeholder"><i class="fa fa-user"></i></div>';
                        $eggAgeVal = ($d['gender'] === 'F' && $d['egg_age'] !== '-')
                            ? '<span style="color:#2e7d32;font-weight:700;">'.$d['egg_age'].'</span>'
                            : '<span style="color:#bbb;">—</span>';
                        @endphp
                    <tr>
                        <td><strong>{{ $sno }}</strong></td>
                        <!-- Basic Info -->
                        <td><a href="{{ route('report.art_bank_detail', $d['id']) }}" style="color:var(--art-primary);font-size:11px;font-weight:700;text-decoration:none;border-bottom:1px dashed var(--art-primary);" title="View Donor Profile">{{ $d['id'] }}</a></td>
                        <td>{!! artStatusBadge($d['status']) !!}</td>
                        <td>{{ $d['reg_date'] }}</td>
                        <td>{!! $genderBadge !!}</td>
                        <td style="text-align:left;"><i class="fa fa-map-marker" style="color:var(--art-accent);margin-right:4px;"></i>{{ $d['location'] }}</td>
                        <td>{{ $d['phone'] }}</td>
                        <td>{{ $d['age'] }} yrs</td>
                        <td>{!! $photoEl !!}</td>
                        <!-- Marital & Family -->
                        <td>
                            @php
                            $mc = ['Married'=>'#5c6bc0','Unmarried'=>'#2e7d32','Divorced'=>'#e53935'];
                            $col = $mc[$d['marital']] ?? '#555';
                            @endphp
                            <span style="font-weight:700;color:{{ $col }};">{{ $d['marital'] }}</span>
                        </td>
                        <td>{{ $d['children'] !== '-' ? $d['children'] : '<span style="color:#bbb;">—</span>' }}</td>
                        <td>{{ $d['child_age'] !== '-' ? $d['child_age'] : '<span style="color:#bbb;">—</span>' }}</td>
                        <td>{!! artDocCell($d['child_cert']) !!}</td>
                        <td>{!! artDocCell($d['marriage_photo']) !!}</td>
                        <!-- Documents -->
                        <td>{!! artDocCell($d['aadhar']) !!}</td>
                        <td style="font-family:monospace;font-size:11px;color:#555;">{{ $d['aadhar_no'] }}</td>
                        <td>{!! $aadharVerIcon !!}</td>
                        <td>{!! artDocCell($d['marriage_cert']) !!}</td>
                        <td>{!! artDocCell($d['insurance']) !!}</td>
                        <td><span style="font-weight:700;color:var(--art-dark);font-size:11px;">{!! $d['art_enrol'] !== '-' ? $d['art_enrol'] : '<span style="color:#bbb;">—</span>' !!}</span></td>
                        <td>{!! artDocCell($d['pan']) !!}</td>
                        <!-- Medical Tests -->
                        <td>{!! artDocCell($d['tv_scan']) !!}</td>
                        <td>{!! artDocCell($d['serology']) !!}</td>
                        <td>{!! artDocCell($d['hb_electrophoresis']) !!}</td>
                        <td>{!! artDocCell($d['semen']) !!}</td>
                        <td>{!! artDocCell($d['bbt']) !!}</td>
                        <td>{!! artDocCell($d['tft']) !!}</td>
                        <td>{!! artDocCell($d['cardiac']) !!}</td>
                        <td>{!! artDocCell($d['ecg']) !!}</td>
                        <td>{!! artDocCell($d['inf_consent']) !!}</td>
                        <!-- Consents -->
                        <td>{!! artDocCell($d['donor_consent']) !!}</td>
                        <td>{!! artDocCell($d['donor_bond']) !!}</td>
                        <td>{!! $eggAgeVal !!}</td>
                        <!-- Recipient -->
                        <td style="text-align:left;">{!! $d['recip_name'] !== '-' ? $d['recip_name'] : '<span style="color:#bbb;">—</span>' !!}</td>
                        <td>{!! $d['recip_mrd'] !== '-' ? '<span style="font-weight:700;color:#1565c0;font-size:11px;">'.$d['recip_mrd'].'</span>' : '<span style="color:#bbb;">—</span>' !!}</td>
                        <td>{!! $d['anesthesio'] !== '-' ? $d['anesthesio'] : '<span style="color:#bbb;">—</span>' !!}</td>
                        <!-- OT Details -->
                        <td>{!! $d['ip_no'] !== '-' ? '<span style="font-size:11px;font-weight:600;color:#4527a0;">'.$d['ip_no'].'</span>' : '<span style="color:#bbb;">—</span>' !!}</td>
                        <td>{!! artDocCell($d['pre_photo']) !!}</td>
                        <td>{!! artDocCell($d['post_photo']) !!}</td>
                        <td>{!! artDocCell($d['pre_video']) !!}</td>
                        <td>{!! artDocCell($d['post_video']) !!}</td>
                        <td>{!! $d['tubbing'] !== '-' ? '<span style="font-weight:700;font-size:11px;color:#4527a0;">'.$d['tubbing'].'</span>' : '<span style="color:#bbb;">—</span>' !!}</td>
                        <td>{!! $d['ot_tech'] !== '-' ? $d['ot_tech'] : '<span style="color:#bbb;">—</span>' !!}</td>
                        <td>{!! artDocCell($d['opu']) !!}</td>
                        <!-- Location -->
                        <td><span style="font-size:11px;font-weight:600;color:#00838f;">{{ $d['zone'] }}</span></td>
                        <td style="text-align:left;font-size:11px;">{{ $d['center'] }}</td>
                        <td style="text-align:left;font-size:11px;">{!! $d['proc_branch'] !== '-' ? $d['proc_branch'] : '<span style="color:#bbb;">—</span>' !!}</td>
                        <!-- Prescription -->
                        <td>{!! artDocCell($d['rx_pre']) !!}</td>
                        <td>{!! artDocCell($d['rx_during']) !!}</td>
                        <td>{!! artDocCell($d['rx_post']) !!}</td>
                        <!-- Expenses -->
                        <td>{!! $d['exp_travel'] !== '-' ? '<span style="font-weight:600;color:#2e7d32;">'.$d['exp_travel'].'</span>' : '<span style="color:#bbb;">—</span>' !!}</td>
                        <td>{!! $d['exp_food']   !== '-' ? '<span style="font-weight:600;color:#2e7d32;">'.$d['exp_food'].'</span>'   : '<span style="color:#bbb;">—</span>' !!}</td>
                        <td>{!! $d['app_travel'] !== '-' ? '<span style="font-weight:600;color:#1565c0;">'.$d['app_travel'].'</span>' : '<span style="color:#bbb;">—</span>' !!}</td>
                        <td>{!! $d['app_food']   !== '-' ? '<span style="font-weight:600;color:#1565c0;">'.$d['app_food'].'</span>'   : '<span style="color:#bbb;">—</span>' !!}</td>
                        <td style="font-family:monospace;font-size:11px;">{!! $d['utr'] !== '-' ? $d['utr'] : '<span style="color:#bbb;">—</span>' !!}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="art-pagination">
            <div class="pg-info">Showing <strong>1 – 6</strong> of <strong>248</strong> entries</div>
            <div class="pg-btns">
                <button disabled><i class="fa fa-chevron-left"></i></button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <span style="padding:5px 6px;font-size:12px;color:#aaa;">…</span>
                <button>25</button>
                <button><i class="fa fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

</div><!-- /pc-content -->
</div><!-- /pc-container -->

<!-- ===== Can't Access Modal ===== -->
<div class="modal fade" id="artCantAccessModal" tabindex="-1" role="dialog" aria-labelledby="artCantAccessLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document" style="max-width:340px;">
        <div class="modal-content" style="border-radius:12px;border:none;overflow:hidden;">
            <div class="modal-body text-center" style="padding:36px 28px 28px;">
                <div style="width:60px;height:60px;background:#fce4e4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fa fa-lock" style="font-size:26px;color:#c62828;"></i>
                </div>
                <h5 style="font-weight:700;color:#222;margin-bottom:8px;">Access Restricted</h5>
                <p style="color:#666;font-size:13px;margin-bottom:24px;">You don't have permission to view this document.</p>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" style="padding:7px 28px;border-radius:7px;font-weight:600;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/* ===== Can't Access popup ===== */
function artCantAccess() {
    $('#artCantAccessModal').modal('show');
}

/* ================================================================
   SHOW / HIDE COLUMNS
   ================================================================ */

var ART_COLUMNS = [
    { idx:1,  label:'ART-BNK ID',               group:'Basic Information',   always:true  },
    { idx:2,  label:'Status',                    group:'Basic Information',   always:true  },
    { idx:3,  label:'Register Date',             group:'Basic Information',   always:false },
    { idx:4,  label:'Donor (M/F)',               group:'Basic Information',   always:false },
    { idx:5,  label:'Location',                  group:'Basic Information',   always:false },
    { idx:6,  label:'Phone',                     group:'Basic Information',   always:false },
    { idx:7,  label:'Age',                       group:'Basic Information',   always:false },
    { idx:8,  label:'Photo',                     group:'Basic Information',   always:false },
    { idx:9,  label:'Marital Status',            group:'Marital & Family',    always:false },
    { idx:10, label:'Children',                  group:'Marital & Family',    always:false },
    { idx:11, label:'Child Age',                 group:'Marital & Family',    always:false },
    { idx:12, label:'Child Birth Cert.',         group:'Marital & Family',    always:false },
    { idx:13, label:'Marriage Photo',            group:'Marital & Family',    always:false },
    { idx:14, label:'Aadhar',                    group:'Documents & Proofs',  always:false },
    { idx:15, label:'Aadhar Number',             group:'Documents & Proofs',  always:false },
    { idx:16, label:'Aadhar Verified',           group:'Documents & Proofs',  always:false },
    { idx:17, label:'Marriage Cert.',            group:'Documents & Proofs',  always:false },
    { idx:18, label:'Insurance Copy',            group:'Documents & Proofs',  always:false },
    { idx:19, label:'ART Enrol. No.',            group:'Documents & Proofs',  always:false },
    { idx:20, label:'PAN Card',                  group:'Documents & Proofs',  always:false },
    { idx:21, label:'TV Scan',                   group:'Medical Tests',       always:false },
    { idx:22, label:'Serology',                  group:'Medical Tests',       always:false },
    { idx:23, label:'HB Electrophoresis',        group:'Medical Tests',       always:false },
    { idx:24, label:'Semen',                     group:'Medical Tests',       always:false },
    { idx:25, label:'BBT',                       group:'Medical Tests',       always:false },
    { idx:26, label:'TFT',                       group:'Medical Tests',       always:false },
    { idx:27, label:'Cardiac Fitness',           group:'Medical Tests',       always:false },
    { idx:28, label:'ECG',                       group:'Medical Tests',       always:false },
    { idx:29, label:'Informed Consent',          group:'Medical Tests',       always:false },
    { idx:30, label:'Donor Consent',             group:'Consents & Bonds',    always:false },
    { idx:31, label:'Donor Bond',                group:'Consents & Bonds',    always:false },
    { idx:32, label:'Egg Donor Age',             group:'Consents & Bonds',    always:false },
    { idx:33, label:'Recipient Name',            group:'Recipient Info',      always:false },
    { idx:34, label:'Recipient MRD',             group:'Recipient Info',      always:false },
    { idx:35, label:'Anesthesiologist',          group:'Recipient Info',      always:false },
    { idx:36, label:'IP Number',                 group:'OT Details',          always:false },
    { idx:37, label:'Pre Pick-Up Photo',         group:'OT Details',          always:false },
    { idx:38, label:'Post Pick-Up Photo',        group:'OT Details',          always:false },
    { idx:39, label:'Pre Pick-Up Video',         group:'OT Details',          always:false },
    { idx:40, label:'Post Pick-Up Video',        group:'OT Details',          always:false },
    { idx:41, label:'Tubbing By (ECIID)',        group:'OT Details',          always:false },
    { idx:42, label:'OT Technical',             group:'OT Details',          always:false },
    { idx:43, label:'OPU Summary',              group:'OT Details',          always:false },
    { idx:44, label:'Zone Name',                group:'Location',            always:false },
    { idx:45, label:'Center Name',              group:'Location',            always:false },
    { idx:46, label:'Procedure Branch/Zone',    group:'Location',            always:false },
    { idx:47, label:'Pre-Operative',            group:'Prescription',        always:false },
    { idx:48, label:'During Operative',         group:'Prescription',        always:false },
    { idx:49, label:'Post-Operative',           group:'Prescription',        always:false },
    { idx:50, label:'Expected Expense (Travel)','group':'Expenses',          always:false },
    { idx:51, label:'Expected Expense (Food)',  group:'Expenses',            always:false },
    { idx:52, label:'Approved Expense (Travel)',group:'Expenses',            always:false },
    { idx:53, label:'Approved Expense (Food)',  group:'Expenses',            always:false },
    { idx:54, label:'UTR Number',               group:'Expenses',            always:false }
];

var ART_GROUPS = [
    { label:'Basic Information',  from:1,  to:8  },
    { label:'Marital & Family',   from:9,  to:13 },
    { label:'Documents & Proofs', from:14, to:20 },
    { label:'Medical Tests',      from:21, to:29 },
    { label:'Consents & Bonds',   from:30, to:32 },
    { label:'Recipient Info',     from:33, to:35 },
    { label:'OT Details',         from:36, to:43 },
    { label:'Location',           from:44, to:46 },
    { label:'Prescription',       from:47, to:49 },
    { label:'Expenses',           from:50, to:54 }
];

var artVisibleCols = {};

function artInitColClasses() {
    var headThs = document.querySelectorAll('#artBankTable thead tr.head-row th');
    headThs.forEach(function(th, i) {
        th.classList.add('artcol-' + (i + 1));
    });
    var rows = document.querySelectorAll('#artBankTable tbody tr');
    rows.forEach(function(row) {
        var tds = row.querySelectorAll('td');
        tds.forEach(function(td, i) {
            if (i > 0) td.classList.add('artcol-' + i);
        });
    });
}

function artBuildPanel() {
    var body = document.getElementById('ctpBody');
    body.innerHTML = '';
    var lastGroup = '';
    ART_COLUMNS.forEach(function(col) {
        if (col.group !== lastGroup) {
            var g = document.createElement('div');
            g.className = 'ctp-group-label';
            g.textContent = col.group;
            body.appendChild(g);
            lastGroup = col.group;
        }
        var item = document.createElement('label');
        item.className = 'ctp-item' + (col.always ? ' always' : '');
        var cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.dataset.colIdx = col.idx;
        cb.checked = artVisibleCols[col.idx] !== false;
        cb.disabled = col.always;
        cb.addEventListener('change', function() {
            artToggleCol(col.idx, this.checked);
            artUpdateSelectAll();
        });
        var span = document.createElement('span');
        span.textContent = col.label;
        item.appendChild(cb);
        item.appendChild(span);
        if (col.always) {
            var hint = document.createElement('span');
            hint.className = 'ctp-hint';
            hint.textContent = 'Always visible';
            item.appendChild(hint);
        }
        body.appendChild(item);
    });
}

function artToggleCol(idx, visible) {
    artVisibleCols[idx] = visible;
    var els = document.querySelectorAll('.artcol-' + idx);
    els.forEach(function(el) {
        el.style.display = visible ? '' : 'none';
    });
    artUpdateGroupColspans();
}

function artUpdateGroupColspans() {
    var groupThs = document.querySelectorAll('#artBankTable thead tr.group-row th:not([rowspan])');
    groupThs.forEach(function(th, i) {
        var grp = ART_GROUPS[i];
        if (!grp) return;
        var visCount = 0;
        for (var c = grp.from; c <= grp.to; c++) {
            if (artVisibleCols[c] !== false) visCount++;
        }
        if (visCount === 0) {
            th.style.display = 'none';
        } else {
            th.style.display = '';
            th.setAttribute('colspan', visCount);
        }
    });
}

function ctpToggleAll(checked) {
    ART_COLUMNS.forEach(function(col) {
        if (!col.always) {
            artToggleCol(col.idx, checked);
        }
    });
    document.querySelectorAll('#ctpBody input[type=checkbox]').forEach(function(cb) {
        if (!cb.disabled) cb.checked = checked;
    });
}

function artUpdateSelectAll() {
    var all = document.querySelectorAll('#ctpBody input[type=checkbox]:not(:disabled)');
    var checked = document.querySelectorAll('#ctpBody input[type=checkbox]:not(:disabled):checked');
    document.getElementById('ctpSelectAll').checked = (all.length === checked.length);
    document.getElementById('ctpSelectAll').indeterminate = (checked.length > 0 && checked.length < all.length);
}

function toggleColPanel(e) {
    e.stopPropagation();
    document.getElementById('colTogglePanel').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('colToggleWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('colTogglePanel').classList.remove('open');
    }
});

function ctpSaveFavourite() {
    var state = {};
    ART_COLUMNS.forEach(function(col) {
        state[col.idx] = artVisibleCols[col.idx] !== false;
    });
    localStorage.setItem('art_bank_fav_cols', JSON.stringify(state));
    var btn = document.getElementById('btnLoadFav');
    btn.innerHTML = '<i class="fa fa-star"></i> Favourite';
    btn.style.background = '#fff8e1';
    btn.style.background = '#ffe082';
    setTimeout(function() { btn.style.background = '#fff8e1'; }, 600);
    alert('Column layout saved as Favourite!');
}

function ctpLoadFavourite() {
    var saved = localStorage.getItem('art_bank_fav_cols');
    if (!saved) { alert('No favourite saved yet. Use "Save as Favourite" first.'); return; }
    var state = JSON.parse(saved);
    ART_COLUMNS.forEach(function(col) {
        if (!col.always) {
            var vis = state[col.idx] !== false;
            artToggleCol(col.idx, vis);
        }
    });
    artBuildPanel();
    artUpdateSelectAll();
}

document.addEventListener('DOMContentLoaded', function() {
    ART_COLUMNS.forEach(function(col) { artVisibleCols[col.idx] = true; });
    artInitColClasses();
    artBuildPanel();
    artUpdateSelectAll();
});

/* ===== Quick filter for Custom date range ===== */
document.getElementById('filterQuickRange').addEventListener('change', function() {
    var show = this.value === 'Custom';
    document.getElementById('customDateFrom').style.display = show ? 'block' : 'none';
    document.getElementById('customDateTo').style.display   = show ? 'block' : 'none';
});

/* ===== Quick search (client-side) ===== */
function quickSearch(val) {
    var rows = document.querySelectorAll('#artTableBody tr');
    val = val.toLowerCase();
    rows.forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().indexOf(val) > -1 ? '' : 'none';
    });
}

/* ===== Filter apply / reset ===== */
function applyFilters() {
    document.getElementById('artLoading').style.display = 'flex';
    setTimeout(function() {
        document.getElementById('artLoading').style.display = 'none';
    }, 800);
}

function resetFilters() {
    ['filterZone','filterCenter','filterDonorType','filterStatus','filterMarital',
     'filterSource','filterDateBy','filterQuickRange','filterAadharVerified'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.selectedIndex = 0;
    });
    document.getElementById('filterFrom') && (document.getElementById('filterFrom').value = '');
    document.getElementById('filterTo')   && (document.getElementById('filterTo').value = '');
    document.getElementById('customDateFrom').style.display = 'none';
    document.getElementById('customDateTo').style.display   = 'none';
    document.getElementById('tableSearch').value = '';
    quickSearch('');
}
</script>
@include('superadmin.superadminfooter')
</body>
</html>