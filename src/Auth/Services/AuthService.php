<?php

namespace Nexus\Auth\Services;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Nexus\Auth\Contracts\AuthStrategy;
use Nexus\Auth\Support\AuthConfig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class AuthService implements AuthStrategy
{
    protected array $rules = [];

    public function __construct()
    {
        $this->rules = AuthConfig::getRules();
    }

    public function validate(array $attributes): void
    {
        Validator::make($attributes, $this->rules)->validate();
    }

    public function login(array $attributes, ?string $guard = null): bool
    {
        if (Auth::check()) {
            return true;
        }

        if ($guard === null) {
            $guard = AuthConfig::getGuard();
        }
        $this->validate(
            collect($attributes)->only(AuthConfig::getLoginAttributes())->all()
        );

        $remember = (bool) ($attributes['remember'] ?? false);
        unset($attributes['remember']);

        if (! Auth::guard($guard)->attempt($attributes, $remember)) {
            return false;
        }

        if (Auth::guard($guard) instanceof StatefulGuard) {
            session()->regenerate();
        }

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
}
