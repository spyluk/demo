<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class CustomVarCode
 * @property int $id
 * @property int $var_id
 * @property int $model_id
 * @property string $model_type
 * @property bool $active
 *
 * @package App\Models
 */
class CustomVarModel extends Model
{
    use BaseTrait;

    use HasRoles {
        hasPermissionTo as hasPermissionToParent;
        assignRole as assignRoleParent;
    }

    /**
     * @var string
     */
    public $guard_name = 'api';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'var_id',
        'model_id',
        'model_type',
        'active'
    ];

    /**
     * @param $category_code
     * @param $model_type
     * @param $model_id
     * @return mixed
     */
    public function getCustomModelByCategoryAndModel($category_code, $model_type, $model_id)
    {
        return static::select('custom_var_models.*', 'cvc.code')
            ->leftJoin('custom_var_codes as cvc', 'custom_var_models.var_id', '=', 'cvc.var_id')
            ->leftJoin('category_codes as cc', 'cvc.category_id', '=', 'cc.category_id')
            ->where('cc.code', $category_code)
            ->where('model_type', $model_type)
            ->where('model_id', $model_id)
            ->get();
    }

    /**
     * Assign the given role to the model.
     *
     * @param array|string|\Spatie\Permission\Contracts\Role ...$roles
     *
     * @return $this
     */
    public function assignRole(...$roles)    {
        $return = false;
        try {
            $return = $this->assignRoleParent($roles);
        } catch (\Exception $e) {}

        return $return;
    }

    /**
     * @param string $permission
     * @param string|null $guardName
     * @return bool
     */
    public function hasPermissionTo(string $permission, string $guardName = null): bool
    {
        $return = false;
        try {
            $return = $this->hasPermissionToParent($permission, $guardName);
        } catch (\Exception $e) {}

        return $return;
    }
}
