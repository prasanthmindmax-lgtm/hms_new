<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>VMS — @yield('page_title','Dashboard')</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.5.0/tabler-icons.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
/* ── Reset & Base ───────────────────────────────────── */
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --nav:#0f2d4a;--nav-active:#1a7f64;
  --accent:#1a7f64;--accent2:#16a37e;
  --warn:#d97706;--danger:#dc2626;--blue:#2563eb;--purple:#7c3aed;
  --bg:#f0f4f8;--card:#fff;--border:#e2e8f0;
  --text:#1e293b;--muted:#64748b;--sidebar-w:220px;
}

html,body{height:100%;font-family:'Inter',system-ui,sans-serif;background:var(--bg);color:var(--text)}

/* ── Shell ──────────────────────────────────────────── */
.vms-shell{display:flex;min-height:100vh}

/* ── Sidebar ────────────────────────────────────────── */
.vms-sidebar{
  width:var(--sidebar-w);background:var(--nav);
  display:flex;flex-direction:column;
  position:fixed;top:0;left:0;height:100vh;z-index:1000;
  transition:transform 0.25s;overflow:hidden;
}
.sidebar-logo{padding:18px 16px 12px;border-bottom:1px solid rgba(255,255,255,0.08)}
.sidebar-logo .logo-icon{width:36px;height:36px;background:var(--accent);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:10px}
.sidebar-logo .logo-icon i{font-size:20px;color:#fff}
.sidebar-logo .hosp{font-size:11.5px;font-weight:600;color:#fff;line-height:1.4;letter-spacing:0.01em}
.sidebar-logo .sub{font-size:9.5px;color:rgba(255,255,255,0.4);margin-top:2px;text-transform:uppercase;letter-spacing:0.08em}

.sidebar-branch{margin:10px 12px;background:rgba(255,255,255,0.07);border-radius:8px;padding:7px 12px;font-size:10px;color:rgba(255,255,255,0.45)}
.sidebar-branch span{color:rgba(255,255,255,0.9);font-size:11px;font-weight:600}

.vms-nav{flex:1;overflow-y:auto;padding:8px 0}
.vms-nav::-webkit-scrollbar{width:3px}
.vms-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.12);border-radius:2px}

.nav-section{font-size:9px;letter-spacing:0.1em;color:rgba(255,255,255,0.3);padding:12px 16px 4px;text-transform:uppercase;font-weight:600}

.nav-link-vms{
  display:flex;align-items:center;gap:9px;
  padding:8px 16px;font-size:12.5px;
  color:rgba(255,255,255,0.65);
  text-decoration:none;
  border-left:3px solid transparent;
  transition:all 0.15s;position:relative;
}
.nav-link-vms:hover{background:rgba(255,255,255,0.07);color:#fff;text-decoration:none}
.nav-link-vms.active{background:rgba(26,127,100,0.22);color:#fff;border-left-color:var(--accent2)}
.nav-link-vms i{font-size:17px;width:18px;flex-shrink:0}
.nav-badge-vms{margin-left:auto;background:var(--danger);color:#fff;font-size:9px;padding:1px 6px;border-radius:10px;font-weight:600}
.nav-badge-vms.warn{background:var(--warn)}

.sidebar-user{padding:12px 16px;border-top:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;gap:9px}
.sidebar-user .uavatar{width:30px;height:30px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff;font-weight:600;flex-shrink:0}
.sidebar-user .uname{font-size:11px;color:#fff;font-weight:500}
.sidebar-user .urole{font-size:9.5px;color:rgba(255,255,255,0.35)}

/* ── Main area ──────────────────────────────────────── */
.vms-main{flex:1;margin-left:var(--sidebar-w);display:flex;flex-direction:column;min-height:100vh;min-width:0}

/* ── Topbar ─────────────────────────────────────────── */
.vms-topbar{
  background:var(--card);border-bottom:1px solid var(--border);
  padding:0 24px;height:56px;
  display:flex;align-items:center;gap:14px;
  position:sticky;top:0;z-index:900;
}
.topbar-title{font-size:15px;font-weight:600;color:var(--text)}
.topbar-title .sep{color:var(--muted);font-weight:400;margin:0 4px}
.topbar-title .sub{font-size:13px;color:var(--muted);font-weight:400}

.topbar-search{flex:1;max-width:260px;position:relative}
.topbar-search input{width:100%;height:34px;border-radius:8px;border:1px solid var(--border);background:var(--bg);padding:0 12px 0 34px;font-size:12px;color:var(--text);outline:none}
.topbar-search input:focus{border-color:var(--accent2)}
.topbar-search i{position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:16px;color:var(--muted)}
.topbar-actions{display:flex;align-items:center;gap:8px;margin-left:auto}
.topbar-icon-btn{width:34px;height:34px;border-radius:8px;border:1px solid var(--border);background:var(--card);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);position:relative;text-decoration:none}
.topbar-icon-btn:hover{background:var(--bg);color:var(--text)}
.topbar-icon-btn .dot{position:absolute;top:6px;right:7px;width:6px;height:6px;background:var(--danger);border-radius:50%;border:1.5px solid var(--card)}
.topbar-avatar{width:34px;height:34px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#fff;cursor:pointer}

/* ── Content ────────────────────────────────────────── */
.vms-content{flex:1;padding:24px;overflow-x:hidden}

/* ── Cards ──────────────────────────────────────────── */
.vms-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px 20px}
.vms-card-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.vms-card-title{font-size:13px;font-weight:600;color:var(--text)}
.vms-card-pill{font-size:11px;padding:3px 10px;border-radius:20px;background:var(--bg);color:var(--muted);border:1px solid var(--border)}

/* ── KPI cards ──────────────────────────────────────── */
.kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:580px){.kpi-grid{grid-template-columns:1fr}}

.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px 18px;position:relative;overflow:hidden;transition:box-shadow 0.2s}
.kpi-card:hover{box-shadow:0 4px 20px rgba(0,0,0,0.08)}
.kpi-stripe{position:absolute;top:0;left:0;width:4px;height:100%;background:var(--accent);border-radius:4px 0 0 4px}
.kpi-card.warn .kpi-stripe{background:var(--warn)}
.kpi-card.danger .kpi-stripe{background:var(--danger)}
.kpi-card.blue .kpi-stripe{background:var(--blue)}
.kpi-card.purple .kpi-stripe{background:var(--purple)}
.kpi-label{font-size:11px;color:var(--muted);margin-bottom:6px;font-weight:500;letter-spacing:0.02em}
.kpi-value{font-size:26px;font-weight:700;color:var(--text);line-height:1}
.kpi-sub{font-size:11px;color:var(--muted);margin-top:5px}
.kpi-sub.up{color:#16a34a}
.kpi-sub.dn{color:var(--danger)}
.kpi-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:32px;opacity:0.08;color:var(--text)}

/* ── Status dots ────────────────────────────────────── */
.vdot{width:8px;height:8px;border-radius:50%;flex-shrink:0;display:inline-block}
.vdot.green{background:#16a34a}
.vdot.orange{background:#d97706}
.vdot.red{background:#dc2626}
.vdot.blue{background:var(--blue)}
.vdot.muted{background:var(--muted)}

/* ── Badges ─────────────────────────────────────────── */
.badge-pharma{background:#dcfce7;color:#166534;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-non{background:#dbeafe;color:#1e40af;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-pending{background:#fef9c3;color:#854d0e;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-approved{background:#dcfce7;color:#166534;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-rejected{background:#fee2e2;color:#991b1b;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-inside{background:#dbeafe;color:#1e40af;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.badge-checked_out{background:var(--bg);color:var(--muted);font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600;border:1px solid var(--border)}
.badge-blacklist{background:#fee2e2;color:#991b1b;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600;border:1px solid #fca5a5}

/* ── Buttons ─────────────────────────────────────────── */
.vbtn{font-size:11px;padding:5px 12px;border-radius:7px;border:1px solid;cursor:pointer;font-weight:600;transition:all 0.15s;display:inline-flex;align-items:center;gap:4px;text-decoration:none}
.vbtn-approve{background:#dcfce7;color:#166534;border-color:#86efac}
.vbtn-approve:hover{background:#bbf7d0}
.vbtn-reject{background:#fee2e2;color:#991b1b;border-color:#fca5a5}
.vbtn-reject:hover{background:#fecaca}
.vbtn-hold{background:var(--bg);color:var(--muted);border-color:var(--border)}
.vbtn-hold:hover{background:var(--border)}
.vbtn-primary{background:var(--accent);color:#fff;border-color:var(--accent)}
.vbtn-primary:hover{background:var(--accent2)}
.vbtn-danger{background:var(--danger);color:#fff;border-color:var(--danger)}
.vbtn-danger:hover{opacity:0.9}

/* ── Tables ─────────────────────────────────────────── */
.vms-table{width:100%;border-collapse:collapse;font-size:12px}
.vms-table th{font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.06em;padding:10px 14px;border-bottom:2px solid var(--border);background:var(--bg);white-space:nowrap}
.vms-table td{padding:10px 14px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
.vms-table tbody tr:hover{background:rgba(26,127,100,0.03)}
.vms-table td:first-child,
.vms-table th:first-child{padding-left:18px}

/* ── Toast ──────────────────────────────────────────── */
.vms-toast-wrap{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px}
.vms-toast{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:12px 16px;font-size:12px;box-shadow:0 8px 30px rgba(0,0,0,0.12);display:flex;align-items:center;gap:10px;min-width:240px;animation:slideIn 0.2s ease}
.vms-toast.success .toast-icon{color:var(--accent)}
.vms-toast.error .toast-icon{color:var(--danger)}
@keyframes slideIn{from{transform:translateX(100px);opacity:0}to{transform:none;opacity:1}}

/* ── Modal helpers ───────────────────────────────────── */
.vms-modal .modal-header{background:var(--nav);color:#fff}
.vms-modal .modal-header .btn-close{filter:invert(1)}
.vms-modal .modal-title{font-size:14px;font-weight:600}

/* ── Responsive sidebar ──────────────────────────────── */
@media(max-width:768px){
  .vms-sidebar{transform:translateX(-100%)}
  .vms-sidebar.open{transform:none}
  .vms-main{margin-left:0}
  .sidebar-toggle{display:flex!important}
  .vms-content{padding:16px}
}
.sidebar-toggle{display:none;width:34px;height:34px;border-radius:8px;border:1px solid var(--border);background:var(--card);align-items:center;justify-content:center;cursor:pointer}
</style>
@yield('extra_css')
</head>
<body>
<div class="vms-shell">

  {{-- SIDEBAR --}}
  <aside class="vms-sidebar" id="vmsSidebar">
    <div class="sidebar-logo">
      <div class="logo-icon"><i class="ti ti-building-hospital"></i></div>
      <div class="hosp">{{ $settings['hospital_name'] ?? "Dr. Aravind's IVF & Pregnancy Centre" }}</div>
      <div class="sub">Visitor Management</div>
    </div>
    <div class="sidebar-branch">Branch: <span>{{ $settings['default_branch'] ?? 'Main Hospital' }}</span></div>

    <nav class="vms-nav">
      <div class="nav-section">Main</div>
      <a href="{{ route('vms.dashboard') }}" class="nav-link-vms {{ request()->routeIs('vms.dashboard') ? 'active' : '' }}">
        <i class="ti ti-layout-dashboard"></i> Dashboard
      </a>
      <a href="{{ route('vms.approvals') }}" class="nav-link-vms {{ request()->routeIs('vms.approvals') ? 'active' : '' }}">
        <i class="ti ti-checklist"></i> Approvals
        @php $pendingCount = \App\Models\VmsVisitor::where('status','pending')->count() @endphp
        @if($pendingCount > 0)
          <span class="nav-badge-vms warn">{{ $pendingCount }}</span>
        @endif
      </a>
      <a href="{{ route('vms.active') }}" class="nav-link-vms {{ request()->routeIs('vms.active') ? 'active' : '' }}">
        <i class="ti ti-users"></i> Active Visitors
        @php $insideCount = \App\Models\VmsVisitor::where('status','inside')->count() @endphp
        @if($insideCount > 0)
          <span class="nav-badge-vms" style="background:var(--blue)">{{ $insideCount }}</span>
        @endif
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
        @php $blCount = \App\Models\VmsBlacklist::where('is_active',true)->count() @endphp
        @if($blCount > 0)
          <span class="nav-badge-vms">{{ $blCount }}</span>
        @endif
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

    <div class="sidebar-user">
      <div class="uavatar">{{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}</div>
      <div>
        <div class="uname">{{ auth()->user()->name ?? 'Admin' }}</div>
        <div class="urole">VMS Admin</div>
      </div>
      <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" style="margin-left:auto;color:rgba(255,255,255,0.3);text-decoration:none">
        <i class="ti ti-logout" style="font-size:16px"></i>
      </a>
    </div>
  </aside>

  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

  {{-- MAIN --}}
  <div class="vms-main">

    {{-- TOPBAR --}}
    <div class="vms-topbar">
      <button class="sidebar-toggle" onclick="document.getElementById('vmsSidebar').classList.toggle('open')">
        <i class="ti ti-menu-2" style="font-size:18px;color:var(--muted)"></i>
      </button>
      <div class="topbar-title">
        @yield('page_title','Dashboard')
        @hasSection('page_subtitle')
          <span class="sep">/</span><span class="sub">@yield('page_subtitle')</span>
        @endif
      </div>
      <div class="topbar-search">
        <i class="ti ti-search"></i>
        <input type="text" placeholder="Search visitors, companies…" id="globalSearch">
      </div>
      <div class="topbar-actions">
        <a href="{{ route('vms.approvals') }}" class="topbar-icon-btn" title="Pending approvals">
          <i class="ti ti-bell" style="font-size:17px"></i>
          @if($pendingCount > 0)<div class="dot"></div>@endif
        </a>
        <a href="{{ route('vms.dashboard') }}" class="topbar-icon-btn" title="Refresh">
          <i class="ti ti-refresh" style="font-size:17px"></i>
        </a>
        <div class="topbar-avatar" title="{{ auth()->user()->name ?? 'Admin' }}">
          {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
        </div>
      </div>
    </div>

    {{-- PAGE CONTENT --}}
    <div class="vms-content">
      @yield('content')
    </div>
  </div>
</div>

{{-- Toast container --}}
<div class="vms-toast-wrap" id="toastWrap"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
  // Global CSRF setup
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  // Toast helper
  function showToast(msg, type='success') {
    const icon = type === 'success' ? 'ti-circle-check' : 'ti-alert-circle';
    const t = $(`<div class="vms-toast ${type}">
      <i class="ti ${icon} toast-icon" style="font-size:20px"></i>
      <span>${msg}</span>
    </div>`);
    $('#toastWrap').append(t);
    setTimeout(() => t.fadeOut(300, () => t.remove()), 3500);
  }

  // Auto-refresh active count every 30s
  setInterval(function(){
    $.get('{{ route("vms.ajax.stats") }}', function(data){
      if(data.pending_approvals !== undefined){
        // update badges if elements exist
        if($('#nav-badge-pending').length) $('#nav-badge-pending').text(data.pending_approvals);
        if($('#stat-active').length) $('#stat-active').text(data.active_inside);
      }
    });
  }, 30000);
</script>
@yield('extra_js')
</body>
</html>
