<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
/* ── Wrapper ────────────────────────────────────────────── */
.is-wrapper { padding: 24px; }

/* ── Page header ────────────────────────────────────────── */
.is-page-title    { font-size: 1.55rem; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
.is-page-subtitle { font-size: 0.84rem; color: #64748b; margin-bottom: 0; }

/* ── Header action buttons ──────────────────────────────── */
.is-filter-btn {
    background: #4f46e5; color: #fff; border: none;
    border-radius: 8px; padding: 9px 20px; font-size: .875rem;
    font-weight: 600; cursor: pointer; display: inline-flex;
    align-items: center; gap: 8px; transition: background .2s;
}
.is-filter-btn:hover { background: #4338ca; }

/* ── Filter Panel ───────────────────────────────────────── */
.is-filter-panel {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 12px; padding: 20px 24px;
    margin-bottom: 20px; display: none;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    animation: isFadeDown .2s ease;
}
.is-filter-panel.open { display: block; }
@keyframes isFadeDown {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.is-filter-title {
    font-size: .92rem; font-weight: 700; color: #1e293b;
    margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
}
.is-filter-panel .form-label { font-size: .78rem; font-weight: 600; color: #475569; margin-bottom: 4px; }
.is-filter-panel .form-control {
    border-radius: 8px; border: 1px solid #cbd5e1;
    font-size: .875rem; padding: 8px 12px; transition: border-color .2s;
}
.is-filter-panel .form-control:focus {
    border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); outline: none;
}

/* ── Date presets ───────────────────────────────────────── */
.is-date-presets { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 0; }
.is-preset {
    padding: 6px 14px; border-radius: 20px; font-size: .78rem; font-weight: 600;
    border: 1.5px solid #cbd5e1; background: #fff; color: #475569;
    cursor: pointer; transition: all .15s; white-space: nowrap;
}
.is-preset:hover { border-color: #4f46e5; color: #4f46e5; background: #ede9fe; }
.is-preset.active { background: #4f46e5; color: #fff; border-color: #4f46e5; }

/* Discount / Cancel / Refund approval (HMS forms) */
.is-dcr-opt {
    display: inline-block; padding: 6px 14px; font-size: 0.8rem; font-weight: 600;
    border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; color: #475569;
    background: #fff; transition: all .15s;
}
.is-dcr-opt:hover { border-color: #4f46e5; color: #4f46e5; background: #f5f3ff; }
.is-dcr-opt.active { background: #4f46e5; color: #fff; border-color: #4f46e5; }

/* ── Custom date row ────────────────────────────────────── */
.is-date-custom { display: none; margin-top: 10px; }
.is-date-custom.show { display: flex; gap: 12px; flex-wrap: wrap; }
.fp-wrap { position: relative; }
.fp-wrap .fp-icon { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
.fp-wrap .form-control { padding-right: 32px; cursor: pointer !important; }

/* ── Apply / Reset ──────────────────────────────────────── */
.is-btn-row { display: flex; gap: 10px; align-items: center; margin-top: 14px; }
.is-btn-apply { background: #4f46e5; color: #fff; border: none; border-radius: 8px; padding: 9px 22px; font-weight: 600; font-size: .875rem; cursor: pointer; }
.is-btn-apply:hover { background: #4338ca; }
.is-btn-reset { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 18px; font-weight: 600; font-size: .875rem; cursor: pointer; }
.is-btn-reset:hover { background: #e2e8f0; }

/* ── Multi-Select Widget ────────────────────────────────── */
.ms-wrap     { position: relative; }
.ms-trigger  {
    display: flex; align-items: center; justify-content: space-between;
    border: 1px solid #cbd5e1; border-radius: 8px;
    padding: 8px 12px; font-size: .875rem; background: #fff;
    cursor: pointer; min-height: 40px; user-select: none;
    transition: border-color .2s; gap: 6px;
}
.ms-trigger:hover, .ms-trigger.open { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
.ms-trigger-text { flex: 1; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; color: #334155; }
.ms-trigger-text.ph { color: #94a3b8; }
.ms-trigger-arrow { color: #94a3b8; font-size: .7rem; transition: transform .2s; flex-shrink: 0; }
.ms-trigger.open .ms-trigger-arrow { transform: rotate(180deg); }
.ms-count { background: #4f46e5; color: #fff; border-radius: 10px; font-size: .65rem; font-weight: 700; padding: 1px 6px; flex-shrink: 0; }
.ms-dropdown {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0; min-width: 220px;
    background: #fff; border: 1px solid #cbd5e1; border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12); z-index: 1050;
    display: none; flex-direction: column; max-height: 240px; overflow: hidden;
}
.ms-dropdown.open { display: flex; }
.ms-search-row { padding: 8px; border-bottom: 1px solid #f1f5f9; flex-shrink: 0; }
.ms-search { width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 5px 10px; font-size: .8rem; outline: none; }
.ms-search:focus { border-color: #4f46e5; }
.ms-action-row { display: flex; gap: 6px; padding: 5px 8px; border-bottom: 1px solid #f1f5f9; flex-shrink: 0; }
.ms-act { font-size: .72rem; font-weight: 600; color: #4f46e5; cursor: pointer; padding: 2px 6px; border-radius: 4px; }
.ms-act:hover { background: #ede9fe; }
.ms-act.clr { color: #dc2626; }
.ms-act.clr:hover { background: #fee2e2; }
.ms-list { overflow-y: auto; flex: 1; }
.ms-opt {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 10px; cursor: pointer; font-size: .82rem; color: #475569; transition: background .1s;
}
.ms-opt:hover { background: #f8fafc; }
.ms-opt.sel   { background: #ede9fe; color: #4f46e5; font-weight: 600; }
.ms-cb {
    width: 14px; height: 14px; border: 1.5px solid #cbd5e1; border-radius: 3px;
    flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: .6rem;
}
.ms-opt.sel .ms-cb { background: #4f46e5; border-color: #4f46e5; color: #fff; }

/* ── Active filter chips ────────────────────────────────── */
.is-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; min-height: 4px; }
.is-chip {
    background: #ede9fe; color: #4f46e5; border-radius: 20px;
    padding: 4px 12px; font-size: .78rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px;
}
.is-chip .rm { cursor: pointer; font-size: 1rem; line-height: 1; opacity: .7; }
.is-chip .rm:hover { opacity: 1; }
.is-chip-clear { background: #fee2e2; color: #dc2626; cursor: pointer; }
.is-chip-clear:hover { background: #fecaca; }

/* ── Summary badges ─────────────────────────────────────── */
.is-badges { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px; }
.is-badge {
    padding: 10px 18px; border-radius: 10px; color: #fff;
    font-weight: 700; font-size: .9rem;
    display: inline-flex; align-items: center; gap: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
}
.is-badge i { font-size: 1rem; }
.is-badge--total    { background: linear-gradient(135deg, #0ea5e9, #2563eb); }
.is-badge--discount { background: linear-gradient(135deg, #f59e0b, #d97706); }
.is-badge--cancel   { background: linear-gradient(135deg, #ef4444, #b91c1c); }
.is-badge--refund   { background: linear-gradient(135deg, #a855f7, #7c3aed); }

/* ── Table card ─────────────────────────────────────────── */
.is-table-card {
    background: #fff; border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
    overflow: hidden; position: relative;
}
.is-table-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 10px;
}
.is-table-title { font-size: 1rem; font-weight: 700; color: #1e293b; }
.is-export-btns { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.is-btn-exp {
    border-radius: 8px; padding: 7px 16px; font-size: .8rem;
    font-weight: 600; cursor: pointer; display: inline-flex;
    align-items: center; gap: 6px; border: none; transition: opacity .2s;
}
.is-btn-exp:hover { opacity: .85; }
.is-btn-xlsx { background: #16a34a; color: #fff; }
.is-btn-csv  { background: #2563eb; color: #fff; }
.is-per-page { display: flex; align-items: center; gap: 8px; font-size: .82rem; color: #64748b; }
.is-per-page select { border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: .82rem; }

/* ── Data table ─────────────────────────────────────────── */
.is-table-card table { width: 100%; border-collapse: collapse; margin: 0; }
.is-table-card thead th {
    background: #f8fafc; font-size: .73rem; font-weight: 700;
    color: #64748b; text-transform: uppercase; letter-spacing: .4px;
    padding: 12px 14px; border-bottom: 2px solid #e2e8f0; white-space: nowrap;
}
.is-table-card tbody td {
    font-size: .82rem; color: #334155; padding: 11px 14px;
    border-bottom: 1px solid #f1f5f9; vertical-align: middle;
}
.is-table-card tbody tr:last-child td { border-bottom: none; }
.is-table-card tbody tr:hover td { background: #f8fafc; }
.is-table-card .is-total-row td { background: #f8fafc !important; font-weight: 700; color: #1e293b !important; font-size: .84rem; }
td.loc-name { color: #1e293b !important; font-weight: 600; }
td.num-col  { color: #334155 !important; font-family: 'Courier New', monospace; }
td.num-col-discount { color: #d97706 !important; font-weight: 600; }
td.num-col-cancel   { color: #dc2626 !important; font-weight: 600; }
td.num-col-bill     { color: #7c3aed !important; font-weight: 600; }
td.num-col-total    { color: #1d4ed8 !important; font-weight: 700; }

/* Drill-down bill list / matrix — match main income it-table (red headers, no grey theme) */
#ddCard table.it-table thead th {
    background: #fff !important;
    color: #e74c3c !important;
    font-size: 13px;
    font-weight: 700;
    text-transform: none;
    letter-spacing: 0;
    padding: 12px 10px;
    border-bottom: 2px solid #ecf0f1;
}
#ddCard table.it-table tbody td {
    padding: 11px 10px;
    font-size: 13px;
    color: #e74c3c;
    border-bottom: 1px solid #ecf0f1;
    vertical-align: middle;
}
#ddCard table.it-table thead tr,
#ddCard table.it-table tbody tr { display: table-row !important; }
#ddCard table.it-table thead th,
#ddCard table.it-table tbody td { display: table-cell !important; }

/* ── Loading overlay ────────────────────────────────────── */
.is-overlay {
    position: absolute; inset: 0; background: rgba(255,255,255,.8);
    display: flex; align-items: center; justify-content: center;
    border-radius: 14px; z-index: 20; opacity: 0; pointer-events: none; transition: opacity .2s;
}
.is-overlay.show { opacity: 1; pointer-events: all; }
.is-spin {
    width: 36px; height: 36px; border: 3px solid #e2e8f0;
    border-top-color: #4f46e5; border-radius: 50%;
    animation: isSpin .7s linear infinite;
}
@keyframes isSpin { to { transform: rotate(360deg); } }

/* ── Empty state ────────────────────────────────────────── */
.is-empty { text-align: center; padding: 48px 24px; color: #94a3b8; }
.is-empty i { font-size: 2rem; display: block; margin-bottom: 8px; }
</style>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    <div class="is-wrapper">

      @php
        $summary = $summary ?? ['total_discount' => 0, 'total_cancel' => 0, 'total_refund' => 0];
        $dcrStatus = $dcrStatus ?? 'approved';
        if (! in_array($dcrStatus, ['approved', 'pending', 'rejected'], true)) { $dcrStatus = 'approved'; }
        $selStateIds  = collect(request('state_id',  []))->map(fn($v) => (int)$v)->toArray();
        $selZoneIds   = collect(request('zone_ids',  []))->map(fn($v) => (int)$v)->toArray();
        $selBranchIds = collect(request('branch_ids',[]))->map(fn($v) => (int)$v)->toArray();
      @endphp

      {{-- ── Page header ──────────────────────────────────────────── --}}
      <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
          <div class="is-page-title"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Income Summary</div>
          <div class="is-page-subtitle">Location-wise payment totals (Cash&ndash;UPI). <strong>Total</strong> excludes Discount, Cancel, and Refund columns.</div>
        </div>
        <button class="is-filter-btn" id="isFilterToggle">
          <i class="bi bi-funnel-fill"></i> Filters
          <span id="isFilterBadge" style="display:none;background:rgba(255,255,255,.28);border-radius:10px;padding:1px 7px;font-size:.68rem;margin-left:2px;"></span>
        </button>
      </div>

      {{-- ── Filter panel ─────────────────────────────────────────── --}}
      <div class="is-filter-panel {{ $dateFilter === 'custom' ? 'open' : '' }}" id="isFilterPanel">
        <div class="is-filter-title"><i class="bi bi-sliders"></i> Filter Options</div>

        <div class="row g-3 align-items-start">

          {{-- State --}}
          <div class="col-lg-2 col-md-4 col-sm-6">
            <label class="form-label">State</label>
            <div class="ms-wrap" id="ms-state-wrap">
              <div class="ms-trigger" id="ms-state-trigger">
                <span class="ms-trigger-text ph" id="ms-state-label">All States</span>
                <span class="ms-count" id="ms-state-count" style="display:none;"></span>
                <i class="bi bi-chevron-down ms-trigger-arrow"></i>
              </div>
              <div class="ms-dropdown" id="ms-state-dropdown">
                <div class="ms-search-row">
                  <input type="text" class="ms-search" id="ms-state-search" placeholder="Search…">
                </div>
                <div class="ms-action-row">
                  <span class="ms-act" data-ms="state" data-action="all">All</span>
                  <span class="ms-act clr" data-ms="state" data-action="clear">Clear</span>
                </div>
                <div class="ms-list" id="ms-state-list">
                  @foreach($stateOptions as $s)
                    <div class="ms-opt {{ in_array($s['id'], $selStateIds) ? 'sel' : '' }}"
                         data-ms="state" data-value="{{ $s['id'] }}" data-label="{{ $s['name'] }}">
                      <span class="ms-cb"><i class="bi bi-check"></i></span>{{ $s['name'] }}
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          {{-- Zone --}}
          <div class="col-lg-2 col-md-4 col-sm-6">
            <label class="form-label">Zone</label>
            <div class="ms-wrap" id="ms-zone-wrap">
              <div class="ms-trigger" id="ms-zone-trigger">
                <span class="ms-trigger-text ph" id="ms-zone-label">All Zones</span>
                <span class="ms-count" id="ms-zone-count" style="display:none;"></span>
                <i class="bi bi-chevron-down ms-trigger-arrow"></i>
              </div>
              <div class="ms-dropdown" id="ms-zone-dropdown">
                <div class="ms-search-row">
                  <input type="text" class="ms-search" id="ms-zone-search" placeholder="Search…">
                </div>
                <div class="ms-action-row">
                  <span class="ms-act" data-ms="zone" data-action="all">All</span>
                  <span class="ms-act clr" data-ms="zone" data-action="clear">Clear</span>
                </div>
                <div class="ms-list" id="ms-zone-list">
                  @foreach($zones as $z)
                    <div class="ms-opt {{ in_array($z->id, $selZoneIds) ? 'sel' : '' }}"
                         data-ms="zone" data-value="{{ $z->id }}" data-label="{{ $z->name }}">
                      <span class="ms-cb"><i class="bi bi-check"></i></span>{{ $z->name }}
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          {{-- Branch --}}
          <div class="col-lg-3 col-md-4 col-sm-6">
            <label class="form-label">Branch</label>
            <div class="ms-wrap" id="ms-branch-wrap">
              <div class="ms-trigger" id="ms-branch-trigger">
                <span class="ms-trigger-text ph" id="ms-branch-label">All Branches</span>
                <span class="ms-count" id="ms-branch-count" style="display:none;"></span>
                <i class="bi bi-chevron-down ms-trigger-arrow"></i>
              </div>
              <div class="ms-dropdown" id="ms-branch-dropdown">
                <div class="ms-search-row">
                  <input type="text" class="ms-search" id="ms-branch-search" placeholder="Search…">
                </div>
                <div class="ms-action-row">
                  <span class="ms-act" data-ms="branch" data-action="all">All</span>
                  <span class="ms-act clr" data-ms="branch" data-action="clear">Clear</span>
                </div>
                <div class="ms-list" id="ms-branch-list">
                  @foreach($branchOptions as $b)
                    <div class="ms-opt {{ in_array($b->id, $selBranchIds) ? 'sel' : '' }}"
                         data-ms="branch" data-value="{{ $b->id }}" data-label="{{ $b->name }}" data-zone="{{ $b->zone_id }}">
                      <span class="ms-cb"><i class="bi bi-check"></i></span>{{ $b->name }}
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          {{-- Date presets --}}
          <div class="col-lg-5 col-md-12">
            <label class="form-label">Date Range</label>
            <div class="is-date-presets">
              @foreach([
                'yesterday'     => 'Yesterday',
                'today'         => 'Today',
                'this_month'    => 'This Month',
                'last_2_months' => 'Last 2 Months',
                'last_3_months' => 'Last 3 Months',
                'custom'        => 'Custom',
              ] as $val => $label)
                <span class="is-preset {{ $dateFilter === $val ? 'active' : '' }}" data-val="{{ $val }}">{{ $label }}</span>
              @endforeach
            </div>
            <input type="hidden" name="date_filter" id="dateFilterInput" value="{{ $dateFilter }}">
            <div class="is-date-custom {{ $dateFilter === 'custom' ? 'show' : '' }}" id="customDateRow">
              <div class="fp-wrap">
                <label class="form-label">Start date</label>
                <input type="date" id="start_date" name="start_date" class="form-control"
                       value="{{ $startDate }}" autocomplete="off">
                <i class="bi bi-calendar3 fp-icon"></i>
              </div>
              <div class="fp-wrap">
                <label class="form-label">End date</label>
                <input type="date" id="end_date" name="end_date" class="form-control"
                       value="{{ $endDate }}" autocomplete="off">
                <i class="bi bi-calendar3 fp-icon"></i>
              </div>
            </div>
          </div>

        </div>{{-- /row --}}

        <!-- <div class="row g-3 align-items-start mt-1">
          <div class="col-12">
            <label class="form-label">Discount / Cancel / Refund (HMS)</label>
            <div class="d-flex flex-wrap gap-2 align-items-center" id="dcrStatusGroup" role="group" aria-label="DCR approval filter">
              <span class="is-dcr-opt {{ $dcrStatus === 'approved' ? 'active' : '' }}" data-dcr="approved" title="Final-approved forms only">Approved</span>
              <span class="is-dcr-opt {{ $dcrStatus === 'pending' ? 'active' : '' }}" data-dcr="pending" title="Awaiting approval">Unapproved</span>
              <span class="is-dcr-opt {{ $dcrStatus === 'rejected' ? 'active' : '' }}" data-dcr="rejected" title="Rejected forms">Rejected</span>
            </div>
            <div class="form-text mt-1" style="font-size:0.75rem;color:#64748b;">
              Applies to the three amount columns and to the line list when you open a link. Payment columns still use final-approved billing lines.
            </div>
          </div>
        </div> -->

        <div class="is-btn-row">
          <button class="is-btn-apply" id="isApplyBtn"><i class="bi bi-search"></i> Apply</button>
          <button class="is-btn-reset" id="isResetBtn">Reset</button>
        </div>
      </div>{{-- /filter-panel --}}

      {{-- ── Active chips ──────────────────────────────────────────── --}}
      <div class="is-chips" id="isChips"></div>

      {{-- ── Summary badges ───────────────────────────────────────── --}}
      <div class="is-badges mb-3">
        <span class="is-badge is-badge--total js-is-total" id="badgeTotal" title="Cash through UPI only; excludes Discount, Cancel, Refund">
          <i class="bi bi-currency-rupee"></i> Payment total: ₹ {{ formatIndianMoney($grandTotal) }}
        </span>
        <span class="is-badge is-badge--discount" id="badgeDiscount">
          <i class="bi bi-tag-fill"></i> Discount: ₹ {{ formatIndianMoney($summary['total_discount']) }}
        </span>
        <span class="is-badge is-badge--cancel" id="badgeCancel">
          <i class="bi bi-x-circle-fill"></i> Cancelled: ₹ {{ formatIndianMoney($summary['total_cancel']) }}
        </span>
        <span class="is-badge is-badge--refund" id="badgeRefund">
          <i class="bi bi-arrow-counterclockwise"></i> Refunded: ₹ {{ formatIndianMoney($summary['total_refund']) }}
        </span>
      </div>

      {{-- ── Table card ────────────────────────────────────────────── --}}
      <div class="is-table-card" id="mainTableCard">
        <div class="is-overlay" id="isLoader"><div class="is-spin"></div></div>

        <div class="is-table-toolbar">
          <div class="is-table-title"><i class="bi bi-table me-2 text-muted"></i>Income from All Locations</div>
          <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="is-per-page">
              <label>Show</label>
              <select id="perPage" name="perPage">
                <option value="100" {{ request('perPage','100') == '100' ? 'selected' : '' }}>100</option>
                <option value="50"  {{ request('perPage') == '50' ? 'selected' : '' }}>50</option>
                <option value="25"  {{ request('perPage') == '25' ? 'selected' : '' }}>25</option>
              </select>
              <span>entries</span>
            </div>
            <div class="is-export-btns">
              <button type="button" class="is-btn-exp is-btn-xlsx" id="btnExcel">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
              </button>
              <button type="button" class="is-btn-exp is-btn-csv" id="btnCsv">
                <i class="bi bi-filetype-csv"></i> Export CSV
              </button>
            </div>
          </div>
        </div>

        <div style="overflow-x:auto;">
          <div id="incomeTableContainer">
            @include('vendor.partials.table.income_table_rows', ['incomeData' => $incomeData, 'summary' => $summary])
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-top" style="font-size:.82rem;color:#64748b;">
          <span class="js-is-total is-badge is-badge--total" id="badgeTotalFooter" style="padding:6px 14px;font-size:.82rem;" title="Cash through UPI only; excludes Discount, Cancel, Refund">
            Payment total: ₹ {{ formatIndianMoney($grandTotal) }}
          </span>
        </div>
      </div>

      {{-- ── Drill-down card (hidden by default, replaces main table in-place) --}}
      <div class="is-table-card" id="ddCard" style="display:none;">
        {{-- Toolbar --}}
        <div class="is-table-toolbar" style="flex-wrap:wrap;gap:10px;">
          <div>
            <button id="ddBackBtn" style="
                background:#f1f5f9;border:1px solid #cbd5e1;border-radius:8px;
                padding:7px 16px;font-size:.82rem;font-weight:600;cursor:pointer;
                display:inline-flex;align-items:center;gap:6px;color:#475569;">
              <i class="bi bi-arrow-left"></i> Back to Summary
            </button>
          </div>
          <div>
            <div id="ddTitle" style="font-size:1rem;font-weight:700;color:#1e293b;"></div>
            <div id="ddSubtitle" style="font-size:.78rem;color:#64748b;margin-top:2px;"></div>
          </div>
        </div>

        {{-- D/C/R approval filter (Discount, Cancel, Refund drill only) — AJAX refresh --}}
        <div id="ddDcrFilter" style="display:none;width:100%;padding:12px 20px;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <span style="font-size:0.82rem;font-weight:700;color:#334155;">HMS form status</span>
            <div class="d-flex flex-wrap gap-2 align-items-center" id="ddDcrStatusGroup" role="group" aria-label="Discount cancel refund approval">
              <span class="is-dcr-opt" data-dd-dcr="approved" title="Final approved">Approved</span>
              <span class="is-dcr-opt" data-dd-dcr="pending" title="Awaiting approval">Unapproved</span>
              <span class="is-dcr-opt" data-dd-dcr="rejected" title="Rejected">Rejected</span>
            </div>
          </div>
        </div>

        {{-- IP / OP / Pharmacy summary chips (shown when clicking branch/total) --}}
        <div id="ddTypeGroups" style="display:none;padding:10px 20px;border-bottom:1px solid #f1f5f9;flex-wrap:wrap;gap:8px;"></div>

        {{-- Loading overlay --}}
        <div class="is-overlay" id="ddLoader"><div class="is-spin"></div></div>

        {{-- Drill-down table --}}
        <div style="overflow-x:auto;">
          <div id="ddTableWrap"></div>
        </div>

        {{-- Pagination footer --}}
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-top"
             style="font-size:.82rem;color:#64748b;">
          <span id="ddPageInfo">—</span>
          <div id="ddPagination" style="display:flex;gap:6px;flex-wrap:wrap;"></div>
        </div>
      </div>

      {{-- Hidden form to carry all filter values for export --}}
      <form id="isFilterForm" style="display:none;">
        <input type="hidden" id="hf_date_filter"  name="date_filter"  value="{{ $dateFilter }}">
        <input type="hidden" id="hf_start_date"   name="start_date"   value="{{ $startDate }}">
        <input type="hidden" id="hf_end_date"      name="end_date"     value="{{ $endDate }}">
        <input type="hidden" id="hf_perPage"       name="perPage"      value="{{ request('perPage',100) }}">
      </form>

    </div>{{-- /is-wrapper --}}

  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

<script>
$(function () {

/* ── Initial state from PHP ──────────────────────────────── */
var IS = {
    date_filter: '{{ $dateFilter }}',
    start_date:  '{{ $startDate }}',
    end_date:    '{{ $endDate }}',
    state_ids:   @json($selStateIds),
    zone_ids:    @json($selZoneIds),
    branch_ids:  @json($selBranchIds),
    perPage:     '{{ request('perPage', 100) }}',
    dcr_status:  '{{ $dcrStatus }}'
};

var URL_REPORT = '{{ route("superadmin.vendorincomeReport") }}';
var URL_EXPORT = '{{ route("incomeSummary.export") }}';

var allBranches = @json($branchOptions->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'zone_id' => $b->zone_id]));

/* ── Multi-select config ─────────────────────────────────── */
var msCfg = {
    state:  { key: 'state_ids',  ph: 'All States',    label: 'State'   },
    zone:   { key: 'zone_ids',   ph: 'All Zones',     label: 'Zone'    },
    branch: { key: 'branch_ids', ph: 'All Branches',  label: 'Branch'  }
};

function msSync(name) {
    var vals = IS[msCfg[name].key].map(String);
    $('#ms-'+name+'-list .ms-opt').each(function(){
        $(this).toggleClass('sel', vals.indexOf(String($(this).data('value'))) > -1);
    });
    var $lbl = $('#ms-'+name+'-label');
    var $cnt = $('#ms-'+name+'-count');
    if (!vals.length) {
        $lbl.text(msCfg[name].ph).addClass('ph');
        $cnt.hide();
    } else if (vals.length === 1) {
        var lbl = $('#ms-'+name+'-list .ms-opt[data-value="'+vals[0]+'"]').data('label') || vals[0];
        $lbl.text(lbl).removeClass('ph');
        $cnt.hide();
    } else {
        $lbl.text(vals.length+' selected').removeClass('ph');
        $cnt.text(vals.length).show();
    }
}

/* Rebuild branch dropdown filtered by selected zones */
function refreshBranchList() {
    var selZones = IS.zone_ids.map(String);
    var filtered = selZones.length ? allBranches.filter(function(b){ return selZones.indexOf(String(b.zone_id)) > -1; }) : allBranches;
    var $list = $('#ms-branch-list').empty();
    filtered.forEach(function(b){
        var isSel = IS.branch_ids.map(String).indexOf(String(b.id)) > -1;
        $list.append(
            '<div class="ms-opt'+(isSel?' sel':'')+'" data-ms="branch" data-value="'+b.id+'" data-label="'+b.name+'" data-zone="'+b.zone_id+'">' +
            '<span class="ms-cb"><i class="bi bi-check"></i></span>'+b.name+'</div>'
        );
    });
    /* Drop branch IDs no longer in list */
    var valid = filtered.map(function(b){ return String(b.id); });
    IS.branch_ids = IS.branch_ids.filter(function(id){ return valid.indexOf(String(id)) > -1; });
    msSync('branch');
}

/* Initial sync */
msSync('state'); msSync('zone'); msSync('branch');
refreshBranchList();

/* Open / close */
$(document).on('click', '.ms-trigger', function(e){
    e.stopPropagation();
    var name = this.id.replace('ms-','').replace('-trigger','');
    var $d = $('#ms-'+name+'-dropdown'), was = $d.hasClass('open');
    $('.ms-dropdown.open').removeClass('open');
    $('.ms-trigger.open').removeClass('open');
    if (!was) {
        $d.addClass('open');
        $(this).addClass('open');
        $('#ms-'+name+'-search').val('').trigger('input').focus();
    }
});
$(document).on('click', function(e){
    if (!$(e.target).closest('.ms-wrap').length) {
        $('.ms-dropdown.open').removeClass('open');
        $('.ms-trigger.open').removeClass('open');
    }
});
$(document).on('click', '.ms-dropdown', function(e){ e.stopPropagation(); });

/* Toggle option */
$(document).on('click', '.ms-opt', function(e){
    e.stopPropagation();
    var name = $(this).data('ms');
    var val  = String($(this).data('value'));
    var arr  = IS[msCfg[name].key].map(String);
    var idx  = arr.indexOf(val);
    if (idx > -1) arr.splice(idx,1); else arr.push(val);
    IS[msCfg[name].key] = arr;
    msSync(name);
    if (name === 'zone') refreshBranchList();
});

/* Select all / clear */
$(document).on('click', '.ms-act', function(e){
    e.stopPropagation();
    var name = $(this).data('ms'), action = $(this).data('action');
    if (action === 'all') {
        var all = [];
        $('#ms-'+name+'-list .ms-opt:visible').each(function(){ all.push(String($(this).data('value'))); });
        IS[msCfg[name].key] = all;
    } else {
        IS[msCfg[name].key] = [];
    }
    msSync(name);
    if (name === 'zone') refreshBranchList();
});

/* Inline search */
$(document).on('input', '.ms-search', function(){
    var name = this.id.replace('ms-','').replace('-search','');
    var q = $(this).val().toLowerCase();
    $('#ms-'+name+'-list .ms-opt').each(function(){
        $(this).toggle((String($(this).data('label')||$(this).text())).toLowerCase().indexOf(q) > -1);
    });
});

/* ── Filter panel toggle ──────────────────────────────────── */
var filterOpen = {{ $dateFilter === 'custom' ? 'true' : 'false' }};
$('#isFilterToggle').on('click', function(){
    filterOpen = !filterOpen;
    if (filterOpen) $('#isFilterPanel').addClass('open');
    else { $('#isFilterPanel').removeClass('open'); $('.ms-dropdown.open').removeClass('open'); $('.ms-trigger.open').removeClass('open'); }
});

/* ── Date presets ────────────────────────────────────────── */
$(document).on('click', '.is-preset', function(){
    $('.is-preset').removeClass('active');
    $(this).addClass('active');
    var val = $(this).data('val');
    IS.date_filter = val;
    $('#dateFilterInput').val(val);
    if (val === 'custom') {
        $('#customDateRow').addClass('show');
    } else {
        $('#customDateRow').removeClass('show');
        IS.start_date = '';
        IS.end_date = '';
        $('#start_date').val('');
        $('#end_date').val('');
    }
});

$('#start_date').on('change', function(){ IS.start_date = $(this).val(); IS.date_filter = 'custom'; });
$('#end_date').on('change',   function(){ IS.end_date   = $(this).val(); IS.date_filter = 'custom'; });
$('#perPage').on('change', function(){ IS.perPage = $(this).val(); doFetch(); });

$(document).on('click', '#dcrStatusGroup .is-dcr-opt', function(){
    var v = $(this).data('dcr');
    if (!v || v === IS.dcr_status) return;
    IS.dcr_status = v;
    $('#dcrStatusGroup .is-dcr-opt').removeClass('active');
    $(this).addClass('active');
    buildChips();
    updateFilterBadge();
    doFetch();
});

/* ── Apply & Reset ──────────────────────────────────────── */
$('#isApplyBtn').on('click', function(){
    IS.date_filter = $('#dateFilterInput').val();
    IS.start_date  = $('#start_date').val();
    IS.end_date    = $('#end_date').val();
    doFetch();
    filterOpen = false;
    $('#isFilterPanel').removeClass('open');
    $('.ms-dropdown.open').removeClass('open');
    $('.ms-trigger.open').removeClass('open');
    buildChips();
    updateFilterBadge();
});

$('#isResetBtn').on('click', function(){
    IS.state_ids = []; IS.zone_ids = []; IS.branch_ids = [];
    IS.date_filter = 'yesterday'; IS.start_date = ''; IS.end_date = '';
    IS.perPage = 100;
    IS.dcr_status = 'approved';
    $('#dcrStatusGroup .is-dcr-opt').removeClass('active').filter('[data-dcr="approved"]').addClass('active');
    $('#start_date').val(''); $('#end_date').val('');
    $('.is-preset').removeClass('active').filter('[data-val="yesterday"]').addClass('active');
    $('#dateFilterInput').val('yesterday');
    $('#customDateRow').removeClass('show');
    $('#perPage').val('100');
    msSync('state'); msSync('zone');
    refreshBranchList();
    doFetch();
    buildChips();
    updateFilterBadge();
});

/* ── Format helpers ──────────────────────────────────────── */
function fmtInr(n) {
    return '₹\u00a0' + Number(n||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
}

/* ── Build query params ───────────────────────────────────── */
function buildParams(page) {
    var p = {
        date_filter: IS.date_filter || 'yesterday',
        perPage:     IS.perPage || 100,
        page:        page || 1
    };
    if (IS.start_date) p.start_date = IS.start_date;
    if (IS.end_date)   p.end_date   = IS.end_date;
    p.dcr_status = IS.dcr_status || 'approved';
    IS.state_ids.forEach(function(id,i)  { p['state_id['+i+']']   = id; });
    IS.zone_ids.forEach(function(id,i)   { p['zone_ids['+i+']']   = id; });
    IS.branch_ids.forEach(function(id,i) { p['branch_ids['+i+']'] = id; });
    return p;
}

/* ── AJAX fetch ──────────────────────────────────────────── */
function doFetch(page) {
    var params = buildParams(page || 1);
    $('#isLoader').addClass('show');
    $('#incomeTableContainer').css('opacity', '.5');
    $.ajax({
        url: URL_REPORT,
        type: 'GET',
        data: params,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(resp) {
            if (resp && resp.html !== undefined) {
                $('#incomeTableContainer').html(resp.html).css('opacity','1');
                var totalTxt = 'Payment total: ₹\u00a0' + Number(resp.total||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
                $('.js-is-total').text(totalTxt);
                if (resp.summary) {
                    $('#badgeDiscount').html('<i class="bi bi-tag-fill"></i> Discount: ' + fmtInr(resp.summary.total_discount));
                    $('#badgeCancel').html('<i class="bi bi-x-circle-fill"></i> Cancelled: ' + fmtInr(resp.summary.total_cancel));
                    $('#badgeRefund').html('<i class="bi bi-arrow-counterclockwise"></i> Refunded: ' + fmtInr(resp.summary.total_refund));
                }
            }
        },
        complete: function() {
            $('#isLoader').removeClass('show');
            $('#incomeTableContainer').css('opacity','1');
        }
    });
}

/* ── Pagination ──────────────────────────────────────────── */
$(document).on('click', '.pagination a', function(e){
    e.preventDefault();
    var href = $(this).attr('href') || '';
    var page = 1;
    try {
        var u = new URL(href, window.location.origin);
        if (u.searchParams.get('page')) page = parseInt(u.searchParams.get('page'));
    } catch(err) {}
    doFetch(page);
});

/* ══════════════════════════════════════════════════════════
   DRILL-DOWN — inline replace (same card, no modal)
══════════════════════════════════════════════════════════ */
var DD = { locId:'', locName:'', payType:'', page:1, lastPage:1 };
var URL_DRILLDOWN = '{{ route("incomeSummary.drilldown") }}';

function ddDcrStatusLabel(s) {
    if (s === 'pending') return 'Unapproved';
    if (s === 'rejected') return 'Rejected';
    return 'Approved';
}

function ddIsDcrDrill() {
    var p = DD.payType || '';
    return p === 'Discount' || p === 'Cancel' || p === 'Refund';
}

/** Show D/C/R pills only for HMS drill views; sync active pill from IS.dcr_status */
function ddUpdateDcrToolbar() {
    if (!ddIsDcrDrill()) {
        $('#ddDcrFilter').hide();
        return;
    }
    $('#ddDcrFilter').show();
    var st = IS.dcr_status || 'approved';
    $('#ddDcrStatusGroup .is-dcr-opt').removeClass('active');
    $('#ddDcrStatusGroup .is-dcr-opt[data-dd-dcr="' + st + '"]').addClass('active');
}

/** Subtitle under branch name — reflects current D/C/R filter for HMS drills */
function ddRefreshSubtitle() {
    var sub;
    if (!DD.payType) {
        sub = 'OP · IP · Pharmacy — Cash, Card, UPI, … by category';
    } else if (DD.payType === 'Discount') {
        sub = 'Discount — ' + ddDcrStatusLabel(IS.dcr_status).toLowerCase() + ' (HMS forms)';
    } else if (DD.payType === 'Cancel') {
        sub = 'Cancel — ' + ddDcrStatusLabel(IS.dcr_status).toLowerCase() + ' (HMS forms)';
    } else if (DD.payType === 'Refund') {
        sub = 'Refund — ' + ddDcrStatusLabel(IS.dcr_status).toLowerCase() + ' (HMS forms)';
    } else {
        sub = 'Bills paid via ' + DD.payType;
    }
    $('#ddSubtitle').text(sub);
}

$(document).on('click', '#ddDcrStatusGroup .is-dcr-opt', function() {
    var v = $(this).data('dd-dcr');
    if (v === undefined || v === '' || v === IS.dcr_status) return;
    IS.dcr_status = v;
    $('#dcrStatusGroup .is-dcr-opt').removeClass('active');
    $('#dcrStatusGroup .is-dcr-opt[data-dcr="' + v + '"]').addClass('active');
    ddUpdateDcrToolbar();
    ddRefreshSubtitle();
    buildChips();
    updateFilterBadge();
    ddFetch(1);
    doFetch(1);
});

function ddOpen() {
    $('#mainTableCard').hide();
    $('#ddCard').show();
    $('html,body').animate({ scrollTop: $('#ddCard').offset().top - 20 }, 200);
}
function ddClose() { $('#ddCard').hide(); $('#mainTableCard').show(); }
$('#ddBackBtn').on('click', ddClose);

/* Fetch drill-down data */
function ddFetch(page) {
    DD.page = page || 1;
    ddUpdateDcrToolbar();
    ddRefreshSubtitle();
    var params = {
        location_id:   DD.locId,
        location_name: DD.locName,
        payment_type:  DD.payType,
        date_filter:   IS.date_filter || 'yesterday',
        start_date:    IS.start_date || '',
        end_date:      IS.end_date || '',
        page:          DD.page,
        dcr_status:    IS.dcr_status || 'approved'
    };

    $('#ddLoader').addClass('show');
    $('#ddTableWrap').empty();
    $('#ddTypeGroups').hide().empty();

    $.ajax({
        url: URL_DRILLDOWN,
        type: 'GET',
        data: params,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(res) {
            DD.lastPage = res.last_page;

            /* Branch / Total: OP · IP · Pharmacy × payment matrix */
            if (res.view === 'matrix') {
                $('#ddTypeGroups').hide().empty();
                $('#ddPagination').empty();
                $('#ddPageInfo').text('');

                var mRows = res.matrix_rows || [];
                if (!mRows.length) {
                    $('#ddTableWrap').html(
                        '<div style="text-align:center;padding:48px;color:#94a3b8;">'
                        +'<i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>'
                        +'<p style="margin:0;">No approved income rows for this branch in the selected period.</p></div>');
                    return;
                }

                var mth = 'padding:11px 10px;text-align:left;font-weight:700;color:#e74c3c;border-bottom:2px solid #ecf0f1;font-size:13px;background:#fff;white-space:nowrap;';
                var mtd = 'padding:11px 10px;font-size:13px;color:#e74c3c;vertical-align:middle;white-space:nowrap;';
                var mtdR = mtd + 'text-align:right;font-weight:600;';
                var hdrs = ['Cash','Card','Cheque','DD','NEFT','Credit','UPI','Total'];
                var tbl = '<div class="table-responsive"><table style="width:100%;border-collapse:collapse;" class="it-table">'
                    + '<thead><tr><th style="'+mth+'"> </th>';
                hdrs.forEach(function(h) {
                    tbl += '<th style="'+mth+'text-align:right;">'+esc(h)+'</th>';
                });
                tbl += '</tr></thead><tbody>';

                function fmtM(x) {
                    return Number(x||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
                }
                mRows.forEach(function(r, idx) {
                    var bg = idx % 2 === 0 ? '#fff' : '#fafafa';
                    var lab = r.category === 'Pharmacy' ? 'Pharmacy' : r.category;
                    tbl += '<tr style="background:'+bg+';border-bottom:1px solid #ecf0f1;">'
                        + '<td style="'+mtd+'color:#1e293b;font-weight:700;">'+esc(lab)+'</td>'
                        + '<td style="'+mtdR+'">'+fmtM(r.cash)+'</td>'
                        + '<td style="'+mtdR+'">'+fmtM(r.card)+'</td>'
                        + '<td style="'+mtdR+'">'+fmtM(r.cheque)+'</td>'
                        + '<td style="'+mtdR+'">'+fmtM(r.dd)+'</td>'
                        + '<td style="'+mtdR+'">'+fmtM(r.neft)+'</td>'
                        + '<td style="'+mtdR+'">'+fmtM(r.credit)+'</td>'
                        + '<td style="'+mtdR+'">'+fmtM(r.upi)+'</td>'
                        + '<td style="'+mtdR+'color:#27ae60;font-weight:800;">'+fmtM(r.line_total)+'</td>'
                        + '</tr>';
                });
                tbl += '<tr style="background:#f8f9fa;border-top:2px solid #ecf0f1;">'
                    + '<td style="'+mtd+'font-weight:800;color:#1e293b;">Grand Total</td>'
                    + '<td colspan="7" style="'+mtd+'"></td>'
                    + '<td style="'+mtdR+'color:#15803d;font-weight:800;">'+fmtM(res.matrix_grand_total)+'</td>'
                    + '</tr>';
                tbl += '</tbody></table></div>';
                $('#ddTableWrap').html(tbl);
                return;
            }

            /* Build detail table */
            if (!res.rows || !res.rows.length) {
                $('#ddTableWrap').html(
                    '<div style="text-align:center;padding:48px;color:#94a3b8;">'
                    +'<i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>'
                    +'<p style="margin:0;">No records found.</p></div>');
            } else {
                var dk = res.drill_kind || '';
                var amtHdr = 'Amount';
                var amtColor = '#e74c3c';
                var footLbl = 'Total';
                if (dk === 'discount') { amtHdr = 'Discount'; amtColor = '#e67e22'; footLbl = 'Total discount'; }
                if (dk === 'cancel')   { amtHdr = 'Cancel';   amtColor = '#c0392b'; footLbl = 'Total cancel'; }
                if (dk === 'refund')   { amtHdr = 'Refund';   amtColor = '#8e44ad'; footLbl = 'Total refund'; }

                var tbl = '<div class="table-responsive">'
                    + '<table class="it-table" style="width:100%;border-collapse:collapse;">'
                    + '<thead><tr>'
                    + '<th style="'+thS+'">S.No</th>'
                    + '<th style="'+thS+'">Location</th>'
                    + '<th style="'+thS+'">Date</th>'
                    + '<th style="'+thS+'">Bill No</th>'
                    + '<th style="'+thS+'">PHID</th>'
                    + '<th style="'+thS+'">Patient Name</th>'
                    + '<th style="'+thS+'">Consultant</th>'
                    + '<th style="'+thS+'text-align:right;">'+esc(amtHdr)+'</th>'
                    + '<th style="'+thS+'">BillType</th>'
                    + '<th style="'+thS+'">PaymentType</th>'
                    + '<th style="'+thS+'">User</th>'
                    + '</tr></thead><tbody>';

                res.rows.forEach(function(r, idx) {
                    var rowBg = idx % 2 === 0 ? '#fff' : '#fafafa';
                    tbl += '<tr style="background:'+rowBg+';border-bottom:1px solid #ecf0f1;">'
                        + '<td style="'+tdS+'">'+r.sno+'</td>'
                        + '<td style="'+tdS+'color:#1e293b;font-weight:600;">'+esc(r.location)+'</td>'
                        + '<td style="'+tdS+'">'+esc(r.date)+'</td>'
                        + '<td style="'+tdS+'color:#e74c3c;font-weight:600;">'+esc(r.bill_no)+'</td>'
                        + '<td style="'+tdS+'">'+esc(r.phid)+'</td>'
                        + '<td style="'+tdS+'color:#1e293b;font-weight:600;">'+esc(r.patient)+'</td>'
                        + '<td style="'+tdS+'">'+esc(r.consultant)+'</td>'
                        + '<td style="'+tdS+'text-align:right;font-weight:700;color:'+amtColor+';">'+esc(r.amount)+'</td>'
                        + '<td style="'+tdS+'color:#e74c3c;">'+esc(r.bill_type)+'</td>'
                        + '<td style="'+tdS+'">'+esc(r.pay_type)+'</td>'
                        + '<td style="'+tdS+'">'+esc(r.user)+'</td>'
                        + '</tr>';
                });

                tbl += '<tr class="it-footer" style="background:#f8f9fa;border-top:2px solid #ecf0f1;">'
                     + '<td colspan="7" style="'+tdS+'font-weight:700;color:#1e293b;">'+esc(footLbl)+'</td>'
                     + '<td style="'+tdS+'text-align:right;font-weight:800;color:'+amtColor+';">'
                     + Number(res.grand_total||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2})
                     + '</td><td colspan="3" style="'+tdS+'"></td></tr>';
                tbl += '</tbody></table></div>';
                $('#ddTableWrap').html(tbl);
            }

            /* Page info */
            var from = (res.page - 1) * res.per_page + 1;
            var to   = Math.min(res.page * res.per_page, res.total);
            $('#ddPageInfo').text('Showing ' + from + '–' + to + ' of ' + res.total + ' records');

            /* Pagination */
            buildDdPagination(res.page, res.last_page);
        },
        complete: function() {
            $('#ddLoader').removeClass('show');
        }
    });
}

/* Detail table styles — same red-text design as main table */
var thS = 'padding:11px 10px;text-align:left;font-weight:700;color:#e74c3c;border-bottom:2px solid #ecf0f1;font-size:13px;white-space:nowrap;background:#fff;';
var tdS = 'padding:11px 10px;font-size:13px;color:#e74c3c;vertical-align:middle;white-space:nowrap;';

function esc(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function buildDdPagination(cur, last) {
    var $pg = $('#ddPagination').empty();
    if (last <= 1) return;

    var pages = [];
    for (var p = 1; p <= last; p++) {
        if (p === 1 || p === last || (p >= cur - 2 && p <= cur + 2)) {
            pages.push(p);
        } else if (pages[pages.length-1] !== '…') {
            pages.push('…');
        }
    }
    pages.forEach(function(p) {
        if (p === '…') {
            $pg.append('<span style="padding:0 4px;color:#94a3b8;">…</span>');
            return;
        }
        var active = p === cur;
        var btn = $('<button style="min-width:32px;height:32px;border-radius:6px;border:1px solid '
            + (active ? '#4f46e5' : '#e2e8f0') + ';background:'
            + (active ? '#4f46e5' : '#fff') + ';color:'
            + (active ? '#fff' : '#475569') + ';font-size:.8rem;font-weight:600;cursor:'
            + (active ? 'default' : 'pointer') + ';padding:0 6px;">' + p + '</button>');
        if (!active) {
            btn.on('click', (function(pp){ return function(){ ddFetch(pp); }; })(p));
        }
        $pg.append(btn);
    });
}

/* Trigger drill-down on cell click */
$(document).on('click', '[data-action="drilldown"]', function() {
    DD.locId   = $(this).attr('data-loc-id') || '';
    DD.locName = $(this).attr('data-loc-name') || '';
    DD.payType = $(this).attr('data-pay-type') || '';

    $('#ddTitle').text(DD.locName);
    ddRefreshSubtitle();
    ddUpdateDcrToolbar();

    ddOpen();
    ddFetch(1);
});

/* ── Export ──────────────────────────────────────────────── */
function doExport(type) {
    var params = buildParams(1);
    params.export_type = (type === 'csv') ? 'csv' : 'excel';
    $.ajax({
        url: URL_EXPORT,
        type: 'GET',
        data: params,
        xhrFields: { responseType: 'blob' },
        success: function(data, status, xhr) {
            var blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') });
            var fileName = 'income_report';
            var cd = xhr.getResponseHeader('content-disposition');
            if (cd && cd.indexOf('filename=') > -1) {
                fileName = cd.split('filename=')[1].replace(/"/g,'');
            }
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            a.remove();
        },
        error: function(){ toastr.error('Export failed, please try again.'); }
    });
}
$('#btnCsv').on('click', function(){ doExport('csv'); });
$('#btnExcel').on('click', function(){ doExport('excel'); });

/* ── Active filter chips ─────────────────────────────────── */
function buildChips() {
    var $c = $('#isChips').empty();
    var hasAny = false;

    if (IS.state_ids.length) {
        hasAny = true;
        var names = IS.state_ids.map(function(id){
            return $('#ms-state-list .ms-opt[data-value="'+id+'"]').data('label') || id;
        }).join(', ');
        $c.append('<span class="is-chip">State: '+names+' <span class="rm" data-key="state_ids">×</span></span>');
    }
    if (IS.zone_ids.length) {
        hasAny = true;
        var names = IS.zone_ids.map(function(id){
            return $('#ms-zone-list .ms-opt[data-value="'+id+'"]').data('label') || id;
        }).join(', ');
        $c.append('<span class="is-chip">Zone: '+names+' <span class="rm" data-key="zone_ids">×</span></span>');
    }
    if (IS.branch_ids.length) {
        hasAny = true;
        var names = IS.branch_ids.map(function(id){
            return $('#ms-branch-list .ms-opt[data-value="'+id+'"]').data('label') || id;
        }).join(', ');
        $c.append('<span class="is-chip">Branch: '+names+' <span class="rm" data-key="branch_ids">×</span></span>');
    }
    if (IS.date_filter && IS.date_filter !== 'yesterday') {
        hasAny = true;
        var dl = IS.date_filter.replace(/_/g,' ').replace(/\b\w/g,function(c){ return c.toUpperCase(); });
        if (IS.date_filter === 'custom') {
            dl = (IS.start_date||'?') + ' → ' + (IS.end_date||'?');
        }
        $c.append('<span class="is-chip">Date: '+dl+' <span class="rm" data-key="date_filter">×</span></span>');
    }
    if (IS.dcr_status && IS.dcr_status !== 'approved') {
        hasAny = true;
        var dcrLbl = IS.dcr_status === 'pending' ? 'Unapproved' : (IS.dcr_status === 'rejected' ? 'Rejected' : 'Approved');
        $c.append('<span class="is-chip">D/C/R: '+dcrLbl+' <span class="rm" data-key="dcr_status">×</span></span>');
    }
    if (hasAny) {
        $c.append('<span class="is-chip is-chip-clear" id="clearAllChip">Clear All ×</span>');
    }
}

$(document).on('click', '.rm', function(){
    var key = $(this).data('key');
    if (key === 'state_ids')   { IS.state_ids = []; msSync('state'); }
    if (key === 'zone_ids')    { IS.zone_ids = []; msSync('zone'); refreshBranchList(); }
    if (key === 'branch_ids')  { IS.branch_ids = []; msSync('branch'); }
    if (key === 'date_filter') {
        IS.date_filter = 'yesterday'; IS.start_date = ''; IS.end_date = '';
        $('.is-preset').removeClass('active').filter('[data-val="yesterday"]').addClass('active');
        $('#customDateRow').removeClass('show');
        $('#start_date,#end_date').val('');
    }
    if (key === 'dcr_status') {
        IS.dcr_status = 'approved';
        $('#dcrStatusGroup .is-dcr-opt').removeClass('active').filter('[data-dcr="approved"]').addClass('active');
    }
    buildChips();
    updateFilterBadge();
    doFetch();
});

$(document).on('click', '#clearAllChip', function(){
    IS.state_ids = []; IS.zone_ids = []; IS.branch_ids = [];
    IS.date_filter = 'yesterday'; IS.start_date = ''; IS.end_date = '';
    IS.dcr_status = 'approved';
    $('#dcrStatusGroup .is-dcr-opt').removeClass('active').filter('[data-dcr="approved"]').addClass('active');
    msSync('state'); msSync('zone');
    refreshBranchList();
    $('.is-preset').removeClass('active').filter('[data-val="yesterday"]').addClass('active');
    $('#customDateRow').removeClass('show');
    $('#start_date,#end_date').val('');
    buildChips();
    updateFilterBadge();
    doFetch();
});

function updateFilterBadge() {
    var count = IS.state_ids.length + IS.zone_ids.length + IS.branch_ids.length;
    if (IS.date_filter && IS.date_filter !== 'yesterday') count++;
    if (IS.dcr_status && IS.dcr_status !== 'approved') count++;
    var $b = $('#isFilterBadge');
    if (count) { $b.text(count).show(); } else { $b.hide(); }
}

/* Initial chips from URL state */
buildChips();
updateFilterBadge();

});
</script>

@include('superadmin.superadminfooter')
</body>
</html>
