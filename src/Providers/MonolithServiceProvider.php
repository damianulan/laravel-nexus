<?php

namespace Nexus\Providers;

use Illuminate\Support\ServiceProvider;
use Nexus\Monolith\DataTable\Providers\DataTableProvider;
use Nexus\Monolith\Filtering\Providers\FilteringProvider;

class MonolithServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(FilteringProvider::class);
        $this->app->register(DataTableProvider::class);
    }

    public function boot(): void
    {
        //
    }
}
