<?php
/**
 *
 * User: sergei
 * Date: 02.10.18
 * Time: 16:20
 */

namespace App\Components\Database;

use Illuminate\Database\DatabaseManager as ParentDatabaseManager;
use App\Components\Database\Connectors\ConnectionFactory;

class DatabaseManager extends ParentDatabaseManager
{
    public function __construct($app, ConnectionFactory $connector)
    {
        parent::__construct($app, $connector);

    }
}