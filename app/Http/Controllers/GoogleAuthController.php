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
		// profile image if gmail account has profile img

		$googleUser = Socialite::driver('google')->stateless()->user();
		$emailExists = User::where('email', $googleUser->email)->first();
		if ($emailExists && !$emailExists->google_id) {
			return response()->json(['error' => __('email-verification.gmail_attempt_with_normal_email_error_message')], 403);
		}

		$user = User::updateOrCreate(
			[
				'google_id' => $googleUser->id,
			],
			[
				'username'                 => $googleUser->name,
				'email'                    => $googleUser->email,
				'email_verified_at'        => now(),
			]
		);
		auth()->login($user);
	}
}
