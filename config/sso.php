<?php

return [
    'issuer' => rtrim((string) (env('SSO_ISSUER') ?: env('APP_URL') ?: 'http://localhost'), '/'),
    'authorization_code_ttl' => max(30, (int) env('SSO_AUTHORIZATION_CODE_TTL', 120)),
    'allowed_scopes' => ['openid', 'profile', 'email'],
];
