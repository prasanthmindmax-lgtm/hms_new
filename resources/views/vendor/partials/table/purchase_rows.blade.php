<div class="qdt-wrap">
  <table class="qdt-table">
      <thead class="qdt-head">
      <tr>
          <th class="qdt-th-check"><input type="checkbox" id="selectAll"></th>
          <th>DATE</th>
          <th>INVOICE DATE</th>
          <th class="sortable" data-column="location">
            LOCATION
            <span class="sort-icons">
                <i class="fa fa-sort-up"></i>
                <i class="fa fa-sort-down"></i>
            </span>
          </th>
          <th class="sortable" data-column="po_number">
                PO Number
                <span class="sort-icons">
                    <i class="fa fa-sort-up"></i>
                    <i class="fa fa-sort-down"></i>
                </span>
          </th>
          <th>Q-Number</th>
          <th>PURCHASE RELATION</th>
          <th>REFERENCE NUMBER</th>
          <th>VENDOR NAME</th>
          <th>DUE DATE</th>
          <th class="text-end">AMOUNT</th>
          <th>ATTACH</th>
          <th>STATUS</th>
          <th>PO STATUS</th>
          <th>ACTION</th>
          @if(isset($limit_access) && $limit_access == 1)
          <th class="text-center">HISTORY</th>
          @endif
      </tr>
      </thead>
      <tbody>
          @foreach ($purchaselist as $bill)
            @php
                $BillLines = $bill->BillLines;

                $zoneRaw  = strtolower($bill->zone_name ?? '');
                $zoneCls  = str_contains($zoneRaw,'karnataka') ? 'qdt-zone-orange'
                          : (str_contains($zoneRaw,'kerala')   ? 'qdt-zone-green'
                          : (str_contains($zoneRaw,'inter')    ? 'qdt-zone-purple'
                          : 'qdt-zone-teal'));

                $status   = strtolower($bill->status ?? '');
                $badgeCls = match($status) {
                    'save'  => 'qd-badge qd-badge-save',
                    'draft' => 'qd-badge qd-badge-draft',
                    default => 'qd-badge qd-badge-default',
                };

                $editHistory = [];
                if (!empty($bill->edit_history)) {
                    $decoded = json_decode($bill->edit_history, true);
                    if (is_array($decoded)) {
                        $editHistory = $decoded;
                    }
                }
            @endphp
              <tr class="qdt-row customer-row"
                  data-id="{{ $bill->id }}"
                  data-purchase_order_number="{{ $bill->purchase_order_number }}"
                  data-delivery_address="{{ $bill->delivery_address }}"
                  data-order-number="{{ $bill->order_number }}"
                  data-vendor-name="{{ $bill->vendor_name }}"
                  data-vendor-address='@json($bill->TblBilling)'
                  data-vendor='@json($bill->Tblvendor)'
                  data-purchase_all='@json($bill)'
                  data-bill-date="{{ $bill->bill_date }}"
                  data-due-date="{{ $bill->due_date }}"
                  data-approval_status="{{ $bill->approval_status }}"
                  data-payment-terms="{{ $bill->payment_terms }}"
                  data-discount_amount="{{ $bill->discount_amount }}"
                  data-grand-total="{{ $bill->grand_total_amount }}"
                  data-sub-total="{{ $bill->sub_total_amount }}"
                  data-note="{{ $bill->note ?? 'No notes' }}"
                  data-quot-id="{{ $bill->quotation->id ?? '' }}"
                  data-items='@json($bill->BillLines)'>

                  <td class="qdt-td-check"><input type="checkbox" class="row-check" /></td>

                  <td class="qdt-date-cell">
                      <span class="qdt-date-main">{{ $bill->created_at->format('d M Y') }}</span>
                  </td>
                  <td>{{ $bill->bill_date ?? '-' }}</td>

                  <td>
                      <span class="qdt-zone-badge {{ $zoneCls }}">{{ $bill->zone_name ?? '-' }}</span>
                      @if($bill->branch_name)
                          <div style="font-size:11px;color:#888;margin-top:2px;">{{ $bill->branch_name }}</div>
                      @endif
                  </td>

                  <td><a class="print-pop-btn" style="color:#2a6fdb;font-weight:600;">{{ $bill->purchase_gen_order ?? '-' }}</a></td>
                  <td><a class="print-quot-pop-btn" style="color:#2a6fdb;">{{ $bill->quotation->quotation_gen_no ?? '-' }}</a></td>
                  <td>{{ $bill->BillLines->pluck('account')->unique()->sort()->implode(', ') }}</td>
                  <td>{{ $bill->order_number ?? '-' }}</td>

                  <td class="vendor_link">
                    <a class="vendor_link" href="{{ route('superadmin.getvendor') }}?id={{ $bill->vendor_id }}" style="color:#2a6fdb;">
                        <span title="{{ optional($bill->Tblvendor)->display_name }}">
                            {{ \Illuminate\Support\Str::limit(optional($bill->Tblvendor)->display_name, 20, '...') }}
                        </span>
                    </a>
                  </td>

                  <td>{{ $bill->due_date ?? '-' }}</td>
                  <td class="text-end" style="font-weight:600;">₹{{ number_format($bill->grand_total_amount ?? 0, 2) }}</td>

                  @php
                      $docs = json_decode($bill->documents, true) ?? [];
                      $basePath = '../public/uploads/vendor/bill/';
                      $docsWithPath = array_map(fn($f) => $basePath.$f, $docs);
                      $encodedDocs = htmlspecialchars(json_encode($docsWithPath), ENT_QUOTES);
                  @endphp
                  <td class="doc-row"
                      data-filetype="documents"
                      data-files="{{ $encodedDocs }}"
                      style="cursor:pointer;text-align:center;">
                      @if(!empty($docs))
                          <i class="fa fa-paperclip" style="color:#2a6fdb;"></i>
                      @else
                          -
                      @endif
                  </td>

                  <td>
                      <span class="{{ $badgeCls }}">{{ ucfirst($bill->status ?? '-') }}</span>
                  </td>

                  <td>
                      @if($bill->approval_status == 0 && $bill->reject_status == 0)
                          <span class="qd-badge qd-badge-pending">Pending</span>
                      @elseif($bill->approval_status == 1)
                          <span class="qd-badge qd-badge-approved">Approved</span>
                      @elseif($bill->reject_status == 1)
                          <span class="qd-badge qd-badge-rejected">Rejected</span>
                      @endif
                  </td>

                  <td class="appr_center">
                    @if($bill->approval_status == 0 && $bill->reject_status == 0)
                        <span class="btn approver" data-value="Approve" data-id="{{ $bill->id }}" title="Approve" style="color:green;">✔️</span>
                        <span class="btn approver" data-value="Reject" data-id="{{ $bill->id }}" title="Reject" style="color:red;">❌</span>
                    @elseif($bill->approval_status == 1)
                        <span style="color:green;">✔️</span>
                    @elseif($bill->reject_status == 1)
                        <span style="color:red;">❌</span>
                    @endif
                  </td>

                  @if(isset($limit_access) && $limit_access == 1)
                  <td class="text-center">
                      <button class="qdt-history-btn btn btn-sm btn-outline-secondary"
                          data-history='@json($editHistory)'
                          data-qno="{{ $bill->purchase_gen_order ?? $bill->id }}"
                          title="Edit History">
                          <i class="bi bi-clock-history"></i>
                          @if(count($editHistory) > 0)
                              <span class="badge bg-secondary ms-1">{{ count($editHistory) }}</span>
                          @endif
                      </button>
                  </td>
                  @endif
              </tr>
          @endforeach
      </tbody>
  </table>
</div>

{{-- Pagination --}}
@if($purchaselist->total() > 10)
<div class="d-flex justify-content-between align-items-center mt-3 px-1">
    <div>
        {{ $purchaselist->links('pagination::bootstrap-4') }}
    </div>
    <div>
        <form method="GET" id="perPageForm">
            <select name="per_page" id="per_page" class="form-control form-control-sm" style="width:70px;display:inline-block;">
                @foreach([10, 25, 50, 100, 250, 500] as $size)
                    <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
            <span>entries</span>
        </form>
    </div>
</div>
@endif
