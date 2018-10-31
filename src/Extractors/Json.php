<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Row;
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
     * Extract data from the input.
     *
     * @return \Generator
     */
    public function extract()
    {
        $data = json_decode(file_get_contents($this->input), true);

        if ($this->columns) {
            $jsonPath = new JSONPath($data);

            foreach ($this->columns as $key => $path) {
                $this->columns[$key] = $jsonPath->find($path)->data();
            }

            $data = $this->transpose($this->columns);
        }

        foreach ($data as $row) {
            yield new Row($row);
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
