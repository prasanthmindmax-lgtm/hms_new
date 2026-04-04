<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">

@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/pettycash.css') }}" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .item-table {
        width: 100% !important;
        table-layout: fixed;
    }

    .item-table .form-control {
        min-width: 0;
        width: 100%;
    }

    .item-table th,
    .item-table td {
        vertical-align: middle;
        padding: 0.7rem;
    }

    .item-table th {
        font-weight: 600;
    }

    .item-table .tax-dropdown-wrapper {
        width: 100%;
    }

    .item-table td .btn {
        margin-right: 4px;
    }

    .report-item-card {
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        margin-bottom: 6px;
        border: 1px solid #e5e7eb;
        transition: 0.2s;
    }

    .report-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .report-id {
        font-weight: 600;
    }

    .report-amount {
        font-weight: 600;
        text-align: right;
        min-width: 80px;
    }

    .report-name {
        font-size: 14px;
        margin-top: 4px;
    }

    .report-date {
        font-size: 12px;
        color: #6b7280;
    }

    .report-item-card:hover {
        background: #2563eb;
        color: #fff;
    }

    .report-item-card:hover .report-date {
        color: #e0e7ff !important;
    }

    .new-report-option {
        border-top: 1px solid #e5e7eb;
        margin-top: 6px;
        padding-top: 6px;
    }

    .report-new-btn {
        padding: 10px;
        cursor: pointer;
        color: #2563eb;
        font-weight: 500;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .report-new-btn:hover {
        background: #eff6ff;
    }

    .pc-zoho-tabs .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.65rem 1rem;
    }

    .pc-zoho-tabs .nav-link.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
        background: transparent;
    }

    .pc-receipt-panel {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        min-height: 320px;
        padding: 1.25rem;
        background: #fafbfc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .pc-receipt-panel .upload-hint {
        margin-bottom: 0;
    }

    .pc-form-footer-tip {
        font-size: 0.85rem;
        color: #64748b;
    }

    .bulk-pc-table th {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #991b1b;
        white-space: nowrap;
    }

    .bulk-pc-table .form-control,
    .bulk-pc-table .form-select {
        font-size: 0.875rem;
    }

    /* Bulk receipt upload */
    .bulk-receipt-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border: 1.5px dashed #94a3b8;
        border-radius: 6px;
        background: #f8fafc;
        cursor: pointer;
        color: #64748b;
        font-size: 1.1rem;
        transition: border-color 0.18s, background 0.18s;
        position: relative;
        margin: auto;
    }

    .bulk-receipt-btn:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        color: #3b82f6;
    }

    .bulk-receipt-btn input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .bulk-receipt-thumb {
        width: 38px;
        height: 38px;
        object-fit: cover;
        border-radius: 5px;
        display: none;
        cursor: pointer;
        border: 1.5px solid #cbd5e1;
    }

    .pc-field-hint {
        display: block;
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 4px;
    }

    /* Bulk inline search-dropdown */
    .bulk-search-wrapper {
        position: relative;
    }

    .bulk-search-wrapper .bulk-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-height: 200px;
        overflow-y: auto;
        min-width: 180px;
    }

    .bulk-search-wrapper .bulk-dropdown-menu .bulk-search-inner {
        padding: 6px;
        border-bottom: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 2;
    }

    .bulk-search-wrapper .bulk-dropdown-menu .bulk-search-inner input {
        width: 100%;
        font-size: 0.8rem;
    }

    .bulk-search-wrapper .bulk-dropdown-menu .bulk-dropdown-list div {
        padding: 7px 10px;
        font-size: 0.85rem;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .bulk-search-wrapper .bulk-dropdown-menu .bulk-dropdown-list div:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .bulk-row-total {
        font-weight: 600;
        font-size: 0.875rem;
        text-align: right;
        padding-right: 4px;
        white-space: nowrap;
    }

    /* Bulk Zone/Company dropdown item styling */
    .bulk-zone-list div,
    .bulk-company-list div {
        padding: 8px 12px;
        cursor: pointer;
        font-size: 0.875rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .bulk-zone-list div:hover,
    .bulk-company-list div:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .bulk-zone-list,
    .bulk-company-list {
        max-height: 200px;
        overflow-y: auto;
    }

    /* Tax dropdown chevron (Advances-style) */
    #pettyCashForm .tax-dropdown-wrapper .form-control,
    #bulkPettyCashForm .tax-dropdown-wrapper .form-control {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 14px;
        padding-right: 36px;
        cursor: pointer;
    }

    /* Multiselect Dropdown Styling */
    .dropdown-menu.tax-dropdown.branch-menu {
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(15, 23, 42, 0.12);
        padding-bottom: 0;
        max-height: none !important;
        overflow: hidden;
    }

    .dropdown-menu.tax-dropdown .branch-actions {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 8px;
    }

    .dropdown-menu.tax-dropdown .branch-actions .btn {
        flex: 1 1 0;
        min-width: 0;
    }

    .dropdown-menu.tax-dropdown .inner-search-container {
        padding: 8px 10px;
        border-bottom: 1px solid #eef0f7;
    }

    .dropdown-menu.tax-dropdown .inner-search {
        width: 100%;
        border: 1px solid #d4d9e8;
        border-radius: 6px;
        font-size: 12.5px;
        padding: 6px 10px;
        color: #3d4a5c;
        background: #fff;
    }

    .dropdown-menu.tax-dropdown .inner-search:focus {
        border-color: #4f6ef7;
        outline: none;
        box-shadow: 0 0 0 2px rgba(79, 110, 247, .12);
    }

    .dropdown-menu.tax-dropdown .dropdown-list.multiselect {
        max-height: 220px;
        overflow-y: auto;
        padding: 4px 0;
    }

    .dropdown-menu.tax-dropdown .dropdown-list.multiselect::-webkit-scrollbar {
        width: 6px;
    }

    .dropdown-menu.tax-dropdown .dropdown-list.multiselect::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .dropdown-menu.tax-dropdown .dropdown-list.multiselect div {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 9px 12px 9px 14px;
        cursor: pointer;
        font-size: 12.5px;
        border-bottom: 1px solid #f1f5f9;
        color: #3d4a5c;
    }

    .dropdown-menu.tax-dropdown .dropdown-list.multiselect div:last-child {
        border-bottom: none;
    }

    .dropdown-menu.tax-dropdown .dropdown-list.multiselect div:hover {
        background: #f1f5f9;
    }

    .dropdown-menu.tax-dropdown .dropdown-list.multiselect div.selected {
        background: #ecfdf5;
        color: #14532d;
        font-weight: 500;
    }

    .dropdown-menu.tax-dropdown .dropdown-list.multiselect div::after {
        content: '';
        flex: 0 0 18px;
        width: 18px;
        height: 18px;
        border: 1.5px solid #cbd5e1;
        border-radius: 4px;
        background: #fff;
        box-sizing: border-box;
    }

    .dropdown-menu.tax-dropdown .dropdown-list.multiselect div.selected::after {
        content: '\2713';
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        border-color: #22c55e;
        background: #dcfce7;
        color: #15803d;
    }

    /* vendor.css hides .tab-content unless .active — keep wrapper visible for Bootstrap tab-panes */
    #pcPettycashTabContent.tab-content {
        display: block !important;
    }

    #pcPettycashTabContent.tab-content>.tab-pane {
        display: none !important;
    }

    #pcPettycashTabContent.tab-content>.tab-pane.active.show {
        display: block !important;
    }

    #btn-itemize {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #2563eb;
        text-decoration: none;
    }

    #btn-itemize:hover {
        text-decoration: underline;
    }

    /* Fix bulk action buttons alignment */
    .bulk-pc-table td:last-child {
        white-space: nowrap;
    }

    .bulk-pc-table .bulk-action-btns {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .bulk-pc-table .bulk-action-btns .btn {
        padding: 4px 10px;
        font-size: 13px;
    }

    .bulk-total-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-top: 12px;
    }

    .bulk-total-box {
        display: flex;
        align-items: center;
        gap: 20px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        padding: 10px 16px;
        border-radius: 8px;
        min-width: 250px;
        justify-content: space-between;
        font-weight: 600;
    }

    .bulk-total-box span:last-child {
        font-size: 16px;
    }

    .single-notes-upload-wrapper {
        padding: 0px;
    }
</style>

<body style="overflow-x: hidden;">
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')
    <div class="page-loader">
        <div class="bar"></div>
    </div>
    <div class="pc-container">
        <div class="pc-content">
            <div class="container">
                <h1 class="mb-3">{{ isset($pettycash) ? 'Edit' : 'New' }} Petty Cash</h1>

                @if (isset($pettycash))
                    <form id="pettyCashForm" method="POST" action="{{ route('superadmin.savepettycash') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $pettycash->id }}">
                        <input type="hidden" name="save_action" id="save_action" value="close">
                    @else
                        <ul class="nav nav-tabs pc-zoho-tabs mb-3 border-bottom-0" id="pcCreateTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-add-link" data-toggle="tab" href="#tab-add-pc"
                                    role="tab">Add Petty Cash</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-bulk-link" data-toggle="tab" href="#tab-bulk-pc"
                                    role="tab">Bulk Add Petty Cash</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link" id="tab-import-link" data-toggle="tab" href="#tab-import-pc"
                                    role="tab">Import</a>
                            </li> --}}
                        </ul>
                        <div class="tab-content active" id="pcPettycashTabContent">
                            <div class="tab-pane fade show active" id="tab-add-pc" role="tabpanel">
                                <form id="pettyCashForm" method="POST" action="{{ route('superadmin.savepettycash') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="">
                                    <input type="hidden" name="save_action" id="save_action" value="close">
                @endif
                <div class="container mt-4">
                    <div class="row mb-3">
                        <label for="report-search" class="col-md-2 ">Report<span style="color:red;">*</span></label>
                        <div class="col-md-4">
                            <div class="search-dropdown">
                                <input type="text" id="report-search" class="form-control search-input"
                                    name="report_name" placeholder="Select" autocomplete="off"
                                    value="{{ old('report_name', isset($pettycash) ? $pettycash->report_search_display ?? ($pettycash->report->report_name ?? '') : '') }}">
                                <div class="dropdown-menu" id="report-dropdown">
                                    <div class="search-box">
                                        <input type="text" placeholder="Search"
                                            class="inner-search form-control mb-2 report-inner-search">
                                    </div>
                                    <div class="report-list"></div>
                                    <div class="new-report-option sticky">
                                        <div class="report-new-btn open-report-modal">
                                            <i class="fa fa-plus"></i> New Report
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="selected-report-id" name="report_id"
                                value="{{ old('report_id', $pettycash?->report_id ?? '') }}">
                            <span class="error_report_name" style="color:red"></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="expense_date" class="col-md-2">Expense date<span
                                style="color:red;">*</span></label>
                        <div class="col-md-4">
                            <input type="text" class="form-control datepicker" autocomplete="off"
                                autocorrect="off" id="expense_date" name="expense_date" placeholder="dd/MM/yyyy"
                                required
                                value="{{ old('expense_date', $pettycash && $pettycash->expense_date ? \Carbon\Carbon::parse($pettycash->expense_date)->format('d/m/Y') : '') }}">
                            <span class="error_expense_date" style="color:red"></span>
                        </div>
                    </div>

                    <div class="row mb-3 align-items-start">
                        <div class="col-md-6">
                            <div class="row mb-2 align-items-start">
                                <label for="vendor-search" class="col-md-4  fw-semibold">Merchant</label>
                                <div class="col-md-8">
                                    <div class="search-dropdown">
                                        <input type="text" id="vendor-search" class="form-control search-input"
                                            name="vendor_name" placeholder="Search vendor..." autocomplete="off"
                                            value="{{ old('vendor_name', $pettycash?->vendor_display_name ?? '') }}">
                                        <input type="hidden" name="vendor_id" id="selected-vendor-id"
                                            value="{{ old('vendor_id', $pettycash?->vendor_id ?? '') }}">
                                        <div class="dropdown-menu" id="vendor-dropdown"
                                            style="max-height:240px; overflow:auto;">
                                            <div class="search-box">
                                                <input type="text" placeholder="Search"
                                                    class="inner-search form-control mb-2">
                                            </div>
                                            <div class="vendor-list"></div>
                                        </div>
                                    </div>
                                    <div class="address_container">
                                        <div id="billing-address"
                                            class="billing-address-section mt-3 text-muted small"></div>
                                        <div id="shipping-address"
                                            class="shipping-address-section mt-3 text-muted small"></div>
                                    </div>
                                    <span class="error_vendor_name" style="color:red"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="zone" class="col-md-2 ">Zones<span style="color:red;">*</span></label>
                        <div class="col-md-4">
                            <div class="tax-dropdown-wrapper account-section" style="width:300px">
                                <input type="text" class="form-control zone-search-input" autocomplete="off"
                                    autocorrect="off" name="zone" placeholder="Select a Zones" readonly
                                    value="{{ old('zone', $pettycash?->zone_name ?? '') }}">
                                <input type="hidden" name="zone_id" class="zone_id"
                                    value="{{ old('zone_id', $pettycash?->zone_id ?? '') }}">
                                <div class="dropdown-menu tax-dropdown">
                                    <div class="zone-list"></div>
                                </div>
                                <span class="error_zone" style="color:red"></span>
                            </div>
                        </div>
                        <label for="branch" class="col-md-2">Branch <span style="color:red;">*</span></label>
                        <div class="col-md-4">
                            <div class="tax-dropdown-wrapper account-section branch-section" style="max-width:300px">
                                <input type="text" class="form-control branch-search-input dropdown-search-input"
                                    autocomplete="off" autocorrect="off" name="branch" placeholder="Select Branch"
                                    readonly
                                    value="{{ old('branch', isset($pettycash) ? $pettycash->branch_display_names ?? ($pettycash->branch_name ?? '') : '') }}">
                                <input type="hidden" name="branch_id" class="branch_id"
                                    value="{{ old('branch_id', isset($pettycash) ? $pettycash->branch_ids ?? ($pettycash->branch_id ?? '') : '') }}">
                                <div class="dropdown-menu tax-dropdown branch-menu">
                                    <div class="inner-search-container">
                                        <input type="text" class="inner-search" placeholder="Search Branch..."
                                            autocomplete="off">
                                    </div>
                                    <div class="d-flex justify-content-between p-2 border-bottom branch-actions" style="gap:8px;">
                                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                    </div>
                                    <div class="dropdown-list multiselect branch-list"></div>
                                </div>
                                <span class="error_branch text-danger"></span>
                            </div>
                        </div>
                    </div>
                        {{-- </div> --}}
                        {{-- <div class="col-md-6">
                            <div class="qd-filter-group tax-dropdown-wrapper branch-section" style="max-width:300px">
                                <label>Branch <span class="text-danger">*</span></label>
                                <input type="text" class="form-control branch-search-input dropdown-search-input"
                                    autocomplete="off" autocorrect="off" name="branch" placeholder="Select Branch" readonly
                                    value="{{ old('branch', isset($pettycash) ? ($pettycash->branch_display_names ?? $pettycash->branch_name ?? '') : '') }}">
                                <input type="hidden" name="branch_id" class="branch_id"
                                    value="{{ old('branch_id', isset($pettycash) ? ($pettycash->branch_ids ?? $pettycash->branch_id ?? '') : '') }}">
                                <div class="dropdown-menu tax-dropdown pc-dash-branch-panel">
                                    <div class="inner-search-container"><input type="text" class="inner-search"
                                            placeholder="Search Branch..." autocomplete="off"></div>
                                    <div
                                        class="d-flex justify-content-between p-2 border-bottom pc-dash-branch-actions">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary select-all">All</button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                    </div>
                                    <div class="dropdown-list multiselect branch-list"></div>
                                </div>
                                <span class="error_branch" style="color:red"></span>
                            </div>
                        </div> --}}


                    <div class="row mb-3">
                        <label for="zone" class="col-md-2 ">Group of Company<span
                                style="color:red;">*</span></label>
                        <div class="col-md-4">
                            <div class="tax-dropdown-wrapper company-section" style="width:300px">
                                <input type="text" class="form-control company-search-input" autocomplete="off"
                                    autocorrect="off" name="company_name" placeholder="Select a Company" readonly
                                    value="{{ old('company_name', $pettycash?->company_name ?? '') }}">
                                <input type="hidden" name="company_id" class="company_id"
                                    value="{{ old('company_id', $pettycash?->company_id ?? '') }}">
                                <div class="dropdown-menu tax-dropdown">
                                    <div class="company-list"></div>
                                </div>
                                <span class="error_company" style="color:red"></span>
                            </div>
                        </div>

                        <label class="col-md-2">Claim reimbursement</label>
                        <div class="col-md-4">
                            <div class="form-check d-flex align-items-center">
                                <input type="checkbox" class="form-check-input" id="claim_reimbursement"
                                    name="claim_reimbursement" value="1"
                                    {{ isset($pettycash) ? (old('claim_reimbursement', $pettycash->claim_reimbursement ?? 0) ? 'checked' : '') : 'checked' }}>
                                <label class="form-check-label" for="claim_reimbursement">
                                    Yes
                                </label>
                                {{-- <i class="bi bi-question-circle text-muted small"
                                    style="margin-top:2px;"
                                    title="Mark if this amount should be reimbursed."></i> --}}
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3 mt-3 align-items-center">
                        <label class="col-md-2 mb-0">Expense Type</label>
                        <div class="col-md-6">
                            @php $editExpenseType = $pettycash->expense_type ?? 'single'; @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input expense-type-radio" type="radio" name="expense_type"
                                    id="single_expense" value="single"
                                    {{ $editExpenseType === 'single' ? 'checked' : '' }}>
                                <label class="form-check-label" for="single_expense">Single Expense</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input expense-type-radio" type="radio" name="expense_type"
                                    id="itemized_expense" value="itemized"
                                    {{ $editExpenseType === 'itemized' ? 'checked' : '' }}>
                                <label class="form-check-label" for="itemized_expense">Itemized
                                    Expense</label>
                            </div>
                        </div>
                    </div>

                    <div id="multiple-expense-section">
                        <div id="singleExpenseView">
                            @php
                                $singleBlockHidden =
                                    isset($pettycash) && ($pettycash->expense_type ?? 'single') === 'itemized'
                                        ? 'd-none'
                                        : '';
                            @endphp
                            @php
                                $singleItem =
                                    isset($pettycash) && ($pettycash->expense_type ?? 'single') === 'single'
                                        ? $pettycashItems->first() ?? null
                                        : null;
                                $singleCategoryId = $singleItem ? $singleItem->expense_category_id ?? '' : '';
                                $singleCategoryName = $singleCategoryId
                                    ? $categories->firstWhere('id', $singleCategoryId)->name ?? ''
                                    : '';
                                $singleAmount = $singleItem ? $singleItem->amount ?? '' : '';
                            @endphp
                            <div class="row mb-3 {{ $singleBlockHidden }}" id="pc-single-expense-block">
                                <label for="pc-single-category-input" class="col-md-2 ">Expense Category<span
                                        style="color:red;">*</span></label>
                                <div class="col-md-4">
                                    <div class="tax-dropdown-wrapper category-section w-100">
                                        <input type="text" id="pc-single-category-input"
                                            class="form-control category-search-input" autocomplete="off"
                                            placeholder="Select" readonly value="{{ $singleCategoryName }}">
                                        <input type="hidden" id="pc-single-category-id" class="category_id"
                                            value="{{ $singleCategoryId }}">
                                        <input type="hidden" id="pc-single-category-name" class="category_name"
                                            value="{{ $singleCategoryName }}">
                                        <div class="dropdown-menu tax-dropdown">
                                            <div style="max-height:200px; overflow-y:auto;">
                                                <div class="search-box p-2">
                                                    <input type="text" placeholder="Search Category"
                                                        class="inner-search form-control category-inner-search">
                                                </div>
                                                <div class="category-list" id="pc-single-category-list-el"></div>
                                            </div>
                                            <div class="manage-category-link">⚙️ Manage Category</div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="row mb-3 align-items-center">

                                <label class="col-md-2 d-flex align-items-center justify-content-between">
                                    <span>Amount<span style="color:red;">*</span></span>

                                    {{-- <a href="javascript:void(0)" id="btn-itemize"
                                    class="ms-2 d-inline-flex align-items-center text-primary"
                                    style="font-size:13px;">
                                    <i class="bi bi-list-ul me-1"></i> Itemize
                                </a> --}}
                                </label>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select class="custom-select" style="max-width:88px;" name="currency"
                                            id="pc-currency">
                                            @php $pcCur = old('currency', $pettycash->currency ?? 'INR'); @endphp
                                            <option value="INR" {{ $pcCur === 'INR' ? 'selected' : '' }}>INR</option>
                                            <option value="USD" {{ $pcCur === 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ $pcCur === 'EUR' ? 'selected' : '' }}>EUR</option>
                                        </select>
                                        <input type="number" class="form-control" id="pc-single-amount-input"
                                            placeholder="0.00" value="{{ $singleAmount }}" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>

                            @php
                                $gstTaxTypeVal = old('tax_type', isset($pettycash) ? $pettycash->tax_type ?? 'gst_not_applicable' : 'gst_not_applicable');
                                $gstSupplyVal = old('supply_kind', isset($pettycash) ? $pettycash->supply_kind ?? 'service' : 'service');
                                $gstStates = [
                                    'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Andaman and Nicobar Islands','Chandigarh','Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry',
                                ];
                            @endphp
                            <div class="row mb-3 pc-single-gst-field {{ $singleBlockHidden }}" id="pc-gst-block">
                                <label for="pc-tax-type" class="col-md-2">Tax Type<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-4">
                                    <select class="form-select" name="tax_type" id="pc-tax-type">
                                        <option value="gst_not_applicable" @selected($gstTaxTypeVal === 'gst_not_applicable')>GST Not Applicable</option>
                                        <option value="outside_scope" @selected($gstTaxTypeVal === 'outside_scope')>Outside the scope of GST</option>
                                        <option value="domestic_expense" @selected($gstTaxTypeVal === 'domestic_expense')>Domestic expense</option>
                                        <option value="import" @selected($gstTaxTypeVal === 'import')>Import</option>
                                    </select>
                                    <span class="pc-field-hint">GST treatment for this expense (India).</span>
                                </div>
                            </div>

                            <div class="row mb-3 pc-single-gst-field pc-gst-extended {{ $singleBlockHidden }} d-none" id="pc-gst-extended">
                                <label class="col-md-2 pt-1">Supply type</label>
                                <div class="col-md-4">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="supply_kind"
                                            id="pc-supply-goods" value="goods" @checked($gstSupplyVal === 'goods')>
                                        <label class="form-check-label" for="pc-supply-goods">Goods</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="supply_kind"
                                            id="pc-supply-service" value="service" @checked($gstSupplyVal !== 'goods')>
                                        <label class="form-check-label" for="pc-supply-service">Service</label>
                                    </div>
                                </div>
                                <label for="pc-destination-supply" class="col-md-2">Destination of supply<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-4">
                                    <select class="form-select" name="destination_of_supply" id="pc-destination-supply">
                                        <option value="">Select state / UT</option>
                                        @foreach ($gstStates as $st)
                                            <option value="{{ $st }}" @selected(old('destination_of_supply', $pettycash->destination_of_supply ?? '') === $st)>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- <div class="row mb-3 pc-single-gst-field pc-gst-extended {{ $singleBlockHidden }} d-none" id="pc-gst-row-gstin">
                                <label for="pc-gstin" class="col-md-2">GSTIN</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control text-uppercase" name="gstin" id="pc-gstin"
                                        maxlength="20" placeholder="e.g. 27AAAAA0000A1Z5"
                                        value="{{ old('gstin', $pettycash->gstin ?? '') }}">
                                </div>
                            </div> --}}

                            {{-- <div class="row mb-3 pc-single-gst-field pc-gst-extended {{ $singleBlockHidden }} d-none" id="pc-gst-row-reverse">
                                <label class="col-md-2"></label>
                                <div class="col-md-8">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="reverse_charge"
                                            id="pc-reverse-charge" value="1"
                                            {{ old('reverse_charge', isset($pettycash) && ($pettycash->reverse_charge ?? false)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pc-reverse-charge">Is Reverse charge
                                            applicable?</label>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="row mb-3 pc-single-gst-field pc-gst-extended {{ $singleBlockHidden }} d-none" id="pc-gst-row-dest">

                            </div>

                            {{-- <div class="row mb-3 pc-single-gst-field pc-gst-extended {{ $singleBlockHidden }} d-none" id="pc-gst-row-tax">
                                <label for="pc-gst-tax" class="col-md-2">Tax<span class="text-danger">*</span></label>
                                <div class="col-md-4">
                                    <select class="form-select" name="gst_tax_label" id="pc-gst-tax">
                                        @php $gtax = old('gst_tax_label', $pettycash->gst_tax_label ?? ''); @endphp
                                        <option value="">Select</option>
                                        <option value="Exempt" @selected($gtax === 'Exempt')>Exempt</option>
                                        <option value="0%" @selected($gtax === '0%')>0%</option>
                                        <option value="5%" @selected($gtax === '5%')>5%</option>
                                        <option value="12%" @selected($gtax === '12%')>12%</option>
                                        <option value="18%" @selected($gtax === '18%')>18%</option>
                                        <option value="28%" @selected($gtax === '28%')>28%</option>
                                        <option value="Composition" @selected($gtax === 'Composition')>Composition</option>
                                    </select>
                                    <div class="small text-muted mt-1 d-none" id="pc-reverse-charge-hint">Reverse charge
                                    </div>
                                </div>
                                <label for="pc-sac-hsn" class="col-md-2"><span id="pc-sac-hsn-label">SAC</span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="sac_hsn" id="pc-sac-hsn"
                                        maxlength="64" placeholder="Service / HSN code"
                                        value="{{ old('sac_hsn', $pettycash->sac_hsn ?? '') }}">
                                </div>
                            </div> --}}

                            <div class="row mb-3 pc-gst-extended {{ $singleBlockHidden }} d-none" id="pc-gst-row-sac">

                            </div>

                            <div class="row mb-3">
                                <label for="pc-single-description" class="col-md-2">Description</label>
                                <div class="col-md-4">
                                    <textarea id="pc-single-description" name="description" class="form-control" rows="2"
                                        placeholder="Enter description...">{{ old('description', $pettycash->description ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="pc_reference_no_single" class="col-md-2 ">Reference#</label>
                                <div class="col-md-4">
                                    <input type="text" name="reference_no" id="pc_reference_no_single" class="form-control"
                                        value="{{ old('reference_no', $pettycash?->reference_no ?? '') }}">
                                </div>
                            </div>

                            <div class="row mb-3 pc-single-gst-field {{ $singleBlockHidden }}" id="pc-invoice-row">
                                <label for="invoice_no" class="col-md-2">Invoice#</label>
                                <div class="col-md-4">
                                    <input type="text" name="invoice_no" id="invoice_no" class="form-control"
                                        value="{{ old('invoice_no', $pettycash?->invoice_no ?? '') }}"
                                        placeholder="Vendor invoice number">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-md-2">Receipt / Attachment</label>

                                <div class="col-md-4">
                                    <div class="single-notes-upload-wrapper" id="single-upload-wrapper">
                                        <div class="notes-section" style="display:none;"></div>

                                        <div class="upload-section">
                                            <input type="file" id="pcSingleFileInput" name="receipt"
                                                accept="image/*,application/pdf" style="display:none;" />

                                            <input type="hidden" name="remove_receipt" id="pcSingleRemoveFlag"
                                                value="0">

                                            <div class="upload-box">
                                                <button type="button" class="upload-btn" id="pcSingleUploadTrigger">
                                                    📤 Upload File
                                                </button>
                                                <button type="button" class="upload-dropdown">▼</button>
                                            </div>

                                            <p class="upload-hint">
                                                You can upload 1 file, max 5 MB (JPG, PNG, PDF)
                                            </p>

                                            <ul class="file-list" id="pcSingleFileList"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="notes-upload-wrapper" id="single-upload-wrapper">
                                <div class="notes-section" style="display:none;"></div>
                                <div class="upload-section">
                                    <label class="upload-title">Receipt / Attachment</label>
                                    <input type="file" id="pcSingleFileInput" name="receipt"
                                        accept="image/*,application/pdf" style="display:none;" />
                                    <input type="hidden" name="remove_receipt" id="pcSingleRemoveFlag"
                                        value="0">

                                    <div class="upload-box">
                                        <button type="button" class="upload-btn" id="pcSingleUploadTrigger">📤
                                            Upload
                                            File</button>
                                        <button type="button" class="upload-dropdown">▼</button>
                                    </div>

                                    <p class="upload-hint">You can upload 1 file, max 5 MB (JPG, PNG, PDF)</p>
                                    <ul class="file-list" id="pcSingleFileList"></ul>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>

                @php
                    $itemizedViewHidden =
                        isset($pettycash) && ($pettycash->expense_type ?? 'single') === 'itemized' ? '' : 'd-none';
                @endphp
                <div id="itemizedExpenseView" class="{{ $itemizedViewHidden }}">
                    <div class="card" id="pc-itemized-card">
                        <div class="card-header">
                            <h3>Item Table</h3>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:scroll">
                                <table class="table item-table" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width:35%">EXPENSE CATEGORY</th>
                                            <th style="width:30%">DESCRIPTION</th>
                                            <th style="width:20%">AMOUNT</th>
                                            <th style="width:15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse (old('items', $pettycashItems->toArray()) as $index => $item)
                                            @php
                                                $item_category_id = is_array($item)
                                                    ? $item['expense_category_id'] ?? ''
                                                    : $item->expense_category_id ?? '';
                                                $item_category_name =
                                                    $categories->firstWhere('id', $item_category_id)->name ?? '';
                                                $item_description = is_array($item)
                                                    ? $item['description'] ?? ''
                                                    : $item->description ?? '';
                                                $item_amount = is_array($item)
                                                    ? $item['amount'] ?? 0
                                                    : $item->amount ?? 0;
                                            @endphp
                                            <tr class="item-row">
                                                <td>
                                                    <div class="tax-dropdown-wrapper category-section"
                                                        style="width:100%">

                                                        <input type="text"
                                                            class="form-control category-search-input"
                                                            placeholder="Select Category" autocomplete="off"
                                                            value="{{ $item_category_name }}" readonly>

                                                        <input type="hidden"
                                                            name="items[{{ $index ?? 0 }}][expense_category_id]"
                                                            class="category_id" value="{{ $item_category_id }}">
                                                        <input type="hidden"
                                                            name="items[{{ $index ?? 0 }}][category_name]"
                                                            class="category_name" value="{{ $item_category_name }}">

                                                        <div class="dropdown-menu tax-dropdown">
                                                            <div style="max-height:200px; overflow-y:auto;">
                                                                <div class="search-box p-2">
                                                                    <input type="text" placeholder="Search"
                                                                        class="inner-search form-control category-inner-search">
                                                                </div>
                                                                <div class="category-list"></div>
                                                            </div>
                                                            <div class="manage-category-link">⚙️ Manage Category</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><input type="text"
                                                        name="items[{{ $index }}][description]"
                                                        value="{{ $item_description }}" class="form-control" />
                                                </td>
                                                <td><input type="number" step="0.01" min="0"
                                                        name="items[{{ $index }}][amount]"
                                                        value="{{ $item_amount }}"
                                                        class="form-control item-amount" />
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm btn-success add-row">+</button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger remove-row">X</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="item-row">
                                                <td>
                                                    <div class="tax-dropdown-wrapper category-section"
                                                        style="width:100%">
                                                        <input type="text"
                                                            class="form-control category-search-input"
                                                            autocomplete="off" autocorrect="off"
                                                            name="items[0][category]" placeholder="Select Category"
                                                            readonly>
                                                        <input type="hidden" name="items[0][expense_category_id]"
                                                            class="category_id">
                                                        <input type="hidden" name="items[0][category_name]"
                                                            class="category_name">
                                                        <div class="dropdown-menu tax-dropdown">
                                                            <div style="max-height:200px; overflow-y:auto;">
                                                                <div class="search-box p-2">
                                                                    <input type="text" placeholder="Search"
                                                                        class="inner-search form-control category-inner-search">
                                                                </div>
                                                                <div class="category-list"></div>
                                                            </div>
                                                            <div class="manage-category-link">⚙️ Manage Category</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><input type="text" name="items[0][description]"
                                                        class="form-control" /></td>
                                                <td><input type="number" step="0.01" min="0"
                                                        name="items[0][amount]" class="form-control item-amount"
                                                        value="" placeholder="0.00" /></td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm btn-success add-row">+</button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger remove-row">X</button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold">Total ( ₹ )</td>
                                            <td>
                                                <input type="hidden" name="total_amount" id="totalAmount"
                                                    value="0">
                                                <input type="text" id="totalAmountDisplay" readonly value="0.00"
                                                    class="form-control form-control-sm text-end">
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>



                        </div>{{-- card-body --}}

                        <div class="notes-upload-wrapper">
                            <div class="notes-section">
                                <label for="pc_reference_no_itemized">Reference#</label>
                                <textarea id="pc_reference_no_itemized" class="notes-textarea" name="reference_no" placeholder="Enter reference...">{{ old('reference_no', $pettycash?->reference_no ?? '') }}</textarea>
                                <p class="note-hint">It will not be shown in PDF</p>
                            </div>

                            <div class="upload-section">
                                <label class="upload-title">Attach File(s) to Petty Cash</label>
                                <input type="file" id="fileInput" name="uploads[]" multiple
                                    style="display: none;" />
                                <input type="hidden" name="existing_files" id="existingFilesInput">
                                <div class="upload-box">
                                    <button type="button" class="upload-btn" id="uploadTrigger">📤 Upload
                                        File</button>
                                    <button type="button" class="upload-dropdown">▼</button>
                                </div>
                                <p class="upload-hint">You can upload a maximum of 5 files, 10MB each</p>
                                <ul class="file-list" id="fileList"></ul>
                            </div>
                        </div>


                    </div>{{-- card pc-itemized-card --}}
                </div>

                <div class="action-buttons">
                    @unless (isset($pettycash))
                        <button type="button" id="btnSaveClose" class="btn open-btn">Save</button>
                        {{-- <button type="button" id="btnSaveNew" class="btn draft-btn">Save and New</button> --}}
                        <a href="{{ route('superadmin.getpettycash') }}" class="btn cancel-btn">Cancel</a>
                    @else
                        <button type="submit" id="savePettyCash" class="btn open-btn">Save changes</button>
                        <a href="{{ route('superadmin.getpettycash') }}" class="btn cancel-btn">Cancel</a>
                    @endunless
                </div>

                @if (isset($pettycash))
                    </form>
                @else
                    </form>
            </div>{{-- tab-add-pc --}}

            <div class="tab-pane fade" id="tab-bulk-pc" role="tabpanel">
                <form id="bulkPettyCashForm" method="POST" action="{{ route('superadmin.savepettycashbulk') }}">
                    @csrf

                    <div class="container mt-4">
                        <div class="row mb-3">
                            <label for="report-search" class="col-md-2 ">Report<span
                                    style="color:red;">*</span></label>
                            <div class="col-md-4">
                                <div class="search-dropdown">
                                    <input type="text" id="bulk-report-search" class="form-control search-input"
                                        name="report_name" placeholder="Select" autocomplete="off"
                                        value="{{ old('report_name', isset($pettycash) ? $pettycash->report_search_display ?? ($pettycash->report->report_name ?? '') : '') }}">
                                    <div class="dropdown-menu" id="bulk-report-dropdown">
                                        <div class="search-box">
                                            <input type="text" placeholder="Search"
                                                class="inner-search form-control mb-2 report-inner-search">
                                        </div>
                                        <div class="report-list"></div>
                                        <div class="new-report-option">
                                            <div class="report-new-btn open-report-modal">
                                                <i class="fa fa-plus"></i> New Report
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="bulk-selected-report-id" name="report_id"
                                    value="{{ old('report_id', $pettycash?->report_id ?? '') }}">
                                <span class="error_report_name" style="color:red"></span>
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <label for="bulk_report_id" class="col-md-2">Report<span
                                    style="color:red;">*</span></label>
                            <div class="col-md-4">
                                <select name="report_id" id="bulk_report_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($reportsData as $rep)
                                        <option value="{{ $rep->id }}">{{ $rep->report_display }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}

                        <div class="row mb-3">
                            <label for="bulk_zone_select" class="col-md-2">Zones<span
                                    style="color:red;">*</span></label>
                            <div class="col-md-4">
                                <div class="tax-dropdown-wrapper account-section" style="width:300px">
                                    <input type="text" class="form-control bulk-zone-input" autocomplete="off"
                                        autocorrect="off" placeholder="Select a Zone" readonly>
                                    <input type="hidden" name="zone_id" id="bulk_zone_select" class="bulk_zone_id">
                                    <div class="dropdown-menu tax-dropdown">
                                        <div class="bulk-zone-list"></div>
                                    </div>
                                    <span class="error_bulk_zone" style="color:red;font-size:0.85rem;"></span>
                                </div>
                            </div>

                            <label class="col-md-2">Branch <span style="color:red;">*</span></label>
                            <div class="col-md-4">
                                <div class="tax-dropdown-wrapper account-section branch-section" style="max-width:300px">
                                    <input type="text"
                                        class="form-control bulk-branch-input dropdown-search-input"
                                        autocomplete="off" autocorrect="off"
                                        placeholder="Select Branch" readonly>
                                    <input type="hidden" name="branch_id" id="bulk_branch_select"
                                        class="bulk_branch_id">
                                        <div class="dropdown-menu tax-dropdown branch-menu">
                                        <div class="inner-search-container">
                                            <input type="text" class="inner-search"
                                                placeholder="Search Branch..." autocomplete="off">
                                        </div>
                                        <div class="d-flex justify-content-between p-2 border-bottom branch-actions" style="gap:8px;">
                                            <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                        </div>
                                        <div class="dropdown-list multiselect branch-list"></div>
                                    </div>
                                    <span class="error_bulk_branch"
                                        style="color:red;font-size:0.85rem;"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="bulk_company_select" class="col-md-2">Group of Company<span
                                    style="color:red;">*</span></label>
                            <div class="col-md-4">
                                <div class="tax-dropdown-wrapper company-section" style="width:300px">
                                    <input type="text" class="form-control bulk-company-input" autocomplete="off"
                                        autocorrect="off" placeholder="Select a Company" readonly>
                                    <input type="hidden" name="company_id" id="bulk_company_select"
                                        class="bulk_company_id">
                                    <div class="dropdown-menu tax-dropdown">
                                        <div class="bulk-company-list"></div>
                                    </div>
                                    <span class="error_bulk_company" style="color:red;font-size:0.85rem;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Bulk Entry Table</h3>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table item-table bulk-pc-table" id="bulkItemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width:54px;">RECEIPT</th>
                                            <th style="min-width:130px;">PETTY CASH DATE</th>
                                            <th style="min-width:170px;">MERCHANT</th>
                                            <th style="min-width:170px;">CATEGORY</th>
                                            <th style="min-width:110px;">AMOUNT</th>
                                            <th style="min-width:120px;">REFERENCE #</th>
                                            <th style="width:80px;" class="text-center">REIMBURSE</th>

                                            <th style="width:75px;" class="text-center">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bulkItemsBody">
                                        @for ($i = 0; $i < 5; $i++)
                                            <tr class="bulk-row">
                                                {{-- Receipt upload --}}
                                                <td class="text-center align-middle">
                                                    <label class="bulk-receipt-btn" title="Upload receipt">
                                                        <i class="bi bi-camera"></i>
                                                        <input type="file"
                                                            name="bulk_rows[{{ $i }}][receipt]"
                                                            class="bulk-receipt-input" accept="image/*,.pdf">
                                                    </label>
                                                    <img src="" alt="" class="bulk-receipt-thumb">
                                                </td>
                                                {{-- Date --}}
                                                <td>
                                                    <input type="text"
                                                        name="bulk_rows[{{ $i }}][expense_date]"
                                                        class="form-control bulk-datepicker" placeholder="dd/mm/yyyy"
                                                        autocomplete="off">
                                                </td>
                                                {{-- Merchant search-dropdown --}}
                                                <td>
                                                    <div class="bulk-search-wrapper">
                                                        <input type="text" class="form-control bulk-vendor-input"
                                                            placeholder="Search merchant..." autocomplete="off">
                                                        <input type="hidden"
                                                            name="bulk_rows[{{ $i }}][vendor_id]"
                                                            class="bulk-vendor-id">
                                                        <div class="bulk-dropdown-menu">
                                                            <div class="bulk-search-inner">
                                                                <input type="text"
                                                                    class="form-control form-control-sm bulk-vendor-search"
                                                                    placeholder="Type to search...">
                                                            </div>
                                                            <div class="bulk-dropdown-list bulk-vendor-list"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                {{-- Category search-dropdown --}}
                                                <td>
                                                    <div class="bulk-search-wrapper">
                                                        <input type="text" class="form-control bulk-category-input"
                                                            placeholder="Select category..." autocomplete="off"
                                                            readonly>
                                                        <input type="hidden"
                                                            name="bulk_rows[{{ $i }}][expense_category_id]"
                                                            class="bulk-category-id">
                                                        <div class="bulk-dropdown-menu">
                                                            <div class="bulk-search-inner">
                                                                <input type="text"
                                                                    class="form-control form-control-sm bulk-category-search"
                                                                    placeholder="Type to search...">
                                                            </div>
                                                            <div
                                                                class="bulk-dropdown-list bulk-category-list-row category">
                                                            </div>
                                                            <div class="manage-category-link">⚙️ Manage Category</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                {{-- Amount --}}
                                                <td>
                                                    <input type="number" step="0.01" min="0"
                                                        name="bulk_rows[{{ $i }}][amount]"
                                                        class="form-control bulk-amount" placeholder="0.00">
                                                </td>
                                                {{-- Reference --}}
                                                <td>
                                                    <input type="text"
                                                        name="bulk_rows[{{ $i }}][reference_no]"
                                                        class="form-control" placeholder="Ref #">
                                                </td>
                                                {{-- Reimburse --}}
                                                <td class="text-center align-middle">
                                                    <input type="checkbox"
                                                        name="bulk_rows[{{ $i }}][claim_reimbursement]"
                                                        class="form-check-input" value="1" checked
                                                        title="Claim reimbursement">
                                                </td>

                                                {{-- Actions --}}
                                                <td class="text-center align-middle">
                                                    <div class="bulk-action-btns">

                                                        <button type="button"
                                                            class="btn btn-sm btn-danger bulk-remove-row">X</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <button type="button" id="bulkAddRows" class="btn btn-outline-primary">
                                    + Add More Expenses
                                </button>
                            </div>
                            <div class="bulk-total-wrapper">
                                <div class="bulk-total-box">
                                    <div class="label">Grand Total ( ₹ )</div>
                                    <div class="value" id="bulkTotalDisplay">0.00</div>
                                </div>
                            </div>

                        </div>{{-- card-body --}}
                    </div>{{-- card --}}

                    <div class="action-buttons">
                        <button type="submit" class="btn open-btn" id="bulkSaveBtn">Save</button>
                        <a href="{{ route('superadmin.getpettycash') }}" class="btn cancel-btn">Cancel</a>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="tab-import-pc" role="tabpanel">
                <p class="text-muted">Import petty cash rows from the Excel template (grouped line
                    items).</p>
                <a href="{{ route('superadmin.pettycash.template') }}" class="btn btn-outline-primary mb-3"><i
                        class="bi bi-download"></i> Download
                    template</a>
                <form method="POST" action="{{ route('superadmin.pettycash.import') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Excel file</label>
                        <input type="file" name="file" class="form-control-file" accept=".xlsx,.xls,.csv"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Upload &amp; import</button>
                </form>
            </div>
        </div>{{-- tab-content --}}
        @endif
    </div>{{-- container --}}
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // ── Flatpickr date ──
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr('#expense_date', {
                dateFormat: 'd/m/Y',
                allowInput: true,
                defaultDate: document.getElementById('expense_date').value || null,
            });
        });
        const reportData = @json($reportsData);

        // DEFAULT LOAD — respect server-rendered expense_type on edit
        @php $jsExpenseType = $pettycash->expense_type ?? 'single'; @endphp
            (function() {
                var initType = '{{ $jsExpenseType }}';
                if (initType === 'itemized') {
                    $('#singleExpenseView').hide();
                    $('#itemizedExpenseView').show();
                    $('#singleExpenseView').find('[name="reference_no"]').prop('disabled', true);

                    // enable itemized
                    $('#itemizedExpenseView').find('[name="reference_no"]').prop('disabled', false);
                    $('#single-upload-wrapper').hide();
                } else {
                    $('#singleExpenseView').show();
                    $('#itemizedExpenseView').hide();
                    $('#singleExpenseView').find('[name="reference_no"]').prop('disabled', false);

                    // ❗ disable itemized
                    $('#itemizedExpenseView').find('[name="reference_no"]').prop('disabled', true);
                    $('#single-upload-wrapper').show();
                }
            })();

        // RADIO SWITCH
        function pcToggleSingleGstInputs() {
            var single = $('input[name="expense_type"]:checked').val() === 'single';
            $('.pc-single-gst-field').find('input, select, textarea').prop('disabled', !single);
        }

        function pcRefreshGstUi() {
            var tt = $('#pc-tax-type').val();
            var showExt = tt === 'domestic_expense' || tt === 'import';
            $('.pc-gst-extended').toggleClass('d-none', !showExt);
            $('#pc-reverse-charge-hint').toggleClass('d-none', !$('#pc-reverse-charge').is(':checked'));
            var sk = $('input[name="supply_kind"]:checked').val() || 'service';
            $('#pc-sac-hsn-label').text(sk === 'goods' ? 'HSN' : 'SAC');
        }

        $(document).on('change', '#pc-tax-type', pcRefreshGstUi);
        $(document).on('change', 'input[name="supply_kind"]', pcRefreshGstUi);
        $(document).on('change', '#pc-reverse-charge', function() {
            $('#pc-reverse-charge-hint').toggleClass('d-none', !$(this).is(':checked'));
        });

        $(document).on('change', '.expense-type-radio', function() {
            var value = $(this).val();
            if (value === 'itemized') {
                $('#singleExpenseView').hide();
                $('#itemizedExpenseView').show();
                $('#single-upload-wrapper').hide();
                $('#singleExpenseView').find('[name="reference_no"]').prop('disabled', true);
                $('#itemizedExpenseView').find('[name="reference_no"]').prop('disabled', false);
            } else {
                $('#singleExpenseView').show();
                $('#itemizedExpenseView').hide();
                $('#single-upload-wrapper').show();
                $('#singleExpenseView').find('[name="reference_no"]').prop('disabled', false);
                $('#itemizedExpenseView').find('[name="reference_no"]').prop('disabled', true);
            }
            pcToggleSingleGstInputs();
        });

        pcRefreshGstUi();
        pcToggleSingleGstInputs();

        (function() {
            var existingPath = @json($pettycash->receipt_path ?? '');
            window.pcSingleExisting = existingPath ? [existingPath] : [];
            window.pcSingleNew = [];

            function renderPcSingleReceipt() {
                var $list = $('#pcSingleFileList');
                $list.empty();
                $.each(window.pcSingleExisting, function(i, path) {
                    var filename = path.split('/').pop();
                    $list.append(
                        '<li class="documentclk" data-filetype="document"' +
                        ' data-files="' + path + '">' +
                        '<a href="/' + path + '" target="_blank"' +
                        ' style="cursor:pointer;color:#2563eb;">' + filename + '</a>' +
                        ' <span class="remove-pc-existing" data-index="' + i + '"' +
                        ' style="cursor:pointer;color:red;" title="Remove">❌</span>' +
                        '</li>'
                    );
                });

                // ── Newly selected file (not yet saved) ──
                $.each(window.pcSingleNew, function(i, file) {
                    $list.append(
                        '<li>' +
                        '<span style="color:#374151;">' + file.name + '</span>' +
                        ' <span class="remove-pc-new" data-index="' + i + '"' +
                        ' style="cursor:pointer;color:red;" title="Remove">❌</span>' +
                        '</li>'
                    );
                });

                // Keep remove_receipt flag in sync
                $('#pcSingleRemoveFlag').val(window.pcSingleExisting.length === 0 && existingPath ? '1' : '0');
            }

            // Initial render (shows existing file on edit page)
            renderPcSingleReceipt();

            // Open file picker
            $('#pcSingleUploadTrigger').on('click', function() {
                $('#pcSingleFileInput').click();
            });

            // New file selected
            $('#pcSingleFileInput').on('change', function(e) {
                var file = e.target.files[0];
                if (!file) return;
                window.pcSingleNew = [file];
                // Choosing a new file replaces the existing one visually
                window.pcSingleExisting = [];
                existingPath = '';
                renderPcSingleReceipt();
            });

            // Remove existing saved file
            $('#pcSingleFileList').on('click', '.remove-pc-existing', function() {
                window.pcSingleExisting = [];
                existingPath = '';
                renderPcSingleReceipt();
            });

            // Remove newly selected file
            $('#pcSingleFileList').on('click', '.remove-pc-new', function() {
                window.pcSingleNew = [];
                $('#pcSingleFileInput').val('');
                renderPcSingleReceipt();
            });

            // Click filename → open in new tab (existing files)
            $('#pcSingleFileList').on('click', '.documentclk', function(e) {
                if ($(e.target).is('.remove-pc-existing')) return;
                var path = $(this).data('files');
                if (path) window.open('/' + path, '_blank');
            });
        })();

        function reindexRows() {
            $('#itemsTable tbody tr').each(function(index) {

                $(this).find('.category_id')
                    .attr('name', 'items[' + index + '][expense_category_id]');

                $(this).find('.category_name')
                    .attr('name', 'items[' + index + '][category_name]');

                $(this).find('input[name*="[description]"]')
                    .attr('name', 'items[' + index + '][description]');

                $(this).find('.item-amount')
                    .attr('name', 'items[' + index + '][amount]');
            });
        }

        $(document).ready(function() {
            function updateTotal() {

                let total = 0;

                $('.item-amount').each(function() {
                    let value = parseFloat($(this).val());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });

                // hidden field
                $('#totalAmount').val(total.toFixed(2));

                // display field
                $('#totalAmountDisplay').val(total.toFixed(2));
            }

            $(document).on('click', '#pcCreateTabs a[data-toggle="tab"]', function(e) {
                e.preventDefault();
                var $a = $(this);
                var target = $a.attr('href');
                if (!target || target.indexOf('#') !== 0) {
                    return;
                }
                $('#pcCreateTabs .nav-link').removeClass('active');
                $a.addClass('active');
                $('#pcPettycashTabContent .tab-pane').removeClass('show active');
                $(target).addClass('show active');
            });

            if (typeof flatpickr !== 'undefined') {
                flatpickr('#expense_date', {
                    dateFormat: 'd/m/Y',
                    allowInput: true,
                    altInput: true,
                    altFormat: 'd/m/Y',
                    defaultDate: $('#expense_date').val() || null,
                });
            }



            updateTotal();

            $('#itemsTable').on('input', '.item-amount', function() {
                updateTotal();
            });

            // Shared render helper for search dropdowns
            function renderLookupList(containerSelector, items, query, rowClassPrefix) {
                const list = $(containerSelector);
                list.empty();
                const q = (query || '').toString().toLowerCase();
                let hasAnything = false;

                items.forEach(it => {
                    if (!q || it.name.toLowerCase().includes(q)) {
                        hasAnything = true;
                        list.append(
                            `<div class="${rowClassPrefix}-item p-2" data-id="${it.id}" data-name="${it.name}">${it.name}</div>`
                        );
                    }
                });

                if (!hasAnything) {
                    list.append(`<div class="${rowClassPrefix}-item p-2 text-muted">No result found</div>`);
                }
            }

            const vendorData = @json($vendors->map(fn($v) => ['id' => $v->id, 'name' => $v->display_name ?? ($v->name ?? '')]));
            const expenseTypeData = @json($expenseTypes->map(fn($t) => ['id' => $t->id, 'name' => $t->name]));
            const expenseCategoryData = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
            const zones = @json($zones);
            const companies = @json($companies);

            // Populate all category lists
            $('.category-list').each(function() {
                const $list = $(this);
                $list.empty();
                expenseCategoryData.forEach(item => {
                    $list.append(
                        `<div class="category-item p-2" data-id="${item.id}" data-name="${item.name}">${item.name}</div>`
                    );
                });
            });

            // Populate zones and companies — add tab + bulk tab
            zones.forEach(zone => {
                const zHtml = `<div data-id="${zone.id}">${zone.name}</div>`;
                $('.zone-list').append(zHtml);
                $('.bulk-zone-list').append(zHtml);
            });
            companies.forEach(company => {
                const cHtml = `<div data-id="${company.id}">${company.company_name}</div>`;
                $('.company-list').append(cHtml);
                $('.bulk-company-list').append(cHtml);
            });

            // Handler for category inner search (works on cloned body-level dropdown)
            $(document).on('input', '.category-inner-search', function() {
                const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                const $list = $dropdown.find('.category-list');
                const filter = $(this).val().toLowerCase();
                $list.find('.category-item').each(function() {
                    $(this).toggle($(this).text().toLowerCase().includes(filter));
                });
            });

            function renderVendorList(filter = '') {
                renderLookupList('#vendor-dropdown .vendor-list', vendorData, filter, 'vendor');
            }

            function renderExpenseTypeList(filter = '') {
                renderLookupList('#expense-type-dropdown .expense-type-list', expenseTypeData, filter,
                    'expense-type');
            }

            function renderExpenseCategoryList(filter = '') {
                renderLookupList('#expense-category-dropdown .expense-category-list', expenseCategoryData, filter,
                    'expense-category');
            }

            function renderCategoryList(listSelector, filter = '') {
                renderLookupList(listSelector, expenseCategoryData, filter, 'category');
            }

            $('#vendor-search').on('focus click', function() {
                $('#vendor-dropdown').show();
                renderVendorList($(this).val());
            });

            $('#expense-type-search').on('focus click', function() {
                $('#expense-type-dropdown').show();
                renderExpenseTypeList($(this).val());
            });

            $('#expense-category-search').on('focus click', function() {
                $('#expense-category-dropdown').show();
                renderExpenseCategoryList($(this).val());
            });

            // Clone-to-body pattern (matches quotation account dropdown)
            $(document).on('click', '.category-search-input', function(e) {
                e.stopPropagation();
                $('.dropdown-menu.tax-dropdown').hide();

                const $input = $(this);
                let $dropdown = $input.data('dropdown');

                if (!$dropdown) {
                    $dropdown = $input.siblings('.dropdown-menu').clone(true);
                    $('body').append($dropdown);
                    $input.data('dropdown', $dropdown);
                }

                $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
                $dropdown.data('row', $input.closest('tr'));

                const offset = $input.offset();
                $dropdown.css({
                    position: 'absolute',
                    top: offset.top + $input.outerHeight(),
                    left: offset.left,
                    width: $input.outerWidth(),
                    zIndex: 1050
                }).show();

                // Reset search
                $dropdown.find('.category-inner-search').val('');
                $dropdown.find('.category-item').show();
            });

            $('#vendor-dropdown .inner-search').on('input', function() {
                renderVendorList($(this).val());
            });

            $('#expense-type-dropdown .inner-search').on('input', function() {
                renderExpenseTypeList($(this).val());
            });

            $('#expense-category-dropdown .inner-search').on('input', function() {
                renderExpenseCategoryList($(this).val());
            });

            $(document).on('click', '.vendor-item[data-id]', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                $('#vendor-search').val(name);
                $('#selected-vendor-id').val(id);
                $('#vendor-dropdown').hide();
            });

            $(document).on('click', '.expense-type-item[data-id]', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                $('#expense-type-search').val(name);
                $('#selected-expense-type-id').val(id);
                $('#expense-type-dropdown').hide();
            });

            $(document).on('click', '.expense-category-item[data-id]', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                $('#expense-category-search').val(name);
                $('#selected-expense-category-id').val(id);
                $('#expense-category-dropdown').hide();
            });

            // FIXED Category Selection Handler
            $(document).on('click', '.category-item[data-id]', function(e) {
                e.stopPropagation();

                const id = $(this).data('id');
                const name = $(this).data('name');

                const $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
                let $wrapper = $dropdown.data('wrapper');

                // Fallback if wrapper not attached properly
                if (!$wrapper || !$wrapper.length) {
                    $wrapper = $dropdown.closest('.tax-dropdown-wrapper');
                }

                if ($wrapper && $wrapper.length) {
                    // Update visible field
                    $wrapper.find('.category-search-input').val(name);

                    // CRITICAL: Update hidden field
                    $wrapper.find('input.category_id').val(id);
                    $wrapper.find('input.category_name').val(name);

                    // Trigger change for validation to recognize the value
                    $wrapper.find('input.category_id').trigger('change');

                    console.log('Category selected → ID:', id, 'Name:', name); // For debugging
                } else {
                    console.warn('Wrapper not found for category selection');
                }

                // Hide dropdown
                $('.dropdown-menu.tax-dropdown').hide();

                // Clear error immediately
                $wrapper.find('.error-text').remove();
            });
            // Zone, Branch, Company handlers
            $(document).on('click', '.zone-search-input', function() {
                const $wrapper = $(this).closest('.account-section');
                $('.tax-dropdown').hide();
                $wrapper.find('.dropdown-menu').show();
            });

            // ── Branch (same structure as advances dashboard) ─────────────────
            $(document).on('click', '.dropdown-search-input', function (e) {
                e.stopPropagation();
                $('.dropdown-menu.tax-dropdown').hide();
                var $input    = $(this);
                var $dropdown = $input.data('dropdown');
                if (!$dropdown) {
                    $dropdown = $input.siblings('.dropdown-menu').clone(true);
                    $('body').append($dropdown);
                    $input.data('dropdown', $dropdown);
                }
                $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
                var offset = $input.offset();
                $dropdown.css({ position: 'absolute', top: offset.top + $input.outerHeight(), left: offset.left, width: $input.outerWidth(), zIndex: 9999 }).show();
                $dropdown.find('.inner-search').val('').focus();
            });

            $(document).on('keyup', '.inner-search', function () {
                var q = $(this).val().toLowerCase();
                $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
                });
            });

            function updateMultiSelection($dropdown) {
                var wrapper = $dropdown.data('wrapper');
                if (!wrapper) return;
                var items = [], ids = [];
                $dropdown.find('.dropdown-list.multiselect div.selected').each(function () {
                    items.push($(this).text().trim());
                    ids.push($(this).data('id'));
                });
                wrapper.find('.dropdown-search-input').val(items.join(', '));
                wrapper.find('input[type="hidden"]').val(ids.join(','));
                wrapper.find('input[type="hidden"]').trigger('click');
            }

            $(document).on('click', '.dropdown-list.multiselect div', function (e) {
                e.stopPropagation();
                $(this).toggleClass('selected');
                updateMultiSelection($(this).closest('.dropdown-menu'));
            });

            $(document).on('click', '.select-all', function (e) {
                e.stopPropagation();
                var $d = $(this).closest('.dropdown-menu');
                $d.find('.dropdown-list.multiselect div').addClass('selected');
                updateMultiSelection($d);
            });

            $(document).on('click', '.deselect-all', function (e) {
                e.stopPropagation();
                var $d = $(this).closest('.dropdown-menu');
                $d.find('.dropdown-list.multiselect div').removeClass('selected');
                updateMultiSelection($d);
            });

            $(document).on('click', '.company-search-input', function() {
                const $wrapper = $(this).closest('.company-section');
                $('.tax-dropdown').hide();
                $wrapper.find('.dropdown-menu').show();
            });

            $(document).on('click', '.zone-list div', function() {
                const id = $(this).data('id');
                const name = $(this).text().trim();
                const $wrap = $(this).closest('.tax-dropdown-wrapper');
                $wrap.find('.zone-search-input').val(name);
                $wrap.find('.zone_id').val(id).trigger('change');
                $('.tax-dropdown').hide();
            });

            $(document).on('click', '.company-list div', function() {
                const id = $(this).data('id');
                const name = $(this).text().trim();
                const $wrap = $(this).closest('.tax-dropdown-wrapper');
                $wrap.find('.company-search-input').val(name);
                $wrap.find('.company_id').val(id);
                $('.tax-dropdown').hide();
            });

            function loadBranchesForZone(zoneId, done) {
                if (!zoneId) {
                    if (typeof done === 'function') done();
                    return;
                }
                const formData = new FormData();
                formData.append('id', zoneId);
                $.ajax({
                    url: '{{ route('superadmin.getbranchfetch') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        const $list = $('#pettyCashForm .branch-section .branch-list');
                        $list.empty();
                        $('#pettyCashForm .branch-search-input').removeData('dropdown');
                        if (response.branch && response.branch.length > 0) {
                            response.branch.forEach(branch => {
                                $list.append(
                                    $('<div>').attr('data-id', branch.id).attr('data-value',
                                        branch.name).text(
                                        branch.name)
                                );
                            });
                        }
                        if (typeof done === 'function') done();
                    },
                    error: function() {
                        toastr.error('Failed to fetch branches');
                        if (typeof done === 'function') done();
                    }
                });
            }

            $('#pettyCashForm .zone_id').on('change', function() {
                $('#pettyCashForm .branch-search-input').val('').attr('readonly', true);
                $('#pettyCashForm .branch_id').val('');
                $('#pettyCashForm .branch-section .branch-list div').removeClass('selected');
                loadBranchesForZone($(this).val());
            });

            const pettyCashEditContext = @json($pettyCashEditContext ?? null);
            if (pettyCashEditContext && pettyCashEditContext.zone_id) {
                loadBranchesForZone(String(pettyCashEditContext.zone_id), function() {
                    const csv = pettyCashEditContext.branch_ids_csv || '';
                    const savedIds = csv.split(',').map(s => String(s).trim()).filter(Boolean);
                    $('#pettyCashForm .branch-section .branch-list div').each(function() {
                        const id = String($(this).data('id'));
                        if (savedIds.includes(id)) {
                            $(this).addClass('selected');
                        }
                    });
                    const names = [];
                    const ids = [];
                    $('#pettyCashForm .branch-section .branch-list div.selected').each(function() {
                        names.push($(this).text().trim());
                        ids.push($(this).data('id'));
                    });
                    if (names.length) {
                        $('#pettyCashForm .branch-search-input').val(names.join(', ')).attr('readonly',
                            true);
                        $('#pettyCashForm .branch_id').val(ids.join(','));
                    } else if (pettyCashEditContext.branch_name) {
                        $('#pettyCashForm .branch-search-input').val(pettyCashEditContext.branch_name).attr(
                            'readonly', true);
                        $('#pettyCashForm .branch_id').val(pettyCashEditContext.branch_ids_csv ||
                            pettyCashEditContext.branch_id || '');
                    }
                });
            }
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-dropdown').length &&
                    !$(e.target).closest('.tax-dropdown-wrapper').length &&
                    !$(e.target).closest('.category-section').length &&
                    !$(e.target).closest('.account-section').length &&
                    !$(e.target).closest('.company-section').length &&
                    !$(e.target).closest('.dropdown-menu.tax-dropdown').length) {
                    $('#vendor-dropdown').hide();
                    $('#expense-type-dropdown').hide();
                    $('#expense-category-dropdown').hide();
                    $('.dropdown-menu.tax-dropdown').hide();
                    $('.account-section .dropdown-menu').hide();
                    $('.company-section .dropdown-menu').hide();
                }
            });

            function appendItemRow() {
                const rowCount = $('#itemsTable tbody tr').length;
                const newRow = `<tr class="item-row">
                    <td>
                        <div class="tax-dropdown-wrapper category-section" style="width:100%">
                            <input type="text" class="form-control category-search-input" autocomplete="off" autocorrect="off" name="items[${rowCount}][category]" placeholder="Select Category" readonly>
                            <input type="hidden" name="items[${rowCount}][expense_category_id]" class="category_id">
                            <input type="hidden" name="items[${rowCount}][category_name]" class="category_name">
                            <div class="dropdown-menu tax-dropdown">
                                <div style="max-height:200px; overflow-y:auto;">
                                    <div class="search-box p-2">
                                        <input type="text" placeholder="Search Category" class="inner-search form-control category-inner-search">
                                    </div>
                                    <div class="category-list"></div>
                                </div>
                                <div class="manage-category-link">⚙️ Manage Category</div>
                            </div>
                        </div>
                    </td>
                    <td><input type="text" name="items[${rowCount}][description]" class="form-control" /></td>
                    <td><input type="number" step="0.01" min="0" name="items[${rowCount}][amount]" class="form-control item-amount" value="" placeholder="0.00" /></td>
                    <td>
    <button type="button" class="btn btn-sm btn-success add-row">+</button>
    <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
</td>
                </tr>`;
                $('#itemsTable tbody').append(newRow);
                // Populate the category list for the new row
                const $newList = $('#itemsTable tbody tr:last .category-list');
                $newList.empty();
                expenseCategoryData.forEach(item => {
                    $newList.append(
                        `<div class="category-item p-2" data-id="${item.id}" data-name="${item.name}">${item.name}</div>`
                    );
                });
                updateTotal();
                reindexRows();
            }

            $(document).on('click', '#pettyCashForm .add-row', function(e) {
                e.preventDefault();

                let $currentRow = $(this).closest('tr'); // current row
                let rowCount = $('#itemsTable tbody tr').length;

                const newRow = `<tr class="item-row">
        <td>
            <div class="tax-dropdown-wrapper category-section" style="width:100%">
                <input type="text" class="form-control category-search-input"
                    autocomplete="off" name="items[${rowCount}][category]" placeholder="Select Category" readonly>
                <input type="hidden" name="items[${rowCount}][expense_category_id]" class="category_id">
                <input type="hidden" name="items[${rowCount}][category_name]" class="category_name">
                <div class="dropdown-menu tax-dropdown">
                    <div style="max-height:200px; overflow-y:auto;">
                        <div class="search-box p-2">
                            <input type="text" placeholder="Search"
                                class="inner-search form-control category-inner-search">
                        </div>
                        <div class="category-list"></div>
                    </div>
                    <div class="manage-category-link">⚙️ Manage Category</div>
                </div>
            </div>
        </td>
        <td><input type="text" name="items[${rowCount}][description]" class="form-control" /></td>
        <td><input type="number" step="0.01" min="0"
                name="items[${rowCount}][amount]" class="form-control item-amount"
                value="" placeholder="0.00" /></td>
        <td>
            <button type="button" class="btn btn-sm btn-success add-row">+</button>
            <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
        </td>
    </tr>`;

                // 🔥 INSERT BELOW CURRENT ROW
                $currentRow.after(newRow);

                // Populate category list
                const $newList = $currentRow.next().find('.category-list');
                $newList.empty();
                expenseCategoryData.forEach(item => {
                    $newList.append(
                        `<div class="category-item p-2" data-id="${item.id}" data-name="${item.name}">${item.name}</div>`
                    );
                });

                updateTotal();
                reindexRows();
            });

            $('#itemsTable').on('click', '.remove-row', function() {
                const rowCount = $('.item-row').length;
                if (rowCount > 1) {
                    $(this).closest('tr').remove();
                    reindexRows();
                    updateTotal();
                } else {
                    alert('You must have at least one row');
                }
            });

            const pcIsEdit = @json(isset($pettycash));

            function syncSingleToFirstRow() {

                if (pcIsEdit) return;

                let expenseType = $('input[name="expense_type"]:checked').val();
                if (expenseType !== 'single') return;

                const $r = $('#itemsTable tbody tr.item-row').first();

                $r.find('.category_id').val($('#pc-single-category-id').val());
                $r.find('.category_name').val($('#pc-single-category-name').val());
                $r.find('.category-search-input').val($('#pc-single-category-input').val());

                const amt = $('#pc-single-amount-input').val();
                $r.find('.item-amount').val(amt !== '' ? amt : '');
                $r.find('input[name*="[description]"]').val($('#pc-single-description').val());
            }

            function syncFirstRowToSingle() {
                const $r = $('#itemsTable tbody tr.item-row').first();
                $('#pc-single-category-id').val($r.find('.category_id').val());
                $('#pc-single-category-name').val($r.find('.category_name').val());
                $('#pc-single-category-input').val($r.find('.category-search-input').val());
                $('#pc-single-amount-input').val($r.find('.item-amount').val());
            }
            // ITEMIZE CLICK (delegated)
            $(document).on('click', '#btn-itemize', function(e) {
                e.preventDefault();

                console.log('Itemize clicked');

                syncSingleToFirstRow();

                $('#singleExpenseView').hide(); // 🔥 use hide()
                $('#itemizedExpenseView').show(); // 🔥 use show()
                $('#singleExpenseView').find('[name="reference_no"]').prop('disabled', true);
                $('#itemizedExpenseView').find('[name="reference_no"]').prop('disabled', false);

                $('#pc-itemized-block').show();
            });

            $(document).on('click', '#btn-back-single', function(e) {
                e.preventDefault();

                syncFirstRowToSingle();

                $('#singleExpenseView').show();
                $('#itemizedExpenseView').hide();
                $('#singleExpenseView').find('[name="reference_no"]').prop('disabled', false);
                $('#itemizedExpenseView').find('[name="reference_no"]').prop('disabled', true);
                $('#pc-itemized-block').hide();
            });



            $(document).on('input', '#pc-single-amount-input', function() {
                if (!$('#pc-single-expense-block').hasClass('d-none')) {
                    $('#itemsTable tbody tr.item-row').first().find('.item-amount').val($(this).val() || 0);
                    updateTotal();
                }
            });

            $('#btnSaveClose, #btnSaveNew').on('click', function() {

                $('#save_action').val(this.id === 'btnSaveNew' ? 'new' : 'close');

                let expenseType = $('input[name="expense_type"]:checked').val();

                // ✅ ONLY for single
                if (expenseType === 'single') {
                    syncSingleToFirstRow();
                }

                $('#pettyCashForm').trigger('submit');
            });

            // ===== BULK ZONE / COMPANY DROPDOWNS (branch uses floating panel like dashboard) =====
            $(document).on('click', '.bulk-zone-input, .bulk-company-input', function(e) {
                e.stopPropagation();
                const $dropdown = $(this).closest('.tax-dropdown-wrapper').find('.tax-dropdown');
                $('.tax-dropdown').not($dropdown).hide();
                $dropdown.toggle();
            });

            // Zone selected → populate branch list via AJAX
            $(document).on('click', '.bulk-zone-list div', function(e) {
                e.stopPropagation();
                const id = $(this).data('id');
                const name = $(this).text().trim();
                const $wrap = $(this).closest('.tax-dropdown-wrapper');
                $wrap.find('.bulk-zone-input').val(name);
                $wrap.find('.bulk_zone_id').val(id);
                $wrap.find('.tax-dropdown').hide();

                const $bulkForm = $('#bulkPettyCashForm');
                const $list = $bulkForm.find('.branch-section .branch-list');
                $list.empty();
                $bulkForm.find('.bulk-branch-input').removeData('dropdown');
                $bulkForm.find('.bulk-branch-input').val('').attr('placeholder', 'Loading branches...');
                $bulkForm.find('.bulk_branch_id').val('');
                $bulkForm.find('.branch-section .branch-list div').removeClass('selected');

                if (!id) {
                    $bulkForm.find('.bulk-branch-input').attr('placeholder', 'Select Branch');
                    return;
                }
                $.post(
                    '{{ route('superadmin.getbranchfetch') }}', {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content') || $(
                                'input[name="_token"]')
                            .val()
                    },
                    function(res) {
                        $list.empty();
                        $bulkForm.find('.bulk-branch-input').attr('placeholder', 'Select Branch');
                        (res.branch || []).forEach(function(b) {
                            $list.append($('<div>').attr('data-id', b.id).attr('data-value', b
                                .name).text(b
                                .name));
                        });
                    }
                );
            });

            // Company selected
            $(document).on('click', '.bulk-company-list div', function(e) {
                e.stopPropagation();
                const $wrap = $(this).closest('.tax-dropdown-wrapper');
                $wrap.find('.bulk-company-input').val($(this).text().trim());
                $wrap.find('.bulk_company_id').val($(this).data('id'));
                $wrap.find('.tax-dropdown').hide();
            });

            // ===== BULK ROW — VENDOR (MERCHANT) INLINE SEARCH =====
            $(document).on('click', '.bulk-vendor-input', function(e) {
                e.stopPropagation();
                const $wrapper = $(this).closest('.bulk-search-wrapper');
                $('.bulk-dropdown-menu').not($wrapper.find('.bulk-dropdown-menu')).hide();
                $wrapper.find('.bulk-dropdown-menu').show();
                // Populate list if empty
                const $list = $wrapper.find('.bulk-vendor-list');
                if ($list.children().length === 0) {
                    vendorData.forEach(function(v) {
                        $list.append('<div data-id="' + v.id + '">' + v.name + '</div>');
                    });
                }
                $wrapper.find('.bulk-vendor-search').val('').focus();
            });
            // Filter vendor list on typing in search
            $(document).on('input', '.bulk-vendor-search', function() {
                const filter = $(this).val().toLowerCase();
                const $list = $(this).closest('.bulk-dropdown-menu').find('.bulk-vendor-list');
                $list.children().each(function() {
                    $(this).toggle($(this).text().toLowerCase().includes(filter));
                });
            });
            // Vendor item clicked
            $(document).on('click', '.bulk-vendor-list div', function(e) {
                e.stopPropagation();
                const $wrapper = $(this).closest('.bulk-search-wrapper');
                $wrapper.find('.bulk-vendor-input').val($(this).text().trim());
                $wrapper.find('.bulk-vendor-id').val($(this).data('id'));
                $wrapper.find('.bulk-dropdown-menu').hide();
            });

            // ===== BULK ROW — CATEGORY INLINE SEARCH =====
            $(document).on('click', '.bulk-category-input', function(e) {
                e.stopPropagation();
                const $wrapper = $(this).closest('.bulk-search-wrapper');
                $('.bulk-dropdown-menu').not($wrapper.find('.bulk-dropdown-menu')).hide();
                $wrapper.find('.bulk-dropdown-menu').show();
                const $list = $wrapper.find('.bulk-category-list-row');
                if ($list.children().length === 0) {
                    expenseCategoryData.forEach(function(c) {
                        $list.append('<div data-id="' + c.id + '">' + c.name + '</div>');
                    });
                }
                $wrapper.find('.bulk-category-search').val('').focus();
            });
            $(document).on('input', '.bulk-category-search', function() {
                const filter = $(this).val().toLowerCase();
                const $list = $(this).closest('.bulk-dropdown-menu').find('.bulk-category-list-row');
                $list.children().each(function() {
                    $(this).toggle($(this).text().toLowerCase().includes(filter));
                });
            });
            $(document).on('click', '.bulk-category-list-row div', function(e) {
                e.stopPropagation();
                const $wrapper = $(this).closest('.bulk-search-wrapper');
                $wrapper.find('.bulk-category-input').val($(this).text().trim());
                $wrapper.find('.bulk-category-id').val($(this).data('id'));
                $wrapper.find('.bulk-dropdown-menu').hide();
            });

            // ===== RECEIPT UPLOAD PREVIEW =====
            $(document).on('change', '.bulk-receipt-input', function() {
                const file = this.files[0];
                if (!file) return;
                const $td = $(this).closest('td');
                const $btn = $td.find('.bulk-receipt-btn');
                const $img = $td.find('.bulk-receipt-thumb');
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        $img.attr('src', ev.target.result).show();
                        $btn.hide();
                    };
                    reader.readAsDataURL(file);
                } else {
                    // PDF — show an icon placeholder instead
                    $btn.html(
                        '<i class="bi bi-file-earmark-pdf text-danger"></i><input type="file" name="' +
                        $(this).attr('name') + '" class="bulk-receipt-input" accept="image/*,.pdf">');
                }
            });
            // Click thumbnail to change file
            $(document).on('click', '.bulk-receipt-thumb', function() {
                $(this).closest('td').find('.bulk-receipt-input').trigger('click');
            });

            // ===== BULK ROW TOTAL (per-row) =====
            function updateBulkRowTotal($row) {
                const v = parseFloat($row.find('.bulk-amount').val()) || 0;
                $row.find('.bulk-row-total').text('₹' + v.toFixed(2));
            }

            function updateBulkTotal() {
                let total = 0;
                $('#bulkItemsBody tr.bulk-row').each(function() {
                    const v = parseFloat($(this).find('.bulk-amount').val()) || 0;
                    total += v;
                    updateBulkRowTotal($(this));
                });
                $('#bulkTotalDisplay').text(total.toFixed(2));
            }
            $(document).on('input', '#bulkItemsBody .bulk-amount', function() {
                updateBulkRowTotal($(this).closest('tr'));
                updateBulkTotal();
            });
            updateBulkTotal();

            // ===== REINDEX =====
            function reindexBulkRows() {
                $('#bulkItemsBody tr.bulk-row').each(function(i) {
                    $(this).find('input, select').each(function() {
                        const n = $(this).attr('name');
                        if (!n || n.indexOf('bulk_rows[') !== 0) return;
                        $(this).attr('name', n.replace(/bulk_rows\[\d+\]/, 'bulk_rows[' + i + ']'));
                    });
                });
            }

            // ===== ADD ROW (per-row "+" button) =====
            function appendBulkRow($afterRow) {
                const rowCount = $('#bulkItemsBody tr.bulk-row').length;
                const newRow = `<tr class="bulk-row">
                    <td class="text-center align-middle">
                        <label class="bulk-receipt-btn" title="Upload receipt">
                            <i class="bi bi-camera"></i>
                            <input type="file" name="bulk_rows[${rowCount}][receipt]" class="bulk-receipt-input" accept="image/*,.pdf">
                        </label>
                        <img src="" alt="" class="bulk-receipt-thumb">
                    </td>
                    <td><input type="text" name="bulk_rows[${rowCount}][expense_date]" class="form-control bulk-datepicker" placeholder="dd/mm/yyyy" autocomplete="off"></td>
                    <td>
                        <div class="bulk-search-wrapper">
                            <input type="text" class="form-control bulk-vendor-input" placeholder="Search merchant..." autocomplete="off">
                            <input type="hidden" name="bulk_rows[${rowCount}][vendor_id]" class="bulk-vendor-id">
                            <div class="bulk-dropdown-menu">
                                <div class="bulk-search-inner"><input type="text" class="form-control form-control-sm bulk-vendor-search" placeholder="Type to search..."></div>
                                <div class="bulk-dropdown-list bulk-vendor-list"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="bulk-search-wrapper">
                            <input type="text" class="form-control bulk-category-input" placeholder="Select category..." autocomplete="off" readonly>
                            <input type="hidden" name="bulk_rows[${rowCount}][expense_category_id]" class="bulk-category-id">
                            <div class="bulk-dropdown-menu">
                                <div class="bulk-search-inner"><input type="text" class="form-control form-control-sm bulk-category-search" placeholder="Type to search..."></div>
                                <div class="bulk-dropdown-list bulk-category-list-row"></div>
                            </div>
                        </div>
                    </td>
                    <td><input type="number" step="0.01" min="0" name="bulk_rows[${rowCount}][amount]" class="form-control bulk-amount" placeholder="0.00"></td>
                    <td><input type="text" name="bulk_rows[${rowCount}][reference_no]" class="form-control" placeholder="Ref #"></td>
                    <td class="text-center align-middle"><input type="checkbox" name="bulk_rows[${rowCount}][claim_reimbursement]" class="form-check-input" value="1" checked></td>

                    <td class="text-center align-middle">

                        <button type="button" class="btn btn-sm btn-danger bulk-remove-row" title="Remove row">X</button>
                    </td>
                </tr>`;
                const $newRow = $(newRow);
                if ($afterRow && $afterRow.length) {
                    $afterRow.after($newRow);
                } else {
                    $('#bulkItemsBody').append($newRow);
                }
                reindexBulkRows();
                if (typeof flatpickr !== 'undefined') {
                    $newRow.find('.bulk-datepicker').each(function() {
                        flatpickr(this, {
                            dateFormat: 'd/m/Y',
                            allowInput: true
                        });
                    });
                }
            }

            $('#bulkAddRows').on('click', function() {

                let $lastRow = $('#bulkItemsBody tr.bulk-row:last');

                for (let i = 0; i < 5; i++) {
                    appendBulkRow($lastRow);
                    $lastRow = $lastRow.next();
                }

                updateBulkTotal();
            });

            // Close bulk dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.bulk-search-wrapper').length) {
                    $('.bulk-dropdown-menu').hide();
                }
                if (!$(e.target).closest('#tab-bulk-pc .tax-dropdown-wrapper').length) {
                    $('#tab-bulk-pc .tax-dropdown').hide();
                }
            });

            $('#bulkPettyCashForm').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                $('.error_bulk_zone, .error_bulk_branch, .error_bulk_company').text('');
                if (!$form.find('.bulk_zone_id').val()) {
                    $form.find('.error_bulk_zone').text('Zone is required');
                    toastr.error('Please select a zone.');
                    return;
                }
                if (!$form.find('.bulk_branch_id').val()) {
                    $form.find('.error_bulk_branch').text('At least one branch is required');
                    toastr.error('Please select at least one branch.');
                    return;
                }
                if (!$form.find('.bulk_company_id').val()) {
                    $form.find('.error_bulk_company').text('Company is required');
                    toastr.error('Please select a company.');
                    return;
                }
                if (!$form.find('#bulk-selected-report-id').val()) {
                    toastr.error('Please select a report.');
                    return;
                }
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        toastr.success(response.message || 'Saved');
                        setTimeout(function() {
                            window.location = '{{ route('superadmin.getpettycash') }}';
                        }, 900);
                    },
                    error: function(xhr) {
                        const err = xhr.responseJSON;
                        const msg = err && err.message ? err.message : (err && err.errors ? JSON
                            .stringify(err.errors) : 'Could not save bulk entries.');
                        toastr.error(msg);
                    }
                });
            });

            if (typeof flatpickr !== 'undefined') {
                $('.bulk-datepicker').each(function() {
                    flatpickr(this, {
                        dateFormat: 'd/m/Y',
                        allowInput: true
                    });
                });
            }



            $('#pettyCashForm').on('submit', function(e) {
                e.preventDefault();
                let expenseType = $('input[name="expense_type"]:checked').val();

                // ✅ ONLY sync for SINGLE
                if (expenseType === 'single') {
                    syncSingleToFirstRow();
                }
                if (!validateForm()) {
                    return; // STOP API CALL
                }
                const form = $(this);

                // Use FormData so the receipt file is included in the request
                const formData = new FormData(form[0]);

                // Attach newly selected receipt file explicitly (in case browser needs it)
                const receiptFile = $('#pcSingleFileInput')[0];
                if (receiptFile && receiptFile.files.length > 0) {
                    formData.set('receipt', receiptFile.files[0]);
                }

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.message || 'Saved successfully');
                        if (response.save_and_new) {
                            window.location = '{{ route('superadmin.getpettycashcreate') }}';
                            return;
                        }
                        setTimeout(function() {
                            window.location = '{{ route('superadmin.getpettycash') }}';
                        }, 1200);
                    },
                    error: function(xhr) {

                        const errors = xhr.responseJSON.errors;

                        // REMOVE OLD ERRORS
                        $('.error-text').remove();

                        if (errors) {

                            Object.keys(errors).forEach(function(key) {

                                let message = errors[key][0];

                                // MATCH items.0.amount
                                let match = key.match(/items\.(\d+)\.(\w+)/);

                                if (match) {
                                    let rowIndex = match[1];
                                    let field = match[2];

                                    let row = $('#itemsTable tbody tr').eq(rowIndex);

                                    if (field === 'amount') {
                                        row.find('.item-amount')
                                            .after(
                                                '<span class="error-text" style="color:red;">Amount is required</span>'
                                            );
                                    }

                                    if (field === 'expense_category_id') {
                                        row.find('.category-search-input')
                                            .after(
                                                '<span class="error-text" style="color:red;">Category is required</span>'
                                            );
                                    }
                                }

                                // NORMAL FIELDS
                                if (key === 'report_id') {
                                    $('#report-search').after(
                                        '<span class="error-text" style="color:red;">Report is required</span>'
                                    );
                                }

                                if (key === 'expense_date') {
                                    $('[name="expense_date"]').after(
                                        '<span class="error-text" style="color:red;">Date is required</span>'
                                    );
                                }

                            });

                            toastr.error('Please fix the highlighted fields');
                        }
                    }
                });
            });

            function validateForm() {

                let isValid = true;
                $('.error-text').remove(); // Clear previous errors

                // Basic fields
                if (!$('#selected-report-id').val()) {
                    $('#report-search').after(
                        '<span class="error-text" style="color:red;">Report is required</span>');
                    isValid = false;
                }

                if (!$('[name="expense_date"]').val()) {
                    $('.error_expense_date').text('Petty cash date is required');
                    isValid = false;
                }

                if (!$('#pettyCashForm .zone_id').val()) {
                    $('#pettyCashForm .zone-search-input').first().after(
                        '<span class="error-text" style="color:red;">Zone is required</span>');
                    isValid = false;
                }
                if (!$('#pettyCashForm .branch_id').val()) {
                    $('#pettyCashForm .branch-search-input').first().after(
                        '<span class="error-text" style="color:red;">At least one branch is required</span>');
                    isValid = false;
                }
                if (!$('#pettyCashForm .company_id').val()) {
                    $('#pettyCashForm .company-search-input').first().after(
                        '<span class="error-text" style="color:red;">Company is required</span>');
                    isValid = false;
                }

                let expenseType = $('input[name="expense_type"]:checked').val() || 'itemized';

                if (expenseType === 'single') {
                    // Single expense validation (keep as is)
                    let category = $('#pc-single-category-id').val();
                    let amount = $('#pc-single-amount-input').val();
                    let parsedAmount = parseFloat(amount);

                    if (!category) {
                        $('#pc-single-category-input').after(
                            '<span class="error-text" style="color:red;">Category is required</span>');
                        isValid = false;
                    }
                    if (isNaN(parsedAmount) || parsedAmount <= 0) {
                        $('#pc-single-amount-input').closest('.input-group').after(
                            '<span class="error-text d-block mt-1" style="color:red;">Amount is required</span>'
                        );
                        isValid = false;
                    }

                    var tt = $('#pc-tax-type').val();
                    if (tt === 'domestic_expense' || tt === 'import') {
                        if (!$('#pc-destination-supply').val()) {
                            $('#pc-destination-supply').after(
                                '<span class="error-text d-block mt-1" style="color:red;">Destination of supply is required</span>'
                            );
                            isValid = false;
                        }
                        if (!$('#pc-gst-tax').val()) {
                            $('#pc-gst-tax').after(
                                '<span class="error-text d-block mt-1" style="color:red;">Tax is required</span>'
                            );
                            isValid = false;
                        }
                    }
                } else if (expenseType === 'itemized') {

                    let hasValidRow = false;

                    $('#itemsTable tbody tr.item-row').each(function() {
                        let $row = $(this);
                        let categoryId = $row.find('input.category_id').val()?.trim() || '';
                        let amountStr = $row.find('.item-amount').val()?.trim() || '';
                        let parsedAmount = parseFloat(amountStr);

                        // Skip empty rows
                        if (!categoryId && (!amountStr || isNaN(parsedAmount) || parsedAmount <= 0)) {
                            return;
                        }

                        hasValidRow = true;

                        if (!categoryId) {
                            $row.find('.category-search-input').after(
                                '<span class="error-text" style="color:red; display:block; margin-top:4px;">Category is required</span>'
                            );
                            isValid = false;
                        }

                        if (isNaN(parsedAmount) || parsedAmount <= 0) {
                            $row.find('.item-amount').closest('td').append(
                                '<span class="error-text d-block mt-1" style="color:red;">Amount is required</span>'
                            );
                            isValid = false;
                        }
                    });

                    if (!hasValidRow) {
                        toastr.error('Please enter at least one valid item');
                        isValid = false;
                    }
                }

                return isValid;
            }

            $(document).on('input', '.item-amount', function() {
                if ($(this).val() > 0) {
                    $(this).siblings('.error-text').remove();
                }
            });

            $('#report-search').on('focus click', function() {
                $('#report-dropdown').show();
                renderReportList('');
            });

            $(document).on('input', '.report-inner-search', function() {
                renderReportList($(this).val());
            });

            function renderReportList(filter = '') {

                const list = $('#report-dropdown .report-list');
                list.empty();

                const q = filter.toLowerCase();

                reportData.forEach(r => {

                    const text = r.report_display;

                    if (!q || text.toLowerCase().includes(q)) {

                        const reportId = text.split(' - ')[0];
                        const reportName = text.split(' - ')[1]?.split(' (')[0] ?? '';
                        const reportDate = text.match(/\((.*?)\)/)?.[1] ?? '';

                        list.append(`
                <div class="report-item-card"
                     data-id="${r.id}"
                     data-name="${text}">

                    <!-- TOP ROW -->
                    <div class="report-top">
                        <span class="report-id">${reportId}</span>
                        <span class="report-amount">₹300.00</span>
                    </div>

                    <!-- NAME -->
                    <div class="report-name">
                        ${reportName}
                    </div>

                    <!-- DATE -->
                    <div class="report-date">
                        ${reportDate}
                    </div>

                </div>
            `);
                    }
                });

                if (!list.children().length) {
                    list.append(`<div class="p-2 text-muted">No report found</div>`);
                }
            }

            // + New Report → superadmin expense report creation page
            $(document).on('click', '.open-report-modal', function(e) {
                e.preventDefault();
                e.stopPropagation();
                window.location.href = @json(route('superadmin.expensereport.index'));
            });

            // 🔹 CLOSE DROPDOWN OUTSIDE CLICK
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-dropdown').length) {
                    $('#report-dropdown').hide();
                }
            });
            $(document).on('click', '.report-item-card', function() {
                const id = $(this).data('id');
                const fullText = $(this).data('name');

                let reportName = fullText.split(' - ')[1]?.split(' (')[0] ?? '';

                $('#report-search').val(reportName);
                $('#selected-report-id').val(id);
                $('#report-dropdown').hide();
            });

            // Open file input on upload button click
            $('#uploadTrigger').on('click', function() {
                $('#fileInput').click();
            });

            // On file input change
            $('#fileInput').on('change', function() {
                const fileList = $('#fileList');
                fileList.empty();
                const files = Array.from(this.files);
                window.selectedFiles = files;

                $.each(files, function(index, file) {
                    const li = $(`
                      <li>
                        <span class="file-name" data-index="${index}" style="cursor:pointer; color:blue;">
                          ${file.name}
                        </span>
                        <span class="remove-file" data-index="${index}"> ❌</span>
                      </li>
                    `);
                    fileList.append(li);
                });
            });

            // Remove file from list visually
            $('#fileList').on('click', '.remove-file', function() {
                const index = $(this).data('index');
                window.selectedFiles.splice(index, 1);

                // Rebuild the list after removal
                $('#fileInput').val('');
                const newFileList = window.selectedFiles;
                $('#fileList').empty();

                $.each(newFileList, function(i, file) {
                    const li = $(`
                      <li>
                        <span class="file-name" data-index="${i}" style="cursor:pointer; color:blue; text-decoration:underline;">
                          ${file.name}
                        </span>
                        <span class="remove-file" data-index="${i}"> ❌</span>
                      </li>
                    `);
                    $('#fileList').append(li);
                });
            });
            // When clicking file name -> open your modal and preview
            $('#fileList').on('click', '.file-name', function() {
                const index = $(this).data('index');
                const file = window.selectedFiles[index];
                if (!file) return;

                const reader = new FileReader();

                reader.onload = function(e) {
                    const fileURL = e.target.result;
                    let previewHTML = '';

                    if (file.type.startsWith("image/")) {
                        // Show image in right panel
                        previewHTML =
                            `<img src="${fileURL}" class="img-fluid rounded" style="max-height:600px;">`;
                        $('#pdfmain').replaceWith(
                            `<div id="pdfmain" class="text-center">${previewHTML}</div>`);
                    } else if (file.type === "application/pdf") {
                        // Show PDF inside embed
                        $('#pdfmain').replaceWith(
                            `<embed id="pdfmain" src="${fileURL}" width="100%" height="600px" />`);
                    } else if (file.type.startsWith("text/")) {
                        // Show text inside <pre>
                        previewHTML =
                            `<pre style="max-height:600px; overflow:auto; text-align:left;">${e.target.result}</pre>`;
                        $('#pdfmain').replaceWith(`<div id="pdfmain">${previewHTML}</div>`);
                    } else {
                        $('#pdfmain').replaceWith(
                            `<div id="pdfmain"><p class="text-muted">Preview not supported for this file type.</p></div>`
                        );
                    }
                    const modal = new bootstrap.Modal(document.getElementById('documentModal1'));
                    modal.show();
                };

                if (file.type.startsWith("text/")) {
                    reader.readAsText(file);
                } else {
                    reader.readAsDataURL(file);
                }
            });


            // Clear errors when user changes category or amount
            $(document).on('change input', 'input.category_id, .item-amount, .category-search-input', function() {
                $(this).closest('tr').find('.error-text').remove();
            });

            // ===== BULK REPORT DROPDOWN =====

            $('#bulk-report-search').on('focus click', function() {
                $('#bulk-report-dropdown').show();
                renderBulkReportList('');
            });

            $(document).on('input', '#bulk-report-dropdown .report-inner-search', function() {
                renderBulkReportList($(this).val());
            });

            function renderBulkReportList(filter = '') {

                const list = $('#bulk-report-dropdown .report-list');
                list.empty();

                const q = filter.toLowerCase();

                reportData.forEach(r => {

                    const text = r.report_display;

                    if (!q || text.toLowerCase().includes(q)) {

                        const reportId = text.split(' - ')[0];
                        const reportName = text.split(' - ')[1]?.split(' (')[0] ?? '';
                        const reportDate = text.match(/\((.*?)\)/)?.[1] ?? '';

                        list.append(`
                <div class="report-item-card"
                     data-id="${r.id}"
                     data-name="${text}">

                    <div class="report-top">
                        <span class="report-id">${reportId}</span>
                        <span class="report-amount">₹300.00</span>
                    </div>

                    <div class="report-name">${reportName}</div>
                    <div class="report-date">${reportDate}</div>

                </div>
            `);
                    }
                });

                if (!list.children().length) {
                    list.append(`<div class="p-2 text-muted">No report found</div>`);
                }
            }

            // SELECT REPORT
            $(document).on('click', '#bulk-report-dropdown .report-item-card', function() {

                const id = $(this).data('id');
                const fullText = $(this).data('name');

                let reportName = fullText.split(' - ')[1]?.split(' (')[0] ?? '';

                $('#bulk-report-search').val(reportName);
                $('#bulk-selected-report-id').val(id);
                $('#bulk-report-dropdown').hide();
            });
            // Manage Category link
            $(document).on('click', '.manage-category-link', function() {
                $('#newcatModal').fadeIn();
                $('body').addClass('no-scroll');
                $('.dropdown-menu.tax-dropdown').hide();
            });

            $(document).on('click', '.close-new-modal-cat', function() {
                $('#newcatModal').fadeOut();
                $('body').removeClass('no-scroll');
            });

            $(document).on('click', '.cat_save', function(e) {
                e.preventDefault();

                let status = true;
                $('.cat_name_error').text('');
                $('.cat_type_error').text('');

                if ($('.cat_name').val() === '') {
                    $('.cat_name_error').text('Category Name Required');
                    status = false;
                }
                if ($('.cat_expense_type_id').val() === '') {
                    $('.cat_type_error').text('Expense Type Required');
                    status = false;
                }

                if (!status) return;

                const formData = new FormData();
                formData.append('name', $('.cat_name').val());
                formData.append('expense_type_id', $('.cat_expense_type_id').val());
                formData.append('description', $('.cat_description').val());
                formData.append('is_active', 1);

                $.ajax({
                    url: '{{ route('superadmin.expensecategory.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);

                            $('#newcatModal').fadeOut();
                            $('body').removeClass('no-scroll');

                            // Add new category to local data and all lists
                            if (response.category) {
                                expenseCategoryData.push({
                                    id: response.category.id,
                                    name: response.category.name
                                });

                                const newItem =
                                    `<div class="category-item p-2" data-id="${response.category.id}" data-name="${response.category.name}">${response.category.name}</div>`;
                                $('.category-list').append(newItem);
                            }

                            // Clear inputs
                            $('.cat_name').val('');
                            $('.cat_expense_type_id').val('');
                            $('.cat_description').val('');
                        } else {
                            toastr.warning(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                toastr.warning(value[0]);
                            });
                        } else {
                            toastr.error('An unexpected error occurred. Please try again.');
                        }
                    },
                });
            });
        });
    </script>

    <!-- New Category Modal -->
    <div id="newcatModal" class="tds-modal">
        <div class="tds-modal-content" style="max-width: 600px;">
            <div class="tds-modal-header">
                <h4>New Expense Category</h4>
                <span class="close-new-modal-cat" style="font-size: 2rem; cursor: pointer;">&times;</span>
            </div>
            <div class="tds-modal-body">
                <form>
                    <div style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label>Category Name <span style="color: red">*</span></label>
                            <span class="cat_name_error" style="color: red;"></span>
                            <input type="text" class="form-control cat_name" autocomplete="off"
                                autocorrect="off" />
                        </div>
                        <div style="flex: 1;">
                            <label>Expense Type <span style="color: red">*</span></label>
                            <span class="cat_type_error" style="color: red;"></span>
                            <select class="form-control cat_expense_type_id">
                                <option value="">-- Select Type --</option>
                                @foreach ($expenseTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <div style="flex: 1;">
                            <label>Description</label>
                            <textarea class="form-control cat_description" rows="3" autocomplete="off"></textarea>
                        </div>
                    </div>
                    <br />
                    <button class="btn-save cat_save" type="submit">Save</button>
                    <button class="btn-cancel close-new-modal-cat" type="button">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
