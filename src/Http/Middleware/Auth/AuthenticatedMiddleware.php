<?php

namespace Nexus\Http\Middleware\Auth;

use Illuminate\Auth\Middleware\Authenticate;
use Nexus\Auth\Contracts\AuthContract;

class AuthenticatedMiddleware extends Authenticate
{
    public function __construct(
        protected AuthContract $service
    )
    {}

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
