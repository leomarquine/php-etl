<?php

namespace Marquine\Etl\Transformers;

class JsonDecode extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * Use associative arrays.
     *
     * @var bool
     */
    protected $assoc = false;

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
        'assoc', 'columns', 'depth', 'options'
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
