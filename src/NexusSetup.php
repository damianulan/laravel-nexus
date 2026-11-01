<?php

namespace Nexus;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Nexus\Http\Middleware\Auth\AuthenticatedMiddleware;
use Nexus\Http\Middleware\Inertia\InertiaMiddleware;
use Nexus\Http\Middleware\Page\ResolveThemeVariant;

final class NexusSetup
{
    /** @param class-string<InertiaMiddleware> $inertiaMiddleware */
    public static function middleware(
        Middleware $middleware,
        string $inertiaMiddleware = InertiaMiddleware::class,
    ): void {
        $middleware->web(
            [
                ResolveThemeVariant::class,
                $inertiaMiddleware,
            ],
            [
                StartSession::class,
                EncryptCookies::class,
            ]
        );

        $middleware->alias([
            'nexus.auth' => AuthenticatedMiddleware::class,
        ]);
    }
}
