<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Step;

abstract class Extractor extends Step
{
    /**
     * Extract data from an input.
     *
     * @return \Generator
     */
    abstract public function extract();
};
