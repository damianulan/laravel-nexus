<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $name
 * @property array<array-key, mixed> $payload
 * @property string|null $module
 * @property string|null $group
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model $model
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PreferenceModel whereUpdatedAt($value)
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
