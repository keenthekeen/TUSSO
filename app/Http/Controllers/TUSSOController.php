<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Log;
use Auth;
use Validator;

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
			if (Auth::attempt(['username' => $request->input('username'), 'password' => $request->input('password')],
				$request->has('remember'))
			) {
				if ($this->cleanUserInfo()) {

					if ($request->session()->has('simple_auth_queue')) {
						return redirect('simple_auth?application=' . $request->session()->get('simple_auth_queue'))->with('notify',
							trans('messages.loginsuccess'));
					} else if ($request->session()->has('redirect_queue')) {
						return redirect($request->session()->get('redirect_queue'));
					} else {
						return redirect('/')->with('notify', trans('messages.loginsuccess'));
					}
				} else {
					// User not registered as staff nor student, suspected as guest, denying access.
					Auth::logout();

					return redirect('/')->with('notify', trans('messages.userdenied'));
				}
			} else {
				return redirect('/')->with('notify', trans('messages.loginfail'));
			}
		} catch (\Exception $e) {
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
		if (str_contains($user->group, 'Staffs')) {
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
	public function proxyAuth(Request $request) {
		if (Auth::check()) {
			return response('AUTHENTICATED', 200)->header('X-AccessRight', 'GRANTED');
		} else {
			return response('UNAUTHORIZED', 403)->header('X-AccessRight', '');
		}
	}

	/*
	 * authUserData()
	 *
	 * a route handler OAuth application called asking for user's information.
	 */
	public function authUserInfo() {
		$userid = Authorizer::getResourceOwnerId();
		$user = \App\User::find($userid);

		return response()->json(array(
			'id' => $user->username,
			'name' => $user->name,
			'type' => $user->type,
			'group' => $user->group
		));
	}

	/*
	 * simpleAuth()
	 *
	 * forward user to application with encrypted user's info in JSON
	 * @input [GET] application (application id)
	 */
	public function simpleAuth(Request $request) {
		$validator = Validator::make($request->all(), [
			'application' => 'required|max:255',
		]);
		if ($validator->fails()) {
			return redirect('/')->with('notify', trans('messages.simple_auth_input'));
		}

		if (Auth::check()) {
			$request->session()->forget('simple_auth_queue');
			$user = $request->user();
			$goto = DB::table('oauth_client_endpoints')->where('client_id',
				$request->input('application'))->first()->redirect_uri;
			$encKey = DB::table('oauth_clients')->where('id', $request->input('application'))->first()->secret;

			$userdata = json_encode(array(
				'id' => $user->username,
				'name' => $user->name,
				'type' => $user->type,
				'group' => $user->group,
				'timestamp' => time(),
				'random' => rand(1,99)
			));
			$serialized = openssl_encrypt($userdata, 'AES128', $encKey, 0, config('tusso.aes_ivfactor'));

			return view('auth-forward', ['goto' => $goto, 'data' => [ 'userinfo' => $serialized]]);
		} else {
			$request->session()->put('simple_auth_queue', $request->input('application'));

			return redirect('/login')->with('notify', trans('messages.pleaselogin'));
		}
	}


}
