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
        width: 59%;
        height: 590px;
        /* object-fit:cover; */
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
        scrollbar-color: #efdaec #f1f1f1;
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
        word-wrap: break-word;
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
        border: 2px solid #b163a6;
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

    .btn-primary.d-inline-flex:hover {
        background-color: rgb(255, 255, 255) !important;
        /* Change to your desired hover color */
        border-color: #4b4fc5 !important;
        color: #6a6ee4;
    }

    .sidebarviewsss {
        width: 474px;
        background: #353772;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 15px;
        gap: 12px;
        height: 100vh;
        overflow-y: auto;
        border-radius: 10px 0 0 10px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
    }

    .pdf-itemnnnssss {
        background: white;
        border: none;
        color: #353772;
        font-size: 14px;
        cursor: pointer;
        padding: 12px;
        width: 95%;
        text-align: left;
        border-radius: 5px;
        font-weight: bold;
        transition: all 0.3s ease;

        display: flex;
        flex-direction: column;
        text-align: center;
        word-wrap: break-word;
    }

    .pdf-itemnnnssss:hover,
    .activesssss {
        background: #ec008a;
        color: white;
        transform: scale(1.05);
    }

    .viewer-container {
        flex-grow: 1;
        height: 100vh;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        position: relative;
        padding: 20px;
    }

    iframe {
        width: 100%;
        height: 100%;
        border: none;
        position: absolute;
    }

    #document_camp {
        color: #ec008c;
    }

    .expanded-row {
        background-color: #7d80cf99;
        /* light green */
    }

    .collapsed-row {
        background-color: #7d80cf99;
        /* light grey */
    }


    /* for table css  */


    .tbl1 {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        /* overflow: scroll; */
    }

    .tbl1 th,
    .tbl1 td {
        /* border: 1px solid #ccc; */
        padding: 8px;
        word-wrap: break-word;
        text-align: left;
    }

    .tbl1 th {
        background-color: #f2f2f2;
    }

    .tbl1 th:nth-child(1),
    .tbl1 td:nth-child(1) {
        width: 10%;
    }

    .tbl1 th:nth-child(2),
    .tbl1 td:nth-child(2) {
        width: 10%;
    }

    .tbl1 th:nth-child(3),
    .tbl1 td:nth-child(3) {
        width: 10%;
    }

    .tbl1 th:nth-child(4),
    .tbl1 td:nth-child(4) {
        width: 10%;
    }

    .tbl1 th:nth-child(5),
    .tbl1 td:nth-child(5) {
        width: 10%;
    }

    .tbl1 th:nth-child(6),
    .tbl1 td:nth-child(6) {
        width: 10%;
    }

    .tbl1 th:nth-child(7),
    .tbl1 td:nth-child(7) {
        width: 10%;
    }

    .tbl1 th:nth-child(8),
    .tbl1 td:nth-child(8) {
        width: 10%;
    }

    .tbl1 th:nth-child(9),
    .tbl1 td:nth-child(9) {
        width: 10%;
    }

    .tbl1 th:nth-child(10),
    .tbl1 td:nth-child(10) {
        width: 5%;
    }

    .card-test {
        border-bottom: 3px solid #161413;
        margin-bottom: 50px !important;
        /* overflow: hidden; */
    }

    .btn-primary {
        padding: 5px 20px;
        border-radius: 5px;
        background: #3866cc;
        color: #ffffff;
        border: none;
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

            <div class="row">
                <div class="modal-content">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="form-label required" style="font-size: 12px;font-weight: 600;"><b
                                    style="font-size: larger;color: #0c599d;">Add new Camp Activities</b></label>
                        </div>

                        <div class="col-lg-6 text-end"> <a href="{{ url()->previous() }}" class="btn btn-primary">Return Back</a> </div>
                    </div>

                    <ul id="save_msgList"></ul>
                    <div id="error-message"></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    @php
                                        $locations = App\Models\TblLocationModel::select(
                                            'tbl_locations.name',
                                            'tbl_locations.id',
                                        )->get();
                                    @endphp
                                    <label class="form-label required"
                                        style="font-size: 12px; font-weight: 600;">Preferred
                                        Location:</label>&nbsp;&nbsp;
                                    <span style="font-size: 10px; color: red;" class="error_location errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            placeholder="Select Branch" style="color: #505050 !important;width: 100%;"
                                            required name="act_zone_id" id="act_zone_idssss" autocomplete="off">
                                        <div class="dropdown-options">
                                            @if ($locations)
                                                @foreach ($locations as $location)
                                                    <div class="dropdown-item-loc-act" data-value="{{ $location->id }}">
                                                        {{ $location->name }}
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Days:</label>&nbsp;&nbsp;<span
                                        style="font-size:10px; color:red;" class="error_days errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            name="days" id="act_days" placeholder="---Select---">
                                        <div class="dropdown-options">
                                            <div data-value="Day 1">Day 1</div>
                                            <div data-value="Day 2">Day 2</div>
                                            <div data-value="Day 3">Day 3</div>
                                            <div data-value="Day 4">Day 4</div>
                                            <div data-value="Day 5">Day 5</div>
                                            <div data-value="Day 6">Day 6</div>
                                            <div data-value="Day 7">Day 7</div>
                                            <div data-value="Day 8">Day 8</div>
                                            <div data-value="Day 9">Day 9</div>
                                            <div data-value="Day 10">Day 10</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php $camp_name =App\Models\Campmanagement::select('camp_management_system.Camp_Centre_Name','camp_management_system.id')->distinct()->get(); @endphp
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Camp
                                        Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_campname errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            id="camp_name" name="camp_name" placeholder="Select the Camp Name"
                                            autocomplete="off">
                                        <div class="dropdown-options single_search">
                                            @if ($camp_name)
                                                @foreach ($camp_name as $campname)
                                                    <div data-value="{{ $campname->id }}">
                                                        {{ $campname->Camp_Centre_Name }}</div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Budget:</label>&nbsp;&nbsp;<span
                                        style="font-size:10px; color:red;" class="error_budget errorss"></span>
                                    <input type="text" class="form-control" id="budget" name="budget"
                                        style="height: 30px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Budget">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Notes
                                        Image:[ Min 2 - Max 6
                                        ]</label>
                                    <span style="font-size:10px; color:red;"class="error_images errorss"></span>
                                    <input name="files[]" id="image_uploads_notes" type="file" class="form-control"
                                        style="padding: 5px;height: 30px;" multiple accept="image/*" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Banner Image:[ Min 2 - Max 6
                                        ]</label>
                                    <span style="font-size:10px; color:red;" class="error_images errorss"></span>
                                    <input name="files[]" id="image_uploads_banner" type="file"
                                        class="form-control" style="padding: 5px;height: 30px;" multiple
                                        accept="image/*" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Login
                                        time</label>
                                    <span style="font-size:10px; color:red;" class="error_login errorss"></span>
                                    <input type="time" id="login_time" class="form-control"
                                        style="height: 30px;" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Logout time</label>
                                    <span style="font-size:10px; color:red;" class="error_logout errorss"></span>
                                    <input type="time" id="logout" class="form-control"
                                        style="height: 30px;" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">location Track</label>
                                    <span style="font-size:10px; color:red;" class="error_track errorss"></span>
                                    <input type="text" class="form-control" id="location_track"
                                        name="location_track"
                                        style="height: 30px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Location Track">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Activity Description:
                                    </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_feedback errorss"></span>
                                    <textarea require class="form-control " id="activity_des" name="activity_des" rows="6"
                                        style="border-radius: 10px; border: solid 1px #d3d3d3; background-color: #fff; color: #505050 !important; width: 100%; padding: 6px;"
                                        placeholder="Enter Activity details here"></textarea>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="submit-new-activites"
                                style="height: 34px;width: 133px;font-size: 12px;"
                                class="btn btn-outline-primary">Submit</button>
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
                            <div
                                class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>Camp Filter</h5>
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
                                                    style="font-size: 12px;font-weight: 600;">Branch
                                                    Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                                    class="error_doctor errorss"></span>
                                                <div class="dropdown">
                                                    <input type="text"
                                                        class="searchInput multi_searchInput campfitters"
                                                        name="Branch" id="Branch_more" placeholder="Select Branch">
                                                    <div
                                                        class="dropdown-options options_multi options_marketers brachviewsall">
                                                        <div data-value="chennai">chennai</div>
                                                        <div data-value="Madurai">Madurai</div>
                                                        <div data-value="11811">11811</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-12"><br>
                                                <label class="form-label required"
                                                    style="font-size: 12px;font-weight: 600;">camp Type</label>
                                                <select class="mb-3 form-select campfitters" id="camp_type_more"
                                                    name="camp_type"
                                                    style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                    required name="empolyee_name">
                                                    <option value="">Camp Location</option>

                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <label class="form-label required"
                                                    style="font-size: 12px;font-weight: 600;">camp Incharge</label>
                                                <select class="mb-3 form-select campfitters" id="camp_incharge_more"
                                                    style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                    required name="camp_incharge	">
                                                    <option value="">Select city</option>

                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <label class="form-label required"
                                                    style="font-size: 12px;font-weight: 600;">Organized
                                                    By:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                                    class="error_doctor errorss"></span>
                                                <input type="text" class="form-control campfitters"
                                                    id="organized_by_more" name="organized_by"
                                                    style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                    placeholder="Doctor Name">
                                            </div>
                                        </div>


                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <br><label class="form-label required"
                                                    style="font-size: 12px;font-weight: 600;">doctor
                                                    Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                                    class="error_hplname errorss"></span>
                                                <input type="text" class="form-control campfitters"
                                                    id="doctor_name_more" name="doctor_name"
                                                    style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                    placeholder="Clinic / Hospital Name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Fixed Clear All Button -->
                                <div class="card-footer sticky-bottom bg-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                            data-bs-dismiss="offcanvas"
                                            class="btn btn-outline-danger w-50 me-2 mainclearallcamp">Clear All</a>
                                        <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                            id="morefitter_camp_search" data-bs-dismiss="offcanvas"
                                            class="btn btn-outline-primary w-50">Submit</a>
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
                            <div
                                class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                                <h5>Activites Filter</h5>
                                <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                    data-bs-dismiss="offcanvas" data-bs-target="#offcanvas_mail_filternew">
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
                                                    style="font-size: 12px;font-weight: 600;">Branch
                                                    Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                                    class="error_doctor errorss"></span>

                                                <div class="dropdown">
                                                    <input type="text"
                                                        class="searchInput multi_searchInput activityfitters"
                                                        name="camp_id" id="camp_id_more" placeholder="Select Branch">
                                                    <div
                                                        class="dropdown-options options_multi options_marketers brachviewsall">
                                                        <div data-value="chennai">chennai</div>
                                                        <div data-value="Madurai">Madurai</div>
                                                        <div data-value="11811">11811</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-12"><br>
                                                <label class="form-label required"
                                                    style="font-size: 12px;font-weight: 600;">Date Activites:</label>
                                                <input type="date" class="form-control activityfitters"
                                                    id="date_activites_more" name="date_activites"
                                                    style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                    placeholder="Clinic / Hospital Name">

                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-12">
                                                <br><label class="form-label required"
                                                    style="font-size: 12px;font-weight: 600;">Area Covered:</label>
                                                <input type="text" class="form-control activityfitters"
                                                    id="area_covered_more" name="area_covered"
                                                    style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                    placeholder="Area Covered">

                                            </div><br>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required"
                                                style="font-size: 12px;font-weight: 600;">Camp Type:</label>
                                            <input type="text" class="form-control activityfitters"
                                                id="camp_type_mores" name="camp_type"
                                                style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                placeholder="Camp Type">

                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- Fixed Clear All Button -->
                            <div class="card-footer sticky-bottom bg-white">
                                <div class="d-flex justify-content-between">
                                    <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                        data-bs-dismiss="offcanvas"
                                        class="btn btn-outline-danger w-50 me-2 mainclearallactivity">Clear All</a>
                                    <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                        id="activity_fitters_search" data-bs-dismiss="offcanvas"
                                        class="btn btn-outline-primary w-50">Submit</a>
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
                            <h5>Expenses Filter</h5>
                            <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                data-bs-dismiss="offcanvas" data-bs-target="#offcanvas_mail_filterpatient">
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
                                                style="font-size: 12px;font-weight: 600;">Branch:</label>&nbsp;&nbsp;<span
                                                style="font-size:10px; color:red;"
                                                class="error_doctor errorss"></span>

                                            <div class="dropdown">
                                                <input type="text"
                                                    class="searchInput multi_searchInput expensens_cls" name="Branch"
                                                    id="Branch_more_expenses" placeholder="Select Branch">
                                                <div
                                                    class="dropdown-options options_multi options_marketers brachviewsall">
                                                    <div data-value="chennai">chennai</div>
                                                    <div data-value="Madurai">Madurai</div>
                                                    <div data-value="11811">11811</div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>
                                            <label class="form-label required"
                                                style="font-size: 12px;font-weight: 600;">Activites:</label>
                                            <select class="mb-3 form-select expensens_cls"
                                                id="activites_more_expenses" name="activites"
                                                style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                required name="empolyee_name">
                                                <option value="">Marketer</option>
                                                <option value="R. Anusuya">R. Anusuya</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required"
                                                style="font-size: 12px;font-weight: 600;">Cost :</label>
                                            <input type="text" class="form-control expensens_cls"
                                                id="cost_more_expanses" name="cost"
                                                style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                placeholder="Clinic / Hospital Name">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                            <div class="d-flex justify-content-between">
                                <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                    data-bs-dismiss="offcanvas"
                                    class="btn btn-outline-danger w-50 me-2 mainclearall_patient">Clear All</a>
                                <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                    id="expensens_submit_search" data-bs-dismiss="offcanvas"
                                    class="btn btn-outline-primary w-50">Submit</a>
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
                            <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                data-bs-dismiss="offcanvas" data-bs-target="#offcanvas_mail_filternotes">
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
                                                <h4 class="alert-heading" style="font-size: 12px;">Employee Details
                                                </h4>
                                                <p style="font-size: 12px;" id="empname_views_all">Emp Name : R.
                                                    Anusuya</p>
                                                <h4 class="alert-heading" style="font-size: 12px;">Patient Details
                                                </h4>
                                                <p style="font-size: 12px;" id="wifenames">Wife Name : nnn</p>
                                                <p style="font-size: 12px; margin-top: -11px;" id="husbandnames">
                                                    Husband Name : cccc</p>
                                                <h4 class="alert-heading" style="font-size: 12px;">Feedback</h4>
                                                <p style="font-size: 12px;" id="notesfeedback">asdsadsa sdasdasd
                                                    sdasdsd asdsadsa sdasdasd sdasdsd asdsadsa
                                                    sdasdasd sdasdsd asdsadsa sdasdasd sdasdsd asdsadsa sdasdasd sdasdsd
                                                </p>
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
                            <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                data-bs-dismiss="offcanvas" data-bs-target="#offcanvas_mail_filterfeedback">
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
                                                <p style="font-size: 12px;" id="doctor_names_feed">Name :
                                                    Dr.Aravindivf</p>
                                                <h4 class="alert-heading" style="font-size: 12px;">Marketer Details
                                                </h4>
                                                <p style="font-size: 12px;" id="empname_views_all_feed">Emp Name : R.
                                                    Anusuya</p>
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
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#080fd399;height: 0px;">
                            <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">
                                Camp Management</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
                        <div class="modal-body">
                            <div class="row"><label class="form-label required"
                                    style="font-size: 12px;font-weight: 600;"><b
                                        style="font-size: larger;color: #0c599d;">Camp
                                        Details</b></label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                    class="error_hplname errorss"></span>
                                <div class="col-sm-3">

                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Branch :</label>&nbsp;&nbsp;<span
                                            style="font-size:10px; color:red;" class="error_doctor errorss"></span>
                                        <div class="dropdown">
                                            <input type="text"
                                                class="searchInput multi_searchInput marketervalues_search"
                                                name="Branch" id="Branch" placeholder="Select Branch">
                                            <div class="dropdown-options brachviewsall">
                                                <div data-value="chennai">chennai</div>
                                                <div data-value="Madurai">Madurai</div>
                                                <div data-value="11811">11811</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Camp
                                            Date:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_employee errorss"></span>
                                        <input type="datetime-local" class="form-control" id="Camp_Date"
                                            name="Camp_Date"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Camp Date">

                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Camp
                                            End:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_employee errorss"></span>
                                        <input type="datetime-local" class="form-control" id="Camp_enddate"
                                            name="Camp_enddate"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Camp Date">

                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Camp Centre
                                            Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_special errorss"></span>
                                        <input type="text" class="form-control" id="Camp_Centre_Name"
                                            name="Camp_Centre_Name"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Camp Centre Name">

                                    </div>
                                </div>


                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Camp Location
                                            :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_hplname errorss"></span>
                                        <input type="text" class="form-control" id="Camp_Location"
                                            name="Camp_Location"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Camp Location">
                                    </div>
                                </div>

                            </div>
                            <div class="row">

                                <label class="form-label required" style="font-size: 12px;font-weight: 600;"><b
                                        style="font-size: larger;color: #0c599d;">Digital Marketing
                                        Details</b></label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                    class="error_hplname errorss"></span>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Digital Marketing coordinator
                                            :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_hplname errorss"></span>
                                        <input type="text" class="form-control" id="Digital_Marketing_coordinator"
                                            name="Digital_Marketing_coordinator"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Digital Marketing coordinator">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Digital Marketing Cost
                                            :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_adress errorss"></span>
                                        <input type="text" class="form-control" id="Digital_Marketing_Cost"
                                            name="Digital_Marketing_Cost"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Digital Marketing Cost">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Days :</label>&nbsp;&nbsp;<span
                                            style="font-size:10px; color:red;" class="error_city errorss"></span>
                                        <input type="text" class="form-control" id="Digi_Days" name="Digi_Days"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Days">

                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Total Cost
                                            :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_city errorss"></span>
                                        <input type="text" class="form-control" id="Total_Cost" name="Total_Cost"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Total Cost">

                                    </div>
                                </div>
                            </div>


                            <div class="row">

                                <label class="form-label required" style="font-size: 12px;font-weight: 600;"><b
                                        style="font-size: larger;color: #0c599d;">Budget
                                        Details</b></label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                    class="error_hplname errorss"></span>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Budget For Auto
                                            :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_hplname errorss"></span>
                                        <input type="text" class="form-control" id="Budget_For_Auto"
                                            name="Budget_For_Auto"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Budget For Auto">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Auto
                                            Cost:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_adress errorss"></span>
                                        <input type="text" class="form-control" id="Auto_Cost" name="Auto_Cost"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Auto Cost">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Days :</label>&nbsp;&nbsp;<span
                                            style="font-size:10px; color:red;" class="error_city errorss"></span>
                                        <input type="text" class="form-control" id="Auto_Days" name="Auto_Days"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Days">

                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Total Cost
                                            :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_city errorss"></span>
                                        <input type="text" class="form-control" id="Auto_Total_Cost"
                                            name="Auto_Total_Cost"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Total Cost">

                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Budget For Snacks
                                            :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_hplname errorss"></span>
                                        <input type="text" class="form-control" id="Budget_For_Snacks"
                                            name="Budget_For_Snacks"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Budget For Snacks">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Snacks
                                            Cost:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_adress errorss"></span>
                                        <input type="text" class="form-control" id="Snacks_Cost"
                                            name="Snacks_Cost"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Snacks Cost">
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <label class="form-label required" style="font-size: 12px;font-weight: 600;"><b
                                        style="font-size: larger;color: #0c599d;">Activites
                                        Details</b></label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                    class="error_hplname errorss"></span>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Notices
                                            Image:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_hplname errorss"></span>
                                        <input name="files[]" id="Notices_img" accept="image/*" type="file"
                                            style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;"
                                            multiple />
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Notices Cost
                                            :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_adress errorss"></span>
                                        <input type="text" class="form-control" id="Notices_Cost"
                                            name="Notices_Cost"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Notices Cost">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Notices
                                            Count:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_city errorss"></span>
                                        <input type="text" class="form-control" id="Notices_Count"
                                            name="Notices_Count"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Doctor Name">

                                    </div>
                                </div>

                                <div class="col-sm-3">
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Banner
                                            Image:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_city errorss"></span>
                                        <input name="files[]" id="Banner_img" accept="image/*" type="file"
                                            style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;"
                                            multiple />

                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Banner Cost
                                            :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_hplname errorss"></span>
                                        <input type="text" class="form-control" id="Banner_Cost"
                                            name="Banner_Cost"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Banner Cost">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Banner
                                            Count:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_adress errorss"></span>
                                        <input type="text" class="form-control" id="Banner_Count"
                                            name="Banner_Count"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Banner Count">
                                    </div>
                                </div>

                                <label class="form-label required" style="font-size: 12px;font-weight: 600;"><b
                                        style="font-size: larger;color: #0c599d;">Coordinator
                                        Details</b></label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                    class="error_hplname errorss"></span>


                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Dr
                                            Attending:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_adress errorss"></span>
                                        <input type="text" class="form-control" id="Dr_attended"
                                            name="Dr_attended"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Dr Attending">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label required"
                                            style="font-size: 12px;font-weight: 600;">Camp
                                            Executives:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                            class="error_adress errorss"></span>
                                        <input type="text" class="form-control" id="Camp_Executives"
                                            name="Camp_Executives"
                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                            placeholder="Camp Executives">
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                    id="close-button" class="btn btn-outline-danger"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit-campdatas"
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
    <!-- Add meeting -->
    <div class="card-body pc-component btn-page">
        <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Add
                            Activites <span id="idsviews"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            style="background-color: #ffffff;" aria-label="Close"></button>
                    </div>
                    <ul id="save_msgList"></ul>
                    <div id="error-message"></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    @php
                                        $locations = App\Models\TblLocationModel::select(
                                            'tbl_locations.name',
                                            'tbl_locations.id',
                                        )->get();
                                    @endphp
                                    <label class="form-label required"
                                        style="font-size: 12px; font-weight: 600;">Preferred
                                        Location:</label>&nbsp;&nbsp;
                                    <span style="font-size: 10px; color: red;" class="error_location errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            placeholder="Select Branch" style="color: #505050 !important;width: 100%;"
                                            required name="act_zone_id" id="act_zone_idssss" autocomplete="off">
                                        <div class="dropdown-options">
                                            @if ($locations)
                                                @foreach ($locations as $location)
                                                    <div class="dropdown-item-loc-act"
                                                        data-value="{{ $location->id }}">
                                                        {{ $location->name }}
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Days:</label>&nbsp;&nbsp;<span
                                        style="font-size:10px; color:red;" class="error_days errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            name="days" id="act_days" placeholder="---Select---">
                                        <div class="dropdown-options">
                                            <div data-value="Day 1">Day 1</div>
                                            <div data-value="Day 2">Day 2</div>
                                            <div data-value="Day 3">Day 3</div>
                                            <div data-value="Day 4">Day 4</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php $camp_name =App\Models\Campmanagement::select('camp_management_system.Camp_Centre_Name')->distinct()->get(); @endphp
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Camp
                                        Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_campname errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            id="camp_name" name="camp_name" placeholder="Select the Camp Name"
                                            autocomplete="off">
                                        <div class="dropdown-options single_search">
                                            @if ($camp_name)
                                                @foreach ($camp_name as $campname)
                                                    <div data-value="{{ $campname->Camp_Centre_Name }}">
                                                        {{ $campname->Camp_Centre_Name }}</div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Budget:</label>&nbsp;&nbsp;<span
                                        style="font-size:10px; color:red;" class="error_budget errorss"></span>
                                    <input type="text" class="form-control" id="budget" name="budget"
                                        style="height: 30px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Budget">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Notes
                                        Image:[ Min 2 - Max 6
                                        ]</label>
                                    <span style="font-size:10px; color:red;"class="error_images errorss"></span>
                                    <input name="files[]" id="image_uploads_notes" type="file"
                                        class="form-control" style="padding: 5px;height: 30px;" multiple
                                        accept="image/*" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Banner Image:[ Min 2 - Max 6
                                        ]</label>
                                    <span style="font-size:10px; color:red;" class="error_images errorss"></span>
                                    <input name="files[]" id="image_uploads_banner" type="file"
                                        class="form-control" style="padding: 5px;height: 30px;" multiple
                                        accept="image/*" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Login
                                        time</label>
                                    <span style="font-size:10px; color:red;" class="error_login errorss"></span>
                                    <input type="time" id="login_time" class="form-control"
                                        style="height: 30px;" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Logout time</label>
                                    <span style="font-size:10px; color:red;" class="error_logout errorss"></span>
                                    <input type="time" id="logout" class="form-control"
                                        style="height: 30px;" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">location Track</label>
                                    <span style="font-size:10px; color:red;" class="error_track errorss"></span>
                                    <input type="text" class="form-control" id="location_track"
                                        name="location_track"
                                        style="height: 30px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Location Track">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Activity Description:
                                    </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_feedback errorss"></span>
                                    <textarea require class="form-control " id="activity_des" name="activity_des" rows="6"
                                        style="border-radius: 10px; border: solid 1px #d3d3d3; background-color: #fff; color: #505050 !important; width: 100%; padding: 6px;"
                                        placeholder="Enter Activity details here"></textarea>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                id="close-button" class="btn btn-outline-danger"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="submit-activites"
                                style="height: 34px;width: 133px;font-size: 12px;"
                                class="btn btn-outline-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- end meeting  -->

    <!-- Add patient -->
    <div class="card-body pc-component btn-page">
        <div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Add
                            Leads <span id="idsviews"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            style="background-color: #ffffff;" aria-label="Close"></button>
                    </div>
                    <ul id="save_msgList"></ul>
                    <div id="error-message"></div>
                    <div class="modal-body">
                        <div class="row">
                            @php $camp_name =App\Models\Campmanagement::select('camp_management_system.Camp_Centre_Name')->distinct()->get(); @endphp
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Camp
                                        Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_doctor errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            id="campa_name" name="campa_name" placeholder="Select the Camp Name"
                                            autocomplete="off">
                                        <div class="dropdown-options single_search">
                                            @if ($camp_name)
                                                @foreach ($camp_name as $campname)
                                                    <div data-value="{{ $campname->Camp_Centre_Name }}">
                                                        {{ $campname->Camp_Centre_Name }}</div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Wife
                                        Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_wife errorss"></span>
                                    <input type="text" class="form-control" id="wifename" name="wifename"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Wife Name">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Wife
                                        Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_wifeno errorss"></span>
                                    <input type="text" class="form-control" id="wifeno" name="wifeno"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Wife Number">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    @php
                                        $locations = App\Models\TblLocationModel::select(
                                            'tbl_locations.name',
                                            'tbl_locations.id',
                                        )->get();
                                    @endphp
                                    <label class="form-label required"
                                        style="font-size: 12px; font-weight: 600;">Preferred Location
                                        :</label>&nbsp;&nbsp;
                                    <span style="font-size: 10px; color: red;"
                                        class="error_location errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            placeholder="Select Branch"
                                            style="color: #505050 !important;width: 100%;" required name="zone_id"
                                            id="zone_id" autocomplete="off">
                                        <div class="dropdown-options">
                                            @if ($locations)
                                                @foreach ($locations as $location)
                                                    <div class="dropdown-item-loc"
                                                        data-value="{{ $location->id }}">
                                                        {{ $location->name }}
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Address:</label>&nbsp;&nbsp;<span
                                        style="font-size:10px; color:red;" class="error_address errorss"></span>
                                    <input type="text" class="form-control" id="address" name="address"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Address">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Husband
                                        Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_employee errorss"></span>
                                    <input type="text" class="form-control" id="husname" name="husname"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Husband Name">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Marriage
                                        at:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_special errorss"></span>
                                    <input type="date" class="form-control" id="marriageat"
                                        name="marriage_at"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Marriage At">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Married
                                        Years:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_special errorss"></span>
                                    <input type="text" class="form-control" id="marriedyear"
                                        name="marriage_at"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Married Years" readonly>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Husband
                                        Nubmer:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_employee errorss"></span>
                                    <input type="text" class="form-control" id="husno" name="husno"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Husband Number">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Husband
                                        Age:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_employee errorss"></span>
                                    <input type="text" class="form-control" id="husage" name="husage"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Husband Age">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Wife
                                        Age:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_employee errorss"></span>
                                    <input type="text" class="form-control" id="wifeage" name="wifeage"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="wife Age">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">City:</label>&nbsp;&nbsp;<span
                                        style="font-size:10px; color:red;" class="error_employee errorss"></span>
                                    <input type="text" class="form-control" id="city" name="city"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="City">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">State:</label>&nbsp;&nbsp;<span
                                        style="font-size:10px; color:red;" class="error_employee errorss"></span>
                                    <input type="text" class="form-control" id="state" name="state"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="State">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Email
                                        Address:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_employee errorss"></span>
                                    <input type="email" class="form-control" id="emailadd" name="emailadd"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Email Address">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Country:</label>&nbsp;&nbsp;<span
                                        style="font-size:10px; color:red;" class="error_employee errorss"></span>
                                    <input type="text" class="form-control" id="country" name="country"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="State">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Wife MRD
                                        No:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_employee errorss"></span>
                                    <input type="text" class="form-control" id="wifemrd" name="wifemrd"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Wife MRD No">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Husband MRD
                                        No:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_employee errorss"></span>
                                    <input type="text" class="form-control" id="husmrd" name="husmrd"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Husband MRD No">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Zip
                                        Code:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_hplname errorss"></span>
                                    <input type="text" class="form-control" id="zipcode" name="zipcode"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Zip Code">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required">Profile Group:</label>&nbsp;
                                    <span style="font-size:10px; color:red;" class="error_shift errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            name="profile_grp" id="profile_grp" placeholder="---Select---">
                                        <div class="dropdown-options">
                                            <div data-value="Consulting">Consulting</div>
                                            <div data-value="Infertility Tests">Infertility Tests</div>
                                            <div data-value="Natural">Natural</div>
                                            <div data-value="IUI">IUI</div>
                                            <div data-value="ICSI">ICSI</div>
                                            <div data-value="Consulting">IVF</div>
                                            <div data-value="Consulting">Surrogacy</div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required">For Fertility:</label>&nbsp;
                                    <span style="font-size:10px; color:red;" class="error_shift errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            name="for_fertility" id="for_fertility" placeholder="---Select---">
                                        <div class="dropdown-options">
                                            <div data-value="New">New</div>
                                            <div data-value="Old">Old</div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required">Prefered time to call1:</label>&nbsp;
                                    <span style="font-size:10px; color:red;" class="error_shift errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            name="prefered_call" id="prefered_call" placeholder="---Select---">
                                        <div class="dropdown-options">
                                            <div data-value="7am to 11am">7am to 11am</div>
                                            <div data-value="11am to 3pm">11am to 3pm</div>
                                            <div data-value="3pm to 7pm">3pm to 7pm</div>
                                            <div data-value="7pm to 11pm">7pm to 11pm</div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required">Prefered language:</label>&nbsp;
                                    <span style="font-size:10px; color:red;" class="error_shift errorss"></span>
                                    <div class="dropdown">
                                        <input type="text" class="form-control searchInput single_search"
                                            name="prefered_lan" id="prefered_lan" placeholder="---Select---">
                                        <div class="dropdown-options">
                                            <div data-value="Tamil">Tamil</div>
                                            <div data-value="English">English</div>
                                            <div data-value="kannada">kannada</div>
                                            <div data-value="Hindi">Hindi</div>
                                            <div data-value="Telugu">Telugu</div>
                                            <div data-value="Malayalam">Malayalam</div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Walk in
                                        Date:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                        class="error_special errorss"></span>
                                    <input type="date" class="form-control" id="walkin_date"
                                        name="walkin_date"
                                        style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                        placeholder="Marriage At">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label required"
                                        style="font-size: 12px;font-weight: 600;">Description:</label>&nbsp;&nbsp;<span
                                        style="font-size:10px; color:red;" class="error_special errorss"></span>
                                    <textarea require class="form-control " id="camp_desc" name="camp_desc" rows="6"
                                        style="border-radius: 10px; border: solid 1px #d3d3d3; background-color: #fff; color: #505050 !important; width: 100%; padding: 6px;"
                                        placeholder="Enter feedback details here"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                id="close-button" class="btn btn-outline-danger"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="submit-expenses-data"
                                style="height: 34px;width: 133px;font-size: 12px;"
                                class="btn btn-outline-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- end patient  -->
    <div class="offcanvas pc-announcement-offcanvas offcanvas-start ecom-offcanvas" tabindex="-1"
        id="announcement">
        <div class="offcanvas-body p-0">
            <div id="ecom-filter" class="collapse collapse-horizontal show">
                <div class="ecom-filter">
                    <div class="card">
                        <!-- Sticky Header -->
                        <div
                            class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                            <h5>Doctor details Edit : #<span id="uesrids"></span></h5>
                            <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                                data-bs-dismiss="offcanvas" data-bs-target="#announcement">
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
                                                style="font-size: 12px;font-weight: 600;">Doctor
                                                Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"
                                                class="error_doctor errorss"></span>
                                            <input type="text" class="form-control editsall"
                                                id="doctorname_edits" name="doctor_name"
                                                style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                placeholder="Doctor Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>
                                            <label class="form-label required"
                                                style="font-size: 12px;font-weight: 600;">Marketer Name:</label>
                                            <select class="mb-3 form-select editsall" id="emp_name"
                                                name="empolyee_name"
                                                style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                required name="empolyee_name">
                                                <option value="">Marketer</option>
                                                <option value="R. Anusuya">R. Anusuya</option>
                                                <option value="G. Aswin">G. Aswin</option>
                                                <option value="V.Soundarya">V.Soundarya</option>
                                                <option value="E.Dhanalakshmi">E.Dhanalakshmi</option>
                                                <option value="T.Balaji">T.Balaji</option>
                                                <option value="Saravanan M D">Saravanan M D</option>
                                                <option value="Soundararajan P">Soundararajan P</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required"
                                                style="font-size: 12px;font-weight: 600;">Contact:</label>&nbsp;&nbsp;<span
                                                style="font-size:10px; color:red;"
                                                class="error_hplname errorss"></span>
                                            <input type="text" class="form-control editsall" id="contactviews"
                                                name="hpl_contact"
                                                style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                placeholder="Clinic / Hospital Name">
                                        </div><br>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required"
                                                style="font-size: 12px;font-weight: 600;">city:</label>
                                            <select class="mb-3 form-select editsall" id="citys"
                                                style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                required name="city">
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
                                            <label class="form-label required"
                                                style="font-size: 12px;font-weight: 600;">Address:</label>&nbsp;&nbsp;<span
                                                style="font-size:10px; color:red;"
                                                class="error_hplname errorss"></span>
                                            <input type="text" class="form-control editsall" id="addressviews"
                                                name="address"
                                                style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                placeholder="Clinic / Hospital Name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fixed Clear All Button -->
                            <div class="card-footer sticky-bottom bg-white">
                                <div class="d-flex justify-content-between">
                                    <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                        data-bs-dismiss="offcanvas" class="btn btn-outline-danger w-50 me-2 ">Clear
                                        All</a>
                                    <a href="#" style="height: 34px;width: 133px; font-size: 12px;"
                                        data-bs-dismiss="offcanvas"
                                        class="btn btn-outline-primary w-50 editsoveralls">Submit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="noteimagepopup" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                    <h5 class="modal-title" style="color: #ffffff;font-size: 12px;">Notes Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        style="background-color: #ffffff;" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-12">
                                <img src="" id="main_notes" style="width:100%; height:auto;">
                                <div id="thumbnails_notes" style="margin-top: 10px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bannerimagepopup" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                    <h5 class="modal-title" style="color: #ffffff;font-size: 12px;">Banner Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        style="background-color: #ffffff;" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-12">
                                <img src="" id="main_banner" style="width:100%; height:auto;">
                                <div id="thumbnails_banner" style="margin-top: 10px;"></div>
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
                        <div class="modal-header" style="background-color: #353772;height: 0px;">
                            <h5 class="modal-title" id="exampleModalLabel"
                                style="color: #ffffff;font-size: 12px;">Doctor Details : #<span
                                    id="doctor_ids"></span> - <span id="Doctornamehead"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-12">
                                    <img src="" id="main">
                                    <div id="thumbnails">
                                        <!-- <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png">
                                          <img src="../assets/images/gallery-grid/1722403363_IMG_20240731_105245156.png">
                                           <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png">
                                           <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png"> -->
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
            <div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #353772;height: 0px;">
                            <h5 class="modal-title" id="exampleModalLabel"
                                style="color: #ffffff;font-size: 12px;">Doctor Details : #<span
                                    id="doctor_ids"></span> - <span id="Doctornamehead"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <div class="row">

                            <div class="col-sm-12">
                                <div class="mb-12">
                                    <img src="" id="main">
                                    <div id="thumbnails">
                                        <!-- <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png">
                                          <img src="../assets/images/gallery-grid/1722403363_IMG_20240731_105245156.png">
                                           <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png">
                                           <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png"> -->
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="announcement"
        aria-labelledby="announcementLabel">
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
                    <img src="../assets/images/layout/img-announcement-1.png" alt="img"
                        class="img-fluid mb-3" />
                    <div class="row">
                        <div class="col-12">
                            <div class="d-grid"><a class="btn btn-outline-secondary"
                                    href="https://1.envato.market/zNkqj6" target="_blank">Check Now</a></div>
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
                    <a href="https://1.envato.market/zNkqj6" target="_blank"><img
                            src="../assets/images/layout/img-announcement-2.png" alt="img"
                            class="img-fluid" /></a>
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
                    <p class="text-muted">Do you know Able Pro is one of the featured dashboard template selected by
                        Themeforest team.?</p>
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
    <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
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
                $('#myreportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
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
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')]
            }
        }, my);
        my(start, end);

        function mycamp(start, end) {

            $("#mydateviewsallcamp").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            $("#datecampfitters").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

            // Check the selected date range and adjust the display accordingly
            if (start.isSame(end, 'day')) {
                // If the start and end date are the same, show the single date
                if (start.isSame(moment(), 'day')) {
                    $('#reportrangecamp span').html('Today');
                } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                    $('#reportrangecamp span').html('Yesterday');
                } else {
                    $('#reportrangecamp span').html(start.format('DD/MM/YYYY'));
                }
            } else {
                // For other ranges like "Last 7 Days", "This Month", etc.
                $('#reportrangecamp span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            }
        }

        $('#reportrangecamp').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')]
            }
        }, mycamp);
        mycamp(start, end);


        $(document).on('click', '.addpatient', function(e) {
            $('#exampleModal3').modal('show');
        });
        $(document).on('click', '.addmeeting', function(e) {
            $('#exampleModal2').modal('show');
        });
        $(document).on('click', '.editbtn', function(e) {
            $('#exampleModal').modal('show');
        });
        $("#document_manage").css("color", "#080fd399");
        // Simulate fetching data
        const data = []; // Empty array means no data
        const tableBody = document.getElementById('table-body');
        const noDataMessage = document.getElementById('no-data');
        const prevButton = document.getElementById('prev-button');
        const nextButton = document.getElementById('next-button');
    </script>
    <script>
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
    </script>
    <script type="text/javascript">
        $(".nav-link").click(function() {
            targetTab = $(this).data("bs-target");
            if (targetTab == "#analytics-tab-1-pane") {
                $(".add-meeting").hide();
                $(".add-patient").hide();
                $(".add-doctors").show();
            }
            if (targetTab == "#analytics-tab-2-pane") {
                $(".add-meeting").show();
                $(".add-patient").hide();
                $(".add-doctors").hide();
            }
            if (targetTab == "#analytics-tab-3-pane") {
                $(".add-meeting").hide();
                $(".add-patient").show();
                $(".add-doctors").hide();
            }
        });

        const leadsadddata = "{{ route('superadmin.leadsadddata') }}";
        const leadsadataUrl = "{{ route('superadmin.leadsdata') }}";
        const activitedatasave = "{{ route('superadmin.activitedatasave') }}";
        const activitydataUrl = "{{ route('superadmin.activitydata') }}";
    </script>

    <script>
        document.getElementById("marriageat").addEventListener("change", function() {
            const marriageDate = new Date(this.value);
            const today = new Date();

            if (!isNaN(marriageDate)) {
                let years = today.getFullYear() - marriageDate.getFullYear();
                const m = today.getMonth() - marriageDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < marriageDate.getDate())) {
                    years--;
                }
                document.getElementById("marriedyear").value = years >= 0 ? years : 0;
            } else {
                document.getElementById("marriedyear").value = '';
            }
        });
    </script>

    <!-- Activity -->
    <script>
        function mydata(start, end) {
            $("#mydateviewsallsave").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            $("#campactivitesdate").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            if (start.isSame(end, 'day')) {
                if (start.isSame(moment(), 'day')) {
                    $('#mysavereportrange span').html('Today');
                } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                    $('#mysavereportrange span').html('Yesterday');
                } else {
                    $('#mysavereportrange span').html(start.format('DD/MM/YYYY'));
                }
            } else {
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
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')]
            }
        }, mydata);
        mydata(start, end);



        $(document).ready(function() {
            $('.selectzone > div').off('click').on('click', function() {
                const selectedType = $(this).data('value');
                const selectedText = $(this).text();
                $('#sec_zone_views').val(selectedText);
                $('#sec_zone_id').val(selectedType);
                $('#sec_loc_views').val('');
                $('#getlocation').hide();

                $('#getlocation > div').removeClass('selected');

                $('#getlocation > div')
                    .hide()
                    .filter(function() {
                        return Number($(this).data('type')) === Number(selectedType);
                    })
                    .show();
            });

            $('#sec_zone_views').on('input', function() {
                $('#sec_zone_id').val('');
                $('#getlocation > div').show();
                $('#sec_loc_views').val('');
                $('#getlocation > div').removeClass('selected');
            });

            $('#sec_loc_views').on('focus', function() {
                const selectedType = Number($('#sec_zone_id').val()); // use hidden ID

                if (selectedType) {
                    $('#getlocation > div')
                        .hide()
                        .filter(function() {
                            return Number($(this).data('type')) === selectedType;
                        })
                        .show();
                } else {
                    $('#getlocation > div').show().removeClass('selected');
                }

                $('#getlocation').show();
            });
            $('#getlocation > div').off('click').on('click', function() {
                const name = $(this).data('value');
                $('#lic_loc_views').val(name);

                $('#getlocation > div').removeClass('selected');
                $(this).addClass('selected');
                $('#getlocation').hide();
            });

            $('input.searchInput').attr('autocomplete', 'off');
        });

        $(document).ready(function() {
            $('.selectzone_act > div').off('click').on('click', function() {
                const selectedType = $(this).data('value');
                const selectedText = $(this).text();
                $('#act_zone_views').val(selectedText);
                $('#act_zone_id').val(selectedType);
                $('#act_loc_views').val('');
                $('#getlocation_act').hide();

                $('#getlocation_act > div').removeClass('selected');

                $('#getlocation_act > div')
                    .hide()
                    .filter(function() {
                        return Number($(this).data('type')) === Number(selectedType);
                    })
                    .show();
            });

            $('#act_zone_views').on('input', function() {
                $('#act_zone_id').val('');
                $('#getlocation_act > div').show();
                $('#act_loc_views').val('');
                $('#getlocation_act > div').removeClass('selected');
            });

            $('#act_loc_views').on('focus', function() {
                const selectedType = Number($('#act_zone_id').val()); // use hidden ID

                if (selectedType) {
                    $('#getlocation_act > div')
                        .hide()
                        .filter(function() {
                            return Number($(this).data('type')) === selectedType;
                        })
                        .show();
                } else {
                    $('#getlocation_act > div').show().removeClass('selected');
                }

                $('#getlocation_act').show();
            });
            $('#getlocation_act > div').off('click').on('click', function() {
                const name = $(this).data('value');
                $('#act_loc_views').val(name);
                $('#getlocation_act > div').removeClass('selected');
                $(this).addClass('selected');
                $('#getlocation_act').hide();
            });
            $('input.searchInput').attr('autocomplete', 'off');
        });

        $(document).ready(function() {
            $('.selectzone_camp > div').off('click').on('click', function() {
                const selectedType = $(this).data('value');
                const selectedText = $(this).text();
                $('#zoneviews').val(selectedText);
                $('#camp_zone_id').val(selectedType);
                $('#branchviews').val('');
                $('#getlocation_act').hide();

                $('#getlocation_act > div').removeClass('selected');

                $('#getlocation_act > div')
                    .hide()
                    .filter(function() {
                        return Number($(this).data('type')) === Number(selectedType);
                    })
                    .show();
            });

            $('#zoneviews').on('input', function() {
                $('#camp_zone_id').val('');
                $('#getlocation_camp > div').show();
                $('#branchviews').val('');
                $('#getlocation_camp > div').removeClass('selected');
            });

            $('#branchviews').on('focus', function() {
                const selectedType = Number($('#camp_zone_id').val()); // use hidden ID

                if (selectedType) {
                    $('#getlocation_camp > div')
                        .hide()
                        .filter(function() {
                            return Number($(this).data('type')) === selectedType;
                        })
                        .show();
                } else {
                    $('#getlocation_camp > div').show().removeClass('selected');
                }

                $('#getlocation_camp').show();
            });
            $('#getlocation_camp > div').off('click').on('click', function() {
                const name = $(this).data('value');
                $('#branchviews').val(name);

                $('#getlocation_camp > div').removeClass('selected');
                $(this).addClass('selected');
                $('#getlocation_camp').hide();
            });

            $('input.searchInput').attr('autocomplete', 'off');
        });
    </script>



    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')
</body>
<!-- [Body] end -->

</html>
