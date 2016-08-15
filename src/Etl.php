<?php

namespace Marquine\Etl;

use Marquine\Etl\Database\Connectors\ConnectionFactory;

class Etl
{
    /**
     * Global configuration array.
     *
     * @var array
     */
    protected static $config;

    /**
     * Global database connections.
     *
     * @var array
    */
    protected static $connections = [];

    /**
     * Set the global configuration or get a config item.
     *
     * @param mixed $config
     * @param mixed $default
     * @return mixed
     */
    public static function config($config, $default = null)
    {
        if (is_string($config)) {
            foreach (explode('.', $config) as $segment) {
                $value = static::$config[$segment];
            }

            return $value ?: $default;
        }

        static::$config = $config;

        if (isset($config['connections'])) {
            foreach ($config['connections'] as $name => $connection) {
                static::addConnection($connection, $name);
            }
        }
    }

    /**
     * Add a database connection.
     *
     * @param array $connection
     * @param string $name
     * @return void
     */
    public static function addConnection($connection, $name = 'default')
    {
        static::$connections[$name] = ConnectionFactory::make($connection);
    }

    /**
    * Get a database connection.
    *
    * @param string $name
    * @return \Marquine\Etl\Database\Connection
    */
    public static function connection($name = 'default')
    {
        return static::$connections[$name];
    }
}
