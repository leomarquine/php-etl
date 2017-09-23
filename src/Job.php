<?php

namespace Marquine\Etl;

class Job
{
    /**
     * The Job pipeline.
     *
     * @var \Marquine\Etl\Pipeline
     */
    protected $pipeline;

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
     * Get job data generator.
     *
     * @return array
     */
    public function get()
    {
        return $this->pipeline->get();
    }

    /**
     * Extract data from the given source.
     *
     * @param  string  $extractor
     * @param  mixed  $source
     * @param  array  $options
     * @return $this
     */
    public function extract($extractor, $source, $options = null)
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

        $this->pipeline->pipe($transformer->handle());

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

        $loader->load($destination, $this->pipeline);

        return $this;
    }
}
