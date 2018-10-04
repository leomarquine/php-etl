<?php

namespace Marquine\Etl\Extractors;

class Collection extends Extractor
{
    /**
     * Extractor columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * The extractor data collection.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns'
    ];

    /**
     * Set up the extraction from the given source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function extract($source)
    {
        $this->data = $source;
    }

    /**
     * Get the extractor iterator.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        foreach ($this->data as $row) {
            if ($this->columns) {
                yield array_intersect_key($row, array_flip($this->columns));
            } else {
                yield $row;
            }
        }
    }
}
