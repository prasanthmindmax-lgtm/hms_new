<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/payment_request.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/rental_agreement.css') }}?v={{ @filemtime(public_path('assets/css/rental_agreement.css')) }}" />

<body class="ra-page" style="overflow-x: hidden;">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<script id="raBranchesData" type="application/json">@json($branches->map(fn ($branch) => ['id' => (int) $branch->id, 'name' => $branch->name, 'zone_id' => (int) $branch->zone_id])->values())</script>
<script id="raChartData" type="application/json">@json($chartData ?? [])</script>
<div
  id="raFlashData"
  data-success="{{ session('success', '') }}"
  data-error="{{ session('error', '') }}"
  hidden
></div>

<div class="pc-container">
  <div class="pc-content">
    <div class="ra-shell tk-pr-index ra-register--llp-match">
      <div class="ra-card">
        <header class="tk-hero ra-register-tk-hero" aria-labelledby="ra-register-heading">
          <div class="tk-hero-inner">
            <h1 class="tk-hero-title" id="ra-register-heading">
              <i class="bi bi-journal-bookmark" aria-hidden="true"></i>
              {{ $moduleRegisterTitle ?? $moduleTitle }}
            </h1>
            @php
              $raLeadCat = $indexCategoryQuery ?? 'all';
              $raLeadScope = match ($raLeadCat) {
                  \App\Models\RentalAgreement::TYPE_HOSPITAL => \App\Models\RentalAgreement::TYPE_LABELS[\App\Models\RentalAgreement::TYPE_HOSPITAL],
                  \App\Models\RentalAgreement::TYPE_HOSTEL => \App\Models\RentalAgreement::TYPE_LABELS[\App\Models\RentalAgreement::TYPE_HOSTEL],
                  default => 'All categories (hospital & hostel)',
              };
            @endphp
            <p class="ra-hero-lead mb-0">{{ $raLeadScope }}</p>
          </div>
          <div class="tk-hero-actions flex-wrap">
            <button type="button" class="ra-register-btn-ghost ra-chart-toggle" id="raChartToggle" aria-expanded="false" aria-controls="raChartPanel">
              <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
              Chart
            </button>
            <a href="{{ $raCreateUrl }}" class="tk-btn-raise">
              <i class="bi bi-plus-lg" aria-hidden="true"></i>
              New agreement
            </a>
          </div>
        </header>

        <div class="ra-body">
          <div class="ra-stats">
            <div class="ra-stat">
              <span class="ra-stat-ic"><i class="bi bi-journal-text"></i></span>
              <span class="ra-stat-lbl">Total agreements</span>
              <span class="ra-stat-num">{{ number_format($stats['total']) }}</span>
              <span class="ra-stat-hint">Matching current filters</span>
            </div>
            <div class="ra-stat">
              <span class="ra-stat-ic"><i class="bi bi-cash-stack"></i></span>
              <span class="ra-stat-lbl">Refundable advance total</span>
              <span class="ra-stat-num">{{ number_format($stats['advance_total'], 2) }}</span>
              <span class="ra-stat-hint">Sum of refundable advance paid</span>
            </div>
            <div class="ra-stat">
              <span class="ra-stat-ic"><i class="bi bi-currency-rupee"></i></span>
              <span class="ra-stat-lbl">Monthly rent total</span>
              <span class="ra-stat-num">{{ number_format($stats['monthly_rent_total'], 2) }}</span>
              <span class="ra-stat-hint">Combined monthly rent</span>
            </div>
            <div class="ra-stat">
              <span class="ra-stat-ic"><i class="bi bi-calendar2-week"></i></span>
              <span class="ra-stat-lbl">Ending in 30 days</span>
              <span class="ra-stat-num">{{ number_format($stats['ending_soon']) }}</span>
              <span class="ra-stat-hint">Upcoming expiries</span>
            </div>
          </div>

          @php
            $chartData = $chartData ?? ['rentByType' => [], 'countByType' => [], 'typeLabels' => [], 'topOwners' => []];
          @endphp
          <div class="ra-chart-layout" id="raChartLayout">
            <aside class="ra-chart-panel d-none" id="raChartPanel" aria-label="Rental agreements chart">
              <div class="ra-chart-panel-head">
                <span><i class="bi bi-bar-chart-line me-1" aria-hidden="true"></i>Overview chart</span>
                <span class="ra-chart-panel-sub text-muted">Filtered agreements</span>
              </div>
              <div class="ra-chart-canvas-wrap">
                <canvas id="raRentByTypeChart" height="220" aria-label="Monthly rent by category"></canvas>
              </div>
              <div class="ra-chart-canvas-wrap ra-chart-canvas-wrap--sm mt-3">
                <canvas id="raTopOwnersChart" height="200" aria-label="Top owners by monthly rent"></canvas>
              </div>
            </aside>

          <div class="ra-register-main">
          <div class="tk-filter-shell tk-filter-qd pay-pr-filter-shell">
            <div class="tk-filter-head pay-pr-filter-head">
              <div class="tk-filter-title">
                <i class="bi bi-sliders2" aria-hidden="true"></i> Filters
              </div>
              <div class="pay-pr-filter-head-meta d-flex flex-wrap align-items-center gap-2 justify-content-end">
                <span class="tk-showing-pill mb-0">
                  Rows <strong>{{ $rowRangeLabel }}</strong> of <strong>{{ $records->total() }}</strong>
                </span>
              </div>
            </div>

            <form method="get" action="{{ $raListingBaseUrl }}" id="ra-filter-form" autocomplete="off">
              <input type="hidden" name="date_from" id="ra_date_from" value="{{ request('date_from') }}">
              <input type="hidden" name="date_to" id="ra_date_to" value="{{ request('date_to') }}">

              <div class="pay-pr-array-hiddens" data-array-name="company_id" aria-hidden="true">
                @foreach ($selCompanyIds as $cid)
                  <input type="hidden" name="company_id[]" value="{{ $cid }}">
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
              <div class="pay-pr-array-hiddens" data-array-name="state_id" aria-hidden="true">
                @foreach ($selStateIds as $stateId)
                  <input type="hidden" name="state_id[]" value="{{ $stateId }}">
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
                <div class="qd-filter-row pay-pr-qd-filter-row">
                  <div class="qd-filter-group">
                    <label><i class="bi bi-calendar3 me-1" aria-hidden="true"></i>Date Range</label>
                    <div class="qd-date-wrap" id="raReportRange" role="button" tabindex="0">
                      <i class="fa fa-calendar" aria-hidden="true"></i>
                      <span id="raDateLabel">{{ $dateLabel }}</span>
                      <i class="fa fa-caret-down" style="margin-left:auto;" aria-hidden="true"></i>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper company-section pay-pr-dd" data-filter-param="company_id" data-empty-label="All companies">
                    <label>Company</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Company" value="{{ $companyDisp }}" readonly autocomplete="off">
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

                  <div class="qd-filter-group tax-dropdown-wrapper zone-section pay-pr-dd" data-filter-param="zone_id" data-empty-label="All zones">
                    <label>Zone</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Zone" value="{{ $zoneDisp }}" readonly autocomplete="off">
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
                    <label>Branch</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Branch" value="{{ $branchDisp }}" readonly autocomplete="off">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search branch..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect branch-list"></div>
                    </div>
                  </div>
                </div>

                <div class="qd-filter-row pay-pr-qd-filter-row pay-pr-qd-filter-row--second ra-filter-row-category-gst">
                  @php
                    $raCat = $indexCategoryQuery ?? 'all';
                  @endphp
                  <div class="qd-filter-group ra-filter-category-gst-cell">
                    <label for="ra_category_filter"><i class="bi bi-tags me-1" aria-hidden="true"></i>Category</label>
                    <select name="category" id="ra_category_filter" class="form-select form-select-sm ra-category-select" autocomplete="off" onchange="this.form.submit()">
                      <option value="all" @selected($raCat === 'all')>All</option>
                      <option value="{{ \App\Models\RentalAgreement::TYPE_HOSPITAL }}" @selected($raCat === \App\Models\RentalAgreement::TYPE_HOSPITAL)>Hospital</option>
                      <option value="{{ \App\Models\RentalAgreement::TYPE_HOSTEL }}" @selected($raCat === \App\Models\RentalAgreement::TYPE_HOSTEL)>Hostel</option>
                    </select>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper vendor-section pay-pr-dd ra-filter-category-gst-cell" data-filter-param="vendor_id" data-empty-label="All landlords">
                    <label><i class="bi bi-person-badge me-1" aria-hidden="true"></i>Landlord name</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select landlord" value="{{ $landlordDisp ?? 'All landlords' }}" readonly autocomplete="off">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search landlord..." autocomplete="off"></div>
                      <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                      </div>
                      <div class="dropdown-list multiselect vendor-list">
                        @foreach ($landlords ?? [] as $landlord)
                          @php
                            $landlordLabel = trim((string) ($landlord->display_name ?? '')) !== ''
                              ? (string) $landlord->display_name
                              : (string) ($landlord->company_name ?? '');
                          @endphp
                          @if ($landlordLabel !== '')
                            <div data-value="{{ $landlordLabel }}" data-id="{{ $landlord->id }}" @class(['selected' => in_array((int) $landlord->id, $selVendorIds ?? [], true)])>{{ $landlordLabel }}</div>
                          @endif
                        @endforeach
                      </div>
                    </div>
                  </div>

                  <div class="qd-filter-group tax-dropdown-wrapper state-section pay-pr-dd ra-filter-category-gst-cell" data-filter-param="state_id" data-empty-label="All states">
                    <label>State</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select State" value="{{ $stateDisp }}" readonly autocomplete="off">
                    <div class="dropdown-menu tax-dropdown pay-pr-tax-dd">
                      <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search State..." autocomplete="off"></div>
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

                  <div class="qd-filter-group tax-dropdown-wrapper pay-pr-dd ra-filter-category-gst-cell" data-filter-param="rcm_applicable" data-empty-label="All RCM">
                    <label>RCM</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select RCM" value="{{ $rcmDisp }}" readonly autocomplete="off">
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

                  <div class="qd-filter-group tax-dropdown-wrapper pay-pr-dd ra-filter-category-gst-cell" data-filter-param="vendor_type_name" data-empty-label="All GST types">
                    <label>GST type</label>
                    <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select GST type" value="{{ $gstDisp }}" readonly autocomplete="off">
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
                        <span>Landlord: {{ $selectedLandlordLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
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

              <div class="row align-items-center justify-content-between pay-pr-table-toolbar mt-3 g-2 g-md-3 mx-0 w-100">
                <div class="col-12 col-auto pay-pr-table-toolbar-search">
                  <div class="qd-search-row pay-pr-toolbar-search-row mb-0">
                    <div class="qd-search-wrap pay-pr-toolbar-search-wrap">
                      <i class="bi bi-search" aria-hidden="true"></i>
                      <input type="text"
                        name="search"
                        id="ra_universal_search"
                        value="{{ request('search') }}"
                        maxlength="200"
                        placeholder="Search agreements, landlord, location, EB, PAN..."
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

          <div class="tk-table-card pay-pr-table-card">
            <div class="qdt-wrap pay-pr-qdt-wrap">
              <table class="qdt-table tk-pr-table pay-pr-data-table">
                <thead class="qdt-head pay-pr-thead">
                  <tr>
                    <th scope="col" class="pay-pr-th-sno text-center">S.No</th>
                    <th scope="col" class="pay-pr-th-num">Rental RVID</th>
                    <th scope="col" class="pay-pr-th-date">Date</th>
                    <th scope="col" class="pay-pr-th-vendor">Owner</th>
                    <th scope="col" class="pay-pr-th-company">Company</th>
                    <th scope="col" class="pay-pr-th-loc">Location</th>
                    <th scope="col" class="pay-pr-th-type text-nowrap">Category</th>
                    <th scope="col" class="pay-pr-th-date">Period</th>
                    <th scope="col" class="text-end pay-pr-th-amt pay-pr-th-refund">
                      <span class="pay-pr-th-amt-label">Refundable<br>advance paid</span>
                    </th>
                    <th scope="col" class="text-end pay-pr-th-amt pay-pr-th-rent">
                      <span class="pay-pr-th-amt-label">Monthly rent</span>
                    </th>
                    <th scope="col" class="pay-pr-th-status">GST</th>
                    <th scope="col" class="pay-pr-th-rcm">RCM</th>
                    <th scope="col" class="pay-pr-th-date">Ends on</th>
                    <th scope="col" class="pay-pr-th-building text-center">Building Photo</th>
                    <th scope="col" class="text-end pay-pr-th-action">Action</th>
                  </tr>
                </thead>
                <tbody class="pay-pr-tbody">
                  @forelse ($records as $r)
                    @php
                      $buildingPhotoUrl = \App\Models\RentalAgreement::buildingPhotoPublicUrl($r->building_photo_path);
                      if ($buildingPhotoUrl && ! str_contains($buildingPhotoUrl, '/public/')) {
                          $buildingPhotoUrl = str_replace('/rental_agreement_attachments/', '/public/rental_agreement_attachments/', $buildingPhotoUrl);
                      }
                      $gstKey = (string) ($r->gst_type ?? '');
                      $raRowType = \App\Models\RentalAgreement::normalizeType((string) ($r->agreement_type ?? ''));
                      $extraNames = $r->additionalPartyNamesList();
                      $mapUrl = \App\Models\RentalAgreement::googleMapsSearchUrl($r->zone?->name, $r->branch?->name, (string) $r->address);
                      $periodLabel = $formatAgreementPeriod($r->agreement_period);
                      $rentSchedule = $r->yearlyRentSchedule();
                      $locationLabel = trim(collect([$r->zone?->name, $r->branch?->name])->filter()->implode(' - '));
                    @endphp
                    <tr class="qdt-row pay-pr-row" data-ra-row-id="{{ $r->id }}">
                      <td class="pay-pr-td-sno text-muted text-center">{{ $records->firstItem() ? $records->firstItem() + $loop->index : $loop->iteration }}</td>
                      <td class="text-nowrap pay-pr-td-num">
                        <a class="tk-pr-num-link pay-pr-ref" href="{{ route($routeNames['show'], ['rentalAgreement' => $r]) }}">{{ $r->agreement_number }}</a>
                      </td>
                      <td class="pay-pr-td-date text-nowrap">
                        @if ($r->agreement_date)
                          <time datetime="{{ $r->agreement_date->toDateString() }}">{{ $r->agreement_date->format('d M Y') }}</time>
                        @else
                          <span class="pay-pr-dash">-</span>
                        @endif
                      </td>
                      <td class="pay-pr-td-names">
                        @php
                          $vendorName = trim((string) $r->owner_name);
                          $vendorKey = mb_strtolower($vendorName);
                          $ownerVendorId = (int) ($r->vendor_id ?? 0) > 0
                            ? (int) $r->vendor_id
                            : (($vendorIdByOwnerName ?? [])[$vendorKey] ?? null);
                          $ownerPaymentsQs = array_filter(['category' => request()->query('category')]);
                          $ownerPaymentsUrl = $ownerVendorId
                            ? route($routeNames['vendorOwnerPayments'], array_merge(['vendor' => $ownerVendorId], $ownerPaymentsQs))
                            : route($routeNames['ownerPayments'], array_merge(['rentalAgreement' => $r], $ownerPaymentsQs));
                        @endphp
                        @if ($vendorName !== '')
                          <a href="{{ $ownerPaymentsUrl }}"
                            class="pay-pr-vendor-name ra-owner-payments-link"
                            title="Rental advances &amp; bill payments for {{ $vendorName }}">
                            {{ \Illuminate\Support\Str::limit($vendorName, 48) }}
                          </a>
                        @else
                          <span class="pay-pr-dash">-</span>
                        @endif
                      </td>
                      <td class="pay-pr-td-company ra-expand-cell" role="button" tabindex="0" title="Click to expand full details">
                        @if ($r->company?->company_name)
                          <span class="pay-pr-company-name" title="{{ $r->company->company_name }}">{{ $r->company->company_name }}</span>
                        @else
                          <span class="pay-pr-dash">-</span>
                        @endif
                      </td>
                      <td class="align-middle pay-pr-loc-cell pay-pr-col-loc ra-expand-cell" role="button" tabindex="0" title="Click to expand full details">
                        <div class="pay-pr-loc-stack">
                          <div class="pay-pr-loc-text">
                            @if ($r->zone)
                              <div class="pay-pr-loc-zone">
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="pay-pr-map-link pay-pr-map-link--inline" title="Open in Google Maps" aria-label="Open {{ $r->zone->name }} in Google Maps" onclick="event.stopPropagation();">
                                  <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                                </a>
                                <span class="pay-pr-zone-pill text-nowrap">{{ strtoupper($r->zone->name) }}</span>
                              </div>
                            @endif
                            @if ($r->branch && $r->zone)
                              <div class="pay-pr-branch-txt text-nowrap" title="{{ $r->branch->name }}">{{ $r->branch->name }}</div>
                            @elseif ($r->branch)
                              <div class="pay-pr-loc-zone pay-pr-loc-zone--branch-only">
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="pay-pr-map-link pay-pr-map-link--inline" title="Open in Google Maps" aria-label="Open {{ $r->branch->name }} in Google Maps" onclick="event.stopPropagation();">
                                  <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                                </a>
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

                      <td class="pay-pr-td-date text-nowrap">
                        @if (filled(trim((string) $periodLabel)) && trim((string) $periodLabel) !== '-')
                          <button type="button"
                            class="ra-period-link"
                            data-period="{{ $periodLabel }}"
                            data-schedule='@json($rentSchedule)'
                            data-hike="{{ (float) ($r->rent_hike_percentage ?? 0) }}"
                            data-base-rent="{{ (float) $r->monthly_rent_amount }}"
                            aria-label="View year-wise rent for {{ $periodLabel }}">
                            {{ $periodLabel }}
                          </button>
                        @else
                          <span class="pay-pr-dash">-</span>
                        @endif
                      </td>
                      <td class="text-end text-nowrap pay-pr-td-amt">
                        <span class="pay-pr-amount"><span class="pay-pr-amount-curr" aria-hidden="true">&#8377;</span>{{ number_format((float) $r->advance_amount, 2) }}</span>
                      </td>
                      <td class="text-end text-nowrap pay-pr-td-amt">
                        <span class="pay-pr-amount"><span class="pay-pr-amount-curr" aria-hidden="true">&#8377;</span>{{ number_format((float) $r->monthly_rent_amount, 2) }}</span>
                      </td>
                      <td class="pay-pr-td-status">
                        <div class="ra-tax-cell">
                          <span class="ra-gst-pill ra-gst-pill--{{ $gstKey !== '' ? $gstKey : 'unknown' }}" title="{{ \App\Models\RentalAgreement::gstLabel($r->gst_type) }}">
                            {{ \App\Models\RentalAgreement::gstLabel($r->gst_type) }}
                          </span>
                          @if (\App\Models\RentalAgreement::isGstApplicableType($r->gst_type))
                            <div class="ra-gst-meta">{{ $r->gstRateAmountSummary() }}</div>
                          @endif
                        </div>
                      </td>
                      <td class="pay-pr-td-rcm text-center align-middle">
                        @if ($r->isRcmApplicable())
                          <div class="ra-rcm-col">
                            <span class="ra-rcm-pill ra-rcm-pill--yes" title="{{ $r->rcmSummary() }}">Yes</span>
                            @if ($r->rcm_value !== null && (float) $r->rcm_value > 0)
                              <span class="ra-rcm-value">&#8377;{{ number_format((float) $r->rcm_value, 2) }}</span>
                            @endif
                          </div>
                        @else
                          <span class="ra-rcm-pill ra-rcm-pill--no" title="RCM not applicable">No</span>
                        @endif
                      </td>
                      <td class="pay-pr-td-date text-nowrap">
                        @if ($r->end_of_agreement_date)
                          <time datetime="{{ $r->end_of_agreement_date->toDateString() }}">{{ $r->end_of_agreement_date->format('d M Y') }}</time>
                        @else
                          <span class="pay-pr-dash">-</span>
                        @endif
                      </td>
                      <td class="pay-pr-td-building text-center align-middle">
                        @if ($buildingPhotoUrl)
                          <a href="{{ $buildingPhotoUrl }}" target="_blank" rel="noopener" class="pay-pr-building-thumb-wrap" title="Building photo">
                            <img src="{{ $buildingPhotoUrl }}" alt="" class="pay-pr-building-thumb" loading="lazy" width="56" height="42">
                          </a>
                        @else
                          <span class="pay-pr-dash">-</span>
                        @endif
                      </td>
                      <td class="text-end pay-pr-td-action">
                        <div class="pay-pr-action-group" role="group" aria-label="Row actions">
                          <a class="pay-pr-btn-view pay-pr-btn-icononly" href="{{ route($routeNames['show'], ['rentalAgreement' => $r]) }}" title="View agreement" aria-label="View agreement">
                            <span class="pay-pr-btn-view-ic" aria-hidden="true"><i class="bi bi-eye"></i></span>
                          </a>
                          <a class="pay-pr-btn-edit pay-pr-btn-icononly" href="{{ route($routeNames['edit'], ['rentalAgreement' => $r]) }}" title="Edit agreement" aria-label="Edit agreement">
                            <span class="pay-pr-btn-edit-ic" aria-hidden="true"><i class="bi bi-pencil-square"></i></span>
                          </a>
                        </div>
                      </td>
                    </tr>
                    <tr class="ra-detail-row d-none" data-ra-detail-for="{{ $r->id }}">
                      <td colspan="15">
                        <div class="ra-detail-panel">
                          <div class="ra-detail-panel-title"><i class="bi bi-layout-text-window-reverse me-1" aria-hidden="true"></i>Full details</div>
                          <div class="ra-detail-grid">
                            <div class="ra-detail-item">
                              <span class="ra-detail-lbl">Landlord</span>
                              <span class="ra-detail-val">{{ trim((string) $r->owner_name) !== '' ? $r->owner_name : '-' }}</span>
                            </div>
                            <div class="ra-detail-item">
                              <span class="ra-detail-lbl">Owner</span>
                              <span class="ra-detail-val">
                                @if (!empty($extraNames))
                                  {!! implode('<br>', array_map('e', $extraNames)) !!}
                                @else
                                  -
                                @endif
                              </span>
                            </div>
                            <div class="ra-detail-item">
                              <span class="ra-detail-lbl">Company</span>
                              <span class="ra-detail-val">{{ $r->company?->company_name ?: '-' }}</span>
                            </div>
                            <div class="ra-detail-item">
                              <span class="ra-detail-lbl">Location</span>
                              <span class="ra-detail-val">{{ $locationLabel !== '' ? $locationLabel : '-' }}</span>
                            </div>
                            @if (\App\Models\RentalAgreement::isGstApplicableType($r->gst_type))
                              <div class="ra-detail-item">
                                <span class="ra-detail-lbl">GST % / amount</span>
                                <span class="ra-detail-val">{{ $r->gstRateAmountSummary() }}</span>
                              </div>
                            @endif
                            <div class="ra-detail-item">
                              <span class="ra-detail-lbl">TDS</span>
                              <span class="ra-detail-val">{{ $r->tdsSummary() }}</span>
                            </div>
                            <div class="ra-detail-item">
                              <span class="ra-detail-lbl">RCM</span>
                              <span class="ra-detail-val">{{ $r->rcmSummary() }}</span>
                            </div>
                            <div class="ra-detail-item ra-detail-item--wide">
                              <span class="ra-detail-lbl">Address</span>
                              <span class="ra-detail-val">{{ trim((string) $r->address) !== '' ? $r->address : '-' }}</span>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="15" class="text-center pay-pr-empty ra-register-empty">
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
          </div>
        </div>
      </div>
    </div>

<div id="raPeriodPopover" class="ra-period-popover d-none" role="dialog" aria-label="Year-wise rent schedule"></div>
  </div>
</div>
@include('superadmin.superadminfooter')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('assets/js/rental_agreement.js') }}?v={{ @filemtime(public_path('assets/js/rental_agreement.js')) }}"></script>
</body>
</html>
