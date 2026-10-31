<?php

namespace Nexus\Monolith\DataTable\Blueprints;

use Nexus\Monolith\DataTable\Contracts\ColumnContract;
use Nexus\Monolith\DataTable\Enums\ColumnType;

class SelectableColumn implements ColumnContract
{
    protected string $name;

    protected bool $sortable = false;

    protected string $alignment = 'left';

    protected ColumnType $type = ColumnType::Selectable;

    public function __construct() {
        $this->name = 'select';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function align(string $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'sortable' => $this->sortable,
            'alignment' => $this->alignment,
            'type' => $this->type->value,
        ];
    }
}
