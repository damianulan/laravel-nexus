<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Nexus\Monolith\Settings\Factory;

/**
 * @property string $group
 * @property string $name
 * @property mixed $payload
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel query()
 * @mixin \Eloquent
 */
class SettingModel extends Model
{
    protected $table = 'settings';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(static function (): void {
            Cache::forget(Factory::CACHE_KEY);
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
