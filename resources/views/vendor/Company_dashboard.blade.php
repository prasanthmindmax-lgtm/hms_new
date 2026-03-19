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
  .sm-modal { display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; overflow-y:auto; padding:20px 0; }
  .sm-modal.show { display:flex; }
  .sm-modal-overlay.show { display:block; }
  .sm-modal-box { background:#fff; border-radius:14px; padding:28px 30px; width:100%; max-width:820px; box-shadow:0 20px 60px rgba(0,0,0,.18); position:relative; animation:smSlideIn .2s ease; }
  @keyframes smSlideIn { from{transform:translateY(-20px);opacity:0} to{transform:translateY(0);opacity:1} }
  .sm-modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
  .sm-modal-title { font-size:16px; font-weight:700; color:#1f2937; display:flex; align-items:center; gap:8px; }
  .sm-modal-close { width:30px;height:30px;border-radius:50%;background:#f3f4f6;border:none;cursor:pointer;font-size:18px;color:#6b7280;display:flex;align-items:center;justify-content:center; }
  .sm-modal-close:hover { background:#e5e7eb; }
  .sm-form-row { display:flex; gap:14px; margin-bottom:16px; }
  .sm-form-group { flex:1; }
  .sm-form-group label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
  .sm-form-group .form-control, .sm-form-group textarea { border-radius:8px; border:1px solid #d1d5db; font-size:13px; padding:8px 12px; width:100%; }
  .sm-form-group textarea { resize:vertical; min-height:70px; }
  .sm-section-label { font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin:16px 0 10px; padding-bottom:6px; border-bottom:1px solid #f3f4f6; }
  .sm-modal-footer { display:flex; gap:10px; margin-top:22px; justify-content:flex-end; }
  .sm-btn-primary { padding:9px 22px; background:linear-gradient(135deg,#4f6ef7,#7c3aed); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
  .sm-btn-cancel { padding:9px 22px; background:#f3f4f6; color:#374151; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
  #logoPreview { margin-top:10px; max-height:100px; width:200px; display:none; border-radius:8px; border:1px solid #e5e7eb; }
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
          <i class="bi bi-building"></i> Companies
        </div>
        <div class="qd-header-actions">
          <button class="btn btn-primary btn-sm gst_new">
            <i class="bi bi-plus-lg me-1"></i>New Company
          </button>
        </div>
      </div>

      <div class="qd-search-row">
        <div class="qd-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" id="tableSearch" placeholder="Search company name, GST, phone...">
        </div>
      </div>

      <div class="qdt-wrap">
        <table class="qdt-table" id="mainTable">
          <thead class="qdt-head">
            <tr>
              <th class="qdt-th-check"><input type="checkbox" id="selectAll"></th>
              <th>COMPANY NAME</th>
              <th>REG NO / CIN</th>
              <th>ADDRESS</th>
              <th>EMAIL</th>
              <th>PHONE</th>
              <th>GST NUMBER</th>
              <th class="text-center">ACTION</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($Tblcompany as $v)
              <tr class="qdt-row customer-row" data-id="{{ $v->id }}" data-company="{{ $v }}">
                <td class="qdt-td-check"><input type="checkbox" class="row-check"></td>
                <td>
                  <div style="font-weight:700;color:#1f2937;font-size:13px;">{{ $v->company_name }}</div>
                </td>
                <td class="qdt-mono">{{ $v->reg_number ?? '—' }}</td>
                <td style="font-size:12px;color:#6b7280;max-width:160px;">{{ Str::limit($v->address ?? '—', 40) }}</td>
                <td style="font-size:12px;">{{ $v->email ?? '—' }}</td>
                <td class="qdt-mono">{{ $v->phone ?? '—' }}</td>
                <td class="qdt-mono">{{ $v->gst_number ?? '—' }}</td>
                <td class="text-center">
                  <button class="btn btn-sm edit-btn"
                    style="font-size:11px;padding:4px 14px;border:1px solid #4f6ef7;color:#4f6ef7;border-radius:6px;background:#fff;">
                    <i class="bi bi-pencil me-1"></i>Edit
                  </button>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>No companies found
              </td></tr>
            @endforelse
          </tbody>
        </table>

        @if($Tblcompany->total() > 10)
        <div class="qd-pagination d-flex justify-content-between align-items-center mt-2 px-1">
          <div>{{ $Tblcompany->links('pagination::bootstrap-4') }}</div>
          <div style="display:flex;align-items:center;gap:6px;">
            <select id="per_page" class="form-control form-control-sm" style="width:70px;">
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

<div class="sm-modal-overlay" id="modalOverlay"></div>
<div class="sm-modal" id="newgstModal">
  <div class="sm-modal-box">
    <div class="sm-modal-header">
      <div class="sm-modal-title"><i class="bi bi-building" style="color:#4f6ef7;"></i> Company</div>
      <button class="sm-modal-close close-modal">&times;</button>
    </div>
    <form id="company_form">
      <input type="hidden" class="company_id" name="company_id" id="company_id">
      <div class="sm-form-row">
        <div class="sm-form-group">
          <label>Company Name <span style="color:red">*</span></label>
          <input type="text" class="form-control company_name" name="company_name" placeholder="Company name" autocomplete="off">
        </div>
        <div class="sm-form-group">
          <label>Registration No / CIN</label>
          <input type="text" class="form-control reg_number" name="reg_number" placeholder="e.g. U12345MH2020PTC123456" autocomplete="off">
        </div>
      </div>
      <div class="sm-form-row">
        <div class="sm-form-group">
          <label>Address</label>
          <textarea class="form-control address" name="address" rows="2" placeholder="Full address"></textarea>
        </div>
      </div>
      <div class="sm-form-row">
        <div class="sm-form-group">
          <label>Email</label>
          <input type="text" class="form-control email" name="email" placeholder="company@email.com" autocomplete="off">
        </div>
        <div class="sm-form-group">
          <label>Phone</label>
          <input type="text" class="form-control phone" name="phone" placeholder="+91 xxxxxxxxxx" autocomplete="off">
        </div>
        <div class="sm-form-group">
          <label>GST Number</label>
          <input type="text" class="form-control gst_number" name="gst_number" placeholder="GST number" autocomplete="off">
        </div>
      </div>
      <div class="sm-form-row">
        <div class="sm-form-group">
          <label>Website</label>
          <input type="text" class="form-control website" name="website" placeholder="https://..." autocomplete="off">
        </div>
      </div>
      <div class="sm-section-label">Location</div>
      <div class="sm-form-row">
        <div class="sm-form-group">
          <label>City</label>
          <input type="text" class="form-control city_name" name="city_name" placeholder="City" autocomplete="off">
        </div>
        <div class="sm-form-group">
          <label>State</label>
          <input type="text" class="form-control state" name="state" placeholder="State" autocomplete="off">
        </div>
        <div class="sm-form-group">
          <label>Country</label>
          <input type="text" class="form-control country" name="country" placeholder="Country" autocomplete="off">
        </div>
        <div class="sm-form-group">
          <label>ZIP / Postal Code</label>
          <input type="text" class="form-control zip_code" name="zip_code" placeholder="ZIP code" autocomplete="off">
        </div>
      </div>
      <div class="sm-form-row">
        <div class="sm-form-group">
          <label>Company Logo</label>
          <input type="file" class="form-control logo_upload" name="logo_upload" accept="image/*">
          <img id="logoPreview" src="" alt="Logo Preview">
        </div>
      </div>
      <div class="sm-modal-footer">
        <button type="button" class="sm-btn-cancel close-modal">Cancel</button>
        <button type="submit" class="sm-btn-primary company_save">Save Company</button>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
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

  $('.gst_new').on('click', function () { $('#company_form')[0].reset(); $('#logoPreview').hide(); openModal(); });
  $(document).on('click', '.edit-btn', function () {
    const c = $(this).closest('tr').data('company');
    $('.company_id').val(c.id); $('.company_name').val(c.company_name); $('.reg_number').val(c.reg_number);
    $('.address').val(c.address); $('.email').val(c.email); $('.phone').val(c.phone);
    $('.gst_number').val(c.gst_number); $('.website').val(c.website); $('.city_name').val(c.city);
    $('.state').val(c.state); $('.country').val(c.country); $('.zip_code').val(c.zip_code);
    if (c.logo_upload) { $('#logoPreview').attr('src','../uploads/vendor/company/'+c.logo_upload).show(); }
    else { $('#logoPreview').hide(); }
    openModal();
  });
  $(document).on('click', '.close-modal, #modalOverlay', closeModal);

  $(document).on('change', '.logo_upload', function (e) {
    const file = e.target.files[0];
    if (file) { const r = new FileReader(); r.onload = e => $('#logoPreview').attr('src',e.target.result).show(); r.readAsDataURL(file); }
  });

  $(document).on('click', '.company_save', function (e) {
    e.preventDefault();
    $.ajax({
      url:'{{ route("superadmin.getcompanysave") }}', type:'POST', data:new FormData($('#company_form')[0]), processData:false, contentType:false,
      success: function (res) { if (res.success) { toastr.success(res.message); closeModal(); setTimeout(()=>window.location.reload(),800); } },
      error: function (err) { if (err.responseJSON?.errors) $.each(err.responseJSON.errors,(k,v)=>toastr.error(v[0])); }
    });
  });
});
</script>

@include('superadmin.superadminfooter')
</body>
</html>
