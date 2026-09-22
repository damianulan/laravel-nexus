<?php

namespace Nexus\Concerns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid()
    {
        static::creating(function (Model $model) {
            $model->uuid = Str::uuid();
            return $model;
        });
    }

    public function getUuidKeyName(): string
    {
        return 'uuid';
    }

    public function getUuidKey(): string
    {
        return $this->getAttribute($this->getUuidKeyName());
    }
}
