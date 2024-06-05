<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
	public function rules(): array
	{
		return [
			'quote.en'        => ['required', 'regex:/^[A-Za-z0-9\s.,:;\'"`?!()-]+$/'],
			'quote.ka'        => ['required', 'regex:/^[\x{10A0}-\x{10FF}0-9\s.,:;\'"`?!()-]+$/u'],
			'image'           => ['required', 'image', 'max:2048'],
		];
	}

	public function messages(): array
	{
		return [
			'quote.en' => [
				'required' => __('validation.movie.quote.required'),
				'regex'    => __('validation.regex_en'),
			],
			'quote.ka' => [
				'required' => __('validation.movie.quote.required'),
				'regex'    => __('validation.regex_ka'),
			],
			'image' => [
				'required'   => __('validation.movie.image.required'),
				'image'      => __('profile.file_must_be_image'),
				'uploaded'   => __('profile.failed_to_upload'),
				'max'        => __('profile.size_too_big'),
			],
		];
	}
}
