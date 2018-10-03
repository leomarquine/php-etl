<?php

namespace Marquine\Etl\Transformers;

class JsonEncode extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * Options.
     *
     * @var int
     */
    protected $options = 0;

    /**
     * Maximum depth.
     *
     * @var string
     */
    protected $depth = 512;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'depth', 'options'
    ];

    /**
     * Get the transformer handler.
     *
     * @return callable
     */
    public function transform()
    {
        return function ($row) {
            if ($this->columns) {
                foreach ($this->columns as $column) {
                    $row[$column] = json_encode($row[$column], $this->options, $this->depth);
                }
            } else {
                foreach ($row as $column => $value) {
                    $row[$column] = json_encode($value, $this->options, $this->depth);
                }
            }

            return $row;
        };
    }
}
