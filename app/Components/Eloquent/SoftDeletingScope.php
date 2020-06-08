<?php
/**
 *
 * User: sergei
 * Date: 01.08.18
 */

namespace App\Components\Eloquent;

use Illuminate\Database\Eloquent\SoftDeletingScope as EloquentSoftDeletingScope;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Model;

class SoftDeletingScope extends EloquentSoftDeletingScope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $alias = explode(' as ', $builder->getQuery()->from);

        $field = isset($alias[1]) ? $alias[1] . '.' . $model->getDeletedAtColumn() :
            $model->getQualifiedDeletedAtColumn();

        $builder->where($field, '=', 0);
    }
}