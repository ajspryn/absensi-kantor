<?php

return [
    'issuer' => env('SSO_ISSUER', env('APP_URL')),
    'authorization_code_ttl' => (int) env('SSO_AUTHORIZATION_CODE_TTL', 60),
    'allowed_scopes' => ['openid', 'profile', 'email'],
];