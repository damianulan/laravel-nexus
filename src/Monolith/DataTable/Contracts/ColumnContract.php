<?php

namespace Nexus\Monolith\DataTable\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface ColumnContract extends Arrayable
{
    public function getName(): string;
    public function align(string $alignment): static;
}
