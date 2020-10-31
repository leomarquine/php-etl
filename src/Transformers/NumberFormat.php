<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Row;
use Marquine\Etl\Transformers\Transformer;
use InvalidArgumentException;

class NumberFormat extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * The number of decimal points.
     *
     * @var int
     */
    protected $decimals = 0;

    /**
     * The separator for the decimal point.
     *
     * @var string
     */
    protected $decimalPoint = '.';

    /**
     * The thousands separator.
     *
     * @var string
     */
    protected $thousandsSeparator = ',';

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'decimals', 'decimalPoint', 'thousandsSeparator'
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
            return number_format($column, $this->decimals, $this->decimalPoint, $this->thousandsSeparator);
        });
    }
}
