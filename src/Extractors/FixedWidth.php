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
     * The source file.
     *
     * @var string
     */
    protected $file;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns'
    ];

    /**
     * Set up the extraction from the given source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function extract($source)
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
