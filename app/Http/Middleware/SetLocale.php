<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Request;

class SetLocale {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next) {

		$locale = 'en';

		if ($request->session()->has('locale')) {
			$locale = $request->session()->get('locale', 'th');
		/*} elseif (!empty($request->cookie('locale'))) {
			$locale = $request->cookie('locale');*/
		} elseif (str_contains($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'th-TH')) {
			$locale = 'th';
		}

		$uic = new \App\Http\Controllers\UIController();
		$uic->setLocale($locale);

		return $next($request);
	}
}
