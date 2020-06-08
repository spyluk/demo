<?php
/**
 * 
 * User: sergei
 * Date: 02.10.18
 * Time: 16:16
 */

namespace App\Providers;

use Illuminate\Database\DatabaseServiceProvider as ParentDatabaseServiceProvider;
use App\Components\Database\DatabaseManager;

use App\Components\Database\Connectors\ConnectionFactory;

class DatabaseServiceProvider extends ParentDatabaseServiceProvider
{
    /**
     * Register the primary database bindings.
     *
     * @return void
     */
    protected function registerConnectionServices()
    {
        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });

        // The database manager is used to resolve various connections, since multiple
        // connections might be managed. It also implements the connection resolver
        // interface which may be used by other components requiring connections.
        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });

        $this->app->bind('db.connection', function ($app) {
            return $app['db']->connection();
        });
    }
}