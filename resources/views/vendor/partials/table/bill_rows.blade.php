<div class="qdt-wrap">
<div class="d-flex justify-content-end mb-2" style="padding:0 4px;">
    <button id="printSelected" class="btn btn-sm btn-outline-secondary">
        <i class="fa fa-print me-1"></i> Print Selected
    </button>
</div>
<table class="qdt-table">
    <thead class="qdt-head">
    <tr>
        <th class="qdt-th-check">
            <input type="checkbox" id="selectAll" />
        </th>
        <th>DATE</th>
        <th>INVOICE DATE</th>
        <th>LOCATION</th>
        <th>BILL GEN NO</th>
        <th>PO-NO</th>
        <th>Q-NO</th>
        <th>BILL #</th>
        <th>REF NO</th>
        <th>Bill Category</th>
        <th>Department</th>
        <th>NATURE</th>
        <th>VENDOR</th>
        <th>OTHER REASON</th>
        <th>DUE DATE</th>
        <th class="text-end">AMOUNT</th>
        <th>ATTACH</th>
        <th>STATUS</th>
        <th>PAID STATUS</th>
        <th>ASSET</th>
        @if(isset($limit_access) && $limit_access == 1)
        <th class="text-center">HISTORY</th>
        @endif
    </tr>
    </thead>
    <tbody>
        @foreach ($billlist as $bill)
        @php
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

            $billStatus = strtolower($bill->bill_status ?? '');
            $paidBadge  = match(true) {
                str_contains($billStatus, 'paid') && !str_contains($billStatus, 'partial') => 'qd-badge qd-badge-approved',
                str_contains($billStatus, 'partial') => 'qd-badge qd-badge-pending',
                str_contains($billStatus, 'due')     => 'qd-badge qd-badge-rejected',
                default => 'qd-badge qd-badge-default',
            };

            // Overdue highlight
            $isOverdue = false;
            try {
                if (!empty($bill->due_date)) {
                    $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $bill->due_date)->startOfDay();
                    $isOverdue = strtolower($bill->bill_status ?? '') !== 'paid' && $dueDate->lt(now()->startOfDay());
                }
            } catch(\Exception $e) {}

            // Edit history
            $editHistory = [];
            if (!empty($bill->edit_history)) {
                $decoded = json_decode($bill->edit_history, true);
                if (is_array($decoded)) { $editHistory = $decoded; }
            }
        @endphp
        <tr class="qdt-row customer-row {{ $isOverdue ? 'qdt-overdue-row' : '' }}"
            data-id="{{ $bill->id }}"
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
            data-po-id="{{ $bill->Purchase->id ?? '' }}"
            data-quot-id="{{ $bill->Purchase->quotation->id ?? '' }}"
            data-billshow='@json($bill->billPayments)'
            data-items='@json($bill->BillLines)'>

            <td class="qdt-td-check"><input type="checkbox" class="bill-checkbox" /></td>

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

            <td><a class="print-pop-btn" style="color:#2a6fdb;font-weight:600;">{{ $bill->bill_gen_number ?? '-' }}</a></td>
            <td><a class="print-po-pop-btn" style="color:#2a6fdb;">{{ $bill->Purchase->purchase_gen_order ?? '-' }}</a></td>
            <td><a class="print-quot-pop-btn" style="color:#2a6fdb;">{{ $bill->Purchase->quotation->quotation_gen_no ?? '-' }}</a></td>
            <td>{{ $bill->bill_number ?? '-' }}</td>
            <td>{{ $bill->order_number ?? '-' }}</td>
            <td>{{ $bill->category->name ?? '-' }}</td>
            <td>{{ $bill->department->name ?? '-' }}</td>
            <td>{{ $bill->BillLines->pluck('account')->unique()->sort()->implode(', ') }}</td>

            <td class="vendor_link">
                <a class="vendor_link" href="{{ route('superadmin.getvendor') }}?id={{ $bill->vendor_id }}" style="color:#2a6fdb;">
                    <span title="{{ optional($bill->Tblvendor)->display_name }}">
                        {{ \Illuminate\Support\Str::limit(optional($bill->Tblvendor)->display_name, 20, '...') }}
                    </span>
                </a>
            </td>

            <td>
                <span title="{{ $bill->other_reason }}">
                    {{ $bill->other_reason ? \Illuminate\Support\Str::limit($bill->other_reason, 20, '...') : '-' }}
                </span>
            </td>

            <td style="{{ $isOverdue ? 'color:#dc2626;font-weight:700;' : '' }}">
                {{ $bill->due_date ?? '-' }}
                @if($isOverdue)
                    <span class="qd-badge qd-badge-rejected ms-1" style="font-size:9px;">Overdue</span>
                @endif
            </td>

            <td class="text-end" style="font-weight:600;">₹{{ number_format($bill->grand_total_amount ?? 0, 2) }}</td>

            @php
                $rawDocs = $bill->documents;
                $docs = !empty($rawDocs) ? json_decode($rawDocs, true) : [];
                if (!is_array($docs)) { $docs = []; }
                $basePath = '../public/uploads/vendor/bill/';
                $docsWithPath = array_map(fn($f) => $basePath . ltrim($f, '/'), $docs);
                $encodedDocs = htmlspecialchars(json_encode($docsWithPath), ENT_QUOTES);
            @endphp
            <td class="doc-row qdt-attach" data-filetype="documents" data-files="{{ $encodedDocs }}" style="cursor:pointer;text-align:center;">
                @if(!empty($docs))
                    <i class="fa fa-paperclip qdt-clip" style="color:#2a6fdb;"></i>
                @else
                    <span class="qdt-dash">—</span>
                @endif
            </td>

            <td><span class="{{ $badgeCls }}">{{ ucfirst($bill->status ?? '-') }}</span></td>

            <td><span class="{{ $paidBadge }}">{{ ucfirst($bill->bill_status ?? '-') }}</span></td>

            <td id="asset-status" style="text-align:center;">
                @if ($bill->asset_status == 0)
                    <button class="btn btn-sm btn-primary asset-btn">Asset</button>
                @else
                    <i class="bi bi-check-circle-fill text-success"></i>
                @endif
            </td>

            @if(isset($limit_access) && $limit_access == 1)
            <td class="text-center">
                @if(count($editHistory) > 0)
                <button class="qdt-history-btn"
                    title="View Edit History ({{ count($editHistory) }} edit{{ count($editHistory) > 1 ? 's' : '' }})"
                    data-history='@json($editHistory)'
                    data-qno="{{ $bill->bill_gen_number ?? $bill->bill_number ?? 'B#'.$bill->id }}">
                    <i class="bi bi-clock-history"></i>
                    <span class="qdt-history-count">{{ count($editHistory) }}</span>
                </button>
                @else
                    <span class="qdt-no-history" title="No edits yet"><i class="bi bi-dash-circle"></i></span>
                @endif
            </td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
</div>

{{-- Pagination --}}
@if($billlist->total() > 10)
<div class="qd-pagination">
    <div>{{ $billlist->links('pagination::bootstrap-4') }}</div>
    <div>
        <form method="GET" id="perPageForm" class="d-flex align-items-center gap-2">
            <select name="per_page" id="per_page" class="form-control form-control-sm" style="width:80px;">
                @foreach([10, 25, 50, 100, 250, 500] as $size)
                    <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
            <span style="font-size:12px;color:#8a94a6;">entries</span>
        </form>
    </div>
</div>
@endif
