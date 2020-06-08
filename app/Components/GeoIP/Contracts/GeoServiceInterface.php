<?php

namespace App\Components\GeoIP\Contracts;

/**
 * Interface GeoServiceInterface.
 *
 * @package App\Components\GeoIP\Contracts
 */
interface GeoServiceInterface
{
    public function get($ip);
}