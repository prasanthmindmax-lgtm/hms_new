@extends('vms.layout')
@section('page_title','Reports')
@section('page_subtitle','Analytics & Insights')

@section('extra_css')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
.chart-wrap{height:220px}
.metric-tile{text-align:center;padding:16px;background:var(--bg);border-radius:12px}
.metric-tile .mt-val{font-size:22px;font-weight:700;color:var(--text);margin-top:4px}
.metric-tile .mt-lbl{font-size:11px;color:var(--muted)}
.metric-tile .mt-sub{font-size:11px;margin-top:3px}
</style>
@endsection

@section('content')

{{-- Month picker --}}
<form method="GET" class="vms-card mb-4" style="padding:14px 18px">
  <div class="d-flex align-items-center gap-3 flex-wrap">
    <div class="vms-card-title">Report Period</div>
    <input type="month" name="month" class="form-control form-control-sm" value="{{ $month }}" style="width:auto;font-size:12px">
    <button type="submit" class="vbtn vbtn-primary"><i class="ti ti-chart-bar"></i> Generate</button>
  </div>
</form>

{{-- Key metrics --}}
<div class="vms-card mb-4">
  <div class="vms-card-header">
    <div class="vms-card-title">Key Metrics — {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</div>
  </div>
  <div class="row g-3">
    <div class="col-6 col-md-3">
      <div class="metric-tile">
        <div class="mt-lbl">Total Visitors</div>
        <div class="mt-val" style="color:var(--accent)">{{ $totalMonth }}</div>
        <div class="mt-sub" style="color:var(--muted)">This month</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="metric-tile">
        <div class="mt-lbl">Top Pharma Company</div>
        <div class="mt-val" style="font-size:15px">{{ $topPharma?->company_name ?: '—' }}</div>
        <div class="mt-sub" style="color:var(--muted)">{{ $topPharma ? $topPharma->total.' visits' : '' }}</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="metric-tile">
        <div class="mt-lbl">Avg Visit Duration</div>
        <div class="mt-val" style="color:{{ $avgDuration > 45 ? 'var(--warn)' : 'var(--text)' }}">{{ round($avgDuration) }} min</div>
        <div class="mt-sub" style="color:{{ $avgDuration > 60 ? 'var(--danger)' : 'var(--muted)' }}">
          {{ $avgDuration > 60 ? '⚠ Above limit' : 'Average' }}
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="metric-tile">
        <div class="mt-lbl">Repeat Visitors</div>
        <div class="mt-val" style="color:var(--blue)">{{ $repeatRate }}%</div>
        <div class="mt-sub" style="color:var(--muted)">Known visitors</div>
      </div>
    </div>
  </div>
</div>

{{-- Charts row --}}
<div class="row g-4 mb-4">
  <div class="col-lg-6">
    <div class="vms-card">
      <div class="vms-card-header">
        <div class="vms-card-title">Daily Visitor Trend</div>
        <span class="vms-card-pill">{{ \Carbon\Carbon::parse($month.'-01')->format('M Y') }}</span>
      </div>
      <div class="chart-wrap">
        <canvas id="dailyChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="vms-card">
      <div class="vms-card-header">
        <div class="vms-card-title">Pharma Visits by Company</div>
      </div>
      <div class="chart-wrap">
        <canvas id="pharmaChart"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="vms-card">
      <div class="vms-card-header">
        <div class="vms-card-title">Doctor / Contact-wise Visits</div>
      </div>
      <div class="chart-wrap">
        <canvas id="doctorChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="vms-card">
      <div class="vms-card-header">
        <div class="vms-card-title">Top Companies</div>
      </div>
      <div style="margin-top:4px">
        @foreach($pharmaByCompany as $i => $co)
        <div style="display:flex;align-items:center;gap-12;padding:8px 0;border-bottom:1px solid var(--border)">
          <span style="font-size:11px;color:var(--muted);width:20px">{{ $i+1 }}</span>
          <div class="flex-grow-1 px-2">
            <div style="font-size:12px;font-weight:600;color:var(--text)">{{ $co->company_name ?: 'Unknown' }}</div>
            <div style="background:var(--accent);height:4px;border-radius:2px;margin-top:4px;width:{{ $pharmaByCompany->first()?->total > 0 ? round(($co->total/$pharmaByCompany->first()->total)*100) : 0 }}%"></div>
          </div>
          <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;flex-shrink:0">{{ $co->total }}</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

@endsection

@section('extra_js')
<script>
const gc = 'rgba(0,0,0,0.06)';
const tc = 'rgba(0,0,0,0.45)';
const opt = (axis='x') => ({ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
  scales:{
    [axis]:{ grid:{color:gc}, ticks:{color:tc,font:{size:10}} },
    [axis==='x'?'y':'x']:{ grid:{color:gc}, ticks:{color:tc,font:{size:10}}, beginAtZero:true }
  }
});

new Chart(document.getElementById('dailyChart'), {
  type: 'line',
  data: {
    labels: @json($daily->pluck('date')->map(fn($d)=>\Carbon\Carbon::parse($d)->format('d M'))->toArray()),
    datasets: [{
      label: 'Visitors',
      data: @json($daily->pluck('total')->toArray()),
      borderColor: '#1a7f64', backgroundColor: '#1a7f6420',
      borderWidth: 2, tension: 0.4, fill: true, pointRadius: 3
    }]
  },
  options: { ...opt(), maintainAspectRatio: false, responsive: true }
});

new Chart(document.getElementById('pharmaChart'), {
  type: 'bar',
  data: {
    labels: @json($pharmaByCompany->pluck('company_name')->map(fn($c)=>$c?:'Unknown')->toArray()),
    datasets: [{
      label: 'Visits',
      data: @json($pharmaByCompany->pluck('total')->toArray()),
      backgroundColor: '#2563eb80', borderColor: '#2563eb', borderWidth:1.5, borderRadius:5
    }]
  },
  options: { indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
    scales:{
      x:{grid:{color:gc},ticks:{color:tc,font:{size:10}},beginAtZero:true},
      y:{grid:{display:false},ticks:{color:tc,font:{size:10}}}
    }
  }
});

new Chart(document.getElementById('doctorChart'), {
  type: 'bar',
  data: {
    labels: @json($byDoctor->pluck('person_to_meet')->toArray()),
    datasets: [{
      label: 'Visits',
      data: @json($byDoctor->pluck('total')->toArray()),
      backgroundColor: '#7c3aed80', borderColor: '#7c3aed', borderWidth:1.5, borderRadius:5
    }]
  },
  options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
    scales:{
      x:{grid:{color:gc},ticks:{color:tc,font:{size:10}}},
      y:{grid:{color:gc},ticks:{color:tc,font:{size:10}},beginAtZero:true}
    }
  }
});
</script>
@endsection
