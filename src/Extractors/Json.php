<?php

namespace Marquine\Etl\Extractors;

use Flow\JSONPath\JSONPath;
use Marquine\Etl\Traits\ValidateSource;

class Json implements ExtractorInterface
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
     * @param  string  $source
     * @return \Generator
     */
    public function extract($source)
    {
        $source = $this->validateSource($source);

        $items = json_decode(file_get_contents($source), true);

        if ($this->columns) {
            $jsonPath = new JSONPath($items);

            foreach ($this->columns as $key => $path) {
                $this->columns[$key] = $jsonPath->find($path)->data();
            }

            $items = $this->transpose($this->columns);
        }

        return (new Arr)->extract($items);
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
