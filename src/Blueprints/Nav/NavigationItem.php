<?php

namespace Nexus\Blueprints\Nav;

use Illuminate\Support\Facades\Route;
use Nexus\Contracts\Nav\NavigationElement;

class NavigationItem implements NavigationElement
{
    protected string $title;

    protected string $route;

    protected ?string $icon = null;

    protected bool $external = false;

    protected int $priority = 0;

    public static function make(
        string $title,
        string $route,
        ?string $icon = null
    ): static
    {
        return new static()->setTitle($title)
            ->setRoute($route)
            ->setIcon($icon);
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function setRoute(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getLink(): string
    {
        return route($this->route);
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isActive(): bool
    {
        return $this->getRoute() === Route::currentRouteName();
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'link' => $this->getLink(),
            'icon' => $this->getIcon(),
            'active' => $this->isActive(),
        ];
    }
}
