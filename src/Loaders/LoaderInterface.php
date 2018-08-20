<?php

namespace Marquine\Etl\Loaders;

use Marquine\Etl\Pipeline;

interface LoaderInterface
{
    /**
     * Get the loader handler.
     *
     * @param  \Marquine\Etl\Pipeline  $pipeline
     * @param  string  $destination
     * @return callable
     */
    public function handler(Pipeline $pipeline, $destination);
}
