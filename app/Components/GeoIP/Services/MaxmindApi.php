<?php

namespace App\Components\GeoIP\Services;

use App\Components\GeoIP\Contracts\GeoServiceInterface;
use GeoIp2\WebService\Client;

/**
 * Class MaxmindApi.
 *
 * @package App\Components\GeoIP\Services
 */
class MaxmindApi implements GeoServiceInterface
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
        $this->client = new Client($config['user_id'], $config['license_key']);
    }

    /**
     * @inheritdoc
     */
    public function get($ip)
    {
        return $this->client->city($ip);
    }
}