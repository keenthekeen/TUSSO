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
	| During shutdown , web access will be denied.
	*/
	'shutdown' => false,


	/* --------------------------------------------------------------------------
	 * LDAP Switch
	 * --------------------------------------------------------------------------
	 * You can turn off LDAP usage here, useful in local environment.
	 * When off, the app will not try to connect to directory server, instead, it will use saved user info in DB.
	 */
	'use_ldap' => env('TUSSO_USE_LDAP',true),

	/* --------------------------------------------------------------------------
	 * Allow new student login
	 * --------------------------------------------------------------------------
	 * Allow new student to register&login using their TUENT info, expecting "tuent_applicant" table containing fname,lname,nationalid,plan_id
	 */
	'use_tuent' => env('TUSSO_ALLOW_TUENT',false),

	/* --------------------------------------------------------------------------
	 * Allow password change
	 * --------------------------------------------------------------------------
	 *  Allow users to change their password
	 */
	'allow_password_change' => env('TUSSO_ALLOW_PASSWORD_CHANGE', false),

	/* --------------------------------------------------------------------------
	 * TURS URL
	 * --------------------------------------------------------------------------
	 */
	'turs' => 'http://resource.local.triamudom.ac.th',

	/* --------------------------------------------------------------------------
	 * Administrators
	 * --------------------------------------------------------------------------
	 */
	'admin' => array('tuadmin', 's53783'),

	/* CAUTION!
	 * - This app utilize laravel's encryption key configuration (APP_KEY), secure randomized encryption key is required.
	 * 
	 */
];
