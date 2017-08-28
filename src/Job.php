<?php

namespace Marquine\Etl;

class Job
{
    /**
     * The items contained in the transformation.
     *
     * @var array
     */
    protected $items;

    /**
    * Create a new Job instance.
    *
    * @return Job
    */
    public static function start()
    {
        return new Job;
    }

    /**
     * Get current job items.
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
     * @param string $type
     * @param mixed $source
     * @param array $options
     * @return $this
     */
    public function extract($type, $source, $options = [])
    {
        $extractor = $this->factory($type, 'extractors', $options);

        $this->items = $extractor->extract($source);

        return $this;
    }

    /**
     * Execute a transformation.
     *
     * @param string $type
     * @param array $options
     * @return $this
     */
    public function transform($type, $options = [])
    {
        $transformer = $this->factory($type, 'transformers', $options);

        $this->items = $transformer->transform($this->items);

        return $this;
    }

    /**
     * Load data to the given destination.
     *
     * @param string $type
     * @param string $destination
     * @param array $options
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
     * @param string $class
     * @param string $category
     * @param array $options
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

        $instance = new $class;

        $instance = $this->setOptions($instance, $options);

        return $instance;
    }

    /**
     * Set options.
     *
     * @param mixed $instance
     * @param array $options
     * @return mixed
     */
    protected function setOptions($instance, $options)
    {
        $reflector = new \ReflectionClass($instance);

        foreach ($options as $option => $value) {
            if ($reflector->hasProperty($option)) {
                $property = $reflector->getProperty($option);
            }

            if ($property && $property->isPublic()) {
                $instance->$option = $value;
            }
        }

        return $instance;
    }
}
