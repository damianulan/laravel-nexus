<?php

namespace Nexus\Facades\Page;

use Nexus\Facades\Page\FlashableFacade;
use Nexus\Contracts\Page\AlertContract;

/** @mixin AlertContract */
class Alert extends FlashableFacade
{
    protected static function getFacadeAccessor()
    {
        return AlertContract::class;
    }
}
