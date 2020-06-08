<?php

namespace App\Models\Filters\User;

use App\Components\Eloquent\Builder;
use App\Models\Contracts\Filter;
use App\Models\Filters\BaseFilter;

/**
 * User: Sergei
 * Date: 30.03.20
 */
class SearchFilter extends BaseFilter implements Filter
{
    /**
     * Search user by First/Last Name or Email
     *
     * @param Builder $builder
     * @param mixed $value
     * @param null $data
     *
     * @return Builder
     */
    public static function apply(Builder $builder, $value, $data = null): Builder {

        $builder->where(function($query) use ($value){
            $query->where('x.first_name', 'LIKE', "%$value%")
                ->orWhere('x.last_name', 'LIKE', "%$value%")
                ->orWhere('x.email', 'LIKE', "%$value%");
        });

        return $builder;
    }
}