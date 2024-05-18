<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Movie extends Model implements HasMedia
{
	use HasFactory,HasTranslations,InteractsWithMedia;

	protected $guarded = ['id'];

	public $translatable = ['name', 'director', 'description'];

	public function quotes()
	{
		return $this->hasMany(Quote::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function categories()
	{
		return $this->belongsToMany(Category::class);
	}

	public function getImage()
	{
		return $this->getFirstMedia('movie_images')->getUrl();
	}

	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('movie_images')
			 ->singleFile();
	}
}
