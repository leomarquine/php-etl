<?php

namespace Marquine\Etl\Transformers;

interface TransformerInterface
{
    /**
     * Get the transformer handler.
     *
     * @return callable
     */
    public function handler();
}
