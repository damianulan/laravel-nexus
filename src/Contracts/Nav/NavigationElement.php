<?php

namespace Nexus\Contracts\Nav;

use Illuminate\Contracts\Support\Arrayable;

interface NavigationElement extends Arrayable
{
    public function setTitle(string $title): static;

    public function getTitle(): string;

    public function setIcon(?string $icon): static;

    public function getIcon(): ?string;

    public function getPriority(): int;

    public function isDisabled(): bool;

    public function disable(): static;

    public function isActive(): bool;
}
