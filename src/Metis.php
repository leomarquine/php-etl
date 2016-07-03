<?php

namespace Marquine\Metis;

use Doctrine\DBAL\DriverManager;

class Metis
{
    /**
     * The items contained in the transformation.
     *
     * @var array
     */
    protected $items;

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
     * @param  mixed $config
     * @param  mixed $default
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
    }

    /**
     * Add a database connection.
     *
     * @param  array  $params
     * @param  string $name
     * @return void
     */
    public static function addConnection($params, $name = 'default')
    {
        static::$connections[$name] = DriverManager::getConnection($params);
    }

    /**
    * Get a database connection.
    *
    * @param  string $connection
    * @return \Doctrine\DBAL\Connection
    */
    public static function connection($name = 'default')
    {
        return static::$connections[$name];
    }

    /**
    * Create a new Metis instance.
    *
    * @return Metis
    */
    public static function start()
    {
        return new Metis;
    }

    /**
     * Get current transformation items.
     *
     * @return array
     */
    public function get()
    {
        return $this->items;
    }

    /**
     * Utilities.
     *
     * @param  string $type
     * @param  array  $options
     * @return $this
     */
    public function utility($type, $options)
    {
        $utility = $this->factory($type, 'utilities', $options);

        $utility->handle();

        return $this;
    }

    /**
     * Extract data from the given source.
     *
     * @param  string $type
     * @param  mixed  $source
     * @param  array  $columns
     * @param  array  $options
     * @return $this
     */
    public function extract($type, $source, $columns = null, $options = [])
    {
        $extractor = $this->factory($type, 'extractors', $options);

        $this->items = $extractor->extract($source, $columns);

        return $this;
    }

    /**
     * Execute a transformation.
     *
     * @param  string $type
     * @param  mixed  $columns
     * @param  array  $options
     * @return $this
     */
    public function transform($type, $columns = null, $options = [])
    {
        $transformer = $this->factory($type, 'transformers', $options);

        $this->items = $transformer->transform($this->items, $columns);

        return $this;
    }

    /**
     * Load data to the given destination.
     *
     * @param  string $type
     * @param  string $destination
     * @param  array  $options
     * @return $this
     */
    public function load($type, $destination, $options = [])
    {
        $loader = $this->factory($type, 'loaders', $options);

        $loader->load($destination, $this->items);

        return $this;
    }

    /**
     * Create an instance of the given class.
     *
     * @param  string $class
     * @param  string $category
     * @param  array  $options
     * @return mixed
     */
    protected function factory($class, $category, $options)
    {
        $aliases = [
            'extractors' => [
                'array' => 'ArrayData',
            ]
        ];

        if (! class_exists($class)) {

            if (isset($aliases[$category][$class])) {
                $class = $aliases[$category][$class];
            }

            $class = __NAMESPACE__ . '\\' . ucwords($category) . '\\' . $class;
        }

        return new $class($options);
    }
}
