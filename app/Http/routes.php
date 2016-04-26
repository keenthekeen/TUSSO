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

// This app must use TLS to prevent vulnerability
if (config('tusso.shutdown')) {
	Route::any('{catchall}', function () {
		// Return 503 Service Unavailable
		return response(view('shutdown'), '503');
	})->where('catchall', '(.*)');
} else {

	Route::group(['middleware' => ['web']], function () { //Apply session, CSRF protection, and cookies encryption.

		Route::get('/', 'UIController@home');
		Route::get('switch_lang', 'UIController@switchLanguage');

		Route::group(['middleware' => 'throttle:15,5'], function () {
			//Prevents brute-force attack
			Route::post('login', 'TUSSOController@TryLogIn');
		});

		Route::get('login', function () {
			return view('login');
		});
		Route::get('logout', 'UIController@logout');

		Route::get('api/status', 'TUSSOController@proxyAuth');
		Route::get('logout/remote', 'ProviderController@RemoteLogout');

		//DEBUGGING PURPOSE
		if (config('app.debug')) {
			Route::get('/view/{id}', function ($id) {
				if (!empty($id) && view()->exists($id)) {
					return view($id);
				} else {
					abort(404);

					return 'Not Found';
				}
			});
			Route::get('session', 'UIController@debugSession');
		}

		Route::any('openid/authorize', 'ProviderController@AuthRequest');

	});

	Route::group(['middleware' => ['web', 'auth']], function () {
		Route::get('account', function () {
			return view('home');
		});
	});

	Route::group(['middleware' => ['api']], function () {
		Route::get('.well-known/openid-configuration', 'ProviderController@publishConfig');

		Route::any('access/challenge', 'ProviderController@getChallenge');
		Route::post('access/token', 'ProviderController@verifyResponse');

		Route::get('openid/token', 'ProviderController@tokenRequest');
	});

	Route::group(['middleware' => ['web', 'api', 'auth.api:true']], function () {
		Route::get('api/search', 'TUSSOController@apiSearch');
	});

}