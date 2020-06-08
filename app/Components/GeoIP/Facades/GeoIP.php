<?php

namespace App\Components\GeoIP\Facades;

use Illuminate\Support\Facades\Facade;
use App\Components\GeoIP\GeoIpManager;

/**
 * Class GeoIP.
 *
 * @package App\Components\GeoIP\Facades
 */
class GeoIP extends Facade
{
    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return GeoIpManager::class;
    }
}