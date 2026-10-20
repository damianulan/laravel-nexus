<?php

namespace Nexus;

use Illuminate\Support\ServiceProvider;
use Nexus\Blueprints\Nav\NavigationBar;
use Nexus\Contracts\Page\PageContract;
use Nexus\Blueprints\Page\PageBlueprint;
use Illuminate\Routing\Route;
use Nexus\Auth\Contracts\AuthStrategy;
use Nexus\Blueprints\Page\AlertBlueprint;
use Nexus\Contracts\Page\AlertContract;
use Nexus\Contracts\Page\SnackbarContract;
use Nexus\Facades\Page\Page;
use Nexus\Auth\Providers\NexusAuthProvider;

/**
 * @author Damian Ułan <damian.ulan@protonmail.com>
 * @copyright 2026 damianulan
 * @license MIT
 */
class NexusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/nexus.php', 'nexus');
        $this->mergeConfigFrom(__DIR__ . '/../config/theme.php', 'theme');

        $this->app->register(NexusAuthProvider::class);

        $this->app->singleton(PageContract::class, fn (): PageContract => new PageBlueprint());
        $this->app->singleton('nexus.sidebar', fn (): NavigationBar => new NavigationBar());

        $this->app->bind(
            SnackbarContract::class,
            fn (): SnackbarContract => new AlertBlueprint()->snackbar()
        );

        $this->app->bind(
            AlertContract::class,
            fn (): AlertContract => new AlertBlueprint()->alert()
        );
    }

    public function boot(): void
    {
        $migrationPublishers = $this->migrationPublishers();

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'nexus');

        // $this->publishesMigrations([
        //     __DIR__ . '/../database/migrations' => database_path('migrations'),
        // ]);

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
