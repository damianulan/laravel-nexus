<?php

namespace Nexus\Providers;

use Illuminate\Support\ServiceProvider;
use Nexus\Monolith\DataTable\Providers\DataTableProvider;
use Nexus\Monolith\Filtering\Providers\FilteringProvider;
use Nexus\Monolith\Settings\Providers\SettingsProvider;

class MonolithServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(FilteringProvider::class);
        $this->app->register(DataTableProvider::class);
        $this->app->register(SettingsProvider::class);
    }

    public function boot(): void
    {
        //
    }
}
