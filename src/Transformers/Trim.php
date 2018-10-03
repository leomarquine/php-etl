<?php

namespace Marquine\Etl\Transformers;

use InvalidArgumentException;

class Trim extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    protected $columns;

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
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'type', 'mask'
    ];

    /**
     * Get the transformer handler.
     *
     * @return callable
     */
    public function transform()
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
