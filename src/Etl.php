<?php

namespace Marquine\Etl;

use Marquine\Etl\Loaders\Loader;
use Marquine\Etl\Extractors\Extractor;
use Marquine\Etl\Transformers\Transformer;

class Etl
{
    /**
     * The etl pipeline.
     *
     * @var \Marquine\Etl\Pipeline
     */
    protected $pipeline;

    /**
     * Create a new Etl instance.
     *
     * @param  Pipeline  $pipeline
     * @return void
     */
    public function __construct(Pipeline $pipeline = null)
    {
        $this->pipeline = $pipeline ?? new Pipeline;
    }

    /**
     * Extract.
     *
     * @param  Extractor  $extractor
     * @param  string  $input
     * @param  array  $options
     * @return $this
     */
    public function extract(Extractor $extractor, $input, $options = [])
    {
        $extractor->input($input)->options($options);

        $this->pipeline->extractor($extractor);

        return $this;
    }

    /**
     * Transform.
     *
     * @param  Transformer  $transformer
     * @param  array  $options
     * @return $this
     */
    public function transform(Transformer $transformer, $options = [])
    {
        $transformer->options($options);

        $this->pipeline->pipe($transformer);

        return $this;
    }

    /**
     * Load.
     *
     * @param  Loader  $loader
     * @param  string  $output
     * @param  array  $options
     * @return $this
     */
    public function load(Loader $loader, $output, $options = [])
    {
        $loader->output($output)->options($options);

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
