<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyActivity;
use App\Models\Employee;
use App\Http\Requests\StoreDailyActivityRequest;
use Illuminate\Support\Facades\Auth;

class DailyActivityController extends Controller
{
     public function index(Request $request)
     {
          $employee = Auth::user()->employee;
          $query = DailyActivity::where('employee_id', $employee->id)->orderBy('date', 'desc');

          $activities = $query->paginate(15);

          return view('employee.daily_activities.index', compact('activities'));
     }

     public function create()
     {
          return view('employee.daily_activities.create');
     }

     public function store(StoreDailyActivityRequest $request)
     {
          $employee = Auth::user()->employee;

          $data = $request->only(['date', 'start_time', 'end_time', 'title', 'description', 'tasks']);
          $data['employee_id'] = $employee->id;

          // Handle attachments upload
          $attachmentPaths = [];
          if ($request->hasFile('attachments')) {
               foreach ($request->file('attachments') as $file) {
                    if ($file && $file->isValid()) {
                         $path = $file->store('daily_activity_attachments', 'public');
                         $attachmentPaths[] = $path;
                    }
               }
          }

          $data['attachments'] = $attachmentPaths ?: null;

          $activity = DailyActivity::create($data);

          return redirect()->route('employee.daily-activities.index')->with('success', 'Daily activity submitted');
     }

     public function show(DailyActivity $dailyActivity)
     {
          $user = Auth::user();
          $employee = $user->employee;

          // Allow owner or manager of same department (check in controller for simplicity)
          if ($dailyActivity->employee_id !== $employee->id) {
               // if user is manager, allow if same department
               if (!$user->role || $user->role->name !== 'Manager' || $dailyActivity->employee->department_id !== $employee->department_id) {
                    abort(403);
               }
          }

          return view('employee.daily_activities.show', ['activity' => $dailyActivity]);
     }
}
