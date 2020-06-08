<?php

namespace App\Components\GeoIP;

use App\Components\GeoIP\Contracts\GeoServiceInterface;

/**
 * Class GeoIPAdapter.
 *
 * @package App\Components\GeoIP
 */
class GeoIPAdapter
{
    /**
     * @var GeoServiceInterface
     */
    protected $service;

    /**
     * GeoIPAdapter constructor.
     *
     * @param GeoServiceInterface $service
     */
    public function __construct(GeoServiceInterface $service)
    {
        $this->service = $service;
    }

    public function location($ip)
    {
        return $this->service->get($ip);
    }
}