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
  .sm-modal-box { background:#fff; border-radius:14px; padding:28px 30px; width:100%; max-width:560px; box-shadow:0 20px 60px rgba(0,0,0,.18); position:relative; animation:smSlideIn .2s ease; }
  @keyframes smSlideIn { from{transform:translateY(-20px);opacity:0} to{transform:translateY(0);opacity:1} }
  .sm-modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
  .sm-modal-title { font-size:16px; font-weight:700; color:#1f2937; display:flex; align-items:center; gap:8px; }
  .sm-modal-close { width:30px;height:30px;border-radius:50%;background:#f3f4f6;border:none;cursor:pointer;font-size:18px;color:#6b7280;display:flex;align-items:center;justify-content:center; }
  .sm-modal-close:hover { background:#e5e7eb; }
  .sm-form-row { display:flex; gap:14px; margin-bottom:16px; flex-wrap:wrap; }
  .sm-form-group { flex:1; min-width:200px; }
  .sm-form-group label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
  .sm-form-group .form-control,
  .sm-form-group select { border-radius:8px; border:1px solid #d1d5db; font-size:13px; padding:8px 12px; width:100%; }
  .sm-modal-footer { display:flex; gap:10px; margin-top:22px; justify-content:flex-end; }
  .sm-btn-primary { padding:9px 22px; background:linear-gradient(135deg,#4f6ef7,#7c3aed); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
  .sm-btn-cancel { padding:9px 22px; background:#f3f4f6; color:#374151; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
  .ld-catalog-key-hint { font-size:12px; color:#6b7280; margin-top:4px; }
  .ld-catalog-key-hint code { font-size:11px; background:#f3f4f6; padding:2px 6px; border-radius:4px; }
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
          <i class="bi bi-file-earmark-medical"></i> Licence document master
        </div>
        <div class="qd-header-actions">
          <button type="button" class="btn btn-primary btn-sm" id="btnNewCatalogSlot">
            <i class="bi bi-plus-lg me-1"></i>New document
          </button>
        </div>
      </div>

      <div class="qd-search-row">
        <div class="qd-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" id="tableSearch" placeholder="Search by key or label...">
        </div>
      </div>

      <div class="qdt-wrap">
        <table class="qdt-table" id="mainTable">
          <thead class="qdt-head">
            <tr>
              <th class="qdt-th-check"><input type="checkbox" id="selectAll"></th>
              <th>LEVEL</th>
              {{--  <th>DOCUMENT KEY</th>  --}}
              <th>DOCUMENT NAME</th>
              <th>STATUS</th>
              <th class="text-center">ACTION</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($items as $row)
              <tr class="qdt-row type-row" data-id="{{ $row->id }}" data-type='@json($row)'>
                <td class="qdt-td-check"><input type="checkbox" class="row-check"></td>
                <td style="font-weight:600;color:#1f2937;">
                  <span class="badge {{ $row->level === 2 ? 'bg-success' : 'bg-primary' }}">Level {{ $row->level }}</span>
                </td>
                {{--  <td style="font-weight:600;color:#1f2937;"><code class="small">{{ $row->document_key }}</code></td>  --}}
                <td style="font-weight:600;color:#1f2937;">{{ $row->label }}</td>
                <td>
                  @if($row->is_active)
                    <span style="font-size:12px;background:#ecfdf3;color:#15803d;padding:3px 10px;border-radius:20px;font-weight:500;">Active</span>
                  @else
                    <span style="font-size:12px;background:#fef2f2;color:#b91c1c;padding:3px 10px;border-radius:20px;font-weight:500;">Inactive</span>
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
              <tr><td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>No document slots found. Run migrations or add a new slot.
              </td></tr>
            @endforelse
          </tbody>
        </table>

        @if($items->total() > 10)
        <div class="qd-pagination d-flex justify-content-between align-items-center mt-2 px-1">
          <div>{{ $items->links('pagination::bootstrap-4') }}</div>
          <div style="display:flex;align-items:center;gap:6px;">
            <select id="per_page" class="form-control form-control-sm" style="width:70px;">
              @foreach([10,25,50,100] as $size)
                <option value="{{ $size }}" {{ (int)$perPage === $size ? 'selected' : '' }}>{{ $size }}</option>
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
<div class="sm-modal" id="catalogModal">
  <div class="sm-modal-box">
    <div class="sm-modal-header">
      <div class="sm-modal-title"><i class="bi bi-file-earmark-medical" style="color:#4f6ef7;"></i> Licence document slot</div>
      <button type="button" class="sm-modal-close close-modal" aria-label="Close">&times;</button>
    </div>
    <input type="hidden" id="catalog_id" value="">
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Level <span style="color:red">*</span></label>
        <select class="form-control" id="catalog_level">
          <option value="">Select level</option>
          <option value="1">Level 1</option>
          <option value="2">Level 2</option>
        </select>
      </div>
      <div class="sm-form-group" id="catalog_key_row" style="display:none;">
        <label>System key</label>
        <div class="ld-catalog-key-hint" id="catalog_key_hint"></div>
      </div>
    </div>
    <div class="sm-form-row">
      <div class="sm-form-group" style="flex:1 1 100%;">
        <label>Document name <span style="color:red">*</span></label>
        <input type="text" class="form-control" id="catalog_label" placeholder="e.g. Drug licence" autocomplete="off">
      </div>
    </div>
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Status <span style="color:red">*</span></label>
        <select class="form-control" id="catalog_is_active">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
    </div>
    <div class="sm-modal-footer">
      <button type="button" class="sm-btn-cancel close-modal">Cancel</button>
      <button type="button" class="sm-btn-primary" id="catalogSaveBtn">Save</button>
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
    $('#mainTable tbody tr.type-row').each(function () {
      $(this).toggle($(this).text().toLowerCase().includes(q));
    });
  });

  function openModal() {
    $('#catalogModal').addClass('show');
    $('#modalOverlay').addClass('show');
    $('body').css('overflow','hidden');
  }

  function closeModal() {
    $('#catalogModal').removeClass('show');
    $('#modalOverlay').removeClass('show');
    $('body').css('overflow','auto');
  }

  $('#btnNewCatalogSlot').on('click', function () {
    $('#catalog_id').val('');
    $('#catalog_level').val('').prop('disabled', false);
    $('#catalog_key_row').hide();
    $('#catalog_key_hint').empty();
    $('#catalog_label').val('');
    $('#catalog_is_active').val('1');
    openModal();
  });

  $(document).on('click', '.edit-btn', function () {
    const d = $(this).closest('tr').data('type');
    $('#catalog_id').val(d.id);
    $('#catalog_level').val(String(d.level)).prop('disabled', true);
    $('#catalog_key_row').show();
    $('#catalog_key_hint').html('Stored identifier: <code>' + $('<div/>').text(d.document_key).html() + '</code>');
    $('#catalog_label').val(d.label);
    $('#catalog_is_active').val(d.is_active ? '1' : '0');
    openModal();
  });

  $(document).on('click', '.close-modal, #modalOverlay', closeModal);

  $('#catalogSaveBtn').on('click', function (e) {
    e.preventDefault();
    const fd = new FormData();
    const id = $('#catalog_id').val();
    fd.append('id', id || '');
    if (!id) {
      fd.append('level', $('#catalog_level').val());
    }
    fd.append('label', ($('#catalog_label').val() || '').trim());
    fd.append('is_active', $('#catalog_is_active').val());
    $.ajax({
      url: '{{ route("superadmin.licence_documents.catalog.store") }}',
      type: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (res) {
        if (res.success) {
          toastr.success(res.message);
          closeModal();
          setTimeout(function () { window.location.reload(); }, 600);
        }
      },
      error: function (err) {
        if (err.responseJSON && err.responseJSON.errors) {
          $.each(err.responseJSON.errors, function (k, v) {
            toastr.error(v[0]);
          });
        } else if (err.responseJSON && err.responseJSON.message) {
          toastr.error(err.responseJSON.message);
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
