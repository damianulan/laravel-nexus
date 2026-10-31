<?php

namespace Nexus\Monolith\Settings\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Nexus\Monolith\Settings\Factory;

class SettingsProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('nexus.settings', fn () => new Factory());
    }
}
