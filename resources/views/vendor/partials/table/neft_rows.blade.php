<div class="qdt-wrap">
<table class="qdt-table">
    <thead class="qdt-head">
        <tr>
            <th style="width:36px;"><input type="checkbox" id="selectAll" /></th>
            <th>#</th>
            <th>DATE</th>
            <th>LOCATION</th>
            <th>SERIAL NO</th>
            <th>CREATED BY</th>
            <th>VENDOR</th>
            <th>NATURE</th>
            <th>PAYMENT</th>
            <th>UTR</th>
            <th>BILL / PO / Q</th>
            <th style="text-align:right;">AMOUNT</th>
            <th>TDS</th>
            <th style="text-align:right;">GST AMT</th>
            <th style="text-align:right;">BALANCE</th>
            <th>CHECK</th>
            <th>APPROVE</th>
            @if(isset($limit_access) && $limit_access == 1)
            <th>HISTORY</th>
            @endif
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($purchaselist as $bill)
        @php
            $amount    = 0;
            $taxamount = 0;
            $balance   = 0;
            $gstamount = 0;
            $gstsection = [];
            $tdssection = [];
            foreach ($bill->BillLines as $line) {
                foreach ($line->Tblbilllines as $bl) {
                    $gstamount += (float)$bl->gst_amount;
                    if (!empty($bl->gst_name)) $gstsection[] = $bl->gst_name;
                }
            }
            foreach ($bill->BillLines as $line) {
                $amount    += (float)$line->invoice_amount;
                $taxamount += (float)$line->tax_amount;
                $balance   += (float)$line->only_payable;
                if (!empty($line->tds_tax_name)) $tdssection[] = $line->tds_tax_name;
            }
            $firstLine = $bill->BillLines->first();
            $billNo    = optional(optional($firstLine)->Bill)->bill_gen_number ?? '-';
            $billId    = optional(optional($firstLine)->Bill)->id ?? '-';
            $poNo      = optional(optional(optional($firstLine)->Bill)->Purchase)->purchase_gen_order ?? '-';
            $poId      = optional(optional(optional($firstLine)->Bill)->Purchase)->id ?? '-';
            $qNo       = optional(optional(optional(optional($firstLine)->Bill)->Purchase)->quotation)->quotation_gen_no ?? '-';
            $qId       = optional(optional(optional(optional($firstLine)->Bill)->Purchase)->quotation)->id ?? '-';

            $zoneName = $bill->zone_name ?? '';
            $zoneClass = 'qdt-zone-teal';
            $zl = strtolower($zoneName);
            if (str_contains($zl,'chennai') || str_contains($zl,'tn') || str_contains($zl,'tamil')) $zoneClass = 'qdt-zone-orange';
            elseif (str_contains($zl,'karnat') || str_contains($zl,'bang')) $zoneClass = 'qdt-zone-purple';
            elseif (str_contains($zl,'kerala')) $zoneClass = 'qdt-zone-green';

            $rowNum = ($purchaselist->currentPage() - 1) * $purchaselist->perPage() + $loop->iteration;

            $editHistory = [];
            if (isset($bill->edit_history) && !empty($bill->edit_history)) {
                $editHistory = is_array($bill->edit_history) ? $bill->edit_history : (json_decode($bill->edit_history, true) ?: []);
            }
            $historyCount = count($editHistory);
        @endphp
        <tr class="qdt-row customer-row"
            data-id="{{ $bill->id }}"
            data-quot-id="{{ $qId }}"
            data-po-id="{{ $poId }}"
            data-bill-id="{{ $billId }}"
            data-items='@json($bill)'>
            <td><input type="checkbox" class="row-checkbox" value="{{ $bill->id }}" /></td>
            <td class="qdt-num">{{ $rowNum }}</td>
            <td class="qdt-date-cell">
                <div>{{ $bill->created_at->format('d/m/Y') }}</div>
                @if($bill->bill_date)
                <div class="qdt-date-sub">{{ $bill->bill_date }}</div>
                @endif
            </td>
            <td>
                @if($zoneName)
                <span class="qdt-zone-badge {{ $zoneClass }}">{{ $zoneName }}</span>
                @endif
                <div class="qdt-branch">{{ $bill->branch_name ?? '-' }}</div>
            </td>
            <td class="qdt-mono">{{ $bill->serial_number ?? '-' }}</td>
            <td>{{ $bill->created_by ?? '-' }}</td>
            <td>
                <a class="qdt-vendor-link" href="{{ route('superadmin.getvendor') }}?id={{ $bill->vendor_id }}"
                   title="{{ optional($bill->Tblvendor)->display_name }}">
                    {{ \Illuminate\Support\Str::limit(optional($bill->Tblvendor)->display_name, 18, '…') }}
                </a>
            </td>
            <td class="qdt-nature">{{ \Illuminate\Support\Str::limit($bill->nature_payment ?? '-', 14, '…') }}</td>
            <td>
                <span class="qdt-zone-badge qdt-zone-teal">{{ $bill->payment_method ?? '-' }}</span>
            </td>
            <td class="qdt-mono" style="font-size:11px;">{{ $bill->utr_number ?? '-' }}</td>
            <td style="font-size:11px;line-height:1.6;">
                <a class="print-pop-btn qdt-link" data-id="{{ $billId }}">{{ $billNo }}</a><br>
                <a class="print-po-pop-btn qdt-link" data-id="{{ $poId }}">{{ $poNo }}</a><br>
                <a class="print-quot-pop-btn qdt-link" data-id="{{ $qId }}">{{ $qNo }}</a>
            </td>
            <td class="qdt-amount" style="text-align:right;">₹{{ number_format($amount,2) }}</td>
            <td style="font-size:11px;" title="{{ implode(', ',$tdssection) }}">
                {{ !empty($tdssection) ? \Illuminate\Support\Str::limit(implode(', ',$tdssection),10,'…') : '-' }}
                @if($taxamount > 0)<br><span style="color:#6366f1;font-weight:600;">₹{{ number_format($taxamount,2) }}</span>@endif
            </td>
            <td style="text-align:right;font-size:11px;" title="{{ implode(', ',$gstsection) }}">
                ₹{{ number_format($gstamount,2) }}
            </td>
            <td style="text-align:right;" class="qdt-amount">₹{{ number_format($balance,2) }}</td>
            <td style="text-align:center;">
                @if($bill->checker_status == 1)
                    <span style="color:#10b981;font-size:16px;">✔</span>
                @else
                    <span style="color:#ef4444;font-size:16px;">✘</span>
                @endif
            </td>
            <td style="text-align:center;">
                @if($bill->approval_status == 1)
                    <span style="color:#10b981;font-size:16px;">✔</span>
                @else
                    <span style="color:#ef4444;font-size:16px;">✘</span>
                @endif
            </td>
            @if(isset($limit_access) && $limit_access == 1)
            <td>
                @if($historyCount > 0)
                    <button class="qdt-history-btn"
                        data-history='@json($editHistory)'
                        data-qno="{{ $bill->serial_number ?? $bill->id }}">
                        <i class="bi bi-clock-history"></i>
                        <span class="qdt-history-count">{{ $historyCount }}</span>
                    </button>
                @else
                    <span class="qdt-no-history">—</span>
                @endif
            </td>
            @endif
            <td>
                @if($bill->checker_status == 0)
                    @if(isset($limit_access) && ($limit_access == 1 || $limit_access == 2))
                        <button class="btn btn-warning btn-sm check" data-id="{{ $bill->id }}">Check</button>
                    @endif
                @elseif($bill->approval_status == 0)
                    @if(isset($limit_access) && $limit_access == 1)
                        <button class="btn btn-success btn-sm approver" data-id="{{ $bill->id }}">Approve</button>
                    @endif
                @else
                    <button class="btn btn-success btn-sm" disabled>Approved</button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

@if($purchaselist->total() > 10)
<div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-top:1px solid #e8ecf5;">
    <div>{{ $purchaselist->links('pagination::bootstrap-4') }}</div>
    <div>
        <select name="per_page" id="per_page" class="form-control form-control-sm" style="width:70px;display:inline-block;">
            @foreach([10,25,50,100,250,500] as $size)
                <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
            @endforeach
        </select>
        <span style="font-size:12px;color:#7b8ab0;">entries</span>
    </div>
</div>
@endif
