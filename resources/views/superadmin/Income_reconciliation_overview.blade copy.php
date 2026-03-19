<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->
  <script type="text/javascript">
        document.addEventListener("contextmenu", function(e) {
            e.preventDefault();
        });
    </script>
<link rel="stylesheet" href="{{ asset('/assets/css/new_discount_dashboard.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

<script>
function rowClick(event) {
    // Remove the 'selected' class from any currently selected row
    const selectedRows = document.querySelectorAll('.selected');
    selectedRows.forEach(row => row.classList.remove('selected'));
    // Add the 'selected' class to the clicked row
    const clickedRow = event.currentTarget;
    clickedRow.classList.add('selected');
}
</script>
  <body style="overflow-x: hidden;">
    <div class="page-loader">
      <div class="bar"></div>
    </div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
             <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                <div class="spinner"></div>
            </div>
        
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="page-title">Discount Form Management</h1>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">Manage and track discount forms</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateModal()">
                            <i class="bi bi-plus-lg"></i>
                            New Document
                        </button>
                    </div>
                </div>
        
                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="stat-label">Total Documents</div>
                        <div class="stat-value" id="totalDocs">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-label">Approved</div>
                        <div class="stat-value" id="approvedDocs">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div class="stat-label">Rejected</div>
                        <div class="stat-value" id="rejectedDocs">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="stat-label">Pending</div>
                        <div class="stat-value" id="pendingDocs">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="bi bi-currency-rupee"></i>
                        </div>
                        <div class="stat-label">Total Discount</div>
                        <div class="stat-value" id="totalDiscount">₹0</div>
                    </div>
                </div>
        
                <!-- Tabs -->
                <div class="nav-tabs-custom">
                    <button class="nav-tab active" id="pendingTab" data-tab="pending">
                        Discount Form Documents
                    </button>
                    <button class="nav-tab" id="savedTab" data-tab="saved">
                        Saved Discount Form
                    </button>
                </div>
        
                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-row">
                        <div>
                            <input type="text" class="form-control" placeholder="Search by MRD, Name..." id="searchInput">
                        </div>
                        
                        <div id="dateRangePicker" class="form-control" style="cursor: pointer;">
                            <i class="bi bi-calendar"></i>
                            <span>Last 30 Days</span>
                        </div>
        
                        <select class="form-select" id="zoneFilter">
                            <option value="">Select Zone</option>
                            <option value="Zone 1">Zone 1</option>
                            <option value="Zone 2">Zone 2</option>
                            <option value="Zone 3">Zone 3</option>
                        </select>
        
                        <select class="form-select" id="branchFilter">
                            <option value="">Select Branch</option>
                            <option value="Branch A">Branch A</option>
                            <option value="Branch B">Branch B</option>
                            <option value="Branch C">Branch C</option>
                        </select>
        
                        <input type="text" class="form-control" placeholder="Enter the MRD" id="mrdFilter">
        
                        <select class="form-select" id="itemsPerPage">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
        
                    <!-- Filter Summary -->
                    <div class="d-flex align-items-center" style="margin-top: 10px;">
                        <p class="text-muted mb-0" style="font-size: 12px;">
                            <span id="mycounts">0</span> Rows for <span id="mydateallviews">Last 30 days</span>
                        </p>
                    </div>
        
                    <!-- Active Filters -->
                    <div class="filter-tags" id="filterTags" style="margin-top: 10px;">
                        <span class="search-label">Search:</span>
                    </div>
                </div>
        
                <!-- Progress Bar -->
                <div id="loader-container" style="display:none;">
                    <div id="progress-bar">Loading: 0%</div>
                </div>
        
                <!-- Table Container -->
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr id="pendingTableHeader">
                                    <th>S.No</th>
                                    <th>Zone Name</th>
                                    <th>Branch Name</th>
                                    <th>Wife MRD No / Name</th>
                                    <th>Husband MRD No / Name</th>
                                    <th>Service Name</th>
                                    <th>Total Bill Value</th>
                                    <th>Discount Expected Request</th>
                                    <th>Post Discount</th>
                                    <th>Counselled By</th>
                                    <th>Authorised By</th>
                                    <th>Final Approved By</th>
                                    <th>B.R. No.</th>
                                    <th>Final Authorised Amount</th>
                                    <th colspan="2">Action</th>
                                </tr>
                                <tr id="savedTableHeader" style="display: none;">
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Zone Name</th>
                                    <th>Branch Name</th>
                                    <th>Wife MRD No / Name</th>
                                    <th>Husband MRD No / Name</th>
                                    <th>Service Name</th>
                                    <th>Total Bill Value</th>
                                    <th>Discount Expected Request</th>
                                    <th>Post Discount</th>
                                    <th>Counselled By</th>
                                    <th>Authorised By</th>
                                    <th>Final Approved By</th>
                                    <th>B.R. No.</th>
                                    <th>Final Authorised Amount</th>
                                    <th>Zonal Approved</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <th>Edit</th>
                                    <th>Print</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Data will be populated here -->
                            </tbody>
                        </table>
                    </div>
        
                    <!-- Pagination -->
                    <div class="footer">
                        <div>
                            Items per page:
                            <select id="itemsPerPageSelect" style="padding: 2px 5px; margin-left: 5px;">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                        </div>
                        <div class="pagination" id="paginationControls">
                            <!-- Pagination buttons will be generated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        // Application State
        const AppState = {
            currentTab: 'pending',
            currentPage: 1,
            itemsPerPage: 10,
            filters: {
                search: '',
                dateRange: { start: moment().subtract(29, 'days'), end: moment() },
                zone: '',
                branch: '',
                mrd: ''
            },
            data: [],
            filteredData: [],
            stats: {
                total: 0,
                approved: 0,
                rejected: 0,
                pending: 0,
                totalDiscount: 0
            }
        };

        // Sample Data matching exact structure
        const sampleData = [
            {
                id: 1,
                date: '2026-02-05',
                zoneName: 'Zone 1',
                branchName: 'Branch A',
                wifeMRD: 'W001',
                wifeName: 'Priya Sharma',
                husbandMRD: 'H001',
                husbandName: 'Rajesh Sharma',
                serviceName: 'IVF Treatment',
                totalBillValue: '250000.00',
                discountExpectedRequest: '25000 (10%)',
                postDiscount: '20000',
                counselledBy: 'Dr. Kumar',
                authorisedBy: 'Admin',
                finalApprovedBy: 'Director',
                brNo: 'BR-001',
                finalAuthorisedAmount: '230000.00',
                zonalApproved: 'Pending',
                status: 'pending',
                isPending: true
            },
            {
                id: 2,
                date: '2026-02-04',
                zoneName: 'Zone 2',
                branchName: 'Branch B',
                wifeMRD: 'W002',
                wifeName: 'Anita Verma',
                husbandMRD: 'H002',
                husbandName: 'Suresh Verma',
                serviceName: 'ICSI Treatment',
                totalBillValue: '180000.00',
                discountExpectedRequest: '15000 (8.3%)',
                postDiscount: '12000',
                counselledBy: 'Dr. Patel',
                authorisedBy: 'Admin',
                finalApprovedBy: 'Director',
                brNo: 'BR-002',
                finalAuthorisedAmount: '168000.00',
                zonalApproved: 'Approved',
                status: 'approved',
                isPending: false
            },
            {
                id: 3,
                date: '2026-02-03',
                zoneName: 'Zone 1',
                branchName: 'Branch C',
                wifeMRD: 'W003',
                wifeName: 'Kavita Singh',
                husbandMRD: 'H003',
                husbandName: 'Amit Singh',
                serviceName: 'IUI Treatment',
                totalBillValue: '120000.00',
                discountExpectedRequest: '10000 (8.3%)',
                postDiscount: '8000',
                counselledBy: 'Dr. Reddy',
                authorisedBy: 'Admin',
                finalApprovedBy: 'Director',
                brNo: 'BR-003',
                finalAuthorisedAmount: '112000.00',
                zonalApproved: 'Rejected',
                status: 'rejected',
                isPending: false
            },
            {
                id: 4,
                date: '2026-02-02',
                zoneName: 'Zone 3',
                branchName: 'Branch A',
                wifeMRD: 'W004',
                wifeName: 'Sunita Devi',
                husbandMRD: 'H004',
                husbandName: 'Ramesh Kumar',
                serviceName: 'Fertility Consultation',
                totalBillValue: '50000.00',
                discountExpectedRequest: '5000 (10%)',
                postDiscount: '4000',
                counselledBy: 'Dr. Shah',
                authorisedBy: 'Manager',
                finalApprovedBy: 'Director',
                brNo: 'BR-004',
                finalAuthorisedAmount: '46000.00',
                zonalApproved: 'Approved',
                status: 'approved',
                isPending: false
            },
            {
                id: 5,
                date: '2026-02-01',
                zoneName: 'Zone 2',
                branchName: 'Branch B',
                wifeMRD: 'W005',
                wifeName: 'Rekha Jain',
                husbandMRD: 'H005',
                husbandName: 'Vikas Jain',
                serviceName: 'Egg Freezing',
                totalBillValue: '150000.00',
                discountExpectedRequest: '12000 (8%)',
                postDiscount: '10000',
                counselledBy: 'Dr. Mehta',
                authorisedBy: 'Admin',
                finalApprovedBy: 'Director',
                brNo: 'BR-005',
                finalAuthorisedAmount: '140000.00',
                zonalApproved: 'Pending Review',
                status: 'approved',
                isPending: false
            },
            {
                id: 6,
                date: '2026-01-31',
                zoneName: 'Zone 1',
                branchName: 'Branch C',
                wifeMRD: 'W006',
                wifeName: 'Neha Gupta',
                husbandMRD: 'H006',
                husbandName: 'Sanjay Gupta',
                serviceName: 'IVF with ICSI',
                totalBillValue: '300000.00',
                discountExpectedRequest: '30000 (10%)',
                postDiscount: '25000',
                counselledBy: 'Dr. Kumar',
                authorisedBy: 'Admin',
                finalApprovedBy: 'Director',
                brNo: 'BR-006',
                finalAuthorisedAmount: '275000.00',
                zonalApproved: 'Rejected',
                status: 'rejected',
                isPending: false
            }
        ];

        // Initialize Date Range Picker
        $('#dateRangePicker').daterangepicker({
            startDate: AppState.filters.dateRange.start,
            endDate: AppState.filters.dateRange.end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, function(start, end, label) {
            AppState.filters.dateRange = { start, end };
            let displayText = label;
            if (!label || label === 'Custom Range') {
                displayText = start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY');
            }
            $('#dateRangePicker span').html(displayText);
            $('#mydateallviews').text(displayText);
            filterData();
        });

        // Event Listeners
        $('#searchInput').on('input', debounce(function() {
            AppState.filters.search = $(this).val();
            filterData();
        }, 300));

        $('#zoneFilter, #branchFilter, #mrdFilter').on('change input', function() {
            const filterType = $(this).attr('id').replace('Filter', '');
            AppState.filters[filterType] = $(this).val();
            filterData();
        });

        $('#itemsPerPage, #itemsPerPageSelect').on('change', function() {
            AppState.itemsPerPage = parseInt($(this).val());
            $('#itemsPerPage').val(AppState.itemsPerPage);
            $('#itemsPerPageSelect').val(AppState.itemsPerPage);
            AppState.currentPage = 1;
            renderTable();
        });

        // Initialize
        function init() {
            AppState.data = sampleData;
            filterData();
            updateStats();
        }

        // Filter Data
        function filterData() {
            let filtered = AppState.data;

            // Tab filter (most important)
            if (AppState.currentTab === 'pending') {
                filtered = filtered.filter(item => item.isPending === true);
            } else {
                filtered = filtered.filter(item => item.isPending === false);
            }

            // Search filter
            if (AppState.filters.search) {
                const search = AppState.filters.search.toLowerCase();
                filtered = filtered.filter(item => 
                    item.wifeName.toLowerCase().includes(search) ||
                    item.husbandName.toLowerCase().includes(search) ||
                    item.wifeMRD.toLowerCase().includes(search) ||
                    item.husbandMRD.toLowerCase().includes(search) ||
                    item.serviceName.toLowerCase().includes(search)
                );
            }

            // Date range filter
            filtered = filtered.filter(item => {
                const itemDate = moment(item.date);
                return itemDate.isBetween(AppState.filters.dateRange.start, AppState.filters.dateRange.end, 'days', '[]');
            });

            // Zone filter
            if (AppState.filters.zone) {
                filtered = filtered.filter(item => item.zoneName === AppState.filters.zone);
            }

            // Branch filter
            if (AppState.filters.branch) {
                filtered = filtered.filter(item => item.branchName === AppState.filters.branch);
            }

            // MRD filter
            if (AppState.filters.mrd) {
                const mrd = AppState.filters.mrd.toLowerCase();
                filtered = filtered.filter(item => 
                    item.wifeMRD.toLowerCase().includes(mrd) ||
                    item.husbandMRD.toLowerCase().includes(mrd)
                );
            }

            AppState.filteredData = filtered;
            AppState.currentPage = 1;
            renderTable();
            updateFilterTags();
            updateCounts();
        }

        // Render Table
        function renderTable() {
            const start = (AppState.currentPage - 1) * AppState.itemsPerPage;
            const end = start + AppState.itemsPerPage;
            const pageData = AppState.filteredData.slice(start, end);
            
            const tbody = $('#tableBody');
            tbody.empty();

            // Switch table headers based on current tab
            if (AppState.currentTab === 'pending') {
                $('#pendingTableHeader').show();
                $('#savedTableHeader').hide();
            } else {
                $('#pendingTableHeader').hide();
                $('#savedTableHeader').show();
            }

            if (pageData.length === 0) {
                const colspan = AppState.currentTab === 'pending' ? 16 : 20;
                tbody.html(`
                    <tr>
                        <td colspan="${colspan}">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h3>No documents found</h3>
                                <p>Try adjusting your filters or create a new document</p>
                            </div>
                        </td>
                    </tr>
                `);
            } else {
                pageData.forEach((item, index) => {
                    let row = '';
                    
                    if (AppState.currentTab === 'pending') {
                        // Pending Documents Table Structure
                        row = `
                            <tr onclick="rowClick(event)">
                                <td>${start + index + 1}</td>
                                <td>${item.zoneName}</td>
                                <td>${item.branchName}</td>
                                <td>${item.wifeMRD} / ${item.wifeName}</td>
                                <td>${item.husbandMRD} / ${item.husbandName}</td>
                                <td>${item.serviceName}</td>
                                <td>₹${parseFloat(item.totalBillValue).toLocaleString()}</td>
                                <td>${item.discountExpectedRequest}</td>
                                <td>₹${parseFloat(item.postDiscount).toLocaleString()}</td>
                                <td>${item.counselledBy}</td>
                                <td>${item.authorisedBy}</td>
                                <td>${item.finalApprovedBy}</td>
                                <td>${item.brNo}</td>
                                <td>₹${parseFloat(item.finalAuthorisedAmount).toLocaleString()}</td>
                                <td colspan="2">
                                    <button class="ship-now" onclick="viewDocument(${item.id})">View</button>
                                </td>
                            </tr>
                        `;
                    } else {
                        // Saved Documents Table Structure
                        const statusClass = item.status === 'approved' ? 'approved' : 
                                          item.status === 'rejected' ? 'rejected' : 'pending';
                        row = `
                            <tr onclick="rowClick(event)">
                                <td>${start + index + 1}</td>
                                <td>${moment(item.date).format('DD/MM/YYYY')}</td>
                                <td>${item.zoneName}</td>
                                <td>${item.branchName}</td>
                                <td>${item.wifeMRD} / ${item.wifeName}</td>
                                <td>${item.husbandMRD} / ${item.husbandName}</td>
                                <td>${item.serviceName}</td>
                                <td>₹${parseFloat(item.totalBillValue).toLocaleString()}</td>
                                <td>${item.discountExpectedRequest}</td>
                                <td>₹${parseFloat(item.postDiscount).toLocaleString()}</td>
                                <td>${item.counselledBy}</td>
                                <td>${item.authorisedBy}</td>
                                <td>${item.finalApprovedBy}</td>
                                <td>${item.brNo}</td>
                                <td>₹${parseFloat(item.finalAuthorisedAmount).toLocaleString()}</td>
                                <td><span class="badge badge-${statusClass}">${item.zonalApproved}</span></td>
                                <td><span class="badge badge-${statusClass}">${item.status}</span></td>
                                <td>
                                    <div class="action-icons" data-id="${item.id}">
                                        <span class="approve-btn" data-status="1" title="Approve">✔</span>
                                        <span class="reject-btn" data-status="2" title="Reject">✖</span>
                                    </div>
                                </td>
                                <td><button class="ship-now" onclick="editDocument(${item.id})">Edit</button></td>
                                <td><button class="ship-now" onclick="printDocument(${item.id})">Print</button></td>
                            </tr>
                        `;
                    }
                    tbody.append(row);
                });
            }

            updatePagination();
        }

        // Update Pagination
        function updatePagination() {
            const totalPages = Math.ceil(AppState.filteredData.length / AppState.itemsPerPage);
            const controls = $('#paginationControls');
            controls.empty();

            // Previous button
            controls.append(`
                <button onclick="changePage(${AppState.currentPage - 1})" 
                        ${AppState.currentPage === 1 ? 'disabled' : ''}>
                    ‹
                </button>
            `);

            // Page numbers
            const maxVisible = 5;
            let startPage = Math.max(1, AppState.currentPage - 2);
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                controls.append(`
                    <button class="${i === AppState.currentPage ? 'active' : ''}" 
                            onclick="changePage(${i})">
                        ${i}
                    </button>
                `);
            }

            // Next button
            controls.append(`
                <button onclick="changePage(${AppState.currentPage + 1})" 
                        ${AppState.currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}>
                    ›
                </button>
            `);
        }

        // Update Counts
        function updateCounts() {
            $('#mycounts').text(AppState.filteredData.length);
        }

        // Update Stats
        function updateStats() {
            AppState.stats.total = AppState.data.length;
            AppState.stats.approved = AppState.data.filter(d => d.status === 'approved').length;
            AppState.stats.rejected = AppState.data.filter(d => d.status === 'rejected').length;
            AppState.stats.pending = AppState.data.filter(d => d.status === 'pending').length;
            AppState.stats.totalDiscount = AppState.data.reduce((sum, d) => sum + parseFloat(d.postDiscount), 0);

            $('#totalDocs').text(AppState.stats.total);
            $('#approvedDocs').text(AppState.stats.approved);
            $('#rejectedDocs').text(AppState.stats.rejected);
            $('#pendingDocs').text(AppState.stats.pending);
            $('#totalDiscount').text('₹' + AppState.stats.totalDiscount.toLocaleString());
        }

        // Update Filter Tags
        function updateFilterTags() {
            const tags = $('#filterTags');
            tags.html('<span class="search-label">Search:</span>');

            if (AppState.filters.zone) {
                tags.append(`
                    <span class="filter-tag" onclick="removeFilter('zone')">
                        ${AppState.filters.zone}
                    </span>
                `);
            }

            if (AppState.filters.branch) {
                tags.append(`
                    <span class="filter-tag" onclick="removeFilter('branch')">
                        ${AppState.filters.branch}
                    </span>
                `);
            }

            if (AppState.filters.mrd) {
                tags.append(`
                    <span class="filter-tag" onclick="removeFilter('mrd')">
                        MRD: ${AppState.filters.mrd}
                    </span>
                `);
            }

            const hasFilters = AppState.filters.zone || AppState.filters.branch || AppState.filters.mrd;
            if (hasFilters) {
                tags.append(`
                    <button class="clear-all-badge" onclick="clearAllFilters()">
                        Clear all
                    </button>
                `);
            }
        }

        // Helper Functions
        function changePage(page) {
            const totalPages = Math.ceil(AppState.filteredData.length / AppState.itemsPerPage);
            if (page >= 1 && page <= totalPages) {
                AppState.currentPage = page;
                renderTable();
            }
        }

        function switchTab(tab) {
            console.log('Switching to tab:', tab);
            AppState.currentTab = tab;
            
            // Update active tab styling
            $('.nav-tab').removeClass('active');
            $(`[data-tab="${tab}"]`).addClass('active');
            
            // Reset page to 1 when switching tabs
            AppState.currentPage = 1;
            
            // Apply filters and re-render
            filterData();
        }

        // Tab click handler
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            const tab = $(this).data('tab');
            switchTab(tab);
        });

        function removeFilter(filterType) {
            AppState.filters[filterType] = '';
            $(`#${filterType}Filter`).val('');
            filterData();
        }

        function clearAllFilters() {
            AppState.filters.zone = '';
            AppState.filters.branch = '';
            AppState.filters.mrd = '';
            $('#zoneFilter, #branchFilter, #mrdFilter').val('');
            filterData();
        }

        function rowClick(event) {
            $('.data-table tbody tr').removeClass('selected');
            $(event.currentTarget).addClass('selected');
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function showLoading() {
            $('#loadingOverlay').fadeIn(200);
        }

        function hideLoading() {
            $('#loadingOverlay').fadeOut(200);
        }

        // Action Functions
        function viewDocument(id) {
            console.log('View document:', id);
            alert('View Document: ' + id);
        }

        function editDocument(id) {
            console.log('Edit document:', id);
            alert('Edit Document: ' + id);
        }

        function printDocument(id) {
            console.log('Print document:', id);
            alert('Print Document: ' + id);
        }

        function openCreateModal() {
            console.log('Open create modal');
            alert('Create New Document');
        }

        // Initialize on page load
        $(document).ready(function() {
            init();
        });

        // Approve/Reject action
        $(document).on('click', '.approve-btn, .reject-btn', function(e) {
            e.stopPropagation();
            const status = $(this).data('status');
            const id = $(this).closest('.action-icons').data('id');
            const actionText = status === 1 ? 'Approve' : 'Reject';
            
            if (confirm(`Are you sure you want to ${actionText.toLowerCase()} this document?`)) {
                console.log(`${actionText} document:`, id);
                alert(`Document ${id} ${actionText.toLowerCase()}d successfully`);
            }
        });
    </script>
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
