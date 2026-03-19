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
td#Tooltip_Text_container {
        max-width: 25em;
        height: auto;
        position: relative;
		cursor: pointer;
      }

      td#Tooltip_Text_container a {
        text-decoration: none;
        color: black;
        cursor: default;
        font-weight: normal;
      }

      td#Tooltip_Text_container a span.tooltips {
        visibility: hidden;
        opacity: 0;
        transition: visibility 0s linear 0.2s, opacity 0.2s linear;
        position: absolute;
		    margin-left: -15em;
        left: 50px;
        top: 6px;
        width: 30em;
        border: 1px solid #404040;
        padding: 0.2em 0.5em;
        cursor: default;
        line-height: 140%;
        font-size: 12px;
        font-family: 'Segoe UI';
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        -moz-box-shadow: 7px 7px 5px -5px #666;
        -webkit-box-shadow: 7px 7px 5px -5px #666;
        box-shadow: 7px 7px 5px -5px #666;
        background: #E4E5F0 repeat-x;
      }

     td#Tooltip_Text_container i:hover + a span.tooltips,
td#Tooltip_Text_container a span.tooltips:hover {
        visibility: visible;
        opacity: 1;
        transition-delay: 0.2s;
		width: 300px;
		text-align: justify;
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
		<div class="col-md-3 col-sm-3">
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
     background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
    
"><i class="ti ti-plus f-18"></i> Add Ticket</a></div>

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
                      id="analytics-tab-3"
                      data-bs-toggle="tab"
                      data-bs-target="#analytics-tab-3-pane"
                      type="button"
                      role="tab"
                      aria-controls="analytics-tab-3-pane"
                      aria-selected="false"
                      >My Tickets</button
                    >
                  </li>				  
				 @if(auth()->user()->role_id == 1 && auth()->user()->access_limits == 1)				 
                  <li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="analytics-tab-1"
                      data-bs-toggle="tab"
                      data-bs-target="#analytics-tab-1-pane"
                      type="button"
                      role="tab"
                      aria-controls="analytics-tab-1-pane"
                      aria-selected="true"
                      >Approvals</button
                    >
                  </li>
				   @endif
				   @if(auth()->user()->role_id == 1 && (auth()->user()->access_limits == 1 || auth()->user()->access_limits == 2))
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
                      >Tickets</button
                    >
                  </li>
				   @endif
                </ul>
              </div>

</div>
</div><br>

              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show" id="analytics-tab-1-pane" role="tabpanel" aria-labelledby="analytics-tab-1" tabindex="0">

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
@php $user_name = App\Models\usermanagementdetails::select('users.user_fullname')
                                ->whereIn('users.role_id', [3,4,1])->get();    
                  @endphp 
<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search marketervalues_search" name="users.user_fullname" id="marketer_fetch" placeholder="Select Marketer" autocomplete="off">
                                    <div class="dropdown-options multi_search option_marketers marketernameall">
									 @if($user_name)
											@foreach($user_name as $user)
                                         <div data-value="{{$user->user_fullname}}">{{$user->user_fullname}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php $location = App\Models\TblLocationModel::select('tbl_locations.name')->get(); @endphp 
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search marketervalues_search" name="tbl_locations.name" id="branchviews" placeholder="Select Branch" autocomplete="off">
                                    <div class="dropdown-options multi_search option_marketers brachviewsall">
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
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search marketervalues_search" name="tblzones.name" id="zoneviews" placeholder="Select Zone" autocomplete="off">
                                    <div class="dropdown-options multi_search option_marketers zoneviewsall">
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
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search marketervalues_search" name="department.depart_name" id="dept_views" placeholder="Select Dept" autocomplete="off">
                                    <div class="dropdown-options multi_search option_marketers dept_views">
                                        @if($categories)
												@foreach ($categories as $category)
													<div data-value="{{ $category->id }}">{{ $category->depart_name}}</div>
													@endforeach
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>

<div class="col-xl-2 col-md-2">
<div class="">
<a href="#" class="btn btn-primary d-inline-flex app_ticket_search" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filter"  style="
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
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="counts">0</span> Rows for <span id="dateallviews">Last 30 days</span></span>
<span class="search_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span style="cursor: pointer;" id="created_by_search" class="badge bg-success value_views_main"></span>
<span style="cursor: pointer;" id="branch_search" class="badge bg-success value_views_main"></span>
<span style="cursor: pointer;" id="zone_search" class="badge bg-success value_views_main"></span>
<span style="cursor: pointer;" id="dept_search" class="badge bg-success value_views_main"></span>
<span class="badge bg-success value_views"></span>
<span class="badge bg-success value_views"></span>
<span class="badge bg-success value_views"></span>
<span class="badge bg-success value_views"></span>
<span class="badge bg-success value_views"></span>
<span class="badge bg-success value_views"></span>
<span  class="badge bg-danger clear_view" style="display:none;">Clear all</span>
</p><br>

        <div class="col-sm-12">
            
              <div class="card-body">
              <div class="table-container">
        <table class="tbl">
            <thead class="thd">
                <tr class="trview">
					<th class="thview">Ticket No</th>
                    <th class="thview">Branch</th>
                    <th class="thview">From Department</th>
                    <th class="thview">To Department</th>
                    <th class="thview">Sub Department</th>
                    <th class="thview">Target Date</th>
                    <th class="thview">Priority</th>
                    <th class="thview">Subject</th>
                    <th class="thview">Description</th>
					<th class="thview">Created By</th>
                    <th class="thview" style="width:13%">Status</th>
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
  
<div id="allreportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
</div>

<span style="display:none;"  id="alldateviewsall"></span>

</div>
</div>
<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search allmarketer_search" name="users.user_fullname" id="allmarketer_fetch" placeholder="Select Marketer" autocomplete="off">
                                    <div class="dropdown-options multi_search all_options_marketers marketernameall">
									 @if($user_name)
											@foreach($user_name as $user)
                                         <div data-value="{{$user->user_fullname}}">{{$user->user_fullname}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>
@php $locati = App\Models\TblLocationModel::select('tbl_locations.name')->get(); @endphp 
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search allmarketer_search" name="tbl_locations.name" id="allbranchviews" placeholder="Select Branch" autocomplete="off">
                                    <div class="dropdown-options multi_search all_options_marketers brachviewsall">
                                       @if($locati)
											@foreach($locati as $location)
                                        <div data-value="{{$location->name}}">{{$location->name}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search allmarketer_search" name="tblzones.name" id="allzoneviews" placeholder="Select Zone" autocomplete="off">
                                    <div class="dropdown-options multi_search all_options_marketers zoneviewsall">
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
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search allmarketer_search" name="department.depart_name" id="alldeptviews" placeholder="Select Dept" autocomplete="off">
                                    <div class="dropdown-options multi_search all_options_marketers alldeptviews">
                                        @if($categories)
												@foreach ($categories as $category)
													<div data-value="{{ $category->id }}">{{ $category->depart_name}}</div>
													@endforeach
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>

<div class="col-xl-2 col-md-2">
<div class="">
<a href="#" class="btn btn-primary d-inline-flex all_ticket_search" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_all_filter" data-custom-param="yourValueHere" style="
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
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="allcounts">0</span> Rows for <span id="alldateallviews">Last 30 days</span></span>
<span class="all_search_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span style="cursor: pointer;" id="created_all_search" class="badge bg-success value_views_allsearch"></span>
<span style="cursor: pointer;" id="allbranch_search" class="badge bg-success value_views_allsearch"></span>
<span style="cursor: pointer;" id="allzone_search" class="badge bg-success value_views_allsearch"></span>
<span style="cursor: pointer;" id="alldept_search" class="badge bg-success value_views_allsearch"></span>
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
					<th class="thview" style="width:13%">Ticket No</th>
                    <th class="thview">Branch</th>
                    <th class="thview">From Department</th>
                    <th class="thview">To Department</th>
                    <th class="thview">Sub Department</th>
                    <th class="thview">Target Date</th>
                    <th class="thview">Priority</th>
                    <th class="thview">Subject</th>
                    <th class="thview">Description</th>
					<th class="thview" style="width:14%">Created By</th>
                    <th class="thview" style="width:13%">Status</th>
                </tr>
            </thead>

            <tbody id="all_ticket_details1">
            <tbody id="all_ticket_details">
           
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
            <select id="allitemsPerPageSelect">
                <option>10</option>
                <option>15</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="pagination" id="allticketpagination"></div>
    </div>

              </div>
     
          </div>                     
                </div>
				
<div class="tab-pane fade show active" id="analytics-tab-3-pane" role="tabpanel" aria-labelledby="analytics-tab-3" tabindex="0">
                
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
<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search mymarketer_search" name="users.user_fullname" id="mymarketer_fetch" placeholder="Select Marketer" autocomplete="off">
                                    <div class="dropdown-options multi_search my_options_marketers marketernameall">
									 @if($user_name)
											@foreach($user_name as $user)
                                        <div data-value="{{$user->user_fullname}}">{{$user->user_fullname}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>
@php $locat= App\Models\TblLocationModel::select('tbl_locations.name')->get(); @endphp 
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search mymarketer_search" name="tbl_locations.name" id="mybranchviews" placeholder="Select Branch" autocomplete="off">
                                    <div class="dropdown-options multi_search my_options_marketers brachviewsall">
                                       @if($locat)
											@foreach($locat as $location)
                                        <div data-value="{{$location->name}}">{{$location->name}}</div>
                                        @endforeach
									  @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search mymarketer_search" name="tblzones.name" id="myzoneviews" placeholder="Select Zone" autocomplete="off">
                                    <div class="dropdown-options multi_search my_options_marketers zoneviewsall">
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
                                <div class="dropdown">
                                    <input type="text" class="searchInput multi_search mymarketer_search" name="department.depart_name" id="mydeptviews" placeholder="Select Dept" autocomplete="off">
                                    <div class="dropdown-options multi_search my_options_marketers deptviewsall">
                                        @if($categories)
												@foreach ($categories as $category)
													<div data-value="{{ $category->id }}">{{ $category->depart_name}}</div>
													@endforeach
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>

<div class="col-xl-2 col-md-2">
<div class="">
<a href="#" class="btn btn-primary d-inline-flex my_ticket_search" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_my_filter"  style="
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
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="my_counts">0</span> Rows for <span id="mydateallviews">Last 30 days</span></span>
<span class="my_search_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span style="cursor: pointer;" id="created_my_search" class="badge bg-success value_views_mysearch"></span>
<span style="cursor: pointer;" id="mybranch_search" class="badge bg-success value_views_mysearch"></span>
<span style="cursor: pointer;" id="myzone_search" class="badge bg-success value_views_mysearch"></span>
<span style="cursor: pointer;" id="mydept_search" class="badge bg-success value_views_mysearch"></span>
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
					<th class="thview">Ticket No</th>
                    <th class="thview">Branch</th>
                    <th class="thview">From Department</th>
                    <th class="thview">To Department</th>
                    <th class="thview">Sub Department</th>
                    <th class="thview">Target Date</th>
                    <th class="thview">Priority</th>
                    <th class="thview">Subject</th>
                    <th class="thview">Description</th>
					<th class="thview">Created By</th>
                    <th class="thview" style="width:13%">Status</th>
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
              </div>
            </div>
          </div>
          <!-- Column Rendering table end -->
          <!-- Multiple Table Control Elements start -->
          <!-- Row Created Callback table end -->
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
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Ticket Status</label>												
												<div class="multiselect-container" tabindex="0">
                                                    <input type="text" class="multiselect-input morefittersclr" placeholder="Select Ticket Status" name="ticket_status_master.status_name" readonly id="selectedStatus">
                                                    <div class="multiselect-options ticket_status">
													@if($statuses)
																@foreach ($statuses as $status)
                                                        <label>															
                                                            <input type="checkbox" value="{{ $status->id }}" class="statusCheckbox" onchange="updateSelectedValues()"> {{ $status->status_name}}															
                                                        </label>
														@endforeach
													@endif
                                                    </div>
                                                </div>
												
											</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>                                           
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Ticket Priority</label>
												<div class="multiselect-container" tabindex="0">
                                                    <input type="text" class="multiselect-input morefittersclr" placeholder="Select Ticket Priority" name="ticket_priority.priority_name" readonly id="priorityStatus">
                                                    <div class="multiselect-options">
													@if($priorities)
																@foreach ($priorities as $priority)
                                                        <label>															
                                                            <input type="checkbox" value="{{ $priority->id }}" class="priorityCheckbox" onchange="updateSelectedValues()"> {{ $priority->priority_name}}															
                                                        </label>
														@endforeach
													@endif
                                                    </div>
                                                </div>
												
											</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Location</label>												
												<div class="loct-dropdown">
												<input type="text" id="locationInput" class="searchLocation morefittersclr" name="tbl_locations.name" placeholder="Select Location" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="location">
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

                                </div>
                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2">Clear All</a>
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="ticket_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_all_filter">
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
                            data-bs-target="#offcanvas_all_filter"
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
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Ticket Status</label>												
												<div class="multiselect-container" tabindex="0">
                                                    <input type="text" class="multiselect-input allfittersclr" placeholder="Select Ticket Status" name="ticket_status_master.status_name" readonly id="allselectedStatus">
                                                    <div class="multiselect-options ticket_status">
													@if($statuses)
																@foreach ($statuses as $status)
                                                        <label>															
                                                            <input type="checkbox" value="{{ $status->id }}" class="allstatusCheckbox" onchange="allSelectedValues()"> {{ $status->status_name}}															
                                                        </label>
														@endforeach
													@endif
                                                    </div>
                                                </div>
												
											</div>
                                    </div>
                                     <div class="col-sm-12">
                                        <div class="mb-12"><br>                                           
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Ticket Priority</label>
												<div class="multiselect-container" tabindex="0">
                                                    <input type="text" class="multiselect-input allfittersclr" placeholder="Select Ticket Priority" name="ticket_priority.priority_name" readonly id="allpriorityStatus">
                                                    <div class="multiselect-options">
													@if($priorities)
																@foreach ($priorities as $priority)
                                                        <label>															
                                                            <input type="checkbox" value="{{ $priority->id }}" class="allpriorityCheckbox" onchange="allSelectedValues()"> {{ $priority->priority_name}}															
                                                        </label>
														@endforeach
													@endif
                                                    </div>
                                                </div>
												
											</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Location</label>												
												<div class="allloct-dropdown">
												<input type="text" id="alllocationInput" class="allsearchLocation allfittersclr" name="tbl_locations.name" placeholder="Select Location" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="location">
												<div class="allloct-dropdown-options">
													@if($locations)
														@foreach ($locations as $location)
															<div data-value="{{ $location->id }}">{{ $location->name}}</div>
															@endforeach
													@endif
												</div>
											</div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;"  class="btn btn-outline-danger w-50 me-2">Clear All</a>
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="all_ticket_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
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
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Ticket Status</label>												
												<div class="multiselect-container" tabindex="0">
                                                    <input type="text" class="multiselect-input myfittersclr" placeholder="Select Ticket Status" name="ticket_status_master.status_name" readonly id="myselectedStatus">
                                                    <div class="multiselect-options ticket_status">
													@if($statuses)
																@foreach ($statuses as $status)
                                                        <label>															
                                                            <input type="checkbox" value="{{ $status->id }}" class="mystatusCheckbox" onchange="mySelectedValues()"> {{ $status->status_name}}															
                                                        </label>
														@endforeach
													@endif
                                                    </div>
                                                </div>
												
											</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>                                           
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Ticket Priority</label>
												<div class="multiselect-container" tabindex="0">
                                                    <input type="text" class="multiselect-input myfittersclr" placeholder="Select Ticket Priority" name="ticket_priority.priority_name" readonly id="mypriorityStatus">
                                                    <div class="multiselect-options">
													@if($priorities)
																@foreach ($priorities as $priority)
                                                        <label>															
                                                            <input type="checkbox" value="{{ $priority->id }}" class="mypriorityCheckbox" onchange="mySelectedValues()"> {{ $priority->priority_name}}															
                                                        </label>
														@endforeach
													@endif
                                                    </div>
                                                </div>
												
											</div>
                                    </div>
                                   <div class="col-sm-12">
                                        <div class="mb-12"><br>
												<label class="form-label required" style="font-size: 12px;font-weight: 600;">Location</label>												
												<div class="myloct-dropdown">
												<input type="text" id="mylocationInput" class="mysearchLocation myfittersclr" name="tbl_locations.name" placeholder="Select Location" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="location">
												<div class="myloct-dropdown-options">
													@if($locations)
														@foreach ($locations as $location)
															<div data-value="{{ $location->id }}">{{ $location->name}}</div>
															@endforeach
													@endif
												</div>
											</div>
                                        </div>
                                    </div>                                  

                                </div>
                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;"  class="btn btn-outline-danger w-50 me-2">Clear All</a>
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="my_ticket_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-12">
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
                        <h5 class="modal-title" id="exampleModalLabel1" style="color: #ffffff;font-size: 12px;"> Add Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
						
                            <div class="modal-body">
							 <form method="post" action="{{ route('superadmin.ticketadded') }}" name="demoform" id="demoform"  enctype="multipart/form-data">
							@csrf
                                 <input type="hidden" class="ticketId" name="ticketId" id="ticketId" value="">
                                <input type="hidden" class="userid" name="userid" id="userid" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                                <div class="row">                                    
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Location:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_location errorss"></span>
											<div class="loct-dropdown">
												<input type="text" class="searchLocation" placeholder="Select Location" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="location">
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
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">From Department:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_department errorss"></span>
											<div class="loct-dropdown">
												<input type="text" class="searchLocation" placeholder="Select From Department" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="from_department">
												<div class="loct-dropdown-options">
											@if($categories)
												@foreach ($categories as $category)
													<div data-value="{{ $category->id }}">{{ $category->depart_name}}</div>
													@endforeach
                                            @endif
												</div>
											</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">To Department:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_department errorss"></span>
											<div class="dept-dropdown">
												<input type="text" class="searchDept" placeholder="Select To Department" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="department">
												<div class="dept-dropdown-options">
											@if($categories)
												@foreach ($categories as $category)
													<div data-value="{{ $category->id }}">{{ $category->depart_name}}</div>
													@endforeach
                                            @endif
												</div>
											</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Sub Department:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_sub_department errorss"></span>
                                            <select class="mb-3 form-select" id="sub_department_id" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="sub_department_id">                                                                                                                   
											</select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Target Date :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_target_date errorss"></span>
                                             <input type="date" class="form-control" id="targetDate" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" name="target_date" onfocus="disablePastDates()"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Priority:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_priority errorss"></span>
                                            <select class="mb-3 form-select" id="ticket_priority" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="priority">
                                            <option value="">Select Priority</option>
                                            <option value="1">Low</option>
                                            <option value="2">Medium</option>
                                            <option value="3">High</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Subject:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_subject errorss"></span>
                                            <input type="text" class="form-control" id="ticket_subject" name="subject" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Subject">
                                        </div>
                                    </div>
                                     <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Description: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_description errorss"></span>
                                            <!--<input type="text" class="form-control" id="ticket_description" name="description" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Enter Description">-->
											<textarea class="form-control" id="ticket_description" rows="4" name="description" placeholder="Type your message here..."></textarea>
                                        </div>
                                    </div>
                                </div>
                             
                                <div class="row">
                                    <div class="col-sm-9">
                                   
                                    <div class="fallback"> &nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_images errorss"></span>
                                       <!-- <input name="files[]" id="image_uploads" type="file" multiple />-->
                                       <div class="mb-3 ">
										<div class="form-group dropzone">
										  <div id="dropzoneDragArea" class="dz-default dz-message dropzoneDragArea ">
											<span>Upload Attachments (Images and Pdf only)</span>
										  </div>
										  <div class="dropzone-previews"></div>
										</div>
									  </div>
										<ul id="save_msgList1"></ul>
                                    </div>
                                  
                                </div>
                                <div class="modal-footer">
                                    <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="submit-ticket-datas" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
                                </div>                               
                        </div>
						</form>
						 <button id="ticketCreate" class="btn btn-light-warning" style="display:none;">Try me!</button>
						 <button id="ticketMaxSize" class="btn btn-light-danger" style="display:none;">Try me!</button>
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
<script src="{{ asset('/assets/superadmin/ticket-details.js') }}"></script>

<script>
        Dropzone.autoDiscover = false;
        // Dropzone.options.demoform = false;	
        let token = $('meta[name="csrf-token"]').attr('content');
        $(function() {
        var myDropzone = new Dropzone("div#dropzoneDragArea", { 
          paramName: "file",
          url: "{{ url('superadmin/storeImage') }}",
		  clickable: "#dropzoneDragArea span",
          previewsContainer: 'div.dropzone-previews',
          addRemoveLinks: true,
          autoProcessQueue: false,
          uploadMultiple: true,
          acceptedFiles: ".jpeg,.jpg,.png,.pdf",
          maxFilesize: 1, //MB
          parallelUploads: 10,
          maxFiles: 10,
          params: {
                _token: token
            },
          // The setting up of the dropzone
          init: function() {
            
              var myDropzone = this;
              //form submission code goes here
              $("form[name='demoform']").submit(function(event) {
                //Make sure that the form isn't actully being sent.
                event.preventDefault();			
				$('#exampleModal').modal('hide');				
				Swal.fire({
				title: 'Are you sure?',
				text: "Do you want to submit the form?",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, submit it!',
				cancelButtonText: 'Cancel'
				}).then((result) => {
					if (result.isConfirmed) {
						// If the user clicks 'Yes', submit the form
						URL = $("#demoform").attr('action');
						formData = $('#demoform').serialize();
						$.ajax({
						  type: 'POST',
						  url: URL,
						  data: formData,
						  success: function(result) {
							if(result.status == "success") {
							  $('#ticketCreate').click();
							//reset the form
							$('#demoform')[0].reset();
							//reset dropzone
							$('.dropzone-previews').empty();
							  var userid = result.user_id;
							  
							$("#userid").val(userid); // inseting userid into hidden input field
							
							  //process the queue
							  myDropzone.processQueue();
							  location.reload();
							} else {
								window.dispatchEvent(new CustomEvent('swal:toast', {
									detail: {
									  title:'Info!',
									  text: result.errors,
									  icon: 'error',
									  background: '#f8d7da',
									}
								}));
							  $.each(response.errors, function (key, err_value) {
								$('#save_msgList').addClass('alert alert-danger');
								$('#save_msgList').append('<li>' + err_value +
									'</li>');
							  });
							}
						  }
						});
					}
				});
              });

              //Gets triggered when we submit the image.
              this.on('sending', function(file, xhr, formData){
			   //fetch the user id from hidden input field and send that userid with our image
                let userid = document.getElementById('userid').value;
              formData.append('userid', userid);
            });
            
              this.on("success", function (file, response) {
                    
                });

                this.on("queuecomplete", function () {
            
                });
            
                // Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
              // of the sending event because uploadMultiple is set to true.
              this.on("sendingmultiple", function() {
                // Gets triggered when the form is actually being sent.
                // Hide the success button or the complete form.
              });
            
              this.on("successmultiple", function(files, response) {
                // Gets triggered when the files have successfully been sent.
                // Redirect user or notify of success.
              });
            
              this.on("errormultiple", function(files, response) {
                // Gets triggered when there was an error sending the files.
                // Maybe show form again, and notify user of error
              });
          }
          });
        myDropzone.on("error", function(file) {
          myDropzone.removeFile(file);
            $('#ticketMaxSize').click();
        });
       
      });
    </script>

<script type="text/javascript">

    const ticketfetchUrl = "{{ route('superadmin.ticketfetch') }}";
    const allticketfetchUrl = "{{ route('superadmin.allticketfetch') }}";
    const myticketfetchUrl = "{{ route('superadmin.myticketfetch') }}";
	const ticketfillter = "{{ route('superadmin.ticketfillter') }}";
	const allticketfillter = "{{ route('superadmin.allticketfillter') }}";
	const myticketfillter = "{{ route('superadmin.myticketfillter') }}";
	const ticketdatefillter = "{{ route('superadmin.ticketdatefillter') }}";
	const allticketdatefillter = "{{ route('superadmin.allticketdatefillter') }}";
	const myticketdatefillter = "{{ route('superadmin.myticketdatefillter') }}";
	const fetchUrlticketfitterremove = "{{ route('superadmin.fetchticketfitterremove') }}";
	const fetchAllticketfitterremove = "{{ route('superadmin.fetchallfitterremove') }}";
	const fetchMyticketfitterremove = "{{ route('superadmin.fetchmyticketfitterremove') }}";
	const fetchUrlmorefitter = "{{ route('superadmin.fetchticketfitter') }}";
	const fetchMymorefitter = "{{ route('superadmin.fetchmyticketfitter') }}";
	const fetchAllmorefitter = "{{ route('superadmin.fetchallticketfitter') }}";

    function disablePastDates() {
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();

      today = yyyy + '-' + mm + '-' + dd;
      document.getElementById("targetDate").setAttribute("min", today);
    }

Dropzone.options.myDropzone = {
    acceptedFiles: "image/*", // Only accept image files (any image type)
    addRemoveLinks: true, // Optionally, show remove links for the file
    dictDefaultMessage: "Drag an image here or click to select one image"
  };

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
        function all(start, end) {
   
            $("#alldateviewsall").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            $("#alldateallviews").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

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
        $('#allreportrange').daterangepicker({
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
        }, all);
		
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
        all(start, end);
        my(start, end);

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
	// Handle input focus: Add 'active' class to the closest dropdown
$(document).on("focus", ".searchLocation", function () {
    $(this).closest(".loct-dropdown").addClass("active");
});

// Search and filter dropdown options as you type
$(document).on("input", ".searchLocation", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".loct-dropdown-options").find("div").each(function () {
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


// Handle input focus: Add 'active' class to the closest dropdown
$(document).on("focus", ".mysearchLocation", function () {
    $(this).closest(".myloct-dropdown").addClass("active");
});

// Search and filter dropdown options as you type
$(document).on("input", ".mysearchLocation", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".myloct-dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText) || searchText === "");
    });
});

// Handle option click: Select a single option and close the dropdown
$(document).on("click", ".myloct-dropdown-options div", function () {
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".myloct-dropdown").find(".mysearchLocation");

    // Set the input field value to the selected option
    inputField.val(selectedValue);

    // Highlight the selected option
    $(this).addClass("selected").siblings().removeClass("selected");

    // Close the dropdown after selecting an option (optional)
    $(this).closest(".myloct-dropdown").removeClass("active");
	
});

// Update dropdown UI on input focus (clear previous selection highlights)
$(document).on("focus", ".mysearchLocation", function () {
    const inputField = $(this);
    const selectedValue = inputField.val().trim();
    // Highlight the selected option and remove the highlight from others
    inputField.siblings(".myloct-dropdown-options").find("div").each(function () {
        if ($(this).text() === selectedValue) {
            $(this).addClass("selected");
        } else {
            $(this).removeClass("selected");
        }
    });
});

// Handle input focus: Add 'active' class to the closest dropdown
$(document).on("focus", ".allsearchLocation", function () {
    $(this).closest(".allloct-dropdown").addClass("active");
});

// Search and filter dropdown options as you type
$(document).on("input", ".allsearchLocation", function () {
    const searchText = $(this).val().toLowerCase();
    $(this).siblings(".allloct-dropdown-options").find("div").each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchText) || searchText === "");
    });
});

// Handle option click: Select a single option and close the dropdown
$(document).on("click", ".allloct-dropdown-options div", function () {
    const selectedValue = $(this).text();
    const inputField = $(this).closest(".allloct-dropdown").find(".allsearchLocation");

    // Set the input field value to the selected option
    inputField.val(selectedValue);

    // Highlight the selected option
    $(this).addClass("selected").siblings().removeClass("selected");

    // Close the dropdown after selecting an option (optional)
    $(this).closest(".allloct-dropdown").removeClass("active");
	
});

// Update dropdown UI on input focus (clear previous selection highlights)
$(document).on("focus", ".allsearchLocation", function () {
    const inputField = $(this);
    const selectedValue = inputField.val().trim();
    // Highlight the selected option and remove the highlight from others
    inputField.siblings(".allloct-dropdown-options").find("div").each(function () {
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
                url: "/superadmin/getSubcategory",
                data: {
                    category: category
                },
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
                success: function(res) {
                    if (res.status == '200') {
                        let all_options = "<option value=''>Select Sub Category</option>";
                        let all_subcategories = res.subcategories;
                        $.each(all_subcategories, function(index, value) {
                            all_options += "<option value='" + value.id +
                                "'>" + value.sub_category_name + "</option>";
                        });
                        $("#sub_department_id").html(all_options);
                    }
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
               function updateSelectedValues() {
				// Get all selected checkboxes
				const selectedCheckboxes = document.querySelectorAll('.statusCheckbox:checked');
				const selectedValues = [];

				// Loop through all checked checkboxes and collect their values
				selectedCheckboxes.forEach(function(checkbox) {
					selectedValues.push(checkbox.parentElement.textContent.trim()); // Get the status name
				});
				
				// Join the selected status names and display them in the input field
				document.getElementById('selectedStatus').value = selectedValues.join(', ');// Get all selected checkboxes
				
				const priorityCheckboxes = document.querySelectorAll('.priorityCheckbox:checked');
				const priorityValues = [];

				// Loop through all checked checkboxes and collect their values
				priorityCheckboxes.forEach(function(checkbox) {
					priorityValues.push(checkbox.parentElement.textContent.trim()); // Get the status name
				});
				
				// Join the selected status names and display them in the input field
				document.getElementById('priorityStatus').value = priorityValues.join(', ');
			}
               function mySelectedValues() {
				// Get all selected checkboxes
				const selectedCheckboxes = document.querySelectorAll('.mystatusCheckbox:checked');
				const selectedValues = [];

				// Loop through all checked checkboxes and collect their values
				selectedCheckboxes.forEach(function(checkbox) {
					selectedValues.push(checkbox.parentElement.textContent.trim()); // Get the status name
				});
				
				// Join the selected status names and display them in the input field
				document.getElementById('myselectedStatus').value = selectedValues.join(', ');// Get all selected checkboxes
				
				const priorityCheckboxes = document.querySelectorAll('.mypriorityCheckbox:checked');
				const priorityValues = [];

				// Loop through all checked checkboxes and collect their values
				priorityCheckboxes.forEach(function(checkbox) {
					priorityValues.push(checkbox.parentElement.textContent.trim()); // Get the status name
				});
				
				// Join the selected status names and display them in the input field
				document.getElementById('mypriorityStatus').value = priorityValues.join(', ');
			}
			function allSelectedValues() {
				// Get all selected checkboxes
				const selectedCheckboxes = document.querySelectorAll('.allstatusCheckbox:checked');
				const selectedValues = [];

				// Loop through all checked checkboxes and collect their values
				selectedCheckboxes.forEach(function(checkbox) {
					selectedValues.push(checkbox.parentElement.textContent.trim()); // Get the status name
				});
				
				// Join the selected status names and display them in the input field
				document.getElementById('allselectedStatus').value = selectedValues.join(', ');// Get all selected checkboxes
				
				const priorityCheckboxes = document.querySelectorAll('.allpriorityCheckbox:checked');
				const priorityValues = [];

				// Loop through all checked checkboxes and collect their values
				priorityCheckboxes.forEach(function(checkbox) {
					priorityValues.push(checkbox.parentElement.textContent.trim()); // Get the status name
				});
				
				// Join the selected status names and display them in the input field
				document.getElementById('allpriorityStatus').value = priorityValues.join(', ');
			}
        </script>


    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
