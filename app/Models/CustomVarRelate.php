<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

/**
 * Class CustomVarRelate
 * @property string $owner_model_type
 * @property int $owner_model_id
 * @property string $model_type
 * @property int $model_id
 * @property int $var_id
 *
 * @package App\Models
 */
class CustomVarRelate extends Model
{
    use BaseTrait;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'owner_model_type',
        'owner_model_id',
        'model_type',
        'model_id',
        'var_id'
    ];

    /**
     * @param string $owner_model_type
     * @param int $owner_model_id
     * @param string $model_type
     * @param int $model_id
     * @param int $category
     * @return Model|null|object|static
     */
    public function getByModelAndCategory(string $owner_model_type,  int $owner_model_id, string $model_type, int $model_id, int $category)
    {
        return $this->getInitDbByFields([
            'owner_model_type' => $owner_model_type,
            'owner_model_id' => $owner_model_id,
            'model_type' => $model_type,
            'model_id' => $model_id
        ])
            ->join('custom_vars as cv', 'cv.id', '=', 'x.var_id')
            ->where('cv.category_id', '=', $category)
            ->select('x.*')
            ->first();
    }


    /**
     * @param $model
     * @param int $model_id
     * @param int $var_id
     * @param string $owner_model_type
     * @param int $owner_model_id
     * @return Model|null
     */
    public function add($model, int $model_id, int $var_id, string $owner_model_type = null, int $owner_model_id = null)
    {
        $modelClass = is_string($model) ? $model : get_class($model);
        $owner_model_type = $owner_model_type ?? $modelClass;
        $owner_model_id = $owner_model_id ?? $model_id;
        return $this->updateOrCreate(
            [
                'model_type' => $modelClass,
                'model_id' => $model_id,
                'owner_model_id' => $owner_model_id,
                'owner_model_type' => $owner_model_type,
                'var_id' => $var_id
            ],
            [
                'model_type' => $modelClass,
                'model_id' => $model_id,
                'owner_model_id' => $owner_model_id,
                'owner_model_type' => $owner_model_type,
                'var_id' => $var_id
            ]
        );
    }

    /**
     * @param $model
     * @param int $model_id
     * @param int $var_id
     * @param string $owner_model_type
     * @param int $owner_model_id
     * @return mixed
     */
    public function remove($model, int $model_id, int $var_id, string $owner_model_type, int $owner_model_id)
    {
        $modelClass = is_string($model) ? $model : get_class($model);
        return $this->deleteByFields([
            'model_type' => $modelClass,
            'model_id' => $model_id,
            'owner_model_id' => $owner_model_id,
            'owner_model_type' => $owner_model_type,
            'var_id' => $var_id
        ]);
    }
}
