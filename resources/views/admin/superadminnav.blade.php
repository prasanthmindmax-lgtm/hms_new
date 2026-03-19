<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header" style=" border: 3px solid #ffffff;
    /* border-radius: 8px; */
    padding: 5px;
    box-shadow: 0px 0px 10px rgb(4 11 221);
    transition: transform 0.3s ease-in-out;">
      <a href="{{ route('superadmin.dashboard') }}" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <img src="{{ asset('/assets/images/dralogos.png') }}" class="img-fluid " style="background-color: #ffffff;margin-left: 20px;" alt="logo" />
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
            <img src="{{ asset('/assets/images/dashboard.png') }}" style="width: 19px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" >Dashboard</span>
          </a>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.referral') }}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/trend.png') }}" style="width: 19px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="dashboard_color">Referral</span>
          </a>
        </li>

        <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.document-management')}}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/files.png') }}" style="width: 19px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="document_manage">Documents</span>
          </a>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.camp')}}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="document_camp">Camp Management</span>
          </a>
        </li>

        <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.Income_reconciliation')}}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="income_recon">Income Reconciliation</span>
          </a>
        </li>


        <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.bill_list')}}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/budgeting.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="accounts_recon">Income</span>
          </a>
        </li>
		 <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.referral') }}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/trend.png') }}" style="width: 19px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="dashboard_color">Referral</span>
          </a>
        </li>
		<li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.vehicle') }}" class="pc-link">
           <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext">Vehicle Details</span>
          </a> 
        </li>
		<li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.dailysummary') }}" class="pc-link">
           <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext">Daily Summary</span>
          </a> 
        </li>
		<li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.checkin') }}" class="pc-link">
           <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext">Checkin Report</span>
          </a> 
        </li>
		<li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.registrationreport') }}" class="pc-link">
           <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext">Registration Report</span>
          </a> 
        </li>
		<li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.regularaudit') }}" class="pc-link">
           <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext">Regular Audit</span>
          </a> 
        </li>
		<li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.ticket') }}" class="pc-link">
           <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext">Ticket Management</span>
          </a> 
        </li>
		<li class="pc-item"><a class="pc-link" href="{{ route('superadmin.discountform_document')}}">Discount Form Report</a></li>
       <!-- <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            ><span class="pc-micon">
              <img src="{{ asset('/assets/images/user.png') }}" style="width: 19px;"  alt="Icon" class="icon">
               </span
            ><span class="pc-mtext" id="usermanages">User Management</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span
          ></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('superadmin.usermanagent') }}" id="usersub">User</a></li>
            <li class="pc-item"><a class="pc-link" href="#">Role</a></li>
          </ul>
        </li>-->
      </ul>
    </div>
  </div>
  <div class="pc-sidebar-footer">
      <div style="display: flex; align-items: center; gap: 2px; margin-top: 10px;">
        <p style="margin: 0;margin-left: 39px;font-size: 11px;">Powered by</p>
        <img src="{{ asset('/assets/images/pages/powed by.png') }}" alt="MaxCompany Logo" style="width: 35px; margin-left: 2px; height: auto;">
    </div>
    </div>
</nav>
