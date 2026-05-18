{{-- External filter panel for Radiant Cash Pickup master --}}
<div class="filter-panel" id="rcpFilterPanel">
  <div class="filter-panel-header">
    <div class="filter-panel-title">
      <i class="bi bi-sliders" style="color:var(--amber2)"></i>
      Filters
      <span class="filter-count-badge" id="rcpFilterCountBadge">0 active</span>
    </div>
    <div class="filter-panel-actions">
      <button type="button" class="fbtn fbtn-ghost fbtn-sm" id="rcpFilterResetBtn"><i class="bi bi-arrow-counterclockwise"></i> Clear All</button>
      <button type="button" class="fbtn fbtn-amber fbtn-sm" id="rcpFilterApplyBtn"><i class="bi bi-funnel-fill"></i> Apply Filters</button>
    </div>
  </div>
  <div class="filter-body">
    <div class="filter-grid">
      <div class="filter-field">
        <label><i class="bi bi-calendar3"></i> Date From</label>
        <div class="date-wrap">
          <i class="bi bi-calendar3 cal-ico"></i>
          <input type="text" name="date_from" id="fDateFrom" class="fc fc-date" placeholder="dd/mm/yyyy" autocomplete="off" readonly value="{{ request('date_from') }}">
        </div>
      </div>
      <div class="filter-field">
        <label><i class="bi bi-calendar-check"></i> Date To</label>
        <div class="date-wrap">
          <i class="bi bi-calendar3 cal-ico"></i>
          <input type="text" name="date_to" id="fDateTo" class="fc fc-date" placeholder="dd/mm/yyyy" autocomplete="off" readonly value="{{ request('date_to') }}">
        </div>
      </div>
      <div class="filter-field">
        <label><i class="bi bi-geo-alt"></i> State</label>
        <select name="state" id="rcpStateSelect" class="fc">
          <option value="">All States</option>
          @foreach($states as $s)
            <option value="{{ $s }}" {{ request('state') == $s ? 'selected' : '' }}>{{ $s }}</option>
          @endforeach
        </select>
      </div>
      <div class="filter-field">
        <label><i class="bi bi-diagram-3"></i> Zone</label>
        <select name="zone_id" id="rcpZoneSelect" class="fc">
          <option value="">All Zones</option>
          @foreach($zones as $z)
            <option value="{{ $z->id }}" {{ (string) request('zone_id') === (string) $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="filter-field">
        <label><i class="bi bi-building"></i> Branch</label>
        <select name="branch_id" id="rcpBranchSelect" class="fc">
          <option value="">All Branches</option>
          @foreach($branchesForFilter as $b)
            <option value="{{ $b->id }}" {{ (string) request('branch_id') === (string) $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>
      @if($bankRadiantLinking ?? false)
      <div class="filter-field" id="rcpBankRadiantTagWrap">
        <label><i class="bi bi-tag"></i> Bank recon · Radiant tag</label>
        <select class="fc" id="rcpBankRadiantTag" style="cursor:pointer;">
          <option value="">All pickups</option>
          <option value="tagged" {{ request('bank_radiant_tag') === 'tagged' ? 'selected' : '' }}>Tagged only</option>
          <option value="untagged" {{ request('bank_radiant_tag') === 'untagged' ? 'selected' : '' }}>Not tagged only</option>
        </select>
      </div>
      <div class="filter-field" id="rcpRadiantTaggedByWrap">
        <label><i class="bi bi-person-check"></i> Bank tagged by</label>
        <select class="fc" id="rcpRadiantTaggedBy" style="cursor:pointer;">
          <option value="">Anyone</option>
        </select>
      </div>
      @endif
      <div class="filter-field" style="grid-column:span 2;">
        <label><i class="bi bi-search"></i> Search</label>
        <input type="text" name="search" id="filterSearchInput" class="fc" placeholder="Slip no, location, state, amount…" value="{{ request('search') }}">
      </div>
    </div>
  </div>
  <div class="active-chips" id="activeChips">
    <span class="no-chip" id="noChip">No filters applied</span>
  </div>
</div>
<input type="hidden" name="per_page" id="filterFormPerPage" value="{{ request('per_page', 25) }}">
