@extends('vms.layout')
@section('page_title','Settings')
@section('page_subtitle','System Configuration')

@section('content')

<div class="row g-4">
  <div class="col-lg-8">
    <div class="vms-card">
      <div class="vms-card-header">
        <div class="vms-card-title"><i class="ti ti-settings me-2" style="color:var(--accent)"></i>VMS Configuration</div>
      </div>

      <div style="border-bottom:2px solid var(--border);padding-bottom:4px;margin-bottom:20px">
        <span style="font-size:11px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.1em">Hospital Information</span>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label" style="font-size:12px;font-weight:600">Hospital Name</label>
          <input type="text" id="hospital_name" class="form-control" value="{{ $settings['hospital_name'] ?? '' }}" style="font-size:12px">
        </div>
        <div class="col-md-6">
          <label class="form-label" style="font-size:12px;font-weight:600">Default Branch</label>
          <input type="text" id="default_branch" class="form-control" value="{{ $settings['default_branch'] ?? '' }}" style="font-size:12px">
        </div>
      </div>

      <div style="border-bottom:2px solid var(--border);padding-bottom:4px;margin-bottom:20px">
        <span style="font-size:11px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.1em">Visit Rules</span>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label" style="font-size:12px;font-weight:600">Maximum Visit Duration (minutes)</label>
          <input type="number" id="max_visit_duration" class="form-control" value="{{ $settings['max_visit_duration'] ?? '60' }}" min="10" max="480" style="font-size:12px">
          <div class="form-text" style="font-size:11px">Visitors exceeding this duration will be flagged as overstay</div>
        </div>
        <div class="col-md-3">
          <label class="form-label" style="font-size:12px;font-weight:600">Auto-Approve Visitors</label>
          <select id="auto_approve" class="form-select" style="font-size:12px">
            <option value="0" {{ ($settings['auto_approve']??'0')=='0'?'selected':'' }}>No (Manual Approval)</option>
            <option value="1" {{ ($settings['auto_approve']??'0')=='1'?'selected':'' }}>Yes (Auto-Approve)</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label" style="font-size:12px;font-weight:600">OTP Verification</label>
          <select id="otp_enabled" class="form-select" style="font-size:12px">
            <option value="0" {{ ($settings['otp_enabled']??'0')=='0'?'selected':'' }}>Disabled</option>
            <option value="1" {{ ($settings['otp_enabled']??'0')=='1'?'selected':'' }}>Enabled</option>
          </select>
        </div>
      </div>

      <div style="border-bottom:2px solid var(--border);padding-bottom:4px;margin-bottom:20px">
        <span style="font-size:11px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.1em">Doctors / Contacts List</span>
      </div>
      <div class="mb-4">
        <label class="form-label" style="font-size:12px;font-weight:600">Doctors / Contacts (one per line)</label>
        <textarea id="doctors_list" class="form-control" rows="5" style="font-size:12px">{{ $settings['doctors_list'] ?? '' }}</textarea>
        <div class="form-text" style="font-size:11px">These appear in the visitor registration dropdown</div>
      </div>

      <div style="border-bottom:2px solid var(--border);padding-bottom:4px;margin-bottom:20px">
        <span style="font-size:11px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.1em">Departments</span>
      </div>
      <div class="mb-4">
        <label class="form-label" style="font-size:12px;font-weight:600">Departments (one per line)</label>
        <textarea id="departments_list" class="form-control" rows="4" style="font-size:12px">{{ $settings['departments_list'] ?? '' }}</textarea>
      </div>

      <button class="vbtn vbtn-primary" style="padding:8px 24px;font-size:13px" onclick="saveSettings()">
        <i class="ti ti-device-floppy"></i> Save Settings
      </button>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="vms-card mb-4">
      <div class="vms-card-header">
        <div class="vms-card-title"><i class="ti ti-info-circle me-2" style="color:var(--blue)"></i>System Info</div>
      </div>
      <div style="font-size:12px">
        @php
          $total = \App\Models\VmsVisitor::count();
          $qrCount = \App\Models\VmsQrCode::count();
          $blCount = \App\Models\VmsBlacklist::where('is_active',true)->count();
        @endphp
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
          <span style="color:var(--muted)">Total Visitors Registered</span>
          <span style="font-weight:600">{{ $total }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
          <span style="color:var(--muted)">Active QR Codes</span>
          <span style="font-weight:600">{{ $qrCount }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
          <span style="color:var(--muted)">Blacklisted Visitors</span>
          <span style="font-weight:600;color:var(--danger)">{{ $blCount }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:8px 0">
          <span style="color:var(--muted)">Version</span>
          <span style="font-weight:600;color:var(--accent)">VMS 1.0</span>
        </div>
      </div>
    </div>

    <div class="vms-card">
      <div class="vms-card-header">
        <div class="vms-card-title"><i class="ti ti-link me-2" style="color:var(--purple)"></i>Quick Links</div>
      </div>
      <div style="display:flex;flex-direction:column;gap:8px">
        <a href="{{ route('vms.qr') }}" class="vbtn vbtn-hold" style="justify-content:flex-start"><i class="ti ti-qrcode"></i> Manage QR Codes</a>
        <a href="{{ route('vms.reports') }}" class="vbtn vbtn-hold" style="justify-content:flex-start"><i class="ti ti-chart-bar"></i> View Reports</a>
        <a href="{{ route('vms.blacklist') }}" class="vbtn vbtn-hold" style="justify-content:flex-start"><i class="ti ti-ban"></i> Blacklist Manager</a>
      </div>
    </div>
  </div>
</div>

@endsection

@section('extra_js')
<script>
function saveSettings() {
  const data = {
    hospital_name:      $('#hospital_name').val(),
    default_branch:     $('#default_branch').val(),
    max_visit_duration: $('#max_visit_duration').val(),
    auto_approve:       $('#auto_approve').val(),
    otp_enabled:        $('#otp_enabled').val(),
    doctors_list:       $('#doctors_list').val(),
    departments_list:   $('#departments_list').val(),
  };
  $.post('{{ route("vms.settings.save") }}', data, function(res) {
    if(res.success) showToast(res.message, 'success');
  }).fail(() => showToast('Error saving settings', 'error'));
}
</script>
@endsection
