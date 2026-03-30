<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

<style>
/* ════════════════════════════════════════════════════════════════
   RADIANT CASH PICKUP — Design System
   Aesthetic: Financial precision meets warm energy
   Palette: Deep navy + warm amber + teal
════════════════════════════════════════════════════════════════ */
:root {
    --navy:     #0f172a;
    --navy2:    #1e293b;
    --navy3:    #334155;
    --amber:    #f59e0b;
    --amber2:   #d97706;
    --amber-lt: #fef3c7;
    --teal:     #0d9488;
    --teal-lt:  #ccfbf1;
    --rose:     #f43f5e;
    --rose-lt:  #ffe4e6;
    --violet:   #8b5cf6;
    --violet-lt:#ede9fe;
    --surface:  #fff;
    --surface2: #f8fafc;
    --border:   #e2e8f0;
    --border2:  #cbd5e1;
    --text:     #0f172a;
    --text2:    #475569;
    --text3:    #94a3b8;
    --radius:   14px;
    --radius-sm:9px;
    --shadow:   0 4px 24px rgba(15,23,42,.08);
    --shadow-lg:0 16px 48px rgba(15,23,42,.14);
    --font:     'Plus Jakarta Sans', sans-serif;
    --mono:     'JetBrains Mono', monospace;
}
*{box-sizing:border-box;}
body{font-family:var(--font);background:#f1f5f9;}

/* ── WRAPPER ── */
.rcp-wrap{padding:28px 24px;}

/* ── PAGE HEADER ── */
.rcp-header{
    background:linear-gradient(135deg,var(--navy) 0%,var(--navy2) 60%,#1e3a5f 100%);
    border-radius:20px;padding:28px 32px;margin-bottom:24px;
    position:relative;overflow:hidden;
}
.rcp-header::before{
    content:'';position:absolute;top:-60px;right:-60px;
    width:260px;height:260px;border-radius:50%;
    background:radial-gradient(circle,rgba(245,158,11,.15),transparent 70%);
}
.rcp-header::after{
    content:'';position:absolute;bottom:-40px;left:160px;
    width:180px;height:180px;border-radius:50%;
    background:radial-gradient(circle,rgba(13,148,136,.12),transparent 70%);
}
.rcp-header-inner{position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;}
.rcp-header-title{display:flex;align-items:center;gap:16px;}
.rcp-header-icon{
    width:52px;height:52px;background:rgba(245,158,11,.15);
    border:1px solid rgba(245,158,11,.3);border-radius:14px;
    display:flex;align-items:center;justify-content:center;font-size:1.5rem;
}
.rcp-header-text h1{font-size:1.4rem;font-weight:800;color:#fff;margin:0 0 3px;letter-spacing:-.4px;}
.rcp-header-text p{font-size:.8rem;color:rgba(255,255,255,.6);margin:0;}
.rcp-header-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}

/* ── HEADER BUTTONS ── */
.hbtn{
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 20px;border-radius:var(--radius-sm);
    font-size:.82rem;font-weight:700;cursor:pointer;
    border:none;font-family:var(--font);white-space:nowrap;
    transition:all .15s;
}
.hbtn-amber{background:var(--amber);color:var(--navy);}
.hbtn-amber:hover{background:var(--amber2);transform:translateY(-1px);box-shadow:0 4px 14px rgba(245,158,11,.4);}
.hbtn-outline{background:rgba(255,255,255,.08);color:rgba(255,255,255,.85);border:1px solid rgba(255,255,255,.18);}
.hbtn-outline:hover{background:rgba(255,255,255,.15);}

/* ── STAT CARDS ── */
.stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:24px;}
.stat-card{
    background:var(--surface);border-radius:var(--radius);
    padding:20px 22px;border:1px solid var(--border);
    box-shadow:var(--shadow);position:relative;overflow:hidden;
    transition:transform .15s,box-shadow .15s;
}
.stat-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg);}
.stat-card::after{
    content:'';position:absolute;top:0;left:0;right:0;height:3px;
    border-radius:var(--radius) var(--radius) 0 0;
}
.sc-amber::after{background:linear-gradient(90deg,var(--amber),#fbbf24);}
.sc-teal::after{background:linear-gradient(90deg,var(--teal),#2dd4bf);}
.sc-violet::after{background:linear-gradient(90deg,var(--violet),#c084fc);}
.sc-rose::after{background:linear-gradient(90deg,var(--rose),#fb7185);}
.stat-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:12px;}
.sc-amber .stat-icon{background:var(--amber-lt);color:var(--amber2);}
.sc-teal  .stat-icon{background:var(--teal-lt);color:var(--teal);}
.sc-violet .stat-icon{background:var(--violet-lt);color:var(--violet);}
.sc-rose  .stat-icon{background:var(--rose-lt);color:var(--rose);}
.stat-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text3);margin-bottom:3px;}
.stat-value{font-size:1.35rem;font-weight:800;color:var(--text);font-family:var(--mono);letter-spacing:-.5px;}
.stat-sub{font-size:.72rem;color:var(--text3);margin-top:2px;}

/* ── UPLOAD MODAL OVERLAY ── */
.upload-overlay{
    position:fixed;inset:0;background:rgba(15,23,42,.65);
    backdrop-filter:blur(6px);z-index:1000;
    display:none;align-items:center;justify-content:center;
    animation:fadeIn .2s ease;
}
.upload-overlay.show{display:flex;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}

.upload-modal{
    background:var(--surface);border-radius:22px;
    width:100%;max-width:560px;overflow:hidden;
    box-shadow:0 32px 80px rgba(0,0,0,.25);
    animation:slideUp .28s cubic-bezier(.22,1,.36,1);
}
@keyframes slideUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

.um-header{
    background:linear-gradient(135deg,var(--navy),var(--navy2));
    padding:22px 26px;display:flex;align-items:center;justify-content:space-between;
}
.um-header-left{display:flex;align-items:center;gap:12px;}
.um-icon{
    width:40px;height:40px;background:rgba(245,158,11,.2);
    border:1px solid rgba(245,158,11,.35);border-radius:11px;
    display:flex;align-items:center;justify-content:center;
    font-size:1.15rem;
}
.um-title{font-size:1rem;font-weight:800;color:#fff;}
.um-sub{font-size:.73rem;color:rgba(255,255,255,.55);margin-top:1px;}
.um-close{
    width:32px;height:32px;background:rgba(255,255,255,.1);
    border:1px solid rgba(255,255,255,.18);border-radius:8px;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:background .14s;color:rgba(255,255,255,.7);
    font-size:.9rem;
}
.um-close:hover{background:rgba(255,255,255,.2);}

.um-body{padding:26px;}

/* Drop Zone — <label for="fileInput"> opens file dialog reliably in all browsers */
.drop-zone{
    border:2px dashed var(--border2);border-radius:14px;
    padding:40px 24px;text-align:center;cursor:pointer;
    transition:all .2s;position:relative;background:var(--surface2);
    display:block;margin:0;
}
.drop-zone:hover,.drop-zone.drag-over{
    border-color:var(--amber);background:var(--amber-lt);
}
.rcp-file-hidden{
    position:absolute;
    left:-9999px;
    width:1px;
    height:1px;
    opacity:0;
    overflow:hidden;
    clip:rect(0,0,0,0);
    white-space:nowrap;
    border:0;
}
.dz-icon{font-size:2.5rem;margin-bottom:12px;display:block;}
.dz-title{font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:5px;}
.dz-sub{font-size:.78rem;color:var(--text3);}
.dz-file-name{
    margin-top:12px;padding:8px 14px;background:var(--amber-lt);
    border:1px solid #fcd34d;border-radius:8px;
    font-size:.78rem;font-weight:700;color:var(--amber2);
    display:none;align-items:center;gap:6px;
}

/* Upload rules */
.upload-rules{
    background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;
    padding:14px 16px;margin-top:16px;
}
.upload-rules h6{font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#15803d;margin-bottom:8px;}
.upload-rules ul{margin:0;padding-left:16px;}
.upload-rules li{font-size:.75rem;color:#166534;margin-bottom:3px;font-weight:600;}

.um-footer{padding:16px 26px 22px;display:flex;gap:10px;}
.btn-upload{
    flex:1;padding:11px;border-radius:10px;border:none;
    background:var(--amber);color:var(--navy);
    font-family:var(--font);font-size:.85rem;font-weight:800;
    cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:7px;
}
.btn-upload:hover{background:var(--amber2);transform:translateY(-1px);box-shadow:0 4px 14px rgba(245,158,11,.4);}
.btn-cancel{
    padding:11px 20px;border-radius:10px;border:1.5px solid var(--border2);
    background:var(--surface);color:var(--text2);
    font-family:var(--font);font-size:.82rem;font-weight:700;cursor:pointer;
    transition:all .14s;
}
.btn-cancel:hover{border-color:var(--rose);color:var(--rose);}

/* ── FILTER MODAL ── */
.filter-overlay{
    position:fixed;inset:0;background:rgba(15,23,42,.55);
    backdrop-filter:blur(4px);z-index:900;
    display:none;align-items:center;justify-content:center;
}
.filter-overlay.show{display:flex;}
.filter-modal{
    background:var(--surface);border-radius:20px;
    width:100%;max-width:680px;overflow:hidden;
    box-shadow:0 24px 64px rgba(0,0,0,.2);
    animation:slideUp .25s cubic-bezier(.22,1,.36,1);
}
.fm-header{
    background:linear-gradient(135deg,#1e293b,#334155);
    padding:18px 24px;display:flex;align-items:center;justify-content:space-between;
}
.fm-title{font-size:.95rem;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;}
.fm-close{
    width:30px;height:30px;background:rgba(255,255,255,.1);
    border:1px solid rgba(255,255,255,.18);border-radius:7px;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:rgba(255,255,255,.7);font-size:.85rem;
}
.fm-close:hover{background:rgba(255,255,255,.2);}
.fm-body{padding:22px 24px;display:flex;flex-direction:column;gap:16px;}
.fm-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:12px;}
.fm-field label{display:block;font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.7px;color:var(--text3);margin-bottom:5px;}
.fm-field .fc{
    width:100%;padding:8px 12px;border:1.5px solid var(--border);
    border-radius:var(--radius-sm);font-size:.82rem;font-family:var(--font);
    color:var(--text);background:var(--surface2);
    transition:border-color .15s,box-shadow .15s;
}
.fm-field .fc:focus{outline:none;border-color:var(--amber);box-shadow:0 0 0 3px rgba(245,158,11,.12);}
.fm-field .fc-date{padding-left:34px;}
.date-wrap{position:relative;}
.date-wrap .cal-ico{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--amber);font-size:.85rem;pointer-events:none;}

/* Active filter chips */
.active-chips{display:flex;flex-wrap:wrap;gap:6px;min-height:28px;align-items:center;padding:10px 24px;background:#f8fafc;border-bottom:1px solid var(--border);}
.fchip{
    display:inline-flex;align-items:center;gap:4px;
    padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;
    background:var(--amber-lt);color:var(--amber2);border:1px solid #fcd34d;
}
.fchip .rx{cursor:pointer;opacity:.6;margin-left:2px;}
.fchip .rx:hover{opacity:1;}
.no-chip{font-size:.75rem;color:var(--text3);font-style:italic;}

.fm-footer{padding:14px 24px 20px;display:flex;gap:10px;justify-content:flex-end;border-top:1px solid var(--border);background:var(--surface2);}
.btn-apply-f{
    padding:9px 24px;border-radius:9px;border:none;
    background:linear-gradient(135deg,var(--navy2),var(--navy3));
    color:#fff;font-family:var(--font);font-size:.82rem;font-weight:800;
    cursor:pointer;display:flex;align-items:center;gap:6px;
    box-shadow:0 4px 14px rgba(15,23,42,.25);transition:all .14s;
}
.btn-apply-f:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(15,23,42,.35);}
.btn-reset-f{
    padding:9px 18px;border-radius:9px;border:1.5px solid var(--border2);
    background:var(--surface);color:var(--text2);
    font-family:var(--font);font-size:.82rem;font-weight:700;cursor:pointer;
    transition:all .14s;
}
.btn-reset-f:hover{border-color:var(--rose);color:var(--rose);}

/* ── TABLE TOOLBAR ── */
.tbl-toolbar{
    display:flex;align-items:center;justify-content:space-between;
    flex-wrap:wrap;gap:12px;margin-bottom:14px;
}
.toolbar-left{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}

/* Search input */
.search-wrap{position:relative;}
.search-input{
    padding:8px 12px 8px 36px;border:1.5px solid var(--border);
    border-radius:var(--radius-sm);font-size:.82rem;font-family:var(--font);
    background:var(--surface);color:var(--text);width:240px;
    transition:all .15s;
}
.search-input:focus{outline:none;border-color:var(--amber);box-shadow:0 0 0 3px rgba(245,158,11,.1);width:280px;}
.search-ico{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text3);font-size:.9rem;}

/* Filter toggle button */
.btn-filter{
    display:inline-flex;align-items:center;gap:6px;
    padding:8px 16px;border-radius:var(--radius-sm);border:1.5px solid var(--border2);
    background:var(--surface);color:var(--text2);font-family:var(--font);
    font-size:.8rem;font-weight:700;cursor:pointer;transition:all .14s;position:relative;
}
.btn-filter:hover{border-color:var(--amber);color:var(--amber2);}
.btn-filter.active{background:var(--amber-lt);border-color:var(--amber);color:var(--amber2);}
.filter-dot{
    position:absolute;top:-4px;right:-4px;
    width:14px;height:14px;background:var(--amber);border-radius:50%;
    display:none;align-items:center;justify-content:center;
    font-size:.55rem;font-weight:800;color:var(--navy);border:2px solid var(--surface);
}
.filter-dot.show{display:flex;}

.per-page-select{
    padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;
    font-size:.78rem;font-family:var(--font);color:var(--text2);background:var(--surface);cursor:pointer;
}
.per-page-select:focus{outline:none;border-color:var(--amber);}

/* ── TABLE ── */
.tbl-card{
    background:var(--surface);border-radius:var(--radius);
    border:1px solid var(--border);overflow:hidden;
    box-shadow:var(--shadow);position:relative;
}
.tbl-card table{width:100%;margin:0;border-collapse:collapse;}
.tbl-card thead th{
    background:#1e293b;color:rgba(255,255,255,.75);
    font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.7px;
    padding:12px 14px;white-space:nowrap;border-bottom:none;
    position:sticky;top:0;z-index:2;
}
.tbl-card thead th:first-child{color:rgba(255,255,255,.5);}
.tbl-card tbody td{
    font-size:.8rem;color:var(--text2);
    padding:11px 14px;border-bottom:1px solid #f1f5f9;
    vertical-align:middle;
}
.tbl-card tbody tr:last-child td{border-bottom:none;}
.tbl-card tbody tr:hover td{background:#fafbfc;}

/* Badges */
.bdg{display:inline-block;padding:3px 9px;border-radius:20px;font-size:.65rem;font-weight:800;white-space:nowrap;}
.bdg-amber{background:var(--amber-lt);color:var(--amber2);}
.bdg-teal{background:var(--teal-lt);color:var(--teal);}
.bdg-violet{background:var(--violet-lt);color:var(--violet);}
.bdg-navy{background:#e2e8f0;color:var(--navy3);}
.bdg-green{background:#dcfce7;color:#166534;}

/* Amount */
.amt{font-family:var(--mono);font-weight:700;color:var(--text);font-size:.82rem;}
.amt-big{color:var(--teal);font-size:.85rem;}

/* Denomination pills */
.denom-row{display:flex;flex-wrap:wrap;gap:3px;}
.denom-pill{
    font-size:.58rem;font-weight:700;padding:2px 5px;border-radius:4px;
    background:#f1f5f9;color:var(--text3);font-family:var(--mono);
}
.denom-pill.has-val{background:var(--amber-lt);color:var(--amber2);}

/* Serial number */
.sno-cell{font-family:var(--mono);font-size:.75rem;color:var(--text3);font-weight:600;}

/* CCV badge */
.ccv-yes{background:#dcfce7;color:#15803d;}
.ccv-no {background:var(--rose-lt);color:var(--rose);}

/* ── PAGINATION (single row: counts + page + nav) ── */
.pg-wrap.rcp-pg-row{
    display:flex;align-items:center;justify-content:space-between;
    padding:13px 18px;border-top:1px solid var(--border);flex-wrap:wrap;gap:12px;
}
.rcp-pg-meta{
    font-size:.75rem;color:var(--text3);font-weight:600;
    flex:1 1 auto;min-width:200px;
}
.rcp-pg-meta strong{color:var(--text);}
.rcp-pg-sep{margin:0 6px;opacity:.45;}
.rcp-pg-links-col{flex:0 0 auto;display:flex;align-items:center;justify-content:flex-end;}
.pg-links .page-link{
    border-radius:7px !important;margin:0 2px;font-size:.78rem;font-weight:700;
    color:var(--navy2);border-color:var(--border);
}
.pg-links .page-item.active .page-link{
    background:var(--navy2);border-color:var(--navy2);color:#fff;
}
.rcp-pag-nav .pagination{gap:2px;}
.rcp-pag-nav .page-link{min-width:2.25rem;text-align:center;}

/* ── EMPTY STATE ── */
.empty-state{text-align:center;padding:60px 24px;color:var(--text3);}
.empty-state i{font-size:2.8rem;display:block;margin-bottom:12px;opacity:.3;}
.empty-state p{font-size:.875rem;margin:0;}

/* ── UPLOAD PROGRESS ── */
.upload-progress{display:none;margin-top:14px;}
.up-bar{height:5px;background:var(--border);border-radius:3px;overflow:hidden;}
.up-fill{height:100%;background:linear-gradient(90deg,var(--amber),var(--teal));border-radius:3px;animation:upAnim 1.5s ease infinite;}
@keyframes upAnim{0%{width:0%}50%{width:80%}100%{width:100%}}
.up-label{font-size:.75rem;color:var(--text3);margin-top:6px;font-weight:600;}

/* ── SCROLLABLE TABLE WRAP ── */
.tbl-scroll{overflow-x:auto;}
.tbl-scroll::-webkit-scrollbar{height:4px;}
.tbl-scroll::-webkit-scrollbar-track{background:transparent;}
.tbl-scroll::-webkit-scrollbar-thumb{background:var(--border2);border-radius:3px;}

@media(max-width:640px){
    .rcp-wrap{padding:14px 10px;}
    .stat-grid{grid-template-columns:repeat(2,1fr);}
    .fm-row{grid-template-columns:1fr;}
    .search-input{width:100%;}
}
</style>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container"><div class="pc-content">
<div class="rcp-wrap">

{{-- ── PAGE HEADER ─────────────────────────────────────────────────── --}}
<div class="rcp-header">
  <div class="rcp-header-inner">
    <div class="rcp-header-title">
      <div class="rcp-header-icon">🏦</div>
      <div class="rcp-header-text">
        <h1>Radiant Cash Pickup MIS</h1>
        <p>Cash collection records — upload, search and reconcile</p>
      </div>
    </div>
    <div class="rcp-header-actions">
      <button type="button" class="hbtn hbtn-amber" id="rcpOpenUploadBtn">
        <i class="bi bi-cloud-upload-fill"></i> Upload Excel
      </button>
      <button type="button" class="hbtn hbtn-outline" id="rcpRefreshBtn">
        <i class="bi bi-arrow-clockwise"></i> Refresh
      </button>
    </div>
  </div>
</div>

{{-- ── FLASH MESSAGES ───────────────────────────────────────────────── --}}
@if(session('success'))
  <div class="alert alert-success d-flex align-items-center gap-2 mb-3 border-0 rounded-3 shadow-sm"
       style="background:#f0fdf4;border-left:4px solid #22c55e !important;">
    <i class="bi bi-check-circle-fill text-success"></i>
    <span style="font-size:.83rem;font-weight:600;color:#15803d;">{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 border-0 rounded-3 shadow-sm">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span style="font-size:.83rem;font-weight:600;">{{ session('error') }}</span>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- ── STAT CARDS ───────────────────────────────────────────────────── --}}
<div class="stat-grid">
  <div class="stat-card sc-amber">
    <div class="stat-icon"><i class="bi bi-currency-rupee"></i></div>
    <div class="stat-label">Total Pickup Amount</div>
    <div class="stat-value" id="rcpStatAmount">₹{{ number_format($totalAmount, 0) }}</div>
    <div class="stat-sub">Current filters</div>
  </div>
  <div class="stat-card sc-teal">
    <div class="stat-icon"><i class="bi bi-list-check"></i></div>
    <div class="stat-label">Total Records</div>
    <div class="stat-value" id="rcpStatRecords">{{ number_format($totalRecords) }}</div>
    <div class="stat-sub">Current filters</div>
  </div>
  <div class="stat-card sc-violet">
    <div class="stat-icon"><i class="bi bi-collection-fill"></i></div>
    <div class="stat-label">Upload Batches</div>
    <div class="stat-value" id="rcpStatBatches">{{ $totalBatches }}</div>
    <div class="stat-sub">Current filters</div>
  </div>
  <div class="stat-card sc-rose">
    <div class="stat-icon"><i class="bi bi-geo-alt-fill"></i></div>
    <div class="stat-label">Locations</div>
    <div class="stat-value" id="rcpStatLocations">{{ $locationsCount }}</div>
    <div class="stat-sub">Distinct locations</div>
  </div>
</div>

{{-- ── ACTIVE FILTER CHIPS ───────────────────────────────────────────── --}}
<div class="active-chips" id="activeChips">
  <span class="no-chip" id="noChip">No filters applied</span>
</div>

{{-- ── TABLE TOOLBAR ─────────────────────────────────────────────────── --}}
<div class="tbl-toolbar">
  <div class="toolbar-left">
    <div class="search-wrap">
      <i class="bi bi-search search-ico"></i>
      <input type="text" class="search-input" id="searchInput"
             placeholder="Search by state, location, slip no…"
             value="{{ request('search') }}"
             autocomplete="off">
    </div>
    <button type="button" class="btn-filter" id="filterBtn">
      <i class="bi bi-sliders2"></i> Filters
      <span class="filter-dot" id="filterDot"></span>
    </button>
    <select class="per-page-select" id="rcpPerPage">
      @foreach([25,50,100,250] as $pp)
        <option value="{{ $pp }}" {{ request('per_page', 25) == $pp ? 'selected' : '' }}>{{ $pp }} / page</option>
      @endforeach
    </select>
  </div>
</div>

{{-- ── DATA TABLE ────────────────────────────────────────────────────── --}}
<div class="tbl-card">
  <div class="tbl-scroll">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>State</th>
          <th>Region / Zone</th>
          <th>Location</th>
          <th>HCI Slip No</th>
          <th>Point ID</th>
          <th>Pickup Amount</th>
          <th>Cash Limit</th>
          <th>Deposit Mode</th>
          <th>Denominations (₹)</th>
          <th>Coins</th>
          <th>Difference</th>
          <th>Remarks</th>
          <th>CCV</th>
          <th>Batch</th>
        </tr>
      </thead>
      <tbody id="rcpTableBody">
        @include('Radiant.partials.radiant_cash_rows', ['records' => $records])
      </tbody>
    </table>
  </div>

  <div id="rcpPaginationHost">
    @include('Radiant.partials.radiant_cash_pagination', ['records' => $records])
  </div>
</div>

</div>{{-- rcp-wrap --}}
</div></div>

{{-- ══════════════════════════════════════════════════════════════════
     UPLOAD MODAL
══════════════════════════════════════════════════════════════════ --}}
<div class="upload-overlay" id="uploadOverlay">
  <div class="upload-modal" id="uploadModalInner">
    <div class="um-header">
      <div class="um-header-left">
        <div class="um-icon">📤</div>
        <div>
          <div class="um-title">Upload Radiant Excel</div>
          <div class="um-sub">Cashpickup MIS — .xlsx format only</div>
        </div>
      </div>
      <div class="um-close" id="rcpUploadClose" role="button" tabindex="0"><i class="bi bi-x-lg"></i></div>
    </div>
    <form method="POST" action="{{ route('superadmin.radiantcash.upload') }}"
          enctype="multipart/form-data" id="uploadForm">
      @csrf
      <div class="um-body">

        <label id="dropZone" class="drop-zone">
          <span class="dz-icon">📂</span>
          <div class="dz-title">Drag &amp; drop Excel file here</div>
          <div class="dz-sub">or click to browse — .xlsx / .xls supported, max 10 MB</div>
          <div class="dz-file-name" id="fileNameShow" style="display:none;">
            <i class="bi bi-file-earmark-spreadsheet-fill" style="color:var(--teal)"></i>
            <span id="fileNameText"></span>
          </div>
          <input type="file" name="excel_file" id="fileInput" class="rcp-file-hidden" accept=".xlsx,.xls" title="">
        </label>

        <div class="upload-rules">
          <h6>📋 File Format Rules</h6>
          <ul>
            <li>Row 1–3: Company / Customer / Date headers → auto-skipped</li>
            <li>Row 4: Column headers → auto-skipped</li>
            <li>Row 5 onwards: Data rows → inserted</li>
            <li>Last "Grand Total" row → auto-skipped</li>
            <li>Denominations (₹2000 to ₹5) and Coins are read automatically</li>
          </ul>
        </div>

        <div class="upload-progress" id="uploadProgress">
          <div class="up-bar"><div class="up-fill"></div></div>
          <div class="up-label">Uploading and processing rows…</div>
        </div>

      </div>
      <div class="um-footer">
        <button type="button" class="btn-cancel" id="rcpUploadCancelBtn">Cancel</button>
        <button type="submit" class="btn-upload" id="uploadBtn">
          <i class="bi bi-cloud-upload-fill"></i> Upload &amp; Import
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     FILTER MODAL
══════════════════════════════════════════════════════════════════ --}}
<div class="filter-overlay" id="filterOverlay">
  <div class="filter-modal" id="filterModalInner">
    <div class="fm-header">
      <div class="fm-title"><i class="bi bi-sliders2"></i> Search &amp; Filter</div>
      <div class="fm-close" id="rcpFilterClose" role="button" tabindex="0"><i class="bi bi-x-lg"></i></div>
    </div>
    <form id="filterForm" action="#" onsubmit="return false;">
      <div class="fm-body">
        <div class="fm-row">
          <div class="fm-field">
            <label>Date From</label>
            <div class="date-wrap">
              <i class="bi bi-calendar3 cal-ico"></i>
              <input type="text" name="date_from" id="fDateFrom" class="fc fc-date"
                     placeholder="dd/mm/yyyy" autocomplete="off" readonly
                     value="{{ request('date_from') }}">
            </div>
          </div>
          <div class="fm-field">
            <label>Date To</label>
            <div class="date-wrap">
              <i class="bi bi-calendar3 cal-ico"></i>
              <input type="text" name="date_to" id="fDateTo" class="fc fc-date"
                     placeholder="dd/mm/yyyy" autocomplete="off" readonly
                     value="{{ request('date_to') }}">
            </div>
          </div>
        </div>
        <div class="fm-row">
          <div class="fm-field">
            <label>State</label>
            <select name="state" id="rcpStateSelect" class="fc">
              <option value="">All States</option>
              @foreach($states as $s)
                <option value="{{ $s }}" {{ request('state') == $s ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="fm-field">
            <label>Zone</label>
            <select name="zone_id" id="rcpZoneSelect" class="fc">
              <option value="">All Zones</option>
              @foreach($zones as $z)
                <option value="{{ $z->id }}" {{ (string) request('zone_id') === (string) $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="fm-field">
            <label>Branch</label>
            <select name="branch_id" id="rcpBranchSelect" class="fc">
              <option value="">All Branches</option>
              @foreach($branchesForFilter as $b)
                <option value="{{ $b->id }}" {{ (string) request('branch_id') === (string) $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="fm-field">
          <label>Universal Search</label>
          <input type="text" name="search" id="filterSearchInput" class="fc" placeholder="Slip no, location, state, amount…"
                 value="{{ request('search') }}">
        </div>
        <input type="hidden" name="per_page" id="filterFormPerPage" value="{{ request('per_page', 25) }}">
      </div>
      <div class="fm-footer">
        <button type="button" class="btn-reset-f" id="rcpFilterResetBtn">
          <i class="bi bi-arrow-counterclockwise"></i> Reset
        </button>
        <button type="button" class="btn-apply-f" id="rcpFilterApplyBtn">
          <i class="bi bi-search"></i> Apply Filters
        </button>
      </div>
    </form>
  </div>
</div>

{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
(function ($) {
    'use strict';

    var rcpRoutes = {
        data: @json(route('superadmin.radiantcash.data')),
        upload: @json(route('superadmin.radiantcash.upload')),
        index: @json(route('superadmin.radiantcash.index')),
        branchFetch: @json(route('superadmin.getbranchfetch'))
    };
    var rcpCsrf = $('meta[name="csrf-token"]').attr('content');
    var rcpState = { page: {{ (int) $records->currentPage() }} };
    var rcpSearchTimer = null;
    var rcpLoading = false;

    toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 3000 };

    flatpickr('#fDateFrom', { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', allowInput: false });
    flatpickr('#fDateTo', { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', allowInput: false });

    function fmtINR(n) {
        return '₹' + Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 });
    }

    function syncFilterSearchFromToolbar() {
        $('#filterSearchInput').val($('#searchInput').val());
    }

    function syncToolbarSearchFromFilter() {
        $('#searchInput').val($('#filterSearchInput').val());
    }

    function syncPerPageHidden() {
        $('#filterFormPerPage').val($('#rcpPerPage').val());
    }

    function getListParams(page) {
        syncPerPageHidden();
        return {
            page: page,
            per_page: $('#rcpPerPage').val(),
            date_from: $('#fDateFrom').val() || '',
            date_to: $('#fDateTo').val() || '',
            state: $('#rcpStateSelect').val() || '',
            zone_id: $('#rcpZoneSelect').val() || '',
            branch_id: $('#rcpBranchSelect').val() || '',
            search: $('#searchInput').val() || ''
        };
    }

    function loadBranchesForZone(zoneId, selectedBranchId) {
        var $br = $('#rcpBranchSelect');
        $br.html('<option value="">All Branches</option>');
        if (!zoneId) return;
        $.ajax({
            url: rcpRoutes.branchFetch,
            method: 'POST',
            data: { _token: rcpCsrf, id: String(zoneId) },
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function (res) {
            var list = res.branch || [];
            list.forEach(function (b) {
                var opt = $('<option></option>').attr('value', b.id).text(b.name);
                if (selectedBranchId && String(b.id) === String(selectedBranchId)) opt.prop('selected', true);
                $br.append(opt);
            });
        });
    }

    function renderChips() {
        var bar = $('#activeChips');
        var html = '';
        var count = 0;
        function addChip(label, text, dataKey) {
            if (!text) return;
            count++;
            html += '<span class="fchip">' + label + ': <strong>' + $('<div/>').text(text).html() + '</strong>' +
                '<span class="rx rcp-chip-remove" data-key="' + dataKey + '">×</span></span>';
        }
        addChip('From', $('#fDateFrom').val(), 'date_from');
        addChip('To', $('#fDateTo').val(), 'date_to');
        addChip('State', $('#rcpStateSelect').val() ? $('#rcpStateSelect option:selected').text() : '', 'state');
        if ($('#rcpZoneSelect').val()) {
            addChip('Zone', $('#rcpZoneSelect option:selected').text(), 'zone_id');
        }
        if ($('#rcpBranchSelect').val()) {
            addChip('Branch', $('#rcpBranchSelect option:selected').text(), 'branch_id');
        }
        addChip('Search', $('#searchInput').val(), 'search');
        if (count > 0) {
            html += '<span class="fchip rcp-clear-all" style="background:#fee2e2;color:#dc2626;border-color:#fca5a5;cursor:pointer">Clear All ×</span>';
            bar.html(html);
            $('#filterDot').addClass('show').text(count);
            $('#filterBtn').addClass('active');
        } else {
            bar.html('<span class="no-chip" id="noChip">No filters applied</span>');
            $('#filterDot').removeClass('show').text('');
            $('#filterBtn').removeClass('active');
        }
    }

    function rcpLoad(page) {
        if (rcpLoading) return;
        if (page == null || page === undefined) page = rcpState.page;
        rcpLoading = true;
        var params = getListParams(page);
        $.ajax({
            url: rcpRoutes.data,
            method: 'GET',
            data: params,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function (res) {
            if (!res || !res.success) {
                toastr.error('Failed to load data.');
                return;
            }
            $('#rcpTableBody').html(res.table_html);
            $('#rcpPaginationHost').html(res.pagination_html);
            $('#rcpStatAmount').text(fmtINR(res.stats.total_amount));
            $('#rcpStatRecords').text(Number(res.stats.total_records || 0).toLocaleString('en-IN'));
            $('#rcpStatBatches').text(res.stats.total_batches);
            $('#rcpStatLocations').text(res.stats.locations_count);
            var from = res.result.from != null ? res.result.from : 0;
            var to = res.result.to != null ? res.result.to : 0;
            var total = res.result.total != null ? res.result.total : 0;
            $('#rcpResFrom').text(from);
            $('#rcpResTo').text(to);
            $('#rcpResTotal').text(total);
            if (res.pagination_meta) {
                rcpState.page = res.pagination_meta.current_page;
            }
            renderChips();
        }).fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not load records.';
            toastr.error(msg);
        }).always(function () {
            rcpLoading = false;
        });
    }

    function openUploadModal() {
        $('#uploadOverlay').addClass('show');
        $('body').css('overflow', 'hidden');
    }

    function closeUploadModal() {
        $('#uploadOverlay').removeClass('show');
        $('body').css('overflow', '');
    }

    function resetUploadFormState() {
        $('#fileInput').val('');
        $('#fileNameShow').hide();
        $('#fileNameText').text('');
        $('#dropZone').css({ borderColor: '', background: '' });
        $('#uploadProgress').hide();
        var $btn = $('#uploadBtn');
        $btn.prop('disabled', false);
        $btn.html('<i class="bi bi-cloud-upload-fill"></i> Upload &amp; Import');
    }

    function onFileSelect($input) {
        var file = $input[0].files && $input[0].files[0];
        if (!file) return;
        $('#fileNameText').text(file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)');
        $('#fileNameShow').css('display', 'flex');
        $('#dropZone').css({ borderColor: 'var(--teal)', background: 'var(--teal-lt)' });
    }

    function clearFlatpickr(sel) {
        var el = document.querySelector(sel);
        if (el && el._flatpickr) el._flatpickr.clear();
    }

    function openFilterModal() {
        syncFilterSearchFromToolbar();
        $('#filterOverlay').addClass('show');
        $('body').css('overflow', 'hidden');
    }

    function closeFilterModal() {
        $('#filterOverlay').removeClass('show');
        $('body').css('overflow', '');
    }

    function resetAllFilters() {
        clearFlatpickr('#fDateFrom');
        clearFlatpickr('#fDateTo');
        $('#rcpStateSelect').val('');
        $('#rcpZoneSelect').val('');
        $('#rcpBranchSelect').html('<option value="">All Branches</option>');
        $('#filterSearchInput, #searchInput').val('');
        $('#rcpPerPage').val('25');
        $('#filterFormPerPage').val('25');
        rcpLoad(1);
    }

    function removeOneFilter(key) {
        if (key === 'date_from') clearFlatpickr('#fDateFrom');
        else if (key === 'date_to') clearFlatpickr('#fDateTo');
        else if (key === 'state') $('#rcpStateSelect').val('');
        else if (key === 'zone_id') {
            $('#rcpZoneSelect').val('');
            $('#rcpBranchSelect').html('<option value="">All Branches</option>');
        } else if (key === 'branch_id') $('#rcpBranchSelect').val('');
        else if (key === 'search') {
            $('#filterSearchInput, #searchInput').val('');
        }
        rcpLoad(1);
    }

    /* ── Upload modal ── */
    $('#rcpOpenUploadBtn').on('click.rcp', function () {
        resetUploadFormState();
        openUploadModal();
    });
    $('#rcpUploadClose, #rcpUploadCancelBtn').on('click.rcp', function () {
        closeUploadModal();
        resetUploadFormState();
    });
    $('#uploadOverlay').on('click.rcp', function (e) {
        if (e.target === this) {
            closeUploadModal();
            resetUploadFormState();
        }
    });
    $('#uploadModalInner').on('click.rcp', function (e) { e.stopPropagation(); });

    $('#fileInput').on('change.rcp', function () { onFileSelect($(this)); });

    var $dz = $('#dropZone');
    $dz.on('dragenter.rcp dragover.rcp', function (e) {
        e.preventDefault();
        $dz.addClass('drag-over');
    });
    $dz.on('dragleave.rcp drop.rcp', function (e) {
        e.preventDefault();
        $dz.removeClass('drag-over');
    });
    $dz.on('drop.rcp', function (e) {
        var files = e.originalEvent.dataTransfer.files;
        if (!files || !files.length) return;
        var dt = new DataTransfer();
        dt.items.add(files[0]);
        document.getElementById('fileInput').files = dt.files;
        onFileSelect($('#fileInput'));
    });

    $('#uploadForm').on('submit.rcp', function (e) {
        e.preventDefault();
        var fi = document.getElementById('fileInput');
        if (!fi.files.length) {
            toastr.warning('Please select an Excel file first.');
            return;
        }
        $('#uploadProgress').show();
        $('#uploadBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing…');
        var fd = new FormData(this);
        $.ajax({
            url: rcpRoutes.upload,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': rcpCsrf
            }
        }).done(function (res) {
            if (res && res.success) {
                toastr.success(res.message || 'Upload complete.');
                closeUploadModal();
                resetUploadFormState();
                rcpLoad(1);
            } else {
                toastr.error((res && res.message) ? res.message : 'Upload failed.');
            }
        }).fail(function (xhr) {
            var json = xhr.responseJSON;
            if (json && json.message) toastr.error(json.message);
            else if (json && json.errors && json.errors.excel_file) toastr.error(json.errors.excel_file[0]);
            else toastr.error('Upload failed.');
        }).always(function () {
            $('#uploadProgress').hide();
            $('#uploadBtn').prop('disabled', false).html('<i class="bi bi-cloud-upload-fill"></i> Upload &amp; Import');
        });
    });

    /* ── Filter modal ── */
    $('#filterBtn').on('click.rcp', function () { openFilterModal(); });
    $('#rcpFilterClose').on('click.rcp', function () { closeFilterModal(); });
    $('#filterOverlay').on('click.rcp', function (e) {
        if (e.target === this) closeFilterModal();
    });
    $('#filterModalInner').on('click.rcp', function (e) { e.stopPropagation(); });

    $('#rcpFilterApplyBtn').on('click.rcp', function () {
        syncToolbarSearchFromFilter();
        closeFilterModal();
        rcpLoad(1);
    });

    $('#rcpFilterResetBtn').on('click.rcp', function () {
        resetAllFilters();
        closeFilterModal();
    });

    $(document).on('click.rcp', '.rcp-chip-remove', function () {
        removeOneFilter($(this).data('key'));
    });
    $(document).on('click.rcp', '.rcp-clear-all', function () {
        resetAllFilters();
    });

    $('#rcpPerPage').on('change.rcp', function () {
        syncPerPageHidden();
        rcpLoad(1);
    });

    $('#searchInput').on('input.rcp', function () {
        clearTimeout(rcpSearchTimer);
        var v = $(this).val();
        rcpSearchTimer = setTimeout(function () {
            $('#filterSearchInput').val(v);
            rcpLoad(1);
        }, 500);
    });

    $(document).on('click.rcp', '#rcpPaginationHost a', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        if (!href || href === '#') return;
        var url = new URL(href, window.location.origin);
        var p = parseInt(url.searchParams.get('page') || '1', 10);
        rcpLoad(p);
    });

    $('#rcpRefreshBtn').on('click.rcp', function () {
        rcpLoad(rcpState.page);
    });

    $(document).on('keydown.rcp', function (e) {
        if (e.key === 'Escape') {
            closeUploadModal();
            resetUploadFormState();
            closeFilterModal();
        }
    });

    $('#rcpZoneSelect').on('change.rcp', function () {
        var zid = $(this).val();
        $('#rcpBranchSelect').val('');
        loadBranchesForZone(zid, '');
    });

    renderChips();
})(window.jQuery);
</script>

@include('superadmin.superadminfooter')
</body>
</html>