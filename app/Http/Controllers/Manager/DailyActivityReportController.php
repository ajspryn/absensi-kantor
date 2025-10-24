<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyActivity;
use Illuminate\Support\Facades\Auth;

class DailyActivityReportController extends Controller
{
     public function index(Request $request)
     {
          $user = Auth::user();
          $employee = $user->employee;

          if (!$employee) {
               abort(403);
          }

          // Manager should only see activities for employees in their department
          $query = DailyActivity::whereHas('employee', function ($q) use ($employee) {
               $q->where('department_id', $employee->department_id);
          });

          if ($request->has('start_date') && $request->has('end_date')) {
               $query->whereBetween('date', [$request->start_date, $request->end_date]);
          }

          if ($request->filled('employee_id')) {
               $query->where('employee_id', $request->employee_id);
          }

          $activities = $query->with('employee')->orderBy('date', 'desc')->paginate(25)->withQueryString();

          return view('manager.daily_activities.index', compact('activities'));
     }

     public function show(DailyActivity $dailyActivity)
     {
          $user = Auth::user();
          $employee = $user->employee;

          if (!$employee || $dailyActivity->employee->department_id !== $employee->department_id) {
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

          if (!$employee || !$dailyActivity->employee || $dailyActivity->employee->department_id !== $employee->department_id) {
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

          if (!$employee || $dailyActivity->employee->department_id !== $employee->department_id) {
               abort(403);
          }

          if (!$user->role || !$user->role->hasPermission('daily_activities.approve')) {
               abort(403);
          }

          $dailyActivity->status = 'rejected';
          $dailyActivity->save();

          return redirect()->back()->with('success', 'Activity rejected');
     }
}
