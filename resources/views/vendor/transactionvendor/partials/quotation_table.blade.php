@if($TblQuotation->count() > 0)
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Quotation No</th>
      <th>Location</th>
      <th>Date</th>
      <th>Vendor</th>
      <th>Amount</th>
    </tr>
  </thead>
  <tbody>
    @foreach($TblQuotation as $quotation)
      <tr>
        <td>{{ ($TblQuotation->currentPage() - 1) * $TblQuotation->perPage() + $loop->iteration }}</td>

        <td>
          <a class="vendor_link" href="{{ route('superadmin.getquotation') }}?id={{ $quotation->id }}" style="color:blue;">
            {{ $quotation->quotation_gen_no }}
          </a>
        </td>
        {{-- <td>{{ $quotation->quotation_gen_no }}</td> --}}
        <td>{{ $quotation->zone_name }}<br>{{ $quotation->branch_name }}</td>
        <td>{{ $quotation->created_at->format('d/m/Y') }}</td>
        <td>{{ $quotation->vendor_name }}</td>
        <td>{{ $quotation->grand_total_amount }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
@else
<p>No quotations found.</p>
@endif
