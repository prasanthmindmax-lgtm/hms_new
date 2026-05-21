<!doctype html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('superadmin.superadminhead')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/branch-financial.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .swal-on-top {
            z-index: 99999 !important;
        }

        #tableLoadingOverlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        #tableLoadingOverlay.active {
            display: flex;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3em;
        }
    </style>
</head>

<body style="overflow-x: hidden;">
    <script type="text/javascript">
        document.addEventListener("contextmenu", function(e) {
            e.preventDefault();
        });
    </script>

    <div class="page-loader">
        <div class="bar"></div>
    </div>

    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
            <div class="branch-financial-container">

                <!-- Statistics Cards -->
                <!-- <div class="statistics-grid">
                    <div class="stat-card gradient-blue">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $statistics['total_reports'] }}</h3>
                            <p>Total Reports</p>
                        </div>
                    </div>

                    <div class="stat-card gradient-green">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $statistics['today_reports'] }}</h3>
                            <p>Today's Reports</p>
                        </div>
                    </div>

                    <div class="stat-card gradient-purple">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $statistics['this_month'] }}</h3>
                            <p>This Month</p>
                        </div>
                    </div>

                    <div class="stat-card gradient-teal">
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-content">
                            <h3>₹{{ number_format($statistics['total_radiant'], 2) }}</h3>
                            <p>Total Radiant</p>
                        </div>
                    </div>

                    <div class="stat-card gradient-pink">
                        <div class="stat-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="stat-content">
                            <h3>₹{{ number_format($statistics['total_bank'], 2) }}</h3>
                            <p>Total Bank Deposit</p>
                        </div>
                    </div>
                </div> -->

                <!-- Header with New Button -->
                <div class="page-header">
                    <h2><i class="fas fa-chart-line"></i> Branch Financial Reports</h2>
                    <button class="btn-new-report" id="openModalBtn">
                        <i class="fas fa-plus"></i> New Report
                    </button>
                </div>

                <!-- Table Container with Loading Overlay -->
                <div style="position: relative;">
                    <div id="tableLoadingOverlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="tableContainer">
                        @include('branch.partials.financial-table', ['reports' => $reports])
                    </div>
                </div>

            </div>

            @include('branch.partials.financial-report-form-modal', ['zones' => $zones, 'locations' => $locations])

        </div>
    </div>
<!-- BEFORE closing </body> tag -->

<!-- jQuery (if not in head) -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>

<!-- Moment.js -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Date Range Picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Flatpickr (optional) -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    // Define route variables for JavaScript
    const storeRoute = "{{ route('branch-financial.store') }}";
    const updateRoute = "{{ route('branch-financial.update', ':id') }}";
    const showRoute = "{{ route('branch-financial.show', ':id') }}";
    const destroyRoute = "{{ route('branch-financial.destroy', ':id') }}";
    const indexRoute = "{{ route('branch-financial.index') }}";

    // Current pagination state
    let currentPage = 1;
    let currentPerPage = {{ request('per_page', 10) }};
    </script>

    <script src="{{ asset('/assets/js/branch/branch-financial.js') }}"></script>

    <script>
    // ================================================
    // PAGINATION AJAX HANDLER
    // ================================================
    $(document).on('click', '.pagination-links .pagination a', function(e) {
        e.preventDefault();

        const url = $(this).attr('href');
        const page = new URL(url).searchParams.get('page');

        loadReports(page, currentPerPage);
    });

    // ================================================
    // PER PAGE CHANGE HANDLER
    // ================================================
    $(document).on('change', '#perPageSelect', function() {
        currentPerPage = $(this).val();
        loadReports(1, currentPerPage);
    });

    // ================================================
    // LOAD REPORTS VIA AJAX
    // ================================================
    function loadReports(page = 1, perPage = 10) {
        currentPage = page;
        currentPerPage = perPage;

        $('#tableLoadingOverlay').addClass('active');

        $.ajax({
            url: indexRoute,
            type: 'GET',
            data: {
                page: page,
                per_page: perPage
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#tableContainer').html(response);
                $('#tableLoadingOverlay').removeClass('active');

                $('html, body').animate({
                    scrollTop: $('#tableContainer').offset().top - 100
                }, 300);
            },
            error: function(xhr, status, error) {
                console.error('Error loading reports:', error);
                $('#tableLoadingOverlay').removeClass('active');

                if (window.toastr) {
                    toastr.error('Failed to load reports. Please try again.');
                } else {
                    alert('Failed to load reports. Please try again.');
                }
            }
        });
    }

    // ================================================
    // RELOAD TABLE AFTER SAVE/UPDATE/DELETE
    // ================================================
    function reloadTable() {
        loadReports(currentPage, currentPerPage);
    }

    $(document).ready(function() {
        $(document).on('ajaxSuccess', function(event, xhr, settings) {
            if (settings.url === storeRoute || settings.url.includes('/branch-financial/')) {
                setTimeout(function() {
                    reloadTable();
                }, 500);
            }
        });
    });

    function checkAmountFile(amountId, fileId, label) {
        let amount = parseFloat($(amountId).val() || 0);
        let files = $(fileId)[0].files.length;

        if (amount > 0 && files === 0) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: label + " file upload required",
                confirmButtonText: "OK"
            });
            return false;
        }
        return true;
    }

    $(document).on("click", "#submitBtn", function (e) {
        let ok = true;

        if (!checkAmountFile( "#radiant_collection_amount","#radiant_collection_files","Radiant Collection"
        )) ok = false;

        if (!checkAmountFile("#deposit_amount","#deposit_files","Deposit"
        )) ok = false;

        if (!checkAmountFile("#actual_card_amount","#actual_card_files","Actual Card"
        )) ok = false;

        if (!checkAmountFile("#upi_amount","#upi_files","UPI"
        )) ok = false;

        if (!checkAmountFile("#bank_deposit_amount","#bank_deposit_files","Bank Deposit"
        )) ok = false;

        if (!ok) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        }
    });
    </script>

    @include('superadmin.superadminfooter')
</body>
</html>
