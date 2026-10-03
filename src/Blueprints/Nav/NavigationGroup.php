<?php

namespace Nexus\Blueprints\Nav;

use Nexus\Contracts\Nav\NavigationElement;

class NavigationGroup implements NavigationElement
{
    protected string $title;

    protected ?string $icon = null;

    protected array $items = [];

    protected int $priority = 0;

    public static function make(string $title): static
    {
        return new static()->setTitle($title);
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function add(NavigationElement $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function toArray(): array
    {
        $items = $this->items;
        usort(
            $items,
            fn (NavigationElement $a, NavigationElement $b): int => $b->getPriority() <=> $a->getPriority()
        );
        return [
            'title' => $this->getTitle(),
            'icon' => $this->getIcon(),
            'items' => array_map(fn (NavigationElement $item): array => $item->toArray(), $items)
        ];
    }
}
