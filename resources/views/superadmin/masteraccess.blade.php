<!doctype html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Access Master</title>

    @include('superadmin.superadminhead')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />



<style>

/* Shared Styles */

%shared {

    box-shadow: 2px 2px 10px 5px #b8b8b8;

    border-radius: 10px;

}



/* Table Styles */

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

    background: #6a6ee4;

    border-radius: 4px;

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



.thview, .tdview {

    padding: 15px;

    text-align: left;

    border-bottom: 11px solid #ddd;

}



/* Loading Animation */

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



@keyframes loading-wave-animation {

    0% { height: 10px; }

    50% { height: 50px; }

    100% { height: 10px; }

}



/* Button Styles */

.btn-primary.d-inline-flex:hover {

    background-color: rgb(255, 255, 255) !important;

    border-color: #4b4fc5 !important;

    color: #6a6ee4;

}



/* Card Styles */

.stat-card {

    transition: transform 0.3s ease-in-out;

    border: 1px solid #c3bfc3;

    border-radius: 8px;

}



.stat-card:hover {

    transform: translateY(-5px);

}



/* Dropdown Styles */

.dropdown {

    position: relative;

    width: 100%;

}



/* Dropdown Input Field */

.dropdown input {

    width: 100%;

    padding: 8px 12px;

    border: 1px solid #ddd;

    border-radius: 4px;

    font-size: 14px;

    cursor: pointer;

    background-color: white;

}



/* Dropdown Options Panel */

.dropdown-options {

    position: absolute;

    top: 100%;

    left: 0;

    width: 100%;

    max-height: 300px;

    /* overflow-y: auto; */

    border: 1px solid #ddd;

    background: #fff;

    display: none;

    z-index: 1000;

    box-shadow: 0 4px 8px rgba(0,0,0,0.1);

    border-radius: 4px;

    padding: 5px;

}



.dropdown.active .dropdown-options {

    display: block;

}



/* Search Input Inside Dropdown */

.dropdown-search {

    width: 100%;

    padding: 8px;

    margin-bottom: 5px;

    border: 1px solid #ddd;

    border-radius: 4px;

    font-size: 14px;

    background-color: white;

}



/* Options Container */

.options-container {

    max-height: 250px;

    overflow-y: auto;

}



/* Individual Dropdown Options */

.dropdown-options div {

    padding: 8px 12px;

    cursor: pointer;

    font-size: 14px;

    border-bottom: 1px solid #f0f0f0;

    color: #333;

    background-color: white;

    margin: 2px 0;

    border-radius: 3px;

}



.dropdown-options div:hover {

    background-color: #6a6ee4;

    color: white;

}



.dropdown:hover,

.filter-card:hover,

.searchInput:hover {

    background-color: white;

    border-color: #ddd;

}



/* Ensure no other elements change color on hover */

.filter-card:hover {

    background-color: inherit !important;

    border-color: #e0e0e0 !important;

}



.dropdown:hover {

    background-color: inherit !important;

}



.searchInput:hover {

    background-color: inherit !important;

}



.searchInput:not(:placeholder-shown) {

    border-color: #6a6ee4;

    background-color: #f8f9ff;

}



.dropdown.active .searchInput {

    border-color: #6a6ee4;

    box-shadow: 0 0 0 2px rgba(106, 110, 228, 0.2);

}



.dropdown-search:focus {

    border-color: #6a6ee4;

    outline: none;

}



.filter-card {

    border: 1px solid #e0e0e0;

}



/* Footer & Pagination */

.footer {

    margin-top: 15px;

    width: 100%;

}



.pagination-controls {

    display: flex;

    align-items: center;

    gap: 15px;

    flex-wrap: wrap;

}



.items-per-page {

    display: flex;

    align-items: center;

    gap: 5px;

    font-size: 12px;

}



#access-items-per-page {

    width: 60px;

    height: 24px;

    font-size: 12px;

    padding: 0 5px;

}



.pagination {

    display: flex;

    gap: 3px;

    margin-left: auto;

}



.pagination button {

    min-width: 24px;

    height: 24px;

    padding: 0 5px;

    font-size: 11px;

    background: #f8f8f8;

    border: 1px solid #ddd;

    cursor: pointer;

    border-radius: 3px;

    display: flex;

    align-items: center;

    justify-content: center;

}



.pagination button:hover {

    background: #eaeaea;

}



.pagination button.active {

    background: #6a6ee4;

    color: #fff;

    border-color: #6a6ee4;

}



/* Header Styles */

.page-header {

    margin-bottom: 20px;

}



.page-header h1 {

    font-size: 24px;

    font-weight: 600;

    color: #333;

}

#dashboard_color {
    color: #6a6ee4;
}

#locationDropdown {
    display: none;
    z-index: 1050;
}

#locationDropdown.show {
    display: block;
}

.location-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.tick {
    font-size: 16px;
    font-weight: bold;
}

.dropdown-menu {
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.375rem;
    background-color: white;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
}

.bg-light {
    background-color: #f8f9fa !important;
}
#locationDropdown {
    display: none;
    position: absolute;
    z-index: 1060 !important; /* higher than modal */
}

#locationDropdown.show {
    display: block;
}

</style>





</head>

<body style="overflow-x: hidden;">

    <div class="page-loader">

        <div class="bar"></div>

    </div>



    @include('superadmin.superadminnav')

    @include('superadmin.superadminheader')



  <div class="pc-container">

        <div class="pc-content">

            <div class="card-body border-bottom pb-0">

                <div class="d-flex align-items-center justify-content-between">

                    <h1>Access Master</h1>

                </div>

                <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">

                    <li class="nav-item" role="presentation">

                        <button class="nav-link active" id="analytics-tab-1" data-bs-toggle="tab"

                            data-bs-target="#analytics-tab-1-pane" type="button" role="tab"

                            aria-controls="analytics-tab-1-pane" aria-selected="true">Employee Access</button>

                    </li>

                </ul>

            </div>



            <div class="container" style="margin-top: 20px;">

                <div class="row g-4">

                    <div class="col-md-3 col-sm-3">

                        <div class="stat-card text-center p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">

                            <h3 class="fs-5 fw-bold" id="total_access_count">0</h3>

                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Total User</p>

                        </div>

                    </div>
                    <div class="col-md-3 col-sm-3">

                        <div class="stat-card text-center p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">

                            <h3 class="fs-5 fw-bold" id="total_active_count">0</h3>

                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Active User</p>

                        </div>

                    </div>
                    <div class="col-md-3 col-sm-3">

                        <div class="stat-card text-center p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">

                            <h3 class="fs-5 fw-bold" id="total_inactive_count">0</h3>

                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Inactive User</p>

                        </div>

                    </div>

                </div><br>

            </div>



 <div class="row">

    <div class="col-xl-3 col-md-3">

        <div class="card">

            <div class="dropdown">

                <input type="text" class="searchInput access-values-search" name="role_name" id="role-views" placeholder="Select Designation" autocomplete="off" value="">

                <div class="dropdown-options options-roles">

                    <input type="text" class="dropdown-search" placeholder="Search designation..." data-target="roles">

                    <div class="options-container" id="roles-options">

                        <!-- Roles will be populated via JS -->

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-xl-3 col-md-3">

        <div class="card">

            <div class="dropdown">

                <input type="text" class="searchInput access-values-search" name="zone_name" id="zone-views" placeholder="Select Zone" autocomplete="off" value="">

                <div class="dropdown-options options-zones">

                    <input type="text" class="dropdown-search" placeholder="Search zone..." data-target="zones">

                    <div class="options-container" id="zones-options">

                        <!-- Zones will be populated via JS -->

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-xl-3 col-md-3">

        <div class="card">

            <div class="dropdown">

                <input type="text" class="searchInput access-values-search" name="branch_name" id="branch-views" placeholder="Select Branch" autocomplete="off">

                <div class="dropdown-options options-branches">

                    <input type="text" class="dropdown-search" placeholder="Search branch..." data-target="branches">

                    <div class="options-container" id="branches-options">

                        <!-- Branches will be populated via JS -->

                    </div>

                </div>

            </div>

        </div>

    </div>





<div class="col-xl-3 col-md-3">

    <div class="card">

        <div class="dropdown">

            <!-- Added autocomplete="new-password" and readonly attribute -->

            <input type="text"

                   class="searchInput access-values-search"

                   name="employee_name"

                   id="name-views"

                   placeholder="Select Employee"

                   autocomplete="new-password"

                   readonly

                   onfocus="this.removeAttribute('readonly')">

            <div class="dropdown-options options-names">

                <input type="text"

                       class="dropdown-search"

                       placeholder="Search employee..."

                       data-target="names"

                       autocomplete="off">

                <div class="options-container" id="names-options">

                    <!-- Names will be populated via JS -->

                </div>

            </div>

        </div>

    </div>

</div>

<div class="col-xl-3 col-md-3">
  <div class="card">
    <div class="dropdown">
      <form autocomplete="off">
        <input type="text"
               name="emp_num_manual"
               id="employee_num"
               autocomplete="new-field"
               placeholder="Search anything...">
      </form>
    </div>
  </div>
</div>







</div>





<p style="margin-top: -9px;" class="text-muted f-12 mb-0">

    <span class="text-truncate w-100"><span id="access-count">0</span> Employees</span>

    <div class="filter-values-container" style="display: inline-block;"></div>

    <span style="cursor: pointer;display:none;" class="badge bg-danger clear-all-access-views">Clear all</span>

</p><br>





            <div class="col-sm-12">

                <div class="card-body">

                    <div class="table-container">

                        <table class="tbl">

                            <thead class="thd">

                                <tr class="trview">

                                    <th class="thview">Employee ID</th>

                                    <th class="thview">Full Name</th>

                                    <th class="thview">Designation</th>

                                    <th class="thview">Branch</th>

                                    <th class="thview">Zone</th>

                                    <th class="thview">Status</th>

                                    <th class="thview">Modified Status</th>

                                    <th class="thview">Permission</th>

                                    <th class="thview">View</th>

                                </tr>

                            </thead>

                            <tbody id="employee_details1">

                                <tr>

                                    <td colspan="6">

                                        <div class="loading-wave">

                                            <div class="loading-bar"></div>

                                            <div class="loading-bar"></div>

                                            <div class="loading-bar"></div>

                                            <div class="loading-bar"></div>

                                        </div>

                                    </td>

                                </tr>

                            </tbody>

                            <tbody id="employee_details" style="display:none;"></tbody>

                        </table>

                    </div>

<div class="footer">

    <div class="pagination-controls">

        <div class="items-per-page">

            <span>Show</span>

            <select id="access-items-per-page" class="form-control">

                <option>10</option>

                <option>25</option>

                <option>50</option>

                <option>100</option>

            </select>

            <span>entries</span>

        </div>

        <div class="pagination" id="access-pagination"></div>

    </div>

</div>

                </div>

            </div>

        </div>

    </div>



    <!-- View Access Modal -->

<div class="modal fade" id="access-view-modal" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-xl" role="document">

        <div class="modal-content">

            <div class="modal-header" style="background-color: #6a6ee4;height: 40px;">

                <h5 class="modal-title text-white">Employee Details #<span id="view-access-id"></span></h5>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6">

                        <div class="card mb-3">

                            <div class="card-header py-2" style="background-color: #f8f9fa;">

                                <h6 class="mb-0">Basic Information</h6>

                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6">

                                        <p class="mb-2"><strong>Employee ID:</strong> <span id="view-employee-id"></span></p>

                                        <p class="mb-2"><strong>Name:</strong> <span id="view-employee-name"></span></p>

                                        <p class="mb-2"><strong>Email:</strong> <span id="view-employee-email"></span></p>
                                        <p class="mb-2"><strong>Created:</strong> <span id="view-employee-created"></span></p>
                                        <p class="mb-2"><strong>Modified Time :</strong> <span id="view-employee-modified-time"></span></p>

                                    </div>

                                    <div class="col-md-6">

                                        <p class="mb-2"><strong>Designation:</strong> <span id="view-role"></span></p>

                                        <p class="mb-2"><strong>Branch:</strong> <span id="view-branch"></span></p>

                                        <p class="mb-2"><strong>Zone:</strong> <span id="view-zone"></span></p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="card mb-3">

                            <div class="card-header py-2" style="background-color: #f8f9fa;">

                                <h6 class="mb-0">Reporting Structure</h6>

                            </div>

                            <div class="card-body">

                                <p class="mb-2"><strong>Reporting Manager:</strong> <span id="view-reporting-manager"></span></p>

                                <p class="mb-2"><strong>Zonal Head:</strong> <span id="view-zonal-head"></span></p>

                            </div>

                        </div>

                    </div>

                </div>



                <div class="card">

                    <div class="card-header py-2" style="background-color: #f8f9fa;">

                        <h6 class="mb-0">Menu Permissions</h6>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-sm table-bordered">

                                <thead class="table-light">

                                    <tr>

                                        <th>Menu</th>

                                        <th>Submenu</th>

                                        <th>Status</th>

                                    </tr>

                                </thead>

                                <tbody id="view-permissions-table">

                                    <!-- Will be populated by JavaScript -->

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer py-2">

                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>





    <!-- Permission Modal -->

     <div class="modal fade" id="permission-modal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header py-2" style="background-color: #6a6ee4;">

                <h6 class="modal-title text-white">Manage Permissions #<span id="permission-employee-id"></span></h6>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>



            <div class="modal-body py-2 px-3">

                <div class="row g-2">

                    <div class="col-md-6">

                        <div class="form-group mb-2">

                            <label class="form-label small mb-1">Employee ID</label>

                            <input type="text" class="form-control form-control-sm" id="perm-employee-id" readonly>

                        </div>

                        <div class="form-group mb-2">

                            <label class="form-label small mb-1">Employee Name</label>

                            <input type="text" class="form-control form-control-sm" id="perm-employee-name" readonly>

                        </div>

                        <div class="form-group mb-2">

                            <label class="form-label small mb-1">Email</label>

                            <input type="email" class="form-control form-control-sm" id="perm-email">

                        </div>

                        <div class="form-group mb-2">

                            <label class="form-label small mb-1">Password</label>

                            <input type="password" class="form-control form-control-sm" id="perm-password" placeholder="Leave blank to keep current password">

                        </div>

                    </div>



                    <div class="col-md-6">

                        <div class="form-group mb-2">

                            <label class="form-label small mb-1">Branch</label>

                            <input type="hidden" id="perm-branch-id" value="">

                            <input type="text" class="form-control form-control-sm" id="perm-branch" readonly>

                        </div>

                        <div class="form-group mb-2">

                            <label class="form-label small mb-1">Zone</label>

                            <input type="hidden" id="perm-zone-id" value="">

                            <input type="text" class="form-control form-control-sm" id="perm-zone" readonly>

                        </div>

                        <div class="form-group mb-2">

                            <label class="form-label small mb-1">Reporting Manager</label>

                            <select class="form-control form-control-sm" id="perm-reporting-manager" name="reporting_manager">

                                <option value="">Select Reporting Manager</option>

                                <!-- dynamically filled -->

                            </select>

                        </div>

                        <div class="form-group mb-2">

                            <label class="form-label small mb-1">Zonal Head</label>

                            <select class="form-control form-control-sm" id="perm-zonal-head">

                                <option value="0">No</option>

                                <option value="1">Yes</option>

                            </select>

                        </div>

                    </div>

                </div>


                <div class="row g-2">

                    <div class="form-group col-md-6 mt-2">

                        <label class="form-label small mb-1">Role</label>

                        <select class="form-control form-control-sm" id="perm-role">

                            <option value="1">Superadmin</option>

                            <option value="2">Zonal Admin</option>

                            <option value="3">Admin</option>

                            <option value="4">Auditor</option>

                            <option value="5">User</option>


                        </select>

                    </div>
                    <div class="form-group col-md-6 mb-2">
                        <label class="form-label small mb-1">Multi-Location</label>

                        <div class="dropdown w-100 position-relative">
                            <input type="text"
                                class="form-control form-control-sm"
                                id="multi-location"
                                placeholder="Select locations"
                                aria-haspopup="true"
                                aria-expanded="false">

                            <div class="dropdown-menu w-100 p-2" id="locationDropdown"
                                style="max-height:300px; overflow-y:auto; position: absolute; top: 100%; left: 0; right: 0;">
                                <!-- Search input -->
                                <div class="mb-2">
                                    <input type="text"
                                        class="form-control form-control-sm"
                                        id="locationSearch"
                                        placeholder="Search locations...">
                                </div>

                                <div id="locationList">
                                    @foreach ($locations as $location)
                                        <div class="location-item d-flex align-items-center mb-1 p-2 rounded"
                                            data-id="{{ $location->id }}"
                                            data-name="{{ $location->name }}"
                                            style="cursor: pointer;"
                                            onclick="toggleLocation(this)">

                                            <div class="location-name flex-grow-1">
                                                {{ $location->name }}
                                            </div>

                                            <div class="tick ms-2 text-success" style="display: none;">
                                                ✔
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="location_ids" id="location_ids">
                </div>



                <div class="table-responsive mt-2">

                    <table class="table table-bordered table-sm">

                        <thead class="table-light">

                            <tr class="small">

                                <th>Menu</th>

                                <th>Submenu</th>

                                <th>Enable/Disable </br>
                                    <label>
                                        <input type="checkbox" id="selectAllMenus">
                                         Select All
                                    </label>
                                </th>

                            </tr>

                        </thead>

                        <tbody id="permission-table-body">

                            <!-- JS will fill -->

                        </tbody>

                    </table>

                </div>

            </div>



            <div class="modal-footer py-2 px-3">

                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>

                <button type="button" class="btn btn-sm btn-primary" id="save-permissions">Save Changes</button>

            </div>

        </div>

    </div>

</div>
<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Change User Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="statusChangeForm">
          <div class="mb-3">
            <label class="form-label">Status:</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="active_status" id="statusActive" value="0">
              <label class="form-check-label" for="statusActive">Active</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="active_status" id="statusInactive" value="1">
              <label class="form-check-label" for="statusInactive">Inactive</label>
            </div>
          </div>

          <div class="mb-3">
            <label for="statusDate" class="form-label">Effective Date:</label>
            <input type="date" class="form-control" id="statusDate" name="status_date" required>
          </div>
        </form>

        <div class="text-end">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmStatusChange">Change</button>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
    const updateStatusUrl = "{{ route('update.status') }}";
</script>




    @include('superadmin.superadminfooter')



    <!-- Include the separate JS file -->

    <script src="{{ asset('/assets/js/masteraccess.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


</body>

</html>