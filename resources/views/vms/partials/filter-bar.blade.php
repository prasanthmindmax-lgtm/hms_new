{{--
  VMS Filter Bar — shared across approvals, active, history, pharma, non-pharma
  Props:
    $zones      — collection of zones with ->locations
    $locations  — flat collection of locations
    $showType   — bool, show visitor type select (default true)
    $showSearch — bool, show text search (default false)
    $showDate   — bool, show date picker (default false)
    $showStatus — bool, show status select (default false)
    $resetRoute — route name string for Reset button
--}}
@php
  $showType   = $showType   ?? true;
  $showSearch = $showSearch ?? false;
  $showDate   = $showDate   ?? false;
  $showStatus = $showStatus ?? false;
  $resetRoute = $resetRoute ?? request()->route()->getName();
  $selLocs    = (array)(request()->input('location_ids', []));
@endphp

<div class="vms-filter-bar">
  <form method="GET" id="vmsFilterForm" class="d-flex flex-wrap gap-3 align-items-end">

    @if($showSearch)
    <div style="flex:1;min-width:180px">
      <label class="filter-label">Search</label>
      <input type="text" name="search" value="{{ request('search') }}"
             class="form-control form-control-sm" placeholder="Name, phone, company…"
             style="font-size:12px;border-radius:8px">
    </div>
    @endif

    @if($showType)
    <div style="min-width:150px">
      <label class="filter-label">Visitor Type</label>
      <select name="type" class="form-select form-select-sm" style="font-size:12px;border-radius:8px">
        <option value="">All Types</option>
        <option value="pharma"           {{ request('type')=='pharma'?'selected':'' }}>Pharma</option>
        <option value="non_pharma"       {{ request('type')=='non_pharma'?'selected':'' }}>Non-Pharma</option>
        <option value="patient_relative" {{ request('type')=='patient_relative'?'selected':'' }}>Patient Relative</option>
        <option value="job_applicant"    {{ request('type')=='job_applicant'?'selected':'' }}>Job Applicant</option>
      </select>
    </div>
    @endif

    @if($showStatus)
    <div style="min-width:130px">
      <label class="filter-label">Status</label>
      <select name="status" class="form-select form-select-sm" style="font-size:12px;border-radius:8px">
        <option value="">All Status</option>
        <option value="inside"      {{ request('status')=='inside'?'selected':'' }}>Active</option>
        <option value="checked_out" {{ request('status')=='checked_out'?'selected':'' }}>Checked Out</option>
        <option value="rejected"    {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
        <option value="pending"     {{ request('status')=='pending'?'selected':'' }}>Pending</option>
      </select>
    </div>
    @endif

    @if($showDate)
    <div style="min-width:150px">
      <label class="filter-label">Date</label>
      <input type="date" name="date" value="{{ request('date') }}"
             class="form-control form-control-sm" style="font-size:12px;border-radius:8px">
    </div>
    @endif

    {{-- Zone multi-select --}}
    @if(isset($zones) && $zones->count() > 0)
    <div style="min-width:200px;flex:1;max-width:280px">
      <label class="filter-label">Zone (multi-select)</label>
      <select name="zone_ids[]" id="vmsZoneFilter" class="vms-multi-select"
              multiple data-placeholder="All Zones" style="width:100%">
        @foreach($zones as $zone)
          <option value="{{ $zone->id }}"
            {{ in_array($zone->id, (array)request()->input('zone_ids', [])) ? 'selected' : '' }}>
            {{ $zone->name }}
          </option>
        @endforeach
      </select>
    </div>
    @endif

    {{-- Branch/Location multi-select --}}
    @if(isset($zones) && $zones->count() > 0)
    <div style="min-width:220px;flex:1;max-width:300px">
      <label class="filter-label">Branch / Location (multi-select)</label>
      <select name="location_ids[]" id="vmsLocationFilter" class="vms-multi-select"
              multiple data-placeholder="All Branches" style="width:100%">
        @foreach($zones as $zone)
          @if($zone->locations->count() > 0)
          <optgroup label="{{ $zone->name }}">
            @foreach($zone->locations as $loc)
              <option value="{{ $loc->id }}"
                {{ in_array($loc->id, $selLocs) ? 'selected' : '' }}>
                {{ $loc->name }}
              </option>
            @endforeach
          </optgroup>
          @endif
        @endforeach
        {{-- locations without a zone --}}
        @php $unzoned = $locations->filter(fn($l) => !$l->zone_id); @endphp
        @if($unzoned->count() > 0)
        <optgroup label="Other">
          @foreach($unzoned as $loc)
            <option value="{{ $loc->id }}" {{ in_array($loc->id, $selLocs) ? 'selected' : '' }}>
              {{ $loc->name }}
            </option>
          @endforeach
        </optgroup>
        @endif
      </select>
    </div>
    @elseif(isset($locations) && $locations->count() > 0)
    <div style="min-width:200px">
      <label class="filter-label">Branch / Location</label>
      <select name="location_id" class="form-select form-select-sm" style="font-size:12px;border-radius:8px">
        <option value="">All Locations</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}" {{ request('location_id')==$loc->id?'selected':'' }}>{{ $loc->name }}</option>
        @endforeach
      </select>
    </div>
    @endif

    <div class="d-flex gap-2 align-items-end" style="padding-bottom:0">
      <button type="submit" class="vbtn vbtn-primary">
        <i class="ti ti-filter"></i> Filter
      </button>
      <a href="{{ route($resetRoute) }}" class="vbtn vbtn-hold">
        <i class="ti ti-x"></i> Reset
      </a>
    </div>
  </form>
</div>

@push('filter_js')
<script>
// Zone ↔ Location cascading filter
$('#vmsZoneFilter').on('change', function() {
  const selectedZones = $(this).val() || [];
  const $locSel = $('#vmsLocationFilter');
  if (!$locSel.length) return;

  $locSel.find('optgroup').each(function() {
    const groupName = $(this).attr('label');
    // Find zone id by label matching
    const zoneIds = @json($zones->pluck('id','name'));
    const matchedZoneId = Object.entries(zoneIds).find(([name]) => name === groupName)?.[1];
    if (matchedZoneId && selectedZones.length > 0) {
      $(this).find('option').prop('disabled', !selectedZones.includes(String(matchedZoneId)));
    } else {
      $(this).find('option').prop('disabled', false);
    }
  });
  $locSel.trigger('change.select2');
});
</script>
@endpush
