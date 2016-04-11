<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('applications', function (Blueprint $table) {
            $table->string('name',50); // e.g. reg.clubs.triamudom.ac.th
            $table->string('secret'); // random string
            $table->string('scope'); // comma-separated values of allowed scope e.g. club,result
            $table->string('redirect_uri'); // comma-separated values of allowed redirect uri e.g. https://reg.clubs.triamudom.ac.th/sso
            $table->timestamps();

            $table->primary('name');
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
