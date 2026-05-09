<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<style>
  body.grn-page .pc-container .pc-content {
    max-width: 100%;
    width: 100%;
    box-sizing: border-box;
    padding-left: 14px;
    padding-right: 18px;
  }

  .grn-shell { width: 100%; max-width: 100%; margin: 0; }

  /* ── Premium hero ── */
  .grn-hero {
    position: relative;
    border-radius: 22px;
    padding: 24px 28px;
    margin-bottom: 18px;
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 55%, #4338ca 100%);
    color: #e2e8f0;
    box-shadow: 0 14px 40px -12px rgba(30, 27, 75, 0.45);
    overflow: hidden;
  }
  .grn-hero::before {
    content: '';
    position: absolute; top: -55%; right: -8%;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(99,102,241,0.30) 0%, transparent 70%);
    border-radius: 50%; pointer-events: none;
  }
  .grn-hero::after {
    content: '';
    position: absolute; bottom: -70%; left: 5%;
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(56,189,248,0.18) 0%, transparent 70%);
    border-radius: 50%; pointer-events: none;
  }
  .grn-hero-row {
    position: relative; z-index: 1;
    display: flex; flex-wrap: wrap; gap: 16px;
    align-items: center; justify-content: space-between;
  }
  .grn-hero h1 {
    margin: 0 0 6px; color: #f8fafc;
    font-size: 1.45rem; font-weight: 800; letter-spacing: -0.01em;
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
  }
  .grn-hero h1 i { color: #a5b4fc; }
  .grn-hero p { margin: 0; max-width: 60ch; color: rgba(226,232,240,0.85); font-size: 0.9rem; line-height: 1.55; }
  .grn-hero-badge {
    display: inline-block; margin-top: 12px;
    padding: 5px 12px; border-radius: 999px;
    font-size: 0.7rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase;
    background: rgba(99,102,241,0.20); color: #c7d2fe; border: 1px solid rgba(165,180,252,0.25);
    backdrop-filter: blur(4px);
  }
  .grn-btn-ghost {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.5rem 0.95rem; border-radius: 999px;
    font-size: 0.85rem; font-weight: 600; line-height: 1.2;
    color: #f8fafc !important; text-decoration: none !important; white-space: nowrap;
    border: 1px solid rgba(248,250,252,0.5);
    background: rgba(255,255,255,0.12);
    box-shadow: 0 1px 0 rgba(255,255,255,0.12) inset;
    transition: all 0.2s ease;
  }
  .grn-btn-ghost:hover { color: #1e1b4b !important; background: #f8fafc; border-color: transparent; }

  /* ── Step strip ── */
  .grn-step-strip {
    display: flex; flex-wrap: wrap; gap: 8px;
    margin-bottom: 16px;
  }
  .grn-step {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 14px; border-radius: 10px;
    background: #f1f5f9; border: 1px solid #e2e8f0;
    font-size: 0.78rem; color: #475569; font-weight: 600;
  }
  .grn-step b {
    display: inline-flex; align-items: center; justify-content: center;
    width: 1.4rem; height: 1.4rem; border-radius: 50%;
    background: #e0e7ff; color: #4338ca; font-size: 0.7rem;
  }
  .grn-step.is-active {
    background: linear-gradient(120deg, #eef2ff, #f8fafc);
    border-color: #c7d2fe; color: #312e81;
  }
  .grn-step.is-active b { background: #4f46e5; color: #fff; }

  /* ── Section cards ── */
  .grn-sec {
    background: #fff;
    border: 1px solid rgba(226,232,240,0.85);
    border-radius: 18px;
    padding: 1.25rem 1.4rem 1.4rem;
    margin-bottom: 1.1rem;
    box-shadow: 0 4px 18px rgba(15,23,42,0.03);
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
  }
  .grn-sec:hover { border-color: #cbd5e1; box-shadow: 0 8px 28px rgba(15,23,42,0.05); }
  .grn-sec--location { background: linear-gradient(145deg, #ffffff, #f8fafc); }
  .grn-sec-title {
    font-weight: 800; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em;
    color: #334155;
    display: flex; align-items: center; gap: 0.55rem;
    margin: 0 0 1rem; padding-bottom: 0.55rem;
    border-bottom: 1px dashed #e2e8f0;
  }
  .grn-sec-title i {
    color: #4f46e5; font-size: 1rem;
    background: #eef2ff; padding: 5px; border-radius: 7px;
  }
  .grn-sec-title small {
    margin-left: auto; text-transform: none; font-weight: 500; font-size: 0.7rem; color: #94a3b8; letter-spacing: 0;
  }

  /* ── Inputs ── */
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

  /* ── Dropdown look (built on shared .tax-dropdown-wrapper / .dropdown-list) ── */
  .grn-shell .tax-dropdown-wrapper {
    width: 100%; position: relative;
  }
  .grn-shell .tax-dropdown-wrapper .dropdown-search-input {
    background: #fff !important; cursor: pointer;
    padding: 0.55rem 2.1rem 0.55rem 0.75rem;
    height: calc(1.5em + 1.1rem + 2px);
    font-size: 0.875rem;
    border: 1px solid #e2e8f0; border-radius: 10px;
  }
  .grn-shell .tax-dropdown-wrapper::after {
    content: '\F282'; /* bi-chevron-down */
    font-family: 'bootstrap-icons';
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    font-size: 0.85rem; color: #94a3b8; pointer-events: none;
  }
  .grn-shell .tax-dropdown-wrapper .dropdown-search-input:focus {
    border-color: #818cf8;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
  }
  /* Rules apply to BOTH the inline (.grn-shell) and body-cloned (.grn-floating)
     versions of the dropdown so the rows look identical once the menu floats. */
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
  /* Employee rows now use the same single-tone plain typography as vendor
     rows (handled by the shared .dropdown-list styles below) so the
     Received-by dropdown matches the Vendor dropdown UI design. The
     dedicated .grn-opt-line / .grn-opt-line-name / .grn-opt-line-id
     two-tone styles were intentionally removed — see paint() in the script
     block below for the simplified row markup. */

  /* Floating clone (lives at <body> level, so we override Bootstrap's default
     .dropdown-menu min-width / padding that otherwise widen it past the input). */
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
  /* Cap height so very long lists scroll inside the menu instead of
     visually overflowing onto the page. */
  .grn-floating .dropdown-list {
    max-height: 280px; overflow-y: auto; overscroll-behavior: contain;
  }
  .grn-floating .dropdown-list::-webkit-scrollbar { width: 8px; }
  .grn-floating .dropdown-list::-webkit-scrollbar-thumb {
    background: #cbd5e1; border-radius: 999px;
  }
  .grn-floating .dropdown-list::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

  /* ── Drag-and-drop uploads ── */
  .grn-upload {
    position: relative; display: block;
    border: 1.5px dashed #cbd5e1; border-radius: 14px;
    background: linear-gradient(160deg, #ffffff 0%, #f8fafc 100%);
    padding: 16px 16px;
    transition: all 0.2s ease; cursor: pointer; overflow: hidden;
  }
  .grn-upload:hover { border-color: #818cf8; background: linear-gradient(160deg, #eef2ff 0%, #f8fafc 100%); }
  .grn-upload.is-drag { border-color: #4f46e5; background: #eef2ff; }
  .grn-upload .grn-upload-row {
    display: flex; align-items: center; gap: 12px;
  }
  .grn-upload-ico {
    width: 42px; height: 42px; flex: 0 0 42px;
    border-radius: 10px; background: #eef2ff; color: #4f46e5;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.15rem;
  }
  .grn-upload-text { flex: 1; min-width: 0; }
  .grn-upload-text .ttl { font-size: 0.85rem; font-weight: 700; color: #1e293b; }
  .grn-upload-text .sub { font-size: 0.75rem; color: #64748b; }
  .grn-upload-name {
    display: none; margin-top: 8px;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
    padding: 6px 10px; font-size: 0.78rem; color: #334155;
    align-items: center; gap: 8px;
  }
  .grn-upload-name.is-set { display: inline-flex; }
  .grn-upload-name .x {
    margin-left: 6px; cursor: pointer; color: #ef4444; font-weight: 700;
  }
  .grn-upload input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; display: block !important;
  }
  .grn-current-file {
    margin-top: 6px; font-size: 0.75rem; color: #64748b;
  }
  .grn-current-file a { color: #4f46e5; font-weight: 600; }

  /* ── Footer / actions ── */
  .grn-footer {
    position: sticky; bottom: 0;
    background: rgba(255,255,255,0.95); backdrop-filter: blur(8px);
    border: 1px solid rgba(226,232,240,0.9);
    border-radius: 16px;
    padding: 12px 16px;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    box-shadow: 0 -8px 25px rgba(15,23,42,0.05);
    margin-top: 1rem;
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
  .grn-footer .btn-primary:hover {
    filter: brightness(1.06); transform: translateY(-1px);
    box-shadow: 0 10px 26px rgba(99,102,241,0.4);
  }
  .grn-footer .btn-light {
    background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;
  }

  .grn-status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 999px;
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
  }
  .grn-status-pending { background: #fef3c7; color: #92400e; }
  .grn-status-approved { background: #dcfce7; color: #166534; }
  .grn-status-rejected { background: #fee2e2; color: #991b1b; }

  .grn-info-banner {
    border-radius: 12px;
    padding: 10px 14px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #78350f;
    font-size: 0.8rem;
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 1rem;
  }

  /* ── Inline validation ── */
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
  .grn-upload.is-invalid {
    border-color: #ef4444 !important;
    background: #fef2f2 !important;
  }
  .grn-shell .tax-dropdown-wrapper.is-invalid .dropdown-search-input,
  .grn-shell .form-control.is-invalid {
    border-color: #ef4444 !important;
    background: #fff5f5 !important;
  }
  .grn-shell .tax-dropdown-wrapper.is-invalid::after { color: #ef4444; }

  /* ── Preview modal ── */
  #grnPreviewModal { z-index: 1080; }
  #grnPreviewModal .modal-dialog { max-width: min(92vw, 1100px); }
  #grnPreviewModal .modal-content {
    border: none; border-radius: 16px; overflow: hidden;
    box-shadow: 0 30px 80px rgba(15,23,42,0.25);
  }
  #grnPreviewModal .modal-header {
    background: linear-gradient(120deg, #1e1b4b, #4338ca);
    color: #fff; border: 0; padding: 0.85rem 1.1rem;
  }
  #grnPreviewModal .modal-title { font-size: 0.95rem; font-weight: 700; color: #fff; }
  #grnPreviewModal .btn-close { filter: invert(1) brightness(2); opacity: 0.9; }
  #grnPreviewModal .modal-body {
    background: #0b1220; color: #e2e8f0; padding: 0; min-height: 60vh;
    display: block; text-align: center;
  }
  #grnPreviewModal iframe,
  #grnPreviewModal video,
  #grnPreviewModal img {
    display: block;
    width: 100% !important;
    border: 0;
    background: #000;
    margin: 0 auto;
  }
  #grnPreviewModal iframe { height: 78vh !important; }
  #grnPreviewModal video  { height: 78vh !important; max-height: 78vh; object-fit: contain; }
  #grnPreviewModal img    { height: auto; max-height: 78vh; object-fit: contain; }
  .grn-preview-empty {
    color: #94a3b8; font-size: 0.9rem; padding: 2rem; text-align: center;
  }
  .modal-backdrop.show { z-index: 1075; opacity: 0.65; }

  /* ── Preview action buttons inside upload tile ── */
  .grn-upload-actions {
    display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px;
  }
  .grn-upload-actions .btn {
    font-size: 0.72rem; padding: 4px 10px; border-radius: 8px; font-weight: 600;
  }
  .grn-upload-actions .btn-outline-primary {
    color: #4f46e5; border: 1px solid #c7d2fe; background: #fff;
  }
  .grn-upload-actions .btn-outline-primary:hover {
    background: #eef2ff; color: #312e81;
  }

  /* responsive tweaks */
  @media (max-width: 575.98px) {
    .grn-hero { padding: 18px 18px; border-radius: 16px; }
    .grn-hero h1 { font-size: 1.2rem; }
    .grn-sec { padding: 1rem; border-radius: 14px; }
    #grnPreviewModal iframe, #grnPreviewModal video { height: 60vh; }
  }
</style>
<body class="grn-page" style="overflow-x: hidden;">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')
<div class="pc-container">
  <div class="pc-content">
    @php
      $r = $record;
      $o = fn(string $k, $def = '') => old($k, $r ? data_get($r, $k) : $def);
      $statusKey = $r ? $r->audit_approval_status : null;
    @endphp

    <div class="grn-shell">
      {{-- ── Hero ── --}}
      <header class="grn-hero">
        <div class="grn-hero-row">
          <div>
            <h1>
              <i class="bi bi-box-seam"></i>
              {{ $isEdit ? 'Edit GRN' : 'New GRN' }}
              @if($isEdit && $r)
                <span class="grn-status-pill
                  @if($statusKey==='approved') grn-status-approved
                  @elseif($statusKey==='rejected') grn-status-rejected
                  @else grn-status-pending @endif">
                  {{ \App\Models\GrnRecord::statusLabel($statusKey) }}
                </span>
              @endif
            </h1>
            <span class="grn-hero-badge">
              <i class="bi bi-shield-check me-1"></i>
              {{ $isEdit && $r ? $r->grn_number : 'Auto-generated GRN reference' }}
            </span>
          </div>
          <a href="{{ route('grn.index') }}" class="grn-btn-ghost">
            <i class="bi bi-arrow-left"></i> Back to list
          </a>
        </div>
      </header>

      @if ($errors->any())
        <div class="grn-info-banner" style="background:#fef2f2;border-color:#fecaca;color:#991b1b;">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <div>
            <strong>Please fix the following:</strong>
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        </div>
      @endif

      @if($isEdit && $r && !$r->isPending())
        <div class="grn-info-banner">
          <i class="bi bi-info-circle-fill"></i>
          This record is no longer pending. Only users with audit access (limits 1 &amp; 4) may edit it.
        </div>
      @endif

      <form method="post" action="{{ $isEdit ? route('grn.update', $r) : route('grn.store') }}" enctype="multipart/form-data" id="grnForm">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- ── Company & Vendor ── --}}
        <section class="grn-sec grn-sec--location">
          <div class="grn-sec-title">
            <i class="bi bi-buildings"></i> Company &amp; vendor
            <small>Required to identify the receiving location and supplier</small>
          </div>

          <div class="row g-3">
            <div class="col-xl-3 col-md-6">
              <label class="form-label">Company <span class="text-danger">*</span></label>
              <div class="tax-dropdown-wrapper company-section">
                <input type="text" class="form-control company-search-input dropdown-search-input"
                       placeholder="Select Company" readonly value="{{ $o('company_name') }}">
                <input type="hidden" name="company_id" class="company_id" value="{{ $o('company_id') }}">
                <input type="hidden" name="company_name" class="company_name" value="{{ $o('company_name') }}">
                <div class="dropdown-menu tax-dropdown">
                  <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Company..."></div>
                  <div class="dropdown-list company-list"></div>
                </div>
              </div>
              <div class="grn-field-error" data-for="company_id"></div>
            </div>

            <div class="col-xl-3 col-md-6">
              <label class="form-label">Zone <span class="text-danger">*</span></label>
              <div class="tax-dropdown-wrapper zone-section">
                <input type="text" class="form-control zone-search-input dropdown-search-input"
                       placeholder="Select Zone" readonly value="{{ $o('zone_name') }}">
                <input type="hidden" name="zone_id" class="zone_id" value="{{ $o('zone_id') }}">
                <input type="hidden" name="zone_name" class="zone_name" value="{{ $o('zone_name') }}">
                <div class="dropdown-menu tax-dropdown">
                  <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Zone..."></div>
                  <div class="dropdown-list zone-list"></div>
                </div>
              </div>
              <div class="grn-field-error" data-for="zone_id"></div>
            </div>

            <div class="col-xl-3 col-md-6">
              <label class="form-label">Branch <span class="text-danger">*</span></label>
              <div class="tax-dropdown-wrapper branch-section">
                <input type="text" class="form-control branch-search-input dropdown-search-input"
                       placeholder="Select Branch" readonly value="{{ $o('branch_name') }}">
                <input type="hidden" name="branch_id" class="branch_id" value="{{ $o('branch_id') }}">
                <input type="hidden" name="branch_name" class="branch_name" value="{{ $o('branch_name') }}">
                <div class="dropdown-menu tax-dropdown">
                  <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Branch..."></div>
                  <div class="dropdown-list branch-list"></div>
                </div>
              </div>
              <div class="grn-field-error" data-for="branch_id"></div>
            </div>

            <div class="col-xl-3 col-md-6">
              <label class="form-label">Vendor name <span class="text-danger">*</span></label>
              <div class="tax-dropdown-wrapper vendor-section">
                <input type="text" class="form-control vendor-search-input dropdown-search-input"
                       placeholder="Select Vendor" readonly value="{{ $o('vendor_name') }}">
                <input type="hidden" name="vendor_id" class="vendor_id" value="{{ $o('vendor_id') }}">
                <input type="hidden" name="vendor_name" class="vendor_name" value="{{ $o('vendor_name') }}">
                <div class="dropdown-menu tax-dropdown">
                  <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search Vendor..."></div>
                  <div class="dropdown-list vendor-list"></div>
                </div>
              </div>
              <div class="grn-field-error" data-for="vendor_name"></div>
            </div>
          </div>
        </section>

        {{-- ── Invoice details ── --}}
        <section class="grn-sec">
          <div class="grn-sec-title">
            <i class="bi bi-receipt"></i> Invoice details
            <small>Original invoice as issued by the vendor</small>
          </div>

          <div class="row g-3">
            <div class="col-xl-3 col-md-6">
              <label class="form-label">Invoice number <span class="text-danger">*</span></label>
              <input type="text" name="invoice_number" class="form-control" maxlength="120"
                     placeholder="e.g. INV-2026-00012" value="{{ $o('invoice_number') }}">
              <div class="grn-field-error" data-for="invoice_number"></div>
            </div>
            <div class="col-xl-3 col-md-6">
              <label class="form-label">Invoice date <span class="text-danger">*</span></label>
              <input type="date" name="invoice_date" class="form-control"
                     value="{{ $r && $r->invoice_date ? $r->invoice_date->format('Y-m-d') : old('invoice_date') }}">
              <div class="grn-field-error" data-for="invoice_date"></div>
            </div>
            <div class="col-xl-3 col-md-6">
              <label class="form-label">Received date <span class="text-danger">*</span></label>
              <input type="date" name="received_date" class="form-control"
                     value="{{ $r && $r->received_date ? $r->received_date->format('Y-m-d') : old('received_date') }}">
              <div class="grn-field-error" data-for="received_date"></div>
            </div>
            <div class="col-xl-3 col-md-6">
              <label class="form-label">Received by <span class="text-danger">*</span></label>
              @php
                  // Compose the visible trigger label as "Name - EmpID" while
                  // keeping the hidden form fields strictly normalised:
                  //   • received_by         → cleaned employee name only
                  //   • received_by_emp_id  → HRMS employment_id
                  // Legacy GRNs may have stored the ID glued onto the name
                  // ("Kalaiselvi10022") or wrapped in brackets / dashes, so we
                  // strip those artefacts before re-appending the canonical EmpID.
                  $rcvName = trim((string) $o('received_by'));
                  $rcvEmp  = trim((string) $o('received_by_emp_id'));
                  if ($rcvName !== '') {
                      $cleaned = preg_replace('/\s*(?:[\(\[]\s*|[-·]\s*)(?:EMP(?:LOYEE)?[-\s]?)?[A-Za-z0-9_-]+\s*[\)\]]?\s*$/iu', '', $rcvName);
                      if (is_string($cleaned) && trim($cleaned) !== '') { $rcvName = $cleaned; }
                      $cleaned = preg_replace('/(?<=\D)\s*\d{3,}\s*$/u', '', $rcvName);
                      if (is_string($cleaned) && trim($cleaned) !== '') { $rcvName = $cleaned; }
                      $rcvName = trim($rcvName);
                  }
                  $rcvDisplay = $rcvName !== ''
                      ? ($rcvEmp !== '' ? $rcvName . ' - ' . $rcvEmp : $rcvName)
                      : '';
              @endphp
              <div class="tax-dropdown-wrapper received-section">
                {{-- Trigger displays "Name - EmpID" so the user can see exactly
                     which employee is currently selected (matches the row format
                     inside the dropdown list and works the same on create + edit). --}}
                <input type="text" class="form-control received-search-input dropdown-search-input"
                       placeholder="Select a user" readonly value="{{ $rcvDisplay }}">
                <input type="hidden" name="received_by" class="received_by" value="{{ $rcvName }}">
                <input type="hidden" name="received_by_emp_id" class="received_by_emp_id" value="{{ $rcvEmp }}">
                <div class="dropdown-menu tax-dropdown">
                  <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search User..."></div>
                  <div class="dropdown-list received-list"></div>
                </div>
              </div>
              <div class="grn-field-error" data-for="received_by"></div>
            </div>
          </div>
        </section>

        {{-- ── Attachments & remarks ── --}}
        <section class="grn-sec">
          <div class="grn-sec-title">
            <i class="bi bi-paperclip"></i> Attachments &amp; remarks
            <small>Invoice copy (PDF) and GPS verification video</small>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Invoice copy (PDF) @if(!$isEdit || !optional($r)->invoice_copy_path)<span class="text-danger">*</span>@endif</label>
              <label class="grn-upload" data-target="invoice_copy" data-kind="pdf">
                <div class="grn-upload-row">
                  <span class="grn-upload-ico"><i class="bi bi-filetype-pdf"></i></span>
                  <div class="grn-upload-text">
                    <div class="ttl">Click or drag a PDF file here</div>
                    <div class="sub">Max 10 MB · only .pdf accepted</div>
                  </div>
                </div>
                <input type="file" name="invoice_copy" accept=".pdf,application/pdf" data-max-mb="10">
                <div class="grn-upload-name">
                  <i class="bi bi-file-earmark-text"></i>
                  <span class="fname"></span>
                  <span class="x" title="Remove">×</span>
                </div>
              </label>
              <div class="grn-field-error" data-for="invoice_copy"></div>
              <div class="grn-upload-actions">
                {{-- "Preview new file" stays hidden until the user actually
                     selects a PDF (toggled in the .grn-upload change handler
                     and the file-size/type validator). --}}
                <button type="button" class="btn btn-outline-primary grn-preview-new" data-input="invoice_copy" data-kind="pdf" data-title="Invoice PDF preview" style="display:none;">
                  <i class="bi bi-eye me-1"></i>Preview new file
                </button>
                @if($isEdit && $r->invoice_copy_path)
                  <button type="button" class="btn btn-outline-primary grn-preview-existing" data-url="{{ asset('public/' . $r->invoice_copy_path) }}" data-kind="pdf" data-title="Current invoice PDF">
                    <i class="bi bi-cloud-arrow-down me-1"></i>Preview saved
                  </button>
                @endif
              </div>
              @if($isEdit && $r->invoice_copy_path)
                <div class="grn-current-file">
                  <i class="bi bi-link-45deg"></i>
                  <a href="{{ asset($r->invoice_copy_path) }}" target="_blank">Current invoice file</a>
                  — upload to replace.
                </div>
              @endif
            </div>

            <div class="col-md-6">
              <label class="form-label">GPS verification video @if(!$isEdit || !optional($r)->gps_video_path)<span class="text-danger">*</span>@endif</label>
              <label class="grn-upload" data-target="gps_video" data-kind="video">
                <div class="grn-upload-row">
                  <span class="grn-upload-ico" style="background:#ecfeff;color:#0e7490;"><i class="bi bi-camera-video"></i></span>
                  <div class="grn-upload-text">
                    <div class="ttl">Upload GPS-tagged video</div>
                    <div class="sub">Max 10 MB · .mp4, .webm, .mov</div>
                  </div>
                </div>
                <input type="file" name="gps_video" accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov" data-max-mb="50">
                <div class="grn-upload-name">
                  <i class="bi bi-film"></i>
                  <span class="fname"></span>
                  <span class="x" title="Remove">×</span>
                </div>
              </label>
              <div class="grn-field-error" data-for="gps_video"></div>
              <div class="grn-upload-actions">
                {{-- "Preview new video" stays hidden until the user actually
                     selects a video (toggled in the .grn-upload change handler
                     and the file-size/type validator). --}}
                <button type="button" class="btn btn-outline-primary grn-preview-new" data-input="gps_video" data-kind="video" data-title="GPS video preview" style="display:none;">
                  <i class="bi bi-eye me-1"></i>Preview new video
                </button>
                @if($isEdit && $r->gps_video_path)
                  <button type="button" class="btn btn-outline-primary grn-preview-existing" data-url="{{ asset('public/' . $r->gps_video_path) }}" data-kind="video" data-title="Current GPS video">
                    <i class="bi bi-cloud-arrow-down me-1"></i>Preview saved
                  </button>
                @endif
              </div>
              @if($isEdit && $r->gps_video_path)
                <div class="grn-current-file">
                  <i class="bi bi-link-45deg"></i>
                  <a href="{{ asset($r->gps_video_path) }}" target="_blank">Current video</a>
                  — upload to replace.
                </div>
              @endif
            </div>

            <div class="col-12">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="3" maxlength="5000"
                        placeholder="Optional notes (delivery condition, missing items, audit observations, etc.)">{{ $o('remarks') }}</textarea>
            </div>
          </div>
        </section>

        {{-- ── Sticky footer actions ── --}}
        <div class="grn-footer">
          <a href="{{ route('grn.index') }}" class="btn btn-light">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2-circle me-1"></i>{{ $isEdit ? 'Update GRN' : 'Save GRN' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ── Preview modal ── --}}
<div class="modal fade" id="grnPreviewModal" tabindex="-1" aria-hidden="true" aria-labelledby="grnPreviewModalLabel">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="grnPreviewModalLabel"><i class="bi bi-eye me-1"></i>Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="grnPreviewModalBody">
        <div class="grn-preview-empty">No file to preview yet.</div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(function () {
  // ── Toastr defaults ──
  if (window.toastr) {
    toastr.options = {
      closeButton: true,
      progressBar: true,
      positionClass: 'toast-top-right',
      timeOut: 4500,
      preventDuplicates: true,
      newestOnTop: true
    };
  }

  // ── Backend feedback (session + validation errors) ──
  @if(session('success'))
    if (window.toastr) toastr.success(@json(session('success')));
  @endif
  @if(session('error'))
    if (window.toastr) toastr.error(@json(session('error')));
  @endif
  @if($errors->any())
    if (window.toastr) {
      @foreach($errors->all() as $msg)
        toastr.error(@json($msg));
      @endforeach
    }
  @endif

  // ── Source data ──
  const ZONES     = @json($zones->map(fn($z) => ['id' => $z->id, 'name' => $z->name])->values());
  const BRANCHES  = @json($branches->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'zone_id' => $b->zone_id])->values());
  const COMPANIES = @json($companies->map(fn($c) => ['id' => $c->id, 'name' => $c->company_name])->values());
  const VENDORS   = @json($vendors->map(fn($v) => ['id' => $v->id, 'name' => $v->display_name])->values());

  // Employees come back as [{id, name, emp_id}]. Render rows as Name + EMP-ID pill.
  @php
    $employeeOptions = collect($employees ?? [])->map(function ($e) {
        $name = trim((string) ($e['name'] ?? ''));
        $emp  = trim((string) ($e['emp_id'] ?? ''));
        $id   = (string) ($e['id'] ?? ($emp !== '' ? $emp : $name));
        return [
            'id' => $id,
            'name' => $name,
            'emp_id' => $emp,
            'display' => $emp !== '' ? $name.' ('.$emp.')' : $name,
        ];
    })->values();
  @endphp
  const EMPLOYEES = @json($employeeOptions);

  const GRN_IS_EDIT = @json((bool) $isEdit);
  const GRN_HAS_INVOICE_FILE = @json((bool) ($isEdit && optional($r)->invoice_copy_path));
  const GRN_HAS_VIDEO_FILE = @json((bool) ($isEdit && optional($r)->gps_video_path));

  function escapeHtml(s) { return $('<div>').text(s == null ? '' : s).html(); }

  function paint($list, items, selectedId, opts) {
    opts = opts || {};
    $list.empty();
    if (!items.length) {
      $list.append('<div class="empty">' + escapeHtml(opts.emptyText || 'No options') + '</div>');
      return;
    }
    items.forEach(function (it) {
      const sel = (selectedId && String(selectedId) === String(it.id)) ? ' selected' : '';
      // searchable text used by the .inner-search filter (also shown as a fallback)
      const searchText = [it.name, it.emp_id, it.display].filter(Boolean).join(' ');
      // Employees render the same plain single-tone label as vendors so the
      // Received-by dropdown matches the Vendor dropdown UI design. The EmpID
      // is appended inline ("Name - EmpID") in the same typography so the user
      // can still disambiguate employees with identical names.
      let inner;
      if (opts.kind === 'employee') {
        inner = escapeHtml(it.name) + (it.emp_id ? ' - ' + escapeHtml(it.emp_id) : '');
      } else {
        inner = escapeHtml(it.name);
      }
      $list.append(
        '<div class="' + sel.trim() + '" ' +
        'data-id="' + escapeHtml(it.id) + '" ' +
        'data-text="' + escapeHtml(it.name) + '" ' +
        (opts.kind === 'employee' ? 'data-display="' + escapeHtml(it.display) + '" ' : '') +
        'data-search="' + escapeHtml(searchText) + '">' +
        inner +
        '</div>'
      );
    });
  }

  paint($('.company-list'), COMPANIES, $('.company_id').val());
  paint($('.zone-list'),    ZONES,     $('.zone_id').val());
  paint($('.vendor-list'),  VENDORS,   $('.vendor_id').val());

  // ── Received-by (HRMS) dropdown — name + emp ID pill ──
  function grnRenderEmployees() {
    const sel = $('.received_by_emp_id').val() || $('.received_by').val();
    if (EMPLOYEES.length) {
      paint($('.received-list'), EMPLOYEES, sel, { kind: 'employee', emptyText: 'No employees match' });
      $('#receivedHint').text('Sourced from HRMS · ' + EMPLOYEES.length + ' employees');
    } else {
      $('.received-list').empty().append('<div class="empty">Loading employees…</div>');
      $('#receivedHint').text('Loading from HRMS…');
    }
    // Throw away any cached body-cloned dropdown so the next click re-clones the fresh list.
    const $rcvInput = $('.received-search-input');
    const $cached = $rcvInput.data('dropdown');
    if ($cached) { $cached.remove(); $rcvInput.removeData('dropdown'); }
  }

  function grnLoadEmployeesFromApi(opts) {
    opts = opts || {};
    return $.ajax({
      url: '{{ route("hrms.employees") }}',
      method: 'GET',
      data: opts.refresh ? { refresh: 1 } : {},
      dataType: 'json'
    }).done(function (res) {
      const items = (res && res.data) ? res.data : [];
      EMPLOYEES.length = 0;
      items.forEach(function (it) {
        const name = (it.name || '').toString().trim();
        const emp = (it.emp_id || it.id || '').toString().trim();
        if (!name) return;
        EMPLOYEES.push({
          id: (it.id || emp || name).toString(),
          name: name,
          emp_id: emp,
          display: emp ? (name + ' (' + emp + ')') : name
        });
      });
      grnRenderEmployees();
      if (!EMPLOYEES.length) {
        $('.received-list').empty().append('<div class="empty">No employees found.</div>');
        $('#receivedHint').text('HRMS unavailable — try refreshing');
      }
    }).fail(function () {
      $('.received-list').empty().append('<div class="empty">Could not reach HRMS API</div>');
      $('#receivedHint').text('HRMS unavailable — try refreshing');
    });
  }

  grnRenderEmployees();
  // If the server-side load came back empty (transient HRMS hiccup, cache poisoning, etc.)
  // try once more from the browser so the user isn't stuck with an empty dropdown.
  if (!EMPLOYEES.length) {
    grnLoadEmployeesFromApi({ refresh: true });
  }

  const initialZone = $('.zone_id').val();
  const initialBranches = initialZone ? BRANCHES.filter(b => String(b.zone_id) === String(initialZone)) : [];
  paint($('.branch-list'), initialBranches, $('.branch_id').val(), { emptyText: 'Select zone first' });

  // ── Dropdown open / close (cloned to body for stable absolute positioning) ──
  function grnPositionFloating($input, $dropdown) {
    const rect = $input[0].getBoundingClientRect();
    $dropdown.css({
      position: 'absolute',
      top:   Math.round(rect.bottom + window.scrollY + 4) + 'px',
      left:  Math.round(rect.left   + window.scrollX) + 'px',
      width: Math.round(rect.width) + 'px',
      zIndex: 10080
    });
  }

  let $grnOpenInput = null;

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

    grnPositionFloating($input, $dropdown);
    $dropdown.show();
    $dropdown.find('.inner-search').val('').focus();
    $dropdown.find('.dropdown-list div').show();
    $dropdown.find('.dropdown-list').scrollTop(0);
    $grnOpenInput = $input;
  });

  // Keep the floating menu glued to the input as the user scrolls or resizes.
  $(window).on('scroll resize', function () {
    if (!$grnOpenInput || !$grnOpenInput.length) return;
    const $dropdown = $grnOpenInput.data('dropdown');
    if ($dropdown && $dropdown.is(':visible')) {
      grnPositionFloating($grnOpenInput, $dropdown);
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
    const $wrapper  = $dropdown.data('wrapper');
    if (!$wrapper) return;

    // Prefer the explicit data-text (employee row hides the ID inside a pill,
    // so we want just the name in the visible input). Falls back to text.
    const text = ($row.attr('data-text') || $row.text() || '').trim();
    const id   = $row.attr('data-id');

    $wrapper.find('.dropdown-search-input').val(text);
    if ($wrapper.hasClass('received-section')) {
      // Received-by stores the employee's full name in `received_by` (string column)
      // and the HRMS employment_id in `received_by_emp_id` for traceability.
      // The visible trigger shows "Name - EmpID" (mirrors the dropdown row
      // format) so the user always sees both pieces of information at a glance.
      const empId   = (id || '').toString().trim();
      const display = empId ? (text + ' - ' + empId) : text;
      $wrapper.find('.dropdown-search-input').val(display);
      $wrapper.find('input[name="received_by"]').val(text);
      $wrapper.find('input[name="received_by_emp_id"]').val(empId);
    } else {
      $wrapper.find('input[type="hidden"][name$="_id"]').val(id);
      $wrapper.find('input[type="hidden"][name$="_name"]').val(text);
    }

    // Keep the source dropdown list in sync so reopen shows the selected row.
    $wrapper.find('.dropdown-list div').each(function () {
      $(this).toggleClass('selected', $(this).attr('data-id') === id);
    });

    // Clear any existing inline error on this field
    $wrapper.removeClass('is-invalid');
    const $err = $wrapper.parent().find('.grn-field-error');
    if ($err.length) { $err.text('').removeClass('is-active'); }

    $dropdown.hide();
    $grnOpenInput = null;
    $wrapper.find('.dropdown-search-input').trigger('grn:change', [{ id: id, name: text }]);
  });

  $(document).on('click', function (e) {
    if (!$(e.target).closest('.tax-dropdown-wrapper').length && !$(e.target).closest('.grn-floating').length) {
      $('.grn-floating').hide();
      $grnOpenInput = null;
    }
  });
  $(document).on('click', '.grn-floating', function (e) { e.stopPropagation(); });

  // ── Zone change → reload Branch list (server-driven) ──
  function refreshBranches(items, zoneId) {
    paint($('.branch-list'), items, null, { emptyText: zoneId ? 'No branches in this zone' : 'Select zone first' });
    // Invalidate the cached floating-clone so the next open re-clones the fresh source
    const $branchInput = $('.branch-search-input');
    const $cached = $branchInput.data('dropdown');
    if ($cached) { $cached.remove(); $branchInput.removeData('dropdown'); }
    if (window.toastr && zoneId) {
      toastr.info(items.length + ' branch' + (items.length === 1 ? '' : 'es') + ' for the selected zone.', '', { timeOut: 1800 });
    }
  }

  $(document).on('grn:change', '.zone-search-input', function (_e, payload) {
    $('.branch-search-input').val('');
    $('.branch_id').val('');
    $('.branch_name').val('');

    const zoneId = payload && payload.id;
    if (!zoneId) {
      refreshBranches([], null);
      return;
    }
    $.ajax({
      url: '{{ route("superadmin.getbranchfetch") }}',
      method: 'POST',
      data: { id: zoneId, _token: '{{ csrf_token() }}' },
      success: function (res) {
        const items = (res && res.branch ? res.branch : []).map(b => ({ id: b.id, name: b.name }));
        refreshBranches(items, zoneId);
      },
      error: function () {
        if (window.toastr) toastr.warning('Could not fetch branches for this zone — showing local list.');
        const items = BRANCHES.filter(b => String(b.zone_id) === String(zoneId));
        refreshBranches(items, zoneId);
      }
    });
  });

  // ── File upload UX (drag/drop + filename pill + remove) ──
  $('.grn-upload').each(function () {
    const $wrap  = $(this);
    const $input = $wrap.find('input[type="file"]');
    const $name  = $wrap.find('.grn-upload-name');
    const $fname = $name.find('.fname');

    // The matching "Preview new …" button lives in the sibling actions block.
    // It must only become visible AFTER the user picks a valid file, and must
    // disappear again whenever the file is removed or rejected.
    const inputName = $input.attr('name');
    const $previewNew = $wrap.closest('.col-md-6')
        .find('.grn-preview-new[data-input="' + inputName + '"]');

    $input.on('change', function () {
      if (this.files && this.files[0]) {
        $fname.text(this.files[0].name);
        $name.addClass('is-set');
        if ($previewNew.length) $previewNew.show();
      } else {
        $name.removeClass('is-set');
        $fname.text('');
        if ($previewNew.length) $previewNew.hide();
      }
    });

    $name.find('.x').on('click', function (e) {
      e.preventDefault(); e.stopPropagation();
      $input.val('');
      $name.removeClass('is-set');
      $fname.text('');
      if ($previewNew.length) $previewNew.hide();
    });

    $wrap.on('dragover', function (e) { e.preventDefault(); $wrap.addClass('is-drag'); });
    $wrap.on('dragleave drop', function () { $wrap.removeClass('is-drag'); });
    $wrap.on('drop', function (e) {
      e.preventDefault();
      const dt = e.originalEvent && e.originalEvent.dataTransfer;
      if (dt && dt.files && dt.files.length) {
        $input[0].files = dt.files;
        $input.trigger('change');
      }
    });
  });

  // ── File-size + extension validation when a file is chosen ──
  function setFieldError(name, msg) {
    const $err = $('.grn-field-error[data-for="' + name + '"]');
    if (!$err.length) return;
    const $col = $err.parent();
    const $drops = $col.find('.tax-dropdown-wrapper');
    const $inps = $col.find('input[name="' + name + '"], textarea[name="' + name + '"], select[name="' + name + '"]');
    const $upload = $col.find('.grn-upload');
    if (msg) {
      $err.text(msg).addClass('is-active');
      $drops.addClass('is-invalid');
      $upload.addClass('is-invalid');
      $inps.addClass('is-invalid');
    } else {
      $err.text('').removeClass('is-active');
      $drops.removeClass('is-invalid');
      $upload.removeClass('is-invalid');
      $inps.removeClass('is-invalid');
    }
  }

  function focusForField(name) {
    const $err = $('.grn-field-error[data-for="' + name + '"]');
    if (!$err.length) return null;
    const $col = $err.parent();
    const $dd = $col.find('.dropdown-search-input').first();
    if ($dd.length) return $dd;
    const $inp = $col.find('[name="' + name + '"]').first();
    return $inp.length ? $inp : null;
  }

  $(document).on('input change', '#grnForm input[name="invoice_number"], #grnForm input[name="invoice_date"], #grnForm input[name="received_date"]', function () {
    const n = $(this).attr('name');
    if (n) setFieldError(n, '');
  });
  $('.grn-upload input[type="file"]').on('change.grnSize', function () {
    const $input = $(this);
    const name = $input.attr('name');
    const file = this.files && this.files[0];
    // Locate the matching "Preview new …" button so it can be hidden whenever
    // we reject or clear the upload below.
    const $previewNew = $input.closest('.col-md-6')
        .find('.grn-preview-new[data-input="' + name + '"]');
    if (!file) { setFieldError(name, ''); $previewNew.hide(); return; }

    const maxMb = parseInt($input.data('max-mb'), 10) || 0;
    const sizeMb = file.size / (1024 * 1024);
    if (maxMb > 0 && sizeMb > maxMb) {
      setFieldError(name, 'File too large (' + sizeMb.toFixed(1) + ' MB). Max allowed is ' + maxMb + ' MB.');
      if (window.toastr) toastr.error(file.name + ' is over the ' + maxMb + ' MB limit.', 'Upload rejected');
      $input.val('');
      $input.closest('.grn-upload').find('.grn-upload-name').removeClass('is-set').find('.fname').text('');
      $previewNew.hide();
      return;
    }

    const allowed = String($input.attr('accept') || '').split(',').map(s => s.trim().toLowerCase()).filter(Boolean);
    if (allowed.length) {
      const lname = (file.name || '').toLowerCase();
      const ftype = (file.type || '').toLowerCase();
      const ok = allowed.some(a => {
        if (a.indexOf('.') === 0) return lname.endsWith(a);
        if (a.indexOf('/*') === a.length - 2) return ftype.indexOf(a.slice(0, -1)) === 0;
        return ftype === a;
      });
      if (!ok) {
        setFieldError(name, 'This file type is not allowed.');
        if (window.toastr) toastr.error(file.name + ' is not an accepted file type.', 'Upload rejected');
        $input.val('');
        $input.closest('.grn-upload').find('.grn-upload-name').removeClass('is-set').find('.fname').text('');
        $previewNew.hide();
        return;
      }
    }

    setFieldError(name, '');
    // File passed all checks → reveal the preview-new control.
    $previewNew.show();
    if (window.toastr) toastr.success(file.name + ' selected · click "Preview" to verify.', '', { timeOut: 1800 });
  });

  // ── Preview modal (PDF iframe / video / image) ──
  // IMPORTANT: clearModal() must NOT revoke `previewBlobUrl` here. openPreview()
  // calls clearModal() before mounting the new <iframe>/<video>, and we need the
  // blob URL we *just* created in the .grn-preview-new handler to stay valid
  // until the element is rendered. The URL is revoked in two safe places:
  //   1) right before we create a replacement blob URL (next click), and
  //   2) when the modal is actually dismissed (hidden.bs.modal handler).
  let previewBlobUrl = null;
  function clearModal() {
    $('#grnPreviewModalBody').empty().html('<div class="grn-preview-empty">Loading…</div>');
  }
  function disposePreviewBlob() {
    if (previewBlobUrl) {
      try { URL.revokeObjectURL(previewBlobUrl); } catch (e) {}
      previewBlobUrl = null;
    }
  }
  function openPreview(src, kind, title) {
    if (!src) {
      if (window.toastr) toastr.warning('Nothing to preview.');
      return;
    }
    clearModal();
    $('#grnPreviewModalLabel').html('<i class="bi bi-eye me-1"></i>' + escapeHtml(title || 'Preview'));

    const safeSrc = String(src).replace(/"/g, '&quot;');
    let $content;
    if (kind === 'pdf') {
      $content = $('<iframe>').attr({ src: safeSrc, allow: 'fullscreen', title: 'PDF preview' });
    } else if (kind === 'video') {
      $content = $('<video>').attr({ src: safeSrc, controls: 'controls', autoplay: 'autoplay', playsinline: 'playsinline' });
    } else if (kind === 'image') {
      $content = $('<img>').attr({ src: safeSrc, alt: title || '' });
    } else {
      $content = $('<div class="grn-preview-empty">Preview not available for this file type.</div>');
    }
    $('#grnPreviewModalBody').empty().append($content);

    const el = document.getElementById('grnPreviewModal');
    if (!el) {
      if (window.toastr) toastr.error('Preview window is missing — please refresh the page.');
      return;
    }
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      try {
        bootstrap.Modal.getOrCreateInstance(el).show();
      } catch (err) {
        console.error('GRN preview modal error', err);
        if (window.toastr) toastr.error('Could not open the preview window.');
      }
    } else {
      // Fallback: show without backdrop
      console.warn('Bootstrap Modal API not available — showing fallback preview.');
      $(el).addClass('show').css({ display: 'block' });
      $('body').addClass('modal-open').css('overflow', 'hidden');
      if (!$('#grnPreviewBackdrop').length) {
        $('<div id="grnPreviewBackdrop" class="modal-backdrop fade show"></div>').appendTo('body');
      }
      $(el).find('[data-bs-dismiss="modal"]').off('click.grnFallback').on('click.grnFallback', function () {
        $(el).removeClass('show').css('display', 'none');
        $('body').removeClass('modal-open').css('overflow', '');
        $('#grnPreviewBackdrop').remove();
        clearModal();
      });
    }
  }

  $(document).on('click', '.grn-preview-new', function () {
    const inputName = $(this).data('input');
    const kind = $(this).data('kind');
    const title = $(this).data('title');
    const input = document.querySelector('[name="' + inputName + '"]');
    if (!input || !input.files || !input.files[0]) {
      if (window.toastr) toastr.warning('Choose a file first to preview.');
      return;
    }
    // Release any URL from a previous preview, then mint a fresh one for this
    // file. The new URL must outlive openPreview()'s internal clearModal()
    // call so the <iframe>/<video> can actually load it.
    disposePreviewBlob();
    previewBlobUrl = URL.createObjectURL(input.files[0]);
    openPreview(previewBlobUrl, kind, title);
  });

  $(document).on('click', '.grn-preview-existing', function () {
    // Saved files use a normal asset URL — make sure no stale blob URL keeps
    // leaking memory in the background once we switch to the saved preview.
    disposePreviewBlob();
    openPreview($(this).data('url'), $(this).data('kind'), $(this).data('title'));
  });

  $('#grnPreviewModal').on('hidden.bs.modal', function () {
    clearModal();
    disposePreviewBlob();
  });

  // ── Refresh HRMS employees ──
  $('#receivedRefresh').on('click', function (e) {
    e.preventDefault();
    const $btn = $(this);
    const original = $btn.html();
    $btn.html('<i class="bi bi-arrow-repeat"></i> Refreshing…');
    grnLoadEmployeesFromApi({ refresh: true })
      .done(function () {
        if (window.toastr) {
          if (EMPLOYEES.length) toastr.success('Employee list refreshed.');
          else                  toastr.warning('HRMS API returned no employees.');
        }
      })
      .fail(function () {
        if (window.toastr) toastr.error('Could not reach the HRMS API.');
      })
      .always(function () { $btn.html(original); });
  });

  // ── Frontend submit validation (remarks optional; all other fields required) ──
  function validateBeforeSubmit() {
    let firstName = null;

    function requireVal(name, message) {
      const $inp = $('[name="' + name + '"]');
      const val = ($inp.val() || '').toString().trim();
      if (!val) {
        setFieldError(name, message);
        if (!firstName) firstName = name;
      } else {
        setFieldError(name, '');
      }
    }

    requireVal('company_id', 'Company field is required.');
    requireVal('zone_id', 'Zone field is required.');
    requireVal('branch_id', 'Branch field is required.');
    requireVal('vendor_name', 'Vendor name field is required.');
    requireVal('invoice_number', 'Invoice number field is required.');
    requireVal('invoice_date', 'Invoice date field is required.');
    requireVal('received_date', 'Received date field is required.');
    requireVal('received_by', 'Received by field is required.');

    const inv = ($('[name="invoice_date"]').val() || '').toString();
    const rcv = ($('[name="received_date"]').val() || '').toString();
    if (inv && rcv && new Date(rcv) < new Date(inv)) {
      setFieldError('received_date', 'Received date cannot be before invoice date.');
      if (!firstName) firstName = 'received_date';
    }

    const needInvoice = !GRN_IS_EDIT || !GRN_HAS_INVOICE_FILE;
    const invInput = document.querySelector('[name="invoice_copy"]');
    if (needInvoice && (!invInput || !invInput.files || !invInput.files.length)) {
      setFieldError('invoice_copy', 'Invoice copy (PDF) field is required.');
      if (!firstName) firstName = 'invoice_copy';
    } else {
      setFieldError('invoice_copy', '');
    }

    const needVideo = !GRN_IS_EDIT || !GRN_HAS_VIDEO_FILE;
    const vidInput = document.querySelector('[name="gps_video"]');
    if (needVideo && (!vidInput || !vidInput.files || !vidInput.files.length)) {
      setFieldError('gps_video', 'GPS verification video field is required.');
      if (!firstName) firstName = 'gps_video';
    } else {
      setFieldError('gps_video', '');
    }

    return firstName;
  }

  $('#grnForm').on('submit', function (e) {
    const firstName = validateBeforeSubmit();
    if (firstName) {
      e.preventDefault();
      if (window.toastr) toastr.error('Please fix the highlighted fields, then submit again.', 'Validation failed');
      const $el = focusForField(firstName);
      if ($el && $el.length) {
        try {
          $el[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
          $el.focus();
        } catch (err) {}
      }
      return false;
    }
  });

  // ── Render backend per-field errors inline ──
  @if ($errors->any())
    @foreach (['company_id','zone_id','branch_id','vendor_name','invoice_number','invoice_date','received_date','received_by','invoice_copy','gps_video','remarks'] as $f)
      @if ($errors->has($f))
        setFieldError(@json($f), @json($errors->first($f)));
      @endif
    @endforeach
  @endif
});
</script>
@include('superadmin.superadminfooter')
</body>
</html>
