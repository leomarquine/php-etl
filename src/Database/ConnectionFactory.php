<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Database;

use Wizaplace\Etl\Database\Connectors\Connector;

class ConnectionFactory
{
    /**
     * Make a new database connection.
     */
    public function make(array $config): \PDO
    {
        if (!isset($config['driver'])) {
            throw new \InvalidArgumentException('A driver must be specified.');
        }

        return $this->getConnector($config['driver'])->connect($config);
    }

    /**
     * Get the database connector.
     */
    protected function getConnector(string $driver): Connector
    {
        switch ($driver) {
            case 'sqlite':
                return new Connectors\SqliteConnector();
            case 'mysql':
                return new Connectors\MySqlConnector();
            case 'pgsql':
                return new Connectors\PostgresConnector();
            case 'sqlsrv':
                return new Connectors\SqlServerConnector();
        }

        throw new \InvalidArgumentException("Unsupported SQL driver: $driver");
    }
}
