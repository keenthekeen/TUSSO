<?php

namespace App\Providers;

use App\Http\Controllers\UIController;
use Illuminate\Support\ServiceProvider;
use Log;
use Monolog\Handler\GelfHandler;

class AppServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		if (UIController::isGraylog()) {
			// Monolog setup
			$monolog = Log::getMonolog();
			$gelf = new GelfHandler(new \Gelf\Publisher(new \Gelf\Transport\HttpTransport(env('GRAYLOG2_SERVER'), env('GRAYLOG2_PORT'), '/gelf')));
			$gelf->setFormatter(new \Monolog\Formatter\GelfMessageFormatter('TUSSO'));
			$monolog->pushHandler($gelf);
		}
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
}
