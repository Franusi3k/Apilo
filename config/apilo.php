<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Apilo API Configuration
    |--------------------------------------------------------------------------
    |
    | Stałe dane potrzebne do integracji z API Apilo.
    |
    */

    'base_url' => env('APILO_BASE_URL', 'https://api.apilo.com'),
    'client_id' => env('APILO_CLIENT_ID'),
    'client_secret' => env('APILO_CLIENT_SECRET'),
    'platform_id' => env('APILO_PLATFORM_ID'),
    'authorization_code' => env('APILO_AUTHORIZATION_CODE') ?? null,
];
