<?php

namespace Nexus;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Nexus\Http\Middleware\Auth\AuthenticatedMiddleware;
use Nexus\Http\Middleware\Page\ResolveThemeVariant;

final class NexusSetup
{
    public static function middleware(Middleware $middleware): void
    {
        $middleware->web(
            [
                ResolveThemeVariant::class,
                HandleInertiaRequests::class,
            ],
            [
                StartSession::class,
                EncryptCookies::class,
            ]
        );

        $middleware->alias([
            'nexus.auth' => AuthenticatedMiddleware::class
        ]);
    }
}
