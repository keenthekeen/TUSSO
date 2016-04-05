<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class Client {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		/*
		| This middleware check if request come from trusted client or administrator.
		|
		| @input [String]client_key
		*/
		if (!empty($request->input('client_key'))) {
			if ($clientinfo = DB::table('client')->where('key', $request->input('client_key'))->first()) {
				$request->session()->put('client', $clientinfo->id);
			}
		}

		if (!$request->session()->has('client') && !$request->session()->get('useradmin', FALSE)) {
			return abort(403);
			//return view('errors.custom', ['title' => 'Access Denied','subtitle' => 'This client is not registered','description' => 'เครื่องลูกข่ายนี้ไม่ได้รับอนุญาตให้เข้าถึงระบบ']);
		}

		return $next($request);
	}
}
