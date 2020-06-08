<?php

namespace App\Models\Contracts;

use App\Components\Eloquent\Builder;

/**
 * User: Sergei
 * Date: 10.02.20
 */
interface Filter
{
    /**
     * @param Builder $builder
     * @param mixed $value
     * @param array|null $data
     * @return mixed
     */
    public static function apply(Builder $builder, $value, $data = null): Builder;
}