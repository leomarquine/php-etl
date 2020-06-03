<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

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
        // All these if are here to clean the legacy code before the fork. See the git history.
        $host = array_key_exists('host', $config) ? $config['host'] : null;
        $port = array_key_exists('port', $config) ? $config['port'] : null;
        $database = array_key_exists('database', $config) ? $config['database'] : null;

        $dsn = [];

        if (null !== $host) {
            $dsn['host'] = $host;
        }

        if (null !== $port) {
            $dsn['port'] = $port;
        }

        if (null !== $database) {
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
        // All these if are here to clean the legacy code before the fork. See the git history.
        $charset = array_key_exists('charset', $config) ? $config['charset'] : null;
        $timezone = array_key_exists('timezone', $config) ? $config['timezone'] : null;
        $schema = array_key_exists('schema', $config) ? $config['schema'] : null;
        $application_name = array_key_exists('application_name', $config) ? $config['application_name'] : null;

        if (null !== $charset) {
            $connection->prepare("set names '$charset'")->execute();
        }

        if (null !== $timezone) {
            $connection->prepare("set time zone '$timezone'")->execute();
        }

        if (null !== $schema) {
            $schema = $this->formatSchema($schema);

            $connection->prepare("set search_path to $schema")->execute();
        }

        if (null !== $application_name) {
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
