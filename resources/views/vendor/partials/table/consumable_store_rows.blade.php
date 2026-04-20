<div class="qdt-wrap">
  <table class="qdt-table cs-store-table">
    <thead class="qdt-head">
      <tr>
        <th class="cs-col-grn">GRN NUMBER</th>
        <th class="cs-col-dept">DEPARTMENT</th>
        <th class="cs-col-zonebranch">ZONE / BRANCH</th>
        <th class="cs-col-company">COMPANY</th>
        <th class="cs-col-item">ITEM NAME</th>
        <!-- <th class="text-end cs-col-price">PRICE</th> -->
        <th class="text-end cs-col-qty">QTY</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($consumableStoreList as $row)
        @php
          $grn = $row->Grn;
          $zoneName = $grn ? (string) ($grn->zone_name ?? '') : '';
          $zoneRaw = strtolower($zoneName);
          $zoneCls = str_contains($zoneRaw, 'karnataka') ? 'qdt-zone-orange'
                   : (str_contains($zoneRaw, 'kerala')   ? 'qdt-zone-green'
                   : (str_contains($zoneRaw, 'inter')    ? 'qdt-zone-purple'
                   : 'qdt-zone-teal'));
        @endphp
        <tr class="qdt-row">
          <td class="qdt-mono cs-col-grn" style="font-weight:600;color:#1f2937;">{{ $row->grn_number ?? '—' }}</td>
          <td class="cs-col-dept" style="font-weight:500;color:#334155;">{{ optional($row->Department)->name ?? optional(optional($row->Grn)->Department)->name ?? '—' }}</td>
          <td class="cs-col-zonebranch">
            @if($zoneName !== '')
              <span class="qdt-zone-badge {{ $zoneCls }}">{{ strtoupper($zoneName) }}</span>
            @endif
            <div class="qdt-branch">{{ $grn ? ($grn->branch_name ?? '—') : '—' }}</div>
          </td>
          <td class="cs-col-company" style="font-weight:500;color:#334155;">{{ $grn ? ($grn->company_name ?? '—') : '—' }}</td>
          <td class="cs-col-item-cell" style="font-weight:500;color:#334155;" title="{{ e($row->item_name ?? '') }}">{{ $row->item_name ?? '—' }}</td>
          <!-- <td class="text-end cs-col-price" style="font-weight:600;">₹ {{ number_format((float) ($row->unit_price ?? 0), 2) }}</td> -->
          <td class="text-end cs-col-qty" style="font-weight:600;">{{ number_format((float) ($row->quantity ?? 0), 2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center py-4 text-muted">
            <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
            No {{ $moduleLabel ?? 'Consumable Store' }} records found. Save a GRN (Save as Open, not Draft) to post lines here.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($consumableStoreList->total() > 10)
<div class="qd-pagination">
  <div>{{ $consumableStoreList->links('pagination::bootstrap-4') }}</div>
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
