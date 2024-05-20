<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Quote extends Model implements HasMedia
{
	use HasFactory,HasTranslations,InteractsWithMedia;

	protected $guarded = ['id'];

	public $translatable = ['quote'];

	public function movie()
	{
		return $this->belongsTo(Movie::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function likes()
	{
		return $this->hasMany(Like::class);
	}

	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	public function getImage()
	{
		return $this->getFirstMedia('quote_images')->getUrl();
	}

	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('quote_images')
			 ->singleFile();
	}
}
