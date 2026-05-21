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
<link rel="stylesheet" href="{{ asset('assets/css/landlord_payments.css') }}">
@stack('styles')
<body class="llp-page @stack('body-class')" style="overflow-x: hidden;">
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
      @yield('content')
    </div>
  </div>
</div>

@include('superadmin.superadminfooter')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('assets/js/form_field_validation.js') }}?v={{ @filemtime(public_path('assets/js/form_field_validation.js')) }}"></script>
@stack('scripts')
</body>
</html>
