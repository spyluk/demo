<?php

namespace App\Console\Commands;

use App\Models\AclPermission;
use Illuminate\Console\Command;

class AclSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions with permissions used in scripts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        base_path('app');
        $this->search(base_path('app'));
        return true;
    }

    /**
     * @param $dir
     */
    function search($dir){
        $results = array();
        $files = scandir($dir);

        foreach($files as $key => $value){
            if(!is_dir($dir. DIRECTORY_SEPARATOR .$value)){
                if ( $value != '.' && $value != '..' ){
                    if ( substr($value, -4) == '.php' && $value !== 'AclSync.php' ) {
                        $content = file_get_contents($dir . "/" . $value);
                        preg_match_all("|\>hasPermissionTo.*\(([^\)]*)\)|", $content, $matches);
                        if(!empty($matches[1])) {
                            $permissions = '';
                            foreach($matches[1] as $item) {
                                if(strpos($item, "'") !== false || strpos($item, '"') !== false) {
                                    $permission = trim(trim(trim($item), '"'), "'");
                                    $permissions .= ($permissions ? ', ' : '') . $permission;
                                    //AclPermission::findOrCreate($permission);
                                }
                            }
                            print($dir . "/" . $value . ' --> ' . $permissions . "\r\n");
                        }
                    }
                }


            } else if(is_dir($dir. DIRECTORY_SEPARATOR .$value)) {
                if ( $value != '.' && $value != '..' ) {
                    $this->search($dir . DIRECTORY_SEPARATOR . $value);
                }
            }
        }
    }
}
