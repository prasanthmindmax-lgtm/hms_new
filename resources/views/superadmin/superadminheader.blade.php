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

    </div>
</header>
@include('partials.change_password_modal')