<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Request;
use \App\Http\Controllers\UIController;

class SetLocale {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next) {

		$locale = 'th';

		if ($request->session()->has('locale')) {
			$locale = $request->session()->get('locale', 'th');
		/*} elseif (!empty($request->cookie('locale'))) {
			$locale = $request->cookie('locale');*/
		} elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && str_contains($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'th-TH')) {
			$locale = 'th';
		}

		UIController::setLocale($locale);

		return $next($request);
	}
}
