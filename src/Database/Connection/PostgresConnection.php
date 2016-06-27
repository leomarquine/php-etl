<?php

namespace Marquine\Metis\Database\Connection;

class PostgresConnection extends Connection
{
    /**
    * Create a new PostgresConnection instance.
    *
    * @param  array $config
    * @return void
    */
    public function __construct($config)
    {
        $options = $this->getOptions($config);

        $dsn = $this->makeDsn($config);

        $this->connect($dsn, $config, $options);

        if (isset($config['charset'])) {
            $charset = $config['charset'];
            $this->connection->prepare("set names '$charset'")->execute();
        }

        if (isset($config['timezone'])) {
            $timezone = $config['timezone'];
            $this->connection->prepare("set time zone '$timezone'")->execute();
        }

        if (isset($config['schema'])) {
            $schema = $this->schema($config['schema']);
            $this->connection->prepare("set search_path to $schema")->execute();
        }

        if (isset($config['application_name'])) {
            $applicationName = $config['application_name'];
            $this->connection->prepare("set application_name to '$applicationName'")->execute();
        }
    }

    /**
     * Make a DSN string from a configuration.
     *
     * @param  array  $config
     * @return string
     */
    protected function makeDsn($config)
    {
        extract($config, EXTR_SKIP);

        $dsn = ["dbname=$database"];

        if (isset($host)) {
            $dsn[] = "host=$host";
        }

        if (isset($port)) {
            $dsn[] = "port=$port";
        }

        if (isset($sslmode)) {
            $dsn[] = "sslmode=$sslmode";
        }

        return 'pgsql:' . implode(';', $dsn);
    }

    /**
     * Format the schema.
     *
     * @param  array|string $schema
     * @return string
     */
    protected function schema($schema)
    {
        if (is_array($schema)) {
            $schema = implode(', ', $schema);
        }

        return $schema;
    }
}
