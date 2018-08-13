<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Pipeline;

interface TransformerInterface
{
    /**
     * Get the transformer handler.
     *
     * @param  \Marquine\Etl\Pipeline  $pipeline
     * @return callable
     */
    public function handler(Pipeline $pipeline);
}
