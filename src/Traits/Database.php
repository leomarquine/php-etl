<?php

namespace Marquine\Etl\Traits;

use Marquine\Etl\Etl;
use Marquine\Etl\Database\Connectors\ConnectionFactory;


trait Database
{
    /**
     * Database connection.
     *
     * @var \Marquine\Etl\Database\Connection
     */
    protected $db;

    /**
     * Validate the given source.
     *
     * @param string $source
     * @return string
     */
    protected function connect($connection)
    {
        if ($connection == 'default') {
            $connection = Etl::config('database.default');
        }

        $connection = Etl::config("database.connections.$connection");

        $this->db = ConnectionFactory::make($connection);
    }
}
