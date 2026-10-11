<?php

namespace Nexus\Facades\Page;

use Illuminate\Support\Facades\Facade;
use Nexus\Contracts\Page\PageContract;
use Nexus\Blueprints\Page\PageBlueprint;

/** @mixin PageBlueprint */
class Page extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PageContract::class;
    }
}
