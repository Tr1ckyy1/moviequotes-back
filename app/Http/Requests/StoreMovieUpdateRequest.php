<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieUpdateRequest extends FormRequest
{
	public function rules(): array
	{
		return [
			'name.en'         => ['required', 'regex:/^[A-Za-z0-9\s.,:;\'"`?!-]+$/'],
			'name.ka'         => ['required', 'regex:/^[\x{10A0}-\x{10FF}0-9\s.,:;\'"`?!-]+$/u'],
			'year'            => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
			'director.en'     => ['required', 'regex:/^[A-Za-z0-9\s.,:;\'"`?!-]+$/'],
			'director.ka'     => ['required', 'regex:/^[\x{10A0}-\x{10FF}0-9\s.,:;\'"`?!-]+$/u'],
			'description.en'  => ['required', 'regex:/^[A-Za-z0-9\s.,:;\'"`?!-]+$/'],
			'description.ka'  => ['required', 'regex:/^[\x{10A0}-\x{10FF}0-9\s.,:;\'"`?!-]+$/u'],
			'image'           => ['image', 'max:2048'],
		];
	}

	public function messages(): array
	{
		return [
			'name.en' => [
				'required' => __('validation.movie.name.required'),
				'regex'    => __('validation.regex_en'),
			],
			'name.ka' => [
				'required' => __('validation.movie.name.required'),
				'regex'    => __('validation.regex_ka'),
			],
			'year' => [
				'required' => __('validation.movie.year.required'),
				'min'      => __('validation.movie.year.min'),
				'max'      => __('validation.movie.year.max'),
			],
			'director.en' => [
				'required' => __('validation.movie.director.required'),
				'regex'    => __('validation.regex_en'),
			],
			'director.ka' => [
				'required' => __('validation.movie.director.required'),
				'regex'    => __('validation.regex_ka'),
			],
			'description.en' => [
				'required' => __('validation.movie.description.required'),
				'regex'    => __('validation.regex_en'),
			],
			'description.ka' => [
				'required' => __('validation.movie.description.required'),
				'regex'    => __('validation.regex_ka'),
			],
			'image' => [
				'image'      => __('profile.file_must_be_image'),
				'uploaded'   => __('profile.failed_to_upload'),
				'max'        => __('profile.size_too_big'),
			],
		];
	}
}
