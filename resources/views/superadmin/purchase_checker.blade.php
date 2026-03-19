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
.document-container {
    padding: 15px;
    display:flex;
    flex-wrap:wrap;
    width:100%;
}

.document-section {
    margin: 05px;
    width: 200px;
}

.document-section h5 {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 5px;
    margin-bottom: 15px;
}

.document-item {
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    transition: all 0.3s;
}

.document-item:hover {
    background-color: #e9ecef;
}
.docs-content {
        background: #f9f9f9;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 10px;
    }
    .bank_docs{
        height: 40px;
        box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
        /* justify-content: center; */
        align-items: center;
        gap: 20px;
    }
    .documentclk:hover {
        background-color: #f1f1f1 !important;
        box-shadow: 0 0 6px rgba(0,0,0,0.1) !important;
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
    <div class="pc-container">
        <div class="pc-content">
            <!-- <div class="page-header">
                <div class="page-block">
                    <div class="row ">
                        <div class="col-md-9 col-sm-9">
                            <input type="text" id="icon-search" class="form-control mb-4" style="height: 35px;font-size: 11px;" placeholder="Search">
                        </div>
                        <div class="col-md-3 col-sm-3 add-doctors">
                            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="height: 34px;width: 133px;font-size: 12px;background-color: #6a6ee4;--bs-btn-border-color: #6a6ee4;"><i class="ti ti-plus f-18"></i>Purchase</a>
                        </div>
                    </div>
                </div>
            </div><br><br> -->

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
                                            <th class="thview">Serial Number</th>
                                            <th class="thview">Created By</th>
                                            <th class="thview">Vendor</th>
                                            <th class="thview">Nature Payment</th>
                                            <th class="thview">Payment Status</th>
                                            <th class="thview">Account Number</th>
                                            <th class="thview">IFSC Code</th>
                                            <th class="thview">Invioce Amount</th>
                                            <th class="thview">Already Paid</th>
                                            <th class="thview">Checker Status</th>
                                            <th class="thview">Approver Status</th>
                                            <th class="thview">Bank Details</th>
                                            <th class="thview">View</th>
                                            <!-- <th class="thview">Pan Document</th>
                                            <th class="thview">Invoice Document</th>
                                            <th class="thview">BAnk Document</th>
                                            <th class="thview">Po Document</th>
                                            <th class="thview">Po Signed Document</th>
                                            <th class="thview">Po Delivery Document</th> -->
                                            <th class="thview">Action</th>
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
        </div>

        <div class="col-sm-12">
            <div class="card-body pc-component btn-page">
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">NEFT Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                            </div>
                            <ul id="save_msgList"></ul>
                            <div id="error-message"></div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">SI. No</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_serial errorss"></span>
                                                <input type="hidden" id="id" name="id">
                                                <input type="hidden" id="branch_id" name="branch_id" value="{{ $admin?->branch_id ?? '' }}">
												<input type="hidden" id="users_id" name="users_id" value="{{ $admin?->id}}">
                                                <input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px"  readonly required name="serial_number" id="serial_number" autocomplete="off">

                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Created by:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_created_by errorss"></span>
                                            <input type="text" class="form-control" id="created_by" name="created_by" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"  placeholder="Created by" >
                                        </div>
                                    </div>
                                     <div class="col-sm-3">
                                        <div class="row">
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required" style="font-size: 12px;font-weight: 600;">Vendor/ Employee Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_special errorss"></span>
                                                <div class="dropdown">
                                                    <input type="text" class="searchInput single_search" name="vendor" readonly id="vendor" placeholder="Select Specialization" required>
                                                    <!-- <div class="dropdown-options options_marketers ">
                                                        <div data-value="Vendor">Vendor</div>
                                                        <div data-value="Employee">Employee</div>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Vendor/Employee Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_vendor errorss"></span>
                                            <input type="text" class="form-control" id="vendor" name="vendor" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Vendor/Employee Name" >
                                        </div>
                                    </div> -->
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Nature of Payment:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_nature_payment errorss"></span>
                                            <!-- <input type="text" class="form-control" id="description" name="description" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Nature of Payment" > -->
                                            <select class="form-control" id="nature_payment" name="nature_payment" style="height: 42px;" disabled>
                                                <option value="">Select Status</option>
                                                <option value="Travell Allowance" >Travell Allowance</option>
                                                <option value="Expense" >Expense</option>
                                                <option value="Imprest" >Imprest</option>

                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                     <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Invoice Amount:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_invoice_amount errorss"></span>
                                            <input type="text" class="form-control" id="invoice_amount" name="invoice_amount" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"  placeholder="Invoice Amount" >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Already Paid:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_already_paid errorss"></span>
                                            <input type="number" class="form-control" id="already_paid" name="already_paid" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"  placeholder="Already Paid" >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">PAN Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_pan_number errorss"></span>
                                            <input type="text" class="form-control" id="pan_number" name="pan_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"  placeholder="PAN Number">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Account Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_account_number errorss"></span>
                                            <input type="text" class="form-control" id="account_number" name="account_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"  placeholder="Account Number">
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">IFSC Code:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_ifsc_code errorss"></span>
                                            <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"  placeholder="IFSC Code">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Payment Status:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_payment_status errorss"></span>
                                            <select class="form-control" id="payment_status" name="payment_status" style="height: 42px;" disabled>
                                                <option value="">Select Status</option>
                                                <option value="Success" >Success</option>
                                                <option value="Failed" >Failed</option>
                                                <option value="Return" >Return</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">UTR Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_utr_number errorss"></span>
                                            <input type="text" class="form-control" id="utr_number" name="utr_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"  placeholder="UTR Number" >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Payment Method:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_payment_method errorss"></span>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="payment_neft" name="payment_method[]" value="NEFT" >
                                                        <label class="form-check-label" for="payment_neft" style="font-size: 12px;">NEFT</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="payment_rtgs" name="payment_method[]" value="RTGS" >
                                                        <label class="form-check-label" for="payment_rtgs" style="font-size: 12px;">RTGS</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="payment_cheque" name="payment_method[]" value="Cheque" >
                                                        <label class="form-check-label" for="payment_cheque" style="font-size: 12px;">Cheque</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="payment_dd" name="payment_method[]" value="DD" >
                                                        <label class="form-check-label" for="payment_dd" style="font-size: 12px;">DD</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="payment_idhs" name="payment_method[]" value="IDhS" >
                                                        <label class="form-check-label" for="payment_idhs" style="font-size: 12px;">IDhS</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="payment_internet" name="payment_method[]" value="Internet Banking" >
                                                        <label class="form-check-label" for="payment_internet" style="font-size: 12px;">Internet Banking</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="payment_card" name="payment_method[]" value="Card Swipe" >
                                                        <label class="form-check-label" for="payment_card" style="font-size: 12px;">Card Swipe</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Bank Proof:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_bank_upload errorss"></span>
                                            <input type="file" class="form-control" id="bank_upload" name="bank_upload[]" multiple accept="image/*,application/pdf" disabled style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;">
                                            <input type="hidden" name="existing_bank_upload" id="existing_bank_upload">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Invoice Upload:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_invoice_upload errorss"></span>
                                            <input type="file" class="form-control" id="invoice_upload" name="invoice_upload[]" multiple accept="image/*,application/pdf" disabled style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;">
                                            <input type="hidden" name="existing_invoice_upload" id="existing_invoice_upload">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">PAN Upload:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_pan_upload errorss"></span>
                                            <input type="file" class="form-control" id="pan_upload" name="pan_upload[]" multiple accept="image/*,application/pdf" disabled style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;">
                                            <input type="hidden" name="existing_pan_file" id="existing_pan_file">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">PO Attachment:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_bank_upload errorss"></span>
                                            <input type="file" class="form-control" id="po_upload" name="po_upload[]" multiple accept="image/*,application/pdf" disabled style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;">
                                            <input type="hidden" name="existing_po_upload" id="existing_po_upload">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">PO Signed Copy Upload:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_invoice_upload errorss"></span>
                                            <input type="file" class="form-control" id="po_signed_upload" name="po_signed_upload[]" multiple accept="image/*,application/pdf" disabled style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;">
                                            <input type="hidden" name="existing_po_signed_upload" id="existing_po_signed_upload">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">PO Delivery Copy Upload:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_pan_upload errorss"></span>
                                            <input type="file" class="form-control" id="po_delivery_upload" name="po_delivery_upload[]" multiple accept="image/*,application/pdf" disabled style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;">
                                            <input type="hidden" name="existing_po_delivery_upload" id="existing_po_delivery_upload">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Status</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_nature_payment errorss"></span>
                                            <select class="form-control" id="checker_status" name="checker_status" style="height: 42px;">
                                                <option value="">Select Status</option>
                                                <option value="0" >Un Checked</option>
                                                <option value="1" >Checked</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close_button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="submit-neft-datas" style="height: 34px;width: 133px;font-size: 12px;background-color: #6a6ee4;--bs-btn-border-color: #6a6ee4;" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="modal fade"
            id="documentModal1"
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

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('/assets/js/purchase/checker.js') }}"></script>

@if ($errors->any())
<script>
    $(document).ready(function () {
        $('#exampleModal').modal('show');
    });
</script>
@endif

<script type="text/javascript">

//purchase
    const purchasefetchUrl = "{{ route('superadmin.purchasefetch') }}";
    const purchasecheckfetchUrl = "{{ route('superadmin.purchaseCheckerfetch') }}";
    const purchaseapproverfetchUrl = "{{ route('superadmin.purchaseApproverfetch') }}";
    const purchasesaveUrl = "{{ route('superadmin.purchasesave') }}";
    const purchasechecksaveUrl = "{{ route('superadmin.purchasecheckersave') }}";
    const purchaseapproversaveUrl = "{{ route('superadmin.purchaseapproversave') }}";
    const fetchUrlmorefitterpurchase = "{{ route('superadmin.fetchmorefitterpurchase') }}";
    const fetchUrlmorefitterdateclearpurchase = "{{ route('superadmin.fetchmorefitterdateclrpurchase') }}";
    const purchasefetchdatefilter = "{{ route('superadmin.purchasefetchfitter') }}";


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
        $(document).on('click', '.editbtn', function(e) {
                $("#id").val('');
                $("#serial_number").val('');
                $("#created_by").val('');
                $("#vendor").val('');
                $("#nature_payment").val('');
                $("#invoice_amount").val('');
                $("#already_paid").val('');
                $("#pan_number").val('');
                $("#account_number").val('');
                $("#ifsc_code").val('');
                $("#payment_status").val('');
                $("#utr_number").val('');
                $('input[name="payment_method[]"]').prop('checked', false);
                $("#pan_upload").val('');
                $("#invoice_upload").val('');
                $("#bank_upload").val('');
                $("#po_upload").val('');
                $("#po_signed_upload").val('');
                $("#po_delivery_upload").val('');
                $(".existing-files").hide();
                $('#exampleModal').modal('show');
            });
        $(document).on('click', '#close_button', function(e) {
                $('#exampleModal').modal('hide');
            });

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
         $(document).ready(function() {

            console.log('jQuery version:', $.fn.jquery);
            console.log('Moment version:', moment.version);
            console.log('DateRangePicker available:', $.fn.daterangepicker ? 'YES' : 'NO');
            // When zone is selected, load marketers for that zone
            $('.selectzone_camp div[data-value]').on('click', function() {
                const zoneId = $(this).data('value');
                $('#camp_zone_id').val(zoneId);
                $('#zoneviews').val($(this).text());
                $('.selectzone_camp > div').removeClass('selected');
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

    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
