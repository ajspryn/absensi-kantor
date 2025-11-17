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

        // Filtering: default to today's activities, or use filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('date', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('date', $request->end_date);
        } else {
            // Default: show today's activities
            $query->whereDate('date', today());
        }

        $activities = $query->paginate(15)->withQueryString();

        // Summary tiles - use same date filtering as main query
        $summaryQuery = DailyActivity::where('employee_id', $employee->id);
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $summaryQuery->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $summaryQuery->whereDate('date', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $summaryQuery->whereDate('date', $request->end_date);
        } else {
            // Default: show today's activities
            $summaryQuery->whereDate('date', today());
        }

        $total = $summaryQuery->count();
        $approved = (clone $summaryQuery)->where('status', 'approved')->count();
        $pending = (clone $summaryQuery)->where('status', 'submitted')->count();
        $withAttachments = (clone $summaryQuery)->whereNotNull('attachments')->count();

        return view('employee.daily_activities.index', compact('activities', 'total', 'approved', 'pending', 'withAttachments'));
    }

    /** Export activities as CSV with current filters */
    public function export(Request $request)
    {
        $employee = Auth::user()->employee;
        $query = DailyActivity::where('employee_id', $employee->id)->orderBy('date', 'desc');

        $filterType = $request->get('filter_type', 'month');
        if ($filterType === 'date_range' && $request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } else {
            $month = $request->get('month', now()->format('Y-m'));
            try {
                $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
                $end = \Carbon\Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');
                $query->whereBetween('date', [$start, $end]);
            } catch (\Exception $e) {
            }
        }

        $rows = $query->get();

        $filename = 'daily_activities_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['date', 'title', 'start_time', 'end_time', 'status', 'tasks_count', 'attachments']);
            foreach ($rows as $r) {
                $tasks = $r->tasks ?? [];
                $attachments = $r->attachments ? implode('|', $r->attachments) : '';
                fputcsv($out, [$r->date->format('Y-m-d'), $r->title, $r->start_time, $r->end_time, $r->status, count($tasks), $attachments]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
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

    /** Show edit form */
    public function edit(DailyActivity $dailyActivity)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // only owner or manager same dept can edit
        if ($dailyActivity->employee_id !== ($employee->id ?? null)) {
            if (!($user->role && strtolower($user->role->name) === 'manager' && $dailyActivity->employee && $employee && $dailyActivity->employee->department_id === $employee->department_id)) {
                abort(403);
            }
        }

        return view('employee.daily_activities.edit', ['activity' => $dailyActivity]);
    }

    /** Update an existing activity */
    public function update(StoreDailyActivityRequest $request, DailyActivity $dailyActivity)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if ($dailyActivity->employee_id !== ($employee->id ?? null)) {
            if (!($user->role && strtolower($user->role->name) === 'manager' && $dailyActivity->employee && $employee && $dailyActivity->employee->department_id === $employee->department_id)) {
                abort(403);
            }
        }

        $data = $request->only(['date', 'start_time', 'end_time', 'title', 'description', 'tasks']);

        // Handle attachments upload (append)
        $attachmentPaths = $dailyActivity->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('daily_activity_attachments', 'public');
                    $attachmentPaths[] = $path;
                }
            }
        }

        $data['attachments'] = $attachmentPaths ?: null;

        $dailyActivity->update($data);

        return redirect()->route('employee.daily-activities.show', $dailyActivity->id)->with('success', 'Daily activity diperbarui');
    }

    /** Delete an activity */
    public function destroy(DailyActivity $dailyActivity)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if ($dailyActivity->employee_id !== ($employee->id ?? null)) {
            if (!($user->role && strtolower($user->role->name) === 'manager' && $dailyActivity->employee && $employee && $dailyActivity->employee->department_id === $employee->department_id)) {
                abort(403);
            }
        }

        // delete attached files from storage if any
        if ($dailyActivity->attachments && is_array($dailyActivity->attachments)) {
            foreach ($dailyActivity->attachments as $p) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($p);
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }

        $dailyActivity->delete();

        return redirect()->route('employee.daily-activities.index')->with('success', 'Daily activity dihapus');
    }

    /**
     * Update a single task's completed status.
     * Expects request field `completed` (0/1) or will toggle if absent.
     */
    public function updateTask(Request $request, DailyActivity $dailyActivity, $index)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if ($dailyActivity->employee_id !== $employee->id) {
            abort(403);
        }

        $tasks = $dailyActivity->tasks ?? [];

        if (!isset($tasks[$index])) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Determine new completed value
        if ($request->has('completed')) {
            $completed = (int) $request->input('completed') ? 1 : 0;
        } else {
            $completed = empty($tasks[$index]['completed']) ? 1 : 0;
        }

        $tasks[$index]['completed'] = $completed;

        $dailyActivity->tasks = $tasks;
        $dailyActivity->save();

        return response()->json(['success' => true, 'completed' => $completed]);
    }

    /**
     * Add one or more attachments to an existing daily activity.
     */
    public function addAttachments(Request $request, DailyActivity $dailyActivity)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Allow owner, or allow manager of same department, or allow user with department view permission
        $isOwner = $dailyActivity->employee_id === ($employee->id ?? null);
        $isManagerSameDept = false;
        if ($user->role && strtolower($user->role->name) === 'manager' && $dailyActivity->employee && $employee) {
            $isManagerSameDept = $dailyActivity->employee->department_id === $employee->department_id;
        }

        $canBypass = false;
        if ($user->role && is_string($user->role->name)) {
            $roleName = strtolower($user->role->name);
            // allow common elevated roles to bypass ownership check
            $canBypass = in_array($roleName, ['manager', 'admin', 'hr']);
        }

        if (!($isOwner || $isManagerSameDept || $canBypass)) {
            abort(403);
        }

        $request->validate([
            'attachments' => 'required',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $attachmentPaths = $dailyActivity->attachments ?? [];

        foreach ($request->file('attachments') as $file) {
            if ($file && $file->isValid()) {
                $path = $file->store('daily_activity_attachments', 'public');
                $attachmentPaths[] = $path;
            }
        }

        $dailyActivity->attachments = $attachmentPaths ?: null;
        $dailyActivity->save();

        return redirect()->back()->with('success', 'Foto kegiatan berhasil ditambahkan');
    }
}
