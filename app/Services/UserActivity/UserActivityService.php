<?php

namespace App\Services\UserActivity;

use App\Models\usermanagementdetails;
use App\Models\UserActivityLog;
use App\Models\UserActivitySession;
use App\Support\CreateFormDuration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class UserActivityService
{
    /**
     * @var array<int, object|null>
     */
    private static array $snapshotUserById = [];

    public const T_LOGIN = 'login';

    public const T_LOGOUT = 'logout';

    public const T_READ = 'read';

    public const T_CREATE = 'create';

    public const T_UPDATE = 'update';

    public const T_DELETE = 'delete';

    public const T_OTHER = 'other';

    /**
     * Set on the request as an array; merged into activity log {@see requestSnapshotForActivityLog}
     * after the controller runs (e.g. bill id from save). Key: "uap.activity_snapshot".
     */
    public const ACTIVITY_SNAPSHOT_REQUEST_ATTR = 'uap.activity_snapshot';

    private function config(string $key, mixed $default = null): mixed
    {
        return config('user_activity.'.$key, $default);
    }
    public function withoutClientTabEventRows(Builder $q): Builder
    {
        $route = (string) $this->config('client_event_route', 'user_activity.client_event');
        $excludeNames = array_values(array_filter(
            (array) $this->config('excluded_from_activity_report_route_names', [])
        ));

        $q->where(function (Builder $w) use ($route) {
            $w->whereNull('route_name')
                ->orWhere('route_name', '!=', $route);
        });
        if ($excludeNames !== []) {
            $q->where(function (Builder $w) use ($excludeNames) {
                $w->whereNull('route_name')
                    ->orWhereNotIn('route_name', $excludeNames);
            });
        }

        return $q;
    }

    public function sessionKey(): string
    {
        return (string) $this->config('session_key', 'user_activity_session_id');
    }

    /**
     * Session can lose user_activity_session_id after session()->regenerate() on login.
     * We always fall back to the open work session row in the database.
     */
    public function activeOpenWorkSessionId(int $userId): ?int
    {
        $row = UserActivitySession::query()
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->orderByDesc('id')
            ->first();

        return $row ? (int) $row->id : null;
    }

    /**
     * True if this work session still belongs to the user and is open. Stale IDs in the web
     * session (after logout or an ended row) would otherwise make {@see logRequest} log nothing.
     */
    public function isOpenWorkSessionForUser(int $userId, int $workSessionId): bool
    {
        if ($workSessionId < 1) {
            return false;
        }

        return UserActivitySession::query()
            ->where('id', $workSessionId)
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->exists();
    }

    /**
     * If the user has no open work session (e.g. existing session from before UAP, or remember-me
     * without a fresh {@see \Illuminate\Auth\Events\Login} event), create one.
     */
    public function ensureOpenWorkSessionForUser(int $userId, Request $request): int
    {
        $id = $this->activeOpenWorkSessionId($userId);
        if ($id) {
            Session::put($this->sessionKey(), $id);

            return $id;
        }
        $fp = substr(sha1((string) session()->getId()), 0, 64);
        $row = UserActivitySession::query()->create([
            'user_id' => $userId,
            'laravel_session_fingerprint' => $fp,
            'started_at' => now(),
            'last_seen_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ? (string) $request->userAgent() : null,
        ]);
        $newId = (int) $row->id;
        Session::put($this->sessionKey(), $newId);

        return $newId;
    }

    /**
     * Resolves the current work-session id: session key, then legacy key, then DB.
     * Invalid or ended IDs stored in the session are cleared so work can fall back to the DB.
     */
    public function resolveWorkSessionIdForUser(int $userId): ?int
    {
        $k = $this->sessionKey();
        $id = Session::get($k);
        if ($id) {
            $wk = (int) $id;
            if ($this->isOpenWorkSessionForUser($userId, $wk)) {
                return $wk;
            }
            Session::forget($k);
        }
        $legacy = Session::get('upm_work_session_id');
        if ($legacy) {
            $wk = (int) $legacy;
            if ($this->isOpenWorkSessionForUser($userId, $wk)) {
                Session::put($k, $wk);
                Session::forget('upm_work_session_id');

                return $wk;
            }
            Session::forget('upm_work_session_id');
        }
        $fromDb = $this->activeOpenWorkSessionId($userId);
        if ($fromDb) {
            Session::put($k, $fromDb);
        }

        return $fromDb;
    }

    /**
     * Human label for which app screen / feature was hit (Bill, Quotation, PO, etc.).
     * Stored in activity_logs.activity_module; shown in the UI as "Module".
     * Uses {@see resolveModuleLabelForPathAndRoute} so new logs get a name even when the match is only a path segment.
     */
    public function resolveActivityModuleFromRequest(Request $request): ?string
    {
        $path = '/'.ltrim($request->path(), '/');
        $route = (string) ($request->route()?->getName() ?? '');

        return $this->resolveModuleLabelForPathAndRoute($path, $route, true);
    }

    /**
     * For dashboard rows: prefer stored `activity_module`, else resolve from path/route (incl. historical null rows).
     */
    public function resolveModuleLabelForLog(?string $activityModule, ?string $path, ?string $routeName): string
    {
        $stored = $activityModule !== null ? trim($activityModule) : '';
        if ($stored !== '') {
            return $stored;
        }

        $path = (string) ($path ?? '');
        if ($path !== '' && $path[0] !== '/') {
            $path = '/'.ltrim($path, '/');
        }
        $r = (string) ($routeName ?? '');
        $resolved = $this->resolveModuleLabelForPathAndRoute($path, $r, requireFallback: true);

        return $resolved ?? 'Unassigned';
    }

    /**
     * Longest `module_path_map` key matches first, then `path_segment_to_module` / route segment, then a headline of the first segment.
     */
    public function resolveModuleLabelForPathAndRoute(string $path, string $routeName, bool $requireFallback = false): ?string
    {
        $p = strtolower($path);
        $r = strtolower($routeName);
        $fromMap = $this->moduleMapLabelFromStrings($p, $r);
        if ($fromMap !== null) {
            return $fromMap;
        }
        $fromSeg = $this->moduleFromPathAndRouteSegments($p, $r);
        if ($fromSeg !== null) {
            return $fromSeg;
        }
        if ($requireFallback && ($p !== '' || $r !== '')) {
            if ($p !== '' && $p !== '/') {
                return (string) Str::headline(collect(explode('/', trim($p, '/')))->filter()->last() ?: 'app');
            }
            if ($r !== '' && str_contains($r, '.')) {
                $lp = Str::afterLast($r, '.');

                return (string) Str::headline(str_replace(['.', '-', '_'], ' ', $lp));
            }
        }

        return null;
    }

    private function moduleMapLabelFromStrings(string $path, string $routeName): ?string
    {
        $map = (array) $this->config('module_path_map', []);
        uksort($map, fn (string $a, string $b) => strlen((string) $b) <=> strlen((string) $a));
        foreach ($map as $fragment => $label) {
            $f = strtolower((string) $fragment);
            if ($f === '') {
                continue;
            }
            if (str_contains($path, $f) || str_contains($routeName, $f)) {
                return (string) $label;
            }
        }

        return null;
    }

    private function moduleFromPathAndRouteSegments(string $path, string $routeName): ?string
    {
        $bySeg = (array) $this->config('path_segment_to_module', []);
        $pParts = $path === '' || $path === '/'
            ? []
            : array_values(array_filter(explode('/', trim($path, '/'))));
        $i = array_search('superadmin', $pParts, true);
        if ($i !== false && isset($pParts[$i + 1])) {
            $raw = $pParts[$i + 1];
            $seg = preg_match('/^([^.\/]+)/', $raw, $m) ? $m[1] : $raw;
            if (isset($bySeg[$seg])) {
                return (string) $bySeg[$seg];
            }
            if ($seg !== '') {
                return (string) Str::headline(str_replace(['.', '-', '_', '/'], ' ', $seg));
            }
        }
        if ($routeName !== '' && (str_starts_with($routeName, 'superadmin') || str_contains($routeName, 'superadmin.'))) {
            $rParts = explode('.', $routeName);
            if (count($rParts) >= 2) {
                $cand = $rParts[1] ?? null;
                if ($cand !== null && $cand !== 'superadmin') {
                    if (isset($bySeg[$cand])) {
                        return (string) $bySeg[$cand];
                    }
                    if ($cand !== '') {
                        return (string) Str::headline(str_replace(['-', '_'], ' ', $cand));
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param  \Illuminate\Support\Collection|iterable<int, \App\Models\UserActivityLog>  $rows
     * @return \Illuminate\Support\Collection<int, object{activity_module: string, n: int, records: int, n_create: int, n_update: int, n_read: int, n_delete: int, n_login: int, n_logout: int, n_other: int}>
     */
    public function aggregateModuleBreakdown(iterable $rows)
    {
        $b = [];
        foreach ($rows as $e) {
            $label = $this->resolveModuleLabelForLog(
                $e->activity_module ? (string) $e->activity_module : null,
                $e->path ? (string) $e->path : null,
                $e->route_name ? (string) $e->route_name : null
            );
            if (! isset($b[$label])) {
                $b[$label] = [
                    'n' => 0,
                    'records' => 0,
                    'n_create' => 0,
                    'n_update' => 0,
                    'n_read' => 0,
                    'n_delete' => 0,
                    'n_login' => 0,
                    'n_logout' => 0,
                    'n_other' => 0,
                ];
            }
            $b[$label]['n']++;
            $b[$label]['records'] += (int) ($e->records_count ?? 0);
            $t = (string) ($e->type ?? 'other');
            if ($t === 'create') {
                $b[$label]['n_create']++;
            } elseif ($t === 'update') {
                $b[$label]['n_update']++;
            } elseif ($t === 'read') {
                $b[$label]['n_read']++;
            } elseif ($t === 'delete') {
                $b[$label]['n_delete']++;
            } elseif ($t === 'login') {
                $b[$label]['n_login']++;
            } elseif ($t === 'logout') {
                $b[$label]['n_logout']++;
            } else {
                $b[$label]['n_other']++;
            }
        }
        uasort($b, fn (array $a, array $b) => $b['n'] <=> $a['n']);

        return collect($b)->map(function (array $stats, string $name) {
            $o = (object) $stats;
            $o->activity_module = $name;
            $o->module = $name;

            return $o;
        })->values();
    }

    public function onLogin(int $userId, ?string $ip, ?string $userAgent, ?string $sessionFp): UserActivitySession
    {
        $this->closeOrphanOpenSessions($userId);

        $row = UserActivitySession::query()->create([
            'user_id' => $userId,
            'laravel_session_fingerprint' => $sessionFp,
            'started_at' => now(),
            'last_seen_at' => now(),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        Session::put($this->sessionKey(), $row->id);

        $this->writeEvent(
            $userId,
            (int) $row->id,
            self::T_LOGIN,
            null,
            null,
            'auth/login',
            'Logged in',
            0,
            'Auth — login',
            null,
            null,
            null,
            null
        );

        return $row;
    }

    public function onLogout(int $userId, ?string $source = null): void
    {
        $id = $this->resolveWorkSessionIdForUser($userId);
        if (! $id) {
            return;
        }

        $ws = UserActivitySession::query()->where('id', $id)->where('user_id', $userId)->first();
        if (! $ws) {
            Session::forget($this->sessionKey());

            return;
        }

        $end = now();
        $ws->ended_at = $end;
        $ws->last_seen_at = $end;
        $ws->duration_seconds = max(0, (int) $ws->started_at->diffInSeconds($end));
        $ws->save();

        $isUnload = $source === 'unload';
        $path = $isUnload ? 'auth/session_end' : 'auth/logout';
        $summary = $isUnload ? 'Logged out (browser or tab closed)' : 'Logged out';

        $this->writeEvent(
            $userId,
            (int) $ws->id,
            self::T_LOGOUT,
            null,
            null,
            $path,
            $summary,
            0,
            'Auth — logout',
            null,
            null,
            null,
            null
        );

        Session::forget($this->sessionKey());
    }

    public function closeOrphanOpenSessions(int $userId): void
    {
        $end = now();
        UserActivitySession::query()
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->orderBy('id', 'desc')
            ->get()
            ->each(function (UserActivitySession $ws) use ($end, $userId) {
                $ws->ended_at = $end;
                $ref = $ws->last_seen_at && $ws->last_seen_at->gt($ws->started_at) ? $ws->last_seen_at : $end;
                $ws->duration_seconds = max(0, $ws->started_at->diffInSeconds($ref));
                $ws->save();

                $this->writeEvent(
                    $userId,
                    (int) $ws->id,
                    self::T_LOGOUT,
                    null,
                    null,
                    'auth/session_lapsed',
                    'Logged out (session ended without sign-out — e.g. timeout, expired session, or tab closed before unload beacon)',
                    0,
                    'Auth — logout',
                    null,
                    null,
                    null,
                    null
                );
            });
    }

    public function touchLastSeen(int $userId, int $workSessionId): void
    {
        $k = "user_activity:lastseen:{$userId}:{$workSessionId}";
        if (Cache::has($k)) {
            return;
        }
        Cache::put($k, 1, 30);
        UserActivitySession::query()
            ->where('id', $workSessionId)
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->update(['last_seen_at' => now()]);
    }

    /**
     * Wall-clock seconds this user’s work sessions overlap “today” in app timezone (start of local day → now).
     * Open sessions are treated as ending at the current time; only the portion that falls on today is counted.
     */
    public function sumTodayWorkSessionOverlapSecondsForUser(int $userId): int
    {
        $tz = (string) config('app.timezone');
        $now = Carbon::now($tz);
        $dayStart = $now->copy()->startOfDay();
        $windowEnd = $now->copy();

        $query = UserActivitySession::query()
            ->where('user_id', $userId)
            ->where('started_at', '<=', $windowEnd)
            ->where(function ($q) use ($dayStart) {
                $q->whereNull('ended_at')
                    ->orWhere('ended_at', '>=', $dayStart);
            });

        $total = 0;
        foreach ($query->get(['started_at', 'ended_at']) as $ws) {
            $start = $ws->started_at->copy()->timezone($tz);
            $end = $ws->ended_at
                ? $ws->ended_at->copy()->timezone($tz)
                : $now->copy();
            if ($end->lte($start)) {
                continue;
            }
            $segStart = $start->gt($dayStart) ? $start : $dayStart;
            $segEnd = $end->lt($windowEnd) ? $end : $windowEnd;
            if ($segEnd->gt($segStart)) {
                $total += (int) $segStart->diffInSeconds($segEnd);
            }
        }

        return max(0, $total);
    }

    /**
     * True for normal full-page GET requests under /superadmin/ (section navigation), excluding data/ajax endpoints.
     * Used with {@see config('user_activity.log_superadmin_navigation_gets')}.
     */
    public function isSuperadminContentNavigationGet(Request $request): bool
    {
        if (! $request->isMethod('GET') || $request->ajax()) {
            return false;
        }
        if (! (bool) $this->config('log_superadmin_navigation_gets', false)) {
            return false;
        }
        $p = '/'.ltrim(strtolower($request->path()), '/');
        if (! str_starts_with($p, '/superadmin/')) {
            return false;
        }
        if (str_contains($p, '/_ignition') || str_contains($p, 'telescope') || str_contains($p, 'horizon') || str_contains($p, 'livewire')) {
            return false;
        }
        if (str_ends_with($p, '.map') || str_ends_with($p, '.js') || str_ends_with($p, '.css') || str_ends_with($p, '.json') || str_ends_with($p, '.ico')) {
            return false;
        }
        if (str_contains($p, 'ticketsdata') || str_contains($p, '/tickets/data') || str_contains($p, 'data-table') || str_contains($p, 'datatable')
            || str_contains($p, 'typeahead') || str_contains($p, 'autocomplete') || str_contains($p, 'select2')) {
            return false;
        }
        if (str_contains($p, 'export') && (str_contains($p, 'report') || str_contains($p, 'template'))) {
            return false;
        }
        if (preg_match('#/(fetch|ajax)(/|$|_)#i', $p) || str_contains($p, 'fetchremove') || str_contains($p, 'fitchremove') || str_contains($p, 'filterremove')) {
            return false;
        }
        if (str_contains($p, 'fillter') || str_contains($p, 'fillterremove')) {
            return false;
        }
        if (str_contains($p, '/tickets/') && (str_contains($p, 'data') || str_contains($p, 'fetch'))) {
            return false;
        }

        return true;
    }

    /**
     * Records tab / page visibility from the client (not inferrable from HTTP server-side).
     */
    public function logClientTabEvent(Request $request, string $kind, ?string $pagePath = null): void
    {
        if (! (bool) $this->config('client_tab_events', false)) {
            return;
        }
        if (! $request->user()) {
            return;
        }
        $uid = (int) $request->user()->getAuthIdentifier();
        if ($uid < 1) {
            return;
        }
        $throttleKey = 'uap:clientui:'.(int) $uid.':'.md5($kind.':'.(string) $pagePath);
        if (Cache::has($throttleKey)) {
            return;
        }
        Cache::put($throttleKey, 1, 2);

        $workId = (int) ($this->resolveWorkSessionIdForUser($uid) ?? 0);
        if ($workId < 1) {
            $workId = $this->ensureOpenWorkSessionForUser($uid, $request);
        }
        if ($workId < 1 || ! $this->isOpenWorkSessionForUser($uid, $workId)) {
            return;
        }

        $map = [
            'tab_hidden' => 'Browser: left tab or hid window (page not visible)',
            'tab_visible' => 'Browser: returned to tab / window (page visible again)',
            'page_hide' => 'Browser: page hide (navigate away or close)',
        ];
        $path = 'client/'.$kind;
        $label = $map[$kind] ?? ('Browser: '.$kind);
        $q = $pagePath !== null && $pagePath !== '' ? 'path='.Str::limit(urlencode($pagePath), 450, '') : null;

        $this->writeEvent(
            $uid,
            $workId,
            self::T_OTHER,
            'POST',
            'user_activity.client_event',
            $path,
            $label,
            0,
            'Client — browser',
            null,
            null,
            $q,
            $pagePath !== null && $pagePath !== '' ? ['page' => Str::limit((string) $pagePath, 200, '')] : null
        );
    }

    public function shouldLogRequest(Request $request): bool
    {
        if (! $request->user()) {
            return false;
        }

        $p = strtolower($request->path());
        if (str_contains($p, 'login1') && $request->isMethod('POST')) {
            return false;
        }
        if (str_contains($p, 'logout') || $request->routeIs('logout')) {
            return false;
        }
        $rn = $request->route()?->getName();
        if ($rn && in_array($rn, (array) $this->config('do_not_log_route_names', []), true)) {
            return false;
        }

        $path = ltrim($request->path(), '/');
        foreach ($this->config('exclude_path_prefixes', []) as $p) {
            if ($p !== '' && Str::startsWith($path, $p)) {
                return false;
            }
        }

        $name = (string) $request->path();
        foreach ($this->config('read_extensions', []) as $ext) {
            if (Str::endsWith(strtolower($name), strtolower($ext))) {
                return false;
            }
        }

        foreach ($this->config('exclude_paths_containing', []) as $c) {
            if ($c !== '' && str_contains($name, $c)) {
                return false;
            }
        }

        return true;
    }

    public function inferType(string $method): string
    {
        $m = strtoupper($method);

        return match ($m) {
            'GET', 'HEAD', 'OPTIONS' => self::T_READ,
            'POST' => self::T_CREATE,
            'PUT', 'PATCH' => self::T_UPDATE,
            'DELETE' => self::T_DELETE,
            default => self::T_OTHER,
        };
    }

    /**
     * Method-only inference is wrong for POST / *.update and similar — those are still updates in this app.
     */
    public function inferTypeFromRequest(Request $request): string
    {
        $m = strtoupper($request->getMethod());
        if ($m !== 'POST') {
            return $this->inferType($m);
        }
        if ($this->isPostToUpdate($request)) {
            return self::T_UPDATE;
        }

        return self::T_CREATE;
    }

    private function isPostToUpdate(Request $request): bool
    {
        if (! $request->isMethod('POST')) {
            return false;
        }
        $n = strtolower((string) ($request->route()?->getName() ?? ''));
        /*
         * Many legacy routes use a single POST for create + update (e.g. superadmin.savequotation)
         * and only distinguish in the request: ?type=edit, or a non-empty record id. Without this,
         * every POST is classified as "create" and shows under Create (POST) in reports.
         */
        if (! $this->isLikelyDataOnlyActionRoute($request)) {
            $typeQ = strtolower(trim((string) $request->input('type', '')));
            if (in_array($typeQ, ['edit', 'update'], true)) {
                return true;
            }
            $modeQ = strtolower(trim((string) $request->input('mode', '')));
            if ($modeQ === 'edit') {
                return true;
            }
            if ($n !== '' && str_contains($n, 'save')) {
                $idRaw = $request->input('id');
                if (! is_array($idRaw) && is_numeric($idRaw) && (int) $idRaw > 0) {
                    return true;
                }
            }
        }
        if (str_ends_with($n, '.update') || str_ends_with($n, '.replace')) {
            return true;
        }
        $p = '/'.ltrim(strtolower($request->path()), '/');
        if (preg_match('#/update($|/|\?)#', $p)) {
            return true;
        }
        if (preg_match('#/([^/]+)/?$#', rtrim($p, '/'), $m)) {
            $last = (string) ($m[1] ?? '');
            if ($last === 'update' || (strlen($last) >= 7 && str_ends_with($last, 'update') && ! preg_match('/^(get|fetch|post|postal)/i', $last))) {
                return ! $this->isLikelyDataOnlyActionRoute($request);
            }
        }
        if ($n !== '' && str_ends_with($n, 'update') && ! $this->isLikelyDataOnlyActionRoute($request)
            && ! str_contains($n, 'search')
        ) {
            $lastSeg = $n;
            if (str_contains($n, '.')) {
                $parts = explode('.', $n);
                $lastSeg = (string) (end($parts) ?: $n);
            }
            if (preg_match('/^get\w*update$/', $lastSeg) || (str_starts_with($lastSeg, 'fetch') && str_ends_with($lastSeg, 'update'))) {
                return false;
            }
            if (str_contains($n, 'import') && ! str_contains($n, 'report')) {
                return false;
            }
            if (str_ends_with($n, 'delete') || str_ends_with($n, 'destroy')) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function recordsForType(string $type, string $method): int
    {
        $m = strtoupper($method);
        if (in_array($m, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return 0;
        }
        if ($m === 'PUT' || $m === 'PATCH') {
            return 1;
        }
        if ($m === 'DELETE') {
            return 1;
        }
        if ($m === 'POST') {
            return 1;
        }

        return 0;
    }

    public function logRequest(Request $request, ?int $serverDurationMs = null): void
    {
        if (! $this->shouldLogRequest($request)) {
            return;
        }
        $user = $request->user();
        if (! $user) {
            return;
        }
        $uid = (int) $user->getAuthIdentifier();
        if ($uid < 1) {
            return;
        }
        $workId = (int) ($this->resolveWorkSessionIdForUser($uid) ?? 0);
        if ($workId < 1) {
            $workId = $this->ensureOpenWorkSessionForUser($uid, $request);
        }
        if ($workId < 1) {
            return;
        }
        if (! $this->isOpenWorkSessionForUser($uid, $workId)) {
            return;
        }

        $this->touchLastSeen($uid, $workId);

        if ($request->isMethod('GET') && $this->isCreateFormGetRequest($request)) {
            $k = $this->resolveCreateFlowKeyForGet($request);
            if ($k) {
                $this->markCreateFormOpened($uid, $k);
            }
        }

        if ($request->isMethod('GET') && $this->isEditFormGetRequest($request)) {
            $editKey = $this->resolveEditFlowKeyForGet($request);
            if ($editKey !== null) {
                $this->markCreateFormOpened($uid, $editKey);
            }
        }

        $type = $this->inferTypeFromRequest($request);
        $activityModule = $this->resolveActivityModuleFromRequest($request);
        $isBackground = $this->isBackgroundDataRequest($request, $type);
        $route = $request->route();
        $routeName = $route?->getName();
        $path = '/'.ltrim($request->path(), '/');
        if (Str::length($path) > 500) {
            $path = Str::substr($path, 0, 500);
        }

        $logRawGet = (bool) $this->config('log_raw_get_page_views', false);
        if (! $logRawGet && $type === self::T_READ && $request->isMethod('GET') && $activityModule && ! $isBackground) {
            $this->maybeLogModuleScreenOpened($uid, $workId, $request, $activityModule, $serverDurationMs);
        }

        if ($isBackground) {
            return;
        }

        $navGet = $this->isSuperadminContentNavigationGet($request);
        if ($type === self::T_READ && $request->isMethod('GET') && ! $logRawGet && ! $navGet) {
            return;
        }

        if ($type === self::T_READ && $request->isMethod('GET') && ($logRawGet || $navGet) && $this->shouldSkipFrequentGet($uid, $path)) {
            return;
        }

        if ($this->isDuplicateEvent($uid, $workId, $type, $path, $request->getMethod())) {
            return;
        }

        $label = $routeName
            ? ($routeName.' — '.$request->getMethod())
            : ($path.' — '.$request->getMethod());
        if (Str::length($label) > 500) {
            $label = Str::substr($label, 0, 500);
        }
        if ($activityModule) {
            $pref = '['.$activityModule.'] ';
            $label = Str::length($pref.$label) > 500
                ? Str::substr($pref.$label, 0, 500)
                : $pref.$label;
        }

        $actionDurationMs = null;
        if ($type === self::T_CREATE) {
            $actionDurationMs = CreateFormDuration::nullableIntFromRequest($request);
            if ($actionDurationMs === null) {
                $actionDurationMs = $this->popCreateFormDurationMs($uid, $request);
            }
        } elseif ($type === self::T_UPDATE) {
            $actionDurationMs = CreateFormDuration::nullableIntFromRequest($request);
            if ($actionDurationMs === null) {
                $actionDurationMs = $this->popEditFormDurationFromSession($uid, $request);
            }
        }

        $this->writeEvent(
            $uid,
            $workId,
            $type,
            $request->getMethod(),
            $routeName ? Str::substr($routeName, 0, 256) : null,
            $path,
            $label,
            $this->recordsForType($type, $request->getMethod()),
            $activityModule,
            $actionDurationMs,
            $serverDurationMs,
            $this->urlQueryStringForActivityLog($request),
            $this->requestSnapshotForActivityLog($request)
        );
    }

    private function isBackgroundDataRequest(Request $request, string $inferredType): bool
    {
        if ($inferredType === self::T_READ
            && $this->isSuperadminContentNavigationGet($request)
        ) {
            return false;
        }

        $p = '/'.ltrim(strtolower($request->path()), '/');
        $pathStripped = ltrim($p, '/');

        foreach ((array) $this->config('noise_path_prefixes', []) as $pre) {
            $pre = strtolower((string) $pre);
            if ($pre === '') {
                continue;
            }
            $preT = ltrim($pre, '/');
            if (str_starts_with($pathStripped, $preT) || str_starts_with($p, '/'.$preT)) {
                return true;
            }
        }
        if ($this->isLikelyDataOnlyActionRoute($request)) {
            return true;
        }
        /*
         * Route/path substring "noise" exists to hide repetitive GET fetches. Applying it to POST/PUT/DELETE
         * can block real form saves: e.g. a route name or path that accidentally contains "fetch" or a vendor
         * key fragment. Only apply for GET; save actions are still filtered by isLikelyDataOnlyActionRoute above.
         */
        if ($request->isMethod('GET')) {
            $rn = strtolower((string) ($request->route()?->getName() ?? ''));
            foreach ((array) $this->config('noise_route_name_substrings', []) as $frag) {
                $f = strtolower((string) $frag);
                if ($f === '') {
                    continue;
                }
                if (str_contains($rn, $f) || str_contains($p, $f)) {
                    return true;
                }
            }
            foreach ((array) $this->config('noise_path_substrings', []) as $frag) {
                $f = strtolower((string) $frag);
                if ($f === '') {
                    continue;
                }
                if (str_contains($p, $f)) {
                    return true;
                }
            }
        }
        if ($inferredType === self::T_READ
            && $request->isMethod('GET')
            && (bool) $this->config('treat_ajax_get_as_background', true)
            && $request->ajax()
        ) {
            return true;
        }

        return false;
    }

    private function maybeLogModuleScreenOpened(
        int $userId,
        int $workSessionId,
        Request $request,
        string $activityModule,
        ?int $serverDurationMs
    ): void {
        if (trim($activityModule) === '') {
            return;
        }
        $trailKey = 'upm_mtrail:'.$workSessionId;
        if ((string) Session::get($trailKey) === (string) $activityModule) {
            return;
        }
        $route = $request->route();
        $routeName = $route?->getName();
        $path = '/'.ltrim($request->path(), '/');
        if (Str::length($path) > 500) {
            $path = Str::substr($path, 0, 500);
        }
        $label = '['.$activityModule.'] Module screen opened';
        if ($routeName) {
            $label .= ' — '.Str::limit($routeName, 200, '');
        }
        if (Str::length($label) > 500) {
            $label = Str::substr($label, 0, 500);
        }
        $this->writeEvent(
            $userId,
            $workSessionId,
            self::T_READ,
            'GET',
            $routeName ? Str::substr($routeName, 0, 256) : null,
            $path,
            $label,
            0,
            $activityModule,
            null,
            $serverDurationMs,
            $this->urlQueryStringForActivityLog($request),
            $this->requestSnapshotForActivityLog($request)
        );
        Session::put($trailKey, $activityModule);
    }

    private function urlQueryStringForActivityLog(Request $request): ?string
    {
        $q = $request->getQueryString();
        if ($q === null || $q === '') {
            return null;
        }
        if (Str::length($q) > 500) {
            return Str::substr($q, 0, 500);
        }

        return $q;
    }

    /**
     * Whitelisted form/query inputs plus any {@see self::ACTIVITY_SNAPSHOT_REQUEST_ATTR} set
     * by controllers after a successful save (e.g. new bill id — middleware logs after the response is built from the controller).
     *
     * @return array<string, string|int>|null
     */
    private function requestSnapshotForActivityLog(Request $request): ?array
    {
        $out = [];
        $keys = (array) $this->config('activity_log_request_snapshot_keys', []);
        foreach ($keys as $k) {
            if (! is_string($k) || $k === '' || ! $request->has($k)) {
                continue;
            }
            $v = $request->input($k);
            if (is_array($v) || is_object($v)) {
                continue;
            }
            $s = trim((string) $v);
            if ($s === '') {
                continue;
            }
            $out[$k] = Str::limit($s, 80, '');
        }

        $enriched = $request->attributes->get(self::ACTIVITY_SNAPSHOT_REQUEST_ATTR);
        if (is_array($enriched) && $enriched !== []) {
            foreach ($enriched as $k => $v) {
                if (! is_string($k) || $k === '') {
                    continue;
                }
                if (is_int($v) && $v > 0) {
                    $out[$k] = $v;
                } elseif (is_string($v) && (ctype_digit($v) && (int) $v > 0)) {
                    $out[$k] = (int) $v;
                } elseif (is_string($v) && trim($v) !== '') {
                    $out[$k] = Str::limit(trim($v), 80, '');
                } elseif (is_float($v) && $v > 0) {
                    $out[$k] = (int) $v;
                }
            }
        }

        return $out === [] ? null : $out;
    }

    private function shouldSkipFrequentGet(int $userId, string $path): bool
    {
        $h = "user_activity:get:{$userId}:".md5($path);
        if (Cache::has($h)) {
            return true;
        }
        Cache::put($h, 1, 15);

        return false;
    }

    private function isDuplicateEvent(int $userId, int $workSessionId, string $type, string $path, string $method): bool
    {
        if ($type === self::T_READ) {
            return false;
        }
        $h = 'user_activity:ev:'.md5($userId.':'.$workSessionId.':'.$type.':'.$path.':'.$method);
        if (Cache::has($h)) {
            return true;
        }
        Cache::put($h, 1, 1);

        return false;
    }

    public function writeEvent(
        int $userId,
        int $workSessionId,
        string $type,
        ?string $httpMethod,
        ?string $routeName,
        string $path,
        ?string $label,
        int $recordsCount,
        ?string $activityModule = null,
        ?int $actionDurationMs = null,
        ?int $serverDurationMs = null,
        ?string $urlQuery = null,
        ?array $requestSnapshot = null
    ): void {
        $snap = $this->userSnapshotForActivityLog($userId);

        UserActivityLog::query()->create(array_merge($snap, [
            'user_id' => $userId,
            'activity_module' => $activityModule,
            'user_activity_session_id' => $workSessionId > 0 ? $workSessionId : null,
            'type' => $type,
            'http_method' => $httpMethod,
            'route_name' => $routeName,
            'path' => $path,
            'label' => $label,
            'records_count' => $recordsCount,
            'action_duration_ms' => $actionDurationMs,
            'server_duration_ms' => $serverDurationMs,
            'url_query' => $urlQuery,
            'request_snapshot' => $requestSnapshot,
            'created_at' => now(),
        ]));
    }

    /**
     * True when this GET is treated as "opened the new-record form" (used to time create submits).
     */
    public function isCreateFormGetRequest(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }
        $p = strtolower($request->path());
        foreach (['user-activity', 'user_activity', 'user-productivity', 'user_productivity', 'telescope', 'horizon', '_ignition', 'login', 'register'] as $x) {
            if (str_contains($p, $x)) {
                return false;
            }
        }
        if (str_contains($p, 'export') && str_contains($p, 'report')) {
            return false;
        }
        $n = (string) ($request->route()?->getName() ?? '');
        if ($n !== '' && (str_ends_with($n, '.create') || str_ends_with($n, '.new'))) {
            return true;
        }
        if ($n !== '' && preg_match('/^(.+\.)?get\w*create$/i', $n)) {
            return true;
        }
        if (preg_match('#(^|/)(create|new)(/|$)#', $p)) {
            return true;
        }
        if (preg_match('#_create$|-create$#', $p)) {
            return true;
        }

        return false;
    }

    /**
     * True when this GET is a typical Laravel "edit" screen — used to time update submits
     * when {@see CreateFormDuration} is not sent (e.g. XHR, some legacy POST patterns).
     */
    public function isEditFormGetRequest(Request $request): bool
    {
        return $this->resolveEditFlowKeyForGet($request) !== null;
    }

    public function resolveCreateFlowKeyForGet(Request $request): ?string
    {
        $n = (string) ($request->route()?->getName() ?? '');
        if ($n !== '') {
            if (str_ends_with($n, '.create')) {
                return 'r:'.substr($n, 0, -strlen('.create'));
            }
            if (str_ends_with($n, '.new')) {
                return 'r:'.substr($n, 0, -strlen('.new'));
            }
            if (preg_match('/^(.+\.)?get\w*create$/i', $n)) {
                return 'r:'.$n;
            }
        }
        $path = '/'.ltrim($request->path(), '/');
        if (Str::length($path) > 500) {
            $path = Str::substr($path, 0, 500);
        }

        return 'p:'.md5($path);
    }

    /**
     * Session key when the user opened an edit form (GET). Pairs with {@see createFlowKeyForUpdateSubmitRequest()}.
     */
    public function resolveEditFlowKeyForGet(Request $request): ?string
    {
        if (! $request->isMethod('GET')) {
            return null;
        }
        $p = strtolower($request->path());
        foreach (['user-activity', 'user_activity', 'user-productivity', 'user_productivity', 'telescope', 'horizon', '_ignition', 'login', 'register'] as $x) {
            if (str_contains($p, $x)) {
                return null;
            }
        }
        $n = (string) ($request->route()?->getName() ?? '');
        if ($n === '' || ! str_ends_with($n, '.edit')) {
            return null;
        }

        return 'r:'.$n;
    }

    public function createFlowKeyForCreatePostRequest(Request $request): ?string
    {
        if (! $this->isRecordCreateSubmitRequest($request)) {
            return null;
        }
        $n = (string) ($request->route()?->getName() ?? '');
        $map = (array) $this->config('create_flow_post_to_get_route', []);
        if ($n !== '' && isset($map[$n])) {
            return 'r:'.(string) $map[$n];
        }
        if ($n !== '' && str_ends_with($n, '.store')) {
            return 'r:'.substr($n, 0, -strlen('.store'));
        }

        return null;
    }

    /**
     * Session key for a successful update save — must match the edit GET key from {@see resolveEditFlowKeyForGet()}.
     * Convention: "foo.update" pairs with the edit key "r:foo.edit" (Laravel resource naming).
     */
    public function createFlowKeyForUpdateSubmitRequest(Request $request): ?string
    {
        $m = strtoupper($request->getMethod());
        if (! in_array($m, ['POST', 'PUT', 'PATCH'], true)) {
            return null;
        }
        if ($this->isLikelyDataOnlyActionRoute($request)) {
            return null;
        }
        $n = (string) ($request->route()?->getName() ?? '');
        if ($n === '' || ! str_ends_with($n, '.update')) {
            return null;
        }
        $base = (string) Str::beforeLast($n, '.update');
        if ($base === '') {
            return null;
        }

        return 'r:'.$base.'.edit';
    }

    /**
     * True for POST/PUT that represent saving a new record in our HTTP-method model (we still only pair POST).
     */
    public function isRecordCreateSubmitRequest(Request $request): bool
    {
        if (! $this->isCreateHttpMethod($request->getMethod())) {
            return false;
        }
        if ($this->isLikelyDataOnlyActionRoute($request)) {
            return false;
        }
        if ($this->isPostToUpdate($request)) {
            return false;
        }

        return true;
    }

    public function formatDurationMs(?int $ms): string
    {
        if ($ms === null) {
            return '—';
        }
        if ($ms < 1000) {
            return $ms.'ms';
        }
        $s = (int) floor($ms / 1000);
        if ($s < 60) {
            return $s.'s';
        }
        $m = intdiv($s, 60);
        $rem = $s % 60;
        if ($m < 60) {
            return $m.'m '.str_pad((string) $rem, 2, '0', STR_PAD_LEFT).'s';
        }
        $h = intdiv($m, 60);
        $m2 = $m % 60;

        return $h.'h '.$m2.'m';
    }

    public function formatActionAndServerDurationsForUi(?int $actionMs, ?int $serverMs): string
    {
        if ($actionMs === null && $serverMs === null) {
            return '—';
        }
        if ($actionMs !== null) {
            return 'Time on form: '.$this->formatDurationMs($actionMs);
        }

        return 'Server '.$this->formatDurationMs($serverMs);
    }

    /**
     * Three display columns: From (time), To (time), Total (durations). Single row per log entry.
     * From = earliest available start: form open time if form duration is stored, else request start.
     * To = log timestamp (request end). Total = same compact line as before (form and/or server).
     *
     * @return array{from: string, to: string, total: string, title: string}
     */
    public function getLogTimeColumnValues(object $e): array
    {
        $end = $e->created_at ?? null;
        if ($end === null) {
            return [
                'from' => '—',
                'to' => '—',
                'total' => '—',
                'title' => 'No log timestamp.',
            ];
        }
        $endC = $end instanceof Carbon ? $end->copy() : Carbon::parse($end);
        $a = (int) ($e->action_duration_ms ?? 0);
        $s = (int) ($e->server_duration_ms ?? 0);
        if ($a <= 0 && $s <= 0) {
            return [
                'from' => '—',
                'to' => '—',
                'total' => '—',
                'title' => 'No stored durations on this log row (older data or not captured).',
            ];
        }
        if ($a > 0) {
            $fromC = $endC->copy()->subMilliseconds($a);
        } else {
            $fromC = $endC->copy()->subMilliseconds($s);
        }
        $toStr = $this->formatTimePointForLog($endC);
        $fromStr = $this->formatTimePointForLog($fromC);
        $total = $this->formatActionAndServerDurationsForUi(
            $a > 0 ? $a : null,
            $s > 0 ? $s : null
        );

        return [
            'from' => $fromStr,
            'to' => $toStr,
            'total' => $total,
            'title' => 'From/To: times only (H:i:s). To = log time. From = end minus the primary stored span (form time if action_duration_ms present, else server). Total shows user time on the form when known; otherwise server time.',
        ];
    }

    /**
     * @return array{module: ?string, module_label: string, route_name: ?string, path: string, date: ?string, start_datetime: ?string, end_datetime: ?string, start_time: string, end_time: string, total: string, form_duration_label: ?string, server_duration_label: ?string, reference: string, link_url: ?string, link_label: string}
     */
    public function getLogTimeDetailForApi(object $e): array
    {
        $link = $e instanceof UserActivityLog
            ? app(ActivityLogResourceResolver::class)->resolveForApi($e)
            : [
                'reference' => '—',
                'link_url' => null,
                'link_label' => 'Open',
            ];
        $module = isset($e->activity_module) && (string) $e->activity_module !== ''
            ? (string) $e->activity_module
            : null;
        $pathStr = (string) ($e->path ?? '');
        $routeNameStr = $e->route_name !== null && (string) $e->route_name !== ''
            ? (string) $e->route_name
            : null;
        $base = [
            'module' => $module,
            'module_label' => $this->resolveModuleLabelForLog($module, $pathStr, $routeNameStr),
            'route_name' => $e->route_name ?? null,
            'path' => $pathStr,
        ];
        $end = $e->created_at ?? null;
        if ($end === null) {
            return array_merge($base, [
                'date' => null,
                'start_datetime' => null,
                'end_datetime' => null,
                'start_time' => '—',
                'end_time' => '—',
                'total' => '—',
                'form_duration_label' => null,
                'server_duration_label' => null,
            ], $link);
        }
        $endC = $end instanceof Carbon ? $end->copy() : Carbon::parse($end);
        $a = (int) ($e->action_duration_ms ?? 0);
        $s = (int) ($e->server_duration_ms ?? 0);
        $date = $endC->format('Y-m-d');
        $endTime = $this->formatTimePointForLog($endC);
        $endDatetime = $endC->toIso8601String();
        if ($a <= 0 && $s <= 0) {
            return array_merge($base, [
                'date' => $date,
                'start_datetime' => null,
                'end_datetime' => $endDatetime,
                'start_time' => '—',
                'end_time' => $endTime,
                'total' => '— (no duration captured)',
                'form_duration_label' => null,
                'server_duration_label' => null,
            ], $link);
        }
        if ($a > 0) {
            $fromC = $endC->copy()->subMilliseconds($a);
        } else {
            $fromC = $endC->copy()->subMilliseconds($s);
        }
        $cols = $this->getLogTimeColumnValues($e);
        $startDatetime = $fromC->toIso8601String();
        $total = $a > 0
            ? $cols['total']
            : 'Request: '.$this->formatDurationMs($s);

        return array_merge($base, [
            'date' => $date,
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime,
            'start_time' => $cols['from'],
            'end_time' => $cols['to'],
            'total' => $total,
            'form_duration_label' => $a > 0 ? $this->formatDurationMs($a) : null,
            'server_duration_label' => $s > 0 ? $this->formatDurationMs($s) : null,
        ], $link);
    }

    private function formatTimePointForLog(Carbon $dt): string
    {
        return $dt->format('H:i:s');
    }

    private function isCreateHttpMethod(string $method): bool
    {
        return strtoupper($method) === 'POST';
    }

    private function isXmlHttpOrPrefetch(Request $request): bool
    {
        $p = $request->headers->get('Purpose', $request->headers->get('Sec-Purpose', ''));

        return $p === 'prefetch' || $p === 'prerender';
    }

    private function isLikelyDataOnlyActionRoute(Request $request): bool
    {
        $n = strtolower((string) ($request->route()?->getName() ?? ''));
        if ($n === '' || $n === '0') {
            return false;
        }
        $dataLoadFragments = [
            '_fetch', '.fetch', '_ajax', '.ajax',
            'getquotationfetch', 'getpurchasefetch', 'getpettycashajax',
            'getbranchfetch', // zone → branch POST /superadmin/branch_fetch
            'purchasecheckerfetch', 'purchaseapproverfetch', // PO approval dropdown loads
            'branchfetchviews', 'zonefetchviews', // company/zone/branch selector lists
            'check.bill.number', // bill create: duplicate bill # AJAX
        ];
        foreach ($dataLoadFragments as $f) {
            if (str_contains($n, $f)) {
                return true;
            }
        }
        $p = '/'.ltrim(strtolower($request->path()), '/');
        if ($p === 'check-bill-number' && $request->isMethod('POST')) {
            return true;
        }
        if (str_contains($n, 'import') && ! str_contains($n, 'report')) {
            return true;
        }
        if (str_ends_with($n, 'delete') || str_ends_with($n, 'destroy')) {
            return true;
        }

        return str_contains($n, 'search');
    }

    private function markCreateFormOpened(int $userId, string $flowKey): void
    {
        Session::put($this->createFlowSessionKey($userId, $flowKey), microtime(true));
    }

    private function createFlowSessionKey(int $userId, string $flowKey): string
    {
        return 'upm_cf:'.(int) $userId.':'.sha1($flowKey);
    }

    private function popCreateFormDurationMs(int $userId, Request $request): ?int
    {
        if (! $this->isCreateHttpMethod($request->getMethod())) {
            return null;
        }
        // XHR (e.g. jQuery $.ajax) is common for saves; do not block — pair with GET via create_flow_post_to_get_route.
        if ($this->isLikelyDataOnlyActionRoute($request)) {
            return null;
        }
        $k = $this->createFlowKeyForCreatePostRequest($request);
        if (! $k) {
            return null;
        }

        return $this->popDurationForSessionKey($userId, $k);
    }

    private function popEditFormDurationFromSession(int $userId, Request $request): ?int
    {
        $k = $this->createFlowKeyForUpdateSubmitRequest($request);
        if (! $k) {
            return null;
        }

        return $this->popDurationForSessionKey($userId, $k);
    }

    private function popDurationForSessionKey(int $userId, string $k): ?int
    {
        $sKey = $this->createFlowSessionKey($userId, $k);
        $t0 = Session::pull($sKey);
        if (! is_float($t0) && ! is_int($t0) && ! is_string($t0)) {
            return null;
        }
        $t0f = (float) $t0;
        if ($t0f <= 0) {
            return null;
        }
        $maxS = (int) $this->config('create_flow_max_form_seconds', 172800);
        if ($maxS < 1) {
            $maxS = 172800;
        }
        $elapsed = microtime(true) - $t0f;
        if ($elapsed < 0 || $elapsed > $maxS) {
            return null;
        }

        return (int) min(2147483647, (int) round($elapsed * 1000));
    }

    /**
     * Same pattern as {@see \App\Models\ActivityLog::log} / `user_activity_logs` — snapshot of identity at the moment the row is written.
     *
     * @return array{username: string|null, user_fullname: string|null, user_email: string|null}
     */
    private function userSnapshotForActivityLog(int $userId): array
    {
        $user = $this->userForSnapshot($userId);
        if (! $user) {
            return ['username' => null, 'user_fullname' => null, 'user_email' => null];
        }

        $full = $user->user_fullname ?? $user->name ?? null;
        if ($full === '') {
            $full = null;
        }
        $un = $user->username;
        if ($un === '') {
            $un = null;
        }

        return [
            'username' => $un,
            'user_fullname' => is_string($full) ? $full : null,
            'user_email' => $user->email ?: null,
        ];
    }

    private function userForSnapshot(int $userId): ?object
    {
        if (array_key_exists($userId, self::$snapshotUserById)) {
            return self::$snapshotUserById[$userId];
        }
        if (auth()->check() && (int) auth()->id() === $userId) {
            $u = auth()->user();

            return self::$snapshotUserById[$userId] = $u;
        }

        return self::$snapshotUserById[$userId] = usermanagementdetails::query()->find($userId);
    }
}
