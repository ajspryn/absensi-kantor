<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingRecord extends Model
{
     protected $fillable = [
          'employee_id',
          'course_name',
          'organizer',
          'city',
          'duration',
          'year',
          'paid_by'
     ];

     public function employee()
     {
          return $this->belongsTo(Employee::class);
     }
}
