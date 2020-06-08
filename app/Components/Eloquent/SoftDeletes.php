<?php
/**
 *
 * User: sergei
 * Date: 01.08.18
 * Time: 21:59
 */

namespace App\Components\Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes as SoftDeletesTrait;

trait SoftDeletes
{
    use SoftDeletesTrait;
    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingScope);
    }
}