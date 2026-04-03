<!doctype html>
<html lang="en">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
  --em-v1: #4c1d95; --em-v2: #6d28d9; --em-v3: #8b5cf6; --em-vlt: #ede9fe;
  --em-t1: #0d9488; --em-t2: #14b8a6; --em-tlt: #ccfbf1;
  --em-a1: #d97706; --em-alt: #fef3c7;
  --em-r1: #dc2626; --em-rlt: #fee2e2;
  --em-s: #fff; --em-bg: #f5f3ff; --em-bd: #e4e4e7;
  --em-tx: #18181b; --em-tx2: #52525b; --em-tx3: #a1a1aa;
  --em-rd: 14px; --em-sh: 0 4px 24px rgba(76,29,149,.09);
  --em-fn: 'Plus Jakarta Sans', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; }
body { font-family: var(--em-fn); background: var(--em-bg); }

/* HERO */
.em-hero {
  background: linear-gradient(135deg, var(--em-v1) 0%, var(--em-v2) 55%, #7c3aed 100%);
  border-radius: 20px; padding: 28px 32px; margin-bottom: 20px;
  position: relative; overflow: hidden;
}
.em-hero::before {
  content:''; position:absolute; top:-70px; right:-70px; width:260px; height:260px;
  border-radius:50%; background:radial-gradient(circle,rgba(139,92,246,.25),transparent 70%);
}
.em-hero::after {
  content:''; position:absolute; bottom:-40px; left:180px; width:180px; height:180px;
  border-radius:50%; background:radial-gradient(circle,rgba(20,184,166,.18),transparent 70%);
}
.em-hero-inner { position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; }
.em-hero-left  { display:flex; align-items:center; gap:18px; }
.em-hero-icon  { width:54px; height:54px; background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.3); border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; color:#fff; }
.em-hero h1 { font-size:1.4rem; font-weight:800; color:#fff; margin:0 0 3px; }
.em-hero p  { font-size:.77rem; color:rgba(255,255,255,.62); margin:0; }

/* STATS */
.em-stats { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:12px; margin-bottom:20px; }
.em-stat {
  background:var(--em-s); border-radius:14px; padding:16px 18px;
  border:1px solid var(--em-bd); box-shadow:var(--em-sh); position:relative; overflow:hidden;
}
.em-stat::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
.s-total::before   { background:var(--em-v3); }
.s-active::before  { background:var(--em-t2); }
.s-inactive::before{ background:var(--em-a1); }
.em-stat-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:.95rem; margin-bottom:9px; }
.s-total   .em-stat-icon { background:var(--em-vlt); color:var(--em-v2); }
.s-active  .em-stat-icon { background:var(--em-tlt); color:var(--em-t1); }
.s-inactive .em-stat-icon{ background:var(--em-alt); color:var(--em-a1); }
.em-stat-val { font-size:1.75rem; font-weight:800; color:var(--em-tx); line-height:1; }
.em-stat-lbl { font-size:.68rem; font-weight:700; color:var(--em-tx3); text-transform:uppercase; letter-spacing:.5px; margin-top:3px; }

/* TOOLBAR */
.em-toolbar { display:flex; align-items:center; gap:10px; margin-bottom:14px; flex-wrap:wrap; }
.em-search-wrap { position:relative; flex:1; min-width:200px; }
.em-search-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--em-tx3); font-size:.85rem; }
.em-search {
  width:100%; padding:9px 14px 9px 36px;
  border:1.5px solid var(--em-bd); border-radius:10px;
  font-size:.82rem; color:var(--em-tx); font-family:var(--em-fn);
  background:var(--em-s); outline:none; transition:border-color .2s;
}
.em-search:focus { border-color:var(--em-v3); }

/* Menu filter dropdown */
.em-filter-dd { position:relative; }
.em-filter-select {
  padding:8px 36px 8px 14px; border:1.5px solid var(--em-bd); border-radius:9px;
  font-size:.82rem; font-weight:600; font-family:var(--em-fn); color:var(--em-tx);
  background:var(--em-s); cursor:pointer; outline:none; appearance:none;
  min-width:200px; transition:border-color .2s;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 16 16'%3E%3Cpath fill='%23a1a1aa' d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
  background-repeat:no-repeat; background-position:right 12px center;
}
.em-filter-select:focus { border-color:var(--em-v3); box-shadow:0 0 0 3px rgba(139,92,246,.1); }
.em-filter-select.has-filter { border-color:var(--em-v2); background-color:var(--em-vlt); color:var(--em-v2); }

/* TABLE CARD */
.em-card { background:var(--em-s); border:1px solid var(--em-bd); border-radius:16px; box-shadow:var(--em-sh); overflow:hidden; }
.em-table { width:100%; border-collapse:collapse; font-size:.81rem; }
.em-table thead th { padding:11px 14px; text-align:left; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--em-tx3); background:#fafafa; border-bottom:1px solid var(--em-bd); white-space:nowrap; }
.em-table tbody td { padding:12px 14px; border-bottom:1px solid #f4f4f5; vertical-align:middle; color:var(--em-tx); }
.em-table tbody tr:last-child td { border-bottom:none; }
.em-table tbody tr:hover { background:#faf5ff; }

/* Menu badge */
.em-mbadge { display:inline-flex; align-items:center; gap:4px; padding:2px 9px; border-radius:20px; font-size:.68rem; font-weight:700; margin:1px; background:var(--em-vlt); color:var(--em-v2); border:1px solid #c4b5fd; }

/* CC / menu tags */
.em-tag { display:inline-flex; align-items:center; padding:2px 8px; border-radius:20px; font-size:.68rem; font-weight:600; margin:1px; background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
.em-more { display:inline-flex; align-items:center; padding:2px 8px; border-radius:20px; font-size:.68rem; font-weight:600; margin:1px; background:#f4f4f5; color:#666; }

/* Toggle */
.em-toggle { position:relative; width:40px; height:21px; cursor:pointer; }
.em-toggle input { opacity:0; width:0; height:0; }
.em-toggle-slider { position:absolute; inset:0; background:#d4d4d8; border-radius:21px; transition:background .25s; }
.em-toggle-slider::before { content:''; position:absolute; height:15px; width:15px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:transform .25s; box-shadow:0 1px 4px rgba(0,0,0,.18); }
.em-toggle input:checked + .em-toggle-slider { background:var(--em-t2); }
.em-toggle input:checked + .em-toggle-slider::before { transform:translateX(19px); }

/* Row action buttons */
.em-btn-icon { width:30px; height:30px; border-radius:8px; border:1px solid var(--em-bd); background:var(--em-s); cursor:pointer; font-size:.8rem; display:inline-flex; align-items:center; justify-content:center; transition:all .15s; }
.em-btn-icon.edit { color:var(--em-v2); }
.em-btn-icon.edit:hover { background:var(--em-vlt); border-color:var(--em-v3); }
.em-btn-icon.del  { color:var(--em-r1); }
.em-btn-icon.del:hover  { background:var(--em-rlt); border-color:var(--em-r1); }
.em-no-data { padding:50px; text-align:center; color:var(--em-tx3); font-size:.85rem; }
.em-no-data i { font-size:2.2rem; display:block; margin-bottom:8px; }

/* Row flash on update */
@keyframes rowFlash {
  0%   { background:#ede9fe; }
  60%  { background:#ede9fe; }
  100% { background:transparent; }
}
.row-updated { animation: rowFlash 1.6s ease forwards; }

/* Add button in hero */
.em-btn-add { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; background:rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.3); border-radius:10px; color:#fff; font-size:.82rem; font-weight:700; font-family:var(--em-fn); cursor:pointer; transition:background .15s; white-space:nowrap; }
.em-btn-add:hover { background:rgba(255,255,255,.26); }

/* ── DRAWER ── */
.em-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:1040; backdrop-filter:blur(2px); }
.em-overlay.open { display:block; }
.em-drawer {
  position:fixed; top:0; right:-500px; bottom:0; width:480px; max-width:98vw;
  background:var(--em-s); box-shadow:-8px 0 40px rgba(0,0,0,.15); z-index:1050;
  display:flex; flex-direction:column; transition:right .3s cubic-bezier(.4,0,.2,1); border-radius:16px 0 0 16px;
}
.em-drawer.open { right:0; }
.em-drawer-head {
  display:flex; align-items:center; justify-content:space-between; padding:20px 24px;
  background:linear-gradient(135deg,var(--em-v1),var(--em-v2)); border-radius:16px 0 0 0;
}
.em-drawer-head h5 { color:#fff; font-size:.98rem; font-weight:700; margin:0; }
.em-drawer-close { width:32px; height:32px; border-radius:8px; border:1px solid rgba(255,255,255,.25); background:rgba(255,255,255,.12); color:#fff; cursor:pointer; font-size:1rem; display:flex; align-items:center; justify-content:center; }
.em-drawer-close:hover { background:rgba(255,255,255,.22); }
.em-drawer-body { flex:1; overflow-y:auto; padding:22px 24px; }
.em-drawer-foot { padding:14px 24px; border-top:1px solid var(--em-bd); display:flex; gap:10px; justify-content:flex-end; }

/* Form elements */
.em-fg { margin-bottom:16px; }
.em-lbl { display:block; font-size:.72rem; font-weight:700; color:var(--em-tx2); margin-bottom:5px; text-transform:uppercase; letter-spacing:.4px; }
.em-lbl .req { color:var(--em-r1); margin-left:2px; }
.em-lbl .opt { color:var(--em-tx3); font-weight:400; font-size:.68rem; text-transform:none; }
.em-inp, .em-sel {
  width:100%; padding:9px 13px; border:1.5px solid var(--em-bd); border-radius:9px;
  font-size:.83rem; color:var(--em-tx); font-family:var(--em-fn);
  background:var(--em-s); outline:none; transition:border-color .2s, box-shadow .2s;
}
.em-inp:focus, .em-sel:focus { border-color:var(--em-v3); box-shadow:0 0 0 3px rgba(139,92,246,.1); }

/* ── MULTI-SELECT MENU PICKER ── */
.ms-menu-wrap {
  position:relative;
}
.ms-menu-trigger {
  width:100%; padding:9px 13px; border:1.5px solid var(--em-bd); border-radius:9px;
  font-size:.83rem; color:var(--em-tx); font-family:var(--em-fn); background:var(--em-s);
  outline:none; cursor:pointer; text-align:left; display:flex; align-items:center; justify-content:space-between;
  transition:border-color .2s;
}
.ms-menu-trigger:focus, .ms-menu-trigger.open { border-color:var(--em-v3); box-shadow:0 0 0 3px rgba(139,92,246,.1); }
.ms-menu-trigger .placeholder { color:var(--em-tx3); }
.ms-menu-trigger .count-badge { background:var(--em-v2); color:#fff; border-radius:20px; padding:1px 8px; font-size:.68rem; font-weight:700; }
.ms-menu-dropdown {
  position:absolute; top:calc(100% + 4px); left:0; right:0; z-index:9999;
  background:var(--em-s); border:1.5px solid var(--em-v3); border-radius:10px;
  box-shadow:0 8px 24px rgba(0,0,0,.12); display:none; max-height:240px; overflow-y:auto;
}
.ms-menu-dropdown.open { display:block; }
.ms-menu-search {
  padding:10px 12px; border-bottom:1px solid var(--em-bd);
  position:sticky; top:0; background:var(--em-s); z-index:1;
}
.ms-menu-search input {
  width:100%; border:1.5px solid var(--em-bd); border-radius:7px;
  padding:7px 10px; font-size:.8rem; font-family:var(--em-fn); outline:none;
}
.ms-menu-search input:focus { border-color:var(--em-v3); }
.ms-menu-acts {
  padding:7px 12px; border-bottom:1px solid var(--em-bd);
  display:flex; gap:8px; background:#fafafa;
}
.ms-menu-act-btn {
  padding:4px 12px; font-size:.72rem; font-weight:700; border-radius:6px;
  border:1px solid var(--em-bd); background:var(--em-s); cursor:pointer; color:var(--em-tx2);
}
.ms-menu-act-btn:hover { border-color:var(--em-v3); color:var(--em-v2); }
.ms-menu-opt {
  display:flex; align-items:center; gap:9px; padding:9px 14px;
  cursor:pointer; font-size:.82rem; color:var(--em-tx); transition:background .12s;
}
.ms-menu-opt:hover { background:#faf5ff; }
.ms-menu-opt input[type=checkbox] { accent-color:var(--em-v2); width:15px; height:15px; cursor:pointer; }
.ms-menu-opt.selected { background:var(--em-vlt); }
.ms-menu-no-results { padding:14px; text-align:center; color:var(--em-tx3); font-size:.8rem; }

/* Selected menu tags below picker */
.ms-menu-tags { display:flex; flex-wrap:wrap; gap:5px; margin-top:7px; min-height:0; }
.ms-menu-tag {
  display:inline-flex; align-items:center; gap:4px; padding:3px 10px;
  background:var(--em-vlt); color:var(--em-v2); border:1px solid #c4b5fd;
  border-radius:20px; font-size:.71rem; font-weight:700;
}
.ms-menu-tag button { background:none; border:none; cursor:pointer; color:var(--em-v2); font-size:.8rem; padding:0; line-height:1; }

/* CC tag input */
.cc-wrap {
  border:1.5px solid var(--em-bd); border-radius:9px; padding:7px 10px;
  display:flex; flex-wrap:wrap; gap:5px; cursor:text; min-height:44px;
  background:var(--em-s); transition:border-color .2s, box-shadow .2s;
}
.cc-wrap:focus-within { border-color:var(--em-v3); box-shadow:0 0 0 3px rgba(139,92,246,.1); }
.cc-tag-item { display:inline-flex; align-items:center; gap:4px; background:var(--em-tlt); color:var(--em-t1); border:1px solid #99f6e4; border-radius:20px; padding:3px 10px; font-size:.71rem; font-weight:600; }
.cc-tag-item button { background:none; border:none; cursor:pointer; color:var(--em-t1); font-size:.8rem; padding:0; }
.cc-tag-input { border:none; outline:none; font-size:.81rem; color:var(--em-tx); font-family:var(--em-fn); min-width:160px; flex:1; padding:2px 4px; background:transparent; }
.em-hint { font-size:.67rem; color:var(--em-tx3); margin-top:3px; }

/* Status pills */
.em-status-group { display:flex; gap:8px; }
.em-status-pill {
  flex:1; display:flex; align-items:center; justify-content:center; gap:6px;
  padding:9px; border-radius:9px; border:1.5px solid var(--em-bd);
  cursor:pointer; font-size:.8rem; font-weight:700; transition:all .18s;
  background:var(--em-s); color:var(--em-tx2);
}
.em-status-pill.on  { border-color:var(--em-t2); background:var(--em-tlt); color:var(--em-t1); }
.em-status-pill.off { border-color:var(--em-a1); background:var(--em-alt); color:var(--em-a1); }

/* Drawer buttons */
.em-btn-save   { padding:10px 22px; background:linear-gradient(135deg,var(--em-v2),var(--em-v3)); color:#fff; border:none; border-radius:9px; font-size:.84rem; font-weight:700; font-family:var(--em-fn); cursor:pointer; }
.em-btn-save:hover { opacity:.88; }
.em-btn-cancel { padding:10px 18px; background:var(--em-s); color:var(--em-tx2); border:1.5px solid var(--em-bd); border-radius:9px; font-size:.84rem; font-weight:600; font-family:var(--em-fn); cursor:pointer; }
.em-btn-cancel:hover { border-color:var(--em-r1); color:var(--em-r1); }

/* Divider in form */
.em-divider { border:none; border-top:1px dashed var(--em-bd); margin:18px 0; }

/* Mobile icon in table */
.mobile-val { display:flex; align-items:center; gap:5px; font-size:.78rem; }
.mobile-val i { color:var(--em-t1); }

@media(max-width:640px){
  .em-stats { grid-template-columns:1fr 1fr; }
  .em-drawer { width:100%; border-radius:16px 16px 0 0; top:auto; height:92vh; right:0; bottom:-92vh; }
  .em-drawer.open { bottom:0; }
}
</style>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    <div class="container-fluid px-3 py-3">

      {{-- HERO --}}
      <div class="em-hero mb-3">
        <div class="em-hero-inner">
          <div class="em-hero-left">
            <div class="em-hero-icon"><i class="bi bi-envelope-check"></i></div>
            <div>
              <h1>Email Master</h1>
              <p>Configure TO, CC &amp; mobile recipients per menu module with active / inactive control</p>
            </div>
          </div>
          <button class="em-btn-add" onclick="openDrawer()">
            <i class="bi bi-plus-lg"></i> Add Email Config
          </button>
        </div>
      </div>

      {{-- STATS --}}
      <div class="em-stats">
        <div class="em-stat s-total">
          <div class="em-stat-icon"><i class="bi bi-envelope-paper"></i></div>
          <div class="em-stat-val" id="st-total">{{ $stats['total'] }}</div>
          <div class="em-stat-lbl">Total Configs</div>
        </div>
        <div class="em-stat s-active">
          <div class="em-stat-icon"><i class="bi bi-check-circle"></i></div>
          <div class="em-stat-val" id="st-active">{{ $stats['active'] }}</div>
          <div class="em-stat-lbl">Active</div>
        </div>
        <div class="em-stat s-inactive">
          <div class="em-stat-icon"><i class="bi bi-pause-circle"></i></div>
          <div class="em-stat-val" id="st-inactive">{{ $stats['inactive'] }}</div>
          <div class="em-stat-lbl">Inactive</div>
        </div>
      </div>

      {{-- TOOLBAR --}}
      <div class="em-toolbar">
        <div class="em-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" id="emSearch" class="em-search" placeholder="Search by label, email, menu, mobile…">
        </div>
        <div class="em-filter-dd">
          <select id="emMenuFilter" class="em-filter-select" onchange="onFilterChange(this)">
            <option value="all">All Menus</option>
            @foreach($menus as $m)
            <option value="{{ $m->menu_name }}">{{ $m->menu_name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- TABLE --}}
      <div class="em-card">
        <div style="overflow-x:auto;">
          <table class="em-table">
            <thead>
              <tr>
                <th style="width:40px">#</th>
                <th>Label / Config</th>
                <th>Menus</th>
                <th>To (Primary)</th>
                <th>CC Emails</th>
                <th>Mobile</th>
                <th style="width:80px">Status</th>
                <th style="width:80px">Updated</th>
                <th style="width:72px">Actions</th>
              </tr>
            </thead>
            <tbody id="emTableBody">
              @forelse($records as $i => $rec)
              @php
                $cc     = is_string($rec->cc_emails) ? json_decode($rec->cc_emails, true) : ($rec->cc_emails ?? []);
                $cc     = is_array($cc) ? $cc : [];
                $mts    = is_string($rec->menu_type) ? json_decode($rec->menu_type, true) : ($rec->menu_type ?? []);
                $mts    = is_array($mts) ? $mts : ($mts ? [$mts] : []);
                $search = strtolower(($rec->label ?? '') . ' ' . ($rec->to_email ?: $rec->email) . ' ' . implode(' ', $mts) . ' ' . ($rec->mobile_number ?? ''));
              @endphp
              <tr data-record-id="{{ $rec->id }}" data-menus="{{ implode(',', $mts) }}" data-search="{{ $search }}">
                <td style="color:var(--em-tx3);font-weight:600;">{{ $i + 1 }}</td>
                <td>
                  <div style="font-weight:700;">{{ $rec->label ?: '—' }}</div>
                  <div style="font-size:.68rem;color:var(--em-tx3);">by {{ $rec->created_by }}</div>
                </td>
                <td>
                  @forelse($mts as $mt)
                    <span class="em-mbadge"><i class="bi bi-tag"></i>{{ $mt }}</span>
                  @empty
                    <span style="color:var(--em-tx3);font-size:.75rem;">—</span>
                  @endforelse
                </td>
                <td>
                  <div style="display:flex;align-items:center;gap:5px;">
                    <i class="bi bi-envelope" style="color:var(--em-v3);font-size:.8rem;"></i>
                    <span style="font-weight:600;font-size:.8rem;">{{ $rec->to_email ?: $rec->email }}</span>
                  </div>
                </td>
                <td>
                  @if(count($cc) === 0)
                    <span style="color:var(--em-tx3);font-size:.73rem;">No CC</span>
                  @else
                    @foreach(array_slice($cc, 0, 2) as $c)
                      <span class="em-tag">{{ $c }}</span>
                    @endforeach
                    @if(count($cc) > 2)
                      <span class="em-more">+{{ count($cc) - 2 }}</span>
                    @endif
                  @endif
                </td>
                <td>
                  @if($rec->mobile_number)
                    <div class="mobile-val"><i class="bi bi-phone"></i>{{ $rec->mobile_number }}</div>
                  @else
                    <span style="color:var(--em-tx3);font-size:.73rem;">—</span>
                  @endif
                </td>
                <td>
                  <label class="em-toggle">
                    <input type="checkbox" class="toggle-status" data-id="{{ $rec->id }}" {{ $rec->status ? 'checked' : '' }}>
                    <span class="em-toggle-slider"></span>
                  </label>
                </td>
                <td style="font-size:.7rem;color:var(--em-tx3);">{{ $rec->updated_at ? $rec->updated_at->format('d M Y') : '' }}</td>
                <td>
                  <div style="display:flex;gap:4px;">
                    <button class="em-btn-icon edit" title="Edit" onclick="openEdit({{ $rec->id }})">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="em-btn-icon del" title="Delete" onclick="deleteRecord({{ $rec->id }})">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr id="emEmptyRow">
                <td colspan="9">
                  <div class="em-no-data">
                    <i class="bi bi-envelope-x"></i>
                    No email configurations yet. Click <strong>Add Email Config</strong> to get started.
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- OVERLAY --}}
<div class="em-overlay" id="emOverlay" onclick="closeDrawer()"></div>

{{-- DRAWER --}}
<div class="em-drawer" id="emDrawer">
  <div class="em-drawer-head">
    <h5 id="drawerTitle"><i class="bi bi-envelope-plus me-2"></i>Add Email Config</h5>
    <button class="em-drawer-close" onclick="closeDrawer()"><i class="bi bi-x-lg"></i></button>
  </div>

  <div class="em-drawer-body">
    <input type="hidden" id="editId">

    {{-- Label --}}
    <div class="em-fg">
      <label class="em-lbl">Config Label <span class="opt">(optional)</span></label>
      <input type="text" id="fLabel" class="em-inp" placeholder="e.g. PO Approval — Finance Team">
    </div>

    {{-- Multi-select Menu --}}
    <div class="em-fg">
      <label class="em-lbl">Menu(s) <span class="req">*</span></label>
      <div class="ms-menu-wrap" id="msMenuWrap">
        <button type="button" class="ms-menu-trigger" id="msMenuTrigger" onclick="toggleMenuDD()">
          <span id="msMenuPlaceholder" class="placeholder">Select menus…</span>
          <i class="bi bi-chevron-down" style="font-size:.75rem;"></i>
        </button>
        <div class="ms-menu-dropdown" id="msMenuDD">
          <div class="ms-menu-search">
            <input type="text" id="msMenuSearch" placeholder="Search menus…" oninput="filterMenuOpts(this.value)">
          </div>
          <div class="ms-menu-acts">
            <button class="ms-menu-act-btn" onclick="selectAllMenus()">Select All</button>
            <button class="ms-menu-act-btn" onclick="clearAllMenus()">Clear All</button>
          </div>
          <div id="msMenuOpts">
            @foreach($menus as $m)
            <label class="ms-menu-opt" data-name="{{ $m->menu_name }}" id="msOpt_{{ $m->id }}">
              <input type="checkbox" value="{{ $m->menu_name }}" class="ms-menu-cb" onchange="onMenuCheck()">
              {{ $m->menu_name }}
            </label>
            @endforeach
            <div class="ms-menu-no-results" id="msNoResults" style="display:none;">No menus found</div>
          </div>
        </div>
      </div>
      <div class="ms-menu-tags" id="msMenuTags"></div>
    </div>

    <hr class="em-divider">

    {{-- To Email --}}
    <div class="em-fg">
      <label class="em-lbl">To Email (Primary) <span class="req">*</span></label>
      <div style="position:relative;">
        <i class="bi bi-envelope" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--em-tx3);font-size:.85rem;"></i>
        <input type="email" id="fToEmail" class="em-inp" style="padding-left:34px;" placeholder="primary@example.com">
      </div>
    </div>

    {{-- CC Emails tag input --}}
    <div class="em-fg">
      <label class="em-lbl">CC Emails <span class="opt">(multiple)</span></label>
      <div class="cc-wrap" id="ccWrap" onclick="document.getElementById('ccInput').focus()">
        <input type="text" id="ccInput" class="cc-tag-input" placeholder="Type email &amp; press Enter or comma…">
      </div>
      <p class="em-hint"><i class="bi bi-info-circle"></i> Press <kbd>Enter</kbd> or <kbd>,</kbd> after each email</p>
    </div>

    {{-- Mobile Number --}}
    <div class="em-fg">
      <label class="em-lbl">Mobile Number <span class="opt">(optional)</span></label>
      <div style="position:relative;">
        <i class="bi bi-phone" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--em-tx3);font-size:.85rem;"></i>
        <input type="tel" id="fMobile" class="em-inp" style="padding-left:34px;" placeholder="+91 99999 99999" maxlength="20">
      </div>
    </div>

    <hr class="em-divider">

    {{-- Status --}}
    <div class="em-fg">
      <label class="em-lbl">Status <span class="req">*</span></label>
      <div class="em-status-group">
        <div class="em-status-pill on" id="pillOn" onclick="setStatus(1)">
          <i class="bi bi-check-circle-fill"></i> Active
        </div>
        <div class="em-status-pill" id="pillOff" onclick="setStatus(0)">
          <i class="bi bi-pause-circle"></i> Inactive
        </div>
      </div>
      <input type="hidden" id="fStatus" value="1">
    </div>

  </div>

  <div class="em-drawer-foot">
    <button class="em-btn-cancel" onclick="closeDrawer()">Cancel</button>
    <button class="em-btn-save" onclick="saveRecord()"><i class="bi bi-save me-1"></i>Save Config</button>
  </div>
</div>

<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
const CSRF      = '{{ csrf_token() }}';
const urlStore  = '{{ route("superadmin.emailmaster.store") }}';
const baseUrl = window.location.origin;

const urlDelete = (id) => `${baseUrl+'/hms/public'}/superadmin/email-master/${id}`;
const urlToggle = (id) => `${baseUrl+'/hms/public'}/superadmin/email-master/${id}/toggle`;
toastr.options = { positionClass: 'toast-top-right', timeOut: 3000 };

// HTML-escape helper used in buildRowCells
function esc(s) {
  return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Global record cache — populated server-side and kept in sync after AJAX
window.EM_RECORDS = @json($recordsMap ?? []);

/* ════════════════════════════════════
   MULTI-SELECT MENU DROPDOWN
════════════════════════════════════ */
let selectedMenus = [];

function toggleMenuDD() {
  const dd  = document.getElementById('msMenuDD');
  const btn = document.getElementById('msMenuTrigger');
  const open = dd.classList.toggle('open');
  btn.classList.toggle('open', open);
  if (open) document.getElementById('msMenuSearch').focus();
}

// Close if click outside
document.addEventListener('click', function(e) {
  const wrap = document.getElementById('msMenuWrap');
  if (wrap && !wrap.contains(e.target)) {
    document.getElementById('msMenuDD').classList.remove('open');
    document.getElementById('msMenuTrigger').classList.remove('open');
  }
});

function filterMenuOpts(q) {
  q = q.toLowerCase().trim();
  let visible = 0;
  document.querySelectorAll('.ms-menu-opt[data-name]').forEach(opt => {
    const match = opt.dataset.name.toLowerCase().includes(q);
    opt.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  document.getElementById('msNoResults').style.display = visible ? 'none' : '';
}

function onMenuCheck() {
  selectedMenus = Array.from(document.querySelectorAll('.ms-menu-cb:checked')).map(cb => cb.value);
  syncMenuUI();
}

function syncMenuUI() {
  // Update selected highlight
  document.querySelectorAll('.ms-menu-opt').forEach(opt => {
    const cb = opt.querySelector('.ms-menu-cb');
    opt.classList.toggle('selected', cb && cb.checked);
  });
  // Trigger button label
  const ph = document.getElementById('msMenuPlaceholder');
  if (selectedMenus.length === 0) {
    ph.innerHTML = '<span class="placeholder">Select menus…</span>';
  } else if (selectedMenus.length === 1) {
    ph.innerHTML = `<span>${selectedMenus[0]}</span>`;
  } else {
    ph.innerHTML = `<span class="count-badge">${selectedMenus.length} menus selected</span>`;
  }
  // Tags below
  renderMenuTags();
}

function renderMenuTags() {
  const wrap = document.getElementById('msMenuTags');
  wrap.innerHTML = selectedMenus.map((m, i) =>
    `<span class="ms-menu-tag">${m} <button type="button" onclick="removeMenu(${i})"><i class="bi bi-x"></i></button></span>`
  ).join('');
}

function removeMenu(i) {
  selectedMenus.splice(i, 1);
  // uncheck
  document.querySelectorAll('.ms-menu-cb').forEach(cb => {
    cb.checked = selectedMenus.includes(cb.value);
  });
  syncMenuUI();
}

function selectAllMenus() {
  document.querySelectorAll('.ms-menu-opt[style*="display: none"]'); // only visible
  document.querySelectorAll('.ms-menu-opt:not([style*="display: none"]) .ms-menu-cb').forEach(cb => cb.checked = true);
  selectedMenus = Array.from(document.querySelectorAll('.ms-menu-cb:checked')).map(cb => cb.value);
  syncMenuUI();
}

function clearAllMenus() {
  selectedMenus = [];
  document.querySelectorAll('.ms-menu-cb').forEach(cb => cb.checked = false);
  syncMenuUI();
}

function setSelectedMenus(arr) {
  selectedMenus = arr || [];
  document.querySelectorAll('.ms-menu-cb').forEach(cb => {
    cb.checked = selectedMenus.includes(cb.value);
  });
  syncMenuUI();
}

/* ════════════════════════════════════
   CC TAG INPUT
════════════════════════════════════ */
let ccTags = [];

function renderCcTags() {
  const wrap  = document.getElementById('ccWrap');
  const input = document.getElementById('ccInput');
  wrap.querySelectorAll('.cc-tag-item').forEach(t => t.remove());
  ccTags.forEach((email, i) => {
    const tag = document.createElement('span');
    tag.className = 'cc-tag-item';
    tag.innerHTML = `${email} <button type="button" onclick="removeCC(${i})"><i class="bi bi-x"></i></button>`;
    wrap.insertBefore(tag, input);
  });
}

function addCC(val) {
  val = val.trim().replace(/,+$/, '');
  if (!val) return;
  const rx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!rx.test(val)) { toastr.warning(`"${val}" is not a valid email`); return; }
  if (ccTags.includes(val)) { toastr.info('Already added'); return; }
  ccTags.push(val);
  renderCcTags();
}

function removeCC(i) { ccTags.splice(i, 1); renderCcTags(); }

document.getElementById('ccInput').addEventListener('keydown', function(e) {
  if (e.key === 'Enter' || e.key === ',') {
    e.preventDefault(); addCC(this.value); this.value = '';
  } else if (e.key === 'Backspace' && this.value === '' && ccTags.length) {
    ccTags.pop(); renderCcTags();
  }
});
document.getElementById('ccInput').addEventListener('blur', function() {
  if (this.value.trim()) { addCC(this.value); this.value = ''; }
});

/* ════════════════════════════════════
   STATUS PILLS
════════════════════════════════════ */
function setStatus(val) {
  document.getElementById('fStatus').value = val;
  document.getElementById('pillOn').classList.toggle('on',  val === 1);
  document.getElementById('pillOff').classList.toggle('off', val === 0);
}

/* ════════════════════════════════════
   DRAWER OPEN / CLOSE
════════════════════════════════════ */
function openDrawer() {
  document.getElementById('drawerTitle').innerHTML = '<i class="bi bi-envelope-plus me-2"></i>Add Email Config';
  document.getElementById('editId').value  = '';
  document.getElementById('fLabel').value  = '';
  document.getElementById('fToEmail').value = '';
  document.getElementById('fMobile').value  = '';
  document.getElementById('msMenuSearch').value = '';
  filterMenuOpts('');
  clearAllMenus();
  ccTags = []; renderCcTags();
  setStatus(1);
  document.getElementById('emOverlay').classList.add('open');
  document.getElementById('emDrawer').classList.add('open');
}

function openEdit(id) {
  const r = window.EM_RECORDS[id];
  if (!r) { toastr.error('Record not found — please refresh the page.'); return; }

  document.getElementById('drawerTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Email Config';
  document.getElementById('editId').value   = r.id;
  document.getElementById('fLabel').value   = r.label         || '';
  document.getElementById('fToEmail').value = r.to_email      || '';
  document.getElementById('fMobile').value  = r.mobile_number || '';

  document.getElementById('msMenuSearch').value = '';
  filterMenuOpts('');
  setSelectedMenus(Array.isArray(r.menu_types) ? r.menu_types : []);

  ccTags = Array.isArray(r.cc_emails) ? [...r.cc_emails] : [];
  renderCcTags();

  setStatus(r.status === 1 ? 1 : 0);
  document.getElementById('emOverlay').classList.add('open');
  document.getElementById('emDrawer').classList.add('open');
}

function closeDrawer() {
  document.getElementById('emOverlay').classList.remove('open');
  document.getElementById('emDrawer').classList.remove('open');
}

/* ════════════════════════════════════
   SAVE
════════════════════════════════════ */
function saveRecord() {
  const id      = document.getElementById('editId').value;
  const label   = document.getElementById('fLabel').value.trim();
  const toEmail = document.getElementById('fToEmail').value.trim();
  const mobile  = document.getElementById('fMobile').value.trim();
  const status  = parseInt(document.getElementById('fStatus').value);

  if (!toEmail) { toastr.error('Primary TO email is required'); return; }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(toEmail)) { toastr.error('Invalid TO email format'); return; }

  // flush any pending CC
  const ccPending = document.getElementById('ccInput').value.trim();
  if (ccPending) { addCC(ccPending); document.getElementById('ccInput').value = ''; }

  $.ajax({
    url:         urlStore,
    type:        'POST',
    contentType: 'application/json',
    data: JSON.stringify({
      _token:       CSRF,
      id:           id || null,
      label,
      to_email:     toEmail,
      cc_emails:    ccTags,
      menu_types:   selectedMenus,
      mobile_number: mobile,
      status,
    }),
    success(res) {
      if (res.success) {
        toastr.success(res.message);
        closeDrawer();
        if (id) {
          // Edit: patch just the changed row, no full rebuild
          const updated = (res.records || []).find(r => r.id == id);
          if (updated) {
            updateSingleRow(updated);
          } else {
            rebuildTable(res.records);
          }
        } else {
          // New record: full rebuild to include the new row
          rebuildTable(res.records);
        }
        updateStats(res.stats);
      } else {
        toastr.error(res.message || 'Error saving');
      }
    },
    error(xhr) {
      const err = xhr.responseJSON;
      if (err && err.errors) Object.values(err.errors).flat().forEach(m => toastr.error(m));
      else toastr.error('Server error. Please try again.');
    }
  });
}

/* ════════════════════════════════════
   DELETE
════════════════════════════════════ */
function deleteRecord(id) {
  if (!confirm('Delete this email config?')) return;
  $.ajax({
    url:     urlDelete(id),
    type:    'DELETE',
    headers: { 'X-CSRF-TOKEN': CSRF },
    success(res) {
      toastr.success(res.message);
      // Remove the row instantly, then renumber remaining rows
      const tr = document.querySelector(`#emTableBody tr[data-record-id="${id}"]`);
      if (tr) {
        tr.style.transition = 'opacity .3s';
        tr.style.opacity    = '0';
        setTimeout(() => {
          tr.remove();
          delete window.EM_RECORDS[id];
          // Renumber
          document.querySelectorAll('#emTableBody tr[data-record-id]').forEach((row, i) => {
            if (row.cells[0]) row.cells[0].textContent = i + 1;
          });
          const tbody = document.getElementById('emTableBody');
          if (!tbody.querySelector('tr[data-record-id]')) {
            tbody.innerHTML = `<tr><td colspan="9"><div class="em-no-data"><i class="bi bi-envelope-x"></i>No email configurations yet.</div></td></tr>`;
          }
        }, 300);
      }
      updateStats(res.stats);
    },
    error: () => toastr.error('Error deleting')
  });
}

/* ════════════════════════════════════
   TOGGLE STATUS
════════════════════════════════════ */
$(document).on('change', '.toggle-status', function() {
  const id  = $(this).data('id');
  const box = this;
  $.ajax({
    url:     urlToggle(id),
    type:    'PATCH',
    headers: { 'X-CSRF-TOKEN': CSRF },
    success(res) {
      toastr.success(res.message);
      box.checked = !!res.status;
      if (window.EM_RECORDS[id]) window.EM_RECORDS[id].status = res.status ? 1 : 0;
      updateStats(res.stats);
    },
    error() { toastr.error('Error toggling status'); box.checked = !box.checked; }
  });
});

/* ════════════════════════════════════
   ROW BUILDER  (shared helper)
════════════════════════════════════ */
function buildRowCells(r, idx) {
  const cc  = Array.isArray(r.cc_emails)  ? r.cc_emails  : [];
  const mts = Array.isArray(r.menu_types) ? r.menu_types : [];

  const ccHtml = cc.length === 0
    ? '<span style="color:var(--em-tx3);font-size:.73rem;">No CC</span>'
    : cc.slice(0,2).map(c=>`<span class="em-tag">${esc(c)}</span>`).join('') + (cc.length>2?`<span class="em-more">+${cc.length-2}</span>`:'');

  const mtsHtml = mts.length === 0
    ? '<span style="color:var(--em-tx3);font-size:.73rem;">—</span>'
    : mts.map(m=>`<span class="em-mbadge"><i class="bi bi-tag"></i>${esc(m)}</span>`).join('');

  const mobileHtml = r.mobile_number
    ? `<div class="mobile-val"><i class="bi bi-phone"></i>${esc(r.mobile_number)}</div>`
    : `<span style="color:var(--em-tx3);font-size:.73rem;">—</span>`;

  return `
    <td style="color:var(--em-tx3);font-weight:600;">${idx}</td>
    <td>
      <div style="font-weight:700;">${esc(r.label||'—')}</div>
      <div style="font-size:.68rem;color:var(--em-tx3);">by ${esc(r.created_by||'')}</div>
    </td>
    <td>${mtsHtml}</td>
    <td>
      <div style="display:flex;align-items:center;gap:5px;">
        <i class="bi bi-envelope" style="color:var(--em-v3);font-size:.8rem;"></i>
        <span style="font-weight:600;font-size:.8rem;">${esc(r.to_email||'')}</span>
      </div>
    </td>
    <td>${ccHtml}</td>
    <td>${mobileHtml}</td>
    <td>
      <label class="em-toggle">
        <input type="checkbox" class="toggle-status" data-id="${r.id}" ${r.status?'checked':''}>
        <span class="em-toggle-slider"></span>
      </label>
    </td>
    <td style="font-size:.7rem;color:var(--em-tx3);">${esc(r.updated_at||'')}</td>
    <td>
      <div style="display:flex;gap:4px;">
        <button class="em-btn-icon edit" title="Edit" onclick="openEdit(${r.id})"><i class="bi bi-pencil"></i></button>
        <button class="em-btn-icon del"  title="Delete" onclick="deleteRecord(${r.id})"><i class="bi bi-trash"></i></button>
      </div>
    </td>`;
}

function rowSearchAttr(r) {
  const mts = Array.isArray(r.menu_types) ? r.menu_types : [];
  return `${r.label||''} ${r.to_email||''} ${mts.join(' ')} ${r.mobile_number||''}`.toLowerCase();
}

/* ════════════════════════════════════
   UPDATE A SINGLE ROW IN PLACE
════════════════════════════════════ */
function updateSingleRow(r) {
  window.EM_RECORDS[r.id] = r;
  const tr = document.querySelector(`#emTableBody tr[data-record-id="${r.id}"]`);
  if (!tr) { rebuildTable(Object.values(window.EM_RECORDS)); return; }

  const mts = Array.isArray(r.menu_types) ? r.menu_types : [];
  const rows = [...document.querySelectorAll('#emTableBody tr[data-record-id]')];
  const idx  = rows.indexOf(tr) + 1;

  tr.dataset.menus  = mts.join(',');
  tr.dataset.search = rowSearchAttr(r);
  tr.innerHTML      = buildRowCells(r, idx);

  tr.classList.remove('row-updated');
  void tr.offsetWidth; // force reflow so animation restarts
  tr.classList.add('row-updated');

  applyFilter();
}

/* ════════════════════════════════════
   REBUILD TABLE (full — add / delete)
════════════════════════════════════ */
function rebuildTable(records) {
  // keep EM_RECORDS in sync
  if (records && records.length) {
    window.EM_RECORDS = {};
    records.forEach(r => { window.EM_RECORDS[r.id] = r; });
  }

  const tbody = document.getElementById('emTableBody');
  if (!records || !records.length) {
    tbody.innerHTML = `<tr><td colspan="9"><div class="em-no-data"><i class="bi bi-envelope-x"></i>No email configurations yet.</div></td></tr>`;
    return;
  }

  let html = '';
  records.forEach((r, i) => {
    const mts    = Array.isArray(r.menu_types) ? r.menu_types : [];
    html += `<tr data-record-id="${r.id}" data-menus="${mts.join(',')}" data-search="${rowSearchAttr(r)}">${buildRowCells(r, i+1)}</tr>`;
  });
  tbody.innerHTML = html;

  const dd = document.getElementById('emMenuFilter');
  if (dd) dd.value = activeFilter;
  applyFilter();
}

/* ════════════════════════════════════
   STATS UPDATE
════════════════════════════════════ */
function updateStats(s) {
  document.getElementById('st-total').textContent    = s.total;
  document.getElementById('st-active').textContent   = s.active;
  document.getElementById('st-inactive').textContent = s.inactive;
}

/* ════════════════════════════════════
   FILTER / SEARCH
════════════════════════════════════ */
let activeFilter = 'all';

function applyFilter() {
  const q = document.getElementById('emSearch').value.toLowerCase().trim();
  document.querySelectorAll('#emTableBody tr[data-menus]').forEach(row => {
    const menuMatch   = activeFilter === 'all' || (row.dataset.menus || '').split(',').includes(activeFilter);
    const searchMatch = !q || (row.dataset.search || '').includes(q);
    row.style.display = (menuMatch && searchMatch) ? '' : 'none';
  });
}

function onFilterChange(sel) {
  activeFilter = sel.value;
  sel.classList.toggle('has-filter', activeFilter !== 'all');
  applyFilter();
}

document.getElementById('emSearch').addEventListener('input', applyFilter);
</script>

@include('superadmin.superadminfooter')
</body>
</html>
