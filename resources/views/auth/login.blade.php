<!doctype html>
<html lang="en">
  <!-- [Head] start -->

<head>
  <title>Login</title>
<!-- [Meta] -->
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta
  name="description"
  content="  Dr. Aravind's IVF Fertility & Pregnancy Centre is the best fertility hospital in India. Our vision is to help childless couples enjoy the joy of parenthood with world class fertility treatments and having 30+ years of experience. Book your appointments now!"
/>
<meta
  name="keywords"
  content="  Dr. Aravind's IVF Fertility & Pregnancy Centre is the best fertility hospital in India. Our vision is to help childless couples enjoy the joy of parenthood with world class fertility treatments and having 30+ years of experience. Book your appointments now!"
/>
<meta name="author" content="The Mindmax" />

<!-- [Favicon] icon -->
<link rel="icon" href="{{ asset('/assets/images/favi.jpg') }}" type="image/x-icon" />
<!-- [Font] Family -->
<link rel="stylesheet" href="{{ asset('/assets/fonts/inter/inter.css') }}" id="main-font-link" />
<!-- [phosphor Icons] https://phosphoricons.com/ -->
<link rel="stylesheet" href="{{ asset('/assets/fonts/phosphor/duotone/style.css') }}" />
<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{ asset('/assets/fonts/tabler-ic') }}" />
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{ asset('/assets/fonts/feather.css') }}" />
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{ asset('/assets/fonts/fontawesome.css') }}" />
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{ asset('/assets/fonts/material.css') }}" />
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/style-preset.css') }}" />

</head>
  <!-- [Head] end -->
  <!-- [Body] Start -->

  <body @@bodySetup>
    <!-- [ Pre-loader ] start -->
    <div class="page-loader">
    <div class="bar"></div>
    </div>
<!-- [ Pre-loader ] End -->

    <div class="auth-main">
      <div class="auth-wrapper v2">
        <div class="auth-sidecontent">
        <div>&nbsp;</div>
          {{-- <img src="{{ asset('/assets/images/19767.jpg') }}" alt="images" class="img-fluid img-auth-side" style="height:500px;width:600px;"/> --}}
        </div>
        <div class="auth-form">
          <div class="card my-5">
            <div class="card-body">

              <div class="text-center" >
                <a href="#"><img src="{{ asset('/assets/images/dralogos.png') }} " alt="img" style="width: 100%;
                height: auto;
                overflow: hidden;"/></a>
              </div>
              <hr />
              <x-input-error :messages="$errors->get('username')" class="mt-2" />
              <x-input-error :messages="$errors->get('password')" class="mt-2" />
              <form id="loginForm" method="POST" action="{{ route('login1') }}">
              @csrf
              <input type="hidden" name="login_latitude" id="login_latitude" value="" />
              <input type="hidden" name="login_longitude" id="login_longitude" value="" />
              <input type="hidden" name="login_location_accuracy" id="login_location_accuracy" value="" />
              <input type="hidden" name="login_geo_status" id="login_geo_status" value="" />
              <div class="mb-3">
                <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username" />

              </div>
              <div class="mb-3">
                <input type="password" name="password" class="form-control" id="floatingInput1" placeholder="Password" />

              </div>
              <div class="d-flex mt-1 justify-content-between align-items-center">
                <div class="form-check">
                  <input style="
                  border-color: #4f4f4f;
                  background-color: #105fcf;
              " class="form-check-input input-primary" type="checkbox" id="customCheckc1" checked="" />
                  <label class="form-check-label text-muted" for="customCheckc1">Remember me?</label>
                </div>
                <!-- <h6 class="text-secondary f-w-400 mb-0">
                  <a href="forgot-password-v2.html"> Forgot Password? </a>
                </h6> -->
              </div>
              <div class="d-grid mt-4" style="
              margin-left: -13px;
          ">
                <!-- <button type="button" class="btn btn-primary">Login</button> -->
                <x-primary-button class="ms-3 btn btn-primary">
                {{ __('Log in') }}
                </x-primary-button>
              </div>
              </form>

               <div class="saprator my-3">
                <span>OR</span>
              </div>

              <div class="d-flex justify-content-between align-items-end mt-4">
                <h6 class="f-w-500 mb-0">Face ID</h6>
                <h6 class="f-w-500 mb-0">Finger Print</h6>
              </div>

              <div style="display: flex; align-items: center; gap: 2px; margin-top: 10px;    margin-left: 110px;">
                <p style="margin: 0;margin-left: 39px;font-size: 11px;">Powered by</p>
                <img src="{{ asset('/assets/images/pages/powed by.png') }}" alt="MaxCompany Logo" style="width: 74px; margin-left: 2px; height: auto;">


            </div>


            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ Main Content ] end -->
     <!-- Required Js -->
<script src="{{ asset('/assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('/assets/js/plugins/simplebar.min.js') }}.."></script>
<script src="{{ asset('/assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('/assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('/assets/js/pcoded.js') }}"></script>
<script src="{{ asset('/assets/js/plugins/feather.min.js') }}"></script>
<!-- [Page Specific JS] end -->
<script>
  layout_change('light');
</script>
<script>
  layout_theme_contrast_change('false');
</script>
<script>
  change_box_container('false');
</script>
<script>
  layout_caption_change('true');
</script>
<script>
  layout_rtl_change('false');
</script>
<script>
  preset_change('preset-1');
</script>
<!-- <div class="pct-c-btn">
  <a href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_pc_layout">
    <i class="ph-duotone ph-gear-six"></i>
  </a>
</div> -->
<div class="offcanvas border-0 pct-offcanvas offcanvas-end" tabindex="-1" id="offcanvas_pc_layout">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Settings</h5>
    <button type="button" class="btn btn-icon btn-link-danger ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"
      ><i class="ti ti-x"></i
    ></button>
  </div>
  <div class="pct-body customizer-body">
    <div class="offcanvas-body py-0">
      <ul class="list-group list-group-flush">
        <li class="list-group-item">
          <div class="pc-dark">
            <h6 class="mb-1">Theme Mode</h6>
            <p class="text-muted text-sm">Choose light or dark mode or Auto</p>
            <div class="row theme-color theme-layout">
              <div class="col-4">
                <div class="d-grid">
                  <button
                    class="preset-btn btn active"
                    data-value="true"
                    onclick="layout_change('light');"
                    data-bs-toggle="tooltip"
                    title="Light"
                  >
                    <svg class="pc-icon text-warning">
                      <use xlink:href="#custom-sun-1"></use>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="col-4">
                <div class="d-grid">
                  <button class="preset-btn btn" data-value="false" onclick="layout_change('dark');" data-bs-toggle="tooltip" title="Dark">
                    <svg class="pc-icon">
                      <use xlink:href="#custom-moon"></use>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="col-4">
                <div class="d-grid">
                  <button
                    class="preset-btn btn"
                    data-value="default"
                    onclick="layout_change_default();"
                    data-bs-toggle="tooltip"
                    title="Automatically sets the theme based on user's operating system's color scheme."
                  >
                    <span class="pc-lay-icon d-flex align-items-center justify-content-center">
                      <i class="ph-duotone ph-cpu"></i>
                    </span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <h6 class="mb-1">Theme Contrast</h6>
          <p class="text-muted text-sm">Choose theme contrast</p>
          <div class="row theme-contrast">
            <div class="col-6">
              <div class="d-grid">
                <button
                  class="preset-btn btn"
                  data-value="true"
                  onclick="layout_theme_contrast_change('true');"
                  data-bs-toggle="tooltip"
                  title="True"
                >
                  <svg class="pc-icon">
                    <use xlink:href="#custom-mask"></use>
                  </svg>
                </button>
              </div>
            </div>
            <div class="col-6">
              <div class="d-grid">
                <button
                  class="preset-btn btn active"
                  data-value="false"
                  onclick="layout_theme_contrast_change('false');"
                  data-bs-toggle="tooltip"
                  title="False"
                >
                  <svg class="pc-icon">
                    <use xlink:href="#custom-mask-1-outline"></use>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <h6 class="mb-1">Custom Theme</h6>
          <p class="text-muted text-sm">Choose your primary theme color</p>
          <div class="theme-color preset-color">
            <a href="#!" data-bs-toggle="tooltip" title="Blue" class="active" data-value="preset-1"><i class="ti ti-checks"></i></a>
            <a href="#!" data-bs-toggle="tooltip" title="Indigo" data-value="preset-2"><i class="ti ti-checks"></i></a>
            <a href="#!" data-bs-toggle="tooltip" title="Purple" data-value="preset-3"><i class="ti ti-checks"></i></a>
            <a href="#!" data-bs-toggle="tooltip" title="Pink" data-value="preset-4"><i class="ti ti-checks"></i></a>
            <a href="#!" data-bs-toggle="tooltip" title="Red" data-value="preset-5"><i class="ti ti-checks"></i></a>
            <a href="#!" data-bs-toggle="tooltip" title="Orange" data-value="preset-6"><i class="ti ti-checks"></i></a>
            <a href="#!" data-bs-toggle="tooltip" title="Yellow" data-value="preset-7"><i class="ti ti-checks"></i></a>
            <a href="#!" data-bs-toggle="tooltip" title="Green" data-value="preset-8"><i class="ti ti-checks"></i></a>
            <a href="#!" data-bs-toggle="tooltip" title="Teal" data-value="preset-9"><i class="ti ti-checks"></i></a>
            <a href="#!" data-bs-toggle="tooltip" title="Cyan" data-value="preset-10"><i class="ti ti-checks"></i></a>
          </div>
        </li>
        <li class="list-group-item">
          <h6 class="mb-1">Theme layout</h6>
          <p class="text-muted text-sm">Choose your layout</p>
          <div class="theme-main-layout d-flex align-center gap-1 w-100">
            <a href="#!" data-bs-toggle="tooltip" title="Vertical" class="active" data-value="vertical">
              <img src="{{ asset('/assets/images/customizer/caption-on.svg') }}" alt="img" class="img-fluid" />
            </a>
            <a href="#!" data-bs-toggle="tooltip" title="Horizontal" data-value="horizontal">
              <img src="{{ asset('/assets/images/customizer/horizontal.svg') }}" alt="img" class="img-fluid" />
            </a>
            <a href="#!" data-bs-toggle="tooltip" title="Color Header" data-value="color-header">
              <img src="{{ asset('/assets/images/customizer/color-header.svg') }}" alt="img" class="img-fluid" />
            </a>
            <a href="#!" data-bs-toggle="tooltip" title="Compact" data-value="compact">
              <img src="{{ asset('/assets/images/customizer/compact.svg') }}" alt="img" class="img-fluid" />
            </a>
            <a href="#!" data-bs-toggle="tooltip" title="Tab" data-value="tab">
              <img src="{{ asset('/assets/images/customizer/tab.svg') }}" alt="img" class="img-fluid" />
            </a>
          </div>
        </li>
        <li class="list-group-item">
          <h6 class="mb-1">Sidebar Caption</h6>
          <p class="text-muted text-sm">Sidebar Caption Hide/Show</p>
          <div class="row theme-color theme-nav-caption">
            <div class="col-6">
              <div class="d-grid">
                <button
                  class="preset-btn btn-img btn active"
                  data-value="true"
                  onclick="layout_caption_change('true');"
                  data-bs-toggle="tooltip"
                  title="Caption Show"
                >
                  <img src="{{ asset('/assets/images/customizer/caption-on.svg') }}" alt="img" class="img-fluid" />
                </button>
              </div>
            </div>
            <div class="col-6">
              <div class="d-grid">
                <button
                  class="preset-btn btn-img btn"
                  data-value="false"
                  onclick="layout_caption_change('false');"
                  data-bs-toggle="tooltip"
                  title="Caption Hide"
                >
                  <img src="{{ asset('/assets/images/customizer/caption-off.svg') }}" alt="img" class="img-fluid" />
                </button>
              </div>
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="pc-rtl">
            <h6 class="mb-1">Theme Layout</h6>
            <p class="text-muted text-sm">LTR/RTL</p>
            <div class="row theme-color theme-direction">
              <div class="col-6">
                <div class="d-grid">
                  <button
                    class="preset-btn btn-img btn active"
                    data-value="false"
                    onclick="layout_rtl_change('false');"
                    data-bs-toggle="tooltip"
                    title="LTR"
                  >
                    <img src="{{ asset('/assets/images/customizer/ltr.svg') }}" alt="img" class="img-fluid" />
                  </button>
                </div>
              </div>
              <div class="col-6">
                <div class="d-grid">
                  <button
                    class="preset-btn btn-img btn"
                    data-value="true"
                    onclick="layout_rtl_change('true');"
                    data-bs-toggle="tooltip"
                    title="RTL"
                  >
                    <img src="../assets/images/customizer/rtl.svg" alt="img" class="img-fluid" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="list-group-item pc-box-width">
          <div class="pc-container-width">
            <h6 class="mb-1">Layout Width</h6>
            <p class="text-muted text-sm">Choose Full or Container Layout</p>
            <div class="row theme-color theme-container">
              <div class="col-6">
                <div class="d-grid">
                  <button
                    class="preset-btn btn-img btn active"
                    data-value="false"
                    onclick="change_box_container('false')"
                    data-bs-toggle="tooltip"
                    title="Full Width"
                  >
                    <img src="../assets/images/customizer/full.svg" alt="img" class="img-fluid" />
                  </button>
                </div>
              </div>
              <div class="col-6">
                <div class="d-grid">
                  <button
                    class="preset-btn btn-img btn"
                    data-value="true"
                    onclick="change_box_container('true')"
                    data-bs-toggle="tooltip"
                    title="Fixed Width"
                  >
                    <img src="../assets/images/customizer/fixed.svg" alt="img" class="img-fluid" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="d-grid">
            <button class="btn btn-light-danger" id="layoutreset">Reset Layout</button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>

<script type="text/javascript">
(function () {
  var form = document.getElementById('loginForm');
  if (!form) return;
  /** Best-effort precise location: GPS when available; avoids stale/cached coarse fixes. */
  var GEO_OPTS = { enableHighAccuracy: true, maximumAge: 0, timeout: 30000 };
  var WATCH_MS = 14000;
  var ACC_GOOD_M = 25;

  form.addEventListener('submit', function (e) {
    if (form.dataset.geoHandled === '1') return;
    e.preventDefault();
    var lat = document.getElementById('login_latitude');
    var lng = document.getElementById('login_longitude');
    var accEl = document.getElementById('login_location_accuracy');
    var st = document.getElementById('login_geo_status');
    var finished = false;
    var watchId = null;
    var timer = null;
    var best = null;

    function finishSubmit(status) {
      if (st) st.value = status || '';
      form.dataset.geoHandled = '1';
      form.submit();
    }
    function clearTimers() {
      if (timer) {
        clearTimeout(timer);
        timer = null;
      }
      if (watchId != null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
      }
    }
    function submitGranted(b) {
      if (finished) return;
      finished = true;
      clearTimers();
      if (lat) lat.value = String(b.latitude);
      if (lng) lng.value = String(b.longitude);
      if (accEl && b.accuracy != null) accEl.value = String(b.accuracy);
      finishSubmit('granted');
    }
    function submitError(err) {
      if (finished) return;
      finished = true;
      clearTimers();
      var code = err && err.code;
      if (code === 1) finishSubmit('denied');
      else if (code === 3) finishSubmit('timeout');
      else finishSubmit('unavailable');
    }
    function consider(pos) {
      if (finished) return;
      var a = pos.coords.accuracy;
      if (a == null || isNaN(a)) a = 999999;
      if (!best || a < best.accuracy) {
        best = { latitude: pos.coords.latitude, longitude: pos.coords.longitude, accuracy: a };
      }
      if (a <= ACC_GOOD_M) {
        submitGranted({
          latitude: pos.coords.latitude,
          longitude: pos.coords.longitude,
          accuracy: pos.coords.accuracy
        });
      }
    }
    function endWatchPhase() {
      if (finished) return;
      clearTimers();
      if (best) {
        submitGranted(best);
        return;
      }
      navigator.geolocation.getCurrentPosition(
        function (pos) {
          if (finished) return;
          finished = true;
          if (lat) lat.value = String(pos.coords.latitude);
          if (lng) lng.value = String(pos.coords.longitude);
          if (accEl && pos.coords.accuracy != null) accEl.value = String(pos.coords.accuracy);
          finishSubmit('granted');
        },
        submitError,
        GEO_OPTS
      );
    }

    if (!navigator.geolocation) {
      finishSubmit('unsupported');
      return;
    }

    watchId = navigator.geolocation.watchPosition(
      function (pos) { consider(pos); },
      function (err) {
        if (finished) return;
        if (err && err.code === 1) {
          submitError(err);
          return;
        }
        if (timer) clearTimeout(timer);
        timer = setTimeout(endWatchPhase, 0);
      },
      GEO_OPTS
    );

    if (timer) clearTimeout(timer);
    timer = setTimeout(endWatchPhase, WATCH_MS);
  });
})();
</script>
  </body>
  <!-- [Body] end -->
</html>