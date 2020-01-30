<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Database\Connectors;

class MySqlConnector extends Connector
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
     */
    protected function getDsn(array $config): string
    {
        extract($config, EXTR_SKIP);

        // @TODO refactor this code as the use of extract() is a bad practice, prone to create bugs

        $dsn = [];

        if (!empty($unix_socket)) {
            $dsn['unix_socket'] = $unix_socket;
        }

        if (isset($host) && empty($unix_socket)) {
            $dsn['host'] = $host;
        }

        if (isset($port) && empty($unix_socket)) {
            $dsn['port'] = $port;
        }

        if (isset($database)) {
            $dsn['dbname'] = $database;
        }

        return 'mysql:' . http_build_query($dsn, '', ';');
    }

    /**
     * Handle tasks after connection.
     */
    protected function afterConnection(\PDO $connection, array $config): void
    {
        extract($config, EXTR_SKIP);

        // @TODO refactor this code as the use of extract() is a bad practice, prone to create bugs

        if (isset($database)) {
            $connection->exec("use `$database`");
        }

        if (isset($charset)) {
            $statement = "set names '$charset'";

            if (isset($collation)) {
                $statement .= " collate '$collation'";
            }

            $connection->prepare($statement)->execute();
        }

        if (isset($timezone)) {
            $connection->prepare("set time_zone = '$timezone'")->execute();
        }
    }
}
