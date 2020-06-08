<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sites')->insert([
            ['id' => Site::DEFAULT_SITE, 'url' => 'http://default', 'domain' => 'default', 'name' => 'Default empty site', 'email' => null],
            ['id' => Site::ONLINETUTORSERVICE, 'url' => 'http://onlinetutorservice.com.loc:8080', 'domain' => 'onlinetutorservice.com.loc', 'name' => 'OnlineTutorService.com', 'email' => 'noreply@onlinetutorservice.com'],
        ]);
    }
}
