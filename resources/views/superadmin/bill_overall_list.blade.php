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
        .pc-container .page-header + .row {
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
    .dropdown-options div.selected {
    background-color: rgb(107 111 229) !important;
    color: white !important;
    font-weight: bold;
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
        <p id="fetchvaluesss"></p>
        <p id="fetchvaluesssviews"></p>
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
            <div class="col-md-3 col-sm-3 ">
            {{-- <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn_user" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
            height: 34px;
         width: 133px;
         font-size: 12px;
                 background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4;
     "><i class="ti ti-plus f-18"></i>Document</a> --}}

    </div>
          </div>
        </div>
      </div>
<!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
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
                      aria-selected="true"><span id="incometypesss"><span> </button>
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
<div id="reportrangedoucment" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;font-size: 10px;">
    <i class="fa fa-calendar"></i>&nbsp;
    <span ></span> <i class="fa fa-caret-down"></i>
</div>
<span style="display:none;"  ></span>
</div>
</div>
<div class="col-xl-2 col-md-2">
    <div class="card">
        <div class="dropdown">
            <input type="text" class="searchInput marketervalues_search_patient" name="" id="patient_martketer" placeholder="Select document">
            <div class="dropdown-options options_patient">
                <div data-value="lcation43">Vellour</div>
                <div data-value="location13">Salem</div>

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
</div><div class="col-xl-2 col-md-2">
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
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span >0</span> Rows for <span >Last 30 days</span></span>
<span class="search_view" style="color: #e40505;font-size: 12px;font-weight: 600;cursor: pointer;">Search :</span>
<span style="cursor: pointer;" class="badge bg-success "></span>
<span style="cursor: pointer;" class="badge bg-success "></span>
<span style="cursor: pointer;" class="badge bg-success "></span>
<span style="cursor: pointer;" class="badge bg-success "></span>
<span style="cursor: pointer;" class="badge bg-success "></span>
<span  style="cursor: pointer;display:none;" class="badge bg-danger " >Clear all</span>
<span style="cursor: pointer;" class="badge bg-success " style="display:none;"></span>
</p><br>
        <div class="col-sm-12">
              <div class="card-body">
              <div class="table-container">
        <table class="tbl">
            <thead class="thd">
                <tr class="trview">
                    <th class="thview">S.No</th>
                    <th class="thview">Bill Date</th>
                    <th class="thview">Bill No</th>
                    <th class="thview">ID</th>
                    <th class="thview">Name</th>
                    <th class="thview">Consultant</th>
                    <th class="thview">Amount</th>
                    <th class="thview">Bill type</th>
                    <th class="thview">Payment type</th>
                    <th class="thview">Recieved By</th>
                </tr>
            </thead>
            <tbody id="bill_overall_list_all">
                <tr>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="footer">
        <div>
            Items per page:
            <select id="itemsPerPageSelectbitslsls">

                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
        <div class="pagination" id="paginationbitslsls"></div>
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
                        <h5>Documents Filter</h5>
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
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Document Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>
                                            <input type="text" class="form-control " id="doctornames_more"  name="doctor_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Document Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12"><br>
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Zone:</label>
                                            <select class="mb-3 form-select zoneid"   style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" required name="empolyee_name">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-12">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch Name:</label>
                                            <div class="multiselect-container" tabindex="0">
                                 <input type="text" id="special_more" name="special" class="multiselect-input " placeholder="Select Specialization" readonly>
                                    <div class="multiselect-options doctor_option" id="branchviews">
                                        <label>
                                            <input type="checkbox" value="Ayurvedic" onchange="updateSelectedValues()"> Ayurvedic
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
                        </div>
                        <div class="col-sm-12">
                    </div><br><br><br><br><br><br><br><br>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" data-bs-dismiss="offcanvas"  class="btn btn-outline-danger w-50 me-2 ">Clear All</a>
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;"  data-bs-dismiss="offcanvas" class="btn btn-outline-primary w-50">Submit</a>
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
                    id="exampleModaluser"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Document Management</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
                            <div class="modal-body">
                                <input type="hidden" class="userid" name="userid" id="userid" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>

                                            <div class="dropdown">
                                                <input type="text" class="searchInput multi_searchInput " name="zone_id" id="zone_id" placeholder="Select Branch">
                                                <div class="dropdown-options brachviewsall documentscls">
                                                    <div data-value="chennai">chennai</div>
                                                    <div data-value="Madurai">Madurai</div>
                                                    <div data-value="11811">11811</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Document Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_doctor errorss"></span>

                                                <div class="dropdown">
                                                    <input type="text" class="searchInput single_search" name="document_name" id="document_name" placeholder="Select document">
                                                    <div class="dropdown-options  ">
                                                        <div data-value="location43">Vellour</div>
                                                        <div data-value="Rental PCB Agreement">Salem</div>

                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Expire Date :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_adress errorss"></span>
                                            <input type="date" class="form-control" id="expire_date" name="expire_date" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Expire Date">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                  <div class="col-sm-6">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Document [ .pdf ]</label>
                                    <div class="fallback"> &nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_images errorss"></span>
                                        <input name="files[]" id="pf_uploads" type="file" accept="application/pdf" multiple style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;"  />
                                    </div>
                                </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="submit-document_data" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
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
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Document Renew : #<span id="docu_id" ></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <div id="error-message"></div>
                            <div class="modal-body">
                                 <div class="row">
                                 <input type="hidden" class="id" name="id" id="id_document" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                                 <input type="hidden" class="id" name="document_type" id="update_documents_all" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                                 <input type="hidden" class="id" name="expire_dates" id="expire_dates" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="">
                                <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required" style="font-size: 12px;font-weight: 600;">Expire Date :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_hplname errorss"></span>
                                            <input type="date" class="form-control" id="expire_update_date" name="expire_date" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Address">
                                        </div>
                                    </div>
                                <div class="col-sm-6">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Update Document [ .pdf ]</label>
                                    <div class="fallback"> &nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_images errorss"></span>
                                        <input name="files[]" id="pf_update" type="file" accept="application/pdf" style="height: 28px;border: ridge; background-color: #ffffff; border-color: #ffffff;" />
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
                        <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">Document Management system</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color: #ffffff;" aria-label="Close"></button>
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
                                    <embed id="pfmain" src="../public/document_data/1738390856_dictionary.pdf" width="100%" height="600px" />
                                    </div>
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
        // Callback function to update the span text with the selected date range
        function cb(start, end) {
            $("#dateviewsall").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            $("#dateallviews").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            // Check the selected date range and adjust the display accordingly
            if (start.isSame(end, 'day')) {
                // If the start and end date are the same, show the single date
                if (start.isSame(moment(), 'day')) {
                    $('#reportrangedoucment span').html('Today');
                } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                    $('#reportrangedoucment span').html('Yesterday');
                } else {
                    $('#reportrangedoucment span').html(start.format('DD/MM/YYYY'));
                }
            } else {
                // For other ranges like "Last 7 Days", "This Month", etc.
                $('#reportrangedoucment span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            }
        }
        // Initialize the date range picker
        $('#reportrangedoucment').daterangepicker({
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
        // Set initial date range text
        cb(start, end);
        $(document).on('click', '.editbtn_user', function (e) {
        $('#exampleModaluser').modal('show');
        });
        $("#accounts_recon").css("color", "#080fd399");
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
        function updateSelectedValues() {
            const checkboxes = document.querySelectorAll('.doctor_option input[type="checkbox"]');
            const selected = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);
            let inputElement = document.getElementById('special_more') ;
            inputElement.value = selected.length > 0 ? selected.join(',') : '';
        }
    </script>
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
