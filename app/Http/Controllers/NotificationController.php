<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;

class NotificationController extends Controller
{
	public function index()
	{
		return NotificationResource::collection(Notification::where('user_id_to', auth()->id())->with('userFrom')->latest()->get());
	}

	public function markNotification(Notification $notification)
	{
		if (!$notification->read_at) {
			$notification->read_at = now();
			$notification->save();
		}
	}

	public function markAllAsRead()
	{
		Notification::where('user_id_to', auth()->id())
			->whereNull('read_at')
			->update(['read_at' => now()]);
	}
}
