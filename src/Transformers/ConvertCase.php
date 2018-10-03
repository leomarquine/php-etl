<?php

namespace Marquine\Etl\Transformers;

use InvalidArgumentException;

class ConvertCase extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    protected $columns;

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
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'encoding', 'mode'
    ];

    /**
     * Get the transformer handler.
     *
     * @return callable
     */
    public function transform()
    {
        $mode = $this->getConversionMode();

        return function ($row) use ($mode) {
            if ($this->columns) {
                foreach ($this->columns as $column) {
                    $row[$column] = mb_convert_case($row[$column], $mode, $this->encoding);
                }
            } else {
                foreach ($row as $column => $value) {
                    $row[$column] = mb_convert_case($value, $mode, $this->encoding);
                }
            }

            return $row;
        };
    }

    /**
     * Get the conversion mode.
     *
     * @return string
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

        throw new InvalidArgumentException('The provided conversion mode is not supported.');
    }
}
