<?php

namespace Nexus\Auth\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class AuthenticatedFromClient
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public array $rvcUser,
        public UserContract $user
    ) {}
}
