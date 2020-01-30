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

class ConvertCase extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected $columns = [];

    /**
     * The mode of the conversion.
     *
     * @var string
     */
    protected $mode = 'lower';

    /**
     * The character encoding.
     *
     * @var string
     */
    protected $encoding = 'utf-8';

    /**
     * The int representation of the mode.
     *
     * @var int
     */
    protected $conversionMode;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected $availableOptions = [
        'columns', 'encoding', 'mode',
    ];

    /**
     * Initialize the step.
     */
    public function initialize(): void
    {
        $this->conversionMode = $this->getConversionMode();
    }

    /**
     * Transform the given row.
     */
    public function transform(Row $row): void
    {
        $row->transform($this->columns, function ($column) {
            return mb_convert_case($column, $this->conversionMode, $this->encoding);
        });
    }

    /**
     * Get the conversion mode.
     *
     * @throws \InvalidArgumentException
     */
    protected function getConversionMode(): int
    {
        switch ($this->mode) {
            case 'upper':
            case 'uppercase':
                return MB_CASE_UPPER;

            case 'lower':
            case 'lowercase':
                return MB_CASE_LOWER;

            case 'title':
                return MB_CASE_TITLE;
        }

        throw new \InvalidArgumentException("The conversion mode [{$this->mode}] is invalid.");
    }
}
