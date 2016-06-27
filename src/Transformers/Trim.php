<?php

namespace Marquine\Metis\Transformers;

use Marquine\Metis\Traits\SetOptions;
use Marquine\Metis\Contracts\Transformer;

class Trim implements Transformer
{
    use SetOptions;

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
     * Execute a transformation.
     *
     * @param  array $items
     * @param  mixed $columns
     * @return array
     */
    public function transform($items, $columns)
    {
        $this->normalizeType();

        return array_map(function($row) use ($columns) {
            if ($columns) {
                foreach ($columns as $column) {
                    $row[$column] = call_user_func($this->type, $row[$column], $this->mask);
                }
            } else {
                foreach ($row as $column => $value) {
                    $row[$column] = call_user_func($this->type, $value, $this->mask);
                }
            }

            return $row;
        }, $items);
    }

    /**
     * Normalize the trim function name.
     *
     * @return void
     */
    protected function normalizeType()
    {
        switch ($this->type) {
            case 'ltrim':
            case 'start':
            case 'left':
                $this->type = 'ltrim'; break;

            case 'rtrim':
            case 'end':
            case 'right':
                $this->type = 'rtrim'; break;

            case 'trim':
            case 'all':
            case 'both':
                $this->type = 'trim'; break;

            default:
                $this->type = 'trim';
        }
    }
}
