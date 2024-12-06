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
    | Supported: "scalingparrots", "iuscribo"
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
        'scalingparrots' => [
            'service' => \Beliven\Notarify\Services\ScalingParrotsService::class,
            'endpoint' => env('SCALING_PARROTS_ENDPOINT', ''),
            'username' => env('SCALING_PARROTS_USERNAME', ''),
            'password' => env('SCALING_PARROTS_PASSWORD', ''),
            'settings' => [
                'hash-algorithm' => 'sha256',
            ],
        ],
        'iuscribo' => [
            'service' => \Beliven\Notarify\Services\IuscriboService::class,
            'endpoint' => env('IUSCRIBO_ENDPOINT', ''),
            'username' => env('IUSCRIBO_USERNAME', ''),
            'password' => env('IUSCRIBO_PASSWORD', ''),
            'company' => env('IUSCRIBO_COMPANY', ''),
            'settings' => [
                'blockchain' => 'PolygonTestNet',
                'send-method' => 'NoSend',
                'hash-algorithm' => 'sha256',
            ],
        ],
    ],
];
