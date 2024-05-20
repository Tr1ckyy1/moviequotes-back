<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieIndexResource extends JsonResource
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
            'name'   => [
                'en' => $this->getTranslation('name', 'en'),
                'ka' => $this->getTranslation('name', 'ka'),
            ],
			'year'                => $this->year,
			'quotes'              => $this->quotes->count(),
            'image' => $this->getImage()
		];
	}
}
