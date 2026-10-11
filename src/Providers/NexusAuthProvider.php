<?php

declare(strict_types=1);

namespace Nexus\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class NexusAuthProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config([
            'auth.guards.token.driver' => 'nexus.token',
        ]);

        Auth::viaRequest('nexus.token', function (Request $request) {
            return config('providers.users.model')::where('token', $request->token)->first();
        });
    }
}
