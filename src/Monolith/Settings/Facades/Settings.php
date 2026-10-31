<?php

namespace Nexus\Monolith\Settings\Facades;

use Illuminate\Support\Facades\Facade;

/** @mixin \Nexus\Monolith\Settings\Factory */
class Settings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'nexus.settings';
    }
}
