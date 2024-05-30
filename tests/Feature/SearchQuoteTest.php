<?php

use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;

beforeEach(function () {
	$this->user = User::factory()->create(['username' => 'test', 'email' => 'test@gmail.com', 'password' => 'password']);
});

test('user can filter for movie name and quote in english but not in georgian', function () {
	$movieInception = Movie::factory()->create(['user_id' => $this->user->id, 'name' => ['en' => 'Inception', 'ka' => 'დასაწყისი']]);
	$movieRocky = Movie::factory()->create(['user_id' => $this->user->id, 'name' => ['en' => 'Rocky', 'ka' => 'როკი']]);
	Quote::factory()->withImage()->create(['quote' => ['en' => 'inception good movie', 'ka' => 'დასაწყისი კაი ფილმია'], 'movie_id' => $movieInception->id]);
	Quote::factory()->withImage()->create(['quote' => ['en' => 'inception quote', 'ka' => 'სხვა ციტატა'], 'movie_id' => $movieInception->id]);
	Quote::factory()->withImage()->create(['quote' => ['en' => 'random', 'ka' => 'რენდომ'], 'movie_id' => $movieRocky->id]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'en')->getJson(route('quote.index', ['filter' => ['movie.name' => 'rocky']]))->assertStatus(200)->assertJsonCount('1', 'data')->assertJsonFragment(['quote' => ['en' => 'random', 'ka' => 'რენდომ']]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'en')->getJson(route('quote.index', ['filter' => ['quote' => 'inception']]))->assertStatus(200)->assertJsonCount('2', 'data')->assertJsonFragment(['quote' => ['en' => 'inception good movie', 'ka' => 'დასაწყისი კაი ფილმია']])->assertJsonFragment(['quote' => ['en' => 'inception quote', 'ka' =>'სხვა ციტატა']]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'en')->getJson(route('quote.index', ['filter' => ['movie.name' => 'inception', 'quote' => 'inception good movie']]))->assertStatus(200)->assertJsonCount('1', 'data')->assertJsonFragment(['quote' => ['en' => 'inception good movie', 'ka' => 'დასაწყისი კაი ფილმია']]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'en')->getJson(route('quote.index', ['filter' => ['movie.name' => 'inception', 'quote' => 'inception']]))->assertStatus(200)->assertJsonCount('2', 'data')->assertJsonFragment(['quote' => ['en' => 'inception good movie', 'ka' => 'დასაწყისი კაი ფილმია']])->assertJsonFragment(['quote' => ['en' => 'inception quote', 'ka' =>'სხვა ციტატა']]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'en')->getJson(route('quote.index', ['filter' => ['movie.name' => 'დასაწყისი']]))->assertStatus(200)->assertJsonFragment(['data' => []]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'en')->getJson(route('quote.index', ['filter' => ['quote' => 'სხვა ციტატა']]))->assertStatus(200)->assertJsonFragment(['data' => []]);
});

test('user can filter for movie name and quote in georgian but not in english', function () {
	$movieInception = Movie::factory()->create(['user_id' => $this->user->id, 'name' => ['en' => 'Inception', 'ka' => 'დასაწყისი']]);
	$movieRocky = Movie::factory()->create(['user_id' => $this->user->id, 'name' => ['en' => 'Rocky', 'ka' => 'როკი']]);
	Quote::factory()->withImage()->create(['quote' => ['en' => 'inception good movie', 'ka' => 'დასაწყისი კაი ფილმია'], 'movie_id' => $movieInception->id]);
	Quote::factory()->withImage()->create(['quote' => ['en' => 'inception quote', 'ka' => 'დასაწყისი სხვა ციტატა'], 'movie_id' => $movieInception->id]);
	Quote::factory()->withImage()->create(['quote' => ['en' => 'random', 'ka' => 'რენდომ'], 'movie_id' => $movieRocky->id]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'ka')->getJson(route('quote.index', ['filter' => ['movie.name' => 'როკი']]))->assertStatus(200)->assertJsonCount('1', 'data')->assertJsonFragment(['quote' => ['en' => 'random', 'ka' => 'რენდომ']]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'ka')->getJson(route('quote.index', ['filter' => ['quote' => 'დასაწყისი']]))->assertStatus(200)->assertJsonCount('2', 'data')->assertJsonFragment(['quote' => ['en' => 'inception good movie', 'ka' => 'დასაწყისი კაი ფილმია']])->assertJsonFragment(['quote' => ['en' => 'inception quote', 'ka' =>'დასაწყისი სხვა ციტატა']]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'ka')->getJson(route('quote.index', ['filter' => ['movie.name' => 'დასაწყისი', 'quote' => 'დასაწყისი კაი ფილმია']]))->assertStatus(200)->assertJsonCount('1', 'data')->assertJsonFragment(['quote' => ['en' => 'inception good movie', 'ka' => 'დასაწყისი კაი ფილმია']]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'ka')->getJson(route('quote.index', ['filter' => ['movie.name' => 'დასაწყისი', 'quote' => 'დასაწყისი']]))->assertStatus(200)->assertJsonCount('2', 'data')->assertJsonFragment(['quote' => ['en' => 'inception good movie', 'ka' => 'დასაწყისი კაი ფილმია']])->assertJsonFragment(['quote' => ['en' => 'inception quote', 'ka' =>'დასაწყისი სხვა ციტატა']]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'ka')->getJson(route('quote.index', ['filter' => ['movie.name' => 'inception']]))->assertStatus(200)->assertJsonFragment(['data' => []]);

	$this->actingAs($this->user)->withCredentials()->withUnencryptedCookie('locale', 'ka')->getJson(route('quote.index', ['filter' => ['quote' => 'inception good movie']]))->assertStatus(200)->assertJsonFragment(['data' => []]);
});
