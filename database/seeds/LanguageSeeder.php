<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Site;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('languages')->insert([
            ['code' => 'en', 'name' => 'English', 'local_name' => 'English'],
            ['code' => 'ru', 'name' => 'Russian', 'local_name' => 'Русский'],
        ]);

        $language = new Language;
        DB::table('site_languages')->insert([
            ['site_id' => Site::DEFAULT_SITE, 'language_id' => $language->getLanguageByCode('en')['id']],
            ['site_id' => Site::DEFAULT_SITE, 'language_id' => $language->getLanguageByCode('ru')['id']],

            ['site_id' => Site::ONLINETUTORSERVICE, 'language_id' => $language->getLanguageByCode('en')['id']],
            ['site_id' => Site::ONLINETUTORSERVICE, 'language_id' => $language->getLanguageByCode('ru')['id']],
        ]);
    }
}
