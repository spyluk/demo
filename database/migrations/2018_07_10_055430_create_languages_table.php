<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->char('code', 2)->unique();
            $table->string('name');
            $table->string('local_name');
        });

        Schema::create('site_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('site_id');
            $table->unsignedInteger('language_id');

            $table->foreign('site_id')->references('id')->on('sites');
            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
}
