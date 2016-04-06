<?php

namespace App\Http\Controllers;

use App;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UIController extends Controller {
	public function switchLanguage(Request $request) {
		$setlang = 'en';
		if (App::isLocale('en')) {
			$setlang = 'th';
		}

		$request->session()->put('locale', $setlang);

		//return $setlang;
		return back()->with('notify', 'Language changed to '.strtoupper($setlang).'!');

	}

	public function setLocale($loc) {
		App::setLocale($loc);
	}

	public function isLoggedIn() {
		return Auth::check();
	}

	public function home() {
		if ($this->isLoggedIn()) {
			return view('home');
		} else {
			return view('login');
		}
	}

	public function logout() {
		Auth::logout();
		return redirect('/')->with('notify', trans('messages.loggedout'));
	}

	public function debugSession (Request $request) {
		dump(session()->all());
		return dump($request->user());
	}

}