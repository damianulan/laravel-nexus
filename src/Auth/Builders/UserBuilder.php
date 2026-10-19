<?php

declare(strict_types=1);

namespace Nexus\Auth\Builders;

use Illuminate\Database\Eloquent\Builder;
use Nexus\Auth\AuthorizableModel;

/**
 * @template TModel of AuthorizableModel
 *
 * @extends Builder<TModel>
 */
class UserBuilder extends Builder
{
    public function authorizable(): static
    {
        $this->whereNull('suspended_at');

        return $this;
    }
}
