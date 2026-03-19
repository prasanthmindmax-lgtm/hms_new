<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="{{ route('referral.referral') }}" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <img src="{{ asset('/assets/images/dralogos.png') }}" class="img-fluid " style="background-color: #ffffff;margin-left: -9px;" alt="logo" />
      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">
        <li class="pc-item pc-caption">
          <!-- <label>Navigation</label> -->
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="#" class="pc-link">
            <span class="pc-micon">
            <img src="../assets/images/dashboard.png" style="width: 19px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" >Dashboard</span>
            <!-- <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            <span class="pc-badge">2</span> -->
          </a>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('admin.ticket') }}" class="pc-link">
            <span class="pc-micon">
            <img src="../assets/images/trend.png" style="width: 19px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="dashboard_color">Ticket Management</span>
            <!-- <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            <span class="pc-badge">2</span> -->
          </a>
        </li>
      </ul>
    </div>
  </div>
  <div class="pc-sidebar-footer">
      <div style="display: flex; align-items: center; gap: 2px; margin-top: 10px;">
        <p style="margin: 0;margin-left: 39px;font-size: 11px;">Powered by</p>
        <img src="{{ asset('assets/images/pages/powed by.png') }}" alt="MaxCompany Logo" style="width: 35px; margin-left: 2px; height: auto;">
    </div>
    </div>
</nav>
