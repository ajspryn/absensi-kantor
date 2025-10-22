<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeProfileComplete
{
     /**
      * Handle an incoming request.
      */
     public function handle(Request $request, Closure $next): Response
     {
          if (!Auth::check()) {
               return redirect()->route('login');
          }

          $user = Auth::user();

          // Exclude admins/system roles from this middleware
          if ($user->role && in_array(strtolower($user->role->name), ['admin', 'super admin', 'system'])) {
               return $next($request);
          }

          // If user has no employee record, let them complete profile via dedicated route
          $employee = $user->employee;

          // Allow access to the profile completion endpoints to avoid redirect loop
          $allowedRoutes = [
               'employee.profile.complete',
               'employee.profile.store',
               'employee.profile.index',
               'logout',
          ];

          $routeName = $request->route()?->getName();

          // Always allow attendance corrections routes (employee can submit corrections even if profile incomplete)
          if ($routeName && str_contains($routeName, 'attendance.corrections')) {
               return $next($request);
          }

          if (in_array($routeName, $allowedRoutes)) {
               return $next($request);
          }

          // If no employee row or required fields missing, redirect to complete profile
          $required = [
               'employee_id',
               'full_name',
               'department_id',
               'position_id',
          ];

          $missing = false;
          if (!$employee) {
               $missing = true;
          } else {
               foreach ($required as $field) {
                    if (is_null($employee->{$field}) || $employee->{$field} === '') {
                         $missing = true;
                         break;
                    }
               }
          }

          if ($missing) {
               return redirect()->route('employee.profile.complete');
          }

          return $next($request);
     }
}
