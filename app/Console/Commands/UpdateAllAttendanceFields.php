<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;

class UpdateAllAttendanceFields extends Command
{
    protected $signature = 'attendance:update-fields';
    protected $description = 'Update all attendance records to recalculate status, working_hours, and schedule_status based on check_in and check_out';

    public function handle()
    {
        $bar = $this->output->createProgressBar(Attendance::count());
        $bar->start();

        Attendance::with(['employee.workSchedule'])->chunk(100, function ($attendances) use ($bar) {
            foreach ($attendances as $attendance) {
                // Status
                $employee = $attendance->employee;
                $workSchedule = $employee ? $employee->workSchedule : null;
                $startTime = $workSchedule ? $workSchedule->start_time : '08:00:00';
                $attendance->status = 'absent';
                if ($attendance->check_in) {
                    $checkInTime = \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s');
                    if ($checkInTime > $startTime) {
                        $attendance->status = 'late';
                    } else {
                        $attendance->status = 'present';
                    }
                }
                // Working hours
                if ($attendance->check_in && $attendance->check_out) {
                    $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                    $checkOut = \Carbon\Carbon::parse($attendance->check_out);
                    if ($checkOut > $checkIn) {
                        $attendance->working_hours = $checkIn->diffInMinutes($checkOut);
                    } else {
                        $attendance->working_hours = 0;
                    }
                } else {
                    $attendance->working_hours = 0;
                }
                // Schedule status
                $attendance->calculateWorkingHours();
                $attendance->calculateScheduleStatus();
                $attendance->save();
                $bar->advance();
            }
        });
        $bar->finish();
        $this->info("\nSemua data absensi berhasil diupdate.");
    }
}
