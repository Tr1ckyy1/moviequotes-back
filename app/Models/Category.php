<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
	use HasFactory,HasTranslations;

	protected $guarded = ['id'];

	public $translatable = ['name'];

	public function movies()
	{
		return $this->belongsToMany(Movie::class);
	}
}
