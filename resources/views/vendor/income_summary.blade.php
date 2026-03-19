<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>

.filter-section {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-teal {
    background-color: #1abc9c;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-teal:hover {
    background-color: #16a085;
    color: white;
}

.btn-danger-custom {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 4px;
}

.btn-success-custom {
    background-color: #27ae60;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    margin-left: 5px;
}

.total-badge {
    background-color: #27ae60;
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    display: inline-block;
    font-weight: bold;
    margin-bottom: 15px;
}

.table-container {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    /* box-shadow: 0 2px 4px rgba(0,0,0,0.1); */
  }
#incomeTableContainer{
  padding: 10px;
  margin: 5px 0px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);

}
.section-title {
    color: #d35400;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    margin: 20px 0;
}

.custom-table {
    width: 100%;
    border-collapse: collapse;
}

.custom-table thead {
    background-color: #fff;
}

.custom-table thead th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #e74c3c;
    border-bottom: 2px solid #ecf0f1;
    font-size: 14px;
        text-wrap: unset;

}

.custom-table tbody tr {
    border-bottom: 1px solid #ecf0f1;
    transition: background-color 0.2s;
}

.custom-table tbody tr:hover {
    background-color: #f8f9fa;
}

.custom-table tbody td {
    padding: 12px;
    color: #e74c3c;
    font-size: 14px;
}

.total-row {
    background-color: #f8f9fa !important;
    font-weight: bold;
}

.dropdown-custom {
    position: relative;
    display: inline-block;
}

.dropdown-menu-custom {
    display: none;
    position: absolute;
    background-color: white;
    min-width: 160px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    border-radius: 4px;
    z-index: 1;
    margin-top: 5px;
}

.dropdown-menu-custom.show {
    display: block;
}

.dropdown-item-custom {
    padding: 12px 16px;
    cursor: pointer;
    transition: background-color 0.2s;
    color: #333;
}

.dropdown-item-custom:hover {
    background-color: #f8f9fa;
}

.date-inputs {
    display: none;
    margin-top: 10px;
}

.date-inputs.show {
    display: flex;
    gap: 10px;
}
.spinner {
  width: 40px;
  height: 40px;
  border: 5px solid #ddd;
  border-top-color: #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: auto;
  margin-top: 15px;
}
@keyframes spin { 100% { transform: rotate(360deg); } }
</style>
<body style="overflow-x: hidden;">

    <div class="page-loader">
      <div class="bar"></div>
    </div>
    {{-- @php
        dd($billlist);
    @endphp --}}
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')
    <!-- [ Header ] end -->
    <div class="pc-container">
      <div class="pc-content">

         <!-- Main Content -->
          <div class="main-content">
              <!-- Filter Section -->
              <div class="filter-section mt-4">
                  <form method="GET" action="{{ route('superadmin.vendorincomeReport') }}" id="filterForm">
                      <div class="row align-items-end">
                          <div class="col-md-3">
                              <label for="location" class="form-label">Location</label>
                              <select class="form-select" id="location" name="location">
                                  <option value="">All Location</option>
                                  @foreach($incomeData as $location)
                                      <option value="{{ $location->location_id }}">{{ $location->location_name }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-3">
                              <label class="form-label">Date Range</label>
                              <div class="dropdown-custom">
                                  <button type="button" class="btn btn-teal dropdown-toggle w-100" id="dateDropdown">
                                      {{ ucwords(str_replace('_', ' ', $dateFilter)) }}
                                  </button>
                                  <div class="dropdown-menu-custom" id="dateMenu">
                                    <div class="dropdown-item-custom" data-value="today">Today</div>
                                    <div class="dropdown-item-custom" data-value="this_month">This Month</div>
                                    <div class="dropdown-item-custom" data-value="last_2_months">Last 2 Months</div>
                                    <div class="dropdown-item-custom" data-value="last_3_months">Last 3 Months</div>
                                    <div class="dropdown-item-custom" data-value="custom">Custom Date</div>
                                </div>

                              </div>
                              <input type="hidden" name="date_filter" id="dateFilterInput" value="{{ $dateFilter }}">
                          </div>

                          <div class="col-md-4 date-inputs" id="customDateInputs">
                              <div>
                                  <label class="form-label">Start Date</label>
                                  <input type="date" class="form-control start_date" name="start_date" value="{{ $startDate }}">
                              </div>
                              <div>
                                  <label class="form-label">End Date</label>
                                  <input type="date" class="form-control end_date" name="end_date" value="{{ $endDate }}">
                              </div>
                          </div>

                      </div>


              <!-- Income Report Section -->
              <div class="table-container">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                      <div>
                          <div class="total-badge">
                              Total: ₹ {{ formatIndianMoney($grandTotal) }}
                          </div>
                          <div>
                              <label>Show</label>
                              <select name="perPage" id="perPage" class="form-select d-inline-block" style="width: 70px;">
                                <option>100</option>
                                <option>50</option>
                                <option>25</option>
                              </select>
                              <span>entries</span>
                          </div>
                      </div>

                      <div>
                          <button type="button" class="btn btn-success-custom" id="btnCsv">
                              <i class="fas fa-file-csv"></i> Export As CSV
                          </button>
                          <button type="button" class="btn btn-success-custom" id="btnExcel">
                              <i class="fas fa-file-excel"></i> Export As Excel
                          </button>
                      </div>

                  </div>

                  <div class="section-title">Income from All Locations</div>
                  <div id="incomeTableContainer">
                    @include('vendor.partials.table.income_table_rows', ['incomeData' => $incomeData])
                  </div>

                  <div id="loader" style="display:none; text-align:center;">
                      <div class="spinner"></div>
                  </div>


                  <div class="total-badge">
                      Total: ₹ {{ formatIndianMoney($grandTotal) }}
                  </div>
              </div>
          </div>
        </form>
      </div>
      </div>

    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

<script>
$(document).ready(function () {

    // Toggle date dropdown
    $("#dateDropdown").on("click", function () {
        $("#dateMenu").toggleClass("show");
    });

    // Close dropdown when clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest("#dateDropdown").length) {
            $("#dateMenu").removeClass("show");
        }
    });
    function formatIndianMoney(amount) {
      amount = parseFloat(amount) || 0;
      return "₹ " + Math.round(amount).toLocaleString('en-IN');
  }
    // Function to reload report
    function fetchReport() {
      $.ajax({
          url: "{{ route('superadmin.vendorincomeReport') }}",
          type: "GET",
          data: $("#filterForm").serialize(),

          beforeSend: function () {
              $("#loader").show();          // show loader
              $("#incomeTableContainer").hide();  // hide table during loading (optional)
          },

          success: function (response) {
              $("#incomeTableContainer").html(response.html).show();
              $('.total-badge').text("Total: ₹ " + Number(response.total).toLocaleString('en-IN', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
              }));
          },

          complete: function () {
              $("#loader").hide();          // hide loader after success/error
          },
      });
  }


    // Location & perPage change
    $("#location, #perPage").on("change", fetchReport);

    // Date filter dropdown click
    $(".dropdown-item-custom").on("click", function () {
        let selected = $(this).data("value");
        $("#dateFilterInput").val(selected);
        $("#dateDropdown").text($(this).text());

        if (selected === "custom") {
            $("#customDateInputs").addClass("show");
        } else {
            $("#customDateInputs").removeClass("show");
            $("input[name=start_date], input[name=end_date]").val("");
        }

        fetchReport();
        $("#dateMenu").removeClass("show");
    });

    // When selecting custom dates
    $("input[name=start_date], input[name=end_date]").on("change", function () {
        $("#dateFilterInput").val("custom");
        $("#dateDropdown").text("Custom Date");
        fetchReport();
    });

    // Handle pagination ajax
    $(document).on("click", ".pagination a", function (e) {
        e.preventDefault();
        let url = $(this).attr("href");

        $.ajax({
            url: url,
            type: "GET",
            data: $("#filterForm").serialize(),
            success: function (response) {
                $("#incomeTableContainer").html(response.html);
            }
        });
    });

    function exportIncomeReport(type) {
      let params = {
          date_filter: $("#dateFilterInput").val(),
          start_date: $("#start_date").val(),
          end_date: $("#end_date").val(),
          location: $("#location").val(),
          export_type: type
      };

      $.ajax({
          url: "{{ route('incomeSummary.export') }}",
          type: "GET",
          data: params,
          xhrFields: {
              responseType: 'blob'  // RECEIVE FILE AS BLOB
          },
          success: function (data, status, xhr) {
              let blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') });

              // get filename from response header
              let fileName = "income_report";
              let disposition = xhr.getResponseHeader('content-disposition');

              if (disposition && disposition.indexOf('filename=') !== -1) {
                  fileName = disposition.split('filename=')[1].replace(/"/g, '');
              }

              let link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = fileName;
              document.body.appendChild(link);
              link.click();
              link.remove();
          },
          error: function () {
              alert("Download failed, try again.");
          }
      });
  }

  $("#btnCsv").click(function () {
      exportIncomeReport("csv");
  });

  $("#btnExcel").click(function () {
      exportIncomeReport("excel");
  });




});
</script>

<!-- [ Main Content ] end -->
@include('superadmin.superadminfooter')
</body>
<!-- [Body] end -->
</html>