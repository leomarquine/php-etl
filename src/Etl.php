<?php

namespace Marquine\Etl;

use Marquine\Etl\Support\Database\Connectors\ConnectionFactory;

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
            $value = static::$config;

            foreach (explode('.', $config) as $segment) {
                $value = isset($value[$segment]) ? $value[$segment] : null;
            }

            return $value ?: $default;
        }

        static::$config = $config;
    }

    /**
    * Get a database connection.
    *
    * @param string $connection
    * @return \Marquine\Etl\Database\Connection
    */
    public static function database($connection = 'default')
    {
        if ($connection == 'default') {
            $connection = static::config('database.default');
        }

        if (! isset(static::$connections[$connection])) {
            static::$connections[$connection] = ConnectionFactory::make(
                static::config("database.connections.$connection")
            );
        }

        return static::$connections[$connection];
    }
}
