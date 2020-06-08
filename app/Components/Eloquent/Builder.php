<?php
/**
 *
 * User: sergei
 * Date: 21.02.19
 */

namespace App\Components\Eloquent;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;
use App\Components\Database\Pagination\LengthAwarePaginator;
use Illuminate\Container\Container;

class Builder extends BaseBuilder
{
    /**
     * Create a new length-aware paginator instance.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }
}