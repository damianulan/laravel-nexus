<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings\Facades;

use Illuminate\Support\Facades\Facade;

/** @mixin \Nexus\Monolith\Settings\Factory */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'nexus.settings';
    }
}
