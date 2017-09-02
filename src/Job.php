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
     * @param  string  $extractor
     * @param  mixed  $source
     * @param  array  $options
     * @return $this
     */
    public function extract($extractor, $source, $options = null)
    {
        $extractor = Factory::extractor($extractor, $options);

        $this->items = $extractor->extract($source);

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

        $this->items = $transformer->transform($this->items);

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

        $loader->load($destination, $this->items);

        return $this;
    }
}
