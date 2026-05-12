@extends('vms.layout')
@section('page_title','Non-Pharma Vendors')
@section('page_subtitle','Service & Supplier Visits')

@section('content')

@include('vms.partials.filter-bar', [
  'showType'   => false,
  'showSearch' => true,
  'showDate'   => true,
  'showStatus' => false,
  'resetRoute' => 'vms.non-pharma',
  'zones'      => $zones,
  'locations'  => $locations,
])

<div class="vms-card">
  <div class="vms-card-header">
    <div class="vms-card-title"><i class="ti ti-briefcase me-2" style="color:var(--blue)"></i>Non-Pharma Visitor Records</div>
    <span class="vms-card-pill">{{ $visitors->total() }} visits</span>
  </div>
  <div class="table-responsive">
    <table class="vms-table">
      <thead>
        <tr>
          <th>Visitor</th>
          <th>Company</th>
          <th>Contact</th>
          <th>Purpose</th>
          <th>Items Carried</th>
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
          <td style="font-size:11px;color:var(--muted)">{{ $v->equipment_carried ?: '—' }}</td>
          <td style="font-size:11px;white-space:nowrap">{{ $v->created_at->format('d M Y') }}</td>
          <td style="font-size:11px;color:var(--muted)">{{ $v->duration }}</td>
          <td><span class="badge-{{ $v->status }}">{{ ucfirst(str_replace('_',' ',$v->status)) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-4" style="color:var(--muted)">No non-pharma visits found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $visitors->appends(request()->query())->links() }}</div>
</div>

@endsection
