@php
  use App\Services\LandlordAdvanceVendorDashboard;

  $paymentsScope = $paymentsScope ?? ($history['scope'] ?? 'agreement');
  $isVendorScope = $paymentsScope === 'vendor';
  $r = $record;
  $summary = $summary ?? ($history['summary'] ?? []);
  $sections = $sections ?? ($history['sections'] ?? []);
  $rows = $rows ?? ($history['rows'] ?? []);
  $agreementsList = $agreementsList ?? ($history['agreements'] ?? []);
  $locationLabel = $r
      ? trim(collect([$r->zone?->name, $r->branch?->name])->filter()->implode(' - '))
      : '';
  $segment = $r
      ? \App\Models\RentalAgreement::normalizeType((string) $r->agreement_type)
      : (isset($agreementsList[0]['agreement_type'])
          ? $agreementsList[0]['agreement_type']
          : ($agreementType ?? \App\Models\RentalAgreement::TYPE_HOSPITAL));
  $ownerTitle = $history['owner_name'] ?? ($r?->owner_name ?? ($vendor?->display_name ?? 'Owner'));
  $dataSource = $history['data_source'] ?? 'landlord_module';
  $isBillModule = $dataSource === 'bill_module';
  $categoryQs = array_filter(['category' => request()->query('category')]);
  $agreementCount = (int) ($summary['agreement_count'] ?? count($agreementsList));
  $vendorBills = $summary['vendor_bills'] ?? null;
  $rentLedger = $summary['rent_ledger'] ?? [];
  $advancePending = (float) ($summary['advance_balance'] ?? 0);
  $maintenancePending = (float) ($summary['maintenance_pending'] ?? 0);
  $rentPendingLedger = (float) ($summary['rent_expense_pending'] ?? ($rentLedger['pending_total'] ?? 0));
  $billCount = (int) ($rentLedger['bill_count'] ?? ($vendorBills['bill_count'] ?? 0));
  $billGross = (float) ($rentLedger['bill_gross_total'] ?? ($vendorBills['bill_gross_total'] ?? 0));
  $billPaid = (float) ($rentLedger['bill_paid_total'] ?? ($vendorBills['bill_paid_total'] ?? 0));
  $billDue = (float) ($rentLedger['pending_total'] ?? $rentPendingLedger);
  $paymentCount = (int) ($vendorBills['payment_count'] ?? 0);
  $vendorBillDue = (float) ($vendorBills['bill_due_total'] ?? 0);
  $rentPending = $rentPendingLedger;
  $totalPending = (float) ($summary['total_pending'] ?? ($rentPending + $advancePending + $maintenancePending));

  $ledgerSections = $sections;
  if ($ledgerSections === [] && $rows !== []) {
      $ledgerSections = LandlordAdvanceVendorDashboard::buildLedgerSections($rows);
  }

  $rentExpenseSectionSummary = $ledgerSections[LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES]['summary'] ?? [];
  $rentTotalAmount = $billGross > 0.009
      ? $billGross
      : round((float) ($rentExpenseSectionSummary['amount_sent'] ?? 0) + (float) ($rentExpenseSectionSummary['pending_balance'] ?? 0), 2);
  $rentPaidAmount = ($billGross > 0.009 || $billPaid > 0.009)
      ? $billPaid
      : (float) ($rentExpenseSectionSummary['amount_sent'] ?? 0);
  $rentBalanceAmount = $billGross > 0.009
      ? $billDue
      : (float) ($rentExpenseSectionSummary['pending_balance'] ?? $rentPending);

  $sectionMeta = function (string $key) use ($ledgerSections): array {
      $section = $ledgerSections[$key] ?? null;
      $sectionRows = $section['rows'] ?? [];
      $sectionSummary = $section['summary'] ?? [];
      $lineCount = (int) ($sectionSummary['line_count'] ?? count($sectionRows));
      $firstDue = collect($sectionRows)->first(fn ($row) => (float) ($row['pending_balance'] ?? 0) > 0.009);
      $nextDue = $firstDue['payment_month'] ?? null;

      return [
          'line_count' => $lineCount,
          'pending' => (float) ($sectionSummary['pending_balance'] ?? 0),
          'next_due' => $nextDue,
      ];
  };

  $rentMeta = $sectionMeta(LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES);
  $advanceMeta = $sectionMeta(LandlordAdvanceVendorDashboard::NATURE_RENT_ADVANCE);
  $maintMeta = $sectionMeta(LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE);

  $currentAgreementId = $r?->id;
  $sectionOrder = LandlordAdvanceVendorDashboard::LEDGER_SECTION_ORDER;
  $showAgreementCol = $isVendorScope && count($agreementsList) > 1;
  $showBillCol = $isBillModule;
  $tabSlugs = [
      LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES => 'rent-expenses',
      LandlordAdvanceVendorDashboard::NATURE_RENT_ADVANCE => 'rent-advance',
      LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE => 'maintenance-charges',
      LandlordAdvanceVendorDashboard::NATURE_EB_CHARGES => 'electricity-eb',
  ];
  $tabIcons = [
      'rent-expenses' => 'bi-eye',
      'rent-advance' => 'bi-piggy-bank',
      'maintenance-charges' => 'bi-tools',
      'electricity-eb' => 'bi-lightning-charge-fill',
  ];
  $vlMoney = function ($value) {
      if ($value === null || $value === '' || $value === '—') {
          return '—';
      }
      if (! is_numeric($value)) {
          return '—';
      }
      if (abs((float) $value) < 0.009) {
          return '0.00';
      }

      return number_format((float) $value, 2);
  };
  $ledgerTableCols = 7 + ($showBillCol ? 1 : 0) + ($showAgreementCol ? 1 : 0);

  $tabMetaList = [];
  foreach ($sectionOrder as $sectionKey) {
      $sec = $ledgerSections[$sectionKey] ?? null;
      if ($sec === null) {
          continue;
      }
      $tabSlug = $tabSlugs[$sectionKey] ?? str_replace('_', '-', $sectionKey);
      $tabMetaList[] = [
          'key' => $sectionKey,
          'slug' => $tabSlug,
          'title' => ucwords($sec['title'] ?? $sectionKey),
          'icon' => $tabIcons[$tabSlug] ?? ($sec['icon'] ?? 'bi-receipt'),
      ];
  }
  $firstTab = true;
  $firstPanel = true;
@endphp
<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/rental_agreement.css') }}?v={{ @filemtime(public_path('assets/css/rental_agreement.css')) }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/vendor_ledger.css') }}" />

<body class="ra-page ra-owner-payments-page vl-dashboard-page" style="overflow-x: hidden;">
  @include('superadmin.superadminnav')
  @include('superadmin.superadminheader')

  <div class="pc-container">
    <div class="pc-content">
      <div class="ra-shell">
        <div class="ra-card vl-dashboard">
          <header class="vl-hero" aria-labelledby="vl-owner-heading">
            <div class="vl-hero-brand">
              <div class="vl-hero-avatar" aria-hidden="true">
                <i class="bi bi-building"></i>
              </div>
              <div>
                <div class="vl-hero-title-row">
                  <h1 class="vl-hero-title" id="vl-owner-heading">{{ $ownerTitle }}</h1>
                  @if ($isVendorScope)
                    <span class="vl-hero-badge">{{ $agreementCount }} {{ $agreementCount === 1 ? 'Agreement' : 'Agreements' }}</span>
                  @else
                    <span class="vl-hero-badge">{{ ucfirst($segment) }}</span>
                  @endif
                </div>
                <p class="vl-hero-subtitle">
                  @if ($isVendorScope)
                    Vendor Ledger
                  @else
                    {{ $history['agreement_number'] ?: $r->agreement_number }}
                    @if ($locationLabel !== '')
                      &middot; {{ $locationLabel }}
                    @endif
                  @endif
                </p>
              </div>
            </div>
            <div class="vl-hero-actions">
              <a href="{{ $backUrl }}" class="vl-btn-back">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Back to Register
              </a>
              @if ($r)
                <a href="{{ route($routeNames['show'], array_merge(['rentalAgreement' => $r], $categoryQs)) }}" class="vl-btn-back">
                  <i class="bi bi-eye" aria-hidden="true"></i>
                  View agreement
                </a>
              @endif
            </div>
          </header>

          @if ($isVendorScope && count($agreementsList) > 0)
            <nav class="vl-agreement-pills" aria-label="Linked rental agreements">
              @foreach ($agreementsList as $ag)
                @php
                  $isActivePill = $currentAgreementId && (int) $ag['id'] === (int) $currentAgreementId;
                  $pillHref = route($routeNames['ownerPayments'], array_merge(['rentalAgreement' => $ag['id']], $categoryQs));
                @endphp
                <a href="{{ $pillHref }}" class="vl-agreement-pill{{ $isActivePill ? ' is-active' : '' }}">
                  {{ $ag['agreement_number'] }}
                  <span class="vl-agreement-pill-cat">{{ ucfirst($ag['agreement_type']) }}</span>
                </a>
              @endforeach
            </nav>
          @endif

          <div class="vl-stats">
            <article class="vl-stat-card vl-stat-card--expense" data-vl-stat-tab="rent-expenses" role="button" tabindex="0">
              <div class="vl-stat-icon"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></div>
              <div class="vl-stat-body">
                <span class="vl-stat-label">Rent Expense</span>
                <div class="vl-stat-breakdown">
                  <div class="vl-stat-breakdown-item">
                    <span class="vl-stat-breakdown-label">Total</span>
                    <span class="vl-stat-breakdown-value">&#8377;{{ number_format($rentTotalAmount, 2) }}</span>
                  </div>
                  <div class="vl-stat-breakdown-item vl-stat-breakdown-item--paid">
                    <span class="vl-stat-breakdown-label">Paid</span>
                    <span class="vl-stat-breakdown-value">&#8377;{{ number_format($rentPaidAmount, 2) }}</span>
                  </div>
                  <div class="vl-stat-breakdown-item vl-stat-breakdown-item--balance">
                    <span class="vl-stat-breakdown-label">Balance</span>
                    <span class="vl-stat-breakdown-value">&#8377;{{ number_format($rentBalanceAmount, 2) }}</span>
                  </div>
                </div>
                <span class="vl-stat-meta">
                  @if ($billCount > 0)
                    {{ $billCount }} {{ $billCount === 1 ? 'Bill' : 'Bills' }}
                  @elseif ($rentMeta['next_due'])
                    Next Due: {{ $rentMeta['next_due'] }}
                    &middot; {{ $rentMeta['line_count'] }} {{ $rentMeta['line_count'] === 1 ? 'Record' : 'Records' }}
                  @else
                    {{ $rentMeta['line_count'] }} {{ $rentMeta['line_count'] === 1 ? 'Record' : 'Records' }}
                  @endif
                </span>
              </div>
            </article>

            <article class="vl-stat-card vl-stat-card--advance" data-vl-stat-tab="rent-advance" role="button" tabindex="0">
              <div class="vl-stat-icon"><i class="bi bi-wallet2" aria-hidden="true"></i></div>
              <div class="vl-stat-body">
                <span class="vl-stat-label">Rent Advance</span>
                <span class="vl-stat-value">&#8377;{{ number_format($advancePending, 2) }}</span>
                <span class="vl-stat-meta">
                  Status: {{ $advancePending > 0.009 ? 'Active' : 'Settled' }}
                  &middot; {{ $advanceMeta['line_count'] }} {{ $advanceMeta['line_count'] === 1 ? 'Record' : 'Records' }}
                </span>
              </div>
            </article>

            <article class="vl-stat-card vl-stat-card--maintenance" data-vl-stat-tab="maintenance-charges" role="button" tabindex="0">
              <div class="vl-stat-icon"><i class="bi bi-wrench" aria-hidden="true"></i></div>
              <div class="vl-stat-body">
                <span class="vl-stat-label">Maintenance</span>
                <span class="vl-stat-value">&#8377;{{ number_format($maintenancePending, 2) }}</span>
                <span class="vl-stat-meta">
                  @if ($maintMeta['next_due'])
                    Next Due: {{ $maintMeta['next_due'] }}
                  @else
                    No pending due
                  @endif
                  &middot; {{ $maintMeta['line_count'] }} {{ $maintMeta['line_count'] === 1 ? 'Record' : 'Records' }}
                </span>
              </div>
            </article>

            <article class="vl-stat-card vl-stat-card--total">
              <div class="vl-stat-icon"><i class="bi bi-check-circle" aria-hidden="true"></i></div>
              <div class="vl-stat-body">
                <span class="vl-stat-label">Total Pending</span>
                <span class="vl-stat-value">&#8377;{{ number_format($totalPending, 2) }}</span>
                <span class="vl-stat-meta">
                  @if ($vendorBills && $vendorBillDue > 0.009)
                    All vendor bills due: &#8377;{{ number_format($vendorBillDue, 2) }}
                    @if ($paymentCount > 0)
                      &middot; {{ $paymentCount }} {{ $paymentCount === 1 ? 'payment' : 'payments' }}
                    @endif
                  @else
                    Overall Outstanding
                  @endif
                </span>
              </div>
            </article>
          </div>

          <div class="vl-main-card">
            <div class="vl-ledger-card">
  <nav class="vl-tabs" role="tablist" aria-label="Ledger categories">
    @foreach ($tabMetaList as $tabMeta)
      @php
        $tabClass = 'vl-tab';
        if ($firstTab) {
            $tabClass .= ' is-active';
        }
      @endphp
      <button
        type="button"
        class="{{ $tabClass }}"
        role="tab"
        data-vl-tab="{{ $tabMeta['slug'] }}"
        aria-selected="{{ $firstTab ? 'true' : 'false' }}"
      >
        <i class="bi {{ $tabMeta['icon'] }}" aria-hidden="true"></i>
        <span>{{ $tabMeta['title'] }}</span>
      </button>
      @php $firstTab = false; @endphp
    @endforeach
  </nav>

  @php $firstPanel = true; @endphp
  @foreach ($sectionOrder as $sectionKey)
    @php
      $section = $ledgerSections[$sectionKey] ?? null;
      if ($section === null) {
          continue;
      }
      $sectionRows = $section['rows'] ?? [];
      $sectionSummary = $section['summary'] ?? [];
      $slug = $tabSlugs[$sectionKey] ?? str_replace('_', '-', $sectionKey);
      $pendingTotal = (float) ($sectionSummary['pending_balance'] ?? 0);
      $lineCount = (int) ($sectionSummary['line_count'] ?? count($sectionRows));
      $badgeLabel = $sectionKey === LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES
          ? 'Monthly'
          : ($sectionKey === LandlordAdvanceVendorDashboard::NATURE_RENT_ADVANCE ? 'Advance' : 'Upkeep');
    @endphp
    <section
      class="vl-ledger-panel{{ $firstPanel ? ' is-active' : '' }}"
      role="tabpanel"
      data-vl-panel="{{ $slug }}"
      @if (! $firstPanel) hidden @endif
    >
      <div class="vl-panel-toolbar">
        <div class="vl-panel-toolbar-left">
          <h2 class="vl-panel-title">{{ ucwords($section['title'] ?? $sectionKey) }}</h2>
          <span class="vl-panel-badge">{{ strtoupper($badgeLabel) }}</span>
        </div>
        <div class="vl-panel-toolbar-right">
          <span class="vl-panel-pending">
            <span class="vl-panel-pending-label">Total Pending:</span>
            <strong class="vl-panel-pending-amt">&#8377;{{ number_format($pendingTotal, 2) }}</strong>
          </span>
          @if (($isBillModule ?? false) || ! empty($vendor))
            <a href="{{ route('superadmin.getbillcreate', array_filter(['vendor_id' => $vendor->id ?? null])) }}" class="vl-btn-add-bill">
              <i class="bi bi-plus-lg" aria-hidden="true"></i>
              Add New Bill
            </a>
          @endif
        </div>
      </div>

      <div class="vl-table-card">
        <div class="vl-table-wrap">
          <table class="vl-table">
            <thead>
              <tr>
                @if ($showBillCol)
                  <th scope="col" class="vl-th">Bill no.</th>
                @endif
                @if ($showAgreementCol)
                  <th scope="col" class="vl-th">Agreement</th>
                @endif
                <th scope="col" class="vl-th">Month</th>
                <th scope="col" class="vl-th">Details</th>
                <th scope="col" class="vl-th vl-th--num">To pay (&#8377;)</th>
                <th scope="col" class="vl-th vl-th--num">Paid (&#8377;)</th>
                <th scope="col" class="vl-th vl-th--center">Status</th>
                <th scope="col" class="vl-th">Due date</th>
                <th scope="col" class="vl-th vl-th--center">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($sectionRows as $rowIndex => $row)
                @php
                  $rowId = $slug.'-'.$rowIndex;
                  $statusClass = match ($row['status'] ?? '') {
                      'completed' => 'vl-status-pill--done',
                      'opening' => 'vl-status-pill--opening',
                      default => 'vl-status-pill--due',
                  };
                  $billRef = $row['bill_ref'] ?? ($row['agreement_number'] ?? 'â€”');
                  $toPay = (float) ($row['net_payable'] ?? $row['pending_balance'] ?? 0);
                  $paidAmt = (float) ($row['amount_sent'] ?? 0);
                  $dueDate = $row['due_date'] ?? ($row['payment_month'] ?? 'â€”');
                @endphp
                <tr class="vl-data-row" data-vl-row-id="{{ $rowId }}">
                  @if ($showBillCol)
                    <td class="vl-td"><span class="vl-bill-no">{{ $billRef }}</span></td>
                  @endif
                  @if ($showAgreementCol)
                    <td class="vl-td vl-td--nowrap">
                      @if (!empty($row['rental_agreement_id']))
                        <a href="{{ route($routeNames['ownerPayments'], array_merge(['rentalAgreement' => $row['rental_agreement_id']], $categoryQs ?? [])) }}" class="vl-link">{{ $row['agreement_number'] ?? 'â€”' }}</a>
                      @else
                        {{ $row['agreement_number'] ?? 'â€”' }}
                      @endif
                    </td>
                  @endif
                  <td class="vl-td vl-td--nowrap">{{ $row['payment_month'] ?? 'â€”' }}</td>
                  <td class="vl-td vl-td--desc">
                    <span class="vl-desc-title">{{ $row['payment_purpose'] ?? 'â€”' }}</span>
                  </td>
                  <td class="vl-td vl-td--num vl-td--topay">{{ $vlMoney($toPay) }}</td>
                  <td class="vl-td vl-td--num vl-td--paid">{{ $vlMoney($paidAmt) }}</td>
                  <td class="vl-td vl-td--center">
                    <span class="vl-status-pill {{ $statusClass }}">{{ strtoupper($row['status_label'] ?? 'â€”') }}</span>
                  </td>
                  <td class="vl-td vl-td--nowrap">{{ $dueDate }}</td>
                  <td class="vl-td vl-td--center vl-td--actions">
                    <div class="vl-action-btns">
                      @if (!empty($row['detail_url']))
                        <a href="{{ $row['detail_url'] }}" class="vl-action-btn" target="_blank" rel="noopener" title="View">
                          <i class="bi bi-eye" aria-hidden="true"></i>
                        </a>
                      @endif
                      <button
                        type="button"
                        class="vl-action-btn vl-more-btn"
                        data-vl-row='@json($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
                        aria-expanded="false"
                        title="Tax &amp; payment breakdown"
                      >
                        <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="{{ $ledgerTableCols }}" class="vl-table-empty">
                    <i class="bi bi-inbox vl-empty-icon" aria-hidden="true"></i>
                    <span>No records in this category.</span>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <footer class="vl-table-footer">
        <span class="vl-table-count">
          @if ($lineCount > 0)
            Showing 1 to {{ $lineCount }} of {{ $lineCount }} {{ $lineCount === 1 ? 'record' : 'records' }}
          @else
            Showing 0 records
          @endif
        </span>
        @if ($lineCount > 0)
          <div class="vl-pagination" aria-label="Table pagination">
            <button type="button" class="vl-page-btn" disabled aria-label="Previous page">
              <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
            <span class="vl-page-num is-active" aria-current="page">1</span>
            <button type="button" class="vl-page-btn" disabled aria-label="Next page">
              <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
            <select class="vl-page-size" aria-label="Rows per page" disabled>
              <option selected>10 / page</option>
            </select>
          </div>
        @endif
      </footer>
    </section>
    @php $firstPanel = false; @endphp
  @endforeach
</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="vlBreakdownDrawer" class="vl-breakdown-drawer" aria-hidden="true">
  <div class="vl-breakdown-drawer__backdrop" data-vl-drawer-close tabindex="-1" aria-hidden="true"></div>
  <aside
    class="vl-breakdown-drawer__panel"
    role="dialog"
    aria-modal="true"
    aria-labelledby="vlBreakdownDrawerTitle"
  >
    <header class="vl-breakdown-drawer__head">
      <div>
        <p class="vl-breakdown-drawer__eyebrow">Bill details</p>
        <h2 class="vl-breakdown-drawer__title" id="vlBreakdownDrawerTitle">Tax &amp; payment breakdown</h2>
      </div>
      <button type="button" class="vl-breakdown-drawer__close" data-vl-drawer-close aria-label="Close breakdown">
        <i class="bi bi-x-lg" aria-hidden="true"></i>
      </button>
    </header>

    <div class="vl-breakdown-drawer__body">
      <section class="vl-breakdown-drawer__context" aria-label="Line summary">
        <div class="vl-breakdown-context-grid">
          <div class="vl-breakdown-context-item">
            <span class="vl-breakdown-context-label">Bill no.</span>
            <span class="vl-breakdown-context-value" id="vlDrawerBill">â€”</span>
          </div>
          <div class="vl-breakdown-context-item">
            <span class="vl-breakdown-context-label">Month</span>
            <span class="vl-breakdown-context-value" id="vlDrawerMonth">â€”</span>
          </div>
          <div class="vl-breakdown-context-item vl-breakdown-context-item--wide">
            <span class="vl-breakdown-context-label">Details</span>
            <span class="vl-breakdown-context-value" id="vlDrawerPurpose">â€”</span>
          </div>
          <div class="vl-breakdown-context-item">
            <span class="vl-breakdown-context-label">Status</span>
            <span class="vl-breakdown-context-value" id="vlDrawerStatus">â€”</span>
          </div>
          <div class="vl-breakdown-context-item">
            <span class="vl-breakdown-context-label">To pay (â‚¹)</span>
            <span class="vl-breakdown-context-value vl-breakdown-context-value--due" id="vlDrawerToPay">â€”</span>
          </div>
          <div class="vl-breakdown-context-item">
            <span class="vl-breakdown-context-label">Paid (â‚¹)</span>
            <span class="vl-breakdown-context-value" id="vlDrawerPaid">â€”</span>
          </div>
          <div class="vl-breakdown-context-item">
            <span class="vl-breakdown-context-label">Due date</span>
            <span class="vl-breakdown-context-value" id="vlDrawerDue">â€”</span>
          </div>
        </div>
      </section>

      <section class="vl-breakdown-drawer__tax" aria-label="Tax and payment breakdown">
        <h3 class="vl-detail-heading">Tax &amp; payment breakdown</h3>
        <div class="vl-detail-grid" id="vlBreakdownGrid"></div>
      </section>
    </div>

    <footer class="vl-breakdown-drawer__foot">
      <a href="#" class="vl-btn-view-bill d-none" id="vlDrawerViewBill" target="_blank" rel="noopener">
        <i class="bi bi-eye" aria-hidden="true"></i>
        View bill
      </a>
    </footer>
  </aside>
</div>


  <script src="{{ asset('assets/js/rental_agreement.js') }}?v={{ @filemtime(public_path('assets/js/rental_agreement.js')) }}"></script>

  @include('superadmin.superadminfooter')
</body>
</html>
