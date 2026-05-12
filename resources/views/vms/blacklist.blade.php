@extends('vms.layout')
@section('page_title','Blacklisted Visitors')
@section('page_subtitle','Security Management')

@section('extra_css')
<style>
/* ── Tab pills ─────────────────────────────────────── */
.bl-tabs{display:flex;gap:8px;margin-bottom:20px;border-bottom:2px solid var(--border);padding-bottom:0}
.bl-tab{
  padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;
  border:none;background:none;color:var(--muted);
  border-bottom:2px solid transparent;margin-bottom:-2px;
  transition:all 0.15s;border-radius:8px 8px 0 0;
}
.bl-tab:hover{color:var(--text);background:var(--bg)}
.bl-tab.active{color:var(--danger);border-bottom-color:var(--danger);background:none}
.bl-tab.active.green{color:var(--accent);border-bottom-color:var(--accent)}
.bl-tab .tab-count{
  font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;
  margin-left:6px;color:#fff;
}
.bl-tab .tab-count.danger{background:var(--danger)}
.bl-tab .tab-count.green{background:var(--accent)}
.bl-tab-panel{display:none}
.bl-tab-panel.active{display:block}

/* ── Active visitor row ────────────────────────────── */
.av-row{
  background:var(--card);border:1px solid var(--border);
  border-radius:12px;padding:14px 18px;margin-bottom:10px;
  display:flex;align-items:center;gap:14px;
  transition:box-shadow 0.2s;
}
.av-row:hover{box-shadow:0 4px 16px rgba(0,0,0,0.07)}
.av-avatar{
  width:42px;height:42px;border-radius:50%;flex-shrink:0;
  background:var(--accent);display:flex;align-items:center;
  justify-content:center;font-size:14px;font-weight:700;color:#fff;
}

/* ── Select source toggle in modal ──────────────────── */
.src-toggle{display:flex;gap:8px;margin-bottom:14px}
.src-btn{
  flex:1;padding:8px 12px;font-size:12px;font-weight:600;
  border:1.5px solid var(--border);border-radius:8px;cursor:pointer;
  background:var(--bg);color:var(--muted);text-align:center;transition:all 0.15s;
}
.src-btn.active{border-color:var(--danger);background:#fff1f2;color:var(--danger)}
.src-btn.active.green{border-color:var(--accent);background:#f0fdf4;color:var(--accent)}

/* ── Active visitor picker ──────────────────────────── */
.av-picker{border:1.5px solid var(--border);border-radius:10px;max-height:200px;overflow-y:auto;margin-bottom:14px}
.av-pick-item{
  display:flex;align-items:center;gap:10px;padding:10px 14px;
  cursor:pointer;border-bottom:1px solid var(--border);
  transition:background 0.12s;
}
.av-pick-item:last-child{border-bottom:none}
.av-pick-item:hover{background:#f0fdf4}
.av-pick-item.selected{background:#dcfce7;border-left:3px solid var(--accent)}
.av-pick-dot{width:8px;height:8px;border-radius:50%;background:var(--accent);flex-shrink:0}
</style>
@endsection

@section('content')

@php
  $activeCount     = $blacklisted->where('is_active',true)->count();
  $activeVisCount  = $activeVisitors->count();
  $defaultTab      = request('tab', 'blacklist');
@endphp

{{-- Security alert banner --}}
@if($activeCount > 0)
<div style="background:#fff1f2;border:1px solid #fca5a5;border-radius:12px;padding:11px 18px;margin-bottom:18px;display:flex;align-items:center;gap:10px">
  <i class="ti ti-shield-exclamation" style="font-size:20px;color:var(--danger)"></i>
  <span style="font-size:13px;font-weight:600;color:#991b1b">
    {{ $activeCount }} active blacklist record(s) — Security escalation mode active.
  </span>
</div>
@endif

{{-- Toolbar --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <form method="GET" class="d-flex gap-2">
    <input type="hidden" name="tab" id="activeTabInput" value="{{ $defaultTab }}">
    <input type="text" name="search" value="{{ request('search') }}"
           class="form-control form-control-sm" placeholder="Search name, phone, company…"
           style="font-size:12px;border-radius:8px;width:240px">
    <button type="submit" class="vbtn vbtn-primary"><i class="ti ti-search"></i></button>
    @if(request('search'))<a href="{{ route('vms.blacklist') }}" class="vbtn vbtn-hold"><i class="ti ti-x"></i></a>@endif
  </form>
  <button class="vbtn vbtn-danger" onclick="openBlacklistModal()">
    <i class="ti ti-plus"></i> Add to Blacklist
  </button>
</div>

{{-- Tabs --}}
<div class="bl-tabs">
  <button class="bl-tab {{ $defaultTab !== 'active' ? 'active' : '' }}" onclick="switchTab('blacklist')" id="tab-blacklist">
    <i class="ti ti-ban me-1"></i>Blacklisted
    @if($activeCount > 0)<span class="tab-count danger">{{ $activeCount }}</span>@endif
  </button>
  <button class="bl-tab green {{ $defaultTab === 'active' ? 'active green' : '' }}" onclick="switchTab('active')" id="tab-active">
    <i class="ti ti-users me-1"></i>Active Visitors
    @if($activeVisCount > 0)<span class="tab-count green">{{ $activeVisCount }}</span>@endif
  </button>
</div>

{{-- ═══ TAB: BLACKLISTED ════════════════════════════════════════════════ --}}
<div class="bl-tab-panel {{ $defaultTab !== 'active' ? 'active' : '' }}" id="panel-blacklist">

  @forelse($blacklisted as $b)
  <div class="vms-card mb-3" style="{{ $b->is_active ? 'border-color:#fca5a5' : '' }}" id="bl-card-{{ $b->id }}">
    <div class="d-flex align-items-start gap-3">
      <div style="width:44px;height:44px;background:{{ $b->is_active ? 'var(--danger)' : 'var(--muted)' }};border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0">
        {{ strtoupper(substr($b->visitor_name,0,1)) }}
      </div>
      <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
          <span style="font-size:14px;font-weight:700;color:var(--text)">{{ $b->visitor_name }}</span>
          @if($b->is_active)
            <span class="badge-blacklist">Blacklisted</span>
          @else
            <span class="vms-card-pill" style="background:#f0fdf4;color:#166534;border-color:#86efac;font-size:10px">Removed</span>
          @endif
        </div>
        <div style="font-size:11.5px;color:var(--muted)">
          {{ $b->company_name ?: '—' }}
          @if($b->visitor_type) · {{ ucfirst(str_replace('_',' ',$b->visitor_type)) }}@endif
        </div>
        <div class="d-flex gap-4 flex-wrap mt-2">
          <div style="font-size:11.5px;color:var(--muted)">Reason: <span style="color:var(--danger);font-weight:600">{{ $b->reason }}</span></div>
          <div style="font-size:11.5px;color:var(--muted)">Date: <span style="color:var(--text);font-weight:500">{{ $b->blacklisted_at->format('d M Y') }}</span></div>
          @if($b->visitor_phone)<div style="font-size:11.5px;color:var(--muted)">Phone: <span style="color:var(--text);font-weight:500">{{ $b->visitor_phone }}</span></div>@endif
          <div style="font-size:11.5px;color:var(--muted)">Incidents: <span style="color:var(--text);font-weight:500">{{ $b->incidents }}</span></div>
        </div>
      </div>
      @if($b->is_active)
      <div class="flex-shrink-0 d-flex gap-2">
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
  <div class="mt-2">{{ $blacklisted->appends(request()->query())->links() }}</div>
</div>

{{-- ═══ TAB: ACTIVE VISITORS ══════════════════════════════════════════ --}}
<div class="bl-tab-panel {{ $defaultTab === 'active' ? 'active' : '' }}" id="panel-active">

  @if($activeVisitors->isEmpty())
  <div class="vms-card text-center" style="padding:60px 20px">
    <i class="ti ti-users-group" style="font-size:48px;color:var(--muted);opacity:0.3;display:block;margin-bottom:12px"></i>
    <div style="font-size:14px;font-weight:600;color:var(--text)">No active visitors right now</div>
    <div style="font-size:12px;color:var(--muted);margin-top:4px">All visitors have checked out.</div>
  </div>
  @else
  <div class="vms-card mb-3" style="padding:12px 18px;background:#f0fdf4;border-color:#86efac">
    <div style="font-size:12px;color:#166534;font-weight:500">
      <i class="ti ti-info-circle me-1"></i>
      {{ $activeVisitors->count() }} visitor(s) currently inside. Click <strong>Blacklist</strong> to add any to the security list.
    </div>
  </div>

  @foreach($activeVisitors as $v)
  @php
    $mins = $v->entry_time ? \Carbon\Carbon::parse($v->entry_time)->diffInMinutes(now()) : 0;
    $initials = strtoupper(substr($v->visitor_name,0,1));
  @endphp
  <div class="av-row" id="av-row-{{ $v->id }}">
    <div class="av-avatar">{{ $initials }}</div>
    <div class="flex-grow-1">
      <div style="font-size:13px;font-weight:700;color:var(--text)">{{ $v->visitor_name }}</div>
      <div style="font-size:11.5px;color:var(--muted)">
        {{ $v->visitor_phone }}
        @if($v->company_name) · {{ $v->company_name }}@endif
        @if($v->person_to_meet) · Visiting: {{ $v->person_to_meet }}@endif
      </div>
      <div class="d-flex align-items-center gap-3 mt-1 flex-wrap">
        @if($v->visitor_type === 'pharma')
          <span class="badge-pharma">Pharma</span>
        @elseif($v->visitor_type === 'non_pharma')
          <span class="badge-non">Non-Pharma</span>
        @else
          <span class="vms-card-pill" style="font-size:10px">{{ ucfirst(str_replace('_',' ',$v->visitor_type)) }}</span>
        @endif
        <span style="font-size:11px;color:var(--muted)">
          <span class="vdot green"></span>
          Inside {{ $mins }} min
          @if($v->entry_time) · Entry: {{ \Carbon\Carbon::parse($v->entry_time)->format('h:i A') }}@endif
        </span>
      </div>
    </div>
    <div class="d-flex gap-2 flex-shrink-0">
      <button class="vbtn vbtn-hold"
        onclick="checkoutVisitor({{ $v->id }}, '{{ addslashes($v->visitor_name) }}')">
        <i class="ti ti-logout"></i> Check-out
      </button>
      <button class="vbtn vbtn-danger"
        onclick="openBlacklistModalFromVisitor({{ $v->id }}, '{{ addslashes($v->visitor_name) }}', '{{ addslashes($v->visitor_phone) }}', '{{ addslashes($v->company_name) }}', '{{ $v->visitor_type }}')">
        <i class="ti ti-ban"></i> Blacklist
      </button>
    </div>
  </div>
  @endforeach
  @endif
</div>

{{-- ═══ ADD TO BLACKLIST MODAL ══════════════════════════════════════════ --}}
<div class="modal fade vms-modal" id="addBlacklistModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--danger)">
        <h5 class="modal-title"><i class="ti ti-ban me-2"></i>Add to Blacklist</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        {{-- Source toggle --}}
        <div class="src-toggle" id="sourceToggle">
          <div class="src-btn active" id="srcActive" onclick="setSource('active')" style="{{ $activeVisitors->isEmpty() ? 'opacity:0.4;pointer-events:none' : '' }}">
            <i class="ti ti-users me-1"></i>From Active Visitors
            @if(!$activeVisitors->isEmpty())<span style="font-size:10px;background:var(--danger);color:#fff;padding:1px 6px;border-radius:20px;margin-left:4px">{{ $activeVisitors->count() }}</span>@endif
          </div>
          <div class="src-btn {{ $activeVisitors->isEmpty() ? 'active' : '' }}" id="srcManual" onclick="setSource('manual')">
            <i class="ti ti-pencil me-1"></i>Manual Entry
          </div>
        </div>

        {{-- Active visitor picker (shown when source = active) --}}
        <div id="avPickerWrap" style="{{ $activeVisitors->isEmpty() ? 'display:none' : '' }}">
          <div style="font-size:11.5px;font-weight:600;color:var(--muted);margin-bottom:6px">Select visitor to blacklist:</div>
          <div class="av-picker">
            @foreach($activeVisitors as $v)
            <div class="av-pick-item" id="pick-{{ $v->id }}"
                 onclick="pickVisitor({{ $v->id }}, '{{ addslashes($v->visitor_name) }}', '{{ addslashes($v->visitor_phone) }}', '{{ addslashes($v->company_name) }}', '{{ $v->visitor_type }}')">
              <div class="av-pick-dot"></div>
              <div style="flex:1;min-width:0">
                <div style="font-size:12.5px;font-weight:600;color:var(--text)">{{ $v->visitor_name }}</div>
                <div style="font-size:11px;color:var(--muted)">
                  {{ $v->visitor_phone }}
                  @if($v->company_name) · {{ $v->company_name }}@endif
                </div>
              </div>
              <i class="ti ti-chevron-right" style="font-size:14px;color:var(--muted)"></i>
            </div>
            @endforeach
          </div>
          <div id="pickedVisitorInfo" style="display:none;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:10px 14px;margin-bottom:6px;font-size:12px;color:#166534">
            <i class="ti ti-circle-check me-1"></i><strong id="pickedName"></strong> selected
          </div>
        </div>

        {{-- Form fields --}}
        <div id="blFormFields">
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
                <option value="job_applicant">Job Applicant</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label" style="font-size:12px;font-weight:600">Reason for Blacklisting *</label>
              <textarea id="bl_reason" class="form-control form-control-sm" rows="3"
                        placeholder="Reason for blacklisting…" style="font-size:12px"></textarea>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="vbtn vbtn-hold" data-bs-dismiss="modal">Cancel</button>
        <button class="vbtn vbtn-danger" onclick="submitBlacklist()">
          <i class="ti ti-ban"></i> Add to Blacklist
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('extra_js')
<script>
// ── Tab switching ────────────────────────────────────
function switchTab(tab) {
  document.querySelectorAll('.bl-tab').forEach(t => t.classList.remove('active','green'));
  document.querySelectorAll('.bl-tab-panel').forEach(p => p.classList.remove('active'));

  const btn = document.getElementById('tab-' + tab);
  btn.classList.add('active');
  if (tab === 'active') btn.classList.add('green');
  document.getElementById('panel-' + tab).classList.add('active');
  document.getElementById('activeTabInput').value = tab;
}

// ── Blacklist modal helpers ──────────────────────────
let selectedVisitorId = null;
let blSource = '{{ $activeVisitors->isEmpty() ? "manual" : "active" }}';

function setSource(src) {
  blSource = src;
  document.getElementById('srcActive').classList.toggle('active', src === 'active');
  document.getElementById('srcManual').classList.toggle('active', src === 'manual');
  document.getElementById('avPickerWrap').style.display = src === 'active' ? '' : 'none';
  // Clear fields when switching
  if (src === 'manual') {
    clearBlForm();
    selectedVisitorId = null;
    document.getElementById('pickedVisitorInfo').style.display = 'none';
    document.querySelectorAll('.av-pick-item').forEach(i => i.classList.remove('selected'));
  }
}

function clearBlForm() {
  ['bl_name','bl_phone','bl_company','bl_reason'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('bl_type').value = '';
}

function openBlacklistModal() {
  selectedVisitorId = null;
  clearBlForm();
  document.getElementById('pickedVisitorInfo').style.display = 'none';
  document.querySelectorAll('.av-pick-item').forEach(i => i.classList.remove('selected'));
  // Reset source
  const hasActive = {{ $activeVisitors->isNotEmpty() ? 'true' : 'false' }};
  setSource(hasActive ? 'active' : 'manual');
  new bootstrap.Modal('#addBlacklistModal').show();
}

function openBlacklistModalFromVisitor(id, name, phone, company, type) {
  selectedVisitorId = id;
  clearBlForm();
  fillForm(name, phone, company, type);
  // Mark selected in picker
  document.querySelectorAll('.av-pick-item').forEach(i => i.classList.remove('selected'));
  const pick = document.getElementById('pick-' + id);
  if (pick) pick.classList.add('selected');
  document.getElementById('pickedName').textContent = name;
  document.getElementById('pickedVisitorInfo').style.display = '';
  setSource('active');
  new bootstrap.Modal('#addBlacklistModal').show();
}

function pickVisitor(id, name, phone, company, type) {
  selectedVisitorId = id;
  document.querySelectorAll('.av-pick-item').forEach(i => i.classList.remove('selected'));
  document.getElementById('pick-' + id).classList.add('selected');
  fillForm(name, phone, company, type);
  document.getElementById('pickedName').textContent = name;
  document.getElementById('pickedVisitorInfo').style.display = '';
}

function fillForm(name, phone, company, type) {
  document.getElementById('bl_name').value    = name    || '';
  document.getElementById('bl_phone').value   = phone   || '';
  document.getElementById('bl_company').value = company || '';
  document.getElementById('bl_type').value    = type    || '';
}

// ── Submit blacklist ─────────────────────────────────
function submitBlacklist() {
  const name   = $('#bl_name').val().trim();
  const reason = $('#bl_reason').val().trim();
  if (!name)   { showToast('Visitor name is required', 'error'); return; }
  if (!reason) { showToast('Reason is required', 'error'); return; }

  $.post('{{ route("vms.blacklist.store") }}', {
    visitor_name:  name,
    visitor_phone: $('#bl_phone').val(),
    company_name:  $('#bl_company').val(),
    visitor_type:  $('#bl_type').val(),
    reason:        reason,
    visitor_id:    selectedVisitorId || '',
  }, function(res) {
    if (res.success) {
      showToast(res.message, 'success');
      bootstrap.Modal.getInstance('#addBlacklistModal').hide();
      setTimeout(() => location.reload(), 800);
    }
  }).fail(() => showToast('Error adding to blacklist', 'error'));
}

// ── Remove from blacklist ────────────────────────────
function removeBlacklist(id, name) {
  if (!confirm(`Remove "${name}" from blacklist?`)) return;
  $.post(`{{ url('vms/blacklist') }}/${id}/remove`, {}, function(res) {
    if (res.success) {
      showToast(res.message, 'success');
      const card = document.getElementById('bl-card-' + id);
      if (card) card.style.opacity = '0.4';
      setTimeout(() => location.reload(), 800);
    }
  }).fail(() => showToast('Error removing from blacklist', 'error'));
}

// ── Check-out from active tab ────────────────────────
function checkoutVisitor(id, name) {
  if (!confirm(`Check out "${name}"?`)) return;
  $.post(`{{ url('vms/visitors') }}/${id}/checkout`, {}, function(res) {
    if (res.success) {
      showToast(res.message, 'success');
      document.getElementById('av-row-' + id)?.remove();
    }
  }).fail(() => showToast('Error during checkout', 'error'));
}
</script>
@endsection
