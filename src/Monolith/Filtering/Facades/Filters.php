<?php

namespace Nexus\Monolith\Filtering\Facades;

use Illuminate\Support\Facades\Facade;
use Nexus\Monolith\Filtering\Blueprints\FilterBag;

/** @mixin FilterBag */
class Filters extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'nexus.filters';
    }
}
