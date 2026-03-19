<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->
 
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
 
<style>
 #loader-container {
      width: 100%;
      background: #eee;
      border: 1px solid #ccc;
    }

    #progress-bar {
      height: 30px;
      width: 0%;
      background-color: #4caf50;
      color: white;
      font-weight: bold;
      text-align: center;
      line-height: 30px;
      transition: width 0.3s ease;
    }
 #daily_details {
            display: none;
        }
.loading-bar {
  width: 20px;
  height: 15px;
  margin: 0 5px;
  background-color: #3498db;
  border-radius: 5px;
  animation: loading-wave-animation 1s ease-in-out infinite;
}

.loading-bar:nth-child(2) {
  animation-delay: 0.1s;
}

.loading-bar:nth-child(3) {
  animation-delay: 0.2s;
}

.loading-bar:nth-child(4) {
  animation-delay: 0.3s;
}    
 %shared {
        box-shadow: 2px 2px 10px 5px #b8b8b8;
        border-radius: 10px;
    }

    #thumbnails {
        text-align: center;

        img {
            width: 100px;
            height: 100px;
            margin: 10px;
            cursor: pointer;

            @media only screen and (max-width:480px) {
                width: 50px;
                height: 50px;
            }

            @extend %shared;

            &:hover {
                transform: scale(1.05)
            }
        }
    }

    #main {
        width: 50%;
        height: 400px;
        object-fit: cover;
        display: block;
        margin: 20px auto;
        @extend %shared;

        @media only screen and (max-width:480px) {
            width: 100%;
        }
    }

    .hidden {
        opacity: 0;
    }

    .table-container {
        width: 104%;
        padding: 0px;
        font-size: 12px;
        position: relative;
        overflow-x: auto;
        /* Enable horizontal scrolling */
        overflow-y: auto;
        /* Enable vertical scrolling */
        max-height: 450px;
        /* Adjust as necessary */
    }

    /* Thin scrollbar for modern browsers */
    .table-container::-webkit-scrollbar {
        width: 6px;
        /* Width of vertical scrollbar */
        height: 6px;
        /* Height of horizontal scrollbar */
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #b163a6;
        /* Color of the scrollbar handle */
        border-radius: 4px;
        /* Rounded corners */
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #df64ce;
        /* Color when hovered */
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        /* Background of the scrollbar track */
    }

    /* For Firefox */
    .table-container {
        scrollbar-width: thin;
        /* Thin scrollbar */
        scrollbar-color:#bbbee5 #f1f1f1;
        /* Handle color and track color */
    }

    .tbl {
        width: 100%;
        border-collapse: collapse;
        /* table-layout: fixed; Ensures consistent column widths */
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .thd {
        position: sticky;
        /* Keeps the header fixed within the container */
        top: -1px;
        /* Aligns it to the top of the container */
        z-index: 10;
        /* Ensures it stays above other elements */
        background: #f8f8f8;
        /* Prevent transparency during scrolling */
        box-shadow: 12px -1px 0px rgba(0, 0, 0, 0.1);
        /* Adds a subtle shadow for better visibility */
    }

    .thview,
    .tdview {
        padding: 9px;
        text-align: left;
        border-bottom: 11px solid #ddd;
    }

    .thview {
        font-weight: bold;
        font-size: 12px;
        color: #333;
    }

    .tdview {
        font-size: 12px;
        color: #000000;
		height: 75px;
    }

    .trview:last-child .tdview {
        border-bottom: none;
    }

    .trview:hover {
        background-color: #f1f1f1;
    }

    .selected {
        border: 2px solid #080fd399;
        background-color: #ffffff;
    }

    .new-badge {
        background: #d4f8d4;
        color: #228b22;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 9px;
    }

    .ship-now {
        background: #b163a6;
        color: #fff;
        border: none;
        padding: 7px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }

    .ship-now:hover {
        background: #df64ce;
    }

    .footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        background: #f3f3f3;
        border-top: 1px solid #ddd;
        font-size: 12px;
        width: 104%;
    }

    .pagination {
        display: flex;
        gap: 5px;
    }

    .pagination button {
        background: #f8f8f8;
        border: 1px solid #ddd;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 4px;
    }

    .pagination button:hover {
        background: #eaeaea;
    }

    .pagination button.active {
        background: #b163a6;
        color: #fff;
        border-color: #b163a6;
    }

    @media only screen and (max-width: 768px) {
        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .footer {
            flex-direction: column;
            gap: 20px;
        }
    }

    .stat-card {
        transition: transform 0.3s ease-in-out;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card i {
        transition: transform 0.3s ease-in-out;
    }

    .stat-card:hover i {
        transform: scale(1.1);
    }

    .multiselect-container {
        position: relative;
        width: 300px;
    }

    .multiselect-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        cursor: pointer;
        background: #f9f9f9;
    }

    .multiselect-options {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        border: 1px solid #ccc;
        background: #fff;
        z-index: 10;
        max-height: 150px;
        overflow-y: auto;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .multiselect-container:focus-within .multiselect-options {
        display: block;
    }

    .multiselect-options label {
        display: block;
        padding: 8px 10px;
        cursor: pointer;
    }

    .multiselect-options label:hover {
        background: #f0f0f0;
    }

    .multiselect-options input {
        margin-right: 10px;
    }
	
	
    .dept-dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .dept-dropdown input {
        width: 100%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .dept-dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 296px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-top: none;
        background: #fff;
        display: none;
        z-index: 9999;
        /* Increased z-index */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
		font-size:12px
    }

    .dept-dropdown-options div {
        padding: 10px;
        cursor: pointer;
    }

    .dept-dropdown-options div:hover {
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .dept-dropdown.active .dept-dropdown-options {
        display: block;
    }

    /* Highlight selected values */
.dept-dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
}

.loct-dropdown,.myloct-dropdown,.allloct-dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .loct-dropdown input, .myloct-dropdown input, .allloct-dropdown input{
        width: 100%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .loct-dropdown-options,.myloct-dropdown-options,.allloct-dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 296px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-top: none;
        background: #fff;
        display: none;
        z-index: 9999;
        /* Increased z-index */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
		font-size:12px
    }

    .loct-dropdown-options div,.myloct-dropdown-options div ,.allloct-dropdown-options div {
        padding: 10px;
        cursor: pointer;
    }

    .loct-dropdown-options div:hover,.myloct-dropdown-options div:hover,.allloct-dropdown-options div:hover {
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .loct-dropdown.active .loct-dropdown-options,.myloct-dropdown.active .myloct-dropdown-options,.allloct-dropdown.active .allloct-dropdown-options {
        display: block;
    }

    /* Highlight selected values */
.loct-dropdown-options div.selected,.myloct-dropdown-options div.selected,.allloct-dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
}


    .dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .dropdown input {
        width: 100%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 296px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-top: none;
        background: #fff;
        display: none;
        z-index: 9999;
        /* Increased z-index */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .dropdown-options div {
        padding: 10px;
        cursor: pointer;
    }

    .dropdown-options div:hover {
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .dropdown.active .dropdown-options {
        display: block;
    }

    /* Highlight selected values */
.dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
}


.loading-wave {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    margin-right: -450%;
}

.loading-bar {
  width: 20px;
  height: 10px;
  margin: 0 5px;
  background-color: #3498db;
  border-radius: 5px;
  animation: loading-wave-animation 1s ease-in-out infinite;
}

.loading-bar:nth-child(2) {
  animation-delay: 0.1s;
}

.loading-bar:nth-child(3) {
  animation-delay: 0.2s;
}

.loading-bar:nth-child(4) {
  animation-delay: 0.3s;
}

@keyframes loading-wave-animation {
  0% {
    height: 10px;
  }

  50% {
    height: 50px;
  }

  100% {
    height: 10px;
  }
}

.btn-primary.d-inline-flex:hover {
    background-color:rgb(255, 255, 255) !important; /* Change to your desired hover color */
    border-color: #4b4fc5 !important;
    color: #6a6ee4;
}

ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

ul li {
    border-radius: 3px;
    margin: 0;
}

ul li label {
    display: flex;
    flex-grow: 1;
    justify-content: space-between;
}

    </style>
    <script>
        function rowClick(event) {
            // Remove the 'selected' class from any currently selected row
            const selectedRows = document.querySelectorAll('.selected');
            selectedRows.forEach(row => row.classList.remove('selected'));

            // Add the 'selected' class to the clicked row
            const clickedRow = event.currentTarget;
            clickedRow.classList.add('selected');

            
        }
    </script>
  <body style="overflow-x: hidden;">
    <div class="page-loader">
      <div class="bar"></div>
    </div>
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')  

<!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">
        <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row ">
           
            <div class="col-md-9 col-sm-9">
              <input type="text" id="icon-search" class="form-control mb-4"
              style="
    height: 35px;
    font-size: 11px;
"  placeholder="Search">
            </div>
		<!--<div class="col-md-3 col-sm-3">
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
     background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
    
"><i class="ti ti-plus f-18"></i> Add Ticket</a></div>-->

          </div>
   
        </div>
      </div><br><br>
	  
	  <!-- <div class="container" style="margin-top: -51px;">
                <div class="row g-4">
                  
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="op_income">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">O/P Income</p>
                        </div>
                    </div>
                 
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="ip_income">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">	I/P Income</p>
                        </div>
                    </div>
                 
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="phary_income">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Pharmacy Income</p>
                        </div>
                    </div>
                </div><br>
            </div>-->
	  
        <!-- [ Main Content ] end -->
        <div class="row">
        <div class="col-xl-12 col-md-12" >
            
        <div class="card-body border-bottom pb-0">
                <div class="d-flex align-items-center justify-content-between">
                  
                </div>
                <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">
				
                  <li class="nav-item" role="presentation">
                    <button
                      class="nav-link active"
                      id="analytics-tab-1"
                      data-bs-toggle="tab"
                      data-bs-target="#analytics-tab-1-pane"
                      type="button"
                      role="tab"
                      aria-controls="analytics-tab-1-pane"
                      aria-selected="false"
                      >Registration Report</button
                    >
                  </li>                  
                </ul>
              </div>

</div>
</div><br>

              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel" aria-labelledby="analytics-tab-1" tabindex="0">

                <div  id="table1">
                <div class="row">

<div class="col-xl-2 col-md-2">
<div class="card">
  
<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
</div>

<span style="display:none;"  id="dateviewsall"></span>

</div>
</div>
@php $zones = App\Models\TblZonesModel::select('id','name')->get();
                  @endphp 
<div class="col-xl-3 col-md-3">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation marketervalues_search" name="tblzones" id="zone_views" placeholder="Select Zone" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch">
									 @if($zones)
											@foreach($zones as $zone)
                                         <div data-value="{{$zone->name}}">{{$zone->name}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>

         <div class="col-xl-3 col-md-3">
    <div class="card">
        <div class="loct-dropdown">
            <input type="text" class="searchLocation marketervalues_search" name="area" id="branchviews" placeholder="Select Branch" autocomplete="off">
            <div class="loct-dropdown-options options_branch brachviewsall">              
            </div>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-3">
    <div class="card">
        <div class="loct-dropdown">
            <input type="text" class="searchLocation marketervalues_search" name="phid" id="mrd_phid" placeholder="Select PHID" autocomplete="off">
        </div>
    </div>
</div>
					
                </div>
                
                <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="rcounts">0</span> Rows for <span id="dateallviews">Last 30 days</span></span>
<span class="search_views" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span style="cursor: pointer;" id="created_by_search" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="branch_search" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="zone_search" class="badge bg-success value_views_mainsearch"></span>
<span  class="badge bg-danger clear_views" style="display:none;">Clear all</span>
</p><br>

        <div class="col-sm-12">
            
              <div class="card-body">
              <div class="table-container">
    <table class="tbl">
        <thead class="thd">
            <tr class="trview">
                <!--<th class="thview">S.No</th>
                <th class="thview">City</th>
                <th class="thview">Count</th>-->
				<th class="thview">S.No</th>
                <th class="thview">Name</th>
                <th class="thview">Mobile</th>
                <th class="thview">DOB</th>
                <th class="thview">Gender</th>
                <th class="thview">Age</th>
                <th class="thview">MRD Number</th>
                <th class="thview">Branch</th>
                <th class="thview">Registration Date</th>
            </tr>
        </thead>
		
        <!-- Correct the tbody and remove the h1 tag -->
        <tbody id="loader_row">
    <tr>
      <td colspan="11">
        <div id="loader-container">
          <div id="progress-bar">Loading: 0%</div>
		  <div id="error-message" style="color: red; display: none;"></div> 
        </div>
      </td>
    </tr>
  </tbody>
		 <tbody id="daily_details" style="display:none;">
                  
        </tbody>
    </table>
</div>


    <div class="footer">
        <div>
            Items per page:
            <select id="itemsPerPageSelect">
                <option>10</option>
                <option>15</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="pagination" id="ticketpagination"></div>
    </div>

              </div>
     
          </div>
          </div>
		  
   <div id="table2" style="display:none">
   <div class="row">
		<div class="col-xl-2 col-md-2">
			<div class="card">
				<div class="loct-dropdown" style="width:150%">
					<input type="text" class="searchLocation report_search" name="area" id="reportviews" placeholder="Select Phid" autocomplete="off">
					<div class="loct-dropdown-options report_branch brachviewsall" id="mobileDropdown"></div>
				</div>
			</div>
		</div>

		<!-- Right-aligned button -->
		<div class="col text-end align-self-center">
			<a href="{{ route('superadmin.registrationreport') }}" class="btn btn-primary d-inline-flex" style="height: 34px;width: 77px;
    font-size: 13px;
       background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4;
">Back</a>
		</div>
	</div>

				<!--<p style=" margin-top: -9px;" class="text-muted f-12 mb-0">
<span class="search_report" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span style="cursor: pointer;" id="created_by_search" class="badge bg-success report_views_mainsearch"></span>
<span  class="badge bg-danger clear_report" style="display:none;">Clear all</span>
</p>-->
		   <br>
		  <div class="col-sm-12">
            
              <div class="card-body">
              <div class="table-container">
    <table class="tbl">
        <thead class="thd">
            <tr class="trview">
                <th class="thview">S.No</th>
                <th class="thview">Name</th>
                <th class="thview">Mobile</th>
                <th class="thview">DOB</th>
                <th class="thview">Gender</th>
                <th class="thview">Age</th>
                <th class="thview">Phid</th>
                <th class="thview">City</th>
                <th class="thview">Registration Date</th>
            </tr>
        </thead>
		
        <!-- Correct the tbody and remove the h1 tag -->
        <tbody id="report_details11">
                                        <tr>
																<td >
				<div class="loading-wave">
					  <div class="loading-bar"></div>
					  <div class="loading-bar"></div>
					  <div class="loading-bar"></div>
					  <div class="loading-bar"></div>
					</div>
																</td>
															</tr>
														</tbody>
		 <tbody id="report_details" style="display:none;">
                  
        </tbody>
    </table>
</div>


    <div class="footer">
        <div>
            Items per page:
            <select id="reportPerPageSelect">
                <option>10</option>
                <option>15</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="paginatin" id="reportpagination"></div>
    </div>

              </div>
     
          </div>		  
          </div>		  
                     
                </div>	
              </div>
            </div>
          </div>
          <!-- Column Rendering table end -->
          <!-- Multiple Table Control Elements start -->
          <!-- Row Created Callback table end -->
        
 			
			<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
            <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>			
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('/assets/registration/registration-details.js') }}"></script>

<script type="text/javascript">

    const dailyfetchUrl = "{{ route('superadmin.dailysummaryfetch') }}";
    const datefetchUrl = "{{ route('superadmin.dailydatefilter') }}";
    const fetchBranchUrlfitter = "{{ route('superadmin.dailybranchfilter') }}";
    const regViewUrlfitter = "{{ route('superadmin.registrationview') }}";
	
	const regfetchUrl = "{{ route('superadmin.registrationfetch') }}";
	const regfetchBranch = "{{ route('superadmin.registrationfetchbranch') }}";
    

        // Set the initial start and end dates
        var start = moment();
        var end = moment();
    
        // Callback function to update the span text with the selected date range
        function cb(start, end) {
            $("#dateviewsall").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            $("#dateallviews").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

            // Check the selected date range and adjust the display accordingly
            if (start.isSame(end, 'day')) {
                // If the start and end date are the same, show the single date
                if (start.isSame(moment(), 'day')) {
                    $('#reportrange span').html('Today');
                } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                    $('#reportrange span').html('Yesterday');
                } else {
                    $('#reportrange span').html(start.format('DD/MM/YYYY'));
                }
            } else {
                // For other ranges like "Last 7 Days", "This Month", etc.
                $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            }
        }
    
        // Initialize the date range picker
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);		
    
        // Set initial date range text
        cb(start, end);        

        $(document).on('click', '.editbtn', function (e) {
                    $('#exampleModal').modal('show');
                });
        $("#dashboard_color").css("color", "#96488b");


         // Simulate fetching data
    const data = []; // Empty array means no data

const tableBody = document.getElementById('table-body');
const noDataMessage = document.getElementById('no-data');
const prevButton = document.getElementById('prev-button');
const nextButton = document.getElementById('next-button');

function renderTable() {
  if (data.length === 0) {
    noDataMessage.style.display = 'block';
    tableBody.style.display = 'none';
  } else {
    noDataMessage.style.display = 'none';
    tableBody.style.display = 'table-row-group';
  }
}

// Initialize table rendering
renderTable();

 
    </script>	
	<script>
	// Open dropdown and show options on focus or click
$(document).on("focus click", ".searchLocation", function (event) {
    event.stopPropagation(); // Prevent the event from bubbling up
    const inputField = $(this);
    const dropdown = inputField.closest(".loct-dropdown");
    const options = dropdown.find(".loct-dropdown-options");

    $(".loct-dropdown-options").hide(); // Hide all others
    options.show(); // Show current dropdown
    dropdown.addClass("active");

    // Show all options initially
    options.find("div").show();

    // Highlight selected option
    const selectedValue = inputField.val().trim();
    options.find("div").each(function () {
        $(this).toggleClass("selected", $(this).text().trim() === selectedValue);
    });
});

// Filter options on input
$(document).on("input", ".searchLocation", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".loct-dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText));
    });
});

// Handle option click
$(document).on("click", ".loct-dropdown-options div", function (event) {
    event.stopPropagation(); // Prevent closing from body click
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".loct-dropdown").find(".searchLocation");

    inputField.val(selectedValue);
    $(this).addClass("selected").siblings().removeClass("selected");
    $(".loct-dropdown-options").hide(); // Close all dropdowns
    $(".loct-dropdown").removeClass("active");
});

// Close dropdown when clicking outside
$(document).on("click", function () {
    $(".loct-dropdown-options").hide();
    $(".loct-dropdown").removeClass("active");
});

/*$(document).on("focus", ".searchLocation", function () {
    $(this).closest(".loct-dropdown").addClass("active");
});

$(document).on("input", ".searchLocation", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".loct-dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText) || searchText === "");
    });
});

$(document).on("click", ".loct-dropdown-options div", function () {
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".loct-dropdown").find(".searchLocation");
    inputField.val(selectedValue);
    $(this).addClass("selected").siblings().removeClass("selected");
    $(this).closest(".loct-dropdown").removeClass("active");	
});

$(document).on("focus", ".searchLocation", function () {
    const inputField = $(this);
    inputField.siblings(".loct-dropdown-options").show();
    inputField.siblings(".loct-dropdown-options").find("div").show();
    const selectedValue = inputField.val().trim();
    inputField.siblings(".loct-dropdown-options").find("div").each(function () {
        if ($(this).text() === selectedValue) {
            $(this).addClass("selected");
        } else {
            $(this).removeClass("selected");
        }
    });
});

$(document).on("click", ".dropdown-options div", function () {
        const selectedValue = $(this).text().trim();
        const inputField = $(this).closest(".dropdown").find(".searchInput");
        if (inputField.hasClass("single_search")) {
                                // SINGLE selection: Replace previous value
                                inputField.val(selectedValue);
                                inputField.closest(".dropdown").removeClass("active"); // Close dropdown
                            } else {
        const currentValues = inputField.val().split(",").map(v => v.trim()).filter(Boolean);

        if (!currentValues.includes(selectedValue)) {
            currentValues.push(selectedValue);
            inputField.val(currentValues.join(", "));
        }

        $(this).addClass("selected");

        $(this).closest(".dropdown").removeClass("active");
    }
    })

// Close dropdown when clicking outside
$(document).on("click", function (event) {
    if (!$(event.target).closest(".dropdown").length) {
        $(".dropdown").removeClass("active");
    }
});

 $('.searchLocation').on('click', function(event) {
        event.stopPropagation(); // Prevent the click from propagating to the document
        $(this).next('.loct-dropdown-options').toggle(); // Toggle the dropdown visibility
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.loct-dropdown').length) {
            $('.loct-dropdown-options').hide(); // Hide the dropdown if clicked outside
        }
    });

    // Handle selecting an option
    $('.loct-dropdown-options div').on('click', function() {
        var selectedBranch = $(this).text(); // Get the branch name
        $('.searchLocation').val(selectedBranch); // Set the input field value
        $('.loct-dropdown-options').hide(); // Close the dropdown
    });*/

            </script>
    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
