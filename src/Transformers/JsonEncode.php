<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Transformers;

use Wizaplace\Etl\Row;

class JsonEncode extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected $columns = [];

    /**
     * Options.
     *
     * @var int
     */
    protected $options = 0;

    /**
     * Maximum depth.
     *
     * @var int
     */
    protected $depth = 512;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected $availableOptions = [
        'columns', 'depth', 'options',
    ];

    /**
     * Transform the given row.
     */
    public function transform(Row $row): void
    {
        $row->transform($this->columns, function ($column) {
            return \json_encode($column, $this->options, $this->depth);
        });
    }
}
