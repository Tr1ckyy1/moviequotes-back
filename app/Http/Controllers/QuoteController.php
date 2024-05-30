<?php

namespace App\Http\Controllers;

use App\Events\QuoteCommented;
use App\Events\QuoteCommentNotification;
use App\Events\QuoteLiked;
use App\Events\QuoteLikeNotification;
use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\StoreQuoteUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\QuoteResource;
use App\Models\Notification;
use App\Models\Quote;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class QuoteController extends Controller
{
	public function index()
	{
		$locale = app()->getLocale();
		return QuoteResource::collection(
			QueryBuilder::for(Quote::class)
				->allowedFilters([
					AllowedFilter::callback('quote', function ($query, $value) use ($locale) {
						$query->whereRaw('LOWER(JSON_EXTRACT(`quote`, \'$."' . "$locale" . '"\')) like ?', ['%' . strtolower($value) . '%']);
					}),
					AllowedFilter::callback('movie.name', function ($query, $value) use ($locale) {
						$query->whereHas('movie', function ($query) use ($locale, $value) {
							$query->whereRaw('LOWER(JSON_EXTRACT(`name`, \'$."' . "$locale" . '"\')) like ?', ['%' . strtolower($value) . '%']);
						});
					}),
				])
				->with('user', 'movie', 'likes', 'comments', 'comments.user')
				->latest()
				->paginate(10)
		);
	}

	public function show(Quote $quote)
	{
		return new QuoteResource($quote->load('likes', 'comments', 'comments.user', 'user', 'movie'));
	}

	public function store(StoreQuoteRequest $request)
	{
		if (!$request->has('movie')) {
			return response()->json(['errors' => ['movie' => __('validation.movie.quote.movie')]], 422);
		}

		$quote = Quote::create([...$request->validated(), 'user_id' => auth()->id(), 'movie_id' => $request->movie]);

		if ($request->hasFile('image')) {
			$quote->addMediaFromRequest('image')->toMediaCollection('quote_images');
		}

		$quote->save();
	}

	public function update(StoreQuoteUpdateRequest $request, Quote $quote)
	{
		$quote->update($request->validated());

		if ($request->hasFile('image')) {
			$quote->addMediaFromRequest('image')->toMediaCollection('quote_images');
		}

		$quote->save();
	}

	public function updateLike(Quote $quote)
	{
		$likeExists = $quote->likes()->where('user_id', auth()->id())->first();

		if ($likeExists) {
			$likeExists->delete();
			broadcast(new QuoteLiked(['like_id' => $likeExists->id, 'quote_id' => $quote->id]));
		} else {
			$like = $quote->likes()->create([
				'user_id' => auth()->id(),
			]);
			broadcast(new QuoteLiked(['quote_id' => $quote->id, 'like' => $like]));
			if (auth()->id() !== $quote->user_id) {
				$notification = Notification::create(['user_id_from' => auth()->id(), 'user_id_to'=>$quote->user_id, 'type' => 'like', 'quote_id' => $quote->id]);
				broadcast(new QuoteLikeNotification(new NotificationResource($notification)));
			}
		}
	}

	public function addComment(Request $request, Quote $quote)
	{
		$comment = $quote->comments()->create([
			'user_id' => auth()->id(),
			'comment' => $request->comment,
		])->load('user');

		broadcast(new QuoteCommented([
			'comment'  => new CommentResource($comment),
			'quote_id' => $quote->id,
		]));
		if (auth()->id() !== $quote->user_id) {
			$notification = Notification::create(['user_id_from' => auth()->id(), 'user_id_to' => $quote->user_id, 'type' => 'comment', 'quote_id' => $quote->id]);
			broadcast(new QuoteCommentNotification(new NotificationResource($notification)));
		}
	}

	public function destroy(Quote $quote)
	{
		$quote->delete();
	}
}
