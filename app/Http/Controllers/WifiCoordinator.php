<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WifiCoordinator extends Controller {
	// Work as captive portal login for Unifi AP Controller
	public function unifiInitialize(Request $request) {
		$request->session()->put('mac', $request->input('id'));
		$request->session()->put('redirect-url', $request->input('url', config('unifi.default_url')));

		return view('login', ['mac' => $request->input('id'), 'redirect' => '/unifi/auth']);
	}

	public function unifiAuthorize(Request $request) {
		// Start Curl for login
		$ch = curl_init();
		// We are posting data
		curl_setopt($ch, CURLOPT_POST, true);
		// Set up cookies
		$cookie_file = "/tmp/unifi_cookie";
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		// Allow Self Signed Certs
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		// Force SSL3 only
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		// Login to the UniFi controller
		curl_setopt($ch, CURLOPT_URL, config('unifi.controller_address') . "/login");
		curl_setopt($ch, CURLOPT_POSTFIELDS,
			"login=login&username=" . config('unifi.controller_user') . "&password=" . config('unifi.controller_password'));
		curl_exec($ch);
		curl_close($ch);

		// Send user to authorize and the time allowed
		$data = json_encode(array(
			'cmd' => 'authorize-guest',
			'mac' => $request->session()->get('mac'),
			'minutes' => config('unifi.timeout')
		));
		$ch = curl_init();
		// We are posting data
		curl_setopt($ch, CURLOPT_POST, true);
		// Set up cookies
		$cookie_file = "/tmp/unifi_cookie";
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		// Allow Self Signed Certs
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		// Force SSL3 only
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		// Make the API Call
		curl_setopt($ch, CURLOPT_URL, config('unifi.controller_address') . '/api/cmd/stamgr');
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'json=' . $data);
		curl_exec($ch);
		curl_close($ch);

		// Logout of the connection
		$ch = curl_init();
		// We are posting data
		curl_setopt($ch, CURLOPT_POST, true);
		// Set up cookies
		$cookie_file = "/tmp/unifi_cookie";
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		// Allow Self Signed Certs
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		// Force SSL3 only
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		// Make the API Call
		curl_setopt($ch, CURLOPT_URL, config('unifi.controller_address') . '/logout');
		curl_exec($ch);
		curl_close($ch);
		//echo "Login successful, I should redirect to: " . $url; //$_SESSION['url'];
		//sleep(8); // Small sleep to allow controller time to authorize
		return response('<h3>Login successful, please wait</h3>')->header('Refresh', '8;'.$request->session()->get('redirect-url'));
	}
}