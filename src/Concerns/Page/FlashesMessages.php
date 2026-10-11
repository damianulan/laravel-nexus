<?php

namespace Nexus\Concerns\Page;

use Nexus\Facades\Page\Snackbar;

trait FlashesMessages
{
    public function success(string $message): void
    {
        $this->flash($message, __FUNCTION__);
    }

    public function primary(string $message): void
    {
        $this->flash($message, __FUNCTION__);
    }

    public function error(string $message): void
    {
        $this->flash($message, __FUNCTION__);
    }

    public function warning(string $message): void
    {
        $this->flash($message, __FUNCTION__);
    }

    public function info(string $message): void
    {
        $this->flash($message, __FUNCTION__);
    }

    private function flash(string $message, string $type): void
    {
        Snackbar::text($message)->$type()->flash();
    }
}
