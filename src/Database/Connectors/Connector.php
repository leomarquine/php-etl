<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Database\Connectors;

abstract class Connector
{
    /**
     * The default PDO connection options.
     *
     * @var array
     */
    public const PDO_OPTIONS = [
        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
        \PDO::ATTR_EMULATE_PREPARES => true,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ];

    /**
     * Connect to a database.
     */
    abstract public function connect(array $config): \PDO;

    /**
     * Create a new PDO connection.
     */
    protected function createConnection(string $dsn, array $config): \PDO
    {
        $username = $config['username'] ?? null;
        $password = $config['password'] ?? null;

        return new \PDO($dsn, $username, $password, $this->getOptions($config));
    }

    /**
     * Get the PDO options based on the configuration.
     */
    public function getOptions(array $config): array
    {
        $options = $config['options'] ?? [];

        return array_diff_key(static::PDO_OPTIONS, $options) + $options;
    }
}
