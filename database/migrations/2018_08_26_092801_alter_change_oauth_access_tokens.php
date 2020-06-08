<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Artisan;

class AlterChangeOauthAccessTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `oauth_access_tokens` CHANGE `expires_at` `expires_at` INT(11) NULL DEFAULT NULL;');
        Artisan::call('passport:install', ["--force"=> true ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `oauth_access_tokens` CHANGE `expires_at` `expires_at` DATETIME NULL DEFAULT NULL;');
    }
}
