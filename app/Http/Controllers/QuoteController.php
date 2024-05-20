<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\StoreQuoteUpdateRequest;
use App\Http\Resources\QuoteResource;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
	public function index()
	{
		return QuoteResource::collection(Quote::with('user', 'likes', 'movie', 'comments', 'comments.user')->latest()->paginate(10));
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
		$like = $quote->likes()->where('user_id', auth()->id())->first();

		if ($like) {
			$like->delete();
		} else {
			$quote->likes()->create([
				'user_id' => auth()->id(),
			]);
		}
	}

	public function addComment(Request $request, Quote $quote)
	{
		$quote->comments()->create([
			'user_id' => auth()->id(),
			'comment' => $request->comment,
		]);
	}

	public function destroy(Quote $quote)
	{
		$quote->delete();
	}
}
