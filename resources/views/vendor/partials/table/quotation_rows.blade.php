<div class="qdt-wrap">
    <table class="qdt-table">
        <thead class="qdt-head">
            <tr>
                <th class="qdt-th-check"><input type="checkbox" id="selectAll"></th>
                <!-- <th>#</th> -->
                <th>DATE</th>
                <th>INVOICE DATE</th>
                <th>LOCATION</th>
                <th>Q-AUTO NO</th>
                <th>QUOTATION NO</th>
                <th>REF NO</th>
                <th>NATURE</th>
                <th>VENDOR</th>
                <th>DUE DATE</th>
                <th class="text-end">AMOUNT</th>
                <th>ATTACH</th>
                <th>STATUS</th>
                <th>QO STATUS</th>
                <th>ACTION</th>
                @if(isset($limit_access) && $limit_access == 1)
                <th class="text-center">HISTORY</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($quotationlist as $i => $quotation)
            @php
                $zoneRaw  = strtolower($quotation->zone_name ?? '');
                $zoneCls  = str_contains($zoneRaw,'karnataka') ? 'qdt-zone-orange'
                          : (str_contains($zoneRaw,'kerala')   ? 'qdt-zone-green'
                          : (str_contains($zoneRaw,'inter')    ? 'qdt-zone-purple'
                          : 'qdt-zone-teal'));

                $status   = strtolower($quotation->status ?? '');
                $badgeCls = match($status) {
                    'save'  => 'qd-badge qd-badge-save',
                    'draft' => 'qd-badge qd-badge-draft',
                    default => 'qd-badge qd-badge-default',
                };

                // Parse edit history safely
                $editHistory = [];
                if (!empty($quotation->edit_history)) {
                    $decoded = json_decode($quotation->edit_history, true);
                    if (is_array($decoded)) {
                        $editHistory = $decoded;
                    }
                }
            @endphp
            <tr class="qdt-row customer-row"
                data-id="{{ $quotation->id }}"
                data-purchase_order_number="{{ $quotation->purchase_order_number }}"
                data-delivery_address="{{ $quotation->delivery_address }}"
                data-order-number="{{ $quotation->order_number }}"
                data-vendor-name="{{ $quotation->vendor_name }}"
                data-vendor-address='@json($quotation->TblBilling)'
                data-vendor='@json($quotation->Tblvendor)'
                data-quotation='@json($quotation)'
                data-approval_status="{{ $quotation->approval_status }}"
                data-bill-date="{{ $quotation->bill_date }}"
                data-due-date="{{ $quotation->due_date }}"
                data-payment-terms="{{ $quotation->payment_terms }}"
                data-discount_amount="{{ $quotation->discount_amount }}"
                data-grand-total="{{ $quotation->grand_total_amount }}"
                data-sub-total="{{ $quotation->sub_total_amount }}"
                data-note="{{ $quotation->note ?? 'No notes' }}"
                data-items='@json($quotation->BillLines)'>

                {{-- Checkbox --}}
                <td class="qdt-td-check"><input type="checkbox" class="row-check"></td>

                {{-- Row number --}}
                <!-- <td class="qdt-num">{{ $quotationlist->firstItem() + $loop->index }}</td> -->

                {{-- Date --}}
                <td class="qdt-date-cell">
                    <span class="qdt-date-main">{{ $quotation->created_at->format('d M Y') }}</span>
                   
                </td>

                {{-- Date --}}
                <td class="qdt-date-cell">
                    @if($quotation->bill_date)
                        <span class="qdt-date-sub">INV: {{ $quotation->bill_date }}</span>
                    @endif
                </td>

                {{-- Location: zone badge + branch --}}
                <td>
                    @if($quotation->zone_name)
                        <span class="qdt-zone-badge {{ $zoneCls }}">{{ strtoupper($quotation->zone_name) }}</span>
                    @endif
                    <div class="qdt-branch">{{ $quotation->branch_name ?? '-' }}</div>
                </td>

                {{-- Q-Auto No --}}
                <td>
                    <a class="print-pop-btn qdt-link" href="#">{{ $quotation->quotation_gen_no ?? '-' }}</a>
                </td>

                {{-- Quotation No --}}
                <td class="qdt-mono">{{ $quotation->quotation_no ?? '-' }}</td>

                {{-- Reference No --}}
                <td class="qdt-mono">{{ $quotation->order_number ?? '-' }}</td>

                {{-- Nature of Payment --}}
                <td class="qdt-nature">{{ $quotation->BillLines->pluck('account')->unique()->sort()->implode(', ') }}</td>

                {{-- Vendor --}}
                <td class="vendor_link">
                    <a class="vendor_link qdt-vendor-link" href="{{ route('superadmin.getvendor') }}?id={{ $quotation->vendor_id }}">
                        <span title="{{ optional($quotation->Tblvendor)->display_name }}">
                            {{ \Illuminate\Support\Str::limit(optional($quotation->Tblvendor)->display_name, 20, '...') }}
                        </span>
                    </a>
                </td>

                {{-- Due Date --}}
                <td class="qdt-due">{{ $quotation->due_date ?? '-' }}</td>

                {{-- Amount --}}
                <td class="qdt-amount text-end">₹{{ number_format($quotation->grand_total_amount ?? 0, 2) }}</td>

                {{-- Attachment --}}
                @php
                    $rawDocs = $quotation->documents;
                    $docs = !empty($rawDocs) ? json_decode($rawDocs, true) : [];
                    if (!is_array($docs)) { $docs = []; }
                    $basePath = '../public/uploads/vendor/bill/';
                    $docsWithPath = array_map(fn($f) => $basePath . ltrim($f, '/'), $docs);
                    $encodedDocs = htmlspecialchars(json_encode($docsWithPath), ENT_QUOTES);
                @endphp
                <td class="doc-row qdt-attach" data-filetype="documents" data-files="{{ $encodedDocs }}" style="cursor:pointer;">
                    @if(!empty($docs))
                        <i class="fa fa-paperclip qdt-clip"></i>
                    @else
                        <span class="qdt-dash">—</span>
                    @endif
                </td>

                {{-- Status --}}
                <td><span class="{{ $badgeCls }}">{{ ucfirst($quotation->status ?? '-') }}</span></td>

                {{-- QO Status --}}
                <td>
                    @if($quotation->approval_status == 0 && $quotation->reject_status == 0)
                        <span class="qd-badge qd-badge-pending">Pending</span>
                    @elseif($quotation->approval_status == 1)
                        <span class="qd-badge qd-badge-approved">Approved</span>
                    @elseif($quotation->reject_status == 1)
                        <span class="qd-badge qd-badge-rejected">Rejected</span>
                    @endif
                </td>

                {{-- Action --}}
                <td class="appr_center" style="white-space:nowrap;">
                    @if($quotation->approval_status == 0 && $quotation->reject_status == 0)
                        <button class="qd-action-btn qd-action-approve approver" data-value="Approve" data-id="{{ $quotation->id }}" title="Approve">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        <button class="qd-action-btn qd-action-reject approver" data-value="Reject" data-id="{{ $quotation->id }}" title="Reject">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    @elseif($quotation->approval_status == 1)
                        <span class="qdt-done-approved"><i class="bi bi-check-circle-fill"></i></span>
                    @elseif($quotation->reject_status == 1)
                        <span class="qdt-done-rejected"><i class="bi bi-x-circle-fill"></i></span>
                    @endif
                </td>

                {{-- Edit History (admin only) --}}
                @if(isset($limit_access) && $limit_access == 1)
                <td class="text-center">
                    @if(count($editHistory) > 0)
                    <button class="qdt-history-btn"
                        title="View Edit History ({{ count($editHistory) }} edit{{ count($editHistory) > 1 ? 's' : '' }})"
                        data-history='@json($editHistory)'
                        data-qno="{{ $quotation->quotation_gen_no ?? $quotation->quotation_no ?? 'Q#'.$quotation->id }}">
                        
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

<!-- Pagination -->
@if($quotationlist->total() > 10)
<div class="qd-pagination">
    <div>{{ $quotationlist->links('pagination::bootstrap-4') }}</div>
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
