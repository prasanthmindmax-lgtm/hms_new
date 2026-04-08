{{-- Shared: filters + batch table (IDs used by bank-reconciliation.js and batch-upload.js) --}}
<div class="card shadow-sm mt-0 batch-toolbar-card">
    <div class="card-body batch-toolbar">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0">Account #</label>
                <input type="text" class="form-control form-control-sm" id="fltAccount" placeholder="Search">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0">File name</label>
                <input type="text" class="form-control form-control-sm" id="fltFile" placeholder="Contains…">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0">Uploaded by</label>
                <input type="text" class="form-control form-control-sm" id="fltUser" placeholder="Name / user">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0">From</label>
                <input type="date" class="form-control form-control-sm" id="fltDateFrom">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0">To</label>
                <input type="date" class="form-control form-control-sm" id="fltDateTo">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0">Per page</label>
                <select class="form-select form-select-sm" id="fltPerPage">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-12 col-md-6 d-flex flex-wrap gap-2 pt-2 pt-md-0">
                <button type="button" class="btn btn-primary btn-sm" id="btnApplyBatchFilters">
                    <i class="bi bi-search me-1"></i>Apply filters
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnClearBatchFilters">
                    Clear
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Batch master</h5>
        <span class="text-muted small" id="batchTotalHint"></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive bank-recon-batch-table-wrap">
            <table class="table table-hover table-striped mb-0" id="batchMasterTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Upload date</th>
                        <th>Account</th>
                        <th>File</th>
                        <th>Rows</th>
                        <th>Dup.</th>
                        <th>Skipped</th>
                        <th>Uploaded by</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="batchTableBody">
                    <tr><td colspan="9" class="text-center py-4 text-muted">Open this view to load batches…</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 border-top">
            <div class="small text-muted" id="batchPageInfo"></div>
            <nav><ul class="pagination pagination-sm mb-0" id="batchPagination"></ul></nav>
        </div>
    </div>
</div>
