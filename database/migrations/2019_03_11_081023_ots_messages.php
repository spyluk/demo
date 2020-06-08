<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Bridges\MessageType as MessageTypeBridge;
use \Illuminate\Support\Facades\DB;

class OtsMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('main_id')->nullable()->comment('id of the first message in the thread');
            $table->unsignedInteger('user_id')->comment('the user who created the message');
            $table->unsignedInteger('site_id');
            $table->string('subject');
            $table->text('message');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('main_id')->references('id')->on('ots_messages');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('site_id')->references('id')->on('sites');
        });

        Schema::create('ots_message_statistics', function (Blueprint $table) {
            $table->unsignedBigInteger('main_id');
            $table->unsignedInteger('count')->nullable();
            $table->unsignedBigInteger('last_id');
            $table->timestamp('last_at');

            $table->primary('main_id');
            $table->foreign('main_id')->references('id')->on('ots_messages');
            $table->foreign('last_id')->references('id')->on('ots_messages');
        });

        Schema::create('ots_message_users', function (Blueprint $table) {
            $table->unsignedBigInteger('message_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('read')->default('0');
            $table->timestamp('read_at');

            $table->unique(['message_id', 'user_id']);
            $table->foreign('message_id')->references('id')->on('ots_messages');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('ots_message_entities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('main_id');
            $table->unsignedInteger('entity_id');
            $table->unsignedInteger('pk_id');

            $table->unique(['main_id', 'entity_id', 'pk_id']);
            $table->foreign('main_id')->references('id')->on('ots_messages');
            $table->foreign('entity_id')->references('id')->on('entities');
        });

        Schema::create('ots_message_user_tags', function (Blueprint $table) {
            $table->increments('id')->comment('Each user can have their own tags on the message.');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('message_id');
            $table->unsignedInteger('tag_id');

            $table->unique(['message_id', 'user_id', 'tag_id']);
            $table->foreign('message_id')->references('id')->on('ots_messages');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tag_id')->references('id')->on('custom_vars');
        });

        Schema::create('ots_message_user_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('site_id');
            $table->unsignedInteger('tag_id')->comment('Tag id, message can have several tag')->nullable();
            $table->unsignedInteger('count')->nullable();
            $table->unsignedInteger('count_read')->default('0');

            $table->unique(['user_id', 'site_id', 'tag_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('site_id')->references('id')->on('sites');
            $table->foreign('tag_id')->references('id')->on('custom_vars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ots_message_entities');
        Schema::dropIfExists('ots_message_user_statistics');
        Schema::dropIfExists('ots_message_user_tags');
        Schema::dropIfExists('ots_message_users');
        Schema::dropIfExists('ots_message_statistics');
        Schema::dropIfExists('ots_messages');
    }
}
