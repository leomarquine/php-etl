<?php

namespace Marquine\Etl;

use BadMethodCallException;

class Job
{
    /**
     * The Job pipeline.
     *
     * @var \Marquine\Etl\Pipeline
     */
    protected $pipeline;

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
        $extractor = Factory::extractor($extractor, $options);

        $this->pipeline = new Pipeline($extractor->extract($source));

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
        $transformer = Factory::transformer($transformer, $options);

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
        $loader = Factory::loader($loader, $options);

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
}
