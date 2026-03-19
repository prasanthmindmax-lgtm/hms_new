@if(isset($Tblgrn) && $Tblgrn->count() > 0)
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>GRN No</th>
        <th>Date</th>
        <th>Vendor</th>
        <th>Received Qty</th>
    </tr>
    </thead>
    <tbody>
    @foreach($Tblgrn as $grn)
        <tr>
            <td>{{ ($Tblgrn->currentPage() - 1) * $Tblgrn->perPage() + $loop->iteration }}</td>
            <td>
                <a class="vendor_link" href="{{ route('superadmin.getgrndashboard') }}?id={{ $grn->id }}" style="color:blue;">
                  {{ $grn->grn_number }}
                </a>
              </td>
            {{-- <td>{{ $grn->grn_number }}</td> --}}
            <td>{{ $grn->bill_date }}</td>
            <td>{{ $grn->vendor_name }}</td>
            @foreach($grn->BillLines as $line)
                <td>{{ $line->acceptable_quantity }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
@else
<p>No GRNs found.</p>
@endif