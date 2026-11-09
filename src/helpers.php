<?php

use Nexus\Monolith\Settings\Facades\Settings;

if (! function_exists('setting')) {
    function setting($key, $default = null)
    {
        return Settings::find($key) ?? $default;
    }
}
