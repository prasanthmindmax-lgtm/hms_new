<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Statement Reconciliation</title>
    
    @include('superadmin.superadminhead')
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/bank-reconciliation.css') }}">

    
    <style>
        /* Additional inline styles */
        .upload-area {
            border: 3px dashed #667eea;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .upload-area:hover {
            border-color: #764ba2;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }
        
        .upload-area.dragover {
            border-color: #28a745;
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(32, 201, 151, 0.1) 100%);
        }
        
        .upload-icon {
            font-size: 80px;
            color: #667eea;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .file-name-display {
            margin-top: 15px;
            padding: 12px 20px;
            background: white;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        }
        
        .processing-overlay.active {
            display: flex;
        }
        
        .processing-content {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            max-width: 400px;
        }
        
        .spinner-large {
            width: 80px;
            height: 80px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .category-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #ffc107;
            color: #000;
        }
        
        .category-badge.uncategorized {
            background: #ffc107;
            color: #000;
        }
        
        .category-badge.categorized {
            background: #28a745;
            color: white;
        }

        /* ===== INCOME TAG TAB ===== */
        .income-tag-panel {
            padding: 4px 2px;
        }
        .income-tag-filter-summary {
            font-size: 12px;
            color: #94a3b8;
            font-style: italic;
            margin-bottom: 14px;
        }
        .income-tag-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.7px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .income-tag-label-dot {
            width: 10px;
            height: 3px;
            border-radius: 2px;
            display: inline-block;
        }
        .income-tag-modes {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }
        .income-tag-mode-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 12px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            min-width: 72px;
        }
        .income-tag-mode-btn .mode-icon {
            font-size: 22px;
        }
        .income-tag-mode-btn:hover {
            border-color: #6366f1;
            background: #eef2ff;
            color: #4338ca;
        }
        .income-tag-mode-btn.selected {
            border-color: #6366f1;
            background: #6366f1;
            color: #fff;
        }
        .income-tag-date-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 10px;
            background: #f8fafc;
        }
        .income-tag-date-input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 13px;
            color: #334155;
            cursor: pointer;
            width: 100%;
        }
        .income-tag-date-input::placeholder { color: #94a3b8; }
    </style>
</head>
<body style="overflow-x: hidden;">

    <div class="page-loader">
      <div class="bar"></div>
    </div>
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')
    
    <div class="pc-container">
        <div class="pc-content">
            <div class="container-fluid py-4">
                
                {{-- Page Header --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card header-card">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h2 class="text-white mb-1">
                                            <i class="bi bi-bank me-2"></i>Bank Statement Reconciliation
                                        </h2>
                                        <p class="text-white-50 mb-0">Upload, match and reconcile bank statements with bills</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <button class="btn btn-light me-2" id="viewStatementsBtn">
                                            <i class="bi bi-table me-2"></i>View Statements
                                        </button>
                                        <button class="btn btn-light" id="uploadBtn">
                                            <i class="bi bi-upload me-2"></i>Upload New
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Import Section (Initially Visible) - Minimized --}}
                <div id="importSection">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-body p-4">
                                    <form id="uploadFormMain" enctype="multipart/form-data">
                                        @csrf
                                        <div class="upload-area-mini" id="uploadArea">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-cloud-upload-fill text-primary me-3" style="font-size: 40px;"></i>
                                                        <div>
                                                            <h5 class="mb-1">Import Bank Statement</h5>
                                                            <p class="text-muted mb-0 small">Upload Excel file (.xlsx, .xls) - Max 10MB</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <input type="file" name="excel_file" id="excelFileInput" accept=".xlsx,.xls" style="display: none;">
                                                    <button type="button" class="btn btn-primary" id="browseBtn">
                                                        <i class="bi bi-folder2-open me-2"></i>Browse Files
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="file-name-display mt-3" id="fileNameDisplay" style="display: none;">
                                                <div class="alert alert-success mb-0 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="bi bi-file-earmark-excel me-2"></i>
                                                        <span id="fileName"></span>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" id="removeFileBtn">
                                                        <i class="bi bi-x-circle"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mt-3">
                                            <button type="submit" class="btn btn-success" id="uploadSubmitBtn" disabled>
                                                <i class="bi bi-upload me-2"></i>Upload & Process
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statements Section (Initially Hidden) --}}
                <div id="statementsSection" style="display: none;">
                    
                    {{-- Statistics Cards --}}
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card stat-card-1">
                                <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                                <div class="stat-content">
                                    <h3 id="totalStatements">0</h3>
                                    <p>Total Statements</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card stat-card-2">
                                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                                <div class="stat-content">
                                    <h3 id="matchedStatements">0</h3>
                                    <p>Matched</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card stat-card-3">
                                <div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div>
                                <div class="stat-content">
                                    <h3 id="unmatchedStatements">0</h3>
                                    <p>Unmatched</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card stat-card-4">
                                <div class="stat-icon"><i class="bi bi-currency-rupee"></i></div>
                                <div class="stat-content">
                                    <h3 id="totalAmount">₹0</h3>
                                    <p>Total Amount</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Income Reconciliation Stats --}}
                    <div class="row mb-2">
                        <div class="col-12">
                            <h6 class="text-muted mb-2" style="font-size:12px;letter-spacing:1px;text-transform:uppercase;">
                                <i class="bi bi-arrow-left-right me-1"></i> Income Reconciliation
                            </h6>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);">
                                <div class="stat-icon"><i class="bi bi-check2-all"></i></div>
                                <div class="stat-content">
                                    <h3 id="incomeMatchedCount">0</h3>
                                    <p>Income Matched</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                                <div class="stat-icon"><i class="bi bi-dash-circle"></i></div>
                                <div class="stat-content">
                                    <h3 id="incomeUnmatchedCount">0</h3>
                                    <p>Income Unmatched</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bank Statements Table with Filter Button --}}
                    <div class="card shadow-sm">
                        <div class="card-header table-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Bank Statements</h5>
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0 small text-white-50">Per page:</label>
                                <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <button class="btn btn-light btn-sm" id="openFilterBtn">
                                    <i class="bi bi-funnel me-1"></i>Search & Filter
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive bank-recon-table-wrap">
                                <table class="table table-hover" id="statementsTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Reference</th>
                                            <th>Transaction ID</th>
                                            <th>Withdrawal</th>
                                            <th>Deposit</th>
                                            <th>Balance</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Matched Bill</th>
                                            <th>Matched By</th>
                                            <th>Income Tag</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="statementsTableBody">
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                                                <p class="text-muted mt-3">No statements uploaded</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="paginationContainer"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Processing Overlay --}}
    <div class="processing-overlay" id="processingOverlay">
        <div class="processing-content">
            <div class="spinner-large"></div>
            <h4>Processing Excel File...</h4>
            <p class="text-muted mb-0">Please wait while we import your data</p>
            <p class="text-muted"><small id="processingStatus">Analyzing file...</small></p>
        </div>
    </div>

    {{-- Filter Modal --}}
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-funnel me-2"></i>Search & Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Date Range</label>
                            <input type="text" class="form-control" id="filterDateRange" placeholder="Select date range">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-control" id="filterMatchStatus">
                                <option value="">All Status</option>
                                <option value="unmatched">Unmatched</option>
                                <option value="matched">Matched</option>
                                <option value="partially_matched">Partially Matched</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Income Match</label>
                            <select class="form-control" id="filterIncomeMatch">
                                <option value="">All</option>
                                <option value="income_matched">Income Matched</option>
                                <option value="income_unmatched">Income Unmatched</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Range (Min)</label>
                            <input type="number" class="form-control" id="filterAmountMin" placeholder="Min Amount">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Range (Max)</label>
                            <input type="number" class="form-control" id="filterAmountMax" placeholder="Max Amount">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference/transcation ID</label>
                            <input type="text" class="form-control" id="filterReference" placeholder="Reference Number">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" id="filterDescription" placeholder="Max 500 characters">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" >
                    <button type="button" class="btn btn-outline-secondary" id="clearAllFiltersBtn">
                        <i class="bi bi-x-circle me-1"></i>Clear All
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyFiltersBtn">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Match Transaction Modal - Right Side Drawer --}}
    <div class="modal fade right" id="matchTransactionModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-link-45deg me-2"></i>Match Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Transaction Details at Top --}}
                    <div class="transaction-info-card mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="text-muted small">Date</label>
                                <div class="fw-bold" id="txnDate">-</div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Reference</label>
                                <div class="fw-bold" id="txnReference">-</div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Description</label>
                                <div class="fw-bold" id="txnDescription">-</div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Amount</label>
                                <div class="fw-bold" style="font-size: 20px;" id="txnAmount">₹0.00</div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabs --}}
                    <ul class="nav nav-tabs mb-3" id="matchTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="match-tab" data-bs-toggle="tab" data-bs-target="#match-content" type="button">
                                MATCH TRANSACTIONS
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="categorize-tab" data-bs-toggle="tab" data-bs-target="#categorize-content" type="button">
                                <i class="bi bi-tag me-1"></i>INCOME TAG
                            </button>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="matchTabContent">
                        {{-- Match Transactions Tab --}}
                        <div class="tab-pane fade" id="match-content">
                            {{-- Best Matches Section --}}
                            <div class="matches-section-new mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <strong>Best Matches</strong>
                                        <span class="badge bg-primary ms-2" id="bestMatchesCount">0</span>
                                    </h6>
                                    <button class="btn btn-sm btn-outline-secondary" id="toggleBestMatches">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                                <div id="bestMatchesList">
                                    <p class="text-muted text-center py-3">No matches found</p>
                                </div>
                            </div>

                            {{-- Possible Matches Section with Filter --}}
                            <div class="matches-section-new">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <strong>Possible Matches</strong>
                                    </h6>
                                    <button class="btn btn-sm btn-outline-primary" id="togglePossibleFilter">
                                        <i class="bi bi-funnel me-1"></i>UNSELECT ALL <i class="bi bi-funnel-fill ms-1"></i>
                                    </button>
                                </div>

                                {{-- Possible Matches Filter Box --}}
                                <div class="filter-box mb-3" id="possibleFilterBox" style="display: none;">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label small">Amount Range (Min)</label>
                                            <input type="number" class="form-control form-control-sm" id="possibleAmountMin" value="1">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small">Amount Range (Max)</label>
                                            <input type="number" class="form-control form-control-sm" id="possibleAmountMax">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small">Date Range</label>
                                            <input type="text" class="form-control form-control-sm" id="possibleDateFrom" placeholder="From Date">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small">To Date</label>
                                            <input type="text" class="form-control form-control-sm" id="possibleDateTo" placeholder="To Date">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Contact</label>
                                            <input type="text" class="form-control form-control-sm" id="possibleContact" placeholder="Select Contact">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Type</label>
                                            <select class="form-control form-control-sm" id="possibleType">
                                                <option value="">Select Type</option>
                                                <option value="bill">Bill</option>
                                                <option value="payment">Payment</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Reference#</label>
                                            <input type="text" class="form-control form-control-sm" id="possibleReference">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Bill No#</label>
                                            <input type="text" class="form-control form-control-sm" id="possiblebillno">
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="includeDeposits">
                                                <label class="form-check-label small" for="includeDeposits">
                                                    Include Deposits
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-outline-secondary btn-sm" id="clearAllPossibleFilterBtn">
                                                <i class="bi bi-x-circle me-1"></i>Clear All
                                            </button>
                                            <button class="btn btn-primary btn-sm" id="applyPossibleFilter">
                                                <i class="bi bi-search me-1"></i>Search
                                            </button>
                                            <button class="btn btn-secondary btn-sm" id="cancelPossibleFilter">Cancel</button>
                                        </div>
                                    </div>
                                </div>

                                <div id="possibleMatchesList">
                                    <p class="text-muted text-center py-3">No possible matches found</p>
                                </div>
                            </div>
                        </div>

                        {{-- Income Tag Tab --}}
                        <div class="tab-pane fade show active" id="categorize-content">
                            <div class="income-tag-panel">

                                {{-- Active filter summary --}}
                                <p class="income-tag-filter-summary" id="incomeTagFilterSummary">No filters applied yet</p>

                                {{-- Zone & Branch row --}}
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="income-tag-label">
                                            <span class="income-tag-label-dot" style="background:#6366f1;"></span>ZONE
                                        </label>
                                        <select class="form-select form-select-sm" id="incomeTagZone">
                                            <option value="">Select zone...</option>
                                        </select>
                                        {{-- hidden: stores zone name for API submission --}}
                                        <input type="hidden" id="incomeTagZoneName">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="income-tag-label">
                                            <span class="income-tag-label-dot" style="background:#10b981;"></span>BRANCH
                                        </label>
                                        <select class="form-select form-select-sm" id="incomeTagBranch" disabled>
                                            <option value="">Select zone first...</option>
                                        </select>
                                        {{-- hidden: stores branch name for API submission --}}
                                        <input type="hidden" id="incomeTagBranchName">
                                    </div>
                                </div>

                                {{-- Date of Collection --}}
                                <div class="mb-3">
                                    <label class="income-tag-label">
                                        <span class="income-tag-label-dot" style="background:#f59e0b;"></span>DATE OF COLLECTION
                                    </label>
                                    <div class="income-tag-date-wrap">
                                        <i class="bi bi-calendar3 text-warning"></i>
                                        <input type="text" id="incomeTagDate" class="income-tag-date-input" placeholder="Pick a date...">
                                    </div>
                                </div>

                                {{-- Mode of Collection --}}
                                <div class="mb-3">
                                    <label class="income-tag-label">
                                        <span class="income-tag-label-dot" style="background:#22c55e;"></span>MODE OF COLLECTION
                                        <span class="text-muted fw-normal ms-1" style="font-size:10px;text-transform:none;">(multi-select)</span>
                                    </label>
                                    <div class="income-tag-modes">
                                        <button type="button" class="income-tag-mode-btn" data-mode="cash">
                                            <span class="mode-icon">💵</span><span>Cash</span>
                                        </button>
                                        <button type="button" class="income-tag-mode-btn" data-mode="card">
                                            <span class="mode-icon">💳</span><span>Card</span>
                                        </button>
                                        <button type="button" class="income-tag-mode-btn" data-mode="upi">
                                            <span class="mode-icon">📱</span><span>UPI</span>
                                        </button>
                                        <button type="button" class="income-tag-mode-btn" data-mode="neft">
                                            <span class="mode-icon">🏦</span><span>NEFT</span>
                                        </button>
                                        <button type="button" class="income-tag-mode-btn" data-mode="other">
                                            <span class="mode-icon">📄</span><span>Others</span>
                                        </button>
                                    </div>
                                    <small class="text-muted" style="font-size:10px;">Card &amp; UPI share the same bank entry. NEFT and Others are tracked separately.</small>
                                </div>

                                {{-- Submit --}}
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm" id="applyIncomeTagBtn">
                                        <i class="bi bi-tag me-1"></i>Apply Income Tag
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="display:none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    {{-- jQuery: loaded here because the theme footer does NOT include it --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    {{-- NOTE: Bootstrap is intentionally NOT loaded here.
         @include('superadmin.superadminfooter') loads /assets/js/plugins/bootstrap.min.js.
         Loading Bootstrap twice breaks data-bs-toggle="dropdown" in the header. --}}
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const routes = {
            upload: "{{ route('bank-reconciliation.upload') }}",
            statements: "{{ route('bank-reconciliation.statements') }}",
            searchBills: "{{ route('bank-reconciliation.search-bills') }}",
            filterBills: "{{ route('bank-reconciliation.filter-bills') }}",
            match: "{{ route('bank-reconciliation.match') }}",
            unmatch: "{{ route('bank-reconciliation.unmatch', ':id') }}",
            destroy: "{{ route('bank-reconciliation.destroy', ':id') }}",
            incomeTag: "{{ route('bank-reconciliation.income-tag') }}",
            incomeUnmatch: "{{ route('bank-reconciliation.income-unmatch', ':id') }}",
            incomeTagZones: "{{ route('bank-reconciliation.income-tag.zones') }}",
            incomeTagBranches: "{{ route('bank-reconciliation.income-tag.branches') }}",
            incomeTagResolve: "{{ route('bank-reconciliation.income-tag.resolve-description') }}"
        };
    </script>
    <script>
    // ============================================
    // FLATPICKR - Bank Reconciliation (replaced daterangepicker)
    // ============================================
    $(document).ready(function() {
        var filterDateEl = document.getElementById('filterDateRange');
        if (filterDateEl) {
            window.bankReconDateFrom = moment().format('YYYY-MM-DD');
            window.bankReconDateTo = moment().format('YYYY-MM-DD');
            flatpickr(filterDateEl, {
                mode: 'range',
                dateFormat: 'd/m/Y',
                defaultDate: [moment().toDate(), moment().toDate()],
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length === 2) {
                        window.bankReconDateFrom = moment(selectedDates[0]).format('YYYY-MM-DD');
                        window.bankReconDateTo = moment(selectedDates[1]).format('YYYY-MM-DD');
                    } else if (selectedDates.length === 1) {
                        window.bankReconDateFrom = moment(selectedDates[0]).format('YYYY-MM-DD');
                        window.bankReconDateTo = null;
                    } else {
                        window.bankReconDateFrom = null;
                        window.bankReconDateTo = null;
                    }
                },
                onClose: function(selectedDates, dateStr) {
                    if (selectedDates.length === 0) {
                        window.bankReconDateFrom = null;
                        window.bankReconDateTo = null;
                    }
                }
            });
            filterDateEl.value = moment().format('DD/MM/YYYY') + ' to ' + moment().format('DD/MM/YYYY');
        }

        $('#matchTransactionModal').on('shown.bs.modal', function() {
            var fromEl = document.getElementById('possibleDateFrom');
            var toEl = document.getElementById('possibleDateTo');
            if (fromEl && !fromEl._flatpickr) {
                flatpickr(fromEl, {
                    dateFormat: 'd/m/Y',
                    onChange: function(selectedDates, dateStr) {
                        fromEl.value = dateStr;
                    }
                });
            }
            if (toEl && !toEl._flatpickr) {
                flatpickr(toEl, {
                    dateFormat: 'd/m/Y',
                    onChange: function(selectedDates, dateStr) {
                        toEl.value = dateStr;
                    }
                });
            }
        });
    });
    </script>
    <script src="{{ asset('/assets/js/bank-reconciliation/bank-reconciliation.js') }}"></script>

    
    @include('superadmin.superadminfooter')
</body>
</html>