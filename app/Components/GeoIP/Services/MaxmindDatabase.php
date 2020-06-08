<?php

namespace App\Components\GeoIP\Services;

use App\Components\GeoIP\Contracts\GeoServiceInterface;
use GeoIp2\Database\Reader;

/**
 * Class MaxmindApi.
 *
 * @package App\Components\GeoIP\Services
 */
class MaxmindDatabase implements GeoServiceInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * MaxmindApi constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->client = new Reader($config['database_path']);
    }

    /**
     * @inheritdoc
     */
    public function get($ip)
    {
        return $this->client->city($ip);
    }
}