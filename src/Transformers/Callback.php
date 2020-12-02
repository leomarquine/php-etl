<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @author      Karl DeBisschop <kdebisschop@gmail.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Transformers;

use Wizaplace\Etl\Row;

class Callback extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected $columns = [];

    /**
     * The callback function.
     *
     * @var callable
     */
    protected $callback;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected $availableOptions = ['columns', 'callback'];

    /**
     * Transform the given row.
     */
    public function transform(Row $row): void
    {
        foreach ($this->columns as $column) {
            $row->set($column, call_user_func($this->callback, $row, $column));
        }
    }
}
