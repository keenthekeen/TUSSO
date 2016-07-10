<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Admin {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @param  string|null              $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = NULL) {
		if (in_array($request->user()->username, config('tusso.admin'))) {
			return $next($request);
		} else {
			return response('Unauthorized.', 401);
		}
	}
}
