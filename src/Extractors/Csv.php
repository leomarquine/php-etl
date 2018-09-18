<?php

namespace Marquine\Etl\Extractors;

class Csv implements ExtractorInterface
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
     * Path to the file.
     *
     * @var string
     */
    protected $file;

    /**
     * Set the extractor source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function source($source)
    {
        $this->file = $source;
    }

    /**
     * Get the extractor iterator.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        $handle = fopen($this->file, 'r');

        $columns = $this->makeColumns($handle);

        while ($row = fgets($handle)) {
            yield $this->makeRow($row, $columns);
        }

        fclose($handle);
    }

    /**
     * Converts the row string to array.
     *
     * @param  string  $row
     * @param  array  $columns
     * @return array
     */
    protected function makeRow($row, $columns)
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
     * @param  array  $handle
     * @return array
     */
    protected function makeColumns($handle)
    {
        if (is_array($this->columns) && is_numeric(current($this->columns))) {
            return $this->columns;
        }

        $columns = array_flip(str_getcsv(fgets($handle), $this->delimiter, $this->enclosure));

        foreach ($columns as $key => $index) {
            $columns[$key] = $index + 1;
        }

        if (empty($this->columns)) {
            return $columns;
        }

        if (array_keys($this->columns) === range(0, count($this->columns) - 1)) {
           return array_intersect_key($columns, array_flip($this->columns));
        }

        $result = [];

        foreach ($this->columns as $key => $value) {
            $result[$value] = $columns[$key];
        }

        return $result;
    }
}
