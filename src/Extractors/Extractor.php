<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Step;

abstract class Extractor extends Step
{
    /**
     * Extract data from the given source.
     *
     * @param  mixed  $source
     * @return iterable
     */
    abstract public function extract($source);
};
