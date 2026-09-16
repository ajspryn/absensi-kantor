<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SsoApplicationRole;
use App\Models\SsoClient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SsoApplicationController extends Controller
{
    public function index()
    {
        $applications = SsoClient::with('applicationRoles')
            ->withCount('userAccess')
            ->latest()
            ->get();

        return view('admin.sso-applications.index', compact('applications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'redirect_uris' => ['required', 'string'],
            'client_id' => ['nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9_-]+$/'],
            'client_secret' => ['nullable', 'string', 'min:32', 'max:128'],
        ]);

        $redirectUris = collect(preg_split('/\r?\n/', $validated['redirect_uris']))
            ->map(fn (string $uri) => trim($uri))
            ->filter()
            ->unique()
            ->values();
        abort_if($redirectUris->isEmpty() || $redirectUris->contains(fn (string $uri) => ! filter_var($uri, FILTER_VALIDATE_URL)), 422, 'Redirect URI tidak valid.');

        $clientId = $validated['client_id'] ?? $this->generateClientId();
        $clientSecret = $validated['client_secret'] ?? $this->generateClientSecret();

        $client = SsoClient::create([
            'client_id' => $clientId,
            'name' => $validated['name'],
            'client_secret' => Hash::make($clientSecret),
            'redirect_uris' => $redirectUris->all(),
            'is_active' => true,
        ]);

        return redirect()->route('admin.sso-applications.index')->with('sso_credentials', [
            'name' => $client->name,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);
    }

    private function generateClientId(): string
    {
        do {
            $value = 'client_' . Str::random(24);
        } while (SsoClient::where('client_id', $value)->exists());

        return $value;
    }

    private function generateClientSecret(): string
    {
        return Str::random(64);
    }

    public function editUserAccess(User $user)
    {
        $user->load('employee', 'ssoApplicationAccess.role', 'ssoApplicationAccess.client');
        $applications = SsoClient::with(['applicationRoles' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $currentAccess = $user->ssoApplicationAccess->keyBy('sso_client_id');

        return view('admin.sso-applications.user-access', compact('user', 'applications', 'currentAccess'));
    }

    public function updateUserAccess(Request $request, User $user)
    {
        $validated = $request->validate([
            'access' => ['nullable', 'array'],
            'access.*' => ['nullable', 'integer'],
        ]);

        DB::transaction(function () use ($validated, $user) {
            foreach (SsoClient::where('is_active', true)->get() as $client) {
                $roleId = $validated['access'][$client->id] ?? null;
                if (! $roleId) {
                    $client->userAccess()->where('user_id', $user->id)->delete();
                    continue;
                }

                $role = SsoApplicationRole::where('id', $roleId)
                    ->where('sso_client_id', $client->id)
                    ->where('is_active', true)
                    ->firstOrFail();
                $client->userAccess()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['sso_application_role_id' => $role->id, 'is_active' => true]
                );
            }
        });

        return redirect()->route('admin.employees.index')->with('success', 'Akses aplikasi SSO berhasil diperbarui.');
    }
}