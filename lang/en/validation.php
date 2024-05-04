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
		'token' =>  ['required' => 'Token is required'],
	],
];
