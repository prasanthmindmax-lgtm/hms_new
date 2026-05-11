@extends('vms.layout')
@section('page_title','Visitor History')
@section('page_subtitle','All Records')

@section('content')

{{-- Filters --}}
<form method="GET" class="vms-card mb-4">
  <div class="row g-3 align-items-end">
    <div class="col-md-3 col-sm-6">
      <label class="form-label" style="font-size:11px;font-weight:600;color:var(--muted)">Search</label>
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, phone, company…" value="{{ request('search') }}" style="font-size:12px">
    </div>
    <div class="col-md-2 col-sm-6">
      <label class="form-label" style="font-size:11px;font-weight:600;color:var(--muted)">Visitor Type</label>
      <select name="type" class="form-select form-select-sm" style="font-size:12px">
        <option value="">All Types</option>
        <option value="pharma" {{ request('type')=='pharma'?'selected':'' }}>Pharma</option>
        <option value="non_pharma" {{ request('type')=='non_pharma'?'selected':'' }}>Non-Pharma</option>
        <option value="patient_relative" {{ request('type')=='patient_relative'?'selected':'' }}>Patient Relative</option>
        <option value="job_applicant" {{ request('type')=='job_applicant'?'selected':'' }}>Job Applicant</option>
        <option value="government" {{ request('type')=='government'?'selected':'' }}>Government</option>
      </select>
    </div>
    <div class="col-md-2 col-sm-6">
      <label class="form-label" style="font-size:11px;font-weight:600;color:var(--muted)">Status</label>
      <select name="status" class="form-select form-select-sm" style="font-size:12px">
        <option value="">All Status</option>
        <option value="inside" {{ request('status')=='inside'?'selected':'' }}>Inside</option>
        <option value="checked_out" {{ request('status')=='checked_out'?'selected':'' }}>Checked Out</option>
        <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
        <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
      </select>
    </div>
    <div class="col-md-2 col-sm-6">
      <label class="form-label" style="font-size:11px;font-weight:600;color:var(--muted)">Date</label>
      <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" style="font-size:12px">
    </div>
    <div class="col-md-3 col-sm-12 d-flex gap-2">
      <button type="submit" class="vbtn vbtn-primary flex-grow-1"><i class="ti ti-filter"></i> Filter</button>
      <a href="{{ route('vms.history') }}" class="vbtn vbtn-hold">Reset</a>
    </div>
  </div>
</form>

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
