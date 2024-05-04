<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
	return $request->user();
})->middleware('auth:sanctum');

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
