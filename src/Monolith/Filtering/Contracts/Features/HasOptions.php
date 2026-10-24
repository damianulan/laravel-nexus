<?php

namespace Nexus\Monolith\Filtering\Contracts\Features;

interface HasOptions
{
    /**
     * Options for the filter (e.g. select, multiselect, radio, checkbox etc.).
     *
     * @return array
     */
    public function getOptions(): array;
}
