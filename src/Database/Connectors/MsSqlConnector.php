<?php

namespace Marquine\Etl\Database\Connectors;

class MsSqlConnector extends Connector
{
    /**
    * Connect to a database.
    *
    * @param  array  $config
    * @return \PDO
    */
    public function connect($config)
    {
        $dsn = $this->getDsn($config);

        $connection = $this->createConnection($dsn, $config);

        $this->afterConnection($connection, $config);

        return $connection;
    }

    /**
     * Get the DSN string.
     *
     * @param  array  $config
     * @return string
     */
    public function getDsn($config)
    {
        extract($config, EXTR_SKIP);

        $dsn = [];

        if (isset($host) && ! isset($unix_socket)) {
            $dsn['host'] = $host;
        }

        if (isset($port) && ! isset($unix_socket)) {
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
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    public function afterConnection($connection, $config)
    {
        extract($config, EXTR_SKIP);

        if (isset($database)) {
            $connection->exec("USE $database");
        }

    }
}
