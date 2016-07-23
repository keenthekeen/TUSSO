<?php

namespace App\Http\Controllers;

use App\Application;
use App\User;
use Illuminate\Http\Request;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Crypt;
use Auth;
use Log;
use Storage;
use Validator;
use Lcobucci\JWT\Builder;
use Illuminate\Contracts\Encryption\DecryptException;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;
use \Lcobucci\JWT\Signer\Key;

class ProviderController extends Controller {
	/*
	 * OpenID Connect 1.0 Provider
	 *
	 * Currently supports authorization code (code) and implicit (id_token) flow
	 *
	 * Referenced from http://openid.net/specs/openid-connect-core-1_0.html
	 * Created by Siwat Techavoranant
	 * Configuration published via OpenID Connect Discovery 1.0 specification
	 *
	 * CAUTION: The code designed to work with TLS, so this service and all applications MUST be available in HTTPS.
	 */
	
	
	/* Step 1: Client prepares an Authentication Request containing the desired request parameters.
	 *
	 * The Authorization Endpoint performs Authentication of the End-User. This is done by sending
	 * the User Agent to the Authorization Server's Authorization Endpoint for Authentication and Authorization,
	 *  using request parameters defined by OAuth 2.0 and additional parameters and parameter values defined by OpenID Connect.
	 *
	 * Communication with the Authorization Endpoint MUST utilize TLS. See Section 16.17 for more information on using TLS.
	 */
	
	
	/* Step 2: Authentication Request
	 *
	 *  An Authentication Request is an OAuth 2.0 Authorization Request that requests that the End-User be authenticated by the Authorization Server.
	 *
	 * Authorization Servers MUST support the use of the HTTP GET and POST methods defined in RFC 2616 [RFC2616] at the Authorization Endpoint.
	 * Clients MAY use the HTTP GET or POST methods to send the Authorization Request to the Authorization Server.
	 * If using the HTTP GET method, the request parameters are serialized using URI Query String Serialization, per Section 13.1.
	 * If using the HTTP POST method, the request parameters are serialized using Form Serialization, per Section 13.2.
	 */
	
	public function AuthRequest(Request $request) {
		// OpenID Connect uses the following OAuth 2.0 request parameters with the Authorization Code Flow:
		$validator = Validator::make($request->all(), [
			/* scope
			 *
			 * REQUIRED. OpenID Connect requests MUST contain the openid scope value.
			 * If the openid scope value is not present, the behavior is entirely unspecified.
			 * Other scope values MAY be present.
			 * Scope values used that are not understood by an implementation SHOULD be ignored.
			 *
			 * scope values are separated by space (" ")
			 */
			'scope' => 'required',
			
			/* response_type
			 *
			 * REQUIRED. OAuth 2.0 Response Type value that determines the authorization
			 * processing flow to be used, including what parameters are returned from the
			 * endpoints used. When using the Authorization Code Flow, this value is code.
			 *
			 # We implement only some of the type.
			 */
			'response_type' => 'required',
			
			/* client_id
			 *
			 * REQUIRED. OAuth 2.0 Client Identifier valid at the Authorization Server.
			 */
			'client_id' => 'required',
			
			/* redirect_uri
			 *
			 * REQUIRED. Redirection URI to which the response will be sent.
			 * This URI MUST exactly match one of the Redirection URI values for the Client
			 * pre-registered at the OpenID Provider, with the matching performed as described
			 * in Section 6.2.1 of [RFC3986] (Simple String Comparison).
			 *
			 * In implicit flow, https is required.
			 */
			'redirect_uri' => 'required',
			
			/* state
			 *
			 * RECOMMENDED. Opaque value used to maintain state between the request and
			 * the callback. Typically, Cross-Site Request Forgery (CSRF, XSRF) mitigation is done
			 * by cryptographically binding the value of this parameter with a browser cookie.
			 */
			'state' => '',
			
			/* response_mode
			 *
			 * OPTIONAL. Informs the Authorization Server of the mechanism to be used
			 * for returning parameters from the Authorization Endpoint.
			 *
			 # Not Implemented
			 */
			
			/* nonce
			 *
			 * OPTIONAL. String value used to associate a Client session with an ID Token,
			 * and to mitigate replay attacks. The value is passed through unmodified from the
			 *  Authentication Request to the ID Token. Sufficient entropy MUST be present in the
			 * nonce values used to prevent attackers from guessing values. For implementation
			 * notes, see Section 15.5.2.
			 *
			 * In implicit flow, this is required.
			 */
			'nonce' => 'required_if:response_type,id_token',
			
			/* prompt
			 *
			 * OPTIONAL. Space delimited, case sensitive list of ASCII string values that specifies
			 * whether the Authorization Server prompts the End-User for reauthentication
			 * and consent.
			 *
			 # We will only implement NULL or "login".
			 */
			'prompt' => '',
			
			/*
			 * The other fields are ignored
			 * including max_age, ui_locales, id_token_hint, login_hint, acr_values.
			 */
		]);
		
		if ($validator->fails()) {
			return view('auth-error', ['error' => 'MALFORMED_AUTHENTICATION_REQUEST']);
		}
		
		if ($app = Application::find($request->input('client_id'))) {
			if ($allowed_uri = explode(',', $app->redirect_uri)) {
				if (!in_array(rtrim($request->input('redirect_uri'), '/'), $allowed_uri)) {
					return view('auth-error', ['error' => 'NOT_ALLOWED_REDIRECT_URI']);
				}
				
				$allowed_scope = explode(' ', $app->scope);
				$requested_scope = explode(' ', $request->input('scope'));
				if (count(array_intersect($allowed_scope, $requested_scope)) < count($requested_scope)) {
					// Some of the requested scope is not allowed.
					return view('auth-error', ['error' => 'NOT_ALLOWED_SCOPE']);
				}
			} else {
				return view('auth-error', ['error' => 'MISCONFIGURED_CLIENT']);
			}
		} else {
			return view('auth-error', ['error' => 'CLIENT_ID_NOT_FOUND']);
		}
		
		/* Step 3: Authorization Server Authenticates End-User
		 *
		 * If the request is valid, the Authorization Server attempts to Authenticate the End-User
		 *  or determines whether the End-User is Authenticated, depending upon the request
		 *  parameter values used.
		 *
		 * The Authorization Server MUST attempt to Authenticate the End-User in the
		 * following cases:
		 * - The End-User is not already Authenticated.
		 * - The Authentication Request contains the prompt parameter with the value login.
		 *   In this case, the Authorization Server MUST reauthenticate the End-User even
		 *   if the End-User is already authenticated.
		 */
		
		if ($request->input('prompt', 'NULL') == 'login') {
			// Client wants us to re-authenticate.
			Auth::logout();
		} elseif ($request->input('prompt', 'NULL') == 'none' && !Auth::check()) {
			// Client wants us not to prompt user to authenticate.
			return redirect($request->input('redirect_uri') . '?error=login_required');
		}
		if (!Auth::check()) {
			// User not authenticated.
			// After logged in, makes the user redirected back here again.
			//$request->session()->put('redirect_queue', $request->fullUrl());
			
			$request->session()->set('redirect_queue', $request->fullUrl());
			
			return redirect('/login')->with('notify', trans('messages.pleaselogin'));
		}
		
		/* Step 4: Authorization Server Obtains End-User Consent/Authorization
		 *
		 * We allowed only trusted client to access our api, so let's skip this.
		 */
		
		/*
		 * Step 5: Successful Authentication Response
		 */
		
		$user = $request->user();
		$data = array();
		$respType = explode(' ', $request->input('response_type', 'NULL'));
		if (in_array('code', $respType)) {
			
			//Create authorization token.
			$token = (new Builder())->setIssuer(config('tusso.url'))// Configures the issuer (iss claim)
			->setId('TUSSO-AUTHCODE-' . substr(sha1($user->username . microtime() . rand()), 0, 10) . rand(10, 99), true)// Configures the id (jti claim), replicating as a header item
			->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
			->setExpiration(time() + 300)// Configures the expiration time of the token (exp claim)
			->set('user', $user->username)->set('client_id', $request->input('client_id'))->set('scope', $requested_scope)->getToken(); // Retrieves the generated token
			
			$data['code'] = $this->encrypt($token);
			$data['state'] = $request->input('state', '');
		}
		if (in_array('id_token', $respType)) {
			if ($request->input('client_id') == 'nginx') {
				// Create JWT Pass token
				$token = $this->issuePassToken($user, $app, $request->input('nonce', ''), $request->session()->get('session_state', ''), $request);
			} else {
				//Create JWT ID token, containing user's basic info, signed with app secret.
				$token = $this->createIDToken($user, $app, $request->input('nonce', ''), $request->session()->get('session_state', ''));
			}
			
			// We send user's info to client in JWT, which is not encrypted, so using of TLS is highly recommended and avoid sensitive data being sent.
			$data['id_token'] = $token;
			$data['state'] = $request->input('state', '');
			$data['expires_in'] = 3600;
		} elseif (empty($data)) {
			return view('auth-error', ['error' => 'NOT_IMPLEMENTED_RESPONSE_TYPE']);
		}
		Log::debug($user->username . ' logging into ' . $request->input('client_id'));
		
		if ($request->has('kiosk')) {
			// Kiosk mode: Don't remember user in session, just authenticate user to an app.
			Auth::logout();
			$request->session()->flush();
		}
		
		return view('auth-forward', ['goto' => $request->input('redirect_uri'), 'data' => $data]);
	}
	
	
	/*
	 * Section 3.1.3.
	 * Token Endpoint
	 */
	public function tokenRequest(Request $request) {
		$validator = Validator::make($request->all(), [
			'grant_type' => 'required|in:authorization_code',
			'code' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error' => 'invalid_request', 'error_description' => 'Request malformed'], 400);
		}
		
		if ($clientCredential = $this->getClientCredential($request)) {
			if ($client = Application::find($clientCredential['id'])) {
				if ($client->secret != $clientCredential['secret']) {
					return response()->json([
						'error' => 'invalid_client',
						'error_description' => 'Invalid client credential'
					], 400);
					/*} elseif ($allowed_uri = explode(',', $client->redirect_uri)) {
						if (!in_array(rtrim($request->input('redirect_uri'), '/'), $allowed_uri)) {
							return response()->json([
								'error' => 'invalid_client',
								'error_description' => 'Invalid redirect uri'
							], 400);
						}
				} else {
					return response()->json(['error' => 'invalid_client', 'error_description' => 'Misconfigured client'], 400);*/
				}
			} else {
				return response()->json(['error' => 'invalid_client', 'error_description' => 'Client not found'], 400);
			}
		} else {
			return response()->json([
				'error' => 'invalid_client',
				'error_description' => 'No client credential found'
			], 401);
		}
		
		// Parse & validate JWT
		if (!$dec = $this->decrypt($request->input('code'))) {
			return response()->json(['error' => 'invalid_client', 'error_description' => 'Invalid authorization code'], 400);
		}
		$token = (new Parser())->parse((string)$dec);
		$vdata = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
		$vdata->setIssuer(config('tusso.url'));
		if (!$token->validate($vdata)) {
			return response()->json(['error' => 'invalid_grant', 'error_description' => 'Expired authorization code'], 400);
		} elseif ($token->getClaim('client_id') != $client->name) {
			return response()->json(['error' => 'invalid_grant', 'error_description' => 'Stolen authorization code'], 400);
		}
		
		//If possible, verify that the Authorization Code has not been previously used.
		
		// Now, client is authenticated and token is valid.
		if (!$user = User::find($token->getClaim('user'))) {
			// User not found, nearly impossible to happens.
			return response()->json(['error' => 'invalid_grant', 'error_description' => 'Invalid authorization code'], 400);
		}
		Log::info('Access token has been issued for ' . $client->name . ' (' . $request->ip() . ', User:' . $user->username . ')');
		
		return response()->json([
			'access_token' => self::issueAccessToken($client->name, $token->getClaim('scope'), $user->username),
			'token_type' => 'Bearer',
			'expires_in' => 3600,
			'id_token' => $this->createIDToken($user, $client)
		]);
	}
	
	/* publishConfig()
	 *
	 * responds OpenID Connect Discovery request.
	 */
	public function publishConfig() {
		return response()->json(array(
			'issuer' => config('tusso.url'),
			-'authorization_endpoint' => config('tusso.url') . '/openid/authorize',
			'token_endpoint' => config('tusso.url') . '/openid/token',
			'response_types_supported' => ['code', 'id_token'],
			'grant_types_supported' => ["authorization_code", "implicit"],
			'claims_supported' => ['id', 'name', 'type', 'group'],
			'ui_locales_supported' => ['th', 'en'],
			
			// The following endpoint is not comply with the spec.
			'x_check_session_iframe' => config('tusso.url') . '/state_validate',
		));
	}
	
	
	/*
	 * ACCESS TOKEN RETRIEVING FLOW
	 * the following methods will be called during access token issuing process.
	 */
	
	/*
	 * getChallenge()
	 *
	 * send back a random challenge.
	 * input: [GET/POST] client_id
	 */
	public function getChallenge(Request $request) {
		if (!$request->has('client_id')) {
			return response('MALFORMED_REQUEST', 400);
		}
		$appname = $request->input('client_id');
		$signer = new Sha256();
		$token = (new Builder())->setIssuer(config('tusso.url'))// Configures the issuer (iss claim)
		->setId('TUSSO-ATREQ-' . substr(sha1($appname . microtime() . rand()), 0, 10) . rand(10, 99), true)// Configures the id (jti claim), replicating as a header item
		->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
		->setNotBefore(time())// Configures the time that the token can be used (nbf claim)
		->setExpiration(time() + 90)// To prevent replay attack, but not using database, use fast expiration time, which may break in slow connection.
		->set('client', $appname)->set('challenge', sha1(microtime() . rand() . rand(0, 999999)))->sign($signer, config('app.key'))// creates a signature
		->getToken(); // Retrieves the generated token
		
		return $token;
	}
	
	/*
	 * verifyResponse()
	 *
	 * after client hash/sign the challenge, we verify the response and issue an access token.
	 * input: [POST] Challenge, Response (SHA256 of challenge appended by client key)
	 */
	public function verifyResponse(Request $request) {
		if (!$request->has('challenge') || !$request->has('response')) {
			return response('MALFORMED_REQUEST', 400);
		}
		
		// Parse & validate JWT
		$token = (new Parser())->parse((string)$request->input('challenge'));
		$vdata = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
		$vdata->setIssuer(config('tusso.url'));
		$signer = new Sha256();
		if (!$token->validate($vdata)) {
			return response('INVALID_CHALLENGE', 400);
		} elseif (!$token->verify($signer, config('app.key'))) {
			return response('MODIFIED_CHALLENGE', 400);
		}
		
		// Get client info
		if ($client = Application::find(trim($token->getClaim('client')))) {
			if (hash('sha256', trim($request->input('challenge')) . $client->secret) != trim($request->input('response'))) {
				return response('INVALID_RESPONSE (' . hash('sha256', trim($request->input('challenge')) . $client->secret) . ')', 400);
			}
		} else {
			return response('NON_EXISTENT_CLIENT', 400);
		}
		Log::info('Access token has been issued for ' . $client->name . ' (' . $request->ip() . ')');
		
		return self::issueAccessToken($client->name, explode(',', $client->scope), '*');
	}
	
	/* validateSessionState()
	 *
	 * match requested session_state with session.
	 */
	public function validateSessionState(Request $request) {
		return ($request->input('session_state') == $request->session()->get('session_state')) ? 'valid' : 'invalid';
	}
	
	/* remoteLogout()
	 *
	 * handle RP-Initiated Logout (OpenID Connect Session Management), receive following parameters:
	 * - id_token_hint : ID Token represent currently logged in user, issued by TUSSO
	 * - post_logout_redirect_uri : URL to which the RP is requesting that the End-User's User Agent be redirected after a logout has been performed.
	 *   The value MUST have been previously registered with the OP.
	 * - state : Opaque value used by the RP to maintain state between the logout request and the callback to the endpoint
	 *   specified by the post_logout_redirect_uri query parameter.
	 */
	public function remoteLogout(Request $request) {
		if ($request->has('post_logout_redirect_uri')) {
			if (!$request->has('id_token_hint')) {
				return view('auth-error', ['error' => 'Please specify ID token.']);
			}
			/*$token = (new Parser())->parse((string)$request->input('id_token_hint'));
			$vdata = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
			$vdata->setIssuer(config('tusso.url'));
			$signer = new Sha256();
			if (!$client = Application::find(str_replace('https://', '', $token->getClaim('aud')))) {
				return view('auth-error', ['error' => 'Invalid identity token (CLI)']);
			}
			if (!$token->verify($signer, $client->secret)) {
				return view('error', ['error' => 'Invalid identity token (SIG)']);
			}
			
			$parse = parse_url($request->input('post_logout_redirect_uri'));
			if ($parse['host'] != str_replace('https://', '', $token->getClaim('aud'))) {
				return view('auth-error', ['error' => 'Unauthorized redirect URL']);
			}*/
			
			Auth::logout();
			$locale = $request->session()->get('locale', 'th');
			$request->session()->flush();
			$request->session()->put('locale', $locale);
			
			$redir = $request->input('post_logout_redirect_uri') . (str_contains($request->input('post_logout_redirect_uri'), '?') ? '&' : '?') . 'state=' . $request->input('state');
			
			return view('loggedout', ['goto' => $redir]);
		} else {
			return (new UIController)->logout($request);
		}
	}
	
	
	/*
	 * SUPPORTIVE FUNCTIONS
	 * the following methods shouldn't be called directly from outside.
	 */
	
	private function createIDToken($user, $app, $nonce = '', $session_state = '') {
		//Create JWT ID token, containing user's basic info, signed with app secret.
		$signer = new Sha256();
		$token = (new Builder())->setIssuer(config('tusso.url'))// Configures the issuer (iss claim)
		->setAudience('https://' . $app->name)// Configures the audience (aud claim)
		->setId('TUSSO-ID-' . $user->username . '-' . microtime(true) . rand(10, 99), true)// Configures the id (jti claim), replicating as a header item
		->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
		->setNotBefore(time() - 60)// Configures the time that the token can be used (nbf claim) -- set to minus to help server with inaccurate time
		->setExpiration(time() + 3600)// Configures the expiration time of the token (exp claim) -- Client should set their session expiration time to this
		->set('id', $user->username)// Configures a new claim
		->set('name', $user->name)->set('type', $user->type)->set('group', $user->group)->set('nonce', $nonce)->set('session_state', $session_state)->sign($signer,
			$app->secret)->getToken(); // Retrieves the generated token
		return (String)$token;
	}
	
	private function issuePassToken($user, $app, $nonce = '', $session_state = '', Request $request) {
		// A token that replace usual ID Token, designated for Nginx's auth_request authentication.
		// Create JWT, containing user's browser info, signed with service's private key.
		$signer = new \Lcobucci\JWT\Signer\Rsa\Sha256();
		$privateKey = new Key(Storage::get('private.key'));
		$token = (new Builder())->setIssuer(config('tusso.url'))// Configures the issuer (iss claim)
		->setAudience('https://' . $app->name)// Configures the audience (aud claim)
		->setId('TUSSO-PS-' . $user->username . '-' . microtime(true) . rand(10, 99), true)// Configures the id (jti claim), replicating as a header item
		->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
		->setNotBefore(time() - 60)// Configures the time that the token can be used (nbf claim) -- set to minus to help server with inaccurate time
		->setExpiration(time() + 3600)// Configures the expiration time of the token (exp claim) -- Client should set their session expiration time to this
		->set('id', $user->username)// Configures a new claim
		->set('type', $user->type)->set('locale', $request->getLocale())->set('nonce', $nonce)->set('session_state', $session_state)->sign($signer,
			$privateKey)->getToken(); // Retrieves the generated token
		return (String)$token;
	}
	
	private function encrypt($secret) {
		// Encrypt using OpenSSL and the AES-256-CBC cipher, signed with MAC.
		return Crypt::encrypt($secret);
	}
	
	private function decrypt($alien) {
		try {
			$decrypted = Crypt::decrypt($alien);
		} catch (DecryptException $e) {
			$decrypted = false;
		}
		
		return $decrypted;
	}
	
	/*
	 * issueAccessToken()
	 *
	 * issues an access token, enabling client to access resource server.
	 * @param String $appname
	 * @param Array $appscope
	 */
	public static function issueAccessToken($appname, $appscope, $foruser) {
		//Create JWT access token, grant access to all user.
		$signer = new \Lcobucci\JWT\Signer\Rsa\Sha256();
		$privateKey = new Key(Storage::get('private.key'));
		
		$token = (new Builder())->setIssuer(config('tusso.url'))// Configures the issuer (iss claim)
		->setAudience('https://' . $appname)// Configures the audience (aud claim)
		->setId('TUSSO-AT-' . sha1($appname . microtime() . rand(10, 99)), true)// Configures the id (jti claim), replicating as a header item
		->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
		->setNotBefore(time())// Configures the time that the token can be used (nbf claim)
		->setExpiration(time() + 3600)// Configures the expiration time of the token (exp claim)
		->set('client', $appname)// Configures a new claim
		->set('scope', $appscope)->set('foruser', $foruser)->sign($signer, $privateKey)->getToken(); // Retrieves the generated token
		return (String)$token;
	}
	
	public function getClientCredential(Request $request) {
		if ($request->has('client_id') && $request->has('client_secret')) {
			// "client_secret_post" (including the Client Credentials in the request body)
			$client_id = $request->input('client_id');
			$client_secret = $request->input('client_secret');
			
			// "client_secret_basic" (using of the HTTP Basic authentication scheme)
			/*} else {
			$headers = apache_request_headers(); // Don't worry, this function also works with FastCGI in PHP5.4+
			if (array_key_exists('Authorization', $headers)) {
				// Header must be formed as urlencode(urlencode(CLIENT_ID).':'.urlencode(CLIENT_SECRET)) (same as Twitter's)
				$clientAuthHeader = explode(' ', trim($headers['Authorization']));
				$client_credential = explode(':', $clientAuthHeader[1]);
				$client_id = urldecode($client_credential[0]);
				$client_secret = urldecode($client_credential[1]);*/
		} elseif (isset($_SERVER['PHP_AUTH_USER'])) {
			$client_id = $_SERVER['PHP_AUTH_USER'];
			$client_secret = $_SERVER['PHP_AUTH_PW'];
		} else {
			return false;
		}
		
		return ['id' => $client_id, 'secret' => $client_secret];
	}
}