<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role_id): Response
    {

        // print_r($role_id); exit;

        $role = (int) $role_id; // Convert role to integer

 //print_r($role); exit;

        if($request->user()->role_id !== $role) {
            \Auth::logout();
            session_unset();
            return redirect('login');
        }
        return $next($request);
    }
}
