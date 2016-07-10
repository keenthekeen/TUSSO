<?php

namespace App\Http\Controllers;

use Adldap\Laravel\AdldapAuthUserProvider;
use App\User;
use DB;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Request;
use Hash;
use Log;
use Auth;
use Validator;

class TUSSOController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| TUSSO Controller
	|--------------------------------------------------------------------------
	| a HTTP controller that handle internal R/W operations
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

		if (config('tusso.use_ldap') && !(config('tusso.use_tuent') && strlen($request->input('username')) == 14 && (substr($request->input('username'), 0,
						1) == 'n' || substr($request->input('username'), 0, 1) == 'N') && is_numeric(substr($request->input('username'), 1, 13)))
		) {
			try {
				\Adldap::connect('default');
				if (Auth::attempt([
					'username' => $request->input('username'),
					'password' => $request->input('password')
				], $request->has('remember'))
				) {
					if ($this->cleanUserInfo()) {
						Log::info(Auth::user()->username . ' logged in from ' . $this->getIPAddress($request));
					} else {
						// User has problem with his data, deny access and tell him to contact administrator
						Log::warning(Auth::user()->username . ' tried to log in from ' . $this->getIPAddress($request) . ' but has some problem, denied access.');
						Auth::logout();

						if ($request->ajax()) {
							return response()->json(['error' => trans('messages.userdenied')]);
						} else {
							return view('auth-error', ['error' => trans('messages.userdenied')]);
						}
					}
				} else {
					return $this->returnLoginError($request, trans('messages.loginfail'));
				}
			} catch (\Exception $e) {
				if (!$this->manualLogin($request->username, $request->password, $request)) {
					Log::error('Authentication failed (probably caused by unreachable directory server)');

					return $this->returnLoginError($request, trans('messages.ldapfail'));
				}
			}
		} else {
			if (!$this->manualLogin($request->username, $request->password, $request)) {
				return $this->returnLoginError($request, trans('messages.ldapofffail'));
			}
		}

		return $this->finishedLogin($request);

	}

	private function finishedLogin(Request $request) {
		$request->session()->put('session_state', sha1('TUSSOSessionState:' . $request->user()->username . '-' . microtime()));
		$request->session()->put('login_time', time());
		if ($request->has('redirect_queue')) {
			return $this->returnLoginSuccess($request, $request->input('redirect_queue'));
		} elseif ($request->session()->has('redirect_queue')) {
			$redirect = $request->session()->get('redirect_queue');
			$request->session()->forget('redirect_queue');

			return $this->returnLoginSuccess($request, $redirect);
		} else {
			return $this->returnLoginSuccess($request, '/account');
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

	private function returnLoginError(Request $request, $error) {
		if ($request->ajax()) {
			return response()->json(['error' => $error]);
		} else {
			return redirect('/login')->with('error_message', $error)->with('redirect_queue', $request->input('redirect_queue', ''));
		}
	}

	private function returnLoginSuccess(Request $request, $redirect) {
		if ($request->ajax()) {
			return response()->json(['redirect' => $redirect]);
		} else {
			return redirect($redirect)->with('notify', trans('messages.loginsuccess'));
		}
	}

	/*
	 * cleanUserInfo()
	 *
	 * generate user's information from LDAP data.
	 */
	private function cleanUserInfo() {
		$user = Auth::user();

		// Group
		$grn = explode(',', $user->group, 2);
		$user->group = str_replace('CN=', '', $grn[0]);

		// User type
		if (str_contains($user->group, 'Staffs') || $user->group == 'Domain Admins') {
			$user->type = 'staff';
		} elseif ($user->group == 'Students') {
			// If he is student, his username must be in "s00000" format, or else deny access to prevent further problem in other applications.
			if (preg_match("/^s\d\d\d\d\d$/", $user->username)) {
				$user->type = 'student';
			} else {
				return false;
			}
		} else {
			// Neither staff nor student, not allowed to authenticate
			return false;
		}
		
		// @todo Use staff's Thai name, not English.
		
		$user->save();
		
		return true;
	}

	/*
	 * newStudentLogin()
	 *
	 * Try to log user in using new student database (who doesn't have an account in directory server)
	 * This function expects "newstudent" table, containing fname, lname, nationalid, room
	 *
	 * // OBSOLETE Try to log user in using TUENT's database (for new student who doesn't have an account in directory)
	 * // OBSOLETE This function expects "tuent_applicant" table containing fname,lname,nationalid,plan_id
	 */
	public function newStudentRegister(Request $request) {
		$validator = Validator::make($request->all(), [
			'fname' => 'required|max:50',
			'lname' => 'required|max:50',
			'citizenid' => 'required|digits:13',
			//'plan' => 'required|digits_between:1,8',
			'room' => 'required'
			//'password' => 'confirmed'
		]);

		if ($validator->fails()) {
			return response()->json(['status' => 'MALFORMED_REQUEST']);
		}

		if ($applicant = DB::table('newstudent')->where('nationalid', $request->input('citizenid'))->first()) {
			if ($user = User::find('n' . $applicant->nationalid)) {
				return response()->json(['status' => 'USER_EXISTS']);
			} elseif ($request->fname != $applicant->fname || $request->lname != $applicant->lname || $request->room != $applicant->room) {
				return response()->json(['status' => 'INVALID_INFO']);
			}
		} else {
			return response()->json(['status' => 'USER_NOT_EXIST']);
		}

		if ($request->has('password')) {
			$validator = Validator::make($request->all(), [
				'password' => 'confirmed'
			]);

			if ($validator->fails()) {
				return response()->json(['status' => 'MALFORMED_REQUEST']);
			}
			//Step2
			$user = new User();
			$user->username = 'n' . $applicant->nationalid;
			$user->name = $applicant->fname . ' ' . $applicant->lname;
			$user->type = 'student';
			$user->group = 'New Student (Temporary)';
			$user->password = Hash::make($request->input('password'));
			if ($user->save()) {
				return response()->json(['status' => 'SUCCEED']);
			} else {
				return response()->json(['status' => 'FAIL']);
			}
		} else {
			//Step1
			return response()->json(['status' => 'GOOD']);
		}
		
	}
	
	public function changePassword (Request $request) {
		$validator = Validator::make($request->all(), [
			'oldpassword' => 'required',
			'password' => 'required|confirmed'
		]);
		if ($validator->fails()) {
			return response('MALFORMED_REQUEST');
		}

		$user = $request->user();
		if (Hash::check($request->oldpassword, $user->password)) {
			// Old password match...

			/*$user->fill([
				'password' => Hash::make($request->password)
			])->save();*/

			/*$aduser = (new AdldapAuthUserProvider(new BcryptHasher(), $user))->retrieveById($user->username);
			$aduser->setPassword('abc123')->save();*/

			return 'WORK_IN_PROGRESS_503';
		} else {
			return response('PASSWORD_NOT_MATCH');
		}
	}

	public function adminLoginAs (Request $request) {
		$oldid = $request->user()->username;
		if (Auth::loginUsingId($request->input('user'))) {
			Log::notice($oldid . ' logged in as '.$request->user()->username . ' from ' . $this->getIPAddress($request));

			return redirect('/account')->with('notify', 'Logged in!');
		} else {
			return redirect('/account')->with('notify', 'Cannot log in!');
		}
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
			if ($quser = User::where('name', 'LIKE', '%' . $request->keyword . '%')->orWhere('username', 'LIKE', '%' . $request->keyword . '%')->first()) {
				return response()->json([
					'name' => $quser->name,
					'id' => $quser->username
				])->header('Access-Control-Allow-Origin', '*');
			} else {
				return response()->json(['error' => 'NAME_NOT_FOUND'])->header('Access-Control-Allow-Origin', '*');
			}
		} elseif ($request->has('id')) {
			if ($quser = User::find($request->id)) {
				return response()->json([
					'name' => $quser->name,
					'id' => $quser->username
				])->header('Access-Control-Allow-Origin', '*');
			} else {
				return response()->json(['error' => 'ID_NOT_FOUND'])->header('Access-Control-Allow-Origin', '*');
			}
		} else {
			return response()->json(['error' => 'EMPTY_REQUEST'])->header('Access-Control-Allow-Origin', '*');
		}
	}

	public function getIPAddress(Request $request) {
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			// Cloudflare
			$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// Proxy server, including Nginx.
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $request->ip();
		}

		return $ip;
	}
	
}
