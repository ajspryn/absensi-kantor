<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SsoClient;
use App\Models\User;

class SsoAuthorizationCode extends Model
{
    protected $fillable = [
        'code_hash',
        'sso_client_id',
        'user_id',
        'redirect_uri',
        'scopes',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'scopes' => 'array',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(SsoClient::class, 'sso_client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}