<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
	public function show()
	{
		return new UserResource(User::find(auth()->id()));
	}

	public function update(StoreUpdateProfileRequest $request): JsonResponse
	{
		$credentials = $request->validated();
		$user = User::findOrFail(auth()->id());

		if (
			!isset($credentials['username']) &&
			!isset($credentials['password']) &&
			!isset($credentials['profile_image'])
		) {
			return response()->json(['error' => __('profile.no_changes')], 400);
		}

		if (isset($credentials['password'])) {
			if ($user->google_id) {
				return response()->json(['errors' => ['password' => __('profile.gmail_password_error')]], 403);
			}
			if (Hash::check($credentials['password'], $user->password)) {
				return response()->json(['errors' => ['password' => __('email-verification.password_reset_new_password')]], 422);
			}
			$user->password = bcrypt($credentials['password']);
		}

		if (isset($credentials['username'])) {
			$user->username = $credentials['username'];
		}

		if ($request->hasFile('profile_image')) {
			$user->addMediaFromRequest('profile_image')->toMediaCollection('user_images');
		}

		$user->save();
		return response()->json(['password_message' => isset($credentials['password']) ? __('profile.updated_success_password') : null, 'message' => __('profile.updated_success')]);
	}
}
