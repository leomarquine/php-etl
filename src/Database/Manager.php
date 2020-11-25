<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Database;

class Manager
{
    /**
     * The Connection Factory.
     *
     * @var \Wizaplace\Etl\Database\ConnectionFactory
     */
    protected $factory;

    /**
     * The connections configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The connections instances.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * Create a new database manager instance.
     *
     * @param \Wizaplace\Etl\Database\ConnectionFactory $factory
     *
     * @return void
     */
    public function __construct(ConnectionFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Register a connection.
     */
    public function addConnection(array $config, string $name = 'default'): void
    {
        $this->config[$name] = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get a connection instance.
     */
    protected function getConnection(string $name): \PDO
    {
        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Make a connection instance.
     *
     * @throws \InvalidArgumentException
     */
    protected function makeConnection(string $name): \PDO
    {
        if (isset($this->config[$name])) {
            return $this->factory->make($this->config[$name]);
        }

        throw new \InvalidArgumentException("Database [{$name}] not configured.");
    }

    /**
     * Get a new query builder instance.
     *
     * @return \Wizaplace\Etl\Database\Query
     */
    public function query(string $connection): Query
    {
        return new Query($this->getConnection($connection));
    }

    /**
     * Get a new statement builder instance.
     *
     * @return \Wizaplace\Etl\Database\Statement
     */
    public function statement(string $connection): Statement
    {
        return new Statement($this->getConnection($connection));
    }

    /**
     * Get a new transaction instance.
     *
     * @return \Wizaplace\Etl\Database\Transaction
     */
    public function transaction(string $connection): Transaction
    {
        return new Transaction($this->getConnection($connection));
    }

    /**
     * Get the pdo connection instance.
     */
    public function pdo(string $connection): \PDO
    {
        return $this->getConnection($connection);
    }
}
