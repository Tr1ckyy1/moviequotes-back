<?php

use App\Events\QuoteCommented;
use App\Events\QuoteCommentNotification;
use App\Events\QuoteLiked;
use App\Events\QuoteLikeNotification;
use App\Models\Like;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
	$this->user = User::factory()->create(['username' => 'test', 'email' => 'test@gmail.com', 'password' => 'password']);
});

test('post like is broadcasted', function () {
	Event::fake();
	$quote = Quote::factory()->create();

	$this->actingAs($this->user)->postJson(route('quote.likes', $quote->id))->assertStatus(200);

	Event::assertDispatched(QuoteLiked::class);
});

test('post comment is broadcasted', function () {
	Event::fake();
	$quote = Quote::factory()->create();
	$this->actingAs($this->user)->postJson(route('quote.comments', $quote->id), ['comment' => 'random comment'])->assertStatus(200);

	Event::assertDispatched(QuoteCommented::class);
});

test('unliking liked post is broadcasted', function () {
	Event::fake();
	$quote = Quote::factory()->create();
	Like::factory()->create(['user_id' => $this->user->id, 'quote_id' => $quote->id]);

	$this->actingAs($this->user)->postJson(route('quote.likes', $quote->id))->assertStatus(200);

	Event::assertDispatched(QuoteLiked::class);
});

test('like notification is not broadcasted to post creator by post creator', function () {
	Event::fake();
	$quote = Quote::factory()->create(['user_id' => $this->user->id]);

	$this->actingAs($this->user)->postJson(route('quote.likes', $quote->id))->assertStatus(200);

	Event::assertNotDispatched(QuoteLikeNotification::class);
});

test('comment notification is not broadcasted to post creator by post creator', function () {
	Event::fake();
	$quote = Quote::factory()->create(['user_id' => $this->user->id]);
	$this->actingAs($this->user)->postJson(route('quote.comments', $quote->id), ['comment' => 'random comment'])->assertStatus(200);

	Event::assertNotDispatched(QuoteCommentNotification::class);
});

test('like notification is broadcasted to post creator by other user', function () {
	Event::fake();
	$quote = Quote::factory()->create(['user_id' => $this->user->id]);

	$otherUser = User::factory()->create();
	$this->actingAs($otherUser)->postJson(route('quote.likes', $quote->id))->assertStatus(200);

	Event::assertDispatched(QuoteLikeNotification::class);
});

test('comment notification is broadcasted to post creator by other user', function () {
	Event::fake();
	$quote = Quote::factory()->create(['user_id' => $this->user->id]);

	$otherUser = User::factory()->create();
	$this->actingAs($otherUser)->postJson(route('quote.comments', $quote->id), ['comment' => 'random comment'])->assertStatus(200);

	Event::assertDispatched(QuoteCommentNotification::class);
});
