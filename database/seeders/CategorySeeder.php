<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$categories = [
			'Adventure'       => 'სათავგადასავლო',
			'Animation'       => 'ანიმაცია',
			'Biography'       => 'ბიოგრაფიული',
			'Comedy'          => 'კომედია',
			'Detective'       => 'დეტექტივი',
			'Drama'           => 'დრამა',
			'Documentary'     => 'დოკუმენტური',
			'Family'          => 'საოჯახო',
			'Fantasy'         => 'ფანტასტიკა',
			'History'         => 'ისტორიული',
			'Horror'          => 'საშინელება',
			'Music'           => 'მიუზიკლი',
			'Mystery'         => 'მისტიკა',
			'Romance'         => 'რომანტიკა',
			'Science Fiction' => 'სამეცნიერო ფანტასტიკა',
			'Sport'           => 'სპორტი',
			'Thriller'        => 'თრილერი',
			'Travel'          => 'მოგზაურობა',
		];

		foreach ($categories as $name => $translation) {
			Category::create([
				'name' => [
					'en' => $name,
					'ka' => $translation,
				],
			]);
		}
	}
}
