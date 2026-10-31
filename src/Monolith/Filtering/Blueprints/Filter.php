<?php

namespace Nexus\Monolith\Filtering\Blueprints;

use Illuminate\Contracts\Database\Query\Builder;
use Nexus\Monolith\Filtering\Contracts\Types\TextFilter;
use Nexus\Monolith\Filtering\Contracts\Types\SelectFilter;
use Nexus\Monolith\Filtering\Contracts\Types\MultiselectFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Nexus\Monolith\Filtering\Contracts\Features\HasOptions;
use Nexus\Monolith\Filtering\Contracts\FilterInstance;

abstract class Filter implements FilterInstance
{
    public function __construct(
        protected array $payload,
    ) {}

    /**
     * Filter unique identifier.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return Str::snake(Str::before(class_basename(static::class), 'Filter'));
    }

    /**
     * Display name of the filter.
     *
     * @return string
     */
    abstract public function getLabel(): string;


    public function getValue(): string|array|null
    {
        $value = $this->payload[$this->getSlug()] ?? null;
        return empty($value) ? null : $value;
    }

    public function withQuery(Builder $query): void
    {
        $class = static::class;
        throw new \RuntimeException("The '$class' filter does implement filtering by query.");
    }

    public function withCollection(Collection $collection): void
    {
        $class = static::class;
        throw new \RuntimeException("The '$class' filter does implement filtering collections.");
    }

    public function getType(): string
    {
        $type = match (true) {
            $this instanceof TextFilter => 'text',
            $this instanceof SelectFilter => 'select',
            $this instanceof MultiselectFilter => 'multiselect',
            default => null,
        };

        if ($type === null) {
            $class = static::class;
            throw new \RuntimeException("The '$class' filter does not have a valid type.");
        }

        return $type;
    }

    public function toArray(): array
    {
        $attributes = [
            'slug' => $this->getSlug(),
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
            'type' => $this->getType(),
        ];

        if ($this instanceof HasOptions) {
            $attributes['options'] = $this->getOptions();
        }

        return $attributes;
    }
}
