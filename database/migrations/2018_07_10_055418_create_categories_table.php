<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Site;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create(/**
         * @param Blueprint $table
         */
            'categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('site_id')->default(Site::ONLINETUTORSERVICE);
            $table->unsignedInteger('type_id');
            $table->string('name');
            $table->string('path')->nullable();
            $table->unsignedInteger('level');
            $table->boolean('system')->index()
                ->comment('Category used for system operation, closed for deletion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('site_id')->references('id')->on('sites');
            $table->foreign('parent_id')->references('id')->on('categories');
            $table->foreign('type_id')->references('id')->on('category_types');
        });

        Schema::create('category_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->nullable()->unique();
            $table->string('code');

            $table->index(['code']);
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('category_types');
    }
}
