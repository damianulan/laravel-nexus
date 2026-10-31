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
use Nexus\Providers\MonolithServiceProvider;

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
        $this->mergeConfigFrom(__DIR__ . '/../config/datatables.php', 'datatables');

        $this->app->register(NexusAuthProvider::class);

        $this->app->singleton(PageContract::class, fn (): PageContract => new PageBlueprint);
        $this->app->singleton('nexus.sidebar', fn (): NavigationBar => new NavigationBar);

        $this->app->bind(
            SnackbarContract::class,
            fn (): SnackbarContract => new AlertBlueprint()->snackbar()
        );

        $this->app->bind(
            AlertContract::class,
            fn (): AlertContract => new AlertBlueprint()->alert()
        );

        $this->app->register(MonolithServiceProvider::class);
    }

    public function boot(): void
    {
        $migrationPublishers = $this->migrationPublishers();

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'nexus');

        // $this->publishes([
        //     __DIR__ . '/../config/nexus.php' => config_path('nexus.php'),
        // ], 'nexus-config');

        $this->publishesMigrations($migrationPublishers, 'nexus-migrations');

        Route::macro('header', function (string $header): Route {
            Page::defineHeader($this->getName(), $header);
            return $this;
        });
        Route::macro('description', function (string $description): Route {
            Page::defineDescription($this->getName(), $description);
            return $this;
        });
    }

    /** @return array<string, string> */
    private function migrationPublishers(): array
    {
        $timestamp = now()->startOfSecond();
        $migrations = [
            'create_settings_table.php',
            'create_preferences_table.php',
        ];

        $publishers = [];

        foreach ($migrations as $offset => $migration) {
            $publishers[__DIR__ . "/../stubs/{$migration}.stub"] = database_path(sprintf(
                'migrations/%s_%s',
                $timestamp->copy()->addSeconds($offset)->format('Y_m_d_His'),
                $migration,
            ));
        }

        return $publishers;
    }
}
