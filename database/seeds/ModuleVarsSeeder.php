<?php

use Illuminate\Database\Seeder;
use App\Migrations\Manager as MigrationsManager;
use App\Models\VsmModule as VsmModuleModel;
use App\Models\Category as CategoryModel;
use App\Models\Site as SiteModel;
use App\Models\Entity as EntityModel;
use \App\Models\CustomVar as CustomVarModel;

class ModuleVarsSeeder extends Seeder
{
    const VARS = 'database/seeds/config/vars';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($scanned_directory = array_diff(scandir(self::VARS), array('..', '.'))) {

            foreach ($scanned_directory as $item) {
                $item = str_replace(".php", "", $item);
                $config_file = self::VARS . '/' . $item . '.php';
                $config = file_exists($config_file) ? include $config_file : [];
                if ($config) {

                    if(($category = (new CategoryModel)->getCategoryByCode(SiteModel::ONLINETUTORSERVICE, $item))
                        && ($module = VsmModuleModel::where('category_id', $category->id)->first()))
                    {
                        $config['category_id'] = $category->id;
                        MigrationsManager::addVariableByConfig($module->id, \App\Models\VsmModule::class, $config, CustomVarModel::TYPE_VAR, SiteModel::ONLINETUTORSERVICE);
                    } else {
                        MigrationsManager::addVariableByConfig(null, null, $config, CustomVarModel::TYPE_VAR, SiteModel::ONLINETUTORSERVICE);
                    }
                } else {
                    print "No config: " . $config_file . "\r\n";
                }

            }
        }
    }
}
