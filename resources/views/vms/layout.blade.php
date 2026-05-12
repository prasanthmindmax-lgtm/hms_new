<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>VMS · @yield('page_title','Dashboard') — Dr. Aravind's IVF</title>
<!-- <link rel="icon" type="image/png" href="{{ asset('assets/images/dralogos.png') }}"> -->
<link rel="icon" href="{{ asset('/assets/images/favi.jpg') }}" type="image/x-icon" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.5.0/dist/tabler-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
/* ─── Reset ─────────────────────────────────────────── */
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --nav:#0f2d4a;--nav-active:#1a7f64;
  --accent:#1a7f64;--accent2:#16a37e;
  --warn:#d97706;--danger:#dc2626;--blue:#2563eb;--purple:#7c3aed;
  --bg:#f0f4f8;--card:#fff;--border:#e2e8f0;
  --text:#1e293b;--muted:#64748b;--sidebar-w:220px;
  --topbar-h:58px;
}
html,body{height:100%;font-family:'Inter',system-ui,sans-serif;background:var(--bg);color:var(--text)}

/* ─── Sidebar ────────────────────────────────────────── */
.vms-sidebar{
  width:var(--sidebar-w);background:var(--nav);
  display:flex;flex-direction:column;
  position:fixed;top:0;left:0;height:100vh;z-index:1000;
  transition:transform 0.25s;
}
.sidebar-logo{
  height:var(--topbar-h);display:flex;align-items:center;
  padding:0 16px;border-bottom:1px solid rgba(255,255,255,0.07);flex-shrink:0;
  gap:10px;
}
.sidebar-logo img{max-height:32px;width:auto;object-fit:contain;filter:brightness(0) invert(1);opacity:0.9}
.sidebar-logo .vms-badge{
  background:var(--accent);color:#fff;font-size:9px;font-weight:700;
  padding:2px 7px;border-radius:20px;letter-spacing:0.06em;text-transform:uppercase;flex-shrink:0;
}

.vms-nav{flex:1;overflow-y:auto;padding:8px 0}
.vms-nav::-webkit-scrollbar{width:3px}
.vms-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.12);border-radius:2px}

.nav-section{font-size:9px;letter-spacing:0.1em;color:rgba(255,255,255,0.3);padding:14px 16px 4px;text-transform:uppercase;font-weight:700}

.nav-link-vms{
  display:flex;align-items:center;gap:10px;
  padding:9px 16px;font-size:13px;color:rgba(255,255,255,0.65);
  text-decoration:none;border-left:3px solid transparent;
  transition:all 0.15s;
}
.nav-link-vms:hover{background:rgba(255,255,255,0.06);color:#fff;text-decoration:none}
.nav-link-vms.active{
  background:rgba(26,127,100,0.2);color:#fff;
  border-left-color:var(--accent);font-weight:600;
}
.nav-link-vms i{font-size:18px;width:20px;flex-shrink:0}
.nav-badge-vms{
  margin-left:auto;font-size:10px;font-weight:700;
  padding:1px 7px;border-radius:20px;color:#fff;
}

/* ─── Sidebar footer ─────────────────────────────────── */
.sidebar-footer{
  border-top:1px solid rgba(255,255,255,0.07);
  padding:10px 12px;flex-shrink:0;
}
.sidebar-user-card{
  display:flex;align-items:center;gap:9px;
  padding:6px 8px;border-radius:8px;cursor:pointer;
}
.sidebar-user-card:hover{background:rgba(255,255,255,0.07)}
.sidebar-user-avatar{
  width:33px;height:33px;border-radius:50%;flex-shrink:0;
  background:var(--accent);
  display:flex;align-items:center;justify-content:center;
  font-size:12px;font-weight:700;color:#fff;
}
.sidebar-user-name{font-size:12px;font-weight:600;color:#fff;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sidebar-user-role{font-size:10.5px;color:rgba(255,255,255,0.4)}
.sidebar-user-dots{color:rgba(255,255,255,0.3);font-size:15px;margin-left:auto}
.sidebar-user-dropdown{
  background:#1a3a54;border:1px solid rgba(255,255,255,0.1)!important;
  border-radius:10px!important;
}
.sidebar-user-dropdown .dropdown-item{color:rgba(255,255,255,0.7)!important;font-size:12.5px}
.sidebar-user-dropdown .dropdown-item:hover{background:rgba(255,255,255,0.08)!important;color:#fff!important}
.sidebar-user-dropdown .dropdown-divider{border-color:rgba(255,255,255,0.1)!important}
.sidebar-user-dropdown .text-danger{color:#fca5a5!important}
.sidebar-user-dropdown .text-danger:hover{color:#fff!important;background:rgba(220,38,38,0.3)!important}

/* ─── Topbar ─────────────────────────────────────────── */
.vms-topbar{
  position:fixed;top:0;left:var(--sidebar-w);right:0;
  height:var(--topbar-h);background:var(--nav);
  display:flex;align-items:center;gap:12px;
  padding:0 20px;z-index:999;
  border-bottom:1px solid rgba(255,255,255,0.07);
}
.topbar-breadcrumb{font-size:14px;font-weight:600;color:#fff}
.topbar-breadcrumb .tb-sep{color:rgba(255,255,255,0.3);margin:0 6px}
.topbar-breadcrumb .tb-sub{font-size:13px;font-weight:400;color:rgba(255,255,255,0.5)}
.topbar-search{
  flex:1;max-width:280px;position:relative;margin-left:auto;
}
.topbar-search input{
  width:100%;height:34px;border-radius:8px;
  border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.08);
  padding:0 12px 0 34px;font-size:12px;color:#fff;outline:none;
}
.topbar-search input::placeholder{color:rgba(255,255,255,0.35)}
.topbar-search input:focus{border-color:var(--accent2)}
.topbar-search .ts-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:16px;color:rgba(255,255,255,0.4)}
.topbar-actions{display:flex;align-items:center;gap:6px}
.tb-icon-btn{
  width:34px;height:34px;border-radius:8px;
  border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.06);
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;color:rgba(255,255,255,0.7);position:relative;
  text-decoration:none;font-size:18px;transition:all 0.15s;
}
.tb-icon-btn:hover{background:rgba(255,255,255,0.12);color:#fff}
.tb-icon-btn .dot{
  position:absolute;top:5px;right:6px;
  width:7px;height:7px;background:#ef4444;
  border-radius:50%;border:2px solid var(--nav);
}
.tb-avatar{
  width:34px;height:34px;border-radius:50%;
  background:var(--accent);
  display:flex;align-items:center;justify-content:center;
  font-size:12px;font-weight:700;color:#fff;cursor:pointer;
  border:2px solid rgba(255,255,255,0.15);
}
.sidebar-toggle-btn{
  display:none;width:34px;height:34px;border-radius:8px;
  border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.06);
  align-items:center;justify-content:center;cursor:pointer;color:#fff;
}
@media(max-width:768px){.sidebar-toggle-btn{display:flex}}

/* App switcher panel */
.app-switcher-panel{
  display:none;position:absolute;top:calc(100% + 10px);right:0;
  background:#fff;border:1px solid #e5e7eb;border-radius:16px;
  box-shadow:0 12px 40px rgba(0,0,0,0.18);padding:18px;
  min-width:230px;z-index:9999;
}
.app-switcher-panel .asp-title{font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:12px}
.app-switcher-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.app-card{display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;border:1.5px solid #e5e7eb;border-radius:12px;text-decoration:none;transition:all 0.15s;background:#fff}
.app-card:hover{background:#f8fafc;border-color:#cbd5e1;transform:translateY(-1px);text-decoration:none}
.app-card.current{background:#f0fdf4;border-color:#86efac}
.app-card .ac-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center}
.app-card .ac-name{font-size:12px;font-weight:700;color:#1e293b}
.app-card .ac-desc{font-size:10px;color:#64748b;text-align:center}

/* ─── Main ───────────────────────────────────────────── */
.vms-main{margin-left:var(--sidebar-w);padding-top:var(--topbar-h);min-height:100vh}
.vms-content{padding:22px 24px}

/* ─── Cards ─────────────────────────────────────────── */
.vms-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:18px 20px}
.vms-card-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.vms-card-title{font-size:13px;font-weight:600;color:var(--text)}
.vms-card-pill{font-size:11px;padding:3px 10px;border-radius:20px;background:var(--bg);color:var(--muted);border:1px solid var(--border)}

/* ─── KPI grid ───────────────────────────────────────── */
.kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
@media(max-width:960px){.kpi-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){.kpi-grid{grid-template-columns:1fr}}
.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 18px;position:relative;overflow:hidden;transition:box-shadow 0.2s}
.kpi-card:hover{box-shadow:0 4px 18px rgba(0,0,0,0.08)}
.kpi-stripe{position:absolute;top:0;left:0;width:4px;height:100%;background:var(--accent);border-radius:4px 0 0 4px}
.kpi-card.warn .kpi-stripe{background:var(--warn)}
.kpi-card.danger .kpi-stripe{background:var(--danger)}
.kpi-card.blue .kpi-stripe{background:var(--blue)}
.kpi-card.purple .kpi-stripe{background:var(--purple)}
.kpi-label{font-size:11px;color:var(--muted);margin-bottom:6px;font-weight:500}
.kpi-value{font-size:26px;font-weight:700;color:var(--text);line-height:1}
.kpi-sub{font-size:11px;color:var(--muted);margin-top:5px}
.kpi-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:32px;opacity:0.07;color:var(--text)}

/* ─── Status dots ────────────────────────────────────── */
.vdot{width:9px;height:9px;border-radius:50%;flex-shrink:0;display:inline-block}
.vdot.green{background:#16a34a}.vdot.orange{background:#d97706}.vdot.red{background:#dc2626}.vdot.blue{background:var(--blue)}.vdot.muted{background:var(--muted)}

/* ─── Badges ─────────────────────────────────────────── */
.badge-pharma{background:#dcfce7;color:#166534;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-non{background:#dbeafe;color:#1e40af;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-pending{background:#fef9c3;color:#854d0e;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-approved,.badge-inside{background:#dcfce7;color:#166534;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-rejected{background:#fee2e2;color:#991b1b;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-checked_out{background:var(--bg);color:var(--muted);font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600;border:1px solid var(--border)}
.badge-blacklist{background:#fee2e2;color:#991b1b;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600;border:1px solid #fca5a5}

/* ─── Buttons ────────────────────────────────────────── */
.vbtn{font-size:12px;padding:6px 14px;border-radius:8px;border:1px solid;cursor:pointer;font-weight:600;transition:all 0.15s;display:inline-flex;align-items:center;gap:5px;text-decoration:none}
.vbtn-approve{background:#dcfce7;color:#166534;border-color:#86efac}
.vbtn-approve:hover{background:#bbf7d0;color:#166534}
.vbtn-reject{background:#fee2e2;color:#991b1b;border-color:#fca5a5}
.vbtn-reject:hover{background:#fecaca;color:#991b1b}
.vbtn-hold{background:var(--bg);color:var(--muted);border-color:var(--border)}
.vbtn-hold:hover{background:#e5e7eb;color:var(--text)}
.vbtn-primary{background:var(--accent);color:#fff;border-color:var(--accent)}
.vbtn-primary:hover{background:var(--accent2);color:#fff}
.vbtn-danger{background:var(--danger);color:#fff;border-color:var(--danger)}
.vbtn-danger:hover{opacity:0.9}

/* ─── Tables ─────────────────────────────────────────── */
.vms-table{width:100%;border-collapse:collapse;font-size:12.5px}
.vms-table th{font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.06em;padding:10px 14px;border-bottom:2px solid var(--border);background:#fafafa;white-space:nowrap}
.vms-table td{padding:11px 14px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
.vms-table tbody tr:hover{background:rgba(26,127,100,0.03)}
.vms-table td:first-child,.vms-table th:first-child{padding-left:18px}

/* ─── Filter bar ─────────────────────────────────────── */
.vms-filter-bar{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:14px 18px;margin-bottom:18px}
.vms-filter-bar .filter-label{font-size:11px;font-weight:600;color:var(--muted);margin-bottom:5px;display:block}

/* Select2 customisation */
.select2-container--default .select2-selection--multiple{border:1px solid var(--border)!important;border-radius:8px!important;background:var(--bg)!important;min-height:36px!important;font-size:12px;padding:2px 6px}
.select2-container--default .select2-selection--multiple .select2-selection__choice{background:#f0fdf4!important;border:1px solid #86efac!important;color:#166534!important;border-radius:6px!important;font-size:11px!important;padding:1px 8px!important;margin:2px 3px!important}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove{color:#166534!important;margin-right:4px!important}
.select2-dropdown{border:1px solid var(--border)!important;border-radius:10px!important;box-shadow:0 8px 24px rgba(0,0,0,0.1)!important}
.select2-container--default .select2-results__option--highlighted{background:var(--accent)!important}

/* ─── Toast ──────────────────────────────────────────── */
.vms-toast-wrap{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px}
.vms-toast{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:12px 16px;font-size:12px;box-shadow:0 8px 30px rgba(0,0,0,0.12);display:flex;align-items:center;gap:10px;min-width:240px;animation:vSlideIn 0.2s ease}
.vms-toast.success .vti{color:var(--accent)}
.vms-toast.error .vti{color:var(--danger)}
@keyframes vSlideIn{from{transform:translateX(100px);opacity:0}to{transform:none;opacity:1}}

/* ─── Modal ──────────────────────────────────────────── */
.vms-modal .modal-header{background:var(--nav);color:#fff}
.vms-modal .modal-header .btn-close{filter:invert(1)}
.vms-modal .modal-title{font-size:14px;font-weight:600}

/* ─── Responsive ─────────────────────────────────────── */
@media(max-width:768px){
  .vms-sidebar{transform:translateX(-100%)}
  .vms-sidebar.mob-open{transform:none}
  .vms-main{margin-left:0}
  .vms-topbar{left:0}
  .vms-content{padding:14px}
}
</style>
@yield('extra_css')
</head>
<body>

{{-- ══ SIDEBAR ══════════════════════════════════════════════════════════ --}}
<aside class="vms-sidebar" id="vmsSidebar">

  {{-- Logo --}}
  <div class="sidebar-logo">
    <a href="{{ route('vms.dashboard') }}" style="display:flex;align-items:center;gap:8px;text-decoration:none;flex:1;min-width:0">
      <img src="{{ asset('/assets/images/dralogos.png') }}" alt="Dr. Aravind's IVF">
    </a>
    <span class="vms-badge">VMS</span>
  </div>

  {{-- Navigation --}}
  @php
    $pendingCount = \App\Models\VmsVisitor::where('status','pending')->count();
    $insideCount  = \App\Models\VmsVisitor::where('status','inside')->count();
    $blCount      = \App\Models\VmsBlacklist::where('is_active',true)->count();
  @endphp
  <nav class="vms-nav">
    <div class="nav-section">Main</div>
    <a href="{{ route('vms.dashboard') }}" class="nav-link-vms {{ request()->routeIs('vms.dashboard') ? 'active' : '' }}">
      <i class="ti ti-layout-dashboard"></i> Dashboard
    </a>
    <a href="{{ route('vms.approvals') }}" class="nav-link-vms {{ request()->routeIs('vms.approvals') ? 'active' : '' }}">
      <i class="ti ti-checklist"></i> Approvals
      @if($pendingCount > 0)<span class="nav-badge-vms" style="background:var(--warn)">{{ $pendingCount }}</span>@endif
    </a>
    <a href="{{ route('vms.active') }}" class="nav-link-vms {{ request()->routeIs('vms.active') ? 'active' : '' }}">
      <i class="ti ti-users"></i> Active Visitors
      @if($insideCount > 0)<span class="nav-badge-vms" style="background:var(--blue)">{{ $insideCount }}</span>@endif
    </a>
    <a href="{{ route('vms.history') }}" class="nav-link-vms {{ request()->routeIs('vms.history') ? 'active' : '' }}">
      <i class="ti ti-history"></i> Visitor History
    </a>

    <div class="nav-section">Vendors</div>
    <a href="{{ route('vms.pharma') }}" class="nav-link-vms {{ request()->routeIs('vms.pharma') ? 'active' : '' }}">
      <i class="ti ti-pill"></i> Pharma Vendors
    </a>
    <a href="{{ route('vms.non-pharma') }}" class="nav-link-vms {{ request()->routeIs('vms.non-pharma') ? 'active' : '' }}">
      <i class="ti ti-briefcase"></i> Non-Pharma
    </a>
    <a href="{{ route('vms.blacklist') }}" class="nav-link-vms {{ request()->routeIs('vms.blacklist') ? 'active' : '' }}">
      <i class="ti ti-ban"></i> Blacklisted
      @if($blCount > 0)<span class="nav-badge-vms" style="background:var(--danger)">{{ $blCount }}</span>@endif
    </a>

    <div class="nav-section">Management</div>
    <a href="{{ route('vms.reports') }}" class="nav-link-vms {{ request()->routeIs('vms.reports') ? 'active' : '' }}">
      <i class="ti ti-chart-bar"></i> Reports
    </a>
    <a href="{{ route('vms.qr') }}" class="nav-link-vms {{ request()->routeIs('vms.qr') ? 'active' : '' }}">
      <i class="ti ti-qrcode"></i> QR Management
    </a>
    <a href="{{ route('vms.settings') }}" class="nav-link-vms {{ request()->routeIs('vms.settings') ? 'active' : '' }}">
      <i class="ti ti-settings"></i> Settings
    </a>
  </nav>

  {{-- User footer --}}
  @php
    $vmsUser = auth()->user();
    $vmsName = $vmsUser ? ($vmsUser->user_fullname ?? $vmsUser->name ?? 'Admin') : 'Admin';
    $vmsInit = strtoupper(implode('', array_map(fn($w)=>$w[0], array_slice(array_filter(explode(' ',$vmsName)),0,2))));
  @endphp
  <div class="sidebar-footer">
    <div class="dropup">
      <div class="sidebar-user-card" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="sidebar-user-avatar">{{ $vmsInit ?: 'AD' }}</div>
        <div style="flex:1;min-width:0">
          <div class="sidebar-user-name">{{ $vmsName }}</div>
          <div class="sidebar-user-role">VMS Admin</div>
        </div>
        <i class="ti ti-dots-vertical sidebar-user-dots"></i>
      </div>
      <ul class="dropdown-menu sidebar-user-dropdown" style="width:100%;min-width:190px;margin-bottom:6px">
        <li><div class="px-3 py-2" style="font-size:11px;color:rgba(255,255,255,0.4)">{{ $vmsUser?->email ?? '' }}</div></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a href="{{ route('superadmin.dashboard') }}" class="dropdown-item">
            <i class="ti ti-building-hospital me-2" style="font-size:15px"></i>Back to HMS
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit" class="dropdown-item text-danger">
              <i class="ti ti-power me-2" style="font-size:15px"></i>Log Out
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</aside>

{{-- ══ TOPBAR ════════════════════════════════════════════════════════════ --}}
<div class="vms-topbar">
  <button class="sidebar-toggle-btn" onclick="document.getElementById('vmsSidebar').classList.toggle('mob-open')">
    <i class="ti ti-menu-2" style="font-size:18px"></i>
  </button>

  <div class="topbar-breadcrumb">
    @yield('page_title','Dashboard')
    @hasSection('page_subtitle')
      <span class="tb-sep">/</span><span class="tb-sub">@yield('page_subtitle')</span>
    @endif
  </div>

  <div class="topbar-search">
    <i class="ti ti-search ts-icon"></i>
    <input type="text" placeholder="Search visitors…" id="globalSearch">
  </div>

  <div class="topbar-actions">
    {{-- App Switcher --}}
    <div style="position:relative" id="vmsAppSwitcherWrap">
      <button class="tb-icon-btn" onclick="toggleVmsAppSwitcher()" title="Switch App">
        <i class="ti ti-grid-dots"></i>
      </button>
      <div class="app-switcher-panel" id="vmsAppSwitcherPanel">
        <div class="asp-title">Switch Application</div>
        <div class="app-switcher-grid">
          <a href="{{ route('superadmin.dashboard') }}" class="app-card">
            <div class="ac-icon" style="background:linear-gradient(135deg,#0f2d4a,#1e4976)">
              <i class="ti ti-building-hospital" style="font-size:22px;color:#fff"></i>
            </div>
            <span class="ac-name">HMS</span>
            <span class="ac-desc">Hospital Management</span>
          </a>
          <a href="{{ route('vms.dashboard') }}" class="app-card current">
            <div class="ac-icon" style="background:linear-gradient(135deg,#1a7f64,#16a37e)">
              <i class="ti ti-qrcode" style="font-size:22px;color:#fff"></i>
            </div>
            <span class="ac-name">VMS</span>
            <span class="ac-desc">Visitor Management</span>
          </a>
        </div>
      </div>
    </div>

    {{-- Bell --}}
    <a href="{{ route('vms.approvals') }}" class="tb-icon-btn" title="Pending approvals">
      <i class="ti ti-bell"></i>
      @if($pendingCount > 0)<div class="dot"></div>@endif
    </a>

    {{-- Refresh --}}
    <a href="{{ request()->fullUrl() }}" class="tb-icon-btn" title="Refresh">
      <i class="ti ti-refresh"></i>
    </a>

    {{-- User avatar dropdown --}}
    <div class="dropdown">
      <div class="tb-avatar" data-bs-toggle="dropdown" title="{{ $vmsName }}">{{ $vmsInit ?: 'AD' }}</div>
      <ul class="dropdown-menu dropdown-menu-end" style="font-size:13px;min-width:180px">
        <li><div class="px-3 py-2" style="font-size:12px;color:var(--muted)">{{ $vmsName }}</div></li>
        <li><hr class="dropdown-divider"></li>
        <li><a href="{{ route('superadmin.dashboard') }}" class="dropdown-item"><i class="ti ti-building-hospital me-2"></i>Back to HMS</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="dropdown-item text-danger" type="submit"><i class="ti ti-power me-2"></i>Log Out</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</div>

{{-- ══ CONTENT ══════════════════════════════════════════════════════════ --}}
<div class="vms-main">
  <div class="vms-content">
    @yield('content')
  </div>
</div>

{{-- Toast --}}
<div class="vms-toast-wrap" id="toastWrap"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  function showToast(msg, type='success') {
    const icon = type === 'success' ? 'ti-circle-check' : 'ti-alert-circle';
    const t = $(`<div class="vms-toast ${type}">
      <i class="ti ${icon} vti" style="font-size:20px;flex-shrink:0"></i>
      <span>${msg}</span>
    </div>`);
    $('#toastWrap').append(t);
    setTimeout(() => t.fadeOut(300, () => t.remove()), 3500);
  }

  function toggleVmsAppSwitcher() {
    const p = document.getElementById('vmsAppSwitcherPanel');
    p.style.display = p.style.display === 'block' ? 'none' : 'block';
  }

  document.addEventListener('click', function(e) {
    const w = document.getElementById('vmsAppSwitcherWrap');
    if (w && !w.contains(e.target)) {
      document.getElementById('vmsAppSwitcherPanel').style.display = 'none';
    }
    if (!e.target.closest('#vmsSidebar') && !e.target.closest('.sidebar-toggle-btn')) {
      document.getElementById('vmsSidebar').classList.remove('mob-open');
    }
  });

  // Auto-refresh stats
  setInterval(function(){
    $.get('{{ route("vms.ajax.stats") }}', function(d){
      if(d.active_inside !== undefined && document.getElementById('stat-active'))
        document.getElementById('stat-active').textContent = d.active_inside;
    });
  }, 30000);

  // Select2
  $(document).ready(function(){
    $('.vms-multi-select').each(function(){
      $(this).select2({
        placeholder: $(this).data('placeholder') || 'Select…',
        allowClear: true,
        width: '100%',
        closeOnSelect: false,
        dropdownParent: $(this).closest('form'),
      });
    });
  });
</script>
@stack('filter_js')
@yield('extra_js')
</body>
</html>
