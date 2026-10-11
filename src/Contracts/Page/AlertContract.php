<?php

namespace Nexus\Contracts\Page;

use Illuminate\Contracts\Support\Arrayable;

interface AlertContract extends Arrayable
{
    public function header(string $header): static;
}
