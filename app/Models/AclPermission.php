<?php

namespace App\Models;

use App\Components\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as ParentPermission;
use Spatie\Permission\Guard;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use App\Models\Traits\BaseTrait;

class AclPermission extends ParentPermission implements PermissionContract
{
    use BaseTrait, SoftDeletes;

    public $guard_name = 'api';

    /**
     * @param array $attributes
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public static function create(array $attributes = []) {
        return parent::create($attributes);
    }

    /**
     * @param string $name
     * @param null $guardName
     * @return PermissionContract
     */
    public static function findOrCreate(string $name, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermissions(['name' => $name, 'guard_name' => $guardName])->first();

        if (! $permission) {
            return static::query()->create(['name' => $name, 'guard_name' => $guardName]);
        }

        return $permission;
    }

    /**
     * @param int $var_id
     * @param null $guardName
     * @return PermissionContract
     */
    public static function findOrCreateVar(int $var_id, $guardName = null)
    {
        $permission_name = 'var.'.$var_id;
        return static::findOrCreate($permission_name, $guardName);
    }
}
