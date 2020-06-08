<?php

namespace App\Models\Filters;

use Illuminate\Support\Facades\DB;

/**
 * User: Sergei
 * Date: 10.02.20
 */
class BaseFilter
{
    /**
     * @param $value
     * @return \Illuminate\Database\Query\Expression
     */
    public static function quote($value)
    {
        return DB::Raw(DB::connection()->getPdo()->quote($value));
    }
}