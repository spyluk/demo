<?php

namespace App\Components\GeoIP;

use Illuminate\Contracts\Foundation\Application;
use App\Components\GeoIP\Services\MaxmindApi;
use App\Components\GeoIP\Services\MaxmindDatabase;
use InvalidArgumentException;

/**
 * Class GeoIp.
 *
 * @package App\Components\GeoIP
 */
class GeoIpManager
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * GeoIpManager constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Returns a GEO IP instance.
     *
     * @param string|null $name
     * @return mixed
     */
    public function service($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();
        return $this->services[$name] = $this->get($name);
    }

    /**
     * Returns the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['geoip.default'];
    }

    /**
     * Attempt to get the disk.
     *
     * @param string $name
     * @return mixed
     */
    protected function get($name)
    {
        return isset($this->services[$name]) ? $this->services[$name] : $this->resolve($name);
    }

    /**
     * Resolve the given driver.
     *
     * @param string $name
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config         = $this->getConfig($name);
        $serviceMethod  = 'create' . ucfirst(camel_case($config['driver'])) . 'Service';
        if (method_exists($this, $serviceMethod)) {
            return $this->{$serviceMethod}($config);
        } else {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }
    }

    /**
     * Returns the GEO connection configuration.
     *
     * @param string $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["geoip.services.{$name}"];
    }

    /**
     * @param array $config
     * @return GeoIPAdapter
     */
    protected function createMaxmindApiService(array $config)
    {
        $service = new MaxmindApi($config);
        return new GeoIPAdapter($service);
    }

    /**
     * @param array $config
     * @return GeoIPAdapter
     */
    protected function createMaxmindDatabaseService(array $config)
    {
        $service = new MaxmindDatabase($config);
        return new GeoIPAdapter($service);
    }
}