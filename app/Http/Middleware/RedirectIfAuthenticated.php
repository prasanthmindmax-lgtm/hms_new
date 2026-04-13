<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                // Never send authenticated users back to guest /login (would 302-loop with old HOME=/login).
                $path = '/';
                if ($user && isset($user->role_id)) {
                    $path = match ((int) $user->role_id) {
                        1 => '/superadmin/dashboard',
                        2 => '/referral/dashboard',
                        3 => '/staff/dashboard',
                        4 => '/admin/dashboard',
                        5 => '/management/dashboard',
                        default => '/',
                    };
                }

                return redirect()->intended($path);
            }
        }

        return $next($request);
    }
}
