<?php

namespace App\Http\Controllers;

use App\Models\SsoAuthorizationCode;
use App\Models\SsoApplicationRole;
use App\Models\SsoClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class OAuthController extends Controller
{
    public function authorize(Request $request)
    {
        $validated = $request->validate([
            'response_type' => ['required', 'in:code'],
            'client_id' => ['required', 'string'],
            'redirect_uri' => ['required', 'url'],
            'scope' => ['nullable', 'string'],
            'state' => ['nullable', 'string', 'max:512'],
        ]);

        $client = SsoClient::where('client_id', $validated['client_id'])
            ->where('is_active', true)
            ->first();

        abort_unless($client && $client->acceptsRedirectUri($validated['redirect_uri']), 400, 'Invalid client or redirect URI.');
        abort_unless($client->allowsUser($request->user()), 403, 'User is not authorized for this application.');

        $scopes = collect(preg_split('/\s+/', trim($validated['scope'] ?? 'openid profile email')))
            ->filter()
            ->unique()
            ->values();
        $allowedScopes = config('sso.allowed_scopes', ['openid', 'profile', 'email']);
        if (! is_array($allowedScopes) || $allowedScopes === []) {
            $allowedScopes = ['openid', 'profile', 'email'];
        }

        abort_unless($scopes->every(fn(string $scope) => in_array($scope, $allowedScopes, true)), 400, 'Invalid scope.');

        $rawCode = Str::random(96);
        SsoAuthorizationCode::create([
            'code_hash' => hash('sha256', $rawCode),
            'sso_client_id' => $client->id,
            'user_id' => $request->user()->id,
            'redirect_uri' => $validated['redirect_uri'],
            'scopes' => $scopes->all(),
            'expires_at' => now()->addSeconds(max(30, (int) config('sso.authorization_code_ttl', 120))),
        ]);

        return redirect()->away($this->appendQuery($validated['redirect_uri'], [
            'code' => $rawCode,
            'state' => $validated['state'] ?? null,
        ]));
    }

    public function token(Request $request)
    {
        $validated = $request->validate([
            'grant_type' => ['required', 'in:authorization_code'],
            'code' => ['required', 'string'],
            'client_id' => ['required', 'string'],
            'client_secret' => ['required', 'string'],
            'redirect_uri' => ['required', 'url'],
        ]);

        $client = SsoClient::where('client_id', $validated['client_id'])
            ->where('is_active', true)
            ->first();
        if (! $client || ! Hash::check($validated['client_secret'], $client->client_secret)) {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        $tokenData = DB::transaction(function () use ($validated, $client) {
            $authorizationCode = SsoAuthorizationCode::where('code_hash', hash('sha256', $validated['code']))
                ->where('sso_client_id', $client->id)
                ->lockForUpdate()
                ->first();

            if (! $authorizationCode) {
                return ['error' => 'authorization_code_not_found'];
            }
            if ($authorizationCode->used_at) {
                return ['error' => 'authorization_code_used'];
            }
            if ($authorizationCode->expires_at->isPast()) {
                return ['error' => 'authorization_code_expired'];
            }
            if ($authorizationCode->redirect_uri !== $validated['redirect_uri']) {
                return ['error' => 'redirect_uri_mismatch'];
            }

            $user = $authorizationCode->user;
            if (! $user || ! $user->is_active) {
                return ['error' => 'user_inactive'];
            }

            $access = $client->accessForUser($user);
            if (! $access || ! $access->role) {
                return ['error' => 'user_access_missing'];
            }

            $authorizationCode->update(['used_at' => now()]);

            $accessToken = JWTAuth::claims([
                'client_id' => $client->client_id,
                'scope' => implode(' ', $authorizationCode->scopes),
                'roles' => [$access->role->code],
                'iss' => config('sso.issuer'),
            ])->fromUser($user);

            return [
                'success' => true,
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => (int) config('jwt.ttl') * 60,
            ];
        });

        if (! ($tokenData['success'] ?? false)) {
            $error = $tokenData['error'] ?? 'invalid_grant';
            Log::warning('SSO token exchange rejected', [
                'client_id' => $client->client_id,
                'reason' => $error,
                'redirect_uri' => $validated['redirect_uri'],
            ]);

            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => $error,
            ], 400);
        }

        return response()->json($tokenData);
    }

    public function userinfo(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        abort_unless($user && $user->is_active, 401, 'User is inactive.');

        $clientId = JWTAuth::getPayload()->get('client_id');
        $client = SsoClient::where('client_id', $clientId)->where('is_active', true)->first();
        $access = $client?->accessForUser($user);
        abort_unless($client && $access && $access->role, 403, 'User is not authorized for this application.');

        $employee = $user->employee()->with(['department', 'position'])->first();

        return response()->json([
            'sub' => (string) $user->getJWTIdentifier(),
            'name' => $user->name,
            'full_name' => $employee?->full_name ?? $user->name,
            'email' => $user->email,
            'email_verified' => (bool) $user->email_verified_at,
            'phone' => $employee?->phone,
            'mobile' => $employee?->mobile,
            'employee_id' => $employee?->employee_id,
            'address' => $employee?->address,
            'gender' => $employee?->gender,
            'department' => $employee?->department?->name,
            'position' => $employee?->position?->name,
            'hire_date' => $employee?->hire_date?->toDateString(),
            'birth_date' => $employee?->birth_date?->toDateString(),
            'identity_role' => $user->role_name,
            'roles' => [$access->role->code],
            'application_role' => $access->role->code,
            'application' => $client->name,
        ]);
    }

    public function registerRoles(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', 'string'],
            'client_secret' => ['required', 'string'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*.code' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9._-]+$/'],
            'roles.*.name' => ['required', 'string', 'max:150'],
        ]);

        $client = $this->authenticateClient($validated['client_id'], $validated['client_secret']);
        if (! $client) {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        $roles = collect($validated['roles'])->map(function (array $role) use ($client) {
            return SsoApplicationRole::updateOrCreate(
                ['sso_client_id' => $client->id, 'code' => $role['code']],
                ['name' => $role['name'], 'is_active' => true]
            );
        });

        return response()->json([
            'application' => $client->name,
            'roles' => $roles->map(fn(SsoApplicationRole $role) => [
                'code' => $role->code,
                'name' => $role->name,
                'is_active' => $role->is_active,
            ])->values(),
        ]);
    }

    public function roles(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', 'string'],
            'client_secret' => ['required', 'string'],
        ]);

        $client = $this->authenticateClient($validated['client_id'], $validated['client_secret']);
        if (! $client) {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        return response()->json([
            'application' => $client->name,
            'roles' => $client->applicationRoles()->where('is_active', true)->get(['code', 'name']),
        ]);
    }

    private function authenticateClient(string $clientId, string $clientSecret): ?SsoClient
    {
        $client = SsoClient::where('client_id', $clientId)->where('is_active', true)->first();

        return $client && Hash::check($clientSecret, $client->client_secret) ? $client : null;
    }

    private function appendQuery(string $uri, array $parameters): string
    {
        $parameters = array_filter($parameters, fn($value) => $value !== null);
        return $uri . (str_contains($uri, '?') ? '&' : '?') . http_build_query($parameters);
    }
}
