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
  .sm-form-row { display:flex; gap:14px; margin-bottom:16px; }
  .sm-form-group { flex:1; }
  .sm-form-group label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
  .sm-form-group .form-control,
  .sm-form-group select { border-radius:8px; border:1px solid #d1d5db; font-size:13px; padding:8px 12px; width:100%; }
  .sm-modal-footer { display:flex; gap:10px; margin-top:22px; justify-content:flex-end; }
  .sm-btn-primary { padding:9px 22px; background:linear-gradient(135deg,#4f6ef7,#7c3aed); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
  .sm-btn-cancel { padding:9px 22px; background:#f3f4f6; color:#374151; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
  #departmentModal .qd-filter-group.tax-dropdown-wrapper { width:100%; max-width:100%; }
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
          <i class="bi bi-diagram-3"></i> Department Master
        </div>
        <div class="qd-header-actions">
          @if(!empty($canAssignDepartmentUsers))
          <button class="btn btn-primary btn-sm new_department">
            <i class="bi bi-plus-lg me-1"></i>New Department
          </button>
          @endif
        </div>
      </div>

      <div class="qd-search-row">
        <div class="qd-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" id="tableSearch" placeholder="Search department name...">
        </div>
      </div>

      <div class="qdt-wrap">
        <table class="qdt-table" id="mainTable">
          <thead class="qdt-head">
            <tr>
              <th class="qdt-th-check"><input type="checkbox" id="selectAll"></th>
              <th>NAME</th>
              <th>ASSIGNED USERS</th>
              <th>STATUS</th>
              <th class="text-center">ACTION</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($departments as $department)
              <tr class="qdt-row type-row" data-id="{{ $department->id }}" data-dept-json="{{ e(json_encode($department->only(['id','name','description','is_active']), JSON_UNESCAPED_UNICODE)) }}">
                <td class="qdt-td-check"><input type="checkbox" class="row-check"></td>
                <td style="font-weight:600;color:#1f2937;">{{ $department->name }}</td>
                <td style="max-width:320px;">
                  @if($department->relationLoaded('assignedUsers') && $department->assignedUsers->isNotEmpty())
                    <div style="display:flex;flex-wrap:wrap;gap:4px;">
                      @foreach($department->assignedUsers as $user)
                        <span style="font-size:11px;background:#eef2ff;color:#4338ca;padding:2px 8px;border-radius:20px;font-weight:500;white-space:nowrap;">{{ $user->user_fullname }}</span>
                      @endforeach
                    </div>
                  @else
                    <span class="text-muted" style="font-size:12px;">—</span>
                  @endif
                </td>
                <td>
                  @if($department->is_active == 1)
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
                  <button class="btn btn-sm edit-btn"
                    style="font-size:11px;padding:4px 14px;border:1px solid #4f6ef7;color:#4f6ef7;border-radius:6px;background:#fff;">
                    <i class="bi bi-pencil me-1"></i>Edit
                  </button>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>No departments found
              </td></tr>
            @endforelse
          </tbody>
        </table>

        @if($departments->total() > 10)
        <div class="qd-pagination d-flex justify-content-between align-items-center mt-2 px-1">
          <div>{{ $departments->links('pagination::bootstrap-4') }}</div>
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
<div class="sm-modal" id="departmentModal">
  <div class="sm-modal-box">
    <div class="sm-modal-header">
      <div class="sm-modal-title"><i class="bi bi-diagram-3" style="color:#4f6ef7;"></i> Department</div>
      <button class="sm-modal-close close-modal">&times;</button>
    </div>
    <input type="hidden" class="department_id" id="department_id">
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Name <span style="color:red">*</span></label>
        <input type="text" class="form-control name" placeholder="e.g. Finance, HR" autocomplete="off">
      </div>
    </div>
    <div class="sm-form-row">
        <div class="sm-form-group">
            <label>Description</label>
            <textarea class="form-control description"
            placeholder="Enter description"
            rows="2"></textarea>
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
    @if(!empty($canAssignDepartmentUsers))
    <div class="sm-form-row">
      <div class="qd-filter-group tax-dropdown-wrapper zone-section">
        <label>Assign users</label>
        <input type="text" class="form-control dept-user-search-input dropdown-search-input" placeholder="Select users" readonly>
        <input type="hidden" name="dept_user_ids" class="dept_user_ids" value="">
        <div class="dropdown-menu tax-dropdown">
          <div class="inner-search-container"><input type="text" class="inner-search" placeholder="Search user..."></div>
          <div class="d-flex justify-content-between p-2 border-bottom" style="gap:8px;">
            <button type="button" class="btn btn-sm btn-outline-primary select-all">All</button>
            <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Clear</button>
          </div>
          <div class="dropdown-list multiselect zone-list">
            @foreach($departmentUsersList ?? [] as $u)
            <div data-value="{{ $u->user_fullname }}" data-id="{{ $u->id }}">{{ $u->user_fullname }}</div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @endif
    <div class="sm-modal-footer">
      <button class="sm-btn-cancel close-modal">Cancel</button>
      <button class="sm-btn-primary department_save">Save</button>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
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
    $('#departmentModal').addClass('show');
    $('#modalOverlay').addClass('show');
    $('body').css('overflow','hidden');
  }

  function closeModal() {
    $('.dropdown-menu.tax-dropdown').hide();
    $('#departmentModal').removeClass('show');
    $('#modalOverlay').removeClass('show');
    $('body').css('overflow','auto');
  }

  @if(!empty($canAssignDepartmentUsers))
  function deptModalUpdateMultiSelection() {
    const $wrap = $('#departmentModal .tax-dropdown-wrapper.zone-section');
    if (!$wrap.length) return;
    const selectedItems = [];
    const selectedIds = [];
    $wrap.find('.dropdown-menu.tax-dropdown .zone-list div.selected').each(function () {
      selectedItems.push($(this).text().trim());
      selectedIds.push($(this).data('id'));
    });
    $wrap.find('.dropdown-search-input').val(selectedItems.join(', '));
    $wrap.find('.dept_user_ids').val(selectedIds.join(','));
  }
  function resetDeptUserDropdown() {
    const $m = $('#departmentModal');
    $m.find('.zone-list div').removeClass('selected');
    $m.find('.dept-user-search-input').val('');
    $m.find('.dept_user_ids').val('');
    $m.find('.dropdown-menu.tax-dropdown .inner-search').val('');
    const $clone = $m.find('.dept-user-search-input').data('dropdown');
    if ($clone && $clone.length) {
      $clone.find('.dropdown-list.multiselect div').removeClass('selected');
      $clone.find('.inner-search').val('');
    }
  }
  function deptMirrorUserSelection(id, selected) {
    $('#departmentModal .zone-list div[data-id="' + id + '"]').toggleClass('selected', !!selected);
    const $clone = $('#departmentModal .dept-user-search-input').data('dropdown');
    if ($clone && $clone.length) {
      $clone.find('.zone-list div[data-id="' + id + '"]').toggleClass('selected', !!selected);
    }
  }
  function loadDeptUsersForEdit(deptId) {
    resetDeptUserDropdown();
    if (!deptId) return;
    $.get('{{ route("superadmin.departments.users") }}', { department_id: deptId }, function (res) {
      if (!res.success || !Array.isArray(res.user_ids)) return;
      const set = {};
      res.user_ids.forEach(function (id) { set[String(id)] = true; });
      function apply($root) {
        $root.find('.zone-list div').each(function () {
          $(this).toggleClass('selected', !!set[String($(this).data('id'))]);
        });
      }
      apply($('#departmentModal'));
      const $clone = $('#departmentModal .dept-user-search-input').data('dropdown');
      if ($clone && $clone.length) apply($clone);
      deptModalUpdateMultiSelection();
    }).fail(function () {
      toastr.error('Could not load assigned users.');
    });
  }
  $(document).on('click', '#departmentModal .dropdown-search-input', function (e) {
    e.stopPropagation();
    $('.dropdown-menu.tax-dropdown').hide();
    const $input = $(this);
    let $dropdown = $input.data('dropdown');
    if (!$dropdown) {
      $dropdown = $input.siblings('.dropdown-menu').clone(true);
      $('body').append($dropdown);
      $input.data('dropdown', $dropdown);
    }
    $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));
    const offset = $input.offset();
    $dropdown.css({
      position: 'absolute',
      top: offset.top + $input.outerHeight(),
      left: offset.left,
      width: $input.outerWidth(),
      zIndex: 10050
    }).show();
    $dropdown.find('.inner-search').focus();
  });
  $(document).on('keyup', '.inner-search', function () {
    const $menu = $(this).closest('.dropdown-menu.tax-dropdown');
    const w = $menu.data('wrapper');
    if (!w || !w.closest('#departmentModal').length) return;
    const searchVal = $(this).val().toLowerCase();
    $menu.find('.dropdown-list div').each(function () {
      const text = $(this).text().toLowerCase();
      $(this).toggle(text.indexOf(searchVal) > -1);
    });
  });
  $(document).on('click', '.dropdown-list.multiselect div', function (e) {
    const $dropdown = $(this).closest('.tax-dropdown');
    const w = $dropdown.data('wrapper');
    if (!w || !w.closest('#departmentModal').length) return;
    e.stopPropagation();
    $(this).toggleClass('selected');
    const id = $(this).data('id');
    const sel = $(this).hasClass('selected');
    deptMirrorUserSelection(id, sel);
    deptModalUpdateMultiSelection();
  });
  $(document).on('click', '.select-all', function (e) {
    const $dropdown = $(this).closest('.tax-dropdown');
    const w = $dropdown.data('wrapper');
    if (!w || !w.closest('#departmentModal').length) return;
    e.stopPropagation();
    $('#departmentModal .zone-list div').addClass('selected');
    const $clone = $('#departmentModal .dept-user-search-input').data('dropdown');
    if ($clone && $clone.length) $clone.find('.zone-list div').addClass('selected');
    deptModalUpdateMultiSelection();
  });
  $(document).on('click', '.deselect-all', function (e) {
    const $dropdown = $(this).closest('.tax-dropdown');
    const w = $dropdown.data('wrapper');
    if (!w || !w.closest('#departmentModal').length) return;
    e.stopPropagation();
    $('#departmentModal .zone-list div').removeClass('selected');
    const $clone = $('#departmentModal .dept-user-search-input').data('dropdown');
    if ($clone && $clone.length) $clone.find('.zone-list div').removeClass('selected');
    deptModalUpdateMultiSelection();
  });
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.tax-dropdown-wrapper').length && !$(e.target).closest('.tax-dropdown').length) {
      $('.dropdown-menu.tax-dropdown').hide();
    }
  });
  $(document).on('click', '.dropdown-menu.tax-dropdown', function (e) {
    const w = $(this).data('wrapper');
    if (w && w.closest('#departmentModal').length) e.stopPropagation();
  });
  @endif

  $('.new_department').on('click', function () {
    $('#department_id').val('');
    $('.name').val('');
    $('.description').val('');
    $('#is_active').val('1');
    @if(!empty($canAssignDepartmentUsers))
    resetDeptUserDropdown();
    @endif
    openModal();
  });

  function parseDepartmentFromRow($tr) {
    const raw = $tr.attr('data-dept-json');
    if (raw) {
      try {
        const d = JSON.parse(raw);
        if (d && d.id != null) return d;
      } catch (e) { /* ignore */ }
    }
    const id = parseInt(String($tr.attr('data-id') || ''), 10);
    if (!id) return null;
    const $cells = $tr.children('td');
    const nameFromTable = $cells.length >= 2 ? $cells.eq(1).text().trim() : '';
    const statusText = ($cells.length >= 4 ? $cells.eq(3).text() : '').toLowerCase();
    const isActive = statusText.indexOf('inactive') !== -1 ? 0 : 1;
    return { id, name: nameFromTable, description: '', is_active: isActive };
  }

  $(document).on('click', '.edit-btn', function () {
    const $tr = $(this).closest('tr');
    const d = parseDepartmentFromRow($tr);
    if (!d || d.id == null) {
      toastr.error('Could not load department data for edit.');
      return;
    }
    $('#department_id').val(d.id);
    $('.name').val(d.name != null ? String(d.name) : '');
    $('.description').val(d.description != null ? String(d.description) : '');
    const active = (d.is_active === true || d.is_active === 1 || d.is_active === '1') ? '1' : '0';
    $('#is_active').val(active);
    @if(!empty($canAssignDepartmentUsers))
    loadDeptUsersForEdit(Number(d.id));
    @endif
    openModal();
  });

  $(document).on('click', '.close-modal, #modalOverlay', closeModal);

  $(document).on('click', '.department_save', function (e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('id', $('#department_id').val());
    fd.append('name', $('.name').val());
    fd.append('description', $('.description').val());
    fd.append('is_active', $('#is_active').val());
    @if(!empty($canAssignDepartmentUsers))
    const uidRaw = $('#departmentModal .dept_user_ids').val();
    if (uidRaw) {
      String(uidRaw).split(',').map(function (s) { return s.trim(); }).filter(Boolean).forEach(function (id) {
        fd.append('user_ids[]', id);
      });
    }
    @endif
    $.ajax({
      url:'{{ route("superadmin.departments.store") }}',
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
