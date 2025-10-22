<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade directives for permission checking

        // @canDo('permission')
        Blade::directive('canDo', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->hasPermission($permission)): ?>";
        });

        // @endCanDo
        Blade::directive('endCanDo', function () {
            return "<?php endif; ?>";
        });

        // @cannotDo('permission')
        Blade::directive('cannotDo', function ($permission) {
            return "<?php if(auth()->check() && !auth()->user()->hasPermission($permission)): ?>";
        });

        // @endCannotDo
        Blade::directive('endCannotDo', function () {
            return "<?php endif; ?>";
        });

        // @hasAnyPermission(['perm1', 'perm2'])
        Blade::directive('hasAnyPermission', function ($permissions) {
            return "<?php if(auth()->check() && auth()->user()->hasAnyPermission($permissions)): ?>";
        });

        // @endHasAnyPermission
        Blade::directive('endHasAnyPermission', function () {
            return "<?php endif; ?>";
        });

        // @hasRole('roleName')
        Blade::directive('hasRole', function ($roleName) {
            return "<?php if(auth()->check() && auth()->user()->role && auth()->user()->role->name === $roleName): ?>";
        });

        // @endHasRole
        Blade::directive('endHasRole', function () {
            return "<?php endif; ?>";
        });

        // Register a fallback named route used by older code/tests. If the
        // route name 'attendance.corrections.index' is not defined, create a
        // simple redirect to the canonical employee-attendance corrections
        // index route. This keeps compatibility with tests that expect the
        // plain route name.
        if (!\Illuminate\Support\Facades\Route::has('attendance.corrections.index')) {
            \Illuminate\Support\Facades\Route::get('/attendance-corrections', function () {
                return redirect()->route('employee.attendance.corrections.index');
            })->name('attendance.corrections.index');
        }
    }
}
