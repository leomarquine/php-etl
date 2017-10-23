<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Support\ValidateSource;

class FixedWidth extends Extractor
{
    use ValidateSource;

    /**
     * Extractor columns.
     *
     * @var array
     */
    public $columns;

    /**
     * Extract data from the given source.
     *
     * @param  string  $source
     * @return \Generator
     */
    public function extract($source)
    {
        $source = $this->validateSource($source);

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
