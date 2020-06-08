<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CategoryType;
use \App\Models\OtsTutor;
use \App\Models\User;

class CategorySeeder extends Seeder
{
    /**
     * @var array
     */
    protected $categories = [
            ['code' => User::CODE_TEXT, 'name' => 'Tutor/About/Text'],
            ['code' => User::CODE_SHORT_TEXT, 'name' => 'Tutor/About/Short text']
        ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (CategoryType::$types as $id_type => $name_type) {
            DB::table('category_types')->insert([['id' => $id_type, 'name' => $name_type]]);
        }

        foreach ($this->categories as $category) {
            \App\Migrations\Manager::addCategoryIfNotExists(
                $category['name'],
                CategoryType::VARS,
                $category['code'],
                true);
        }
    }
}
