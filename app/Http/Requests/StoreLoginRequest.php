<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
			'user'           => ['required', 'min:3'],
			'password'       => ['required'],
			'remember_token' => '',
		];
	}

	public function messages()
	{
		return [
			'user'              => ['required' => __('validation.required_all'), 'min' => __('validation.auth.username.min')],
			'password.required' => __('validation.auth.password.required'),
		];
	}
}
