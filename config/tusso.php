<?php
// TUSSO Configuration file
return [

	/*--------------------------------------------------------------------------
	 * Application location
	 * --------------------------------------------------------------------------
	 * with no trailing slash
	 * example: https://sso.triamudom.ac.th
	 */
	'url' => 'http://localhost',


	/*
	|--------------------------------------------------------------------------
	| Shutdown switch
	|--------------------------------------------------------------------------
	| During shutdown , web access will be denied and all emails will be sent to specified address.
	|
	| Useful when under attack.
	*/
	'shutdown' => false,

	/*
	 * --------------------------------------------------------------------------
	 * Encryption initialization factor
	 * --------------------------------------------------------------------------
	 * Must be exact length at 16 characters.
	 */
	'aes_ivfactor' => 'TriamudomSSOProV',

	/* CAUTION!
	 * This app utilize laravel's encryption key configuration (APP_KEY)
	 */
];
