<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('/assets/css/quotation.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
  .sm-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9998; }
  .sm-modal { display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; }
  .sm-modal.show { display:flex; }
  .sm-modal-overlay.show { display:block; }
  .sm-modal-box {
    background:#fff; border-radius:14px; padding:28px 30px;
    width:100%; max-width:560px; box-shadow:0 20px 60px rgba(0,0,0,.18);
    position:relative; animation:smSlideIn .2s ease;
  }
  @keyframes smSlideIn { from{transform:translateY(-20px);opacity:0} to{transform:translateY(0);opacity:1} }
  .sm-modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
  .sm-modal-title { font-size:16px; font-weight:700; color:#1f2937; display:flex; align-items:center; gap:8px; }
  .sm-modal-close { width:30px;height:30px;border-radius:50%;background:#f3f4f6;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:18px;color:#6b7280; }
  .sm-modal-close:hover { background:#e5e7eb; }
  .sm-form-row { display:flex; gap:14px; margin-bottom:16px; }
  .sm-form-group { flex:1; }
  .sm-form-group label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
  .sm-form-group .form-control { border-radius:8px; border:1px solid #d1d5db; font-size:13px; padding:8px 12px; }
  .sm-form-group .form-control:focus { border-color:#4f6ef7; box-shadow:0 0 0 3px rgba(79,110,247,.1); outline:none; }
  .sm-section-label { font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin:16px 0 10px; padding-bottom:6px; border-bottom:1px solid #f3f4f6; }
  .sm-modal-footer { display:flex; gap:10px; margin-top:22px; justify-content:flex-end; }
  .sm-btn-primary { padding:9px 22px; background:linear-gradient(135deg,#4f6ef7,#7c3aed); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
  .sm-btn-primary:hover { opacity:.9; }
  .sm-btn-cancel { padding:9px 22px; background:#f3f4f6; color:#374151; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
  .sm-btn-cancel:hover { background:#e5e7eb; }
</style>

<body style="overflow-x: hidden;">
<div class="page-loader"><div class="bar"></div></div>

@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    <div class="qd-card">

      {{-- Header --}}
      <div class="qd-header">
        <div class="qd-header-title">
          <i class="bi bi-percent"></i> TDS Tax
        </div>
        <div class="qd-header-actions">
          <button class="btn btn-primary btn-sm tds_new">
            <i class="bi bi-plus-lg me-1"></i>New TDS
          </button>
        </div>
      </div>

      {{-- Search --}}
      <div class="qd-search-row">
        <div class="qd-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" id="tableSearch" placeholder="Search tax name, rate...">
        </div>
      </div>

      {{-- Table --}}
      <div class="qdt-wrap">
        <table class="qdt-table" id="mainTable">
          <thead class="qdt-head">
            <tr>
              <th class="qdt-th-check"><input type="checkbox" id="selectAll"></th>
              <th>TAX NAME</th>
              <th>RATE (%)</th>
              <th>SECTION</th>
              <th>START DATE</th>
              <th>EXPIRED DATE</th>
              <th>STATUS</th>
              <th class="text-center">ACTION</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($tdstax as $v)
              @php
                $endDate     = DateTime::createFromFormat('d/m/Y', $v->tax_end_date);
                $currentDate = new DateTime();
                $isActive    = $endDate && $endDate > $currentDate;
              @endphp
              <tr class="qdt-row customer-row" data-id="{{ $v->id }}" data-tdstax='@json($v)'>
                <td class="qdt-td-check"><input type="checkbox" class="row-check"></td>
                <td style="font-weight:600;color:#1f2937;">{{ $v->tax_name }}</td>
                <td class="qdt-mono">{{ $v->tax_rate }}%</td>
                <td>{{ $v->section_name ?: '—' }}</td>
                <td class="qdt-date-cell"><span class="qdt-date-main">{{ $v->tax_start_date }}</span></td>
                <td class="qdt-date-cell"><span class="qdt-date-main">{{ $v->tax_end_date }}</span></td>
                <td>
                  <span class="qd-badge {{ $isActive ? 'qd-badge-approved' : 'qd-badge-rejected' }}">
                    {{ $isActive ? 'Active' : 'Expired' }}
                  </span>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm edit-btn"
                    style="font-size:11px;padding:4px 14px;border:1px solid #4f6ef7;color:#4f6ef7;border-radius:6px;background:#fff;">
                    <i class="bi bi-pencil me-1"></i>Edit
                  </button>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>No TDS tax records
              </td></tr>
            @endforelse
          </tbody>
        </table>

        @if($tdstax->total() > 10)
        <div class="qd-pagination d-flex justify-content-between align-items-center mt-2 px-1">
          <div>{{ $tdstax->links('pagination::bootstrap-4') }}</div>
          <div style="display:flex;align-items:center;gap:6px;">
            <select name="per_page" id="per_page" class="form-control form-control-sm" style="width:70px;">
              @foreach([10,25,50,100,250,500] as $size)
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

{{-- New/Edit TDS Modal --}}
<div class="sm-modal-overlay" id="modalOverlay"></div>
<div class="sm-modal" id="newTdsModal">
  <div class="sm-modal-box">
    <div class="sm-modal-header">
      <div class="sm-modal-title"><i class="bi bi-percent" style="color:#4f6ef7;"></i> TDS Tax</div>
      <button class="sm-modal-close close-modal">&times;</button>
    </div>
    <input type="hidden" class="tds_id" id="tds_id">
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Tax Name <span style="color:red">*</span></label>
        <span class="tds_name_error" style="color:red;font-size:11px;"></span>
        <input type="text" class="form-control tds_name" placeholder="e.g. TDS on Professional Fees" autocomplete="off">
      </div>
      <div class="sm-form-group">
        <label>Rate (%) <span style="color:red">*</span></label>
        <span class="tds_rate_error" style="color:red;font-size:11px;"></span>
        <input type="text" class="form-control tds_rate" placeholder="e.g. 10" autocomplete="off">
      </div>
    </div>
    {{-- Section dropdown --}}
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Section</label>
        <input type="hidden" class="tds_section_id" id="tds_section_id">
        <select class="form-control tds_section_select" id="tds_section_select" style="border-radius:8px;border:1px solid #d1d5db;font-size:13px;padding:8px 12px;">
          <option value="">— Select Section —</option>
          @foreach($Tbltdssection as $sec)
            <option value="{{ $sec->id }}" data-name="{{ $sec->name }}">{{ $sec->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="sm-section-label">Applicable Period</div>
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Start Date</label>
        <input type="text" class="form-control datepicker tds_start_date" placeholder="dd/MM/yyyy" autocomplete="off">
      </div>
      <div class="sm-form-group">
        <label>End Date</label>
        <input type="text" class="form-control datepicker tds_end_date" placeholder="dd/MM/yyyy" autocomplete="off">
      </div>
    </div>
    <div class="sm-modal-footer">
      <button class="sm-btn-cancel close-modal">Cancel</button>
      <button class="sm-btn-primary tds_save">Save TDS</button>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function () {
  $('#per_page').on('change', function () { window.location.href = '?per_page=' + $(this).val(); });

  // Select all
  $('#selectAll').on('change', function () { $('.row-check').prop('checked', $(this).prop('checked')); });

  // Live search
  $('#tableSearch').on('keyup', function () {
    const q = $(this).val().toLowerCase();
    $('#mainTable tbody tr').each(function () {
      $(this).toggle($(this).text().toLowerCase().includes(q));
    });
  });

  // Open modal for new
  $('.tds_new').on('click', function () {
    $('#tds_id,.tds_name,.tds_rate,.tds_start_date,.tds_end_date').val('');
    $('.tds_name_error,.tds_rate_error').text('');
    $('#tds_section_select').val('');
    $('.tds_section_id').val('');
    openModal();
  });

  // Open modal for edit
  $(document).on('click', '.edit-btn', function () {
    const d = $(this).closest('tr').data('tdstax');
    $('#tds_id').val(d.id);
    $('.tds_name').val(d.tax_name);
    $('.tds_rate').val(d.tax_rate);
    $('.tds_name_error,.tds_rate_error').text('');
    // Populate section dropdown
    if (d.section_id) {
      $('#tds_section_select').val(d.section_id);
      $('.tds_section_id').val(d.section_id);
    } else {
      $('#tds_section_select').val('');
      $('.tds_section_id').val('');
    }
    $('.tds_start_date').val(d.tax_start_date);
    $('.tds_end_date').val(d.tax_end_date);
    openModal();
  });

  // Sync hidden section_id when select changes
  $('#tds_section_select').on('change', function () {
    $('.tds_section_id').val($(this).val());
  });

  function openModal() {
    $('#newTdsModal').addClass('show');
    $('#modalOverlay').addClass('show');
    $('body').css('overflow','hidden');
  }
  function closeModal() {
    $('#newTdsModal').removeClass('show');
    $('#modalOverlay').removeClass('show');
    $('body').css('overflow','auto');
  }
  $(document).on('click', '.close-modal, #modalOverlay', closeModal);

  // Save
  $(document).on('click', '.tds_save', function (e) {
    e.preventDefault();
    let valid = true;
    if (!$('.tds_name').val().trim()) {
      $('.tds_name_error').text('Tax Name is required');
      valid = false;
    } else { $('.tds_name_error').text(''); }
    if (!$('.tds_rate').val().trim()) {
      $('.tds_rate_error').text('Rate is required');
      valid = false;
    } else { $('.tds_rate_error').text(''); }
    if (!valid) return;

    const selectedOption = $('#tds_section_select option:selected');
    const fd = new FormData();
    fd.append('id',           $('.tds_id').val());
    fd.append('name',         $('.tds_name').val());
    fd.append('rate',         $('.tds_rate').val());
    fd.append('section_id',   selectedOption.val() || '');
    fd.append('section_name', selectedOption.data('name') || '');
    fd.append('start_date',   $('.tds_start_date').val());
    fd.append('end_date',     $('.tds_end_date').val());
    $.ajax({
      url:'{{ route("superadmin.gettdssave") }}', type:'POST', data:fd, processData:false, contentType:false,
      success: function (res) {
        if (res.success) { toastr.success(res.message); closeModal(); setTimeout(()=>window.location.reload(), 800); }
      },
      error: function (err) {
        if (err.responseJSON?.errors) $.each(err.responseJSON.errors, (k,v) => toastr.error(v[0]));
        else toastr.error('Something went wrong. Please try again.');
      }
    });
  });

  flatpickr('.datepicker', { dateFormat:'d/m/Y', allowInput:true });
});
</script>

@include('superadmin.superadminfooter')
</body>
</html>
