<?php

namespace Marquine\Etl\Extractors;

use Flow\JSONPath\JSONPath;

class Json extends Extractor
{
    /**
     * Extractor columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * The extractor data.
     *
     * @var array
     */
    protected $data;

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
        $data = json_decode(file_get_contents($source), true);

        if ($this->columns) {
            $jsonPath = new JSONPath($data);

            foreach ($this->columns as $key => $path) {
                $this->columns[$key] = $jsonPath->find($path)->data();
            }

            $data = $this->transpose($this->columns);
        }

        $this->data = $data;
    }

    /**
     * Get the extractor iterator.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        foreach ($this->data as $row) {
            yield $row;
        }
    }

    /**
     * Swap columns and rows.
     *
     * @param  array  $columns
     * @return array
     */
    protected function transpose($columns)
    {
        $data = [];

        foreach ($columns as $column => $items) {
            foreach ($items as $row => $item) {
                $data[$row][$column] = $item;
            }
        }

        return $data;
    }
}
