<!doctype html>
<html lang="en">
<!-- [Head] start -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<!-- [Head] end -->
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/pettycash.css') }}" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<style>
    .gst_seperate span {
        margin: 0;
        font-size: 10px;
    }

    .sm-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .45);
        z-index: 9998;
    }

    .sm-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .sm-modal.show {
        display: flex;
    }

    .sm-modal-overlay.show {
        display: block;
    }

    .sm-modal-box {
        background: #fff;
        border-radius: 14px;
        padding: 28px 30px;
        width: 100%;
        max-width: 560px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .18);
        position: relative;
        animation: smSlideIn .2s ease;
    }

    @keyframes smSlideIn {
        from {
            transform: translateY(-20px);
            opacity: 0
        }

        to {
            transform: translateY(0);
            opacity: 1
        }
    }

    .sm-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .sm-modal-title {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sm-modal-close {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #f3f4f6;
        border: none;
        cursor: pointer;
        font-size: 18px;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sm-modal-close:hover {
        background: #e5e7eb;
    }

    .sm-form-row {
        display: flex;
        gap: 14px;
        margin-bottom: 16px;
    }

    .sm-form-group {
        flex: 1;
    }

    .sm-form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .sm-form-group .form-control,
    .sm-form-group select {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 13px;
        padding: 8px 12px;
        width: 100%;
    }

    .sm-modal-footer {
        display: flex;
        gap: 10px;
        margin-top: 22px;
        justify-content: flex-end;
    }

    .sm-btn-primary {
        padding: 9px 22px;
        background: linear-gradient(135deg, #4f6ef7, #7c3aed);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .sm-btn-cancel {
        padding: 9px 22px;
        background: #f3f4f6;
        color: #374151;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
</style>

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

    @php
        $stats =
            $stats ??
            ($pettycashStats ?? [
                'total' => 0,
                'total_amount' => 0,
                'approved' => 0,
                'approved_amount' => 0,
                'pending' => 0,
                'pending_amount' => 0,
                'rejected' => 0,
                'draft' => 0,
                'draft_amount' => 0,
            ]);
    @endphp

    <div class="pc-container">
        <div class="pc-content">

            <div class="qd-card">

                {{-- ── Header ── --}}
                <div class="qd-header">
                    <div class="qd-header-title">
                        <i class="bi bi-file-earmark-text"></i>
                        Petty Cash Dashboard
                    </div>
                    <div class="qd-header-actions">
                        {{-- Toggle: Stats --}}
                        <button class="btn btn-sm qd-toggle-btn" id="toggleStats" title="Toggle Stats">
                            <i class="bi bi-bar-chart-line me-1"></i>Stats
                            <i class="bi bi-chevron-up qd-toggle-icon" id="statsChevron"></i>
                        </button>
                        {{-- Toggle: Filters --}}
                        <button class="btn btn-sm qd-toggle-btn" id="toggleFilters" title="Toggle Filters">
                            <i class="bi bi-funnel me-1"></i>Filter
                            <i class="bi bi-chevron-up qd-toggle-icon" id="filtersChevron"></i>
                        </button>
                        <a href="{{ route('superadmin.getpettycashcreate') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>New Petty Cash
                        </a>
                        {{-- <a href="{{ route('superadmin.getpettycashreports') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-graph-up me-1"></i>Reports
                        </a> --}}
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="#" class="dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#importModal">
                                        <i class="bi bi-upload me-2"></i>Import Petty Cash
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a href="{{ route('superadmin.getpettycashreports') }}" class="dropdown-item">
                                        <i class="bi bi-graph-up me-2"></i>Petty cash reports
                                    </a></li>
                                <li><a href="{{ route('superadmin.getadvances') }}" class="dropdown-item">
                                        <i class="bi bi-wallet2 me-2"></i>Advances
                                    </a></li>
                                <li><a href="{{ route('superadmin.getadvancescreate') }}" class="dropdown-item">
                                        <i class="bi bi-plus-circle me-2"></i>Record advance
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a href="#" class="dropdown-item" id="exportXlsx">
                                        <i class="bi bi-file-earmark-excel me-2 text-success"></i>Export XLSX
                                    </a></li>
                                <li><a href="#" class="dropdown-item" id="exportCsv">
                                        <i class="bi bi-file-earmark-text me-2 text-secondary"></i>Export CSV
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- ── Stats ── --}}
                <div class="qd-stats" id="statsSection">
                    <div class="qd-stat-card qd-stat-blue qd-stat-active" data-stat-filter="" title="Show all">
                        <div class="qd-stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Total Petty Cash</div>
                            <div class="qd-stat-value">{{ $stats['total'] }}</div>
                            <div class="qd-stat-sub">₹{{ number_format($stats['total_amount'], 2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-green" data-stat-filter="Approved" title="Filter: Approved">
                        <div class="qd-stat-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Approved</div>
                            <div class="qd-stat-value">{{ $stats['approved'] }}</div>
                            <div class="qd-stat-sub">₹{{ number_format($stats['approved_amount'], 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-orange" data-stat-filter="pending" title="Filter: Pending">
                        <div class="qd-stat-icon"><i class="bi bi-clock"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Pending</div>
                            <div class="qd-stat-value">{{ $stats['pending'] }}</div>
                            <div class="qd-stat-sub">₹{{ number_format($stats['pending_amount'], 2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-red" data-stat-filter="rejected" title="Filter: Rejected">
                        <div class="qd-stat-icon"><i class="bi bi-x-circle"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Rejected</div>
                            <div class="qd-stat-value">{{ $stats['rejected'] }}</div>
                            <div class="qd-stat-sub">
                                ₹{{ number_format($stats['rejected_amount'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>

                {{-- ── Filters ── --}}
                <div class="qd-filters">
                    {{-- Row 1: Date, Company, State, Zone --}}
                    <div class="qd-filter-row">
                        <div class="qd-filter-group">
                            <label><i class="bi bi-calendar3 me-1"></i>Date Range</label>
                            <div class="qd-date-wrap" id="reportrange">
                                <i class="fa fa-calendar"></i>
                                <span id="data_values">All Dates</span>
                                <i class="fa fa-caret-down" style="margin-left:auto;"></i>
                                <input type="hidden" class="data_values">
                            </div>
                        </div>

                        <div class="qd-filter-group tax-dropdown-wrapper company-section">
                            <label>Company</label>
                            <input type="text" class="form-control company-search-input dropdown-search-input"
                                placeholder="Select Company" readonly>
                            <input type="hidden" name="company_id" class="company_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search"
                                        placeholder="Search Company..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect company-list"></div>
                            </div>
                        </div>

                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label>State</label>
                            <input type="text" class="form-control state-search-input dropdown-search-input"
                                placeholder="Select State" readonly>
                            <input type="hidden" name="state_id" class="state_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search"
                                        placeholder="Search State..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect state-list">
                                    <div data-value="Tamil Nadu" data-id="1">Tamil Nadu</div>
                                    <div data-value="Karnataka" data-id="2">Karnataka</div>
                                    <div data-value="Kerala" data-id="3">Kerala</div>
                                    <div data-value="Andra Pradesh" data-id="4">Andra Pradesh</div>
                                    <div data-value="International" data-id="5">International</div>
                                </div>
                            </div>
                        </div>

                        <div class="qd-filter-group tax-dropdown-wrapper zone-section">
                            <label>Zone</label>
                            <input type="text" class="form-control zone-search-input dropdown-search-input"
                                placeholder="Select Zone" readonly>
                            <input type="hidden" name="zone_id" class="zone_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search"
                                        placeholder="Search Zone..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect zone-list"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Branch, Vendor, Nature, Status --}}
                    <div class="qd-filter-row" style="margin-top:10px;">
                        <div class="qd-filter-group tax-dropdown-wrapper branch-section">
                            <label>Branch</label>
                            <input type="text" class="form-control branch-search-input dropdown-search-input"
                                placeholder="Select Branch" readonly>
                            <input type="hidden" name="branch_id" class="branch_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search"
                                        placeholder="Search Branch..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect branch-list"></div>
                            </div>
                        </div>

                        {{-- <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label>Vendor</label>
                            <input type="text" class="form-control vendor-search-input dropdown-search-input" placeholder="Select Vendor" readonly>
                            <input type="hidden" name="vendor_id" class="vendor_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Vendor..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect vendor-list"></div>
                            </div>
                        </div> --}}

                        {{-- <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label>Nature of Payment</label>
                            <input type="text" class="form-control nature-search-input dropdown-search-input" placeholder="Select Nature" readonly>
                            <input type="hidden" name="nature_id" class="nature_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Nature..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect account-list"></div>
                            </div>
                        </div> --}}

                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label>Status</label>
                            <input type="text" class="form-control status-search-input dropdown-search-input"
                                placeholder="Select Status" readonly>
                            <input type="hidden" name="status_id" class="status_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search"
                                        placeholder="Search Status..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                </div>
                                <div class="dropdown-list multiselect status-list">
                                    <div data-value="pending" data-id="1">Pending</div>
                                    <div data-value="approved" data-id="2">Approved</div>
                                    <div data-value="rejected" data-id="3">Rejected</div>
                                    <div data-value="draft" data-id="4">Draft</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Search bar ── --}}
                <div class="qd-search-row">
                    <div class="qd-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" class="universal_search" placeholder="Search petty cash...">
                    </div>
                </div>

                {{-- ── Applied filters ── --}}
                <div class="qd-applied-bar">
                    <span class="applied-label">Filters:</span>
                    <div id="filter-summary" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
                </div>

                {{-- ── Table ── --}}
                <div class="qd-table-wrap">
                    <div id="pettycash-body">
                        @include('superadmin.pettycash.pettycash_rows', [
                            'pettycashlist' => $pettycashlist,
                            'perPage' => $perPage,
                        ])
                    </div>
                </div>

            </div>{{-- qd-card --}}
            {{-- OLD COMMENTED CODE REMOVED --}}
            {{-- <div class="col-xl-3 col-md-3">
                      <label for="vendor">Zone</label>
                      <div class="tax-dropdown-wrapper account-section" >
                          <input type="text" class="form-control zone-search-input" name="zone" placeholder="Select a Zones" readonly>
                          <input type="hidden" name="zone_id" class="zone_id">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="inner-search-container">
                              <input type="text" class="zone-inner-search" placeholder="Search...">
                            </div>
                            <div class="zone-list">
                            </div>
                          </div>
                          <span class="error_zone" style="color:red"></span>
                        </div>
                    </div> --}}
            {{-- <div class="col-xl-3 col-md-3">
                      <label for="vendor">Zone</label>
                      <div class="tax-dropdown-wrapper account-section">
                        <input type="text" class="form-control zone-search-input" name="zone" placeholder="Select Zones" readonly>
                        <input type="hidden" name="zone_ids" class="zone_id">

                        <div class="dropdown-menu tax-dropdown">
                          <div class="inner-search-container p-2 border-bottom">
                            <input type="text" class="zone-inner-search form-control form-control-sm" placeholder="Search...">
                          </div>

                          <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap: 10px;">
                            <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                          </div>

                          <div class="zone-list p-2" style="">
                            <!-- dynamically appended zones -->
                          </div>
                        </div>

                        <span class="error_zone text-danger"></span>
                      </div>
                    </div> --}}

        </div>

    </div>

    {{-- Zoho-style expense detail (slide-over): Details / Comments / History --}}
    <div id="pc-sp-overlay" class="pc-sp-overlay" aria-hidden="true"></div>
    <aside id="pc-sp-panel" class="pc-sp-panel" aria-hidden="true">
        <div class="pc-sp-header">
            <div>
                <div class="pc-sp-title" id="pc-sp-title">—</div>
                <div class="pc-sp-status-row" id="pc-sp-subtitle"></div>
            </div>
            <div class="pc-sp-actions">
                <a href="#" class="pc-sp-iconbtn" id="pc-sp-edit" title="Edit"><i class="bi bi-pencil"></i></a>
                <button type="button" class="pc-sp-iconbtn" id="pc-sp-close" title="Close"><i
                        class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <div class="pc-sp-body">
            <div id="pc-sp-associated" style="display:none;">
                <div class="pc-sp-associated">
                    <div class="pc-sp-associated-k"><i class="bi bi-folder2-open"></i> Associated report</div>
                    <a href="#" id="pc-sp-report-link" target="_blank" rel="noopener noreferrer"
                        title="Open this report in a new tab">—</a>
                </div>
            </div>
            <div class="pc-sp-summary">
                <div class="pc-sp-receipt">
                    <img id="pc-sp-receipt-img" src="" alt="Receipt"
                        style="display:none;max-width:100%;max-height:220px;object-fit:contain;border-radius:8px;">
                    <div id="pc-sp-receipt-placeholder" class="pc-sp-receipt-ph">
                        <i class="bi bi-image"></i>
                        No receipt attached
                    </div>
                </div>
                <div>
                    <div class="pc-sp-amount-block">
                        <div class="pc-sp-reimb-tag" id="pc-sp-reimb-tag">—</div>
                        <div class="pc-sp-amount-big" id="pc-sp-amount-big">—</div>
                    </div>
                    <div class="pc-sp-meta">
                        <div class="pc-sp-meta-row"><span class="pc-sp-meta-k">Date</span><span
                                class="pc-sp-meta-v" id="pc-sp-meta-date">—</span></div>
                        <div class="pc-sp-meta-row"><span class="pc-sp-meta-k">Category</span><span
                                class="pc-sp-meta-v" id="pc-sp-meta-cat">—</span></div>
                        <div class="pc-sp-meta-row"><span class="pc-sp-meta-k">Merchant</span><span
                                class="pc-sp-meta-v" id="pc-sp-meta-merchant">—</span></div>
                        <div class="pc-sp-meta-row"><span class="pc-sp-meta-k">Status</span><span
                                class="pc-sp-meta-v" id="pc-sp-meta-status">—</span></div>
                    </div>
                </div>
            </div>
            <div class="pc-sp-tabs">
                <button type="button" class="pc-sp-tab active" data-pc-pane="details">Details</button>
                <button type="button" class="pc-sp-tab" data-pc-pane="comments">Comments</button>
                <button type="button" class="pc-sp-tab" data-pc-pane="history">History</button>
            </div>
            <div class="pc-sp-tab-pane active" id="pc-pane-details">
                <div class="pc-sp-detail-fields pc-sp-meta">
                    <div class="pc-sp-meta-row"><span class="pc-sp-meta-k">Description</span><span
                            class="pc-sp-meta-v" id="pc-det-desc">—</span></div>
                    <div class="pc-sp-meta-row"><span class="pc-sp-meta-k">Policy</span><span
                            class="pc-sp-meta-v" id="pc-det-policy">—</span></div>
                    <div class="pc-sp-meta-row"><span class="pc-sp-meta-k">Invoice#</span><span
                            class="pc-sp-meta-v" id="pc-det-inv">—</span></div>
                </div>
                <table class="pc-sp-items-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="pc-sp-items-body"></tbody>
                    <tfoot class="pc-sp-items-tfoot">
                        <tr>
                            <td colspan="2" class="text-end">Total</td>
                            <td class="text-end" id="pc-sp-items-total">—</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="pc-sp-meta" style="margin-top:14px;">
                    <div class="pc-sp-meta-row"><span class="pc-sp-meta-k">Recorded by</span><span
                            class="pc-sp-meta-v" id="pc-det-by">—</span></div>
                </div>
            </div>
            <div class="pc-sp-tab-pane" id="pc-pane-comments">
                <div class="pc-sp-comment-box">
                    <span class="pc-sp-avatar" id="pc-comment-av">—</span>
                    <textarea placeholder="Add your comment here (coming soon)" disabled></textarea>
                </div>
            </div>
            <div class="pc-sp-tab-pane" id="pc-pane-history">
                <div id="pc-history-list"></div>
            </div>
        </div>
    </aside>

    <!-- Petty Cash Detail Modal -->
    <div class="zoho-modal" id="billDetailModal">
        <div class="zoho-modal-content">
            <div class="zoho-modal-header">
                <div class="zoho-modal-title">Petty Cash Details</div>
                <div class="zoho-modal-actions">
                    <button class="btn btn-primary edit-btn">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="zoho-btn zoho-btn-icon close-modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="zoho-modal-body">

                <!-- Left Info + Right Summary -->
                <div class="modal-main-layout">

                    <!-- LEFT CARD -->
                    <div class="info-card">

                        <div class="info-grid-2">

                            <div class="info-row">
                                <span class="info-label">REPORT ID</span>
                                <span class="info-value" id="pc-report-id">—</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">DATE</span>
                                <span class="info-value" id="pc-expense-date">—</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">VENDOR</span>
                                <span class="info-value" id="pc-vendor-name">—</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">ZONE</span>
                                <span class="info-value" id="pc-zone-name">—</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">COMPANY</span>
                                <span class="info-value" id="pc-company-name">—</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">BRANCH</span>
                                <span class="info-value" id="pc-branch-name">—</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">STATUS</span>
                                <span class="info-value" id="pc-status">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT CARD -->
                    <div class="amount-card">

                        <div class="amount-title">TOTAL AMOUNT</div>
                        <div class="amount-price" id="pc-side-total">₹0.00</div>

                        <div class="amount-pill" id="pc-reimbursable-label">
                            Non-Reimbursable
                        </div>
                    </div>
                </div>

                <!-- Expense Items -->
                <div class="zoho-section">
                    <div class="zoho-section-title">EXPENSE ITEMS</div>
                    <table class="zoho-items-table">
                        <thead>
                            <tr>
                                <th>CATEGORY</th>
                                <th>DESCRIPTION</th>
                                <th class="text-end">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody id="pc-items"></tbody>
                    </table>
                </div>

                <!-- Grand Total -->
                <div class="zoho-section">
                    <div class="zoho-totals-grid">
                        <div class="zoho-total-row zoho-total-amount">
                            <span>Total</span>
                            <span id="pc-grand-total">₹0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="zoho-section">
                    <div class="zoho-section-title">Reference</div>
                    <div class="zoho-notes-content" id="pc-notes">—</div>
                </div>

            </div>
        </div>
    </div>

    <div class="zoho-modal-overlay" id="modalOverlay"></div>

    <!-- ── Edit History Popup (admin only) ── -->
    @if (isset($limit_access) && $limit_access == 1)
        <div class="qdt-history-popup" id="editHistoryPopup">
            <div class="qdt-history-popup-inner">
                <div class="qdt-history-popup-header">
                    <div>
                        <i class="bi bi-clock-history me-2"></i>
                        <span id="historyPopupTitle">Edit History</span>
                    </div>
                    <button class="qdt-history-popup-close" id="closeHistoryPopup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="qdt-history-popup-body" id="historyPopupBody">
                    <!-- Rows injected by JS -->
                </div>
            </div>
        </div>
        <div class="qdt-history-overlay" id="historyOverlay"></div>
    @endif

    <!--import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Petty Cash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Step 1: Download the Excel Template</p>
                    <a href="{{ url('/download-pettycash-template') }}" class="btn btn-success mb-3">
                        Download Template
                    </a>
                    <p>Step 2: Upload the Filled File</p>
                    <!-- Custom Styled File Upload -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="btn btn-outline-primary position-relative mb-2">
                            <i class="bi bi-upload"></i> Upload File
                            <input type="file" name="file" class="d-none" id="importFileInput"
                                accept=".xlsx,.csv" required>
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
    <div class="modal fade" id="documentModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                    <h5 class="modal-title" id="exampleModalLabel" style="color: #ffffff;font-size: 12px;">
                        Document Management system</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        style="background-color: #ffffff;" aria-label="Close"></button>
                </div>
                <div class="row">
                    <div class="col-sm-3"><br>
                        <div class="btn-group-vertical w-100" id="image_pdfs"
                            style="
                                  margin-left: 11px;
                              ">
                            <button type="button" class="btn btn-primary">Tab 1</button>
                            <button type="button" class="btn btn-primary">Tab 2</button>
                            <button type="button" class="btn btn-primary">Tab 3</button>
                            <!-- More tabs if needed -->
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <embed id="pdfmain" src="" width="100%" height="600px" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->

    <!-- Place this near the end of <body> -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width:90%;">
            <div class="modal-content">
                <div class="modal-header position-relative">
                    <h5 class="modal-title">Petty Cash Preview</h5>

                    <!-- Custom fallback close button (always shown, at right) -->
                    <button type="button" class="modal-close-fallback" aria-label="Close preview"
                        style="position:absolute; right:1rem; top:0.6rem; font-size:1.4rem; background:none; border:0;">
                        &times;
                    </button>
                </div>

                <div class="modal-body p-0">
                    <iframe id="pdfFrame" src="" width="100%" height="600px"
                        style="border:none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div class="sm-modal-overlay" id="rejectOverlay"></div>

    <!-- Reject Modal -->
    <div class="sm-modal" id="rejectModalCustom">
        <div class="sm-modal-box">

            <!-- Header -->
            <div class="sm-modal-header">
                <div class="sm-modal-title">
                    <i class="bi bi-x-circle text-danger"></i> Reject Petty Cash
                </div>
                <button class="sm-modal-close close-reject">&times;</button>
            </div>

            <!-- Body -->
            <div class="sm-form-row">
                <div class="sm-form-group">
                    <label>Reject Reason <span style="color:red">*</span></label>

                    <textarea id="rejectReason" class="form-control" rows="3" placeholder="Enter reject reason..."></textarea>

                    <!-- validation -->
                    <div class="text-danger mt-1 d-none" id="rejectError">
                        Please enter reject reason
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="sm-modal-footer">
                <button class="sm-btn-cancel close-reject">Cancel</button>
                <button class="sm-btn-primary" id="confirmRejectCustom">
                    Reject
                </button>
            </div>

        </div>
    </div>

    {{-- </div>
    </div>

    </div> --}}

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('/assets/js/vendor/pettycash_search.js') }}"></script>
    @if ($errors->any())
        <script>
            $(document).ready(function() {
                $('#exampleModal').modal('show');
            });
        </script>
    @endif
    @if (session('warning'))
        <script>
            $(function() {
                toastr.warning(@json(session('warning')));
            });
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $(document).ready(function() {
                $('#per_page').on('change', function() {
                    $('#perPageForm').submit();
                });
            });
            $('#importFileInput').on('change', function() {
                const file = this.files[0];
                if (file) {
                    $('#fileNameDisplay').text('Selected file: ' + file.name);
                } else {
                    $('#fileNameDisplay').text('');
                }
            });
            let rejectId = null;
            var pcDetailUrl = @json(route('superadmin.getpettycashdetail'));
            var pcPanelOpenId = null;

            // OPEN
            $(document).on('click', '.qd-action-approve, .qd-action-reject', function(e) {
                e.preventDefault();

                const id = $(this).data('id');
                const value = $(this).data('value');

                console.log("clicked", id, value); // debug

                if (value === 'approved') {
                    updateStatus(id, value);
                }

                if (value === 'rejected') {
                    rejectId = id;

                    $('#rejectReason').val('');
                    $('#rejectError').addClass('d-none');

                    $('#rejectModalCustom').addClass('show');
                    $('#rejectOverlay').addClass('show');
                    $('body').css('overflow', 'hidden');
                }
            });

            // CLOSE
            $(document).on('click', '.close-reject, #rejectOverlay', function() {
                $('#rejectModalCustom').removeClass('show');
                $('#rejectOverlay').removeClass('show');
                $('body').css('overflow', 'auto');
            });

            $('#confirmRejectCustom').on('click', function() {

                const reason = $('#rejectReason').val().trim();

                if (!reason) {
                    $('#rejectError').removeClass('d-none');
                    $('#rejectReason').focus();
                    return;
                }

                updateStatus(rejectId, 'rejected', reason);

                // close modal
                $('#rejectModalCustom').removeClass('show');
                $('#rejectOverlay').removeClass('show');
                $('body').css('overflow', 'auto');
            });

            /* COMMON FUNCTION */
            function updateStatus(id, value, reason = '') {

                $.ajax({
                    url: '{{ route('superadmin.PettyCashApprover') }}',
                    method: "GET",
                    data: {
                        approver_id: id,
                        value: value,
                        reason: reason // 👈 important
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr);
                        toastr.error("Something went wrong");
                    }
                });
            }

            $(document).on('click', '.import-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Saving...');

                const fileInput = $('#importFileInput')[0];
                const file = fileInput.files[0];

                if (!file) {
                    toastr.error("Please select a file to upload.");
                    $btn.prop('disabled', false).html(originalText);
                    return;
                }

                let formData = new FormData();
                formData.append('file', file);

                $.ajax({
                    url: '{{ url('/import-pettycash') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success("File imported successfully!");
                        $('#importModal').modal('hide');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        console.error("Import failed:", xhr);
                        let msg = "Failed to import file.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors && xhr
                            .responseJSON.errors.file) {
                            msg = xhr.responseJSON.errors.file[0];
                        }
                        toastr.error(msg);
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Row click: Zoho-style detail slide-over (Details / Comments / History)
            $(document).on('click', '.customer-row', function(e) {
                if ($(e.target).is('input[type="checkbox"]')) {
                    return;
                }
                if ($(e.target).closest('.pc-row-action').length) {
                    return;
                }
                if ($(e.target).closest('.approver').length) {
                    return;
                }
                if ($(e.target).closest('.doc-row').length) return;
                if ($(e.target).closest('.vendor_link').length) return;
                if ($(e.target).closest('.print-pop-btn').length) return;

                const id = $(this).data('id');
                if (!id) return;
                openPcDetailPanel(id);
            });

            // Close modal handler for button and overlay
            $(document).on('click', '.close-modal, #modalOverlay', function(e) {
                e.stopPropagation();
                closeModal();
            });

            // Handle keyboard escape key to close modal
            $(document).on('keyup', function(e) {
                if (e.key === "Escape") {
                    if ($('#pc-sp-panel').hasClass('open')) {
                        closePcDetailPanel();
                        return;
                    }
                    closeModal();
                }
            });

            $(document).on('click', '#pc-sp-close, #pc-sp-overlay', function() {
                closePcDetailPanel();
            });

            $(document).on('click', '.pc-sp-tab', function() {
                const pane = $(this).data('pc-pane');
                $('.pc-sp-tab').removeClass('active');
                $(this).addClass('active');
                $('.pc-sp-tab-pane').removeClass('active');
                $('#pc-pane-' + pane).addClass('active');
            });

            // PDF button handler
            // $(document).on('click', '.pdf-btn', function () {
            //     const billNumber = $('#bill-number').text();
            //     alert(`Generating PDF for bill ${billNumber}`);
            //     // window.open(`/bills/pdf/${billNumber}`, '_blank');
            // });
            //   $(document).on('click', '.edit-btn', function () {
            //     const billId = $('#billDetailModal').data('bill-id');
            //     alert(billId);
            //     window.location.href = "{{ route('superadmin.getpettycashcreate') }}" + "?id=" + billId + "&type=edit";
            // });

            // $(document).on('click', '.edit-btn', function () {
            //     const billId = $('#billDetailModal').data('bill-id');
            //     window.location.href = "{{ route('superadmin.getpettycashcreate') }}" + "?id=" + billId + "&type=edit";
            // });
            $(document).on('click', '.edit-btn', function() {
                const billId = $('#billDetailModal').data('bill-id');
                window.location.href = "{{ route('superadmin.getpettycashcreate') }}" + "?id=" + billId;
            });
            $(document).on('click', '.pdf-btn', function() {
                const billId = $('#billDetailModal').data('bill-id');
                // Make AJAX request
                $.ajax({
                    url: '{{ route('superadmin.getquotationpdf') }}',
                    method: 'GET',
                    data: {
                        id: billId
                    },
                    xhrFields: {
                        responseType: 'blob' // Important for handling binary data
                    },
                    success: function(data) {
                        // Create a blob from the response
                        const blob = new Blob([data], {
                            type: 'application/pdf'
                        });
                        // Create a link element
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = `quotation_order_${billId}.pdf`;

                        // Trigger the download
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        // Remove the blob URL
                        window.URL.revokeObjectURL(link.href);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error generating PDF:', error);
                        alert('Error generating PDF. Please try again.');
                    }
                });
            });

            // // Edit button handler
            // $(document).on('click', '.edit-btn', function () {
            //     const billId = $('#billDetailModal').data('bill-id');
            //     window.location.href = "{{ route('superadmin.getquotationcreate') }}" + "?id=" + billId;
            // });

            $(document).on('click', '.print-btn', function() {
                const billId = $('#billDetailModal').data('bill-id');

                $.ajax({
                    url: '{{ route('superadmin.getquotationprint') }}',
                    method: "GET",
                    data: {
                        id: billId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var url = URL.createObjectURL(blob);

                        // Inject into iframe instead of opening new tab
                        $('#pdfFrame').attr('src', url);

                        // Show modal
                        $('#pdfPreviewModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr);
                        toastr.error("Error occurred while printing");
                    }
                });
            });

            //   $(document).on('click', '.print-btn', function () {
            //     const billId = $('#billDetailModal').data('bill-id');

            //     $.ajax({
            //         url: '{{ route('superadmin.getquotationprint') }}',
            //         method: "GET",
            //         data: { id: billId },
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         xhrFields: {
            //             responseType: 'blob' // This tells jQuery to handle binary data
            //         },
            //         success: function (response) {
            //           var blob = new Blob([response], {type: 'application/pdf'});
            //           var url = URL.createObjectURL(blob);

            //           // Inject into iframe instead of opening new tab
            //           $('#pdfFrame').attr('src', url);

            //           // Show modal
            //           $('#pdfPreviewModal').modal('show');
            //       },
            //         error: function (xhr) {
            //             console.error("Error:", xhr);
            //             toastr.error("Error occurred while printing");
            //         }
            //     });
            // });
            $(document).on('click', '.print-pop-btn', function() {
                const row = $(this).closest('tr');
                let billId = row.data('id');
                $.ajax({
                    url: '{{ route('superadmin.getquotationprint') }}',
                    method: "GET",
                    data: {
                        id: billId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    xhrFields: {
                        responseType: 'blob' // This tells jQuery to handle binary data
                    },
                    success: function(response) {
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var url = URL.createObjectURL(blob);

                        // Inject into iframe instead of opening new tab
                        $('#pdfFrame').attr('src', url);

                        // Show modal
                        $('#pdfPreviewModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr);
                        toastr.error("Error occurred while printing");
                    }
                });
            });
            // jQuery/vanilla fallback: will hide modal for Bootstrap 4, Bootstrap 5, or just remove backdrop if needed
            $(document).on('click', '.modal-close-fallback', function(e) {
                e.preventDefault();

                // Bootstrap 5: try to get or create the Modal instance and hide it
                try {
                    if (typeof bootstrap !== 'undefined') {
                        var modalEl = document.getElementById('pdfPreviewModal');
                        var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        bsModal.hide();
                        return;
                    }
                } catch (err) {
                    /* ignore */
                }

                // Bootstrap 4 (jQuery)
                if ($.fn && $.fn.modal) {
                    $('#pdfPreviewModal').modal('hide');
                    return;
                }

                // Ultimate fallback: hide and remove backdrop
                $('#pdfPreviewModal').hide();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });


            // Function to close modal
            function closeModal() {
                $('#billDetailModal').removeClass('show');
                $('#modalOverlay').removeClass('show');
                $('body').css('overflow', 'auto');
                $('.edit-btn').show();
            }

            // Function to populate modal with data
            function populateModal(data) {
                // console.log(data);
                $('#billDetailModal').data('bill-id', data.id);

                let status = (data.status || '').toLowerCase();
                let statusBadge = '—';

                if (status === 'approved') {
                    statusBadge = '<span class="qd-badge qd-badge-pc qd-badge-pc-approved">Approved</span>';
                } else if (status === 'pending') {
                    statusBadge = '<span class="qd-badge qd-badge-pc qd-badge-pc-pending">Pending</span>';
                } else if (status === 'rejected') {
                    statusBadge = '<span class="qd-badge qd-badge-pc qd-badge-pc-rejected">Rejected</span>';
                } else if (status === 'draft') {
                    statusBadge = '<span class="qd-badge qd-badge-pc qd-badge-pc-draft">Draft</span>';
                } else if (status === 'reimbursed') {
                    statusBadge = '<span class="qd-badge qd-badge-pc qd-badge-reimbursed">Reimbursed</span>';
                } else {
                    statusBadge = '<span class="qd-badge qd-badge-pc qd-badge-default">' + (data.status || '—')
                        .toString().toUpperCase() + '</span>';
                }

                let reimbursable = (data.pettycash && data.pettycash.claim_reimbursement == 1);

                let reimbursableBadge = reimbursable ?
                    '<span class="reimb-badge yes">Reimbursable</span>' :
                    '<span class="reimb-badge no">Non-Reimbursable</span>';

                $('.zoho-modal-title').text(
                    (data.report_name || data.report_code || ('#' + data.id))
                );
console.log(data.reference_no);
                $('#pc-report-name').text(data.report_name || '—');
                $('#pc-report-id').text(data.report_code || '—');
                $('#pc-expense-date').text(data.expense_date || '—');
                $('#pc-vendor-name').text(data.vendor_name || '—');
                $('#pc-zone-name').text(data.zone_name || '—');
                $('#pc-company-name').text(data.company_name || '—');
                $('#pc-branch-name').text(data.branch_name || '—');
                $('#pc-status').html(statusBadge);
                $('#pc-total').text(formatCurrency(data.total_amount));
                $('#pc-side-total').text(formatCurrency(data.total_amount));
                $('#pc-grand-total').text(formatCurrency(data.total_amount));
                $('#pc-notes').text(
    (data.pettycash && data.pettycash.reference_no) || '—'
);
                $('#pc-reimbursable').html(reimbursableBadge);
                if (reimbursable) {
                    $('#pc-reimbursable-label')
                        .text('Reimbursable')
                        .removeClass('non')
                        .addClass('yes');
                } else {
                    $('#pc-reimbursable-label')
                        .text('Non-Reimbursable')
                        .removeClass('yes')
                        .addClass('non');
                }

                // Items — fetch via AJAX since items aren't on the row
                $('#pc-items').html(
                    '<tr><td colspan="3" class="text-center text-muted" style="padding:12px;">Loading...</td></tr>'
                );
                $.ajax({
                    url: '{{ route('superadmin.getpettycash') }}',
                    type: 'GET',
                    data: {
                        id: data.id,
                        items_only: 1
                    },
                    success: function(res) {
                        console.log(res);
                        if (res.items && res.items.length) {
                            const rows = res.items.map(item =>
                                `<tr>
                                        <td>${item.category_name || '—'}</td>
                                        <td>${item.description || '—'}</td>
                                        <td class="text-end">${formatCurrency(item.amount)}</td>
                                    </tr>`
                            ).join('');
                            $('#pc-items').html(rows);
                        } else {
                            $('#pc-items').html(
                                '<tr><td colspan="3" class="text-center text-muted" style="padding:12px;">No items found</td></tr>'
                            );
                        }
                    },
                    error: function() {
                        $('#pc-items').html(
                            '<tr><td colspan="3" class="text-center text-muted" style="padding:12px;">—</td></tr>'
                        );
                    }
                });
            }

            // Currency formatter
            function formatCurrency(amount) {
                if (!amount) return '₹0.00';
                const num = typeof amount === 'string' ? parseFloat(amount.replace(/,/g, '')) : amount;
                return '₹' + num.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function formatPcAmountPlain(amount) {
                const num = typeof amount === 'string' ? parseFloat(String(amount).replace(/,/g, '')) : amount;
                const n = (num !== null && !isNaN(num)) ? num : 0;
                return n.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function closePcDetailPanel() {
                $('#pc-sp-overlay, #pc-sp-panel').removeClass('open');
                $('.customer-row').removeClass('pc-sp-row-active');
                $('body').css('overflow', 'auto');
                pcPanelOpenId = null;
            }

            function formatPcHistAt(at) {
                if (!at) return '';
                try {
                    return moment(at).format('DD/MM/YYYY hh:mm A');
                } catch (err) {
                    return String(at);
                }
            }

            function pcStatusBadgeHtml(status) {
                const s = (status || '').toLowerCase();
                if (s === 'approved') {
                    return '<span class="qd-badge qd-badge-pc qd-badge-pc-approved">Approved</span>';
                }
                if (s === 'pending') {
                    return '<span class="qd-badge qd-badge-pc qd-badge-pc-pending">Pending</span>';
                }
                if (s === 'rejected') {
                    return '<span class="qd-badge qd-badge-pc qd-badge-pc-rejected">Rejected</span>';
                }
                if (s === 'draft') {
                    return '<span class="qd-badge qd-badge-pc qd-badge-pc-draft">Draft</span>';
                }
                if (s === 'reimbursed') {
                    return '<span class="qd-badge qd-badge-pc qd-badge-reimbursed">Reimbursed</span>';
                }
                return '<span class="qd-badge qd-badge-pc qd-badge-default">' + (status || '—').toString()
                    .toUpperCase() + '</span>';
            }

            function renderPcHistory(list) {
                const $w = $('#pc-history-list').empty();
                if (!list || !list.length) {
                    $w.append(
                        $('<p style="color:#9ca3af;font-size:13px;padding:4px 0 8px;"/>').text(
                            'No history yet.')
                    );
                    return;
                }
                list.forEach(function(h) {
                    const initials = (h.actor_initials || '?').toString();
                    const name = (h.actor_name || 'System').toString();
                    const $row = $('<div class="pc-sp-history-item"/>');
                    $row.append($('<div class="pc-sp-hist-av"/>').text(initials));
                    const $body = $('<div style="flex:1;min-width:0;"/>');
                    $body.append($('<div class="pc-sp-hist-meta"/>').text(name + ' · ' + formatPcHistAt(h
                        .at)));
                    $body.append($('<div class="pc-sp-hist-msg"/>').text(h.message || ''));
                    $row.append($body);
                    $w.append($row);
                });
            }

            function fillPcDetailPanel(d) {
                const e = d.expense || {};
                pcPanelOpenId = e.id;
                $('#pc-sp-title').text(e.title || 'Expense');
                $('#pc-sp-subtitle').text([e.zone_name, e.company_name].filter(Boolean).join(' · ') || '');
                $('#pc-sp-edit').attr('href', d.edit_url || '#').toggle(!e.readonly);

                if (d.linked_report && d.linked_report.id) {
                    $('#pc-sp-associated').show();
                    const t = (d.linked_report.report_name || d.linked_report.report_id || 'Report').toString();
                    $('#pc-sp-report-link').text(t).attr('href', d.linked_report.url || '#')
                        .attr('title', 'Open this report in a new tab');
                } else {
                    $('#pc-sp-associated').hide();
                }

                if (e.receipt_url) {
                    $('#pc-sp-receipt-img').attr('src', e.receipt_url).show();
                    $('#pc-sp-receipt-placeholder').hide();
                } else {
                    $('#pc-sp-receipt-img').hide().attr('src', '');
                    $('#pc-sp-receipt-placeholder').show();
                }

                const pref = (e.currency_prefix != null && e.currency_prefix !== '') ? e.currency_prefix : '₹';
                $('#pc-sp-reimb-tag').text(e.claim_reimbursement ? 'REIMBURSABLE' : 'NON-REIMBURSABLE');
                $('#pc-sp-amount-big').text(pref + formatPcAmountPlain(e.total_amount));
                $('#pc-sp-meta-date').text(e.expense_date_fmt || '—');
                $('#pc-sp-meta-cat').text(e.expense_type === 'itemized' ? 'Itemized' : (e.category_name || '—'));
                $('#pc-sp-meta-merchant').text(e.vendor_name && e.vendor_name !== '-' ? e.vendor_name : '—');
                $('#pc-sp-meta-status').html(pcStatusBadgeHtml(e.status));
                $('#pc-det-desc').text(e.description_display || '—');
                $('#pc-det-policy').text(e.company_name || '—');
                $('#pc-det-inv').text(e.reference_no || '—');

                const $tb = $('#pc-sp-items-body').empty();
                const items = d.items || [];
                if (items.length) {
                    items.forEach(function(it) {
                        const $tr = $('<tr/>');
                        $tr.append($('<td/>').text(it.category_name || '—'));
                        $tr.append($('<td/>').text(it.description || '—'));
                        $tr.append($('<td class="text-end"/>').text(pref + formatPcAmountPlain(it.amount)));
                        $tb.append($tr);
                    });
                } else {
                    $tb.append($('<tr><td colspan="3" class="text-center text-muted" style="padding:12px;">No line items</td></tr>'));
                }
                $('#pc-sp-items-total').text(pref + formatPcAmountPlain(e.total_amount));

                const by = (d.recorded_by_name || '—').toString();
                const ini = (d.recorded_by_initials || '—').toString().substring(0, 2);
                $('#pc-det-by').empty()
                    .append($('<span class="pc-sp-avatar"/>').text(ini))
                    .append(document.createTextNode(' ' + by));
                $('#pc-comment-av').text(ini);

                renderPcHistory(d.history || []);
            }

            function openPcDetailPanel(id) {
                if (!id) return;
                pcPanelOpenId = id;
                $('.customer-row').removeClass('pc-sp-row-active');
                $('.customer-row[data-id="' + id + '"]').addClass('pc-sp-row-active');
                $('#pc-sp-overlay, #pc-sp-panel').addClass('open');
                $('body').css('overflow', 'hidden');
                $('.pc-sp-tab').removeClass('active').filter('[data-pc-pane="details"]').addClass('active');
                $('.pc-sp-tab-pane').removeClass('active');
                $('#pc-pane-details').addClass('active');
                $('#pc-sp-title').text('Loading…');
                $('#pc-sp-edit').hide();
                $.getJSON(pcDetailUrl, {
                    petty_cash_id: id
                }, function(r) {
                    if (!r.success) {
                        toastr.error(r.message || 'Could not load expense.');
                        closePcDetailPanel();
                        return;
                    }
                    fillPcDetailPanel(r);
                }).fail(function() {
                    toastr.error('Could not load expense.');
                    closePcDetailPanel();
                });
            }

            const TblZonesModel = @json($TblZonesModel);
            const Tblcompany = @json($Tblcompany);
            const Tblvendor = @json($Tblvendor);

            TblZonesModel.forEach(zone => {
                const item = $(`
        <div data-id="${zone.id}">${zone.name}</div>
    `);
                $('.zone-list').append(item);
            });
            // (Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data || [])).forEach(locations => {
            //     const item = $(`
        //               <div data-id="${locations.id}">${locations.name} </div>
        //             `);
            //     $('.zone-list').append(item);
            // });
            Tblcompany.forEach(Tblcompany => {
                const item = $(`
                            <div data-value="${Tblcompany.company_name}" data-id="${Tblcompany.id}">${Tblcompany.company_name}</div>
                        `);
                $('.company-list').append(item);
            });
            Tblvendor.forEach(Tblvendor => {
                const item = $(`
                            <div data-value="${Tblvendor.display_name}" data-id="${Tblvendor.id}">${Tblvendor.display_name}</div>
                        `);
                $('.vendor-list').append(item);
            });

            $(document).ready(function() {

                // =================== OPEN DROPDOWN ===================
                $(document).on('click', '.dropdown-search-input', function(e) {
                    e.stopPropagation();
                    $('.dropdown-menu.tax-dropdown').hide(); // close others

                    const $input = $(this);
                    let $dropdown = $input.data('dropdown');

                    // Clone dropdown only once per input
                    if (!$dropdown) {
                        $dropdown = $input.siblings('.dropdown-menu').clone(true);
                        $('body').append($dropdown);
                        $input.data('dropdown', $dropdown);
                    }

                    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));

                    const offset = $input.offset();
                    $dropdown.css({
                        position: 'absolute',
                        top: offset.top + $input.outerHeight(),
                        left: offset.left,
                        width: $input.outerWidth(),
                        zIndex: 999
                    }).show();

                    $dropdown.find('.inner-search').focus();
                });

                // =================== FILTER SEARCH ===================
                $(document).on('keyup', '.inner-search', function() {
                    const searchVal = $(this).val().toLowerCase();
                    $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function() {
                        const text = $(this).text().toLowerCase();
                        $(this).toggle(text.indexOf(searchVal) > -1);
                    });
                });

                // =================== MULTISELECT (Individual Item) ===================
                $(document).on('click', '.dropdown-list.multiselect div', function(e) {
                    e.stopPropagation();
                    $(this).toggleClass('selected');
                    const $dropdown = $(this).closest('.tax-dropdown');
                    updateMultiSelection($dropdown);
                });

                // =================== SELECT ALL ===================
                $(document).on('click', '.select-all', function(e) {
                    e.stopPropagation();
                    const $dropdown = $(this).closest('.tax-dropdown');
                    $dropdown.find('.dropdown-list.multiselect div').addClass('selected');
                    updateMultiSelection($dropdown);
                });

                // =================== DESELECT ALL ===================
                $(document).on('click', '.deselect-all', function(e) {
                    e.stopPropagation();
                    const $dropdown = $(this).closest('.tax-dropdown');
                    $dropdown.find('.dropdown-list.multiselect div').removeClass('selected');
                    updateMultiSelection($dropdown);
                });

                // =================== CLOSE ON OUTSIDE CLICK ===================
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.tax-dropdown-wrapper').length && !$(e.target)
                        .closest('.tax-dropdown').length) {
                        $('.dropdown-menu.tax-dropdown').hide();
                    }
                });

                // =================== UPDATE MULTISELECT VALUES ===================
                function updateMultiSelection($dropdown) {
                    const wrapper = $dropdown.data('wrapper');
                    if (!wrapper) return;

                    const selectedItems = [];
                    const selectedIds = [];

                    $dropdown.find('.dropdown-list.multiselect div.selected').each(function() {
                        selectedItems.push($(this).text().trim());
                        selectedIds.push($(this).data('id'));
                    });

                    const $visibleInput = wrapper.find('.dropdown-search-input');
                    const $hiddenInput = wrapper.find('input[type="hidden"]');

                    $visibleInput.val(selectedItems.join(', '));
                    $hiddenInput.val(selectedIds.join(','));

                    $hiddenInput.trigger('click');
                }
            });

            $('.zone_id').on('click', function() {
                var id = $('.zone_id').val();
                let formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    url: '{{ route('superadmin.getbranchfetch') }}',
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        // toastr.success(response.message);
                        if (response.branch !== "") {
                            $('.branch-list div').remove();
                            response.branch.forEach(branch => {
                                const item = $(`
                      <div data-id="${branch.id}">${branch.name} </div>
                    `);
                                $('.branch-list').append(item);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error("Error saving form:", xhr);
                        toastr.error("Something went wrong.");
                    }
                });
            });
            $('.zone-list div').on('click', function() {
                $('.branch-search-input').val('');
                $('.branch_id').val('');
            })
            $('.zone-inner-search').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                const list = $(this).closest('.dropdown-menu').find('.zone-list div');

                list.each(function() {
                    const itemText = $(this).text().toLowerCase();
                    $(this).toggle(itemText.includes(searchText));
                });
            });
            $('.branch-inner-search').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                const list = $(this).closest('.dropdown-menu').find('.branch-list div');

                list.each(function() {
                    const itemText = $(this).text().toLowerCase();
                    $(this).toggle(itemText.includes(searchText));
                });
            });
            $('.company-inner-search').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                const list = $(this).closest('.dropdown-menu').find('.company-list div');

                list.each(function() {
                    const itemText = $(this).text().toLowerCase();
                    $(this).toggle(itemText.includes(searchText));
                });
            });
            $('.vendor-inner-search').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                const list = $(this).closest('.dropdown-menu').find('.vendor-list div');

                list.each(function() {
                    const itemText = $(this).text().toLowerCase();
                    $(this).toggle(itemText.includes(searchText));
                });
            });
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.tax-dropdown-wrapper').length) {
                    $('.dropdown-menu.tax-dropdown').hide();
                }
            });
            $(document).on('click', '.dropdown-menu.tax-dropdown', function(e) {
                e.stopPropagation();
            });

            $(document).ready(function() {
                let savedFilters = null;
                if (sessionStorage.getItem("restore_filters")) {
                    try {
                        savedFilters = JSON.parse(sessionStorage.getItem("quotation_filters"));
                    } catch (e) {
                        savedFilters = null;
                    }
                    sessionStorage.removeItem("restore_filters");
                }

                let filters = savedFilters || {
                    date_from: '',
                    date_to: '',
                    zone_name: '',
                    zone_id: '',
                    branch_name: '',
                    branch_id: '',
                    company_id: '',
                    company_name: '',
                    vendor_name: '',
                    vendor_id: '',
                    status_name: '',
                    status_id: '',
                    state_id: '',
                    state_name: '',
                    universal_search: '',
                    expense_report_id: '',
                };

                if (savedFilters) {
                    filters = savedFilters;
                }

                function buildExportUrl(format) {
                    var base = '{{ route('superadmin.pettycash.export') }}';

                    var params = {
                        format: format,
                        date_from: filters.date_from || '',
                        date_to: filters.date_to || '',
                        zone_id: filters.zone_id || '',
                        branch_id: filters.branch_id || '',
                        company_id: filters.company_id || '',
                        vendor_id: filters.vendor_id || '',
                        status_name: filters.status_name || '',
                        universal_search: filters.universal_search || '',
                        expense_report_id: filters.expense_report_id || '',
                    };

                    return base + '?' + $.param(params);
                }

                $('#exportXlsx').on('click', function(e) {
                    e.preventDefault();
                    window.location.href = buildExportUrl('xlsx');
                });

                $('#exportCsv').on('click', function(e) {
                    e.preventDefault();
                    window.location.href = buildExportUrl('csv');
                });
                (function() {
                    try {
                        var p = new URLSearchParams(window.location.search);
                        var er = p.get('expense_report_id');
                        if (er && String(er).match(/^\d+$/)) {
                            filters.expense_report_id = er;
                        }
                    } catch (e) {
                        /* ignore */
                    }
                })();

                if (!savedFilters) {
                    loadPettyCash(
                        sessionStorage.getItem('pettycash_page') || 1
                    );
                }

                function renderSummary() {
                    let summaryHtml = '';

                    if (filters.date_from && filters.date_to) {
                        summaryHtml += `<span class="filter-badge remove-icon" data-type="date">
                    ${filters.date_from} → ${filters.date_to}
                </span>`;
                    }

                    if (filters.zone_id) {
                        summaryHtml += `<span class="filter-badge remove-icon" data-type="zone">
                    ${filters.zone_name}
                </span>`;
                    }
                    if (filters.branch_id) {
                        summaryHtml += `<span class="filter-badge remove-icon" data-type="branch">
                    ${filters.branch_name}
                </span>`;
                    }
                    if (filters.company_id) {
                        summaryHtml += `<span class="filter-badge remove-icon" data-type="company">
                    ${filters.company_name}
                </span>`;
                    }
                    if (filters.vendor_id) {
                        summaryHtml += `<span class="filter-badge remove-icon" data-type="vendor">
                    ${filters.vendor_name}
                </span>`;
                    }
                    if (filters.nature_id) {
                        summaryHtml += `<span class="filter-badge remove-icon" data-type="nature">
                    ${filters.nature_name}
                </span>`;
                    }
                    if (filters.status_id) {
                        summaryHtml += `<span class="filter-badge remove-icon" data-type="status">
                    ${filters.status_name}
                </span>`;
                    }

                    if (filters.state_id) {
                        summaryHtml += `<span class="filter-badge remove-icon" data-type="state">
                    ${filters.state_name}
                </span>`;
                    }
                    if (filters.expense_report_id) {
                        summaryHtml += `<span class="filter-badge remove-icon" data-type="expense_report">
                    Report #${filters.expense_report_id}
                </span>`;
                    }
                    if (summaryHtml) {
                        summaryHtml += `<span class="filter-badge filter-clear" id="clear-all">
                    Clear all
                </span>`;
                    }
                    $("#filter-summary").html(summaryHtml || "");
                }

                function loadPettyCash(page = 1, perPage = $('#per_page').val()) {
                    $.ajax({
                        url: '{{ route('superadmin.getpettycash') }}',
                        type: "GET",
                        data: {
                            per_page: perPage,
                            page: page,
                            date_from: filters.date_from,
                            date_to: filters.date_to,
                            zone_id: filters.zone_id,
                            branch_id: filters.branch_id,
                            company_id: filters.company_id,
                            vendor_id: filters.vendor_id,
                            nature_id: filters.nature_id,
                            status_name: filters.status_name,
                            state_name: filters.state_name,
                            universal_search: filters.universal_search,
                            expense_report_id: filters.expense_report_id || undefined
                        },
                        success: function(data) {
                            if (data && typeof data === 'object' && data.html !== undefined) {
                                var pag =
                                    data.pagination ?
                                    '<div class="qd-pagination d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">' +
                                    data.pagination + '</div>' :
                                    '';
                                $('#pettycash-body').html(data.html + pag);
                                if (data.stats) {
                                    updateStatCards(data.stats);
                                }
                            } else {
                                $('#pettycash-body').html(data);
                            }
                            renderSummary();
                            var wrap = $('.qdt-wrap')[0];
                            if (wrap) {
                                wrap.offsetHeight;
                            }
                        }
                    });
                }

                // ── Date range picker (required for filters.date_from / date_to + AJAX reload) ──
                if (typeof $.fn.daterangepicker !== 'undefined') {
                    $('#reportrange').daterangepicker({
                        autoUpdateInput: false,
                        locale: {
                            cancelLabel: 'Clear',
                            format: 'DD/MM/YYYY'
                        }
                    }, function(start, end) {
                        filters.date_from = start.format('DD/MM/YYYY');
                        filters.date_to = end.format('DD/MM/YYYY');
                        $('#data_values').text(start.format('DD/MM/YYYY') + ' – ' + end.format('DD/MM/YYYY'));
                        $('.data_values').val(start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY'));
                        sessionStorage.setItem('pettycash_page', 1);
                        loadPettyCash(1);
                        renderSummary();
                    });
                    $('#reportrange').on('cancel.daterangepicker', function() {
                        filters.date_from = '';
                        filters.date_to = '';
                        $('#data_values').text('All Dates');
                        $('.data_values').val('');
                        sessionStorage.setItem('pettycash_page', 1);
                        loadPettyCash(1);
                        renderSummary();
                    });
                }

                // Saved filters
                if (savedFilters) {
                    window.restoringFilters = true;

                    filters = savedFilters;

                    $('.vendor-search-input').val(filters.vendor_name);
                    $('.company-search-input').val(filters.company_name);
                    $('.zone-search-input').val(filters.zone_name);
                    $('.branch-search-input').val(filters.branch_name);
                    $('.nature-search-input').val(filters.nature_name);
                    $('.status-search-input').val(filters.status_name);
                    $('.state-search-input').val(filters.state_name);
                    $('.universal_search').val(filters.universal_search);

                    if (filters.date_from && filters.date_to) {
                        $('#data_values').text(filters.date_from + ' - ' + filters.date_to);
                        $('.data_values').val(filters.date_from + ' to ' + filters.date_to);

                        let start = moment(filters.date_from, "DD/MM/YYYY");
                        let end = moment(filters.date_to, "DD/MM/YYYY");

                        if ($('#reportrange').data('daterangepicker')) {
                            let picker = $('#reportrange').data('daterangepicker');

                            picker.setStartDate(start);
                            picker.setEndDate(end);
                        }
                    }

                    const savedPage = sessionStorage.getItem('pettycash_page') || 1;

                    setTimeout(function() {
                        loadPettyCash(savedPage, $('#per_page').val());
                    }, 200);
                }


                function updateStatCards(s) {
                    var fmt = function(n) {
                        n = parseFloat(n) || 0;
                        return '₹' + n.toLocaleString('en-IN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    };
                    // values
                    $('.qd-stat-blue  .qd-stat-value').text(s.total);
                    $('.qd-stat-green .qd-stat-value').text(s.approved);
                    $('.qd-stat-orange .qd-stat-value').text(s.pending);
                    $('.qd-stat-red   .qd-stat-value').text(s.rejected);
                    $('.qd-stat-purple .qd-stat-value').text(s.draft);
                    // amounts
                    $('.qd-stat-blue  .qd-stat-sub').text(fmt(s.total_amount));
                    $('.qd-stat-green .qd-stat-sub').text(fmt(s.approved_amount));
                    $('.qd-stat-orange .qd-stat-sub').text(fmt(s.pending_amount));
                    $('.qd-stat-red   .qd-stat-sub').text(fmt(s.rejected_amount));
                    $('.qd-stat-purple .qd-stat-sub').text(fmt(s.draft_amount));
                }

                // =================== EDIT HISTORY POPUP ===================
                @if (isset($limit_access) && $limit_access == 1)
                    $(document).on('click', '.qdt-history-btn', function(e) {
                        e.stopPropagation();
                        var history = JSON.parse($(this).attr('data-history') || '[]');
                        var qno = $(this).attr('data-qno') || 'Quotation';

                        $('#historyPopupTitle').text('Edit History — ' + qno);

                        var html = '';
                        if (history.length === 0) {
                            html = '<div class="qdt-history-empty">No edits recorded yet.</div>';
                        } else {
                            // newest first
                            history.slice().reverse().forEach(function(entry, idx) {
                                console.log(entry, 'entry');

                                html += '<div class="qdt-history-entry">' +
                                    '<div class="qdt-history-meta">' +
                                    '<span class="qdt-history-idx">' + (history.length -
                                        idx) + '</span>' +
                                    '<span class="qdt-history-by"><i class="bi bi-person-fill me-1"></i>' +
                                    (entry.edited_by || '—') + '</span>' +
                                    '<span class="qdt-history-role ' + (entry.role ===
                                        'Admin' ? 'qdt-role-admin' : 'qdt-role-user') +
                                    '">' + (entry.role || '') + '</span>' +
                                    '</div>' +
                                    '<div class="qdt-history-detail">' +
                                    '<span class="qdt-history-at"><i class="bi bi-calendar3 me-1"></i>' +
                                    (entry.edited_at || '—') + '</span>' +
                                    '<span class="qdt-history-status">Status: <strong>' + (
                                        entry.status || '—') + '</strong></span>' +
                                    (entry.amount ? '<span class="qdt-history-amt">' + entry
                                        .amount.toLocaleString('en-IN', {
                                            minimumFractionDigits: 2
                                        }) + '</span>' : '') +
                                    '</div>' +
                                    '</div>';
                            });
                        }

                        $('#historyPopupBody').html(html);
                        $('#editHistoryPopup, #historyOverlay').addClass('active');
                    });

                    $(document).on('click', '#closeHistoryPopup, #historyOverlay', function() {
                        $('#editHistoryPopup, #historyOverlay').removeClass('active');
                    });
                @endif

                // =================== TOGGLE: STATS & FILTERS ===================
                (function() {
                    var statsVisible = true;
                    var filtersVisible = true;

                    $('#toggleStats').on('click', function() {
                        var $btn = $(this);
                        var $chev = $('#statsChevron');
                        if (statsVisible) {
                            $('.qd-stats').addClass('qd-section-hidden');
                            $btn.addClass('qd-toggle-active');
                            $chev.addClass('rotated');
                        } else {
                            $('.qd-stats').removeClass('qd-section-hidden');
                            $btn.removeClass('qd-toggle-active');
                            $chev.removeClass('rotated');
                        }
                        statsVisible = !statsVisible;
                    });

                    $('#toggleFilters').on('click', function() {
                        var $btn = $(this);
                        var $chev = $('#filtersChevron');
                        if (filtersVisible) {
                            $('.qd-filters, .qd-search-row').addClass('qd-section-hidden');
                            $btn.addClass('qd-toggle-active');
                            $chev.addClass('rotated');
                        } else {
                            $('.qd-filters, .qd-search-row').removeClass('qd-section-hidden');
                            $btn.removeClass('qd-toggle-active');
                            $chev.removeClass('rotated');
                        }
                        filtersVisible = !filtersVisible;
                    });
                })();

                // =================== MULTI-SELECT CHANGE LISTENER ===================
                function setupMultiSelect(selectorInput, selectorHidden) {
                    var ns = 'click.ms' + selectorHidden.replace(/[^a-zA-Z0-9]/g, '');
                    $(document).off(ns, selectorHidden).on(ns, selectorHidden, function() {
                        const selectedIds = $(this).val();
                        const selectedText = $(selectorInput).val();
                        if (selectorHidden === '.zone_id') {
                            filters.zone_id = selectedIds;
                            filters.zone_name = selectedText;
                        } else if (selectorHidden === '.branch_id') {
                            filters.branch_id = selectedIds;
                            filters.branch_name = selectedText;
                        } else if (selectorHidden === '.company_id') {
                            filters.company_id = selectedIds;
                            filters.company_name = selectedText;
                        } else if (selectorHidden === '.vendor_id') {
                            filters.vendor_id = selectedIds;
                            filters.vendor_name = selectedText;
                        } else if (selectorHidden === '.nature_id') {
                            filters.nature_id = selectedIds;
                            filters.nature_name = selectedText;
                        } else if (selectorHidden === '.status_id') {
                            filters.status_id = selectedIds;
                            filters.status_name = selectedText;
                        } else if (selectorHidden === '.state_id') {
                            filters.state_id = selectedIds;
                            filters.state_name = selectedText;
                        }
                        sessionStorage.setItem('pettycash_page', 1);
                        loadPettyCash(1);
                    });
                }
                setupMultiSelect('.zone-search-input', '.zone_id');
                setupMultiSelect('.branch-search-input', '.branch_id');
                setupMultiSelect('.company-search-input', '.company_id');
                setupMultiSelect('.vendor-search-input', '.vendor_id');
                setupMultiSelect('.nature-search-input', '.nature_id');
                setupMultiSelect('.status-search-input', '.status_id');
                setupMultiSelect('.state-search-input', '.state_id');
                var pettySearchTimer = null;
                $('.universal_search').on('keyup', function() {
                    clearTimeout(pettySearchTimer);
                    var q = $(this).val();
                    pettySearchTimer = setTimeout(function() {
                        filters.universal_search = q;
                        loadPettyCash(1);
                    }, 350);
                });


                $('.data_values').on('change', function() {
                    var dateRange = ($(this).val() || '').trim();
                    if (!dateRange || dateRange.indexOf(' to ') === -1) {
                        filters.date_from = '';
                        filters.date_to = '';
                    } else {
                        var parts = dateRange.split(' to ');
                        filters.date_from = (parts[0] || '').trim();
                        filters.date_to = (parts[1] || '').trim();
                    }
                    sessionStorage.setItem('pettycash_page', 1);
                    loadPettyCash(1);
                });

                // Remove single filter
                $("#filter-summary").on('click', '.remove-icon', function() {
                    let type = $(this).data('type');

                    if (type === 'date') {
                        filters.date_from = '';
                        filters.date_to = '';
                        $('.data_values').val('');
                        $('#data_values').text('All Dates');
                    } else if (type === 'zone') {
                        filters.zone_id = '';
                        filters.zone_name = '';
                        $('.zone_id').val('');
                        $('.zone-search-input').val('');
                        $('.zone-list div').removeClass('selected');
                    } else if (type === 'branch') {
                        filters.branch_id = '';
                        filters.branch_name = '';
                        $('.branch_id').val('');
                        $('.branch-search-input').val('');
                        $('.branch-list div').removeClass('selected');
                    } else if (type === 'company') {
                        filters.company_id = '';
                        filters.company_name = '';
                        $('.company_id').val('');
                        $('.company-search-input').val('');
                        $('.company-list div').removeClass('selected');
                    } else if (type === 'vendor') {
                        filters.vendor_id = '';
                        filters.vendor_name = '';
                        $('.vendor_id').val('');
                        $('.vendor-search-input').val('');
                        $('.vendor-list div').removeClass('selected');
                    } else if (type === 'nature') {
                        filters.nature_id = '';
                        filters.nature_name = '';
                        $('.nature_id').val('');
                        $('.nature-search-input').val('');
                        $('.account-list div').removeClass('selected');
                    } else if (type === 'status') {
                        filters.status_id = '';
                        filters.status_name = '';
                        $('.status_id').val('');
                        $('.status-search-input').val('');
                        $('.status-list div').removeClass('selected');
                    } else if (type === 'state') {
                        filters.state_id = '';
                        filters.state_name = '';
                        $('.state_id').val('');
                        $('.state-search-input').val('');
                        $('.state-list div').removeClass('selected');
                    } else if (type === 'expense_report') {
                        filters.expense_report_id = '';
                        try {
                            var u = new URL(window.location.href);
                            u.searchParams.delete('expense_report_id');
                            window.history.replaceState({}, '', u.pathname + (u.search ? u.search :
                                '') + (u.hash || ''));
                        } catch (e) {
                            /* ignore */
                        }
                    }
                    sessionStorage.setItem('pettycash_page', 1);
                    loadPettyCash(1);
                });

                // Clear all filters
                $("#filter-summary").on('click', '#clear-all', function() {
                    filters = {
                        date_from: '',
                        date_to: '',
                        zone_name: '',
                        zone_id: '',
                        branch_name: '',
                        branch_id: '',
                        company_id: '',
                        company_name: '',
                        vendor_name: '',
                        vendor_id: '',
                        nature_name: '',
                        nature_id: '',
                        status_name: '',
                        status_id: '',
                        state_id: '',
                        state_name: '',
                        universal_search: '',
                        expense_report_id: '',
                    };
                    try {
                        var u = new URL(window.location.href);
                        u.searchParams.delete('expense_report_id');
                        window.history.replaceState({}, '', u.pathname + (u.search ? u.search :
                            '') + (u.hash || ''));
                    } catch (e) {
                        /* ignore */
                    }
                    $('.zone-search-input, .branch-search-input, .company-search-input, .vendor-search-input,.nature-search-input,.status-search-input,.state-search-input')
                        .val('');
                    $('.zone_id, .branch_id, .company_id, .vendor_id,.nature_id,.status_id,.state_id')
                        .val('');
                    $('.data_values').val('');
                    $('.dropdown-list div').removeClass('selected');
                    $('#data_values').text('All Dates');
                    $('.qd-stat-card').removeClass('qd-stat-active');
                    $('.qd-stat-card[data-stat-filter=""]').addClass('qd-stat-active');
                    sessionStorage.setItem('pettycash_page', 1);
                    loadPettyCash(1);
                });

                // Pagination
                // $(document).on('click', '.pagination a', function (e) {
                //     e.preventDefault();
                //     let url = $(this).attr('href');
                //     let params = new URLSearchParams(url.split('?')[1]);
                //     let page = params.get('page') || 1;
                //     let perPage = $('#per_page').val();
                //     loadPettyCash(page, perPage);
                // });

                // // Change per_page
                // $(document).on('change', '#per_page', function () {
                //     loadPettyCash(1, $(this).val());
                // });
                // ===== STAT CARD CLICK FILTER =====
                $(document).on('click', '.qd-stat-card[data-stat-filter]', function() {
                    var val = $(this).data('stat-filter');
                    if (val === undefined || val === null || val === '') {
                        filters.status_name = '';
                        filters.status_id = '';
                        $('.status-search-input').val('');
                        $('.status_id').val('');
                    } else {
                        filters.status_name = String(val).toLowerCase();
                        filters.status_id = filters.status_name;
                        var label = $(this).find('.qd-stat-label').first().text().trim();
                        $('.status-search-input').val(label);
                        $('.status_id').val(filters.status_name);
                    }
                    $('.qd-stat-card').removeClass('qd-stat-active');
                    $(this).addClass('qd-stat-active');
                    sessionStorage.setItem('pettycash_page', 1);
                    loadPettyCash(1);
                });

                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();

                    let url = $(this).attr('href');
                    let params = new URLSearchParams(url.split('?')[1]);
                    let page = params.get('page') || 1;
                    let perPage = $('#per_page').val();

                    sessionStorage.setItem('pettycash_page', page);

                    loadPettyCash(page, perPage);
                });
                $(document).on('change', '#per_page', function() {
                    sessionStorage.setItem('pettycash_page', 1);
                    loadPettyCash(1, $(this).val());
                });
            });



        });

        $(document).ready(function() {
            $(document).on('click', '.documentclk', function() {
                $('#documentModal1').modal('show');

                const fileType = $(this).data('filetype');
                const filesData = $(this).attr('data-files');
                let fileArray = [];

                try {
                    fileArray = JSON.parse(filesData);
                    if (typeof fileArray === 'string') {
                        fileArray = JSON.parse(fileArray);
                    }
                } catch (e) {
                    console.error('Invalid JSON in data-files:', filesData);
                    $('#image_pdfs').html('<p>Invalid file data</p>');
                    return;
                }

                if (!Array.isArray(fileArray) || fileArray.length === 0) {
                    $('#image_pdfs').html('<p>No files found</p>');
                    return;
                }

                const firstFile = fileArray[0];
                $('#pdfmain').attr('src', firstFile);

                let views = '';
                fileArray.forEach(file => {
                    const fileName = file.split('/').pop().trim();
                    views +=
                        `<button style="font-size: 11px;" type="button" class="btn btn-primary pdf-btn" data-filepath="${file}">${fileName}</button>`;
                });

                $('#image_pdfs').html(views);
            });
            $(document).on('click', '.doc-row', function() {

                $('#documentModal1').modal('show');

                let raw = $(this).attr('data-files') || '';

                // Normalize all escaping
                raw = raw.replace(/&amp;quot;/g, '&quot;') // &amp;quot; -> &quot;
                    .replace(/&quot;/g, '"') // &quot; -> "
                    .replace(/\\\//g, '/'); // \/ -> /

                let files = [];

                try {
                    files = JSON.parse(raw);
                } catch (e) {
                    console.error('Invalid data-files:', raw);
                    $('#image_pdfs').html('<p>Invalid file data</p>');
                    return;
                }

                if (!Array.isArray(files) || files.length === 0) {
                    $('#image_pdfs').html('<p>No files found</p>');
                    return;
                }

                // Show first file in iframe/img
                $('#pdfmain').attr('src', files[0]);

                // Build buttons list
                let html = '';
                files.forEach(f => {
                    let name = f.split('/').pop();
                    html += `<button type="button" class="btn btn-primary pdf-btn" style="font-size:11px" data-filepath="${f}">
                      ${name}
                  </button>`;
                });

                $('#image_pdfs').html(html);
            });


            // Global handler for pdf buttons
            $(document).on('click', '.pdf-btn', function() {
                $('.pdf-btn').removeClass('active');
                $(this).addClass('active');
                const filePath = $(this).data('filepath');
                $('#pdfmain').attr('src', filePath);
            });



        });

        const vendorsaveUrl = "{{ route('superadmin.vendorsave') }}";
        const vendorfetch = "{{ route('superadmin.vendorfetch') }}";
    </script>

    @if (!empty($_GET['id']))
        <script>
            $(document).ready(function() {
                let quotationId = "{{ request()->get('id') }}";
                let perPage = {{ $quotationlist->perPage() }};
                let currentPage = {{ $quotationlist->currentPage() }};

                if (!quotationId) return;

                // Check if bill is on current page
                let row = $('.customer-row[data-id="' + quotationId + '"]');
                if (row.length) {
                    row.trigger('click'); // open modal immediately
                    return;
                }

                // Bill not on current page, find its index (position) from server-side
                let quotationIndex = null;
                @foreach ($allquotation as $i => $quotation)
                    if ({{ $quotation->id }} == quotationId) { // ✅ use BillId not vendorId
                        quotationIndex = {{ $i + 1 }};
                    }
                @endforeach

                console.log("quotationIndex", quotationIndex);

                if (!quotationIndex) {
                    console.warn("Bill not found in dataset");
                    return;
                }

                let targetPage = Math.ceil(quotationIndex / perPage);
                console.log("quotation index:", quotationIndex, "Target page:", targetPage);

                if (currentPage != targetPage) {
                    // redirect directly to target page with id
                    let newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('page', targetPage);
                    newUrl.searchParams.set('id', BillId); // ✅ use BillId
                    window.location.href = newUrl.toString();
                }
            });
        </script>
    @endif

    <script>
        window.addEventListener('beforeunload', function() {
            sessionStorage.removeItem('force_first_page_done');
        });
    </script>
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')
</body>
<!-- [Body] end -->

</html>
