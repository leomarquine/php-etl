<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Pipeline;
use InvalidArgumentException;

class ConvertCase implements TransformerInterface
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    public $columns;

    /**
     * The mode of the conversion.
     *
     * @var string
     */
    public $mode = 'lower';

    /**
     * The character encoding.
     *
     * @var string
     */
    public $encoding = 'utf-8';

    /**
     * Get the transformer handler.
     *
     * @param  \Marquine\Etl\Pipeline  $pipeline
     * @return callable
     */
    public function handler(Pipeline $pipeline)
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
