<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<style>
/* ── Vendor Panel (vp-*) ── */
.vp-backdrop {
    position: fixed; inset: 0;
    background: rgba(15,23,42,.45);
    z-index: 1040;
    opacity: 0; pointer-events: none;
    transition: opacity .25s;
}
.vp-backdrop.vp-backdrop-show { opacity: 1; pointer-events: auto; }

.vp-panel {
    position: fixed; top: 0; right: 0;
    width: 68%; max-width: 1000px;
    height: 100vh;
    background: #f8f9fc;
    box-shadow: -6px 0 32px rgba(0,0,0,.14);
    z-index: 1050;
    display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform .28s cubic-bezier(.4,0,.2,1);
    overflow: hidden;
}
.vp-panel.vp-panel-open { transform: translateX(0); }

/* Header */
.vp-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px;
    background: #fff;
    border-bottom: 1px solid #e5e9f0;
    flex-shrink: 0;
    gap: 12px;
}
.vp-header-left { display: flex; align-items: center; gap: 12px; }
.vp-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    background: linear-gradient(135deg,#4a6cf7,#6a82fb);
    color: #fff; font-weight: 700; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.vp-header-info .vp-vendor-name {
    font-size: 15px; font-weight: 700; color: #1e293b; line-height: 1.2;
}
.vp-header-info .vp-vendor-company {
    font-size: 12px; color: #64748b; margin-top: 2px;
}
.vp-header-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; flex-wrap: wrap; }
.vp-close-btn {
    background: none; border: none; cursor: pointer;
    color: #64748b; font-size: 18px; line-height: 1;
    padding: 4px 6px; border-radius: 6px;
    transition: background .15s, color .15s;
}
.vp-close-btn:hover { background: #f1f5f9; color: #1e293b; }

/* Tabs */
.vp-tabs {
    display: flex; gap: 2px;
    padding: 0 20px;
    background: #fff;
    border-bottom: 1px solid #e5e9f0;
    flex-shrink: 0;
    overflow-x: auto;
}
.vp-tab {
    padding: 11px 16px; cursor: pointer;
    font-size: 13px; font-weight: 500; color: #64748b;
    border-bottom: 2px solid transparent;
    white-space: nowrap;
    transition: color .15s, border-color .15s;
    display: flex; align-items: center;
}
.vp-tab:hover { color: #4a6cf7; }
.vp-tab.active { color: #4a6cf7; border-bottom-color: #4a6cf7; }

/* Body */
.vp-body {
    flex: 1; overflow-y: auto; padding: 16px 20px;
}

/* Overview grid */
.vp-overview-grid {
    display: grid; grid-template-columns: 300px 1fr; gap: 16px;
    align-items: start;
}
@media (max-width: 780px) {
    .vp-panel { width: 96%; }
    .vp-overview-grid { grid-template-columns: 1fr; }
}

/* Stats row */
.vp-stats-row {
    display: grid; grid-template-columns: repeat(3,1fr); gap: 10px;
    margin-bottom: 12px;
}
.vp-stat-box {
    background: #fff; border-radius: 10px;
    border: 1px solid #e5e9f0;
    padding: 10px 12px; text-align: center;
}
.vp-stat-label { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
.vp-stat-val   { font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 3px; }

/* Info card */
.vp-info-card {
    background: #fff; border-radius: 10px;
    border: 1px solid #e5e9f0;
    padding: 14px 16px; margin-bottom: 12px;
}
.vp-info-section-title {
    display: flex; align-items: center; justify-content: space-between;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: #475569;
    margin-bottom: 12px;
}
.vp-section-toggle { cursor: pointer; user-select: none; }
.vp-section-toggle:hover { color: #4a6cf7; }
.vp-chevron { font-size: 12px; transition: transform .2s; color: #94a3b8; }

/* Info rows */
.vp-info-rows { display: flex; flex-direction: column; gap: 8px; }
.vp-info-row {
    display: flex; justify-content: space-between; align-items: flex-start;
    font-size: 12.5px; gap: 8px;
}
.vp-info-key { color: #94a3b8; flex-shrink: 0; display: flex; align-items: center; gap: 5px; }
.vp-info-val { color: #1e293b; font-weight: 500; text-align: right; word-break: break-word; }

/* Address grid */
.vp-address-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 12.5px; }
.vp-address-label { font-size: 10.5px; color: #94a3b8; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px; }
.vp-address-val { color: #1e293b; line-height: 1.55; }

/* List items (contacts, bank) */
.vp-list-items { display: flex; flex-direction: column; gap: 8px; }
.contact-person, .bank-person {
    background: #f8fafc; border-radius: 8px;
    padding: 8px 12px; display: flex; flex-direction: column; gap: 3px;
    font-size: 12.5px; border: 1px solid #f1f5f9;
}
.contact-person .contact-name { font-weight: 600; color: #1e293b; }
.contact-person .contact-phone { color: #64748b; }
.bank-person .bank-name { font-weight: 600; color: #1e293b; }
.bank-person .bank-phone { color: #64748b; }

/* Income total */
.vp-income-total {
    font-size: 12px; font-weight: 600; color: #475569;
    text-align: right; margin-top: 8px;
}

/* Timeline */
.vp-timeline .timeline-item {
    display: flex; gap: 12px;
    padding: 8px 0; border-bottom: 1px solid #f1f5f9;
    font-size: 12px;
}
.vp-timeline .timeline-item:last-child { border-bottom: none; }
.vp-timeline .timeline-date { color: #94a3b8; font-size: 11px; min-width: 80px; flex-shrink: 0; line-height: 1.4; }
.vp-timeline .timeline-event { font-weight: 600; color: #1e293b; }
.vp-timeline .timeline-meta { color: #64748b; margin-top: 2px; line-height: 1.4; }

/* Empty state */
.vp-empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px; }
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
    <div class="pc-container">
        <div class="pc-content">

            <div class="qd-card">

                {{-- ── Header ── --}}
                <div class="qd-header">
                    <div class="qd-header-title">
                        <i class="bi bi-people-fill"></i>
                        Vendor List
                    </div>
                    <div class="qd-header-actions">
                        {{-- Toggle: Filters --}}
                        <button class="btn btn-sm qd-toggle-btn" id="toggleFilters" title="Toggle Filters">
                            <i class="bi bi-funnel me-1"></i>Filter
                            <i class="bi bi-chevron-up qd-toggle-icon" id="filtersChevron"></i>
                                </button>
                        <a href="{{ route('superadmin.getvendorcreate') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>New Vendor
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importvendorModal">
                                    <i class="bi bi-upload me-2"></i>Import Vendor
                                </a></li>
                                <li><a class="dropdown-item export-btn" href="#" data-format="xlsx">
                                    <i class="bi bi-file-earmark-excel me-2"></i>Export XLSX
                                </a></li>
                                <li><a class="dropdown-item export-btn" href="#" data-format="csv">
                                    <i class="bi bi-filetype-csv me-2"></i>Export CSV
                                </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                {{-- ── Filters ── --}}
                <div class="qd-filters" id="filtersSection">
                    <div class="qd-filter-row">
                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label>Vendor</label>
                            <input type="text" class="form-control vendor-search-input dropdown-search-input" placeholder="Select Vendor" readonly>
                            <input type="hidden" name="vendor_id" class="vendor_id">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="inner-search-container">
                                    <input type="text" class="inner-search" placeholder="Search Vendor...">
                            </div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                            </div>
                                <div class="dropdown-list multiselect vendor-list"></div>
                        </div>
                    </div>

                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label>Status</label>
                            <input type="text" class="form-control status-search-input dropdown-search-input" placeholder="Select Status" readonly>
                            <input type="hidden" name="status_filter" class="status_filter">
                          <div class="dropdown-menu tax-dropdown">
                            <div class="inner-search-container">
                                    <input type="text" class="inner-search" placeholder="Search Status...">
                            </div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                            </div>
                                <div class="dropdown-list multiselect status-list">
                                    <div data-value="Active" data-id="0">Active</div>
                                    <div data-value="Inactive" data-id="1">Inactive</div>
                          </div>
                        </div>
                        </div>
                          </div>
                        </div>

                {{-- ── Search bar ── --}}
                <div class="qd-search-row">
                    <div class="qd-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" class="universal_search" placeholder="Search vendor name, company, PAN, GST...">
                      </div>
                    </div>

                {{-- ── Applied filters ── --}}
                <div class="qd-applied-bar">
                    <span class="applied-label">Filters:</span>
                    <div id="filter-summary" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
                  </div>

                {{-- ── Table ── --}}
                <div class="qd-table-wrap">
              <div id="Neft-body">
                @include('vendor.partials.table.vendor_rows',['vendor' => $vendor, 'perPage' => $perPage])
                    </div>
              </div>

            </div>{{-- qd-card --}}
        </div>
    </div>

    <!-- Vendor Overview Panel Backdrop -->
    <div class="vp-backdrop" id="customerModalBackdrop"></div>

    <!-- Vendor Overview Panel -->
    <div class="vp-panel" id="customerModal">

        {{-- ── Panel Header ── --}}
        <div class="vp-header">
            <div class="vp-header-left">
                <div class="vp-avatar" id="vp-avatar-initials">VS</div>
                <div class="vp-header-info">
                    <div class="vp-vendor-name contact-name">Vendor Name</div>
                    <div class="vp-vendor-company" id="vp-company-name">Company Name</div>
                </div>
            </div>
            <div class="vp-header-right">
                @if ($limit_access === 1)
                    <button class="btn btn-sm btn-outline-danger delete_btn">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                @endif
                <button class="btn btn-sm btn-outline-secondary edit_btn">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <div class="dropdown">
                    <button class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-plus-lg me-1"></i>New
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('superadmin.getquotationcreate') }}"><i class="bi bi-file-earmark-text me-2"></i>Quotation</a></li>
                        <li><a class="dropdown-item" href="{{ route('superadmin.getpurchasecreate') }}"><i class="bi bi-cart me-2"></i>Purchase Order</a></li>
                        <li><a class="dropdown-item" href="{{ route('superadmin.getbillcreate') }}"><i class="bi bi-receipt me-2"></i>Bill</a></li>
                        <li><a class="dropdown-item" href="{{ route('superadmin.getbillmadecreate') }}"><i class="bi bi-cash-coin me-2"></i>Bill Payment</a></li>
                        <li><a class="dropdown-item" href="{{ route('superadmin.getneftdashboard') }}"><i class="bi bi-bank me-2"></i>NEFT</a></li>
                        <li><a class="dropdown-item" href="{{ route('superadmin.getgrncreate') }}"><i class="bi bi-box-seam me-2"></i>GRN</a></li>
                    </ul>
                </div>
                <button class="vp-close-btn" id="closeCustomerModal" title="Close">
                    <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

        {{-- ── Tabs ── --}}
        <div class="vp-tabs">
            <div class="vp-tab active" data-tab="overview"><i class="bi bi-grid-1x2 me-1"></i>Overview</div>
            <div class="vp-tab" data-tab="transactions"><i class="bi bi-arrow-left-right me-1"></i>Transactions</div>
            <div class="vp-tab" data-tab="comments"><i class="bi bi-chat-left-text me-1"></i>Comments</div>
            <div class="vp-tab" data-tab="mails"><i class="bi bi-envelope me-1"></i>Mails</div>
            <div class="vp-tab" data-tab="statement"><i class="bi bi-file-earmark-bar-graph me-1"></i>Statement</div>
    </div>

        {{-- ── Tab Content ── --}}
        <div class="vp-body">

            {{-- Overview Tab --}}
            <div id="overview-tab" class="vp-tab-content active">
                <div class="vp-overview-grid">

                    {{-- Left Column --}}
                    <div class="vp-col-left">

                        {{-- Stats Bar --}}
                        <div class="vp-stats-row">
                            <div class="vp-stat-box">
                                <div class="vp-stat-label">Outstanding</div>
                                <div class="vp-stat-val" id="customer-balance">₹0.00</div>
                                        </div>
                            <div class="vp-stat-box">
                                <div class="vp-stat-label">Unused Credits</div>
                                <div class="vp-stat-val">₹0.00</div>
                                    </div>
                            <div class="vp-stat-box">
                                <div class="vp-stat-label">Currency</div>
                                <div class="vp-stat-val customer_currency">INR</div>
                                </div>
                            </div>

                        {{-- Info Card --}}
                        <div class="vp-info-card">
                            <div class="vp-info-section-title">
                                <i class="bi bi-info-circle me-1"></i>Basic Info
                                </div>
                            <div class="vp-info-rows">
                                <div class="vp-info-row">
                                    <span class="vp-info-key"><i class="bi bi-hash"></i> Vendor ID</span>
                                    <span class="vp-info-val customerid">—</span>
                                    </div>
                                <div class="vp-info-row">
                                    <span class="vp-info-key"><i class="bi bi-credit-card"></i> PAN</span>
                                    <span class="vp-info-val" id="pannumber">—</span>
                                    </div>
                                <div class="vp-info-row">
                                    <span class="vp-info-key"><i class="bi bi-currency-rupee"></i> Currency</span>
                                    <span class="vp-info-val" id="currency">INR</span>
                                </div>
                                <div class="vp-info-row">
                                    <span class="vp-info-key"><i class="bi bi-clock"></i> Payment Terms</span>
                                    <span class="vp-info-val cust_payment_term">—</span>
                            </div>
                                <div class="vp-info-row">
                                    <span class="vp-info-key"><i class="bi bi-globe"></i> Language</span>
                                    <span class="vp-info-val" id="customer_language">—</span>
                                </div>
                                <div class="vp-info-row">
                                    <span class="vp-info-key"><i class="bi bi-calendar3"></i> Created On</span>
                                    <span class="vp-info-val created_date">—</span>
                                </div>
                            </div>
                                    </div>

                        {{-- Address Card --}}
                        <div class="vp-info-card">
                            <div class="vp-info-section-title vp-section-toggle" data-target="#vp-address-body">
                                <span><i class="bi bi-geo-alt me-1"></i>Address</span>
                                <i class="bi bi-chevron-down vp-chevron"></i>
                                        </div>
                            <div id="vp-address-body">
                                <div class="vp-address-grid">
                                    <div>
                                        <div class="vp-address-label"><i class="bi bi-building me-1"></i>Billing</div>
                                        <div class="billing-address vp-address-val">—</div>
                                    </div>
                                    <div>
                                        <div class="vp-address-label"><i class="bi bi-truck me-1"></i>Shipping</div>
                                        <div class="shipping-address vp-address-val">—</div>
                                </div>
                                    </div>
                                        </div>
                                    </div>

                        {{-- Contact Persons --}}
                        <div class="vp-info-card">
                            <div class="vp-info-section-title vp-section-toggle" data-target="#vp-contacts-body">
                                <span><i class="bi bi-people me-1"></i>Contact Persons</span>
                                <i class="bi bi-chevron-down vp-chevron"></i>
                                </div>
                            <div id="vp-contacts-body">
                                <div class="contact-content vp-list-items"></div>
                                    </div>
                                    </div>

                        {{-- Bank Details --}}
                        <div class="vp-info-card">
                            <div class="vp-info-section-title vp-section-toggle" data-target="#vp-bank-body">
                                <span><i class="bi bi-bank2 me-1"></i>Bank Details</span>
                                <i class="bi bi-chevron-down vp-chevron"></i>
                                </div>
                            <div id="vp-bank-body">
                                <div class="bank-content vp-list-items"></div>
                                </div>
                            </div>

                        {{-- Documents --}}
                        <div class="vp-info-card">
                            <div class="vp-info-section-title vp-section-toggle" data-target="#vp-docs-body">
                                <span><i class="bi bi-paperclip me-1"></i>Documents</span>
                                <i class="bi bi-chevron-down vp-chevron"></i>
                            </div>
                            <div id="vp-docs-body">
                                <div id="upload_document" class="d-flex flex-wrap gap-2 mt-2"></div>
                            </div>
                            </div>

                    </div>{{-- /vp-col-left --}}

                    {{-- Right Column --}}
                    <div class="vp-col-right">

                        {{-- Income Chart --}}
                        <div class="vp-info-card">
                            <div class="vp-info-section-title">
                                <span><i class="bi bi-bar-chart-line me-1"></i>Income</span>
                                <select id="vendor-overview-fy" class="form-select form-select-sm ms-auto" style="width:130px;">
                                        <option value="">Current FY</option>
                                        @php
                                        $currentYear  = (int) date('Y');
                                            $currentMonth = (int) date('n');
                                            $fyStart = $currentMonth >= 4 ? $currentYear : $currentYear - 1;
                                            for ($i = 0; $i <= 4; $i++) {
                                            $y     = $fyStart - $i;
                                                $label = $y . '-' . substr((string)($y + 1), -2);
                                                echo '<option value="' . $label . '">' . $label . '</option>';
                                            }
                                        @endphp
                                    </select>
                                </div>
                            <div style="height:200px; position:relative;">
                                    <canvas id="incomeChart"></canvas>
                                </div>
                            <div class="vp-income-total" id="total-income">Total Income — ₹0.00</div>
                            </div>

                        {{-- Activity Timeline --}}
                        <div class="vp-info-card">
                            <div class="vp-info-section-title">
                                <i class="bi bi-clock-history me-1"></i>Recent Activity
                                    </div>
                            <div class="timeline-container vp-timeline"></div>
                                </div>

                    </div>{{-- /vp-col-right --}}
                </div>{{-- /vp-overview-grid --}}
            </div>{{-- /overview-tab --}}

            {{-- Transactions Tab --}}
            <div id="transactions-tab" class="vp-tab-content" style="display:none;">
                <div id="transactions-tab-inner"></div>
                                </div>

            {{-- Comments Tab --}}
            <div id="comments-tab" class="vp-tab-content" style="display:none;">
                <div class="vp-empty-state">
                    <i class="bi bi-chat-left-text" style="font-size:2rem;color:#ccc;"></i>
                    <p class="text-muted mt-2">No comments yet.</p>
                                    </div>
                                </div>

            {{-- Mails Tab --}}
            <div id="mails-tab" class="vp-tab-content" style="display:none;">
                <div class="vp-empty-state">
                    <i class="bi bi-envelope" style="font-size:2rem;color:#ccc;"></i>
                    <p class="text-muted mt-2">No mails found.</p>
                            </div>
            </div>

            {{-- Statement Tab --}}
            <div id="statement-tab" class="vp-tab-content" style="display:none;"></div>

        </div>{{-- /vp-body --}}
    </div>{{-- /vp-panel --}}
    <!--import Modal -->
          <div class="modal fade" id="importvendorModal" tabindex="-1" aria-labelledby="importvendorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="importvendorModalLabel">Import vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p>Step 1: Download the Excel Template</p>
                    <a href="{{ url('/vendor-download-template') }}" class="btn btn-success mb-3">
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


          <div
                class="modal fade"
                id="documentModal1"
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
                                <div class="btn-group-vertical w-100" id="image_pdfs" style="
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

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
{{-- <script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script> --}}

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

        // Toggle Filters panel
        $('#toggleFilters').on('click', function () {
            const $section = $('#filtersSection');
            const $icon = $('#filtersChevron');
            if ($section.is(':visible')) {
                $section.slideUp(200);
                $icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
            } else {
                $section.slideDown(200);
                $icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
            }
        });

        // Vendor Active/Inactive toggle
        $(document).on('change', '.vendor-status-toggle', function () {
            const $toggle = $(this);
            const vendorId = $toggle.data('id');
            const $label = $toggle.closest('td').find('.vendor-status-label');
            $.ajax({
                url: '{{ route("superadmin.vendor.togglestatus") }}',
                type: 'POST',
                data: { id: vendorId, _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.success) {
                        if (response.active_status == 0) {
                            $label.removeClass('bg-secondary').addClass('bg-success').text('Active');
                            $toggle.prop('checked', true);
                        } else {
                            $label.removeClass('bg-success').addClass('bg-secondary').text('Inactive');
                            $toggle.prop('checked', false);
                        }
                        toastr.success(response.message);
                    }
                },
                error: function () {
                    toastr.error('Failed to update vendor status.');
                    $toggle.prop('checked', !$toggle.prop('checked'));
                }
            });
        });
    });

    $(document).ready(function() {
            // Global handler for pdf buttons
            $(document).on('click', '.pdf-btn', function () {
                $('.pdf-btn').removeClass('active');
                $(this).addClass('active');
                const filePath = $(this).data('filepath');
                $('#pdfmain').attr('src', filePath);
            });

        $(document).on('click', '.import-btn', function (e) {
            e.preventDefault();

            const fileInput = $('#importFileInput')[0];
            const file = fileInput.files[0];

            if (!file) {
                toastr.error("Please select a file to upload.");
                return;
            }

            let formData = new FormData();
            formData.append('file', file);

            $.ajax({
                url: '{{ url("/import-vendor") }}',
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
                        $('#importvendorModal').modal('hide');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        toastr.error("Unexpected response from server.");
                    }
                },
                error: function (xhr) {
                    console.error("Import failed:", xhr);
                    toastr.error("Failed to import file.");
                }
            });
        });

        $('.export-btn').click(function (e) {
            e.preventDefault();
            const format = $(this).data('format');
            // Get all checked checkboxes and collect their values (IDs)
            const selectedIds = $('.row-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            // if (selectedIds.length === 0) {
            //     alert('Please select at least one record to export.');
            //     return;
            // }
            console.log("format",format);
            console.log("selectedIds",selectedIds);
            $.ajax({
                url: '{{ route("superadmin.exportvendor") }}',
                type: 'GET',
                data: {
                ids: selectedIds,
                format: format,
                _token: '{{ csrf_token() }}'
                },
                xhrFields: {
                responseType: 'blob'
                },
                success: function (data) {
                const blob = new Blob([data], { type: 'application/octet-stream' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `vendor_export.${format}`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                }
            });
            });


        var vendorId="";
        // Handle row click
         $(document).on('click','.customer-row', function (e) {
            // Highlight selected row
            if ($(e.target).is('input[type="checkbox"]') || $(e.target).closest('.form-check').length) {
                return;
            }
            if ($(e.target).closest('.uploadClick').length) {
                return;
            }
            $('.customer-row').removeClass('selected');
            $(this).addClass('selected');

            // Get customer data from data attributes
             vendorId = $(this).data('id');

            const customerName = $(this).data('name');
            const customerEmail = $(this).data('email');
            const customerPhone = $(this).data('phone');
            const customerCompany = $(this).data('company');
            const customerBalance = $(this).data('balance');
            const customerCurrency = $(this).data('currency');
            const customerPan = $(this).data('pan');
            const customerType = $(this).data('type');
            const customerpayment_terms= $(this).data('payment_terms');
            const customerLanguage = $(this).data('portal_language');
            const billing_address = JSON.parse($(this).attr('data-billingaddress'));
            const shippingAddress = JSON.parse($(this).attr('data-shippingaddress'));
            const contacts = JSON.parse($(this).attr('data-contacts'));
            const history = JSON.parse($(this).attr('data-history'));
            const bankdetails = JSON.parse($(this).attr('data-bankdetails'));
            const created_at = $(this).data('created_at');
            const all_data = $(this).data('all_data');
            const created_date = created_at.split(" ")[0];
            const custype=(customerType==0)?'Business':'Individual';
            console.log("all_data",all_data);

            const billingaddress = `
                ${billing_address.address}</br>
                ${billing_address.city}</br>
                ${billing_address.state}</br>
                ${billing_address.country}</br>
                ${billing_address.zip_code}</br>
                ${billing_address.phone}
            `;
            const shipping_address = `
                ${shippingAddress.address}</br>
                ${shippingAddress.city}</br>
                ${shippingAddress.state}</br>
                ${shippingAddress.country}</br>
                ${shippingAddress.zip_code}</br>
                ${shippingAddress.phone}
            `;
            let contact = ""; // Use let instead of const

            contacts.forEach(element => {
                contact += `
                    <div class="contact-person">
                        <span class="contact-name">${element.first_name} ${element.last_name}</span>
                        <span class="contact-phone">${element.mobile}</span>
                    </div>
                `;
            });
            let bank = ""; // Use let instead of const

            bankdetails.forEach(element => {
                bank += `
                    <div class="bank-person">
                        <span class="bank-name">Bank Name: ${element.bank_name}</span>
                        <span class="bank-phone">Account No: ${element.accont_number}</span>
                        <span class="bank-phone">IFSC Code: ${element.ifsc_code}</span>
                    </div>
                `;
            });

            let history_list="";
            history.slice(-7).forEach(element=>{
                history_list+=`<div class="timeline-item">
                                <div class="timeline-date">${element.date}<br>${element.time}</div>
                                <div class="timeline-content">
                                <div class="timeline-event">${element.name}</div>
                                <div class="timeline-meta">${element.description}</div>
                                </div>
                            </div>`;
            })

            // Update panel content
            // Avatar initials
            const initials = (customerName || '').trim().split(/\s+/).slice(0,2).map(w=>w[0]||'').join('').toUpperCase() || 'V';
            $('#vp-avatar-initials').text(initials);

            $('.contact-name').text(customerName);
            $('#vp-company-name').text(customerCompany);
            $('.created_date').text(created_date);
            $('.customerid').text(vendorId);
            $('.cust_payment_term').text(customerpayment_terms || '—');
            $('.billing-address').html(billingaddress || '—');
            $('.shipping-address').html(shipping_address || '—');
            $('.contact-content').html(contact || '<p class="text-muted small mb-0">No contacts.</p>');
            $('.bank-content').html(bank || '<p class="text-muted small mb-0">No bank details.</p>');
            $('.vp-timeline').html(history_list || '<p class="text-muted small mb-0">No activity.</p>');
            $('#customer-balance').text('₹' + customerBalance.toFixed(2));
            $('#pannumber').text(customerPan || '—');
            $('#currency').text(customerCurrency);
            $('.customer_currency').text(customerCurrency);
            $('#customer_language').text(customerLanguage || '—');
            $('#total-income').text('Total Income — ₹0.00');
            $('#vendor-overview-fy').val('');
            // Reset to overview tab
            $('.vp-tab').removeClass('active');
            $('.vp-tab[data-tab="overview"]').addClass('active');
            $('.vp-tab-content').hide();
            $('#overview-tab').show();
            // Open panel
            $('#customerModal').addClass('vp-panel-open');
            $('#customerModalBackdrop').addClass('vp-backdrop-show');
            $('body').css('overflow', 'hidden');

            // Function to generate preview HTML for a file
          function generatePreviewHtml(filename, basePath, containerId,title) {
            const extension = filename.split('.').pop().toLowerCase();
            const fileType = 'documents';
            const fileArray = JSON.stringify([basePath + filename]);

            let iconUrl;
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
              iconUrl = `${basePath}${filename}`;
            } else if (extension === 'pdf') {
              iconUrl = 'https://cdn-icons-png.flaticon.com/512/337/337946.png';
            } else {
              iconUrl = 'https://cdn-icons-png.flaticon.com/512/564/564619.png';
            }

            const previewHtml = `
                        <div>
                            <h5>${title}</h5>
                            <div class="preview-card col-sm-3 uploadClick"
                                data-filetype="${fileType}"
                                data-files='${fileArray}'
                                onclick="Documentview(this)">
                                <img src="${iconUrl}" alt="${extension === 'pdf' ? 'PDF' : 'File'}" style="height:60px;">
                                <div class="file_name">${filename}</div>
                            </div>
                        </div>
                        `;


            $(containerId).append(previewHtml);
          }

          $('#upload_document').empty();
          let pan_upload = all_data.pan_upload;
            if (pan_upload && pan_upload.trim() !== "" && pan_upload !== null ) {
                pan_upload = JSON.parse(pan_upload);
                pan_upload.forEach(filename =>
                    generatePreviewHtml(filename, '../public/uploads/customers/', '#upload_document','Pan Upload')
                );
            }
          let documents = all_data.documents;
            if (documents && documents.trim() !== "" && documents !== null ) {
                documents = JSON.parse(documents);
                documents.forEach(filename =>
                    generatePreviewHtml(filename, '../public/uploads/customers/', '#upload_document','Document Upload')
                );
            }

           bankdetails.forEach((element,index) => {
                if (element.bank_uploads !== "" && element.bank_uploads !== null) {
                    let bank_upload = JSON.parse(element.bank_uploads); // ✅ fixed
                    bank_upload.forEach(filename =>
                        generatePreviewHtml(filename, '../public/uploads/customers/', '#upload_document','Bank Upload')
                    );
                }
                });


            $.ajax({
                    url: '{{ route("superadmin.gettranscationvendor") }}',
                    type: 'GET',
                    data: { id: vendorId },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        $('#transactions-tab-inner').html(response.html);
                        $('#statement-tab').html(response.statement);
                    },
                    error: function (xhr) {
                        console.error("Request failed:", xhr);
                        toastr.error("Failed to fetch data.");
                    }
                });

            loadVendorOverviewChart();

        });

        function loadVendorOverviewChart() {
            if (!vendorId) return;
            var fy = $('#vendor-overview-fy').val() || '';
            $.ajax({
                url: '{{ route("superadmin.getvendorchart") }}',
                type: 'GET',
                data: { id: vendorId, financial_year: fy },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#customer-balance').text('₹' + Number(response.balance).toLocaleString('en-IN'));
                    var fyLabel = (response.fy_label || 'FY') + '';
                    $('#total-income').text('Total Income (' + fyLabel + ') - ₹' + Number(response.total_income || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 }));
                    updateIncomeChart(response.months, response.amounts, response.balance);
                },
                error: function (xhr) {
                    console.error("Request failed:", xhr);
                    toastr.error("Failed to fetch chart data.");
                }
            });
        }

        $(document).on('change', '#vendor-overview-fy', function () {
            loadVendorOverviewChart();
        });

       $('.edit_btn').on('click', function() {
        let currentFilters = {
                    vendor_id: $('.vendor_id').val(),
                    vendor_name: $('.vendor-search-input').val(),
                    status_name: $('.status-search-input').val(),
                    status_filter: $('.status_filter').val(),

                    universal_search: $('.universal_search').val()
                };

                sessionStorage.setItem("vendor_filters", JSON.stringify(currentFilters));

                sessionStorage.setItem("restore_filters", "1");

                sessionStorage.setItem("vendor_page", sessionStorage.getItem('bill_made_page') || 1);

            window.location.href = "{{ route('superadmin.getvendorcreate') }}" + "?id=" + vendorId;
        });
       $('.delete_btn').on('click', function () {

            Swal.fire({
                title: "Are you sure?",
                text: "This Vendor will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('superadmin.vendor.vendordelete') }}",
                        type: "DELETE",
                        data: {
                            id: vendorId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success",
                                timer: 1500,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        },
                        error: function () {
                            Swal.fire(
                                "Failed!",
                                "Something went wrong. Please try again.",
                                "error"
                            );
                        }
                    });
                }
            });
        });


        $('.uploadClick').on('click',function(){
            alert(1111);
        })



        // Close panel
        function closeVendorPanel() {
            $('#customerModal').removeClass('vp-panel-open');
            $('#customerModalBackdrop').removeClass('vp-backdrop-show');
            $('body').css('overflow', 'auto');
        }
        $('#closeCustomerModal').on('click', closeVendorPanel);
        $('#customerModalBackdrop').on('click', closeVendorPanel);

        // Tab switching — delegated from panel (not document) to avoid stopPropagation conflict
        $('#customerModal').on('click', '.vp-tab', function(e) {
            e.stopPropagation();
            const tabId = $(this).data('tab');
            $('.vp-tab').removeClass('active');
            $(this).addClass('active');
            $('.vp-tab-content').hide();
            $('#' + tabId + '-tab').show();
        });

        // Collapsible section toggles — same fix
        $('#customerModal').on('click', '.vp-section-toggle', function(e) {
            e.stopPropagation();
            const $target = $($(this).data('target'));
            const $chevron = $(this).find('.vp-chevron');
            $target.slideToggle(180);
            $chevron.toggleClass('bi-chevron-down bi-chevron-up');
        });

        // Stop panel backdrop from triggering when clicking panel content
        $('#customerModal').on('click', function(e) {
            e.stopPropagation();
        });

    });

        let incomeChart = null;

        function updateIncomeChart(months, amounts, balance) {
            const ctx = document.getElementById('incomeChart');

            if (incomeChart) {
                incomeChart.data.labels = months || [];
                incomeChart.data.datasets[0].data = (amounts || []).map(v => v / 1000);
                incomeChart.update();
                return;
            }

            incomeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months || [],
                    datasets: [{
                        label: 'Income',
                        data: (amounts || []).map(v => v / 1000),
                        backgroundColor: '#4a6cf7',
                        borderColor: '#4a6cf7',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value + 'k';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₹' + (context.raw * 1000).toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }

        $('#closeCustomerModal, #customerModalBackdrop').on('click', function() {
            if (incomeChart) {
                incomeChart.destroy();
                incomeChart = null;
            }
            $('#customerModal').removeClass('vp-panel-open');
            $('#customerModalBackdrop').removeClass('vp-backdrop-show');
            $('body').css('overflow', 'auto');
        });

        function smoothToggle(header, arrowSelector) {
          const content = header.nextElementSibling;
          const arrow = header.querySelector(arrowSelector);

          // Prevent double-click animations
          if (content.dataset.animating === "true") return;
          content.dataset.animating = "true";

          const isCollapsed = window.getComputedStyle(content).display === "none";

          if (isCollapsed) {
            // EXPANDING the content
            content.style.display = "block";
            content.style.overflow = "hidden";
            content.style.height = "0px";

            // Small delay to ensure styles are applied before transition
            requestAnimationFrame(() => {
              const height = content.scrollHeight + "px";
              content.style.transition = "height 0.3s ease";
              content.style.height = height;
            });

            // Clean up after transition completes
            const onOpenComplete = () => {
              content.style.height = "auto";
              content.style.overflow = "";
              content.style.transition = "";
              content.dataset.animating = "false";
              arrow.textContent = "▼";
              content.removeEventListener("transitionend", onOpenComplete);
            };
            content.addEventListener("transitionend", onOpenComplete);
          } else {
            // COLLAPSING the content
            const height = content.scrollHeight + "px";
            content.style.height = height;
            content.style.overflow = "hidden";

            // Force reflow before transition
            content.offsetHeight;

            content.style.transition = "height 0.3s ease";
            content.style.height = "0px";

            // Clean up after transition completes
            const onCloseComplete = () => {
              content.style.display = "none";
              content.style.height = "";
              content.style.overflow = "";
              content.style.transition = "";
              content.dataset.animating = "false";
              arrow.textContent = "▶";
              content.removeEventListener("transitionend", onCloseComplete);
            };
            content.addEventListener("transitionend", onCloseComplete);
          }
        }

      // Toggle functions for different sections
      function toggleSection(header) {
        smoothToggle(header, '.toggle-arrow');
      }

      function toggleDetails(header) {
        smoothToggle(header, '.toggle-arrow');
      }

      function toggleContactSection(header) {
        smoothToggle(header, '.contact-toggle-icon');  // Corrected selector
      }

      function togglebankSection(header) {
        smoothToggle(header, '.bank-toggle-icon');    // Corrected selector
      }

      function toggleRecordInfo(header) {
        smoothToggle(header, '.record-toggle-arrow');
      }
       function Documentview(el) {
            const myModal = new bootstrap.Modal(document.getElementById('documentModal1'));
            myModal.show();

            const fileType = $(el).data('filetype');
            const filesData = $(el).attr('data-files');
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
                views += `<button style="font-size: 11px;"
                                type="button"
                                class="btn btn-primary pdf-btn"
                                data-filepath="${file}">
                                ${fileName}
                        </button>`;
            });

            $('#image_pdfs').html(views);
        }

        // PDF button click handler
        $(document).on('click', '.pdf-btn', function () {
            $('.pdf-btn').removeClass('active');
            $(this).addClass('active');
            const filePath = $(this).data('filepath');
            $('#pdfmain').attr('src', filePath);
        });

    const vendorsaveUrl = "{{ route('superadmin.vendorsave') }}";
    const vendorfetch = "{{ route('superadmin.vendorfetch') }}";


    $(document).ready(function () {

    const Tblvendor = @json($Tblvendor);

      Tblvendor.forEach(Tblvendor => {
                        const item = $(`
                            <div data-value="${Tblvendor.display_name}" data-id="${Tblvendor.id}">${Tblvendor.display_name}</div>
                        `);
                        $('.vendor-list').append(item);
                    });


    $(document).ready(function () {

        // =================== OPEN DROPDOWN ===================
        $(document).on('click', '.dropdown-search-input', function (e) {
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
        $(document).on('keyup', '.inner-search', function () {
        const searchVal = $(this).val().toLowerCase();
        $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function () {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(searchVal) > -1);
        });
        });

        // =================== MULTISELECT (Individual Item) ===================
        $(document).on('click', '.dropdown-list.multiselect div', function (e) {
        e.stopPropagation();
        $(this).toggleClass('selected');
        const $dropdown = $(this).closest('.tax-dropdown');
        updateMultiSelection($dropdown);
        });

        // =================== SELECT ALL ===================
        $(document).on('click', '.select-all', function (e) {
        e.stopPropagation();
        const $dropdown = $(this).closest('.tax-dropdown');
        $dropdown.find('.dropdown-list.multiselect div').addClass('selected');
        updateMultiSelection($dropdown);
        });

        // =================== DESELECT ALL ===================
        $(document).on('click', '.deselect-all', function (e) {
        e.stopPropagation();
        const $dropdown = $(this).closest('.tax-dropdown');
        $dropdown.find('.dropdown-list.multiselect div').removeClass('selected');
        updateMultiSelection($dropdown);
        });

        // =================== CLOSE ON OUTSIDE CLICK ===================
        $(document).on('click', function (e) {
        if (!$(e.target).closest('.tax-dropdown-wrapper').length && !$(e.target).closest('.tax-dropdown').length) {
            $('.dropdown-menu.tax-dropdown').hide();
        }
        });

        // =================== UPDATE MULTISELECT VALUES ===================
        function updateMultiSelection($dropdown) {
        const wrapper = $dropdown.data('wrapper');
        if (!wrapper) return;

        const selectedItems = [];
        const selectedIds = [];

        $dropdown.find('.dropdown-list.multiselect div.selected').each(function () {
            selectedItems.push($(this).text().trim());
            selectedIds.push($(this).data('id'));
        });

        const $visibleInput = wrapper.find('.dropdown-search-input');
        const $hiddenInput = wrapper.find('input[type="hidden"]');

        // Update visible & hidden inputs
        $visibleInput.val(selectedItems.join(', '));
        $hiddenInput.val(selectedIds.join(','));

        // ✅ Trigger change event (important)
        $hiddenInput.trigger('click');
        }
    });

      $('.zone_id').on('click', function () {
        var id=$('.zone_id').val();
        let formData = new FormData();
        formData.append('id',id);
        $.ajax({
            url: '{{ route("superadmin.getvendor") }}',
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
              // toastr.success(response.message);
              if(response.branch !==""){
                $('.branch-list div').remove();
                response.branch.forEach(branch => {
                    const item = $(`
                      <div data-id="${branch.id}">${branch.name} </div>
                    `);
                    $('.branch-list').append(item);
                });
              }
            },
            error: function (xhr) {
                console.error("Error saving form:", xhr);
                toastr.error("Something went wrong.");
            }
        });
      });
      $('.zone-list div').on('click',function(){
        $('.branch-search-input').val('');
        $('.branch_id').val('');
      })
       $('.zone-inner-search').on('keyup', function () {
          const searchText = $(this).val().toLowerCase();
          const list = $(this).closest('.dropdown-menu').find('.zone-list div');

          list.each(function () {
            const itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.includes(searchText));
          });
        });
        $('.branch-inner-search').on('keyup', function () {
          const searchText = $(this).val().toLowerCase();
          const list = $(this).closest('.dropdown-menu').find('.branch-list div');

          list.each(function () {
            const itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.includes(searchText));
          });
        });
        $('.company-inner-search').on('keyup', function () {
          const searchText = $(this).val().toLowerCase();
          const list = $(this).closest('.dropdown-menu').find('.company-list div');

          list.each(function () {
            const itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.includes(searchText));
          });
        });
        $('.vendor-inner-search').on('keyup', function () {
          const searchText = $(this).val().toLowerCase();
          const list = $(this).closest('.dropdown-menu').find('.vendor-list div');

          list.each(function () {
            const itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.includes(searchText));
          });
        });
        $(document).on('keyup', '.vendor-inner-search' ,function () {
          const searchText = $(this).val().toLowerCase();
          const list = $(this).closest('.dropdown-menu').find('.vendor-list div');

          list.each(function () {
            const itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.includes(searchText));
          });
        });

         $(document).on('click', function (e) {
            if (!$(e.target).closest('.tax-dropdown-wrapper').length) {
              $('.dropdown-menu.tax-dropdown').hide();
            }
          });
          $(document).on('click', '.dropdown-menu.tax-dropdown', function (e) {
                e.stopPropagation();
            });

    //     $(document).ready(function () {
    //       let filters = {
    //           date_from: '',
    //           date_to: '',
    //           zone_name: '',
    //           zone_id: '',
    //           branch_name: '',
    //           branch_id: '',
    //           company_id: '',
    //           company_name: '',
    //           vendor_name: '',
    //           vendor_id: '',
    //       };

    //       function renderSummary() {
    //           let summaryHtml = '';

    //           if (filters.date_from && filters.date_to) {
    //               summaryHtml += `<span class="filter-badge remove-icon" data-type="date">
    //                   ${filters.date_from} → ${filters.date_to}
    //               </span>`;
    //           }
    //           if (filters.zone_id) {
    //               summaryHtml += `<span class="filter-badge remove-icon" data-type="zone">
    //                   ${filters.zone_name}
    //               </span>`;
    //           }
    //           if (filters.branch_id) {
    //               summaryHtml += `<span class="filter-badge remove-icon" data-type="branch">
    //                   ${filters.branch_name}
    //               </span>`;
    //           }
    //           if (filters.company_id) {
    //               summaryHtml += `<span class="filter-badge remove-icon" data-type="company">
    //                   ${filters.company_name}
    //               </span>`;
    //           }
    //           if (filters.vendor_id) {
    //               summaryHtml += `<span class="filter-badge remove-icon" data-type="vendor">
    //                   ${filters.vendor_name}
    //               </span>`;
    //           }

    //           if (summaryHtml) {
    //               summaryHtml += `<span class="filter-badge filter-clear" id="clear-all">
    //                   Clear all
    //               </span>`;
    //           }
    //           $("#filter-summary").html(summaryHtml || "");
    //       }

    //        function loadVendor(page = 1, perPage = $('#per_page').val()) {
    //         $.ajax({
    //             url: '{{ route("superadmin.getvendor") }}',
    //             type: "GET",
    //             data: {
    //                 per_page: perPage,
    //                 page: page,
    //                 date_from: filters.date_from,
    //                 date_to: filters.date_to,
    //                 zone_id: filters.zone_id,
    //                 branch_id: filters.branch_id,
    //                 company_id: filters.company_id,
    //                 vendor_id: filters.vendor_id
    //             },
    //             success: function (data) {
    //                 $("#Neft-body").html(data);
    //                 renderSummary();
    //             }
    //         });
    //     }

    //       // Zone change
    //       $('.zone_id').on('click', function () {
    //           filters.zone_id = $(this).val();
    //           filters.zone_name = $('.zone-search-input').val();
    //           loadVendor();
    //       });

    //       // Branch change
    //       $('.branch_id').on('click', function () {
    //           filters.branch_id = $(this).val();
    //           filters.branch_name = $('.branch-search-input').val();
    //           loadVendor();
    //       });
    //       // company change
    //       $('.company_id').on('click', function () {
    //           filters.company_id = $(this).val();
    //           filters.company_name = $('.company-search-input').val();
    //           loadVendor();
    //       });
    //       // vendor change
    //       $('.vendor_id').on('click', function () {
    //           filters.vendor_id = $(this).val();
    //           filters.vendor_name = $('.vendor-search-input').val();
    //           loadVendor();
    //       });

    //       // Date change
    //       $('.data_values').on('change', function () {
    //           let dateRange = $(this).val();
    //           if (dateRange.includes('to')) {
    //               let parts = dateRange.split(' to ');
    //               filters.date_from = parts[0].trim();
    //               filters.date_to = parts[1].trim();
    //           }
    //           loadVendor();
    //       });

    //       // Remove single filter
    //       $("#filter-summary").on('click', '.remove-icon', function () {
    //           let type = $(this).closest('.filter-badge').data('type');
    //           if (type === 'date') {
    //               filters.date_from = '';
    //               filters.date_to = '';
    //               $('.data_values').val('');
    //           } else if (type === 'zone') {
    //               filters.zone_id = '';
    //               filters.zone_name = '';
    //               $('.zone_id').val('');
    //               $('.zone-search-input').val('');
    //           } else if (type === 'branch') {
    //               filters.branch_id = '';
    //               filters.branch_name = '';
    //               $('.branch_id').val('');
    //               $('.branch-search-input').val('');
    //           }else if(type === 'company'){
    //               filters.company_id = '';
    //               filters.company_name = '';
    //               $('.company_id').val('');
    //               $('.company-search-input').val('');
    //           }else if(type === 'vendor'){
    //               filters.vendor_name = '';
    //               filters.vendor_id = '';
    //               $('.vendor_id').val('');
    //               $('.vendor-search-input').val('');
    //           }
    //           loadVendor();
    //       });

    //       // Clear all filters
    //       $("#filter-summary").on('click', '#clear-all', function () {
    //           filters = {
    //               date_from: '',
    //               date_to: '',
    //               zone_name: '',
    //               zone_id: '',
    //               branch_name: '',
    //               branch_id: '',
    //               company_id: '',
    //               company_name: '',
    //               vendor_id: '',
    //               vendor_name: ''
    //           };
    //           $('.zone-search-input, .branch-search-input,.company-search-input,.vendor-search-input').val('');
    //           $('.zone_id, .branch_id,.company_id,.vendor_id').val('');
    //           $('.data_values').val('');
    //           loadVendor();
    //       });
    //       // Listen for pagination click
    //       $(document).on('click', '.pagination a', function (e) {
    //         e.preventDefault();

    //         let url = $(this).attr('href');
    //         let params = new URLSearchParams(url.split('?')[1]);
    //         let page = params.get('page') || 1; // Get page from query string
    //         let perPage = $('#per_page').val(); // Keep current per_page value

    //         loadVendor(page, perPage);
    //     });

    //       // Change per_page without reload
    //       $(document).on('change', '#per_page', function () {
    //           let perPage = $(this).val();
    //           loadVendor(1, perPage); // Always go to first page when per_page changes
    //       });
    //   });

      $(document).ready(function () {
        let savedFilters = null;
        if (sessionStorage.getItem("restore_filters")) {
            try {
                savedFilters = JSON.parse(sessionStorage.getItem("vendor_filters"));
            } catch (e) {
                savedFilters = null;
            }
           sessionStorage.removeItem("restore_filters");
        }
        let filters = savedFilters ||{
            vendor_name: '',
            vendor_id: '',
            status_filter: '',
            status_name: '',
            universal_search: '',
        };
        
        if (savedFilters) {
            filters = savedFilters;
        }

        loadVendor(
            sessionStorage.getItem("vendor_page") || 1
        );

    function renderSummary() {
        let summaryHtml = '';

        if (filters.vendor_id) {
            summaryHtml += `<span class="filter-badge remove-icon" data-type="vendor">
                <i class="bi bi-person me-1"></i>${filters.vendor_name} &times;
            </span>`;
        }
        if (filters.status_filter !== '') {
            summaryHtml += `<span class="filter-badge remove-icon" data-type="status">
                <i class="bi bi-toggle-on me-1"></i>${filters.status_name} &times;
            </span>`;
        }
        if (filters.universal_search) {
            summaryHtml += `<span class="filter-badge remove-icon" data-type="search">
                <i class="bi bi-search me-1"></i>${filters.universal_search} &times;
            </span>`;
        }

        if (summaryHtml) {
            summaryHtml += `<span class="filter-badge filter-clear" id="clear-all">Clear all</span>`;
        }

        $("#filter-summary").html(summaryHtml || "");
    }

    function loadVendor(page = 1, perPage = null) {
        if (!perPage) perPage = $('#per_page').val() || 10;
        $.ajax({
            url: '{{ route("superadmin.getvendor") }}',
            type: "GET",
            data: {
                per_page: perPage,
                page: page,
                vendor_id: filters.vendor_id,
                active_status: filters.status_filter,
                universal_search: filters.universal_search
            },
            success: function (data) {
                if (data && typeof data === 'object' && data.html !== undefined) {
                    $("#Neft-body").html(data.html);
                } else {
                    $("#Neft-body").html(data);
                }
                renderSummary();
            },
            error: function (xhr) {
                console.error('Vendor load error:', xhr.status, xhr.responseText);
            }
        });
    }
    // Saved filters
    if (savedFilters) {
        window.restoringFilters = true;

        filters = savedFilters;
        console.log("savedFilterslower",savedFilters);
        
        $('.vendor-search-input').val(filters.vendor_name);
        $('.universal_search').val(filters.universal_search || '');
        $('.status-search-input').val(filters.status_name || '');

        const savedPage = sessionStorage.getItem('vendor_page') || 1;

        setTimeout(function() {
            loadVendor(savedPage, $('#per_page').val());
        }, 200);
    }

    // =================== MULTI-SELECT CHANGE LISTENER ===================
    function setupMultiSelect(selectorInput, selectorHidden) {
        $(document).on('click', selectorHidden, function () {
            const selectedIds = $(this).val();
            const selectedText = $(selectorInput).val();

            if (selectorHidden === '.vendor_id') {
                filters.vendor_id = selectedIds;
                filters.vendor_name = selectedText;
            } else if (selectorHidden === '.status_filter') {
                filters.status_filter = selectedIds;
                filters.status_name = selectedText;
            }
            loadVendor();
        });
    }

    $('.vendor_id').on('click', function () {
      setupMultiSelect('.vendor-search-input', '.vendor_id');
    });
    $('.status_filter').on('click', function () {
      setupMultiSelect('.status-search-input', '.status_filter');
    });
    $('.universal_search').on('keyup', function () {
      filters.universal_search = $('.universal_search').val();
        loadVendor();
    });

    // Remove single filter
    $("#filter-summary").on('click', '.remove-icon', function () {
        let type = $(this).data('type');
        if (type === 'vendor') {
            filters.vendor_id = '';
            filters.vendor_name = '';
            $('.vendor_id').val('');
            $('.vendor-search-input').val('');
            $('.vendor-list div').removeClass('selected');
        } else if (type === 'status') {
            filters.status_filter = '';
            filters.status_name = '';
            $('.status_filter').val('');
            $('.status-search-input').val('');
            $('.status-list div').removeClass('selected');
        } else if (type === 'search') {
            filters.universal_search = '';
            $('.universal_search').val('');
        }
        loadVendor();
    });

    // Clear all filters
    $("#filter-summary").on('click', '#clear-all', function () {
        filters = { vendor_name: '', vendor_id: '', status_filter: '', status_name: '', universal_search: '' };
        $('.vendor-search-input, .status-search-input').val('');
        $('.vendor_id, .status_filter').val('');
        $('.universal_search').val('');
        $('.dropdown-list div').removeClass('selected');
        loadVendor();
    });

    // Pagination
    $(document).on('click', '#Neft-body .pagination a', function (e) {
        e.preventDefault();
        const href = $(this).attr('href') || '';
        const qs = href.includes('?') ? href.split('?')[1] : '';
        const page = qs ? (new URLSearchParams(qs).get('page') || 1) : 1;
        const perPage = $('#per_page').val() || 10;
        loadVendor(page, perPage);
    });

    // Change per_page
    $(document).on('change', '#Neft-body #per_page', function () {
        loadVendor(1, $(this).val());
    });
});
});
</script>
@if(!empty($_GET['id']))
<script>
$(document).ready(function() {
    let vendorId = "{{ request()->get('id') }}";
    let perPage = {{ $vendor->perPage() }};
    let currentPage = {{ $vendor->currentPage() }};
    console.log("vendorId:", vendorId);
    console.log("perPage:", perPage);
    console.log("currentPage:", currentPage);

    if (!vendorId) return;

    // Check if vendor is on current page
    let row = $('.customer-row[data-id="' + vendorId + '"]');
    if (row.length) {
        row.trigger('click'); // open modal immediately
        return;
    }

    // Vendor not on current page, find its index (position) from server-side
    let vendorIndex = null;
    @foreach ($allVendors as $i => $v)

        if ({{ $v->id }} == vendorId) {
            vendorIndex = {{ $i + 1 }};
        }
    @endforeach

    if (!vendorIndex) {
        console.warn("Vendor not found in dataset");
        return;
    }

    let targetPage = Math.ceil(vendorIndex / perPage);
    console.log("Vendor index:", vendorIndex, "Target page:", targetPage);

    if (currentPage != targetPage) {
        // redirect directly to target page with id
        let newUrl = new URL(window.location.href);
        newUrl.searchParams.set('page', targetPage);
        newUrl.searchParams.set('id', vendorId);
        window.location.href = newUrl.toString();
    }
});
</script>
@endif

    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>