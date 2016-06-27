<?php

namespace Marquine\Metis\Extractors;

use Flow\JSONPath\JSONPath;
use Marquine\Metis\Contracts\Extractor;
use Marquine\Metis\Traits\ValidateSource;

class Json implements Extractor
{
    use ValidateSource;

    /**
     * Extract data from the given source.
     *
     * @param  string $source
     * @param  mixed  $columns
     * @return array
     */
    public function extract($source, $columns)
    {
        $source = $this->validateSource($source);

        $items = json_decode(file_get_contents($source), true);

        if ($columns) {
            $jsonPath = new JSONPath($items);

            foreach ($columns as $key => $path) {
                $columns[$key] = $jsonPath->find($path)->data();
            }

            $items = $this->transpose($columns);
        }

        return $items;
    }

    /**
     * Switch columns and rows.
     *
     * @param  array $columns
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
