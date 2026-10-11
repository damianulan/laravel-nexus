<?php

namespace Nexus\Services\Auth;

use Nexus\Contracts\Auth\AuthService;

class LocalAuthService implements AuthService
{
    protected function getIdentifierName(): string
    {
        return config('nexus.auth.fields.identifier');
    }

    protected function getSecretName(): string
    {
        return config('nexus.auth.fields.secret');
    }
}
