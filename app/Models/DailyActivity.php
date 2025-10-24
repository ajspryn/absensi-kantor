<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyActivity extends Model
{
     use HasFactory;

     protected $fillable = [
          'employee_id',
          'date',
          'start_time',
          'end_time',
          'title',
          'description',
          'tasks',
          'attachments',
          'status',
     ];

     protected $casts = [
          'date' => 'date',
          'tasks' => 'array',
          'attachments' => 'array',
     ];

     public function employee()
     {
          return $this->belongsTo(Employee::class);
     }
}
