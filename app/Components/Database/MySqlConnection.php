<?php
namespace App\Components\Database;

use App\Components\Database\Schema\Blueprint;
use Illuminate\Database\MySqlConnection as ParentMySqlConnection;
use Illuminate\Database\Schema\MySqlBuilder;
use App\Components\Database\Query\Builder as QueryBuilder;
/**
 * Class MySqlConnection
 *
 * @package App\Database
 */
class MySqlConnection extends ParentMySqlConnection
{
    /**
     * Get a schema builder instance for the connection.
     * Set {@see \App\Components\Database\Schema\Blueprint} for connection
     * Blueprint resolver
     *
     * @return \Illuminate\Database\Schema\MySqlBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        $builder = new MySqlBuilder($this);
        $builder->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });
        return $builder;
    }

    public function query()
    {
        return new QueryBuilder(
            $this,
            $this->getQueryGrammar(),
            $this->getPostProcessor()
        );
    }
}