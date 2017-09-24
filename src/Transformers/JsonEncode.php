<?php

namespace Marquine\Etl\Transformers;

class JsonEncode implements TransformerInterface
{
    /**
     * Transformer columns.
     *
     * @var array
     */
     public $columns;

    /**
     * Options.
     *
     * @var int
     */
    public $options = 0;

    /**
     * Maximum depth.
     *
     * @var string
     */
    public $depth = 512;

    /**
     * Get the transformer handler.
     *
     * @return callable
     */
    public function handler()
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
