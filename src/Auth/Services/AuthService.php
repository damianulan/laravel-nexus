<?php

declare(strict_types=1);

namespace Nexus\Auth\Services;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Nexus\Auth\Contracts\AuthStrategy;
use Nexus\Auth\Support\AuthConfig;
use RuntimeException;

final class AuthService implements AuthStrategy
{
    /** @param array<string, mixed> $attributes */
    public function validate(array $attributes): void
    {
        Validator::make($attributes, AuthConfig::getRules())->validate();
    }

    /** @param array<string, mixed> $attributes */
    public function login(array $attributes, ?string $guard = null): bool
    {
        $guardName = $guard ?? AuthConfig::getGuard();
        $credentials = Arr::only($attributes, AuthConfig::getLoginAttributes());
        $statefulGuard = Auth::guard($guardName);

        if (! $statefulGuard instanceof StatefulGuard) {
            throw new RuntimeException('The configured authentication guard must be stateful.');
        }

        if ($statefulGuard->check()) {
            return true;
        }

        $this->validate($credentials);

        $remember = AuthConfig::usesRememberMe() && (bool) ($credentials['remember'] ?? false);
        unset($credentials['remember']);

        try {
            if (! $statefulGuard->attempt($credentials, $remember)) {
                $this->rejectAuthentication();
            }
        } catch (RequestException $exception) {
            if (in_array($exception->response->status(), [401, 403, 422], true)) {
                $this->rejectAuthentication();
            }

            throw $exception;
        }

        session()->regenerate();

        return true;
    }

    public function logout(): void
    {
        $guard = Auth::guard();

        if (! $guard instanceof StatefulGuard) {
            throw new RuntimeException('The default authentication guard must be stateful.');
        }

        $guard->logout();

        session()->invalidate();
        session()->regenerateToken();
    }

    private function rejectAuthentication(): never
    {
        throw ValidationException::withMessages([
            AuthConfig::secretAttribute() => [AuthConfig::invalidCredentialsMessage()],
        ]);
    }
}
