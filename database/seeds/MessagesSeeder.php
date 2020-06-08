<?php

use Illuminate\Database\Seeder;

class MessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testUser = \App\Models\User::find(1)->first();
        for($i = 1; $i <= 2; $i++)
        {
            (new \App\Services\Ots\Messages\MessageService($testUser))
                ->messageToUser(
                    'Subject ' . $i,
                    'Message text',
                    2,
                    2,
                    3,
                    ['common']
                );
        }
    }

}
