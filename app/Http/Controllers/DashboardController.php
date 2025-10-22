<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Models\PasswordResetRequest;
use App\Models\ActivityLog;
use App\Models\SystemLog;

class DashboardController extends Controller
{
    public function index()
    {
        // Get user with role relationship
        // Get authenticated user and eager load role
        /** @var User|null $user */
        $user = Auth::user();

        // If not authenticated send to login
        if (!$user) {
            return redirect()->route('login');
        }

        // Eager-load role if missing
        $user->loadMissing('role');

        // If user has admin dashboard permission or is system/admin role, show admin dashboard
        if ($this->shouldShowAdminDashboard($user)) {
            return $this->adminDashboard();
        }

        return $this->employeeDashboard();
    }

    /**
     * Determine whether the given user should see the admin dashboard.
     */
    protected function shouldShowAdminDashboard(?User $user): bool
    {
        if (!$user) return false;

        $roleName = optional($user->role)->name ? strtolower(optional($user->role)->name) : null;
        if (in_array($roleName, ['admin', 'super admin', 'system'])) {
            return true;
        }

        // Prefer permission check if available on User
        if (method_exists($user, 'hasPermission') && $user->hasPermission('admin.dashboard')) {
            return true;
        }

        return false;
    }

    private function adminDashboard()
    {
        $totalEmployees = Employee::where('is_active', true)->count();
        $totalDepartments = Department::where('is_active', true)->count();
        $totalPositions = Position::where('is_active', true)->count();
        $totalRoles = Role::where('is_active', true)->count();
        $todayAttendances = Attendance::whereDate('date', today())->count();
        $presentToday = Attendance::whereDate('date', today())
            ->whereNotNull('check_in')
            ->count();

        // Get pending password reset requests
        $pendingResetRequests = PasswordResetRequest::with('user')
            ->pending()
            ->notExpired()
            ->orderBy('created_at', 'desc')
            ->get();

        // Get recent activity logs and system logs
        $activityLogs = class_exists(ActivityLog::class)
            ? ActivityLog::with('user')->latest()->limit(10)->get()
            : collect();
        $systemLogs = class_exists(SystemLog::class)
            ? SystemLog::latest()->limit(10)->get()
            : collect();

        return view('dashboard.admin', compact(
            'totalEmployees',
            'totalDepartments',
            'totalPositions',
            'totalRoles',
            'todayAttendances',
            'presentToday',
            'pendingResetRequests',
            'activityLogs',
            'systemLogs'
        ));
    }

    private function employeeDashboard()
    {

        /** @var User|null $user */
        $user = Auth::user();
        $employee = $user->employee;

        // Fields required to consider profile complete for dashboard purposes
        $requiredFields = ['employee_id', 'full_name', 'email'];

        // If user has no employee record, redirect to complete-profile (unless admin/system)
        if (!$employee) {
            if ($this->shouldShowAdminDashboard($user)) {
                return $this->adminDashboard();
            }
            return redirect()->route('employee.profile.complete');
        }

        // Ensure employee relations are loaded to avoid N+1 and to let accessors work
        $employee->loadMissing(['department', 'position', 'attendances']);

        // Check required fields using Employee model helper
        $missing = $employee->getMissingProfileFields($requiredFields);
        if (!empty($missing)) {
            if ($this->shouldShowAdminDashboard($user)) {
                return $this->adminDashboard();
            }
            return redirect()->route('employee.profile.complete');
        }

        $todayAttendance = $employee->getTodayAttendance();
        $recentAttendances = $employee->attendances()
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        // Calculate monthly stats (current month)
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $today = now();

        $monthlyAttendances = $employee->attendances()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get();

        // Calculate days that have passed in this month (excluding today and future days)
        $daysPassed = $today->day - 1; // Days before today
        $daysWithAttendance = $monthlyAttendances->where('date', '<', $today->startOfDay())->count();

        $weeklyStats = [
            'present' => $monthlyAttendances->where('check_in', '!=', null)->count(),
            'absent' => max(0, $daysPassed - $daysWithAttendance), // Only count past days without attendance
            'leave' => 0 // Will be implemented when leave system is added
        ];

        return view('dashboard.employee', compact(
            'employee',
            'todayAttendance',
            'recentAttendances',
            'weeklyStats'
        ));
    }
}
