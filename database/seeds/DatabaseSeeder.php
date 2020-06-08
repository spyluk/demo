<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SiteSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(TimezoneSeeder::class);
        $this->call(AclSeeder::class);
        $this->call(SystemEventSeeder::class);
        $this->call(EntitySeeder::class);
        $this->call(ListSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(ModuleVarsSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(OtsEventTypeSeeder::class);
//        $this->call(OtsEventUserTypeSeeder::class);
        $this->call(OtsEventStatusSeeder::class);
        $this->call(RolesToTagSeeder::class);
    }
}
