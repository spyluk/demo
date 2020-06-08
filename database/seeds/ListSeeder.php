<?php

use Illuminate\Database\Seeder;
use App\Migrations\Manager as MigrationsManager;
use \App\Models\VsmModule;
use \App\Models\CustomVar;
use \App\Models\Site;

class ListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($scanned_directory = array_diff(scandir('database/seeds/config/lists'), array('..', '.'))) {
            foreach ($scanned_directory as $item) {
                $config = require_once 'database/seeds/config/lists/' . $item;
                if ($config) {
                    MigrationsManager::addVariableByConfig(null, VsmModule::class, $config, CustomVar::TYPE_LIST, Site::ONLINETUTORSERVICE);
                }
            }
        }
    }
}
