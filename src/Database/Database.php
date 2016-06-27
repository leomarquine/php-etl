<?php

namespace Marquine\Metis\Database;

use InvalidArgumentException;

class Database
{
    /**
    * Connections list.
    *
    * @var array
    */
    protected $connections = [];

    /**
    * Add a connection.
    *
    * @param  array  $config
    * @param  string $name
    * @return void
    */
    public function addConnection($config, $name)
    {
        $this->connections[$name] = $this->connection($config);
    }

    /**
    * Get a connection.
    *
    * @param  string $name
    * @return \Marquine\Metis\Database\Connection\Connection
    */
    public function getConnection($name)
    {
        return $this->connections[$name];
    }

    /**
    * Get a new Connection instance based on config's driver.
    *
    * @param  array $config
    * @return \Marquine\Metis\Database\Connection\Connection
    */
    protected function connection($config)
    {
        switch ($config['driver']) {
            case 'sqlite':
            case 'sqlite3':
            case 'pdo_sqlite':
                return new Connection\SQLiteConnection($config);

            case 'pgsql':
            case 'postgres':
            case 'pdo_pgsql':
            case 'postgresql':
                return new Connection\PostgresConnection($config);
        }

        throw new InvalidArgumentException("Unsupported driver: {$config['driver']}");
    }
}
