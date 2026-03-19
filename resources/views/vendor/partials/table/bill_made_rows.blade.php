<div class="qdt-wrap">
    <table class="qdt-table">
        <thead class="qdt-head">
            <tr>
                <th class="qdt-th-check"><input type="checkbox" id="selectAll"></th>
                <th>#</th>
                <th>DATE</th>
                <th>LOCATION</th>
                <th>PAY GEN #</th>
                <th>PAYMENT #</th>
                <th>REFERENCE #</th>
                <th>NATURE</th>
                <th>VENDOR</th>
                <th>BILL #</th>
                <th class="text-end">AMOUNT</th>
                <th>MODE</th>
                <th>STATUS</th>
                <th>NEFT</th>
                @if(isset($limit_access) && $limit_access == 1)
                <th class="text-center">HISTORY</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($billpaylist as $bill)
            @php
                $zoneRaw = strtolower($bill->zone_name ?? '');
                $zoneCls = str_contains($zoneRaw,'karnataka') ? 'qdt-zone-orange'
                         : (str_contains($zoneRaw,'kerala')   ? 'qdt-zone-green'
                         : (str_contains($zoneRaw,'inter')    ? 'qdt-zone-purple'
                         : 'qdt-zone-teal'));

                $bsStatus = strtolower($bill->bank_statement_status ?? '');
                $statusCls = match($bsStatus) {
                    'paid'      => 'qd-badge qd-badge-approved',
                    'partially' => 'qd-badge qd-badge-pending',
                    'pending'   => 'qd-badge qd-badge-rejected',
                    default     => 'qd-badge qd-badge-default',
                };

                $accounts = $bill->BillLines
                    ->flatMap(fn($line) => $line->BillLines->pluck('account'))
                    ->unique()->sort()->implode(', ');

                $billNumbers = $bill->BillLines
                    ->filter(fn($l) => $l->Bill && $l->Bill->bill_number)
                    ->map(fn($l) => $l->Bill->bill_number)
                    ->implode(', ');

                // Edit history
                $editHistory = [];
                if (!empty($bill->edit_history)) {
                    $decoded = json_decode($bill->edit_history, true);
                    if (is_array($decoded)) { $editHistory = $decoded; }
                }
            @endphp
            <tr class="qdt-row customer-row"
                data-id="{{ $bill->id }}"
                data-vendor_name="{{ $bill->vendor_name }}"
                data-payment="{{ $bill->payment }}"
                data-payment_made="{{ $bill->payment_made }}"
                data-payment_date="{{ $bill->payment_date }}"
                data-payment_mode="{{ $bill->payment_mode }}"
                data-paid_through="{{ $bill->paid_through }}"
                data-reference="{{ $bill->reference }}"
                data-amount_used="{{ $bill->amount_used }}"
                data-allbill='@json($bill)'
                data-bank='@json($bill->Tblbankdetails)'
                data-vendor-address='@json($bill->TblBilling)'
                data-vendor='@json($bill->Tblvendor)'
                data-items='@json($bill->BillLines)'>

                <td class="qdt-td-check"><input type="checkbox" class="row-check"></td>
                <td class="qdt-num">{{ $billpaylist->firstItem() + $loop->index }}</td>

                {{-- Date --}}
                <td class="qdt-date-cell">
                    <span class="qdt-date-main">{{ $bill->created_at->format('d M Y') }}</span>
                    @if($bill->payment_date)
                        <span class="qdt-date-sub">PAY: {{ $bill->payment_date }}</span>
                    @endif
                </td>

                {{-- Location --}}
                <td>
                    @if($bill->zone_name)
                        <span class="qdt-zone-badge {{ $zoneCls }}">{{ strtoupper($bill->zone_name) }}</span>
                    @endif
                    <div class="qdt-branch">{{ $bill->branch_name ?? '-' }}</div>
                </td>

                <td class="qdt-mono">{{ $bill->payment_gen_order ?? '-' }}</td>
                <td class="qdt-mono">{{ $bill->payment ?? '-' }}</td>
                <td class="qdt-mono">{{ $bill->reference ?? '-' }}</td>
                <td class="qdt-nature">{{ $accounts ?: '-' }}</td>

                {{-- Vendor --}}
                <td class="vendor_link">
                    <a class="vendor_link qdt-vendor-link" href="{{ route('superadmin.getvendor') }}?id={{ $bill->vendor_id }}">
                        <span title="{{ optional($bill->Tblvendor)->display_name }}">
                            {{ \Illuminate\Support\Str::limit(optional($bill->Tblvendor)->display_name, 20, '...') }}
                        </span>
                    </a>
                </td>

                <td class="qdt-mono">{{ \Illuminate\Support\Str::limit($billNumbers, 20, '...') }}</td>
                <td class="qdt-amount text-end">₹{{ number_format($bill->amount_used ?? 0, 2) }}</td>
                <td><span class="qdt-zone-badge qdt-zone-teal">{{ $bill->payment_mode ?? '-' }}</span></td>

                {{-- Status --}}
                <td><span class="{{ $statusCls }}">{{ ucfirst($bill->bank_statement_status ?? '-') }}</span></td>

                <td class="neft_modal" style="cursor:pointer;">
                    <span class="qdt-link"><i class="fas fa-money-check-alt me-1"></i>NEFT</span>
                </td>

                {{-- Edit History (admin only) --}}
                @if(isset($limit_access) && $limit_access == 1)
                <td class="text-center">
                    @if(count($editHistory) > 0)
                        <button class="qdt-history-btn"
                            title="View Edit History ({{ count($editHistory) }} edit{{ count($editHistory) > 1 ? 's' : '' }})"
                            data-history="{{ htmlspecialchars(json_encode($editHistory), ENT_QUOTES) }}"
                            data-qno="{{ $bill->payment_gen_order ?? $bill->payment ?? 'P#'.$bill->id }}">
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
@if($billpaylist->total() > 10)
<div class="qd-pagination">
    <div>{{ $billpaylist->links('pagination::bootstrap-4') }}</div>
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
