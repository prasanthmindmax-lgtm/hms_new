<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/payment_request.css') }}" />

<body class="pay-pr-index-page pay-ap-index-page" style="overflow-x: hidden;">
  <div class="page-loader">
    <div class="bar"></div>
  </div>

  @include('superadmin.superadminnav')
  @include('superadmin.superadminheader')

  <div class="pc-container">
    <div class="pc-content">

@php
  $fmtMoney = function (float $n): string {
    return number_format($n, 2);
  };
  $rowFrom = $rows->firstItem();
  $rowTo = $rows->lastItem();
  $rowRangeLabel = ($rowFrom && $rowTo) ? ($rowFrom . '–' . $rowTo) : '0';

  $selCompanyIds = array_values(array_filter(array_map('intval', (array) request('company_id', []))));
  $selZoneIds = array_values(array_filter(array_map('intval', (array) request('zone_id', []))));
  $selBranchIds = array_values(array_filter(array_map('intval', (array) request('branch_id', []))));
  $selPaymentTypes = array_values(array_filter((array) request('payment_type', [])));
  $selVendorIds = array_values(array_filter(array_map('intval', (array) request('vendor_id', []))));

  $pr_join = function (array $ids, $collection, string $idKey, string $labelKey, string $emptyLabel): string {
    if ($ids === []) {
      return $emptyLabel;
    }
    $labels = $collection->whereIn($idKey, $ids)->pluck($labelKey)->filter()->values();
    return $labels->isNotEmpty() ? $labels->implode(', ') : $emptyLabel;
  };

  $companyDisp = $pr_join($selCompanyIds, $companies, 'id', 'company_name', 'All companies');
  $zoneDisp = $pr_join($selZoneIds, $zones, 'id', 'name', 'All zones');
  $branchDisp = $pr_join($selBranchIds, $branches, 'id', 'name', 'All branches');
  $typeDisp = $selPaymentTypes === []
    ? 'All payment types'
    : collect($selPaymentTypes)->map(fn ($k) => $paymentTypeLabels[$k] ?? $k)->implode(', ');
  $vendorDisp = $selVendorIds === []
    ? 'All vendors'
    : $vendors->filter(fn ($v) => in_array((int) $v->id, $selVendorIds, true))->map(function ($v) {
      $t = trim((string) (($v->display_name ?: '') ?: ($v->company_name ?? '')));

      return $t !== '' ? $t : 'Vendor #'.$v->id;
    })->implode(', ');

  $df = request('date_from');
  $dt = request('date_to');
  $dateLabel = 'All approval dates';
  if ($df && $dt) {
    try {
      $dateLabel = \Carbon\Carbon::parse($df)->format('M j, Y').' – '.\Carbon\Carbon::parse($dt)->format('M j, Y');
    } catch (\Throwable $e) {
      $dateLabel = trim(($df ?: '…').' – '.($dt ?: '…'));
    }
  } elseif ($df || $dt) {
    $dateLabel = trim(($df ?: '…').' – '.($dt ?: '…'));
  }

  $payApChipUrl = function (array $withoutKeys): string {
    $keys = array_merge($withoutKeys, ['page']);

    return route('superadmin.approved-payments.index', request()->except($keys));
  };

  $payApSearchTrim = trim((string) request('universal_search', ''));
  $payApHasFilterChips = ($df && $dt)
    || $selCompanyIds !== []
    || $selZoneIds !== []
    || $selBranchIds !== []
    || $selPaymentTypes !== []
    || $selVendorIds !== []
    || $payApSearchTrim !== '';
@endphp
<div class="pr-pay-module w-100 mb-4">
  <div class="qd-card tk-tickets-page tk-pr-index pay-ap-card w-100">
    <header class="tk-hero">
      <div class="tk-hero-inner">
        <h1 class="tk-hero-title" id="pay-ap-dash-title">
          <i class="bi bi-patch-check-fill" aria-hidden="true"></i> Approved payments
        </h1>
      </div>
    </header>

    <div class="tk-dash-body">
    <div class="tk-stats-row" role="group" aria-label="Approved payment summary">
      <div class="tk-stat-card tk-stat-total tk-stat-pr-static" role="group" aria-label="Approved count">
        <span class="tk-stat-ic" aria-hidden="true"><i class="bi bi-patch-check"></i></span>
        <span class="tk-stat-lbl">Approved total</span>
        <span class="tk-stat-num">{{ $stats['total'] }}</span>
        <span class="tk-stat-hint">Matching filters</span>
      </div>
      <div class="tk-stat-card tk-stat-in_progress tk-stat-pr-static" role="group" aria-label="Sum approved amount">
        <span class="tk-stat-ic" aria-hidden="true"><i class="bi bi-cash-stack"></i></span>
        <span class="tk-stat-lbl">Sum of amounts</span>
        <span class="tk-stat-num pay-pr-stat-amount">₹{{ $fmtMoney($stats['sum_amount']) }}</span>
        <span class="tk-stat-hint">Approved requests only</span>
      </div>
      <div class="tk-stat-card tk-stat-open tk-stat-pr-static" role="group" aria-label="Approved this month">
        <span class="tk-stat-ic" aria-hidden="true"><i class="bi bi-calendar-check"></i></span>
        <span class="tk-stat-lbl">Approved this month</span>
        <span class="tk-stat-num">{{ $stats['this_month'] }}</span>
        <span class="tk-stat-hint">By review date · {{ now()->format('F Y') }}</span>
      </div>
      <div class="tk-stat-card tk-stat-closed tk-stat-pr-static" role="group" aria-label="PO linked">
        <span class="tk-stat-ic" aria-hidden="true"><i class="bi bi-link-45deg"></i></span>
        <span class="tk-stat-lbl">PO-linked</span>
        <span class="tk-stat-num">{{ $stats['po_linked'] }}</span>
        <span class="tk-stat-hint">With a purchase order</span>
      </div>
    </div>

    <div class="tk-filter-shell tk-filter-qd pay-pr-filter-shell">
      <div class="tk-filter-head pay-pr-filter-head">
        <div class="tk-filter-title">
          <i class="bi bi-funnel" aria-hidden="true"></i> Refine approved list
        </div>
        <div class="pay-pr-filter-head-meta d-flex flex-wrap align-items-center gap-2 justify-content-end">
          <span class="tk-showing-pill mb-0">
            Rows <strong>{{ $rowRangeLabel }}</strong> of <strong>{{ $rows->total() }}</strong>
          </span>
        </div>
      </div>

      <form method="get" action="{{ route('superadmin.approved-payments.index') }}" id="pay-ap-filter-form" autocomplete="off">
        <input type="hidden" name="date_from" id="pay_ap_date_from" value="{{ request('date_from') }}">
        <input type="hidden" name="date_to" id="pay_ap_date_to" value="{{ request('date_to') }}">

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
        <div class="pay-pr-array-hiddens" data-array-name="payment_type" aria-hidden="true">
          @foreach ($selPaymentTypes as $pk)
            <input type="hidden" name="payment_type[]" value="{{ $pk }}">
          @endforeach
        </div>
        <div class="pay-pr-array-hiddens" data-array-name="vendor_id" aria-hidden="true">
          @foreach ($selVendorIds as $vid)
            <input type="hidden" name="vendor_id[]" value="{{ $vid }}">
          @endforeach
        </div>

        <div class="qd-filters pay-pr-qd-filters">
          <div class="qd-filter-row pay-pr-qd-filter-row">
            <div class="qd-filter-group">
              <label><i class="bi bi-patch-check me-1" aria-hidden="true"></i>Approval date range</label>
              <div class="qd-date-wrap" id="payApReportRange" role="button" tabindex="0">
                <i class="fa fa-calendar" aria-hidden="true"></i>
                <span id="payApDateLabel">{{ $dateLabel }}</span>
                <i class="fa fa-caret-down" style="margin-left:auto;" aria-hidden="true"></i>
              </div>
            </div>

            <div class="qd-filter-group tax-dropdown-wrapper company-section pay-pr-dd" data-filter-param="company_id" data-empty-label="All companies">
              <label>Company</label>
              <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Company" value="{{ $companyDisp }}" readonly autocomplete="off">
              <div class="dropdown-menu tax-dropdown pay-ap-tax-dd">
                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search company…" autocomplete="off"></div>
                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                  <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                </div>
                <div class="dropdown-list multiselect company-list">
                  @foreach ($companies as $co)
                    <div data-value="{{ $co->company_name }}" data-id="{{ $co->id }}" @class(['selected' => in_array((int) $co->id, $selCompanyIds, true)])>{{ $co->company_name }}</div>
                  @endforeach
                </div>
              </div>
            </div>

            <div class="qd-filter-group tax-dropdown-wrapper zone-section pay-pr-dd" data-filter-param="zone_id" data-empty-label="All zones">
              <label>Zone</label>
              <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Zone" value="{{ $zoneDisp }}" readonly autocomplete="off">
              <div class="dropdown-menu tax-dropdown pay-ap-tax-dd">
                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search zone…" autocomplete="off"></div>
                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                  <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                </div>
                <div class="dropdown-list multiselect zone-list">
                  @foreach ($zones as $z)
                    <div data-value="{{ $z->name }}" data-id="{{ $z->id }}" @class(['selected' => in_array((int) $z->id, $selZoneIds, true)])>{{ $z->name }}</div>
                  @endforeach
                </div>
              </div>
            </div>

            <div class="qd-filter-group tax-dropdown-wrapper branch-section pay-pr-dd" data-filter-param="branch_id" data-empty-label="All branches">
              <label>Branch</label>
              <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Branch" value="{{ $branchDisp }}" readonly autocomplete="off">
              <div class="dropdown-menu tax-dropdown pay-ap-tax-dd">
                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search branch…" autocomplete="off"></div>
                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                  <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                </div>
                <div class="dropdown-list multiselect branch-list">
                  @foreach ($branches as $b)
                    <div data-value="{{ $b->name }}" data-id="{{ $b->id }}" @class(['selected' => in_array((int) $b->id, $selBranchIds, true)])>{{ $b->name }}</div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          <div class="qd-filter-row pay-pr-qd-filter-row pay-pr-qd-filter-row--second">
            <div class="qd-filter-group tax-dropdown-wrapper pay-pr-dd" data-filter-param="payment_type" data-empty-label="All payment types">
              <label>Payment type</label>
              <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Payment Type" value="{{ $typeDisp }}" readonly autocomplete="off">
              <div class="dropdown-menu tax-dropdown pay-ap-tax-dd">
                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search…" autocomplete="off"></div>
                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                  <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                </div>
                <div class="dropdown-list multiselect pay-pr-type-list">
                  @foreach ($paymentTypeLabels as $typeKey => $typeLabel)
                    <div data-value="{{ $typeLabel }}" data-id="{{ $typeKey }}" @class(['selected' => in_array($typeKey, $selPaymentTypes, true)])>{{ $typeLabel }}</div>
                  @endforeach
                </div>
              </div>
            </div>

            <div class="qd-filter-group tax-dropdown-wrapper pay-pr-dd" data-filter-param="vendor_id" data-empty-label="All vendors">
              <label>Vendor</label>
              <input type="text" class="form-control pay-pr-dd-input dropdown-search-input" placeholder="Select Vendor" value="{{ $vendorDisp }}" readonly autocomplete="off">
              <div class="dropdown-menu tax-dropdown pay-ap-tax-dd">
                <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search vendor…" autocomplete="off"></div>
                <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
                  <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
                </div>
                <div class="dropdown-list multiselect vendor-list">
                  @foreach ($vendors as $v)
                    @php $vLabel = trim((string) (($v->display_name ?: '') ?: ($v->company_name ?? ''))); @endphp
                    <div data-value="{{ $vLabel !== '' ? $vLabel : 'Vendor #'.$v->id }}" data-id="{{ $v->id }}" @class(['selected' => in_array((int) $v->id, $selVendorIds, true)])>{{ $vLabel !== '' ? $vLabel : 'Vendor #'.$v->id }}</div>
                  @endforeach
                </div>
              </div>
            </div>

            <div class="qd-filter-group pay-pr-qd-filter-spacer d-none d-lg-block" aria-hidden="true"></div>
            <div class="qd-filter-group pay-pr-qd-filter-spacer d-none d-lg-block" aria-hidden="true"></div>
          </div>
        </div>

        @if($payApHasFilterChips)
          <div class="qd-applied-bar pay-pr-applied-bar">
            <span class="applied-label">Filters:</span>
            <div class="pay-pr-filter-chips d-flex flex-wrap align-items-center" style="gap:6px;flex:1;min-width:0;">
              @if($df && $dt)
                <a href="{{ $payApChipUrl(['date_from', 'date_to']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                  <i class="bi bi-calendar3" aria-hidden="true"></i><span>{{ $dateLabel }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                </a>
              @endif
              @if($selCompanyIds !== [])
                <a href="{{ $payApChipUrl(['company_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                  <span>{{ $companyDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                </a>
              @endif
              @if($selZoneIds !== [])
                <a href="{{ $payApChipUrl(['zone_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                  <span>{{ $zoneDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                </a>
              @endif
              @if($selBranchIds !== [])
                <a href="{{ $payApChipUrl(['branch_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                  <span>{{ $branchDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                </a>
              @endif
              @if($selPaymentTypes !== [])
                <a href="{{ $payApChipUrl(['payment_type']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                  <span>{{ $typeDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                </a>
              @endif
              @if($selVendorIds !== [])
                <a href="{{ $payApChipUrl(['vendor_id']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                  <span>{{ $vendorDisp }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                </a>
              @endif
              @if($payApSearchTrim !== '')
                <a href="{{ $payApChipUrl(['universal_search']) }}" class="filter-badge text-decoration-none text-white d-inline-flex align-items-center gap-1">
                  <i class="bi bi-search" aria-hidden="true"></i><span>{{ \Illuminate\Support\Str::limit($payApSearchTrim, 48) }}</span><span class="remove-icon" aria-hidden="true">&times;</span>
                </a>
              @endif
            </div>
            <a href="{{ route('superadmin.approved-payments.index') }}" class="filter-badge filter-clear text-decoration-none ms-auto">Clear all</a>
          </div>
        @endif

        <div class="row align-items-end justify-content-between pay-pr-table-toolbar mt-3 g-2 g-md-3 mx-0 w-100">
          <div class="col-12 col-md-4 pay-pr-table-toolbar-search">
            <div class="qd-search-row pay-pr-toolbar-search-row mb-0">
              <div class="qd-search-wrap pay-pr-toolbar-search-wrap w-100">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="text"
                  name="universal_search"
                  id="pay_ap_universal_search"
                  value="{{ request('universal_search') }}"
                  maxlength="200"
                  placeholder="Search approved requests…"
                  autocomplete="off">
              </div>
            </div>
          </div>
          <div class="col-12 col-md-auto pay-pr-per-page-field pay-pr-per-page-field--toolbar">
            <label class="form-label pay-pr-per-page-label mb-1 d-block text-md-end" for="pay-ap-per-page">Rows per page</label>
            <select id="pay-ap-per-page" name="per_page" class="form-select form-select-sm pay-pr-per-page-select" autocomplete="off" aria-label="Rows per page">
              @foreach ($pr_per_page_choices ?? [10, 15, 25, 50, 100] as $pp)
                <option value="{{ $pp }}" @selected((int) ($pr_per_page ?? 25) === (int) $pp)>{{ $pp }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </form>
    </div>

    <div class="tk-table-card pay-pr-table-card">
      <div class="qdt-wrap pay-pr-qdt-wrap">
        <table class="qdt-table tk-pr-table pay-pr-data-table pay-ap-data-table">
          <thead class="qdt-head pay-pr-thead">
            <tr>
              <th scope="col" class="pay-pr-th-num">Request No.#</th>
              <th scope="col" class="pay-pr-th-date">Submitted on</th>
              <th scope="col" class="pay-pr-th-date">Approved on</th>
              <th scope="col" class="pay-pr-th-type">Type</th>
              <th scope="col" class="pay-pr-th-loc">Location</th>
              <th scope="col" class="text-end pay-pr-th-amt">Amount</th>
              <th scope="col" class="pay-pr-th-vendor">Vendor</th>
              <th scope="col" class="pay-pr-th-by">Requested by</th>
              <th scope="col" class="pay-pr-th-by">Approved by</th>
              <th scope="col" class="pay-pr-th-by">Status</th>
              <th scope="col" class="pay-pr-th-bill-pay">Bill payment</th>
              <th scope="col" class="text-center pay-pr-th-generate-bill">Generate bill</th>
              <th scope="col" class="text-center pay-pr-th-action">Action</th>
            </tr>
          </thead>
          <tbody class="pay-pr-tbody">
            @forelse($rows as $r)
              @php $billPay = $r->billDisbursementState(); @endphp
              <tr class="qdt-row pay-pr-row">
                <td class="text-nowrap pay-pr-td-num">
                  <a class="tk-pr-num-link pay-pr-ref" href="{{ route('superadmin.payment-requests.show', ['paymentRequest' => $r, 'from' => 'approved-payments']) }}">{{ $r->request_no }}</a>
                </td>
                <td class="pay-pr-td-date text-nowrap">
                  <time datetime="{{ $r->created_at->toIso8601String() }}">{{ $r->created_at->format('d M Y') }}</time>
                </td>
                <td class="pay-pr-td-date text-nowrap">
                  @if($r->reviewed_at)
                    <time datetime="{{ $r->reviewed_at->toIso8601String() }}">{{ $r->reviewed_at->format('d M Y') }}</time>
                  @else
                    <span class="pay-pr-dash">—</span>
                  @endif
                </td>
                <td class="pay-pr-td-type">
                  <span class="pay-pr-type-pill pay-pr-type-pill--{{ $r->payment_type }}" title="{{ \App\Models\PaymentRequest::typeLabel($r->payment_type) }}">
                    {{ \App\Models\PaymentRequest::typeLabel($r->payment_type) }}
                  </span>
                </td>
                <td class="align-middle pay-pr-loc-cell pay-pr-col-loc">
                  @if($r->zone)
                    <div class="pay-pr-loc-zone">
                      <span class="pay-pr-zone-pill">{{ strtoupper($r->zone->name) }}</span>
                    </div>
                  @endif
                  @if($r->branch)
                    <div class="pay-pr-branch-txt text-nowrap">{{ $r->branch->name }}</div>
                  @endif
                  @if(!$r->zone && !$r->branch)
                    <span class="pay-pr-dash">—</span>
                  @endif
                </td>
                <td class="text-end text-nowrap pay-pr-td-amt">
                  <span class="pay-pr-amount"><span class="pay-pr-amount-curr">₹</span>{{ $fmtMoney((float) $r->amount) }}</span>
                </td>
                <td class="pay-pr-td-vendor">
                  @php $vDisp = trim((string) (($r->sourceVendor?->display_name ?: '') ?: ($r->sourceVendor?->company_name ?? ''))); @endphp
                  @if($vDisp !== '')
                    <span class="pay-pr-vendor-name" title="{{ $vDisp }}">{{ $vDisp }}</span>
                  @elseif($r->vendor_id)
                    <span class="pay-pr-vendor-name" title="Vendor #{{ $r->vendor_id }}">Vendor #{{ $r->vendor_id }}</span>
                  @else
                    <span class="pay-pr-dash">—</span>
                  @endif
                </td>
                <td class="pay-pr-td-requester">
                  {{ $r->creator?->user_fullname ?? '—' }}
                </td>
                <td class="pay-pr-td-requester">
                  {{ $r->reviewer?->user_fullname ?? '—' }}
                </td>
                <td class="pay-pr-td-status">
                  <span class="pay-pr-status-pill pay-pr-status-pill--{{ $r->status }}" title="{{ \App\Models\PaymentRequest::statusLabel($r->status) }}">
                    {{ \App\Models\PaymentRequest::statusLabel($r->status) }}
                  </span>
                </td>
                <td class="pay-pr-td-bill-pay align-middle">
                  <span class="pay-pr-bill-pay-pill {{ \App\Models\PaymentRequest::billDisbursementPillClass($billPay) }} px-2 py-1 fw-semibold" style="font-size: 0.72rem;">
                    {{ \App\Models\PaymentRequest::billDisbursementLabel($billPay) }}
                  </span>
                </td>
                <td class="text-center text-nowrap pay-pr-td-generate-bill align-middle">
                  @if($billPay === \App\Models\PaymentRequest::BILL_DISBURSE_NONE)
                    <a class="pay-pr-btn-generate-bill" href="{{ route('superadmin.getbillcreate', ['payment_request_id' => $r->id]) }}" aria-label="Generate bill from this payment request">
                      <span class="pay-pr-btn-generate-bill-icon-wrap" aria-hidden="true">
                        <i class="bi bi-file-earmark-text pay-pr-btn-generate-bill-icon"></i>
                      </span>
                      <span class="pay-pr-btn-generate-bill-label">Generate bill</span>
                    </a>
                  @else
                    <span class="pay-pr-dash" title="A bill is already linked to this payment request">—</span>
                  @endif
                </td>
                <td class="text-center pay-pr-td-action">
                  <div class="pay-pr-action-group" role="group" aria-label="Row actions">
                    <a class="pay-pr-btn-view" href="{{ route('superadmin.payment-requests.show', ['paymentRequest' => $r, 'from' => 'approved-payments']) }}">
                      <span class="pay-pr-btn-view-ic" aria-hidden="true"><i class="bi bi-eye"></i></span>
                      <span>View</span>
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="13" class="text-center pay-pr-empty">
                  <i class="bi bi-inbox d-block pay-pr-empty-icon" aria-hidden="true"></i>
                  <div class="pay-pr-empty-title mt-3">No approved payments to display</div>
                  <p class="pay-pr-empty-hint mb-0">When a payment request is approved, it will appear here. You can also open all requests to review pending items.</p>
                  <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                    <a href="{{ route('superadmin.payment-requests.index') }}" class="tk-btn-export text-decoration-none d-inline-flex align-items-center gap-1">
                      <i class="bi bi-list-ul" aria-hidden="true"></i> All payment requests
                    </a>
                    <a href="{{ route('superadmin.payment-requests.create') }}" class="tk-btn-raise d-inline-flex">
                      <i class="bi bi-plus-lg" aria-hidden="true"></i> New request
                    </a>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($rows->total() > 0)
      <div class="pay-pr-pagination-bar">
        @if($rows->hasPages())
          <div class="pay-pr-pag-nav pay-pr-pag-nav--full">
            {{ $rows->links('vendor.pagination.bootstrap-5') }}
          </div>
        @else
          <p class="pay-pr-pag-summary mb-0 px-1">
            @if($rows->firstItem())
              Showing <strong>{{ $rows->firstItem() }}</strong> to <strong>{{ $rows->lastItem() }}</strong> of <strong>{{ $rows->total() }}</strong> results
            @else
              Showing <strong>0</strong> of <strong>{{ $rows->total() }}</strong> results
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

@include('superadmin.superadminfooter')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
(function() {
  if (typeof toastr === 'undefined') return;
  toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 3500 };
  @if(session('success')) toastr.success(@json(session('success'))); @endif
  @if(session('error')) toastr.error(@json(session('error'))); @endif
})();
</script>
<script>
(function($) {
  var $form = $('#pay-ap-filter-form');
  if (!$form.length) return;

  var payApFilterSubmitTimer = null;
  function submitPayApFilterNow() {
    if (payApFilterSubmitTimer) {
      clearTimeout(payApFilterSubmitTimer);
      payApFilterSubmitTimer = null;
    }
    var el = $form[0];
    if (el) el.submit();
  }
  function schedulePayApFilterSubmit() {
    if (payApFilterSubmitTimer) clearTimeout(payApFilterSubmitTimer);
    payApFilterSubmitTimer = setTimeout(function() {
      payApFilterSubmitTimer = null;
      submitPayApFilterNow();
    }, 380);
  }

  function syncPayApArray(paramName, ids) {
    var $box = $form.find('.pay-pr-array-hiddens[data-array-name="' + paramName + '"]');
    $box.empty();
    $.each(ids, function(_, id) {
      if (id === '' || id === null || id === undefined) return;
      $box.append($('<input>', { type: 'hidden', name: paramName + '[]', value: String(id) }));
    });
  }

  function updatePayApDropdown($dropdown) {
    var $wrapper = $dropdown.data('wrapper');
    if (!$wrapper || !$wrapper.length) return;
    var param = $wrapper.data('filter-param');
    var emptyLbl = $wrapper.data('empty-label') || 'All';
    var texts = [];
    var ids = [];
    $dropdown.find('.dropdown-list.multiselect div').each(function() {
      if (!$(this).hasClass('selected')) return;
      texts.push($(this).text().trim());
      ids.push($(this).attr('data-id'));
    });
    $wrapper.find('.pay-pr-dd-input').val(texts.length ? texts.join(', ') : emptyLbl);
    syncPayApArray(param, ids);
    schedulePayApFilterSubmit();
  }

  function positionPayApFilterDd($input, $dropdown) {
    var el = $input[0];
    if (!el || !$dropdown || !$dropdown.length) return;
    var r = el.getBoundingClientRect();
    var w = Math.max(r.width, 260);
    var vw = window.innerWidth || document.documentElement.clientWidth || 0;
    var left = r.left;
    if (left + w > vw - 8) {
      left = Math.max(8, vw - w - 8);
    }
    $dropdown.css({
      position: 'fixed',
      top: r.bottom + 4,
      left: left,
      width: w,
      zIndex: 10050
    });
  }

  $(window).on('scroll.payApFilterDd resize.payApFilterDd', function() {
    $('.dropdown-menu.tax-dropdown.pay-ap-tax-dd:visible').each(function() {
      var $dd = $(this);
      var $wrap = $dd.data('wrapper');
      if (!$wrap || !$wrap.length) return;
      var $inp = $wrap.find('.pay-pr-dd-input').first();
      if ($inp.length) positionPayApFilterDd($inp, $dd);
    });
  });

  $(document).on('click', '#pay-ap-filter-form .pay-pr-dd-input', function(e) {
    e.stopPropagation();
    $('.dropdown-menu.tax-dropdown.pay-ap-tax-dd').hide();
    var $input = $(this);
    var $dropdown = $input.data('dropdown');
    if (!$dropdown) {
      $dropdown = $input.siblings('.dropdown-menu').clone(true);
      $('body').append($dropdown);
      $input.data('dropdown', $dropdown);
    }
    $dropdown.addClass('pay-ap-tax-dd');
    $dropdown.data('wrapper', $input.closest('.pay-pr-dd'));
    positionPayApFilterDd($input, $dropdown);
    $dropdown.show();
    $dropdown.find('.inner-search').first().val('');
    $dropdown.find('.dropdown-list.multiselect div').show();
    $dropdown.find('.inner-search').first().focus();
  });

  $(document).on('keyup', '.pay-ap-tax-dd .inner-search', function() {
    var q = ($(this).val() || '').toLowerCase();
    $(this).closest('.dropdown-menu').find('.dropdown-list.multiselect div').each(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
    });
  });

  $(document).on('click', '.pay-ap-tax-dd .dropdown-list.multiselect div', function(e) {
    e.stopPropagation();
    $(this).toggleClass('selected');
    updatePayApDropdown($(this).closest('.dropdown-menu.tax-dropdown'));
  });

  $(document).on('click', '.pay-ap-tax-dd .select-all', function(e) {
    e.stopPropagation();
    var $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
    $dropdown.find('.dropdown-list.multiselect div').addClass('selected');
    updatePayApDropdown($dropdown);
  });

  $(document).on('click', '.pay-ap-tax-dd .deselect-all', function(e) {
    e.stopPropagation();
    var $dropdown = $(this).closest('.dropdown-menu.tax-dropdown');
    $dropdown.find('.dropdown-list.multiselect div').removeClass('selected');
    updatePayApDropdown($dropdown);
  });

  $(document).on('click', function(e) {
    if ($(e.target).closest('#pay-ap-filter-form .tax-dropdown-wrapper').length) return;
    if ($(e.target).closest('.dropdown-menu.tax-dropdown.pay-ap-tax-dd').length) return;
    $('.dropdown-menu.tax-dropdown.pay-ap-tax-dd').hide();
  });

  $('#pay_ap_universal_search').on('input', function() {
    schedulePayApFilterSubmit();
  });

  if (typeof $.fn.daterangepicker === 'function' && typeof moment !== 'undefined') {
    var $dr = $('#payApReportRange');
    var df = $('#pay_ap_date_from').val();
    var dt = $('#pay_ap_date_to').val();
    var opts = {
      autoUpdateInput: false,
      locale: { format: 'YYYY-MM-DD', separator: ' – ', cancelLabel: 'Clear', applyLabel: 'Apply' },
      opens: 'left',
      drops: 'down'
    };
    if (df && dt) {
      opts.startDate = moment(df);
      opts.endDate = moment(dt);
    }
    $dr.daterangepicker(opts);
    $dr.on('apply.daterangepicker', function(ev, picker) {
      $('#pay_ap_date_from').val(picker.startDate.format('YYYY-MM-DD'));
      $('#pay_ap_date_to').val(picker.endDate.format('YYYY-MM-DD'));
      $('#payApDateLabel').text(picker.startDate.format('MMM D, YYYY') + ' – ' + picker.endDate.format('MMM D, YYYY'));
      submitPayApFilterNow();
    });
    $dr.on('cancel.daterangepicker', function() {
      $('#pay_ap_date_from').val('');
      $('#pay_ap_date_to').val('');
      $('#payApDateLabel').text('All approval dates');
      submitPayApFilterNow();
    });
  }

  $('#pay-ap-per-page').on('change', function() {
    if (payApFilterSubmitTimer) {
      clearTimeout(payApFilterSubmitTimer);
      payApFilterSubmitTimer = null;
    }
    var el = $form[0];
    if (el) {
      el.submit();
    }
  });
})(jQuery);
</script>

</body>
</html>
