<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \App\Models\Entity;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Entity::$list as $id => $object) {
            DB::table('entities')->insert([['id' => $id, 'name' => $object]]);
        }
    }
}
