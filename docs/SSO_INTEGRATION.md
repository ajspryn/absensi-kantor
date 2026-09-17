# SSO Integration Guide

This project acts as an Identity Provider (IdP) using OAuth 2.0 Authorization Code flow with JWT access tokens. External applications connect to this provider as OAuth clients.

This document is written to be copy-pasteable for another app or AI integration agent. It explains the contract, the required parameters, and the most common failure points.

## 1. Overview

The SSO flow is:

1. Admin registers the external application as an SSO client.
2. The external application receives a client_id and client_secret.
3. The external app redirects the browser to the provider authorization endpoint.
4. The provider authenticates the user and redirects back with an authorization code.
5. The external app exchanges the authorization code for a JWT access token.
6. The external app calls the userinfo endpoint to read user profile and role data.
7. The app maps the returned role to local permissions.

Important rule:

- The external app must not log in by username/password directly.
- The external app must not bypass the provider.
- The provider uses JWT access tokens.

## 2. Provider base URL and environment config

Production provider URL:

```text
https://absensi.bprsbtb.co.id
```

Recommended environment variables:

```env
APP_ENV=production
APP_URL=https://absensi.bprsbtb.co.id
SSO_ISSUER=https://absensi.bprsbtb.co.id
APP_DEBUG=false
JWT_SECRET=your-production-jwt-secret
SESSION_SECURE_COOKIE=true
```

Rules:

- Always use HTTPS in production.
- Do not hardcode the domain into application code.
- Use environment variables to move between server or domain without changing source code.
- The redirect_uri used by the external app must exactly match the registered callback URL.

## 3. Client registration in the SSO admin

The admin should register the external application in the admin SSO screen.

Required data:

- Application name
- Redirect URI(s)
- Client ID and Client Secret
- Active status
- Assigned user access and application roles

The client_id is safe to display. The client_secret is shown only once during creation because it is stored hashed in the database.

A client secret must never be committed to source code or stored in frontend code.

## 4. Redirect the user to the provider

This is the first step in the Google-like SSO flow.

```text
GET https://absensi.bprsbtb.co.id/oauth/authorize
    ?response_type=code
    &client_id=CLIENT_ID
    &redirect_uri=https%3A%2F%2Fapp-client.example.com%2Fauth%2Fcallback
    &scope=openid%20profile%20email
    &state=RANDOM_STATE
```

Required fields:

- response_type = code
- client_id = the registered client ID for the external app
- redirect_uri = exactly the callback URL registered for the client
- scope = openid profile email
- state = a random value generated on the external app before redirect

State requirement:

- Store the value server-side before redirecting.
- After callback, compare the received state to the saved state.
- If they do not match, reject the request.

## 5. Callback handling

After successful login, the provider redirects back to the callback URL.

Example callback URL:

```text
https://app-client.example.com/auth/callback?code=AUTHORIZATION_CODE&state=RANDOM_STATE
```

The external app must:

1. Read the code parameter.
2. Read the state parameter.
3. Validate that state matches the original random state.
4. Reject login if state mismatch occurs.
5. Continue only if validation passes.

If the redirect URI does not match exactly, the provider returns HTTP 400.

## 6. Exchange authorization code for access token

This exchange must happen on the backend from the server, not in the browser.

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
    "access_token": "JWT_ACCESS_TOKEN",
    "token_type": "Bearer",
    "expires_in": 3600
}
```

Important notes:

- The authorization code is single-use.
- It expires quickly.
- It cannot be reused.
- A wrong client_secret or wrong redirect_uri causes a failure.

## 7. Get user info

Once the access token is received, the external app calls the userinfo endpoint.

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

The external app should use:

- sub as the user ID in the external app
- application_role or roles as the app permission signal
- email only as contact info, not as the primary key

The global identity role is not enough to authorize a user in the external app. The provider-level identity role and app-level role are different concepts.

## 8. Role registration flow

The external application is the source of truth for its application roles. For example, Finboard sends its local roles such as `admin`, `pengurus`, `lending`, and `funding` to the provider. Absensi stores a synchronized copy only so the administrator can assign an application role to each user and so the OAuth response can include the assigned role.

The external app should synchronize roles during deployment and may synchronize them again before login. It must not require the administrator to recreate the same roles manually in Absensi.

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

Role rules:

- lowercase letters only
- numbers allowed
- allowed punctuation: . \_ -
- valid examples: finance_admin, approver, staff

For the Finboard application, run this command after configuring the SSO environment:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan sso:sync-roles
```

The command sends the roles currently used by Finboard to the registered client. The client ID and secret must belong to the same SSO client as the callback URL. If a role is added later in Finboard, run the command again; existing roles are updated rather than duplicated.

The SSO admin screen should only be used to assign the synchronized role to an employee. If the role list is empty, verify the client credentials and run the synchronization command on the external application before creating roles manually.

The admin later assigns the role to the relevant employee in the SSO access management screen.

## 9. Authorization rules for the external app

The external app should:

1. Create and store a random state before redirect.
2. Validate the returned state on callback.
3. Exchange the code using backend-to-backend request.
4. Store the JWT access token server-side.
5. Call userinfo with the bearer token.
6. Map sub to the local user record.
7. Save application_role or roles in the local application session or database.
8. Enforce local permission logic from those roles.

## 10. Common integration errors and fixes

### A. HTTP 400 invalid client or redirect URI

Typical causes:

- redirect_uri mismatch
- client_id mismatch
- app is not active
- callback URL not listed in the registered client

Fix:

- Confirm the registered redirect URI exactly matches the callback URL.
- Confirm client_id matches the generated one.
- Ensure the app is active in the SSO admin.

### B. HTTP 403 user not authorized for this application

Typical causes:

- user has no access assignment for this application
- role not assigned
- application not enabled for the user

Fix:

- Open the admin SSO user access screen.
- Grant access to the relevant user and role.
- Make sure the application role is active.

### C. HTTP 401 invalid_client

Typical causes:

- client_secret is wrong
- client_id is wrong
- the secret came from a different client

Fix:

- Re-check client_id and client_secret exactly.
- Use the secret generated for that specific client.
- Never expose the secret in the browser.

### D. HTTP 400 invalid_grant

Typical causes:

- code already used
- code expired
- redirect_uri mismatch during token exchange
- wrong client

Fix:

- Generate a fresh authorization flow.
- Use the exact redirect_uri used during the authorize request.
- Do not reuse the same code.

### E. Userinfo endpoint fails

Typical causes:

- access token expired
- token not sent as Bearer
- user not authorized for the client
- app using a different client_id than expected

Fix:

- Ensure the Authorization header is exactly Bearer ACCESS_TOKEN.
- Verify the JWT token is fresh.
- Ensure the client access assignment is valid.

## 11. Minimal beginner checklist for a new external app

Before production, confirm all of these:

- Client has been registered in the SSO admin
- Callback URL is exactly the same in both provider and app
- client_id and client_secret are stored on the backend only
- Login button redirects to the provider authorize URL
- state is random and verified on callback
- code exchange uses POST to /api/oauth/token
- JWT access token is used for /api/oauth/userinfo
- user has access to the client in the admin screen
- external application has synchronized its roles to `/api/oauth/roles`
- user has been assigned one of the synchronized application roles
- application role is mapped to local permissions

## 12. AI-ready copy-paste prompt for a new external app

```text
Integrate this external application as an OAuth 2.0 client to the SSO provider below.

SSO provider base URL:
https://absensi.bprsbtb.co.id

Use authorization code flow with JWT access token.

Required endpoints:
- Authorize: https://absensi.bprsbtb.co.id/oauth/authorize
- Token: https://absensi.bprsbtb.co.id/api/oauth/token
- Userinfo: https://absensi.bprsbtb.co.id/api/oauth/userinfo

Required flow:
1. Generate random state and save it server-side.
2. Redirect browser to /oauth/authorize with response_type=code, client_id, redirect_uri, scope=openid profile email, and state.
3. Receive callback with code and state.
4. Validate state.
5. Send backend request to /api/oauth/token with grant_type=authorization_code, code, client_id, client_secret, redirect_uri.
6. Receive JWT access token.
7. Call /api/oauth/userinfo with Authorization: Bearer <token>.
8. Read sub, full_name, email, phone, employee_id, application_role, and roles.
9. Map sub to the local user record.
10. Use application_role or roles for app permissions.
11. Do not use identity_role as local permission.
12. Never expose client_secret to frontend or browser.

Important constraints:
- Use only HTTPS.
- The redirect_uri must exactly match the callback URL registered in the provider.
- Use the exact client_id and client_secret generated for the app.
- The authorize request must include scope=openid profile email.
- The app must reject login if state is invalid or callback URI mismatches.
- If the user has no SSO access assigned to this app, the request should be rejected.

Expected userinfo fields include:
- sub
- name
- full_name
- email
- phone
- mobile
- employee_id
- department
- position
- application_role
- roles

Use sub as the external user ID. Do not use email as the primary key.
```

## 13. Final guidance

The SSO is designed so external apps behave like Google-style OAuth clients:

- browser redirects to provider
- provider returns code
- app exchanges code for JWT
- app reads user profile via userinfo
- app authorizes via application role

The most common failure is not JWT or the user account itself. It is usually caused by:

- wrong redirect_uri
- wrong client_id/secret
- missing scope
- no access assignment for the user
- stale or reused authorization code

If the integration fails, check these items in order: redirect_uri, client_id, client_secret, scope, app access assignment, and then callback state validation.
