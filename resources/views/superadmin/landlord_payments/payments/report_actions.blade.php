@php
  $showChart = $showChart ?? true;
  $showBack = $showBack ?? false;
  $backUrl = $backUrl ?? null;
@endphp
<div class="llp-payments-card__toolbar llp-report-actions" role="group" aria-label="Reports and export">
  <span class="llp-report-actions__label">
    <i class="bi bi-download" aria-hidden="true"></i> Reports
  </span>
  <div class="llp-report-actions__strip">
    @if ($showBack && !empty($backUrl))
      <a href="{{ $backUrl }}" class="llp-report-btn llp-report-btn--back text-decoration-none" data-llp-action="back" title="Return to rental payments list">
        <span class="llp-report-btn__ic" aria-hidden="true"><i class="bi bi-table"></i></span>
        <span class="llp-report-btn__txt">
          <span class="llp-report-btn__title">Back to list</span>
          <span class="llp-report-btn__hint">Table view</span>
        </span>
      </a>
    @endif
    @if ($showChart && !empty($chartReportUrl))
      <a href="{{ $chartReportUrl }}" class="llp-report-btn llp-report-btn--chart text-decoration-none" data-llp-action="chart" title="Open chart report for filtered data">
        <span class="llp-report-btn__ic" aria-hidden="true"><i class="bi bi-bar-chart-line"></i></span>
        <span class="llp-report-btn__txt">
          <span class="llp-report-btn__title">Chart report</span>
          <span class="llp-report-btn__hint">Visual summary</span>
        </span>
      </a>
    @endif
    @if (!empty($exportExcelUrl))
      <a href="{{ $exportExcelUrl }}" class="llp-report-btn llp-report-btn--excel text-decoration-none" data-llp-action="excel" title="Export filtered rows to Excel">
        <span class="llp-report-btn__ic llp-report-btn__ic--excel" aria-hidden="true"><i class="bi bi-file-earmark-excel"></i></span>
        <span class="llp-report-btn__txt">
          <span class="llp-report-btn__title">Excel</span>
          <span class="llp-report-btn__hint">.xlsx download</span>
        </span>
      </a>
    @endif
    @if (!empty($exportCsvUrl))
      <a href="{{ $exportCsvUrl }}" class="llp-report-btn llp-report-btn--csv text-decoration-none" data-llp-action="csv" title="Export filtered rows to CSV">
        <span class="llp-report-btn__ic llp-report-btn__ic--csv" aria-hidden="true"><i class="bi bi-filetype-csv"></i></span>
        <span class="llp-report-btn__txt">
          <span class="llp-report-btn__title">CSV</span>
          <span class="llp-report-btn__hint">Spreadsheet file</span>
        </span>
      </a>
    @endif
  </div>
</div>
