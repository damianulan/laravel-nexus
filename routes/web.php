<?php

use Illuminate\Support\Facades\Route;
use Nexus\Http\Controllers\AuthController;

Route::middleware('web')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [config('nexus.auth.controller'), 'loginForm'])->name('login');
        Route::post('/login', [config('nexus.auth.controller'), 'login'])->middleware('throttle:6,1')->name('login.store');

        Route::get('/register', [config('nexus.auth.controller'), 'registerForm'])->name('register');
        Route::post('/register', [config('nexus.auth.controller'), 'register'])->middleware('throttle:6,1')->name('register.store');

        Route::get('/forgot-password', [config('nexus.auth.controller'), 'forgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [config('nexus.auth.controller'), 'sendResetLink'])->middleware('throttle:6,1')->name('password.email');

        Route::get('/reset-password/{token}', [config('nexus.auth.controller'), 'resetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [config('nexus.auth.controller'), 'resetPassword'])->middleware('throttle:6,1')->name('password.store');
    });

    Route::middleware('nexus.auth')->group(function (): void {
        Route::post('/logout', [config('nexus.auth.controller'), 'logout'])->name('logout');

        Route::get('/confirm-password', [config('nexus.auth.controller'), 'confirmPasswordForm'])->name('password.confirm');
        Route::post('/confirm-password', [config('nexus.auth.controller'), 'confirmPassword'])->middleware('throttle:6,1')->name('password.confirm.store');

        Route::put('/password', [config('nexus.auth.controller'), 'updatePassword'])->name('password.update');
    });
});
