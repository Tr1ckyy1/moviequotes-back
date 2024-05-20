<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteMovieResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id'                   => $this->id,
			'quote'                => [
				'en' => $this->getTranslation('quote', 'en'),
				'ka' => $this->getTranslation('quote', 'ka'),
			],
			'image'                => $this->getImage(),
			'comments'             => $this->comments,
			'likes'                => $this->likes,
		];
	}
}
