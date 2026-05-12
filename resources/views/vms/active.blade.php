@extends('vms.layout')
@section('page_title','Active Visitors')
@section('page_subtitle','Live Inside')

@section('extra_css')
<style>
.active-table-row{display:grid;align-items:center;gap:12px;padding:12px 16px;background:var(--card);border-radius:12px;border:1px solid var(--border);margin-bottom:10px;transition:box-shadow 0.2s}
.active-table-row:hover{box-shadow:0 4px 16px rgba(0,0,0,0.07)}
.overstay-row{border-color:#fca5a5;background:#fff8f8}
.dur-badge{font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px}
.dur-ok{background:#dcfce7;color:#166534}
.dur-warn{background:#fef9c3;color:#854d0e}
.dur-over{background:#fee2e2;color:#991b1b}
.legend{display:flex;gap:16px;font-size:11px;flex-wrap:wrap}
.leg-item{display:flex;align-items:center;gap:6px}
</style>
@endsection

@section('content')

<div class="legend mb-3">
  <div class="leg-item"><span class="vdot green"></span>Within limit (&lt;{{ round($maxMinutes*0.8) }} min)</div>
  <div class="leg-item"><span class="vdot orange"></span>Nearing limit</div>
  <div class="leg-item"><span class="vdot red"></span>Overstay (&gt;{{ $maxMinutes }} min)</div>
  <span style="margin-left:auto;font-size:13px;font-weight:600;color:var(--text)">
    <i class="ti ti-live-view me-1" style="color:var(--accent)"></i>{{ $visitors->count() }} visitors inside
  </span>
</div>
@include('vms.partials.filter-bar', [
  'showType'   => false,
  'showSearch' => false,
  'showDate'   => false,
  'showStatus' => false,
  'resetRoute' => 'vms.active',
  'zones'      => $zones,
  'locations'  => $locations,
])

@forelse($visitors as $v)
@php
  $mins = $v->entry_time ? $v->entry_time->diffInMinutes(now()) : 0;
  $dotColor = $mins > $maxMinutes ? 'red' : ($mins > $maxMinutes * 0.8 ? 'orange' : 'green');
  $durClass = $mins > $maxMinutes ? 'dur-over' : ($mins > $maxMinutes*0.8 ? 'dur-warn' : 'dur-ok');
  $isOverstay = $mins > $maxMinutes;
@endphp
<div class="active-table-row {{ $isOverstay ? 'overstay-row' : '' }}" style="grid-template-columns:auto 1fr auto auto auto">
  <span class="vdot {{ $dotColor }}" style="width:10px;height:10px"></span>
  <div>
    <div style="font-size:13px;font-weight:600;color:var(--text)">
      {{ $v->visitor_name }}
      @if($v->badge_number)<span style="font-size:11px;color:var(--muted);font-weight:400"> · {{ $v->badge_number }}</span>@endif
    </div>
    <div style="font-size:11px;color:var(--muted)">
      {{ $v->visitor_type_label }} @if($v->company_name)· {{ $v->company_name }}@endif
      @if($v->person_to_meet) · Visiting: {{ $v->person_to_meet }}@endif
    </div>
  </div>
  <div style="font-size:11px;color:var(--muted);text-align:right">
    Entry: <strong style="color:var(--text)">{{ $v->entry_time ? $v->entry_time->format('h:i A') : '—' }}</strong>
  </div>
  <span class="dur-badge {{ $durClass }}">
    {{ $v->duration }} {{ $isOverstay ? '⚠ OVERSTAY' : '' }}
  </span>
  <div class="d-flex gap-2">
    <button class="vbtn vbtn-approve" onclick="checkoutVisitor({{ $v->id }}, '{{ addslashes($v->visitor_name) }}')">
      <i class="ti ti-logout"></i> Check-out
    </button>
    @if($isOverstay)
    <button class="vbtn vbtn-reject" onclick="alert('Security alert sent for {{ addslashes($v->visitor_name) }}')">
      <i class="ti ti-alert-triangle"></i> Alert
    </button>
    @endif
  </div>
</div>
@empty
<div class="vms-card text-center" style="padding:60px 20px">
  <i class="ti ti-users-group" style="font-size:48px;color:var(--muted);opacity:0.3;display:block;margin-bottom:12px"></i>
  <div style="font-size:14px;font-weight:600;color:var(--text)">No active visitors</div>
  <div style="font-size:12px;color:var(--muted);margin-top:4px">All visitors have checked out.</div>
</div>
@endforelse

@endsection

@section('extra_js')
<script>
function checkoutVisitor(id, name) {
  if(!confirm(`Check out ${name}?`)) return;
  $.post(`{{ url('vms/visitors') }}/${id}/checkout`, {}, function(res) {
    if(res.success){
      showToast(res.message, 'success');
      location.reload();
    }
  }).fail(() => showToast('Error during checkout', 'error'));
}

// Auto-refresh every 60s
setTimeout(() => location.reload(), 60000);
</script>
@endsection
