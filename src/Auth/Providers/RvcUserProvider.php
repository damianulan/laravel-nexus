<?php

namespace Nexus\Auth\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Support\Arrayable;
use Nexus\Auth\Clients\RvcHttpClient;
use Nexus\Auth\Support\AuthConfig;

class RvcUserProvider implements UserProvider
{
    public function __construct(
        protected string $model,
        protected RvcHttpClient $client
    ) {}

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
            return;
        }

        return $this->retrieveFromClient($credentials);
    }

    protected function retrieveFromClient(array $credentials): ?UserContract
    {
        $rawUser = $this->client->getAuthenticatableUser($credentials);
        $supportIdentifier = AuthConfig::getSupportIdentifier();

        return $this->newModelQuery()->firstOrCreate([
            $supportIdentifier => $rawUser[$supportIdentifier],
        ], $rawUser);
    }

    public function validateCredentials(UserContract $user, #[\SensitiveParameter] array $credentials)
    {
        $authenticated = $this->retrieveFromClient($credentials);

        return $authenticated->getAuthIdentifier() === $user->getAuthIdentifier();
    }

    /**
     * Get a new query builder for the model instance.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  TModel|null  $model
     * @return \Illuminate\Database\Eloquent\Builder<TModel>
     */
    protected function newModelQuery($model = null)
    {
        $query = is_null($model)
            ? $this->createModel()->newQuery()
            : $model->newQuery();

        with($query, $this->queryCallback);

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
        $class = '\\'.ltrim($this->model, '\\');

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
            $model->getAuthIdentifierName(), $identifier
        )->first();

        if (! $retrievedModel) {
            return;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $retrievedModel : null;
    }
}
