<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Log;
use Auth;

class TUSSOController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| The TUSSO I/O Handler
	|--------------------------------------------------------------------------
	*/

	/*
	| TryLogIn()
	|
	| a method called when user submitted the login form
	|
	| @CallMethod Route
	| @Input [String]username, [String]password
	| @Output Redirection
	|
	*/
	public function TryLogIn(Request $request) {
		//$this->validate($request, ['username' => 'required', 'password' => 'string']);

		if (Auth::attempt(['username' => $request->input('username'), 'password' => $request->input('password')], $request->has('remember'))) {
			//$request->session()->put('userid', Auth::user()->username);
			//$request->session()->put('name', Auth::user()->name);

			return redirect('/')->with('notify', trans('messages.loginsuccess'));
		} else {
			//return 'BAD';
			return redirect('/')->with('notify', trans('messages.loginfail'));
		}
		/*$search = Adldap::getProvider('default')->search()->where('userprincipalname', '=', $request->input('username'))->get();
		$login = Adldap::getProvider('default')->auth()->attempt($request->input('username'), $request->input('password'));*/
	}

	/*
	 * proxyAuth()
	 *
	 * a method called from reverse proxy server to check whether user is authenticated
	 *
	 * @return HTTP Status 200 with X-Username header or 403
	 */
	public function proxyAuth(Request $request) {
		if (Auth::check()) {
			// @todo Send back OAuth token, not real username
			return response('AUTHENTICATED', 200)->header('X-Username', $request->user()->username);
		} else {
			return response('UNAUTHORIZED', 403)->header('X-Username', '');
		}
	}
	

}
