<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFailedLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('failed_logins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username'); // random string
            $table->string('ip',16); // comma-separated values of allowed scope e.g. club,result
            $table->timestamps();
    
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('applications');
    }
}
