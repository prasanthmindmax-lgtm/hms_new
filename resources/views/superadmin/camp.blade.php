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
%shared{
  box-shadow:2px 2px 10px 5px #b8b8b8;
  border-radius:10px;
}
#thumbnails{
  text-align:center;
  img{
    width:100px;
    height:100px;
    margin:10px;
    cursor:pointer;
      @media only screen and (max-width:480px){
    width:50px;
    height:50px;
  }
    @extend %shared;
    &:hover{
      transform:scale(1.05)
    }
  }
}
#main{
  width:50%;
  height:400px;
  object-fit:cover;
  display:block;
  margin:20px auto;
  @extend %shared;
  @media only screen and (max-width:480px){
    width:100%;
  }
}
.hidden{
  opacity:0;
}
.table-container {
    width: 104%;
    padding: 0px;
    font-size: 12px;
    position: relative;
    overflow-x: auto; /* Enable horizontal scrolling */
    overflow-y: auto; /* Enable vertical scrolling */
    max-height: 450px; /* Adjust as necessary */
}
/* Thin scrollbar for modern browsers */
.table-container::-webkit-scrollbar {
    width: 6px; /* Width of vertical scrollbar */
    height: 6px; /* Height of horizontal scrollbar */
}
.table-container::-webkit-scrollbar-thumb {
    background: #b163a6; /* Color of the scrollbar handle */
    border-radius: 4px; /* Rounded corners */
}
.table-container::-webkit-scrollbar-thumb:hover {
    background: #df64ce; /* Color when hovered */
}
.table-container::-webkit-scrollbar-track {
    background: #f1f1f1; /* Background of the scrollbar track */
}
/* For Firefox */
.table-container {
    scrollbar-width: thin; /* Thin scrollbar */
    scrollbar-color: #efdaec #f1f1f1; /* Handle color and track color */
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
    position: sticky; /* Keeps the header fixed within the container */
    top: -1px; /* Aligns it to the top of the container */
    z-index: 10; /* Ensures it stays above other elements */
    background: #f8f8f8; /* Prevent transparency during scrolling */
    box-shadow: 12px -1px 0px rgba(0, 0, 0, 0.1); /* Adds a subtle shadow for better visibility */
}
.thview, .tdview {
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
    background-color:rgb(255, 255, 255) !important; /* Change to your desired hover color */
    border-color: #4b4fc5 !important;
    color: #6a6ee4;
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
            <div class="col-md-3 col-sm-3 add-doctors">
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
            height: 34px;
         width: 133px;
         font-size: 12px;
                 background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4;
     "><i class="ti ti-plus f-18"></i>New Camp</a></div>

<div class="col-md-3 col-sm-3 add-meeting">
    <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 addmeeting" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
height: 34px;
width: 133px;
font-size: 12px;
 background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
"><i class="ti ti-plus f-18"></i>Activites</a></div>

<div class="col-md-3 col-sm-3 add-patient">
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 addpatient" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
     background-color: #6a6ee4;
    --bs-btn-border-color: #6a6ee4;
"><i class="ti ti-plus f-18"></i>Expenses</a></div>
          </div>
        </div>
      </div><br><br>
<!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="container" style="margin-top: -51px;">
    <div class="row g-4">
        <!-- Row 1 -->
        <div class="col-md-1 col-sm-3">
            <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                <h3 class="fs-5 fw-bold" id="total_camps">0</h3>
                <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Camp</p>
            </div>
        </div>
        <!-- Row 2 -->
        <div class="col-md-1 col-sm-3">
            <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                <h3 class="fs-5 fw-bold" id="">0</h3>
                <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Meetings</p>
            </div>
        </div>
        <!-- Row 3 -->
        <div class="col-md-1 col-sm-3">
            <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                <h3 class="fs-5 fw-bold" id="">0</h3>
                <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Patients</p>
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
                      class="nav-link active" id="analytics-tab-1" data-bs-toggle="tab" data-bs-target="#analytics-tab-1-pane" type="button"
                      role="tab"
                      aria-controls="analytics-tab-1-pane"
                      aria-selected="true">Camp</button>
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
                      >Activites</button
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
                      >Expenses</button
                    >
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
<div id="reportrangecamp" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span id="data_values"></span> <i class="fa fa-caret-down"></i>
</div>
</div>
</div>

                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search" name="Branch_name" id="branchviews" placeholder="Select Branch">
                                    <div class="dropdown-options options_marketers brachviewsall">
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
                                    <input type="text" class="searchInput marketervalues_search" name="zone_name" id="zoneviews" placeholder="Select Zone">
                                    <div class="dropdown-options options_marketers zoneviewsall" >
                                        <div data-value="11649">B.Henry Remgious</div>
                                        <div data-value="11461">S.Selvamurgan</div>
                                        <div data-value="11811">11811</div>
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
"><i class="ti ti-filter f-18"></i>&nbsp; More filter</a>
</div>&nbsp;&nbsp;&nbsp;&nbsp;
</div>

                </div>
                <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="">0</span> Rows for <span id="datecampfitters">Last 30 days</span></span>
<span class="search_camp_view" style="color: #e40505;font-size: 12px;font-weight: 600;cursor: pointer;">Search :</span>
<span style="cursor: pointer;" class="badge bg-success camp_views"></span>
<span style="cursor: pointer;" class="badge bg-success camp_views"></span>
<span style="cursor: pointer;" class="badge bg-success camp_views"></span>
<span style="cursor: pointer;" class="badge bg-success camp_views"></span>
<span style="cursor: pointer;" class="badge bg-success camp_views"></span>
<span  style="cursor: pointer;display:none;" class="badge bg-danger clear_camp_views" >Clear all</span>
<span style="cursor: pointer;" class="badge bg-success " style="display:none;"></span>
</p><br>
        <div class="col-sm-12">
              <div class="card-body">
              <div class="table-container">
        <table class="tbl">
            <thead class="thd">
                <tr class="trview">
                    <th class="thview">Camp ID</th>
                    <th class="thview">Branch Name</th>
                    <th class="thview">Location</th>
                    <th class="thview">organizer</th>
                    <th class="thview">Date</th>
                    <th class="thview">Doctor Name</th>
                </tr>
            </thead>
            <tbody id="camp_details">


            </tbody>
        </table>
    </div>
    <div class="footer">
        <div>
            Items per page:
            <select id="itemsPerPagecamp">
            <option>3</option>
            <option>5</option>
                <option>10</option>
                <option>15</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="pagination" id="paginationcamp"></div>
    </div>
              </div>
          </div>
                </div>
                <div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel" aria-labelledby="analytics-tab-2" tabindex="0">
                <div class="col-xl-12 col-md-12">
                <div class="row">
<div class="col-xl-2 col-md-2">
<div class="card">
<div id="reportrangeactivites" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span ></span> <i class="fa fa-caret-down "></i>
</div>
</div>
</div>
<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search_meeting" name="userfullname" id="meeting_mark" placeholder="Select Activites">
                                    <div class="dropdown-options options_meeting marketernameall">
                                        <div data-value="B.Henry Remgious">B.Henry Remgious</div>
                                        <div data-value="S.Selvamurgan">S.Selvamurgan</div>


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
<div class="">
<a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filternew"  style="
       height: 34px;
    width: 133px;
    font-size: 12px;
     background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4;
"><i class="ti ti-filter f-18"></i>&nbsp; More filter</a>
</div>&nbsp;&nbsp;&nbsp;&nbsp;
</div>

                </div>
                <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span >0</span> Rows for <span id="campactivitesdate">Last 30 days</span></span>
<span  style="color: #e40505;font-size: 12px;font-weight: 600;" class="search_activites">Search :</span>
<span style="cursor: pointer;" class="badge bg-success activites_views"></span>
<span style="cursor: pointer;" class="badge bg-success activites_views"></span>
<span style="cursor: pointer;" class="badge bg-success activites_views"></span>
<span style="cursor: pointer;" class="badge bg-success activites_views"></span>
<span style="cursor: pointer;" class="badge bg-success activites_views"></span>
<span style="cursor: pointer; display:none;"  class="badge bg-danger clear_activites_views" >Clear all</span>
<span style="cursor: pointer; display:none;" class="badge bg-success " ></span>
</p><br>
        <div class="col-sm-12">
              <div class="card-body">
              <div class="table-container">
        <table class="tbl">
            <thead class="thd">
                <tr class="trview">
                    <th class="thview"> ID & Date</th>
                    <th class="thview">Branch Name</th>
                    <th class="thview">Activites</th>
                    <th class="thview">Date</th>
                    <th class="thview">Images</th>
                    <th class="thview">Description</th>
                </tr>
            </thead>
            <tbody id="activites_details">

            </tbody>
        </table>
    </div>
    <div class="footer">
        <div>
            Items per page:
            <select id="itemsPerPageactivites">
            <option>3</option>
            <option>5</option>
                <option>10</option>
                <option>15</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="pagination" id="paginateactivites"></div>
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
<div id="reportrangeexpenses" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
</div>
</div>
</div>

<div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput marketervalues_search_patient" name="" id="patient_martketer" placeholder="Select Expenses">
                                    <div class="dropdown-options options_patient marketernameall">
                                        <div data-value="B.Henry Remgious">B.Henry Remgious</div>
                                        <div data-value="S.Selvamurgan">S.Selvamurgan</div>


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
<div class="">
<a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filterpatient"  style="
       height: 34px;
    width: 133px;
    font-size: 12px;
     background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4;
"><i class="ti ti-filter f-18"></i>&nbsp; More filter</a>
</div>&nbsp;&nbsp;&nbsp;&nbsp;
</div>

                </div>
                <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="counts">0</span> Rows for <span id="expensesdateviews">Last 30 days</span></span>
<span class="search_expensenssss" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
<span class="badge bg-success expenses_views"></span>
<span class="badge bg-success expenses_views"></span>
<span class="badge bg-success expenses_views"></span>
<span class="badge bg-success expenses_views"></span>
<span class="badge bg-success expenses_views"></span>
<span  class="badge bg-danger clear_expenses_views" style="display:none;" >Clear all</span>
<span class="badge bg-success " style="display:none;"></span>
</p><br>
        <div class="col-sm-12">
              <div class="card-body">
              <div class="table-container">
        <table class="tbl">
            <thead class="thd">
                <tr class="trview">
                    <th class="thview">ID & Date</th>
                    <th class="thview">Expenses</th>
                    <th class="thview">Activity</th>
                    <th class="thview">Total Cost</th>
                    <th class="thview">Purchase order</th>
                    <th class="thview">Creatives</th>
                    <th class="thview">Date</th>
                </tr>
            </thead>
            <tbody id="expenses_details">

            </tbody>
        </table>
    </div>
    <div class="footer">
        <div>
            Items per page:
            <select id="itemsPerPageexpenses">
            <option>3</option>
            <option>5</option>
                <option>10</option>
                <option>15</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="pagination" id="paginatexpenses"></div>
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
                        <h5>Camp Filter</h5>
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
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>
                                           <div class="dropdown">
                                                <input type="text" class="searchInput multi_searchInput campfitters" name="Branch" id="Branch_more" placeholder="Select Branch">
                                                <div class="dropdown-options options_multi options_marketers brachviewsall">
                                                    <div data-value="chennai">chennai</div>
                                                    <div data-value="Madurai">Madurai</div>
                                                    <div data-value="11811">11811</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">camp Type</label>
                                            <select class="mb-3 form-select campfitters" id="camp_type_more" name="camp_type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="empolyee_name">
                                            <option value="">Camp Location</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">camp Incharge</label>
                                            <select class="mb-3 form-select campfitters" id="camp_incharge_more"  style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="camp_incharge	">
                                            <option value="">Select city</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Organized By:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>
                                            <input type="text" class="form-control campfitters" id="organized_by_more"  name="organized_by" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Doctor Name">
                                        </div>
                                    </div>


                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <br><label class="form-label required" style="font-size: 12px;font-weight: 600;">doctor Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_hplname errorss"></span>
                                            <input type="text" class="form-control campfitters" id="doctor_name_more" name="doctor_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Clinic / Hospital Name">
                                        </div>
                                    </div>
                           </div>
                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas"  class="btn btn-outline-danger w-50 me-2 mainclearallcamp">Clear All</a>
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="morefitter_camp_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
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
                        <h5>Activites Filter</h5>
                        <a
                            href="#"
                            class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                            data-bs-dismiss="offcanvas"
                            data-bs-target="#offcanvas_mail_filternew"
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
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>

                                                <div class="dropdown">
                                                    <input type="text" class="searchInput multi_searchInput activityfitters" name="camp_id" id="camp_id_more" placeholder="Select Branch">
                                                    <div class="dropdown-options options_multi options_marketers brachviewsall">
                                                        <div data-value="chennai">chennai</div>
                                                        <div data-value="Madurai">Madurai</div>
                                                        <div data-value="11811">11811</div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Date Activites:</label>
                                            <input type="date" class="form-control activityfitters" id="date_activites_more" name="date_activites" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Clinic / Hospital Name">

                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <br><label class="form-label required" style="font-size: 12px;font-weight: 600;">Area Covered:</label>
                                            <input type="text" class="form-control activityfitters" id="area_covered_more" name="area_covered" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Area Covered">

                                </div><br>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Camp Type:</label>
                                            <input type="text" class="form-control activityfitters" id="camp_type_mores" name="camp_type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Camp Type">

                                        </div>
                                    </div>

                                </div>
                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas"  class="btn btn-outline-danger w-50 me-2 mainclearallactivity">Clear All</a>
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="activity_fitters_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
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
                        <a
                            href="#"
                            class="avtar avtar-s btn-link-danger btn-pc-default clsrmv"
                            data-bs-dismiss="offcanvas"
                            data-bs-target="#offcanvas_mail_filterpatient"
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
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>

                                                <div class="dropdown">
                                                    <input type="text" class="searchInput multi_searchInput expensens_cls" name="Branch" id="Branch_more_expenses" placeholder="Select Branch">
                                                    <div class="dropdown-options options_multi options_marketers brachviewsall">
                                                        <div data-value="chennai">chennai</div>
                                                        <div data-value="Madurai">Madurai</div>
                                                        <div data-value="11811">11811</div>
                                                    </div>
                                                </div>

                                            </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Activites:</label>
                                            <select class="mb-3 form-select expensens_cls" id="activites_more_expenses" name="activites" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="empolyee_name">
                                            <option value="">Marketer</option>
                                            <option value="R. Anusuya">R. Anusuya</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Cost :</label>
                                            <input type="text" class="form-control expensens_cls" id="cost_more_expanses" name="cost" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Clinic / Hospital Name">

                                        </div>
                                    </div>
                        </div>
                    </div>

                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas"  class="btn btn-outline-danger w-50 me-2 mainclearall_patient">Clear All</a>
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" id="expensens_submit_search" data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
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
                            data-bs-target="#offcanvas_mail_filternotes"
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
                                     </div></div>
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
                            data-bs-target="#offcanvas_mail_filterfeedback"
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
                                        <div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                                       <h4 class="alert-heading" style="font-size: 12px;">Doctor Name</h4>
                                       <p style="font-size: 12px;" id="doctor_names_feed">Name : Dr.Aravindivf</p>
                                       <h4 class="alert-heading" style="font-size: 12px;">Marketer Details</h4>
                                       <p style="font-size: 12px;" id="empname_views_all_feed">Emp Name : R. Anusuya</p>
                                       <h4 class="alert-heading" style="font-size: 12px;">Feedback</h4>
                                       <p style="font-size: 12px;" id="feedback_meetss"></p>
                                     </div></div>
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
                    <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Camp Management</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>
                                            <div class="dropdown">
                                                <input type="text" class="searchInput multi_searchInput marketervalues_search" name="Branch" id="Branch" placeholder="Select Branch">
                                                <div class="dropdown-options options_multi options_marketers brachviewsall">
                                                    <div data-value="chennai">chennai</div>
                                                    <div data-value="Madurai">Madurai</div>
                                                    <div data-value="11811">11811</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Camp Date:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_employee errorss"></span>
                                            <input type="date" class="form-control" id="camp_date" name="camp_date" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Camp date">

                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Camp Type:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_special errorss"></span>
                                            <input type="text" class="form-control" id="camp_type" name="camp_type" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Camp Type">

                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Camp Location :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_hplname errorss"></span>
                                            <input type="text" class="form-control" id="camp_location" name="camp_location" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Camp Location">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">G map :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_adress errorss"></span>
                                            <input type="text" class="form-control" id="g_map" name="g_map" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="G map">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Doctor Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_city errorss"></span>
                                            <input type="text" class="form-control" id="doctor_name_views" name="doctor_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Doctor Name">

                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Orginatized by :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_city errorss"></span>
                                            <input type="text" class="form-control" id="organized_by" name="organized_by" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="orginatized by">

                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Camp Incharge :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_city errorss"></span>
                                            <input type="text" class="form-control" id="camp_incharge" name="camp_incharge" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Camp incharge">

                                        </div>
                                    </div>



                                </div>

                                <div class="modal-footer">
                                    <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="submit-campdatas" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
                                </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
      <!-- Add meeting -->
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
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Add Activites <span id="idsviews" ></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>
                                            <select class="mb-3 form-select " id="camp_id_views" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"  name="camp_id">
                                            <option value="">Branch Name</option>
                                            <option value="1">1</option>
                                            </select>
                                            <div class="dropdown">
                                                <input type="text" class="searchInput multi_searchInput marketervalues_search" name="camp_id" id="camp_id_views" placeholder="Select Branch">
                                                <div class="dropdown-options options_multi options_marketers brachviewsall">
                                                    <div data-value="chennai">chennai</div>
                                                    <div data-value="Madurai">Madurai</div>
                                                    <div data-value="11811">11811</div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Date:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>
                                            <input type="date" class="form-control" id="date_activites" name="date_activites" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="date">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Activites:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>
                                            <input type="text" class="form-control" id="activites" name="activites" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Activites">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Area Covered:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>
                                            <input type="text" class="form-control" id="area_covered" name="area_covered" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Area covered">
                                    </div>
                                    </div>
                                          <div class="col-sm-12 ">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Description: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_feedbackss errorss"></span>
                                            <textarea require class="form-control " id="description" name="description" rows="6" style="border-radius: 10px; border: solid 1px #d3d3d3; background-color: #fff; color: #505050 !important; width: 100%; padding: 6px;" placeholder="Enter feedback details here"></textarea></div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                   <br> <label class="form-label required" style="font-size: 12px;font-weight: 600;">Activites Images : [ Min 2 - Max 6 ]</label>
                                    <div class="fallback"> &nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_images errorss"></span>
                                        <input name="files[]" id="image_uploads_camp" type="file" style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;" multiple />
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="submit-activites" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
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
                <div
                    class="modal fade"
                    id="exampleModal3"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Add Expenses <span id="idsviews" ></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="col-sm-4">
                                        <div class="mb-4">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch Name: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_hplcontact errorss"></span>
                                            <input type="text" class="form-control" id="Branch_expenses" name="Branch" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Branch Name">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="mb-4">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Activites:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>
                                            <select class="mb-3 form-select " id="activites_expenses" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"  name="activites">
                                            <option value="">Doctor Name</option>
                                            <option value="sdsadsadsad">dsdasdasd</option>
                                            </select></div>
                                    </div>
                                   <div class="col-sm-4">
                                        <div class="mb-4">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Cost: </label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_hplcontact errorss"></span>
                                            <input type="text" class="form-control" id="cost_expenses" name="cost" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="cost">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <br> <label class="form-label required" style="font-size: 12px;font-weight: 600;">Creatives</label>
                                         <div class="fallback"> &nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_images errorss"></span>
                                             <input name="files[]" id="image_creatives_expenses" type="file" style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;" multiple />
                                         </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <br> <label class="form-label required" style="font-size: 12px;font-weight: 600;">Document Purchase order</label>
                                         <div class="fallback"> &nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_images errorss"></span>
                                             <input name="files[]" id="image_uploads_expenses" type="file" style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;" multiple />
                                         </div>
                                    </div>
                                <div class="modal-footer">
                                    <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="submit-expenses-data" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
                                </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
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
                          data-bs-target="#announcement"
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
                                          <label class="form-label required" style="font-size: 12px;font-weight: 600;">Doctor Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>
                                          <input type="text" class="form-control editsall" id="doctorname_edits" name="doctor_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Doctor Name">
                                      </div>
                                  </div>
                                  <div class="col-sm-12">
                                      <div class="mb-12"><br>
                                          <label class="form-label required" style="font-size: 12px;font-weight: 600;">Marketer Name:</label>
                                          <select class="mb-3 form-select editsall" id="emp_name" name="empolyee_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="empolyee_name">
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
                                          <label class="form-label required" style="font-size: 12px;font-weight: 600;">Contact:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_hplname errorss"></span>
                                          <input type="text" class="form-control editsall" id="contactviews"  name="hpl_contact" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Clinic / Hospital Name">
                                      </div><br>
                                  </div>
                                  <div class="col-sm-12">
                                      <div class="mb-12">
                                          <label class="form-label required" style="font-size: 12px;font-weight: 600;">city:</label>
                                          <select class="mb-3 form-select editsall"  id="citys" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="city">
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
                                          <label class="form-label required" style="font-size: 12px;font-weight: 600;">Address:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_hplname errorss"></span>
                                          <input type="text" class="form-control editsall" id="addressviews"  name="address" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Clinic / Hospital Name">
                                      </div>
                                  </div>
                              </div>
                      </div>
                      <!-- Fixed Clear All Button -->
                      <div class="card-footer sticky-bottom bg-white">
                      <div class="d-flex justify-content-between">
      <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas"  class="btn btn-outline-danger w-50 me-2 ">Clear All</a>
      <a href="#" style="height: 34px;width: 133px; font-size: 12px;"  data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50 editsoveralls">Submit</a>
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
                    id="exampleModal1"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #96488b;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Doctor Details : #<span id="doctor_ids"> 4</span> - <span id="Doctornamehead"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <div class="row">
                                    <div class="col-sm-7">
                                    <div class="mb-7">
                                    <img src="" id="main">
                                     <div id="thumbnails">
                                         <!-- <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png">
                                          <img src="../assets/images/gallery-grid/1722403363_IMG_20240731_105245156.png">
                                           <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png">
                                           <img src="../assets/images/gallery-grid/1722403612_IMG_20240731_105651149.png"> -->
                                       </div>
                                       </div>
                                    </div>
                                    <div class="col-sm-4"><br>
                                    <ul class="nav nav-tabs analytics-tab" >
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="doctor_info" style="padding: 0.5rem 0.8rem;" type="button" >Profile</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link "  id="meeting_info" style="padding: 0.5rem 0.8rem;" type="button" >Meeting info</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="patients_info" style="padding: 0.5rem 0.8rem;" type="button"  >Patients Info</button>
                  </li>
                </ul><br>
                                <div class="mb-4" >
                                    <div class="card" id="doctor_info_details" style="overflow-y: auto; max-height: 400px;scrollbar-width: thin; /* For Firefox */ ">
                                        <div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                                       <h4 class="alert-heading" style="font-size: 12px;">Doctor Name</h4>
                                       <p style="font-size: 12px;" class="doctor_names">Name : Dr.Aravindivf</p>
                                       <h4 class="alert-heading" style="font-size: 12px;margin-top: 15px;">Address details</h4>
                                       <p style="font-size: 12px; margin-top: 0px;" id="docaddress">50/287, Bypass Rd, Meyyanur, Salem, Tamil Nadu 636006, India</p>
                                       <h4 class="alert-heading" style="font-size: 12px;">Contact Details</h4>
                                       <p style="font-size: 12px;" id="dcnum">Doctor Number : 9677670823</p>
                                       <p style="font-size: 12px; ;margin-top: -12px;" id="hpnum">Hospital Number : 8677670823</p>
                                       <h4 class="alert-heading" style="font-size: 12px;">Marketer Details</h4>
                                       <p style="font-size: 12px;" class="empname_views_all">Emp Name : 9677670823</p>
                                       <h4 class="alert-heading" style="font-size: 12px;">Schedules</h4>
                                       <p style="font-size: 12px;" id="visit_date">visit Date : 03 Jan 2025 | 11:38 AM</p>
                                       <h4 class="alert-heading" style="font-size: 12px;">Location</h4>
                                       <p style="font-size: 12px;color: #d70ebb;" id="maplocation">visit Date : 03 Jan 2025 | 11:38 AM</p>
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
            <div class="d-grid"
              ><a class="btn btn-outline-secondary" href="https://1.envato.market/zNkqj6" target="_blank">Check Now</a></div
            >
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
        <a href="https://1.envato.market/zNkqj6" target="_blank"
          ><img src="../assets/images/layout/img-announcement-2.png" alt="img" class="img-fluid"
        /></a>
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
        // Set the initial start and end dates
        var start = moment().subtract(29, 'days');
        var end = moment();
        var start1 = moment().subtract(29, 'days');
        var end1 = moment();
        var start2 = moment().subtract(29, 'days');
        var end2 = moment();
        // Callback function to update the span text with the selected date range
        function cb(start, end) {
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
        function cb1(start1, end1) {
                    $("#campactivitesdate").text(start1.format('DD/MM/YYYY') + ' - ' + end1.format('DD/MM/YYYY'));
          // Check the selected date range and adjust the display accordingly
          if (start1.isSame(end1, 'day')) {
              // If the start and end date are the same, show the single date
              if (start1.isSame(moment(), 'day')) {
                  $('#reportrangeactivites span').html('Today');
              } else if (start1.isSame(moment().subtract(1, 'days'), 'day')) {
                  $('#reportrangeactivites span').html('Yesterday');
              } else {
                  $('#reportrangeactivites span').html(start1.format('DD/MM/YYYY'));
              }
          } else {
              // For other ranges like "Last 7 Days", "This Month", etc.
              $('#reportrangeactivites span').html(start1.format('DD/MM/YYYY') + ' - ' + end1.format('DD/MM/YYYY'));
          }
          }
                function cb2(start2, end2) {
                  $("#expensesdateviews").text(start2.format('DD/MM/YYYY') + ' - ' + end2.format('DD/MM/YYYY'));
                // Check the selected date range and adjust the display accordingly
                if (start2.isSame(end2, 'day')) {
                // If the start and end date are the same, show the single date
                if (start2.isSame(moment(), 'day')) {
                $('#reportrangeexpenses span').html('Today');
                } else if (start2.isSame(moment().subtract(1, 'days'), 'day')) {
                $('#reportrangeexpenses span').html('Yesterday');
                } else {
                $('#reportrangeexpenses span').html(start2.format('DD/MM/YYYY'));
                }
                } else {
                // For other ranges like "Last 7 Days", "This Month", etc.
                $('#reportrangeexpenses span').html(start2.format('DD/MM/YYYY') + ' - ' + end2.format('DD/MM/YYYY'));
                }
                }
        // Initialize the date range picker
        $('#reportrangecamp').daterangepicker({
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
        $('#reportrangeactivites').daterangepicker({
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
        $('#reportrangeexpenses').daterangepicker({
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
        $(document).on('click', '.editbtn', function (e) {
                    $('#exampleModal').modal('show');
                });
        $(document).on('click', '.addmeeting', function (e) {
                    $('#exampleModal2').modal('show');
                });
        $(document).on('click', '.addpatient', function (e) {
                    $('#exampleModal3').modal('show');
                });
        $("#document_camp").css("color", "#080fd399");
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
                  // Show dropdown when input is focused
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

  // Handle option click using event delegation
  $(document).on("click", ".dropdown-options div", function () {
    const selectedValue = $(this).text();
    const dropdown = $(this).closest(".dropdown");
    dropdown.find(".searchInput").val(selectedValue);
    dropdown.removeClass("active");
  });

  // Close all dropdowns when clicking outside
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
