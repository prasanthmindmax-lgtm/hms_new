<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Access Master</title>
    @include('superadmin.superadminhead')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
/* ── Banner ── */
.am-banner {
    background: linear-gradient(135deg, #4f52c9 0%, #6a6ee4 60%, #8b5cf6 100%);
    border-radius: 14px;
    padding: 22px 28px 18px;
    color: #fff;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.am-banner::before {
    content: '';
    position: absolute;
    right: -40px; top: -40px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.am-banner::after {
    content: '';
    position: absolute;
    right: 60px; bottom: -60px;
    width: 150px; height: 150px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.am-banner h2 { font-size: 1.4rem; font-weight: 700; margin: 0; }
.am-banner .am-sub { font-size: .82rem; opacity: .82; margin-top: 2px; }

/* ── Stat Cards ── */
.am-stat { border-radius: 12px; padding: 14px 18px; background: #fff;
    box-shadow: 0 2px 10px rgba(106,110,228,.12); border: 1px solid #ece9ff;
    transition: box-shadow .15s, border-color .15s; }
.am-stat[data-status]:hover { box-shadow: 0 4px 18px rgba(106,110,228,.22); border-color: #6a6ee4; }
.am-stat.am-stat-active-filter { border-color: #6a6ee4; box-shadow: 0 0 0 3px rgba(106,110,228,.25); background: #f4f3ff; }
.am-stat .am-stat-val { font-size: 1.6rem; font-weight: 700; }
.am-stat .am-stat-lbl { font-size: .72rem; color: #7b80a0; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }
.am-stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }

/* ── Filter Card ── */
.am-filter-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e8e6f0;
    box-shadow: 0 2px 8px rgba(106,110,228,.07);
    padding: 14px 18px 12px;
    margin-bottom: 16px;
    overflow: visible;
}
.am-filter-card .fc-title { font-size: .72rem; font-weight: 700; color: #6a6ee4; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 10px; }

/* Custom dropdown */
.am-dd { position: relative; width: 100%; }
.am-dd-input {
    width: 100%; padding: 7px 32px 7px 10px; border: 1px solid #dde1f5;
    border-radius: 7px; font-size: .82rem; cursor: pointer; background: #f8f8ff;
    color: #333; outline: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.am-dd-input:focus { border-color: #6a6ee4; box-shadow: 0 0 0 2px rgba(106,110,228,.18); }
.am-dd-caret {
    position: absolute; right: 9px; top: 50%; transform: translateY(-50%);
    color: #6a6ee4; pointer-events: none; font-size: .8rem;
}
.am-dd-panel {
    display: none; position: absolute; top: calc(100% + 4px); left: 0; width: 100%;
    min-width: 180px; background: #fff; border: 1px solid #dde1f5;
    border-radius: 8px; box-shadow: 0 6px 20px rgba(80,80,200,.13);
    z-index: 9999; padding: 6px;
}
.am-dd.open .am-dd-panel { display: block; }
.am-dd-search {
    width: 100%; padding: 5px 8px; border: 1px solid #dde1f5; border-radius: 5px;
    font-size: .8rem; margin-bottom: 5px; outline: none;
}
.am-dd-search:focus { border-color: #6a6ee4; }
.am-dd-opts { max-height: 220px; overflow-y: auto; }
.am-dd-opt {
    padding: 6px 10px; border-radius: 5px; cursor: pointer;
    font-size: .81rem; color: #333; transition: background .12s;
}
.am-dd-opt:hover { background: #6a6ee4; color: #fff; }
.am-dd-opt.selected { background: #f0f0ff; color: #4f52c9; font-weight: 600; }

/* Search input */
.am-search-wrap { position: relative; }
.am-search-wrap input {
    width: 100%; padding: 7px 10px 7px 32px; border: 1px solid #dde1f5;
    border-radius: 7px; font-size: .82rem; background: #f8f8ff; outline: none;
}
.am-search-wrap input:focus { border-color: #6a6ee4; box-shadow: 0 0 0 2px rgba(106,110,228,.18); }
.am-search-wrap .am-search-icon { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: #6a6ee4; font-size: .85rem; }

/* Active filter chips */
.am-chip {
    display: inline-flex; align-items: center; gap: 5px;
    background: #eeecff; color: #4f52c9; border-radius: 20px;
    padding: 3px 10px 3px 8px; font-size: .75rem; font-weight: 600;
    margin: 2px 3px; cursor: default;
}
.am-chip .am-chip-x { cursor: pointer; font-size: .9rem; line-height: 1; }
.am-chip .am-chip-x:hover { color: #c00; }

/* Table card */
.am-tbl-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e8e6f0;
    box-shadow: 0 2px 8px rgba(106,110,228,.07);
    overflow: hidden;
}
.am-tbl-head { background: #f4f3ff; border-bottom: 1px solid #e8e6f0; padding: 10px 16px; }
.am-tbl-inner { overflow-x: auto; overflow-y: auto; max-height: 500px; }
.am-tbl-inner::-webkit-scrollbar { width: 5px; height: 5px; }
.am-tbl-inner::-webkit-scrollbar-thumb { background: #6a6ee4; border-radius: 4px; }

table.am-tbl { width: 100%; border-collapse: collapse; font-size: .82rem; }
table.am-tbl thead th {
    position: sticky; top: 0; z-index: 5;
    background: #f4f3ff; padding: 10px 12px; font-weight: 700;
    color: #4f52c9; border-bottom: 2px solid #dde1f5; white-space: nowrap;
}
table.am-tbl tbody tr { border-bottom: 1px solid #f0eeff; transition: background .1s; }
table.am-tbl tbody tr:hover { background: #f8f7ff; }
table.am-tbl tbody td { padding: 9px 12px; vertical-align: middle; color: #333; }

/* Status badges */
.badge-active   { background: #d1fae5; color: #065f46; }
.badge-inactive { background: #fee2e2; color: #991b1b; }
.badge-notused  { background: #f3f4f6; color: #6b7280; }
.am-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; cursor: pointer; }

/* Pagination */
.am-pager { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
.am-pager button {
    min-width: 28px; height: 28px; padding: 0 6px; border-radius: 6px;
    border: 1px solid #dde1f5; background: #f8f8ff; font-size: .8rem;
    cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
    transition: all .12s; color: #4f52c9; font-weight: 600;
}
.am-pager button:hover { background: #ece9ff; border-color: #6a6ee4; }
.am-pager button.active { background: #6a6ee4; color: #fff; border-color: #6a6ee4; }
.am-pager button:disabled { opacity: .45; cursor: default; }
.am-pager .dots { padding: 0 2px; color: #999; font-size: .8rem; }

/* Per page select */
.am-pp { display: flex; align-items: center; gap: 6px; font-size: .8rem; color: #6b7280; }
.am-pp select { border: 1px solid #dde1f5; border-radius: 6px; padding: 3px 6px; font-size: .8rem; outline: none; }

/* Export buttons */
.btn-exp-csv  { background: #059669; color: #fff; border: none; border-radius: 7px; padding: 6px 14px; font-size: .78rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; }
.btn-exp-xlsx { background: #1d6f42; color: #fff; border: none; border-radius: 7px; padding: 6px 14px; font-size: .78rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; }
.btn-exp-csv:hover  { background: #047857; }
.btn-exp-xlsx:hover { background: #155534; }

/* Menu filter label */
.menu-filter-badge { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; padding: 2px 8px; border-radius: 12px; font-size: .72rem; font-weight: 600; }

/* Loading */
.am-loading { text-align: center; padding: 40px; color: #6a6ee4; }
.am-loading-dots span { display: inline-block; width: 10px; height: 10px; background: #6a6ee4; border-radius: 50%; margin: 0 3px; animation: am-bounce .8s infinite alternate; }
.am-loading-dots span:nth-child(2) { animation-delay: .2s; }
.am-loading-dots span:nth-child(3) { animation-delay: .4s; }
@keyframes am-bounce { from { transform: translateY(0); } to { transform: translateY(-10px); } }

/* Modal tweaks */
.modal-header-am { background: linear-gradient(135deg,#4f52c9,#6a6ee4); }

#locationDropdown { display: none; z-index: 1060 !important; position: absolute; }
#locationDropdown.show { display: block; }
.location-item:hover { background: #f0f0ff; cursor: pointer; }
</style>
</head>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

{{-- Export URL & menus data for JS --}}
<script>
    var amExportUrl    = "{{ route('superadmin.masteraccess.export') }}";
    var amEmployeeUrl  = "{{ route('superadmin.employee-data') }}";
    var updateStatusUrl = "{{ route('update.status') }}";
    var amMenus = @json($menus);
</script>

<div class="pc-container">
<div class="pc-content">

{{-- ── Banner ── --}}
<div class="am-banner d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h2><i class="bi bi-shield-lock me-2"></i>Access Master</h2>
        <div class="am-sub">Manage employee permissions, menu access and export reports</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn-exp-csv" id="btnExportCsv"><i class="bi bi-filetype-csv"></i> Export CSV</button>
        <button class="btn-exp-xlsx" id="btnExportXlsx"><i class="bi bi-file-earmark-excel"></i> Export XLSX</button>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="am-stat d-flex align-items-center gap-3" title="Show all employees">
            <div class="am-stat-icon" style="background:#ece9ff;color:#6a6ee4;"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="am-stat-val" id="total_access_count">0</div>
                <div class="am-stat-lbl">Total Users</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="am-stat d-flex align-items-center gap-3" data-status="active" style="cursor:pointer;" title="Click to filter Active">
            <div class="am-stat-icon" style="background:#d1fae5;color:#059669;"><i class="bi bi-person-check-fill"></i></div>
            <div>
                <div class="am-stat-val" id="total_active_count">0</div>
                <div class="am-stat-lbl">Active</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="am-stat d-flex align-items-center gap-3" data-status="inactive" style="cursor:pointer;" title="Click to filter Inactive">
            <div class="am-stat-icon" style="background:#fee2e2;color:#dc2626;"><i class="bi bi-person-x-fill"></i></div>
            <div>
                <div class="am-stat-val" id="total_inactive_count">0</div>
                <div class="am-stat-lbl">Inactive</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="am-stat d-flex align-items-center gap-3" data-status="not_used" style="cursor:pointer;" title="Click to filter Not Used">
            <div class="am-stat-icon" style="background:#fef3c7;color:#d97706;"><i class="bi bi-person-dash-fill"></i></div>
            <div>
                <div class="am-stat-val" id="total_notused_count">0</div>
                <div class="am-stat-lbl">Not Used</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Filter Card ── --}}
<div class="am-filter-card">
    <div class="fc-title"><i class="bi bi-funnel me-1"></i>Filters</div>
    <div class="row g-2 align-items-end">
        {{-- Designation --}}
        <div class="col-6 col-md-2">
            <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;color:#555;">Designation</label>
            <div class="am-dd" id="ddRole">
                <input type="text" class="am-dd-input" id="role-views" placeholder="All Designations" readonly>
                <span class="am-dd-caret"><i class="bi bi-chevron-down"></i></span>
                <div class="am-dd-panel">
                    <input type="text" class="am-dd-search" id="ddRoleSearch" placeholder="Search...">
                    <div class="am-dd-opts" id="roles-options"></div>
                </div>
            </div>
        </div>
        {{-- Zone --}}
        <div class="col-6 col-md-2">
            <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;color:#555;">Zone</label>
            <div class="am-dd" id="ddZone">
                <input type="text" class="am-dd-input" id="zone-views" placeholder="All Zones" readonly>
                <span class="am-dd-caret"><i class="bi bi-chevron-down"></i></span>
                <div class="am-dd-panel">
                    <input type="text" class="am-dd-search" id="ddZoneSearch" placeholder="Search...">
                    <div class="am-dd-opts" id="zones-options"></div>
                </div>
            </div>
        </div>
        {{-- Branch --}}
        <div class="col-6 col-md-2">
            <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;color:#555;">Branch</label>
            <div class="am-dd" id="ddBranch">
                <input type="text" class="am-dd-input" id="branch-views" placeholder="All Branches" readonly>
                <span class="am-dd-caret"><i class="bi bi-chevron-down"></i></span>
                <div class="am-dd-panel">
                    <input type="text" class="am-dd-search" id="ddBranchSearch" placeholder="Search...">
                    <div class="am-dd-opts" id="branches-options"></div>
                </div>
            </div>
        </div>
        {{-- Employee --}}
        <div class="col-6 col-md-2">
            <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;color:#555;">Employee</label>
            <div class="am-dd" id="ddName">
                <input type="text" class="am-dd-input" id="name-views" placeholder="All Employees" readonly>
                <span class="am-dd-caret"><i class="bi bi-chevron-down"></i></span>
                <div class="am-dd-panel">
                    <input type="text" class="am-dd-search" id="ddNameSearch" placeholder="Search...">
                    <div class="am-dd-opts" id="names-options"></div>
                </div>
            </div>
        </div>
        {{-- Menu Filter --}}
        <div class="col-6 col-md-2">
            <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;color:#555;">
                Menu Access <span class="menu-filter-badge">by menu</span>
            </label>
            <div class="am-dd" id="ddMenu">
                <input type="text" class="am-dd-input" id="menu-views" placeholder="All Menus" readonly>
                <span class="am-dd-caret"><i class="bi bi-chevron-down"></i></span>
                <div class="am-dd-panel">
                    <input type="text" class="am-dd-search" id="ddMenuSearch" placeholder="Search menu...">
                    <div class="am-dd-opts" id="menus-options"></div>
                </div>
            </div>
        </div>
        {{-- Search --}}
        <div class="col-6 col-md-2">
            <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;color:#555;">Quick Search</label>
            <div class="am-search-wrap">
                <i class="bi bi-search am-search-icon"></i>
                <input type="text" id="employee_num" placeholder="Search anything...">
            </div>
        </div>
    </div>
    {{-- Active chips --}}
    <div class="mt-2 d-flex align-items-center flex-wrap gap-1" id="amChipsArea" style="min-height:26px;">
        <span id="amClearAll" class="badge bg-danger ms-1" style="cursor:pointer;display:none;font-size:.72rem;">
            <i class="bi bi-x-circle me-1"></i>Clear all
        </span>
    </div>
</div>

{{-- ── Table Card ── --}}
<div class="am-tbl-card">
    <div class="am-tbl-head d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="fw-bold text-purple-700" style="color:#4f52c9;font-size:.88rem;">
            <i class="bi bi-table me-1"></i>
            Employees — <span id="access-count">0</span> records
        </div>
        <div class="am-pp">
            <span>Show</span>
            <select id="access-items-per-page">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>per page</span>
        </div>
    </div>
    <div class="am-tbl-inner">
        <table class="am-tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee ID</th>
                    <th>Full Name</th>
                    <th>Designation</th>
                    <th>Branch</th>
                    <th>Zone</th>
                    <th>Status</th>
                    <th>Modified By / Date</th>
                    <th>Menus</th>
                    <th>Permission</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody id="employee_details1">
                <tr><td colspan="11">
                    <div class="am-loading">
                        <div class="am-loading-dots"><span></span><span></span><span></span></div>
                        <div class="mt-2 text-muted" style="font-size:.8rem;">Loading employees...</div>
                    </div>
                </td></tr>
            </tbody>
            <tbody id="employee_details" style="display:none;"></tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-3 py-2 border-top" style="background:#fafaff;">
        <div class="text-muted" style="font-size:.78rem;" id="amPageInfo">–</div>
        <div class="am-pager" id="access-pagination"></div>
    </div>
</div>

</div>{{-- /pc-content --}}
</div>{{-- /pc-container --}}


{{-- ════════════════════════════════════════════
     VIEW MODAL
════════════════════════════════════════════ --}}
<div class="modal fade" id="access-view-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-am py-2">
                <h5 class="modal-title text-white fw-bold"><i class="bi bi-person-badge me-2"></i>Employee Details #<span id="view-access-id"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header py-2 fw-bold" style="background:#f4f3ff;color:#4f52c9;font-size:.82rem;">Basic Information</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Employee ID:</strong> <span id="view-employee-id"></span></p>
                                        <p class="mb-2"><strong>Name:</strong> <span id="view-employee-name"></span></p>
                                        <p class="mb-2"><strong>Email:</strong> <span id="view-employee-email"></span></p>
                                        <p class="mb-2"><strong>Created:</strong> <span id="view-employee-created"></span></p>
                                        <p class="mb-2"><strong>Modified:</strong> <span id="view-employee-modified-time"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Designation:</strong> <span id="view-role"></span></p>
                                        <p class="mb-2"><strong>Branch:</strong> <span id="view-branch"></span></p>
                                        <p class="mb-2"><strong>Zone:</strong> <span id="view-zone"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header py-2 fw-bold" style="background:#f4f3ff;color:#4f52c9;font-size:.82rem;">Reporting Structure</div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Reporting Manager:</strong> <span id="view-reporting-manager"></span></p>
                                <p class="mb-2"><strong>Zonal Head:</strong> <span id="view-zonal-head"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header py-2 fw-bold" style="background:#f4f3ff;color:#4f52c9;font-size:.82rem;">Menu Permissions</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr><th>Menu</th><th>Submenu</th><th>Status</th></tr>
                                </thead>
                                <tbody id="view-permissions-table"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════
     PERMISSION MODAL
════════════════════════════════════════════ --}}
<div class="modal fade" id="permission-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-am py-2">
                <h6 class="modal-title text-white fw-bold"><i class="bi bi-key me-2"></i>Manage Permissions #<span id="permission-employee-id"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-3">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="form-label small mb-1">Employee ID</label>
                            <input type="text" class="form-control form-control-sm" id="perm-employee-id" readonly>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label small mb-1">Employee Name</label>
                            <input type="text" class="form-control form-control-sm" id="perm-employee-name" readonly>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label small mb-1">Email</label>
                            <input type="email" class="form-control form-control-sm" id="perm-email">
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label small mb-1">Password</label>
                            <input type="password" class="form-control form-control-sm" id="perm-password" placeholder="Leave blank to keep current password">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="form-label small mb-1">Branch</label>
                            <input type="hidden" id="perm-branch-id" value="">
                            <input type="text" class="form-control form-control-sm" id="perm-branch" readonly>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label small mb-1">Zone</label>
                            <input type="hidden" id="perm-zone-id" value="">
                            <input type="text" class="form-control form-control-sm" id="perm-zone" readonly>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label small mb-1">Reporting Manager</label>
                            <select class="form-control form-control-sm" id="perm-reporting-manager" name="reporting_manager">
                                <option value="">Select Reporting Manager</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label small mb-1">Zonal Head</label>
                            <select class="form-control form-control-sm" id="perm-zonal-head">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="form-group col-md-6 mt-2">
                        <label class="form-label small mb-1">Role</label>
                        <select class="form-control form-control-sm" id="perm-role">
                            <option value="1">Superadmin</option>
                            <option value="2">Zonal Admin</option>
                            <option value="3">Admin</option>
                            <option value="4">Auditor</option>
                            <option value="5">User</option>
                            <option value="6">IT Technician</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6 mb-2">
                        <label class="form-label small mb-1">Multi-Location</label>
                        <div class="dropdown w-100 position-relative">
                            <input type="text" class="form-control form-control-sm" id="multi-location"
                                placeholder="Select locations" aria-haspopup="true" aria-expanded="false">
                            <div class="dropdown-menu w-100 p-2" id="locationDropdown"
                                style="max-height:300px;overflow-y:auto;position:absolute;top:100%;left:0;right:0;z-index:1060;">
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" id="locationSearch" placeholder="Search locations...">
                                </div>
                                <div id="locationList">
                                    @foreach ($locations as $location)
                                    <div class="location-item d-flex align-items-center mb-1 p-2 rounded"
                                        data-id="{{ $location->id }}" data-name="{{ $location->name }}"
                                        style="cursor:pointer;" onclick="toggleLocation(this)">
                                        <div class="location-name flex-grow-1">{{ $location->name }}</div>
                                        <div class="tick ms-2 text-success" style="display:none;">✔</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="location_ids" id="location_ids">
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr class="small">
                                <th>Menu</th>
                                <th>Submenu</th>
                                <th>Enable/Disable
                                    <label class="ms-2 mb-0">
                                        <input type="checkbox" id="selectAllMenus"> Select All
                                    </label>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="permission-table-body"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary" id="save-permissions">Save Changes</button>
            </div>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════
     STATUS MODAL
════════════════════════════════════════════ --}}
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-am py-2">
                <h5 class="modal-title text-white fw-bold"><i class="bi bi-toggle-on me-2"></i>Change User Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusChangeForm">
                    <div class="mb-3">
                        <label class="form-label">Status:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="active_status" id="statusActive" value="0">
                            <label class="form-check-label" for="statusActive">Active</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="active_status" id="statusInactive" value="1">
                            <label class="form-check-label" for="statusInactive">Inactive</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="statusDate" class="form-label">Effective Date:</label>
                        <input type="date" class="form-control" id="statusDate" name="status_date" required>
                    </div>
                </form>
                <div class="text-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmStatusChange">Change</button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('superadmin.superadminfooter')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('/assets/js/masteraccess.js') }}"></script>
</body>
</html>
