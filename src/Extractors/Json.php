<?php

namespace Marquine\Etl\Extractors;

use Flow\JSONPath\JSONPath;

class Json implements ExtractorInterface
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
     * @return \Generator
     */
    public function getIterator()
    {
        $items = json_decode(file_get_contents($this->file), true);

        if ($this->columns) {
            $jsonPath = new JSONPath($items);

            foreach ($this->columns as $key => $path) {
                $this->columns[$key] = $jsonPath->find($path)->data();
            }

            $items = $this->transpose($this->columns);
        }

        $data = new Arr;

        $data->source($items);

        return $data;
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
