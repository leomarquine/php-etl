<?php

namespace Marquine\Etl;

use ReflectionClass;

class Etl
{
    /**
     * The etl container.
     *
     * @var \Marquine\Etl\Container
     */
    protected $container;

    /**
     * The etl pipeline.
     *
     * @var \Marquine\Etl\Pipeline
     */
    protected $pipeline;

    /**
     * Create a new Etl instance.
     *
     * @param  Container  $container
     * @param  Pipeline  $pipeline
     * @return void
     */
    public function __construct(Container $container = null, Pipeline $pipeline = null)
    {
        $this->container = $container ?? Container::getInstance();
        $this->pipeline = $pipeline ?? new Pipeline;
    }

    /**
     * Extract.
     *
     * @param  string  $extractor
     * @param  string  $source
     * @param  array  $options
     * @return $this
     */
    public function extract($extractor, $source, $options = [])
    {
        $extractor = $this->make('extractor', $extractor, $options);

        $extractor->source($source);

        $this->pipeline->flow($extractor);

        return $this;
    }

    /**
     * Transform.
     *
     * @param  string  $transformer
     * @param  array  $options
     * @return $this
     */
    public function transform($transformer, $options = [])
    {
        $transformer = $this->make('transformer', $transformer, $options);

        $this->pipeline->pipe($transformer->handler($this->pipeline));

        return $this;
    }

    /**
     * Load.
     *
     * @param  string  $loader
     * @param  string  $destination
     * @param  array  $options
     * @return $this
     */
    public function load($loader, $destination, $options = [])
    {
        $loader = $this->make('loader', $loader, $options);

        $this->pipeline->pipe($loader->handler($this->pipeline, $destination));

        return $this;
    }

    /**
     * Execute the ETL.
     *
     * @return void
     */
    public function run()
    {
        $generator = $this->pipeline->get();

        while($generator->valid()) {
            $generator->next();
        }
    }

    /**
     * Get an array of the resulting ETL data.
     *
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this->pipeline->get());
    }

    /**
     * Make a new step instance.
     *
     * @param  string  $type
     * @param  string  $step
     * @param  array  $options
     * @return object
     */
    protected function make($type, $step, $options)
    {
        $step = $this->container->make("$type.$step");

        if (!empty($options)) {
            $reflector = new ReflectionClass($step);

            foreach ($options as $property => $value) {
                $property = lcfirst(implode('', array_map('ucfirst', explode('_', $property))));

                if ($reflector->hasProperty($property) && $reflector->getProperty($property)->isPublic()) {
                    $step->$property = $value;
                }
            }
        }

        return $step;
    }

    /**
     * Handle dynamic method calls.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     */
    public function __call($method, $parameters)
    {
        return $this->pipeline->$method(...$parameters);
    }
}
