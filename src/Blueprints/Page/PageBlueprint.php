<?php

namespace Nexus\Blueprints\Page;

use Nexus\Contracts\Page\PageContract;
use Illuminate\Support\Facades\Route;

class PageBlueprint implements PageContract
{
    /**
     * The page's title.
     */
    protected string $title;

    protected array $headers = [];

    /**
     * The page's description.
     */
    protected array $descriptions = [];

    public function __construct()
    {
        $this->title = config('app.name');
    }



    public function defineHeader(string $routeName, string $header): self
    {
        $this->headers[$routeName] = $header;

        return $this;
    }

    public function defineDescription(string $routeName, string $description): self
    {
        $this->descriptions[$routeName] = $description;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getHeader(): ?string
    {
        $routeName = Route::currentRouteName();

        return $this->headers[$routeName] ?? null;
    }

    public function getDescription(): ?string
    {
        $routeName = Route::currentRouteName();

        return $this->descriptions[$routeName] ?? null;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'header' => $this->getHeader(),
            'description' => $this->getDescription(),
        ];
    }
}
