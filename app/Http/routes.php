<?php
/*
|==========================================================================
| Routes File
|==========================================================================
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

if (config('tusso.shutdown')) {
	Route::any('{catchall}', function ($page) {
		return view('shutdown');
	})->where('catchall', '(.*)');
} else {

	Route::group(['middleware' => ['web']], function () { //Apply session, CSRF protection, and cookies encryption.


		Route::get('/', 'UIController@home');
		Route::get('switch_lang', 'UIController@switchLanguage');
		Route::post('login', 'TUSSOController@TryLogIn');
		Route::get('login', function () {
			return view('login');
		});
		Route::get('logout', 'UIController@logout');

		//DEBUGGING PURPOSE
		if (config('app.debug')) {
			Route::get('/view/{id}', function ($id) {
				if (!empty($id) && view()->exists($id)) {
					return view($id);
				} else {
					return abort(404);
				}
			});
			Route::get('session', 'UIController@debugSession');
		}

	});

	Route::group(['middleware' => ['web', 'auth']], function () {

		Route::get('account', function () {
			return view('home');
		});

	});
}