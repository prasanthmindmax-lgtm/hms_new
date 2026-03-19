<table class="table mb-0">
  <thead class="table-light">
    <tr>
      <th></th><th>Quotation DETAILS</th><th>VENDOR</th><th>DATE</th><th>AMOUNT</th>
    </tr>
  </thead>
  <tbody>
    @if(!empty($TblQuotation))
      @foreach ($TblQuotation  as $list)
        <tr>
          <td><input type="checkbox" name="selected_purchase[]" value="{{ $list->id }}"></td>
          <td>{{ $list->quotation_gen_no }} <span class="text-muted">| Ref#: {{ $list->quotation_no }}</span></td>
          <td>{{ $list->Tblvendor->display_name }}</td>
          <td>{{ $list->bill_date }}</td>
          <td>₹{{ number_format($list->grand_total_amount, 2) }}</td>
        </tr>
      @endforeach
    @endif
  </tbody>
</table>

<!-- ✅ Pagination Controls -->
@if($TblQuotation->total() > 10)
<div class="d-flex justify-content-between align-items-center">

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $TblQuotation->links('pagination::bootstrap-4') }}
    </div>

    {{-- Per Page Dropdown --}}
    <div class="mt-3">
        <form method="GET" id="perPageForm" class="d-flex align-items-center">
            <select name="per_page" id="per_page" class="form-control form-control-sm me-2" style="width: 80px;">
                @foreach([10, 25, 50, 100,250,500] as $size)
                    <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
            <span>entries</span>
        </form>
    </div>

</div>
@endif

