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
 
 .custom-dropdown-list {
        position: absolute;
        background: white;
        border: 1px solid #d3d3d3;
        max-height: 200px;
        overflow-y: auto;
        width: 100%;
        z-index: 1000;
        display: none;
    }

    .custom-dropdown-item {
        padding: 8px 10px;
        cursor: pointer;
    }

    .custom-dropdown-item:hover {
        background-color: #f1f1f1;
    }
	
.page-btn, .page-lst, .page-nxt, .page-btp, .page-bup, .pge-nxt, .pge-pnxt {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}
#reportpagination, #lastticketpagination, #nxtpagination, #reportpagination3, #reportpagination4, #amtpagination, #pharmacypagination {
    display: flex;
    gap: 5px;
    flex-wrap: wrap; /* Makes buttons wrap nicely on small screens */
    align-items: center;
}

    .fa-info-circle {
      color: #007bff;
      margin-left: 5px;
      cursor: pointer;
    }

    /* Popup modal styles */
    .modals {
      display: none; 
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }

    .modal-contents {
      background-color: #fff;
      margin: 15% auto;
      padding: 20px;
      border-radius: 4px;
      width: 300px;
      text-align: center;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 20px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover {
      color: #000;
    }
</style>
 
<style>
.tooltip-hover {
  position: relative;
  display: inline-block;
  cursor: pointer;
}

.tooltip-hover .tooltip-text {
  visibility: hidden;
  width: max-content;
  background-color: #333;
  color: #fff;
  text-align: left;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 14px;

  position: absolute;
  left: -220px;
  top: 50%;
  transform: translateY(-50%);
  white-space: nowrap;
  z-index: 1000;
  box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
}

.tooltip-hover:hover .tooltip-text {
  visibility: visible;
}


.tooltip-table {
  display: none;
  position: absolute;
  background-color: white;
  border: 1px solid #ccc;
  padding: 10px;
  top: 17px;
  right: 1px;
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
				<li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="analytics-tab-2"
                      data-bs-toggle="tab"
                      data-bs-target="#analytics-tab-2-pane"
                      type="button"
                      role="tab"
                      aria-controls="analytics-tab-2-pane"
                      aria-selected="false"
                      >Last Visit Date</button
                    >
                  </li>
                  <li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="analytics-tab-3"
                      data-bs-toggle="tab"
                      data-bs-target="#analytics-tab-3-pane"
                      type="button"
                      role="tab"
                      aria-controls="analytics-tab-3-pane"
                      aria-selected="false"
                      >Next Appointment Date</button
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
			@php $tcategory = App\Models\TblTreamentCategory::select('name')->orderBy('name', 'asc')->get();
                  @endphp 
				<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.treatment_category" id="ctrt_category" placeholder="Select Treatment Category" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch treatment_category">
									@if($tcategory)
											@foreach($tcategory as $category)
                                         <div data-value="{{$category->name}}">{{$category->name}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>
				@php $tstage = App\Models\TblTreamentStage::select('name')->orderBy('name', 'asc')->get();
                  @endphp 
				<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.stage_of_treatment" id="ctreatment_stage" placeholder="Select Treatment Stage" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch treatment_stage">
											@if($tstage)
											@foreach($tstage as $stage)
                                         <div data-value="{{$stage->name}}">{{$stage->name}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>
						
<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.cc_name" id="name_cc_audit" placeholder="Select cc name" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch cc_name_view" style="display: none;">									 
                                    </div>
                                </div>
                            </div>
                        </div>
						
<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.cc_audit_name" id="cc_audit_name" placeholder="Select cc audit name" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch">
									 @if($employeeData)
											@foreach($employeeData as $chk)
                                         <div data-value="{{ $chk['fullname'] }}">{{ $chk['fullname'] }}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>
					                 
					<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.ptsource" id="pt_source_id" placeholder="Select Ptsource" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch pt_source">
											<div data-value="">Self</div>
											<div data-value="">Television</div>
											<div data-value="">Camp</div>
											<div data-value="">Doctor</div>
											<div data-value="">Seo</div>
											<div data-value="">Flyers</div>
											<div data-value="">Patientreference</div>
											<div data-value="">Friends</div>
											<div data-value="">Family</div>
											<div data-value="">Banner</div>
											<div data-value="">Facebook</div>
											<div data-value="">Youtube</div>
                                    </div>
                                </div>
                            </div>
                        </div>
							@php $doctor_name = App\Models\CheckinModel::select('doctor_name')->where('doctor_name','!=','')->distinct()->orderBy('doctor_name', 'asc')->get();
                  @endphp
                    <!--<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.doctor_name" id="cc_doctor_name" placeholder="Select doctor name" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch">
									 @if($doctor_name)
											@foreach($doctor_name as $doctor)
                                         <div data-value="{{$doctor->doctor_name}}">{{$doctor->doctor_name}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>-->

<div class="col-xl-2 col-md-2">
						<div class="card">
							<div class="loct-dropdown">
								<input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.doctor_name" id="cc_doctor_name" placeholder="Select doctor name" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch doctorviewsall" style="display: none;">
							</div>
						</div>
					</div>						
					</div>						
						
						<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="loct-dropdown">
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.patient_age" id="cc_patient_age" placeholder="Select patient age" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch">									
										 @for($i = 20; $i <= 60; $i += 10)
											<div data-value="{{$i}}">{{$i}} - {{$i + 9}}</div>
										 @endfor								  
                                    </div>
                                </div>
                            </div>
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
<span style="cursor: pointer;" id="pt_stage" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="name_cc" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="cc_name_audit" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="doctor_name" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="patient_age" class="badge bg-success value_views_mainsearch"></span>
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
				<th class="thview">AREA NAME</th>
                <th class="thview">BRANCH NAME</th>                
                <th class="thview">SOURCE OF ENTRY</th>
                <th class="thview">PURPOSE - CONSULTATION</th>
                <th class="thview">OP IP PHAR DUE</th>
                <!--<th class="thview">LAST VISIT DATE AND NEXT APPOINTMENT DATE</th>-->
                <th class="thview">APPOINTMENT DATE</th>
                <th class="thview">FINANCIALS</th>
                <!--<th class="thview">FINANCIALS  ICSI EF FET iINJ </th>
                <th class="thview">OD ICSI EF FET LEGAL</th>
                <th class="thview">ED ICSI EF FET LEGAL </th>-->
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
		  
		  <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                    <h5 class="modal-title" id="exampleModalLabel"
                                        style="color: #ffffff;font-size: 12px;">Time Line</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>
                                <div class="row" style="margin-left: 1em; margin-top: 1em;">                                    
								<!--<div class="col-xl-2 col-md-2">
								<div class="card">
								  
								<div id="myreportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
									<i class="fa fa-calendar"></i>&nbsp;
									<span id="data_values"></span> <i class="fa fa-caret-down views"></i>
								</div>

								<span style="display:none;"  id="mydateviewsall"></span>

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
                        </div>	-->					
						
                                </div>
								 <p style="
										margin-top: -9px;margin-left: 1.2em;
									" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><!--<span id="mycounts">0</span> Rows for <span id="mydateallviews">Last 30 days</span></span>-->
									<span class="my_search_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
									<span style="cursor: pointer;" id="created_my_search" class="badge bg-success value_views_mysearch"></span>
									<span style="cursor: pointer;" id="mybranch_search" class="badge bg-success value_views_mysearch"></span>
									<span style="cursor: pointer;" id="myzone_search" class="badge bg-success value_views_mysearch"></span>
									<span class="badge bg-success my_value_views"></span>
									<span class="badge bg-success my_value_views"></span>
									<span class="badge bg-success my_value_views"></span>
									<span class="badge bg-success my_value_views"></span>
									<span class="badge bg-success my_value_views"></span>
									<span class="badge bg-success my_value_views"></span>
									<span  class="badge bg-danger clear_my_views" style="display:none;">Clear all</span>
									</p><br>
											   <br>
									<div class="card-body" style="margin-left: 1em;margin-right: 1em;margin-top: -1em;">
													  <div class="">
											<table class="tbl">
												<thead class="thd">
													<tr class="trview">
														<th class="thview">S.NO</th>
														<th class="thview">Consultant</th>
														<th class="thview">Visit Type</th>
														<th class="thview">Bill NO </th>
														<th class="thview">Services</th>
														<th class="thview">Payment Type </th>
														<th class="thview">Discount </th>
														<th class="thview">Prev Balance</th>
														<th class="thview">Amt Receivable</th>
														<th class="thview">Amt Received</th>
													</tr>
												</thead>
												
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


											<div class="footer" style="width:100%">
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
					
					<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog"
							 aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-xl" role="document">
								<div class="modal-content">
									<div class="modal-header" style="background-color: #080fd399;height: 0px;">
										<h5 class="modal-title" id="exampleModalLabel"
											style="color: #ffffff;font-size: 12px;">Checkin Details</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal"
												style="background-color: #ffffff;" aria-label="Close"></button>
									</div>

									<div class="card-body" style="margin-left: 1em;margin-right: 1em;margin-top: -1em;">
										<!-- Nav tabs -->
										<ul class="nav nav-tabs" id="appointmentTabs" role="tablist" style="margin-top: 2em;">
											<li class="nav-item" role="presentation">
												<button class="nav-link active" id="previous-tab" data-bs-toggle="tab"
														data-bs-target="#previous" type="button" role="tab"
														aria-controls="previous" aria-selected="true">
													Previous Appointments
												</button>
											</li>
											<li class="nav-item" role="presentation">
												<button class="nav-link" id="upcoming-tab" data-bs-toggle="tab"
														data-bs-target="#upcoming" type="button" role="tab"
														aria-controls="upcoming" aria-selected="false">
													Upcoming Appointments
												</button>
											</li>
										</ul>

										<!-- Tab panes -->
										<div class="tab-content" style="margin-top: 1em;">
											<!-- Previous Appointments Tab -->
											<div class="tab-pane fade show active" id="previous" role="tabpanel"
												 aria-labelledby="previous-tab">
												<table class="tbl">
													<thead class="thd">
														<tr class="trview">
															<th class="thview">S.NO</th>
															<th class="thview">Appt Date</th>
															<th class="thview">Appt Time</th>
															<th class="thview">Purpose</th>
															<th class="thview">Doctor Name</th>
															<th class="thview">Booked by</th>
														</tr>
													</thead>
													<tbody id="appt_details11">
																				<tr>
																										<td >
														<div class="loading-wave" style="margin-left: -3em;">
															  <div class="loading-bar"></div>
															  <div class="loading-bar"></div>
															  <div class="loading-bar"></div>
															  <div class="loading-bar"></div>
															</div>
																										</td>
																									</tr>
																								</tbody>
												 <tbody id="nxt_appt_details" style="display:none;">
														  
												</tbody>
												</table>
												<div class="footer" style="width:100%; margin-top: 1em;">
													<div>
														Items per page:
														<select id="reportPerPageSelect3">
															<option>10</option>
															<option>15</option>
															<option>25</option>
															<option>50</option>
															<option>100</option>
														</select>
													</div>
													<div class="paginatin3" id="reportpagination3"></div>
												</div>
											</div>

											<!-- Upcoming Appointments Tab -->
											<div class="tab-pane fade" id="upcoming" role="tabpanel"
												 aria-labelledby="upcoming-tab">
												<table class="tbl">
													<thead class="thd">
														<tr class="trview">
															<th class="thview">S.NO</th>
															<th class="thview">Appt Date</th>
															<th class="thview">Appt Time</th>
															<th class="thview">Purpose</th>
															<th class="thview">Doctor Name</th>
															<th class="thview">Booked by</th>
														</tr>
													</thead>
													<tbody id="upcom_details11">
																				<tr>
																										<td >
														<div class="loading-wave" style="margin-left: -3em;">
															  <div class="loading-bar"></div>
															  <div class="loading-bar"></div>
															  <div class="loading-bar"></div>
															  <div class="loading-bar"></div>
															</div>
																										</td>
																									</tr>
																								</tbody>
												 <tbody id="upp_appt_details" style="display:none;">
														  
												</tbody>
												</table>
												<div class="footer" style="width:100%; margin-top: 1em;">
													<div>
														Items per page:
														<select id="reportPerPageSelect4">
															<option>10</option>
															<option>15</option>
															<option>25</option>
															<option>50</option>
															<option>100</option>
														</select>
													</div>
													<div class="paginatin4" id="reportpagination4"></div>
												</div>
											</div>
										</div>										
									</div>
								</div>
							</div>
						</div>     

					<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                    <h5 class="modal-title" id="exampleModalLabel"
                                        style="color: #ffffff;font-size: 12px;">Time Line</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>
                                <div class="row" style="margin-left: 1em; margin-top: 1em;">     
                                </div>											  
									<div class="card-body" style="margin-left: 1em;margin-right: 1em;margin-top: -1em;">									
									  <table style="font-family: Arial, sans-serif; font-size: 14px;">&nbsp;
										<tr>
											<td><strong>Location:</strong></td>
											<td></td>
										</tr>
										<tr>
											<td><strong>Consultant:</strong></td>
											<td></td>
										</tr>
										<tr>
											<td><strong>Patient Type:</strong></td>
											<td></td>
										</tr>
										<tr>
											<td><strong>ID:</strong></td>
											<td></td>
										</tr>
										<tr>
											<td><strong>Name:</strong></td>
											<td></td>
										</tr>
										<tr>
											<td><strong>Mobile:</strong></td>
											<td></td>
										</tr>
										<tr>
											<td><strong>Age:</strong></td>
											<td></td>
										</tr>
										<tr>
											<td><strong>Gender:</strong></td>
											<td></td>
										</tr>
										<tr>
											<td><strong>Payment Type:</strong></td>
											<td></td>
										</tr>
										<tr>
											<td><strong>Bill No:</strong></td>
											<td></td>
										</tr>
										<tr>
											<td><strong>Bill Date:</strong></td>
											<td></td>
										</tr>
									</table> <br>
										<div id="not_pharmacy">
											<table class="tbl">
												<thead class="thd">
													<tr class="trview">
														<th class="thview">S.NO</th>
														<th class="thview">Name</th>
														<th class="thview">Quantity</th>
														<th class="thview">Price</th>
														<th class="thview">Tax(%)</th>
														<th class="thview">Disc</th>
														<th class="thview">Discount</th>
														<th class="thview">Total</th>
													</tr>
												</thead>
												
												<tbody id="amt_details11">
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
												 <tbody id="amt_details" style="display:none;">
														  
												</tbody>
											</table>
											<div class="footer" style="width:100%">
												<div>
													Items per page:
													<select id="amtPerPageSelect">
														<option>10</option>
														<option>15</option>
														<option>25</option>
														<option>50</option>
														<option>100</option>
													</select>
												</div>
												<div class="amtpaginatin" id="amtpagination"></div>
											</div>
											</div>
											<div id="pharmacy">
											<table class="tbl">
												<thead class="thd">
													<tr class="trview">
														<th class="thview">S.NO</th>
														<th class="thview">Name</th>
														<th class="thview">Batch</th>
														<th class="thview">Expiry</th>
														<th class="thview">Quantity</th>
														<th class="thview">Rate</th>
														<th class="thview">Tax</th>
														<th class="thview">Disc</th>
														<th class="thview">Total</th>
													</tr>
												</thead>
												
												<tbody id="pharmacy_details11">
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
												 <tbody id="pharmacy_details" style="display:none;">
														  
												</tbody>
											</table>
											<div class="footer" style="width:100%">
												<div>
													Items per page:
													<select id="pharmacyPerPageSelect">
														<option>10</option>
														<option>15</option>
														<option>25</option>
														<option>50</option>
														<option>100</option>
													</select>
												</div>
												<div class="pharmacypaginatin" id="pharmacypagination"></div>
											</div>
										</div>
								  </div>
								
                            </div>
                        </div>
                    </div>
							
                </div>	


<div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel" aria-labelledby="analytics-tab-2" tabindex="0">
                
                <div class="row">

<div class="col-xl-2 col-md-2">
<div class="card">
  
<div id="allreportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
</div>

<span style="display:none;"  id="lastdateviewsall"></span>

</div>
</div>

 </div>                
                <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="lastcounts">0</span> Rows for <span id="lastdateallviews">Last 30 days</span></span>
<span class="last_search_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span style="cursor: pointer;" id="created_all_search" class="badge bg-success value_views_allsearch"></span>
<span style="cursor: pointer;" id="allbranch_search" class="badge bg-success value_views_allsearch"></span>
<span style="cursor: pointer;" id="allzone_search" class="badge bg-success value_views_allsearch"></span>
<span class="badge bg-success all_value_views"></span>
<span class="badge bg-success all_value_views"></span>
<span class="badge bg-success all_value_views"></span>
<span class="badge bg-success all_value_views"></span>
<span class="badge bg-success all_value_views"></span>
<span  class="badge bg-danger clear_all_views" style="display:none;">Clear all</span>
</p><br>

        <div class="col-sm-12">
            
              <div class="card-body">
              <div class="table-container">
        <table class="tbl">
            <thead class="thd">
                <tr class="trview">
					<th class="thview" style="width:13%">S.NO</th>
					<th class="thview" style="width:13%">MRD NUMBER</th>
					<th class="thview" style="width:13%">PT NAME</th>
                    <th class="thview">Appt Date</th>
                    <th class="thview">Appt Time</th>
                    <th class="thview">Purpose</th>
                    <th class="thview">Doctor Name</th>
                    <th class="thview">Booked by</th>
                </tr>
            </thead>
<tbody id="all_details11">
                                        <tr>
																<td >
				<div class="loading-wave">
					  <div class="loading-bar" style="margin-left: -2.1em;"></div>
					  <div class="loading-bar"></div>
					  <div class="loading-bar"></div>
					  <div class="loading-bar"></div>
					</div>
																</td>
															</tr>
														</tbody>
		 <tbody id="all_details" style="display:none;">
                  
        </tbody>
        </table>
    </div>

    <div class="footer">
        <div>
            Items per page:
            <select id="lastitemsPerPageSelect">
                <option>10</option>
                <option>15</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="lastpagination" id="lastticketpagination"></div>
    </div>

              </div>
     
          </div>                     
                </div>	

<div class="tab-pane fade" id="analytics-tab-3-pane" role="tabpanel" aria-labelledby="analytics-tab-3" tabindex="0">
                
                <div class="row">

<div class="col-xl-2 col-md-2">
<div class="card">
  
<div id="nextreportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
</div>

<span style="display:none;"  id="nextdateviewsall"></span>

</div>
</div>

 </div>                
                <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="nextcounts">0</span> Rows for <span id="nextdateallviews">Last 30 days</span></span>
<span class="last_search_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span style="cursor: pointer;" id="created_all_search" class="badge bg-success value_views_allsearch"></span>
<span style="cursor: pointer;" id="allbranch_search" class="badge bg-success value_views_allsearch"></span>
<span style="cursor: pointer;" id="allzone_search" class="badge bg-success value_views_allsearch"></span>
<span class="badge bg-success all_value_views"></span>
<span class="badge bg-success all_value_views"></span>
<span class="badge bg-success all_value_views"></span>
<span class="badge bg-success all_value_views"></span>
<span class="badge bg-success all_value_views"></span>
<span  class="badge bg-danger clear_all_views" style="display:none;">Clear all</span>
</p><br>

        <div class="col-sm-12">
            
              <div class="card-body">
              <div class="table-container">
        <table class="tbl">
            <thead class="thd">
                <tr class="trview">
					<th class="thview" style="width:13%">S.NO</th>
					<th class="thview" style="width:13%">MRD NUMBER</th>
					<th class="thview" style="width:13%">PT NAME</th>
                    <th class="thview">Appt Date</th>
                    <th class="thview">Appt Time</th>
                    <th class="thview">Purpose</th>
                    <th class="thview">Doctor Name</th>
                    <th class="thview">Booked by</th>
                </tr>
            </thead>
<tbody id="next_details11">
                                        <tr>
																<td >
				<div class="loading-wave">
					  <div class="loading-bar" style="margin-left: -2.1em;"></div>
					  <div class="loading-bar"></div>
					  <div class="loading-bar"></div>
					  <div class="loading-bar"></div>
					</div>
																</td>
															</tr>
														</tbody>
		 <tbody id="next_details" style="display:none;">
                  
        </tbody>
        </table>
    </div>

    <div class="footer">
        <div>
            Items per page:
            <select id="nextitemsPerPageSelect">
                <option>10</option>
                <option>15</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="nextpagination" id="nxtpagination"></div>
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
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">TREATMENT CATEGORY</label>												
												<div class="multiselect-container" tabindex="0">
                                                    <input type="text" class="multiselect-input morefittersclr" placeholder="Select CATEGORY" name="treatment_category" readonly id="trt_category">
                                                    <div class="multiselect-options ticket_status">													
													  @if($tcategory)
																@foreach ($tcategory as $categry)
                                                        <label>															
                                                            <input type="checkbox" value="{{ $categry->name }}" class="statusCheckbox" onchange="updateSelectedValues()"> {{ $categry->name}}															
                                                        </label>
														@endforeach
													@endif
                                                        
                                                    </div>
                                                </div>
												<br>
											</div>
                                    </div>
						<br/>
						 <div id="treatmentAmountContainer"></div>
						<div class="col-sm-12" style="margin-top: 1em;">
									<div class="mb-12">
											<label class="form-label required" style="font-size: 12px;font-weight: 600;">STAGE OF TREATMENT: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_description errorss"></span>
                                            <select class="mb-3 form-select" id="stage_trt" placeholder="Select STAGE OF TREATMENT" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" name="stage_trt">
													@if($tstage)
													@foreach($tstage as $stage)
													 <option value="{{$stage->name}}">{{$stage->name}}</option>
													@endforeach
												  @endif
													
                                            </select> 
											</div>
                                    </div>
									<input type="hidden" id="income_id" name="income_id"/>
									<!--@php $locations = App\Models\TblLocationModel::select('id','name')->get(); @endphp 
									<div class="col-sm-12">
										<div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Location:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_location errorss"></span>
											<div class="dept-dropdown" style="position: relative;">
											   <input type="hidden" name="location_id" id="location_id">
												<input type="text" class="searchDept" id="location_display" placeholder="Select Location" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" autocomplete="off">
												<div class="dept-dropdown-options">
													@if($locations)
														@foreach ($locations as $location)
															  <div class="location-option" data-value="{{ $location->id }}">{{ $location->name }}</div>
															@endforeach
													@endif
												</div>
											</div>
                                        </div><br>
                                    </div>-->
                               <div class="col-sm-12">
										<div class="mb-12">
											<label class="form-label required" style="font-size: 12px; font-weight: 600;">CC NAME:</label>
											<span style="font-size: 10px; color: red;" class="error_subject errorss"></span>
											<input type="text" id="cc_name" name="cc_name" class="form-control" 
												style="height: 36px; border-radius: 10px; border: solid 1px #d3d3d3; background-color: #fff; 
												color: #505050 !important; width: 100%; padding-left: 6px;" placeholder="Select CC Name" autocomplete="off">
											<input type="hidden" name="cc_employment_id" id="cc_employment_id">
											<div id="cc_name_list" class="custom-dropdown-list" style="display:none; position: absolute; width: 100%; max-height: 150px; overflow-y: auto; background-color: #fff; border: 1px solid #d3d3d3;"></div>
											<br>
										</div>
									</div>


                                    <div class="col-sm-12">
									<div class="mb-12">
										<label class="form-label required" style="font-size: 12px; font-weight: 600;">CC AUDIT NAME:</label>
										<span style="font-size: 10px; color: red;" class="error_subject errorss"></span>
										<input type="text" id="cc_auddit_name" name="cc_audit_name" class="form-control"
											style="height: 36px; border-radius: 10px; border: solid 1px #d3d3d3; background-color: #fff;
											color: #505050 !important; width: 100%; padding-left: 6px;" placeholder="Select CC Audit Name" autocomplete="off">
										<input type="hidden" name="cc_audit_employment_id" id="cc_audit_employment_id">
										<div id="cc_audit_name_list" class="custom-dropdown-list" style="display:none; position: absolute; width: 100%; max-height: 150px; overflow-y: auto; background-color: #fff; border: 1px solid #d3d3d3;"></div>
										<br>
									</div>
								</div>
                                    <!--<div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">OP IP PHAR DUE:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="ip_phar_due" name="ip_phar_due" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter OP IP PHAR DUE">
                                       <br> </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">LAST VISIT DATE AND NEXT APPOINTMENT DATE:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="date" class="form-control" id="next_appt_date" name="next_appt_date" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter LAST VISIT DATE AND NEXT APPOINTMENT DATE">
                                       <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">FINANCIALS ICSI EF FET iINJ:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="fincial_icsi" name="fincial_icsi" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter LAST VISIT DATE AND NEXT APPOINTMENT DATE">
                                       <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">OD ICSI EF FET LEGAL:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="od_fet_legal" name="od_fet_legal" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter OD ICSI EF FET LEGAL">
                                       <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">ED ICSI EF FET LEGAL:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="ed_fet_legal" name="ed_fet_legal" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter ED ICSI EF FET LEGAL">
                                       <br></div>
                                    </div>-->
                                </div>
                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white" style="margin-top: 2em;">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" onclick="document.getElementById('demoform').reset();"  class="btn btn-outline-danger w-50 me-2">Clear All</a>
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

<div id="infoModal" class="modals">
    <div class="modal-contents">
      <span class="close" onclick="closePopup()">&times;</span>
      <p><strong>SOURCE OF ENTRY</strong></p>
      <p>Self</p>
      <p>Television</p>
      <p>Camp </p>
      <p>Doctor  </p>
      <p>Seo   </p>
      <p>Flyers   </p>
      <p>Banner   </p>
      <p>Friends    </p>
      <p>Family    </p>
      <p>Patient Reference     </p>
      <p>Social Media     </p>
    </div>
  </div>
 			
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>			
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script src="{{ asset('/assets/checkin/checkin-details.js') }}"></script>

<script>
$(document).ready(function () {
    let auditTypingTimer;
    const auditDebounceDelay = 0;

    const auditNameInput = $('#cc_auddit_name');
    const auditDropdown = $('#cc_audit_name_list');
    const auditHiddenInput = $('#cc_audit_employment_id');

    auditNameInput.on('keyup', function () {
        clearTimeout(auditTypingTimer);

        const query = $(this).val().trim();
        if (!query) {
            auditDropdown.hide().empty();
            return;
        }

        auditTypingTimer = setTimeout(function () {
            $.ajax({
                url: checkinCCName, // Define this variable with your audit endpoint URL
                method: 'GET',
                data: { empid: query },
                success: function (data) {
                    auditDropdown.empty().show();

                    if (data.length > 0) {
                        data.forEach(function (emp) {
                            if (emp.fullname) {
                                const item = $('<div>')
                                    .addClass('custom-dropdown-item')
                                    .text(emp.fullname)
                                    .attr('data-cid', emp.employment_id)
                                    .on('click', function () {
                                        auditNameInput.val(emp.fullname);
                                        auditHiddenInput.val(emp.employment_id);
                                        auditDropdown.hide();
                                    });

                                auditDropdown.append(item);
                            }
                        });
                    } else {
                        auditDropdown.append('<div class="custom-dropdown-item">No matching employees found</div>');
                    }
                },
                error: function () {
                    auditDropdown.hide().empty();
                    console.error('Failed to fetch audit CC names.');
                }
            });
        }, auditDebounceDelay);
    });

    auditNameInput.on('focus', function () {
        if (auditDropdown.children().length > 0) {
            auditDropdown.show();
        }
    });

    $(document).on('click', function (event) {
        if (!$(event.target).closest('#cc_auddit_name, #cc_audit_name_list').length) {
            auditDropdown.hide();
        }
    });
});
</script>

<script>
$(document).ready(function () {
    let typingTimer;
    const debounceDelay = 0; // 0.5 second

    const ccNameInput = $('#cc_name');
    const ccNameDropdown = $('#cc_name_list');
    const hiddenIdInput = $('#cc_employment_id');

    ccNameInput.on('keyup', function () {
        clearTimeout(typingTimer);

        const query = $(this).val().trim();
        if (!query) {
            ccNameDropdown.hide().empty();
            return;
        }

        typingTimer = setTimeout(function () {
            $.ajax({
                url: checkinCCName, // example: '/api/search-employee'
                method: 'GET',
                data: { empid: query },
                success: function (employeeData) {
                    ccNameDropdown.empty().show();

                    if (employeeData.length > 0) {
                        employeeData.forEach(function (emp) {
                            if (emp.fullname) {
                                const item = $('<div>')
                                    .addClass('custom-dropdown-item')
                                    .text(emp.fullname)
                                    .attr('data-cid', emp.employment_id)
                                    .on('click', function () {
                                        ccNameInput.val(emp.fullname);
                                        hiddenIdInput.val(emp.employment_id);
                                        ccNameDropdown.hide();
                                    });

                                ccNameDropdown.append(item);
                            }
                        });
                    } else {
                        ccNameDropdown.append('<div class="custom-dropdown-item">No matching employees found</div>');
                    }
                },
                error: function () {
                    ccNameDropdown.hide().empty();
                    console.error('Failed to fetch CC names.');
                }
            });
        }, debounceDelay);
    });

    // Show dropdown on input focus
    ccNameInput.on('focus', function () {
        if (ccNameDropdown.children().length > 0) {
            ccNameDropdown.show();
        }
    });

    // Hide dropdown on outside click
    $(document).on('click', function (event) {
        if (!$(event.target).closest('#cc_name, #cc_name_list').length) {
            ccNameDropdown.hide();
        }
    });
});



/*
document.addEventListener('DOMContentLoaded', function () {
    const options = document.querySelectorAll('.location-option');
    const displayInput = document.getElementById('location_display');
    const hiddenInput = document.getElementById('location_id');
    const dropdown = document.querySelector('.dept-dropdown-options');

    options.forEach(option => {
        option.addEventListener('click', function () {
            displayInput.value = this.textContent.trim();
            hiddenInput.value = this.getAttribute('data-value');
            dropdown.style.display = 'none'; // Hides dropdown after selection
        });
    });

    // Toggle dropdown on input click
    displayInput.addEventListener('click', function () {
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    // Hide dropdown if clicking outside
    document.addEventListener('click', function (event) {
        const container = document.querySelector('.dept-dropdown');
        if (!container.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
});

	// Handle input focus: Add 'active' class to the closest dropdown
$(document).on("focus", ".searchDept", function () {
    $(this).closest(".dept-dropdown").addClass("active");
});

// Search and filter dropdown options as you type
$(document).on("input", ".searchDept", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".dept-dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText) || searchText === "");
    });
});

// Handle option click: Select a single option and close the dropdown
$(document).on("click", ".dept-dropdown-options div", function () {
	let category = $(this).attr('data-value');
	
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".dept-dropdown").find(".searchDept");

    // Set the input field value to the selected option
    inputField.val(selectedValue);

    // Highlight the selected option
    $(this).addClass("selected").siblings().removeClass("selected");

    // Close the dropdown after selecting an option (optional)
    $(this).closest(".dept-dropdown").removeClass("active");
	
	 $.ajax({
                method: 'post',
                url: checkinCCName,
                data: {
                    id: category
                },
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
                success: function(employeeData) {
                    var ccNameDropdown = $('#cc_name_list');
					ccNameDropdown.empty();
					ccNameDropdown.append('<div class="custom-dropdown-item">Select CC Name</div>');
					if (employeeData.length > 0) {
							employeeData.forEach(function(emp) {
								if (emp.fullname) {
									const item = $('<div>')
										.addClass('custom-dropdown-item')
										.text(emp.fullname)
										.attr('data-cid', emp.employment_id);

									item.on('click', function () {
										const selectedNme = $(this).text();
										const selectedI = $(this).data('cid');
										$('#cc_name').val(selectedNme);
										$('#cc_employment_id').val(selectedI);
										ccNameDropdown.hide();
									});

									ccNameDropdown.append(item);
								}
							});
						} else {
							ccNameDropdown.append('<div class="custom-dropdown-item">No employee data available</div>');
						}
					$('#cc_name').on('click', function () {
					    ccNameDropdown.show(); 
					});
					$('#cc_name').on('keyup', function () {
						var filter = $(this).val().toLowerCase();
						ccNameDropdown.empty();
						ccNameDropdown.append('<div class="custom-dropdown-item">Select CC Name</div>');
						
						employeeData.forEach(function(emp) {
							if (emp.fullname && emp.fullname.toLowerCase().includes(filter)) {
								const items = $('<div>')
									.addClass('custom-dropdown-item')
									.text(emp.fullname)
									.attr('data-cid', emp.employment_id);

								items.on('click', function () {
									const selectedNae = $(this).text();
									const selctedId = $(this).data('cid');
									$('#cc_name').val(selectedNae);
									$('#cc_employment_id').val(selctedId);
									ccNameDropdown.hide();
								});
								ccNameDropdown.append(items);
							}
						});

                if (ccNameDropdown.children().length === 1) {
							ccNameDropdown.append('<div class="custom-dropdown-item">No matching results</div>');
						}
					});
					
				$(document).on('click', function(event) {
               if (!$(event.target).closest('#cc_name, #cc_name_list').length) {
						   ccNameDropdown.hide();
					   }
				   });
                }
        })
});

// Update dropdown UI on input focus (clear previous selection highlights)
$(document).on("focus", ".searchDept", function () {
    const inputField = $(this);
    const selectedValue = inputField.val().trim();

    // Highlight the selected option and remove the highlight from others
    inputField.siblings(".dept-dropdown-options").find("div").each(function () {
        if ($(this).text() === selectedValue) {
            $(this).addClass("selected");
        } else {
            $(this).removeClass("selected");
        }
    });
});
*/
	</script>

 <script>
 
 function updateSelectedValues() {
    const checkboxes = document.querySelectorAll('.statusCheckbox:checked');    
    const selectedCategories = Array.from(checkboxes).map(cb => cb.value);
    const treatmentAmounts = selectedCategories.map(function(category) {
        const sanitizedCategory = category.replace(/[^a-zA-Z0-9_-]/g, '');        
        const input = document.getElementById(`treat_amt_${sanitizedCategory}`);        
        return input ? input.value : "";
    });
    updateAmountValues(selectedCategories, treatmentAmounts);
}
 
function updateAmountValues(selectedCategories, treatmentAmounts) {
    const container = document.getElementById('treatmentAmountContainer');
    container.innerHTML = ''; 
    const selectedValues = [];
    selectedCategories.forEach(function(category, index) {
        const amount = treatmentAmounts[index] || "";
        selectedValues.push(category);
        const sanitizedCategory = category.replace(/[^a-zA-Z0-9_-]/g, '');
        const div = document.createElement('div');
        div.className = 'col-sm-12 treatment-amount-field';
        div.innerHTML = `
            <div class="mb-12">
                <label class="form-label required" style="font-size: 12px;font-weight: 600;">TREATMENT AMOUNT for ${category}:</label>
                <span style="font-size:10px; color:red;" class="error_subject errorss"></span>
                <input type="text" class="form-control" id="treat_amt_${sanitizedCategory}" name="treat_amt_${sanitizedCategory}" value="${amount}" placeholder="ENTER TREATMENT AMOUNT">
                <br>
            </div>
        `;
        container.appendChild(div);
    });

    document.getElementById('trt_category').value = selectedValues.join(', ');
}

$(document).on('click', '.statusCheckbox', function (e) {
		let id = $('#income_id').val();
	   let selectedValues = [];
    $('.statusCheckbox:checked').each(function () {
        selectedValues.push($(this).val());
    });
	
	$.ajax({
    url: checkinTreatmentAmt,
    data: {
        id: id,
        selectedValues: selectedValues
    },
    type: "POST",
    success: function(response) {
        response.data.forEach(item => {
            const inputId = "treat_amt_" + item.name.replace(/\s+/g, "").replace(/\+/g, ""); 
            const inputElement = document.getElementById(inputId);
            if (inputElement && inputElement.value === "") {
                inputElement.value = item.amount;
            }
        });
    },
    error: function(xhr) {
        console.error('Error occurred:', xhr.responseText);
    }
});
});
  
</script>
<script type="text/javascript">
    const checkfetchUrl = "{{ route('superadmin.dailysummaryfetch') }}";
    const dateCheckUrl = "{{ route('superadmin.dailydatefilter') }}";
    const checkBranchUrlfitter = "{{ route('superadmin.dailybranchfilter') }}";
	//new
	const checkinfetchUrl = "{{ route('superadmin.checkinreportfetch') }}";
	const checkinCCName = "{{ route('superadmin.checkinccname') }}";
    const checkinBranchUrlfitter = "{{ route('superadmin.checkinreportfilter') }}";
	const dateCheckinUrl = "{{ route('superadmin.checkindatefilter') }}";
	const checkinBranchfitter = "{{ route('superadmin.checkinbranchfilter') }}";
	const checkinRptEdit = "{{ route('superadmin.checkinreportedit') }}";
	const checkinTimeLine = "{{ route('superadmin.checkintimeline') }}";
	const checkinTreatmentAmt = "{{ route('superadmin.checkintreatmentamt') }}";
	const checkinLastFetch = "{{ route('superadmin.checkinlastfetch') }}";
	const lastdatefillter = "{{ route('superadmin.checkinlastdatefiltr') }}";
	const nextdatefillter = "{{ route('superadmin.checkinnextdatefiltr') }}";
	const patientDashboardBaseURL = "{{ route('superadmin.patientdashboard', ['phid' => '__PHID__']) }}";
	
	function showPopup() {
      document.getElementById("infoModal").style.display = "block";
    }

    function closePopup() {
      document.getElementById("infoModal").style.display = "none";
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
      const modal = document.getElementById("infoModal");
      if (event.target === modal) {
        closePopup();
      }
    }
	
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
		
		function all(start, end) {
   
            $("#lastdateviewsall").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            $("#lastdateallviews").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

            // Check the selected date range and adjust the display accordingly
            if (start.isSame(end, 'day')) {
                // If the start and end date are the same, show the single date
                if (start.isSame(moment(), 'day')) {
                    $('#allreportrange span').html('Today');
                } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                    $('#allreportrange span').html('Yesterday');
                } else {
                    $('#allreportrange span').html(start.format('DD/MM/YYYY'));
                }
            } else {
                // For other ranges like "Last 7 Days", "This Month", etc.
                $('#allreportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            }
        }
		
		// Callback function to update the span text with the selected date range
        function my(start, end) {
   
            $("#mydateviewsall").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            $("#mydateallviews").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

            // Check the selected date range and adjust the display accordingly
            if (start.isSame(end, 'day')) {
                // If the start and end date are the same, show the single date
                if (start.isSame(moment(), 'day')) {
                    $('#myreportrange span').html('Today');
                } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                    $('#myreportrange span').html('Yesterday');
                } else {
                    $('#myreportrange span').html(start.format('DD/MM/YYYY'));
                }
            } else {
                // For other ranges like "Last 7 Days", "This Month", etc.
                $('#myreportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            }
        }
		
		// Callback function to update the span text with the selected date range
        function nxt(start, end) {
   
            $("#nextdateviewsall").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            $("#nextdateallviews").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

            // Check the selected date range and adjust the display accordingly
            if (start.isSame(end, 'day')) {
                // If the start and end date are the same, show the single date
                if (start.isSame(moment(), 'day')) {
                    $('#nextreportrange span').html('Today');
                } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                    $('#nextreportrange span').html('Yesterday');
                } else {
                    $('#nextreportrange span').html(start.format('DD/MM/YYYY'));
                }
            } else {
                // For other ranges like "Last 7 Days", "This Month", etc.
                $('#nextreportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
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
                'Last 14 Days': [moment().subtract(13, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);	

// Initialize the date range picker
        $('#myreportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 14 Days': [moment().subtract(13, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, my);	

$('#allreportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 14 Days': [moment().subtract(13, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, all);
		
$('#nextreportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 14 Days': [moment().subtract(13, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, nxt);		
    
        // Set initial date range text
        cb(start, end);
		my(start, end);
		all(start, end);
		nxt(start, end);
 
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
