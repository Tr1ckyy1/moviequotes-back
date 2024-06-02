<?php

return [
	'auth' => [
		'username' => [
			'required' => 'Username is required',
			'min'      => 'Must be at least 3 characters',
			'max'      => 'Cannot exceed 15 characters',
			'regex'    => 'Must only contain lowercase letters and numbers',
			'unique'   => 'The username has already been taken',
		],
		'email' => [
			'required'          => 'Email is required',
			'valid_email'       => 'Must be a valid email address',
			'unique'            => 'The email has already been taken',
			'exists'            => 'The provided email was not found',
		],
		'password' => [
			'required' => 'Password is required',
			'min'      => 'Must be at least 8 characters',
			'max'      => 'Cannot exceed 15 characters',
			'regex'    => 'Must only contain lowercase letters and numbers',
		],
		'password_confirmation' => [
			'required'          => 'Password confirmation is required',
			'password_mismatch' => 'Passwords do not match',
		],
		'token'   => ['required' => 'Token is required'],
		'image'   => 'File has to be an image',
	],
	'regex_en'=> 'English letters only',
	'regex_ka'=> 'Georgian letters only',
	'movie'   => [
		'name' => [
			'required' => 'Movie name is required',
		],
		'categories' => 'Please choose at least one category',
		'year'       => [
			'required' => 'Movie year is required',
			'min'      => 'Must be at least from 1900',
			'max'      => "Cannot exceed today's date",
		],
		'director' => [
			'required' => 'Director is required',
		],
		'description' => [
			'required' => 'Description is required',
		],
		'quote' => [
			'required' => 'Quote is required',
			'movie'    => 'Please choose a movie',
		],
		'image' => [
			'required' => 'Image is required',
		],
	],
	'required_all'               => 'This field is required',
	'user_or_email_doesnt_exist' => 'No such user exists',
];
