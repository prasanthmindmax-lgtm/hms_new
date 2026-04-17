@php
$__assetDashboardFragment = $assetDashboardFragment ?? null;
$columnDefinitions = $columnDefinitions ?? [];
@endphp

@if ($__assetDashboardFragment === 'thead')
<tr>
    @foreach ($columnDefinitions as $col)
    <th class="{{ $col['th_class'] ?? '' }}">{{ $col['label'] ?? '' }}</th>
    @endforeach
</tr>
@elseif ($__assetDashboardFragment === 'rows')
@php
$rowBase = ($assets->currentPage() - 1) * $assets->perPage();
$colspanEmpty = max(count($columnDefinitions), 1);
@endphp
@forelse ($assets as $idx => $a)
@php $assetRowNum = $rowBase + $loop->iteration; @endphp
<tr class="qdt-row asset-grid-row" data-id="{{ $a->id }}">
    @foreach ($columnDefinitions as $col)
    @php $ck = $col['key'] ?? ''; @endphp
    @if ($ck === 'action')
    <td class="text-center asset-grid-td-action" style="white-space:nowrap;">
        <a href="{{ route('superadmin.assets.edit', $a->id) }}" class="tk-btn-edit-ticket" style="text-decoration:none;">
            <i class="bi bi-pencil-square" aria-hidden="true"></i>Edit
        </a>
    </td>
    @else
    <td
        @if ($ck === 'remarks')
        class="tk-desc" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $a->remarks }}"
        @elseif ($ck === 'sno')
        class="text-center tk-tk-muted"
        @elseif ($ck === 'category')
        class="tk-tk-route"
        @else
        class="asset-grid-td-default"
        @endif
    >
        @switch($ck)
        @case('sno')
        {{ $assetRowNum }}
        @break
        @case('category')
        {{ $a->category->name ?? '—' }}
        @break
        @case('company')
        {{ $a->primaryCompany->company_name ?? '—' }}
        @break
        @case('zone')
        {{ $a->primaryZone->name ?? '—' }}
        @break
        @case('branch')
        {{ $a->primaryBranch->name ?? '—' }}
        @break
        @case('department')
        {{ $a->department?->name ?? '—' }}
        @break
        @case('system_model')
        {{ $a->systemModelDisplay() ?: '—' }}
        @break
        @case('monitor_model')
        {{ $a->typeAttr('monitor_model') ?: '—' }}
        @break
        @case('serial')
        {{ $a->serial_number ?? '—' }}
        @break
        @case('os_installed')
        {{ $a->typeAttr('os_installed') ?: '—' }}
        @break
        @case('processor')
        {{ $a->typeAttr('processor') ?: '—' }}
        @break
        @case('ssd_hdd')
        {{ $a->typeAttr('ssd_hdd') ?: '—' }}
        @break
        @case('ram')
        {{ $a->typeAttr('ram') ?: '—' }}
        @break
        @case('responsible')
        @case('responsible_person')
        {{ $a->responsible_person ?? '—' }}
        @break
        @case('model')
        {{ $a->model ?? '—' }}
        @break
        @case('brand')
        {{ $a->typeAttr('brand') ?: '—' }}
        @break
        @case('ip_address')
        {{ $a->typeAttr('ip_address') ?: '—' }}
        @break
        @case('camera_name')
        {{ $a->typeAttr('camera_name') ?: '—' }}
        @break
        @case('dvr_name')
        {{ $a->typeAttr('dvr_name') ?: '—' }}
        @break
        @case('dvr_channel')
        {{ $a->typeAttr('dvr_channel') ?: '—' }}
        @break
        @case('device_username')
        {{ $a->typeAttr('device_username') ?: '—' }}
        @break
        @case('warranty')
        {{ $a->warranty_expiry ? $a->warranty_expiry->format('d/m/Y') : '—' }}
        @break
        @case('remarks')
        {{ $a->remarks ?? '—' }}
        @break
        @default
        —
        @endswitch
    </td>
    @endif
    @endforeach
</tr>
@empty
<tr class="qdt-row">
    <td colspan="{{ $colspanEmpty }}" class="text-center py-5 tk-tk-empty">
        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.5;"></i>No assets found
    </td>
</tr>
@endforelse
@elseif ($__assetDashboardFragment === 'pagination')
@if ($assets->hasPages())
<div style="display:flex; justify-content:space-between; align-items:center; width: 100%; flex-wrap:wrap; gap: 10px;">
    <div style="margin-right:20px;">
        <span style="font-size:13px;color:#6c757d;">Showing {{ $assets->firstItem() ?? 0 }}–{{ $assets->lastItem() ?? 0 }} of {{ $assets->total() }}</span>
    </div>
    <div>
        {{ $assets->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
    </div>
</div>
@endif
@else
<!doctype html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('superadmin.superadminhead')

    <link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
    {{--  <link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />  --}}
    <link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <style>
        .asset-dashboard-page {
            color: #1e293b;
        }

        .asset-hero-sub {
            font-size: 0.95rem;
            line-height: 1.45;
            max-width: 36rem;
        }

        /* Filter row: clearer labels than default ticket micro-label */
        .asset-dashboard-page .qd-filter-group > label {
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            color: #475569 !important;
            letter-spacing: 0.04em;
        }

        .asset-dashboard-page .qd-filter-group .form-control,
        .asset-dashboard-page .qd-date-wrap {
            font-size: 0.875rem;
        }

        .asset-dashboard-page .dropdown-menu.tax-dropdown.asset-filter-dd {
            max-height: 320px;
            overflow-y: auto;
        }

        /* Table: use same look as tickets — tickets.css .tk-table-card .qdt-* (do not override thead/body here) */
        .asset-dashboard-page.tk-tickets-page .tk-table-card .qdt-table {
            font-family: "Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .asset-dashboard-page #assetPaginationWrap .tk-pagination-inner {
            gap: 10px 16px;
        }

        .asset-dashboard-page #assetPaginationWrap .asset-per-page-meta {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .asset-dashboard-page #assetPaginationWrap .asset-per-page-meta .tk-per-page-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #475569;
            margin: 0;
            white-space: nowrap;
        }

        .asset-dashboard-page #assetPaginationWrap .asset-per-page-select {
            width: 64px;
            min-width: 64px;
            max-width: 64px;
            padding: 6px 24px 6px 10px;
            font-size: 0.8125rem;
            font-weight: 600;
            line-height: 1.2;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 16 16'%3E%3Cpath fill='%23475569' d='M8 11L3 6h10z'/%3E%3C/svg%3E") no-repeat right 7px center;
            background-size: 10px 10px;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            color: #1e293b;
        }

        .asset-dashboard-page #assetPaginationWrap .asset-pagination-links {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            justify-content: flex-end;
        }

        .asset-module-tabs {
            display: flex;
            gap: 4px;
            border-bottom: 1px solid #e2e8f0;
            margin: 0 0 16px;
            padding: 0 2px;
        }

        .asset-module-tab {
            padding: 10px 18px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #64748b;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }

        .asset-module-tab:hover {
            color: #2563eb;
        }

        .asset-module-tab.active {
            color: #1e3a8a;
            border-bottom-color: #2563eb;
        }

        /* Category quick-tabs — between filters and table (no "All categories" quick-tab; use filter dropdown for that) */
        .asset-cat-tabs-title {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        .asset-category-tabs-wrap {
            border-bottom: none;
            padding-bottom: 0;
        }

        .asset-category-tabs-above-table {
            margin-top: 10px;
            margin-bottom: 14px;
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .asset-category-tabs-wrap .asset-cat-tab {
            appearance: none;
            -webkit-appearance: none;
            font: inherit;
            line-height: 1.35;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 500;
            padding: 7px 14px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155;
            white-space: nowrap;
        }

        .asset-category-tabs-wrap .asset-cat-tab:hover {
            border-color: #94a3b8;
            color: #0f172a;
            background: #fff;
        }

        .asset-category-tabs-wrap .asset-cat-tab.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }
    </style>
</head>

<body style="overflow-x: hidden;">
    <div class="page-loader">
        <div class="bar"></div>
    </div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
            <div class="qd-card tk-tickets-page asset-dashboard-page">
                <div class="tk-hero">
                    <div class="tk-hero-inner">
                        <h1 class="tk-hero-title"><i class="bi bi-cpu" aria-hidden="true"></i> Assets Management</h1>
                        <p class="asset-hero-sub text-muted mb-0 mt-2">Search, filter, and maintain hardware in one place.</p>
                    </div>
                    <div class="tk-hero-actions">
                        <button type="button" class="tk-btn-export" id="btnExportAssets" title="Export filtered list to Excel">
                            <i class="bi bi-download" aria-hidden="true"></i>Export
                        </button>
                        <button type="button" class="tk-btn-export" id="btnOpenAssetImport" title="Import assets from Excel" data-bs-toggle="modal" data-bs-target="#assetImportModal">
                            <i class="bi bi-upload" aria-hidden="true"></i>Import
                        </button>
                        <a href="{{ route('superadmin.assets.create') }}" class="tk-btn-raise" style="text-decoration:none;">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i>Add asset
                        </a>
                    </div>
                </div>

                <nav class="asset-module-tabs px-2 px-md-3" aria-label="Assets section">
                    <a href="{{ route('superadmin.assets.dashboard') }}" class="asset-module-tab active">Assets</a>
                    <a href="{{ route('superadmin.asset.categories.index') }}" class="asset-module-tab">Categories</a>
                </nav>

                <div class="tk-dash-body">
                    @php
                        $selCatIds = array_values(array_filter(array_map('intval', array_map('trim', explode(',', (string) request('category_id'))))));
                        $systemCategoryIds = $systemCategoryIds ?? [];
                        $systemCategoryIdsCsv = $systemCategoryIdsCsv ?? '';
                        $systemRowSelected = false;
                        if ($systemCategoryIdsCsv !== '' && $selCatIds !== []) {
                            $sysNorm = $systemCategoryIds;
                            sort($sysNorm);
                            $selNorm = $selCatIds;
                            sort($selNorm);
                            $systemRowSelected = $selNorm === $sysNorm;
                        }
                        $selStatuses = array_values(array_filter(array_map('trim', explode(',', (string) request('status_name')))));
                        $selDeptIds = array_values(array_filter(array_map('intval', explode(',', (string) request('department_id')))));
                        $assetDf = request('date_from');
                        $assetDt = request('date_to');
                        $assetDateLabel = 'All Dates';
                        if ($assetDf && $assetDt) {
                            try {
                                $assetDateLabel = \Illuminate\Support\Carbon::parse($assetDf)->format('d/m/Y')
                                    . ' - '
                                    . \Illuminate\Support\Carbon::parse($assetDt)->format('d/m/Y');
                            } catch (\Throwable $e) {
                                $assetDateLabel = 'All Dates';
                            }
                        }
                    @endphp

                    <div class="tk-stats-row" role="toolbar" id="statsSection">
                        <button type="button" class="tk-stat-card tk-stat-total tk-stat-active" data-stat-filter="" title="All assets" style="cursor:pointer;" aria-pressed="true">
                            <span class="tk-stat-ic" aria-hidden="true"><i class="bi bi-hdd-stack"></i></span>
                            <span class="tk-stat-lbl">Total</span>
                            <span class="tk-stat-num" data-stat-key="total">{{ $stats['total'] ?? 0 }}</span>
                            <span class="tk-stat-hint">All assets</span>
                        </button>
                        @php
                        $statMeta = [
                        'available' => ['label' => 'Available', 'class' => 'tk-stat-closed', 'icon' => 'bi-check2-circle', 'hint' => 'Ready to use'],
                        'assigned' => ['label' => 'Assigned', 'class' => 'tk-stat-in_progress', 'icon' => 'bi-person-check', 'hint' => 'In use'],
                        'maintenance' => ['label' => 'Maintenance', 'class' => 'tk-stat-open', 'icon' => 'bi-tools', 'hint' => 'Being fixed'],
                        'retired' => ['label' => 'Retired', 'class' => 'tk-stat-cancelled', 'icon' => 'bi-archive', 'hint' => 'No longer in use'],
                        ];
                        @endphp
                        @foreach ($statuses as $st)
                        @php $m = $statMeta[$st] ?? ['label' => ucfirst($st), 'class' => 'tk-stat-open', 'icon' => 'bi-dot', 'hint' => '']; @endphp
                        <button type="button" class="tk-stat-card {{ $m['class'] }}" data-stat-filter="{{ $st }}" title="Filter: {{ $m['label'] }}" style="cursor:pointer;" aria-pressed="false">
                            <span class="tk-stat-ic" aria-hidden="true"><i class="bi {{ $m['icon'] }}"></i></span>
                            <span class="tk-stat-lbl">{{ $m['label'] }}</span>
                            <span class="tk-stat-num" data-stat-key="{{ $st }}">{{ $stats['by_status'][$st] ?? 0 }}</span>
                            <span class="tk-stat-hint">{{ $m['hint'] }}</span>
                        </button>
                        @endforeach
                    </div>

                    <form id="assetFilterForm" onsubmit="return false;">
                        <input type="hidden" name="stat_filter" id="stat_filter_input" value="{{ request('stat_filter', '') }}">

                        <div class="tk-filter-shell tk-filter-qd" id="filtersSection">
                            <div class="tk-filter-head">
                                <div class="tk-filter-title"><i class="bi bi-sliders2" aria-hidden="true"></i> Filter assets</div>
                                <span class="tk-showing-pill" id="assetShowingPill">Rows <strong id="asset-stat-showing-range">—</strong> of <strong id="asset-stat-filtered-total">—</strong></span>
                            </div>

                            <div class="qd-filters tk-ticket-qd-filters asset-qd-filters">
                                <div class="qd-filter-row">
                                    <div class="qd-filter-group">
                                        <label><i class="bi bi-calendar3 me-1"></i>Date range</label>
                                        <div class="qd-date-wrap" id="assetReportRange" role="button" tabindex="0">
                                            <i class="fa fa-calendar"></i>
                                            <span id="asset_date_label">{{ $assetDateLabel }}</span>
                                            <i class="fa fa-caret-down" style="margin-left:auto;"></i>
                                            <input type="hidden" name="date_from" id="asset_date_from" value="{{ request('date_from') }}">
                                            <input type="hidden" name="date_to" id="asset_date_to" value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                    <div class="qd-filter-group tax-dropdown-wrapper asset-filter-tax asset-cat-wrap tk-ticket-dept-wrap">
                                        <label>Category</label>
                                        <input type="text" class="form-control asset-cat-search-input dropdown-search-input tk-dept-search-input" placeholder="All categories" readonly autocomplete="off">
                                        <input type="hidden" name="category_id" id="category_id_input" value="{{ request('category_id') }}">
                                        <div class="dropdown-menu tax-dropdown tk-ticket-filter-dd asset-filter-dd">
                                            <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search category…" autocomplete="off"></div>
                                            <div class="dropdown-list asset-cat-list asset-cat-list-single tk-ticket-dept-list">
                                                <div data-value="All categories" data-id="" class="{{ count($selCatIds) === 0 ? 'selected' : '' }}">All categories</div>
                                                @if ($systemCategoryIdsCsv !== '')
                                                <div data-value="System" data-id="{{ $systemCategoryIdsCsv }}" class="{{ $systemRowSelected ? 'selected' : '' }}">System</div>
                                                @endif
                                                @foreach ($categories as $c)
                                                @if (in_array((int) $c->id, $systemCategoryIds, true))
                                                @continue
                                                @endif
                                                <div data-value="{{ $c->name }}" data-id="{{ $c->id }}" @if (count($selCatIds) >= 1 && count($selCatIds) === 1 && (int) $selCatIds[0] === (int) $c->id) class="selected"@endif>{{ $c->name }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="qd-filter-group tax-dropdown-wrapper asset-filter-tax asset-status-wrap tk-ticket-status-wrap">
                                        <label>Status</label>
                                        <input type="text" class="form-control asset-status-search-input dropdown-search-input tk-status-search-input" placeholder="Select status" readonly autocomplete="off">
                                        <input type="hidden" name="status_name" id="status_name_input" value="{{ request('status_name') }}">
                                        <div class="dropdown-menu tax-dropdown tk-ticket-filter-dd asset-filter-dd">
                                            <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search status…" autocomplete="off"></div>
                                            <div class="tk-ticket-dd-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                            </div>
                                            <div class="dropdown-list multiselect asset-status-list tk-ticket-status-list">
                                                @foreach ($statuses as $st)
                                                <div data-value="{{ $st }}" data-id="{{ $st }}" @if (in_array($st, $selStatuses, true)) class="selected" @endif>{{ ucfirst(str_replace('_', ' ', $st)) }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="qd-filter-group tax-dropdown-wrapper asset-filter-tax asset-dept-wrap tk-ticket-branch-wrap">
                                        <label>Department</label>
                                        <input type="text" class="form-control asset-dept-search-input dropdown-search-input tk-branch-search-input" placeholder="Select department" readonly autocomplete="off">
                                        <input type="hidden" name="department_id" id="department_hidden" value="{{ request('department_id') }}">
                                        <div class="dropdown-menu tax-dropdown tk-ticket-filter-dd asset-filter-dd">
                                            <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search department…" autocomplete="off"></div>
                                            <div class="tk-ticket-dd-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                            </div>
                                            <div class="dropdown-list multiselect asset-dept-list branch-list">
                                                @foreach ($departments as $d)
                                                <div data-value="{{ $d->name }}" data-id="{{ $d->id }}" @if (in_array((int) $d->id, $selDeptIds, true)) class="selected"@endif>{{ $d->name }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="qd-search-row tk-ticket-search-row">
                                <div class="tk-ticket-search-left">
                                    <div class="qd-search-wrap">
                                        <i class="bi bi-search"></i>
                                        <input type="search" name="universal_search" id="universal_search"
                                            value="{{ request('universal_search') }}" placeholder="Search assets…" autocomplete="off">
                                    </div>
                                </div>
                                <div class="tk-filter-actions">
                                    <button type="button" class="tk-refresh-btn" id="btnApplyFilters"><i class="bi bi-search" aria-hidden="true"></i>Search</button>
                                    <button type="button" class="tk-refresh-btn tk-btn-clear-filters" id="btnClearFilters"><i class="bi bi-x-lg" aria-hidden="true"></i>Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="asset-category-tabs-wrap asset-category-tabs-above-table" aria-label="Quick category">
                        <div class="asset-cat-tabs-intro mb-2">
                            <div class="asset-cat-tabs-title">Quick category</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center" id="assetCategoryTabs" role="tablist">
                            @if ($systemCategoryIdsCsv !== '')
                            <button type="button"
                                class="asset-cat-tab {{ $systemRowSelected ? 'active' : '' }}"
                                data-category-id="{{ $systemCategoryIdsCsv }}"
                                aria-selected="{{ $systemRowSelected ? 'true' : 'false' }}">System</button>
                            @endif
                            @foreach ($categories as $c)
                            @if (in_array((int) $c->id, $systemCategoryIds, true))
                            @continue
                            @endif
                            @php $tabOne = count($selCatIds) === 1 && (int) $selCatIds[0] === (int) $c->id; @endphp
                            <button type="button" class="asset-cat-tab {{ $tabOne ? 'active' : '' }}" data-category-id="{{ $c->id }}"
                                aria-selected="{{ $tabOne ? 'true' : 'false' }}">{{ $c->name }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div class="tk-table-card">
                        <div class="qdt-wrap" style="overflow-x:auto;">
                            <table class="qdt-table" id="assetsTable">
                                <thead class="qdt-head">
                                    @include('superadmin.assets.asset_dashboard', [
                                        'assets' => $assets,
                                        'assetDashboardFragment' => 'thead',
                                        'columnDefinitions' => $columnDefinitions,
                                    ])
                                </thead>
                                <tbody id="assetTableBody">
                                    @include('superadmin.assets.asset_dashboard', [
                                        'assets' => $assets,
                                        'assetDashboardFragment' => 'rows',
                                        'columnDefinitions' => $columnDefinitions,
                                    ])
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="assetPaginationWrap" class="tk-pagination-wrap">
                        <div class="tk-pagination-inner" style="display:flex;">
                            <div class="tk-pagination-meta asset-per-page-meta">
                                <label class="tk-per-page-label" for="per_page_select">Per page</label>
                                <select class="tk-per-page-select asset-per-page-select" name="per_page" id="per_page_select" title="Rows per page">
                                    @foreach ([10, 25, 50, 100] as $sz)
                                    <option value="{{ $sz }}" {{ (int) request('per_page', 10) === $sz ? 'selected' : '' }}>{{ $sz }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="asset-pagination-links">
                                @include('superadmin.assets.asset_dashboard', ['assets' => $assets, 'assetDashboardFragment' => 'pagination'])
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assetImportModal" tabindex="-1" aria-labelledby="assetImportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assetImportModalLabel">Import assets</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">Select a category, then download a template whose columns match the <strong>assets</strong> table and JSON type fields for that category. Use <strong>company_name</strong>, <strong>zone_name</strong>, and <strong>branch_name</strong> exactly as in the master data. Each data row still needs a category: set <strong>category_id</strong> / <strong>category</strong> in the sheet, or pick a default below for rows that omit both.</p>
                    <div class="mb-3">
                        <label class="form-label small mb-1" for="assetImportDefaultCategory">Category (required for template download)</label>
                        <select id="assetImportDefaultCategory" class="form-select form-select-sm" title="Template download and default category for upload">
                            <option value="">— Select category —</option>
                            @foreach ($categories as $ic)
                            <option value="{{ $ic->id }}">{{ $ic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="mb-1">Step 1: Download the Excel template</p>
                    <a href="#" id="assetImportTemplateLink" class="btn btn-success btn-sm mb-3 disabled" aria-disabled="true" style="pointer-events:none;opacity:.6;">
                        <i class="bi bi-download me-1"></i>Download template
                    </a>
                    <p class="mb-1">Step 2: Upload the filled file</p>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <label class="btn btn-outline-primary btn-sm position-relative mb-0">
                            <i class="bi bi-file-earmark-arrow-up"></i> Choose file
                            <input type="file" name="file" class="d-none" id="assetImportFileInput" accept=".xlsx,.xls,.csv">
                        </label>
                        <span id="assetImportFileName" class="text-muted small"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" id="assetImportSubmitBtn">Upload &amp; import</button>
                </div>
            </div>
        </div>
    </div>

    @php
    $assetInitialPagination = [
    'total' => $assets->total(),
    'from' => $assets->firstItem(),
    'to' => $assets->lastItem(),
    ];
    @endphp

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        (function() {
            const dataUrl = @json(route('superadmin.assets.data'));
            const exportUrl = @json(route('superadmin.assets.export'));
            const assetImportUrl = @json(route('superadmin.assets.import'));
            const assetImportTemplateBase = @json(route('superadmin.assets.import.template'));
            const ASSET_CAT_ALL = 'All categories';
            const ASSET_STATUS_ALL = 'All statuses';
            const ASSET_DEPT_ALL = 'All departments';

            @if(session('success'))
            toastr.success(@json(session('success')));
            @endif
            @if(session('warning'))
            toastr.warning(@json(session('warning')));
            @endif

            function filterQuery(page) {
                const f = $('#assetFilterForm').serialize();
                return f + (page ? '&page=' + encodeURIComponent(page) : '');
            }

            function updateStatCards(stats) {
                if (!stats) return;
                $('[data-stat-key="total"]').text(stats.total ?? 0);
                @foreach($statuses as $st)
                $('[data-stat-key="{{ $st }}"]').text((stats.by_status && stats.by_status['{{ $st }}']) ? stats.by_status['{{ $st }}'] : 0);
                @endforeach
            }

            function setStatActive(filter) {
                $('.tk-stat-card').removeClass('tk-stat-active');
                $('.tk-stat-card[data-stat-filter="' + (filter === undefined ? '' : filter) + '"]').addClass('tk-stat-active');
            }

            function updateAssetShowingPill(p) {
                if (!p || p.total == null) {
                    $('#asset-stat-showing-range').text('—');
                    $('#asset-stat-filtered-total').text('—');
                    return;
                }
                const t = parseInt(p.total, 10) || 0;
                if (t === 0) {
                    $('#asset-stat-showing-range').text('0–0');
                    $('#asset-stat-filtered-total').text('0');
                    return;
                }
                const a = p.from != null ? p.from : '—';
                const b = p.to != null ? p.to : '—';
                $('#asset-stat-showing-range').text(a + '–' + b);
                $('#asset-stat-filtered-total').text(String(t));
            }

            function loadGrid(page) {
                assetCloseFilterDropdowns();
                $.ajax({
                    url: dataUrl + '?' + filterQuery(page || 1),
                    type: 'GET',
                    success: function(res) {
                        if (res.success) {
                            if (res.thead_html) {
                                $('#assetsTable thead').html(res.thead_html);
                            }
                            $('#assetTableBody').html(res.html);
                            $('#assetPaginationWrap > div > div:last-child').html(res.pagination_html || '');
                            updateStatCards(res.stats);
                            updateAssetShowingPill(res.pagination);
                            syncAssetCategoryTabs();
                        }
                    },
                    error: function() {
                        toastr.error('Could not load assets.');
                    }
                });
            }

            /** Highlights System / named category quick-tabs; none active when filter is "all categories". */
            function syncAssetCategoryTabs() {
                var raw = String($('#category_id_input').val() || '').trim();
                var $page = $('.asset-dashboard-page');
                $page.find('.asset-cat-tab').removeClass('active').attr('aria-selected', 'false');
                if (raw === '') {
                    return;
                }
                $page.find('.asset-cat-tab').each(function() {
                    if (String($(this).attr('data-category-id') || '') === raw) {
                        $(this).addClass('active').attr('aria-selected', 'true');
                    }
                });
            }

            $(document).on('click', '.asset-dashboard-page .asset-cat-tab', function() {
                var id = $(this).attr('data-category-id');
                var $page = $('.asset-dashboard-page');
                var idStr = String(id === undefined || id === null ? '' : id);
                $('#category_id_input').val(idStr);
                $page.find('.asset-cat-wrap .asset-cat-list > div').removeClass('selected');
                var $pick = $page.find('.asset-cat-wrap .asset-cat-list > div').filter(function() {
                    return String($(this).attr('data-id') || '') === idStr;
                });
                if ($pick.length) {
                    $pick.first().addClass('selected');
                    $page.find('.asset-cat-search-input').val($pick.first().text().trim());
                } else {
                    $page.find('.asset-cat-search-input').val(ASSET_CAT_ALL);
                }
                syncAssetCategoryTabs();
                loadGrid(1);
            });

            /** Sync filter dropdowns from hidden fields (category = single select). */
            function assetInitFilterUiFromHiddens() {
                var $root = $('.asset-dashboard-page');

                function applyList($wrap, listSel, hiddenVal) {
                    var raw = String(hiddenVal || '').trim();
                    var parts = raw ? raw.split(',').map(function(s) {
                        return s.trim();
                    }).filter(Boolean) : [];
                    $wrap.find(listSel + ' > div').removeClass('selected');
                    var labels = [];
                    parts.forEach(function(p) {
                        $wrap.find(listSel + ' > div').each(function() {
                            var id = String($(this).attr('data-id') || '');
                            if (id === String(p)) {
                                $(this).addClass('selected');
                                labels.push($(this).text().trim());
                            }
                        });
                    });
                    return labels;
                }
                var rawCat = String($('#category_id_input').val() || '').trim();
                var catParts = rawCat ? rawCat.split(',').map(function(s) {
                    return s.trim();
                }).filter(Boolean) : [];
                $root.find('.asset-cat-wrap .asset-cat-list > div').removeClass('selected');
                if (catParts.length === 0) {
                    $root.find('.asset-cat-wrap .asset-cat-list > div[data-id=""]').addClass('selected');
                    $root.find('.asset-cat-search-input').val(ASSET_CAT_ALL);
                } else {
                    var rawCatFull = String($('#category_id_input').val() || '').trim();
                    var $pick = $root.find('.asset-cat-wrap .asset-cat-list > div').filter(function() {
                        return String($(this).attr('data-id') || '') === rawCatFull;
                    });
                    if (!$pick.length && catParts.length === 1) {
                        $pick = $root.find('.asset-cat-wrap .asset-cat-list > div').filter(function() {
                            return String($(this).attr('data-id') || '') === String(catParts[0]);
                        });
                    }
                    $pick.first().addClass('selected');
                    $root.find('.asset-cat-search-input').val($pick.length ? $pick.first().text().trim() : ASSET_CAT_ALL);
                }
                var stLabels = applyList($root.find('.asset-status-wrap'), '.asset-status-list', $('#status_name_input').val());
                $root.find('.asset-status-search-input').val(stLabels.length ? stLabels.join(', ') : ASSET_STATUS_ALL);
                var depLabels = applyList($root.find('.asset-dept-wrap'), '.asset-dept-list', $('#department_hidden').val());
                $root.find('.asset-dept-search-input').val(depLabels.length ? depLabels.join(', ') : ASSET_DEPT_ALL);
                syncAssetCategoryTabs();
            }

            function assetFilterUpdateMultiSelection($dropdown) {
                var $wrapper = $dropdown.data('wrapper');
                if (!$wrapper || !$wrapper.length) return;
                var selectedItems = [];
                var selectedIds = [];
                $dropdown.find('.asset-status-list > div.selected, .asset-dept-list > div.selected').each(function() {
                    selectedItems.push($(this).text().trim());
                    var rid = $(this).attr('data-id');
                    if (rid !== undefined && rid !== null && String(rid) !== '') {
                        selectedIds.push(String(rid));
                    }
                });
                var $visible = $wrapper.find('.dropdown-search-input');
                var idsCsv = selectedIds.join(',');
                if ($wrapper.hasClass('asset-status-wrap')) {
                    $visible.val(selectedItems.length ? selectedItems.join(', ') : ASSET_STATUS_ALL);
                    $('#status_name_input').val(idsCsv);
                } else if ($wrapper.hasClass('asset-dept-wrap')) {
                    $visible.val(selectedItems.length ? selectedItems.join(', ') : ASSET_DEPT_ALL);
                    $('#department_hidden').val(idsCsv);
                }
            }

            function assetFilterSyncCloneSelection($dropdown) {
                var $w = $dropdown.data('wrapper');
                if (!$w || !$w.length) return;
                if ($w.hasClass('asset-cat-wrap')) {
                    var raw = String($('#category_id_input').val() || '').trim();
                    $dropdown.find('.asset-cat-list > div').removeClass('selected');
                    if (raw === '') {
                        $dropdown.find('.asset-cat-list > div[data-id=""]').addClass('selected');
                    } else {
                        var $m = $dropdown.find('.asset-cat-list > div').filter(function() {
                            return String($(this).attr('data-id') || '') === raw;
                        });
                        if ($m.length) {
                            $m.first().addClass('selected');
                        } else {
                            var one = raw.split(',')[0].trim();
                            $dropdown.find('.asset-cat-list > div').filter(function() {
                                return String($(this).attr('data-id') || '') === one;
                            }).first().addClass('selected');
                        }
                    }
                } else if ($w.hasClass('asset-status-wrap')) {
                    var rawSt = String($('#status_name_input').val() || '').trim();
                    var sts = rawSt ? rawSt.split(',').map(function(s) {
                        return s.trim();
                    }).filter(Boolean) : [];
                    $dropdown.find('.asset-status-list > div').removeClass('selected');
                    sts.forEach(function(st) {
                        $dropdown.find('.asset-status-list > div').each(function() {
                            if (String($(this).attr('data-id')) === String(st)) {
                                $(this).addClass('selected');
                            }
                        });
                    });
                } else if ($w.hasClass('asset-dept-wrap')) {
                    var rawD = String($('#department_hidden').val() || '').trim();
                    var deps = rawD ? rawD.split(',').map(function(s) {
                        return s.trim();
                    }).filter(Boolean) : [];
                    $dropdown.find('.asset-dept-list > div').removeClass('selected');
                    deps.forEach(function(name) {
                        $dropdown.find('.asset-dept-list > div').each(function() {
                            if (String($(this).attr('data-id')) === String(name)) {
                                $(this).addClass('selected');
                            }
                        });
                    });
                }
            }

            function assetCloseFilterDropdowns() {
                $('.dropdown-menu.tax-dropdown.asset-filter-dd').removeClass('show').hide();
            }

            function assetFilterDdCleanup() {
                $('body > .dropdown-menu.tax-dropdown.asset-filter-dd').remove();
                $('.asset-dashboard-page .asset-filter-tax .dropdown-search-input').removeData('assetFilterDropdown');
            }
            assetFilterDdCleanup();

            $(document).on('click.assetDash', '.asset-dashboard-page .asset-filter-tax .dropdown-search-input', function(e) {
                e.stopPropagation();
                $('.dropdown-menu.tax-dropdown.asset-filter-dd').hide();
                var $input = $(this);
                var $dropdown = $input.data('assetFilterDropdown');
                if (!$dropdown || !$dropdown.length) {
                    $dropdown = $input.siblings('.dropdown-menu').clone(true);
                    $('body').append($dropdown);
                    $input.data('assetFilterDropdown', $dropdown);
                }
                $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
                var offset = $input.offset();
                var triggerW = $input.outerWidth();
                var panelW = Math.max(triggerW, 260);
                $dropdown.css({
                    position: 'absolute',
                    top: offset.top + $input.outerHeight(),
                    left: offset.left,
                    width: panelW,
                    minWidth: panelW,
                    zIndex: 10050
                }).addClass('show').show();
                $dropdown.find('.inner-search').val('');
                $dropdown.find('.dropdown-list div').show();
                assetFilterSyncCloneSelection($dropdown);
                $dropdown.find('.inner-search').trigger('focus');
            });

            $(document).on('click.assetDash', '.dropdown-menu.asset-filter-dd .asset-cat-list > div', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $row = $(this);
                var $dd = $row.closest('.dropdown-menu');
                var $wrap = $dd.data('wrapper');
                if (!$wrap || !$wrap.hasClass('asset-cat-wrap')) return;
                $dd.find('.asset-cat-list > div').removeClass('selected');
                $row.addClass('selected');
                var id = $row.attr('data-id');
                var label = $row.text().trim();
                $('#category_id_input').val(id === undefined || id === null || String(id) === '' ? '' : String(id));
                $wrap.find('.asset-cat-search-input').val(label || ASSET_CAT_ALL);
                syncAssetCategoryTabs();
                assetCloseFilterDropdowns();
                loadGrid(1);
            });
            $(document).on('click.assetDash', '.dropdown-menu.asset-filter-dd .asset-status-list > div', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $row = $(this);
                var $dd = $row.closest('.dropdown-menu');
                $row.toggleClass('selected');
                assetFilterUpdateMultiSelection($dd);
            });
            $(document).on('click.assetDash', '.dropdown-menu.asset-filter-dd .asset-dept-list > div', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $row = $(this);
                var $dd = $row.closest('.dropdown-menu');
                $row.toggleClass('selected');
                assetFilterUpdateMultiSelection($dd);
            });

            $(document).on('click.assetDash', '.dropdown-menu.asset-filter-dd .select-all', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $dd = $(this).closest('.dropdown-menu');
                var $w = $dd.data('wrapper');
                if (!$w || !$w.length) return;
                if ($w.hasClass('asset-status-wrap')) {
                    $dd.find('.asset-status-list > div').addClass('selected');
                } else if ($w.hasClass('asset-dept-wrap')) {
                    $dd.find('.asset-dept-list > div').addClass('selected');
                }
                assetFilterUpdateMultiSelection($dd);
            });
            $(document).on('click.assetDash', '.dropdown-menu.asset-filter-dd .deselect-all', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $dd = $(this).closest('.dropdown-menu');
                var $w = $dd.data('wrapper');
                if (!$w || !$w.length) return;
                if ($w.hasClass('asset-status-wrap')) {
                    $dd.find('.asset-status-list > div').removeClass('selected');
                } else if ($w.hasClass('asset-dept-wrap')) {
                    $dd.find('.asset-dept-list > div').removeClass('selected');
                }
                assetFilterUpdateMultiSelection($dd);
            });

            $(document).on('keyup.assetDash', '.dropdown-menu.asset-filter-dd .inner-search', function() {
                var q = $(this).val().toLowerCase();
                var $dd = $(this).closest('.dropdown-menu');
                $dd.find('.dropdown-list div').each(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
                });
            });

            $(document).on('click.assetDash', function(e) {
                if (!$(e.target).closest('.asset-dashboard-page .tax-dropdown-wrapper.asset-filter-tax').length &&
                    !$(e.target).closest('.dropdown-menu.tax-dropdown.asset-filter-dd').length) {
                    assetCloseFilterDropdowns();
                }
            });

            function assetCloseFilterOnScroll() {
                if ($('body > .dropdown-menu.tax-dropdown.asset-filter-dd:visible').length) {
                    assetCloseFilterDropdowns();
                }
            }
            $(window).on('scroll.assetDash resize.assetDash', assetCloseFilterOnScroll);
            $('.pc-content, .pc-container').on('scroll.assetDash', assetCloseFilterOnScroll);

            $('#btnApplyFilters').on('click', function() {
                loadGrid(1);
            });
            $('#per_page_select').on('change', function() {
                loadGrid(1);
            });

            function assetResetDateRangePicker() {
                $('#asset_date_label').text('All Dates');
                $('#asset_date_from, #asset_date_to').val('');
                var $rr = $('#assetReportRange');
                var drp = $rr.data('daterangepicker');
                if (drp) {
                    drp.setStartDate(moment().subtract(29, 'days'));
                    drp.setEndDate(moment());
                }
            }

            function assetInitDateRangePicker() {
                var $rr = $('#assetReportRange');
                if (!$rr.length || typeof $.fn.daterangepicker !== 'function' || typeof moment === 'undefined') {
                    return;
                }
                if ($rr.data('daterangepicker')) {
                    $rr.data('daterangepicker').remove();
                }
                var df = String($('#asset_date_from').val() || '').trim();
                var dt = String($('#asset_date_to').val() || '').trim();
                var start;
                var end;
                if (df && dt && moment(df, 'YYYY-MM-DD', true).isValid() && moment(dt, 'YYYY-MM-DD', true).isValid()) {
                    start = moment(df, 'YYYY-MM-DD');
                    end = moment(dt, 'YYYY-MM-DD');
                } else {
                    start = moment().subtract(29, 'days');
                    end = moment();
                }

                function applyRange(startM, endM, label) {
                    if (label === 'All Dates') {
                        $('#asset_date_label').text('All Dates');
                        $('#asset_date_from, #asset_date_to').val('');
                        return;
                    }
                    $('#asset_date_from').val(startM.format('YYYY-MM-DD'));
                    $('#asset_date_to').val(endM.format('YYYY-MM-DD'));
                    $('#asset_date_label').text(startM.format('DD/MM/YYYY') + ' - ' + endM.format('DD/MM/YYYY'));
                }

                $rr.daterangepicker({
                    startDate: start,
                    endDate: end,
                    opens: 'left',
                    drops: 'down',
                    parentEl: 'body',
                    autoUpdateInput: false,
                    alwaysShowCalendars: true,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'DD/MM/YYYY',
                    },
                    ranges: {
                        'All Dates': [moment().subtract(50, 'years'), moment().add(50, 'years')],
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [
                            moment().subtract(1, 'month').startOf('month'),
                            moment().subtract(1, 'month').endOf('month'),
                        ],
                    },
                }, applyRange);

                $rr.off('cancel.daterangepicker.asset').on('cancel.daterangepicker.asset', function() {
                    $('#asset_date_label').text('All Dates');
                    $('#asset_date_from, #asset_date_to').val('');
                    var drp2 = $rr.data('daterangepicker');
                    if (drp2) {
                        drp2.setStartDate(moment().subtract(29, 'days'));
                        drp2.setEndDate(moment());
                    }
                });

                if (!df || !dt) {
                    $('#asset_date_label').text('All Dates');
                    $('#asset_date_from, #asset_date_to').val('');
                } else {
                    var drp3 = $rr.data('daterangepicker');
                    if (drp3) {
                        drp3.setStartDate(moment(df, 'YYYY-MM-DD'));
                        drp3.setEndDate(moment(dt, 'YYYY-MM-DD'));
                    }
                }
            }

            $('#btnClearFilters').on('click', function() {
                assetFilterDdCleanup();
                $('#category_id_input').val('');
                $('#status_name_input').val('');
                $('#department_hidden').val('');
                $('#universal_search').val('');
                assetResetDateRangePicker();
                $('#stat_filter_input').val('');
                $('.asset-dashboard-page .asset-cat-list > div, .asset-dashboard-page .asset-status-list > div, .asset-dashboard-page .asset-dept-list > div')
                    .removeClass('selected');
                assetInitFilterUiFromHiddens();
                setStatActive('');
                loadGrid(1);
            });

            $(document).on('click', '.tk-stat-card[data-stat-filter]', function() {
                const v = $(this).data('stat-filter');
                $('#stat_filter_input').val(v === undefined || v === null ? '' : String(v));
                setStatActive(String(v));
                loadGrid(1);
            });

            $(document).on('click', '#assetPaginationWrap a.page-link', function(e) {
                const href = $(this).attr('href');
                if (!href) return;
                e.preventDefault();
                try {
                    const u = new URL(href, window.location.origin);
                    const p = u.searchParams.get('page') || 1;
                    loadGrid(p);
                } catch (err) {
                    loadGrid(1);
                }
            });

            $('#btnExportAssets').on('click', function() {
                const q = $('#assetFilterForm').serialize();
                window.location.href = exportUrl + (q ? ('?' + q) : '');
            });

            function syncAssetImportTemplateHref() {
                var v = String($('#assetImportDefaultCategory').val() || '').trim();
                var $a = $('#assetImportTemplateLink');
                if (!v) {
                    $a.attr('href', '#').addClass('disabled').attr('aria-disabled', 'true').css({ pointerEvents: 'none', opacity: '0.6' });
                    return;
                }
                $a.attr('href', assetImportTemplateBase + '?category_id=' + encodeURIComponent(v))
                    .removeClass('disabled').attr('aria-disabled', 'false').css({ pointerEvents: '', opacity: '' });
            }
            $('#assetImportDefaultCategory').on('change', syncAssetImportTemplateHref);
            syncAssetImportTemplateHref();

            $('#assetImportTemplateLink').on('click', function(e) {
                var v = String($('#assetImportDefaultCategory').val() || '').trim();
                if (!v) {
                    e.preventDefault();
                    toastr.warning('Select a category first to download the import template.');
                }
            });

            $('#assetImportFileInput').on('change', function() {
                var f = this.files && this.files[0];
                $('#assetImportFileName').text(f ? f.name : '');
            });

            $('#assetImportSubmitBtn').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var orig = $btn.html();
                var input = document.getElementById('assetImportFileInput');
                if (!input || !input.files || !input.files[0]) {
                    toastr.error('Please choose an Excel or CSV file.');
                    return;
                }
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Importing…');
                var fd = new FormData();
                fd.append('file', input.files[0]);
                var defCat = String($('#assetImportDefaultCategory').val() || '').trim();
                if (defCat !== '') {
                    fd.append('import_category_id', defCat);
                }
                $.ajax({
                    url: assetImportUrl,
                    type: 'POST',
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        var msg = (data && data.message) ? data.message : 'Import completed.';
                        if (data && data.success === false) {
                            toastr.warning(msg);
                        } else {
                            toastr.success(msg);
                        }
                        var el = document.getElementById('assetImportModal');
                        if (window.bootstrap && el) {
                            var m = window.bootstrap.Modal.getInstance(el) || new window.bootstrap.Modal(el);
                            m.hide();
                        } else {
                            $('#assetImportModal').modal('hide');
                        }
                        $('#assetImportFileInput').val('');
                        $('#assetImportFileName').text('');
                        setTimeout(function() { window.location.reload(); }, 600);
                        $btn.prop('disabled', false).html(orig);
                    },
                    error: function(xhr) {
                        var msg = 'Import failed.';
                        if (xhr.status === 422 && xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors && xhr.responseJSON.errors.file) {
                                msg = xhr.responseJSON.errors.file[0];
                            }
                            toastr.warning(msg);
                            $btn.prop('disabled', false).html(orig);
                            return;
                        }
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.file) {
                            msg = xhr.responseJSON.errors.file[0];
                        }
                        toastr.error(msg);
                        $btn.prop('disabled', false).html(orig);
                    }
                });
            });

            $('#universal_search').on('keypress', function(e) {
                if (e.which === 13) {
                    loadGrid(1);
                }
            });

            assetInitFilterUiFromHiddens();
            assetInitDateRangePicker();
            setStatActive(String($('#stat_filter_input').val() || ''));
            updateAssetShowingPill(@json($assetInitialPagination));
        })();
    </script>

    @include('superadmin.superadminfooter')
</body>

</html>
@endif
