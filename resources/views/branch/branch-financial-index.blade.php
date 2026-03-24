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

        .acknowledgement-section {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .acknowledgement-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .acknowledgement-checkbox input[type="checkbox"] {
            margin-top: 5px;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .acknowledgement-checkbox label {
            flex: 1;
            font-size: 14px;
            line-height: 1.5;
            cursor: pointer;
        }

        /* New Gradient Colors */
        /* .gradient-section.yellow {
            background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
        }

        .gradient-section.indigo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        } */


        /* Radiant Not Collected Styles */
        .custom-checkbox-wrapper {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #fee;
            border-left: 4px solid #e74c3c;
            border-radius: 6px;
        }

        .custom-checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
        }

        .custom-checkbox-wrapper label {
            margin: 0;
            cursor: pointer;
            font-weight: 600;
            color: #e74c3c;
        }

        .custom-checkbox-wrapper input[type="checkbox"]:checked + label {
            color: #c0392b;
            font-weight: 700;
        }

        #radiantRemarksContainer {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #ffc107;
        }

        #radiantRemarksContainer label {
            color: #e74c3c;
            font-weight: 600;
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
                <div class="statistics-grid">
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
                </div>

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

            <!-- Modal -->
            <div class="modal-overlay" id="reportModal">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3 id="modalTitle">
                            <i class="fas fa-file-invoice-dollar"></i> New Financial Report
                        </h3>
                        <button class="modal-close" id="closeModalBtn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="reportForm">
                            @csrf
                            <input type="hidden" id="reportId" name="report_id">

                            <!-- Hidden inputs for date range -->
                            <input type="hidden" name="radiant_collection_from_date" id="radiant_collection_from_date">
                            <input type="hidden" name="radiant_collection_to_date" id="radiant_collection_to_date">

                            <!-- Basic Info Section -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-info-circle"></i> Basic Information
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Report Date <span class="required">*</span></label>
                                        <input type="date" name="report_date" id="report_date" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Zone <span class="required">*</span></label>
                                        <select name="zone_id" id="zone_id" class="form-control" required>
                                            <option value="">Select Zone</option>
                                            @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}" data-name="{{ $zone->name }}">{{ $zone->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="zone_name" id="zone_name">
                                    </div>

                                    <div class="form-group">
                                        <label>Branch <span class="required">*</span></label>
                                        <select name="branch_id" id="branch_id" class="form-control" required>
                                            <option value="">Select Branch</option>
                                            @foreach($locations as $location)
                                            <option value="{{ $location->id }}"
                                                    data-zone="{{ $location->zone_id }}"
                                                    data-name="{{ $location->name }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="branch_name" id="branch_name">
                                    </div>
                                </div>
                            </div>

                            <!-- Radiant Cash Collection (UPDATED) -->
                            <div class="form-section gradient-section blue">
                                <h4 class="section-title">
                                    <i class="fas fa-wallet"></i> Radiant Cash Collection
                                </h4>

                                <!-- Checkbox for Radiant Not Collected -->
                                <div class="form-group full-width" style="margin-bottom: 15px;">
                                    <div class="custom-checkbox-wrapper">
                                        <input type="checkbox" name="radiant_not_collected" id="radiant_not_collected">
                                        <label for="radiant_not_collected">
                                            <i class="fas fa-exclamation-triangle"></i> Radiant Not Collected
                                        </label>
                                    </div>
                                </div>

                                <!-- Remarks Textarea (Hidden by default) -->
                                <div class="form-group full-width" id="radiantRemarksContainer" style="display: none; margin-bottom: 15px;">
                                    <label>Remarks for Not Collecting <span class="required">*</span></label>
                                    <textarea name="radiant_not_collected_remarks" id="radiant_not_collected_remarks" class="form-control" rows="3" placeholder="Please explain why radiant was not collected..."></textarea>
                                </div>

                                <div class="form-grid">
                                    <!-- Hidden Collection Date (Still saves to DB) -->
                                    <input type="hidden" name="radiant_collected_date" id="radiant_collected_date">

                                    <!-- Date Range Picker -->
                                    <div class="form-group">
                                        <label>Collection Date Range</label>
                                        <input type="text" name="radiant_date_range" id="radiant_date_range" class="form-control" placeholder="Select date range" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Collection Amount</label>
                                        <input type="number" step="0.01" name="radiant_collection_amount" id="radiant_collection_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments</label>
                                        <input type="file" name="radiant_collection_files[]" id="radiant_collection_files" class="form-control file-input" multiple>
                                        <div class="file-preview" id="radiant_collection_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Deposit Section (NEW) -->
                            <div class="form-section gradient-section indigo">
                                <h4 class="section-title">
                                    <i class="fas fa-hand-holding-usd"></i> Deposit
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Deposit Date</label>
                                        <input type="date" name="deposit_date" id="deposit_date" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Deposit Amount</label>
                                        <input type="number" step="0.01" name="deposit_amount" id="deposit_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments</label>
                                        <input type="file" name="deposit_files[]" id="deposit_files" class="form-control file-input" multiple>
                                        <div class="file-preview" id="deposit_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actual Card Amount -->
                            <div class="form-section gradient-section green">
                                <h4 class="section-title">
                                    <i class="fas fa-credit-card"></i> Actual Card Amount
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Actual Card Amount</label>
                                        <input type="number" step="0.01" name="actual_card_amount" id="actual_card_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments</label>
                                        <input type="file" name="actual_card_files[]" id="actual_card_files" class="form-control file-input" multiple>
                                        <div class="file-preview" id="actual_card_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- UPI Section (NEW) -->
                            <div class="form-section gradient-section yellow">
                                <h4 class="section-title">
                                    <i class="fas fa-mobile-alt"></i> UPI Collection
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>UPI Amount</label>
                                        <input type="number" step="0.01" name="upi_amount" id="upi_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments</label>
                                        <input type="file" name="upi_files[]" id="upi_files" class="form-control file-input" multiple>
                                        <div class="file-preview" id="upi_preview"></div>
                                    </div>
                                </div>
                            </div>



                            <!-- Direct Bank Deposit -->
                            <div class="form-section gradient-section purple">
                                <h4 class="section-title">
                                    <i class="fas fa-university"></i> Direct Bank Deposit
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Bank Deposit Amount</label>
                                        <input type="number" step="0.01" name="bank_deposit_amount" id="bank_deposit_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments</label>
                                        <input type="file" name="bank_deposit_files[]" id="bank_deposit_files" class="form-control file-input" multiple>
                                        <div class="file-preview" id="bank_deposit_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cashier Info -->
                            <div class="form-section gradient-section orange">
                                <h4 class="section-title">
                                    <i class="fas fa-user-tie"></i> Cashier Information
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Placed By Whom</label>
                                        <input type="text" name="placed_by_whom" id="placed_by_whom" class="form-control" placeholder="eg.12345 emp no">
                                    </div>

                                    <div class="form-group">
                                        <label>Locker By Whom</label>
                                        <input type="text" name="locker_by_whom" id="locker_by_whom" class="form-control" placeholder="eg.12345 emp no">
                                    </div>

                                    <div class="form-group">
                                        <label>Who Gave Radiant Cash</label>
                                        <input type="text" name="who_gave_radiant_cash" id="who_gave_radiant_cash" class="form-control" placeholder="eg.12345 emp no">
                                    </div>

                                    <div class="form-group">
                                        <label>Cash in Drawer</label>
                                        <input type="number" step="0.01" name="cash_in_drawer" id="cash_in_drawer" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments</label>
                                        <input type="file" name="cashier_info_files[]" id="cashier_info_files" class="form-control file-input" multiple>
                                        <div class="file-preview" id="cashier_info_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Amounts -->
                            <div class="form-section gradient-section teal">
                                <h4 class="section-title">
                                    <i class="fas fa-calculator"></i> Additional Amounts
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Today's Discount Amount</label>
                                        <input type="number" step="0.01" name="today_discount_amount" id="today_discount_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group">
                                        <label>Cancel Bill Amount</label>
                                        <input type="number" step="0.01" name="cancel_bill_amount" id="cancel_bill_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group">
                                        <label>Refund Bill Amount</label>
                                        <input type="number" step="0.01" name="refund_bill_amount" id="refund_bill_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group">
                                        <label>POS Refund</label>
                                        <input type="number" step="0.01" name="pos_refund_amount" id="pos_refund_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments</label>
                                        <input type="file" name="additional_amounts_files[]" id="additional_amounts_files" class="form-control file-input" multiple>
                                        <div class="file-preview" id="additional_amounts_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Acknowledgement Section -->
                            <div class="acknowledgement-section">
                                <h4 class="section-title">
                                    <i class="fas fa-check-circle"></i> Acknowledgement
                                </h4>
                                <div class="acknowledgement-checkbox">
                                    <input type="checkbox" name="acknowledgement_agreed" id="acknowledgement_agreed" required>
                                    <label for="acknowledgement_agreed">
                                        <strong>I acknowledge and agree</strong> that all the information provided in this financial report is accurate and complete to the best of my knowledge. I understand that this data will be used for financial reconciliation and reporting purposes. <span class="required">*</span>
                                    </label>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
                        <button type="button" class="btn-submit" id="submitBtn">
                            <i class="fas fa-save"></i> Save Report
                        </button>
                    </div>
                </div>
            </div>

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
