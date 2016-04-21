<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Hash;
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
		$this->validate($request, ['username' => 'required', 'password' => 'required']);
		
		try {
			\Adldap::connect('default');
			if (Auth::attempt(['username' => $request->input('username'), 'password' => $request->input('password')],
				$request->has('remember'))
			) {
				if ($this->cleanUserInfo()) {
					Log::info(Auth::user()->username . ' logged in from ' . $request->ip());

					if ($request->has('redirect_queue')) {
						return redirect($request->input('redirect_queue'));
					} elseif ($request->session()->has('redirect_queue')) {
						$redirect = $request->session()->get('redirect_queue');
						$request->session()->forget('redirect_queue');
						return redirect($redirect);
					} else {
						return redirect('/')->with('notify', trans('messages.loginsuccess'));
					}
				} else {
					// User not registered as staff nor student, suspected as guest, denying access.
					Auth::logout();
					Log::notice(Auth::user()->username . ' tried to log in from ' . $request->ip() . ' but cannot determine user type');
					
					return redirect('/')->with('notify', trans('messages.userdenied'));
				}
			} else {
				return redirect('/')->with('notify', trans('messages.loginfail'));
			}
		} catch (\Exception $e) {
			if ($user = User::find($request->input('username'))) {
				if (Hash::check($request->input('password'), $user->password)) {
					if (Auth::loginUsingId($user->username)) {
						Log::notice(Auth::user()->username . ' logged in USING LOCAL DB from ' . $request->ip());
						
						if ($request->has('redirect_queue')) {
							return redirect($request->input('redirect_queue'));
						} elseif ($request->session()->has('redirect_queue')) {
							$redirect = $request->session()->get('redirect_queue');
							$request->session()->forget('redirect_queue');
							return redirect($redirect);
						} else {
							return redirect('/')->with('notify', trans('messages.loginsuccess'));
						}
					}
				}
			}
			
			Log::error('Authentication failed (probably caused by unreachable LDAP server)');
			
			return redirect('/')->with('notify', trans('messages.ldapfail'));
		}
	}
	
	/*
	 * cleanUserInfo()
	 *
	 * generate user's information from LDAP data.
	 */
	private function cleanUserInfo() {
		$user = Auth::user();
		
		// User type
		if (str_contains($user->group, 'Staffs') || str_contains($user->group, 'Domain Admins')) {
			$user->type = 'staff';
		} elseif (str_contains($user->group, 'Students')) {
			$user->type = 'student';
		} else {
			// Neither staff nor student, not allowed to authenticate
			return false;
		}
		
		// Group
		$grn = explode(',', $user->group, 2);
		$user->group = str_replace('CN=', '', $grn[0]);
		
		// @todo Use staff's Thai name, not English.
		
		$user->save();
		
		return true;
	}
	
	
	/*
	 * proxyAuth()
	 *
	 * a method called from reverse proxy server to check whether user is authenticated
	 *
	 * @return HTTP Status 200 with X-Username header or 403
	 */
	public function proxyAuth() {
		if (Auth::check()) {
			return response('AUTHENTICATED', 200)->header('X-AccessRight', 'GRANTED');
		} else {
			return response('UNAUTHORIZED', 403)->header('X-AccessRight', '');
		}
	}
	
	
}
