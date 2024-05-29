<?php

use App\Models\Like;
use App\Models\Quote;
use App\Models\User;

beforeEach(function () {
	$this->user = User::factory()->create(['username' => 'test', 'email' => 'test@gmail.com', 'password' => 'password']);
});

test('user can like a post if he hasnt liked it yet', function () {
	$quote = Quote::factory()->create();
	$this->actingAs($this->user)->postJson(route('quote.likes', $quote->id))->assertStatus(200);
	$this->assertDatabaseHas('likes', [
		'user_id'  => $this->user->id,
		'quote_id' => $quote->id,
	]);
});

test('user can unlike a post if he had already liked it', function () {
	$quote = Quote::factory()->create();
	Like::factory()->create(['user_id' => $this->user->id, 'quote_id' => $quote->id]);
	$this->actingAs($this->user)->postJson(route('quote.likes', $quote->id))->assertStatus(200);

	$this->assertDatabaseMissing('likes', [
		'user_id'  => $this->user->id,
		'quote_id' => $quote->id,
	]);
});

test('user can add a comment to the post', function () {
	$quote = Quote::factory()->create();
	$this->actingAs($this->user)->postJson(route('quote.comments', $quote->id), ['comment' => 'random comment'])->assertStatus(200);
	$this->assertDatabaseHas('comments', [
		'user_id'  => $this->user->id,
		'quote_id' => $quote->id,
		'comment'  => 'random comment',
	]);
});
