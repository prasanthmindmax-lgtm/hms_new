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
        padding: 15px;
        text-align: left;
        border-bottom: 11px solid #ddd;
    }

    .thview {
		font-weight: bold;
		font-size: 12px;
		color: #333;
		text-align: center;
		vertical-align: middle;
		white-space: nowrap;
	}

    .tdview {
        font-size: 12px;
        color: #000000;
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
		  <div class="col-md-3 col-sm-3" id ="document_btn">
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 modalbtn" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
    background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
    
"><i class="ti ti-plus f-18"></i> Add Form</a></div>
          </div>
   
        </div>
      </div><br><br>
	  
	 <!-- <div class="container" style="margin-top: -51px;">
                <div class="row g-4">
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="checkin_report">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Checkin Report Count</p>
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
                      >Sample</button
                    >
                  </li>  
				<!--<li class="nav-item" role="presentation">
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
                  </li>	-->			  
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
                                     <input type="text" class="searchLocation checkvalues_search" name="tbl_checkin_report.cc_name" id="name_cc_audit" placeholder="Select cc name" autocomplete="off">
                                    <div class="loct-dropdown-options options_branch cc_name_view" style="display: none;">									 
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
    <tr class="trview">
      <th class="thview" rowspan="2">S.NO</th>
      <th class="thview" rowspan="2">Created By</th>
      <th class="thview" rowspan="2">Vendor/ Employee Name </th>
      <th class="thview" rowspan="2">NEFT Amount</th>
      <th class="thview" rowspan="2">Description</th>
      <th class="thview" rowspan="2">PAN Number</th>
      <th class="thview" rowspan="2">Account Number</th>
      <th class="thview" rowspan="2">IFSC Code</th>
      <th class="thview" rowspan="2">Invoice Number</th>
      <th class="thview" rowspan="2">Invoice Amount</th>
      <th class="thview" rowspan="2">Aressio Paid</th>
      <th class="thview" rowspan="2">Already Paid</th> 
      <th class="thview" rowspan="2">PAN Upload</th> 
      <th class="thview" rowspan="2">Invoice Upload</th> 
      <th class="thview" rowspan="2">Upload Bank Document</th> 

    </tr>
   
  </thead>

  <tbody id="daily_details11">
    <tr>
      <td colspan="24">
        <div class="loading-wave">
          <div class="loading-bar" style="margin-left: 8.5em;"></div>
          <div class="loading-bar"></div>
          <div class="loading-bar"></div>
          <div class="loading-bar"></div>
        </div>
      </td>
    </tr>
  </tbody>

  <tbody id="regular_details" style="display:none;">
    <!-- dynamically filled data -->
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
        <div class="pagination" id="regularpagination"></div>
    </div>

              </div>
     
          </div>
          </div>					                 
                </div>	
              </div>
            </div>
          </div>
		  
          <div class="card-body pc-component btn-page">
                <div
                    class="modal fade"
                    id="exampleModal"
                    role="dialog"
                    aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #6a6ee4;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel1" style="color: #ffffff;font-size: 12px;"> Add Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
                        @if ($errors->any())
                            <div class="alert alert-danger" style="font-size: 13px;">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
						
                            <div class="modal-body">
							 <form method="post" action="{{ route('superadmin.Samplesave') }}" name="demoform" id="demoform"  enctype="multipart/form-data">
							@csrf                             
                                <div class="row">                                    
                                    <?php
                                        $conn = new mysqli("localhost", "root", "", "drar_hms");

                                        $result = $conn->query("SELECT COUNT(*) AS id FROM tbl_sample");
                                        $row = $result->fetch_assoc();
                                        $next_id = $row['id'] + 1; // Generate next number
                                        $prefix = "REC-";
                                        $number = str_pad($next_id, 3, "0", STR_PAD_LEFT); // makes it 001, 002, ...
                                        $auto_number = $prefix . $number;

                                        ?>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">SI. No</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="errorss"></span>
										
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" value="<?php echo $auto_number;?>" readonly required name="serial_number"  autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Created By :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_special errorss"></span>
                                            <div class="dropdown">
                                                <input type="text" class="searchInput single_search" name="created_by" id="created_by" placeholder="Select Specialization" required>
                                                <div class="dropdown-options options_marketers ">
                                                    <div data-value="Zonal Head">Zonal Head</div>
                                                    <div data-value="Zonal Officer">Zonal Officer</div>
                                                    <!-- <div data-value="Homeopathy">Homeopathy</div>
                                                    <div data-value="NGO">NGO</div>
                                                    <div data-value="Others">Others</div> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required" style="font-size: 12px;font-weight: 600;">Vendor/ Employee Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_special errorss"></span>
                                                <div class="dropdown">
                                                    <input type="text" class="searchInput single_search" name="vendor" id="vendor" placeholder="Select Specialization" required>
                                                    <div class="dropdown-options options_marketers ">
                                                        <div data-value="Vendor">Vendor</div>
                                                        <div data-value="Employee">Employee</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">NEFT Amount</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="errorss"></span>
                                                
                                                        <input type="number" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="neft_amount" autocomplete="off">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Description</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="errorss"></span>
                                                <textarea name="description" id="description" row="15" style="height: 100px;width: 100%;"></textarea>
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">PAN Number</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="pan_number" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">PAN Upload</label>
                                        <div class="fallback"> <span style="font-size:10px; color:red;"
                                                class="error_images errorss"></span>
                                            <input name="pan_upload[]" id="pan_uploads" type="file"
                                                accept="image/*,application/pdf" multiple
                                                style="height: 28px;border: ridge;width: 100%;background-color: #ffffff; border-color: #ffffff;" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Account Number</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="account_number" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">IFSC Code</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="ifsc_code" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Invoice Number</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="invoice_number" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Invoice Amount</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="invoice_amount" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Invoice Upload</label>
                                        <div class="fallback"> <span style="font-size:10px; color:red;"
                                                class="error_images errorss"></span>
                                            <input name="invoice_upload[]" id="invoice_uploads" type="file"
                                                accept="image/*,application/pdf" multiple
                                                style="height: 28px;border: ridge;width: 100%;background-color: #ffffff; border-color: #ffffff;" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Upload Bank Document</label>
                                        <div class="fallback"> <span style="font-size:10px; color:red;"
                                                class="error_images errorss"></span>
                                            <input name="bank_upload[]" id="bank_uploads" type="file"
                                                accept="image/*,application/pdf" multiple
                                                style="height: 28px;border: ridge;width: 100%;background-color: #ffffff; border-color: #ffffff;" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Aressio Paid</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="aressio_paid" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Already Paid</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="already_paid" autocomplete="off">
											
                                        </div>
                                    </div>
                                    
                                    
                                </div>
								
								<div class="row">
                                    
                                <div class="modal-footer">
                                    <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="submit-ticket-datas" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
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
<script src="{{ asset('/assets/sample/sample.js') }}"></script>
 
@if ($errors->any())
<script>
    $(document).ready(function () {
        $('#exampleModal').modal('show');
    });
</script>
@endif

<script type="text/javascript">
    const regfetchUrl = "{{ route('superadmin.regularfetch') }}";

    
	
	$(document).on('click', '.modalbtn', function (e) {
                    $('#exampleModal').modal('show');
                });
	
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

 $(document).on("focus input", ".searchInput", function () {
        const searchText = $(this).val().trim().toLowerCase().split(",").pop().trim();
        const dropdownOptions = $(this).siblings(".dropdown-options").find("div");

        let hasMatches = false;
        dropdownOptions.each(function () {
            const optionText = $(this).text().trim().toLowerCase();
            if (optionText.includes(searchText)) {
                $(this).show();
                hasMatches = true;
            } else {
                $(this).hide();
            }
        });

        // Show dropdown if matches exist
        if (hasMatches) {
            $(this).closest(".dropdown").addClass("active");
        } else {
            $(this).closest(".dropdown").removeClass("active");
        }
    });

     // Handle option click for both single and multiple search
    $(document).on("click", ".dropdown-options div", function (e) {
        e.stopPropagation(); // Prevent dropdown from closing immediately

        const selectedValue = $(this).text().trim();
        const inputField = $(this).closest(".dropdown").find(".searchInput");

        if (inputField.hasClass("single_search")) {
            // SINGLE selection: Replace previous value
            inputField.val(selectedValue);
            inputField.closest(".dropdown").removeClass("active"); // Close dropdown
        } else {
            // MULTIPLE selection
            let currentValues = inputField.data("values") || [];

            if (currentValues.includes(selectedValue)) {
                // REMOVE value if already selected
                currentValues = currentValues.filter(v => v !== selectedValue);
                $(this).removeClass("selected");
            } else {
                // ADD value if not yet selected
                currentValues.push(selectedValue);
                $(this).addClass("selected");
            }

            inputField.data("values", currentValues);
            inputField.val(currentValues.join(", ")); // Display updated values

            // Keep dropdown open for further selection
            inputField.trigger("input");
        }
    });
    $(document).on("click", function (event) {
        if (!$(event.target).closest(".dropdown").length) {
            $(".dropdown").removeClass("active");
        }
    });

	</script>	
	
    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
