@extends('vms.layout')
@section('page_title','QR Management')
@section('page_subtitle','Registration QR Codes')

@section('extra_css')
<style>
.qr-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;text-align:center;transition:all 0.2s;position:relative}
.qr-card:hover{box-shadow:0 4px 20px rgba(0,0,0,0.08);transform:translateY(-2px)}
.qr-card.inactive{opacity:0.6}
.qr-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
.qr-label{font-size:13px;font-weight:700;color:var(--text);margin-top:12px}
.qr-loc{font-size:11px;color:var(--muted);margin-top:3px}
.qr-stats{display:flex;justify-content:center;gap:16px;margin:10px 0;font-size:11px;color:var(--muted)}
.qr-stats span{color:var(--text);font-weight:600}
.qr-badge{position:absolute;top:12px;right:12px;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600}
.qr-badge.active-badge{background:#dcfce7;color:#166534}
.qr-badge.inactive-badge{background:#fee2e2;color:#991b1b}
#qrCanvas{display:flex;justify-content:center;align-items:center}
</style>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <div style="font-size:13px;color:var(--muted)">{{ $qrCodes->count() }} QR code(s) configured</div>
  <button class="vbtn vbtn-primary" data-bs-toggle="modal" data-bs-target="#createQrModal">
    <i class="ti ti-plus"></i> Create New QR
  </button>
</div>

@if($qrCodes->isEmpty())
<div class="vms-card text-center" style="padding:60px 20px">
  <i class="ti ti-qrcode" style="font-size:48px;color:var(--muted);opacity:0.3;display:block;margin-bottom:12px"></i>
  <div style="font-size:14px;font-weight:600;color:var(--text)">No QR codes yet</div>
  <div style="font-size:12px;color:var(--muted);margin:4px 0 16px">Create a QR code to let visitors self-register at the entrance.</div>
  <button class="vbtn vbtn-primary" data-bs-toggle="modal" data-bs-target="#createQrModal">
    <i class="ti ti-plus"></i> Create First QR Code
  </button>
</div>
@else
<div class="qr-grid">
  @foreach($qrCodes as $qr)
  <div class="qr-card {{ !$qr->is_active ? 'inactive' : '' }}">
    <span class="qr-badge {{ $qr->is_active ? 'active-badge' : 'inactive-badge' }}">
      {{ $qr->is_active ? 'Active' : 'Inactive' }}
    </span>
    {{-- QR code rendered client-side --}}
    <div id="qr-{{ $qr->id }}" style="display:flex;justify-content:center;margin:0 auto"></div>
    <div class="qr-label">{{ $qr->label }}</div>
    @if($qr->location)
    <div class="qr-loc"><i class="ti ti-map-pin" style="font-size:13px"></i> {{ $qr->location }}</div>
    @endif
    @if($qr->branch)
    <div class="qr-loc"><i class="ti ti-building" style="font-size:13px"></i> {{ $qr->branch }}</div>
    @endif
    <div class="qr-stats">
      <div>Scans <span>{{ $qr->scan_count }}</span></div>
      <div>Visitors <span>{{ $qr->visitors_count }}</span></div>
    </div>
    <div style="font-size:10px;color:var(--muted);margin-bottom:10px;word-break:break-all">
      {{ route('vms.register', $qr->uuid) }}
    </div>
    <div class="d-flex gap-2 justify-content-center flex-wrap">
      <button class="vbtn vbtn-hold" onclick="copyUrl('{{ route('vms.register', $qr->uuid) }}')">
        <i class="ti ti-copy"></i> Copy URL
      </button>
      <button class="vbtn vbtn-hold" onclick="downloadQr({{ $qr->id }}, '{{ addslashes($qr->label) }}')">
        <i class="ti ti-download"></i> Download
      </button>
      <button class="vbtn {{ $qr->is_active ? 'vbtn-hold' : 'vbtn-approve' }}" onclick="toggleQr({{ $qr->id }}, this)">
        <i class="ti ti-{{ $qr->is_active ? 'eye-off' : 'eye' }}"></i>
        {{ $qr->is_active ? 'Disable' : 'Enable' }}
      </button>
      <button class="vbtn vbtn-reject" onclick="deleteQr({{ $qr->id }})">
        <i class="ti ti-trash"></i>
      </button>
    </div>
  </div>
  @endforeach
</div>
@endif

{{-- Create QR Modal --}}
<div class="modal fade vms-modal" id="createQrModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti ti-qrcode me-2"></i>Create New QR Code</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label" style="font-size:12px;font-weight:600">Label / Name *</label>
          <input type="text" id="qr_label" class="form-control" placeholder="e.g. Main Entrance Gate" style="font-size:12px">
          <div class="form-text" style="font-size:11px">A descriptive name for this QR code's location</div>
        </div>
        <div class="mb-3" style="position:relative">
          <label class="form-label" style="font-size:12px;font-weight:600">Location</label>
          <div class="input-group">
            <input type="text" id="qr_location_search" class="form-control" placeholder="Click to select location…"
                   style="font-size:12px" oninput="filterQrLocations(this.value)"
                   onclick="showQrDrop()" autocomplete="off" readonly>
            <span class="input-group-text" style="cursor:pointer" onclick="showQrDrop()">
              <i class="ti ti-chevron-down" style="font-size:14px;color:var(--muted)"></i>
            </span>
          </div>
          <div id="qrLocationDrop" style="display:none;border:1px solid #e2e8f0;border-radius:10px;max-height:200px;overflow-y:auto;background:#fff;box-shadow:0 8px 24px rgba(0,0,0,0.12);margin-top:4px;position:absolute;z-index:9999;width:100%;left:0">
            <div style="padding:8px 10px">
              <input type="text" id="qr_loc_inner_search" placeholder="Type to search…"
                     class="form-control form-control-sm" style="font-size:12px"
                     oninput="filterQrLocations(this.value)">
            </div>
            @foreach($locations as $loc)
            <div class="qr-loc-opt px-3 py-2" style="font-size:12px;cursor:pointer;border-top:1px solid #f1f5f9;transition:background 0.1s"
                 onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background=''"
                 onclick="selectQrLocation({{ $loc->id }}, '{{ addslashes($loc->name) }}')"
                 data-name="{{ strtolower($loc->name) }}">
              <i class="ti ti-map-pin me-1" style="font-size:13px;color:var(--accent)"></i>{{ $loc->name }}
            </div>
            @endforeach
          </div>
          <input type="hidden" id="qr_location_id">
          <input type="hidden" id="qr_location_name">
        </div>
        <div class="mb-3">
          <label class="form-label" style="font-size:12px;font-weight:600">Branch Type</label>
          <select id="qr_branch_type" class="form-select" style="font-size:12px">
            <option value="">Select branch type</option>
            <option value="hospital">Hospital</option>
            <option value="regional_office">Regional Office</option>
            <option value="clinic">Clinic</option>
            <option value="lab">Lab</option>
          </select>
        </div>
        <div style="background:var(--bg);border-radius:10px;padding:14px;text-align:center">
          <div id="newQrPreview" style="display:flex;justify-content:center;margin-bottom:8px"></div>
          <div style="font-size:11px;color:var(--muted)">QR code preview will appear after creation</div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="vbtn vbtn-hold" data-bs-dismiss="modal">Cancel</button>
        <button class="vbtn vbtn-primary" onclick="createQr()"><i class="ti ti-qrcode"></i> Generate QR</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('extra_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// Render existing QR codes
@foreach($qrCodes as $qr)
new QRCode(document.getElementById('qr-{{ $qr->id }}'), {
  text: '{{ route('vms.register', $qr->uuid) }}',
  width: 130, height: 130,
  colorDark: '#0f2d4a', colorLight: '#ffffff',
  correctLevel: QRCode.CorrectLevel.M
});
@endforeach

function showQrDrop() {
  const drop = document.getElementById('qrLocationDrop');
  drop.style.display = 'block';
  document.getElementById('qr_loc_inner_search').focus();
}

function filterQrLocations(q) {
  const term = q.toLowerCase();
  document.querySelectorAll('.qr-loc-opt').forEach(el => {
    el.style.display = el.dataset.name.includes(term) ? '' : 'none';
  });
}

function selectQrLocation(id, name) {
  $('#qr_location_id').val(id);
  $('#qr_location_name').val(name);
  $('#qr_location_search').val(name).removeAttr('readonly');
  $('#qr_location_search').attr('placeholder', name);
  $('#qrLocationDrop').hide();
}

document.addEventListener('click', function(e) {
  const wrap = document.querySelector('.mb-3 #qr_location_search')?.closest('.mb-3');
  if (wrap && !wrap.contains(e.target)) {
    document.getElementById('qrLocationDrop').style.display = 'none';
  }
});

function createQr() {
  const label = $('#qr_label').val().trim();
  if(!label) { showToast('Label is required', 'error'); return; }
  $.post('{{ route("vms.qr.create") }}', {
    label,
    location_name: $('#qr_location_name').val() || $('#qr_location_search').val(),
    location_id:   $('#qr_location_id').val(),
    branch_type:   $('#qr_branch_type').val(),
  }, function(res) {
    if(res.success){
      showToast('QR code created!', 'success');
      setTimeout(() => location.reload(), 1000);
    }
  }).fail(() => showToast('Error creating QR code', 'error'));
}

function toggleQr(id, btn) {
  $.post(`{{ url('vms/qr') }}/${id}/toggle`, {}, function(res) {
    if(res.success){
      showToast(res.is_active ? 'QR code enabled' : 'QR code disabled');
      location.reload();
    }
  });
}

function deleteQr(id) {
  if(!confirm('Delete this QR code? All visitor scan history will be retained.')) return;
  $.ajax({ url:`{{ url('vms/qr') }}/${id}`, method:'DELETE', success:function(res){
    if(res.success){ showToast('QR code deleted'); location.reload(); }
  }});
}

function copyUrl(url) {
  navigator.clipboard.writeText(url).then(() => showToast('URL copied to clipboard!'));
}

function downloadQr(id, label) {
  const canvas = document.querySelector(`#qr-${id} canvas`);
  if(!canvas) { showToast('QR not rendered yet','error'); return; }
  const link = document.createElement('a');
  link.download = `QR-${label}.png`;
  link.href = canvas.toDataURL();
  link.click();
}
</script>
@endsection
