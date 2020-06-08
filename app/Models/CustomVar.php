<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Site;
use App\Components\Database\Query\Builder as QueryBuilder;

class CustomVar extends Model
{
    use BaseTrait {
        create as createParent;
    }

    /**
     *
     */
    const TYPE_VAR = 1;
    /**
     *
     */
    const TYPE_LIST = 2;
    /**
     *
     */
    const TYPE_ATTR = 3;
    /**
     *
     */
    const FORMAT_STRING = 1;
    /**
     *
     */
    const FORMAT_JSON = 2;


    /**
     * @var array
     */
    protected $fillable = [
        'category_id',
        'type_id',
        'parent_id',
        'order',
        'active',
        'format_id',
        'site_id',
        'path'
    ];

    /**
     * @param array $data
     * @return Model|null
     */
    public function create(array $data)
    {
        $res = $this->createParent($data);
        $parent = null;
        if($res['parent_id']) {
            $parent = self::where('id', $res['parent_id'])->first();
        }
        $path = ($parent ? $parent->path . '.' : '') . $res['id'];
        $updateData['path'] = $path;

        self::where('id', $res['id'])->update($updateData);
        $res->path = $path;

        return $res;
    }


}
