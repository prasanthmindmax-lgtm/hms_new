<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header" style=" border: 3px solid #ffffff;
    /* border-radius: 8px; */
    padding: 5px;
    box-shadow: 0px 0px 10px rgb(4 11 221);
    transition: transform 0.3s ease-in-out;">
      <a href="{{ route('staff.dashboard') }}" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <img src="{{ asset('/assets/images/dralogos.png') }}" class="img-fluid " style="background-color: #ffffff;margin-left: 20px;" alt="logo" />
      </a>
    </div>
    <div class="navbar-content">
      
      <ul class="pc-navbar">
         <!--<li class="pc-item pc-caption">
          <label>Navigation</label>
        </li>-->
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('staff.dashboard') }}" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-status-up"></use>
              </svg>
            </span>
            <span class="pc-mtext">Dashboard</span>
            <!-- <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            <span class="pc-badge">2</span> -->
          </a> 
        </li>
		<li class="pc-item pc-hasmenu">
          <a href="{{ route('staff.ticket') }}" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-status-up"></use>
              </svg>
            </span>
            <span class="pc-mtext">Ticket Management</span>
            <!-- <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            <span class="pc-badge">2</span> -->
          </a> 
        </li>
        <!--<li class="pc-item pc-caption">
          <label>Tickets</label>
          <svg class="pc-icon">
            <use xlink:href="#custom-layer"></use>
          </svg>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('staff.createticket') }}" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-status-up"></use>
              </svg>
            </span>
            <span class="pc-mtext">Create Ticket</span>
          </a> 
        </li>
        
        <li class="pc-item pc-hasmenu">
          <a href="{{ route('staff.tickets')}}" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-clipboard"></use>
              </svg>
            </span>
            <span class="pc-mtext">Tickets</span>
          </a> 
        </li>

        <li class="pc-item pc-hasmenu">
          <a href="{{ route('staff.loan')}}" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-clipboard"></use>
              </svg>
            </span>
            <span class="pc-mtext">Loans</span>
          </a> 
        </li> -->
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