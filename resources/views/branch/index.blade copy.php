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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="{{ asset('/assets/css/branch-financial.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-card-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

    .stat-icon {
        font-size: 48px;
        opacity: 0.8;
    }

    .stat-content h6 {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
    }

    .stat-content h3 {
        margin: 5px 0 0 0;
        font-size: 24px;
        font-weight: bold;
    }

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

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table tfoot tr {
        background: #e9ecef;
    }

    .card {
        border-radius: 10px;
        border: none;
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
    }

    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
    }
</style>
<body style="overflow-x: hidden;">
    <div class="page-loader">
      <div class="bar"></div>
    </div>
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
                                        <p class="text-white-50 mb-0">View and export branch financial data</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-light text-dark px-3 py-2 fs-6">
                                            <i class="fas fa-eye me-2"></i>Read Only
                                        </span>
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
                            <div class="stat-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Total Reports</h6>
                                <h3>{{ number_format($summary['report_count']) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-2">
                            <div class="stat-icon">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Total Radiant</h6>
                                <h3>₹{{ number_format($summary['total_radiant'], 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-3">
                            <div class="stat-icon">
                                <i class="fas fa-minus-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Total Card</h6>
                                <h3>₹{{ number_format($summary['total_card'], 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-4">
                            <div class="stat-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Total Bank</h6>
                                <h3>₹{{ number_format($summary['total_bank'], 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-2">
                            <div class="stat-icon">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Discount Amount</h6>
                                <h3>₹{{ number_format($summary['total_discount'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-3">
                            <div class="stat-icon">
                                <i class="fas fa-minus-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Cancel Amount</h6>
                                <h3>₹{{ number_format($summary['total_cancel'], 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-4">
                            <div class="stat-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Refund Amount</h6>
                                <h3>₹{{ number_format($summary['total_refund'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-3">
                            <div class="stat-icon">
                                <i class="fas fa-minus-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Total Deductions</h6>
                                <h3>₹{{ number_format($summary['total_deductions'], 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-card-4">
                            <div class="stat-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Net Amount</h6>
                                <h3>₹{{ number_format($summary['net_amount'], 2) }}</h3>
                            </div>
                        </div>
                    </div> -->
                </div>

                <!-- Filters Card -->
              <div class="card mb-4 shadow-sm">
                    <div class="card-header filter-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filters & Export
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="filterForm">
                            <div class="row g-3 align-items-end">
                                <!-- Date Range -->
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Start Date</label>
                                    <input type="date" class="form-control auto-filter" id="start_date" name="start_date" value="{{ request('start_date') }}">
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">End Date</label>
                                    <input type="date" class="form-control auto-filter" id="end_date" name="end_date" value="{{ request('end_date') }}">
                                </div>

                                <!-- Zone Filter (Multiple) -->
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Zone</label>
                                    <select class="form-select form-control auto-filter" id="zone_id" name="zone_id[]" multiple>
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}"
                                                {{ in_array($zone->id, request('zone_id', [])) ? 'selected' : '' }}>
                                                {{ $zone->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                                </div>

                                <!-- Branch Filter (Multiple) -->
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Branch</label>
                                    <select class="form-select form-control auto-filter" id="branch_id" name="branch_id[]" multiple>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" data-zone="{{ $branch->zone_id }}"
                                                {{ in_array($branch->id, request('branch_id', [])) ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                                </div>

                                <!-- Reset Button -->
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-secondary w-100" id="resetFilters">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-success" id="exportExcel">
                                        <i class="fas fa-file-excel me-2"></i>Export Excel
                                    </button>
                                    <button type="button" class="btn btn-info" id="exportCsv">
                                        <i class="fas fa-file-csv me-2"></i>Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading Overlay -->
                <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-light mt-2">Loading...</p>
                    </div>
                </div>
                <!-- Breakdown Summary -->
                <!-- <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-start border-success border-4 h-100">
                            <div class="card-body">
                                <h6 class="text-success mb-3">Collection Breakdown</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Radiant Cash:</span>
                                    <strong>₹{{ number_format($summary['total_radiant'], 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Card Amount:</span>
                                    <strong>₹{{ number_format($summary['total_card'], 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Bank Deposit:</span>
                                    <strong>₹{{ number_format($summary['total_bank'], 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-start border-danger border-4 h-100">
                            <div class="card-body">
                                <h6 class="text-danger mb-3">Deduction Breakdown</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Discounts:</span>
                                    <strong>₹{{ number_format($summary['total_discount'], 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Cancelled Bills:</span>
                                    <strong>₹{{ number_format($summary['total_cancel'], 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Refunds:</span>
                                    <strong>₹{{ number_format($summary['total_refund'], 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-start border-primary border-4 h-100">
                            <div class="card-body">
                                <h6 class="text-primary mb-3">Summary</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Reports:</span>
                                    <strong>{{ number_format($summary['report_count']) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Gross Collection:</span>
                                    <strong>₹{{ number_format($summary['total_collection'], 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-success fw-bold">Net Amount:</span>
                                    <strong class="text-success">₹{{ number_format($summary['net_amount'], 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Data Table -->
                <div class="card shadow-sm">
                    <div class="card-header table-header">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2"></i>Financial Report Data
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center" style="width: 60px;">#</th>
                                        <th>Date</th>
                                        <th>Zone</th>
                                        <th>Branch</th>
                                        <th class="text-end">Radiant</th>
                                        <th class="text-end">Card</th>
                                        <th class="text-end">Bank</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Deductions</th>
                                        <th class="text-end">Net</th>
                                        <th>Personnel</th>
                                        <th class="text-center">Files</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reports as $index => $report)
                                    <tr>
                                        <td class="text-center">{{ $reports->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $report->report_date->format('d M Y') }}</strong>
                                            @if($report->radiant_collected_date)
                                                <br><small class="text-muted">R: {{ $report->radiant_collected_date->format('d M Y') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $report->zone_name }}</span>
                                        </td>
                                        <td>{{ $report->branch_name }}</td>
                                        <td class="text-end text-success fw-bold">₹{{ number_format($report->radiant_collection_amount, 2) }}</td>
                                        <td class="text-end text-primary fw-bold">₹{{ number_format($report->actual_card_amount, 2) }}</td>
                                        <td class="text-end text-warning fw-bold">₹{{ number_format($report->bank_deposit_amount, 2) }}</td>
                                        <td class="text-end fw-bold">₹{{ number_format($report->total_collection, 2) }}</td>
                                        <td class="text-end text-danger">₹{{ number_format($report->total_deductions, 2) }}</td>
                                        <td class="text-end fw-bold text-success">₹{{ number_format($report->net_amount, 2) }}</td>
                                        <td>
                                            <small>
                                                @if($report->placed_by_whom)
                                                    <strong>P:</strong> {{ $report->placed_by_whom }}<br>
                                                @endif
                                                @if($report->locker_by_whom)
                                                    <strong>L:</strong> {{ $report->locker_by_whom }}<br>
                                                @endif
                                                @if($report->who_gave_radiant_cash)
                                                    <strong>G:</strong> {{ $report->who_gave_radiant_cash }}
                                                @endif
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $hasFiles = ($report->radiant_collection_files && count($report->radiant_collection_files) > 0) ||
                                                            ($report->actual_card_files && count($report->actual_card_files) > 0) ||
                                                            ($report->bank_deposit_files && count($report->bank_deposit_files) > 0);
                                            @endphp
                                            @if($hasFiles)
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewFiles({{ $report->id }})">
                                                    <i class="fas fa-paperclip"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('financial-reports.show', $report->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="13" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted mb-0">No reports found matching your filters</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($reports->isNotEmpty())
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td colspan="4" class="text-end">TOTALS (Filtered):</td>
                                        <td class="text-end text-success">₹{{ number_format($summary['total_radiant'], 2) }}</td>
                                        <td class="text-end text-primary">₹{{ number_format($summary['total_card'], 2) }}</td>
                                        <td class="text-end text-warning">₹{{ number_format($summary['total_bank'], 2) }}</td>
                                        <td class="text-end">₹{{ number_format($summary['total_collection'], 2) }}</td>
                                        <td class="text-end text-danger">₹{{ number_format($summary['total_deductions'], 2) }}</td>
                                        <td class="text-end text-success">₹{{ number_format($summary['net_amount'], 2) }}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($reports->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} entries
                                </small>
                            </div>
                            <div>
                                {{ $reports->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

            </div>

            <!-- Files Modal -->
            <div class="modal fade" id="filesModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-paperclip me-2"></i>Attached Files
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="filesContent">
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- income related script start -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('/assets/income_new/Income_reconciliation_branch.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ asset('/assets/js/branch/branch-financial.js') }}"></script>
<script>
// Define route variables for JavaScript
const storeRoute = "{{ route('branch-financial.store') }}";
const updateRoute = "{{ route('branch-financial.update', ':id') }}";
const showRoute = "{{ route('branch-financial.show', ':id') }}";
const destroyRoute = "{{ route('branch-financial.destroy', ':id') }}";
</script>
<script>
    function exportExcel() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = '{{ route("financial-reports.export.excel") }}?' + params.toString();
    }

    function exportCsv() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = '{{ route("financial-reports.export.csv") }}?' + params.toString();
    }

    function viewFiles(reportId) {
        const modal = new bootstrap.Modal(document.getElementById('filesModal'));
        modal.show();

        fetch(`/financial-reports/${reportId}`)
            .then(response => response.json())
            .then(data => {
                let content = '<div class="row">';
                
                // Radiant files
                if (data.radiant_collection_files && data.radiant_collection_files.length > 0) {
                    content += '<div class="col-12 mb-3">';
                    content += '<h6 class="text-success"><i class="fas fa-money-bill me-2"></i>Radiant Cash Files</h6>';
                    content += generateFileLinks(data.id, 'radiant_collection', data.radiant_collection_files);
                    content += '</div>';
                }

                // Card files
                if (data.actual_card_files && data.actual_card_files.length > 0) {
                    content += '<div class="col-12 mb-3">';
                    content += '<h6 class="text-primary"><i class="fas fa-credit-card me-2"></i>Card Files</h6>';
                    content += generateFileLinks(data.id, 'actual_card', data.actual_card_files);
                    content += '</div>';
                }

                // Bank files
                if (data.bank_deposit_files && data.bank_deposit_files.length > 0) {
                    content += '<div class="col-12 mb-3">';
                    content += '<h6 class="text-warning"><i class="fas fa-university me-2"></i>Bank Deposit Files</h6>';
                    content += generateFileLinks(data.id, 'bank_deposit', data.bank_deposit_files);
                    content += '</div>';
                }

                content += '</div>';
                document.getElementById('filesContent').innerHTML = content;
            })
            .catch(error => {
                document.getElementById('filesContent').innerHTML = 
                    '<p class="text-danger text-center">Error loading files</p>';
            });
    }

    function generateFileLinks(reportId, type, files) {
        let html = '<div class="list-group">';
        files.forEach((file, index) => {
            const fileName = file.split('/').pop();
            const ext = fileName.split('.').pop().toLowerCase();
            let icon = 'fa-file';
            if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) icon = 'fa-file-image';
            if (ext === 'pdf') icon = 'fa-file-pdf';
            
            html += `
                <a href="/storage/${file}" target="_blank" class="list-group-item list-group-item-action">
                    <i class="fas ${icon} me-2"></i>${fileName}
                </a>
            `;
        });
        html += '</div>';
        return html;
    }
     // Configuration
    const CONFIG = {
        debounceDelay: 500,
        routes: {
            index: "{{ route('financial-reports.index') }}",
            export: "{{ route('financial-reports.export') }}" // You'll need to create this route
        }
    };

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    // Show loading overlay
    function showLoading() {
        $('#loadingOverlay').fadeIn(200);
    }

    // Hide loading overlay
    function hideLoading() {
        $('#loadingOverlay').fadeOut(200);
    }

    // Get current filter values
    function getFilterValues() {
        const filters = {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            zone_id: $('#zone_id').val() || [], // Get selected values as array
            branch_id: $('#branch_id').val() || [] // Get selected values as array
        };
        return filters;
    }

    // Apply filters via AJAX
    function applyFilters() {
        const filters = getFilterValues();
        
        showLoading();

        $.ajax({
            url: CONFIG.routes.index,
            type: 'GET',
            data: filters,
            dataType: 'json',
            success: function(response) {
                // Update reports table
                if (response.html) {
                    $('#reportsContainer').html(response.html);
                }

                // Update summary statistics
                if (response.summary) {
                    updateSummary(response.summary);
                }

                // Update URL without page reload
                updateUrlParams(filters);

                hideLoading();
            },
            error: function(xhr, status, error) {
                console.error('Filter error:', error);
                hideLoading();
                
                // Show error message
                alert('Error loading data. Please try again.');
            }
        });
    }

    // Debounced version of applyFilters
    const debouncedApplyFilters = debounce(applyFilters, CONFIG.debounceDelay);

    // Update summary statistics
    function updateSummary(summary) {
        $('#summaryContainer').html(`
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Total Collection</h6>
                            <h4>${formatCurrency(summary.total_collection)}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Total Deductions</h6>
                            <h4>${formatCurrency(summary.total_deductions)}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Net Amount</h6>
                            <h4>${formatCurrency(summary.net_amount)}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Report Count</h6>
                            <h4>${summary.report_count}</h4>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    // Format currency
    function formatCurrency(amount) {
        return '₹' + parseFloat(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Update URL parameters without reload
    function updateUrlParams(filters) {
        const url = new URL(window.location.href);
        const params = new URLSearchParams();

        // Add non-empty filters to URL
        if (filters.start_date) params.set('start_date', filters.start_date);
        if (filters.end_date) params.set('end_date', filters.end_date);
        
        if (filters.zone_id && filters.zone_id.length > 0) {
            filters.zone_id.forEach(id => params.append('zone_id[]', id));
        }
        
        if (filters.branch_id && filters.branch_id.length > 0) {
            filters.branch_id.forEach(id => params.append('branch_id[]', id));
        }

        // Update browser URL
        const newUrl = url.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
    }

    // Filter branches based on selected zones
    function filterBranches() {
        const selectedZones = $('#zone_id').val() || [];
        const branchSelect = $('#branch_id');
        const allOptions = branchSelect.find('option');

        if (selectedZones.length === 0) {
            // Show all branches
            allOptions.show().prop('disabled', false);
        } else {
            // Filter branches by selected zones
            allOptions.each(function() {
                const branchZone = $(this).data('zone');
                
                if (selectedZones.includes(branchZone.toString())) {
                    $(this).show().prop('disabled', false);
                } else {
                    $(this).hide().prop('disabled', true).prop('selected', false);
                }
            });
        }

        // Trigger change to update display
        branchSelect.trigger('chosen:updated'); // If using Chosen plugin
        branchSelect.trigger('select2:update'); // If using Select2 plugin
    }

    // Reset all filters
    function resetFilters() {
        $('#start_date').val('');
        $('#end_date').val('');
        $('#zone_id').val([]).trigger('change');
        $('#branch_id').val([]).trigger('change');
        
        // Redirect to base URL
        window.location.href = CONFIG.routes.index;
    }

    // Export data
    function exportData(format) {
        const filters = getFilterValues();
        filters.export = format;

        // Create form and submit
        const form = $('<form>', {
            method: 'GET',
            action: CONFIG.routes.export
        });

        // Add filter parameters
        $.each(filters, function(key, value) {
            if (Array.isArray(value)) {
                value.forEach(function(val) {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: key + '[]',
                        value: val
                    }));
                });
            } else if (value) {
                form.append($('<input>', {
                    type: 'hidden',
                    name: key,
                    value: value
                }));
            }
        });

        // Submit form
        form.appendTo('body').submit().remove();
    }

    // Initialize auto-filter functionality
    function initAutoFilter() {
        // Date inputs - apply filter on change
        $('#start_date, #end_date').on('change', function() {
            applyFilters();
        });

        // Zone select - filter branches and apply filter
        $('#zone_id').on('change', function() {
            filterBranches();
            applyFilters();
        });

        // Branch select - apply filter on change
        $('#branch_id').on('change', function() {
            applyFilters();
        });

        // Reset button
        $('#resetFilters').on('click', function() {
            resetFilters();
        });

        // Export buttons
        $('#exportExcel').on('click', function() {
            exportData('excel');
        });

        $('#exportCsv').on('click', function() {
            exportData('csv');
        });

        // Initial branch filter on page load
        filterBranches();
    }

    // Initialize when document is ready
    $(document).ready(function() {
        initAutoFilter();
        
        console.log('Financial Reports Auto-Filter initialized');
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        location.reload();
    });
    
</script>
@include('superadmin.superadminfooter')
</body>
  <!-- [Body] end -->
</html>
