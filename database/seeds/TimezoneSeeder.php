<?php

use App\Models\Timezone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimezoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timezone_identifiers = DateTimeZone::listIdentifiers();
        foreach($timezone_identifiers as $item) {
            $timezone = new Timezone;
            $timezone->name = $item;
            $timezone->save();
        }
    }
}