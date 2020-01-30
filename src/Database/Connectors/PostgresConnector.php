<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Database\Connectors;

class PostgresConnector extends Connector
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
     * @return void
     */
    protected function afterConnection(\PDO $connection, array $config)
    {
        extract($config, EXTR_SKIP);

        // @TODO refactor this code as the use of extract() is a bad practice, prone to create bugs

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
     *
     * @return string
     */
    protected function formatSchema($schema)
    {
        if (is_string($schema)) {
            $schema = [$schema];
        }

        return implode(', ', $schema);
    }
}
