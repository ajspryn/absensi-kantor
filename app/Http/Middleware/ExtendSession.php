<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class ExtendSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is authenticated, migrate session on active requests to
        // reduce session fixation risk and allow sliding expiration.
        if (Auth::check()) {
            // Only regenerate session occasionally to avoid excessive writes.
            if (
                ! $request->session()->has('last_activity_at') ||
                now()->diffInMinutes($request->session()->get('last_activity_at')) >= 30
            ) {
                $request->session()->regenerate();
                $request->session()->put('last_activity_at', now());
            }
        }

        return $next($request);
    }
}
