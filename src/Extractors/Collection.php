<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Row;

class Collection extends Extractor
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
        foreach ($this->input as $row) {
            if (is_array($this->columns)) {
                $row = array_intersect_key($row, array_flip($this->columns));
            }

            yield new Row($row);
        }
    }
}
