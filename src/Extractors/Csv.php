<?php

namespace Marquine\Metis\Extractors;

use Marquine\Metis\Traits\SetOptions;
use Marquine\Metis\Contracts\Extractor;
use Marquine\Metis\Traits\ValidateSource;

class Csv implements Extractor
{
    use SetOptions, ValidateSource;

    /**
     * The delimiter string.
     *
     * @var string
     */
    protected $delimiter = ';';

    /**
     * The enclosure string.
     *
     * @var string
     */
    protected $enclosure = '"';

    /**
     * Extract data from the given source.
     *
     * @param  string $source
     * @param  mixed  $columns
     * @return array
     */
    public function extract($source, $columns)
    {
        $source = $this->validateSource($source);

        $items = [];

        $handle = fopen($source, 'r');
        if ($handle) {
            while ($row = fgets($handle)) {
                if (! $columns) {
                    $columns = $this->makeColumns($row);
                } else {
                    $items[] = $this->processRow($row, $columns);
                }
            }
            fclose($handle);
        }

        return $items;
    }


    /**
     * Converts the row string into array.
     *
     * @param  string $row
     * @param  array  $columns
     * @return array
     */
    protected function processRow($row, $columns)
    {
        $row = str_getcsv($row, $this->delimiter, $this->enclosure);

        $data = [];

        foreach ($columns as $column => $index) {
            $data[$column] = $row[$index - 1];
        }

        return $data;
    }

    /**
     * Make columns based on csv header.
     *
     * @param  string $header
     * @return array
     */
    protected function makeColumns($header)
    {
        $columns = array_flip(str_getcsv($header, $this->delimiter, $this->enclosure));

        foreach ($columns as $key => $index) {
            $columns[$key] = $index + 1;
        }

        return $columns;
    }
}
