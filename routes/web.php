<?php

use Illuminate\Support\Facades\Route;
use Nexus\Http\Controllers\AuthController;

Route::middleware('web')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');

        Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.store');

        Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:6,1')->name('password.email');

        Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:6,1')->name('password.store');
    });

    Route::middleware('nexus.auth')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/confirm-password', [AuthController::class, 'confirmPasswordForm'])->name('password.confirm');
        Route::post('/confirm-password', [AuthController::class, 'confirmPassword'])->middleware('throttle:6,1')->name('password.confirm.store');

        Route::put('/password', [AuthController::class, 'updatePassword'])->name('password.update');
    });
});
