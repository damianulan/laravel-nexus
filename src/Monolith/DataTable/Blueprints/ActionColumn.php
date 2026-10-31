<?php

declare(strict_types=1);

namespace Nexus\Monolith\DataTable\Blueprints;

use Closure;
use Nexus\Monolith\DataTable\Contracts\ColumnContract;
use Nexus\Monolith\DataTable\Enums\ColumnType;

class ActionColumn implements ColumnContract
{
    protected string $name;

    protected ?string $label = null;

    protected bool $sortable = false;

    protected string $alignment = 'right';

    protected ColumnType $type = ColumnType::Action;

    /** @var list<Closure(array<string, mixed>): array<string, mixed>> */
    protected array $buttons = [];

    public function __construct(string $name = 'actions')
    {
        $this->name = $name;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /** @param Closure(array<string, mixed>): array<string, mixed> $button */
    public function addButton(Closure $button): static
    {
        $this->buttons[] = $button;

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

    /**
     * @param array<string, mixed> $resource
     * @return list<array<string, mixed>>
     */
    public function buttonsFor(array $resource): array
    {
        return array_map(
            fn (Closure $button): array => $button($resource),
            $this->buttons,
        );
    }

    /**
     * @return array{
     *     name: string,
     *     label: string|null,
     *     sortable: bool,
     *     alignment: string,
     *     type: string
     * }
     */
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
