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

		Route::get('proxy_auth', 'TUSSOController@proxyAuth');

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

	/*
	 * OAuth Provider Routes
	 */
	// route to respond to the incoming auth code requests
	Route::get('oauth/authorize', ['as' => 'oauth.authorize.get', 'middleware' => ['check-authorization-params', 'auth'], function() {
		$authParams = Authorizer::getAuthCodeRequestParams();

		$formParams = array_except($authParams,'client');

		$formParams['client_id'] = $authParams['client']->getId();

		$formParams['scope'] = implode(config('oauth2.scope_delimiter'), array_map(function ($scope) {
			return $scope->getId();
		}, $authParams['scopes']));

		return view('oauth.authorization-form', ['params' => $formParams, 'client' => $authParams['client']]);
	}]);

	// Set up a route to respond to the form being posted.
	Route::post('oauth/authorize', ['as' => 'oauth.authorize.post', 'middleware' => ['csrf', 'check-authorization-params', 'auth'], function(Request $request) {

		$params = Authorizer::getAuthCodeRequestParams();
		$params['user_id'] = Auth::user()->username;
		$redirectUri = '/';

		// If the user has allowed the client to access its data, redirect back to the client with an auth code.
		if ($request->has('approve')) {
			$redirectUri = Authorizer::issueAuthCode('user', $params['user_id'], $params);
		}

		// If the user has denied the client to access its data, redirect back to the client with an error message.
		if ($request->has('deny')) {
			$redirectUri = Authorizer::authCodeRequestDeniedRedirectUri();
		}

		return redirect($redirectUri);
	}]);

	// Add a route to respond to the access token requests
	Route::post('oauth/access_token', function() {
		return response()->json(Authorizer::issueAccessToken());
	});

	Route::group(['middleware' => ['oauth']], function () {
		Route::get('oauth/userinfo', 'TUSSOController@authUserInfo');
	});

}