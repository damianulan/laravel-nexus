<?php

namespace Nexus;

use Illuminate\Support\ServiceProvider;
use Nexus\Blueprints\Nav\NavigationBar;

/**
 * @author Damian Ułan <damian.ulan@protonmail.com>
 * @copyright 2026 damianulan
 * @license MIT
 */
class NexusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // $this->mergeConfigFrom(__DIR__ . '/../config/nexus.php', 'nexus');

        $this->app->singleton('nexus.sidebar', function (): NavigationBar {
            return new NavigationBar();
        });
    }

    public function boot(): void
    {
        $migrationPublishers = $this->migrationPublishers();

        // $this->publishes([
        //     __DIR__ . '/../config/nexus.php' => config_path('nexus.php'),
        // ], 'nexus-config');

        // $this->publishes($migrationPublishers, 'nexus-migrations');

        // $this->publishes(array_merge([
        //     __DIR__ . '/../config/nexus.php' => config_path('nexus.php'),
        // ], $migrationPublishers), 'nexus');

    }

    private function migrationPublishers(): array
    {
        return [
            __DIR__ . '/../database/migrations/create_modules_table.php.stub' => database_path('migrations/' . date('Y_m_d_His') . '_create_modules_table.php'),
        ];
    }
}
