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

class Trim extends Transformer
{
    public const MASK = 'mask';
    public const TYPE = 'type';

    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected array $columns = [];

    /**
     * The trim type.
     */
    protected string $type = 'both';

    /**
     * The trim mask.
     */
    protected string $mask = " \t\n\r\0\x0B";

    /**
     * The trim function.
     */
    protected string $function = 'trim';

    /**
     * Properties that can be set via the options method.
     */
    protected array $availableOptions = [
        self::COLUMNS,
        self::MASK,
        self::TYPE,
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
            return ($this->function)($column, $this->mask);
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
