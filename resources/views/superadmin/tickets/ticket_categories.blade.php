<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
  .sm-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9998; }
  .sm-modal { display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; }
  .sm-modal.show { display:flex; }
  .sm-modal-overlay.show { display:block; }
  .sm-modal-box { background:#fff; border-radius:14px; padding:28px 30px; width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,.18); position:relative; animation:smSlideIn .2s ease; }
  @keyframes smSlideIn { from{transform:translateY(-20px);opacity:0} to{transform:translateY(0);opacity:1} }
  .sm-modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
  .sm-modal-title { font-size:16px; font-weight:700; color:#1f2937; display:flex; align-items:center; gap:8px; }
  .sm-modal-close { width:30px;height:30px;border-radius:50%;background:#f3f4f6;border:none;cursor:pointer;font-size:18px;color:#6b7280;display:flex;align-items:center;justify-content:center; }
  .sm-form-row { display:flex; gap:14px; margin-bottom:16px; }
  .sm-form-group { flex:1; }
  .sm-form-group label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
  .sm-form-group .form-control { border-radius:8px; border:1px solid #d1d5db; font-size:13px; padding:8px 12px; width:100%; }
  .sm-modal-footer { display:flex; gap:10px; margin-top:22px; justify-content:flex-end; }
  .sm-btn-primary { padding:9px 22px; background:linear-gradient(135deg,#4f6ef7,#7c3aed); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
  .sm-btn-cancel { padding:9px 22px; background:#f3f4f6; color:#374151; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
</style>

<body style="overflow-x: hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    <div class="qd-card">

      <div class="qd-header">
        <div class="qd-header-title">
          <i class="bi bi-bookmark-star"></i> Ticket Category
        </div>
        <div class="qd-header-actions">
          <a href="{{ route('superadmin.issue.categories.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-diagram-3 me-1"></i> Issue categories
          </a>
          <button type="button" class="btn btn-primary btn-sm new_row">
            <i class="bi bi-plus-lg me-1"></i>New Ticket Category
          </button>
        </div>
      </div>

      <div class="qd-search-row">
        <div class="qd-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" id="tableSearch" placeholder="Search by name…">
        </div>
      </div>

      <div class="qdt-wrap">
        <table class="qdt-table" id="mainTable">
          <thead class="qdt-head">
            <tr>
              <th class="qdt-th-check"><input type="checkbox" id="selectAll"></th>
              <th>DEPARTMENT</th>
              <th>TICKET CATEGORY</th>
              <th>CREATED BY</th>
              <th>STATUS</th>
              <th class="text-center">ACTION</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($ticketCategories as $row)
              <tr class="qdt-row type-row" data-id="{{ $row->id }}" data-type='@json($row)'>
                <td class="qdt-td-check"><input type="checkbox" class="row-check"></td>
                <td style="font-weight:600;color:#1f2937;">{{ $row->department ? $row->department->name : '-' }}</td>
                <td style="font-weight:600;color:#1f2937;">{{ $row->name }}</td>
                <td style="font-weight:600;color:#1f2937;">{{ $row->created_by ? $row->createdBy->user_fullname : '-' }}</td>
                <td>
                  @if($row->is_active == 1)
                    <span style="font-size:12px;background:#ecfdf3;color:#15803d;padding:3px 10px;border-radius:20px;font-weight:500;">
                      Active
                    </span>
                  @else
                    <span style="font-size:12px;background:#fef2f2;color:#b91c1c;padding:3px 10px;border-radius:20px;font-weight:500;">
                      Inactive
                    </span>
                  @endif
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm edit-btn"
                    style="font-size:11px;padding:4px 14px;border:1px solid #4f6ef7;color:#4f6ef7;border-radius:6px;background:#fff;">
                    <i class="bi bi-pencil me-1"></i>Edit
                  </button>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>No ticket categories yet
              </td></tr>
            @endforelse
          </tbody>
        </table>

        @if($ticketCategories->total() > 10)
        <div class="qd-pagination d-flex justify-content-between align-items-center mt-2 px-1">
          <div>{{ $ticketCategories->links('pagination::bootstrap-4') }}</div>
          <div style="display:flex;align-items:center;gap:6px;">
            <select id="per_page" class="form-control form-control-sm" style="width:70px;">
              @foreach([10,25,50,100] as $size)
                <option value="{{ $size }}" {{ $perPage==$size ? 'selected' : '' }}>{{ $size }}</option>
              @endforeach
            </select>
            <span style="font-size:13px;color:#6c757d;">entries</span>
          </div>
        </div>
        @endif
      </div>

    </div>
  </div>
</div>

<div class="sm-modal-overlay" id="modalOverlay"></div>
<div class="sm-modal" id="categoryModal">
  <div class="sm-modal-box">
    <div class="sm-modal-header">
      <div class="sm-modal-title"><i class="bi bi-bookmark-star" style="color:#4f6ef7;"></i> Ticket Category</div>
      <button type="button" class="sm-modal-close close-modal">&times;</button>
    </div>
    <input type="hidden" class="row_id" id="row_id">
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Department <span style="color:red">*</span></label>
        <select class="form-control department_id" id="department_id">
          <option value="">Select Department</option>
          @foreach($departments as $department)
            <option value="{{ $department->id }}">{{ $department->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Ticket Category <span style="color:red">*</span></label>
        <input type="text" class="form-control name" placeholder="e.g. IT Support" maxlength="255" autocomplete="off">
      </div>
    </div>
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Status <span style="color:red">*</span></label>
        <select class="form-control is_active" id="is_active">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
    </div>
    <div class="sm-modal-footer">
      <button type="button" class="sm-btn-cancel close-modal">Cancel</button>
      <button type="button" class="sm-btn-primary row_save">Save</button>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function () {
  $('#per_page').on('change', function () {
    window.location.href = '?per_page=' + $(this).val();
  });

  $('#selectAll').on('change', function () {
    $('.row-check').prop('checked', $(this).prop('checked'));
  });

  $('#tableSearch').on('keyup', function () {
    const q = $(this).val().toLowerCase();
    $('#mainTable tbody tr').each(function () {
      $(this).toggle($(this).text().toLowerCase().includes(q));
    });
  });

  function openModal() {
    $('#categoryModal').addClass('show');
    $('#modalOverlay').addClass('show');
    $('body').css('overflow','hidden');
  }

  function closeModal() {
    $('#categoryModal').removeClass('show');
    $('#modalOverlay').removeClass('show');
    $('body').css('overflow','auto');
  }

  $('.new_row').on('click', function () {
    $('#row_id').val('');
    $('.name').val('');
    openModal();
  });

  $(document).on('click', '.edit-btn', function () {
    const d = $(this).closest('tr').data('type');
    $('#row_id').val(d.id);
    $('.department_id').val(d.department_id);
    $('.name').val(d.name);
    $('.is_active').val(d.is_active);
    openModal();
  });

  $(document).on('click', '.close-modal, #modalOverlay', closeModal);

  $(document).on('click', '.row_save', function (e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('id', $('#row_id').val());
    fd.append('department_id', $('.department_id').val());
    fd.append('name', $('.name').val());
    fd.append('is_active', $('.is_active').val());
    $.ajax({
      url:'{{ route("superadmin.ticket.categories.store") }}',
      type:'POST',
      data:fd,
      processData:false,
      contentType:false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (res) {
        if (res.success) {
          toastr.success(res.message);
          closeModal();
          setTimeout(()=>window.location.reload(),800);
        }
      },
      error: function (err) {
        if (err.responseJSON && err.responseJSON.errors) {
          $.each(err.responseJSON.errors, function (k, v) {
            toastr.error(v[0]);
          });
        } else {
          toastr.error('Something went wrong.');
        }
      }
    });
  });
});
</script>

@include('superadmin.superadminfooter')
</body>
</html>
