@php
    $summary     = $summary ?? ['total_discount' => 0, 'total_cancel' => 0, 'total_refund' => 0];
    $totalCash   = 0; $totalCard   = 0; $totalCheque = 0;
    $totalDd     = 0; $totalNeft   = 0; $totalCredit = 0;
    $totalUpi    = 0; $totalDisc   = 0; $totalCancel = 0;
    $totalRefund = 0; $grandTotal  = 0;

    $rowBase = 0;
    if (method_exists($incomeData, 'currentPage') && method_exists($incomeData, 'perPage')) {
        $rowBase = max(0, ((int) $incomeData->currentPage() - 1) * (int) $incomeData->perPage());
    }
@endphp

<style>
/* Force real table layout (theme CSS must not use flex on tr/td) */
.it-table { width:100%; border-collapse:collapse; table-layout:auto; }
.is-table-card .it-table thead tr,
.is-table-card .it-table tbody tr { display:table-row !important; }
.is-table-card .it-table thead th,
.is-table-card .it-table tbody td { display:table-cell !important; vertical-align:middle !important; }
.it-table thead th {
    padding:12px 10px; text-align:left; font-weight:700;
    color:#e74c3c; border-bottom:2px solid #ecf0f1;
    font-size:13px; white-space:nowrap; background:#fff;
}
.it-table tbody tr   { border-bottom:1px solid #ecf0f1; transition:background .15s; }
.it-table tbody tr:hover { background:#fafafa; }
.it-table tbody td   { padding:11px 10px; font-size:13px; color:#e74c3c; }

/* Clickable cells */
td.it-link {
    cursor:pointer; text-decoration:underline dotted 1px;
    transition:color .15s, background .15s; border-radius:3px;
}
td.it-link:hover { color:#2563eb !important; background:#eff6ff; }

/* Column colour classes */
td.it-loc    { color:#1e293b !important; font-weight:600; }
td.it-disc   { color:#e67e22 !important; font-weight:600; }
td.it-cancel { color:#c0392b !important; font-weight:600; }
td.it-refund { color:#8e44ad !important; font-weight:600; }
td.it-total  { color:#27ae60 !important; font-weight:700; }

/* Footer row */
.it-footer td {
    background:#f8f9fa !important; font-weight:700;
    padding:12px 10px; font-size:13px; border-top:2px solid #ecf0f1;
    color:#e74c3c;
}
.it-footer td.it-disc   { color:#e67e22 !important; }
.it-footer td.it-cancel { color:#c0392b !important; }
.it-footer td.it-refund { color:#8e44ad !important; }
.it-footer td.it-total  { color:#27ae60 !important; }
</style>

@if($incomeData->count() > 0)
<div class="table-responsive">
<table class="it-table">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Location</th>
            <th>Cash</th>
            <th>Card</th>
            <th>Cheque</th>
            <th>DD</th>
            <th>Neft</th>
            <th>Credit</th>
            <th>UPI</th>
            <th>Discount</th>
            <th>Cancel</th>
            <th>Refund</th>
            <th title="Sum of Cash through UPI only; not including Discount, Cancel, or Refund">Total <span style="font-weight:500;font-size:11px;color:#94a3b8;">(pay)</span></th>
        </tr>
    </thead>
    <tbody>
    @foreach($incomeData as $i => $row)
        @php
            $locId  = $row->location_id ?? '';
            $locNm  = $row->location_name ?? '';
            $cash   = (float)($row->cash   ?? 0);
            $card   = (float)($row->card   ?? 0);
            $cheque = (float)($row->cheque ?? 0);
            $dd     = (float)($row->dd     ?? 0);
            $neft   = (float)($row->neft   ?? 0);
            $credit = (float)($row->credit ?? 0);
            $upi    = (float)($row->upi    ?? 0);
            $disc   = (float)($row->discount   ?? 0);
            $cancel = (float)($row->cancel_amt ?? 0);
            $refund = (float)($row->refund_amt ?? 0);
            $line   = $cash + $card + $cheque + $dd + $neft + $credit + $upi;

            $totalCash   += $cash;   $totalCard   += $card;
            $totalCheque += $cheque; $totalDd     += $dd;
            $totalNeft   += $neft;   $totalCredit += $credit;
            $totalUpi    += $upi;    $totalDisc   += $disc;
            $totalCancel += $cancel; $totalRefund += $refund;
            $grandTotal  += $line;
        @endphp
        <tr>
            <td>{{ $rowBase + $i + 1 }}</td>

            {{-- Location — click = all bills for this branch --}}
            <td class="it-loc it-link"
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type=""
                title="View all bills – {{ $locNm }}">
                {{ $locNm }}
            </td>

            {{-- Cash --}}
            <td class="{{ $cash > 0 ? 'it-link' : '' }}"
                @if($cash > 0)
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type="Cash"
                title="Cash bills – {{ $locNm }}"
                @endif>
                {{ formatIndianMoney($cash) }}
            </td>

            {{-- Card --}}
            <td class="{{ $card > 0 ? 'it-link' : '' }}"
                @if($card > 0)
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type="Card"
                title="Card bills – {{ $locNm }}"
                @endif>
                {{ formatIndianMoney($card) }}
            </td>

            {{-- Cheque --}}
            <td class="{{ $cheque > 0 ? 'it-link' : '' }}"
                @if($cheque > 0)
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type="Cheque"
                title="Cheque bills – {{ $locNm }}"
                @endif>
                {{ formatIndianMoney($cheque) }}
            </td>

            {{-- DD --}}
            <td class="{{ $dd > 0 ? 'it-link' : '' }}"
                @if($dd > 0)
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type="DD"
                title="DD bills – {{ $locNm }}"
                @endif>
                {{ formatIndianMoney($dd) }}
            </td>

            {{-- Neft --}}
            <td class="{{ $neft > 0 ? 'it-link' : '' }}"
                @if($neft > 0)
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type="Neft"
                title="Neft bills – {{ $locNm }}"
                @endif>
                {{ formatIndianMoney($neft) }}
            </td>

            {{-- Credit --}}
            <td class="{{ $credit > 0 ? 'it-link' : '' }}"
                @if($credit > 0)
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type="Credit"
                title="Credit bills – {{ $locNm }}"
                @endif>
                {{ formatIndianMoney($credit) }}
            </td>

            {{-- UPI --}}
            <td class="{{ $upi > 0 ? 'it-link' : '' }}"
                @if($upi > 0)
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type="UPI"
                title="UPI bills – {{ $locNm }}"
                @endif>
                {{ formatIndianMoney($upi) }}
            </td>

            {{-- Discount — approved discount value lines --}}
            <td class="it-disc {{ $disc > 0 ? 'it-link' : '' }}"
                @if($disc > 0)
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type="Discount"
                title="Discount detail – {{ $locNm }}"
                @endif>
                {{ formatIndianMoney($disc) }}
            </td>

            {{-- Cancel --}}
            <td class="it-cancel {{ $cancel > 0 ? 'it-link' : '' }}"
                @if($cancel > 0)
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type="Cancel"
                title="Cancelled bills – {{ $locNm }}"
                @endif>
                {{ formatIndianMoney($cancel) }}
            </td>

            {{-- Refund --}}
            <td class="it-refund {{ $refund > 0 ? 'it-link' : '' }}"
                @if($refund > 0)
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type="Refund"
                title="Refund lines – {{ $locNm }}"
                @endif>
                {{ formatIndianMoney($refund) }}
            </td>

            {{-- Total — click = all bills for this branch --}}
            <td class="it-total it-link"
                data-action="drilldown"
                data-loc-id="{{ $locId }}"
                data-loc-name="{{ $locNm }}"
                data-pay-type=""
                title="All bills – {{ $locNm }}">
                {{ formatIndianMoney($line) }}
            </td>
        </tr>
    @endforeach

    {{-- Footer total row --}}
    <tr class="it-footer">
        <td colspan="2"><b>Total</b></td>
        <td><b>{{ formatIndianMoney($totalCash) }}</b></td>
        <td><b>{{ formatIndianMoney($totalCard) }}</b></td>
        <td><b>{{ formatIndianMoney($totalCheque) }}</b></td>
        <td><b>{{ formatIndianMoney($totalDd) }}</b></td>
        <td><b>{{ formatIndianMoney($totalNeft) }}</b></td>
        <td><b>{{ formatIndianMoney($totalCredit) }}</b></td>
        <td><b>{{ formatIndianMoney($totalUpi) }}</b></td>
        <td class="it-disc"><b>{{ formatIndianMoney($totalDisc) }}</b></td>
        <td class="it-cancel"><b>{{ formatIndianMoney($totalCancel) }}</b></td>
        <td class="it-refund"><b>{{ formatIndianMoney($totalRefund) }}</b></td>
        <td class="it-total"><b>{{ formatIndianMoney($grandTotal) }}</b></td>
    </tr>
    </tbody>
</table>
</div>

@php
    $shown        = method_exists($incomeData,'count') ? $incomeData->count() : count($incomeData);
    $totalEntries = method_exists($incomeData,'total') ? $incomeData->total() : $shown;
@endphp
<div class="d-flex align-items-center justify-content-between px-1 pt-2"
     style="font-size:.82rem;color:#555;">
    <span>Showing {{ $rowBase + 1 }}–{{ $rowBase + $shown }} of {{ $totalEntries }} entries</span>
    @if(method_exists($incomeData,'links'))
        <div>{{ $incomeData->links() }}</div>
    @endif
</div>

@else
<div style="text-align:center;padding:48px 24px;color:#94a3b8;">
    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
    <p style="margin:0;">No income data found for the selected filters.</p>
</div>
@endif
