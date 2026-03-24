<!doctype html>
<html lang="en">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<style>
input[type="file"] { display: none; }
.preview-card {
  background: #f8f9fa; border: 1px solid #ddd; border-radius: 10px;
  padding: 12px; margin: 8px; width: 140px; text-align: center;
  font-size: 12px; overflow-wrap: break-word; cursor: pointer;
}
.preview-card img { max-width:100%; max-height:80px; object-fit:cover; margin-bottom:6px; }
#documentModal1 { z-index: 999999; }

/* ── Asset detail side panel ── */
.asset-backdrop {
  position:fixed; inset:0; background:rgba(15,23,42,.45);
  z-index:1040; opacity:0; pointer-events:none; transition:opacity .25s;
}
.asset-backdrop.show { opacity:1; pointer-events:auto; }
.asset-panel {
  position:fixed; top:0; right:0; width:68%; max-width:980px;
  height:100vh; background:#f8f9fc;
  box-shadow:-6px 0 32px rgba(0,0,0,.14);
  z-index:1050; display:flex; flex-direction:column;
  transform:translateX(100%);
  transition:transform .28s cubic-bezier(.4,0,.2,1);
}
.asset-panel.show { transform:translateX(0); }
.asset-panel-hdr {
  display:flex; align-items:center; justify-content:space-between;
  padding:14px 20px; background:#fff; border-bottom:1px solid #e5e9f0; flex-shrink:0;
}
.asset-panel-title { font-size:15px; font-weight:700; color:#1e293b; }
.asset-panel-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.asset-close-btn {
  background:none; border:none; cursor:pointer; font-size:18px;
  color:#64748b; padding:4px 8px; border-radius:6px; transition:background .15s;
}
.asset-close-btn:hover { background:#f1f5f9; color:#1e293b; }
.asset-panel-body { flex:1; overflow-y:auto; padding:20px; }
.ap-card {
  background:#fff; border-radius:10px; border:1px solid #e5e9f0;
  padding:16px; margin-bottom:14px;
}
.ap-card-title {
  font-size:11px; font-weight:700; text-transform:uppercase;
  letter-spacing:.6px; color:#475569; margin-bottom:12px;
  display:flex; align-items:center; gap:6px;
}
.ap-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px 20px; }
.ap-item .ap-lbl { font-size:11px; color:#94a3b8; margin-bottom:2px; }
.ap-item .ap-val { font-size:13px; font-weight:600; color:#1e293b; }
.ap-table { width:100%; border-collapse:collapse; font-size:12.5px; }
.ap-table th {
  background:#f8fafc; color:#64748b; font-size:11px; font-weight:700;
  text-transform:uppercase; letter-spacing:.4px; padding:8px 10px;
  border-bottom:1px solid #e5e9f0; text-align:left;
}
.ap-table td { padding:9px 10px; border-bottom:1px solid #f1f5f9; color:#1e293b; }
.ap-table tbody tr:last-child td { border-bottom:none; }
.ap-table tbody tr:hover td { background:#f8fafc; }
.ap-totals { display:flex; flex-direction:column; gap:6px; }
.ap-total-row { display:flex; justify-content:space-between; font-size:13px; color:#1e293b; padding:4px 0; }
.ap-total-row.grand { font-weight:700; font-size:14px; border-top:2px solid #e5e9f0; padding-top:8px; margin-top:4px; }
.pay-toggle { cursor:pointer; color:#4f6ef7; font-weight:600; font-size:13px; user-select:none; }
</style>

<body style="overflow-x: hidden;">
<div class="page-loader"><div class="bar"></div></div>

    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">

    <div class="qd-card">

      {{-- ── Header ── --}}
      <div class="qd-header">
        <div class="qd-header-title">
          <i class="bi bi-boxes"></i>
          Asset Dashboard
        </div>
        <div class="qd-header-actions">
          <button class="btn btn-sm qd-toggle-btn" id="toggleStats">
            <i class="bi bi-bar-chart-line me-1"></i>Stats
            <i class="bi bi-chevron-up qd-toggle-icon" id="statsChevron"></i>
          </button>
          <button class="btn btn-sm qd-toggle-btn" id="toggleFilters">
            <i class="bi bi-funnel me-1"></i>Filter
            <i class="bi bi-chevron-up qd-toggle-icon" id="filtersChevron"></i>
          </button>
                            <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
              <i class="bi bi-three-dots-vertical"></i>
                                </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importbillModal">
                <i class="bi bi-upload me-1"></i>Import Bill
              </a></li>
                                </ul>
                            </div>
                    </div>
                    </div>

      {{-- ── Stats ── --}}
      <div class="qd-stats" id="statsSection">
        <div class="qd-stat-card qd-stat-blue qd-stat-active" data-stat-filter="" title="All Assets">
          <div class="qd-stat-icon"><i class="bi bi-boxes"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total Assets</div>
            <div class="qd-stat-value" data-stat-key="total">{{ $stats['total'] }}</div>
            <div class="qd-stat-sub">&nbsp;</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-green" data-stat-filter="save" title="Filter: Saved">
          <div class="qd-stat-icon"><i class="bi bi-check2-circle"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Saved</div>
            <div class="qd-stat-value" data-stat-key="save">{{ $stats['save'] }}</div>
            <div class="qd-stat-sub">&nbsp;</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-orange" data-stat-filter="draft" title="Filter: Draft">
          <div class="qd-stat-icon"><i class="bi bi-file-earmark-text"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Draft</div>
            <div class="qd-stat-value" data-stat-key="draft">{{ $stats['draft'] }}</div>
            <div class="qd-stat-sub">&nbsp;</div>
          </div>
        </div>
        <div class="qd-stat-card qd-stat-purple" data-stat-filter="" title="Total Value">
          <div class="qd-stat-icon"><i class="bi bi-currency-rupee"></i></div>
          <div class="qd-stat-body">
            <div class="qd-stat-label">Total Value</div>
            <div class="qd-stat-value" data-stat-key="total_amount">₹{{ number_format($stats['total_amount'], 0) }}</div>
            <div class="qd-stat-sub">&nbsp;</div>
          </div>
        </div>
      </div>

      {{-- ── Filters ── --}}
      <div class="qd-filters" id="filtersSection">
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
                      <input type="text" class="form-control company-search-input dropdown-search-input" placeholder="Select Company" readonly>
                      <input type="hidden" name="company_id" class="company_id">
                      <div class="dropdown-menu tax-dropdown">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Company..."></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                        </div>
              <div class="dropdown-list multiselect company-list"></div>
                          </div>
                        </div>

          <div class="qd-filter-group tax-dropdown-wrapper zone-section">
            <label>Zone</label>
                      <input type="text" class="form-control zone-search-input dropdown-search-input" placeholder="Select Zone" readonly>
                      <input type="hidden" name="zone_id" class="zone_id">
                      <div class="dropdown-menu tax-dropdown">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Zone..."></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                        </div>
              <div class="dropdown-list multiselect zone-list"></div>
                          </div>
                        </div>

          <div class="qd-filter-group tax-dropdown-wrapper branch-section">
            <label>Branch</label>
                        <input type="text" class="form-control branch-search-input dropdown-search-input" placeholder="Select Branch" readonly>
                        <input type="hidden" name="branch_id" class="branch_id">
                        <div class="dropdown-menu tax-dropdown">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Branch..."></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                          </div>
              <div class="dropdown-list multiselect branch-list"></div>
                          </div>
                          </div>
                        </div>

        <div class="qd-filter-row" style="margin-top:10px;">
          <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
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
                    </div>

          <div class="qd-filter-group tax-dropdown-wrapper nature-section">
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
                        </div>

          <div class="qd-filter-group tax-dropdown-wrapper status-section">
            <label>Status</label>
            <input type="text" class="form-control status-search-input dropdown-search-input" placeholder="Select Status" readonly>
                      <input type="hidden" name="status_id" class="status_id">
                      <div class="dropdown-menu tax-dropdown">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Status..."></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                          </div>
                        <div class="dropdown-list multiselect status-list">
                          <div data-value="save" data-id="1">Save</div>
                          <div data-value="draft" data-id="2">Draft</div>
                        </div>
                      </div>
                    </div>
                    </div>
                  </div>

      {{-- ── Search ── --}}
      <div class="qd-search-row">
        <div class="qd-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" class="universal_search" placeholder="Search bill no, vendor, zone, branch, amount...">
                  </div>
                </div>

      {{-- ── Applied filters ── --}}
      <div class="qd-applied-bar">
        <span class="applied-label">Filters:</span>
        <div id="filter-summary" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
              </div>

      {{-- ── Table ── --}}
      <div class="qd-table-wrap">
              <div id="bill-body">
                @include('vendor.partials.table.bill_rows', ['billlist' => $billlist, 'perPage' => $perPage])
              </div>
            </div>

    </div>{{-- /qd-card --}}

                  </div>
                </div>

{{-- ── Asset Detail Side Panel ── --}}
<div class="asset-backdrop" id="assetBackdrop"></div>
<div class="asset-panel" id="assetDetailPanel">

  <div class="asset-panel-hdr">
    <div class="d-flex align-items-center gap-3">
      <div class="asset-panel-title" id="ap-bill-number">Bill Details</div>
      <span class="qd-badge qd-badge-default" id="ap-status-badge">—</span>
      <span class="qd-badge qd-badge-default" id="ap-paid-badge">—</span>
                      </div>
    <div class="asset-panel-actions">
      <button class="btn btn-sm btn-outline-secondary edit-btn"><i class="bi bi-pencil me-1"></i>Edit</button>
      <button class="btn btn-sm btn-outline-primary print-btn"><i class="bi bi-printer me-1"></i>Print</button>
      <button class="btn btn-sm btn-outline-danger pdf-btn"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</button>
      <button class="asset-close-btn" id="closeAssetPanel"><i class="bi bi-x-lg"></i></button>
                    </div>
                  </div>

  <div class="asset-panel-body">

    {{-- Payments Made toggle --}}
    <div class="ap-card payment_design" style="border-color:#e5e9f0;">
      <div class="d-flex align-items-center justify-content-between">
        <span class="pay-toggle payment-toggle">
          <i class="bi bi-credit-card me-1"></i>Payments Made
          <span class="bill_count badge bg-primary ms-1">0</span>
                        </span>
                      </div>
                    <div class="payment-table mt-3" style="display:none;">
                      <div class="table-responsive">
          <table class="ap-table">
            <thead>
              <tr>
                <th>Date</th><th>Payment #</th><th>Reference #</th>
                <th>Status</th><th>Mode</th><th class="text-end">Amount</th>
                            </tr>
                          </thead>
            <tbody class="payment-body"></tbody>
                        </table>
                      </div>
                    </div>
                  </div>

    {{-- Bill Info --}}
    <div class="ap-card">
      <div class="ap-card-title"><i class="bi bi-receipt"></i>Bill Information</div>
      <div class="ap-grid">
        <div class="ap-item"><div class="ap-lbl">Bill Number</div><div class="ap-val" id="ap-bill-no">—</div></div>
        <div class="ap-item"><div class="ap-lbl">Order Number</div><div class="ap-val" id="ap-order-no">—</div></div>
        <div class="ap-item"><div class="ap-lbl">Bill Date</div><div class="ap-val" id="ap-bill-date">—</div></div>
        <div class="ap-item"><div class="ap-lbl">Due Date</div><div class="ap-val" id="ap-due-date">—</div></div>
        <div class="ap-item"><div class="ap-lbl">Payment Terms</div><div class="ap-val" id="ap-payment-terms">—</div></div>
        <div class="ap-item"><div class="ap-lbl">Zone / Branch</div><div class="ap-val" id="ap-zone-branch">—</div></div>
                        </div>
                    </div>

    {{-- Vendor --}}
    <div class="ap-card">
      <div class="ap-card-title"><i class="bi bi-person-circle"></i>Vendor Details</div>
      <div class="ap-grid">
        <div class="ap-item"><div class="ap-lbl">Vendor Name</div><div class="ap-val ap-vendor-name">—</div></div>
        <div class="ap-item"><div class="ap-lbl">Phone</div><div class="ap-val ap-vendor-phone">—</div></div>
        <div class="ap-item" style="grid-column:1/-1;">
          <div class="ap-lbl">Address</div>
          <div class="ap-val ap-vendor-addr" style="font-weight:400; color:#475569;">—</div>
                      </div>
                    </div>
                  </div>

    {{-- Items --}}
    <div class="ap-card">
      <div class="ap-card-title"><i class="bi bi-list-ul"></i>Items & Description</div>
      <div style="overflow-x:auto;">
        <table class="ap-table">
                      <thead>
                        <tr>
              <th>Item</th><th>Customer</th><th>Qty</th>
              <th>Rate</th><th>GST</th><th>GST Amt</th><th class="text-end">Amount</th>
                        </tr>
                      </thead>
          <tbody id="ap-items"><tr><td colspan="7" class="text-center text-muted py-3">No items</td></tr></tbody>
                    </table>
      </div>
                  </div>

    {{-- Totals --}}
    <div class="ap-card">
      <div class="ap-card-title"><i class="bi bi-calculator"></i>Totals</div>
      <div class="ap-totals">
        <div class="ap-total-row"><span>Sub Total</span><span id="ap-sub-total">₹0.00</span></div>
        <div class="ap-total-row"><span>Discount</span><span id="ap-discount">₹0.00</span></div>
        <div class="gst-breakdown"></div>
        <div class="ap-total-row"><span>TDS</span><span id="ap-tds">₹0.00</span></div>
        <div class="ap-total-row grand"><span>Total</span><span id="ap-grand-total">₹0.00</span></div>
                    </div>
                  </div>

    {{-- Notes --}}
    <div class="ap-card">
      <div class="ap-card-title"><i class="bi bi-sticky"></i>Notes</div>
      <div id="ap-notes" class="text-muted" style="font-size:13px;">—</div>
                  </div>

    {{-- Documents --}}
    <div class="ap-card">
      <div class="ap-card-title"><i class="bi bi-paperclip"></i>Documents</div>
      <div class="upload_doc d-flex flex-wrap gap-2 mt-1"></div>
                  </div>

              </div>
            </div>

{{-- ── NEFT Modal (preserved) ── --}}
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
                <label class="form-label required" style="font-size:12px;font-weight:600;">SI. No</label>&nbsp;&nbsp;<span style="font-size:10px;color:red;" class="error_serial errorss"></span>
                                        <input type="hidden" id="id" name="id">
                <input type="hidden" id="branch_id" name="branch_id" value="{{ $admin?->branch_id }}">
                <input type="hidden" id="users_id" name="users_id" value="{{ $admin?->id }}">
                                        <input type="hidden" id="bill_id" name="bill_id">
                <input type="text" class="form-control" style="height:36px;border-radius:10px;border:solid 1px #d3d3d3;background:#fff;color:#505050!important;width:100%;padding-left:6px;font-size:12px" required name="serial_number" id="serial_number" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                <label class="form-label required" style="font-size:12px;font-weight:600;">Created by:</label>&nbsp;&nbsp;<span style="font-size:10px;color:red;" class="error_created_by errorss"></span>
                <input type="text" class="form-control" id="created_by" name="created_by" style="height:36px;border-radius:10px;border:solid 1px #d3d3d3;background:#fff;color:#505050!important;width:100%;padding-left:6px;" placeholder="Created by" readonly value="{{ $admin?->user_fullname ?? '' }}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                  <label class="form-label required" style="font-size:12px;font-weight:600;">Vendor/ Employee Name:</label>&nbsp;&nbsp;<span style="font-size:10px;color:red;" class="error_special errorss"></span>
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
                <label class="form-label required" style="font-size:12px;font-weight:600;">Nature of Payment:</label>&nbsp;&nbsp;<span style="font-size:10px;color:red;" class="error_nature_payment errorss"></span>
                <select class="form-control" id="nature_payment" name="nature_payment" style="height:42px;">
                                        <option value="">Select Status</option>
                  <option value="Travell Allowance">Travell Allowance</option>
                  <option value="Expense">Expense</option>
                  <option value="Imprest">Imprest</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-3">
                <label class="form-label required" style="font-size:12px;font-weight:600;">UTR Number:</label>&nbsp;&nbsp;<span style="font-size:10px;color:red;" class="error_utr_number errorss"></span>
                <input type="text" class="form-control" id="utr_number" name="utr_number" style="height:36px;border-radius:10px;border:solid 1px #d3d3d3;background:#fff;color:#505050!important;width:100%;padding-left:6px;" placeholder="UTR Number">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                <label class="form-label required" style="font-size:12px;font-weight:600;">PAN Number:</label>&nbsp;&nbsp;<span style="font-size:10px;color:red;" class="error_pan_number errorss"></span>
                <input type="text" class="form-control" id="pan_number" name="pan_number" style="height:36px;border-radius:10px;border:solid 1px #d3d3d3;background:#fff;color:#505050!important;width:100%;padding-left:6px;" readonly placeholder="PAN Number">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                <label class="form-label required" style="font-size:12px;font-weight:600;">Account Number:</label>&nbsp;&nbsp;<span style="font-size:10px;color:red;" class="error_account_number errorss"></span>
                <input type="text" class="form-control" id="account_number" name="account_number" style="height:36px;border-radius:10px;border:solid 1px #d3d3d3;background:#fff;color:#505050!important;width:100%;padding-left:6px;" readonly placeholder="Account Number">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                <label class="form-label required" style="font-size:12px;font-weight:600;">IFSC Code:</label>&nbsp;&nbsp;<span style="font-size:10px;color:red;" class="error_ifsc_code errorss"></span>
                <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" style="height:36px;border-radius:10px;border:solid 1px #d3d3d3;background:#fff;color:#505050!important;width:100%;padding-left:6px;" readonly placeholder="IFSC Code">
                                </div>
                            </div>
                        </div>
                        <div class="row">
            <h4>Account</h4>
            <table class="table" style="width:100%;">
                                <thead>
                                  <tr>
                  <th style="width:20%;">Invoice Amount</th>
                  <th style="width:20%;">Already Paid</th>
                  <th style="width:20%;">TDS</th>
                  <th style="width:20%;">Tax Amount</th>
                  <th style="width:20%;">Only Payable</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                  <td><input type="text" class="form-control" id="invoice_amount" name="invoice_amount" style="height:36px;border-radius:10px;border:solid 1px #d3d3d3;background:#fff;color:#505050!important;width:100%;padding-left:6px;" readonly placeholder="Invoice Amount"></td>
                  <td><input type="text" class="form-control" id="already_paid" name="already_paid" style="height:36px;border-radius:10px;border:solid 1px #d3d3d3;background:#fff;color:#505050!important;width:100%;padding-left:6px;" readonly placeholder="Already Paid"></td>
                  <td>
                                        <div class="col-md-12 tax-dropdown-wrapper tds-tax-section" style="width:170px">
                      <input type="text" class="form-control tax-search-input" name="tds_tax_name" readonly placeholder="Select a Tax">
                                          <input type="hidden" name="tds_tax_selected" class="selected-tds-tax" id="tds_tax_value">
                                          <input type="hidden" name="tds_tax_id" class="tds-tax-id">
                                          <div class="dropdown-menu tax-dropdown">
                        <div class="inner-search-container"><input type="text" class="tax-inner-search" placeholder="Search..."></div>
                                            <div class="tax-list"></div>
                                            <div class="manage-tds-link">⚙️ Manage TDS</div>
                                        </div>
                                      </div>
                                    </td>
                  <td><input type="text" class="form-control" id="tax_amount" name="tax_amount" style="height:36px;border-radius:10px;border:solid 1px #d3d3d3;background:#fff;color:#505050!important;width:100%;padding-left:6px;" readonly placeholder="Tax Amount"></td>
                  <td><input type="text" class="form-control" id="only_payable" name="only_payable" style="height:36px;border-radius:10px;border:solid 1px #d3d3d3;background:#fff;color:#505050!important;width:100%;padding-left:6px;" readonly placeholder="Only Payable"></td>
                                  </tr>
                                </tbody>
                              </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-3">
                <label class="form-label required" style="font-size:12px;font-weight:600;">Payment Status:</label>&nbsp;&nbsp;<span style="font-size:10px;color:red;" class="error_payment_status errorss"></span>
                <select class="form-control" id="payment_status" name="payment_status" style="height:42px;">
                                        <option value="">Select Status</option>
                  <option value="Success">Success</option>
                  <option value="Failed">Failed</option>
                  <option value="Return">Return</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                <label class="form-label required" style="font-size:12px;font-weight:600;">Payment Method:</label>&nbsp;&nbsp;<span style="font-size:10px;color:red;" class="error_payment_method errorss"></span>
                                    <div class="row">
                                        <div class="col-sm-6">
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="payment_neft" name="payment_method[]" value="NEFT"><label class="form-check-label" for="payment_neft" style="font-size:12px;">NEFT</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="payment_rtgs" name="payment_method[]" value="RTGS"><label class="form-check-label" for="payment_rtgs" style="font-size:12px;">RTGS</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="payment_cheque" name="payment_method[]" value="Cheque"><label class="form-check-label" for="payment_cheque" style="font-size:12px;">Cheque</label></div>
                                        </div>
                                        <div class="col-sm-6">
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="payment_dd" name="payment_method[]" value="DD"><label class="form-check-label" for="payment_dd" style="font-size:12px;">DD</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="payment_idhs" name="payment_method[]" value="IDhS"><label class="form-check-label" for="payment_idhs" style="font-size:12px;">IDhS</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="payment_internet" name="payment_method[]" value="Internet Banking"><label class="form-check-label" for="payment_internet" style="font-size:12px;">Internet Banking</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="payment_card" name="payment_method[]" value="Card Swipe"><label class="form-check-label" for="payment_card" style="font-size:12px;">Card Swipe</label></div>
                                            </div>
                                            </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-sm-4 mb-4 bank_uploads">
                            <label class="form-label">Bank Proof</label><br>
              <label class="upload-btn" for="bank_upload"><i class="fas fa-upload"></i> Upload File</label>
                            <input type="file" id="bank_upload" name="bank_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
              <ul id="preview_bank_upload" style="list-style:none;padding-left:0;"></ul>
                        </div>
                        <div class="col-sm-4 mb-4">
                            <label class="form-label">Invoice Upload</label><br>
              <label class="upload-btn" for="invoice_upload"><i class="fas fa-upload"></i> Upload File</label>
                            <input type="file" id="invoice_upload" name="invoice_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
              <ul id="preview_invoice_upload" style="list-style:none;padding-left:0;"></ul>
                        </div>
                        <div class="col-sm-4 mb-4 pan_upload">
                            <label class="form-label">PAN Upload</label><br>
              <label class="upload-btn" for="pan_upload"><i class="fas fa-upload"></i> Upload File</label>
                            <input type="file" id="pan_upload" name="pan_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
              <ul id="preview_pan_upload" style="list-style:none;padding-left:0;"></ul>
                        </div>
                        <div class="col-sm-4 mb-4">
                            <label class="form-label">PO Attachment</label><br>
              <label class="upload-btn" for="po_upload"><i class="fas fa-upload"></i> Upload File</label>
                            <input type="file" id="po_upload" name="po_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
              <ul id="preview_po_upload" style="list-style:none;padding-left:0;"></ul>
                        </div>
                        <div class="col-sm-4 mb-4">
              <label class="form-label">PO Signed Copy</label><br>
              <label class="upload-btn" for="po_signed_upload"><i class="fas fa-upload"></i> Upload File</label>
                            <input type="file" id="po_signed_upload" name="po_signed_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
              <ul id="preview_po_signed_upload" style="list-style:none;padding-left:0;"></ul>
                        </div>
                        <div class="col-sm-4 mb-4">
              <label class="form-label">PO Delivery Copy</label><br>
              <label class="upload-btn" for="po_delivery_upload"><i class="fas fa-upload"></i> Upload File</label>
                            <input type="file" id="po_delivery_upload" name="po_delivery_upload[]" multiple accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1">Max 5 files, 10MB each</small>
              <ul id="preview_po_delivery_upload" style="list-style:none;padding-left:0;"></ul>
                        </div>
                        </div>
                        <div class="row">
            <h5>Bill Upload</h5>
            <div class="col-md-4" id="documentPreviewContainer" style="display:flex;"></div>
                          </div>
          <div class="vendor_upload" style="display:flex;">
            <div><h6>Bank upload</h6><div id="documentPanview"></div></div>
            <div><h6>Pan upload</h6><div id="documentBankview"></div></div>
                        </div>
          <div class="modal-footer" style="gap:20px">
            <button type="button" style="height:34px;width:133px;font-size:12px;" id="close_button" class="btn btn-outline-danger close-modal-tcs" data-bs-dismiss="modal">Close</button>
            <button type="submit" id="submit-neft-datas" style="height:34px;width:133px;font-size:12px;background:#6a6ee4;--bs-btn-border-color:#6a6ee4;" class="btn btn-primary">Submit</button>
                          </div>
                      </form>
                    </div>
                    </div>
                </div>
              </div>

{{-- ── Document viewer modal ── --}}
<div class="modal fade" id="documentModal1" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:999999;">
                  <div class="modal-dialog modal-xl" role="document">
                      <div class="modal-content">
      <div class="modal-header" style="background:#080fd399;height:0;">
        <h5 class="modal-title" style="color:#fff;font-size:12px;">Document Viewer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background:#fff;"></button>
                          </div>
                          <div class="row">
                          <div class="col-sm-3"><br>
          <div class="btn-group-vertical w-100" id="image_pdfs" style="margin-left:11px;"></div>
                                  </div>
        <div class="col-sm-9"><embed id="pdfmain" src="" width="100%" height="600px" /></div>
                                  </div>
                                  </div>
                          </div>
                      </div>

{{-- Import Bill Modal --}}
              <div class="modal fade" id="importbillModal" tabindex="-1" aria-labelledby="importbillModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="importbillModalLabel">Import Bill</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p>Step 1: Download the Excel Template</p>
        <a href="{{ url('/bill-download-template') }}" class="btn btn-success mb-3">Download Template</a>
                        <p>Step 2: Upload the Filled File</p>
                        <div class="d-flex align-items-center gap-2">
                          <label class="btn btn-outline-primary position-relative mb-2">
                            <i class="bi bi-upload"></i> Upload File
                            <input type="file" name="file" class="d-none" id="importFileInput" accept=".xlsx,.csv" required>
                          </label>
                        </div>
        <div id="fileNameDisplay" class="text-muted" style="font-size:0.85rem;"></div>
                      </div>
                      <div class="modal-footer">
        <button type="submit" class="btn btn-primary import-btn">Upload &amp; Import</button>
                      </div>
                  </div>
                </div>
              </div>

{{-- PDF Preview Modal --}}
              <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document" style="max-width:90%;">
                  <div class="modal-content">
                    <div class="modal-header position-relative">
                      <h5 class="modal-title">Bill Preview</h5>
        <button type="button" class="modal-close-fallback" aria-label="Close preview"
          style="position:absolute;right:1rem;top:0.6rem;font-size:1.4rem;background:none;border:0;">&times;</button>
                    </div>
                    <div class="modal-body p-0">
                      <iframe id="pdfFrame" src="" width="100%" height="600px" style="border:none;"></iframe>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

<script>
    $(document).ready(function () {
  // Reset date display to "All Dates" on page load
  $('#data_values').text('All Dates');
  $('.data_values').val('');

  // ── Toggle Stats ──
  $('#toggleStats').on('click', function () {
    const $s = $('#statsSection'), $i = $('#statsChevron');
    $s.is(':visible') ? ($s.slideUp(200), $i.removeClass('bi-chevron-up').addClass('bi-chevron-down'))
                      : ($s.slideDown(200), $i.removeClass('bi-chevron-down').addClass('bi-chevron-up'));
  });

  // ── Toggle Filters ──
  $('#toggleFilters').on('click', function () {
    const $s = $('#filtersSection'), $i = $('#filtersChevron');
    $s.is(':visible') ? ($s.slideUp(200), $i.removeClass('bi-chevron-up').addClass('bi-chevron-down'))
                      : ($s.slideDown(200), $i.removeClass('bi-chevron-down').addClass('bi-chevron-up'));
  });

  // ── Stat card filter ──
  let statFilter = '';
  $(document).on('click', '.qd-stat-card[data-stat-filter]', function () {
    $('.qd-stat-card').removeClass('qd-stat-active');
    $(this).addClass('qd-stat-active');
    statFilter = $(this).data('stat-filter');
    loadBill();
  });

  // ── Dropdown open ──
  $(document).on('click', '.dropdown-search-input', function (e) {
    e.stopPropagation();
    $('.dropdown-menu.tax-dropdown').hide();
    const $input = $(this);
    let $dropdown = $input.data('dropdown');
    if (!$dropdown) {
      $dropdown = $input.siblings('.dropdown-menu').clone(true);
      $('body').append($dropdown);
      $input.data('dropdown', $dropdown);
    }
    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
    const offset = $input.offset();
    $dropdown.css({ position:'absolute', top: offset.top + $input.outerHeight(), left: offset.left, width: $input.outerWidth(), zIndex: 9999 }).show();
    $dropdown.find('.inner-search').focus();
  });

  $(document).on('keyup', '.inner-search', function () {
    const v = $(this).val().toLowerCase();
    $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1);
        });
    });

  $(document).on('click', '.dropdown-list.multiselect div', function (e) {
    e.stopPropagation();
    $(this).toggleClass('selected');
    updateMultiSelection($(this).closest('.tax-dropdown'));
  });
  $(document).on('click', '.select-all', function (e) {
    e.stopPropagation();
    const $d = $(this).closest('.tax-dropdown');
    $d.find('.dropdown-list.multiselect div').addClass('selected');
    updateMultiSelection($d);
  });
  $(document).on('click', '.deselect-all', function (e) {
    e.stopPropagation();
    const $d = $(this).closest('.tax-dropdown');
    $d.find('.dropdown-list.multiselect div').removeClass('selected');
    updateMultiSelection($d);
  });
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.tax-dropdown-wrapper').length && !$(e.target).closest('.tax-dropdown').length) {
      $('.dropdown-menu.tax-dropdown').hide();
    }
  });
  $(document).on('click', '.dropdown-menu.tax-dropdown', function (e) { e.stopPropagation(); });

  function updateMultiSelection($dropdown) {
    const wrapper = $dropdown.data('wrapper');
    if (!wrapper) return;
    const selectedItems = [], selectedIds = [];
    $dropdown.find('.dropdown-list.multiselect div.selected').each(function () {
      selectedItems.push($(this).text().trim());
      selectedIds.push($(this).data('id'));
    });
    const $visibleInput = wrapper.find('.dropdown-search-input');
    const $hiddenInput  = wrapper.find('input[type="hidden"]');
    $visibleInput.val(selectedItems.join(', '));
    $hiddenInput.val(selectedIds.join(','));
    $hiddenInput.trigger('click');
  }

  // ── Populate dropdowns ──
  const TblZonesModel = @json($TblZonesModel);
  const Tblcompany    = @json($Tblcompany);
  const Tblvendor     = @json($Tblvendor);
  const Tblaccount    = @json($Tblaccount);

  (Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data||[])).forEach(z => {
    $('.zone-list').append(`<div data-id="${z.id}">${z.name}</div>`);
  });
  (Tblcompany.data||[]).forEach(c => {
    $('.company-list').append(`<div data-value="${c.company_name}" data-id="${c.id}">${c.company_name}</div>`);
  });
  Tblvendor.forEach(v => {
    $('.vendor-list').append(`<div data-value="${v.display_name}" data-id="${v.id}">${v.display_name}</div>`);
  });
  Tblaccount.forEach(a => {
    $('.account-list').append(`<div data-value="${a.name}" data-id="${a.id}">${a.name}</div>`);
  });

  // Zone → Branch fetch
  $('.zone_id').on('click', function () {
    var id = $('.zone_id').val();
    if (!id) return;
    let formData = new FormData();
    formData.append('id', id);
      $.ajax({
      url: '{{ route("superadmin.getbranchfetch") }}',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
      success: function (res) {
        if (res.branch !== '') {
          $('.branch-list div').remove();
          res.branch.forEach(b => $('.branch-list').append(`<div data-id="${b.id}">${b.name}</div>`));
        }
          }
      });
    });
  $('.zone-list div').on('click', function () {
    $('.branch-search-input').val('');
    $('.branch_id').val('');
  });

  // ── Filter state & loader ──
  let filters = {
    date_from:'', date_to:'', zone_id:'', zone_name:'',
    branch_id:'', branch_name:'', company_id:'', company_name:'',
    vendor_id:'', vendor_name:'', nature_id:'', nature_name:'',
    status_id:'', status_name:'', universal_search:''
  };

  function renderSummary() {
    let html = '';
    if (filters.date_from && filters.date_to)
      html += `<span class="filter-badge remove-icon" data-type="date"><i class="bi bi-calendar3 me-1"></i>${filters.date_from} → ${filters.date_to} &times;</span>`;
    if (filters.company_id)
      html += `<span class="filter-badge remove-icon" data-type="company"><i class="bi bi-building me-1"></i>${filters.company_name} &times;</span>`;
    if (filters.zone_id)
      html += `<span class="filter-badge remove-icon" data-type="zone"><i class="bi bi-geo-alt me-1"></i>${filters.zone_name} &times;</span>`;
    if (filters.branch_id)
      html += `<span class="filter-badge remove-icon" data-type="branch"><i class="bi bi-diagram-3 me-1"></i>${filters.branch_name} &times;</span>`;
    if (filters.vendor_id)
      html += `<span class="filter-badge remove-icon" data-type="vendor"><i class="bi bi-person me-1"></i>${filters.vendor_name} &times;</span>`;
    if (filters.nature_id)
      html += `<span class="filter-badge remove-icon" data-type="nature"><i class="bi bi-tag me-1"></i>${filters.nature_name} &times;</span>`;
    if (filters.status_id)
      html += `<span class="filter-badge remove-icon" data-type="status"><i class="bi bi-circle-half me-1"></i>${filters.status_name} &times;</span>`;
    if (filters.universal_search)
      html += `<span class="filter-badge remove-icon" data-type="search"><i class="bi bi-search me-1"></i>${filters.universal_search} &times;</span>`;
    if (html) html += `<span class="filter-badge filter-clear" id="clear-all">Clear all</span>`;
    $('#filter-summary').html(html);
  }

  function loadBill(page = 1, perPage = $('#per_page').val()) {
      $.ajax({
      url: '{{ route("superadmin.getasset") }}',
      type: 'GET',
      data: {
        per_page: perPage, page,
        date_from: filters.date_from, date_to: filters.date_to,
        zone_id: filters.zone_id, branch_id: filters.branch_id,
        company_id: filters.company_id, vendor_id: filters.vendor_id,
        nature_id: filters.nature_id, status_name: filters.status_name,
        universal_search: filters.universal_search,
        stat_filter: statFilter
      },
      success: function (data) {
        if (typeof data === 'object' && data.html !== undefined) {
          $('#bill-body').html(data.html);
          if (data.stats) {
            $.each(data.stats, function(key, val) {
              var $el = $('[data-stat-key="' + key + '"]');
              if (key === 'total_amount') {
                $el.text('₹' + parseFloat(val).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
              } else {
                $el.text(val);
              }
            });
          }
        } else {
          $('#bill-body').html(data);
        }
        renderSummary();
      }
    });
  }

  // ── Multiselect → filter wire-up (same pattern as quotation dashboard) ──
  function setupMultiSelect(selectorInput, selectorHidden) {
    var ns = 'click.ms' + selectorHidden.replace(/[^a-zA-Z0-9]/g, '');
    $(document).off(ns, selectorHidden).on(ns, selectorHidden, function () {
      const selectedIds  = $(this).val();
      const selectedText = $(selectorInput).val();
      if (selectorHidden === '.zone_id')         { filters.zone_id = selectedIds; filters.zone_name = selectedText; }
      else if (selectorHidden === '.branch_id')  { filters.branch_id = selectedIds; filters.branch_name = selectedText; }
      else if (selectorHidden === '.company_id') { filters.company_id = selectedIds; filters.company_name = selectedText; }
      else if (selectorHidden === '.vendor_id')  { filters.vendor_id = selectedIds; filters.vendor_name = selectedText; }
      else if (selectorHidden === '.nature_id')  { filters.nature_id = selectedIds; filters.nature_name = selectedText; }
      else if (selectorHidden === '.status_id')  { filters.status_id = selectedIds; filters.status_name = selectedText; }
      loadBill();
    });
  }
  setupMultiSelect('.zone-search-input', '.zone_id');
  setupMultiSelect('.branch-search-input', '.branch_id');
  setupMultiSelect('.company-search-input', '.company_id');
  setupMultiSelect('.vendor-search-input', '.vendor_id');
  setupMultiSelect('.nature-search-input', '.nature_id');
  setupMultiSelect('.status-search-input', '.status_id');

  $('.universal_search').on('keyup', function () {
    filters.universal_search = $(this).val();
    loadBill();
  });

  // Date change — triggered by quotation_search.js cb()
  $('.data_values').on('change', function () {
    let dateRange = $(this).val();
    if (dateRange && dateRange.includes(' to ')) {
      let parts = dateRange.split(' to ');
      filters.date_from = parts[0].trim();
      filters.date_to   = parts[1].trim();
    } else {
      filters.date_from = '';
      filters.date_to   = '';
    }
    loadBill();
  });

  $('#filter-summary').on('click', '.remove-icon', function () {
    const type = $(this).data('type');
    if (type === 'date')    { filters.date_from=''; filters.date_to=''; $('#data_values').text('All Dates'); $('.data_values').val(''); }
    else if (type==='zone')    { filters.zone_id=''; filters.zone_name=''; $('.zone_id').val(''); $('.zone-search-input').val(''); $('.zone-list div').removeClass('selected'); }
    else if (type==='branch')  { filters.branch_id=''; filters.branch_name=''; $('.branch_id').val(''); $('.branch-search-input').val(''); $('.branch-list div').removeClass('selected'); }
    else if (type==='company') { filters.company_id=''; filters.company_name=''; $('.company_id').val(''); $('.company-search-input').val(''); $('.company-list div').removeClass('selected'); }
    else if (type==='vendor')  { filters.vendor_id=''; filters.vendor_name=''; $('.vendor_id').val(''); $('.vendor-search-input').val(''); $('.vendor-list div').removeClass('selected'); }
    else if (type==='nature')  { filters.nature_id=''; filters.nature_name=''; $('.nature_id').val(''); $('.nature-search-input').val(''); $('.account-list div').removeClass('selected'); }
    else if (type==='status')  { filters.status_id=''; filters.status_name=''; $('.status_id').val(''); $('.status-search-input').val(''); $('.status-list div').removeClass('selected'); }
    else if (type==='search')  { filters.universal_search=''; $('.universal_search').val(''); }
    loadBill();
  });
  $('#filter-summary').on('click', '#clear-all', function () {
    filters = { date_from:'', date_to:'', zone_id:'', zone_name:'', branch_id:'', branch_name:'', company_id:'', company_name:'', vendor_id:'', vendor_name:'', nature_id:'', nature_name:'', status_id:'', status_name:'', universal_search:'' };
    statFilter = '';
    $('.zone-search-input,.branch-search-input,.company-search-input,.vendor-search-input,.nature-search-input,.status-search-input').val('');
    $('.zone_id,.branch_id,.company_id,.vendor_id,.nature_id,.status_id').val('');
    $('.data_values').val(''); $('#data_values').text('All Dates');
    $('.universal_search').val('');
    $('.dropdown-list div').removeClass('selected');
    $('.qd-stat-card').removeClass('qd-stat-active'); $('.qd-stat-card[data-stat-filter=""]').first().addClass('qd-stat-active');
    loadBill();
  });

  $(document).on('click', '.pagination a', function (e) {
      e.preventDefault();
    const params = new URLSearchParams($(this).attr('href').split('?')[1]);
    loadBill(params.get('page') || 1, $('#per_page').val());
  });
  $(document).on('change', '#per_page', function () { loadBill(1, $(this).val()); });

  // ── Asset panel open/close ──
  function openPanel() {
    $('#assetDetailPanel').addClass('show');
    $('#assetBackdrop').addClass('show');
    $('body').css('overflow', 'hidden');
  }
  function closePanel() {
    $('#assetDetailPanel').removeClass('show');
    $('#assetBackdrop').removeClass('show');
    $('body').css('overflow', 'auto');
  }
  $('#closeAssetPanel, #assetBackdrop').on('click', closePanel);
  $('#assetDetailPanel').on('click', function (e) { e.stopPropagation(); });
  $(document).on('keyup', function (e) { if (e.key === 'Escape') closePanel(); });

  // ── Row click → populate and open panel ──
  $(document).on('click', '.customer-row', function (e) {
    if ($(e.target).is('input[type="checkbox"]')) return;
    if ($(e.target).closest('.neft_modal').length) return;
    if ($(e.target).closest('.vendor_link').length) return;
    if ($(e.target).closest('.print-pop-btn').length) return;
    if ($(e.target).closest('.print-po-pop-btn').length) return;
    if ($(e.target).closest('.print-quot-pop-btn').length) return;
    if ($(e.target).closest('.asset-btn').length) return;

    const d = {
      id:            $(this).data('id'),
      bill_number:   $(this).data('bill-number'),
      order_number:  $(this).data('order-number'),
      vendor_name:   $(this).data('vendor-name'),
      vendor:        $(this).data('vendor'),
      vendor_address:$(this).data('vendor-address'),
      bill_date:     $(this).data('bill-date'),
      due_date:      $(this).data('due-date'),
      payment_terms: $(this).data('payment-terms'),
      grand_total:   $(this).data('grand-total'),
      sub_total:     $(this).data('sub-total'),
      note:          $(this).data('note'),
      discount_amount: $(this).data('discount_amount'),
      items:         $(this).data('items'),
      allbill:       $(this).data('allbill'),
      bill_show:     $(this).data('billshow'),
      zone_name:     $(this).data('zone-name') || $(this).find('.qdt-zone-badge').text(),
      branch_name:   $(this).find('.qdt-branch').text(),
      status:        $(this).data('status'),
      bill_status:   $(this).data('bill-status'),
    };
    populatePanel(d);
    openPanel();
  });

  function fmt(amount) {
    if (!amount) return '₹0.00';
    const n = typeof amount === 'string' ? parseFloat(amount.replace(/,/g,'')) : amount;
    return '₹' + n.toLocaleString('en-IN', { minimumFractionDigits:2, maximumFractionDigits:2 });
  }

  function populatePanel(d) {
    $('#assetDetailPanel').data('bill-id', d.id);

    // Header
    $('#ap-bill-number').text(d.bill_number || 'Bill Details');

    const $sb = $('#ap-status-badge');
    const $pb = $('#ap-paid-badge');
    const st = (d.status || '').toLowerCase();
    $sb.attr('class', 'qd-badge ' + (st === 'save' ? 'qd-badge-save' : st === 'draft' ? 'qd-badge-draft' : 'qd-badge-default'))
       .text(d.status || '—');
    const bs = (d.bill_status || '').toLowerCase();
    $pb.attr('class', 'qd-badge ' + (bs.includes('partial') ? 'qd-badge-pending' : bs.includes('paid') ? 'qd-badge-approved' : bs.includes('due') ? 'qd-badge-rejected' : 'qd-badge-default'))
       .text(d.bill_status || '—');

    // Bill Info
    $('#ap-bill-no').text(d.bill_number || '—');
    $('#ap-order-no').text(d.order_number || '—');
    $('#ap-bill-date').text(d.bill_date || '—');
    $('#ap-due-date').text(d.due_date || '—');
    $('#ap-payment-terms').text(d.payment_terms || '—');
    $('#ap-zone-branch').text([d.zone_name||'', d.branch_name||''].filter(Boolean).join(' / ') || '—');
    $('#ap-notes').text(d.note || 'No notes');

    // Vendor
    const addr = d.vendor_address;
    $('.ap-vendor-name').text(d.vendor_name || '—');
    if (addr) {
      $('.ap-vendor-addr').text([addr.address, addr.city, addr.state, addr.country, addr.zip_code].filter(Boolean).join(', '));
      $('.ap-vendor-phone').text(addr.phone || '—');
    }

    // Items
    const items = d.items || [];
    if (items.length) {
      $('#ap-items').html(items.map(item => `
        <tr>
          <td>${item.item_details || '—'}</td>
          <td>${d.vendor_name || '—'}</td>
                <td>${item.quantity || 1}</td>
                <td>${item.rate || '0.00'}</td>
          <td>${item.gst_name || '—'}</td>
          <td>${fmt(item.gst_amount)}</td>
          <td class="text-end">${fmt(item.amount)}</td>
        </tr>`).join(''));
    } else {
      $('#ap-items').html('<tr><td colspan="7" class="text-center text-muted py-3">No items found</td></tr>');
    }

    // Totals
    $('#ap-sub-total').text(fmt(d.sub_total));
    $('#ap-discount').text(fmt(d.discount_amount));
    $('#ap-tds').text(fmt(d.allbill ? d.allbill.tax_amount : 0));
    $('#ap-grand-total').text(fmt(d.grand_total));

    // GST breakdown
    let gstRows = {};
    (items || []).forEach(item => {
                if (item.gst_rate > 0) {
                    if (item.cgst_amount && item.cgst_amount > 0) {
          const r = item.gst_rate / 2;
          gstRows[`CGST ${r}%`] = (gstRows[`CGST ${r}%`]||0) + item.gst_amount/2;
          gstRows[`SGST ${r}%`] = (gstRows[`SGST ${r}%`]||0) + item.gst_amount/2;
                    } else {
          const l = `IGST ${item.gst_rate}%`;
          gstRows[l] = (gstRows[l]||0) + parseFloat(item.gst_amount||0);
                    }
                }
            });
    $('.gst-breakdown').html(Object.entries(gstRows).map(([l,a]) =>
      `<div class="ap-total-row"><span>${l}</span><span>${fmt(a)}</span></div>`).join(''));

    // Documents
    $('.upload_doc').empty();
    if (d.allbill && d.allbill.documents) {
      try {
        JSON.parse(d.allbill.documents).forEach(fn => addDocPreview(fn, '../public/uploads/vendor/bill/', '.upload_doc'));
      } catch(e) {}
    }

    // Payments
    const payments = d.bill_show || [];
                $('.payment-body').empty();
                $('.bill_count').text(payments.length);
    if (payments.length) {
      payments.forEach(p => {
        let status = 'Pending', amount = '';
        if (p.neftget && p.neftget.length) status = p.neftget[0].payment_status || 'Pending';
        if (p.bill_pay_lines && p.bill_pay_lines.length) {
          amount = p.bill_pay_lines[0].amount
            ? `₹${parseFloat(p.bill_pay_lines[0].amount).toLocaleString('en-IN',{minimumFractionDigits:2})}`
                        : '₹0.00';
                  }
                    $('.payment-body').append(`
                      <tr>
            <td>${p.payment_date||'—'}</td><td>${p.payment||'—'}</td>
            <td>${p.reference||'—'}</td>
            <td class="${status==='Success'?'text-success':'text-danger'} fw-semibold">${status}</td>
            <td>${p.payment_mode||'—'}</td><td class="text-end">${amount}</td>
          </tr>`);
                  });
                } else {
      $('.payment-body').html('<tr><td colspan="6" class="text-center text-muted">No payments found</td></tr>');
    }
  }

  function addDocPreview(filename, basePath, containerId) {
    const ext = filename.split('.').pop().toLowerCase();
    const fa  = JSON.stringify([basePath + filename]);
    const ico = ['jpg','jpeg','png','gif','webp'].includes(ext) ? `${basePath}${filename}`
              : ext === 'pdf' ? 'https://cdn-icons-png.flaticon.com/512/337/337946.png'
              : 'https://cdn-icons-png.flaticon.com/512/564/564619.png';
    $(containerId).append(`
      <div class="preview-card documentclk" data-filetype="documents" data-files="${fa.replace(/"/g,'&quot;')}">
        <img src="${ico}" alt="file" style="height:60px;"><div>${filename}</div>
      </div>`);
  }

  // Payment toggle
  $(document).on('click', '.payment-toggle', function () {
    $(this).closest('.payment_design').find('.payment-table').slideToggle(300);
    $(this).toggleClass('active');
  });

  // Edit / PDF / Print buttons from panel
  $(document).on('click', '.edit-btn', function () {
    window.location.href = "{{ route('superadmin.getbillcreate') }}" + "?id=" + $('#assetDetailPanel').data('bill-id');
  });
  $(document).on('click', '.pdf-btn', function () {
    const id = $('#assetDetailPanel').data('bill-id');
    $.ajax({ url:'{{ route("superadmin.getbillpdf") }}', method:'GET', data:{id}, xhrFields:{responseType:'blob'},
      success:function(data){
        const a = document.createElement('a'); a.href = window.URL.createObjectURL(new Blob([data],{type:'application/pdf'}));
        a.download = `bill_${id}.pdf`; document.body.appendChild(a); a.click(); a.remove();
      }
    });
  });
  $(document).on('click', '.print-btn', function () {
    const id = $('#assetDetailPanel').data('bill-id');
    $.ajax({ url:'{{ route("superadmin.getbillprint") }}', method:'GET', data:{id}, xhrFields:{responseType:'blob'},
      success:function(r){ $('#pdfFrame').attr('src', URL.createObjectURL(new Blob([r],{type:'application/pdf'}))); $('#pdfPreviewModal').modal('show'); }
        });
    });

  // Row print buttons (unchanged)
  $(document).on('click', '.print-pop-btn', function () {
    const id = $(this).closest('tr').data('id');
    $.ajax({ url:'{{ route("superadmin.getbillprint") }}', method:'GET', data:{id}, xhrFields:{responseType:'blob'},
      success:function(r){ $('#pdfFrame').attr('src', URL.createObjectURL(new Blob([r],{type:'application/pdf'}))); $('#pdfPreviewModal').modal('show'); }
    });
  });
  $(document).on('click', '.print-po-pop-btn', function () {
    const id = $(this).closest('tr').data('poId');
    $.ajax({ url:'{{ route("superadmin.getpurchaseprint") }}', method:'GET', data:{id}, xhrFields:{responseType:'blob'},
      success:function(r){ $('#pdfFrame').attr('src', URL.createObjectURL(new Blob([r],{type:'application/pdf'}))); $('#pdfPreviewModal').modal('show'); }
    });
  });
  $(document).on('click', '.print-quot-pop-btn', function () {
    const id = $(this).closest('tr').data('quotId');
    $.ajax({ url:'{{ route("superadmin.getquotationprint") }}', method:'GET', data:{id}, xhrFields:{responseType:'blob'},
      success:function(r){ $('#pdfFrame').attr('src', URL.createObjectURL(new Blob([r],{type:'application/pdf'}))); $('#pdfPreviewModal').modal('show'); }
            });
          });

  $(document).on('click', '.modal-close-fallback', function (e) {
    e.preventDefault();
    try { if (typeof bootstrap !== 'undefined') { var m = bootstrap.Modal.getInstance(document.getElementById('pdfPreviewModal')) || new bootstrap.Modal(document.getElementById('pdfPreviewModal')); m.hide(); return; } } catch(err) {}
    if ($.fn && $.fn.modal) { $('#pdfPreviewModal').modal('hide'); return; }
    $('#pdfPreviewModal').hide(); $('.modal-backdrop').remove(); $('body').removeClass('modal-open');
  });

  // Asset confirm button
  $(document).on('click', '.asset-btn', function () {
    const id = $(this).closest('tr').data('id');
    const $td = $(this).closest('td');
    Swal.fire({ title:'Confirm Asset Update', text:'Mark this asset as confirmed?', icon:'question', showCancelButton:true, confirmButtonText:'Yes, confirm!', cancelButtonText:'Cancel' })
      .then(result => {
        if (result.isConfirmed) {
          $.ajax({ url:"{{ route('superadmin.asset_status') }}", type:"POST", data:{id, _token:"{{ csrf_token() }}"},
            success: function(r) {
              if (r.success) { Swal.fire('Updated!', 'Asset status updated.', 'success'); $td.html('<i class="fa fa-check text-success" style="font-size:18px;" title="Asset Confirmed"></i>'); }
              else Swal.fire('Error', 'Something went wrong!', 'error');
            }, error: function () { Swal.fire('Error', 'Server error occurred.', 'error'); }
          });
        }
      });
  });

  // Document viewer modal
  $(document).on('click', '.documentclk', function () {
    $('#documentModal1').modal('show');
    const filesData = $(this).attr('data-files');
    let fileArray = [];
    try { fileArray = JSON.parse(filesData); if (typeof fileArray === 'string') fileArray = JSON.parse(fileArray); } catch(e) { return; }
    if (!Array.isArray(fileArray) || !fileArray.length) return;
    $('#pdfmain').attr('src', fileArray[0]);
    let views = '';
    fileArray.forEach(file => { const fn = file.split('/').pop().trim(); views += `<button style="font-size:11px;" type="button" class="btn btn-primary pdf-doc-btn mb-1" data-filepath="${file}">${fn}</button>`; });
    $('#image_pdfs').html(views);
  });
  $(document).on('click', '.pdf-doc-btn', function () {
    $('.pdf-doc-btn').removeClass('active'); $(this).addClass('active');
    $('#pdfmain').attr('src', $(this).data('filepath'));
  });

  // NEFT Modal
  $(document).on('click', '.neft_modal', function (e) {
                e.stopPropagation();
    $('#NEFTModal').fadeIn();
    $('input[type="file"]').val('');
    ['bank_upload','invoice_upload','pan_upload','po_upload','po_signed_upload','po_delivery_upload'].forEach(id => $(`#preview_${id}`).empty());
    const row = $(this).closest('.customer-row');
    $('#bill_id').val(row.data('id'));
    $('#vendor-search').val(row.data('vendor-name'));
    const vendor = row.data('vendor') || {};
    const bank   = row.data('bank') || {};
    const allbill = row.data('allbill') || {};
    $('#selected-vendor-id').val(vendor.id);
    $('#pan_number').val(vendor.pan_number);
    $('#invoice_amount').val(row.data('sub-total'));
    $('#account_number').val(bank.accont_number);
    $('#ifsc_code').val(bank.ifsc_code);
    $('.tax-search-input').val(allbill.tax_name);
    $('#tds_tax_value').val(allbill.tax_rate);
    $('#tax_amount').val(allbill.tax_amount);
    $('#only_payable').val(row.data('grand-total'));
    $('#already_paid').val(allbill.partially_payment);
  });
  $('.close-modal-tcs').on('click', function () { $('#NEFTModal').fadeOut(); });

  // NEFT submit
  $('#submit-neft-datas').click(function (e) {
    e.preventDefault();
    let isValid = true;
    if ($('#serial_number').val() === '') { $('.error_serial').text('Serial Required'); isValid = false; }
    if ($('#created_by').val() === '') { $('.error_created_by').text('Enter Creator Name'); isValid = false; }
    if ($('#vendor-search').val() === '') { $('.error_special').text('Select Vendor'); isValid = false; }
    if ($('#nature_payment').val() === '') { $('.error_nature_payment').text('Enter Nature Payment'); isValid = false; }
    if ($('#invoice_amount').val() === '') { $('.error_invoice_amount').text('Enter Invoice Amount'); isValid = false; }
    if ($('#payment_status').val() === '') { $('.error_payment_status').text('Select Payment Status'); isValid = false; }
    if ($('#utr_number').val() === '') { $('.error_utr_number').text('Enter UTR Number'); isValid = false; }
    let payChecked = false;
    $('input[name="payment_method[]"]:checked').each(function () { payChecked = true; });
    if (!payChecked) { $('.error_payment_method').text('Select at least one payment method'); isValid = false; }
    const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
    if (!panPattern.test($('#pan_number').val().toUpperCase())) { $('.error_pan_number').text('Invalid PAN Number'); isValid = false; } else { $('.error_pan_number').text(''); }
    if (!isValid) return;
    const $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
    let formData = new FormData($('#neftForm')[0]);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
              $.ajax({
      url: '{{ route("superadmin.saveneft") }}', type:'POST', data:formData, processData:false, contentType:false,
      success: function (r) { toastr.success(r.message); if (r.success) $('#NEFTModal').fadeOut(); $btn.prop('disabled', false).html('Submit'); },
      error: function (err) {
        if (err.responseJSON && err.responseJSON.errors) $.each(err.responseJSON.errors, (k,v) => $('.error_' + k).text(v[0]));
        $btn.prop('disabled', false).html('Submit');
      }
    });
  });

  // Import Bill
  $('#importFileInput').on('change', function () {
    $('#fileNameDisplay').text(this.files[0] ? 'Selected: ' + this.files[0].name : '');
  });
  $(document).on('click', '.import-btn', function (e) {
    e.preventDefault();
    const file = $('#importFileInput')[0].files[0];
    if (!file) { toastr.error('Please select a file.'); return; }
    const $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Importing...');
    let fd = new FormData(); fd.append('file', file);
    $.ajax({
      url:'{{ url("/import-bill") }}', type:'POST', data:fd, processData:false, contentType:false,
      headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(r) {
        if (r.status === 'success') { toastr.success(r.message); $('#importbillModal').modal('hide'); setTimeout(()=>location.reload(), 2000); }
        else { toastr.error('Unexpected server response.'); $btn.prop('disabled',false).html('Upload & Import'); }
      },
      error: function() { toastr.error('Failed to import file.'); $btn.prop('disabled',false).html('Upload & Import'); }
    });
  });

  // File preview
  const previewFiles = {};
  $('input[type="file"]').on('change', function () {
    const id = $(this).attr('id'); const $prev = $(`#preview_${id}`); const files = Array.from(this.files);
    previewFiles[id] = files; $prev.empty();
    files.forEach((f,i) => $prev.append(`<li class="file-preview-item" data-index="${i}"><span>${f.name}</span><span class="remove-file-btn" data-input="${id}" data-index="${i}">❌</span></li>`));
  });
  $(document).on('click', '.remove-file-btn', function () {
    const id = $(this).data('input'), idx = $(this).data('index');
    previewFiles[id].splice(idx, 1);
    const dt = new DataTransfer(); previewFiles[id].forEach(f => dt.items.add(f));
    $(`#${id}`)[0].files = dt.files;
    const $prev = $(`#preview_${id}`); $prev.empty();
    previewFiles[id].forEach((f,i) => $prev.append(`<li class="file-preview-item" data-index="${i}"><span>${f.name}</span><span class="remove-file-btn" data-input="${id}" data-index="${i}">❌</span></li>`));
  });

});

const vendorsaveUrl = "{{ route('superadmin.vendorsave') }}";
const vendorfetch   = "{{ route('superadmin.vendorfetch') }}";
</script>

<script>
// Override cb so "All Dates" preset clears the filter
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
</script>

@include('superadmin.superadminfooter')
</body>
</html>
