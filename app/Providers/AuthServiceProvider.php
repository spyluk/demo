<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\OToken;
use App\Models\OAuthCode;
use App\Models\OClient;
use App\Models\OPersonalAccessClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        \Illuminate\Support\Facades\Auth::provider('customeloquentuserprovider', function($app, array $config) {
            return new Auth\CustomEloquentUserProvider($app['hash'], $config['model']);
        });

        Passport::cookie(config('auth.cookie'));
        Passport::useClientModel(OClient::class);
        Passport::useTokenModel(OToken::class);
        Passport::useAuthCodeModel(OAuthCode::class);
        Passport::usePersonalAccessClientModel(OPersonalAccessClient::class);

        Passport::tokensExpireIn(Carbon::now()->addDays(5));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(10));
    }
}
