<?php

namespace Marquine\Etl\Database;

use InvalidArgumentException;

class ConnectionFactory
{
    /**
    * Make a new database connection.
    *
    * @param  array  $config
    * @return \Marquine\Etl\Database\Connection
    */
    public function make($config)
    {
        if (! isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }

        $pdo = $this->getConnector($config['driver'])->connect($config);

        return new Connection($pdo);
    }

    /**
    * Get the database connector.
    *
    * @param  string  $driver
    * @return \Marquine\Etl\Database\Connectors\Connector
    */
    protected function getConnector($driver)
    {
        switch ($driver) {
            case 'sqlite':
                return new Connectors\SqliteConnector;
            case 'mysql':
                return new Connectors\MySqlConnector;
            case 'pgsql':
                return new Connectors\PostgresConnector;
            case 'mssql':
                return new Connectors\MsSqlConnector;
        }

        throw new InvalidArgumentException("Unsupported driver: $driver");
    }
}
