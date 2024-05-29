<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as GeorgianFactory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'name' => ['en' => $this->faker->text, 'ka' => GeorgianFactory::create('ka_GE')->realText(10),
			],
			'year'        => $this->faker->numberBetween(1900, now()->year),
			'director'    => ['en' => $this->faker->name, 'ka' => GeorgianFactory::create('ka_GE')->name()],
			'description' => ['en' => $this->faker->paragraph, 'ka' =>GeorgianFactory::create('ka_GE')->realText(50)],
			'user_id'     => User::factory(),
		];
	}

	public function withImage()
	{
		return $this->afterCreating(function (Movie $movie) {
			$movie->addMedia(UploadedFile::fake()->image('movie.jpg'))
				  ->toMediaCollection('movie_images');
		});
	}
}
