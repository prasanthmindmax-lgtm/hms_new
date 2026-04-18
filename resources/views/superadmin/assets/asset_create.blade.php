@php
    $isEdit = $asset->exists;
    $o = function ($key, $default = null) {
        $v = old($key);
        if ($v !== null && $v !== '') {
            return $v;
        }
        return $default;
    };
    $cid = $o('company_id', $asset->company_id);
    $zid = $o('zone_id', $asset->zone_id);
    $bid = $o('branch_id', $asset->branch_id);
    $dispCompany = $cid ? $companies->firstWhere('id', (int) $cid)?->company_name ?? '' : '';
    $dispZone = $zid ? $zones->firstWhere('id', (int) $zid)?->name ?? '' : '';
    $dispBranch = $bid ? $branches->firstWhere('id', (int) $bid)?->name ?? '' : '';
    $catIdForDisp = $o('category_id', $asset->category_id ?? null);
    $dispCategory = $catIdForDisp ? $categories->firstWhere('id', (int) $catIdForDisp)->name ?? '' : '';
    $deptIdVal = $o('department_id', $asset->department_id);
    $dispDepartment = $deptIdVal ? $departments->firstWhere('id', (int) $deptIdVal)->name ?? '' : '';
    $consumable = $consumable ?? null;
    $employeeDataUrl = $employeeDataUrl ?? route('superadmin.employee-data');
    $assigneeToken = (string) (old('asset_assignee') ?? '');
    if ($assigneeToken === '') {
        if (!empty($asset->assigned_user_id)) {
            $assigneeToken = 'u:' . (int) $asset->assigned_user_id;
        } elseif (!empty($asset->assigned_hrm_employment_id)) {
            $assigneeToken = 'h:' . rawurlencode((string) $asset->assigned_hrm_employment_id);
        }
    }
    $assigneeLabelVal = (string) (old('asset_assignee_label', $asset->responsible_person ?? '') ?? '');
    $assigneeDisplayVal = $assigneeLabelVal !== '' ? $assigneeLabelVal : '';

    $typeAttr = old('type_attributes', is_array($asset->type_attributes ?? null) ? $asset->type_attributes : []);
    $uiTemplate = old('type_attributes.ui_template');
    if ($uiTemplate === null || $uiTemplate === '') {
        $uiTemplate = is_array($typeAttr) && !empty($typeAttr['ui_template']) ? $typeAttr['ui_template'] : null;
    }
    if ($uiTemplate === 'general' || $uiTemplate === 'cpu' || $uiTemplate === 'monitor') {
        $uiTemplate = 'system';
    }
    $catIdResolved = $o('category_id', $asset->category_id ?? null);
    if ($uiTemplate === null || $uiTemplate === '') {
        if ($catIdResolved) {
            $selCat = $categories->firstWhere('id', (int) $catIdResolved);
            $cn = strtolower((string) ($selCat->name ?? ''));
            if (str_contains($cn, 'printer')) {
                $uiTemplate = 'printer';
            } elseif (str_contains($cn, 'cctv') || str_contains($cn, 'camera')) {
                $uiTemplate = 'cctv';
            } elseif (str_contains($cn, 'nvr')) {
                $uiTemplate = 'nvr';
            } elseif (str_contains($cn, 'dvr')) {
                $uiTemplate = 'dvr';
            } elseif (str_contains($cn, 'router')) {
                $uiTemplate = 'router';
            } elseif (str_contains($cn, 'switch')) {
                $uiTemplate = 'switch';
            } elseif (str_contains($cn, 'monitor')
                || str_contains($cn, 'cpu')
                || str_contains($cn, 'desktop')
                || str_contains($cn, 'laptop')) {
                $uiTemplate = 'system';
            } else {
                $uiTemplate = 'system';
            }
        } else {
            $uiTemplate = '';
        }
    }
    $ta = function (string $key, $default = '') use ($typeAttr) {
        $v = old('type_attributes.' . $key);
        if ($v !== null && $v !== '') {
            return $v;
        }

        return data_get($typeAttr, $key, $default);
    };
    $recorderUiKind = $uiTemplate === 'dvr' ? 'dvr' : 'nvr';
@endphp
<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

<body style="overflow-x: hidden;">
    <div class="page-loader">
        <div class="bar"></div>
    </div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <nav class="asset-module-tabs px-3 asset-form-module-tabs" aria-label="Assets section">
        <a href="{{ route('superadmin.assets.dashboard') }}" class="asset-module-tab active">Assets</a>
        <a href="{{ route('superadmin.asset.categories.index') }}" class="asset-module-tab">Categories</a>
    </nav>

    <div class="pc-container">
        <div class="pc-content">
            <div class="card asset-form-page-card border-0 shadow-sm">
                <div class="card-header asset-form-card-header bg-white border-bottom">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                        <div>
                            <h5 class="mb-1">
                                @if ($isEdit)
                                    <i class="bi bi-pencil-square me-2 text-primary"></i>Edit asset
                                @else
                                    <i class="bi bi-cpu me-2 text-primary"></i>New asset
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4 pb-5 asset-form-card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 border-0 shadow-sm">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post"
                        action="{{ $isEdit ? route('superadmin.assets.update') : route('superadmin.assets.store') }}"
                        id="assetForm">
                        @csrf
                        @if ($isEdit)
                            <input type="hidden" name="id" value="{{ $asset->id }}">
                        @endif

                        <div class="asset-form-premium">
                            <div class="asset-form-section asset-form-section-location mb-4">
                                <div class="asset-form-section-title">
                                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                    <span>Company, zone &amp; branch</span>
                                </div>
                                <div class="row mb-0" id="assetLocationStrip">
                                    <div class="col-lg-4 col-md-6 mb-3 asset-loc-col">
                                        <label class="form-label" for="asset_dd_company">Company <span
                                                class="text-danger">*</span></label>
                                        <div class="tax-dropdown-wrapper company-section asset-dd-wrap">
                                            <input id="asset_dd_company" type="text"
                                                class="form-control company-search-input asset-dd-input asset-loc-dd-input"
                                                readonly autocomplete="off" placeholder="Select company"
                                                value="{{ $dispCompany }}">
                                            <input type="hidden" name="company_id" class="company_id"
                                                value="{{ $cid ?: '' }}">
                                            <div class="dropdown-menu tax-dropdown asset-dd-panel">
                                                <div class="inner-search-container">
                                                    <input type="text"
                                                        class="inner-search form-control form-control-sm"
                                                        placeholder="Search company…" autocomplete="off">
                                                </div>
                                                <div class="company-list">
                                                    @foreach ($companies as $co)
                                                        <div data-value="{{ $co->company_name }}"
                                                            data-id="{{ $co->id }}">
                                                            {{ $co->company_name }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-danger small mt-1 d-none asset-field-error-msg"
                                            id="asset_err_company" role="alert">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 mb-3 asset-loc-col">
                                        <label class="form-label" for="asset_dd_zone">Zone <span
                                                class="text-danger">*</span></label>
                                        <div class="tax-dropdown-wrapper account-section asset-dd-wrap">
                                            <input id="asset_dd_zone" type="text"
                                                class="form-control zone-search-input asset-dd-input asset-loc-dd-input"
                                                readonly autocomplete="off" placeholder="Select zone"
                                                value="{{ $dispZone }}">
                                            <input type="hidden" name="zone_id" class="zone_id"
                                                value="{{ $zid ?: '' }}">
                                            <div class="dropdown-menu tax-dropdown asset-dd-panel">
                                                <div class="inner-search-container">
                                                    <input type="text"
                                                        class="inner-search form-control form-control-sm"
                                                        placeholder="Search zone…" autocomplete="off">
                                                </div>
                                                <div class="zone-list">
                                                    @foreach ($zones as $z)
                                                        <div data-id="{{ $z->id }}"
                                                            data-value="{{ $z->name }}">{{ $z->name }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-danger small mt-1 d-none asset-field-error-msg"
                                            id="asset_err_zone" role="alert"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 mb-3 asset-loc-col">
                                        <label class="form-label" for="asset_dd_branch">Branch <span
                                                class="text-danger">*</span></label>
                                        <div class="tax-dropdown-wrapper account-section asset-dd-wrap">
                                            <input id="asset_dd_branch" type="text"
                                                class="form-control branch-search-input asset-dd-input asset-loc-dd-input"
                                                readonly autocomplete="off" placeholder="Select branch"
                                                value="{{ $dispBranch }}">
                                            <input type="hidden" name="branch_id" class="branch_id"
                                                value="{{ $bid ?: '' }}">
                                            <div class="dropdown-menu tax-dropdown asset-dd-panel">
                                                <div class="inner-search-container">
                                                    <input type="text"
                                                        class="inner-search form-control form-control-sm"
                                                        placeholder="Search branch…" autocomplete="off">
                                                </div>
                                                <div class="branch-list"></div>
                                            </div>
                                        </div>
                                        <div class="text-danger small mt-1 d-none asset-field-error-msg"
                                            id="asset_err_branch" role="alert"></div>
                                    </div>
                                </div>
                                <div class="row mb-0">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="asset_dd_department">Department</label>
                                    <div class="tax-dropdown-wrapper account-section asset-dd-wrap" id="assetDepartmentDd">
                                        <input id="asset_dd_department" type="text" class="form-control asset-dept-search-input asset-dd-input"
                                            readonly autocomplete="off" placeholder="Select department" value="{{ e($dispDepartment) }}">
                                        <input type="hidden" name="department_id" id="asset_department" class="department_field"
                                            value="{{ $deptIdVal !== null && $deptIdVal !== '' ? (int) $deptIdVal : '' }}">
                                        <div class="dropdown-menu tax-dropdown asset-dd-panel">
                                            <div class="inner-search-container">
                                                <input type="text" class="inner-search form-control form-control-sm"
                                                    placeholder="Search department…" autocomplete="off">
                                            </div>
                                            <div class="department-list">
                                                @foreach ($departments as $d)
                                                <div data-id="{{ $d->id }}" data-value="{{ e($d->name) }}">{{ $d->name }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="asset_dd_category">Category</label>
                                    <div class="tax-dropdown-wrapper account-section asset-dd-wrap" id="assetCategoryDd">
                                        <input id="asset_dd_category" type="text" class="form-control asset-category-search-input asset-dd-input"
                                            readonly autocomplete="off" placeholder="Select category" value="{{ e($dispCategory) }}">
                                        <input type="hidden" name="category_id" id="asset_category_id" class="category_id"
                                            value="{{ $catIdForDisp !== null && $catIdForDisp !== '' ? (int) $catIdForDisp : '' }}">
                                        <div class="dropdown-menu tax-dropdown asset-dd-panel">
                                            <div class="inner-search-container">
                                                <input type="text" class="inner-search form-control form-control-sm"
                                                    placeholder="Search category…" autocomplete="off">
                                            </div>
                                            <div class="category-list">
                                                @foreach ($categories as $c)
                                                <div data-id="{{ $c->id }}" data-value="{{ $c->name }}">{{ $c->name }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="asset_code">Asset code</label>
                                    <input id="asset_code" type="text" name="asset_code" class="form-control" maxlength="100"
                                        value="{{ $o('asset_code', $asset->asset_code ?? '') }}" placeholder="Optional unique code">
                                </div>
                                <!-- <div class="col-md-4 mb-3">
                                    <label class="form-label" for="asset_name">Asset name <span class="text-danger">*</span></label>
                                    <input id="asset_name" type="text" name="asset_name" class="form-control" required maxlength="255"
                                        value="{{ $o('asset_name', $asset->asset_name ?? '') }}" placeholder="e.g. Desktop – Finance 01">
                                    <div class="text-danger small mt-1 d-none asset-field-error-msg" id="asset_err_name" role="alert">
                                    </div>
                                </div> -->
                            </div>
                            </div>

                            @if (!empty($consumable))
                                <div class="alert alert-info py-2 mb-3">
                                    <strong>Consumable Store</strong> — converting
                                    <strong>{{ $consumable->item_name }}</strong>
                                    (available qty: {{ number_format((float) $consumable->quantity, 2) }}, GRN
                                    {{ $consumable->grn_number ?? '—' }}).
                                </div>
                                <input type="hidden" name="consumable_store_id" value="{{ $consumable->id }}">
                                <div class="row mb-3">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="convert_qty">Quantity to move to asset <span
                                                class="text-danger">*</span></label>
                                        <input id="convert_qty" type="number" name="convert_qty"
                                            class="form-control" step="0.01" min="0.01"
                                            max="{{ (float) $consumable->quantity }}"
                                            value="{{ old('convert_qty', min(1, (float) $consumable->quantity)) }}"
                                            required>
                                    </div>
                                </div>
                            @endif

                            <input type="hidden" name="type_attributes[ui_template]" id="asset_ui_template"
                                value="{{ e($uiTemplate) }}">

                            <div class="asset-form-section asset-form-section-device mb-4">
                                <div class="asset-form-section-title">
                                    <i class="bi bi-cpu" aria-hidden="true"></i>
                                    <span>Device details</span>
                                </div>
                                <p class="text-muted mb-3 asset-form-device-hint">These fields change when you pick a
                                    category (for example System, CCTV, or printer).</p>

                                <div id="asset-panel-system"
                                    class="asset-type-panel asset-form-type-panel mb-3 {{ in_array($uiTemplate, ['system', 'cpu', 'monitor'], true) ? '' : 'd-none' }}"
                                    data-asset-panel="system">
                                    <div class="asset-type-panel-heading">System</div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_system_model">System model</label>
                                            <input id="asset_system_model" type="text"
                                                name="type_attributes[system_model]" class="form-control"
                                                value="{{ $ta('system_model') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_monitor_model">Monitor model</label>
                                            <input id="asset_monitor_model" type="text"
                                                name="type_attributes[monitor_model]" class="form-control"
                                                value="{{ $ta('monitor_model') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_serial_system">Serial number</label>
                                            <input id="asset_serial_system" type="text" name="serial_number"
                                                class="form-control"
                                                value="{{ $o('serial_number', $asset->serial_number ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_os">OS installed</label>
                                            <input id="asset_os" type="text" name="type_attributes[os_installed]"
                                                class="form-control" value="{{ $ta('os_installed') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_processor">Processor</label>
                                            <input id="asset_processor" type="text"
                                                name="type_attributes[processor]" class="form-control"
                                                value="{{ $ta('processor') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_ssd">SSD/HDD</label>
                                            <input id="asset_ssd" type="text" name="type_attributes[ssd_hdd]"
                                                class="form-control" value="{{ $ta('ssd_hdd') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_ram">RAM</label>
                                            <input id="asset_ram" type="text" name="type_attributes[ram]"
                                                class="form-control" value="{{ $ta('ram') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_purchase">Purchase date</label>
                                            <input id="asset_purchase" type="date" name="purchase_date"
                                                class="form-control"
                                                value="{{ $o('purchase_date', $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '') }}">
                                        </div>
                                    </div>
                                </div>

                                <div id="asset-panel-printer"
                                    class="asset-type-panel asset-form-type-panel mb-3 {{ $uiTemplate === 'printer' ? '' : 'd-none' }}"
                                    data-asset-panel="printer">
                                    <div class="asset-type-panel-heading">Printer</div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_printer_model">Printer model</label>
                                            <input id="asset_printer_model" type="text" name="model"
                                                class="form-control" value="{{ $o('model', $asset->model ?? '') }}"
                                                autocomplete="off">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_printer_serial">Printer serial
                                                no.</label>
                                            <input id="asset_printer_serial" type="text" name="serial_number"
                                                class="form-control"
                                                value="{{ $o('serial_number', $asset->serial_number ?? '') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div id="asset-panel-cctv"
                                    class="asset-type-panel asset-form-type-panel mb-3 {{ $uiTemplate === 'cctv' ? '' : 'd-none' }}"
                                    data-asset-panel="cctv">
                                    <div class="asset-type-panel-heading">CCTV</div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="ta_camera_name">Camera name</label>
                                            <input id="ta_camera_name" type="text"
                                                name="type_attributes[camera_name]" class="form-control"
                                                value="{{ e($ta('camera_name')) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_cctv_brand">Camera brand</label>
                                            <input id="asset_cctv_brand" type="text" name="type_attributes[brand]"
                                                class="form-control" value="{{ $ta('brand') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_cctv_model">Camera model</label>
                                            <input id="asset_cctv_model" type="text" name="model"
                                                class="form-control" value="{{ $o('model', $asset->model ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="ta_ip_cctv">IP address</label>
                                            <input id="ta_ip_cctv" type="text" name="type_attributes[ip_address]"
                                                class="form-control" value="{{ e($ta('ip_address')) }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div id="asset-panel-recorder"
                                    class="asset-type-panel asset-form-type-panel mb-3 {{ $uiTemplate === 'nvr' || $uiTemplate === 'dvr' ? '' : 'd-none' }}"
                                    data-asset-panel="recorder">
                                    <div class="asset-type-panel-heading" id="asset_recorder_heading">{{ $recorderUiKind === 'dvr' ? 'DVR' : 'NVR' }}</div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="ta_dvr_name" id="lbl_rec_name">{{ $recorderUiKind === 'dvr' ? 'DVR name' : 'NVR name' }}</label>
                                            <input id="ta_dvr_name" type="text" name="type_attributes[dvr_name]"
                                                class="form-control" value="{{ e($ta('dvr_name')) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_dvr_brand" id="lbl_rec_brand">Brand</label>
                                            <input id="asset_dvr_brand" type="text" name="type_attributes[brand]"
                                                class="form-control" value="{{ $ta('brand') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_dvr_model" id="lbl_rec_model">{{ $recorderUiKind === 'dvr' ? 'DVR model' : 'NVR model' }}</label>
                                            <input id="asset_dvr_model" type="text" name="model"
                                                class="form-control" value="{{ $o('model', $asset->model ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="ta_ip_dvr" id="lbl_rec_ip">{{ $recorderUiKind === 'dvr' ? 'DVR IP address' : 'NVR IP address' }}</label>
                                            <input id="ta_ip_dvr" type="text" name="type_attributes[ip_address]"
                                                class="form-control" value="{{ e($ta('ip_address')) }}"
                                                autocomplete="off">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="ta_dvr_channel" id="lbl_rec_channel">Channel</label>
                                            <input id="ta_dvr_channel" type="text"
                                                name="type_attributes[dvr_channel]" class="form-control"
                                                value="{{ e($ta('dvr_channel')) }}">
                                        </div>
                                    </div>
                                </div>

                                <div id="asset-panel-network"
                                    class="asset-type-panel asset-form-type-panel mb-3 {{ $uiTemplate === 'switch' || $uiTemplate === 'router' ? '' : 'd-none' }}"
                                    data-asset-panel="network">
                                    <div class="asset-type-panel-heading">Switch / Router</div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_sw_brand">Brand name</label>
                                            <input id="asset_sw_brand" type="text" name="type_attributes[brand]"
                                                class="form-control" value="{{ $ta('brand') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="asset_sw_model">Model</label>
                                            <input id="asset_sw_model" type="text" name="model"
                                                class="form-control" value="{{ $o('model', $asset->model ?? '') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="ta_ip_sw">IP address</label>
                                            <input id="ta_ip_sw" type="text" name="type_attributes[ip_address]"
                                                class="form-control" value="{{ e($ta('ip_address')) }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="ta_dev_user">Username</label>
                                            <input id="ta_dev_user" type="text"
                                                name="type_attributes[device_username]" class="form-control"
                                                value="{{ e($ta('device_username')) }}" autocomplete="off">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="ta_dev_pass">Password</label>
                                            <input id="ta_dev_pass" type="password"
                                                name="type_attributes[device_password]" class="form-control"
                                                value="{{ e($ta('device_password')) }}" autocomplete="new-password">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div id="asset-panel-shared"
                                class="asset-form-section asset-form-section-lifecycle asset-panel-shared mb-0">
                                <div class="asset-form-section-title">
                                    <i class="bi bi-clipboard-check" aria-hidden="true"></i>
                                    <span>Warranty, status &amp; people</span>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="asset_warranty">Warranty (expiry)</label>
                                        <input id="asset_warranty" type="date" name="warranty_expiry"
                                            class="form-control"
                                            value="{{ $o('warranty_expiry', $asset->warranty_expiry ? $asset->warranty_expiry->format('Y-m-d') : '') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="asset_status">Status <span
                                                class="text-danger">*</span></label>
                                        <select id="asset_status" name="status" class="form-control" required>
                                            @foreach ($statuses as $st)
                                                <option value="{{ $st }}"
                                                    {{ $o('status', $asset->status ?? \App\Models\Asset::STATUS_AVAILABLE) === $st ? 'selected' : '' }}>
                                                    {{ ucfirst($st) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger small mt-1 d-none asset-field-error-msg"
                                            id="asset_err_status" role="alert"></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="asset_assignee_display">Assigned user /
                                            Responsible person</label>
                                        <div class="tax-dropdown-wrapper account-section asset-dd-wrap"
                                            id="assetAssigneeDd">
                                            <input type="text" id="asset_assignee_display"
                                                class="form-control asset-assignee-search-input asset-dd-input"
                                                readonly autocomplete="off" placeholder="Select user"
                                                value="{{ e($assigneeDisplayVal) }}">
                                            <input type="hidden" name="asset_assignee" id="asset_assignee_token"
                                                value="{{ e($assigneeToken) }}"
                                                @if (!empty($consumable)) required @endif>
                                            <input type="hidden" name="asset_assignee_label"
                                                id="asset_assignee_label" value="{{ e($assigneeLabelVal) }}">
                                            <div class="dropdown-menu tax-dropdown asset-dd-panel asset-assignee-dd">
                                                <div class="inner-search-container">
                                                    <input type="text"
                                                        class="inner-search form-control form-control-sm"
                                                        placeholder="Search user…" autocomplete="off">
                                                </div>
                                                <div class="user-list"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 mb-3">
                                        <label class="form-label" for="asset_remarks">Remarks</label>
                                        <textarea id="asset_remarks" name="remarks" class="form-control" rows="3" maxlength="5000"
                                            placeholder="Notes…">{{ $o('remarks', $asset->remarks ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="asset-form-actions d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4 pt-4">
                            <a href="{{ route('superadmin.assets.dashboard') }}"
                                class="btn btn-outline-secondary asset-form-btn-cancel">
                                <i class="bi bi-arrow-left me-1"></i>Back to list
                            </a>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary asset-form-btn-submit px-4">
                                    <i
                                        class="bi bi-check2-circle me-1"></i>{{ $isEdit ? 'Update asset' : 'Save asset' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @include('superadmin.superadminfooter')

    <script>
        (function() {
            var root = document.querySelector('.asset-form-premium');
            if (!root) return;

            var assetBranchFetchUrl = @json(route('superadmin.getbranchfetch'));
            var assetCsrf = '';
            var m = document.querySelector('meta[name="csrf-token"]');
            if (m) assetCsrf = m.getAttribute('content') || '';

            var form = root.closest('form');
            var assetLocStrip = root.querySelector('#assetLocationStrip');

            function clearLocationInvalid() {
                root.querySelectorAll('#assetLocationStrip .form-control.is-invalid').forEach(function(el) {
                    el.classList.remove('is-invalid');
                });
            }

            function setFieldMessage(errId, inputEl, message) {
                var box = document.getElementById(errId);
                if (!box) return;
                if (message) {
                    box.textContent = message;
                    box.classList.remove('d-none');
                    if (inputEl) inputEl.classList.add('is-invalid');
                } else {
                    box.textContent = '';
                    box.classList.add('d-none');
                    if (inputEl) inputEl.classList.remove('is-invalid');
                }
            }

            function hideClientErrors() {
                var strip = root.querySelector('#assetLocationStrip');
                setFieldMessage('asset_err_company', strip ? strip.querySelector('.company-search-input') : null, '');
                setFieldMessage('asset_err_zone', strip ? strip.querySelector('.zone-search-input') : null, '');
                setFieldMessage('asset_err_branch', strip ? strip.querySelector('.branch-search-input') : null, '');
                var ne = document.getElementById('asset_name');
                var se = document.getElementById('asset_status');
                setFieldMessage('asset_err_name', ne, '');
                setFieldMessage('asset_err_status', se, '');
                clearLocationInvalid();
                if (ne) ne.classList.remove('is-invalid');
                if (se) se.classList.remove('is-invalid');
            }

            if (form) {
                form.setAttribute('novalidate', 'novalidate');
                form.addEventListener('submit', function(e) {
                    var strip = root.querySelector('#assetLocationStrip');
                    var companyId = strip && strip.querySelector('.company_id') ? String(strip.querySelector(
                        '.company_id').value || '').trim() : '';
                    var zoneId = strip && strip.querySelector('.zone_id') ? String(strip.querySelector(
                        '.zone_id').value || '').trim() : '';
                    var branchId = strip && strip.querySelector('.branch_id') ? String(strip.querySelector(
                        '.branch_id').value || '').trim() : '';
                    var nameEl = document.getElementById('asset_name');
                    var statusEl = document.getElementById('asset_status');
                    var nameVal = nameEl ? String(nameEl.value || '').trim() : '';
                    var statusVal = statusEl ? String(statusEl.value || '').trim() : '';

                    hideClientErrors();

                    var cIn = strip ? strip.querySelector('.company-search-input') : null;
                    var zIn = strip ? strip.querySelector('.zone-search-input') : null;
                    var bIn = strip ? strip.querySelector('.branch-search-input') : null;

                    var hasErr = false;
                    if (!companyId) {
                        setFieldMessage('asset_err_company', cIn, 'Company is required.');
                        hasErr = true;
                    }
                    if (!zoneId) {
                        setFieldMessage('asset_err_zone', zIn, 'Zone is required.');
                        hasErr = true;
                    }
                    if (!branchId) {
                        setFieldMessage('asset_err_branch', bIn, 'Branch is required.');
                        hasErr = true;
                    }
                    // if (!nameVal) {
                    //     setFieldMessage('asset_err_name', nameEl, 'Asset name is required.');
                    //     hasErr = true;
                    // }
                    if (!statusVal) {
                        setFieldMessage('asset_err_status', statusEl, 'Status is required.');
                        hasErr = true;
                    }

                    if (hasErr) {
                        e.preventDefault();
                        e.stopPropagation();
                        var firstErr = root.querySelector('.asset-field-error-msg:not(.d-none)');
                        if (firstErr) firstErr.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                        return false;
                    }
                });
            }

            var nameInput = document.getElementById('asset_name');
            var statusSelect = document.getElementById('asset_status');
            if (nameInput) nameInput.addEventListener('input', hideClientErrors);
            if (statusSelect) statusSelect.addEventListener('change', hideClientErrors);

            function closeAllPanels() {
                root.querySelectorAll('.asset-dd-panel').forEach(function(p) {
                    p.classList.remove('show');
                });
            }

            function filterList(panel, q) {
                q = (q || '').toLowerCase();
                var list = panel.querySelector('.company-list') || panel.querySelector('.zone-list') || panel
                    .querySelector('.branch-list') || panel.querySelector('.category-list') || panel.querySelector(
                        '.department-list') ||
                    panel.querySelector('.user-list');
                if (!list) return;
                list.querySelectorAll('div').forEach(function(el) {
                    el.style.display = !q || (el.textContent || '').toLowerCase().indexOf(q) !== -1 ? '' :
                        'none';
                });
            }

            /**
             * Quotation-style: POST zone id → tbl_locations rows for that zone only.
             */
            function loadAssetBranchesForZone(zoneId, done) {
                var strip = root.querySelector('#assetLocationStrip');
                if (!strip) {
                    if (done) done();
                    return;
                }
                var branchPanel = strip.querySelector('.branch-list');
                if (!branchPanel) {
                    if (done) done();
                    return;
                }
                zoneId = String(zoneId || '').trim();
                branchPanel.innerHTML = '';
                if (!zoneId) {
                    if (done) done();
                    return;
                }
                var fd = new FormData();
                fd.append('id', zoneId);
                fd.append('_token', assetCsrf);
                fetch(assetBranchFetchUrl, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                }).then(function(r) {
                    return r.json();
                }).then(function(data) {
                    var rows = data.branch || [];
                    rows.forEach(function(branch) {
                        var div = document.createElement('div');
                        div.setAttribute('data-id', branch.id);
                        div.setAttribute('data-value', branch.name || '');
                        if (branch.zone_id != null) {
                            div.setAttribute('data-zone', branch.zone_id);
                        }
                        div.textContent = branch.name || '';
                        branchPanel.appendChild(div);
                    });
                    if (done) done();
                }).catch(function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Could not load branches for this zone.');
                    }
                    if (done) done();
                });
            }

            root.addEventListener('click', function(e) {
                if (!e.target.closest('.asset-dd-wrap')) closeAllPanels();
            });

            root.querySelectorAll('.asset-dd-input').forEach(function(inp) {
                inp.addEventListener('click', function(ev) {
                    ev.stopPropagation();
                    closeAllPanels();
                    var panel = this.closest('.asset-dd-wrap').querySelector('.asset-dd-panel');
                    if (panel) {
                        panel.classList.add('show');
                        var inner = panel.querySelector('.inner-search');
                        if (inner) {
                            inner.value = '';
                            filterList(panel, '');
                            inner.focus();
                        }
                    }
                });
            });

            root.querySelectorAll('.asset-dd-panel .inner-search').forEach(function(inner) {
                inner.addEventListener('input', function() {
                    filterList(this.closest('.asset-dd-panel'), this.value);
                });
                inner.addEventListener('click', function(ev) {
                    ev.stopPropagation();
                });
            });

            root.querySelectorAll('.company-list div').forEach(function(div) {
                div.addEventListener('click', function(ev) {
                    ev.stopPropagation();
                    var wrap = this.closest('.asset-dd-wrap');
                    wrap.querySelector('.company-search-input').value = this.getAttribute(
                        'data-value') || this.textContent.trim();
                    wrap.querySelector('.company_id').value = this.getAttribute('data-id') || '';
                    this.closest('.asset-dd-panel').classList.remove('show');
                    hideClientErrors();
                });
            });

            root.querySelectorAll('.zone-list div').forEach(function(div) {
                div.addEventListener('click', function(ev) {
                    ev.stopPropagation();
                    var strip = root.querySelector('#assetLocationStrip');
                    var wrap = this.closest('.asset-dd-wrap');
                    wrap.querySelector('.zone-search-input').value = this.getAttribute('data-value') ||
                        this.textContent.trim();
                    wrap.querySelector('.zone_id').value = this.getAttribute('data-id') || '';
                    this.closest('.asset-dd-panel').classList.remove('show');
                    var zid = wrap.querySelector('.zone_id').value;
                    var bInp = strip.querySelector('.branch-search-input');
                    var bHid = strip.querySelector('.branch_id');
                    if (bInp) bInp.value = '';
                    if (bHid) bHid.value = '';
                    loadAssetBranchesForZone(zid);
                    hideClientErrors();
                });
            });

            if (assetLocStrip) {
                assetLocStrip.addEventListener('click', function(ev) {
                    var item = ev.target.closest('.branch-list div[data-id]');
                    if (!item) return;
                    ev.stopPropagation();
                    var wrap = item.closest('.asset-dd-wrap');
                    if (!wrap) return;
                    wrap.querySelector('.branch-search-input').value = item.getAttribute('data-value') ||
                        item.textContent.trim();
                    wrap.querySelector('.branch_id').value = item.getAttribute('data-id') || '';
                    var panel = wrap.querySelector('.asset-dd-panel');
                    if (panel) panel.classList.remove('show');
                    hideClientErrors();
                });
            }

            function templateFromCategoryLabel(label) {
                var t = String(label || '').toLowerCase();
                if (!t.trim()) return '';
                if (t.indexOf('printer') !== -1) return 'printer';
                if (t.indexOf('cctv') !== -1 || t.indexOf('camera') !== -1) return 'cctv';
                if (t.indexOf('nvr') !== -1) return 'nvr';
                if (t.indexOf('dvr') !== -1) return 'dvr';
                if (t.indexOf('router') !== -1) return 'router';
                if (t.indexOf('switch') !== -1) return 'switch';
                if (t.indexOf('monitor') !== -1) return 'system';
                if (t.indexOf('cpu') !== -1 || t.indexOf('desktop') !== -1 || t.indexOf('laptop') !== -1) return 'system';
                return 'system';
            }

            root.querySelectorAll('#assetDepartmentDd .department-list div').forEach(function(div) {
                div.addEventListener('click', function(ev) {
                    ev.stopPropagation();
                    var wrap = this.closest('.asset-dd-wrap');
                    var label = this.getAttribute('data-value');
                    if (label === null || label === undefined) label = '';
                    var id = this.getAttribute('data-id');
                    if (id === null || id === undefined) id = '';
                    wrap.querySelector('.asset-dept-search-input').value = label;
                    var hid = wrap.querySelector('.department_field');
                    if (hid) hid.value = id;
                    this.closest('.asset-dd-panel').classList.remove('show');
                    hideClientErrors();
                });
            });

            root.addEventListener('click', function(ev) {
                var ar = ev.target.closest('#assetAssigneeDd .user-list [data-assignee]');
                if (!ar) return;
                ev.stopPropagation();
                var panel = ar.closest('.asset-dd-panel');
                var tok = ar.getAttribute('data-assignee');
                if (tok === null || tok === undefined) tok = '';
                var lab = ar.getAttribute('data-label');
                if (lab === null || lab === undefined) lab = '';
                var dis = tok === '' ? '' : (ar.textContent || '').trim();
                var te = document.getElementById('asset_assignee_token');
                var le = document.getElementById('asset_assignee_label');
                var inp = document.getElementById('asset_assignee_display');
                if (te) te.value = tok;
                if (le) le.value = lab;
                if (inp) inp.value = dis;
                if (panel) panel.classList.remove('show');
                var ul = ar.closest('.user-list');
                if (ul) ul.querySelectorAll('[data-assignee]').forEach(function(d) {
                    d.classList.toggle('selected', d === ar);
                });
                hideClientErrors();
            });

            root.querySelectorAll('#assetCategoryDd .category-list div').forEach(function(div) {
                div.addEventListener('click', function(ev) {
                    ev.stopPropagation();
                    var wrap = this.closest('.asset-dd-wrap');
                    var id = this.getAttribute('data-id');
                    var label = this.getAttribute('data-value') !== null ? this.getAttribute(
                        'data-value') : this.textContent.trim();
                    var idStr = id === null || id === undefined ? '' : String(id);
                    wrap.querySelector('.asset-category-search-input').value = idStr === '' ? '' : (
                        label || '');
                    wrap.querySelector('.category_id').value = idStr;
                    this.closest('.asset-dd-panel').classList.remove('show');
                    var h = document.getElementById('asset_ui_template');
                    if (h) h.value = idStr === '' ? '' : templateFromCategoryLabel(label);
                    syncAssetTypePanels();
                    hideClientErrors();
                });
            });

            var zEl = assetLocStrip ? assetLocStrip.querySelector('.zone_id') : null;
            var bHidInit = assetLocStrip ? assetLocStrip.querySelector('.branch_id') : null;
            var preBranchId = bHidInit ? String(bHidInit.value || '').trim() : '';
            if (zEl && zEl.value) {
                loadAssetBranchesForZone(zEl.value, function() {
                    if (!preBranchId || !assetLocStrip) return;
                    var bp = assetLocStrip.querySelector('.branch-list');
                    var bInp = assetLocStrip.querySelector('.branch-search-input');
                    if (!bp || !bInp) return;
                    var found = null;
                    bp.querySelectorAll('div[data-id]').forEach(function(el) {
                        if (String(el.getAttribute('data-id')) === preBranchId) found = el;
                    });
                    if (found) {
                        bInp.value = found.getAttribute('data-value') || found.textContent.trim();
                    }
                });
            }

            function mapTemplateToPanelKey(mode) {
                if (mode === 'general') mode = 'system';
                if (!mode) return '';
                if (mode === 'cpu' || mode === 'monitor' || mode === 'system') return 'system';
                if (mode === 'switch' || mode === 'router') return 'network';
                if (mode === 'nvr' || mode === 'dvr') return 'recorder';
                return mode;
            }

            function updateAssetRecorderPanelLabels(kind) {
                var isDvr = String(kind || '').toLowerCase() === 'dvr';
                var L = isDvr ? {
                    title: 'DVR',
                    name: 'DVR name',
                    model: 'DVR model',
                    ip: 'DVR IP address',
                    brand: 'Brand',
                    channel: 'Channel'
                } : {
                    title: 'NVR',
                    name: 'NVR name',
                    model: 'NVR model',
                    ip: 'NVR IP address',
                    brand: 'Brand',
                    channel: 'Channel'
                };
                var h = document.getElementById('asset_recorder_heading');
                if (h) h.textContent = L.title;
                var ln = document.getElementById('lbl_rec_name');
                if (ln) ln.textContent = L.name;
                var lb = document.getElementById('lbl_rec_brand');
                if (lb) lb.textContent = L.brand;
                var lm = document.getElementById('lbl_rec_model');
                if (lm) lm.textContent = L.model;
                var li = document.getElementById('lbl_rec_ip');
                if (li) li.textContent = L.ip;
                var lc = document.getElementById('lbl_rec_channel');
                if (lc) lc.textContent = L.channel;
            }

            function syncAssetTypePanels() {
                var hidden = document.getElementById('asset_ui_template');
                var catEl = root.querySelector('#assetCategoryDd .category_id');
                var hasCat = catEl && String(catEl.value || '').trim() !== '';
                if (!hasCat) {
                    root.querySelectorAll('.asset-type-panel').forEach(function(panel) {
                        if (panel.id === 'asset-panel-shared') return;
                        panel.classList.add('d-none');
                        panel.querySelectorAll('input, select, textarea').forEach(function(el) {
                            if (!el.name) return;
                            el.disabled = true;
                        });
                    });
                    return;
                }
                var mode = hidden && String(hidden.value || '').trim() !== '' ? hidden.value : '';
                if (mode === 'general') mode = 'system';
                if (mode === '') {
                    var catInp = root.querySelector('#asset_dd_category');
                    mode = templateFromCategoryLabel(catInp ? catInp.value : '');
                    if (hidden && mode) hidden.value = mode;
                }
                if (!mode) mode = 'system';
                var pkey = mapTemplateToPanelKey(mode);
                root.querySelectorAll('.asset-type-panel').forEach(function(panel) {
                    if (panel.id === 'asset-panel-shared') return;
                    var pid = panel.getAttribute('data-asset-panel');
                    var on = pid === pkey;
                    panel.classList.toggle('d-none', !on);
                    panel.querySelectorAll('input, select, textarea').forEach(function(el) {
                        if (!el.name) return;
                        el.disabled = !on;
                    });
                });
                if (pkey === 'recorder') {
                    updateAssetRecorderPanelLabels(mode === 'dvr' ? 'dvr' : 'nvr');
                }
            }

            syncAssetTypePanels();
        })();
    </script>

    <style>
        .asset-module-tabs {
            display: flex;
            gap: 4px;
            border-bottom: 1px solid #e2e8f0;
            margin: 0;
            padding: 0 12px;
            background: #fff;
        }

        .asset-form-module-tabs {
            max-width: 100%;
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

        .asset-form-page-card {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            background-color: #ffffff;
        }

        .asset-form-card-header {
            padding-top: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #f1f5f9 !important;
            background: #ffffff;
        }

        .asset-form-card-header h5 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        .asset-form-section {
            padding: 1.5rem 1.5rem 1.75rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -2px rgba(0, 0, 0, 0.02);
            transition: box-shadow 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }

        .asset-form-section:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -4px rgba(0, 0, 0, 0.02);
            border-color: #cbd5e1;
        }

        .asset-form-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            text-transform: none;
            color: #0f172a;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 0.75rem;
        }

        .asset-form-section-title i {
            color: #3b82f6;
            font-size: 1.15rem;
            background: #eff6ff;
            padding: 0.4rem;
            border-radius: 8px;
        }

        .asset-form-section-location .asset-loc-badge {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            padding: 0.4em 0.85em;
            border: 1px solid transparent;
        }

        .asset-form-section-location .asset-loc-badge-company {
            background: #eef2ff;
            color: #3730a3;
            border-color: #c7d2fe;
        }

        .asset-form-section-location .asset-loc-badge-zone {
            background: #ecfeff;
            color: #0f766e;
            border-color: #99f6e4;
        }

        .asset-form-section-location .asset-loc-badge-branch {
            background: #f0fdf4;
            color: #14532d;
            border-color: #bbf7d0;
        }

        .asset-form-section-location .asset-loc-field-label {
            margin-bottom: 0.5rem;
        }

        .asset-form-section-location .asset-loc-dd-input {
            border-radius: 10px;
            font-size: 0.875rem;
            min-height: 42px;
            border-color: #cbd5e1;
            background-color: #f8fafc;
            transition: all 0.2s ease-in-out;
        }

        .asset-form-section-location .asset-loc-dd-input:focus {
            background-color: #ffffff;
            border-color: #818cf8;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.15);
        }

        .asset-form-premium .tax-dropdown-wrapper.asset-dd-wrap {
            position: relative;
            width: 100% !important;
            max-width: 100%;
        }

        .asset-form-premium .asset-dd-panel {
            display: none;
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 4px);
            z-index: 100;
            max-height: 260px;
            overflow: hidden;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            animation: fadeInDown 0.2s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .asset-form-premium .asset-dd-panel.show {
            display: block;
        }

        .asset-form-premium #assetLocationStrip .company-list,
        .asset-form-premium #assetLocationStrip .zone-list,
        .asset-form-premium #assetLocationStrip .branch-list,
        .asset-form-premium #assetCategoryDd .category-list,
        .asset-form-premium #assetDepartmentDd .department-list {
            max-height: 180px;
            overflow-y: auto;
        }

        .asset-form-premium #assetLocationStrip .company-list div,
        .asset-form-premium #assetLocationStrip .zone-list div,
        .asset-form-premium #assetLocationStrip .branch-list div,
        .asset-form-premium #assetCategoryDd .category-list div,
        .asset-form-premium #assetDepartmentDd .department-list div {
            padding: 10px 14px;
            cursor: pointer;
            font-size: 0.875rem;
            color: #334155;
            transition: background-color 0.15s ease;
        }

        .asset-form-premium #assetLocationStrip .company-list div:hover,
        .asset-form-premium #assetLocationStrip .zone-list div:hover,
        .asset-form-premium #assetLocationStrip .branch-list div:hover,
        .asset-form-premium #assetCategoryDd .category-list div:hover,
        .asset-form-premium #assetDepartmentDd .department-list div:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .asset-form-premium #assetAssigneeDd .user-list {
            max-height: 180px;
            overflow-y: auto;
        }

        .asset-form-premium #assetAssigneeDd .user-list div {
            padding: 10px 14px;
            cursor: pointer;
            font-size: 0.875rem;
            color: #334155;
            transition: background-color 0.15s ease;
        }

        .asset-form-premium #assetAssigneeDd .user-list div:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .asset-form-premium #assetAssigneeDd .user-list div.selected {
            background: #ecfdf5;
            color: #065f46;
            font-weight: 600;
        }

        .asset-form-section-meta,
        .asset-form-section-device,
        .asset-form-section-lifecycle.asset-panel-shared {
            background: #ffffff;
            border-color: #e2e8f0;
        }

        .asset-form-device-hint {
            font-size: 0.875rem;
            line-height: 1.6;
            max-width: 42rem;
            color: #64748b;
        }

        .asset-form-type-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.25rem 1.25rem 0.75rem;
            transition: all 0.2s ease-in-out;
        }

        .asset-form-type-panel:hover {
            border-color: #cbd5e1;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -2px rgba(0, 0, 0, 0.02);
        }

        .asset-type-panel-heading {
            font-size: 0.875rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .asset-form-premium .form-label {
            font-size: 0.8125rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #475569;
            margin-bottom: 0.4rem;
        }

        .asset-form-premium .form-control,
        .asset-form-premium select.form-control {
            border-radius: 8px;
            border-color: #cbd5e1;
            font-size: 0.875rem;
            background-color: #f8fafc;
            transition: all 0.2s ease-in-out;
        }

        .asset-form-premium .form-control:focus,
        .asset-form-premium select.form-control:focus {
            background-color: #ffffff;
            border-color: #60a5fa;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
        }

        .asset-form-premium .form-control::placeholder {
            color: #94a3b8;
        }

        .asset-form-premium .asset-dd-input:not(.asset-loc-dd-input) {
            min-height: 42px;
        }

        .asset-form-actions {
            border-top: 1px solid #f1f5f9;
            padding-top: 1.5rem !important;
            margin-top: 1rem;
            padding-bottom: 0.5rem;
            background: #ffffff;
        }

        .asset-form-btn-submit {
            font-weight: 600;
            min-width: 9rem;
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
        }

        .asset-form-btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.3);
        }

        .asset-form-btn-cancel {
            font-weight: 600;
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            color: #475569;
            border-color: #cbd5e1;
            transition: all 0.2s ease-in-out;
        }

        .asset-form-btn-cancel:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }
    </style>
    <script>
        (function() {
            var wrap = document.getElementById('assetAssigneeDd');
            if (!wrap) return;

            var employeeDataUrl = @json($employeeDataUrl);
            var input = wrap.querySelector('.asset-assignee-search-input');
            var listEl = wrap.querySelector('.user-list');
            var tokenEl = document.getElementById('asset_assignee_token');
            var labelEl = document.getElementById('asset_assignee_label');
            var preToken = tokenEl ? String(tokenEl.value || '').trim() : '';

            function markSelected(container) {
                if (!container || !tokenEl) return;
                var t = tokenEl.value;
                container.querySelectorAll('[data-assignee]').forEach(function(div) {
                    div.classList.toggle('selected', div.getAttribute('data-assignee') === t);
                });
            }

            var csrf = document.querySelector('meta[name="csrf-token"]');
            var headers = {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            if (csrf && csrf.getAttribute('content')) {
                headers['X-CSRF-TOKEN'] = csrf.getAttribute('content');
            }

            fetch(employeeDataUrl, {
                    headers: headers,
                    credentials: 'same-origin'
                })
                .then(function(r) {
                    return r.json();
                })
                .then(function(data) {
                    var rows = data.data || [];
                    if (!listEl) return;
                    listEl.innerHTML = '';
                    var addedEmp = {};
                    var addedVal = {};
                    rows.forEach(function(emp) {
                        /* getEmployeeData(): id = employment_id, user.name = full name */
                        var empKey = String(emp.employment_id || emp.id || '').trim();
                        if (!empKey || addedEmp[empKey]) return;
                        addedEmp[empKey] = true;
                        var uid = emp.user_id;
                        if (uid !== null && uid !== undefined && uid !== '' && emp.active_status === 1)
                            return;
                        var namePart = String(emp.fullname || (emp.user && emp.user.name) || emp.user_fullname || '').trim();
                        var displayLabel = namePart ? (empKey + ' - ' + namePart) : empKey;
                        var val;
                        var optText;
                        if (uid !== null && uid !== undefined && uid !== '') {
                            val = 'u:' + String(uid);
                            optText = displayLabel;
                        } else {
                            val = 'h:' + encodeURIComponent(empKey);
                            optText = displayLabel;
                        }
                        if (addedVal[val]) return;
                        addedVal[val] = true;
                        var div = document.createElement('div');
                        div.setAttribute('data-assignee', val);
                        div.setAttribute('data-label', displayLabel);
                        div.textContent = optText;
                        listEl.appendChild(div);
                    });
                    markSelected(listEl);
                    if (preToken && input) {
                        var found = null;
                        listEl.querySelectorAll('[data-assignee]').forEach(function(d) {
                            if (d.getAttribute('data-assignee') === preToken) found = d;
                        });
                        if (found && !input.value) {
                            input.value = (found.textContent || '').trim();
                        }
                    }
                })
                .catch(function() {
                    if (input) input.placeholder = 'Could not load users';
                });
        })();
    </script>
</body>

</html>
