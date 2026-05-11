@extends('vms.layout')
@section('page_title','Dashboard')
@section('page_subtitle','Today\'s Overview')

@section('extra_css')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
.section-grid{display:grid;grid-template-columns:1.7fr 1fr;gap:16px;margin-bottom:16px}
.section-grid.three{grid-template-columns:repeat(3,1fr)}
@media(max-width:900px){.section-grid,.section-grid.three{grid-template-columns:1fr}}
.qa-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.qa-btn{background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:14px 10px;display:flex;flex-direction:column;align-items:center;gap:7px;cursor:pointer;transition:all 0.15s;text-decoration:none}
.qa-btn:hover{border-color:var(--accent2);background:#f0fdf4;transform:translateY(-1px)}
.qa-btn i{font-size:24px;color:var(--accent)}
.qa-btn.danger i{color:var(--danger)}
.qa-btn .qa-label{font-size:11px;font-weight:600;color:var(--text);text-align:center}
.chart-wrap{position:relative;height:180px}
.active-row{display:grid;grid-template-columns:auto 1fr auto auto;align-items:center;gap:12px;padding:10px 14px;background:var(--bg);border-radius:10px;border:1px solid var(--border);margin-bottom:8px}
.active-name{font-size:12px;font-weight:600;color:var(--text);line-height:1.3}
.active-dept{font-size:11px;color:var(--muted)}
.active-time{font-size:11px;color:var(--muted);text-align:right}
.active-dur{font-size:12px;font-weight:600;color:var(--text);text-align:right}
</style>
@endsection

@section('content')

{{-- KPI Grid --}}
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-stripe"></div>
    <div class="kpi-label">Total Visitors Today</div>
    <div class="kpi-value">{{ $stats['total_today'] }}</div>
    <div class="kpi-sub">All visitor types</div>
    <i class="ti ti-users kpi-icon"></i>
  </div>
  <div class="kpi-card blue">
    <div class="kpi-stripe"></div>
    <div class="kpi-label">Active Inside</div>
    <div class="kpi-value" id="stat-active">{{ $stats['active_inside'] }}</div>
    <div class="kpi-sub">Live now</div>
    <i class="ti ti-activity kpi-icon"></i>
  </div>
  <div class="kpi-card warn">
    <div class="kpi-stripe"></div>
    <div class="kpi-label">Pending Approvals</div>
    <div class="kpi-value" id="stat-pending">{{ $stats['pending_approvals'] }}</div>
    @if($stats['pending_approvals'] > 0)<div class="kpi-sub dn">Needs attention</div>@else<div class="kpi-sub up">All clear</div>@endif
    <i class="ti ti-clock kpi-icon"></i>
  </div>
  <div class="kpi-card purple">
    <div class="kpi-stripe"></div>
    <div class="kpi-label">Pharma Visitors</div>
    <div class="kpi-value">{{ $stats['pharma_today'] }}</div>
    <div class="kpi-sub">Today</div>
    <i class="ti ti-pill kpi-icon"></i>
  </div>
  <div class="kpi-card">
    <div class="kpi-stripe"></div>
    <div class="kpi-label">Non-Pharma Visitors</div>
    <div class="kpi-value">{{ $stats['non_pharma_today'] }}</div>
    <div class="kpi-sub">Today</div>
    <i class="ti ti-briefcase kpi-icon"></i>
  </div>
  <div class="kpi-card danger">
    <div class="kpi-stripe"></div>
    <div class="kpi-label">Blacklist Alerts</div>
    <div class="kpi-value">{{ $stats['blacklist_alerts'] }}</div>
    @if($stats['blacklist_alerts'] > 0)<div class="kpi-sub dn">Active records</div>@else<div class="kpi-sub">None active</div>@endif
    <i class="ti ti-shield-exclamation kpi-icon"></i>
  </div>
</div>

{{-- Charts row --}}
<div class="section-grid" style="margin-bottom:16px">
  <div class="vms-card">
    <div class="vms-card-header">
      <div class="vms-card-title">Hour-wise Visitor Trend</div>
      <div class="vms-card-pill">Today</div>
    </div>
    <div class="chart-wrap">
      <canvas id="hourChart"></canvas>
    </div>
  </div>
  <div class="vms-card">
    <div class="vms-card-header">
      <div class="vms-card-title">Visitor Type Split</div>
      <div class="vms-card-pill">Today</div>
    </div>
    <div class="chart-wrap" style="height:160px">
      <canvas id="typeChart"></canvas>
    </div>
    <div style="display:flex;gap:12px;justify-content:center;margin-top:10px;font-size:11px;color:var(--muted);flex-wrap:wrap">
      <span style="display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:3px;background:#1a7f64"></span>Pharma {{ $typeData['pharma'] }}</span>
      <span style="display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:3px;background:#2563eb"></span>Non-Pharma {{ $typeData['non_pharma'] }}</span>
      <span style="display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:3px;background:#7c3aed"></span>Patient Rel. {{ $typeData['patient_relative'] }}</span>
      <span style="display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:3px;background:#d97706"></span>Others {{ $typeData['others'] }}</span>
    </div>
  </div>
</div>

{{-- Quick actions + Active visitors --}}
<div class="section-grid three">
  <div class="vms-card">
    <div class="vms-card-header"><div class="vms-card-title">Quick Actions</div></div>
    <div class="qa-grid">
      <a href="{{ route('vms.approvals') }}" class="qa-btn">
        <i class="ti ti-checklist"></i><div class="qa-label">Review Approvals</div>
      </a>
      <a href="{{ route('vms.qr') }}" class="qa-btn">
        <i class="ti ti-qrcode"></i><div class="qa-label">QR Codes</div>
      </a>
      <a href="{{ route('vms.active') }}" class="qa-btn">
        <i class="ti ti-users"></i><div class="qa-label">Active Now</div>
      </a>
      <a href="{{ route('vms.history') }}" class="qa-btn">
        <i class="ti ti-history"></i><div class="qa-label">History</div>
      </a>
      <a href="{{ route('vms.reports') }}" class="qa-btn">
        <i class="ti ti-chart-bar"></i><div class="qa-label">Reports</div>
      </a>
      <a href="{{ route('vms.blacklist') }}" class="qa-btn danger">
        <i class="ti ti-ban"></i><div class="qa-label">Blacklist</div>
      </a>
    </div>
  </div>

  <div class="vms-card" style="grid-column:span 2">
    <div class="vms-card-header">
      <div class="vms-card-title">Active Visitors</div>
      <span class="vms-card-pill">{{ $stats['active_inside'] }} inside</span>
    </div>
    @forelse($activeVisitors as $v)
    @php
      $mins = $v->entry_time ? $v->entry_time->diffInMinutes(now()) : 0;
      $maxMins = (int)($settings['max_visit_duration'] ?? 60);
      $dotColor = $mins > $maxMins ? 'red' : ($mins > $maxMins * 0.8 ? 'orange' : 'green');
    @endphp
    <div class="active-row">
      <span class="vdot {{ $dotColor }}"></span>
      <div>
        <div class="active-name">{{ $v->visitor_name }}</div>
        <div class="active-dept">{{ $v->visitor_type_label }} @if($v->company_name)· {{ $v->company_name }}@endif</div>
      </div>
      <div class="active-time">Entry {{ $v->entry_time ? $v->entry_time->format('h:i A') : '—' }}</div>
      <div class="active-dur" style="color:{{ $dotColor === 'red' ? 'var(--danger)' : ($dotColor === 'orange' ? 'var(--warn)' : '#16a34a') }}">
        {{ $v->duration }} {{ $dotColor === 'red' ? '⚠' : '' }}
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:30px;color:var(--muted);font-size:13px">
      <i class="ti ti-users-group" style="font-size:32px;opacity:0.3;display:block;margin-bottom:8px"></i>
      No active visitors currently inside
    </div>
    @endforelse
    @if($stats['active_inside'] > 5)
      <a href="{{ route('vms.active') }}" class="vbtn vbtn-hold" style="margin-top:10px;font-size:11px">View all {{ $stats['active_inside'] }} →</a>
    @endif
  </div>
</div>

@endsection

@section('extra_js')
<script>
const gridColor = 'rgba(0,0,0,0.06)';
const textColor = 'rgba(0,0,0,0.45)';

new Chart(document.getElementById('hourChart'), {
  type: 'bar',
  data: {
    labels: ['8am','9am','10am','11am','12pm','1pm','2pm','3pm','4pm','5pm','6pm'],
    datasets: [{
      label: 'Visitors',
      data: @json($hourlyData),
      backgroundColor: '#1a7f6480',
      borderColor: '#1a7f64',
      borderWidth: 1.5,
      borderRadius: 5,
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } } },
      y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } }, beginAtZero: true }
    }
  }
});

new Chart(document.getElementById('typeChart'), {
  type: 'doughnut',
  data: {
    labels: ['Pharma','Non-Pharma','Patient Relative','Others'],
    datasets: [{
      data: [{{ $typeData['pharma'] }},{{ $typeData['non_pharma'] }},{{ $typeData['patient_relative'] }},{{ $typeData['others'] }}],
      backgroundColor: ['#1a7f64','#2563eb','#7c3aed','#d97706'],
      borderWidth: 0,
      hoverOffset: 6
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    cutout: '68%',
    plugins: { legend: { display: false } }
  }
});
</script>
@endsection
