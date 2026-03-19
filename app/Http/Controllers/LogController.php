<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogController extends Controller
{
    // ─────────────────────────────────────────────
    // Page: /superadmin/logs
    // ─────────────────────────────────────────────
    public function index()
    {
        
        $admin = auth()->user();

        $users = DB::table('user_activity_logs')
            ->select('user_id', 'user_fullname', 'user_email')
            ->whereNotNull('user_id')
            ->distinct()
            ->orderBy('user_fullname')
            ->get();

        $modules = DB::table('user_activity_logs')
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $actions = DB::table('user_activity_logs')
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Distinct IPs for filter
        $ips = DB::table('user_activity_logs')
            ->whereNotNull('ip_address')
            ->distinct()
            ->orderBy('ip_address')
            ->pluck('ip_address');
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        $since = Carbon::now()->subDays(30)->startOfDay();  // always Sunday 23:59:59
        $stats = [
            'total_today'  => ActivityLog::whereDate('created_at', today())->count(),
            'total_week'   => ActivityLog::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
            'total_month'  => ActivityLog::where('created_at', '>=', $since)->count(),
            'total_all'    => ActivityLog::count(),
            'active_users' => ActivityLog::where('created_at', '>=', $since)->distinct('user_id')->count('user_id'),
        ];

        return view('superadmin.logs', compact('admin', 'users', 'modules', 'actions', 'ips', 'stats'));
    }

    // ─────────────────────────────────────────────
    // AJAX: GET /superadmin/logs-data
    // ─────────────────────────────────────────────
    public function getData(Request $request)
    {
        $query = ActivityLog::query();

        // ── Date range ─────────────────────────────────
        if ($request->filled('date_range')) {
            $parts = explode(' - ', $request->date_range);
            if (count($parts) === 2) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($parts[0]))->startOfDay();
                    $end   = \Carbon\Carbon::createFromFormat('d/m/Y', trim($parts[1]))->endOfDay();
                    $query->whereBetween('created_at', [$start, $end]);
                } catch (\Exception $e) { /* ignore bad date */ }
            }
        }

        // ── User ───────────────────────────────────────
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // ── Module (supports partial match for "Parent > Child") ─
        if ($request->filled('module')) {
            $query->where('module', 'like', '%' . $request->module . '%');
        }

        // ── Action ─────────────────────────────────────
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // ── IP address ─────────────────────────────────
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        // ── Keyword search ─────────────────────────────
        if ($request->filled('search')) {
            $kw = $request->search;
            $query->where(function ($q) use ($kw) {
                $q->where('username',       'like', "%$kw%")
                  ->orWhere('user_fullname', 'like', "%$kw%")
                  ->orWhere('user_email',    'like', "%$kw%")
                  ->orWhere('description',   'like', "%$kw%")
                  ->orWhere('module',        'like', "%$kw%")
                  ->orWhere('action',        'like', "%$kw%")
                  ->orWhere('ip_address',    'like', "%$kw%")
                  ->orWhere('url',           'like', "%$kw%")
                  ->orWhere('extra_data',    'like', "%$kw%");
            });
        }

        // ── Filtered stats — same shape as global stats ─────
        // Always compute the same 5 metrics (total, today, week, month, users)
        // but scoped to whatever filters the user applied.
        $baseQuery = clone $query;   // clone before skip/take

       
        $wStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $wEnd   = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        $mStart = Carbon::now()->subDays(30)->startOfDay();

        $total       = $baseQuery->count();
        $todayCount  = (clone $baseQuery)->whereDate('created_at', today())->count();
        $weekCount   = (clone $baseQuery)->whereBetween('created_at', [$wStart, $wEnd])->count();
        $monthCount  = (clone $baseQuery)->where('created_at', '>=', $mStart)->count();
        $uniqueUsers = (clone $baseQuery)->distinct('user_id')->count('user_id');

        // Detect if any filter is active
        $isFiltered = $request->filled('date_range')
                   || $request->filled('user_id')
                   || $request->filled('module')
                   || $request->filled('action')
                   || $request->filled('ip_address')
                   || $request->filled('search');

        // ── Pagination ─────────────────────────────────
        $perPage = max(1, min(500, (int)($request->per_page ?? 50)));
        $page    = max(1, (int)($request->page ?? 1));

        $logs = $query->orderByDesc('created_at')
                      ->skip(($page - 1) * $perPage)
                      ->take($perPage)
                      ->get();

        return response()->json([
            'data'        => $logs,
            'total'       => $total,
            'per_page'    => $perPage,
            'page'        => $page,
            'last_page'   => (int) ceil($total / max(1, $perPage)),
            'is_filtered' => $isFiltered,
            'stats' => [
                'total'        => $total,
                'today'        => $todayCount,
                'week'         => $weekCount,
                'month'        => $monthCount,
                'unique_users' => $uniqueUsers,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // AJAX: POST /superadmin/logs-store
    // (JS click tracker sends here)
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'action'      => 'required|string|max:255',
            'module'      => 'nullable|string|max:200',
            'description' => 'nullable|string|max:2000',
        ]);

        $extra = [];
        if ($request->filled('extra_data')) {
            $decoded = json_decode($request->extra_data, true);
            if (is_array($decoded)) {
                $extra = $decoded;
            }
        }

        // Always use the session login IP — ignore anything from the browser
        $loginIp = session('user_ip');
        $loginIp = session('user_ip');
        if(empty($loginIp)){
            $loginIp   = session('user_login_ip', ActivityLog::resolveIp());
        }
        $loginAt   = session('user_login_at');
        $extra['login_ip']  = $loginIp;
        if ($loginAt) $extra['session_started'] = $loginAt;

        $user = auth()->user();

        try {
            \App\Models\ActivityLog::create([
                'user_id'       => $user?->id,
                'username'      => $user?->username ?? null,
                'user_fullname' => $user?->user_fullname ?? $user?->name,
                'user_email'    => $user?->email,
                'user_role'     => $user?->role_id ?? null,
                'access_level'  => $user?->access_limits ?? null,
                'action'        => $request->action,
                'module'        => $request->module,
                'description'   => $request->description,
                'url'           => $request->fullUrl(),
                'method'        => 'JS-CLICK',
                'ip_address'    => $loginIp,
                'user_agent'    => substr($request->userAgent() ?? '', 0, 500),
                'extra_data'    => $extra ?: null,
            ]);
        } catch (\Throwable $e) {
            \Log::error('LogController::store failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────
    // POST: /superadmin/logs-clear
    // ─────────────────────────────────────────────
    public function clear(Request $request)
    {
        $days    = max(1, (int)($request->days ?? 90));
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        ActivityLog::log(
            'Clear Logs',
            'Logs',
            "Cleared logs older than {$days} days. Deleted: {$deleted} records.",
            ['days' => $days, 'deleted_count' => $deleted]
        );

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'message' => "Deleted {$deleted} log records older than {$days} days.",
        ]);
    }

    // ─────────────────────────────────────────────
    // GET: /superadmin/logs-stats
    // ─────────────────────────────────────────────
    public function stats()
    {
        $ws = now()->startOfIsoWeek();
        $we = now()->endOfIsoWeek();
        $ms = now()->subDays(30)->startOfDay();

        return response()->json([
            'total_today'  => ActivityLog::whereDate('created_at', today())->count(),
            'total_week'   => ActivityLog::whereBetween('created_at', [$ws, $we])->count(),
            'total_month'  => ActivityLog::where('created_at', '>=', $ms)->count(),
            'total_all'    => ActivityLog::count(),
            'active_users' => ActivityLog::where('created_at', '>=', $ms)->distinct('user_id')->count('user_id'),
        ]);
    }
}
