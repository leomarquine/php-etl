<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Transformers;

use Wizaplace\Etl\Row;
use Wizaplace\Etl\Transformers\Transformer;

class ColumnFilterTransformer extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected $columns = [];

    /**
     * @var callable|null $callback Callback function to apply on every Row attribute (column):
     *                            - it takes the column name as first parameter
     *                            - it takes the column value as second parameter
     *                            - it must return a boolean (true to keep the column, false otherwise)
     */
    protected $callback;

    /**
     * Properties that can be set via the options method. Supports:
     *   - columns (optional): list of column names to keep
     *   - callback:
     *
     * @var string[]
     */
    protected $availableOptions = [
        'columns', 'callback',
    ];

    /**
     * @inheritDoc
     */
    public function transform(Row $row): void
    {
        $rowArray = $row->toArray();
        $hasColumns = count($this->columns) > 0;
        $hasCallback = is_callable($this->callback);

        $row->clearAttributes();

        foreach ($rowArray as $columnName => $value) {
            if (
                ($hasColumns && false === in_array($columnName, $this->columns, true))
                || ($hasCallback && false === ($this->callback)($columnName, $value))
            ) {
                continue;
            }

            $row->offsetSet($columnName, $value);
        }
    }
}
