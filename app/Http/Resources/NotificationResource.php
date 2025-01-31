<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id'                     => $this->id,
			'quote_id'               => $this->quote_id,
			'type'                   => $this->type,
			'read_at'                => $this->read_at,
			'created_at'             => $this->created_at,
			'user_from'              => UserResource::make($this->userFrom),
		];
	}
}
