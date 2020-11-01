<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Row;
use Marquine\Etl\Transformers\Transformer;

class Pad extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * The total string size.
     *
     * @var int
     */
    protected $length = 0;

    /**
     * The string to pad with.
     *
     * @var string
     */
    protected $string = ' ';

    /**
     * The pad type.
     *
     * @var int
     */
    protected $type = 'right';

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'length', 'string', 'type'
    ];

    /**
     * Transform the given row.
     *
     * @param  \Marquine\Etl\Row  $row
     * @return void
     */
    public function transform(Row $row)
    {
        $row->transform($this->columns, function ($column) {
            return str_pad($column, $this->length, $this->string, $this->type());
        });
    }

    /**
     * Get the pad type.
     *
     * @return string
     */
    protected function type()
    {
        switch ($this->type) {
            case 'right':
                return STR_PAD_RIGHT;
            case 'left':
                return STR_PAD_LEFT;
            case 'both':
                return STR_PAD_BOTH;
            default:
                return $this->type;
        }
    }
}
