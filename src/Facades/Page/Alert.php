<?php

namespace Nexus\Facades\Page;

use Nexus\Facades\Page\FlashableFacade;
use Nexus\Contracts\Page\AlertContract;
use Illuminate\Support\Facades\Session;

/** @mixin AlertContract */
class Alert extends FlashableFacade
{
    protected static function getFacadeAccessor()
    {
        return AlertContract::class;
    }

    public static function getAll(): array
    {
        return Session::pull(static::getType()->value, []);
    }
}
