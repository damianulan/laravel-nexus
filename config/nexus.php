<?php

return [
    'auth' => [
        'strategy' => Nexus\Auth\Services\AuthService::class,

        'controller' => App\Http\Controllers\AuthController::class,

        'rvc' => [
            'url' => env('AUTH_API_URL'),
            'token' => env('AUTH_API_TOKEN'),
        ],

        'support_identifier' => env('AUTH_SUPPORT_IDENTIFIER', 'email'),
        'secret_attribute' => env('AUTH_SECRET_ATTRIBUTE', 'password'),
        'remember_me' => true,
        'login_attributes' => [
            'email',
            'username',
            'token',
            'remember',
            'password',
        ],
        'rules' => [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:6'],
            'remember' => ['sometimes', 'boolean'],
        ],
    ],

    'inertia' => [
        'middleware' => App\Http\Middleware\HandleInertiaRequests::class
    ]
];
