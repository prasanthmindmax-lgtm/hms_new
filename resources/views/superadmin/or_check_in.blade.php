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

.tooltip-table {
  display: none;
  position: absolute;
  background-color: white;
  border: 1px solid #ccc;
  padding: 10px;
  top: 20px;
  right: 20px;
  z-index: 999;
  width: 500px; 
  white-space: nowrap;
  font-size: 13px;
  box-shadow: 0 0 8px rgba(0,0,0,0.2);
}

.tooltip-table table {
  width: 100%;
  border-collapse: collapse;
}

.tooltip-table td {
  padding: 4px 10px;
  vertical-align: top;
}
.tooltip-container {
  position: relative;
  display: inline-block;
}


.tooltip-container:hover .tooltip-table {
  display: block;
}

.tooltip-table th, .tooltip-table td {
  border: 1px solid #ddd;
  padding: 4px 8px;
  font-size: 12px;
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

    .loct-dropdown-options {
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

    .loct-dropdown-options div {
        padding: 10px;
        cursor: pointer;
    }

    .loct-dropdown-options div:hover{
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .loct-dropdown.active .loct-dropdown-options{
        display: block;
    }

    /* Highlight selected values */
.loct-dropdown-options div.selected {
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
		
          </div>
   
        </div>
      </div><br><br>
	  
	  <div class="container" style="margin-top: -51px;">
                <div class="row g-4">
                    <!-- Row 1 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="checkin_report">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Checkin Report Count</p>
                        </div>
                    </div>
                </div><br>
            </div>
	  	  
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
                      >Checkin Report</button
                    >
                  </li>                  
                </ul>
              </div>

</div>
</div><br>

              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel" aria-labelledby="analytics-tab-1" tabindex="0">

                <div id="table11">
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
@php $zones = App\Models\TblZonesModel::select('name')->get();
                  @endphp 
<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tblzones.name" id="czone_views" placeholder="Select Zone" autocomplete="off">
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
                        <div class="col-xl-2 col-md-2">
						<div class="card">
							<div class="loct-dropdown">
								<input type="text" class="searchLocation checkvalues_search" name="tbl_locations.name" id="cbranch_views" placeholder="Select Branch" autocomplete="off">
								<div class="loct-dropdown-options options_branch brachviewsall" style="display: none;"></div>
							</div>
						</div>
					</div>
				<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.treatment_category" id="ctrt_category" placeholder="Select Treatment Category" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch treatment_category">
											<div data-value="">UNCATEGORISED</div>
											<div data-value="">NC</div>
											<div data-value="">IUI</div>
											<div data-value="">ICSI SELF</div>
											<div data-value="">OD ICSI</div>
											<div data-value="">ED ICSI</div>
											<div data-value="">FOR RECANALISATION</div>
											<div data-value="">ANC FOLLOW UP</div>
											<div data-value="">PED</div>
											<div data-value="">SURROGATE</div>
											<div data-value="">ANC</div>
											<div data-value="">POST NATAL</div>
											<div data-value="">ANC +VE MISCARRIED</div>
                                    </div>
                                </div>
                            </div>
                        </div>
				<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.stage_of_treatment" id="ctreatment_stage" placeholder="Select Treatment Stage" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch treatment_stage">
											<div data-value="">NC ONGOING</div>
											<div data-value="">IUI ONGOING </div>
											<div data-value="">IVF ICSI ON PRE PICK UP</div>
											<div data-value="">IVF ICSI ONGOING </div>
											<div data-value="">IVF ICSI OPU COMPLETED</div>
											<div data-value="">OD ICSI ON PROCESS </div>
											<div data-value="">OD ICSI WAITING</div>
											<div data-value="">ED ICSI ON PROCESS</div>
											<div data-value="">ED ICSI WAITING</div>
											<div data-value="">NC  +VE</div>
											<div data-value="">IUI +VE</div>
											<div data-value="">IVF + VE</div>
											<div data-value="">PRE FET ONGOING</div>
											<div data-value="">FET ONGOING</div>
											<div data-value="">ANC 1ST TRIMESTER</div>
											<div data-value="">ANC 2ND TRIMESTER</div>
											<div data-value="">ANC 3RD TRIMESTER</div>
											<div data-value="">DROPPED OUT</div>
											<div data-value="">NOT VISITED FOR 1 MONTH</div>
											<div data-value="">CX STITCHING AWAITED</div>
											<div data-value="">CX STITCH COMPLETED</div>
											<div data-value="">NT COMPLETED</div>
											<div data-value="">DUAL MARKER COMPLETED</div>
											<div data-value="">ANOMALY SCAN COMPLETED</div>
											<div data-value="">ANC  FETAL ECHO COMPLETED</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                      

<!--<div class="col-xl-2 col-md-2">
<div class="">
<a href="#" class="btn btn-primary d-inline-flex app_ticket_search" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filter"  style="
       height: 34px;
    width: 133px;
    font-size: 12px;
      background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
    
"><i class="ti ti-filter f-18"></i>&nbsp; More Filter</a>
</div>&nbsp;&nbsp;&nbsp;&nbsp;

</div>-->

                  
                </div>
                </div>

                
                <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="dcounts">0</span> Rows for <span id="dateallviews">Last 30 days</span></span>
<span class="csearch_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span style="cursor: pointer;" id="cbranch_search" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="czone_search" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="ctrt_search" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="ctrt_stage" class="badge bg-success value_views_mainsearch"></span>
<span  class="badge bg-danger clear_views" style="display:none;">Clear all</span>
</p><br>

        <div class="col-sm-12">
            
              <div class="card-body">
              <div class="table-container">
    <table class="tbl">
        <thead class="thd">
            <!--<tr class="trview">
                <th class="thview">S.No</th>
                <th class="thview">Name</th>
                <th class="thview">Mobile</th>
                <th class="thview">DOB</th>
                <th class="thview">Ptsource</th>
                <th class="thview">City</th>
                <th class="thview">Purpose</th>
            </tr>-->
			<tr class="trview">
                <th class="thview">S.NO</th>
                <th class="thview">MRD NUMBER</th>
                <th class="thview">FAMILY ID</th>
                <th class="thview">PT NAME </th>
                <th class="thview">MOB NUMBER</th>
                <th class="thview">AGE </th>
                <th class="thview">TREATMENT CATEGORY </th>
                <th class="thview">STAGE OF TREATMENT</th>
                <th class="thview">CONSULTANT NAME</th>
                <th class="thview">CC NAME</th>
                <th class="thview">CC AUDIT NAME</th>
                <th class="thview">BRANCH NAME</th>
                <th class="thview">AREA NAME</th>
                <th class="thview">SOURCE OF ENTRY</th>
                <th class="thview">PURPOSE - CONSULTATION</th>
                <th class="thview">OP IP PHAR DUE</th>
                <th class="thview">LAST VISIT DATE AND NEXT APPOINTMENT DATE</th>
                <th class="thview">FINANCIALS  ICSI EF FET iINJ </th>
                <th class="thview">OD ICSI EF FET LEGAL</th>
                <th class="thview">ED ICSI EF FET LEGAL </th>
                <th class="thview">Action</th>
            </tr>
        </thead>
		
        <!-- Correct the tbody and remove the h1 tag -->
       <!-- <tbody id="daily_details11" style="display:none;">
            <tr>
            <td data-column-index="7">
    <img src="../assets/images/loader1.gif" style="
    width: 50%;
    margin-left: 130%;
"  alt="Icon" class="icon">
</td>
            </tr>
        
        </tbody>-->
		 <tbody id="daily_details11">
                                        <tr>
																<td >
				<div class="loading-wave">
					  <div class="loading-bar" style="margin-left: 8.5em;"></div>
					  <div class="loading-bar"></div>
					  <div class="loading-bar"></div>
					  <div class="loading-bar"></div>
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
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="pagination" id="ticketpagination"></div>
    </div>

              </div>
     
          </div>
                     
                </div>				

              </div>
            </div>
          </div>
		  
  <div id="table21" style="display:none">
   <div class="row">
		<div class="col-xl-2 col-md-2">
			<div class="card">
				<div class="loct-dropdown" style="width:150%">
					<input type="text" class="searchLocation report_search" name="tbl_branch.name" id="reportviews" placeholder="Select Phid" autocomplete="off">
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
        <tbody id="time_details11">
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
		 <tbody id="time_details" style="display:none;">
                  
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
          <!-- Column Rendering table end -->
          <!-- Multiple Table Control Elements start -->
          <!-- Row Created Callback table end -->
         <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_edit_income">
    <div class="offcanvas-body p-0">
        <div id="ecom-filter" class="collapse collapse-horizontal show">
            <div class="ecom-filter">
                <div class="card">
                    <!-- Sticky Header -->
                    <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                        <h5>Edit Checkin Report</h5>
                        
                    </div>
                    <!-- Scrollable Block -->
					 <form method="post" action="{{ route('superadmin.checkinupdate') }}" name="demoform" id="demoform"  enctype="multipart/form-data">
							@csrf                             
                    <div class="scroll-block position-relative">
                        <div class="card-body">
                        <div class="row">
						<div class="col-sm-12">
									<div class="mb-12">
											<label class="form-label required" style="font-size: 12px;font-weight: 600;">TREATMENT CATEGORY: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_description errorss"></span>
                                            <select class="mb-3 form-select" id="trt_category" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="trt_category">
													<option value="">SELECT TREATMENT CATEGORY</option>
													<option value="UNCATEGORISED">UNCATEGORISED</option>
                                                    <option value="NC">NC</option>
                                                    <option value="IUI">IUI</option>
                                                    <option value="ICSI SELF">ICSI SELF</option>
                                                    <option value="OD ICSI">OD ICSI</option>
													<option value="ED ICSI">ED ICSI</option>
													<option value="FOR RECANALISATION">FOR RECANALISATION</option>
													<option value="ANC FOLLOW UP">ANC FOLLOW UP</option>
													<option value="PED">PED</option>
													<option value="SURROGATE">SURROGATE</option>
													<option value="ANC">ANC</option>
													<option value="POST NATAL">POST NATAL</option>
													<option value="ANC +VE MISCARRIED">ANC +VE MISCARRIED</option>
													
                                            </select> 
											</div>
                                    </div>
						<div class="col-sm-12">
									<div class="mb-12">
											<label class="form-label required" style="font-size: 12px;font-weight: 600;">STAGE OF TREATMENT: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_description errorss"></span>
                                            <select class="mb-3 form-select" id="stage_trt" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="stage_trt">
													<option value="">SELECT STAGE OF TREATMENT</option>
													<option value="NC ONGOING"> NC ONGOING </option>
													<option value="IUI ONGOING">IUI ONGOING </option>
													<option value="IVF ICSI ON PRE PICK UP">IVF ICSI ON PRE PICK UP</option>
													<option value="IVF ICSI ONGOING">IVF ICSI ONGOING </option>
													<option value="IVF ICSI OPU COMPLETED">IVF ICSI OPU COMPLETED</option>
													<option value="OD ICSI ON PROCESS">OD ICSI ON PROCESS </option>
													<option value="OD ICSI WAITING">OD ICSI WAITING</option>
													<option value="ED ICSI ON PROCESS">ED ICSI ON PROCESS</option>
													<option value="ED ICSI WAITING">ED ICSI WAITING</option>
													<option value="NC  +VE">NC  +VE </option>
													<option value="IUI +VE"> IUI +VE </option>
													<option value="IVF + VE">IVF + VE</option>
													<option value="PRE FET ONGOING"> PRE FET ONGOING</option>
													<option value="FET ONGOING"> FET ONGOING</option>
													<option value="ANC 1ST TRIMESTER"> ANC 1ST TRIMESTER</option>
													<option value="ANC 2ND TRIMESTER"> ANC 2ND TRIMESTER</option>
													<option value="ANC 3RD TRIMESTER"> ANC 3RD TRIMESTER</option>
													<option value="DROPPED OUT"> DROPPED OUT</option>
													<option value="NOT VISITED FOR 1 MONTH"> NOT VISITED FOR 1 MONTH</option>
													<option value="CX STITCHING AWAITED"> CX STITCHING AWAITED</option>
													<option value="CX STITCH COMPLETED"> CX STITCH COMPLETED</option>
													<option value="NT COMPLETED"> NT COMPLETED</option>
													<option value="DUAL MARKER COMPLETED"> DUAL MARKER COMPLETED</option>
													<option value="ANOMALY SCAN COMPLETED"> ANOMALY SCAN COMPLETED</option>
													<option value="ANC  FETAL ECHO COMPLETED"> ANC  FETAL ECHO COMPLETED</option>
													
                                            </select> 
											</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											  <label class="form-label required" style="font-size: 12px;font-weight: 600;">PT NAME:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="pt_name" name="pt_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="ENTER PT NAME" required>                                      
											<br></div>
                                    </div>
									<input type="hidden" id="income_id" name="income_id"/>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											  <label class="form-label required" style="font-size: 12px;font-weight: 600;">CC NAME:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="cc_name" name="cc_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter CC NAME" required>
                                        <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											  <label class="form-label required" style="font-size: 12px;font-weight: 600;">CC AUDIT NAME:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="cc_audit_name" name="cc_audit_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter CC AUDIT NAME" required>
                                       <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">OP IP PHAR DUE:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="ip_phar_due" name="ip_phar_due" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter OP IP PHAR DUE" required>
                                       <br> </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">LAST VISIT DATE AND NEXT APPOINTMENT DATE:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="date" class="form-control" id="next_appt_date" name="next_appt_date" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter LAST VISIT DATE AND NEXT APPOINTMENT DATE" required>
                                       <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">FINANCIALS ICSI EF FET iINJ:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="fincial_icsi" name="fincial_icsi" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter LAST VISIT DATE AND NEXT APPOINTMENT DATE" required>
                                       <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">OD ICSI EF FET LEGAL:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="od_fet_legal" name="od_fet_legal" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter OD ICSI EF FET LEGAL" required>
                                       <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">ED ICSI EF FET LEGAL:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="ed_fet_legal" name="ed_fet_legal" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter ED ICSI EF FET LEGAL" required>
                                       <br></div>
                                    </div>
                                </div>
                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2">Clear All</a>
        <button style="height: 34px;width: 133px; font-size: 12px;" id="vehicle_update" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</button>
    </div>
                        </div>
                    </div>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>
 			
			<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
            <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>			
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('/assets/checkin/checkin-details.js') }}"></script>

<script type="text/javascript">

    const checkfetchUrl = "{{ route('superadmin.dailysummaryfetch') }}";
    const dateCheckUrl = "{{ route('superadmin.dailydatefilter') }}";
    const checkBranchUrlfitter = "{{ route('superadmin.dailybranchfilter') }}";
	//new
	const checkinfetchUrl = "{{ route('superadmin.checkinreportfetch') }}";
    const checkinBranchUrlfitter = "{{ route('superadmin.checkinreportfilter') }}";
	const dateCheckinUrl = "{{ route('superadmin.checkindatefilter') }}";
	const checkinBranchfitter = "{{ route('superadmin.checkinbranchfilter') }}";
	const checkinRptEdit = "{{ route('superadmin.checkinreportedit') }}";
	const checkinTimeLine = "{{ route('superadmin.checkintimeline') }}";
	const patientDashboardBaseURL = "{{ route('superadmin.patientdashboard', ['phid' => '__PHID__']) }}";
	
	 window.onload = function() {
            // Check if the session has a success message
            var successMessage = @json(session('success'));

            if (successMessage) {
                 window.dispatchEvent(new CustomEvent('swal:toast', {
							detail: {
							  title:'Info!',
							  text: successMessage,
							  icon: 'success',
							  background: 'success',
							}
						}));
            }
	 }

        // Set the initial start and end dates
        var start = moment().subtract(6, 'days');
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
 
    </script>	
	
	<script>
	// Show dropdown on focus or click
  $(document).on("focus click", ".searchLocation", function (event) {
    event.stopPropagation();
    const inputField = $(this);
    const dropdown = inputField.closest(".loct-dropdown");
    const options = dropdown.find(".loct-dropdown-options");

    $(".loct-dropdown-options").hide(); // Hide others
    options.show(); // Show current
    dropdown.addClass("active");

    // Reset filter
    options.find("div").show();

    const selectedValue = inputField.val().trim();
    options.find("div").each(function () {
      $(this).toggleClass("selected", $(this).text().trim() === selectedValue);
    });
  });

 $(document).on("input", ".searchLocation", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".loct-dropdown-options").find("div").each(function () {
      $(this).toggle($(this).text().toLowerCase().includes(searchText));
    });
  });

// Click option
$(document).on("click", ".loct-dropdown-options div", function (event) {
    event.stopPropagation();
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".loct-dropdown").find(".searchLocation");

    inputField.val(selectedValue);
    $(this).addClass("selected").siblings().removeClass("selected");

    // Don't empty or clear here!
    $(".loct-dropdown-options").hide();
    $(".loct-dropdown").removeClass("active");
});


// Hide dropdown on outside click
$(document).on("click", function () {
    $(".loct-dropdown-options").hide();
    $(".loct-dropdown").removeClass("active");
});

	</script>	
	
    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
