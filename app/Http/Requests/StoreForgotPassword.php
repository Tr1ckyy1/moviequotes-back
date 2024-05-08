<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreForgotPassword extends FormRequest
{
	public function rules(): array
	{
		return [
			'email'          => ['required', 'email', Rule::exists('users', 'email')],
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
		];
	}
}
