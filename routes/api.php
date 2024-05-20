<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
	Route::controller(UserController::class)->group(function () {
		Route::get('/user', 'show')->name('user.show');
		Route::patch('/update-profile', 'update')->name('user.update');
	});

	Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
	Route::controller(MovieController::class)->group(function () {
		Route::get('/get-movies', 'index')->name('movie.index');
		Route::get('/movies/{movie}', 'show')->name('movie.show');
		Route::post('/add-movie', 'store')->name('movie.store');
		Route::patch('/edit-movie/{movie}', 'update')->name('movie.update');
		Route::delete('delete-movie/{movie}', 'destroy')->name('movie.destroy');
	});

	Route::controller(QuoteController::class)->group(function () {
		Route::get('/get-quotes', 'index')->name('quotes.index');
		Route::get('/get-quote/{quote}', 'show')->name('quotes.show');
		Route::post('/add-quote', 'store')->name('quote.store');
		Route::post('/update-like/{quote}', 'updateLike')->name('quote.likes');
		Route::post('/add-comment/{quote}', 'addComment')->name('quote.comments');
		Route::patch('/edit-quote/{quote}', 'update')->name('quote.update');
		Route::delete('/delete-quote/{quote}', 'destroy')->name('quote.destroy');
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
