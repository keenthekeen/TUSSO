<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->string('username',15); // AD: samaccountname
            $table->enum('type', ['student', 'staff']); // If memberof contains "Students"
            $table->string('name'); // AD: cn (in students) or description (in staffs)
            $table->string('group'); // AD: explode(','memberof,2)[0] without starting OU=
	        $table->string('password');
	        $table->string('remember_token');
            $table->timestamps();

            $table->primary('username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('users');
    }
}