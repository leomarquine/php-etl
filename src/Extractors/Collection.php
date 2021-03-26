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

class Collection extends Extractor
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
        foreach ($this->input as $row) {
            if ([] !== $this->columns) {
                $row = array_intersect_key($row, array_flip($this->columns));
            }

            yield new Row($row);
        }
    }
}
