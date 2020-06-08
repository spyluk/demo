<?php

namespace App\Components\Database\Connectors;

use Illuminate\Database\Connectors\ConnectionFactory as ParentConnectionFactory;
use App\Components\Database\MySqlConnection;
use Illuminate\Database\Connection;

/**
 *
 * User: sergei
 * Date: 02.10.18
 * Time: 16:32
 */
class ConnectionFactory extends ParentConnectionFactory
{
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($resolver = Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);
        }

        switch ($driver) {
            case 'mysql':
                return new MySqlConnection($connection, $database, $prefix, $config);
        }

        throw new \InvalidArgumentException("Unsupported driver [{$driver}]");
    }
}