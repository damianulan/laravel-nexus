<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Nexus\Monolith\Settings\Factory;

/**
 * @property int $id
 * @property string $name
 * @property array<array-key, mixed> $payload
 * @property string|null $module
 * @property string|null $group
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel whereUpdatedAt($value)
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
