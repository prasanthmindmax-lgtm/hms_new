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

 input[type="file"] {
  display: none;
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
                        <i class="bi bi-cash-stack"></i>
                        Bill Made Dashboard
                    </div>
                    <div class="qd-header-actions">
                        <button class="btn btn-sm qd-toggle-btn" id="toggleStats" title="Toggle Stats">
                            <i class="bi bi-bar-chart-line me-1"></i>Stats
                            <i class="bi bi-chevron-up qd-toggle-icon" id="statsChevron"></i>
                        </button>
                        <button class="btn btn-sm qd-toggle-btn" id="toggleFilters" title="Toggle Filters">
                            <i class="bi bi-funnel me-1"></i>Filter
                            <i class="bi bi-chevron-up qd-toggle-icon" id="filtersChevron"></i>
                        </button>
                        <a href="{{ route('superadmin.getbillmadecreate') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>New Payment
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importbillpaymentModal">
                                    <i class="bi bi-upload me-2"></i>Import Bill Payment
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- ── Stats ── --}}
                <div class="qd-stats">
                    <div class="qd-stat-card qd-stat-blue" data-stat-filter="" title="Show all">
                        <div class="qd-stat-icon"><i class="bi bi-cash-stack"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Total Payments</div>
                            <div class="qd-stat-value" id="stat-total">{{ $stats['total'] }}</div>
                            <div class="qd-stat-sub" id="stat-total-amt">₹{{ number_format($stats['total_amount'],2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-green" data-stat-filter="Paid" title="Filter: Paid">
                        <div class="qd-stat-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Paid</div>
                            <div class="qd-stat-value" id="stat-paid">{{ $stats['paid'] }}</div>
                            <div class="qd-stat-sub" id="stat-paid-amt">₹{{ number_format($stats['paid_amount'],2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-orange" data-stat-filter="Partially" title="Filter: Partially">
                        <div class="qd-stat-icon"><i class="bi bi-hourglass-split"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Partially Paid</div>
                            <div class="qd-stat-value" id="stat-partial">{{ $stats['partially'] }}</div>
                            <div class="qd-stat-sub" id="stat-partial-amt">₹{{ number_format($stats['partially_amount'],2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-red" data-stat-filter="Pending" title="Filter: Pending">
                        <div class="qd-stat-icon"><i class="bi bi-clock"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Pending</div>
                            <div class="qd-stat-value" id="stat-pending">{{ $stats['pending'] }}</div>
                            <div class="qd-stat-sub" id="stat-pending-amt">₹{{ number_format($stats['pending_amount'],2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-purple" data-stat-filter="" title="NEFT Payments">
                        <div class="qd-stat-icon"><i class="bi bi-bank"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">NEFT</div>
                            <div class="qd-stat-value" id="stat-neft">{{ $stats['neft'] }}</div>
                            <div class="qd-stat-sub" id="stat-neft-amt">₹{{ number_format($stats['neft_amount'],2) }}</div>
                        </div>
                    </div>
                </div>

                {{-- ── Filters ── --}}
                <div class="qd-filters">
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
                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
                            <label>State</label>
                            <input type="text" class="form-control state-search-input dropdown-search-input" placeholder="Select State" readonly>
                            <input type="hidden" name="state_id" class="state_id">
                            <div class="dropdown-menu tax-dropdown">
                                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search State..."></div>
                                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
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
                    </div>
                    <div class="qd-filter-row">
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
                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
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
                        <div class="qd-filter-group tax-dropdown-wrapper vendor-section">
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
                                    <div data-value="Paid" data-id="1">Paid</div>
                                    <div data-value="Partially" data-id="2">Partially</div>
                                    <div data-value="Pending" data-id="3">Pending</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Search Row ── --}}
                <div class="qd-search-row">
                    <span class="qd-search-label"><i class="bi bi-search me-1"></i>Search</span>
                    <div class="qd-search-wrap">
                        <input type="text" class="universal_search" placeholder="Search payments...">
                    </div>
                </div>

                {{-- ── Applied Filters Bar ── --}}
                <div class="qd-applied-bar" id="filter-summary"></div>

                {{-- ── Table ── --}}
                <div class="qd-table-wrap">
                    <div id="bill-made-body">
                        @include('vendor.partials.table.bill_made_rows', ['billpaylist' => $billpaylist, 'perPage' => $perPage, 'limit_access' => $limit_access])
                    </div>
                </div>

            </div>

            {{-- ── Edit History Popup (admin only) ── --}}
            @if(isset($limit_access) && $limit_access == 1)
            <div class="qdt-history-popup" id="editHistoryPopup">
                <div class="qdt-history-popup-inner">
                    <div class="qdt-history-popup-header">
                        <div><i class="bi bi-clock-history me-2"></i><span id="historyPopupTitle">Edit History</span></div>
                        <button class="qdt-history-popup-close" id="closeHistoryPopup"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="qdt-history-popup-body" id="historyPopupBody"></div>
                </div>
            </div>
            <div class="qdt-history-overlay" id="historyOverlay"></div>
            @endif

              <!-- Bill Detail Modal -->
              <div class="zoho-modal" id="billDetailModal">
                <div class="zoho-modal-content">
                  <div class="zoho-modal-header">
                    <div class="zoho-modal-title">myorder</div>
                    <div class="zoho-modal-actions">
                      <button class="zoho-btn zoho-btn-primary edit-btn">
                        <i class="bi bi-pencil"></i> Edit
                      </button>
                      <button class="zoho-btn zoho-btn-primary print-btn">
                        <i class="bi bi-pencil"></i> Print
                      </button>
                      <button class="zoho-btn zoho-btn-primary pdf-btn">
                        <i class="bi bi-pencil"></i>  PDF
                      </button>
                      <button class="zoho-btn zoho-btn-icon close-modal">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </div>
                  </div>

                  <div class="zoho-modal-body">
                    <!-- Bill Header Section -->
                    <div class="bill-header">
                      <div>
                        <span class="bill-paid-status">Payments Made 1</span>
                      </div>
                      <div>
                        <a href="#" class="bill-pdf-link">Show PDF View</a>
                      </div>
                    </div>
                    <div class="header_container">
                    <div class="header">
                      <div class="corner-banner"></div>
                      <h2>abc priveete limited</h2>
                      <p>Tamil Nadu<br>India<br>9500970811<br><a href="mailto:santhk0708@gmail.com">santhk0708@gmail.com</a></p>
                    </div>

                    <hr />

                    <h3 class="section-title">PAYMENTS MADE</h3>

                    <div class="payment-details">
                      <div class="details-left">
                        <table class="payment-details">
                          <tr>
                            <td class="label">Payment#</td>
                            <td class="value" id="payment_no">3</td>
                          </tr>
                          <tr>
                            <td class="label">Payment Date</td>
                            <td class="value date" id="payment_date">26/07/2025</td>
                          </tr>
                          <tr>
                            <td class="label">Reference Number</td>
                            <td class="value" id="reference"></td>
                          </tr>
                          <tr>
                            <td class="label">Paid To</td>
                            <td class="value"><a href="#" class="link" id="paid_to">old company</a></td>
                          </tr>
                          <tr>
                            <td class="label">Payment Mode</td>
                            <td class="value" id="payment_mode">Cash</td>
                          </tr>
                          <tr>
                            <td class="label">Paid Through</td>
                            <td class="value" id="paid_through">Petty Cash</td>
                          </tr>
                          <tr>
                            <td class="label">Amount Paid In Words</td>
                            <td class="value amount" id="amount_word">Indian Rupee Fifty Thousand Only</td>
                          </tr>
                        </table>
                      </div>
                      <div class="details-right">
                        <div class="amount-box">
                          <span>Amount Paid</span>
                          <h2 id="amount_used">₹1,00,000.00</h2>
                        </div>
                      </div>
                    </div>
                  </div>

                    <div class="bill-divider"></div>
                      <div class="bill_container">


                      <!-- Vendor Address Section -->
                      <div class="zoho-section">
                        <div class="zoho-section-title">VENDOR ADDRESS</div>
                        <div class="zoho-address-block">
                          <div class="vendor-name">Mr. santh k</div>
                          <div class="vendor-street">street ,chemai</div>
                          <div class="vendor-city-state">cennai, Tamil Nadu</div>
                          <div class="vendor-country-zip">India - 600028</div>
                          <div class="vendor-phone">123456789</div>
                        </div>
                      </div>
                    </div>


                    <!-- Items Section -->
                    <div class="zoho-section">
                      <div class="zoho-section-title">PAYMENT FOR</div>
                      <table class="zoho-items-table">
                        <thead>
                          <tr>
                            <th>Bill Number</th>
                            <th>Bill Date</th>
                            <th>Bill Amount</th>
                            <th>Payment Amount</th>

                          </tr>
                        </thead>
                        <tbody id="bill-items">

                        </tbody>
                      </table>
                    </div>
                    <div class="journal-container">
                        <p class="note">Amount is displayed in your base currency <span class="currency-badge">INR</span></p>

                        <div class="entry" id="journal-entries">
                          <h3>Vendor Payment - 4</h3>

                        </div>
                        <div class="table_html">

                        </div>
                      </div>
                      <div class="upload_doc">

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
                                          {{-- <input type="hidden" id="branch_id" name="branch_id" value="{{ $admin?->branch_id  }}"> --}}
                                          <input type="hidden" id="users_id" name="users_id" value="{{ $admin?->id}}">
                                          <input type="hidden" id="bill_id" name="bill_id">
                                          <input type="hidden" id="bill_pay_id" name="bill_pay_id">
                                          <input type="text" class="form-control" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;font-size:12px"  required readonly  value="{{$serial}}" name="serial_number" id="serial_number" autocomplete="off">
                                  </div>
                              </div>
                              <div class="col-sm-3">
                                  <div class="mb-3">
                                      <label class="form-label required" style="font-size: 12px;font-weight: 600;">Created by:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_created_by errorss"></span>
                                      <input type="text" class="form-control" id="created_by" name="created_by" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Created by" readonly value="{{ $admin?->user_fullname .'-'. $admin?->username}}" >
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
                              <!-- <div class="col-sm-3">
                                  <div class="mb-3">
                                      <label class="form-label required" style="font-size: 12px;font-weight: 600;">Vendor/Employee Name:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_vendor errorss"></span>
                                      <input type="text" class="form-control" id="vendor" name="vendor" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" placeholder="Vendor/Employee Name" >
                                  </div>
                              </div> -->
                              <div class="col-sm-3">
                                  <div class="mb-3">
                                      <label class="form-label required" style="font-size: 12px;font-weight: 600;">Nature of Payment:</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_nature_payment errorss"></span>
                                      <input type="text" class="form-control" id="nature_payment" name="nature_payment" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Nature Payment">
                                      {{-- <select class="form-control" id="nature_payment" name="nature_payment" style="height: 42px;">
                                          <option value="">Select Status</option>
                                          <option value="Travell Allowance" >Travell Allowance</option>
                                          <option value="Expense" >Expense</option>
                                          <option value="Imprest" >Imprest</option>
                                          <!-- <option value="NEFT">NEFT</option>
                                          <option value="RTGS">RTGS</option>
                                          <option value="IDR">IDR</option>
                                          <option value="Cheque">Cheque</option>
                                          <option value="DD">DD</option>
                                          <option value="Internet Banking">Internet Banking</option>
                                          <option value="Card Swipe">Card Swipe</option> -->
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

                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Company</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_utr_number errorss"></span>
                                    <input type="text" class="form-control" id="company_name" name="company_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Company" >
                                    <input type="hidden" class="form-control" id="company_id" name="company_id" >
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Zone</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_utr_number errorss"></span>
                                    <input type="text" class="form-control" id="zone_name" name="zone_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Zone" >
                                    <input type="hidden" class="form-control" id="zone_id" name="zone_id" >
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 12px;font-weight: 600;">Branch</label>&nbsp;&nbsp;<span style="font-size:10px; color:red;" class="error_utr_number errorss"></span>
                                    <input type="text" class="form-control" id="branch_name" name="branch_name" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly placeholder="Branch" >
                                    <input type="hidden" class="form-control" id="branch_id" name="branch_id" >
                                </div>
                            </div>
                        </div>
                          <div class="row mb-3">
                            <h4>Account </h4>
                            <div class="table_overload" style="width:100%;overflow:scroll;">
                              <table class="table" style="width: 100%;">
                                  <thead>
                                    <tr>
                                      <th >Base Price</th>
                                      {{-- <th >GST</th>
                                      <th >GST Amount</th> --}}
                                      <th >TDS</th>
                                      <th >Tax Amount</th>
                                      <th >Invoice Amount</th>
                                      <th >Amount Paid</th>
                                      <th >Already Paid</th>
                                      <th >Balance</th>
                                    </tr>
                                  </thead>
                                  <tbody id="account_table">

                                  </tbody>
                                </table>
                            </div>
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
                          <div class="row bill_up">
                            <h5> Bill Upload</h5>
                            <div class="col-md-4" id="documentPreviewContainer" style="display: flex">
                            </div>
                          </div>
                          <div class="vendor_upload" style="display: flex;">
                            <div class="bank_up">
                              <h6>Bank upload</h6>
                              <div id="documentBankview"></div>
                            </div>
                            <div class="pan_up">
                              <h6>Pan upload</h6>
                              <div id="documentPanview" ></div>
                            </div>
                          </div>
                          <div class="modal-footer" style="gap: 20px">
                              <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close_button" class="btn btn-outline-danger close-modal-tcs">Close</button>
                              <button type="submit" id="submit-neft-datas" style="height: 34px;width: 133px;font-size: 12px;background-color: #6a6ee4;--bs-btn-border-color: #6a6ee4;" class="btn btn-primary">Submit</button>
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
                <!--import Modal -->
              <div class="modal fade" id="importbillpaymentModal" tabindex="-1" aria-labelledby="importbillpaymentModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="importbillpaymentModalLabel">Import Bill Made</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p>Step 1: Download the Excel Template</p>
                        <a href="{{ url('/billMade-download-template') }}" class="btn btn-success mb-3">
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
                <!-- Modal -->
                <!-- Place this near the end of <body> -->
                <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-xl" role="document" style="max-width:90%;">
                    <div class="modal-content">
                      <div class="modal-header position-relative">
                        <h5 class="modal-title">Bill Payment Preview</h5>

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
    // Handle row click to show modal with bill details
    $(document).on('click', '.neft_modal', function (e) {
      e.stopPropagation();
      $('#utr_number').val('');
      $('#NEFTModal').fadeIn();

      $('input[type="file"]').val('');
      $('#preview_bank_upload').empty();
      $('#preview_invoice_upload').empty();
      $('#preview_pan_upload').empty();
      $('#preview_po_upload').empty();
      $('#preview_po_signed_upload').empty();
      $('#preview_po_delivery_upload').empty();

      let row = $(this).closest('.customer-row');
      const billData = {
          id: row.data('id'),
          bill_number: row.data('bill-number'),
          order_number: row.data('order-number'),
          vendor_name: row.data('vendor-name'),
          vendor: row.data('vendor'),
          vendor_address: row.data('vendor-address'),
          bill_date: row.data('bill-date'),
          due_date: row.data('due-date'),
          payment_terms: row.data('payment-terms'),
          grand_total: row.data('grand-total'),
          sub_total: row.data('sub-total'),
          note: row.data('note'),
          discount_amount: row.data('discount-amount'), // fixed typo in key
          items: row.data('items'),
          bank: row.data('bank'),
          allbill: row.data('allbill')
      };

      console.log("billData", billData);
      $('#bill_pay_id').val(billData.allbill.id);
      $('#vendor-search').val(billData.allbill.vendor_name);
      $('#selected-vendor-id').val(billData.vendor.id);
      $('#pan_number').val(billData.vendor.pan_number);
      $('#utr_number').val(billData.allbill.bank_statement?.description ?? '');
      // $('#nature_payment').val(bill_lines[0].bill_lines[0].pan_number);
      $('#invoice_amount').val(billData.sub_total);
      $('#account_number').val(
          billData?.bank?.[0]?.accont_number ?? '-'
      );

      $('#ifsc_code').val(
          billData?.bank?.[0]?.ifsc_code ?? '-'
      );
      $('.tax-search-input').val(billData.allbill.tax_name);
      $('#tds_tax_value').val(billData.allbill.tax_rate);
      $('#tax_amount').val(billData.allbill.tax_amount);
      $('#only_payable').val(billData.grand_total);
      $('#zone_id').val(billData.allbill.zone_id);
      $('#zone_name').val(billData.allbill.zone_name);
      $('#branch_id').val(billData.allbill.branch_id);
      $('#branch_name').val(billData.allbill.branch_name);
      $('#company_id').val(billData.allbill.company_id);
      $('#company_name').val(billData.allbill.company_name);
      $('#already_paid').val(billData.allbill.partially_payment);
      var account=billData.items;
      console.log("account",account);
      let html = '';
      let finalString='';
      account.forEach((element, index) => {
        let already_paid=0;
        if(element.aleardypay !==null && element.aleardypay !==''){
          element.aleardypay.forEach((element, index) => {
            if(billData.allbill.id !==element.bill_pay_id){
              already_paid=element.amount;
            }
          });
        }

        html +=`<tr>
                <td>
                  <input type="text" class="form-control" id="invoice_amount" name="account[${index}][base_price]" style="width: 80px  !important;height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.bill.sub_total_amount}" placeholder="Invoice Amount">
                </td>
                <td>
                  <div class="" style="width:100px  !important">
                    <div class="col-md-12 tax-dropdown-wrapper tds-tax-section" style="width:100px  !important">
                      <input type="text" class="form-control tax-search-input" name="account[${index}][tds_tax_name]" readonly placeholder="Select a Tax" value="${element.bill.tax_name}" readonly>
                      <input type="hidden" name="account[${index}][tds_tax_selected]" class="selected-tds-tax" value="${element.bill.tax_rate}" id="tds_tax_value">
                      <input type="hidden" name="account[${index}][tds_tax_id]" class="tds-tax-id" value="${element.bill.tds_tax_id}">
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
                  <div class="" style="width:100px">
                    <input type="text" class="form-control" id="tax_amount" name="account[${index}][tax_amount]" style="width: 80px  !important;height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="${element.bill.tax_amount}" readonly placeholder="Already Paid">
                  </div>
                </td>
                <td>
                  <div class="" style="width:100px">
                    <input type="text" class="form-control" id="invoice_amount" name="account[${index}][invoice_amount]" style="width:80px  !important;height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;padding-left: 6px;" readonly value="${element.bill.grand_total_amount}" placeholder="Invoice Amount">
                    <input type="hidden" class="form-control" id="invoice_amount" name="account[${index}][bill_id]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.bill_id}" placeholder="Invoice Amount">
                    <input type="hidden" class="form-control" id="invoice_amount" name="account[${index}][bill_pay_id]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.bill_pay_id}" placeholder="Invoice Amount">
                    <input type="hidden" class="form-control" id="invoice_amount" name="account[${index}][bill_pay_lines_id]" style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.id}" placeholder="Invoice Amount">
                  </div>
                </td>
                 <td>
                  <div class="" style="width:100px">
                    <input type="text" class="form-control" id="amount_paid" name="account[${index}][amount_paid]" style="width: 80px  !important;height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${element.amount}" placeholder="Amount Paid">
                  </div>
                </td>
                 <td>
                  <div class="" style="width:100px">
                    <input type="text" class="form-control" id="already_paid" name="account[${index}][already_paid]" style="width: 80px  !important;height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" readonly value="${already_paid}" placeholder="Already Paid">
                  </div>
                </td>
                <td>
                  <div class="" style="width:100px">
                    <input type="text" class="form-control" id="only_payable" name="account[${index}][only_payable]" style="width: 80px  !important;height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;" value="${element.bill.balance_amount}" readonly placeholder="PAN Number">
                  </div>
                </td>

              </tr>`;
              $('#account_table').html(html);
              let seen = new Set();
              let uniqueAccounts = [];

              element.bill_lines.forEach((line) => {
                  let acc = line.account.trim();
                  if (acc && !seen.has(acc)) {
                      seen.add(acc);
                      uniqueAccounts.push(acc);
                  }
              });
               finalString = uniqueAccounts.join(", ");

      });
      $('#nature_payment').val(finalString);

      // Clear containers
      $('#documentPreviewContainer, #documentPanview, #documentBankview').empty();


      // Parse the JSON data
      const documents = JSON.parse(billData.allbill.documents);
      let pan_upload=billData.vendor.pan_upload;
      if(billData.vendor.pan_upload !=="" && billData.vendor.pan_upload !==null && billData.vendor.pan_upload !==[]){
        pan_upload = JSON.parse(billData.vendor.pan_upload);
        pan_upload.forEach(filename => generatePreviewHtml(filename, '../public/uploads/customers/', '#documentPanview'));
      }else{
        $('.pan_upload').hide();
      }
      let bank=billData.bank;
        bank.forEach((element,index) => {
          if (element.bank_uploads !=="" && element.bank_uploads !==null) {
              bank_upload = JSON.parse(element.bank_uploads);
              bank_upload.forEach(filename => generatePreviewHtml(filename, '../public/uploads/customers/', '#documentBankview'));
            }else{
              $('.bank_uploads').hide();
            }
          });
      // Function to generate preview HTML for a file
      function generatePreviewHtml(filename, basePath, containerId) {
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
                  <div class="preview-card col-sm-3 documentclk"
                      data-filetype="${fileType}"
                      data-files="${fileArray.replace(/"/g, '&quot;')}">
                      <img src="${iconUrl}" alt="${extension === 'pdf' ? 'PDF' : 'File'}" style="height:60px;">
                      <div>${filename.replace(/'/g, "&#39;").replace(/"/g, "&quot;")}</div>
                  </div>
              `;

        $(containerId).append(previewHtml);
      }



      // Process each file type
        documents.forEach(filename => generatePreviewHtml(filename, '{{ asset("uploads/vendor/bill") }}/', '#documentPreviewContainer'));
  });
    $(document).on('click', '.customer-row', function (e) {
        // Don't trigger if clicking on checkbox
        if ($(e.target).is('input[type="checkbox"]')) {
            return;
        }
        if ($(e.target).is('.neft_modal')) {
            $('#NEFTModal').fadeIn();
            return;
        }

         const billData = {
            id: $(this).data('id'),
            vendor_name : $(this).data('vendor_name'),
            payment : $(this).data('payment'),
            payment_made: $(this).data('payment_made'),
            payment_date: $(this).data('payment_date'),
            payment_mode: $(this).data('payment_mode'),
            paid_through: $(this).data('paid_through'),
            amount_used: $(this).data('amount_used'),
            reference: $(this).data('reference'),
            vendor_address: $(this).data('vendor-address'),
            vendor: $(this).data('vendor'),
            items: $(this).data('items'),
            allbill: $(this).data('allbill')
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
        $('.neft_modal').on('click', function () {
          $('#NEFTModal').fadeIn(); // or use .show() depending on your animation style
        });

        // To close modal
        $('.close-modal-tcs').on('click', function () {
          $('#NEFTModal').fadeOut(); // or use .hide()
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
    // $(document).on('click', '.pdf-btn', function () {
    //     const billNumber = $('#bill-number').text();
    //     alert(`Generating PDF for bill ${billNumber}`);
    //     // window.open(`/bills/pdf/${billNumber}`, '_blank');
    // });

    // Edit button handler
    // $(document).on('click', '.edit-btn', function () {
    //     const billId = $('#billDetailModal').data('bill-id');
    //     window.location.href = "{{ route('superadmin.getbillmadecreate') }}" + "?id=" + billId;
    // });
    $(document).on('click', '.edit-btn', function () {
      const billId = $('#billDetailModal').data('bill-id');

      const page = sessionStorage.getItem('bill_made_page') || 1;
      let currentFilters = {
                    date_from: '',
                    date_to: '',
                    zone_id: $('.zone_id').val(),
                    zone_name: $('.zone-search-input').val(),

                    branch_id: $('.branch_id').val(),
                    branch_name: $('.branch-search-input').val(),

                    company_id: $('.company_id').val(),
                    company_name: $('.company-search-input').val(),

                    vendor_id: $('.vendor_id').val(),
                    vendor_name: $('.vendor-search-input').val(),

                    nature_id: $('.nature_id').val(),
                    nature_name: $('.nature-search-input').val(),

                    status_id: $('.status_id').val(),
                    status_name: $('.status-search-input').val(),

                    state_id: $('.state_id').val(),
                    state_name: $('.state-search-input').val(),

                    universal_search: $('.universal_search').val()
                };

                let dateVal = $('.data_values').val();
                let dateText = $('#data_values').text().trim();

                if (dateVal && dateVal.includes('to') && dateText !== 'Today') {
                    let parts = dateVal.split(' to ');
                    currentFilters.date_from = parts[0];
                    currentFilters.date_to = parts[1];
                } else {
                    currentFilters.date_from = '';
                    currentFilters.date_to = '';
                }

                sessionStorage.setItem("quotation_filters", JSON.stringify(currentFilters));

                sessionStorage.setItem("restore_filters", "1");

                sessionStorage.setItem("quotation_page", sessionStorage.getItem('bill_made_page') || 1);

      window.location.href =
          "{{ route('superadmin.getbillmadecreate') }}" +
          "?id=" + billId +
          "&type=edit" +
          "&redirect_page=" + page;
  });


    $('#importFileInput').on('change', function () {
      const file = this.files[0];
      if (file) {
        $('#fileNameDisplay').text('Selected file: ' + file.name);
      } else {
        $('#fileNameDisplay').text('');
      }
    });
    $(document).on('click', '.import-btn', function (e) {
      $btn=$(this).prop('disabled', true);
      // Save original button text
      let originalText = $btn.html();
      // Show loader on button
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        e.preventDefault();

        const fileInput = $('#importFileInput')[0];
        const file = fileInput.files[0];

        if (!file) {
            toastr.error("Please select a file to upload.");
            $btn.prop('disabled', false).html(' Upload & Import');
            return;
        }

        let formData = new FormData();
        formData.append('file', file);

        $.ajax({
            url: '{{ url("/import-billmade") }}',
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
                    $('#importbillpaymentModal').modal('hide');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    toastr.error("Unexpected response from server.");
                    $btn.prop('disabled', false).html(' Upload & Import');
                }
            },
            error: function (xhr) {
                console.error("Import failed:", xhr);
                toastr.error("Failed to import file.");
                $btn.prop('disabled', false).html(' Upload & Import');
            }
        });
    });
    $(document).on('click', '.pdf-btn', function() {
      const billId = $('#billDetailModal').data('bill-id');
      // Make AJAX request
      $.ajax({
          url: '{{ route("superadmin.getbillmadepdf") }}',
          method: 'GET',
          data: { id: billId },
          xhrFields: {
              responseType: 'blob' // Important for handling binary data
          },
          success: function(data) {
              // Create a blob from the response
              const blob = new Blob([data], {type: 'application/pdf'});
              // Create a link element
              const link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = `Payment_made_${billId}.pdf`;

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
    $(document).on('click', '.print-btn', function () {
    const billId = $('#billDetailModal').data('bill-id');

    $.ajax({
        url: '{{ route("superadmin.getbillmadeprint") }}',
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
  // jQuery/vanilla fallback: will hide modal for Bootstrap 4, Bootstrap 5, or just remove backdrop if needed
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

    // Function to close modal
    function closeModal() {
        $('#billDetailModal').removeClass('show');
        $('#modalOverlay').removeClass('show');
        $('body').css('overflow', 'auto');

    }

    // Function to populate modal with data
    function populateModal(data) {
      console.log("data",data);

        $('#billDetailModal').data('bill-id', data.id);

        // Set modal title to bill number
        $('.zoho-modal-title').text(data.bill_number || 'Bill Details');
        $('#payment_no').text(data.payment || 'Not specified');
        $('#payment_made').text(data.payment_made || 'Not specified');
        $('#paid_to').text(data.vendor_name || 'Not specified');
        $('#payment_date').text(data.payment_date || 'Not specified');
        $('#payment_mode').text(data.payment_mode || 'Not specified');
        $('#paid_through').text(data.paid_through || 'Not specified');
        $('#reference').text(data.reference || 'Not specified');
        $('#amount_used').text(Number(data.amount_used).toLocaleString('en-IN') || 'Not specified');

        // Vendor Details
        if (data.vendor_address) {
            const addressParts = data.vendor_address;
            console.log("addressParts",addressParts);

            $('.vendor-name').text(data.vendor_name || 'Not specified');
            $('.vendor-street').text(addressParts.address|| 'Not specified');
            $('.vendor-city-state').text(`${addressParts.city},${addressParts.state}`|| 'Not specified');
            $('.vendor-country-zip').text(`${addressParts.country}-${addressParts.zip_code}`|| 'Not specified');
            $('.vendor-phone').text(`${addressParts.phone}`|| 'Not specified');
        }

        // Bill Details
        $('#order-number').text(data.order_number || 'Not specified');
        $('#bill-date').text(data.bill_date || 'Not specified');
        $('#due-date').text(data.due_date || 'Not specified');
        $('#payment-terms').text(data.payment_terms || 'Not specified');
        $('.total-amount').text(formatCurrency(data.grand_total));
        $('#sub-total').text(formatCurrency(data.sub_total));
        $('#discount').text(formatCurrency(data.discount_amount));
        $('#grand-total').text(formatCurrency(data.grand_total));
        $('#notes').text(data.note || 'No notes');

        // Bill Items Table
        const itemsHtml = data.items.map(item => `
            <tr>
                <td>${item.bill.bill_number || 'No description'}</td>
                <td>${item.bill_date || 'Not specified'}</td>
                <td>${formatCurrency(item.grand_total_amount)}</td>
                <td>${formatCurrency(item.amount)}</td>
            </tr>
        `).join('');
        console.log("itemsHtml",itemsHtml);

        $('#bill-items').html(itemsHtml);


        // Journal Entries
        const journalHtml = `
            <table class="zoho-items-table">
              <thead>
                <tr>
                  <th>ACCOUNT</th>
                  <th>DEBIT</th>
                  <th>CREDIT</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Petty Cash</td>
                  <td>0.00</td>
                  <td>${formatCurrency(data.amount_used)}</td>
                </tr>
                <tr>
                  <td>Prepaid Expenses</td>
                  <td>${formatCurrency(data.amount_used)}</td>
                  <td>0.00</td>
                </tr>
                <tr class="total-row">
                  <td><strong>Total</strong></td>
                  <td><strong>${formatCurrency(data.amount_used)}</strong></td>
                  <td><strong>${formatCurrency(data.amount_used)}</strong></td>
                </tr>
              </tbody>
            </table>
        `;
        $('#journal-entries').html(journalHtml);
        const tableHtml = data.items.map(item => `
           <div class="entry">
              <h3>Payments Made - ${item.bill.bill_number}</h3>
              <table class="zoho-items-table">
                <thead>
                  <tr>
                    <th>ACCOUNT</th>
                    <th>DEBIT</th>
                    <th>CREDIT</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Prepaid Expenses</td>
                    <td>0.00</td>
                    <td>${formatCurrency(item.amount)}</td>
                  </tr>
                  <tr>
                    <td>Accounts Payable</td>
                    <td>${formatCurrency(item.amount)}</td>
                    <td>0.00</td>
                  </tr>
                  <tr class="total-row">
                    <td><strong>Total</strong></td>
                    <td><strong>${formatCurrency(item.amount)}</strong></td>
                    <td><strong>${formatCurrency(item.amount)}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
        `).join('');
        $('.table_html').html(tableHtml);
        $('#amount_word').text(numberToWords(data.amount_used));
        const documents = JSON.parse(data.allbill.documents);
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
                <div class="preview-card col-sm-3 documentclk" data-filetype="${fileType}" data-files='${fileArray}'>
                  <img src="${iconUrl}" alt="${extension === 'pdf' ? 'PDF' : 'File'}" style="height:60px;">
                  <div>${filename}</div>
                </div>
              </div>
            `;

            $(containerId).append(previewHtml);
          }
          $('.upload_doc').empty();
          // Process each file type
          documents.forEach(filename => generatePreviewHtml(filename, '{{ asset("uploads/vendor/bill") }}/', '.upload_doc','Upload Document'));

    }
    function numberToWords(num) {
              const a = [
                '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen',
                'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
              ];
              const b = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

              const numberToWordsInternal = (n) => {
                if (n < 20) return a[n];
                if (n < 100) return b[Math.floor(n / 10)] + (n % 10 ? ' ' + a[n % 10] : '');
                if (n < 1000) return a[Math.floor(n / 100)] + ' Hundred' + (n % 100 ? ' and ' + numberToWordsInternal(n % 100) : '');
                if (n < 100000) return numberToWordsInternal(Math.floor(n / 1000)) + ' Thousand' + (n % 1000 ? ' ' + numberToWordsInternal(n % 1000) : '');
                if (n < 10000000) return numberToWordsInternal(Math.floor(n / 100000)) + ' Lakh' + (n % 100000 ? ' ' + numberToWordsInternal(n % 100000) : '');
                return numberToWordsInternal(Math.floor(n / 10000000)) + ' Crore' + (n % 10000000 ? ' ' + numberToWordsInternal(n % 10000000) : '');
              };

              return 'Indian Rupee ' + numberToWordsInternal(num) + ' Only';
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

$(document).ready(function () {
  const previewFiles = {};

  $('input[type="file"]').on('change', function () {
    const input = $(this);
    const inputId = input.attr('id');
    const preview = $(`#preview_${inputId}`);
    const files = Array.from(this.files);

    previewFiles[inputId] = files;
    preview.empty();

    files.forEach((file, index) => {
      const fileHtml = `
        <li class="file-preview-item" data-index="${index}">
          <span><i class="fas fa-file"></i> ${file.name}</span>
          <span class="remove-file-btn" data-input="${inputId}" data-index="${index}">❌</span>
        </li>
      `;
      preview.append(fileHtml);
    });
  });

  $(document).on('click', '.remove-file-btn', function () {
    const inputId = $(this).data('input');
    const index = $(this).data('index');

    previewFiles[inputId].splice(index, 1);
    const dt = new DataTransfer();
    previewFiles[inputId].forEach(file => dt.items.add(file));
    $(`#${inputId}`)[0].files = dt.files;

    const preview = $(`#preview_${inputId}`);
    preview.empty();

    previewFiles[inputId].forEach((file, i) => {
      const fileHtml = `
        <li class="file-preview-item" data-index="${i}">
          <span><i class="fas fa-file"></i> ${file.name}</span>
          <span class="remove-file-btn" data-input="${inputId}" data-index="${i}">❌</span>
        </li>
      `;
      preview.append(fileHtml);
    });
  });
});

$(document).ready(function () {
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
      views += `<button style="font-size: 11px;" type="button" class="btn btn-primary doc-view-btn mb-1" data-filepath="${file}">${fileName}</button>`;
    });

    $('#image_pdfs').html(views);
  });

  // Document viewer navigation buttons (renamed to avoid conflict with .pdf-btn PDF download)
  $(document).on('click', '.doc-view-btn', function () {
    $('.doc-view-btn').removeClass('active');
    $(this).addClass('active');
    $('#pdfmain').attr('src', $(this).data('filepath'));
  });
});

  $('#submit-neft-datas').click(function (event) {
        event.preventDefault();
        // let isValid = true;

        // if ($('#serial_number').val() === "") {
        //     $('.error_serial').text('Serial Required');
        //     isValid = false;
        // }
        // if ($('#created_by').val() === "") {
        //     $('.error_created_by').text('Enter the Creator Name');
        //     isValid = false;
        // }
        // if ($('#vendor').val() === "") {
        //     $('.error_vendor').text('Please select the Employee Name or Vendor');
        //     isValid = false;
        // }

        // if ($('#nature_payment').val() === "") {
        //     $('.error_nature_payment').text('Enter the Nature Payment');
        //     isValid = false;
        // }
        // if ($('#invoice_amount').val() === "") {
        //     $('.error_invoice_amount').text('Enter the Invoice Amount');
        //     isValid = false;
        // }

        // if ($('#payment_status').val() === "") {
        //     $('.error_payment_status').text('Select Payment Status');
        //     isValid = false;
        // }

        // if ($('#utr_number').val() === "") {
        //     $('.error_utr_number').text('Enter UTR Number');
        //     isValid = false;
        // }

        // let paymentStatusChecked = false;
        // $('input[name="payment_method[]"]:checked').each(function() {
        //     paymentStatusChecked = true;
        // });
        // if (!paymentStatusChecked) {
        //     $('.error_payment_method').text('Select at least one payment method');
        //     isValid = false;
        // }

        // // PAN number validation (e.g. ABCDE1234F)
        // let panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        // let panNumber = $('#pan_number').val().toUpperCase();
        // if (!panPattern.test(panNumber)) {
        //     $('.error_pan_number').text('Invalid PAN Number');
        //     isValid = false;
        // } else {
        //     $('.error_pan_number').text('');
        // }

        // // IFSC code validation (e.g. SBIN0001234)
        // let ifscPattern = /^[A-Z]{4}0[A-Z0-9]{6}$/;
        // let ifscCode = $('#ifsc_code').val().toUpperCase();
        // if (!ifscPattern.test(ifscCode)) {
        //     $('.error_ifsc_code').text('Invalid IFSC Code');
        //     isValid = false;
        // } else {
        //     $('.error_ifsc_code').text('');
        // }

        // function isFileTooLarge(inputId, errorClass) {
        //         const file = $(inputId)[0].files[0];
        //         if (file && file.size > 5242880) {
        //             $(errorClass).text('File size must be less than 5MB');
        //             return true;
        //         }
        //         $(errorClass).text('');
        //         return false;
        //     }

        //     // Apply file size check
        //     if (isFileTooLarge('#pan_upload', '.error_pan_upload')) isValid = false;
        //     if (isFileTooLarge('#bank_upload', '.error_bank_upload')) isValid = false;
        //     if (isFileTooLarge('#invoice_upload', '.error_invoice_upload')) isValid = false;
        //     if (isFileTooLarge('#po_upload', '.error_po_upload')) isValid = false;
        //     if (isFileTooLarge('#po_signed_upload', '.error_po_signed_upload')) isValid = false;
        //     if (isFileTooLarge('#po_delivery_upload', '.error_po_delivery_upload')) isValid = false;


        // console.log("isValid",isValid);
        // if (!isValid) {
        //     return;
        // }

      //   // Create FormData object
      //   let formData = new FormData();
      //   formData.append('id', $('#id').val());
      //   formData.append('user_id', $('#users_id').val());
      //   formData.append('bill_id', $('#bill_id').val());
      //   formData.append('branch_id', $('#branch_id').val());
      //   formData.append('serial_number', $('#serial_number').val());
      //   formData.append('created_by', $('#created_by').val());
      //   formData.append('vendor', $('#vendor-search').val());
      //   formData.append('vendor_id', $('#selected-vendor-id').val());
      //   formData.append('nature_payment', $('#nature_payment').val());
      //   formData.append('invoice_amount', $('#invoice_amount').val());
      //   formData.append('already_paid', $('#already_paid').val());
      //   formData.append('pan_number', $('#pan_number').val());
      //   formData.append('account_number', $('#account_number').val());
      //   formData.append('tds', $('.tax-search-input').val());
      //   formData.append('tax_rate', $('#tds_tax_value').val());
      //   formData.append('tds_tax_id', $('#tds-tax-id').val());
      //   formData.append('ifsc_code', $('#ifsc_code').val());
      //   formData.append('tax_amount', $('#tax_amount').val());
      //   formData.append('only_payable', $('#only_payable').val());
      //   formData.append('payment_status', $('#payment_status').val());
      //   formData.append('existing_pan_file', $('#existing_pan_file').val());
      //   formData.append('existing_bank_upload', $('#existing_bank_upload').val());
      //   formData.append('existing_invoice_upload', $('#existing_invoice_upload').val());
      //   formData.append('existing_po_upload', $('#existing_po_upload').val());
      //   formData.append('existing_po_signed_upload', $('#existing_po_signed_upload').val());
      //   formData.append('existing_po_delivery_upload', $('#existing_po_delivery_upload').val());
      //   let paymentMethods = [];
      //   $('input[name="payment_method[]"]:checked').each(function() {
      //       paymentMethods.push($(this).val());
      //   });
      //   paymentMethods.forEach((method, index) => {
      //       formData.append(`payment_method[${index}]`, method);
      //   });
      //   let account = [];
      //   $('input[name="account[]"]:checked').each(function() {
      //       account.push($(this).val());
      //   });
      //   account.forEach((method, index) => {
      //       formData.append(`account[${index}]`, method);
      //   });
      //   formData.append('utr_number', $('#utr_number').val());

      //   // Add payment_status
      //   $('input[name="payment_status[]"]:checked').each(function() {
      //       formData.append('payment_status[]', $(this).val());
      //   });

      //   // Add file uploads
      //  // Append all files from each input
      //   function appendMultipleFiles(formData, inputId, fieldName) {
      //       const files = $('#' + inputId)[0].files;
      //       for (let i = 0; i < files.length; i++) {
      //           formData.append(fieldName + '[]', files[i]); // Use array syntax
      //       }
      //   }
      //   appendMultipleFiles(formData, 'bank_upload', 'bank_upload');
      //   appendMultipleFiles(formData, 'invoice_upload', 'invoice_upload');
      //   appendMultipleFiles(formData, 'pan_upload', 'pan_upload');
      //   appendMultipleFiles(formData, 'po_upload', 'po_upload');
      //   appendMultipleFiles(formData, 'po_signed_upload', 'po_signed_upload');
      //   appendMultipleFiles(formData, 'po_delivery_upload', 'po_delivery_upload');

       // Save original button text
        let originalText = $(this).html();
        // Show loader on button
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

      let formData = new FormData($('#neftForm')[0]);
        console.log("formData",formData);

        // Include CSRF Token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // AJAX Request
        $.ajax({
            url: '{{ route("superadmin.saveneft") }}',
            type: "POST",
            data: formData,
            processData: false, // Prevent processing of the data
            contentType: false, // Prevent setting content-type header
            success: function (response) {
              toastr.success(response.message);
                if (response.success) {
                    $('#NEFTModal').fadeOut();
                }
            },
            error: function (error) {
                if (error.responseJSON && error.responseJSON.errors) {
                    // Display validation errors
                    $.each(error.responseJSON.errors, function(key, value) {
                        $('.error_' + key).text(value[0]);
                    });
                } else {
                    console.error(error.responseJSON);
                }
            },
            complete: function () {
                // Reset button back
                $(this).prop('disabled', false).html(originalText);
            }
        });
    });



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

                loadBillMade(
                    sessionStorage.getItem("quotation_page") || 1
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

        function loadBillMade(page = 1, perPage = $('#per_page').val()) {
            $.ajax({
                url: '{{ route("superadmin.getbillmade") }}',
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
                    universal_search: filters.universal_search
                },
                success: function (data) {
                    if (data && typeof data === 'object' && data.html !== undefined) {
                        $("#bill-made-body").html(data.html);
                        if (data.stats) { updateStatCards(data.stats); }
                    } else {
                        $("#bill-made-body").html(data);
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

                    const savedPage = sessionStorage.getItem('quotation_page') || 1;

                    setTimeout(function() {
                        loadQuotations(savedPage, $('#per_page').val());
                    }, 200);
                }

        function updateStatCards(s) {
            var fmt = function(n) {
                n = parseFloat(n) || 0;
                return '₹' + n.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
            };
            $('#stat-total').text(s.total);
            $('#stat-total-amt').text(fmt(s.total_amount));
            $('#stat-paid').text(s.paid);
            $('#stat-paid-amt').text(fmt(s.paid_amount));
            $('#stat-partial').text(s.partially);
            $('#stat-partial-amt').text(fmt(s.partially_amount));
            $('#stat-pending').text(s.pending);
            $('#stat-pending-amt').text(fmt(s.pending_amount));
            $('#stat-neft').text(s.neft);
            $('#stat-neft-amt').text(fmt(s.neft_amount));
        }

        // ── Stat card click filter ──
        $(document).on('click', '.qd-stat-card[data-stat-filter]', function () {
            var val = $(this).data('stat-filter');
            if (val === '' || val === undefined) {
                filters.status_name = '';
            } else {
                filters.status_name = val;
            }
            $('.qd-stat-card').removeClass('qd-stat-active');
            $(this).addClass('qd-stat-active');
            loadBillMade();
        });
        $('.qd-stat-card[data-stat-filter=""]').first().addClass('qd-stat-active');

        // ── Toggle Stats / Filters ──
        (function () {
            var sv = true, fv = true;
            $('#toggleStats').on('click', function () {
                if (sv) { $('.qd-stats').addClass('qd-section-hidden'); $(this).addClass('qd-toggle-active'); $('#statsChevron').addClass('rotated'); }
                else     { $('.qd-stats').removeClass('qd-section-hidden'); $(this).removeClass('qd-toggle-active'); $('#statsChevron').removeClass('rotated'); }
                sv = !sv;
            });
            $('#toggleFilters').on('click', function () {
                if (fv) { $('.qd-filters, .qd-search-row').addClass('qd-section-hidden'); $(this).addClass('qd-toggle-active'); $('#filtersChevron').addClass('rotated'); }
                else     { $('.qd-filters, .qd-search-row').removeClass('qd-section-hidden'); $(this).removeClass('qd-toggle-active'); $('#filtersChevron').removeClass('rotated'); }
                fv = !fv;
            });
        })();

        // ── Edit History Popup ──
        @if(isset($limit_access) && $limit_access == 1)
        $(document).on('click', '.qdt-history-btn', function (e) {
            e.stopPropagation();
            var history = JSON.parse($(this).attr('data-history') || '[]');
            var qno = $(this).attr('data-qno') || 'Payment';
            $('#historyPopupTitle').text('Edit History — ' + qno);
            var html = '';
            if (!history.length) {
                html = '<div class="qdt-history-empty">No edits recorded yet.</div>';
            } else {
                history.slice().reverse().forEach(function(entry, idx) {
                    html += '<div class="qdt-history-entry">' +
                        '<div class="qdt-history-meta">' +
                            '<span class="qdt-history-idx">' + (history.length - idx) + '</span>' +
                            '<span class="qdt-history-by"><i class="bi bi-person-fill me-1"></i>' + (entry.edited_by || '—') + '</span>' +
                            '<span class="qdt-history-role ' + (entry.role === 'Admin' ? 'qdt-role-admin' : 'qdt-role-user') + '">' + (entry.role || '') + '</span>' +
                        '</div>' +
                        '<div class="qdt-history-detail">' +
                            '<span class="qdt-history-at"><i class="bi bi-calendar3 me-1"></i>' + (entry.edited_at || '—') + '</span>' +
                            '<span class="qdt-history-status">Status: <strong>' + (entry.status || '—') + '</strong></span>' +
                            (entry.amount ? '<span class="qdt-history-amt">₹' + parseFloat(entry.amount).toLocaleString('en-IN', {minimumFractionDigits:2}) + '</span>' : '') +
                        '</div>' +
                    '</div>';
                });
            }
            $('#historyPopupBody').html(html);
            $('#editHistoryPopup, #historyOverlay').addClass('active');
        });
        $(document).on('click', '#closeHistoryPopup, #historyOverlay', function () {
            $('#editHistoryPopup, #historyOverlay').removeClass('active');
        });
        @endif

        // =================== MULTI-SELECT CHANGE LISTENER ===================
        function setupMultiSelect(selectorInput, selectorHidden) {
            var ns = 'click.ms' + selectorHidden.replace(/[^a-zA-Z0-9]/g, '');
            $(document).off(ns, selectorHidden).on(ns, selectorHidden, function () {
                const selectedIds  = $(this).val();
                const selectedText = $(selectorInput).val();
                if (selectorHidden === '.zone_id') {
                    filters.zone_id = selectedIds; filters.zone_name = selectedText;
                } else if (selectorHidden === '.branch_id') {
                    filters.branch_id = selectedIds; filters.branch_name = selectedText;
                } else if (selectorHidden === '.company_id') {
                    filters.company_id = selectedIds; filters.company_name = selectedText;
                } else if (selectorHidden === '.vendor_id') {
                    filters.vendor_id = selectedIds; filters.vendor_name = selectedText;
                } else if (selectorHidden === '.nature_id') {
                    filters.nature_id = selectedIds; filters.nature_name = selectedText;
                } else if (selectorHidden === '.status_id') {
                    filters.status_id = selectedIds; filters.status_name = selectedText;
                } else if (selectorHidden === '.state_id') {
                    filters.state_id = selectedIds; filters.state_name = selectedText;
                }
                loadBillMade();
            });
        }
        setupMultiSelect('.zone-search-input', '.zone_id');
        setupMultiSelect('.branch-search-input', '.branch_id');
        setupMultiSelect('.company-search-input', '.company_id');
        setupMultiSelect('.vendor-search-input', '.vendor_id');
        setupMultiSelect('.nature-search-input', '.nature_id');
        setupMultiSelect('.status-search-input', '.status_id');
        setupMultiSelect('.state-search-input', '.state_id');
        $('.universal_search').on('keyup', function () {
          filters.universal_search=  $('.universal_search').val();
          loadBillMade();
        });


        // Date change
        $('.data_values').on('change', function () {
            let dateRange = $(this).val();
            if (dateRange.includes('to')) {
                let parts = dateRange.split(' to ');
                filters.date_from = parts[0].trim();
                filters.date_to = parts[1].trim();
            }
            loadBillMade();
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
            loadBillMade();
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
            loadBillMade();
        });

        // // Pagination
        // $(document).on('click', '.pagination a', function (e) {
        //     e.preventDefault();
        //     let url = $(this).attr('href');
        //     let params = new URLSearchParams(url.split('?')[1]);
        //     let page = params.get('page') || 1;
        //     let perPage = $('#per_page').val();
        //     loadBillMade(page, perPage);
        // });

        // // Change per_page
        // $(document).on('change', '#per_page', function () {
        //     loadBillMade(1, $(this).val());
        // });
        $(document).on('click', '.pagination a', function (e) {
          e.preventDefault();

          let url = $(this).attr('href');
          let params = new URLSearchParams(url.split('?')[1]);
          let page = params.get('page') || 1;
          let perPage = $('#per_page').val();

          // ✅ Save current page in memory
          sessionStorage.setItem('bill_made_page', page);

          loadBillMade(page, perPage);
      });
      $(document).on('change', '#per_page', function () {
          sessionStorage.setItem('bill_made_page', 1);
          loadBillMade(1, $(this).val());
      });

    });

});


const vendorsaveUrl = "{{ route('superadmin.vendorsave') }}";
const vendorfetch = "{{ route('superadmin.vendorfetch') }}";
</script>

@if(!empty($_GET['id']))
<script>
$(document).ready(function() {
    let billmadeId = "{{ request()->get('id') }}";
    let perPage = {{ $billpaylist->perPage() }};
    let currentPage = {{ $billpaylist->currentPage() }};

    if (!billmadeId) return;

    // Check if bill is on current page
    let row = $('.customer-row[data-id="' + billmadeId + '"]');
    if (row.length) {
        row.trigger('click'); // open modal immediately
        return;
    }

    // Bill not on current page, find its index (position) from server-side
    let billmadeIndex = null;
    @foreach ($allBillpays as $i => $billmade)
        if ({{ $billmade->id }} == billmadeId) {   // ✅ use BillId not vendorId
            billmadeIndex = {{ $i + 1 }};
        }
    @endforeach

    console.log("billmadeIndex", billmadeIndex);

    if (!billmadeIndex) {
        console.warn("Bill not found in dataset");
        return;
    }

    let targetPage = Math.ceil(billmadeIndex / perPage);
    console.log("purchase index:", billmadeIndex, "Target page:", targetPage);

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
window.addEventListener('load', function () {

    if (sessionStorage.getItem('force_first_page_done')) {
        return;
    }

    const navEntry = performance.getEntriesByType('navigation')[0];
    const isReload =
        navEntry &&
        (navEntry.type === 'reload' || navEntry.type === 'navigate');

    if (!isReload) return;

    sessionStorage.setItem('force_first_page_done', '1');

    if (typeof loadBillMade === 'function') {
        loadBillMade(1, $('#per_page').val());
    }

    const url = new URL(window.location.href);
    window.history.replaceState({}, document.title, url.pathname);
});
</script>
<script>
window.addEventListener('beforeunload', function () {
    sessionStorage.removeItem('force_first_page_done');
});
</script>

<script>
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
    </script>

<!-- [ Main Content ] end -->
@include('superadmin.superadminfooter')
</body>
<!-- [Body] end -->
</html>
