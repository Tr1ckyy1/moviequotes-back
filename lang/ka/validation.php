<?php

return [
	'auth' => [
		'username' => [
			'required' => 'მომხმარებელი სავალდებულოა',
			'min'      => 'მინიმუმ 3 სიმბოლო',
			'max'      => 'მაქსიმუმ 15 სიმბოლო',
			'regex'    => 'მხოლოდ დაბალი რეგისტრის ლათინური სიმბოლოები და რიცხვები',
			'unique'   => 'მსგავსი მომხმარებელი უკვე არსებობს',
		],
		'email' => [
			'required'          => 'ელ.ფოსტა სავალდებულოა',
			'valid_email'       => 'ელ.ფოსტა უნდა იყოს ვალიდური ელ.ფოსტის მისამართი',
			'unique'            => 'მსგავსი ელ.ფოსტა უკვე არსებობს',
			'exists'            => 'მოცემული ელ.ფოსტა ვერ მოიძებნა',
		],
		'password' => [
			'required' => 'პაროლი სავალდებულოა',
			'min'      => 'მინიმუმ 8 სიმბოლო',
			'max'      => 'მაქსიმუმ 15 სიმბოლო',
			'regex'    => 'მხოლოდ დაბალი რეგისტრის ლათინური სიმბოლოები და რიცხვები',
		],
		'password_confirmation' => [
			'required'          => 'პაროლის დადასტურება სავალდებულოა',
			'password_mismatch' => 'პაროლები არ ემთხვევა ერთმანეთს',
		],
		'token' => ['required' => 'ტოკენი სავალდებულოა'],
		'image' => 'ფაილი უნდა იყოს სურათი',
	],
];
