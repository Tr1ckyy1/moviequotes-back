<?php

use App\Models\Category;
use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
	$this->user = User::factory()->create(['username' => 'test', 'email' => 'test@gmail.com', 'password' => 'password']);
	$this->seed(CategorySeeder::class);
});

test('should return error if unauthenticated user is trying to fetch movies', function () {
	Movie::factory(5)->withImage()->create(['user_id' => $this->user->id]);
	$this->getJson(route('movie.index'))->assertStatus(401);
});

test('should return error if unauthorized user is trying to fetch movies', function () {
	Movie::factory(5)->withImage()->create(['user_id' => $this->user->id]);
	$user = User::factory()->create(['email_verified_at' => null]);
	$this->actingAs($user)->getJson(route('movie.index'))->assertStatus(403);
});

test('should return error if movie id does not exist', function () {
	Movie::factory(5)->withImage()->create(['user_id' => $this->user->id]);
	$this->actingAs($this->user)->getJson(route('movie.show', 6))->assertStatus(404);
});

test('should return regex error if name.en is provided in georgian', function () {
	$this->actingAs($this->user)->postJson(route('movie.store'), [
		'name' => ['en' => 'ტესტ'],
	])->assertJsonValidationErrors(['name.en' => __('validation.regex_en')])->assertStatus(422);
});

test('should return regex error if name.ka is provided in english', function () {
	$this->actingAs($this->user)->postJson(route('movie.store'), [
		'name' => ['ka' => 'test'],
	])->assertJsonValidationErrors(['name.ka' => __('validation.regex_ka')])->assertStatus(422);
});

test('should return error if year is below 1900', function () {
	$this->actingAs($this->user)->postJson(route('movie.store'), [
		'year' => 1899,
	])->assertJsonValidationErrors(['year' => __('validation.movie.year.min')])->assertStatus(422);
});

test('should return error if year is above current year', function () {
	$this->actingAs($this->user)->postJson(route('movie.store'), [
		'year' => 2025,
	])->assertJsonValidationErrors(['year' => __('validation.movie.year.max')])->assertStatus(422);
});

test('should return error if file is not an image', function () {
	$this->actingAs($this->user)->postJson(route('movie.store'), [
		'image'       => UploadedFile::fake()->create('not_an_image.txt'),
	])->assertJsonValidationErrors([
		'image' => __('profile.file_must_be_image'),
	])->assertStatus(422);
});

test('should return error if categories were not provided', function () {
	$this->actingAs($this->user)->postJson(route('movie.store'), [
		'name'        => ['en' => 'Test Movie', 'ka' => 'სატესტო ფილმი'],
		'year'        => 2022,
		'director'    => ['en' => 'John Doe', 'ka' => 'ჯონ დო'],
		'description' => ['en' => 'A great movie.', 'ka' => 'შესანიშნავი ფილმი.'],
		'image'       => UploadedFile::fake()->image('movie.jpg'),
	])->assertStatus(422);
});

test('should return empty data if user id is no equal to movie user id', function () {
	Movie::factory(5)->withImage()->create(['user_id' => $this->user->id]);
	$otherUser = User::factory()->create();
	$this->actingAs($otherUser)->getJson(route('movie.index'))->assertStatus(200)
	->assertExactJson(['data' => []]);
});

test('user can fetch all movies', function () {
	Movie::factory(5)->withImage()->create(['user_id' => $this->user->id]);
	$this->actingAs($this->user)->getJson(route('movie.index'))->assertStatus(200)->assertJsonCount(5, 'data');
});

test('user can fetch single movie', function () {
	$movies = Movie::factory(5)->withImage()->create(['user_id' => $this->user->id]);
	$movie = $movies->first();
	$this->actingAs($this->user)->getJson(route('movie.show', $movie->id))
	->assertStatus(200)
	->assertJsonFragment([
		'id'   => $movie->id,
		'name' => ['en' => $movie->getTranslation('name', 'en'), 'ka' => $movie->getTranslation('name', 'ka')],
		'year' => $movie->year,
	]);
});

test('user can add a movie', function () {
	$adventureCategoryId = Category::where('name->en', 'Adventure')->first()->id;
	$animationCategoryId = Category::where('name->en', 'Animation')->first()->id;

	$this->actingAs($this->user)->postJson(route('movie.store'), [
		'name'        => ['en' => 'Test Movie', 'ka' => 'სატესტო ფილმი'],
		'year'        => 2022,
		'director'    => ['en' => 'John Doe', 'ka' => 'ჯონ დო'],
		'description' => ['en' => 'A great movie.', 'ka' => 'შესანიშნავი ფილმი.'],
		'categories'  => [$adventureCategoryId, $animationCategoryId],
		'image'       => UploadedFile::fake()->image('movie.jpg'),
	])->assertStatus(200);
	$this->assertDatabaseHas('movies', ['name->en' => 'Test Movie']);
});

test('user can edit movie', function () {
	$movie = Movie::factory()->create(['user_id' => $this->user->id]);

	$adventureCategoryId = Category::where('name->en', 'Adventure')->first()->id;
	$animationCategoryId = Category::where('name->en', 'Animation')->first()->id;

	$this->actingAs($this->user)->patchJson(route('movie.update', $movie->id), [
		'name'        => ['en' => 'Updated Movie Name', 'ka' => 'განახლებული ფილმის სახელი'],
		'year'        => 2023,
		'director'    => ['en' => 'Updated Director', 'ka' => 'განახლებული რეჟისორი'],
		'description' => ['en' => 'Updated movie description.', 'ka' => 'განახლებული ფილმის აღწერა.'],
		'categories'  => [$adventureCategoryId, $animationCategoryId],
		'image'       => UploadedFile::fake()->image('updated_movie.jpg'),
	])->assertStatus(200);

	$this->assertDatabaseHas('movies', [
		'id'              => $movie->id,
		'name->en'        => 'Updated Movie Name',
		'year'            => 2023,
		'director->en'    => 'Updated Director',
		'description->en' => 'Updated movie description.',
	]);
});

test('user can delete a movie', function () {
	$movie = Movie::factory()->create(['user_id' => $this->user->id]);
	$this->actingAs($this->user)->deleteJson(route('movie.destroy', $movie->id))
		->assertStatus(200);
});

test('users can fetch quotes', function () {
	Quote::factory(5)->withImage()->create();
	$this->actingAs($this->user)->getJson(route('quote.index'))->assertStatus(200)->assertJsonCount(5, 'data');
});

test('user can fetch single quote', function () {
	$quotes = Quote::factory(5)->withImage()->create();
	$quote = $quotes->first();
	$this->actingAs($this->user)->getJson(route('quote.show', $quote->id))->assertStatus(200)->assertJsonFragment(['id' => $quote->id]);
});

test('user can add a quote', function () {
	$movie = Movie::factory()->create();
	$this->actingAs($this->user)->postJson(route('quote.store'), [
		'quote'       => ['en' => 'new test quote', 'ka' => 'ახალი სატესტო ციტატა'],
		'movie'       => $movie->id,
		'image'       => UploadedFile::fake()->image('quote.jpg'),
	])->assertStatus(200);

	$this->assertDatabaseHas('quotes', ['quote->en' => 'new test quote']);
});

test('user can edit quote', function () {
	$quote = Quote::factory()->create();

	$this->actingAs($this->user)->patchJson(route('quote.update', $quote->id), [
		'quote'       => ['en' => 'updated quote name', 'ka' => 'განახლებული ციტატა'],
		'image'       => UploadedFile::fake()->image('updated_quote.jpg'),
	])->assertStatus(200);

	$this->assertDatabaseHas('quotes', ['quote->en' => 'updated quote name', 'quote->ka' => 'განახლებული ციტატა']);
});

test('user can delete quote', function () {
	$quote = Quote::factory()->create();
	$this->actingAs($this->user)->deleteJson(route('quote.destroy', $quote->id))->assertStatus(200);
});
