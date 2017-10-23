<?php

namespace Marquine\Etl;

use BadMethodCallException;
use Marquine\Etl\Loaders\Loader;
use Marquine\Etl\Extractors\Extractor;
use Marquine\Etl\Transformers\Transformer;

class Job
{
    /**
     * The step factory.
     *
     * @var \Marquine\Etl\Factory
     */
    protected $factory;

    /**
     * The Job pipeline.
     *
     * @var \Marquine\Etl\Pipeline
     */
    protected $pipeline;

    /**
     * Create a new Job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->factory = new Factory;
    }

    /**
     * Set the step factory.
     *
     * @param  \Marquine\Etl\Factory  $factory
     * @return $this
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Extract data from the given source.
     *
     * @param  string  $extractor
     * @param  mixed  $source
     * @param  array  $options
     * @return $this
     */
    protected function extract($extractor, $source, $options = null)
    {
        $extractor = $this->factory->make(Extractor::class, $extractor, $options);

        $this->pipeline = $extractor->pipeline($source);

        return $this;
    }

    /**
     * Execute a transformation.
     *
     * @param  string  $transformer
     * @param  array  $options
     * @return $this
     */
    public function transform($transformer, $options = null)
    {
        $transformer = $this->factory->make(Transformer::class, $transformer, $options);

        $this->pipeline->pipe($transformer->handler());

        return $this;
    }

    /**
     * Load data to the given destination.
     *
     * @param  string  $loader
     * @param  string  $destination
     * @param  array  $options
     * @return $this
     */
    public function load($loader, $destination, $options = null)
    {
        $loader = $this->factory->make(Loader::class, $loader, $options);

        $loader->load($this->pipeline->get(), $destination);

        return $this;
    }

    /**
     * Get the Job data generator.
     *
     * @return array
     */
    public function data()
    {
        return $this->pipeline->get();
    }

    /**
     * Get the Job data array.
     *
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this->data());
    }

    /**
     * Handle method calls to allow static 'extract' job constructor.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return void
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if ($method != 'extract') {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        return $this->extract(...$parameters);
    }

    /**
     * Handle method calls to allow static 'extract' job constructor.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return void
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if ($method != 'extract') {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        return (new static)->extract(...$parameters);
    }
}
