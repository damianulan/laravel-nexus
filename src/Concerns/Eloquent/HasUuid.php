<?php

declare(strict_types=1);

namespace Nexus\Concerns\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** @mixin Model */
trait HasUuid
{
    protected static function bootHasUuid()
    {
        static::creating(function (Model $model): Model {
            $model->setAttribute($model->getUuidKey(), Str::uuid7());
            return $model;
        });
    }

    public function getUuidKey(): string
    {
        return 'uuid';
    }

    public function uniqueIds(): array
    {
        return [$this->getUuidKey()];
    }

    public function getUuid(): string
    {
        return $this->getAttribute($this->getUuidKey());
    }

    public static function findByUuid(string $uuid): ?static
    {
        $key = app(static::class)->getUuidKey();
        return static::query()->where($key, $uuid)->first();
    }
}
