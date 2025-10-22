<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (empty($roles)) {
            return $next($request);
        }

        $userRoleName = strtolower(optional($user->role)->name ?? '');

        // Normalize role names passed as parameters and allow comma separated
        $allowed = collect($roles)
            ->flatMap(function ($r) {
                return array_map('trim', explode(',', $r));
            })
            ->map(fn($r) => strtolower($r))
            ->unique()
            ->toArray();

        if (! in_array($userRoleName, $allowed, true)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
