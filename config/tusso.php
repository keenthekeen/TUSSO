<?php
// TUSSO Configuration file
return [

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
	 * Encryption intrinsic factor
	 * --------------------------------------------------------------------------
	 * Must be exact length at 16 characters.
	 */
	'aes_ivfactor' => 'TriamudomSSOProV',

	
];
