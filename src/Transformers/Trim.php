<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Row;
use InvalidArgumentException;

class Trim extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
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
     * @var array
     */
    protected $availableOptions = [
        'columns', 'type', 'mask'
    ];

    /**
     * Initialize the step.
     *
     * @return void
     */
    public function initialize()
    {
        $this->function = $this->getTrimFunction();
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
            return call_user_func($this->function, $column, $this->mask);
        });
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

        throw new InvalidArgumentException("The trim type [{$this->type}] is invalid.");
    }
}
