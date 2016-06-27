<?php

namespace Marquine\Metis\Contracts;

interface Transformer
{
    /**
     * Execute a transformation.
     *
     * @param  array $items
     * @param  mixed $columns
     * @return array
     */
    public function transform($items, $columns);
}
