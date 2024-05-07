<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
	public function redirect()
	{
		$url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
		return response()->json(['url' => $url]);
	}

	public function callbackGoogle()
	{
		$googleUser = Socialite::driver('google')->stateless()->user();

		$user = User::where('email', $googleUser->email)->first();

		if ($user) {
			if (!$user->google_id) {
				return response()->json([
					'error' => __('email-verification.gmail_attempt_with_normal_email_error_message'),
				], 422);
			}

			$user->update([
				'google_id'     => $googleUser->id,
			]);
		} else {
			$user = User::create([
				'google_id'             => $googleUser->id,
				'username'              => $googleUser->name,
				'email'                 => $googleUser->email,
				'email_verified_at'     => now(),
				'profile_image'         => $googleUser->avatar,
			]);
		}

		auth()->login($user);
	}
}
