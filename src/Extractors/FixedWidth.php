<?php

namespace Marquine\Etl\Extractors;

use IteratorAggregate;

class FixedWidth implements ExtractorInterface, IteratorAggregate
{
    /**
     * Extractor columns.
     *
     * @var array
     */
    public $columns;

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
     * @param  array  $source
     * @return \Generator
     */
    public function getIterator()
    {
        $handle = fopen($this->file, 'r');

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
