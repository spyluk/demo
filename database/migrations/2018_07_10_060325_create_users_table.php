<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Language;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid')->nullable();
            $table->unsignedInteger('site_id');
            $table->unsignedInteger('type_id')->default('2')->comment('1 - visitor; 2 - client');
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('timezone_id')->nullable();
            $table->unsignedInteger('language_id')->default(Language::ENGLISH);
            $table->string('email');
            $table->tinyInteger('email_verified')->default(0);
            $table->string('password');
            $table->string('user_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->rememberToken();
            $table->boolean('active')->default(0);

            $table->foreign('site_id')->references('id')->on('sites');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('timezone_id')->references('id')->on('timezones');
            $table->foreign('language_id')->references('id')->on('languages');

            $table->unique(['site_id', 'email']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}