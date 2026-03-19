<table  class="tds-table table-scroll">
  <thead>
    <tr><th>TAX NAME</th><th>RATE (%)</th><th>STATUS</th></tr>
  </thead>
  <tbody>
    @if(!empty($Tblgsttax))
      @foreach ($Tblgsttax  as $tax)
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
  {!! $Tblgsttax ->links('pagination::bootstrap-4') !!}
</div>

{{-- @if($Tblgsttax->total() > 10)
<div class="d-flex justify-content-between">
<div class="mt-3">
  {{ $Tblgsttax->links('pagination::bootstrap-4') }}
</div>
<div>
  <form method="GET" id="perPageForm">
    <select name="per_page" id="per_page" class="form-control form-control-sm" style="width: 70px; display: inline-block;">
      @foreach([10, 25, 50, 100] as $size)
        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
      @endforeach
    </select>
    <span>entries</span>
  </form>
</div>
</div>
@endif --}}