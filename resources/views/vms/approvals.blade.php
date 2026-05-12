@extends('vms.layout')
@section('page_title','Approvals')
@section('page_subtitle','Pending Visitors')

@section('extra_css')
<style>
.approval-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px 18px;margin-bottom:12px;transition:box-shadow 0.2s}
.approval-card:hover{box-shadow:0 4px 20px rgba(0,0,0,0.07)}
.approval-avatar{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0}
.av-green{background:var(--accent)}
.av-purple{background:var(--purple)}
.av-blue{background:var(--blue)}
.av-warn{background:var(--warn)}
.meta-item{font-size:11.5px;color:var(--muted)}
.meta-item span{color:var(--text);font-weight:600}
.filter-bar{display:flex;align-items:center;gap:10px;margin-bottom:18px;flex-wrap:wrap}
</style>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-2" style="font-size:13px">
  <span><strong style="color:var(--text)">{{ $visitors->total() }}</strong> <span style="color:var(--muted)">pending approval(s)</span></span>
</div>
@include('vms.partials.filter-bar', [
  'showType'   => true,
  'showSearch' => false,
  'showDate'   => false,
  'showStatus' => false,
  'resetRoute' => 'vms.approvals',
  'zones'      => $zones,
  'locations'  => $locations,
])

@forelse($visitors as $v)
@php
  $colors = ['pharma'=>'av-green','non_pharma'=>'av-blue','patient_relative'=>'av-purple','job_applicant'=>'av-warn'];
  $avatarClass = $colors[$v->visitor_type] ?? 'av-green';
  $initials = strtoupper(substr($v->visitor_name,0,1).(str_contains($v->visitor_name,' ')?substr(strrchr($v->visitor_name,' '),1,1):''));
@endphp
<div class="approval-card" id="card-{{ $v->id }}">
  <div class="d-flex align-items-center gap-3 mb-3">
    <div class="approval-avatar {{ $avatarClass }}">{{ $initials }}</div>
    <div class="flex-grow-1">
      <div style="font-size:13px;font-weight:700;color:var(--text);line-height:1.3">{{ $v->visitor_name }}</div>
      <div style="font-size:11px;color:var(--muted)">{{ $v->company_name ?: '—' }} · {{ $v->visitor_type_label }}</div>
    </div>
    @if($v->visitor_type === 'pharma')
      <span class="badge-pharma">Pharma</span>
    @elseif($v->visitor_type === 'non_pharma')
      <span class="badge-non">Non-Pharma</span>
    @else
      <span class="vms-card-pill">{{ $v->visitor_type_label }}</span>
    @endif
    <div style="font-size:11px;color:var(--muted)">{{ $v->created_at->diffForHumans() }}</div>
  </div>

  <div class="d-flex gap-4 flex-wrap mb-3">
    <div class="meta-item">Phone: <span>{{ $v->visitor_phone }}</span></div>
    @if($v->person_to_meet)<div class="meta-item">Doctor/Contact: <span>{{ $v->person_to_meet }}</span></div>@endif
    <div class="meta-item">Purpose: <span>{{ $v->purpose }}</span></div>
    @if($v->appointment_time)<div class="meta-item">Appt: <span>{{ $v->appointment_time }}</span></div>@endif
    @if($v->equipment_carried)<div class="meta-item">Items: <span>{{ $v->equipment_carried }}</span></div>@endif
  </div>

  <div class="d-flex gap-2 flex-wrap">
    <button class="vbtn vbtn-approve" onclick="approveVisitor({{ $v->id }})">
      <i class="ti ti-check"></i> Approve
    </button>
    <button class="vbtn vbtn-reject" onclick="showRejectModal({{ $v->id }}, '{{ addslashes($v->visitor_name) }}')">
      <i class="ti ti-x"></i> Reject
    </button>
    <button class="vbtn vbtn-hold">
      <i class="ti ti-clock-pause"></i> Hold
    </button>
    <button class="vbtn vbtn-hold" onclick="addToBlacklist({{ $v->id }}, '{{ addslashes($v->visitor_name) }}', '{{ addslashes($v->visitor_phone) }}', '{{ addslashes($v->company_name) }}')">
      <i class="ti ti-ban"></i> Blacklist
    </button>
  </div>
</div>
@empty
<div class="vms-card text-center" style="padding:60px 20px">
  <i class="ti ti-circle-check" style="font-size:48px;color:var(--accent);opacity:0.5;display:block;margin-bottom:12px"></i>
  <div style="font-size:14px;font-weight:600;color:var(--text)">All caught up!</div>
  <div style="font-size:12px;color:var(--muted);margin-top:4px">No pending approvals at the moment.</div>
</div>
@endforelse

{{ $visitors->links() }}

{{-- Reject Modal --}}
<div class="modal fade vms-modal" id="rejectModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti ti-x-circle me-2"></i>Reject Visitor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p style="font-size:13px;color:var(--muted)">Rejecting: <strong id="rejectName" style="color:var(--text)"></strong></p>
        <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:6px">Reason for rejection</label>
        <textarea class="form-control" id="rejectReason" rows="3" placeholder="Enter reason…" style="font-size:12px"></textarea>
      </div>
      <div class="modal-footer">
        <button class="vbtn vbtn-hold" data-bs-dismiss="modal">Cancel</button>
        <button class="vbtn vbtn-reject" onclick="confirmReject()"><i class="ti ti-x"></i> Confirm Reject</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('extra_js')
<script>
let rejectId = null;

function approveVisitor(id) {
  if(!confirm('Approve this visitor and record entry?')) return;
  $.post(`{{ url('vms/approvals') }}/${id}/approve`, {}, function(res) {
    if(res.success){
      showToast(res.message, 'success');
      $(`#card-${id}`).fadeOut(300, function(){ $(this).remove(); });
    }
  }).fail(() => showToast('Error approving visitor', 'error'));
}

function showRejectModal(id, name) {
  rejectId = id;
  $('#rejectName').text(name);
  $('#rejectReason').val('');
  new bootstrap.Modal('#rejectModal').show();
}

function confirmReject() {
  const reason = $('#rejectReason').val().trim();
  $.post(`{{ url('vms/approvals') }}/${rejectId}/reject`, { reason }, function(res) {
    if(res.success){
      showToast(res.message, 'success');
      $(`#card-${rejectId}`).fadeOut(300, function(){ $(this).remove(); });
      bootstrap.Modal.getInstance('#rejectModal').hide();
    }
  }).fail(() => showToast('Error rejecting visitor', 'error'));
}

function addToBlacklist(id, name, phone, company) {
  if(!confirm(`Add ${name} to blacklist?`)) return;
  $.post('{{ route("vms.blacklist.store") }}', {
    visitor_name: name,
    visitor_phone: phone,
    company_name: company,
    reason: 'Added during approval review',
  }, function(res) {
    if(res.success) showToast('Added to blacklist.', 'success');
  });
}
</script>
@endsection
