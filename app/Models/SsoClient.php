<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SsoAuthorizationCode;
use App\Models\SsoApplicationRole;
use App\Models\SsoUserApplicationAccess;
use App\Models\User;

class SsoClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'client_secret',
        'redirect_uris',
        'is_active',
    ];

    protected $hidden = ['client_secret'];

    protected $casts = [
        'redirect_uris' => 'array',
        'is_active' => 'boolean',
    ];

    public function authorizationCodes()
    {
        return $this->hasMany(SsoAuthorizationCode::class);
    }

    public function applicationRoles()
    {
        return $this->hasMany(SsoApplicationRole::class);
    }

    public function userAccess()
    {
        return $this->hasMany(SsoUserApplicationAccess::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'sso_user_application_access')
            ->withPivot(['sso_application_role_id', 'is_active'])
            ->withTimestamps();
    }

    public function acceptsRedirectUri(string $redirectUri): bool
    {
        return in_array($redirectUri, $this->redirect_uris ?? [], true);
    }

    public function accessForUser(User $user): ?SsoUserApplicationAccess
    {
        return $this->userAccess()
            ->with('role')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->whereHas('role', fn ($query) => $query->where('is_active', true))
            ->first();
    }

    public function allowsUser(User $user): bool
    {
        return (bool) $this->accessForUser($user);
    }
}