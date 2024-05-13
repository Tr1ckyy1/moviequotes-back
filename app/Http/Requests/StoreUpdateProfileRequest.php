<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateProfileRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'username'                  => ['min:3', 'max:15', 'regex:/^[a-z0-9]+$/', Rule::unique('users', 'username')],
			'password'                  => ['min:8', 'max:15', 'regex:/^[a-z0-9]+$/'],
			'password_confirmation'     => ['required_with:password', 'same:password'],
			'profile_image'             => ['image', 'max:2048'],
		];
	}

	public function messages(): array
	{
		return [
			'username' => [
				'min'                   => __('validation.auth.username.min'),
				'max'                   => __('validation.auth.username.max'),
				'regex'                 => __('validation.auth.username.regex'),
				'unique'                => __('validation.auth.username.unique'),
			],
			'password' => [
				'min'                   => __('validation.auth.password.min'),
				'max'                   => __('validation.auth.password.max'),
				'regex'                 => __('validation.auth.password.regex'),
			],
			'password_confirmation' => [
				'required_with' => __('validation.auth.password_confirmation.required'),
				'same'          => __('validation.auth.password_confirmation.password_mismatch'),
			],
			'profile_image.image'      => __('profile.file_must_be_image'),
			'profile_image.uploaded'   => __('profile.failed_to_upload'),
			'profile_image.max'        => __('profile.size_too_big'),
		];
	}
}
