<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 */
	public function __construct()
	{
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @return array<int, string>
	 */
	public function via(object $notifiable): array
	{
		return ['mail'];
	}

	protected function verificationUrl($notifiable)
	{
		$expiration = now()->addMinutes(config('auth.verification.expire', 60));
		$signedUrl = URL::temporarySignedRoute(
			'verification.verify',
			$expiration,
			['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
		);

		$urlParts = parse_url($signedUrl);
		$query = isset($urlParts['query']) ? $urlParts['query'] : '';
		parse_str($query, $queryParams);
		$expires = $queryParams['expires'] ?? null;
		$signature = $queryParams['signature'] ?? null;

		return config('app.frontend_url') . '?' . http_build_query([
			'id'        => $notifiable->getKey(),
			'hash'      => sha1($notifiable->getEmailForVerification()),
			'expires'   => $expires,
			'signature' => $signature,
		]);
	}

	public function toMail($notifiable)
	{
		return (new MailMessage)
			->view('auth.verify-email', [
				'url'      => $this->verificationUrl($notifiable),
				'user'     => $notifiable->username,
				'text'     => __('email-verification.text_verify'),
				'linkText' => __('email-verification.link_text_verify'),
			])
			->subject(__('email-verification.subject_text'))
			->from(env('MAIL_FROM_ADDRESS'), 'no-reply@moviequotes.ge');
	}
}
