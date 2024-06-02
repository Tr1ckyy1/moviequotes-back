<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
	use HasFactory;

	use Notifiable;

	use InteractsWithMedia;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $guarded = ['id'];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'email_verified_at' => 'datetime',
			'password'          => 'hashed',
		];
	}

	public function quotes()
	{
		return $this->hasMany(Quote::class);
	}

	public function likes()
	{
		return $this->hasMany(Like::class);
	}

	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	public function movies()
	{
		return $this->hasMany(Movie::class);
	}

	public function notificationsSent()
	{
		return $this->hasMany(Notification::class, 'user_id_from');
	}

	public function notificationsReceived()
	{
		return $this->hasMany(Notification::class, 'user_id_to');
	}

	public function sendPasswordResetNotification($token): void
	{
		$this->notify((new ResetPasswordNotification(config('app.frontend_url') . '?token=' . $token . '&email=' . Crypt::encryptString($this->email)))->locale(app()->getLocale()));
	}

	public function getProfileImageUrl()
	{
		if ($this->getFirstMedia('user_images')) {
			return $this->getFirstMedia('user_images')->getUrl();
		}
		if ($this->profile_image) {
			return $this->profile_image;
		}
		return null;
	}

	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('user_images')
			 ->singleFile();
	}

	public function sendEmailVerificationNotification()
	{
		$this->notify((new VerifyEmailNotification())->locale(app()->getLocale()));
	}
}
