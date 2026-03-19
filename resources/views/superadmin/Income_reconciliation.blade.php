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
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
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

table {
            width: 104%;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #6b6fe5;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .edit-btn,.save-btn,.apply-btn,.back-btn,.verify-btn{
            background-color: #6b6fe5;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            margin: 3px 0;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
.save-btn {
            background-color: #6b6fe5;
            color: white;
            border: none;
            margin-top:10px;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
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
            width: 100%;
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
 /* =========================
       DROPDOWN STYLES
    ========================= */
    .dropdown, .loct-dropdown, .myloct-dropdown, .allloct-dropdown {
        position: relative;
        width: 100%;
        font-size: 10px;
    }

    .dropdown input, .loct-dropdown input, .myloct-dropdown input, .allloct-dropdown input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        box-sizing: border-box;
        cursor: pointer;
        font-size: 12px;
        background: white;
        transition: border-color 0.2s ease;
    }

    .dropdown input:focus, .loct-dropdown input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .dropdown-options, .loct-dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-top: none;
        background: white;
        display: none;
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 0 0 6px 6px;
        font-size: 12px;
    }

    .dropdown-options div, .loct-dropdown-options div {
        padding: 10px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }

    .dropdown-options div:hover, .loct-dropdown-options div:hover {
        background-color: #f0f9ff;
        color: #0c4a6e;
    }

    .dropdown.active .dropdown-options,
    .loct-dropdown.active .loct-dropdown-options {
        display: block;
    }

    /* Selected items */
    .dropdown-options div.selected,
    .loct-dropdown-options div.selected {
        background-color: #6366f1 !important;
        color: white !important;
        font-weight: 500;
    }

    /* =========================
       SELECT/DESELECT BUTTONS
    ========================= */
    .dropdown-actions {
        padding: 8px 12px;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
        display: flex;
        gap: 8px;
    }

    .select-all, .deselect-all {
        padding: 6px 12px;
        font-size: 11px;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        background-color: white;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
        flex: 1;
    }

    .select-all:hover {
        background-color: #10b981;
        border-color: #10b981;
        color: white;
    }

    .deselect-all:hover {
        background-color: #ef4444;
        border-color: #ef4444;
        color: white;
    }
    .text-green { color: green; }
    .text-red   { color: red; }
    /* ==================== ENHANCED STATISTICS CARDS ==================== */
    .stat-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    }

    .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #6b6fe5, #b163a6);
    }

    .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border-color: #6b6fe5;
    }

    .stat-card h3 {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 5px;
    color: #2d3748;
    }

    .stat-card p {
    font-size: 11px;
    font-weight: 600;
    color: #718096;
    margin-bottom: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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
        .diff-zero {
            color: #28a745 !important;
            background-color: #d4f8d4 !important;
            font-weight: 600 !important;
            border-radius: 4px;
            padding: 2px 6px !important;
        }

        .diff-positive {
            color: #17a2b8 !important;
            background-color: #d1ecf1 !important;
            font-weight: 600 !important;
            border-radius: 4px;
            padding: 2px 6px !important;
        }

        .diff-negative {
            color: #dc3545 !important;
            background-color: #f8d7da !important;
            font-weight: 600 !important;
            border-radius: 4px;
            padding: 2px 6px !important;
        }
        .apply-btn-icon i {
            color: #28a745; /* green tick */
            margin-right: 6px;
        }
        .table-container{
            overflow-y: auto;
        }
.table-container::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #6b6fe5 0%, #5a5fd8 100%);
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a5fd8 0%, #4a4fcb 100%);
}

/* For Firefox */
.table-container {
    scrollbar-width: thin;
    scrollbar-color: #6b6fe5 #f1f1f1;
}
    .modal-input {
    display: none;
}

.file-view{
    font-size: 13px;
}
.plus-icon {
 display: none;
 font-size:10px;
    cursor: pointer;

    pointer-events: auto;
}

.edit-text {
    display: inline-block;
    min-width: 40px;
}
.calendar-icon{
  cursor:pointer;
  color:#007bff;
}

.calendar-icon:hover{
  color:#0056b3;
}
.info-icon{
  cursor:pointer;
  color:#007bff;
  margin-left:6px;
}
.tdview {
    position: relative;
}

.tooltip-text ,.calander-text{
    position: absolute;
    top: 1px;
    left: 10px;
    background: #333;
    color: #fff;
    padding: 2px 3px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 999;

}
/* show tooltip on hover */
.info-icon:hover + .tooltip-text,.calendar-icon:hover + .calander-text{
  display:inline-block;
}
.custom-tooltip {
    position: absolute;
    background: #ffffff;
    border: none;
    padding: 0;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12),
                0 2px 8px rgba(0, 0, 0, 0.08);
    z-index: 9999;
    font-size: 13px;
    min-width: 320px;
    overflow: hidden;
     display:none;
    animation: tooltipSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes tooltipSlideIn {
    from {
        opacity: 0;
        transform: translateY(-15px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Decorative top border */
.custom-tooltip::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
}

.custom-tooltip table {
    width: 100%;
    background: #ffffff;
    margin: 0;
    border-spacing: 0;
    padding: 8px 0;
}

.custom-tooltip td {
    padding: 10px 20px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
    font-size: 13px;
}

.custom-tooltip tr:hover:not(.total) td {
    background: #f8fafc;
}

.custom-tooltip tr:last-child:not(.total) td {
    border-bottom: 1px solid #e2e8f0;
}

.custom-tooltip td:first-child {
    font-weight: 500;
    color: #475569;
}

.custom-tooltip td:last-child {
    font-weight: 600;
    color: #0f172a;
    font-family: 'Segoe UI', system-ui, sans-serif;
    text-align: right;
}

/* Enhanced Total Row */
.custom-tooltip .total {
    background: none !important;
}


.custom-tooltip .total td {
    color: #ffffff;
    padding: 16px 20px;
    border-bottom: none;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.3px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    background: none !important;
}

.custom-tooltip .total td:first-child {
    color: #000000;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
}

.custom-tooltip .total:hover td {
    background: transparent;
}

/* Date Section Styling */
.custom-tooltip > span {
    display: inline-block;
    padding: 14px 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #64748b;
    font-size: 13px;
    font-weight: 500;
    width: 100%;
    box-sizing: border-box;
    border-top: 1px solid #e2e8f0;
}

.custom-tooltip > span:first-of-type {
    color: #475569;
    font-weight: 600;
    padding-right: 8px;
    background: transparent;
    border: none;
    width: auto;
    display: inline;
}

.custom-tooltip > span:last-of-type {
    color: #0f172a;
    font-weight: 600;
    padding-left: 0;
    background: transparent;
    width: auto;
}

/* Date container wrapper */
.custom-tooltip > span:first-of-type::before {
    content: '📅';
    margin-right: 6px;
    font-size: 14px;
}

/* Icon hover effects */
.info-icon, .calendar-icon {
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    font-size: 12px;
    display: inline-block;
}

.info-icon:hover {
    transform: scale(1.15) rotate(12deg);
    color: #6366f1;
    filter: drop-shadow(0 2px 4px rgba(99, 102, 241, 0.3));
}

.calendar-icon:hover {
    transform: scale(1.15);
    color: #8b5cf6;
    filter: drop-shadow(0 2px 4px rgba(139, 92, 246, 0.3));
}
.remark_value{
        font-size: 20px;
    font-weight: 600;
}
.remark_viewer{
    padding: 10px;
    font-size: 15px;
}
.tooltip-box{
  position:absolute;
  background:#333;
  color:#fff;
  padding:8px 12px;
  border-radius:6px;
  white-space: normal;     /* <<< allow wrapping */
  max-width:260px;         /* <<< optional limit width */
  font-size:12px;
  z-index:99999;
  display:none;
  line-height:1.4;
  box-shadow:0 3px 8px rgba(0,0,0,.25);
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
            <div class="col-md-9 col-sm-9" style="display: flex;gap: 10px;">
              <input type="text" id="icon-search" class="form-control mb-4"
                            style="
                    height: 35px;
                    font-size: 11px;
                "  placeholder="Search">
                <div style="text-align: right;">
                    <div class="dropdown">
                        <button class="btn btn-light" data-bs-toggle="dropdown">
                            &#x22EE;
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importincomeModal">Import Income Data</a></li>
                            {{-- <li><a class="dropdown-item export-btn" href="#" data-format="xlsx">Export XLSX</a></li> --}}
                        </ul>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-3 col-sm-3 ">
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn_user" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
    background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4;
"><i class="ti ti-plus f-18"></i>Document</a></div> --}}
          </div>
        </div>
      </div><br><br>
<!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
              <!-- [ Main Content ] start -->
              <div class="container" style="margin-top: -51px;">
                <div class="row g-4">
                    <!-- Row 1 -->
                     <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_cash">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Total Cash</p>
                        </div>
                    </div>
                    <!-- Row 2 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_card">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Total Card</p>
                        </div>
                    </div>
                    <!-- Row 3 -->

                    <!-- Row 5 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_neft">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Total Neft</p>
                        </div>
                    </div>
                    <!-- Row 6 -->

                    <!-- Row 7 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_upi">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Total UPI</p>
                        </div>
                    </div>
                    <!-- Row 8 -->
                    <div class="col-md-1 col-sm-3">
                        <div class="stat-card text-center  p-2 shadow-sm" style="border: 1px solid #c3bfc3; border-radius: 8px;">
                            <h3 class="fs-5 fw-bold" id="total_amount">0</h3>
                            <p class="text-muted mb-0" style="font-weight: 600; font-size: 10px;">Total Amount</p>
                        </div>
                    </div>

                </div><br>
            </div>
            <!-- [ Main Content ] end -->
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
                      aria-selected="true">Income Reconciliation</button>
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
                                <span id="data_values"></span> <i class="fa fa-caret-down views"></i>
                            </div>

                            <span style="display:none;"  id="dateviewsall"></span>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-3">
                        <div class="card">
                            <div class="dropdown">
                                    <input type="text" class="searchZone multi_search checkvalues_search" name="tblzones.name" id="izone_views" placeholder="Select Zone" autocomplete="off">
                                <div class="dropdown-options multi_search options_branch">
                                    <div class="dropdown-actions">
                                        <button type="button" class="select-all">Select All</button>
                                        <button type="button" class="deselect-all">Deselect All</button>
                                    </div>

                                    @if($zones)
                                        @foreach($zones as $zone)
                                        <div data-value="{{$zone->name}}">{{$zone->name}}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-3">
						<div class="card">
							<div class="dropdown">
								<input type="text" class="searchZone multi_search checkvalues_search" name="tbl_locations.name" id="ibranch_views" placeholder="Select Branch" autocomplete="off">
								<div class="dropdown-options multi_search options_branch branch_viewsall">
                                    <div class="dropdown-actions">
                                        <button type="button" class="select-all">Select All</button>
                                        <button type="button" class="deselect-all">Deselect All</button>
                                    </div>
								</div>
							</div>
						</div>
					</div>


                    <div class="col-xl-2 col-md-2">
                        <div class="">

                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>

                </div>
                <p style="margin-top: -9px;" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="dcounts">0</span> Rows for <span id="dateallviews">Last 30 days</span></span>
                    <span class="cincome_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search :</span>
                    <span style="cursor: pointer;" id="cbranch_search" class="badge bg-success value_views_mainsearch"></span>
                    <span style="cursor: pointer;" id="czone_search" class="badge bg-success value_views_mainsearch"></span>
                    <span style="cursor: pointer;" id="income_search" class="badge bg-success value_views_mainsearch"></span>
                    <span  class="badge bg-danger clear_views" style="display:none;">Clear all</span>
                </p><br>
                <div class="col-sm-12">
                    <div class="card-body">
                        <div class="table-container">
                            <table class="tblvis">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Sl.no.</th>
                                        <th rowspan="2">Date</th>
                                        <th rowspan="2">Location</th>
                                        <th colspan="5">Cash</th>
                                        <th colspan="3">Card</th>
                                        <th colspan="3">UPI</th>
                                        <th colspan="3">Bank Statement</th>
                                        <th colspan="3">NEFT</th>
                                        <th rowspan="2">Edit</th>

                                    </tr>
                                    <tr>
                                        <th>Moc doc</th> <th>Radiant</th> <th>Diff</th> <th>Bank St.</th><th>Diff</th>
                                        <th>Moc doc</th> <th>Orange</th> <th>Diff</th>
                                        <th>Moc doc</th> <th>Orange</th> <th>Diff</th>
                                        <th>Charges</th> <th>Amount</th> <th>Diff</th>
                                        <th>Moc doc</th> <th>Bank St.</th> <th>Diff</th>
                                    </tr>
                                </thead>
                                <!-- sample data  -->
                                <tbody id="loader_row">
                                    <tr>
                                    <td colspan="22">
                                        <div id="loader-container">
                                        <div id="progress-bar">Loading: 0%</div>
                                        <div id="error-message" style="color: red; display: none;"></div>
                                        </div>
                                    </td>
                                    </tr>
                                </tbody>
                                <tbody id="daily_details_recon" style="display:none;">

                                </tbody>


                            </table>
                        </div>
                        <div class="footer">
                            <div>
                                Items per page:
                                <select id="itemsPerPageSelect">
                                    <option>20</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select>
                            </div>
                            <div class="pagination" id="ticketpagination"></div>
                        </div>
                    </div>
                </div>
            </div>




<div class="modal fade" id="cashRadiantModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Amount</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-2 remark-wrap">
            <label>Remark</label>
            <textarea class="form-control modal-remark" rows="3"
                    placeholder="Enter remark..."></textarea>
        </div>
        <!-- CASH -->
        <input type="file" id="modalCashRadiant" class="form-control modal-input" data-field="cash_radiant" accept=".pdf,image/*" />
        <input type="file" id="modalCashBank" class="form-control modal-input mt-2" data-field="cash_bank"  accept=".pdf,image/*"/>

        <!-- CARD -->
        <input type="file" id="modalCardRadiant" class="form-control modal-input mt-2" data-field="card_radiant"  accept=".pdf,image/*"/>

        <!-- UPI -->
        <input type="file" id="modalUpiRadiant" class="form-control modal-input mt-2" data-field="upi_radiant"  accept=".pdf,image/*"/>

        <!-- Bank -->
        <input type="file" id="modalBankstmtchr" class="form-control modal-input mt-2" data-field="bank_stmt_charge" accept=".pdf,image/*" />
        <input type="file" id="modalBankstmtamt" class="form-control modal-input mt-2" data-field="bank_stmt_amount" accept=".pdf,image/*" />
        <!-- NEFT -->
        <input type="file" id="modalNeftBank" class="form-control modal-input mt-2" data-field="neft_bank"  accept=".pdf,image/*"/>

      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" id="modalSaveCash">Save</button>
      </div>

    </div>
  </div>
</div>

<!-- file preview model -->
<div class="modal fade" id="filePreviewModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">File Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="remark_viewer">
        <span>Remark :</span>
        <span class="remark_value"></span>
      </div>
      <div class="modal-body text-center" id="filePreviewBody">
        <!-- dynamic content -->
      </div>

      <div class="modal-footer">
        <a href="#" id="downloadFileBtn" class="btn btn-success" download>
          Download
        </a>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- edit apply btn logic -->
<!-- Modal -->
<div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="dateModalLabel">Select Date, Remark & Upload File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">

        <div class="mb-2">
          <label for="fromDate" class="form-label">From Date</label>
          <input type="date" id="fromDate" class="form-control">
        </div>

        <div class="mb-2">
          <label for="toDate" class="form-label">To Date</label>
          <input type="date" id="toDate" class="form-control">
        </div>

        <div class="mb-2">
          <label for="remark" class="form-label">Remark</label>
          <textarea id="remark" class="form-control" rows="2" placeholder="Enter remark here..."></textarea>
        </div>

        <div class="mb-2">
          <label for="mocFile" class="form-label">Upload File</label>
          <input type="file" id="mocFile" class="form-control">
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" id="applyDates" class="btn btn-success">
          Apply & Upload
        </button>
      </div>

    </div>
  </div>
</div>

<!--import Modal -->
<div class="modal fade" id="importincomeModal" tabindex="-1" aria-labelledby="importincomeModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="importincomeModalLabel">Import Income Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <p>Step 1: Download the Excel Template</p>
        <a href="{{ url('/superadmin/Income-download-template') }}" class="btn btn-success mb-3">
            Download Template
        </a>
        <p>Step 2: Upload the Filled File</p>
        <!-- Custom Styled File Upload -->
        <div class="d-flex align-items-center gap-2">
            <label class="btn btn-outline-primary position-relative mb-2">
            <i class="bi bi-upload"></i> Upload File
            <input type="file" name="file" class="d-none" id="importFileInput" accept=".xlsx,.csv" required>
            </label>
        </div>
        <!-- File name display -->
        <div id="fileNameDisplay" class="text-muted" style="font-size: 0.85rem;"></div>
        </div>
        <div class="modal-footer">
        <button type="submit" class="btn btn-primary import-btn">Upload & Import</button>
        </div>
    </div>
</div>
</div>


            <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- income related script start -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('/assets/income/Income_reconciliation.js') }}"></script>

<script type="text/javascript">

	const incomefetchUrl = "{{ route('superadmin.incomereportfetch') }}";
    const incomeBranchUrlfitter = "{{ route('superadmin.incomereportfilter') }}";
	const dateIncomeUrl = "{{ route('superadmin.incomedatefilter') }}";
	const incomeBranchfitter = "{{ route('superadmin.incomebranchfilter') }}";
	const incomestore = "{{ route('superadmin.incomestore') }}";
	const incomeradiantfetch = "{{ route('superadmin.incomeradiantfetch') }}";
	const incomeuploadFile = "{{ route('superadmin.incomeuploadFile') }}";
	const incomedatecheck = "{{ route('superadmin.recon.check') }}";
    var filterTriggerTimer = null;




        // Set the initial start and end dates
        var start = moment();
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

        // Set initial date range text
        cb(start, end);
    </script>

	<script>


        $(document).ready(function () {
            $('#importFileInput').on('change', function () {
                const file = this.files[0];
                if (file) {
                    $('#fileNameDisplay').text('Selected file: ' + file.name);
                } else {
                    $('#fileNameDisplay').text('');
                }
                });
         /* =========================
            OPEN DROPDOWN
            ========================= */
            $(document).on("focus click", ".searchZone", function (e) {
                e.stopPropagation();
                $(this).closest(".dropdown").addClass("active");
            });

            /* =========================
            SEARCH FILTER
            ========================= */
            $(document).on("input", ".searchZone", function () {
                const searchText = $(this).val().toLowerCase().split(",").pop().trim();
                $(this).siblings(".dropdown-options").find("div[data-value]").each(function () {
                    $(this).toggle($(this).text().toLowerCase().includes(searchText));
                });
            });

            /* =========================
            SELECT / DESELECT SINGLE VALUE
            ========================= */
            $(document).on("click", ".dropdown-options div[data-value]", function (e) {
                e.stopPropagation();
                const input = $(this).closest(".dropdown").find(".searchZone");
                const selectedValue = $(this).text().trim();

                let values = input.data("values");
                if (!Array.isArray(values)) values = [];

                if (values.includes(selectedValue)) {
                    values = values.filter(v => v !== selectedValue);
                    $(this).removeClass("selected");
                } else {
                    values.push(selectedValue);
                    $(this).addClass("selected");
                }

                input.data("values", values);
                input.val(values.join(", "));
            });

            /* =========================
            KEEP SELECTION ON FOCUS
            ========================= */
            $(document).on("focus", ".searchZone", function () {
                const input = $(this);
                const values = input.data("values") || [];
                input.siblings(".dropdown-options").find("div[data-value]").each(function () {
                    $(this).toggleClass("selected", values.includes($(this).text().trim()));
                });
            });

            /* =========================
            BLUR VALIDATION
            ========================= */
            $(document).on("blur", ".multi_search", function () {
                const input = $(this);
                const values = input.data("values") || [];
                input.val(values.join(", "));
            });

            /* =========================
            LOCATION DROPDOWN (UNCHANGED)
            ========================= */
            $(document).on("focus click", ".searchLocation", function (event) {
                event.stopPropagation();
                const inputField = $(this);
                const dropdown = inputField.closest(".loct-dropdown");
                const options = dropdown.find(".loct-dropdown-options");
                $(".loct-dropdown-options").hide();
                options.show();
                dropdown.addClass("active");
                options.find("div").show();
                const selectedValue = inputField.val().trim();
                options.find("div").each(function () {
                    $(this).toggleClass("selected", $(this).text().trim() === selectedValue);
                });
            });

            $(document).on("input", ".searchLocation", function () {
                const searchText = $(this).val().toLowerCase();
                $(this).siblings(".loct-dropdown-options").find("div").each(function () {
                    $(this).toggle($(this).text().toLowerCase().includes(searchText));
                });
            });

            $(document).on("click", ".loct-dropdown-options div", function (event) {
                event.stopPropagation();
                const selectedValue = $(this).text();
                const inputField = $(this).closest(".loct-dropdown").find(".searchLocation");
                inputField.val(selectedValue);
                $(this).addClass("selected").siblings().removeClass("selected");
                $(".loct-dropdown-options").hide();
                $(".loct-dropdown").removeClass("active");
            });

            /* =========================
            CLOSE DROPDOWN
            ========================= */
            $(document).on("click", function () {
                $(".dropdown").removeClass("active");
                $(".loct-dropdown-options").hide();
                $(".loct-dropdown").removeClass("active");
            });

            $(document).on('click', '.import-btn', function (e) {
                $btn=$(this).prop('disabled', true);
                // Save original button text
                let originalText = $btn.html();
                // Show loader on button
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                    e.preventDefault();

                    const fileInput = $('#importFileInput')[0];
                    const file = fileInput.files[0];

                    if (!file) {
                        toastr.error("Please select a file to upload.");
                        $btn.prop('disabled', false).html(' Upload & Import');
                        return;
                    }

                    let formData = new FormData();
                    formData.append('file', file);

                    $.ajax({
                        url: '{{ url("/superadmin/import-income") }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                toastr.success(response.message);
                                $('#importbillModal').modal('hide');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                toastr.error("Unexpected response from server.");
                                $btn.prop('disabled', false).html(' Upload & Import');
                            }
                        },
                        error: function (xhr) {
                            console.error("Import failed:", xhr);
                            toastr.error("Failed to import file.");
                            $btn.prop('disabled', false).html(' Upload & Import');
                        }
                    });
                });
        });
        $(document).on('click', '.verify-btn', function () {

            let btn = $(this);
            let tr = btn.closest("tr");
            let zone = $("#izone_views").val();
            let location = tr.find(".location-cell").data("location");

            let dummy = $('#dateallviews').text();
            let firstDate = dummy.split('-')[0].trim();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will mark the records as verified.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Verify',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('income.verify') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            branch: location,
                            date: firstDate
                        },
                        success: function (res) {

                            Swal.fire(res.message);

                            // optional UI update
                            tr.find(".apply-btn-icon").show();
                            tr.find(".edit-btn").hide();
                            tr.find(".verify-btn").hide();
                        },
                        error: function () {
                            Swal.fire(
                                'Error',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    });
                }
            });
        });



	</script>
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
