<?php

use Illuminate\Database\Seeder;
use App\Migrations\Manager as MigrationsManager;
use App\Models\CategoryType as CategoryTypeModel;
use \App\Models\VsmModule as VsmModuleModel;

class ModuleSeeder extends Seeder
{
    const CONFIG = 'database/seeds/config/modules/config.php';

    const MODULES = 'database/seeds/config/modules/list';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($scanned_directory = array_diff(scandir(self::MODULES), array('..', '.'))) {

            $vars = file_exists(self::CONFIG) ? include self::CONFIG : [];
            foreach ($scanned_directory as $item) {
                $file = self::MODULES . '/' . $item;
                if (is_dir($file)) {
                    continue;
                }

                $content = file_get_contents($file);
                $module = str_replace(".php", '', $item);
                $category_name = !empty($vars[$module]['category']) ? $vars[$module]['category'] : str_replace(".", "/", $module);
                $category_id = MigrationsManager::addCategoryIfNotExists($category_name, CategoryTypeModel::MODULE, $module);

                (new VsmModuleModel)->updateOrCreate(
                    ['category_id' => $category_id],
                    [
                        'category_id' => $category_id,
                        'content' => $content
                    ]
                );
            }
        }
    }

}
