<?php

namespace App\Models;

use App\Components\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as ParentRole;
use App\Models\Traits\BaseTrait;

class AclRole extends ParentRole
{
    use SoftDeletes;
    use BaseTrait {
        create as createParent;
    }

    public $guard_name = 'api';

    /**
     * @param array $attributes
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public static function create(array $attributes = [])
    {
        return parent::create($attributes);
    }
}
