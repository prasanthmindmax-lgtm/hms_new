@php
  $chartData = $chartData ?? ['statusBreakdown' => ['paid' => 0, 'pending' => 0], 'statusLabels' => [], 'topLandlords' => []];
@endphp
<div class="ra-chart-layout llp-payments-chart-layout" id="llpChartLayout">
  <aside class="ra-chart-panel d-none" id="llpChartPanel" aria-label="Rental payments chart">
    <div class="ra-chart-panel-head">
      <span><i class="bi bi-bar-chart-line me-1" aria-hidden="true"></i>Chart report</span>
      <span class="ra-chart-panel-sub text-muted">Active tab · matching filters</span>
    </div>
    <div class="ra-chart-canvas-wrap">
      <canvas id="llpStatusChart" height="220" aria-label="Paid vs pending amounts"></canvas>
    </div>
    <div class="ra-chart-canvas-wrap ra-chart-canvas-wrap--sm mt-3">
      <canvas id="llpTopLandlordsChart" height="200" aria-label="Top landlords by payable amount"></canvas>
    </div>
  </aside>

  <div class="llp-payments-register-main ra-register-main">
