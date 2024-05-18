<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreForgotPassword;
use App\Http\Requests\StoreLoginRequest;
use App\Http\Requests\StoreResetPassword;
use App\Http\Requests\StoreSignupRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
	public function signup(StoreSignupRequest $request)
	{
		$user = User::create($request->validated());
		event(new Registered($user));
	}

	public function login(StoreLoginRequest $request)
	{
		$credentials = $request->validated();

		$user = User::where('email', $credentials['email'])->first();
		if ($user->google_id) {
			return response()->json(['errors' => ['email' => __('auth.google_login_error_message')]], 422);
		}

		if (!$user->email_verified_at) {
			return response()->json(['errors' =>['email' =>  __('auth.user_is_not_verified_error_message')]], 422);
		}

		if (!Auth::attempt(
			[
				'email'          => $credentials['email'],
				'password'       => $credentials['password'],
			],
			$credentials['remember_token'] ?? null
		)) {
			return response()->json(['errors' =>['email' =>  __('auth.auth_login_credentials_dont_match')]], 422);
		}

		session()->regenerate();
	}

	public function logout()
	{
		auth('web')->logout();
		session()->invalidate();
		session()->regenerateToken();
	}

	public function forgotPassword(StoreForgotPassword $request)
	{
		$user = User::where('email', $request->email)->first();

		if ($user->google_id) {
			return response()->json(['errors' => ['email' => __('profile.gmail_password_error')]], 403);
		}
		Password::sendResetLink(
			$request->only('email')
		);
	}

	public function resetPassword(StoreResetPassword $request)
	{
		$validatedData = $request->validated();

		$user = User::where('email', $validatedData['email'])->first();

		if (!$user) {
			return response()->json(['error' => 'User not found'], 404);
		}

		if (Hash::check($validatedData['password'], $user->password)) {
			return response()->json(['errors' =>['password' => __('email-verification.password_reset_new_password')]], 422);
		}

		$status = Password::reset(
			$validatedData,
			function (User $user, string $password) {
				$user->forceFill([
					'password' => Hash::make($password),
				])->setRememberToken(Str::random(60));

				$user->save();

				event(new PasswordReset($user));
			}
		);

		if ($status === Password::INVALID_TOKEN) {
			return response()->json(['expired' => true], 400);
		}
	}

	public function checkTokenValidity()
	{
		$user = User::where('email', request()->email)->first();
		if (!$user) {
			return response()->json(['User not found'], 404);
		}

		$validToken = Password::tokenExists($user, request()->token);

		if (!$validToken) {
			return response()->json(['expired' => true], 400);
		}
	}
}
