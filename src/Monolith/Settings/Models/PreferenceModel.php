<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PreferenceModel extends SettingModel
{
    protected $table = 'preferences';

    /** @return MorphTo<Model, $this> */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
