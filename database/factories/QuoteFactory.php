<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as GeorgianFactory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'quote'    => ['en' => $this->faker->sentence, 'ka' => GeorgianFactory::create('ka_GE')->realText(40)],
			'user_id'  => User::factory(),
			'movie_id' => Movie::factory(),
		];
	}

	public function withImage()
	{
		return $this->afterCreating(function (Quote $quote) {
			$quote->addMedia(UploadedFile::fake()->image('quote.jpg'))
				  ->toMediaCollection('quote_images');
		});
	}
}
