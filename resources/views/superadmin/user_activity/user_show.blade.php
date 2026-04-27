<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
<link rel="stylesheet" href="{{ asset('assets/css/user_activity.css') }}?v=47" />
@inject('uap', 'App\Services\UserActivity\UserActivityService')
@php
$uapNameWithEmp = function ($u, ?int $fallbackId = null): string {
if (! $u) {
return $fallbackId ? 'User #'.$fallbackId : '—';
}
$full = trim((string) (data_get($u, 'user_fullname') ?: data_get($u, 'name') ?: ''));
$login = trim((string) (data_get($u, 'username') ?? ''));
if ($login === '') {
$eid = data_get($u, 'employee_id');
$login = is_scalar($eid) && (string) $eid !== '' ? trim((string) $eid) : '';
}
if ($full !== '' && $login !== '' && $full === $login) {
return $full;
}
if ($full !== '' && $login !== '') {
return $full.' - '.$login;
}
if ($full !== '') { return $full; }
if ($login !== '') { return $login; }
return $fallbackId ? 'User #'.$fallbackId : '—';
};
$actionLabel = function (string $t): string {
    return match ($t) {
        'read' => 'Page view',
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'login' => 'Login',
        'logout' => 'Logout',
        'other' => 'Other',
        default => $t !== '' ? $t : '—',
    };
};
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
$backUrl = route('user_activity.dashboard', $dashboardQuery);
$linkActivity = route('user_activity.dashboard', array_merge($dashboardQuery, ['ui' => 'activity']));
$uapFromTs = \Illuminate\Support\Carbon::parse($from)->startOfDay();
$uapToTs = \Illuminate\Support\Carbon::parse($to)->endOfDay();
@endphp
<body class="upp-activity-body" style="overflow-x: hidden;">
<div class="page-loader"><div class="bar"></div></div>
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')
<div class="pc-container">
  <div class="pc-content">
<div class="uap-detail-page">
  <div class="container-fluid py-2 py-lg-3" style="max-width: 1600px; margin: 0 auto;">

  <div class="upp-hero uap-detail-hero">
    <div class="uap-detail-hero__text">
      <p class="uap-detail-hero__eyebrow"><i class="bi bi-activity" aria-hidden="true"></i> User activity</p>
      <h1 class="uap-detail-hero__name">{{ $uapNameWithEmp($row) }}</h1>
    </div>
    <div class="upp-hero-btns">
      <a class="lbtn" href="{{ $backUrl }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back to list</a>
      <a class="lbtn" href="{{ $linkActivity }}"><i class="bi bi-layout-text-sidebar-reverse" aria-hidden="true"></i> Activity log layout</a>
    </div>
  </div>

  <form method="get" action="{{ route('user_activity.user', ['user' => $userId]) }}" class="upp-filters">
    <div class="row g-3 align-items-end">
      <div class="col-auto">
        <label for="uap-det-from">From</label>
        <input type="date" id="uap-det-from" name="from" value="{{ $from }}" class="form-control form-control-sm" />
      </div>
      <div class="col-auto">
        <label for="uap-det-to">To</label>
        <input type="date" id="uap-det-to" name="to" value="{{ $to }}" class="form-control form-control-sm" />
      </div>
      <div class="col-auto d-flex gap-2">
        <button type="submit" class="btn btn-apply btn-sm" style="min-height: 2.125rem;"><i class="bi bi-check2-circle me-1"></i> Apply range</button>
        <a href="{{ route('user_activity.user', ['user' => $userId]) }}" class="btn btn-light btn-sm d-inline-flex align-items-center justify-content-center" style="min-height: 2.125rem; border: 1.5px solid #e2e8f0; border-radius: 10px; color: #64748b; font-weight: 500; padding: 0 1rem;"><i class="bi bi-x-circle me-1"></i> Clear</a>
      </div>
    </div>
  </form>

  <div class="mb-3">
    <p class="uap-detail-range mb-0" role="status">
      <i class="bi bi-calendar-range" aria-hidden="true"></i>
      <span>From <time datetime="{{ $uapFromTs->toIso8601String() }}">{{ $uapFromTs->format('Y-m-d H:i') }}</time>
      to <time datetime="{{ $uapToTs->toIso8601String() }}">{{ $uapToTs->format('Y-m-d H:i') }}</time></span>
    </p>
  </div>

  <div class="row g-3 upp-stat-row" role="list">
    <div class="col-6 col-sm-4 col-lg-3 col-xl-3">
      <a href="#" class="d-block h-100 uap-upp-stat-link uap-type-events-link" role="listitem" data-type-key="all" data-type-label="All log entries (this range)">
        <div class="upp-sc purple h-100"><i class="bi bi-list-check"></i><div><div class="n">{{ number_format($logs->total()) }}</div><div class="l">Log entries in range</div></div></div>
      </a>
    </div>
    <div class="col-6 col-sm-4 col-lg-3 col-xl-3">
      <a href="#" class="d-block h-100 uap-upp-stat-link uap-type-events-link" role="listitem" data-type-key="login" data-type-label="Logins (events)">
        <div class="upp-sc pink h-100"><i class="bi bi-box-arrow-in-right"></i><div><div class="n">{{ number_format($logins) }}</div><div class="l">Logins</div></div></div>
      </a>
    </div>
    <div class="col-6 col-sm-4 col-lg-3 col-xl-3">
      <a href="#" class="d-block h-100 uap-upp-stat-link uap-type-events-link" role="listitem" data-type-key="logout" data-type-label="Logouts (events)">
        <div class="upp-sc cyan h-100"><i class="bi bi-box-arrow-left"></i><div><div class="n">{{ number_format($logouts) }}</div><div class="l">Logouts</div></div></div>
      </a>
    </div>
    <div class="col-6 col-sm-4 col-lg-3 col-xl-3">
      <div
        class="d-block h-100 uap-stat-noclick"
        role="listitem"
        title="Sign-in to sign-out (or right now if the session is still open), summed for {{ config('app.timezone') }} today only. Not tied to the date range above.">
        <div class="upp-sc teal h-100"><i class="bi bi-stopwatch" aria-hidden="true"></i><div><div class="n">{{ $todayTimeInAppLabel ?? '—' }}</div><div class="l">Time in app (today)</div></div></div>
      </div>
    </div>
  </div>

  <div class="upp-sec2 uap-detail-sec2">
    <div class="uap-detail-col">
      <div class="uap-ws uap-ws--read uap-ws--detail uap-detail-panel" id="uap-work-sessions" data-uap-ws>
        <div class="uap-detail-panel__head">
          <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 gap-sm-3 w-100">
            <div class="d-flex align-items-start gap-2 flex-grow-1" style="min-width: min(100%, 10rem);">
              <span class="uap-card-icon uap-card-icon--soft flex-shrink-0 mt-1"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
              <div class="min-w-0">
                <span class="uap-detail-panel__title d-block">Work sessions</span>
                <span class="uap-detail-panel__sub">Sign in, sign out &amp; duration in this range</span>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body uap-card-body-inset uap-detail-panel__body p-3 p-lg-4 d-flex flex-column gap-2">
      @if(isset($workSessions) && $workSessions->isNotEmpty())
        <p class="uap-ws-range-total uap-ws-range-total--bar mb-0" role="status">
          <span class="uap-ws-range-total__n text-body fw-semibold">{{ number_format($workSessions->total()) }}</span>
          <span class="uap-ws-range-total__txt">work session(s) in this range</span>
        </p>
        <div class="uap-ws-panel uap-ws-panel--read p-0">
          <div class="uap-ws-table-scroll table-responsive">
            <table class="table uap-ws-sessions-table table-sm table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th scope="col" class="text-nowrap">S.No</th>
                  <th scope="col">Sign in</th>
                  <th scope="col">Sign out</th>
                  <th scope="col" class="text-nowrap">Duration</th>
                  <th scope="col" class="d-none d-md-table-cell">IP</th>
                </tr>
              </thead>
              <tbody>
                @foreach($workSessions as $s)
                  <tr>
                    <td class="text-nowrap uap-ws-sid text-muted">{{ ($workSessions->firstItem() ?? 1) + $loop->index }}</td>
                    <td>
                      <time datetime="{{ $s->started_at?->toIso8601String() }}">{{ $s->started_at?->format('M j, Y H:i') }}</time>
                    </td>
                    <td>
                      @if($s->ended_at)
                        <time datetime="{{ $s->ended_at->toIso8601String() }}">{{ $s->ended_at->format('M j, Y H:i') }}</time>
                      @else
                        <span class="badge uap-ws-open-badge">Open</span>
                      @endif
                    </td>
                    <td class="text-nowrap small">
                      <span
                        class="uap-ws-dur-txt @if($s->isOpen()) uap-ws-dur-ongoing @endif"
                        title="{{ $s->isOpen() ? 'Time since sign-in (session still open)' : 'Session duration' }}"
                      >{{ $s->displayWorkSessionSpan() }}</span>
                      @if($s->isOpen())
                        <span class="uap-ws-dur-hint">ongoing</span>
                      @endif
                    </td>
                    <td class="d-none d-md-table-cell small font-monospace uap-ws-ip">{{ $s->ip_address ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="uap-ws-pg-wrap uap-paginate mt-auto">
          @php
            $p = $workSessions;
            $tot = (int) $p->total();
            $cur = (int) $p->currentPage();
            $last = max(1, (int) $p->lastPage());
            $summaryNounOne = 'work session';
            $summaryNounMany = 'work sessions';
            $nounPhrase = $tot === 1 ? $summaryNounOne : $summaryNounMany;
            if ($last > 0 && $tot > 0) {
                if ($last <= 7) {
                    $pageRange = range(1, $last);
                } else {
                    $w = 2;
                    $pgFrom = max(1, $cur - $w);
                    $pgTo = min($last, $cur + $w);
                    if ($pgTo - $pgFrom < 4) {
                        if ($pgFrom === 1) {
                            $pgTo = min($last, $pgFrom + 4);
                        } else {
                            $pgFrom = max(1, $pgTo - 4);
                        }
                    }
                    $pageRange = range($pgFrom, $pgTo);
                }
            } else {
                $pageRange = [];
            }
          @endphp
          @if($tot > 0)
          <nav class="uap-pg-bar" aria-label="Table pagination" role="navigation">
            <p class="uap-pg-bar__summary">
              Showing
              <strong class="uap-pg-bar__num">{{ $p->firstItem() ?? 0 }}</strong>–<strong class="uap-pg-bar__num">{{ $p->lastItem() ?? 0 }}</strong>
              of
              <strong class="uap-pg-bar__num">{{ number_format($tot) }}</strong>
              {{ $nounPhrase }}
            </p>
            @if($p->hasPages())
            <div class="uap-pg-join" role="group" aria-label="Page navigation">
              @if($cur > 1)
              <a href="{{ $p->url(1) }}" class="uap-pg-join__link uap-pg-join__link--edge" rel="first" title="First page" aria-label="First page">«</a>
              <a href="{{ $p->previousPageUrl() }}" class="uap-pg-join__link uap-pg-join__link--edge" rel="prev" title="Previous page" aria-label="Previous page">‹</a>
              @else
              <span class="uap-pg-join__span uap-pg-join__span--edge is-disabled" aria-disabled="true" title="First page">«</span>
              <span class="uap-pg-join__span uap-pg-join__span--edge is-disabled" aria-disabled="true" title="Previous page">‹</span>
              @endif
              @foreach($pageRange as $i)
                @if($i === $cur)
                <span class="uap-pg-join__span is-active" aria-current="page">{{ $i }}</span>
                @else
                <a href="{{ $p->url($i) }}" class="uap-pg-join__link" aria-label="Page {{ $i }}">{{ $i }}</a>
                @endif
              @endforeach
              @if($p->hasMorePages())
              <a href="{{ $p->nextPageUrl() }}" class="uap-pg-join__link uap-pg-join__link--edge" rel="next" title="Next page" aria-label="Next page">›</a>
              <a href="{{ $p->url($last) }}" class="uap-pg-join__link uap-pg-join__link--edge" rel="last" title="Last page" aria-label="Last page">»</a>
              @else
              <span class="uap-pg-join__span uap-pg-join__span--edge is-disabled" aria-disabled="true" title="Next page">›</span>
              <span class="uap-pg-join__span uap-pg-join__span--edge is-disabled" aria-disabled="true" title="Last page">»</span>
              @endif
            </div>
            @endif
          </nav>
          @endif
        </div>
      @else
        <p class="uap-detail-empty text-muted small mb-0">No work sessions in this range. Widen the date range or check that sign-in activity is recorded for this user.</p>
      @endif
        </div>
      </div>
    </div>
    <div class="uap-detail-col">
      <div class="uap-actions-type uap-detail-panel d-flex flex-column h-100 mb-0">
        <div class="uap-detail-panel__head">
          <div class="d-flex align-items-start gap-2">
            <span class="uap-card-icon uap-card-icon--soft flex-shrink-0 mt-1"><i class="bi bi-diagram-3" aria-hidden="true"></i></span>
            <div class="min-w-0">
              <span class="uap-detail-panel__title d-block">Actions by type</span>
              <span class="uap-detail-panel__sub">This user &middot; selected range &middot; click a count to see events</span>
            </div>
          </div>
        </div>
        <div class="card-body uap-card-body-inset uap-actions-type__body uap-detail-panel__body p-3 p-lg-4 d-flex flex-column flex-grow-1 min-h-0">
        <div class="table-responsive flex-grow-1 uap-actions-type__table-wrap">
          <table class="table upp-side-tbl uap-side-table--detail table-hover align-middle mb-0">
            <thead>
              <tr>
                <th scope="col" class="uap-col-type">Type</th>
                <th scope="col" class="text-end uap-col-num">Events</th>
                <th scope="col" class="text-end uap-col-num">Rec. units</th>
              </tr>
            </thead>
            <tbody>
              @foreach($typeLabels as $key => $label)
                @php $brow = $byType->get($key); @endphp
                <tr>
                  <td class="uap-type-label">
                    <span class="upp-ab {{ $abClass($key) }}" title="{{ e($label) }}">{{ $actionLabel($key) }}</span>
                  </td>
                  <td class="text-end uap-type-num">
                    @if($brow && (int) $brow->n > 0)
                    <a href="#"
                      class="uap-type-events-link uap-type-events-link--count"
                      data-type-key="{{ $key }}"
                      data-type-label="{{ e($label) }}">{{ number_format($brow->n) }}</a>
                    @else
                    <span class="uap-type-zero">0</span>
                    @endif
                  </td>
                  <td class="text-end uap-type-num uap-type-num--rec">{{ $brow ? number_format($brow->records) : '0' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        </div>
      </div>
    </div>
  </div>

  <div class="upp-tbl-wrap uap-detail-logs-wrap mb-0">
    <div class="upp-tbl-top">
      <h2 class="m-0 d-flex flex-wrap align-items-baseline gap-2"><i class="bi bi-journal-richtext me-1" aria-hidden="true"></i> <span>Activity &amp; logs</span>
        <span class="text-muted" style="font-size:0.8rem;font-weight:600;">({{ $logs->total() }} in this page)</span>
      </h2>
    </div>
    <div class="table-responsive" style="max-height: 560px; overflow: auto;">
      <table class="upp-tbl">
        <thead>
          <tr>
            <th class="nw" style="width:44px;">#</th>
            <th class="nw" style="min-width:9rem;">Date &amp; time</th>
            <th style="min-width:9rem;">User</th>
            <th>Action</th>
            <th>Module</th>
            <th style="min-width:12rem;">Route / path</th>
            <th class="text-start upp-tcol-timing" style="min-width:4.5rem;">From (time)</th>
            <th class="text-start upp-tcol-timing" style="min-width:4.5rem;">To (time)</th>
            <th class="text-start upp-tcol-timing" style="min-width:6.5rem;">Time taken (total)</th>
          </tr>
        </thead>
        <tbody>
            @forelse($logs as $e)
            @php
            $lineIdentity = (trim((string) (data_get($e, 'user_fullname') ?? data_get($e, 'username') ?? '')) !== '')
            ? (object) ['user_fullname' => $e->user_fullname, 'username' => $e->username]
            : $row;
            $lineDisplay = $uapNameWithEmp($lineIdentity);
            $modLabel = $uap->resolveModuleLabelForLog($e->activity_module, $e->path, $e->route_name);
            $uapT = $uap->getLogTimeColumnValues($e);
            @endphp
            <tr>
              <td class="nw text-muted small">{{ ($logs->firstItem() ?? 0) + $loop->index }}</td>
              <td class="nw">{{ $e->created_at?->format('Y-m-d H:i:s') }}</td>
              <td class="upp-user-cell" style="min-width: 160px;">
                <div class="nm">{{ $lineDisplay }}</div>
              </td>
              <td><span class="upp-ab {{ $abClass((string) ($e->type ?? '')) }}">{{ $actionLabel((string) ($e->type ?? '')) }}</span></td>
              <td>
                <span class="upp-mod-chip @if($modLabel === 'Unassigned') upp-mod-na @endif" title="Stored: {{ $e->activity_module ?? '—' }}">{{ $modLabel }}</span>
              </td>
              <td style="min-width: 200px;">
                @if($e->route_name)
                  <div class="text-primary" style="font-size:0.8rem; font-weight: 600;">{{ $e->route_name }}</div>
                @endif
                <div class="text-muted" style="font-size:0.72rem; word-break: break-all;">{{ $e->path }}</div>
              </td>
              <td class="uap-time-frm uap-tcol text-nowrap" title="Estimated start of the measured window">{{ $uapT['from'] }}</td>
              <td class="uap-time-to uap-tcol text-nowrap" title="End of request (log time, time only)">{{ $uapT['to'] }}</td>
              <td class="uap-time-tot uap-tcol" title="{{ $uapT['title'] }}">{{ $uapT['total'] }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="9" class="uap-empty py-4">No log entries in this date range for this user.</td>
            </tr>
            @endforelse
        </tbody>
      </table>
    </div>
    @if($logs->total() > 0)
    <div class="uap-pg-wrap uap-detail-pg uap-paginate">
      @php
        $p = $logs->withQueryString();
        $tot = (int) $p->total();
        $cur = (int) $p->currentPage();
        $last = max(1, (int) $p->lastPage());
        $summaryNounOne = 'log entry';
        $summaryNounMany = 'log entries';
        $nounPhrase = $tot === 1 ? $summaryNounOne : $summaryNounMany;
        if ($last > 0 && $tot > 0) {
            if ($last <= 7) {
                $pageRange = range(1, $last);
            } else {
                $w = 2;
                $pgFrom = max(1, $cur - $w);
                $pgTo = min($last, $cur + $w);
                if ($pgTo - $pgFrom < 4) {
                    if ($pgFrom === 1) {
                        $pgTo = min($last, $pgFrom + 4);
                    } else {
                        $pgFrom = max(1, $pgTo - 4);
                    }
                }
                $pageRange = range($pgFrom, $pgTo);
            }
        } else {
            $pageRange = [];
        }
      @endphp
      @if($tot > 0)
      <nav class="uap-pg-bar" aria-label="Table pagination" role="navigation">
        <p class="uap-pg-bar__summary">
          Showing
          <strong class="uap-pg-bar__num">{{ $p->firstItem() ?? 0 }}</strong>–<strong class="uap-pg-bar__num">{{ $p->lastItem() ?? 0 }}</strong>
          of
          <strong class="uap-pg-bar__num">{{ number_format($tot) }}</strong>
          {{ $nounPhrase }}
        </p>
        @if($p->hasPages())
        <div class="uap-pg-join" role="group" aria-label="Page navigation">
          @if($cur > 1)
          <a href="{{ $p->url(1) }}" class="uap-pg-join__link uap-pg-join__link--edge" rel="first" title="First page" aria-label="First page">«</a>
          <a href="{{ $p->previousPageUrl() }}" class="uap-pg-join__link uap-pg-join__link--edge" rel="prev" title="Previous page" aria-label="Previous page">‹</a>
          @else
          <span class="uap-pg-join__span uap-pg-join__span--edge is-disabled" aria-disabled="true" title="First page">«</span>
          <span class="uap-pg-join__span uap-pg-join__span--edge is-disabled" aria-disabled="true" title="Previous page">‹</span>
          @endif
          @foreach($pageRange as $i)
            @if($i === $cur)
            <span class="uap-pg-join__span is-active" aria-current="page">{{ $i }}</span>
            @else
            <a href="{{ $p->url($i) }}" class="uap-pg-join__link" aria-label="Page {{ $i }}">{{ $i }}</a>
            @endif
          @endforeach
          @if($p->hasMorePages())
          <a href="{{ $p->nextPageUrl() }}" class="uap-pg-join__link uap-pg-join__link--edge" rel="next" title="Next page" aria-label="Next page">›</a>
          <a href="{{ $p->url($last) }}" class="uap-pg-join__link uap-pg-join__link--edge" rel="last" title="Last page" aria-label="Last page">»</a>
          @else
          <span class="uap-pg-join__span uap-pg-join__span--edge is-disabled" aria-disabled="true" title="Next page">›</span>
          <span class="uap-pg-join__span uap-pg-join__span--edge is-disabled" aria-disabled="true" title="Last page">»</span>
          @endif
        </div>
        @endif
      </nav>
      @endif
    </div>
    @endif
  </div>

  </div>
</div>
  </div>
</div>
{{-- Body-level: allows CSS blur of .pc-container / header / sidebar; backdrop-filter on .modal-backdrop alone is unreliable inside nested layout. --}}
<div class="modal fade uap-activity-detail-modal" id="uapActivityDetailModal" tabindex="-1" aria-labelledby="uapActivityDetailTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl uap-activity-fr-dialog">
    <div class="modal-content uap-activity-fr-modal uap-activity-detail-modal__content uap-adm uap-adm--v2">
      <div class="modal-header uap-activity-fr-modal__header uap-activity-detail-modal__header uap-adm__header">
        <h5 class="modal-title mb-0" id="uapActivityDetailTitle">
          <i class="bi bi-eye uap-activity-fr-modal__title-ico" aria-hidden="true"></i>
          <span id="uapActivityDetailHeading">Activity by type</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body uap-activity-fr-modal__body uap-activity-detail-modal__body uap-adm__body">
        <div class="uap-adm-filter" id="uapActivityDetailRange" aria-live="polite">
          <div class="uap-adm-filter__ico" aria-hidden="true"><i class="bi bi-calendar3"></i></div>
          <div class="uap-adm-filter__text">
            <span class="uap-adm-filter__label">Reporting period</span>
            <span class="uap-adm-filter__value uap-adm-filter__value--range" id="uapActivityDetailRangeText"></span>
          </div>
        </div>
        <p class="uap-adm-trunc alert alert-warning d-none small mt-2 mb-0" id="uapActivityDetailTruncated" role="status">
          Showing the first 500 of more events. Narrow the date range to see the rest.
        </p>
        <div class="d-none uap-adm-loading text-center py-5" id="uapActivityDetailLoading" role="status" aria-live="polite">
          <div class="uap-adm-loading__spinner" role="status" aria-label="Loading"></div>
          <p class="mt-3 uap-adm-loading__txt mb-0">Loading events…</p>
        </div>
        <p class="d-none uap-adm-err text-danger small mt-2 mb-0" id="uapActivityDetailError" role="alert"></p>
        <div class="d-none uap-adm-events-wrap" id="uapActivityDetailEventsSection">
          <div class="uap-adm-panel" id="uapActivityDetailTableWrap">
            <div class="uap-adm-panel__head">
              <i class="bi bi-table" aria-hidden="true"></i>
              <span>Event log for this type</span>
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
                <tbody id="uapActivityDetailTbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@include('superadmin.superadminfooter')
<script>
  (function() {
    if (typeof jQuery === 'undefined') return;
    var uapFrom = @json($from);
    var uapTo = @json($to);
    var uapUrlT = @json(route('user_activity.user.type_events', ['user' => $userId, 'typeKey' => '__TKEY__']));
    jQuery(document).on('click', 'a.uap-type-events-link', function(e) {
      e.preventDefault();
      var tkey = jQuery(this).data('type-key');
      if (!tkey) return;
      var $m = jQuery('#uapActivityDetailModal');
      var label = jQuery(this).data('type-label') || tkey;
      var url = uapUrlT.replace('__TKEY__', encodeURIComponent(tkey)) + '?' + jQuery.param({
        from: uapFrom,
        to: uapTo
      });
      $m.find('#uapActivityDetailHeading').text(label);
      $m.find('#uapActivityDetailRangeText').text(uapFrom + ' \u2192 ' + uapTo);
      $m.find('#uapActivityDetailTbody').empty();
      $m.find('#uapActivityDetailError').addClass('d-none').text('');
      $m.find('#uapActivityDetailTruncated').addClass('d-none');
      $m.find('#uapActivityDetailEventsSection').addClass('d-none');
      $m.find('#uapActivityDetailLoading').removeClass('d-none');
      if (window.bootstrap && window.bootstrap.Modal) {
        var el = $m[0];
        var bsm = window.bootstrap.Modal.getOrCreateInstance ?
          window.bootstrap.Modal.getOrCreateInstance(el) :
          new window.bootstrap.Modal(el);
        bsm.show();
      }
      fetch(url, {
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json'
          }
        })
        .then(function(r) {
          if (!r.ok) throw new Error('Request failed');
          return r.json();
        })
        .then(function(data) {
          if (data.type_label) {
            $m.find('#uapActivityDetailHeading').text(data.type_label);
          }
          $m.find('#uapActivityDetailLoading').addClass('d-none');
          $m.find('#uapActivityDetailEventsSection').removeClass('d-none');
          if (data.truncated) {
            $m.find('#uapActivityDetailTruncated').removeClass('d-none');
          }
          var $tb = $m.find('#uapActivityDetailTbody');
          (data.items || []).forEach(function(row) {
            if (typeof window.uapEventLogBuildRow === 'function') {
              $tb.append(window.uapEventLogBuildRow(row));
            } else {
              $tb.append(
                jQuery('<tr>').append(
                  jQuery('<td class="uap-activity-td uap-activity-td--module">').text(row.module_label || '—'),
                  jQuery('<td class="uap-activity-td uap-activity-td--ref">').text(row.reference || '—'),
                  jQuery('<td class="uap-activity-td uap-activity-td--date text-nowrap">').text(row.date || '—'),
                  jQuery('<td class="uap-activity-td uap-activity-td--time text-nowrap">').text(row.start_time || '—'),
                  jQuery('<td class="uap-activity-td uap-activity-td--time text-nowrap">').text(row.end_time || '—'),
                  jQuery('<td class="uap-activity-td--total">').text(row.total || '—')
                )
              );
            }
          });
          if (!data.items || !data.items.length) {
            $tb.append('<tr><td colspan="6" class="text-center text-muted py-4 uap-evt-log-empty">No log entries in this range for this type.</td></tr>');
          }
        })
        .catch(function() {
          $m.find('#uapActivityDetailLoading').addClass('d-none');
          $m.find('#uapActivityDetailEventsSection').addClass('d-none');
          if (window.toastr) toastr.error('Failed to load activity', 'Error');
          $m.find('#uapActivityDetailError').removeClass('d-none').text('Could not load the list. Try again.');
        });
    });
  })();

  /* Work sessions: Read vs Premium (persisted) */
  (function() {
    var root = document.getElementById('uap-work-sessions');
    if (!root) return;
    var KEY = 'uap_work_sessions_ui';
    var radios = root.querySelectorAll('input[name="uapWsMode"]');

    function applyMode(mode) {
      root.classList.remove('uap-ws--read', 'uap-ws--premium');
      root.classList.add(mode === 'premium' ? 'uap-ws--premium' : 'uap-ws--read');
      try {
        localStorage.setItem(KEY, mode);
      } catch (e) {}
    }
    var saved = null;
    try {
      saved = localStorage.getItem(KEY);
    } catch (e) {}
    if (saved === 'premium' || saved === 'read') {
      var r = root.querySelector('input[name="uapWsMode"][value="' + saved + '"]');
      if (r) {
        r.checked = true;
      }
      applyMode(saved);
    } else {
      applyMode('read');
    }
    radios.forEach(function(radio) {
      radio.addEventListener('change', function() {
        if (radio.checked) {
          applyMode(radio.value);
        }
      });
    });
  })();
</script>
</body>
</html>
