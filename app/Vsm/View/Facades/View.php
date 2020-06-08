<?php
/**
 * User: Sergei
 * Date: 19.06.19
 */

namespace App\Vsm\View\Facades;

use Illuminate\Support\Facades\Facade;

class View extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vsmview';
    }
}