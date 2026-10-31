<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingModel query()
 * @mixin \Eloquent
 */
class SettingModel extends Model
{
    protected $table = 'settings';

    protected $guarded = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
