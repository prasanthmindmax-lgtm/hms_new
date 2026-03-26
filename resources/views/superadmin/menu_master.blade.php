<!doctype html>
<html lang="en">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
.mm-badge { font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 20px; }
.mm-icon-prev { width: 22px; height: 22px; border-radius: 4px; object-fit: contain;
                background:#f1f5f9; padding:2px; vertical-align:middle; }
.action-btn { border: none; background: none; cursor: pointer; padding: 4px 6px;
              border-radius: 6px; transition: background .15s; }
.action-btn:hover { background: #f1f5f9; }
</style>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">

    {{-- Breadcrumb --}}
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Menu Master</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    {{-- Main card --}}
    <div class="row">
      <div class="col-xl-12">
        <div class="card">
          <div class="card-body border-bottom pb-0">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h3 class="mb-0"><i class="bi bi-list-ul me-2"></i>Menu Master</h3>
              <button class="btn btn-primary btn-sm" id="btnAddMenu">
                <i class="bi bi-plus-lg me-1"></i> Add Menu
              </button>
            </div>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover" id="menuTable">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Menu Name</th>
                    <th>Route</th>
                    <th>Icon</th>
                    <th>Type</th>
                    <th>Dropdown</th>
                    <th>Parent Menu</th>
                    <th style="width:100px;">Actions</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- ═══════════════════ ADD MODAL ═══════════════════ --}}
<div class="modal fade" id="addMenuModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Menu Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul id="addErrors" class="alert alert-danger d-none"></ul>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Menu Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="add_menu_name" placeholder="e.g. Vendor Master">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Route / URL</label>
            <input type="text" class="form-control" id="add_route" placeholder="e.g. superadmin/vendor">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Icon Filename</label>
            <input type="text" class="form-control" id="add_icon" placeholder="e.g. health.png" value="health.png">
            <div class="form-text">File should be in <code>assets/images/</code></div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Active ID (CSS class)</label>
            <input type="text" class="form-control" id="add_active_ids" placeholder="e.g. dashboard_color" value="dashboard_color">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Menu Type <span class="text-danger">*</span></label>
            <select class="form-select" id="add_main_menu">
              <option value="1">Main Menu</option>
              <option value="0">Sub Menu</option>
            </select>
          </div>
          <div class="col-md-4" id="add_dropdown_wrap">
            <label class="form-label fw-semibold">Has Dropdown?</label>
            <select class="form-select" id="add_dropdown">
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>
          <div class="col-md-4" id="add_parent_wrap" style="display:none;">
            <label class="form-label fw-semibold">Parent Menu <span class="text-danger">*</span></label>
            <select class="form-select" id="add_sub_menus">
              <option value="">— Select Parent —</option>
              @foreach($mainMenus as $m)
                <option value="{{ $m->id }}">{{ $m->menu_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="btnSaveMenu"><i class="bi bi-check2 me-1"></i>Save</button>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════ EDIT MODAL ═══════════════════ --}}
<div class="modal fade" id="editMenuModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Menu Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul id="editErrors" class="alert alert-danger d-none"></ul>
        <input type="hidden" id="edit_id">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Menu Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit_menu_name">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Route / URL</label>
            <input type="text" class="form-control" id="edit_route">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Icon Filename</label>
            <input type="text" class="form-control" id="edit_icon">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Active ID (CSS class)</label>
            <input type="text" class="form-control" id="edit_active_ids">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Menu Type <span class="text-danger">*</span></label>
            <select class="form-select" id="edit_main_menu">
              <option value="1">Main Menu</option>
              <option value="0">Sub Menu</option>
            </select>
          </div>
          <div class="col-md-4" id="edit_dropdown_wrap">
            <label class="form-label fw-semibold">Has Dropdown?</label>
            <select class="form-select" id="edit_dropdown">
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>
          <div class="col-md-4" id="edit_parent_wrap">
            <label class="form-label fw-semibold">Parent Menu <span class="text-danger">*</span></label>
            <select class="form-select" id="edit_sub_menus">
              <option value="">— Select Parent —</option>
              @foreach($mainMenus as $m)
                <option value="{{ $m->id }}">{{ $m->menu_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="btnUpdateMenu"><i class="bi bi-check2 me-1"></i>Update</button>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════ DELETE MODAL ═══════════════════ --}}
<div class="modal fade" id="deleteMenuModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Delete Menu</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1">Are you sure you want to delete:</p>
        <strong id="delete_menu_name" class="text-danger"></strong>
        <input type="hidden" id="delete_id">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger btn-sm" id="btnConfirmDelete">
          <i class="bi bi-trash me-1"></i>Delete
        </button>
      </div>
    </div>
  </div>
</div>

@include('superadmin.superadminfooter')

{{-- jQuery & DataTables are already loaded by superadminfooter — do NOT re-load jQuery --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
// Re-bind $ to jQuery here — pcoded.js / theme scripts call jQuery.noConflict()
// which unlinks $ from jQuery. jQuery itself is always the correct global reference.
(function ($) {
'use strict';

const ROUTES = {
  list:    '{{ route("superadmin.menumaster.list") }}',
  store:   '{{ route("superadmin.menumaster.store") }}',
  show:    '{{ url("superadmin/menu-master") }}',
  update:  '{{ url("superadmin/menu-master") }}',
  destroy: '{{ url("superadmin/menu-master") }}',
};

toastr.options = { positionClass:'toast-top-right', timeOut:3000, progressBar:true };

$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

// ── DataTable ──────────────────────────────────────────────────────────────────
const table = $('#menuTable').DataTable({
  processing: true,
  serverSide: true,
  ajax: { url: ROUTES.list, type: 'GET' },
  order: [[0, 'asc']],
  columns: [
    { data: 'DT_RowIndex',  orderable: false, searchable: false, width: '40px' },
    {
      data: 'menu_name',
      render: (d, t, row) => {
        const icon = row.icon ? `<img src="/assets/images/${row.icon}" class="mm-icon-prev me-1" onerror="this.style.display='none'">` : '';
        return `${icon}<strong>${d}</strong>`;
      }
    },
    { data: 'route', render: d => d ? `<code style="font-size:11px;">${d}</code>` : '<span class="text-muted">—</span>' },
    {
      data: 'icon',
      render: (d, t, row) => d
        ? `<img src="/assets/images/${d}" class="mm-icon-prev" onerror="this.style.display='none'"> <small>${d}</small>`
        : '<span class="text-muted">—</span>'
    },
    {
      data: 'main_menu',
      render: d => d == 1
        ? '<span class="badge bg-primary mm-badge">Main Menu</span>'
        : '<span class="badge bg-secondary mm-badge">Sub Menu</span>'
    },
    {
      data: 'dropdown',
      render: d => d == 1
        ? '<span class="badge bg-success mm-badge"><i class="bi bi-chevron-down"></i> Yes</span>'
        : '<span class="badge bg-light text-muted mm-badge">No</span>'
    },
    { data: 'parent_name', render: d => d === '—' ? '<span class="text-muted">—</span>' : `<span class="badge bg-info text-dark mm-badge">${d}</span>` },
    {
      data: 'id', orderable: false, searchable: false,
      render: (id, t, row) =>
        `<button class="action-btn text-primary btn-edit" data-id="${id}" title="Edit">
           <i class="bi bi-pencil-square fs-5"></i>
         </button>
         <button class="action-btn text-danger btn-delete" data-id="${id}" data-name="${row.menu_name}" title="Delete">
           <i class="bi bi-trash fs-5"></i>
         </button>`
    },
  ]
});

// ── ADD ────────────────────────────────────────────────────────────────────────
$('#btnAddMenu').on('click', () => {
  $('#addErrors').addClass('d-none').html('');
  $('#add_menu_name,#add_route').val('');
  $('#add_icon').val('health.png');
  $('#add_active_ids').val('dashboard_color');
  $('#add_main_menu').val('1').trigger('change');
  $('#add_dropdown').val('0');
  $('#add_sub_menus').val('');
  $('#addMenuModal').modal('show');
});

$('#add_main_menu').on('change', function () {
  const isMain = $(this).val() == '1';
  $('#add_dropdown_wrap').toggle(isMain);
  $('#add_parent_wrap').toggle(!isMain);
});

$('#btnSaveMenu').on('click', function () {
  const btn = $(this).text('Saving…').prop('disabled', true);
  $('#addErrors').addClass('d-none').html('');

  $.ajax({
    url: ROUTES.store, type: 'POST',
    data: {
      menu_name:  $('#add_menu_name').val(),
      route:      $('#add_route').val(),
      icon:       $('#add_icon').val(),
      active_ids: $('#add_active_ids').val(),
      main_menu:  $('#add_main_menu').val(),
      dropdown:   $('#add_dropdown').val(),
      sub_menus:  $('#add_sub_menus').val(),
    },
    success (res) {
      btn.text('Save').prop('disabled', false);
      if (res.status === 400) {
        $('#addErrors').removeClass('d-none').html(res.errors.map(e => `<li>${e}</li>`).join(''));
      } else {
        $('#addMenuModal').modal('hide');
        table.ajax.reload(null, false);
        toastr.success('Menu created successfully!');
      }
    },
    error () { btn.text('Save').prop('disabled', false); toastr.error('Server error.'); }
  });
});

// ── EDIT ───────────────────────────────────────────────────────────────────────
$(document).on('click', '.btn-edit', function () {
  const id = $(this).data('id');
  $('#editErrors').addClass('d-none').html('');

  $.get(`${ROUTES.show}/${id}`, function (res) {
    if (res.status === 404) { toastr.error('Record not found.'); return; }
    const m = res.menu;
    $('#edit_id').val(m.id);
    $('#edit_menu_name').val(m.menu_name);
    $('#edit_route').val(m.route);
    $('#edit_icon').val(m.icon);
    $('#edit_active_ids').val(m.active_ids);
    $('#edit_main_menu').val(m.main_menu).trigger('change');
    $('#edit_dropdown').val(m.dropdown);
    $('#edit_sub_menus').val(m.sub_menus || '');
    $('#editMenuModal').modal('show');
  });
});

$('#edit_main_menu').on('change', function () {
  const isMain = $(this).val() == '1';
  $('#edit_dropdown_wrap').toggle(isMain);
  $('#edit_parent_wrap').toggle(!isMain);
});

$('#btnUpdateMenu').on('click', function () {
  const id  = $('#edit_id').val();
  const btn = $(this).text('Updating…').prop('disabled', true);
  $('#editErrors').addClass('d-none').html('');

  $.ajax({
    url: `${ROUTES.update}/${id}`, type: 'PUT',
    data: {
      menu_name:  $('#edit_menu_name').val(),
      route:      $('#edit_route').val(),
      icon:       $('#edit_icon').val(),
      active_ids: $('#edit_active_ids').val(),
      main_menu:  $('#edit_main_menu').val(),
      dropdown:   $('#edit_dropdown').val(),
      sub_menus:  $('#edit_sub_menus').val(),
    },
    success (res) {
      btn.text('Update').prop('disabled', false);
      if (res.status === 400) {
        $('#editErrors').removeClass('d-none').html(res.errors.map(e => `<li>${e}</li>`).join(''));
      } else {
        $('#editMenuModal').modal('hide');
        table.ajax.reload(null, false);
        toastr.success('Menu updated successfully!');
      }
    },
    error () { btn.text('Update').prop('disabled', false); toastr.error('Server error.'); }
  });
});

// ── DELETE ─────────────────────────────────────────────────────────────────────
$(document).on('click', '.btn-delete', function () {
  $('#delete_id').val($(this).data('id'));
  $('#delete_menu_name').text($(this).data('name'));
  $('#deleteMenuModal').modal('show');
});

$('#btnConfirmDelete').on('click', function () {
  const id  = $('#delete_id').val();
  const btn = $(this).text('Deleting…').prop('disabled', true);

  $.ajax({
    url: `${ROUTES.destroy}/${id}`, type: 'DELETE',
    success (res) {
      btn.text('Delete').prop('disabled', false);
      $('#deleteMenuModal').modal('hide');
      if (res.status === 400) {
        toastr.error(res.message);
      } else {
        table.ajax.reload(null, false);
        toastr.success('Menu deleted successfully!');
      }
    },
    error () { btn.text('Delete').prop('disabled', false); toastr.error('Server error.'); }
  });
});

// Initial toggle state
$('#add_main_menu').trigger('change');
$('#edit_main_menu').trigger('change');

})(window.jQuery); // end IIFE — ensures $ === jQuery regardless of noConflict()
</script>
</body>
</html>
