<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="{{ asset('assets/css/pharmacy_audit.css') }}">
<style>
  body.phau-page .pc-container .pc-content {
    max-width: 100%; width: 100%; box-sizing: border-box;
    padding-left: 14px; padding-right: 18px;
  }
  .grn-shell { width: 100%; max-width: 100%; margin: 0; }

  /* ── GRN-style hero ── */
  .grn-hero {
    position: relative; border-radius: 22px; padding: 24px 28px; margin-bottom: 18px;
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 55%, #4338ca 100%);
    color: #e2e8f0; box-shadow: 0 14px 40px -12px rgba(30, 27, 75, 0.45); overflow: hidden;
  }
  .grn-hero::before {
    content: ''; position: absolute; top: -55%; right: -8%;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(99,102,241,0.30) 0%, transparent 70%);
    border-radius: 50%; pointer-events: none;
  }
  .grn-hero-row {
    position: relative; z-index: 1;
    display: flex; flex-wrap: wrap; gap: 16px; align-items: center; justify-content: space-between;
  }
  .grn-hero h1 {
    margin: 0 0 6px; color: #f8fafc;
    font-size: 1.45rem; font-weight: 800; letter-spacing: -0.01em;
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
  }
  .grn-hero h1 i { color: #a5b4fc; }
  .grn-hero-badge {
    display: inline-block; margin-top: 8px;
    padding: 5px 12px; border-radius: 999px;
    font-size: 0.7rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase;
    background: rgba(99,102,241,0.20); color: #c7d2fe; border: 1px solid rgba(165,180,252,0.25);
  }
  .grn-btn-ghost {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.5rem 0.95rem; border-radius: 999px;
    font-size: 0.85rem; font-weight: 600; line-height: 1.2;
    color: #f8fafc !important; text-decoration: none !important; white-space: nowrap;
    border: 1px solid rgba(248,250,252,0.5);
    background: rgba(255,255,255,0.12);
    transition: all 0.2s ease;
  }
  .grn-btn-ghost:hover { color: #1e1b4b !important; background: #f8fafc; border-color: transparent; }

  .grn-footer {
    position: sticky; bottom: 0;
    background: rgba(255,255,255,0.95); backdrop-filter: blur(8px);
    border: 1px solid rgba(226,232,240,0.9); border-radius: 16px;
    padding: 12px 16px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    box-shadow: 0 -8px 25px rgba(15,23,42,0.05); margin-top: 1rem;
  }
  .grn-footer .grn-meta {
    margin-right: auto; display: flex; align-items: center; gap: 8px;
    font-size: 0.78rem; color: #64748b;
  }
  .grn-footer .btn {
    border-radius: 10px; font-weight: 600; font-size: 0.85rem; padding: 0.55rem 1.1rem;
  }
  .grn-footer .btn-primary {
    background: linear-gradient(120deg, #4f46e5, #7c3aed);
    border: none; color: #fff;
    box-shadow: 0 8px 22px rgba(99,102,241,0.32);
  }
  .grn-footer .btn-primary:hover { filter: brightness(1.06); transform: translateY(-1px); }
  .grn-footer .btn-light { background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; }

  .grn-info-banner {
    border-radius: 12px; padding: 10px 14px;
    background: #fffbeb; border: 1px solid #fde68a; color: #78350f;
    font-size: 0.8rem; display: flex; align-items: flex-start; gap: 8px;
    margin-bottom: 1rem;
  }

  /* ── Match GRN create: section + dropdowns (.grn-shell scoped) ── */
  .grn-shell .grn-sec {
    background: #fff;
    border: 1px solid rgba(226,232,240,0.85);
    border-radius: 18px;
    padding: 1.25rem 1.4rem 1.4rem;
    margin-bottom: 1.1rem;
    box-shadow: 0 4px 18px rgba(15,23,42,0.03);
  }
  .grn-shell .grn-sec--location { background: linear-gradient(145deg, #ffffff, #f8fafc); }
  .grn-shell .grn-sec-title {
    font-weight: 800; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em;
    color: #334155;
    display: flex; align-items: center; gap: 0.55rem;
    margin: 0 0 1rem; padding-bottom: 0.55rem;
    border-bottom: 1px dashed #e2e8f0;
  }
  .grn-shell .grn-sec-title i { color: #4f46e5; font-size: 1rem; background: #eef2ff; padding: 5px; border-radius: 7px; }
  .grn-shell .grn-sec-title small {
    margin-left: auto; text-transform: none; font-weight: 500; font-size: 0.7rem; color: #94a3b8; letter-spacing: 0;
  }
  .grn-shell .form-label {
    display: block; font-size: 0.74rem; font-weight: 700;
    color: #334155; margin-bottom: 5px; letter-spacing: 0.01em;
  }
  .grn-shell .form-control,
  .grn-shell .form-select {
    font-size: 0.875rem; padding: 0.55rem 0.75rem;
    border: 1px solid #e2e8f0; border-radius: 10px;
    background: #fff; transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .grn-shell .form-control:focus,
  .grn-shell .form-select:focus {
    border-color: #818cf8;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
  }
  .grn-shell .tax-dropdown-wrapper { width: 100%; position: relative; }
  .grn-shell .tax-dropdown-wrapper .dropdown-search-input {
    background: #fff !important; cursor: pointer;
    padding: 0.55rem 2.1rem 0.55rem 0.75rem;
    height: calc(1.5em + 1.1rem + 2px);
    font-size: 0.875rem;
    border: 1px solid #e2e8f0; border-radius: 10px;
  }
  .grn-shell .tax-dropdown-wrapper::after {
    content: '\F282';
    font-family: 'bootstrap-icons';
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    font-size: 0.85rem; color: #94a3b8; pointer-events: none;
  }
  .grn-shell .tax-dropdown-wrapper .dropdown-search-input:focus {
    border-color: #818cf8;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
  }
  .grn-shell .dropdown-list div,
  .grn-floating .dropdown-list div {
    padding: 10px 14px; cursor: pointer; font-size: 0.875rem; color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
    box-sizing: border-box;
    overflow: hidden;
    transition: background 0.12s ease;
  }
  .grn-shell .dropdown-list div:last-child,
  .grn-floating .dropdown-list div:last-child { border-bottom: 0; }
  .grn-shell .dropdown-list div:hover,
  .grn-floating .dropdown-list div:hover { background: #f1f5f9; }
  .grn-shell .dropdown-list div.selected,
  .grn-floating .dropdown-list div.selected { background: #eef2ff; color: #3730a3; font-weight: 600; }
  .grn-shell .dropdown-list div.empty,
  .grn-floating .dropdown-list div.empty {
    color: #94a3b8; cursor: default; background: #fff !important; font-style: italic;
  }
  .grn-floating {
    box-sizing: border-box !important;
    min-width: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    border-radius: 12px !important;
    border: 1px solid #e2e8f0 !important;
    background: #fff !important;
    box-shadow: 0 18px 40px rgba(15,23,42,0.12) !important;
    overflow: hidden !important;
    z-index: 10080 !important;
  }
  .grn-floating .inner-search-container {
    padding: 8px 10px; background: #f8fafc; border-bottom: 1px solid #eef2f7;
  }
  .grn-floating .inner-search {
    width: 100%; box-sizing: border-box;
    border-radius: 8px; border: 1px solid #e2e8f0;
    padding: 6px 10px; font-size: 0.85rem;
    background: #fff;
  }
  .grn-floating .inner-search:focus {
    outline: 0;
    border-color: #818cf8;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
  }
  .grn-floating .dropdown-list {
    max-height: 280px; overflow-y: auto; overscroll-behavior: contain;
  }

  /* ── Inline validation (GRN parity) ── */
  .grn-shell .form-control.is-invalid,
  .grn-shell .tax-dropdown-wrapper.is-invalid .dropdown-search-input {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
  }
  .grn-shell .grn-field-error {
    display: none;
    margin-top: 4px; font-size: 0.74rem; color: #b91c1c; font-weight: 600;
  }
  .grn-shell .has-error .grn-field-error,
  .grn-shell .grn-field-error.is-active { display: block; }
  .grn-shell .tax-dropdown-wrapper.is-invalid .dropdown-search-input,
  .grn-shell .form-control.is-invalid {
    border-color: #ef4444 !important;
    background: #fff5f5 !important;
  }
  .grn-shell .tax-dropdown-wrapper.is-invalid::after { color: #ef4444; }
  .phau-items-table input.form-control.is-invalid {
    border-color: #ef4444 !important;
    background: #fff5f5 !important;
  }
  .phau-line-field-error {
    display: none;
    margin-top: 2px; font-size: 0.68rem; color: #b91c1c; font-weight: 600; line-height: 1.25;
  }
  .phau-line-field-error.is-active { display: block; }
  .phau-items-table input[type="month"].form-control-sm {
    min-width: 10rem;
    max-width: 100%;
  }
  .phau-notes-below-table {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px dashed #e2e8f0;
  }
</style>

<body class="phau-page grn-page" style="overflow-x: hidden;">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

@php
  $r = $record ?? null;
  $isEdit = $isEdit ?? false;
  $o = fn (string $k, $def = '') => old($k, $r ? data_get($r, $k) : $def);
  $auditDate = old('audit_date', $r && $r->audit_date ? $r->audit_date->format('Y-m-d') : '');

  /** Normalize stored / legacy expiry values for HTML month input (value must be YYYY-MM). */
  $phauExpiryForMonthInput = static function (?string $exp): string {
      $exp = trim((string) $exp);
      if ($exp === '') {
          return '';
      }
      if (preg_match('/^\d{4}-\d{2}$/', $exp)) {
          return $exp;
      }
      if (preg_match('/^(\d{1,2})[\/\-](\d{2})$/', $exp, $m)) {
          $mm = str_pad((string) (int) $m[1], 2, '0', STR_PAD_LEFT);
          $yy = (int) $m[2];
          $year = $yy <= 30 ? 2000 + $yy : 1900 + $yy;

          return sprintf('%04d-%s', $year, $mm);
      }
      if (preg_match('/^(\d{4})[\/\-](\d{1,2})$/', $exp, $m)) {
          return sprintf('%04d-%s', (int) $m[1], str_pad((string) (int) $m[2], 2, '0', STR_PAD_LEFT));
      }
      try {
          return \Carbon\Carbon::parse($exp)->format('Y-m');
      } catch (\Throwable $e) {
          return '';
      }
  };

  $itemRows = old('items');
  if (! is_array($itemRows)) {
    if ($r) {
      $r->loadMissing('items');
      $itemRows = $r->items->isNotEmpty()
        ? $r->items->map(fn ($i) => [
            'item_name' => $i->item_name,
            'batch_no' => $i->batch_no,
            'expiry' => $phauExpiryForMonthInput($i->expiry),
            'mrp' => $i->mrp,
            'system_qty' => $i->system_qty,
            'manual_qty' => $i->manual_qty,
            'diff_qty' => $i->diff_qty,
            'val' => $i->val,
          ])->values()->all()
        : [];
    } else {
      $itemRows = [];
    }
  } else {
      foreach ($itemRows as $k => $row) {
          if (! is_array($row)) {
              continue;
          }
          $itemRows[$k]['expiry'] = $phauExpiryForMonthInput($row['expiry'] ?? '');
      }
  }
  if ($itemRows === []) {
    $itemRows = [[
      'item_name' => '',
      'batch_no' => '',
      'expiry' => '',
      'mrp' => '',
      'system_qty' => '',
      'manual_qty' => '',
      'diff_qty' => '',
      'val' => '',
    ]];
  }
@endphp

<div class="pc-container">
  <div class="pc-content">
    <div class="grn-shell">
      <header class="grn-hero">
        <div class="grn-hero-row">
          <div>
            <h1><i class="bi bi-capsule-pill"></i> {{ $isEdit ? 'Edit pharmacy audit' : 'New pharmacy audit' }}</h1>
            <span class="grn-hero-badge">
              <i class="bi bi-shield-check me-1"></i>
              {{ $isEdit && $r ? $r->audit_number : 'Reference assigned on save' }}
            </span>
            <p class="grn-hero-sub">{{ $isEdit ? 'Update stock variance lines and keep the audit record consistent across location and date.' : 'Capture branch-wise stock variance with a cleaner audit form and auto-calculated quantity differences.' }}</p>
          </div>
          <a href="{{ route('pharmacy-audits.index') }}" class="grn-btn-ghost">
            <i class="bi bi-arrow-left"></i> Back to list
          </a>
        </div>
      </header>

      @if ($errors->any())
        <div class="grn-info-banner" style="background:#fef2f2;border-color:#fecaca;color:#991b1b;">
          <i class="bi bi-exclamation-triangle-fill mt-1"></i>
          <div>
            <strong>Please fix the following:</strong>
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        </div>
      @endif

      <form method="post" action="{{ $isEdit ? route('pharmacy-audits.update', $r) : route('pharmacy-audits.store') }}" id="phauAuditForm">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <section class="grn-sec grn-sec--location">
          <div class="grn-sec-title">
            <i class="bi bi-buildings"></i> Company &amp; location
          </div>
          <div class="row g-3">
            <div class="col-xl-3 col-md-6">
              <label class="form-label">Company <span class="text-danger">*</span></label>
              <div class="tax-dropdown-wrapper company-section">
                <input type="text" class="form-control company-search-input dropdown-search-input"
                       placeholder="Select company" readonly value="{{ $o('company_name') }}">
                <input type="hidden" name="company_id" class="company_id" value="{{ $o('company_id') }}">
                <input type="hidden" name="company_name" class="company_name" value="{{ $o('company_name') }}">
                <div class="dropdown-menu tax-dropdown">
                  <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search company…"></div>
                  <div class="dropdown-list company-list"></div>
                </div>
              </div>
              <div class="grn-field-error" data-for="company_id"></div>
            </div>
            <div class="col-xl-3 col-md-6">
              <label class="form-label">Zone <span class="text-danger">*</span></label>
              <div class="tax-dropdown-wrapper zone-section">
                <input type="text" class="form-control zone-search-input dropdown-search-input"
                       placeholder="Select zone" readonly value="{{ $o('zone_name') }}">
                <input type="hidden" name="zone_id" class="zone_id" value="{{ $o('zone_id') }}">
                <input type="hidden" name="zone_name" class="zone_name" value="{{ $o('zone_name') }}">
                <div class="dropdown-menu tax-dropdown">
                  <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search zone…"></div>
                  <div class="dropdown-list zone-list"></div>
                </div>
              </div>
              <div class="grn-field-error" data-for="zone_id"></div>
            </div>
            <div class="col-xl-3 col-md-6">
              <label class="form-label">Branch <span class="text-danger">*</span></label>
              <div class="tax-dropdown-wrapper branch-section">
                <input type="text" class="form-control branch-search-input dropdown-search-input"
                       placeholder="Select branch" readonly value="{{ $o('branch_name') }}">
                <input type="hidden" name="branch_id" class="branch_id" value="{{ $o('branch_id') }}">
                <input type="hidden" name="branch_name" class="branch_name" value="{{ $o('branch_name') }}">
                <div class="dropdown-menu tax-dropdown">
                  <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search branch…"></div>
                  <div class="dropdown-list branch-list"></div>
                </div>
              </div>
              <div class="grn-field-error" data-for="branch_id"></div>
            </div>
            <div class="col-xl-3 col-md-6">
              <label class="form-label">Audit date <span class="text-danger">*</span></label>
              <input type="date" name="audit_date" class="form-control" value="{{ $auditDate }}">
              <div class="grn-field-error" data-for="audit_date"></div>
            </div>
          </div>
        </section>

        <section class="grn-sec">
          <div class="grn-sec-title">
            <i class="bi bi-table"></i> Line items
          </div>
          <div class="grn-field-error" data-for="items"></div>
          <div class="phau-items-table-wrap">
            <table class="phau-items-table" id="phauItemsTable">
              <thead>
                <tr>
                  <th class="phau-th-nowrap" style="width:36px;">#</th>
                  <th class="phau-th-nowrap">Name <span class="text-danger">*</span></th>
                  <th class="phau-th-nowrap">Batch</th>
                  <th class="phau-th-nowrap">Expiry <span class="text-muted fw-normal" style="font-size:0.65rem;">(Mo/Yr)</span></th>
                  <th class="text-end phau-th-nowrap">MRP</th>
                  <th class="text-end phau-th-qty">System Quantity<span class="phau-th-hint">Stock on system</span></th>
                  <th class="text-end phau-th-qty">Manual Quantity<span class="phau-th-hint">Physical count</span></th>
                  <th class="text-end phau-th-nowrap">Diff</th>
                  <th class="text-end phau-th-nowrap">Val</th>
                  <th class="phau-th-nowrap phau-th-action" style="width:92px;">Action</th>
                </tr>
              </thead>
              <tbody id="phauItemsBody">
                @foreach ($itemRows as $idx => $row)
                  <tr class="phau-item-row">
                    <td class="text-muted small phau-line-no">{{ $idx + 1 }}</td>
                    <td>
                      <input type="text" name="items[{{ $idx }}][item_name]" class="form-control form-control-sm phau-line-inp" value="{{ $row['item_name'] ?? '' }}">
                      <div class="phau-line-field-error" data-line-field="item_name"></div>
                    </td>
                    <td>
                      <input type="text" name="items[{{ $idx }}][batch_no]" class="form-control form-control-sm phau-line-inp" value="{{ $row['batch_no'] ?? '' }}">
                      <div class="phau-line-field-error" data-line-field="batch_no"></div>
                    </td>
                    <td>
                      <input type="month" name="items[{{ $idx }}][expiry]" class="form-control form-control-sm phau-line-inp phau-in-expiry" value="{{ $row['expiry'] ?? '' }}" title="Month and year only">
                      <div class="phau-line-field-error" data-line-field="expiry"></div>
                    </td>
                    <td>
                      <input type="number" step="0.01" name="items[{{ $idx }}][mrp]" class="form-control form-control-sm text-end phau-in-mrp phau-line-inp" value="{{ $row['mrp'] ?? '' }}">
                      <div class="phau-line-field-error" data-line-field="mrp"></div>
                    </td>
                    <td>
                      <input type="number" step="1" name="items[{{ $idx }}][system_qty]" class="form-control form-control-sm text-end phau-in-sys phau-line-inp" value="{{ $row['system_qty'] ?? '' }}">
                      <div class="phau-line-field-error" data-line-field="system_qty"></div>
                    </td>
                    <td>
                      <input type="number" step="1" name="items[{{ $idx }}][manual_qty]" class="form-control form-control-sm text-end phau-in-man phau-line-inp" value="{{ $row['manual_qty'] ?? '' }}">
                      <div class="phau-line-field-error" data-line-field="manual_qty"></div>
                    </td>
                    <td>
                      <input type="number" step="1" name="items[{{ $idx }}][diff_qty]" class="form-control form-control-sm text-end phau-in-diff phau-line-inp" value="{{ $row['diff_qty'] ?? '' }}">
                      <div class="phau-line-field-error" data-line-field="diff_qty"></div>
                    </td>
                    <td>
                      <input type="number" step="0.01" name="items[{{ $idx }}][val]" class="form-control form-control-sm text-end phau-in-val phau-line-inp" value="{{ $row['val'] ?? '' }}">
                      <div class="phau-line-field-error" data-line-field="val"></div>
                    </td>
                    <td class="text-center phau-action-cell">
                      <button type="button" class="phau-btn-addline-icon phau-add-row" title="Add line after this row" aria-label="Add line after this row"><i class="bi bi-plus-lg" aria-hidden="true"></i></button>
                      <button type="button" class="phau-btn-remove phau-remove-row" title="Remove line" aria-label="Remove line" @if (count($itemRows) < 2) disabled @endif><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="phau-notes-below-table">
            <label class="form-label" for="phau_notes_field">Notes</label>
            <textarea id="phau_notes_field" name="notes" class="form-control" rows="2" maxlength="5000" placeholder="Optional context for this audit">{{ $o('notes') }}</textarea>
            <div class="grn-field-error" data-for="notes"></div>
          </div>
        </section>

        <div class="grn-footer">
          <div class="grn-meta">
            <i class="bi bi-info-circle-fill"></i>
            <span>Difference and value fields are recalculated while you type.</span>
          </div>
          <a href="{{ $isEdit ? route('pharmacy-audits.show', $r) : route('pharmacy-audits.index') }}" class="btn btn-light border">Cancel</a>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2-circle me-1"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(function () {
  if (window.toastr) {
    toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 4500 };
  }
  @if (session('success'))
    if (window.toastr) toastr.success(@json(session('success')));
  @endif
  @if (session('error'))
    if (window.toastr) toastr.error(@json(session('error')));
  @endif

  const ZONES = @json($zones->map(fn ($z) => ['id' => $z->id, 'name' => $z->name])->values());
  const BRANCHES = @json($branches->map(fn ($b) => ['id' => $b->id, 'name' => $b->name, 'zone_id' => $b->zone_id])->values());
  const COMPANIES = @json($companies->map(fn ($c) => ['id' => $c->id, 'name' => $c->company_name])->values());

  function setFieldError(name, msg) {
    const $err = $('#phauAuditForm .grn-field-error[data-for="' + name + '"]');
    if (!$err.length) return;
    const $col = $err.parent();
    const $drops = $col.find('.tax-dropdown-wrapper');
    const $inps = $col.find('input[name="' + name + '"], textarea[name="' + name + '"], select[name="' + name + '"]');
    if (msg) {
      $err.text(msg).addClass('is-active');
      $drops.addClass('is-invalid');
      $inps.addClass('is-invalid');
    } else {
      $err.text('').removeClass('is-active');
      $drops.removeClass('is-invalid');
      $inps.removeClass('is-invalid');
    }
  }

  function focusForField(name) {
    const $err = $('#phauAuditForm .grn-field-error[data-for="' + name + '"]');
    if (!$err.length) return null;
    const $col = $err.parent();
    const $dd = $col.find('.dropdown-search-input').first();
    if ($dd.length) return $dd;
    const $inp = $col.find('[name="' + name + '"]').first();
    if ($inp.length) return $inp;
    return $err;
  }

  function phauSetLineError(rowIdx, field, msg) {
    const $inp = $('#phauAuditForm [name="items[' + rowIdx + '][' + field + ']"]');
    if (!$inp.length) return;
    const $err = $inp.closest('td').find('.phau-line-field-error[data-line-field="' + field + '"]');
    if (msg) {
      $inp.addClass('is-invalid');
      $err.text(msg).addClass('is-active');
    } else {
      $inp.removeClass('is-invalid');
      $err.text('').removeClass('is-active');
    }
  }

  function clearAllLineErrors() {
    $('#phauItemsBody .phau-line-inp').removeClass('is-invalid');
    $('#phauItemsBody .phau-line-field-error').text('').removeClass('is-active');
  }

  function validateBeforeSubmit() {
    let firstTop = null;
    let firstLine = null;

    clearAllLineErrors();

    function requireVal(name, message) {
      const $inp = $('#phauAuditForm [name="' + name + '"]');
      const val = ($inp.val() || '').toString().trim();
      if (!val) {
        setFieldError(name, message);
        if (!firstTop) firstTop = name;
      } else {
        setFieldError(name, '');
      }
    }

    requireVal('company_id', 'Company field is required.');
    requireVal('zone_id', 'Zone field is required.');
    requireVal('branch_id', 'Branch field is required.');
    requireVal('audit_date', 'Audit date field is required.');

    const rows = document.querySelectorAll('#phauItemsBody .phau-item-row');
    if (!rows.length) {
      setFieldError('items', 'Add at least one line item.');
      if (!firstTop) firstTop = 'items';
    } else {
      setFieldError('items', '');
    }

    rows.forEach(function (tr, i) {
      const itemNameEl = tr.querySelector('[name="items[' + i + '][item_name]"]');
      const nm = itemNameEl ? (itemNameEl.value || '').toString().trim() : '';
      if (!nm) {
        phauSetLineError(i, 'item_name', 'Each line must have an item name.');
        if (!firstLine) firstLine = { row: i, field: 'item_name' };
      } else {
        phauSetLineError(i, 'item_name', '');
      }

      function reqMrp() {
        const el = tr.querySelector('[name="items[' + i + '][mrp]"]');
        const raw = el ? (el.value || '').toString().trim() : '';
        if (raw === '') {
          phauSetLineError(i, 'mrp', 'MRP is required on each line.');
          if (!firstLine) firstLine = { row: i, field: 'mrp' };
          return;
        }
        const n = parseFloat(raw.replace(',', '.'));
        if (!Number.isFinite(n) || n < 0) {
          phauSetLineError(i, 'mrp', 'MRP must be a valid number.');
          if (!firstLine) firstLine = { row: i, field: 'mrp' };
          return;
        }
        phauSetLineError(i, 'mrp', '');
      }

      function reqInt(field, emptyMsg, badMsg) {
        const el = tr.querySelector('[name="items[' + i + '][' + field + ']"]');
        const raw = el ? (el.value || '').toString().trim() : '';
        if (raw === '') {
          phauSetLineError(i, field, emptyMsg);
          if (!firstLine) firstLine = { row: i, field: field };
          return;
        }
        if (!/^-?\d+$/.test(raw)) {
          phauSetLineError(i, field, badMsg);
          if (!firstLine) firstLine = { row: i, field: field };
          return;
        }
        phauSetLineError(i, field, '');
      }

      function reqValField() {
        const el = tr.querySelector('[name="items[' + i + '][val]"]');
        const raw = el ? (el.value || '').toString().trim() : '';
        if (raw === '') {
          phauSetLineError(i, 'val', 'Value is required on each line.');
          if (!firstLine) firstLine = { row: i, field: 'val' };
          return;
        }
        const n = parseFloat(raw.replace(',', '.'));
        if (!Number.isFinite(n)) {
          phauSetLineError(i, 'val', 'Value must be a valid number.');
          if (!firstLine) firstLine = { row: i, field: 'val' };
          return;
        }
        phauSetLineError(i, 'val', '');
      }

      reqMrp();
      reqInt('system_qty', 'System quantity is required on each line.', 'System quantity must be a whole number.');
      reqInt('manual_qty', 'Manual quantity is required on each line.', 'Manual quantity must be a whole number.');
      reqInt('diff_qty', 'Difference quantity is required on each line.', 'Difference quantity must be a whole number.');
      reqValField();
    });

    return { firstTop: firstTop, firstLine: firstLine };
  }

  function escapeHtml(s) {
    return $('<div>').text(s == null ? '' : s).html();
  }

  function paint($list, items, selectedId, opts) {
    opts = opts || {};
    $list.empty();
    if (!items.length) {
      $list.append('<div class="empty">' + escapeHtml(opts.emptyText || 'No options') + '</div>');
      return;
    }
    items.forEach(function (it) {
      const sel = (selectedId && String(selectedId) === String(it.id)) ? 'selected' : '';
      const searchText = [it.name].filter(Boolean).join(' ');
      $list.append(
        '<div class="' + sel + '" data-id="' + escapeHtml(it.id) + '" data-text="' + escapeHtml(it.name) + '" data-search="' + escapeHtml(searchText) + '">' +
        escapeHtml(it.name) +
        '</div>'
      );
    });
  }

  paint($('.company-list'), COMPANIES, $('.company_id').val());
  paint($('.zone-list'), ZONES, $('.zone_id').val());

  const initialZone = $('.zone_id').val();
  const initialBranches = initialZone ? BRANCHES.filter(function (b) { return String(b.zone_id) === String(initialZone); }) : [];
  paint($('.branch-list'), initialBranches, $('.branch_id').val(), { emptyText: 'Select zone first' });

  function phauPositionFloating($input, $dropdown) {
    const rect = $input[0].getBoundingClientRect();
    $dropdown.css({
      position: 'absolute',
      top: Math.round(rect.bottom + window.scrollY + 4) + 'px',
      left: Math.round(rect.left + window.scrollX) + 'px',
      width: Math.round(rect.width) + 'px',
      zIndex: 10080
    });
  }

  let $phauOpenInput = null;

  $(document).on('click', '.grn-shell .dropdown-search-input', function (e) {
    e.stopPropagation();
    $('.dropdown-menu.tax-dropdown.grn-floating').hide();

    const $input = $(this);
    let $dropdown = $input.data('dropdown');
    if (!$dropdown) {
      $dropdown = $input.siblings('.dropdown-menu').clone(true);
      $dropdown.addClass('grn-floating');
      $('body').append($dropdown);
      $input.data('dropdown', $dropdown);
    }
    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));

    const $sourceList = $input.siblings('.dropdown-menu').find('.dropdown-list');
    $dropdown.find('.dropdown-list').replaceWith($sourceList.clone());

    phauPositionFloating($input, $dropdown);
    $dropdown.show();
    $dropdown.find('.inner-search').val('').focus();
    $dropdown.find('.dropdown-list div').show();
    $dropdown.find('.dropdown-list').scrollTop(0);
    $phauOpenInput = $input;
  });

  $(window).on('scroll resize', function () {
    if (!$phauOpenInput || !$phauOpenInput.length) return;
    const $dropdown = $phauOpenInput.data('dropdown');
    if ($dropdown && $dropdown.is(':visible')) {
      phauPositionFloating($phauOpenInput, $dropdown);
    }
  });

  $(document).on('keyup', '.grn-floating .inner-search', function () {
    const v = $(this).val().toLowerCase();
    $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function () {
      const $row = $(this);
      if ($row.hasClass('empty')) return;
      const haystack = ($row.attr('data-search') || $row.text() || '').toLowerCase();
      $row.toggle(haystack.indexOf(v) > -1);
    });
  });

  $(document).on('click', '.grn-floating .dropdown-list div', function (e) {
    e.stopPropagation();
    if ($(this).hasClass('empty')) return;

    const $row = $(this);
    const $dropdown = $row.closest('.dropdown-menu');
    const $wrapper = $dropdown.data('wrapper');
    if (!$wrapper) return;

    const text = ($row.attr('data-text') || $row.text() || '').trim();
    const id = $row.attr('data-id');

    $wrapper.find('.dropdown-search-input').val(text);
    $wrapper.find('input[type="hidden"][name$="_id"]').val(id);
    $wrapper.find('input[type="hidden"][name$="_name"]').val(text);

    $wrapper.find('.dropdown-list div').each(function () {
      $(this).toggleClass('selected', $(this).attr('data-id') === id);
    });

    $wrapper.removeClass('is-invalid');
    const $fe = $wrapper.parent().find('.grn-field-error');
    if ($fe.length) { $fe.text('').removeClass('is-active'); }
    if ($wrapper.hasClass('company-section')) setFieldError('company_id', '');
    if ($wrapper.hasClass('zone-section')) setFieldError('zone_id', '');
    if ($wrapper.hasClass('branch-section')) setFieldError('branch_id', '');

    $dropdown.hide();
    $phauOpenInput = null;
    $wrapper.find('.dropdown-search-input').trigger('phau:change', [{ id: id, name: text }]);
  });

  $(document).on('click', function (e) {
    if (!$(e.target).closest('.tax-dropdown-wrapper').length && !$(e.target).closest('.grn-floating').length) {
      $('.grn-floating').hide();
      $phauOpenInput = null;
    }
  });
  $(document).on('click', '.grn-floating', function (e) { e.stopPropagation(); });

  function refreshBranches(items, zoneId) {
    paint($('.branch-list'), items, null, { emptyText: zoneId ? 'No branches in this zone' : 'Select zone first' });
    const $branchInput = $('.branch-search-input');
    const $cached = $branchInput.data('dropdown');
    if ($cached) {
      $cached.remove();
      $branchInput.removeData('dropdown');
    }
  }

  $(document).on('phau:change', '.zone-search-input', function (_e, payload) {
    setFieldError('branch_id', '');
    $('.branch-search-input').val('');
    $('.branch_id').val('');
    $('.branch_name').val('');

    const zoneId = payload && payload.id;
    if (!zoneId) {
      refreshBranches([], null);
      return;
    }
    $.ajax({
      url: @json(route('superadmin.getbranchfetch')),
      method: 'POST',
      data: { id: zoneId, _token: @json(csrf_token()) },
      success: function (res) {
        const items = (res && res.branch ? res.branch : []).map(function (b) { return { id: b.id, name: b.name, zone_id: zoneId }; });
        refreshBranches(items, zoneId);
      },
      error: function () {
        if (window.toastr) toastr.warning('Could not fetch branches — showing cached list.');
        const items = BRANCHES.filter(function (b) { return String(b.zone_id) === String(zoneId); });
        refreshBranches(items, zoneId);
      }
    });
  });

  // Line items table
  const tbody = document.getElementById('phauItemsBody');

  function phauNewRowHtml(i) {
    return (
      '<td class="text-muted small phau-line-no"></td>' +
      '<td><input type="text" name="items[' + i + '][item_name]" class="form-control form-control-sm phau-line-inp"><div class="phau-line-field-error" data-line-field="item_name"></div></td>' +
      '<td><input type="text" name="items[' + i + '][batch_no]" class="form-control form-control-sm phau-line-inp"><div class="phau-line-field-error" data-line-field="batch_no"></div></td>' +
      '<td><input type="month" name="items[' + i + '][expiry]" class="form-control form-control-sm phau-line-inp phau-in-expiry" title="Month and year only"><div class="phau-line-field-error" data-line-field="expiry"></div></td>' +
      '<td><input type="number" step="0.01" name="items[' + i + '][mrp]" class="form-control form-control-sm text-end phau-in-mrp phau-line-inp" value="0"><div class="phau-line-field-error" data-line-field="mrp"></div></td>' +
      '<td><input type="number" step="1" name="items[' + i + '][system_qty]" class="form-control form-control-sm text-end phau-in-sys phau-line-inp" value="0"><div class="phau-line-field-error" data-line-field="system_qty"></div></td>' +
      '<td><input type="number" step="1" name="items[' + i + '][manual_qty]" class="form-control form-control-sm text-end phau-in-man phau-line-inp" value="0"><div class="phau-line-field-error" data-line-field="manual_qty"></div></td>' +
      '<td><input type="number" step="1" name="items[' + i + '][diff_qty]" class="form-control form-control-sm text-end phau-in-diff phau-line-inp" value="0"><div class="phau-line-field-error" data-line-field="diff_qty"></div></td>' +
      '<td><input type="number" step="0.01" name="items[' + i + '][val]" class="form-control form-control-sm text-end phau-in-val phau-line-inp" value="0"><div class="phau-line-field-error" data-line-field="val"></div></td>' +
      '<td class="text-center phau-action-cell">' +
      '<button type="button" class="phau-btn-addline-icon phau-add-row" title="Add line after this row" aria-label="Add line after this row"><i class="bi bi-plus-lg" aria-hidden="true"></i></button>' +
      '<button type="button" class="phau-btn-remove phau-remove-row" title="Remove line" aria-label="Remove line"><i class="bi bi-x-lg" aria-hidden="true"></i></button>' +
      '</td>'
    );
  }

  function parseNum(el, dec) {
    const v = parseFloat(String(el.value || '0').replace(',', '.'));
    if (Number.isNaN(v)) return 0;
    return dec ? Math.round(v * 100) / 100 : Math.trunc(v);
  }

  function recalcRow(tr) {
    const mrpEl = tr.querySelector('.phau-in-mrp');
    const sysEl = tr.querySelector('.phau-in-sys');
    const manEl = tr.querySelector('.phau-in-man');
    const diffEl = tr.querySelector('.phau-in-diff');
    const valEl = tr.querySelector('.phau-in-val');
    if (!mrpEl || !sysEl || !manEl || !diffEl || !valEl) return;
    const sys = parseNum(sysEl, false);
    const man = parseNum(manEl, false);
    const mrp = parseNum(mrpEl, true);
    const diff = man - sys;
    const val = Math.round(diff * mrp * 100) / 100;
    diffEl.value = String(diff);
    valEl.value = String(val);
  }

  function wireRow(tr) {
    tr.querySelectorAll('.phau-in-mrp, .phau-in-sys, .phau-in-man').forEach(function (inp) {
      inp.addEventListener('input', function () { recalcRow(tr); });
    });
  }

  function renumber() {
    const rows = tbody.querySelectorAll('.phau-item-row');
    rows.forEach(function (tr, i) {
      const no = tr.querySelector('.phau-line-no');
      if (no) no.textContent = String(i + 1);
      tr.querySelectorAll('input[name^="items["]').forEach(function (inp) {
        const m = inp.name.match(/^items\[(\d+)\]/);
        if (!m) return;
        inp.name = inp.name.replace(/items\[\d+\]/, 'items[' + i + ']');
      });
      const rm = tr.querySelector('.phau-remove-row');
      if (rm) {
        rm.disabled = rows.length < 2;
      }
    });
    clearAllLineErrors();
  }

  if (tbody) {
    tbody.querySelectorAll('.phau-item-row').forEach(wireRow);
  }

  if (tbody) {
    tbody.addEventListener('click', function (e) {
      const addB = e.target.closest('.phau-add-row');
      if (addB) {
        e.preventDefault();
        const trAfter = addB.closest('.phau-item-row');
        const i = tbody.querySelectorAll('.phau-item-row').length;
        const tr = document.createElement('tr');
        tr.className = 'phau-item-row';
        tr.innerHTML = phauNewRowHtml(i);
        if (trAfter) {
          trAfter.insertAdjacentElement('afterend', tr);
        } else {
          tbody.appendChild(tr);
        }
        wireRow(tr);
        renumber();
        return;
      }
      const btn = e.target.closest('.phau-remove-row');
      if (!btn || btn.disabled) return;
      const tr = btn.closest('.phau-item-row');
      if (!tr || tbody.querySelectorAll('.phau-item-row').length < 2) return;
      tr.remove();
      renumber();
    });
  }

  $('#phauAuditForm').on('input change', 'input[name="audit_date"], textarea[name="notes"]', function () {
    const n = $(this).attr('name');
    if (n) setFieldError(n, '');
  });

  $('#phauAuditForm').on('input change', '#phauItemsBody .phau-line-inp', function () {
    const m = this.name.match(/^items\[(\d+)\]\[(\w+)\]/);
    if (m) phauSetLineError(parseInt(m[1], 10), m[2], '');
    setFieldError('items', '');
  });

  $('#phauAuditForm').on('submit', function (e) {
    const v = validateBeforeSubmit();
    if (v.firstTop || v.firstLine) {
      e.preventDefault();
      if (window.toastr) toastr.error('Please fix the highlighted fields, then submit again.', 'Validation failed');
      if (v.firstTop) {
        const $el = focusForField(v.firstTop);
        if ($el && $el.length) {
          try {
            $el[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            $el.focus();
          } catch (err) {}
        }
      } else if (v.firstLine) {
        const inp = document.querySelector('#phauAuditForm [name="items[' + v.firstLine.row + '][' + v.firstLine.field + ']"]');
        if (inp) {
          try {
            inp.scrollIntoView({ behavior: 'smooth', block: 'center' });
            inp.focus();
          } catch (err2) {}
        }
      }
      return false;
    }
  });

  @if ($errors->any())
    @foreach (['company_id', 'zone_id', 'branch_id', 'audit_date', 'notes', 'items'] as $f)
      @if ($errors->has($f))
        setFieldError(@json($f), @json($errors->first($f)));
      @endif
    @endforeach
    @foreach ($errors->getMessages() as $key => $msgs)
      @if (preg_match('/^items\.(\d+)\.(\w+)$/', $key, $phauErr))
        phauSetLineError({{ (int) $phauErr[1] }}, @json($phauErr[2]), @json($msgs[0] ?? ''));
      @endif
    @endforeach
  @endif
});
</script>
@include('superadmin.superadminfooter')

</body>
</html>
