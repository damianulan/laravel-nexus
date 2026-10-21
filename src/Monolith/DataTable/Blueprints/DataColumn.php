<?php

namespace Nexus\Monolith\DataTable\Blueprints;

use Nexus\Monolith\DataTable\Contracts\ColumnContract;

class DataColumn implements ColumnContract
{
    protected string $name;

    protected string $label;

    protected ?string $icon;

    public function __construct(
        string $name,
        string $label,
        ?string $icon = null,
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->icon = $icon;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'icon' => $this->icon,
        ];
    }
}
