<div class="qdt-wrap">
  <table class="qdt-table">
    <thead class="qdt-head">
      <tr>
        <th class="qdt-th-check"><input type="checkbox" id="selectAllGrn"></th>
        <th>DATE</th>
        <th>INVOICE DATE</th>
        <th>ZONE / BRANCH</th>
        <th>GRN NO #</th>
        <th>REFERENCE NO</th>
        <th>VENDOR</th>
        <th>QC Checked By</th>
        <th>STATUS</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($grnlist as $grn)
        @php
          $zoneRaw = strtolower($grn->zone_name ?? '');
          $zoneCls = str_contains($zoneRaw, 'karnataka') ? 'qdt-zone-orange'
                   : (str_contains($zoneRaw, 'kerala')   ? 'qdt-zone-green'
                   : (str_contains($zoneRaw, 'inter')    ? 'qdt-zone-purple'
                   : 'qdt-zone-teal'));

          if ($grn->approval_status == 1) {
            $statusBadge = 'qd-badge qd-badge-approved';
            $statusText  = 'Approved';
          } elseif (isset($grn->reject_status) && $grn->reject_status == 1) {
            $statusBadge = 'qd-badge qd-badge-rejected';
            $statusText  = 'Rejected';
          } else {
            $statusBadge = 'qd-badge qd-badge-pending';
            $statusText  = 'Pending';
          }
        @endphp
        <tr class="qdt-row customer-row"
            data-id="{{ $grn->id }}"
            data-grn_number="{{ $grn->grn_number }}"
            data-order-number="{{ $grn->order_number }}"
            data-vendor-name="{{ $grn->vendor_name }}"
            data-zone-name="{{ $grn->zone_name }}"
            data-branch-name="{{ $grn->branch_name }}"
            data-vendor-address='@json($grn->TblBilling)'
            data-vendor='@json($grn->Tblvendor)'
            data-grn_all='@json($grn)'
            data-bill-date="{{ $grn->bill_date }}"
            data-due-date="{{ $grn->due_date }}"
            data-approval_status="{{ $grn->approval_status }}"
            data-payment-terms="{{ $grn->payment_terms }}"
            data-note="{{ $grn->note ?? 'No notes' }}"
            data-items='@json($grn->BillLines)'>

          <td class="qdt-td-check">
            <input type="checkbox" class="row-check" onclick="event.stopPropagation()">
          </td>

          <td class="qdt-date-cell">
            <span class="qdt-date-main">{{ $grn->created_at->format('d M Y') }}</span>
          </td>

          <td class="qdt-date-cell">
            @if($grn->bill_date)
              <span class="qdt-date-sub">INV: {{ $grn->bill_date }}</span>
            @else
              <span class="qdt-dash">—</span>
            @endif
          </td>

          <td>
            @if($grn->zone_name)
              <span class="qdt-zone-badge {{ $zoneCls }}">{{ strtoupper($grn->zone_name) }}</span>
            @endif
            <div class="qdt-branch">{{ $grn->branch_name ?? '—' }}</div>
          </td>

          <td>
            <a class="print-pop-btn qdt-link" href="#">{{ $grn->grn_number ?? '—' }}</a>
          </td>

          <td class="qdt-mono">{{ $grn->order_number ?? '—' }}</td>

          <td class="qdt-vendor-link">{{ $grn->vendor_name ?? '—' }}</td>

          <td><span class="qdt-qc-user">{{ $grn->QcCheckedBy->user_fullname ?? '-'}}</span></td>

          <td><span class="{{ $statusBadge }}">{{ $statusText }}</span></td>

        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center py-4 text-muted">
            <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
            No GRN records found
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($grnlist->total() > 10)
<div class="qd-pagination">
  <div>{{ $grnlist->links('pagination::bootstrap-4') }}</div>
  <div>
    <form method="GET" id="perPageForm" class="d-flex align-items-center gap-2">
      <select name="per_page" id="per_page" class="form-control form-control-sm" style="width:80px;">
        @foreach([10, 25, 50, 100, 250, 500] as $size)
          <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
        @endforeach
      </select>
      <span style="font-size:12px; color:#8a94a6;">entries</span>
    </form>
  </div>
</div>
@endif
