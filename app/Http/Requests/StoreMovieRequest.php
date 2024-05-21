<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
	public function rules(): array
	{
		return [
			'name.en'         => ['required', 'regex:'],
			'name.ka'         => ['required', 'regex:/^[A-Za-z0-9\s.,:;\'"`-]+$/'],
			'year'            => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
			'director.en'     => ['required', 'regex:'],
			'director.ka'     => ['required', 'regex:/^[A-Za-z0-9\s.,:;\'"`-]+$/'],
			'description.en'  => ['required', 'regex:'],
			'description.ka'  => ['required', 'regex:/^[A-Za-z0-9\s.,:;\'"`-]+$/'],
			'image'           => ['required', 'image', 'max:2048'],
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
				'required'   => __('validation.movie.image.required'),
				'image'      => __('profile.file_must_be_image'),
				'uploaded'   => __('profile.failed_to_upload'),
				'max'        => __('profile.size_too_big'),
			],
		];
	}
}
