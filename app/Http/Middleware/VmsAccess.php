<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * VmsAccess middleware:
 * Grants access if the authenticated user EITHER:
 *   (a) has access_limits = 1 (full superadmin), OR
 *   (b) has at least one VMS menu assigned in user_menus (status = 1)
 */
class VmsAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // (a) Full superadmin — always has VMS access
        if ((int)($user->access_limits ?? 0) === 1) {
            return $next($request);
        }

        // (b) User was explicitly granted at least one VMS menu
        $hasVmsMenu = DB::table('user_menus')
            ->join('menus', 'user_menus.menu_id', '=', 'menus.id')
            ->where('user_menus.user_id', $user->id)
            ->where('user_menus.status', '1')
            ->where('menus.active_ids', 'vms_color')
            ->exists();

        if ($hasVmsMenu) {
            return $next($request);
        }

        // No VMS access — redirect back to HMS dashboard with message
        return redirect()
            ->route('superadmin.dashboard')
            ->with('error', 'You do not have access to the Visitor Management System.');
    }
}
