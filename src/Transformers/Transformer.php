<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Step;

abstract class Transformer extends Step
{
    /**
     * Transform the given row.
     *
     * @param  \Marquine\Etl\Row  $row
     * @return void
     */
    abstract public function transform($row);
}
