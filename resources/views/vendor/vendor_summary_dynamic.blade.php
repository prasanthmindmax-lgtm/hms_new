@php
  $grandBills = collect($summaryData)->sum('total_bills');
  $grandPaid  = collect($summaryData)->sum('total_paid');
  $grandDue   = collect($summaryData)->sum('total_due');
@endphp

{{-- ── Grand Total Strip ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px 14px;border-bottom:1px solid #eaedf3;margin-bottom:14px;">
  <span style="font-size:13px;font-weight:600;color:#374151;">
    <i class="bi bi-layers me-1" style="color:#4f6ef7;"></i>
    {{ collect($summaryData)->count() }} Account{{ collect($summaryData)->count() !== 1 ? 's' : '' }}
  </span>
  <div style="display:flex;gap:20px;">
    <span style="font-size:13px;color:#374151;">
      Total Bills: <strong>₹{{ number_format($grandBills, 2) }}</strong>
    </span>
    <span style="font-size:13px;color:#059669;">
      Paid: <strong>₹{{ number_format($grandPaid, 2) }}</strong>
    </span>
    <span style="font-size:13px;color:#dc2626;">
      Due: <strong>₹{{ number_format($grandDue, 2) }}</strong>
    </span>
  </div>
</div>

@forelse($summaryData as $accountSummary)
  <div class="vs-account-card">

    {{-- Account Header (accordion toggle) --}}
    <div class="vs-account-header">
      <div class="vs-account-left">
        <div class="vs-account-index">{{ $loop->iteration }}</div>
        <div class="vs-account-name">{{ strtoupper($accountSummary['account'] ?? 'N/A') }}</div>
      </div>
      <div style="display:flex;align-items:center;gap:10px;">
        <div class="vs-account-pills">
          <span class="vs-pill">
            <i class="bi bi-receipt me-1"></i>₹{{ number_format($accountSummary['total_bills'], 2) }}
          </span>
          <span class="vs-pill vs-pill-paid">
            <i class="bi bi-check-circle me-1"></i>₹{{ number_format($accountSummary['total_paid'], 2) }}
          </span>
          <span class="vs-pill vs-pill-due">
            <i class="bi bi-clock me-1"></i>₹{{ number_format($accountSummary['total_due'], 2) }}
          </span>
        </div>
        <i class="bi bi-chevron-down vs-chevron"></i>
      </div>
    </div>

    {{-- Account Body (hidden until clicked) --}}
    <div class="vs-account-body">
      <table class="vs-vendor-table">
        <thead>
          <tr>
            <th style="width:36px;">#</th>
            <th>VENDOR NAME</th>
            <th class="text-end">TOTAL BILLS</th>
            <th class="text-end">PAID</th>
            <th class="text-end">DUE</th>
          </tr>
        </thead>
        <tbody>
          @foreach($accountSummary['vendors'] as $i => $vendor)
            <tr>
              <td style="color:#9ca3af;font-size:12px;">{{ $loop->iteration }}</td>
              <td>
                <a class="vs-vendor-link"
                   href="{{ route('superadmin.getvendor') }}?id={{ $vendor['vendor_id'] }}">
                  {{ strtoupper($vendor['vendor_name']) }}
                </a>
              </td>
              <td class="vs-amount text-end">₹{{ number_format($vendor['bills'], 2) }}</td>
              <td class="vs-amount vs-amount-paid text-end">₹{{ number_format($vendor['paid'], 2) }}</td>
              <td class="vs-amount vs-amount-due text-end">₹{{ number_format($vendor['due'], 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr class="vs-footer-row">
            <td colspan="2" class="text-end" style="padding:10px 16px;">
              Total — {{ strtoupper($accountSummary['account']) }}
            </td>
            <td class="vs-amount text-end">₹{{ number_format($accountSummary['total_bills'], 2) }}</td>
            <td class="vs-amount vs-amount-paid text-end">₹{{ number_format($accountSummary['total_paid'], 2) }}</td>
            <td class="vs-amount vs-amount-due text-end">₹{{ number_format($accountSummary['total_due'], 2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

  </div>
@empty
  <div class="text-center py-5 text-muted">
    <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:10px;color:#d1d5db;"></i>
    No vendor summary data found
  </div>
@endforelse
