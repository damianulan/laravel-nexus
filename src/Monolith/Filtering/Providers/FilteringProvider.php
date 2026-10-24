<?php

namespace Nexus\Monolith\Filtering\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Nexus\Monolith\Filtering\Blueprints\FilterBag;

class FilteringProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('nexus.filters', fn () => new FilterBag(Request::instance()));
    }
}
