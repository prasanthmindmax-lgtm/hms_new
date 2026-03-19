<table class="tds-table">
  <thead>
    <tr><th>TAX NAME</th><th>RATE (%)</th><th>STATUS</th></tr>
  </thead>
  <tbody>
    @if(!empty($Tbltdstax))
      @foreach ($Tbltdstax as $tax)
         @php
          try {
              $start = \Carbon\Carbon::createFromFormat('d/m/Y', $tax->tax_start_date);
              $end = \Carbon\Carbon::createFromFormat('d/m/Y', $tax->tax_end_date);
          } catch (\Exception $e) {
              $status = 'Invalid Date';
          }

          $today = \Carbon\Carbon::today();

          if (isset($start) && isset($end)) {
              if ($today->lt($start)) $status = 'Upcoming';
              elseif ($today->gt($end)) $status = 'Expired';
              else $status = 'Active';
          }
      @endphp
        <tr>
          <td>{{ $tax->tax_name }}</td>
          <td>{{ $tax->tax_rate }}</td>
          <td class="{{ strtolower($status) }}">{{ $status }}</td>
        </tr>
      @endforeach
    @endif
  </tbody>
</table>

<div class="pagination-wrapper">
  {!! $Tbltdstax->links('pagination::bootstrap-4') !!}
</div>
