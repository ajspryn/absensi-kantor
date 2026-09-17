# SSO Integration Contract for External App / AI Agent

You are integrating a separate application with this project as an external client using OAuth 2.0 Authorization Code flow with JWT access token.

This project is the SSO provider.

## Provider details

Base URL:

```text
https://absensi.bprsbtb.co.id
```

## Required flow

### Step 1: Redirect user to provider

```text
GET https://absensi.bprsbtb.co.id/oauth/authorize
    ?response_type=code
    &client_id=CLIENT_ID
    &redirect_uri=https%3A%2F%2Fapp-client.example.com%2Fauth%2Fcallback
    &scope=openid%20profile%20email
    &state=RANDOM_STATE
```

Requirements:

- client_id is issued by the provider
- redirect_uri must exactly match the registered callback URL in the provider
- state must be randomly generated and stored in session/server before redirect
- after callback, compare the returned state with the stored value

### Step 2: Handle callback

The provider redirects back to:

```text
https://app-client.example.com/auth/callback?code=CODE_FROM_PROVIDER&state=RANDOM_STATE
```

The external app must:

- read code
- read state
- validate state
- reject login if state mismatch
- continue only if valid

### Step 3: Exchange authorization code for access token

```text
POST https://absensi.bprsbtb.co.id/api/oauth/token
Content-Type: application/json

{
  "grant_type": "authorization_code",
  "code": "CODE_FROM_CALLBACK",
  "client_id": "CLIENT_ID",
  "client_secret": "CLIENT_SECRET",
  "redirect_uri": "https://app-client.example.com/auth/callback"
}
```

Expected response:

```json
{
    "access_token": "jwt-token-here",
    "token_type": "Bearer",
    "expires_in": 3600
}
```

Important:

- client_secret must never be exposed to frontend or browser
- this request must happen from backend/server side
- the provider validates redirect URI and client_secret

### Step 4: Fetch user information

```text
GET https://absensi.bprsbtb.co.id/api/oauth/userinfo
Authorization: Bearer ACCESS_TOKEN
```

Expected response:

```json
{
    "sub": "15",
    "name": "Budi",
    "full_name": "Budi Santoso",
    "email": "budi@example.com",
    "email_verified": true,
    "phone": "081234567890",
    "mobile": "082233445566",
    "employee_id": "EMP001",
    "address": "Jl. Mawar No. 10",
    "gender": "M",
    "department": "HRD",
    "position": "Staff",
    "hire_date": "2024-01-15",
    "birth_date": "1995-05-10",
    "identity_role": "employee",
    "application": "Portal Keuangan",
    "application_role": "approver",
    "roles": ["approver"]
}
```

## Application roles and authorization rules

Use application_role or roles as the authorization signal inside the external app.

Do NOT treat identity_role as application permission.

- identity_role = global role inside Absensi, e.g. employee
- application_role = role for this external application, e.g. approver

## Copy-paste connection procedure

Implement the integration in this order:

1. Register the application in Absensi **Admin -> Aplikasi SSO**.
2. Use one client for local development and another client for production.
3. Register the exact callback URL. Examples:

- Local: `http://finboard.test/auth/callback`
- Production: `https://finboard.corebtb.com/auth/callback`

4. Store the generated `client_id` and `client_secret` only in the backend `.env`.
5. Configure:

```env
SSO_ENABLED=true
SSO_ISSUER=https://absensi.bprsbtb.co.id
SSO_CLIENT_ID=CLIENT_ID_FROM_ABSENSI
SSO_CLIENT_SECRET=CLIENT_SECRET_FROM_ABSENSI
SSO_REDIRECT_URI=https://your-app.example.com/auth/callback
SSO_SCOPE="openid profile email"
SSO_AUTHORIZE_URL=https://absensi.bprsbtb.co.id/oauth/authorize
SSO_TOKEN_URL=https://absensi.bprsbtb.co.id/api/oauth/token
SSO_USERINFO_URL=https://absensi.bprsbtb.co.id/api/oauth/userinfo
SSO_ROLES_URL=https://absensi.bprsbtb.co.id/api/oauth/roles
```

6. Run `php artisan optimize:clear` and `php artisan config:cache`.
7. Register the application's local roles by calling `/api/oauth/roles`, or run `php artisan sso:sync-roles` for Finboard.
8. Ask the Absensi administrator to assign the synchronized application role to each user.
9. Redirect the browser to the authorize endpoint and complete the callback/token/userinfo flow described below.

Do not manually recreate Finboard roles in Absensi. Absensi only assigns the roles received from Finboard.

The external app should map the user to local role/permission based on application_role or roles.

## User identity rules

- Use sub as the external user identifier
- Do not use email as primary key
- If the user is not assigned access to the external app in the Absensi admin panel, the authorize request is rejected with HTTP 403

## Role registration flow

The external application owns the definition of its application roles. Absensi does not invent or replace these roles; it stores a synchronized copy for user assignment and authorization. The external app must register its roles through:

```text
POST https://absensi.bprsbtb.co.id/api/oauth/roles
Content-Type: application/json

{
  "client_id": "CLIENT_ID",
  "client_secret": "CLIENT_SECRET",
  "roles": [
    {"code": "finance_admin", "name": "Finance Admin"},
    {"code": "approver", "name": "Approver"},
    {"code": "staff", "name": "Staff"}
  ]
}
```

Role code rules:

- lowercase letters only
- numbers allowed
- allowed punctuation: . \_ -
- example valid: finance_admin, approver, staff

Synchronize roles during deployment and whenever the external role catalog changes. For Finboard, run:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan sso:sync-roles
```

The command reads the roles used by Finboard and sends them to the provider. The login flow also attempts synchronization before redirecting the browser, but deployment synchronization is recommended so roles are available in the Absensi admin screen before the first login.

The Absensi admin selects a role for each employee only after synchronization. Do not ask the administrator to manually recreate roles that already exist in the external application.

## Security requirements

- use HTTPS everywhere
- do not store client_secret in frontend or browser storage
- validate state on callback
- validate redirect_uri
- use JWT access token for authenticated requests
- treat access token as bearer token only

## Recommended implementation for external app

1. Generate random state
2. Store state in session or secure server-side storage
3. Redirect browser to /oauth/authorize
4. Receive callback with code and state
5. Validate state
6. Send backend request to /api/oauth/token
7. Receive JWT access_token
8. Call /api/oauth/userinfo with Authorization: Bearer <token>
9. Map sub to local user record
10. Save application_role / roles into user session or local database
11. Grant app-local authorization using those roles
12. Reject if token or user role is invalid

## Minimal architecture logic

```text
External App Frontend -> redirect to provider /oauth/authorize
Provider -> redirect back with code + state
External App Backend -> POST /api/oauth/token
External App Backend -> GET /api/oauth/userinfo
External App Backend -> create local user/session and role mapping
```

## Troubleshooting checklist

### 400 invalid client or redirect URI

Common causes:

- redirect_uri mismatch
- client_id mismatch
- app is inactive
- callback URL is not registered exactly

Fix:

- Use the exact callback URL registered in the admin screen.
- Confirm the client_id matches the generated client.
- Ensure the app is active.

### 403 user not authorized for this application

Common causes:

- user has no access assignment
- role not assigned
- app access disabled

Fix:

- Assign the user access in the admin SSO screen.
- Ensure the role is active.

### 401 invalid_client

Common causes:

- wrong client_secret
- wrong client_id
- different app secret used

Fix:

- Use the exact credentials generated for that app.
- Keep client_secret in server-side config only.

### 400 invalid_grant

Common causes:

- code already used
- code expired
- redirect_uri mismatch during token exchange

Fix:

- Repeat the full login flow and use a fresh code.
- Use the same redirect_uri in authorize and token exchange.

### Userinfo request fails

Common causes:

- missing bearer token
- expired JWT
- wrong client access assignment

Fix:

- Use Authorization: Bearer <token>
- Confirm the token is valid and not expired.
- Ensure the user is assigned access to the client.

## Final instruction to AI / code generator

Build the integration using OIDC/OAuth Authorization Code flow with JWT bearer tokens.

Do not implement direct login by username/password from the external app.
Do not bypass the provider.
Do not trust identity_role as app permission.
Use sub as user id.
Use application_role or roles for local authorization.
Validate state and redirect_uri strictly.
Never expose client_secret to the frontend.

This project is the SSO provider. The external app is just the client.
