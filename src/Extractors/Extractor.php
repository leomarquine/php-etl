<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Pipeline;

abstract class Extractor
{
    /**
     * Extract data from the given source.
     *
     * @param  string  $source
     * @return \Generator
     */
    abstract public function extract($source);

    /**
     * Get the extractor pipeline.
     *
     * @param  string  $source
     * @return \Marquine\Etl\Pipeline
     */
    public function pipeline($source)
    {
        return new Pipeline($this->extract($source));
    }
};
