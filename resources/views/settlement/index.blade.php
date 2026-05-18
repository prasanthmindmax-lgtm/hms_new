<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

<style>
/* ════════════════════════════════════════════════════════════════
   SETTLEMENT DASHBOARD
════════════════════════════════════════════════════════════════ */
:root{
    --navy:#0f172a;--navy2:#1e293b;--navy3:#334155;
    --amber:#f59e0b;--amber2:#d97706;--amber-lt:#fef3c7;
    --teal:#0d9488;--teal-lt:#ccfbf1;
    --rose:#f43f5e;--rose-lt:#ffe4e6;
    --violet:#8b5cf6;--violet-lt:#ede9fe;
    --surface:#fff;--surface2:#f8fafc;
    --border:#e2e8f0;--border2:#cbd5e1;
    --text:#0f172a;--text2:#475569;--text3:#94a3b8;
    --radius:14px;--radius-sm:9px;
    --shadow:0 4px 24px rgba(15,23,42,.08);
    --shadow-lg:0 16px 48px rgba(15,23,42,.14);
    --font:'Plus Jakarta Sans',sans-serif;
    --mono:'JetBrains Mono',monospace;
}
*{box-sizing:border-box;}
body{font-family:var(--font);background:#f1f5f9;}
.sh-wrap{padding:28px 24px;}

/* ── HEADER ── */
.sh-header{
    background:linear-gradient(135deg,var(--navy) 0%,var(--navy2) 60%,#1e3a5f 100%);
    border-radius:20px;padding:28px 32px;margin-bottom:24px;
    position:relative;overflow:hidden;
}
.sh-header::before{content:'';position:absolute;top:-60px;right:-60px;width:260px;height:260px;border-radius:50%;background:radial-gradient(circle,rgba(245,158,11,.15),transparent 70%);}
.sh-header::after{content:'';position:absolute;bottom:-40px;left:160px;width:180px;height:180px;border-radius:50%;background:radial-gradient(circle,rgba(13,148,136,.12),transparent 70%);}
.sh-header-inner{position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;}
.sh-header-title{display:flex;align-items:center;gap:16px;}
.sh-header-icon{width:52px;height:52px;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;}
.sh-header-text h1{font-size:1.4rem;font-weight:800;color:#fff;margin:0 0 3px;letter-spacing:-.4px;}
.sh-header-text p{font-size:.8rem;color:rgba(255,255,255,.6);margin:0;}
.sh-header-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.hbtn{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:var(--radius-sm);font-size:.82rem;font-weight:700;cursor:pointer;border:none;font-family:var(--font);white-space:nowrap;transition:all .15s;text-decoration:none;}
.hbtn-amber{background:var(--amber);color:var(--navy);}
.hbtn-amber:hover{background:var(--amber2);transform:translateY(-1px);box-shadow:0 4px 14px rgba(245,158,11,.4);color:var(--navy);}
.hbtn-outline{background:rgba(255,255,255,.08);color:rgba(255,255,255,.85);border:1px solid rgba(255,255,255,.18);}
.hbtn-outline:hover{background:rgba(255,255,255,.15);color:#fff;}

/* ── STAT CARDS (single row) ── */
.stat-grid{
  display:flex;flex-wrap:nowrap;align-items:stretch;gap:12px;
  margin-bottom:24px;overflow-x:auto;-webkit-overflow-scrolling:touch;
  scrollbar-width:thin;
}
.stat-grid::-webkit-scrollbar{height:5px;}
.stat-grid::-webkit-scrollbar-thumb{background:var(--border2);border-radius:4px;}
.stat-card{
  flex:1 1 0;min-width:0;
  background:var(--surface);border-radius:var(--radius);padding:14px 16px;
  border:1px solid var(--border);box-shadow:var(--shadow);position:relative;overflow:hidden;
  transition:transform .15s,box-shadow .15s;
}
.stat-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg);}
.stat-card::after{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:var(--radius) var(--radius) 0 0;}
.sc-amber::after{background:linear-gradient(90deg,var(--amber),#fbbf24);}
.sc-teal::after{background:linear-gradient(90deg,var(--teal),#2dd4bf);}
.sc-violet::after{background:linear-gradient(90deg,var(--violet),#c084fc);}
.sc-emerald::after{background:linear-gradient(90deg,#059669,#34d399);}
.sc-slate::after{background:linear-gradient(90deg,#64748b,#94a3b8);}
.sc-rose::after{background:linear-gradient(90deg,#e11d48,#fb7185);}
.stat-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:.95rem;margin-bottom:8px;}
.sc-amber .stat-icon{background:var(--amber-lt);color:var(--amber2);}
.sc-teal  .stat-icon{background:var(--teal-lt);color:var(--teal);}
.sc-violet .stat-icon{background:var(--violet-lt);color:var(--violet);}
.sc-emerald .stat-icon{background:#d1fae5;color:#047857;}
.sc-slate .stat-icon{background:#e2e8f0;color:#475569;}
.sc-rose .stat-icon{background:var(--rose-lt);color:var(--rose);}
.stat-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text3);margin-bottom:3px;}
.stat-value{font-size:1.05rem;font-weight:800;color:var(--text);font-family:var(--mono);letter-spacing:-.5px;line-height:1.25;word-break:break-word;}
.stat-sub{font-size:.72rem;color:var(--text3);margin-top:2px;}

/* ── UPLOAD CARD ── */
.sh-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);margin-bottom:20px;overflow:hidden;}
.sh-card-header{padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--surface2);}
.sh-card-header h3{font-size:.9rem;font-weight:800;color:var(--text);display:flex;align-items:center;gap:8px;margin:0;}
.sh-card-body{padding:22px;}
.drop-zone{border:2px dashed var(--border2);border-radius:14px;padding:36px 24px;text-align:center;cursor:pointer;transition:all .2s;background:var(--surface2);}
.drop-zone:hover,.drop-zone.drag-over{border-color:var(--amber);background:var(--amber-lt);}
.dz-icon{font-size:2.2rem;margin-bottom:10px;display:block;color:var(--amber2);}
.dz-title{font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:4px;}
.dz-sub{font-size:.78rem;color:var(--text3);}
.upload-progress-wrap{margin-top:16px;display:none;}
.upload-progress-bar{height:6px;background:var(--border);border-radius:99px;overflow:hidden;}
.upload-progress-fill{height:100%;width:0;background:linear-gradient(90deg,var(--amber),var(--teal));border-radius:99px;transition:width .4s;}
.upload-status-text{font-size:.75rem;color:var(--text3);margin-top:6px;}

/* ════════════════════════════════════════════
   FILTER PANEL
════════════════════════════════════════════ */
.filter-panel{
    background:var(--surface);border-radius:var(--radius);
    border:1px solid var(--border);box-shadow:var(--shadow);
    margin-bottom:20px;
    /* overflow:visible — dropdowns extend past panel; hidden clips them */
    overflow:visible;
    position:relative;
    z-index:50;
}
.filter-panel.ms-dd-open{z-index:10060;}
.filter-panel-header{
    padding:14px 20px;background:var(--surface2);
    border-bottom:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;
    flex-wrap:wrap;gap:8px;
}
.filter-panel-title{font-size:.82rem;font-weight:800;color:var(--text);display:flex;align-items:center;gap:7px;}
.filter-panel-actions{display:flex;align-items:center;gap:8px;}
.filter-count-badge{
    background:var(--amber-lt);color:var(--amber2);
    font-size:.68rem;font-weight:800;padding:2px 8px;
    border-radius:20px;border:1px solid #fcd34d;
    display:none;
}
.filter-count-badge.show{display:inline-flex;}

.filter-body{padding:18px 20px;}
.filter-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:14px;align-items:start;
}
.filter-field label{
    display:flex;align-items:center;gap:5px;
    font-size:.67rem;font-weight:800;text-transform:uppercase;
    letter-spacing:.7px;color:var(--text3);margin-bottom:6px;
}

/* ── DATE INPUTS ── */
.fc-date{
    width:100%;padding:8px 12px;border:1.5px solid var(--border);
    border-radius:var(--radius-sm);font-size:.82rem;font-family:var(--font);
    color:var(--text);background:var(--surface2);
    transition:border-color .15s,box-shadow .15s;
}
.fc-date:focus{outline:none;border-color:var(--amber);box-shadow:0 0 0 3px rgba(245,158,11,.12);}

/* ── ACTIVE CHIPS ROW ── */
.active-chips{
    display:flex;flex-wrap:wrap;gap:6px;
    padding:10px 20px 14px;
    min-height:0;
}
.active-chips:empty{display:none;}
.fchip{
    display:inline-flex;align-items:center;gap:5px;
    padding:3px 10px 3px 10px;border-radius:20px;
    font-size:.7rem;font-weight:700;
    background:var(--navy);color:#fff;
    border:1px solid var(--navy2);
    transition:all .14s;
}
.fchip-remove{
    display:flex;align-items:center;justify-content:center;
    width:14px;height:14px;border-radius:50%;
    background:rgba(255,255,255,.2);cursor:pointer;
    font-size:.6rem;transition:background .12s;
    margin-left:2px;
}
.fchip-remove:hover{background:rgba(255,255,255,.4);}
.fchip-group{background:var(--amber2);border-color:var(--amber2);}
.fchip-date{background:var(--teal);border-color:var(--teal);}

/* filter action buttons */
.fbtn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:var(--radius-sm);font-size:.82rem;font-weight:700;cursor:pointer;border:none;font-family:var(--font);transition:all .15s;}
.fbtn-amber{background:var(--amber);color:var(--navy);}
.fbtn-amber:hover{background:var(--amber2);transform:translateY(-1px);}
.fbtn-ghost{background:var(--surface2);color:var(--text2);border:1.5px solid var(--border);}
.fbtn-ghost:hover{border-color:var(--border2);background:var(--border);}
.fbtn-sm{padding:6px 12px;font-size:.76rem;}

/* ════════════════════════════════════════════
   CUSTOM MULTI-SELECT DROPDOWN
════════════════════════════════════════════ */
.ms-wrap{position:relative;width:100%;z-index:1;}
.ms-wrap.ms-open{z-index:10061;}

/* Trigger button */
.ms-trigger{
    width:100%;display:flex;align-items:center;justify-content:space-between;
    padding:8px 12px;border:1.5px solid var(--border);
    border-radius:var(--radius-sm);background:var(--surface2);
    cursor:pointer;transition:border-color .15s,box-shadow .15s;
    user-select:none;min-height:38px;gap:8px;
}
.ms-trigger:hover{border-color:var(--border2);}
.ms-trigger.open{border-color:var(--amber);box-shadow:0 0 0 3px rgba(245,158,11,.12);}
.ms-trigger-text{
    font-size:.82rem;color:var(--text3);font-family:var(--font);
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;
}
.ms-trigger-text.has-val{color:var(--text);font-weight:600;}
.ms-count{
    display:none;align-items:center;justify-content:center;
    min-width:20px;height:20px;padding:0 6px;
    background:var(--amber);color:var(--navy);
    border-radius:10px;font-size:.65rem;font-weight:800;
}
.ms-count.show{display:flex;}
.ms-caret{font-size:.75rem;color:var(--text3);transition:transform .2s;flex-shrink:0;}
.ms-trigger.open .ms-caret{transform:rotate(180deg);}

/* Dropdown panel */
.ms-dropdown{
    position:absolute;top:calc(100% + 6px);left:0;right:0;
    background:var(--surface);border:1.5px solid var(--border);
    border-radius:var(--radius-sm);box-shadow:var(--shadow-lg);
    z-index:10062;display:none;flex-direction:column;
    max-height:min(360px,calc(100vh - 220px));
    overflow:hidden;
    animation:dropIn .15s ease;
}
.ms-dropdown.open{display:flex;}
@keyframes dropIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}

/* Search box */
.ms-search-wrap{
    padding:10px 10px 8px;
    border-bottom:1px solid var(--border);
    position:relative;flex-shrink:0;
}
.ms-search{
    width:100%;padding:6px 10px 6px 32px;
    border:1.5px solid var(--border);border-radius:8px;
    font-size:.8rem;font-family:var(--font);color:var(--text);
    background:var(--surface2);
    transition:border-color .15s;
}
.ms-search:focus{outline:none;border-color:var(--amber);}
.ms-search-icon{
    position:absolute;left:18px;top:50%;transform:translateY(-50%);
    color:var(--text3);font-size:.78rem;pointer-events:none;
}

/* Select/Clear all row */
.ms-ctrl-row{
    display:flex;justify-content:space-between;align-items:center;
    padding:5px 10px;border-bottom:1px solid var(--border);
    background:var(--surface2);flex-shrink:0;
}
.ms-ctrl-btn{
    font-size:.7rem;font-weight:700;color:var(--amber2);
    background:none;border:none;cursor:pointer;padding:2px 4px;
    font-family:var(--font);transition:color .12s;
}
.ms-ctrl-btn:hover{color:var(--navy);}
.ms-no-results{padding:14px 12px;font-size:.78rem;color:var(--text3);text-align:center;display:none;}

/* Options list */
.ms-list{
    overflow-y:auto;flex:1;
    /* custom scrollbar */
    scrollbar-width:thin;
    scrollbar-color:var(--border2) transparent;
}
.ms-list::-webkit-scrollbar{width:5px;}
.ms-list::-webkit-scrollbar-track{background:transparent;}
.ms-list::-webkit-scrollbar-thumb{background:var(--border2);border-radius:99px;}
.ms-list::-webkit-scrollbar-thumb:hover{background:var(--navy3);}

.ms-option{
    display:flex;align-items:center;gap:10px;
    padding:8px 12px;cursor:pointer;transition:background .1s;
    font-size:.8rem;color:var(--text);
}
.ms-option:hover{background:var(--surface2);}
.ms-option.selected{background:var(--amber-lt);}
.ms-option-check{
    width:16px;height:16px;border:1.5px solid var(--border2);
    border-radius:4px;display:flex;align-items:center;justify-content:center;
    flex-shrink:0;transition:all .12s;background:#fff;
}
.ms-option.selected .ms-option-check{
    background:var(--amber);border-color:var(--amber);
}
.ms-option.selected .ms-option-check::after{
    content:'✓';font-size:.6rem;color:var(--navy);font-weight:900;
}
.ms-option-label{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}

/* ════════════════════════════════════════════
   TABLE CARD
════════════════════════════════════════════ */
.table-card{
    background:var(--surface);border-radius:var(--radius);
    border:1px solid var(--border);box-shadow:var(--shadow);
    overflow:hidden;
    position:relative;
    z-index:1;
}
.table-card-head{
    padding:14px 20px;border-bottom:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;
    background:var(--surface2);flex-wrap:wrap;gap:8px;
    position:sticky;top:0;z-index:10;
}
.table-card-head h3{font-size:.9rem;font-weight:800;color:var(--text);display:flex;align-items:center;gap:8px;margin:0;}

/* Scrollable table wrapper with custom scrollbar */
.table-scroll{
    overflow:auto;
    max-height:520px;
    /* custom scrollbar */
    scrollbar-width:thin;
    scrollbar-color:var(--border2) var(--surface2);
}
.table-scroll::-webkit-scrollbar{width:7px;height:7px;}
.table-scroll::-webkit-scrollbar-track{background:var(--surface2);border-radius:4px;}
.table-scroll::-webkit-scrollbar-thumb{background:var(--border2);border-radius:99px;}
.table-scroll::-webkit-scrollbar-thumb:hover{background:var(--navy3);}
.table-scroll::-webkit-scrollbar-corner{background:var(--surface2);}

table{width:100%;border-collapse:collapse;min-width:900px;}
thead th{
    background:#f1f5f9;padding:.7rem 1rem;text-align:left;
    font-size:.67rem;font-weight:800;text-transform:uppercase;
    letter-spacing:.6px;color:var(--text3);white-space:nowrap;
    border-bottom:2px solid var(--border);
    position:sticky;top:0;z-index:5;
}
thead th:first-child{border-radius:0;}
tbody tr{border-bottom:1px solid var(--border);transition:.12s;}
tbody tr:last-child{border-bottom:none;}
tbody tr:hover{background:#fffbeb;}
tbody td{padding:.8rem 1rem;font-size:.82rem;color:var(--text);white-space:nowrap;}

/* ── BADGES ── */
.bdg{display:inline-flex;align-items:center;padding:.2rem .65rem;border-radius:99px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;}
.bdg-green{background:#d1fae5;color:#065f46;}
.bdg-amber{background:var(--amber-lt);color:var(--amber2);}
.bdg-teal{background:var(--teal-lt);color:var(--teal);}
.bdg-gray{background:#e5e7eb;color:#4b5563;}

/* MID pill */
.mid-pill{font-family:var(--mono);font-size:.72rem;font-weight:700;background:var(--navy);color:#fff;padding:.22rem .55rem;border-radius:6px;letter-spacing:.02em;}

/* Amount */
.amt{font-family:var(--mono);font-weight:700;}
.amt-positive{color:var(--teal);}
.amt-neutral{color:var(--text);}

/* ── PAGINATION ── */
.pg-wrap{padding:14px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);flex-wrap:wrap;gap:.5rem;background:var(--surface2);}
.pg-info{font-size:.75rem;color:var(--text3);}
.pg-btns{display:flex;gap:.3rem;}
.pg-btn{width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:1.5px solid var(--border);background:#fff;color:var(--text);font-size:.78rem;font-weight:700;cursor:pointer;transition:.15s;font-family:var(--font);}
.pg-btn:hover:not(:disabled){border-color:var(--amber);color:var(--amber2);}
.pg-btn.active{background:var(--amber);border-color:var(--amber);color:var(--navy);}
.pg-btn:disabled{opacity:.4;cursor:not-allowed;}
.per-page-select{font-size:.78rem;padding:.35rem .6rem;border:1.5px solid var(--border);border-radius:8px;font-family:var(--font);color:var(--text);background:var(--surface2);}
.per-page-select:focus{outline:none;border-color:var(--amber);}

/* ── TOAST ── */
.toast-wrap{position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;gap:.5rem;}
.sh-toast{display:flex;align-items:center;gap:.75rem;padding:.85rem 1.2rem;border-radius:10px;background:var(--navy);color:#fff;font-size:.82rem;font-weight:600;min-width:280px;box-shadow:var(--shadow-lg);animation:slideIn .3s ease;font-family:var(--font);}
.sh-toast.success{background:#064e3b;}
.sh-toast.error{background:#7f1d1d;}
@keyframes slideIn{from{transform:translateX(120%);opacity:0}to{transform:translateX(0);opacity:1}}

/* ── EMPTY / SKELETON ── */
.empty-state{padding:3rem;text-align:center;color:var(--text3);}
.empty-state i{font-size:2.5rem;margin-bottom:1rem;opacity:.3;display:block;}
.empty-state p{font-size:.85rem;}
.skeleton{background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.2s infinite;border-radius:6px;}
@keyframes shimmer{to{background-position:-200% 0;}}

@media(max-width:768px){
    .sh-wrap{padding:14px;}
    .stat-card{flex:0 0 min(28vw, 160px);min-width:130px;}
    .filter-grid{grid-template-columns:1fr;}
}
</style>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container"><div class="pc-content">
<div class="sh-wrap">

{{-- ── PAGE HEADER ── --}}
<div class="sh-header">
  <div class="sh-header-inner">
    <div class="sh-header-title">
      <div class="sh-header-icon">💳</div>
      <div class="sh-header-text">
        <h1>Settlement Report Dashboard</h1>
        <p>Upload Excel files, multi-filter and analyse account-wise settlement data</p>
      </div>
    </div>
    <div class="sh-header-actions">
      <button type="button" class="hbtn hbtn-amber" id="btnOpenUpload">
        <i class="bi bi-cloud-upload-fill"></i> Upload Excel
      </button>
      <a href="{{ route('settlement.uploads') }}" class="hbtn hbtn-outline">
        <i class="bi bi-folder2-open"></i> File Monitor
      </a>
    </div>
  </div>
</div>

{{-- ── STAT CARDS ── --}}
<div class="stat-grid">
  <div class="stat-card sc-teal">
    <div class="stat-icon"><i class="bi bi-bank2"></i></div>
    <div class="stat-label">Total Accounts</div>
    <div class="stat-value" id="statAccounts">—</div>
    <div class="stat-sub">Current filter</div>
  </div>
  <div class="stat-card sc-violet">
    <div class="stat-icon"><i class="bi bi-receipt"></i></div>
    <div class="stat-label">Total Transactions</div>
    <div class="stat-value" id="statTxCount">—</div>
    <div class="stat-sub">Transaction count</div>
  </div>
  <div class="stat-card sc-amber">
    <div class="stat-icon"><i class="bi bi-currency-rupee"></i></div>
    <div class="stat-label">Transaction Amount</div>
    <div class="stat-value"><span style="font-size:.75rem;font-weight:500;color:var(--text3);margin-right:2px;">₹</span><span id="statTxAmount">—</span></div>
    <div class="stat-sub">Gross value</div>
  </div>
  <div class="stat-card sc-emerald">
    <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
    <div class="stat-label">Net Settlement</div>
    <div class="stat-value"><span style="font-size:.75rem;font-weight:500;color:var(--text3);margin-right:2px;">₹</span><span id="statNetAmount">—</span></div>
    <div class="stat-sub">After charges &amp; taxes</div>
  </div>
  <div class="stat-card sc-slate" id="statPosTaggedWrap" style="display:none;">
    <div class="stat-icon"><i class="bi bi-link-45deg"></i></div>
    <div class="stat-label">POS tagged (bank)</div>
    <div class="stat-value" id="statPosTagged">—</div>
    <div class="stat-sub">Matched in bank recon</div>
  </div>
  <div class="stat-card sc-rose" id="statPosUntaggedWrap" style="display:none;">
    <div class="stat-icon"><i class="bi bi-link-break"></i></div>
    <div class="stat-label">POS not tagged</div>
    <div class="stat-value" id="statPosUntagged">—</div>
    <div class="stat-sub">No bank recon POS link</div>
  </div>
</div>

{{-- ── UPLOAD DROP ZONE (hidden by default) ── --}}
<div class="sh-card" id="uploadZoneCard" style="display:none;">
  <div class="sh-card-header">
    <h3><i class="bi bi-cloud-upload" style="color:var(--amber2)"></i> Upload Settlement Report</h3>
    <button type="button" id="btnCloseUpload" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;font-size:.78rem;font-weight:700;background:var(--surface2);color:var(--text2);border:1.5px solid var(--border);border-radius:8px;cursor:pointer;font-family:var(--font);">
      <i class="bi bi-x-lg"></i> Close
    </button>
  </div>
  <div class="sh-card-body">
    <div class="drop-zone" id="uploadDropZone">
      <input type="file" id="fileInput" accept=".xlsx,.xls" style="display:none;">
      <span class="dz-icon"><i class="bi bi-file-earmark-spreadsheet"></i></span>
      <div class="dz-title">Drop your Excel file here</div>
      <div class="dz-sub">or <strong style="color:var(--amber2);cursor:pointer;" onclick="document.getElementById('fileInput').click()">browse files</strong> — .xlsx / .xls up to 10 MB</div>
    </div>
    <div class="upload-progress-wrap" id="uploadProgressWrap">
      <div class="upload-progress-bar"><div class="upload-progress-fill" id="uploadProgressFill"></div></div>
      <div class="upload-status-text" id="uploadStatusText">Uploading…</div>
    </div>
  </div>
</div>

{{-- ── FILTER PANEL ── --}}
<div class="filter-panel" id="shFilterPanel">
  <div class="filter-panel-header">
    <div class="filter-panel-title">
      <i class="bi bi-sliders" style="color:var(--amber2)"></i>
      Filters
      <span class="filter-count-badge" id="filterCountBadge">0 active</span>
    </div>
    <div class="filter-panel-actions">
      <button class="fbtn fbtn-ghost fbtn-sm" id="btnReset"><i class="bi bi-arrow-counterclockwise"></i> Clear All</button>
      <button class="fbtn fbtn-amber fbtn-sm" id="btnApply"><i class="bi bi-funnel-fill"></i> Apply Filters</button>
    </div>
  </div>

  <div class="filter-body">
    <div class="filter-grid">

      {{-- Zone (location master) --}}
      <div class="filter-field">
        <label><i class="bi bi-diagram-3"></i> Zone</label>
        <select class="fc-date" id="filterZoneId" style="cursor:pointer;width:100%;">
          <option value="">All zones</option>
          @foreach(($zones ?? []) as $z)
            <option value="{{ $z['id'] }}">{{ $z['name'] }}</option>
          @endforeach
        </select>
      </div>

      {{-- Branch (location master) --}}
      <div class="filter-field">
        <label><i class="bi bi-building"></i> Branch</label>
        <select class="fc-date" id="filterBranchId" style="cursor:pointer;width:100%;">
          <option value="">All branches</option>
        </select>
      </div>

      @if(!empty($bankPosLinking))
      <div class="filter-field" id="filterBankPosTagWrap">
        <label><i class="bi bi-tag"></i> Bank recon · POS tag</label>
        <select class="fc-date" id="filterBankPosTag" style="cursor:pointer;width:100%;">
          <option value="">All accounts</option>
          <option value="tagged">Tagged only</option>
          <option value="untagged">Not tagged only</option>
        </select>
      </div>

      <div class="filter-field" id="filterPosTaggedByWrap">
        <label><i class="bi bi-person-check"></i> Bank tagged by</label>
        <select class="fc-date" id="filterPosTaggedBy" style="cursor:pointer;width:100%;">
          <option value="">Anyone</option>
        </select>
      </div>
      @endif

      {{-- Upload File multi-select --}}
      <div class="filter-field">
        <label><i class="bi bi-file-earmark-excel"></i> Upload File</label>
        <div class="ms-wrap" id="ms-upload">
          <div class="ms-trigger" onclick="toggleDropdown('ms-upload')">
            <span class="ms-trigger-text" id="ms-upload-text">All Uploads</span>
            <span class="ms-count" id="ms-upload-count">0</span>
            <i class="bi bi-chevron-down ms-caret"></i>
          </div>
          <div class="ms-dropdown" id="ms-upload-dd">
            <div class="ms-search-wrap">
              <i class="bi bi-search ms-search-icon"></i>
              <input class="ms-search" type="text" placeholder="Search uploads…" oninput="filterOptions('ms-upload',this.value)">
            </div>
            <div class="ms-ctrl-row">
              <button class="ms-ctrl-btn" onclick="selectAll('ms-upload')">Select All</button>
              <button class="ms-ctrl-btn" onclick="clearAll('ms-upload')">Clear</button>
            </div>
            <div class="ms-no-results" id="ms-upload-empty">No results found</div>
            <div class="ms-list" id="ms-upload-list"></div>
          </div>
        </div>
      </div>

      {{-- Account MID multi-select --}}
      <div class="filter-field">
        <label><i class="bi bi-person-badge"></i> Account (MID)</label>
        <div class="ms-wrap" id="ms-mid">
          <div class="ms-trigger" onclick="toggleDropdown('ms-mid')">
            <span class="ms-trigger-text" id="ms-mid-text">All Accounts</span>
            <span class="ms-count" id="ms-mid-count">0</span>
            <i class="bi bi-chevron-down ms-caret"></i>
          </div>
          <div class="ms-dropdown" id="ms-mid-dd">
            <div class="ms-search-wrap">
              <i class="bi bi-search ms-search-icon"></i>
              <input class="ms-search" type="text" placeholder="Search MID…" oninput="filterOptions('ms-mid',this.value)">
            </div>
            <div class="ms-ctrl-row">
              <button class="ms-ctrl-btn" onclick="selectAll('ms-mid')">Select All</button>
              <button class="ms-ctrl-btn" onclick="clearAll('ms-mid')">Clear</button>
            </div>
            <div class="ms-no-results" id="ms-mid-empty">No results found</div>
            <div class="ms-list" id="ms-mid-list"></div>
          </div>
        </div>
      </div>

      {{-- Branch multi-select --}}
      <div class="filter-field">
        <label><i class="bi bi-building"></i> Merchant (from file)</label>
        <div class="ms-wrap" id="ms-branch">
          <div class="ms-trigger" onclick="toggleDropdown('ms-branch')">
            <span class="ms-trigger-text" id="ms-branch-text">All Branches</span>
            <span class="ms-count" id="ms-branch-count">0</span>
            <i class="bi bi-chevron-down ms-caret"></i>
          </div>
          <div class="ms-dropdown" id="ms-branch-dd">
            <div class="ms-search-wrap">
              <i class="bi bi-search ms-search-icon"></i>
              <input class="ms-search" type="text" placeholder="Search branch…" oninput="filterOptions('ms-branch',this.value)">
            </div>
            <div class="ms-ctrl-row">
              <button class="ms-ctrl-btn" onclick="selectAll('ms-branch')">Select All</button>
              <button class="ms-ctrl-btn" onclick="clearAll('ms-branch')">Clear</button>
            </div>
            <div class="ms-no-results" id="ms-branch-empty">No results found</div>
            <div class="ms-list" id="ms-branch-list"></div>
          </div>
        </div>
      </div>

      {{-- Date From --}}
      <div class="filter-field">
        <label><i class="bi bi-calendar3"></i> Date From</label>
        <input type="date" class="fc-date" id="filterDateFrom">
      </div>

      {{-- Date To --}}
      <div class="filter-field">
        <label><i class="bi bi-calendar-check"></i> Date To</label>
        <input type="date" class="fc-date" id="filterDateTo">
      </div>


    </div>
  </div>

  {{-- Active filter chips --}}
  <div class="active-chips" id="activeChips"></div>
</div>

{{-- ── ACCOUNTS TABLE ── --}}
<div class="table-card">
  <div class="table-card-head">
    <h3><i class="bi bi-table" style="color:var(--amber2)"></i> Account-wise Settlement</h3>
    <div style="display:flex;align-items:center;gap:10px;">
      <span style="font-size:.75rem;color:var(--text3);">Per page:</span>
      <select class="per-page-select" id="perPage">
        <option value="10">10</option>
        <option value="15" selected>15</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
    </div>
  </div>
  <div class="table-scroll">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>MID</th>
          <th>TID</th>
          <th>Merchant / Branch</th>
          <th>Txn Date</th>
          <th>Settlement Date</th>
          <th>Txn Count</th>
          <th>Txn Amount (₹)</th>
          <th>Charges (₹)</th>
          <th>Taxes (₹)</th>
          <th>Net Settlement (₹)</th>
          <th>Bank POS</th>
          <th id="thPosTaggedBy" style="display:none;">Tagged by</th>
          <th id="thPosTaggedAt" style="display:none;">Tagged at</th>
          <th id="thPosStatus" style="display:none;">Status</th>
          <th>Currency</th>
        </tr>
      </thead>
      <tbody id="accountsTbody">
        <tr><td colspan="13"><div class="empty-state"><i class="bi bi-inbox"></i><p>Upload a file to view settlement data</p></div></td></tr>
      </tbody>
    </table>
  </div>
  <div class="pg-wrap" id="paginationBar" style="display:none;">
    <div class="pg-info" id="pageInfo"></div>
    <div class="pg-btns" id="pageBtns"></div>
  </div>
</div>

</div>{{-- /.sh-wrap --}}
</div></div>{{-- /.pc-content /.pc-container --}}

<div class="toast-wrap" id="toastWrap"></div>

<script>
/* ════════════════════════════════════════════════════════════
   STATE
════════════════════════════════════════════════════════════ */
const CSRF = document.querySelector('meta[name=csrf-token]').content;
let currentPage = 1;
let currentFilters = {};
let bankPosLinkingAvailable = @json(!empty($bankPosLinking));

// Multi-select state: { id: { label, value, selected } }
const msState = { 'ms-upload': [], 'ms-mid': [], 'ms-branch': [] };

/* ════════════════════════════════════════════════════════════
   TOAST
════════════════════════════════════════════════════════════ */
function toast(msg, type = 'default', duration = 4000) {
    const el = document.createElement('div');
    el.className = `sh-toast ${type}`;
    const icon = type === 'success' ? 'bi-check-circle-fill' : type === 'error' ? 'bi-exclamation-circle-fill' : 'bi-info-circle-fill';
    el.innerHTML = `<i class="bi ${icon}"></i> ${msg}`;
    document.getElementById('toastWrap').appendChild(el);
    setTimeout(() => el.remove(), duration);
}

/* ════════════════════════════════════════════════════════════
   UPLOAD
════════════════════════════════════════════════════════════ */
document.getElementById('btnOpenUpload').addEventListener('click', () => {
    document.getElementById('uploadZoneCard').style.display = '';
    document.getElementById('uploadZoneCard').scrollIntoView({behavior:'smooth',block:'nearest'});
});
document.getElementById('btnCloseUpload').addEventListener('click', () => {
    document.getElementById('uploadZoneCard').style.display = 'none';
});

const zone = document.getElementById('uploadDropZone');
const fileInput = document.getElementById('fileInput');
zone.addEventListener('click', () => fileInput.click());
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('drag-over'); handleFile(e.dataTransfer.files[0]); });
fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

function handleFile(file) {
    if (!file) return;
    if (!file.name.match(/\.xlsx?$/i)) { toast('Please upload an .xlsx or .xls file.', 'error'); return; }
    const wrap = document.getElementById('uploadProgressWrap');
    const fill = document.getElementById('uploadProgressFill');
    const status = document.getElementById('uploadStatusText');
    wrap.style.display = 'block'; fill.style.width = '0%'; status.textContent = 'Uploading…';
    const fd = new FormData(); fd.append('file', file);
    let prog = 0;
    const iv = setInterval(() => { prog = Math.min(prog + 5, 85); fill.style.width = prog + '%'; }, 120);
    fetch('{{ route("settlement.upload") }}', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        clearInterval(iv);
        if (data.success) {
            fill.style.width = '100%'; status.textContent = 'Processing complete!';
            const who = data.upload.uploaded_by_display && data.upload.uploaded_by_display !== '—'
                ? ' · Logged as ' + data.upload.uploaded_by_display
                : '';
            toast(data.message || ('File processed — ' + data.upload.total_accounts + ' accounts imported.' + who), 'success');
            setTimeout(() => { wrap.style.display = 'none'; fileInput.value = ''; document.getElementById('uploadZoneCard').style.display = 'none'; }, 2000);
            loadFilterOptions(); loadAccounts();
        } else {
            fill.style.width = '100%'; fill.style.background = 'var(--rose)';
            status.textContent = data.message || 'Upload failed.';
            toast(data.message || 'Upload failed.', 'error');
        }
    })
    .catch(() => { clearInterval(iv); toast('Network error during upload.', 'error'); });
}

/* ════════════════════════════════════════════════════════════
   MULTI-SELECT COMPONENT
════════════════════════════════════════════════════════════ */
function populateMultiSelect(id, items) {
    // items: [{ value, label }]
    msState[id] = items.map(it => ({ value: it.value, label: it.label, selected: false }));
    renderOptions(id);
}

function renderOptions(id) {
    const list = document.getElementById(id + '-list');
    list.innerHTML = msState[id].map((item, idx) => `
        <div class="ms-option ${item.selected ? 'selected' : ''}"
             data-idx="${idx}" data-id="${id}"
             onclick="toggleOption('${id}', ${idx})">
            <div class="ms-option-check"></div>
            <span class="ms-option-label" title="${escHtml(item.label)}">${escHtml(item.label)}</span>
        </div>
    `).join('') || '';
    updateTrigger(id);
}

function toggleOption(id, idx) {
    msState[id][idx].selected = !msState[id][idx].selected;
    renderOptions(id);
    filterOptions(id, document.querySelector(`#${id}-dd .ms-search`)?.value || '');
}

function filterOptions(id, q) {
    const lower = q.toLowerCase().trim();
    const items = document.querySelectorAll(`#${id}-list .ms-option`);
    let visible = 0;
    items.forEach(el => {
        const label = el.querySelector('.ms-option-label').textContent.toLowerCase();
        const show = !lower || label.includes(lower);
        el.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const empty = document.getElementById(id + '-empty');
    if (empty) empty.style.display = visible === 0 ? 'block' : 'none';
}

function selectAll(id) {
    msState[id].forEach(it => it.selected = true);
    renderOptions(id);
    filterOptions(id, document.querySelector(`#${id}-dd .ms-search`)?.value || '');
}

function clearAll(id) {
    msState[id].forEach(it => it.selected = false);
    renderOptions(id);
    updateTrigger(id);
}

function updateTrigger(id) {
    const selected = msState[id].filter(it => it.selected);
    const textEl  = document.getElementById(id + '-text');
    const countEl = document.getElementById(id + '-count');
    if (selected.length === 0) {
        const placeholder = { 'ms-upload': 'All Uploads', 'ms-mid': 'All Accounts', 'ms-branch': 'All Branches' };
        textEl.textContent = placeholder[id] || 'All';
        textEl.classList.remove('has-val');
        countEl.classList.remove('show');
        countEl.textContent = '0';
    } else if (selected.length === 1) {
        textEl.textContent = selected[0].label;
        textEl.classList.add('has-val');
        countEl.classList.remove('show');
    } else {
        textEl.textContent = selected.length + ' selected';
        textEl.classList.add('has-val');
        countEl.textContent = selected.length;
        countEl.classList.add('show');
    }
}

function closeAllMsDropdowns() {
    document.querySelectorAll('.ms-dropdown.open').forEach(d => d.classList.remove('open'));
    document.querySelectorAll('.ms-trigger.open').forEach(t => t.classList.remove('open'));
    document.querySelectorAll('.ms-wrap.ms-open').forEach(w => w.classList.remove('ms-open'));
    const fp = document.getElementById('shFilterPanel');
    if (fp) fp.classList.remove('ms-dd-open');
}

function toggleDropdown(id) {
    const dd = document.getElementById(id + '-dd');
    const wrap = document.getElementById(id);
    const trigger = document.querySelector(`#${id} .ms-trigger`);
    const isOpen = dd.classList.contains('open');

    closeAllMsDropdowns();

    if (!isOpen) {
        dd.classList.add('open');
        trigger.classList.add('open');
        if (wrap) wrap.classList.add('ms-open');
        const fp = document.getElementById('shFilterPanel');
        if (fp) fp.classList.add('ms-dd-open');
        const search = dd.querySelector('.ms-search');
        if (search) { search.value = ''; filterOptions(id, ''); setTimeout(() => search.focus(), 50); }
    }
}

document.addEventListener('click', e => {
    if (!e.target.closest('.ms-wrap')) closeAllMsDropdowns();
});

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ════════════════════════════════════════════════════════════
   ACTIVE CHIPS & FILTER COUNT
════════════════════════════════════════════════════════════ */
function renderChips() {
    const container = document.getElementById('activeChips');
    const chips = [];

    ['ms-upload','ms-mid','ms-branch'].forEach(id => {
        const sel = msState[id].filter(it => it.selected);
        sel.forEach(item => {
            chips.push({ id, value: item.value, label: item.label, type: 'group' });
        });
    });

    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo   = document.getElementById('filterDateTo').value;
    if (dateFrom) chips.push({ id: 'date_from', value: dateFrom, label: 'From: ' + dateFrom, type: 'date' });
    if (dateTo)   chips.push({ id: 'date_to',   value: dateTo,   label: 'To: '   + dateTo,   type: 'date' });

    const posTagEl = document.getElementById('filterBankPosTag');
    const posTag = posTagEl ? posTagEl.value : '';
    if (posTag === 'tagged') chips.push({ id: 'bank_pos_tag', value: 'tagged', label: 'Bank POS: tagged only', type: 'pos' });
    else if (posTag === 'untagged') chips.push({ id: 'bank_pos_tag', value: 'untagged', label: 'Bank POS: not tagged only', type: 'pos' });

    const zoneEl = document.getElementById('filterZoneId');
    const branchEl = document.getElementById('filterBranchId');
    if (zoneEl?.value) chips.push({ id: 'zone_id', value: zoneEl.value, label: 'Zone: ' + zoneEl.options[zoneEl.selectedIndex].text, type: 'group' });
    if (branchEl?.value) chips.push({ id: 'branch_id', value: branchEl.value, label: 'Branch: ' + branchEl.options[branchEl.selectedIndex].text, type: 'group' });

    const taggerEl = document.getElementById('filterPosTaggedBy');
    if (taggerEl?.value) chips.push({ id: 'pos_tagged_by', value: taggerEl.value, label: 'Tagged by: ' + taggerEl.options[taggerEl.selectedIndex].text, type: 'group' });

    container.innerHTML = chips.map(c => `
        <span class="fchip ${c.type === 'date' ? 'fchip-date' : 'fchip-group'}"
              onclick="removeChip('${c.id}','${escHtml(c.value)}')">
            ${escHtml(c.label)}
            <span class="fchip-remove"><i class="bi bi-x"></i></span>
        </span>
    `).join('');

    // Update badge
    const badge = document.getElementById('filterCountBadge');
    if (chips.length > 0) {
        badge.textContent = chips.length + ' active';
        badge.classList.add('show');
    } else {
        badge.classList.remove('show');
    }
}

function removeChip(id, value) {
    if (id === 'date_from') { document.getElementById('filterDateFrom').value = ''; }
    else if (id === 'date_to') { document.getElementById('filterDateTo').value = ''; }
    else if (id === 'bank_pos_tag') {
        const el = document.getElementById('filterBankPosTag');
        if (el) el.value = '';
    }
    else if (id === 'zone_id') {
        const z = document.getElementById('filterZoneId');
        const b = document.getElementById('filterBranchId');
        if (z) z.value = '';
        if (b) { b.value = ''; b.innerHTML = '<option value="">All branches</option>'; }
        loadFilterOptions(true);
    }
    else if (id === 'branch_id') {
        const b = document.getElementById('filterBranchId');
        if (b) b.value = '';
    }
    else if (id === 'pos_tagged_by') {
        const t = document.getElementById('filterPosTaggedBy');
        if (t) t.value = '';
    }
    else {
        const item = msState[id]?.find(it => String(it.value) === String(value));
        if (item) item.selected = false;
        renderOptions(id);
        updateTrigger(id);
    }
    renderChips();
    applyFilters();
}

/* ════════════════════════════════════════════════════════════
   LOAD FILTER OPTIONS
════════════════════════════════════════════════════════════ */
function populateLocationBranches(branches, selectedId) {
    const sel = document.getElementById('filterBranchId');
    if (!sel) return;
    sel.innerHTML = '<option value="">All branches</option>';
    (branches || []).forEach(b => {
        const opt = document.createElement('option');
        opt.value = String(b.id);
        opt.textContent = b.name;
        if (selectedId && String(selectedId) === String(b.id)) opt.selected = true;
        sel.appendChild(opt);
    });
}

function loadFilterOptions(branchesOnly) {
    const params = new URLSearchParams();
    const zoneSel = document.getElementById('filterZoneId');
    const zoneId = branchesOnly ? (zoneSel?.value || '') : '';
    if (zoneId) params.set('zone_id', zoneId);

    fetch('{{ route("settlement.api.filter-options") }}' + (params.toString() ? '?' + params : ''))
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            if (data.success === false) throw new Error(data.message || 'Filter options failed');

            bankPosLinkingAvailable = !!data.bank_pos_linking;
            const posWrap = document.getElementById('filterBankPosTagWrap');
            const tagWrap = document.getElementById('filterPosTaggedByWrap');
            if (posWrap) posWrap.style.display = bankPosLinkingAvailable ? '' : 'none';
            if (tagWrap) tagWrap.style.display = bankPosLinkingAvailable ? '' : 'none';

            if (branchesOnly) {
                populateLocationBranches(data.location_branches || []);
                return;
            }

            if (zoneSel && (data.zones || []).length) {
                const cur = zoneSel.value;
                zoneSel.innerHTML = '<option value="">All zones</option>';
                (data.zones || []).forEach(z => {
                    const opt = document.createElement('option');
                    opt.value = String(z.id);
                    opt.textContent = z.name;
                    if (cur && String(cur) === String(z.id)) opt.selected = true;
                    zoneSel.appendChild(opt);
                });
            }

            populateLocationBranches(data.location_branches || []);

            const taggerSel = document.getElementById('filterPosTaggedBy');
            if (taggerSel) {
                const curT = taggerSel.value;
                taggerSel.innerHTML = '<option value="">Anyone</option>';
                (data.pos_taggers || []).forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = String(u.id);
                    opt.textContent = u.name;
                    if (curT && String(curT) === String(u.id)) opt.selected = true;
                    taggerSel.appendChild(opt);
                });
            }

            populateMultiSelect('ms-upload', (data.uploads || []).map(u => ({ value: String(u.id), label: u.label })));
            populateMultiSelect('ms-mid', (data.mids || []).map(m => ({
                value: m.mid,
                label: m.label || (m.mid + ' — ' + (m.merchant_name || ''))
            })));
            populateMultiSelect('ms-branch', (data.branches || []).map(b => ({ value: b, label: b })));
            renderChips();
        })
        .catch(err => {
            console.error('loadFilterOptions', err);
            toast('Could not load filter options. Refresh the page.', 'error');
        });
}

document.getElementById('filterZoneId')?.addEventListener('change', function () {
    document.getElementById('filterBranchId').value = '';
    loadFilterOptions(true);
});

/* ════════════════════════════════════════════════════════════
   APPLY / RESET FILTERS
════════════════════════════════════════════════════════════ */
function applyFilters() {
    currentFilters = {};

    const uploadIds = msState['ms-upload'].filter(it => it.selected).map(it => it.value);
    const mids      = msState['ms-mid'].filter(it => it.selected).map(it => it.value);
    const branches  = msState['ms-branch'].filter(it => it.selected).map(it => it.value);
    const dateFrom  = document.getElementById('filterDateFrom').value;
    const dateTo    = document.getElementById('filterDateTo').value;

    if (uploadIds.length) currentFilters.upload_ids = uploadIds;
    if (mids.length)      currentFilters.mids        = mids;
    if (branches.length)  currentFilters.branches    = branches;
    if (dateFrom)         currentFilters.date_from   = dateFrom;
    if (dateTo)           currentFilters.date_to     = dateTo;

    const posTag = document.getElementById('filterBankPosTag')?.value || '';
    if (posTag === 'tagged' || posTag === 'untagged') currentFilters.bank_pos_tag = posTag;
    else delete currentFilters.bank_pos_tag;

    const zoneId = document.getElementById('filterZoneId')?.value || '';
    const branchId = document.getElementById('filterBranchId')?.value || '';
    if (zoneId) currentFilters.zone_id = zoneId; else delete currentFilters.zone_id;
    if (branchId) currentFilters.branch_id = branchId; else delete currentFilters.branch_id;

    const taggedBy = document.getElementById('filterPosTaggedBy')?.value || '';
    if (taggedBy) currentFilters.pos_tagged_by = [taggedBy]; else delete currentFilters.pos_tagged_by;

    renderChips();
    loadAccounts(1);
}

document.getElementById('btnApply').addEventListener('click', applyFilters);

document.getElementById('btnReset').addEventListener('click', () => {
    ['ms-upload','ms-mid','ms-branch'].forEach(id => { msState[id].forEach(it => it.selected = false); renderOptions(id); updateTrigger(id); });
    document.getElementById('filterDateFrom').value = '';
    document.getElementById('filterDateTo').value   = '';
    const posSel = document.getElementById('filterBankPosTag');
    if (posSel) posSel.value = '';
    const zSel = document.getElementById('filterZoneId');
    if (zSel) zSel.value = '';
    const bSel = document.getElementById('filterBranchId');
    if (bSel) bSel.innerHTML = '<option value="">All branches</option>';
    const tSel = document.getElementById('filterPosTaggedBy');
    if (tSel) tSel.value = '';
    currentFilters = {};
    loadFilterOptions();
    renderChips();
    loadAccounts(1);
});

// Also auto-apply on date change
document.getElementById('filterDateFrom').addEventListener('change', applyFilters);
document.getElementById('filterDateTo').addEventListener('change', applyFilters);
document.getElementById('filterBankPosTag')?.addEventListener('change', applyFilters);
document.getElementById('filterBranchId')?.addEventListener('change', applyFilters);
document.getElementById('filterPosTaggedBy')?.addEventListener('change', applyFilters);

/* ════════════════════════════════════════════════════════════
   LOAD ACCOUNTS TABLE
════════════════════════════════════════════════════════════ */
function loadAccounts(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('accountsTbody');
    tbody.innerHTML = `<tr><td colspan="13">
        <div class="skeleton" style="height:40px;margin:.5rem 1rem"></div>
        <div class="skeleton" style="height:40px;margin:.5rem 1rem"></div>
        <div class="skeleton" style="height:40px;margin:.5rem 1rem"></div>
    </td></tr>`;

    const params = new URLSearchParams({ page, per_page: document.getElementById('perPage').value });

    // Append array params
    if (currentFilters.upload_ids) currentFilters.upload_ids.forEach(v => params.append('upload_ids[]', v));
    if (currentFilters.mids)       currentFilters.mids.forEach(v => params.append('mids[]', v));
    if (currentFilters.branches)   currentFilters.branches.forEach(v => params.append('branches[]', v));
    if (currentFilters.date_from)  params.set('date_from', currentFilters.date_from);
    if (currentFilters.date_to)    params.set('date_to',   currentFilters.date_to);
    if (currentFilters.bank_pos_tag) params.set('bank_pos_tag', currentFilters.bank_pos_tag);
    if (currentFilters.zone_id) params.set('zone_id', currentFilters.zone_id);
    if (currentFilters.branch_id) params.set('branch_id', currentFilters.branch_id);
    if (currentFilters.pos_tagged_by) currentFilters.pos_tagged_by.forEach(v => params.append('pos_tagged_by[]', v));

    fetch(`{{ route("settlement.api.accounts") }}?${params}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { toast('Failed to load data.', 'error'); return; }

            const s = data.summary;
            bankPosLinkingAvailable = !!data.bank_pos_linking;
            const posWrap = document.getElementById('filterBankPosTagWrap');
            if (posWrap) posWrap.style.display = bankPosLinkingAvailable ? '' : 'none';
            if (!bankPosLinkingAvailable) {
                const sel = document.getElementById('filterBankPosTag');
                if (sel) sel.value = '';
                delete currentFilters.bank_pos_tag;
            }
            renderChips();

            document.getElementById('statAccounts').textContent = Number(s.total_accounts).toLocaleString();
            document.getElementById('statTxCount').textContent  = data.meta.total.toLocaleString();
            document.getElementById('statTxAmount').textContent = Number(s.total_transaction_amount).toLocaleString('en-IN',{minimumFractionDigits:2});
            document.getElementById('statNetAmount').textContent= Number(s.total_net_settlement_amount).toLocaleString('en-IN',{minimumFractionDigits:2});

            const posTW = document.getElementById('statPosTaggedWrap');
            const posUW = document.getElementById('statPosUntaggedWrap');
            if (data.bank_pos_linking && s.bank_pos_tagged != null && s.bank_pos_untagged != null) {
                if (posTW) { posTW.style.display = ''; document.getElementById('statPosTagged').textContent = Number(s.bank_pos_tagged).toLocaleString(); }
                if (posUW) { posUW.style.display = ''; document.getElementById('statPosUntagged').textContent = Number(s.bank_pos_untagged).toLocaleString(); }
            } else {
                if (posTW) posTW.style.display = 'none';
                if (posUW) posUW.style.display = 'none';
            }

            const showPosCols = !!data.bank_pos_linking;
            ['thPosTaggedBy','thPosTaggedAt','thPosStatus'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = showPosCols ? '' : 'none';
            });
            const emptyCols = showPosCols ? 16 : 13;

            if (data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${emptyCols}"><div class="empty-state"><i class="bi bi-inbox"></i><p>No records found for the selected filters.</p></div></td></tr>`;
                document.getElementById('paginationBar').style.display = 'none';
                return;
            }

            const offset = (data.meta.current_page - 1) * data.meta.per_page;
            tbody.innerHTML = data.data.map((a, i) => {
                let posCell = '<span style="color:var(--text3);font-size:.75rem;">—</span>';
                let byCell = '—';
                let atCell = '—';
                let statusCell = '—';
                if (a.bank_pos_linking) {
                    if (a.bank_pos_tag_status === 'tagged') {
                        const extra = (a.bank_pos_link_count > 1 ? a.bank_pos_link_count + ' bank lines' : '1 bank line')
                            + (a.bank_pos_statement_id ? ' · #' + a.bank_pos_statement_id : '');
                        posCell = `<span class="bdg bdg-green">Tagged</span><div style="font-size:.68rem;color:var(--text3);margin-top:4px;line-height:1.35;">${extra}</div>`;
                        byCell = a.bank_pos_matched_by || '—';
                        atCell = a.bank_pos_matched_at || '—';
                        statusCell = a.bank_pos_match_status
                            ? `<span class="bdg bdg-teal" style="font-size:.62rem;">${a.bank_pos_match_status}</span>`
                            : '—';
                    } else {
                        posCell = '<span class="bdg bdg-gray">Not tagged</span>';
                    }
                }
                const posExtraCols = showPosCols ? `
                    <td style="font-size:.73rem;color:var(--text2);max-width:120px;">${byCell}</td>
                    <td style="font-family:var(--mono);font-size:.7rem;color:var(--text3);white-space:nowrap;">${atCell}</td>
                    <td style="font-size:.7rem;">${statusCell}</td>` : '';
                return `
                <tr>
                    <td style="color:var(--text3);font-size:.75rem;font-family:var(--mono);">${offset + i + 1}</td>
                    <td><span class="mid-pill">${a.mid}</span></td>
                    <td style="font-family:var(--mono);font-size:.72rem;color:var(--text3);">${a.tid || '—'}</td>
                    <td>
                        <div style="font-weight:700;font-size:.82rem;">${a.merchant_name || '—'}</div>
                        <div style="font-size:.72rem;color:var(--text3);">${a.trading_name || ''}</div>
                    </td>
                    <td style="font-family:var(--mono);font-size:.78rem;">${a.transaction_date || '—'}</td>
                    <td style="font-family:var(--mono);font-size:.78rem;">${a.settlement_date || '—'}</td>
                    <td><span class="bdg bdg-green">${a.transaction_count}</span></td>
                    <td class="amt amt-neutral">₹${a.total_transaction_amount}</td>
                    <td style="font-family:var(--mono);font-size:.78rem;color:var(--rose);">₹${a.total_charges}</td>
                    <td style="font-family:var(--mono);font-size:.78rem;color:var(--amber2);">₹${a.total_taxes}</td>
                    <td class="amt amt-positive">₹${a.total_net_settlement_amount}</td>
                    <td style="min-width:118px;vertical-align:middle;">${posCell}</td>
                    ${posExtraCols}
                    <td><span class="bdg bdg-amber">${a.currency}</span></td>
                </tr>`;
            }).join('');

            renderPagination(data.meta);
        })
        .catch(() => toast('Error loading accounts.', 'error'));
}

/* ════════════════════════════════════════════════════════════
   PAGINATION
════════════════════════════════════════════════════════════ */
function renderPagination(meta) {
    const bar = document.getElementById('paginationBar');
    bar.style.display = 'flex';
    document.getElementById('pageInfo').textContent = `Showing ${meta.from ?? 0}–${meta.to ?? 0} of ${meta.total} records`;

    const cp = meta.current_page, lp = meta.last_page;
    let html = '';
    html += `<button class="pg-btn" onclick="loadAccounts(1)" ${cp===1?'disabled':''}><i class="bi bi-chevron-double-left"></i></button>`;
    html += `<button class="pg-btn" onclick="loadAccounts(${cp-1})" ${cp===1?'disabled':''}><i class="bi bi-chevron-left"></i></button>`;

    let pages = lp <= 7 ? Array.from({length:lp},(_,i)=>i+1) : [1];
    if (lp > 7) {
        if (cp > 3) pages.push('…');
        for (let p = Math.max(2,cp-1); p <= Math.min(lp-1,cp+1); p++) pages.push(p);
        if (cp < lp-2) pages.push('…');
        pages.push(lp);
    }
    pages.forEach(p => {
        if (p === '…') html += `<span class="pg-btn" style="cursor:default">…</span>`;
        else html += `<button class="pg-btn ${p===cp?'active':''}" onclick="loadAccounts(${p})">${p}</button>`;
    });
    html += `<button class="pg-btn" onclick="loadAccounts(${cp+1})" ${cp===lp?'disabled':''}><i class="bi bi-chevron-right"></i></button>`;
    html += `<button class="pg-btn" onclick="loadAccounts(${lp})" ${cp===lp?'disabled':''}><i class="bi bi-chevron-double-right"></i></button>`;
    document.getElementById('pageBtns').innerHTML = html;
}

document.getElementById('perPage').addEventListener('change', () => loadAccounts(1));

/* ════════════════════════════════════════════════════════════
   INIT
════════════════════════════════════════════════════════════ */
loadFilterOptions();
loadAccounts();
</script>

@include('superadmin.superadminfooter')
</body>
</html>
