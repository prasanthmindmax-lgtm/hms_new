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
    width:100%; max-width:580px; box-shadow:0 20px 60px rgba(0,0,0,.18);
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
  .sm-form-group .form-control, .sm-form-group select { border-radius:8px; border:1px solid #d1d5db; font-size:13px; padding:8px 12px; width:100%; }
  .sm-form-group .form-control:focus, .sm-form-group select:focus { border-color:#4f6ef7; box-shadow:0 0 0 3px rgba(79,110,247,.1); outline:none; }
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

      <div class="qd-header">
        <div class="qd-header-title">
          <i class="bi bi-receipt-cutoff"></i> GST Tax
        </div>
        <div class="qd-header-actions">
          <button class="btn btn-primary btn-sm gst_new">
            <i class="bi bi-plus-lg me-1"></i>New GST
          </button>
        </div>
      </div>

      <div class="qd-search-row">
        <div class="qd-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" id="tableSearch" placeholder="Search tax name, rate, type...">
        </div>
      </div>

      <div class="qdt-wrap">
        <table class="qdt-table" id="mainTable">
          <thead class="qdt-head">
            <tr>
              <th class="qdt-th-check"><input type="checkbox" id="selectAll"></th>
              <th>TAX NAME</th>
              <th>RATE (%)</th>
              <th>TYPE</th>
              <th>START DATE</th>
              <th>EXPIRED DATE</th>
              <th>STATUS</th>
              <th class="text-center">ACTION</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($gsttax as $v)
              @php
                $endDate     = DateTime::createFromFormat('d/m/Y', $v->tax_end_date);
                $currentDate = new DateTime();
                $isActive    = $endDate && $endDate > $currentDate;
              @endphp
              <tr class="qdt-row customer-row" data-id="{{ $v->id }}" data-gsttax="{{ $v }}">
                <td class="qdt-td-check"><input type="checkbox" class="row-check"></td>
                <td style="font-weight:600;color:#1f2937;">{{ $v->tax_name }}</td>
                <td class="qdt-mono">{{ $v->tax_rate }}%</td>
                <td>
                  <span class="qd-badge qd-badge-default">{{ $v->tax_type ?? 'GST' }}</span>
                </td>
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
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>No GST tax records
              </td></tr>
            @endforelse
          </tbody>
        </table>

        @if($gsttax->total() > 10)
        <div class="qd-pagination d-flex justify-content-between align-items-center mt-2 px-1">
          <div>{{ $gsttax->links('pagination::bootstrap-4') }}</div>
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

{{-- Modal --}}
<div class="sm-modal-overlay" id="modalOverlay"></div>
<div class="sm-modal" id="newgstModal">
  <div class="sm-modal-box">
    <div class="sm-modal-header">
      <div class="sm-modal-title"><i class="bi bi-receipt-cutoff" style="color:#4f6ef7;"></i> GST Tax</div>
      <button class="sm-modal-close close-modal">&times;</button>
    </div>
    <input type="hidden" class="gst_id" id="gst_id">
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Tax Name <span style="color:red">*</span></label>
        <input type="text" class="form-control gst_name" placeholder="e.g. GST 18%" autocomplete="off">
      </div>
      <div class="sm-form-group">
        <label>Rate (%) <span style="color:red">*</span></label>
        <input type="text" class="form-control gst_rate" placeholder="e.g. 18" autocomplete="off">
      </div>
    </div>
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Tax Type <span style="color:red">*</span></label>
        <select class="tax_type" id="tax_type">
          <option value="GST">GST</option>
          <option value="IGST">IGST</option>
        </select>
      </div>
    </div>
    <div class="sm-section-label">Applicable Period</div>
    <div class="sm-form-row">
      <div class="sm-form-group">
        <label>Start Date</label>
        <input type="text" class="form-control datepicker gst_start_date" placeholder="dd/MM/yyyy" autocomplete="off">
      </div>
      <div class="sm-form-group">
        <label>End Date</label>
        <input type="text" class="form-control datepicker gst_end_date" placeholder="dd/MM/yyyy" autocomplete="off">
      </div>
    </div>
    <div class="sm-modal-footer">
      <button class="sm-btn-cancel close-modal">Cancel</button>
      <button class="sm-btn-primary gst_save">Save GST</button>
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
  $('#selectAll').on('change', function () { $('.row-check').prop('checked', $(this).prop('checked')); });
  $('#tableSearch').on('keyup', function () {
    const q = $(this).val().toLowerCase();
    $('#mainTable tbody tr').each(function () { $(this).toggle($(this).text().toLowerCase().includes(q)); });
  });

  function openModal() { $('#newgstModal').addClass('show'); $('#modalOverlay').addClass('show'); $('body').css('overflow','hidden'); }
  function closeModal() { $('#newgstModal').removeClass('show'); $('#modalOverlay').removeClass('show'); $('body').css('overflow','auto'); }

  $('.gst_new').on('click', function () {
    $('#gst_id,.gst_name,.gst_rate,.gst_start_date,.gst_end_date').val('');
    $('.tax_type').val('GST');
    openModal();
  });
  $(document).on('click', '.edit-btn', function () {
    const d = $(this).closest('tr').data('gsttax');
    $('#gst_id').val(d.id); $('.gst_name').val(d.tax_name); $('.gst_rate').val(d.tax_rate);
    $('.tax_type').val(d.tax_type); $('.gst_start_date').val(d.tax_start_date); $('.gst_end_date').val(d.tax_end_date);
    openModal();
  });
  $(document).on('click', '.close-modal, #modalOverlay', closeModal);

  $(document).on('click', '.gst_save', function (e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('id', $('.gst_id').val()); fd.append('name', $('.gst_name').val());
    fd.append('rate', $('.gst_rate').val()); fd.append('tax_type', $('.tax_type').val());
    fd.append('start_date', $('.gst_start_date').val()); fd.append('end_date', $('.gst_end_date').val());
    $.ajax({
      url:'{{ route("superadmin.getgstsave") }}', type:'POST', data:fd, processData:false, contentType:false,
      success: function (res) { if (res.success) { toastr.success(res.message); closeModal(); setTimeout(()=>window.location.reload(),800); } },
      error: function (err) { if (err.responseJSON?.errors) $.each(err.responseJSON.errors,(k,v)=>toastr.error(v[0])); }
    });
  });
  flatpickr('.datepicker', { dateFormat:'d/m/Y', allowInput:true });
});
</script>

@include('superadmin.superadminfooter')
</body>
</html>
