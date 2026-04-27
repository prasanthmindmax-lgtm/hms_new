<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use App\Models\UserActivitySession;
use App\Models\usermanagementdetails;
use App\Services\UserActivity\UserActivityService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        $admin = auth()->user();
        $from = $request->input('from', Carbon::now()->startOfDay()->toDateString());
        $to = $request->input('to', Carbon::now()->endOfDay()->toDateString());
        $userId = $request->input('user_id') ? (int) $request->input('user_id') : null;

        $fromDt = Carbon::parse($from)->startOfDay();
        $toDt = Carbon::parse($to)->endOfDay();

        $ui = $request->query('ui', 'compact');
        if (! in_array($ui, ['compact', 'activity'], true)) {
            $ui = 'compact';
        }

        $uap = app(UserActivityService::class);
        $eventsQ = function () use ($fromDt, $toDt, $userId, $uap) {
            $q = $uap->withoutClientTabEventRows(UserActivityLog::query())
                ->whereBetween('created_at', [$fromDt, $toDt]);
            if ($userId) {
                $q->where('user_id', $userId);
            }

            return $q;
        };

        $logsForModule = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->get(['activity_module', 'type', 'records_count', 'path', 'route_name']);
        $byModule = $uap->aggregateModuleBreakdown($logsForModule);

        $perUser = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->select('user_id', DB::raw('COUNT(*) as events'), DB::raw('COALESCE(SUM(records_count),0) as record_units'))
            ->groupBy('user_id')
            ->orderByDesc('events')
            ->limit(20)
            ->get();

        $userIds = $perUser->pluck('user_id')->unique()->filter();
        $perUserById = $userIds->isEmpty()
            ? collect()
            : usermanagementdetails::query()
                ->select($this->managementUserColumns())
                ->whereIn('id', $userIds)
                ->get()
                ->keyBy('id');

        $perUserWithLabels = $perUser->map(function ($r) use ($perUserById) {
            $uid = (int) $r->user_id;
            $u = $perUserById->get($uid);
            if ($u) {
                $display = trim((string) ($u->user_fullname ?? $u->name ?? ''));
                $r->user_label = $u->username ? (string) $u->username : ('#'.$uid);
                $r->user_name = $display !== '' ? $display : $r->user_label;
            } else {
                $r->user_label = '#'.$uid;
                $r->user_name = null;
            }

            return $r;
        });

        $qForFilter = usermanagementdetails::query()->select($this->managementUserColumns());
        $this->orderManagementUsers($qForFilter);
        $usersForFilter = $qForFilter->get();

        if ($ui === 'activity') {
            $statQ = function () use ($userId, $uap) {
                $q = $uap->withoutClientTabEventRows(UserActivityLog::query());
                if ($userId) {
                    $q->where('user_id', $userId);
                }

                return $q;
            };
            $activityStats = [
                'in_range' => (int) $eventsQ()->count(),
                'today' => (int) $statQ()->whereDate('created_at', Carbon::today())->count(),
                'week' => (int) $statQ()->where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
                'last_30' => (int) $statQ()->where('created_at', '>=', Carbon::now()->subDays(30)->startOfDay())->count(),
                'active_users' => (int) $uap->withoutClientTabEventRows(UserActivityLog::query())
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->when($userId, fn ($q) => $q->where('user_id', $userId))
                    ->distinct()
                    ->count('user_id'),
            ];

            $recentFlat = $uap->withoutClientTabEventRows(UserActivityLog::query())
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->when($userId, fn ($q) => $q->where('user_id', $userId))
                ->orderByDesc('id')
                ->limit(200)
                ->get();

            $recentUserIds = $recentFlat->pluck('user_id')->unique()->filter();
            $userLookup = $recentUserIds->isEmpty()
                ? collect()
                : usermanagementdetails::query()
                    ->select($this->managementUserColumns())
                    ->whereIn('id', $recentUserIds)
                    ->get()
                    ->keyBy('id');

            return view('superadmin.user_activity.dashboard_activity', [
                'admin' => $admin,
                'from' => $from,
                'to' => $to,
                'userId' => $userId,
                'activityStats' => $activityStats,
                'recentFlat' => $recentFlat,
                'userLookup' => $userLookup,
                'byModule' => $byModule,
                'perUserWithLabels' => $perUserWithLabels,
                'usersForFilter' => $usersForFilter,
            ]);
        }

        $sessionBuilder = function () use ($fromDt, $toDt, $userId) {
            $q = UserActivitySession::query()
                ->whereBetween('started_at', [$fromDt, $toDt]);
            if ($userId) {
                $q->where('user_id', $userId);
            }

            return $q;
        };

        $openCount = (int) $sessionBuilder()->whereNull('ended_at')->count();

        $logins = (int) $eventsQ()->where('type', UserActivityService::T_LOGIN)->count();
        $logouts = (int) $eventsQ()->where('type', UserActivityService::T_LOGOUT)->count();

        $totalWrites = (int) $eventsQ()
            ->whereIn('type', [
                UserActivityService::T_CREATE,
                UserActivityService::T_UPDATE,
                UserActivityService::T_DELETE,
            ])
            ->sum('records_count');

        $totalReads = (int) $eventsQ()->where('type', UserActivityService::T_READ)->count();

        $userStatsInRange = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->select('user_id', DB::raw('COUNT(*) as event_count'), DB::raw('MAX(created_at) as last_at'))
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $perPage = (int) $request->input('per_page', 25);
        $perPage = min(100, max(5, $perPage));

        $usersQ = usermanagementdetails::query()->select($this->managementUserColumns());
        if ($userId) {
            $usersQ->where('id', $userId);
        }
        $this->orderManagementUsers($usersQ);
        $users = $usersQ->paginate($perPage)->withQueryString();

        return view('superadmin.user_activity.dashboard', compact(
            'admin',
            'from',
            'to',
            'userId',
            'logins',
            'logouts',
            'byModule',
            'openCount',
            'totalWrites',
            'totalReads',
            'perUserWithLabels',
            'userStatsInRange',
            'users',
            'usersForFilter'
        ));
    }

    public function userShow(Request $request, usermanagementdetails $user)
    {
        $admin = auth()->user();
        $from = $request->input('from', Carbon::now()->startOfDay()->toDateString());
        $to = $request->input('to', Carbon::now()->endOfDay()->toDateString());
        $fromDt = Carbon::parse($from)->startOfDay();
        $toDt = Carbon::parse($to)->endOfDay();

        $row = $user;
        $uap = app(UserActivityService::class);

        $logs = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $logins = (int) $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->where('type', UserActivityService::T_LOGIN)
            ->count();
        $logouts = (int) $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->where('type', UserActivityService::T_LOGOUT)
            ->count();

        $typeLabels = [
            UserActivityService::T_LOGIN => 'Logins (events)',
            UserActivityService::T_LOGOUT => 'Logouts (events)',
            UserActivityService::T_READ => 'Read / view (GET)',
            UserActivityService::T_CREATE => 'Create (POST)',
            UserActivityService::T_UPDATE => 'Update (POST/PUT/PATCH)',
            UserActivityService::T_DELETE => 'Delete',
            UserActivityService::T_OTHER => 'Other',
        ];
        $byType = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->selectRaw('type, COUNT(*) as n, COALESCE(SUM(records_count),0) as records')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $createFormTiming = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->where('type', UserActivityService::T_CREATE)
            ->whereNotNull('action_duration_ms')
            ->selectRaw('COUNT(*) as n, AVG(action_duration_ms) as avg_ms')
            ->first();

        $logQ = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$fromDt, $toDt]);

        $logTimeTotals = [
            'n_events' => (int) (clone $logQ)->count(),
            'n_with_server' => (int) (clone $logQ)->whereNotNull('server_duration_ms')->count(),
            'sum_server_ms' => (int) (clone $logQ)->whereNotNull('server_duration_ms')->sum('server_duration_ms'),
            'n_with_action' => (int) (clone $logQ)->whereNotNull('action_duration_ms')->count(),
            'sum_action_ms' => (int) (clone $logQ)->whereNotNull('action_duration_ms')->sum('action_duration_ms'),
        ];

        $dashboardQuery = array_filter([
            'from' => $from,
            'to' => $to,
        ], fn ($v) => $v !== null && $v !== '');

        $wsPerPage = (int) config('user_activity.work_sessions_per_page', 25);
        $wsPerPage = min(100, max(10, $wsPerPage));

        $workSessions = UserActivitySession::query()
            ->where('user_id', $user->id)
            ->where(function ($q) use ($fromDt, $toDt) {
                $q->whereBetween('started_at', [$fromDt, $toDt])
                    ->orWhere(function ($q2) use ($fromDt, $toDt) {
                        $q2->whereNotNull('ended_at')
                            ->whereBetween('ended_at', [$fromDt, $toDt]);
                    })
                    ->orWhere(function ($q2) use ($fromDt, $toDt) {
                        $q2->where('started_at', '<=', $toDt)
                            ->where(function ($q3) use ($fromDt) {
                                $q3->whereNull('ended_at')
                                    ->orWhere('ended_at', '>=', $fromDt);
                            });
                    });
            })
            ->orderByDesc('started_at')
            ->paginate($wsPerPage)
            ->withQueryString();

        $todayTimeInAppSeconds = $uap->sumTodayWorkSessionOverlapSecondsForUser((int) $user->id);
        $todayTimeInAppLabel = $todayTimeInAppSeconds === 0
            ? '0s'
            : $uap->formatDurationMs($todayTimeInAppSeconds * 1000);

        return view('superadmin.user_activity.user_show', [
            'admin' => $admin,
            'from' => $from,
            'to' => $to,
            'row' => $row,
            'userId' => (int) $user->id,
            'logs' => $logs,
            'logins' => $logins,
            'logouts' => $logouts,
            'byType' => $byType,
            'typeLabels' => $typeLabels,
            'dashboardQuery' => $dashboardQuery,
            'createFormTiming' => $createFormTiming,
            'logTimeTotals' => $logTimeTotals,
            'workSessions' => $workSessions,
            'todayTimeInAppLabel' => $todayTimeInAppLabel,
            'todayTimeInAppSeconds' => $todayTimeInAppSeconds,
        ]);
    }

    public function storeClientEvent(Request $request, UserActivityService $uap)
    {
        if (! (bool) config('user_activity.client_tab_events', false)) {
            return response()->json(['ok' => true, 'skipped' => true]);
        }
        $v = $request->validate([
            'kind' => 'required|in:tab_hidden,tab_visible,page_hide',
            'path' => 'nullable|string|max:512',
        ]);
        $uap->logClientTabEvent(
            $request,
            (string) $v['kind'],
            isset($v['path']) ? (string) $v['path'] : null
        );

        return response()->json(['ok' => true]);
    }

    /**
     * sendBeacon/keepalive when the user leaves without clicking Logout — records {@see UserActivityService::T_LOGOUT} and ends work session.
     */
    public function beaconSessionEnd(Request $request, UserActivityService $uap): \Illuminate\Http\Response
    {
        if (! (bool) config('user_activity.beacon_logout_on_unload', true)) {
            return response()->noContent();
        }
        $user = $request->user();
        if (! $user) {
            return response()->noContent();
        }
        $id = (int) $user->getAuthIdentifier();
        if ($id < 1) {
            return response()->noContent();
        }
        $uap->onLogout($id, 'unload');

        return response()->noContent();
    }

    /**
     * Events in range whose resolved module label matches the "By module" table row (newest first, cap 500).
     */
    public function moduleEventsInRange(Request $request, UserActivityService $uap)
    {
        $v = $request->validate([
            'module' => 'required|string|max:500',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'user_id' => 'nullable|integer|min:1',
        ]);

        $from = $v['from'] ?? Carbon::now()->startOfDay()->toDateString();
        $to = $v['to'] ?? Carbon::now()->endOfDay()->toDateString();
        $fromDt = Carbon::parse($from)->startOfDay();
        $toDt = Carbon::parse($to)->endOfDay();
        $userId = ! empty($v['user_id']) ? (int) $v['user_id'] : null;
        $moduleLabel = $v['module'];

        $scanLimit = 15000;
        $max = 500;

        $q = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->when($userId, fn (Builder $qb) => $qb->where('user_id', $userId))
            ->orderByDesc('id');

        $rows = (clone $q)->limit($scanLimit)->get();
        $itemsModels = collect();
        $totalInScan = 0;
        foreach ($rows as $e) {
            $label = $uap->resolveModuleLabelForLog(
                $e->activity_module ? (string) $e->activity_module : null,
                $e->path ? (string) $e->path : null,
                $e->route_name ? (string) $e->route_name : null
            );
            if ($label !== $moduleLabel) {
                continue;
            }
            $totalInScan++;
            if ($itemsModels->count() < $max) {
                $itemsModels->push($e);
            }
        }
        $truncated = $totalInScan > $max;
        $scanCapped = $rows->count() >= $scanLimit;

        $items = $itemsModels->map(fn ($e) => $uap->getLogTimeDetailForApi($e))->values()->all();

        return response()->json([
            'module' => $moduleLabel,
            'type_label' => $moduleLabel,
            'from' => $fromDt->toDateString(),
            'to' => $toDt->toDateString(),
            'user_id' => $userId,
            'count' => $totalInScan,
            'truncated' => $truncated,
            'scan_capped' => $scanCapped,
            'items' => $items,
        ]);
    }

    public function typeEventsByType(Request $request, usermanagementdetails $user, string $typeKey)
    {
        $typeLabels = $this->typeKeyToLabelMap();
        if (! array_key_exists($typeKey, $typeLabels)) {
            abort(404);
        }
        $from = $request->input('from', Carbon::now()->startOfDay()->toDateString());
        $to = $request->input('to', Carbon::now()->endOfDay()->toDateString());
        $fromDt = Carbon::parse($from)->startOfDay();
        $toDt = Carbon::parse($to)->endOfDay();

        $max = 500;
        $q = app(UserActivityService::class)->withoutClientTabEventRows(UserActivityLog::query())
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->when($typeKey !== 'all', fn (Builder $qb) => $qb->where('type', $typeKey))
            ->orderByDesc('id');
        $total = (int) (clone $q)->count();
        $rows = (clone $q)->limit($max + 1)->get();
        $truncated = $rows->count() > $max;
        if ($truncated) {
            $rows = $rows->take($max);
        }

        $uap = app(UserActivityService::class);
        $items = $rows->map(function (UserActivityLog $e) use ($uap) {
            $d = $uap->getLogTimeDetailForApi($e);

            return array_merge($d, [
                'log_type' => (string) $e->type,
                'log_type_label' => $this->logEventTypeShortLabel((string) $e->type),
                'http_method' => (string) ($e->http_method ?? ''),
                'records' => (int) ($e->records_count ?? 0),
            ]);
        })->values()->all();

        return response()->json([
            'type' => $typeKey,
            'type_label' => $typeLabels[$typeKey] ?? $typeKey,
            'from' => $fromDt->toDateString(),
            'to' => $toDt->toDateString(),
            'user_id' => (int) $user->id,
            'count' => $total,
            'truncated' => $truncated,
            'items' => $items,
        ]);
    }

    /**
     * @return array<string, string> type key (URL) => label
     */
    private function typeKeyToLabelMap(): array
    {
        return [
            'all' => 'All log entries (this range)',
            UserActivityService::T_LOGIN => 'Logins (events)',
            UserActivityService::T_LOGOUT => 'Logouts (events)',
            UserActivityService::T_READ => 'Read / view (GET)',
            UserActivityService::T_CREATE => 'Create (POST)',
            UserActivityService::T_UPDATE => 'Update (POST/PUT/PATCH)',
            UserActivityService::T_DELETE => 'Delete',
            UserActivityService::T_OTHER => 'Other',
        ];
    }

    public function authEventsInRange(Request $request, string $typeKey)
    {
        if (! in_array($typeKey, [UserActivityService::T_LOGIN, UserActivityService::T_LOGOUT], true)) {
            abort(404);
        }

        $from = $request->input('from', Carbon::now()->startOfDay()->toDateString());
        $to = $request->input('to', Carbon::now()->endOfDay()->toDateString());
        $userId = $request->input('user_id') ? (int) $request->input('user_id') : null;
        $fromDt = Carbon::parse($from)->startOfDay();
        $toDt = Carbon::parse($to)->endOfDay();

        $uap = app(UserActivityService::class);
        $max = 500;

        $q = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->where('type', $typeKey)
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->when($userId, fn (Builder $qb) => $qb->where('user_id', $userId))
            ->orderByDesc('id');

        $total = (int) (clone $q)->count();
        $rows = (clone $q)->limit($max + 1)->get(['id', 'user_id', 'created_at']);
        $truncated = $rows->count() > $max;
        if ($truncated) {
            $rows = $rows->take($max);
        }

        $uids = $rows->pluck('user_id')->unique()->filter();
        $usersById = $uids->isEmpty()
            ? collect()
            : usermanagementdetails::query()
                ->select($this->managementUserColumns())
                ->whereIn('id', $uids)
                ->get()
                ->keyBy('id');

        $label = $typeKey === UserActivityService::T_LOGOUT ? 'Logouts' : 'Logins';
        $items = $rows->map(function ($e) use ($usersById) {
            $uid = (int) $e->user_id;
            $at = $e->created_at;
            if ($at === null) {
                return [
                    'user_id' => $uid,
                    'user_name' => 'User #'.$uid,
                    'date' => '—',
                    'time' => '—',
                ];
            }
            $a = $at instanceof Carbon ? $at->copy() : Carbon::parse($at);

            return [
                'user_id' => $uid,
                'user_name' => $this->formatManagementUserLabel($usersById->get($uid), $uid),
                'date' => $a->format('Y-m-d'),
                'time' => $a->format('H:i:s'),
            ];
        })->values()->all();

        return response()->json([
            'type' => $typeKey,
            'type_label' => $label,
            'from' => $fromDt->toDateString(),
            'to' => $toDt->toDateString(),
            'user_id' => $userId,
            'count' => $total,
            'truncated' => $truncated,
            'items' => $items,
        ]);
    }

    /**
     * Open work sessions: started in range, no end time, same rules as the dashboard KPI.
     */
    public function openSessionsInRange(Request $request)
    {
        $from = $request->input('from', Carbon::now()->startOfDay()->toDateString());
        $to = $request->input('to', Carbon::now()->endOfDay()->toDateString());
        $userId = $request->input('user_id') ? (int) $request->input('user_id') : null;
        $fromDt = Carbon::parse($from)->startOfDay();
        $toDt = Carbon::parse($to)->endOfDay();

        $max = 500;
        $q = UserActivitySession::query()
            ->whereNull('ended_at')
            ->whereBetween('started_at', [$fromDt, $toDt])
            ->when($userId, fn (Builder $qb) => $qb->where('user_id', $userId))
            ->orderByDesc('started_at');

        $total = (int) (clone $q)->count();
        $rows = (clone $q)->limit($max + 1)->get(['id', 'user_id', 'started_at', 'last_seen_at']);
        $truncated = $rows->count() > $max;
        if ($truncated) {
            $rows = $rows->take($max);
        }

        $uids = $rows->pluck('user_id')->unique()->filter();
        $usersById = $uids->isEmpty()
            ? collect()
            : usermanagementdetails::query()
                ->select($this->managementUserColumns())
                ->whereIn('id', $uids)
                ->get()
                ->keyBy('id');

        $items = $rows->map(function ($e) use ($usersById) {
            $uid = (int) $e->user_id;
            $s = $e->started_at;
            $l = $e->last_seen_at;
            if ($s === null) {
                $startDate = '—';
                $startTime = '—';
            } else {
                $c = $s instanceof Carbon ? $s->copy() : Carbon::parse($s);
                $startDate = $c->format('Y-m-d');
                $startTime = $c->format('H:i:s');
            }
            if ($l === null) {
                $lastSeen = '—';
            } else {
                $lc = $l instanceof Carbon ? $l->copy() : Carbon::parse($l);
                $lastSeen = $lc->format('Y-m-d H:i:s');
            }

            return [
                'user_id' => $uid,
                'user_name' => $this->formatManagementUserLabel($usersById->get($uid), $uid),
                'date' => $startDate,
                'time' => $startTime,
                'last_seen' => $lastSeen,
            ];
        })->values()->all();

        return response()->json([
            'type' => 'open_sessions',
            'type_label' => 'Open sessions',
            'from' => $fromDt->toDateString(),
            'to' => $toDt->toDateString(),
            'user_id' => $userId,
            'count' => $total,
            'truncated' => $truncated,
            'items' => $items,
        ]);
    }

    /**
     * Read (GET) or write (C/U/D) log rows for the dashboard KPI (all users, optional user filter), max 500.
     */
    public function kpiActivityEventsInRange(Request $request, string $typeKey)
    {
        if (! in_array($typeKey, ['read', 'write'], true)) {
            abort(404);
        }

        $from = $request->input('from', Carbon::now()->startOfDay()->toDateString());
        $to = $request->input('to', Carbon::now()->endOfDay()->toDateString());
        $userId = $request->input('user_id') ? (int) $request->input('user_id') : null;
        $fromDt = Carbon::parse($from)->startOfDay();
        $toDt = Carbon::parse($to)->endOfDay();

        $uap = app(UserActivityService::class);
        $max = 500;

        $q = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->when($userId, fn (Builder $qb) => $qb->where('user_id', $userId));

        if ($typeKey === 'read') {
            $q->where('type', UserActivityService::T_READ);
        } else {
            $q->whereIn('type', [
                UserActivityService::T_CREATE,
                UserActivityService::T_UPDATE,
                UserActivityService::T_DELETE,
            ]);
        }
        $q->orderByDesc('id');

        $total = (int) (clone $q)->count();
        $rows = (clone $q)->limit($max + 1)->get();
        $truncated = $rows->count() > $max;
        if ($truncated) {
            $rows = $rows->take($max);
        }

        $uids = $rows->pluck('user_id')->unique()->filter();
        $usersById = $uids->isEmpty()
            ? collect()
            : usermanagementdetails::query()
                ->select($this->managementUserColumns())
                ->whereIn('id', $uids)
                ->get()
                ->keyBy('id');

        $typeLabel = $typeKey === 'read'
            ? 'Read / view (GET)'
            : 'Writes (create, update, delete)';

        $kpiNote = $typeKey === 'write'
            ? 'The KPI above sums record units across C/U/D; the table lists each log row and its unit count where applicable.'
            : null;

        $items = $rows->map(function (UserActivityLog $e) use ($uap, $usersById) {
            $d = $uap->getLogTimeDetailForApi($e);
            $uid = (int) $e->user_id;

            return array_merge($d, [
                'user_id' => $uid,
                'user_name' => $this->formatManagementUserLabel($usersById->get($uid), $uid),
                'type' => (string) $e->type,
                'type_label' => $this->kpiEventTypeLabel((string) $e->type),
                'records' => (int) ($e->records_count ?? 0),
            ]);
        })->values()->all();

        return response()->json([
            'type' => $typeKey,
            'type_label' => $typeLabel,
            'kpi_note' => $kpiNote,
            'from' => $fromDt->toDateString(),
            'to' => $toDt->toDateString(),
            'user_id' => $userId,
            'count' => $total,
            'truncated' => $truncated,
            'items' => $items,
        ]);
    }

    /**
     * Full “All users” list for the date range and optional user filter (up to 2k rows) — for dashboard modal.
     */
    public function allUsersInRangeList(Request $request)
    {
        $from = $request->input('from', Carbon::now()->startOfDay()->toDateString());
        $to = $request->input('to', Carbon::now()->endOfDay()->toDateString());
        $userId = $request->input('user_id') ? (int) $request->input('user_id') : null;
        $fromDt = Carbon::parse($from)->startOfDay();
        $toDt = Carbon::parse($to)->endOfDay();

        $uap = app(UserActivityService::class);
        $userStatsInRange = $uap->withoutClientTabEventRows(UserActivityLog::query())
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->when($userId, fn (Builder $qb) => $qb->where('user_id', $userId))
            ->select('user_id', DB::raw('COUNT(*) as event_count'), DB::raw('MAX(created_at) as last_at'))
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $usersQ = usermanagementdetails::query()->select($this->managementUserColumns());
        if ($userId) {
            $usersQ->where('id', $userId);
        }
        $this->orderManagementUsers($usersQ);
        $totalInDirectory = (int) (clone $usersQ)->count();

        $max = 2000;
        $rows = (clone $usersQ)->limit($max + 1)->get();
        $truncated = $rows->count() > $max;
        if ($truncated) {
            $rows = $rows->take($max);
        }

        $query = array_filter([
            'from' => $from,
            'to' => $to,
            'ui' => $request->query('ui'),
        ], fn ($v) => $v !== null && $v !== '');

        $items = $rows->map(function (usermanagementdetails $u) use ($userStatsInRange, $query) {
            $st = $userStatsInRange->get($u->id);
            $lastRaw = $st?->last_at;
            if ($lastRaw && ! $lastRaw instanceof Carbon) {
                $lastC = Carbon::parse($lastRaw);
            } else {
                $lastC = $lastRaw instanceof Carbon ? $lastRaw->copy() : null;
            }
            $uid = (int) $u->id;
            $url = route('user_activity.user', ['user' => $uid]);
            if ($query !== []) {
                $url .= '?'.http_build_query($query);
            }

            return [
                'id' => $uid,
                'name' => $this->formatManagementUserLabel($u, $uid),
                'event_count' => (int) ($st?->event_count ?? 0),
                'last_at' => $lastC?->toIso8601String(),
                'last_at_display' => $lastC?->format('Y-m-d H:i:s') ?? '—',
                'detail_url' => $url,
            ];
        })->values()->all();

        return response()->json([
            'type' => 'all_users',
            'type_label' => 'All users in range',
            'from' => $fromDt->toDateString(),
            'to' => $toDt->toDateString(),
            'user_id' => $userId,
            'total_users' => $totalInDirectory,
            'truncated' => $truncated,
            'items' => $items,
        ]);
    }

    /**
     * Single JSON entry for dashboard drill-down modals. Query: panel=auth|activity|open|all_users|module, plus
     * typeKey (for auth, activity), module (for module), and from, to, user_id, ui as used by the underlying handlers.
     */
    public function dashboardData(Request $request, UserActivityService $uap)
    {
        $request->validate([
            'panel' => 'required|in:auth,activity,open,all_users,module',
        ], [
            'panel.required' => 'A panel type is required.',
        ]);

        return match ($request->string('panel')->toString()) {
            'auth' => $this->authEventsInRange($request, (string) $request->query('typeKey', '')),
            'activity' => $this->kpiActivityEventsInRange($request, (string) $request->query('typeKey', '')),
            'open' => $this->openSessionsInRange($request),
            'all_users' => $this->allUsersInRangeList($request),
            'module' => $this->moduleEventsInRange($request, $uap),
            default => abort(404),
        };
    }

    private function kpiEventTypeLabel(string $t): string
    {
        return match ($t) {
            UserActivityService::T_READ => 'Read',
            UserActivityService::T_CREATE => 'Create',
            UserActivityService::T_UPDATE => 'Update',
            UserActivityService::T_DELETE => 'Delete',
            default => $t,
        };
    }

    private function logEventTypeShortLabel(string $t): string
    {
        return match ($t) {
            UserActivityService::T_LOGIN => 'Login',
            UserActivityService::T_LOGOUT => 'Logout',
            UserActivityService::T_READ => 'Read',
            UserActivityService::T_CREATE => 'Create',
            UserActivityService::T_UPDATE => 'Update',
            UserActivityService::T_DELETE => 'Delete',
            UserActivityService::T_OTHER => 'Other',
            default => $t,
        };
    }

    /**
     * @param  usermanagementdetails|object|null  $u
     */
    private function formatManagementUserLabel($u, int $userId): string
    {
        if (! $u) {
            return 'User #'.$userId;
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
        if ($full !== '') {
            return $full;
        }
        if ($login !== '') {
            return $login;
        }

        return 'User #'.$userId;
    }

    private function managementUserColumns(): array
    {
        $t = (new usermanagementdetails)->getTable();
        $cols = ['id', 'username', 'user_fullname'];
        if (Schema::hasColumn($t, 'name')) {
            $cols[] = 'name';
        }
        if (Schema::hasColumn($t, 'email')) {
            $cols[] = 'email';
        }
        if (Schema::hasColumn($t, 'employee_id')) {
            $cols[] = 'employee_id';
        }

        return $cols;
    }

    private function orderManagementUsers(Builder $q): void
    {
        $t = (new usermanagementdetails)->getTable();
        if (Schema::hasColumn($t, 'user_fullname')) {
            $q->orderBy("{$t}.user_fullname");
        }
        $q->orderBy("{$t}.username");
        $q->orderBy("{$t}.id");
    }
}
