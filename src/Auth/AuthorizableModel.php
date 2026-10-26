<?php

namespace Nexus\Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Nexus\Auth\Builders\UserBuilder;
use Nexus\Concerns\Eloquent\HasUuid;

/**
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static UserBuilder<static>|AuthorizableModel authorizable()
 * @method static UserBuilder<static>|AuthorizableModel newModelQuery()
 * @method static UserBuilder<static>|AuthorizableModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizableModel onlyTrashed()
 * @method static UserBuilder<static>|AuthorizableModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizableModel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizableModel withoutTrashed()
 * @mixin \Eloquent
 */
class AuthorizableModel extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use HasUuid;
    use SoftDeletes;
    use Notifiable;

    protected static string $builder = UserBuilder::class;

    public function syncable(): array
    {
        return [];
    }
}
