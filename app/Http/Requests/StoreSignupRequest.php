<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSignupRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'username'              => ['required', 'min:3', 'max:15', 'regex:/^[a-z0-9]+$/', Rule::unique('users', 'username')],
			'email'                 => ['required', 'email', Rule::unique('users', 'email')],
			'password'              => ['required', 'min:8', 'max:15', 'regex:/^[a-z0-9]+$/'],
			'password_confirmation' => ['required', 'same:password'],
		];
	}

	public function messages()
	{
		return [
			'username' => [
				'required'              => __('validation.auth.username.required'),
				'min'                   => __('validation.auth.username.min'),
				'max'                   => __('validation.auth.username.max'),
				'regex'                 => __('validation.auth.username.regex'),
				'unique'                => __('validation.auth.username.unique'),
			],
			'email' => [
				'required'                 => __('validation.auth.email.required'),
				'email'                    => __('validation.auth.email.valid_email'),
				'unique'                   => __('validation.auth.email.unique'),
			],
			'password' => [
				'required'              => __('validation.auth.password.required'),
				'min'                   => __('validation.auth.password.min'),
				'max'                   => __('validation.auth.password.max'),
				'regex'                 => __('validation.auth.password.regex'),
			],
			'password_confirmation' => [
				'required' => __('validation.auth.password_confirmation.required'),
				'same'     => __('validation.auth.password_confirmation.password_mismatch'),
			],
		];
	}
}
