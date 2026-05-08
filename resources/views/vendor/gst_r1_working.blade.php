<!doctype html>
<html lang="en">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Body] Start -->
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
{{-- FA 4.7 ensures all fa fa-* icon names used in this page render correctly --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

<style>
/* ===== GST R1 Module Styles ===== */
:root {
    --gst-primary: #0f7b6c;
    --gst-dark:    #085f53;
    --gst-light:   #e6f7f5;
    --gst-accent:  #13c4a3;
    --gst-blue:    #1565c0;
    --gst-orange:  #e65100;
    --gst-red:     #c62828;
    --gst-green:   #2e7d32;
    --gst-purple:  #4527a0;
}

/* ---- Stat Cards ---- */
.gst-stat {
    background:#fff; border-radius:10px; padding:14px 18px;
    box-shadow:0 2px 10px rgba(0,0,0,.07); display:flex; align-items:center;
    gap:12px; margin-bottom:18px; border-left:4px solid var(--gst-primary);
}
.gst-stat-icon { width:44px; height:44px; border-radius:9px; background:var(--gst-light);
    color:var(--gst-primary); display:flex; align-items:center; justify-content:center;
    font-size:18px; flex-shrink:0; }
.gst-stat-val  { font-size:22px; font-weight:700; line-height:1.1; color:#222; }
.gst-stat-lbl  { font-size:11px; color:#777; margin-top:2px; }

/* ---- Panel Card ---- */
.gst-card {
    background:#fff; border-radius:12px; padding:0;
    box-shadow:0 2px 12px rgba(0,0,0,.07); margin-bottom:22px; overflow:hidden;
}
.gst-card-head {
    padding:14px 20px; border-bottom:1px solid #f0f0f0;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
    font-size:14px; font-weight:700; color:var(--gst-dark);
}
.gst-card-body { padding:20px; }

/* ---- Buttons ---- */
.btn-gst-primary {
    background:linear-gradient(135deg,var(--gst-primary),var(--gst-dark));
    color:#fff; border:none; padding:8px 20px; border-radius:7px;
    font-size:13px; font-weight:600; cursor:pointer; transition:all .2s;
    display:inline-flex; align-items:center; gap:6px;
}
.btn-gst-primary:hover { opacity:.88; transform:translateY(-1px); color:#fff; }
.btn-gst-outline {
    background:#fff; color:var(--gst-primary); border:1.5px solid var(--gst-primary);
    padding:7px 18px; border-radius:7px; font-size:13px; font-weight:600;
    cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:6px;
}
.btn-gst-outline:hover { background:var(--gst-light); }
.btn-gst-danger { background:var(--gst-red); color:#fff; border:none; padding:5px 12px;
    border-radius:6px; font-size:11px; font-weight:600; cursor:pointer; }
.btn-gst-edit   { background:#e3f0ff; color:var(--gst-blue); border:none; padding:5px 12px;
    border-radius:6px; font-size:11px; font-weight:600; cursor:pointer; }
.btn-icon { background:none; border:none; padding:4px 8px; cursor:pointer; border-radius:5px; }
.btn-icon:hover { background:#f0f0f0; }

/* ---- Filter Bar ---- */
.gst-filter { background:#fff; border-radius:10px; padding:16px 20px;
    box-shadow:0 2px 10px rgba(0,0,0,.07); margin-bottom:18px; }
.gst-filter .form-control { font-size:13px; height:36px; border-radius:7px;
    border:1px solid #d8e0ea; padding:5px 10px; }
.gst-filter label { font-size:11px; font-weight:700; color:#666; margin-bottom:3px; }

/* ---- Tab Nav ---- */
.gst-tabs { display:flex; gap:6px; margin-bottom:18px; border-bottom:2px solid #e8ecef; padding-bottom:0; }
.gst-tab-btn {
    padding:9px 20px; border:none; background:none; font-size:13px; font-weight:600;
    color:#888; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px;
    border-radius:8px 8px 0 0; transition:all .15s;
}
.gst-tab-btn.active { color:var(--gst-primary); border-bottom-color:var(--gst-primary); background:var(--gst-light); }
.gst-tab-btn:hover  { color:var(--gst-primary); background:#f5faf9; }
.gst-tab-pane { display:none; }
.gst-tab-pane.active { display:block; }

/* ---- Table ---- */
.gst-table-wrap { overflow-x:auto; }
.gst-table-wrap::-webkit-scrollbar { height:6px; }
.gst-table-wrap::-webkit-scrollbar-thumb { background:var(--gst-accent); border-radius:10px; }
.gst-table { width:100%; border-collapse:separate; border-spacing:0; font-size:12px; min-width:1400px; }
.gst-table thead th {
    background:#f0faf8; color:var(--gst-dark); padding:9px 10px; border:1px solid #d6ecea;
    font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.03em;
    white-space:nowrap; text-align:center; position:sticky; top:0; z-index:2; left: 40;
}
.gst-table tbody td {
    padding:8px 10px; border:1px solid #e8eeee; text-align:center;
    vertical-align:middle; color:#333; white-space:nowrap; font-size:12px;
}
.gst-table tbody tr:hover td { background:#f5faf9; }
.gst-table tbody tr:nth-child(even) td { background:#fafcfc; }
.gst-table tbody tr:nth-child(even):hover td { background:#f0faf8; }
.gst-table tfoot td { background:#085f53; color:#fff; font-weight:700; font-size:11px;
    padding:8px 10px; border:1px solid #0a6b5c; }
.col-sticky   { position:sticky; left:0;    z-index:1; background:#fff; }
.col-sticky-2 {
    position:sticky; left:40px; z-index:1; background:#fff;
    box-shadow:3px 0 10px rgba(0,0,0,.08);
}
thead th.col-sticky, thead th.col-sticky-2 { z-index:3; background:#f0faf8; }
/* Keep sticky cell background consistent across even/hover rows */
.gst-table tbody tr:nth-child(even) td.col-sticky,
.gst-table tbody tr:nth-child(even) td.col-sticky-2 { background:#fafcfc; }
.gst-table tbody tr:hover td.col-sticky,
.gst-table tbody tr:hover td.col-sticky-2 { background:#f5faf9; }

/* ---- Amount Badges ---- */
.amt-positive { color:var(--gst-green); font-weight:700; }
.amt-negative { color:var(--gst-red);   font-weight:700; }
.badge-import { background:#e3f0ff; color:var(--gst-blue); padding:2px 9px; border-radius:10px; font-size:10px; font-weight:700; }
.badge-manual { background:#fff3e0; color:var(--gst-orange); padding:2px 9px; border-radius:10px; font-size:10px; font-weight:700; }

/* ---- Pagination ---- */
.gst-pagination { padding:12px 20px; border-top:1px solid #f0f0f0;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
    font-size:13px; color:#666; }
.pg-btns { display:flex; gap:4px; flex-wrap:wrap; }
.pg-btns button { border:1px solid #d8e0ea; background:#fff; padding:5px 11px;
    border-radius:6px; font-size:12px; cursor:pointer; color:#444; transition:all .15s; }
.pg-btns button.active, .pg-btns button:hover { background:var(--gst-primary); color:#fff; border-color:var(--gst-primary); }
.pg-btns button:disabled { opacity:.4; cursor:not-allowed; }

/* ---- Upload Drop Zone ---- */
.gst-dropzone {
    border:2px dashed #b0d8d3; border-radius:12px; padding:40px 20px;
    text-align:center; cursor:pointer; transition:all .2s; background:#fafffe;
}
.gst-dropzone.dragover { border-color:var(--gst-primary); background:var(--gst-light); }
.gst-dropzone i { font-size:42px; color:var(--gst-accent); margin-bottom:12px; display:block; }
.gst-dropzone p { margin:0; font-size:14px; color:#555; }
.gst-dropzone small { color:#aaa; font-size:12px; }

/* ---- Import Result ---- */
.import-result { background:#f0faf8; border:1px solid #b2ddd8; border-radius:8px;
    padding:14px 18px; margin-top:14px; font-size:13px; display:none; }
.import-result .stat { display:inline-flex; align-items:center; gap:5px;
    margin-right:16px; font-weight:600; }

/* ---- Form ---- */
.gst-form-section { margin-bottom:20px; }
.gst-form-section-title {
    font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em;
    color:var(--gst-dark); background:var(--gst-light); padding:7px 14px;
    border-radius:6px; margin-bottom:12px;
}
.gst-form-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
.gst-form-grid.g2 { grid-template-columns:repeat(2,1fr); }
.gst-form-grid.g3 { grid-template-columns:repeat(3,1fr); }
@media(max-width:768px) { .gst-form-grid { grid-template-columns:repeat(2,1fr); } }
.gst-form-grid .form-group label { font-size:11px; font-weight:700; color:#666; margin-bottom:3px; }
.gst-form-grid .form-control { font-size:12px; height:34px; border-radius:6px;
    border:1px solid #d8e0ea; padding:4px 9px; }

/* ---- Loading ---- */
.gst-loading-row td { padding:50px !important; text-align:center; color:#aaa; font-size:14px; }
.spinner-sm { display:inline-block; width:18px; height:18px; border:2px solid rgba(255,255,255,.3);
    border-top-color:#fff; border-radius:50%; animation:spin .8s linear infinite; vertical-align:middle; margin-right:5px; }
@keyframes spin { to { transform:rotate(360deg); } }

/* ---- Filter rows (qd-style) ---- */
.gst-filter-row { display:grid; gap:12px; align-items:end; }
.gst-filter-row + .gst-filter-row { margin-top:12px; }
.gst-filter-row .form-group { margin-bottom:0; }
.gst-filter-row label { font-size:11px; font-weight:700; color:#666; margin-bottom:3px; display:block; }
.gst-filter-row .form-control, .gst-filter-row select.form-control {
    font-size:13px; height:36px; border-radius:7px; border:1px solid #d8e0ea; }
@media(max-width:992px){ .gst-filter-row.r4 { grid-template-columns:repeat(2,1fr) !important; } }
@media(max-width:576px){ .gst-filter-row { grid-template-columns:1fr !important; } }

/* ---- Auto-calc readonly fields ---- */
.f-auto-calc {
    background:linear-gradient(135deg,#f0faf8,#e8f5f2) !important;
    border-color:#9bd0cb !important; color:var(--gst-dark) !important;
    font-weight:700 !important; cursor:not-allowed;
}
.f-manual { border-color:#1565c0 !important; background:#f0f5ff !important; }

/* ---- Calc Summary Preview ---- */
.calc-preview-card {
    background:linear-gradient(135deg,#f0faf8 0%,#e8f7f4 100%);
    border:1.5px solid #9bd0cb; border-radius:12px;
    padding:16px 20px; margin-bottom:18px;
}
.calc-preview-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-top:12px; }
@media(max-width:768px){ .calc-preview-grid { grid-template-columns:repeat(2,1fr); } }
.calc-item { text-align:center; background:#fff; border-radius:8px; padding:10px 8px; border:1px solid #c8e6e2; }
.calc-item .calc-val { font-size:15px; font-weight:700; color:var(--gst-dark); line-height:1.2; }
.calc-item .calc-lbl { font-size:10px; color:#888; margin-top:3px; text-transform:uppercase; letter-spacing:.05em; }
.calc-diff-pos .calc-val { color:var(--gst-green) !important; }
.calc-diff-neg .calc-val { color:var(--gst-red) !important; }

/* ---- Form select ---- */
.gst-form-grid select.form-control { height:34px; font-size:12px; }

/* =========================================================
   MULTI-SELECT DROPDOWN
   ========================================================= */
.gst-ms-wrap { position:relative; }
.gst-ms-trigger {
    display:flex; align-items:center; justify-content:space-between;
    background:#fff; border:1.5px solid #d8e0ea; border-radius:8px;
    padding:0 10px; height:36px; cursor:pointer; font-size:12px; color:#666;
    transition:all .15s; user-select:none; gap:6px;
}
.gst-ms-trigger:hover { border-color:var(--gst-accent); }
.gst-ms-trigger.active, .gst-ms-trigger.has-val { border-color:var(--gst-primary); color:var(--gst-dark); font-weight:600; }
.gst-ms-badge {
    background:var(--gst-primary); color:#fff; font-size:10px; font-weight:700;
    border-radius:10px; padding:1px 7px; min-width:18px; text-align:center;
}
.gst-ms-dropdown {
    position:absolute; top:calc(100% + 5px); left:0; z-index:9999; min-width:220px; width:100%;
    background:#fff; border:1.5px solid #d0e8e4; border-radius:10px;
    box-shadow:0 10px 32px rgba(0,0,0,.14); overflow:hidden; display:none;
}
.gst-ms-dropdown.open { display:block; }
.gst-ms-search-wrap { padding:8px 10px; border-bottom:1px solid #f0f0f0; }
.gst-ms-search { width:100%; border:1px solid #e0e0e0; border-radius:6px; padding:5px 9px; font-size:12px; outline:none; }
.gst-ms-list { max-height:190px; overflow-y:auto; padding:4px 0; }
.gst-ms-list::-webkit-scrollbar { width:4px; }
.gst-ms-list::-webkit-scrollbar-thumb { background:var(--gst-accent); border-radius:10px; }
.gst-ms-opt {
    padding:7px 12px 7px 10px; font-size:12px; cursor:pointer;
    display:flex; align-items:center; gap:8px; transition:background .1s;
}
.gst-ms-opt:hover { background:#f2faf8; }
.gst-ms-opt.sel { background:var(--gst-light); color:var(--gst-dark); font-weight:600; }
.gst-ms-opt .ms-check { width:14px; height:14px; border:2px solid #ccc; border-radius:3px; display:inline-block; flex-shrink:0; transition:.15s; }
.gst-ms-opt.sel .ms-check { background:var(--gst-primary); border-color:var(--gst-primary); position:relative; }
.gst-ms-opt.sel .ms-check::after { content:'✓'; color:#fff; font-size:9px; font-weight:700; position:absolute; top:-1px; left:1px; }
.gst-ms-foot { border-top:1px solid #f0f0f0; padding:7px 12px; display:flex; justify-content:space-between; align-items:center; }
.gst-ms-clr { font-size:11px; color:#aaa; cursor:pointer; } .gst-ms-clr:hover { color:var(--gst-red); }
.gst-ms-apply { font-size:11px; font-weight:700; color:var(--gst-primary); cursor:pointer; background:var(--gst-light); border-radius:5px; padding:3px 10px; }
.gst-ms-apply:hover { background:var(--gst-primary); color:#fff; }
.gst-ms-empty { padding:18px; text-align:center; color:#bbb; font-size:12px; }

/* =========================================================
   MONTH-YEAR PICKER
   ========================================================= */
.gst-mp-wrap { position:relative; }
.gst-mp-trigger {
    display:flex; align-items:center; gap:7px;
    background:#fff; border:1.5px solid #d8e0ea; border-radius:8px;
    padding:0 10px; height:36px; cursor:pointer; font-size:12px; color:#666;
    transition:all .15s; user-select:none;
}
.gst-mp-trigger:hover  { border-color:var(--gst-accent); }
.gst-mp-trigger.has-val { border-color:var(--gst-primary); color:var(--gst-dark); font-weight:600; }
.gst-mp-trigger .mp-ico { color:var(--gst-accent); font-size:13px; }
.gst-mp-trigger .mp-clr { margin-left:auto; color:#bbb; font-size:11px; }
.gst-mp-trigger .mp-clr:hover { color:var(--gst-red); }
.gst-mp-dropdown {
    position:absolute; top:calc(100% + 5px); left:0; z-index:9999; width:260px;
    background:#fff; border:1.5px solid #d0e8e4; border-radius:12px;
    box-shadow:0 10px 32px rgba(0,0,0,.14); padding:14px; display:none;
}
.gst-mp-dropdown.open { display:block; }
.gst-mp-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
.gst-mp-yr-btn { background:var(--gst-light); border:none; width:28px; height:28px; border-radius:7px;
    cursor:pointer; font-size:13px; font-weight:700; color:var(--gst-dark); transition:.15s; }
.gst-mp-yr-btn:hover { background:var(--gst-primary); color:#fff; }
.gst-mp-yr-label { font-weight:700; font-size:15px; color:var(--gst-dark); letter-spacing:.03em; }
.gst-mp-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:6px; }
.gst-mp-month {
    padding:8px 4px; border:1.5px solid #e8ecef; border-radius:8px; background:#fff;
    cursor:pointer; font-size:11px; font-weight:600; color:#555; text-align:center; transition:all .15s;
}
.gst-mp-month:hover { background:var(--gst-light); border-color:var(--gst-accent); color:var(--gst-dark); }
.gst-mp-month.cur { background:var(--gst-primary); color:#fff; border-color:var(--gst-primary); }
.gst-mp-month.today-yr { border-color:#b2ddd8; }
.gst-mp-foot { margin-top:10px; border-top:1px solid #f0f0f0; padding-top:8px; display:flex; justify-content:space-between; font-size:11px; }
.gst-mp-foot .mp-clear { color:#bbb; cursor:pointer; } .gst-mp-foot .mp-clear:hover { color:var(--gst-red); }
.gst-mp-foot .mp-today { color:var(--gst-primary); cursor:pointer; font-weight:700; }

/* ---- Filter label ---- */
.gst-filter-lbl { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#888; margin-bottom:4px; }

/* ---- State badge in table ---- */
.state-badge { display:inline-flex; align-items:center; gap:4px; background:linear-gradient(135deg,#f3e5f5,#ede2fa); color:#6a1b9a; font-size:10px; font-weight:700; padding:2px 8px; border-radius:10px; border:1px solid #ce93d8; white-space:nowrap; }
.zone-badge  { display:inline-flex; align-items:center; gap:4px; background:#e3f0ff; color:#1565c0; font-size:10px; font-weight:700; padding:2px 8px; border-radius:10px; border:1px solid #90caf9; white-space:nowrap; }

/* ---- Toast ---- */
.gst-toast { position:fixed; bottom:24px; right:24px; z-index:9999; min-width:260px; max-width:380px; }
.toast-item { background:#fff; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.15);
    padding:14px 18px; margin-top:10px; display:flex; align-items:center; gap:12px;
    border-left:4px solid var(--gst-primary); font-size:13px; animation:slideUp .3s ease; }
.toast-item.success { border-left-color:var(--gst-green); }
.toast-item.error   { border-left-color:var(--gst-red); }
.toast-item.warning { border-left-color:var(--gst-orange); }
@keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
</style>

<body style="overflow-x: hidden;">

    <div class="page-loader">
      <div class="bar"></div>
    </div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')
    <div class="pc-container">
        <div class="pc-content">

            <div class="qd-card">

                <div class="qd-header">
                    <div class="qd-header-title">
                        <div>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <i class="bi bi-file-earmark-spreadsheet"></i>
                                <span>GST R1 Workings</span>
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:11px;">2026-27</span>
                            </div>
                            <div class="mt-1 fw-normal text-secondary" style="font-size:12px;letter-spacing:0;">Branch-wise GST R1 Working Register · Import · Export · Manual Entry</div>
                        </div>
                    </div>
                    <div class="qd-header-actions">
                        <button type="button" class="btn btn-primary btn-sm" onclick="showTab('import')">
                            <i class="bi bi-upload me-1"></i>Import Excel
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="showTab('add')">
                            <i class="bi bi-plus-lg me-1"></i>Add Manual
                        </button>
                    </div>
                </div>

                <div class="p-3 pt-2">

    <!-- ===== STAT CARDS ===== -->
    <div class="row" id="statCards">
        <div class="col-md-2 col-sm-4 col-6">
            <div class="gst-stat">
                <div class="gst-stat-icon"><i class="fa fa-sitemap"></i></div>
                <div><div class="gst-stat-val" id="statBranches">—</div><div class="gst-stat-lbl">Branches</div></div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="gst-stat" style="border-left-color:#8e24aa;">
                <div class="gst-stat-icon" style="background:#f3e5f5;color:#8e24aa;"><i class="fa fa-building"></i></div>
                <div><div class="gst-stat-val" id="statPharmacy">—</div><div class="gst-stat-lbl">Total Pharmacy</div></div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="gst-stat" style="border-left-color:var(--gst-orange);">
                <div class="gst-stat-icon" style="background:#fff3e0;color:var(--gst-orange);"><i class="fa fa-percent"></i></div>
                <div><div class="gst-stat-val" id="statGst">—</div><div class="gst-stat-lbl">Total GST</div></div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="gst-stat" style="border-left-color:var(--gst-blue);">
                <div class="gst-stat-icon" style="background:#e3f0ff;color:var(--gst-blue);"><i class="fa fa-line-chart fa-chart-line"></i></div>
                <div><div class="gst-stat-val" id="statTurnover">—</div><div class="gst-stat-lbl">Total Turnover</div></div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="gst-stat" style="border-left-color:var(--gst-green);">
                <div class="gst-stat-icon" style="background:#e8f5e9;color:var(--gst-green);"><i class="fa fa-money fa-money-bill"></i></div>
                <div><div class="gst-stat-val" id="statCollection">—</div><div class="gst-stat-lbl">Collection</div></div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="gst-stat" style="border-left-color:var(--gst-red);">
                <div class="gst-stat-icon" style="background:#fce4e4;color:var(--gst-red);"><i class="fa fa-exchange fa-exchange-alt"></i></div>
                <div><div class="gst-stat-val" id="statDiff">—</div><div class="gst-stat-lbl">Net Difference</div></div>
            </div>
        </div>
    </div>

    <!-- ===== TABS ===== -->
    <div class="gst-tabs">
        <button class="gst-tab-btn active" onclick="showTab('list')"   id="tab-list">   <i class="fa fa-table"></i> Data List</button>
        <button class="gst-tab-btn"        onclick="showTab('import')" id="tab-import"> <i class="fa fa-upload"></i> Import Excel</button>
        <button class="gst-tab-btn"        onclick="showTab('add')"    id="tab-add">    <i class="fa fa-plus-circle"></i> Manual Entry</button>
    </div>

    <!-- ===== TAB: LIST ===== -->
    <div class="gst-tab-pane active" id="pane-list">

        <!-- ===== Filter Bar ===== -->
        <div class="gst-filter" style="padding:18px 20px;">

            {{-- Row 1: State · Zone · Branch · Month --}}
            <div class="gst-filter-row r4" style="grid-template-columns:repeat(4,1fr);">

                {{-- State Multi-Select --}}
                <div>
                    <div class="gst-filter-lbl"><i class="fa fa-globe" style="color:var(--gst-accent);margin-right:3px;"></i>State</div>
                    <div class="gst-ms-wrap" id="stateMs">
                        <div class="gst-ms-trigger" onclick="toggleMs('stateMs')" id="stateMsTrigger">
                            <span id="stateMsLabel">All States</span>
                            <i class="fa fa-chevron-down" style="font-size:10px;color:#aaa;margin-left:auto;"></i>
                        </div>
                        <div class="gst-ms-dropdown" id="stateMsDrop">
                            <div class="gst-ms-search-wrap">
                                <input class="gst-ms-search" id="stateMsSearch" placeholder="Search state…" oninput="filterMsOpts('stateMs')">
                            </div>
                            <div class="gst-ms-list" id="stateMsList">
                                @forelse($dbStates as $s)
                                <div class="gst-ms-opt" data-val="{{ $s }}" onclick="toggleMsItem(this,'stateMs')">
                                    <span class="ms-check"></span><span>{{ $s }}</span>
                                </div>
                                @empty
                                <div class="gst-ms-empty">No states in DB yet</div>
                                @endforelse
                            </div>
                            <div class="gst-ms-foot">
                                <span class="gst-ms-clr" onclick="clearMs('stateMs')">Clear</span>
                                <span class="gst-ms-apply" onclick="applyMsFilter()">Apply</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Zone Multi-Select --}}
                <div>
                    <div class="gst-filter-lbl"><i class="fa fa-map-marker" style="color:var(--gst-accent);margin-right:3px;"></i>Zone</div>
                    <div class="gst-ms-wrap" id="zoneMs">
                        <div class="gst-ms-trigger" onclick="toggleMs('zoneMs')" id="zoneMsTrigger">
                            <span id="zoneMsLabel">All Zones</span>
                            <i class="fa fa-chevron-down" style="font-size:10px;color:#aaa;margin-left:auto;"></i>
                        </div>
                        <div class="gst-ms-dropdown" id="zoneMsDrop">
                            <div class="gst-ms-search-wrap">
                                <input class="gst-ms-search" id="zoneMsSearch" placeholder="Search zone…" oninput="filterMsOpts('zoneMs')">
                            </div>
                            <div class="gst-ms-list" id="zoneMsList">
                                @foreach($allZones as $z)
                                <div class="gst-ms-opt" data-val="{{ $z->name }}" data-id="{{ $z->id }}" onclick="toggleMsItem(this,'zoneMs')">
                                    <span class="ms-check"></span><span>{{ $z->name }}</span>
                                </div>
                                @endforeach
                            </div>
                            <div class="gst-ms-foot">
                                <span class="gst-ms-clr" onclick="clearMs('zoneMs')">Clear</span>
                                <span class="gst-ms-apply" onclick="applyMsFilter()">Apply</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Branch Multi-Select --}}
                <div>
                    <div class="gst-filter-lbl"><i class="fa fa-building" style="color:var(--gst-accent);margin-right:3px;"></i>Branch</div>
                    <div class="gst-ms-wrap" id="branchMs">
                        <div class="gst-ms-trigger" onclick="toggleMs('branchMs')" id="branchMsTrigger">
                            <span id="branchMsLabel">All Branches</span>
                            <i class="fa fa-chevron-down" style="font-size:10px;color:#aaa;margin-left:auto;"></i>
                        </div>
                        <div class="gst-ms-dropdown" id="branchMsDrop">
                            <div class="gst-ms-search-wrap">
                                <input class="gst-ms-search" id="branchMsSearch" placeholder="Search branch…" oninput="filterMsOpts('branchMs')">
                            </div>
                            <div class="gst-ms-list" id="branchMsList">
                                @foreach($allBranches as $b)
                                <div class="gst-ms-opt" data-val="{{ $b->name }}" data-zone-id="{{ $b->zone_id }}" onclick="toggleMsItem(this,'branchMs')">
                                    <span class="ms-check"></span><span>{{ $b->name }}</span>
                                </div>
                                @endforeach
                            </div>
                            <div class="gst-ms-foot">
                                <span class="gst-ms-clr" onclick="clearMs('branchMs')">Clear</span>
                                <span class="gst-ms-apply" onclick="applyMsFilter()">Apply</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Month Picker --}}
                <div>
                    <div class="gst-filter-lbl"><i class="fa fa-calendar" style="color:var(--gst-accent);margin-right:3px;"></i>Month</div>
                    <div class="gst-mp-wrap" id="listMp">
                        <div class="gst-mp-trigger" onclick="openMp('listMp')" id="listMpTrigger">
                            <i class="fa fa-calendar-o mp-ico"></i>
                            <span id="listMpLabel" style="flex:1;">All Months</span>
                            <span class="mp-clr" id="listMpClr" onclick="clearMp('listMp',event)" style="display:none;">✕</span>
                        </div>
                        <div class="gst-mp-dropdown" id="listMpDrop">
                            <div class="gst-mp-head">
                                <button type="button" class="gst-mp-yr-btn" onclick="mpChangeYear('listMp',-1)">‹</button>
                                <span class="gst-mp-yr-label" id="listMpYr">2026</span>
                                <button type="button" class="gst-mp-yr-btn" onclick="mpChangeYear('listMp',1)">›</button>
                            </div>
                            <div class="gst-mp-grid" id="listMpGrid"></div>
                            <div class="gst-mp-foot">
                                <span class="mp-clear" onclick="clearMp('listMp',event)">Clear</span>
                                <span class="mp-today" onclick="mpSelectCurrent('listMp')">This Month</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Row 2: Source · Search · Buttons --}}
            <div class="gst-filter-row" style="grid-template-columns:160px 1fr auto;margin-top:12px;">
                <div>
                    <div class="gst-filter-lbl"><i class="fa fa-filter" style="color:var(--gst-accent);margin-right:3px;"></i>Source</div>
                    <select class="form-control" id="fSource" style="height:36px;font-size:13px;">
                        <option value="">— All —</option>
                        <option value="import">Import</option>
                        <option value="manual">Manual</option>
                    </select>
                </div>
                <div>
                    <div class="gst-filter-lbl"><i class="fa fa-search" style="color:var(--gst-accent);margin-right:3px;"></i>Search</div>
                    <input type="text" class="form-control" id="fSearch" placeholder="Search branch, state, zone…" style="height:36px;font-size:13px;">
                </div>
                <div style="display:flex;gap:8px;align-items:flex-end;">
                    <button class="btn-gst-primary" style="height:36px;padding:0 20px;" onclick="loadList(1)">
                        <i class="fa fa-search"></i> Search
                    </button>
                    <button class="btn-gst-outline" onclick="clearListFilters()" title="Clear all filters" style="height:36px;padding:0 12px;">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            {{-- Active Filter Chips --}}
            <div id="activeChips" style="display:none;margin-top:10px;flex-wrap:wrap;gap:6px;align-items:center;">
                <span style="font-size:11px;color:#888;font-weight:600;">Active:</span>
            </div>
            <input type="hidden" id="f_list_month" value="">
        </div>

        <!-- Table Card -->
        <div class="gst-card">
            <div class="gst-card-head">
                <span><i class="fa fa-table" style="color:var(--gst-accent);margin-right:6px;"></i>
                    GST R1 Records &nbsp;<span id="totalCount" style="font-size:12px;color:#999;font-weight:400;"></span>
                </span>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <select id="perPage" class="form-control" style="width:70px;height:32px;font-size:13px;border-radius:6px;border:1px solid #d8e0ea;">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span style="font-size:12px;color:#888;">per page</span>
                    <button class="btn-gst-outline" onclick="exportData('xlsx')" style="padding:5px 14px;font-size:12px;">
                        <i class="fa fa-file-excel-o fa-file-excel"></i> XLSX
                    </button>
                    <button class="btn-gst-outline" onclick="exportData('csv')" style="padding:5px 14px;font-size:12px;border-color:#e65100;color:#e65100;">
                        <i class="fa fa-file-text-o fa-file-alt"></i> CSV
                    </button>
                </div>
            </div>

            <div class="gst-table-wrap">
                <table class="gst-table" id="gstTable">
                    <thead>
                        <tr>
                            <th class="col-sticky"   style="min-width:40px;">#</th>
                            <th class="col-sticky-2" style="min-width:160px;">Branch</th>
                            <th style="min-width:110px;">Month</th>
                            <th style="min-width:105px;">State</th>
                            <th style="min-width:90px;">Zone</th>
                            <th style="min-width:90px;">GST0 Taxable</th>
                            <th style="min-width:90px;">GST5 Taxable</th>
                            <th style="min-width:80px;">GST5 CGST</th>
                            <th style="min-width:80px;">GST5 SGST</th>
                            <th style="min-width:90px;">GST12 Taxable</th>
                            <th style="min-width:80px;">GST12 CGST</th>
                            <th style="min-width:90px;">GST18 Taxable</th>
                            <th style="min-width:80px;">GST18 CGST</th>
                            <th style="min-width:110px;">Total Pharmacy</th>
                            <th style="min-width:90px;">Total GST</th>
                            <th style="min-width:100px;">Exempt Sales</th>
                            <th style="min-width:110px;">Total Turnover</th>
                            <th style="min-width:100px;">Collection</th>
                            <th style="min-width:100px;">Difference</th>
                            <th style="min-width:70px;">Source</th>
                            <th style="min-width:80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="gstBody">
                        <tr class="gst-loading-row"><td colspan="21"><i class="fa fa-spinner fa-spin"></i> Loading…</td></tr>
                    </tbody>
                    <tfoot>
                        <tr id="footerRow" style="display:none;">
                            <td colspan="5" style="text-align:right;">TOTALS</td>
                            <td id="ft_gst0"      >—</td>
                            <td id="ft_gst5"      >—</td>
                            <td id="ft_gst5c"     >—</td>
                            <td id="ft_gst5s"     >—</td>
                            <td id="ft_gst12"     >—</td>
                            <td id="ft_gst12c"    >—</td>
                            <td id="ft_gst18"     >—</td>
                            <td id="ft_gst18c"    >—</td>
                            <td id="ft_pharmacy"  >—</td>
                            <td id="ft_gst"       >—</td>
                            <td id="ft_exempt"    >—</td>
                            <td id="ft_turnover"  >—</td>
                            <td id="ft_collection">—</td>
                            <td id="ft_diff"      >—</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="gst-pagination">
                <div id="pgInfo" style="color:#666;font-size:13px;"></div>
                <div class="pg-btns" id="pgBtns"></div>
            </div>
        </div>
    </div>

    <!-- ===== TAB: IMPORT ===== -->
    <div class="gst-tab-pane" id="pane-import">
        <div class="gst-card">
            <div class="gst-card-head"><i class="fa fa-upload" style="color:var(--gst-accent);margin-right:6px;"></i> Import Excel File</div>
            <div class="gst-card-body">
                <div class="row">
                    <div class="col-md-7">
                        <div class="gst-dropzone" id="dropZone" onclick="document.getElementById('importFile').click()">
                            <i class="fa fa-cloud-upload fa-cloud-upload-alt"></i>
                            <p><strong>Click to browse</strong> or drag & drop your Excel file here</p>
                            <small>Supported: .xlsx, .xls, .csv &nbsp;|&nbsp; Each sheet name = Month</small>
                            <div id="selectedFile" style="margin-top:12px;display:none;">
                                <span style="background:var(--gst-light);color:var(--gst-dark);padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;">
                                    <i class="fa fa-file-excel-o fa-file-excel"></i> <span id="fileName"></span>
                                </span>
                            </div>
                        </div>
                        <input type="file" id="importFile" accept=".xlsx,.xls,.csv" style="display:none;" onchange="fileSelected(this)">

                        <div style="margin-top:16px; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                            <label style="display:flex;align-items:center;gap:7px;font-size:13px;cursor:pointer;">
                                <input type="checkbox" id="overwriteCheck" style="accent-color:var(--gst-primary);width:15px;height:15px;">
                                <span>Overwrite existing records (same Branch + Month)</span>
                            </label>
                        </div>

                        <div style="margin-top:16px;">
                            <button class="btn-gst-primary" id="btnImport" onclick="doImport()" disabled>
                                <span class="spinner-sm" id="importSpinner" style="display:none;border-top-color:var(--gst-accent);border-color:rgba(255,255,255,.3);"></span>
                                <i class="fa fa-upload" id="importIcon"></i> Start Import
                            </button>
                            &nbsp;
                            <button class="btn-gst-outline" onclick="resetImport()">
                                <i class="fa fa-repeat"></i> Reset
                            </button>
                        </div>

                        <div class="import-result" id="importResult"></div>
                    </div>

                    <div class="col-md-5">
                        <div style="background:#f8fffe;border:1px solid #b2ddd8;border-radius:10px;padding:18px 20px;">
                            <div style="font-size:13px;font-weight:700;color:var(--gst-dark);margin-bottom:12px;">
                                <i class="fa fa-info-circle" style="color:var(--gst-accent);"></i> Expected File Format
                            </div>
                            <ul style="font-size:12px;color:#555;margin:0;padding-left:18px;line-height:2;">
                                <li>Each <strong>Sheet tab = Month name</strong> (e.g. "April 2026")</li>
                                <li>Row 1 = Group headers (GST 0%, GST 5%…)</li>
                                <li>Row 2 = Sub-headers (Qty, Taxable, CGST…)</li>
                                <li>Row 3 onwards = Branch data rows</li>
                                <li>State summary rows (Tamil Nadu, Kerala…) are auto-skipped</li>
                                <li>Column order: Branch → GST0% → GST5% → GST12% → GST18% → Totals</li>
                            </ul>
                            <div style="margin-top:14px;padding-top:12px;border-top:1px solid #d8eeeb;">
                                <div style="font-size:12px;font-weight:700;color:var(--gst-dark);margin-bottom:8px;">Column Mapping</div>
                                <table style="width:100%;font-size:11px;border-collapse:collapse;">
                                    <tr style="background:var(--gst-light);">
                                        <th style="padding:4px 8px;text-align:left;">Col</th>
                                        <th style="padding:4px 8px;text-align:left;">Field</th>
                                    </tr>
                                    @foreach([
                                        ['A','Branch'], ['B','GST0 Qty'], ['C','GST0 Taxable'],
                                        ['D','GST5 Qty'], ['E','GST5 Taxable'], ['F','GST5 CGST'], ['G','GST5 SGST'],
                                        ['H','GST12 Qty'], ['I','GST12 Taxable'], ['J','GST12 CGST'], ['K','GST12 SGST'],
                                        ['L','GST18 Qty'], ['M','GST18 Taxable'], ['N','GST18 CGST'], ['O','GST18 SGST'],
                                        ['P','Total Pharmacy'], ['Q','Total GST'], ['R','Exempt Sales'],
                                        ['S','Total Turnover'], ['T','Collection'], ['U','Difference'],
                                    ] as [$col, $field])
                                    <tr>
                                        <td style="padding:3px 8px;font-weight:700;color:var(--gst-primary);">{{ $col }}</td>
                                        <td style="padding:3px 8px;color:#444;">{{ $field }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== TAB: MANUAL ADD ===== -->
    <div class="gst-tab-pane" id="pane-add">
        <div class="gst-card">
            <div class="gst-card-head">
                <span><i class="fa fa-plus-circle" style="color:var(--gst-accent);margin-right:6px;"></i>
                    <span id="manualFormTitle">Add Manual Entry</span>
                </span>
                    <button class="btn-gst-outline" onclick="resetManualForm()" style="padding:5px 14px;font-size:12px;">
                    <i class="fa fa-repeat"></i> Reset Form
                </button>
            </div>
            <div class="gst-card-body">
                <input type="hidden" id="editId">

                <!-- Basic Info -->
                <div class="gst-form-section">
                    <div class="gst-form-section-title"><i class="fa fa-info-circle"></i> Basic Information</div>
                    <div class="gst-form-grid" style="grid-template-columns:repeat(4,1fr);">

                        {{-- State (geographical) --}}
                        <div class="form-group">
                            <label><i class="fa fa-globe" style="color:#8e24aa;margin-right:3px;"></i>
                                State <span style="color:#aaa;font-weight:400;font-size:10px;">(auto from import)</span>
                            </label>
                            <select id="f_state" class="form-control">
                                <option value="">— Select State —</option>
                                @foreach(['Tamil Nadu','Andhra Pradesh','Karnataka','Kerala','Telangana','Pondicherry'] as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                                @endforeach
                                @foreach($dbStates as $st)
                                @if(!in_array($st, ['Tamil Nadu','Andhra Pradesh','Karnataka','Kerala','Telangana','Pondicherry']))
                                <option value="{{ $st }}">{{ $st }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        {{-- Zone (business) --}}
                        <div class="form-group">
                            <label><i class="fa fa-map-marker" style="color:var(--gst-accent);margin-right:3px;"></i>
                                Zone <span style="color:#aaa;font-weight:400;">(optional)</span>
                            </label>
                            <select id="f_zone" class="form-control" onchange="cascadeFormBranch()">
                                <option value="">— Select Zone —</option>
                                @foreach($allZones as $z)
                                <option value="{{ $z->name }}" data-id="{{ $z->id }}">{{ $z->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Branch (cascades from Zone) --}}
                        <div class="form-group">
                            <label><i class="fa fa-building" style="color:var(--gst-accent);margin-right:3px;"></i>
                                Branch <span style="color:red;">*</span>
                            </label>
                            <select id="f_branch" class="form-control">
                                <option value="">— Select Branch —</option>
                                @foreach($allBranches as $b)
                                <option value="{{ $b->name }}" data-zone-id="{{ $b->zone_id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Month — custom picker --}}
                        <div class="form-group">
                            <label><i class="fa fa-calendar" style="color:var(--gst-accent);margin-right:3px;"></i>
                                Month <span style="color:red;">*</span>
                            </label>
                            <div class="gst-mp-wrap" id="formMp" style="position:relative;">
                                <div class="gst-mp-trigger" onclick="openMp('formMp')" id="formMpTrigger">
                                    <i class="fa fa-calendar-o mp-ico"></i>
                                    <span id="formMpLabel" style="flex:1;">Select Month</span>
                                    <span class="mp-clr" id="formMpClr" onclick="clearMp('formMp',event)" style="display:none;">✕</span>
                                </div>
                                <div class="gst-mp-dropdown" id="formMpDrop">
                                    <div class="gst-mp-head">
                                        <button type="button" class="gst-mp-yr-btn" onclick="mpChangeYear('formMp',-1)">‹</button>
                                        <span class="gst-mp-yr-label" id="formMpYr">2026</span>
                                        <button type="button" class="gst-mp-yr-btn" onclick="mpChangeYear('formMp',1)">›</button>
                                    </div>
                                    <div class="gst-mp-grid" id="formMpGrid"></div>
                                    <div class="gst-mp-foot">
                                        <span class="mp-clear" onclick="clearMp('formMp',event)">Clear</span>
                                        <span class="mp-today" onclick="mpSelectCurrent('formMp')">This Month</span>
                                    </div>
                                </div>
                                <input type="hidden" id="f_month" value="">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- GST 0% -->
                <div class="gst-form-section">
                    <div class="gst-form-section-title" style="background:#e8f5e9;color:#2e7d32;"><i class="fa fa-circle" style="margin-right:5px;font-size:9px;"></i>Under GST 0%</div>
                    <div class="gst-form-grid g2">
                        <div class="form-group">
                            <label>Qty</label>
                            <input type="number" id="f_gst0_qty" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>Taxable Amount</label>
                            <input type="number" id="f_gst0_taxable" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                    </div>
                </div>

                <!-- GST 5% -->
                <div class="gst-form-section">
                    <div class="gst-form-section-title" style="background:#fff3e0;color:#e65100;"><i class="fa fa-tag" style="margin-right:5px;"></i>Under GST 5%</div>
                    <div class="gst-form-grid">
                        <div class="form-group">
                            <label>Qty</label>
                            <input type="number" id="f_gst5_qty" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>Taxable Amount</label>
                            <input type="number" id="f_gst5_taxable" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>CGST <span style="color:#aaa;font-size:10px;">(2.5%)</span></label>
                            <input type="number" id="f_gst5_cgst" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>SGST <span style="color:#aaa;font-size:10px;">(2.5%)</span></label>
                            <input type="number" id="f_gst5_sgst" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                    </div>
                </div>

                <!-- GST 12% -->
                <div class="gst-form-section">
                    <div class="gst-form-section-title" style="background:#f3e5f5;color:#7b1fa2;"><i class="fa fa-tag" style="margin-right:5px;"></i>Under GST 12%</div>
                    <div class="gst-form-grid">
                        <div class="form-group">
                            <label>Qty</label>
                            <input type="number" id="f_gst12_qty" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>Taxable Amount</label>
                            <input type="number" id="f_gst12_taxable" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>CGST <span style="color:#aaa;font-size:10px;">(6%)</span></label>
                            <input type="number" id="f_gst12_cgst" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>SGST <span style="color:#aaa;font-size:10px;">(6%)</span></label>
                            <input type="number" id="f_gst12_sgst" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                    </div>
                </div>

                <!-- GST 18% -->
                <div class="gst-form-section">
                    <div class="gst-form-section-title" style="background:#e3f0ff;color:#1565c0;"><i class="fa fa-tag" style="margin-right:5px;"></i>Under GST 18%</div>
                    <div class="gst-form-grid">
                        <div class="form-group">
                            <label>Qty</label>
                            <input type="number" id="f_gst18_qty" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>Taxable Amount</label>
                            <input type="number" id="f_gst18_taxable" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>CGST <span style="color:#aaa;font-size:10px;">(9%)</span></label>
                            <input type="number" id="f_gst18_cgst" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>SGST <span style="color:#aaa;font-size:10px;">(9%)</span></label>
                            <input type="number" id="f_gst18_sgst" class="form-control" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                    </div>
                </div>

                <!-- Summary & Collection -->
                <div class="gst-form-section">
                    <div class="gst-form-section-title"><i class="fa fa-calculator" style="margin-right:5px;"></i> Summary & Collection</div>

                    {{-- Auto-calc live preview card --}}
                    <div class="calc-preview-card">
                        <div style="font-size:11px;font-weight:700;color:var(--gst-dark);text-transform:uppercase;letter-spacing:.06em;">
                            <i class="fa fa-magic" style="color:var(--gst-accent);margin-right:5px;"></i>
                            Auto-Calculated Totals <span style="font-weight:400;color:#999;">(updates as you type)</span>
                        </div>
                        <div class="calc-preview-grid">
                            <div class="calc-item">
                                <div class="calc-val" id="preview_pharmacy">₹0</div>
                                <div class="calc-lbl">Total Pharmacy</div>
                            </div>
                            <div class="calc-item">
                                <div class="calc-val" id="preview_gst" style="color:var(--gst-orange);">₹0</div>
                                <div class="calc-lbl">Total GST</div>
                            </div>
                            <div class="calc-item">
                                <div class="calc-val" id="preview_exempt" style="color:#888;">₹0</div>
                                <div class="calc-lbl">Exempt Sales</div>
                            </div>
                            <div class="calc-item">
                                <div class="calc-val" id="preview_turnover" style="color:var(--gst-blue);">₹0</div>
                                <div class="calc-lbl">Total Turnover</div>
                            </div>
                            <div class="calc-item" id="preview_diff_card">
                                <div class="calc-val" id="preview_diff">₹0</div>
                                <div class="calc-lbl">Difference</div>
                            </div>
                        </div>
                    </div>

                    {{-- Manual inputs + readonly computed --}}
                    <div class="gst-form-grid" style="grid-template-columns:repeat(3,1fr);">
                        <div class="form-group">
                            <label>
                                <i class="fa fa-pencil-alt" style="color:var(--gst-blue);margin-right:3px;"></i>
                                Exempt Sales <span style="font-size:10px;color:#888;">(enter manually)</span>
                            </label>
                            <input type="number" id="f_exempt_sales" class="form-control f-manual" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>
                                <i class="fa fa-pencil-alt" style="color:var(--gst-blue);margin-right:3px;"></i>
                                Collection <span style="font-size:10px;color:#888;">(enter manually)</span>
                            </label>
                            <input type="number" id="f_collection" class="form-control f-manual" value="0" step="0.0001" min="0" oninput="autoCalc()">
                        </div>
                        <div class="form-group">
                            <label>
                                <i class="fa fa-magic" style="color:var(--gst-accent);margin-right:3px;"></i>
                                Difference <span style="font-size:10px;color:#aaa;">(auto)</span>
                            </label>
                            <input type="number" id="f_difference" class="form-control f-auto-calc" value="0" readonly>
                        </div>
                    </div>
                    {{-- Hidden fields for auto-calc values that get submitted --}}
                    <input type="hidden" id="f_total_pharmacy" value="0">
                    <input type="hidden" id="f_total_gst"      value="0">
                    <input type="hidden" id="f_total_turnover" value="0">
                </div>

                <div style="padding-top:8px;border-top:1px solid #f0f0f0;">
                    <button class="btn-gst-primary" id="btnSaveManual" onclick="saveManual()">
                        <span class="spinner-sm" id="saveSpinner" style="display:none;"></span>
                        <i class="fa fa-floppy-o" id="saveIcon"></i>
                        <span id="saveBtnText">Save Record</span>
                    </button>
                    &nbsp;
                    <button class="btn-gst-outline" onclick="resetManualForm()">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

                </div><!-- /.p-3 inner -->
            </div><!-- /.qd-card -->
        </div><!-- /.pc-content -->
    </div><!-- /.pc-container -->

<!-- ===== TOAST CONTAINER ===== -->
<div class="gst-toast" id="toastContainer"></div>

<script>
const ROUTES = {
    list:       '{{ route("gst_r1.list") }}',
    store:      '{{ route("gst_r1.store") }}',
    showBase:   '{{ url("/gst-r1") }}',
    importUrl:  '{{ route("gst_r1.import") }}',
    exportXlsx: '{{ route("gst_r1.export.xlsx") }}',
    exportCsv:  '{{ route("gst_r1.export.csv") }}',
    csrf:       '{{ csrf_token() }}',
};

/* ================================================================
   STATE
   ================================================================ */
let currentPage = 1;
let totalPages  = 1;

/* ================================================================
   TAB SWITCH
   ================================================================ */
function showTab(tab) {
    document.querySelectorAll('.gst-tab-btn').forEach(b  => b.classList.remove('active'));
    document.querySelectorAll('.gst-tab-pane').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-'  + tab).classList.add('active');
    document.getElementById('pane-' + tab).classList.add('active');
}

/* ================================================================
   LOAD LIST (AJAX)
   ================================================================ */
function loadList(page) {
    page = page || currentPage;
    currentPage = page;

    const params = new URLSearchParams({
        page:     page,
        per_page: document.getElementById('perPage').value,
        search:   document.getElementById('fSearch').value,
        month:    document.getElementById('f_list_month')?.value || '',
        source:   document.getElementById('fSource').value,
        states:   getMsValues('stateMs'),
        zones:    getMsValues('zoneMs'),
        branches: getMsValues('branchMs'),
    });

    document.getElementById('gstBody').innerHTML =
        '<tr class="gst-loading-row"><td colspan="20"><i class="fa fa-spinner fa-spin"></i> Loading…</td></tr>';

    fetch(ROUTES.list + '?' + params.toString())
        .then(r => r.json())
        .then(data => {
            renderTable(data);
            renderPagination(data);
            renderStats(data.totals, data.total);
        })
        .catch(() => toast('Failed to load data', 'error'));
}

/* ================================================================
   RENDER TABLE
   ================================================================ */
function renderTable(data) {
    const tbody = document.getElementById('gstBody');
    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = '<tr class="gst-loading-row"><td colspan="21" style="color:#aaa;"><i class="fa fa-inbox" style="font-size:28px;display:block;margin-bottom:8px;"></i>No records found</td></tr>';
        document.getElementById('footerRow').style.display = 'none';
        return;
    }

    const perPage = data.per_page;
    const offset  = (data.page - 1) * perPage;

    let html = '';
    data.data.forEach((r, i) => {
        const diffClass = r.difference < 0 ? 'amt-negative' : (r.difference > 0 ? 'amt-positive' : '');
        const srcBadge  = r.source === 'import'
            ? '<span class="badge-import"><i class="fa fa-upload"></i> Import</span>'
            : '<span class="badge-manual"><i class="fa fa-pencil-alt"></i> Manual</span>';

        const stateBadge = r.state ? `<span class="state-badge"><i class="fa fa-globe" style="font-size:9px;"></i>${r.state}</span>` : '<span style="color:#ddd;">—</span>';
        const zoneBadge  = r.zone  ? `<span class="zone-badge">${r.zone}</span>` : '<span style="color:#ddd;">—</span>';
        html += `<tr>
            <td class="col-sticky" style="font-weight:700;text-align:center;">${offset + i + 1}</td>
            <td class="col-sticky-2" style="text-align:left;font-weight:600;font-size:12px;letter-spacing:.01em;">${r.branch}</td>
            <td>${r.month}</td>
            <td style="text-align:left;">${stateBadge}</td>
            <td style="text-align:left;">${zoneBadge}</td>
            <td>${fmt(r.gst0_taxable)}</td>
            <td>${fmt(r.gst5_taxable)}</td>
            <td>${fmt(r.gst5_cgst)}</td>
            <td>${fmt(r.gst5_sgst)}</td>
            <td>${fmt(r.gst12_taxable)}</td>
            <td>${fmt(r.gst12_cgst)}</td>
            <td>${fmt(r.gst18_taxable)}</td>
            <td>${fmt(r.gst18_cgst)}</td>
            <td style="font-weight:700;color:var(--gst-primary);">${fmt(r.total_pharmacy)}</td>
            <td style="color:var(--gst-orange);font-weight:700;">${fmt(r.total_gst)}</td>
            <td>${fmt(r.exempt_sales)}</td>
            <td style="font-weight:700;color:var(--gst-blue);">${fmt(r.total_turnover)}</td>
            <td style="color:var(--gst-green);font-weight:700;">${fmt(r.collection)}</td>
            <td class="${diffClass}">${fmtDiff(r.difference)}</td>
            <td>${srcBadge}</td>
            <td>
                <button class="btn-gst-edit" onclick="editRecord(${r.id})" title="Edit"><i class="fa fa-pencil-alt"></i></button>
                &nbsp;
                <!-- <button class="btn-gst-danger" onclick="deleteRecord(${r.id}, '${r.branch}')" title="Delete"><i class="fa fa-trash"></i></button> -->
            </td>
        </tr>`;
    });
    tbody.innerHTML = html;

    // Footer totals
    const t = data.totals;
    if (t) {
        document.getElementById('footerRow').style.display = '';
        document.getElementById('ft_gst0').innerHTML      = fmt(t.sum_pharmacy); // placeholder reuse
        document.getElementById('ft_gst5').textContent    = '—';
        document.getElementById('ft_gst5c').textContent   = '—';
        document.getElementById('ft_gst5s').textContent   = '—';
        document.getElementById('ft_gst12').textContent   = '—';
        document.getElementById('ft_gst12c').textContent  = '—';
        document.getElementById('ft_gst18').textContent   = '—';
        document.getElementById('ft_gst18c').textContent  = '—';
        document.getElementById('ft_pharmacy').innerHTML   = fmt(t.sum_pharmacy);
        document.getElementById('ft_gst').innerHTML        = fmt(t.sum_gst);
        document.getElementById('ft_exempt').innerHTML     = fmt(t.sum_exempt);
        document.getElementById('ft_turnover').innerHTML   = fmt(t.sum_turnover);
        document.getElementById('ft_collection').innerHTML = fmt(t.sum_collection);
        document.getElementById('ft_diff').innerHTML       = fmtDiff(t.sum_difference);
    }
}

/* ================================================================
   RENDER PAGINATION
   ================================================================ */
function renderPagination(data) {
    totalPages = data.last_page;
    const total = data.total, page = data.page, perPage = data.per_page;
    const from  = (page - 1) * perPage + 1;
    const to    = Math.min(page * perPage, total);

    document.getElementById('pgInfo').innerHTML =
        `Showing <strong>${from}–${to}</strong> of <strong>${total}</strong> records`;
    document.getElementById('totalCount').textContent = `(${total} total)`;

    let btns = '';
    btns += `<button ${page===1?'disabled':''} onclick="loadList(1)"><i class="fa fa-angle-double-left"></i></button>`;
    btns += `<button ${page===1?'disabled':''} onclick="loadList(${page-1})"><i class="fa fa-angle-left"></i></button>`;

    let start = Math.max(1, page - 2), end = Math.min(totalPages, page + 2);
    if (start > 1)          btns += `<button onclick="loadList(1)">1</button>`;
    if (start > 2)          btns += `<span style="padding:4px 6px;color:#aaa;">…</span>`;
    for (let p = start; p <= end; p++) {
        btns += `<button class="${p===page?'active':''}" onclick="loadList(${p})">${p}</button>`;
    }
    if (end < totalPages-1) btns += `<span style="padding:4px 6px;color:#aaa;">…</span>`;
    if (end < totalPages)   btns += `<button onclick="loadList(${totalPages})">${totalPages}</button>`;

    btns += `<button ${page===totalPages?'disabled':''} onclick="loadList(${page+1})"><i class="fa fa-angle-right"></i></button>`;
    btns += `<button ${page===totalPages?'disabled':''} onclick="loadList(${totalPages})"><i class="fa fa-angle-double-right"></i></button>`;

    document.getElementById('pgBtns').innerHTML = btns;
}

/* ================================================================
   RENDER STAT CARDS
   ================================================================ */
function renderStats(totals, total) {
    document.getElementById('statBranches').textContent = total;
    if (!totals) return;
    document.getElementById('statPharmacy').textContent   = fmtLakh(totals.sum_pharmacy);
    document.getElementById('statGst').textContent        = fmtLakh(totals.sum_gst);
    document.getElementById('statTurnover').textContent   = fmtLakh(totals.sum_turnover);
    document.getElementById('statCollection').textContent = fmtLakh(totals.sum_collection);
    const diffEl = document.getElementById('statDiff');
    diffEl.textContent  = fmtLakh(totals.sum_difference);
    diffEl.style.color  = totals.sum_difference < 0 ? 'var(--gst-red)' : 'var(--gst-green)';
}

/* ================================================================
   SAVE MANUAL / EDIT
   ================================================================ */
function saveManual() {
    const editId = document.getElementById('editId').value;
    const payload = collectFormData();

    if (!payload.branch || !payload.month) {
        toast('Branch and Month are required', 'error'); return;
    }

    const url    = editId ? `${ROUTES.showBase}/${editId}` : ROUTES.store;
    const method = editId ? 'PUT' : 'POST';

    setBtnLoading('btnSaveManual', 'saveSpinner', 'saveIcon', true);

    fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': ROUTES.csrf },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(data => {
        setBtnLoading('btnSaveManual', 'saveSpinner', 'saveIcon', false);
        if (data.success) {
            toast(editId ? 'Record updated successfully!' : 'Record saved successfully!', 'success');
            resetManualForm();
            showTab('list');
            loadList(1);
        } else {
            toast(data.message || 'Error saving record', 'error');
        }
    })
    .catch(() => { setBtnLoading('btnSaveManual', 'saveSpinner', 'saveIcon', false); toast('Request failed', 'error'); });
}

function collectFormData() {
    const ids = ['state','zone','branch','month',
        'gst0_qty','gst0_taxable',
        'gst5_qty','gst5_taxable','gst5_cgst','gst5_sgst',
        'gst12_qty','gst12_taxable','gst12_cgst','gst12_sgst',
        'gst18_qty','gst18_taxable','gst18_cgst','gst18_sgst',
        'total_pharmacy','total_gst','exempt_sales','total_turnover','collection','difference'];
    const d = {};
    ids.forEach(k => { const el = document.getElementById('f_'+k); if (el) d[k] = el.value; });
    return d;
}

function resetManualForm() {
    document.getElementById('editId').value = '';
    document.getElementById('manualFormTitle').textContent = 'Add Manual Entry';
    document.getElementById('saveBtnText').textContent = 'Save Record';
    const numFields = ['gst0_qty','gst0_taxable','gst5_qty','gst5_taxable','gst5_cgst','gst5_sgst',
        'gst12_qty','gst12_taxable','gst12_cgst','gst12_sgst','gst18_qty','gst18_taxable','gst18_cgst','gst18_sgst',
        'exempt_sales','collection','difference','total_pharmacy','total_gst','total_turnover'];
    numFields.forEach(k => { const el = document.getElementById('f_'+k); if(el) el.value = 0; });
    // Reset selects
    ['state','zone','branch','month'].forEach(k => {
        const el = document.getElementById('f_'+k);
        if (el) el.value = '';
    });
    // Reset all branches visible (remove cascade hiding)
    document.querySelectorAll('#f_branch option').forEach(o => o.style.display = '');
    // Reset month picker display
    clearMp('formMp', null);
    // Reset preview
    autoCalc();
}

/* ================================================================
   EDIT RECORD
   ================================================================ */
function editRecord(id) {
    fetch(`${ROUTES.showBase}/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editId').value = id;
            document.getElementById('manualFormTitle').textContent = `Edit Record — ${d.branch}`;
            document.getElementById('saveBtnText').textContent = 'Update Record';

            // Set state select
            const stateEl = document.getElementById('f_state');
            if (stateEl && d.state) {
                let found = Array.from(stateEl.options).some(o => o.value === d.state);
                if (!found) { const o = document.createElement('option'); o.value = d.state; o.text = d.state; stateEl.appendChild(o); }
                stateEl.value = d.state;
            }

            // Set zone select (triggers branch cascade)
            const zoneEl = document.getElementById('f_zone');
            if (zoneEl) {
                // Try exact match first; if not found add a temporary option
                let found = false;
                for (let opt of zoneEl.options) { if (opt.value === d.zone) { found = true; break; } }
                if (!found && d.zone) {
                    const opt = document.createElement('option');
                    opt.value = d.zone; opt.text = d.zone;
                    zoneEl.appendChild(opt);
                }
                zoneEl.value = d.zone || '';
                cascadeFormBranch();
            }

            // Set branch select – may need to add option if branch not in tbl_locations
            const branchEl = document.getElementById('f_branch');
            if (branchEl) {
                let found = false;
                for (let opt of branchEl.options) { if (opt.value === d.branch) { found = true; break; } }
                if (!found && d.branch) {
                    const opt = document.createElement('option');
                    opt.value = d.branch; opt.text = d.branch;
                    opt.style.display = '';
                    branchEl.appendChild(opt);
                }
                branchEl.value = d.branch || '';
            }

            // Set month picker
            if (d.month) mpSetValue('formMp', d.month);

            // Set numeric fields
            const nums = ['gst0_qty','gst0_taxable','gst5_qty','gst5_taxable','gst5_cgst','gst5_sgst',
                'gst12_qty','gst12_taxable','gst12_cgst','gst12_sgst','gst18_qty','gst18_taxable','gst18_cgst','gst18_sgst',
                'exempt_sales','collection'];
            nums.forEach(k => { const el = document.getElementById('f_'+k); if (el) el.value = d[k] ?? 0; });

            // Trigger auto-calc to populate derived fields
            autoCalc();

            showTab('add');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(() => toast('Could not load record', 'error'));
}

/* ================================================================
   DELETE RECORD
   ================================================================ */
function deleteRecord(id, branch) {
    if (!confirm(`Delete record for "${branch}"? This cannot be undone.`)) return;
    fetch(`${ROUTES.showBase}/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': ROUTES.csrf },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { toast(`"${branch}" deleted`, 'success'); loadList(currentPage); }
        else               toast('Delete failed', 'error');
    })
    .catch(() => toast('Request failed', 'error'));
}

/* ================================================================
   IMPORT
   ================================================================ */
function fileSelected(input) {
    if (!input.files.length) return;
    const file = input.files[0];
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('selectedFile').style.display = 'block';
    document.getElementById('btnImport').disabled = false;
}

function doImport() {
    const fileInput = document.getElementById('importFile');
    if (!fileInput.files.length) { toast('Please select a file', 'error'); return; }

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('overwrite', document.getElementById('overwriteCheck').checked ? '1' : '0');
    formData.append('_token', ROUTES.csrf);

    document.getElementById('importSpinner').style.display = 'inline-block';
    document.getElementById('importIcon').style.display = 'none';
    document.getElementById('btnImport').disabled = true;
    document.getElementById('importResult').style.display = 'none';

    fetch(ROUTES.importUrl, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            document.getElementById('importSpinner').style.display = 'none';
            document.getElementById('importIcon').style.display = 'inline';
            document.getElementById('btnImport').disabled = false;

            const res = document.getElementById('importResult');
            res.style.display = 'block';

            let html = `<div style="font-weight:700;color:var(--gst-dark);margin-bottom:8px;"><i class="fa fa-check-circle" style="color:var(--gst-green);"></i> ${data.message}</div>`;
            html += `<span class="stat" style="color:var(--gst-green);"><i class="fa fa-plus-circle"></i> ${data.inserted} Inserted</span>`;
            html += `<span class="stat" style="color:var(--gst-blue);"><i class="fa fa-refresh"></i> ${data.updated} Updated</span>`;
            html += `<span class="stat" style="color:var(--gst-orange);"><i class="fa fa-minus-circle"></i> ${data.skipped} Skipped</span>`;

            if (data.errors && data.errors.length) {
                html += `<div style="margin-top:10px;color:var(--gst-red);font-size:11px;">
                    <strong>Errors:</strong><br>${data.errors.join('<br>')}
                </div>`;
            }
            res.innerHTML = html;
            toast(data.message, 'success');
            loadList(1); // refresh list
        })
        .catch(() => {
            document.getElementById('importSpinner').style.display = 'none';
            document.getElementById('importIcon').style.display = 'inline';
            document.getElementById('btnImport').disabled = false;
            toast('Import failed. Check file format.', 'error');
        });
}

function resetImport() {
    document.getElementById('importFile').value = '';
    document.getElementById('selectedFile').style.display = 'none';
    document.getElementById('fileName').textContent = '';
    document.getElementById('btnImport').disabled = true;
    document.getElementById('importResult').style.display = 'none';
    document.getElementById('overwriteCheck').checked = false;
}

/* ================================================================
   MONTH-YEAR PICKER
   ================================================================ */
const MP_MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const MP_FULL   = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const mpState   = {}; // { pickerId: { year, selected } }

function mpInit(id) {
    if (!mpState[id]) mpState[id] = { year: new Date().getFullYear(), selected: null };
}

function openMp(id, event) {
    if (event) event.stopPropagation();
    mpInit(id);
    // Close any other open pickers/dropdowns
    document.querySelectorAll('.gst-mp-dropdown.open,.gst-ms-dropdown.open').forEach(d => { if (d.id !== id+'Drop') d.classList.remove('open'); });
    const drop = document.getElementById(id + 'Drop');
    if (!drop) return;
    const isOpen = drop.classList.contains('open');
    drop.classList.toggle('open', !isOpen);
    if (!isOpen) renderMpGrid(id);
}

function renderMpGrid(id) {
    mpInit(id);
    const yr  = mpState[id].year;
    const sel = mpState[id].selected;
    const now = new Date();
    document.getElementById(id + 'Yr').textContent = yr;
    const grid = document.getElementById(id + 'Grid');
    if (!grid) return;
    grid.innerHTML = '';
    MP_MONTHS.forEach((m, i) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'gst-mp-month';
        if (yr === now.getFullYear()) btn.classList.add('today-yr');
        if (sel === MP_FULL[i] + ' ' + yr) btn.classList.add('cur');
        btn.textContent = m;
        btn.title = MP_FULL[i] + ' ' + yr;
        btn.onclick = () => mpSelect(id, MP_FULL[i], yr);
        grid.appendChild(btn);
    });
}

function mpSelect(id, month, year) {
    mpInit(id);
    const val = month + ' ' + year;
    mpState[id].selected = val;
    mpState[id].year     = year;
    // Update trigger label
    const lbl = document.getElementById(id + 'Label');
    if (lbl) lbl.textContent = val;
    const trigger = document.getElementById(id + 'Trigger');
    if (trigger) trigger.classList.add('has-val');
    // Show clear button
    const clr = document.getElementById(id + 'Clr');
    if (clr) clr.style.display = '';
    // Store in hidden input (list filter uses f_list_month, form uses f_month)
    const hiddenId = id === 'listMp' ? 'f_list_month' : 'f_month';
    const hidden = document.getElementById(hiddenId);
    if (hidden) { hidden.value = val; hidden.dispatchEvent(new Event('change')); }
    // Close
    document.getElementById(id + 'Drop')?.classList.remove('open');
    // Re-render to highlight selected
    renderMpGrid(id);
}

function mpSetValue(id, val) {
    if (!val) return;
    const parts = val.match(/^(\w+)\s+(\d{4})$/);
    if (!parts) return;
    mpInit(id);
    mpState[id].selected = val;
    mpState[id].year = parseInt(parts[2]);
    const lbl = document.getElementById(id + 'Label');
    if (lbl) lbl.textContent = val;
    const trigger = document.getElementById(id + 'Trigger');
    if (trigger) trigger.classList.add('has-val');
    const clr = document.getElementById(id + 'Clr');
    if (clr) clr.style.display = '';
    const hiddenId = id === 'listMp' ? 'f_list_month' : 'f_month';
    const hidden = document.getElementById(hiddenId);
    if (hidden) hidden.value = val;
}

function clearMp(id, event) {
    if (event) event.stopPropagation();
    mpInit(id);
    mpState[id].selected = null;
    const lbl = document.getElementById(id + 'Label');
    if (lbl) lbl.textContent = id === 'listMp' ? 'All Months' : 'Select Month';
    const trigger = document.getElementById(id + 'Trigger');
    if (trigger) trigger.classList.remove('has-val');
    const clr = document.getElementById(id + 'Clr');
    if (clr) clr.style.display = 'none';
    const hiddenId = id === 'listMp' ? 'f_list_month' : 'f_month';
    const hidden = document.getElementById(hiddenId);
    if (hidden) hidden.value = '';
    renderMpGrid(id);
}

function mpChangeYear(id, delta) {
    mpInit(id);
    mpState[id].year = Math.max(2020, Math.min(2035, mpState[id].year + delta));
    renderMpGrid(id);
}

function mpSelectCurrent(id) {
    const now = new Date();
    mpSelect(id, MP_FULL[now.getMonth()], now.getFullYear());
}

/* ================================================================
   MULTI-SELECT
   ================================================================ */
const msState = {}; // { wrapperId: Set of selected values }

function getMsValues(id) {
    return msState[id] ? Array.from(msState[id]).join(',') : '';
}

function toggleMs(id) {
    const drop = document.getElementById(id + 'Drop');
    if (!drop) return;
    const isOpen = drop.classList.contains('open');
    // Close all open dropdowns/pickers
    document.querySelectorAll('.gst-ms-dropdown.open,.gst-mp-dropdown.open').forEach(d => d.classList.remove('open'));
    if (!isOpen) drop.classList.add('open');
}

function toggleMsItem(el, id) {
    if (!msState[id]) msState[id] = new Set();
    const val = el.dataset.val;
    if (msState[id].has(val)) { msState[id].delete(val); el.classList.remove('sel'); }
    else                       { msState[id].add(val);    el.classList.add('sel');    }
    updateMsTrigger(id);
}

function updateMsTrigger(id) {
    if (!msState[id]) msState[id] = new Set();
    const count = msState[id].size;
    const labels = { stateMs:'All States', zoneMs:'All Zones', branchMs:'All Branches' };
    const lbl   = document.getElementById(id + 'Label');
    const trig  = document.getElementById(id + 'Trigger');
    if (count === 0) {
        if (lbl) lbl.innerHTML = labels[id] || 'All';
        if (trig) trig.classList.remove('has-val');
    } else {
        if (lbl) lbl.innerHTML = `<span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${count} selected</span><span class="gst-ms-badge">${count}</span>`;
        if (trig) trig.classList.add('has-val');
    }
}

function clearMs(id) {
    if (!msState[id]) msState[id] = new Set();
    msState[id].clear();
    document.querySelectorAll(`#${id}List .gst-ms-opt`).forEach(el => el.classList.remove('sel'));
    updateMsTrigger(id);
}

function filterMsOpts(id) {
    const searchId = id.replace('Ms','') + 'MsSearch'; // e.g. stateMsSearch
    const search = document.getElementById(searchId)?.value?.toLowerCase() || '';
    document.querySelectorAll(`#${id}List .gst-ms-opt`).forEach(el => {
        const txt = (el.dataset.val || '').toLowerCase();
        el.style.display = txt.includes(search) ? '' : 'none';
    });
}

function applyMsFilter() {
    // Close all dropdowns and reload
    document.querySelectorAll('.gst-ms-dropdown.open').forEach(d => d.classList.remove('open'));
    loadList(1);
}

/* ================================================================
   AUTO-CALCULATION
   ================================================================ */
function n(id) { return parseFloat(document.getElementById(id)?.value || 0) || 0; }

function autoCalc() {
    // Taxable sums per slab
    const pharmacy = n('f_gst0_taxable') + n('f_gst5_taxable') + n('f_gst12_taxable') + n('f_gst18_taxable');
    // Total GST = sum of all CGST + SGST
    const totalGst  = n('f_gst5_cgst')  + n('f_gst5_sgst')
                    + n('f_gst12_cgst') + n('f_gst12_sgst')
                    + n('f_gst18_cgst') + n('f_gst18_sgst');
    const exempt    = n('f_exempt_sales');
    // Turnover = Total Pharmacy (taxable) + Exempt Sales + Total GST
    const turnover  = pharmacy + exempt + totalGst;
    const collection= n('f_collection');
    const diff      = turnover - collection;

    // Update hidden/readonly fields
    document.getElementById('f_total_pharmacy').value = pharmacy.toFixed(4);
    document.getElementById('f_total_gst').value      = totalGst.toFixed(4);
    document.getElementById('f_total_turnover').value  = turnover.toFixed(4);
    document.getElementById('f_difference').value      = diff.toFixed(4);

    // Update preview card
    const fmtP = v => '₹' + Math.abs(v).toLocaleString('en-IN', { maximumFractionDigits: 2 });
    document.getElementById('preview_pharmacy').textContent = fmtP(pharmacy);
    document.getElementById('preview_gst').textContent      = fmtP(totalGst);
    document.getElementById('preview_exempt').textContent   = fmtP(exempt);
    document.getElementById('preview_turnover').textContent = fmtP(turnover);

    const diffEl   = document.getElementById('preview_diff');
    const cardEl   = document.getElementById('preview_diff_card');
    diffEl.textContent = (diff < 0 ? '▼ ' : diff > 0 ? '▲ ' : '') + fmtP(diff);
    cardEl.className   = 'calc-item' + (diff < 0 ? ' calc-diff-neg' : diff > 0 ? ' calc-diff-pos' : '');
}

/* ================================================================
   CASCADE BRANCH (form – filter by zone)
   ================================================================ */
function cascadeFormBranch() {
    const zoneEl   = document.getElementById('f_zone');
    const branchEl = document.getElementById('f_branch');
    if (!zoneEl || !branchEl) return;

    const selectedZone   = zoneEl.options[zoneEl.selectedIndex];
    const selectedZoneId = selectedZone?.dataset?.id || '';
    const selectedVal    = branchEl.value; // preserve current selection

    branchEl.value = ''; // reset first
    let hasVisible = false;
    Array.from(branchEl.options).forEach(opt => {
        if (!opt.value) { opt.style.display = ''; return; } // keep the placeholder
        const zid = opt.dataset?.zoneId || '';
        const show = !selectedZoneId || zid == selectedZoneId;
        opt.style.display = show ? '' : 'none';
        if (show) hasVisible = true;
    });

    // Re-select if still visible
    if (selectedVal) {
        const stillVisible = Array.from(branchEl.options).some(o => o.value === selectedVal && o.style.display !== 'none');
        if (stillVisible) branchEl.value = selectedVal;
    }
}

/* ================================================================
   CLOSE DROPDOWNS ON OUTSIDE CLICK
   ================================================================ */
document.addEventListener('click', function(e) {
    if (!e.target.closest('.gst-ms-wrap') && !e.target.closest('.gst-mp-wrap')) {
        document.querySelectorAll('.gst-ms-dropdown.open,.gst-mp-dropdown.open').forEach(d => d.classList.remove('open'));
    }
});

/* ================================================================
   CLEAR LIST FILTERS
   ================================================================ */
function clearListFilters() {
    clearMs('stateMs'); clearMs('zoneMs'); clearMs('branchMs');
    clearMp('listMp', null);
    document.getElementById('fSource').value = '';
    document.getElementById('fSearch').value = '';
    loadList(1);
}

/* ================================================================
   EXPORT
   ================================================================ */
function exportData(type) {
    const params = new URLSearchParams({
        month:    document.getElementById('f_list_month')?.value || '',
        source:   document.getElementById('fSource').value,
        states:   getMsValues('stateMs'),
        zones:    getMsValues('zoneMs'),
        branches: getMsValues('branchMs'),
    });
    const url = (type === 'xlsx' ? ROUTES.exportXlsx : ROUTES.exportCsv) + '?' + params.toString();
    window.location.href = url;
    toast(`Preparing ${type.toUpperCase()} export…`, 'success');
}

/* ================================================================
   DRAG & DROP
   ================================================================ */
const dropZone = document.getElementById('dropZone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length) {
        document.getElementById('importFile').files = files;
        fileSelected(document.getElementById('importFile'));
    }
});

/* ================================================================
   HELPERS
   ================================================================ */
function fmt(val) {
    if (val === null || val === undefined || val === '' || val == 0) return '<span style="color:#ccc;">—</span>';
    return '₹' + parseFloat(val).toLocaleString('en-IN', { maximumFractionDigits: 2 });
}
function fmtDiff(val) {
    if (val === null || val === undefined) return '—';
    const n = parseFloat(val);
    const f = '₹' + Math.abs(n).toLocaleString('en-IN', { maximumFractionDigits: 2 });
    if (n < 0) return `<span class="amt-negative">▼ ${f}</span>`;
    if (n > 0) return `<span class="amt-positive">▲ ${f}</span>`;
    return f;
}
function fmtLakh(val) {
    if (!val) return '₹0';
    const n = Math.abs(parseFloat(val));
    if (n >= 10000000) return (n / 10000000).toFixed(2) + ' Cr';
    if (n >= 100000)   return (n / 100000).toFixed(2) + ' L';
    return '₹' + n.toLocaleString('en-IN', { maximumFractionDigits: 0 });
}

function setBtnLoading(btnId, spinnerId, iconId, loading) {
    document.getElementById(spinnerId).style.display = loading ? 'inline-block' : 'none';
    document.getElementById(iconId).style.display    = loading ? 'none' : 'inline';
    document.getElementById(btnId).disabled = loading;
}

function toast(msg, type = 'success') {
    const container = document.getElementById('toastContainer');
    const el = document.createElement('div');
    el.className = `toast-item ${type}`;
    const icons = { success: 'check-circle', error: 'times-circle', warning: 'exclamation-triangle' };
    const colors = { success: 'var(--gst-green)', error: 'var(--gst-red)', warning: 'var(--gst-orange)' };
    el.innerHTML = `<i class="fa fa-${icons[type]||'info-circle'}" style="font-size:18px;color:${colors[type]};flex-shrink:0;"></i>
        <span>${msg}</span>`;
    container.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .3s'; setTimeout(() => el.remove(), 300); }, 3500);
}

/* ================================================================
   EVENT LISTENERS
   ================================================================ */
document.getElementById('fSearch').addEventListener('keydown', e => { if (e.key === 'Enter') loadList(1); });
document.getElementById('perPage').addEventListener('change', () => loadList(1));

/* ================================================================
   INIT
   ================================================================ */
document.addEventListener('DOMContentLoaded', () => {
    loadList(1);
    autoCalc();
    // Init month pickers for current year
    renderMpGrid('listMp');
    renderMpGrid('formMp');
});
</script>

@include('superadmin.superadminfooter')
</body>
<!-- [Body] end -->
</html>