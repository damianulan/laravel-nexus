<?php

namespace Nexus\Contracts\Page;

use Illuminate\Contracts\Support\Arrayable;

interface PageContract extends Arrayable
{
    public function defineHeader(string $routeName, string $header): self;

    public function defineDescription(string $routeName, string $description): self;

    public function getTitle(): string;

    public function getHeader(): ?string;

    public function getDescription(): ?string;
}
