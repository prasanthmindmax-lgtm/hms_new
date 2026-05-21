@php
  $activeTab = $activeTab ?? 'all';
  $activeTaxFilter = $activeTaxFilter ?? null;
  $rowFrom = method_exists($records, 'firstItem') && $records->firstItem() ? $records->firstItem() : null;
  $rowTo = method_exists($records, 'lastItem') && $records->lastItem() ? $records->lastItem() : null;
  $rowRangeLabel = ($rowFrom && $rowTo) ? ($rowFrom.'–'.$rowTo) : '0';
@endphp
<div id="llp-payments-panel"
  class="llp-payments-panel"
  data-active-tab="{{ $activeTab }}"
  data-row-range="{{ $rowRangeLabel }}"
  data-total-rows="{{ $records->total() ?? 0 }}"
  @if (!empty($exportExcelUrl)) data-export-excel="{{ $exportExcelUrl }}" @endif
  @if (!empty($exportCsvUrl)) data-export-csv="{{ $exportCsvUrl }}" @endif
  @if (!empty($chartReportUrl)) data-chart-url="{{ $chartReportUrl }}" @endif>
  <div class="llp-payments-card__body">
    <div class="llp-table-shell llp-table-shell--flush mb-0">
      <div class="llp-table-wrap llp-table-wrap--payments-detail">
        <table class="llp-table llp-table--payments llp-table--payments-detail">
          <thead>
            <tr>
              <th class="llp-th-sno text-center">S.No</th>
              <th>Agreement</th>
              <th>Landlord</th>
              <th>Location</th>
              <th>Category</th>
              <th>Type</th>
              <th>Month</th>
              <th>TDS §</th>
              <th class="text-end">TDS %</th>
              <th class="text-end">SGST</th>
              <th class="text-end">CGST</th>
              <th class="text-end">IGST</th>
              <th class="text-end">Other ded.</th>
              <th class="text-end">Final NEFT</th>
              <th>UTR</th>
              <th>Status</th>
              <th class="text-end"></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($records as $row)
              @php
                $agreement = $row['agreement'] ?? null;
                $isPaid = ($row['status'] ?? '') === 'completed';
                $tdsPct = $row['tds_percent'] ?? null;
              @endphp
              <tr>
                <td class="llp-td-sno text-muted text-center">{{ (method_exists($records, 'firstItem') && $records->firstItem()) ? $records->firstItem() + $loop->index : $loop->iteration }}</td>
                <td>
                  <span class="llp-cell-agreement">{{ $row['agreement_number'] ?? ($agreement?->agreement_number ?? '—') }}</span>
                </td>
                <td class="pay-pr-td-names">
                  @php
                    $landlordName = trim((string) ($row['owner_name'] ?? ''));
                    $vendorId = (int) ($row['vendor_id'] ?? 0);
                    $ownerPaymentsQs = array_filter([
                      'category' => $row['agreement_type'] ?? request()->query('segment'),
                    ]);
                    $ownerPaymentsUrl = $vendorId > 0
                      ? route('rental-agreements.vendor-owner-payments', array_merge(['vendor' => $vendorId], $ownerPaymentsQs))
                      : ($agreement
                        ? route('rental-agreements.owner-payments', array_merge(['rentalAgreement' => $agreement], $ownerPaymentsQs))
                        : null);
                  @endphp
                  @if ($landlordName !== '' && $ownerPaymentsUrl)
                    <a href="{{ $ownerPaymentsUrl }}"
                      class="pay-pr-vendor-name ra-owner-payments-link"
                      title="Rental advances &amp; bill payments for {{ $landlordName }}">
                      {{ \Illuminate\Support\Str::limit($landlordName, 48) }}
                    </a>
                  @elseif ($landlordName !== '')
                    <span class="pay-pr-vendor-name">{{ \Illuminate\Support\Str::limit($landlordName, 48) }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                @if ($agreement)
                  <td class="llp-td-loc text-nowrap">
                    @if ($agreement->zone)
                      <span class="llp-loc-zone d-block">{{ strtoupper($agreement->zone->name) }}</span>
                    @endif
                    @if ($agreement->branch)
                      <span class="llp-loc-branch d-block">{{ $agreement->branch->name }}</span>
                    @endif
                    @if (! $agreement->zone && ! $agreement->branch)
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  @php
                    $cat = \App\Models\RentalAgreement::normalizeType((string) ($row['agreement_type'] ?? $agreement->agreement_type ?? ''));
                  @endphp
                  <td class="llp-td-category text-nowrap">
                    <span class="llp-pill {{ $cat === \App\Models\RentalAgreement::TYPE_HOSTEL ? 'llp-pill-hostel' : 'llp-pill-hospital' }}">{{ $cat === \App\Models\RentalAgreement::TYPE_HOSTEL ? 'Hostel' : 'Hospital' }}</span>
                  </td>
                @else
                  <td class="text-muted">—</td>
                  <td><span class="text-muted">—</span></td>
                @endif
                <td class="text-nowrap">
                  @php
                    $typeLabel = trim((string) ($row['nature_label'] ?? ''));
                    $typeKey = (string) ($row['nature_key'] ?? '');
                  @endphp
                  @if ($typeLabel !== '')
                    <span class="llp-type-pill llp-type-pill--{{ $typeKey !== '' ? $typeKey : 'other' }}" title="{{ $typeLabel }}">
                      {{ $typeLabel }}
                    </span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-nowrap">{{ $row['payment_month'] ?? '—' }}</td>
                <td><span class="fw-bold">{{ $row['tds_section'] ?? '194I' }}</span></td>
                <td class="text-end small">
                  @if ($tdsPct !== null)
                    {{ rtrim(rtrim(number_format($tdsPct, 2), '0'), '.') }}%
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-end llp-amount">₹{{ number_format((float) ($row['sgst_display'] ?? 0), 2) }}</td>
                <td class="text-end llp-amount">₹{{ number_format((float) ($row['cgst_display'] ?? 0), 2) }}</td>
                <td class="text-end llp-amount">₹{{ number_format((float) ($row['igst_display'] ?? 0), 2) }}</td>
                <td class="text-end llp-amount">₹{{ number_format((float) ($row['other_deductions'] ?? 0), 2) }}</td>
                <td class="text-end llp-amount llp-amount-neft fw-bold">₹{{ number_format((float) ($row['display_neft'] ?? 0), 2) }}</td>
                <td><span class="llp-code">{{ $row['utr_display'] ?? '—' }}</span></td>
                <td>
                  <span class="llp-pill {{ $isPaid ? 'llp-pill-complete' : 'llp-pill-muted' }}">{{ $row['status_label'] ?? '—' }}</span>
                </td>
                <td class="text-end text-nowrap">
                  @if (!empty($row['detail_url']))
                    <a href="{{ $row['detail_url'] }}" class="llp-btn llp-btn-ghost llp-btn-xs" target="_blank" rel="noopener">View bill</a>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="17" class="llp-empty">
                  @php
                    $emptyLabel = strtolower($paymentsTabLabels[$activeTab] ?? 'records');
                    if ($activeTaxFilter === 'gst') {
                        $emptyLabel = 'GST rows';
                    } elseif ($activeTaxFilter === 'tds') {
                        $emptyLabel = 'TDS rows';
                    }
                  @endphp
                  No {{ $emptyLabel }} match your filters in the bill module.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if ($records->hasPages())
    <footer class="llp-payments-card__foot llp-payments-panel__foot">
      {{ $records->appends(request()->query())->links() }}
    </footer>
  @endif
</div>
