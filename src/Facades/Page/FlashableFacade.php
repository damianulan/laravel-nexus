<?php

namespace Nexus\Facades\Page;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Session;
use Nexus\Contracts\Page\SnackbarContract;
use Nexus\Enums\Page\FlashableType;

abstract class FlashableFacade extends Facade
{
    public static function getType(): FlashableType
    {
        return match (static::getFacadeAccessor()) {
            SnackbarContract::class => FlashableType::Snackbar,
            default => FlashableType::Alert,
        };
    }

    public static function getAll(): array
    {
        return Session::pull(static::getType()->value, []);
    }
}
