<?php

namespace App\Http\Controllers;

use App;
use Auth;
use Illuminate\Http\Request;

class UIController extends Controller {
	public function switchLanguage(Request $request) {
		$setlang = 'en';
		if (App::isLocale('en')) {
			$setlang = 'th';
		}

		self::setLanguage($request, $setlang);

		//return $setlang;
		return back()->with('notify', 'Language changed to ' . strtoupper($setlang) . '!');

	}

	private static function setLanguage(Request $request, $lang) {
		$request->session()->put('locale', $lang);
		self::setLocale($lang);
	}

	public static function setLocale($loc) {
		App::setLocale($loc);
	}

	public function isLoggedIn() {
		return Auth::check();
	}

	public function home(Request $request) {
		if ($this->isLoggedIn()) {
			if ($request->has('id') && config('unifi.switch')) {
				// Unifi
				$request->session()->put('mac', $request->input('id'));
				$request->session()->put('redirect-url', $request->input('url', config('unifi.default_url')));

				return (new WifiCoordinator)->unifiAuthorize($request);
			} else {
				return view('home');
			}
		} else {
			if ($request->has('id') && config('unifi.switch')) {
				// Unifi
				return (new WifiCoordinator)->unifiInitialize($request);
			} else {
				return view('login');
			}
		}
	}

	public function logout(Request $request) {
		Auth::logout();
		$locale = $request->session()->get('locale', 'th');
		$request->session()->flush();
		$request->session()->put('locale', $locale);

		//return redirect('/')->with('notify', trans('messages.loggedout'));
		return view('loggedout');
	}

	public function displayNewRegister(Request $request) {
        self::setLanguage($request, 'th');

		return view('newstudent');
	}

	public function debugSession(Request $request) {
		dump(session()->all());
		dump($request->user());
	}

	public function debugRequest(Request $request) {
		return response()->json($request->all());
	}

	public static function isGraylog () {
		return env('GRAYLOG2_ENABLE', 'false') == true;
	}

}