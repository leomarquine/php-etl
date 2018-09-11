<?php

namespace Marquine\Etl\Database;

use InvalidArgumentException;

class Manager
{
    /**
     * The Connection Factory.
     *
     * @var \Marquine\Etl\Database\ConnectionFactory
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
     * @param  \Marquine\Etl\Database\ConnectionFactory  $factory
     * @return void
     */
    public function __construct(ConnectionFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Register a connection.
     *
     * @param  array  $config
     * @param  string  $name
     * @return void
     */
    public function addConnection($config, $name = 'default')
    {
        $this->config[$name] = $config;
    }

    /**
     * Get a connection instance.
     *
     * @param  string  $name
     * @return \Marquine\Etl\Database\Connection
     */
    protected function getConnection($name)
    {
        if (! isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Make a connection instance.
     *
     * @param  string  $name
     * @return \Marquine\Etl\Database\Connection
     *
     * @throws \InvalidArgumentException
     */
    protected function makeConnection($name)
    {
        if (isset($this->config[$name])) {
            return $this->factory->make($this->config[$name]);
        }

        throw new InvalidArgumentException("Database [{$name}] not configured.");
    }

    /**
     * Get a new query builder instance.
     *
     * @param  string  $connection
     * @return \Marquine\Etl\Database\Query
     */
    public function query($connection)
    {
        return new Query($this->getConnection($connection));
    }

    /**
     * Get a new statement builder instance.
     *
     * @param  string  $connection
     * @return \Marquine\Etl\Database\Statement
     */
    public function statement($connection)
    {
        return new Statement($this->getConnection($connection));
    }

    /**
     * Get a new transaction instance.
     *
     * @param  string  $connection
     * @return \Marquine\Etl\Database\Transaction
     */
    public function transaction($connection)
    {
        return new Transaction($this->getConnection($connection));
    }

    /**
     * Get the pdo connection instance.
     *
     * @param  string  $connection
     * @return \PDO
     */
    public function pdo($connection)
    {
        return $this->getConnection($connection);
    }
}
