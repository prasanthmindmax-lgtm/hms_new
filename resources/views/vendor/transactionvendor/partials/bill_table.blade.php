@if(isset($Tblbill) && $Tblbill->count() > 0)
{{-- @php
    dd($Tblbill);
@endphp --}}
  <table>
      <thead>
      <tr>
          <th>#</th>
          <th>Bill No</th>
          <th>Location</th>
          <th>Created Date</th>
          <th>Bill Date</th>
          <th>Account Name</th>
          <th>ESI</th>
          <th>PF</th>
          <th>Total</th>
      </tr>
      </thead>
      <tbody>
      @foreach($Tblbill as $bill)
          <tr>
              <td>{{ ($Tblbill->currentPage() - 1) * $Tblbill->perPage() + $loop->iteration }}</td>
              <td>
                <a class="vendor_link" href="{{ route('superadmin.getbill') }}?id={{ $bill->id }}" style="color:blue;">
                  {{ $bill->bill_gen_number }}
                </a>
              </td>
              <td>{{ $bill->zone_name }}<br>{{ $bill->branch_name }}</td>
              <td>{{ $bill->created_at->format('d/m/Y') }}</td>
              <td>{{ $bill->bill_date }}</td>
              <td>{{ $bill->BillLines->pluck('account')->unique()->sort()->implode(', ') }}</td>
              {{-- <td>{{ optional($bill->BillLines)->account }}</td> --}}
              <td>{{ $bill->esi_amount === Null ? '-': $bill->esi_amount}}</td>
              <td>{{ $bill->pf_amount === Null ? '-': $bill->pf_amount }}</td>
              <td>{{ $bill->grand_total_amount }}</td>
          </tr>
      @endforeach
      </tbody>
  </table>
@else
  <p>No bills found.</p>
@endif
