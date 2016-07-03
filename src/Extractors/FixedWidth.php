<?php

namespace Marquine\Metis\Extractors;

use Marquine\Metis\Contracts\Extractor;
use Marquine\Metis\Traits\ValidateSource;

class FixedWidth implements Extractor
{
    use ValidateSource;

    /**
     * Extract data from the given source.
     *
     * @param  string $source
     * @param  mixed  $columns
     * @return array
     */
    public function extract($source, $columns = null)
    {
        $source = $this->validateSource($source);

        $items = [];

        $handle = fopen($source, 'r');
        if ($handle) {
            while ($row = fgets($handle)) {
                $items[] = $this->processRow($row, $columns);
            }
            fclose($handle);
        }

        return $items;
    }

    /**
     * Converts a row string into array.
     *
     * @param  string $row
     * @param  array  $columns
     * @return array
     */
    public function processRow($row, $columns)
    {
        $result = [];

        foreach ($columns as $column => $range) {
            $result[$column] = substr($row, $range[0], $range[1]);
        }

        return $result;
    }
}
