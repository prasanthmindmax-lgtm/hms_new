<style>
/* =============================================
   SIDEBAR – Clean Design
   ============================================= */

/* --- Sidebar base --- */
.pc-sidebar {
  background: #ffffff !important;
  border-right: 1px solid #E5E7EB !important;
  box-shadow: none !important;
  width: 220px !important;
}
.pc-sidebar .navbar-wrapper {
  width: 220px !important;
  background: #ffffff !important;
  display: flex;
  flex-direction: column;
  height: 100%;
}
.pc-header .header-wrapper {
  padding: 0 70px !important;
}
/* --- Hide sidebar when pc-sidebar-hide is toggled --- */
.pc-sidebar.pc-sidebar-hide {
  display: none !important;
}
body.pc-sidebar-hide .pc-sidebar {
  display: none !important;
}

/* --- Header / Brand --- */
.pc-sidebar .m-header {
  border: none !important;
  border-bottom: 1px solid #E5E7EB !important;
  box-shadow: none !important;
  background: #ffffff !important;
  height: auto !important;
  padding: 12px 14px !important;
  display: flex;
  align-items: center;
}
.pc-sidebar .navbar-wrapper {
  width: 260px !important;
}
.menuupdatesall{
  margin: 30px 0 0 0 !important;
  width: 240px !important;
}
.pc-sidebar .m-header .b-brand {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}
.pc-sidebar .m-header img {
  height: 30px;
  width: 80%;
  margin-left: 0 !important;
  background: transparent !important;
}

/* --- Scrollable nav area --- */
.pc-sidebar .navbar-content {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 4px 0 10px;
  background: #ffffff;
}
.pc-sidebar .navbar-content::-webkit-scrollbar { width: 3px; }
.pc-sidebar .navbar-content::-webkit-scrollbar-track { background: transparent; }
.pc-sidebar .navbar-content::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 4px; }

/* --- Section labels & dividers (injected by menu.js) --- */
.sidebar-section-label {
  padding: 12px 14px 4px;
  font-size: 10.5px;
  font-weight: 700;
  color: #9CA3AF;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  line-height: 1;
}
.sidebar-section-divider {
  height: 1px;
  background: #F3F4F6;
  margin: 4px 0;
}

/* --- Kill old theme pc-caption --- */
.pc-sidebar .pc-caption {
  display: none !important;
  padding: 0 !important;
}

/* --- pc-navbar list --- */
.pc-sidebar .pc-navbar {
  list-style: none;
  padding: 0;
  margin: 0;
}
.pc-container {
  margin-left: 230px;
}

/* --- Every menu item link --- */
.pc-sidebar .pc-item > .pc-link {
  display: flex !important;
  align-items: center;
  gap: 10px;
  padding: 7px 0px 7px 30px !important;
  margin: 1px 6px !important;
  border-radius: 7px !important;
  color: #000 !important;
  font-size: 13px !important;
  font-weight: 500;
  line-height: 1.5 !important;
  text-decoration: none !important;
  transition: background-color 0.13s ease, color 0.13s ease;
  cursor: pointer;
  position: relative;
  white-space: break-spaces;
  font-weight:600;
}

/* Kill old theme ::after pseudo-elements */
.pc-sidebar .pc-navbar > .pc-item > .pc-link::after,
.pc-sidebar .pc-navbar > .pc-item .pc-submenu .pc-item > .pc-link::after {
  display: none !important;
}

/* --- Hover --- */
.pc-sidebar .pc-item > .pc-link:hover {
  background-color: #F3F4F6 !important;
  color: #111827 !important;
  text-decoration: none !important;
}

/* ── Active item: level 1 (with icon) ── */
.pc-sidebar .pc-item.active > .pc-link {
  background-color: #EEF2FF !important;
  color: #4338CA !important;
  font-weight: 600 !important;
}
.pc-sidebar .pc-item.active > .pc-link:hover {
  background-color: #E0E7FF !important;
  color: #4338CA !important;
}

/* ── Active icon turns indigo ── */
.pc-sidebar .pc-item.active > .pc-link .pc-micon img {
  opacity: 1 !important;
  filter: invert(27%) sepia(87%) saturate(1200%) hue-rotate(228deg) brightness(90%) contrast(98%) !important;
}

/* ── Active level-2 child (no icon) ── */
.pc-sidebar .pc-submenu > .pc-item.active > .pc-link {
  background-color: #EEF2FF !important;
  color: #4338CA !important;
  font-weight: 600 !important;
}

/* ── Active level-3 sub-child (no icon) ── */
.pc-sidebar .pc-submenu-lvl3 > .pc-item.active > .pc-link {
  background-color: #EEF2FF !important;
  color: #4338CA !important;
  font-weight: 600 !important;
}

/* ── Parent dropdown is "active" when a child is active:
      show it with a soft left border accent ── */
.pc-sidebar .pc-item.pc-hasmenu.active > .pc-link {
  background-color: transparent !important;
  color: #4338CA !important;
  border-left: 3px solid #4338CA;
  padding-left: 27px !important;   /* compensate 3px border */
}
.pc-sidebar .pc-item.pc-hasmenu.active > .pc-link .pc-micon img {
  opacity: 1 !important;
  filter: invert(27%) sepia(87%) saturate(1200%) hue-rotate(228deg) brightness(90%) contrast(98%) !important;
}

/* --- Icon --- */
.pc-sidebar .pc-micon {
  width: 20px !important;
  height: 20px !important;
  min-width: 20px;
  display: flex !important;
  align-items: center;
  justify-content: center;
  margin-right: 0 !important;
  flex-shrink: 0;
}
.pc-sidebar .pc-micon img {
  width: 17px !important;
  height: 17px !important;
  opacity: 0.45;
  transition: opacity 0.13s ease;
  display: block;
}
.pc-sidebar .pc-item > .pc-link:hover .pc-micon img {
  opacity: 0.7;
}
.pc-sidebar .pc-item.active > .pc-link .pc-micon img {
  opacity: 1;
  filter: invert(27%) sepia(87%) saturate(1200%) hue-rotate(228deg) brightness(90%) contrast(98%);
}

/* --- Text label --- */
.pc-sidebar .pc-mtext {
  flex: 1;
  font-size: 13px;
  line-height: 1.5;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* --- Chevron arrow (for dropdowns) --- */
.pc-sidebar .menu-arrow {
  width: 14px;
  height: 14px;
  opacity: 0.35;
  flex-shrink: 0;
  transition: transform 0.2s ease, opacity 0.13s ease;
}
.pc-sidebar .pc-hasmenu > .pc-link:hover .menu-arrow {
  opacity: 0.6;
}
.pc-sidebar .pc-hasmenu.open > .pc-link .menu-arrow {
  transform: rotate(180deg);
  opacity: 0.6;
}

/* --- Badges --- */
.pc-sidebar .menu-badge-count {
  background: #4338CA;
  color: #fff;
  font-size: 10.5px;
  font-weight: 700;
  min-width: 19px;
  height: 19px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 5px;
  flex-shrink: 0;
  line-height: 1;
}
.pc-sidebar .menu-badge-new {
  background: #D1FAE5;
  color: #065F46;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 20px;
  flex-shrink: 0;
  line-height: 1.4;
}

/* ══════════════════════════════════════════
   Level-2 submenu  (NO icon)
   ══════════════════════════════════════════ */
.pc-sidebar .pc-submenu {
  list-style: none;
  padding: 1px 0;
  margin: 0;
  display: none;
}
/* Level-2 link (leaf or dropdown header) */
.pc-sidebar .pc-submenu > .pc-item > .pc-link {
  padding: 6px 10px 6px 36px !important;
  font-size: 12.5px !important;
  color: #6B7280 !important;
  font-weight: 400;
  line-height: 1.5 !important;
  gap: 0 !important;
}
.pc-sidebar .pc-submenu > .pc-item > .pc-link:hover {
  background-color: #F3F4F6 !important;
  color: #374151 !important;
}
.pc-sidebar .pc-submenu > .pc-item.active > .pc-link {
  color: #4338CA !important;
  background-color: #EEF2FF !important;
  font-weight: 500;
}
/* Level-2 dropdown: spread text + chevron */
.pc-sidebar .pc-submenu > .pc-item.pc-hasmenu > .pc-link {
  justify-content: space-between !important;
}
/* Smaller chevron for level-2 */
.pc-sidebar .pc-submenu .menu-arrow {
  width: 12px !important;
  height: 12px !important;
}

/* ══════════════════════════════════════════
   Level-3 sub-submenu  (deeper indent, NO icon)
   ══════════════════════════════════════════ */
.pc-sidebar .pc-submenu-lvl3 {
  list-style: none;
  padding: 1px 0;
  margin: 0;
  display: none;
}
.pc-sidebar .pc-submenu-lvl3 > .pc-item > .pc-link {
  padding: 5px 10px 5px 50px !important;
  font-size: 12px !important;
  color: #9CA3AF !important;
  font-weight: 400;
  line-height: 1.5 !important;
  gap: 0 !important;
}
.pc-sidebar .pc-submenu-lvl3 > .pc-item > .pc-link:hover {
  background-color: #F9FAFB !important;
  color: #374151 !important;
}
.pc-sidebar .pc-submenu-lvl3 > .pc-item.active > .pc-link {
  color: #4338CA !important;
  background-color: #EEF2FF !important;
  font-weight: 500;
}

/* kill old theme submenu vertical lines */
.pc-sidebar .pc-navbar > .pc-item .pc-submenu::after,
.pc-sidebar .pc-navbar > .pc-item .pc-submenu-lvl3::after {
  display: none !important;
}

/* -----------------------------------------------
   Footer / User Profile Card + Dropdown
   ----------------------------------------------- */
.pc-sidebar-footer {
  border-top: 1px solid #E5E7EB;
  padding: 8px 10px;
  background: #ffffff;
  flex-shrink: 0;
}

/* The clickable card row */
.sidebar-user-card {
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 6px 6px;
  border-radius: 8px;
  cursor: pointer;
  user-select: none;
  transition: background 0.13s ease;
  outline: none;
}
.sidebar-user-card:hover,
.sidebar-user-card:focus {
  background: #F3F4F6;
}

/* Avatar circle */
.sidebar-user-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4338CA 0%, #7C3AED 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  flex-shrink: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Name + role */
.sidebar-user-info {
  flex: 1;
  min-width: 0;
}
.sidebar-user-name {
  font-size: 12.5px;
  font-weight: 600;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.35;
}
.sidebar-user-role {
  font-size: 11px;
  color: #9CA3AF;
  line-height: 1.3;
}

/* Three-dot icon */
.sidebar-user-dots {
  color: #9CA3AF;
  font-size: 19px;
  line-height: 1;
  flex-shrink: 0;
  padding: 0 2px;
}

/* Dropdown menu that pops up above the card */
.sidebar-user-dropdown {
  min-width: 210px;
  border: 1px solid #E5E7EB;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.10);
  padding: 6px;
  background: #fff;
  margin-bottom: 6px;
}
.sidebar-user-dropdown .dropdown-header-info {
  padding: 8px 10px 10px;
  border-bottom: 1px solid #F3F4F6;
  margin-bottom: 4px;
}
.sidebar-user-dropdown .dhi-name {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.sidebar-user-dropdown .dhi-role {
  font-size: 11px;
  color: #9CA3AF;
}
.sidebar-user-dropdown .dropdown-item {
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 8px 10px;
  border-radius: 7px;
  font-size: 13px;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  transition: background 0.12s ease;
  border: none;
  background: transparent;
  width: 100%;
  text-align: left;
  text-decoration: none;
}
.sidebar-user-dropdown .dropdown-item:hover {
  background: #F3F4F6;
  color: #111827;
}
.sidebar-user-dropdown .dropdown-item.text-danger {
  color: #DC2626 !important;
}
.sidebar-user-dropdown .dropdown-item.text-danger:hover {
  background: #FEF2F2;
}
.sidebar-user-dropdown .dropdown-item i {
  font-size: 16px;
  flex-shrink: 0;
  width: 18px;
  text-align: center;
}
.sidebar-user-dropdown hr {
  margin: 4px 0;
  border-color: #F3F4F6;
}
</style>

<nav class="pc-sidebar">
  <div class="navbar-wrapper">

    {{-- Brand / Logo --}}
    <div class="m-header">
      <a href="{{ route('superadmin.dashboard') }}" class="b-brand text-primary">
        <img src="{{ asset('/assets/images/dralogos.png') }}"  alt="logo" />
      </a>
    </div>

    {{-- Navigation (populated by menu.js) --}}
    <div class="navbar-content">
      <ul class="pc-navbar menuupdatesall">
        {{-- Items injected via AJAX by /public/menu/menu.js --}}
      </ul>
    </div>

  </div>

  {{-- Footer: logged-in user card with dropdown --}}
  <div class="pc-sidebar-footer">
    @php
      $sidebarUser     = auth()->user();
      $sidebarName     = $sidebarUser ? ($sidebarUser->user_fullname ?? $sidebarUser->name ?? 'User') : 'User';
      $sidebarEmail    = $sidebarUser ? ($sidebarUser->email ?? '') : '';
      $roleMap         = [1 => 'Super Admin', 2 => 'Admin', 3 => 'Staff'];
      $sidebarRole     = $sidebarUser ? ($roleMap[$sidebarUser->role_id] ?? 'User') : '';
      $words           = array_filter(explode(' ', $sidebarName));
      $sidebarInitials = '';
      foreach (array_slice($words, 0, 2) as $w) {
        $sidebarInitials .= strtoupper($w[0]);
      }
    @endphp

    {{-- Bootstrap dropup anchored to the card --}}
    <div class="dropup">

      <div class="sidebar-user-card"
           data-bs-toggle="dropdown"
           data-bs-auto-close="true"
           aria-expanded="false"
           tabindex="0">
        <div class="sidebar-user-avatar">{{ $sidebarInitials ?: 'U' }}</div>
        <div class="sidebar-user-info">
          <div class="sidebar-user-name">{{ $sidebarName }}</div>
          <div class="sidebar-user-role">{{ $sidebarRole }}</div>
        </div>
        <div class="sidebar-user-dots">&#8942;</div>
      </div>

      <ul class="dropdown-menu sidebar-user-dropdown" style="width:100%;">

        {{-- User info header --}}
        <li>
          <div class="dropdown-header-info">
            <div class="dhi-name">{{ $sidebarName }}</div>
            <div class="dhi-role">{{ $sidebarEmail }}</div>
          </div>
        </li>

        {{-- Change Password → triggers the modal from superadminheader --}}
        <li>
          <button type="button"
                  class="dropdown-item"
                  data-bs-toggle="modal"
                  data-bs-target="#changePasswordModal">
            <i class="ti ti-key"></i>
            Change Password
          </button>
        </li>

        <li><hr></li>

        {{-- Log Out --}}
        <li>
          <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
              <i class="ti ti-power"></i>
              Log Out
            </button>
          </form>
        </li>

      </ul>
    </div>{{-- /dropup --}}

  </div>
</nav>
