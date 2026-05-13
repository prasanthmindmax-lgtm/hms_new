<style>
    .pc-header .header-user-profile .pc-head-link {
        padding:12px 0 0 !important;
    }
</style>
<header class="pc-header">
  <div class="header-wrapper">
    <!-- [Mobile Media Block] start -->
    <div class="me-auto pc-mob-drp">
    <ul class="list-unstyled">
        <!-- ======= Menu collapse Icon ===== -->
        <li class="pc-h-item pc-sidebar-collapse">
        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ti ti-menu-2"></i>
        </a>
        </li>
        <li class="pc-h-item pc-sidebar-popup">
        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ti ti-menu-2"></i>
        </a>
        </li>
        <!-- <li class="pc-h-item d-none d-md-inline-flex">
        <a href=""><button type="button" class="btn btn-outline-secondary"><i class="ti ti-file-text me-1"></i>Create Ticket</button></li></a>
        </li>
        <li class="pc-h-item d-none d-md-inline-flex">
        <a href=""><button type="button" class="btn btn-outline-secondary"><i class="ti ti-settings me-1"></i>Home Page</button></a>
        </li> -->
    </ul>
    </div>
    <!-- [Mobile Media Block end] -->
    <div class="ms-auto">
    <ul class="list-unstyled">

        {{-- ── App Switcher: show for access_limits=1 OR user has any VMS menu assigned ── --}}
        @php
          $showVmsSwitch = false;
          if (auth()->check()) {
              $u = auth()->user();
              // access_limits = 1 → full superadmin, always show
              if ((int)($u->access_limits ?? 0) === 1) {
                  $showVmsSwitch = true;
              } else {
                  // Check if user has at least one VMS menu assigned in user_menus
                  $showVmsSwitch = DB::table('user_menus')
                      ->join('menus', 'user_menus.menu_id', '=', 'menus.id')
                      ->where('user_menus.user_id', $u->id)
                      ->where('user_menus.status', '1')
                      ->where('menus.active_ids', 'vms_color')
                      ->exists();
              }
          }
        @endphp
        @if($showVmsSwitch)
        <li class="pc-h-item" style="position:relative;display:flex;align-items:center" id="appSwitcherWrap">
          <a href="#" class="pc-head-link" id="appSwitcherBtn" title="Switch App"
             onclick="event.preventDefault();toggleAppSwitcher()"
             style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;transition:background 0.15s;padding:0">
            <i class="ti ti-grid-dots" style="font-size:20px;line-height:1"></i>
          </a>
          <div id="appSwitcherPanel"
               style="display:none;position:absolute;top:calc(100% + 8px);right:0;
                      background:#fff;border:1px solid #e5e7eb;border-radius:16px;
                      box-shadow:0 12px 40px rgba(0,0,0,0.14);padding:20px;
                      min-width:220px;z-index:9999">
            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:14px">Switch Application</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
              <a href="{{ route('superadmin.dashboard') }}"
                 style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 10px;
                        border:1.5px solid #e5e7eb;border-radius:12px;text-decoration:none;
                        transition:all 0.15s;background:{{ request()->routeIs('superadmin.*') ? '#f0fdf4' : '#fff' }};
                        {{ request()->routeIs('superadmin.*') ? 'border-color:#86efac' : '' }}"
                 onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'"
                 onmouseout="this.style.background='{{ request()->routeIs('superadmin.*') ? '#f0fdf4' : '#fff' }}';this.style.borderColor='{{ request()->routeIs('superadmin.*') ? '#86efac' : '#e5e7eb' }}'">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#0f2d4a,#1e4976);border-radius:12px;display:flex;align-items:center;justify-content:center">
                  <i class="ti ti-building-hospital" style="font-size:22px;color:#fff"></i>
                </div>
                <span style="font-size:12px;font-weight:700;color:#1e293b">HMS</span>
                <span style="font-size:10px;color:#64748b;text-align:center">Hospital Management</span>
              </a>
              <a href="{{ route('vms.dashboard') }}"
                 style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 10px;
                        border:1.5px solid #e5e7eb;border-radius:12px;text-decoration:none;
                        transition:all 0.15s;background:{{ request()->routeIs('vms.*') ? '#f0fdf4' : '#fff' }};
                        {{ request()->routeIs('vms.*') ? 'border-color:#86efac' : '' }}"
                 onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'"
                 onmouseout="this.style.background='{{ request()->routeIs('vms.*') ? '#f0fdf4' : '#fff' }}';this.style.borderColor='{{ request()->routeIs('vms.*') ? '#86efac' : '#e5e7eb' }}'">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#1a7f64,#16a37e);border-radius:12px;display:flex;align-items:center;justify-content:center">
                  <i class="ti ti-qrcode" style="font-size:22px;color:#fff"></i>
                </div>
                <span style="font-size:12px;font-weight:700;color:#1e293b">VMS</span>
                <span style="font-size:10px;color:#64748b;text-align:center">Visitor Management</span>
              </a>
            </div>
          </div>
        </li>
        @endif

        <li class="dropdown pc-h-item header-user-profile">
        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="false"
            data-bs-auto-close="outside"
            aria-expanded="false">
            <img src="{{ asset('/assets/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar" />
        </a>
        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
            <!-- <h5 class="m-0">Profile</h5> -->
            </div>
            <div class="dropdown-body">
            <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                <div class="d-flex mb-1" style="margin-left: 61px;">
                <div class="flex-shrink-0">
                    <img src="{{ asset('/assets/images/user/avatar-2.jpg') }}" alt="user-image11" class="user-avtar wid-35" />
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="flex-grow-1 ms-3">

                        <h6 class="mb-0">{{ $admin->username }}</h6>
                        <small>{{ $admin->role_id }}</small>
                    </div>
                </div>
                </div>
                <hr class="border-secondary border-opacity-50" />
                <div class="d-flex gap-2 mb-3" style="margin-left: 23px;width: 90%;">
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                  <i class="ti ti-key"></i> Change Password
                </button>
                <button  class="btn btn-outline-primary" >
                  <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault();
                                    this.closest('form').submit();" style="text-color:#fffff !important;">
                  <i class="ti ti-power"></i>  {{ __('Log Out') }}
                    </x-dropdown-link>
              </form>
                </button>

                </div>
            </div>
            </div>
        </div>
        </li>
    </ul>
</div>
@if($admin->access_limits==1 || $admin->access_limits==4)
    @include('components.notification-bell')
@endif

    </div>
</header>
@include('partials.change_password_modal')

<script>
function toggleAppSwitcher() {
  const p = document.getElementById('appSwitcherPanel');
  if (p) p.style.display = p.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
  const wrap = document.getElementById('appSwitcherWrap');
  if (wrap && !wrap.contains(e.target)) {
    const panel = document.getElementById('appSwitcherPanel');
    if (panel) panel.style.display = 'none';
  }
});
</script>