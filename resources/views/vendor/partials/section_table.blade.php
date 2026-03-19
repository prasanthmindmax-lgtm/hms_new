<table class="tds-table">
<thead>
  <tr><th>Name</th><th>created By</th><th>Edit</th></tr>
</thead>
<tbody id="email_row">
  @if(!empty($Tbltdssection))
    @foreach ($Tbltdssection as $tdssection)
      <tr>
        <td id="emailrow">
          <input type="hidden" class="section_id_table" value="{{$tdssection->id}}">
          <input type="hidden" class="section_name_table" value="{{$tdssection->name}}">
          {{ $tdssection->name }}
        </td>
        <td>{{ $tdssection->created_by }}</td>
        <td>
          <button class="zoho-btn zoho-btn-primary edit-section">
                    <i class="bi bi-pencil"></i> Edit
                  </button>
        </td>
      </tr>
    @endforeach
  @endif
</tbody>
</table>

<div class="pagination-wrapper">
{!! $Tbltdssection->links() !!}
</div>