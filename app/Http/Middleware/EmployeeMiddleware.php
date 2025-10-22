<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Allow any authenticated user whose role is active and is not an admin.
        // Previously this used hard-coded numeric role IDs which is brittle and
        // fails when factories create roles with different IDs. Use role name
        // checks instead for stability and readability.
        if (!$user->role || !$user->role->is_active) {
            abort(403, 'User role not found or inactive');
        }

        if (strtolower($user->role->name) === 'admin') {
            abort(403, 'Unauthorized. Employee access required.');
        }

        return $next($request);
    }
}
