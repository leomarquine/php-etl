<?php

namespace Marquine\Etl\Transformers;

abstract class Transformer
{
    /**
     * Get the transformer handler.
     *
     * @return callable
     */
    abstract public function handler();
}
