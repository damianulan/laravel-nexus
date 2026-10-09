<?php

namespace Nexus\Facades\Page;

use Nexus\Facades\Page\FlashableFacade;
use Nexus\Contracts\Page\SnackbarContract;

/** @mixin SnackbarContract */
class Snackbar extends FlashableFacade
{
    protected static function getFacadeAccessor()
    {
        return SnackbarContract::class;
    }
}
