<?php

namespace Marquine\Etl\Extractors;

use IteratorAggregate;
use Marquine\Etl\Step;

abstract class Extractor extends Step implements IteratorAggregate
{
    /**
     * Set up the extraction from the given source.
     *
     * @param  mixed  $source
     * @return void
     */
    abstract public function extract($source);
};
