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
    protected array $columns = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [self::COLUMNS];

    /**
     * Extract data from the input.
     */
    public function extract(): \Generator
    {
        $data = json_decode(file_get_contents($this->input), true);

        if ([] !== $this->columns) {
            $jsonPath = new JSONPath($data);

            foreach ($this->columns as $key => $path) {
                $this->columns[$key] = $jsonPath->find($path)->getData();
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
