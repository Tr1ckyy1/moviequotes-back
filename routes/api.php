<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
	Route::middleware(['auth:sanctum', 'verified'])->group(function () {
		Route::get('/user', 'show')->name('user.show');
		Route::post('/update-profile', 'update')->name('user.update');
	});
});

Route::controller(AuthController::class)->group(function () {
	Route::post('/logout', 'logout')->middleware(['auth:sanctum', 'verified'])->name('auth.logout');
	Route::middleware('guest')->group(function () {
		Route::post('/signup', 'signup')->name('auth.signup');
		Route::post('/login', 'login')->name('auth.login');
		Route::post('/forgot-password', 'forgotPassword')->name('auth.forgot_password');
		Route::post('/reset-password', 'resetPassword')->name('auth.reset_password');
		Route::post('/check-token-validity', 'checkTokenValidity')->name('auth.check_token_validity');
	});
});

Route::controller(VerificationController::class)->group(function () {
	Route::get('/email/verify/{id}/{hash}', 'verify')->middleware('signed')->name('verification.verify');
	Route::post('/email/verification-notification', 'resend')->middleware('throttle:6,1')->name('verification.send');
});

Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->middleware('guest')->name('google_redirect');
Route::get('auth/google/callback', [GoogleAuthController::class, 'callbackGoogle'])->middleware('guest')->name('google_callback');
