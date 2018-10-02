<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Step;

abstract class Transformer extends Step
{
    /**
     * Get the transformer handler.
     *
     * @return callable
     */
    abstract public function transform();
}
