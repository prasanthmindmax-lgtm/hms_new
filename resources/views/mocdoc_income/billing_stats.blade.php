<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    /* ── Layout ─────────────────────────────────────── */
    .stats-wrapper { padding: 24px; }

    /* ── Page header ─────────────────────────────────── */
    .page-title    { font-size: 1.6rem; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .page-subtitle { font-size: 0.85rem; color: #64748b; margin-bottom: 24px; }

    /* ── Header action buttons ───────────────────────── */
    .filter-toggle-btn {
        background: #4f46e5; color: #fff; border: none;
        border-radius: 8px; padding: 9px 20px; font-size: 0.875rem;
        font-weight: 600; cursor: pointer; display: inline-flex;
        align-items: center; gap: 8px; transition: background .2s;
    }
    .filter-toggle-btn:hover { background: #4338ca; }
    .stats-toggle-btn {
        background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;
        border-radius: 8px; padding: 9px 20px; font-size: 0.875rem;
        font-weight: 600; cursor: pointer; display: inline-flex;
        align-items: center; gap: 8px; transition: all .2s;
    }
    .stats-toggle-btn:hover { background: #e2e8f0; }
    .stats-toggle-btn.active { background: #4f46e5; color: #fff; border-color: #4f46e5; }

    /* ── Filter Panel ────────────────────────────────── */
    .filter-panel {
        background: #fff; border: 1px solid #e2e8f0;
        border-radius: 12px; padding: 24px;
        margin-bottom: 24px; display: none;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        animation: slideDown .2s ease;
    }
    .filter-panel.open { display: block; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .filter-panel .filter-title {
        font-size: 0.95rem; font-weight: 700;
        color: #1e293b; margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px;
    }
    .filter-panel .form-label   { font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .filter-panel .form-control,
    .filter-panel .form-select  {
        border-radius: 8px; border: 1px solid #cbd5e1;
        font-size: 0.875rem; padding: 8px 12px;
        transition: border-color .2s;
    }
    .filter-panel .form-control:focus,
    .filter-panel .form-select:focus {
        border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); outline: none;
    }
    .filter-btn-row { display: flex; gap: 10px; align-items: center; margin-top: 8px; }
    .btn-apply { background: #4f46e5; color: #fff; border: none; border-radius: 8px; padding: 9px 22px; font-weight: 600; font-size: 0.875rem; cursor: pointer; }
    .btn-apply:hover { background: #4338ca; }
    .btn-reset { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 18px; font-weight: 600; font-size: 0.875rem; cursor: pointer; }
    .btn-reset:hover { background: #e2e8f0; }

    /* flatpickr calendar icon wrapper */
    .fp-wrap { position: relative; }
    .fp-wrap .fp-icon { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 0.85rem; }
    .fp-wrap .form-control { padding-right: 32px; cursor: pointer !important; }

    /* ── Multi-Select Dropdown ───────────────────────── */
    .ms-wrap     { position: relative; }
    .ms-trigger  {
        display: flex; align-items: center; justify-content: space-between;
        border: 1px solid #cbd5e1; border-radius: 8px;
        padding: 8px 12px; font-size: 0.875rem; background: #fff;
        cursor: pointer; min-height: 40px; user-select: none;
        transition: border-color .2s; gap: 6px;
    }
    .ms-trigger:hover, .ms-trigger.open { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
    .ms-trigger-text { flex: 1; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; color: #334155; }
    .ms-trigger-text.ph { color: #94a3b8; }
    .ms-trigger-arrow { color: #94a3b8; font-size: 0.7rem; transition: transform .2s; flex-shrink: 0; }
    .ms-trigger.open .ms-trigger-arrow { transform: rotate(180deg); }
    .ms-count    { background: #4f46e5; color: #fff; border-radius: 10px; font-size: 0.65rem; font-weight: 700; padding: 1px 6px; flex-shrink: 0; }
    .ms-dropdown {
        position: absolute; top: calc(100% + 4px); left: 0; right: 0; min-width: 220px;
        background: #fff; border: 1px solid #cbd5e1; border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,.12); z-index: 1000;
        display: none; flex-direction: column; max-height: 240px; overflow: hidden;
    }
    .ms-dropdown.open { display: flex; }
    .ms-search-row { padding: 8px; border-bottom: 1px solid #f1f5f9; flex-shrink: 0; }
    .ms-search {
        width: 100%; border: 1px solid #cbd5e1; border-radius: 6px;
        padding: 5px 10px; font-size: 0.8rem; outline: none;
    }
    .ms-search:focus { border-color: #4f46e5; }
    .ms-action-row { display: flex; gap: 6px; padding: 5px 8px; border-bottom: 1px solid #f1f5f9; flex-shrink: 0; }
    .ms-act { font-size: 0.72rem; font-weight: 600; color: #4f46e5; cursor: pointer; padding: 2px 6px; border-radius: 4px; }
    .ms-act:hover { background: #ede9fe; }
    .ms-act.clr { color: #dc2626; }
    .ms-act.clr:hover { background: #fee2e2; }
    .ms-list { overflow-y: auto; flex: 1; }
    .ms-opt {
        display: flex; align-items: center; gap: 8px;
        padding: 7px 10px; cursor: pointer; font-size: 0.82rem; color: #475569;
        transition: background .1s;
    }
    .ms-opt:hover { background: #f8fafc; }
    .ms-opt.sel   { background: #ede9fe; color: #4f46e5; font-weight: 600; }
    .ms-cb {
        width: 14px; height: 14px; border: 1.5px solid #cbd5e1; border-radius: 3px;
        flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 0.6rem;
        transition: all .1s;
    }
    .ms-opt.sel .ms-cb { background: #4f46e5; border-color: #4f46e5; color: #fff; }

    /* ── Active Filter Chips ─────────────────────────── */
    .active-filters { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .filter-chip {
        background: #ede9fe; color: #4f46e5; border-radius: 20px;
        padding: 4px 12px; font-size: 0.78rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .filter-chip .remove { cursor: pointer; font-size: 1rem; line-height: 1; }
    .filter-chip.clear-chip { background: #fee2e2; color: #dc2626; cursor: pointer; text-decoration: none; }

    /* ── Stats Section ───────────────────────────────── */
    .stats-section { overflow: hidden; transition: max-height .38s ease, opacity .28s ease, margin .3s ease; max-height: 1000px; opacity: 1; margin-bottom: 0; }
    .stats-section.hidden { max-height: 0; opacity: 0; margin-bottom: 0 !important; }

    /* ── Stat Cards ──────────────────────────────────── */
    .stat-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px; margin-bottom: 24px;
    }
    .stat-card {
        background: #fff; border-radius: 14px;
        padding: 20px 22px; border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
        position: relative; overflow: hidden;
        transition: transform .15s, box-shadow .15s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.08); }
    .stat-card .card-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; margin-bottom: 14px;
    }
    .stat-card .card-label { font-size: 0.78rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
    .stat-card .card-value { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-top: 2px; }
    .stat-card .card-sub   { font-size: 0.78rem; color: #64748b; margin-top: 2px; }
    .stat-card .card-bar   { position: absolute; bottom: 0; left: 0; right: 0; height: 4px; border-radius: 0 0 14px 14px; }

    .card-total .card-icon { background: #ede9fe; color: #7c3aed; } .card-total .card-bar { background: #7c3aed; }
    .card-cash  .card-icon { background: #dcfce7; color: #16a34a; } .card-cash  .card-bar { background: #16a34a; }
    .card-upi   .card-icon { background: #fef3c7; color: #d97706; } .card-upi   .card-bar { background: #d97706; }
    .card-neft  .card-icon { background: #dbeafe; color: #2563eb; } .card-neft  .card-bar { background: #2563eb; }
    .card-card  .card-icon { background: #fce7f3; color: #db2777; } .card-card  .card-bar { background: #db2777; }
    .card-other .card-icon { background: #f1f5f9; color: #475569; } .card-other .card-bar { background: #475569; }

    /* ── Type / Location Breakdown ───────────────────── */
    .type-breakdown, .loc-breakdown {
        background: #fff; border-radius: 14px; border: 1px solid #e2e8f0;
        padding: 22px 24px; margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
    }
    .loc-breakdown { max-height: 340px; overflow-y: auto; }
    .type-breakdown h6, .loc-breakdown h6 { font-size: 0.9rem; font-weight: 700; color: #1e293b; margin-bottom: 18px; }
    .type-item { margin-bottom: 14px; }
    .type-item-header { display: flex; justify-content: space-between; margin-bottom: 5px; }
    .type-item-header .type-name  { font-size: 0.82rem; font-weight: 600; color: #475569; }
    .type-item-header .type-value { font-size: 0.82rem; font-weight: 700; color: #1e293b; }
    .progress { height: 8px; border-radius: 6px; background: #f1f5f9; overflow: hidden; }
    .progress-bar { border-radius: 6px; height: 100%; transition: width .5s ease; }
    .loc-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.82rem; }
    .loc-item:last-child { border-bottom: none; }
    .loc-name  { color: #475569; font-weight: 600; }
    .loc-value { color: #1e293b; font-weight: 800; }
    .loc-count { color: #94a3b8; font-size: 0.75rem; }

    /* ── Export Bar ──────────────────────────────────── */
    .export-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
    .export-bar .result-count { font-size: 0.85rem; color: #64748b; font-weight: 600; }
    .export-btns { display: flex; gap: 10px; }
    .btn-export {
        border-radius: 8px; padding: 8px 18px; font-size: 0.82rem;
        font-weight: 600; cursor: pointer; display: inline-flex;
        align-items: center; gap: 6px; text-decoration: none;
        transition: opacity .2s; border: none;
    }
    .btn-export:hover { opacity: .85; }
    .btn-xlsx { background: #16a34a; color: #fff; }
    .btn-csv  { background: #2563eb; color: #fff; }

    /* ── Table ───────────────────────────────────────── */
    .table-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.04); position: relative; }
    .table-card table { margin: 0; }
    .table-card thead th { background: #f8fafc; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; padding: 14px 16px; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .table-card tbody td { font-size: 0.82rem; color: #334155; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .table-card tbody tr:last-child td { border-bottom: none; }
    .table-card tbody tr:hover td { background: #f8fafc; }

    /* table loading overlay */
    .tbl-loading { position: absolute; inset: 0; background: rgba(255,255,255,.75); display: flex; align-items: center; justify-content: center; border-radius: 14px; z-index: 10; opacity: 0; pointer-events: none; transition: opacity .2s; }
    .tbl-loading.show { opacity: 1; pointer-events: all; }
    .spin { width: 36px; height: 36px; border: 3px solid #e2e8f0; border-top-color: #4f46e5; border-radius: 50%; animation: spin .7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* badges */
    .badge-type { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
    .badge-pharmacy { background: #ede9fe; color: #6d28d9; }
    .badge-ip       { background: #dbeafe; color: #1d4ed8; }
    .badge-op       { background: #dcfce7; color: #15803d; }
    .badge-other    { background: #f1f5f9; color: #475569; }
    .badge-pay { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
    .badge-cash { background: #dcfce7; color: #15803d; }
    .badge-upi  { background: #fef3c7; color: #b45309; }
    .badge-neft { background: #dbeafe; color: #1d4ed8; }
    .badge-card { background: #fce7f3; color: #9d174d; }
    .amt-col { font-weight: 700; color: #1e293b; }

    /* ── Custom Pagination ───────────────────────────── */
    .pg-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid #f1f5f9; flex-wrap: wrap; gap: 10px; }
    .pg-info { font-size: 0.8rem; color: #64748b; }
    .pg-btns { display: flex; gap: 4px; flex-wrap: wrap; }
    .pg-btn {
        min-width: 36px; height: 36px; border-radius: 8px; border: 1px solid #e2e8f0;
        background: #fff; color: #475569; font-size: 0.8rem; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
        transition: all .15s; padding: 0 10px;
    }
    .pg-btn:hover { border-color: #4f46e5; color: #4f46e5; background: #ede9fe; }
    .pg-btn.active { background: #4f46e5; color: #fff; border-color: #4f46e5; }
    .pg-btn.disabled { opacity: .38; cursor: not-allowed; pointer-events: none; }
    .pg-dots { padding: 0 4px; color: #94a3b8; font-size: 0.8rem; line-height: 36px; }

    /* ── Empty state ─────────────────────────────────── */
    .empty-state { text-align: center; padding: 48px 24px; color: #94a3b8; }
    .empty-state i { font-size: 2rem; display: block; margin-bottom: 8px; }
</style>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    <div class="stats-wrapper">

      {{-- ── Page Header ─────────────────────────────────────────── --}}
      <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
          <div class="page-title">Billing Statistics</div>
          <div class="page-subtitle">Revenue overview from all locations</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button class="stats-toggle-btn active" id="statsToggleBtn">
            <i class="bi bi-grid-1x2-fill"></i> Stats
          </button>
          <button class="filter-toggle-btn" id="filterToggleBtn">
            <i class="bi bi-funnel-fill"></i> Filters
            <span id="filterBadge" style="display:none;background:rgba(255,255,255,.3);border-radius:10px;padding:1px 7px;font-size:.68rem;"></span>
          </button>
        </div>
      </div>

      {{-- ── Filter Panel ─────────────────────────────────────────── --}}
      <div class="filter-panel" id="filterPanel">
        <div class="filter-title"><i class="bi bi-sliders"></i> Filter Options</div>
        <div class="row g-3">

          {{-- Date From --}}
          <div class="col-md-2 col-sm-6">
            <label class="form-label">Date From</label>
            <div class="fp-wrap">
              <input type="text" id="f_date_from" class="form-control" placeholder="dd/mm/yyyy" autocomplete="off" readonly>
              <i class="bi bi-calendar3 fp-icon"></i>
            </div>
          </div>

          {{-- Date To --}}
          <div class="col-md-2 col-sm-6">
            <label class="form-label">Date To</label>
            <div class="fp-wrap">
              <input type="text" id="f_date_to" class="form-control" placeholder="dd/mm/yyyy" autocomplete="off" readonly>
              <i class="bi bi-calendar3 fp-icon"></i>
            </div>
          </div>

          {{-- Zone multi-select (from tblzones master table) --}}
          <div class="col-md-2 col-sm-6">
            <label class="form-label">Zone</label>
            <div class="ms-wrap" id="ms-zone-wrap">
              <div class="ms-trigger" id="ms-zone-trigger">
                <span class="ms-trigger-text ph" id="ms-zone-label">All Zones</span>
                <span class="ms-count" id="ms-zone-count" style="display:none;"></span>
                <i class="bi bi-chevron-down ms-trigger-arrow"></i>
              </div>
              <div class="ms-dropdown" id="ms-zone-dropdown">
                <div class="ms-search-row">
                  <input type="text" class="ms-search" id="ms-zone-search" placeholder="Search zone…">
                </div>
                <div class="ms-action-row">
                  <span class="ms-act" data-ms="zone" data-action="all">Select All</span>
                  <span class="ms-act clr" data-ms="zone" data-action="clear">Clear</span>
                </div>
                <div class="ms-list" id="ms-zone-list">
                  @foreach($zones as $z)
                    <div class="ms-opt" data-ms="zone" data-value="{{ $z->id }}" data-label="{{ $z->name }}">
                      <span class="ms-cb"><i class="bi bi-check"></i></span>{{ $z->name }}
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          {{-- Branch multi-select (from tbl_locations, cascades by zone) --}}
          <div class="col-md-2 col-sm-6">
            <label class="form-label">Branch</label>
            <div class="ms-wrap" id="ms-branch-wrap">
              <div class="ms-trigger" id="ms-branch-trigger">
                <span class="ms-trigger-text ph" id="ms-branch-label">All Branches</span>
                <span class="ms-count" id="ms-branch-count" style="display:none;"></span>
                <i class="bi bi-chevron-down ms-trigger-arrow"></i>
              </div>
              <div class="ms-dropdown" id="ms-branch-dropdown">
                <div class="ms-search-row">
                  <input type="text" class="ms-search" id="ms-branch-search" placeholder="Search branch…">
                </div>
                <div class="ms-action-row">
                  <span class="ms-act" data-ms="branch" data-action="all">Select All</span>
                  <span class="ms-act clr" data-ms="branch" data-action="clear">Clear</span>
                </div>
                <div class="ms-list" id="ms-branch-list">
                  @foreach($branches as $br)
                    <div class="ms-opt" data-ms="branch" data-value="{{ $br->id }}" data-label="{{ $br->name }}" data-zone="{{ $br->zone_id }}">
                      <span class="ms-cb"><i class="bi bi-check"></i></span>{{ $br->name }}
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          {{-- Type multi-select --}}
          <div class="col-md-2 col-sm-6">
            <label class="form-label">Type</label>
            <div class="ms-wrap" id="ms-type-wrap">
              <div class="ms-trigger" id="ms-type-trigger">
                <span class="ms-trigger-text ph" id="ms-type-label">All Types</span>
                <span class="ms-count" id="ms-type-count" style="display:none;"></span>
                <i class="bi bi-chevron-down ms-trigger-arrow"></i>
              </div>
              <div class="ms-dropdown" id="ms-type-dropdown">
                <div class="ms-search-row">
                  <input type="text" class="ms-search" id="ms-type-search" placeholder="Search type…">
                </div>
                <div class="ms-action-row">
                  <span class="ms-act" data-ms="type" data-action="all">Select All</span>
                  <span class="ms-act clr" data-ms="type" data-action="clear">Clear</span>
                </div>
                <div class="ms-list" id="ms-type-list">
                  @foreach($types as $t)
                    <div class="ms-opt" data-ms="type" data-value="{{ $t }}" data-label="{{ $t }}">
                      <span class="ms-cb"><i class="bi bi-check"></i></span>{{ $t }}
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          {{-- Payment Type multi-select --}}
          <div class="col-md-2 col-sm-6">
            <label class="form-label">Payment Type</label>
            <div class="ms-wrap" id="ms-pay-wrap">
              <div class="ms-trigger" id="ms-pay-trigger">
                <span class="ms-trigger-text ph" id="ms-pay-label">All Payments</span>
                <span class="ms-count" id="ms-pay-count" style="display:none;"></span>
                <i class="bi bi-chevron-down ms-trigger-arrow"></i>
              </div>
              <div class="ms-dropdown" id="ms-pay-dropdown">
                <div class="ms-search-row">
                  <input type="text" class="ms-search" id="ms-pay-search" placeholder="Search payment…">
                </div>
                <div class="ms-action-row">
                  <span class="ms-act" data-ms="pay" data-action="all">Select All</span>
                  <span class="ms-act clr" data-ms="pay" data-action="clear">Clear</span>
                </div>
                <div class="ms-list" id="ms-pay-list">
                  @foreach($paymentTypes as $pt)
                    <div class="ms-opt" data-ms="pay" data-value="{{ $pt }}" data-label="{{ $pt }}">
                      <span class="ms-cb"><i class="bi bi-check"></i></span>{{ $pt }}
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          {{-- Search --}}
          <div class="col-md-3 col-sm-6">
            <label class="form-label">Search</label>
            <input type="text" id="f_search" class="form-control" placeholder="Patient / Bill No / Mobile">
          </div>

        </div>
        <div class="filter-btn-row mt-3">
          <button class="btn-apply" id="applyBtn"><i class="bi bi-search"></i> Apply Filters</button>
          <button class="btn-reset" id="resetBtn">Reset</button>
        </div>
      </div>

      {{-- ── Active Filter Chips ──────────────────────────────────── --}}
      <div class="active-filters" id="activeChips"></div>

      {{-- ── Stats Section ────────────────────────────────────────── --}}
      <div class="stats-section" id="statsSection">

        {{-- Stat Cards --}}
        <div class="stat-cards-grid">
          <div class="stat-card card-total">
            <div class="card-icon"><i class="bi bi-currency-rupee"></i></div>
            <div class="card-label">Total Revenue</div>
            <div class="card-value" id="v-total">₹0.00</div>
            <div class="card-sub"  id="s-total">0 transactions</div>
            <div class="card-bar"></div>
          </div>
          <div class="stat-card card-cash">
            <div class="card-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="card-label">Cash</div>
            <div class="card-value" id="v-cash">₹0.00</div>
            <div class="card-sub"  id="s-cash">0 transactions</div>
            <div class="card-bar"></div>
          </div>
          <div class="stat-card card-upi">
            <div class="card-icon"><i class="bi bi-phone"></i></div>
            <div class="card-label">UPI</div>
            <div class="card-value" id="v-upi">₹0.00</div>
            <div class="card-sub"  id="s-upi">0 transactions</div>
            <div class="card-bar"></div>
          </div>
          <div class="stat-card card-neft">
            <div class="card-icon"><i class="bi bi-bank"></i></div>
            <div class="card-label">NEFT / Bank</div>
            <div class="card-value" id="v-neft">₹0.00</div>
            <div class="card-sub"  id="s-neft">0 transactions</div>
            <div class="card-bar"></div>
          </div>
          <div class="stat-card card-card">
            <div class="card-icon"><i class="bi bi-credit-card-2-front"></i></div>
            <div class="card-label">Card</div>
            <div class="card-value" id="v-card">₹0.00</div>
            <div class="card-sub"  id="s-card">0 transactions</div>
            <div class="card-bar"></div>
          </div>
          <div class="stat-card card-other">
            <div class="card-icon"><i class="bi bi-three-dots"></i></div>
            <div class="card-label">Other</div>
            <div class="card-value" id="v-other">₹0.00</div>
            <div class="card-sub"  id="s-other">0 transactions</div>
            <div class="card-bar"></div>
          </div>
        </div>

        {{-- Type + Location Breakdown --}}
        <div class="row mb-3">
          <div class="col-lg-6">
            <div class="type-breakdown">
              <h6><i class="bi bi-pie-chart-fill me-2" style="color:#7c3aed;"></i>Revenue by Type</h6>
              <div id="typeBreakdown">
                <div class="empty-state" style="padding:20px 0;"><i class="bi bi-hourglass-split"></i><p class="mb-0 small">Loading…</p></div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="loc-breakdown">
              <h6><i class="bi bi-geo-alt-fill me-2" style="color:#db2777;"></i>Revenue by Location</h6>
              <div id="locBreakdown">
                <div class="empty-state" style="padding:20px 0;"><i class="bi bi-hourglass-split"></i><p class="mb-0 small">Loading…</p></div>
              </div>
            </div>
          </div>
        </div>

      </div>{{-- /stats-section --}}

      {{-- ── Export Bar ───────────────────────────────────────────── --}}
      <div class="export-bar">
        <div class="result-count" id="resultCount">Loading…</div>
        <div class="export-btns">
          <a href="#" id="exportXlsx" class="btn-export btn-xlsx">
            <i class="bi bi-file-earmark-excel-fill"></i> Export XLSX
          </a>
          <a href="#" id="exportCsv" class="btn-export btn-csv">
            <i class="bi bi-filetype-csv"></i> Export CSV
          </a>
        </div>
      </div>

      {{-- ── Data Table ───────────────────────────────────────────── --}}
      <div class="table-card">
        <div class="tbl-loading" id="tblLoading"><div class="spin"></div></div>
        <div style="overflow-x:auto;">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th><th>Bill No</th><th>Bill Date</th><th>Patient</th>
                <th>Mobile</th><th>Location</th><th>Type</th><th>Payment</th>
                <th>Amount</th><th>Consultant</th><th>OP No</th><th>Grand Total</th><th>Tax</th>
              </tr>
            </thead>
            <tbody id="tblBody">
              <tr><td colspan="13">
                <div class="empty-state"><i class="bi bi-hourglass-split"></i><p class="mb-0">Loading data…</p></div>
              </td></tr>
            </tbody>
          </table>
        </div>
        <div id="pgWrap"></div>
      </div>

    </div>{{-- /stats-wrapper --}}
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════════════════ --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(function () {

    toastr.options = { closeButton:true, progressBar:true, positionClass:'toast-top-right', timeOut:3000 };

    /* ── STATE ─────────────────────────────────────────────────── */
    var S = {
        date_from:    '',
        date_to:      '',
        zone_ids:     [],   // IDs from tblzones
        branch_ids:   [],   // IDs from tbl_locations
        type_vals:    [],   // multi-select types
        payment_vals: [],   // multi-select payment types
        search:       '',
        page:         1
    };
    var filterOpen   = false;
    var statsVisible = true;
    var searchTimer  = null;

    var BASE_URL   = '{{ route("superadmin.billingstats") }}';
    var EXPORT_URL = '{{ route("superadmin.billingexport") }}';

    /* ── ALL BRANCHES (for zone→branch cascade) ─────────────────── */
    var allBranches = @json($branches->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'zone_id' => $b->zone_id]));

    /* ── FLATPICKR ─────────────────────────────────────────────── */
    var fpFrom = flatpickr('#f_date_from', {
        dateFormat: 'd/m/Y', allowInput: false,
        onChange: function(sel){ S.date_from = sel.length ? toISO(sel[0]) : ''; }
    });
    var fpTo = flatpickr('#f_date_to', {
        dateFormat: 'd/m/Y', allowInput: false,
        onChange: function(sel){ S.date_to = sel.length ? toISO(sel[0]) : ''; }
    });
    function toISO(d){
        return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate());
    }
    function pad(n){ return String(n).padStart(2,'0'); }

    /* ── MULTI-SELECT WIDGET ───────────────────────────────────── */
    var msCfg = {
        zone:   { key:'zone_ids',     ph:'All Zones',     label:'zone'    },
        branch: { key:'branch_ids',   ph:'All Branches',  label:'branch'  },
        type:   { key:'type_vals',    ph:'All Types',     label:'type'    },
        pay:    { key:'payment_vals', ph:'All Payments',  label:'payment' }
    };

    /* Rebuild branch list based on selected zones (cascade) */
    function refreshBranchList() {
        var selectedZones = S.zone_ids.map(String);
        var filtered = selectedZones.length === 0
            ? allBranches
            : allBranches.filter(function(b){ return selectedZones.indexOf(String(b.zone_id)) > -1; });

        var $list = $('#ms-branch-list');
        $list.empty();
        filtered.forEach(function(b){
            $list.append(
                '<div class="ms-opt" data-ms="branch" data-value="'+b.id+'" data-label="'+b.name+'" data-zone="'+b.zone_id+'">' +
                '<span class="ms-cb"><i class="bi bi-check"></i></span>'+b.name+'</div>'
            );
        });
        // Drop branch selections that are no longer in the filtered list
        var validIds = filtered.map(function(b){ return String(b.id); });
        S.branch_ids = S.branch_ids.filter(function(id){ return validIds.indexOf(String(id)) > -1; });
        msSync('branch');
    }

    function msSync(name){
        var vals = S[msCfg[name].key].map(String);
        $('#ms-'+name+'-list .ms-opt').each(function(){
            $(this).toggleClass('sel', vals.indexOf($(this).data('value')+'') > -1);
        });
        var $lbl = $('#ms-'+name+'-label');
        var $cnt = $('#ms-'+name+'-count');
        if (!vals.length){
            $lbl.text(msCfg[name].ph).addClass('ph');
            $cnt.hide();
        } else if (vals.length === 1){
            var lbl = $('#ms-'+name+'-list .ms-opt[data-value="'+vals[0]+'"]').data('label') || vals[0];
            $lbl.text(lbl).removeClass('ph');
            $cnt.hide();
        } else {
            $lbl.text(vals.length+' selected').removeClass('ph');
            $cnt.text(vals.length).show();
        }
    }

    /* open / close */
    $(document).on('click', '.ms-trigger', function(e){
        e.stopPropagation();
        var name = this.id.replace('ms-','').replace('-trigger','');
        var $d = $('#ms-'+name+'-dropdown'), was = $d.hasClass('open');
        $('.ms-dropdown.open').removeClass('open');
        $('.ms-trigger.open').removeClass('open');
        if (!was){ $d.addClass('open'); $(this).addClass('open'); $('#ms-'+name+'-search').val('').trigger('input').focus(); }
    });
    $(document).on('click', function(e){
        if (!$(e.target).closest('.ms-wrap').length){
            $('.ms-dropdown.open').removeClass('open');
            $('.ms-trigger.open').removeClass('open');
        }
    });
    $(document).on('click', '.ms-dropdown', function(e){ e.stopPropagation(); });

    /* option toggle */
    $(document).on('click', '.ms-opt', function(e){
        e.stopPropagation();
        var name = $(this).data('ms');
        var val  = $(this).data('value')+'';
        var arr  = S[msCfg[name].key].map(String);
        var idx  = arr.indexOf(val);
        if (idx > -1) arr.splice(idx,1); else arr.push(val);
        S[msCfg[name].key] = arr;
        msSync(name);
        if (name === 'zone') refreshBranchList();
    });

    /* select all / clear */
    $(document).on('click', '.ms-act', function(e){
        e.stopPropagation();
        var name = $(this).data('ms'), action = $(this).data('action');
        if (action === 'all'){
            var all = [];
            $('#ms-'+name+'-list .ms-opt:visible').each(function(){ all.push($(this).data('value')+''); });
            S[msCfg[name].key] = all;
        } else {
            S[msCfg[name].key] = [];
        }
        msSync(name);
        if (name === 'zone') refreshBranchList();
    });

    /* inline search */
    $(document).on('input', '.ms-search', function(){
        var name = this.id.replace('ms-','').replace('-search','');
        var q = $(this).val().toLowerCase();
        $('#ms-'+name+'-list .ms-opt').each(function(){
            $(this).toggle(($(this).data('label')||$(this).text()).toLowerCase().indexOf(q) > -1);
        });
    });

    /* ── TOGGLE FILTER PANEL ───────────────────────────────────── */
    $('#filterToggleBtn').on('click', function(){
        filterOpen = !filterOpen;
        var $p = $('#filterPanel');
        if (filterOpen){ $p.addClass('open'); }
        else { $p.removeClass('open'); $('.ms-dropdown.open').removeClass('open'); $('.ms-trigger.open').removeClass('open'); }
    });

    /* ── TOGGLE STATS ──────────────────────────────────────────── */
    $('#statsToggleBtn').on('click', function(){
        statsVisible = !statsVisible;
        if (statsVisible){
            $('#statsSection').removeClass('hidden');
            $(this).addClass('active').html('<i class="bi bi-grid-1x2-fill"></i> Stats');
        } else {
            $('#statsSection').addClass('hidden');
            $(this).removeClass('active').html('<i class="bi bi-grid-1x2"></i> Stats');
        }
    });

    /* ── APPLY FILTERS ─────────────────────────────────────────── */
    $('#applyBtn').on('click', function(){
        S.search = $('#f_search').val().trim();
        S.page   = 1;
        fetchAll();
        filterOpen = false;
        $('#filterPanel').removeClass('open');
        $('.ms-dropdown.open').removeClass('open');
        $('.ms-trigger.open').removeClass('open');
    });

    /* ── RESET ─────────────────────────────────────────────────── */
    $('#resetBtn').on('click', function(){
        S = { date_from:'', date_to:'', zone_ids:[], branch_ids:[], type_vals:[], payment_vals:[], search:'', page:1 };
        $('#f_search').val('');
        fpFrom.clear(); fpTo.clear();
        msSync('zone'); msSync('type'); msSync('pay');
        refreshBranchList();
        fetchAll();
    });

    /* ── LIVE SEARCH ───────────────────────────────────────────── */
    $('#f_search').on('input', function(){
        clearTimeout(searchTimer);
        var v = $(this).val().trim();
        searchTimer = setTimeout(function(){ S.search = v; S.page = 1; fetchAll(); }, 420);
    });

    /* ── CHIP REMOVE ───────────────────────────────────────────── */
    $(document).on('click', '.remove', function(){
        var p = $(this).data('param');
        S.page = 1;
        if      (p === 'zone_ids')     { S.zone_ids = []; msSync('zone'); refreshBranchList(); }
        else if (p === 'branch_ids')   { S.branch_ids = []; msSync('branch'); }
        else if (p === 'type_vals')    { S.type_vals = []; msSync('type'); }
        else if (p === 'payment_vals') { S.payment_vals = []; msSync('pay'); }
        else if (p === 'date_from')    { S.date_from = ''; fpFrom.clear(); }
        else if (p === 'date_to')      { S.date_to   = ''; fpTo.clear(); }
        else if (p === 'search')       { S.search    = ''; $('#f_search').val(''); }
        fetchAll();
    });

    $(document).on('click', '#clearAllChip', function(){
        S = { date_from:'', date_to:'', zone_ids:[], branch_ids:[], type_vals:[], payment_vals:[], search:'', page:1 };
        $('#f_search').val('');
        fpFrom.clear(); fpTo.clear();
        msSync('zone'); msSync('type'); msSync('pay');
        refreshBranchList();
        fetchAll();
    });

    /* ── EXPORT ────────────────────────────────────────────────── */
    function exportUrl(fmt){ var p = params(); p.format = fmt; return EXPORT_URL+'?'+$.param(p); }
    $('#exportXlsx').on('click', function(e){ e.preventDefault(); window.location.href = exportUrl('xlsx'); });
    $('#exportCsv').on('click',  function(e){ e.preventDefault(); window.location.href = exportUrl('csv');  });

    /* ── PARAMS ────────────────────────────────────────────────── */
    function params(){
        var p = {};
        if (S.date_from)           p.date_from    = S.date_from;
        if (S.date_to)             p.date_to      = S.date_to;
        if (S.zone_ids.length)     p.zone_ids     = S.zone_ids;
        if (S.branch_ids.length)   p.branch_ids   = S.branch_ids;
        if (S.type_vals.length)    p.type_vals    = S.type_vals;
        if (S.payment_vals.length) p.payment_vals = S.payment_vals;
        if (S.search)              p.search       = S.search;
        if (S.page > 1)            p.page         = S.page;
        return p;
    }

    /* ── FETCH ALL ─────────────────────────────────────────────── */
    function fetchAll(){ renderChips(); updateBadge(); fetchStats(); fetchTable(); }

    /* ── FETCH STATS ───────────────────────────────────────────── */
    function fetchStats(){
        var p = params(); p.ajax = 'stats';
        $.ajax({ url:BASE_URL, method:'GET', data:p,
            success: function(r){ renderStats(r); },
            error:   function(){ toastr.error('Failed to load stats'); }
        });
    }

    /* ── FETCH TABLE ───────────────────────────────────────────── */
    function fetchTable(){
        overlay(true);
        var p = params(); p.ajax = 'table';
        $.ajax({ url:BASE_URL, method:'GET', data:p,
            success: function(res){
                overlay(false);
                renderRows(res);
                renderPagination(res.pagination);
                $('#resultCount').html(
                    'Showing <strong>'+(res.from||0)+'</strong> – <strong>'+(res.to||0)+
                    '</strong> of <strong>'+(res.total||0)+'</strong> records'
                );
            },
            error: function(){ overlay(false); toastr.error('Failed to load data'); }
        });
    }

    /* ── RENDER STATS ──────────────────────────────────────────── */
    var COLORS = ['#7c3aed','#2563eb','#16a34a','#d97706','#db2777','#475569','#0891b2','#9333ea'];
    var KNOWN  = ['cash','upi','neft','bank transfer','rtgs','card','debit card','credit card'];

    function findPay(pay, keys){
        for (var i=0;i<keys.length;i++){
            if (pay[keys[i]]) return pay[keys[i]];
            var f = Object.keys(pay).find(function(k){ return k.toLowerCase()===keys[i].toLowerCase(); });
            if (f) return pay[f];
        }
        return null;
    }

    function rupee(v){ return '₹'+parseFloat(v||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    function num(v)  { return parseInt(v||0).toLocaleString('en-IN'); }

    function renderStats(res){
        $('#v-total').text(rupee(res.total_amount));
        $('#s-total').text(num(res.total_records)+' transactions');

        var pay = res.payment_stats||{};
        var cash=findPay(pay,['Cash']), upi=findPay(pay,['UPI','Upi']),
            neft=findPay(pay,['NEFT','Neft','Bank Transfer','RTGS']),
            card=findPay(pay,['Card','Debit Card','Credit Card']);

        $('#v-cash').text(rupee(cash?cash.total:0)); $('#s-cash').text(num(cash?cash.count:0)+' transactions');
        $('#v-upi').text(rupee(upi?upi.total:0));    $('#s-upi').text(num(upi?upi.count:0)+' transactions');
        $('#v-neft').text(rupee(neft?neft.total:0)); $('#s-neft').text(num(neft?neft.count:0)+' transactions');
        $('#v-card').text(rupee(card?card.total:0)); $('#s-card').text(num(card?card.count:0)+' transactions');

        var ot=0,oc=0;
        $.each(pay,function(k,v){ if(KNOWN.indexOf(k.toLowerCase())===-1){ot+=parseFloat(v.total||0);oc+=parseInt(v.count||0);} });
        $('#v-other').text(rupee(ot)); $('#s-other').text(num(oc)+' transactions');

        /* type breakdown */
        var ts=res.type_stats||[], tmax=Math.max.apply(null,ts.map(function(t){return parseFloat(t.total||0);}).concat([1]));
        var th='';
        ts.forEach(function(t,i){
            var pct=((parseFloat(t.total||0)/tmax)*100).toFixed(1);
            th+='<div class="type-item">'
               +'<div class="type-item-header">'
               +'<span class="type-name">'+(t.type||'Unknown')+'</span>'
               +'<div><span class="type-value">'+rupee(t.total)+'</span>'
               +'<span class="ms-2 loc-count">('+num(t.count)+')</span></div></div>'
               +'<div class="progress"><div class="progress-bar" style="width:'+pct+'%;background:'+COLORS[i%COLORS.length]+';"></div></div>'
               +'</div>';
        });
        $('#typeBreakdown').html(th||'<div class="text-muted small">No data</div>');

        /* location breakdown */
        var ls=res.location_stats||[], lh='';
        ls.forEach(function(l){
            lh+='<div class="loc-item">'
               +'<span class="loc-name">'+l.location_name+'</span>'
               +'<div class="text-end"><div class="loc-value">'+rupee(l.total)+'</div>'
               +'<div class="loc-count">'+num(l.count)+' records</div></div></div>';
        });
        $('#locBreakdown').html(lh||'<div class="text-muted small">No data</div>');
    }

    /* ── RENDER ROWS ───────────────────────────────────────────── */
    function renderRows(res){
        var rows=res.records||[], from=res.from||0;
        if (!rows.length){
            $('#tblBody').html('<tr><td colspan="13"><div class="empty-state"><i class="bi bi-inbox"></i><p class="mb-0">No records found. Try adjusting your filters.</p></div></td></tr>');
            return;
        }
        var h='';
        rows.forEach(function(r,i){
            var tl=(r.type||'').toLowerCase();
            var tb=tl.indexOf('pharmacy')>-1?'badge-pharmacy':tl.indexOf('ip')>-1?'badge-ip':tl.indexOf('op')>-1?'badge-op':'badge-other';
            var pl=(r.paymenttype||'').toLowerCase();
            var pb=pl.indexOf('cash')>-1?'badge-cash':pl.indexOf('upi')>-1?'badge-upi'
                  :(pl.indexOf('neft')>-1||pl.indexOf('bank')>-1||pl.indexOf('rtgs')>-1)?'badge-neft'
                  :pl.indexOf('card')>-1?'badge-card':'';
            h+='<tr>'
              +'<td>'+(from+i)+'</td>'
              +'<td><strong>'+(r.billno||'—')+'</strong></td>'
              +'<td style="white-space:nowrap;">'+(r.billdate||'—')+'</td>'
              +'<td><div style="font-weight:600;">'+(r.patientname||'—')+'</div>'
              +'<div style="font-size:.75rem;color:#94a3b8;">'+(r.gender||'')+' '+(r.age?'| '+r.age:'')+'</div></td>'
              +'<td>'+(r.mobile||'—')+'</td>'
              +'<td style="white-space:nowrap;font-size:.78rem;">'+(r.location_name||'—')+'</td>'
              +'<td><span class="badge-type '+tb+'">'+(r.type||'—')+'</span></td>'
              +'<td><span class="badge-pay '+pb+'">'+(r.paymenttype||'—')+'</span></td>'
              +'<td class="amt-col">'+rupee(r.amt)+'</td>'
              +'<td style="font-size:.78rem;">'+(r.consultant||'—')+'</td>'
              +'<td>'+(r.opno||'—')+'</td>'
              +'<td>'+rupee(r.grandtotal)+'</td>'
              +'<td>'+rupee(r.tax)+'</td>'
              +'</tr>';
        });
        $('#tblBody').html(h);
    }

    /* ── RENDER PAGINATION ─────────────────────────────────────── */
    function renderPagination(pg){
        if (!pg||pg.last_page<=1){ $('#pgWrap').html(''); return; }
        var cur=pg.current_page, last=pg.last_page;
        var h='<div class="pg-row"><div class="pg-info">Page '+cur+' of '+last+'</div><div class="pg-btns">';

        h+='<button class="pg-btn'+(cur===1?' disabled':'')+'" data-page="'+(cur-1)+'"><i class="bi bi-chevron-left"></i></button>';

        pageRange(cur,last).forEach(function(p){
            if (p==='...') h+='<span class="pg-dots">…</span>';
            else h+='<button class="pg-btn'+(p===cur?' active':'')+'" data-page="'+p+'">'+p+'</button>';
        });

        h+='<button class="pg-btn'+(cur===last?' disabled':'')+'" data-page="'+(cur+1)+'"><i class="bi bi-chevron-right"></i></button>';
        h+='</div></div>';
        $('#pgWrap').html(h);
    }

    function pageRange(cur,last){
        if (last<=7){ var r=[]; for(var i=1;i<=last;i++) r.push(i); return r; }
        if (cur<=4)       return [1,2,3,4,5,'...',last];
        if (cur>=last-3)  return [1,'...',last-4,last-3,last-2,last-1,last];
        return [1,'...',cur-1,cur,cur+1,'...',last];
    }

    $(document).on('click', '.pg-btn:not(.disabled):not(.active)', function(){
        var pg=parseInt($(this).data('page'));
        if (!isNaN(pg)&&pg>=1){ S.page=pg; fetchTable(); $('html,body').animate({scrollTop:$('.table-card').offset().top-80},180); }
    });

    /* ── CHIPS ─────────────────────────────────────────────────── */
    function renderChips(){
        var $c=$('#activeChips').empty(), n=0;

        function addChip(param, label){
            n++;
            $c.append('<span class="filter-chip">'+label+' <span class="remove" data-param="'+param+'">×</span></span>');
        }

        if (S.date_from) addChip('date_from','From: '+S.date_from);
        if (S.date_to)   addChip('date_to',  'To: '+S.date_to);
        if (S.zone_ids.length){
            var zl = S.zone_ids.length===1
                ? ($('#ms-zone-list .ms-opt[data-value="'+S.zone_ids[0]+'"]').data('label')||S.zone_ids[0])
                : S.zone_ids.length+' zones';
            addChip('zone_ids','Zone: '+zl);
        }
        if (S.branch_ids.length){
            var bl = S.branch_ids.length===1
                ? ($('#ms-branch-list .ms-opt[data-value="'+S.branch_ids[0]+'"]').data('label')||S.branch_ids[0])
                : S.branch_ids.length+' branches';
            addChip('branch_ids','Branch: '+bl);
        }
        if (S.type_vals.length){
            var tl = S.type_vals.length===1 ? S.type_vals[0] : S.type_vals.length+' types';
            addChip('type_vals','Type: '+tl);
        }
        if (S.payment_vals.length){
            var pl = S.payment_vals.length===1 ? S.payment_vals[0] : S.payment_vals.length+' payments';
            addChip('payment_vals','Payment: '+pl);
        }
        if (S.search)      addChip('search','Search: '+S.search);

        if (n>0) $c.append('<span class="filter-chip clear-chip" id="clearAllChip">Clear All ×</span>');
    }

    function updateBadge(){
        var n=0;
        if(S.date_from)n++; if(S.date_to)n++; if(S.zone_ids.length)n++;
        if(S.branch_ids.length)n++; if(S.type_vals.length)n++; if(S.payment_vals.length)n++; if(S.search)n++;
        n>0 ? $('#filterBadge').text(n).show() : $('#filterBadge').hide();
    }

    /* ── OVERLAY ───────────────────────────────────────────────── */
    function overlay(on){ $('#tblLoading').toggleClass('show', on); }

    /* ── INIT ──────────────────────────────────────────────────── */
    fetchAll();

});
</script>

@include('superadmin.superadminfooter')
</body>
</html>