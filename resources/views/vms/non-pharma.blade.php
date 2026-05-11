@extends('vms.layout')
@section('page_title','Non-Pharma Vendors')
@section('page_subtitle','Service & Supplier Visits')

@section('content')

<form method="GET" class="vms-card mb-4">
  <div class="d-flex gap-3 flex-wrap align-items-end">
    <div>
      <label class="form-label" style="font-size:11px;font-weight:600;color:var(--muted)">Search</label>
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Company name…" value="{{ request('search') }}" style="font-size:12px;width:200px">
    </div>
    <div>
      <label class="form-label" style="font-size:11px;font-weight:600;color:var(--muted)">Date</label>
      <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" style="font-size:12px">
    </div>
    <button type="submit" class="vbtn vbtn-primary"><i class="ti ti-filter"></i> Filter</button>
    <a href="{{ route('vms.non-pharma') }}" class="vbtn vbtn-hold">Reset</a>
  </div>
</form>

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
