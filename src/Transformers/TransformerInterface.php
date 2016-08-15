<?php

namespace Marquine\Etl\Transformers;

interface TransformerInterface
{
    /**
     * Execute a transformation.
     *
     * @param array $items
     * @return array
     */
    public function transform($items);
}
