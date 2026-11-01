<?php

declare(strict_types=1);

namespace Nexus\Auth\Providers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Nexus\Auth\Clients\RvcHttpClient;
use Nexus\Auth\Events\AuthenticatedFromClient;
use Nexus\Auth\Support\AuthConfig;
use Nexus\Enums\Auth\AuthType;
use RuntimeException;

class RvcUserProvider implements UserProvider
{
    private mixed $retrievedUserIdentifier = null;

    public function __construct(
        protected string $model,
        protected RvcHttpClient $client
    ) {
    }

    /**
     * The callback that may modify the user retrieval queries.
     *
     * @var (\Closure(\Illuminate\Database\Eloquent\Builder<*>):mixed)|null
     */
    protected $queryCallback;

    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        $user = $this->retrieveFromClient($credentials);
        $this->retrievedUserIdentifier = $user?->getAuthIdentifier();

        return $user;
    }

    protected function retrieveFromClient(array $credentials): ?UserContract
    {
        $rawUser = $this->client->getAuthenticatableUser($credentials);
        $supportIdentifier = AuthConfig::getSupportIdentifier();

        if (! is_string($supportIdentifier) || $supportIdentifier === '') {
            throw new RuntimeException('The authentication support identifier must be a non-empty string.');
        }

        $identifier = $rawUser[$supportIdentifier] ?? null;

        if (! is_int($identifier) && ! is_string($identifier)) {
            throw new RuntimeException("The authentication response must contain [{$supportIdentifier}].");
        }

        $model = $this->createModel();
        $matchingUsers = $this->newSynchronizationQuery($model)
            ->where($supportIdentifier, $identifier)
            ->limit(2)
            ->get();

        if ($matchingUsers->count() > 1) {
            throw new AuthenticationException('Multiple local users match the external identity.');
        }

        $user = $matchingUsers->first() ?? $model;

        if (! $user instanceof UserContract || ! $this->isAuthorizable($user)) {
            return null;
        }

        $rawUser['auth'] = AuthType::RVC;
        $syncableAttributes = $this->syncableAttributes($user, $rawUser);
        $user->fill($syncableAttributes)->save();

        if (! $this->isAuthorizable($user)) {
            return null;
        }

        AuthenticatedFromClient::dispatch($syncableAttributes, $user);

        return $user;
    }

    public function validateCredentials(UserContract $user, #[\SensitiveParameter] array $credentials): bool
    {
        return $this->retrievedUserIdentifier !== null
            && $this->retrievedUserIdentifier === $user->getAuthIdentifier();
    }

    /**
     * Get a new query builder for the model instance.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  TModel|null  $model
     * @return \Illuminate\Database\Eloquent\Builder<TModel>
     */
    protected function newModelQuery($model = null): Builder
    {
        $query = is_null($model)
            ? $this->createModel()->newQuery()
            : $model->newQuery();

        with($query, $this->queryCallback);

        if (method_exists($query, 'authorizable')) {
            $query->authorizable();
        }

        return $query;
    }

    public function rehashPasswordIfRequired(UserContract $user, #[\SensitiveParameter] array $credentials, bool $force = false)
    {
        //
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Gets the name of the Eloquent user model.
     *
     * @return class-string<\Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model>
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets the name of the Eloquent user model.
     *
     * @param  class-string<\Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model>  $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the callback that modifies the query before retrieving users.
     *
     * @return (\Closure(\Illuminate\Database\Eloquent\Builder<*>):mixed)|null
     */
    public function getQueryCallback()
    {
        return $this->queryCallback;
    }

    /**
     * Sets the callback to modify the query before retrieving users.
     *
     * @param  (\Closure(\Illuminate\Database\Eloquent\Builder<*>):mixed)|null  $queryCallback
     * @return $this
     */
    public function withQuery($queryCallback = null)
    {
        $this->queryCallback = $queryCallback;

        return $this;
    }

    public function updateRememberToken(UserContract $user, #[\SensitiveParameter] $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token)
    {
        $model = $this->createModel();

        $retrievedModel = $this->newModelQuery($model)->where(
            $model->getAuthIdentifierName(),
            $identifier
        )->first();

        if (! $retrievedModel) {
            return;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $retrievedModel : null;
    }

    /** @return Builder<Model> */
    private function newSynchronizationQuery(Model $model): Builder
    {
        $query = $model->newQuery();

        if (method_exists($model, 'trashed')) {
            $query->withTrashed();
        }

        return $query;
    }

    /**
     * @param UserContract&Model $user
     * @param array<string, mixed> $rawUser
     * @return array<string, mixed>
     */
    private function syncableAttributes(UserContract $user, array $rawUser): array
    {
        if (! method_exists($user, 'syncable')) {
            return $rawUser;
        }

        $syncable = $user->syncable();

        return is_array($syncable) && $syncable !== []
            ? Arr::only($rawUser, $syncable)
            : $rawUser;
    }

    private function isAuthorizable(UserContract $user): bool
    {
        if (! $user instanceof Model || ! $user->exists) {
            return true;
        }

        return $this->newModelQuery()
            ->whereKey($user->getKey())
            ->exists();
    }
}
