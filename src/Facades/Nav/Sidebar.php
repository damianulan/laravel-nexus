<?php

namespace Nexus\Facades\Nav;

use Illuminate\Support\Facades\Facade;
use Nexus\Blueprints\Nav\NavigationBar;

/** @mixin NavigationBar */
class Sidebar extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'nexus.sidebar';
    }
}
