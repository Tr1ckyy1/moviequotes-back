<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResetPassword extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'email'                 => ['required', 'email', Rule::exists('users', 'email')],
			'password'              => ['required', 'min:8', 'max:15', 'regex:/^[a-z0-9]+$/'],
			'password_confirmation' => ['same:password', 'required'],
			'token'                 => ['required'],
		];
	}

	public function messages()
	{
		return [
			'email' => [
				'required'                  => __('validation.auth.email.required'),
				'email'                     => __('validation.auth.email.valid_email'),
				'exists'                    => __('validation.auth.email.exists'),
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
			'token.required' => __('validation.auth.token.required'),
		];
	}
}
