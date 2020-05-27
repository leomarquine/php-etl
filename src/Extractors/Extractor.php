<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Step;

abstract class Extractor extends Step
{
    /**
     * The extractor input.
     *
     * @var mixed
     */
    protected $input;

    /**
     * Set the extractor input.
     *
     * @param  mixed  $input
     * @return $this
     */
    public function input($input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Extract data from the input.
     *
     * @return \Generator
     */
    abstract public function extract();
}
