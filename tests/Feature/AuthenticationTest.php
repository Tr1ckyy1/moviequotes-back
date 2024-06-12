<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Crypt;

test('signup should have error if inputs are empty', function () {
	$this->postJson(route('auth.signup'))->assertJsonValidationErrors(['username' => 'required', 'email' => 'required', 'password' =>'required', 'password_confirmation' => 'required'])->assertStatus(422);
});

test('user able to signup successfully with provided credentials', function () {
	$this->postJson(route('auth.signup'), ['username' => 'testing', 'email' => 'testing@gmail.com', 'password'=> 'password', 'password_confirmation' => 'password'])->assertStatus(200);
	$this->assertDatabaseHas('users', ['username' => 'testing', 'email' => 'testing@gmail.com']);
});

test('input should have error if length is less than three characters', function () {
	$this->postJson(route('auth.signup'), ['username' => 'a'])->assertJsonValidationErrors(['username' => __('validation.auth.username.min')])->assertStatus(422);
});

test('input should have error if length is more than fifteen characters', function () {
	$this->postJson(route('auth.signup'), ['username' => 'abcdefghijklmnop'])->assertJsonValidationErrors(['username' => __('validation.auth.username.max')])->assertStatus(422);
});

test('input should have error if regex pattern is not matched', function () {
	$this->postJson(route('auth.signup'), ['username' => 'Test'])->assertJsonValidationErrors(['username' => __('validation.auth.username.regex')])->assertStatus(422);
});

test('username should have error if user already exists', function () {
	User::factory()->create(['username' => 'test']);
	$this->postJson(route('auth.signup'), ['username' => 'test'])->assertJsonValidationErrors(['username' => __('validation.auth.username.unique')])->assertStatus(422);
});

test('email should have error if email is invalid', function () {
	$this->postJson(route('auth.signup'), ['email' => 'test-gmail.com'])->assertJsonValidationErrors(['email' => 'email'])->assertStatus(422);
});

test('email should have error if email already exists', function () {
	User::factory()->create(['email' => 'test@gmail.com']);

	$this->postJson(route('auth.signup'), ['email' => 'test@gmail.com'])->assertJsonValidationErrors(['email' => __('validation.auth.email.unique')])->assertStatus(422);
});

test('password confirmation should have error if it does not match password', function () {
	$this->postJson(route('auth.signup'), ['password' => 'password1', 'password_confirmation' => 'password2'])->assertJsonValidationErrors(['password_confirmation' => __('validation.auth.password_confirmation.password_mismatch')])->assertStatus(422);
});

test('signup should return error if authorized user is trying to sign up', function () {
	$user = User::factory()->create(['email' => 'test@gmail.com']);
	$this->actingAs($user)->postJson(route('auth.signup'))->assertStatus(401);
});

test('login should have error if email does not exist in database', function () {
	$this->postJson(route('auth.login'), ['user' => 'abcdefghijklmnop@aa', 'password' => 'password'])->assertJsonValidationErrors(['user' => __('validation.user_or_email_doesnt_exist')])->assertStatus(422);
});

test('login should have error if credentials dont match the existing user credentials', function () {
	User::factory()->create(['email' => 'test@gmail.com', 'password' => 'password1']);
	$this->postJson(route('auth.login'), ['user' => 'test@gmail.com', 'password' => 'password2'])->assertJsonValidationErrors(['user' => __('auth.auth_login_credentials_dont_match')])->assertStatus(422);
});

test('login should have error if user has not verified email', function () {
	User::factory()->create(['email' => 'test@gmail.com', 'password' => 'password1', 'email_verified_at' => null]);
	$this->postJson(route('auth.login'), ['user' => 'test@gmail.com', 'password' => 'password'])->assertJsonValidationErrors(['user' => __('auth.user_is_not_verified_error_message')])->assertStatus(422);
});

test('user able to login successfully with provided credentials', function () {
	User::factory()->create(['email' => 'test@gmail.com', 'password' => 'password']);
	$this->postJson(route('auth.login'), ['user' => 'test@gmail.com', 'password'=>'password'])->assertStatus(200);
});

test('user able to logout successfully', function () {
	$user = User::factory()->create(['email' => 'test@gmail.com', 'password' => 'password']);
	$this->actingAs($user)->postJson(route('auth.logout'))->assertStatus(200);
});

test('user able to request password reset', function () {
	Notification::fake();

	$user = User::factory()->create(['email' => 'test@gmail.com']);

	$response = $this->postJson(route('auth.forgot_password'), [
		'email' => 'test@gmail.com',
	]);

	$response->assertStatus(200);

	Notification::assertSentTo($user, ResetPasswordNotification::class);
});

test('user can reset password with valid token', function () {
	$user = User::factory()->create([
		'email'    => 'test@gmail.com',
		'password' => 'password123',
	]);

	$token = Password::createToken($user);

	$response = $this->postJson(route('auth.reset_password'), [
		'email'                 => Crypt::encryptString('test@gmail.com'),
		'token'                 => $token,
		'password'              => 'newpassword123',
		'password_confirmation' => 'newpassword123',
	]);

	$response->assertStatus(200);

	$updatedUser = User::find($user->id);

	$this->assertNotEquals($updatedUser->password, $user->password);
});

test('reset password should return error if new password is same as old one', function () {
	$user = User::factory()->create([
		'email'    => 'test@gmail.com',
		'password' => 'password123',
	]);

	$token = Password::createToken($user);

	$response = $this->postJson(route('auth.reset_password'), [
		'email'                 => Crypt::encryptString('test@gmail.com'),
		'token'                 => $token,
		'password'              => 'password123',
		'password_confirmation' => 'password123',
	]);

	$response->assertStatus(422);

	$updatedUser = User::find($user->id);

	$this->assertEquals($updatedUser->password, $user->password);
});

test('reset password should return error if invalid token is being sent', function () {
	$user = User::factory()->create([
		'email'    => 'test@gmail.com',
		'password' => 'password123',
	]);

	$token = Password::createToken($user);

	$response = $this->postJson(route('auth.reset_password'), [
		'email'                 => Crypt::encryptString('test@gmail.com'),
		'token'                 => $token . '1',
		'password'              => 'newpassword123',
		'password_confirmation' => 'newpassword123',
	]);

	$response->assertStatus(400);

	$updatedUser = User::find($user->id);

	$this->assertTrue(Hash::check('password123', $updatedUser->password));
});

test('reset password should return error for expired token', function () {
	$user = User::factory()->create([
		'email'    => 'test@gmail.com',
		'password' => 'password123',
	]);

	$token = Password::createToken($user);

	Carbon::setTestNow(Carbon::now()->addMinutes(config('auth.passwords.users.expire') + 1));

	$response = $this->postJson(route('auth.reset_password'), [
		'email'                 => Crypt::encryptString('test@gmail.com'),
		'token'                 => $token,
		'password'              => 'newpassword123',
		'password_confirmation' => 'newpassword123',
	]);

	$response->assertStatus(400);

	$updatedUser = User::find($user->id);
	$this->assertTrue(Hash::check('password123', $updatedUser->password));
});
