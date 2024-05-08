<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
	public function verify($id)
	{
		$user = User::findOrFail($id);

		if ($user->hasVerifiedEmail()) {
			return response()->json([
				'already_verified' => true], 400);
		} else {
			$user->markEmailAsVerified();
		}
	}

	public function resend(Request $request)
	{
		$id = $request->input('id');
		$expires = $request->input('expires');

		$user = User::find($id);

		if (Carbon::now()->gte(Carbon::createFromTimestamp($expires)) && !$user->hasVerifiedEmail()) {
			$user->sendEmailVerificationNotification();
		}

		if ($user->hasVerifiedEmail()) {
			return response()->json([
				'already_verified' => true], 400);
		}
	}
}
