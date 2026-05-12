@extends('vms.layout')
@section('page_title','Pharma Vendors')
@section('page_subtitle','Visit Tracking')

@section('extra_css')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>.chart-wrap{height:200px}</style>
@endsection

@section('content')

<div class="row g-4 mb-4">
  <div class="col-lg-8">
    @include('vms.partials.filter-bar', [
      'showType'   => false,
      'showSearch' => true,
      'showDate'   => true,
      'showStatus' => false,
      'resetRoute' => 'vms.pharma',
      'zones'      => $zones,
      'locations'  => $locations,
    ])

    <div class="vms-card">
      <div class="vms-card-header">
        <div class="vms-card-title"><i class="ti ti-pill me-2" style="color:var(--accent)"></i>Pharma Visitor Records</div>
        <span class="vms-card-pill">{{ $visitors->total() }} visits</span>
      </div>
      <div class="table-responsive">
        <table class="vms-table">
          <thead>
            <tr>
              <th>Visitor</th>
              <th>Company</th>
              <th>Meeting</th>
              <th>Purpose</th>
              <th>Visit Date</th>
              <th>Duration</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($visitors as $v)
            <tr>
              <td>
                <div style="font-weight:600;font-size:12px">{{ $v->visitor_name }}</div>
                <div style="color:var(--muted);font-size:11px">{{ $v->visitor_phone }}</div>
              </td>
              <td style="font-size:12px;font-weight:500">{{ $v->company_name ?: '—' }}</td>
              <td style="font-size:12px">{{ $v->person_to_meet ?: '—' }}</td>
              <td style="font-size:12px">{{ $v->purpose }}</td>
              <td style="font-size:11px;white-space:nowrap">{{ $v->created_at->format('d M Y') }}</td>
              <td style="font-size:11px;color:var(--muted)">{{ $v->duration }}</td>
              <td><span class="badge-{{ $v->status }}">{{ ucfirst(str_replace('_',' ',$v->status)) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">No pharma visits found</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $visitors->appends(request()->query())->links() }}</div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="vms-card">
      <div class="vms-card-header">
        <div class="vms-card-title">Top Companies</div>
        <span class="vms-card-pill">All time</span>
      </div>
      <div class="chart-wrap">
        <canvas id="pharmaChart"></canvas>
      </div>
      <div style="margin-top:16px">
        @foreach($topCompanies as $co)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border);font-size:12px">
          <div style="font-weight:500;color:var(--text)">{{ $co->company_name ?: 'Unknown' }}</div>
          <span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600">{{ $co->total }}</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

@endsection

@section('extra_js')
<script>
new Chart(document.getElementById('pharmaChart'), {
  type: 'bar',
  data: {
    labels: @json($topCompanies->pluck('company_name')->map(fn($c) => $c ?: 'Unknown')->toArray()),
    datasets: [{
      label: 'Visits',
      data: @json($topCompanies->pluck('total')->toArray()),
      backgroundColor: '#1a7f6480',
      borderColor: '#1a7f64',
      borderWidth: 1.5,
      borderRadius: 5,
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { beginAtZero: true, ticks: { font: { size: 10 }, color: 'rgba(0,0,0,0.45)' }, grid: { color: 'rgba(0,0,0,0.06)' } },
      y: { ticks: { font: { size: 10 }, color: 'rgba(0,0,0,0.55)' }, grid: { display: false } }
    }
  }
});
</script>
@endsection
