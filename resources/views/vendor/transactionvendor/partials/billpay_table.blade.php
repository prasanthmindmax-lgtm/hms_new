@if(isset($Tblbillpay) && $Tblbillpay->count() > 0)
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Payment ID</th>
        <th>Date</th>
        <th>Vendor</th>
        <th>Amount Paid</th>
        <th>Mode</th>
    </tr>
    </thead>
    <tbody>
    @foreach($Tblbillpay as $payment)
        <tr>
            <td>{{ ($Tblbillpay->currentPage() - 1) * $Tblbillpay->perPage() + $loop->iteration }}</td>
            <td>
                <a class="vendor_link" href="{{ route('superadmin.getbillmade') }}?id={{ $payment->id }}" style="color:blue;">
                  {{ $payment->payment_gen_order }}
                </a>
              </td>
            <td>{{ $payment->created_at->format('d/m/Y') }}</td>
            <td>{{ $payment->vendor_name }}</td>
            <td>{{ $payment->amount_paid }}</td>
            <td>{{ $payment->payment_mode }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@else
<p>No bill payments found.</p>
@endif
