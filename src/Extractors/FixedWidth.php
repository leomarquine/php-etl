<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Traits\ValidateSource;

class FixedWidth implements ExtractorInterface
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
     * @param string $source
     * @return array
     */
    public function extract($source)
    {
        $source = $this->validateSource($source);

        $items = [];

        $handle = fopen($source, 'r');
        if ($handle) {
            while ($row = fgets($handle)) {
                $items[] = $this->processRow($row, $this->columns);
            }
            fclose($handle);
        }

        return $items;
    }

    /**
     * Converts a row string into array.
     *
     * @param string $row
     * @param array $columns
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
