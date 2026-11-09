<?php

namespace Nexus\Enums\Page;

enum EnvironmentType: string
{
    case Local = 'local';
    case Testing = 'testing';
    case Development = 'development';
    case Staging = 'staging';
    case Production = 'production';

    public static function current(): self
    {
        return self::from(app()->environment());
    }

    public static function isProduction(): bool
    {
        return self::current() === self::Production;
    }

    public static function isLocal(): bool
    {
        return self::current() === self::Local;
    }

    public static function isTesting(): bool
    {
        return self::current() === self::Testing;
    }

    public function order(): int
    {
        return match($this) {
            self::Development => 1,
            self::Staging => 2,
            self::Production => 3,
            default => 0
        };
    }

    public function lt(string|self $env): bool
    {
        if (! $env instanceof self) {
            $env = self::from($env);
        }

        return $env->order() > $this->order();
    }

    public function lte(string|self $env): bool
    {
        if (! $env instanceof self) {
            $env = self::from($env);
        }

        return $env->order() >= $this->order();
    }

    public function gt(string|self $env): bool
    {
        if (! $env instanceof self) {
            $env = self::from($env);
        }

        return $this->order() > $env->order();
    }

    public function gte(string|self $env): bool
    {
        if (! $env instanceof self) {
            $env = self::from($env);
        }

        return $this->order() >= $env->order();
    }
}
