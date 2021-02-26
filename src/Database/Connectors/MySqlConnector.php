<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

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
        // All these if, empty, are here to clean the legacy code before the fork. See the git history.
        $host = array_key_exists('host', $config) ? $config['host'] : null;
        $socket = array_key_exists('unix_socket', $config) ? $config['unix_socket'] : null;
        $port = array_key_exists('port', $config) ? $config['port'] : null;
        $database = array_key_exists('database', $config) ? $config['database'] : null;

        $dsn = [];

        if (null !== $socket) {
            $dsn['unix_socket'] = $socket;
        }

        if (null !== $host && null == $socket) {
            $dsn['host'] = $host;
        }

        if (null !== $port && null === $socket) {
            $dsn['port'] = $port;
        }

        if (null !== $database) {
            $dsn['dbname'] = $database;
        }

        return 'mysql:' . http_build_query($dsn, '', ';');
    }

    /**
     * Handle tasks after connection.
     */
    protected function afterConnection(\PDO $connection, array $config): void
    {
        // All these if are here to clean the legacy code before the fork. See the git history.
        $database = array_key_exists('database', $config) ? $config['database'] : null;
        $charset = array_key_exists('charset', $config) ? $config['charset'] : null;
        $collation = array_key_exists('collation', $config) ? $config['collation'] : null;
        $timezone = array_key_exists('timezone', $config) ? $config['timezone'] : null;

        if (null !== $database) {
            $connection->exec("use `$database`");
        }

        if (null !== $charset) {
            $statement = "set names '$charset'";

            if (null !== $collation) {
                $statement .= " collate '$collation'";
            }

            $connection->prepare($statement)->execute();
        }

        if (null !== $timezone) {
            $connection->prepare("set time_zone = '$timezone'")->execute();
        }
    }
}
