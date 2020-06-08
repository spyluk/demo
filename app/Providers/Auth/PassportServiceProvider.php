<?php
/**
 * 
 * User: sergei
 * Date: 25.11.18
 * Time: 16:20
 */

namespace App\Providers\Auth;

use Laravel\Passport\PassportServiceProvider as DefaultPassportServiceProvider;
use Laravel\Passport\Bridge;
use App\Passport\Bridge\AccessTokenRepository;
use League\OAuth2\Server\AuthorizationServer;

class PassportServiceProvider extends DefaultPassportServiceProvider
{
    /**
     * Make the authorization service instance.
     *
     * @return \League\OAuth2\Server\AuthorizationServer
     */
    public function makeAuthorizationServer()
    {
        return new AuthorizationServer(
            $this->app->make(Bridge\ClientRepository::class),
            $this->app->make(AccessTokenRepository::class),
            $this->app->make(Bridge\ScopeRepository::class),
            $this->makeCryptKey('private'),
            app('encrypter')->getKey()
        );
    }
}