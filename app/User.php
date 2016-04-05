<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Adldap\Laravel\Traits\AdldapUserModelTrait;

class User extends Authenticatable {
	use AdldapUserModelTrait;

	protected $primaryKey = 'username';
	public $incrementing = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'type',
		'username',
		'group',
		'password',
		'remember_token'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token'
	];
}
