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

class ConvertCase extends Transformer
{
    public const ENCODING = 'encoding';
    public const MODE = 'mode';

    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected array $columns = [];

    /**
     * The mode of the conversion.
     */
    protected string $mode = 'lower';

    /**
     * The character encoding.
     */
    protected string $encoding = 'utf-8';

    /**
     * The int representation of the mode.
     */
    protected int $conversionMode;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [
        self::COLUMNS,
        self::ENCODING,
        self::MODE,
    ];

    public function initialize(): void
    {
        $this->conversionMode = $this->getConversionMode();
    }

    public function transform(Row $row): void
    {
        $row->transform($this->columns, function ($column): string {
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
