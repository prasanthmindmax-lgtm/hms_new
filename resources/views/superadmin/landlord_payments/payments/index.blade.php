@extends('superadmin.landlord_payments.layout')

@push('body-class')
ra-page llp-payments-page
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/payment_request.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/rental_agreement.css') }}?v={{ @filemtime(public_path('assets/css/rental_agreement.css')) }}" />
@endpush

@section('content')
@php
  $activeTab = $activeTab ?? 'all';
  $dataSource = $dataSource ?? 'bill_module';

  $overviewStats = $overviewStats ?? [];
  $chartReportScope = $chartReportScope ?? false;
  $enableTaxKpiFilters = $enableTaxKpiFilters ?? ! $chartReportScope;
  $activeTaxFilter = $activeTaxFilter ?? null;
  $tabQuery = request()->except(['page', 'tab', 'segment', 'charge', 'tax_filter']);
  $tabOrder = ['all' => 'All'];
  foreach ($paymentsTabLabels ?? [] as $tabKey => $tabLabel) {
      if ($tabKey !== 'all') {
          $tabOrder[$tabKey] = $tabLabel;
      }
  }
  $tabLabel = $chartReportScope
      ? ($chartScopeLabel ?? 'All charge types · matching filters')
      : (($paymentsTabLabels ?? [])[$activeTab] ?? 'Summary');
  if ($activeTaxFilter === 'gst') {
      $tabLabel .= ' · with GST';
  } elseif ($activeTaxFilter === 'tds') {
      $tabLabel .= ' · with TDS';
  }
  $reportFilterBaseUrl = $paymentsBaseUrl ?? $reportFilterBaseUrl ?? route('rental-payments', ['segment' => $agreementType]);
  $clearReportFiltersUrl = $clearReportFiltersUrl ?? $reportFilterBaseUrl.'?'.http_build_query(['segment' => $agreementType, 'tab' => 'all']);
@endphp

<div class="llp-payments-workspace tk-pr-index ra-register--llp-match">
  <div class="llp-payments-card">
    <header class="llp-payments-card__head">
      <div class="llp-payments-card__head-top">
        <div class="llp-payments-card__intro">
          <h1 class="llp-payments-card__title">Rental payments</h1>
        </div>
        @include('superadmin.landlord_payments.payments.report_actions', ['showChart' => true, 'showBack' => false])
      </div>
      <nav class="llp-payments-seg" aria-label="Charge type" data-llp-tabs>
        @foreach ($tabOrder as $tabKey => $chargeTabLabel)
          @php
            $tabHref = route('rental-payments', array_merge(
                ['segment' => $agreementType ?? 'hospital', 'tab' => $tabKey],
                $tabQuery
            ));
          @endphp
          <a href="{{ $tabHref }}"
            class="llp-payments-seg__item {{ $activeTab === $tabKey ? 'is-active' : '' }}"
            data-llp-tab="{{ $tabKey }}"
            @if ($activeTab === $tabKey) aria-current="page" @endif>
            {{ $chargeTabLabel }}
          </a>
        @endforeach
      </nav>
    </header>

    @include('superadmin.landlord_payments.payments.stats')

    <div class="llp-payments-filters-slot">
  <div class="tk-filter-shell tk-filter-qd pay-pr-filter-shell">
    <div class="tk-filter-head pay-pr-filter-head">
      <div class="tk-filter-title">
        <i class="bi bi-sliders2" aria-hidden="true"></i> Filters
      </div>
      <div class="pay-pr-filter-head-meta d-flex flex-wrap align-items-center gap-2 justify-content-end">
        <span class="tk-showing-pill mb-0">
          Rows <strong>{{ $rowRangeLabel ?? '0' }}</strong> of <strong>{{ $records->total() ?? 0 }}</strong>
        </span>
      </div>
    </div>

    <form method="get" action="{{ $reportFilterBaseUrl }}" id="llp-payments-filter-form" autocomplete="off">
      <input type="hidden" name="segment" value="{{ $agreementType }}">
      <input type="hidden" name="tab" value="{{ $activeTab }}">
      @if ($activeTaxFilter)
        <input type="hidden" name="tax_filter" id="llp_payments_tax_filter" value="{{ $activeTaxFilter }}">
      @endif
      <input type="hidden" name="date_from" id="llp_payments_date_from" value="{{ $dateFrom ?? request('date_from', request('from')) }}">
      <input type="hidden" name="date_to" id="llp_payments_date_to" value="{{ $dateTo ?? request('date_to', request('to')) }}">

      <div class="pay-pr-array-hiddens" data-array-name="company_id" aria-hidden="true">
        @foreach ($selCompanyIds ?? [] as $cid)<input type="hidden" name="company_id[]" value="{{ $cid }}">@endforeach
      </div>
      <div class="pay-pr-array-hiddens" data-array-name="zone_id" aria-hidden="true">
        @foreach ($selZoneIds ?? [] as $zid)<input type="hidden" name="zone_id[]" value="{{ $zid }}">@endforeach
      </div>
      <div class="pay-pr-array-hiddens" data-array-name="branch_id" aria-hidden="true">
        @foreach ($selBranchIds ?? [] as $bid)<input type="hidden" name="branch_id[]" value="{{ $bid }}">@endforeach
      </div>
      <div class="pay-pr-array-hiddens" data-array-name="state_id" aria-hidden="true">
        @foreach ($selStateIds ?? [] as $stateId)<input type="hidden" name="state_id[]" value="{{ $stateId }}">@endforeach
      </div>
      <div class="pay-pr-array-hiddens" data-array-name="vendor_id" aria-hidden="true">
        @foreach ($selVendorIds ?? [] as $vid)<input type="hidden" name="vendor_id[]" value="{{ $vid }}">@endforeach
      </div>

      <div class="qd-filters pay-pr-qd-filters">
        {{-- Row 1: Date range, Company, State, Zone --}}
        <div class="qd-filter-row pay-pr-qd-filter-row llp-payments-filter-row--primary">
          <div class="qd-filter-group">
            <label><i class="bi bi-calendar3 me-1" aria-hidden="true"></i>Date range</label>
            <div class="qd-date-wrap" id="llpPaymentsDateRange" role="button" tabindex="0">
              <i class="fa fa-calendar" aria-hidden="true"></i>
              <span id="llpPaymentsDateLabel">{{ $dateLabel ?? 'All dates' }}</span>
              <i class="fa fa-caret-down" style="margin-left:auto;" aria-hidden="true"></i>
            </div>
          </div>

          <div class="qd-filter-group tax-dropdown-wrapper company-section pay-pr-dd" data-filter-param="company_id" data-empty-label="All companies">
            <label>Company</label>
            <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select company" value="{{ $companyDisp ?? 'All companies' }}" readonly autocomplete="off">
            <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search company..." autocomplete="off"></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
              </div>
              <div class="dropdown-list multiselect company-list">
                @foreach ($companies ?? [] as $company)
                  <div data-value="{{ $company->company_name }}" data-id="{{ $company->id }}" @class(['selected' => in_array((int) $company->id, $selCompanyIds ?? [], true)])>{{ $company->company_name }}</div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="qd-filter-group tax-dropdown-wrapper state-section pay-pr-dd" data-filter-param="state_id" data-empty-label="All states">
            <label>State</label>
            <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select state" value="{{ $stateDisp ?? 'All states' }}" readonly autocomplete="off">
            <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search state..." autocomplete="off"></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
              </div>
              <div class="dropdown-list multiselect state-list">
                @foreach ($stateOptions ?? [] as $stateKey => $stateLabel)
                  <div data-value="{{ $stateLabel }}" data-id="{{ $stateKey }}" @class(['selected' => in_array((int) $stateKey, $selStateIds ?? [], true)])>{{ $stateLabel }}</div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="qd-filter-group tax-dropdown-wrapper zone-section pay-pr-dd" data-filter-param="zone_id" data-empty-label="All zones">
            <label>Zone</label>
            <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select zone" value="{{ $zoneDisp ?? 'All zones' }}" readonly autocomplete="off">
            <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search zone..." autocomplete="off"></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
              </div>
              <div class="dropdown-list multiselect zone-list">
                @foreach ($zones ?? [] as $zone)
                  <div data-value="{{ $zone->name }}" data-id="{{ $zone->id }}" @class(['selected' => in_array((int) $zone->id, $selZoneIds ?? [], true)])>{{ $zone->name }}</div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        {{-- Row 2: Branch, Vendor name --}}
        <div class="qd-filter-row pay-pr-qd-filter-row pay-pr-qd-filter-row--second llp-payments-filter-row--secondary">
          <div class="qd-filter-group tax-dropdown-wrapper branch-section pay-pr-dd" data-filter-param="branch_id" data-empty-label="All branches">
            <label>Branch</label>
            <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select branch" value="{{ $branchDisp ?? 'All branches' }}" readonly autocomplete="off">
            <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search branch..." autocomplete="off"></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
              </div>
              <div class="dropdown-list multiselect branch-list"></div>
            </div>
          </div>

          <div class="qd-filter-group tax-dropdown-wrapper vendor-section pay-pr-dd" data-filter-param="vendor_id" data-empty-label="All vendors">
            <label><i class="bi bi-person-badge me-1" aria-hidden="true"></i>Vendor name</label>
            <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select vendor" value="{{ $vendorDisp ?? 'All vendors' }}" readonly autocomplete="off">
            <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
              <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search vendor..." autocomplete="off"></div>
              <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
              </div>
              <div class="dropdown-list multiselect vendor-list">
                @foreach ($vendors ?? [] as $vendor)
                  @php $vendorLabel = trim((string) ($vendor->display_name ?? '')) !== '' ? (string) $vendor->display_name : (string) ($vendor->company_name ?? ''); @endphp
                  @if ($vendorLabel !== '')
                    <div data-value="{{ $vendorLabel }}" data-id="{{ $vendor->id }}" @class(['selected' => in_array((int) $vendor->id, $selVendorIds ?? [], true)])>{{ $vendorLabel }}</div>
                  @endif
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      @if ($hasFilterChips ?? false)
        <div class="qd-applied-bar pay-pr-applied-bar">
          <span class="applied-label">Filters:</span>
          <div class="pay-pr-filter-chips d-flex flex-wrap align-items-center" style="gap:6px;flex:1;min-width:0;">
            @if (!empty($dateFrom) && !empty($dateTo))
              <a href="{{ $chipUrl(['date_from', 'date_to', 'from', 'to']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                <i class="bi bi-calendar3" aria-hidden="true"></i><span>{{ $dateLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
              </a>
            @endif
            @if (!empty($companyDisp) && ($companyDisp ?? '') !== 'All companies')
              <a href="{{ $chipUrl(['company_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                <span>{{ $companyDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
              </a>
            @endif
            @if (!empty($stateDisp) && ($stateDisp ?? '') !== 'All states')
              <a href="{{ $chipUrl(['state_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                <span>State: {{ $stateDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
              </a>
            @endif
            @if (!empty($zoneDisp) && ($zoneDisp ?? '') !== 'All zones')
              <a href="{{ $chipUrl(['zone_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                <span>{{ $zoneDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
              </a>
            @endif
            @if (!empty($branchDisp) && ($branchDisp ?? '') !== 'All branches')
              <a href="{{ $chipUrl(['branch_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                <span>{{ $branchDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
              </a>
            @endif
            @if (!empty($vendorDisp) && ($vendorDisp ?? '') !== 'All vendors')
              <a href="{{ $chipUrl(['vendor_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                <span>Vendor: {{ $vendorDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
              </a>
            @endif
            @if (!empty($taxFilterLabel))
              <a href="{{ $chipUrl(['tax_filter']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                <span>{{ $taxFilterLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
              </a>
            @endif
          </div>
          <a href="{{ $clearReportFiltersUrl }}" class="filter-badge filter-clear text-decoration-none ms-auto">Clear all</a>
        </div>
      @endif
    </form>
  </div>
</div>

    @include('superadmin.landlord_payments.payments.panel')

  </div>
</div>
@endsection

@push('scripts')
<script id="llpPaymentsBranchesData" type="application/json">@json(($branches ?? collect())->map(fn ($branch) => ['id' => (int) $branch->id, 'name' => $branch->name, 'zone_id' => (int) $branch->zone_id])->values())</script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('assets/js/rental_agreement.js') }}?v={{ @filemtime(public_path('assets/js/rental_agreement.js')) }}"></script>
@endpush
