<?php

namespace App\Http\Middleware;

use App\Application;
use App\Http\Controllers\ProviderController;
use Closure;
use Auth;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use Storage;

class ApiAuth {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @param  string|null              $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $allowUser = false) {
		
		$allowUser = !empty($allowUser);
		
		// Get client secret
		$providerController = new ProviderController();
		if ($clientCredential = $providerController->getClientCredential($request)) {
			$client_id = $clientCredential['id'];
			$client_secret = $clientCredential['secret'];
			
			// Authenticate client
			if ($client = Application::find($client_id)) {
				if ($client->secret != $client_secret) {
					return response()->json([
						'error' => 'invalid_client',
						'error_description' => 'Invalid client credential'
					], 400);
				} elseif ($request->has('redirect_uri')) {
					$allowed_uri = explode(',', $client->redirect_uri);
					if (!in_array(rtrim($request->input('redirect_uri'), '/'), $allowed_uri)) {
						return response()->json([
							'error' => 'invalid_client',
							'error_description' => 'Invalid redirect uri'
						], 400);
					}
				}
				$request->session()->put('api_clearance', '*');
			} else {
				return response()->json(['error' => 'invalid_client', 'error_description' => 'Client not found'], 400);
			}
		} elseif (Auth::check() && $allowUser) {
			// OK
		} else {
			// Get access token
			if ($request->has('access_token')) {
				// "client_secret_post" (including the Client Credentials in the request body)
				$access_token = $request->input('access_token');
			} else {
				$headers = apache_request_headers(); // Don't worry, this function also works with FastCGI in PHP5.4+
				if (array_key_exists('Authorization', $headers)) {
					// "client_secret_basic" (using of the HTTP Basic authentication scheme)
					// Header must be formed as urlencode(urlencode(CLIENT_ID).':'.urlencode(CLIENT_SECRET)) (same as Twitter's)
					$clientAuthHeader = explode(' ', trim($headers['Authorization']));
					$access_token = $clientAuthHeader[1];
				} else {
					return response()->json([
						'error' => 'invalid_client',
						'error_description' => 'No client credential found'
					], 401)->header('WWW-Authenticate', 'Basic realm="Please specify your client credential"');
				}
			}

			// Parse & validate JWT
			try {
				$token = (new Parser())->parse((string)$access_token);
			} catch (\Exception $e) {
				return response()->json([
					'error' => 'MALFORMED_ACCESS_TOKEN',
				], 403);
			}
			$vdata = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
			$signer = new Sha256();
			$publicKey = new Key(Storage::get('public.key'));
			if (!$token->validate($vdata)) {
				return response()->json([
					'error' => 'INVALID_ACCESS_TOKEN',
				], 403);
			} elseif (!$token->verify($signer, $publicKey)) {
				return response()->json([
					'error' => 'UNTRUSTED_ACCESS_TOKEN',
				], 403);
			} else {
				$request->session()->put('api_clearance', $token->getClaim('foruser'));
			}
		}
		
		return $next($request);
	}
}
