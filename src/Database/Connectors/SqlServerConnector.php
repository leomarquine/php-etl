<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Database\Connectors;

class SqlServerConnector extends Connector
{
    /**
     * Connect to a database.
     */
    public function connect(array $config): \PDO
    {
        $dsn = $this->getDsn($config);

        $connection = $this->createConnection($dsn, $config);

        $this->afterConnection($connection, $config);

        return $connection;
    }

    /**
     * Get the DSN string.
     *
     * @return string
     */
    protected function getDsn(array $config)
    {
        extract($config, EXTR_SKIP);

        // @TODO refactor this code as the use of extract() is a bad practice, prone to create bugs

        $dsn = [];

        if (isset($host) && !isset($unix_socket)) {
            $dsn['host'] = $host;
        }

        if (isset($port) && !isset($unix_socket)) {
            $dsn['port'] = $port;
        }

        if (isset($database)) {
            $dsn['dbname'] = $database;
        }

        return 'dblib:' . http_build_query($dsn, '', ';');
    }

    /**
     * Handle tasks after connection.
     *
     * @return void
     */
    protected function afterConnection(\PDO $connection, array $config)
    {
        extract($config, EXTR_SKIP);

        // @TODO refactor this code as the use of extract() is a bad practice, prone to create bugs

        if (isset($database)) {
            $connection->exec("USE $database");
        }
    }
}
