<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = [
        'event',
        'description',
        'level',
        'ip_address'
    ];
}
