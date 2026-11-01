<?php

declare(strict_types=1);

namespace Nexus\Auth\Contracts;

interface AuthStrategy
{
    /** @param array<string, mixed> $attributes */
    public function login(array $attributes, ?string $guard = null): bool;

    public function logout(): void;
}
