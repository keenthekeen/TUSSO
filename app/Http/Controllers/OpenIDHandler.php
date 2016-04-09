<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Log;
use Auth;
use Validator;

class OpenIDHandler extends Controller {
	/*
	 * OpenID Connect 1.0 Provider
	 *
	 * Referenced from http://openid.net/specs/openid-connect-core-1_0.html
	 * Created by Siwat Techavoranant
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

	public function AuthRequest (Request $request) {
		// OpenID Connect uses the following OAuth 2.0 request parameters with the Authorization Code Flow:
		$validator = Validator::make($request->all(), [
			/* scope
			 *
			 * REQUIRED. OpenID Connect requests MUST contain the openid scope value.
			 * If the openid scope value is not present, the behavior is entirely unspecified.
			 * Other scope values MAY be present.
			 * Scope values used that are not understood by an implementation SHOULD be ignored.
			 *
			 # We currently implements only "openid" scope.
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
			'response_type' => 'required|in:code,id_token',

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
			// @todo Display user-friendly error page.
			return 'AUTHENTICATION_REQUEST_MALFORMED';
		}

		// @todo Verify if redirect_uri exactly matches our data of client_id.

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
			return redirect($request->input('redirect_uri').'?error=login_required');
		} elseif (!Auth::check()) {
			// User not authenticated.
			// After logged in, makes the user redirected back here again.
			$request->session()->put('redirect_queue', $request->fullUrl());
			return redirect('/login')->with('notify', trans('messages.pleaselogin'));
		}

		/* Step 4: Authorization Server Obtains End-User Consent/Authorization
		 *
		 * We allowed only trusted client to access our api, so let's skip this.
		 */

		/* Step 5: Successful Authentication Response
		 *
		 * WORK IN PROGRESS
		 */
	}
}