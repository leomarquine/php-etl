<?php

namespace Marquine\Etl\Database\Connectors;

use PDO;

abstract class Connector
{
    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    /**
     * Connect to a database.
     *
     * @param  array  $config
     * @return \PDO
     */
    abstract public function connect($config);

    /**
     * Create a new PDO connection.
     *
     * @param  string  $dsn
     * @param  array  $config
     * @return \PDO
     */
    public function createConnection($dsn, array $config)
    {
        $username = $config['username'] ?? null;

        $password = $config['password'] ?? null;

        return new PDO($dsn, $username, $password, $this->getOptions($config));
    }

    /**
     * Get the PDO options based on the configuration.
     *
     * @param  array  $config
     * @return array
     */
    public function getOptions(array $config)
    {
        $options = $config['options'] ?? [];

        return array_diff_key($this->options, $options) + $options;
    }
}
