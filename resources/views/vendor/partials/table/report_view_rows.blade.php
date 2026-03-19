{{-- ── Stat Cards ── --}}
<div class="qd-stats" style="border-bottom: 1px solid #eaedf3; margin-bottom: 0;">
  <div class="qd-stat-card qd-stat-blue" style="cursor:default;">
    <div class="qd-stat-icon"><i class="bi bi-receipt"></i></div>
    <div class="qd-stat-body">
      <div class="qd-stat-label">Invoice Payable</div>
      <div class="qd-stat-value">₹{{ number_format($totalInvoiceAmount, 2) }}</div>
      <div class="qd-stat-sub">Sub total of all bills</div>
    </div>
  </div>
  <div class="qd-stat-card qd-stat-orange" style="cursor:default;">
    <div class="qd-stat-icon"><i class="bi bi-percent"></i></div>
    <div class="qd-stat-body">
      <div class="qd-stat-label">Total TDS</div>
      <div class="qd-stat-value">₹{{ number_format($totalTDS, 2) }}</div>
      <div class="qd-stat-sub">TDS deducted</div>
    </div>
  </div>
  <div class="qd-stat-card qd-stat-purple" style="cursor:default;">
    <div class="qd-stat-icon"><i class="bi bi-calculator"></i></div>
    <div class="qd-stat-body">
      <div class="qd-stat-label">Total GST</div>
      <div class="qd-stat-value">₹{{ number_format($totalGST, 2) }}</div>
      <div class="qd-stat-sub">GST on all bills</div>
    </div>
  </div>
  <div class="qd-stat-card qd-stat-green" style="cursor:default;">
    <div class="qd-stat-icon"><i class="bi bi-check-circle"></i></div>
    <div class="qd-stat-body">
      <div class="qd-stat-label">Final Payable</div>
      <div class="qd-stat-value">₹{{ number_format($totalFinalAmount, 2) }}</div>
      <div class="qd-stat-sub">Grand total payable</div>
    </div>
  </div>
</div>

{{-- ── Tabs ── --}}
<div style="padding: 0 4px;">
  <div class="rv-tab-bar" style="display:flex; border-bottom: 2px solid #eaedf3; margin-bottom: 0; padding: 12px 4px 0;">
    <button class="rv-tab active" data-tab="rv-view-tab">
      <i class="bi bi-table me-1"></i>View
    </button>
    <button class="rv-tab" data-tab="rv-summary-tab">
      <i class="bi bi-bar-chart-line me-1"></i>Summary
    </button>
  </div>

  {{-- View Tab --}}
  <div id="rv-view-tab" class="rv-tab-content rv-active">
    <div class="qdt-wrap">
      <table class="qdt-table">
        <thead class="qdt-head">
          <tr>
            <th>DATE</th>
            <th>LOCATION</th>
            <th>BILL DATE</th>
            <th>BILL</th>
            <th>VENDOR</th>
            <th>NATURE OF ACCOUNT</th>
            <th class="text-end">INVOICE PAYABLE</th>
            <th class="text-end">TDS</th>
            <th class="text-end">GST</th>
            <th class="text-end">FINAL PAYABLE</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($details as $bill)
            @foreach ($bill->BillLines as $line)
              @php
                $zoneRaw = strtolower($bill->zone_name ?? '');
                $zoneCls = str_contains($zoneRaw,'karnataka') ? 'qdt-zone-orange'
                         : (str_contains($zoneRaw,'kerala')   ? 'qdt-zone-green'
                         : (str_contains($zoneRaw,'inter')    ? 'qdt-zone-purple'
                         : 'qdt-zone-teal'));
              @endphp
              <tr class="qdt-row">
                <td class="qdt-date-cell">
                  <span class="qdt-date-main">{{ $bill->created_at->format('d/m/Y') }}</span>
                </td>
                <td>
                  @if($bill->zone_name)
                    <span class="qdt-zone-badge {{ $zoneCls }}">{{ strtoupper($bill->zone_name) }}</span>
                  @endif
                  <div class="qdt-branch">{{ $bill->branch_name ?? '—' }}</div>
                </td>
                <td class="qdt-date-cell">
                  <span class="qdt-date-main">{{ $bill->bill_date }}</span>
                </td>
                <td>
                  <a href="{{ route('superadmin.getbill') }}?id={{ $bill->id }}" class="qdt-mono" style="color:#4f6ef7;text-decoration:none;">
                    {{ $bill->bill_gen_number ?? '—' }}
                  </a>
                </td>
                <td>
                  <a href="{{ route('superadmin.getvendor') }}?id={{ $bill->vendor_id }}" style="color:#4f6ef7;text-decoration:none;font-size:13px;font-weight:500;">
                    {{ $bill->Tblvendor->display_name ?? $bill->vendor_name ?? '—' }}
                  </a>
                </td>
                <td>
                  <span style="font-size:11px;font-weight:500;color:#4f6ef7;background:#eff2ff;padding:2px 8px;border-radius:20px;">
                    {{ $line->account ?? '—' }}
                  </span>
                </td>
                <td class="qdt-amount text-end">₹{{ number_format($bill->sub_total_amount ?? 0, 2) }}</td>
                <td class="qdt-amount text-end">₹{{ number_format($bill->tds_amount ?? 0, 2) }}</td>
                <td class="qdt-amount text-end">₹{{ number_format($line->gst_amount ?? 0, 2) }}</td>
                <td class="qdt-amount text-end" style="font-weight:700;color:#059669;">₹{{ number_format($bill->grand_total_amount ?? 0, 2) }}</td>
              </tr>
            @endforeach
          @empty
            <tr>
              <td colspan="10" class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                No expense records found
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Summary Tab --}}
  <div id="rv-summary-tab" class="rv-tab-content">
    <div class="qdt-wrap">
      <table class="qdt-table">
        <thead class="qdt-head">
          <tr>
            <th>VENDOR NAME</th>
            <th class="text-end">TOTAL BILL AMOUNT</th>
            <th class="text-center">ACTION</th>
          </tr>
        </thead>
        <tbody>
          @php $grandTotal = 0; @endphp
          @forelse ($groupedVendors as $vendorName => $data)
            @php $grandTotal += $data['total_amount']; @endphp
            <tr class="qdt-row">
              <td>
                <a href="{{ route('superadmin.getvendor') }}?id={{ $data['vendor_id'] }}" style="color:#4f6ef7;text-decoration:none;font-weight:500;font-size:13px;">
                  {{ $vendorName }}
                </a>
              </td>
              <td class="qdt-amount text-end">₹{{ number_format($data['total_amount'], 2) }}</td>
              <td class="text-center">
                <button class="btn btn-sm view-vendor-bills"
                  style="font-size:11px;padding:4px 12px;border:1px solid #4f6ef7;color:#4f6ef7;border-radius:6px;background:#fff;"
                  data-vendor-id="{{ $data['vendor_id'] }}"
                  data-vendor-name="{{ $vendorName }}"
                  data-account-name="{{ request()->segment(4) ?? '' }}">
                  <i class="bi bi-calendar3 me-1"></i>Monthly Summary
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                No vendor data found
              </td>
            </tr>
          @endforelse

          @if(count($groupedVendors) > 0)
            <tr style="background:#f8f9ff;font-weight:700;">
              <td style="text-align:right;color:#374151;font-size:13px;">Grand Total</td>
              <td class="qdt-amount text-end" style="color:#059669;">₹{{ number_format($grandTotal, 2) }}</td>
              <td></td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>

</div>
