<?php

namespace Nexus\Monolith\DataTable\Blueprints;

use Nexus\Monolith\DataTable\Contracts\ColumnContract;
use Nexus\Monolith\DataTable\Enums\ColumnType;

class DataColumn implements ColumnContract
{
    protected string $name;

    protected ?string $label = null;

    protected bool $sortable = true;

    protected string $alignment = 'left';

    protected ColumnType $type = ColumnType::Default;

    public function __construct(
        string $name
    ) {
        $this->name = $name;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
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

    public function selectable(): static
    {
        $this->type = ColumnType::Selectable;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'alignment' => $this->alignment,
            'type' => $this->type->value,
        ];
    }
}
