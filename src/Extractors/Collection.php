<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Row;

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
     * Extract data from the input.
     *
     * @return \Generator
     */
    public function extract()
    {
        foreach ($this->input as $row) {
            if ($this->columns) {
                $row = array_intersect_key($row, array_flip($this->columns));
            }

            yield new Row($row);
        }
    }
}
