<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SsoApplicationRole;
use App\Models\SsoClient;
use App\Models\User;

class SsoUserApplicationAccess extends Model
{
    protected $table = 'sso_user_application_access';

    protected $fillable = [
        'user_id',
        'sso_client_id',
        'sso_application_role_id',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(SsoClient::class, 'sso_client_id');
    }

    public function role()
    {
        return $this->belongsTo(SsoApplicationRole::class, 'sso_application_role_id');
    }
}