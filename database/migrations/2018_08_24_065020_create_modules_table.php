<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vsm_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('created_user_id')->nullable();
            $table->longText('content');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['category_id']);

            $table->foreign('category_id')->references('id')->on('categories');
        });

        Schema::create('vsm_module_relates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('module_id');
            $table->unsignedInteger('relate_id');

            $table->foreign('module_id')->references('id')->on('vsm_modules');
            $table->foreign('relate_id')->references('id')->on('vsm_modules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_relates');
        Schema::dropIfExists('modules');
    }
}
