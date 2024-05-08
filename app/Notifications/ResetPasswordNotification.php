<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
	use Queueable;

	protected $url;

	/**
	 * Create a new notification instance.
	 */
	public function __construct($url)
	{
		$this->url = $url;
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

	/**
	 * Get the mail representation of the notification.
	 */
	public function toMail(object $notifiable): MailMessage
	{
		return (new MailMessage)->view('auth.verify-email', ['url' => $this->url, 'user' => $notifiable->username, 'text' => __('email-verification.password_reset_text'), 'linkText' => __('email-verification.password_reset_link_text')])->subject(__('email-verification.password_reset_subject_text'))->from(env('MAIL_FROM_ADDRESS'), 'no-reply@moviequotes.ge');
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(object $notifiable): array
	{
		return [
		];
	}
}
