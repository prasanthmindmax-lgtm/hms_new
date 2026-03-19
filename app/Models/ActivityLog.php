<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'user_activity_logs';

    protected $fillable = [
        'user_id',
        'username',
        'user_fullname',
        'user_email',
        'user_role',
        'access_level',
        'action',
        'module',
        'description',
        'url',
        'method',
        'ip_address',
        'user_agent',
        'extra_data',
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    /**
     * Return the real client/machine IP.
     *
     * Priority:
     *  1. Session-stored login IP  — set once at login, reused for the whole session
     *  2. Forwarded headers        — for reverse proxy / CDN
     *  3. REMOTE_ADDR              — direct connection
     *  4. Server's own LAN IP      — when browser & server are on the same machine (XAMPP)
     *     (gethostbyname resolves the server hostname → actual network adapter IP e.g. 192.168.0.177)
     */
    public static function resolveIp(): string
    {
        // ── 1. Session-stored login IP ──────────────────────────────────────────
        try {
            $sessionIp = session('user_login_ip');
            if ($sessionIp && filter_var($sessionIp, FILTER_VALIDATE_IP)) {
                return $sessionIp;
            }
        } catch (\Throwable $e) {
            // session not available (CLI, queue worker) — continue
        }

        return static::detectRealIp();
    }

    /**
     * Detect the real client IP without relying on the session.
     * Called at login time (before the session IP is saved) and as a fallback.
     *
     * Priority:
     *  1. CloudFlare real IP header
     *  2. X-Real-IP (set by nginx)
     *  3. X-Forwarded-For first entry (set by any reverse proxy)
     *     → accepts LAN/private IPs (192.168.x.x, 10.x.x.x) — these are real client IPs on an internal network
     *  4. HTTP_CLIENT_IP
     *  5. REMOTE_ADDR — the raw TCP connection IP
     *  6. XAMPP localhost fallback — only when REMOTE_ADDR is literally 127.0.0.1/::1,
     *     meaning the browser and server are on the same machine;
     *     gethostbyname(gethostname()) resolves the server's own LAN adapter IP.
     */
    public static function detectRealIp(): string
    {
        // ── 1-4. Proxy / CDN forwarded headers ──────────────────────────────
        // NOTE: We intentionally accept private-range (LAN) IPs here because
        // on an internal network the real client IP IS a private address.
        foreach ([
            'HTTP_CF_CONNECTING_IP',   // Cloudflare
            'HTTP_X_REAL_IP',          // nginx proxy_pass
            'HTTP_X_FORWARDED_FOR',    // standard reverse-proxy header
            'HTTP_CLIENT_IP',          // some proxies
        ] as $key) {
            $val = $_SERVER[$key] ?? null;
            if (!$val) continue;

            // X-Forwarded-For can be a comma-separated list; take the first (leftmost = real client)
            $ip = trim(explode(',', $val)[0]);

            if (filter_var($ip, FILTER_VALIDATE_IP) && $ip !== '127.0.0.1' && $ip !== '::1') {
                return $ip;   // ← real client LAN IP or public IP
            }
        }

        // ── 5. REMOTE_ADDR — direct TCP connection ───────────────────────────
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? (request()->ip() ?? '');
        $remoteAddr = ($remoteAddr === '::1') ? '127.0.0.1' : $remoteAddr;

        if ($remoteAddr && $remoteAddr !== '127.0.0.1') {
            return $remoteAddr;   // real client IP (LAN or public)
        }

        // ── 6. XAMPP same-machine fallback ───────────────────────────────────
        // Only reached when browser and XAMPP server are on the SAME physical machine.
        // gethostbyname(gethostname()) returns the machine's own LAN adapter IP
        // (e.g. 192.168.0.177) instead of 127.0.0.1.
        try {
            $hostname = gethostname();
            $lanIp    = $hostname ? gethostbyname($hostname) : '';
            if ($lanIp && filter_var($lanIp, FILTER_VALIDATE_IP) && $lanIp !== '127.0.0.1') {
                return $lanIp;
            }
        } catch (\Throwable $e) { /* ignore */ }

        return '127.0.0.1';
    }

    /**
     * Log an action from anywhere in the application.
     *
     * @param string      $action      e.g. "Approve", "Filter", "Save", "Login"
     * @param string|null $module      e.g. "Discount > Dashboard"
     * @param string|null $description Human-readable detail
     * @param array       $extra       Any structured context (record_id, filters, etc.)
     */
    public static function log(
        string $action,
        ?string $module = null,
        ?string $description = null,
        array $extra = []
    ): void {
        try {
            $user = auth()->user();
            $req  = request();

            // Always attach the session IP so every log row shows login machine IP
            $ip = static::resolveIp();

            static::create([
                'user_id'       => $user?->id,
                'username'      => $user?->username ?? null,
                'user_fullname' => $user?->user_fullname ?? $user?->name,
                'user_email'    => $user?->email,
                'user_role'     => $user?->role_id ?? null,
                'access_level'  => $user?->access_limits ?? null,
                'action'        => $action,
                'module'        => $module,
                'description'   => $description,
                'url'           => $req->fullUrl(),
                'method'        => $req->method(),
                'ip_address'    => $ip,
                'user_agent'    => substr($req->userAgent() ?? '', 0, 500),
                'extra_data'    => !empty($extra) ? $extra : null,
            ]);
        } catch (\Throwable $e) {
            \Log::error('ActivityLog::log failed: ' . $e->getMessage());
        }
    }
}
