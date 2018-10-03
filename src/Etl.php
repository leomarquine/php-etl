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
     * @param  string  $source
     * @param  array  $options
     * @return $this
     */
    public function extract($extractor, $source, $options = [])
    {
        $extractor = $this->container->step($extractor, Extractor::class);

        $extractor->pipeline($this->pipeline)->options($options);

        $flow = $this->container->make(Flow::class, $extractor->extract($source));

        $this->pipeline->flow($flow);

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

        $transformer->pipeline($this->pipeline)->options($options);

        $this->pipeline->pipe($transformer->transform());

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
        $loader = $this->container->step($loader, Loader::class);

        $loader->pipeline($this->pipeline)->options($options);

        $this->pipeline->pipe($loader->load($destination));

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
