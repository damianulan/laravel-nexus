<?php

namespace Nexus\Http\Controllers;

use Illuminate\Http\Request;
use Nexus\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    public function login(Request $request): void
    {
        $this->validateLogin($request);

        $this->attemptLogin($request);
    }

    public function logout(): void
    {
        Auth::guard()->logout();
    }
}
