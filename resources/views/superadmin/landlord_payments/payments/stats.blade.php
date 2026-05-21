@php
  $overviewStats = $overviewStats ?? [];
  $activeTab = $activeTab ?? 'all';
  $chartReportScope = $chartReportScope ?? false;
  $enableTaxKpiFilters = $enableTaxKpiFilters ?? ! $chartReportScope;
  $activeTaxFilter = $activeTaxFilter ?? null;
  if (! isset($tabLabel)) {
      $tabLabel = $chartReportScope
          ? ($chartScopeLabel ?? 'All charge types · matching filters')
          : (($paymentsTabLabels ?? [])[$activeTab] ?? 'Summary');
      if ($activeTaxFilter === 'gst') {
          $tabLabel .= ' · with GST';
      } elseif ($activeTaxFilter === 'tds') {
          $tabLabel .= ' · with TDS';
      }
  }
@endphp
<div id="llp-payments-stats" class="llp-payments-stats" data-active-tab="{{ $activeTab }}" data-active-tax-filter="{{ $activeTaxFilter ?? '' }}">
  <div class="llp-pay-kpi-grid" role="region" aria-label="Summary totals for active tab" data-llp-kpi-grid>
    <article class="llp-pay-kpi llp-pay-kpi--records">
      <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-layers"></i></span>
      <div class="llp-pay-kpi__body">
        <span class="llp-pay-kpi__lbl">Records</span>
        <span class="llp-pay-kpi__val">{{ number_format($overviewStats['total_rows'] ?? 0) }}</span>
        <span class="llp-pay-kpi__hint">{{ $tabLabel }} &middot; matching filters</span>
      </div>
    </article>

    @if ($chartReportScope ?? false)
      <article class="llp-pay-kpi llp-pay-kpi--neft llp-pay-kpi--featured">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-bank2"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Payable / paid</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['final_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint">Rent, advance, maintenance &amp; EB</span>
        </div>
      </article>
    @elseif ($activeTab === 'all')
      <article class="llp-pay-kpi llp-pay-kpi--neft llp-pay-kpi--featured">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-bank2"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Payable / paid</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['final_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint">All charge types</span>
        </div>
      </article>
    @elseif ($activeTab === 'rent_expense')
      <article class="llp-pay-kpi llp-pay-kpi--rent llp-pay-kpi--featured">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-building"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Sub total</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['sub_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint">Rent expense lines</span>
        </div>
      </article>
    @elseif ($activeTab === 'rent_advance')
      <article class="llp-pay-kpi llp-pay-kpi--paid llp-pay-kpi--featured">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-wallet2"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Amount paid</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['paid_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint">Advance paid via bills</span>
        </div>
      </article>
    @elseif ($activeTab === 'maintenance')
      <article class="llp-pay-kpi llp-pay-kpi--maintenance llp-pay-kpi--featured">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-tools"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Sub total</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['sub_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint">Maintenance charges</span>
        </div>
      </article>
    @elseif ($activeTab === 'eb_bill')
      <article class="llp-pay-kpi llp-pay-kpi--eb llp-pay-kpi--featured">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-lightning-charge-fill"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Sub total</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['sub_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint">Electricity / EB</span>
        </div>
      </article>
    @endif

    <article class="llp-pay-kpi llp-pay-kpi--gross">
      <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-receipt"></i></span>
      <div class="llp-pay-kpi__body">
        <span class="llp-pay-kpi__lbl">Gross amount</span>
        <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['gross_total'] ?? 0, 2) }}</span>
        <span class="llp-pay-kpi__hint">Before deductions</span>
      </div>
    </article>

    @if ($enableTaxKpiFilters)
      <button type="button"
        class="llp-pay-kpi llp-pay-kpi--gst llp-pay-kpi--filter {{ $activeTaxFilter === 'gst' ? 'is-active' : '' }}"
        data-llp-tax-filter="gst"
        aria-pressed="{{ $activeTaxFilter === 'gst' ? 'true' : 'false' }}"
        title="Show rows with GST only (click again to clear)">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-percent"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Total GST</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['gst_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint llp-pay-kpi__hint--tax">
            <span class="llp-pay-kpi__tax-split">
              <span>SGST &#8377;{{ number_format($overviewStats['sgst_total'] ?? 0, 2) }}</span>
              <span>CGST &#8377;{{ number_format($overviewStats['cgst_total'] ?? 0, 2) }}</span>
              <span>IGST &#8377;{{ number_format($overviewStats['igst_total'] ?? 0, 2) }}</span>
            </span>
            <span class="llp-pay-kpi__tax-action">{{ $activeTaxFilter === 'gst' ? 'Filter on &middot; click to clear' : 'Click to filter' }}</span>
          </span>
        </div>
      </button>

      <button type="button"
        class="llp-pay-kpi llp-pay-kpi--tds llp-pay-kpi--filter {{ $activeTaxFilter === 'tds' ? 'is-active' : '' }}"
        data-llp-tax-filter="tds"
        aria-pressed="{{ $activeTaxFilter === 'tds' ? 'true' : 'false' }}"
        title="Show rows with TDS only (click again to clear)">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-file-earmark-minus"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Total TDS</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['tds_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint llp-pay-kpi__hint--tax">
            <span class="llp-pay-kpi__tax-action">Tax deducted at source</span>
            <span class="llp-pay-kpi__tax-action">{{ $activeTaxFilter === 'tds' ? 'Filter on &middot; click to clear' : 'Click to filter' }}</span>
          </span>
        </div>
      </button>
    @else
      <article class="llp-pay-kpi llp-pay-kpi--gst">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-percent"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Total GST</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['gst_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint llp-pay-kpi__hint--tax">
            <span class="llp-pay-kpi__tax-split">
              <span>SGST &#8377;{{ number_format($overviewStats['sgst_total'] ?? 0, 2) }}</span>
              <span>CGST &#8377;{{ number_format($overviewStats['cgst_total'] ?? 0, 2) }}</span>
              <span>IGST &#8377;{{ number_format($overviewStats['igst_total'] ?? 0, 2) }}</span>
            </span>
          </span>
        </div>
      </article>

      <article class="llp-pay-kpi llp-pay-kpi--tds">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-file-earmark-minus"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Total TDS</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['tds_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint">Tax deducted at source</span>
        </div>
      </article>
    @endif

    @if ($activeTab !== 'all' && ! ($chartReportScope ?? false))
      <article class="llp-pay-kpi llp-pay-kpi--neft">
        <span class="llp-pay-kpi__icon" aria-hidden="true"><i class="bi bi-bank2"></i></span>
        <div class="llp-pay-kpi__body">
          <span class="llp-pay-kpi__lbl">Payable / paid</span>
          <span class="llp-pay-kpi__val">&#8377;{{ number_format($overviewStats['final_total'] ?? 0, 2) }}</span>
          <span class="llp-pay-kpi__hint">NEFT or outstanding</span>
        </div>
      </article>
    @endif
  </div>
</div>
