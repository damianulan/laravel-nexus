<?php

namespace Nexus\Blueprints\Nav;

use Closure;
use Nexus\Contracts\Nav\NavigationElement;

class NavigationBar
{
    /**
     * @var array<NavigationElement>
     */
    protected array $items = [];

    /**
     * @param array<NavigationElement> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->items = $elements;
    }

    public function add(NavigationElement $element, ?Closure $condition = null): static
    {
        if ($condition !== null && ! $condition($element)) {
            return $this;
        }

        $this->items[] = $element;

        return $this;
    }

    public function getInstance(): static
    {
        return $this;
    }

    public function toArray(): array
    {
        $items = $this->items;
        usort(
            $items,
            fn (NavigationElement $a, NavigationElement $b): int => $b->getPriority() <=> $a->getPriority()
        );

        return array_map(fn (NavigationElement $element): array => $element->toArray(), $items);
    }
}
