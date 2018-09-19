<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Pipeline;

class JsonDecode implements TransformerInterface
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    public $columns;

    /**
     * Use associative arrays.
     *
     * @var bool
     */
    public $assoc = false;

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
     * @param  \Marquine\Etl\Pipeline  $pipeline
     * @return callable
     */
    public function handler(Pipeline $pipeline)
    {
        return function ($row) {
            if ($this->columns) {
                foreach ($this->columns as $column) {
                    $row[$column] = json_decode($row[$column], $this->assoc, $this->depth, $this->options);
                }
            } else {
                foreach ($row as $column => $value) {
                    $row[$column] = json_decode($value, $this->assoc, $this->depth, $this->options);
                }
            }

            return $row;
        };
    }
}
