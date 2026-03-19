<table class="table mb-0">
  <thead class="table-light">
    <tr>
      <th></th><th>PURCHASE ORDER DETAILS</th><th>VENDOR</th><th>DATE</th><th>AMOUNT</th>
    </tr>
  </thead>
  <tbody>
    @if(!empty($purchaselist))
      @foreach ($purchaselist as $list)
        <tr>
          <td><input type="checkbox" name="selected_purchase[]" value="{{ $list->id }}"></td>
          <td>{{ $list->purchase_order_number }} <span class="text-muted">| Ref#: {{ $list->order_number }}</span></td>
          <td>{{ $list->Tblvendor->display_name }}</td>
          <td>{{ $list->bill_date }}</td>
          <td>₹{{ number_format($list->sub_total_amount, 2) }}</td>
        </tr>
      @endforeach
    @endif
  </tbody>
</table>


<!-- ✅ Pagination Controls -->
  @if($purchaselist->total() > 10)
  <div class="d-flex justify-content-between">
    <div class="mt-3">
      {{ $purchaselist->links('pagination::bootstrap-4') }}
    </div>
    <div>
      <form method="GET" id="perPageForm">
        {{-- <label for="per_page">Show</label> --}}
        <select name="per_page" id="per_page" class="form-control form-control-sm" style="width: 70px; display: inline-block;">
          @foreach([10, 25, 50, 100,250,500] as $size)
            <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
          @endforeach
        </select>
        <span>entries</span>
      </form>
    </div>
  </div>
  @endif

