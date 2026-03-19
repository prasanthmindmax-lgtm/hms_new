
@if(isset($TblPurchaseorder) && $TblPurchaseorder->count() > 0)
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>PO No</th>
        <th>Location</th>
        <th>Date</th>
        <th>Vendor</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach($TblPurchaseorder as $purchase)
        <tr>
            <td>{{ ($TblPurchaseorder->currentPage() - 1) * $TblPurchaseorder->perPage() + $loop->iteration }}</td>
            {{-- <td>{{ $purchase->purchase_gen_order }}</td> --}}
            <td>
              <a class="vendor_link" href="{{ route('superadmin.getpurchaseorder') }}?id={{ $purchase->id }}" style="color:blue;">
                {{ $purchase->purchase_gen_order }}
              </a>
            </td>
            <td>{{ $purchase->zone_name  }} </br> {{ $purchase->branch_name  }}</td>
            <td>{{ $purchase->created_at->format('d/m/Y') }}</td>
            <td>{{ $purchase->vendor_name }}</td>
            <td>{{ $purchase->grand_total_amount }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@else
<p>No purchase orders found.</p>
@endif
