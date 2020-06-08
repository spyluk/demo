<?php

use Illuminate\Database\Seeder;
use App\Models\AclRole;
use App\Models\AclPermission;

class AclSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $permissions = [
        /**
         *
         */
        'management.subject.requested.approved' => ['manager'],
        /**
         * Management of school groups
         */
        'management.school.groups' => ['manager'],
        /**
         * Management of another users subjects
         */
        'management.users.subjects' => ['manager'],
        /**
         * Management of users
         */
        'management.users' => ['manager'],
        /**
         * View project groups
         */
        'school.groups.view' => ['manager'],
        /**
         *
         */
        //'management.event.set.status'  => ['manager'],
        /**
         * Full list of users without filtering by tags
         */
        'user.list' => ['manager'],
        /**
         * User can assign tags to another user
         */
        'management.users.assign.tags' => ['manager'],
        /**
         * User can remove tags from another user
         */
        'management.users.remove.tags' => ['manager'],
        /**
         * User can assign groups to another user
         */
        'management.users.assign.groups' => ['manager'],
        /**
         * User can remove groups from another user
         */
        'management.users.remove.groups' => ['manager'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Add roles
         */
        try {
            $aclRole = new AclRole;
            foreach (\App\Acl\AclManager::$roles as $role) {
                $aclRole->create(['name' => $role]);
            }

        } catch (\Exception $e) {
            print_r($e->getMessage());
        }

        $this->addPermission();
    }

    /**
     * Add permissions
     */
    protected function addPermission()
    {
        try {
            foreach($this->permissions as $permission => $roles) {
                $permissionR = AclPermission::create(['name' => $permission]);
                if($roles) {
                    foreach($roles as $role) {
                        $permissionR->assignRole($role);
                    }
                }
            }
        } catch (\Exception $e) {}

    }
}
