<?php

namespace Nexus\Providers;

use Illuminate\Support\ServiceProvider;
use Nexus\Monolith\DataTable\Providers\DataTableProvider;

class MonolithServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(DataTableProvider::class);
    }

    public function boot(): void
    {
        //
    }
}
