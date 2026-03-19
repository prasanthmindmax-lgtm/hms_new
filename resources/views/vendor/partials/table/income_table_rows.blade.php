<div class="table-responsive">

    {{-- Initialize totals --}}
    @php
        $totalCash = 0;
        $totalCard = 0;
        $totalCheque = 0;
        $totalDd = 0;
        $totalNeft = 0;
        $totalCredit = 0;
        $totalUpi = 0;
        $grandTotal = 0;
    @endphp

    <table class="custom-table">
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
            <th>Total</th>
        </tr>
        </thead>

        <tbody>
        @foreach($incomeData as $i => $data)

            @php
                // Add running totals
                $totalCash   += $data->cash;
                $totalCard   += $data->card;
                $totalCheque += $data->cheque;
                $totalDd     += $data->dd;
                $totalNeft   += $data->neft;
                $totalCredit += $data->credit;
                $totalUpi    += $data->upi;

                // Line total
                $lineTotal =
                    $data->cash +
                    $data->card +
                    $data->cheque +
                    $data->dd +
                    $data->neft +
                    $data->credit +
                    $data->upi;

                $grandTotal += $lineTotal;
            @endphp

            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="color: black">{{ $data->location_name }}</td>
                <td>{{ formatIndianMoney($data->cash) }}</td>
                <td>{{ formatIndianMoney($data->card) }}</td>
                <td>{{ formatIndianMoney($data->cheque) }}</td>
                <td>{{ formatIndianMoney($data->dd) }}</td>
                <td>{{ formatIndianMoney($data->neft) }}</td>
                <td>{{ formatIndianMoney($data->credit) }}</td>
                <td>{{ formatIndianMoney($data->upi) }}</td>
                <td>{{ formatIndianMoney($lineTotal) }}</td>
            </tr>
        @endforeach

        {{-- Final total row --}}
        <tr class="total-row">
            <td colspan="2"><b>Total</b></td>
            <td><b>{{ formatIndianMoney($totalCash) }}</b></td>
            <td><b>{{ formatIndianMoney($totalCard) }}</b></td>
            <td><b>{{ formatIndianMoney($totalCheque) }}</b></td>
            <td><b>{{ formatIndianMoney($totalDd) }}</b></td>
            <td><b>{{ formatIndianMoney($totalNeft) }}</b></td>
            <td><b>{{ formatIndianMoney($totalCredit) }}</b></td>
            <td><b>{{ formatIndianMoney($totalUpi) }}</b></td>
            <td class="total_amount"><b>{{ formatIndianMoney($grandTotal) }}</b></td>
        </tr>
        </tbody>
    </table>
</div>

<div class="mt-3">
    <span>Showing 1 to {{ $incomeData->count() }} of {{ $incomeData->count() }} entries</span>
</div>
