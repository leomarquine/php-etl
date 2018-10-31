<?php

namespace Marquine\Etl;

use Marquine\Etl\Loaders\Loader;
use Marquine\Etl\Extractors\Extractor;
use Marquine\Etl\Transformers\Transformer;

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
     * Get a service from the container.
     *
     * @param  string  $name
     * @return mixed
     */
    public static function service($name)
    {
        return Container::getInstance()->make($name);
    }

    /**
     * Extract.
     *
     * @param  string  $extractor
     * @param  string  $input
     * @param  array  $options
     * @return $this
     */
    public function extract($extractor, $input, $options = [])
    {
        $extractor = $this->container->step($extractor, Extractor::class);

        $options['input'] = $input;

        $extractor->options($options);

        $this->pipeline->extractor($extractor);

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
        $transformer = $this->container->step($transformer, Transformer::class);

        $transformer->options($options);

        $this->pipeline->pipe($transformer);

        return $this;
    }

    /**
     * Load.
     *
     * @param  string  $loader
     * @param  string  $output
     * @param  array  $options
     * @return $this
     */
    public function load($loader, $output, $options = [])
    {
        $loader = $this->container->step($loader, Loader::class);

        $options['output'] = $output;

        $loader->options($options);

        $this->pipeline->pipe($loader);

        return $this;
    }

    /**
     * Execute the ETL.
     *
     * @return void
     */
    public function run()
    {
        $this->pipeline->rewind();

        while ($this->pipeline->valid()) {
            $this->pipeline->next();
        }
    }

    /**
     * Get an array of the resulting ETL data.
     *
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this->pipeline);
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
        $this->pipeline->$method(...$parameters);

        return $this;
    }
}
