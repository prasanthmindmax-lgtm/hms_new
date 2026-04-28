<!doctype html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('superadmin.superadminhead')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('/assets/css/branch-financial.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <style>
        .header-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
        }
        .stat-card {
            padding: 20px;
            border-radius: 15px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-card-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .stat-card-6 { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
        .stat-card-7 { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        .stat-card-7 { background: linear-gradient(135deg, #6366f1 0%, #22d3ee 100%);}
        .stat-card-8 { background: linear-gradient(135deg, #ef4444 0%, #f59e0b 100%);}
        .stat-icon { font-size: 48px; opacity: 0.8; }
        .stat-content h6 { margin: 0; font-size: 14px; opacity: 0.9; }
        .stat-content h3 { margin: 5px 0 0 0; font-size: 12px; font-weight: bold; }
        .filter-header, .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
        }
        .table thead th {
            background: #1e293b !important;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 12px;
            border: none;
        }
        .table tbody tr { transition: background-color 0.2s ease; }
        .table tbody tr:hover { background-color: #f8f9fa; }
        .table tfoot tr { background: #e9ecef; }
        .card { border-radius: 10px; border: none; }
        .shadow-sm { box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important; }
        .badge { font-weight: 500; padding: 0.4em 0.8em; }
        .btn { border-radius: 8px; font-weight: 500; }

        /* Multi-Select Styles */
        :root {
            --primary-blue: #667eea;
            --success-green: #22c55e;
            --danger-red: #ef4444;
            --warning-orange: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-700: #374151;
            --gray-900: #111827;
        }
        .custom-multiselect { position: relative; width: 100%; }
        .multiselect-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.625rem 0.875rem;
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            min-height: 42px;
        }
        .multiselect-trigger:hover { border-color: var(--primary-blue); }
        .multiselect-trigger.active { border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .multiselect-placeholder { color: #6b7280; font-size: 0.875rem; }
        .multiselect-selected { display: flex; flex-wrap: wrap; gap: 0.375rem; flex: 1; }
        .selected-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.5rem;
            background: var(--primary-blue);
            color: white;
            border-radius: 6px;
            font-size: 0.8125rem;
            font-weight: 500;
        }
        .selected-tag .remove-tag { cursor: pointer; font-size: 1rem; line-height: 1; opacity: 0.8; transition: opacity 0.2s; }
        .selected-tag .remove-tag:hover { opacity: 1; }
        .multiselect-arrow { color: var(--gray-700); transition: transform 0.2s; font-size: 0.875rem; margin-left: 0.5rem; }
        .multiselect-trigger.active .multiselect-arrow { transform: rotate(180deg); }
        .multiselect-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 0.5rem;
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            display: none;
        }
        .multiselect-dropdown.show { display: block; }
        .multiselect-search { padding: 0.75rem; border-bottom: 1px solid var(--gray-200); position: sticky; top: 0; background: white; z-index: 1; }
        .multiselect-search input { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-200); border-radius: 6px; font-size: 0.875rem; outline: none; }
        .multiselect-search input:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .multiselect-options { padding: 0.5rem; }
        .multiselect-option { display: flex; align-items: center; gap: 0.625rem; padding: 0.625rem 0.75rem; cursor: pointer; border-radius: 6px; transition: background 0.15s ease; user-select: none; }
        .multiselect-option:hover { background: var(--gray-50); }
        .multiselect-option.selected { background: rgba(102, 126, 234, 0.08); }
        .multiselect-checkbox { width: 18px; height: 18px; border: 2px solid var(--gray-300); border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s; }
        .multiselect-option.selected .multiselect-checkbox { background: var(--primary-blue); border-color: var(--primary-blue); }
        .multiselect-checkbox::after { content: '✓'; color: white; font-size: 0.75rem; font-weight: bold; display: none; }
        .multiselect-option.selected .multiselect-checkbox::after { display: block; }
        .multiselect-label { font-size: 0.875rem; color: var(--gray-900); }
        .multiselect-clear { padding: 0.5rem 0.75rem; border-top: 1px solid var(--gray-200); display: flex; justify-content: center; }
        .btn-clear-selection { font-size: 0.8125rem; color: var(--danger-red); background: none; border: none; cursor: pointer; padding: 0.25rem 0.5rem; font-weight: 500; }
        .btn-clear-selection:hover { text-decoration: underline; }

        .date-range-input {
            padding: 0.625rem 0.875rem;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            width: 100%;
            cursor: pointer;
        }
        .date-range-input:focus { outline: none; border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .filter-label { font-weight: 600; font-size: 0.875rem; color: var(--gray-700); margin-bottom: 0.5rem; display: block; }

        .applied-filters { margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.625rem; align-items: center; }
        .applied-filters-label { font-weight: 600; font-size: 0.875rem; color: var(--gray-700); }
        .filter-chip { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.75rem; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 20px; font-size: 0.8125rem; color: var(--gray-700); }
        .filter-chip-label { font-weight: 600; }
        .filter-chip .remove { cursor: pointer; color: var(--gray-700); font-weight: bold; line-height: 1; }
        .filter-chip .remove:hover { color: var(--danger-red); }

        .filter-actions { display: flex; gap: 0.75rem; margin-top: 1.25rem; }
        .btn-clear-all { padding: 0.625rem 1.5rem; background: white; color: var(--gray-700); border: 2px solid var(--gray-200); border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.875rem; }
        .btn-clear-all:hover { border-color: var(--gray-300); background: var(--gray-50); }

        /* Auto-filter indicator */
        .auto-filter-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: #22c55e;
            font-weight: 600;
        }
        .auto-filter-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse-dot 1.5s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        .loading-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.9); display: none; align-items: center; justify-content: center; z-index: 100; border-radius: 10px; }
        .loading-overlay.active { display: flex; }
        .spinner { border: 3px solid var(--gray-200); border-top: 3px solid var(--primary-blue); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .approval-badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.8125rem; font-weight: 600; }
        .approval-pending { background: #fef3c7; color: #92400e; }
        .approval-approved { background: #d1fae5; color: #065f46; }
        .approval-rejected { background: #fee2e2; color: #991b1b; }

        .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .info-row { display: flex; padding: 0.75rem 0; border-bottom: 1px solid #e5e7eb; }
        .info-label { flex: 0 0 220px; font-weight: 600; color: #4b5563; }
        .info-value { flex: 1; color: #1f2937; }
        .approval-actions { display: flex; gap: 0.75rem; margin-top: 1rem; }

        /* Attachment preview styles */
        .attachment-section { margin-bottom: 1.5rem; }
        .attachment-section-title { font-weight: 700; font-size: 0.9rem; color: #374151; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb; }
        .attachment-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 0.75rem; }
        .attachment-item { position: relative; border: 2px solid #e5e7eb; border-radius: 10px; overflow: hidden; cursor: pointer; transition: all 0.2s; background: #f9fafb; }
        .attachment-item:hover { border-color: #667eea; box-shadow: 0 4px 12px rgba(102,126,234,0.2); transform: translateY(-2px); }
        .attachment-item img { width: 100%; height: 120px; object-fit: cover; display: block; }
        .attachment-item .file-icon { width: 100%; height: 120px; display: flex; align-items: center; justify-content: center; font-size: 3rem; }
        .attachment-item .file-name { padding: 0.4rem 0.5rem; font-size: 0.75rem; color: #374151; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; background: white; }
        .attachment-item .file-download { position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 5px; padding: 0.2rem 0.4rem; font-size: 0.7rem; cursor: pointer; }
        .attachment-item .file-download:hover { background: #667eea; }
        .no-attachments { text-align: center; padding: 2rem; color: #9ca3af; }

        /* Full image preview */
        .img-preview-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.85);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: zoom-out;
        }
        .img-preview-overlay img { max-width: 90vw; max-height: 90vh; border-radius: 8px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
        .img-preview-close { position: absolute; top: 20px; right: 30px; color: white; font-size: 2rem; cursor: pointer; line-height: 1; }

        /* Personnel/acknowledgment modal */
        .personnel-card { border: 2px solid #e5e7eb; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; transition: border-color 0.2s; }
        .personnel-card:hover { border-color: #667eea; }
        .personnel-role-badge { font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.7rem; border-radius: 20px; }
        .personnel-name { font-size: 1rem; font-weight: 700; color: #1f2937; margin-bottom: 0.25rem; }
        .personnel-username { font-size: 0.85rem; color: #6b7280; }

        @media (max-width: 768px) {
            .filter-actions { flex-direction: column; }
        }
    </style>
</head>

<body style="overflow-x: hidden;">
    <script type="text/javascript">
        document.addEventListener("contextmenu", function(e) { e.preventDefault(); });
    </script>

    <div class="page-loader"><div class="bar"></div></div>

    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
            <div class="container-fluid py-4">

                <!-- Page Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card header-card">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h2 class="text-white mb-1">
                                            <i class="fas fa-chart-line me-2"></i>Financial Reports
                                        </h2>
                                        <p class="text-white-50 mb-0">View and manage branch financial data</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        @if($admin->access_limits == 1)
                                            <span class="badge bg-light text-dark px-3 py-2 fs-6"><i class="fas fa-crown me-2"></i>Superadmin</span>
                                        @elseif($admin->access_limits == 4)
                                            <span class="badge bg-warning text-dark px-3 py-2 fs-6"><i class="fas fa-user-shield me-2"></i>Auditor</span>
                                        @elseif($admin->access_limits == 2)
                                            <span class="badge bg-info text-dark px-3 py-2 fs-6"><i class="fas fa-map-marked me-2"></i>Zonal Admin</span>
                                        @elseif($admin->access_limits == 3)
                                            <span class="badge bg-success text-white px-3 py-2 fs-6"><i class="fas fa-user-tie me-2"></i>Admin</span>
                                        @else
                                            <span class="badge bg-secondary text-white px-3 py-2 fs-6"><i class="fas fa-user me-2"></i>User</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-1">
                            <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            <div class="stat-content"><h6>Total Reports</h6><h3>{{ number_format($summary['report_count']) }}</h3></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-2">
                            <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                            <div class="stat-content"><h6>Total Radiant</h6><h3>₹{{ number_format($summary['total_radiant'], 2) }}</h3></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-3">
                            <div class="stat-icon"><i class="fas fa-credit-card"></i></div>
                            <div class="stat-content"><h6>Total Card</h6><h3>₹{{ number_format($summary['total_card'], 2) }}</h3></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-4">
                            <div class="stat-icon"><i class="fas fa-mobile-alt"></i></div>
                            <div class="stat-content"><h6>Total UPI</h6><h3>₹{{ number_format($summary['total_upi'], 2) }}</h3></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-5">
                            <div class="stat-icon"><i class="fas fa-piggy-bank"></i></div>
                            <div class="stat-content"><h6>Total Deposit</h6><h3>₹{{ number_format($summary['total_deposit'], 2) }}</h3></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-6">
                            <div class="stat-icon"><i class="fas fa-university"></i></div>
                            <div class="stat-content"><h6>Total Bank</h6><h3>₹{{ number_format($summary['total_bank'], 2) }}</h3></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-7">
                            <div class="stat-icon"><i class="fas fa-paperclip"></i></div>
                            <div class="stat-content">
                                <h6>Files Uploaded</h6>
                                <h3>{{ $summary['reports_with_files'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-8">
                            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                            <div class="stat-content">
                                <h6>No Files</h6>
                                <h3>{{ $summary['reports_without_files'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Card -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header filter-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-filter me-2"></i>Filters & Export
                            </h5>
                            <div class="auto-filter-indicator">
                                <span class="auto-filter-dot"></span>
                                Auto-filter active
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="filterForm">
                            <div class="row g-3">
                                <!-- Date Range Picker -->
                                <div class="col-md-4">
                                    <label class="filter-label"><i class="fas fa-calendar-alt me-1"></i>Date Range</label>
                                    <input type="text" class="date-range-input" name="date_range" id="dateRange" placeholder="Select date range" value="{{ request('date_range') }}" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">Zone</label>
                                    <div class="custom-multiselect" id="zoneMultiselect">
                                        <div class="multiselect-trigger">
                                            <div class="multiselect-selected">
                                                <span class="multiselect-placeholder">Select Zone</span>
                                            </div>
                                            <span class="multiselect-arrow">▼</span>
                                        </div>
                                        <div class="multiselect-dropdown">
                                            <div class="multiselect-search">
                                                <input type="text" placeholder="Search zones..." class="search-input">
                                            </div>
                                            <div class="multiselect-options">
                                                @foreach($zones as $zone)
                                                <div class="multiselect-option" data-value="{{ $zone->id }}" data-label="{{ $zone->name }}">
                                                    <div class="multiselect-checkbox"></div>
                                                    <span class="multiselect-label">{{ $zone->name }}</span>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="multiselect-clear">
                                                <button type="button" class="btn-clear-selection">Clear Selection</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">Branch</label>
                                    <div class="custom-multiselect" id="branchMultiselect">
                                        <div class="multiselect-trigger">
                                            <div class="multiselect-selected">
                                                <span class="multiselect-placeholder">Select Branch</span>
                                            </div>
                                            <span class="multiselect-arrow">▼</span>
                                        </div>
                                        <div class="multiselect-dropdown">
                                            <div class="multiselect-search">
                                                <input type="text" placeholder="Search branches..." class="search-input">
                                            </div>
                                            <div class="multiselect-options">
                                                @foreach($branches as $branch)
                                                <div class="multiselect-option" data-value="{{ $branch->id }}" data-label="{{ $branch->name }}" data-zone="{{ $branch->zone_id }}">
                                                    <div class="multiselect-checkbox"></div>
                                                    <span class="multiselect-label">{{ $branch->name }}</span>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="multiselect-clear">
                                                <button type="button" class="btn-clear-selection">Clear Selection</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Export Buttons + Clear (no Apply button - auto filter) -->
                            <div class="filter-actions">
                                <button type="button" class="btn-clear-all" id="clearFilters">
                                    <i class="fas fa-times me-2"></i>Clear All Filters
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportExcel()">
                                    <i class="fas fa-file-excel me-2"></i>Export Excel
                                </button>
                                <button type="button" class="btn btn-info" onclick="exportCsv()">
                                    <i class="fas fa-file-csv me-2"></i>Export CSV
                                </button>
                            </div>

                            <!-- Applied Filters Display -->
                            <div id="appliedFilters" class="applied-filters"></div>
                        </form>
                    </div>
                </div>

                <!-- Table Container -->
                <div style="position: relative;">
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="spinner"></div>
                    </div>
                    <div id="tableContainer">
                        @include('branch.partials.reports-table', [
                            'reports' => $reports,
                            'summary' => $summary,
                            'admin' => $admin,
                            'allowedBranchIdsForZonalApprove' => $allowedBranchIdsForZonalApprove,
                        ])
                    </div>
                </div>

            </div>

            <!-- ===================== VIEW REPORT MODAL ===================== -->
            <div class="modal fade" id="viewReportModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-file-invoice me-2"></i>Financial Report Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="reportDetailsContent">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== ATTACHMENTS MODAL ===================== -->
            <div class="modal fade" id="attachmentsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <h5 class="modal-title text-white">
                                <i class="fas fa-paperclip me-2"></i>
                                Report Attachments
                                <span id="attachmentReportInfo" class="ms-2 badge bg-white text-dark" style="font-size:0.8rem;"></span>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="attachmentsContent">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2 text-muted">Loading attachments...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== ACKNOWLEDGMENT / PERSONNEL MODAL ===================== -->
            <div class="modal fade" id="acknowledgmentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <h5 class="modal-title text-white">
                                <i class="fas fa-users me-2"></i>Personnel Details
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="acknowledgmentContent">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== ZONAL HEAD REJECT MODAL (first-level; route: reject.auditor) ===================== -->
            <div class="modal fade" id="auditorRejectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Zonal Head Rejection</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="auditorRejectForm">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="auditor_rejection_remarks" rows="4" placeholder="Enter reason for rejection..." required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" onclick="confirmAuditorReject()">
                                <i class="fas fa-times-circle me-2"></i>Reject Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== MANAGEMENT REJECT MODAL ===================== -->
            <div class="modal fade" id="managementRejectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-ban me-2"></i>Management Rejection</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="managementRejectForm">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="management_rejection_remarks" rows="4" placeholder="Enter reason for rejection..." required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" onclick="confirmManagementReject()">
                                <i class="fas fa-ban me-2"></i>Reject Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

<!-- Image Full Preview Overlay (outside modals) -->
<div class="img-preview-overlay" id="imgPreviewOverlay" style="display:none;" onclick="closeImagePreview()">
    <span class="img-preview-close" onclick="closeImagePreview()">✕</span>
    <img id="imgPreviewSrc" src="" alt="Preview">
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    // ==================== ROUTE VARIABLES ====================
    const indexRoute             = "{{ route('financial-reports.index') }}";
    const showRoute              = "{{ route('financial-reports.show', ':id') }}";
    const attachmentsRoute       = "{{ route('financial-reports.attachments', ':id') }}";
    const approveAuditorRoute    = "{{ route('financial-reports.approve.auditor', ':id') }}";
    const rejectAuditorRoute     = "{{ route('financial-reports.reject.auditor', ':id') }}";
    const approveManagementRoute = "{{ route('financial-reports.approve.management', ':id') }}";
    const rejectManagementRoute  = "{{ route('financial-reports.reject.management', ':id') }}";
    const exportExcelRoute       = "{{ route('financial-reports.export.excel') }}";
    const exportCsvRoute         = "{{ route('financial-reports.export.csv') }}";

    const userAccessLevel = {{ $admin->access_limits }};
    const hasZoneFilter   = {{ ($admin->access_limits !== 6) ? 'true' : 'false' }};
    const hasBranchFilter = {{ ($admin->access_limits != 6)  ? 'true' : 'false' }};

    let currentReportId = null;

    // Toastr config
    toastr.options = { closeButton: true, progressBar: true, positionClass: "toast-top-right", timeOut: "3000" };

    // ==================== MULTI-SELECT CLASS ====================
    class MultiSelect {
        constructor(elementId, name, onChange = null) {
            this.element = $(`#${elementId}`);
            if (!this.element.length) return;
            this.name = name;
            this.onChange = onChange;
            this.selectedValues = [];
            this.allOptions = [];
            this.trigger          = this.element.find('.multiselect-trigger');
            this.dropdown         = this.element.find('.multiselect-dropdown');
            this.searchInput      = this.element.find('.search-input');
            this.optionsContainer = this.element.find('.multiselect-options');
            this.clearBtn         = this.element.find('.btn-clear-selection');
            this.selectedContainer = this.element.find('.multiselect-selected');
            this.init();
        }
        init() {
            this.allOptions = this.optionsContainer.find('.multiselect-option').toArray();
            this.bindEvents();
        }
        bindEvents() {
            this.trigger.on('click', (e) => { e.stopPropagation(); this.toggleDropdown(); });
            this.searchInput.on('input', (e) => { this.filterOptions($(e.target).val()); });
            this.allOptions.forEach(option => {
                $(option).on('click', (e) => { e.stopPropagation(); this.toggleOption($(option)); });
            });
            this.clearBtn.on('click', (e) => { e.stopPropagation(); this.clearAll(); });
            $(document).on('click', (e) => { if (!this.element[0].contains(e.target)) this.closeDropdown(); });
        }
        toggleDropdown() {
            const isActive = this.dropdown.hasClass('show');
            $('.multiselect-dropdown.show').removeClass('show');
            $('.multiselect-trigger.active').removeClass('active');
            if (!isActive) {
                this.dropdown.addClass('show');
                this.trigger.addClass('active');
                this.searchInput.focus();
            }
        }
        closeDropdown() {
            this.dropdown.removeClass('show');
            this.trigger.removeClass('active');
            this.searchInput.val('');
            this.filterOptions('');
        }
        toggleOption(option) {
            const value = option.data('value');
            const index = this.selectedValues.indexOf(value);
            if (index > -1) {
                this.selectedValues.splice(index, 1);
                option.removeClass('selected');
            } else {
                this.selectedValues.push(value);
                option.addClass('selected');
            }
            this.updateDisplay();
            if (this.onChange) this.onChange(this.selectedValues);
        }
        updateDisplay() {
            this.selectedContainer.empty();
            if (this.selectedValues.length === 0) {
                this.selectedContainer.append($('<span>', { class: 'multiselect-placeholder', text: `Select ${this.name}` }));
            } else {
                this.selectedValues.forEach(value => {
                    const option = this.allOptions.find(opt => $(opt).data('value') === value);
                    if (option) {
                        const tag = $(`<div class="selected-tag"><span>${$(option).data('label')}</span><span class="remove-tag" data-value="${value}">×</span></div>`);
                        tag.find('.remove-tag').on('click', (e) => { e.stopPropagation(); this.removeValue(value); });
                        this.selectedContainer.append(tag);
                    }
                });
            }
        }
        removeValue(value) {
            const index = this.selectedValues.indexOf(value);
            if (index > -1) {
                this.selectedValues.splice(index, 1);
                const option = this.allOptions.find(opt => $(opt).data('value') === value);
                if (option) $(option).removeClass('selected');
                this.updateDisplay();
                if (this.onChange) this.onChange(this.selectedValues);
            }
        }
        filterOptions(searchTerm) {
            const term = searchTerm.toLowerCase();
            this.allOptions.forEach(option => {
                const label = $(option).data('label').toLowerCase();
                $(option).toggle(label.includes(term));
            });
        }
        clearAll() {
            this.selectedValues = [];
            this.allOptions.forEach(option => $(option).removeClass('selected'));
            this.updateDisplay();
            if (this.onChange) this.onChange(this.selectedValues);
        }
        getValues() { return this.selectedValues; }
        setValues(values) {
            this.selectedValues = values;
            this.allOptions.forEach(option => {
                const optValue = $(option).data('value');
                if (values.includes(optValue) || values.includes(String(optValue))) $(option).addClass('selected');
                else $(option).removeClass('selected');
            });
            this.updateDisplay();
        }
        filterOptionsByAttribute(attribute, values) {
            this.allOptions.forEach(option => {
                const attrValue = $(option).data(attribute);
                if (values.length === 0) $(option).show();
                else if (values.includes(attrValue) || values.includes(String(attrValue))) $(option).show();
                else $(option).hide();
            });
        }
    }

    // ==================== INIT ====================
    let zoneSelect, branchSelect;
    const branchesData = @json($branches);
    const zonesData    = @json($zones);

    // Auto-filter debounce timer
    let autoFilterTimer = null;
    const AUTO_FILTER_DELAY = 600; // ms debounce

    function scheduleAutoFilter() {
        clearTimeout(autoFilterTimer);
        autoFilterTimer = setTimeout(() => { applyFilters(); }, AUTO_FILTER_DELAY);
    }

    jQuery(document).ready(function ($) {

        // Date Range Picker
        if (typeof $.fn.daterangepicker === 'function') {
            $('#dateRange').daterangepicker({
                autoUpdateInput: false,
                locale: { cancelLabel: 'Clear', format: 'YYYY-MM-DD' },
                ranges: {
                    'Today':        [moment(), moment()],
                    'Yesterday':    [moment().subtract(1,'days'), moment().subtract(1,'days')],
                    'Last 7 Days':  [moment().subtract(6,'days'), moment()],
                    'Last 30 Days': [moment().subtract(29,'days'), moment()],
                    'This Month':   [moment().startOf('month'), moment().endOf('month')],
                    'Last Month':   [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
                }
            });

            $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
                const dateStr = picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD');
                $(this).val(dateStr);
                scheduleAutoFilter(); // AUTO FILTER on date select
            });

            $('#dateRange').on('cancel.daterangepicker', function() {
                $(this).val('');
                scheduleAutoFilter(); // AUTO FILTER on date clear
            });
        }

        // Zone MultiSelect with auto-filter on change
        if (hasZoneFilter) {
            zoneSelect = new MultiSelect('zoneMultiselect', 'Zone', function(selectedZones) {
                // Cascade branch filter
                if (hasBranchFilter && branchSelect) {
                    if (selectedZones.length > 0) {
                        branchSelect.filterOptionsByAttribute('zone', selectedZones);
                        const currentBranches = branchSelect.getValues();
                        const validBranches = currentBranches.filter(branchId => {
                            const branch = branchesData.find(b => String(b.id) === String(branchId));
                            return branch && selectedZones.includes(String(branch.zone_id));
                        });
                        if (validBranches.length !== currentBranches.length) {
                            branchSelect.setValues(validBranches);
                        }
                    } else {
                        branchSelect.filterOptionsByAttribute('zone', []);
                    }
                }
                scheduleAutoFilter(); // AUTO FILTER on zone change
            });
        }

        // Branch MultiSelect with auto-filter on change
        if (hasBranchFilter) {
            branchSelect = new MultiSelect('branchMultiselect', 'Branch', function(selectedBranches) {
                scheduleAutoFilter(); // AUTO FILTER on branch change
            });
        }

        loadFiltersFromURL();
        displayAppliedFilters();
    });

    // Clear All
    $('#clearFilters').on('click', function() {
        $('#dateRange').val('');
        if (zoneSelect) zoneSelect.clearAll();
        if (branchSelect) branchSelect.clearAll();
        displayAppliedFilters();
        window.location.href = window.location.pathname;
    });

    // ==================== APPLY FILTERS ====================
    function applyFilters(page = 1, perPage = null) {
        // Clean params for the browser URL (no ajax/page/per_page pollution)
        const cleanParams = new URLSearchParams();

        const dateRange = $('#dateRange').val();
        if (dateRange) cleanParams.append('date_range', dateRange);

        if (zoneSelect) {
            zoneSelect.getValues().forEach(zone => cleanParams.append('zone_id[]', zone));
        }

        if (branchSelect) {
            branchSelect.getValues().forEach(branch => cleanParams.append('branch_id[]', branch));
        }

        // AJAX params include page, per_page and ajax=1 (never pushed to browser URL)
        const ajaxParams = new URLSearchParams(cleanParams.toString());
        if (page) ajaxParams.append('page', page);
        if (perPage) ajaxParams.append('per_page', perPage);
        else if ($('#perPageSelect').length) ajaxParams.append('per_page', $('#perPageSelect').val());
        ajaxParams.append('ajax', '1');

        const url = `${indexRoute}?${ajaxParams.toString()}`;

        $('#loadingOverlay').addClass('active');

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() { $('#tableContainer').css('opacity', '0.5'); },
            success: function(response) {
                updateSummaryCards(response.summary);
                $('#tableContainer').html(response.table);
                displayAppliedFilters();
                // Push only the clean params (no ajax=1, no page, no per_page)
                const browserUrl = cleanParams.toString()
                    ? `${window.location.pathname}?${cleanParams.toString()}`
                    : window.location.pathname;
                window.history.pushState({}, '', browserUrl);
                $('#loadingOverlay').removeClass('active');
                $('#tableContainer').css('opacity', '1');
            },
            error: function(xhr) {
                console.error('Filter error:', xhr);
                $('#loadingOverlay').removeClass('active');
                $('#tableContainer').css('opacity', '1');
                toastr.error('An error occurred while filtering.');
            }
        });
    }
    function updateSummaryCards(summary) {
            $('.stat-card-1 h3').text(summary.report_count);
            $('.stat-card-2 h3').text('₹' + Number(summary.total_radiant).toLocaleString());
            $('.stat-card-3 h3').text('₹' + Number(summary.total_card).toLocaleString());
            $('.stat-card-4 h3').text('₹' + Number(summary.total_upi).toLocaleString());
            $('.stat-card-5 h3').text('₹' + Number(summary.total_deposit).toLocaleString());
            $('.stat-card-6 h3').text('₹' + Number(summary.total_bank).toLocaleString());
            $('.stat-card-7 h3').text(summary.reports_with_files);
            $('.stat-card-8 h3').text(summary.reports_without_files);
        }

    // ==================== DISPLAY APPLIED FILTERS ====================
    function displayAppliedFilters() {
        const container = $('#appliedFilters');
        container.empty();
        const filters = [];

        const dateRange = $('#dateRange').val();
        if (dateRange) {
            filters.push({ label: 'Date Range', value: dateRange, clearFn: () => { $('#dateRange').val(''); displayAppliedFilters(); applyFilters(); } });
        }

        if (zoneSelect) {
            zoneSelect.getValues().forEach(zoneId => {
                const zone = zonesData.find(z => z.id == zoneId);
                if (zone) {
                    filters.push({ label: 'Zone', value: zone.name, clearFn: () => { zoneSelect.removeValue(zoneId); displayAppliedFilters(); applyFilters(); } });
                }
            });
        }

        if (branchSelect) {
            branchSelect.getValues().forEach(branchId => {
                const branch = branchesData.find(b => b.id == branchId);
                if (branch) {
                    filters.push({ label: 'Branch', value: branch.name, clearFn: () => { branchSelect.removeValue(branchId); displayAppliedFilters(); applyFilters(); } });
                }
            });
        }

        if (filters.length > 0) {
            container.append($('<span>', { class: 'applied-filters-label', text: 'Applied Filters:' }));
            filters.forEach(filter => {
                const chip = $(`<div class="filter-chip"><span class="filter-chip-label">${filter.label}:</span><span>${filter.value}</span><span class="remove">×</span></div>`);
                chip.find('.remove').on('click', () => filter.clearFn());
                container.append(chip);
            });
        }
    }

    // ==================== LOAD FILTERS FROM URL ====================
    function loadFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('date_range')) $('#dateRange').val(urlParams.get('date_range'));
        if (zoneSelect && urlParams.has('zone_id[]')) zoneSelect.setValues(urlParams.getAll('zone_id[]').map(z => parseInt(z)));
        if (branchSelect && urlParams.has('branch_id[]')) branchSelect.setValues(urlParams.getAll('branch_id[]').map(b => parseInt(b)));
    }

    // ==================== EXPORT ====================
    function exportExcel() {
        const params = new URLSearchParams();
        const dateRange = $('#dateRange').val();
        if (dateRange) params.append('date_range', dateRange);
        if (zoneSelect) zoneSelect.getValues().forEach(z => params.append('zone_id[]', z));
        if (branchSelect) branchSelect.getValues().forEach(b => params.append('branch_id[]', b));
        window.location.href = exportExcelRoute + '?' + params.toString();
    }

    function exportCsv() {
        const params = new URLSearchParams();
        const dateRange = $('#dateRange').val();
        if (dateRange) params.append('date_range', dateRange);
        if (zoneSelect) zoneSelect.getValues().forEach(z => params.append('zone_id[]', z));
        if (branchSelect) branchSelect.getValues().forEach(b => params.append('branch_id[]', b));
        window.location.href = exportCsvRoute + '?' + params.toString();
    }

    // ==================== VIEW REPORT MODAL ====================
    function viewReport(reportId) {
        const modal = new bootstrap.Modal($('#viewReportModal')[0]);
        modal.show();

        $.ajax({
            url: showRoute.replace(':id', reportId),
            method: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            beforeSend: function() {
                $('#reportDetailsContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading...</p></div>');
            },
            success: function(data) {
                currentReportId = reportId;
                renderReportDetails(data.report, data.can_approve_auditor, data.can_approve_management);
            },
            error: function(xhr) {
                let msg = 'Error loading report';
                if (xhr.status === 404) msg = 'Report not found';
                else if (xhr.status === 403) msg = 'Access denied';
                $('#reportDetailsContent').html(`<p class="text-danger text-center">${msg}</p>`);
            }
        });
    }

    function renderReportDetails(report, canApproveAuditor, canApproveManagement) {
        let html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                <div class="info-row"><div class="info-label">Report Date:</div><div class="info-value"><strong>${moment(report.report_date).format('DD MMM YYYY')}</strong></div></div>`;
        
        // Radiant Collection Date Range
        if (report.radiant_collection_from_date && report.radiant_collection_to_date) {
            html += `<div class="info-row"><div class="info-label">Radiant Collection Period:</div><div class="info-value">${moment(report.radiant_collection_from_date).format('DD MMM YYYY')} - ${moment(report.radiant_collection_to_date).format('DD MMM YYYY')}</div></div>`;
        } else if (report.radiant_collected_date) {
            html += `<div class="info-row"><div class="info-label">Radiant Collected Date:</div><div class="info-value">${moment(report.radiant_collected_date).format('DD MMM YYYY')}</div></div>`;
        }
        
        // Deposit Date
        if (report.deposit_date) {
            html += `<div class="info-row"><div class="info-label">Deposit Date:</div><div class="info-value">${moment(report.deposit_date).format('DD MMM YYYY')}</div></div>`;
        }
        
        html += `
                <div class="info-row"><div class="info-label">Zone:</div><div class="info-value"><span class="badge bg-info">${report.zone_name || 'N/A'}</span></div></div>
                <div class="info-row"><div class="info-label">Branch:</div><div class="info-value"><strong>${report.branch_name || 'N/A'}</strong></div></div>
                <div class="info-row"><div class="info-label">Created By:</div><div class="info-value">${report.creator ? report.creator.user_fullname : 'N/A'}</div></div>
                <div class="info-row"><div class="info-label">Created At:</div><div class="info-value">${moment(report.created_at).format('DD MMM YYYY hh:mm A')}</div></div>
            </div>
            <div class="col-md-6">
                <h6 class="text-success mb-3"><i class="fas fa-money-bill-wave me-2"></i>Financial Details</h6>`;
        
        // Radiant Collection with "Not Collected" indicator
        if (report.radiant_not_collected) {
            html += `<div class="info-row"><div class="info-label">Radiant Collection:</div><div class="info-value"><span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Not Collected</span>`;
            if (report.radiant_not_collected_remarks) {
                html += `<br><small class="text-muted">${report.radiant_not_collected_remarks}</small>`;
            }
            html += `</div></div>`;
        } else {
            html += `<div class="info-row"><div class="info-label">Radiant Collection:</div><div class="info-value text-success fw-bold">₹${Number(report.radiant_collection_amount||0).toLocaleString('en-IN',{minimumFractionDigits:2})}</div></div>`;
        }
        
        html += `
                <div class="info-row"><div class="info-label">Card Amount:</div><div class="info-value text-primary fw-bold">₹${Number(report.actual_card_amount||0).toLocaleString('en-IN',{minimumFractionDigits:2})}</div></div>
                <div class="info-row"><div class="info-label">UPI Amount:</div><div class="info-value text-info fw-bold">₹${Number(report.upi_amount||0).toLocaleString('en-IN',{minimumFractionDigits:2})}</div></div>
                <div class="info-row"><div class="info-label">Deposit Amount:</div><div class="info-value" style="color: #14b8a6; font-weight: bold;">₹${Number(report.deposit_amount||0).toLocaleString('en-IN',{minimumFractionDigits:2})}</div></div>
                <div class="info-row"><div class="info-label">Bank Deposit:</div><div class="info-value text-warning fw-bold">₹${Number(report.bank_deposit_amount||0).toLocaleString('en-IN',{minimumFractionDigits:2})}</div></div>
                <div class="info-row"><div class="info-label">Discount Amount:</div><div class="info-value text-danger">₹${Number(report.today_discount_amount||0).toLocaleString('en-IN',{minimumFractionDigits:2})}</div></div>
                <div class="info-row"><div class="info-label">Cancel Amount:</div><div class="info-value text-danger">₹${Number(report.cancel_bill_amount||0).toLocaleString('en-IN',{minimumFractionDigits:2})}</div></div>
                <div class="info-row"><div class="info-label">Refund Amount:</div><div class="info-value text-danger">₹${Number(report.refund_bill_amount||0).toLocaleString('en-IN',{minimumFractionDigits:2})}</div></div>
            </div>
        </div>
        <hr class="my-4">
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-warning mb-3"><i class="fas fa-check-circle me-2"></i>Approval Status</h6>
                <div class="info-row"><div class="info-label">Zonal Head Status:</div><div class="info-value">`;

        if (report.auditor_approval_status == 0)      html += '<span class="approval-badge approval-pending"><i class="fas fa-clock me-1"></i>Pending</span>';
        else if (report.auditor_approval_status == 1)  html += '<span class="approval-badge approval-approved"><i class="fas fa-check-circle me-1"></i>Approved</span>';
        else if (report.auditor_approval_status == 2)  html += '<span class="approval-badge approval-rejected"><i class="fas fa-times-circle me-1"></i>Rejected</span>';

        html += `</div></div>`;

        if (report.auditor_approval_status != 0 && report.auditor_approved_by) {
            html += `<div class="info-row"><div class="info-label">Zonal Head Reviewed By:</div><div class="info-value">${report.auditor_approved_by.user_fullname||'N/A'}</div></div>`;
            html += `<div class="info-row"><div class="info-label">Zonal Head Review Date:</div><div class="info-value">${report.auditor_approved_at ? moment(report.auditor_approved_at).format('DD MMM YYYY hh:mm A') : 'N/A'}</div></div>`;
            if (report.auditor_approval_remarks) html += `<div class="info-row"><div class="info-label">Zonal Head Remarks:</div><div class="info-value">${report.auditor_approval_remarks}</div></div>`;
        }

        html += `<div class="info-row mt-3"><div class="info-label">Management Status:</div><div class="info-value">`;

        if (report.auditor_approval_status != 1)       html += '<span class="text-muted"><i class="fas fa-minus-circle me-1"></i>Awaiting Zonal Head Approval</span>';
        else if (report.management_approval_status == 0) html += '<span class="approval-badge approval-pending"><i class="fas fa-clock me-1"></i>Pending</span>';
        else if (report.management_approval_status == 1) html += '<span class="approval-badge approval-approved"><i class="fas fa-check-circle me-1"></i>Approved</span>';
        else if (report.management_approval_status == 2) html += '<span class="approval-badge approval-rejected"><i class="fas fa-times-circle me-1"></i>Rejected</span>';

        html += `</div></div>`;

        if (report.management_approval_status != 0 && report.management_approved_by) {
            html += `<div class="info-row"><div class="info-label">Management Reviewed By:</div><div class="info-value">${report.management_approved_by.user_fullname||'N/A'}</div></div>`;
            html += `<div class="info-row"><div class="info-label">Management Review Date:</div><div class="info-value">${report.management_approved_at ? moment(report.management_approved_at).format('DD MMM YYYY hh:mm A') : 'N/A'}</div></div>`;
            if (report.management_approval_remarks) html += `<div class="info-row"><div class="info-label">Management Remarks:</div><div class="info-value">${report.management_approval_remarks}</div></div>`;
        }

        html += `</div></div>`;
        html += '<div class="approval-actions">';
        if (canApproveAuditor) {
            html += `<button class="btn btn-success" onclick="approveReportAuditor(${report.id})"><i class="fas fa-check-circle me-2"></i>Zonal Head Approve</button>`;
            html += `<button class="btn btn-danger" onclick="openRejectModalAuditor(${report.id})"><i class="fas fa-times-circle me-2"></i>Zonal Head Reject</button>`;
        }
        if (canApproveManagement) {
            html += `<button class="btn btn-primary" onclick="approveReportManagement(${report.id})"><i class="fas fa-check-double me-2"></i>Management Approve</button>`;
            html += `<button class="btn btn-warning" onclick="openRejectModalManagement(${report.id})"><i class="fas fa-ban me-2"></i>Management Reject</button>`;
        }
        html += '</div>';
        $('#reportDetailsContent').html(html);
    }

    // ==================== ATTACHMENTS MODAL ====================
    function viewAttachments(reportId) {
        const modal = new bootstrap.Modal($('#attachmentsModal')[0]);
        modal.show();

        $('#attachmentReportInfo').text('');
        $('#attachmentsContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading attachments...</p></div>');

        $.ajax({
            url: attachmentsRoute.replace(':id', reportId),
            method: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(data) {
                if (data.success) {
                    $('#attachmentReportInfo').text((data.branch_name || '') + (data.report_date ? ' · ' + moment(data.report_date).format('DD MMM YYYY') : ''));
                    renderAttachments(data.attachments);
                } else {
                    $('#attachmentsContent').html('<p class="text-danger text-center">Failed to load attachments.</p>');
                }
            },
            error: function(xhr) {
                let msg = 'Error loading attachments';
                if (xhr.status === 403) msg = 'Access denied';
                $('#attachmentsContent').html(`<p class="text-danger text-center">${msg}</p>`);
            }
        });
    }

    function renderAttachments(attachments) {
        let anyFiles = false;
        let html = '';

        for (const [key, section] of Object.entries(attachments)) {
            const files = section.files || [];
            if (!Array.isArray(files) || files.length === 0) continue;
            anyFiles = true;

            html += `<div class="attachment-section">
                <div class="attachment-section-title">
                    <i class="fas fa-folder-open me-2 text-primary"></i>${section.label}
                    <span class="badge bg-primary ms-2">${files.length}</span>
                </div>
                <div class="attachment-grid">`;

            files.forEach((file, idx) => {
                var fileUrl  = typeof file === 'string' ? file : (file.url || file.path || '');
                const fileName = typeof file === 'string' ? file.split('/').pop() : (file.name || file.original_name || `File ${idx + 1}`);
                const ext      = fileName.split('.').pop().toLowerCase();
                const isImage  = ['jpg','jpeg','png','gif','webp','bmp','svg'].includes(ext);
                const isPdf    = ext === 'pdf';
                if (fileUrl) {
                    const base = window.location.origin; // https://draravinds.com
                    fileUrl = base + '/hms/public/' + fileUrl.replace(/^\/+/, '');
                }

                console.log(fileUrl);

                if (isImage) {
                    html += `<div class="attachment-item" title="${fileName}">
                        <img src="${fileUrl}" alt="${fileName}" onclick="previewImage('${fileUrl}')" onerror="this.src=''; this.parentElement.querySelector('.file-icon') && (this.style.display='none');">
                        <button class="file-download" onclick="downloadFile('${fileUrl}','${fileName}'); event.stopPropagation();" title="Download"><i class="fas fa-download"></i></button>
                        <div class="file-name">${fileName}</div>
                    </div>`;
                } else if (isPdf) {
                    html += `<div class="attachment-item" title="${fileName}" onclick="window.open('${fileUrl}','_blank')">
                        <div class="file-icon text-danger"><i class="fas fa-file-pdf"></i></div>
                        <button class="file-download" onclick="downloadFile('${fileUrl}','${fileName}'); event.stopPropagation();" title="Download"><i class="fas fa-download"></i></button>
                        <div class="file-name">${fileName}</div>
                    </div>`;
                } else {
                    const iconClass = getFileIcon(ext);
                    html += `<div class="attachment-item" title="${fileName}" onclick="downloadFile('${fileUrl}','${fileName}')">
                        <div class="file-icon ${iconClass.color}"><i class="fas ${iconClass.icon}"></i></div>
                        <button class="file-download" onclick="downloadFile('${fileUrl}','${fileName}'); event.stopPropagation();" title="Download"><i class="fas fa-download"></i></button>
                        <div class="file-name">${fileName}</div>
                    </div>`;
                }
            });

            html += `</div></div>`;
        }

        if (!anyFiles) {
            html = `<div class="no-attachments"><i class="fas fa-folder-open fa-3x mb-3 d-block"></i><p class="mb-0">No attachments found for this report.</p></div>`;
        }

        $('#attachmentsContent').html(html);
    }

    function getFileIcon(ext) {
        const map = {
            'xls': { icon: 'fa-file-excel', color: 'text-success' },
            'xlsx': { icon: 'fa-file-excel', color: 'text-success' },
            'doc': { icon: 'fa-file-word', color: 'text-primary' },
            'docx': { icon: 'fa-file-word', color: 'text-primary' },
            'zip': { icon: 'fa-file-archive', color: 'text-warning' },
            'rar': { icon: 'fa-file-archive', color: 'text-warning' },
            'txt': { icon: 'fa-file-alt', color: 'text-secondary' },
        };
        return map[ext] || { icon: 'fa-file', color: 'text-secondary' };
    }

    function previewImage(url) {
        $('#imgPreviewSrc').attr('src', url);
        $('#imgPreviewOverlay').fadeIn(200);
    }

    function closeImagePreview() {
        $('#imgPreviewOverlay').fadeOut(200);
        $('#imgPreviewSrc').attr('src', '');
    }

    function downloadFile(url, name) {
        const a = document.createElement('a');
        a.href = url;
        a.download = name;
        a.target = '_blank';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    // ==================== ACKNOWLEDGMENT MODAL ====================
    function viewAcknowledgment(reportId, posName, posUsername, posFullname, lockerName, lockerUsername, lockerFullname, cmsName, cmsUsername, cmsFullname) {
        let html = '';

        if (posName || posFullname) {
            html += buildPersonnelCard('POS Settled By', '#667eea', 'fa-cash-register', posName, posUsername, posFullname);
        }
        if (lockerName || lockerFullname) {
            html += buildPersonnelCard('Locker Handled By', '#4facfe', 'fa-lock', lockerName, lockerUsername, lockerFullname);
        }
        if (cmsName || cmsFullname) {
            html += buildPersonnelCard('CMS / Radiant Cash Handled By', '#43e97b', 'fa-hand-holding-usd', cmsName, cmsUsername, cmsFullname);
        }

        if (!html) {
            html = '<p class="text-muted text-center py-3">No personnel details available.</p>';
        }

        $('#acknowledgmentContent').html(`<div class="pt-2">${html}</div>`);
        const modal = new bootstrap.Modal($('#acknowledgmentModal')[0]);
        modal.show();
    }

    function buildPersonnelCard(role, color, icon, identifierName, username, fullname) {
        return `
        <div class="personnel-card">
            <div class="d-flex align-items-center mb-2">
                <div style="width:38px;height:38px;border-radius:50%;background:${color};display:flex;align-items:center;justify-content:center;margin-right:0.75rem;">
                    <i class="fas ${icon} text-white" style="font-size:1rem;"></i>
                </div>
                <span class="personnel-role-badge" style="background:${color}22;color:${color};">${role}</span>
            </div>
            ${fullname ? `<div class="personnel-name"><i class="fas fa-user me-2 text-muted" style="font-size:0.85rem;"></i>${fullname}</div>` : ''}
            ${username ? `<div class="personnel-username"><i class="fas fa-at me-1 text-muted"></i>${username}</div>` : ''}
            ${identifierName && identifierName !== username ? `<div class="personnel-username mt-1"><i class="fas fa-id-badge me-1 text-muted"></i><em>${identifierName}</em></div>` : ''}
        </div>`;
    }

    // ==================== APPROVAL FUNCTIONS ====================
    function approveReportAuditor(reportId) {
        Swal.fire({
            title: 'Approve Report?',
            text: 'Are you sure you want to approve this report as Zonal Head?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: approveAuditorRoute.replace(':id', reportId),
                    method: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        toastr.success(response.message);
                        $('#viewReportModal').modal('hide');
                        applyFilters();
                    },
                    error: function(xhr) { toastr.error(xhr.responseJSON?.error || 'Failed to approve report'); }
                });
            }
        });
    }

    function openRejectModalAuditor(reportId) {
        currentReportId = reportId;
        $('#auditor_rejection_remarks').val('');
        new bootstrap.Modal($('#auditorRejectModal')[0]).show();
    }

    function confirmAuditorReject() {
        const remarks = $('#auditor_rejection_remarks').val().trim();
        if (!remarks) { toastr.error('Please enter rejection reason'); return; }
        $.ajax({
            url: rejectAuditorRoute.replace(':id', currentReportId),
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), remarks },
            success: function(response) {
                toastr.success(response.message);
                $('#auditorRejectModal').modal('hide');
                $('#viewReportModal').modal('hide');
                applyFilters();
            },
            error: function(xhr) { toastr.error(xhr.responseJSON?.error || 'Failed to reject report'); }
        });
    }

    function approveReportManagement(reportId) {
        Swal.fire({
            title: 'Approve Report?',
            text: 'Are you sure you want to approve this report as Management?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#dc3545',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: approveManagementRoute.replace(':id', reportId),
                    method: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        toastr.success(response.message);
                        $('#viewReportModal').modal('hide');
                        applyFilters();
                    },
                    error: function(xhr) { toastr.error(xhr.responseJSON?.error || 'Failed to approve report'); }
                });
            }
        });
    }

    function openRejectModalManagement(reportId) {
        currentReportId = reportId;
        $('#management_rejection_remarks').val('');
        new bootstrap.Modal($('#managementRejectModal')[0]).show();
    }

    function confirmManagementReject() {
        const remarks = $('#management_rejection_remarks').val().trim();
        if (!remarks) { toastr.error('Please enter rejection reason'); return; }
        $.ajax({
            url: rejectManagementRoute.replace(':id', currentReportId),
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), remarks },
            success: function(response) {
                toastr.success(response.message);
                $('#managementRejectModal').modal('hide');
                $('#viewReportModal').modal('hide');
                applyFilters();
            },
            error: function(xhr) { toastr.error(xhr.responseJSON?.error || 'Failed to reject report'); }
        });
    }

    // ==================== PAGINATION ====================
    let currentPerPage = {{ request('per_page', 10) }};

    $(document).on('click', '.pagination-links .pagination a', function(e) {
        e.preventDefault();
        const page = new URL($(this).attr('href')).searchParams.get('page');
        applyFilters(page, currentPerPage);
    });

    $(document).on('change', '#perPageSelect', function() {
        currentPerPage = $(this).val();
        applyFilters(1, currentPerPage);
    });
</script>

@include('superadmin.superadminfooter')
</body>
</html>