<?php

namespace Marquine\Etl\Extractors;

class Arr extends Extractor
{
    /**
     * Extractor columns.
     *
     * @var array
     */
    public $columns;

    /**
     * Extract data from the given source.
     *
     * @param  array  $source
     * @return \Generator
     */
    public function extract($source)
    {
        foreach ($source as $row) {
            if ($this->columns) {
                yield array_intersect_key($row, array_flip($this->columns));
            } else {
                yield $row;
            }
        }
    }
}
