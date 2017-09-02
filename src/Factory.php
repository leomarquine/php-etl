<?php

namespace Marquine\Etl;

use InvalidArgumentException;

class Factory
{
    /**
     * Step class.
     *
     * @var mixed
     */
    protected $class;

    /**
     * Step interface.
     *
     * @var mixed
     */
    protected $interface;

    /**
     * Step properties.
     *
     * @var array|null
     */
    protected $properties;

    /**
     * Make a new factory instance.
     *
     * @param  mixed  $class
     * @param  mixed  $interface
     * @param  array|null  $properties
     * @return void
     */
    public function __construct($class, $interface, $properties)
    {
        $this->class = $class;
        $this->interface = $interface;
        $this->properties = $properties;
    }

    /**
     * Make a new extractor.
     *
     * @param  \Marquine\Etl\Extractors\ExtractorInterface|string  $extractor
     * @return \Marquine\Etl\Extractors\ExtractorInterface
     */
    public static function extractor($extractor, $properties = null)
    {
        return (new static($extractor, Extractors\ExtractorInterface::class, $properties))->make();
    }

    /**
     * Make a new transformer.
     *
     * @param  \Marquine\Etl\Transformers\TransformerInterface|string  $transformer
     * @return \Marquine\Etl\Transformers\TransformerInterface
     */
    public static function transformer($transformer, $properties = null)
    {
        return (new static($transformer, Transformers\TransformerInterface::class, $properties))->make();
    }

    /**
     * Make a new loader.
     *
     * @param  \Marquine\Etl\Loaders\LoaderInterface|string  $loader
     * @return \Marquine\Etl\Loaders\LoaderInterface
     */
    public static function loader($loader, $properties = null)
    {
        return (new static($loader, Loaders\LoaderInterface::class, $properties))->make();
    }

    /**
     * Make an instance of the given class.
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    protected function make()
    {
        if (is_string($this->class) && class_exists($this->class)) {
            $this->class = new $this->class;
        }

        if ($this->class instanceof $this->interface) {
            $this->setProperties();

            return $this->class;
        }

        $type = ucfirst(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function']);

        throw new InvalidArgumentException("$type must implement '{$this->interface}' interface.");
    }

    /**
     * Set properties.
     *
     * @return void
     */
    protected function setProperties()
    {
        if (! $this->properties) {
            return;
        }

        $reflector = new \ReflectionClass($this->class);

        foreach ($this->properties as $property => $value) {
            if ($reflector->hasProperty($property) && $reflector->getProperty($property)->isPublic()) {
                $this->class->$property = $value;
            }
        }
    }
}
