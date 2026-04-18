<!doctype html>
<html lang="en">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════
   LOCATION MASTER — Design System
   Palette: Deep indigo + emerald + coral accent
═══════════════════════════════════════════════ */
:root{
  --lm-indigo:  #312e81;
  --lm-indigo2: #4338ca;
  --lm-indigo3: #6366f1;
  --lm-indigo-lt:#eef2ff;
  --lm-green:   #059669;
  --lm-green2:  #10b981;
  --lm-green-lt:#d1fae5;
  --lm-coral:   #f43f5e;
  --lm-coral-lt:#ffe4e6;
  --lm-amber:   #f59e0b;
  --lm-amber-lt:#fef3c7;
  --lm-surface: #ffffff;
  --lm-bg:      #f1f5f9;
  --lm-border:  #e2e8f0;
  --lm-text:    #0f172a;
  --lm-text2:   #475569;
  --lm-text3:   #94a3b8;
  --lm-radius:  16px;
  --lm-shadow:  0 4px 24px rgba(49,46,129,.09);
  --lm-font:    'Plus Jakarta Sans', sans-serif;
}
body { font-family: var(--lm-font); background: var(--lm-bg); }

/* ── HERO HEADER ── */
.lm-hero {
  background: linear-gradient(135deg, var(--lm-indigo) 0%, var(--lm-indigo2) 55%, #4f46e5 100%);
  border-radius: 20px;
  padding: 30px 32px;
  margin-bottom: 24px;
  position: relative;
  overflow: hidden;
}
.lm-hero::before {
  content:'';
  position:absolute; top:-80px; right:-80px;
  width:280px; height:280px; border-radius:50%;
  background:radial-gradient(circle,rgba(99,102,241,.22),transparent 70%);
}
.lm-hero::after {
  content:'';
  position:absolute; bottom:-50px; left:200px;
  width:200px; height:200px; border-radius:50%;
  background:radial-gradient(circle,rgba(16,185,129,.14),transparent 70%);
}
.lm-hero-inner { position:relative; z-index:1; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:14px; }
.lm-hero-left  { display:flex; align-items:center; gap:18px; }
.lm-hero-icon  {
  width:56px; height:56px;
  background:rgba(255,255,255,.12);
  border:1px solid rgba(255,255,255,.25);
  border-radius:16px;
  display:flex; align-items:center; justify-content:center;
  font-size:1.6rem;
}
.lm-hero h1 { font-size:1.45rem; font-weight:800; color:#fff; margin:0 0 3px; letter-spacing:-.4px; }
.lm-hero p  { font-size:.78rem; color:rgba(255,255,255,.6); margin:0; }
.lm-hero-actions { display:flex; gap:10px; align-items:center; }

/* ── STATS BAR ── */
.lm-stats-row { display:grid; grid-template-columns: repeat(auto-fill, minmax(150px,1fr)); gap:12px; margin-bottom:22px; }
.lm-stat {
  background:var(--lm-surface);
  border-radius:14px;
  padding:18px 20px;
  border:1px solid var(--lm-border);
  box-shadow:var(--lm-shadow);
  position:relative; overflow:hidden;
  transition:transform .15s,box-shadow .15s;
}
.lm-stat:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(49,46,129,.14); }
.lm-stat::after {
  content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:14px 14px 0 0;
}
.ls-indigo::after { background:linear-gradient(90deg,var(--lm-indigo3),#818cf8); }
.ls-green::after  { background:linear-gradient(90deg,var(--lm-green),var(--lm-green2)); }
.ls-amber::after  { background:linear-gradient(90deg,var(--lm-amber),#fbbf24); }
.ls-coral::after  { background:linear-gradient(90deg,var(--lm-coral),#fb7185); }
.lm-stat .si { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem; margin-bottom:10px; }
.ls-indigo .si { background:var(--lm-indigo-lt); color:var(--lm-indigo3); }
.ls-green  .si { background:var(--lm-green-lt);  color:var(--lm-green); }
.ls-amber  .si { background:var(--lm-amber-lt);  color:var(--lm-amber); }
.ls-coral  .si { background:var(--lm-coral-lt);  color:var(--lm-coral); }
.lm-stat .sv { font-size:1.4rem; font-weight:800; color:var(--lm-text); line-height:1; }
.lm-stat .sl { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--lm-text3); margin-top:3px; }

/* ── PANEL HEADERS ── */
.lm-panel {
  background:var(--lm-surface);
  border-radius:var(--lm-radius);
  border:1px solid var(--lm-border);
  box-shadow:var(--lm-shadow);
  overflow:hidden;
}
.lm-panel-hdr {
  padding:18px 20px;
  display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
  border-bottom:1px solid var(--lm-border);
}
.lm-panel-hdr-left { display:flex; align-items:center; gap:12px; }
.lm-panel-hdr-icon {
  width:40px; height:40px; border-radius:11px;
  display:flex; align-items:center; justify-content:center;
  font-size:1.1rem;
}
.phi-indigo { background:var(--lm-indigo-lt); color:var(--lm-indigo2); }
.phi-green  { background:var(--lm-green-lt);  color:var(--lm-green); }
.lm-panel-title { font-size:.95rem; font-weight:800; color:var(--lm-text); margin:0; }
.lm-panel-sub   { font-size:.7rem; color:var(--lm-text3); font-weight:500; margin-top:1px; }

/* ── ADD BUTTONS ── */
.lm-btn-add {
  display:inline-flex; align-items:center; gap:6px;
  padding:9px 18px; border-radius:10px; border:none;
  font-family:var(--lm-font); font-size:.8rem; font-weight:700;
  cursor:pointer; transition:all .15s; line-height:1;
}
.lba-indigo { background:linear-gradient(135deg,var(--lm-indigo2),var(--lm-indigo3)); color:#fff; box-shadow:0 4px 14px rgba(67,56,202,.3); }
.lba-indigo:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(67,56,202,.45); }
.lba-green  { background:linear-gradient(135deg,var(--lm-green),var(--lm-green2)); color:#fff; box-shadow:0 4px 14px rgba(5,150,105,.28); }
.lba-green:hover  { transform:translateY(-1px); box-shadow:0 6px 20px rgba(5,150,105,.42); }

/* ── ZONE CARDS LIST ── */
.zone-card-list { padding:14px 16px; display:flex; flex-direction:column; gap:8px; max-height:540px; overflow-y:auto; }
.zone-card-list::-webkit-scrollbar { width:4px; }
.zone-card-list::-webkit-scrollbar-thumb { background:var(--lm-border); border-radius:3px; }
.zone-item {
  display:flex; align-items:center; justify-content:space-between;
  padding:12px 16px; border-radius:12px;
  background:#f8fafc; border:1px solid var(--lm-border);
  transition:all .14s; cursor:default;
}
.zone-item:hover { background:var(--lm-indigo-lt); border-color:#c7d2fe; }
.zone-item-left { display:flex; align-items:center; gap:12px; }
.zone-num {
  width:28px; height:28px; border-radius:8px;
  background:var(--lm-indigo2); color:#fff;
  font-size:.7rem; font-weight:800;
  display:flex; align-items:center; justify-content:center;
  flex-shrink:0;
}
.zone-label { font-size:.85rem; font-weight:700; color:var(--lm-text); }
.zone-count-pill {
  font-size:.62rem; font-weight:700; padding:2px 8px; border-radius:20px;
  background:var(--lm-green-lt); color:var(--lm-green);
  border:1px solid #a7f3d0; margin-top:2px; display:inline-block;
}
.zone-actions { display:flex; gap:4px; }
.z-btn {
  width:30px; height:30px; border-radius:8px; border:1px solid transparent;
  display:flex; align-items:center; justify-content:center; cursor:pointer;
  font-size:.8rem; transition:all .12s; background:transparent;
}
.z-btn-edit  { color:var(--lm-indigo2); }
.z-btn-edit:hover  { background:var(--lm-indigo-lt); border-color:#c7d2fe; }
.z-btn-del   { color:var(--lm-coral); }
.z-btn-del:hover   { background:var(--lm-coral-lt); border-color:#fecdd3; }

.zone-empty { text-align:center; padding:48px 20px; color:var(--lm-text3); }
.zone-empty i { font-size:2.5rem; display:block; margin-bottom:10px; opacity:.35; }

/* ── LOCATIONS TABLE (DataTables custom) ── */
.lm-dt-body { padding:14px 16px 18px; }
.lm-dt-toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:12px; }
.lm-search-wrap { position:relative; }
.lm-search-wrap i { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:var(--lm-text3); font-size:.85rem; }
.lm-search-input {
  padding:8px 12px 8px 34px; border:1.5px solid var(--lm-border); border-radius:10px;
  font-size:.8rem; font-family:var(--lm-font); color:var(--lm-text); background:#fff; width:220px;
  transition:all .15s;
}
.lm-search-input:focus { outline:none; border-color:var(--lm-indigo3); box-shadow:0 0 0 3px rgba(99,102,241,.1); width:260px; }
.lm-zone-filter {
  padding:8px 12px; border:1.5px solid var(--lm-border); border-radius:10px;
  font-size:.8rem; font-family:var(--lm-font); color:var(--lm-text2); background:#fff; cursor:pointer;
  transition:border-color .15s;
}
.lm-zone-filter:focus { outline:none; border-color:var(--lm-indigo3); }

/* table inside DataTables */
#locationsTable thead th {
  background:#1e293b; color:rgba(255,255,255,.78);
  font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px;
  padding:11px 13px; white-space:nowrap; border:none;
}
#locationsTable tbody td { font-size:.79rem; color:var(--lm-text2); padding:11px 13px; vertical-align:middle; border-bottom:1px solid #f1f5f9; }
#locationsTable tbody tr:last-child td { border-bottom:none; }
#locationsTable tbody tr:hover td { background:#fafbfc; }

.loc-zone-badge {
  display:inline-block; padding:3px 10px; border-radius:20px;
  font-size:.65rem; font-weight:700;
  background:var(--lm-indigo-lt); color:var(--lm-indigo2);
  border:1px solid #c7d2fe;
}
.loc-name-cell { font-weight:700; color:var(--lm-text); }
.loc-act-wrap  { display:flex; gap:4px; }
.la-btn { width:30px; height:30px; border-radius:8px; border:1px solid transparent; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:.78rem; transition:all .12s; background:transparent; }
.la-btn-edit { color:var(--lm-indigo2); }
.la-btn-edit:hover { background:var(--lm-indigo-lt); border-color:#c7d2fe; }
.la-btn-del  { color:var(--lm-coral); }
.la-btn-del:hover  { background:var(--lm-coral-lt); border-color:#fecdd3; }

/* ── MODALS ── */
.lm-modal .modal-content { border:none; border-radius:18px; overflow:hidden; box-shadow:0 24px 64px rgba(15,23,42,.2); }
.lm-modal .modal-header  { padding:20px 24px; border-bottom:1px solid var(--lm-border); }
.lm-modal .modal-title   { font-size:1rem; font-weight:800; color:var(--lm-text); }
.lm-modal .modal-body    { padding:22px 24px; }
.lm-modal .modal-footer  { padding:14px 24px 20px; border-top:1px solid var(--lm-border); background:#f8fafc; }
.lm-modal .lm-label      { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--lm-text3); margin-bottom:5px; display:block; }
.lm-modal .lm-input, .lm-modal .lm-select {
  width:100%; padding:10px 14px; border:1.5px solid var(--lm-border); border-radius:10px;
  font-size:.85rem; font-family:var(--lm-font); color:var(--lm-text); background:#fff;
  transition:border-color .15s, box-shadow .15s;
}
.lm-modal .lm-input:focus, .lm-modal .lm-select:focus {
  outline:none; border-color:var(--lm-indigo3); box-shadow:0 0 0 3px rgba(99,102,241,.12);
}
.lm-modal .lm-err { font-size:.76rem; color:var(--lm-coral); padding:8px 12px; background:var(--lm-coral-lt); border-radius:8px; border:1px solid #fecdd3; }
.lm-btn-save {
  padding:10px 24px; border-radius:10px; border:none;
  background:linear-gradient(135deg,var(--lm-indigo2),var(--lm-indigo3));
  color:#fff; font-family:var(--lm-font); font-size:.85rem; font-weight:700; cursor:pointer;
  box-shadow:0 4px 14px rgba(67,56,202,.3); transition:all .15s;
}
.lm-btn-save:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(67,56,202,.45); }
.lm-btn-cancel {
  padding:10px 18px; border-radius:10px; border:1.5px solid var(--lm-border);
  background:#fff; color:var(--lm-text2); font-family:var(--lm-font); font-size:.84rem; font-weight:700; cursor:pointer;
  transition:all .12s;
}
.lm-btn-cancel:hover { border-color:var(--lm-coral); color:var(--lm-coral); }
.lm-btn-del {
  padding:10px 20px; border-radius:10px; border:none;
  background:linear-gradient(135deg,var(--lm-coral),#fb7185);
  color:#fff; font-family:var(--lm-font); font-size:.84rem; font-weight:700; cursor:pointer;
  box-shadow:0 4px 14px rgba(244,63,94,.3); transition:all .14s;
}
.lm-btn-del:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(244,63,94,.42); }

.del-modal-body { text-align:center; padding:28px 24px 20px; }
.del-icon-ring {
  width:64px; height:64px; border-radius:50%; margin:0 auto 14px;
  background:var(--lm-coral-lt); border:2px solid #fecdd3;
  display:flex; align-items:center; justify-content:center; font-size:1.8rem; color:var(--lm-coral);
}
.del-modal-body h6 { font-size:1rem; font-weight:800; color:var(--lm-text); margin-bottom:4px; }
.del-modal-body p  { font-size:.82rem; color:var(--lm-text2); margin:0; }

/* responsive */
@media(max-width:640px){
  .lm-stats-row { grid-template-columns: repeat(2,1fr); }
  .lm-search-input { width:100%; }
}
</style>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container"><div class="pc-content">
  <div style="padding:22px 18px;">

    {{-- BREADCRUMB --}}
    <ul class="breadcrumb mb-3" style="font-size:.78rem;">
      <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
      <li class="breadcrumb-item active">Location Master</li>
    </ul>

    {{-- HERO --}}
    <div class="lm-hero">
      <div class="lm-hero-inner">
        <div class="lm-hero-left">
          <div class="lm-hero-icon">🗺️</div>
          <div>
            <h1>Location Master</h1>
            <p>Manage zones and branches used across bills, reports &amp; reconciliation</p>
          </div>
        </div>
      </div>
    </div>

    {{-- STATS --}}
    @php
      $totalBranches = \App\Models\TblLocationModel::count();
      $activeBranches = \App\Models\TblLocationModel::where('status',1)->count();
      $inactiveBranches = $totalBranches - $activeBranches;
    @endphp
    <div class="lm-stats-row">
      <div class="lm-stat ls-indigo">
        <div class="si"><i class="bi bi-diagram-3"></i></div>
        <div class="sv">{{ $zones->count() }}</div>
        <div class="sl">Total Zones</div>
      </div>
      <div class="lm-stat ls-green">
        <div class="si"><i class="bi bi-geo-alt"></i></div>
        <div class="sv">{{ $totalBranches }}</div>
        <div class="sl">Total Branches</div>
      </div>
      <div class="lm-stat ls-amber">
        <div class="si"><i class="bi bi-check-circle"></i></div>
        <div class="sv">{{ $activeBranches }}</div>
        <div class="sl">Active</div>
      </div>
      <div class="lm-stat ls-coral">
        <div class="si"><i class="bi bi-x-circle"></i></div>
        <div class="sv">{{ $inactiveBranches }}</div>
        <div class="sl">Inactive</div>
      </div>
    </div>

    {{-- TWO-PANEL LAYOUT --}}
    @php $zoneBranchCount = \App\Models\TblLocationModel::selectRaw('zone_id, count(*) as cnt')->groupBy('zone_id')->pluck('cnt','zone_id'); @endphp
    <div class="row g-3 align-items-start">

      {{-- ZONES --}}
      <div class="col-lg-4">
        <div class="lm-panel">
          <div class="lm-panel-hdr">
            <div class="lm-panel-hdr-left">
              <div class="lm-panel-hdr-icon phi-indigo"><i class="bi bi-diagram-3-fill"></i></div>
              <div>
                <div class="lm-panel-title">Zones</div>
                <div class="lm-panel-sub">{{ $zones->count() }} zone{{ $zones->count()==1?'':'s' }}</div>
              </div>
            </div>
            <button type="button" class="lm-btn-add lba-indigo" id="btnAddZone">
              <i class="bi bi-plus-lg"></i> Add zone
            </button>
          </div>
          <div class="zone-card-list" id="zonesCardList">
            @forelse($zones as $i => $z)
            <div class="zone-item" data-zone-id="{{ $z->id }}">
              <div class="zone-item-left">
                <div class="zone-num">{{ $i + 1 }}</div>
                <div>
                  <div class="zone-label">{{ $z->name }}</div>
                  <span class="zone-count-pill">
                    {{ $zoneBranchCount->get($z->id, 0) }} branch{{ $zoneBranchCount->get($z->id, 0)==1?'':'es' }}
                  </span>
                </div>
              </div>
              <div class="zone-actions">
                <button type="button" class="z-btn z-btn-edit btn-edit-zone"
                  data-id="{{ $z->id }}" data-name="{{ e($z->name) }}" title="Edit zone">
                  <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="z-btn z-btn-del btn-del-zone"
                  data-id="{{ $z->id }}" data-name="{{ e($z->name) }}" title="Delete zone">
                  <i class="bi bi-trash3"></i>
                </button>
              </div>
            </div>
            @empty
            <div class="zone-empty">
              <i class="bi bi-diagram-3"></i>
              <p class="mb-0" style="font-size:.8rem;">No zones yet.<br>Click <strong>Add zone</strong> to create one.</p>
            </div>
            @endforelse
          </div>
        </div>
      </div>

      {{-- BRANCHES --}}
      <div class="col-lg-8">
        <div class="lm-panel">
          <div class="lm-panel-hdr">
            <div class="lm-panel-hdr-left">
              <div class="lm-panel-hdr-icon phi-green"><i class="bi bi-geo-alt-fill"></i></div>
              <div>
                <div class="lm-panel-title">Branches</div>
                <div class="lm-panel-sub">Filter by zone, search, edit or delete</div>
              </div>
            </div>
            <button type="button" class="lm-btn-add lba-green" id="btnAddLocation">
              <i class="bi bi-plus-lg"></i> Add branch
            </button>
          </div>
          <div class="lm-dt-body">
            <div class="lm-dt-toolbar">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="lm-search-wrap">
                  <i class="bi bi-search"></i>
                  <input type="text" class="lm-search-input" id="locSearch" placeholder="Search branch / zone…" autocomplete="off">
                </div>
                <select class="lm-zone-filter" id="filterZone">
                  <option value="">All zones</option>
                  @foreach($zones as $z)
                    <option value="{{ $z->id }}">{{ $z->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="table-responsive">
              <table id="locationsTable" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Zone</th>
                    <th>Branch name</th>
                    <th>Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div></div>

{{-- ═══ ZONE MODAL ═══ --}}
<div class="modal fade lm-modal" id="zoneModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:36px;height:36px;border-radius:10px;background:var(--lm-indigo-lt);color:var(--lm-indigo2);display:flex;align-items:center;justify-content:center;font-size:1rem;">
            <i class="bi bi-diagram-3-fill"></i>
          </div>
          <div class="modal-title" id="zoneModalTitle">Add zone</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="zoneModalErrors" class="lm-err d-none mb-3"></div>
        <input type="hidden" id="zoneEditId" value="">
        <label class="lm-label">Zone name <span style="color:var(--lm-coral)">*</span></label>
        <input type="text" class="lm-input" id="zoneNameInput" maxlength="191" placeholder="e.g. Bangalore South">
      </div>
      <div class="modal-footer">
        <button type="button" class="lm-btn-cancel" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="lm-btn-save" id="btnSaveZone">
          <i class="bi bi-check2-circle me-1"></i> Save zone
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ═══ BRANCH MODAL ═══ --}}
<div class="modal fade lm-modal" id="locationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:36px;height:36px;border-radius:10px;background:var(--lm-green-lt);color:var(--lm-green);display:flex;align-items:center;justify-content:center;font-size:1rem;">
            <i class="bi bi-geo-alt-fill"></i>
          </div>
          <div class="modal-title" id="locationModalTitle">Add branch</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="locationModalErrors" class="lm-err d-none mb-3"></div>
        <input type="hidden" id="locationEditId" value="">
        <div class="mb-3">
          <label class="lm-label">Zone <span style="color:var(--lm-coral)">*</span></label>
          <select class="lm-select" id="locZoneSelect">
            @forelse($zones as $z)
              <option value="{{ $z->id }}">{{ $z->name }}</option>
            @empty
              <option value="">— Add a zone first —</option>
            @endforelse
          </select>
        </div>
        <div class="mb-3">
          <label class="lm-label">Branch / location name <span style="color:var(--lm-coral)">*</span></label>
          <input type="text" class="lm-input" id="locNameInput" maxlength="191" placeholder="As used in bills &amp; MIS">
        </div>
        <div class="mb-3">
          <label class="lm-label">Level <span style="color:var(--lm-coral)">*</span></label>
          <select class="lm-select" id="locLevelSelect" required>
              <option value="">Select Level 1 or 2</option>
              <option value="1">Level 1</option>
              <option value="2">Level 2</option>
          </select>
          <div class="small text-muted mt-1" style="font-size:.72rem;">Each branch uses one pack only. License Documents module follows this setting.</div>
        </div>
        <div class="mb-3">
          <label class="lm-label">Status</label>
          <select class="lm-select" id="locStatusSelect">
            <option value="1">✅ Active</option>
            <option value="0">⛔ Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="lm-btn-cancel" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="lm-btn-save" id="btnSaveLocation" style="background:linear-gradient(135deg,var(--lm-green),var(--lm-green2));box-shadow:0 4px 14px rgba(5,150,105,.28);">
          <i class="bi bi-check2-circle me-1"></i> Save branch
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ═══ DELETE ZONE MODAL ═══ --}}
<div class="modal fade lm-modal" id="deleteZoneModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body del-modal-body">
        <div class="del-icon-ring"><i class="bi bi-diagram-3"></i></div>
        <h6>Delete zone?</h6>
        <p>Zone <strong id="delZoneName"></strong> will be permanently removed.</p>
        <input type="hidden" id="delZoneId">
      </div>
      <div class="modal-footer justify-content-center gap-2" style="background:#fff;border-top:1px solid var(--lm-border);padding:14px;">
        <button type="button" class="lm-btn-cancel" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="lm-btn-del" id="btnConfirmDelZone">
          <i class="bi bi-trash3 me-1"></i> Delete
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ═══ DELETE BRANCH MODAL ═══ --}}
<div class="modal fade lm-modal" id="deleteLocModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body del-modal-body">
        <div class="del-icon-ring"><i class="bi bi-geo-alt"></i></div>
        <h6>Delete branch?</h6>
        <p>Branch <strong id="delLocName"></strong> will be permanently removed.</p>
        <input type="hidden" id="delLocId">
      </div>
      <div class="modal-footer justify-content-center gap-2" style="background:#fff;border-top:1px solid var(--lm-border);padding:14px;">
        <button type="button" class="lm-btn-cancel" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="lm-btn-del" id="btnConfirmDelLoc">
          <i class="bi bi-trash3 me-1"></i> Delete
        </button>
      </div>
    </div>
  </div>
</div>

@include('superadmin.superadminfooter')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
(function ($) {
  'use strict';
  var CSRF = $('meta[name="csrf-token"]').attr('content');
  var hasZones = @json($zones->count() > 0);
  var ROUTES = {
    locList:  @json(route('superadmin.locationmaster.locations.list')),
    zoneStore:@json(route('superadmin.locationmaster.zones.store')),
    zoneBase: @json(url('superadmin/location-master/zones')),
    locStore: @json(route('superadmin.locationmaster.locations.store')),
    locBase:  @json(url('superadmin/location-master/locations'))
  };

  toastr.options = { positionClass:'toast-top-right', timeOut:3000, progressBar:true, closeButton:true };
  $.ajaxSetup({ headers:{ 'X-CSRF-TOKEN': CSRF } });

  /* ── DataTable ── */
  var locTable = $('#locationsTable').DataTable({
    processing: true,
    serverSide: true,
    dom: 't<"d-flex justify-content-between align-items-center mt-2"ip>',
    language: { processing:'<span style="font-size:.8rem;color:#6366f1">Loading…</span>' },
    ajax: {
      url: ROUTES.locList,
      type: 'GET',
      data: function(d) {
        d.zone_id  = $('#filterZone').val();
        d.search.value = $('#locSearch').val();
      }
    },
    order: [[1,'asc']],
    columns: [
      { data:'DT_RowIndex', orderable:false, searchable:false, width:'40px' },
      {
        data:'zone_name', render:function(d){
          return '<span class="loc-zone-badge">' + d + '</span>';
        }
      },
      { data:'name', render:function(d){ return '<span class="loc-name-cell">' + d + '</span>'; } },
      { data:'level', orderable:false, searchable:false, width:'110px', render:function(h){ return h; } },
      { data:'status', orderable:false, searchable:false, render:function(h){ return h; } },
      {
        data:'id', orderable:false, searchable:false,
        render:function(id,t,row){
          var plain = row.name_plain || '';
          var safe  = $('<div/>').text(plain).html();
          return '<div class="loc-act-wrap">'
            + '<button type="button" class="la-btn la-btn-edit btn-edit-loc" data-id="'+id+'" title="Edit"><i class="bi bi-pencil"></i></button>'
            + '<button type="button" class="la-btn la-btn-del btn-del-loc" data-id="'+id+'" data-name="'+safe+'" title="Delete"><i class="bi bi-trash3"></i></button>'
            + '</div>';
        }
      }
    ]
  });

  /* custom search/filter triggers */
  var srTimer;
  $('#locSearch').on('input', function(){
    clearTimeout(srTimer);
    srTimer = setTimeout(function(){ locTable.ajax.reload(null,false); }, 420);
  });
  $('#filterZone').on('change', function(){ locTable.ajax.reload(null,false); });

  /* ── helpers ── */
  function showErrors($el, list) {
    if (!list || !list.length) { $el.addClass('d-none').html(''); return; }
    $el.removeClass('d-none').html(list.map(function(e){ return '<div>⚠ ' + e + '</div>'; }).join(''));
  }

  /* ══ ZONES ══ */
  $('#btnAddZone').on('click', function(){
    $('#zoneModalTitle').text('Add zone');
    $('#zoneEditId').val('');
    $('#zoneNameInput').val('');
    showErrors($('#zoneModalErrors'), []);
    $('#zoneModal').modal('show');
    setTimeout(function(){ $('#zoneNameInput').focus(); }, 350);
  });

  $(document).on('click','.btn-edit-zone', function(){
    $('#zoneModalTitle').text('Edit zone');
    $('#zoneEditId').val($(this).data('id'));
    $('#zoneNameInput').val($(this).data('name'));
    showErrors($('#zoneModalErrors'), []);
    $('#zoneModal').modal('show');
    setTimeout(function(){ $('#zoneNameInput').focus(); }, 350);
  });

  $('#btnSaveZone').on('click', function(){
    var btn  = $(this).prop('disabled', true);
    var id   = $('#zoneEditId').val();
    var name = $('#zoneNameInput').val().trim();
    if (!name) { showErrors($('#zoneModalErrors'),['Zone name is required.']); btn.prop('disabled',false); return; }
    var url  = id ? (ROUTES.zoneBase+'/'+id) : ROUTES.zoneStore;
    var data = { name:name, _token:CSRF };
    if (id) data._method = 'PUT';
    $.ajax({ url:url, type:'POST', data:data })
      .done(function(res){
        if (res.success){ $('#zoneModal').modal('hide'); toastr.success(res.message||'Saved.'); window.location.reload(); }
        else showErrors($('#zoneModalErrors'), res.errors||[res.message||'Error']);
      })
      .fail(function(xhr){
        var j=xhr.responseJSON;
        showErrors($('#zoneModalErrors'), j&&(j.errors?Object.values(j.errors).flat():[j.message])||['Request failed']);
      })
      .always(function(){ btn.prop('disabled',false); });
  });

  $(document).on('click','.btn-del-zone', function(){
    $('#delZoneId').val($(this).data('id'));
    $('#delZoneName').text($(this).data('name'));
    $('#deleteZoneModal').modal('show');
  });

  $('#btnConfirmDelZone').on('click', function(){
    var id  = $('#delZoneId').val();
    var btn = $(this).prop('disabled', true);
    $.ajax({ url:ROUTES.zoneBase+'/'+id, type:'POST', data:{_method:'DELETE',_token:CSRF} })
      .done(function(res){
        if (res.success){ $('#deleteZoneModal').modal('hide'); toastr.success(res.message||'Deleted.'); window.location.reload(); }
        else toastr.error(res.message||'Cannot delete.');
      })
      .fail(function(xhr){ var j=xhr.responseJSON; toastr.error((j&&j.message)?j.message:'Delete failed.'); })
      .always(function(){ btn.prop('disabled',false); });
  });

  /* ══ BRANCHES ══ */
  $('#btnAddLocation').on('click', function(){
    if (!hasZones){ toastr.warning('Create at least one zone before adding a branch.'); return; }
    $('#locationModalTitle').text('Add branch');
    $('#locationEditId').val('');
    $('#locNameInput').val('');
    $('#locLevelSelect').val('');
    $('#locStatusSelect').val('1');
    var fz = $('#filterZone').val();
    if (fz) $('#locZoneSelect').val(fz);
    showErrors($('#locationModalErrors'), []);
    $('#locationModal').modal('show');
    setTimeout(function(){ $('#locNameInput').focus(); }, 350);
  });

  $(document).on('click','.btn-edit-loc', function(){
    var id = $(this).data('id');
    $.get(ROUTES.locBase+'/'+id, function(res){
      if (!res.success){ toastr.error('Not found.'); return; }
      var L = res.location;
      $('#locationModalTitle').text('Edit branch');
      $('#locationEditId').val(L.id);
      $('#locZoneSelect').val(String(L.zone_id));
      $('#locNameInput').val(L.name);
      $('#locLevelSelect').val(String(L.level));
      $('#locStatusSelect').val(String(L.status));
      showErrors($('#locationModalErrors'), []);
      $('#locationModal').modal('show');
      setTimeout(function(){ $('#locNameInput').focus(); }, 350);
    });
  });

  $('#btnSaveLocation').on('click', function(){
    var btn = $(this).prop('disabled', true);
    var id  = $('#locationEditId').val();
    var payload = {
      zone_id: $('#locZoneSelect').val(),
      name:    $('#locNameInput').val().trim(),
      level:   $('#locLevelSelect').val(),
      status:  $('#locStatusSelect').val(),
      _token:  CSRF
    };
    if (!payload.name) { showErrors($('#locationModalErrors'),['Branch name is required.']); btn.prop('disabled',false); return; }
    if (!payload.level || (payload.level !== '1' && payload.level !== '2')) {
      showErrors($('#locationModalErrors'),['Select Level 1 or Level 2.']); btn.prop('disabled',false); return;
    }
    var url = id ? (ROUTES.locBase+'/'+id) : ROUTES.locStore;
    if (id) payload._method = 'PUT';
    $.ajax({ url:url, type:'POST', data:payload })
      .done(function(res){
        if (res.success){ $('#locationModal').modal('hide'); toastr.success(res.message||'Saved.'); locTable.ajax.reload(null,false); }
        else showErrors($('#locationModalErrors'), res.errors||[res.message]);
      })
      .fail(function(xhr){
        var j=xhr.responseJSON;
        showErrors($('#locationModalErrors'), j&&(j.errors?Object.values(j.errors).flat():[j.message])||['Request failed']);
      })
      .always(function(){ btn.prop('disabled',false); });
  });

  $(document).on('click','.btn-del-loc', function(){
    $('#delLocId').val($(this).data('id'));
    $('#delLocName').text($(this).data('name'));
    $('#deleteLocModal').modal('show');
  });

  $('#btnConfirmDelLoc').on('click', function(){
    var id  = $('#delLocId').val();
    var btn = $(this).prop('disabled', true);
    $.ajax({ url:ROUTES.locBase+'/'+id, type:'POST', data:{_method:'DELETE',_token:CSRF} })
      .done(function(res){
        if (res.success){ $('#deleteLocModal').modal('hide'); toastr.success(res.message||'Deleted.'); locTable.ajax.reload(null,false); }
        else toastr.error(res.message||'Cannot delete.');
      })
      .fail(function(xhr){ var j=xhr.responseJSON; toastr.error((j&&j.message)?j.message:'Delete failed.'); })
      .always(function(){ btn.prop('disabled',false); });
  });

  /* Enter key in modal inputs triggers save */
  $('#zoneNameInput').on('keydown', function(e){ if(e.key==='Enter') $('#btnSaveZone').trigger('click'); });
  $('#locNameInput').on('keydown', function(e){ if(e.key==='Enter') $('#btnSaveLocation').trigger('click'); });

})(window.jQuery);
</script>
</body>
</html>
