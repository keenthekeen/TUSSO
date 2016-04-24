<?php

namespace App\Http\Middleware;

use App\Application;
use App\Http\Controllers\ProviderController;
use Closure;
use Auth;

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
			} else {
				return response()->json(['error' => 'invalid_client', 'error_description' => 'Client not found'], 400);
			}
		} elseif ($allowUser) {
			if (!Auth::check()) {
				return response()->json([
					'error' => 'invalid_client',
					'error_description' => 'No client credential found'
				], 401);
			}
		} else {
			return response()->json([
				'error' => 'invalid_client',
				'error_description' => 'No client credential found'
			], 401);
		}

		return $next($request);
	}
}
