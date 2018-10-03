<?php

namespace Marquine\Etl\Extractors;

class FixedWidth extends Extractor
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
        $handle = fopen($source, 'r');

        while ($row = fgets($handle)) {
            yield $this->makeRow($row);
        }

        fclose($handle);
    }

    /**
     * Converts a row string into array.
     *
     * @param  string  $row
     * @return array
     */
    public function makeRow($row)
    {
        $result = [];

        foreach ($this->columns as $column => $range) {
            $result[$column] = substr($row, $range[0], $range[1]);
        }

        return $result;
    }
}
