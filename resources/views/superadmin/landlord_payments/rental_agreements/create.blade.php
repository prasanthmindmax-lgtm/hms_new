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
  $currentAttachmentUrl = $r ? \App\Models\RentalAgreement::attachmentPublicUrl($r->attachment_path) : null;
  $currentAttachmentName = $r?->attachment_original_name ?: ($r?->attachment_path ? basename((string) $r->attachment_path) : '');
  $currentBuildingPhotoUrl = $r ? \App\Models\RentalAgreement::buildingPhotoPublicUrl($r->building_photo_path) : null;
  $currentBuildingPhotoName = $r?->building_photo_original_name ?: ($r?->building_photo_path ? basename((string) $r->building_photo_path) : '');
  if ($currentAttachmentUrl && ! str_contains($currentAttachmentUrl, '/public/')) {
      $currentAttachmentUrl = str_replace('/rental_agreement_attachments/', '/public/rental_agreement_attachments/', $currentAttachmentUrl);
  }
  if ($currentBuildingPhotoUrl && ! str_contains($currentBuildingPhotoUrl, '/public/')) {
      $currentBuildingPhotoUrl = str_replace('/rental_agreement_attachments/', '/public/rental_agreement_attachments/', $currentBuildingPhotoUrl);
  }
  $raAttFileMeta = function (string $name): array {
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

      return match ($ext) {
          'pdf' => ['badge' => 'PDF', 'class' => 'ra-attach-type-pdf', 'kind' => 'pdf'],
          'doc', 'docx' => ['badge' => 'DOC', 'class' => 'ra-attach-type-doc', 'kind' => 'doc'],
          'png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp' => ['badge' => strtoupper($ext === 'jpeg' ? 'JPG' : $ext), 'class' => 'ra-attach-type-img', 'kind' => 'image'],
          default => ['badge' => $ext !== '' ? strtoupper($ext) : 'FILE', 'class' => 'ra-attach-type-file', 'kind' => 'other'],
      };
  };
  $selectedOwnerName = trim((string) $o('owner_name'));
  $selectedVendorId = old('vendor_id', $r?->vendor_id ?? '');
  $rcmApplicable = (string) $o('rcm_applicable', $r && $r->rcm_applicable ? '1' : '0');
  if (! in_array($rcmApplicable, ['0', '1'], true)) {
      $rcmApplicable = $r && $r->rcm_applicable ? '1' : '0';
  }
  if ($selectedVendorId === '' && $selectedOwnerName !== '') {
      foreach ($vendors ?? [] as $v) {
          $label = trim((string) ($v->display_name ?? '')) !== '' ? (string) $v->display_name : (string) ($v->company_name ?? '');
          if ($label !== '' && strcasecmp($label, $selectedOwnerName) === 0) {
              $selectedVendorId = (string) $v->id;
              break;
          }
      }
  }
  $selectedVendorDisplay = $selectedOwnerName;
  if ($selectedVendorId !== '') {
      $matchedVendor = ($vendors ?? collect())->firstWhere('id', (int) $selectedVendorId);
      if ($matchedVendor) {
          $selectedVendorDisplay = trim((string) ($matchedVendor->display_name ?? '')) !== ''
              ? (string) $matchedVendor->display_name
              : (string) ($matchedVendor->company_name ?? '');
      }
  }
  $raIndexBackQs = [];
  if (request()->filled('category')) {
      $raIndexBackQs['category'] = request('category');
  }
@endphp
<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/payment_request.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="{{ asset('/assets/css/rental_agreement.css') }}?v={{ @filemtime(public_path('assets/css/rental_agreement.css')) }}">

<body class="ra-page" style="overflow-x: hidden;">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')
<div id="rentalFlashData"
  data-success="{{ e((string) session('success', '')) }}"
  data-error="{{ e((string) session('error', '')) }}"
  data-validation-errors="{{ e(json_encode($errors->all())) }}"
  hidden></div>
<script id="rentalBranchesData" type="application/json">@json($branches->map(fn ($branch) => ['id' => (int) $branch->id, 'name' => $branch->name, 'zone_id' => (int) $branch->zone_id])->values())</script>

<div class="pc-container">
  <div class="pc-content">
    <div class="ra-shell pr-pay-module pr-form-page pr-pay-form-page pay-create-surface">
      <div class="ra-card ra-card--form">
        <header class="ra-hero">
          <div>
            <div class="ra-hero-kicker">
              <i class="bi bi-file-earmark-text"></i>
              {{ $moduleTitle }}
            </div>
            <h1 class="ra-hero-title">
              <i class="bi bi-house-check"></i>
              @if ($isEdit)
                Edit {{ $moduleTitleLower }}
              @else
                New rental agreement
              @endif
            </h1>
          </div>
          <div class="ra-hero-actions">
            <a href="{{ $isEdit ? route($routeNames['show'], ['rentalAgreement' => $r]) : route($routeNames['index'], $raIndexBackQs) }}" class="ra-btn-ghost">
              <i class="bi bi-arrow-left"></i>
              {{ $isEdit ? 'Back to agreement' : 'Back to list' }}
            </a>
          </div>
        </header>

        <div class="ra-body">
          <style>
            .ra-card.ra-card--form,
            .ra-card.ra-card--form .ra-body,
            .ra-card.ra-card--form .ra-section,
            .ra-card.ra-card--form form {
              overflow: visible !important;
            }
            .ra-loc-strip .pr-dd-wrap .pr-dd-panel {
              width: 100%;
              min-width: 100%;
            }
            .ra-loc-strip .pr-dd-wrap .company-list,
            .ra-loc-strip .pr-dd-wrap .zone-list,
            .ra-loc-strip .pr-dd-wrap .branch-list,
            .ra-loc-strip .pr-dd-wrap .vendor-list {
              max-height: 220px;
              overflow-y: auto;
            }
          </style>

          <form method="post" action="{{ $isEdit ? route($routeNames['update'], ['rentalAgreement' => $r]) : route($routeNames['store']) }}" enctype="multipart/form-data" class="pr-form-premium js-ra-validate" id="ra_agreement_form" novalidate>
            @csrf
            @if ($isEdit)
              @method('PUT')
            @endif

            <div class="ra-section pr-pay-sec pr-pay-sec--location">
              <div class="ra-section-title">
                <i class="bi bi-journal-text"></i>
                Agreement details
              </div>
              <div class="row g-3 ra-loc-strip" id="payLocationStrip">
                <div class="col-lg-4 col-md-6" data-field="company_id">
                  <div class="tax-dropdown-wrapper company-section pr-dd-wrap">
                    <label class="form-label mb-0" for="rental_dd_company">Company <span class="text-danger">*</span></label>
                    <input id="rental_dd_company" type="text"
                      class="form-control company-search-input pr-dd-input pr-loc-dd-input @error('company_id') is-invalid @enderror"
                      readonly autocomplete="off" placeholder="Select company"
                      value="{{ e($selectedCompanyName) }}">
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
                      value="{{ e($selectedZoneName) }}">
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
                      value="{{ e($selectedBranchName) }}">
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
                  <div class="col-lg-4 col-md-6">
                    <label class="ra-field-label" for="ra_agreement_type">Category <span class="text-danger">*</span></label>
                    <select id="ra_agreement_type" name="agreement_type" class="ra-form-control @error('agreement_type') is-invalid @enderror" required>
                      @php
                        $raCatSel = old('agreement_type', $defaultAgreementType ?? \App\Models\RentalAgreement::TYPE_HOSPITAL);
                      @endphp
                      <option value="{{ \App\Models\RentalAgreement::TYPE_HOSPITAL }}" @selected($raCatSel === \App\Models\RentalAgreement::TYPE_HOSPITAL)>Hospital</option>
                      <option value="{{ \App\Models\RentalAgreement::TYPE_HOSTEL }}" @selected($raCatSel === \App\Models\RentalAgreement::TYPE_HOSTEL)>Hostel</option>
                    </select>
                    @error('agreement_type')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                @endif
                <div class="col-lg-4 col-md-6" data-field="agreement_date">
                  <label class="ra-field-label">Rental Agreement Date <span class="text-danger">*</span></label>
                  <input type="date" name="agreement_date" class="ra-form-control @error('agreement_date') is-invalid @enderror" value="{{ $agreementDate }}">
                  @error('agreement_date')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-lg-4 col-md-6" data-field="owner_name">
                  <div class="tax-dropdown-wrapper vendor-section pr-dd-wrap">
                    <label class="form-label mb-0" for="rental_dd_landlord">Landlord name <span class="text-danger">*</span></label>
                    <input id="rental_dd_landlord" type="text"
                      class="form-control vendor-search-input pr-dd-input pr-loc-dd-input @error('owner_name') is-invalid @enderror"
                      readonly autocomplete="off" placeholder="Select landlord"
                      value="{{ e($selectedVendorDisplay) }}">
                    <input type="hidden" name="vendor_id" class="vendor_id" value="{{ $selectedVendorId }}">
                    <input type="hidden" name="owner_name" id="rental_owner_name" class="owner_name" value="{{ e($selectedOwnerName) }}" maxlength="255">
                    <div class="tax-dropdown pr-dd-panel">
                      <div class="inner-search-container">
                        <input type="text" class="inner-search form-control form-control-sm" placeholder="Search landlord..." autocomplete="off">
                      </div>
                      <div class="vendor-list">
                        @foreach (($vendors ?? collect()) as $v)
                          @php
                            $vendorLabel = trim((string) ($v->display_name ?? '')) !== '' ? (string) $v->display_name : (string) ($v->company_name ?? '');
                          @endphp
                          @if ($vendorLabel !== '')
                            <div data-value="{{ e($vendorLabel) }}" data-id="{{ $v->id }}" data-pan="{{ e((string) ($v->pan_number ?? '')) }}">{{ $vendorLabel }}</div>
                          @endif
                        @endforeach
                      </div>
                    </div>
                  </div>
                  @error('owner_name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                  @error('vendor_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="col-lg-4 col-md-6" data-field="agreement_period">
                  <label class="ra-field-label">Agreement Period <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="agreementPeriodPicker"
                    name="agreement_period"
                    class="ra-form-control @error('agreement_period') is-invalid @enderror @error('agreement_period_start') is-invalid @enderror @error('agreement_period_end') is-invalid @enderror"
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
                  <label class="ra-field-label">End of Agreement Date <span class="text-danger">*</span></label>
                  <input type="date" id="endAgreementDate" name="end_of_agreement_date" class="ra-form-control @error('end_of_agreement_date') is-invalid @enderror" value="{{ $endDate }}">
                  @error('end_of_agreement_date')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-lg-4 col-md-6">
                  <label class="ra-field-label">Termination Period</label>
                  <input type="text" name="termination_period" class="ra-form-control @error('termination_period') is-invalid @enderror" maxlength="120" value="{{ $o('termination_period') }}" placeholder="e.g. 3 months notice">
                </div>
                <div class="col-lg-4 col-md-6">
                  <label class="ra-field-label">Date of Rent Payment</label>
                  <input type="text" name="date_of_rent_payment" class="ra-form-control @error('date_of_rent_payment') is-invalid @enderror" maxlength="120" value="{{ $o('date_of_rent_payment') }}" placeholder="e.g. 5th of every month">
                </div>
                <div class="row g-3">
                <!-- <div class="col-6">
                  <label class="ra-field-label">Other parties (additional names)</label>
                  <textarea name="additional_party_names" class="ra-textarea @error('additional_party_names') is-invalid @enderror" rows="3" placeholder="One name per line (e.g. joint owners, co-lessors)">{{ $o('additional_party_names') }}</textarea>
                  @error('additional_party_names')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div> -->
                <div class="col-12" data-field="address">
                  <label class="ra-field-label">Address <span class="text-danger">*</span></label>
                  <textarea name="address" class="ra-textarea @error('address') is-invalid @enderror" placeholder="Enter complete address">{{ $o('address') }}</textarea>
                  @error('address')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                </div>
              </div>
            </div>
            @php
            $rcmApplicable = old('rcm_applicable', $o('rcm_applicable', '0'));
            if ($rcmApplicable === true || $rcmApplicable === 1) {
            $rcmApplicable = '1';
            } elseif ($rcmApplicable === false || $rcmApplicable === 0 || $rcmApplicable === null || $rcmApplicable === '') {
            $rcmApplicable = '0';
            } else {
            $rcmApplicable = (string) $rcmApplicable;
            }

            $rentRevisionOptions = [
            'Every 11 Months',
            'Every 1 Year',
            'Every 2 Years',
            'Every 3 Years',
            'Every 5 Years',
            ];
            $currentRentRevision = trim((string) $o('rent_revision'));

            $raGstIncluding = \App\Models\RentalAgreement::GST_INCLUDING;
            $raGstExcluding = \App\Models\RentalAgreement::GST_EXCLUDING;
            $raGstTypeVal = (string) $o('gst_type');
            $raGstApplicable = old('gst_applicable');
            if ($raGstApplicable === null || $raGstApplicable === '') {
                $raGstApplicable = \App\Models\RentalAgreement::isGstApplicableType($raGstTypeVal) ? '1' : '0';
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
            $raTdsTaxName = $o('tds_tax_name');
            $raTdsTaxId = $o('tds_tax_id');
            $raTdsRate = $o('tds_rate');
            $raTdsSectionId = $o('tds_section_id');
            $raTdsSection = trim((string) $o('tds_section'));
            $raTdsAmt = $o('tds_amount');
            $raTdsSearchDisplay = (string) $raTdsTaxName;
            if ($raTdsSearchDisplay !== '' && $raTdsRate !== '' && $raTdsRate !== null) {
            $pct = (float) $raTdsRate;
            $displayPct = $pct <= 1 && $pct > 0 ? $pct * 100 : $pct;
            $pctFmt = rtrim(rtrim(number_format($displayPct, 2), '0'), '.');
            $raTdsSearchDisplay = $pctFmt . '% TDS';
            }
            $raTdsSectionDisplay = $raTdsSection;
            if ($raTdsSectionDisplay !== '' && $raTdsTaxName !== '' && $raTdsTaxName !== null && stripos($raTdsSectionDisplay, (string) $raTdsTaxName) === false) {
            $raTdsSectionDisplay = $raTdsSectionDisplay . ' - ' . $raTdsTaxName;
            }
            $raTdsDisplayFormatted = $raTdsAmt !== '' && $raTdsAmt !== null
            ? number_format((float) $raTdsAmt, 2)
            : '';
            $gstOptions = $gstOptions ?? \App\Models\RentalAgreement::GST_TAX_MODE_LABELS;
            $gstTaxes = $gstTaxes ?? collect();
            $tdsTaxes = $tdsTaxes ?? collect();
            @endphp

            <div class="ra-rent-charges-stack">

            <section class="ra-rent-block">
            <header class="ra-rent-block-head">
            <i class="bi bi-house-door" aria-hidden="true"></i>
            <span>Rent and charges</span>
            </header>
            <div class="ra-rent-block-body ra-rent-grid ra-rent-grid--3">
            <div data-field="advance_amount">
            <label class="ra-field-label" for="ra_advance_amount">Advance amount (refundable advance paid) <span class="text-danger">*</span></label>
            <div class="ra-rent-input-wrap">
            <span class="ra-rent-input-prefix" aria-hidden="true">&#8377;</span>
            <input type="number" step="0.01" min="0" name="advance_amount" id="ra_advance_amount" class="ra-form-control ra-rent-input @error('advance_amount') is-invalid @enderror" value="{{ $o('advance_amount') }}" placeholder="0.00" required>
            </div>
            @error('advance_amount')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            </div>
            <div data-field="monthly_rent_amount">
            <label class="ra-field-label" for="ra_monthly_rent_amount">Monthly Rent Amount <span class="text-danger">*</span></label>
            <div class="ra-rent-input-wrap">
            <span class="ra-rent-input-prefix" aria-hidden="true">&#8377;</span>
            <input type="number" step="0.01" min="0" name="monthly_rent_amount" id="ra_monthly_rent_amount" class="ra-form-control ra-rent-input @error('monthly_rent_amount') is-invalid @enderror" value="{{ $o('monthly_rent_amount') }}" placeholder="0.00">
            </div>
            @error('monthly_rent_amount')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            </div>
            <div>
            <label class="ra-field-label" for="ra_maintenance_amount">Maintenance Amount <span class="text-danger">*</span></label>
            <div class="ra-rent-input-wrap">
            <span class="ra-rent-input-prefix" aria-hidden="true">&#8377;</span>
            <input type="number" step="0.01" min="0" name="maintenance_amount" id="ra_maintenance_amount" class="ra-form-control ra-rent-input @error('maintenance_amount') is-invalid @enderror" value="{{ $o('maintenance_amount') }}" placeholder="0.00" required>
            </div>
            @error('maintenance_amount')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            </div>
            </div>
            </section>

            <section class="ra-rent-block ra-rent-block--tax">
            <header class="ra-rent-block-head">
            <i class="bi bi-receipt" aria-hidden="true"></i>
            <span>GST &amp; TDS</span>
            </header>

            <div class="ra-rent-block-body">
            <div id="ra_gst_card" @class([
              'ra-gst-card',
              'ra-gst-card--expanded' => $raGstApplicable === '1',
              'ra-gst-card--with-breakdown' => $raShowGstCalc,
            ])>
              <div class="ra-gst-card__inputs">
                <div class="ra-gst-inputs-row ra-rent-grid ra-rent-grid--4">
                  <div class="ra-gst-input-field" data-field="gst_applicable">
                    <label class="ra-field-label" for="ra_gst_applicable">GST applicable <span class="text-danger">*</span></label>
                    <select name="gst_applicable" id="ra_gst_applicable" class="ra-form-select @error('gst_applicable') is-invalid @enderror">
                      <option value="0" @selected($raGstApplicable === '0')>No</option>
                      <option value="1" @selected($raGstApplicable === '1')>Yes</option>
                    </select>
                    @error('gst_applicable')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  </div>

                  <div id="ra_gst_detail_wrap" @class([
                    'ra-gst-input-field',
                    'ra-gst-detail-field',
                    'ra-gst-detail-wrap--hidden' => $raGstApplicable !== '1',
                  ]) data-field="gst_type">
                    <label class="ra-field-label" for="ra_gst_type">Tax mode <span class="text-danger ra-gst-tax-mode-req">*</span></label>
                    <select name="gst_type" id="ra_gst_type" class="ra-form-select @error('gst_type') is-invalid @enderror" @disabled($raGstApplicable !== '1')>
                      <option value="">Select tax mode</option>
                      @foreach ($gstOptions ?? \App\Models\RentalAgreement::GST_TAX_MODE_LABELS as $gstKey => $gstLabel)
                        <option value="{{ $gstKey }}" @selected($raGstTypeVal === (string) $gstKey)>{{ $gstLabel }}</option>
                      @endforeach
                    </select>
                    @error('gst_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  </div>

                  <div @class([
                    'ra-gst-input-field',
                    'ra-gst-fields-wrap',
                    'ra-gst-detail-field',
                    'ra-gst-detail-wrap--hidden' => $raGstApplicable !== '1',
                    'ra-gst-fields--hidden' => ! $raShowGstCalc,
                  ]) id="ra_gst_fields_wrap" data-field="gst_tax_id">
                    <label class="ra-field-label" for="ra_gst_search_input">GST <span class="text-danger ra-gst-req">*</span></label>
                    <div class="tax-dropdown-wrapper gst-section ra-gst-dropdown w-100">
                      <input type="text"
                        class="form-control gst-search-input ra-form-control @error('gst_tax_name') is-invalid @enderror"
                        id="ra_gst_search_input"
                        value="{{ $raGstSearchDisplay }}"
                        placeholder="Select GST"
                        readonly
                        autocomplete="off">
                      <input type="hidden" name="gst_tax_name" id="ra_gst_tax_name" value="{{ $raGstTaxName }}">
                      <input type="hidden" name="gst_percentage" class="selected-gst-tax" id="ra_gst_percentage" value="{{ $raGstPct }}">
                      <input type="hidden" name="gst_tax_type" class="gst_tax_type" id="ra_gst_tax_type" value="{{ $raGstTaxType }}">
                      <input type="hidden" name="gst_tax_id" class="gst-tax-id" id="ra_gst_tax_id" value="{{ $raGstTaxId }}">
                      <div class="dropdown-menu tax-dropdown ra-gst-tax-dropdown-menu">
                        <div class="tax-gst-list" id="ra_tax_gst_list">
                          @forelse ($gstTaxes ?? [] as $tax)
                            <div data-type="{{ $tax->tax_type }}" data-value="{{ $tax->tax_rate }}" data-id="{{ $tax->id }}" data-name="{{ $tax->tax_name }}">
                              {{ $tax->tax_name }} [{{ $tax->tax_rate }}%]
                            </div>
                          @empty
                            <div class="ra-tax-list-empty text-muted small px-2 py-1">No GST taxes in gst_tax_tbl</div>
                          @endforelse
                        </div>
                      </div>
                    </div>
                    @error('gst_tax_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('gst_percentage')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>

              <div class="ra-gst-card__breakdown ra-gst-fields-wrap {{ $raShowGstCalc ? '' : 'ra-gst-fields--hidden' }}" id="ra_gst_breakdown_panel" aria-live="polite">
                <h3 class="ra-gst-breakdown-heading">GST breakdown</h3>
                <div class="ra-gst-breakdown-grid">
                  <div class="ra-gst-breakdown-col">
                    <span class="ra-gst-breakdown-col-title">Rent amount</span>
                    <div class="ra-gst-breakdown-box ra-gst_calculate_show" id="ra_gst_breakdown_rent"></div>
                  </div>
                  <div class="ra-gst-breakdown-col">
                    <span class="ra-gst-breakdown-col-title">Maintenance amount</span>
                    <div class="ra-gst-breakdown-box ra-gst_calculate_show" id="ra_gst_breakdown_maintenance"></div>
                  </div>
                  <div class="ra-gst-breakdown-col ra-gst-breakdown-col--total">
                    <span class="ra-gst-breakdown-col-title">Combined total</span>
                    <div class="ra-gst-breakdown-box ra-gst_calculate_show gst_calculate_show" id="ra_gst_breakdown_total"></div>
                  </div>
                </div>
                <input type="hidden" name="gst_amount" id="ra_gst_amount" value="{{ $raGstAmt }}">
                <input type="hidden" name="cgst_amount" id="ra_cgst_amount" value="{{ $raCgst }}">
                <input type="hidden" name="sgst_amount" id="ra_sgst_amount" value="{{ $raSgst }}">
                <input type="hidden" name="igst_amount" id="ra_igst_amount" value="{{ $raIgst }}">
                @error('gst_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>{{-- /ra_gst_card --}}

            <div class="ra-rent-tax-row ra-rent-tax-row--tds" id="ra_tds_fields_wrap">
            <div class="ra-rent-tax-col" data-field="tds_tax_id">
            <label class="ra-field-label">TDS <span class="text-danger">*</span></label>
            <div class="tax-dropdown-wrapper tds-tax-section ra-tds-dropdown w-100">
            <input type="text"
            class="form-control tax-search-input ra-form-control @error('tds_tax_name') is-invalid @enderror"
            id="ra_tds_search_input"
            value="{{ $raTdsSearchDisplay }}"
            placeholder="Select TDS"
            readonly
            autocomplete="off">
            <input type="hidden" name="tds_tax_name" id="ra_tds_tax_name" value="{{ $raTdsTaxName }}">
            <input type="hidden" name="tds_rate" class="selected-tds-tax" id="ra_tds_rate" value="{{ $raTdsRate }}">
            <input type="hidden" name="tds_tax_id" class="tds-tax-id" id="ra_tds_tax_id" value="{{ $raTdsTaxId }}">
            <input type="hidden" name="tds_section_id" class="tds_section_id" id="ra_tds_section_id" value="{{ $raTdsSectionId }}">
            <input type="hidden" name="tds_section" id="ra_tds_section" value="{{ $raTdsSection }}">
            <div class="dropdown-menu tax-dropdown ra-tds-tax-dropdown-menu">
            <div class="tax-list" id="ra_tax_tds_list">
            @forelse ($tdsTaxes ?? [] as $tax)
            @php
            $sectionName = trim((string) ($tax->section_name ?? $tax->section?->name ?? ''));
            @endphp
            <div
            data-value="{{ $tax->tax_rate }}"
            data-id="{{ $tax->id }}"
            data-name="{{ $tax->tax_name }}"
            data-section-id="{{ $tax->section_id }}"
            data-section-name="{{ $sectionName }}">
            {{ $tax->tax_name }} [{{ $tax->tax_rate }}%]@if ($sectionName !== '') &mdash; {{ $sectionName }}@endif
            </div>
            @empty
            <div class="ra-tax-list-empty text-muted small px-2 py-1">No TDS taxes in tds_tax_tbl</div>
            @endforelse
            </div>
            </div>
            </div>
            @error('tds_tax_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            @error('tds_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="ra-rent-tax-col">
            <label class="ra-field-label" for="ra_tds_section_display">TDS section</label>
            <input type="text" class="ra-form-control" id="ra_tds_section_display" value="{{ $raTdsSectionDisplay }}" placeholder="From selected TDS" readonly>
            </div>
            <div class="ra-rent-tax-col">
            <label class="ra-field-label" for="ra_tds_display">TDS on rent</label>
            <div class="ra-rent-input-wrap">
            <span class="ra-rent-input-prefix" aria-hidden="true">&#8377;</span>
            <input type="text" class="ra-form-control ra-rent-input" id="ra_tds_display" value="{{ $raTdsDisplayFormatted }}" placeholder="0.00" readonly tabindex="-1">
            </div>
            <input type="hidden" name="tds_amount" id="ra_tds_amount" value="{{ $raTdsAmt }}">
            @error('tds_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <p class="ra-rent-field-hint mb-0">Calculated on monthly rent amount.</p>
            </div>
            </div>
            </div>
            </section>

            <section class="ra-rent-block">
            <header class="ra-rent-block-head">
            <i class="bi bi-building" aria-hidden="true"></i>
            <span>Property details</span>
            </header>
            <div class="ra-rent-block-body ra-rent-grid ra-rent-grid--4">
            <div>
            <label class="ra-field-label" for="ra_eb_number">EB Number</label>
            <input type="text" name="eb_number" id="ra_eb_number" class="ra-form-control @error('eb_number') is-invalid @enderror" maxlength="120" value="{{ $o('eb_number') }}" placeholder="Enter EB number">
            </div>
            <div>
            <label class="ra-field-label" for="ra_sq_ft_area">Sq Ft Area</label>
            <input type="number" step="0.01" min="0" name="sq_ft_area" id="ra_sq_ft_area" class="ra-form-control @error('sq_ft_area') is-invalid @enderror" value="{{ $o('sq_ft_area') }}" placeholder="0.00">
            </div>
            <div>
            <label class="ra-field-label" for="ra_rent_revision">Rent Revision</label>
            <select name="rent_revision" id="ra_rent_revision" class="ra-form-select @error('rent_revision') is-invalid @enderror">
            <option value="">Select period</option>
            @foreach ($rentRevisionOptions as $opt)
            <option value="{{ $opt }}" @selected($currentRentRevision === $opt)>{{ $opt }}</option>
            @endforeach
            @if ($currentRentRevision !== '' && ! in_array($currentRentRevision, $rentRevisionOptions, true))
            <option value="{{ $currentRentRevision }}" selected>{{ $currentRentRevision }}</option>
            @endif
            </select>
            </div>
            <div>
            <label class="ra-field-label" for="ra_rent_hike_percentage">Rent Hike Percentage</label>
            <div class="ra-rent-input-wrap ra-rent-input-wrap--suffix">
            <input type="number" step="0.01" min="0" max="100" name="rent_hike_percentage" id="ra_rent_hike_percentage" class="ra-form-control ra-rent-input @error('rent_hike_percentage') is-invalid @enderror" value="{{ $o('rent_hike_percentage') }}" placeholder="0.00">
            <span class="ra-rent-input-suffix" aria-hidden="true">%</span>
            </div>
            </div>
            </div>
            </section>

            <section class="ra-rent-block">
            <header class="ra-rent-block-head">
            <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
            <span>RCM details</span>
            </header>
            <div class="ra-rent-block-body ra-rent-grid ra-rent-grid--rcm">
            <div data-field="rcm_applicable">
            <label class="ra-field-label" for="ra_rcm_applicable">RCM applicable <span class="text-danger">*</span></label>
            <select name="rcm_applicable" id="ra_rcm_applicable" class="ra-form-select @error('rcm_applicable') is-invalid @enderror">
            <option value="0" @selected($rcmApplicable === '0')>No</option>
            <option value="1" @selected($rcmApplicable === '1')>Yes</option>
            </select>
            @error('rcm_applicable')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div id="ra_rcm_value_wrap" data-field="rcm_value" @class(['ra-rcm-value--hidden' => $rcmApplicable !== '1'])>
            <label class="ra-field-label" for="ra_rcm_value">RCM value <span class="text-danger ra-rcm-value-req">*</span></label>
            <div class="ra-rent-input-wrap">
            <span class="ra-rent-input-prefix" aria-hidden="true">&#8377;</span>
            <input type="number" step="0.01" min="0" name="rcm_value" id="ra_rcm_value" class="ra-form-control ra-rent-input @error('rcm_value') is-invalid @enderror" value="{{ $o('rcm_value') }}" placeholder="0.00" @disabled($rcmApplicable !== '1')>
            </div>
            @error('rcm_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <p class="ra-rent-field-hint mb-0">Enter RCM amount if applicable.</p>
            </div>
            </div>
            </section>

            </div>

            <div class="ra-section">
              <div class="ra-section-title">
                <i class="bi bi-person-vcard"></i>
                Contact and attachment
              </div>
              <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                  <label class="ra-field-label">PAN Number</label>
                  <input type="text" name="pan_number" class="ra-form-control @error('pan_number') is-invalid @enderror" maxlength="30" value="{{ $o('pan_number') }}" placeholder="Enter PAN number">
                </div>
                <div class="col-lg-4 col-md-6">
                  <label class="ra-field-label">Contact Person Name</label>
                  <input type="text" name="contact_person_name" class="ra-form-control @error('contact_person_name') is-invalid @enderror" maxlength="255" value="{{ $o('contact_person_name') }}" placeholder="Enter contact person name">
                </div>
                <div class="col-lg-4 col-md-6">
                  <label class="ra-field-label">Contact Person Number</label>
                  <input type="text" name="contact_person_number" class="ra-form-control @error('contact_person_number') is-invalid @enderror" maxlength="30" value="{{ $o('contact_person_number') }}" placeholder="Enter contact number">
                </div>
                <div class="row g-3">
                <div class="col-6">
                  <label class="ra-field-label">Building photo @if (! $isEdit || ! $currentBuildingPhotoUrl)<span class="text-danger">*</span>@endif</label>
                  <div class="pr-pay-attachment-zone">
                    <div class="pr-pay-upload-box @error('building_photo') border border-danger @enderror" id="rental-building-photo-upload-box">
                      <div class="pr-pay-upload-icon"><i class="bi bi-image"></i></div>
                      <div class="pr-pay-upload-text">Drag &amp; drop or <span>browse image</span></div>
                      <p class="pr-pay-upload-hint">JPG, PNG or WebP up to 5 MB</p>
                      <input type="file" name="building_photo" id="rental_building_photo_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @if (! $isEdit || ! $currentBuildingPhotoUrl) required @endif>
                    </div>
                  </div>
                  <div class="ra-attach-section mt-3" id="rental-building-photo-attach-area" data-ra-attach-section>
                    @php $buildingHasExisting = filled($currentBuildingPhotoUrl); @endphp
                    <p class="ra-attach-section-head mb-2 {{ $buildingHasExisting ? '' : 'd-none' }}" data-ra-attach-count>
                      <span class="fw-semibold text-dark">{{ $buildingHasExisting ? '1 attachment' : '0 attachments' }}</span>
                      <span class="text-muted"> · building photo</span>
                    </p>
                    <div class="ra-attach-grid" id="rental-building-photo-gallery" role="list" aria-live="polite">
                      @if ($buildingHasExisting)
                        @php $buildingMeta = $raAttFileMeta($currentBuildingPhotoName); @endphp
                        <div class="ra-attach-card" role="listitem" data-ra-existing-attach-card data-file-name="{{ $currentBuildingPhotoName }}">
                          <button type="button" class="ra-attach-thumb" data-ra-preview-url="{{ $currentBuildingPhotoUrl }}" title="Preview {{ $currentBuildingPhotoName }}">
                            <span class="ra-attach-thumb-inner ra-attach-thumb--{{ $buildingMeta['kind'] }}">
                              @if ($buildingMeta['kind'] === 'image')
                                <img src="{{ $currentBuildingPhotoUrl }}" alt="" loading="lazy" class="ra-attach-thumb-media">
                              @elseif ($buildingMeta['kind'] === 'pdf')
                                <iframe src="{{ $currentBuildingPhotoUrl }}#toolbar=0&navpanes=0&scrollbar=0" title="" class="ra-attach-thumb-media" loading="lazy"></iframe>
                              @else
                                <span class="ra-attach-thumb-fallback" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span>
                              @endif
                            </span>
                          </button>
                          <div class="ra-attach-foot">
                            <span class="ra-attach-type-badge {{ $buildingMeta['class'] }}">{{ $buildingMeta['badge'] }}</span>
                            <span class="ra-attach-name" title="{{ $currentBuildingPhotoName }}">{{ $currentBuildingPhotoName }}</span>
                          </div>
                          <span class="ra-attach-fold" aria-hidden="true"></span>
                        </div>
                      @endif
                    </div>
                  </div>
                  @error('building_photo')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-6">
                  <label class="ra-field-label">Rental Agreement Document @if (! $isEdit || ! $currentAttachmentUrl)<span class="text-danger">*</span>@endif</label>
                  <div class="pr-pay-attachment-zone">
                    <div class="pr-pay-upload-box @error('attachment') border border-danger @enderror" id="rental-attachment-upload-box">
                      <div class="pr-pay-upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                      <div class="pr-pay-upload-text">Drag &amp; drop or <span>browse files</span></div>
                      <p class="pr-pay-upload-hint">Support for PDF, images, Word, and Excel files up to 10 MB</p>
                      <input type="file" name="attachment" id="rental_attachment_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,application/pdf,image/*" @if (! $isEdit || ! $currentAttachmentUrl) required @endif>
                    </div>
                  </div>
                  <div class="ra-attach-section mt-3" id="rental-attachment-attach-area" data-ra-attach-section>
                    @php $attachmentHasExisting = filled($currentAttachmentUrl); @endphp
                    <p class="ra-attach-section-head mb-2 {{ $attachmentHasExisting ? '' : 'd-none' }}" data-ra-attach-count>
                      <span class="fw-semibold text-dark">{{ $attachmentHasExisting ? '1 attachment' : '0 attachments' }}</span>
                      <span class="text-muted"> · agreement document</span>
                    </p>
                    <div class="ra-attach-grid" id="rental-attachment-gallery" role="list" aria-live="polite">
                      @if ($attachmentHasExisting)
                        @php $attachmentMeta = $raAttFileMeta($currentAttachmentName); @endphp
                        <div class="ra-attach-card" role="listitem" data-ra-existing-attach-card data-file-name="{{ $currentAttachmentName }}">
                          <button type="button" class="ra-attach-thumb" data-ra-preview-url="{{ $currentAttachmentUrl }}" title="Preview {{ $currentAttachmentName }}">
                            <span class="ra-attach-thumb-inner ra-attach-thumb--{{ $attachmentMeta['kind'] }}">
                              @if ($attachmentMeta['kind'] === 'image')
                                <img src="{{ $currentAttachmentUrl }}" alt="" loading="lazy" class="ra-attach-thumb-media">
                              @elseif ($attachmentMeta['kind'] === 'pdf')
                                <iframe src="{{ $currentAttachmentUrl }}#toolbar=0&navpanes=0&scrollbar=0" title="" class="ra-attach-thumb-media" loading="lazy"></iframe>
                              @else
                                <span class="ra-attach-thumb-fallback" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span>
                              @endif
                            </span>
                          </button>
                          <div class="ra-attach-foot">
                            <span class="ra-attach-type-badge {{ $attachmentMeta['class'] }}">{{ $attachmentMeta['badge'] }}</span>
                            <span class="ra-attach-name" title="{{ $currentAttachmentName }}">{{ $currentAttachmentName }}</span>
                          </div>
                          <span class="ra-attach-fold" aria-hidden="true"></span>
                        </div>
                      @endif
                    </div>
                  </div>
                  <div class="ra-help">Accepted formats: PDF, image, Word, and Excel files up to 10 MB.</div>
                  @error('attachment')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                </div>
              </div>
            </div>

            <div class="ra-footer-bar">
              <div class="ra-footer-meta">
                {{ $isEdit ? 'Update the '.$moduleTitleLower.' details and save your changes.' : 'A reference number will be generated automatically when the '.$moduleTitleLower.' is saved.' }}
              </div>
              <a href="{{ $isEdit ? route($routeNames['show'], ['rentalAgreement' => $r]) : route($routeNames['index'], $raIndexBackQs) }}" class="ra-btn-outline">
                Cancel
              </a>
              <button type="submit" class="ra-btn-primary border-0">
                <i class="bi bi-check2-circle"></i>
                {{ $isEdit ? 'Update agreement' : 'Save agreement' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="raUploadPreviewModal" tabindex="-1" aria-labelledby="raPreviewModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable pay-upload-preview-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="raPreviewModalTitle">Document preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 bg-body-secondary text-center pay-upload-preview-body position-relative" style="min-height: 200px;">
        <iframe class="d-none w-100 pay-preview-frame" id="raPreviewIframe" title="Document preview"></iframe>
        <img class="d-none img-fluid p-2 pay-preview-img" id="raPreviewImg" alt="" style="max-height: 75vh; object-fit: contain;" />
        <div class="d-none p-4 pay-preview-fallback" id="raPreviewFallback">
          <i class="bi bi-file-earmark-zip display-4 text-secondary d-block mb-2" aria-hidden="true"></i>
          <p class="mb-1 fw-semibold">In-browser preview is not available for this file type</p>
          <p class="text-muted small mb-0" id="raPreviewFallbackName"></p>
        </div>
      </div>
    </div>
  </div>
</div>

@include('superadmin.superadminfooter')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('assets/js/form_field_validation.js') }}?v={{ @filemtime(public_path('assets/js/form_field_validation.js')) }}"></script>
<script src="{{ asset('assets/js/rental_agreement.js') }}?v={{ @filemtime(public_path('assets/js/rental_agreement.js')) }}"></script>
</body>
</html>