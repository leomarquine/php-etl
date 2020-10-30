<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Row;

class Callback extends Transformer
{
    /**
     * The callback for the transformation.
     *
     * @var callable
     */
    protected $callback;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'callback'
    ];

    /**
     * Transform the given row.
     *
     * @param  \Marquine\Etl\Row  $row
     * @return void
     */
    public function transform(Row $row)
    {
        call_user_func($this->callback, $row);
    }
}
