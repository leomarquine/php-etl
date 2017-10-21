<?php

namespace Marquine\Etl;

class Etl
{
    /**
     * Global configuration array.
     *
     * @var array
     */
    protected static $config = [];

    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @return mixed
     */
    public static function get($key)
    {
        $value = static::$config;

        foreach (explode('.', $key) as $segment) {
            $value = isset($value[$segment]) ? $value[$segment] : null;
        }

        return $value;
    }

    /**
     * Set a given configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public static function set($key, $value)
    {
        $keys = explode('.', $key);

        $array = &static::$config;

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * Add a database connection.
     *
     * @param  array  $config
     * @param  string  $name
     * @return void
     */
    public static function addConnection($config, $name = 'default')
    {
        static::set("connections.$name", $config);
    }
}
