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

        <!-- <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.income_reconciliation')}}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="income_recon">Income Reconciliation</span>
          </a>
        </li> -->
        <li class="pc-item pc-hasmenu">
                <a href="javascript:void(0);" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;" alt="Icon" class="icon">
                    </span>
                    <span class="pc-mtext">Income Reconciliation</span>
                </a>
                <ul class="pc-submenu">
                    <li class="pc-item">
                        <a href="{{ route('superadmin.income_reconciliation') }}" class="pc-link">Income Reconciliation</a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('superadmin.overviewindex') }}" class="pc-link">Income Reconciliation Overview Old</a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('superadmin.overviewindexnew') }}" class="pc-link">Income Reconciliation Overview New</a>
                    </li>
                    <li class="pc-item">
                      <a href="{{ route('superadmin.IncomeMontlyReport') }}" class="pc-link">Income Montly Report</a>
                    </li>

                </ul>
            </li>

        <li class="pc-item pc-hasmenu">
                <a href="javascript:void(0);" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;" alt="Icon" class="icon">
                    </span>
                    <span class="pc-mtext">Branch Income</span>
                </a>
                <ul class="pc-submenu">
                    <li class="pc-item">
                        <a href="{{ route('branch-financial.index') }}" class="pc-link">Branch Income</a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('financial-reports.index') }}" class="pc-link">Branch Financial Report</a>
                    </li>

                </ul>
            </li>
        <!-- <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.overviewindex')}}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="income_recon">Income Reconciliation Overview Old</span>
          </a>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.overviewindexnew')}}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="income_recon">Income Reconciliation Overview New</span>
          </a>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.IncomeMontlyReport')}}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="income_recon">Income Montly Report</span>
          </a>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('branch-financial.index')}}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="income_recon">Branch Income</span>
          </a>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('financial-reports.index')}}" class="pc-link">
            <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext" id="income_recon">Branch Financial Report</span>
          </a>
        </li> -->


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
          <a href="{{ route('superadmin.masteraccess') }}" class="pc-link">
           <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext">Access Master</span>
          </a>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('superadmin.logs') }}" class="pc-link">
           <span class="pc-micon">
            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor" viewBox="0 0 16 16" style="opacity:.85;">
              <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
              <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3z"/>
            </svg>
            </span>
            <span class="pc-mtext" style="font-weight:600;">Logs</span>
          </a>
        </li>
		<li class="pc-item pc-hasmenu">
          <a href="{{ route('bank-reconciliation.index') }}" class="pc-link">
           <span class="pc-micon">
            <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
            </span>
            <span class="pc-mtext">Bank Reconciliation</span>
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
            <a href="javascript:void(0);" class="pc-link">
                <span class="pc-micon">
                  <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;" alt="Icon" class="icon">
                </span>
                <span class="pc-mtext">Ticket Management</span>
            </a>
            <ul class="pc-submenu">
              <li class="pc-item">
                <a href="{{ route('superadmin.getTicketMaster') }}" class="pc-link">Ticket Master</a>
              </li>
              <li class="pc-item">
                <a href="{{ route('superadmin.ticket') }}" class="pc-link">Ticket Management</a>
              </li>
            </ul>
          </li>

          <!-- <li class="pc-item pc-hasmenu">
            <a href="{{ route('superadmin.sample') }}" class="pc-link">
            <span class="pc-micon">
              <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;"  alt="Icon" class="icon">
              </span>
              <span class="pc-mtext">Sample</span>
            </a>
          </li> -->
          {{-- <li class="pc-item pc-hasmenu">
                <a href="javascript:void(0);" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;" alt="Icon" class="icon">
                    </span>
                    <span class="pc-mtext">Vendor</span>
                </a>
                <ul class="pc-submenu">
                    @if($admin->access_limits === 3)
                        <li class="pc-item">
                            <a href="{{ route('superadmin.purchasemaker') }}" class="pc-link">Purchase Maker</a>
                        </li>
                    @elseif($admin->access_limits === 2)
                        <li class="pc-item">
                            <a href="{{ route('superadmin.purchasechecker') }}" class="pc-link">Purchase Checker</a>
                        </li>
                    @else
                        <li class="pc-item">
                            <a href="{{ route('superadmin.getvendor') }}" class="pc-link">Vendor</a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('superadmin.purchasemaker') }}" class="pc-link">Purchase Maker</a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('superadmin.purchasechecker') }}" class="pc-link">Purchase Checker</a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('superadmin.purchaseapprover') }}" class="pc-link">Purchase Approver</a>
                        </li>
                    @endif
                </ul>

            </li> --}}
            <li class="pc-item pc-hasmenu">
                <a href="javascript:void(0);" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;" alt="Icon" class="icon">
                    </span>
                    <span class="pc-mtext">New Vendor</span>
                </a>
                <ul class="pc-submenu">
                    <li class="pc-item">
                        <a href="{{ route('superadmin.getcustomer') }}" class="pc-link">Customer</a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('superadmin.getvendor') }}" class="pc-link">Vendor</a>
                    </li>
                    <li class="pc-item">
                      <a href="{{ route('superadmin.getquotation') }}" class="pc-link">Quotation</a>
                    </li>
                    <li class="pc-item">
                      <a href="{{ route('superadmin.getpurchaseorder') }}" class="pc-link">Purchase Order</a>
                    </li>
                    <li class="pc-item">
                      <a href="{{ route('superadmin.getbill') }}" class="pc-link">Bill</a>
                    </li>
                    <li class="pc-item">
                      <a href="{{ route('superadmin.getbillmade') }}" class="pc-link">Bill Made</a>
                    </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.getneftdashboard') }}" class="pc-link">NEFT Dashboard</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.getgrndashboard') }}" class="pc-link">GRN Dashboard</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.gettdsdashboard') }}" class="pc-link">TDS Tax</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.getgstdashboard') }}" class="pc-link">GST Tax</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.getnaturedashboard') }}" class="pc-link">Nature Of Payment</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('ai.compare.page') }}" class="pc-link">compare</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.dashboard') }}" class="pc-link">Bank Dashboard</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.gettdssummary') }}" class="pc-link">Tds Summary</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.gettdsreport') }}" class="pc-link">Tds Report</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.getgstsummary') }}" class="pc-link">Gst Summary</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.getasset') }}" class="pc-link">Asset Master</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.getcompany') }}" class="pc-link">Company</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.getvendortype') }}" class="pc-link">Vendor Type</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.reportindex') }}" class="pc-link">Report</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.vendorSummary') }}" class="pc-link">Vendor Summary</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.getprofessionalsummary') }}" class="pc-link">Professional Summary</a>
                   </li>
                    <li class="pc-item">
                       <a href="{{ route('superadmin.vendorincomeReport') }}" class="pc-link">Income Summary</a>
                   </li>
                </ul>


            </li>
            <li class="pc-item pc-hasmenu">
                <a href="javascript:void(0);" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('/assets/images/health.png') }}" style="width: 22px;" alt="Icon" class="icon">
                    </span>
                    <span class="pc-mtext">Discount</span>
                </a>
                <ul class="pc-submenu">
                    <li class="pc-item">
                        <a href="{{ route('superadmin.discountform_document') }}" class="pc-link">Discount Form</a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('superadmin.discountDocumentNew') }}" class="pc-link">Discount Form New</a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('superadmin.cancelbillform') }}" class="pc-link">Cancel Form</a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('superadmin.cancelbill_dashboard') }}" class="pc-link">Cancel Bill Dashboard</a>
                    </li>
                    <li class="pc-item">
                      <a href="{{ route('superadmin.refundform') }}" class="pc-link">Return Form</a>
                    </li>

                </ul>
            </li>

		{{-- <li class="pc-item"><a class="pc-link" href="{{ route('superadmin.discountform_document')}}">Discount Form Report</a></li> --}}
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
