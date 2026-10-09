<?php

namespace Nexus\Contracts\Page;

use Illuminate\Contracts\Support\Arrayable;

interface FlashableAlert extends Arrayable
{
    public function text(string $text): static;

    public function icon(string $icon): static;

    public function success(): static;

    public function warning(): static;

    public function info(): static;

    public function error(): static;

    public function accent(): static;

    public function primary(): static;

    public function flash(): void;
}
