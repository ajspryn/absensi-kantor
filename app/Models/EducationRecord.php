<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationRecord extends Model
{
     protected $fillable = [
          'employee_id',
          'school_name',
          'city',
          'major',
          'start_year',
          'end_year',
          'status'
     ];

     public function employee()
     {
          return $this->belongsTo(Employee::class);
     }
}
