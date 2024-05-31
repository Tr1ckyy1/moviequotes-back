<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreForgotPassword;
use App\Http\Requests\StoreLoginRequest;
use App\Http\Requests\StoreResetPasswordRequest;
use App\Http\Requests\StoreSignupRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
	public function signup(StoreSignupRequest $request)
	{
		$user = User::create($request->validated());
		$user->sendEmailVerificationNotification();
	}

	public function login(StoreLoginRequest $request)
	{
		$credentials = $request->validated();

		$user = User::where('username', $credentials['user'])->orWhere('email', $credentials['user'])->first();

		if (!$user) {
			return response()->json(['errors' => ['user' => __('validation.user_or_email_doesnt_exist')]], 422);
		}
		if ($user->google_id) {
			return response()->json(['errors' => ['user' => __('auth.google_login_error_message')]], 422);
		}

		if (!$user->email_verified_at) {
			return response()->json(['errors' =>['user' =>  __('auth.user_is_not_verified_error_message')]], 422);
		}

		if (!Auth::attempt(
			[
				'username'          => $credentials['user'],
				'password'          => $credentials['password'],
			],
			$credentials['remember_token'] ?? null
		)) {
			return response()->json(['errors' =>['user' =>  __('auth.auth_login_credentials_dont_match')]], 422);
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
			return response()->json(['errors' => ['email' => __('profile.gmail_password_error')]], 422);
		}
		Password::sendResetLink(
			$request->only('email')
		);
	}

	public function resetPassword(StoreResetPasswordRequest $request)
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
		$user = User::where('email', Crypt::decryptString(request()->email))->first();
		if (!$user) {
			return response()->json(['User not found'], 404);
		}

		$validToken = Password::tokenExists($user, request()->token);

		if (!$validToken) {
			return response()->json(['expired' => true], 400);
		}
	}
}
