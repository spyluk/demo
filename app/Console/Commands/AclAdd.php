<?php

namespace App\Console\Commands;

use App\Models\AclPermission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AclAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:add {permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permission';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $permission = $userId = $this->argument('permission');
        if(!AclPermission::getByName($permission)) {
            AclPermission::findOrCreate($permission);
            $this->info('Permission successfully added.');
            return true;
        } else {
            $this->error('Such permission already exists!');
        }

        return false;
    }
}
