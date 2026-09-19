<?php

if (! function_exists('modules_path')) {
    function modules_path(string $path = ''): string
    {
        return app()->joinPaths(app()->basePath('modules'), $path);
    }
}
