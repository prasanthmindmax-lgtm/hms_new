<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('assets/css/user_activity.css') }}?v=47" />
@inject('uap', 'App\Services\UserActivity\UserActivityService')

@php
  $uapNameWithEmp = function ($user) {
      if (! $user) { return '—'; }
      $full = trim((string) (data_get($user, 'user_fullname') ?: data_get($user, 'name') ?: ''));
      $login = trim((string) (data_get($user, 'username') ?? ''));
      if ($login === '') {
          $eid = data_get($user, 'employee_id');
          $login = is_scalar($eid) && (string) $eid !== '' ? trim((string) $eid) : '';
      }
      if ($full !== '' && $login !== '' && $full === $login) { return $full; }
      if ($full !== '' && $login !== '') { return $full.' - '.$login; }
      if ($full !== '') { return $full; }
      if ($login !== '') { return $login; }
      return '—';
  };
  $qAll = request()->query();
  $qBase = request()->except('ui');
  $linkCompact = route('user_activity.dashboard', array_merge($qBase, ['ui' => 'compact']));
  $linkSame = route('user_activity.dashboard', $qAll);
  $abClass = function (string $t): string {
      return match ($t) {
          'read' => 'read',
          'create' => 'create',
          'update' => 'update',
          'delete' => 'delete',
          'login', 'logout' => 'auth',
          default => 'other',
      };
  };
  $abText = function (string $t): string {
      return match ($t) {
          'read' => 'Page view',
          'create' => 'Create',
          'update' => 'Update',
          'delete' => 'Delete',
          'login' => 'Login',
          'logout' => 'Logout',
          default => $t,
      };
  };
@endphp

<body class="upp-activity-body">
<div class="page-loader"><div class="bar"></div></div>
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container">
  <div class="pc-content">
    <div class="container-fluid" style="max-width: 1600px;">

      <div class="upp-hero">
        <div>
          <p class="upp-hero-title"><i class="bi bi-activity me-2"></i>User activity &amp; performance</p>
          </div>
        <div class="upp-hero-btns">
          <a class="lbtn" href="{{ $linkSame }}">
            <i class="bi bi-arrow-clockwise"></i> Refresh
          </a>
          <a class="lbtn" href="{{ $linkCompact }}"><i class="bi bi-layout-text-sidebar"></i> Compact layout</a>
        </div>
      </div>

      <form method="get" action="{{ route('user_activity.dashboard') }}" class="upp-filters">
        <input type="hidden" name="ui" value="activity" />
        <input type="hidden" name="per_page" value="{{ (int) request('per_page', 25) }}" />
        <div class="row g-3 align-items-end">
          <div class="col-auto">
            <label>From</label>
            <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm" />
          </div>
          <div class="col-auto">
            <label>To</label>
            <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm" />
          </div>
          <div class="col-md-4 col-lg-3" style="min-width: 12rem;">
            <label for="uap-user-activity">User (optional)</label>
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
          <div class="col-auto d-flex gap-2">
            <button type="submit" class="btn btn-apply btn-sm" style="min-height: 2.125rem;">Apply filters</button>
            <a href="{{ route('user_activity.dashboard', ['ui' => 'activity']) }}" class="btn btn-light btn-sm d-inline-flex align-items-center justify-content-center" style="min-height: 2.125rem; border: 1.5px solid #e2e8f0; border-radius: 10px; color: #64748b; font-weight: 500; padding: 0 1rem;"><i class="bi bi-x-circle me-1"></i> Clear</a>
          </div>
        </div>
      </form>

      <div class="text-muted small mb-3">
        @php
          $uapFromTs = \Illuminate\Support\Carbon::parse($from)->startOfDay();
          $uapToTs = \Illuminate\Support\Carbon::parse($to)->endOfDay();
        @endphp
        <p class="uap-range-caption mb-0 d-inline-block">
          <i class="bi bi-calendar-range" aria-hidden="true"></i>
          <span class="ms-1">From <time datetime="{{ $uapFromTs->toIso8601String() }}">{{ $uapFromTs->format('Y-m-d H:i') }}</time>
          to <time datetime="{{ $uapToTs->toIso8601String() }}">{{ $uapToTs->format('Y-m-d H:i') }}</time></span>
        </p>
      </div>

      <div class="row g-3 upp-stat-row">
        <div class="col-6 col-sm-4 col-lg">
          <div class="upp-sc purple"><i class="bi bi-list-check"></i><div><div class="n">{{ number_format($activityStats['in_range']) }}</div><div class="l">Events in range</div></div></div>
        </div>
        <div class="col-6 col-sm-4 col-lg">
          <div class="upp-sc pink"><i class="bi bi-calendar-check"></i><div><div class="n">{{ number_format($activityStats['today']) }}</div><div class="l">Today (all time)</div></div></div>
        </div>
        <div class="col-6 col-sm-4 col-lg">
          <div class="upp-sc cyan"><i class="bi bi-calendar-week"></i><div><div class="n">{{ number_format($activityStats['week']) }}</div><div class="l">This week</div></div></div>
        </div>
        <div class="col-6 col-sm-4 col-lg">
          <div class="upp-sc green"><i class="bi bi-calendar2-range"></i><div><div class="n">{{ number_format($activityStats['last_30']) }}</div><div class="l">Last 30 days</div></div></div>
        </div>
        <div class="col-6 col-sm-4 col-lg">
          <div class="upp-sc amber"><i class="bi bi-people"></i><div><div class="n">{{ number_format($activityStats['active_users']) }}</div><div class="l">Active users (30d)</div></div></div>
        </div>
      </div>

      <div class="upp-tbl-wrap">
        <div class="upp-tbl-top">
          <h2><i class="bi bi-clock-history me-1"></i> Activity entries <span class="text-muted" style="font-size:0.78rem;font-weight:500;">— {{ $recentFlat->count() }} rows (max 200)</span></h2>
        </div>
        <div style="overflow: auto; max-height: 620px;">
          <table class="upp-tbl">
            <thead>
              <tr>
                <th class="nw">#</th>
                <th class="nw">Date &amp; time</th>
                <th>User</th>
                <th>Action</th>
                <th>Module</th>
                <th>Route / path</th>
                <th class="text-start upp-tcol-timing" style="min-width:4.5rem;">From (time)</th>
                <th class="text-start upp-tcol-timing" style="min-width:4.5rem;">To (time)</th>
                <th class="text-start upp-tcol-timing" style="min-width:6.5rem;">Time taken (total)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentFlat as $i => $e)
                @php
                  $uu = $userLookup->get($e->user_id);
                  $fromRowSnap = trim((string) (data_get($e, 'user_fullname') ?? data_get($e, 'username') ?? '')) !== '';
                  $nameTarget = $fromRowSnap
                    ? (object) ['user_fullname' => $e->user_fullname, 'username' => $e->username]
                    : $uu;
                @endphp
                <tr>
                  <td class="nw text-muted">{{ $i + 1 }}</td>
                  <td class="nw">{{ $e->created_at?->format('Y-m-d H:i:s') }}</td>
                  <td class="upp-user-cell" style="min-width: 180px;">
                    @if($nameTarget)
                      <div class="nm">{{ $uapNameWithEmp($nameTarget) }}</div>
                    @else
                      <div class="nm">#{{ $e->user_id }}</div>
                    @endif
                  </td>
                  <td><span class="upp-ab {{ $abClass($e->type) }}">{{ $abText($e->type) }}</span></td>
                  <td>
                    @php
                      $modLabel = $uap->resolveModuleLabelForLog($e->activity_module, $e->path, $e->route_name);
                    @endphp
                    <span class="upp-mod-chip @if($modLabel === 'Unassigned') upp-mod-na @endif" title="Stored: {{ $e->activity_module ?? '—' }}">{{ $modLabel }}</span>
                  </td>
                  <td style="min-width: 200px;">
                    @if($e->route_name)
                      <div class="text-primary" style="font-size:0.8rem; font-weight: 600;">{{ $e->route_name }}</div>
                    @endif
                    <div class="text-muted" style="font-size:0.72rem; word-break: break-all;">{{ $e->path }}</div>
                  </td>
                  @php
                    $uapT = app(\App\Services\UserActivity\UserActivityService::class)->getLogTimeColumnValues($e);
                  @endphp
                  <td class="uap-time-frm uap-tcol text-nowrap" title="Estimated start of the measured window">{{ $uapT['from'] }}</td>
                  <td class="uap-time-to uap-tcol text-nowrap" title="End of request (log time, time only)">{{ $uapT['to'] }}</td>
                  <td class="uap-time-tot uap-tcol" title="{{ $uapT['title'] }}">{{ $uapT['total'] }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if($recentFlat->isEmpty())
          <p class="text-center text-muted py-5 mb-0">No rows in this date range.</p>
        @endif
      </div>

      <div class="upp-sec2">
        <div class="upp-side-card">
          <h3>By module</h3>
          <div style="overflow:auto; max-width: 100%;">
            <table class="upp-side-tbl uap-by-module-tbl">
              <thead>
                <tr>
                  <th>Module</th>
                  <th class="text-end">Events</th>
                  <th class="text-end" title="Create (POST, etc.)">Crt</th>
                  <th class="text-end" title="Update (POST/PUT/PATCH)">Upd</th>
                  <th class="text-end" title="Sum of records_count">Units</th>
                </tr>
              </thead>
              <tbody>
                @forelse($byModule as $ar)
                  <tr class="uap-by-module-row" role="button" tabindex="0" data-module="{{ e($ar->activity_module) }}">
                    <td><span class="upp-mod-chip" style="max-width: 100%">{{ $ar->activity_module }}</span></td>
                    <td class="text-end">{{ number_format($ar->n) }}</td>
                    <td class="text-end">{{ number_format($ar->n_create ?? 0) }}</td>
                    <td class="text-end">{{ number_format($ar->n_update ?? 0) }}</td>
                    <td class="text-end">{{ number_format($ar->records) }}</td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-muted p-3 text-center">No activity in range.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <div class="upp-side-card">
          <h3>Top users</h3>
          <table class="upp-side-tbl">
            <thead>
              <tr><th>User</th><th class="text-end">Events</th><th class="text-end">Units</th></tr>
            </thead>
            <tbody>
              @forelse($perUserWithLabels as $row)
                <tr>
                  <td>
                    <div class="nm">{{ $row->user_name ?? '—' }}</div>
                    <div class="em">{{ $row->user_label }}</div>
                  </td>
                  <td class="text-end fw-bold">{{ number_format($row->events) }}</td>
                  <td class="text-end">{{ number_format($row->record_units) }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted p-3">No data</td></tr>
              @endforelse
            </tbody>
          </table>
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
</script>
<script>
  var uapDataUrl = @json(route('user_activity.data'));
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
    if (window.bootstrap && window.bootstrap.Modal) {
      var el = $m[0];
      var bsm = window.bootstrap.Modal.getOrCreateInstance
        ? window.bootstrap.Modal.getOrCreateInstance(el)
        : new window.bootstrap.Modal(el);
      bsm.show();
    }
    var params = { panel: 'module', from: uapFrom, to: uapTo, module: moduleName };
    if (uapUserId) params.user_id = uapUserId;
    var url = uapDataUrl + '?' + jQuery.param(params);
    fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
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
