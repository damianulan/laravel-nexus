<?php

namespace Nexus\Facades\Page;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Session;
use Nexus\Contracts\Page\SnackbarContract;
use Nexus\Enums\Page\FlashableType;

/**
 * @mixin \Nexus\Contracts\Page\FlashableAlert
 */
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
        $messages = Session::pull(static::getType()->value, []);
        $class = \Nexus\Configs\AlertConfig::getActionClass();

        if (static::getType() === $class) {
            foreach (['success', 'error', 'warning', 'info'] as $alert) {
                if (Session::has($alert)) {
                    $messages[] = $class::text(Session::get($alert))->$alert()->toArray();
                }
            }
        }

        return $messages;
    }
}
