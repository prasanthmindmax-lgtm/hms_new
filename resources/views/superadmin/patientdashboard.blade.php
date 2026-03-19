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


.containerssss {

            background: #eef2f3;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        .headertablsss {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .photo img {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
        }
        .details {
            margin-top: 15px;
        }
        .details p {
            margin: 5px 0;
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
        .active {
            color: green;
            font-weight: bold;
        }
        .membership {
            color: red;
            font-weight: bold;
        }

        .visit-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
        }
        .visit-card h3 {
            color: #d66b00;
            margin: 0 0 10px;
            font-size: 17px;
        }
        .icons {
            margin-bottom: 10px;
        }
        .icons span {
            margin-right: 10px;
            color: #d66b00;
            cursor: pointer;
        }
        .balance {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            background: #6c757d;
        }
        .doctor {
            font-weight: bold;
            margin-top: 10px;
        }
        .date {
            font-style: italic;
            color: #666;
        }
        .patient-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .patient-details span {
            font-weight: bold;
            margin-right: 10px;
        }
        .patient-details {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .mobile-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .btn i {
            margin-right: 5px;
        }
        .info-icon {
            background: #17a2b8;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
                      aria-selected="true">Dashboard</button>
                  </li>
                </ul>
              </div>
</div>
</div><br>

<div class="row">
    <div class="col-sm-12">


                <div class="patient-info">
                    <div class="patient-details">
                        <div><span>Name:</span> {{ $phid_id?->name ?? '' }}</div>
                        <div><span>ID:</span> {{ $phid_id?->phid ?? '' }}</div>
                        <div><span>Age:</span> {{ $phid_id?->age ?? '' }}</div>
                        <div><span>Gender:</span> {{ $phid_id?->gender ?? '' }}</div>
                    </div>

                </div>


</div>
</div><br><br><br>



<div class="row">
                <div class="col-sm-3">

                    <div class="containerssss">
                        <div class="headertablsss">
                            <h5>Personal Details</h5>
                        </div>
                        <div class="photo">
                            <img src="../assets/images/immm.jpeg" alt="Profile Photo">
                        </div>
                        <div class="details">
                            <p><strong>Patient Status:</strong> <span class="active">Active</span></p>
                            <p><strong>Reg Date:</strong><span class="active">{{ \Carbon\Carbon::parse($phid_id?->date)->format('d-m-Y') }}</span></p>
                            <p><strong>Membership:</strong> <span class="membership">No membership card</span></p>
                            <p><strong>DOB:</strong><span class="active">{{$phid_id?->dob ? \Carbon\Carbon::parse($phid_id->dob)->format('d-m-Y') : '' }}</span></p>
                            <p><strong>Spouse Name/Age:</strong><span class="active"></span></p>
                            <p><strong>Status:</strong><span class="active"></span></p>
                            <p><strong>Address:</strong><span class="active">{{ ($phid_id?->street ?? '') . ', ' . ($phid_id?->city ?? '') . ', ' . ($phid_id?->state ?? '')}}</span></p>
                            <p><strong>Source:</strong><span class="active">{{ $phid_id?->ptsource ?? '' }}</span></p>
                            <p><strong>Guardian:</strong><span class="active"></span></p>
                            <p><strong>Referred By:</strong><span class="active">{{ $phid_id?->referred_by ?? '' }}</span></p>

                        </div>
                    </div>

            </div>


            <div class="col-sm-3">

                <div class="containerssss">
                    <div class="headertablsss">
                        <h5>Medical History</h5>
                    </div>

                    <div class="details">
                        <p><strong>Medical:</strong> <span class="active"></span></p>
                        <p><strong>Family:</strong> </p>
                        <p><strong>Drug:</strong> </p>
                        <p><strong>Social:</strong></p>
                        <p><strong>Allergies:</strong> </p>
                        <p><strong>Habits:</strong> </p>
                        <p><strong>Surgical:</strong> </p>
                        <p><strong>Source:</strong> </p>
                    </div><br><br>

                    <div class="headertablsss">
                        <h5>IP Visit Summary</h5>
                    </div>

                    <div class="details">
                        <table>
                            <tr>
                                <th>IPNo:594</th>
                                <th>DOA: 18/02/2025</th>
                                <th>DOD: 18/02/2025</th>
                            </tr>
                            <tr>
                                <td><strong>Medical</strong></td>
                                <td><strong>Medical</strong></td>
                                <td class="active"></td>
                            </tr>
                            <tr>
                                <td><strong>Family</strong></td>
                                <td><strong>Medical</strong></td>
                                <td></td>
                            </tr>

                            </tr>
                        </table>
                    </div>
                </div>

        </div>

        <div class="col-sm-5">

            <div class="containerssss">
                <div class="headertablsss">
                    <h5>OP Visit Summary</h5>
                </div>

                <div class="visit-card">
                    <h3>Chief Complaints: Consultation</h3>
                    <div class="icons">✏️ 📧 📞 ✅ 🔄</div>
                    <span class="balance">Balance: 0.00</span>
                    <p class="doctor">{{ $phid_id?->referred_by ?? '' }}</p>
                    <p class="date">18/03/2025 10:03</p>
                </div>

                <div class="visit-card">
                    <h3>Chief Complaints: Diagnostic</h3>
                    <div class="icons">✏️ 📧 📞 ✅ 🔄</div>
                    <span class="balance" style="background: #d66b00;">Balance: 10000.00</span>
                    <p class="doctor">{{ $phid_id?->referred_by ?? '' }}</p>
                    <p class="date">14/03/2025 11:14</p>
                </div>

                <div class="visit-card">
                    <h3>Chief Complaints: Others</h3>
                    <div class="icons">✏️ 📧 📞 ✅ 🔄</div>
                    <span class="balance">Balance: 0.00</span>
                    <p class="doctor">{{ $phid_id?->referred_by ?? '' }}</p>
                    <p class="date">18/03/2025 10:04</p>
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
