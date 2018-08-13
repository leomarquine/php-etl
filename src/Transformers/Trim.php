<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Pipeline;
use InvalidArgumentException;

class Trim implements TransformerInterface
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    public $columns;

    /**
     * The trim type.
     *
     * @var string
     */
    public $type = 'both';

    /**
     * The trim mask.
     *
     * @var string
     */
    public $mask = " \t\n\r\0\x0B";

    /**
     * Get the transformer handler.
     *
     * @param  \Marquine\Etl\Pipeline  $pipeline
     * @return callable
     */
    public function handler(Pipeline $pipeline)
    {
        $type = $this->getTrimFunction();

        return function ($row) use ($type) {
            if ($this->columns) {
                foreach ($this->columns as $column) {
                    $row[$column] = call_user_func($type, $row[$column], $this->mask);
                }
            } else {
                foreach ($row as $column => $value) {
                    $row[$column] = call_user_func($type, $value, $this->mask);
                }
            }

            return $row;
        };
    }

    /**
     * Get the trim function name.
     *
     * @return string
     */
    protected function getTrimFunction()
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

        throw new InvalidArgumentException('The provided trim type is not supported.');
    }
}
