<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank statement upload batches</title>
    @include('superadmin.superadminhead')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/bank-reconciliation.css') }}">
    <style>
        .batch-toolbar .form-control, .batch-toolbar .form-select { min-width: 140px; }
    </style>
</head>
<body style="overflow-x: hidden;">
    <div class="page-loader"><div class="bar"></div></div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card header-card">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h2 class="text-white mb-1">
                                            <i class="bi bi-collection me-2"></i>Statement upload batches
                                        </h2>
                                        <p class="text-white-50 mb-0">Serial, file, uploader, and quick preview — all via AJAX (no full page reload).</p>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                        <a href="{{ route('bank-reconciliation.index') }}" class="btn btn-light">
                                            <i class="bi bi-arrow-left me-1"></i>Back to reconciliation
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(empty($bankAccountsEnabled))
                <div class="alert alert-warning mt-3">
                    Run migrations (<code>php artisan migrate</code>) to enable <code>bank_reconciliation_accounts</code> and batch history.
                </div>
                @else
                @include('bank-reconciliation.partials.batch_upload_panel_inner')
                @endif
            </div>
        </div>
    </div>

    @include('bank-reconciliation.partials.batch_preview_modal')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @include('superadmin.superadminfooter')
    <script>
        window.bankBatchPage = {
            bankAccountsEnabled: @json(!empty($bankAccountsEnabled)),
            uploadBatches: "{{ route('bank-reconciliation.upload-batches') }}",
            batchFileBase: "{{ url('/bank-reconciliation/batch-file') }}",
            indexUrl: "{{ route('bank-reconciliation.index') }}"
        };
        window.BANK_RECON_BATCH_PREVIEW_BASE = "{{ url('/bank-reconciliation/batch-preview') }}";
    </script>
    <script src="{{ asset('/assets/js/bank-reconciliation/batch-preview-modal.js') }}"></script>
    <script src="{{ asset('/assets/js/bank-reconciliation/batch-upload.js') }}"></script>
</body>
</html>
