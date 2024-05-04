<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 */
	public function boot()
	{
		VerifyEmail::toMailUsing(function ($notifiable) {
			$expiration = now()->addMinutes(config('auth.verification.expire'));
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

			$frontendUrl = config('app.frontend_url') . '?' . http_build_query([
				'id'        => $notifiable->getKey(),
				'hash'      => sha1($notifiable->getEmailForVerification()),
				'expires'   => $expires,
				'signature' => $signature,
				'email'     => $notifiable->email,
			]);

			return (new MailMessage)->view('auth.verify-email', ['url' => $frontendUrl, 'user' => $notifiable->username, 'text' => __('email-verification.text_verify'), 'linkText' => __('email-verification.link_text_verify')])->subject(__('email-verification.subject_text'))->from(env('MAIL_FROM_ADDRESS'), 'no-reply@moviequotes.ge');
		});

		Model::preventLazyLoading();
	}
}
