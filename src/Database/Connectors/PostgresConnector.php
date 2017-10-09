<?php

namespace Marquine\Etl\Database\Connectors;

use PDO;

class PostgresConnector extends Connector
{
    /**
    * Connect to a database.
    *
    * @param array $config
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
     * @param array $config
     * @return string
     */
    public function getDsn($config)
    {
        extract($config, EXTR_SKIP);

        $dsn = [];

        if (isset($host)) {
            $dsn['host'] = $host;
        }

        if (isset($port)) {
            $dsn['port'] = $port;
        }

        if (isset($database)) {
            $dsn['dbname'] = $database;
        }

        return 'pgsql:' . http_build_query($dsn, '', ';');
    }

    /**
     * Handle tasks after connection.
     *
     * @param \PDO $connection
     * @param array $config
     * @return void
     */
    public function afterConnection($connection, $config)
    {
        extract($config, EXTR_SKIP);

        if (isset($charset)) {
            $connection->prepare("set names '$charset'")->execute();
        }

        if (isset($timezone)) {
            $connection->prepare("set time zone '$timezone'")->execute();
        }

        if (isset($schema)) {
            $schema = $this->formatSchema($schema);

            $connection->prepare("set search_path to $schema")->execute();
        }

        if (isset($application_name)) {
            $connection->prepare("set application_name to '$application_name'")->execute();
        }
    }

    /**
     * Format the schema.
     *
     * @param array|string $schema
     * @return string
     */
    public function formatSchema($schema)
    {
        if (is_string($schema)) {
            $schema = [$schema];
        }

        return implode(', ', $schema);
    }
}
