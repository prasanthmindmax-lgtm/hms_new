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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
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

        /* Bank match modal — attachment staging (preview + document type) */
        .bank-match-att-staging-item {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-start;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px;
            background: #fafafa;
        }
        .bank-match-att-staging-preview {
            width: 88px;
            height: 88px;
            border-radius: 8px;
            overflow: hidden;
            background: #e2e8f0;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bank-match-att-staging-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bank-match-att-staging-meta {
            flex: 1;
            min-width: 180px;
        }
        .bank-match-att-viewer-body .bank-recon-att-doc-badge {
            font-size: 11px;
        }
        /* Nature multiselect panel must not be clipped by scrollable modal body */
        #bankMatchDetailsModal.modal .modal-dialog,
        #bankMatchDetailsModal .modal-content,
        #bankMatchDetailsModal .modal-body,
        #bankMatchDetailsModal .tab-content,
        #bankMatchDetailsModal .tab-pane,
        #bankMatchDetailsModal .bank-match-details-card {
            overflow: visible;
        }
        #bankMatchDetailsModal .tax-dropdown-wrapper.br-bank-match-nature {
            position: relative;
            z-index: 2;
        }
        #bankMatchDetailsModal .dropdown-menu.tax-dropdown.br-bank-match-dd {
            z-index: 2005;
        }
        .batch-toolbar .form-control, .batch-toolbar .form-select { min-width: 140px; }
        /* Quick filter: compact financial year + transaction date field */
        .bank-recon-qf-col--fy .bank-recon-qf-btn {
            font-size: 0.78rem;
            line-height: 1.2;
            padding: 0.28rem 0.4rem;
            min-height: 2.05rem;
        }
        .bank-recon-qf-col--fy .bank-recon-qf-field-label {
            font-size: 0.7rem;
            margin-bottom: 0.15rem;
        }
        .bank-recon-qf-txn-dates {
            font-size: 0.8rem;
        }
        /* Transaction date in first row: same visual height as compact FY / qf buttons */
        .bank-recon-qf-col--txn .bank-recon-qf-field-label {
            font-size: 0.7rem;
            margin-bottom: 0.15rem;
        }
        .bank-recon-qf-col--txn .form-control.bank-recon-qf-txn-dates {
            min-height: 2.05rem;
            padding: 0.28rem 0.4rem;
        }
        .bank-recon-qf-col--nature .bank-recon-qf-menu {
            min-width: 100% !important;
            max-width: 100% !important;
        }
        /* Quick filter rows: 5 equal columns (xl+); 4 in a row; single ≈ 20% width in last row */
        @media (min-width: 1200px) {
            .bank-recon-qf-row--5 {
                flex-wrap: nowrap;
            }
            .bank-recon-qf-row--5 > .bank-recon-qf5-c {
                flex: 1 1 0% !important;
                max-width: none !important;
            }
            .bank-recon-qf-row--5 > .bank-recon-qf5-c--1 {
                flex: 0 0 20% !important;
                max-width: 20% !important;
            }
        }
        .bank-recon-qf-row--5 .dropdown,
        .bank-recon-qf-row--5 .bank-recon-qf-btn,
        .bank-recon-qf-row--5 .form-control { width: 100%; }
        .bank-recon-qf-row--5 .bank-recon-qf-menu { min-width: 100% !important; max-width: 100% !important; }
        @media (min-width: 1200px) {
            .bank-recon-qf-row--5 .bank-recon-qf-col--fy .bank-recon-qf-field-label,
            .bank-recon-qf-row--5 .bank-recon-qf-col--txn .bank-recon-qf-field-label { font-size: 0.79rem; }
        }
        /* Row with 6 filters (Bill match … Vendor): one line at xl+ like the 5-column rows */
        @media (min-width: 1200px) {
            .bank-recon-qf-row--6 {
                flex-wrap: nowrap;
            }
            .bank-recon-qf-row--6 > .bank-recon-qf6-c {
                flex: 1 1 0% !important;
                max-width: none !important;
            }
        }
        .bank-recon-qf-row--6 .dropdown,
        .bank-recon-qf-row--6 .bank-recon-qf-btn,
        .bank-recon-qf-row--6 .form-control { width: 100%; }
        .bank-recon-qf-row--6 .bank-recon-qf-menu { min-width: 100% !important; max-width: 100% !important; }
        /* SweetAlert2 above Match Transaction drawer (JS also lowers modal z-index while Swal is open) */
        .swal2-container { z-index: 2147483647 !important; }
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
                @php
                    $bankReconFyNow = \Carbon\Carbon::now();
                    $bankReconFyStart = ($bankReconFyNow->month >= 4)
                        ? \Carbon\Carbon::create($bankReconFyNow->year, 4, 1)->startOfDay()
                        : \Carbon\Carbon::create($bankReconFyNow->year - 1, 4, 1)->startOfDay();
                    $bankReconFyEnd = $bankReconFyStart->copy()->addYear()->subDay()->endOfDay();
                    $bankReconFyYearOptions = [];
                    // Current FY plus the four previous FYs (five choices total).
                    $bankReconFyCurrentStartYear = (int) $bankReconFyStart->year;
                    for ($bankReconFyIdx = 0; $bankReconFyIdx < 5; $bankReconFyIdx++) {
                        $fyY = $bankReconFyCurrentStartYear - $bankReconFyIdx;
                        $optStart = \Carbon\Carbon::create($fyY, 4, 1)->startOfDay();
                        $optEnd = $optStart->copy()->addYear()->subDay()->endOfDay();
                        $bankReconFyYearOptions[] = [
                            'value' => $optStart->format('Y-m-d') . '|' . $optEnd->format('Y-m-d'),
                            'label' => 'FY ' . $fyY . '-' . substr((string) ($fyY + 1), -2) . ' (Apr ' . $fyY . ' – Mar ' . ($fyY + 1) . ')',
                            'default' => $bankReconFyIdx === 0,
                        ];
                    }
                @endphp
                
                {{-- Page Header --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card header-card bank-recon-header-compact">
                            <div class="card-body">
                                <div class="row align-items-center g-2">
                                    <div class="col-md-5 col-lg-6">
                                        <h2 class="text-white mb-0 bank-recon-header-title">
                                            <i class="bi bi-bank me-2"></i>Bank Statement Reconciliation
                                        </h2>
                                        <p class="text-white-50 mb-0 bank-recon-header-sub">Upload, match and reconcile bank statements with bills</p>
                                    </div>
                                    <div class="col-md-7 col-lg-6 text-end d-flex flex-wrap justify-content-md-end gap-1 align-items-center">
                                        @if(!empty($bankAccountsEnabled))
                                        <button type="button" class="btn btn-light btn-sm" id="accountDetailsModalBtn" title="Bank accounts &amp; settings">
                                            <i class="bi bi-wallet2 me-1"></i>Account details
                                        </button>
                                        @endif
                                        <button type="button" class="btn btn-light btn-sm" id="headerUploadBtn" title="Open bank statement Excel upload">
                                            <i class="bi bi-upload me-1"></i>Upload
                                        </button>
                                        <button type="button" class="btn btn-light btn-sm" id="btnBankReconSalaryUpload" title="Upload salary Excel; UTR values are matched in statement descriptions">
                                            <i class="bi bi-file-earmark-excel me-1"></i>Salary UTR
                                        </button>
                                        <!--                                         <button type="button" class="btn btn-outline-light btn-sm" id="btnBankReconSalaryHistory" title="Salary file uploads and match results">
                                            <i class="bi bi-journal-text me-1"></i>Salary log
                                        </button>
                                        <a href="{{ route('bank-reconciliation.salary-master') }}" class="btn btn-outline-light btn-sm" title="All salary Excel rows: filter &amp; export">
                                            <i class="bi bi-table me-1"></i>Salary master
                                        </a> -->
                                        <button type="button" class="btn btn-outline-light btn-sm" id="btnOpenBatchUploadView" title="Open batch list (no page reload)">
                                            <i class="bi bi-collection me-1"></i>Batch uploads
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
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                        <h5 class="mb-0 text-secondary"><i class="bi bi-cloud-upload me-2"></i>Import bank statement</h5>
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="backToStatementBtn" title="Return to statements list">
                                            <i class="bi bi-arrow-left me-1"></i>Back to statement
                                        </button>
                                    </div>
                                    <form id="uploadFormMain" enctype="multipart/form-data">
                                        @csrf
                                        @if(!empty($bankAccountsEnabled))
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold"><i class="bi bi-building me-1"></i>Company <span class="text-danger">*</span></label>
                                            <select class="form-select" name="company_id" id="mainUploadCompany" required>
                                                <option value="">Select company…</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold"><i class="bi bi-bank me-1"></i>Bank account <span class="text-danger">*</span></label>
                                            <select class="form-select bank-account-select" name="bank_account_id" id="mainUploadBankAccount" required disabled>
                                                <option value="">Select company first…</option>
                                            </select>
                                            <small class="text-muted">Each upload is stored against this account. Use <strong>Account details</strong> to add a new account.</small>
                                        </div>
                                        @endif
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
                    
                    {{-- Statistics: single horizontal strip (scroll on small screens) --}}
                    <div class="bank-recon-stats-strip mb-3">
                        <div class="bank-recon-stats-scroll">
                            <div class="bank-recon-stat-tile">
                                <div class="stat-card stat-card-1 bank-recon-stat-clickable h-100" id="statCardFilterAll" role="button" tabindex="0" title="Show all statements (current date &amp; filters)">
                                    <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                                    <div class="stat-content">
                                        <h3 id="totalStatements">0</h3>
                                        <p>Total Statements</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bank-recon-stat-tile">
                                <div class="stat-card stat-card-2 bank-recon-stat-clickable h-100" id="statCardFilterMatched" role="button" tabindex="0" title="Show bill-matched statements">
                                    <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                                    <div class="stat-content">
                                        <h3 id="matchedStatements">0</h3>
                                        <p>Matched</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bank-recon-stat-tile">
                                <div class="stat-card stat-card-3 h-100">
                                    <div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div>
                                    <div class="stat-content">
                                        <h3 id="unmatchedStatements">0</h3>
                                        <p>Unmatched</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bank-recon-stat-tile bank-recon-stat-tile--amount">
                                <div class="stat-card stat-card-4 h-100">
                                    <div class="stat-icon"><i class="bi bi-currency-rupee"></i></div>
                                    <div class="stat-content">
                                        <h3 id="totalAmount">₹0</h3>
                                        <p>Total Amount</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bank-recon-stat-tile">
                                <div class="stat-card bank-recon-stat-clickable h-100" id="statCardFilterIncomeMatched" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);" role="button" tabindex="0" title="Show income-matched statements">
                                    <div class="stat-icon"><i class="bi bi-check2-all"></i></div>
                                    <div class="stat-content">
                                        <h3 id="incomeMatchedCount">0</h3>
                                        <p>Income Matched</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bank-recon-stat-tile">
                                <div class="stat-card h-100" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                                    <div class="stat-icon"><i class="bi bi-dash-circle"></i></div>
                                    <div class="stat-content">
                                        <h3 id="incomeUnmatchedCount">0</h3>
                                        <p>Income Unmatched</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick filter panel — dropdown multi-select style --}}
                    <div class="bank-recon-qf-panel" id="bankReconQfPanel">

                        {{-- Header row --}}
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <span class="bank-recon-qf-badge"><i class="bi bi-funnel-fill"></i> Quick Filters</span>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                @if(!empty($bankReconSuperAdmin))
                                <button type="button" class="btn btn-sm btn-outline-dark" id="btnBankReconUserHistory" title="Super Admin: action audit log">
                                    <i class="bi bi-clock-history me-1"></i>History
                                </button>
                                @endif
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnBankReconSalaryHistory" title="Salary file uploads and match results">
                                    <i class="bi bi-journal-text me-1"></i>Salary log
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="qfResetBtn">
                                    <i class="bi bi-x-circle me-1"></i>Reset
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="qfOpenFullFilterBtn">
                                    <i class="bi bi-sliders me-1"></i>More Filters
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="qfApplyBtn">
                                    <i class="bi bi-search me-1"></i>Search
                                </button>
                            </div>
                        </div>

                        {{-- Quick filters: 5 per row (16 with bank / 14 without). Row1: FY, Txn, Mocdoc, +2 --}}
                        <div class="row g-2 mb-2 align-items-end bank-recon-qf-row--5">

                            {{-- Financial Year --}}
                            <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c bank-recon-qf-col--fy">
                                <div class="bank-recon-qf-field-label"><i class="bi bi-calendar3"></i> Financial Year</div>
                                <div class="dropdown">
                                    <button class="bank-recon-qf-btn" type="button" id="qfBtn-financialYear"
                                            data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                                        <span class="qf-btn-text">All years</span>
                                        <i class="bi bi-chevron-down qf-btn-arrow"></i>
                                    </button>
                                    <div class="dropdown-menu bank-recon-qf-menu" id="qfMenu-financialYear">
                                        <div class="qf-menu-item qf-menu-item-all">
                                            <input type="checkbox" class="qf-all-chk" id="brqf_qfFinancialYear_all">
                                            <label class="qf-menu-item-text" for="brqf_qfFinancialYear_all">All years</label>
                                        </div>
                                        <div class="qf-menu-list">
                                            <div class="qf-options-inner">
                                                @foreach ($bankReconFyYearOptions as $opt)
                                                    <div class="qf-menu-item">
                                                        <input type="checkbox" id="brqf_qfFinancialYear_opt_{{ $loop->index }}" value="{{ $opt['value'] }}"{{ !empty($opt['default']) ? ' checked' : '' }}>
                                                        <label class="qf-menu-item-text" for="brqf_qfFinancialYear_opt_{{ $loop->index }}">{{ $opt['label'] }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <select id="qfFinancialYear" multiple class="d-none">
                                    @foreach ($bankReconFyYearOptions as $opt)
                                        <option value="{{ $opt['value'] }}"{{ !empty($opt['default']) ? ' selected' : '' }}>{{ $opt['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Bank transaction date range (same as modal) — right after Financial Year --}}
                            <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c bank-recon-qf-col--txn">
                                <div class="bank-recon-qf-field-label"><i class="bi bi-calendar-range"></i> Transaction date</div>
                                <input type="text"
                                       id="qfTransactionDateRange"
                                       class="form-control form-control-sm bank-recon-qf-txn-dates"
                                       readonly
                                       autocomplete="off"
                                       placeholder="dd/mm/yyyy to dd/mm/yyyy"
                                       aria-label="Filter by bank transaction date range">
                            </div>

                            {{-- Income / Mocdoc collection dates (multi) — independent of Financial Year txn window --}}
                            <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
                                <div class="bank-recon-qf-field-label"><i class="bi bi-calendar2-check"></i> Mocdoc collection dates</div>
                                <input type="text"
                                       id="qfIncomeCollectionDates"
                                       class="form-control form-control-sm bank-recon-qf-mocdoc-dates"
                                       placeholder="Pick one or more dates…"
                                       autocomplete="off"
                                       readonly
                                       aria-label="Filter by Mocdoc or income tag collection dates">
                            </div>

                            @if(!empty($bankAccountsEnabled))
                            <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
                                <div class="bank-recon-qf-field-label"><i class="bi bi-building"></i> Company</div>
                                <div class="dropdown">
                                    <button class="bank-recon-qf-btn" type="button" id="qfBtn-bankCompany"
                                            data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                                        <span class="qf-btn-text">All companies</span>
                                        <i class="bi bi-chevron-down qf-btn-arrow"></i>
                                    </button>
                                    <div class="dropdown-menu bank-recon-qf-menu" id="qfMenu-bankCompany">
                                        <div class="qf-menu-search-wrap">
                                            <input type="text" class="qf-search-input" placeholder="Search companies…">
                                        </div>
                                        <div class="qf-menu-item qf-menu-item-all">
                                            <input type="checkbox" class="qf-all-chk" id="brqf_qfBankCompany_all" checked>
                                            <label class="qf-menu-item-text" for="brqf_qfBankCompany_all">All companies</label>
                                        </div>
                                        <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
                                    </div>
                                </div>
                                <select id="qfBankCompany" multiple class="d-none"></select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
                                <div class="bank-recon-qf-field-label"><i class="bi bi-credit-card-2-front"></i> Account Number</div>
                                <div class="dropdown">
                                    <button class="bank-recon-qf-btn" type="button" id="qfBtn-bankAccount"
                                            data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                                        <span class="qf-btn-text">All accounts</span>
                                        <i class="bi bi-chevron-down qf-btn-arrow"></i>
                                    </button>
                                    <div class="dropdown-menu bank-recon-qf-menu" id="qfMenu-bankAccount">
                                        <div class="qf-menu-search-wrap">
                                            <input type="text" class="qf-search-input" placeholder="Search accounts…">
                                        </div>
                                        <div class="qf-menu-item qf-menu-item-all">
                                            <input type="checkbox" class="qf-all-chk" id="brqf_qfBankAccount_all" checked>
                                            <label class="qf-menu-item-text" for="brqf_qfBankAccount_all">All accounts</label>
                                        </div>
                                        <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
                                    </div>
                                </div>
                                <select id="qfBankAccount" multiple class="d-none"></select>
                            </div>
                            @else
                            <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
                                <div class="bank-recon-qf-field-label" title="Filters vendor bill zone, income-tag branch / description, and salary sheet branch (UTR upload).">
                                    <i class="bi bi-map"></i> Zone
                                </div>
                                <div class="dropdown">
                                    <button class="bank-recon-qf-btn" type="button" id="qfBtn-zone"
                                            data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                                        <span class="qf-btn-text">All zones</span>
                                        <i class="bi bi-chevron-down qf-btn-arrow"></i>
                                    </button>
                                    <div class="dropdown-menu bank-recon-qf-menu" id="qfMenu-zone">
                                        <div class="qf-menu-search-wrap">
                                            <input type="text" class="qf-search-input" placeholder="Search zones…">
                                        </div>
                                        <div class="qf-menu-item qf-menu-item-all">
                                            <input type="checkbox" class="qf-all-chk" id="brqf_qfZone_all" checked>
                                            <label class="qf-menu-item-text" for="brqf_qfZone_all">All zones</label>
                                        </div>
                                        <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
                                    </div>
                                </div>
                                <select id="qfZone" multiple class="d-none"></select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
                                <div class="bank-recon-qf-field-label" title="Filters vendor bill branch, income-tag branch / description, and salary Excel branch column.">
                                    <i class="bi bi-building"></i> Branch
                                </div>
                                <div class="dropdown">
                                    <button class="bank-recon-qf-btn" type="button" id="qfBtn-branch"
                                            data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                                        <span class="qf-btn-text">All branches</span>
                                        <i class="bi bi-chevron-down qf-btn-arrow"></i>
                                    </button>
                                    <div class="dropdown-menu bank-recon-qf-menu" id="qfMenu-branch">
                                        <div class="qf-menu-search-wrap">
                                            <input type="text" class="qf-search-input" placeholder="Search branches…">
                                        </div>
                                        <div class="qf-menu-item qf-menu-item-all">
                                            <input type="checkbox" class="qf-all-chk" id="brqf_qfBranch_all" checked>
                                            <label class="qf-menu-item-text" for="brqf_qfBranch_all">All branches</label>
                                        </div>
                                        <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
                                    </div>
                                </div>
                                <select id="qfBranch" multiple class="d-none"></select>
                            </div>
                            @endif

                        </div>

                        @include('bank-reconciliation.partials.bank_recon_qf_rows_2_4')

                    </div>{{-- /bank-recon-qf-panel --}}

                    <!-- {{-- Radiant cash pickup reconciliation stats --}}
                    <div class="row mb-2">
                        <div class="col-12">
                            <h6 class="text-muted mb-2" style="font-size:12px;letter-spacing:1px;text-transform:uppercase;">
                                <i class="bi bi-brightness-high me-1"></i> Radiant cash pickup
                            </h6>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card" style="background:linear-gradient(135deg,#ea580c,#c2410c);">
                                <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
                                <div class="stat-content">
                                    <h3 id="radiantMatchedCount">0</h3>
                                    <p>Radiant linked</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card" style="background:linear-gradient(135deg,#78716c,#57534e);">
                                <div class="stat-icon"><i class="bi bi-pencil-square"></i></div>
                                <div class="stat-content">
                                    <h3 id="radiantKeywordOnlyCount">0</h3>
                                    <p>Keyword only</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card" style="background:linear-gradient(135deg,#94a3b8,#64748b);">
                                <div class="stat-icon"><i class="bi bi-circle"></i></div>
                                <div class="stat-content">
                                    <h3 id="radiantUnmatchedCount">0</h3>
                                    <p>Radiant not linked</p>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    {{-- Bank Statements Table with Filter Button --}}
                    <div class="card shadow-sm bank-recon-statements-card">
                        <div class="card-header table-header bank-recon-statements-toolbar d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding:10px !important;">
                            <div class="d-flex align-items-center flex-wrap gap-2 flex-grow-1 min-w-0 me-2">
                                <h5 class="mb-0 text-nowrap"><i class="bi bi-table me-2"></i>Bank Statements</h5>
                                @if(!empty($bankAccountsEnabled))
                                <!-- <div id="bankReconToolbarAccountChips" class="bank-recon-toolbar-account-chips d-flex align-items-center flex-wrap gap-1" aria-label="Filter by bank account"></div> -->
                                @endif
                            </div>
                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-end flex-shrink-0">
                                <label class="mb-0 small text-white-50">Per page:</label>
                                <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <!-- <div class="dropdown bank-recon-export-dd">
                                    <button type="button" class="btn btn-outline-light btn-sm dropdown-toggle" id="exportStatementsDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" aria-haspopup="true" title="Export using current filters">
                                        <i class="bi bi-download me-1"></i>Export
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="exportStatementsDropdown">
                                        <li>
                                            <button type="button" class="dropdown-item" id="btnExportStatementsCsv">
                                                <i class="bi bi-file-earmark-text me-2 text-secondary"></i>Download CSV
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item" id="btnExportStatementsXlsx">
                                                <i class="bi bi-file-earmark-excel me-2 text-success"></i>Download Excel (.xlsx)
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <button class="btn btn-light btn-sm" id="openFilterBtn">
                                    <i class="bi bi-funnel me-1"></i>Search &amp; Filter
                                </button> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive bank-recon-table-wrap">
                                <table class="table table-hover" id="statementsTable">
                                    <thead>
                                        <tr>
                                            <th class="bank-recon-th-sort text-nowrap align-middle" scope="col">
                                                <div class="bank-recon-th-sort-inner">
                                                    <span class="bank-recon-th-sort-label">Date</span>
                                                    <span class="bank-recon-sort-btns" role="group" aria-label="Sort by transaction date">
                                                        <button type="button" class="bank-recon-sort-btn bank-recon-sort-asc" data-sort-dir="asc" title="Oldest first">
                                                            <i class="bi bi-sort-up" aria-hidden="true"></i>
                                                        </button>
                                                        <button type="button" class="bank-recon-sort-btn bank-recon-sort-desc" data-sort-dir="desc" title="Newest first">
                                                            <i class="bi bi-sort-down" aria-hidden="true"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </th>
                                            <th>Account</th>
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
                                            <th>Matched date</th>
                                            <th>Nature / files</th>
                                            <th>Income Tag</th>
                                            <th class="text-nowrap">MocDoc date</th>
                                            <th>Radiant</th>
                                            <th>Salary</th>
                                            @if(!empty($bankReconSuperAdmin))
                                            <th>Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="statementsTableBody">
                                        <tr class="br-skel-row">
                                            <td colspan="{{ !empty($bankReconSuperAdmin) ? 19 : 18 }}" class="text-center py-5 border-0">
                                                <div class="br-table-skeleton">
                                                    <div class="br-skel-shimmer-row" style="--w:55%"></div>
                                                    <div class="br-skel-shimmer-row" style="--w:80%"></div>
                                                    <div class="br-skel-shimmer-row" style="--w:65%"></div>
                                                    <div class="br-skel-shimmer-row" style="--w:72%"></div>
                                                    <div class="br-skel-shimmer-row" style="--w:48%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="paginationContainer"></div>
                        </div>
                    </div>
                </div>

                {{-- Batch uploads (same page, AJAX — no full reload) --}}
                <div id="batchUploadSection" class="bank-recon-batch-embedded" style="display: none;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 mt-2">
                        <div>
                            <h4 class="mb-0 text-body"><i class="bi bi-collection me-2"></i>Statement upload batches</h4>
                            <p class="text-muted small mb-0">Data loads via AJAX only.</p>
                        </div>
                        <button type="button" class="btn btn-primary" id="btnCloseBatchUploadView">
                            <i class="bi bi-arrow-left me-1"></i>Back to reconciliation
                        </button>
                    </div>
                    @if(empty($bankAccountsEnabled))
                    <div class="alert alert-warning">
                        Run migrations (<code>php artisan migrate</code>) to enable <code>bank_reconciliation_accounts</code> and batch history.
                    </div>
                    @else
                    @include('bank-reconciliation.partials.batch_upload_panel_inner')
                    @endif
                </div>

            </div>
        </div>
    </div>

    @include('bank-reconciliation.partials.batch_preview_modal')

    {{-- Processing Overlay --}}
    <div class="processing-overlay" id="processingOverlay">
        <div class="processing-content">
            <div class="spinner-large"></div>
            <h4>Processing Excel File...</h4>
            <p class="text-muted mb-0">Please wait while we import your data</p>
            <p class="text-muted"><small id="processingStatus">Analyzing file...</small></p>
        </div>
    </div>

    @if(!empty($bankAccountsEnabled))
    {{-- Account details + upload (modal): list, add/edit, upload --}}
    <div class="modal fade" id="accountDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content br-acc-modal-content">
                {{-- Gradient header --}}
                <div class="modal-header br-acc-modal-header border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="br-acc-modal-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white mb-0 fw-bold">Bank Accounts &amp; Uploads</h5>
                            <p class="text-white-50 mb-0 small">Manage registered accounts and import statements</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    {{-- Pill tab nav --}}
                    <div class="br-acc-tab-nav px-4 pt-3 pb-0">
                        <ul class="nav nav-pills br-acc-nav-pills" id="accountModalTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tabBtnAllAccounts" data-bs-toggle="tab" data-bs-target="#tabAllAccounts" type="button" role="tab">
                                    <i class="bi bi-list-ul me-1"></i>All accounts
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tabBtnNewAccount" data-bs-toggle="tab" data-bs-target="#tabNewAccount" type="button" role="tab">
                                    <i class="bi bi-plus-circle me-1"></i><span id="newAccountTabLabel">New account</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tabBtnUploadStmt" data-bs-toggle="tab" data-bs-target="#tabUploadStmt" type="button" role="tab">
                                    <i class="bi bi-file-earmark-arrow-up me-1"></i>Upload Excel
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tabBtnAttachmentTypes" data-bs-toggle="tab" data-bs-target="#tabAttachmentTypes" type="button" role="tab">
                                    <i class="bi bi-tags me-1"></i>Attachment types
                                </button>
                            </li>
                        </ul>
                        <div class="br-acc-tab-divider"></div>
                    </div>

                    <div class="tab-content px-4 pb-4 pt-3">
                        {{-- TAB: All accounts --}}
                        <div class="tab-pane fade show active" id="tabAllAccounts" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div>
                                    <span class="fw-semibold text-dark">Registered bank accounts</span>
                                    <p class="text-muted small mb-0">Used for statement uploads and filters.</p>
                                </div>
                                <button type="button" class="btn btn-sm br-acc-refresh-btn" id="btnRefreshModalAccountList">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                                </button>
                            </div>
                            <div class="br-acc-table-wrap">
                                <table class="table table-hover align-middle mb-0 br-acc-table" id="modalAccountsTable">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-building me-1 text-muted"></i>Company</th>
                                            <th><i class="bi bi-credit-card me-1 text-muted"></i>Account #</th>
                                            <th><i class="bi bi-bank me-1 text-muted"></i>Bank</th>
                                            <th>Branch</th>
                                            <th>IFSC</th>
                                            <th>Holder</th>
                                            <th class="text-end" style="width: 80px;">Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bankAccountsModalTableBody">
                                        <tr><td colspan="7" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>
                                            Open this window to load accounts…
                                        </td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB: New / Edit account --}}
                        <div class="tab-pane fade" id="tabNewAccount" role="tabpanel">
                            <div class="br-acc-form-card">
                                <p class="text-muted small mb-3" id="newAccountFormHint">
                                    <i class="bi bi-info-circle me-1"></i>Add a new bank account for statement uploads.
                                </p>
                                <form id="formNewBankAccount">
                                    @csrf
                                    <input type="hidden" id="editBankAccountId" value="">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="br-form-label"><i class="bi bi-building me-1"></i>Company <span class="text-danger">*</span></label>
                                            <select class="form-select br-form-select" name="company_id" id="newAccCompanyId" required>
                                                <option value="">Select company…</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="br-form-label"><i class="bi bi-credit-card me-1"></i>Account number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control br-form-input" name="account_number" id="newAccNumber" required maxlength="64" placeholder="e.g. 50100…">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="br-form-label"><i class="bi bi-bank me-1"></i>Bank name</label>
                                            <input type="text" class="form-control br-form-input" name="bank_name" id="newAccBank" maxlength="191" placeholder="e.g. HDFC Bank">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="br-form-label"><i class="bi bi-geo-alt me-1"></i>Branch</label>
                                            <input type="text" class="form-control br-form-input" name="branch_name" id="newAccBranch" maxlength="191" placeholder="Branch name or city">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="br-form-label"><i class="bi bi-hash me-1"></i>IFSC</label>
                                            <input type="text" class="form-control br-form-input" name="ifsc_code" id="newAccIfsc" maxlength="32" placeholder="e.g. HDFC0001234" style="letter-spacing:.05em;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="br-form-label"><i class="bi bi-person me-1"></i>Account holder</label>
                                            <input type="text" class="form-control br-form-input" name="account_holder_name" id="newAccHolder" maxlength="191" placeholder="Full name">
                                        </div>
                                        <div class="col-12">
                                            <label class="br-form-label"><i class="bi bi-sticky me-1"></i>Notes</label>
                                            <textarea class="form-control br-form-input" name="notes" id="newAccNotes" rows="2" maxlength="2000" placeholder="Optional remarks…"></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex flex-wrap gap-2">
                                        <button type="submit" class="btn br-btn-primary" id="btnSaveNewAccount">
                                            <i class="bi bi-check2-circle me-1"></i><span id="btnSaveNewAccountLabel">Save account</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="btnCancelEditAccount" style="display: none;">
                                            <i class="bi bi-x me-1"></i>Cancel edit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- TAB: Upload Excel --}}
                        <div class="tab-pane fade" id="tabUploadStmt" role="tabpanel">
                            <div class="br-upload-card">
                                <div class="br-upload-card-icon">
                                    <i class="bi bi-file-earmark-spreadsheet"></i>
                                </div>
                                <h6 class="fw-bold mb-1">Import bank statement</h6>
                                <p class="text-muted small mb-3">Select company, account and upload your Excel file (.xlsx / .xls)</p>
                                <form id="uploadFormModal" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="br-form-label"><i class="bi bi-building me-1"></i>Company <span class="text-danger">*</span></label>
                                            <select class="form-select br-form-select" name="company_id" id="modalUploadCompany" required>
                                                <option value="">Select company…</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="br-form-label"><i class="bi bi-bank me-1"></i>Bank account <span class="text-danger">*</span></label>
                                            <select class="form-select br-form-select bank-account-select" name="bank_account_id" id="modalUploadBankAccount" required disabled>
                                                <option value="">Select company first…</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="br-form-label"><i class="bi bi-file-earmark-excel me-1"></i>Excel file</label>
                                            <input type="file" class="form-control br-form-input" name="excel_file" id="modalExcelFile" accept=".xlsx,.xls" required>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn br-btn-success" id="modalUploadSubmit">
                                            <i class="bi bi-upload me-1"></i>Upload &amp; process
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- TAB: Match attachment document types (drives "Document type" in Match details) --}}
                        <div class="tab-pane fade" id="tabAttachmentTypes" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div>
                                    <span class="fw-semibold text-dark">Document types for match attachments</span>
                                    <p class="text-muted small mb-0">These labels appear in <strong>Match details → Attachments</strong> for each file.</p>
                                </div>
                                <button type="button" class="btn btn-sm br-acc-refresh-btn" id="btnRefreshMatchAttachmentTypes">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                                </button>
                            </div>
                            <div class="br-acc-form-card mb-3">
                                <p class="text-muted small mb-2" id="matchAttTypeFormHint">
                                    <i class="bi bi-plus-circle me-1"></i>Add a label; optionally attach a sample file (template or example).
                                </p>
                                <form id="formMatchAttachmentType" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="matchAttTypeEditId" value="">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="br-form-label small mb-1">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" id="matchAttTypeName" maxlength="191" placeholder="e.g. Purchase order" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="br-form-label small mb-1">Use for <span class="text-danger">*</span></label>
                                            <select class="form-select form-select-sm" id="matchAttTypeContext" title="Bill match = vendor bill reconciliation; Income tag = deposit income tagging">
                                                <option value="both">Both</option>
                                                <option value="bill">Bill match only</option>
                                                <option value="income">Income tag only</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="br-form-label small mb-1">Sort</label>
                                            <input type="number" class="form-control form-control-sm" id="matchAttTypeSort" min="0" max="65535" value="0" placeholder="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="br-form-label small mb-1">Sample file</label>
                                            <input type="file" class="form-control form-control-sm" id="matchAttTypeSample" accept=".pdf,.png,.jpg,.jpeg,.webp,.doc,.docx,.xls,.xlsx">
                                        </div>
                                        <div class="col-md-2 d-flex flex-column gap-1">
                                            <button type="submit" class="btn btn-sm br-btn-primary" id="btnSaveMatchAttachmentType">
                                                <i class="bi bi-check2 me-1"></i><span id="btnSaveMatchAttachmentTypeLabel">Add</span>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCancelMatchAttachmentTypeEdit" style="display: none;">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="br-acc-table-wrap">
                                <table class="table table-hover align-middle mb-0 br-acc-table table-sm" id="matchAttachmentTypesTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th style="width: 130px;">Use for</th>
                                            <th class="text-end" style="width: 90px;">Sort</th>
                                            <th style="width: 100px;">Active</th>
                                            <th>Sample</th>
                                            <th class="text-end" style="width: 120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="matchAttachmentTypesTableBody">
                                        <tr><td colspan="6" class="text-center text-muted py-4">Open this tab to load types…</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter Modal --}}
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content br-filter-modal-content">
                {{-- Filter modal header --}}
                <div class="br-filter-modal-header modal-header border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="br-filter-modal-icon"><i class="bi bi-funnel-fill"></i></div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0">Search &amp; Filter</h5>
                            <p class="text-white-50 small mb-0">Narrow down bank statements</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-3 pb-2">
                    @if(!empty($bankAccountsEnabled))
                    {{-- Company / Account group --}}
                    <div class="br-filter-group mb-3">
                        <div class="br-filter-group-label">
                            <i class="bi bi-building me-1"></i>Company &amp; Account
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="br-filter-label"><i class="bi bi-building me-1 text-primary"></i>Company</label>
                                <select class="form-select br-filter-select" id="filterBankCompany">
                                    <option value="">All companies</option>
                                </select>
                                <div class="br-filter-hint">Choose a company to limit the account list</div>
                            </div>
                            <div class="col-md-6">
                                <label class="br-filter-label"><i class="bi bi-credit-card me-1 text-primary"></i>Bank account</label>
                                <select class="form-select br-filter-select bank-account-select" id="filterBankAccount">
                                    <option value="">All accounts</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Date group --}}
                    <div class="br-filter-group mb-3">
                        <div class="br-filter-group-label"><i class="bi bi-calendar3 me-1"></i>Date &amp; Period</div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="br-filter-label"><i class="bi bi-calendar-range me-1 text-primary"></i>Financial year</label>
                                <select class="form-select br-filter-select" id="filterFinancialYear">
                                    <option value="">Custom (use transaction dates below)</option>
                                    @foreach ($bankReconFyYearOptions as $opt)
                                        <option value="{{ $opt['value'] }}"{{ !empty($opt['default']) ? ' selected' : '' }}>{{ $opt['label'] }}</option>
                                    @endforeach
                                </select>
                                <div class="br-filter-hint">Optional preset. Transaction date overrides this.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="br-filter-label"><i class="bi bi-calendar-event me-1 text-primary"></i>Transaction date</label>
                                <input type="text" class="form-control br-filter-input" id="filterDateRange" placeholder="Select date range" autocomplete="off">
                                <div class="br-filter-hint">Leave empty to use Financial year filter.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="br-filter-label"><i class="bi bi-calendar-check me-1 text-primary"></i>Matched date <span class="text-muted fw-normal small">(optional)</span></label>
                                <input type="text" class="form-control br-filter-input" id="filterMatchedDateRange" placeholder="Bill match date range">
                            </div>
                            <div class="col-md-6">
                                <label class="br-filter-label"><i class="bi bi-person-check me-1 text-primary"></i>Matched by</label>
                                <select class="form-select br-filter-select" id="filterMatchedByUser">
                                    <option value="">Anyone</option>
                                </select>
                                <div class="br-filter-hint">User who matched the expense/income</div>
                            </div>
                        </div>
                    </div>

                    {{-- Match status group --}}
                    <div class="br-filter-group mb-3">
                        <div class="br-filter-group-label"><i class="bi bi-check2-all me-1"></i>Match Status</div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="br-filter-label"><i class="bi bi-receipt me-1 text-primary"></i>Expense Match</label>
                                <select class="form-select br-filter-select" id="filterMatchStatus">
                                    <option value="">All Status</option>
                                    <option value="unmatched">Unmatched</option>
                                    <option value="matched">Matched</option>
                                    <option value="partially_matched">Partially Matched</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="br-filter-label"><i class="bi bi-currency-rupee me-1 text-primary"></i>Income Match</label>
                                <select class="form-select br-filter-select" id="filterIncomeMatch">
                                    <option value="">All</option>
                                    <option value="income_matched">Income Matched</option>
                                    <option value="income_unmatched">Income Unmatched</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="br-filter-label"><i class="bi bi-link-45deg me-1 text-primary"></i>Radiant match</label>
                                <select class="form-select br-filter-select" id="filterRadiantMatch">
                                    <option value="">All</option>
                                    <option value="radiant_matched">Radiant linked (pickup)</option>
                                    <option value="radiant_keyword_only">Keyword only</option>
                                    <option value="radiant_unmatched">Not linked</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Amount & search group --}}
                    <div class="br-filter-group">
                        <div class="br-filter-group-label"><i class="bi bi-search me-1"></i>Amount &amp; Search</div>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="br-filter-label"><i class="bi bi-arrow-down-circle me-1 text-primary"></i>Min Amount</label>
                                <input type="number" class="form-control br-filter-input" id="filterAmountMin" placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="br-filter-label"><i class="bi bi-arrow-up-circle me-1 text-primary"></i>Max Amount</label>
                                <input type="number" class="form-control br-filter-input" id="filterAmountMax" placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="br-filter-label"><i class="bi bi-upc me-1 text-primary"></i>Reference / Transaction ID</label>
                                <input type="text" class="form-control br-filter-input" id="filterReference" placeholder="Reference number">
                            </div>
                            <div class="col-md-3">
                                <label class="br-filter-label"><i class="bi bi-card-text me-1 text-primary"></i>Description</label>
                                <input type="text" class="form-control br-filter-input" id="filterDescription" placeholder="Keywords…">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer br-filter-footer border-0 pt-0">
                    <button type="button" class="btn br-filter-clear-btn" id="clearAllFiltersBtn">
                        <i class="bi bi-x-circle me-1"></i>Clear All
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn br-filter-apply-btn" id="applyFiltersBtn">
                        <i class="bi bi-search me-1"></i>Apply filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($bankReconSuperAdmin))
    <div class="modal fade" id="bankReconUserHistoryModal" tabindex="-1" aria-labelledby="bankReconUserHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable br-history-dialog">
            <div class="modal-content">
                <div class="modal-header br-history-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="br-history-icon"><i class="bi bi-clock-history"></i></span>
                        <div>
                            <h5 class="modal-title mb-0" id="bankReconUserHistoryModalLabel">Action History</h5>
                            <small class="text-white-50">Bank Reconciliation — Super Admin Audit Log</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Search / Filter Bar --}}
                <div class="br-history-filters p-3 border-bottom bg-light">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label form-label-sm fw-semibold mb-1"><i class="bi bi-search me-1"></i>Search</label>
                            <input type="text" id="brHistorySearch" class="form-control form-control-sm" placeholder="User, action, details, IP, Stmt #…">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label form-label-sm fw-semibold mb-1"><i class="bi bi-funnel me-1"></i>Action Type</label>
                            <select id="brHistoryActionFilter" class="form-select form-select-sm">
                                <option value="">— All Actions —</option>
                                <option value="import_statement">Import Statement</option>
                                <option value="match_bill">Match Bill</option>
                                <option value="unmatch_bill">Unmatch Bill</option>
                                <option value="income_tag">Income Tag</option>
                                <option value="income_unmatch">Income Unmatch</option>
                                <option value="radiant_match">Radiant Match</option>
                                <option value="radiant_unmatch">Radiant Unmatch</option>
                                <option value="delete_statement">Delete Statement</option>
                                <option value="delete_batch">Delete Batch</option>
                                <option value="salary_upload">Salary UTR upload</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label form-label-sm fw-semibold mb-1"><i class="bi bi-calendar-event me-1"></i>From Date</label>
                            <input type="date" id="brHistoryDateFrom" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label form-label-sm fw-semibold mb-1"><i class="bi bi-calendar-event me-1"></i>To Date</label>
                            <input type="date" id="brHistoryDateTo" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-1 d-flex gap-1">
                            <button class="btn btn-sm btn-primary w-100" id="brHistorySearchBtn" title="Apply Filters">
                                <i class="bi bi-search"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary w-100" id="brHistoryClearBtn" title="Clear Filters">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <small class="text-muted" id="brHistoryResultCount"></small>
                    </div>
                </div>

                {{-- Table --}}
                <div class="modal-body p-0">
                    <div class="table-responsive br-history-table-scroll">
                        <table class="table table-sm table-hover mb-0 br-history-table br-history-table-fixed">
                            <colgroup>
                                <col class="br-hist-col-num" />
                                <col class="br-hist-col-date" />
                                <col class="br-hist-col-user" />
                                <col class="br-hist-col-action" />
                                <col class="br-hist-col-stmt" />
                                <col class="br-hist-col-details" />
                                <col class="br-hist-col-ip" />
                            </colgroup>
                            <thead class="br-history-thead">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Action Date</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Statement</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody id="bankReconUserHistoryBody">
                                <tr><td colspan="7" class="text-center text-muted py-5">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Loading history…
                                </td></tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="br-history-pager-wrap border-top p-3" id="bankReconUserHistoryPager"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Salary UTR upload (match by UTR in description) --}}
    <div class="modal fade" id="bankReconSalaryUploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-excel me-2"></i>Import salary UTR sheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="bankReconSalaryUploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted small mb-3">First row must be column headers, including a <strong>UTR</strong> column. Each UTR is searched in <strong>bank statement descriptions</strong> for the <strong>company and account</strong> you select below. Matched lines are tagged; category is set to <code>salary</code>.</p>
                        @if(!empty($bankAccountsEnabled))
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-building me-1"></i>Company <span class="text-danger">*</span></label>
                            <select class="form-select" name="company_id" id="salaryUploadCompany" required>
                                <option value="">Select company…</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-bank me-1"></i>Bank account <span class="text-danger">*</span></label>
                            <select class="form-select bank-account-select" name="bank_account_id" id="salaryUploadBankAccount" required disabled>
                                <option value="">Select company first…</option>
                            </select>
                            <small class="text-muted">UTR matching runs only on statements for this account (same as statement upload).</small>
                        </div>
                        @endif
                        <div class="upload-area-mini br-salary-upload-area border rounded p-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cloud-upload-fill text-primary me-2" style="font-size: 2rem;"></i>
                                    <div>
                                        <div class="fw-semibold">Salary Excel file</div>
                                        <div class="text-muted small">.xlsx or .xls — max 15MB</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="file" name="salary_file" id="bankReconSalaryFile" accept=".xlsx,.xls" class="d-none">
                                    <button type="button" class="btn btn-primary btn-sm" id="bankReconSalaryBrowseBtn"><i class="bi bi-folder2-open me-1"></i>Browse</button>
                                </div>
                            </div>
                            <div class="file-name-display mt-2" id="bankReconSalaryFileNameWrap" style="display: none;">
                                <div class="alert alert-success py-2 mb-0 d-flex justify-content-between align-items-center">
                                    <span class="text-break small" id="bankReconSalaryFileName"></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="bankReconSalaryFileRemove" type="button"><i class="bi bi-x-circle"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="bankReconSalaryUploadSubmit" disabled>
                            <i class="bi bi-cloud-upload me-1"></i>Upload &amp; match
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bankReconSalaryHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                    <div class="modal-header br-salary-hist-header flex-wrap gap-2 align-items-center">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="br-salary-hist-icon flex-shrink-0"><i class="bi bi-journal-text"></i></div>
                            <h5 class="modal-title mb-0 fw-bold text-truncate">Salary UTR — upload log</h5>
                        </div>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <a href="{{ route('bank-reconciliation.salary-master') }}" class="btn btn-sm btn-light text-success fw-semibold shadow-sm d-inline-flex align-items-center gap-1" title="Open all salary rows: filter and export">
                                <i class="bi bi-table"></i><span>Salary master</span>
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body p-0">
                        <div class="p-3 border-bottom bg-light">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label form-label-sm mb-0">Search file name</label>
                                    <input type="text" class="form-control form-control-sm" id="brSalaryHistSearch" placeholder="…">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label form-label-sm mb-0">From</label>
                                    <input type="date" class="form-control form-control-sm" id="brSalaryHistFrom">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label form-label-sm mb-0">To</label>
                                    <input type="date" class="form-control form-control-sm" id="brSalaryHistTo">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-success w-100" id="brSalaryHistApply"><i class="bi bi-search me-1"></i>Filter</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>File</th>
                                        <th class="text-center">Rows</th>
                                        <th class="text-center">Matched</th>
                                        <th>Uploaded at</th>
                                        <th>By</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="brSalaryHistBody">
                                    <tr><td colspan="7" class="text-center text-muted py-4">Open this dialog to load…</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bankReconSalaryDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-list-columns me-2"></i>Salary rows</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>UTR</th>
                                    <th>Name</th>
                                    <th>Branch</th>
                                    <th>Narration</th>
                                    <th>Net</th>
                                    <th>Status</th>
                                    <th>Stmt line</th>
                                </tr>
                            </thead>
                            <tbody id="brSalaryDetailBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bankReconSalaryReadonlyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content br-salary-ro-card">
                <div class="modal-header br-salary-ro-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="br-salary-ro-icon-wrap">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold" id="bankReconSalaryReadonlyTitle">Salary UTR match</h5>
                            <div class="small text-muted" id="bankReconSalaryReadonlySubtitle">View only</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="bankReconSalaryReadonlyBody"></div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Close
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
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="radiant-match-tab" data-bs-toggle="tab" data-bs-target="#radiant-match-content" type="button">
                                <i class="bi bi-brightness-high me-1"></i>RADIANT MATCH
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

                                {{-- Date of Collection (multi-select: one MOC row per date; bank line split optional) --}}
                                <div class="mb-3">
                                    <label class="income-tag-label">
                                        <span class="income-tag-label-dot" style="background:#f59e0b;"></span>DATE OF MOCDOC COLLECTIONS
                                        <span class="text-muted fw-normal ms-1" style="font-size:10px;text-transform:none;">(multi-date)</span>
                                    </label>
                                    <div class="income-tag-date-wrap">
                                        <i class="bi bi-calendar3 text-warning"></i>
                                        <input type="text" id="incomeTagDate" name="bank_recon_income_tag_collection_dt"
                                            class="income-tag-date-input" placeholder="Pick one or more dates..."
                                            autocomplete="off" autocorrect="off" spellcheck="false"
                                            data-lpignore="true" data-1p-ignore data-form-type="other">
                                    </div>
                                    <div id="incomeTagDateSplitWrap" class="mt-2" style="display:none;"></div>
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

                                {{-- Income tag supporting documents (types: Income or Both in Bank Accounts → Attachment types) --}}
                                <div class="mb-3 br-income-tag-att-block">
                                    <label class="income-tag-label">
                                        <span class="income-tag-label-dot" style="background:#7c3aed;"></span>SUPPORTING DOCUMENTS <span class="text-danger">*</span>
                                    </label>
                                    <p class="text-muted small mb-2"><strong>Cash only</strong> (single selected mode): upload <strong>three</strong> files — <strong>MOCDOC COLLECTION SCREEN SHOT</strong>, <strong>RADIANT SLIP</strong>, and <strong>COLLECTION LEDGER</strong> — one per type. <strong>Card, UPI, NEFT, Others</strong> (or any mix that is not cash-only): upload <strong>MOCDOC collection screenshot only</strong> and set its type. Configure matching names under <em>Bank Accounts → Attachment types</em> (scope <strong>Income tag</strong> or <strong>Both</strong>).</p>
                                    <input type="file" class="form-control form-control-sm mb-2" id="incomeTagAttachmentsInput" multiple accept=".pdf,.png,.jpg,.jpeg,.webp,.xlsx,.xls,.doc,.docx">
                                    <div id="incomeTagAttachmentStaging" class="bank-match-attachment-staging"></div>
                                </div>

                                {{-- Submit --}}
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm" id="applyIncomeTagBtn">
                                        <i class="bi bi-tag me-1"></i>Apply Income Tag
                                    </button>
                                </div>

                            </div>
                        </div>

                        {{-- Radiant cash pickup --}}
                        <div class="tab-pane fade" id="radiant-match-content">
                            <div class="br-radiant-panel">

                                {{-- Header banner --}}
                                <div class="br-radiant-header">
                                    <div class="br-radiant-header-icon">
                                        <i class="bi bi-brightness-high-fill"></i>
                                    </div>
                                    <div>
                                        <div class="br-radiant-header-title">Radiant Cash Pickup</div>
                                        <div class="br-radiant-header-sub">Link this deposit to a Radiant slip for the same date</div>
                                    </div>
                                </div>

                                {{-- Keyword field --}}
                                <div class="br-radiant-section">
                                    <label class="br-radiant-field-label" for="radiantMatchAgainstInput">
                                        <span class="br-radiant-dot" style="background:#f97316;"></span>
                                        Match Against <span class="br-radiant-field-hint">(location / keyword)</span>
                                    </label>
                                    <div class="br-radiant-input-wrap">
                                        <i class="bi bi-search br-radiant-input-icon"></i>
                                        <input type="text" class="form-control form-control-sm br-radiant-input" id="radiantMatchAgainstInput"
                                               maxlength="255" placeholder="e.g. Thiruvallur, Ariyalur…" autocomplete="off">
                                    </div>
                                    <p class="br-radiant-hint-text">
                                        Used in Radiant mismatch alerts when automatic <em>BY CASH + location</em> search fails.
                                    </p>
                                </div>

                                {{-- Pickup dropdown --}}
                                <div class="br-radiant-section">
                                    <label class="br-radiant-field-label" for="radiantCashPickupSelect">
                                        <span class="br-radiant-dot" style="background:#c2410c;"></span>
                                        Radiant Pickup Slip <span class="br-radiant-field-hint">(same date as bank line)</span>
                                    </label>

                                    {{-- Loading skeleton shown while AJAX fetches --}}
                                    <div class="br-radiant-pickup-loading" id="radiantPickupLoading" style="display:none;">
                                        <div class="br-radiant-loading-bar"></div>
                                        <span class="br-radiant-loading-text">Fetching pickups for this date…</span>
                                    </div>

                                    <select id="radiantCashPickupSelect" class="form-select form-select-sm br-radiant-pickup-select" data-placeholder="Search branch — amount…" disabled>
                                        <option value="">— Open a deposit row to load pickups —</option>
                                    </select>

                                    {{-- Pickup count badge (filled by JS) --}}
                                    <div class="br-radiant-pickup-meta" id="radiantPickupMeta" style="display:none;">
                                        <span class="br-radiant-count-badge" id="radiantPickupCount">0</span>
                                        <span class="br-radiant-count-label">pickups found for this date</span>
                                    </div>
                                </div>

                                {{-- Action row --}}
                                <div class="br-radiant-actions">
                                    <button type="button" class="btn br-radiant-btn-clear" id="clearRadiantMatchBtn">
                                        <i class="bi bi-x-circle me-1"></i>Clear
                                    </button>
                                    <button type="button" class="btn br-radiant-btn-save" id="saveRadiantMatchBtn">
                                        <i class="bi bi-check2-circle me-1"></i>Save &amp; Link
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

    {{-- Income tag: read-only view (opened when clicking an income-matched row) --}}
    <div class="modal fade right br-income-ro-modal" id="incomeTagReadonlyModal" tabindex="-1" data-bs-backdrop="true">
        <div class="modal-dialog modal-dialog-scrollable br-income-ro-dialog">
            <div class="modal-content br-income-ro-content">
                <div class="modal-header br-income-ro-header border-0">
                    <h5 class="modal-title mb-0 d-flex align-items-center gap-2 text-white">
                        <span class="br-income-ro-header-icon"><i class="bi bi-tag-fill"></i></span>
                        Income tag details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body br-income-ro-body">
                    <p class="br-income-ro-hint small mb-3">Read-only summary. To change tags, use <strong>Unmatch Income</strong> and/or <strong>Unmatch Radiant</strong> on the row.</p>
                    <div id="incomeTagReadonlyBody" class="income-tag-panel br-income-ro-panel"></div>
                </div>
                <div class="modal-footer br-income-ro-footer border-0">
                    <button type="button" class="btn br-income-ro-close-btn" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- billing_list rows for income-tagged statements (collection date + branch + payment mode) --}}
    <div class="modal fade" id="incomeBillingListModal" tabindex="-1" aria-labelledby="incomeBillingListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title mb-1" id="incomeBillingListModalLabel">
                            <i class="bi bi-receipt-cutoff me-2 text-primary"></i>Income bills (<span class="text-muted small">billing_list</span>)
                        </h5>
                        <p class="text-muted small mb-0">
                            Branch: <strong id="incomeBillingListModalBranch">—</strong>
                            · <span id="incomeBillingListModalCount">0</span> row(s)
                        </p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Bill no.</th>
                                    <th scope="col">Bill date</th>
                                    <th scope="col">Payment</th>
                                    <th scope="col">Type</th>
                                    <th scope="col" class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="incomeBillingListModalTableBody"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">Total</th>
                                    <th class="text-end" id="incomeBillingListModalTotal">₹0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Before match: nature of payment (account_tbl, same as vendor bill lines) + attachments --}}
    <div class="modal fade" id="bankMatchDetailsModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bank-match-details-modal">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title mb-1"><i class="bi bi-journal-text me-2 text-primary"></i>Match details</h5>
                        <p class="text-muted small mb-0">Use <strong>Nature</strong> for chart accounts (required). Use <strong>Attachments</strong> to upload <span class="text-danger fw-semibold">at least one file</span> (required), preview each, and tag as PO, Quotation, etc.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <ul class="nav nav-tabs nav-justified mb-3" id="bankMatchDetailsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="br-match-tab-nature" data-bs-toggle="tab" data-bs-target="#brMatchPaneNature" type="button" role="tab">
                                <i class="bi bi-journal-check me-1"></i>Nature of payment
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="br-match-tab-files" data-bs-toggle="tab" data-bs-target="#brMatchPaneFiles" type="button" role="tab">
                                <i class="bi bi-paperclip me-1"></i>Attachments <span class="text-danger">*</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="bankMatchDetailsTabContent">
                        <div class="tab-pane fade show active" id="brMatchPaneNature" role="tabpanel">
                            <div class="bank-match-details-card mb-0">
                                <label class="form-label br-bank-match-nature-label mb-2">Nature of payment <span class="text-danger">*</span></label>
                                <div class="tax-dropdown-wrapper br-bank-match-nature w-100">
                                    <input type="text" class="form-control dropdown-search-input br-bank-match-nature-input" placeholder="Select Nature" readonly autocomplete="off">
                                    <input type="hidden" id="bankMatchNatureIds" value="">
                                    <div class="dropdown-menu tax-dropdown br-bank-match-dd-template">
                                        <div class="inner-search-container">
                                            <input type="text" class="inner-search" placeholder="Search Nature..." autocomplete="off">
                                        </div>
                                        <div class="d-flex justify-content-between p-2 border-bottom br-bank-match-nature-actions" style="gap:8px;">
                                            <button type="button" class="btn btn-sm btn-outline-primary br-bank-match-select-all">All</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary br-bank-match-clear">Clear</button>
                                        </div>
                                        <div class="dropdown-list multiselect br-bank-match-account-list" id="bankMatchNatureAccountList"></div>
                                    </div>
                                </div>
                                <div class="form-text mt-2">Search, then select at least one account.</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="brMatchPaneFiles" role="tabpanel">
                            <div class="bank-match-details-card bank-match-details-card-files mb-0">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-cloud-upload text-secondary"></i> Add files <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="bankMatchAttachmentsInput" multiple accept=".pdf,.png,.jpg,.jpeg,.webp,.xlsx,.xls,.doc,.docx">
                                <div class="form-text mb-2">At least one file is required to confirm a match. Multiple files allowed (max ~15 MB each). After adding, set <strong>Document type</strong> for each file.</div>
                                <div id="bankMatchAttachmentStaging" class="bank-match-attachment-staging"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmBankMatchDetails">
                        <i class="bi bi-check2-circle me-1"></i>Confirm &amp; match
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- View match attachments (public URLs — image/PDF preview + open in new tab) --}}
    <div class="modal fade" id="bankMatchAttachmentsViewerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content br-att-modal-content">
                <div class="modal-header br-att-modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="br-att-modal-icon"><i class="bi bi-paperclip"></i></div>
                        <div>
                            <h5 class="modal-title mb-0">Match Attachments</h5>
                            <div class="br-att-modal-subtitle">Uploaded files for this transaction</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body br-att-modal-body">
                    <div id="bankMatchAttachmentsViewerBody" class="bank-match-att-viewer-body"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Income tag: remark + matched-by (from bank_statements + income recon context) --}}
    <div class="modal fade" id="bankReconIncomeTagDetailModal" tabindex="-1" aria-labelledby="bankReconIncomeTagDetailTitle">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content br-income-detail-modal">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="bankReconIncomeTagDetailTitle">
                        <i class="bi bi-tag-fill text-info me-2"></i>Income tag details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <dl class="row br-income-detail-dl mb-0">
                        <dt class="col-sm-4">Branch</dt>
                        <dd class="col-sm-8" id="brIncomeDetailBranch">—</dd>
                        <dt class="col-sm-4">Collection date</dt>
                        <dd class="col-sm-8" id="brIncomeDetailDate">—</dd>
                        <dt class="col-sm-4">Tagged at</dt>
                        <dd class="col-sm-8" id="brIncomeDetailAt">—</dd>
                        <dt class="col-sm-4">Matched by</dt>
                        <dd class="col-sm-8" id="brIncomeDetailBy">—</dd>
                        <dt class="col-sm-4">MOC mismatch remark</dt>
                        <dd class="col-sm-8">
                            <div class="br-income-detail-remark border rounded p-2 bg-light" id="brIncomeDetailRemark">—</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Drill-down: statements by nature (chart account) or by bill zone/branch — data via AJAX only --}}
    <div class="offcanvas offcanvas-end br-drill-offcanvas" tabindex="-1" id="bankReconDrilldownOffcanvas" aria-labelledby="bankReconDrilldownTitle">
        {{-- Header --}}
        <div class="br-drill-header">
            <div class="br-drill-header-inner">
                <div class="br-drill-header-icon" id="bankReconDrilldownHeaderIcon">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="br-drill-header-text">
                    <div class="br-drill-header-title" id="bankReconDrilldownTitle">Statements</div>
                    <div class="br-drill-header-sub" id="bankReconDrilldownSubtitle"></div>
                </div>
            </div>
            <button type="button" class="br-drill-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Zone / branch filter bar (shown only for zone mode) --}}
        <div id="bankReconDrilldownZoneFilters" class="br-drill-filter-bar" style="display:none;">
            <div class="br-drill-filter-row">
                <div class="br-drill-filter-field">
                    <label class="br-drill-filter-label"><i class="bi bi-diagram-3 me-1"></i>Zone</label>
                    <select class="form-select form-select-sm br-drill-select" id="bankReconDrilldownZoneSelect"></select>
                </div>
                <div class="br-drill-filter-field">
                    <label class="br-drill-filter-label"><i class="bi bi-geo-alt me-1"></i>Branch</label>
                    <select class="form-select form-select-sm br-drill-select" id="bankReconDrilldownBranchSelect">
                        <option value="">All branches</option>
                    </select>
                </div>
                <div class="br-drill-filter-apply">
                    <button type="button" class="btn br-drill-apply-btn" id="bankReconDrilldownApplyZone">
                        <i class="bi bi-search me-1"></i>Apply
                    </button>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="offcanvas-body br-drill-body d-flex flex-column">
            {{-- Loading --}}
            <div id="bankReconDrilldownLoading" class="br-drill-loading" style="display:none;">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <span>Loading statements…</span>
            </div>
            {{-- Empty --}}
            <div id="bankReconDrilldownEmpty" class="br-drill-empty" style="display:none;">
                <i class="bi bi-inbox br-drill-empty-icon"></i>
                <div class="br-drill-empty-text">No matching statements found</div>
            </div>
            {{-- Table --}}
            <div class="table-responsive flex-grow-1 br-drill-table-wrap" id="bankReconDrilldownTableWrap">
                <table class="table mb-0 br-drill-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th class="text-end">Withdrawal</th>
                            <th class="text-end">Deposit</th>
                            <th>Bill</th>
                            <th>Vendor</th>
                            <th>Bank account</th>
                        </tr>
                    </thead>
                    <tbody id="bankReconDrilldownBody"></tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <nav class="br-drill-pagination-wrap" id="bankReconDrilldownPagination" aria-label="Drill-down pagination"></nav>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        window.bankAccountsEnabled = @json(!empty($bankAccountsEnabled));
        {{-- Full account_tbl list for nature dropdown (same as purchase board / Tblaccount) --}}
        window.BANK_RECON_CHART_ACCOUNTS = @json($chartAccountsForSelect ?? []);
        {{-- var on window so external bank-reconciliation.js can read URLs (const is not shared across script files) --}}
        window.bankReconDateFrom = @json($bankReconFyStart->format('Y-m-d'));
        window.bankReconDateTo = @json($bankReconFyEnd->format('Y-m-d'));
        {{-- Web path to Laravel public/ — same /public/ insert as BankStatementController when APP_URL omits public --}}
        @php
            $bankReconMatchFilesPath = parse_url((string) asset('bank_recon_match_files'), PHP_URL_PATH) ?: '';
            if ($bankReconMatchFilesPath !== '' && strpos($bankReconMatchFilesPath, '/public/bank_recon_match_files') === false) {
                $fixedBrPath = preg_replace('#^/([^/]+)/(bank_recon_match_files.*)$#', '/$1/public/$2', $bankReconMatchFilesPath, 1);
                if ($fixedBrPath !== null && $fixedBrPath !== $bankReconMatchFilesPath) {
                    $bankReconMatchFilesPath = $fixedBrPath;
                }
            }
            $bankReconPublicPrefix = $bankReconMatchFilesPath !== ''
                ? rtrim(str_replace('\\', '/', dirname($bankReconMatchFilesPath)), '/')
                : rtrim((string) request()->getBasePath(), '/');
        @endphp
        window.BANK_RECON_PUBLIC_PREFIX = @json($bankReconPublicPrefix);
        window.bankReconRoutes = {
            upload: "{{ route('bank-reconciliation.upload') }}",
            statements: "{{ route('bank-reconciliation.statements') }}",
            statementsExport: "{{ route('bank-reconciliation.statements-export') }}",
            searchBills: "{{ route('bank-reconciliation.search-bills') }}",
            filterBills: "{{ route('bank-reconciliation.filter-bills') }}",
            match: "{{ route('bank-reconciliation.match') }}",
            unmatch: "{{ route('bank-reconciliation.unmatch', ':id') }}",
            destroy: "{{ route('bank-reconciliation.destroy', ':id') }}",
            incomeTag: "{{ route('bank-reconciliation.income-tag') }}",
            incomeUnmatch: "{{ route('bank-reconciliation.income-unmatch', ':id') }}",
            incomeTagZones: "{{ route('bank-reconciliation.income-tag.zones') }}",
            incomeTagBranches: "{{ route('bank-reconciliation.income-tag.branches') }}",
            incomeTagResolve: "{{ route('bank-reconciliation.income-tag.resolve-description') }}",
            radiantMatchAgainst: "{{ route('bank-reconciliation.radiant-match-against') }}",
            radiantCashPickupsForDate: "{{ route('bank-reconciliation.radiant-cash-pickups-for-date') }}",
            radiantUnmatch: "{{ route('bank-reconciliation.radiant-unmatch', ':id') }}",
            accounts: "{{ route('bank-reconciliation.accounts') }}",
            accountsStore: "{{ route('bank-reconciliation.accounts.store') }}",
            accountsUpdateBase: "{{ url('/bank-reconciliation/accounts') }}",
            uploadBatches: "{{ route('bank-reconciliation.upload-batches') }}",
            batchFile: "{{ url('/bank-reconciliation/batch-file') }}",
            matchedByOptions: "{{ route('bank-reconciliation.matched-by-options') }}",
            chartAccounts: "{{ route('bank-reconciliation.chart-accounts') }}",
            quickFilterOptions: "{{ route('bank-reconciliation.quick-filter-options') }}",
            userHistory: "{{ route('bank-reconciliation.user-history') }}",
            salaryUtrUpload: "{{ route('bank-reconciliation.salary-utr-upload') }}",
            salaryUtrUploads: "{{ route('bank-reconciliation.salary-utr-uploads') }}",
            salaryUtrUploadRows: "{{ url('/bank-reconciliation/salary-utr-uploads') }}/:id/rows",
            salaryUtrUploadDelete: "{{ url('/bank-reconciliation/salary-utr-uploads') }}/:id",
            statementShow: "{{ url('/bank-reconciliation/statement') }}/:id",
            matchAttachmentTypes: "{{ route('bank-reconciliation.match-attachment-types.index') }}",
            matchAttachmentTypesStore: "{{ route('bank-reconciliation.match-attachment-types.store') }}",
            matchAttachmentTypesUpdateBase: "{{ url('/bank-reconciliation/match-attachment-types') }}",
            matchAttachmentTypesDestroy: "{{ route('bank-reconciliation.match-attachment-types.destroy', ':id') }}",
            drilldownByNature: "{{ route('bank-reconciliation.drilldown.by-nature') }}",
            drilldownByZone: "{{ route('bank-reconciliation.drilldown.by-zone') }}",
            billPrint: "{{ route('superadmin.getbillprint') }}",
            billDashboard: "{{ route('superadmin.getbill') }}",
            vendorDashboard: "{{ route('superadmin.getvendor') }}",
            incomeReconciliationOverviewNew: "{{ route('superadmin.overviewindexnew') }}",
        };
        var routes = window.bankReconRoutes;
        window.bankReconSuperAdmin = @json(!empty($bankReconSuperAdmin));
    </script>
    <script>window.BANK_RECON_BATCH_PREVIEW_BASE = "{{ url('/bank-reconciliation/batch-preview') }}";</script>
    <script src="{{ asset('/assets/js/bank-reconciliation/batch-preview-modal.js') }}"></script>
    <script src="{{ asset('/assets/js/bank-reconciliation/bank-reconciliation.js') }}"></script>
    <script>
    // ============================================
    // FLATPICKR - Bank Reconciliation (replaced daterangepicker)
    // ============================================
    $(document).ready(function() {
        window.bankReconDefaultFyValue = @json($bankReconFyStart->format('Y-m-d') . '|' . $bankReconFyEnd->format('Y-m-d'));
        function bankReconWireTxnRangeFlatpickr(el) {
            if (!el) return;
            window.bankReconSkipFpChange = false;
            flatpickr(el, {
                mode: 'range',
                dateFormat: 'd/m/Y',
                allowInput: true,
                onChange: function(selectedDates) {
                    if (window.bankReconSkipFpChange) return;
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
                    if (typeof window.syncBankReconFinancialYearSelect === 'function') {
                        window.syncBankReconFinancialYearSelect();
                    }
                    if (typeof window.syncBankReconTransactionDatePickers === 'function') {
                        window.syncBankReconTransactionDatePickers();
                    }
                },
                onClose: function(selectedDates) {
                    if (window.bankReconSkipFpChange) return;
                    if (!selectedDates || selectedDates.length === 0) {
                        if (typeof window.restoreBankReconWindowDatesAfterClearingTxnDay === 'function') {
                            window.restoreBankReconWindowDatesAfterClearingTxnDay();
                        } else {
                            window.bankReconDateFrom = null;
                            window.bankReconDateTo = null;
                        }
                        if (typeof window.syncBankReconFinancialYearSelect === 'function') {
                            window.syncBankReconFinancialYearSelect();
                        }
                        if (typeof window.syncBankReconTransactionDatePickers === 'function') {
                            window.syncBankReconTransactionDatePickers();
                        }
                    } else if (selectedDates.length === 1) {
                        var d = moment(selectedDates[0]).format('YYYY-MM-DD');
                        window.bankReconDateFrom = d;
                        window.bankReconDateTo = d;
                        if (typeof window.syncBankReconFinancialYearSelect === 'function') {
                            window.syncBankReconFinancialYearSelect();
                        }
                        if (typeof window.syncBankReconTransactionDatePickers === 'function') {
                            window.syncBankReconTransactionDatePickers();
                        }
                    }
                }
            });
        }

        var filterDateEl = document.getElementById('filterDateRange');
        var qfTxnDateEl = document.getElementById('qfTransactionDateRange');
        if (filterDateEl || qfTxnDateEl) {
            window.bankReconDateFrom = @json($bankReconFyStart->format('Y-m-d'));
            window.bankReconDateTo = @json($bankReconFyEnd->format('Y-m-d'));
            if (filterDateEl) {
                bankReconWireTxnRangeFlatpickr(filterDateEl);
            }
            if (qfTxnDateEl) {
                bankReconWireTxnRangeFlatpickr(qfTxnDateEl);
            }
            if (typeof window.syncBankReconTransactionDatePickers === 'function') {
                window.syncBankReconTransactionDatePickers();
            }
        }

        window.bankReconQfIncomeCollectionFp = window.bankReconQfIncomeCollectionFp || null;
        window.bankReconInitQfIncomeCollectionFlatpickr = function () {
            var el = document.getElementById('qfIncomeCollectionDates');
            if (!el || el._flatpickr || typeof flatpickr === 'undefined') {
                return;
            }
            window.bankReconQfIncomeCollectionFp = flatpickr(el, {
                mode: 'multiple',
                altInput: true,
                altFormat: 'd/m/Y',
                dateFormat: 'Y-m-d',
                allowInput: false,
                clickOpens: true
            });
        };
        window.bankReconInitQfIncomeCollectionFlatpickr();

        var filterMatchedDateEl = document.getElementById('filterMatchedDateRange');
        if (filterMatchedDateEl) {
            window.bankReconMatchedDateFrom = null;
            window.bankReconMatchedDateTo = null;
            flatpickr(filterMatchedDateEl, {
                mode: 'range',
                dateFormat: 'd/m/Y',
                onChange: function(selectedDates) {
                    if (selectedDates.length === 2) {
                        window.bankReconMatchedDateFrom = moment(selectedDates[0]).format('YYYY-MM-DD');
                        window.bankReconMatchedDateTo = moment(selectedDates[1]).format('YYYY-MM-DD');
                    } else if (selectedDates.length === 1) {
                        window.bankReconMatchedDateFrom = moment(selectedDates[0]).format('YYYY-MM-DD');
                        window.bankReconMatchedDateTo = null;
                    } else {
                        window.bankReconMatchedDateFrom = null;
                        window.bankReconMatchedDateTo = null;
                    }
                },
                onClose: function(selectedDates) {
                    if (selectedDates.length === 0) {
                        window.bankReconMatchedDateFrom = null;
                        window.bankReconMatchedDateTo = null;
                    }
                }
            });
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

        if (typeof window.applyBankReconDomFiltersToCurrent === 'function') {
            window.applyBankReconDomFiltersToCurrent();
        }
    });
    </script>
    {{-- bank-reconciliation.js is loaded before this flatpickr block so sync helpers exist on init --}}
    
    @include('superadmin.superadminfooter')
</body>
</html>