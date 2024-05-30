<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
	$this->user = User::factory()->create(['username' => 'test', 'email' => 'test@gmail.com', 'password' => 'password']);
});

test('should return error if user sends no data', function () {
	$this->actingAs($this->user)->patchJson(route('user.update'))->assertStatus(400)->assertExactJson(['error' => __('profile.no_changes')]);
});

test('should return error if username already exists', function () {
	$this->actingAs($this->user)->patchJson(route('user.update'), ['username' => 'test'])->assertJsonValidationErrors(['username' => __('validation.auth.username.unique')])->assertStatus(422);
});

test('should return error if user sends password without confirmation', function () {
	$this->actingAs($this->user)->patchJson(route('user.update'), ['password' => 'password'])->assertJsonValidationErrors(['password_confirmation' => __('validation.auth.password_confirmation.required')])->assertStatus(422);
});

test('should return error if google user tries to update password', function () {
	$googleUser = User::factory()->create(['google_id' => str()->random(10)]);
	$this->actingAs($googleUser)
		 ->patchJson(route('user.update'), [
		 	'password'              => 'password123',
		 	'password_confirmation' => 'password123',
		 ])->assertStatus(403)->assertExactJson(['errors' => ['password' => __('profile.gmail_password_error')]]);
});

test('should return error if user tries to update with same password', function () {
	$this->actingAs($this->user)->patchJson(route('user.update'), ['password' => 'password', 'password_confirmation' => 'password'])->assertJsonValidationErrors(['password' => __('email-verification.password_reset_new_password')])->assertStatus(422);
});

test('user can update image', function () {
	$this->actingAs($this->user)->patchJson(route('user.update'), [
		'profile_image'       => UploadedFile::fake()->image('updated_image.jpg'),
	])->assertStatus(200);
	$this->assertDatabaseHas('media', [
		'model_id'        => $this->user->id,
		'model_type'      => User::class,
		'collection_name' => 'user_images',
		'file_name'       => 'updated_image.jpg',
	]);
});

test('user can update username', function () {
	$this->actingAs($this->user)->patchJson(route('user.update'), [
		'username' => 'testing',
	]);
	$this->assertDatabaseHas('users', [
		'id'       => $this->user->id,
		'username' => 'testing',
	]);
});

test('user can update password successfully', function () {
	$this->actingAs($this->user)
		 ->patchJson(route('user.update'), [
		 	'password'              => 'newpassword',
		 	'password_confirmation' => 'newpassword',
		 ])->assertStatus(200);

	$this->assertTrue(Hash::check('newpassword', $this->user->fresh()->password));
});
