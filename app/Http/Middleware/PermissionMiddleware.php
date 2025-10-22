<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get user with role relationship
        $user = \App\Models\User::with('role')->find(Auth::id());

        if (!$user || !$user->role) {
            abort(403, 'User role not found');
        }

        // Normalize and flatten permissions (support comma-separated middleware params)
        $required = collect($permissions)
            ->flatMap(function ($p) {
                return array_map('trim', explode(',', $p));
            })
            ->filter()
            ->values()
            ->all();

        if (!empty($required)) {
            $hasPermission = false;
            foreach ($required as $permission) {
                if ($user->hasPermission($permission)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (!$hasPermission) {
                abort(403, 'Unauthorized. Required permissions: ' . implode(', ', $required));
            }
        }

        return $next($request);
    }
}
