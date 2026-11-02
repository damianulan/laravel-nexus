<?php

namespace Nexus\Configs;

use Nexus\Facades\Page\FlashableFacade;

class AlertConfig
{
    /**
     * @return class-string<FlashableFacade>
     */
    public static function getActionClass(): string
    {
        return config('nexus.alerts.action_alerts', \Nexus\Facades\Page\Snackbar::class);
    }
}
