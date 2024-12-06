<?php

// config for Beliven/Notarify

return [

    /*
    |--------------------------------------------------------------------------
    | Default Notarization Service
    |--------------------------------------------------------------------------
    |
    | This value determines the default notarization service to use.
    |
    | Supported: "scaling_parrots", "notarify4", "iuscribo"
    |
    */

    'default' => env('NOTARIFY_SERVICE'),

    /*
    |--------------------------------------------------------------------------
    | Notarization Services Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the supported services credentials and endpoints.
    |
    */

    'services' => [
        'scaling_parrots' => [
            'endpoint' => env('SCALING_PARROTS_ENDPOINT', ''),
            'username' => env('SCALING_PARROTS_USERNAME', ''),
            'password' => env('SCALING_PARROTS_PASSWORD', ''),
            'explorer_url' => env('SCALING_PARROTS_EXPLORER_URL', ''),
        ],
        'notarify4' => [
            'endpoint' => env('NOTARIFY4_ENDPOINT', ''),
            'username' => env('NOTARIFY4_USERNAME', ''),
            'password' => env('NOTARIFY4_PASSWORD', ''),
            'explorer_url' => env('NOTARIFY4_EXPLORER_URL', ''),
        ],
        'iuscribo' => [
            'endpoint' => env('IUSCRIBO_ENDPOINT', ''),
            'api_key' => env('IUSCRIBO_API_KEY', ''),
        ],
    ],
];
