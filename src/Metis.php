<?php

namespace Marquine\Metis;

class Metis
{
    /**
     * The current globally used instance.
     *
     * @var object
     */
    protected static $instance;

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
     * @var \Marquine\Metis\Database\Database
    */
    protected static $database;

    /**
     * Create a new Metis instance.
     */
    private function __construct() {}

    /**
     * Create or get a Metis instance.
     *
     * @return static
     */
    private static function instance()
    {
        if (static::$instance == null) {
            static::$instance = new Metis;
        }

        return static::$instance;
    }

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

        return static::instance();
    }

    /**
     * Create a new database instance if needed and add a connection.
     *
     * @param  array  $connection
     * @param  string $name
     * @return static
     */
    public static function addConnection($config, $name = 'default')
    {
        if (static::$database === null) {
            static::$database = new \Marquine\Metis\Database\Database;
        }

        static::$database->addConnection($config, $name);

        return static::instance();
    }

    /**
    * Get a database connection instance.
    *
    * @param  string $connection
    * @return \Marquine\Metis\Database\Connection\Connection
    */
    public static function db($connection = 'default')
    {
        if (static::$database === null) {
            static::$database = new \Marquine\Metis\Database\Database;
        }

        return static::$database->getConnection($connection);
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
     * Extract data from the given source.
     *
     * @param  string $type
     * @param  string $source
     * @param  mixed  $columns
     * @param  array  $options
     * @return Metis
     */
    public static function extract($type, $source, $columns = null, $options = [])
    {
        $instance = new Metis;

        $extractor = $instance->factory($type, 'extractors', $options);

        $items = $extractor->extract($source, $columns);

        if (is_object($items) && method_exists($items, 'toArray')) {
            $items = $items->toArray();
        }

        $instance->items = $items;

        return $instance;
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
