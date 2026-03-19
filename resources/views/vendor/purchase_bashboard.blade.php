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
                        <i class="bi bi-bag-check"></i>
                        Purchase Order Dashboard
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
                        <a href="{{ route('superadmin.getpurchasecreate') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>New Purchase Order
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importbillModal">
                                    <i class="bi bi-upload me-2"></i>Import Purchase Order
                                </a></li>
                                <li><a href="#" class="dropdown-item" id="emailopen">
                                    <i class="bi bi-envelope me-2"></i>Email
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- ── Stats ── --}}
                <div class="qd-stats" id="statsSection">
                    <div class="qd-stat-card qd-stat-blue qd-stat-active" data-stat-filter="" title="Show all">
                        <div class="qd-stat-icon"><i class="bi bi-bag-check"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Total Purchase Orders</div>
                            <div class="qd-stat-value">{{ $stats['total'] }}</div>
                            <div class="qd-stat-sub">₹{{ number_format($stats['total_amount'],2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-green" data-stat-filter="Approved" title="Filter: Approved">
                        <div class="qd-stat-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Approved</div>
                            <div class="qd-stat-value">{{ $stats['approved'] }}</div>
                            <div class="qd-stat-sub">₹{{ number_format($stats['approved_amount'],2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-orange" data-stat-filter="Pending" title="Filter: Pending">
                        <div class="qd-stat-icon"><i class="bi bi-clock"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Pending</div>
                            <div class="qd-stat-value">{{ $stats['pending'] }}</div>
                            <div class="qd-stat-sub">₹{{ number_format($stats['pending_amount'],2) }}</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-red" data-stat-filter="Reject" title="Filter: Rejected">
                        <div class="qd-stat-icon"><i class="bi bi-x-circle"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Rejected</div>
                            <div class="qd-stat-value">{{ $stats['rejected'] }}</div>
                            <div class="qd-stat-sub">&nbsp;</div>
                        </div>
                    </div>
                    <div class="qd-stat-card qd-stat-purple" data-stat-filter="draft" title="Filter: Draft">
                        <div class="qd-stat-icon"><i class="bi bi-pencil-square"></i></div>
                        <div class="qd-stat-body">
                            <div class="qd-stat-label">Draft</div>
                            <div class="qd-stat-value">{{ $stats['draft'] }}</div>
                            <div class="qd-stat-sub">&nbsp;</div>
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

                    {{-- Row 2: Branch, Vendor, Nature, Status --}}
                    <div class="qd-filter-row" style="margin-top:10px;">
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
                                    <div data-value="save" data-id="1">Save</div>
                                    <div data-value="draft" data-id="2">Draft</div>
                                    <div data-value="pending" data-id="3">Pending</div>
                                    <div data-value="approved" data-id="4">Approved</div>
                                    <div data-value="reject" data-id="5">Reject</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Search bar ── --}}
                <div class="qd-search-row">
                    <div class="qd-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" class="universal_search" placeholder="Search purchase order...">
                    </div>
                </div>

                {{-- ── Applied filters ── --}}
                <div class="qd-applied-bar">
                    <span class="applied-label">Filters:</span>
                    <div id="filter-summary" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
                </div>

                {{-- ── Table ── --}}
                <div class="qd-table-wrap">
                    <div id="purchase-body">
                        @include('vendor.partials.table.purchase_rows', ['purchaselist' => $purchaselist, 'perPage' => $perPage, 'limit_access' => $limit_access])
                    </div>
                </div>

            </div>{{-- qd-card --}}

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
                  <div>
                    <div style="text-align: right;">
                      <div class="dropdown">
                          <button class="btn btn-light" data-bs-toggle="dropdown">
                              &#x22EE;
                          </button>
                          <ul class="dropdown-menu" style="max-width: 190px;">
                              <li><a class="dropdown-item clone-btn">Clone</a></li>
                          </ul>
                      </div>
                    </div>
                  </div>
                  <!-- Bill Header Section -->
                  <div class="bill-header">

                    <div>
                      <span class="bill-paid-status">Payments Made 1</span>
                    </div>
                    <div>
                      <a href="#" class="bill-pdf-link">Show PDF View</a>
                    </div>
                  </div>

                  <div class="bill-divider"></div>
                    <div class="bill_container">
                        <!-- Bill Info Section -->
                      <div class="zoho-section">
                        <div class="zoho-section-title">BILL</div>
                        <div class="bill-title">Bill# myorder</div>

                        <div class="bill-info-section">
                          <div class="bill-info-row">
                            <div class="bill-info-label">ORDER NUMBER</div>
                            <div class="bill-info-value" id="order-number">12345</div>
                          </div>
                          <div class="bill-info-row">
                            <div class="bill-info-label">BILL DATE</div>
                            <div class="bill-info-value" id="bill-date">18/07/2025</div>
                          </div>
                          <div class="bill-info-row">
                            <div class="bill-info-label">DUE DATE</div>
                            <div class="bill-info-value" id="due-date">18/07/2025</div>
                          </div>
                          <div class="bill-info-row">
                            <div class="bill-info-label">PAYMENT TERMS</div>
                            <div class="bill-info-value" id="payment-terms">Due on Receipt</div>
                          </div>
                          <div class="bill-info-row total-row">
                            <div class="bill-info-label">TOTAL</div>
                            <div class="bill-info-value total-amount">₹1,00,000.00</div>
                          </div>
                        </div>
                    </div>

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
                    <div class="zoho-section-title">ITEMS & DESCRIPTION</div>
                    <table class="zoho-items-table">
                      <thead>
                        <tr>
                          <th>ITEMS & DESCRIPTION</th>
                          {{-- <th>ACCOUNT</th> --}}
                          <th>CUSTOMER DETAILS</th>
                          <th>QUANTITY</th>
                          <th>RATE</th>
                          <th>GST</th>
                          <th>GST AMOUNT</th>
                          <th>AMOUNT</th>
                        </tr>
                      </thead>
                      <tbody id="bill-items">
                        {{-- <tr>
                          <td>pen</td>
                          <td>Employee Advance</td>
                          <td>
                            <div>Mr. vasanth s</div>
                            <div class="zoho-item-note">NON-BILLABLE</div>
                          </td>
                          <td>100</td>
                          <td>1000</td>
                          <td>100000.00</td>
                        </tr> --}}
                      </tbody>
                    </table>
                  </div>

                  <!-- Totals Section -->
                  <div class="zoho-section">
                    <div class="zoho-totals-grid">
                      <div class="zoho-total-row">
                        <span>Sub Total</span>
                        <span id="sub-total">₹1,00,000.00</span>
                      </div>
                      <div class="zoho-total-row">
                        <span>Discount (-)</span>
                        <span id="discount">₹0.00</span>
                      </div>
                      <div class="zoho-total-row gst-breakdown">
                        {{-- <span>GST (+)</span>
                        <span id="gst_amount">₹0.00</span> --}}
                      </div>
                      <div class="zoho-total-row">
                        <span>TDS (-)</span>
                        <span id="tds_amount">₹0.00</span>
                      </div>
                      <div class="zoho-total-row zoho-total-amount">
                        <span>Total</span>
                        <span id="grand-total">₹1,00,000.00</span>
                      </div>
                    </div>
                  </div>

                  <!-- Notes Section -->
                  <div class="zoho-section">
                    <div class="zoho-section-title">Notes</div>
                    <div class="zoho-notes-content" id="notes">nothing</div>
                  </div>

                  <!-- Journal Section -->
                  <div class="zoho-section">
                    <div class="zoho-section-title">Journal</div>
                    <div class="zoho-journal-note">Amount is displayed in your base currency <strong>ING</strong></div>
                    <table class="zoho-journal-table">
                      <thead>
                        <tr>
                          <th>ACCOUNT</th>
                          <th>DEBIT</th>
                          <th>CREDIT</th>
                        </tr>
                      </thead>
                      <tbody id="journal-entries">
                        <tr>
                          <td>Employee Advance</td>
                          <td>1,00,000.00</td>
                          <td>0.00</td>
                        </tr>
                        <tr>
                          <td>Accounts Payable</td>
                          <td>0.00</td>
                          <td>1,00,000.00</td>
                        </tr>
                        <tr>
                          <td><strong>Total</strong></td>
                          <td><strong>1,00,000.00</strong></td>
                          <td><strong>1,00,000.00</strong></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="upload_doc">

                  </div>

                </div>
              </div>
            </div>

            <div class="zoho-modal-overlay" id="modalOverlay"></div>

            {{-- ── Edit History Popup (admin only) ── --}}
            @if(isset($limit_access) && $limit_access == 1)
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
              <div class="modal fade" id="importbillModal" tabindex="-1" aria-labelledby="importbillModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="importbillModalLabel">Import Purchase Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p>Step 1: Download the Excel Template</p>
                        <a href="{{ url('/purchase-download-template') }}" class="btn btn-success mb-3">
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

               <!-- Manage TDS Modal -->
                <div id="EmailModal" class="tds-modal">
                  <div class="tds-modal-content">
                    <div class="tds-modal-header">
                      <h4>Manage Email</h4>
                      <span class="close-modal">&times;</span>
                    </div>

                    <div class="tds-modal-body">
                      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h5>Emails</h5>
                        <div>
                          <button type="button" class="btn-new-email">+ New Email</button>
                        </div>
                      </div>
                      <div class="gst-content">
                        <table class="tds-table">
                          <thead>
                            <tr><th>Email</th><th>created By</th><th>Edit</th></tr>
                          </thead>
                          <tbody id="email_row">
                            @if(!empty($TblPoEmail))
                              @foreach ($TblPoEmail as $email)
                                <tr>
                                  <td id="emailrow">
                                    <input type="hidden" class="email_id" value="{{$email->id}}">
                                    <input type="hidden" class="email_mail" value="{{$email->email}}">
                                    {{ $email->email }}
                                  </td>
                                  <td>{{ $email->created_by }}</td>
                                  <td>
                                    <button class="zoho-btn zoho-btn-primary edit-email">
                                              <i class="bi bi-pencil"></i> Edit
                                            </button>
                                  </td>
                                </tr>
                              @endforeach
                            @endif
                          </tbody>
                        </table>

                        <div class="pagination-wrapper">
                          {!! $TblPoEmail->links() !!}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- New TCS Tax Modal -->
                <div id="newemailModal" class="tds-modal">
                  <div class="tds-modal-content" style="max-width: 600px;">
                    <div class="tds-modal-header">
                      <h4>New Email</h4>
                      <span class="close-new-modal" style="font-size: 2rem;cursor:pointer">&times;</span>
                    </div>

                    <div class="tds-modal-body">
                      <form>

                        <div style="display: flex; gap: 10px;">
                          <div style="flex: 1;">
                            <label> Email <span style="color: red">*</span></label>
                            <input type="text" class="form-control new_email" id="new_email"/>
                            <span class="error_email" style="color:red"></span>
                            <input type="hidden" class="email_id">
                          </div>
                        </div>

                        <br />
                        <button class="btn-save email_save" >Save</button>
                        <button class="btn-cancel close-new-modal" type="button">Cancel</button>
                      </form>
                    </div>
                  </div>
                </div>
            {{-- tds modal --}}

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
                  <h5 class="modal-title">Purchase Preview</h5>

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
  $(document).ready(function () {
        $('#per_page').on('change', function () {
            $('#perPageForm').submit();
        });
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
        e.preventDefault();
        $btn=$(this).prop('disabled', true);
        // Save original button text
        let originalText = $btn.html();
        // Show loader on button
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

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
            url: '{{ url("/import-purchase") }}',
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
                    $('#importbillModal').modal('hide');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    toastr.error("Unexpected response from server.");
                    $btn=$(this).prop('disabled', false);
                }
            },
            error: function (xhr) {
                console.error("Import failed:", xhr);
                toastr.error("Failed to import file.");
                $btn.prop('disabled', false).html(' Upload & Import');
            }
        });
    });
  $(document).on('click', '.approver', function (e) {
      e.preventDefault();
      const $btn = $(this);
      $btn.prop('disabled', true);
      const approver_id = $(this).data('id');
      const value = $(this).data('value');
      $.ajax({
          url: '{{ route("superadmin.PurchaseApprover") }}',
          method: "GET",
          data: { approver_id: approver_id,value:value},
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
              toastr.success(response.message);
               setTimeout(() => {
                    window.location.reload();
                    $btn.prop('disabled', false);
                }, 2000);
          },
          error: function (xhr) {
              console.error("Error:", xhr);
              toastr.error("Error occurred while checking/approving");
          }
      });
  });
    $(document).on('click', '.edit-email', function () {
      var row = $(this).closest('tr');
      var email = row.find('.email_mail').val();
      var email_id = row.find('.email_id').val();
      $('#newemailModal').fadeIn();
      $('.new_email').val(email);
      $('.email_id').val(email_id);
    });
    $(document).on('click', '#emailopen', function () {
      $('#EmailModal').show();
    });
    $(document).on('click', '.btn-new-email', function () {
      $('.new_email').val('');
      $('.email_id').val('');
      $('#EmailModal').hide();
      $('#newemailModal').fadeIn();
    });
    $(document).on('click', '.close-modal', function () {
      $('#EmailModal').hide();
    });
    $(document).on('click', '.close-new-modal', function () {
      $('#newemailModal').fadeOut();
      $('#EmailModal').fadeIn(); // Go back to manage TDS
    });
    $(document).on('click', '.email_save', function (e) {
      e.preventDefault();
      let isValid = true;
      let email = $('#new_email').val().trim();
      let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (email === "") {
          $('.error_email').text('Enter Your Email');
          isValid = false;
      } else if (!emailPattern.test(email)) {
          $('.error_email').text('Enter a valid Email Address');
          isValid = false;
      } else {
          $('.error_email').text('');
      }
      if (!isValid) {
          return;
      }
      const formData = new FormData();
      formData.append('name', $('.new_email').val());
      formData.append('id', $('.email_id').val());
        $.ajax({
          url: '{{ route("superadmin.getpoemailsave") }}', // Update with your actual endpoint
          type: "POST",
          data: formData,
          processData: false, // Prevent processing of the data
          contentType: false, // Prevent setting content-type header
          success: function (response) {
              if (response.success) {
                 $('#newemailModal').fadeOut();
                  $('#EmailModal').fadeIn();
                  $('body').removeClass('no-scroll');
                  $('#email_row').empty();
                  response.TblPoEmail.data.forEach(email => {
                  const row = $(`
                      <tr>
                          <td id="emailrow">
                              <input type="hidden" class="email_id" value="${email.id}">
                              <input type="hidden" class="email_mail" value="${email.email}">
                              ${email.email}
                          </td>
                          <td>${email.created_by}</td>
                          <td>
                              <button class="zoho-btn zoho-btn-primary edit-email">
                                  <i class="bi bi-pencil"></i> Edit
                              </button>
                          </td>
                      </tr>
                  `);
                  console.log("row",row);

                  $('#email_row').append(row); // 👈 change to your actual <tbody> selector
              });


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
      });
    });
    // Handle row click to show modal with bill details
    $(document).on('click', '.customer-row', function (e) {
        // Don't trigger if clicking on checkbox
        console.log("e.target",e.target);

        if ($(e.target).is('input[type="checkbox"]')) {
            return;
        }
        if ($(e.target).is('.approver')) {
            return;
        }
        if ($(e.target).is('.bi')) {
            return;
        }
        if ($(e.target).closest('.vendor_link').length) return;
        if ($(e.target).closest('.print-pop-btn').length) return;
        if ($(e.target).closest('.print-quot-pop-btn').length) return;
        if ($(e.target).closest('.doc-row').length) return;
        let approval_status=$(this).data('approval_status');
        console.log("approval_status",approval_status);

        if(approval_status === 1){
          $('.edit-btn').hide();
        }else{
          $('.edit-btn').show();
        }

        const billData = {
            id: $(this).data('id'),
            bill_number: $(this).data('bill-number'),
            order_number: $(this).data('order-number'),
            vendor_name: $(this).data('vendor-name'),
            vendor: $(this).data('vendor'),
            vendor_address: $(this).data('vendor-address'),
            bill_date: $(this).data('bill-date'),
            due_date: $(this).data('due-date'),
            payment_terms: $(this).data('payment-terms'),
            grand_total: $(this).data('grand-total'),
            sub_total: $(this).data('sub-total'),
            note: $(this).data('note'),
            discount_amount: $(this).data('discount_amount'),
            purchase_all: $(this).data('purchase_all'),
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


    // Edit button handler
    // $(document).on('click', '.edit-btn', function () {
    //     const billId = $('#billDetailModal').data('bill-id');
    //     window.location.href = "{{ route('superadmin.getpurchasecreate') }}" + "?id=" + billId + "&type=edit";
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
          "{{ route('superadmin.getpurchasecreate') }}" +
          "?id=" + billId +
          "&type=edit" +
          "&redirect_page=" + page;
  });
    $(document).on('click', '.clone-btn', function () {
        const billId = $('#billDetailModal').data('bill-id');
        window.location.href = "{{ route('superadmin.getpurchasecreate') }}" + "?id=" + billId + "&type=clone";
    });
     $(document).on('click', '.pdf-btn', function() {
      const billId = $('#billDetailModal').data('bill-id');
      // Make AJAX request
      $.ajax({
          url: '{{ route("superadmin.getpurchasepdf") }}',
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
              link.download = `purchase_order_${billId}.pdf`;

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
    $(document).on('click', '.print-pop-btn', function () {
      const row = $(this).closest('tr');
      let billId = row.data('id');

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
      const row = $(this).closest('tr');
      let billId = row.data('quotId');
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
        $('#billDetailModal').data('bill-id', data.id);
        console.log("data",data);

        // Set modal title to bill number
        $('.zoho-modal-title').text(data.bill_number || 'Bill Details');


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
        $('#tds_amount').text(formatCurrency(data.purchase_all.tax_amount));
        $('#gst_amount').text(formatCurrency(data.purchase_all.gst_amount));
        $('#grand-total').text(formatCurrency(data.grand_total));
        $('#notes').text(data.note || 'No notes');


        $('.zoho-total-row.export-row').remove();
        if (data.purchase_all.export_name !== null) {
          const exportRow = `
            <div class="zoho-total-row export-row">
              <span>${data.purchase_all.export_name}</span>
              <span id="tds_amount">${formatCurrency(data.purchase_all.export_amount)}</span>
            </div>
          `;

          // Insert it BEFORE the element with class "zoho-total-amount"
          $('.zoho-total-amount').before(exportRow);
        }
        // Bill Items Table
        const itemsHtml = data.items.map(item => `
            <tr>
                <td class="row_first">${item.item_details || 'No description'}</td>
                <td>
                    <div>${data.vendor_name || 'Not specified'}</div>
                    ${item.note ? `<div class="zoho-item-note">${item.note}</div>` : ''}
                </td>
                <td>${item.quantity || 1}</td>
                <td>${item.rate || '0.00'}</td>
                <td>${item.gst_name || '0.00'}</td>
                <td>${item.gst_amount || '0.00'}</td>
                <td>${formatCurrency(item.amount)}</td>
            </tr>
        `).join('');
        $('#bill-items').html(itemsHtml);
        // ---------------- GST / IGST Breakdown ----------------
        let gstSummary = [];

        if (data.items && data.items.length > 0) {

            data.items.forEach(item => {

                // CGST
                if (item.cgst_amount && item.cgst_amount > 0) {
                    gstSummary.push({
                        label: `CGST ${item.gst_rate / 2}%`,
                        amount: parseFloat(item.cgst_amount)
                    });
                }

                // SGST
                if (item.sgst_amount && item.sgst_amount > 0) {
                    gstSummary.push({
                        label: `SGST ${item.gst_rate / 2}%`,
                        amount: parseFloat(item.sgst_amount)
                    });
                }

                // IGST
                if (item.igst_amount && item.igst_amount > 0) {
                    gstSummary.push({
                        label: `IGST ${item.gst_rate}%`,
                        amount: parseFloat(item.igst_amount)
                    });
                }

            });

        }

        // ✅ Group by label and sum amounts
        let mergedSummary = gstSummary.reduce((acc, curr) => {
            if (!acc[curr.label]) {
                acc[curr.label] = { label: curr.label, amount: 0 };
            }
            acc[curr.label].amount += curr.amount;
            return acc;
        }, {});

        // Convert object back to array
        let finalSummary = Object.values(mergedSummary);

        // Render GST Breakdown as <div> blocks (line-wise)
        let gstHtml = finalSummary.map(row => {
            return `
                <div class="tax_show">
                    <label class="gst_label">${row.label}</label>
                    <div class="gst-amount">${formatCurrency(row.amount)}</div>
                </div>
            `;
        }).join('');

        $('.gst-breakdown').html(gstHtml);

        // Journal Entries
        const journalHtml = `
            <tr>
                <td>Employee Advance</td>
                <td>${formatCurrency(data.grand_total)}</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Accounts Payable</td>
                <td>0.00</td>
                <td>${formatCurrency(data.grand_total)}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>${formatCurrency(data.grand_total)}</strong></td>
                <td><strong>${formatCurrency(data.grand_total)}</strong></td>
            </tr>
        `;
        $('#journal-entries').html(journalHtml);
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
        $('.upload_doc').empty();
        let uploads = data.purchase_all.documents;
        if (uploads && uploads.trim() !== "" && uploads !== null ) {
            upload_doc = JSON.parse(uploads);
            upload_doc.forEach(filename =>
                generatePreviewHtml(filename, '../public/uploads/vendor/bill/', '.upload_doc','Uploads Documents')
            );
        }
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

                loadPurchase(
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

        function loadPurchase(page = 1, perPage = $('#per_page').val()) {
            $.ajax({
                url: '{{ route("superadmin.getpurchaseorder") }}',
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
                    sort_column: filters.sort_column,
                    sort_direction: filters.sort_direction
                },
                success: function (data) {
                    if (data && typeof data === 'object' && data.html !== undefined) {
                        $("#purchase-body").html(data.html);
                        updateStats(data.stats);
                    } else {
                        $("#purchase-body").html(data);
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

        // =================== EDIT HISTORY POPUP ===================
        @if(isset($limit_access) && $limit_access == 1)
        $(document).on('click', '.qdt-history-btn', function (e) {
            e.stopPropagation();
            var history = JSON.parse($(this).attr('data-history') || '[]');
            var qno     = $(this).attr('data-qno') || 'Purchase Order';

            $('#historyPopupTitle').text('Edit History — ' + qno);

            var html = '';
            if (history.length === 0) {
                html = '<div class="qdt-history-empty">No edits recorded yet.</div>';
            } else {
                history.slice().reverse().forEach(function (entry, idx) {
                    html += '<div class="qdt-history-entry">' +
                        '<div class="qdt-history-meta">' +
                            '<span class="qdt-history-idx">' + (history.length - idx) + '</span>' +
                            '<span class="qdt-history-by"><i class="bi bi-person-fill me-1"></i>' + (entry.edited_by || '—') + '</span>' +
                            '<span class="qdt-history-role ' + (entry.role === 'Admin' || entry.role === 'Superadmin' ? 'qdt-role-admin' : 'qdt-role-user') + '">' + (entry.role || '') + '</span>' +
                        '</div>' +
                        '<div class="qdt-history-detail">' +
                            '<span class="qdt-history-at"><i class="bi bi-calendar3 me-1"></i>' + (entry.edited_at || '—') + '</span>' +
                            '<span class="qdt-history-status">Status: <strong>' + (entry.status || '—') + '</strong></span>' +
                            (entry.amount ? '<span class="qdt-history-amt">' + entry.amount.toLocaleString('en-IN', {minimumFractionDigits:2}) + '</span>' : '') +
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

        // =================== TOGGLE: STATS & FILTERS ===================
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

        // =================== UPDATE STATS FROM AJAX ===================
        function updateStats(s) {
            if (!s) return;
            function fmt(v) { return '₹' + parseFloat(v||0).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2}); }
            $('.qd-stat-blue   .qd-stat-value').text(s.total);
            $('.qd-stat-green  .qd-stat-value').text(s.approved);
            $('.qd-stat-orange .qd-stat-value').text(s.pending);
            $('.qd-stat-red    .qd-stat-value').text(s.rejected);
            $('.qd-stat-purple .qd-stat-value').text(s.draft);
            $('.qd-stat-blue   .qd-stat-sub').text(fmt(s.total_amount));
            $('.qd-stat-green  .qd-stat-sub').text(fmt(s.approved_amount));
            $('.qd-stat-orange .qd-stat-sub').text(fmt(s.pending_amount));
        }

        // =================== MULTI-SELECT CHANGE LISTENER ===================
        function setupMultiSelect(selectorInput, selectorHidden) {
            $(document).on('click', selectorHidden, function () {

                const selectedIds = $(this).val(); // comma-separated
                const selectedText = $(selectorInput).val();
              console.log(selectedText,'selectedText');

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
                loadPurchase();
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
          loadPurchase();
        });
      //   $('#purchase-body').on('click', 'th.sortable', function () {
      //     let column = $(this).data('column');
      //     console.log('sorting:', column);
      //     alert(11);
      //     if (filters.sort_column === column) {
      //         filters.sort_direction =
      //             filters.sort_direction === 'asc' ? 'desc' : 'asc';
      //     } else {
      //         filters.sort_column = column;
      //         filters.sort_direction = 'asc';
      //     }

      //     $('#purchase-body th.sortable').removeClass('active');
      //     $(this).addClass('active');

      //     loadPurchase();
      // });




        // Date change
        $('.data_values').on('change', function () {
            let dateRange = $(this).val();
            if (dateRange.includes('to')) {
                let parts = dateRange.split(' to ');
                filters.date_from = parts[0].trim();
                filters.date_to = parts[1].trim();
            }
            loadPurchase();
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
            loadPurchase();
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
            loadPurchase();
        });

        // Pagination
        // $(document).on('click', '.pagination a', function (e) {
        //     e.preventDefault();
        //     let url = $(this).attr('href');
        //     let params = new URLSearchParams(url.split('?')[1]);
        //     let page = params.get('page') || 1;
        //     let perPage = $('#per_page').val();
        //     loadPurchase(page, perPage);
        // });

        // // Change per_page
        // $(document).on('change', '#per_page', function () {
        //     loadPurchase(1, $(this).val());
        // });
        // ===== STAT CARD CLICK FILTER =====
        $(document).on('click', '.qd-stat-card[data-stat-filter]', function () {
            var val = $(this).data('stat-filter');
            filters.status_name = (val === undefined || val === null) ? '' : String(val);
            filters.status_id   = filters.status_name;
            $('.status-search-input').val(filters.status_name || '');
            $('.status_id').val(filters.status_name || '');
            $('.qd-stat-card').removeClass('qd-stat-active');
            $(this).addClass('qd-stat-active');
            loadPurchase();
        });

        // Pagination
          $(document).on('click', '.pagination a', function (e) {
          e.preventDefault();

          let url = $(this).attr('href');
          let params = new URLSearchParams(url.split('?')[1]);
          let page = params.get('page') || 1;
          let perPage = $('#per_page').val();

          // ✅ Save current page in memory
          sessionStorage.setItem('bill_made_page', page);

          loadPurchase(page, perPage);
      });
      $(document).on('change', '#per_page', function () {
          sessionStorage.setItem('bill_made_page', 1);
          loadPurchase(1, $(this).val());
      });
    });
 


});
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
  $(document).on('click', '.doc-row', function () {

      $('#documentModal1').modal('show');

      let raw = $(this).attr('data-files') || '';

      // Normalize all escaping
      raw = raw.replace(/&amp;quot;/g, '&quot;')   // &amp;quot; -> &quot;
              .replace(/&quot;/g, '"')          // &quot; -> "
              .replace(/\\\//g, '/');           // \/ -> /

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
  $(document).on('click', '.pdf-btn', function () {
    $('.pdf-btn').removeClass('active');
    $(this).addClass('active');
    const filePath = $(this).data('filepath');
    $('#pdfmain').attr('src', filePath);
  });
});

const vendorsaveUrl = "{{ route('superadmin.vendorsave') }}";
const vendorfetch = "{{ route('superadmin.vendorfetch') }}";
</script>

@if(!empty($_GET['id']))
<script>
$(document).ready(function() {
    let purchaseId = "{{ request()->get('id') }}";
    let perPage = {{ $purchaselist->perPage() }};
    let currentPage = {{ $purchaselist->currentPage() }};

    if (!purchaseId) return;

    // Check if bill is on current page
    let row = $('.customer-row[data-id="' + purchaseId + '"]');
    if (row.length) {
        row.trigger('click'); // open modal immediately
        return;
    }

    // Bill not on current page, find its index (position) from server-side
    let purchaseIndex = null;
    @foreach ($allpurchase as $i => $purchase)
        if ({{ $purchase->id }} == purchaseId) {   // ✅ use BillId not vendorId
            purchaseIndex = {{ $i + 1 }};
        }
    @endforeach

    console.log("purchaseIndex", purchaseIndex);

    if (!purchaseIndex) {
        console.warn("Bill not found in dataset");
        return;
    }

    let targetPage = Math.ceil(purchaseIndex / perPage);
    console.log("purchase index:", purchaseIndex, "Target page:", targetPage);

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

    if (typeof loadPurchase === 'function') {
        loadPurchase(1, $('#per_page').val());
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