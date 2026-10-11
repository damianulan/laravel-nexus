<?php

return [
    'auth' => [
        'strategy' => Nexus\Services\Auth\LocalAuthService::class,

        'fields' => [
            'identifier' => 'email',
            'secret' => 'password',
        ],

        'controller' => App\Http\Controllers\AuthController::class
    ]
];
