<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('quotes', function () {
	return true;
});

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
	return $user->id === $userId;
});
