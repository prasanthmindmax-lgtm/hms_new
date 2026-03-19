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

    .summary-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 2px solid #28a745;
    }

    .card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }

    .list-group-item {
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        border-left-color: #007bff;
        background-color: #f8f9fa;
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
    
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card header-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="text-white mb-0">
                                            <i class="fas fa-file-invoice me-2"></i>Financial Report Details
                                        </h3>
                                        <p class="text-white-50 mb-0 mt-2">ID: #{{ $report->id }} | Date: {{ $report->report_date->format('d M Y') }}</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ route('financial-reports.index') }}" class="btn btn-light">
                                            <i class="fas fa-arrow-left me-2"></i>Back to Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        
                        <!-- Basic Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Report Date</label>
                                        <p class="fw-bold">{{ $report->report_date->format('d M Y') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Radiant Collected Date</label>
                                        <p class="fw-bold">{{ $report->radiant_collected_date ? $report->radiant_collected_date->format('d M Y') : 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Zone</label>
                                        <p><span class="badge bg-info fs-6">{{ $report->zone_name }}</span></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Branch</label>
                                        <p class="fw-bold">{{ $report->branch_name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Collection Details -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Collection Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Radiant Cash Collection</label>
                                        <h4 class="text-success">₹{{ number_format($report->radiant_collection_amount, 2) }}</h4>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Card Amount</label>
                                        <h4 class="text-primary">₹{{ number_format($report->actual_card_amount, 2) }}</h4>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Bank Deposit</label>
                                        <h4 class="text-warning">₹{{ number_format($report->bank_deposit_amount, 2) }}</h4>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <label class="text-muted small">Total Collection</label>
                                        <h3 class="text-dark">₹{{ number_format($report->total_collection, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deductions -->
                        <div class="card mb-4">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="fas fa-minus-circle me-2"></i>Deductions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Today's Discount</label>
                                        <h5 class="text-danger">₹{{ number_format($report->today_discount_amount, 2) }}</h5>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Cancelled Bills</label>
                                        <h5 class="text-danger">₹{{ number_format($report->cancel_bill_amount, 2) }}</h5>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Refunds</label>
                                        <h5 class="text-danger">₹{{ number_format($report->refund_bill_amount, 2) }}</h5>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <label class="text-muted small">Total Deductions</label>
                                        <h4 class="text-danger">₹{{ number_format($report->total_deductions, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personnel Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Personnel Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Placed By</label>
                                        <p class="fw-bold">{{ $report->placed_by_whom ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Locker By</label>
                                        <p class="fw-bold">{{ $report->locker_by_whom ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Cash Given By</label>
                                        <p class="fw-bold">{{ $report->who_gave_radiant_cash ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        
                        <!-- Summary Card -->
                        <!-- <div class="card mb-4 summary-card">
                            <div class="card-body text-center p-4">
                                <h6 class="text-muted mb-3">NET AMOUNT</h6>
                                <h1 class="display-4 text-success mb-0">₹{{ number_format($report->net_amount, 2) }}</h1>
                                <hr>
                                <div class="text-start">
                                    <small class="d-flex justify-content-between mb-2">
                                        <span>Collection:</span>
                                        <strong>₹{{ number_format($report->total_collection, 2) }}</strong>
                                    </small>
                                    <small class="d-flex justify-content-between text-danger">
                                        <span>Deductions:</span>
                                        <strong>-₹{{ number_format($report->total_deductions, 2) }}</strong>
                                    </small>
                                </div>
                            </div>
                        </div> -->

                        <!-- Files -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i>Attachments</h5>
                            </div>
                            <div class="card-body">
                                @if($report->radiant_collection_files && count($report->radiant_collection_files) > 0)
                                    <h6 class="text-success mb-2">Radiant Cash Files</h6>
                                    <div class="list-group mb-3">
                                        @foreach($report->radiant_collection_files as $file)
                                            <a href="{{ asset('/' . $file) }}" target="_blank" class="list-group-item list-group-item-action">
                                                <i class="fas fa-file me-2"></i>{{ basename($file) }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                @if($report->actual_card_files && count($report->actual_card_files) > 0)
                                    <h6 class="text-primary mb-2">Card Files</h6>
                                    <div class="list-group mb-3">
                                        @foreach($report->actual_card_files as $file)
                                            <a href="{{ asset('/' . $file) }}" target="_blank" class="list-group-item list-group-item-action">
                                                <i class="fas fa-file me-2"></i>{{ basename($file) }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                @if($report->bank_deposit_files && count($report->bank_deposit_files) > 0)
                                    <h6 class="text-warning mb-2">Bank Deposit Files</h6>
                                    <div class="list-group mb-3">
                                        @foreach($report->bank_deposit_files as $file)
                                            <a href="{{ asset('/' . $file) }}" target="_blank" class="list-group-item list-group-item-action">
                                                <i class="fas fa-file me-2"></i>{{ basename($file) }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                @if((!$report->radiant_collection_files || count($report->radiant_collection_files) == 0) &&
                                    (!$report->actual_card_files || count($report->actual_card_files) == 0) &&
                                    (!$report->bank_deposit_files || count($report->bank_deposit_files) == 0))
                                    <p class="text-muted text-center mb-0">No files attached</p>
                                @endif
                            </div>
                        </div>

                        <!-- Audit Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-light text-white">
                                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Audit Trail</h5>
                            </div>
                            <div class="card-body">
                                <small class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Created By:</span>
                                    <strong>{{ $report->creator->name ?? 'N/A' }}</strong>
                                </small>
                                <small class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Created At:</span>
                                    <strong>{{ $report->created_at->format('d M Y H:i') }}</strong>
                                </small>
                                <small class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Last Updated:</span>
                                    <strong>{{ $report->updated_at->format('d M Y H:i') }}</strong>
                                </small>
                                <small class="d-flex justify-content-between">
                                    <span class="text-muted">Edit Count:</span>
                                    <span class="badge bg-secondary">{{ $report->edit_count }}</span>
                                </small>
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

<script>
// Define route variables for JavaScript
const storeRoute = "{{ route('branch-financial.store') }}";
const updateRoute = "{{ route('branch-financial.update', ':id') }}";
const showRoute = "{{ route('branch-financial.show', ':id') }}";
const destroyRoute = "{{ route('branch-financial.destroy', ':id') }}";
</script>

@include('superadmin.superadminfooter')
</body>
  <!-- [Body] end -->
</html>
