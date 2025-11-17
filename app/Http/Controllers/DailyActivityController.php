<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Http\Resources\DailyActivityResource;
use Illuminate\Http\Request;

class DailyActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DailyActivity::with('employee')->where('status', 'approved');

        // Optional filters
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $activities = $query->orderBy('date', 'desc')->paginate(15);

        // Ensure employee relation is loaded for all items
        $activities->load('employee');

        return DailyActivityResource::collection($activities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implement if needed
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyActivity $dailyActivity)
    {
        $activity = $dailyActivity->load('employee');

        // Add file URLs to response
        if ($activity->employee && $activity->employee->photo) {
            $activity->employee->photo_url = url("api/files/employee-photos/{$activity->employee->photo}");
        }

        if ($activity->attachments && is_array($activity->attachments)) {
            $activity->attachment_urls = array_map(function ($attachment) {
                return url("api/files/daily-activity-attachments/{$attachment}");
            }, $activity->attachments);
        }

        return response()->json($activity);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyActivity $dailyActivity)
    {
        // Implement if needed
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyActivity $dailyActivity)
    {
        // Implement if needed
    }
}
