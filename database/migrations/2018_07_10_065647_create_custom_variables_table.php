<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Site;

class CreateCustomVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Локализированные переменные и деревя
         */
        Schema::create('custom_vars', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('site_id')->default(Site::ONLINETUTORSERVICE);
            $table->unsignedInteger('type_id')->comment = '1-var, 2-list, 3-attr';
            $table->unsignedInteger('format_id')->comment = '1-string, 2-json'; /*1-string, 2-json*/
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('path')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->timestamps();
            $table->boolean('active')->default(1);

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('parent_id')->references('id')->on('custom_vars');
            $table->foreign('site_id')->references('id')->on('sites');
        });

        Schema::create('custom_var_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('language_id');
            $table->unsignedInteger('var_id');

            $table->text('value');
            $table->timestamps();

            $table->foreign('language_id')->references('id')->on('languages');
            $table->foreign('var_id')->references('id')->on('custom_vars');
            $table->index(['language_id', 'var_id']);
        });

        Schema::create('custom_var_attrs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('var_id');
            $table->jsonb('value');
            $table->timestamps();

            $table->foreign('var_id')->references('id')->on('custom_vars');
        });

        Schema::create('custom_var_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('var_id');
            $table->unsignedInteger('category_id');
            $table->string('code');

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('var_id')->references('id')->on('custom_vars');
            $table->unique(['var_id', 'category_id'], 'var_id_category_id_unique');
            $table->index(['code']);
        });

        Schema::create('custom_var_models', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('var_id');
            $table->string('model_type');
            $table->unsignedInteger('model_id');
            $table->boolean('active')->default(1);

            $table->index(['model_type', 'model_id']);
            $table->unique(['var_id', 'model_type', 'model_id'], 'var_id_module_type_module_id_unique');
            $table->foreign('var_id')->references('id')->on('custom_vars');
        });

        Schema::create('custom_var_relates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('owner_model_type');
            $table->unsignedInteger('owner_model_id')->comment('Each model can have their own vars on the other model (message, user...).');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->unsignedInteger('var_id');
            $table->index(['model_id', 'model_type'], 'model_id_model_type_index');

            $table->foreign('var_id')->references('id')->on('custom_vars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_var_codes');
        Schema::dropIfExists('custom_var_values');
        Schema::dropIfExists('custom_var_categories');
        Schema::dropIfExists('custom_var_relates');
        Schema::dropIfExists('custom_var_attrs');
        Schema::dropIfExists('custom_vars');
    }
}
