<?php

namespace Nexus\Http\Middleware\Auth;

use Illuminate\Auth\Middleware\Authenticate;
use Nexus\Auth\Contracts\AuthStrategy;
use Illuminate\Contracts\Auth\Factory as Auth;
use Closure;

class AuthenticatedMiddleware extends Authenticate
{
    public function __construct(
        Auth $auth,
        protected AuthStrategy $service,
    )
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        return $next($request);
    }
}
