<?php

namespace Nexus\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Nexus\Enums\Auth\AuthType;
use Nexus\Contracts\Auth\AuthService;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Contracts\Auth\Factory as AuthContract;

abstract class AuthController extends BaseController
{
    public function __construct(
        protected AuthContract $auth,
        protected AuthService $service
    ) {}

    abstract public function loginForm(): mixed;

    abstract public function forgotPasswordForm(): mixed;

    abstract public function registerForm(): mixed;

    abstract public function confirmPasswordForm(): mixed;

    public function login(Request $request): mixed
    {
        return redirect()->back()->with('error', 'This is error');
        dd(Auth::attempt([
            'email' => 'little.waylon@example.org',
            'password' => 'test'
        ]));

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);
        unset($credentials['remember']);

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $request->session()->regenerate();

        return response()->noContent();
    }

    public function register(Request $request): mixed
    {
        $user = $this->newUserModel();
        $attributes = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique($user->getTable(), 'username')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique($user->getTable(), 'email')],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user->forceFill([
            ...$attributes,
            'password' => Hash::make($attributes['password']),
            'auth' => AuthType::LOCAL->value,
        ])->save();

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return response()->noContent(HttpResponse::HTTP_CREATED);
    }

    public function logout(Request $request): mixed
    {
        $guard = Auth::guard();

        if (! $guard instanceof StatefulGuard) {
            throw new RuntimeException('The default authentication guard must be stateful.');
        }

        $guard->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }

    public function sendResetLink(Request $request): mixed
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink($credentials);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->noContent();
    }

    public function resetPasswordForm(Request $request, string $token): JsonResponse
    {
        return response()->json([
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): mixed
    {
        $credentials = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $credentials,
            function (CanResetPasswordContract $user, string $password): void {
                if (! $user instanceof Model || ! $user instanceof AuthenticatableContract) {
                    throw new RuntimeException('The configured user model must be an authenticatable Eloquent model.');
                }

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if (! is_string($status)) {
            throw new RuntimeException('The password broker returned an invalid status.');
        }

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->noContent();
    }

    public function confirmPassword(Request $request): mixed
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        $request->session()->put('auth.password_confirmed_at', time());

        return response()->noContent();
    }

    public function updatePassword(Request $request): mixed
    {
        $attributes = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = $request->user();

        if (! $user instanceof Model) {
            throw new RuntimeException('The authenticated user must be an Eloquent model.');
        }

        $user->forceFill([
            'password' => Hash::make($attributes['password']),
            'remember_token' => Str::random(60),
        ])->save();

        return response()->noContent();
    }

    private function newUserModel(): Model&AuthenticatableContract
    {
        $guard = config('auth.defaults.guard');
        $provider = is_string($guard) ? config("auth.guards.{$guard}.provider") : null;
        $modelClass = is_string($provider) ? config("auth.providers.{$provider}.model") : null;

        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw new RuntimeException('The default authentication provider must configure an Eloquent user model.');
        }

        $user = new $modelClass;

        if (! $user instanceof Model || ! $user instanceof AuthenticatableContract) {
            throw new RuntimeException('The configured user model must be an authenticatable Eloquent model.');
        }

        return $user;
    }
}
