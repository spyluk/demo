<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type')->comment('possible triggered event');
            $table->boolean('active')->default(1);
            $table->timestamps();
        });

        Schema::create('system_event_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type');
            $table->timestamps();
        });

        Schema::create('system_event_models', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('system_event_id');
            $table->string('model_type');
            $table->integer('model_id');
            $table->timestamps();

            $table->foreign('system_event_id')->references('id')->on('system_events');
            $table->index(['model_type', 'model_id']);
        });

        Schema::create('system_event_model_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('system_event_model_id');
            $table->unsignedInteger('system_event_action_id');
            $table->integer('order');
            $table->jsonb('data')->nullable();
            $table->timestamps();

            $table->foreign('system_event_model_id')->references('id')->on('system_event_models');
            $table->foreign('system_event_action_id')->references('id')->on('system_event_actions');
            $table->index(['order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_event');
    }
}
