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
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns'
    ];

    /**
     * Extract data from the given source.
     *
     * @param  mixed  $source
     * @return iterable
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
