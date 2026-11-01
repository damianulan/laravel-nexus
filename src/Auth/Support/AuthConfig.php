<?php

namespace Nexus\Auth\Support;

class AuthConfig
{
    public static function getRules(array $defaults = []): array
    {
        return config('nexus.auth.rules') ?? $defaults;
    }

    public static function getSupportIdentifier(): ?string
    {
        return config('nexus.auth.support_identifier');
    }

    public static function secretAttribute(): string
    {
        return config('nexus.auth.secret_attribute', 'password');
    }

    public static function getLoginAttributes(): array
    {
        return config('nexus.auth.login_attributes', []);
    }

    public static function usesRememberMe(): bool
    {
        return (bool) config('nexus.auth.remember_me', true);
    }

    public static function invalidCredentialsMessage(): string
    {
        $key = config('nexus.auth.invalid_credentials_message', 'auth.failed');
        $message = __(is_string($key) ? $key : 'auth.failed');

        return is_string($message) ? $message : 'The provided credentials do not match our records.';
    }

    public static function getGuard(): string
    {
        return config('auth.defaults.guard', 'web');
    }
}
