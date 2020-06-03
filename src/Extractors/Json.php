<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Extractors;

use Flow\JSONPath\JSONPath;
use Wizaplace\Etl\Row;

class Json extends Extractor
{
    /**
     * Extractor columns.
     *
     * @var array|null
     */
    protected $columns;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns',
    ];

    /**
     * Extract data from the input.
     */
    public function extract(): \Generator
    {
        $data = json_decode(file_get_contents($this->input), true);

        if (is_array($this->columns) && [] !== $this->columns) {
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
     */
    protected function transpose(array $columns): array
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
