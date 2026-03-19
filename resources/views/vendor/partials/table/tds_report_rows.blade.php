<div class="qdt-wrap">
  <table class="qdt-table">
    <thead class="qdt-head">
      <tr>
        <th class="qdt-th-check"><input type="checkbox" id="selectAll"></th>
        <th>DATE</th>
        <th>LOCATION</th>
        <th>BILL GEN NO</th>
        <th>BILL #</th>
        <th>REFERENCE NO</th>
        <th>VENDOR</th>
        <th>VENDOR PAN</th>
        <th>DUE DATE</th>
        <th class="text-end">INVOICE AMT</th>
        <th class="text-end">TDS AMT</th>
        <th>TDS PAY DATE</th>
        <th>CHALLAN NO</th>
        <th>QUARTER</th>
        <th>TDS STATUS</th>
        <th>ACTION</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($billlist as $bill)
        @php
          $zoneRaw  = strtolower($bill->zone_name ?? '');
          $zoneCls  = str_contains($zoneRaw, 'karnataka') ? 'qdt-zone-orange'
                    : (str_contains($zoneRaw, 'kerala')   ? 'qdt-zone-green'
                    : (str_contains($zoneRaw, 'inter')    ? 'qdt-zone-purple'
                    : 'qdt-zone-teal'));
          $tdsStatus   = strtolower($bill->tds_paid_status ?? '');
          $tdsBadgeCls = $tdsStatus === 'paid' ? 'qd-badge qd-badge-approved' : 'qd-badge qd-badge-rejected';
        @endphp
        <tr class="qdt-row customer-row"
            data-id="{{ $bill->id }}"
            data-tds-pay-date="{{ $bill->tds_pay_date ?? '' }}"
            data-tds-challan-no="{{ $bill->tds_challan_no ?? '' }}"
            data-tds-quarter="{{ $bill->tds_quarter ?? '' }}"
            data-tds-utr-no="{{ $bill->tds_utr_no ?? '' }}"
            data-bill-number="{{ $bill->bill_number }}"
            data-order-number="{{ $bill->order_number }}"
            data-vendor-name="{{ $bill->vendor_name }}"
            data-vendor-address='@json($bill->TblBilling)'
            data-allbill='@json($bill)'
            data-vendor='@json($bill->Tblvendor)'
            data-bank='@json($bill->Tblbankdetails)'
            data-bill-date="{{ $bill->bill_date }}"
            data-due-date="{{ $bill->due_date }}"
            data-payment-terms="{{ $bill->payment_terms }}"
            data-discount_amount="{{ $bill->discount_amount }}"
            data-grand-total="{{ $bill->grand_total_amount }}"
            data-sub-total="{{ $bill->sub_total_amount }}"
            data-note="{{ $bill->note ?? 'No notes' }}"
            data-items='@json($bill->BillLines)'>

          <td class="qdt-td-check"><input type="checkbox" class="bill-checkbox"></td>

          <td class="qdt-date-cell">
            <span class="qdt-date-main">{{ $bill->bill_date ?? '—' }}</span>
          </td>

          <td>
            @if($bill->zone_name)
              <span class="qdt-zone-badge {{ $zoneCls }}">{{ strtoupper($bill->zone_name) }}</span>
            @endif
            <div class="qdt-branch">{{ $bill->branch_name ?? '—' }}</div>
          </td>

          <td class="qdt-mono">{{ $bill->bill_gen_number ?? '—' }}</td>
          <td class="qdt-mono">{{ $bill->bill_number ?? '—' }}</td>
          <td class="qdt-mono">{{ $bill->order_number ?? '—' }}</td>

          <td>{{ $bill->Tblvendor->display_name ?? $bill->vendor_name ?? '—' }}</td>
          <td class="qdt-mono">{{ $bill->Tblvendor->pan_number ?? '—' }}</td>

          <td class="qdt-due">{{ $bill->due_date ?? '—' }}</td>
          <td class="qdt-amount text-end">₹{{ number_format($bill->grand_total_amount ?? 0, 2) }}</td>
          <td class="qdt-amount text-end" style="color:#dc3545;">
            ₹{{ number_format($bill->tax_amount ?? 0, 2) }}
          </td>

          <td class="qdt-date-cell">
            <span class="qdt-date-main" style="font-size:12px;">{{ $bill->tds_pay_date ?? '—' }}</span>
          </td>
          <td class="qdt-mono">{{ $bill->tds_challan_no ?? '—' }}</td>
          <td>
            @if($bill->tds_quarter)
              <span class="qd-badge qd-badge-default">{{ $bill->tds_quarter }}</span>
            @else
              <span class="qdt-dash">—</span>
            @endif
          </td>

          <td><span class="{{ $tdsBadgeCls }}">{{ ucfirst($bill->tds_paid_status ?? '—') }}</span></td>

          <td>
            <button class="btn btn-sm btn-outline-primary openUpdateModal"
                    style="font-size:11px;padding:3px 10px;">
              Update
            </button>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="16" class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
            No TDS records found
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Pagination --}}
  @if($billlist->total() > 10)
  <div class="qd-pagination d-flex justify-content-between align-items-center mt-2 px-1">
    <div>{{ $billlist->links('pagination::bootstrap-4') }}</div>
    <div>
      <form method="GET" id="perPageForm" style="display:inline-flex;align-items:center;gap:6px;">
        <select name="per_page" id="per_page" class="form-control form-control-sm" style="width:70px;">
          @foreach([10,25,50,100,250,500] as $size)
            <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
          @endforeach
        </select>
        <span style="font-size:13px;color:#6c757d;">entries</span>
      </form>
    </div>
  </div>
  @endif

</div>
