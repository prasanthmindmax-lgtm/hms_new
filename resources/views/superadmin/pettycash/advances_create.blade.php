<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/pettycash.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
    .adv-form-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        padding: 32px;
        margin-bottom: 24px;
    }

    .adv-form-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 28px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .adv-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 28px;
    }

    .adv-field {
        position: relative;
    }

    .adv-field label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: block;
    }

    .adv-field label .required {
        color: #ef4444;
        margin-left: 2px;
    }

    .adv-field .form-control,
    .adv-field .form-select {
        height: 44px;
        border: 1.5px solid #d1d5db;
        border-radius: 10px;
        font-size: 14px;
        padding: 0 14px;
        background: #fafafa;
        transition: all 0.2s ease;
    }

    .adv-field .form-control:focus,
    .adv-field .form-select:focus {
        border-color: #6366f1;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }

    .amount-group {
        display: flex;
        border-radius: 10px;
        overflow: hidden;
        border: 1.5px solid #d1d5db;
    }

    .amount-group select {
        width: 100px;
        border: none;
        border-right: 1px solid #e5e7eb;
        background: #f8fafc;
        font-weight: 600;
        color: #374151;
    }

    .amount-group input {
        flex: 1;
        border: none;
        font-weight: 600;
        font-size: 15px;
        padding: 0 14px;
    }

    .amount-group input:focus {
        background: #fff;
        outline: none;
    }

    /* tax-dropdown-wrapper inside adv-field: full width */
    .adv-field .tax-dropdown-wrapper {
        width: 100% !important;
    }

    .adv-field .tax-dropdown-wrapper .form-control {
        width: 100%;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 14px;
        padding-right: 36px;
        cursor: pointer;
    }

    .adv-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        align-items: center;
        padding-top: 24px;
        border-top: 1px solid #f1f5f9;
        margin-top: 12px;
    }

    .btn-save {
        background: #4f46e5;
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14.5px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-save:hover {
        background: #4338ca;
        transform: translateY(-1px);
    }

    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14.5px;
        text-decoration: none;
        display: inline-block;
    }

    .invalid-feedback {
        font-size: 12.5px;
        color: #ef4444;
        margin-top: 4px;
    }

    .adv-field .invalid-feedback.d-block {
        display: block !important;
    }

    .adv-field .form-control.is-invalid,
    .adv-field .form-select.is-invalid {
        border-color: #ef4444 !important;
        background-color: #fff;
    }

    .amount-group.is-invalid {
        border-color: #ef4444 !important;
    }

    .adv-field-error {
        display: block;
        color: #ef4444;
        font-size: 12.5px;
        margin-top: 4px;
    }

    .adv-notes-field {
        grid-column: span 2;
    }

    .adv-notes-field textarea {
        min-height: 130px;
        resize: vertical;
    }

    /* ── Balance Summary Card ── */
    .adv-balance-card {
        background: #f8faff;
        border: 1.5px solid #e0e7ff;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 24px;
    }

    .adv-balance-title {
        font-size: 13px;
        font-weight: 700;
        color: #4f46e5;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .adv-balance-grid {
        display: flex;
        gap: 0;
        border: 1px solid #e0e7ff;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .adv-balance-item {
        flex: 1;
        padding: 14px 18px;
        border-right: 1px solid #e0e7ff;
        text-align: center;
    }

    .adv-balance-item:last-child { border-right: none; }

    .adv-balance-label {
        font-size: 11px;
        color: #8a94a6;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 6px;
    }

    .adv-balance-value {
        font-size: 17px;
        font-weight: 700;
        color: #1a2332;
    }

    .adv-balance-value.green { color: #16a34a; }
    .adv-balance-value.red   { color: #dc2626; }
    .adv-balance-value.blue  { color: #4f46e5; }

    .adv-reimburse-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
    }

    .adv-reimburse-row:last-child { border-bottom: none; padding-bottom: 0; }

    .adv-reimburse-label { color: #6b7280; }

    .adv-reimburse-value { font-weight: 600; color: #1a2332; }

    .adv-prev-balance-banner {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 12.5px;
        color: #92400e;
        margin-top: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .adv-apply-section {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e0e7ff;
    }

    .adv-apply-form-row {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .adv-apply-form-row .adv-field { flex: 1; min-width: 180px; }

    .btn-apply-advance {
        background: #4f46e5;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 9px;
        font-weight: 600;
        font-size: 13.5px;
        cursor: pointer;
        white-space: nowrap;
        transition: background .2s;
    }

    .btn-apply-advance:hover { background: #4338ca; }

    /* ── Info banner (create / edit) ── */
    .adv-whats-next {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 22px;
        font-size: 13.5px;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
    }
    .adv-whats-next.wn-orange { background: #fffbeb; border-color: #fde68a; }
    .adv-whats-next.wn-blue   { background: #eff6ff; border-color: #bfdbfe; }
    .adv-whats-next.wn-gray   { background: #f9fafb; border-color: #e5e7eb; }
    .adv-whats-next.wn-red    { background: #fef2f2; border-color: #fecaca; }
    .adv-whats-next-icon  { font-size: 20px; flex-shrink: 0; line-height: 1.4; }
    .adv-whats-next-title { font-weight: 700; color: #1f2937; margin-bottom: 3px; }
    .adv-whats-next-text  { color: #6b7280; line-height: 1.55; }

    /* ── Settlement badges ── */
    .adv-settle-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 700;
        margin-top: 10px;
    }
    .adv-settle-badge.excess   { background: #dcfce7; color: #16a34a; }
    .adv-settle-badge.deficit  { background: #fee2e2; color: #dc2626; }
    .adv-settle-badge.balanced { background: #e0e7ff; color: #4f46e5; }
</style>

<body style="overflow-x: hidden;">
    <div class="page-loader"><div class="bar"></div></div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
            <form id="advanceForm">
                @csrf
                @php
                    $advActStatus = $advance ? strtolower($advance->status ?? '') : '';
                    $advFormLocked = in_array($advActStatus, ['closed', 'rejected'], true);
                @endphp
                <input type="hidden" name="id" value="{{ $advance->id ?? '' }}">
                @if(empty($advance) && !empty($prefillExpenseReportId ?? null))
                    <input type="hidden" name="expense_report_id" value="{{ (int) $prefillExpenseReportId }}">
                @endif

                <div class="adv-form-card" @if($advFormLocked) style="opacity:.96;" @endif>
                    <div class="adv-form-title">
                        <i class="bi bi-cash-coin"></i>
                        {{ $advance ? 'Edit Advance' : 'Record Advance' }}
                        @if($advance)
                            @php $advStatus = strtolower($advance->status ?? 'pending'); @endphp
                            @if($advStatus === 'pending')
                                <span style="margin-left:auto;background:#fef3c7;color:#d97706;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;"><i class="bi bi-clock me-1"></i>Pending</span>
                            @elseif($advStatus === 'applied')
                                <span style="margin-left:auto;background:#dcfce7;color:#16a34a;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;"><i class="bi bi-check-circle me-1"></i>Approved</span>
                            @elseif($advStatus === 'closed')
                                <span style="margin-left:auto;background:#e5e7eb;color:#374151;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;"><i class="bi bi-archive me-1"></i>Closed</span>
                            @elseif($advStatus === 'rejected')
                                <span style="margin-left:auto;background:#fee2e2;color:#dc2626;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                            @elseif($advStatus === 'draft')
                                <span style="margin-left:auto;background:#f3f4f6;color:#6b7280;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;"><i class="bi bi-pencil me-1"></i>Draft</span>
                            @endif
                        @endif
                    </div>

                    <fieldset class="adv-form-fields border-0 p-0 m-0 min-w-0"
                        @if($advFormLocked) disabled @endif
                        style="min-width:0;">

                    @php $advFlowStatus = $advActStatus !== '' ? $advActStatus : 'new'; @endphp
                    @if(!$advance)
                        <div class="adv-whats-next wn-blue">
                            <span class="adv-whats-next-icon"><i class="bi bi-info-circle"></i></span>
                            <div>
                                <div class="adv-whats-next-title">Record an advance</div>
                                <div class="adv-whats-next-text">Complete the form and click <strong>Record Advance</strong>. It will appear under <strong>Pending advances</strong> on the Advances page. Use <strong>Apply to Report</strong> there to link it to a petty cash expense report. Further steps (approve, settle) are handled from that list.</div>
                            </div>
                        </div>
                    @elseif($advFlowStatus === 'draft')
                        <div class="adv-whats-next wn-orange">
                            <span class="adv-whats-next-icon"><i class="bi bi-pencil-square"></i></span>
                            <div>
                                <div class="adv-whats-next-title">Draft</div>
                                <div class="adv-whats-next-text">Click <strong>Update Advance</strong> to save; the record becomes <strong>pending</strong> and appears in Pending advances. Use <strong>Apply to Report</strong> on the Advances list for the next step.</div>
                            </div>
                        </div>
                    @elseif($advFlowStatus === 'pending')
                        <div class="adv-whats-next wn-blue">
                            <span class="adv-whats-next-icon"><i class="bi bi-folder2-open"></i></span>
                            <div>
                                <div class="adv-whats-next-title">Next step: link to a report</div>
                                <div class="adv-whats-next-text">Go to <a href="{{ route('superadmin.getadvances') }}">Advances</a> and use <strong>Apply to Report</strong> on this line. Approve / reject / settle are available from the advances list where needed.</div>
                            </div>
                        </div>
                    @elseif($advFlowStatus === 'applied')
                        <div class="adv-whats-next wn-blue">
                            <span class="adv-whats-next-icon"><i class="bi bi-cash-stack"></i></span>
                            <div>
                                <div class="adv-whats-next-title">Approved advance</div>
                                <div class="adv-whats-next-text">Use the <a href="{{ route('superadmin.getadvances') }}">Advances</a> list to settle or review balances. Expenses are tracked via petty cash reports.</div>
                            </div>
                        </div>
                    @elseif($advFlowStatus === 'closed')
                        <div class="adv-whats-next wn-gray">
                            <span class="adv-whats-next-icon"><i class="bi bi-patch-check-fill"></i></span>
                            <div>
                                <div class="adv-whats-next-title">Closed</div>
                                <div class="adv-whats-next-text">This advance is closed. This page is for reference only.</div>
                            </div>
                        </div>
                    @elseif($advFlowStatus === 'rejected')
                        <div class="adv-whats-next wn-red">
                            <span class="adv-whats-next-icon"><i class="bi bi-x-octagon"></i></span>
                            <div>
                                <div class="adv-whats-next-title">Rejected</div>
                                <div class="adv-whats-next-text">You can start a <a href="{{ route('superadmin.getadvancescreate') }}">new advance</a> if needed.</div>
                            </div>
                        </div>
                    @endif

                    {{-- Row 1: Amount | Date | Paid Through | Group of Company --}}
                    <div class="adv-row">

                        {{-- Amount --}}
                        <div class="adv-field">
                            <label>Amount <span class="required">*</span></label>
                            <div class="amount-group">
                                <select name="currency" id="adv-currency">
                                    <option value="INR" {{ ($advance->currency ?? 'INR') === 'INR' ? 'selected' : '' }}>INR</option>
                                    <option value="USD" {{ ($advance->currency ?? '') === 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ ($advance->currency ?? '') === 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="GBP" {{ ($advance->currency ?? '') === 'GBP' ? 'selected' : '' }}>GBP</option>
                                </select>
                                <input type="number" name="advance_amount" id="adv-amount" step="0.01" min="0"
                                    placeholder="0.00" value="{{ $advance->advance_amount ?? '' }}" required>
                            </div>
                            <div class="invalid-feedback d-block adv-field-error" id="err-amount" style="display:none;"></div>
                        </div>

                        {{-- Date --}}
                        <div class="adv-field">
                            <label>Date <span class="required">*</span></label>
                            <input type="text" class="form-control" name="advance_date" id="adv-date"
                                placeholder="DD/MM/YYYY"
                                value="{{ $advance && $advance->advance_date ? \Carbon\Carbon::parse($advance->advance_date)->format('d/m/Y') : '' }}"
                                autocomplete="off" readonly required>
                            <div class="invalid-feedback d-block" id="err-date" style="display:none;"></div>
                        </div>

                        {{-- Paid Through --}}
                        <div class="adv-field">
                            <label>Paid Through</label>
                            <select class="form-select" name="paid_through" id="adv-paid-through">
                                <option value="">— Select —</option>
                                @php $paidOptions = ['Cash','Petty Cash','Undeposited Funds','Bank Transfer','Cheque']; @endphp
                                @foreach($paidOptions as $val)
                                    <option value="{{ $val }}" {{ ($advance->paid_through ?? '') === $val ? 'selected' : '' }}>
                                        {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Group of Company --}}
                        <div class="adv-field">
                            <label>Group of Company <span class="required">*</span></label>
                            <div class="tax-dropdown-wrapper company-section">
                                <input type="text" class="form-control company-search-input"
                                    name="company_name" placeholder="Select a Company" readonly
                                    value="{{ $advance->company_name ?? '' }}"
                                    autocomplete="off">
                                <input type="hidden" name="company_id" class="company_id"
                                    value="{{ $advance->company_id ?? '' }}">
                                <div class="dropdown-menu tax-dropdown">
                                    <div class="company-list">
                                        @foreach($companies as $c)
                                            <div data-id="{{ $c->id }}"
                                                class="{{ ($advance->company_id ?? '') == $c->id ? 'selected' : '' }}">
                                                {{ $c->company_name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <span class="error_company adv-field-error" style="display:none;"></span>
                            </div>
                        </div>

                    </div>

                    {{-- Row 2: Zone | Branch | Vendor | Reference --}}
                    <div class="adv-row">

                        {{-- Zone --}}
                        <div class="adv-field">
                            <label>Zone <span class="required">*</span></label>
                            <div class="tax-dropdown-wrapper account-section">
                                <input type="text" class="form-control zone-search-input"
                                    name="zone" placeholder="Select a Zone" readonly
                                    value="{{ $advance->zone_name ?? '' }}"
                                    autocomplete="off">
                                <input type="hidden" name="zone_id" class="zone_id"
                                    value="{{ $advance->zone_id ?? '' }}">
                                <div class="dropdown-menu tax-dropdown">
                                    <div class="zone-list">
                                        @foreach($zones as $z)
                                            <div data-id="{{ $z->id }}"
                                                class="{{ ($advance->zone_id ?? '') == $z->id ? 'selected' : '' }}">
                                                {{ $z->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <span class="error_zone adv-field-error" style="display:none;"></span>
                            </div>
                        </div>

                        {{-- Branch --}}
                        @php
                            $advBranchCsv = '';
                            if (!empty($advance)) {
                                $mergedBranchIds = [];
                                if (!empty($advance->branch_id)) {
                                    $mergedBranchIds[] = (int) $advance->branch_id;
                                }
                                foreach (array_filter(array_map('intval', explode(',', (string) ($advance->branch_ids ?? '')))) as $_advBid) {
                                    $mergedBranchIds[] = $_advBid;
                                }
                                $advBranchCsv = implode(',', array_values(array_unique(array_filter($mergedBranchIds))));
                            }
                        @endphp
                        <div class="adv-field">
                            <label>Branch <span class="required">*</span></label>
                            <div class="tax-dropdown-wrapper account-section">
                                <input type="text" class="form-control branch-search-input dropdown-search-input"
                                    name="branch" placeholder="Select a Branch" readonly
                                    value="{{ $advance->branch_name ?? '' }}"
                                    autocomplete="off">
                                <input type="hidden" name="branch_id" class="branch_id"
                                    value="{{ $advBranchCsv }}">
                                <div class="dropdown-menu tax-dropdown">
                                    <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Branch..."></div>
                                    <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                                    </div>
                                    <div class="dropdown-list multiselect branch-list">
                                        {{-- Populated via AJAX when zone is selected --}}
                                    </div>
                                </div>
                                <span class="error_branch adv-field-error" style="display:none;"></span>
                            </div>
                        </div>

                        {{-- Person / Vendor --}}
                        <div class="adv-field">
                            <label>Person / Vendor</label>
                            <div class="tax-dropdown-wrapper vendor-section">
                                <input type="text" class="form-control vendor-search-input"
                                    name="vendor_name" placeholder="Search vendor" readonly
                                    value="{{ $advance->vendor_display_name ?? '' }}"
                                    autocomplete="off">
                                <input type="hidden" name="vendor_id" class="vendor_id"
                                    value="{{ $advance->vendor_id ?? '' }}">
                                <div class="dropdown-menu tax-dropdown">
                                    <div class="vendor-list">
                                        @foreach($vendors as $v)
                                            <div data-id="{{ $v->id }}"
                                                class="{{ ($advance->vendor_id ?? '') == $v->id ? 'selected' : '' }}">
                                                {{ $v->display_name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Reference # --}}
                        <div class="adv-field">
                            <label>Reference #</label>
                            <input type="text" class="form-control" name="reference_no" id="adv-reference"
                                placeholder="Enter reference number" value="{{ $advance->reference_no ?? '' }}">
                        </div>

                    </div>

                    {{-- Row 3: Notes --}}
                    <div class="adv-row">
                        <div class="adv-field adv-notes-field">
                            <label>Notes</label>
                            <textarea class="form-control" name="notes" id="adv-notes" rows="3"
                                placeholder="Additional notes...">{{ $advance->notes ?? '' }}</textarea>
                        </div>
                    </div>

                    {{-- ── Reimbursement Summary (edit mode: show for applied/closed advances) ── --}}
                    @if($advance && in_array(strtolower($advance->status ?? ''), ['applied', 'closed']))
                    @php
                        $reimburseAmt  = (float)($advance->used_amount ?? 0) - (float)($advance->advance_amount ?? 0);
                        $settleType    = $reimburseAmt > 0 ? 'excess' : ($reimburseAmt < 0 ? 'deficit' : 'balanced');
                        $settleBadge   = match($settleType) {
                            'excess'   => ['class' => 'excess',   'icon' => 'bi-arrow-up-circle-fill',   'text' => 'Expenses exceed advance — Reimbursement due to employee'],
                            'deficit'  => ['class' => 'deficit',  'icon' => 'bi-arrow-down-circle-fill', 'text' => 'Advance exceeds expenses — Employee to return balance'],
                            default    => ['class' => 'balanced', 'icon' => 'bi-check-circle-fill',      'text' => 'Perfectly balanced — No further payment required'],
                        };
                    @endphp
                    <div class="adv-balance-card">
                        <div class="adv-balance-title">
                            <i class="bi bi-calculator"></i>
                            Settlement Summary
                            @if(strtolower($advance->status ?? '') === 'closed')
                                <span style="margin-left:auto;background:#e0e7ff;color:#4f46e5;padding:3px 12px;border-radius:20px;font-size:11.5px;font-weight:600;"><i class="bi bi-patch-check-fill me-1"></i>Settled</span>
                            @endif
                        </div>
                        <div class="adv-balance-grid">
                            <div class="adv-balance-item">
                                <div class="adv-balance-label">Advance Amount</div>
                                <div class="adv-balance-value blue">
                                    ₹{{ number_format($advance->advance_amount ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="adv-balance-item">
                                <div class="adv-balance-label">Total Expenses Applied</div>
                                <div class="adv-balance-value">
                                    ₹{{ number_format($advance->used_amount ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="adv-balance-item">
                                <div class="adv-balance-label">Remaining Balance</div>
                                @php $bal = (float)($advance->balance_amount ?? 0); @endphp
                                <div class="adv-balance-value {{ $bal >= 0 ? 'green' : 'red' }}">
                                    ₹{{ number_format(abs($bal), 2) }}
                                    @if($bal < 0) <span style="font-size:12px;">(Deficit)</span> @endif
                                </div>
                            </div>
                            <div class="adv-balance-item">
                                <div class="adv-balance-label">
                                    @if($settleType === 'excess') Employee Reimbursement
                                    @elseif($settleType === 'deficit') Balance to Return
                                    @else Net Settlement @endif
                                </div>
                                <div class="adv-balance-value {{ $reimburseAmt > 0 ? 'green' : ($reimburseAmt < 0 ? 'red' : 'blue') }}">
                                    @if($reimburseAmt >= 0)
                                        ₹{{ number_format($reimburseAmt, 2) }}
                                    @else
                                        <span style="font-size:13px;">(-)&nbsp;</span>₹{{ number_format(abs($reimburseAmt), 2) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="adv-settle-badge {{ $settleBadge['class'] }}">
                            <i class="bi {{ $settleBadge['icon'] }}"></i>
                            {{ $settleBadge['text'] }}
                        </div>

                        @if($monthBalance && ($monthBalance['total_advance'] > 0 || $monthBalance['prev_balance'] > 0))
                        <div style="margin-top:14px;">
                            <div class="adv-reimburse-row">
                                <span class="adv-reimburse-label">Other advances this month (same branch/company)</span>
                                <span class="adv-reimburse-value">₹{{ number_format($monthBalance['total_advance'], 2) }}</span>
                            </div>
                            @if($monthBalance['prev_balance'] > 0)
                            <div class="adv-prev-balance-banner">
                                <i class="bi bi-info-circle-fill"></i>
                                Carried over from previous month
                                ({{ \Carbon\Carbon::createFromFormat('Y-m', $monthBalance['prev_month'])->format('M Y') }}):
                                <strong>&nbsp;₹{{ number_format($monthBalance['prev_balance'], 2) }}</strong>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- ── Monthly Balance Summary (new or pending advance: informational) ── --}}
                    @if(!$advance || strtolower($advance->status ?? '') === 'pending')
                    <div class="adv-balance-card" id="monthly-balance-panel" style="display:none;">
                        <div class="adv-balance-title">
                            <i class="bi bi-bar-chart-line"></i> This Month's Advance Balance
                            <span id="monthly-balance-month" style="font-weight:400;color:#6b7280;font-size:12px;margin-left:6px;text-transform:none;letter-spacing:0;"></span>
                        </div>
                        <div class="adv-balance-grid">
                            <div class="adv-balance-item">
                                <div class="adv-balance-label">Total Advance (Month)</div>
                                <div class="adv-balance-value blue" id="mb-total-advance">₹0.00</div>
                            </div>
                            <div class="adv-balance-item">
                                <div class="adv-balance-label">Total Used</div>
                                <div class="adv-balance-value" id="mb-total-used">₹0.00</div>
                            </div>
                            <div class="adv-balance-item">
                                <div class="adv-balance-label">Net Balance</div>
                                <div class="adv-balance-value green" id="mb-balance">₹0.00</div>
                            </div>
                        </div>
                        <div id="mb-prev-banner" style="display:none;" class="adv-prev-balance-banner">
                            <i class="bi bi-info-circle-fill"></i>
                            <span id="mb-prev-text"></span>
                        </div>
                    </div>
                    @endif

                    </fieldset>

                    {{-- Actions: save from this page only; approve / apply report / settle use Advances list --}}
                    <div class="adv-actions">
                        <a href="{{ route('superadmin.getadvances') }}" class="btn-cancel">Back to list</a>
                        @if(!$advFormLocked)
                            <button type="button" id="btn-save" class="btn-save">
                                <i class="bi bi-check2-circle me-2"></i>
                                {{ $advance ? 'Update Advance' : 'Record Advance' }}
                            </button>
                        @endif
                    </div>
                </div>
            </form>

        </div>
    </div>

    @include('superadmin.superadminfooter')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function () {

            // ── Generic clone-to-body dropdown helper (same as quotation) ────
            function openCloneDropdown($input) {
                $('.dropdown-menu.tax-dropdown').hide();
                let $dropdown = $input.data('dropdown');
                if (!$dropdown) {
                    $dropdown = $input.siblings('.dropdown-menu').clone(true);
                    $('body').append($dropdown);
                    $input.data('dropdown', $dropdown);
                }
                $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
                var offset = $input.offset();
                $dropdown.css({
                    position: 'absolute',
                    top:  offset.top + $input.outerHeight(),
                    left: offset.left,
                    width: $input.outerWidth(),
                    zIndex: 9999
                }).show();
                $input.removeAttr('readonly');
            }

            // ── Zone ─────────────────────────────────────────────────────────
            $(document).on('click', '.zone-search-input', function (e) {
                e.stopPropagation();
                $(this).val('');
                openCloneDropdown($(this));
            });

            $(document).on('keyup', '.zone-search-input', function () {
                var q = $(this).val().toLowerCase();
                var $dd = $(this).data('dropdown');
                if ($dd) {
                    $dd.find('.zone-list div[data-id]').each(function () {
                        $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
                    });
                }
            });

            $(document).on('click', '.zone-list div[data-id]', function () {
                var selectedText = $(this).text().trim();
                var selectedId   = $(this).data('id');
                var $dropdown    = $(this).closest('.dropdown-menu.tax-dropdown');
                var wrapper      = $dropdown.data('wrapper');
                if (!wrapper) { $dropdown.hide(); return; }
                wrapper.find('.zone-search-input').val(selectedText).attr('readonly', true).removeClass('is-invalid');
                wrapper.find('.error_zone').text('').hide();
                wrapper.find('.zone_id').val(selectedId).trigger('click');
                $dropdown.hide();
            });

            // ── Zone → fetch branches (same AJAX pattern as quotation) ───────
            $(document).on('click', '.zone_id', function () {
                var id = $(this).val();
                if (!id) return;
                var formData = new FormData();
                formData.append('id', id);
                // Reset branch
                $('.branch-search-input').val('').attr('readonly', true);
                $('.branch_id').val('');
                // Clear any cached cloned dropdown for branch
                $('.branch-search-input').removeData('dropdown');
                $('.branch-list').empty();

                $.ajax({
                    url: '{{ route("superadmin.getbranchfetch") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                    success: function (response) {
                        if (response.branch && response.branch.length) {
                            response.branch.forEach(function (branch) {
                                var item = $('<div>').attr('data-id', branch.id).attr('data-value', branch.name).text(branch.name);
                                $('.branch-list').append(item);
                            });
                        }
                    },
                    error: function () {}
                });
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
                if (ids.length) {
                    wrapper.find('.branch-search-input').removeClass('is-invalid');
                    wrapper.find('.error_branch').text('').hide();
                }
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

            // ── Company ──────────────────────────────────────────────────────
            $(document).on('click', '.company-search-input', function (e) {
                e.stopPropagation();
                $(this).val('');
                openCloneDropdown($(this));
            });

            $(document).on('keyup', '.company-search-input', function () {
                var q  = $(this).val().toLowerCase();
                var $dd = $(this).data('dropdown');
                if ($dd) {
                    $dd.find('.company-list div[data-id]').each(function () {
                        $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
                    });
                }
            });

            $(document).on('click', '.company-list div[data-id]', function () {
                var selectedText = $(this).text().trim();
                var selectedId   = $(this).data('id');
                var $dropdown    = $(this).closest('.dropdown-menu.tax-dropdown');
                var wrapper      = $dropdown.data('wrapper');
                if (!wrapper) { $dropdown.hide(); return; }
                wrapper.find('.company-search-input').val(selectedText).attr('readonly', true).removeClass('is-invalid');
                wrapper.find('.error_company').text('').hide();
                wrapper.find('.company_id').val(selectedId).trigger('change');
                $dropdown.hide();
            });

            // ── Vendor ───────────────────────────────────────────────────────
            $(document).on('click', '.vendor-search-input', function (e) {
                e.stopPropagation();
                $(this).val('');
                openCloneDropdown($(this));
            });

            $(document).on('keyup', '.vendor-search-input', function () {
                var q  = $(this).val().toLowerCase();
                var $dd = $(this).data('dropdown');
                if ($dd) {
                    $dd.find('.vendor-list div[data-id]').each(function () {
                        $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
                    });
                }
            });

            $(document).on('click', '.vendor-list div[data-id]', function () {
                var selectedText = $(this).text().trim();
                var selectedId   = $(this).data('id');
                var $dropdown    = $(this).closest('.dropdown-menu.tax-dropdown');
                var wrapper      = $dropdown.data('wrapper');
                if (!wrapper) { $dropdown.hide(); return; }
                wrapper.find('.vendor-search-input').val(selectedText).attr('readonly', true);
                wrapper.find('.vendor_id').val(selectedId).trigger('change');
                $dropdown.hide();
            });

            // ── Close all dropdowns on outside click ──────────────────────────
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.tax-dropdown-wrapper').length &&
                    !$(e.target).closest('.dropdown-menu.tax-dropdown').length) {
                    $('.dropdown-menu.tax-dropdown').hide();
                    // Restore readonly on all search inputs
                    $('.zone-search-input, .branch-search-input, .company-search-input, .vendor-search-input')
                        .attr('readonly', true);
                }
            });

            // ── Edit mode: pre-load branches for saved zone ───────────────────
            @if($advance && $advance->zone_id)
            (function () {
                var savedZoneId = '{{ $advance->zone_id }}';
                var savedBranchCsv = '{{ $advBranchCsv }}';
                var savedIdSet = {};
                savedBranchCsv.split(',').forEach(function (s) {
                    s = String(s).trim();
                    if (s) savedIdSet[s] = true;
                });
                var formData = new FormData();
                formData.append('id', savedZoneId);
                $.ajax({
                    url: '{{ route("superadmin.getbranchfetch") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                    success: function (response) {
                        if (response.branch && response.branch.length) {
                            response.branch.forEach(function (branch) {
                                var $item = $('<div>').attr('data-id', branch.id).attr('data-value', branch.name).text(branch.name);
                                if (savedIdSet[String(branch.id)]) {
                                    $item.addClass('selected');
                                }
                                $('.branch-list').append($item);
                            });
                            var selectedNames = [], selectedIds = [];
                            $('.branch-list div.selected').each(function () {
                                selectedNames.push($(this).text().trim());
                                selectedIds.push($(this).data('id'));
                            });
                            if (selectedNames.length) {
                                $('.branch-search-input').val(selectedNames.join(', ')).attr('readonly', true);
                                $('.branch_id').val(selectedIds.join(','));
                            }
                        }
                    },
                    error: function () {}
                });
            })();
            @endif

            function clearAdvanceFieldErrors() {
                $('#adv-amount').removeClass('is-invalid');
                $('.amount-group').removeClass('is-invalid');
                $('#err-amount').text('').hide();
                $('#adv-date').removeClass('is-invalid');
                $('#err-date').text('').hide();
                $('.company-search-input').removeClass('is-invalid');
                $('.error_company').text('').hide();
                $('.zone-search-input').removeClass('is-invalid');
                $('.error_zone').text('').hide();
                $('.branch-search-input').removeClass('is-invalid');
                $('.error_branch').text('').hide();
            }

            function isValidDdMmYyyy(s) {
                if (!s || typeof s !== 'string') return false;
                var m = s.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
                if (!m) return false;
                var d = parseInt(m[1], 10), mo = parseInt(m[2], 10), y = parseInt(m[3], 10);
                if (mo < 1 || mo > 12 || d < 1 || d > 31 || y < 1900 || y > 2100) return false;
                var dt = new Date(y, mo - 1, d);
                return dt.getFullYear() === y && dt.getMonth() === mo - 1 && dt.getDate() === d;
            }

            /** Client-side checks aligned with saveAdvance (amount, date, branch). */
            function validateAdvanceForm(action) {
                if (action !== 'save') {
                    return { ok: true, $first: null };
                }
                var $first = null;
                var errCount = 0;
                function markFirst($el) {
                    if (!$first || !$first.length) {
                        $first = $el;
                    }
                }

                var rawAmt = ($('#adv-amount').val() || '').trim();
                var amount = parseFloat(rawAmt);
                if (!rawAmt || isNaN(amount) || amount < 0.01) {
                    errCount++;
                    $('#adv-amount').addClass('is-invalid');
                    $('.amount-group').addClass('is-invalid');
                    $('#err-amount').text('Amount field is required.').show();
                    markFirst($('#adv-amount'));
                }

                var dateStr = ($('#adv-date').val() || '').trim();
                if (!dateStr || !isValidDdMmYyyy(dateStr)) {
                    errCount++;
                    $('#adv-date').addClass('is-invalid');
                    $('#err-date').text('Select a valid date (DD/MM/YYYY).').show();
                    markFirst($('#adv-date'));
                }

                var cid = parseInt(($('.company_id').val() || '').trim(), 10);
                if (!cid || cid <= 0) {
                    errCount++;
                    $('.company-search-input').addClass('is-invalid');
                    $('.error_company').text('Please select a group of company.').show();
                    markFirst($('.company-search-input'));
                }

                var zid = parseInt(($('.zone_id').val() || '').trim(), 10);
                if (!zid || zid <= 0) {
                    errCount++;
                    $('.zone-search-input').addClass('is-invalid');
                    $('.error_zone').text('Please select a zone.').show();
                    markFirst($('.zone-search-input'));
                }

                var brRaw = ($('.branch_id').val() || '').trim();
                var branchIds = brRaw
                    ? brRaw.split(',').map(function (s) {
                        return parseInt(String(s).trim(), 10);
                    }).filter(function (n) {
                        return !isNaN(n) && n > 0;
                    })
                    : [];
                if (!branchIds.length) {
                    errCount++;
                    $('.branch-search-input').addClass('is-invalid');
                    $('.error_branch').text('Please select at least one branch.').show();
                    markFirst($('.branch-search-input'));
                }

                return { ok: errCount === 0, $first: $first };
            }

            function scrollToAdvField($el) {
                if (!$el || !$el.length) return;
                try {
                    var top = $el.offset().top - 100;
                    $('html, body').animate({ scrollTop: Math.max(0, top) }, 200);
                    $el.trigger('focus');
                } catch (e) {}
            }

            $('#adv-amount').on('input', function () {
                var raw = ($(this).val() || '').trim();
                var amount = parseFloat(raw);
                if (raw && !isNaN(amount) && amount >= 0.01) {
                    $(this).removeClass('is-invalid');
                    $('.amount-group').removeClass('is-invalid');
                    $('#err-amount').text('').hide();
                }
            });

            // ── Form submission ───────────────────────────────────────────────
            function submitForm(action) {
                clearAdvanceFieldErrors();
                var v = validateAdvanceForm(action);
                if (!v.ok) {
                    toastr.error('Please correct the highlighted fields.');
                    scrollToAdvField(v.$first);
                    return;
                }

                var formData = {
                    _token:         $('meta[name="csrf-token"]').attr('content'),
                    id:             $('input[name="id"]').val(),
                    currency:       $('#adv-currency').val(),
                    advance_amount: $('#adv-amount').val(),
                    advance_date:   $('#adv-date').val(),
                    paid_through:   $('#adv-paid-through').val(),
                    zone_id:        $('.zone_id').val(),
                    company_id:     $('.company_id').val(),
                    branch_id:      $('.branch_id').val(),
                    vendor_id:      $('.vendor_id').val(),
                    reference_no:   $('#adv-reference').val(),
                    notes:          $('#adv-notes').val(),
                    save_action:    action,
                };

                var $btn = action === 'draft'
                    ? $('#btn-draft')
                    : (action === 'new' ? $('#btn-save-new') : $('#btn-save'));
                if (!$btn.length) {
                    toastr.error('Save is not available on this screen.');
                    return;
                }
                var originalText = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: '{{ route('superadmin.saveadvance') }}',
                    method: 'POST',
                    data: formData,
                    success: function (res) {
                        if (res.success) {
                            toastr.success(res.message);
                            setTimeout(function () {
                                window.location.href = res.redirect || '{{ route('superadmin.getadvances') }}';
                            }, 1200);
                        } else {
                            toastr.error(res.message || 'An error occurred.');
                            $btn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function (xhr) {
                        var msg = 'An error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors)
                                .map(function (e) { return e[0]; }).join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            }

            $('#btn-save').on('click', function () { submitForm('save'); });

            // ── Monthly Balance Fetch ─────────────────────────────────────────
            function fetchMonthlyBalance() {
                var branchId  = $('.branch_id').val();
                var companyId = $('.company_id').val();
                var dateVal   = $('#adv-date').val(); // d/m/Y
                var month     = '';
                if (dateVal) {
                    try {
                        var parts = dateVal.split('/');
                        month = parts[2] + '-' + parts[1]; // Y-m
                    } catch(e) {}
                }

                if (!month || (!branchId && !companyId)) {
                    $('#monthly-balance-panel').hide();
                    return;
                }

                $.ajax({
                    url: '{{ route("superadmin.getadvancemonthbalance") }}',
                    type: 'GET',
                    data: {
                        branch_id:  branchId  || undefined,
                        company_id: companyId || undefined,
                        month:      month,
                        exclude_id: $('input[name="id"]').val() || undefined,
                    },
                    success: function (r) {
                        var fmt = function(n) {
                            return '₹' + parseFloat(n || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        };

                        $('#mb-total-advance').text(fmt(r.total_advance));
                        $('#mb-total-used').text(fmt(r.total_used));

                        var bal = parseFloat(r.balance || 0);
                        $('#mb-balance').text(fmt(bal))
                            .removeClass('green red')
                            .addClass(bal >= 0 ? 'green' : 'red');

                        // Month label
                        try {
                            var d = new Date(month + '-01');
                            $('#monthly-balance-month').text('— ' + d.toLocaleString('default', { month: 'long', year: 'numeric' }));
                        } catch(e) {}

                        if (r.prev_balance > 0) {
                            try {
                                var pm = new Date(r.prev_month + '-01');
                                var pmLabel = pm.toLocaleString('default', { month: 'long', year: 'numeric' });
                                $('#mb-prev-text').html('Carried over from <strong>' + pmLabel + '</strong>: <strong>₹' + parseFloat(r.prev_balance).toLocaleString('en-IN', {minimumFractionDigits:2,maximumFractionDigits:2}) + '</strong>');
                                $('#mb-prev-banner').show();
                            } catch(e) {}
                        } else {
                            $('#mb-prev-banner').hide();
                        }

                        if (r.total_advance > 0 || r.prev_balance > 0) {
                            $('#monthly-balance-panel').show();
                        } else {
                            $('#monthly-balance-panel').hide();
                        }
                    }
                });
            }

            // Trigger balance fetch on branch, company, or date change
            $(document).on('change', '.branch_id, .company_id', function () {
                fetchMonthlyBalance();
            });

            // Flatpickr date change triggers balance fetch
            if (typeof flatpickr !== 'undefined') {
                var fpDate = flatpickr('#adv-date', {
                    dateFormat: 'd/m/Y',
                    allowInput: false,
                    defaultDate: $('#adv-date').val() || 'today',
                    onChange: function () {
                        $('#adv-date').removeClass('is-invalid');
                        $('#err-date').text('').hide();
                        fetchMonthlyBalance();
                    }
                });
            }

            // Initial fetch (edit mode: branch/company already set)
            @if($advance && $advance->branch_id)
            fetchMonthlyBalance();
            @endif;

        });
    </script>
</body>
</html>
