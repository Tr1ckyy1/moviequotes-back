<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	use HasFactory;

	protected $guarded = ['id'];

	public function userFrom()
	{
		return $this->belongsTo(User::class, 'user_id_from');
	}

	public function userTo()
	{
		return $this->belongsTo(User::class, 'user_id_to');
	}
}
