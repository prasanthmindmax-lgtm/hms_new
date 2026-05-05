<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('assets/css/user_activity.css') }}?v=47" />
@php
$uapUserDetailUrl = function (int $id) {
$q = ['user' => $id];
if (request()->filled('from')) {
$q['from'] = request('from');
}
if (request()->filled('to')) {
$q['to'] = request('to');
}
return route('user_activity.user', $q);
};
$linkActivity = route('user_activity.dashboard', array_merge(request()->except('ui'), ['ui' => 'activity']));

/* Template URL for /user/{id}/events/all — replaced in JS (unlikely real user id) */
$uapTypeEventsAllUrlTemplate = str_replace(
  '1000000001',
  '__UID__',
  route('user_activity.user.type_events', ['user' => 1000000001, 'typeKey' => 'all'])
);

$uapNameWithEmp = function ($user, ?int $fallbackId = null): string {
if (! $user) {
return $fallbackId ? 'User #'.$fallbackId : '—';
}
$full = trim((string) (data_get($user, 'user_fullname') ?: data_get($user, 'name') ?: ''));
$login = trim((string) (data_get($user, 'username') ?? ''));
if ($login === '') {
$eid = data_get($user, 'employee_id');
$login = is_scalar($eid) && (string) $eid !== '' ? trim((string) $eid) : '';
}
if ($full !== '' && $login !== '' && $full === $login) {
return $full;
}
if ($full !== '' && $login !== '') {
return $full.' - '.$login;
}
if ($full !== '') {
return $full;
}
if ($login !== '') {
return $login;
}

return $fallbackId ? 'User #'.$fallbackId : '—';
};
@endphp
<body style="overflow-x: hidden;">
<div class="page-loader"><div class="bar"></div></div>
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')
<div class="pc-container">
  <div class="pc-content">
<div class="container-fluid uap-page uap-page--tight-x py-3 py-lg-4 px-0">
  <header class="uap-hero d-flex flex-wrap align-items-start justify-content-between gap-3 mb-0">
    <div class="flex-grow-1" style="min-width: min(100%, 28rem);">
      <h1 class="uap-hero-title"><i class="bi bi-activity me-1"></i> User activity</h1>
    </div>
    <div class="uap-hero-aside d-flex flex-wrap gap-2 align-items-center">
      <a class="uap-btn-ghost" href="{{ $linkActivity }}"><i class="bi bi-layout-text-sidebar-reverse"></i> Activity log layout</a>
    </div>
  </header>

  <div class="uap-kpi-grid" role="list">
    <a
      href="javascript:void(0)"
      class="uap-kpi uap-kpi--1 uap-dash-auth-kpi uap-kpi--clickable text-decoration-none text-body"
      role="listitem"
      tabindex="0"
      data-type-key="login"
      data-type-label="Logins in range">
      <div class="uap-kpi-icon" aria-hidden="true"><i class="bi bi-box-arrow-in-right"></i></div>
      <div class="uap-kpi-body">
        <div class="uap-kpi-label">Logins</div>
        <div class="uap-kpi-val">{{ number_format($logins) }}</div>
      </div>
    </a>
    <a
      href="javascript:void(0)"
      class="uap-kpi uap-kpi--2 uap-dash-auth-kpi uap-kpi--clickable text-decoration-none text-body"
      role="listitem"
      tabindex="0"
      data-type-key="logout"
      data-type-label="Logouts in range">
      <div class="uap-kpi-icon" aria-hidden="true"><i class="bi bi-box-arrow-left"></i></div>
      <div class="uap-kpi-body">
        <div class="uap-kpi-label">Logouts</div>
        <div class="uap-kpi-val">{{ number_format($logouts) }}</div>
      </div>
    </a>
    <a
      href="javascript:void(0)"
      class="uap-kpi uap-kpi--4 uap-dash-open-sessions-kpi uap-kpi--clickable text-decoration-none text-body"
      role="listitem"
      tabindex="0"
      data-type-label="Open work sessions in range"
      title="Open sessions: started in range, no sign-out yet">
      <div class="uap-kpi-icon" aria-hidden="true"><i class="bi bi-link-45deg"></i></div>
      <div class="uap-kpi-body">
        <div class="uap-kpi-label">Open sessions</div>
        <div class="uap-kpi-val">{{ number_format($openCount) }}</div>
      </div>
    </a>
    <a
      href="javascript:void(0)"
      class="uap-kpi uap-kpi--5 uap-dash-activity-kpi uap-kpi--clickable text-decoration-none text-body"
      role="listitem"
      tabindex="0"
      data-activity="read"
      data-type-label="Read (GET) in range">
      <div class="uap-kpi-icon" aria-hidden="true"><i class="bi bi-eye"></i></div>
      <div class="uap-kpi-body">
        <div class="uap-kpi-label">Read (GET)</div>
        <div class="uap-kpi-val">{{ number_format($totalReads) }}</div>
      </div>
    </a>
    <a
      href="javascript:void(0)"
      class="uap-kpi uap-kpi--6 uap-dash-activity-kpi uap-kpi--clickable text-decoration-none text-body"
      role="listitem"
      tabindex="0"
      data-activity="write"
      data-type-label="Writes in range (create, update, delete)">
      <div class="uap-kpi-icon" aria-hidden="true"><i class="bi bi-pencil-square"></i></div>
      <div class="uap-kpi-body">
        <div class="uap-kpi-label">Writes (C/U/D)</div>
        <div class="uap-kpi-val">{{ number_format($totalWrites) }}</div>
      </div>
    </a>
  </div>

  <form method="get" action="{{ route('user_activity.dashboard') }}" class="uap-filters">
    <input type="hidden" name="per_page" value="{{ (int) request('per_page', 25) }}">
    @if(request()->filled('ui'))
    <input type="hidden" name="ui" value="{{ e(request('ui')) }}">
    @endif
    <div class="uap-filters-inner">
      <div class="uap-filters-icon"><i class="bi bi-funnel" aria-hidden="true"></i></div>
      <div class="row g-3 align-items-end flex-grow-1 w-100 min-w-0">
        <div class="col-auto">
          <label class="form-label" for="uap-from">From</label>
          <input type="date" id="uap-from" name="from" value="{{ $from }}" class="form-control form-control-sm" />
        </div>
        <div class="col-auto">
          <label class="form-label" for="uap-to">To</label>
          <input type="date" id="uap-to" name="to" value="{{ $to }}" class="form-control form-control-sm" />
        </div>
        <div class="col-12 col-md uap-filters-user-col min-w-0" style="min-width: 10rem; max-width: 28rem;">
          <label class="form-label" for="uap-user">User <span class="uap-optional"></span></label>
          <div class="tax-dropdown-wrapper uap-custom-dd w-100">
            @php
              $selectedUserName = 'All users';
              if ($userId) {
                $selUser = collect($usersForFilter)->firstWhere('id', $userId);
                if ($selUser) {
                  $selectedUserName = $uapNameWithEmp($selUser);
                }
              }
            @endphp
            <input type="text" class="form-control form-control-sm dropdown-search-input uap-dd-input" placeholder="Select a user" value="{{ $selectedUserName }}" readonly autocomplete="off">
            <input type="hidden" name="user_id" id="uap-user-hidden" value="{{ $userId }}">
            <div class="dropdown-menu tax-dropdown uap-tax-dd w-100">
              <div class="inner-search-container">
                <input type="text" class="inner-search form-control form-control-sm" placeholder="Search..." autocomplete="off">
              </div>
              <div class="dropdown-list">
                <div data-value="" data-id="" @class(['selected' => !$userId])>All users</div>
                @foreach($usersForFilter as $u)
                  <div data-value="{{ $uapNameWithEmp($u) }}" data-id="{{ $u->id }}" @class(['selected' => (string)$userId===(string)$u->id])>
                    {{ $uapNameWithEmp($u) }}
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-auto d-flex flex-column align-items-stretch uap-filters-apply ms-md-auto">
          <label class="form-label uap-filters-apply-lbl" aria-hidden="true">Action</label>
          <div class="d-flex gap-2">
            <button type="submit" class="uap-btn-apply"><i class="bi bi-check2-circle me-1"></i> Apply filters</button>
            <a href="{{ route('user_activity.dashboard', request()->filled('ui') ? ['ui' => request('ui')] : []) }}" class="btn btn-light d-inline-flex align-items-center justify-content-center" style="min-height: 2.125rem; border: 1.5px solid #e2e8f0; border-radius: 10px; color: #64748b; font-weight: 500; padding: 0 1rem;"><i class="bi bi-x-circle me-1"></i> Clear</a>
          </div>
        </div>
      </div>
    </div>
  </form>



  <div class="uap-card uap-card--table">
    <div class="uap-card-header uap-card-header--all-users">
      <a
        href="#"
        class="d-flex flex-wrap align-items-center gap-2 gap-md-3 uap-table-card-head__left uap-all-users-header-kpi text-decoration-none text-body"
        role="button"
        title="Open full list for this date range and filters"
        aria-label="Open all users in range (modal)">
        <div class="d-flex align-items-center gap-2">
          <span class="uap-card-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
          <strong class="uap-card-title mb-0">All users</strong>
        </div>
        <div class="uap-meta-chips mb-0">
          <span class="uap-meta-chip"><i class="bi bi-people" aria-hidden="true"></i> {{ number_format($users->total()) }} user(s)</span>
        </div>
      </a>
      <form method="get" action="{{ route('user_activity.dashboard') }}" class="uap-per-page-form uap-per-page-form--in-header d-flex">
        <input type="hidden" name="from" value="{{ e($from) }}">
        <input type="hidden" name="to" value="{{ e($to) }}">
        @if($userId)
        <input type="hidden" name="user_id" value="{{ (int) $userId }}">
        @endif
        @if(request()->filled('ui'))
        <input type="hidden" name="ui" value="{{ e(request('ui')) }}">
        @endif
        <div class="uap-per-page-field uap-per-page-field--inline">
          <label class="uap-per-page-label" for="uap-per-page">Per page</label>
          <select id="uap-per-page" name="per_page" class="form-select form-select-sm uap-per-page-select" onchange="this.form.submit()">
            @foreach([10, 25, 50, 100] as $n)
            <option value="{{ $n }}" @selected((int) request('per_page', 25)===$n)>{{ $n }}</option>
            @endforeach
          </select>
        </div>
      </form>
    </div>
    <div class="uap-logs-table-wrap">
      <table class="uap-logs-tbl uap-logs-tbl--list mb-0">
        <thead>
          <tr>
            <th scope="col" class="uap-logs-th-n" style="width:44px;">#</th>
            <th scope="col" style="min-width:220px;">User</th>
            <th scope="col" style="width:110px;">Events in range</th>
            <th scope="col" style="min-width:150px;">Last activity</th>
            <th scope="col" style="width:120px;" class="text-end" aria-label="Open detail"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
          @php
          $st = $userStatsInRange->get($u->id);
          $evN = $st->event_count ?? 0;
          $lastAt = $st->last_at ?? null;
          @endphp
          <tr
            class="uap-logs-group-head uap-user-list-row @if($loop->odd) uap-logs-group-head--alt @endif"
            data-href="{{ $uapUserDetailUrl((int) $u->id) }}"
            data-user-id="{{ (int) $u->id }}"
            data-user-name="{{ e($uapNameWithEmp($u, (int) $u->id)) }}"
            role="row"
            tabindex="0"
            title="Open all log events for this user in the selected range">
            <td
              class="uap-logs-td-idx uap-user-all-events-idx text-muted fw-semibold"
              role="button"
              tabindex="0"
              data-user-id="{{ (int) $u->id }}"
              data-user-name="{{ e($uapNameWithEmp($u, (int) $u->id)) }}"
              title="All log entries in this date range (newest first)">{{ $users->firstItem() + $loop->index }}</td>
            <td>
              <div class="d-flex align-items-center gap-2 uap-logs-user-cell">
                <div class="uap-logs-user-text min-w-0">
                  <div class="uap-user-line fw-bold mb-0">{{ $uapNameWithEmp($u) }}</div>
                </div>
              </div>
            </td>
            <td>
              <span class="uap-logs-cnt-pill">{{ number_format($evN) }}</span>
            </td>
            <td class="uap-when nw text-nowrap">
              @if($lastAt)
              <time datetime="{{ \Carbon\Carbon::parse($lastAt)->toIso8601String() }}">{{ \Carbon\Carbon::parse($lastAt)->format('Y-m-d H:i:s') }}</time>
              @else
              <span class="text-muted">—</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ $uapUserDetailUrl((int) $u->id) }}"
                 class="uap-logs-open-link"
                 title="Open full user activity for this user (selected date range)">
                View <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="uap-empty text-center">
              <p class="uap-empty-title mb-1">No users found</p>
              <p class="uap-empty-hint mb-0">If you used the user filter, try clearing it to see the full list.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($users->total() > 0)
    <div class="uap-card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-2 border-top">
      <div class="text-muted small">
        @if($users->firstItem())
        Showing {{ number_format($users->firstItem()) }}–{{ number_format($users->lastItem()) }} of
        @endif
        {{ number_format($users->total()) }} user(s)
      </div>
      @if($users->hasPages())
      <div class="uap-paginate">{{ $users->links('pagination::bootstrap-4') }}</div>
      @endif
    </div>
    @endif
  </div>

  <div class="row g-3 mb-4 align-items-stretch">
    <div class="col-lg-6">
      <div class="uap-card uap-card--tight h-100 mb-0">
        <div class="uap-card-header">
          <div class="d-flex align-items-center gap-2">
            <span class="uap-card-icon uap-card-icon--soft"><i class="bi bi-grid-1x2" aria-hidden="true"></i></span>
            <div>
              <strong class="uap-card-title">By module</strong>
              <span class="d-block uap-sub">Resolved from path/route; create &amp; update = record-typed events</span>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table uap-side-table mb-0 table-hover uap-by-module-tbl">
            <thead>
              <tr>
                <th>Module</th>
                <th class="text-end">Events</th>
                <th class="text-end" title="POST (etc.)">Creates</th>
                <th class="text-end" title="PUT/PATCH">Edits</th>
                <th class="text-end" title="Sum of records_count">Units</th>
              </tr>
            </thead>
            <tbody>
              @forelse($byModule as $ar)
              <tr class="uap-by-module-row" role="button" tabindex="0" data-module="{{ e($ar->activity_module) }}">
                <td><span class="uap-mod" style="max-width: 100%;">{{ $ar->activity_module }}</span></td>
                <td class="text-end fw-semibold">{{ number_format($ar->n) }}</td>
                <td class="text-end">{{ number_format($ar->n_create ?? 0) }}</td>
                <td class="text-end">{{ number_format($ar->n_update ?? 0) }}</td>
                <td class="text-end">{{ number_format($ar->records) }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-muted p-4 text-center">No activity in this range for the selected filters.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="uap-card uap-card--tight h-100 mb-0">
        <div class="uap-card-header">
          <div class="d-flex align-items-center gap-2">
            <span class="uap-card-icon uap-card-icon--soft"><i class="bi bi-trophy" aria-hidden="true"></i></span>
            <div>
              <strong class="uap-card-title">Top users</strong>
              <span class="d-block uap-sub">By event count in range</span>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table uap-side-table mb-0">
            <thead>
              <tr>
                <th>User</th>
                <th class="text-end">Events</th>
                <th class="text-end">Rec. units</th>
              </tr>
            </thead>
            <tbody>
              @forelse($perUserWithLabels as $row)
              <tr>
                <td>
                  <a href="{{ $uapUserDetailUrl((int) $row->user_id) }}" class="uap-logs-open-link d-inline" style="font-size:0.9rem; color:#0f172a;">
                    {{ $uapNameWithEmp((object) ['user_fullname' => $row->user_name, 'username' => $row->user_label]) }}
                  </a>
                </td>
                <td class="text-end fw-bold">{{ number_format($row->events) }}</td>
                <td class="text-end">{{ number_format($row->record_units) }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="text-muted p-3 text-center">No data in this range.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
  </div>
</div>
<div class="modal fade uap-activity-detail-modal" id="uapDashAuthEventsModal" tabindex="-1" aria-labelledby="uapDashAuthEventsTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable uap-activity-fr-dialog">
    <div class="modal-content uap-activity-fr-modal uap-activity-detail-modal__content uap-adm">
      <div class="modal-header uap-activity-fr-modal__header uap-activity-detail-modal__header uap-adm__header">
        <h5 class="modal-title mb-0" id="uapDashAuthEventsTitle">
          <i class="bi bi-box-arrow-in-right uap-activity-fr-modal__title-ico" aria-hidden="true" id="uapDashAuthEventsTitleIco"></i>
          <span id="uapDashAuthEventsHeading">Logins</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body uap-activity-fr-modal__body uap-activity-detail-modal__body uap-adm__body">
        <div class="uap-adm-filter" aria-live="polite">
          <div class="uap-adm-filter__ico" aria-hidden="true"><i class="bi bi-calendar3"></i></div>
          <div class="uap-adm-filter__text">
            <span class="uap-adm-filter__label">Reporting period</span>
            <span class="uap-adm-filter__value" id="uapDashAuthEventsRange"></span>
          </div>
        </div>
        <p class="uap-adm-trunc alert alert-warning d-none small mt-2 mb-0" id="uapDashAuthEventsTruncated" role="status">Showing the first 500. Narrow the date range to see the rest.</p>
        <div class="d-none uap-adm-loading text-center py-5" id="uapDashAuthEventsLoading" role="status">
          <div class="uap-adm-loading__spinner" role="status" aria-label="Loading"></div>
          <p class="mt-3 uap-adm-loading__txt mb-0">Loading…</p>
        </div>
        <p class="d-none uap-adm-err text-danger small mt-2 mb-0" id="uapDashAuthEventsError" role="alert"></p>
        <div class="d-none" id="uapDashAuthEventsTableSection">
          <div class="uap-adm-panel uap-dash-auth-panel">
            <div class="uap-adm-panel__head">
              <i class="bi bi-people" aria-hidden="true"></i>
              <span id="uapDashAuthEventsPanelSub">Name, date, and time for each event</span>
            </div>
            <div class="table-responsive uap-adm-table-wrap">
              <table class="table uap-activity-fr-table uap-dash-auth-table uap-adm-data-table table-sm align-middle mb-0">
                <thead class="uap-adm-thead">
                  <tr>
                    <th scope="col">User</th>
                    <th scope="col" class="text-nowrap">Date</th>
                    <th scope="col" class="text-nowrap">Time</th>
                  </tr>
                </thead>
                <tbody id="uapDashAuthEventsTbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade uap-activity-detail-modal" id="uapDashOpenSessionsModal" tabindex="-1" aria-labelledby="uapDashOpenSessionsTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable uap-activity-fr-dialog">
    <div class="modal-content uap-activity-fr-modal uap-activity-detail-modal__content uap-adm">
      <div class="modal-header uap-activity-fr-modal__header uap-activity-detail-modal__header uap-adm__header">
        <h5 class="modal-title mb-0" id="uapDashOpenSessionsTitle">
          <i class="bi bi-link-45deg uap-activity-fr-modal__title-ico" aria-hidden="true"></i>
          <span id="uapDashOpenSessionsHeading">Open sessions</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body uap-activity-fr-modal__body uap-activity-detail-modal__body uap-adm__body">
        <div class="uap-adm-filter" aria-live="polite">
          <div class="uap-adm-filter__ico" aria-hidden="true"><i class="bi bi-calendar3"></i></div>
          <div class="uap-adm-filter__text">
            <span class="uap-adm-filter__label">Reporting period (session start)</span>
            <span class="uap-adm-filter__value" id="uapDashOpenSessionsRange"></span>
          </div>
        </div>
        <p class="uap-adm-trunc alert alert-warning d-none small mt-2 mb-0" id="uapDashOpenSessionsTruncated" role="status">Showing the first 500. Narrow the date range to see the rest.</p>
        <div class="d-none uap-adm-loading text-center py-5" id="uapDashOpenSessionsLoading" role="status">
          <div class="uap-adm-loading__spinner" role="status" aria-label="Loading"></div>
          <p class="mt-3 uap-adm-loading__txt mb-0">Loading…</p>
        </div>
        <p class="d-none uap-adm-err text-danger small mt-2 mb-0" id="uapDashOpenSessionsError" role="alert"></p>
        <div class="d-none" id="uapDashOpenSessionsTableSection">
          <div class="uap-adm-panel">
            <div class="uap-adm-panel__head">
              <i class="bi bi-clock-history" aria-hidden="true"></i>
              <span>Sign-in and last seen</span>
            </div>
            <div class="table-responsive uap-adm-table-wrap">
              <table class="table uap-activity-fr-table uap-adm-data-table table-sm align-middle mb-0">
                <thead class="uap-adm-thead">
                  <tr>
                    <th scope="col">User</th>
                    <th scope="col" class="text-nowrap">Date (sign in)</th>
                    <th scope="col" class="text-nowrap">Time (sign in)</th>
                    <th scope="col" class="text-nowrap">Last seen</th>
                  </tr>
                </thead>
                <tbody id="uapDashOpenSessionsTbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade uap-activity-detail-modal" id="uapDashKpiActivityModal" tabindex="-1" aria-labelledby="uapDashKpiActivityTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg uap-activity-fr-dialog">
    <div class="modal-content uap-activity-fr-modal uap-activity-detail-modal__content uap-adm">
      <div class="modal-header uap-activity-fr-modal__header uap-activity-detail-modal__header uap-adm__header">
        <h5 class="modal-title mb-0" id="uapDashKpiActivityTitle">
          <i class="bi bi-list-columns uap-activity-fr-modal__title-ico" aria-hidden="true" id="uapDashKpiActivityTitleIco"></i>
          <span id="uapDashKpiActivityHeading">Activity</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body uap-activity-fr-modal__body uap-activity-detail-modal__body uap-adm__body">
        <div class="uap-adm-filter" aria-live="polite">
          <div class="uap-adm-filter__ico" aria-hidden="true"><i class="bi bi-calendar3"></i></div>
          <div class="uap-adm-filter__text">
            <span class="uap-adm-filter__label">Reporting period</span>
            <span class="uap-adm-filter__value" id="uapDashKpiActivityRange"></span>
          </div>
        </div>
        <p class="d-none text-muted small mb-2" id="uapDashKpiActivityNote" role="note"></p>
        <p class="uap-adm-trunc alert alert-warning d-none small mt-1 mb-0" id="uapDashKpiActivityTruncated" role="status">Showing the first 500. Narrow the date range to see the rest.</p>
        <div class="d-none uap-adm-loading text-center py-5" id="uapDashKpiActivityLoading" role="status">
          <div class="uap-adm-loading__spinner" role="status" aria-label="Loading"></div>
          <p class="mt-3 uap-adm-loading__txt mb-0">Loading…</p>
        </div>
        <p class="d-none uap-adm-err text-danger small mt-2 mb-0" id="uapDashKpiActivityError" role="alert"></p>
        <div class="d-none" id="uapDashKpiActivityTableSection">
          <div class="uap-adm-panel uap-dash-auth-panel">
            <div class="uap-adm-panel__head">
              <i class="bi bi-people" aria-hidden="true"></i>
              <span>Per event for the selected filters (same as KPI scope)</span>
            </div>
            <div class="table-responsive uap-adm-table-wrap">
              <table class="table uap-activity-fr-table uap-adm-data-table table-sm align-middle mb-0">
                <thead class="uap-adm-thead">
                  <tr>
                    <th scope="col">User</th>
                    <th scope="col">Type</th>
                    <th scope="col" class="d-none d-md-table-cell">Module</th>
                    <th scope="col" class="text-nowrap">Date</th>
                    <th scope="col" class="text-nowrap">Time</th>
                    <th scope="col" class="text-end">Rec.</th>
                    <th scope="col" class="d-none d-lg-table-cell">Ref / open</th>
                  </tr>
                </thead>
                <tbody id="uapDashKpiActivityTbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade uap-activity-detail-modal" id="uapAllUsersListModal" tabindex="-1" aria-labelledby="uapAllUsersListTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg uap-activity-fr-dialog">
    <div class="modal-content uap-activity-fr-modal uap-activity-detail-modal__content uap-adm">
      <div class="modal-header uap-activity-fr-modal__header uap-activity-detail-modal__header uap-adm__header">
        <h5 class="modal-title mb-0" id="uapAllUsersListTitle">
          <i class="bi bi-people uap-activity-fr-modal__title-ico" aria-hidden="true"></i>
          <span id="uapAllUsersListHeading">All users in range</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body uap-activity-fr-modal__body uap-activity-detail-modal__body uap-adm__body">
        <div class="uap-adm-filter" aria-live="polite">
          <div class="uap-adm-filter__ico" aria-hidden="true"><i class="bi bi-calendar3"></i></div>
          <div class="uap-adm-filter__text">
            <span class="uap-adm-filter__label">Reporting period</span>
            <span class="uap-adm-filter__value" id="uapAllUsersListRange"></span>
          </div>
        </div>
        <p class="text-muted small mb-2" id="uapAllUsersListSub" role="status"></p>
        <p class="uap-adm-trunc alert alert-warning d-none small mt-1 mb-0" id="uapAllUsersListTruncated" role="status">List is limited to 2,000 names. Narrow filters or use the table below for pagination.</p>
        <div class="d-none uap-adm-loading text-center py-5" id="uapAllUsersListLoading" role="status">
          <div class="uap-adm-loading__spinner" role="status" aria-label="Loading"></div>
          <p class="mt-3 uap-adm-loading__txt mb-0">Loading…</p>
        </div>
        <p class="d-none uap-adm-err text-danger small mt-2 mb-0" id="uapAllUsersListError" role="alert"></p>
        <div class="d-none" id="uapAllUsersListTableSection">
          <div class="uap-adm-panel uap-dash-auth-panel">
            <div class="table-responsive uap-adm-table-wrap">
              <table class="table uap-activity-fr-table uap-adm-data-table table-sm align-middle mb-0">
                <thead class="uap-adm-thead">
                  <tr>
                    <th scope="col" class="text-muted" style="width:3rem;">#</th>
                    <th scope="col">User</th>
                    <th scope="col" class="text-end" style="min-width:6.5rem;">Events</th>
                    <th scope="col" class="text-nowrap">Last activity</th>
                    <th scope="col" class="text-end" style="min-width:5.5rem;" aria-label="Open detail">View</th>
                  </tr>
                </thead>
                <tbody id="uapAllUsersListTbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- “#” column: all events in range for one user (same data as the KPI read/write modals, scoped to the user) --}}
<div class="modal fade uap-activity-detail-modal" id="uapUserListRowAllEventsModal" tabindex="-1" aria-labelledby="uapUserListRowAllEventsTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl uap-activity-fr-dialog">
    <div class="modal-content uap-activity-fr-modal uap-activity-detail-modal__content uap-adm">
      <div class="modal-header uap-activity-fr-modal__header uap-activity-detail-modal__header uap-adm__header">
        <h5 class="modal-title mb-0" id="uapUserListRowAllEventsTitle">
          <i class="bi bi-list-ul uap-activity-fr-modal__title-ico" aria-hidden="true"></i>
          <span id="uapUserListRowAllEventsHeading">All events in range</span>
        </h5>
        <a href="#" class="btn btn-sm uap-adm__header-link d-none" id="uapUserListRowAllEventsOpenUser" target="_blank" rel="noopener">Full page <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i></a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body uap-activity-fr-modal__body uap-activity-detail-modal__body uap-adm__body">
        <div class="uap-adm-filter" aria-live="polite">
          <div class="uap-adm-filter__ico" aria-hidden="true"><i class="bi bi-calendar3"></i></div>
          <div class="uap-adm-filter__text">
            <span class="uap-adm-filter__label">Reporting period</span>
            <span class="uap-adm-filter__value" id="uapUserListRowAllEventsRange"></span>
          </div>
        </div>
        <p class="uap-adm-trunc alert alert-warning d-none small mt-2 mb-0" id="uapUserListRowAllEventsTruncated" role="status">Showing the first 500 log rows. Narrow the date range to load older rows.</p>
        <div class="d-none uap-adm-loading text-center py-5" id="uapUserListRowAllEventsLoading" role="status">
          <div class="uap-adm-loading__spinner" role="status" aria-label="Loading"></div>
          <p class="mt-3 uap-adm-loading__txt mb-0">Loading…</p>
        </div>
        <p class="d-none uap-adm-err text-danger small mt-2 mb-0" id="uapUserListRowAllEventsError" role="alert"></p>
        <div class="d-none" id="uapUserListRowAllEventsTableSection">
          <div class="uap-adm-panel uap-dash-auth-panel">
            <div class="uap-adm-panel__head">
              <i class="bi bi-journal-text" aria-hidden="true"></i>
              <span>Log lines (newest first)</span>
            </div>
            <div class="table-responsive uap-adm-table-wrap">
              <table class="table uap-activity-fr-table uap-adm-data-table table-sm align-middle mb-0">
                <thead class="uap-adm-thead">
                  <tr>
                    <th scope="col" class="text-nowrap">Type</th>
                    <th scope="col" class="text-nowrap d-none d-sm-table-cell">Method</th>
                    <th scope="col" class="d-none d-md-table-cell">Module</th>
                    <th scope="col" class="d-none d-lg-table-cell">Path</th>
                    <th scope="col" class="text-nowrap">Date</th>
                    <th scope="col" class="text-nowrap">Start</th>
                    <th scope="col" class="text-nowrap">End</th>
                    <th scope="col" class="text-nowrap d-none d-xl-table-cell">Time taken</th>
                    <th scope="col" class="text-end">Rec.</th>
                    <th scope="col" class="d-none d-md-table-cell">Reference</th>
                  </tr>
                </thead>
                <tbody id="uapUserListRowAllEventsTbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- By-module drill-down: same shell as other UAP detail modals --}}
<div class="modal fade uap-activity-detail-modal" id="uapDashModuleEventsModal" tabindex="-1" aria-labelledby="uapDashModuleEventsTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl uap-activity-fr-dialog">
    <div class="modal-content uap-activity-fr-modal uap-activity-detail-modal__content uap-adm uap-adm--v2">
      <div class="modal-header uap-activity-fr-modal__header uap-activity-detail-modal__header uap-adm__header">
        <h5 class="modal-title mb-0" id="uapDashModuleEventsTitle">
          <i class="bi bi-eye uap-activity-fr-modal__title-ico" aria-hidden="true"></i>
          <span id="uapDashModuleEventsHeading">Module</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body uap-activity-fr-modal__body uap-activity-detail-modal__body uap-adm__body">
        <div class="uap-adm-filter" aria-live="polite">
          <div class="uap-adm-filter__ico" aria-hidden="true"><i class="bi bi-calendar3"></i></div>
          <div class="uap-adm-filter__text">
            <span class="uap-adm-filter__label">Reporting period</span>
            <span class="uap-adm-filter__value uap-adm-filter__value--range" id="uapDashModuleEventsRange"></span>
          </div>
        </div>
        <p class="d-none uap-adm-trunc alert alert-warning small mt-2 mb-0" id="uapDashModuleEventsScanCap" role="status">
          Only the most recent <strong>15&thinsp;000</strong> log entries in this range were scanned. Narrow the date range to include older matches.
        </p>
        <p class="uap-adm-trunc alert alert-warning d-none small mt-2 mb-0" id="uapDashModuleEventsTruncated" role="status">
          Showing the first 500 of more events. Narrow the date range to see the rest.
        </p>
        <div class="d-none uap-adm-loading text-center py-5" id="uapDashModuleEventsLoading" role="status" aria-live="polite">
          <div class="uap-adm-loading__spinner" role="status" aria-label="Loading"></div>
          <p class="mt-3 uap-adm-loading__txt mb-0">Loading events…</p>
        </div>
        <p class="d-none uap-adm-err text-danger small mt-2 mb-0" id="uapDashModuleEventsError" role="alert"></p>
        <div class="d-none uap-adm-events-wrap" id="uapDashModuleEventsEventsSection">
          <div class="uap-adm-panel" id="uapDashModuleEventsTableWrap">
            <div class="uap-adm-panel__head">
              <i class="bi bi-table" aria-hidden="true"></i>
              <span>Event log for this module</span>
            </div>
            <div class="table-responsive uap-adm-table-wrap">
              <table class="table uap-activity-fr-table uap-activity-detail-table uap-adm-data-table uap-adm--event-log align-middle mb-0">
                <thead class="uap-adm-thead uap-adm-thead--soft">
                  <tr>
                    <th scope="col" class="uap-activity-col-module">Module</th>
                    <th scope="col" class="uap-activity-col-ref">Reference</th>
                    <th scope="col" class="uap-activity-col-date text-nowrap">Date</th>
                    <th scope="col" class="uap-activity-col-time text-nowrap uap-evt-th-start">Start <span class="uap-evt-th-hint">(time)</span></th>
                    <th scope="col" class="uap-activity-col-time text-nowrap uap-evt-th-end">End <span class="uap-evt-th-hint">(time)</span></th>
                    <th scope="col" class="uap-activity-col-total">Time taken</th>
                  </tr>
                </thead>
                <tbody id="uapDashModuleEventsTbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@include('superadmin.superadminfooter')
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
  window.uapDataUrl = @json(route('user_activity.data'));
  var uapDataUrl = window.uapDataUrl;
  window.uapShowBsModal = function($m) {
    if (!$m || !$m.length) return;
    var el = $m[0];
    if (window.bootstrap && window.bootstrap.Modal) {
      var M = window.bootstrap.Modal;
      (M.getOrCreateInstance ? M.getOrCreateInstance(el) : new M(el)).show();
      return;
    }
    if (typeof jQuery !== 'undefined' && jQuery.fn && jQuery.fn.modal) {
      jQuery(el).modal('show');
    }
  };
  window.uapFetchJson = function(url) {
    var t = document.querySelector('meta[name="csrf-token"]');
    return fetch(url, {
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': t ? t.getAttribute('content') : ''
      }
    });
  };
</script>
<script>
  (function($) {
    if (!$('.uap-custom-dd').length) return;
    $(document).on('click', '.uap-custom-dd .uap-dd-input', function(e) {
      e.stopPropagation();
      var $dd = $(this).siblings('.tax-dropdown');
      $('.tax-dropdown').not($dd).hide();
      $dd.toggle();
      if ($dd.is(':visible')) {
        $dd.find('.inner-search').val('').focus();
        $dd.find('.dropdown-list div').show();
      }
    });

    $(document).on('keyup', '.uap-custom-dd .inner-search', function() {
      var q = ($(this).val() || '').toLowerCase();
      $(this).closest('.tax-dropdown').find('.dropdown-list div').each(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
      });
    });

    $(document).on('click', '.uap-custom-dd .dropdown-list div', function(e) {
      e.stopPropagation();
      var $wrapper = $(this).closest('.tax-dropdown-wrapper');
      $wrapper.find('.dropdown-list div').removeClass('selected');
      $(this).addClass('selected');
      $wrapper.find('.uap-dd-input').val($(this).text().trim());
      $wrapper.find('#uap-user-hidden').val($(this).attr('data-id'));
      $(this).closest('.tax-dropdown').hide();
    });

    $(document).on('click', function(e) {
      if (!$(e.target).closest('.uap-custom-dd').length) {
        $('.tax-dropdown').hide();
      }
    });
  })(jQuery);
  (function() {
    if (typeof jQuery === 'undefined') return;
    var uapFrom = @json($from);
    var uapTo = @json($to);
    var uapUserId = @json($userId);
    jQuery(document).on('click', 'a.uap-dash-auth-kpi', function(e) {
      e.preventDefault();
      var tkey = jQuery(this).attr('data-type-key');
      if (tkey !== 'login' && tkey !== 'logout') return;
      var $m = jQuery('#uapDashAuthEventsModal');
      var label = jQuery(this).attr('data-type-label') || tkey;
      var ico = tkey === 'logout' ? 'bi-box-arrow-left' : 'bi-box-arrow-in-right';
      jQuery('#uapDashAuthEventsTitleIco').attr('class', 'bi uap-activity-fr-modal__title-ico ' + ico);
      $m.find('#uapDashAuthEventsHeading').text(label);
      $m.find('#uapDashAuthEventsRange').text(uapFrom + '  \u2192  ' + uapTo);
      $m.find('#uapDashAuthEventsTbody').empty();
      $m.find('#uapDashAuthEventsError').addClass('d-none').text('');
      $m.find('#uapDashAuthEventsTruncated').addClass('d-none');
      $m.find('#uapDashAuthEventsTableSection').addClass('d-none');
      $m.find('#uapDashAuthEventsLoading').removeClass('d-none');
      if (window.uapShowBsModal) window.uapShowBsModal($m);
      var params = { panel: 'auth', typeKey: tkey, from: uapFrom, to: uapTo };
      if (uapUserId) params.user_id = uapUserId;
      var url = uapDataUrl + '?' + jQuery.param(params);
      (window.uapFetchJson ? window.uapFetchJson(url) : fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } }))
        .then(function(r) {
          if (!r.ok) throw new Error('failed');
          return r.json();
        })
        .then(function(data) {
          $m.find('#uapDashAuthEventsLoading').addClass('d-none');
          if (data.type_label) $m.find('#uapDashAuthEventsHeading').text(data.type_label);
          if (data.truncated) $m.find('#uapDashAuthEventsTruncated').removeClass('d-none');
          $m.find('#uapDashAuthEventsTableSection').removeClass('d-none');
          var $tb = $m.find('#uapDashAuthEventsTbody');
          (data.items || []).forEach(function(row) {
            $tb.append(jQuery('<tr>').append(
              jQuery('<td>').addClass('uap-dash-auth-user').text(row.user_name || '—'),
              jQuery('<td>').addClass('text-nowrap uap-tcol').text(row.date || '—'),
              jQuery('<td>').addClass('text-nowrap uap-tcol').text(row.time || '—')
            ));
          });
          if (!data.items || !data.items.length) {
            $tb.append('<tr><td colspan="3" class="text-center text-muted py-4">No ' + tkey + ' events in this range for the current filters.</td></tr>');
          }
        })
        .catch(function() {
          $m.find('#uapDashAuthEventsLoading').addClass('d-none');
          $m.find('#uapDashAuthEventsTableSection').addClass('d-none');
          if (window.toastr) toastr.error('Failed to load events', 'Error');
          $m.find('#uapDashAuthEventsError').removeClass('d-none').text('Could not load the list. Try again.');
        });
    });
  })();
  (function() {
    if (typeof jQuery === 'undefined') return;
    var uapFrom = @json($from);
    var uapTo = @json($to);
    var uapUserId = @json($userId);
    jQuery(document).on('click', 'a.uap-dash-open-sessions-kpi', function(e) {
      e.preventDefault();
      var $m = jQuery('#uapDashOpenSessionsModal');
      var label = jQuery(this).attr('data-type-label') || 'Open sessions';
      $m.find('#uapDashOpenSessionsHeading').text(label);
      $m.find('#uapDashOpenSessionsRange').text(uapFrom + '  \u2192  ' + uapTo);
      $m.find('#uapDashOpenSessionsTbody').empty();
      $m.find('#uapDashOpenSessionsError').addClass('d-none').text('');
      $m.find('#uapDashOpenSessionsTruncated').addClass('d-none');
      $m.find('#uapDashOpenSessionsTableSection').addClass('d-none');
      $m.find('#uapDashOpenSessionsLoading').removeClass('d-none');
      if (window.uapShowBsModal) window.uapShowBsModal($m);
      var params = { panel: 'open', from: uapFrom, to: uapTo };
      if (uapUserId) params.user_id = uapUserId;
      var url = uapDataUrl + '?' + jQuery.param(params);
      (window.uapFetchJson ? window.uapFetchJson(url) : fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } }))
        .then(function(r) {
          if (!r.ok) throw new Error('failed');
          return r.json();
        })
        .then(function(data) {
          $m.find('#uapDashOpenSessionsLoading').addClass('d-none');
          if (data.type_label) $m.find('#uapDashOpenSessionsHeading').text(data.type_label);
          if (data.truncated) $m.find('#uapDashOpenSessionsTruncated').removeClass('d-none');
          $m.find('#uapDashOpenSessionsTableSection').removeClass('d-none');
          var $tb = $m.find('#uapDashOpenSessionsTbody');
          (data.items || []).forEach(function(row) {
            $tb.append(jQuery('<tr>').append(
              jQuery('<td>').addClass('uap-dash-auth-user').text(row.user_name || '—'),
              jQuery('<td>').addClass('text-nowrap uap-tcol').text(row.date || '—'),
              jQuery('<td>').addClass('text-nowrap uap-tcol').text(row.time || '—'),
              jQuery('<td>').addClass('text-nowrap uap-tcol text-muted small').text(row.last_seen != null && row.last_seen !== '' ? row.last_seen : '—')
            ));
          });
          if (!data.items || !data.items.length) {
            $tb.append('<tr><td colspan="4" class="text-center text-muted py-4">No open sessions in this range for the current filters. Sessions are “open” when the user is still signed in (no end time) and the session start falls in the selected dates.</td></tr>');
          }
        })
        .catch(function() {
          $m.find('#uapDashOpenSessionsLoading').addClass('d-none');
          $m.find('#uapDashOpenSessionsTableSection').addClass('d-none');
          if (window.toastr) toastr.error('Failed to load open sessions', 'Error');
          $m.find('#uapDashOpenSessionsError').removeClass('d-none').text('Could not load the list. Try again.');
        });
    });
  })();
  (function() {
    if (typeof jQuery === 'undefined') return;
    var uapFrom = @json($from);
    var uapTo = @json($to);
    var uapUserId = @json($userId);
    jQuery(document).on('click', 'a.uap-dash-activity-kpi', function(e) {
      e.preventDefault();
      var act = jQuery(this).attr('data-activity');
      if (act !== 'read' && act !== 'write') return;
      var $m = jQuery('#uapDashKpiActivityModal');
      var label = jQuery(this).attr('data-type-label') || act;
      var ico = act === 'read' ? 'bi-eye' : 'bi-pencil-square';
      jQuery('#uapDashKpiActivityTitleIco').attr('class', 'bi uap-activity-fr-modal__title-ico ' + ico);
      $m.find('#uapDashKpiActivityHeading').text(label);
      $m.find('#uapDashKpiActivityRange').text(uapFrom + '  \u2192  ' + uapTo);
      $m.find('#uapDashKpiActivityTbody').empty();
      $m.find('#uapDashKpiActivityError').addClass('d-none').text('');
      $m.find('#uapDashKpiActivityTruncated').addClass('d-none');
      $m.find('#uapDashKpiActivityNote').addClass('d-none').text('');
      $m.find('#uapDashKpiActivityTableSection').addClass('d-none');
      $m.find('#uapDashKpiActivityLoading').removeClass('d-none');
      if (window.uapShowBsModal) window.uapShowBsModal($m);
      var params = { panel: 'activity', typeKey: act, from: uapFrom, to: uapTo };
      if (uapUserId) params.user_id = uapUserId;
      var url = uapDataUrl + '?' + jQuery.param(params);
      (window.uapFetchJson ? window.uapFetchJson(url) : fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } }))
        .then(function(r) { if (!r.ok) throw new Error('failed'); return r.json(); })
        .then(function(data) {
          $m.find('#uapDashKpiActivityLoading').addClass('d-none');
          if (data.type_label) $m.find('#uapDashKpiActivityHeading').text(data.type_label);
          if (data.truncated) $m.find('#uapDashKpiActivityTruncated').removeClass('d-none');
          if (data.kpi_note) {
            $m.find('#uapDashKpiActivityNote').text(data.kpi_note).removeClass('d-none');
          }
          $m.find('#uapDashKpiActivityTableSection').removeClass('d-none');
          var $tb = $m.find('#uapDashKpiActivityTbody');
          (data.items || []).forEach(function(row) {
            var $ref = jQuery('<td>').addClass('d-none d-lg-table-cell small uap-activity-td--ref');
            if (row.link_url) {
              $ref.append(
                jQuery('<a class="uap-ref-link" rel="noopener" target="_blank">')
                  .attr('href', row.link_url)
                  .text((row.reference && row.reference !== '—') ? String(row.reference).slice(0, 40) : (row.link_label || 'Open'))
              );
            } else {
              $ref.text((row.reference != null && String(row.reference) !== '—' && String(row.reference) !== '') ? String(row.reference).slice(0, 48) : '—');
            }
            $tb.append(
              jQuery('<tr>').append(
                jQuery('<td>').addClass('uap-dash-auth-user').text(row.user_name || '—'),
                jQuery('<td>').addClass('text-nowrap small').text(row.type_label || row.type || '—'),
                jQuery('<td>').addClass('d-none d-md-table-cell text-muted small').text(row.module_label || row.module || '—'),
                jQuery('<td>').addClass('text-nowrap uap-tcol').text(row.date || '—'),
                jQuery('<td>').addClass('text-nowrap uap-tcol').text(row.end_time || '—'),
                jQuery('<td>').addClass('text-end uap-tcol').text((row.records != null && row.records > 0) ? row.records : (row.type === 'read' ? '—' : 0)),
                $ref
              )
            );
          });
          if (!data.items || !data.items.length) {
            $tb.append('<tr><td colspan="7" class="text-center text-muted py-4">No log rows in this range for the current filters.</td></tr>');
          }
        })
        .catch(function() {
          $m.find('#uapDashKpiActivityLoading').addClass('d-none');
          $m.find('#uapDashKpiActivityTableSection').addClass('d-none');
          if (window.toastr) toastr.error('Failed to load activity', 'Error');
          $m.find('#uapDashKpiActivityError').removeClass('d-none').text('Could not load the list. Try again.');
        });
    });
  })();
  (function() {
    if (typeof jQuery === 'undefined') return;
    var uapFromA = @json($from);
    var uapToA = @json($to);
    var uapUserIdA = @json($userId);
    var uapUiA = @json(request('ui'));
    jQuery(document).on('click', 'a.uap-all-users-header-kpi', function(e) {
      e.preventDefault();
      var $m = jQuery('#uapAllUsersListModal');
      $m.find('#uapAllUsersListRange').text(uapFromA + '  \u2192  ' + uapToA);
      $m.find('#uapAllUsersListSub').text('');
      $m.find('#uapAllUsersListTbody').empty();
      $m.find('#uapAllUsersListError').addClass('d-none').text('');
      $m.find('#uapAllUsersListTruncated').addClass('d-none');
      $m.find('#uapAllUsersListTableSection').addClass('d-none');
      $m.find('#uapAllUsersListLoading').removeClass('d-none');
      if (window.uapShowBsModal) window.uapShowBsModal($m);
      var params = { panel: 'all_users', from: uapFromA, to: uapToA };
      if (uapUserIdA) params.user_id = uapUserIdA;
      if (uapUiA) params.ui = uapUiA;
      var url = uapDataUrl + '?' + jQuery.param(params);
      (window.uapFetchJson ? window.uapFetchJson(url) : fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } }))
        .then(function(r) { if (!r.ok) throw new Error('failed'); return r.json(); })
        .then(function(data) {
          $m.find('#uapAllUsersListLoading').addClass('d-none');
          if (data.type_label) $m.find('#uapAllUsersListHeading').text(data.type_label);
          var total = (typeof data.total_users === 'number' ? data.total_users : 0);
          var nShown = (data.items && data.items.length) ? data.items.length : 0;
          var sub = 'Users in directory' + (uapUserIdA ? ' (user filter on)' : '') + ': ' + total + ' — ' + nShown + ' in this list.';
          $m.find('#uapAllUsersListSub').text(sub);
          if (data.truncated) $m.find('#uapAllUsersListTruncated').removeClass('d-none');
          $m.find('#uapAllUsersListTableSection').removeClass('d-none');
          var $tb = $m.find('#uapAllUsersListTbody');
          (data.items || []).forEach(function(row, i) {
            var $a = jQuery('<a>').addClass('uap-logs-open-link')
              .attr('href', row.detail_url || '#')
              .html('View <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>');
            $tb.append(
              jQuery('<tr>').append(
                jQuery('<td>').addClass('text-muted small').text(i + 1),
                jQuery('<td>').addClass('uap-dash-auth-user small').text(row.name || '—'),
                jQuery('<td>').addClass('text-end uap-tcol small').text((row.event_count != null) ? String(row.event_count) : '0'),
                jQuery('<td>').addClass('text-nowrap uap-tcol text-muted small').text(row.last_at_display || '—'),
                jQuery('<td>').addClass('text-end').append($a)
              )
            );
          });
          if (!data.items || !data.items.length) {
            $tb.append('<tr><td colspan="5" class="text-center text-muted py-4">No users in this list for the current filters.</td></tr>');
          }
        })
        .catch(function() {
          $m.find('#uapAllUsersListLoading').addClass('d-none');
          $m.find('#uapAllUsersListTableSection').addClass('d-none');
          if (window.toastr) toastr.error('Failed to load users', 'Error');
          $m.find('#uapAllUsersListError').removeClass('d-none').text('Could not load the list. Try again.');
        });
    });
  })();
  (function() {
    if (typeof jQuery === 'undefined') return;
    var uapFromRow = @json($from);
    var uapToRow = @json($to);
    var uapTypeEventsAllUrlT = @json($uapTypeEventsAllUrlTemplate);
    function uapUserAllEventsUrl(uid) {
      return uapTypeEventsAllUrlT.replace('__UID__', String(uid)) + '?' + jQuery.param({
        from: uapFromRow,
        to: uapToRow
      });
    }
    function uapUserRowRefCell(row) {
      var $ref = jQuery('<td>').addClass('d-none d-md-table-cell uap-activity-td--ref text-break small');
      if (row.link_url) {
        $ref.append(
          jQuery('<a class="uap-ref-link" rel="noopener" target="_blank">')
            .attr('href', row.link_url)
            .text((row.reference && row.reference !== '—') ? String(row.reference).slice(0, 120) : (row.link_label || 'Open'))
        );
      } else {
        $ref.text((row.reference != null && String(row.reference) !== '—' && String(row.reference) !== '') ? String(row.reference).slice(0, 80) : '—');
      }
      return $ref;
    }
    function openUserListRowAllEventsFromTr($tr) {
      if (!$tr || !$tr.length) {
        return;
      }
      var uid = parseInt(String($tr.attr('data-user-id') || '0'), 10);
      if (uid < 1) {
        return;
      }
      var uname = $tr.attr('data-user-name') || ('User #' + uid);
      var userPage = $tr.attr('data-href') || '#';
      var $m = jQuery('#uapUserListRowAllEventsModal');
      $m.find('#uapUserListRowAllEventsHeading').text(uname);
      $m.find('#uapUserListRowAllEventsRange').text(uapFromRow + '  \u2192  ' + uapToRow);
      $m.find('#uapUserListRowAllEventsTbody').empty();
      $m.find('#uapUserListRowAllEventsError').addClass('d-none').text('');
      $m.find('#uapUserListRowAllEventsTruncated').addClass('d-none');
      $m.find('#uapUserListRowAllEventsTableSection').addClass('d-none');
      $m.find('#uapUserListRowAllEventsLoading').removeClass('d-none');
      if (userPage && userPage !== '#') {
        jQuery('#uapUserListRowAllEventsOpenUser').attr('href', userPage).removeClass('d-none');
      } else {
        jQuery('#uapUserListRowAllEventsOpenUser').addClass('d-none').attr('href', '#');
      }
      if (window.uapShowBsModal) window.uapShowBsModal($m);
      var url = uapUserAllEventsUrl(uid);
      (window.uapFetchJson ? window.uapFetchJson(url) : fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } }))
        .then(function(r) { if (!r.ok) throw new Error('failed'); return r.json(); })
        .then(function(data) {
          $m.find('#uapUserListRowAllEventsLoading').addClass('d-none');
          if (data.type_label) {
            $m.find('#uapUserListRowAllEventsHeading').text(uname + ' \u2014 ' + data.type_label);
          }
          if (data.truncated) {
            $m.find('#uapUserListRowAllEventsTruncated').removeClass('d-none');
          }
          $m.find('#uapUserListRowAllEventsTableSection').removeClass('d-none');
          var $tb = $m.find('#uapUserListRowAllEventsTbody');
          (data.items || []).forEach(function(row) {
            $tb.append(
              jQuery('<tr>').append(
                jQuery('<td>').addClass('text-nowrap small fw-medium').text(row.log_type_label || row.log_type || '—'),
                jQuery('<td>').addClass('d-none d-sm-table-cell text-nowrap text-muted small').text((row.http_method && row.http_method !== '') ? String(row.http_method) : '—'),
                jQuery('<td>').addClass('d-none d-md-table-cell text-muted small').text(row.module_label || row.module || '—'),
                jQuery('<td>').addClass('d-none d-lg-table-cell text-muted small uap-activity-td--path text-break')
                  .text((row.path && String(row.path) !== '') ? (String(row.path).length > 80 ? String(row.path).slice(0, 78) + '…' : String(row.path)) : '—'),
                jQuery('<td>').addClass('text-nowrap uap-tcol').text(row.date || '—'),
                jQuery('<td>').addClass('text-nowrap uap-tcol').text(row.start_time || '—'),
                jQuery('<td>').addClass('text-nowrap uap-tcol').text(row.end_time || '—'),
                jQuery('<td>').addClass('d-none d-xl-table-cell text-muted small').text(row.total != null && String(row.total) !== '' ? String(row.total) : '—'),
                jQuery('<td>').addClass('text-end uap-tcol small')
                  .text((row.records != null && row.records > 0) ? String(row.records) : '—'),
                uapUserRowRefCell(row)
              )
            );
          });
          if (!data.items || !data.items.length) {
            $tb.append(
              jQuery('<tr>').append(
                jQuery('<td colspan="10" class="text-center text-muted py-4">')
                  .text('No log rows in this range for this user.')
              )
            );
          }
        })
        .catch(function() {
          $m.find('#uapUserListRowAllEventsLoading').addClass('d-none');
          $m.find('#uapUserListRowAllEventsTableSection').addClass('d-none');
          if (window.toastr) toastr.error('Failed to load user activity', 'Error');
          $m.find('#uapUserListRowAllEventsError').removeClass('d-none').text('Could not load the list. Try again.');
        });
    }
    jQuery(document).on('click', 'a.uap-user-row-open-modal', function(e) {
      e.preventDefault();
      e.stopPropagation();
      openUserListRowAllEventsFromTr(jQuery(this).closest('tr.uap-user-list-row'));
    });
    jQuery(document).on('click', 'tr.uap-user-list-row', function(e) {
      if (jQuery(e.target).closest('a, td.uap-user-all-events-idx').length) {
        return;
      }
      e.preventDefault();
      openUserListRowAllEventsFromTr(jQuery(this));
    });
    jQuery(document).on('keydown', 'tr.uap-user-list-row', function(e) {
      if (e.key !== 'Enter' && e.key !== ' ') {
        return;
      }
      if (jQuery(e.target).closest('a, td.uap-user-all-events-idx').length) {
        return;
      }
      e.preventDefault();
      openUserListRowAllEventsFromTr(jQuery(this));
    });
    jQuery(document).on('click', 'td.uap-user-all-events-idx', function(e) {
      e.preventDefault();
      e.stopPropagation();
      openUserListRowAllEventsFromTr(jQuery(this).closest('tr.uap-user-list-row'));
    });
    jQuery(document).on('keydown', 'td.uap-user-all-events-idx', function(e) {
      if (e.key !== 'Enter' && e.key !== ' ') {
        return;
      }
      e.preventDefault();
      e.stopPropagation();
      openUserListRowAllEventsFromTr(jQuery(this).closest('tr.uap-user-list-row'));
    });
  })();
</script>
<script>
(function() {
  if (typeof jQuery === 'undefined') return;
  var uapFrom = @json($from);
  var uapTo = @json($to);
  var uapUserId = @json($userId);

  function openModuleModal(moduleName) {
    if (!moduleName) return;
    var $m = jQuery('#uapDashModuleEventsModal');
    if (!$m.length) return;
    $m.find('#uapDashModuleEventsHeading').text(moduleName);
    $m.find('#uapDashModuleEventsRange').text(uapFrom + ' \u2192 ' + uapTo);
    $m.find('#uapDashModuleEventsTbody').empty();
    $m.find('#uapDashModuleEventsError').addClass('d-none').text('');
    $m.find('#uapDashModuleEventsTruncated').addClass('d-none');
    $m.find('#uapDashModuleEventsScanCap').addClass('d-none');
    $m.find('#uapDashModuleEventsEventsSection').addClass('d-none');
    $m.find('#uapDashModuleEventsLoading').removeClass('d-none');
    if (window.uapShowBsModal) window.uapShowBsModal($m);
    var params = { panel: 'module', from: uapFrom, to: uapTo, module: moduleName };
    if (uapUserId) params.user_id = uapUserId;
    var url = uapDataUrl + '?' + jQuery.param(params);
    (window.uapFetchJson ? window.uapFetchJson(url) : fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } }))
      .then(function(r) { if (!r.ok) throw new Error('failed'); return r.json(); })
      .then(function(data) {
        $m.find('#uapDashModuleEventsLoading').addClass('d-none');
        if (data.type_label) $m.find('#uapDashModuleEventsHeading').text(data.type_label);
        if (data.truncated) $m.find('#uapDashModuleEventsTruncated').removeClass('d-none');
        if (data.scan_capped) $m.find('#uapDashModuleEventsScanCap').removeClass('d-none');
        $m.find('#uapDashModuleEventsEventsSection').removeClass('d-none');
        var $tb = $m.find('#uapDashModuleEventsTbody');
        (data.items || []).forEach(function(row) {
          if (typeof window.uapEventLogBuildRow === 'function') {
            $tb.append(window.uapEventLogBuildRow(row));
            return;
          }
          $tb.append(
            jQuery('<tr>').append(
              jQuery('<td>').text(row.module_label || '—'),
              jQuery('<td>').text(row.reference || '—'),
              jQuery('<td>').text(row.date || '—'),
              jQuery('<td>').text(row.total || '—')
            )
          );
        });
        if (!data.items || !data.items.length) {
          $tb.append(
            '<tr><td colspan="6" class="text-center text-muted py-4 uap-evt-log-empty">No log entries in this range for this module (with current filters).</td></tr>'
          );
        }
      })
      .catch(function() {
        $m.find('#uapDashModuleEventsLoading').addClass('d-none');
        $m.find('#uapDashModuleEventsEventsSection').addClass('d-none');
        if (window.toastr) toastr.error('Failed to load module events', 'Error');
        $m.find('#uapDashModuleEventsError').removeClass('d-none').text('Could not load the list. Try again.');
      });
  }

  jQuery(document).on('click', 'tr.uap-by-module-row', function(e) {
    var m = this.getAttribute('data-module');
    if (!m) return;
    var tag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
    if (tag === 'a' || tag === 'button' || jQuery(e.target).closest('a').length) return;
    openModuleModal(m);
  });
  jQuery(document).on('keydown', 'tr.uap-by-module-row', function(e) {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    e.preventDefault();
    var m = this.getAttribute('data-module');
    if (m) openModuleModal(m);
  });
})();
</script>
</body>
</html>
