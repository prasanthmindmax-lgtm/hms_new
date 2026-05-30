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
    <div class="security-agreement index tk-pr-index">
      <div class="card">
        <header class="hero register-hero" aria-labelledby="index-heading">
          <div class="hero-inner">
            <h1 class="hero-title" id="index-heading">
              <i class="bi bi-journal-bookmark" aria-hidden="true"></i>
              {{ $moduleRegisterTitle ?? $moduleTitle }}
            </h1>
            @php
              $raLeadCat = $indexCategoryQuery ?? 'all';
              $raLeadScope = match ($raLeadCat) {
                  \App\Models\SecurityAgreement::TYPE_HOSPITAL => \App\Models\SecurityAgreement::TYPE_LABELS[\App\Models\SecurityAgreement::TYPE_HOSPITAL],
                  \App\Models\SecurityAgreement::TYPE_HOSTEL => \App\Models\SecurityAgreement::TYPE_LABELS[\App\Models\SecurityAgreement::TYPE_HOSTEL],
                  default => 'All categories (hospital & hostel)',
              };
            @endphp
            <p class="hero-lead mb-0">{{ $raLeadScope }}</p>
          </div>
          <div class="hero-actions flex-wrap">
            <a href="{{ $raCreateUrl }}" class="create-url">
              <i class="bi bi-plus-lg" aria-hidden="true"></i>
              New agreement
            </a>
          </div>
        </header>

        <div class="page-body">
          @fragment('sa-register-stats')
          <div id="sa-register-stats" class="stats sa-stats">
            <div class="stat sa-stat sa-stat--total">
              <span class="stat-icon sa-stat-icon" aria-hidden="true"><i class="bi bi-journal-text"></i></span>
              <div class="sa-stat-body">
                <span class="stat-label sa-stat-label">Total agreements</span>
                <span class="stat-value sa-stat-value">{{ number_format($stats['total']) }}</span>
                <span class="stat-hint sa-stat-hint">Matching current filters</span>
              </div>
            </div>
            <div class="stat sa-stat sa-stat--security">
              <span class="stat-icon sa-stat-icon" aria-hidden="true"><i class="bi bi-cash-stack"></i></span>
              <div class="sa-stat-body">
                <span class="stat-label sa-stat-label">Security salary total</span>
                <span class="stat-value sa-stat-value">₹{{ number_format($stats['security_salary_total'] ?? 0, 2) }}</span>
                <span class="stat-hint sa-stat-hint">Combined security fixed salary</span>
              </div>
            </div>
            <div class="stat sa-stat sa-stat--housekeeping">
              <span class="stat-icon sa-stat-icon" aria-hidden="true"><i class="bi bi-stars"></i></span>
              <div class="sa-stat-body">
                <span class="stat-label sa-stat-label">Housekeeping salary total</span>
                <span class="stat-value sa-stat-value">₹{{ number_format($stats['housekeeping_salary_total'] ?? 0, 2) }}</span>
                <span class="stat-hint sa-stat-hint">Combined housekeeping fixed salary</span>
              </div>
            </div>
            <div class="stat sa-stat sa-stat--expiry">
              <span class="stat-icon sa-stat-icon" aria-hidden="true"><i class="bi bi-calendar2-week"></i></span>
              <div class="sa-stat-body">
                <span class="stat-label sa-stat-label">Ending in 30 days</span>
                <span class="stat-value sa-stat-value">{{ number_format($stats['ending_soon']) }}</span>
                <span class="stat-hint sa-stat-hint">Upcoming expiries</span>
              </div>
            </div>
          </div>
          @endfragment

          <div class="main-content">
          <div class="filter-shell filter-qd pay-pr-filter-shell">
            <div class="filter-head pay-pr-filter-head">
              <div class="filter-title">
                <i class="bi bi-sliders2" aria-hidden="true"></i> Filters
              </div>
              <div class="pay-pr-filter-head-meta d-flex flex-wrap align-items-center gap-2 justify-content-end">
                <span class="showing-count mb-0">
                  Rows <strong>{{ $rowRangeLabel }}</strong> of <strong>{{ $records->total() }}</strong>
                </span>
              </div>
            </div>

            <form method="get" action="{{ $raListingBaseUrl }}" id="filter-form" autocomplete="off">
              <input type="hidden" name="date_from" id="sa_date_from" value="{{ request('date_from') }}">
              <input type="hidden" name="date_to" id="sa_date_to" value="{{ request('date_to') }}">

              <div class="pay-pr-array-hiddens" data-array-name="company_id" aria-hidden="true">
                @foreach ($selCompanyIds as $cid)
                  <input type="hidden" name="company_id[]" value="{{ $cid }}">
                @endforeach
              </div>
              <div class="pay-pr-array-hiddens" data-array-name="state_id" aria-hidden="true">
                @foreach ($selStateIds as $stateId)
                  <input type="hidden" name="state_id[]" value="{{ $stateId }}">
                @endforeach
              </div>
              <div class="pay-pr-array-hiddens" data-array-name="zone_id" aria-hidden="true">
                @foreach ($selZoneIds as $zid)
                  <input type="hidden" name="zone_id[]" value="{{ $zid }}">
                @endforeach
              </div>
              <div class="pay-pr-array-hiddens" data-array-name="branch_id" aria-hidden="true">
                @foreach ($selBranchIds as $bid)
                  <input type="hidden" name="branch_id[]" value="{{ $bid }}">
                @endforeach
              </div>
              <div class="pay-pr-array-hiddens" data-array-name="vendor_type_name" aria-hidden="true">
                @foreach ($selGstTypes as $gstKey)
                  <input type="hidden" name="vendor_type_name[]" value="{{ $gstKey }}">
                @endforeach
              </div>             
              <div class="pay-pr-array-hiddens" data-array-name="rcm_applicable" aria-hidden="true">
                @foreach ($selRcms as $rcmKey)
                  <input type="hidden" name="rcm_applicable[]" value="{{ $rcmKey }}">
                @endforeach
              </div>
              <div class="pay-pr-array-hiddens" data-array-name="vendor_id" aria-hidden="true">
                @foreach ($selVendorIds ?? [] as $vid)
                  <input type="hidden" name="vendor_id[]" value="{{ $vid }}">
                @endforeach
              </div>

              <div class="qd-filters pay-pr-qd-filters">
                <div class="qd-filter-row pay-pr-qd-filter-row sa-filter-row">
                  <div class="qd-filter-group">
                    <label class="sa-filter-label"><i class="bi bi-calendar3" aria-hidden="true"></i>Date range</label>
                    <div class="qd-date-wrap sa-filter-control" id="reportRange" role="button" tabindex="0" aria-label="Select date range">
                      <i class="fa fa-calendar" aria-hidden="true"></i>
                      <span id="dateLabel">{{ $dateLabel }}</span>
                      <i class="fa fa-caret-down sa-filter-caret" aria-hidden="true"></i>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper company-section pay-pr-dd" data-filter-param="company_id" data-empty-label="All companies">
                    <label class="sa-filter-label"><i class="bi bi-building" aria-hidden="true"></i>Company</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input sa-filter-control" placeholder="All companies" value="{{ $companyDisp }}" readonly autocomplete="off" aria-label="Filter by company">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search company..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect company-list">
                        @foreach ($companies as $company)
                          <div data-value="{{ $company->company_name }}" data-id="{{ $company->id }}" @class(['selected' => in_array((int) $company->id, $selCompanyIds, true)])>{{ $company->company_name }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper state-section pay-pr-dd" data-filter-param="state_id" data-empty-label="All states">
                    <label class="sa-filter-label"><i class="bi bi-geo-alt" aria-hidden="true"></i>State</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input sa-filter-control" placeholder="All states" value="{{ $stateDisp }}" readonly autocomplete="off" aria-label="Filter by state">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search state..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect state-list">
                        @foreach ($stateOptions as $stateKey => $stateLabel)
                          <div data-value="{{ $stateLabel }}" data-id="{{ $stateKey }}" @class(['selected' => in_array((int) $stateKey, $selStateIds, true)])>{{ $stateLabel }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper zone-section pay-pr-dd" data-filter-param="zone_id" data-empty-label="All zones">
                    <label class="sa-filter-label"><i class="bi bi-map" aria-hidden="true"></i>Zone</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input sa-filter-control" placeholder="All zones" value="{{ $zoneDisp }}" readonly autocomplete="off" aria-label="Filter by zone">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search zone..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect zone-list">
                        @foreach ($zones as $zone)
                          <div data-value="{{ $zone->name }}" data-id="{{ $zone->id }}" @class(['selected' => in_array((int) $zone->id, $selZoneIds, true)])>{{ $zone->name }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper branch-section pay-pr-dd" data-filter-param="branch_id" data-empty-label="All branches">
                    <label class="sa-filter-label"><i class="bi bi-diagram-3" aria-hidden="true"></i>Branch</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input sa-filter-control" placeholder="All branches" value="{{ $branchDisp }}" readonly autocomplete="off" aria-label="Filter by branch">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search branch..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect branch-list"></div>
                    </div>
                  </div>

                  @php
                    $raCat = $indexCategoryQuery ?? 'all';
                  @endphp
                  <div class="qd-filter-group">
                    <label class="sa-filter-label" for="category_filter"><i class="bi bi-tags" aria-hidden="true"></i>Category</label>
                    <select name="category" id="category_filter" class="form-select form-select-sm filter-category sa-filter-control" autocomplete="off">
                      <option value="all" @selected($raCat === 'all')>All</option>
                      <option value="{{ \App\Models\SecurityAgreement::TYPE_HOSPITAL }}" @selected($raCat === \App\Models\SecurityAgreement::TYPE_HOSPITAL)>Hospital</option>
                      <option value="{{ \App\Models\SecurityAgreement::TYPE_HOSTEL }}" @selected($raCat === \App\Models\SecurityAgreement::TYPE_HOSTEL)>Hostel</option>
                    </select>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper vendor-section pay-pr-dd" data-filter-param="vendor_id" data-empty-label="All vendors">
                    <label class="sa-filter-label"><i class="bi bi-person-badge" aria-hidden="true"></i>Vendor</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input sa-filter-control" placeholder="All vendors" value="{{ $landlordDisp ?? 'All vendors' }}" readonly autocomplete="off" aria-label="Filter by vendor">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search vendor..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect vendor-list">
                        @foreach ($landlords ?? [] as $landlord)
                          @php $landlordLabel = $landlord->listDisplayLabel(); @endphp
                          <div data-value="{{ $landlordLabel }}" data-id="{{ $landlord->id }}" @class(['selected' => in_array((int) $landlord->id, $selVendorIds ?? [], true)])>{{ $landlordLabel }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper pay-pr-dd" data-filter-param="rcm_applicable" data-empty-label="All RCM">
                    <label class="sa-filter-label"><i class="bi bi-receipt-cutoff" aria-hidden="true"></i>RCM</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input sa-filter-control" placeholder="All RCM" value="{{ $rcmDisp }}" readonly autocomplete="off" aria-label="Filter by RCM">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search RCM..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect rcm-list">
                        @foreach ($rcmOptions as $rcmKey => $rcmLabel)
                          <div data-value="{{ $rcmLabel }}" data-id="{{ $rcmKey }}" @class(['selected' => in_array((string) $rcmKey, $selRcms, true)])>{{ $rcmLabel }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper pay-pr-dd" data-filter-param="vendor_type_name" data-empty-label="All GST types">
                    <label class="sa-filter-label"><i class="bi bi-percent" aria-hidden="true"></i>GST type</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input sa-filter-control" placeholder="All GST types" value="{{ $gstDisp }}" readonly autocomplete="off" aria-label="Filter by GST type">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search GST type..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect gst-list">
                        @foreach ($gstOptions as $gstKey => $gstLabel)
                          <div data-value="{{ $gstLabel }}" data-id="{{ $gstKey }}" @class(['selected' => in_array($gstKey, $selGstTypes, true)]) title="Vendor type: {{ $gstKey }}">{{ $gstLabel }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              @fragment('sa-register-chips')
              <div id="sa-register-chips-slot">
                @if ($hasFilterChips)
                  <div class="qd-applied-bar pay-pr-applied-bar">
                    <span class="applied-label">Filters:</span>
                    <div class="pay-pr-filter-chips d-flex flex-wrap align-items-center" style="gap:6px;flex:1;min-width:0;">
                      @if ($dateFrom && $dateTo)
                        <a href="{{ $chipUrl(['date_from', 'date_to']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                          <i class="bi bi-calendar3" aria-hidden="true"></i><span>{{ $dateLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                        </a>
                      @endif
                      @if ($selectedCompanyName !== '')
                        <a href="{{ $chipUrl(['company_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                          <span>{{ $selectedCompanyName }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                        </a>
                      @endif
                      @if ($selectedZoneName !== '')
                        <a href="{{ $chipUrl(['zone_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                          <span>{{ $selectedZoneName }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                        </a>
                      @endif
                      @if ($selectedBranchName !== '')
                        <a href="{{ $chipUrl(['branch_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                          <span>{{ $selectedBranchName }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                        </a>
                      @endif
                      @if (($selectedLandlordLabel ?? '') !== '')
                        <a href="{{ $chipUrl(['vendor_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                          <span>Vendor: {{ $selectedLandlordLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                        </a>
                      @endif
                      @if ($selectedStateLabel !== '')
                        <a href="{{ $chipUrl(['state_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                          <span>State: {{ $selectedStateLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                        </a>
                      @endif
                      @if ($selectedRcmLabel !== '')
                        <a href="{{ $chipUrl(['rcm_applicable']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                          <span>RCM: {{ $selectedRcmLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                        </a>
                      @endif
                      @if ($selectedGstLabel !== '')
                        <a href="{{ $chipUrl(['vendor_type_name']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                          <span>GST type: {{ $selectedGstLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                        </a>
                      @endif
                      @if ($searchTrim !== '')
                        <a href="{{ $chipUrl(['search']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                          <i class="bi bi-search" aria-hidden="true"></i><span>{{ \Illuminate\Support\Str::limit($searchTrim, 48) }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                        </a>
                      @endif
                      @if (!empty($categoryChipLabel))
                        <a href="{{ $chipUrl(['category']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                          <span>{{ $categoryChipLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                        </a>
                      @endif
                    </div>
                    <a href="{{ $clearRegisterFiltersUrl }}" class="filter-badge filter-clear text-decoration-none ms-auto">Clear all</a>
                  </div>
                @endif
              </div>
              @endfragment

              <div class="row align-items-center justify-content-between pay-pr-table-toolbar mt-3 g-2 g-md-3 mx-0 w-100">
                <div class="col-12 col-auto pay-pr-table-toolbar-search">
                  <div class="qd-search-row pay-pr-toolbar-search-row mb-0">
                    <div class="qd-search-wrap pay-pr-toolbar-search-wrap">
                      <i class="bi bi-search" aria-hidden="true"></i>
                      <input type="text"
                        name="search"
                        id="universal_search"
                        value="{{ request('search') }}"
                        maxlength="200"
                        placeholder="Search agreements, location..."
                        autocomplete="off">
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-auto pay-pr-per-page-field pay-pr-per-page-field--toolbar">
                  <label class="form-label pay-pr-per-page-label mb-1 d-block text-md-end" for="pay-pr-per-page">Rows per page</label>
                  <select id="pay-pr-per-page" name="per_page" class="form-select form-select-sm pay-pr-per-page-select" autocomplete="off" aria-label="Rows per page">
                    @foreach ($perPageChoices as $choice)
                      <option value="{{ $choice }}" @selected((int) $perPage === (int) $choice)>{{ $choice }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </form>
          </div>

          @fragment('sa-register-panel')
          <div id="sa-register-panel" data-row-range="{{ $rowRangeLabel }}" data-total-rows="{{ $records->total() }}">
            <div class=" pay-pr-table-card">
              <div class="qdt-wrap pay-pr-qdt-wrap">
                <table class="qdt-table  pay-pr-data-table">
                  <thead class="qdt-head pay-pr-thead">
                    <tr>
                      <th scope="col" class="pay-pr-th-sno text-center">S.No</th>
                      <th scope="col" class="pay-pr-th-num">Agreement ID</th>
                      <th scope="col" class="pay-pr-th-date">Date</th>
                      <th scope="col" class="pay-pr-th-vendor">Owner</th>
                      <th scope="col" class="pay-pr-th-company">Company</th>
                      <th scope="col" class="pay-pr-th-loc">Location</th>
                      <th scope="col" class="pay-pr-th-type text-nowrap">Category</th>
                      <th scope="col" class="text-end pay-pr-th-amt pay-pr-th-security">
                        <span class="pay-pr-th-amt-label">Security<br>salary</span>
                      </th>
                      <th scope="col" class="text-end pay-pr-th-amt pay-pr-th-housekeeping">
                        <span class="pay-pr-th-amt-label">Housekeeping<br>salary</span>
                      </th>
                      <th scope="col" class="text-end pay-pr-th-pct pay-pr-th-gst-pct">GST %</th>
                      <th scope="col" class="text-end pay-pr-th-pct pay-pr-th-tds-pct">TDS %</th>
                      <th scope="col" class="pay-pr-th-status">GST</th>
                      <th scope="col" class="pay-pr-th-rcm">RCM</th>
                      <th scope="col" class="pay-pr-th-date">Ends on</th>
                      <th scope="col" class="text-end pay-pr-th-action">Action</th>
                    </tr>
                  </thead>
                  <tbody class="pay-pr-tbody">
                    @forelse ($records as $r)
                      @php
                        $gstKey = (string) ($r->gst_type ?? '');
                        $raRowType = \App\Models\SecurityAgreement::normalizeType((string) ($r->agreement_type ?? ''));
                        $locationLabel = trim(collect([$r->zone?->name, $r->branch?->name])->filter()->implode(' - '));
                        $gstPctDisplay = $r->gstPercentageDisplay();
                        $tdsPctDisplay = $r->tdsPercentageDisplay();
                      @endphp
                      <tr class="qdt-row pay-pr-row" data-sa-row-id="{{ $r->id }}">
                        <td class="pay-pr-td-sno text-muted text-center">{{ $records->firstItem() ? $records->firstItem() + $loop->index : $loop->iteration }}</td>
                        <td class="text-nowrap pay-pr-td-num">
                          <a class="pay-pr-ref agreement-ref" href="{{ route($routeNames['show'], ['securityAgreement' => $r]) }}">{{ $r->agreement_number }}</a>
                        </td>
                        <td class="pay-pr-td-date text-nowrap">
                          @if ($r->agreement_date)
                            <time datetime="{{ $r->agreement_date->toDateString() }}">{{ $r->agreement_date->format('d M Y') }}</time>
                          @else
                            <span class="pay-pr-dash">-</span>
                          @endif
                        </td>
                        <td class="pay-pr-td-names">
                          @php $vendorName = $r->vendorDisplayName(); @endphp
                          @if ($vendorName !== '' && $vendorName !== '—')
                            <span class="pay-pr-vendor-name" title="{{ $vendorName }}">{{ \Illuminate\Support\Str::limit($vendorName, 48) }}</span>
                          @else
                            <span class="pay-pr-dash">-</span>
                          @endif
                        </td>
                        <td class="pay-pr-td-company row-expand-trigger" role="button" tabindex="0" title="Click to expand full details">
                          @if ($r->company?->company_name)
                            <span class="pay-pr-company-name" title="{{ $r->company->company_name }}">{{ $r->company->company_name }}</span>
                          @else
                            <span class="pay-pr-dash">-</span>
                          @endif
                        </td>
                        <td class="align-middle pay-pr-loc-cell pay-pr-col-loc row-expand-trigger" role="button" tabindex="0" title="Click to expand full details">
                          <div class="pay-pr-loc-stack">
                            <div class="pay-pr-loc-text">
                              @if ($r->zone)
                                <div class="pay-pr-loc-zone">
                                  <span class="pay-pr-zone-pill text-nowrap">{{ strtoupper($r->zone->name) }}</span>
                                </div>
                              @endif
                              @if ($r->branch && $r->zone)
                                <div class="pay-pr-branch-txt text-nowrap" title="{{ $r->branch->name }}">{{ $r->branch->name }}</div>
                              @elseif ($r->branch)
                                <div class="pay-pr-loc-zone pay-pr-loc-zone--branch-only">
                                  <span class="pay-pr-branch-txt text-nowrap" title="{{ $r->branch->name }}">{{ $r->branch->name }}</span>
                                </div>
                              @elseif (!$r->zone)
                                <span class="pay-pr-dash">-</span>
                              @endif
                            </div>
                          </div>
                        </td>
                        <td class="pay-pr-td-type text-nowrap">
                          <span class="pay-pr-type-pill pay-pr-type-pill--{{ $raRowType }}" title="{{ ucfirst($raRowType) }}">{{ ucfirst($raRowType) }}</span>
                        </td>
                        <td class="text-end text-nowrap pay-pr-td-amt pay-pr-td-security">
                          @if ($r->security_fixed_salary_amount !== null)
                            <span class="pay-pr-amount" title="Security fixed salary"><span class="pay-pr-amount-curr" aria-hidden="true">&#8377;</span>{{ number_format((float) $r->security_fixed_salary_amount, 2) }}</span>
                          @else
                            <span class="pay-pr-dash">-</span>
                          @endif
                        </td>
                        <td class="text-end text-nowrap pay-pr-td-amt pay-pr-td-housekeeping">
                          @if ($r->housekeeping_fixed_salary_amount !== null)
                            <span class="pay-pr-amount" title="Housekeeping fixed salary"><span class="pay-pr-amount-curr" aria-hidden="true">&#8377;</span>{{ number_format((float) $r->housekeeping_fixed_salary_amount, 2) }}</span>
                          @else
                            <span class="pay-pr-dash">-</span>
                          @endif
                        </td>
                        <td class="text-end text-nowrap pay-pr-td-pct pay-pr-td-gst-pct">
                          @if ($gstPctDisplay !== '—')
                            <span class="sa-pct-value" title="GST percentage">{{ $gstPctDisplay }}</span>
                          @else
                            <span class="pay-pr-dash">-</span>
                          @endif
                        </td>
                        <td class="text-end text-nowrap pay-pr-td-pct pay-pr-td-tds-pct">
                          @if ($tdsPctDisplay !== '—')
                            <span class="sa-pct-value" title="TDS percentage">{{ $tdsPctDisplay }}</span>
                          @else
                            <span class="pay-pr-dash">-</span>
                          @endif
                        </td>
                        <td class="pay-pr-td-status">
                          <div class="tax-cell">
                            <span class="gst-pill gst-pill--{{ $gstKey !== '' ? $gstKey : 'unknown' }}" title="{{ \App\Models\SecurityAgreement::gstLabel($r->gst_type) }}">
                              {{ \App\Models\SecurityAgreement::gstLabel($r->gst_type) }}
                            </span>
                          </div>
                        </td>
                        <td class="pay-pr-td-rcm text-center align-middle">
                          @if ($r->isRcmApplicable())
                            <div class="rcm-cell">
                              <span class="rcm-pill rcm-pill--yes" title="{{ $r->rcmSummary() }}">Yes</span>
                              @if ($r->rcm_value !== null && (float) $r->rcm_value > 0)
                                <span class="rcm-value">&#8377;{{ number_format((float) $r->rcm_value, 2) }}</span>
                              @endif
                            </div>
                          @else
                            <span class="rcm-pill rcm-pill--no" title="RCM not applicable">No</span>
                          @endif
                        </td>
                        <td class="pay-pr-td-date text-nowrap">
                          @if ($r->end_of_agreement_date)
                            <time datetime="{{ $r->end_of_agreement_date->toDateString() }}">{{ $r->end_of_agreement_date->format('d M Y') }}</time>
                          @else
                            <span class="pay-pr-dash">-</span>
                          @endif
                        </td>
                        <td class="text-end pay-pr-td-action">
                          <div class="pay-pr-action-group" role="group" aria-label="Row actions">
                            <a class="pay-pr-btn-view pay-pr-btn-icononly" href="{{ route($routeNames['show'], ['securityAgreement' => $r]) }}" title="View agreement" aria-label="View agreement">
                              <span class="pay-pr-btn-view-ic" aria-hidden="true"><i class="bi bi-eye"></i></span>
                            </a>
                            <a class="pay-pr-btn-edit pay-pr-btn-icononly" href="{{ route($routeNames['edit'], ['securityAgreement' => $r]) }}" title="Edit agreement" aria-label="Edit agreement">
                              <span class="pay-pr-btn-edit-ic" aria-hidden="true"><i class="bi bi-pencil-square"></i></span>
                            </a>
                          </div>
                        </td>
                      </tr>
                      <tr class="detail-row d-none" data-sa-detail-for="{{ $r->id }}">
                        <td colspan="15">
                          <div class="detail-panel">
                            <div class="detail-panel-title"><i class="bi bi-layout-text-window-reverse me-1" aria-hidden="true"></i>Full details</div>
                            <div class="detail-grid">
                              <div class="detail-item">
                                <span class="detail-label">Vendor</span>
                                <span class="detail-value">{{ $r->vendorDisplayName() }}</span>
                              </div>
                              <div class="detail-item">
                                <span class="detail-label">Company</span>
                                <span class="detail-value">{{ $r->company?->company_name ?: '-' }}</span>
                              </div>
                              <div class="detail-item">
                                <span class="detail-label">Location</span>
                                <span class="detail-value">{{ $locationLabel !== '' ? $locationLabel : '-' }}</span>
                              </div>
                              <div class="detail-item">
                                <span class="detail-label">Security salary</span>
                                <span class="detail-value">{{ $r->security_fixed_salary_amount !== null ? '₹'.number_format((float) $r->security_fixed_salary_amount, 2) : '-' }}</span>
                              </div>
                              <div class="detail-item">
                                <span class="detail-label">Housekeeping salary</span>
                                <span class="detail-value">{{ $r->housekeeping_fixed_salary_amount !== null ? '₹'.number_format((float) $r->housekeeping_fixed_salary_amount, 2) : '-' }}</span>
                              </div>
                              @if (\App\Models\SecurityAgreement::isGstApplicableType($r->gst_type))
                                <div class="detail-item">
                                  <span class="detail-label">GST %</span>
                                  <span class="detail-value">{{ $gstPctDisplay !== '—' ? $gstPctDisplay : '-' }}</span>
                                </div>
                                <div class="detail-item">
                                  <span class="detail-label">GST amount</span>
                                  <span class="detail-value">{{ $r->gst_amount !== null && (float) $r->gst_amount > 0 ? '₹'.number_format((float) $r->gst_amount, 2) : '-' }}</span>
                                </div>
                              @endif
                              <div class="detail-item">
                                <span class="detail-label">TDS %</span>
                                <span class="detail-value">{{ $tdsPctDisplay !== '—' ? $tdsPctDisplay : '-' }}</span>
                              </div>
                              <div class="detail-item">
                                <span class="detail-label">TDS</span>
                                <span class="detail-value">{{ $r->tdsSummary() }}</span>
                              </div>
                              <div class="detail-item">
                                <span class="detail-label">RCM</span>
                                <span class="detail-value">{{ $r->rcmSummary() }}</span>
                              </div>
                              <div class="detail-item detail-item detail-item--wide">
                                <span class="detail-label">Address</span>
                                <span class="detail-value">{{ trim((string) $r->address) !== '' ? $r->address : '-' }}</span>
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="15" class="text-center pay-pr-empty table-empty">
                          <i class="bi bi-inbox d-block pay-pr-empty-icon" aria-hidden="true"></i>
                          <div class="pay-pr-empty-title mt-3">
                            No {{ $moduleTitleLower }} records found
                          </div>
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            @if ($records->total() > 0)
              <div class="pay-pr-pagination-bar">
                @if ($records->hasPages())
                  <div class="pay-pr-pag-nav pay-pr-pag-nav--full">
                    {{ $records->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                  </div>
                @else
                  <p class="pay-pr-pag-summary mb-0 px-1">
                    @if ($records->firstItem())
                      Showing <strong>{{ $records->firstItem() }}</strong> to <strong>{{ $records->lastItem() }}</strong> of <strong>{{ $records->total() }}</strong> results
                    @else
                      Showing <strong>0</strong> of <strong>{{ $records->total() }}</strong> results
                    @endif
                  </p>
                @endif
              </div>
            @endif
          </div>
          @endfragment
          </div>
        </div>
      </div>
    </div>

    <div id="sa-register-loading-overlay" class="sa-register-loading-overlay" aria-live="polite" aria-busy="false" hidden>
      <div class="sa-register-loading-card" role="status">
        <div class="sa-register-loading-spinner" aria-hidden="true"></div>
        <p class="sa-register-loading-text">Loading agreements…</p>
      </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('assets/js/security_agreement.js') }}?v={{ @filemtime(public_path('assets/js/security_agreement.js')) }}"></script>
@endpush
