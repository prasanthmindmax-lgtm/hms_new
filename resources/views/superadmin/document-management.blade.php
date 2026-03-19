<!doctype html>
<html lang="en">
<!-- [Head] start -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<!-- [Head] end -->
<!-- [Body] Start -->
<script type="text/javascript">
document.addEventListener("contextmenu", function(e) {
    e.preventDefault();
});
</script>
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
    scrollbar-color: #bbbee5 #f1f1f1;
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
    background-color: rgb(255, 255, 255) !important;
    /* Change to your desired hover color */
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

.form-control {
    height: 30px;
}

.badge {
    cursor: pointer;
}

#document_manage
{
    color: #ec008c;
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
                            <input type="text" id="icon-search" class="form-control mb-4" style="
    height: 35px;
    font-size: 11px;
" placeholder="Search">
                        </div>
                        <div class="col-md-3 col-sm-3 ">
                            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn_user"
                                data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
            height: 34px;
         width: 133px;
         font-size: 12px;
                 background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4;
     "><i class="ti ti-plus f-18"></i>Document</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <!-- [ Main Content ] end -->
            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <div class="card-body border-bottom pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                        </div>
                        <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="analytics-tab-1" data-bs-toggle="tab"
                                    data-bs-target="#analytics-tab-1-pane" type="button" role="tab"
                                    aria-controls="analytics-tab-1-pane" aria-selected="true">License Documents</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><br>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel"
                    aria-labelledby="analytics-tab-1" tabindex="0">
                    <div class="row">
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div id="myreportrange"
                                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
                                </div>

                                <span style="display:none;" id="mydateviewsall"></span>
                            </div>
                        </div>
                        @php
                        use App\Models\TblZonesModel;
                        use App\Models\TblLocationModel;

                        $locations = null;
                        $zones = null;

                        if ($admin->access_limits == 1) {
                            // Access limit 1 → All zones
                            $zones = TblZonesModel::select('name', 'id')->get();
                            $locations = TblLocationModel::select('name', 'id','zone_id')->get();

                        } else if ($admin->access_limits == 2) {
                            // Access limit 2 → User zone only + multi-locations under that zone
                            $zoneIds = [];

                            // Check if multi_location exists and is not empty
                            if (!empty($admin->multi_location)) {
                                // Parse comma-separated multi_location string to array
                                $multiLocations = explode(',', $admin->multi_location);

                                // Get zone IDs from multi-locations first
                                $locationsFromMulti = TblLocationModel::whereIn('id', $multiLocations)
                                    ->pluck('zone_id')
                                    ->unique()
                                    ->toArray();
                                // Merge with user's primary zone_id
                                $zoneIds = array_unique(array_merge([$admin->zone_id], $locationsFromMulti));

                                // Get all locations under these zones
                                $locations = TblLocationModel::select('name', 'id','zone_id')
                                    ->where('zone_id', $admin->zone_id)
                                    ->get();

                                // Also include the multi-locations specifically (in case they're from different zones)
                                $specificLocations = TblLocationModel::select('name', 'id','zone_id')
                                    ->whereIn('id', $multiLocations)
                                    ->get();

                                // Merge collections and remove duplicates
                                $locations = $locations->merge($specificLocations)->unique('id');
                            } else {
                                // If no multi_location, get locations from user's zone only
                                $locations = TblLocationModel::select('name', 'id','zone_id')
                                    ->where('zone_id', $admin->zone_id)
                                    ->get();
                                $zoneIds = [$admin->zone_id];
                            }

                            // Get zones based on collected zone IDs
                            $zones = TblZonesModel::select('name', 'id')
                                ->whereIn('id', $zoneIds)
                                ->get();

                        } else {
                            // Access limit 3 → User branch only + multi-locations
                            $branchIds = [];

                            // Always include the primary branch_id
                            $branchIds[] = $admin->branch_id;

                            // Check if multi_location exists and is not empty
                            if (!empty($admin->multi_location)) {
                                // Parse comma-separated multi_location string to array
                                $multiLocations = explode(',', $admin->multi_location);

                                // Add multi-locations to branch IDs
                                $branchIds = array_merge($branchIds, $multiLocations);

                                // Get all specific locations
                                $locations = TblLocationModel::select('name', 'id','zone_id')
                                    ->whereIn('id', $branchIds)
                                    ->get();
                            } else {
                                // If no multi_location, get only the user's branch
                                $locations = TblLocationModel::select('name', 'id','zone_id')
                                    ->where('id', $admin->branch_id)
                                    ->get();
                            }
                            // Get zone for the user (for zone dropdown if needed)
                            $zoneIds = $locations->pluck('zone_id')->unique()->toArray();
                            $zones = TblZonesModel::select('name', 'id')
                                ->whereIn('id', $zoneIds)
                                ->get();
                        }
                        @endphp
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search documentdatasearch zoneselect"
                                        name="tblzones.name" id="lic_zone_views" placeholder="Select Zone"
                                        autocomplete="off">
                                        <input type="hidden" id="lic_zone_id">
                                    <div class="dropdown-options single_search selectzone sec_options_marketers">
                                        @if($zones)
                                        @foreach($zones as $zonename)
                                        <div data-value="{{$zonename->id}}">{{$zonename->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search documentdatasearch"
                                        name="tbl_locations.name" id="lic_loc_views" placeholder="Select Branch"
                                        autocomplete="off">
                                    <div class="dropdown-options single_search sec_options_marketers" id="getlocation">

                                        @if($locations)
                                        @foreach($locations as $location)
                                        <div data-value="{{$location->name}}" data-type="{{ $location->zone_id }}">{{$location->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                            <div class="dropdown">
            <input type="text" class="form-control searchInput single_search doctypenamefilter documentdatasearch" id="selecttypeoptions"
            name="hms_document_typename.doc_type"  placeholder="Select Document Type" autocomplete="off">
            <div class="dropdown-options single_search doctypeoptions sec_options_marketers" ></div>
        </div>
                    </div>
                        </div>

                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                            <div class="dropdown">
            <input type="text" class="form-control searchInput single_search docnamefilter documentdatasearch" id="lic_name_views"
            name="hms_document_typename.doc_name" placeholder="Select document" autocomplete="off" required>
            <div class="dropdown-options single_search selectoptions sec_options_marketers" id="docnamefilteropt"></div>
        </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <div class="">
                                <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvas_mail_filter" style="height: 34px;width: 133px;font-size: 12px;background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4;"><i class="ti ti-filter f-18"></i>&nbsp; More filter</a>
                            </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>

                    </div>
                    <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="mycounts">0</span> Rows for <span
                                id="mydateallviews">Last 30 days</span></span>
                        <span class="my_search_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search
                            :</span>
                            <span style="cursor: pointer;" id="myzone_search"
                            class="badge bg-success value_views_mysearch"></span>
                            <span style="cursor: pointer;" id="licbranch_search"
                            class="badge bg-success value_views_mysearch"></span>
                        <span style="cursor: pointer;" id="lic_my_search"
                            class="badge bg-success value_views_mysearch"></span>
                        <span style="cursor: pointer;" id="licdocname_search"
                            class="badge bg-success value_views_mysearch"></span>

                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-danger clear_my_views" style="display:none;">Clear all</span>
                    </p><br>
                    <div class="col-sm-12">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="tbl">
                                    <thead class="thd">
                                        <tr class="trview">
                                            <th class="thview">S.No</th>
                                            <th class="thview">Location</th>
                                            <th class="thview">Document Type</th>
                                            <th class="thview">Document Name</th>
                                            <th class="thview">Documents</th>
                                            <th class="thview">Update Documents</th>
                                            <th class="thview">Renewal Date</th>
                                            <th class="thview">Created By</th>
                                        </tr>
                                    </thead>
                                    <tbody id="document_tbl">
                                        <tr>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="footer">
                                <div>
                                    Items per page:
                                    <select id="itemsPerPageSelectdocument">
                                        <option>3</option>
                                        <option>5</option>
                                        <option>10</option>
                                        <option>15</option>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                                <div class="pagination" id="paginationdocument"></div>
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
                                    <div
                                        class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                        <h5>Documents Filter</h5>
                                        <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                            data-bs-dismiss="offcanvas" data-bs-target="#offcanvas_mail_filter">
                                            <i class="ti ti-x f-20"></i>
                                        </a>
                                    </div>
                                    <!-- Scrollable Block -->
                                    <div class="scroll-block position-relative">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="mb-12">
                                                        <label class="form-label required">Expire Date
                                                            :</label>&nbsp;&nbsp;<span
                                                            style="font-size:10px; color:red;"
                                                            class="error_adress errorss"></span>
                                                        <input type="date" class="form-control morefittersclr"
                                                            id="search_expire_date"
                                                            name="hms_document_manage.expire_date">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                        </div><br><br><br><br><br><br><br><br>
                                        <!-- Fixed Clear All Button -->
                                        <div class="card-footer sticky-bottom bg-white">
                                            <div class="d-flex justify-content-between">
                                                <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                                    data-bs-dismiss="offcanvas"
                                                    class="btn btn-outline-danger w-50 me-2 ">Clear All</a>
                                                <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                                    data-bs-dismiss="offcanvas" id="license_search"
                                                    class="btn btn-outline-primary w-50">Submit</a>
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
                        <div class="modal fade" id="exampleModaluser" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                        <h5 class="modal-title" id="exampleModalLabel"
                                            style="color: #ffffff;font-size: 12px;">License Document Management</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            style="background-color: #ffffff;" aria-label="Close"></button>
                                    </div>
                                    <ul id="save_msgList"></ul>
                                    <div id="error-message"></div>
                                    <div class="modal-body">
                                        <input type="hidden" class="userid" name="userid" id="userid"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            value="">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="mb-3">
                                               
                                                    <label class="form-label required"
                                                        style="font-size: 12px; font-weight: 600;">Location Name
                                                        :</label>&nbsp;&nbsp;
                                                    <span style="font-size: 10px; color: red;"
                                                        class="error_location errorss"></span>

                                                    <div class="dropdown">
                                                        <input type="text" class="form-control searchInput locationsearch"
                                                            placeholder="Select Branch"
                                                            style="color: #505050 !important;width: 100%;" required
                                                            name="zone_id" id="zone_id" autocomplete="off">
                                                        <div class="dropdown-options">
                                                            @if($locations)
                                                            @foreach ($locations as $location)
                                                            <div class="dropdown-item-loc"
                                                                data-value="{{ $location->id }}">{{ $location->name}}
                                                            </div>
                                                            @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                          <!-- Document Type Input -->
<div class="col-sm-3">
    <div class="mb-3">
        <label class="form-label required" style="font-size: 12px; font-weight: 600;">Document Type:</label>
        <span style="font-size:10px; color:red;" class="error_doc_type errorss"></span>
        <div class="dropdown">
            <input type="text" class="form-control searchInput single_search doctypename" name="document_type_name"
                   id="document_typename" placeholder="Select Document Type" autocomplete="off">
            <div class="dropdown-options single_search documenttypeoptions"></div>
        </div>
    </div>
</div>

<!-- Document Name Input -->
<div class="col-sm-3">
    <div class="mb-3">
        <label class="form-label required" style="font-size: 12px;font-weight: 600;">Document Name:</label>
        <span style="font-size:10px; color:red;" class="error_doc_name errorss"></span>
        <div class="dropdown">
            <input type="text" class="form-control searchInput single_search" id="document_name" name="document_name"
                   placeholder="Select document" autocomplete="off" required>
            <div class="dropdown-options getdocname"></div>
        </div>
    </div>
</div>


                                            <div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label class="form-label required"
                                                        style="font-size: 12px;font-weight: 600;">Expire Date
                                                        :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                                        class="error_exdate errorss"></span>
                                                    <input type="date" class="form-control" id="expire_date"
                                                        name="expire_date" style="color: #505050 !important;"
                                                        placeholder="Expire Date">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <label class="form-label required"
                                                    style="font-size: 12px;font-weight: 600;">Document [ .pdf ]</label>
                                                <div class="fallback"> <span style="font-size:10px; color:red;"
                                                        class="error_pdf errorss"></span>
                                                    <input name="files[]" id="pf_uploads" type="file"
                                                        accept="application/pdf" multiple
                                                        style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;" />
                                                </div>
                                            </div>
                                        </div>


                                        <div class="modal-footer">
                                            <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                                id="close-button" class="btn btn-outline-danger"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" id="submit-document_data"
                                                style="height: 34px;width: 133px;font-size: 12px;"
                                                class="btn btn-outline-primary">Submit</button>
                                        </div>

                                        <div class="row">
                                        <div class="col-sm-3">
                                                <div class="mb-3">
                                                <button type="submit" id="add_document_data" style="margin-top: 10px;"
                                                class="btn btn-outline-primary"><i class="ti ti-plus f-18"></i> Add</button>
                                                </div>
                                            </div>
                                            <div id="item"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add meeting -->
                <div class="card-body pc-component btn-page">
                    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                    <h5 class="modal-title" id="exampleModalLabel"
                                        style="color: #ffffff;font-size: 12px;">Document Renew : #<span
                                            id="docu_id"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>
                                <ul id="save_msgList"></ul>
                                <div id="error-message"></div>
                                <div class="modal-body">
                                    <div class="row">
                                        <input type="hidden" class="id" name="id" id="id_document"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            value="">
                                        <input type="hidden" class="id" name="document_type" id="update_documents_all"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            value="">
                                        <input type="hidden" class="id" name="expire_dates" id="expire_dates"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            value="">
                                        <div class="col-sm-3">
                                            <div class="mb-3">
                                                <label class="form-label required"
                                                    style="font-size: 12px;font-weight: 600;">Expire Date
                                                    :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                                    class="error_hplname errorss"></span>
                                                <input type="date" class="form-control" id="expire_update_date"
                                                    name="expire_date"
                                                    style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                    placeholder="Address">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label required"
                                                style="font-size: 12px;font-weight: 600;">Update Document [ .pdf
                                                ]</label>
                                            <div class="fallback"> &nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                                    class="error_images errorss"></span>
                                                <input name="files[]" id="pf_update" type="file"
                                                    accept="application/pdf" multiple
                                                    style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                            id="close-button" class="btn btn-outline-danger"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" id="submit-document_update"
                                            style="height: 34px;width: 133px;font-size: 12px;"
                                            class="btn btn-outline-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card-body pc-component btn-page">
                    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                    <h5 class="modal-title" id="exampleModalLabel"
                                        style="color: #ffffff;font-size: 12px;">Document Management system</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3"><br>
                                        <div class="btn-group-vertical w-100" id="button_pdfs" style="
                                    margin-left: 11px;
                                ">
                                            <button type="button" class="btn btn-primary">Tab 1</button>
                                            <button type="button" class="btn btn-primary">Tab 2</button>
                                            <button type="button" class="btn btn-primary">Tab 3</button>
                                            <!-- More tabs if needed -->
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <iframe id="pfmain" width="100%" height="600px"></iframe>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
                    <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
                    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js">
                    </script>

                    <script type="text/javascript"
                        src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
                    <script type="text/javascript">
                    Dropzone.options.myDropzone = {
                        acceptedFiles: "image/*", // Only accept image files (any image type)
                        addRemoveLinks: true, // Optionally, show remove links for the file
                        dictDefaultMessage: "Drag an image here or click to select one image"
                    };

                    var start = moment().subtract(29, 'days');
                    var end = moment();

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
                            $('#myreportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format(
                                'DD/MM/YYYY'));
                        }
                    }
                    $('#myreportrange').daterangepicker({
                        startDate: start,
                        endDate: end,
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                                'month').endOf('month')]
                        }
                    }, my);
                    my(start, end);

                    $(document).on('click', '.editbtn_user', function(e) {
                        $('#exampleModaluser').modal('show');
                    });

                    // Simulate fetching data
                    const data = []; // Empty array means no data
                    const tableBody = document.getElementById('table-body');
                    const noDataMessage = document.getElementById('no-data');
                    const prevButton = document.getElementById('prev-button');
                    const nextButton = document.getElementById('next-button');
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
                    </script>




                    <script>


                    $(document).ready(function() {
                        // When a type is selected
                        $('.documenttypeoptions > div').on('click', function() {
                            const selectedType = $(this).data('value');

                            $('.doctypename').val(selectedType);
                            $('#document_name').val('');

                            // Show only names matching this type
                            $('.getdocname > div').hide().filter(function() {
                                return $(this).data('type') === selectedType;
                            }).show();
                        });

                        // If type field is cleared, show all names
                        $('.doctypename').on('input', function() {
                            const val = $(this).val();
                            if (!val) {
                                $('.getdocname > div').show();
                            }
                        });

                        // Optional: Show filtered options on focus in second field
                        $('#document_name').on('focus', function() {
                            const selectedType = $('.doctypename').val();
                            if (selectedType) {
                                $('.getdocname > div').hide().filter(function() {
                                    return $(this).data('type') === selectedType;
                                }).show();
                            } else {
                                $('.getdocname > div').show();
                            }
                            $('.getdocname').show();
                        });
                    });


    $(document).ready(function () {
    $('.selectzone > div').off('click').on('click', function () {
        const selectedType = $(this).data('value');
        const selectedText = $(this).text();

        $('#lic_zone_views').val(selectedText);
        $('#lic_zone_id').val(selectedType);
        $('#lic_loc_views').val('');
        $('#getlocation').hide();

        $('#getlocation > div').removeClass('selected');

       $('#getlocation > div')
            .hide()
            .filter(function () {
                return Number($(this).data('type')) === Number(selectedType);
            })
            .show();
    });

   $('#lic_zone_views').on('input', function () {
        $('#lic_zone_id').val('');
        $('#getlocation > div').show();
        $('#lic_loc_views').val('');
        $('#getlocation > div').removeClass('selected');
    });

    $('#lic_loc_views').on('focus', function () {
        const selectedType = Number($('#lic_zone_id').val()); // use hidden ID

        if (selectedType) {
            $('#getlocation > div')
                .hide()
                .filter(function () {
                    return Number($(this).data('type')) === selectedType;
                })
                .show();
        } else {
            $('#getlocation > div').show().removeClass('selected');
        }

        $('#getlocation').show();
    });
   $('#getlocation > div').off('click').on('click', function () {
        const name = $(this).data('value');
        $('#lic_loc_views').val(name);

        $('#getlocation > div').removeClass('selected');
        $(this).addClass('selected');
        $('#getlocation').hide();
    });

    $('input.searchInput').attr('autocomplete', 'off');
});


    $(document).ready(() => {
    let addinput = `
        <div class="row doc-row">
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label required" style="font-size: 12px; font-weight: 600;">Document Type :</label>
                    <div class="dropdown">
                        <input type="text" class="form-control searchInput single_search doctypename" name="doc_type[]" id="doc_type" placeholder="Select Document Type" autocomplete="off">
                       <span style="font-size:10px; color:red;" class="error_type errorss"></span>
                        <div class="dropdown-options single_search documenttypeoptions"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label required" style="font-size: 12px; font-weight: 600;">Document Name :</label>
                    <input type="text" class="form-control" name="doc_name[]" id="doc_name" placeholder="Enter Document Name" autocomplete="off">
                    <span style="font-size:10px; color:red;" class="error_name errorss"></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div>
                    <button class="remove btn btn-outline-danger" style="border-radius: 10px;">X</button>
                </div>
            </div>
        </div>`;

     $('#add_document_data').on('click', () => {
        $('#item').append(addinput);
        $('.modal-footer').hide();
        if ($('#controls').length === 0) {
            $('#item').after(`
                <div id="controls" class="mt-3">
                    <button type="submit" id="submitBtndoc" class="btn btn-outline-primary">Submit</button>
                    <button id="closeBtn" class="btn btn-outline-secondary">Cancel</button>
                </div>
            `);
        }
        fetchDropdownData();
    });


    $("body").on("click", ".remove", function (e) {
    e.preventDefault();
    $(this).closest(".doc-row").remove();

   if ($('.doc-row').length === 0) {
        $('#controls').remove(); // Remove submit/cancel buttons
        $('.modal-footer').show(); // Show original modal footer
    }
});

    $("body").on("click", "#closeBtn", function () {
        $('.modal-footer').show();
        $('#item').empty();
        $('#controls').remove();
    });
    $("#close-button").on("click", function(){
        $('#item').empty();
        $('#controls').remove();
    });

    $('body').on("click", '#submitBtndoc', function (event){
    event.preventDefault();

    let isValid = true;
    let formData = new FormData();

    $('.doc-row').each(function () {
        const docType = $(this).find('input[name="doc_type[]"]').val();
        const docName = $(this).find('input[name="doc_name[]"]').val();
        const errorType = $(this).find('.error_type');
        const errorName = $(this).find('.error_name');

        errorType.text('');
        errorName.text('');

        if (!docType) {
            errorType.text("Enter the Document Type");
            isValid = false;
        }

        if (!docName) {
            errorName.text("Enter the Document Name");
            isValid = false;
        }

        formData.append('doc_type[]', docType);
        formData.append('doc_name[]', docName);
    });

    if (!isValid) return;

    $.ajax({
        url: doctypenameaddUrl,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
    window.dispatchEvent(new CustomEvent('swal:toast', {
        detail: {
            title: 'Info!',
            text: response.message,
            icon: 'success',
            background: 'success',
        }
    }));
    // Close modal, reset view
    $('.modal-footer').show();
    $("#exampleModaluser").modal('hide');
    $('#item').empty().show();
    $('#controls').remove();
    fetchDropdownData();
}
 else {
                window.dispatchEvent(new CustomEvent('swal:toast', {
                    detail: {
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        background: '#f8d7da',
                    }
                }));
            }

        },
        error: function (error) {
            console.error(error.responseJSON);
        },
    });
});

$("#close-button, #closeBtn").on("click", function () {
    resetDocumentForm();
});

});


function resetDocumentForm() {
    $('#item').empty().show();
    $('#controls').remove();
    $('.modal-footer').show();
}

function fetchDropdownData() {
    $.ajax({
        url: getDocnametype,
        type: "GET",
        success: function (data) {
            const docTypeOptions = $('.documenttypeoptions');
            const docNameOptions = $('.getdocname');
            const doctypenamefil = $('.doctypeoptions');
            const docNameOptionfil = $('.selectoptions');

            // Populate Document Types
            docTypeOptions.empty();
            doctypenamefil.empty();

            Object.keys(data).forEach(function(type) {
                docTypeOptions.append(`<div data-value="${type}" class="dropdown-item-dcotype">${type}</div>`);
                doctypenamefil.append(`<div data-value="${type}" class="dropdown-itemdcotypefilter">${type}</div>`);
            });

            // On selecting document type (main dropdown)
            $(document).on('click', '.dropdown-item-dcotype', function () {
                let selectedType = $(this).data('value');
                $('.doctypename').val(selectedType);

                // Fill related document names
                let names = data[selectedType] || [];
                docNameOptions.empty();
                names.forEach(function(item) {
                    docNameOptions.append(
                        `<div data-value="${item.id}" data-type="${selectedType}" class="docnameselect">${item.name}</div>`
                    );
                });
        $('.docnameselect').on('click', function () {
        var selecteddoctype = $(this).text();
        var selecteddoctypeId = $(this).data('value');
        $('#document_name').val(selecteddoctype);
        $('#document_name').attr('data-value', selecteddoctypeId);
    });
            });

    $(document).on('click', '.dropdown-itemdcotypefilter', function () {
    let selectedType = $(this).data('value');

    let names = data[selectedType] || [];
    const docNameOptionfil = $('.selectoptions');
    docNameOptionfil.empty();

    names.forEach(function(item) {
        docNameOptionfil.append(
            `<div data-value="${item.name}" data-type="${selectedType}">${item.name}</div>`
        );
    });

});
            // On selecting document name
            $(document).on('click', '.getdocname div', function () {
                $('#document_name').val();
            });

            $(document).on('click', '.selectoptions div', function () {
                $('#lic_name_views').val();
            });
        },

        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}


$(document).ready(function () {
    fetchDropdownData();
});

                    </script>

                    <script>
                    const licensedocdetialsUrl = "{{route('superadmin.licensedoc_detials')}}";
                    const licexpdateUrl = "{{route('superadmin.licexpdatefilter')}}";
                    const doctypenameaddUrl = "{{route('superadmin.doctypename')}}";
                    const getDocnametype ="{{route('superadmin.getdocnametype')}}";
                    </script>
                    <!-- [ Main Content ] end -->
                    @include('superadmin.superadminfooter')
                    <script src="{{ asset('/assets/document/documentadded.js') }}"></script>
</body>
<!-- [Body] end -->

</html>