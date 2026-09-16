<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SsoClient;
use App\Models\SsoUserApplicationAccess;

class SsoApplicationRole extends Model
{
    protected $fillable = [
        'sso_client_id',
        'code',
        'name',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function client()
    {
        return $this->belongsTo(SsoClient::class, 'sso_client_id');
    }

    public function userAccess()
    {
        return $this->hasMany(SsoUserApplicationAccess::class);
    }
}