<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = [
        'employee_id', 'relation', 'name', 'gender', 'last_education', 'last_job', 'age'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
