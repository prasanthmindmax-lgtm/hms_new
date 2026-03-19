<!doctype html>
<html lang="en">
<!-- [Head] start -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<!-- [Head] end -->
<!-- [Body] Start -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        padding: 15px;
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
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .multiselect-input_views {
        width: 85%;
        padding: 5px 10px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        cursor: pointer;
    }

    .multiselect-options_views {

        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 85%;
        border: 1px solid #ccc;
        background: #fff;
        z-index: 10;
        max-height: 350px;
        overflow-y: auto;
        border-radius: 5px;
        z-index: 9999;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);

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

    .multiselect-container:focus-within .multiselect-options_views {
        display: block;
    }

    .multiselect-options label {
        display: block;
        padding: 8px 10px;
        cursor: pointer;
    }

    .multiselect-options_views label {
        display: block;
        padding: 8px 10px;
        cursor: pointer;
    }

    .multiselect-options label:hover {
        background: #f0f0f0;
    }
    .multiselect-options_views label:hover {
        background: #f0f0f0;
    }

    .multiselect-options input {
        margin-right: 10px;
    }

    .multiselect-options_views input {
        margin-right: 10px;
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
    .btn-primary.d-inline-flex:hover {
    background-color:rgb(255, 255, 255) !important; /* Change to your desired hover color */
    border-color: #4b4fc5 !important;
    color: #6a6ee4;
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

 #dashboard_color {
      color: #ec008c;
    }

/* vasanth */
.detail-card {
    background: #f4f8fc;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 15px;
    border-left: 4px solid #1976d2;
    font-family: 'Segoe UI', sans-serif;
}

.detail-line {
    margin-bottom: 8px;
    font-size: 13px; /* smaller size */
    color: #333;
}

.detail-line .label {
    font-weight: 600;
    color: #000;
    display: inline-block;
    min-width: 140px;
    font-size: 13px; /* match smaller size */
}

.meeting-details-container h6,
.patient-details-container h6 {
    font-size: 14px; /* smaller heading */
    font-weight: 600;
    color: #1976d2;
    margin-bottom: 10px;
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
" placeholder="Search">
                        </div>
                        <div class="col-md-3 col-sm-3 add-doctors">
                            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
                                height: 34px;
                                width: 133px;
                                font-size: 12px;
                                        background-color: #6a6ee4;
                                        --bs-btn-border-color: #6a6ee4;
                            "><i class="ti ti-plus f-18"></i>Add Doctor</a></div>
                        
                    </div>
                </div>
            </div><br><br>
            <!-- [ breadcrumb ] end -->
           
            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <div class="card-body border-bottom pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                        </div>
                        <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link active" id="analytics-tab-1" data-bs-toggle="tab" data-bs-target="#analytics-tab-1-pane" type="button"
                                    role="tab"
                                    aria-controls="analytics-tab-1-pane"
                                    aria-selected="true">Doctors</button>
                            </li>
                            
                        </ul>
                    </div>
                </div>
            </div><br>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel" aria-labelledby="analytics-tab-1" tabindex="0">
                    <div class="row">
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span id="data_values"></span> <i class="fa fa-caret-down"></i>
                                </div>
                                <span style="display:none;" id="dateviewsall"></span>
                            </div>
                        </div>



                     <!-- @php $zone = App\Models\TblZonesModel::select('tblzones.name','tblzones.id')->get(); @endphp
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search" name="zone_name" id="zoneviews" placeholder="Select Zone">
                                     <input type="hidden" id="camp_zone_id">
                                    <div class="dropdown-options options_marketers selectzone_camp " >
                                         @if($zone)
                                        @foreach($zone as $zonename)
                                        <div data-value="{{$zonename->id}}">{{$zonename->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                        @php $locat = App\Models\TblLocationModel::select('tbl_locations.name','tbl_locations.zone_id')->get(); @endphp
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search" name="branch_name" id="branchviews" placeholder="Select Branch">
                                    <div class="dropdown-options options_marketers" id="getlocation_camp">
                                         @if($locat)
                                        @foreach($locat as $location)
                                        <div data-value="{{$location->name}}" data-type="{{ $location->zone_id }}">{{$location->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                          

                        @php $locatmarket = App\Models\usermanagementdetails::select('user_fullname')->get(); 

                        
                        @endphp

                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                            <div class="dropdown">
                            <input type="text" class="searchInput single_search marketervalues_search" name='userfullname' id="marketer_fetch" placeholder="Select Marketer">
                            <div class="dropdown-options single_search options_marketers">
                                  @if($locatmarket)
                                        @foreach($locatmarket as $marketer)
                                        <div data-value="{{$marketer->user_fullname}}" data-type="{{$marketer->user_fullname}}">{{$marketer->user_fullname}}</div>
                                        @endforeach
                                        @endif
                            </div>
                            </div>
                            </div>
                        </div> -->

@php
    $user = auth()->user();
    $showFilters = true; // Default to show filters
    
    // Check access limits
    if ($user->access_limits == 2) { // Admin - show only their zone
        $zone = App\Models\TblZonesModel::where('id', $user->zone_id)->select('name','id')->get();
        $locat = App\Models\TblLocationModel::where('zone_id', $user->zone_id)->select('name','zone_id')->get();
    } elseif ($user->access_limits > 2) { // Other users - hide filters
        $showFilters = false;
        $zone = collect();
        $locat = collect();
    } else { // Superadmin (1) - show all
        $zone = App\Models\TblZonesModel::select('name','id')->get();
        $locat = App\Models\TblLocationModel::select('name','zone_id')->get();
    }
@endphp

@if($showFilters)
    <!-- Zone Filter -->
    <div class="col-xl-2 col-md-2">
        <div class="card">
            <div class="dropdown">
                <input type="text" class="searchInput marketervalues_search" name="zone_name" id="zoneviews" placeholder="Select Zone">
                <input type="hidden" id="camp_zone_id">
                <div class="dropdown-options options_marketers selectzone_camp">
                    @if($zone->isNotEmpty())
                        @foreach($zone as $zonename)
                            <div data-value="{{$zonename->id}}">{{$zonename->name}}</div>
                        @endforeach
                    @else
                        <div>No zones available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Branch Filter -->
    <div class="col-xl-2 col-md-2">
        <div class="card">
            <div class="dropdown">
                <input type="text" class="searchInput marketervalues_search" name="branch_name" id="branchviews" placeholder="Select Branch">
                <div class="dropdown-options options_marketers" id="getlocation_camp">
                    @if($locat->isNotEmpty())
                        @foreach($locat as $location)
                            <div data-value="{{$location->name}}" data-type="{{ $location->zone_id }}">{{$location->name}}</div>
                        @endforeach
                    @else
                        <div>No branches available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Marketer Filter -->
<div class="col-xl-2 col-md-2">
    <div class="card">
        <div class="dropdown">
            <input type="text" class="searchInput single_search marketervalues_search" name='userfullname' id="marketer_fetch" placeholder="Select Marketer">
            <div class="dropdown-options single_search options_marketers" id="marketerOptions">
                <!-- Marketers will be loaded dynamically via AJAX based on zone selection -->
                <div>Select a zone first</div>
            </div>
        </div>
    </div>
</div>


                        <div class="col-xl-2 col-md-2">
                            <!-- Multiple Search Dropdown -->
                            <div class="card">
                            <div class="dropdown">
                            <input type="text" class="searchInput marketervalues_search" name='special' id="special" placeholder="B2B Type">
                            <div class="dropdown-options options_marketers">
                                <div value="ALLOPATHY DR">ALLOPATHY DR</div>
                                <div value="VHN">VHN</div>
                                <div value="ALLOPATHY HOSPITAL">ALLOPATHY HOSPITAL</div>
                                <div value="ALLOPATHY CLINIC">ALLOPATHY CLINIC</div>
                                <div value="AYUSH CLINIC">AYUSH CLINIC</div>
                                <div value="AYUSH DR">AYUSH DR</div>
                                <div value="AGENT">AGENT</div>

                            </div>
                            </div>
                            </div>
                        </div>




                        <div class="col-xl-2 col-md-2">
                            <div class="">
                                <!-- <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filter" style="height: 34px;width: 133px;font-size: 12px;        background-color: #6a6ee4;--bs-btn-border-color: #6a6ee4;"><i class="ti ti-filter f-18"></i>&nbsp; More Filters</a> -->
                            </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>

                    </div>
                    <p style="margin-top: -9px;" class="text-muted f-12 mb-0">
                        <span class="text-truncate w-100"><span id="counts">0</span> Rows for <span id="dateallviews">Last 30 days</span></span>
                        <span class="search_view" style="color:rgb(16 35 255);font-size: 12px;font-weight: unset;cursor: pointer;">Search</span>
                        <span style="cursor: pointer;" class="badge bg-success value_views_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_views"></span>
                        <span style="cursor: pointer;display:none;" class="badge bg-danger clear_all_views">Clear all</span>
                        <span style="cursor: pointer;" class="badge bg-success value_edit" style="display:none;"></span>
                    </p><br>
                    <div class="col-sm-12">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="tbl">
                                    <thead class="thd">
                                        <tr class="trview">
                                            <th class="thview">Doctor ID</th>
                                            <th class="thview">Doctor Details</th>
                                            <th class="thview">Clinic/Hospital</th>
                                            <th class="thview">Location</th>
                                            <th class="thview">Contacts</th>
                                            <th class="thview">Branch</th>
                                            <th class="thview">Marketer</th>
                                            <th class="thview">View</th>
                                            <!-- <th class="thview">Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody id="doctor_details1">
                                    <tbody id="doctor_details">
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
                                <div class="pagination" id="pagination"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel" aria-labelledby="analytics-tab-2" tabindex="0">
                    <div class="col-xl-12 col-md-12">
                        <div class="row">
                            <div class="col-xl-2 col-md-2">
                                <div class="card">
                                    <div id="reportrange1" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down "></i>
                                    </div>
                                    <span style="display:none;"></span>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search_meeting" name="zone_name" id="meeting_zonss" placeholder="Select Zone">
                                    <div class="dropdown-options options_meeting zoneviewsall">
                                        <div data-value="11649">B.Henry Remgious</div>
                                        <div data-value="11461">S.Selvamurgan</div>
                                        <div data-value="11811">11811</div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search_meeting" name="Branch_name" id="meeting_brans"  placeholder="Select Branch">
                                    <div class="dropdown-options options_meeting brachviewsall">
                                        <div data-value="chennai">chennai</div>
                                        <div data-value="Madurai">Madurai</div>
                                        <div data-value="11811">11811</div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search_meeting" name="userfullname" id="meeting_mark" placeholder="Select Marketer">
                                    <div class="dropdown-options options_meeting marketernameall">
                                        <div data-value="B.Henry Remgious">B.Henry Remgious</div>
                                        <div data-value="S.Selvamurgan">S.Selvamurgan</div>


                                    </div>
                                </div>
                            </div>
                        </div>



                            <div class="col-xl-2 col-md-2">
                                <div class="">
                                    <!-- <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filternew" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
         background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
"><i class="ti ti-filter f-18"></i>&nbsp; More filters</a> -->
                                </div>&nbsp;&nbsp;&nbsp;&nbsp;
                            </div>

                        </div>

                    </div>
                    <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span>0</span> Rows for <span id="meetingdatefitter">Last 30 days</span></span>
                        <span style="color: #080fd399;font-size: 12px;font-weight: 600;" class="search_meeting">Search</span>
                        <span style="cursor: pointer;" class="badge bg-success value_meeting_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_meeting_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_meeting_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success meeting_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success meeting_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success meeting_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success meeting_views"></span>
                        <span style="cursor: pointer;" class="badge bg-success meeting_views"></span>
                        <span style="cursor: pointer; display:none;" class="badge bg-danger clear_all_meeting">Clear all</span>
                        <span style="cursor: pointer; display:none;" class="badge bg-success "></span>
                    </p><br>
                    <div class="col-sm-12">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="tbl">
                                    <thead class="thd">
                                        <tr class="trview">
                                            <th class="thview">Meeting ID & Time</th>
                                            <th class="thview">Doctor Name</th>
                                            <th class="thview">Clinic/Hospital</th>
                                            <th class="thview">Contact</th>
                                            <th class="thview">Marketer Name</th>
                                            <th class="thview">feedbacks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="meetingdetails">
                                        <tr>
                                            <td data-column-index="12">
                                                <img src="../assets/images/loader1.gif" style="
    width: 50%;
    margin-left: 200%;
" alt="Icon" class="icon">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="footer">
                                <div>
                                    Items per page:
                                    <select id="itemsPerPageSelect1">
                                        <option>3</option>
                                        <option>5</option>
                                        <option>10</option>
                                        <option>15</option>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                                <div class="pagination" id="pagination1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="analytics-tab-3-pane" role="tabpanel" aria-labelledby="analytics-tab-3" tabindex="0">
                <div class="col-xl-12 col-md-12">
                    <div class="row">
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div id="reportrange2" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
                                </div>
                                <span style="display:none;"></span>
                            </div>
                        </div>

                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search_patient" name="" id="patient_zone" placeholder="Select Zone">
                                    <div class="dropdown-options options_patient zoneviewsall">
                                        <div data-value="11649">B.Henry Remgious</div>
                                        <div data-value="11461">S.Selvamurgan</div>
                                        <div data-value="11811">11811</div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search_patient" name="" id="patient_branch"  placeholder="Select Branch">
                                    <div class="dropdown-options options_patient brachviewsall">
                                        <div data-value="chennai">chennai</div>
                                        <div data-value="Madurai">Madurai</div>
                                        <div data-value="11811">11811</div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search_patient" name="" id="patient_martketer" placeholder="Select Marketer">
                                    <div class="dropdown-options options_patient marketernameall">
                                        <div data-value="B.Henry Remgious">B.Henry Remgious</div>
                                        <div data-value="S.Selvamurgan">S.Selvamurgan</div>


                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <div class="">
                                <!-- <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filterpatient" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
         background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
"><i class="ti ti-filter f-18"></i>&nbsp; More filters</a> -->
                            </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>

                    </div>
                    <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="counts">0</span> Rows for <span id="patientviews">Last 30 days</span></span>
                        <span class="search_view_patient" style="color: #080fd399;font-size: 12px;font-weight: 600;">Search</span>
                        <span style="cursor: pointer;" class="badge bg-success value_patient_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_patient_mainsearch"></span>
                        <span style="cursor: pointer;" class="badge bg-success value_patient_mainsearch"></span>
                        <span class="badge bg-success value_patient"></span>
                        <span class="badge bg-success value_patient"></span>
                        <span class="badge bg-success value_patient"></span>
                        <span class="badge bg-success value_patient"></span>
                        <span class="badge bg-success value_patient"></span>
                        <span class="badge bg-danger clear_all_views_patient" style="display:none;">Clear all</span>
                        <span class="badge bg-success value_edit" style="display:none;"></span>
                    </p><br>
                    <div class="col-sm-12">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="tbl">
                                    <thead class="thd">
                                        <tr class="trview">
                                            <th class="thview">Referal ID</th>
                                            <th class="thview">Wife Name</th>
                                            <th class="thview">Date</th>
                                            <th class="thview">Husband Name</th>
                                            <th class="thview">Marketer Name</th>
                                            <th class="thview">Doctor Name</th>
                                            <th class="thview">Hospital Name</th>
                                            <th class="thview">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody id="patient_details">
                                        <tr>
                                            <td data-column-index="7">
                                                <img src="../assets/images/loader1.gif" style="width: 50%; margin-left: 200%;" alt="Icon" class="icon">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="footer">
                                <div>
                                    Items per page:
                                    <select id="itemsPerPageSelect2">
                                        <option>3</option>
                                        <option>5</option>
                                        <option>10</option>
                                        <option>15</option>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                                <div class="pagination" id="pagination2"></div>
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
                                <h5>More Filters</h5>
                                <a
                                    href="#"
                                    class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas"
                                    data-bs-target="#offcanvas_mail_filter">
                                    <i class="ti ti-x f-20"></i>
                                </a>



                            </div><br>

                            <label class="form-label required" style="font-size: 12px;font-weight: 600;color:red;margin-left: 28px;" id="error_throws"></label>

                            <!-- Scrollable Block -->
                            <div class="scroll-block position-relative">
                                <div class="card-body">
                                    <div class="row">


                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                               <br> <label class="form-label required" style="font-size: 12px;font-weight: 600;">Specialization</label>
                                                <div class="multiselect-container" tabindex="0">
                                                    <input type="text" id="special_more" name="special" class="multiselect-input morefittersclr" placeholder="Select Specialization" readonly>
                                                    <div class="multiselect-options doctor_option">
                                                        <label>
                                                            <input type="checkbox" value="ALLOPATHY DR" onchange="updateSelectedValues()">ALLOPATHY DR
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="VHN" onchange="updateSelectedValues()">VHN
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="ALLOPATHY HOSPITAL" onchange="updateSelectedValues()">ALLOPATHY HOSPITAL
                                                        </label>

                                                        <label>
                                                            <input type="checkbox" value="ALLOPATHY CLINIC" onchange="updateSelectedValues()">MBBS
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Homeopathy" onchange="updateSelectedValues()">Homeopathy
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="NGO" onchange="updateSelectedValues()">NGO
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Others" onchange="updateSelectedValues()">Others
                                                        </label>
                                                    </div>
                                                </div><br>
                                            </div>
                                        </div>


                                    </div>
                                </div><br><br><br><br><br><br>
                                <!-- Fixed Clear All Button -->
                                <div class="card-footer sticky-bottom bg-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2 mainclearall">Clear All</a>
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;"  id="morefitter_search"  class="btn btn-outline-primary w-50">Submit</a>
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px; display:none;"  id="dismissmodelssss" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filternew">
            <div class="offcanvas-body p-0">
                <div id="ecom-filter" class="collapse collapse-horizontal show">
                    <div class="ecom-filter">
                        <div class="card">
                            <!-- Sticky Header -->
                            <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>More Filters</h5>
                                <a
                                    href="#"
                                    class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas"
                                    data-bs-target="#offcanvas_mail_filternew">
                                    <i class="ti ti-x f-20"></i>
                                </a>
                            </div>
                            <!-- Scrollable Block -->
                            <div class="scroll-block position-relative">
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <label class="form-label required" style="font-size: 12px;font-weight: 600;">Specialization:</label>
                                                <div class="multiselect-container" tabindex="0">
                                                    <input type="text" id="special_more_meeting" name="special" class="multiselect-input morefittersclr_meeting" placeholder="Select Specialization" readonly>
                                                    <div class="multiselect-options meeting-option">
                                                        <label>
                                                            <input type="checkbox" value="Ayurvedic" onchange="updateSelectedValues()"> Ayurvedic
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Gynecologist" onchange="updateSelectedValues()">Gynecologist
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Andrologist" onchange="updateSelectedValues()">Andrologist
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="ALLOPATHY CLINIC" onchange="updateSelectedValues()"> MBBS Doctors
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="AYUSH CLINIC" onchange="updateSelectedValues()">AYUSH CLINIC
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="AYUSH DR" onchange="updateSelectedValues()">AYUSH DR
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="AGENT" onchange="updateSelectedValues()">AGENT
                                                        </label>
                                                    </div>
                                                </div><br>
                                            </div>
                                        </div>

                                    </div>
                                </div><br><br><br><br><br><br>
                                <!-- Fixed Clear All Button -->
                                <div class="card-footer sticky-bottom bg-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2 mainclearall_meeting">Clear All</a>
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="meetingfitter_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filterpatient">
            <div class="offcanvas-body p-0">
                <div id="ecom-filter" class="collapse collapse-horizontal show">
                    <div class="ecom-filter">
                        <div class="card">
                            <!-- Sticky Header -->
                            <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>More Filters</h5>
                                <a
                                    href="#"
                                    class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas"
                                    data-bs-target="#offcanvas_mail_filterpatient">
                                    <i class="ti ti-x f-20"></i>
                                </a>
                            </div>
                            <!-- Scrollable Block -->
                            <div class="scroll-block position-relative">
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <label class="form-label required" style="font-size: 12px;font-weight: 600;">Specialization</label>
                                                <div class="multiselect-container" tabindex="0">
                                                    <input type="text" id="special_more_patient" name="special" class="multiselect-input morefittersclr_patient" placeholder="Select Specialization" readonly>
                                                    <div class="multiselect-options patient-option">
                                                        <label>
                                                            <input type="checkbox" value="Ayurvedic" onchange="updateSelectedValues()"> Ayurvedic
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Gynecologist" onchange="updateSelectedValues()">Gynecologist
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Andrologist" onchange="updateSelectedValues()">Andrologist
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="MBBS Doctors" onchange="updateSelectedValues()"> MBBS Doctors
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Homeopathy" onchange="updateSelectedValues()">Homeopathy
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="NGO" onchange="updateSelectedValues()">NGO
                                                        </label>
                                                        <label>
                                                            <input type="checkbox" value="Others" onchange="updateSelectedValues()">Others
                                                        </label>
                                                    </div>
                                                </div><br>
                                            </div>
                                        </div>


                                    </div>
                                </div><br><br><br><br><br><br>
                                <!-- Fixed Clear All Button -->
                                <div class="card-footer sticky-bottom bg-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2 mainclearall_patient">Clear All</a>
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="patientfitter_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filternotes">
            <div class="offcanvas-body p-0">
                <div id="ecom-filter" class="collapse collapse-horizontal show">
                    <div class="ecom-filter">
                        <div class="card">
                            <!-- Sticky Header -->
                            <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>Notes #<span id="notesid"></span></h5>
                                <a
                                    href="#"
                                    class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas"
                                    data-bs-target="#offcanvas_mail_filternotes">
                                    <i class="ti ti-x f-20"></i>
                                </a>
                            </div>
                            <!-- Scrollable Block -->
                            <div class="scroll-block position-relative">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                                                    <h4 class="alert-heading" style="font-size: 12px;">Doctor Name</h4>
                                                    <p style="font-size: 12px;" id="doctor_names">Name : Dr.Aravindivf</p>
                                                    <h4 class="alert-heading" style="font-size: 12px;">Employee Details</h4>
                                                    <p style="font-size: 12px;" id="empname_views_all">Emp Name : R. Anusuya</p>
                                                    <h4 class="alert-heading" style="font-size: 12px;">Patient Details</h4>
                                                    <p style="font-size: 12px;" id="wifenames">Wife Name : nnn</p>
                                                    <p style="font-size: 12px; margin-top: -11px;" id="husbandnames">Husband Name : cccc</p>
                                                    <h4 class="alert-heading" style="font-size: 12px;">Feedback</h4>
                                                    <p style="font-size: 12px;" id="notesfeedback">asdsadsa sdasdasd sdasdsd asdsadsa sdasdasd sdasdsd asdsadsa
                                                        sdasdasd sdasdsd asdsadsa sdasdasd sdasdsd asdsadsa sdasdasd sdasdsd</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filterfeedback">
            <div class="offcanvas-body p-0">
                <div id="ecom-filter" class="collapse collapse-horizontal show">
                    <div class="ecom-filter">
                        <div class="card">
                            <!-- Sticky Header -->
                            <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>Feedback #<span id="feedbackid"></span></h5>
                                <a
                                    href="#"
                                    class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas"
                                    data-bs-target="#offcanvas_mail_filterfeedback">
                                    <i class="ti ti-x f-20"></i>
                                </a>
                            </div>
                            <!-- Scrollable Block -->
                            <div class="scroll-block position-relative">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                                                    <h4 class="alert-heading" style="font-size: 12px;">Doctor Name</h4>
                                                    <p style="font-size: 12px;" id="doctor_names_feed">Name : Dr.Aravindivf</p>
                                                    <h4 class="alert-heading" style="font-size: 12px;">Marketer Details</h4>
                                                    <p style="font-size: 12px;" id="empname_views_all_feed">Emp Name : R. Anusuya</p>
                                                    <h4 class="alert-heading" style="font-size: 12px;">Feedback</h4>
                                                    <p style="font-size: 12px;" id="feedback_meetss"></p>
                                                </div>
                                            </div>
                                        </div>
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
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Add NEFT</h5>
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
                        <input type="hidden" class="userid" name="userid" id="userid" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
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
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">SI. No</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="serial_error errorss"></span>
										
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" value="<?php echo $auto_number;?>" readonly required name="serial_number"  id="serial_number" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Created By :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_created_by errorss"></span>
                                            <div class="dropdown">
                                                <input type="text" class="searchInput single_search" name="created_by" id="created_by" placeholder="Select Specialization" required>
                                                <div class="dropdown-options options_marketers ">
                                                    <div data-value="Zonal Head">Zonal Head</div>
                                                    <div data-value="Zonal Officer">Zonal Officer</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required" style="font-size: 12px;font-weight: 600;">Vendor/ Employee Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_vendor errorss"></span>
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
                                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">NEFT Amount</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_neft_amount errorss"></span>
                                                
                                                        <input type="number" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="neft_amount" id="neft_amount" autocomplete="off">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Description</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_description errorss"></span>
                                                <textarea name="description" id="description" row="15" style="height: 100px;width: 100%;"></textarea>
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">PAN Number</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class=" error_pan_number errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="pan_number" id="pan_number"  autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">PAN Upload</label>
                                        <div class="fallback"> <span style="font-size:10px; color:red;"
                                                class="error_pan_upload errorss"></span>
                                            <input name="pan_upload[]" id="pan_upload" type="file"
                                                accept="image/*,application/pdf" multiple
                                                style="height: 28px;border: ridge;width: 100%;background-color: #ffffff; border-color: #ffffff;" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Account Number</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_account_number errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="account_number" id="account_number" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">IFSC Code</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_ifsc_code errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="ifsc_code" id="ifsc_code" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Invoice Number</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_invoice_number errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="invoice_number" id="invoice_number" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Invoice Amount</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_invoice_amount errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="invoice_amount" id="invoice_amount" autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Invoice Upload</label>
                                        <div class="fallback"> <span style="font-size:10px; color:red;"
                                                class="error_invoice_upload errorss"></span>
                                            <input name="invoice_upload[]" id="invoice_upload" type="file"
                                                accept="image/*,application/pdf" multiple
                                                style="height: 28px;border: ridge;width: 100%;background-color: #ffffff; border-color: #ffffff;" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Upload Bank Document</label>
                                        <div class="fallback"> <span style="font-size:10px; color:red;"
                                                class="error_bank_upload errorss"></span>
                                            <input name="bank_upload[]" id="bank_upload" type="file"
                                                accept="image/*,application/pdf" multiple
                                                style="height: 28px;border: ridge;width: 100%;background-color: #ffffff; border-color: #ffffff;" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Aressio Paid</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_aressio_paid errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="aressio_paid" id="aressio_paid"  autocomplete="off">
											
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Already Paid</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_already_paid errorss"></span>
												<input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px" required name="already_paid" id="already_paid" autocomplete="off">
											
                                        </div>
                                    </div>
                                    
                                    
                                </div>
								
								<div class="row">
                                    
                                <div class="modal-footer">
                                    <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="submit-sample-datas" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
                                </div> 
                  
                               
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

  
        
    </div>
    </div>
    <!-- end meeting  -->
    <!-- Add patient -->
   
    </div>
    <!-- end patient  -->
    <div class="offcanvas pc-announcement-offcanvas offcanvas-start ecom-offcanvas" tabindex="-1" id="announcement">
        <div class="offcanvas-body p-0">
            <div id="ecom-filter" class="collapse collapse-horizontal show">
                <div class="ecom-filter">
                    <div class="card">
                        <!-- Sticky Header -->
                        <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                            <h5>Doctor details Edit : #<span id="uesrids"></span></h5>
                            <a
                                href="#"
                                class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                data-bs-dismiss="offcanvas"
                                data-bs-target="#announcement">
                                <i class="ti ti-x f-20"></i>
                            </a>
                        </div>
                        <!-- Scrollable Block -->
                        <div class="scroll-block position-relative">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Doctor Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_doctor errorss"></span>
                                            <input type="text" class="form-control editsall" id="doctorname_edits" name="doctor_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Doctor Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Marketer Name:</label>
                                            <select class="mb-3 form-select editsall" id="emp_name" name="empolyee_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="empolyee_name">

                                                        <option value="{{ $admin->username }}">{{ $admin->username }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Contact:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplname errorss"></span>
                                            <input type="text" class="form-control editsall" id="contactviews" name="hpl_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Clinic / Hospital Name">
                                        </div><br>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">city:</label>
                                            <select class="mb-3 form-select editsall" id="citys" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="city">
                                                <option value="">Select city</option>
                                                <option value="Chennai">Chennai</option>
                                                <option value="Tiruppur">Tiruppur</option>
                                                <option value="Salem">Salem</option>
                                                <option value="Coimbatore">Coimbatore</option>
                                                <option value="Pollachi">Pollachi</option>
                                                <option value="Bangalore">Bangalore</option>
                                                <option value="Palakad">Palakad</option>
                                                <option value="Kozhikode">Kozhikode</option>
                                                <option value="Tiruppur">Tiruppur</option>
                                                <option value="Erode">Erode</option>
                                                <option value="Trichy">Trichy</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Address:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_hplname errorss"></span>
                                            <input type="text" class="form-control editsall" id="addressviews" name="address" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Clinic / Hospital Name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fixed Clear All Button -->
                            <div class="card-footer sticky-bottom bg-white">
                                <div class="d-flex justify-content-between">
                                    <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2 ">Clear All</a>
                                    <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50 editsoveralls">Submit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
<div class="col-sm-4"><br>
    <ul class="nav nav-tabs analytics-tab">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="doctor_info" style="padding: 0.5rem 0.8rem;" type="button">Profile</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link " id="meeting_info" style="padding: 0.5rem 0.8rem;" type="button">Meeting info</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="patients_info" style="padding: 0.5rem 0.8rem;" type="button">Patients Info</button>
        </li>
    </ul><br>
        <div class="mb-4">
            <div class="card" id="doctor_info_details" style="overflow-y: auto; max-height: 400px;scrollbar-width: thin; /* For Firefox */ ">
                <div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                    <h4 class="alert-heading" style="font-size: 12px;">Doctor Name</h4>
                    <p style="font-size: 12px;" class="doctor_names"></p>
                    <h4 class="alert-heading" style="font-size: 12px;margin-top: 15px;">Address details</h4>
                    <p style="font-size: 12px; margin-top: 0px;" id="docaddress"></p>
                    <h4 class="alert-heading" style="font-size: 12px;">Contact Details</h4>
                    <p style="font-size: 12px;" id="dcnum"></p>
                    <p style="font-size: 12px; ;margin-top: -12px;" id="hpnum"></p>
                    <h4 class="alert-heading" style="font-size: 12px;">Marketer Details</h4>
                    <p style="font-size: 12px;" class="empname_views_all"></p>
                    <h4 class="alert-heading" style="font-size: 12px;">Schedules</h4>
                    <p style="font-size: 12px;" id="visit_date"></p>
                    <h4 class="alert-heading" style="font-size: 12px;">Location</h4>
                    <p style="font-size: 12px;color: #d70ebb;" id="maplocation"></p>
                </div> <br>
            </div>
                                    <div class="card" id="meeting_info_details" style="overflow-y: auto; max-height: 400px;scrollbar-width: thin; /* For Firefox */ ">
                                        <div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                                            <h4 class="alert-heading" style="font-size: 12px;">Name details</h4>
                                            <p style="font-size: 12px; ;" class="empname_views_all">Employee Name : Vignesh</p>
                                            <h4 class="alert-heading" style="font-size: 12px;margin-top: 15px;">Hopsital Details</h4>
                                            <p style="font-size: 12px; margin-top: 0px;" class="hosptalnames">Hospital Name : Viyay 's hospital</p>
                                            <p style="font-size: 12px; ;margin-top: -12px;" class="cityviews">city : Chennai</p>
                                            <h4 class="alert-heading" style="font-size: 12px;">Meeting Details</h4>
                                            <p style="font-size: 12px;" id="totalmeetins">dfsdfdsfdsf</p>
                                            <!-- Table for additional details -->
                                            <table style="font-size: 12px; margin-top: 10px; width: 90%; border-collapse: collapse;" border="1">
                                                <thead>
                                                    <tr style="background-color: #f2f2f2;">
                                                        <th style="padding: 5px;">Time & Date</th>
                                                        <th style="padding: 5px;">Feedback</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="meeting_popdata">
                                                    <tr>
                                                        <td style="padding: 5px;">John Doe</td>
                                                        <td style="padding: 5px;">35</td>
                                                        <td style="padding: 5px;">Male</td>
                                                        <td style="padding: 5px;">2025-01-22</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div> <br>
                                    </div>
                                    <div class="card" id="patients_info_details" style="overflow-y: auto; max-height: 400px; scrollbar-width: thin; /* For Firefox */">
                                        <div class="views-ali" style="margin-left: 20px; margin-top: 20px;">
                                            <h4 class="alert-heading" style="font-size: 12px;">Details</h4>
                                            <p style="font-size: 12px;" class="empname_views_all">Employee Name: Vignesh</p>
                                            <h4 class="alert-heading" style="font-size: 12px; margin-top: 15px;">Hospital Details</h4>
                                            <p style="font-size: 12px; margin-top: 0px;" class="hosptalnames">Hospital Name: Vijay's Hospital</p>
                                            <p style="font-size: 12px; margin-top: -12px;" class="cityviews">City: Chennai</p>
                                            <h4 class="alert-heading" style="font-size: 12px;">Patients Details</h4>
                                            <p style="font-size: 12px;" id="total_patient">Total Patients Count: 0</p>
                                            <!-- Table for additional details -->
                                            <table style="font-size: 12px; margin-top: 10px; width: 90%; border-collapse: collapse;" border="1">
                                                <thead>
                                                    <tr style="background-color: #f2f2f2;">
                                                        <th style="padding: 5px;">Wife Name</th>
                                                        <th style="padding: 5px;">MRD</th>
                                                        <th style="padding: 5px;">Husband Name</th>
                                                        <th style="padding: 5px;">MRD</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="patient_popdata">
                                                    <tr>
                                                        <td style="padding: 5px;">John Doe</td>
                                                        <td style="padding: 5px;">35</td>
                                                        <td style="padding: 5px;">Male</td>
                                                        <td style="padding: 5px;">2025-01-22</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="announcement" aria-labelledby="announcementLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="announcementLabel">What's new announcement?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <p class="text-span">Today</p>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                            <div class="badge bg-light-success f-12">Big News</div>
                            <p class="mb-0 text-muted">2 min ago</p>
                            <span class="badge dot bg-warning"></span>
                        </div>
                        <h5 class="mb-3">Able Pro is Redesigned</h5>
                        <p class="text-muted">Able Pro is completely renowed with high aesthetics User Interface.</p>
                        <img src="../assets/images/layout/img-announcement-1.png" alt="img" class="img-fluid mb-3" />
                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid"><a class="btn btn-outline-secondary" href="https://1.envato.market/zNkqj6" target="_blank">Check Now</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                            <div class="badge bg-light-warning f-12">Offer</div>
                            <p class="mb-0 text-muted">2 hour ago</p>
                            <span class="badge dot bg-warning"></span>
                        </div>
                        <h5 class="mb-3">Able Pro is in best offer price</h5>
                        <p class="text-muted">Download Able Pro exclusive on themeforest with best price. </p>
                        <a href="https://1.envato.market/zNkqj6" target="_blank"><img src="../assets/images/layout/img-announcement-2.png" alt="img" class="img-fluid" /></a>
                    </div>
                </div>
                <p class="text-span mt-4">Yesterday</p>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                            <div class="badge bg-light-primary f-12">Blog</div>
                            <p class="mb-0 text-muted">12 hour ago</p>
                            <span class="badge dot bg-warning"></span>
                        </div>
                        <h5 class="mb-3">Featured Dashboard Template</h5>
                        <p class="text-muted">Do you know Able Pro is one of the featured dashboard template selected by Themeforest team.?</p>
                        <img src="../assets/images/layout/img-announcement-3.png" alt="img" class="img-fluid" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                            <div class="badge bg-light-primary f-12">Announcement</div>
                            <p class="mb-0 text-muted">12 hour ago</p>
                            <span class="badge dot bg-warning"></span>
                        </div>
                        <h5 class="mb-3">Buy Once - Get Free Updated lifetime</h5>
                        <p class="text-muted">Get the lifetime free updates once you purchase the Able Pro.</p>
                        <img src="../assets/images/layout/img-announcement-4.png" alt="img" class="img-fluid" />
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
        <script src="{{ asset('/assets/sample/sample.js') }}"></script>
        <script type="text/javascript">
            Dropzone.options.myDropzone = {
                acceptedFiles: "image/*", // Only accept image files (any image type)
                addRemoveLinks: true, // Optionally, show remove links for the file
                dictDefaultMessage: "Drag an image here or click to select one image"
            };


            
            // Set the initial start and end dates
            var start = moment().subtract(29, 'days');
            var end = moment();
            var start1 = moment().subtract(29, 'days');
            var end1 = moment();
            var start2 = moment().subtract(29, 'days');
            var end2 = moment();
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

            function cb1(start1, end1) {
                $("#meetingdatefitter").text(start1.format('DD/MM/YYYY') + ' - ' + end1.format('DD/MM/YYYY'));
                // Check the selected date range and adjust the display accordingly
                if (start1.isSame(end1, 'day')) {
                    // If the start and end date are the same, show the single date
                    if (start1.isSame(moment(), 'day')) {
                        $('#reportrange1 span').html('Today');
                    } else if (start1.isSame(moment().subtract(1, 'days'), 'day')) {
                        $('#reportrange1 span').html('Yesterday');
                    } else {
                        $('#reportrange1 span').html(start1.format('DD/MM/YYYY'));
                    }
                } else {
                    // For other ranges like "Last 7 Days", "This Month", etc.
                    $('#reportrange1 span').html(start1.format('DD/MM/YYYY') + ' - ' + end1.format('DD/MM/YYYY'));
                }
            }

            function cb2(start2, end2) {
                $("#patientviews").text(start2.format('DD/MM/YYYY') + ' - ' + end2.format('DD/MM/YYYY'));
                // Check the selected date range and adjust the display accordingly
                if (start2.isSame(end2, 'day')) {
                    // If the start and end date are the same, show the single date
                    if (start2.isSame(moment(), 'day')) {
                        $('#reportrange2 span').html('Today');
                    } else if (start2.isSame(moment().subtract(1, 'days'), 'day')) {
                        $('#reportrange2 span').html('Yesterday');
                    } else {
                        $('#reportrange2 span').html(start2.format('DD/MM/YYYY'));
                    }
                } else {
                    // For other ranges like "Last 7 Days", "This Month", etc.
                    $('#reportrange2 span').html(start2.format('DD/MM/YYYY') + ' - ' + end2.format('DD/MM/YYYY'));
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
            $('#reportrange1').daterangepicker({
                startDate: start1,
                endDate: end1,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb1);
            $('#reportrange2').daterangepicker({
                startDate: start2,
                endDate: end2,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb2);
            // Set initial date range text
            cb(start, end);
            cb1(start1, end1);
            cb2(start2, end2);
            $(document).on('click', '.editbtn', function(e) {
                $('#exampleModal').modal('show');
            });
            $(document).on('click', '.addmeeting', function(e) {
                $('#exampleModal2').modal('show');
            });
            $(document).on('click', '.addpatient', function(e) {
                $('#exampleModal3').modal('show');
            });
            $("#dashboard_color").css("color", "rgba(8, 15, 211, 0.6)");


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
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById("meeting-doctorname");
        const dropdown = document.getElementById("doctor-options");
        const options = dropdown.querySelectorAll("div");

        // Show dropdown when input is focused
        input.addEventListener("focus", function() {
            dropdown.style.display = "block";
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener("click", function() {
                input.value = this.getAttribute("value"); // Set input value
                dropdown.style.display = "none"; // Hide dropdown
            });
        });

        // Filter options based on input value
        input.addEventListener("input", function() {
            let filter = input.value.toLowerCase();
            options.forEach(option => {
                let text = option.textContent.toLowerCase();
                option.style.display = text.includes(filter) ? "block" : "none";
            });
            dropdown.style.display = "block"; // Show dropdown while filtering
        });

        // Close dropdown if clicking outside
        document.addEventListener("click", function(event) {
            if (!event.target.closest(".dropdown")) {
                dropdown.style.display = "none";
            }
        });
    });
</script>

<script>

                         $(document).ready(function() {
        $(document).on("input", ".searchInput", function () {
        const searchText = $(this).val().toLowerCase().split(",").pop().trim();
        const currentValues = $(this).val().split(",").map(v => v.trim());
        $(this).siblings(".dropdown-options").find("div").each(function () {
            const optionText = $(this).text().trim().toLowerCase();
            const fullText = $(this).text().trim();

            // Always show, but dim/hint if selected
            const matchesSearch = optionText.includes(searchText);
            const isSelected = currentValues.includes(fullText);
            $(this).toggle(matchesSearch);
            $(this).toggleClass("selected", isSelected);
        });
    });

                        // Ensure only valid values remain in the input field (for multiple search)
                        $(document).on("blur", ".multi_search", function() {
                            const inputField = $(this);
                            const typedValues = inputField.val().split(",").map(v => v.trim());
                            const validOptions = inputField.siblings(".dropdown-options").find("div")
                                .map(function() {
                                    return $(this).text().trim();
                                }).get();

                            // Filter typed values to keep only valid options
                            const filteredValues = typedValues.filter(v => validOptions.includes(v));

                            inputField.data("values", filteredValues);
                            inputField.val(filteredValues.join(", "));
                        });

                        // Close dropdown when clicking outside
                        $(document).on("click", function(event) {
                            if (!$(event.target).closest(".dropdown").length) {
                                $(".dropdown").removeClass("active");
                            }
                        });

                         $(".dropdown input").on("focus", function () {
        // Close all dropdowns first
        $(".dropdown").removeClass("active");
        // Then open the one that's focused
        $(this).closest(".dropdown").addClass("active");
    });
                    });




                   $(document).on("click", ".dropdown-options div", function () {
        const selectedValue = $(this).text().trim();

        const inputField = $(this).closest(".dropdown").find(".searchInput");
        // alert(inputField);
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
    });

    // On input focus
    $(document).on("focus", ".searchInput", function () {
        const inputField = $(this);
        const currentValues = inputField.val().split(",").map(v => v.trim());

        inputField.siblings(".dropdown-options").find("div").each(function () {
            const optionText = $(this).text().trim();
            const isSelected = currentValues.includes(optionText);

            $(this).show();
            $(this).toggleClass("selected", isSelected);
        });

        $(this).closest(".dropdown").addClass("active");
    });
                    // Close dropdown when clicking outside
                    $(document).on("click", function(event) {
                        if (!$(event.target).closest(".dropdown").length) {
                            $(".dropdown").removeClass("active");
                        }
                    });



  $(document).ready(function () {
        $('.selectzone_camp > div').off('click').on('click', function () {
            const selectedType = $(this).data('value');
            const selectedText = $(this).text();
             $('#zoneviews').val(selectedText);
            $('#camp_zone_id').val(selectedType);
            $('#branchviews').val('');
            $('#getlocation_act').hide();

            $('#getlocation_act > div').removeClass('selected');

           $('#getlocation_act > div')
                .hide()
                .filter(function () {
                    return Number($(this).data('type')) === Number(selectedType);
                })
                .show();
        });

       $('#zoneviews').on('input', function () {
            $('#camp_zone_id').val('');
            $('#getlocation_camp > div').show();
            $('#branchviews').val('');
            $('#getlocation_camp > div').removeClass('selected');
        });

        $('#branchviews').on('focus', function () {
            const selectedType = Number($('#camp_zone_id').val()); // use hidden ID

            if (selectedType) {
                $('#getlocation_camp > div')
                    .hide()
                    .filter(function () {
                        return Number($(this).data('type')) === selectedType;
                    })
                    .show();
            } else {
                $('#getlocation_camp > div').show().removeClass('selected');
            }

            $('#getlocation_camp').show();
        });
       $('#getlocation_camp > div').off('click').on('click', function () {
            const name = $(this).data('value');
            $('#branchviews').val(name);

            $('#getlocation_camp > div').removeClass('selected');
            $(this).addClass('selected');
            $('#getlocation_camp').hide();
        });

        $('input.searchInput').attr('autocomplete', 'off');
    });


// Handle plus/minus icon click
$(document).on('click', '.toggle-details', function(e) {
    e.stopPropagation();
    const id = $(this).data('id');
    const $detailRow = $('#detail-' + id);
    const $icon = $(this);

     // Close all other open detail rows first
    $('.detail-row:visible').not($detailRow).slideUp(300);
    $('.toggle-details').not($icon).removeClass('fa-minus-circle').addClass('fa-plus-circle');
    
    if ($detailRow.is(':visible')) {
        $detailRow.slideUp(300);
        $icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
    } else {
        $detailRow.slideDown(300);
        $icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
        
        if (!$detailRow.hasClass('loaded')) {
            loadDetailsForRow(id);
            $detailRow.addClass('loaded');
        }
    }
});

function loadDetailsForRow(id) {
    $('#meeting-details-' + id).html('<div class="loading-text">Loading meeting details...</div>');
    
    
        $.ajax({
            url: meetingviews,
            type: "GET",
            data: { ref_doctor_id: id }, 
            success: function(response) {
                console.log("mdata",response);
                renderMeetingDetailsForRow(id, response);
            },
            error: function(xhr, status, error) {
                $('#meeting-details-' + id).html('<div class="error-text">Failed to load meeting details</div>');
            }
        });
    
    // Load patient details
    $('#patient-details-' + id).html('<div class="loading-text">Loading patient details...</div>');
    
    
        $.ajax({
            url: patientviews,
            type: "GET",
            data: { ref_doctor_id: id }, 
            success: function(response) {
                  console.log("pdata",response);
                renderPatientDetailsForRow(id, response);
            },
            error: function(xhr, status, error) {
                $('#patient-details-' + id).html('<div class="error-text">Failed to load patient details</div>');
            }
        });
}


// Helper function to render meeting details
function renderMeetingDetailsForRow(id, data) {
    const $container = $('#meeting-details-' + id);
    console.log("meeting_data:", data);

    if (!data || data.length === 0) {
        $container.html('<div class="no-data">No meeting data found</div>');
        return;
    }

    // let html = '<div class="meeting-details-container"><h6>Meeting Details</h6>';
       let html = '<div class="meeting-details-container">' +
               '<span class="detail-heading"><i class="fas fa-calendar-alt"></i> MEETING DETAILS</span>';

    data.forEach(meeting => {
        const formattedDate = moment(meeting.created_at).format("DD MMM YYYY | HH:mm");

        html += `
        <div class="detail-card">
            <div class="detail-line"><span class="label">Date :</span> ${formattedDate}</div>
            <div class="detail-line"><span class="label">Doctor :</span> Dr. ${meeting.doctor_name}</div>
            <div class="detail-line"><span class="label">Feedback :</span> ${meeting.meeting_feedback || 'N/A'}</div>
        </div>`;
    });

    html += '</div>';
    $container.html(html);
}



// Helper function to render patient details
function renderPatientDetailsForRow(id, data) {
    const $container = $('#patient-details-' + id);
    console.log("patient_data:", data);

    if (!data || data.length === 0) {
        $container.html('<div class="no-data">No patient data found</div>');
        return;
    }

    // let html = '<div class="patient-details-container"><h6>Patient Details</h6>';

      let html = '<div class="patient-details-container">' +
               '<span class="detail-heading"><i class="fas fa-user-injured"></i> PATIENT DETAILS</span>';

    data.forEach(patient => {
        const formattedDate = moment(patient.created_at).format("DD MMM YYYY | HH:mm");

        html += `
        <div class="detail-card">
            <div class="detail-line"><span class="label">MRN Number :</span> ${patient.mrn_number}</div>
            <div class="detail-line"><span class="label">Wife Name :</span> ${patient.wifename}</div>
            <div class="detail-line"><span class="label">Date :</span> ${formattedDate}</div>
            <div class="detail-line"><span class="label">Husband Name :</span> ${patient.husband_name || 'N/A'}</div>
            <div class="detail-line"><span class="label">Employee Name :</span> ${patient.empolyee_name || 'N/A'}</div>
            <div class="detail-line"><span class="label">Doctor Name :</span> ${patient.doctor_name || 'N/A'}</div>
            <div class="detail-line"><span class="label">Hospital Name :</span> ${patient.hopsital_name || 'N/A'}</div>
            <div class="detail-line"><span class="label">Notes :</span> ${patient.notes || 'N/A'}</div>
        </div>`;
    });

    html += '</div>';
    $container.html(html);
}
$(document).ready(function() {

    console.log('jQuery version:', $.fn.jquery);
console.log('Moment version:', moment.version);
console.log('DateRangePicker available:', $.fn.daterangepicker ? 'YES' : 'NO');
    // When zone is selected, load marketers for that zone
    $('.selectzone_camp div[data-value]').on('click', function() {
        const zoneId = $(this).data('value');
        $('#camp_zone_id').val(zoneId);
        $('#zoneviews').val($(this).text());
        
        // Load marketers via AJAX
        $.ajax({
            url: 'get-marketers-by-zone',
            type: 'GET',
            data: { zone_id: zoneId },
            success: function(response) {
                $('#marketerOptions').empty();
                if(response.length > 0) {
                    $.each(response, function(index, marketer) {
                        $('#marketerOptions').append(
                            `<div data-value="${marketer.user_fullname}" data-type="${marketer.zone_id}">${marketer.user_fullname}</div>`
                        );
                    });
                } else {
                    $('#marketerOptions').append('<div>No marketers in this zone</div>');
                }
            },
            error: function() {
                $('#marketerOptions').empty().append('<div>Error loading marketers</div>');
            }
        });
    });

    // Initialize with all marketers if superadmin and no zone selected
    @if($user->access_limits == 1)
        loadAllMarketers();
    @endif

    function loadAllMarketers() {
        $.ajax({
            url: 'get-all-marketers',
            type: 'GET',
            success: function(response) {
                $('#marketerOptions').empty();
                if(response.length > 0) {
                    $.each(response, function(index, marketer) {
                        $('#marketerOptions').append(
                            `<div data-value="${marketer.user_fullname}" data-type="${marketer.zone_id}">${marketer.user_fullname}</div>`
                        );
                    });
                } else {
                    $('#marketerOptions').append('<div>No marketers available</div>');
                }
            }
        });
    }
});



</script>
 <script src="{{ asset('/assets/js/referral/doctor-added.js') }}"></script>
  <script src="{{ asset('/assets/js/referral/doctor-details.js') }}"></script>
  <script src="{{ asset('/assets/js/referral/doctor-meeting.js') }}"></script>
  <script src="{{ asset('/assets/js/referral/doctor-patient.js') }}"></script>
  <script src="{{ asset('/assets/js/referral/referral_info.js') }}"></script>




        <!-- [ Main Content ] end -->
        <!-- @include('superadmin.superadminfooter') -->
</body>
<!-- [Body] end -->

</html>