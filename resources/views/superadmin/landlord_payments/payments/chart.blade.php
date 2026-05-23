<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/payment_request.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/landlord_payments.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/rental_agreement.css') }}?v={{ @filemtime(public_path('assets/css/rental_agreement.css')) }}" />
<body class="llp-page ra-page llp-payments-page llp-chart-report-page" style="overflow-x: hidden;">
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')
@if (session('success'))
  <script>document.addEventListener('DOMContentLoaded',function(){if(window.toastr)toastr.success(@json(session('success')));});</script>
@endif
@if ($errors->any())
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var errs = @json($errors->all());
      if (window.FormFieldValidation) {
        FormFieldValidation.showBackendToasts(errs, {
          summary: errs.length > 1 ? 'Please correct the highlighted fields.' : '',
        });
      } else if (window.toastr) {
        errs.forEach(function (m, i) { setTimeout(function () { toastr.error(m); }, i * 120); });
      }
    });
  </script>
@endif

<div class="pc-container">
  <div class="pc-content">
    <div class="container-fluid llp-wrap">
      @php
        $at = $agreementType ?? null;
        $llpPaymentsActive = request()->routeIs(
            'rental-payments',
            'rental-payments.export',
            'rental-payments.charts'
        );
        $llpRentalHubActive = request()->routeIs('rental-agreements.*');
      @endphp
      <div class="card border-0 shadow-sm mb-3 llp-subnav">
        <div class="llp-subnav-accent" aria-hidden="true"></div>
        <div class="card-body llp-subnav-body py-3 px-3 px-lg-4 d-flex flex-column flex-lg-row flex-wrap align-items-stretch align-lg-items-center gap-3 gap-lg-4 justify-content-between">
          <div class="llp-subnav-brand">
            <span class="llp-subnav-brand-ic" aria-hidden="true"><i class="bi bi-buildings"></i></span>
            <div class="llp-subnav-brand-txt">
              <strong>Landlord payments</strong>
              @if (! $at)
                <span>Rental agreements &amp; payments</span>
              @elseif (! $llpPaymentsActive)
                <span>{{ \App\Models\RentalAgreement::typeLabel($at) }}</span>
              @endif
            </div>
          </div>
          <div class="llp-subnav-links">
            <nav class="llp-subnav-pillstrip" aria-label="Landlord payments primary">
              <a href="{{ route('rental-agreements.index') }}"
                class="llp-nav-link llp-nav-pill {{ $llpRentalHubActive ? 'llp-nav-link--active' : '' }}"
                @if ($llpRentalHubActive) aria-current="page" @endif>
                <i class="bi bi-journal-bookmark" aria-hidden="true"></i> Rental agreement
              </a>
              <a href="{{ route('rental-payments', ['segment' => $at ?? 'hospital']) }}"
                class="llp-nav-link llp-nav-pill {{ $llpPaymentsActive ? 'llp-nav-link--active' : '' }}"
                @if ($llpPaymentsActive) aria-current="page" @endif>
                <i class="bi bi-currency-rupee" aria-hidden="true"></i> Rental payments
              </a>
            </nav>
          </div>
        </div>
      </div>
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
          <h1 class="llp-payments-card__title">Rental payments — Chart report</h1>
          <p class="llp-payments-card__meta mb-0">
            {{ $chartData['activeTabLabel'] ?? ($chartScopeLabel ?? 'All charge types') }}
            <span class="llp-payments-card__dot">·</span>
            {{ number_format($chartData['recordCount'] ?? 0) }} records
            <span class="llp-payments-card__dot">·</span>
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
    </div>
  </div>
</div>

@include('superadmin.superadminfooter')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('assets/js/form_field_validation.js') }}?v={{ @filemtime(public_path('assets/js/form_field_validation.js')) }}"></script>
<script id="llpChartData" type="application/json">@json($chartData)</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('assets/js/rental_agreement.js') }}?v={{ @filemtime(public_path('assets/js/rental_agreement.js')) }}"></script>
</body>
</html>
