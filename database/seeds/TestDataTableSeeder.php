<?php

use Illuminate\Database\Seeder;

class TestDataTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		DB::table('client')->insert([
			'id' => 'TEST',
			'description' => 'Testing client',
			'key' => 'kkkkk',
		]);
		DB::table('personnel')->insert([
			'id' => 's00000',
			'type' => 'student',
			'name' => 'John Doe',
			'organization' => 'ห้อง 948',
			'card' => '0123456789'
		]);
	}
}
