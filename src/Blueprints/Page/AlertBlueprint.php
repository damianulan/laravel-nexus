<?php

namespace Nexus\Blueprints\Page;

use Illuminate\Support\Facades\Session;
use Nexus\Contracts\Page\AlertContract;
use Nexus\Contracts\Page\SnackbarContract;
use Nexus\Enums\Page\FlashableType;

class AlertBlueprint implements AlertContract, SnackbarContract
{
    protected ?string $header = null;

    protected string $text;

    protected string $color;

    protected ?string $icon = null;

    private FlashableType $type; // alert / snackbar

    public function __construct()
    {

    }

    public function alert(): static
    {
        $this->type = FlashableType::Alert;
        return $this;
    }

    public function snackbar(): static
    {
        $this->type = FlashableType::Snackbar;
        return $this;
    }

    public function header(string $header): static
    {
        $this->header = $header;
        return $this;
    }

    public function text(string $text): static
    {
        $this->text = $text;
        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function success(): static
    {
        $this->color = 'success';
        return $this;
    }

    public function warning(): static
    {
        $this->color = 'warning';
        return $this;
    }

    public function info(): static
    {
        $this->color = 'info';
        return $this;
    }

    public function error(): static
    {
        $this->color = 'error';
        return $this;
    }

    public function accent(): static
    {
        $this->color = 'accent';
        return $this;
    }

    public function primary(): static
    {
        $this->color = 'primary';
        return $this;
    }

    public function toArray(): array
    {
        return [
            'header' => $this->header,
            'text' => $this->text,
            'color' => $this->color,
            'icon' => $this->icon,
        ];
    }

    public function flash(): void
    {
        Session::push($this->type, $this->toArray());
    }
}
