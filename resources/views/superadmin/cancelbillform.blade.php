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
<link rel="stylesheet" href="{{ asset('/assets/css/statistics.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
#loader-container {
    width: 100%;
    background: #eee;
    border: 1px solid #ccc;
}

.progress-bar {
    height: 30px;
    width: 0%;
    background-color: #4caf50;
    color: white;
    font-weight: bold;
    text-align: center;
    line-height: 30px;
    transition: width 0.3s ease;
}
.progress-bar2 {
    height: 30px;
    width: 0%;
    background-color: #4caf50;
    color: white;
    font-weight: bold;
    text-align: center;
    line-height: 30px;
    transition: width 0.3s ease;
}
</style>
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
    overflow-y: auto;
    max-height: 450px;
}

.table-container::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.table-container::-webkit-scrollbar-thumb {
    background: #b163a6;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #df64ce;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-container {
    scrollbar-width: thin;
    scrollbar-color: #efdaec #f1f1f1;
}

.tbl {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.thd {
    position: sticky;
    top: -1px;
    z-index: 10;
    background: #f8f8f8;
    box-shadow: 12px -1px 0px rgba(0, 0, 0, 0.1);
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
    border: 2px solid #6a6ee4;
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
.action-icons {
        display: inline-flex;
        gap: 8px;              /* space between ✔ and ✖ */
        align-items: center;
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

.pc-container .page-header+.row {
    padding-top: -5px;
    margin-top: -22px;
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

.dropdown.active .dropdown-options {
    display: block;
}

.dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
}

.btn-primary.d-inline-flex:hover {
    background-color: rgb(255, 255, 255) !important;
    border-color: #4b4fc5 !important;
    color: #6a6ee4;
}

.form-control {
    height: 30px;
}

.badge {
    cursor: pointer;
}

.button-container {
    display: flex;
    gap: 10px;
    margin-top: 7px;
}
/* Status styles */
.status-approved {
    color: #2e7d32;
    font-weight: bold;
}

.status-rejected {
    color: #c62828;
    font-weight: bold;
}

.status-pending {
    color: #666;
    font-weight: bold;
}

/* Buttons */
.approver-action {
    text-align: center;
}
/*
.btn {
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    font-size: 13px;
    cursor: pointer;
    margin: 2px;
}

.btn-approve {
    background-color: #2e7d32;
    color: #fff;
}

.btn-approve:hover {
    background-color: #1b5e20;
}

.btn-reject {
    background-color: #c62828;
    color: #fff;
}

.btn-reject:hover {
    background-color: #8e0000;
} */
.signature-canvas {
    width: 100%;
    max-width: 500px;
    height: 150px;
    border: 1px solid #000;
    background: #fff;
    display: block;
}

.btn-approve,
.btn-reject {
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    user-select: none;
}

.btn-approve {
    color: green;
}

.btn-reject {
    color: red;
}

.btn-approve:hover,
.btn-reject:hover {
    opacity: 0.7;
}
</style>
<script>
function rowClick(event) {
    const selectedRows = document.querySelectorAll('.selected');
    selectedRows.forEach(row => row.classList.remove('selected'));
    const clickedRow = event.currentTarget;
    clickedRow.classList.add('selected');
}
</script>

<body style="overflow-x: hidden;">
    <div class="page-loader">
        <div class="bar"></div>
    </div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row ">
                        <div class="col-md-9 col-sm-9">
                            <input type="text" id="icon-search" class="form-control mb-4" style="
    height: 35px;
    font-size: 11px;" placeholder="Search">
                        </div>
                        <div class="col-md-3 col-sm-3 ">
                            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn_user"
                                data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
            height: 34px;
         width: 133px;
         font-size: 12px;
                 background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4; "><i class="ti ti-plus f-18"></i>Document</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <div class="card-body border-bottom pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                        </div>
                        <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="analytics-tab-1" data-bs-toggle="tab"
                                    data-bs-target="#analytics-tab-1-pane" type="button" role="tab"
                                    aria-controls="analytics-tab-1-pane" aria-selected="true">Cancel Bill Form</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="analytics-tab-2" data-bs-toggle="tab"
                                    data-bs-target="#analytics-tab-2-pane" type="button" role="tab"
                                    aria-controls="analytics-tab-2-pane" aria-selected="false">Saved Cancel Bill</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><br>
            <div class="tab-content" id="myTabContent">

                <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel" aria-labelledby="analytics-tab-1" tabindex="0">
                     <div class="row filterrow">
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


                        {{-- @php $zone = App\Models\TblZonesModel::select('tblzones.name','tblzones.id')->get(); @endphp --}}
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search documentdatasearch"
                                        name="tblzones.name" id="can_zone_views" placeholder="Select Zone"
                                        autocomplete="off">
                                          <input type="hidden" id="can_zone_id">
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
                                        name="tbl_locations.name" id="can_loc_views" placeholder="Select Branch"
                                        autocomplete="off">
                                    <div class="dropdown-options single_search sec_options_marketers" id="getlocation">
                                        @if($locations)
                                        @foreach($locations as $location)
                                        <div data-value="{{$location->name}}" data-type="{{ $location->zone_id }}">
                                            {{$location->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search form-control documentdatasearch"
                                        name="phid" id="ref_mrd_views" placeholder="Enter the MRD" autocomplete="off">

                                </div>
                            </div>
                        </div>
                    </div>

                     <div class="col-xl-2 col-md-2">
                        <div class="" hidden>
                            <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvas_mail_filter" style="height: 34px;width: 133px;font-size: 12px;background-color: #6a6ee4;
                                --bs-btn-border-color: #6a6ee4;"><i class="ti ti-filter f-18"></i>&nbsp; More filter</a>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                      </div>

                <p style="margin-top: -25px;" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span
                            id="mycounts">0</span><span id="billcounts" class="hide">0</span> Rows for <span
                            id="mydateallviews">Last 30 days</span></span>
                    <span class="my_search_view search_this" style="color: #e40505;font-size: 12px;font-weight: 600;">Search
                        :</span>
                    <span style="cursor: pointer;" id="refzone_search"
                        class="badge bg-success value_views_mysearch search_this"></span>
                    <span style="cursor: pointer;" id="refbranch_search"
                        class="badge bg-success value_views_mysearch search_this"></span>
                    <span style="cursor: pointer;" id="refmrdno_search"
                        class="badge bg-success value_views_mysearch search_this"></span>

                    <span class="badge bg-success my_value_views search_this"></span>
                    <span class="badge bg-success my_value_views search_this"></span>
                    <span class="badge bg-success my_value_views search_this"></span>
                    <span class="badge bg-success my_value_views search_this"></span>
                    <span class="badge bg-success my_value_views search_this"></span>
                    <span class="badge bg-danger clear_my_views search_this" style="display:none;">Clear all</span>
                </p><br>

                <div class="col-sm-12">
                    <input type="hidden" id="hidden_billno">
                    <div class="card-body">
                        <div class="table-container maintable">
                            <table class="tbl">
                                <thead class="thd">
                                    <tr class="trview">
                                        <th class="thview">S.No</th>
                                        <th class="thview">Zone Name</th>
                                        <th class="thview">Branch Name</th>
                                        <th class="thview">Wife MRD No / Name</th>
                                        <th class="thview">Husband MRD No / Name</th>
                                        <th class="thview">OP No</th>
                                        <th class="thview">Age</th>
                                        <th class="thview">Gender</th>
                                        <th class="thview">mobile</th>
                                        <th class="thview">Consultant</th>
                                        <th class="thview">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="loader_row">
                                    <tr>
                                        <td colspan="15">
                                            <div id="loader-container">
                                                <div class="progress-bar">Loading: 0%</div>
                                                <div id="error-message" style="color: red; display: none;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody id="document_tbl">
                                    <tr>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-container subtable hide">
                            <button id="backToMain" class="btn btn-secondary" style="margin: 10px;">← Back</button>
                            <table class="tbl">
                                <thead class="thd">
                                    <tr class="trview">
                                        <th class="thview">S.No</th>
                                        <th class="thview">Zone Name</th>
                                        <th class="thview">Branch Name</th>
                                        <th class="thview">Wife MRD No / Name</th>
                                        <th class="thview">Husband MRD No / Name</th>
                                        <th class="thview">Bill No</th>
                                        <th class="thview">Bill Type</th>
                                        <th class="thview">Bill Date</th>
                                        <th class="thview">Consultant</th>
                                        <th class="thview">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bill_tbl">
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
                                <select id="itemsPerPageSelectbill" class="hide">
                                    <option value="3" selected>3</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="pagination" id="paginationdocument"></div>
                            <div class="pagination hide" id="paginationbill"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel" aria-labelledby="analytics-tab-2" tabindex="0">

                    <div class="row">
                        <div class="statistics-container">
                            <div class="statistics-header">
                                <h2>
                                    <i class="fas fa-chart-bar"></i>
                                    Cancel Form Statistics
                                </h2>
                            </div>
                            
                            <div class="statistics-cards" id="statisticsCards">
                                <!-- Cards will be dynamically populated via JavaScript -->
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div id="mysavereportrange"
                                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
                                </div>

                                <span style="display:none;" id="mydateviewsallsave"></span>
                            </div>
                        </div>
                       

                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search savedatasearch"
                                        name="tblzones.name" id="save_zone_views" placeholder="Select Zone"
                                        autocomplete="off">
                                    <input type="hidden" id="dissave_zone_id">
                                    <div class="dropdown-options single_search selectzonesave savedata_options">
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
                                    <input type="text" class="searchInput single_search savedatasearch"
                                        name="tbl_locations.name" id="save_loc_views" placeholder="Select Branch"
                                        autocomplete="off">
                                    <div class="dropdown-options single_search savedata_options" id="getlocationsave">
                                        @if($locations)
                                        @foreach($locations as $location)
                                        <div data-value="{{$location->name}}" data-type="{{ $location->zone_id }}">
                                            {{$location->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2" hidden>
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search form-control savedatasearch"
                                        name="phid" id="save_mrd_views" placeholder="Enter the MRD" autocomplete="off">

                                </div>
                            </div>
                        </div>
                    </div>

                     <div class="col-xl-2 col-md-2">
                        <div class="" hidden>
                            <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvas_mail_filter" style="height: 34px;width: 133px;font-size: 12px;background-color: #6a6ee4;
                                --bs-btn-border-color: #6a6ee4;"><i class="ti ti-filter f-18"></i>&nbsp; More filter</a>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                      </div>

            <p style="margin-top: -25px;" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="billsavecounts">0</span> Rows for <span
                            id="mydateallviewssave">Last 30 days</span></span>
                    <span class="my_search_saveview " style="color: #e40505;font-size: 12px;font-weight: 600;">Search
                        :</span>
                    <span style="cursor: pointer;" id="savezone_search"
                        class="badge bg-success value_save_mysearch"></span>
                    <span style="cursor: pointer;" id="savebranch_search"
                        class="badge bg-success value_save_mysearch "></span>
                    <span style="cursor: pointer;" id="savemrdno_search"
                    class="badge bg-success value_save_mysearch "></span>
                    <span class="badge bg-success my_savevalue_views "></span>
                    <span class="badge bg-success my_savevalue_views "></span>
                    <span class="badge bg-success my_savevalue_views "></span>
                    <span class="badge bg-success my_savevalue_views "></span>
                    <span class="badge bg-success my_savevalue_views "></span>
                    <span class="badge bg-danger clear_my_saveviews " style="display:none;">Clear all</span>
                </p><br>


                <div class="row">
                <div class="col-sm-12">
                    <div class="card-body">
                        <div class="table-container">
                            <table class="tbl">
                                <thead class="thd">
                                    <tr class="trview">
                                        <th class="thview">S.No</th>
                                        <th class="thview">Zone Name</th>
                                        <th class="thview">Branch Name</th>
                                        <th class="thview">Wife MRD No / Name</th>
                                        <th class="thview">Husband MRD No / Name</th>
                                        <th class="thview">OP No</th>
                                        <th class="thview">Age</th>
                                        <th class="thview">Gender</th>
                                        <th class="thview">mobile</th>
                                        <th class="thview">Consultant</th>
                                        <th class="thview">Bill No</th>
                                        <th class="thview">Bill Type</th>
                                        <th class="thview">Bill Date</th>
                                        <th class="thview">Bill Amount</th>
                                        <th class="thview">Uploaded By</th>
                                            @if($admin->access_limits == 1)
                                                <th class="thview">Admin Approved</th>
                                                <th class="thview">Zonal Approved</th>
                                                <th class="thview">Audit Approved</th>
                                                <th class="thview">Status</th>
                                                <th class="thview">Action</th>
                                                <th class="thview">Edit</th>

                                                @elseif($admin->access_limits == 2)
                                                <th class="thview">Admin Approved</th>
                                                <th class="thview">Audit Approved</th>
                                                <th class="thview">Final Approved</th>
                                                <th class="thview">Status</th>
                                                <th class="thview">Action</th>
                                                <th class="thview">Edit</th>

                                                @elseif($admin->access_limits == 3)
                                                <th class="thview">Zonal Approved</th>
                                                <th class="thview">Audit Approved</th>
                                                <th class="thview">Final Approved</th>
                                                <th class="thview">Status</th>
                                                <th class="thview">Action</th>
                                                <th class="thview">Edit</th>

                                                @elseif($admin->access_limits == 4)
                                                <th class="thview">Admin Approved</th>
                                                <th class="thview">Zonal Approved</th>
                                                <th class="thview">Final Approved</th>
                                                <th class="thview">Status</th>
                                                <th class="thview">Action</th>
                                                <th class="thview">Edit</th>

                                            @endif

                                            <th class="thview">Print</th>
                                        <!-- <th class="thview">Approver Status</th>
                                        @if($isApprover)
                                            <th class="thview">Approver Action</th>
                                        @endif

                                        <th class="thview">Print</th>
                                        <th class="thview">Action</th> -->
                                    </tr>
                                </thead>
                                <tbody id="loader_row">
                                    <tr>
                                        <td colspan="15">
                                            <div id="loader-container">
                                                <div class="progress-bar2">Loading: 0%</div>
                                                <div id="error-message" style="color: red; display: none;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody id="sveddata_tbl">
                                    <tr>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="footer">
                            <div>
                                Items per page:
                                <select id="itemsPerPageSelectsave">
                                    <option>3</option>
                                    <option>5</option>
                                    <option>10</option>
                                    <option>15</option>
                                    <option>25</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select>
                            </div>
                            <div class="pagination" id="paginationsavedata"></div>

                        </div>
                    </div>
                </div>

                </div></div>
            <div id="printSection" style="display: none;"></div>

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
                                                    <label class="form-label required"
                                                        style="font-size: 12px;font-weight: 600;">Document
                                                        Name:</label>&nbsp;&nbsp;<span
                                                        style="font-size:10px; color:red;"
                                                        class="error_doctor errorss"></span>
                                                    <input type="text" class="form-control " id="doctornames_more"
                                                        name="doctor_name"
                                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                        placeholder="Document Name">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-12"><br>
                                                    <label class="form-label required"
                                                        style="font-size: 12px;font-weight: 600;">Zone:</label>
                                                    <select class="mb-3 form-select zoneid"
                                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                        required name="empolyee_name">
                                                    </select>
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
                                                data-bs-dismiss="offcanvas"
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
                                        style="color: #ffffff;font-size: 12px;">Cancel Bill Form</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <input type="hidden" class="userid" name="userid" id="userid">

                                    <style>
                                    .form-border {
                                        border: 1px solid #000;
                                        padding: 20px;
                                    }

                                    .form-title {
                                        font-weight: bold;
                                        font-size: 20px;
                                    }

                                    .form-subtitle {
                                        font-size: 14px;
                                    }

                                    .label-title {
                                        font-weight: bold;
                                    }

                                    .dotted-line {
                                        border-bottom: 1px dotted #000;
                                        padding-bottom: 5px;
                                        margin-bottom: 10px;
                                    }

                                    .form-row-line {
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                    }

                                    .form-label-col {
                                        width: 20%;
                                        font-weight: bold;
                                    }

                                    .form-colon {
                                        width: 2%;
                                        text-align: center;
                                    }

                                    .form-input {
                                        width: 100%;
                                        border: none;
                                        border-bottom: 1px dotted #000;
                                    }

                                    .form-input2 {
                                        width: 44%;
                                        border: none;
                                        border-bottom: 1px dotted #000;
                                    }

                                    .form-input3 {
                                        border: none;
                                        border-bottom: 1px dotted #000;
                                    }

                                    .signatures .col {
                                        text-align: left;
                                        font-weight: bold;
                                        border-bottom: 1px dotted #000;
                                        padding-top: 20px;
                                    }

                                    .avatar-upload {
                                        position: relative;
                                        display: inline-flex;
                                        margin: 0px 0px 0px 90px;

                                        .avatar-edit {
                                            position: absolute;
                                            /* right: 12px; */
                                            left: 5px;
                                            z-index: 1;
                                            top: -10px;

                                            input {
                                                display: none;

                                                +label {
                                                    display: inline-block;
                                                    width: 34px;
                                                    height: 34px;
                                                    margin-bottom: 0;
                                                    border-radius: 100%;
                                                    background: #FFFFFF;
                                                    border: 1px solid transparent;
                                                    box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
                                                    cursor: pointer;
                                                    font-weight: normal;
                                                    transition: all .2s ease-in-out;

                                                    &:hover {
                                                        background: #f1f1f1;
                                                        border-color: #d6d6d6;
                                                    }

                                                    &:after {
                                                        content: "\f093";
                                                        font-family: 'FontAwesome';
                                                        color: #757575;
                                                        position: absolute;
                                                        top: 10px;
                                                        left: 0;
                                                        right: 0;
                                                        text-align: center;
                                                        margin: auto;
                                                    }
                                                }
                                            }
                                        }

                                        .avatar-preview {
                                            width: 230px;
                                            height: 45px;
                                            position: relative;
                                            border-radius: 10px;
                                            border: 6px solid #F8F8F8;
                                            box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);

                                            >div {
                                                width: 100%;
                                                height: 100%;
                                                border-radius: 10px;
                                                background-size: cover;
                                                background-repeat: no-repeat;
                                                background-position: center;
                                            }
                                        }
                                    }

                                    .form-label-col1 {
                                        width: 10%;
                                        font-weight: bold;
                                    }

                                    .form-colon1 {
                                        width: 2%;
                                        text-align: center;
                                    }

                                    .form-input1 {
                                        width: 20%;
                                        border: none;
                                        border-bottom: 1px dotted #000;
                                    }

                                    #product_detials>thead>tr>th {
                                        border: 1px solid #d9dcdf;
                                        padding: 5px;
                                        border-collapse: collapse;
                                    }

                                    #product_detials>tbody>tr>td {
                                        border: 1px solid #d9dcdf;
                                        padding: 5px;
                                        border-collapse: collapse;
                                    }

                                    #product_detials_edit>thead>tr>th {
                                        border: 1px solid #d9dcdf;
                                        padding: 5px;
                                        border-collapse: collapse;
                                    }

                                    #product_detials_edit>tbody>tr>td {
                                        border: 1px solid #d9dcdf;
                                        padding: 5px;
                                        border-collapse: collapse;
                                    }

                                    .tdinput {
                                        border: 1px solid #e7eaee;
                                        width: 100%;
                                    }

                                    .form-label-col4 {
                                        width: 15%;
                                        font-weight: bold;
                                    }

                                    .form-input4 {
                                        width: 25%;
                                        border: none;
                                        border-bottom: 1px dotted #000;
                                    }

                                    .hide {
                                        display: none;
                                    }
                                    </style>

                                    <div class="form-border">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <div class="form-title">Dr. ARAVIND’s IVF</div>
                                                <div class="form-subtitle">FERTILITY & PREGNANCY CENTRE</div>
                                            </div>
                                            <div class="text-center align-self-center px-3 border"><strong>CANCEL BILL
                                                    FORM</strong></div>
                                            <div class="align-self-start">
                                                <!-- S. No :  <input type="text" id="sno" style="border: none; border-bottom: 1px dotted #000;width: 100%;"> -->
                                            </div>
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col1">OP No</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="opno">
                                            <div style="width:36%"></div>
                                            <div class="form-label-col1">Token No</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="tokenno">
                                        </div>
                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col1">Consultant</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="consultant">
                                            <div style="width:36%"></div>
                                            <div class="form-label-col1">Bill No</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="billno">
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col1">Date</div>
                                            <div class="form-colon1">:</div>
                                            <input type="date" class="form-input1" id="bill_date">
                                            <div style="width:36%"></div>

                                            <div class="form-label-col1">Branch Name</div>
                                            <div class="form-colon1">:</div>
                                            @php $locations =
                                            App\Models\TblLocationModel::select('tbl_locations.name','tbl_locations.id')->get();
                                            @endphp
                                            <div class="dropdown" style="width: 20%;">
                                                <input type="text" class="form-control searchInput single_search locationsearch"
                                                    placeholder="Select Branch" style="color: #505050 !important;"
                                                    required name="zone_id" id="zone_id" autocomplete="off">
                                                <div class="dropdown-options">
                                                    @if($locations)
                                                    @foreach ($locations as $location)
                                                    <div class="dropdown-item-loc" data-value="{{ $location->id }}">
                                                        {{ $location->name}}
                                                    </div>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            <!-- <input type="text" class="form-input" id="branchname"> -->
                                        </div>
                                        <hr>
                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col">Name : <br>
                                                <input type="text" class="form-input3" id="pat_name">
                                            </div>
                                            <div class="form-label-col" style="">MRD No(ID) : <br>
                                                <input type="text" class="form-input3" id="pat_mrdno">
                                            </div>
                                            <div class="form-label-col">Age : <br>
                                                <input type="text" class="form-input3" id="pat_age">
                                            </div>
                                            <div class="form-label-col" style="">Gender : <br>
                                                <input type="radio" class="form-check-input" id="female"
                                                    name="pat_gender_edit" value="F">
                                                  <label for="female">Female</label>
                                                  <input type="radio" class="form-check-input" id="male"
                                                    name="pat_gender_edit" value="M">
                                                  <label for="male">Male</label>
                                            </div>
                                            <div class="form-label-col" style="">Mobile : <br>
                                                <input type="text" class="form-input3" id="pct_mobile">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col1">Payment Type</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="payment_type">

                                            <div class="form-label-col1">Payment Details</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="payment_details">

                                            <div class="form-label-col" style="">
                                                <input type="radio" class="form-check-input" id="op" name="request"
                                                    value="OP">
                                                  <label for="html">op</label>
                                                  <input type="radio" class="form-check-input" id="ip" name="request"
                                                    value="IP">
                                                  <label for="ip">IP</label>
                                                  <input type="radio" class="form-check-input" id="pharmacy"
                                                    name="request" value="Pharmacy">
                                                  <label for="pharmacy">Pharmacy</label>
                                            </div>
                                        </div>
                                        <div class="form-row-line mb-2">
                                            <button class="btn btn-outline-primary" onclick="addRow()">Add Row</button>
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <table style="width:100%;" id="product_detials">
                                                <thead>
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>Particulars</th>
                                                        <th>Qty</th>
                                                        <th>Rate</th>
                                                        <th>Tax(%)</th>
                                                        <th>Amount</th>
                                                        <th>Action</th> <!-- New column for Delete button -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Rows will be added here -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Totals and other inputs -->
                                        <div class="form-row-line mb-2">
                                            <div style="width:76%"></div>
                                            <div class="form-label-col4">Total</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="totalamt" readonly>
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div style="width:76%"></div>
                                            <div class="form-label-col4">Previous Balance</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="prebalanceamt">
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div style="width:76%"></div>
                                            <div class="form-label-col4">Amount Receivable</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="receivableamt" readonly>
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col4">Amount (in words)</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="receivedamtword" readonly>
                                            <div style="width:34%"></div>
                                            <div class="form-label-col4">Amount Received</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="receivedamt">
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col4">Advance(in words)</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="advancedamtword" readonly>
                                            <div style="width:34%"></div>
                                            <div class="form-label-col4">Advance</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="advancedamt" readonly>
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div style="width:76%"></div>
                                            <div class="form-label-col4">Prepared By</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="prepared">
                                        </div>
                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col" style="width:100%">Cancel Reason :
                                                <textarea rows="4" class="form-input" style="border: 1px solid #e7eaee;"
                                                    id="cancelreason"> </textarea>
                                            </div>
                                        </div>
                                        <div class="row signatures">
                                            <div class="col">Admin Sign
                                                <div class="signature-option-group" data-target="admin">
                                                    <label><input type="radio" class="form-check-input"
                                                            name="admin-signature" value="upload" checked> Upload
                                                        Image</label>
                                                    <label><input type="radio" class="form-check-input"
                                                            name="admin-signature" value="draw"> Digital Sign</label>
                                                </div>

                                                <div class="avatar-upload" id="admin-upload"
                                                    style="margin: 0px;margin-top: 10px;">
                                                    <i class="fa fa-close clear-sign" data-target="admin"
                                                        style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                    <div class="avatar-edit" style="position: relative;">
                                                        <input type='file' id="adminsignimg"
                                                            accept=".png, .jpg, .jpeg" />
                                                        <label for="adminsignimg"></label>
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div id="adminimgPreview"></div>
                                                    </div>
                                                </div>

                                                <div class="digital-sign" id="admin-sign" style="display:none;">
                                                    <canvas id="adminCanvas" width="500" height="150" class="signature-canvas"></canvas>
                                                    <button type="button" class="btn btn-outline-danger"
                                                        onclick="clearCanvas('adminCanvas')">Clear</button>
                                                </div>
                                            </div>

                                            <div class="col" id="zonalsign">zonal head Sign
                                                <div class="signature-option-group" data-target="cc">
                                                    <label><input type="radio" class="form-check-input"
                                                            name="cc-signature" value="upload" checked> Upload
                                                        Image</label>
                                                    <label><input type="radio" class="form-check-input"
                                                            name="cc-signature" value="draw"> Digital Sign</label>
                                                </div>

                                                <div class="avatar-upload" id="cc-upload"
                                                    style="margin: 0px;margin-top: 10px;">
                                                    <i class="fa fa-close clear-sign" data-target="cc"
                                                        style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                    <div class="avatar-edit" style="position: relative;">
                                                        <input type='file' id="ccsignimg" accept=".png, .jpg, .jpeg" />
                                                        <label for="ccsignimg"></label>
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div id="ccimgPreview"></div>
                                                    </div>
                                                </div>

                                                <div class="digital-sign" id="cc-sign" style="display:none;">
                                                    <canvas id="ccCanvas" width="300" height="50"
                                                        style="border:1px solid #000;"></canvas>
                                                    <button type="button" class="btn btn-outline-danger"
                                                        onclick="clearCanvas('ccCanvas')">Clear</button>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                                id="close-button" class="btn btn-outline-danger"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" id="submit_cancelform"
                                                style="height: 34px;width: 133px;font-size: 12px;"
                                                class="btn btn-outline-primary">Submit</button>
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
                                        style="color: #ffffff;font-size: 12px;">Edit Cancel Bill Form<span
                                            id="docu_id"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" class="userid" name="ref_id" id="edit_disid" value="">
                                    <div class="form-border">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <div class="form-title">Dr. ARAVIND’s IVF</div>
                                                <div class="form-subtitle">FERTILITY & PREGNANCY CENTRE</div>
                                            </div>
                                            <div class="text-center align-self-center px-3 border"><strong>CANCEL BILL
                                                    FORM</strong></div>
                                            <div class="align-self-start">
                                                <!-- S. No :  <input type="text" id="sno" style="border: none; border-bottom: 1px dotted #000;width: 100%;"> -->
                                            </div>
                                        </div>
                                        <input type="hidden" id="locationid">
                                        <input type="hidden" id="edit_can_id" value="">
                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col1">OP No</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="opno_edit" readonly>
                                            <div style="width:36%"></div>
                                            <div class="form-label-col1">Token No</div>
                                            <div class="form-colon1">:</div><span style="font-size:10px; color:red;" class="token_no errorss"></span>
                                            <input type="text" class="form-input1" id="token_no_edit">
                                        </div>
                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col1">Consultant</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="consultant_edit" readonly>
                                            <div style="width:36%"></div>
                                            <div class="form-label-col1">Bill No</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="billno_edit" readonly>
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col1">Date</div>
                                            <div class="form-colon1">:</div>
                                            <input type="date" class="form-input1" id="bill_date_edit" readonly>
                                            <div style="width:36%"></div>

                                            <div class="form-label-col1">Branch Name</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="zone_id_edit" readonly>
                                            <!-- <input type="text" class="form-input" id="branchname"> -->
                                        </div>
                                        <hr>
                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col">Name : <br>
                                                <input type="text" class="form-input3" id="pat_name_edit" readonly>
                                            </div>
                                            <div class="form-label-col" style="">MRD No(ID) : <br>
                                                <input type="text" class="form-input3" id="pat_mrdno_edit" readonly>
                                            </div>
                                            <div class="form-label-col">Age : <br>
                                                <input type="text" class="form-input3" id="pat_age_edit" readonly>
                                            </div>
                                            <div class="form-label-col" style="">Gender : <br>
                                                <input type="radio" class="form-check-input" id="female"
                                                    name="pat_gender_edit" value="F" disabled>
                                                  <label for="female">Female</label>
                                                  <input type="radio" class="form-check-input" id="male"
                                                    name="pat_gender_edit" value="M" disabled>
                                                  <label for="male">Male</label>
                                            </div>
                                            <div class="form-label-col" style="">Mobile : <br>
                                                <input type="text" class="form-input3" id="pct_mobile_edit" readonly>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col1">Payment Type</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input1" id="payment_type_edit" readonly>

                                            <div class="form-label-col1">Payment Details</div>
                                            <div class="form-colon1">:</div><span style="font-size:10px; color:red;" class="payment errorss"></span>
                                            <input type="text" class="form-input1" id="payment_details_edit">

                                            <div class="form-label-col" style="">
                                                <input type="radio" class="form-check-input" name="request_edit"
                                                    value="OP" disabled>
                                                  <label for="op">op</label>
                                                  <input type="radio" class="form-check-input" name="request_edit"
                                                    value="IP" disabled>
                                                  <label for="ip">IP</label>
                                                  <input type="radio" class="form-check-input" name="request_edit"
                                                    value="Pharmacy" disabled>
                                                  <label for="pharmacy">Pharmacy</label>
                                            </div>
                                        </div>
                                        <!-- <div class="form-row-line mb-2">
                                        <button class="btn btn-outline-primary" onclick="addRow()">Add Row</button>
                                        </div> -->

                                        <div class="form-row-line mb-2">
                                            <table style="width:100%;" id="product_detials_edit">
                                                <thead>
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>Particulars</th>
                                                        <th>Qty</th>
                                                        <th>Rate</th>
                                                        <th>Tax(%)</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Rows will be added here -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Totals and other inputs -->
                                        <div class="form-row-line mb-2">
                                            <div style="width:76%"></div>
                                            <div class="form-label-col4">Total</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="totalamt_edit" readonly>
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div style="width:76%"></div>
                                            <div class="form-label-col4">Previous Balance</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="prebalanceamt_edit" readonly>
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div style="width:76%"></div>
                                            <div class="form-label-col4">Amount Receivable</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="receivableamt_edit" readonly>
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col4">Amount (in words)</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="receivedamtword_edit" readonly>
                                            <div style="width:34%"></div>
                                            <div class="form-label-col4">Amount Received</div>
                                            <div class="form-colon1">:</div><span style="font-size:10px; color:red;" class="received errorss"></span>
                                            <input onchange="calcuation()" type="text" class="form-input4" id="receivedamt_edit">
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col4">Advance(in words)</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="advancedamtword_edit" readonly>
                                            <div style="width:34%"></div>
                                            <div class="form-label-col4">Advance</div>
                                            <div class="form-colon1">:</div>
                                            <input type="text" class="form-input4" id="advancedamt_edit" readonly>
                                        </div>

                                        <div class="form-row-line mb-2">
                                            <div style="width:76%"></div>
                                            <div class="form-label-col4">Prepared By</div>
                                            <div class="form-colon1">:</div><span style="font-size:10px; color:red;" class="prepared errorss"></span>
                                            <input type="text" class="form-input4" id="prepared_edit">
                                        </div>
                                        <div class="form-row-line mb-2">
                                            <div class="form-label-col" style="width:100%">Cancel Reason : <span style="font-size:10px; color:red;" class="reason errorss"></span>
                                                <textarea rows="4" class="form-input" style="border: 1px solid #e7eaee;"
                                                    id="cancelreason_edit"> </textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                    <div class="col-md-4">
                                        <label>Admin Sign</label>
                                        <div class="signature-option-group mb-2" data-target="editadmin">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="editadmin-signature" value="upload" id="adminUpload" checked>
                                            <label class="form-check-label" for="adminUpload">Upload Image</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="editadmin-signature" value="draw" id="adminDraw">
                                            <label class="form-check-label" for="adminDraw">Digital Sign</label>
                                        </div>
                                        </div>

                                        <div class="avatar-upload" id="editadmin-upload" style="margin: 0px;">
                                        <i class="fa fa-close clear-sign" data-target="editadmin" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                        <div class="avatar-edit" style="position: relative;">
                                            <input type="file" id="adminsignimge" accept=".png, .jpg, .jpeg" />
                                            <label for="adminsignimge"></label>
                                        </div>
                                        <div class="avatar-preview" style="width: 200px;">
                                            <div id="adminimgPreviewe"></div>
                                        </div>
                                        </div>

                                        <div class="digital-sign mt-2" id="editadmin-sign" style="display:none;">
                                        <canvas id="editadminCanvas" width="230" height="50" style="border:1px solid #000;"></canvas>
                                        <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="clearCanvas('editadminCanvas')">Clear</button>
                                        </div>
                                    </div>
                                     <div class="col-md-4 hide" id="zonal_sign_edit">
                                        <label>Zonal Sign</label>
                                        <div class="signature-option-group mb-2" data-target="editcc">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="editcc-signature" value="upload" id="ccUpload" checked>
                                            <label class="form-check-label" for="ccUpload">Upload Image</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="editcc-signature" value="draw" id="ccDraw">
                                            <label class="form-check-label" for="ccDraw">Digital Sign</label>
                                        </div>
                                        </div>

                                        <div class="avatar-upload" id="editcc-upload" style="margin: 0px;">
                                        <i class="fa fa-close clear-sign" data-target="editcc" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                        <div class="avatar-edit" style="position: relative;">
                                            <input type="file" id="ccsignimge" accept=".png, .jpg, .jpeg" />
                                            <label for="ccsignimge"></label>
                                        </div>
                                        <div class="avatar-preview" style="width: 200px;">
                                            <div id="ccimgPreviewe"></div>
                                        </div>
                                        </div>

                                        <div class="digital-sign mt-2" id="editcc-sign" style="display:none;">
                                        <canvas id="editccCanvas" width="230" height="50" style="border:1px solid #000;"></canvas>
                                        <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="clearCanvas('editccCanvas')">Clear</button>
                                        </div>
                                    </div>
                                    </div></div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                            id="close-button" class="btn btn-outline-danger"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" id="editcancelform"
                                            style="height: 34px;width: 133px;font-size: 12px;"
                                            class="btn btn-outline-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js">
            </script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js">
            </script>
            <script type="text/javascript">
            Dropzone.options.myDropzone = {
                acceptedFiles: "image/*", // Only accept image files (any image type)
                addRemoveLinks: true, // Optionally, show remove links for the file
                dictDefaultMessage: "Drag an image here or click to select one image"
            };
            var start = moment();
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

              function mydata(start, end) {

                $("#mydateviewsallsave").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                $("#mydateallviewssave").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

                // Check the selected date range and adjust the display accordingly
                if (start.isSame(end, 'day')) {
                    // If the start and end date are the same, show the single date
                    if (start.isSame(moment(), 'day')) {
                        $('#mysavereportrange span').html('Today');
                    } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                        $('#mysavereportrange span').html('Yesterday');
                    } else {
                        $('#mysavereportrange span').html(start.format('DD/MM/YYYY'));
                    }
                } else {
                    // For other ranges like "Last 7 Days", "This Month", etc.
                    $('#mysavereportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format(
                        'DD/MM/YYYY'));
                }
            }
            $('#mysavereportrange').daterangepicker({
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
            }, mydata);
            mydata(start, end);

            $(document).on('click', '.editbtn_user', function(e) {
                $('#exampleModaluser').modal('show');
            });
            $("#document_manage").css("color", "#080fd399");
            // Simulate fetching data
            const data = []; // Empty array means no data
            const tableBody = document.getElementById('table-body');
            const noDataMessage = document.getElementById('no-data');
            const prevButton = document.getElementById('prev-button');
            const nextButton = document.getElementById('next-button');
            </script>
            <!-- <script>
            $(document).ready(function() {

                $(document).on("input", ".searchInput", function() {
                    const searchText = $(this).val().toLowerCase().split(",").pop().trim();
                    const currentValues = $(this).val().split(",").map(v => v.trim());

                    $(this).siblings(".dropdown-options").find("div").each(function() {
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

                $(".dropdown input").on("focus", function() {
                    // Close all dropdowns first
                    $(".dropdown").removeClass("active");
                    // Then open the one that's focused
                    $(this).closest(".dropdown").addClass("active");
                });
            });




            $(document).on("click", ".dropdown-options div", function() {
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
            $(document).on("focus", ".searchInput", function() {
                const inputField = $(this);
                const currentValues = inputField.val().split(",").map(v => v.trim());

                inputField.siblings(".dropdown-options").find("div").each(function() {
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
            </script> -->




            <script>
                            
            $(document).ready(function () {

                /* ================================
                OPEN / CLOSE DROPDOWN
                ================================= */
                $(document).on('focus click', '.searchInput', function (e) {
                    e.stopPropagation();
                    $('.dropdown').not($(this).closest('.dropdown')).removeClass('active');
                    $(this).closest('.dropdown').addClass('active');
                });

                $(document).on('click', function () {
                    $('.dropdown').removeClass('active');
                });

                /* ================================
                OPTION CLICK (SINGLE + MULTI)
                ================================= */
                $(document).on('click', '.dropdown-options div', function (e) {
                    e.stopPropagation();
                    if ($(this).hasClass('no-results')) return;

                    const $dropdown = $(this).closest('.dropdown');
                    const $input = $dropdown.find('.searchInput');
                    const isSingle = $input.hasClass('single_search');
                    const text = $(this).text().trim();
                    const value = $(this).data('value');

                    if (isSingle) {
                        $input.val(text);
                        $dropdown.find('input[type="hidden"]').val(value || '');
                        $(this).siblings().removeClass('selected');
                        $(this).addClass('selected');
                        $dropdown.removeClass('active');
                        $input.trigger('change');
                        return;
                    }

                    // MULTI SELECT
                    let values = $input.val().split(',').map(v => v.trim()).filter(Boolean);

                    if (values.includes(text)) {
                        values = values.filter(v => v !== text);
                        $(this).removeClass('selected');
                    } else {
                        values.push(text);
                        $(this).addClass('selected');
                    }

                    $input.val(values.join(', '));
                    $input.trigger('change');
                });

                /* ================================
                SEARCH INSIDE DROPDOWN
                ================================= */
                $(document).on('input', '.searchInput', function () {
                    const search = $(this).val().split(',').pop().trim().toLowerCase();
                    const $options = $(this).siblings('.dropdown-options');

                    $options.find('div').not('.no-results').each(function () {
                        if (!$(this).hasClass('filtered-by-zone')) {
                            $(this).toggle($(this).text().toLowerCase().includes(search));
                        }
                    });

                    $options.find('.no-results').remove();
                    if ($options.find('div:visible').length === 0 && search) {
                        $options.append('<div class="no-results">No results found</div>');
                    }
                });

                /* ================================
                ZONE → BRANCH FILTER
                ================================= */
                $('#dis_zone_views').on('change', function () {
                    filterBranches(
                        $(this),
                        '#lic_zone_id',
                        '#dis_loc_views',
                        '#getlocation'
                    );
                    // applyFilters();
                });

                $('#save_zone_views').on('change', function () {
                    filterBranches(
                        $(this),
                        '#dissave_zone_id',
                        '#save_loc_views',
                        '#getlocationsave'
                    );
                });

                function filterBranches($zoneInput, hiddenZoneId, branchInput, branchList) {

                    const zoneNames = $zoneInput.val().split(',').map(v => v.trim()).filter(Boolean);
                    const zoneIds = [];

                    $zoneInput.siblings('.dropdown-options').find('div').each(function () {
                        if (zoneNames.includes($(this).text().trim())) {
                            zoneIds.push(String($(this).data('value')));
                        }
                    });

                    $(hiddenZoneId).val(zoneIds.join(','));

                    const $branches = $(branchList).find('div').not('.no-results');
                    let visible = 0;

                    $branches.each(function () {
                        const match = zoneIds.length === 0 || zoneIds.includes(String($(this).data('type')));
                        $(this).toggle(match).toggleClass('filtered-by-zone', !match);
                        if (match) visible++;
                    });

                    const $branchInput = $(branchInput);
                    const validBranches = $branches.filter(':visible').map(function () {
                        return $(this).text().trim();
                    }).get();

                    const selected = $branchInput.val().split(',').map(v => v.trim());
                    $branchInput.val(selected.filter(v => validBranches.includes(v)).join(', '));

                    $(branchList).find('.no-results').remove();
                    if (visible === 0) {
                        $(branchList).append('<div class="no-results">No branches available</div>');
                    }
                }

                /* ================================
                APPLY FILTER (AJAX / TABLE)
                ================================= */
                // function applyFilters() {
                //     console.log('Filter Applied:', {
                //         zone: $('#dis_zone_views').val(),
                //         branch: $('#dis_loc_views').val(),
                //         zone_id: $('#lic_zone_id').val()
                //     });

                //     // 👉 PLACE YOUR AJAX / TABLE RELOAD HERE
                // }

                /* ================================
                UX
                ================================= */
                $('.searchInput').attr('autocomplete', 'off');

            });

            $(document).ready(function() {

                $('.selectzone > div').off('click').on('click', function() {
                    const selectedType = $(this).data('value');
                    const selectedText = $(this).text();
                    $('#can_zone_views').val(selectedText);
                    $('#can_zone_id').val(selectedType);
                    $('#can_loc_views').val('');
                    $('#getlocation').hide();

                    $('#getlocation > div').removeClass('selected');

                    $('#getlocation > div')
                        .hide()
                        .filter(function() {
                            return Number($(this).data('type')) === Number(selectedType);
                        })
                        .show();
                });

                $('#can_zone_views').on('input', function() {
                    $('#can_zone_id').val('');
                    $('#getlocation > div').show();
                    $('#can_loc_views').val('');
                    $('#getlocation > div').removeClass('selected');
                });

                $('#can_loc_views').on('focus', function() {
                    const selectedType = Number($('#can_zone_id').val()); // use hidden ID

                    if (selectedType) {
                        $('#getlocation > div')
                            .hide()
                            .filter(function() {
                                return Number($(this).data('type')) === selectedType;
                            })
                            .show();
                    } else {
                        // $('#getlocation > div').show().removeClass('selected');
                    }

                    $('#getlocation').show();
                });
                $('#getlocation > div').off('click').on('click', function() {
                    const name = $(this).data('value');
                    $('#can_loc_views').val(name);

                    $('#getlocation > div').removeClass('selected');
                    $(this).addClass('selected');
                    $('#getlocation').hide();
                });

                $('input.searchInput').attr('autocomplete', 'off');
            });

               $(document).ready(function () {
                $('.selectzonesave > div').off('click').on('click', function () {
                    const selectedType = $(this).data('value');
                    const selectedText = $(this).text();
                    $('#save_zone_views').val(selectedText);
                    $('#dissave_zone_id').val(selectedType);
                    $('#save_loc_views').val('');
                    $('#getlocationsave').hide();

                    $('#getlocationsave > div').removeClass('selected');

                $('#getlocationsave > div')
                        .hide()
                        .filter(function () {
                            return Number($(this).data('type')) === Number(selectedType);
                        })
                        .show();
                });

            $('#save_zone_views').on('input', function () {
                    $('#dissave_zone_id').val('');
                    $('#getlocationsave > div').show();
                    $('#save_loc_views').val('');
                    $('#getlocationsave > div').removeClass('selected');
                });

                $('#save_loc_views').on('focus', function () {
                    const selectedType = Number($('#dissave_zone_id').val()); // use hidden ID

                    if (selectedType) {
                        $('#getlocationsave > div')
                            .hide()
                            .filter(function () {
                                return Number($(this).data('type')) === selectedType;
                            })
                            .show();
                    } else {
                        $('#getlocationsave > div').show().removeClass('selected');
                    }

                    $('#getlocationsave').show();
                });

                
            $('#getlocationsave > div').off('click').on('click', function () {
                    const name = $(this).data('value');
                    $('#save_loc_views').val(name);

                    $('#getlocationsave > div').removeClass('selected');
                    $(this).addClass('selected');
                    $('#getlocationsave').hide();
                });

                $('input.searchInput').attr('autocomplete', 'off');
            });

            const cancelbillformadded = "{{route('superadmin.cancelbill_added')}}";
            const cancelform_data = "{{route('superadmin.cancelform_data')}}";
            const cancelformsave_data="{{route('superadmin.cancelformsave_data')}}";
            const cancelbill_data="{{route('superadmin.cancelbill_data')}}";
            const cancelsave_data="{{route('superadmin.cancelsave_data')}}";
            const PUBLIC_BASE_URL = "{{ asset('') }}";
            window.admin_user = {
                        id: {{ $admin->id ?? 'null' }},
                        name: @json($admin->username ?? ''),
                        access_limits: @json($admin->access_limits ?? '')
                    };
            </script>

            <script>
            $(document).ready(function() {
                $('#expected_request').on('blur', function() {
                    const totalBill = parseFloat($('#totalbill').val()) || 0;
                    const rawValue = $(this).val().replace(/[^0-9.]/g, '');
                    const discountValue = parseFloat(rawValue) || 0;
                    const expercentage = $('#expercentage').is(':checked');
                    const examount = $('#examount').is(':checked');

                    let formattedInput = '';
                    let discountAmount = 0;
                    if (!expercentage && !examount) {
                        alert("Please select either ₹ or % for the expected discount.");
                        $(this).val('');
                        $('#ex_ref_value').text("");
                    }
                    if (expercentage) {
                        if (discountValue > 100) {
                            alert('Please enter a valid discount: percentage cannot exceed 100%.');
                            $(this).val('');
                            $('#ex_ref_value').text('');
                            return;
                        }
                        discountAmount = (totalBill * discountValue) / 100;
                        formattedInput = discountValue + '%';

                    } else if (examount) {
                        if (discountValue > totalBill) {
                            alert('Please enter a valid discount: amount cannot exceed total bill.');
                            $(this).val('');
                            $('#ex_ref_value').text('');
                            return;
                        }
                        discountAmount = discountValue;
                        formattedInput = discountValue + '₹';
                    }

                    $(this).val(formattedInput);
                    $('#ex_ref_value').text(discountAmount.toFixed(2) + '₹');
                });
                // When total bill input loses focus
                $('#totalbill').on('blur', function() {
                    let inputVal = $(this).val().trim();
                    let numericVal = inputVal.replace(/[^0-9.]/g, '');
                    if (!numericVal || isNaN(numericVal)) {
                        alert('Please enter a valid number for the total bill value.');
                        $(this).val('');
                        return;
                    }
                    const totalBill = parseFloat(numericVal);
                    const formattedInput = totalBill.toFixed(2) + '₹';

                    $(this).val(formattedInput);
                });

                // Final amount calculation
                function upFinalAmount() {
                    const totalBill = parseFloat($('#totalbill').val().replace(/[^0-9.]/g, '')) || 0;
                    const discountAmount = parseFloat($('#ex_ref_value').text().replace(/[^0-9.]/g, '')) || 0;

                    const finalAmount = discountAmount;
                    $('#final_amount').val(finalAmount.toFixed(2));
                }
                $('#totalbill').on('input', upFinalAmount);
                const observerfun = new MutationObserver(upFinalAmount);
                const discountElement = document.getElementById('ex_ref_value');
                if (discountElement) {
                    observerfun.observe(discountElement, {
                        childList: true,
                        characterData: true,
                        subtree: true
                    });
                }



                // When expected discount input loses focus
                $('#edittd_ref_expected_request').on('blur', function() {
                    const totalBill = parseFloat($('#edittd_ref_total_bill').val().replace(/[^0-9.]/g,
                        '')) || 0;
                    const rawValue = $(this).val().replace(/[^0-9.]/g, '');
                    const discountValue = parseFloat(rawValue) || 0;
                    const expercentage = $('#expercentageedit').is(':checked');
                    const examount = $('#examountedit').is(':checked');

                    let formattedInput = '';
                    let discountAmount = 0;

                    if (!expercentage && !examount) {
                        alert('Please select either ₹ or % for the expected discount.');
                        $(this).val('');
                        $('#ex_ref_value_edit').text('');
                        return;
                    }

                    if (expercentage) {
                        if (discountValue > 100) {
                            alert('Please enter a valid discount: percentage cannot exceed 100%.');
                            $(this).val('');
                            $('#ex_ref_value_edit').text('');
                            return;
                        }
                        discountAmount = (totalBill * discountValue) / 100;
                        formattedInput = discountValue + '%';
                    } else if (examount) {
                        if (discountValue > totalBill) {
                            alert('Please enter a valid discount: amount cannot exceed total bill.');
                            $(this).val('');
                            $('#ex_ref_value_edit').text('');
                            return;
                        }
                        discountAmount = discountValue;
                        formattedInput = discountValue + '₹';
                    }
                    $(this).val(formattedInput);
                    $('#ex_ref_value_edit').text(discountAmount.toFixed(2));
                });

                // When total bill input loses focus
                $('#edittd_ref_total_bill').on('blur', function() {
                    let inputVal = $(this).val().trim();
                    let numericVal = inputVal.replace(/[^0-9.]/g, '');
                    if (!numericVal || isNaN(numericVal)) {
                        alert('Please enter a valid number for the total bill value.');
                        $(this).val('');
                        return;
                    }
                    const totalBill = parseFloat(numericVal);
                    const formattedInput = totalBill.toFixed(2) + '₹';

                    $(this).val(formattedInput);
                });

                // Final amount calculation
                function updateFinalAmount() {
                    const totalBill = parseFloat($('#edittd_ref_total_bill').val().replace(/[^0-9.]/g, '')) ||
                    0;
                    const discountAmount = parseFloat($('#ex_ref_value_edit').text().replace(/[^0-9.]/g, '')) ||
                        0;
                    const finalAmount = discountAmount;
                    $('#edittd_ref_final_amt').val(finalAmount.toFixed(2));
                }
                $('#edittd_ref_total_bill').on('input', updateFinalAmount);
                const observer = new MutationObserver(updateFinalAmount);
                const discountEl = document.getElementById('ex_ref_value_edit');
                if (discountEl) {
                    observer.observe(discountEl, {
                        childList: true,
                        characterData: true,
                        subtree: true
                    });
                }
            });


            function readURLcc(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#ccimgPreview').css('background-image', 'url(' + e.target.result + ')');
                        $('#ccimgPreview').hide();
                        $('#ccimgPreview').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#ccsignimg").change(function() {
                readURLcc(this);
            });

            function readURLadmin(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#adminimgPreview').css('background-image', 'url(' + e.target.result + ')');
                        $('#adminimgPreview').hide();
                        $('#adminimgPreview').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#adminsignimg").change(function() {
                readURLadmin(this);
            });

            //edit
           function readURLccedit(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#ccimgPreviewe').css('background-image', 'url('+e.target.result +')');
                        $('#ccimgPreviewe').hide();
                        $('#ccimgPreviewe').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#ccsignimge").change(function() {
                readURLccedit(this);
            });

            function readURLadminedit(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#adminimgPreviewe').css('background-image', 'url('+e.target.result +')');
                        $('#adminimgPreviewe').hide();
                        $('#adminimgPreviewe').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#adminsignimge").change(function() {
                readURLadminedit(this);
            });

            // $('input[type="radio"]').change(function() {
            //     const type = $(this).val();
            //     const group = $(this).closest('.signature-option-group');
            //     const target = group.data('target');
            //     console.log("target",target);

            //     if (type === 'upload') {
            //         $(`#${target}-upload`).show();
            //         $(`#${target}-sign`).hide();
            //         const canvas = document.getElementById(`${target}Canvas`);
            //         if (canvas) {
            //             const ctx = canvas.getContext("2d");
            //             ctx.clearRect(0, 0, canvas.width, canvas.height);
            //         }
            //     } else {
            //         $(`#${target}-upload`).hide();
            //         $(`#${target}-sign`).show();
            //         $(`#${target}-upload input[type="file"]`).val('');
            //         $(`#${target}-upload .avatar-preview div`).css('background-image', 'none');

            //         // Setup canvas when it becomes visible
            //         // setupCanvas(`${target}Canvas`);
            //         // $(`#${target}-sign`).show();

            //         setTimeout(() => {
            //             setupCanvas(`${target}Canvas`);
            //         }, 50);

            //     }
            // });
            $('input[type="radio"]').change(function () {
                    const type = $(this).val();
                    const target = $(this).closest('.signature-option-group').data('target');

                    if (type === 'upload') {
                        $(`#${target}-upload`).show();
                        $(`#${target}-sign`).hide();
                        clearCanvas(`${target}Canvas`);
                    } else {
                        $(`#${target}-upload`).hide();
                        $(`#${target}-sign`).show();

                        setTimeout(() => {
                            setupCanvas(`${target}Canvas`);
                        }, 50);
                    }
                });


$(document).on('click', '.clear-sign', function () {
    const target = $(this).data('target');
    $(`#${target}-upload input[type="file"]`).val('');
    $(`#${target}-upload .avatar-preview div`).css('background-image', 'none');
});
// Digital signature drawing
function setupCanvas(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    // ❗ stop if canvas is still hidden
    const rect = canvas.getBoundingClientRect();
    if (rect.width === 0 || rect.height === 0) return;

    if (canvas.dataset.initialized === "true") return;

    const ctx = canvas.getContext("2d");
    let drawing = false;

    const ratio = window.devicePixelRatio || 1;
    canvas.width = rect.width * ratio;
    canvas.height = rect.height * ratio;
    ctx.scale(ratio, ratio);

    ctx.lineWidth = 2;
    ctx.lineCap = "round";
    ctx.lineJoin = "round";
    ctx.strokeStyle = "#000";

    function getPos(e) {
        const r = canvas.getBoundingClientRect();
        return {
            x: (e.touches ? e.touches[0].clientX : e.clientX) - r.left,
            y: (e.touches ? e.touches[0].clientY : e.clientY) - r.top
        };
    }

    function start(e) {
        drawing = true;
        const p = getPos(e);
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
        e.preventDefault();
    }

    function draw(e) {
        if (!drawing) return;
        const p = getPos(e);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        e.preventDefault();
    }

    function stop() {
        drawing = false;
        ctx.beginPath();
    }

    canvas.addEventListener("mousedown", start);
    canvas.addEventListener("mousemove", draw);
    canvas.addEventListener("mouseup", stop);
    canvas.addEventListener("mouseleave", stop);

    canvas.addEventListener("touchstart", start, { passive: false });
    canvas.addEventListener("touchmove", draw, { passive: false });
    canvas.addEventListener("touchend", stop);

    canvas.dataset.initialized = "true";
}


function clearCanvas(canvasId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// ['ccCanvas', 'adminCanvas'].forEach(setupCanvas);
// ['editccCanvas', 'editadminCanvas'].forEach(setupCanvas);

            </script>
            <script>
            let rowCount = 0;

            function addRow() {
                rowCount++;
                const tbody = document.getElementById('product_detials').getElementsByTagName('tbody')[0];
                const row = tbody.insertRow();

                row.innerHTML = `
                        <td>${rowCount}</td>
                        <td><input type="text" class="tdinput" name="particulars_${rowCount}"></td>
                        <td><input type="number" class="tdinput qty" name="qty_${rowCount}" min="0" onchange="recalculateRow(this)"></td>
                        <td><input type="number" class="tdinput rate" name="rate_${rowCount}" min="0" onchange="recalculateRow(this)"></td>
                        <td><input type="number" class="tdinput tax" name="tax_${rowCount}" min="0" max="100" step="0.01" onchange="recalculateRow(this)"></td>
                        <td><input type="number" class="tdinput amount" name="amount_${rowCount}" readonly></td>
                        <td><button type="button" class="btn btn-outline-danger" style="border-radius: 7px;padding: 3px 7px;font-size: 10px;margin: 0px;" onclick="deleteRow(this)">X</button></td>
                        `;
                    }

            function recalculateRow(input) {
                const row = input.closest("tr");
                const qty = parseFloat(row.querySelector(".qty")?.value) || 0;
                const rate = parseFloat(row.querySelector(".rate")?.value) || 0;
                const tax = parseFloat(row.querySelector(".tax")?.value) || 0;
                if (tax > 100) {
                    alert('Please enter a valid discount: percentage cannot exceed 100%.');
                }
                const base = qty * rate;
                const amount = base + (base * tax / 100);
                row.querySelector(".amount").value = amount.toFixed(2);
                recalculateTotals();
            }

            function deleteRow(button) {
                const row = button.closest("tr");
                row.remove();
                updateSerialNumbers();
                recalculateTotals();
            }

            function updateSerialNumbers() {
                const rows = document.querySelectorAll("#product_detials tbody tr");
                rowCount = 0;
                rows.forEach((row, index) => {
                    rowCount = index + 1;
                    row.cells[0].textContent = rowCount;
                });
            }

            function recalculateTotals() {
                let total = 0;
                document.querySelectorAll(".amount").forEach(input => {
                    total += parseFloat(input.value) || 0;
                });

                total = parseFloat(total.toFixed(2));
                document.getElementById("totalamt").value = total;
                const prev = parseFloat(document.getElementById("prebalanceamt").value) || 0;
                const received = parseFloat(document.getElementById("receivedamt").value) || 0;
                const receivable = parseFloat((total + prev).toFixed(2));
                const advance = parseFloat((receivable - received).toFixed(2));
                // let advance = 0;

                // const isEqual = (a, b) => Math.abs(a - b) < 0.01;

                // if (isEqual(received, total)) {
                //     advance = prev;
                // } else if (isEqual(received, receivable)) {
                //     advance = 0;
                // } else if (received > receivable) {
                //     advance = received - receivable;
                // } else {
                //     advance = 0; // Handle partial payments
                // }

                // if (total >= 5000) {
                //     $('#zonalsign').removeClass('hide');
                // } else {
                //     $('#zonalsign').addClass('hide');
                // }
                document.getElementById("receivableamt").value = receivable.toFixed(2);
                document.getElementById("advancedamt").value = advance.toFixed(2);
                document.getElementById("receivedamtword").value = numberToWords(received);
                document.getElementById("advancedamtword").value = numberToWords(advance);
            }

            ["prebalanceamt", "receivedamt"].forEach(id => {
                document.getElementById(id).addEventListener("input", recalculateTotals);
            });

            window.onload = addRow;

            // Number to words utility (basic for 0–9999)
         function numberToWords(n) {
    let isNegative = n < 0;
    n = Math.floor(Math.abs(n)); // Use absolute value for conversion

    const ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
    const teens = ["Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
    const tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

    if (n === 0) return "Zero only";

    const getWords = (num) => {
        let str = "";

        if (num >= 100) {
            str += ones[Math.floor(num / 100)] + " Hundred ";
            num %= 100;
        }

        if (num >= 20) {
            str += tens[Math.floor(num / 10)] + " ";
            num %= 10;
        } else if (num >= 10) {
            str += teens[num - 10] + " ";
            num = 0;
        }

        if (num > 0) {
            str += ones[num] + " ";
        }

        return str;
    };

    let result = "";

    const crore = Math.floor(n / 10000000);
    if (crore > 0) {
        result += getWords(crore) + "Crore ";
        n %= 10000000;
    }

    const lakh = Math.floor(n / 100000);
    if (lakh > 0) {
        result += getWords(lakh) + "Lakh ";
        n %= 100000;
    }

    const thousand = Math.floor(n / 1000);
    if (thousand > 0) {
        result += getWords(thousand) + "Thousand ";
        n %= 1000;
    }

    const hundred = Math.floor(n / 100);
    if (hundred > 0) {
        result += getWords(hundred * 100); // handles "X Hundred"
        n %= 100;
    }

    result += getWords(n);

    result = result.trim();
    // return (isNegative ? "Minus " : "") + result + " only";
    return (isNegative ? "" : "") + result + " only";
}
function numberToWordsWithRupees(n) {
    return "Rupees " + numberToWords(n);
}
            $('#receivedamt_edit').on('input', function() {
                let amount = parseFloat($(this).val());
                if (!isNaN(amount)) {
                    $('#receivedamtword_edit').val(numberToWords(Math.floor(amount)));
                } else {
                    $('#receivedamtword_edit').val('');
                }
            });

            function calcuation() {
            let receivable = parseFloat($('#receivableamt_edit').val()) || 0;
            let received = parseFloat($('#receivedamt_edit').val()) || 0;
            let balance = receivable - received;

            $('#advancedamt_edit').val(balance.toFixed(2));

            if (!isNaN(balance)) {
                $('#advancedamtword_edit').val(numberToWords(Math.floor(balance)));
            } else {
                $('#advancedamtword_edit').val('');
            }
        }
        $('#receivedamt_edit').on('keyup change', calcuation);

        // $(document).on('click', '.btn-approve, .btn-reject', function (e) {
        //     e.preventDefault();
        //     e.stopPropagation();

        //     let status = $(this).data('status'); // 1 = approve, 2 = reject
        //     let id = $(this).closest('.approver-action').data('id');

        //     let actionText = status == 1 ? 'Approve' : 'Reject';
        //     let actionColor = status == 1 ? '#2e7d32' : '#c62828';

        //     Swal.fire({
        //         title: `Confirm ${actionText}?`,
        //         text: `Are you sure you want to ${actionText.toLowerCase()} this bill?`,
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: actionColor,
        //         cancelButtonColor: '#6c757d',
        //         confirmButtonText: `Yes, ${actionText}`
        //     }).then((result) => {

        //         if (result.isConfirmed) {

        //             $.ajax({
        //                 url: "{{ route('superadmin.approve_reject') }}",
        //                 type: "POST",
        //                 data: {
        //                     id: id,
        //                     status: status,
        //                     _token: $('meta[name="csrf-token"]').attr('content')
        //                 },
        //                 success: function (res) {
        //                     Swal.fire(
        //                         'Success!',
        //                         res.message,
        //                         'success'
        //                     );
        //                     setTimeout(function() {
        //                         location.reload();
        //                     }, 500);
        //                 },
        //                 error: function () {
        //                     Swal.fire(
        //                         'Error!',
        //                         'Something went wrong',
        //                         'error'
        //                     );
        //                 }
        //             });

        //         }
        //     });
        // });


$(document).on('click', '.approve-btn, .reject-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $btn = $(this);
    const status = $btn.data('status');
    const id = $btn.data('id');
    const $row = $btn.closest('tr');

    const actionText = status == 1 ? 'Approve' : 'Reject';
    const actionColor = status == 1 ? '#2e7d32' : '#c62828';

    Swal.fire({
        title: `Confirm ${actionText}?`,
        text: `Are you sure you want to ${actionText.toLowerCase()} this discount?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: actionColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${actionText}`,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            
            // Show loading state
            $row.addClass('updating-row');
            $btn.css('opacity', '0.5');

            $.ajax({
                url: "{{ route('superadmin.approve_reject') }}",
                type: "POST",
                data: {
                    id: id,
                    status: status,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: res.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update the specific row
                        updateTableRow($row, res.record, status);
                        
                        // Update statistics cards
                        if (res.statistics) {
                            renderStatisticsCards(res.statistics);
                        }
                        
                        // Update count badges
                        if (res.counts) {
                            updateCountBadges(res.counts);
                        }

                        // Remove loading state
                        $row.removeClass('updating-row');

                    } else {
                        Swal.fire('Error!', res.message || 'Update failed', 'error');
                        $row.removeClass('updating-row');
                        $btn.css('opacity', '1');
                    }
                },
                error: function (xhr) {
                    let errorMsg = 'Something went wrong';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    Swal.fire('Error!', errorMsg, 'error');
                    $row.removeClass('updating-row');
                    $btn.css('opacity', '1');
                }
            });
        }
    });
});
            </script>

            <!-- [ Main Content ] end -->
            @include('superadmin.superadminfooter')
            <script src="{{ asset('/assets/document/cancelbillform.js') }}"></script>
</body>

</html>