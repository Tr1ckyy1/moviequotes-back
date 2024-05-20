<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\StoreMovieUpdateRequest;
use App\Http\Resources\MovieIndexResource;
use App\Http\Resources\MovieShowResource;
use App\Models\Movie;

class MovieController extends Controller
{
	public function index()
	{
		return MovieIndexResource::collection(Movie::latest()->where('user_id', auth()->id())->with('quotes')->get());
	}

	public function show(Movie $movie)
	{
		if ($movie->user_id !== auth()->id()) {
			return response()->json(null, 403);
		}

		return new MovieShowResource($movie->load('quotes', 'categories', 'quotes.likes', 'quotes.comments'));
	}

	public function store(StoreMovieRequest $request)
	{
		if (!$request->has('categories')) {
			return response()->json(['errors' => ['categories' => __('validation.movie.categories')]], 422);
		}

		$movie = Movie::create([...$request->validated(), 'user_id' => auth()->id()]);

		if ($request->hasFile('image')) {
			$movie->addMediaFromRequest('image')->toMediaCollection('movie_images');
		}

		if ($request->has('categories') && is_array($request->categories)) {
			$movie->categories()->attach($request->categories);
		}

		$movie->save();
	}

	public function update(StoreMovieUpdateRequest $request, Movie $movie)
	{
		if ($movie->user_id !== auth()->id()) {
			return response()->json('Unauthorized', 403);
		}

		if (!$request->has('categories')) {
			return response()->json(['errors' => ['categories' => __('validation.movie.categories')]], 422);
		}

		$movie->update($request->validated());

		if ($request->hasFile('image')) {
			$movie->clearMediaCollection('movie_images');
			$movie->addMediaFromRequest('image')->toMediaCollection('movie_images');
		}

		if ($request->has('categories') && is_array($request->categories)) {
			$movie->categories()->sync($request->categories);
		}

		$movie->save();
	}

	public function destroy(Movie $movie)
	{
		$movie->delete();
	}
}
