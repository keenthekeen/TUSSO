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

		if (config('tusso.use_ldap')) {
			try {
				\Adldap::connect('default');
				if (Auth::attempt([
					'username' => $request->input('username'),
					'password' => $request->input('password')
				], $request->has('remember'))
				) {
					if ($this->cleanUserInfo()) {
						Log::info(Auth::user()->username . ' logged in from ' . $this->getIPAddress($request));

						return $this->finishedLogin($request);
					} else {
						// User not registered as staff nor student, suspected as guest, denying access.
						Auth::logout();
						Log::notice(Auth::user()->username . ' tried to log in from ' . $this->getIPAddress($request) . ' but cannot determine user type');

						//return redirect('/')->with('notify', trans('messages.userdenied'));
						return view('auth-error', ['error' => trans('messages.userdenied')]);
					}
				} else {
					return redirect('/login')->with('error_message',
						trans('messages.loginfail'))->with('redirect_queue', $request->input('redirect_queue', ''));
				}
			} catch (\Exception $e) {
				if ($this->manualLogin($request->username, $request->password, $request)) {
					return $this->finishedLogin($request);
				} else {

					Log::error('Authentication failed (probably caused by unreachable LDAP server)');

					return redirect('/login')->with('error_message', trans('messages.ldapfail'))->with('redirect_queue',
						$request->input('redirect_queue', ''));
				}
			}
		} else {
			if ($this->manualLogin($request->username, $request->password, $request)) {
				return $this->finishedLogin($request);
			} else {
				return redirect('/login')->with('error_message', trans('messages.ldapofffail'))->with('redirect_queue',
					$request->input('redirect_queue', ''));
			}
		}
	}

	private function finishedLogin(Request $request) {
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

	private function manualLogin($username, $password, Request $request) {
		if ($user = User::find($username)) {
			if (Hash::check($password, $user->password)) {
				if (Auth::loginUsingId($user->username)) {
					Log::notice(Auth::user()->username . ' logged in USING LOCAL DB from ' . $this->getIPAddress($request));

					return true;
				}
			}
		}

		return false;
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
			return response('AUTHENTICATED', 200)->header('X-AccessRight', 'TUSSO_GRANTED')->header('Access-Control-Allow-Origin', '*');
		} else {
			return response('UNAUTHORIZED', 403)->header('X-AccessRight', '')->header('Access-Control-Allow-Origin', '*');
		}
	}

	public function apiSearch(Request $request) {
		if ($request->has('keyword')) {
			if ($quser = User::where('name', 'LIKE', '%' . $request->keyword . '%')->orWhere('username', 'LIKE',
				'%' . $request->keyword . '%')->first()
			) {
				return response()->json(['name' => $quser->name, 'id' => $quser->username])->header('Access-Control-Allow-Origin', '*');
			} else {
				return response()->json(['error' => 'NAME_NOT_FOUND'])->header('Access-Control-Allow-Origin', '*');
			}
		} elseif ($request->has('id')) {
			if ($quser = User::find($request->id)) {
				return response()->json(['name' => $quser->name, 'id' => $quser->username])->header('Access-Control-Allow-Origin', '*');
			} else {
				return response()->json(['error' => 'ID_NOT_FOUND'])->header('Access-Control-Allow-Origin', '*');
			}
		} else {
			return response()->json(['error' => 'EMPTY_REQUEST'])->header('Access-Control-Allow-Origin', '*');
		}
	}

	public function getIPAddress (Request $request) {
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $request->ip();
		}
		return $ip;
	}
	
}
