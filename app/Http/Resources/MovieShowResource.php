<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieShowResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id'                  => $this->id,
			'description'         => [
				'en' => $this->getTranslation('description', 'en'),
				'ka' => $this->getTranslation('description', 'ka'),
			],
			'director'         => [
				'en' => $this->getTranslation('director', 'en'),
				'ka' => $this->getTranslation('director', 'ka'),
			],
			'name'                => [
				'en' => $this->getTranslation('name', 'en'),
				'ka' => $this->getTranslation('name', 'ka'),
			],
			'year'                => $this->year,
			'quotes'              => $this->quotes->count(),
			'image'               => $this->getImage(),
			'categories'          => $this->categories,
		];
	}
}
