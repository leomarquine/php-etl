<?php

namespace Marquine\Etl\Extractors;

class Csv extends Extractor
{
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
    public $delimiter = ',';

    /**
     * The enclosure string.
     *
     * @var string
     */
    public $enclosure = '';

    /**
     * Extract data from the given source.
     *
     * @param  string  $source
     * @return \Generator
     */
    public function extract($source)
    {
        $source = $this->validateSourceFile($source);

        $handle = fopen($source, 'r');

        while ($row = fgets($handle)) {
            if (! $this->columns) {
                $this->columns = $this->makeColumns($row);
            } else {
                yield $this->makeRow($row);
            }
        }

        fclose($handle);
    }

    /**
     * Converts the row string to array.
     *
     * @param  string  $row
     * @return array
     */
    protected function makeRow($row)
    {
        $row = str_getcsv($row, $this->delimiter, $this->enclosure);

        $data = [];

        foreach ($this->columns as $column => $index) {
            $data[$column] = $row[$index - 1];
        }

        return $data;
    }

    /**
     * Make columns based on csv header.
     *
     * @param  string  $header
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
