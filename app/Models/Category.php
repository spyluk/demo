<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\Eloquent\SoftDeletes;
use App\Models\Traits\BaseTrait;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use SoftDeletes;
    use BaseTrait {
        create as createParent;
    }

    /**
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'site_id',
        'type_id',
        'name',
        'level',
        'path',
        'system'
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
        self::where('id', $res['id'])->update(['path' => $path]);
        $res->path = $path;

        return $res;
    }

    /**
     * @param $site_id
     * @param string $code
     * @return Category
     */
    public function getCategoryByCode($site_id, string $code/*, int $type_id*/)
    {
        return $this->getInitDbByFields()
            ->select('x.*')
            ->leftJoin('category_codes as cc', 'cc.category_id', '=', 'x.id')
            ->where('x.site_id', '=', DB::raw($site_id))
//            ->where('c.type_id', '=', DB::raw($type_id))
            ->where('cc.code', '=', DB::raw("'$code'"))
            ->first();
    }
}
