<?php

namespace Nexus;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Nexus\Http\Middleware\Auth\AuthenticatedMiddleware;

final class NexusSetup
{
    public static function middleware(Middleware $middleware): void
    {
        $middleware->web(prepend: [
            StartSession::class,
            EncryptCookies::class,
        ]);

        $middleware->alias([
            'nexus.auth' => AuthenticatedMiddleware::class
        ]);
    }
}
