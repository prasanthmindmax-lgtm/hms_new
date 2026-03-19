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
.neft-confirmation-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.summary-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 20px;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
}

.status-badge.new {
  background-color: #ffeb3b;
  color: #333;
}

.status-badge.neft {
  background-color: #4caf50;
  color: white;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.detail-item label {
  display: block;
  font-weight: 600;
  color: #555;
  margin-bottom: 5px;
}

.detail-item p {
  margin: 0;
  padding: 8px 12px;
  background: #f5f5f5;
  border-radius: 4px;
}

.amount-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 30px;
}

.amount-table th, .amount-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.amount-table th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.amount-table .total {
  font-weight: bold;
  color: #2e7d32;
}

.documents-section {
  margin-bottom: 30px;
}

.document-category {
  margin-bottom: 20px;
}

.document-preview {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  margin-top: 10px;
}

.file-thumbnail {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  background: #f5f5f5;
  border-radius: 4px;
  width: 250px;
}

.file-icon {
  width: 24px;
  height: 24px;
  margin-right: 10px;
  background-color: #ddd;
}

.file-icon.pdf {
  background-color: #f44336;
}

.file-icon.image {
  background-color: #2196f3;
}

.view-btn {
  margin-left: auto;
  background: none;
  border: none;
  color: #2196f3;
  cursor: pointer;
}

.action-buttons {
  display: flex;
  gap: 15px;
  margin-top: 30px;
}

.btn {
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
}

.btn.primary {
  background-color: #2196f3;
  color: white;
  border: none;
}

.btn.secondary {
  background-color: #4caf50;
  color: white;
  border: none;
}

.btn.outline {
  background: none;
  border: 1px solid #2196f3;
  color: #2196f3;
}
.preview-card {
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 12px;
  margin: 10px;
  width: 150px;
  text-align: center;
  font-size: 12px;
  overflow-wrap: break-word;
  cursor: pointer;
}
.preview-card img {
  max-width: 100%;
  max-height: 100px;
  object-fit: cover;
  margin-bottom: 8px;
}
.preview-card a {
  text-decoration: none;
  color: #0056b3;
  font-weight: 500;
}
#documentModal1{
  z-index: 999999;
}
.document-preview-bank ,.document-preview-bill{
  display: flex;
}
.document-preview-neft{
  display: flex;
  flex-wrap: wrap;
}
.file_name{
  display: -webkit-box;
  -webkit-line-clamp: 1;       /* Limit to 1 line */
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
 .btn-check {
        background-color: #ffc107;
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 5px;
        font-size: 14px;
    }

    .btn-approve {
        background-color: #28a745;
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 5px;
        font-size: 14px;
    }
    .ellipsis {
  cursor: pointer;
  padding: 10px 13px;
  margin-left: 5px;
}

.btn-group {
  display: flex;
  align-items: center;
}

/* .dropdown-menu {
  min-width: 120px;
} */
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
                <!-- Header -->
                <div class="qd-header">
                    <div class="qd-header-title">
                        <span class="qd-header-icon"><i class="bi bi-send-fill"></i></span>
                        NEFT Dashboard
                    </div>
                    <div class="qd-header-actions">
                        <div class="btn-group">
                            <a href="#" class="btn btn-primary btn-sm" style="border-radius:6px 0 0 6px;">+ New</a>
                            <div class="dropdown">
                                <span class="btn btn-sm btn-secondary dropdown-toggle ellipsis" data-bs-toggle="dropdown" aria-expanded="false">⋮</span>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item export-btn" href="#" data-format="xlsx"><i class="bi bi-file-earmark-excel me-1"></i>Export XLSX</a></li>
                                    <li><a class="dropdown-item export-btn" href="#" data-format="csv"><i class="bi bi-file-earmark-text me-1"></i>Export CSV</a></li>
                                </ul>
                            </div>
                        </div>
                        <button class="btn btn-sm qd-toggle-btn" id="toggleStats" title="Toggle Stats">
                            <i class="bi bi-bar-chart-line me-1"></i>Stats
                            <i class="bi bi-chevron-up qd-toggle-icon" id="statsChevron"></i>
                        </button>
                        <button class="btn btn-sm qd-toggle-btn" id="toggleFilters" title="Toggle Filters">
                            <i class="bi bi-funnel me-1"></i>Filter
                            <i class="bi bi-chevron-up qd-toggle-icon" id="filtersChevron"></i>
                        </button>
                    </div>
                </div>

                <!-- Stats -->
                <div class="qd-stats" id="statsSection">
                    <div class="qd-stat-card qd-stat-blue qd-stat-active" data-stat-filter="" title="Show all">
                        <div class="qd-stat-icon"><i class="bi bi-send-fill"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Total NEFT</div>
                            <div class="qd-stat-value" id="stat-total">{{ $stats['total'] ?? 0 }}</div>
                            <div class="qd-stat-sub" id="stat-total-amt">₹{{ number_format($stats['total_amount'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-orange" data-stat-filter="Checking Status" title="Filter: Checking Status">
                        <div class="qd-stat-icon"><i class="bi bi-search"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Checked</div>
                            <div class="qd-stat-value" id="stat-checked">{{ $stats['checked'] ?? 0 }}</div>
                            <div class="qd-stat-sub">&nbsp;</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-green" data-stat-filter="Approver Status" title="Filter: Approved">
                        <div class="qd-stat-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Approved</div>
                            <div class="qd-stat-value" id="stat-approved">{{ $stats['approved'] ?? 0 }}</div>
                            <div class="qd-stat-sub">&nbsp;</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-red" data-stat-filter="pending" title="Filter: Pending">
                        <div class="qd-stat-icon"><i class="bi bi-clock"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Pending Check</div>
                            <div class="qd-stat-value" id="stat-pending">{{ $stats['pending'] ?? 0 }}</div>
                            <div class="qd-stat-sub">&nbsp;</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-purple" data-stat-filter="Success" title="Filter: Success">
                        <div class="qd-stat-icon"><i class="bi bi-check2-all"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Success</div>
                            <div class="qd-stat-value" id="stat-success">{{ $stats['success'] ?? 0 }}</div>
                            <div class="qd-stat-sub" id="stat-success-amt">₹{{ number_format($stats['success_amount'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="qd-filters" id="filtersSection">
                    <div class="qd-filter-row">
                        <div class="qd-filter-group">
                            <label class="qd-filter-label">Date</label>
                            <div id="reportrange" style="background:#fff;cursor:pointer;padding:8px 10px;border:1px solid #dde1ef;border-radius:6px;min-width:170px;">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span id="data_values"></span> <i class="fa fa-caret-down"></i>
                                <input type="hidden" class="data_values">
                            </div>
                        </div>
                        <div class="qd-filter-group tax-dropdown-wrapper company-section">
                            <label class="qd-filter-label">Company</label>
                            <input type="text" class="form-control company-search-input dropdown-search-input" placeholder="Select Company" readonly>
                            <input type="hidden" name="company_id" class="company_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Company..."></div>
                                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap:10px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                                </div>
                                <div class="dropdown-list multiselect company-list"></div>
                            </div>
                        </div>
                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label class="qd-filter-label">State</label>
                            <input type="text" class="form-control state-search-input dropdown-search-input" placeholder="Search State..." readonly>
                            <input type="hidden" name="state_id" class="state_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search State..."></div>
                                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap:10px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                                </div>
                                <div class="dropdown-list multiselect state-list">
                                    <div data-value="Tamil Nadu" data-id="1">Tamil Nadu</div>
                                    <div data-value="Karnataka" data-id="2">Karnataka</div>
                                    <div data-value="Kerala" data-id="3">Kerala</div>
                                    <div data-value="Andra Pradesh" data-id="3">Andra Pradesh</div>
                                    <div data-value="International" data-id="4">International</div>
                                </div>
                            </div>
                        </div>
                        <div class="qd-filter-group tax-dropdown-wrapper zone-section">
                            <label class="qd-filter-label">Zone</label>
                            <input type="text" class="form-control zone-search-input dropdown-search-input" placeholder="Select Zone" readonly>
                            <input type="hidden" name="zone_id" class="zone_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Zone..."></div>
                                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap:10px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                                </div>
                                <div class="dropdown-list multiselect zone-list"></div>
                            </div>
                        </div>
                      </div>
                      <div class="qd-filter-row">
                        <div class="qd-filter-group tax-dropdown-wrapper branch-section">
                            <label class="qd-filter-label">Branch</label>
                            <input type="text" class="form-control branch-search-input dropdown-search-input" placeholder="Select Branch" readonly>
                            <input type="hidden" name="branch_id" class="branch_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Branch..."></div>
                                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap:10px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                                </div>
                                <div class="dropdown-list multiselect branch-list"></div>
                            </div>
                        </div>
                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label class="qd-filter-label">Vendor</label>
                            <input type="text" class="form-control vendor-search-input dropdown-search-input" placeholder="Select Vendor" readonly>
                            <input type="hidden" name="vendor_id" class="vendor_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Vendor..."></div>
                                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap:10px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                                </div>
                                <div class="dropdown-list multiselect vendor-list"></div>
                            </div>
                        </div>
                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label class="qd-filter-label">Nature of Payment</label>
                            <input type="text" class="form-control nature-search-input dropdown-search-input" placeholder="Nature of Payment..." readonly>
                            <input type="hidden" name="nature_id" class="nature_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Nature..."></div>
                                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap:10px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                                </div>
                                <div class="dropdown-list multiselect account-list"></div>
                            </div>
                        </div>
                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label class="qd-filter-label">Status</label>
                            <input type="text" class="form-control status-search-input dropdown-search-input" placeholder="Search Status..." readonly>
                            <input type="hidden" name="status_id" class="status_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Status..."></div>
                                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap:10px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                                </div>
                                <div class="dropdown-list multiselect status-list">
                                    <div data-value="Checking Status" data-id="1">Checking Status</div>
                                    <div data-value="Approver Status" data-id="2">Approver Status</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search row -->
                <!-- <div class="qd-search-row" id="searchRow">
                    <input type="text" class="form-control universal_search qd-search-input" placeholder="&#128269; Search serial no, vendor, zone, UTR...">
                  </div> -->
                  <div class="qd-search-row">
                    <div class="qd-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" class="universal_search" placeholder="Search serial no, vendor, zone, UTR...">
                    </div>
                </div>
                  <div class="qd-applied-bar">
                      <span style="font-size:12px;color:#7b8ab0;font-weight:600;">Applied:</span>
                      <div id="filter-summary"></div>
                  </div>

                <!-- Table -->
                <div class="qd-table-wrap">
                    <div id="Neft-body">
                        @include('vendor.partials.table.neft_rows',['purchaselist' => $purchaselist, 'perPage' => $perPage, 'limit_access' => $limit_access])
                    </div>
                </div>
            </div>

            @if(isset($limit_access) && $limit_access == 1)
            <!-- Edit History Popup -->
            <div class="qdt-history-popup" id="neftHistoryPopup" style="display:none;">
                <div class="qdt-history-header">
                    <span id="neftHistoryTitle">Edit History</span>
                    <button class="qdt-history-close" id="closeNeftHistory">&times;</button>
                </div>
                <div class="qdt-history-body" id="neftHistoryBody"></div>
            </div>
            <div class="qdt-history-overlay" id="neftHistoryOverlay" style="display:none;"></div>
            @endif

            <!-- Bill Detail Modal -->
            <div class="zoho-modal" id="billDetailModal">
              <div class="zoho-modal-content">
                <div class="zoho-modal-header">
                  <div class="zoho-modal-title"> <h2>NEFT Payment Confirmation</h2></div>
                  <div class="zoho-modal-actions">
                    <button class="zoho-btn zoho-btn-primary edit-btn">
                      <i class="bi bi-pencil"></i> Edit
                    </button>
                    {{-- <button class="zoho-btn zoho-btn-primary print-btn">
                      <i class="bi bi-pencil"></i> Print
                    </button>
                    <button class="zoho-btn zoho-btn-primary pdf-btn">
                      <i class="bi bi-pencil"></i>  PDF
                    </button> --}}
                    <button class="zoho-btn zoho-btn-icon close-modal">
                      <i class="bi bi-x-lg"></i>
                    </button>
                  </div>
                </div>

                <div class="zoho-modal-body">
                  <div class="neft-confirmation-container">
                    <div class="payment-summary">
                      <div class="summary-header">
                        <h3>Payment Details</h3>
                        <span class="status-badge new">NEW</span>
                        <span class="status-badge neft">NEFT</span>
                      </div>

                      <div class="details-grid">
                        <div class="detail-item">
                          <label>Created By:</label>
                          <p id="created_by_view"></p>
                        </div>

                        <div class="detail-item">
                          <label>Vendor/Employee:</label>
                          <p id="vendor_name_view"></p>
                        </div>

                        <div class="detail-item">
                          <label>PAN Number:</label>
                          <p id="pan_number_view"></p>
                        </div>

                        <div class="detail-item">
                          <label>Account Number:</label>
                          <p id="account_number_view"></p>
                        </div>

                        <div class="detail-item">
                          <label>IFSC Code:</label>
                          <p id="ifsc_code_view"></p>
                        </div>

                        <div class="detail-item">
                          <label>Payment Method:</label>
                          <p id="payment_methods_view"></p>
                        </div>
                      </div>
                    </div>

                    <div class="amount-breakdown">
                      <h3>Amount Breakdown</h3>
                      <table class="amount-table">
                        <thead>
                          <tr>
                            <th>Invoice Amount</th>
                            <th>Paid</th>
                            <th>TDS/Commission</th>
                            <th>Tax Amount</th>
                            {{-- <th>GST Tax</th>
                            <th>GST Amount</th> --}}
                            <th>Balance</th>
                          </tr>
                        </thead>
                        <tbody id="tablerow">
                          <tr>
                            <td>₹25,37,499</td>
                            <td>₹10,00,000</td>
                            <td>2% (₹50,749)</td>
                            <td>₹50,749</td>
                            <td class="total">₹25,62,894</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <div class="documents-section">
                      <h3>Attached Documents</h3>
                      <div class="document-category">
                        <div class="document-preview-bank">

                        </div>
                      </div>

                      <div class="document-category">
                        <h4>Bill Made Upload</h4>
                        <div class="document-preview-bill">

                        </div>
                      </div>

                      <div class="document-category">
                        <h4>NEFT Documents</h4>
                        <div class="document-preview-neft">

                        </div>
                      </div>
                    </div>

                    {{-- <div class="action-buttons">
                      <button class="btn primary">Print Confirmation</button>
                      <button class="btn secondary">Download as PDF</button>
                      <button class="btn outline">Back to Dashboard</button>
                    </div> --}}
                  </div>
                </div>
              </div>
            </div>

            <div class="zoho-modal-overlay" id="modalOverlay"></div>

            <div id="NEFTModal" class="tds-modal">
              <div class="tds-modal-content modal-sm">
                <div class="tds-modal-header">
                  <h4>NEFT Payment</h4>
                  <span class="close-modal-tcs">&times;</span>
                </div>

                <div class="tds-modal-body">
                  <div class="container mt-4">
                    <form id="neftForm" method="POST" action="">
                      @csrf
                        <div class="row">

                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">SI. No</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;"  class="error_serial errorss"></span>
                                        <input type="hidden" id="id" name="id">
                                        <input type="hidden" id="branch_id" name="branch_id" value="{{ $admin?->branch_id  }}">
                                        <input type="hidden" id="users_id" name="users_id" value="{{ $admin?->id}}">
                                        <input type="hidden" id="bill_pay_id" name="bill_pay_id">
                                        <input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px"  required name="serial_number" id="serial_number" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Created by:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_created_by errorss"></span>
                                    <input type="text" class="form-control" id="created_by" name="created_by" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Created by" readonly value="{{ $admin?->user_fullname ?? '' }}" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <label class="form-label required" style="font-size: 12px;font-weight: 600;">Vendor/ Employee Name :</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_special errorss"></span>
                                        <div class="col-md-12">
                                        <div class="search-dropdown">
                                            <input type="text" id="vendor-search" class="form-control search-input" name="vendor_name" readonly placeholder="Search vendor..." autocomplete="off">
                                            <div class="dropdown-menu" id="vendor-dropdown">
                                                <div class="search-box">
                                                    <input type="text" placeholder="Search" class="inner-search form-control mb-2">
                                                </div>
                                                <div class="vendor-list"></div>
                                                <div class="new-vendor-option">
                                                    <div class="vendor-item new-vendor">
                                                        <div class="vendor-name"><i class="fa fa-plus"></i> New Vendor</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" id="selected-vendor-id" name="vendor_id">
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Nature of Payment:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_nature_payment errorss"></span>
                                     <input type="text" class="form-control" id="nature_payment" name="nature_payment" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Nature of Payment" >
                                    {{-- <select class="form-control" id="nature_payment" name="nature_payment" style="height: 42px;">
                                        <option value="">Select Status</option>
                                        <option value="Travell Allowance" >Travell Allowance</option>
                                        <option value="Expense" >Expense</option>
                                        <option value="Imprest" >Imprest</option>

                                    </select> --}}
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">UTR Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_utr_number errorss"></span>
                                    <input type="text" class="form-control" id="utr_number" name="utr_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="UTR Number" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">PAN Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_pan_number errorss"></span>
                                    <input type="text" class="form-control" id="pan_number" name="pan_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="PAN Number">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Account Number:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_account_number errorss"></span>
                                    <input type="text" class="form-control" id="account_number" name="account_number" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Account Number">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">IFSC Code:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_ifsc_code errorss"></span>
                                    <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="IFSC Code">
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Company</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_utr_number errorss"></span>
                                    <input type="text" class="form-control" id="company_name" name="company_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Company" >
                                    <input type="hidden" class="form-control" id="company_id" name="company_id" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Zone</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_utr_number errorss"></span>
                                    <input type="text" class="form-control" id="zone_name" name="zone_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Zone" >
                                    <input type="hidden" class="form-control" id="zone_id" name="zone_id" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Company</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_utr_number errorss"></span>
                                    <input type="text" class="form-control" id="branch_name" name="branch_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Branch" >
                                    <input type="hidden" class="form-control" id="branch_id" name="branch_id" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <h4>Account </h4>
                            <table class="table" style="width: 100%;">
                                <thead>
                                  <tr>
                                    <th style="width: 20%;">Invoice Amount</th>
                                    <th style="width: 20%;">TDS</th>
                                    <th style="width: 20%;">Tax Amount</th>
                                    <th style="width: 20%;">Paid</th>
                                    <th style="width: 20%;">Balance</th>
                                  </tr>
                                </thead>
                                <tbody id="account_table">

                                </tbody>
                              </table>
                        </div>

                        <div class="row">

                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Payment Status:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_payment_status errorss"></span>
                                    <select class="form-control" id="payment_status" name="payment_status" style="height: 42px;">
                                        <option value="">Select Status</option>
                                        <option value="Success" >Success</option>
                                        <option value="Failed" >Failed</option>
                                        <option value="Return" >Return</option>
                                        <!-- <option value="NEFT">NEFT</option>
                                        <option value="RTGS">RTGS</option>
                                        <option value="IDR">IDR</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="DD">DD</option>
                                        <option value="Internet Banking">Internet Banking</option>
                                        <option value="Card Swipe">Card Swipe</option> -->
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Payment Method:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_payment_method errorss"></span>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="payment_neft" name="payment_method[]" value="NEFT">
                                                <label class="form-check-label" for="payment_neft" style="font-size: 12px;">NEFT</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="payment_rtgs" name="payment_method[]" value="RTGS">
                                                <label class="form-check-label" for="payment_rtgs" style="font-size: 12px;">RTGS</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="payment_cheque" name="payment_method[]" value="Cheque">
                                                <label class="form-check-label" for="payment_cheque" style="font-size: 12px;">Cheque</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="payment_dd" name="payment_method[]" value="DD">
                                                <label class="form-check-label" for="payment_dd" style="font-size: 12px;">DD</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="payment_idhs" name="payment_method[]" value="IDhS">
                                                <label class="form-check-label" for="payment_idhs" style="font-size: 12px;">IDhS</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="payment_internet" name="payment_method[]" value="Internet Banking">
                                                <label class="form-check-label" for="payment_internet" style="font-size: 12px;">Internet Banking</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="payment_card" name="payment_method[]" value="Card Swipe">
                                                <label class="form-check-label" for="payment_card" style="font-size: 12px;">Card Swipe</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                        <!-- Bank Upload -->
                        <div class="col-sm-4 mb-4 bank_uploads">
                            <label class="form-label">Bank Proof</label><br>
                            <label class="upload-btn" for="bank_upload">
                            <i class="fas fa-upload"></i> Upload File
                            </label>
                            <input type="file" id="bank_upload" name="bank_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                            <ul id="preview_bank_upload" style="list-style: none; padding-left: 0;"></ul>
                        </div>

                        <!-- Invoice Upload -->
                        <div class="col-sm-4 mb-4">
                            <label class="form-label">Invoice Upload</label><br>
                            <label class="upload-btn" for="invoice_upload">
                            <i class="fas fa-upload"></i> Upload File
                            </label>
                            <input type="file" id="invoice_upload" name="invoice_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                            <ul id="preview_invoice_upload" style="list-style: none; padding-left: 0;"></ul>
                        </div>

                        <!-- PAN Upload -->
                        <div class="col-sm-4 mb-4 pan_upload">
                            <label class="form-label">PAN Upload</label><br>
                            <label class="upload-btn" for="pan_upload">
                            <i class="fas fa-upload"></i> Upload File
                            </label>
                            <input type="file" id="pan_upload" name="pan_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                            <ul id="preview_pan_upload" style="list-style: none; padding-left: 0;"></ul>
                        </div>

                        <!-- PO Upload -->
                        <div class="col-sm-4 mb-4">
                            <label class="form-label">PO Attachment</label><br>
                            <label class="upload-btn" for="po_upload">
                            <i class="fas fa-upload"></i> Upload File
                            </label>
                            <input type="file" id="po_upload" name="po_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                            <ul id="preview_po_upload" style="list-style: none; padding-left: 0;"></ul>
                        </div>

                        <!-- PO Signed Copy Upload -->
                        <div class="col-sm-4 mb-4">
                            <label class="form-label">PO Signed Copy Upload</label><br>
                            <label class="upload-btn" for="po_signed_upload">
                            <i class="fas fa-upload"></i> Upload File
                            </label>
                            <input type="file" id="po_signed_upload" name="po_signed_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                            <ul id="preview_po_signed_upload" style="list-style: none; padding-left: 0;"></ul>
                        </div>

                        <!-- PO Delivery Copy Upload -->
                        <div class="col-sm-4 mb-4">
                            <label class="form-label">PO Delivery Copy Upload</label><br>
                            <label class="upload-btn" for="po_delivery_upload">
                            <i class="fas fa-upload"></i> Upload File
                            </label>
                            <input type="file" id="po_delivery_upload" name="po_delivery_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
                            <ul id="preview_po_delivery_upload" style="list-style: none; padding-left: 0;"></ul>
                        </div>
                        </div>

                        <div class="modal-footer" style="gap: 20px">
                            <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close_button" class="btn btn-outline-danger close-modal-tcs" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="submit-neft-datas" style="height: 34px;width: 133px;font-size: 12px;background-color: #6a6ee4;--bs-btn-border-color: #6a6ee4;" class="btn btn-primary">Submit</button>
                        </div>

                      </form>

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
              <!-- Modal -->
              <!-- Place this near the end of <body> -->
              <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document" style="max-width:90%;">
                  <div class="modal-content">
                    <div class="modal-header position-relative">
                      <h5 class="modal-title">Bill Preview</h5>

                      <!-- Custom fallback close button (always shown, at right) -->
                      <button type="button"
                              class="modal-close-fallback"
                              aria-label="Close preview"
                              style="position:absolute; right:1rem; top:0.6rem; font-size:1.4rem; background:none; border:0;">
                        &times;
                      </button>
                    </div>

                    <div class="modal-body p-0">
                      <iframe id="pdfFrame" src="" width="100%" height="600px" style="border:none;"></iframe>
                    </div>
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
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

@if ($errors->any())
<script>
    $(document).ready(function () {
        $('#exampleModal').modal('show');
    });
</script>
@endif

<script>
  $(document).ready(function () {
        $('#per_page').on('change', function () {
            $('#perPageForm').submit();
        });
    });
   toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
$(document).ready(function () {
    $(document).on('click', '.check', function (e) {
      e.preventDefault();
      const neft_id = $(this).data('id');

      $.ajax({
          url: '{{ route("superadmin.CheckerAndApprover") }}',
          method: "GET",
          data: { id: neft_id },
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
              toastr.success(response.message);
               setTimeout(() => {
                    window.location.reload();
                }, 2000);
          },
          error: function (xhr) {
              console.error("Error:", xhr);
              toastr.error("Error occurred while checking/approving");
          }
      });
  });
    $(document).on('click', '.approver', function (e) {
      e.preventDefault();
      const approver_id = $(this).data('id');

      $.ajax({
          url: '{{ route("superadmin.CheckerAndApprover") }}',
          method: "GET",
          data: { approver_id: approver_id },
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
              toastr.success(response.message);
               setTimeout(() => {
                    window.location.reload();
                }, 2000);
          },
          error: function (xhr) {
              console.error("Error:", xhr);
              toastr.error("Error occurred while checking/approving");
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

      if (selectedIds.length === 0) {
        alert('Please select at least one record to export.');
        return;
      }
      console.log("format",format);
      console.log("selectedIds",selectedIds);

      $.ajax({
        url: '{{ route("superadmin.exportExcel") }}',
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
          a.download = `NEFT.${format}`;
          document.body.appendChild(a);
          a.click();
          a.remove();
          window.URL.revokeObjectURL(url);
        }
      });
    });
    $(document).on('click', '.print-pop-btn', function () {
      let billId = $(this).data('id');
      $.ajax({
          url: '{{ route("superadmin.getbillprint") }}',
          method: "GET",
          data: { id: billId },
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          xhrFields: {
              responseType: 'blob'
          },
          success: function (response) {
            var blob = new Blob([response], {type: 'application/pdf'});
            var url = URL.createObjectURL(blob);

            // Inject into iframe instead of opening new tab
            $('#pdfFrame').attr('src', url);

            // Show modal
            $('#pdfPreviewModal').modal('show');
        },
          error: function (xhr) {
              console.error("Error:", xhr);
              toastr.error("Error occurred while printing");
          }
      });
    });
    $(document).on('click', '.print-po-pop-btn', function () {
      let billId = $(this).data('id');

      $.ajax({
          url: '{{ route("superadmin.getpurchaseprint") }}',
          method: "GET",
          data: { id: billId },
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          xhrFields: {
              responseType: 'blob' // This tells jQuery to handle binary data
          },
          success: function (response) {
              var blob = new Blob([response], {type: 'application/pdf'});
              var url = URL.createObjectURL(blob);

              // Inject into iframe instead of opening new tab
              $('#pdfFrame').attr('src', url);

              // Show modal
              $('#pdfPreviewModal').modal('show');
          },
          error: function (xhr) {
              console.error("Error:", xhr);
              toastr.error("Error occurred while printing");
          }
      });
    });
    $(document).on('click', '.print-quot-pop-btn', function () {
      let billId = $(this).data('id');
      $.ajax({
          url: '{{ route("superadmin.getquotationprint") }}',
          method: "GET",
          data: { id: billId },
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          xhrFields: {
              responseType: 'blob' // This tells jQuery to handle binary data
          },
          success: function (response) {
            var blob = new Blob([response], {type: 'application/pdf'});
            var url = URL.createObjectURL(blob);

            // Inject into iframe instead of opening new tab
            $('#pdfFrame').attr('src', url);

            // Show modal
            $('#pdfPreviewModal').modal('show');
        },
          error: function (xhr) {
              console.error("Error:", xhr);
              toastr.error("Error occurred while printing");
          }
      });
  });
  $(document).on('click', '.modal-close-fallback', function(e){
      e.preventDefault();

      // Bootstrap 5: try to get or create the Modal instance and hide it
      try {
        if (typeof bootstrap !== 'undefined') {
          var modalEl = document.getElementById('pdfPreviewModal');
          var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
          bsModal.hide();
          return;
        }
      } catch(err){ /* ignore */ }

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

    // Handle row click to show modal with bill details
    $(document).on('click','.customer-row', function (e) {
        // Don't trigger if clicking on checkbox
        if ($(e.target).is('input[type="checkbox"]')) {
            return;
        }
        if ($(e.target).is('.check')) {
            return;
        }
        if ($(e.target).is('.approver')) {
            return;
        }
        if ($(e.target).closest('.vendor_link').length) return;
        if ($(e.target).closest('.print-pop-btn').length) return;
        if ($(e.target).closest('.print-po-pop-btn').length) return;
        if ($(e.target).closest('.print-quot-pop-btn').length) return;
        const billData = {
            items: $(this).data('items')
        };

        console.log(billData,"billData");

        // Show loading state
        $('#billDetailModal').addClass('show loading');
        $('#modalOverlay').addClass('show');
        $('body').css('overflow', 'hidden');

        // Populate modal with data
        populateModal(billData);

        // Remove loading class after a short delay (simulating data load)
        setTimeout(function() {
            $('#billDetailModal').removeClass('loading');
        }, 300);
    });

    // Close modal handler for button and overlay
    $(document).on('click', '.close-modal, #modalOverlay', function (e) {
        e.stopPropagation();
        closeModal();
    });

    // Handle keyboard escape key to close modal
    $(document).on('keyup', function (e) {
        if (e.key === "Escape") {
            closeModal();
        }
    });

    // PDF button handler
    $(document).on('click', '.pdf-btn', function () {
        const billNumber = $('#bill-number').text();
        alert(`Generating PDF for bill ${billNumber}`);
        // window.open(`/bills/pdf/${billNumber}`, '_blank');
    });

    // Edit button handler
    $(document).on('click', '.edit-btn', function () {
        $('#NEFTModal').fadeIn();

    });

        // To close modal
        $('.close-modal-tcs').on('click', function () {
        $('#NEFTModal').fadeOut(); // or use .hide()
        });



    // Function to close modal
    function closeModal() {
        $('#billDetailModal').removeClass('show');
        $('#modalOverlay').removeClass('show');
        $('body').css('overflow', 'auto');
    }

    // Function to populate modal with data
    function populateModal(data) {
        $('#billDetailModal').data('bill-id', data.id);
        $('#created_by_view').text(data.items.created_by);
        $('#vendor_name_view').text(data.items.vendor);
        $('#pan_number_view').text(data.items.pan_number);
        $('#account_number_view').text(data.items.account_number);
        $('#ifsc_code_view').text(data.items.ifsc_code);
        $('#payment_methods_view').text(data.items.payment_method);
        let tablerow = '';
        console.log("data",data);
        let lines=data.items.bill_lines;

        if (Array.isArray(lines)) {
           lines.forEach(element => {
            const invoiceAmount = element.invoice_amount || 0;
            const alreadyPaid = element.already_paid || 0;

            // Safely handle tds_tax_name
            const tds_tax_name = element.tds_tax_name || '';
            const tds_matches = tds_tax_name.match(/\[(.*?)\]/);
            const tds_percent = tds_matches ? tds_matches[1] : '0%';

            // Safely handle gst_name
            const gst_name = element.gst_name || '';
            const gst_matches = gst_name.match(/\[(.*?)\]/);
            const gst_percent = gst_matches ? gst_matches[1] : '0%';

            const tax_amount = element.tax_amount || 0;
            const onlyPayable = element.only_payable || 0;
            const gst_amount = element.gst_amount || 0;

            tablerow += `
              <tr>
                <td>₹${invoiceAmount.toLocaleString()}</td>
                <td>₹${alreadyPaid.toLocaleString()}</td>
                <td>${tds_percent} (₹${tax_amount.toLocaleString()})</td>
                <td>₹${tax_amount.toLocaleString()}</td>
                <td class="total">₹${onlyPayable.toLocaleString()}</td>
              </tr>`;
          });

          $('#tablerow').html(tablerow);
        } else {
          console.warn('bill_lines is missing or not an array:', data.bill_lines);
        }

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

            // const previewHtml = `
            //   <div>
            //     <h5>${title}</h5>
            //     <div class="preview-card col-sm-3 documentclk" data-filetype="${fileType}" data-files='${fileArray}'>
            //       <img src="${iconUrl}" alt="${extension === 'pdf' ? 'PDF' : 'File'}" style="height:60px;">
            //       <div class="file_name">${filename}</div>
            //     </div>
            //   </div>
            // `;
            const previewHtml = `
              <div>
                <h5>${title}</h5>
                  <div class="preview-card col-sm-3 documentclk"
                      data-filetype="${fileType}"
                      data-files="${fileArray.replace(/"/g, '&quot;')}">
                      <img src="${iconUrl}" alt="${extension === 'pdf' ? 'PDF' : 'File'}" style="height:60px;">
                      <div>${filename.replace(/'/g, "&#39;").replace(/"/g, "&quot;")}</div>
                  </div>
                </div>
              `;

            $(containerId).append(previewHtml);
          }

          // Clear containers
          $('.document-preview-bill, .document-preview-bank, .document-preview-neft').empty();
            // Parse the JSON data
        // let pan_upload = [];
        // let bank_upload = [];
          const documents = JSON.parse(data.items.tblbillpay.documents);
          // if (data.items.tblvendor.pan_upload !== "") {
          //     pan_upload = JSON.parse(data.items.tblvendor.pan_upload);
          //     pan_upload.forEach(filename => generatePreviewHtml(filename, '../uploads/customers/', '.document-preview-bank','Pan Upload'));
          //     $('.pan_upload').hide();
          // } else if (data.items.pan_upload !== "") {
          //     pan_upload = JSON.parse(data.items.pan_upload);
          //     pan_upload.forEach(filename => generatePreviewHtml(filename, '../uploads/neft/', '.document-preview-bank','Pan Upload'));
          //     $('.pan_upload').show();
          // }
          // let bank=data.items.tblbankdetails;
          // bank.forEach((element,index) => {
          //   if (element.bank_uploads !== "") {
          //       bank_upload = JSON.parse(element.bank_uploads);
          //       bank_upload.forEach(filename => generatePreviewHtml(filename, '../uploads/customers/', '.document-preview-bank','Bank Upload'));
          //       $('.bank_uploads').hide();
          //   }
          // });
          // if (data.items.bank_upload !== "") {
          //     bank_upload = JSON.parse(data.items.bank_upload);
          //     bank_upload.forEach(filename => generatePreviewHtml(filename, '../uploads/neft/', '.document-preview-bank','Bank Upload'));
          //     $('.bank_uploads').show();
          // }
          let pan_upload = data.items.tblvendor.pan_upload;
            if (pan_upload && pan_upload.trim() !== "" && pan_upload !== null ) {
                pan_upload = JSON.parse(pan_upload);
                pan_upload.forEach(filename =>
                    generatePreviewHtml(filename, '../uploads/customers/', '#documentPanview')
                );
            } else {
                $('.pan_upload').hide();
            }
          let bank=data.items.tblbankdetails;
            bank.forEach((element,index) => {
              if (element.bank_uploads !== ""  && element.bank_uploads !== null) {
                  bank_upload = JSON.parse(element.bank_uploads);
                  bank_upload.forEach(filename => generatePreviewHtml(filename, '../uploads/customers/', '#documentBankview'));
                }else{
                  $('.bank_uploads').hide();
                }
              });
          const invoice_upload = JSON.parse(data.items.invoice_upload);
          const po_upload = JSON.parse(data.items.po_upload);
          const po_signed_upload = JSON.parse(data.items.po_signed_upload);
          const po_delivery_upload = JSON.parse(data.items.po_delivery_upload);
          // if (Array.isArray(pan_upload) && pan_upload.length > 0) {
          //     $('.pan_upload').hide();
          // }

          // if (Array.isArray(bank_upload) && bank_upload.length > 0) {
          //     $('.bank_uploads').hide();
          // }
          // Process each file type
          (documents || []).forEach(filename => 
              generatePreviewHtml(filename, '../uploads/vendor/bill/', '.document-preview-bill','')
          );

          (invoice_upload || []).forEach(filename => 
              generatePreviewHtml(filename, '../uploads/neft/', '.document-preview-neft','Invoice Upload')
          );

          (po_upload || []).forEach(filename => 
              generatePreviewHtml(filename, '../uploads/neft/', '.document-preview-neft','PO Upload')
          );

          (po_signed_upload || []).forEach(filename => 
              generatePreviewHtml(filename, '../uploads/neft/', '.document-preview-neft','PO Signed Upload')
          );

          (po_delivery_upload || []).forEach(filename => 
              generatePreviewHtml(filename, '../uploads/neft/', '.document-preview-neft','PO Delivery Upload')
          );
        //edit data set
        $('#id').val(data.items.id);
        $('#bill_pay_id').val(data.items.bill_pay_id);
        $('#selected-vendor-id').val(data.items.vendor_id);
        $('#serial_number').val(data.items.serial_number);
        $('#created_by').val(data.items.created_by);
        $('#vendor-search').val(data.items.vendor);
        $('#nature_payment').val(data.items.nature_payment);
        $('#utr_number').val(data.items.utr_number);
        $('#company_name').val(data.items.tblbillpay.company_name);
        $('#zone_name').val(data.items.tblbillpay.zone_name);
        $('#branch_name').val(data.items.tblbillpay.branch_name);
        $('#zone_id').val(data.items.tblbillpay.zone_id);
        $('#branch_id').val(data.items.tblbillpay.branch_id);
        $('#company_id').val(data.items.tblbillpay.company_id);
        if(data.items.pan_number==null){
          $('#pan_number').prop('readonly',false);
        }else{
          $('#pan_number').val(data.items.pan_number);
        }
        if(data.items.account_number==null){
          $('#account_number').prop('readonly',false);
        }else{
          $('#account_number').val(data.items.account_number);
        }
        if(data.items.ifsc_code==null){
          $('#ifsc_code').prop('readonly',false);
        }else{
          $('#ifsc_code').val(data.items.ifsc_code);
        }
        $('#payment_status').val(data.items.payment_status);


        let methodStr = data.items.payment_method;
        let paymentArray = [];
        if (typeof methodStr === 'string') {
            paymentArray = methodStr.split(',').map(e => e.trim()); // clean spaces
        }
        $('input[name="payment_method[]"]').prop('checked', false);
        if (Array.isArray(paymentArray)) {
            paymentArray.forEach(function (method) {
                $('input[name="payment_method[]"][value="' + method + '"]').prop('checked', true);
            });
        }

        let html="";
          if (Array.isArray(lines)) {
          lines.forEach((element,index) => {
            html += `<tr>
                <td>
                  <div class="">
                    <input type="hidden" class="form-control" id="invoice_amount" name="account[${index}][id]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.id}" placeholder="Invoice Amount">
                    <input type="text" class="form-control" id="invoice_amount" name="account[${index}][invoice_amount]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.invoice_amount}" placeholder="Invoice Amount">
                    <input type="hidden" class="form-control" id="invoice_amount" name="account[${index}][bill_id]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.bill_id}" placeholder="Invoice Amount">
                    <input type="hidden" class="form-control" id="invoice_amount" name="account[${index}][bill_pay_id]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.bill_pay_id}" placeholder="Invoice Amount">
                    <input type="hidden" class="form-control" id="invoice_amount" name="account[${index}][bill_pay_lines_id]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.bill_pay_lines_id}" placeholder="Invoice Amount">
                  </div>
                </td>
                <td>
                  <div class="">
                    <div class="col-md-12 tax-dropdown-wrapper tds-tax-section" style="width:170px">
                      <input type="text" class="form-control tax-search-input" name="account[${index}][tds_tax_name]" readonly placeholder="Select a Tax" value="${element.tds_tax_name}" readonly>
                      <input type="hidden" name="account[${index}][tds_tax_selected]" class="selected-tds-tax"  id="tds_tax_value">
                      <input type="hidden" name="account[${index}][tds_tax_id]" class="tds-tax-id" value="${element.tds_tax_id}">
                      <div class="dropdown-menu tax-dropdown">
                        <div class="inner-search-container">
                          <input type="text" class="tax-inner-search" placeholder="Search...">
                        </div>
                        <div class="tax-list"></div>
                        <div class="manage-tds-link">⚙️ Manage TDS</div>
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="">
                    <input type="text" class="form-control" id="tax_amount" name="account[${index}][tax_amount]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="${element.tax_amount}" readonly placeholder="Already Paid">
                  </div>
                </td>
                 <td>
                  <div class="">
                    <input type="text" class="form-control" id="already_paid" name="account[${index}][already_paid]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.already_paid}" placeholder="Already Paid">
                  </div>
                </td>
                <td>
                  <div class="">
                    <input type="text" class="form-control" id="only_payable" name="account[${index}][only_payable]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="${element.only_payable}" readonly placeholder="PAN Number">
                  </div>
                </td>

              </tr>`;
            });
            $('#account_table').html(html);

        } else {
          console.warn('bill_lines is missing or not an array:', data.bill_lines);
        }

       $(document).ready(function () {
        const previewFiles = {};
        const existingFiles = {};

        // Initialize with existing files if any
        function initializeExistingFiles() {
            try {
                if (typeof bank_upload !== 'undefined' && bank_upload) {
                    existingFiles['bank_upload'] = bank_upload;
                }
                if (typeof pan_upload !== 'undefined' && pan_upload) {
                    existingFiles['pan_upload'] = pan_upload;
                }
                if (typeof invoice_upload !== 'undefined' && invoice_upload) {
                    existingFiles['invoice_upload'] = invoice_upload;
                }
                if (typeof po_upload !== 'undefined' && po_upload) {
                    existingFiles['po_upload'] = po_upload;
                }
                if (typeof po_signed_upload !== 'undefined' && po_signed_upload) {
                    existingFiles['po_signed_upload'] = po_signed_upload;
                }
                if (typeof po_delivery_upload !== 'undefined' && po_delivery_upload) {
                    existingFiles['po_delivery_upload'] = po_delivery_upload;
                }

                // Render all existing files initially
                Object.keys(existingFiles).forEach(inputId => {
                    renderExistingFiles(inputId, existingFiles[inputId]);
                });
            } catch (e) {
                console.error("Error initializing existing files:", e);
            }
        }

        // Render existing files in preview
        function renderExistingFiles(inputId, files) {
            const preview = $(`#preview_${inputId}`);
            preview.empty();

            if (!files || !files.length) return;

            files.forEach((file, index) => {
                const fileName = file.name || file.file_name || file;
                const fileValue = file.id || file.path || file;

                const fileHtml = `
                    <li class="file-preview-item" data-index="${index}" data-existing="true">
                        <span><i class="fas fa-file"></i> ${fileName}</span>
                        <span class="remove-file-btn" data-input="${inputId}" data-index="${index}" data-existing="true">❌</span>
                        <input type="hidden" name="existing_${inputId}[]" value="${fileValue}">
                    </li>
                `;
                preview.append(fileHtml);
            });
        }

        // Handle file input change
        $('input[type="file"]').on('change', function () {
            const input = $(this);
            const inputId = input.attr('id');

            // Initialize previewFiles for this input if not exists
            if (!previewFiles[inputId]) {
                previewFiles[inputId] = [];
            }

            // Add new files to the previewFiles array
            const newFiles = Array.from(this.files);
            previewFiles[inputId] = previewFiles[inputId].concat(newFiles);

            // Update the file input
            const dt = new DataTransfer();
            previewFiles[inputId].forEach(file => dt.items.add(file));
            $(`#${inputId}`)[0].files = dt.files;

            // Render all files
            renderAllFiles(inputId);
        });

        // Render both existing and new files
        function renderAllFiles(inputId) {
            const preview = $(`#preview_${inputId}`);
            preview.empty();

            // First render existing files (if any)
            if (existingFiles[inputId] && existingFiles[inputId].length > 0) {
                existingFiles[inputId].forEach((file, index) => {
                    const fileName = file.name || file.file_name || file;
                    const fileValue = file.id || file.path || file;

                    const fileHtml = `
                        <li class="file-preview-item" data-index="${index}" data-existing="true">
                            <span><i class="fas fa-file"></i> ${fileName}</span>
                            <span class="remove-file-btn" data-input="${inputId}" data-index="${index}" data-existing="true">❌</span>
                            <input type="hidden" name="existing_${inputId}[]" value="${fileValue}">
                        </li>
                    `;
                    preview.append(fileHtml);
                });
            }

            // Then render new files (if any)
            if (previewFiles[inputId] && previewFiles[inputId].length > 0) {
                previewFiles[inputId].forEach((file, index) => {
                    const fileHtml = `
                        <li class="file-preview-item" data-index="${index}">
                            <span><i class="fas fa-file"></i> ${file.name}</span>
                            <span class="remove-file-btn" data-input="${inputId}" data-index="${index}">❌</span>
                        </li>
                    `;
                    preview.append(fileHtml);
                });
            }
        }

        // Handle file removal
        $(document).on('click', '.remove-file-btn', function () {
            const inputId = $(this).data('input');
            const index = parseInt($(this).data('index'));
            const isExisting = $(this).data('existing');

            if (isExisting) {
                // For existing files, add a hidden field to mark for deletion
                const fileValue = $(this).siblings('input').val();
                $(this).parent().append(`<input type="hidden" name="delete_${inputId}[]" value="${fileValue}">`);
                $(this).parent().hide();

                // Remove from existingFiles array
                if (existingFiles[inputId] && existingFiles[inputId].length > index) {
                    existingFiles[inputId].splice(index, 1);
                }
            } else {
                // For new files, remove from previewFiles array
                if (previewFiles[inputId] && previewFiles[inputId].length > index) {
                    previewFiles[inputId].splice(index, 1);

                    // Update the file input
                    const dt = new DataTransfer();
                    previewFiles[inputId].forEach(file => dt.items.add(file));
                    $(`#${inputId}`)[0].files = dt.files;
                }
            }

            // Re-render all files
            renderAllFiles(inputId);
        });

        // Initialize existing files on page load
        initializeExistingFiles();
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


});
// $(document).ready(function () {
//   const previewFiles = {};

//   $('input[type="file"]').on('change', function () {
//     const input = $(this);
//     const inputId = input.attr('id');
//     const preview = $(`#preview_${inputId}`);
//     const files = Array.from(this.files);

//     previewFiles[inputId] = files;
//     preview.empty();

//     files.forEach((file, index) => {
//       const fileHtml = `
//         <li class="file-preview-item" data-index="${index}">
//           <span><i class="fas fa-file"></i> ${file.name}</span>
//           <span class="remove-file-btn" data-input="${inputId}" data-index="${index}">❌</span>
//         </li>
//       `;
//       preview.append(fileHtml);
//     });
//   });

//   $(document).on('click', '.remove-file-btn', function () {
//     const inputId = $(this).data('input');
//     const index = $(this).data('index');

//     previewFiles[inputId].splice(index, 1);
//     const dt = new DataTransfer();
//     previewFiles[inputId].forEach(file => dt.items.add(file));
//     $(`#${inputId}`)[0].files = dt.files;

//     const preview = $(`#preview_${inputId}`);
//     preview.empty();

//     previewFiles[inputId].forEach((file, i) => {
//       const fileHtml = `
//         <li class="file-preview-item" data-index="${i}">
//           <span><i class="fas fa-file"></i> ${file.name}</span>
//           <span class="remove-file-btn" data-input="${inputId}" data-index="${i}">❌</span>
//         </li>
//       `;
//       preview.append(fileHtml);
//     });
//   });
// });

$(document).ready(function () {
  $(document).on('click', '.documentclk', function () {
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
      views += `<button style="font-size: 11px;" type="button" class="btn btn-primary pdf-btn" data-filepath="${file}">${fileName}</button>`;
    });

    $('#image_pdfs').html(views);
  });

  // Global handler for pdf buttons
  $(document).on('click', '.pdf-btn', function () {
    $('.pdf-btn').removeClass('active');
    $(this).addClass('active');
    const filePath = $(this).data('filepath');
    $('#pdfmain').attr('src', filePath);
  });
});
// $(document).ready(function () {

//   $('#submit-neft-datas').click(function (event) {
//         event.preventDefault();
//         const $btn = $(this);
//         $btn.prop('disabled', true);
//         let isValid = true;

//         if ($('#serial_number').val() === "") {
//             $('.error_serial').text('Serial Required');
//             isValid = false;
//         }
//         if ($('#created_by').val() === "") {
//             $('.error_created_by').text('Enter the Creator Name');
//             isValid = false;
//         }
//         if ($('#vendor').val() === "") {
//             $('.error_vendor').text('Please select the Employee Name or Vendor');
//             isValid = false;
//         }

//         // if ($('#nature_payment').val() === "") {
//         //     $('.error_nature_payment').text('Enter the Nature Payment');
//         //     isValid = false;
//         // }
//         if ($('#invoice_amount').val() === "") {
//             $('.error_invoice_amount').text('Enter the Invoice Amount');
//             isValid = false;
//         }

//         if ($('#payment_status').val() === "") {
//             $('.error_payment_status').text('Select Payment Status');
//             isValid = false;
//         }

//         if ($('#utr_number').val() === "") {
//             $('.error_utr_number').text('Enter UTR Number');
//             isValid = false;
//         }

//         let paymentStatusChecked = false;
//         $('input[name="payment_method[]"]:checked').each(function() {
//             paymentStatusChecked = true;
//         });
//         if (!paymentStatusChecked) {
//             $('.error_payment_method').text('Select at least one payment method');
//             isValid = false;
//         }

//         // // PAN number validation (e.g. ABCDE1234F)
//         // let panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
//         // let panNumber = $('#pan_number').val().toUpperCase();
//         // if (!panPattern.test(panNumber)) {
//         //     $('.error_pan_number').text('Invalid PAN Number');
//         //     isValid = false;
//         // } else {
//         //     $('.error_pan_number').text('');
//         // }

//         function isFileTooLarge(inputId, errorClass) {
//                 const file = $(inputId)[0].files[0];
//                 if (file && file.size > 5242880) {
//                     $(errorClass).text('File size must be less than 5MB');
//                     return true;
//                 }
//                 $(errorClass).text('');
//                 return false;
//             }

//             // Apply file size check
//             if (isFileTooLarge('#pan_upload', '.error_pan_upload')) isValid = false;
//             if (isFileTooLarge('#bank_upload', '.error_bank_upload')) isValid = false;
//             if (isFileTooLarge('#invoice_upload', '.error_invoice_upload')) isValid = false;
//             if (isFileTooLarge('#po_upload', '.error_po_upload')) isValid = false;
//             if (isFileTooLarge('#po_signed_upload', '.error_po_signed_upload')) isValid = false;
//             if (isFileTooLarge('#po_delivery_upload', '.error_po_delivery_upload')) isValid = false;


//         console.log("isValid",isValid);
//         if (!isValid) {
//             return;
//         }

//       let formData = new FormData($('#neftForm')[0]);
//         console.log("formData",formData);

//         // Include CSRF Token
//         formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

//         // AJAX Request
//         $.ajax({
//             url: '{{ route("superadmin.saveneft") }}',
//             type: "POST",
//             data: formData,
//             processData: false,
//             contentType: false,
//             success: function (response) {
//               toastr.success(response.message);
//                 if (response.success) {
//                     $('#NEFTModal').fadeOut();
//                     window.location.reload();
//                     $btn.prop('disabled', false);
//                 }
//             },
//             error: function (error) {
//                 if (error.responseJSON && error.responseJSON.errors) {
//                     // Display validation errors
//                     $.each(error.responseJSON.errors, function(key, value) {
//                         $('.error_' + key).text(value[0]);
//                     });
//                 } else {
//                     console.error(error.responseJSON);
//                 }
//             },
//         });
//     });
// });
let neftModal;

$(document).ready(function () {
    const modalEl = document.getElementById('NEFTModal');
    if (modalEl) {
        neftModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    }
});


$(document).ready(function () {
    $('#submit-neft-datas').click(function (event) {
        event.preventDefault();
        const $btn = $(this);
        $btn.prop('disabled', true);
        let isValid = true;

        // Reset error messages
        $('.error-text').text('');

        // Validation
        if ($('#serial_number').val() === "") {
            $('.error_serial').text('Serial Required');
            isValid = false;
        }
        if ($('#created_by').val() === "") {
            $('.error_created_by').text('Enter the Creator Name');
            isValid = false;
        }
        if ($('#vendor').val() === "") {
            $('.error_vendor').text('Please select the Employee Name or Vendor');
            isValid = false;
        }
        if ($('#invoice_amount').val() === "") {
            $('.error_invoice_amount').text('Enter the Invoice Amount');
            isValid = false;
        }
        if ($('#payment_status').val() === "") {
            $('.error_payment_status').text('Select Payment Status');
            isValid = false;
        }
        if ($('#utr_number').val() === "") {
            $('.error_utr_number').text('Enter UTR Number');
            isValid = false;
        }

        let paymentStatusChecked = false;
        $('input[name="payment_method[]"]:checked').each(function() {
            paymentStatusChecked = true;
        });
        if (!paymentStatusChecked) {
            $('.error_payment_method').text('Select at least one payment method');
            isValid = false;
        }

        function isFileTooLarge(inputId, errorClass) {
            const file = $(inputId)[0].files[0];
            if (file && file.size > 5242880) {
                $(errorClass).text('File size must be less than 5MB');
                return true;
            }
            $(errorClass).text('');
            return false;
        }

        // Apply file size check
        if (isFileTooLarge('#pan_upload', '.error_pan_upload')) isValid = false;
        if (isFileTooLarge('#bank_upload', '.error_bank_upload')) isValid = false;
        if (isFileTooLarge('#invoice_upload', '.error_invoice_upload')) isValid = false;
        if (isFileTooLarge('#po_upload', '.error_po_upload')) isValid = false;
        if (isFileTooLarge('#po_signed_upload', '.error_po_signed_upload')) isValid = false;
        if (isFileTooLarge('#po_delivery_upload', '.error_po_delivery_upload')) isValid = false;

        if (!isValid) {
            $btn.prop('disabled', false);
            return;
        }

        let formData = new FormData($('#neftForm')[0]);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        console.log("neftModal",neftModal);

        $.ajax({
            url: '{{ route("superadmin.saveneft") }}',
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.message);
                $btn.prop('disabled', false);

                if (!response.success) return;

                const neftId = response.data.id;
                console.log("neftId",neftId);

                // ✅ UPDATE ROW FIRST
                if (neftId) {
                  console.log("inside");

                    updateTableRow(response.data);
                }
                console.log("outside");

                // ✅ THEN HIDE MODAL (SAFE)
                if (neftModal) {
                  $('#NEFTModal').hide();
                  $('.close-modal').trigger('click');
                }
            },
            error: function (error) {
                $btn.prop('disabled', false);
                if (error.responseJSON && error.responseJSON.errors) {
                    $.each(error.responseJSON.errors, function(key, value) {
                        $('.error_' + key).text(value[0]);
                    });
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
        });
    });

    function updateTableRow(neftData) {
      console.log("12112");

      // find row by NEFT id
      const $row = $(`tr.customer-row[data-id="${neftData.id}"]`);

      if (!$row.length) {
          console.error('Row not found for NEFT:', neftData.id);
          return;
      }

      // store updated data
      $row.data('items', neftData);
      $row.attr('data-items', JSON.stringify(neftData));

      // basic columns
      $row.find('td:nth-child(2)').text(formatDate(neftData.created_at));
      $row.find('td:nth-child(3)').text(neftData.serial_number ?? '-');
      $row.find('td:nth-child(4)').html(
          `${neftData.zone_name ?? '-'}<br>${neftData.branch_name ?? '-'}`
      );
      $row.find('td:nth-child(5)').text(neftData.created_by ?? '-');

      // vendor
      const vendorUrl = "{{ route('superadmin.getvendor') }}?id=" + neftData.vendor_id;
      $row.find('td:nth-child(6)').html(`
          <a href="${vendorUrl}" style="color:blue">
              ${neftData.tblvendor && neftData.tblvendor.display_name
                ? neftData.tblvendor.display_name
                : '-'}
          </a>
      `);

      // other details
      $row.find('td:nth-child(7)').text(neftData.nature_payment ?? '-');
      $row.find('td:nth-child(8)').text(neftData.payment_status ?? '-');
      $row.find('td:nth-child(9)').text(neftData.payment_method ?? '-');
      $row.find('td:nth-child(10)').text(neftData.utr_number ?? '-');
      $row.find('td:nth-child(11)').text(neftData.pan_number ?? '-');
      $row.find('td:nth-child(12)').text(neftData.account_number ?? '-');
      $row.find('td:nth-child(13)').text(neftData.ifsc_code ?? '-');

      // totals
      let amount = 0, tax = 0, gst = 0, balance = 0;
      let billNo = '-', poNo = '-', quotNo = '-';

      if (Array.isArray(neftData.BillLines)) {
          neftData.BillLines.forEach(line => {
              amount  += +line.already_paid || 0;
              tax     += +line.tax_amount || 0;
              balance += +line.only_payable || 0;

              if (Array.isArray(line.Tblbilllines)) {
                  line.Tblbilllines.forEach(b => {
                      gst += +b.gst_amount || 0;
                  });
              }

              billNo = line.Bill?.bill_gen_number ?? billNo;
              poNo   = line.Bill?.Purchase?.purchase_gen_order ?? poNo;
              quotNo = line.Bill?.Purchase?.quotation?.quotation_gen_no ?? quotNo;
          });
      }

      // bill / po / quotation
      $row.find('td:nth-child(14)').text(billNo);
      $row.find('td:nth-child(15)').text(poNo);
      $row.find('td:nth-child(16)').text(quotNo);

      // amounts
      $row.find('td:nth-child(17)').text(amount.toFixed(2));
      $row.find('td:nth-child(19)').text(tax.toFixed(2));
      $row.find('td:nth-child(21)').text(gst.toFixed(2));
      $row.find('td:nth-child(22)').text(balance.toFixed(2));

      // status
      $row.find('td:nth-child(23)').html(
          neftData.checker_status == 1 ? '✔️' : '❌'
      );
      $row.find('td:nth-child(24)').html(
          neftData.approval_status == 1 ? '✔️' : '❌'
      );

      // highlight updated row
      $row.addClass('updated');
      setTimeout(() => $row.removeClass('updated'), 1500);
  }



    // Helper function to format date
    function formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-GB');
  }


    // Helper function to truncate text
    function truncateText(text, length) {
        if (!text) return '-';
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    }

    // Function to attach event handlers to links
    function attachLinkEventHandlers($row) {
        // Attach event handlers to print buttons
        $row.find('.print-pop-btn').off('click').on('click', function() {
            const billId = $(this).data('id');
            // Your existing print popup handler
            console.log('Print bill:', billId);
        });

        $row.find('.print-po-pop-btn').off('click').on('click', function() {
            const poId = $(this).data('id');
            // Your existing PO popup handler
            console.log('Print PO:', poId);
        });

        $row.find('.print-quot-pop-btn').off('click').on('click', function() {
            const quotId = $(this).data('id');
            // Your existing quotation popup handler
            console.log('Print Quotation:', quotId);
        });

        // Attach vendor link handler if needed
        $row.find('.vendor_link').off('click').on('click', function(e) {
            // Already has href, let it navigate normally
        });
    }

    // Function to attach event handlers to action buttons
    function attachActionEventHandlers($row) {
        $row.find('.check').off('click').on('click', function() {
            const neftId = $(this).data('id');
            // Your existing check handler
            console.log('Check neft:', neftId);
        });

        $row.find('.approver').off('click').on('click', function() {
            const neftId = $(this).data('id');
            // Your existing approver handler
            console.log('Approve neft:', neftId);
        });
    }
});
$(document).ready(function () {

const TblZonesModel = @json($TblZonesModel);
    const Tblcompany = @json($Tblcompany);
    const Tblvendor = @json($Tblvendor);
    const Tblaccount = @json($Tblaccount);
     (Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data || [])).forEach(locations => {
                        const item = $(`
                          <div data-id="${locations.id}">${locations.name} </div>
                        `);
                        $('.zone-list').append(item);
                    });
      Tblcompany.data.forEach(Tblcompany => {
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
                    Tblaccount.forEach(Tblaccount => {
                        const item = $(`
                            <div data-value="${Tblaccount.name}" data-id="${Tblaccount.id}">${Tblaccount.name}</div>
                        `);
                        $('.account-list').append(item);
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
            url: '{{ route("superadmin.getbranchfetch") }}',
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

         $(document).on('click', function (e) {
            if (!$(e.target).closest('.tax-dropdown-wrapper').length) {
              $('.dropdown-menu.tax-dropdown').hide();
            }
          });
          $(document).on('click', '.dropdown-menu.tax-dropdown', function (e) {
                e.stopPropagation();
            });

          $(document).ready(function () {
            let savedFilters = null;
                if (sessionStorage.getItem("restore_filters")) {
                    try {
                        savedFilters = JSON.parse(sessionStorage.getItem("neft_filters"));
                    } catch (e) {
                        savedFilters = null;
                    }
                    sessionStorage.removeItem("restore_filters");
                }

            let filters = {
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
            };
            if (savedFilters) {
                    filters = savedFilters;
                }

                loadNEFT(
                    sessionStorage.getItem("neft_page") || 1
                );

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

                if (summaryHtml) {
                    summaryHtml += `<span class="filter-badge filter-clear" id="clear-all">
                        Clear all
                    </span>`;
                }

                $("#filter-summary").html(summaryHtml || "");
            }

            function updateNeftStatCards(s) {
                if (!s) return;
                $('#stat-total').text(s.total ?? 0);
                $('#stat-total-amt').text('₹' + (s.total_amount ?? 0).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2}));
                $('#stat-checked').text(s.checked ?? 0);
                $('#stat-approved').text(s.approved ?? 0);
                $('#stat-pending').text(s.pending ?? 0);
                $('#stat-success').text(s.success ?? 0);
                $('#stat-success-amt').text('₹' + (s.success_amount ?? 0).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2}));
            }

            function loadNEFT(page = 1, perPage = $('#per_page').val()) {
                $.ajax({
                    url: '{{ route("superadmin.getneftdashboard") }}',
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
                        nature_name: filters.nature_name,
                        status_name: filters.status_name,
                        state_name: filters.state_name,
                        universal_search: filters.universal_search
                    },
                    success: function (data) {
                        if (data && typeof data === 'object' && data.html !== undefined) {
                            $("#Neft-body").html(data.html);
                            updateNeftStatCards(data.stats);
                        } else {
                            $("#Neft-body").html(data);
                        }
                        renderSummary();
                    }
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

                    const savedPage = sessionStorage.getItem('neft_page') || 1;

                    setTimeout(function() {
                        loadNEFT(savedPage, $('#per_page').val());
                    }, 200);
                }

            // =================== MULTI-SELECT CHANGE LISTENER ===================
            function setupMultiSelect(selectorInput, selectorHidden) {
                $(document).on('click', selectorHidden, function () {
                  console.log("21323423");
                  console.log(selectorHidden);

                    const selectedIds = $(this).val(); // comma-separated
                    const selectedText = $(selectorInput).val();
                  console.log(selectedText,'selectedText');

                    if (selectorHidden === '.zone_id') {
                      console.log("true");

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
                    }else if (selectorHidden === '.state_id') {
                        filters.state_id = selectedIds;
                        filters.state_name = selectedText;
                    }
                    loadNEFT();
                });
            }
            $('.zone_id').on('click', function () {
              setupMultiSelect('.zone-search-input', '.zone_id');
            });
            $('.branch_id').on('click', function () {
              setupMultiSelect('.branch-search-input', '.branch_id');
            });
            $('.company_id').on('click', function () {
              setupMultiSelect('.company-search-input', '.company_id');
            });
            $('.vendor_id').on('click', function () {
              setupMultiSelect('.vendor-search-input', '.vendor_id');
            });
            $('.vendor_id').on('click', function () {
              setupMultiSelect('.vendor-search-input', '.vendor_id');
            });
            $('.nature_id').on('click', function () {
              setupMultiSelect('.nature-search-input', '.nature_id');
            });
            $('.status_id').on('click', function () {
              setupMultiSelect('.status-search-input', '.status_id');
            });
            $('.state_id').on('click', function () {
              setupMultiSelect('.state-search-input', '.state_id');
            });
            $('.universal_search').on('keyup', function () {
              filters.universal_search=  $('.universal_search').val();
              loadNEFT();
            });


            // Date change
            $('.data_values').on('change', function () {
                let dateRange = $(this).val();
                if (dateRange.includes('to')) {
                    let parts = dateRange.split(' to ');
                    filters.date_from = parts[0].trim();
                    filters.date_to = parts[1].trim();
                }
                loadNEFT();
            });

            // Remove single filter
            $("#filter-summary").on('click', '.remove-icon', function () {
                let type = $(this).data('type');

                if (type === 'date') {
                    filters.date_from = '';
                    filters.date_to = '';
                    $('.data_values').val('');
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
                }else if (type === 'status') {
                    filters.status_id = '';
                    filters.status_name = '';
                    $('.status_id').val('');
                    $('.status-search-input').val('');
                    $('.status-list div').removeClass('selected');
                }else if (type === 'state') {
                    filters.state_id = '';
                    filters.state_name = '';
                    $('.state_id').val('');
                    $('.state-search-input').val('');
                    $('.state-list div').removeClass('selected');
                }
                loadNEFT();
            });

            // Clear all filters
            $("#filter-summary").on('click', '#clear-all', function () {
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
                };
                $('.zone-search-input, .branch-search-input, .company-search-input, .vendor-search-input,.nature-search-input,.status-search-input,.state-search-input').val('');
                $('.zone_id, .branch_id, .company_id, .vendor_id,.nature_id,.status_id,.state_id').val('');
                $('.data_values').val('');
                $('.dropdown-list div').removeClass('selected');
                loadNEFT();
            });

            // Pagination
            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let params = new URLSearchParams(url.split('?')[1]);
                let page = params.get('page') || 1;
                let perPage = $('#per_page').val();
                loadNEFT(page, perPage);
            });

            // Change per_page
            $(document).on('change', '#per_page', function () {
                loadNEFT(1, $(this).val());
            });

            // ===== STAT CARD CLICK FILTER =====
            $(document).on('click', '.qd-stat-card', function () {
                $('.qd-stat-card').removeClass('qd-stat-active');
                $(this).addClass('qd-stat-active');
                const f = $(this).data('stat-filter');
                filters.status_name = (f === undefined || f === null) ? '' : String(f);
                loadNEFT();
            });

            // ===== TOGGLE STATS & FILTERS =====
            (function () {
                var statsVisible   = true;
                var filtersVisible = true;

                $('#toggleStats').on('click', function () {
                    var $btn  = $(this);
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

                $('#toggleFilters').on('click', function () {
                    var $btn  = $(this);
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

            @if(isset($limit_access) && $limit_access == 1)
            // ===== NEFT EDIT HISTORY POPUP =====
            $(document).on('click', '.qdt-history-btn', function (e) {
                e.stopPropagation();
                const raw = $(this).data('history');
                const ref  = $(this).data('qno') || 'NEFT';
                let history = [];
                try { history = typeof raw === 'string' ? JSON.parse(raw) : (raw || []); } catch(err) { history = []; }
                $('#neftHistoryTitle').text('Edit History — ' + ref);
                let html = '';
                if (!history.length) {
                    html = '<div class="qdt-no-history">No edit history found.</div>';
                } else {
                    history.forEach(function(h, i) {
                        html += `<div class="qdt-history-entry">
                            <div class="qdt-history-index">#${i+1}</div>
                            <div class="qdt-history-info">
                                <div><strong>${h.edited_by || '—'}</strong> <span class="qdt-history-role">(${h.role || '—'})</span></div>
                                <div class="qdt-history-meta">${h.edited_at || ''}</div>
                                <div>Status: <span class="qd-badge qd-badge-info">${h.status || '—'}</span></div>
                                <div>Amount: <strong>₹${parseFloat(h.amount||0).toLocaleString('en-IN',{minimumFractionDigits:2})}</strong></div>
                            </div>
                        </div>`;
                    });
                }
                $('#neftHistoryBody').html(html);
                $('#neftHistoryPopup, #neftHistoryOverlay').show();
            });
            $('#closeNeftHistory, #neftHistoryOverlay').on('click', function () {
                $('#neftHistoryPopup, #neftHistoryOverlay').hide();
            });
            @endif

    });
    $(document).ready(function() {
                // always reset to All Dates on refresh
                $('#data_values').text('All Dates');
                $('.data_values').val('');

                if ($('#reportrange').data('daterangepicker')) {
                    let picker = $('#reportrange').data('daterangepicker');

                    picker.setStartDate(moment().subtract(50, 'years'));
                    picker.setEndDate(moment().add(50, 'years'));
                    picker.updateElement();
                }
            });

            function cb(start, end, label) {
                if (label === 'All Dates') {
                    $('#data_values').text('All Dates');
                    $('.data_values').val('');
                } else {
                    $('#data_values').text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                    $('.data_values').val(start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY'));
                }
                $('.data_values').trigger('change');
            }

});

</script>

<!-- <script>
        window.addEventListener('beforeunload', function() {
            sessionStorage.removeItem('force_first_page_done');
        });

        $(document).ready(function() {
            // always reset to All Dates on refresh
            $('#data_values').text('All Dates');
            $('.data_values').val('');

            if ($('#reportrange').data('daterangepicker')) {
                let picker = $('#reportrange').data('daterangepicker');

                picker.setStartDate(moment().subtract(50, 'years'));
                picker.setEndDate(moment().add(50, 'years'));
                picker.updateElement();
            }
        });

        function cb(start, end, label) {
            if (label === 'All Dates') {
                $('#data_values').text('All Dates');
                $('.data_values').val('');
            } else {
                $('#data_values').text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                $('.data_values').val(start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY'));
            }
            $('.data_values').trigger('change');
        }
    </script> -->
<!-- [ Main Content ] end -->
@include('superadmin.superadminfooter')
</body>
<!-- [Body] end -->
</html>
