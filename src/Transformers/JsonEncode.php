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
    protected array $columns = [];

    /**
     * Options.
     */
    protected int $options = 0;

    protected int $depth = 512;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = ['columns', 'depth', 'options'];

    public function transform(Row $row): void
    {
        $row->transform($this->columns, function ($column) {
            return \json_encode($column, $this->options, $this->depth);
        });
    }
}
