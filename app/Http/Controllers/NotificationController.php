<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;

class NotificationController extends Controller
{
	public function index()
	{
		$notifications = Notification::where('user_id_to', auth()->id())
		->with('userFrom')
		->latest()
		->paginate(10);

		$notificationsResource = NotificationResource::collection($notifications);

		$paginationMeta = [
			'total'        => $notifications->total(),
			'per_page'     => $notifications->perPage(),
			'current_page' => $notifications->currentPage(),
			'last_page'    => $notifications->lastPage(),
			'from'         => $notifications->firstItem(),
			'to'           => $notifications->lastItem(),
		];

		return response()->json([
			'data'         => $notificationsResource,
			'meta'         => $paginationMeta,
			'unread_total' => Notification::where('user_id_to', auth()->id())
				->whereNull('read_at')
				->count(),
		]);
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
