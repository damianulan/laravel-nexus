<?php

declare(strict_types=1);

namespace Nexus\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Nexus\Auth\Contracts\AuthStrategy;
use Nexus\Auth\Clients\RvcHttpClient;
use Nexus\Auth\Providers\RvcUserProvider;

class NexusAuthProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AuthStrategy::class,
            config('nexus.auth.strategy')
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::provider('rvc', function ($app, $config) {
            return new RvcUserProvider($config['model'], new RvcHttpClient);
        });
    }
}
