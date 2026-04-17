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
            @php $createUser = App\Models\UserProfile::where('user_id', auth()->user()->id)->first(); @endphp
            <h4>Welcome {{ $createUser->fullname }}</h4>
        </li> -->
    </ul>
    </div>
    <!-- [Mobile Media Block end] -->
    <div class="ms-auto">
    <ul class="list-unstyled">
        <!--<li class="dropdown pc-h-item" id="countUpdate"><a class="pc-head-link dropdown-toggle arrow-none me-0"
            data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
            aria-expanded="false"><svg class="pc-icon">
                <use xlink:href="#custom-notification"></use>
            </svg>
            <span class="badge bg-success pc-h-badge">
                @php $notiCounts = App\Models\TicketModel::select('ticket_details.*')
                                    ->where('staff_id', auth()->user()->id)->where('ticket_details.is_read', 3)->get();    
                @endphp 
                {{ count($notiCounts) }}
            </span>
            </a>
          <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
                <h5 class="m-0">Notifications</h5>
                <a href="{{ route('staff.staffNotifyTickets', [auth()->user()->id]) }}" class="pc-link">Mark all read</a>
            </div>
            <div class="dropdown-body text-wrap header-notification-scroll position-relative"
                style="max-height: calc(100vh - 215px)">
                @if ($notiCounts)
                    @foreach ($notiCounts as $notiCount)
                        <div class="card mb-2">
                            <a href="{{ route('staff.staffNotifyTickets', [auth()->user()->id]) }}">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                        <svg class="pc-icon text-primary">
                                            <use xlink:href="#custom-document"></use> 
                                        </svg>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                        <span class="float-end text-sm text-muted">{{  date('d-m-Y', strtotime($notiCount->created_at)) ?? '' }}</span>
                                        <h5 class="text-body mb-2">{{ $notiCount->ticket_no }}-{{ $notiCount->subject }}</h5>
                                        <p class="mb-0"
                                            >{{ $notiCount->description }}</p
                                        >
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
            </div>
        </li> -->
        <!--<li class="dropdown pc-h-item">
        <a
            class="pc-head-link dropdown-toggle arrow-none me-0"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="false"
            aria-expanded="false"
        >
            <svg class="pc-icon">
            <use xlink:href="#custom-sun-1"></use>
            </svg>
        </a>
        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
            <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
            <svg class="pc-icon">
                <use xlink:href="#custom-moon"></use>
            </svg>
            <span>Dark</span>
            </a>
            <a href="#!" class="dropdown-item" onclick="layout_change('light')">
            <svg class="pc-icon">
                <use xlink:href="#custom-sun-1"></use>
            </svg>
            <span>Light</span>
            </a>
            <a href="#!" class="dropdown-item" onclick="layout_change_default()">
            <svg class="pc-icon">
                <use xlink:href="#custom-setting-2"></use>
            </svg>
            <span>Default</span>
            </a>
        </div>
        </li>-->
        </li>
       
        
        <li class="dropdown pc-h-item header-user-profile">
        <a
            class="pc-head-link dropdown-toggle arrow-none me-0"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="false"
            data-bs-auto-close="outside"
            aria-expanded="false"
        >
            <img src="{{ asset('assets/images/user/avatar-2.jpg') }} " alt="user-image" class="user-avtar" />
        </a>
        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
            <!-- <h5 class="m-0">Profile</h5> -->
            </div>
            <div class="dropdown-body">
            <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                <div class="d-flex mb-1" style="margin-left: 61px;">
                <div class="flex-shrink-0">
                    <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar wid-35" />
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="flex-grow-1 ms-3">

                        <h6 class="mb-0">{{ $admin->username }}</h6>
                        <small>{{ $admin->role }}</small>
                    </div>
                </div>
                </div>
                <hr class="border-secondary border-opacity-50" />
                <!--<a href="#" class="dropdown-item">
                 <span>
                    <svg class="pc-icon text-muted me-2">
                    <use xlink:href="#custom-lock-outline"></use>
                    </svg>
                    <span>Change Password</span>
                </span>
                </a> -->
                <div class="d-grid mb-3" style="margin-left: 103px;width: 49%;">
                <button class="btn btn-outline-primary" >
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript">
(function () {
  var POLL_MS = 30000;
  var timer = null;
  function tick() {
    if (document.hidden) return;
    $("#countUpdate").load(window.location.href + " #countUpdate");
  }
  function startPoll() {
    stopPoll();
    tick();
    timer = setInterval(tick, POLL_MS);
  }
  function stopPoll() {
    if (timer) { clearInterval(timer); timer = null; }
  }
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) stopPoll();
    else startPoll();
  });
  $(function () {
    if (!document.hidden) startPoll();
  });
})();
</script>