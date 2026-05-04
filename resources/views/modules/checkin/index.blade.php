<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
:root{
  --p:#1d4ed8;--ph:#1e40af;--ps:#dbeafe;--pk:#93c5fd;
  --live:#059669;--lives:#d1fae5;
  --local:#7c3aed;--locals:#ede9fe;
  --sub:#6b7280;--bdr:#e5e7eb;--txt:#111827;
  --bg:#f0f4ff;--card:#fff;
  --r:12px;--sh:0 1px 4px rgba(0,0,0,.07);
}
*{box-sizing:border-box;}
body{background:var(--bg)!important;}
.mw{padding:20px 22px;}

/* ── Page Header ─────────────────────────── */
.ph{
  background:linear-gradient(130deg,#1e3a8a 0%,#1d4ed8 55%,#3b82f6 100%);
  border-radius:16px;padding:22px 26px 18px;color:#fff;
  display:flex;align-items:flex-start;justify-content:space-between;
  flex-wrap:wrap;gap:14px;margin-bottom:18px;
  box-shadow:0 8px 28px rgba(29,78,216,.28);
  position:relative;overflow:hidden;
}
.ph::before{
  content:'';position:absolute;right:-60px;top:-60px;
  width:220px;height:220px;border-radius:50%;
  background:rgba(255,255,255,.06);
}
.ph::after{
  content:'';position:absolute;right:60px;bottom:-40px;
  width:140px;height:140px;border-radius:50%;
  background:rgba(255,255,255,.04);
}
.ph-left{position:relative;z-index:1;}
.ph-icon{
  width:46px;height:46px;border-radius:12px;
  background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);
  display:flex;align-items:center;justify-content:center;
  font-size:1.35rem;margin-bottom:10px;
}
.ph h4{font-size:1.18rem;font-weight:700;margin:0 0 3px;}
.ph p{font-size:.79rem;opacity:.78;margin:0;}
.ph-right{display:flex;flex-direction:column;align-items:flex-end;gap:8px;position:relative;z-index:1;}
.sdot-wrap{
  font-size:.72rem;background:rgba(255,255,255,.14);
  border-radius:20px;padding:4px 11px;
  color:rgba(255,255,255,.88);display:flex;align-items:center;gap:5px;
  border:1px solid rgba(255,255,255,.2);
}
.sdot{width:7px;height:7px;border-radius:50%;background:#34d399;
  display:inline-block;animation:blink 1.4s infinite;}
@keyframes blink{0%,100%{opacity:1;}50%{opacity:.3;}}
.src-tog{
  display:inline-flex;border-radius:9px;overflow:hidden;
  border:1px solid rgba(255,255,255,.28);
}
.src-tog button{
  padding:6px 16px;font-size:.78rem;font-weight:600;border:none;cursor:pointer;
  background:rgba(255,255,255,.1);color:rgba(255,255,255,.7);transition:all .2s;
}
.src-tog button.al{background:rgba(255,255,255,.95);color:#1d4ed8;}
.src-tog button.lo{background:rgba(255,255,255,.95);color:#7c3aed;}
.src-tog button:hover:not(.al):not(.lo){background:rgba(255,255,255,.2);color:#fff;}

/* ── Source Banner ───────────────────────── */
.sbanner{
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;
  padding:9px 15px;border-radius:9px;margin-bottom:14px;font-size:.79rem;font-weight:600;
}
.sbanner.live {background:var(--lives);color:var(--live);border:1px solid #6ee7b7;}
.sbanner.local{background:var(--locals);color:var(--local);border:1px solid #c4b5fd;}

/* ── Stats Row ───────────────────────────── */
.srow{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px;}
.sc{
  flex:1 1 160px;background:var(--card);border:1px solid var(--bdr);
  border-radius:var(--r);padding:13px 16px;box-shadow:var(--sh);
  display:flex;align-items:center;gap:12px;
  transition:transform .15s,box-shadow .15s;
}
.sc:hover{transform:translateY(-2px);box-shadow:0 4px 14px rgba(0,0,0,.1);}
.si{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
.si.b{background:var(--ps);color:var(--p);}
.si.g{background:#d1fae5;color:#059669;}
.si.a{background:#fef3c7;color:#b45309;}
.si.v{background:#ede9fe;color:#7c3aed;}
.sv{font-size:1.28rem;font-weight:700;line-height:1.1;color:var(--txt);}
.sl{font-size:.69rem;color:var(--sub);margin-top:1px;}

/* ── Filter Card ─────────────────────────── */
.fc{
  background:var(--card);border:1px solid var(--bdr);
  border-radius:var(--r);padding:16px 18px;
  box-shadow:var(--sh);margin-bottom:14px;
}
.fc-title{
  font-size:.7rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.6px;color:var(--sub);margin-bottom:12px;
  display:flex;align-items:center;gap:5px;
}
.frow{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.fg{display:flex;flex-direction:column;gap:4px;flex:1;min-width:160px;}
.fg.date-fg{flex:0 0 230px;}
.fl{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--sub);}
.fg input[type=text]{
  border:1px solid var(--bdr);border-radius:8px;padding:7px 11px;
  font-size:.83rem;width:100%;outline:none;background:#fff;
  transition:border-color .15s,box-shadow .15s;
}
.fg input[type=text]:focus{border-color:var(--p);box-shadow:0 0 0 3px var(--ps);}

/* ── Multi-Select ────────────────────────── */
.ms-wrap{position:relative;user-select:none;}
.ms-btn{
  width:100%;display:flex;align-items:center;justify-content:space-between;
  border:1px solid var(--bdr);border-radius:8px;padding:6px 10px;
  background:#fff;cursor:pointer;font-size:.83rem;min-height:36px;
  transition:border-color .15s,box-shadow .15s;
}
.ms-btn:hover{border-color:#9ca3af;}
.ms-wrap.open .ms-btn{border-color:var(--p);box-shadow:0 0 0 3px var(--ps);}
.ms-label{color:var(--txt);flex:1;text-align:left;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;}
.ms-label.placeholder{color:#9ca3af;}
.ms-arrow{color:var(--sub);font-size:.7rem;flex-shrink:0;transition:transform .2s;}
.ms-wrap.open .ms-arrow{transform:rotate(180deg);}
.ms-tags{display:flex;flex-wrap:wrap;gap:3px;align-items:center;flex:1;min-width:0;}
.ms-tag{
  display:inline-flex;align-items:center;gap:3px;
  background:var(--ps);color:var(--p);
  border-radius:5px;padding:1px 7px;font-size:.71rem;font-weight:600;
}
.ms-tag .rm{cursor:pointer;font-size:.7rem;opacity:.65;line-height:1;}
.ms-tag .rm:hover{opacity:1;}
.ms-dropdown{
  position:absolute;top:calc(100% + 5px);left:0;right:0;
  background:#fff;border:1px solid var(--bdr);border-radius:10px;
  box-shadow:0 10px 30px rgba(0,0,0,.13);z-index:9000;display:none;
}
.ms-wrap.open .ms-dropdown{display:block;}
.ms-search-inp{
  width:100%;border:none;border-bottom:1px solid var(--bdr);
  padding:9px 12px;font-size:.82rem;outline:none;border-radius:10px 10px 0 0;
}
.ms-actions{display:flex;gap:8px;padding:6px 10px;border-bottom:1px solid var(--bdr);}
.ms-act-btn{
  font-size:.72rem;padding:2px 9px;border:1px solid var(--bdr);
  border-radius:5px;background:#fff;cursor:pointer;color:var(--sub);
}
.ms-act-btn:hover{background:var(--ps);border-color:var(--p);color:var(--p);}
.ms-list{max-height:200px;overflow-y:auto;padding:4px 0;}
.ms-item{display:flex;align-items:center;gap:8px;padding:7px 12px;cursor:pointer;font-size:.82rem;transition:background .1s;}
.ms-item:hover{background:#f3f4f6;}
.ms-item input[type=checkbox]{width:15px;height:15px;accent-color:var(--p);cursor:pointer;flex-shrink:0;}
.ms-item label{cursor:pointer;flex:1;}
.ms-empty{padding:14px;text-align:center;font-size:.8rem;color:var(--sub);}

/* ── Buttons ─────────────────────────────── */
.btn-fetch{
  background:var(--p);color:#fff;border:none;border-radius:8px;
  padding:8px 20px;font-size:.83rem;font-weight:600;cursor:pointer;
  white-space:nowrap;display:flex;align-items:center;gap:6px;transition:background .2s,transform .1s;
}
.btn-fetch:hover{background:var(--ph);transform:translateY(-1px);}
.btn-fetch:active{transform:translateY(0);}
.btn-reset{
  background:#f9fafb;color:#374151;border:1px solid var(--bdr);
  border-radius:8px;padding:8px 12px;font-size:.83rem;cursor:pointer;transition:background .15s;
}
.btn-reset:hover{background:#f3f4f6;}
.btn-export{
  background:#fff;color:var(--p);border:1px solid var(--pk);
  border-radius:7px;padding:4px 11px;font-size:.76rem;font-weight:600;
  cursor:pointer;display:none;transition:all .15s;
}
.btn-export:hover{background:var(--ps);}

/* ── Filter History Bar ──────────────────── */
.fhbar{
  display:flex;align-items:center;flex-wrap:wrap;gap:6px;
  background:var(--card);border:1px solid var(--bdr);border-radius:var(--r);
  padding:9px 14px;margin-bottom:14px;box-shadow:var(--sh);
}
.fhlbl{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;
  color:var(--sub);white-space:nowrap;margin-right:2px;}
.fhchip{
  display:inline-flex;align-items:center;gap:5px;
  border-radius:20px;padding:3px 10px 3px 9px;font-size:.73rem;font-weight:600;
  cursor:default;border:1px solid transparent;
}
.fhchip.date  {background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}
.fhchip.zone  {background:#faf5ff;color:#6b21a8;border-color:#e9d5ff;}
.fhchip.branch{background:#fff7ed;color:#9a3412;border-color:#fed7aa;}
.fhchip.src   {background:#f1f5f9;color:#374151;border-color:#e2e8f0;}
.fhchip .xbtn{cursor:pointer;font-size:.78rem;line-height:1;opacity:.6;margin-left:2px;padding:0 2px;border-radius:50%;transition:all .15s;}
.fhchip .xbtn:hover{opacity:1;background:rgba(0,0,0,.1);}
.fhclear{margin-left:auto;font-size:.72rem;color:var(--sub);cursor:pointer;text-decoration:underline;white-space:nowrap;}
.fhclear:hover{color:#ef4444;}

/* ── Table Card ──────────────────────────── */
.tc{background:var(--card);border:1px solid var(--bdr);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;}
.ttbar{
  display:flex;align-items:center;justify-content:space-between;
  padding:13px 17px;border-bottom:1px solid var(--bdr);flex-wrap:wrap;gap:8px;
}
.ttbar h6{font-size:.9rem;font-weight:700;margin:0;color:var(--txt);}
.ttbar small{font-size:.73rem;color:var(--sub);}
.ttr{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.pp{display:flex;align-items:center;gap:5px;font-size:.77rem;color:var(--sub);}
.pp select{border:1px solid var(--bdr);border-radius:5px;padding:2px 5px;font-size:.77rem;}
.qsearch{
  border:1px solid var(--bdr);border-radius:7px;padding:5px 10px;
  font-size:.79rem;width:160px;display:none;outline:none;
  transition:border-color .15s,box-shadow .15s;
}
.qsearch:focus{border-color:var(--p);box-shadow:0 0 0 2px var(--ps);}

table.dt{width:100%;border-collapse:collapse;font-size:.83rem;}
table.dt thead tr{border-bottom:2px solid var(--bdr);}
table.dt thead th{
  background:#f8fafc;color:#4b5563;font-size:.68rem;font-weight:700;
  text-transform:uppercase;letter-spacing:.5px;padding:10px 14px;white-space:nowrap;
}
table.dt thead th:first-child{border-radius:0;}
table.dt tbody tr{border-bottom:1px solid #f3f4f6;transition:background .12s;}
table.dt tbody tr.clickable{cursor:pointer;}
table.dt tbody tr.clickable:hover{background:#eef4ff;}
table.dt tbody td{padding:9px 14px;vertical-align:middle;}
.rno{color:var(--sub);font-size:.7rem;font-weight:600;}
.bp{display:inline-block;padding:2px 9px;border-radius:20px;font-size:.69rem;font-weight:600;}
.bp-op{background:#dbeafe;color:#1d4ed8;}
.bp-ip{background:#fce7f3;color:#9d174d;}
.bp-gn{background:#d1fae5;color:#065f46;}
.src-live{font-size:.69rem;background:var(--lives);color:var(--live);border-radius:20px;padding:1px 7px;font-weight:600;}
.src-local{font-size:.69rem;background:var(--locals);color:var(--local);border-radius:20px;padding:1px 7px;font-weight:600;}

.es{text-align:center;padding:55px 20px;color:var(--sub);}
.es i{font-size:2.2rem;display:block;margin-bottom:10px;opacity:.25;}
.es p{font-size:.85rem;margin:0;}

/* ── Pagination ──────────────────────────── */
.pgw{
  display:flex;align-items:center;justify-content:space-between;
  padding:11px 16px;border-top:1px solid var(--bdr);flex-wrap:wrap;gap:6px;
}
.pgi{font-size:.77rem;color:var(--sub);}
.pgb{display:flex;gap:3px;flex-wrap:wrap;}
.pgb button{
  border:1px solid var(--bdr);background:#fff;border-radius:6px;
  padding:3px 9px;font-size:.76rem;cursor:pointer;color:var(--txt);transition:all .12s;
}
.pgb button:hover{background:var(--ps);border-color:var(--p);color:var(--p);}
.pgb button.active{background:var(--p);border-color:var(--p);color:#fff;font-weight:700;}
.pgb button:disabled{opacity:.35;cursor:not-allowed;}

/* ── Loader ──────────────────────────────── */
.ldr{position:fixed;inset:0;background:rgba(15,23,42,.52);z-index:9999;display:none;align-items:center;justify-content:center;flex-direction:column;gap:12px;}
.ldr.show{display:flex;}
.ldr-ring{width:48px;height:48px;border:4px solid rgba(255,255,255,.2);border-top-color:#fff;border-radius:50%;animation:spin .65s linear infinite;}
.ldr p{color:#fff;font-size:.88rem;font-weight:600;background:rgba(0,0,0,.3);padding:6px 16px;border-radius:20px;}
@keyframes spin{to{transform:rotate(360deg);}}

/* ── Detail Modal ────────────────────────── */
.dmod-bg{
  position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:10000;
  display:none;align-items:center;justify-content:center;
  padding:20px;backdrop-filter:blur(4px);
}
.dmod-bg.show{display:flex;}
.dmod{
  background:#fff;border-radius:20px;width:100%;max-width:540px;
  box-shadow:0 28px 70px rgba(0,0,0,.24);
  animation:popIn .22s cubic-bezier(.34,1.46,.64,1);
  max-height:calc(100vh - 40px);overflow-y:auto;position:relative;
}
@keyframes popIn{from{transform:scale(.88);opacity:0;}to{transform:scale(1);opacity:1;}}
.dmod-hero{
  background:linear-gradient(130deg,#1e3a8a,#3b82f6);
  padding:22px 24px 26px;color:#fff;
  border-radius:20px 20px 0 0;position:relative;
}
.dmod-hero h5{font-size:1.18rem;font-weight:700;margin:0 0 3px;padding-right:36px;}
.dmod-hero .sub{font-size:.8rem;opacity:.78;}
.dmod-close{
  position:absolute;top:14px;right:16px;
  background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);color:#fff;
  width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1.05rem;
  display:flex;align-items:center;justify-content:center;transition:background .15s;
}
.dmod-close:hover{background:rgba(255,255,255,.32);}
.dmod-badges{display:flex;gap:6px;flex-wrap:wrap;margin-top:11px;}
.dbadge{
  display:inline-flex;align-items:center;gap:4px;
  padding:3px 11px;border-radius:20px;font-size:.72rem;font-weight:600;
  background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.25);
}
.dmod-body{padding:20px 24px 26px;}
.dmod-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.dmod-field{background:#f8fafc;border-radius:11px;padding:12px 14px;border:1px solid #f0f4f8;}
.dmod-field.full{grid-column:1/-1;}
.dmod-field .lbl{font-size:.64rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--sub);margin-bottom:5px;}
.dmod-field .val{font-size:.88rem;font-weight:600;color:var(--txt);word-break:break-word;}
.dmod-field .val.mono{font-family:monospace;font-size:.83rem;}
.dmod-divider{height:1px;background:var(--bdr);margin:14px 0;}
</style>

<body>
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')
<div class="pc-container">
<div class="pc-content mw">

<!-- Loader -->
<div class="ldr" id="ldr"><div class="ldr-ring"></div><p id="ldrMsg">Loading…</p></div>

<!-- Detail Modal -->
<div class="dmod-bg" id="dmodBg" onclick="closeDmod(event)">
  <div class="dmod" id="dmod">
    <div class="dmod-hero">
      <button class="dmod-close" onclick="closeDmodNow()"><i class="bi bi-x"></i></button>
      <h5 id="dm-name">Patient Name</h5>
      <div class="sub" id="dm-datetime"></div>
      <div class="dmod-badges" id="dm-badges"></div>
    </div>
    <div class="dmod-body">
      <div class="dmod-grid">
        <div class="dmod-field">
          <div class="lbl"><i class="bi bi-phone me-1"></i>Mobile</div>
          <div class="val" id="dm-mobile">—</div>
        </div>
        <div class="dmod-field">
          <div class="lbl"><i class="bi bi-cake2 me-1"></i>Date of Birth</div>
          <div class="val" id="dm-dob">—</div>
        </div>
        <div class="dmod-field">
          <div class="lbl"><i class="bi bi-geo-alt me-1"></i>City</div>
          <div class="val" id="dm-city">—</div>
        </div>
        <div class="dmod-field">
          <div class="lbl"><i class="bi bi-share me-1"></i>Patient Source</div>
          <div class="val" id="dm-ptsource">—</div>
        </div>
        <div class="dmod-field full">
          <div class="lbl"><i class="bi bi-hospital me-1"></i>MOC Doc Location</div>
          <div class="val" id="dm-location">—</div>
        </div>
        <div class="dmod-field full">
          <div class="lbl"><i class="bi bi-info-circle me-1"></i>Purpose</div>
          <div class="val" id="dm-purpose">—</div>
        </div>
        <div id="dm-extra" style="display:contents;"></div>
      </div>
    </div>
  </div>
</div>

<!-- Page Header -->
<div class="ph">
  <div class="ph-left">
    <div class="ph-icon"><i class="bi bi-clipboard-pulse"></i></div>
    <h4>Check-in Report</h4>
    <p>Patient check-in data — live from MOC Doc API or synced local database</p>
  </div>
  <div class="ph-right">
    <div class="sdot-wrap"><span class="sdot"></span><span id="syncTxt">Last sync: …</span></div>
    <div class="src-tog">
      <button id="btnLive" class="al" onclick="setSrc('live')"><i class="bi bi-wifi me-1"></i>Live</button>
      <button id="btnLocal" onclick="setSrc('local')"><i class="bi bi-database me-1"></i>Local</button>
    </div>
  </div>
</div>

<!-- Source Banner -->
<div class="sbanner live" id="srcBanner">
  <span id="bannerTxt"><i class="bi bi-wifi me-1"></i> Live mode — real-time from MOC Doc API</span>
  <span id="bannerCnt" style="font-size:.78rem;"></span>
</div>

<!-- Stats Row -->
<div class="srow" id="statsRow" style="display:none;">
  <div class="sc"><div class="si b"><i class="bi bi-people-fill"></i></div>
    <div><div class="sv" id="stTotal">0</div><div class="sl">Total Check-ins</div></div></div>
  <div class="sc"><div class="si g"><i class="bi bi-calendar-check"></i></div>
    <div><div class="sv" id="stDays">0</div><div class="sl">Days in Range</div></div></div>
  <div class="sc"><div class="si a"><i class="bi bi-tag-fill"></i></div>
    <div><div class="sv" id="stPurpose" style="font-size:.88rem;">—</div><div class="sl">Top Purpose</div></div></div>
  <div class="sc"><div class="si v"><i class="bi bi-share-fill"></i></div>
    <div><div class="sv" id="stSrc" style="font-size:.88rem;">—</div><div class="sl">Top Source</div></div></div>
</div>

<!-- Filter Card -->
<div class="fc">
  <div class="fc-title"><i class="bi bi-funnel me-1"></i>Filters</div>
  <div class="frow">

    <!-- Date Range -->
    <div class="fg date-fg">
      <span class="fl"><i class="bi bi-calendar3 me-1"></i>Date Range</span>
      <input type="text" id="dateRange" placeholder="DD/MM/YYYY – DD/MM/YYYY" autocomplete="off" readonly>
    </div>

    <!-- Zone -->
    <div class="fg">
      <span class="fl"><i class="bi bi-diagram-2 me-1"></i>Zone</span>
      <div class="ms-wrap" id="ms-zone">
        <div class="ms-btn" onclick="toggleMs('ms-zone')">
          <div class="ms-tags" id="tags-zone"><span class="ms-label placeholder">All Zones</span></div>
          <i class="bi bi-chevron-down ms-arrow"></i>
        </div>
        <div class="ms-dropdown">
          <input class="ms-search-inp" type="text" placeholder="Search zone…" oninput="filterMs('ms-zone',this.value)">
          <div class="ms-actions">
            <button class="ms-act-btn" onclick="msSelectAll('ms-zone')">All</button>
            <button class="ms-act-btn" onclick="msClear('ms-zone')">Clear</button>
          </div>
          <div class="ms-list" id="list-zone">
            @foreach($zones as $zone)
              <div class="ms-item" onclick="msToggle('ms-zone','{{ $zone->id }}','{{ addslashes($zone->name) }}',this)">
                <input type="checkbox" value="{{ $zone->id }}" data-label="{{ $zone->name }}" onclick="event.stopPropagation();msToggle('ms-zone','{{ $zone->id }}','{{ addslashes($zone->name) }}',this.closest('.ms-item'))">
                <label>{{ $zone->name }}</label>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <!-- Branch -->
    <div class="fg">
      <span class="fl"><i class="bi bi-hospital me-1"></i>Branch</span>
      <div class="ms-wrap" id="ms-branch">
        <div class="ms-btn" onclick="toggleMs('ms-branch')">
          <div class="ms-tags" id="tags-branch"><span class="ms-label placeholder">All Branches</span></div>
          <i class="bi bi-chevron-down ms-arrow"></i>
        </div>
        <div class="ms-dropdown">
          <input class="ms-search-inp" type="text" placeholder="Search branch…" oninput="filterMs('ms-branch',this.value)">
          <div class="ms-actions">
            <button class="ms-act-btn" onclick="msSelectAll('ms-branch')">All</button>
            <button class="ms-act-btn" onclick="msClear('ms-branch')">Clear</button>
          </div>
          <div class="ms-list" id="list-branch"><div class="ms-empty">Select a zone first</div></div>
        </div>
      </div>
    </div>

    <!-- Search (local mode only) -->
    <div class="fg" id="searchFg" style="display:none;">
      <span class="fl"><i class="bi bi-search me-1"></i>Search</span>
      <input type="text" id="srchInput" placeholder="Name or mobile…">
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:7px;align-items:flex-end;flex-shrink:0;">
      <button class="btn-fetch" id="btnFetch"><i class="bi bi-search"></i> Fetch</button>
      <button class="btn-reset" id="btnReset" title="Reset filters"><i class="bi bi-arrow-counterclockwise"></i></button>
    </div>

  </div>
</div>

<!-- Filter History Bar -->
<div class="fhbar" id="fhbar" style="display:none;"></div>

<!-- Table Card -->
<div class="tc">
  <div class="ttbar">
    <div>
      <h6 id="tblTitle">Check-in Records</h6>
      <small id="tblSub">Select filters and click <strong>Fetch</strong></small>
    </div>
    <div class="ttr">
      <input type="text" class="qsearch" id="qsearch" placeholder="Quick search…">
      <div class="pp">
        <span>Show</span>
        <select id="perPage">
          <option value="10">10</option>
          <option value="25" selected>25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <button class="btn-export" id="btnExport"><i class="bi bi-download me-1"></i>CSV</button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="dt">
      <thead>
        <tr>
          <th>#</th>
          <th>Date &amp; Time</th>
          <th>PHID</th>
          <th>Patient Name</th>
          <th>Mobile</th>
          <th>Age</th>
          <th>Gender</th>
          <th>Purpose</th>
          <th>Visit Type</th>
          <th>Doctor</th>
          <th>City / State</th>
          <th>Source</th>
          <th>OP No</th>
          <th>Location</th>
        </tr>
      </thead>
      <tbody id="tbody">
        <tr><td colspan="14">
          <div class="es">
            <i class="bi bi-funnel"></i>
            <p>Select filters and click <strong>Fetch</strong> to load data</p>
          </div>
        </td></tr>
      </tbody>
    </table>
  </div>
  <div class="pgw" id="pgw" style="display:none;">
    <div class="pgi" id="pgi"></div>
    <div class="pgb" id="pgb"></div>
  </div>
</div>

</div></div>
@include('superadmin.superadminfooter')
<script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
/* ══ MULTI-SELECT ENGINE (global — called by inline onclick) ══ */
var _ms = {};
['ms-zone','ms-branch'].forEach(function(id){ _ms[id] = new Set(); });

function toggleMs(id){
  document.querySelectorAll('.ms-wrap').forEach(function(el){ if(el.id!==id) el.classList.remove('open'); });
  document.getElementById(id).classList.toggle('open');
}
function msToggle(id, val, label, itemEl){
  var set=_ms[id], cb=itemEl.querySelector('input[type=checkbox]');
  if(set.has(val)){ set.delete(val); if(cb) cb.checked=false; itemEl.style.background=''; }
  else            { set.add(val);    if(cb) cb.checked=true;  itemEl.style.background='#eff6ff'; }
  _updateMsLabel(id);
  if(id==='ms-zone') _loadBranches();
}
function msSelectAll(id){
  document.querySelectorAll('#list-'+id.replace('ms-','')+' .ms-item').forEach(function(el){
    var cb=el.querySelector('input[type=checkbox]');
    if(cb && el.style.display!=='none'){ _ms[id].add(cb.value); cb.checked=true; el.style.background='#eff6ff'; }
  });
  _updateMsLabel(id);
  if(id==='ms-zone') _loadBranches();
}
function msClear(id){
  _ms[id].clear();
  document.querySelectorAll('#list-'+id.replace('ms-','')+' .ms-item').forEach(function(el){
    var cb=el.querySelector('input[type=checkbox]');
    if(cb){ cb.checked=false; el.style.background=''; }
  });
  _updateMsLabel(id);
  if(id==='ms-zone'){
    _ms['ms-branch'].clear(); _updateMsLabel('ms-branch');
    document.getElementById('list-branch').innerHTML='<div class="ms-empty">Select a zone first</div>';
  }
}
function msRemove(id, val){
  _ms[id].delete(val);
  var cb=document.querySelector('#list-'+id.replace('ms-','')+' input[value="'+CSS.escape(val)+'"]');
  if(cb){ cb.checked=false; cb.closest('.ms-item').style.background=''; }
  _updateMsLabel(id);
  if(id==='ms-zone') _loadBranches();
}
function filterMs(id, q){
  q=q.toLowerCase();
  document.querySelectorAll('#list-'+id.replace('ms-','')+' .ms-item').forEach(function(el){
    el.style.display=el.querySelector('label').textContent.toLowerCase().includes(q)?'':'none';
  });
}
function _getMsVals(id){ return Array.from(_ms[id]); }

function _updateMsLabel(id){
  var set=_ms[id];
  var ph={'ms-zone':'All Zones','ms-branch':'All Branches'};
  var $tags=$('#tags-'+id.replace('ms-',''));
  $tags.empty();
  if(!set.size){ $tags.html('<span class="ms-label placeholder">'+ph[id]+'</span>'); return; }
  if(set.size<=3){
    set.forEach(function(v){
      var lbl=$('#list-'+id.replace('ms-','')+' input[value="'+CSS.escape(v)+'"]').data('label')||v;
      $tags.append('<span class="ms-tag">'+_x(lbl)+'<span class="rm" onclick="event.stopPropagation();msRemove(\''+id+'\',\''+v.replace(/\\/g,'\\\\').replace(/'/g,"\\'")+'\')">×</span></span>');
    });
  } else {
    $tags.html('<span class="ms-label">'+set.size+' selected</span>');
  }
}

function _loadBranches(){
  var zIds=_getMsVals('ms-zone');
  _ms['ms-branch'].clear(); _updateMsLabel('ms-branch');
  var $l=$('#list-branch').html('<div class="ms-empty">Loading…</div>');
  if(!zIds.length){ $l.html('<div class="ms-empty">Select a zone first</div>'); return; }
  $.get(_R.branches,{zone_ids:zIds},function(data){
    $l.empty();
    if(!data.length){ $l.html('<div class="ms-empty">No branches found</div>'); return; }
    data.forEach(function(b){
      $l.append('<div class="ms-item" onclick="msToggle(\'ms-branch\',\''+b.id+'\',\''+b.name.replace(/'/g,"\\'")+'\',this)">'
        +'<input type="checkbox" value="'+b.id+'" data-label="'+b.name+'" onclick="event.stopPropagation();msToggle(\'ms-branch\',\''+b.id+'\',\''+b.name.replace(/'/g,"\\'")+'\',this.closest(\'.ms-item\'))">'
        +'<label>'+b.name+'</label></div>');
    });
  });
}

function _x(s){ return s?String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'):''; }

document.addEventListener('click',function(e){
  if(!e.target.closest('.ms-wrap')) document.querySelectorAll('.ms-wrap').forEach(function(el){ el.classList.remove('open'); });
});

/* ══ PAGE LOGIC ══ */
var _R = {};

$(function(){
'use strict';

_R = {
  fetch:    '{{ route("checkin.fetch") }}',
  branches: '{{ route("checkin.branches") }}',
  lastSync: '{{ route("checkin.last-sync") }}',
};
var R = _R;
var state = { page:1, perPage:25, total:0, lastPage:1, source:'live' };

/* Date Range Picker */
$('#dateRange').daterangepicker({
  opens:'left', locale:{ format:'DD/MM/YYYY' },
  startDate:moment().subtract(6,'days'), endDate:moment(), maxDate:moment(),
  ranges:{
    'Today':        [moment(),moment()],
    'Yesterday':    [moment().subtract(1,'days'),moment().subtract(1,'days')],
    'Last 7 Days':  [moment().subtract(6,'days'),moment()],
    'Last 30 Days': [moment().subtract(29,'days'),moment()],
    'This Month':   [moment().startOf('month'),moment().endOf('month')],
  }
});

/* Source Toggle */
function setSrc(src){
  state.source=src;
  if(src==='live'){
    $('#btnLive').addClass('al'); $('#btnLocal').removeClass('lo');
    $('#srcBanner').removeClass('local').addClass('live');
    $('#bannerTxt').html('<i class="bi bi-wifi me-1"></i> Live mode — real-time from MOC Doc API');
    $('#searchFg').hide();
  } else {
    $('#btnLocal').addClass('lo'); $('#btnLive').removeClass('al');
    $('#srcBanner').removeClass('live').addClass('local');
    $('#bannerTxt').html('<i class="bi bi-database me-1"></i> Local mode — synced database');
    $('#searchFg').show();
  }
}
window.setSrc=setSrc;

/* Last Sync */
$.get(R.lastSync,function(r){
  $('#syncTxt').text('Last sync: '+(r.last_sync ? moment(r.last_sync).format('DD MMM, HH:mm') : 'Never'));
});

/* Fetch */
$('#btnFetch').on('click',function(){ state.page=1; doFetch(); });
$('#perPage').on('change',function(){ state.perPage=+$(this).val(); state.page=1; doFetch(); });

function doFetch(){
  var dr=$('#dateRange').val();
  if(!dr){ alert('Please select a date range.'); return; }
  showLdr('Fetching check-in data…');
  $.get(R.fetch,{
    source:     state.source,
    date_range: dr,
    zone_ids:   _getMsVals('ms-zone'),
    branch_ids: _getMsVals('ms-branch'),
    page:       state.page,
    per_page:   state.perPage,
    search:     $('#srchInput').val(),
  })
  .done(function(r){
    hideLdr();
    if(!r.success){ showErr(r.message||'API error'); return; }
    renderTable(r); renderStats(r); renderPagination(r); renderFilterBar();
    $('#bannerCnt').text(r.total ? r.total.toLocaleString()+' records' : '');
    $('#qsearch').show(); $('#btnExport').toggle(r.total>0);
  })
  .fail(function(){ hideLdr(); showErr('Network error. Please try again.'); });
}

/* Table */
function renderTable(r){
  var $b=$('#tbody').empty();
  $('#tblTitle').text('Check-in — '+(r.location_name||'All Locations'));
  $('#tblSub').html(r.total.toLocaleString()+' records &nbsp;<span class="'
    +(r.source==='live'?'src-live"><i class="bi bi-wifi"></i> Live':'src-local"><i class="bi bi-database"></i> Local')
    +'</span>');
  if(!r.data.length){
    $b.html('<tr><td colspan="14"><div class="es"><i class="bi bi-inbox"></i><p>No check-in records found.</p></div></td></tr>'); return;
  }
  var off=(state.page-1)*state.perPage;
  r.data.forEach(function(row,i){
    var pc=/O\/P|OP|outpatient/i.test(row.purpose)?'bp-op':/I\/P|IP|inpatient/i.test(row.purpose)?'bp-ip':'bp-gn';
    var gIcon=row.gender==='F'?'<span style="color:#db2777;font-size:.72rem;">&#9792; F</span>'
             :row.gender==='M'?'<span style="color:#2563eb;font-size:.72rem;">&#9794; M</span>'
             :(row.gender?'<span style="font-size:.72rem;">'+_x(row.gender)+'</span>':'—');
    var cityState=(_x(row.city)||'')+(row.state&&row.state!==row.city?'<br><span style="font-size:.72rem;color:var(--sub);">'+_x(row.state)+'</span>':'');
    var $tr=$('<tr class="clickable">'
      +'<td><span class="rno">'+(off+i+1)+'</span></td>'
      +'<td style="white-space:nowrap;font-size:.79rem;">'+_x(row.datetime)+'</td>'
      +'<td style="font-size:.76rem;font-family:monospace;color:#6d28d9;">'+(_x(row.phid)||'—')+'</td>'
      +'<td><strong style="font-size:.83rem;">'+(_x(row.name)||'—')+'</strong></td>'
      +'<td style="font-size:.79rem;">'+(_x(row.mobile)||'—')+'</td>'
      +'<td style="font-size:.76rem;text-align:center;">'+(_x(row.age)||'—')+'</td>'
      +'<td style="text-align:center;">'+gIcon+'</td>'
      +'<td><span class="bp '+pc+'">'+(_x(row.purpose)||'N/A')+'</span></td>'
      +'<td style="font-size:.76rem;">'+(_x(row.visittype)||'—')+'</td>'
      +'<td style="font-size:.74rem;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="'+_x(row.bookeddr)+'">'+(_x(row.bookeddr)||'—')+'</td>'
      +'<td style="font-size:.79rem;">'+cityState+'</td>'
      +'<td style="font-size:.76rem;">'+(_x(row.ptsource)||'—')+'</td>'
      +'<td style="font-size:.76rem;text-align:center;">'+(_x(row.opno)||'—')+'</td>'
      +'<td style="font-size:.76rem;color:var(--sub);">'+(_x(row.location)||'—')+'</td>'
      +'</tr>');
    (function(d){ $tr.on('click',function(){ openDmod(d); }); })(row);
    $b.append($tr);
  });
  state.total=r.total; state.lastPage=r.last_page;
}

/* Stats */
function renderStats(r){
  if(!r.stats) return;
  var s=r.stats;
  $('#stTotal').text(s.total.toLocaleString());
  var drp=$('#dateRange').data('daterangepicker');
  $('#stDays').text(drp?drp.endDate.diff(drp.startDate,'days')+1:'—');
  var tp=Object.keys(s.purposes||{})[0]||'—', ts=Object.keys(s.sources||{})[0]||'—';
  $('#stPurpose').text(tp.length>14?tp.slice(0,12)+'…':tp);
  $('#stSrc').text(ts.length>14?ts.slice(0,12)+'…':ts);
  $('#statsRow').show();
}

/* Pagination */
function renderPagination(r){
  if(!r.total){ $('#pgw').hide(); return; }
  state.total=r.total; state.lastPage=r.last_page; state.page=r.page; state.perPage=r.per_page;
  var from=(state.page-1)*state.perPage+1, to=Math.min(state.page*state.perPage,state.total);
  $('#pgi').text('Showing '+from+'–'+to+' of '+state.total.toLocaleString());
  var $b=$('#pgb').empty();
  $b.append(mkP('‹',state.page<=1,function(){ state.page--; doFetch(); }));
  var s=Math.max(1,state.page-2), e=Math.min(state.lastPage,state.page+2);
  if(s>1) $b.append(mkP(1,false,function(){ state.page=1; doFetch(); }));
  if(s>2) $b.append($('<button>').text('…').prop('disabled',true));
  for(var p=s;p<=e;p++){
    (function(pg){ $b.append(mkP(pg,false,function(){ state.page=pg; doFetch(); },pg===state.page)); })(p);
  }
  if(e<state.lastPage-1) $b.append($('<button>').text('…').prop('disabled',true));
  if(e<state.lastPage) $b.append(mkP(state.lastPage,false,function(){ state.page=state.lastPage; doFetch(); }));
  $b.append(mkP('›',state.page>=state.lastPage,function(){ state.page++; doFetch(); }));
  $('#pgw').show();
}
function mkP(l,d,fn,a){ return $('<button>').text(l).prop('disabled',!!d).toggleClass('active',!!a).on('click',fn); }

/* Quick Search */
var qt;
$('#qsearch').on('input',function(){
  clearTimeout(qt); var q=$(this).val().toLowerCase();
  qt=setTimeout(function(){ $('#tbody tr').each(function(){ $(this).toggle(!q||$(this).text().toLowerCase().includes(q)); }); },200);
});

/* Export CSV */
$('#btnExport').on('click',function(){
  var dr=$('#dateRange').val(); if(!dr) return;
  showLdr('Preparing export…');
  $.get(R.fetch,{
    source:state.source,date_range:dr,
    zone_ids:_getMsVals('ms-zone'),branch_ids:_getMsVals('ms-branch'),
    page:1,per_page:1000
  },function(r){
    hideLdr(); if(!r.data||!r.data.length) return;
    var hdr=['#','Date & Time','PHID','Name','Mobile','DOB','Age','Gender','Purpose','Visit Type','Doctor','City','State','Source','OP No','Location'];
    dlCSV([hdr].concat(r.data.map(function(row,i){
      return [i+1,row.datetime,row.phid,row.name,row.mobile,row.dob,row.age,row.gender,row.purpose,row.visittype,row.bookeddr,row.city,row.state,row.ptsource,row.opno,row.location];
    })),'checkin_report');
  });
});

/* Reset */
$('#btnReset').on('click',function(){
  $('#dateRange').data('daterangepicker').setStartDate(moment().subtract(6,'days'));
  $('#dateRange').data('daterangepicker').setEndDate(moment());
  msClear('ms-zone'); msClear('ms-branch');
  $('#srchInput').val('');
  $('#tbody').html('<tr><td colspan="9"><div class="es"><i class="bi bi-funnel"></i><p>Select filters and click <strong>Fetch</strong> to load data</p></div></td></tr>');
  $('#tblTitle').text('Check-in Records'); $('#tblSub').text('');
  $('#statsRow').hide(); $('#pgw').hide(); $('#btnExport').hide(); $('#qsearch').hide().val('');
  $('#bannerCnt').text(''); $('#fhbar').hide().empty();
});

/* Filter History Bar */
function renderFilterBar(){
  var $bar=$('#fhbar').empty(), chips=[];
  var drp=$('#dateRange').data('daterangepicker');
  if(drp){
    var ds=drp.startDate.format('DD MMM'), de=drp.endDate.format('DD MMM');
    chips.push({cls:'date',icon:'bi-calendar3',label:ds+' — '+de,noRemove:true});
  }
  chips.push({cls:'src',icon:state.source==='live'?'bi-wifi':'bi-database',
    label:state.source==='live'?'Live':'Local',
    remove:function(){ setSrc(state.source==='live'?'local':'live'); doFetch(); }});
  _getMsVals('ms-zone').forEach(function(v){
    var lbl=$('#list-zone input[value="'+CSS.escape(v)+'"]').data('label')||v;
    chips.push({cls:'zone',icon:'bi-diagram-2',label:lbl,
      remove:function(){ msRemove('ms-zone',v); state.page=1; doFetch(); }});
  });
  _getMsVals('ms-branch').forEach(function(v){
    var lbl=$('#list-branch input[value="'+CSS.escape(v)+'"]').data('label')||v;
    chips.push({cls:'branch',icon:'bi-hospital',label:lbl,
      remove:function(){ msRemove('ms-branch',v); state.page=1; doFetch(); }});
  });
  if(!chips.length){ $bar.hide(); return; }
  $bar.append('<span class="fhlbl"><i class="bi bi-funnel-fill me-1"></i>Active Filters:</span>');
  chips.forEach(function(c){
    var $chip=$('<span class="fhchip '+c.cls+'"><i class="bi '+c.icon+' me-1"></i>'+_x(c.label)
      +(c.noRemove?'':'<span class="xbtn" title="Remove">×</span>')+'</span>');
    if(!c.noRemove){ (function(fn){ $chip.find('.xbtn').on('click',fn); })(c.remove); }
    $bar.append($chip);
  });
  $bar.append('<span class="fhclear" id="fhclearAll">Clear all</span>');
  $('#fhclearAll').on('click',function(){ msClear('ms-zone'); msClear('ms-branch'); state.page=1; doFetch(); });
  $bar.show();
}

/* Detail Modal */
function openDmod(row){
  $('#dm-name').text(row.name||'Unknown Patient');
  $('#dm-datetime').html('<i class="bi bi-clock me-1"></i>'+(row.datetime||'—'));
  var pc=/O\/P|OP|outpatient/i.test(row.purpose)?'bp-op':/I\/P|IP|inpatient/i.test(row.purpose)?'bp-ip':'bp-gn';
  $('#dm-badges').html(
    '<span class="dbadge"><i class="bi bi-tag me-1"></i>'+_x(row.purpose||'N/A')+'</span>'
    +(row.phid?'<span class="dbadge"><i class="bi bi-person-badge me-1"></i>'+_x(row.phid)+'</span>':'')
    +(row.visittype?'<span class="dbadge"><i class="bi bi-arrow-repeat me-1"></i>'+_x(row.visittype)+'</span>':'')
    +(row.city?'<span class="dbadge"><i class="bi bi-geo-alt me-1"></i>'+_x(row.city)+'</span>':'')
    +(state.source==='live'?'<span class="dbadge"><i class="bi bi-wifi me-1"></i>Live</span>':'<span class="dbadge"><i class="bi bi-database me-1"></i>Local</span>')
  );
  $('#dm-mobile').text(row.mobile||'—');
  $('#dm-dob').text(row.dob||'—');
  $('#dm-city').text((row.city||'—')+(row.state?' / '+row.state:''));
  $('#dm-ptsource').text(row.ptsource||'—');
  $('#dm-location').text(row.location||'—');
  $('#dm-purpose').html('<span class="bp '+pc+'" style="font-size:.82rem;padding:3px 10px;">'+_x(row.purpose||'N/A')+'</span>');
  // Extra fields
  var extra='';
  if(row.age)        extra+='<div class="dmod-field"><div class="lbl">Age</div><div class="val">'+_x(row.age)+'</div></div>';
  if(row.gender)     extra+='<div class="dmod-field"><div class="lbl">Gender</div><div class="val">'+_x(row.gender)+'</div></div>';
  if(row.bookeddr)   extra+='<div class="dmod-field full"><div class="lbl">Doctor</div><div class="val">'+_x(row.bookeddr)+'</div></div>';
  if(row.opno)       extra+='<div class="dmod-field"><div class="lbl">OP No</div><div class="val">'+_x(row.opno)+'</div></div>';
  if(row.state)      extra+='<div class="dmod-field"><div class="lbl">State</div><div class="val">'+_x(row.state)+'</div></div>';
  $('#dm-extra').html(extra);
  $('#dmodBg').addClass('show');
  $('body').css('overflow','hidden');
}
function closeDmod(e){ if($(e.target).is('#dmodBg')) closeDmodNow(); }
function closeDmodNow(){ $('#dmodBg').removeClass('show'); $('body').css('overflow',''); }
window.closeDmod=closeDmod; window.closeDmodNow=closeDmodNow;
$(document).on('keydown',function(e){ if(e.key==='Escape') closeDmodNow(); });

/* Helpers */
function showLdr(m){ $('#ldrMsg').text(m||'Loading…'); $('#ldr').addClass('show'); }
function hideLdr(){ $('#ldr').removeClass('show'); }
function showErr(m){ $('#tbody').html('<tr><td colspan="14"><div class="es text-danger"><i class="bi bi-exclamation-triangle"></i><p>'+m+'</p></div></td></tr>'); }
function dlCSV(rows,fn){
  var csv=rows.map(function(r){ return r.map(function(v){ return '"'+String(v==null?'':v).replace(/"/g,'""')+'"'; }).join(','); }).join('\n');
  var a=document.createElement('a');
  a.href=URL.createObjectURL(new Blob([csv],{type:'text/csv'}));
  a.download=fn+'_'+moment().format('YYYYMMDD')+'.csv'; a.click();
}

});// end document.ready
</script>
</body></html>
