<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Row;
use InvalidArgumentException;

class ConvertCase extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
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
     * @var array
     */
    protected $availableOptions = [
        'columns', 'encoding', 'mode'
    ];

    /**
     * Initialize the step.
     *
     * @return void
     */
    public function initialize()
    {
        $this->conversionMode = $this->getConversionMode();
    }

    /**
     * Transform the given row.
     *
     * @param  \Marquine\Etl\Row  $row
     * @return void
     */
    public function transform(Row $row)
    {
        $row->transform($this->columns, function ($column) {
            return mb_convert_case($column, $this->conversionMode, $this->encoding);
        });
    }

    /**
     * Get the conversion mode.
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    protected function getConversionMode()
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

        throw new InvalidArgumentException("The conversion mode [{$this->mode}] is invalid.");
    }
}
