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
    public const DEPTH = 'depth';
    public const OPTIONS = 'options';

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
    protected array $availableOptions = [
        self::COLUMNS,
        self::DEPTH,
        self::OPTIONS,
    ];

    public function transform(Row $row): void
    {
        $row->transform($this->columns, function ($column) {
            return \json_encode($column, $this->options, $this->depth);
        });
    }
}
