<?php

namespace Nexus\Concerns\Page;

use Nexus\Facades\Page\Alert;
use Nexus\Facades\Page\Snackbar;

trait FlashesMessages
{
    public function success(string $message): void
    {
        $this->flash($message, __FUNCTION__, 'bi-check-circle-fill');
    }

    public function primary(string $message): void
    {
        $this->flash($message, __FUNCTION__);
    }

    public function error(string $message): void
    {
        $this->flash($message, __FUNCTION__, 'bi-exclamation-circle-fill');
    }

    public function warning(string $message): void
    {
        $this->flash($message, __FUNCTION__, 'bi-exclamation-diamond-fill');
    }

    public function info(string $message): void
    {
        $this->flash($message, __FUNCTION__, 'bi-patch-question-fill');
    }

    protected function flash(string $message, string $type, ?string $icon = null): void
    {
        Alert::text($message)->$type()->icon($icon)->flash();
    }
}
