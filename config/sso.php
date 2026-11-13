<?php

return [
    'base_url' => rtrim(env('SSO_BASE_URL', 'https://sso.smktelkom-lpg.id'), '/'),
    'client_id' => env('SSO_CLIENT_ID', '01a1d72c-7e51-7113-b5e0-3c9028c646b6'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'callback_url' => env('SSO_CALLBACK_URL', env('APP_URL').'/auth/sso/callback'),
    'scope' => env('SSO_SCOPE', 'profile:read'),
];
