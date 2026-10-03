<?php

namespace Nexus\Contracts\Nav;

use Illuminate\Contracts\Support\Arrayable;

interface NavigationElement extends Arrayable
{
    public function getPriority(): int;
}
