@extends('superadmin.landlord_payments.layout')

@push('body-class')
ra-page llp-payments-page llp-chart-report-page
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/payment_request.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/rental_agreement.css') }}?v={{ @filemtime(public_path('assets/css/rental_agreement.css')) }}" />
@endpush

@section('content')
@php
  $activeTab = $activeTab ?? 'rent_expense';
  $chartData = $chartData ?? [];
  $backUrl = route('rental-payments', array_merge(
      ['segment' => $agreementType ?? 'hospital', 'tab' => $activeTab],
      request()->except(['page'])
  ));
@endphp

<div class="llp-payments-workspace llp-chart-report-workspace">
  <div class="llp-payments-card">
    <header class="llp-payments-card__head llp-chart-report-head">
      <div class="llp-payments-card__head-top">
        <div class="llp-payments-card__intro">
          <h1 class="llp-payments-card__title">Rental payments â€” Chart report</h1>
          <p class="llp-payments-card__meta mb-0">
            {{ $chartData['activeTabLabel'] ?? ($chartScopeLabel ?? 'All charge types') }}
            <span class="llp-payments-card__dot">Â·</span>
            {{ number_format($chartData['recordCount'] ?? 0) }} records
            <span class="llp-payments-card__dot">Â·</span>
            {{ $dateLabel ?? 'All dates' }}
          </p>
        </div>
        @include('superadmin.landlord_payments.payments.report_actions', [
          'showChart' => false,
          'showBack' => true,
          'backUrl' => $backUrl,
        ])
      </div>
    </header>

    @include('superadmin.landlord_payments.payments.stats')

    @if ($hasFilterChips ?? false)
      <div class="llp-chart-report-filters px-3 py-2 border-bottom bg-light">
        <span class="small text-muted fw-semibold me-2">Filters applied:</span>
        @if (!empty($dateFrom) && !empty($dateTo))
          <span class="badge bg-secondary-subtle text-dark border">{{ $dateLabel }}</span>
        @endif
        @if (!empty($companyDisp) && ($companyDisp ?? '') !== 'All companies')
          <span class="badge bg-secondary-subtle text-dark border">{{ $companyDisp }}</span>
        @endif
        @if (!empty($zoneDisp) && ($zoneDisp ?? '') !== 'All zones')
          <span class="badge bg-secondary-subtle text-dark border">{{ $zoneDisp }}</span>
        @endif
        @if (!empty($vendorDisp) && ($vendorDisp ?? '') !== 'All vendors')
          <span class="badge bg-secondary-subtle text-dark border">Vendor: {{ $vendorDisp }}</span>
        @endif
        <a href="{{ $clearReportFiltersUrl ?? $backUrl }}" class="small ms-2">Change filters</a>
      </div>
    @endif

    <div class="llp-chart-report-body p-3 p-md-4">
      <div class="llp-chart-report-grid">
        <article class="llp-chart-report-card">
          <h2 class="llp-chart-report-card__title">Payable by status</h2>
          <div class="llp-chart-report-card__canvas">
            <canvas id="llpStatusChart" height="260" aria-label="Paid vs pending amounts"></canvas>
          </div>
        </article>

        <article class="llp-chart-report-card">
          <h2 class="llp-chart-report-card__title">By charge type</h2>
          <div class="llp-chart-report-card__canvas">
            <canvas id="llpNatureChart" height="260" aria-label="Rent expense, advance, maintenance, and EB"></canvas>
          </div>
        </article>

        <article class="llp-chart-report-card">
          <h2 class="llp-chart-report-card__title">Top landlords by final NEFT</h2>
          <div class="llp-chart-report-card__canvas">
            <canvas id="llpTopLandlordsChart" height="260" aria-label="Top landlords by payable amount"></canvas>
          </div>
        </article>

        <article class="llp-chart-report-card">
          <h2 class="llp-chart-report-card__title">GST split (SGST / CGST / IGST)</h2>
          <div class="llp-chart-report-card__canvas">
            <canvas id="llpGstSplitChart" height="260" aria-label="GST component breakdown"></canvas>
          </div>
        </article>

        <article class="llp-chart-report-card">
          <h2 class="llp-chart-report-card__title">Amount breakdown</h2>
          <div class="llp-chart-report-card__canvas">
            <canvas id="llpFinancialTotalsChart" height="260" aria-label="Sub total, gross, GST, TDS, and final NEFT"></canvas>
          </div>
        </article>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script id="llpChartData" type="application/json">@json($chartData)</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('assets/js/rental_agreement.js') }}?v={{ @filemtime(public_path('assets/js/rental_agreement.js')) }}"></script>
@endpush
