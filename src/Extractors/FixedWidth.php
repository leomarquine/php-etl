<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Row;

class FixedWidth extends Extractor
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
        $handle = fopen($this->input, 'r');

        while ($row = fgets($handle)) {
            yield new Row($this->makeRow($row));
        }

        fclose($handle);
    }

    /**
     * Converts a row string into array.
     */
    public function makeRow(string $row): array
    {
        $result = [];

        foreach ($this->columns as $column => $range) {
            $result[$column] = substr($row, $range[0], $range[1]);
        }

        return $result;
    }
}
