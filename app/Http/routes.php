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
		
		Route::get('openid/logout', 'ProviderController@remoteLogout');
		
		if (config('tusso.use_tuent')) {
			Route::get('newstudent_register', 'UIController@displayNewRegister');
			Route::group(['middleware' => 'throttle:15,3'], function () {
				Route::post('newstudent_register', 'TUSSOController@newStudentRegister');
			});
		}
		
		Route::any('openid/authorize', 'ProviderController@AuthRequest');
	});
	
	Route::group(['middleware' => ['web', 'auth']], function () {
		Route::get('account', function () {
			return view('home');
		});
		
		if (config('tusso.allow_password_change')) {
			// Password change through LDAP requires SSL or TLS, which is not implemented in dc.triamudom.ac.th
			Route::get('password/change', function () {
				abort(501);
				
				return view('changepassword');
			});
			Route::post('password/change', 'TUSSOController@changePassword');
		}
		
		if (config('unifi.switch')) {
			Route::get('unifi/auth', 'WifiCoordinator@unifiAuthorize');
		}
	});
	
	Route::group(['middleware' => ['web', 'auth', 'admin']], function () {
		Route::get('admin', function () {
			return view('admin');
		});
		Route::post('admin/loginas', 'TUSSOController@adminLoginAs');
		Route::get('log', function () {
			return view('viewlog');
		});
	});
	
	Route::group(['middleware' => ['session']], function () {
		Route::any('state_validate', 'TUSSOController@validateSessionState');
		
		Route::get('service/auth/status', 'TUSSOController@proxyAuth');
		Route::get('service/auth/login', 'TUSSOController@proxyGoLogin');
		Route::post('service/auth/login', 'TUSSOController@proxyLogMeIn');
		
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
			Route::any('request', 'UIController@debugRequest');
			Route::get('sendlog', function () {
				Log::debug('Test message from TUSSO');
				echo 'Logged (' . \App\Http\Controllers\UIController::isGraylog() . ')';
			});
			Route::get('ip', function (\Illuminate\Http\Request $request) {
				return response(\App\Http\Controllers\TUSSOController::getIPAddress($request));
			});
		}
	});
	
	Route::group(['middleware' => ['api']], function () {
		Route::get('.well-known/openid-configuration', 'ProviderController@publishConfig');
		
		Route::any('access/challenge', 'ProviderController@getChallenge');
		Route::post('access/token', 'ProviderController@verifyResponse');
		
		Route::post('openid/token', 'ProviderController@tokenRequest');
	});
	
	Route::group(['middleware' => ['web', 'api', 'auth.api:true']], function () {
		Route::get('api/search', 'TUSSOController@apiSearch');
	});
	
}