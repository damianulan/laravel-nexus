<?php

return [
    'auth' => [
        'strategy' => Nexus\Auth\Services\AuthService::class,

        'controller' => null,

        'rvc' => [
            'url' => env('AUTH_API_URL'),
            'token' => env('AUTH_API_TOKEN'),
            'path' => '/api/auth',
        ],

        'support_identifier' => env('AUTH_SUPPORT_IDENTIFIER', 'email'),
        'secret_attribute' => env('AUTH_SECRET_ATTRIBUTE', 'password'),
        'invalid_credentials_message' => 'auth.failed',
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
        'middleware' => Nexus\Http\Middleware\Inertia\InertiaMiddleware::class,
    ],

    'alerts' => [
        'action_alerts' => \Nexus\Facades\Page\Snackbar::class
    ],

    'settings' => [
        'discovery_paths' => [
            app_path(),
            base_path('modules'),
        ],
    ],
];
