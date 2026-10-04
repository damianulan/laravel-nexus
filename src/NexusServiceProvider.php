<?php

namespace Nexus;

use Illuminate\Support\ServiceProvider;
use Nexus\Blueprints\Nav\NavigationBar;
use Nexus\Contracts\Page\PageContract;
use Nexus\Blueprints\Page\PageBlueprint;
use Illuminate\Routing\Route;
use Nexus\Facades\Page\Page;

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

        $this->app->singleton(PageContract::class, fn (): PageContract => new PageBlueprint());
        $this->app->singleton('nexus.sidebar', fn (): NavigationBar => new NavigationBar());
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

        Route::macro('header', function (string $header): Route {
            Page::defineHeader($this->getName(), $header);
            return $this;
        });
        Route::macro('description', function (string $description): Route {
            Page::defineDescription($this->getName(), $description);
            return $this;
        });
    }

    private function migrationPublishers(): array
    {
        return [
            __DIR__ . '/../database/migrations/create_modules_table.php.stub' => database_path('migrations/' . date('Y_m_d_His') . '_create_modules_table.php'),
        ];
    }
}
