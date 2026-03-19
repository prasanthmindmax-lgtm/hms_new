<table class="tds-table">
  <thead>
    <tr><th>Email</th><th>created By</th><th>Edit</th></tr>
  </thead>
  <tbody>
    @if(!empty($TblPoEmail))
      @foreach ($TblPoEmail as $email)
        <tr>
          <td id="emailrow">
            <input type="hidden" class="email_id" value="{{$email->id}}">
            <input type="hidden" class="email_mail" value="{{$email->email}}">
            {{ $email->email }}
          </td>
          <td>{{ $email->created_by }}</td>
          <td>
            <button class="zoho-btn zoho-btn-primary edit-email">
                      <i class="bi bi-pencil"></i> Edit
                    </button>
          </td>
        </tr>
      @endforeach
    @endif
  </tbody>
</table>

<div class="pagination-wrapper">
  {!! $TblPoEmail->links() !!}
</div>
