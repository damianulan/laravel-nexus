<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property-read \Illuminate\Database\Eloquent\Model $model
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel query()
 * @mixin \Eloquent
 */
class PreferenceModel extends SettingModel
{
    protected $table = 'preferences';

    /** @return MorphTo<Model, $this> */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
