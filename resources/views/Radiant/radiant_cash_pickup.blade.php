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
.sc-emerald::after{background:linear-gradient(90deg,#059669,#34d399);}
.sc-emerald .stat-icon{background:#d1fae5;color:#047857;}
.rcp-stat-interactive{cursor:pointer;}
.rcp-stat-interactive:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg);}
.rcp-stat-interactive:focus-visible{outline:2px solid var(--amber);outline-offset:2px;}
.rcp-stat-active.stat-card{transform:translateY(-1px);box-shadow:0 0 0 3px rgba(245,158,11,.35),var(--shadow-lg);}
.stat-card.sc-violet.rcp-stat-active{box-shadow:0 0 0 3px rgba(139,92,246,.42),var(--shadow-lg);}
.stat-card.sc-emerald.rcp-stat-active{box-shadow:0 0 0 3px rgba(5,150,105,.38),var(--shadow-lg);}
.stat-card.sc-rose.rcp-stat-active{box-shadow:0 0 0 3px rgba(244,63,94,.38),var(--shadow-lg);}
@keyframes rcpFadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.stat-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text3);margin-bottom:3px;}
.stat-value{font-size:1.35rem;font-weight:800;color:var(--text);font-family:var(--mono);letter-spacing:-.5px;}
.stat-sub{font-size:.72rem;color:var(--text3);margin-top:2px;}

/* ── UPLOAD MODAL OVERLAY ── */
.upload-overlay{
    position:fixed;inset:0;background:rgba(15,23,42,.65);
    backdrop-filter:blur(6px);z-index:10050; /* above .pc-sidebar (1026) & .pc-header (1025) */
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
    backdrop-filter:blur(4px);z-index:10040;
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

/* ════════════════════════════════════════════
   COMPARISON MODAL
════════════════════════════════════════════ */
.cmp-overlay{
    position:fixed;inset:0;background:rgba(15,23,42,.7);
    backdrop-filter:blur(7px);z-index:10055;
    display:none;align-items:center;justify-content:center;
    animation:fadeIn .2s ease;
}
.cmp-overlay.show{display:flex;}
.cmp-modal{
    background:#fff;border-radius:22px;
    width:100%;max-width:1100px;
    max-height:92vh;overflow:hidden;
    display:flex;flex-direction:column;
    box-shadow:0 32px 80px rgba(0,0,0,.28);
    animation:slideUp .28s cubic-bezier(.22,1,.36,1);
}
.cmp-header{
    background:linear-gradient(135deg,#1e293b,#0f172a);
    padding:20px 26px;flex-shrink:0;
    display:flex;align-items:center;justify-content:space-between;gap:12px;
}
.cmp-header-left{display:flex;align-items:center;gap:14px;}
.cmp-hdr-icon{
    width:44px;height:44px;
    background:rgba(245,158,11,.18);border:1px solid rgba(245,158,11,.35);
    border-radius:13px;display:flex;align-items:center;justify-content:center;
    font-size:1.3rem;
}
.cmp-hdr-title{font-size:1rem;font-weight:800;color:#fff;margin:0;}
.cmp-hdr-sub{font-size:.72rem;color:rgba(255,255,255,.5);margin-top:2px;}
.cmp-hdr-location{
    display:inline-flex;align-items:center;gap:6px;
    background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);
    border-radius:20px;padding:4px 12px;font-size:.78rem;font-weight:700;
    color:var(--amber);margin-top:4px;
}
.cmp-close-btn{
    width:34px;height:34px;background:rgba(255,255,255,.1);
    border:1px solid rgba(255,255,255,.18);border-radius:9px;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:background .14s;color:rgba(255,255,255,.7);
    font-size:.9rem;
}
.cmp-close-btn:hover{background:rgba(255,255,255,.22);}
.cmp-match-bar{
    flex-shrink:0;padding:12px 26px;
    display:flex;align-items:center;gap:10px;flex-wrap:wrap;
    border-bottom:1px solid #e2e8f0;background:#fafafa;
}
.cmp-match-pill{
    display:inline-flex;align-items:center;gap:6px;
    padding:5px 13px;border-radius:20px;font-size:.73rem;font-weight:700;
}
.cmp-pill-match  {background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;}
.cmp-pill-close  {background:#fef3c7;color:#92400e;border:1px solid #fcd34d;}
.cmp-pill-mismatch{background:#ffe4e6;color:#9f1239;border:1px solid #fda4af;}
.cmp-pill-nodata {background:#f1f5f9;color:#64748b;border:1px solid #cbd5e1;}
.cmp-body{
    flex:1;overflow-y:auto;padding:22px 26px;
    display:grid;grid-template-columns:repeat(3,1fr);gap:16px;
}
.cmp-body::-webkit-scrollbar{width:5px;}
.cmp-body::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:4px;}
.cmp-panel{
    border-radius:14px;border:1.5px solid #e2e8f0;
    overflow:hidden;display:flex;flex-direction:column;
    max-height:500px;
}
.cmp-panel-hdr{
    padding:14px 16px;
    display:flex;align-items:center;gap:10px;
    flex-shrink:0;
}
.cmp-ph-icon{
    width:36px;height:36px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;font-size:1rem;
}
.cph-amber {background:#fef3c7;color:#d97706;}
.cph-blue  {background:#dbeafe;color:#1d4ed8;}
.cph-green {background:#d1fae5;color:#059669;}
.cmp-panel-hdr h6{font-size:.8rem;font-weight:800;color:#0f172a;margin:0;}
.cmp-panel-hdr small{font-size:.67rem;color:#94a3b8;font-weight:600;margin-top:1px;}
.cmp-panel-body{
    flex:1;padding:14px 16px;
    overflow-y:auto;
    scrollbar-width:thin;scrollbar-color:#e2e8f0 transparent;
}
.cmp-panel-body::-webkit-scrollbar{width:4px;}
.cmp-panel-body::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:4px;}
.cmp-bfr-total{
    display:flex;align-items:center;justify-content:space-between;
    background:linear-gradient(135deg,#1d4ed8,#3b82f6);
    border-radius:10px;padding:10px 14px;margin-bottom:12px;color:#fff;
}
.cmp-bfr-total-label{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;opacity:.75;}
.cmp-bfr-total-amt{font-size:1.1rem;font-weight:800;font-family:'JetBrains Mono',monospace;}
.cmp-bfr-window{
    font-size:.67rem;font-weight:700;padding:4px 10px;border-radius:6px;
    background:rgba(255,255,255,.18);color:#fff;
}
.cmp-amt{
    font-size:1.5rem;font-weight:800;
    font-family:'JetBrains Mono',monospace;
    letter-spacing:-.5px;margin-bottom:4px;
}
.cmp-amt-sub{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;margin-bottom:14px;}
.cmp-kv{display:grid;grid-template-columns:auto 1fr;gap:4px 10px;margin-top:8px;}
.cmp-k{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;white-space:nowrap;}
.cmp-v{font-size:.77rem;font-weight:600;color:#334155;word-break:break-word;}
.cmp-no-data{
    display:flex;flex-direction:column;align-items:center;
    justify-content:center;padding:32px 16px;text-align:center;
}
.cmp-no-data i{font-size:2rem;color:#cbd5e1;margin-bottom:8px;}
.cmp-no-data p{font-size:.75rem;color:#94a3b8;margin:0;}
.cmp-bank-row{
    padding:10px 12px;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:8px;
    background:#fafafa;transition:background .12s;
}
.cmp-bank-row:hover{background:#f0f9ff;}
.cmp-bank-desc{font-size:.73rem;font-weight:600;color:#334155;margin-bottom:4px;line-height:1.35;}
.cmp-bank-amt{font-size:.9rem;font-weight:800;color:#059669;font-family:'JetBrains Mono',monospace;}
.cmp-bank-meta{font-size:.66rem;color:#94a3b8;margin-top:3px;}
.cmp-spinner{
    display:flex;flex-direction:column;align-items:center;
    justify-content:center;min-height:200px;gap:12px;
}
.cmp-spinner-ring{
    width:40px;height:40px;border:3px solid #e2e8f0;
    border-top-color:var(--amber);border-radius:50%;
    animation:spin .8s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}
.cmp-branch-match-badge{
    display:inline-flex;align-items:center;gap:5px;
    padding:4px 10px;border-radius:8px;font-size:.68rem;font-weight:700;margin-bottom:10px;
}
.cbmb-ok {background:#d1fae5;color:#065f46;}
.cbmb-no {background:#ffe4e6;color:#9f1239;}
@media(max-width:900px){
    .cmp-body{grid-template-columns:1fr;}
}

/* ════════════════════════════════════════════
   MISMATCH ALERT MODAL
════════════════════════════════════════════ */
.alert-overlay{
    position:fixed;inset:0;background:rgba(15,23,42,.7);
    backdrop-filter:blur(7px);z-index:10060;
    display:none;align-items:center;justify-content:center;
    animation:fadeIn .2s ease;
}
.alert-overlay.show{display:flex;}
.alert-modal{
    background:#fff;border-radius:22px;
    width:100%;max-width:520px;
    box-shadow:0 32px 80px rgba(0,0,0,.28);
    animation:slideUp .28s cubic-bezier(.22,1,.36,1);
    overflow:hidden;
}
.alm-header{
    background:linear-gradient(135deg,#0f172a,#1e3a5f);
    padding:22px 26px;
    display:flex;align-items:center;justify-content:space-between;gap:12px;
}
.alm-header-left{display:flex;align-items:center;gap:14px;}
.alm-hdr-icon{
    width:44px;height:44px;
    background:rgba(244,63,94,.2);border:1px solid rgba(244,63,94,.4);
    border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;
}
.alm-hdr-title{font-size:1rem;font-weight:800;color:#fff;margin:0;}
.alm-hdr-sub{font-size:.72rem;color:rgba(255,255,255,.5);margin-top:2px;}
.alm-close-btn{
    width:34px;height:34px;background:rgba(255,255,255,.1);
    border:1px solid rgba(255,255,255,.18);border-radius:9px;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:background .14s;color:rgba(255,255,255,.7);font-size:.9rem;
}
.alm-close-btn:hover{background:rgba(255,255,255,.22);}
.alm-body{padding:28px 26px;}
.alm-field label{
    display:block;font-size:.68rem;font-weight:800;text-transform:uppercase;
    letter-spacing:.7px;color:var(--text3);margin-bottom:6px;
}
.alm-date-wrap{position:relative;}
.alm-date-ico{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--rose);font-size:.95rem;pointer-events:none;}
.alm-date-input{
    padding: 10px 50px !important;
    width:100%;padding:11px 14px 11px 38px;
    border:1.5px solid var(--border);border-radius:var(--radius-sm);
    font-size:.9rem;font-family:var(--font);color:var(--text);background:var(--surface2);
    transition:border-color .15s,box-shadow .15s;
}
.alm-date-input:focus{outline:none;border-color:var(--rose);box-shadow:0 0 0 3px rgba(244,63,94,.12);}
.alm-info{
    margin-top:16px;background:#fff5f5;border:1px solid #fda4af;border-radius:10px;
    padding:12px 14px;display:flex;gap:8px;align-items:flex-start;
}
.alm-info i{color:var(--rose);font-size:.95rem;flex-shrink:0;margin-top:1px;}
.alm-info p{font-size:.75rem;color:#9f1239;line-height:1.55;margin:0;}
.alm-footer{padding:16px 26px 22px;display:flex;gap:10px;}
.btn-send-alert{
    flex:1;padding:12px;border-radius:10px;border:none;
    background:linear-gradient(135deg,var(--rose),#e11d48);
    color:#fff;font-family:var(--font);font-size:.87rem;font-weight:800;
    cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:8px;
    box-shadow:0 4px 14px rgba(244,63,94,.35);
}
.btn-send-alert:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(244,63,94,.45);}
.btn-send-alert:disabled{opacity:.6;cursor:not-allowed;transform:none;}
.btn-alm-cancel{
    padding:12px 20px;border-radius:10px;border:1.5px solid var(--border2);
    background:var(--surface);color:var(--text2);
    font-family:var(--font);font-size:.82rem;font-weight:700;cursor:pointer;transition:all .14s;
}
.btn-alm-cancel:hover{border-color:var(--rose);color:var(--rose);}
/* Result badge in modal */
.alm-result{
    margin-top:14px;padding:14px 16px;border-radius:10px;
    font-size:.82rem;font-weight:700;display:none;
}
.alm-result.success{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;}
.alm-result.warning{background:#fef3c7;color:#92400e;border:1px solid #fcd34d;}
.alm-result.error{background:#ffe4e6;color:#9f1239;border:1px solid #fda4af;}

/* ── Batch uploads (shown when Upload Batches stat is clicked) ── */
.rcp-batch-section{
    display:none;
    background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);
    box-shadow:var(--shadow);margin-bottom:22px;overflow:hidden;
}
.rcp-batch-section.rcp-batch-section--open{
    display:block;
    animation:rcpFadeIn .28s ease;
}
.rcp-batch-head{
    display:flex;align-items:flex-start;justify-content:space-between;gap:12px;
    padding:18px 22px;border-bottom:1px solid var(--border);
    background:linear-gradient(180deg,#fff,#f8fafc);
}
.rcp-batch-head h2{font-size:.95rem;font-weight:800;margin:0;color:var(--navy);letter-spacing:-.25px;}
.rcp-batch-head small{font-size:.74rem;color:var(--text2);display:block;margin-top:6px;line-height:1.45;max-width:52rem;}
.rcp-batch-actions{display:flex;flex-wrap:wrap;gap:8px;align-items:center;}
.btn-batch-ghost{
    padding:7px 14px;border-radius:9px;border:1px solid var(--border2);background:var(--surface);
    color:var(--text2);font-size:.76rem;font-weight:700;cursor:pointer;font-family:var(--font);
    display:inline-flex;align-items:center;gap:6px;transition:all .14s;
}
.btn-batch-ghost:hover{border-color:var(--navy3);color:var(--navy);}
.rcp-batch-scroll{max-height:min(52vh,420px);overflow:auto;}
.rcp-batch-table{width:100%;border-collapse:collapse;font-size:.8rem;}
.rcp-batch-table th{
    text-align:left;padding:12px 18px;font-size:.64rem;font-weight:800;text-transform:uppercase;
    letter-spacing:.55px;color:var(--text3);background:#f1f5f9;border-bottom:1px solid var(--border);
    position:sticky;top:0;z-index:1;
}
.rcp-batch-table td{padding:14px 18px;border-bottom:1px solid var(--border);vertical-align:middle;}
.rcp-batch-table tr:last-child td{border-bottom:none;}
.rcp-batch-table tbody tr:hover{background:#fafafa;}
.rcp-batch-file{font-weight:700;color:var(--text);max-width:min(320px,42vw);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.rcp-batch-meta{color:var(--text2);font-size:.76rem;}
.rcp-batch-id{font-family:var(--mono);font-size:.68rem;color:var(--text3);margin-top:4px;word-break:break-all;}
.rcp-batch-rows{text-align:center;font-weight:700;font-variant-numeric:tabular-nums;}
.btn-batch-del{
    padding:7px 14px;border-radius:9px;border:1px solid #fecaca;background:linear-gradient(180deg,#fff,#fff1f2);
    color:#be123c;font-size:.74rem;font-weight:800;cursor:pointer;font-family:var(--font);
    transition:all .14s;white-space:nowrap;
}
.btn-batch-del:hover{background:#ffe4e6;border-color:#f87171;box-shadow:0 2px 8px rgba(244,63,94,.15);}

/* ── Reconciliation modal (tabbed, soft dashboard) ── */
.rcp-mm-overlay{
    position:fixed;inset:0;background:rgba(15,23,42,.52);
    backdrop-filter:blur(8px);z-index:10050;display:none;align-items:center;justify-content:center;padding:16px;
}
.rcp-mm-overlay.show{display:flex;}
.rcp-mm-modal{
    background:#fff;border-radius:22px;width:100%;max-width:960px;max-height:92vh;
    overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 90px rgba(15,23,42,.2);
    border:1px solid var(--border);
    animation:slideUp .28s cubic-bezier(.22,1,.36,1);
}
.rcp-mm-header{
    padding:20px 24px;background:linear-gradient(135deg,#0f172a 0%,#1e293b 55%,#0f172a 100%);
    display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-shrink:0;
}
.rcp-mm-title{color:#fff;font-size:1.05rem;font-weight:800;margin:0;letter-spacing:-.3px;}
.rcp-mm-sub{color:rgba(255,255,255,.58);font-size:.78rem;margin:8px 0 0;line-height:1.5;max-width:46rem;}
.rcp-mm-close{width:38px;height:38px;border-radius:11px;border:1px solid rgba(255,255,255,.22);
    background:rgba(255,255,255,.08);color:rgba(255,255,255,.9);cursor:pointer;display:flex;
    align-items:center;justify-content:center;transition:background .15s;flex-shrink:0;}
.rcp-mm-close:hover{background:rgba(255,255,255,.16);}
.rcp-mm-body{padding:0 0 16px;overflow:hidden;display:flex;flex-direction:column;flex:1;min-height:0;}
.rcp-mm-summary{
    display:flex;flex-wrap:wrap;gap:10px;padding:14px 22px 0;
}
.rcp-mm-chip{
    display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:999px;
    font-size:.72rem;font-weight:700;border:1px solid var(--border);background:var(--surface2);color:var(--text2);
}
.rcp-mm-chip strong{font-family:var(--mono);color:var(--text);font-size:.8rem;}
.rcp-mm-chip.em{background:#ecfdf5;border-color:#a7f3d0;color:#065f46;}
.rcp-mm-chip.em strong{color:#047857;}
.rcp-mm-chip.un{background:#fff1f2;border-color:#fecdd3;color:#9f1239;}
.rcp-mm-chip.un strong{color:#be123c;}
.rcp-mm-tabs{
    display:flex;gap:6px;padding:12px 22px 0;border-bottom:1px solid var(--border);
}
.rcp-mm-tab{
    flex:1;max-width:240px;padding:11px 16px;border-radius:12px 12px 0 0;border:1px solid transparent;
    border-bottom:none;background:transparent;font-family:var(--font);font-size:.78rem;font-weight:800;
    color:var(--text3);cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:8px;
}
.rcp-mm-tab:hover{background:var(--surface2);color:var(--text);}
.rcp-mm-tab.is-active{
    background:var(--surface);color:var(--navy);border-color:var(--border);border-bottom-color:var(--surface);
    margin-bottom:-1px;box-shadow:0 -2px 12px rgba(15,23,42,.06);
}
.rcp-mm-tab .rcp-mm-tab-badge{
    font-family:var(--mono);font-size:.72rem;padding:2px 8px;border-radius:999px;background:var(--surface2);
    color:var(--text2);font-weight:800;
}
.rcp-mm-tab.is-active .rcp-mm-tab-badge{background:#e2e8f0;color:var(--navy);}
.rcp-mm-tab.tab-match.is-active{color:#047857;}
.rcp-mm-tab.tab-match.is-active .rcp-mm-tab-badge{background:#d1fae5;color:#065f46;}
.rcp-mm-tab.tab-mismatch.is-active{color:#be123c;}
.rcp-mm-tab.tab-mismatch.is-active .rcp-mm-tab-badge{background:#ffe4e6;color:#9f1239;}
.rcp-mm-tab-stack{flex:1;min-height:0;padding:0 22px;overflow:hidden;display:flex;flex-direction:column;}
.rcp-mm-tab-panel{display:none;flex:1;flex-direction:column;min-height:0;padding-top:12px;}
.rcp-mm-tab-panel.is-active{display:flex;}
.rcp-mm-table-wrap{
    flex:1;min-height:220px;max-height:min(50vh,400px);overflow:auto;border:1px solid var(--border);
    border-radius:14px;background:#fafbfc;
}
.rcp-mm-table{width:100%;border-collapse:separate;border-spacing:0;font-size:.78rem;}
.rcp-mm-table th{
    position:sticky;top:0;background:#f1f5f9;padding:10px 12px;text-align:left;
    font-size:.62rem;font-weight:800;text-transform:uppercase;letter-spacing:.55px;color:var(--text3);
    border-bottom:1px solid var(--border);z-index:1;
}
.rcp-mm-table td{padding:10px 12px;border-bottom:1px solid #eef2f7;vertical-align:middle;background:#fff;}
.rcp-mm-table tbody tr:hover td{background:#f8fafc;}
.rcp-mm-table tr:last-child td{border-bottom:none;}
.rcp-mm-pill{font-size:.62rem;font-weight:800;padding:3px 8px;border-radius:6px;display:inline-block;}
.rcp-mm-pill.match{background:#d1fae5;color:#047857;}
.rcp-mm-pill.mismatch{background:#ffe4e6;color:#be123c;}
.rcp-mm-pill.close{background:#fef3c7;color:#b45309;}
.rcp-mm-pill.nodata{background:#f1f5f9;color:#64748b;}
.rcp-mm-amt{font-family:var(--mono);font-weight:700;color:var(--navy);}
.rcp-mm-empty{
    display:none;flex-direction:column;align-items:center;justify-content:center;text-align:center;
    padding:36px 24px;margin-top:10px;border-radius:14px;border:1px dashed var(--border2);background:var(--surface2);
}
.rcp-mm-empty.is-visible{display:flex;}
.rcp-mm-empty-icon{
    width:52px;height:52px;border-radius:16px;display:flex;align-items:center;justify-content:center;
    font-size:1.4rem;margin-bottom:12px;
}
.rcp-mm-empty.match .rcp-mm-empty-icon{background:#d1fae5;color:#047857;}
.rcp-mm-empty.mismatch .rcp-mm-empty-icon{background:#ffe4e6;color:#be123c;}
.rcp-mm-empty strong{font-size:.88rem;color:var(--text);margin-bottom:6px;}
.rcp-mm-empty p{font-size:.76rem;color:var(--text2);margin:0;max-width:28rem;line-height:1.5;}
.rcp-mm-foot{
    margin:12px 22px 0;padding:12px 16px;font-size:.72rem;color:var(--text2);
    background:var(--surface2);border-radius:12px;border:1px solid var(--border);line-height:1.45;
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
      <button type="button" class="hbtn hbtn-outline" id="rcpAlertBtn" title="Send mismatch alert email for a date">
        <i class="bi bi-envelope-exclamation-fill"></i> Mismatch Alert
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
  <div class="stat-card sc-violet rcp-stat-interactive" id="rcpStatBatchesCard" role="button" tabindex="0" title="Show or hide uploaded files for this filter">
    <div class="stat-icon"><i class="bi bi-collection-fill"></i></div>
    <div class="stat-label">Upload Batches</div>
    <div class="stat-value" id="rcpStatBatches">{{ $totalBatches }}</div>
    <div class="stat-sub">Distinct files · click to show list</div>
  </div>
  <div class="stat-card sc-emerald rcp-stat-interactive" id="rcpStatMatchCard" role="button" tabindex="0" title="View matched rows (sample)">
    <div class="stat-icon"><i class="bi bi-patch-check-fill"></i></div>
    <div class="stat-label">Match count</div>
    <div class="stat-value" id="rcpStatMatch">—</div>
    <div class="stat-sub">BFR + bank vs pickup · click for detail</div>
  </div>
  <div class="stat-card sc-rose rcp-stat-interactive" id="rcpStatMismatchCard" role="button" tabindex="0" title="View mismatched rows (sample)">
    <div class="stat-icon"><i class="bi bi-exclamation-octagon-fill"></i></div>
    <div class="stat-label">Mismatch count</div>
    <div class="stat-value" id="rcpStatMismatch">—</div>
    <div class="stat-sub">Missing data or amount variance · click</div>
  </div>
</div>

{{-- ── BATCH UPLOADS: toggled from “Upload Batches” stat card ─────────── --}}
<div id="rcpBatchSection" class="rcp-batch-section">
  <div class="rcp-batch-head">
    <div>
      <h2><i class="bi bi-folder2-open me-1"></i> Batch uploads</h2>
      <small>Each row is one uploaded file. Delete removes only that file’s pickup rows and clears bank links to those rows.</small>
    </div>
    <div class="rcp-batch-actions">
      <button type="button" class="btn-batch-ghost" id="rcpBatchesRefresh">
        <i class="bi bi-arrow-clockwise"></i> Refresh list
      </button>
      <button type="button" class="btn-batch-ghost" id="rcpBatchHide">
        <i class="bi bi-chevron-up"></i> Hide
      </button>
    </div>
  </div>
  <div class="rcp-batch-scroll">
    <table class="rcp-batch-table">
      <thead>
        <tr>
          <th>File</th>
          <th style="text-align:center;">Rows</th>
          <th>Uploaded</th>
          <th style="text-align:right;">Action</th>
        </tr>
      </thead>
      <tbody id="rcpBatchesBody">
        <tr><td colspan="4" class="text-center py-4 text-muted" style="font-size:.8rem;">Click <strong>Upload Batches</strong> in the stats row above to load this list.</td></tr>
      </tbody>
    </table>
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

{{-- ══════════════════════════════════════════════════════════════════
     MISMATCH ALERT MODAL
══════════════════════════════════════════════════════════════════ --}}
<div class="alert-overlay" id="alertOverlay">
  <div class="alert-modal" id="alertModalInner">
    <div class="alm-header">
      <div class="alm-header-left">
        <div class="alm-hdr-icon">⚠️</div>
        <div>
          <div class="alm-hdr-title">Send Mismatch Alert</div>
          <div class="alm-hdr-sub">Compare &amp; email mismatches for a pickup date</div>
        </div>
      </div>
      <div class="alm-close-btn" id="almCloseBtn"><i class="bi bi-x-lg"></i></div>
    </div>

    <div class="alm-body">
      <div class="alm-field">
        <label><i class="bi bi-calendar3"></i> &nbsp;Pickup Date to Check</label>
        <div class="alm-date-wrap">
          <i class="bi bi-calendar-event alm-date-ico"></i>
          <input type="text" id="almDateInput" class="alm-date-input" 
                 placeholder="Select date…" autocomplete="off" readonly>
        </div>
      </div>

      <div class="alm-info">
        <i class="bi bi-info-circle-fill"></i>
        <p>
          The system will compare <strong>every Radiant Cash Pickup</strong> for the selected date
          against the <strong>Branch Financial Report</strong> and <strong>Bank Statement</strong>.
          If any mismatch or missing data is found, a detailed alert email is sent to all
          configured recipients in <strong>Email Master</strong>.
        </p>
      </div>

      <div class="alm-result" id="almResult"></div>
    </div>

    <div class="alm-footer">
      <button type="button" class="btn-alm-cancel" id="almCancelBtn">Cancel</button>
      <button type="button" class="btn-send-alert" id="almSendBtn">
        <i class="bi bi-envelope-exclamation-fill"></i> Run Check &amp; Send Alert
      </button>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     RECONCILIATION MODAL (tabs + full-width table)
══════════════════════════════════════════════════════════════════ --}}
<div class="rcp-mm-overlay" id="rcpMmOverlay" aria-hidden="true">
  <div class="rcp-mm-modal" id="rcpMmModalInner" role="dialog" aria-modal="true" aria-labelledby="rcpMmTitle">
    <div class="rcp-mm-header">
      <div>
        <h2 class="rcp-mm-title" id="rcpMmTitle">Reconciliation overview</h2>
        <div class="rcp-mm-sub">Pickup amounts compared to <strong>Branch Financial Report</strong> and <strong>Bank statement</strong> using the same rules as the row “Compare” action. Each side shows a status: <span style="opacity:.72">match · close · mismatch · no_data</span>.</div>
      </div>
      <button type="button" class="rcp-mm-close" id="rcpMmClose" aria-label="Close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="rcp-mm-body">
      <div class="rcp-mm-summary">
        <span class="rcp-mm-chip em"><i class="bi bi-patch-check-fill"></i> Fully matched <strong id="rcpMmChipMatch">0</strong></span>
        <span class="rcp-mm-chip un"><i class="bi bi-exclamation-octagon-fill"></i> Needs attention <strong id="rcpMmChipMismatch">0</strong></span>
      </div>
      <div class="rcp-mm-tabs" role="tablist">
        <button type="button" class="rcp-mm-tab tab-match is-active" id="rcpMmTabMatched" data-rcp-mm-tab="matched" role="tab" aria-selected="true">
          <i class="bi bi-patch-check-fill"></i> Matched <span class="rcp-mm-tab-badge" id="rcpMmTabBadgeMatch">0</span>
        </button>
        <button type="button" class="rcp-mm-tab tab-mismatch" id="rcpMmTabMismatch" data-rcp-mm-tab="mismatch" role="tab" aria-selected="false">
          <i class="bi bi-exclamation-triangle-fill"></i> Unmatched <span class="rcp-mm-tab-badge" id="rcpMmTabBadgeMismatch">0</span>
        </button>
      </div>
      <div class="rcp-mm-tab-stack">
        <div class="rcp-mm-tab-panel is-active" id="rcpMmTabPanelMatched" role="tabpanel">
          <div class="rcp-mm-table-wrap">
            <table class="rcp-mm-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date</th>
                  <th>Location</th>
                  <th>Slip</th>
                  <th>Pickup ₹</th>
                  <th>BFR</th>
                  <th>Bank</th>
                </tr>
              </thead>
              <tbody id="rcpMmBodyMatch"></tbody>
            </table>
          </div>
          <div class="rcp-mm-empty match" id="rcpMmEmptyMatched">
            <div class="rcp-mm-empty-icon"><i class="bi bi-inbox"></i></div>
            <strong>No matched rows in this filter</strong>
            <p>Either there is no pickup data, or branch report / bank lines are missing or outside tolerance for every row. Try other dates or zones, or use row Compare to inspect a location.</p>
          </div>
        </div>
        <div class="rcp-mm-tab-panel" id="rcpMmTabPanelMismatch" role="tabpanel">
          <div class="rcp-mm-table-wrap">
            <table class="rcp-mm-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date</th>
                  <th>Location</th>
                  <th>Slip</th>
                  <th>Pickup ₹</th>
                  <th>BFR</th>
                  <th>Bank</th>
                </tr>
              </thead>
              <tbody id="rcpMmBodyMismatch"></tbody>
            </table>
          </div>
          <div class="rcp-mm-empty mismatch" id="rcpMmEmptyMismatch">
            <div class="rcp-mm-empty-icon"><i class="bi bi-emoji-smile"></i></div>
            <strong>Nothing to fix here</strong>
            <p>Every pickup in the current filter lines up with branch report and bank within the rules above.</p>
          </div>
        </div>
      </div>
      <div class="rcp-mm-foot" id="rcpMmFootNote"></div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     COMPARISON MODAL
══════════════════════════════════════════════════════════════════ --}}
<div class="cmp-overlay" id="cmpOverlay">
  <div class="cmp-modal" id="cmpModal">

    {{-- Header --}}
    <div class="cmp-header">
      <div class="cmp-header-left">
        <div class="cmp-hdr-icon">⚖️</div>
        <div>
          <div class="cmp-hdr-title">3-Way Cash Comparison</div>
          <div class="cmp-hdr-sub">Radiant Pickup vs Branch Financial Report vs Bank Statement</div>
          <div class="cmp-hdr-location" id="cmpLocationBadge">
            <i class="bi bi-geo-alt-fill"></i> <span id="cmpLocationText">—</span>
          </div>
        </div>
      </div>
      <div class="cmp-close-btn" id="cmpCloseBtn" role="button" tabindex="0">
        <i class="bi bi-x-lg"></i>
      </div>
    </div>

    {{-- Match status bar --}}
    <div class="cmp-match-bar" id="cmpMatchBar">
      <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#64748b;">Match Status:</span>
      <span class="cmp-match-pill cmp-pill-nodata" id="cmpPillRCP">
        <i class="bi bi-cash-stack"></i> RCP: —
      </span>
      <span class="cmp-match-pill cmp-pill-nodata" id="cmpPillBFR">
        <i class="bi bi-file-bar-graph"></i> Branch Report: —
      </span>
      <span class="cmp-match-pill cmp-pill-nodata" id="cmpPillBank">
        <i class="bi bi-bank2"></i> Bank Statement: —
      </span>
    </div>

    {{-- 3-panel body --}}
    <div class="cmp-body" id="cmpBody">
      {{-- Loading spinner shown initially --}}
      <div class="cmp-spinner" id="cmpSpinner" style="grid-column:1/-1;">
        <div class="cmp-spinner-ring"></div>
        <div style="font-size:.8rem;color:#94a3b8;font-weight:600;">Loading comparison data…</div>
      </div>

      {{-- Panel 1: Radiant Cash Pickup --}}
      <div class="cmp-panel" id="cmpPanelRCP" style="display:none;">
        <div class="cmp-panel-hdr" style="background:#fffbeb;border-bottom:1px solid #fde68a;">
          <div class="cmp-ph-icon cph-amber"><i class="bi bi-cash-stack"></i></div>
          <div>
            <h6>Radiant Cash Pickup</h6>
            <small id="cmpRCPDate"></small>
          </div>
        </div>
        <div class="cmp-panel-body">
          <div class="cmp-amt" id="cmpRCPAmt" style="color:#d97706;">—</div>
          <div class="cmp-amt-sub">Pickup Amount</div>
          <div class="cmp-kv">
            <span class="cmp-k">Location</span><span class="cmp-v" id="cmpRCPLoc">—</span>
            <span class="cmp-k">Region</span><span class="cmp-v" id="cmpRCPRegion">—</span>
            <span class="cmp-k">State</span><span class="cmp-v" id="cmpRCPState">—</span>
            <span class="cmp-k">HCI Slip</span><span class="cmp-v" id="cmpRCPSlip">—</span>
            <span class="cmp-k">Deposit Mode</span><span class="cmp-v" id="cmpRCPMode">—</span>
            <span class="cmp-k">Deposit Slip</span><span class="cmp-v" id="cmpRCPDSlip">—</span>
            <span class="cmp-k">Difference</span><span class="cmp-v" id="cmpRCPDiff">—</span>
            <span class="cmp-k">Remarks</span><span class="cmp-v" id="cmpRCPRemarks">—</span>
          </div>
        </div>
      </div>

      {{-- Panel 2: Branch Financial Report --}}
      <div class="cmp-panel" id="cmpPanelBFR" style="display:none;">
        <div class="cmp-panel-hdr" style="background:#eff6ff;border-bottom:1px solid #bfdbfe;">
          <div class="cmp-ph-icon cph-blue"><i class="bi bi-file-bar-graph-fill"></i></div>
          <div>
            <h6>Branch Financial Report</h6>
            <small>Radiant cash collection entry</small>
          </div>
        </div>
        <div class="cmp-panel-body" id="cmpBFRBody">
          <div class="cmp-no-data">
            <i class="bi bi-file-earmark-x"></i>
            <p>No branch report found<br>for this location &amp; date</p>
          </div>
        </div>
      </div>

      {{-- Panel 3: Bank Statement --}}
      <div class="cmp-panel" id="cmpPanelBank" style="display:none;">
        <div class="cmp-panel-hdr" style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;">
          <div class="cmp-ph-icon cph-green"><i class="bi bi-bank2"></i></div>
          <div>
            <h6>Bank Statement</h6>
            <small>BY CASH — matching description</small>
          </div>
        </div>
        <div class="cmp-panel-body" id="cmpBankBody">
          <div class="cmp-no-data">
            <i class="bi bi-search"></i>
            <p>No bank entries matched<br>"BY CASH - {location}"</p>
          </div>
        </div>
      </div>
    </div>

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
        filterOptions: @json(route('superadmin.radiantcash.filteroptions')),
        reconcileCounts: @json(route('superadmin.radiantcash.reconcilecounts')),
        reconcileLists: @json(route('superadmin.radiantcash.reconcilists')),
        batches: @json(route('superadmin.radiantcash.batches')),
        deleteBatch: @json(route('superadmin.radiantcash.deletebatch'))
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

    function getReconcileParams() {
        syncPerPageHidden();
        return {
            date_from: $('#fDateFrom').val() || '',
            date_to: $('#fDateTo').val() || '',
            state: $('#rcpStateSelect').val() || '',
            zone_id: $('#rcpZoneSelect').val() || '',
            branch_id: $('#rcpBranchSelect').val() || '',
            search: $('#searchInput').val() || ''
        };
    }

    function esc(s) {
        return $('<div/>').text(s == null ? '' : String(s)).html();
    }

    function statusPillClass(st) {
        if (st === 'match') return 'match';
        if (st === 'close') return 'close';
        if (st === 'mismatch') return 'mismatch';
        return 'nodata';
    }

    function loadReconcileCounts() {
        $('#rcpStatMatch, #rcpStatMismatch').text('…');
        $.ajax({
            url: rcpRoutes.reconcileCounts,
            method: 'GET',
            data: getReconcileParams(),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function (res) {
            if (!res || !res.success) return;
            $('#rcpStatMatch').text(Number(res.match_count || 0).toLocaleString('en-IN'));
            $('#rcpStatMismatch').text(Number(res.mismatch_count || 0).toLocaleString('en-IN'));
        }).fail(function () {
            $('#rcpStatMatch, #rcpStatMismatch').text('—');
        });
    }

    function renderBatchesTable(rows) {
        var $tb = $('#rcpBatchesBody');
        if (!rows || !rows.length) {
            $tb.html('<tr><td colspan="4" class="text-center py-4 text-muted" style="font-size:.8rem;">No uploads in the current filter.</td></tr>');
            return;
        }
        var html = '';
        rows.forEach(function (r) {
            var fname = r.file_name || '(unknown file)';
            var dt = r.uploaded_at || '';
            html += '<tr>';
            html += '<td><div class="rcp-batch-file" title="' + esc(fname) + '">' + esc(fname) + '</div>';
            html += '<div class="rcp-batch-id">' + esc(r.batch_id || '') + '</div></td>';
            html += '<td class="rcp-batch-rows"><strong>' + Number(r.row_count || 0).toLocaleString('en-IN') + '</strong></td>';
            html += '<td><span class="rcp-batch-meta">' + esc(dt) + '</span></td>';
            html += '<td style="text-align:right;"><button type="button" class="btn-batch-del rcp-batch-delete" data-batch="' + esc(r.batch_id || '') + '">Delete upload</button></td>';
            html += '</tr>';
        });
        $tb.html(html);
    }

    function loadBatches() {
        $('#rcpBatchesBody').html('<tr><td colspan="4" class="text-center py-3 text-muted" style="font-size:.8rem;">Loading…</td></tr>');
        $.ajax({
            url: rcpRoutes.batches,
            method: 'GET',
            data: getReconcileParams(),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function (res) {
            if (!res || !res.success) {
                $('#rcpBatchesBody').html('<tr><td colspan="4" class="text-center py-3 text-danger" style="font-size:.8rem;">Could not load batches.</td></tr>');
                return;
            }
            renderBatchesTable(res.batches || []);
        }).fail(function () {
            $('#rcpBatchesBody').html('<tr><td colspan="4" class="text-center py-3 text-danger" style="font-size:.8rem;">Could not load batches.</td></tr>');
        });
    }

    function renderReconcileRow(r) {
        var bfr = '<span class="rcp-mm-pill ' + statusPillClass(r.bfr_status) + '">' + esc(r.bfr_status) + '</span> <span class="rcp-mm-amt">' + fmtINR(r.bfr_amount) + '</span>';
        var bk = '<span class="rcp-mm-pill ' + statusPillClass(r.bank_status) + '">' + esc(r.bank_status) + '</span> <span class="rcp-mm-amt">' + fmtINR(r.bank_amount) + '</span>';
        var d = r.pickup_date_parsed || r.pickup_date || '';
        var slip = (r.hci_slip != null && String(r.hci_slip) !== '') ? esc(r.hci_slip) : '—';
        var note = r.reason ? '<div style="font-size:.65rem;color:#94a3b8;margin-top:2px;">' + esc(r.reason) + '</div>' : '';
        return '<tr><td>' + esc(r.pickup_id) + '</td><td>' + esc(d) + '</td><td>' + esc(r.location) + note + '</td><td>' + slip + '</td><td class="rcp-mm-amt">' + fmtINR(r.rcp_amount) + '</td><td>' + bfr + '</td><td>' + bk + '</td></tr>';
    }

    function setRcpMmTab(which) {
        var w = which === 'mismatch' ? 'mismatch' : 'matched';
        $('.rcp-mm-tab').removeClass('is-active').attr('aria-selected', 'false');
        $('.rcp-mm-tab[data-rcp-mm-tab="' + w + '"]').addClass('is-active').attr('aria-selected', 'true');
        $('.rcp-mm-tab-panel').removeClass('is-active');
        if (w === 'matched') {
            $('#rcpMmTabPanelMatched').addClass('is-active');
        } else {
            $('#rcpMmTabPanelMismatch').addClass('is-active');
        }
    }

    var rcpMmLoading = false;
    function openReconcileModal(defaultTab) {
        if (rcpMmLoading) return;
        rcpMmLoading = true;
        var tab = defaultTab === 'mismatch' ? 'mismatch' : 'matched';
        setRcpMmTab(tab);
        $('#rcpMmEmptyMatched, #rcpMmEmptyMismatch').removeClass('is-visible');
        $('#rcpMmBodyMatch, #rcpMmBodyMismatch').html('<tr><td colspan="7" class="text-center py-4 text-muted" style="font-size:.8rem;">Loading…</td></tr>');
        $('#rcpMmOverlay').addClass('show').attr('aria-hidden', 'false');
        $('body').css('overflow', 'hidden');
        $.ajax({
            url: rcpRoutes.reconcileLists,
            method: 'GET',
            data: getReconcileParams(),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function (res) {
            if (!res || !res.success) {
                toastr.error('Could not load reconciliation lists.');
                closeReconcileModal();
                return;
            }
            var mc = Number(res.match_count || 0);
            var mmc = Number(res.mismatch_count || 0);
            var cap = res.list_cap || 400;
            $('#rcpMmChipMatch').text(mc.toLocaleString('en-IN'));
            $('#rcpMmChipMismatch').text(mmc.toLocaleString('en-IN'));
            $('#rcpMmTabBadgeMatch').text(mc.toLocaleString('en-IN'));
            $('#rcpMmTabBadgeMismatch').text(mmc.toLocaleString('en-IN'));

            var matched = res.matched || [];
            var mismatched = res.mismatched || [];
            var mRows = matched.map(renderReconcileRow).join('');
            var uRows = mismatched.map(renderReconcileRow).join('');

            $('#rcpMmBodyMatch').html(mRows);
            $('#rcpMmBodyMismatch').html(uRows);

            $('#rcpMmEmptyMatched').toggleClass('is-visible', matched.length === 0);
            $('#rcpMmEmptyMismatch').toggleClass('is-visible', mismatched.length === 0);

            var note = 'Totals include every pickup row under the current filters. Each tab lists up to ' + cap + ' sample rows (whichever appear first in the dataset order).';
            if (matched.length >= cap || mismatched.length >= cap) {
                note += ' One or both samples may stop at the cap even if more rows exist on that side.';
            }
            $('#rcpMmFootNote').text(note);
            setRcpMmTab(tab);
        }).fail(function () {
            toastr.error('Could not load reconciliation lists.');
            closeReconcileModal();
        }).always(function () {
            rcpMmLoading = false;
        });
    }

    function closeReconcileModal() {
        $('#rcpMmOverlay').removeClass('show').attr('aria-hidden', 'true');
        $('body').css('overflow', '');
    }

    function loadFilterOptions(opts) {
        // opts: { state, zoneId, selectedZoneId, selectedBranchId, updateZones, updateBranches }
        var params = {};
        if (opts.state) params.state = opts.state;
        if (opts.zoneId) params.zone_id = opts.zoneId;

        $.ajax({
            url: rcpRoutes.filterOptions,
            method: 'GET',
            data: params,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function (res) {
            if (!res.success) return;

            if (opts.updateZones) {
                var $zn = $('#rcpZoneSelect');
                $zn.html('<option value="">All Zones</option>');
                (res.zones || []).forEach(function (z) {
                    var opt = $('<option></option>').attr('value', z.id).text(z.name);
                    if (opts.selectedZoneId && String(z.id) === String(opts.selectedZoneId)) opt.prop('selected', true);
                    $zn.append(opt);
                });
            }

            if (opts.updateBranches) {
                var $br = $('#rcpBranchSelect');
                $br.html('<option value="">All Branches</option>');
                (res.branches || []).forEach(function (b) {
                    var opt = $('<option></option>').attr('value', b.id).text(b.name);
                    if (opts.selectedBranchId && String(b.id) === String(opts.selectedBranchId)) opt.prop('selected', true);
                    $br.append(opt);
                });
            }
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
            $('#rcpStatBatches').text(res.stats.total_batches);
            loadReconcileCounts();
            if ($('#rcpBatchSection').hasClass('rcp-batch-section--open')) {
                loadBatches();
            }
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
        $('#rcpBranchSelect').html('<option value="">All Branches</option>');
        // Reload all zones that have data (no state filter)
        loadFilterOptions({ state: '', updateZones: true, updateBranches: false });
        $('#filterSearchInput, #searchInput').val('');
        $('#rcpPerPage').val('25');
        $('#filterFormPerPage').val('25');
        rcpLoad(1);
    }

    function removeOneFilter(key) {
        if (key === 'date_from') {
            clearFlatpickr('#fDateFrom');
        } else if (key === 'date_to') {
            clearFlatpickr('#fDateTo');
        } else if (key === 'state') {
            $('#rcpStateSelect').val('');
            $('#rcpZoneSelect').val('');
            $('#rcpBranchSelect').html('<option value="">All Branches</option>');
            // Reload zones without state restriction
            loadFilterOptions({ state: '', updateZones: true, updateBranches: false });
        } else if (key === 'zone_id') {
            $('#rcpZoneSelect').val('');
            $('#rcpBranchSelect').html('<option value="">All Branches</option>');
        } else if (key === 'branch_id') {
            $('#rcpBranchSelect').val('');
        } else if (key === 'search') {
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
                closeUploadModal();
                resetUploadFormState();
                rcpLoad(1);

                // Show upload success
                toastr.success('File uploaded and rows imported successfully.', 'Upload Complete', { timeOut: 4000 });

                // Show per-date alert results
                var summaries = res.alert_summaries || [];
                if (summaries.length === 0) {
                    toastr.info('No mismatch check ran (no data rows found).', '', { timeOut: 4000 });
                } else {
                    var totalMismatch = summaries.reduce(function(s, r){ return s + (r.mismatch || 0); }, 0);
                    summaries.forEach(function(r) {
                        if (r.all_matched) {
                            toastr.success(r.message, '✓ ' + r.date, { timeOut: 5000 });
                        } else if (r.email_sent) {
                            toastr.warning(r.message, '⚠ ' + r.date + ' — Alert Sent', { timeOut: 8000 });
                        } else if (r.mismatch > 0) {
                            toastr.error(r.message, '⚠ ' + r.date + ' — No Recipients', { timeOut: 8000 });
                        } else if (!r.found) {
                            toastr.info(r.message, r.date, { timeOut: 4000 });
                        }
                    });
                }
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
            closeCmpModal();
            closeReconcileModal();
        }
    });

    // State changes → reload zones (only those with data for that state), reset branch
    $('#rcpStateSelect').on('change.rcp', function () {
        var state = $(this).val();
        $('#rcpZoneSelect').val('');
        $('#rcpBranchSelect').html('<option value="">All Branches</option>');
        loadFilterOptions({ state: state, updateZones: true, updateBranches: false });
    });

    // Zone changes → reload branches (only those with data for that zone + current state)
    $('#rcpZoneSelect').on('change.rcp', function () {
        var zid   = $(this).val();
        var state = $('#rcpStateSelect').val();
        $('#rcpBranchSelect').html('<option value="">All Branches</option>');
        if (zid) {
            loadFilterOptions({ state: state, zoneId: zid, updateZones: false, updateBranches: true });
        }
    });

    renderChips();

    loadReconcileCounts();

    function toggleUploadBatchesPanel() {
        var $sec = $('#rcpBatchSection');
        var $card = $('#rcpStatBatchesCard');
        if ($sec.hasClass('rcp-batch-section--open')) {
            $sec.removeClass('rcp-batch-section--open');
            $card.removeClass('rcp-stat-active');
        } else {
            $sec.addClass('rcp-batch-section--open');
            $card.addClass('rcp-stat-active');
            loadBatches();
        }
    }

    $('#rcpStatBatchesCard').on('click.rcp', toggleUploadBatchesPanel);
    $('#rcpStatBatchesCard').on('keydown.rcp', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleUploadBatchesPanel();
        }
    });
    $('#rcpBatchHide').on('click.rcp', function () {
        $('#rcpBatchSection').removeClass('rcp-batch-section--open');
        $('#rcpStatBatchesCard').removeClass('rcp-stat-active');
    });

    $('#rcpMmModalInner').on('click.rcp', '[data-rcp-mm-tab]', function () {
        setRcpMmTab($(this).data('rcp-mm-tab'));
    });

    $('#rcpStatMatchCard').on('click.rcp', function () { openReconcileModal('match'); });
    $('#rcpStatMatchCard').on('keydown.rcp', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openReconcileModal('match'); }
    });
    $('#rcpStatMismatchCard').on('click.rcp', function () { openReconcileModal('mismatch'); });
    $('#rcpStatMismatchCard').on('keydown.rcp', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openReconcileModal('mismatch'); }
    });
    $('#rcpMmClose').on('click.rcp', closeReconcileModal);
    $('#rcpMmOverlay').on('click.rcp', function (e) {
        if (e.target === this) closeReconcileModal();
    });
    $('#rcpMmModalInner').on('click.rcp', function (e) { e.stopPropagation(); });

    $('#rcpBatchesRefresh').on('click.rcp', function () { loadBatches(); });

    $(document).on('click.rcp', '.rcp-batch-delete', function () {
        var batch = $(this).data('batch');
        if (!batch) return;
        if (!window.confirm('Delete this upload? All pickup rows from this file will be removed, and bank statement links pointing at those rows will be cleared.')) {
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: rcpRoutes.deleteBatch,
            method: 'POST',
            data: { _token: rcpCsrf, batch_id: batch },
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function (res) {
            if (res && res.success) {
                toastr.success(res.message || 'Upload deleted.');
                rcpLoad(1);
            } else {
                toastr.error((res && res.message) ? res.message : 'Delete failed.');
            }
        }).fail(function (xhr) {
            var j = xhr.responseJSON;
            toastr.error((j && j.message) ? j.message : 'Delete failed.');
        }).always(function () {
            $btn.prop('disabled', false);
        });
    });

    /* ════════════════════════════════════════
       COMPARISON MODAL
    ════════════════════════════════════════ */
    var cmpRoute = @json(route('superadmin.radiantcash.compare', ['id' => '__ID__']));

    function fmtINR2(n) {
        if (!n && n !== 0) return '—';
        return '₹' + Number(n).toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function matchClass(a, b, tolerance) {
        if (!a && !b) return 'cmp-pill-nodata';
        if (!a || !b) return 'cmp-pill-mismatch';
        var diff = Math.abs(a - b);
        var pct  = (a > 0) ? (diff / a) : 1;
        if (pct <= (tolerance || 0.01)) return 'cmp-pill-match';
        if (pct <= 0.10) return 'cmp-pill-close';
        return 'cmp-pill-mismatch';
    }

    function matchLabel(cls) {
        return { 'cmp-pill-match':'✓ Match', 'cmp-pill-close':'~ Close', 'cmp-pill-mismatch':'✗ Mismatch', 'cmp-pill-nodata':'– No Data' }[cls] || '—';
    }

    function openCmpModal() {
        $('#cmpOverlay').addClass('show');
        $('body').css('overflow', 'hidden');
    }

    function closeCmpModal() {
        $('#cmpOverlay').removeClass('show');
        $('body').css('overflow', '');
    }

    function resetCmpModal() {
        $('#cmpSpinner').show();
        $('#cmpPanelRCP, #cmpPanelBFR, #cmpPanelBank').hide();
        $('#cmpLocationText').text('—');
        ['#cmpPillRCP','#cmpPillBFR','#cmpPillBank'].forEach(function(id) {
            $(id).attr('class','cmp-match-pill cmp-pill-nodata');
        });
        $('#cmpPillRCP').html('<i class="bi bi-cash-stack"></i> RCP: —');
        $('#cmpPillBFR').html('<i class="bi bi-file-bar-graph"></i> Branch Report: —');
        $('#cmpPillBank').html('<i class="bi bi-bank2"></i> Bank Statement: —');
    }

    function renderCmpModal(res) {
        var p   = res.pickup;
        var bfr = res.branch_reports || [];
        var bk  = res.bank_entries  || [];
        var mb  = res.matched_branch;

        /* ── Location badge ── */
        $('#cmpLocationText').text((p.location || '—') + (mb ? '  →  ' + mb.name + ' [' + (mb.zone || '') + ']' : '  (no master match)'));

        /* ── Panel 1: RCP ── */
        $('#cmpRCPDate').text(p.pickup_date || '—');
        $('#cmpRCPAmt').text(fmtINR2(p.pickup_amount));
        $('#cmpRCPLoc').text(p.location || '—');
        $('#cmpRCPRegion').text(p.region || '—');
        $('#cmpRCPState').text(p.state_name || '—');
        $('#cmpRCPSlip').text(p.hci_slip_no || '—');
        $('#cmpRCPMode').text(p.deposit_mode || '—');
        $('#cmpRCPDSlip').text(p.deposit_slip_no || '—');
        var diffVal = p.difference || 0;
        $('#cmpRCPDiff').html(diffVal != 0
            ? '<span style="color:#f43f5e;font-weight:800;">' + fmtINR2(diffVal) + '</span>'
            : '<span style="color:#059669;">0 (Matched)</span>');
        $('#cmpRCPRemarks').text(p.remarks || '—');
        $('#cmpPanelRCP').show();
        $('#cmpPillRCP').html('<i class="bi bi-cash-stack"></i> RCP: ' + fmtINR2(p.pickup_amount));

        /* ── Panel 2: Branch Financial Report ── */
        var bfrTotalAmt = res.bfr_total_amount || 0;

        if (bfr.length) {
            var totalBar =
                '<div class="cmp-bfr-total">' +
                '<div><div class="cmp-bfr-total-label">Total Radiant Collection</div>' +
                '<div class="cmp-bfr-total-amt">' + fmtINR2(bfrTotalAmt) + '</div></div>' +
                '<div class="cmp-bfr-window">' + bfr.length + ' report' + (bfr.length > 1 ? 's' : '') + '</div>' +
                '</div>';

            var bfrHtml = totalBar;
            bfr.forEach(function(r) {
                var notCol = r.radiant_not_collected
                    ? '<div style="color:#f43f5e;font-size:.7rem;font-weight:700;margin-top:4px;">⚠ Not Collected' +
                      (r.radiant_not_collected_remarks ? ': ' + r.radiant_not_collected_remarks : '') + '</div>'
                    : '';
                bfrHtml +=
                    '<div style="padding:10px 0;border-bottom:1px solid #e2e8f0;">' +
                    '<div class="cmp-amt" style="color:#1d4ed8;font-size:1.25rem;">' + fmtINR2(r.radiant_collection_amount) + '</div>' +
                    '<div class="cmp-amt-sub">Radiant Collection</div>' +
                    '<div class="cmp-kv">' +
                    '<span class="cmp-k">Report Date</span><span class="cmp-v">' + (r.report_date || '—') + '</span>' +
                    '<span class="cmp-k">Branch</span><span class="cmp-v">' + (r.branch_name || '—') + '</span>' +
                    '<span class="cmp-k">Zone</span><span class="cmp-v">' + (r.zone_name || '—') + '</span>' +
                    '<span class="cmp-k">Collected Date</span><span class="cmp-v">' + (r.radiant_collected_date || '—') + '</span>' +
                    '<span class="cmp-k">Status</span><span class="cmp-v">' + (r.overall_approval_label || '—') + '</span>' +
                    '</div>' + notCol + '</div>';
            });
            $('#cmpBFRBody').html(bfrHtml);
        } else {
            var noDataMsg = mb
                ? '<i class="bi bi-file-earmark-x"></i><p>No branch report found<br>for <strong>' + mb.name + '</strong></p>'
                : '<i class="bi bi-exclamation-circle"></i><p>Branch not matched in<br>Location Master.<br>Cannot search reports.</p>';
            $('#cmpBFRBody').html('<div class="cmp-no-data">' + noDataMsg + '</div>');
        }
        $('#cmpPanelBFR').show();

        var bfrPillCls = bfr.length ? matchClass(p.pickup_amount, bfrTotalAmt) : 'cmp-pill-mismatch';
        $('#cmpPillBFR').attr('class', 'cmp-match-pill ' + bfrPillCls)
            .html('<i class="bi bi-file-bar-graph"></i> Branch: ' + (bfr.length ? fmtINR2(bfrTotalAmt) : 'No Data') + ' — ' + matchLabel(bfrPillCls));

        /* ── Panel 3: Bank Statement ── */
        var bankTotal = 0;
        if (bk.length) {
            bk.forEach(function(e){ bankTotal += e.deposit || 0; });
            var bkTotalBar =
                '<div style="background:linear-gradient(135deg,#059669,#10b981);border-radius:10px;' +
                'padding:10px 14px;margin-bottom:12px;color:#fff;display:flex;align-items:center;justify-content:space-between;">' +
                '<div><div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;opacity:.75;">Total Deposit</div>' +
                '<div style="font-size:1.1rem;font-weight:800;font-family:\'JetBrains Mono\',monospace;">' + fmtINR2(bankTotal) + '</div></div>' +
                '<div style="font-size:.67rem;font-weight:700;padding:4px 10px;border-radius:6px;background:rgba(255,255,255,.18);">' + bk.length + ' entr' + (bk.length > 1 ? 'ies' : 'y') + '</div>' +
                '</div>';
            var bkHtml = bkTotalBar;
            bk.forEach(function(e) {
                bkHtml +=
                    '<div class="cmp-bank-row">' +
                    '<div class="cmp-bank-desc">' + $('<div/>').text(e.description).html() + '</div>' +
                    '<div class="cmp-bank-amt">+ ' + fmtINR2(e.deposit) + '</div>' +
                    '<div class="cmp-bank-meta">' +
                    '<i class="bi bi-calendar3"></i> ' + (e.transaction_date || '—') +
                    (e.reference_number ? ' &nbsp;·&nbsp; Ref: ' + e.reference_number : '') +
                    (e.match_status ? ' &nbsp;·&nbsp; <em>' + e.match_status + '</em>' : '') +
                    '</div></div>';
            });
            $('#cmpBankBody').html(bkHtml);
        } else {
            $('#cmpBankBody').html(
                '<div class="cmp-no-data">' +
                '<i class="bi bi-search"></i>' +
                '<p>No bank entries matched<br><em>"BY CASH — ' + $('<div/>').text(p.location || '').html() + '"</em><br>in ±1 day window</p>' +
                '</div>'
            );
        }
        $('#cmpPanelBank').show();

        var bkPillCls = bk.length ? matchClass(p.pickup_amount, bankTotal) : 'cmp-pill-mismatch';
        $('#cmpPillBank').attr('class', 'cmp-match-pill ' + bkPillCls)
            .html('<i class="bi bi-bank2"></i> Bank: ' + (bk.length ? fmtINR2(bankTotal) : 'No Data') + ' — ' + matchLabel(bkPillCls));
    }

    function loadCompare(id) {
        resetCmpModal();
        openCmpModal();
        var url = cmpRoute.replace('__ID__', id);
        $.ajax({
            url: url,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function(res) {
            $('#cmpSpinner').hide();
            if (res && res.success) {
                renderCmpModal(res);
            } else {
                toastr.error('Could not load comparison data.');
                closeCmpModal();
            }
        }).fail(function() {
            $('#cmpSpinner').hide();
            toastr.error('Failed to fetch comparison data.');
            closeCmpModal();
        });
    }

    /* Row click → open compare */
    $(document).on('click.rcp', '.rcp-row-clickable', function() {
        var id = $(this).data('id');
        if (id) loadCompare(id);
    });

    /* Close buttons */
    $('#cmpCloseBtn').on('click.rcp', closeCmpModal);
    $('#cmpOverlay').on('click.rcp', function(e) {
        if (e.target === this) closeCmpModal();
    });

    /* ════════════════════════════════════════
       MISMATCH ALERT MODAL
    ════════════════════════════════════════ */
    var almRoute = @json(route('superadmin.radiantcash.mismatchalert'));

    flatpickr('#almDateInput', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        allowInput:  false,
        maxDate:     'today',
        onChange: function() {
            // Clear previous result when date changes
            $('#almResult').hide().removeClass('success warning error').text('');
            $('#almSendBtn').prop('disabled', false)
                .html('<i class="bi bi-envelope-exclamation-fill"></i> Run Check &amp; Send Alert');
        }
    });

    function openAlertModal() {
        $('#almResult').hide().removeClass('success warning error').text('');
        $('#almSendBtn').prop('disabled', false)
            .html('<i class="bi bi-envelope-exclamation-fill"></i> Run Check &amp; Send Alert');
        $('#alertOverlay').addClass('show');
        $('body').css('overflow', 'hidden');
    }
    function closeAlertModal() {
        $('#alertOverlay').removeClass('show');
        $('body').css('overflow', '');
    }

    $('#rcpAlertBtn').on('click.rcp', openAlertModal);
    $('#almCloseBtn, #almCancelBtn').on('click.rcp', closeAlertModal);
    $('#alertOverlay').on('click.rcp', function(e) {
        if (e.target === this) closeAlertModal();
    });
    $('#alertModalInner').on('click.rcp', function(e) { e.stopPropagation(); });

    $('#almSendBtn').on('click.rcp', function() {
        var dt = document.getElementById('almDateInput');
        var dateVal = dt && dt._flatpickr ? dt._flatpickr.input.value : $(dt).val();
        if (!dateVal) {
            toastr.warning('Please select a pickup date first.');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true)
            .html('<i class="bi bi-hourglass-split"></i> Running comparison…');
        $('#almResult').hide().removeClass('success warning error');

        $.ajax({
            url:     almRoute,
            type:    'POST',
            data:    { alert_date: dateVal, _token: rcpCsrf },
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function(res) {
            var $r = $('#almResult');
            if (res.success) {
                if (res.all_matched) {
                    $r.addClass('success').text(res.message).show();
                    toastr.success(res.message);
                    $btn.html('<i class="bi bi-check-circle-fill"></i> All Matched!');
                } else {
                    $r.addClass('warning')
                      .html('<i class="bi bi-envelope-check-fill"></i> ' + res.message).show();
                    toastr.success(res.message);
                    $btn.html('<i class="bi bi-check-circle-fill"></i> Alert Sent!');
                }
            } else {
                $r.addClass('error')
                  .html('<i class="bi bi-exclamation-triangle-fill"></i> ' + (res.message || 'Error')).show();
                toastr.error(res.message || 'Something went wrong.');
                $btn.prop('disabled', false)
                    .html('<i class="bi bi-envelope-exclamation-fill"></i> Run Check &amp; Send Alert');
            }
        }).fail(function(xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message : 'Server error. Please try again.';
            $('#almResult').addClass('error')
                .html('<i class="bi bi-exclamation-triangle-fill"></i> ' + msg).show();
            toastr.error(msg);
            $btn.prop('disabled', false)
                .html('<i class="bi bi-envelope-exclamation-fill"></i> Run Check &amp; Send Alert');
        });
    });

    /* Also close alert modal on Escape */
    $(document).off('keydown.rcp').on('keydown.rcp', function(e) {
        if (e.key === 'Escape') {
            closeUploadModal();
            resetUploadFormState();
            closeFilterModal();
            closeCmpModal();
            closeAlertModal();
        }
    });
})(window.jQuery);
</script>

@include('superadmin.superadminfooter')
</body>
</html>