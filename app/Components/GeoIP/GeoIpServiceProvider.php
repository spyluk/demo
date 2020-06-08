<?php

namespace App\Components\GeoIP;

use Illuminate\Support\ServiceProvider;

/**
 * Class GeoIpServiceProvider.
 *
 * @package App\Components\GeoIP
 */
class GeoIpServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerGeoIP();
    }

    /**
     * Register the GEO IP.
     */
    protected function registerGeoIP()
    {
        $this->registerManager();

        $this->app->singleton('geo-ip.service', function() {
            return $this->app[GeoIpManager::class]->service($this->getDefaultDriver());
        });
    }

    /**
     * Register the GEO IP manager.
     */
    protected function registerManager()
    {
        $this->app->singleton(GeoIpManager::class, function() {
            return new GeoIpManager($this->app);
        });
    }

    /**
     * Returns the default GEO driver.
     *
     * @return string
     */
    protected function getDefaultDriver()
    {
        return $this->app['config']['geoip.default'];
    }
}