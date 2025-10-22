<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\OfficeLocationController;
use App\Http\Controllers\Admin\WorkScheduleController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AttendanceCorrectionController as AdminAttendanceCorrectionController;
use App\Http\Controllers\Employee\AttendanceCorrectionController as EmployeeAttendanceCorrectionController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// PWA Offline page
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Forgot Password routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request.form');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetRequest'])->name('password.request');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Publicly-named alias route used by tests: attendance.corrections.index
    // Points to the employee corrections index but uses the simple route name expected in tests.
    Route::middleware(['auth', 'employee', 'permission:attendance.corrections.request'])->get('/employee/attendance/corrections', [EmployeeAttendanceCorrectionController::class, 'index'])->name('attendance.corrections.index');

    // Permission test route (for development/testing)
    Route::get('/permission-test', function () {
        return view('permission-test');
    })->name('permission.test');

    // Error page test routes (for development/testing)
    Route::prefix('test-error')->group(function () {
        Route::get('/403', function () {
            abort(403, 'Test unauthorized access with required permissions: employees.view, roles.edit');
        });
        Route::get('/404', function () {
            abort(404);
        });
        Route::get('/500', function () {
            abort(500, 'Test server error');
        });
        Route::get('/419', function () {
            abort(419);
        });
    });

    // Profile completion routes (accessible by all authenticated users)
    Route::get('/employee/profile/complete', [EmployeeController::class, 'completeProfile'])->name('employee.profile.complete');
    Route::post('/employee/profile/complete', [EmployeeController::class, 'storeProfile'])->name('employee.profile.store');

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Employee management routes with permission check
        Route::middleware('permission:employees.view,employees.manage')->group(function () {
            Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
            Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->whereNumber('employee')->name('employees.show');
        });

        Route::middleware('permission:employees.create,employees.manage')->group(function () {
            Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
            Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        });

        Route::middleware('permission:employees.edit,employees.manage')->group(function () {
            Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->whereNumber('employee')->name('employees.edit');
            Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->whereNumber('employee')->name('employees.update');
        });

        Route::middleware('permission:employees.delete,employees.manage')->group(function () {
            Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->whereNumber('employee')->name('employees.destroy');
        });

        // Employee bulk import routes
        Route::middleware('permission:employees.create,employees.manage')->group(function () {
            Route::get('/employees-import', [EmployeeController::class, 'showImport'])->name('employees.import');
            Route::post('/employees-import', [EmployeeController::class, 'import'])->name('employees.import.process');
            Route::get('/employees-template', [EmployeeController::class, 'downloadTemplate'])->name('employees.template');
        });

        // Password Reset Management routes
        Route::get('/password-reset', [PasswordResetController::class, 'index'])->name('password-reset.index');
        Route::patch('/password-reset/{id}/approve', [PasswordResetController::class, 'approve'])->name('password-reset.approve');
        Route::patch('/password-reset/{id}/reject', [PasswordResetController::class, 'reject'])->name('password-reset.reject');

        // Settings routes
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');

        // Office Locations routes
        Route::resource('office-locations', OfficeLocationController::class);
        Route::patch('/office-locations/{officeLocation}/toggle-status', [OfficeLocationController::class, 'toggleStatus'])->name('office-locations.toggle-status');

        // Work Schedules routes
        Route::resource('work-schedules', WorkScheduleController::class);
        Route::patch('/work-schedules/{workSchedule}/toggle-status', [WorkScheduleController::class, 'toggleStatus'])->name('work-schedules.toggle-status');
        Route::get('/work-schedules-assign', [WorkScheduleController::class, 'assign'])->name('work-schedules.assign');
        Route::post('/work-schedules-assign', [WorkScheduleController::class, 'storeAssignment'])->name('work-schedules.store-assignment');

        // Departments routes
        Route::resource('departments', DepartmentController::class);
        Route::patch('/departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
        Route::patch('/departments/{department}/set-manager', [DepartmentController::class, 'setManager'])->name('departments.set-manager');

        // Positions routes
        Route::resource('positions', PositionController::class);
        Route::patch('/positions/{position}/toggle-status', [PositionController::class, 'toggleStatus'])->name('positions.toggle-status');
        Route::get('/positions/by-department', [PositionController::class, 'getPositionsByDepartment'])->name('positions.by-department');

        // Roles routes
        Route::resource('roles', RoleController::class);
        Route::patch('/roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
        Route::patch('/roles/{role}/set-default', [RoleController::class, 'setDefault'])->name('roles.set-default');
        Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::patch('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
        Route::post('/roles/{role}/assign-users', [RoleController::class, 'assignUsers'])->name('roles.assign-users');
        Route::delete('/roles/{role}/remove-user', [RoleController::class, 'removeUser'])->name('roles.remove-user');

        // Attendance data (admin) - Untuk melihat data real-time
        Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/export-pdf', [\App\Http\Controllers\Admin\AttendanceController::class, 'exportPdf'])->name('attendance.export-pdf');

        // Attendance reports (admin) - Untuk laporan dan analisis
        Route::prefix('attendance-reports')->name('attendance.reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'index'])->name('index');
            Route::get('/summary', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'summary'])->name('summary');
            Route::get('/export-pdf', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'exportPdf'])->name('export-pdf');
            // Edit attendance modal (for popup)
            Route::get('/edit/{id}', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'edit'])->name('edit');
            Route::post('/update', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'update'])->name('update');
        });

        // (moved) Attendance corrections approval routes are defined outside the admin role group
    });

    // Employee routes
    Route::middleware(['employee', \App\Http\Middleware\EnsureEmployeeProfileComplete::class])->group(function () {
        Route::prefix('employee')->name('employee.')->group(function () {
            // Profile routes (all employees can access)
            Route::get('/profile', [EmployeeProfileController::class, 'index'])->name('profile.index');
            Route::post('/profile/update', [EmployeeProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/change-password', [EmployeeProfileController::class, 'changePassword'])->name('profile.change-password');

            // Attendance routes with permission check
            Route::middleware('permission:attendance.checkin,attendance.checkout')->group(function () {
                Route::get('/attendance', [EmployeeAttendanceController::class, 'index'])->name('attendance.index');
                Route::get('/attendance/locations', [EmployeeAttendanceController::class, 'locations'])->name('attendance.locations');
                Route::post('/attendance/validate-location', [EmployeeAttendanceController::class, 'validateLocation'])->name('attendance.validate-location');
                Route::post('/attendance/validate-location-checkout', [EmployeeAttendanceController::class, 'validateLocationCheckOut'])->name('attendance.validate-location-checkout');
                Route::get('/attendance/location', [EmployeeAttendanceController::class, 'getLocation'])->name('attendance.location');
            });

            Route::middleware('permission:attendance.checkin')->group(function () {
                Route::post('/attendance/check-in', [EmployeeAttendanceController::class, 'checkIn'])->name('attendance.checkin');
            });

            Route::middleware('permission:attendance.checkout')->group(function () {
                Route::post('/attendance/check-out', [EmployeeAttendanceController::class, 'checkOut'])->name('attendance.checkout');
            });

            Route::middleware('permission:attendance.view')->group(function () {
                Route::get('/attendance/history', [EmployeeAttendanceController::class, 'history'])->name('attendance.history');
                Route::get('/attendance/export-pdf', [EmployeeAttendanceController::class, 'exportPdf'])->name('attendance.export-pdf');
            });

            // Attendance corrections (employee submit & history)
            Route::middleware('permission:attendance.corrections.request')->group(function () {
                Route::get('/attendance/corrections', [EmployeeAttendanceCorrectionController::class, 'index'])->name('attendance.corrections.index');
                Route::get('/attendance/corrections/create', [EmployeeAttendanceCorrectionController::class, 'create'])->name('attendance.corrections.create');
                Route::post('/attendance/corrections', [EmployeeAttendanceCorrectionController::class, 'store'])->name('attendance.corrections.store');
                Route::get('/attendance/corrections/{correction}', [EmployeeAttendanceCorrectionController::class, 'show'])->name('attendance.corrections.show');
            });

            // Work Schedule routes
            Route::middleware('permission:schedules.view')->group(function () {
                Route::get('/schedule', [EmployeeAttendanceController::class, 'schedule'])->name('schedule.index');
            });
        });
    });

    // Alias route (non-prefixed name) for attendance corrections index used in tests
    Route::middleware(['auth', 'employee', 'permission:attendance.corrections.request'])->get('/employee/attendance/corrections', [EmployeeAttendanceCorrectionController::class, 'index'])->name('attendance.corrections.index');
});

// Backwards-compatible alias route used by tests and some older callers.
// Redirect directly to the employee attendance corrections URL to avoid
// relying on a named route that may not exist in all route registration orders.
Route::get('/attendance-corrections', function () {
    return redirect('/employee/attendance/corrections');
})->name('attendance.corrections.index');

// Attendance corrections (approval) accessible to Manager/HR/Admin by permission, but not restricted to role:admin
Route::middleware(['auth', 'permission:attendance.corrections.approve'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/attendance-corrections', [AdminAttendanceCorrectionController::class, 'index'])->name('attendance-corrections.index');
    Route::get('/attendance-corrections/{attendanceCorrection}', [AdminAttendanceCorrectionController::class, 'show'])->name('attendance-corrections.show');
    Route::patch('/attendance-corrections/{attendanceCorrection}/approve-manager', [AdminAttendanceCorrectionController::class, 'approveManager'])->name('attendance-corrections.approve-manager');
    Route::patch('/attendance-corrections/{attendanceCorrection}/approve-hr', [AdminAttendanceCorrectionController::class, 'approveHr'])->name('attendance-corrections.approve-hr');
    Route::patch('/attendance-corrections/{attendanceCorrection}/reject', [AdminAttendanceCorrectionController::class, 'reject'])->name('attendance-corrections.reject');
});
