<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Transformers;

use Wizaplace\Etl\Row;

class Trim extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected $columns = [];

    /**
     * The trim type.
     *
     * @var string
     */
    protected $type = 'both';

    /**
     * The trim mask.
     *
     * @var string
     */
    protected $mask = " \t\n\r\0\x0B";

    /**
     * The trim function.
     *
     * @var string
     */
    protected $function;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected $availableOptions = [
        'columns', 'type', 'mask',
    ];

    /**
     * Initialize the step.
     */
    public function initialize(): void
    {
        $this->function = $this->getTrimFunction();
    }

    /**
     * Transform the given row.
     */
    public function transform(Row $row): void
    {
        $row->transform($this->columns, function ($column) {
            return call_user_func($this->function, $column, $this->mask);
        });
    }

    /**
     * Get the trim function name.
     */
    protected function getTrimFunction(): string
    {
        switch ($this->type) {
            case 'ltrim':
            case 'start':
            case 'left':
                return 'ltrim';

            case 'rtrim':
            case 'end':
            case 'right':
                return 'rtrim';

            case 'trim':
            case 'all':
            case 'both':
                return 'trim';
        }

        throw new \InvalidArgumentException("The trim type [{$this->type}] is invalid.");
    }
}
