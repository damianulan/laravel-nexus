<?php

namespace Nexus\Monolith\DataTable\Blueprints;

use Nexus\Monolith\DataTable\Contracts\ColumnContract;

class DataColumn implements ColumnContract
{
    protected string $name;

    protected string $label;

    public function __construct(
        string $name,
        string $label,
    ) {
        $this->name = $name;
        $this->label = $label;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label
        ];
    }
}
