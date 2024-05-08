<?php

use App\Http\Middleware\Localization;
use App\Http\Middleware\RestrictAuthorizedUsers;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		api: __DIR__ . '/../routes/api.php',
		commands: __DIR__ . '/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(function (Middleware $middleware) {
		$middleware->statefulApi();
		$middleware->append(Localization::class);
		$middleware->alias([
			'guest' => RestrictAuthorizedUsers::class,
		]);
	})
	->withExceptions(function (Exceptions $exceptions) {
		$exceptions->render(function (InvalidSignatureException $e) {
			return response()->json(['expired' => true], 400);
		});
	})->create();
