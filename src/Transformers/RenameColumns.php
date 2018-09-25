<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Pipeline;

class RenameColumns implements TransformerInterface
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    public $columns = [];

    /**
     * Get the transformer handler.
     *
     * @param  \Marquine\Etl\Pipeline  $pipeline
     * @return callable
     */
    public function handler(Pipeline $pipeline)
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
