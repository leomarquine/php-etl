<?php

namespace Marquine\Etl\Database\Connectors;

use InvalidArgumentException;
use Marquine\Etl\Database\Connection;

class ConnectionFactory
{
    /**
    * Make a new database connection.
    *
    * @param array $config
    * @return \Marquine\Etl\Database\Connection
    */
    public static function make($config)
    {
        if (! isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }

        $pdo = static::selectConnector($config['driver'])
                     ->connect($config);

        return new Connection($pdo);
    }

    /**
    * Select the database connector.
    *
    * @param string driver
    * @return \Marquine\Etl\Database\Connectors\Connector
    */
    public static function selectConnector($driver)
    {
        switch ($driver) {
            case 'sqlite':
                return new SqliteConnector;
            case 'mysql':
                return new MySqlConnector;
        }

        throw new InvalidArgumentException('The specified driver is not valid.');
    }
}
