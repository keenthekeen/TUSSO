<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model {

	protected $primaryKey = 'name';
	public $incrementing = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'secret',
		'scope',
		'redirect_uri',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'secret',
	];
}
