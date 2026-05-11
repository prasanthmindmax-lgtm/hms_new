@extends('vms.layout')
@section('page_title','Blacklisted Visitors')
@section('page_subtitle','Security Management')

@section('content')

@if($blacklisted->where('is_active',true)->count() > 0)
<div style="background:#fff1f2;border:1px solid #fca5a5;border-radius:12px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px">
  <i class="ti ti-shield-exclamation" style="font-size:20px;color:var(--danger)"></i>
  <span style="font-size:13px;font-weight:600;color:#991b1b">{{ $blacklisted->where('is_active',true)->count() }} active blacklist record(s). Security escalation mode active.</span>
</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-4">
  <div></div>
  <button class="vbtn vbtn-danger" onclick="$('#addBlacklistModal').modal('show')">
    <i class="ti ti-plus"></i> Add to Blacklist
  </button>
</div>

@forelse($blacklisted as $b)
<div class="vms-card mb-3" style="{{ $b->is_active ? 'border-color:#fca5a5' : '' }}">
  <div class="d-flex align-items-start gap-3">
    <div style="width:44px;height:44px;background:var(--danger);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0">
      {{ strtoupper(substr($b->visitor_name,0,1)) }}
    </div>
    <div class="flex-grow-1">
      <div class="d-flex align-items-center gap-2 mb-1">
        <span style="font-size:14px;font-weight:700;color:var(--text)">{{ $b->visitor_name }}</span>
        @if($b->is_active)
          <span class="badge-blacklist">Blacklisted</span>
        @else
          <span class="vms-card-pill" style="background:#f0fdf4;color:#166534;border-color:#86efac">Removed</span>
        @endif
      </div>
      <div style="font-size:11.5px;color:var(--muted)">{{ $b->company_name ?: '—' }}@if($b->visitor_type) · {{ ucfirst(str_replace('_',' ',$b->visitor_type)) }}@endif</div>
      <div class="d-flex gap-4 flex-wrap mt-2">
        <div style="font-size:11.5px;color:var(--muted)">Reason: <span style="color:var(--danger);font-weight:600">{{ $b->reason }}</span></div>
        <div style="font-size:11.5px;color:var(--muted)">Blacklisted: <span style="color:var(--text);font-weight:500">{{ $b->blacklisted_at->format('d M Y') }}</span></div>
        <div style="font-size:11.5px;color:var(--muted)">Incidents: <span style="color:var(--text);font-weight:500">{{ $b->incidents }}</span></div>
        @if($b->visitor_phone)<div style="font-size:11.5px;color:var(--muted)">Phone: <span style="color:var(--text);font-weight:500">{{ $b->visitor_phone }}</span></div>@endif
      </div>
    </div>
    @if($b->is_active)
    <div class="d-flex gap-2 flex-shrink-0">
      <button class="vbtn vbtn-hold" onclick="removeBlacklist({{ $b->id }}, '{{ addslashes($b->visitor_name) }}')">
        <i class="ti ti-circle-check"></i> Remove
      </button>
    </div>
    @endif
  </div>
</div>
@empty
<div class="vms-card text-center" style="padding:60px 20px">
  <i class="ti ti-shield-check" style="font-size:48px;color:var(--accent);opacity:0.5;display:block;margin-bottom:12px"></i>
  <div style="font-size:14px;font-weight:600;color:var(--text)">No blacklisted visitors</div>
  <div style="font-size:12px;color:var(--muted);margin-top:4px">The blacklist is currently empty.</div>
</div>
@endforelse

{{ $blacklisted->links() }}

{{-- Add Blacklist Modal --}}
<div class="modal fade vms-modal" id="addBlacklistModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--danger)">
        <h5 class="modal-title"><i class="ti ti-ban me-2"></i>Add to Blacklist</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" style="font-size:12px;font-weight:600">Visitor Name *</label>
            <input type="text" id="bl_name" class="form-control form-control-sm" placeholder="Full name" style="font-size:12px">
          </div>
          <div class="col-md-6">
            <label class="form-label" style="font-size:12px;font-weight:600">Phone</label>
            <input type="text" id="bl_phone" class="form-control form-control-sm" placeholder="Mobile number" style="font-size:12px">
          </div>
          <div class="col-md-6">
            <label class="form-label" style="font-size:12px;font-weight:600">Company</label>
            <input type="text" id="bl_company" class="form-control form-control-sm" placeholder="Company name" style="font-size:12px">
          </div>
          <div class="col-md-6">
            <label class="form-label" style="font-size:12px;font-weight:600">Visitor Type</label>
            <select id="bl_type" class="form-select form-select-sm" style="font-size:12px">
              <option value="">Select</option>
              <option value="pharma">Pharma</option>
              <option value="non_pharma">Non-Pharma</option>
              <option value="patient_relative">Patient Relative</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label" style="font-size:12px;font-weight:600">Reason *</label>
            <textarea id="bl_reason" class="form-control form-control-sm" rows="3" placeholder="Reason for blacklisting…" style="font-size:12px"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="vbtn vbtn-hold" data-bs-dismiss="modal">Cancel</button>
        <button class="vbtn vbtn-danger" onclick="submitBlacklist()"><i class="ti ti-ban"></i> Add to Blacklist</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('extra_js')
<script>
function removeBlacklist(id, name) {
  if(!confirm(`Remove ${name} from blacklist?`)) return;
  $.post(`{{ url('vms/blacklist') }}/${id}/remove`, {}, function(res) {
    if(res.success){ showToast(res.message, 'success'); location.reload(); }
  }).fail(() => showToast('Error', 'error'));
}

function submitBlacklist() {
  const name = $('#bl_name').val().trim();
  const reason = $('#bl_reason').val().trim();
  if(!name || !reason) { showToast('Name and reason are required', 'error'); return; }
  $.post('{{ route("vms.blacklist.store") }}', {
    visitor_name: name,
    visitor_phone: $('#bl_phone').val(),
    company_name: $('#bl_company').val(),
    visitor_type: $('#bl_type').val(),
    reason: reason,
  }, function(res) {
    if(res.success){ showToast(res.message, 'success'); location.reload(); }
  }).fail(() => showToast('Error adding to blacklist', 'error'));
}
</script>
@endsection
