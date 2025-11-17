<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyActivity;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class DailyActivityReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        // allow users with a global view permission to see all activities
        $canViewAll = $user->role && $user->role->hasPermission('daily_activities.view_all');

        if (!$employee && !$canViewAll) {
            abort(403);
        }

        // If user can view all activities (admin), don't restrict by department.
        if ($canViewAll) {
            $query = DailyActivity::query();
        } else {
            // Manager should only see activities for employees in their department
            $query = DailyActivity::whereHas('employee', function ($q) use ($employee) {
                $q->where('department_id', $employee->department_id);
            });
        }

        // Date filtering: use whereDate so records with time components are matched
        // Default: show today's activities if no filter is applied
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = $request->start_date;
            $end = $request->end_date;

            if ($start === $end) {
                // same-day filter
                $query->whereDate('date', $start);
            } else {
                // range filter (inclusive) using date portions
                $query->whereDate('date', '>=', $start)->whereDate('date', '<=', $end);
            }
        } elseif ($request->filled('start_date')) {
            $query->whereDate('date', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('date', $request->end_date);
        } else {
            // Default: show today's activities
            $query->whereDate('date', today());
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $activities = $query->with('employee')->orderBy('date', 'desc')->paginate(25)->withQueryString();

        // Provide list of employees for the view. If user can view all, show all employees;
        // otherwise show employees in the manager's department.
        if ($canViewAll) {
            $employees = Employee::orderBy('full_name')->get();
        } else {
            $employees = Employee::where('department_id', $employee->department_id)
                ->orderBy('full_name')
                ->get();
        }

        return view('manager.daily_activities.index', compact('activities', 'employees'));
    }

    /** Export activities visible to manager as CSV */
    public function export(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $canViewAll = $user->role && $user->role->hasPermission('daily_activities.view_all');

        if (!$employee && !$canViewAll) {
            abort(403);
        }

        if ($canViewAll) {
            $query = DailyActivity::query();
        } else {
            $query = DailyActivity::whereHas('employee', function ($q) use ($employee) {
                $q->where('department_id', $employee->department_id);
            });
        }

        // Date filtering for export: same logic as index
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = $request->start_date;
            $end = $request->end_date;

            if ($start === $end) {
                $query->whereDate('date', $start);
            } else {
                $query->whereDate('date', '>=', $start)->whereDate('date', '<=', $end);
            }
        } elseif ($request->filled('start_date')) {
            $query->whereDate('date', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('date', $request->end_date);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $rows = $query->with('employee')->orderBy('date', 'desc')->get();

        $filename = 'manager_daily_activities_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['date', 'employee', 'title', 'start_time', 'end_time', 'status', 'tasks_count']);
            foreach ($rows as $r) {
                $tasks = $r->tasks ?? [];
                fputcsv($out, [$r->date->format('Y-m-d'), $r->employee->full_name ?? $r->employee->id, $r->title, $r->start_time, $r->end_time, $r->status, count($tasks)]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(DailyActivity $dailyActivity)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $canViewAll = $user->role && $user->role->hasPermission('daily_activities.view_all');

        if (!$employee && !$canViewAll) {
            abort(403);
        }

        if (!$canViewAll && $dailyActivity->employee->department_id !== $employee->department_id) {
            abort(403);
        }

        return view('manager.daily_activities.show', ['activity' => $dailyActivity]);
    }

    /**
     * Approve a daily activity
     */
    public function approve(Request $request, DailyActivity $dailyActivity)
    {
        $user = Auth::user();
        // fetch manager employee record explicitly to avoid relation caching issues in tests
        $employee = \App\Models\Employee::where('user_id', $user->id)->first();

        $canViewAll = $user->role && $user->role->hasPermission('daily_activities.view_all');

        if ((!$employee && !$canViewAll) || !$dailyActivity->employee) {
            abort(403);
        }

        // allow approve if manager of the department or user can view all and has approve permission
        if (!$canViewAll && $dailyActivity->employee->department_id !== $employee->department_id) {
            abort(403);
        }

        if (!($user->role && $user->role->hasPermission('daily_activities.approve'))) {
            abort(403);
        }

        $dailyActivity->status = 'approved';
        $dailyActivity->save();

        return redirect()->back()->with('success', 'Activity approved');
    }

    /**
     * Reject a daily activity
     */
    public function reject(Request $request, DailyActivity $dailyActivity)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $canViewAll = $user->role && $user->role->hasPermission('daily_activities.view_all');

        if ((!$employee && !$canViewAll) || !$dailyActivity->employee) {
            abort(403);
        }

        if (!$canViewAll && $dailyActivity->employee->department_id !== $employee->department_id) {
            abort(403);
        }

        if (!$user->role || !$user->role->hasPermission('daily_activities.approve')) {
            abort(403);
        }

        $dailyActivity->status = 'rejected';
        $dailyActivity->save();

        return redirect()->back()->with('success', 'Activity rejected');
    }

    /**
     * Destroy (delete) a daily activity. Admins with view_all + delete permission or
     * managers with department access + delete permission can remove an activity.
     */
    public function destroy(Request $request, DailyActivity $dailyActivity)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $canViewAll = $user->role && $user->role->hasPermission('daily_activities.view_all');

        if ((!$employee && !$canViewAll) || !$dailyActivity->employee) {
            abort(403);
        }

        if (!$canViewAll && $dailyActivity->employee->department_id !== $employee->department_id) {
            abort(403);
        }

        if (!($user->role && $user->role->hasPermission('daily_activities.delete'))) {
            abort(403);
        }

        $dailyActivity->delete();

        return redirect()->route('admin.daily-activities.index')->with('success', 'Activity deleted');
    }
}
