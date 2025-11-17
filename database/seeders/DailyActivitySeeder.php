<?php

namespace Database\Seeders;

use App\Models\DailyActivity;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class DailyActivitySeeder extends Seeder
{
     /**
      * Run the database seeds.
      */
     public function run(): void
     {
          // Get existing employees
          $employees = Employee::all();

          if ($employees->isEmpty()) {
               // Create some employees first
               Employee::factory()->create([
                    'user_id' => 2, // employee user
                    'name' => 'John Doe',
                    'employee_id' => 'EMP001',
                    'department_id' => 1,
                    'position_id' => 1,
               ]);

               Employee::factory()->create([
                    'user_id' => 2,
                    'name' => 'Jane Smith',
                    'employee_id' => 'EMP002',
                    'department_id' => 1,
                    'position_id' => 2,
               ]);

               $employees = Employee::all();
          }

          // Create sample daily activities
          DailyActivity::create([
               'employee_id' => $employees->first()->id,
               'date' => now()->toDateString(),
               'start_time' => '08:00:00',
               'end_time' => '17:00:00',
               'title' => 'Pengembangan Fitur Absensi',
               'description' => 'Mengerjakan pengembangan fitur absensi online',
               'tasks' => [
                    [
                         'task' => 'Analisis kebutuhan sistem',
                         'status' => 'completed',
                         'time_spent' => '2 jam'
                    ],
                    [
                         'task' => 'Desain database schema',
                         'status' => 'completed',
                         'time_spent' => '3 jam'
                    ],
                    [
                         'task' => 'Implementasi API endpoints',
                         'status' => 'in_progress',
                         'time_spent' => '4 jam'
                    ],
                    [
                         'task' => 'Testing dan debugging',
                         'status' => 'pending',
                         'time_spent' => '2 jam'
                    ]
               ],
               'attachments' => [
                    'file1.pdf',
                    'screenshot1.png'
               ],
               'status' => 'in_progress'
          ]);

          DailyActivity::create([
               'employee_id' => $employees->last()->id,
               'date' => now()->subDay()->toDateString(),
               'start_time' => '09:00:00',
               'end_time' => '18:00:00',
               'title' => 'Meeting dengan Client',
               'description' => 'Diskusi kebutuhan project baru dengan client',
               'tasks' => [
                    [
                         'task' => 'Persiapan presentasi',
                         'status' => 'completed',
                         'time_spent' => '1.5 jam'
                    ],
                    [
                         'task' => 'Meeting dengan client',
                         'status' => 'completed',
                         'time_spent' => '2 jam'
                    ],
                    [
                         'task' => 'Follow up email',
                         'status' => 'completed',
                         'time_spent' => '30 menit'
                    ]
               ],
               'attachments' => [
                    'proposal.pdf',
                    'meeting_notes.docx'
               ],
               'status' => 'completed'
          ]);

          DailyActivity::create([
               'employee_id' => $employees->first()->id,
               'date' => now()->subDays(2)->toDateString(),
               'start_time' => '08:30:00',
               'end_time' => '16:30:00',
               'title' => 'Maintenance Server',
               'description' => 'Melakukan maintenance rutin pada server production',
               'tasks' => [
                    [
                         'task' => 'Backup database',
                         'status' => 'completed',
                         'time_spent' => '1 jam'
                    ],
                    [
                         'task' => 'Update security patches',
                         'status' => 'completed',
                         'time_spent' => '2 jam'
                    ],
                    [
                         'task' => 'Monitoring performance',
                         'status' => 'completed',
                         'time_spent' => '3 jam'
                    ]
               ],
               'attachments' => [
                    'backup_log.txt',
                    'performance_report.pdf'
               ],
               'status' => 'completed'
          ]);

          // Add more approved activities for testing
          DailyActivity::create([
               'employee_id' => $employees->first()->id,
               'date' => now()->subDays(3)->toDateString(),
               'start_time' => '09:00:00',
               'end_time' => '17:00:00',
               'title' => 'Review Kode Aplikasi',
               'description' => 'Melakukan code review untuk fitur absensi terbaru',
               'tasks' => [
                    [
                         'task' => 'Review pull request',
                         'status' => 'completed',
                         'time_spent' => '2 jam'
                    ],
                    [
                         'task' => 'Testing functionality',
                         'status' => 'completed',
                         'time_spent' => '1.5 jam'
                    ],
                    [
                         'task' => 'Feedback dan suggestions',
                         'status' => 'completed',
                         'time_spent' => '1 jam'
                    ]
               ],
               'attachments' => [
                    'code_review_notes.pdf',
                    'test_results.xlsx'
               ],
               'status' => 'approved'
          ]);

          DailyActivity::create([
               'employee_id' => $employees->last()->id,
               'date' => now()->subDays(4)->toDateString(),
               'start_time' => '08:00:00',
               'end_time' => '16:00:00',
               'title' => 'Training Tim Development',
               'description' => 'Melakukan training untuk tim development mengenai best practices',
               'tasks' => [
                    [
                         'task' => 'Persiapan materi training',
                         'status' => 'completed',
                         'time_spent' => '3 jam'
                    ],
                    [
                         'task' => 'Sesi training',
                         'status' => 'completed',
                         'time_spent' => '4 jam'
                    ],
                    [
                         'task' => 'Evaluasi dan feedback',
                         'status' => 'completed',
                         'time_spent' => '1 jam'
                    ]
               ],
               'attachments' => [
                    'training_materials.pptx',
                    'attendance_list.xlsx',
                    'feedback_form.pdf'
               ],
               'status' => 'approved'
          ]);
     }
}
