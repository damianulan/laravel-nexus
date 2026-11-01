<?php

use Illuminate\Support\Facades\Route;

$authController = config('nexus.auth.controller');

if (is_string($authController) && class_exists($authController)) {
    Route::middleware('web')->group(function () use ($authController): void {
        Route::middleware('guest')->group(function () use ($authController): void {
            Route::get('/login', [$authController, 'loginForm'])->name('login');
            Route::post('/login', [$authController, 'login'])->middleware('throttle:6,1')->name('login.store');

            Route::get('/register', [$authController, 'registerForm'])->name('register');
            Route::post('/register', [$authController, 'register'])->middleware('throttle:6,1')->name('register.store');

            Route::get('/forgot-password', [$authController, 'forgotPasswordForm'])->name('password.request');
            Route::post('/forgot-password', [$authController, 'sendResetLink'])->middleware('throttle:6,1')->name('password.email');

            Route::get('/reset-password/{token}', [$authController, 'resetPasswordForm'])->name('password.reset');
            Route::post('/reset-password', [$authController, 'resetPassword'])->middleware('throttle:6,1')->name('password.store');
        });

        Route::middleware('nexus.auth')->group(function () use ($authController): void {
            Route::post('/logout', [$authController, 'logout'])->name('logout');

            Route::get('/confirm-password', [$authController, 'confirmPasswordForm'])->name('password.confirm');
            Route::post('/confirm-password', [$authController, 'confirmPassword'])->middleware('throttle:6,1')->name('password.confirm.store');

            Route::put('/password', [$authController, 'updatePassword'])->name('password.update');
        });
    });
}
