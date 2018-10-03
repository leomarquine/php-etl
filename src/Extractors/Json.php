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
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns'
    ];

    /**
     * Extract data from the given source.
     *
     * @param  mixed  $source
     * @return iterable
     */
    public function extract($source)
    {
        $items = json_decode(file_get_contents($source), true);

        if ($this->columns) {
            $jsonPath = new JSONPath($items);

            foreach ($this->columns as $key => $path) {
                $this->columns[$key] = $jsonPath->find($path)->data();
            }

            $items = $this->transpose($this->columns);
        }

        return (new Collection)->extract($items);;
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
