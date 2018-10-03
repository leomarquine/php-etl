<?php

namespace Marquine\Etl\Transformers;

class RenameColumns extends Transformer
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
        'columns'
    ];

    /**
     * Get the transformer handler.
     *
     * @return callable
     */
    public function transform()
    {
        return function ($row) {
            foreach ($this->columns as $old => $new) {
                $row[$new] = $row[$old];
                unset($row[$old]);
            }

            return $row;
        };
    }
}
