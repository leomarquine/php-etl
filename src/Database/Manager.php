<?php

namespace Marquine\Etl\Database;

use Marquine\Etl\Etl;

class Manager
{
    /**
     * Database connections.
     *
     * @var array
     */
    protected static $connections = [];

    /**
     * Get the specified database connection.
     *
     * @param  string  $name
     * @param  \Marquine\Etl\Database\ConnectionFactory  $factory
     * @return \Marquine\Etl\Database\Connection
     */
    public static function connection($name, $factory = null)
    {
        $factory = $factory ?: new ConnectionFactory;

        if (! isset(static::$connections[$name])) {
            static::$connections[$name] = $factory->make(Etl::get("connections.$name"));
        }

        return static::$connections[$name];
    }
}
