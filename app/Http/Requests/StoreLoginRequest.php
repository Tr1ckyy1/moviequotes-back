<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoginRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'email'          => ['required', 'email', Rule::exists('users', 'email')],
			'password'       => ['required'],
			'remember_token' => '',
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
			'password.required' => __('validation.auth.password.required'),
		];
	}
}
