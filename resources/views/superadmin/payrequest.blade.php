<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->

  
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="{{ asset('/assets/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- [Page specific CSS] end -->
    
    <link rel="stylesheet" href="{{ asset('/assets/css/uikit.css') }}" />
  
<style>


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

.loct-dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .loct-dropdown input {
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

    .loct-dropdown-options div:hover {
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .loct-dropdown.active .loct-dropdown-options {
        display: block;
    }

    /* Highlight selected values */
.loct-dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
}
.type-dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .type-dropdown input {
        width: 100%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .type-dropdown-options {
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

    .type-dropdown-options div {
        padding: 10px;
        cursor: pointer;
    }

    .type-dropdown-options div:hover {
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .type-dropdown.active .type-dropdown-options {
        display: block;
    }

    /* Highlight selected values */
.type-dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
}

.branch-dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .branch-dropdown input {
        width: 100%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .branch-dropdown-options {
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

    .branch-dropdown-options div {
        padding: 10px;
        cursor: pointer;
    }

    .branch-dropdown-options div:hover {
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .branch-dropdown.active .branch-dropdown-options {
        display: block;
    }

    /* Highlight selected values */
.branch-dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
}

.vehicle-dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .vehicle-dropdown input {
        width: 100%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .vehicle-dropdown-options {
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

    .vehicle-dropdown-options div {
        padding: 10px;
        cursor: pointer;
    }

    .vehicle-dropdown-options div:hover {
        background-color: rgb(107 111 229);
        color: white;
    }

    /* Show dropdown when needed */
    .vehicle-dropdown.active .vehicle-dropdown-options {
        display: block;
    }

    /* Highlight selected values */
.vehicle-dropdown-options div.selected {
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
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
    background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
    
"><i class="ti ti-plus f-18"></i> Add Vehicle</a></div>
 <div class="col-md-3 col-sm-3"  id ="documentbtn" style="display:none">
 <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 doceditbtn" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
    background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
    
"><i class="ti ti-plus f-18"></i> Add Document</a></div>

          </div>
   
        </div>
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
                      aria-selected="true"
                      >Vehicle Details</button
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
                      >Vehicle Documents</button
                    >
                  </li>				  
                </ul>
              </div>

</div>
</div><br>

              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel" aria-labelledby="analytics-tab-1" tabindex="0">
<div class="row">

<!--<div class="col-xl-2 col-md-2">
<div class="card">
  
<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
</div>

<span style="display:none;"  id="dateviewsall"></span>

</div>
</div>-->
@php $reg_no = App\Models\vehicleDetails::select('vehicle_details.registration_number')->get();  @endphp 
					<div class="col-xl-2 col-md-2" style="width:23.6%">
					<div class="card">
						<div class="dropdown">
						<input type="text" class="searchInput multi_search vehiclevalues_search" name="vehicle_details.registration_number" id="reg_number" placeholder="Select Reg No" autocomplete="off">
						<div class="dropdown-options multi_search vehicle_marketers marketernameall">
							@if($reg_no)
								@foreach($reg_no as $reg)
									<div data-value="{{$reg->registration_number}}">{{$reg->registration_number}}</div>
								@endforeach
							@endif
						</div>
					</div>
                            </div>
                        </div>
@php $location = App\Models\TblLocationModel::select('tbl_locations.name')->get(); @endphp 
                        <div class="col-xl-2 col-md-2" style="width:23.6%">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search vehiclevalues_search" name="tbl_locations.name" id="branch_views" placeholder="Select Branch" autocomplete="off">
                                    <div class="dropdown-options multi_search vehicle_marketers brachviewsall">
                                       @if($location)
											@foreach($location as $location)
                                        <div data-value="{{$location->name}}">{{$location->name}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>
@php $zones = App\Models\TblZonesModel::select('name')->get(); @endphp 
                        <div class="col-xl-2 col-md-2" style="width:23.6%">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search vehiclevalues_search" name="tblzones.name" id="zone_views" placeholder="Select Zone" autocomplete="off">
                                    <div class="dropdown-options multi_search vehicle_marketers zoneviewsall">
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
<div class="">
<a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filter"  style="
       height: 34px;
    width: 133px;
    font-size: 12px;
    background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
    
"><i class="ti ti-filter f-18"></i>&nbsp; More Filter</a>
</div>&nbsp;&nbsp;&nbsp;&nbsp;

</div>       
                </div>
				<p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="counts">0</span> Rows </span>
<span class="search_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span style="cursor: pointer;" id="created_by_search" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="branch_search" class="badge bg-success value_views_mainsearch"></span>
<span style="cursor: pointer;" id="zone_search" class="badge bg-success value_views_mainsearch"></span>
<span class="badge bg-success value_views"></span>
<span class="badge bg-success value_views"></span>
<span class="badge bg-success value_views"></span>
<span class="badge bg-success value_views"></span>
<span class="badge bg-success value_views"></span>
<span  class="badge bg-danger clear_views" style="display:none;">Clear all</span>
</p><br>
        <div class="col-sm-12">
            
              <div class="card-body">
              <div class="table-container">
        <table class="tbl">
            <thead class="thd">
                <tr class="trview">
					<th class="thview">Manufacture Year</th>
					<th class="thview">Vehicle ID</th>
                    <th class="thview">Branch</th>
                    <th class="thview">Vehicle Type</th>
                    <th class="thview">Model</th>
                    <th class="thview">Reg No</th>
                    <th class="thview">Engine No</th>
                    <th class="thview">Chassis No</th>
                    <th class="thview">Fuel Type</th>
					<th class="thview">Action</th>
                </tr>
            </thead>

            <tbody id="ticket_details1">
            <tbody id="ticket_details">
           
                <tr>

                <td data-column-index="7">
    <img src="../assets/images/loader1.gif" style="
    width: 50%;
    margin-left: 200%;
"  alt="Icon" class="icon">
</td>
                </tr>
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
				
				<div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel" aria-labelledby="analytics-tab-2" tabindex="0">
                
                <div class="row">

<div class="col-xl-2 col-md-2">
<div class="card">
  
<div id="myreportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
</div>

<span style="display:none;"  id="mydateviewsall"></span>

</div>
</div>

<div class="col-xl-2 col-md-2" style="width:18.6%">
					<div class="card">
						<div class="dropdown">
						<input type="text" class="searchInput multi_search veh_search" name="vehicle_details.registration_number" id="veh_reg_number" placeholder="Select Reg No" autocomplete="off">
						<div class="dropdown-options multi_search veh_options_marketers marketernameall">
							@if($reg_no)
								@foreach($reg_no as $reg)
									<div data-value="{{$reg->registration_number}}">{{$reg->registration_number}}</div>
								@endforeach
							@endif
						</div>
					</div>
                            </div>
                        </div>
@php $locat = App\Models\TblLocationModel::select('tbl_locations.name')->get(); @endphp 
                         <div class="col-xl-2 col-md-2" style="width:18.6%">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search veh_search" name="tbl_locations.name" id="veh_branch_views" placeholder="Select Branch" autocomplete="off">
                                    <div class="dropdown-options multi_search veh_options_marketers brachviewsall">
                                       @if($locat)
											@foreach($locat as $location)
                                        <div data-value="{{$location->name}}">{{$location->name}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-md-2" style="width:18.6%">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search veh_search" name="tblzones.name" id="veh_zone_views" placeholder="Select Zone" autocomplete="off">
                                    <div class="dropdown-options multi_search veh_options_marketers zoneviewsall">
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
<div class="">
<a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_my_filter"  style="
       height: 34px;
    width: 133px;
    font-size: 12px;
    background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
    
"><i class="ti ti-filter f-18"></i>&nbsp; More Filter</a>
</div>&nbsp;&nbsp;&nbsp;&nbsp;

</div>  
                </div>

                
                <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="mycounts">0</span> Rows for <span id="mydateallviews">Last 30 days</span></span>
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

        <div class="col-sm-12">
            
              <div class="card-body">
              <div class="table-container">
        <table class="tbl">
            <thead class="thd">
                <tr class="trview">
					<th class="thview">Vehicle ID</th>
					<th class="thview">Vehicle Type</th>
                    <th class="thview">Model</th>
                    <th class="thview">Reg No</th>
                    <th class="thview">	Document Type</th>
                    <th class="thview">	Fuel Type</th>
                    <th class="thview">Documents</th>
                    <th class="thview">Update Documents</th>
                    <th class="thview">Renewal Date</th>                    
                </tr>
            </thead>

            <tbody id="my_ticket_details1">
            <tbody id="my_ticket_details">
           
                <tr>

                <td data-column-index="7">
    <img src="../assets/images/loader1.gif" style="
    width: 50%;
    margin-left: 200%;
"  alt="Icon" class="icon">
</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div>
            Items per page:
            <select id="myitemsPerPageSelect">
                <option>10</option>
                <option>15</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="pagination" id="myticketpagination"></div>
    </div>

              </div>
     
          </div>                     
                </div>
				<div class="card-body pc-component btn-page">
                <div
                    class="modal fade"
                    id="exampleModal2"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Document Upload<span id="docu_id" ></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
                            <div class="modal-body">
                                 <div class="row">
                                 <input type="hidden" class="id" name="id" id="id_document" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                                 <input type="hidden" class="expire_dates" name="expire_dates" id="expire_dates" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                                 <input type="hidden" class="model" name="model" id="model" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                                  <input type="hidden" class="update_documents_all" name="update_documents_all" id="update_documents_all" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                               	<div class="col-sm-3" id="docu_vehicle_no">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Vehicle No: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_description errorss"></span>
											<div class="vehicle-dropdown">
												<input type="text" id="locationInput" class="searchVehicle" name="tbl_locations.name" placeholder="Select Vehicle No" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="location" autocomplete="off">
												<div class="vehicle-dropdown-options">
													
												</div>
											</div>
                                        </div>
                                    </div>
									<div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Expire Date :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_hplname errorss"></span>
                                            <input type="date" class="form-control" id="expire_update_date" name="expire_date" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Address">
                                        </div>
                                    </div>
									<div class="col-sm-3" id="vehicle_document_type">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Document Type:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_priority errorss"></span>
                                            <select class="mb-3 form-select" id="document_type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="document_type">
                                            <option value="">Select Document Type</option>
                                            <option value="1">Insurance Document</option>
                                            <option value="2">Registration Certificate Document</option>
                                            <option value="3">Vehicle Verification Certificate</option>
                                            <option value="4">Vehicle Inspection Certificate</option>
                                            </select>
                                        </div>
                                    </div>
                                <div class="col-sm-6">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Update Document [ .pdf ]</label>
                                    <div class="fallback"> &nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_images errorss"></span>
                                        <input name="files[]" id="pdf_update" type="file" accept="application/pdf" style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;" />
                                    </div>
                                </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="submit-document_update" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
                                </div>
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
        
  <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_edit_vehicle">
    <div class="offcanvas-body p-0">
        <div id="ecom-filter" class="collapse collapse-horizontal show">
            <div class="ecom-filter">
                <div class="card">
                    <!-- Sticky Header -->
                    <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                        <h5>Edit Vehicle</h5>
                        
                    </div>
                    <!-- Scrollable Block -->
					 <form method="post" action="{{ route('superadmin.vehicleupdate') }}" name="demoform" id="demoform"  enctype="multipart/form-data">
							@csrf                             
                    <div class="scroll-block position-relative">
                        <div class="card-body">
                        <div class="row">
                                    <div class="col-sm-12">
                                        <div class="mb-12">
										 <input type="hidden" name="id" id="edit_id">
												  <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_location errorss"></span>
											<div class="branch-dropdown">
												<input type="text" class="searchBranch" placeholder="Select Branch" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="branch" autocomplete="off">
												<div class="branch-dropdown-options">
													@if($locations)
														@foreach ($locations as $location)
															<div data-value="{{ $location->id }}">{{ $location->name}}</div>
															@endforeach
													@endif
												</div>
											</div>
												<br>
											</div>
                                    </div><br>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											<label class="form-label required" style="font-size: 12px;font-weight: 600;">Vehicle Type:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_location errorss"></span>										
												<div class="type-dropdown">
												<input type="text" class="searchType" placeholder="Select Vehicle Type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="vehicle_type" autocomplete="off">
												<div class="type-dropdown-options">
																									
												</div>
											</div>
												<br>
											</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											  <label class="form-label required" style="font-size: 12px;font-weight: 600;">Model:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="vehicle_model" name="make" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Vehicle Model" required>                                      
											<br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											  <label class="form-label required" style="font-size: 12px;font-weight: 600;">Year of Manufacture:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="yr_of_manufacture" name="year_of_manufacture" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Year of Manufacture" required>
                                        <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											  <label class="form-label required" style="font-size: 12px;font-weight: 600;">Registration Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="registration_number" name="registration_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Registration Number" required>
                                       <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">Engine Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="engine_number" name="engine_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Engine Number" required>
                                       <br> </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											 <label class="form-label required" style="font-size: 12px;font-weight: 600;">Chassis Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="chassis_number" name="chassis_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Chassis Number" required>
                                       <br></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
											<label class="form-label required" style="font-size: 12px;font-weight: 600;">Fuel Type: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_description errorss"></span>
                                            <select class="mb-3 form-select" id="fuel_type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="fuel_type">
                                            <option value="">Select Fuel Type</option>
                                                    <option value="1">Petrol</option>
                                                    <option value="2">Diesel</option>
                                                    <option value="3">Electronic Vehicle</option>
                                                    <option value="4">CNG</option>
                                            </select> 
											</div>
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

                <div class="card-body pc-component btn-page">
                <div
                    class="modal fade"
                    id="exampleModal"
                    role="dialog"
                    aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #6a6ee4;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel1" style="color: #ffffff;font-size: 12px;"> Add Vehicle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
						
                            <div class="modal-body">
							 <form method="post" action="{{ route('superadmin.vehicleadded') }}" name="demoform" id="demoform"  enctype="multipart/form-data">
							@csrf                             
                                <div class="row">                                    
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_location errorss"></span>
											<div class="loct-dropdown">
												<input type="text" class="searchLocation" placeholder="Select Branch" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="branch" autocomplete="off">
												<div class="loct-dropdown-options">
													@if($locations)
														@foreach ($locations as $location)
															<div data-value="{{ $location->id }}">{{ $location->name}}</div>
															@endforeach
													@endif
												</div>
											</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Type:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_department errorss"></span>
											<div class="dept-dropdown">
												<input type="text" class="searchDept" placeholder="Select Type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="vehicle_type" autocomplete="off">
												<div class="dept-dropdown-options">
													<div data-value="1">Visiting Consultant</div>
													<div data-value="2">Bio Waste</div>
												</div>
											</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row" id="consultantDiv" style="display:none;">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Year of Manufacture:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="year_of_manufacture" name="year_of_manufacture" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Year of Manufacture" required>
                                        </div>
                                    </div>
									<div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Registration Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="registration_number" name="registration_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Registration Number" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                           <label class="form-label required" style="font-size: 12px;font-weight: 600;">Engine Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="engine_number" name="engine_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Engine Number" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Chassis Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="chassis_number" name="chassis_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Chassis Number" required>
                                        </div>
                                    </div>
                                     <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Fuel Type: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_description errorss"></span>
                                            <select class="mb-3 form-select" id="fuel_type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="fuel_type">
                                            <option value="">Select Fuel Type</option>
                                                    <option value="1">Petrol</option>
                                                    <option value="2">Diesel</option>
                                                    <option value="3">Electronic Vehicle</option>
                                                    <option value="4">CNG</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
								
                                <div class="row" id="bioWasteDiv" style="display:none;">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Year of Manufacture:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="year_of_manufacture" name="year_of_manufacture" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Year of Manufacture" required>
                                        </div>
                                    </div>
									<div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Registration Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="registration_number" name="registration_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Registration Number" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                           <label class="form-label required" style="font-size: 12px;font-weight: 600;">Engine Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="engine_number" name="engine_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Engine Number" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Chassis Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="chassis_number" name="chassis_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Chassis Number" required>
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
			<div
                    class="modal fade"
                    id="exampleModal1"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Document Management system</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <div class="row">
                        <div class="col-sm-3"><br>
                                <div class="btn-group-vertical w-100" id="image_pdfs" style="
                                    margin-left: 11px;
                                ">
                                <button type="button" class="btn btn-primary">Tab 1</button>
                                <button type="button" class="btn btn-primary">Tab 2</button>
                                <button type="button" class="btn btn-primary">Tab 3</button>
                                <!-- More tabs if needed -->
                                </div>
                                </div>
                                    <div class="col-sm-9">
                                    <embed id="pdfmain" src="" width="100%" height="600px" />
                                    </div>
                                </div>
                </div>
                </div>
            </div>			
 <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filter">
    <div class="offcanvas-body p-0">
        <div id="ecom-filter" class="collapse collapse-horizontal show">
            <div class="ecom-filter">
                <div class="card">
                    <!-- Sticky Header -->
                    <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                        <h5>Filter</h5>
                        <a
                            href="#"
                            class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                            data-bs-dismiss="offcanvas"
                            data-bs-target="#offcanvas_mail_filter"
                        >
                            <i class="ti ti-x f-20"></i>
                        </a>
                    </div>
                    <!-- Scrollable Block -->
                    <div class="scroll-block position-relative">
                        <div class="card-body">
                        <div class="row">
                                    <div class="col-sm-12">
                                        <div class="mb-12">
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Vehicle Type:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_location errorss"></span>										
												<select class="mb-3 form-select morefittersclr" id="vehicle_type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="vehicle_type.type">
												<option value="">Select Vehicle Type</option>
												
												</select> 												
											</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>                                           
											<label class="form-label required" style="font-size: 12px;font-weight: 600;">Fuel Type: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_description errorss"></span>
                                            <select class="mb-3 form-select morefittersclr" id="vfuel_type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="vehicle_details.fuel_type">
                                            <option value="">Select Fuel Type</option>
                                                    <option value="1">Petrol</option>
                                                    <option value="2">Diesel</option>
                                                    <option value="3">Electronic Vehicle</option>
                                                    <option value="4">CNG</option>
                                            </select> 
											</div>
                                    </div>
                                </div>
                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2">Clear All</a>
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="vehicle_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_my_filter">
    <div class="offcanvas-body p-0">
        <div id="ecom-filter" class="collapse collapse-horizontal show">
            <div class="ecom-filter">
                <div class="card">
                    <!-- Sticky Header -->
                    <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                        <h5>Filter</h5>
                        <a
                            href="#"
                            class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                            data-bs-dismiss="offcanvas"
                            data-bs-target="#offcanvas_my_filter"
                        >
                            <i class="ti ti-x f-20"></i>
                        </a>
                    </div>
                    <!-- Scrollable Block -->
                    <div class="scroll-block position-relative">
                        <div class="card-body">
                        <div class="row">
                                    <div class="col-sm-12">
                                        <div class="mb-12">
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Vehicle Type:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_location errorss"></span>										
												<select class="mb-3 form-select myfittersclr" id="veh_type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="vehicle_type.type">
												<option value="">Select Vehicle Type</option>
												
												</select> 												
											</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>                                           
											<label class="form-label required" style="font-size: 12px;font-weight: 600;">Fuel Type: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_description errorss"></span>
                                            <select class="mb-3 form-select myfittersclr" id="fuel_typ" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="vehicle_details.fuel_type">
                                            <option value="">Select Fuel Type</option>
                                                    <option value="1">Petrol</option>
                                                    <option value="2">Diesel</option>
                                                    <option value="3">Electronic Vehicle</option>
                                                    <option value="4">CNG</option>
                                            </select> 
											</div>
                                    </div>
                                </div>
                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;"  class="btn btn-outline-danger w-50 me-2">Clear All</a>
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="my_vehicle_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
    </div>
                        </div>
                    </div>
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
<script src="{{ asset('/assets/payrequest/payrequest-details.js') }}"></script>

<script>
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
			
			 var errorMessage = @json($errors->first('registration_number'));

            if (errorMessage) {
                // Dispatch the custom event to trigger SweetAlert
                window.dispatchEvent(new CustomEvent('swal:toast', {
                    detail: {
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        background: '#f8d7da',
                    }
                }));
            }
        };
    </script>

<script type="text/javascript">

    const vehiclefetchUrl = "{{ route('superadmin.vehiclefetch') }}";
    const vehicledocumentUrl = "{{ route('superadmin.vehicledocument') }}";
    const documentupdatedUrl = "{{ route('superadmin.vehicledocumentupdate') }}";
    const fetchUrldocfitter = "{{ route('superadmin.vehicledocumentfilter') }}";
    const fetchdocUrlfitter = "{{ route('superadmin.vehiclemorefilter') }}";
    const fetchmoreUrlfitter = "{{ route('superadmin.vehiclemoredocfilter') }}";
    const fetchVehdocfitter = "{{ route('superadmin.vehicledocumentUrlfilter') }}";
    const vehicledatefillter = "{{ route('superadmin.vehicledatefillter') }}";
	 var documentBaseUrl = "{{ asset('document_data') }}";	 

    </script>
<script>

        // Set the initial start and end dates
        var start = moment().subtract(29, 'days');
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
		
		// Initialize the date range picker
        $('#myreportrange').daterangepicker({
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
        }, my);
    
        // Set initial date range text
        cb(start, end);
		my(start, end);

        $(document).on('click', '.editbtn', function (e) {
                    $('#exampleModal').modal('show');
                });
        $(document).on('click', '.doceditbtn', function (e) {
                    $('#exampleModal2').modal('show');
					$("#docu_vehicle_no").show();
					$("#vehicle_document_type").show();
					$('#id_document').val('');
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
        $(document).ready(function () {
            // Show dropdown on input focus and while typing
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

            // Ensure only valid values remain in the input field (for multiple search)
            $(document).on("blur", ".multi_search", function () {
                const inputField = $(this);
                const typedValues = inputField.val().split(",").map(v => v.trim());
                const validOptions = inputField.siblings(".dropdown-options").find("div").map(function () {
                    return $(this).text().trim();
                }).get();

                // Filter typed values to keep only valid options
                const filteredValues = typedValues.filter(v => validOptions.includes(v));

                inputField.data("values", filteredValues);
                inputField.val(filteredValues.join(", "));
            });

            // Close dropdown when clicking outside
            $(document).on("click", function (event) {
                if (!$(event.target).closest(".dropdown").length) {
                    $(".dropdown").removeClass("active");
                }
            });
        });

            </script>
			
			<script>
$(document).on("focus", ".searchInput", function () {
    $(this).closest(".dropdown").addClass("active");
});

// Search and filter dropdown options as you type
$(document).on("input", ".searchInput", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText) || searchText === "");
    });
});

// Handle option click: Add if not selected, remove if already selected
$(document).on("click", ".dropdown-options div", function () {
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".dropdown").find(".searchInput");
    let currentValues = inputField.val().split(",").map(v => v.trim()).filter(v => v !== "");

    // Toggle selection: Remove if exists, add if not
    if (currentValues.includes(selectedValue)) {
        currentValues = currentValues.filter(v => v !== selectedValue); // Remove value
        $(this).removeClass("selected"); // Remove highlight
    } else {
        currentValues.push(selectedValue); // Add value
        $(this).addClass("selected"); // Highlight selected item
    }

    inputField.val(currentValues.join(", "));
});

// Update dropdown UI on input focus (keep selected values highlighted)
$(document).on("focus", ".searchInput", function () {
    const inputField = $(this);
    let currentValues = inputField.val().split(",").map(v => v.trim()).filter(v => v !== "");

    // Highlight already selected values
    inputField.siblings(".dropdown-options").find("div").each(function () {
        if (currentValues.includes($(this).text())) {
            $(this).addClass("selected");
        } else {
            $(this).removeClass("selected");
        }
    });
});

// Close dropdown when clicking outside
$(document).on("click", function (event) {
    if (!$(event.target).closest(".dropdown").length) {
        $(".dropdown").removeClass("active");
    }
});

            </script>

<script>
	// Handle input focus: Add 'active' class to the closest dropdown
$(document).on("focus", ".searchDept", function () {
    $(this).closest(".dept-dropdown").addClass("active");
});
// Handle input focus: Add 'active' class to the closest dropdown
$(document).on("focus", ".searchType", function () {
    $(this).closest(".type-dropdown").addClass("active");
});

// Search and filter dropdown options as you type
$(document).on("input", ".searchType", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".type-dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText) || searchText === "");
    });
});
// Search and filter dropdown options as you type
$(document).on("input", ".searchDept", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".dept-dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText) || searchText === "");
    });
});

// Handle option click: Select a single option and close the dropdown
$(document).on("click", ".type-dropdown-options div", function () {
	let category = $(this).attr('data-value');
	
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".type-dropdown").find(".searchType");

    // Set the input field value to the selected option
    inputField.val(selectedValue);

    // Highlight the selected option
    $(this).addClass("selected").siblings().removeClass("selected");

    // Close the dropdown after selecting an option (optional)
    $(this).closest(".type-dropdown").removeClass("active");
	
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

// Update dropdown UI on input focus (clear previous selection highlights)
$(document).on("focus", ".searchType", function () {
    const inputField = $(this);
    const selectedValue = inputField.val().trim();

    // Highlight the selected option and remove the highlight from others
    inputField.siblings(".type-dropdown-options").find("div").each(function () {
        if ($(this).text() === selectedValue) {
            $(this).addClass("selected");
        } else {
            $(this).removeClass("selected");
        }
    });
});

	</script>
<script>
	// Handle input focus: Add 'active' class to the closest dropdown
$(document).on("focus", ".searchLocation", function () {
    $(this).closest(".loct-dropdown").addClass("active");
});
// Handle input focus: Add 'active' class to the closest dropdown
$(document).on("focus", ".searchVehicle", function () {
    $(this).closest(".vehicle-dropdown").addClass("active");
});
// Handle input focus: Add 'active' class to the closest dropdown
$(document).on("focus", ".searchBranch", function () {
    $(this).closest(".branch-dropdown").addClass("active");
});

// Search and filter dropdown options as you type
$(document).on("input", ".searchLocation", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".loct-dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText) || searchText === "");
    });
});

// Search and filter dropdown options as you type
$(document).on("input", ".searchBranch", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".branch-dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText) || searchText === "");
    });
});

// Search and filter dropdown options as you type
$(document).on("input", ".searchVehicle", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".vehicle-dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText) || searchText === "");
    });
});

// Handle option click: Select a single option and close the dropdown
$(document).on("click", ".loct-dropdown-options div", function () {
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".loct-dropdown").find(".searchLocation");

    // Set the input field value to the selected option
    inputField.val(selectedValue);

    // Highlight the selected option
    $(this).addClass("selected").siblings().removeClass("selected");

    // Close the dropdown after selecting an option (optional)
    $(this).closest(".loct-dropdown").removeClass("active");
	
});

// Handle option click: Select a single option and close the dropdown
$(document).on("click", ".branch-dropdown-options div", function () {
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".branch-dropdown").find(".searchBranch");

    // Set the input field value to the selected option
    inputField.val(selectedValue);

    // Highlight the selected option
    $(this).addClass("selected").siblings().removeClass("selected");

    // Close the dropdown after selecting an option (optional)
    $(this).closest(".branch-dropdown").removeClass("active");
	
});

// Handle option click: Select a single option and close the dropdown
$(document).on("click", ".vehicle-dropdown-options div", function () {
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".vehicle-dropdown").find(".searchVehicle");

    // Set the input field value to the selected option
    inputField.val(selectedValue);

    // Highlight the selected option
    $(this).addClass("selected").siblings().removeClass("selected");

    // Close the dropdown after selecting an option (optional)
    $(this).closest(".vehicle-dropdown").removeClass("active");
	
});

// Update dropdown UI on input focus (clear previous selection highlights)
$(document).on("focus", ".searchLocation", function () {
    const inputField = $(this);
    const selectedValue = inputField.val().trim();
    // Highlight the selected option and remove the highlight from others
    inputField.siblings(".loct-dropdown-options").find("div").each(function () {
        if ($(this).text() === selectedValue) {
            $(this).addClass("selected");
        } else {
            $(this).removeClass("selected");
        }
    });
});

// Update dropdown UI on input focus (clear previous selection highlights)
$(document).on("focus", ".searchVehicle", function () {
    const inputField = $(this);
    const selectedValue = inputField.val().trim();
	// Highlight the selected option and remove the highlight from others
    inputField.siblings(".vehicle-dropdown-options").find("div").each(function () {
        if ($(this).text() === selectedValue) {
            $(this).addClass("selected");
        } else {
            $(this).removeClass("selected");
        }
    });
});
// Update dropdown UI on input focus (clear previous selection highlights)
$(document).on("focus", ".searchBranch", function () {
    const inputField = $(this);
    const selectedValue = inputField.val().trim();
	// Highlight the selected option and remove the highlight from others
    inputField.siblings(".branch-dropdown-options").find("div").each(function () {
        if ($(this).text() === selectedValue) {
            $(this).addClass("selected");
        } else {
            $(this).removeClass("selected");
        }
    });
});
</script>

	<script>
$(document).ready(function() {
    // Toggle dropdown visibility when clicking input
    $('.searchBranch').on('click', function(event) {
        event.stopPropagation(); // Prevent the click from propagating to the document
        $(this).next('.branch-dropdown-options').toggle(); // Toggle the dropdown visibility
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.branch-dropdown').length) {
            $('.branch-dropdown-options').hide(); // Hide the dropdown if clicked outside
        }
    });

    // Handle selecting an option
    $('.branch-dropdown-options div').on('click', function() {
        var selectedBranch = $(this).text(); // Get the branch name
        $('.searchBranch').val(selectedBranch); // Set the input field value
        $('.branch-dropdown-options').hide(); // Close the dropdown
    });
	
	 $('.searchLocation').on('click', function(event) {
        event.stopPropagation(); // Prevent click from propagating to the document
        $(this).next('.loct-dropdown-options').toggle(); // Toggle the visibility of the dropdown
    });

    // Close the dropdown if you click outside of the dropdown
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.loct-dropdown').length) {
            $('.loct-dropdown-options').hide(); // Hide the dropdown if click was outside
        }
    });

    // When a branch option is selected, update the input and close the dropdown
    $('.loct-dropdown-options div').on('click', function() {
        var selectedBranch = $(this).text(); // Get the text of the selected branch
        $('.searchLocation').val(selectedBranch); // Set the input field value
        $('.loct-dropdown-options').hide(); // Hide the dropdown
    });
	
	$('#locationInput').on('click', function(event) {
        event.stopPropagation(); // Prevent the click from propagating to the document
        $(this).next('.vehicle-dropdown-options').toggle(); // Toggle the visibility of the dropdown
    });

    // Close the dropdown if you click outside of the dropdown
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.vehicle-dropdown').length) {
            $('.vehicle-dropdown-options').hide(); // Hide the dropdown if click was outside
        }
    });

    // When a vehicle option is selected, update the input and close the dropdown
    $('.vehicle-dropdown-options div').on('click', function() {
        var selectedVehicle = $(this).text(); // Get the text of the selected vehicle
        $('#locationInput').val(selectedVehicle); // Set the input field value
        $('.vehicle-dropdown-options').hide(); // Hide the dropdown
    });
	
	$('.searchDept').on('click', function(event) {
        event.stopPropagation(); // Prevent the click from propagating to the document
        $(this).next('.dept-dropdown-options').toggle(); // Toggle the visibility of the dropdown
    });

    // Close the dropdown if you click outside of the dropdown
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.dept-dropdown').length) {
            $('.dept-dropdown-options').hide(); // Hide the dropdown if click was outside
        }
    });

    // When a vehicle type option is selected, update the input and close the dropdown
    $('.dept-dropdown-options div').on('click', function() {
        var selectedType = $(this).text(); // Get the text of the selected vehicle type
        $('.searchDept').val(selectedType); // Set the input field value
        $('.dept-dropdown-options').hide(); // Hide the dropdown
    });
	
	 $('.searchType').on('click', function(event) {
        event.stopPropagation(); // Prevent the click from propagating to the document
        $(this).next('.type-dropdown-options').toggle(); // Toggle the visibility of the dropdown
    });

    // Close the dropdown if you click outside of the dropdown
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.type-dropdown').length) {
            $('.type-dropdown-options').hide(); // Hide the dropdown if click was outside
        }
    });

    // When a vehicle type option is selected, update the input and close the dropdown
    $('.type-dropdown-options div').on('click', function() {
        var selectedType = $(this).text(); // Get the text of the selected vehicle type
        $('.searchType').val(selectedType); // Set the input field value
        $('.type-dropdown-options').hide(); // Hide the dropdown
    });
});
</script>
	
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
