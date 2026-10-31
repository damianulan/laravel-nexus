<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings\Providers;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Nexus\Monolith\Settings\Factory;

class SettingsProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            Factory::class,
            fn (Application $app): Factory => new Factory(
                cache: $app->make(Repository::class),
                discoveryPaths: config('nexus.settings.discovery_paths', []),
            ),
        );
        $this->app->alias(Factory::class, 'nexus.settings');
    }
}
