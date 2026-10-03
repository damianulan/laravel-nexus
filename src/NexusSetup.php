<?php

namespace Nexus;

use App\Http\Middleware\TestMiddleware;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;

final class NexusSetup
{
    public static function middleware(Middleware $middleware): void
    {
        $middleware->web(prepend: [
            StartSession::class,
            EncryptCookies::class,
        ]);
    }
}
