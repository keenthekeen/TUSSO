<?php
// TUSSO Configuration file
return [

	/*--------------------------------------------------------------------------
	 * Application location
	 * --------------------------------------------------------------------------
	 * with no trailing slash
	 * example: https://sso.triamudom.ac.th
	 */
	'url' => env('TUSSO_URL','http://localhost'),


	/*
	|--------------------------------------------------------------------------
	| Shutdown switch
	|--------------------------------------------------------------------------
	| During shutdown , web access will be denied and all emails will be sent to specified address.
	|
	| Useful when under attack.
	*/
	'shutdown' => false,


	/* --------------------------------------------------------------------------
	 * LDAP Switch
	 * --------------------------------------------------------------------------
	 * You can turn off LDAP usage here, useful in local environment.
	 * When off, the app will not try to connect to directory server, instead, it will use saved user info in DB.
	 */
	'use_ldap' => env('TUSSO_USE_LDAP',true),

	/* CAUTION!
	 * - This app utilize laravel's encryption key configuration (APP_KEY), secure and random encryption key is required.
	 * 
	 */
];
