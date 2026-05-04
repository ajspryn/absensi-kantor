<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    protected $fillable = [
        'employee_id', 'name', 'address', 'relation', 'phone', 'priority'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
