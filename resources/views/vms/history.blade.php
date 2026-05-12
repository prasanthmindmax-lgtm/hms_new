@extends('vms.layout')
@section('page_title','Visitor History')
@section('page_subtitle','All Records')

@section('content')

@include('vms.partials.filter-bar', [
  'showType'   => true,
  'showSearch' => true,
  'showDate'   => true,
  'showStatus' => true,
  'resetRoute' => 'vms.history',
  'zones'      => $zones,
  'locations'  => $locations,
])

<div class="vms-card">
  <div class="vms-card-header">
    <div class="vms-card-title">Visitor Records</div>
    <span class="vms-card-pill">{{ $visitors->total() }} records</span>
  </div>
  <div class="table-responsive">
    <table class="vms-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Visitor</th>
          <th>Type</th>
          <th>Company</th>
          <th>Purpose</th>
          <th>Meeting</th>
          <th>Entry</th>
          <th>Exit</th>
          <th>Duration</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($visitors as $i => $v)
        <tr>
          <td style="color:var(--muted)">{{ $visitors->firstItem() + $i }}</td>
          <td>
            <div style="font-weight:600;font-size:12px">{{ $v->visitor_name }}</div>
            <div style="color:var(--muted);font-size:11px">{{ $v->visitor_phone }}</div>
          </td>
          <td>
            @if($v->visitor_type === 'pharma')
              <span class="badge-pharma">Pharma</span>
            @elseif($v->visitor_type === 'non_pharma')
              <span class="badge-non">Non-Pharma</span>
            @else
              <span style="font-size:11px;color:var(--muted)">{{ $v->visitor_type_label }}</span>
            @endif
          </td>
          <td style="font-size:12px">{{ $v->company_name ?: '—' }}</td>
          <td style="font-size:12px">{{ $v->purpose }}</td>
          <td style="font-size:12px">{{ $v->person_to_meet ?: '—' }}</td>
          <td style="font-size:11px;white-space:nowrap">{{ $v->entry_time ? $v->entry_time->format('d M, h:i A') : '—' }}</td>
          <td style="font-size:11px;white-space:nowrap">{{ $v->exit_time ? $v->exit_time->format('d M, h:i A') : '—' }}</td>
          <td style="font-size:11px;color:var(--muted)">{{ $v->duration }}</td>
          <td>
            <span class="badge-{{ $v->status }}">{{ ucfirst(str_replace('_',' ',$v->status)) }}</span>
          </td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center py-4" style="color:var(--muted);font-size:13px">No records found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $visitors->appends(request()->query())->links() }}</div>
</div>

@endsection
