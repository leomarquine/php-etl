<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Traits\ValidateSource;

class Csv implements ExtractorInterface
{
    use ValidateSource;

    /**
     * Extractor columns.
     *
     * @var array
     */
    public $columns;

    /**
     * The delimiter string.
     *
     * @var string
     */
    public $delimiter = ';';

    /**
     * The enclosure string.
     *
     * @var string
     */
    public $enclosure = '"';

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
                if (! $this->columns) {
                    $this->columns = $this->makeColumns($row);
                } else {
                    $items[] = $this->processRow($row, $this->columns);
                }
            }
            fclose($handle);
        }

        return $items;
    }


    /**
     * Converts the row string into array.
     *
     * @param string $row
     * @param array $columns
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
     * @param string $header
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
