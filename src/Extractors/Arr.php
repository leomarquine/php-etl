<?php

namespace Marquine\Etl\Extractors;

class Arr implements ExtractorInterface
{
    /**
     * Extractor columns.
     *
     * @var array
     */
    public $columns;

    /**
     * The extractor data.
     *
     * @var array
     */
    protected $data;

    /**
     * Set the extractor source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function source($source)
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
