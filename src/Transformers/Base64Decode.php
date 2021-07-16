<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Row;

class Base64Decode extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns',
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
            return base64_decode($column);
        });
    }
}

