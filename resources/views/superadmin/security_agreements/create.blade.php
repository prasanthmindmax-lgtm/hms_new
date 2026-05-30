@extends('superadmin.layouts.app')

@section('body_class', 'security-agreement-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/security_agreement.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section('head_scripts')
<script id="saBranchesData" type="application/json">@json($branches->map(fn ($branch) => ['id' => (int) $branch->id, 'name' => $branch->name, 'zone_id' => (int) $branch->zone_id])->values())</script>
@endsection

@section('content')
@php
  $r = $record ?? null;
  $isEdit = ! empty($isEdit) && $r;
  $o = function (string $key, $default = '') use ($r) {
      $old = old($key);
      if ($old !== null) {
          return $old;
      }

      return $r ? data_get($r, $key, $default) : $default;
  };

  $selectedCompanyId = old('company_id', $r?->company_id);
  $selectedZoneId = old('zone_id', $r?->zone_id);
  $selectedBranchId = old('branch_id', $r?->branch_id);
  $selectedCompanyName = $selectedCompanyId ? ($companies->firstWhere('id', (int) $selectedCompanyId)?->company_name ?? '') : '';
  $selectedZoneName = $selectedZoneId ? ($zones->firstWhere('id', (int) $selectedZoneId)?->name ?? '') : '';
  $selectedBranchName = $selectedBranchId ? ($branches->firstWhere('id', (int) $selectedBranchId)?->name ?? '') : '';

  $saAttachTypeClass = static function (string $kind): string {
      return match ($kind) {
          'pdf' => 'pr-gmail-type-pdf',
          'doc' => 'pr-gmail-type-doc',
          'image' => 'pr-gmail-type-img',
          default => 'pr-gmail-type-file',
      };
  };

  $agreementDate = old('agreement_date', $r?->agreement_date?->format('Y-m-d'));
  $agreementPeriod = old('agreement_period', $r?->agreement_period ?? '');
  $agreementPeriodStart = old('agreement_period_start', '');
  $agreementPeriodEnd = old('agreement_period_end', '');
  if (($agreementPeriodStart === '' || $agreementPeriodEnd === '') && $r && filled($r->agreement_period)) {
      $parts = preg_split('/\s+to\s+/i', (string) $r->agreement_period);
      if (is_array($parts) && count($parts) === 2) {
          try {
              $agreementPeriodStart = \Carbon\Carbon::createFromFormat('d-m-Y', trim((string) $parts[0]))->format('Y-m-d');
              $agreementPeriodEnd = \Carbon\Carbon::createFromFormat('d-m-Y', trim((string) $parts[1]))->format('Y-m-d');
          } catch (\Throwable $e) {
              $agreementPeriodStart = '';
              $agreementPeriodEnd = '';
          }
      } elseif (is_array($parts) && count($parts) === 1) {
          try {
              $singleDate = \Carbon\Carbon::parse(trim((string) $parts[0]))->format('Y-m-d');
              $agreementPeriodStart = $singleDate;
              $agreementPeriodEnd = $singleDate;
          } catch (\Throwable $e) {
              $agreementPeriodStart = '';
              $agreementPeriodEnd = '';
          }
      }
  }
  $endDate = old('end_of_agreement_date', $r?->end_of_agreement_date?->format('Y-m-d'));
  $selectedVendorId = old('vendor_id', $r?->vendor_id ?? '');
  if ($selectedVendorId !== '' && ! is_numeric($selectedVendorId)) {
      $selectedVendorId = '';
  }
  $rcmApplicable = (string) $o('rcm_applicable', $r && $r->rcm_applicable ? '1' : '0');
  if (! in_array($rcmApplicable, ['0', '1'], true)) {
      $rcmApplicable = $r && $r->rcm_applicable ? '1' : '0';
  }
  $selectedVendorDisplay = '';
  if ($selectedVendorId !== '') {
      $matchedVendor = ($vendors ?? collect())->firstWhere('id', (int) $selectedVendorId);
      if ($matchedVendor) {
          $selectedVendorDisplay = $matchedVendor->listDisplayLabel();
      }
  }
  $housekeepingPaidLeaveApplicable = (string) old(
      'housekeeping_paid_leave_applicable',
      $r && ($r->housekeeping_paid_leave_applicable ?? false) ? '1' : '0'
  );
  $housekeepingPaidLeaveDays = old('housekeeping_paid_leave_days', $r?->housekeeping_paid_leave_days ?? '');
  $raIndexBackQs = [];
  if (request()->filled('category')) {
      $raIndexBackQs['category'] = request('category');
  }
@endphp
    <div class="security-agreement create pr-pay-module pr-form-page pr-pay-form-page pay-create-surface">
      <div class="card card--form">
        <header class="hero">
          <div>
            <div class="hero-kicker">
              <i class="bi bi-file-earmark-text"></i>
              {{ $moduleTitle }}
            </div>
            <h1 class="hero-title">
              <i class="bi bi-house-check"></i>
              @if ($isEdit)
                Edit {{ $moduleTitleLower }}
              @else
                New security agreement
              @endif
            </h1>
          </div>
          <div class="hero-actions">
            <a href="{{ $isEdit ? route($routeNames['show'], ['securityAgreement' => $r]) : route($routeNames['index'], $raIndexBackQs) }}" class="btn-ghost">
              <i class="bi bi-arrow-left"></i>
              {{ $isEdit ? 'Back to agreement' : 'Back to list' }}
            </a>
          </div>
        </header>

        <div class="page-body">
          <style>
            .card.card--form,
            .card.card--form .page-body,
            .card.card--form .section,
            .card.card--form form {
              overflow: visible !important;
            }
            .location-strip .pr-dd-wrap .pr-dd-panel {
              width: 100%;
              min-width: 100%;
            }
            .location-strip .pr-dd-wrap .company-list,
            .location-strip .pr-dd-wrap .zone-list,
            .location-strip .pr-dd-wrap .branch-list,
            .location-strip .pr-dd-wrap .vendor-list {
              max-height: 220px;
              overflow-y: auto;
            }
          </style>

          <form method="post" action="{{ $isEdit ? route($routeNames['update'], ['securityAgreement' => $r]) : route($routeNames['store']) }}" enctype="multipart/form-data" class="pr-form-premium js-sa-validate" id="sa_agreement_form" novalidate>
            @csrf
            @if ($isEdit)
              @method('PUT')
            @endif

            <div class="form-stack">

            <section class="form-block form-block--details pr-pay-sec--location">
            <header class="form-block-head">
            <i class="bi bi-journal-text" aria-hidden="true"></i>
            <span>Agreement details</span>
            </header>
            <div class="form-block-body">
              <div class="row g-3 location-strip" id="payLocationStrip">
                <div class="col-lg-4 col-md-6" data-field="company_id">
                  <div class="tax-dropdown-wrapper company-section pr-dd-wrap">
                    <label class="form-label mb-0" for="rental_dd_company">Company <span class="text-danger">*</span></label>
                    <input id="rental_dd_company" type="text"
                      class="form-control company-search-input pr-dd-input pr-loc-dd-input @error('company_id') is-invalid @enderror"
                      readonly autocomplete="off" placeholder="Select company"
                      value="{{ $selectedCompanyName }}">
                    <input type="hidden" name="company_id" class="company_id" value="{{ $selectedCompanyId ?: '' }}">
                    <div class="tax-dropdown pr-dd-panel">
                      <div class="inner-search-container">
                        <input type="text" class="inner-search form-control form-control-sm" placeholder="Search company..." autocomplete="off">
                      </div>
                      <div class="company-list">
                        @foreach ($companies as $company)
                          <div data-value="{{ $company->company_name }}" data-id="{{ $company->id }}">{{ $company->company_name }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                  @error('company_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-lg-4 col-md-6" data-field="zone_id">
                  <div class="tax-dropdown-wrapper account-section pr-dd-wrap">
                    <label class="form-label mb-0" for="rental_dd_zone">Zone <span class="text-danger">*</span></label>
                    <input id="rental_dd_zone" type="text"
                      class="form-control zone-search-input pr-dd-input pr-loc-dd-input @error('zone_id') is-invalid @enderror"
                      readonly autocomplete="off" placeholder="Select zone"
                      value="{{ $selectedZoneName }}">
                    <input type="hidden" name="zone_id" class="zone_id" value="{{ $selectedZoneId ?: '' }}">
                    <div class="tax-dropdown pr-dd-panel">
                      <div class="inner-search-container">
                        <input type="text" class="inner-search form-control form-control-sm" placeholder="Search zone..." autocomplete="off">
                      </div>
                      <div class="zone-list">
                        @foreach ($zones as $zone)
                          <div data-id="{{ $zone->id }}" data-value="{{ $zone->name }}">{{ $zone->name }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                  @error('zone_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-lg-4 col-md-6" data-field="branch_id">
                  <div class="tax-dropdown-wrapper account-section pr-dd-wrap" data-field="branch_id">
                    <label class="form-label mb-0" for="rental_dd_branch">Branch <span class="text-danger">*</span></label>
                    <input id="rental_dd_branch" type="text"
                      class="form-control branch-search-input pr-dd-input pr-loc-dd-input @error('branch_id') is-invalid @enderror"
                      readonly autocomplete="off" placeholder="Select branch (after zone)"
                      value="{{ $selectedBranchName }}">
                    <input type="hidden" name="branch_id" class="branch_id" value="{{ $selectedBranchId ?: '' }}">
                    <div class="tax-dropdown pr-dd-panel">
                      <div class="inner-search-container">
                        <input type="text" class="inner-search form-control form-control-sm" placeholder="Search branch..." autocomplete="off">
                      </div>
                      <div class="branch-list"></div>
                    </div>
                  </div>
                  @error('branch_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                @if (!$isEdit)
                  <div class="col-lg-4 col-md-6" data-field="agreement_type">
                    <label class="field-label" for="sa_agreement_type">Category <span class="text-danger">*</span></label>
                    <select id="sa_agreement_type" name="agreement_type" class="field-input @error('agreement_type') is-invalid @enderror" required>
                      @php
                        $raCatSel = old('agreement_type', $defaultAgreementType ?? \App\Models\SecurityAgreement::TYPE_HOSPITAL);
                      @endphp
                      <option value="{{ \App\Models\SecurityAgreement::TYPE_HOSPITAL }}" @selected($raCatSel === \App\Models\SecurityAgreement::TYPE_HOSPITAL)>Hospital</option>
                      <option value="{{ \App\Models\SecurityAgreement::TYPE_HOSTEL }}" @selected($raCatSel === \App\Models\SecurityAgreement::TYPE_HOSTEL)>Hostel</option>
                    </select>
                    @error('agreement_type')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                @endif
                <div class="col-lg-4 col-md-6" data-field="agreement_date">
                  <label class="field-label">Security Agreement Date <span class="text-danger">*</span></label>
                  <input type="date" name="agreement_date" class="field-input @error('agreement_date') is-invalid @enderror" value="{{ $agreementDate }}">
                  @error('agreement_date')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-lg-4 col-md-6" data-field="vendor_id">
                  <div class="tax-dropdown-wrapper vendor-section pr-dd-wrap">
                    <label class="form-label mb-0" for="sa_dd_vendor">Vendor name <span class="text-danger">*</span></label>
                    <input id="sa_dd_vendor" type="text"
                      class="form-control vendor-search-input pr-dd-input pr-loc-dd-input @error('vendor_id') is-invalid @enderror"
                      readonly autocomplete="off" placeholder="Select vendor"
                      value="{{ $selectedVendorDisplay }}">
                    <input type="hidden" name="vendor_id" class="vendor_id" value="{{ $selectedVendorId }}">
                    <div class="tax-dropdown pr-dd-panel">
                      <div class="inner-search-container">
                        <input type="text" class="inner-search form-control form-control-sm" placeholder="Search vendor..." autocomplete="off">
                      </div>
                      <div class="vendor-list">
                        @foreach (($vendors ?? collect()) as $v)
                          @php $vendorLabel = $v->listDisplayLabel(); @endphp
                          <div data-value="{{ $vendorLabel }}" data-id="{{ $v->id }}" data-pan="{{ $v->pan_number ?? '' }}">{{ $vendorLabel }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                  @error('vendor_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="col-lg-4 col-md-6" data-field="agreement_period">
                  <label class="field-label">Agreement Period <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="agreementPeriodPicker"
                    name="agreement_period"
                    class="field-input @error('agreement_period') is-invalid @enderror @error('agreement_period_start') is-invalid @enderror @error('agreement_period_end') is-invalid @enderror"
                    value="{{ $agreementPeriod }}"
                    placeholder="DD-MM-YYYY to DD-MM-YYYY"
                    autocomplete="off"
                    readonly>
                  <input type="hidden" id="agreementPeriodStart" name="agreement_period_start" value="{{ $agreementPeriodStart }}">
                  <input type="hidden" id="agreementPeriodEnd" name="agreement_period_end" value="{{ $agreementPeriodEnd }}">
                  @error('agreement_period')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                  @error('agreement_period_start')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                  @error('agreement_period_end')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-lg-4 col-md-6" data-field="end_of_agreement_date">
                  <label class="field-label">End of Agreement Date <span class="text-danger">*</span></label>
                  <input type="date" id="endAgreementDate" name="end_of_agreement_date" class="field-input @error('end_of_agreement_date') is-invalid @enderror" value="{{ $endDate }}">
                  @error('end_of_agreement_date')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-lg-4 col-md-6" data-field="termination_period">
                  <label class="field-label">Termination Period <span class="text-danger">*</span></label>
                  <input type="text" name="termination_period" class="field-input @error('termination_period') is-invalid @enderror" maxlength="120" value="{{ $o('termination_period') }}" placeholder="e.g. 3 months notice" required>
                  @error('termination_period')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-12" data-field="address">
                  <label class="field-label">Address <span class="text-danger">*</span></label>
                  <textarea name="address" class="field-textarea @error('address') is-invalid @enderror" placeholder="Enter complete address">{{ $o('address') }}</textarea>
                  @error('address')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
            </section>

            @php
            $rcmApplicable = old('rcm_applicable', $o('rcm_applicable', '0'));
            if ($rcmApplicable === true || $rcmApplicable === 1) {
            $rcmApplicable = '1';
            } elseif ($rcmApplicable === false || $rcmApplicable === 0 || $rcmApplicable === null || $rcmApplicable === '') {
            $rcmApplicable = '0';
            } else {
            $rcmApplicable = (string) $rcmApplicable;
            }

            $raGstIncluding = \App\Models\SecurityAgreement::GST_INCLUDING;
            $raGstExcluding = \App\Models\SecurityAgreement::GST_EXCLUDING;
            $raGstTypeVal = (string) $o('gst_type');
            $raGstApplicable = old('gst_applicable');
            if ($raGstApplicable === null || $raGstApplicable === '') {
                $raGstApplicable = \App\Models\SecurityAgreement::isGstApplicableType($raGstTypeVal) ? '1' : '0';
            }
            $raGstApplicable = (string) $raGstApplicable;
            if (! in_array($raGstApplicable, ['0', '1'], true)) {
                $raGstApplicable = '0';
            }
            $raTaxModeInclusive = $raGstTypeVal === $raGstIncluding;
            $raShowGstCalc = $raGstApplicable === '1' && in_array($raGstTypeVal, [$raGstIncluding, $raGstExcluding], true);
            $raGstTaxName = $o('gst_tax_name');
            $raGstTaxId = $o('gst_tax_id');
            $raGstTaxType = strtoupper((string) $o('gst_tax_type', 'GST'));
            $raGstPct = $o('gst_percentage');
            $raGstSearchDisplay = (string) $raGstTaxName;
            if ($raGstSearchDisplay !== '' && $raGstPct !== '' && $raGstPct !== null) {
            $pctFmt = rtrim(rtrim(number_format((float) $raGstPct, 2), '0'), '.');
            $raGstSearchDisplay = $pctFmt . '% GST';
            }
            $raGstAmt = $o('gst_amount');
            $raCgst = $o('cgst_amount', '0');
            $raSgst = $o('sgst_amount', '0');
            $raIgst = $o('igst_amount', '0');
            $gstOptions = $gstOptions ?? \App\Models\SecurityAgreement::GST_TAX_MODE_LABELS;
            $gstTaxes = $gstTaxes ?? collect();
            $tdsTaxes = $tdsTaxes ?? collect();
            $raTdsTaxId = $o('tds_tax_id');
            $raTdsRate = $o('tds_rate');
            $raTdsAmt = $o('tds_amount');
            $raTdsSearchDisplay = '';
            if ($raTdsTaxId !== '' && $raTdsTaxId !== null) {
                $selectedTds = $tdsTaxes->firstWhere('id', (int) $raTdsTaxId);
                if ($selectedTds) {
                    $sectionName = trim((string) ($selectedTds->section_name ?? $selectedTds->section?->name ?? ''));
                    $pct = (float) $selectedTds->tax_rate;
                    $displayPct = $pct <= 1 && $pct > 0 ? $pct * 100 : $pct;
                    $pctFmt = rtrim(rtrim(number_format($displayPct, 2), '0'), '.');
                    $raTdsSearchDisplay = trim((string) $selectedTds->tax_name);
                    if ($pctFmt !== '') {
                        $raTdsSearchDisplay .= ' ['.$pctFmt.'%]';
                    }
                    if ($sectionName !== '') {
                        $raTdsSearchDisplay .= ' — '.$sectionName;
                    }
                }
            }
            $raTdsDisplayFormatted = $raTdsAmt !== '' && $raTdsAmt !== null
            ? number_format((float) $raTdsAmt, 2)
            : '';
            @endphp

            <section class="form-block">
            <header class="form-block-head">
            <i class="bi bi-person-badge" aria-hidden="true"></i>
            <span>Salary &amp; compliance</span>
            </header>
            <div class="form-block-body">
            <div class="form-grid form-grid--4 sa-salary-grid" data-paid-leave-role="housekeeping">
            <div data-field="security_fixed_salary_amount">
            <label class="field-label" for="sa_security_fixed_salary">Security fixed salary amount <span class="text-danger">*</span></label>
            <div class="amount-wrap">
            <span class="amount-prefix" aria-hidden="true">&#8377;</span>
            <input type="number" step="0.01" min="0" name="security_fixed_salary_amount" id="sa_security_fixed_salary" class="field-input amount-input @error('security_fixed_salary_amount') is-invalid @enderror" value="{{ $o('security_fixed_salary_amount') }}" placeholder="0.00" required>
            </div>
            @error('security_fixed_salary_amount')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            </div>
            <div data-field="housekeeping_fixed_salary_amount">
            <label class="field-label" for="sa_housekeeping_fixed_salary">Housekeeping fixed salary amount <span class="text-danger">*</span></label>
            <div class="amount-wrap">
            <span class="amount-prefix" aria-hidden="true">&#8377;</span>
            <input type="number" step="0.01" min="0" name="housekeeping_fixed_salary_amount" id="sa_housekeeping_fixed_salary" class="field-input amount-input @error('housekeeping_fixed_salary_amount') is-invalid @enderror" value="{{ $o('housekeeping_fixed_salary_amount') }}" placeholder="0.00" required>
            </div>
            @error('housekeeping_fixed_salary_amount')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            </div>
            <div data-field="housekeeping_paid_leave_applicable">
            <label class="field-label" for="sa_housekeeping_paid_leave_applicable">Housekeeping paid leave applicable <span class="text-danger">*</span></label>
            <select name="housekeeping_paid_leave_applicable" id="sa_housekeeping_paid_leave_applicable" class="field-select @error('housekeeping_paid_leave_applicable') is-invalid @enderror" data-paid-leave-toggle>
              <option value="0" @selected($housekeepingPaidLeaveApplicable === '0')>No</option>
              <option value="1" @selected($housekeepingPaidLeaveApplicable === '1')>Yes</option>
            </select>
            @error('housekeeping_paid_leave_applicable')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            </div>
            <div class="sa-paid-leave-days-wrap @if($housekeepingPaidLeaveApplicable !== '1') sa-paid-leave-days-wrap--hidden @endif" data-paid-leave-days-wrap data-field="housekeeping_paid_leave_days">
            <label class="field-label" for="sa_housekeeping_paid_leave_days">Housekeeping paid leave days <span class="text-danger">*</span></label>
            <input type="number" step="1" min="1" max="366" name="housekeeping_paid_leave_days" id="sa_housekeeping_paid_leave_days" class="field-input @error('housekeeping_paid_leave_days') is-invalid @enderror" value="{{ $housekeepingPaidLeaveDays }}" placeholder="e.g. 12" data-paid-leave-days>
            @error('housekeeping_paid_leave_days')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            </div>
            </div>

            </div>
            </section>

            <section class="form-block form-block--tax">
            <header class="form-block-head">
            <i class="bi bi-receipt" aria-hidden="true"></i>
            <span>GST &amp; TDS</span>
            </header>

            <div class="form-block-body">
            <div id="sa_gst_card" @class([
              'gst-card',
              'gst-card--expanded' => $raGstApplicable === '1',
              'gst-card--with-breakdown' => $raShowGstCalc,
            ])>
              <div class="gst-card-inputs">
                <div class="gst-inputs-row form-grid form-grid--4">
                  <div class="gst-input-field" data-field="gst_applicable">
                    <label class="field-label" for="sa_gst_applicable">GST applicable <span class="text-danger">*</span></label>
                    <select name="gst_applicable" id="sa_gst_applicable" class="field-select @error('gst_applicable') is-invalid @enderror">
                      <option value="0" @selected($raGstApplicable === '0')>No</option>
                      <option value="1" @selected($raGstApplicable === '1')>Yes</option>
                    </select>
                    @error('gst_applicable')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  </div>

                  <div id="sa_gst_detail_wrap" @class([
                    'gst-input-field',
                    'gst-detail-field',
                    'gst-detail-wrap',
                    'gst-detail-wrap--hidden' => $raGstApplicable !== '1',
                  ]) data-field="gst_type">
                    <label class="field-label" for="sa_gst_type">Tax mode <span class="text-danger gst-tax-mode-req">*</span></label>
                    <select name="gst_type" id="sa_gst_type" class="field-select @error('gst_type') is-invalid @enderror" @disabled($raGstApplicable !== '1')>
                      <option value="">Select tax mode</option>
                      @foreach ($gstOptions ?? \App\Models\SecurityAgreement::GST_TAX_MODE_LABELS as $gstKey => $gstLabel)
                        <option value="{{ $gstKey }}" @selected($raGstTypeVal === (string) $gstKey)>{{ $gstLabel }}</option>
                      @endforeach
                    </select>
                    @error('gst_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  </div>

                  <div @class([
                    'gst-input-field',
                    'gst-fields',
                    'gst-detail-field',
                    'gst-detail-wrap',
                    'gst-detail-wrap--hidden' => $raGstApplicable !== '1',
                    'gst-fields--hidden' => ! $raShowGstCalc,
                  ]) id="sa_gst_fields_wrap" data-field="gst_tax_id">
                    <label class="field-label" for="sa_gst_search_input">GST <span class="text-danger gst-req">*</span></label>
                    <div class="tax-dropdown-wrapper gst-section gst-dropdown w-100">
                      <input type="text"
                        class="form-control gst-search-input field-input @error('gst_tax_name') is-invalid @enderror"
                        id="sa_gst_search_input"
                        value="{{ $raGstSearchDisplay }}"
                        placeholder="Select GST"
                        readonly
                        autocomplete="off">
                      <input type="hidden" name="gst_tax_name" id="sa_gst_tax_name" value="{{ $raGstTaxName }}">
                      <input type="hidden" name="gst_percentage" class="selected-gst-tax" id="sa_gst_percentage" value="{{ $raGstPct }}">
                      <input type="hidden" name="gst_tax_type" class="gst_tax_type" id="sa_gst_tax_type" value="{{ $raGstTaxType }}">
                      <input type="hidden" name="gst_tax_id" class="gst-tax-id" id="sa_gst_tax_id" value="{{ $raGstTaxId }}">
                      <div class="dropdown-menu tax-dropdown gst-dropdown-menu">
                        <div class="tax-gst-list" id="sa_tax_gst_list">
                          @forelse ($gstTaxes ?? [] as $tax)
                            <div data-type="{{ $tax->tax_type }}" data-value="{{ $tax->tax_rate }}" data-id="{{ $tax->id }}" data-name="{{ $tax->tax_name }}">
                              {{ $tax->tax_name }} [{{ $tax->tax_rate }}%]
                            </div>
                          @empty
                            <div class="tax-list-empty text-muted small px-2 py-1">No GST taxes in gst_tax_tbl</div>
                          @endforelse
                        </div>
                      </div>
                    </div>
                    @error('gst_tax_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('gst_percentage')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>

              <div class="gst-card-breakdown gst-fields {{ $raShowGstCalc ? '' : 'gst-fields--hidden' }}" id="sa_gst_breakdown_panel" aria-live="polite" data-field="gst_amount">
                <h3 class="breakdown-heading">GST breakdown</h3>
                <div class="breakdown-grid">
                  <div class="breakdown-col">
                    <span class="breakdown-col-title">Security salary</span>
                    <div class="breakdown-box gst-calculate-output" id="sa_gst_breakdown_rent"></div>
                  </div>
                  <div class="breakdown-col">
                    <span class="breakdown-col-title">Housekeeping salary</span>
                    <div class="breakdown-box gst-calculate-output" id="sa_gst_breakdown_maintenance"></div>
                  </div>
                  <div class="breakdown-col breakdown-col--total">
                    <span class="breakdown-col-title">Combined total</span>
                    <div class="breakdown-box gst-calculate-output gst_calculate_show" id="sa_gst_breakdown_total"></div>
                  </div>
                </div>
                <input type="hidden" name="gst_amount" id="sa_gst_amount" value="{{ $raGstAmt }}">
                <input type="hidden" name="cgst_amount" id="sa_cgst_amount" value="{{ $raCgst }}">
                <input type="hidden" name="sgst_amount" id="sa_sgst_amount" value="{{ $raSgst }}">
                <input type="hidden" name="igst_amount" id="sa_igst_amount" value="{{ $raIgst }}">
                @error('gst_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>{{-- /sa_gst_card --}}

            <div class="tax-row tax-row--tds tax-row--tds-single" id="sa_tds_fields_wrap">
            <div class="tax-col" data-field="tds_tax_id">
            <label class="field-label" for="sa_tds_search_input">TDS <span class="text-danger">*</span></label>
            <div class="tax-dropdown-wrapper tds-tax-section tds-dropdown w-100">
            <input type="text"
            class="form-control tax-search-input field-input @error('tds_tax_id') is-invalid @enderror"
            id="sa_tds_search_input"
            value="{{ $raTdsSearchDisplay }}"
            placeholder="Select TDS tax"
            readonly
            autocomplete="off">
            <input type="hidden" name="tds_tax_id" class="tds-tax-id" id="sa_tds_tax_id" value="{{ $raTdsTaxId }}">
            <input type="hidden" id="sa_tds_rate" value="{{ $raTdsRate }}">
            <div class="dropdown-menu tax-dropdown tds-dropdown-menu">
            <div class="tax-list" id="sa_tax_tds_list">
            @forelse ($tdsTaxes ?? [] as $tax)
            @php
            $sectionName = trim((string) ($tax->section_name ?? $tax->section?->name ?? ''));
            @endphp
            <div
            data-value="{{ $tax->tax_rate }}"
            data-id="{{ $tax->id }}"
            data-name="{{ $tax->tax_name }}"
            data-section-name="{{ $sectionName }}">
            {{ $tax->tax_name }} [{{ $tax->tax_rate }}%]@if ($sectionName !== '') &mdash; {{ $sectionName }}@endif
            </div>
            @empty
            <div class="tax-list-empty text-muted small px-2 py-1">No TDS taxes in tds_tax_tbl</div>
            @endforelse
            </div>
            </div>
            </div>
            @error('tds_tax_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            </div>

            <div class="tds-breakdown-panel mt-3" id="sa_tds_breakdown_panel" aria-live="polite">
              <h3 class="breakdown-heading">TDS breakdown</h3>
              <div class="breakdown-grid">
                <div class="breakdown-col">
                  <span class="breakdown-col-title">Security salary</span>
                  <div class="breakdown-box tds-calculate-output" id="sa_tds_breakdown_security"></div>
                </div>
                <div class="breakdown-col">
                  <span class="breakdown-col-title">Housekeeping salary</span>
                  <div class="breakdown-box tds-calculate-output" id="sa_tds_breakdown_housekeeping"></div>
                </div>
                <div class="breakdown-col breakdown-col--total">
                  <span class="breakdown-col-title">Combined total</span>
                  <div class="breakdown-box tds-calculate-output" id="sa_tds_breakdown_total"></div>
                </div>
              </div>
              <input type="hidden" id="sa_tds_amount" value="{{ $raTdsAmt }}">
              @error('tds_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              <p class="field-hint mb-0 mt-2">TDS is calculated on security and housekeeping fixed salary amounts, then combined.</p>
            </div>
            </div>
            </section>

            <section class="form-block">
            <header class="form-block-head">
            <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
            <span>RCM details</span>
            </header>
            <div class="form-block-body form-grid form-grid--4">
            <div data-field="rcm_applicable">
            <label class="field-label" for="sa_rcm_applicable">RCM applicable <span class="text-danger">*</span></label>
            <select name="rcm_applicable" id="sa_rcm_applicable" class="field-select @error('rcm_applicable') is-invalid @enderror">
            <option value="0" @selected($rcmApplicable === '0')>No</option>
            <option value="1" @selected($rcmApplicable === '1')>Yes</option>
            </select>
            @error('rcm_applicable')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div id="sa_rcm_value_wrap" data-field="rcm_value" @class(['rcm-value rcm-value--hidden' => $rcmApplicable !== '1'])>
            <label class="field-label" for="sa_rcm_value">RCM value <span class="text-danger rcm-value-req">*</span></label>
            <div class="amount-wrap">
            <span class="amount-prefix" aria-hidden="true">&#8377;</span>
            <input type="number" step="0.01" min="0" name="rcm_value" id="sa_rcm_value" class="field-input amount-input @error('rcm_value') is-invalid @enderror" value="{{ $o('rcm_value') }}" placeholder="0.00" @disabled($rcmApplicable !== '1')>
            </div>
            @error('rcm_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <p class="field-hint mb-0">Enter RCM amount if applicable.</p>
            </div>
            </div>
            </section>

            </div>

            <div class="section">
              <div class="section-title">
                <i class="bi bi-person-vcard"></i>
                Contact and documents
              </div>
              <div class="row g-3">
                <div class="col-lg-4 col-md-6" data-field="pan_number">
                  <label class="field-label">PAN Number <span class="text-danger">*</span></label>
                  <input type="text" name="pan_number" class="field-input @error('pan_number') is-invalid @enderror" maxlength="30" value="{{ $o('pan_number') }}" placeholder="Enter PAN number" required>
                  @error('pan_number')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-lg-4 col-md-6" data-field="contact_person_name">
                  <label class="field-label">Contact Person Name <span class="text-danger">*</span></label>
                  <input type="text" name="contact_person_name" class="field-input @error('contact_person_name') is-invalid @enderror" maxlength="255" value="{{ $o('contact_person_name') }}" placeholder="Enter contact person name" required>
                  @error('contact_person_name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-lg-4 col-md-6" data-field="contact_person_number">
                  <label class="field-label">Contact Person Number <span class="text-danger">*</span></label>
                  <input type="text" name="contact_person_number" class="field-input @error('contact_person_number') is-invalid @enderror" maxlength="30" value="{{ $o('contact_person_number') }}" placeholder="Enter contact number" required>
                  @error('contact_person_number')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                @foreach (\App\Models\SecurityAgreement::FILE_SLOTS as $slot => $fileMeta)
                  @php
                    $fileInput = \App\Models\SecurityAgreement::FILE_INPUT_NAMES[$slot];
                    $keepInput = \App\Models\SecurityAgreement::FILE_KEEP_INPUT_NAMES[$slot];
                    $existingDocuments = $r ? $r->documentsForSlot($slot) : [];
                    $hasExisting = count($existingDocuments) > 0;
                    $slotRequired = ! ($isEdit && $hasExisting);
                    $uploadBoxId = 'sa-upload-box-'.$slot;
                  @endphp
                  <div class="col-12 col-lg-4 sa-doc-upload-col" data-field="{{ $fileInput }}">
                    <label class="field-label" for="{{ $fileInput }}">
                      {{ $fileMeta['label'] }}
                      <span class="text-danger">*</span>
                    </label>
                    @if ($isEdit && $hasExisting)
                      <div class="sa-doc-existing-attach mb-2" data-sa-existing-attach-section>
                        <p class="small text-muted mb-2">Click × to remove on save. Upload more files below.</p>
                        <div class="pr-gmail-attach-grid pr-gmail-attach-grid--row" role="list" aria-live="polite">
                          @foreach ($existingDocuments as $doc)
                            @if (($doc['path'] ?? '') !== '' && ! empty($doc['url']))
                              @php
                                $previewKind = (string) ($doc['preview_kind'] ?? 'other');
                                $previewTitle = (string) ($doc['name'] ?? $fileMeta['label']);
                              @endphp
                              <div class="pr-gmail-attach-card" role="listitem" data-sa-existing-attach-card data-file-name="{{ $previewTitle }}">
                                <input type="checkbox" name="{{ $keepInput }}[]" value="{{ $doc['path'] }}" checked class="d-none sa-attach-keep" aria-hidden="true">
                                <button type="button" class="pr-gmail-attach-remove" title="Remove {{ $previewTitle }}" aria-label="Remove {{ $previewTitle }}">
                                  <i class="bi bi-x-lg" aria-hidden="true"></i>
                                </button>
                                <button type="button"
                                  class="pr-gmail-attach-thumb"
                                  data-sa-attach-preview
                                  data-sa-attach-preview-url="{{ $doc['url'] }}"
                                  data-sa-attach-preview-kind="{{ $previewKind }}"
                                  data-sa-attach-preview-title="{{ $previewTitle }}"
                                  title="Preview {{ $previewTitle }}">
                                  <span class="pr-gmail-attach-thumb-inner pr-gmail-attach-thumb--{{ $previewKind }}">
                                    @if ($previewKind === 'image')
                                      <img src="{{ $doc['url'] }}" alt="" loading="lazy" class="pr-gmail-attach-thumb-media">
                                    @elseif ($previewKind === 'pdf')
                                      <iframe src="{{ $doc['url'] }}#toolbar=0&navpanes=0&scrollbar=0" title="" class="pr-gmail-attach-thumb-media" loading="lazy"></iframe>
                                    @else
                                      <span class="pr-gmail-attach-thumb-fallback" aria-hidden="true"><i class="bi {{ $doc['icon'] ?? 'bi-file-earmark-text' }}"></i></span>
                                    @endif
                                  </span>
                                </button>
                                <div class="pr-gmail-attach-foot">
                                  <span class="pr-gmail-type-badge {{ $saAttachTypeClass($previewKind) }}">{{ $doc['badge'] ?? 'FILE' }}</span>
                                  <span class="pr-gmail-attach-name" title="{{ $previewTitle }}">{{ $previewTitle }}</span>
                                </div>
                                <span class="pr-gmail-attach-fold" aria-hidden="true"></span>
                              </div>
                            @endif
                          @endforeach
                        </div>
                      </div>
                    @endif
                    <div class="pr-pay-attachment-zone sa-doc-upload-zone">
                      <div class="pr-pay-upload-box sa-doc-upload-box @error($fileInput) border border-danger @enderror @error($fileInput.'.*') border border-danger @enderror"
                           id="{{ $uploadBoxId }}"
                           data-sa-doc-upload-box="{{ $slot }}">
                        <div class="pr-pay-upload-icon"><i class="bi bi-cloud-arrow-up" aria-hidden="true"></i></div>
                        <div class="pr-pay-upload-text">Drag &amp; drop or <span>browse files</span></div>
                        <p class="pr-pay-upload-hint">PDF, images, Word, or Excel up to 10 MB per file.</p>
                        <input type="file"
                          name="{{ $fileInput }}[]"
                          id="{{ $fileInput }}"
                          class="sa-doc-file-input @error($fileInput) is-invalid @enderror @error($fileInput.'.*') is-invalid @enderror"
                          data-sa-doc-input="{{ $slot }}"
                          data-sa-doc-preview="{{ $fileInput }}-preview"
                          data-sa-doc-upload-box="{{ $uploadBoxId }}"
                          multiple
                          accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.webp,application/pdf,image/*"
                          @if ($slotRequired && ! ($isEdit && $hasExisting)) required @endif>
                      </div>
                    </div>
                    <div id="{{ $fileInput }}-preview" class="sa-doc-pending-preview mt-2 d-none" aria-live="polite"></div>
                    @error($fileInput)
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error($fileInput.'.*')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                @endforeach
              </div>
            </div>

            <div class="form-footer">
              <div class="form-footer-meta">
                {{ $isEdit ? 'Update the '.$moduleTitleLower.' details and save your changes.' : 'A reference number will be generated automatically when the '.$moduleTitleLower.' is saved.' }}
              </div>
              <a href="{{ $isEdit ? route($routeNames['show'], ['securityAgreement' => $r]) : route($routeNames['index'], $raIndexBackQs) }}" class="btn-outline">
                Cancel
              </a>
              <button type="submit" class="btn-primary border-0">
                <i class="bi bi-check2-circle"></i>
                {{ $isEdit ? 'Update agreement' : 'Save agreement' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
@endsection

@section('modals')
<div class="modal fade preview-modal pr-show-attachment-modal" id="attachmentPreviewModal" tabindex="-1" aria-labelledby="attachmentPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-bottom py-2 py-md-3">
        <h2 class="modal-title h5 mb-0 fw-bold text-truncate pe-2" id="attachmentPreviewModalLabel">Document preview</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 bg-light pr-show-attachment-modal-body preview-modal-body">
        <iframe id="attachmentPreviewIframe" class="preview-modal-iframe pr-show-attachment-iframe d-none" title="Document preview"></iframe>
        <img id="attachmentPreviewImg" class="preview-modal-img pr-show-attachment-img d-none img-fluid d-block mx-auto" alt="" />
        <div id="attachmentPreviewFallback" class="preview-modal-fallback d-none">
          <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
          <p class="mb-2">Preview is not available for this file type.</p>
          <a href="#" id="attachmentPreviewOpenLink" class="btn btn-sm btn-primary" target="_blank" rel="noopener">Open document</a>
        </div>
      </div>
      <div class="modal-footer py-2 border-top">
        <a href="#" id="attachmentPreviewFooterLink" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
          <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Open in new tab
        </a>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('assets/js/form_field_validation.js') }}"></script>
@if (session('success') || session('error') || ($errors ?? null)?->any())
<script>
(function () {
  if (window.__saFlashToastShown || typeof window.toastr === 'undefined') {
    return;
  }
  window.__saFlashToastShown = true;
  @if (session('success'))
  toastr.success(@json(session('success')));
  @endif
  @if (session('error'))
  toastr.error(@json(session('error')));
  @endif
  @php $validationMessages = ($errors ?? null)?->all() ?? []; @endphp
  @if (count($validationMessages) > 0)
  if (window.FormFieldValidation && typeof window.FormFieldValidation.showBackendToasts === 'function') {
    FormFieldValidation.showBackendToasts(@json($validationMessages), {
      summary: @json(count($validationMessages) > 1 ? 'Please correct the highlighted fields.' : '')
    });
  } else {
    @foreach ($validationMessages as $idx => $msg)
    setTimeout(function () { toastr.error(@json($msg)); }, {{ (int) $idx * 120 }});
    @endforeach
  }
  @endif
})();
</script>
@endif
<script src="{{ asset('assets/js/security_agreement.js') }}?v={{ @filemtime(public_path('assets/js/security_agreement.js')) }}"></script>
@endpush
